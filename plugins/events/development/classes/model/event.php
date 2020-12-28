<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Event extends ORM
{
	const TABLE_EVENTS = 'plugin_events_events';
	const TABLE_TAGS = 'plugin_events_tags';
    const TABLE_HAS_TAGS = 'plugin_events_has_tags';
    const TABLE_VENUES = 'plugin_events_venues';
    const TABLE_HAS_ORGANIZERS = 'plugin_events_events_has_organizers';
    const TABLE_HAS_TICKET_TYPES = 'plugin_events_events_has_ticket_types';
    const TABLE_HAS_PAYMENTPLANS = 'plugin_events_events_has_ticket_types_has_paymentplans';
    const TABLE_ACCOUNTS = 'plugin_events_accounts';
    const TABLE_ORDERS = 'plugin_events_orders';
    const TABLE_ORDER_ITEMS = 'plugin_events_orders_items';
    const TABLE_PAYMENTS = 'plugin_events_orders_payments';
    const TABLE_PARTIAL_PAYMENTS = 'plugin_events_orders_payments_has_partial_payments';
    const TABLE_PAYMENTGWS = 'plugin_events_payment_gateways';
    const TABLE_TICKETS = 'plugin_events_orders_tickets';
    const TABLE_TTSOLD = 'plugin_events_events_ticket_types_sold';
    const TABLE_ESOLD = 'plugin_events_events_sold';
    const TABLE_ORGANIZERS = 'plugin_events_organizers';
    const TABLE_DATES = 'plugin_events_events_dates';
    const TABLE_DISCOUNTS = 'plugin_events_discounts';
    const TABLE_TTYPE_HAS_DISCOUNTS = 'plugin_events_ticket_types_has_discounts';
    const TABLE_ORDER_ITEM_DATES = 'plugin_events_orders_items_has_dates';
    const TABLE_SINVOICES = 'plugin_events_seller_invoices';
    const TABLE_CONTACTS = 'plugin_contacts_contact';
    const TABLE_LOOKUPS = 'plugin_events_lookups';
    const TABLE_CHECKOUT_DETAILS = 'plugin_events_checkout_details';
    const TABLE_PENDING_TICKETS = 'plugin_events_orders_pending';
    const TABLE_ODISCOUNTS = 'plugin_events_orders_has_discounts';
    
    const EVENT_STATUS_LIVE = 'Live';
    const EVENT_STATUS_OFFLINE = 'Offline';
    const EVENT_STATUS_DRAFT = 'Draft';
    const EVENT_STATUS_SALE_ENDED = 'Sale Ended';

    const ORDER_STATUS_PAID = 'PAID';

	protected $_table_name = 'plugin_events_events';

    const REFUND_REASON_DUPLICATE = 'duplicate';
    const REFUND_REASON_POSTPONED = 'fraudulent';
    const REFUND_REASON_REQUESTED_BY_CUSTOMER = 'requested_by_customer';
    const REFUND_REASON_EVENT_CANCELED = 'event_canceled';
    const REFUND_REASON_EVENT_POSTPONED = 'event_postponed';

	protected $_belongs_to = array(
		'venue' => array('model' => 'Event_Venue'),
	);

	protected $_has_many = array(
        'organizers' => array('model' => 'Event_Organizer', 'through' => self::TABLE_HAS_ORGANIZERS, 'far_key' => 'contact_id'),
		'tags'       => array('model' => 'Event_Tag',       'through' => self::TABLE_HAS_TAGS),
	);

    public static function getAvailableDiscounts($event_id)
    {
        return DB::select('discounts.*', DB::expr('GROUP_CONCAT(hastt.ticket_type_id) AS ticket_types'))
            ->from(array(self::TABLE_DISCOUNTS, 'discounts'))
            ->join(array(self::TABLE_TTYPE_HAS_DISCOUNTS, 'hastt'), 'left')->on('discounts.id', '=', 'hastt.discount_id')
            ->where('discounts.event_id', '=', $event_id)
            ->and_where('discounts.deleted', '=', 0)
            ->group_by('discounts.id')
            ->execute()
            ->as_array();
    }

    public static function deleteDiscount($discount_id)
    {
        return DB::update(self::TABLE_DISCOUNTS)
            ->set(array('deleted' => 1))
            ->where('id', '=', $discount_id)
            ->execute();
    }

    public static function getDiscountsByCode($event_id, $ticket_type_id, $code)
    {
        $q = DB::select('discounts.*', DB::expr('GROUP_CONCAT(hastt.ticket_type_id) AS ticket_types'))
            ->from(array(self::TABLE_DISCOUNTS, 'discounts'))
            ->join(array(self::TABLE_TTYPE_HAS_DISCOUNTS, 'hastt'), 'left')->on('discounts.id', '=', 'hastt.discount_id')
            ->where('discounts.event_id', '=', $event_id)
            ->and_where('discounts.code', '=', $code)
            ->and_where('discounts.deleted', '=', 0)
            ->and_where_open()
            ->or_where('hastt.ticket_type_id', '=', $ticket_type_id)
            ->or_where('hastt.ticket_type_id', 'IS', NULL)
            ->and_where_close()
        ;

        return $q->execute()->as_array();
    }

    public static function eventSaveSeo($post)
    {
		// Sanitise input data
		$html_fields = array('footer');
		$post = html::clean_array($post, $html_fields);

        $db = Database::instance();
        $db->begin();
        try {
            $user = Auth::instance()->get_user();
            $event = array();
            $id = $post['id'];

            $event['date_modified'] = date('Y-m-d H:i:s');
            $event['modified_by'] = $user['id'];

            if (!is_numeric($id)) {
                $event['date_created'] = date('Y-m-d H:i:s');
                $event['created_by'] = $user['id'];
                $event['owned_by'] = $user['id'];
            }

            $event['name'] = strip_tags($post['name']);
            $event['seo_keywords'] = $post['seo_keywords'];
            $event['seo_description'] = $post['seo_description'];
            $event['footer'] = $post['footer'];
            $event['x_robots_tag'] = $post['x_robots_tag'];

            if (!is_numeric($id)) {
                $eventResult = DB::insert(self::TABLE_EVENTS)->values($event)->execute();
                $id = $eventResult[0];
            } else {
                DB::update(self::TABLE_EVENTS)->set($event)->where('id', '=', $id)->execute();
            }

            $db->commit();
            return $id;
        } catch (Exception $exc) {
            $db->rollback();
            throw $exc;
        }
    }

    public static function getCategories()
    {
        $categories = DB::select(
            array('categories.value', 'id'),
            'categories.label',
            DB::expr("count(*) as cnt")
        )
            ->from(array(Model_Lookup::LOOKUP_FIELDS, 'lf'))
                ->join(array(Model_Lookup::MAIN_TABLE, 'categories'), 'inner')
                    ->on('lf.id', '=', 'categories.field_id')
                    ->on('lf.name', '=', DB::expr("'Event Category'"))
                ->join(array(Model_Event::TABLE_EVENTS, 'events'), 'inner')
                    ->on('categories.value', '=', 'events.category_id')
            ->where('events.deleted', '=', 0)
            ->group_by('categories.value')
            ->order_by('cnt', 'desc')
            ->execute()
            ->as_array();
        return $categories;
    }

    public static function eventSave($post)
    {
		// Sanitise input data
		$html_fields = array('description', 'email_note', 'message');
		$post = html::clean_array($post, $html_fields);
        $db = Database::instance();
        $db->begin();
        //header('content-type: text/plain');print_r($post);exit;
        try {
            $user = Auth::instance()->get_user();
            $event = array();
            $id = $post['id'];

            $event['date_modified'] = date('Y-m-d H:i:s');
            $event['modified_by'] = $user['id'];
            if (!is_numeric($id)) {
                $event['date_created'] = date('Y-m-d H:i:s');
                $event['created_by'] = $user['id'];
                $event['owned_by'] = $user['id'];
            }

            $event['name'] = $post['name'];
            $event['description'] = $post['description'];
            $event['is_public'] = $post['is_public'];

            // The countdown can only be saved by people with the necessary permission.
            // Otherwise it stays the same as what is currently in the database
            if (isset($post['count_down_time']) AND (Auth::instance()->has_access('events_edit')))
            {
                $event['count_down_seconds'] = self::hhmmss_to_seconds($post['count_down_time']);
            }

            if (isset($post['action']) AND in_array($post['action'], array('make_live', 'make_live_and_tweet'))) {
				// User clicked the "Make Your Event Live" button
                $event['status'] = self::EVENT_STATUS_LIVE;
				$event['publish']   = 1;
				$event['is_onsale'] = 1;
			} elseif(isset($post['action']) AND in_array($post['action'], array('make_offline'))) {
				// User clicked the "Make Your Event Offline" button
                $event['status'] = self::EVENT_STATUS_OFFLINE;
                $event['publish']   = 0;
				$event['is_onsale'] = 0;
			} else {
				$event['publish']   = isset($post['publish'])   ? $post['publish']   : 0;
				$event['is_onsale'] = isset($post['is_onsale']) ? $post['is_onsale'] : 0;
			}

			if(isset($post['action']) AND in_array($post['action'], array('save_draft', 'save_stripe_connect', 'save_stripe_disconnect'))) {
                $event['status'] = self::EVENT_STATUS_DRAFT;
            }

            if (Auth::instance()->has_access('events_edit')) {
                if (isset($post['featured'])) {
                    $event['featured'] = $post['featured'];
                }

                if (isset($post['is_home_banner'])) {
                    $event['is_home_banner'] = $post['is_home_banner'];
                }


                if (@$post['commission_fixed_amount'] != '') {
                    $event['commission_fixed_amount'] = $post['commission_fixed_amount'];
                }
                if (@$post['commission_type'] != '') {
                    $event['commission_type'] = $post['commission_type'];
                }
                if (@$post['commission_amount'] != '') {
                    $event['commission_amount'] = $post['commission_amount'];
                }
            }

            $event['timezone'] = $post['timezone'];
            $event['category_id'] = $post['category_id'];
			$event['topic_id'] = $post['topic_id'];
            $event['url'] = self::calculateUrlForEvent($post['url'] ? $post['url'] : $event['name'], $id);
            $event['quantity'] = $post['quantity'];
            $event['videos'] = @$post['videos'] ? json_encode($post['videos']) : '';
            $event['other_times'] = @$post['custom_time'] ? json_encode($post['custom_time']) : '';
            $event['display_start'] = @$post['display_start'] ? $post['display_start'] : 0;
            $event['display_end'] = @$post['display_end'] ? $post['display_end'] : 0;
            $event['display_timezone'] = @$post['display_timezone'] ? $post['display_timezone'] : 0;
            $event['display_othertime'] = @$post['display_othertime'] ? $post['display_othertime'] : 0;
            $event['display_map'] = @$post['display_map'] ? $post['display_map'] : 0;
            $event['timezone'] = $post['timezone'];
            $event['show_remaining_tickets'] = $post['show_remaining_tickets'];
            $event['one_ticket_for_all_dates'] = @$post['one_ticket_for_all_dates'] ? @$post['one_ticket_for_all_dates'] : 0;
            $event['venue_id'] = $post['venue']['id'];
			$event['email_note'] = $post['email_note'];
			$event['ticket_note'] = $post['ticket_note'];
            $event['currency'] = $post['currency'];
            if (Auth::instance()->has_access('seo')) {
                $event['seo_keywords'] = $post['seo_keywords'];
                $event['seo_description'] = $post['seo_description'];
                $event['footer'] = $post['footer'];
                $event['x_robots_tag'] = $post['x_robots_tag'];
            }

            $age_restriction = (int)$post['age_restriction'];
            $event['age_restriction'] = $age_restriction && $age_restriction > 0 ? $age_restriction : 0;

			$event['forecast_icon']    = ( ! empty($post['forecast_icon']))    ? $post['forecast_icon']    : '';
			$event['forecast_summary'] = ( ! empty($post['forecast_summary'])) ? $post['forecast_summary'] : '';
			$event['forecast_json']    = ( ! empty($post['forecast_json']))    ? $post['forecast_json']    : '{}';

            $event['image_media_id'] = $post['event_image_media_id'];
            if (!$event['image_media_id']){
                $event['image_media_id'] = null;
            }

            if (Auth::instance()->has_access('events_edit_advanced')) {
                $event['enable_multiple_payers'] = $post['enable_multiple_payers'] == 1 ? 'YES' : 'NO';
            }

            $venue = array();
            $venue['name'] = $post['venue']['name'];
            $venue['url'] = self::calculateUrlForVenue($post['venue']['name'], $post['venue']['id']);
            $venue['address_1'] = $post['venue']['address_1'];
            $venue['address_2'] = $post['venue']['address_2'];
            $venue['city'] = $post['venue']['city'];
            $venue['country_id'] = $post['venue']['country_id'];
            $venue['county_id'] = is_numeric(@$post['venue']['county_id']) ? $post['venue']['county_id'] : null;
			if (isset($post['venue']['eircode']))
			{
				$venue['eircode'] = $post['venue']['eircode'];
			}
            $venue['email'] = $post['venue']['email'];
            $venue['telephone'] = $post['venue']['telephone'];
            $venue['website'] = $post['venue']['website'];
            $venue['facebook_url'] = IbHelpers::get_path_from_url($post['venue']['facebook_url']);
            $venue['twitter_url'] = IbHelpers::get_path_from_url($post['venue']['twitter_url']);
            $venue['snapchat_url'] = IbHelpers::get_path_from_url($post['venue']['snapchat_url']);
            $venue['instagram_url'] = IbHelpers::get_path_from_url($post['venue']['instagram_url']);
            $venue['map_lat'] = $post['venue']['map_lat'];
            $venue['map_lng'] = $post['venue']['map_lng'];
            $venue['image_media_id'] = $post['venue']['image_media_id'];
            if (!$venue['image_media_id']) {
                $venue['image_media_id'] = null;
            }

            if (!is_numeric($post['venue']['id'])) {
                $venue['created_by'] = $venue['modified_by'] = $user['id'];
                $venue['date_created'] = $venue['date_modified'] = date('Y-m-d H:i:s');
                $venueResult = DB::insert(self::TABLE_VENUES)->values($venue)->execute();
                $event['venue_id'] = $venueResult[0];
            } else {
                DB::update(self::TABLE_VENUES)->set($venue)->where('id', '=', $post['venue']['id'])->execute();
            }

            if (!is_numeric($id)) {
                $eventResult = DB::insert(self::TABLE_EVENTS)->values($event)->execute();
                $id = $eventResult[0];
            } else {
                DB::update(self::TABLE_EVENTS)->set($event)->where('id', '=', $id)->execute();
            }

            DB::delete(self::TABLE_HAS_TAGS)->where('event_id', '=', $id)->execute();
            if (isset($post['has_tag']))
            foreach ($post['has_tag'] as $tags) {
            	$tag_ar=explode(',',$tags);
            	foreach($tag_ar as $has_tag){
	                $tagId = DB::select('id')->from(self::TABLE_TAGS)->where('tag', '=', $has_tag)->execute()->get('id');
	                if (!$tagId) {
	                    $tagResult = DB::insert(self::TABLE_TAGS)->values(array('tag' => $has_tag,'published' => 1 ))->execute();
	                    $tagId = $tagResult[0];
	                }
	                DB::insert(self::TABLE_HAS_TAGS)->values(array('tag_id' => $tagId, 'event_id' => $id))->execute();
            	}
            }

            $dateIds = array();
            if (isset($post['date_id']))
            foreach ($post['date_id'] as $i => $dateId) {
                $date = array();
                $date['event_id'] = $id;
                if ($post['start_date'][$i]) {
                    $date['starts'] = $post['start_date'][$i] . ' ' . $post['start_time'][$i] . ':00';
                } else {
                    $date['starts'] = null;
                }

                if ($post['end_date'][$i]) {
                    $date['ends'] = $post['end_date'][$i] . ' ' . $post['end_time'][$i] . ':00';
                } else {
                    $date['ends'] = null;
                }
                if (@$post['date_onsale'][$i] == 1) {
                    $date['is_onsale'] = 1;
                } else {
                    $date['is_onsale'] = 0;
                }
                if (is_numeric($dateId)) {
                    DB::update(self::TABLE_DATES)->set($date)->where('id', '=', $dateId)->execute();
                    $dateIds[] = $dateId;
                } else {
                    $inserted = DB::insert(self::TABLE_DATES)->values($date)->execute();
                    $dateIds[] = $inserted[0];
                }
            }
            if (count($dateIds)) {
                DB::delete(self::TABLE_DATES)->where('event_id', '=', $id)->and_where('id', 'not in', $dateIds)->execute();
            }

            DB::delete(self::TABLE_HAS_ORGANIZERS)->where('event_id', '=', $id)->execute();
            if (isset($post['organizers']))
			{
				foreach ($post['organizers'] AS $organiserIndex => $organizer)
				{
                    if ($organiserIndex == "-1") {
                        continue;
                    }
					$contactId = $organizer['contact_id'];
					if (!$organizer['contact_id']) {
						$contact = new Model_Contacts();
					} else {
                        $contact = new Model_Contacts($organizer['contact_id']);
						$data=array();
						$data['phone']=@$organizer['telephone'];
						DB::update(self::TABLE_CONTACTS)
						->set($data)
						->where('id', '=', $contactId)
						->execute();
					}

                    $contact->set_first_name($organizer['name']);
                    $contact->set_last_name(@$organizer['lastname']);
                    $contact->set_email(@$organizer['email']);
                    $contact->set_phone(@$organizer['telephone']);
                    $contact->set_mobile(@$organizer['mobile']);
                    $contact->set_publish(1);
                    $contact->set_mailing_list('Event Organizer');
                    $contact->set_permissions(array($user['id']));
                    $contact->test_existing_email = false;
                    $contact->save();
                    $contactId = $contact->get_id();


                    if ($organizer['banner_media_id'] === '' || !$organizer['banner_media_id'])
                        $organizer['banner_media_id'] = 0;

                    $existingOrganiser = DB::select('*')->from(self::TABLE_ORGANIZERS)->where('contact_id', '=', $contactId)->execute()->current();
                    $organiserValues = array(
                        'contact_id' => $contactId,
                        'url' => self::calculateUrlForOrganizer($organizer['name'], $contactId),
                        'twitter' => IbHelpers::get_path_from_url($organizer['twitter_url']),
                        'facebook' => IbHelpers::get_path_from_url($organizer['facebook_url']),
                    	'snapchat' => IbHelpers::get_path_from_url($organizer['snapchat_url']),
                    	'instagram' => IbHelpers::get_path_from_url($organizer['instagram_url']),
                        'linkedin' => IbHelpers::get_path_from_url(@$organizer['linkedin']),
                        'website' => $organizer['website'],
                        'profile_media_id' => @$organizer['profile_media_id'],
                        'banner_media_id' => @$organizer['banner_media_id'],
                    	'email' => @$organizer['email'],
                    );

                    if (!$existingOrganiser) {
                        DB::insert(self::TABLE_ORGANIZERS)
                            ->values($organiserValues)->execute();
                    } else {
                        DB::update(self::TABLE_ORGANIZERS)->set($organiserValues)->where('contact_id', '=', $contactId)->execute();
                    }

                    $has_organizer = array(
                        'event_id' => $id,
                        'contact_id' => $contactId,
                    );

                    //make first one primary if there is no additional organizers
                    if ($organiserIndex == 0) {
                        $has_organizer['is_primary'] = 1;
                    }
                    //make the ticked organizer primary
                    if (@$organizer['is_primary'] == 1) {
                        $has_organizer['is_primary'] = 1;
                        // update others organizers to not be primary
                        DB::update(self::TABLE_HAS_ORGANIZERS)
                            ->set(array('is_primary' => 0))
                            ->where('event_id', '=', $id)
                            ->execute();
                    }
                    DB::insert(self::TABLE_HAS_ORGANIZERS)->values($has_organizer)->execute();
				}
            }

            // If there are no tickets or all tickets' end dates have passed, this is false
            $tickets_on_sale = false;
            $has_tickets = false;

            if (isset($post['ticket_name'])) {
                $ticket_type_sort = 1;
                foreach ($post['ticket_name'] as $i => $ticketName) {
                    $ticketTypeId = $post['ticket_type_id'][$i];
                    $has_tickets = true;
                    $ticketType = array();
                    $ticketType['event_id'] = $id;
                    $ticketType['archived'] = @$post['ticket_archived'][$i] ?: 0;
                    $ticketType['type'] = $post['ticket_type'][$i];
                    $ticketType['name'] = $post['ticket_name'][$i];
                    $ticketType['description'] = $post['ticket_description'][$i];
                    $ticketType['show_description'] = empty($post['ticket_show_description'][$i]) ? 0 : 1;

                    $ticketPrice = $post['ticket_price'][$i];

                    if (empty($ticketPrice))
                        $ticketPrice = 0;

                    $ticketType['price'] = $ticketPrice;

                    $ticketType['include_commission'] = $post['ticket_include_commission'][$i];
					$ticketType['description'] = $post['ticket_description'][$i];
                    $ticketType['quantity'] = $post['ticket_quantity'][$i];
                    $ticketType['max_per_order'] = $post['ticket_max_per_order'][$i] ? $post['ticket_max_per_order'][$i] : null;
                    $ticketType['min_per_order'] = $post['ticket_min_per_order'][$i] ? $post['ticket_min_per_order'][$i] : 0;
                    $ticketType['visible'] = $post['ticket_visible'][$i];
                    $ticketType['sale_starts'] = $post['ticket_sale_starts'][$i] ? $post['ticket_sale_starts'][$i] : null;
                    $ticketType['sale_ends'] = $post['ticket_sale_ends'][$i] ? $post['ticket_sale_ends'][$i] : null;
                    $ticketType['hide_until'] = $post['ticket_hide_before'][$i] ? $post['ticket_hide_before'][$i] : null;
                    $ticketType['hide_after'] = $post['ticket_hide_after'][$i] ? $post['ticket_hide_after'][$i] : null;
                    $ticketType['sleep_capacity'] = $post['ticket_sleep_capacity'][$i] ? $post['ticket_sleep_capacity'][$i] : null;
                    $ticketType['sort'] = $ticket_type_sort++;
                    $ticketType['updated'] = date::now();
                    $ticketType['updated_by'] = $user['id'];
                    if (!$ticketTypeId) {
                        $ticketType['created'] = date::now();
                        $ticketType['created_by'] = $user['id'];
                        $ticketResult = DB::insert(self::TABLE_HAS_TICKET_TYPES)->values($ticketType)->execute();
                        $ticketTypeId = $ticketResult[0];
                        $post['ticket_type_id'][$i] = $ticketTypeId;
                    } else {
                        DB::update(self::TABLE_HAS_TICKET_TYPES)->set($ticketType)->where('id', '=',
                            $ticketTypeId)->execute();
                    }
                    if (!$ticketType['archived']) {
                        // There are still tickets on sale if:
                        // - another ticket has been identified as being on sale
                        // - this ticket has no end date
                        // - this ticket's end date has not passed
                        $tickets_on_sale = ($tickets_on_sale || $ticketType['sale_ends'] == '' || date('U', strtotime($ticketType['sale_ends'])) > date('U'));
                    }

                    if (@$post['ticket_paymentplan'][$i]) {
                        if (Auth::instance()->has_access('events_edit_advanced')) {
                            $pp_ids = array();
                            if (is_array(@$post['ticket_paymentplan'][$i])) {
                                foreach ($post['ticket_paymentplan'][$i] as $paymentplan) {
                                    if ($paymentplan['due_date'] == '') {
                                        $paymentplan['due_date'] = null;
                                    }
                                    $paymentplan['tickettype_id'] = $ticketTypeId;
                                    if (is_numeric(@$paymentplan['id'])) {
                                        DB::update(self::TABLE_HAS_PAYMENTPLANS)
                                            ->set($paymentplan)
                                            ->where('id', '=', $paymentplan['id'])
                                            ->execute();
                                        $pp_ids[] = $paymentplan['id'];
                                    } else {
                                        $pp_inserted = DB::insert(self::TABLE_HAS_PAYMENTPLANS)
                                            ->values($paymentplan)
                                            ->execute();
                                        $pp_ids[] = $pp_inserted[0];
                                    }
                                }
                            }
                            if (count($pp_ids)) {
                                DB::update(self::TABLE_HAS_PAYMENTPLANS)
                                    ->set(array('deleted' => 1))
                                    ->where('tickettype_id', '=', $ticketTypeId)
                                    ->and_where('id', 'not in', $pp_ids)
                                    ->execute();
                            } else {
                                DB::update(self::TABLE_HAS_PAYMENTPLANS)
                                    ->set(array('deleted' => 1))
                                    ->where('tickettype_id', '=', $ticketTypeId)
                                    ->execute();
                            }
                        }
                    }
                }
                if ($post['ticket_type_id']) {
                    DB::update(self::TABLE_HAS_TICKET_TYPES)
                        ->set(array('deleted' => 1, 'updated' => date::now(), 'updated_by' => $user['id']))
                        ->where('event_id', '=', $id)
                        ->and_where('id', 'not in', $post['ticket_type_id'])
                        ->execute();
                }
            } else {
                if ($post['ticket_type_id']) {
                    DB::update(self::TABLE_HAS_TICKET_TYPES)
                        ->set(array('deleted' => 1, 'updated' => date::now(), 'updated_by' => $user['id']))
                        ->where('event_id', '=', $id)
                        ->and_where('id', 'not in', $post['ticket_type_id'])
                        ->execute();
                }
            }

            // If the event is live, but none of the tickets are on sale, flag as "sale ended"
            if ($has_tickets && !$tickets_on_sale) {
                $event_data = new Model_Event($id);

                if ($event_data->status == self::EVENT_STATUS_LIVE) {
                    $event['status']    = self::EVENT_STATUS_SALE_ENDED;
                    $event['is_onsale'] = 0;
                    DB::update(self::TABLE_EVENTS)->set($event)->where('id', '=', $id)->execute();
                }
            }

            if (isset($post['discount_id'])) {
                foreach ($post['discount_id'] as $i => $discountId) {
                    $discount = array();
                    $discount['event_id'] = $id;

                    $discountType = $post['discount_type'][$i];

                    if ($discountType == 'Fixed')
                        $discount['type'] = 1;
                    elseif ($discountType == 'Percentage')
                        $discount['type'] = 2;

                    $discount['amount'] = $post['discount_amount'][$i];
                    $discount['code'] = $post['discount_code'][$i];

                    $quantity = $post['discount_quantity'][$i];

                    if (!empty($quantity))
                        $discount['quantity'] = $quantity;
                    else
                        $discount['quantity'] = 0;

                    $discount['starts'] = $post['discount_starts'][$i] ? $post['discount_starts'][$i] : null;
                    $discount['ends'] = $post['discount_ends'][$i] ? $post['discount_ends'][$i] : null;
                   if (!$discountId) {
                        $discountResult = DB::insert(self::TABLE_DISCOUNTS)->values($discount)->execute();
                        $discountId = $discountResult[0];
                        $post['discount_id'][$i] = $discountId;
                    } else {
                        DB::update(self::TABLE_DISCOUNTS)->set($discount)->where('id', '=', $discountId)->execute();
                    }
                    DB::delete(self::TABLE_TTYPE_HAS_DISCOUNTS)->where('discount_id', '=', $discountId)->execute();
                    foreach ($post['discount_ticket_type'][$i] as $ticketTypeIndex) {
                        DB::insert(self::TABLE_TTYPE_HAS_DISCOUNTS)
                            ->values(array(
                                'discount_id' => $discountId,
                                'ticket_type_id' => $post['ticket_type_id'][$ticketTypeIndex]
                            ))
                            ->execute();
                    }
                }
                DB::delete(self::TABLE_DISCOUNTS)
                    ->where('event_id', '=', $id)
                    ->and_where('id', 'not in', $post['discount_id'])
                    ->execute();
            } else {
                DB::delete(self::TABLE_DISCOUNTS)
                    ->where('event_id', '=', $id)
                    ->execute();
            }

            $db->commit();

            $dashboard_id = DB::select('default_dashboard_id')->from('engine_users')->where('id', '=', $user['id'])->execute()->get('default_dashboard_id');
            if ($dashboard_id == null || $dashboard_id == 5) {
                DB::update('engine_users')->set(array('default_dashboard_id' => -1))->where('id', '=', $user['id'])->execute();
            }

            return $id;
        } catch (Exception $exc) {
            Log::instance()->add(Log::ERROR, "Error saving event.\n".$exc->getMessage()."n".$exc->getTraceAsString());

            IbHelpers::set_message(
                __(
                    'Error saving event. Please try again If this problem continues, please $1',
                    array('$1' => '<a href="/contact-us.html" target="_blank">'.__('contact the administration').'</a>')),
                'danger'
            );
            $db->rollback();
            throw $exc;
        }
    }

    public static function eventLoad($id)
    {
        $event = DB::select('events.*', 'esold.sold')
            ->from(array(self::TABLE_EVENTS, 'events'))
                ->join(array(self::TABLE_ESOLD, 'esold'), 'left')->on('events.id', '=', 'esold.event_id')
            ->where('id', '=', $id)
            ->execute()
            ->current();

        if ($event) {
            if ($event['image_media_id']) {
                $event['image_media_url'] = Model_Media::get_path_to_id($event['image_media_id']);
                $event['image_media_url_thumbs'] = Model_Media::get_path_to_id($event['image_media_id'], true);
            }
            $event['videos'] = @json_decode($event['videos']);
			$event['videos'] = empty($event['videos']) ? array() : $event['videos'];
            $event['other_times'] = @json_decode($event['other_times']);

            $event['venue'] = DB::select('venues.*', array('counties.name', 'county'))
                ->from(array(self::TABLE_VENUES, 'venues'))
                    ->join(array('engine_counties', 'counties'), 'left')->on('venues.county_id', '=', 'counties.id')
                ->where('venues.id', '=', $event['venue_id'])
                ->execute()
                ->current();

            if ($event['venue'] && $event['venue']['image_media_id']) {
                $event['venue']['image_media_url'] = Model_Media::get_path_to_id($event['venue']['image_media_id']);
                $event['venue']['image_media_url_thumbs'] = Model_Media::get_path_to_id($event['venue']['image_media_id'], true);
            }

            $s = $event['count_down_seconds'];
            $event['countdown_formatted'] = ($s > 0) ? sprintf('%02d:%02d:%02d', ($s/3600),($s/60%60), $s%60) : '';

            $sold_tickets = Model_Event::ticketsList(array('event_id' => $event['id']));
            $checkin_stats = array();
            foreach ($sold_tickets as $sold_ticket) {
                if (!isset($checkin_stats[$sold_ticket['date_id']])) {
                    $checkin_stats[$sold_ticket['date_id']] = array('date_id' => $sold_ticket['date_id'], 'checked_in_count' => 0, 'sold_count' => 0);
                }
                $checkin_stats[$sold_ticket['date_id']]['sold_count'] += 1;
                if ($sold_ticket['checked']) {
                    $checkin_stats[$sold_ticket['date_id']]['checked_in_count'] += 1;
                }
            }

            $event['dates'] = DB::select('dates.*', array('orders.id', 'has_order'))
                ->from(array(self::TABLE_DATES, 'dates'))
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'left')->on('dates.id', '=', 'idates.date_id')
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'left')->on('idates.order_item_id', '=', 'items.id')
                    ->join(array(self::TABLE_ORDERS, 'orders'), 'left')
                        ->on('orders.id', '=', 'items.order_id')
                        ->on('orders.status', '=', DB::expr("'PAID'"))
                        ->on('orders.deleted', '=', DB::expr(0))
                ->where('dates.event_id', '=', $id)
                ->and_where('dates.deleted', '=', 0)
                ->and_where('dates.starts', '<>', '0000-00-00 00:00:00')
                ->order_by('dates.starts', 'asc')
                ->group_by('dates.id')
                ->execute()
                ->as_array();

			$event['has_ended'] = TRUE;
            foreach ($event['dates'] as $i => $date) {
                $event['dates'][$i]['others'] = @json_decode($event['dates'][$i]['others'], true);
                $event['dates'][$i]['start_date'] = $date['starts'] ? date('Y-m-d', strtotime($date['starts'])) : '';
                $event['dates'][$i]['start_time'] = $date['starts'] ? date('H:i', strtotime($date['starts'])) : '';
                if ($date['ends']) {
                    $event['dates'][$i]['end_date'] = date('Y-m-d', strtotime($date['ends']));
                    $event['dates'][$i]['end_time'] = date('H:i', strtotime($date['ends']));
					$end_date = $date['ends'];
                } else {
                    $event['dates'][$i]['end_date'] = '';
                    $event['dates'][$i]['end_time'] = '';
					$end_date = date('Y-m-d 23:59:59', strtotime($date['starts'])); // The end of the day
                }

                $event['dates'][$i]['checked_in_count'] = 0;
                $event['dates'][$i]['sold_count'] = 0;

                if (isset($checkin_stats[$date['id']])) {
                    $event['dates'][$i]['checked_in_count'] = $checkin_stats[$date['id']]['checked_in_count'];
                    $event['dates'][$i]['sold_count'] = $checkin_stats[$date['id']]['sold_count'];
                }

				// Event has ended if all of these dates have passed
				$event['has_ended'] = ($event['has_ended'] AND (strtotime($end_date) <= strtotime(date('Y-m-d H:i:s'))));
            }

            $subquery_is_ordered = DB::select('i.ticket_type_id', DB::expr('1 as `ordered`'))
                ->from(array(self::TABLE_ORDERS, 'o'))
                ->join(array(self::TABLE_ORDER_ITEMS, 'i'), 'inner')->on('o.id', '=', 'i.order_id')
                ->where('o.deleted', '=', 0)
                ->group_by('i.ticket_type_id');

            $event['ticket_types'] = DB::select('ticket_types.*', 'events.currency', 'ordered_tickets.ordered')
                ->from(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'))
                    ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
                    ->join(array($subquery_is_ordered, 'ordered_tickets'), 'left')
                        ->on('ticket_types.id', '=', 'ordered_tickets.ticket_type_id')
                ->where('event_id', '=', $id)
                ->and_where('ticket_types.deleted', '=', 0)
                ->order_by('ticket_types.sort', 'asc')
                ->execute()
                ->as_array();

            $event['ticket_types_sold'] = DB::select('ttsold.*')
                ->from(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'))
                    ->join(array(self::TABLE_TTSOLD, 'ttsold'), 'left')->on('ticket_types.id', '=', 'ttsold.ticket_type_id')
                ->where('event_id', '=', $id)
                ->execute()
                ->as_array();

            $now = date::now();

            $event['ticket_types_pending_checkout'] = DB::select(
                'pending.ticket_type_id', 'pending.date_id', DB::expr("sum(pending.quantity) as quantity")
            )
                ->from(array(self::TABLE_PENDING_TICKETS, 'pending'))
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')
                        ->on('pending.ticket_type_id', '=', 'ticket_types.id')
                    ->join(array(self::TABLE_DATES, 'dates'), 'inner')
                        ->on('pending.date_id', '=', 'dates.id')
                    ->join(array(self::TABLE_EVENTS, 'events'), 'inner')
                        ->on('ticket_types.event_id', '=', 'events.id')
                ->where('events.id', '=', $id)
                ->and_where('pending.deleted', '=', 0)
                ->and_where(DB::expr("DATE_ADD(pending.created, INTERVAL events.count_down_seconds SECOND)"), '>=', $now)
                ->group_by('pending.ticket_type_id', 'pending.date_id')
                ->execute()
                ->as_array();

            $event['discounts'] = DB::select('discounts.*', DB::expr('GROUP_CONCAT(hastt.ticket_type_id) AS ticket_types'))
                ->from(array(self::TABLE_DISCOUNTS, 'discounts'))
                    ->join(array(self::TABLE_TTYPE_HAS_DISCOUNTS, 'hastt'), 'left')->on('discounts.id', '=', 'hastt.discount_id')
                ->where('discounts.event_id', '=', $id)
                ->and_where('discounts.deleted', '=', 0)
                ->group_by('discounts.id')
                ->execute()
                ->as_array();


            $commission = self::commissionGet($event, $event['owned_by']);
            $cheapest = false;
            $currency_of_cheapest = false;

            foreach ($event['ticket_types'] as $key => $ticket_type)
			{
                $event['ticket_types'][$key]['paymentplan'] = DB::select('ticket_paymentplans.*')
                    ->from(array(self::TABLE_HAS_PAYMENTPLANS, 'ticket_paymentplans'))
                    ->where('tickettype_id', '=', $ticket_type['id'])
                    ->and_where('ticket_paymentplans.deleted', '=', 0)
                    ->order_by('ticket_paymentplans.due_date', 'asc')
                    ->execute()
                    ->as_array();

                $event['ticket_types'][$key]['is_sold_out'] = false;
                $event['ticket_types'][$key]['dates_quantity_remaining'] = array();
                foreach ($event['dates'] as $date) {
                    $remaing_date = array(
                        'date_id' => $date['id'],
                        'quantity' => $ticket_type['quantity'],
                        'sold' => 0
                    );
                    foreach ($event['ticket_types_sold'] as $ticket_type_sold) {
                        if ($ticket_type_sold['ticket_type_id'] == $ticket_type['id'] && $date['id'] == $ticket_type_sold['event_date_id']) {
                            $remaing_date['quantity'] -= $ticket_type_sold['sold'];
                            $remaing_date['sold'] += $ticket_type_sold['sold'];
                            break;
                        }
                    }
                    $event['ticket_types'][$key]['dates_quantity_remaining'][$date['id']] = $remaing_date;
                }

                $breakdown = Model_Event::calculate_price_breakdown(
                    $ticket_type['price'],
                    $commission['fixed_charge_amount'] + ($commission['type'] == 'Fixed' ? $commission['amount'] : 0),
                    $commission['type'] == 'Fixed' ? 0 : $commission['amount'],
                    Settings::instance()->get('vat_rate'),
                    $ticket_type['include_commission']
                );

                $people = ($event['enable_multiple_payers'] && $ticket_type['sleep_capacity'] > 1) ? $ticket_type['sleep_capacity'] : 1;
                $per_person_price = (float) ($breakdown['total'] / $people);

                if ($cheapest === false || $per_person_price < $cheapest) {
                    $cheapest = $per_person_price;
                    $currency_of_cheapest = $ticket_type['currency'];
                }

                $event['ticket_types'][$key]['is_group_booking'] = ($people > 1);
            }

            $event['from_price'] = $cheapest;
            $event['from_price_currency'] = self::currency_symbol($currency_of_cheapest);

            foreach ($event['discounts'] as $i => $discount) {
                $event['discounts'][$i]['ticket_types'] = $discount['ticket_types'] ? explode(',', $discount['ticket_types']) : array();
            }

            $event['tags'] = DB::select('tags.*')
                ->from(array(self::TABLE_HAS_TAGS, 'has'))
                    ->join(array(self::TABLE_TAGS, 'tags'), 'inner')->on('has.tag_id', '=', 'tags.id')
                ->where('has.event_id', '=', $id)
                ->execute()
                ->as_array();

            $event['organizers'] = self::getEventOrganisers($id);
        }

        return $event;
    }

    public static function getEventOrganisers($eventId)
    {
        $organizers = DB::select('contacts.*', 'has.description', 'organisers.*', 'has.is_primary', array('contacts.email', 'contact_email'))
            ->from(array(self::TABLE_HAS_ORGANIZERS, 'has'))
            ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')->on('has.contact_id', '=', 'contacts.id')
            ->join(array(self::TABLE_ORGANIZERS, 'organisers'), 'inner')->on('has.contact_id', '=', 'organisers.contact_id')
            ->where('has.event_id', '=', $eventId)
            ->order_by('has.is_primary', 'desc') // make the primary first one in the list
            ->execute()
            ->as_array();

        foreach ($organizers as $i => $organizer) {
            if ($organizer['profile_media_id']) {
                $organizers[$i]['profile_media_url'] = Model_Media::get_path_to_id($organizer['profile_media_id']);
                $organizers[$i]['profile_media_url_thumbs'] = Model_Media::get_path_to_id($organizer['profile_media_id'], true);
            }
            if ($organizer['banner_media_id']) {
                $organizers[$i]['banner_media_url'] = Model_Media::get_path_to_id($organizer['banner_media_id']);
                $organizers[$i]['banner_media_url_thumbs'] = Model_Media::get_path_to_id($organizer['banner_media_id'], true);
            }
        }

        return $organizers;
    }

    public static function eventLoadFromUrl($eventUrl, $publish = 1)
    {
        $idq = DB::select('events.id')
            ->from(array(self::TABLE_EVENTS, 'events'))
            ->where('events.deleted', '=', 0);
        if ($publish) {
            $idq->and_where('events.publish', '=', 1);
        }
        $id = $idq->and_where('events.url', '=', $eventUrl)
            ->execute()
            ->get('id');
        if ($id) {
            $event = self::eventLoad($id);
        } else {
            $event = null;
        }
        return $event;
    }

    public static function solveBasePriceFeesIncluded($total, $commission, $vatRate)
    {
        if ($total == 0) { // free tickets
            return 0;
        }

        $divider = 1.0;
        $fixedValues = 0;

        $fixedValues += $commission['fixed_charge_amount'];
        $fixedValues += floor(self::round2($commission['fixed_charge_amount'] * ($vatRate * 100))) / 100;

        if ($commission['type'] == 'Fixed') {
            $fixedValues += $commission['amount'] + (floor(self::round2($commission['amount'] * ($vatRate * 100))) / 100);
        } else {
            $divider += ($commission['amount'] / 100.0) + (($commission['amount'] * $vatRate) / 100.0);
        }

        $price = self::round2(($total - $fixedValues) / $divider, 2);

        // handle rounding errors like 0.01 difference

        $isubtotal = $price;
        $commission_total = $commission['type'] == 'Fixed' ?
            $commission['amount']
            :
            floor(self::round2($commission['amount'] * $isubtotal)) / 100;
        $commission_total += $commission['fixed_charge_amount'];
        $isubtotal += $commission_total;
        $vat = floor(self::round2($commission_total * ($vatRate * 100))) / 100;
        $isubtotal += $vat;

        $diff = $total - $isubtotal;
        if ($diff != 0) {
            $price += $diff;
        }

        return $price;
    }

    public static function set_pending_ticket($ticket_type_id, $date_id, $quantity, $user = null)
    {
        if ($user == null) {
            $user = Auth::instance()->get_user();
        }

        $pending_tickets = DB::select('*')
            ->from(self::TABLE_PENDING_TICKETS)
            ->where('ticket_type_id', '=', $ticket_type_id)
            ->and_where('date_id', '=', $date_id)
            ->and_where('user_id', '=', $user['id'])
            ->and_where('deleted', '=', 0)
            ->execute()
            ->current();
        if ($pending_tickets != null) {
            if ($pending_tickets['quantity'] != $quantity) {
                DB::update(self::TABLE_PENDING_TICKETS)
                    ->set(array('quantity' => $quantity))
                    ->where('id', '=', $pending_tickets['id'])
                    ->execute();
                $pending_tickets['quantity'] = $quantity;
            }
        } else {
            $pending_tickets = array(
                'ticket_type_id' => $ticket_type_id,
                'date_id' => $date_id,
                'quantity' => $quantity,
                'created' => date::now(),
                'user_id' => $user['id']
            );
            $inserted = DB::insert(self::TABLE_PENDING_TICKETS)->values($pending_tickets)->execute();
            $pending_tickets['id'] = $inserted[0];

        }
        return $pending_tickets;
    }

    public static function orderClearPending()
    {
        DB::query(
            Database::UPDATE,
            "update plugin_events_orders_pending p
	            inner join plugin_events_events_has_ticket_types t on p.ticket_type_id = t.id
	            inner join plugin_events_events_dates d on p.date_id = d.id
	            inner join plugin_events_events e on t.event_id = e.id
	          set p.deleted = 1
	        where p.deleted = 0 and DATE_ADD(p.created, INTERVAL e.count_down_seconds SECOND) <= '" . date::now() . "'"
        )->execute();
    }

    public static function orderCalculate($event, $buyTicketTypes, $discountCode = '', $test_on_sale = true)
    {
        self::orderClearPending();
        self::eventsSoldUpdate();
        $vatRate = (float)Settings::instance()->get('vat_rate');
		$discounts = Model_Event::getAvailableDiscounts($event['id']);

        $result = array(
            'error' => null,
            'total' => 0.0,
            'commission' => 0.0,
            'discount' => 0.0,
            'vat' => 0.0,
            'vat_rate' => $vatRate,
            'currency' => $event['currency'],
            'event_id' => $event['id'],
            'has_discounts' => count($discounts) > 0,
            'discount_code' => $discountCode,
			'discount_type' => (isset($discounts[0]) AND isset($discounts[0]['type'])) ? $discounts[0]['type'] : '',
			'discount_type_amount' => (isset($discounts[0]) AND isset($discounts[0]['amount'])) ? $discounts[0]['amount'] : '',
            'items' => array()
        );
        $now = time();

        if (!$event['is_onsale'] && $test_on_sale) {
            $result['error'] = __('Event is not on sale!');
            return $result;
        }

        $account = self::accountDetailsLoad($event['owned_by']);
        $commission = self::commissionGet($event, $event['owned_by']);
        if (@$event['commission_type'] != '') {
            $commission['type'] = $event['commission_type'];
        }
        if (@$event['commission_amount'] != '') {
            $commission['amount'] = $event['commission_amount'];
        }
        if (@$event['commission_fixed_amount'] != '') {
            $commission['fixed_charge_amount'] = $event['commission_fixed_amount'];
        }
        $result['currency'] = $event['currency'];
        foreach ($buyTicketTypes as $buyTicketType) {
            if ($buyTicketType['quantity'] == 0){
                continue;
            }

            foreach ($event['ticket_types'] as $ticketType) {
                if (!isset($buyTicketType['dates']) && $event['one_ticket_for_all_dates'] == 1) {
                    $buyTicketType['dates'] = array();
                    foreach ($event['dates'] as $date) {
                        $buyTicketType['dates'][] = $date['id'];
                    }
                }
                if ($ticketType['id'] == $buyTicketType['ticket_type_id']) {
                    $expired = DB::select(DB::expr('count(*) as expired'))
                        ->from(self::TABLE_DATES)
                        ->where('id', 'in', $buyTicketType['dates'])
                        ->and_where('deleted', '=', 0)
                        ->where('ends', 'is not', NULL)
                        ->and_where('ends', '<', date('Y-m-d H:i:s'))
                        ->execute()
                        ->get('expired');
                    if ($expired) {
                        $result['error'] = __('Event has been expired!');
                        return $result;
                    }

                    if ($ticketType['min_per_order'] > 0 && $ticketType['min_per_order'] > $buyTicketType['quantity']) {
                        $result['error'] = sprintf(__('You need to buy at least %d of %s'),
                            $ticketType['min_per_order'], $ticketType['name']);
                        return $result;
                    }

                    if ($ticketType['max_per_order'] > 0 && $ticketType['max_per_order'] < $buyTicketType['quantity']) {
                        $result['error'] = sprintf(__('You can not buy more than %d of %s'),
                            $ticketType['max_per_order'], $ticketType['name']);
                        return $result;
                    }

                    if ($ticketType['sale_starts'] != null && strtotime($ticketType['sale_starts']) > $now) {
                        $result['error'] = sprintf(__('%s is not on sale'), $ticketType['name']);
                        return $result;
                    }

                    if ($ticketType['sale_ends'] != null && strtotime($ticketType['sale_ends']) <= $now) {
                        $result['error'] = sprintf(__('%s is not on sale'), $ticketType['name']);
                        return $result;
                    }

                    $item = array(
                        'ticket_type_id' => $buyTicketType['ticket_type_id'],
                        'quantity' => $buyTicketType['quantity'],
                        'price' => $ticketType['include_commission'] == 0 ? (float)$ticketType['price'] : self::solveBasePriceFeesIncluded((float)$ticketType['price'], $commission, $vatRate),
                        'donation' => $ticketType['include_commission'] == 0 ? (float)@$buyTicketType['donation'] : self::solveBasePriceFeesIncluded((float)$buyTicketType['donation'], $commission, $vatRate),
                        'dates' => $buyTicketType['dates'],
                        'commission' => 0,
                        'discount' => 0,
                        'discount_type' => 'Fixed',
                        'discount_amount' => 0,
                        'vat' => 0,
                        'total' => $ticketType['price'],
                        'pending' => array(),
                        'sleeping' => $ticketType['sleep_capacity']
                    );

                    if ($event['one_ticket_for_all_dates']) {
                        $item['pending'][] = self::set_pending_ticket($item['ticket_type_id'], 0, $item['quantity']);
                    } else {
                        foreach ($item['dates'] as $date_id) {
                            $item['pending'][] = @self::set_pending_ticket($item['ticket_type_id'], $date_id, $item['quantity']);
                        }
                    }

                    if (!empty($discountCode) && count($discounts = Model_Event::getDiscountsByCode($event['id'], $buyTicketType['ticket_type_id'], $discountCode)) > 0) {
                        $result['discount_type'] = $item['discount_type'] = $discounts[0]['type'];
                        $result['discount_type_amount'] = $item['discount_amount'] = $discounts[0]['amount'];
                    }

                    if ($ticketType['type'] == 'Paid' || $ticketType['type'] == 'Donation') {
                        $isubtotal = ($ticketType['type'] == 'Donation') ? $item['donation'] : $item['price'];

                        $item['discount'] = $item['discount_type'] == 'Fixed' ?
                            $item['discount_amount']
                            :
                            self::round2(($item['discount_amount'] / 100) * $isubtotal, 2);

                        $isubtotal -= $item['discount'];

                        $item['commission'] = $commission['type'] == 'Fixed' ?
                            $commission['amount']
                            :
                            floor(self::round2($isubtotal * $commission['amount'])) / 100;

                        $item['commission'] += $commission['fixed_charge_amount'];
                        $isubtotal += $item['commission'];

                        $item['vat'] = floor(self::round2($item['commission'] * ($vatRate * 100))) / 100;
                        $item['total'] = $isubtotal + $item['vat'];
                    }

                    $result['items'][] = $item;
                    $result['commission'] += ($item['commission'] * $item['quantity']);
                    $result['vat'] += ($item['vat'] * $item['quantity']);
                    $result['discount'] += ($item['discount'] * $item['quantity']);
                    $result['total'] += ($item['total'] * $item['quantity']);
                }
            }
        }

        $result['subtotal'] = $result['total'] - $result['commission'] - $result['vat'] + $result['discount'];
        $result['fees'] = $result['commission'] + $result['vat'] - $result['discount'];
        $result['fees_without_vat'] = $result['commission'] - $result['discount'];
        return $result;
    }

    public static function search($params = array())
    {
        $params['event_ids']  = isset($params['event_ids'])  ? (array) $params['event_ids'] : array();
        $params['event_id']   = isset($params['event_id'])   ? (array) $params['event_id']  : array();
        $params['event_ids']  = array_filter(array_merge($params['event_ids'], $params['event_id']));

        $params['county_ids'] = isset($params['county_ids']) ? (array) $params['county_ids'] : array();
        $params['county_id']  = isset($params['county_id'])  ? (array) $params['county_id']  : array();
        $params['county_ids'] = array_filter(array_merge($params['county_ids'], $params['county_id']));

        $is_random_order      = isset($params['direction']) && in_array(strtolower($params['direction']), array('rand', 'random', 'rand()'));

        $vatRate = (float)Settings::instance()->get('vat_rate');

        $order_events = DB::select("items.order_id", "ttypes.event_id")
            ->distinct("*")
            ->from(array(self::TABLE_ORDERS, 'orders'))
            ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
            ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id');

        /*$partial_payments = DB::select("order_events.event_id", DB::expr("SUM(ppayments.payment_amount) as partial_paid"))
            ->from(array(self::TABLE_ORDERS, 'orders'))
                ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')->on('orders.id', '=', 'payments.order_id')
                ->join(array(self::TABLE_PARTIAL_PAYMENTS, 'ppayments'), 'inner')->on('payments.id', '=', 'ppayments.main_payment_id')
                ->join(array($order_events, 'order_events'), 'inner')->on('orders.id', '=', 'order_events.order_id')
            ->where('ppayments.payment_id', 'is not', null)
            ->and_where('orders.status', '=', 'PAID')
            ->and_where('orders.status_reason', '=', 'Payment Completed')
            ->group_by("order_events.event_id");*/

        $partial_payments = DB::select("order_events.event_id", DB::expr("SUM(payments.amount) as partial_paid"))
            ->from(array(self::TABLE_ORDERS, 'orders'))
            ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')->on('orders.id', '=', 'payments.order_id')
            ->join(array($order_events, 'order_events'), 'inner')->on('orders.id', '=', 'order_events.order_id')
            ->where('payments.paymentgw', '<>', 'Payment Plan')
            ->and_where('orders.status', '=', 'PAID')
            ->and_where('orders.status_reason', '=', 'Payment Completed')
            ->group_by("order_events.event_id");


        $totalsq = DB::select(
            'ttypes.event_id',
            DB::expr("SUM((`items`.`price` + `items`.`donation`) * `items`.`quantity`) AS `total`")
        )
            ->from(array(self::TABLE_ORDERS, 'orders'))
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
            ->where('orders.status', '=', 'PAID')
            ->and_where('orders.deleted', '=', 0)
            ->group_by('ttypes.event_id');

        $soldquantityq = DB::select('event_id', DB::expr('SUM(sold) AS sold'))
            ->from(self::TABLE_ESOLD)
            ->group_by('event_id');

        $number_of_tickets_subquery = DB::select(
            'event_id',
            array(DB::expr("SUM(`quantity`)"), 'quantity')
        )
            ->from(self::TABLE_HAS_TICKET_TYPES)
            ->where('deleted', '=', 0)
            ->group_by('event_id');

        $q = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS DISTINCT events.*'),
            DB::expr('CONCAT(contacts.first_name, " ", contacts.last_name) as contact_full_name'),
            array('categories.label', 'category'),
			'events.url',
			array('venues.id', 'venue_id'),
            array('venues.name', 'venue'),
			'venues.address_1',
			'venues.address_2',
			'venues.address_3',
			'venues.city',
            array('counties.id', 'county_id'),
            array('counties.name', 'county'),
            DB::expr("CONCAT(IFNULL(`dates`.`starts`, ''), '|', IFNULL(`dates`.`ends`, '')) AS dates"),
            array('dates.id', 'date_id'),
            array('dates.is_onsale', 'date_onsale'),
            //'esold.sold',
            array('number_of_tickets.quantity', 'allocated'),
            array('invoices.id', 'invoice_id'),
            //array('totalsold.total', 'totalsold')
            'partial_payments.partial_paid'
        )
            ->from(array(self::TABLE_EVENTS, 'events'))
                ->join(array(Model_Lookup::MAIN_TABLE, 'topics'), 'left')->on('events.topic_id', '=', 'topics.id')
                ->join(array(Model_Lookup::MAIN_TABLE, 'categories'), 'left')
                    ->on('events.category_id', '=', 'categories.value')
                ->join(array(Model_Lookup::LOOKUP_FIELDS, 'cfields'), 'left')
                    ->on('categories.field_id', '=', 'cfields.id')->on('cfields.name', '=', DB::expr("'Event Category'"))
                ->join(array(self::TABLE_VENUES, 'venues'), 'left')->on('events.venue_id', '=', 'venues.id')
                ->join(array('engine_counties', 'counties'), 'left')->on('venues.county_id', '=', 'counties.id')
                //->join(array($soldquantityq, 'esold'), 'left')->on('events.id', '=', 'esold.event_id')
                ->join(array(self::TABLE_DATES, 'dates'), 'left')->on('events.id', '=', 'dates.event_id')
                ->join(array(self::TABLE_SINVOICES, 'invoices'), 'left')
                    ->on('events.id', '=', 'invoices.event_id')
                    ->on('invoices.deleted', '=', DB::expr(0))
                //->join(array($totalsq, 'totalsold'), 'left')->on('events.id', '=', 'totalsold.event_id')
                ->join(array($number_of_tickets_subquery, 'number_of_tickets'), 'left')->on('number_of_tickets.event_id', '=', 'events.id')
                ->join(array(self::TABLE_HAS_ORGANIZERS, 'has'))->on('has.event_id', '=', 'events.id')
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')->on('has.contact_id', '=', 'contacts.id')
                ->join(array(self::TABLE_ORGANIZERS, 'organisers'), 'inner')->on('has.contact_id', '=', 'organisers.contact_id')
                ->join(array($partial_payments, 'partial_payments'),'left')->on('events.id', '=', 'partial_payments.event_id')
            ->where('events.deleted', '=', 0);
        
        $search = isset($params['keyword']) ? $params['keyword'] : '';

		if ( ! empty($params['keyword']))
        {
            $search = preg_split('/[\W\\\'\\"]/i', $search);
            foreach ($search as $i => $keyword)
            {
                if (strlen($keyword) < 3) { // remove too short things like "at" "'s" "on" ...
                    unset($search[$i]);
                } else {
                    if ($keyword[strlen($keyword) - 1] == 's') {
                        $search[$i] = ' +' . substr($keyword, 0, -1) . '*'; /*'+' . $keyword . '* */
                    } else {
                        $search[$i] = '+' . $keyword . '*';
                    }
                }
            }
            $search = Database::instance()->escape(implode(' ', $search));
            
            if ( ! empty($search))
            {
                // Separate terms, enclose in quotes to stop special characters causing problems, "+" before each term
                $q->and_where_open();
                $q->where( DB::expr('match(`events`.`name`, `events`.`description`)'), 'against', DB::expr("(".$search." IN BOOLEAN MODE)"));
                $q->or_where(DB::expr('match(`venues`.`name`, `venues`.`city`, `venues`.`address_1`, `venues`.`address_2`, `venues`.`address_3`)'), 'against', DB::expr("(".$search." IN BOOLEAN MODE)"));
                $q->or_where(DB::expr('match(`topics`.`label`)'),     'against', DB::expr("(".$search." IN BOOLEAN MODE)"));
                $q->or_where(DB::expr('match(`categories`.`label`)'), 'against', DB::expr("(".$search." IN BOOLEAN MODE)"));
                $q->and_where_close();
            }
		}

        if (!empty($params['event_ids'])) {
            $q->where('events.id', 'in', $params['event_ids']);
        }

        $search2 = isset($params['keyword2']) ? $params['keyword2'] : '';

        if ( ! empty($params['keyword2']))
        {
            if (strlen($params['keyword2']) <= 3) {
                $q->where('events.name', 'like', $params['keyword2'] . '%');
            } else {
                $search2 = preg_split('/[\W\\\'\\"]/i', $search2);
                foreach ($search2 as $i => $keyword) {
                    if (strlen($keyword) < 3) { // remove too short things like "at" "'s" "on" ...
                        unset($search2[$i]);
                    } else {
                        if ($keyword[strlen($keyword) - 1] == 's') {
                            $search2[$i] = ' +' . substr($keyword, 0, -1) . '*'; /*'+' . $keyword . '* */
                        } else {
                            $search2[$i] = '+' . $keyword . '*';
                        }
                    }
                }
                $search2 = Database::instance()->escape(implode(' ', $search2));

                if (!empty($search2)) {
                    // Separate terms, enclose in quotes to stop special characters causing problems, "+" before each term
                    $q->where(DB::expr('match(`events`.`name`, `events`.`description`)'), 'against', DB::expr("(" . $search2 . " IN BOOLEAN MODE)"));
                }
            }
        }

        if (isset($params['name'])) {
            $q->and_where('events.name', '=', $params['name']);
        }

        if (isset($params['owned_by'])) {
            $q->and_where('events.owned_by', '=', $params['owned_by']);
        }
        if (isset($params['publish'])) {
            $q->and_where('events.publish', '=', $params['publish']);
        }
        if (isset($params['status'])) {
            if(is_array($params['status'])){
                $q->and_where('events.status', 'IN', $params['status']);
            }else {
                $q->and_where('events.status', '=', $params['status']);
            }
        }
        if (isset($params['is_public'])) {
            $q->and_where('events.is_public', '=', $params['is_public']);
        }

        if (isset($params['topic_ic'])) {
            $q->and_where('events.topic_id', '=', $params['topic_id']);
        }

        if (!empty($params['exclude_events'])) {
            $q->and_where('events.id', 'NOT IN', $params['exclude_events']);
        }

        if (isset($params['category_id'])) {
            $q->and_where('events.category_id', '=', $params['category_id']);
        }

        if (!empty($params['category_ids'])) {
            $q->and_where('events.category_id', 'IN', $params['category_ids']);
        }

        if (isset($params['is_home_banner'])) {
            $q->and_where('events.is_home_banner', '=', $params['is_home_banner']);
        }
        if (isset($params['after'])) {
            $q->and_where_open();
                $q->or_where('dates.starts', '>=', $params['after']);
                $q->or_where('dates.ends', '>=', $params['after']);
            $q->and_where_close();
        }
        if (isset($params['before'])) {
            $q->and_where_open();
                $q->or_where('dates.starts', '<=', $params['before']);
                $q->or_where('dates.ends', '<=', $params['before']);
            $q->and_where_close();
        }
        if (isset($params['tags'])) {
            $q->join(array(self::TABLE_HAS_TAGS, 'has_tags'), 'inner')->on('events.id', '=', 'has_tags.event_id');
            $q->join(array(self::TABLE_TAGS, 'tags'), 'inner')->on('has_tags.tag_id', '=', 'tags.id');
            $q->and_where('tags.tag', 'in', $params['tags']);
        }
        if (isset($params['contact_id'])) {
            $q->and_where('organisers.contact_id', '=', $params['contact_id']);
        }
        if (isset($params['venue_id'])) {
            $q->and_where('events.venue_id', '=', $params['venue_id']);
        }

        if (!empty($params['county_ids'])) {
            $q->and_where('counties.id', 'IN', $params['county_ids']);
        }

        if (!empty($params['offset'])) {
            $q->offset($params['offset']);
        }
        if (isset($params['limit'])) {
            $q->limit($params['limit']);
        }

        if ($is_random_order) {
            $q->order_by(DB::expr(""), DB::expr("RAND()"));
        }
        elseif ( ! empty($params['order_by']))
        {
            $direction = empty($params['direction']) ? 'desc' : $params['direction'];
            $q->order_by($params['order_by'], $direction);
        }
        else {
            $q->order_by('dates.starts', 'desc');
        }

        if (!empty($params['group_by'])) {
            $q->group_by($params['group_by']);
        }
        else {
            $q->group_by('events.id')->group_by('dates.id');
        }

        $events = $q
            ->execute()
            ->as_array();
        DB::query(null, "SET @found_events = FOUND_ROWS()")->execute();

		foreach ($events as $key => $event)
		{
            if ($event['one_ticket_for_all_dates'] == 1) {
                $events[$key]['totalsold'] = DB::select(
                    'ttypes.event_id',
                    DB::expr("SUM((`items`.`price` + `items`.`donation`) * `items`.`quantity`) AS `total`")
                )
                    ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                    ->where('orders.status', '=', 'PAID')
                    ->and_where('ttypes.event_id', '=', $event['id'])
                    ->and_where('orders.deleted', '=', 0)
                    ->group_by('ttypes.event_id')
                    ->execute()
                    ->get('total');
                $events[$key]['sold'] = DB::select(
                    'ttypes.event_id',
                    DB::expr("SUM(`items`.`quantity`) AS `quantity`")
                )
                    ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                    ->where('orders.status', '=', 'PAID')
                    ->and_where('ttypes.event_id', '=', $event['id'])
                    ->and_where('orders.deleted', '=', 0)
                    ->group_by('ttypes.event_id')
                    ->execute()
                    ->get('quantity');
            } else {
                $events[$key]['totalsold'] = DB::select(
                    'ttypes.event_id',
                    DB::expr("SUM((`items`.`price` + `items`.`donation`) * `items`.`quantity`) AS `total`")
                )
                    ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                    ->where('orders.status', '=', 'PAID')
                    ->and_where('ttypes.event_id', '=', $event['id'])
                    ->and_where('idates.date_id', '=', $event['date_id'])
                    ->and_where('orders.deleted', '=', 0)
                    ->group_by('ttypes.event_id')
                    ->execute()
                    ->get('total');
                $events[$key]['sold'] = DB::select(
                    'ttypes.event_id',
                    DB::expr("SUM(`items`.`quantity`) AS `quantity`")
                )
                    ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                    ->where('orders.status', '=', 'PAID')
                    ->and_where('ttypes.event_id', '=', $event['id'])
                    ->and_where('idates.date_id', '=', $event['date_id'])
                    ->and_where('orders.deleted', '=', 0)
                    ->group_by('ttypes.event_id')
                    ->execute()
                    ->get('quantity');
            }
            if ($event['image_media_id']) {
                $events[$key]['image_media_url'] = Model_Media::get_path_to_id($event['image_media_id']);
                $events[$key]['image_media_url_thumbs'] = Model_Media::get_path_to_id($event['image_media_id'], true);
            }
			$events[$key]['tickets'] = DB::select()
				->from(self::TABLE_HAS_TICKET_TYPES)
				->where('event_id', '=', $event['id'])
                ->where('deleted', '=', 0)
				->execute()
				->as_array();

            $cheapest= false;
            foreach ($events[$key]['tickets'] as $n => $ticket) {
                $account = self::accountDetailsLoad($event['owned_by']);
                $commission = self::commissionGet($event, $event['owned_by']);
                if ($event['commission_type'] != '') {
                    $commission['type'] = $event['commission_type'];
                }
                if ($event['commission_amount'] != '') {
                    $commission['amount'] = $event['commission_amount'];
                }
                if ($event['commission_fixed_amount'] != '') {
                    $commission['fixed_charge_amount'] = $event['commission_fixed_amount'];
                }
                $events[$key]['tickets'][$n]['base_price'] = (float)($ticket['include_commission'] == 1 ? Model_Event::solveBasePriceFeesIncluded($ticket['price'], $commission, $vatRate) :  $ticket['price']);

                $events[$key]['tickets'][$n]['total'] = self::ticketTotalCalculate(
                    $ticket['price'],
                    $ticket['include_commission'],
                    $event['owned_by'],
                    true
                );

                $breakdown = Model_Event::calculate_price_breakdown(
                    $ticket['price'],
                    $commission['fixed_charge_amount'] + ($commission['type'] == 'Fixed' ? $commission['amount'] : 0),
                    $commission['type'] == 'Fixed' ? 0 : $commission['amount'],
                    Settings::instance()->get('vat_rate'),
                    $ticket['include_commission']
                );

                $people = ($event['enable_multiple_payers'] && $ticket['sleep_capacity'] > 1) ? $ticket['sleep_capacity'] : 1;
                $per_person_price = round((float) ($breakdown['total'] / $people), 2);

                if ($cheapest === false || $per_person_price < $cheapest) {
                    $cheapest = $per_person_price;
                }
            }

            $events[$key]['from_price'] = $cheapest;

            $dates = DB::select('dates.*', array('orders.id', 'has_order'))
                ->from(array(self::TABLE_DATES, 'dates'))
                ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'left')->on('dates.id', '=', 'idates.date_id')
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'left')->on('idates.order_item_id', '=', 'items.id')
                ->join(array(self::TABLE_ORDERS, 'orders'), 'left')
                ->on('orders.id', '=', 'items.order_id')
                ->on('orders.status', '=', DB::expr("'PAID'"))
                ->on('orders.deleted', '=', DB::expr(0))
                ->where('dates.event_id', '=', $event['id'])
                ->and_where('dates.deleted', '=', 0)
                ->and_where('dates.starts', '<>', '0000-00-00 00:00:00')
                ->order_by('dates.starts', 'asc')
                ->group_by('dates.id')
                ->execute()
                ->as_array();

            $checkin_stats = array();
            foreach ($dates as $i => $date) {
                $checkin_stats[$date['id']] = array('date_id' => $date['id'], 'starts' => $date['starts'], 'checked_in_count' => 0, 'sold_count' => 0);
            }
            $sold_tickets = Model_Event::ticketsList(array('event_id' => $event['id']));
            foreach ($sold_tickets as $sold_ticket) {
                $checkin_stats[$sold_ticket['date_id']]['sold_count'] += 1;
                if ($sold_ticket['checked']) {
                    $checkin_stats[$sold_ticket['date_id']]['checked_in_count'] += 1;
                }
            }
            $events[$key]['checkin_stats'] = array_values($checkin_stats);

            $dates = explode('|', $event['dates']);
            $events[$key] = array_merge($events[$key], array(
                'button_text'     => __('Book Now'),
                'currency_symbol' => Model_Event::currency_symbol($event['currency']),
                'date_end'        => !empty($dates[1]) ? $dates[1] : null,
                'date_start'      => !empty($dates[0]) ? $dates[0] : null,
                'id'              => $event['id'],
                'image'           => Model_Event::static_get_image($event),
                'image_overlay'   => $event['city'],
                'label'           => $event['name'],
                'link'            => Model_Event::static_get_url($event),
                'price_amount'    => $events[$key]['from_price'],
                'subtitle'        => $event['venue'].'<br />'.$event['city'],
                'title'           => $event['name'],
                'type'            => 'event'
            ));
		}
		$result=array();
		if (isset($params['whole_site']) && $params['whole_site'] !== false)
		{
			$result['events']    = $events;

			$only_search_events = (isset($params['events_only']) AND $params['events_only']);

			if ( ! $only_search_events)
			{
                $organizer_query = DB::select(DB::expr('DISTINCT contacts.*'), 'organisers.*')
                    ->from(array(self::TABLE_ORGANIZERS, 'organisers'))
                    ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')->on('organisers.contact_id', '=', 'contacts.id')
                    ->join(array(self::TABLE_HAS_ORGANIZERS, 'has'))->on('contacts.id', '=', 'has.contact_id')
                    ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('has.event_id', '=', 'events.id')
                    ->where('contacts.first_name', 'like', $params['keyword'].'%')
                    ->and_where('organisers.deleted', '=', 0)
                    ->and_where('contacts.deleted', '=', 0)
                    ->and_where('events.deleted', '=', 0)
                    ->and_where('events.status', '=', 'Live')
                    ->group_by('organisers.url');

                if (!empty($params['county_ids'])) {
                    $organizer_query
                        ->join(array(self::TABLE_VENUES, 'venues'), 'left')
                        ->on('events.venue_id', '=', 'venues.id')
                        ->where('venues.county_id', 'in', $params['county_ids']);
                }

                if (!empty($params['event_ids'])) {
                    $organizer_query->and_where('events.id', 'in', $params['event_ids']);
                }

                if (!empty($params['category_ids'])) {
                    $organizer_query->and_where('events.category_id', 'IN', (array) $params['category_ids']);
                }

                $result['organiser'] = $organizer_query->execute()->as_array();


                // If an event or category is specified, the venue must have events in the joining table.
                $event_join_type = (!empty($params['event_ids']) || !empty($params['category_ids'])) ? 'left' : 'inner';

                $venueq = DB::select(DB::expr('DISTINCT venue.*'), array('county.id', 'county_id'), array('county.name', 'county'))
                    ->from(array(self::TABLE_VENUES, 'venue'))
                    ->join(array(self::TABLE_EVENTS, 'events'), $event_join_type)->on('events.venue_id', '=', 'venue.id')
                    ->join(array('engine_counties',  'county'), 'left' )->on('venue.county_id', '=', 'county.id');

                if ($search) {
                    $venueq->where(DB::expr('match(`venue`.`name`, `venue`.`city`, `venue`.`address_1`, `venue`.`address_2`, `venue`.`address_3`)'), 'against', DB::expr("(".$search." IN BOOLEAN MODE)"));
                }

                if ($params['county_ids']) {
                    $venueq->and_where('county.id', 'IN', $params['county_ids']);
                }

                if (!empty($params['event_ids'])) {
                    $venueq->where('events.id', 'in', $params['event_ids']);
                }

                if (!empty($params['category_ids'])) {
                    $venueq->where('events.category_id', 'IN', (array) $params['category_ids']);
                }

                $result['venue'] = $venueq
                    ->and_where('venue.publish',  '=', 1)
                    ->and_where('venue.deleted',  '=', 0)
                    ->and_where('events.deleted', '=', 0)
                    ->and_where('events.status',  '=', 'Live')
                    ->group_by('venue.name')
                    ->execute()->as_array();


                // Excluding pages for now
                $result['pages'] = array();
                /*
				$result['pages'] = DB::select()
					->from('plugin_pages_pages')
					->where('title', 'like', $params['keyword'].'%')
					->or_where('content', 'like','%'.$params['keyword'].'%')
					->where('publish', '=' ,1)
					->where('deleted', '=', 0)
					->order_by('title')
					->execute()->as_array();
                */

                if (!empty($params['county_ids']) || !empty($params['category_ids']) || !empty($params['event_ids'])) {
                    // News items are not linked to counties, categories or events
                    // Stop them dominating search results when these filters are used.
                    $result['news'] = array();
                }
                else {
                    $result['news'] = DB::select('news.*', 'category.category')
                        ->from(array('plugin_news', 'news'))
                        ->join(array('plugin_news_categories', 'category'), 'left')
                        ->on('news.category_id', '=', 'category.id')
                        ->where('news.title', 'like', $params['keyword'].'%')
                        ->where('news.publish', '=', 1)
                        ->where('news.deleted', '=', 0)
                        ->order_by('news.title')
                        ->execute()->as_array();
                }
			}

			$response = array();
			if ( ! $only_search_events)
			{
                // Result items need to be formatted as per
                // kilmartin/views/templates/kes1/views/front_end/snippets/search_result.php

				foreach ($result['organiser'] as $item)
				{
					$response[] = array(
						'id'    => $item['id'],
                        'image' => Model_Event_Organizer::static_get_banner_image($item, array('placeholder' => true)),
						'link'  => '/organiser/'.$item['url'],
						'label' => $item['first_name'],
                        'title' => $item['first_name'],
                        'subtitle'      => View::factory('frontend/snippets/widget_contact_details')->set(array(
                            'contact_text' => __('Contact'),
                            'item_id'      => $item['id'],
                            'email'        => $item['email'],
                            'title'        => $item['first_name'],
                            'type'         => 'organizer',
                            'website'      => $item['website'],
                            'skip_comments_in_beginning_of_included_view_file' => true)
                        ),
                        'social_media' => Model_Event_Organizer::static_get_social_media($item),
						'type'  => 'organiser',
						'data'  => $item
					);
				}
				foreach ($result['venue'] as $item)
				{
					$response[] = array(
						'id'            => $item['id'],
                        'image'         => Model_Event_Venue::static_get_image($item, array('placeholder' => true)),
                        'image_overlay' => $item['city'],
						'link'          => '/venue/'.$item['url'],
						'label'         => $item['name'],
                        'title'         => $item['name'],
                        'subtitle'      => View::factory('frontend/snippets/widget_contact_details')->set(array(
                            'contact_text' => __('Contact'),
                            'item_id'      => $item['id'],
                            'email'        => $item['email'],
                            'title'        => $item['name'],
                            'type'         => 'venue',
                            'website'      => $item['website'],
                            'skip_comments_in_beginning_of_included_view_file' => true)
                        ),
                        'social_media'  => Model_Event_Venue::static_get_social_media($item),
						'type'          => 'venue',
						'data'          => $item
					);
				}

                /* Excluding pages for now
				foreach ($result['pages'] as $item)
				{
					$response[] = array(
						'id'    => $item['id'],
                        'image' => false,
						'link'  => '/'.$item['name_tag'],
						'label' => $item['title'],
                        'title' => $item['title'],
						'type'  => 'page',
						'data'  => $item
					);
				}
                */

				foreach ($result['news'] as $item)
				{
					$response[] = array(
						'id'    => $item['id'],
                        'image' => false,
						'link'  => '/news/'.$item['category'].'/'.$item['title'],
						'label' => $item['title'],
                        'title' => $item['title'],
						'type'  => 'news',
						'data'  => $item
					);
				}
			}

			// Sort the results by label
            /*
             * first show events, with start dates, ordered by date, earliest to latest
             * then show events with no start date, ordered by name
             * then show everything else, ordered by name
             */
			$labels = array();
			foreach ($response as $key => $row) {
                if (($row['type'] == 'event')) {
                    $labels[$key] = ( ! empty($row['data']['dates'])) ? '0,'.$row['data']['dates'] : '1,'.$row['label'];
                }
                else {
                    $labels[$key] = '2,'.strtolower($row['label']);
                }
			}

            $direction = (isset($params['direction']) && $params['direction'] == 'desc') ? SORT_DESC : SORT_ASC;
			array_multisort($labels, $direction, $response);

            $response2 = array();
            foreach ($result['events'] as $item)
            {
                // Result items need to be formatted as per
                // kilmartin/views/templates/kes1/views/front_end/snippets/search_result.php

                $dates = explode('|', $item['dates']);

                $response2[] = array(
                    'data'            => $item,
                    'button_text'     => __('Book Now'),
                    'currency_symbol' => Model_Event::currency_symbol($item['currency']),
                    'date_end'        => !empty($dates[1]) ? $dates[1] : null,
                    'date_start'      => !empty($dates[0]) ? $dates[0] : null,
                    'id'              => $item['id'],
                    'image'           => Model_Event::static_get_image($item),
                    'image_overlay'   => $item['city'],
                    'label'           => $item['name'],
                    'link'            => Model_Event::static_get_url($item),
                    'price_amount'    => $item['from_price'],
                    'subtitle'        => $item['venue'].'<br />'.$item['city'],
                    'title'           => $item['name'],
                    'type'            => 'event'
                );
            }

            $response = array_merge($response2, $response);
            return $response;
		}

        return $events;
    }

    public static function get_for_eventcalendar()
    {
        $filters = ['publish' => 1, 'is_publish' => 1, 'status' => [self::EVENT_STATUS_LIVE, self::EVENT_STATUS_SALE_ENDED]];
        $events  = self::search($filters);
        $results = [];

        foreach ($events as $event) {
            $dates = array_unique(array_filter(explode('|', $event['dates'])));
            foreach ($dates as $date) {
                $results[] = [
                    'date'  => $date, 0, 10,
                    'type'  => 'Event',
                    'title' => $event['name'],
                    'url'   => $event['link']
                ];
            }
        }

        return $results;
    }

    public static function datatable($params)
    {
        $currencies = Model_Currency::getCurrencies(true);
        $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';

        $result = array();
        $result['aaData'] = array();
        $events = self::search($params);
        foreach ($events as $event) {
            $row = array();
            $row[] = '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">' . $event['name'] . '</a>';
            $row[] = $event['contact_full_name'];
            $a = '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">';
					if ($event['dates']) {
                        $event['dates'] = explode(',', $event['dates']);
                        foreach ($event['dates'] as $i => $date) {
                            $date = explode('|', $date);
                            $date['starts'] = $date[0];
                            $date['ends'] = $date[1];
                            $event['dates'][$i] = $date;
                        }
                    } else {
                        $event['dates'] = array();
                    }

					foreach ($event['dates'] as $date) {
                        $a .= '<span class="hidden">' . $date['starts'] . '</span>' .
                        '<div>' . date('j F Y',strtotime($date['starts'])) . '</div>';
                    }
            $a .= '</a>';
            $row[] = $a;
            $a = '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">';
            foreach ($event['dates'] as $date) {
                $a .= '<span class="hidden">' . $date['starts'] . '</span>' .
                    '<div>'  . date('H:i',strtotime($date['starts'])) . '</div>';
            }
            $a .= '</a>';
            $row[] = $a;
            $a = '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">';
                        if ($event['status'] == 'Live' && $event['quantity'] - $event['sold'] <= 0)
                            $a .= __('Sold Out');
                        else if ($event['status'] == 'Live' && $event['is_onsale'] == 1)
                            if ($event['date_onsale'] == 1) $a .= __('On Sale');
                            else $a .= __('Sale Ended');
                        else
                            $a .= $event['status'];
                    $a .= '</a>';
            $row[] = $a;
            $row[] = '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">' . ($event['quantity'] - $event['sold']) . ' / ' . $event['quantity'] . '</a>';
            $row[] = '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">' . $event['sold'] . '</a>';
            if (count($event['tickets']) == 1) {
                $a = '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">';
                            if ($event['tickets'][0]['type'] == 'Donation') {
                                $a .= 'Donation';
                            } else {
                                $a .= $currencies[$event['currency']]['symbol'] . number_format($event['tickets'][0]['base_price'], 2);
                            }
                        $a .= '</a>';
            } else if (count($event['tickets']) > 1) {
                $a .= '<a
							role="button"
							tabindex="0"
							class="text-uppercase"
							data-toggle="popover"
							data-html="true"
							data-trigger="hover touchend mouseup"
							data-placement="top"
							data-content="<table><tbody>';
                foreach ($event['tickets'] as $ticket) {
                    $a .= '<tr>' .
                        '<td>' . $ticket['name'] . '</td>' .
                        '<td>&nbsp;' . $currencies[$event['currency']]['symbol'] . number_format($ticket['base_price'], 2) . '</td>' .
                        '</tr>';
                }
                $a .= '</tbody></table>">Multiple</a>';
            }
            $row[] = $a;
            $row[] = '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">' .
                ($event['partial_paid'] ? $currencies[$event['currency']]['symbol'] . number_format($event['partial_paid'], 2, '.', ',') . ' / '  : '') .
                $currencies[$event['currency']]['symbol'] . number_format($event['totalsold'], 2, '.', ',') .
                '</a>';
            $a = '<div class="dropdown">' .
                '<button class="btn btn-default dropdown-toggle btn-actions" type="button" data-toggle="dropdown">' .
                '<span class="hidden-phone">' . __('Actions') . '</span>' .
                '<span class="caret"></span>' .
                '</button>' .
                '<ul class="dropdown-menu pull-right">';
            if (Auth::instance()->has_access('events_edit') || Auth::instance()->has_access('events_edit_limited')) {
                $a .= '<li>' .
                    '<a href="/admin/events/edit_event/' . $event['id'] . '" class="edit-link">' .
                    '<span class="icon-pencil"></span>' . __('Edit') .
                    '</a>' .
                    '</li>' .
                    '<li>' .
                    '<a href="/admin/events/duplicate_event?id=' . $event['id'] . '" class="edit-link">' .
                    '<span class="icon-copy"></span> ' . __('Duplicate') .
                    '</a>' .
                    '</li>' .
                    '<li>' .
                    '<button type="button" class="btn-link list-sale-end-button" ' .
                    'onclick="$(\'#sale-event-modal [name=id]\').val(' . $event['id'] . '); $(\'#sale-event-modal [name=date_id]\').val(' . $event['date_id'] . ')" ' .
                    'data-toggle="modal" ' .
                    'data-target="#sale-event-modal" ' .
                    'data-id="' . $event['id'] . '" ' .
                    'data-date_id="' . $event['date_id'] . '">' .
                    '<span class="icon-calendar-times-o"></span> ' . __('End Ticket Sales') .
                    '</button>' .
                    '</li>' .
                    '<li>';
                if ($event['status'] != 'Postponed') {
                    $a .= '<button type="button" class="btn-link list-postpone-button" ' .
                        'onclick="$(\'#status-event-modal [name=status]\').val(\'Postponed\'); $(\'#status-event-modal [name=id]\').val(' . $event['id'] . ')" ' .
                        'data-toggle="modal" ' .
                        'data-target="#status-event-modal" ' .
                        'data-id="' . $event['id'] . '"> ' .
                        '<span class="icon-calendar-minus-o"></span> ' . __('Postpone Event') .
                        '</button>';
                } else {
                    $a .= '<button type="button" class="btn-link list-reinstated-button" ' .
                        'onclick="$(\'#status-event-modal\').find(\'[name=status]\').val(\'Live\'); $(\'#status-event-modal\').find(\'[name=id]\').val(' . $event['id'] . ')" ' .
                        'data-toggle="modal" ' .
                        'data-target="#status-event-modal" ' .
                        'data-id="' . $event['id'] . '"> ' .
                        '<span class="icon-calendar-minus-o"></span> ' . __('Reinstate') .
                        '</button>';
                }
                $a .= '</li>' .
                    '<li>' .
                    '<button type="button" class="btn-link list-cancel-button" ' .
                    'onclick="$(\'#status-event-modal [name=status]\').val(\'Cancelled\'); $(\'#status-event-modal [name=id]\').val(' . $event['id'] . ')" ' .
                    'data-toggle="modal" ' .
                    'data-target="#status-event-modal" ' .
                    'data-id="' . $event['id'] . '"> ' .
                    '<span class="icon-ban"></span> ' . __('Cancel Event') .
                    '</button>' .
                    '</li>';
            }
                if ($event['status'] == 'Sale Ended') {
                    if ($event['invoice_id'] == null) {
                        $a .= '<li>' .
                            '<a class="btn-link list-print-button" href="/admin/events/invoice_generate?event_id=' . $event['id'] . '">' .
                            '<span class="icon-print"></span> ' . __('Generate Invoice') .
                            '</a>' .
                            '</li>';
                    } else {
                        $a .= '<li>' .
                            '<a class="btn-link list-print-button" href="/admin/events/invoice_download?invoice_id=' . $event['invoice_id'] . '">' .
                            '<span class="icon-print"></span> ' . __('Download Invoice') .
                            '</a>' .
                            '</li>';
                    }
                    $a .= '<li>' .
                        '<a class="btn-link list-print-button" href="/admin/events/statement_view?event_id=' . $event['id'] . '"> ' .
                        '<span class="icon-print"></span> ' . __('View Statement') .
                        '</a>' .
                        '</li>';
                }
                $a .= '<li>' .
                    '<button ' .
                    'class="btn-link download-attendees" ' .
                    'data-toggle="modal" ' .
                    'data-target="#download-attendees-modal" ' .
                    'data-id="' . $event['id'] . '" ' .
                    'data-date_id="' . $event['date_id'] . '">' .
                    '<select class="ticket_types_data" style="display: none;">' . html::optionsFromRows('id', 'name', $event['tickets']) . '</select>' .
                    '<span class="icon-download"></span> ' . __('Download Attendees') .
                    '</button>' .
                    '</li>' .
                    '<li>' .
                    '<button ' .
                    'class="btn-link" ' .
                    'data-toggle="modal" ' .
                    'data-target="#email-attendees-modal" ' .
                    'data-id="' . $event['id'] . '" ' .
                    'data-date_id="' . $event['date_id'] . '" ' .
                    '>' .
                    '<span class="icon-envelope-o"></span> ' . __('Email Attendees') .
                    '</button>' .
                    '</li>';
                if (Auth::instance()->has_access('events_delete') || Auth::instance()->has_access('events_delete_limited')){
                    $a .= '<li>' .
                        '<button ' .
                        'type="button" ' .
                        'class="btn-link list-delete-button" ' .
                        'title="' . __('Delete') . '"' .
                        'data-toggle="modal" ' .
                        'data-target="#delete-event-modal"' .
                        'data-id="' . $event['id'] . '" ' .
                        '>' .
                        '<span class="icon-times"></span> ' . __('Delete') .
                        '</button>' .
                        '</li>';
                }
                $a .= '</ul>' .
                    '</div>';
            $row[] = $a;
            $row[] = '<button type="button" class="btn-link publish-btn" data-id="' . $event['id'] . '">' .
						'<span class="hidden publish-value">' . $event['publish'] . '</span>' .
                        '<span class="publish-icon icon-' . ($event['publish'] ? 'ok' : 'ban-circle') . '"></span>' .
                        '</button>';
            $result['aaData'][] = $row;
        }
        $result['iTotalDisplayRecords'] = DB::select(DB::expr('@found_events as total'))->execute()->get('total');
        $result['iTotalRecords'] = count($result['aaData']);
        return $result;
    }

    public static function get_for_global_search($params = array())
    {
        $return = array();
        $params['after']       = isset($params['after'])       ? $params['after']       : date('Y-m-d H:i:s');
        $params['direction']   = isset($params['direction'])   ? $params['direction']   : 'asc';
        $params['events_only'] = isset($params['events_only']) ? $params['events_only'] : false;
        $params['keyword']     = isset($params['keyword'])     ? $params['keyword']     : (isset($params['keywords']) ? $params['keywords'] : null);
        $params['is_public']   = isset($params['is_public'])   ? $params['is_public']   : 1;
        $params['limit']       = isset($params['limit'])       ? $params['limit']       : 12;
        $params['offset']      = isset($params['offset'])      ? $params['offset']      : 0;
        $params['order_by']    = isset($params['order_by'])    ? $params['order_by']    : 'dates.starts';
        $params['publish']     = isset($params['publish'])     ? $params['publish']     : 1;
        $params['status']      = isset($params['status'])      ? $params['status']      : array(Model_Event::EVENT_STATUS_LIVE, Model_Event::EVENT_STATUS_SALE_ENDED);
        $params['whole_site']  = isset($params['whole_site'])  ? $params['whole_site']  : 1;

        // Don't apply this limit and offset to the MySQL.
        // The MySQL is separate queries for events, venues, etc.
        // The limit and offset need to be applied after the three queries are merged.
        $limit  = $params['limit'];
        $offset = $params['offset'];
        unset($params['limit']);
        unset($params['offset']);

        $results = self::search($params);
        $total   = count($results);

        if ($total == 0) {
            $return['results_found'] = __('No results found');
        }
        else {
            $return['results_found'] = __('Showing results $1 to $2 of $3', array(
                '$1' => 1 + $offset,
                '$2' => ($limit + $offset > $total) ? $total : $limit + $offset,
                '$3' => $total
            ));
        }

        $return['all_data']    = $results;
        $return['data']        = array_slice($results, $offset, $limit);
        $return['total_count'] = count($results);

        return $return;
    }

    /**
     * Call this function after running the "search" function to get the total number of results found, ignoring pagination
     */
    public static function getSearchTotal() {
        return DB::select(DB::expr('@found_events as found_events'))->execute()->get('found_events');
    }

    public static function getVenues()
    {
        $venues = DB::select('*')
            ->from(array(self::TABLE_VENUES, 'venues'))
            ->order_by('name')
            ->execute()
            ->as_array();
        foreach ($venues as $i => $venue) {
            if ($venue['image_media_id']) {
                $venues[$i]['image_media_url'] = Model_Media::get_path_to_id($venue['image_media_id']);
                $venues[$i]['image_media_url_thumbs'] = Model_Media::get_path_to_id($venue['image_media_id'], true);
            }
        }
        return $venues;
    }

    public static function getVenue($id)
    {
        $venue = DB::select('*')
            ->from(array(self::TABLE_VENUES, 'venues'))
			->and_where_open()
				->where('id', '=', $id)
				->or_where('name', '=', $id)
                ->or_where('url', '=', $id)
			->and_where_close()
			->where('deleted', '=', 0)
			->order_by('date_modified', 'desc')
            ->execute()
            ->current();
        if ($venue['image_media_id']) {
            $venue['image_media_url'] = Model_Media::get_path_to_id($venue['image_media_id']);
            $venue['image_media_url_thumbs'] = Model_Media::get_path_to_id($venue['image_media_id'], true);
        }
        return $venue;
    }

    public static function getOrganiser($id)
    {
        $organizer = DB::select('contacts.*', 'organisers.*')
            ->from(array(self::TABLE_ORGANIZERS, 'organisers'))
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')->on('organisers.contact_id', '=', 'contacts.id')
            ->where('organisers.contact_id', '=', $id)
            ->or_where('organisers.url', '=', $id)
            ->or_where(DB::expr("CONCAT(contacts.first_name, ' ', contacts.last_name)"), '=', $id)
            ->execute()
            ->current();
        if ($organizer['profile_media_id']) {
            $organizer['profile_media_url'] = Model_Media::get_path_to_id($organizer['profile_media_id']);
            $organizer['profile_media_url_thumbs'] = Model_Media::get_path_to_id($organizer['profile_media_id'], true);
        }
        if ($organizer['banner_media_id']) {
            $organizer['banner_media_url'] = Model_Media::get_path_to_id($organizer['banner_media_id']);
            $organizer['banner_media_url_thumbs'] = Model_Media::get_path_to_id($organizer['banner_media_id'], true);
        }
        return $organizer;
    }

    public static function calculateUrlForEvent($name, $excludeEventID = null)
    {
        $url = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
        $exists = true;
        for ($i = 0 ; $i < 10 && $exists ; ++$i) {
            $q = DB::select('id')->from(self::TABLE_EVENTS)
                ->where('url', '=', $url . ($i > 0 ? '-' . $i : ''));
            if ($excludeEventID) {
                $q->and_where('id', '<>', $excludeEventID);
            }
            $exists = $q->execute()->get('id');
        }

        if ($exists) {
            $url .= '-' . md5(microtime(true));
        } else {
            if ($i > 1) {
                $url .= '-' . $i;
            }
        }
        return $url;
    }

    public static function calculateUrlForVenue($name, $excludeID = null)
    {
        $url = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
        $exists = true;
        for ($i = 0 ; $i < 10 && $exists ; ++$i) {
            $q = DB::select('id')->from(self::TABLE_VENUES)
                ->where('url', '=', $url . ($i > 0 ? '-' . $i : ''));
            if ($excludeID) {
                $q->and_where('id', '<>', $excludeID);
            }
            $exists = $q->execute()->get('id');
        }

        if ($exists) {
            $url .= '-' . md5(microtime(true));
        } else {
            if ($i > 1) {
                $url .= '-' . $i;
            }
        }
        return $url;
    }

    public static function calculateUrlForOrganizer($name, $excludeID = null)
    {
        $url = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
        $exists = true;
        for ($i = 0 ; $i < 10 && $exists ; ++$i) {
            $q = DB::select('contact_id')->from(self::TABLE_ORGANIZERS)
                ->where('url', '=', $url . ($i > 0 ? '-' . $i : ''));
            if ($excludeID) {
                $q->and_where('contact_id', '<>', $excludeID);
            }
            $exists = $q->execute()->get('contact_id');
        }

        if ($exists) {
            $url .= '-' . md5(microtime(true));
        } else {
            if ($i > 1) {
                $url .= '-' . $i;
            }
        }
        return $url;
    }

    public static function autocomplete_tag_list($term = '')
    {
        $tags = DB::select(
            array("tags.id", "value"),
            array("tags.tag", "label")
        )
            ->from(array(self::TABLE_TAGS, 'tags'))
            ->where('deleted', '=', 0)
            ->and_where('tags.tag', 'like', '%' . $term . '%')
            ->order_by('tag')
            ->limit(20)
            ->execute()
            ->as_array();

        return $tags;
    }

    public static function autocomplete_venue_list($term = '', $ownerId = null)
    {

        $q = DB::select(
            'venues.*',
            array("venues.id", "value"),
            array("venues.name", "label")
        )
            ->from(array(self::TABLE_VENUES, 'venues'))
            ->where('deleted', '=', 0)
            ->and_where('venues.name', 'like', '%' . $term . '%');
        if ($ownerId) {
            $q->and_where('created_by', '=', $ownerId);
        }
        $venues = $q->order_by('venues.name')
            ->limit(20)
            ->execute()
            ->as_array();

        foreach ($venues as $i => $venue) {
            if ($venue['image_media_id']) {
                $venues[$i]['image_media_url'] = Model_Media::get_path_to_id($venue['image_media_id']);
            }
        }
        return $venues;
    }

    public static function autocomplete_organiser_list($term = '', $ownerId = null)
    {

        $q = DB::select(
            DB::expr('DISTINCT organisers.*'),
            'contacts.*',
            array("contacts.id", "value"),
            DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) AS label"),
            'has.description'
        )
            ->from(array(self::TABLE_ORGANIZERS, 'organisers'))
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')->on('organisers.contact_id', '=', 'contacts.id')
                ->join(array(self::TABLE_HAS_ORGANIZERS, 'has'), 'left')->on('organisers.contact_id', '=', 'has.contact_id')
            ->where('contacts.deleted', '=', 0)
            ->and_where_open()
                ->or_where('contacts.first_name', 'like', '%' . $term . '%')
                ->or_where('contacts.last_name', 'like', '%' . $term . '%')
            ->and_where_close();
        if ($ownerId) {
            Model_Contacts::limited_user_access_filter($q, $ownerId, 'contacts.id');
        }
        $organisers = $q->order_by('contacts.first_name')
            ->order_by('contacts.last_name')
            ->limit(20)
            ->execute()
            ->as_array();

        foreach ($organisers as $i => $organiser) {
            if ($organiser['profile_media_id']) {
                $organisers[$i]['profile_media_url'] = Model_Media::get_path_to_id($organiser['profile_media_id']);
            }
            if ($organiser['banner_media_id']) {
                $organisers[$i]['banner_media_url'] = Model_Media::get_path_to_id($organiser['banner_media_id']);
            }
        }

        return $organisers;
    }

    public static function venueLoad($id)
    {
        $venue = DB::select('*')
            ->from(self::TABLE_VENUES)
            ->where('id', '=', $id)
            ->execute()
            ->current();

        return $venue;
    }

    public static function getCurrencies($selected = 'EUR')
    {
        return html::optionsFromArray(
            array(
                'EUR' => 'EUR'
            ),
            $selected
        );
    }

    public static function getCommissionTypes($selected = 'Fixed')
    {
        return html::optionsFromArray(
            array(
                'Fixed' => __('Fixed'),
                'Percent' => __('Percent')
            ),
            $selected
        );
    }

	// Get a country's name from its ID
	public static function getCountryName($id)
	{
		return DB::select('name')->from('engine_countries')->where('id', '=', $id)->and_where('deleted', '=', 0)->execute()->get('name', 0);
	}

    public static function getCountryMatrix()
    {
        $countries = DB::select('*')
            ->from('engine_countries')
            ->where('deleted', '=', 0)
            // Put "Ireland" first. Put other countries with "Ireland" in their name next.
            ->order_by(DB::expr("CASE
                WHEN `name` = 'Ireland' THEN -2
                WHEN `name` like '%Ireland%' THEN -1
                ELSE 0
                END"), 'ASC')
            ->order_by('name')
            ->execute()
            ->as_array();
        $result = array();
        foreach ($countries as $i => $country) {
            $country['counties'] = DB::select('*')
                ->from('engine_counties')
                ->where('country_id', '=', $country['id'])
                ->order_by('name')
                ->execute()
                ->as_array();
            $result["id_" . $country['id']] = $country;
        }
        return $result;
    }

    /* Get categories that have ongoing events */
    public static function get_active_categories($params = array())
    {
        $categories = array();

        $search = Model_Event::get_for_global_search($params);

        foreach ($search['all_data'] as $result) {
            $data = $result['data'];

            // Get categories that exist within the search results
            if (!empty($result['data']['category']) && !isset($categories[$data['category']])) {
                $categories[$data['category']] = array(
                    'id'   => $data['category_id'],
                    'name' => $data['category']
                );
            }
            ksort($categories);
        }

        return $categories;
    }

    public static function accountDetailsLoad($owner_id)
    {
        $account = DB::select('*')
            ->from(self::TABLE_ACCOUNTS)
            ->where('owner_id', '=', $owner_id)
            ->execute()
            ->current();

		// If the account does not exist, return an associative array with empty values
		if (empty($account))
		{
			$account = array();
			$columns = Database::instance()->list_columns(self::TABLE_ACCOUNTS);
			foreach ($columns as $column => $data)
			{
				$account[$column] = '';
			}
		}

        if ($account['stripe_auth']) {
            $account['stripe_auth'] = json_decode($account['stripe_auth'], true);
        }
        return $account;
    }

    public static function accountDetailsSave($data)
    {
        $user = Auth::instance()->get_user();
        $account = self::accountDetailsLoad($data['owner_id']);
        if (is_array($data['stripe_auth'])) {
            $data['stripe_auth'] = json_encode($data['stripe_auth']);
        }
        if ( ! empty($account['id'])) {
            $data['updated'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $user['id'];
            DB::update(self::TABLE_ACCOUNTS)
                ->set($data)
                ->where('owner_id', '=', $data['owner_id'])
                ->execute();
        } else {
            unset($data['id']); // This will be a new record. Ensure this field is blank, so there's no error on insert
            $data['updated'] = $data['created'] = date('Y-m-d H:i:s');
            //during register process $auth->user is empty.
            if (isset($user['id'])) {
                $data['updated_by'] = $data['created_by'] = $user['id'];
            } else {
                if (isset($data['owner_id'])) {
                    $data['updated_by'] = $data['created_by'] = $user['id'];
                }
            }
            if (@$data['owner_id'] == null && isset($user['id'])) {
                $data['owner_id'] = $user['id'];
            }
            DB::insert(self::TABLE_ACCOUNTS)
                ->values($data)
                ->execute();
        }
    }

    public static function  checkoutDetailsLoad($owner_id)
    {
        $checkoutDetails = DB::select('*')
            ->from(self::TABLE_CHECKOUT_DETAILS)
            ->where('owner_id', '=', $owner_id)
            ->execute()
            ->current();

        // If the account does not exist, return an associative array with empty values
        if (empty($checkoutDetails))
        {
            $checkoutDetails = array();
            $columns = Database::instance()->list_columns(self::TABLE_CHECKOUT_DETAILS);
            foreach ($columns as $column => $data)
            {
                $checkoutDetails[$column] = '';
            }
        }

        // If no country has been selected, select the Republic of Ireland
        if (empty($checkoutDetails['country_id']))
        {
            $roi_variants = array('Ireland (Republic Of)', 'Ireland - (Republic Of)', 'Ireland', 'Republic of Ireland');
            $checkoutDetails['country_id'] = DB::select('id')
                ->from('engine_countries')
                ->where('name', 'in', $roi_variants)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->get('id', 0);
        }

        return $checkoutDetails;
    }

    public static function checkoutDetailsSave($owner_id, $data)
    {
        $checkoutDetails = self::checkoutDetailsLoad($owner_id);
        $user = Auth::instance()->get_user();
        $data['updated'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $user['id'];
        if ( ! empty($checkoutDetails['id'])) {
            DB::update(self::TABLE_CHECKOUT_DETAILS)
                ->set($data)
                ->where('owner_id', '=', $owner_id)
                ->execute();
        } else {
            $data['owner_id'] = $owner_id;
            DB::insert(self::TABLE_CHECKOUT_DETAILS)
            ->values($data)
            ->execute();
        }
    }

    public static function commissionGet($event, $owner_id, $useCache = false)
    {
        static $cache = array();
        if ($useCache) {
            if (isset($cache[$owner_id])) {
                return $cache[$owner_id];
            }
        }
        $account = self::accountDetailsLoad($owner_id);
        $commission = array();
        if ($account['commission_type'] == '') {
            $commission['type'] = Settings::instance()->get('events_commission_type');
            $commission['amount'] = Settings::instance()->get('events_commission_amount');
            $commission['currency'] = Settings::instance()->get('events_commission_currency');
            $commission['fixed_charge_amount'] = Settings::instance()->get('events_fixed_charge_amount');
        } else {
            $commission['type'] = $account['commission_type'];
            $commission['amount'] = $account['commission_amount'];
            $commission['currency'] = $account['commission_currency'];
            $commission['fixed_charge_amount'] = $account['fixed_charge_amount'];
        }

        if ( ! empty($event) && isset($event['commission_type']))
        {
            if ($event['commission_type'])   $commission['type']                = $event['commission_type'];
            if ($event['commission_amount']) $commission['amount']              = $event['commission_amount'];
            if ($event['currency'])          $commission['currency']            = $event['currency'];
            if ($event['commission_fixed_amount']) $commission['fixed_charge_amount'] = $event['commission_fixed_amount'];
        }

        if ($useCache) {
            $cache[$owner_id] = $commission;
        }
        
        return $commission;
    }

    public static function ticketTotalCalculate($price, $includeCommission, $ownerId, $useCache = false)
    {
        static $vatRate = null;
        if ($vatRate == null) {
            $vatRate = (float)Settings::instance()->get('vat_rate');
        }
        $commission = self::commissionGet(NULL, $ownerId, $useCache);
        $result = array('fixed' => 0, 'price' => $price, 'total' => 0, 'vat' => 0, 'commission' => 0);
        if ($price == 0){
            return $result;
        }
        $result['fixed'] = $commission['fixed_charge_amount'];
        if ($includeCommission == 1) {
            $result['total'] = $price;
            $result['vat'] = $result['total'] * ($vatRate / (1 + $vatRate));
            if ($commission['type'] == 'Fixed') {
                $result['commission'] = $commission['amount'] + $result['fixed'];
            } else {
                $stotal = $result['total'] - $result['vat'] - $result['fixed'];
                $result['commission'] = ($stotal * ($commission['amount'] / (100 + $commission['amount']))) + $result['fixed'];
            }
        } else {
            $result['total'] = $price;
            if ($commission['type'] == 'Fixed') {
                $result['commission'] = $commission['amount'] + $result['fixed'];
            } else {
                $result['commission'] = self::round2(($commission['amount'] / 100) * $price, 2) + $result['fixed'];
            }
            $result['total'] += $result['commission'];
            $result['vat'] = self::round2($result['total'] * $vatRate, 2);
            $result['total'] += $result['vat'];
        }
        return $result;
    }

    public static function orderLoad($id, $owner_id = null)
    {
        self::paymentsCancelIfProcessing();
        $q = DB::select('orders.*', array('country.name', 'country'))
            ->from(array(self::TABLE_ORDERS, 'orders'))
                ->join(array(self::TABLE_ACCOUNTS, 'accounts'), 'inner')->on('orders.account_id', '=', 'accounts.id')
                ->join(array('engine_countries',    'country'), 'left' )->on('orders.country_id', '=', 'country.id')
            ->where('orders.id', '=', $id);
        if ($owner_id) {
            $q->and_where('accounts.owner_id', '=', $owner_id);
        }
        $order = $q
            ->execute()
            ->current();
        if ($order) {
            $buyer = new Model_Users($order['buyer_id']);
            $order['total_paid'] = DB::select(DB::expr("SUM(amount) as total_paid"))
                ->from(array(self::TABLE_PAYMENTS, 'payments'))
                ->where('payments.order_id', '=', $id)
                ->and_where('payments.deleted', '=', 0)
                ->and_where('payments.paymentgw', '<>' ,'Payment Plan')
                ->and_where('payments.status', '=', 'PAID')
                ->execute()
                ->get('total_paid');

            $order['buyer'] = $buyer->get_user($order['buyer_id']);
            $order['items'] = DB::select(
                'items.*',
                'ticket_types.type',
                'ticket_types.name',
                'ticket_types.sleep_capacity',
				array('ticket_types.description', 'ticket_description'),
                'ticket_types.event_id',
                array('events.name', 'event'),
				array('events.owned_by', 'event_owner_id'),
                'events.one_ticket_for_all_dates',
                'events.age_restriction',
                array('venues.name', 'venue'),
                DB::expr("CONCAT_WS(CHAR(10 using utf8), venues.address_1, venues.address_2, venues.address_3, venues.city, vcounties.name) AS address"),
                'events.image_media_id',
                DB::expr("GROUP_CONCAT(date_format(dates.starts,'%d/%m/%Y %H:%s') SEPARATOR ',') AS dates"),
                DB::expr("min(dates.starts) AS starts"),
                DB::expr("GROUP_CONCAT(date_format(dates.ends,'%d/%m/%Y %H:%s') SEPARATOR ',') AS dates_end")
            )
                ->from(array(self::TABLE_ORDER_ITEMS, 'items'))
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                    ->join(array(self::TABLE_DATES, 'dates'), 'left')->on('idates.date_id', '=', 'dates.id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
                    ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
                    ->join(array(self::TABLE_VENUES, 'venues'), 'left')->on('events.venue_id', '=', 'venues.id')
                    ->join(array('engine_counties', 'vcounties'), 'left')->on('venues.county_id', '=', 'vcounties.id')
                    ->join(array(self::TABLE_HAS_ORGANIZERS, 'haso'), 'left')->on('events.id', '=', 'haso.event_id')
                    ->join(array(self::TABLE_ORGANIZERS, 'organisers'), 'left')->on('haso.contact_id', '=', 'organisers.contact_id')
                    ->join(array(Model_Contacts::TABLE_CONTACT, 'organisersc'), 'left')->on('organisers.contact_id', '=', 'organisersc.id')
                ->and_where('items.order_id', '=', $id)
                ->group_by('items.id')
                ->execute()
                ->as_array();
            $order['items_total'] = 0;
            foreach ($order['items'] as $item) {
                $order['items_total'] += round($item['total'] * $item['quantity'], 2);
            }
            $order['idates'] = DB::select('idates.*', 'items.quantity', 'items.sleeping', 'dates.starts', 'dates.ends', 'dates.event_id', 'dates.others')
                ->from(array(self::TABLE_ORDER_ITEM_DATES, 'idates'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('idates.order_item_id', '=', 'items.id')
                    ->join(array(self::TABLE_DATES, 'dates'), 'inner')->on('idates.date_id', '=', 'dates.id')
                ->and_where('items.order_id', '=', $id)
                ->execute()
                ->as_array();
            $order['payments'] = DB::select('*')
                ->from(array(self::TABLE_PAYMENTS, 'payments'))
                ->where('payments.order_id', '=', $id)
                ->and_where('payments.deleted', '=', 0)
                ->execute()
                ->as_array();
            $payment_ids = array();
            foreach ($order['payments'] as $payment) {
                $payment_ids[] = $payment['id'];
            }
            $order['partialpayments'] = DB::select('partial_payments.*', 'payments.status', 'pp.title')
                ->from(array(self::TABLE_PARTIAL_PAYMENTS, 'partial_payments'))
                    ->join(array(self::TABLE_PAYMENTS, 'payments'), 'left')
                        ->on('payments.id', '=', 'partial_payments.payment_id')
                    ->join(array(self::TABLE_HAS_PAYMENTPLANS, 'pp') ,'left')
                        ->on('partial_payments.paymentplan_id', '=', 'pp.id')
                ->and_where('partial_payments.main_payment_id', 'in', $payment_ids)
                ->execute()
                ->as_array();

            $order['tickets'] = DB::select(
                'tickets.*',
                'items.ticket_type_id',
                'items.order_id',
                'orders.buyer_id',
				array('orders.total', 'price'),
				'orders.currency',
                array('events.name', 'event'),
                'events.ticket_note',
                'events.email_note',
                "dates.starts",
                "dates.ends",
                'ticket_types.type',
                array('ticket_types.name', 'ticket'),
                DB::expr("CONCAT(buyers.name, ' ', buyers.surname, ' <' , buyers.email, '>') AS buyer"),
                array('venues.name', 'venue'),
                DB::expr("CONCAT_WS(' ', organisersc.first_name, organisersc.last_name) AS organiser_name"),
                array('organisersc.email', 'organiser_email'),
                array('organisersc.mobile', 'organiser_mobile'),
                array('organisersc.phone', 'organiser_phone'),
                array('organisers.website', 'organiser_website'),
                'events.image_media_id',
                array('orders.firstname', 'buyer_order_firstname'),
                array('orders.lastname', 'buyer_order_lastname')
            )
                ->from(array(self::TABLE_TICKETS, 'tickets'))
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('tickets.order_item_has_date_id', '=', 'idates.id')
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('idates.order_item_id', '=', 'items.id')
                    ->join(array(self::TABLE_ORDERS, 'orders'), 'inner')->on('items.order_id', '=', 'orders.id')
                    ->join(array(self::TABLE_ACCOUNTS, 'accounts'), 'inner')->on('orders.account_id', '=', 'accounts.id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
                    ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
                    ->join(array(Model_Users::MAIN_TABLE, 'buyers'), 'inner')->on('orders.buyer_id', '=', 'buyers.id')
                    ->join(array(self::TABLE_DATES, 'dates'), 'left')
                        ->on('events.id', '=', 'dates.event_id')
                        ->on('idates.date_id', '=', 'dates.id')
                    ->join(array(self::TABLE_VENUES, 'venues'), 'left')->on('events.venue_id', '=', 'venues.id')
                    ->join(array(self::TABLE_HAS_ORGANIZERS, 'haso'), 'left')
                        ->on('events.id', '=', 'haso.event_id')->on('haso.is_primary', '=', DB::expr(1))
                    ->join(array(self::TABLE_ORGANIZERS, 'organisers'), 'left')->on('haso.contact_id', '=', 'organisers.contact_id')
                    ->join(array(Model_Contacts::TABLE_CONTACT, 'organisersc'), 'left')->on('organisers.contact_id', '=', 'organisersc.id')
                ->where('tickets.deleted', '=', 0)
                ->and_where('orders.id', '=', $id)
                ->group_by('tickets.id')
                ->execute()
                ->as_array();

        }

        return $order;
    }

    public static function get_orders_for_datatable($params)
    {
        $params['datatable'] = true;
        $data = self::ordersList($params);

        $return = array(
            'iTotalDisplayRecords' => $data['total_found'],
            'iTotalRecords'        => $data['total_displayed'],
            'aaData'               => array()
        );

        $currencies = Model_Currency::getCurrencies(true);
        foreach ($data['orders'] as $order) {
            $row = array();
            $row[] = $order['id'];
            $row[] = trim($order['firstname'] . ' ' . $order['lastname']);
            $row[] = $order['email'];
            $row[] = '<a href="/admin/events/order_details/'.$order['id'].'" class="edit-link">'.$order['tickets'].'</a>';
            $row[] = $order['status'];
            if ($order['email_id']) {
                $row[] = '<a class="order_message_details" data-message_id="'.$order['email_id'].'" href="/admin/messaging/details?message_id='.$order['email_id'].'" target="_blank">'.__('Details').'</a>';
            } else if ($order['status'] == 'PAID') {
                $row[] = '<a class="order_message_send" data-order_id="'.$order['id'].'">'.__('Send Ticket PDF').'</a>';
            } else {
                $row[] = '';
            }
            $row[] = $order['created'];
            $row[] = ($order['total'] != $order['total_paid'] ? $currencies[$order['currency']]['symbol'].$order['total_paid'] . ' / ' : '') . $currencies[$order['currency']]['symbol'].$order['total'];
            $row[] = View::factory('admin/snippets/list_orders_actions')->set('order', $order)->render();
            $return['aaData'][] = $row;
        }
        $return['sEcho'] = intval($params['filters']['sEcho']);

        return $return;
    }

    public static function orderSetArchived($orderId, $archived)
    {
        $user = Auth::instance()->get_user();
        DB::update(self::TABLE_ORDERS)
            ->set(array(
                'archived' => $archived,
                'archived_by' => ($archived ? $user['id'] : null)
            ))
            ->where('id', '=', $orderId)
            ->execute();
    }

    public static function checkDuplicateOrder($email, $items)
    {
        foreach ($items as $item) {
            foreach ($item['dates'] as $date_id) {
                $bought = DB::select(DB::expr('SUM(items.quantity) as quantity'), 'dates.starts', array('ticket_types.name', 'ticket_type'), array('events.name', 'event'))
                    ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'has_dates'), 'inner')->on('items.id', '=', 'has_dates.order_item_id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
                    ->join(array(self::TABLE_DATES, 'dates'), 'inner')->on('has_dates.date_id', '=', 'dates.id')
                    ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
                    ->where('orders.status', '=', 'PAID')
                    ->and_where('orders.status_reason', '=', 'Payment Completed')
                    ->and_where('orders.email', '=', $email)
                    ->and_where('orders.deleted', '=', 0)
                    ->and_where('items.ticket_type_id', '=', $item['ticket_type_id'])
                    ->and_where('has_dates.date_id', '=', $date_id)
                    ->group_by('has_dates.date_id')
                    ->execute()
                    ->current();
                if ($bought) {
                    return $bought;
                }
            }
        }

        return null;
    }

    public static function orderSave($account, $order, $items, $cc, $payers = null, $payment_plan = false, $paymore_amount = null)
    {
        $result = array('error' => null);

        try {
            Database::instance()->begin();
            /*DB::query(
                null,
                'LOCK TABLES ' .
                self::TABLE_ORDERS . ' WRITE, ' .
                self::TABLE_ORDER_ITEMS . ' WRITE, ' .
                self::TABLE_PAYMENTS . ' WRITE, ' .
                self::TABLE_TICKETS . ' WRITE '
            )->execute();*/
            $now = date('Y-m-d H:i:s');
            if (isset($order['id'])) {
                $order['updated'] = $now;
                DB::update(self::TABLE_ORDERS)->set($order)->where('id', '=', $order['id'])->execute();
            } else {
                $order['created'] = $now;
                unset($order['subtotal']);
                $inserted = DB::insert(self::TABLE_ORDERS)->values($order)->execute();
                $order['id'] = $inserted[0];
            }

            if ($order['id']) {
                $result['order_id'] = $order['id'];
                foreach ($items as $item) {
                    if ($order['discount_code'] != '') {
                        $event_id = DB::select('event_id')
                            ->from(self::TABLE_HAS_TICKET_TYPES)
                            ->where('id', '=', $item['ticket_type_id'])
                            ->execute()
                            ->get('event_id');

                        $idiscounts = Model_Event::getDiscountsByCode($event_id, $item['ticket_type_id'], $order['discount_code']);
                        foreach ($idiscounts as $idiscount) {
                            DB::insert(self::TABLE_ODISCOUNTS)
                                ->values(array(
                                    'order_id' => $order['id'],
                                    'discount_id' => $idiscount['id']
                                ))
                                ->execute();
                        }
                    }

                    foreach ($item['pending'] as $pending) {
                        DB::update(self::TABLE_PENDING_TICKETS)
                            ->set(array('deleted' => 1))
                            ->where('id', '=', $pending['id'])
                            ->execute();
                    }
                    $inserted = DB::insert(self::TABLE_ORDER_ITEMS)->values(
                        array(
                            'order_id'        => $order['id'],
                            'quantity'        => $item['quantity'],
                            'ticket_type_id'  => $item['ticket_type_id'],
                            'donation'        => $item['donation'],
                            'price'           => $item['price'],
                            'vat'             => $item['vat'],
                            'total'           => $item['total'],
                            'discount'        => $item['discount'],
                            'discount_type'   => $item['discount_type'],
                            'discount_amount' => $item['discount_amount'],
                            'currency'        => $order['currency'],
                            'commission'      => $item['commission'],
                            'sleeping'        => $item['sleeping']
                        ))->execute();
                    foreach ($item['dates'] as $dateId) {
                        DB::insert(self::TABLE_ORDER_ITEM_DATES)
                            ->values(array(
                                'order_item_id' => $inserted[0],
                                'date_id' => $dateId
                            ))
                            ->execute();
                    }
                }

                $payment_amount = $order['total'];
                $result['generate_ticket'] = 1;
                $first_partial_payment_id = null;
                $partial_payers = array();
                $main_payment_id = null;
                if (is_array($payers) && count($payers) > 0 || is_array($payment_plan)) {
                    $payment = array();
                    $payment['order_id']            = $order['id'];
                    $payment['amount']              = $order['total'];
                    $payment['currency']            = $order['currency'];
                    $payment['status']              = 'Processing';
                    $payment['status_reason']       = 'New Payment';
                    $payment['paymentgw'] = 'Payment Plan';
                    $payment['paymentgw_info'] = '';
                    $inserted = DB::insert(self::TABLE_PAYMENTS)->values($payment)->execute();
                    $main_payment_id = $inserted[0];

                    $first_payment = true;
                    if (is_array($payers) && count($payers) > 0) {
                        foreach ($payers as $payer) {
                            if ($payment_plan) {
                                $pp_remains = $payer['amount'];
                                foreach ($payment_plan as $paymentplan) {
                                    $partial_payment = array(
                                        'main_payment_id' => $main_payment_id,
                                        'payer_email' => $payer['email'],
                                        'payer_name' => $payer['name'],
                                        'payment_amount' => $paymentplan['payment_amount'],
                                        'vat' => $paymentplan['vat'],
                                        'fee' => $paymentplan['fee'],
                                        'total' => $paymentplan['total'],
                                        'due_date' => $paymentplan['due_date'],
                                        'url_hash' => uniqid(),
                                        'paymentplan_id' => $paymentplan['id']
                                    );
                                    $partial_payment['payment_amount'] = min($partial_payment['payment_amount'], $pp_remains);
                                    $pp_remains -= $partial_payment['payment_amount'];
                                    $inserted = DB::insert(self::TABLE_PARTIAL_PAYMENTS)
                                        ->values(
                                            $partial_payment
                                        )
                                        ->execute();
                                    if ($first_payment) {
                                        $first_partial_payment_id = $inserted[0];
                                        $payment_amount = $partial_payment['payment_amount'];
                                        $first_payment = false;
                                    } else {
                                        $partial_payers[] = array(
                                            'title' => $paymentplan['title'],
                                            'email' => $payer['email'],
                                            'name' => $payer['name'],
                                            'amount' => $partial_payment['payment_amount'],
                                            'vat' => $partial_payment['vat'],
                                            'fee' => $partial_payment['fee'],
                                            'total' => $partial_payment['total'],
                                            'url_hash' => $partial_payment['url_hash'],
                                            'due_date' => $paymentplan['due_date'],
                                            'id' => $inserted[0]
                                        );
                                    }
                                }
                            } else {
                                $partial_payment = array(
                                    'main_payment_id' => $main_payment_id,
                                    'payer_email' => $payer['email'],
                                    'payer_name' => $payer['name'],
                                    'payment_amount' => $payer['amount'],
                                    'url_hash' => uniqid()
                                );
                                $inserted = DB::insert(self::TABLE_PARTIAL_PAYMENTS)
                                    ->values(
                                        $partial_payment
                                    )
                                    ->execute();
                                if ($first_payment) {
                                    $first_partial_payment_id = $inserted[0];
                                    $payment_amount = $payer['amount'];
                                    $first_payment = false;
                                } else {
                                    $partial_payers[] = array(
                                        'title' => $paymentplan['title'],
                                        'email' => $payer['email'],
                                        'name' => $payer['name'],
                                        'amount' => $payer['amount'],
                                        'vat' => $payer['vat'],
                                        'fee' => $payer['fee'],
                                        'total' => $payer['total'],
                                        'url_hash' => $partial_payment['url_hash'],
                                        'due_date' => null,
                                        'id' => $inserted[0]
                                    );
                                }
                            }
                        }
                    } else {
                        $first_payment = true;
                        foreach ($payment_plan as $paymentplan) {
                            $partial_payment = array(
                                'main_payment_id' => $main_payment_id,
                                'payer_email' => '',
                                'payer_name' => '',
                                'payment_amount' => $paymentplan['payment_amount'],
                                'vat_total' => $paymentplan['vat'],
                                'commission_total' => $paymentplan['fee'],
                                'total' => $paymentplan['total'],
                                'due_date' => $paymentplan['due_date'],
                                'url_hash' => uniqid(),
                                'paymentplan_id' => $paymentplan['id']
                            );
                            $inserted = DB::insert(self::TABLE_PARTIAL_PAYMENTS)
                                ->values(
                                    $partial_payment
                                )
                                ->execute();
                            if ($first_payment) {
                                $first_partial_payment_id = $inserted[0];
                                $first_partial_payment = $partial_payment;
                                $payment_amount = $partial_payment['total'];
                                $first_payment = false;
                            } else {
                                $partial_payers[] = array(
                                    'title' => $paymentplan['title'],
                                    'email' => '',
                                    'name' => '',
                                    'amount' => $partial_payment['payment_amount'],
                                    'vat' => $partial_payment['vat'],
                                    'fee' => $partial_payment['fee'],
                                    'total' => $partial_payment['total'],
                                    'url_hash' => $partial_payment['url_hash'],
                                    'due_date' => $paymentplan['due_date'],
                                    'id' => $inserted[0],
                                );
                            }
                        }
                    }
                }

                $payment = array();
                $payment['order_id']            = $order['id'];
                $payment['amount']              = $payment_amount;
                $payment['currency']            = $order['currency'];
                $payment['status']              = 'Processing';
                $payment['status_reason']       = 'New Payment';
                $payment['credit_card_type']    = $cc['type'];
                $payment['cc_last_four_digits'] = isset($cc['number']) ? substr(trim($cc['number']), -4) : null;
                $payment['created']             = $payment['created'] = $now;
                if ($payment['amount'] == 0) {
                    $payment['paymentgw'] = 'Free';
                    $payment['paymentgw_info'] = '';
                } else {
                    if ($account['use_stripe_connect'] == 1 || (int)Settings::instance()->get('enable_realex') == 0) {
                        $payment['paymentgw'] = 'stripe';
                        $payment['paymentgw_info'] = 'Request charge';
                    } else {
                        $payment['paymentgw'] = 'realex';
                        $payment['paymentgw_info'] = 'Request charge';
                    }
                }
                $inserted = DB::insert(self::TABLE_PAYMENTS)->values($payment)->execute();
                $payment['id'] = $inserted[0];
                if ($first_partial_payment_id) {
                    if ($account['use_stripe_connect'] == 1 && $account['stripe_auth'] && $account['stripe_auth']['stripe_user_id']) {
                        $payment['transfer_data']['destination'] = $account['stripe_auth']['stripe_user_id'];
                        $payment['application_fee_amount'] = $first_partial_payment['commission_total'] + $first_partial_payment['vat_total'];
                    }
                    DB::update(self::TABLE_PARTIAL_PAYMENTS)
                        ->set(array('payment_id' => $payment['id']))
                        ->where('id', '=', $first_partial_payment_id)
                        ->execute();
                    $result['generate_ticket'] = 0;
                }

                if ($payment['id']) {
                    $result['payment_id'] = $payment['id'];
                    $processed_payment = self::paymentProcess($account, $order, $payment, $cc);

                    if ($processed_payment['success'] === false){
                        DB::update(self::TABLE_ORDERS)
                            ->set(array('status' => 'CANCELLED', 'status_reason' => 'Payment Failed', 'updated' => date('Y-m-d H:i:s')))
                            ->where('id', '=', $order['id'])
                            ->execute();
                        Database::instance()->commit();

                        if (!empty($processed_payment['is_public_error']) && !empty($processed_payment['error'])) {
                            $result['error'] = $processed_payment['error'];
                        } else {
                            $result['error'] = __('Error processing payment. If this problem continues, please contact the administration and use this order number for reference, $1.', array('$1' => '<strong>'.$order['id'].'</strong>'));
                        }
                    } else {
                        if (@$processed_payment['payment_intent_secret']) {
                            $result['payment_intent_secret'] = $processed_payment['payment_intent_secret'];
                            $result['payment_public_key'] = $processed_payment['payment_public_key'];
                        } else {
                            DB::update(self::TABLE_ORDERS)
                                ->set(array(
                                    'status' => 'PAID',
                                    'status_reason' => 'Payment Completed',
                                    'updated' => date('Y-m-d H:i:s')
                                ))
                                ->where('id', '=', $order['id'])
                                ->execute();

                            $payment['status'] = 'PAID';
                            $payment_fee = self::calculate_paymentgw_fee($payment);
                            self::set_payment_fee($payment['id'], $payment_fee);

                            Database::instance()->commit();
                            if (count($partial_payers) == 0) {
                                self::ticketsGenerate($order['id']);
                            }

                            if (count($partial_payers) > 0) {
                                self::email_payment_plan($order['id']);
                            }
                        }
                    }
                }
            }

            //DB::query(null,'UNLOCK TABLES')->execute();
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            Model_Errorlog::save($exc);
            //DB::query(null,'UNLOCK TABLES')->execute();
            throw $exc;
        }

        return $result;
    }

    public static function checkStripePaymentIntent($pi)
    {
        require_once APPPATH . '/vendor/stripe6/init.php';

        $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
        $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
        $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        $payment = DB::select('*')
            ->from(self::TABLE_PAYMENTS)
            ->where('paymentgw', '=', 'stripe')
            ->and_where('paymentgw_info', '=', $pi)
            ->execute()
            ->current();

        if ($payment) {
            $intent = \Stripe\PaymentIntent::retrieve($pi);
            if ($intent->status == 'succeeded') {
                if ($payment['status'] == 'PROCESSING') {
                    DB::update(self::TABLE_PAYMENTS)
                        ->set(
                            array('status' => 'PAID', 'status_reason' => 'Completed')
                        )
                        ->where('id', '=', $payment['id'])
                        ->execute();

                    DB::update(self::TABLE_ORDERS)
                        ->set(array(
                            'status' => 'PAID',
                            'status_reason' => 'Payment Completed',
                            'updated' => date('Y-m-d H:i:s')
                        ))
                        ->where('id', '=', $payment['order_id'])
                        ->execute();

                    $payment['status'] = 'PAID';
                    $payment_fee = self::calculate_paymentgw_fee($payment);
                    self::set_payment_fee($payment['id'], $payment_fee);

                    $is_partial_payment = DB::select('*')
                        ->from(self::TABLE_PARTIAL_PAYMENTS)
                        ->and_where('payment_id', '=', $payment['id'])
                        ->execute()
                        ->current();
                    $partial_payers = array();
                    if ($is_partial_payment) {
                        $partial_payers = DB::select('*')
                            ->from(self::TABLE_PARTIAL_PAYMENTS)
                            ->where('main_payment_id', '=', $is_partial_payment['main_payment_id'])
                            ->and_where('payment_id', 'is', null)
                            ->execute()
                            ->as_array();
                        $result = array();
                        $process = self::sendPartialPaymentProcessedEmail($is_partial_payment['id'], $result);
                        $payments_remaining = Model_Event::calculate_partial_payments_remaing($is_partial_payment['id']);
                        if ($payments_remaining['balance'] <= 0) {
                            $intent['generate_ticket'] = 1;
                        }
                        $tickets_generated = !empty($process['generate_ticket']);
                    } else {
                        $intent['generate_ticket'] = 1;
                    }
                    if (count($partial_payers) == 0 && empty($tickets_generated)) {
                        self::ticketsGenerate($payment['order_id']);
                    }

                    if (count($partial_payers) > 0) {
                        self::email_payment_plan($payment['order_id']);
                    }
                } else {
                    $intent['generate_ticket'] = 1;
                }
            }
            return $intent;
        }
    }

    public static function sendPartialPaymentProcessedEmail($partial_payment_id, $result)
    {
        $partial_payment = self::get_order_from_partial_payment_id($partial_payment_id);
        $order = $partial_payment['order'];

        $organisers = (!empty($order['items']) && !empty($order['items'][0]['event_id'])) ? Model_Event::getEventOrganisers($order['items'][0]['event_id']) : [];

        $payments_remaining = Model_Event::calculate_partial_payments_remaing($partial_payment['partial_payment']['id']);

        $message_parameters = array();
        $message_parameters['address_1'] = $order['address_1'];
        $message_parameters['address_2'] = $order['address_2'];
        $message_parameters['balance'] = $order['currency'] . $payments_remaining['total'];
        $message_parameters['base_url'] = Url::site();
        $message_parameters['buyer'] = $order['firstname'] . ' ' . $order['lastname'];
        $message_parameters['buyer_help_url'] = Url::site('support');
        $message_parameters['due_date'] = date('j F Y', $payments_remaining['due_date0']);
        $message_parameters['city'] = $order['city'];
        $message_parameters['country'] = Model_Event::getCountryName($order['country_id']);
        $message_parameters['county'] = $order['county'];
        $message_parameters['email'] = $order['email'];
        $message_parameters['eventdate'] = date('j F Y', strtotime($order['items'][0]['starts']));
        $message_parameters['eventname'] = $order['items'][0]['event'];
        $message_parameters['firstname'] = $order['firstname'];
        $message_parameters['logosrc'] = Model_Media::get_image_path('logo.png', 'content');
        $message_parameters['order_id'] = $order['id'];
        $message_parameters['paid'] = $order['currency'] . $partial_payment['partial_payment']['total'];
        $message_parameters['payer'] = $order['firstname'] . ' ' . $order['lastname'];
        $message_parameters['payment_id'] = $partial_payment['partial_payment']['payment_id'];
        $message_parameters['profile_url'] = Url::site('admin/profile/edit?section=contact');
        $message_parameters['project'] = Settings::instance()->get('company_title');
        $message_parameters['telephone'] = $order['telephone'];

        if (count($organisers) > 0) {
            $email_parameters['organiser_name'] = $organisers[0]['first_name'] . ' ' . $organisers[0]['last_name'];
            $email_parameters['organiser_email'] = $organisers[0]['email'];
        } else {
            $email_parameters['organiser_name'] = '';
            $email_parameters['organiser_email'] = '';
        }

        $recipients = array(
            array(
                'target_type' => 'EMAIL',
                'target' => $order['email']
            )
        );

        $mm = new Model_Messaging();

        if ($payments_remaining['balance'] <= 0) {
            $mm->send_template('event-partial-payment-completed-nobalance', null, null, $recipients, $message_parameters);
            $result['generate_ticket'] = 1;
        } else {
            $mm->send_template('event-partial-payment-completed', null, null, $recipients, $message_parameters);
        }
        return $result;
    }

    public static function paymentProcess(&$account, &$order, &$payment, $cc, $partial_payment_id = null)
    {
        $descriptorSuffix = Kohana::$environment == Kohana::PRODUCTION ? '' : '-' . Kohana::$environment;

        if ($payment['paymentgw'] == 'stripe') {
            require_once APPPATH . '/vendor/stripe6/init.php';

            $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
            $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
            $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
            \Stripe\Stripe::setApiKey($stripe['secret_key']);

            $statement_descriptor = ($partial_payment_id ? 'Order ' : 'uTicket Order ') . // to handle 22 character limit
                $order['id'] . ($partial_payment_id ? '-' . $partial_payment_id : '') . $descriptorSuffix;

            try {
                $charge_params = array(
                    'amount' => $payment['amount'] * 100,
                    'currency' => $order['currency'],
                    'statement_descriptor' => $statement_descriptor,
                    'description' => $statement_descriptor
                );
                if ($account['use_stripe_connect'] == 1 && $account['stripe_auth'] && $account['stripe_auth']['stripe_user_id']) {
                    if (@$payment['transfer_data']['destination']) {
                        $charge_params['transfer_data'] = array('destination' => $payment['transfer_data']['destination']);
                    } else {
                        $charge_params['transfer_data'] = array('destination' => $account['stripe_auth']['stripe_user_id']);
                    }

                    if (@$payment['application_fee_amount']) {
                        $charge_params['application_fee_amount'] = floor($payment['application_fee_amount'] * 100);
                    } else {
                        $fixed_fee = $order['commission_fixed_charge_amount'];
                        $fixed_fee += ($order['commission_type'] != 'Percent' ? $order['commission_amount'] : 0);
                        $percentage_fee = $order['commission_type'] == 'Percent' ? $order['commission_amount'] : 0;

                        $breakdown = self::calculate_price_breakdown(
                            $payment['amount'],
                            $fixed_fee,
                            $percentage_fee,
                            $order['vat_rate'],
                            true
                        );

                        $charge_params['application_fee_amount'] = floor(($breakdown['fee'] + $breakdown['vat']) * 100);
                    }
                }

                $charge = \Stripe\PaymentIntent::create($charge_params);

                DB::update(self::TABLE_PAYMENTS)
                    ->set(array(
                        'paymentgw_info' => (string)$charge->id,
                        'paymentgw_info_2' => serialize($account['stripe_auth']),
                        'status' => 'Processing',
                        'status_reason' => '3DS2',
                        'updated' => date('Y-m-d H:i:s'),
                        'statement_descriptor' => $statement_descriptor
                    ))
                    ->where('id', '=', $payment['id'])
                    ->execute();

                if ($partial_payment_id) {
                    DB::update(self::TABLE_PARTIAL_PAYMENTS)
                        ->set(array('payment_id' => $payment['id']))
                        ->where('id', '=', $partial_payment_id)
                        ->execute();
                }
                return ['success' => true, 'payment_intent_secret' => $charge->client_secret, 'payment_public_key' => $stripe['publishable_key']];
            } catch (Exception $e) {
                DB::update(self::TABLE_PAYMENTS)
                    ->set(array(
                        'paymentgw_info' => 'error:' . $e->getMessage(),
                        'statement_descriptor' => $statement_descriptor,
                        'status' => 'ERROR',
                        'status_reason' => 'Error',
                        'updated' => date('Y-m-d H:i:s')
                    ))
                    ->where('id', '=', $payment['id'])
                    ->execute();

                return [
                    'success' => false,
                    'error'   => $e->getMessage(),
                    'is_public_error' => (get_class($e) == 'Stripe_CardError')
                ];
            }
        } else if ($payment['paymentgw'] == 'realex') {
            $realex = new Model_Realvault();
            // Realex does not allow for spaces in order IDs
            $realexOrderId = 'uTicket_Order_' . $order['id']. ($partial_payment_id ? '_' . $partial_payment_id : '') . $descriptorSuffix;
            try {
                $realexResult = $realex->charge(
                    $realexOrderId,
                    $payment['amount'],
                    $order['currency'],
                    $cc['number'],
                    $cc['month'] . ($cc['year'] % 100),
                    $cc['type'],
                    $cc['name'],
                    $cc['cvc']
                );
                if ((string)$realexResult->result == '00') {
                    DB::update(self::TABLE_PAYMENTS)
                        ->set(array(
                            'paymentgw_info' => (string)$realexResult->authcode . ':' . (string)$realexResult->pasref,
                            'statement_descriptor' => $realexOrderId,
                            'status' => 'Paid',
                            'status_reason' => 'Success',
                            'updated' => date('Y-m-d H:i:s')
                        ))
                        ->where('id', '=', $payment['id'])
                        ->execute();
                    if ($partial_payment_id) {
                        DB::update(self::TABLE_PARTIAL_PAYMENTS)
                            ->set(array('payment_id' => $payment['id']))
                            ->where('id', '=', $partial_payment_id)
                            ->execute();
                    }
                    return ['success' => true];
                } else {
                    DB::update(self::TABLE_PAYMENTS)
                        ->set(array(
                            'paymentgw_info' => '',
                            'status' => 'ERROR',
                            'status_reason' => $realexResult->message,
                            'updated' => date('Y-m-d H:i:s'),
                            'statement_descriptor' => $realexOrderId
                        ))
                        ->where('id', '=', $payment['id'])
                        ->execute();
                    return [
                        'success' => false,
                        'error'   => $realexResult->message,
                        'is_public_error' => true
                    ];
                }
            } catch (Exception $exc) {
                DB::update(self::TABLE_PAYMENTS)
                    ->set(array(
                        'paymentgw_info' => 'error:' . $exc->getMessage(),
                        'status' => 'ERROR',
                        'status_reason' => $exc->getMessage(),
                        'updated' => date('Y-m-d H:i:s')
                    ))
                    ->where('id', '=', $payment['id'])
                    ->execute();
                return [
                    'success' => false,
                    'error'   => $exc->getMessage(),
                    'is_public_error' => (get_type($exc) == 'RealexValidationException')
                ];
            }
        } else if ($payment['paymentgw'] == 'Free') {
            DB::update(self::TABLE_PAYMENTS)
                ->set(array(
                    'status' => 'Paid',
                    'status_reason' => 'Free',
                    'updated' => date('Y-m-d H:i:s')
                ))
                ->where('id', '=', $payment['id'])
                ->execute();

            return ['success' => true];
        }

        return ['success' => null];
    }

    public static function paymentRefund($paymentId, $amount = null, $reason = null)
    {

        $payment = DB::select('*')
            ->from(self::TABLE_PAYMENTS)
            ->where('id', '=', $paymentId)
            ->execute()
            ->current();
        if ($payment) {
            $order = self::orderLoad($payment['order_id']);
            $account = DB::select('*')
                ->from(self::TABLE_ACCOUNTS)
                ->where('id', '=', $order['account_id'])
                ->execute()
                ->current();
            if ($payment['status'] == 'PAID') {

                try {
                    Database::instance()->begin();
                    $cancelled = false;
                    if ($payment['paymentgw'] == 'stripe') {
                        require_once APPPATH . '/vendor/stripe/lib/Stripe.php';

                        $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
                        $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
                        $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
                        Stripe::setApiKey($stripe['secret_key']);

                        /*$refund = Stripe_Refund::create(array(
                            'charge' => $payment['paymentgw_info'],
                            'reason' => $reason
                        ));*/
                        $params = array('charge' => $payment['paymentgw_info']);
                        $refund_application_fee = false;
                        if ($refund_application_fee) {
                            $params['refund_application_fee'] = "true";
                        }
                        $params['reverse_transfer'] = "true";
                        if ($amount) {
                            $params['amount'] = $amount * 100;
                        }
                        if ($reason) {
                            $params['reason'] = $reason;
                            if ($reason == self::REFUND_REASON_EVENT_CANCELED || $reason == self::REFUND_REASON_EVENT_POSTPONED){
                                $params['reason'] = self::REFUND_REASON_REQUESTED_BY_CUSTOMER;
                            }
                        }
                        
                        $req = curl_init('https://api.stripe.com/v1/refunds');
                        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($req, CURLOPT_POST, true);
                        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($params));
                        curl_setopt($req, CURLOPT_USERPWD, $stripe['secret_key'] . ':');
                        $refund = json_decode(curl_exec($req));

                        DB::update(self::TABLE_PAYMENTS)
                            ->set(array(
                                'status' => 'REFUND',
                                'status_reason' => ($reason ? $reason . '; ' : '') . 'refund ref:' . $refund->id . ' => ' . $amount,
                                'updated' => date('Y-m-d H:i:s'),
                            ))->where('id', '=', $payment['id'])
                            ->execute();
                        $cancelled = true;
                    } else if ($payment['paymentgw'] == 'realex') {
                        $authcode_ref = explode(':', $payment['paymentgw_info']);
                        $realex = new Model_Realvault();
                        $result = $realex->rebate($authcode_ref[0], $authcode_ref[1], 'event-order-' . $order['id'], $amount, $order['currency']);
                        if ((string)$result->result == '00') {
                            DB::update(self::TABLE_PAYMENTS)
                                ->set(array(
                                    'status' => 'REFUND',
                                    'status_reason' => ($reason ? $reason . '; ' : '') . 'refund ref:' . (string)$result->pasref . ' => ' . $amount,
                                    'updated' => date('Y-m-d H:i:s'),
                                ))->where('id', '=', $payment['id'])
                                ->execute();
                            $cancelled = true;
                        }
                    }
                    if ($cancelled) {
                        DB::update(self::TABLE_ORDERS)
                            ->set(array(
                                'status' => 'CANCELLED',
                                'status_reason' => 'Payment Refunded',
                                'updated' => date('Y-m-d H:i:s')
                            ))->where('id', '=', $order['id'])
                            ->execute();
                    }
                    self::eventsSoldUpdate();
                    Database::instance()->commit();
                    if (!$cancelled) {
                        ibhelpers::alert('Refund failed', 'warning');
                    }
                    return $order;
                } catch (Exception $exc) {
                    Database::instance()->rollback();
                    throw $exc;
                }
            } else {
                throw new Exception("Only payments with status \"PAID\" can be refunded");
            }
        } else {
            return null;
        }
    }

    public static function orderStatusChange($orderId, $status, $reason)
    {
        DB::update(self::TABLE_ORDERS)
            ->set(array('status' => $status, 'status_reason' => $reason))
            ->where('id', '=', $orderId)
            ->execute();
        self::eventsSoldUpdate();
    }

    public static function ordersList($params = array())
    {
        self::paymentsCancelIfProcessing();
        $totalsq1 = DB::select(
            'orders.id',
            DB::expr("SUM(items.quantity) AS quantity")
        )
            ->from(array(self::TABLE_ORDERS, 'orders'))
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ttypes.event_id', '=', 'events.id')
            ->where('orders.status', '=', 'PAID')
            ->and_where('orders.deleted', '=', 0)
            ->group_by('orders.id');
        $totalsq2 = clone $totalsq1;

        $totalsq1->and_where('events.one_ticket_for_all_dates', '=', 1);

        $totalsq2->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id');
        $totalsq2->and_where('events.one_ticket_for_all_dates', '=', 0);

        $totalsq3 = DB::select("payments.order_id", DB::expr("SUM(payments.amount) as total_paid"))
        ->from(array(self::TABLE_PAYMENTS, 'payments'))
            ->where('payments.deleted', '=', DB::expr(0))
            ->where('payments.paymentgw', '<>' ,DB::expr("'Payment Plan'"))
            ->where('payments.status', '=', DB::expr("'PAID'"))
            ->group_by("payments.order_id");

        $q = DB::select(
            DB::expr("SQL_CALC_FOUND_ROWS `orders`.*"),
            array('countries.name', 'country'),
            DB::expr("CONCAT_WS(' ', buyers.name, buyers.surname) AS ubuyer"),
            DB::expr("CONCAT_WS(' ', orders.firstname, orders.lastname) AS buyer"),
            array('buyers.email', 'buyer_email'),
            DB::expr("GROUP_CONCAT(CONCAT(ticket_types.type, ': ', events.name, ' / ', ticket_types.name)) AS tickets"),
            DB::expr("IF(events.one_ticket_for_all_dates = 1, totals_1.quantity, totals_2.quantity) AS ticket_quantity"),
            'totals_3.total_paid'
        )
            ->from(array(self::TABLE_ORDERS, 'orders'))
                ->join(array(self::TABLE_ACCOUNTS, 'accounts'), 'inner')->on('orders.account_id', '=', 'accounts.id')
                ->join(array(Model_Users::MAIN_TABLE, 'buyers'), 'inner')->on('orders.buyer_id', '=', 'buyers.id')
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
                ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
                ->join(array('engine_countries', 'countries'), 'left')->on('orders.country_id', '=', 'countries.id')
                ->join(array($totalsq1, 'totals_1'), 'left')->on('orders.id', '=', 'totals_1.id')
                ->join(array($totalsq2, 'totals_2'), 'left')->on('orders.id', '=', 'totals_2.id')
                ->join(array($totalsq3, 'totals_3'), 'left')->on('orders.id', '=', 'totals_3.order_id')
            ->where('orders.deleted', '=', 0);

        if (isset($params['owner_id'])) {
            $q->and_where('accounts.owner_id', '=', $params['owner_id']);
        }

        if (isset($params['status'])) {
            $q->and_where('orders.status', '=', $params['status']);
        }

        if (isset($params['event_id'])) {
            $q->and_where('ticket_types.event_id', '=', $params['event_id']);
        }

        if (isset($params['archived']) && $params['archived'] == true) {
            $q->and_where('orders.archived', 'is not ', null);
        } else {
            $q->and_where('orders.archived', 'is ', null);
        }

        /* Filters for fetching datatable results */
        if (isset($params['filters'])) {
            $filters = $params['filters'];

            $columns = array(
                array('searchable' => true,  'sql' => 'orders.id'),
                array('searchable' => true,  'sql' => 'buyer'),
                array('searchable' => true,  'sql' => 'ubuyer'),
                array('searchable' => true,  'sql' => 'orders.email'),
                array('searchable' => true,  'sql' => DB::expr("GROUP_CONCAT(CONCAT(ticket_types.type, ': ', events.name, ' / ', ticket_types.name))")),
                array('searchable' => true,  'sql' => 'orders.status'),
                array('searchable' => false, 'sql' => null),
                array('searchable' => true,  'sql' => 'orders.created'),
                array('searchable' => true,  'sql' => 'orders.total'),
                array('serachable' => false, 'sql' => null)
            );

            // Keyword search
            if (!empty($filters['sSearch'])) {
                $q->and_having_open();
                foreach($columns as $column) {
                    if ($column['searchable']) {
                        $q->or_having($column['sql'], 'like', '%'.$filters['sSearch'].'%');
                    }
                }
                $q->and_having_close();
            }

            // Limit and offset. Only show the number of records for this paginated page.
            if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
                $q->limit(intval($filters['iDisplayLength']));
                if (isset($filters['iDisplayStart'])) {
                    $q->offset(intval($filters['iDisplayStart']));
                }
            }

            // Order by specific column
            if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
                for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                    if ($columns[$filters['iSortCol_'.$i]]['sql'] != null) {
                        $q->order_by($columns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
                    }
                }
            }
        }

        $q->group_by('orders.id');
        $q->order_by('orders.updated', 'desc');
        $orders = $q->execute()->as_array();
        $total_found = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total');

        if (@$params['display_tickets']) {
            foreach ($orders as $i => $order) {
                $orders[$i]['tickets'] = DB::select(
                    'tickets.id',
                    'tickets.code',
                    'tickets.checked',
                    'tickets.checked_by',
                    'tickets.checked_note',
                    'idates.date_id',
                    'dates.starts',
                    'dates.ends'
                )
                    ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                    ->join(array(self::TABLE_DATES, 'dates'), 'inner')->on('idates.date_id', '=', 'dates.id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                    ->join(array(self::TABLE_TICKETS, 'tickets'), 'inner')->on('tickets.order_item_has_date_id', '=', 'idates.id')
                    ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ttypes.event_id', '=', 'events.id')
                    ->where('orders.status', '=', 'PAID')
                    ->and_where('orders.deleted', '=', 0)
                    ->and_where('orders.id', '=', $order['id'])
                    ->execute()
                    ->as_array();
            }
        }

        if (!empty($params['datatable'])) {
            // If this is for a datatable, we need information other than a list of orders
            return array(
                'orders' => $orders,
                'total_found' => $total_found,
                'total_displayed' => count($orders)
            );
        }
        else {
            return $orders;
        }
    }

    public static function paymentsList($params = array())
    {
        self::paymentsCancelIfProcessing();
        $q = DB::select(
            'orders.currency',
            'payments.*',
            DB::expr("CONCAT_WS(' ', orders.firstname, orders.lastname) AS buyer"),
            'orders.id'
        )
            ->from(array(self::TABLE_ORDERS, 'orders'))
            ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')->on('orders.id', '=', 'payments.order_id')
            ->join(array(self::TABLE_ACCOUNTS, 'accounts'), 'inner')->on('orders.account_id', '=', 'accounts.id')
            ->join(array(Model_Users::MAIN_TABLE, 'buyers'), 'inner')->on('orders.buyer_id', '=', 'buyers.id')
            ->where('orders.deleted', '=', 0);

        if (isset($params['owner_id'])) {
            $q->and_where('accounts.owner_id', '=', $params['owner_id']);
        }

        $q->order_by('payments.updated', 'desc');

        $payments = $q->execute()->as_array();
        return $payments;
    }

    public static function ticketsGenerate($order)
    {
        if (is_numeric($order)) {
            $order = self::orderLoad($order);
        }

        $generated = array();

//        echo json_encode($order);die();

        if ($order) {
            foreach ($order['idates'] as $idate) {
                $qty = $idate['sleeping'] ?: $idate['quantity'];
                for ($i = 0 ; $i < $qty ; ++$i) {
                    $ticket = array();
                    $ticket['order_item_has_date_id'] = $idate['id'];
                    //$ticket['code'] = DB::select(DB::expr('UUID() as code'))->execute()->get('code');
                    $ticket['code'] = uniqid();
                    $inserted = DB::insert(self::TABLE_TICKETS)->values($ticket)->execute();
                    $generated[] = $inserted[0];
                }
            }
        }
        return $generated;
    }

    public static function checkStalePayments($params = array())
    {
        $select = DB::select('payments.*')
            ->from(array(self::TABLE_PAYMENTS, 'payments'))
                ->join(array(self::TABLE_ORDERS, 'orders'), 'inner')
                    ->on('payments.order_id', '=', 'orders.id')
            ->where('payments.status', '=', 'PROCESSING')
            ->and_where('payments.paymentgw_info', 'like', 'pi_%');

        if (@$params['buyer_id']) {
            $select->and_where('orders.buyer_id', '=', $params['buyer_id']);
        }

        $payments = $select->execute()->as_array();
        $inform_payments = array();
        foreach ($payments as $payment) {
            $intent = self::checkStripePaymentIntent($payment['paymentgw_info']);
            if ($intent->status == 'succeeded') {
                $inform_payments[] = array(
                    'order_id' => $payment['order_id'],
                    'payment_id' => $payment['id'],
                    'payment_intent' => $payment['paymentgw_info']
                );
            }
        }
        return $inform_payments;
    }

    public static function paymentsCancelIfProcessing($timeout = 300)
    {
        self::checkStalePayments();
        DB::query(
            null,
            "UPDATE " . self::TABLE_PAYMENTS . " p INNER JOIN " . self::TABLE_ORDERS . " o ON p.order_id = o.id " .
                "SET p.status = 'CANCELLED', p.status_reason = 'Timeout', o.status = 'CANCELLED', o.status_reason = 'Payment Timeout' " .
                "WHERE p.updated < DATE_SUB(NOW(), INTERVAL " . Database::instance()->quote($timeout) . " SECOND) AND p.deleted = 0 AND p.status = 'PROCESSING' and p.paymentgw <> 'Payment Plan'"
        )->execute();

        self::eventsSoldUpdate();
    }

    public static function getTicketUrl($ticket)
    {
        $url = URL::site('/ticket/' . $ticket['order_id'] . '-' . $ticket['order_item_has_date_id'] . '-' . $ticket['code']);
        return $url;
    }

    public static function qrcodeGenerate($ticket, $size = 3)
    {
        if (is_numeric($ticket)) {
            $ticket = DB::select('tickets.*', 'items.ticket_type_id', 'items.order_id')
                ->from(array(self::TABLE_TICKETS, 'tickets'))
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('tickets.order_item_has_date_id', '=', 'idates.id')
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('idates.order_item_id', '=', 'items.id')
                    ->join(array(self::TABLE_ORDERS, 'orders'), 'inner')->on('items.order_id', '=', 'orders.id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
                ->where('tickets.id', '=', $ticket)
                ->execute()
                ->current();
        }

        $urlToCode = self::getTicketUrl($ticket);

        require_once APPPATH . 'vendor/tcpdf/tcpdf.php';
        require_once APPPATH . 'vendor/tcpdf/tcpdf_barcodes_2d.php';

        $barcodeobj = new TCPDF2DBarcode($urlToCode, 'QRCODE,H');
        $qrcodeImage = $barcodeobj->getBarcodePNGData($size, $size, array(0,0,0));

        return $qrcodeImage;
    }

    public static function ticketLoadFromUrlParam($url)
    {
        preg_match('/^(\d+)-(\d+)-(.*?)$/', $url, $params);

        $q = DB::select(
            'tickets.*',
            'items.ticket_type_id',
            'items.order_id',
            'orders.buyer_id',
            array('accounts.owner_id', 'seller_id'),
            array('events.name', 'event'),
            "dates.starts",
            'ticket_types.type',
            array('ticket_types.name', 'ticket'),
            DB::expr("CONCAT_WS('', orders.firstname, ' ', orders.lastname, ' <' , orders.email, '>') AS buyer"),
            DB::expr("CONCAT_WS('', orders.firstname, ' ', orders.lastname) AS buyer_name"),
            DB::expr("CONCAT(checkers.name, ' ', checkers.surname, ' <' , checkers.email, '>') AS checker")
        )
            ->from(array(self::TABLE_TICKETS, 'tickets'))
                ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('tickets.order_item_has_date_id', '=', 'idates.id')
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('idates.order_item_id', '=', 'items.id')
                ->join(array(self::TABLE_ORDERS, 'orders'), 'inner')->on('items.order_id', '=', 'orders.id')
                ->join(array(self::TABLE_ACCOUNTS, 'accounts'), 'inner')->on('orders.account_id', '=', 'accounts.id')
                ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
                ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
                ->join(array(Model_Users::MAIN_TABLE, 'buyers'), 'inner')->on('orders.buyer_id', '=', 'buyers.id')
                ->join(array(Model_Users::MAIN_TABLE, 'checkers'), 'left')->on('tickets.checked_by', '=', 'checkers.id')
                ->join(array(self::TABLE_DATES, 'dates'), 'left')->on('events.id', '=', 'dates.event_id')->on('idates.date_id', '=', 'dates.id')
            ->where('orders.id', '=', $params[1])
            ->and_where('idates.id', '=', $params[2])
            ->and_where('tickets.code', '=', $params[3]);

        $q->group_by('tickets.id');

        $ticket = $q
            ->execute()
            ->current();
        return $ticket;
    }

    public static function ticketLoad($id)
    {
        $q = DB::select(
            'tickets.*',
            'items.ticket_type_id',
            'items.order_id',
            'orders.buyer_id',
            array('accounts.owner_id', 'seller_id'),
            array('events.name', 'event'),
            DB::expr("GROUP_CONCAT(dates.starts) as starts"),
            'ticket_types.type',
            array('ticket_types.name', 'ticket'),
            DB::expr("CONCAT_WS('', orders.firstname, ' ', orders.lastname, ' <' , orders.email, '>') AS buyer"),
            DB::expr("CONCAT_WS('', orders.firstname, ' ', orders.lastname) AS buyer_name")
        )
            ->from(array(self::TABLE_TICKETS, 'tickets'))
                ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('tickets.order_item_has_date_id', '=', 'idates.id')
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('idates.order_item_id', '=', 'items.id')
                ->join(array(self::TABLE_ORDERS, 'orders'), 'inner')->on('items.order_id', '=', 'orders.id')
                ->join(array(self::TABLE_ACCOUNTS, 'accounts'), 'inner')->on('orders.account_id', '=', 'accounts.id')
                ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
                ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
                ->join(array(Model_Users::MAIN_TABLE, 'buyers'), 'inner')->on('orders.buyer_id', '=', 'buyers.id')
                ->join(array(self::TABLE_DATES, 'dates'), 'left')->on('events.id', '=', 'dates.event_id')->on('idates.date_id', '=', 'dates.id')
            ->where('tickets.id', '=', $id);

        $q->group_by('tickets.id');

        $ticket = $q
            ->execute()
            ->current();
        return $ticket;
    }

    public static function ticketsList($params)
    {
        $q = DB::select(
            'tickets.*',
            'items.ticket_type_id',
            'items.order_id',
            'orders.buyer_id','orders.created',
			array('orders.total', 'price'),
			'orders.currency',
            array('events.name', 'event'),
            DB::expr("GROUP_CONCAT(dates.starts) as starts"),
            'ticket_types.type',
            array('ticket_types.name', 'ticket'),
            DB::expr("CONCAT_WS('', orders.firstname, ' ', orders.lastname, ' <' , orders.email, '>') AS buyer"),
            DB::expr("CONCAT_WS('', orders.firstname, ' ', orders.lastname) AS buyer_name"),
            'idates.date_id',
            'ticket_types.event_id',
            array('venues.name', 'venue'),
            DB::expr("SUM(amount) as total_paid")
        )
            ->from(array(self::TABLE_TICKETS, 'tickets'))
                ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('tickets.order_item_has_date_id', '=', 'idates.id')
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('idates.order_item_id', '=', 'items.id')
                ->join(array(self::TABLE_ORDERS, 'orders'), 'inner')->on('items.order_id', '=', 'orders.id')
                ->join(array(self::TABLE_ACCOUNTS, 'accounts'), 'inner')->on('orders.account_id', '=', 'accounts.id')
                ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
                ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
                ->join(array(self::TABLE_VENUES, 'venues'), 'left')->on('events.venue_id', '=', 'venues.id')
                ->join(array(Model_Users::MAIN_TABLE, 'buyers'), 'inner')->on('orders.buyer_id', '=', 'buyers.id')
                ->join(array(self::TABLE_DATES, 'dates'), 'left')->on('events.id', '=', 'dates.event_id')->on('idates.date_id', '=', 'dates.id')
                ->join(array(self::TABLE_PAYMENTS, 'payments'))
                    ->on('orders.id', '=', 'payments.order_id')
                    ->on('payments.deleted', '=', DB::expr(0))
                    ->on('payments.paymentgw', '<>' ,DB::expr("'Payment Plan'"))
                    ->on('payments.status', '=', DB::expr("'PAID'"))
            ->where('tickets.deleted', '=', 0)
            ->and_where('orders.status', '=', 'PAID');
        if (isset($params['owner_id'])) {
            $q->and_where('accounts.owner_id', '=', $params['owner_id']);
        }
        if (isset($params['buyer_id'])) {
            $q->and_where('orders.buyer_id', '=', $params['buyer_id']);
        }
        if (isset($params['user_id'])) {
            $q->and_where_open();
            $q->or_where('accounts.owner_id', '=', $params['user_id']);
            $q->or_where('orders.buyer_id', '=', $params['user_id']);
            $q->and_where_close();
        }
        if (isset($params['event_id'])) {
			$q->and_where('ticket_types.event_id', '=', $params['event_id']);
		}

        if (isset($params['past'])) {
            $q->and_where('dates.starts', '<=', $params['past']);
        }

        if (isset($params['upcoming'])) {
            $q->and_where('dates.starts', '>=', $params['upcoming']);
        }

		$q
			->order_by('orders.created', 'desc')
			->group_by('tickets.id');

        $tickets = $q
            ->execute()
            ->as_array();
        return $tickets;
    }

    public static function ticketsListCount($params)
    {
        $q = DB::select(DB::expr('COUNT(*) as mc'))
            ->from(array(self::TABLE_TICKETS, 'tickets'))
            ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('tickets.order_item_has_date_id', '=', 'idates.id')
            ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('idates.order_item_id', '=', 'items.id')
            ->join(array(self::TABLE_ORDERS, 'orders'), 'inner')->on('items.order_id', '=', 'orders.id')
            ->join(array(self::TABLE_ACCOUNTS, 'accounts'), 'inner')->on('orders.account_id', '=', 'accounts.id')
            ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_types'), 'inner')->on('items.ticket_type_id', '=', 'ticket_types.id')
            ->join(array(self::TABLE_EVENTS, 'events'), 'inner')->on('ticket_types.event_id', '=', 'events.id')
            ->join(array(Model_Users::MAIN_TABLE, 'buyers'), 'inner')->on('orders.buyer_id', '=', 'buyers.id')
            ->join(array(self::TABLE_DATES, 'dates'), 'left')->on('events.id', '=', 'dates.event_id')->on('idates.date_id', '=', 'dates.id')
            ->where('tickets.deleted', '=', 0);
        if (isset($params['owner_id'])) {
            $q->and_where('accounts.owner_id', '=', $params['owner_id']);
        }
        if (isset($params['buyer_id'])) {
            $q->and_where('orders.buyer_id', '=', $params['buyer_id']);
        }
        if (isset($params['event_id'])) {
            $q->and_where('ticket_types.event_id', '=', $params['event_id']);
        }

        $tickets = $q
            ->execute()
            ->get('mc', 0);

        return $tickets;
    }

    public static function eventsSoldUpdate($event_id = null)
    {
        DB::delete(self::TABLE_ESOLD)->execute();
        $query = "REPLACE INTO " . self::TABLE_ESOLD . " " .
            "(event_id, event_date_id, sold) " .
            "(" .
                "SELECT ticket_types.event_id, dates.id, SUM(items.quantity) FROM " . self::TABLE_ORDERS . " orders " .
                    " INNER JOIN " . self::TABLE_ORDER_ITEMS . " items ON orders.id = items.order_id ".
                    " INNER JOIN " . self::TABLE_HAS_TICKET_TYPES . " ticket_types on items.ticket_type_id = ticket_types.id " .
                    " INNER JOIN " . self::TABLE_ORDER_ITEM_DATES . " idates ON items.id = idates.order_item_id " .
                    " INNER JOIN " . self::TABLE_DATES . " dates ON idates.date_id = dates.id " .
                    " WHERE orders.status IN ('PROCESSING', 'PAID') " .
                        (is_numeric($event_id) ? " AND ticket_types.event_id=" . $event_id : "") .
                " GROUP BY ticket_types.event_id, dates.id" .
            ")";
        DB::query(null, $query)->execute();

        DB::delete(self::TABLE_TTSOLD)->execute();
        $query = "REPLACE INTO " . self::TABLE_TTSOLD . " " .
            "(ticket_type_id, event_date_id, sold) " .
            "(" .
                "SELECT ticket_types.id, dates.id, SUM(items.quantity) FROM " . self::TABLE_ORDERS . " orders " .
                    " INNER JOIN " . self::TABLE_ORDER_ITEMS . " items ON orders.id = items.order_id " .
                    " INNER JOIN " . self::TABLE_HAS_TICKET_TYPES . " ticket_types on items.ticket_type_id = ticket_types.id " .
                    " INNER JOIN " . self::TABLE_ORDER_ITEM_DATES . " idates ON items.id = idates.order_item_id " .
                    " INNER JOIN " . self::TABLE_DATES . " dates ON idates.date_id = dates.id " .
                    " WHERE orders.status IN ('PROCESSING', 'PAID') " .
                        (is_numeric($event_id) ? " AND ticket_types.event_id=" . $event_id : "") .
                " GROUP BY ticket_types.id, dates.id" .
            ")";
        DB::query(null, $query)->execute();
    }


    public static function organizerSave($userId, $contactId, $firstname, $lastname, $email, $phone, $mobile, $url, $facebook, $twitter, $linkedin)
    {
        $contact = null;
        if (!is_numeric($contactId)) {
            $contact = new Model_Contacts();
        } else {
            $contact = new Model_Contacts($contactId);
        }
        $contact->set_first_name($firstname);
        $contact->set_last_name($lastname);
        $contact->set_email($email);
        $contact->set_phone($phone);
        $contact->set_mobile($mobile);
        $contact->set_publish(1);
        $contact->set_mailing_list('Event Organizer');
        $contact->set_permissions(array($userId));
        $contact->save();
        $contactDetails = $contact->get_details();
        $contactId = $contactDetails['id'];

        $organizer = array();
        $organizer['contact_id'] = $contactId;
        $organizer['url'] = $url;
        $organizer['facebook'] = $facebook;
        $organizer['twitter'] = $twitter;
        $organizer['linkedin'] = $linkedin;
        DB::delete(self::TABLE_ORGANIZERS)->where('contact_id', '=', $contactId)->execute();
        DB::insert(self::TABLE_ORGANIZERS)->values($organizer)->execute();

        return $contactId;
    }

    public static function organizerLoad($contactId)
    {
        $contact = new Model_Contacts($contactId);
        $contactDetails = $contact->get_details();
        if ($contactDetails) {
            $organizer = DB::select('*')
                ->from(self::TABLE_ORGANIZERS)
                ->where('contact_id', '=', $contactId)
                ->execute()
                ->current();
            return array_merge($contactDetails, $organizer);
        } else {
            return false;
        }
    }

    public static function organizerUrlGet($url, $forContactId = null)
    {
        $url = preg_replace('/[^a-z0-9]+/i', '-', strtolower($url));

        $exists = true;
        for ($i = 0 ; $i < 10 && $exists ; ++$i) {
            $q = DB::select(DB::expr('count(*) as `exists`'))
                ->from(self::TABLE_ORGANIZERS)
                ->where('url', '=', $url . ($i > 0 ? '-' . $i : ''));
            if ($forContactId) {
                $q->and_where('contact_id', '<>', $forContactId);
            }
            $exists = (int)$q->execute()->get('exists');
        }

        $suggestion = '';
        if ($exists) {
            $suggestion .= '-' . md5(microtime(true));
        } else {
            if ($i > 1) {
                $suggestion .= '-' . $i;
            }
        }

        return array('exists' => $exists, 'suggestion' => $suggestion);
    }


    /*
     * this is not used anymore. kept as a sample
     */
    public static function ticketPDFGenerate_fpdi($order, $ticketIndex = null)
    {
        //require Kohana::find_file('vendor', 'tcpdf/tcpdf');
        require Kohana::find_file('vendor', 'fpdf/fpdf');
        require Kohana::find_file('vendor', 'fpdi/fpdi');

        $pdf = new FPDI();

        $deleteOnComplete = array();
        $tmp = tempnam('/tmp', 'ticket-template');
        $templateid = Model_files::get_file_id('event-ticket', Model_Files::get_directory_id('/templates'));
        Model_Files::get_file($templateid, '/', $tmp);
        $pageCount = $pdf->setSourceFile($tmp);
        $tplIdx = $pdf->importPage(1, '/MediaBox');

        foreach ($order['tickets'] as $ticket) {
            $orderItem = null;
            $ticketIDate = null;
            $ticketDate = null;
            foreach ($order['idates'] as $idate) {
                if ($idate['id'] == $ticket['order_item_has_date_id']) {
                    $ticketIDate = $idate;
                }
            }
            foreach ($order['items'] as $item) {
                if ($item['id'] == $ticketIDate['order_item_id']) {
                    $orderItem = $item;
                }
            }

            { // pdf page generationg block
                $pdf->addPage("A6");

				$pdf->AddFont('Roboto-Black','','Roboto-Black.php');
				$pdf->AddFont('Roboto-Bold','','Roboto-Bold.php');
				$pdf->AddFont('Roboto-Light','','Roboto-Light.php');
				$pdf->AddFont('Roboto-Regular','','Roboto-Regular.php');


				$qrcodeImage = self::qrcodeGenerate($ticket);
                $qrcodeFile = '/tmp/' . $ticket['id'] . '.png';
                $deleteOnComplete[] = $qrcodeFile;
                file_put_contents($qrcodeFile, $qrcodeImage);
                $pdf->useTemplate($tplIdx, null, null, 0, 0, true);
                //print_r($pdf);exit;
                //$pdf->setPageUnit('mm');
                $pdf->Image($qrcodeFile, 20, 61, 116, 116, 'PNG', self::getTicketUrl($ticket), '', true, 150, '',
                    false,
                    false, 1, false, false, false);
                if ($ticket['image_media_id']) {
                    try {
                        $image_path = Model_Media::get_path_to_id($ticket['image_media_id']);
                        $mime_type = DB::select('mime_type')
                            ->from(Model_Media::TABLE_MEDIA)
                            ->where('id', '=', $ticket['image_media_id'])
                            ->execute()
                            ->get('mime_type');
                        $pdf->Image($image_path, 144, 61, 171, 142, $mime_type == 'image/jpeg' ? 'JPG' : 'PNG');
                    } catch (Exception $exc) {

                    }
                }
				$pdf->SetFont('Roboto-Bold', '', 30);
				$pdf->setTextColor(0,167,133);
				$pdf->Text(37, 40, uniqid());

				$pdf->Text(330, 40, str_pad($order['id'], 8, '0', STR_PAD_LEFT));

				$pdf->SetFont('Roboto-Bold', '', 59);
				$pdf->setTextColor(255, 1, 117);
				$pdf->Text(18, 202, 'Event Details');

				$pdf->SetFont('Roboto-Bold', '', 63.3);
				$pdf->setTextColor(1, 1, 1);
				$pdf->SetXY(18, 220);
				$pdf->MultiCell(255, 5, $ticket['event']);

				$pdf->SetFont('Roboto-Light', '', 51.5);
				$pdf->Text(18, 263, $ticket['venue']);

				$pdf->Text(18, 358, date('d/m/Y', strtotime($ticket['starts'])));
				$pdf->Text(18, 381, 'Starts: '.date('G:ia', strtotime($ticket['starts'])));
				$pdf->Text(18, 406, 'Ends: '.date('G:ia', strtotime($ticket['ends'])));

				$pdf->SetFont('Roboto-Bold', '', 43.5);
				$pdf->setTextColor(255, 1, 117);
				$pdf->Text(208, 320, 'Purchaser');

				$purchaser = explode('<', $ticket['buyer']);
				$purchaser = isset($purchaser[0]) ? $purchaser[0] : '';
				$currency = ($ticket['currency'] == 'EUR') ? chr(128) : $ticket['currency'];
				$price    = $orderItem['price'] == 0 ? 'Free' : $currency.$orderItem['total'];

				$pdf->SetFont('Roboto-Light', '', 43.5);
				$pdf->setTextColor(1, 1, 1);
				$pdf->Text(208, 341, $purchaser);
				$pdf->Text(208, 360, 'Price: '.$price);

				$pdf->SetFont('Roboto-Bold', '', 43.5);
				$pdf->setTextColor(255, 1, 117);
				$pdf->Text(208, 406, 'Organiser');

				$pdf->SetFont('Roboto-Black', '', 43.5);
				$pdf->setTextColor(255, 1, 117);
				$pdf->Text(208, 425, $ticket['organiser_name']);

				$pdf->SetFont('Roboto-Black', '', 35.6);
				$pdf->setTextColor(49, 205, 179);
				$pdf->Text(208, 444, $ticket['organiser_email']);

				$note = trim(strip_tags($ticket['ticket_note']));

				if ($note)
				{
					$pdf->SetFont('Roboto-Regular', '', 35.6);
					$pdf->setTextColor(1, 1, 1);


					$pdf->Text(23, 480, 'Note');

					$pdf->SetXY(22, 490);
					$pdf->Cell(0, 0, $note, 0, 0, 'C');
				}

                //$pdf->Write(4, 'xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx');
                //$pdf->MultiCell(90, 4, 'xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx', 1);
			}
        }


        $finalpdffile = '/tmp/ticket-' . $order['id'] . '-' . date('YmdHis') . '.pdf';
        $pdf->Output("F", $finalpdffile);
        foreach ($deleteOnComplete as $delete) {
            @unlink($delete);
        }
        return array($finalpdffile);
    }

    public static function ticketPDFGenerate($order, $ticketId = null)
    {
        $template_location = Settings::instance()->get("doc_template_path");
        $temporary_folder = Settings::instance()->get("doc_temporary_path");
        $doc_config = Kohana::$config->load('config')->get('doc_config');
        $script_location = $template_location.$doc_config['script'];
        $currencies = Model_Currency::getCurrencies(true);

        $generatedDocs = array();
        foreach ($order['tickets'] as $ticket) {
            if ($ticketId != null && $ticket['id'] != $ticketId) {
                continue;
            }

            $orderItem = null;
            $ticketIDate = null;
            $ticketDate = null;
            foreach ($order['idates'] as $idate) {
                if ($idate['id'] == $ticket['order_item_has_date_id']) {
                    $ticketIDate = $idate;
                }
            }
            foreach ($order['items'] as $item) {
                if ($item['id'] == $ticketIDate['order_item_id']) {
                    $orderItem = $item;
                }
            }
            $orderItem['other_times'] = @json_decode($orderItem['other_times']);

            $templateParams = array();
            $templateParams['ORDERNO'] = str_pad($order['id'], 8, '0', STR_PAD_LEFT);
            $templateParams['TICKETNO'] = $ticket['code'];
            $templateParams['AGERESTRICTION'] =  $orderItem['age_restriction'] ? 'Age over ' . $orderItem['age_restriction'] . "'s" : 'All ages';
            $templateParams['EVENTNAME'] = $ticket['event'];
            $templateParams['CURRENCY'] = $order['currency'];
            $templateParams['CURSYM'] = $currencies[$order['currency']]['symbol'];


            $templateParams['TICKET_TYPE'] = $ticket['ticket'];
            $templateParams['TICKET_DESCRIPTION'] = $orderItem['ticket_description'];
            $templateParams['PURCHASER'] = $order['firstname'] . ' ' . $order['lastname'];
            $templateParams['PRICE'] = $templateParams['CURSYM'] . $orderItem['total'];

            $templateParams['STARTDATE_ENDDATE'] = date('d/m/Y', strtotime($ticket['starts']));
            if (strtotime($ticket['ends']) > 0) {
                $templateParams['STARTDATE_ENDDATE'] .= ' - ' . date('d/m/Y', strtotime($ticket['ends']));
            }
            $templateParams['STARTTIME_ENDTIME'] = date('H:i', strtotime($ticket['starts']));
            if (strtotime($ticket['ends']) > 0) {
                $templateParams['STARTTIME_ENDTIME'] .= ' - ' . date('H:i', strtotime($ticket['ends']));
            }
            $templateParams['OTHERTIMETITLE1'] = '' .@$orderItem['other_times']->title[0];
            $templateParams['OTHERTIME1'] = '' . @$orderItem['other_times']->time[0];
            $templateParams['OTHERTIME2TITLE'] = '' . @$orderItem['other_times']->title[1];
            $templateParams['OTHERTIME2'] = '' . @$orderItem['other_times']->time[1];

            $templateParams['VENUE'] = $ticket['venue'];
            $templateParams['ADDRESS'] = array('type' => 'multiline', 'lines' => preg_split('/\n\s*/s', $orderItem['address']));

            $templateParams['ORGANISERNAME'] = $ticket['organiser_name'];
            $templateParams['ORGANISEREMAIL'] = $ticket['organiser_email'];
            $templateParams['PHONE'] = $ticket['organiser_phone'];
            $templateParams['WEBSITE'] = $ticket['organiser_website'];
            $templateParams['NOTEPREFIX'] = 'Note:';
            $templateParams['NOTE'] = strip_tags($ticket['ticket_note']);

            $tmpfilename = uniqid() . '-tmp.docx';
            $filename = uniqid() . '-processed';
            $fileId = Model_Files::get_file_id('event-ticket', Model_Files::get_directory_id('/templates'));
            Model_Files::get_file($fileId, $template_location, $tmpfilename);
            $template_file = $tmpfilename;
            $qrcodeImage = self::qrcodeGenerate($ticket);
            $qrcodeFile = Kohana::$cache_dir . '/' . $ticket['code'] . '.png';
            file_put_contents($qrcodeFile, $qrcodeImage);
            $templateParams['QRCODE'] = array('type' => 'image', 'file' => $qrcodeFile);

            $templateParams['EVENTIMAGE'] = array('type' => 'image', 'file' => Model_Media::get_localpath_to_id($ticket['image_media_id']));

            $generator = New Docgenerator($template_location, $script_location, $temporary_folder);
            $generator->add_template($template_file);
            $generator->initalise_document_template($templateParams);
            $feedback = $generator->create($filename);
            if ($feedback) {
                $generatedDocs[] = array(
                    'file' => $template_location . $filename . '.docx',
                    'code' => $ticket['code'],
                    'date' => $ticket['starts']
                );
            }
        }

        $generatedPdfs = array();
        $doc = new Model_Document();
        foreach ($generatedDocs as $docx) {
            $doc->doc_convert_to_pdf($docx['file'], $docx['file'] . '.pdf');
            unlink($docx['file']);
            $docx['file'] = $docx['file'] . '.pdf';
            $generatedPdfs[] = $docx;
        }
        return $generatedPdfs;
    }

    public static function receiptGenerate($order)
    {
		if ($order['currency'] == 'EUR') $order['currency'] = '€';
		if ($order['currency'] == 'GBP') $order['currency'] = '£';

        $template_location = Settings::instance()->get("doc_template_path");
        $temporary_folder = Settings::instance()->get("doc_temporary_path");
        $doc_config = Kohana::$config->load('config')->get('doc_config');
        $script_location = $template_location.$doc_config['script'];

        $templateParams = array();
        $templateParams['ORDERNO'] = str_pad($order['id'], 8, '0', STR_PAD_LEFT);
        $templateParams['ORDERDATE'] = date('d/m/Y', strtotime($order['created']));
        $templateParams['SUBTOTAL'] = $order['currency'] . number_format($order['total'] - $order['vat_total'], 2);
        $templateParams['VAT'] = $order['currency']  . number_format($order['vat_total'], 2);
        $templateParams['TOTAL'] = $order['currency'] . number_format($order['total'], 2);

        $templateParams['PAYMENTGW'] = $order['payments'][0]['paymentgw'];
        $templateParams['PAYMENTGWINFO'] = $order['payments'][0]['paymentgw_info'];

        foreach ($order['tickets'] as $ticket) {
            $templateParams['ORGANISERNAME'] = $ticket['organiser_name'];
            $templateParams['ORGANISEREMAIL'] = $ticket['organiser_email'];
            break;
        }

        $templateParams['TICKETS'] = array();
        foreach ($order['items'] as $item) {
			$address = explode("\n", $item['address']);

			$start_dates = explode(',', $item['dates']);
			$end_dates = explode(',', $item['dates_end']);

			$dates = '';
			foreach ($start_dates as $i => $start_date)
			{
				$dates .= $start_dates[$i].( ! empty($end_dates[$i]) ? ' - '.$end_dates[$i] : '').",";
			}
			$dates = trim($dates, ',');

            $templateParams['EVENTNAME'] = $item['event'];
            $templateParams['VENUENAME'] = $item['venue'];
            $templateParams['ADDRESS'] = array('type' => 'multiline', 'lines' => array_filter($address));
            $templateParams['TICKETS'][] = array(
                'TICKET_TYPE' => $item['name'],
                'TICKET_DATE' => $item['dates'],
                'STARTDATE' => $item['dates'],
                'ENDDATE' => $item['dates_end'],
				'DATES' => $dates,
                'TICKET_QTY' => $item['quantity'],
                'TICKET_PRICE' => $order['currency'] . number_format($item['price'] + $item['donation'], 2),
                'TICKET_FEE' => $order['currency'] . number_format($item['commission'], 2),
                'TICKET_VAT' => $order['currency'] . number_format($item['vat'], 2),
                'TICKET_TOTAL' => $order['currency'].number_format($item['total'] * $item['quantity'], 2)
            );
        }

        $tmpfilename = uniqid() . '-tmp.docx';
        $filename = uniqid() . '-processed';
        $fileId = Model_Files::get_file_id('event-receipt', Model_Files::get_directory_id('/templates'));
        Model_Files::get_file($fileId, $template_location, $tmpfilename);
        $template_file = $tmpfilename;

        $generator = New Docgenerator($template_location, $script_location, $temporary_folder);
        $generator->add_template($template_file);
        $generator->initalise_document_template($templateParams);
        $feedback = $generator->create($filename);
        if ($feedback) {
            $generatedDoc = $template_location . $filename . '.docx';
        }

        //print_r($generatedDocs);exit;
        //return $generatedDocs;
        $doc = new Model_Document();
        $doc->doc_convert_to_pdf($generatedDoc, $generatedDoc . '.pdf');
        unlink($generatedDoc);
        $generatedPdf = $generatedDoc . '.pdf';
        return $generatedPdf;
    }

    public static function calculateSoldQuery($eventId = null, $ticketTypeId = null, $dateId = null)
    {
        $q = DB::select(DB::expr('sum(items.quantity) as sold'))
            ->from(array(self::TABLE_ORDERS, 'orders'))
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id');

        return $q;
    }

    public static function invoices($params)
    {
        $eventFilter = DB::select(DB::expr('DISTINCT ttypes.event_id'))
            ->from(array(self::TABLE_ORDERS, 'orders'))
                ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')->on('orders.id', '=', 'payments.order_id')
                ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
            ->where('payments.paymentgw', 'not in', array('Free'));

        if (isset($params['owned_by'])) {
            $eventFilter->and_where('events.owned_by', '=', $params['owned_by']);
        }

        $q = DB::select(
            'invoices.*',
            array('events.name', 'event'),
            array('events.id', 'event_id')
        )
            ->from(array(self::TABLE_EVENTS, 'events'))
                ->join(array(self::TABLE_SINVOICES, 'invoices'), 'inner')
                    ->on('events.id', '=', 'invoices.event_id')
            ->where('events.status', '=', 'Sale Ended')
            ->and_where('events.deleted', '=', 0)
            ->and_where('invoices.deleted', '=', 0)
            ->and_where('events.id', 'in', $eventFilter);


        if (isset($params['owned_by'])) {
            $q->and_where('events.owned_by', '=', $params['owned_by']);
        }

        $invoices = $q->execute()->as_array();
        return $invoices;

    }
    public static function invoiceGenerate($event)
    {
        try {
            Database::instance()->begin();
            self::eventsSoldUpdate();
            if (is_numeric($event)) {
                $event = self::eventLoad($event);
            }

            if ($event['one_ticket_for_all_dates'] == 1) {
                $totals = DB::select(DB::expr('SUM(items.quantity) AS quantity'), DB::expr('SUM(items.total * items.quantity) AS amount'), DB::expr('SUM(items.price * items.quantity) AS net_amount'))
                    ->from(array(self::TABLE_ORDERS, 'orders'))
                        ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                        ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                    ->where('ttypes.event_id', '=', $event['id'])
                    ->and_where('orders.status', '=', 'PAID')
                    ->and_where('orders.deleted', '=', 0)
                    ->execute()
                    ->current();
            } else {
                $totals = DB::select(DB::expr('SUM(items.quantity) AS quantity'), DB::expr('SUM(items.total * items.quantity) AS amount'), DB::expr('SUM(items.price * items.quantity) AS net_amount'))
                    ->from(array(self::TABLE_ORDERS, 'orders'))
                        ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                        ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
						->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                    ->where('ttypes.event_id', '=', $event['id'])
                    ->and_where('orders.status', '=', 'PAID')
                    ->and_where('orders.deleted', '=', 0)
                    ->execute()
                    ->current();
            }


            $invoice = array();
            $invoice['amount'] = $totals['amount'];
            $invoice['net_amount'] = $totals['net_amount'];
            $currencies = Model_Currency::getCurrencies(true);
            $invoice['currency'] = $event['currency'];
            $invoice['event_id'] = $event['id'];
            $invoice['created'] = date('Y-m-d H:i:s');
            $exists = DB::select('*')->from(self::TABLE_SINVOICES)->where('event_id', '=', $event['id'])->execute()->current();
            if ($exists) {
                $invoice = $exists;
            } else {
                $inserted = DB::insert(self::TABLE_SINVOICES)->values($invoice)->execute();
                $invoice['id'] = $inserted[0];
            }



            $docParams = array();
            $docParams['EVENTNAME'] = $event['name'];
            //$docParams['DATE'] = $event['dates'][0]['starts'];
            $docParams['VENUENAME'] = $event['venue']['name'];
			$address = array($event['venue']['address_1'], $event['venue']['address_2'], $event['venue']['address_3'], $event['venue']['city']);
            $docParams['ADDRESS'] = array('type' => 'multiline', 'lines' => array_filter($address));
            //$docParams['PRICE'] = $ticketType['price'];
            //$docParams['TICKETS'] = array('type' => 'multiline', 'lines' => array());
            $docParams['TICKETS'] = array();
            $total = 0;

            foreach ($event['ticket_types'] as $ticketType) {
                //$docParams['TICKETS']['lines'][] = 'Ticket: ' . $ticketType['name'] . '(' . $ticketType['type'] . ') : ' . ' EUR' . $ticketType['price'] . ' x' . ($ticketType['sold'] ? $ticketType['sold'] : 0);
                $ticketType['sold'] = 0;
                foreach ($event['ticket_types_sold'] as $ticket_types_sold) {
                    if ($ticket_types_sold['ticket_type_id'] == $ticketType['id']) {
                        $ticketType['sold'] += $ticket_types_sold['sold'];
                    }
                }
                if ($event['one_ticket_for_all_dates'] == 1) {
                    $tttotals = DB::select(DB::expr('SUM(items.quantity) AS quantity'), DB::expr('SUM(items.total * items.quantity) AS amount'), DB::expr('SUM(items.price * items.quantity) AS net_amount'), 'items.price')
                        ->from(array(self::TABLE_ORDERS, 'orders'))
                        ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                        ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                        ->where('ttypes.event_id', '=', $event['id'])
                        ->and_where('orders.status', '=', 'PAID')
                        ->and_where('orders.deleted', '=', 0)
                        ->and_where('items.ticket_type_id', '=', $ticketType['id'])
                        ->execute()
                        ->current();
                } else {
                    $tttotals = DB::select(DB::expr('SUM(items.quantity) AS quantity'), DB::expr('SUM(items.total * items.quantity) AS amount'), DB::expr('SUM(items.price * items.quantity) AS net_amount'), 'items.price')
                        ->from(array(self::TABLE_ORDERS, 'orders'))
                        ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                        ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                        ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                        ->where('ttypes.event_id', '=', $event['id'])
                        ->and_where('orders.status', '=', 'PAID')
                        ->and_where('orders.deleted', '=', 0)
                        ->and_where('items.ticket_type_id', '=', $ticketType['id'])
                        ->execute()
                        ->current();
                }
                $docParams['TICKETS'][] = array(
                    'TICKET_TYPE' => $ticketType['name'],
                    'TICKET_PRICE' => $currencies[$event['currency']]['symbol'] . $tttotals['price'],
                    'TICKET_SOLD' => $ticketType['sold'],
                    'TICKET_TOTAL' => $currencies[$event['currency']]['symbol'] . $tttotals['net_amount']
                );
            }
            $total = $totals['net_amount'];

            $docParams['SOLD'] = $totals['quantity'];
            $docParams['REVENUE'] = $currencies[$event['currency']]['symbol'] . $totals['amount'];
            $docParams['TOTAL'] = $currencies[$event['currency']]['symbol'] . $total;
            $docParams['INVOICEDATE'] = date('Y-m-d', strtotime($invoice['created']));
            $docParams['INVOICENO'] = str_pad($invoice['id'], 8, '0', STR_PAD_LEFT);
            $docParams['ORGANISERNAME'] = $event['organizers'][0]['first_name'] . ' ' . $event['organizers'][0]['last_name'];
			$address = array($event['organizers'][0]['address1'], $event['organizers'][0]['address2']);
            $docParams['ORGANISERADDRESS'] = array('type' => 'multiline', 'lines' => array_filter($address));
            $docParams['ORGANISEREMAIL'] = $event['organizers'][0]['email'];

            foreach ($event['dates'] as $edate) {
                if (!isset($docParams['STARTDATE'])) {
                    $docParams['STARTDATE'] = date('d M Y', strtotime($edate['starts']));
                }
                if ($edate['ends']) {
                    $docParams['ENDDATE'] = date('d M Y', strtotime($edate['ends']));
                } else {
                    $docParams['ENDDATE'] = date('d M Y', strtotime($edate['starts']));
                }
            }

            $template_location = Settings::instance()->get("doc_template_path");
            $temporary_folder = Settings::instance()->get("doc_temporary_path");
            $doc_config = Kohana::$config->load('config')->get('doc_config');
            $script_location = $template_location . $doc_config['script'];

            $tmpfilename = uniqid() . '-tmp.docx';
            $filename = uniqid() . '-processed';
            $fileId = Model_Files::get_file_id('event-invoice', Model_Files::get_directory_id('/templates'));
            Model_Files::get_file($fileId, $template_location, $tmpfilename);
            $template_file = $tmpfilename;

            $generator = New Docgenerator($template_location, $script_location, $temporary_folder);
            $generator->add_template($template_file);
            $generator->initalise_document_template($docParams);
            $feedback = $generator->create($filename);
            $generatedDoc = false;
            if ($feedback) {
                $generatedDoc = $template_location . $filename . '.docx';
            }

            $doc = new Model_Document();
            if ($generatedDoc) {
                $generatedPdf = $generatedDoc . '.pdf';
                $doc->doc_convert_to_pdf($generatedDoc, $generatedPdf);
                unlink($generatedDoc);

                $iDirId = Model_Files::get_directory_id('/invoices');
                if (!$iDirId) {
                    Model_Files::create_directory(1, 'invoices');
                    $iDirId = Model_Files::get_directory_id('/invoices');
                }
                $fileId = Model_Files::create_file(
                    $iDirId,
                    'invoice-' . $invoice['id'] . '.pdf',
                    array(
                        'tmp_name' => $generatedPdf,
                        'name' => 'invoice-' . $invoice['id'] . '.pdf',
                        'size' => filesize($generatedPdf),
                        'type' => 'application/pdf'
                    )
                );
                if ($fileId) {
                    if (Auth::instance()->has_access('events_orders_view')) {
                        DB::update(self::TABLE_SINVOICES)->set(array('uticket_file_id' => $fileId))->where('id', '=', $invoice['id'])->execute();
                        $invoice['uticket_file_id'] = $fileId;
                    } else {
                        DB::update(self::TABLE_SINVOICES)->set(array('file_id' => $fileId))->where('id', '=', $invoice['id'])->execute();
                        $invoice['file_id'] = $fileId;
                    }
                }
                @unlink($generatedPdf);
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }

        return $invoice;
    }

    public static function statementGenerate($event)
    {
        self::eventsSoldUpdate();
        if (is_numeric($event)) {
            $event = self::eventLoad($event);
        }

        $docParams = array();
		$currency = '';
        $currencies = Model_Currency::getCurrencies(true);

        $docParams['TICKETS'] = array();
        if ($event['one_ticket_for_all_dates'] == 1) {
            $totals = DB::select(DB::expr('SUM(items.quantity) AS quantity'), DB::expr('SUM(items.price * items.quantity) AS amount'))
                ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                ->where('ttypes.event_id', '=', $event['id'])
                ->and_where('orders.status', '=', 'PAID')
                ->and_where('orders.deleted', '=', 0)
                ->execute()
                ->current();

            $ticketssold = DB::select(
                'items.ticket_type_id',
                'ttypes.name',
                'ttypes.price',
				'orders.currency',
                DB::expr('SUM(items.quantity) AS quantity'),
                DB::expr('SUM(items.price * items.quantity) AS amount')
            )
                ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                ->where('ttypes.event_id', '=', $event['id'])
                ->and_where('orders.status', '=', 'PAID')
                ->and_where('orders.deleted', '=', 0)
                ->group_by('ttypes.id')
                ->execute()
                ->as_array();

            foreach ($ticketssold as $ticketsold) {
                $dates = DB::select(
                    DB::expr("GROUP_CONCAT(date_format(dates.starts,'%d/%m/%Y %H:%s') SEPARATOR ',') AS start"),
                    DB::expr("GROUP_CONCAT(date_format(dates.ends,'%d/%m/%Y %H:%s') SEPARATOR ',') AS end")
                )
                        ->from(array(self::TABLE_ORDERS, 'orders'))
                        ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                        ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                        ->join(array(self::TABLE_DATES, 'dates'), 'left')->on('idates.date_id', '=', 'dates.id')
                        ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                    ->where('ttypes.event_id', '=', $event['id'])
                    ->and_where('ttypes.id', '=', $ticketssold['ticket_type_id'])
                    ->and_where('orders.status', '=', 'PAID')
                    ->and_where('orders.deleted', '=', 0)
                    ->group_by('items.id')
                    ->execute()
                    ->current();

				$currency = $currencies[$event['currency']]['symbol'];

				$docParams['TICKETS'][] = array(
					'TICKET_TYPE'   => $ticketsold['name'],
					'STARTDATE'     => ''.$dates['start'],
					'ENDDATE'       => ''.$dates['end'],
					'TICKET_PRICE'  => $currency . $ticketsold['price'],
					'TICKET_SOLD'   => $ticketsold['quantity'],
					'TICKET__TOTAL' => $currency . $ticketsold['amount']
				);
            }
        } else {
            $totals = DB::select(DB::expr('SUM(items.quantity) AS quantity'), DB::expr('SUM(items.price * items.quantity) AS amount'))
                ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                    ->join(array(self::TABLE_DATES, 'dates'), 'left')->on('idates.date_id', '=', 'dates.id')
				    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                ->where('ttypes.event_id', '=', $event['id'])
                ->and_where('orders.status', '=', 'PAID')
                ->and_where('orders.deleted', '=', 0)
                ->execute()
                ->current();

            $ticketssold = DB::select(
                'ttypes.name',
                'ttypes.price',
				'orders.currency',
                DB::expr('SUM(items.quantity) AS quantity'),
                DB::expr('SUM(items.price * items.quantity) AS amount'),
                DB::expr("date_format(dates.starts,'%d/%m/%Y %H:%s') AS date"),
                DB::expr("date_format(dates.ends,'%d/%m/%Y %H:%s') AS date_end")
            )
                ->from(array(self::TABLE_ORDERS, 'orders'))
                    ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'inner')->on('orders.id', '=', 'items.order_id')
                    ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'inner')->on('items.id', '=', 'idates.order_item_id')
                    ->join(array(self::TABLE_DATES, 'dates'), 'inner')->on('idates.date_id', '=', 'dates.id')
                    ->join(array(self::TABLE_HAS_TICKET_TYPES, 'ttypes'), 'inner')->on('items.ticket_type_id', '=', 'ttypes.id')
                ->where('ttypes.event_id', '=', $event['id'])
                ->and_where('orders.status', '=', 'PAID')
                ->and_where('orders.deleted', '=', 0)
                ->group_by('ttypes.id')
                ->group_by('dates.id')
                ->execute()
                ->as_array();

            foreach ($ticketssold as $ticketsold) {

                $currency = $currencies[$event['currency']]['symbol'];

				$docParams['TICKETS'][] = array(
					'TICKET_TYPE'   => $ticketsold['name'].' '.$ticketssold['date'],
					'TICKET_PRICE'  => $currency . $ticketsold['price'],
					'STARTDATE'     => ''.$ticketsold['date'],
					'ENDDATE'       => ''.$ticketsold['date_end'],
					'TICKET_SOLD'   => $ticketsold['quantity'],
					'TICKET__TOTAL' => $currency . $ticketsold['amount']
				);
            }
        }

		$address = array($event['venue']['address_1'], $event['venue']['address_2'], $event['venue']['address_3'], $event['venue']['city'], $event['venue']['county']);

        $docParams['EVENTNAME'] = $event['name'];
        $docParams['VENUENAME'] = $event['venue']['name'];
        $docParams['ADDRESS'] = array('type' => 'multiline', 'lines' => array_filter($address));
        $docParams['SOLD'] = $totals['quantity'];
        $docParams['REVENUE'] = $currency . $totals['amount'];
        $docParams['TOTAL'] = $docParams['REVENUE'];
        $docParams['STATEMENTDATE'] = date('d/m/y');
        $docParams['STATEMENTNO'] = uniqid();
        $docParams['ORGANISERNAME'] = $event['organizers'][0]['first_name'] . ' ' . $event['organizers'][0]['last_name'];
        $docParams['ORGANISERADDRESS'] = $event['organizers'][0]['address1'] . ' ' . $event['organizers'][0]['address2'];
        $docParams['ORGANISEREMAIL'] = $event['organizers'][0]['email'];

        $template_location = Settings::instance()->get("doc_template_path");
        $temporary_folder = Settings::instance()->get("doc_temporary_path");
        $doc_config = Kohana::$config->load('config')->get('doc_config');
        $script_location = $template_location . $doc_config['script'];

        $tmpfilename = uniqid() . '-tmp.docx';
        $filename = uniqid() . '-processed';
        $fileId = Model_Files::get_file_id('event-statement', Model_Files::get_directory_id('/templates'));
        Model_Files::get_file($fileId, $template_location, $tmpfilename);
        $template_file = $tmpfilename;
        $generator = New Docgenerator($template_location, $script_location, $temporary_folder);
        $generator->add_template($template_file);
        $generator->initalise_document_template($docParams);
        $feedback = $generator->create($filename);
        $generatedDoc = false;
        if ($feedback) {
            $generatedDoc = $template_location . $filename . '.docx';
        }

        $doc = new Model_Document();
        if ($generatedDoc) {
            $generatedPdf = $generatedDoc . '.pdf';
            $doc->doc_convert_to_pdf($generatedDoc, $generatedPdf);
            unlink($generatedDoc);

            $iDirId = Model_Files::get_directory_id('/statements');
            if (!$iDirId) {
                Model_Files::create_directory(1, 'statements');
                $iDirId = Model_Files::get_directory_id('/statements');
            }
            $fileId = Model_Files::create_file(
                $iDirId,
                $docParams['STATEMENTNO'] . '.pdf',
                array(
                    'tmp_name' => $generatedPdf,
                    'name' => $docParams['STATEMENTNO'] . '.pdf',
                    'size' => filesize($generatedPdf),
                    'type' => 'application/pdf'
                )
            );
            @unlink($generatedPdf);
            Model_Files::download_file($fileId);
        }
    }

    public static function invoiceEmail($invoice)
    {
        if (is_numeric($invoice)) {
            $invoice = self::invoiceLoad($invoice);
        }

        if ($invoice && $invoice['file_id']) {
            $email = Settings::instance()->get('events_invoice_email');

            $attachments = array(
                'attachments' => array(
                    array(
                        'file_id' => $invoice['file_id']
                    )
                )
            );
            $msg = new Model_Messaging();
            $msg->send_template(
                'event-invoice',
                $attachments,
                null,
                array(array('target_type' => 'EMAIL', 'target' => $email)),
                $invoice
            );
            return true;
        } else {
            return false;
        }
    }

    public static function invoiceLoad($id)
    {
        $invoice = DB::select('*')->from(self::TABLE_SINVOICES)->where('id', '=', $id)->execute()->current();
        if ($invoice) {
            $invoice['event'] = DB::select('*')
                ->from(self::TABLE_EVENTS)
                ->where('id', '=', $invoice['event_id'])
                ->execute()
                ->current();
        }
        return $invoice;
    }

    public static function invoiceUpdate($invoice)
    {
        DB::update(self::TABLE_SINVOICES)->set($invoice)->where('id', '=', $invoice['id'])->execute();
    }

	public function get_attendees($arg = '', $order_by_lastname = false, $csv_fields = false, $filter = array())
	{
		$q = DB::select()
			->from(array(self::TABLE_ORDERS, 'order'))
			->join(array(self::TABLE_ORDER_ITEMS,      'item'       ))->on('item.order_id', '=', 'order.id')
            ->join(array(self::TABLE_ORDER_ITEM_DATES, 'item_dates' ))->on('item.id', '=', 'item_dates.order_item_id')
            ->join(array(self::TABLE_TICKETS,      'tickets'        ))->on('item_dates.id', '=', 'tickets.order_item_has_date_id')
			->join(array(self::TABLE_HAS_TICKET_TYPES, 'ticket_type'))->on('item.ticket_type_id', '=', 'ticket_type.id')
			->where('ticket_type.event_id', '=', $this->id)
			->and_where('order.deleted', '=', 0)
            ->and_where('order.status', '=', self::ORDER_STATUS_PAID);

        if($order_by_lastname){
            $q->order_by(DB::expr("CASE
                WHEN ((`order`.`lastname` IS NULL OR `order`.`lastname` = '') AND TRIM(`order`.`firstname`) LIKE '% %')
                THEN substring_index(TRIM(`order`.`firstname`), ' ', -1)
                ELSE `order`.`lastname`
                END"), 'ASC');
        }

		// If it's for a CSV, just return the columns we want to appear in the CSV
		if ($arg == 'csv')
		{

            $csv_fields_data = array(
                'item_id' => array('tickets.code', 'Ticket No.'),
                'order_firstname' => array(DB::expr("CONCAT_WS(' ', `order`.`firstname`, `order`.`lastname`)"), 'Customer Name'),
                'order_email' => array('order.email', 'Customer Email Address'),
                'order_id' => array(DB::expr("LPAD(`order`.`id`, 8, '0')"), 'Order No.'),
                'item_total' => array('item.total', 'Total'),
                'order_status' => array('order.status', 'Status'),
                'order_created' => array('order.created', 'Date Paid'),
                'ticket_type' => array('ticket_type.name', 'Ticket Type')
            );

            if ($csv_fields)
            {
                $group_by_order = TRUE;
                foreach($csv_fields as $field => $value)
                {
                    $q->select($csv_fields_data[$field]);
                    if ($field == 'item_id' OR $field == 'ticket_type')
                    {
                        $group_by_order = FALSE;
                    }
                }

                if ($group_by_order)
                {
                    $q->group_by('order_id');
                }
            }
		}

        if (is_array($filter)) {
            foreach ($filter as $filter_key => $filter_value) {
                if ($filter_key == 'ticket_type_id') {
                    if (count($filter_value) > 0) {
                        $q->and_where('item.ticket_type_id', 'in', $filter_value);
                    }
                }
                if ($filter_key == 'date_id') {
                    $q->and_where('item_dates.date_id', '=', $filter_value);
                }
            }
        }

        $q->order_by('order.firstname');
        $q->order_by('order.id');

		return $q->execute()->as_array();
	}

	function find_all_upcoming()
	{
        $dom_after_minutes = Settings::instance()->get('events_display_on_home_after_start_minutes');
		return $this
            ->select('event.*', 'date.starts')
			->join(array(self::TABLE_DATES, 'date'), 'left')->on('event.id', '=', 'date.event_id')
			->where('is_public', '=', 1)
            ->and_where_open()
                ->or_where('date.starts', '>=', date('Y-m-d H:i:s'), time() - ($dom_after_minutes * 60))
                ->or_where('date.ends', '>=', date('Y-m-d H:i:s'), time() - ($dom_after_minutes * 60))
            ->and_where_close()
            ->and_where_open()
				->where('status', '=', Model_Event::EVENT_STATUS_LIVE)
				->or_where('status', '=', Model_Event::EVENT_STATUS_SALE_ENDED)
			->and_where_close()
            ->order_by('date.starts', 'asc')
			->find_all_published();
	}

    /**
     * Get the image URL for the event. If there is no image, return the placeholder URL.
     *
     * @param null $arg
     * @return string
     * @throws Kohana_Exception
     */
	function get_image($args = array())
	{
		return self::static_get_image($this->as_array(), $args);
	}

    public static function static_get_image($event, $args = array())
    {
        $url = '';
        $use_placeholder = isset($args['placeholder']) ? $args['placeholder'] : true;

        if ($event['image_media_id']) {
            $url = Model_Media::get_path_to_id($event['image_media_id']);
        }

        if (!$url && $use_placeholder) {
            $url = Model_Media::get_image_path('no_image_available.png', 'events');
        }

        return $url;
    }

    /* Get the necessary data to render the event as a page banner. */
    function get_banner_data()
    {
        $next_date = $this->get_next_date();
        return array(
            'image'      => $this->get_image(),
            'title'      => html::entities($this->name),
            'venue'      => html::entities($this->venue->name),
            'start_date' => $next_date->starts,
            'end_date'   => $next_date->ends,
            'url'        => $this->get_url(),
            'event'      => $this,
            'target'     => '_self'
        );
    }

    /* Get the entire relative path URL for the event. Avoid hardcoding this link, even though it is a simple path. */
    function get_url()
    {
        return '/event/'.$this->url;
    }

    /* Same as the above function, but can be used when the event has not been loaded as an object */
    static function static_get_url($event)
    {
        return '/event/'.$event['url'];
    }

    /**
     * Get the URL for the search results page
     *
     * @param bool $include_last_search - Specify if the URL should contain parameters from the last search
     * @return string
     */
    public static function get_search_url($include_last_search = false)
    {
        $return = '/events';

        if ($include_last_search) {
            $query = Session::instance()->get('last_event_search_params');

            if (!empty($query) && (is_object($query) || is_array($query))) {
                $return .= '?'.http_build_query($query);
            }
        }

        return $return;
    }

    function get_date_information()
    {
        $dates = DB::select('dates.*', array('orders.id', 'has_order'))
            ->from(array(self::TABLE_DATES, 'dates'))
            ->join(array(self::TABLE_ORDER_ITEM_DATES, 'idates'), 'left')->on('dates.id', '=', 'idates.date_id')
            ->join(array(self::TABLE_ORDER_ITEMS, 'items'), 'left')->on('idates.order_item_id', '=', 'items.id')
            ->join(array(self::TABLE_ORDERS, 'orders'), 'left')
            ->on('orders.id', '=', 'items.order_id')
            ->on('orders.status', '=', DB::expr("'PAID'"))
            ->on('orders.deleted', '=', DB::expr(0))
            ->where('dates.event_id', '=', $this->id)
            ->and_where('dates.deleted', '=', 0)
            ->and_where('dates.starts', '<>', '0000-00-00 00:00:00')
            ->order_by('dates.starts', 'asc')
            ->group_by('dates.id')
            ->execute()
            ->as_array();

        $has_ended = true;
        $end_date = null;

        foreach ($dates as $i => $date) {
            $dates[$i]['others'] = @json_decode($dates[$i]['others'], true);
            $dates[$i]['start_date'] = $date['starts'] ? date('Y-m-d', strtotime($date['starts'])) : '';
            $dates[$i]['start_time'] = $date['starts'] ? date('H:i', strtotime($date['starts'])) : '';
            if ($date['ends']) {
                $dates[$i]['end_date'] = date('Y-m-d', strtotime($date['ends']));
                $dates[$i]['end_time'] = date('H:i', strtotime($date['ends']));
                $end_date = $date['ends'];
            } else {
                $dates[$i]['end_date'] = '';
                $dates[$i]['end_time'] = '';
                $end_date = date('Y-m-d 23:59:59', strtotime($date['starts'])); // The end of the day
            }

            $dates[$i]['checked_in_count'] = 0;
            $dates[$i]['sold_count'] = 0;

            if (isset($checkin_stats[$date['id']])) {
                $dates[$i]['checked_in_count'] = $checkin_stats[$date['id']]['checked_in_count'];
                $dates[$i]['sold_count'] = $checkin_stats[$date['id']]['sold_count'];
            }

            // Event has ended if all of these dates have passed
            $has_ended = ($has_ended && (strtotime($end_date) <= strtotime(date('Y-m-d H:i:s'))));
        }

        return array(
            'dates'     => $dates,
            'end_date'  => $end_date,
            'has_ended' => $has_ended
        );
    }

    public function get_dates()
    {
        $dates = $this->get_date_information();
        return $dates['dates'];
    }

	function get_next_date()
	{
		return ORM::factory('Event_Date')
			->where('event_id', '=', $this->id)
			->where('starts', '>=', date('Y-m-d'))
			->find_undeleted();
	}

    public function has_ended()
    {
        $dates = $this->get_date_information();
        return $dates['has_ended'];
    }

    public static function currency_symbol($code, $encode = true)
    {
        switch ($code) {
            case 'EUR': $symbol = '€'; break;
            case 'GBP': $symbol = '£'; break;
            case 'USD': $symbol = '$'; break;
            default   : $symbol = $code; break;
        }

        if ($encode) {
            $symbol = htmlentities($symbol);
        }

        return $symbol;
    }

    /**
     * Get videos. Parses the URLs to determine the providers, video IDs and embed URLs
     *
     * @param null $providers - Filter results by provider. Either enter a string with one provider or an array of multiple providers.
     * @return array
     */
    function get_videos($providers = null)
    {
        $return    = array();
        $providers = array_filter(is_array($providers) ? $providers : array($providers));
        $videos    = $this->videos ? array_filter(json_decode($this->videos)) : array();

        $youtube_pattern = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";
        $vimeo_pattern   = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/";

        foreach ($videos as $video) {
            $video = trim($video);

            if (preg_match($youtube_pattern, $video, $matches)) {
                if (!$providers || in_array('youtube', $providers)) {
                    $return[] = array(
                        'url'       => $video,
                        'provider'  => 'youtube',
                        'id'        => $matches[1],
                        'embed_url' => 'https://www.youtube.com/embed/'.$matches[1]
                    );
                }
            }
            elseif (preg_match($vimeo_pattern, $video, $matches)) {
                if (!$providers || in_array('vimeo', $providers)) {
                    $return[] = array(
                        'url'       => $video,
                        'provider'  => 'vimeo',
                        'id'        => $matches[3],
                        'embed_url' => 'https://player.vimeo.com/video/'.$matches[3]
                    );
                }
            }
            else {
                if (!$providers || in_array('other', $providers)) {
                    $return[] = array(
                        'url'       => $video,
                        'provider'  => 'other',
                        'id'        => null,
                        'embed_url' => null
                    );
                }
            }
        }

        return $return;
    }

    public function get_primary_organizer()
    {
        return $this->organizers->where('is_primary', '=', 1)->find_undeleted();
    }

    public function get_other_organizers()
    {
        return $this->organizers->where('is_primary', '!=', 1)->find_all_undeleted();
    }

    // Get events for the "You might also like" section.
    // Get three random events with the same topic as the current event
    public function get_related_events($params = array())
    {
        $limit = isset($params['limit']) ? $params['limit'] : 3;

        $search = self::get_for_global_search(array(
            'topic_id'       => $this->topic_id,
            'exclude_events' => array($this->id), // prevent the current event from appearing
            'direction'      => 'random',
            'limit'          => $limit,
            'whole_site'     => false,
            'group_by'       => 'events.id' // stop the same event appearing more than once
        ));

        $return = array();
        foreach ($search['data'] as $event) {
            $return[] = new Model_Event($event['id']);
        }

        return $return;
    }

    public static function contact_form_handler($post)
    {
        $recipients = array();
        $message = "";
        $subject = 'Event Enquiry';
        $ids = array();

        if (isset($post['event_id'])) {
            $event = Model_Event::eventLoad($post['event_id']);
            foreach ($event['organizers'] as $key => $organiser) {
                $ids[$key] = $organiser['id'];
            }
            sort($ids);
            if (isset($post['organiser_id'])) {
                foreach ($event['organizers'] as $organiser) {
                    if ($post['organiser_id'] == $organiser['id']) {
                        $message .= 'Organiser: ' . $organiser['first_name'] . ' ' . $organiser['last_name'] . "<br />\n";
                        $recipients[] = array('target_type' => 'CMS_CONTACT', 'target' => $organiser['contact_id']);
                        $recipients[] = array('target_type' => 'EMAIL', 'target' => $organiser['email']);
                    }
                }

            }
        }
        if (isset($post['venue_id'])) {
            $venue = Model_Event::venueLoad($post['venue_id']);
            $message .= "Venue: " . $event['venue']['name'] . "<br />\n";
            $recipients[] = array('target_type' => 'EMAIL', 'target' => $venue['email']);
        }
        if (isset($post['event_id'])) {
            $message .= "Event: <a href=\"" . URL::site('/event/' . $event['url']) . "\">" . strip_tags($event['name']) . "</a><br />\n";
        }
        $message .= "Name: " . strip_tags($post['name']) . "<br />\n";
        $message .= "Email: " . strip_tags($post['email']) . "<br />\n";
        $message .= "Telephone: " . strip_tags($post['telephone']) . "<br />\n";
        $message .= "Message: " . strip_tags($post['message']) . "<br />\n";

        $mm = new Model_Messaging();
        $mm->send('email', null, null, $recipients, $message, $subject, null, 0, 'new', array(), $post['email']);

        return $event;
    }

    public static function contact_form_handler_organiser($post)
    {
        $recipients = array();
        $message = "";
        $subject = 'Organiser Enquiry';

        if (isset($post['organiser_id'])) {
            $organiser = DB::select('contacts.*', 'has.description', 'organisers.*')
                ->from(array(self::TABLE_HAS_ORGANIZERS, 'has'))
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')->on('has.contact_id', '=', 'contacts.id')
                ->join(array(self::TABLE_ORGANIZERS, 'organisers'), 'inner')->on('has.contact_id', '=', 'organisers.contact_id')
                ->where('organisers.contact_id', '=', $post['organiser_id'])
                ->execute()
                ->current();

            $message .= 'Organiser: ' . $organiser['first_name'] . ' ' . $organiser['last_name'] . "<br />\n";
            $recipients[] = array('target_type' => 'CMS_CONTACT', 'target' => $organiser['contact_id']);
            $recipients[] = array('target_type' => 'EMAIL', 'target' => $organiser['email']);
        }

        if (isset($post['name']) && isset($post['email']) && isset($post['telephone']) && isset($post['message'])) {
            $message .= "Name: " . strip_tags($post['name']) . "<br />\n";
            $message .= "Email: " . strip_tags($post['email']) . "<br />\n";
            $message .= "Telephone: " . strip_tags($post['telephone']) . "<br />\n";
            $message .= "Message: " . strip_tags($post['message']) . "<br />\n";

            $mm = new Model_Messaging();
            $mm->send('email', null, null, $recipients, $message, $subject, null, 0, 'new', array(), $post['email']);
            return true;
        } else {
            return false;
        }
    }

    public static function home_page_helper($view)
    {
        $dom_after_minutes = Settings::instance()->get('events_display_on_home_after_start_minutes');
        $upcoming_events = ORM::factory('Event')->limit(16)->find_all_upcoming();
        $featured_events = ORM::factory('Event')->where('featured', '=', 1)->limit(6)->find_all_upcoming();
        $banner_events = Model_Event::search(array(
            'is_home_banner' => 1,
            'publish'        => 1,
            'deleted'        => 0,
            'is_public'      => 1,
            'order_by'       => 'dates.starts',
            'direction'      => 'desc',
            'offset'         => 0,
            'limit'          => 10,
            'status'         => Model_Event::EVENT_STATUS_LIVE,
            'after'          => date('Y-m-d H:i:s', time() - ($dom_after_minutes * 60))
        ));
        
        $banner = Model_PageBanner::get_banner_data ($view->page_data['banner_photo'], false);
        $cs = new Model_Customscroller();
        $sequence = $cs->get_custom_sequence_data_front_end($banner['sequence_id']);
        $sequence_items = $cs->get_custom_sequence_items_data_front_end($banner['sequence_id'], $sequence['order_type']);
        $banner_items = array();

        foreach ($banner_events as $banner_event) {
            $event = new Model_Event($banner_event['id']);
            $banner_items[] = $event->get_banner_data();

            $sqitem = current($sequence_items);
            if (next($sequence_items) === false) {
                reset($sequence_items);
            }

            $sqitem['event'] = false;
            $banner_items[] = $sqitem;
        }

        $all_upcoming_events = ORM::factory('Event')->find_all_upcoming();

        $view
            ->set('all_upcoming_events', $all_upcoming_events)
            ->set('upcoming_events', $upcoming_events)
            ->set('upcoming_events_count', count($all_upcoming_events))
            ->set('featured_events', $featured_events)
            ->set('banner_events', $banner_events)
			->set('banner_items', $banner_items)
			->set('banner_sequence', $sequence);

    }

    public static function calculate_paymentgw_fee($payment)
    {
        if (is_numeric($payment)) {
            $payment = DB::select('*')->from(self::TABLE_PAYMENTS)->where('id', '=', $payment)->execute()->current();
        }

        $payment_id = $payment['id'];
        $paymentgw = DB::select('*')->from(self::TABLE_PAYMENTGWS)->where('paymentgw', '=', $payment['paymentgw'])->execute()->current();
        if (!$paymentgw) {
            return 0;
        }
        $fee = 0.0;
        $calculate_per_charge = true;
        if ($paymentgw['month_cap'] > 0) {
            /*
             * count the number of charges made via this gateway until this payment */
            $month = date('Y-m-01', strtotime($payment['created']));
            $countq = DB::select(DB::expr("count(*) AS cnt"))
                ->from(self::TABLE_PAYMENTS)
                ->where('paymentgw', '=', $payment['paymentgw'])
                ->and_where('status', '=', 'PAID')
                ->and_where('created', '>=', $month);
            if ($payment_id) {
                $countq->and_where('id', '<=', $payment_id);
            }
            $charged_count_upto = $countq->execute()->get('cnt');
            if ($charged_count_upto <= 1) { // the first payment in a month, set the fee as monthly fee of the gateway
                $fee = $paymentgw['month_fee'];
            }
            if ($charged_count_upto <= $paymentgw['month_cap']) {
                $calculate_per_charge = false;
            }
        }

        if ($calculate_per_charge) {
            $fee += $paymentgw['fixed_charge'];
            $fee += ($paymentgw['percent_charge'] * $payment['amount']);
        }

        return $fee;
    }

    public static function set_payment_fee($id, $fee)
    {
        DB::update(self::TABLE_PAYMENTS)
            ->set(array('paymentgw_fee' => $fee))
            ->where('id', '=', $id)
            ->execute();
    }

    public static function set_paymentgw_fees()
    {
        $payments = DB::select('*')
            ->from(self::TABLE_PAYMENTS)
            ->where('paymentgw_fee', 'IS', null)
            ->and_where('status', '=', 'PAID')
            ->execute()
            ->as_array();
        foreach ($payments as $payment) {
            $fee = self::calculate_paymentgw_fee($payment);
            self::set_payment_fee($payment['id'], $fee);
        }
    }

    static function hhmmss_to_seconds($input)
    {
        $pattern = '/^\d\d:[0-5]\d:[0-5]\d$/'; // HH:MM:SS (00:00:00 to 99:59:59)
        if (preg_match($pattern, $input, $matches))
        {
            $array = explode(':', $input);
        }

        if ( ! empty($array) AND isset($array[2]))
        {
            ltrim($array[0], '0');
            ltrim($array[1], '0');
            ltrim($array[2], '0');
            return (int)$array[0] * 3600 + (int)$array[1] * 60 + (int)$array[2];
        }
        else
        {
            return false;
        }
    }

    public static function round2($value)
    {
        $valuex = round($value * 100, 0);
        $valuey = floor($valuex);
        $valuez = $valuey / 100;
        return $valuez;
    }

    public static function calculate_price_breakdown($price, $fee_fixed, $fee_percent, $vat_rate, $absorb_fee)
    {
        $result = array(
            'base_price' => 0,
            'fee' => 0,
            'vat' => 0,
            'total' => 0
        );

        if ($price == 0) {
            return $result;
        }

        if ($absorb_fee) {
            $result['base_price'] = (float)self::solveBasePriceFeesIncluded(
                $price,
                array(
                    'type' => $fee_percent > 0 ? 'Percent' : 'Fixed',
                    'fixed_charge_amount' => $fee_fixed,
                    'amount' => $fee_percent > 0 ? $fee_percent : 0
                ),
                $vat_rate
            );
        } else {
            $result['base_price'] = (float)$price;
        }

        $result['fee'] = (float)($fee_fixed);
        $result['fee'] += (float)(floor(self::round2($result['base_price'] * $fee_percent)) / 100);
        $result['vat'] = (float)(floor(self::round2($result['fee'] * ($vat_rate * 100))) / 100);
        $result['total'] = (float)($result['base_price'] + $result['fee'] + $result['vat']);

        return $result;
    }

    public static function calculate_payment_plan($base_amount, $paymentplan, $commission, $vat_rate, $paymore = 0)
    {
        $payments = array();
        $remaining = $base_amount;
        $first = true;
        $payless = 0;
        foreach ($paymentplan as $i => $payment) {
            if ($payment['payment_type'] == 'Percent') {
                $partial_amount = round($base_amount * ($payment['payment_amount'] / 100), 2);
            } else {
                $partial_amount = round($payment['payment_amount'], 2);
            }

            if ($payless > 0) {
                $partial_amount -= $payless;
                if ($partial_amount < 0) {
                    $payless = -$partial_amount;
                    $partial_amount = 0;
                } else {
                    $payless = 0;
                }
            }
            if ($first && $paymore) {
                $partial_amount += $paymore;
                $payless = $paymore;
            }
            $partial_amount = min($partial_amount, $remaining);
            $remaining -= $partial_amount;
            $pp_break_down = self::calculate_price_breakdown(
                $partial_amount,
                $commission['fixed_charge_amount'],
                $commission['type'] == 'Percent' ? $commission['amount'] : 0,
                $vat_rate,
                false
            );
            $payment['payment_amount'] = $partial_amount;
            $payment['fee'] = $pp_break_down['fee'];
            $payment['vat'] = $pp_break_down['vat'];
            $payment['total'] = $pp_break_down['total'];
            $payment['remaining'] = $remaining;
            $payments[] = $payment;
        }
        return $payments;
    }

    public static function calculate_payment_split($amount, $emails)
    {
        $payment_each = round($amount / count($emails), 2);
        $remains = $amount;
        $payments = array();
        foreach ($emails as $email) {
            $payment = min($remains, $payment_each);
            $remains -= $payment;
            $payments[] = array(
                'payment' => $payment,
                'email' => $email
            );
        }
        return $payments;
    }

    public static function email_payers($order_id, $comment = '', $payers = array())
    {
        $order = self::orderLoad($order_id);
        $message_parameters = array();
        $message_parameters['buyer']     = $order['firstname'] . ' ' . $order['lastname'];
        $message_parameters['comment']   = $comment;
        $message_parameters['eventname'] = $order['items'][0]['event'];
        $message_parameters['eventdate'] = date('j F Y', strtotime($order['items'][0]['starts']));
        $message_parameters['logosrc']   = Model_Media::get_image_path('logo.png', 'content');
        $message_parameters['order_id']  = $order_id;
        $message_parameters['payer']     = $order['firstname'] . ' ' . $order['lastname'];
        $message_parameters['project']   = Settings::instance()->get('company_title');

        $mm = new Model_Messaging();
        foreach ($order['partialpayments'] as $partial_payment) {
            if ($partial_payment['payment_id'] > 0) {
                continue;
            }
            if ($partial_payment['payer_name']) {
                $message_parameters['payer'] = $partial_payment['payer_name'];
            }
            $message_parameters['amount'] = $order['currency'] . $partial_payment['total'];
            $message_parameters['due_date'] = date('j F Y', strtotime($partial_payment['due_date']));
            $message_parameters['link'] = URL::site('/checkout.html?' . http_build_query(array('order_id' => $order_id, 'partial_id' => $partial_payment['id'], 'url_hash' => $partial_payment['url_hash'])));

            $recipients = [];
            if (count($payers) > 0) {
                $message_parameters['payer'] = '';
                foreach ($payers as $payer) {
                    $recipients[] = array('target_type' => 'EMAIL', 'target' => $payer['email']);
                }
            } else {
                if (@$partial_payment['payer_email'] != '' && trim(strtolower($partial_payment['payer_email'])) != trim(strtolower($order['email']))) {
                    $recipients[] = array('target_type' => 'EMAIL', 'target' => $partial_payment['payer_email']);
                }
            }
            $mm->send_template(
                'event-partial-payment',
                null,
                null,
                $recipients,
                $message_parameters
            );
        }
    }

    public static function get_order_from_partial_payment_id($partial_id, $hash = null)
    {
        $select = DB::select('partial_payments.*', 'payments.order_id')
            ->from(array(self::TABLE_PARTIAL_PAYMENTS, 'partial_payments'))
            ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')
            ->on('partial_payments.main_payment_id', '=', 'payments.id')
            ->where('partial_payments.id', '=', $partial_id);
        if ($hash) {
            $select->and_where('url_hash', '=', $hash);
        }
        $partial_payment = $select->execute()->current();

        if ($partial_payment) {
            $paymentplan = DB::select(
                'partial_payments.*',
                'payments.order_id',
                'paymentplans.title',
                'paymentplans.payment_type',
                'paymentplans.payment_amount'
            )
                ->from(array(self::TABLE_PARTIAL_PAYMENTS, 'partial_payments'))
                ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')
                ->on('partial_payments.main_payment_id', '=', 'payments.id')
                ->join(array(self::TABLE_HAS_PAYMENTPLANS, 'paymentplans'), 'left')
                ->on('partial_payments.paymentplan_id', '=', 'paymentplans.id')
                ->where('partial_payments.main_payment_id', '=', $partial_payment['main_payment_id'])
                ->execute()
                ->as_array();
            $order = self::orderLoad($partial_payment['order_id']);
            $result = array(
                'partial_payment' => $partial_payment,
                'order' => $order,
                'paymentplan' => $paymentplan
            );
            return $result;
        } else {
            return false;
        }
    }

    public static function calculate_paymore_partial_payments($partial_payment_id, $amount)
    {
        $partial_payment = DB::select('partial_payments.*', 'payments.order_id')
            ->from(array(self::TABLE_PARTIAL_PAYMENTS, 'partial_payments'))
            ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')
            ->on('partial_payments.main_payment_id', '=', 'payments.id')
            ->where('partial_payments.id', '=', $partial_payment_id)
            ->execute()
            ->current();
        $order = self::orderLoad($partial_payment['order_id']);
        $event = self::eventLoad($order['items'][0]['event_id']);
        $commission = Model_Event::commissionGet($event, $event['owned_by']);

        $partial_payments = DB::select('*')
            ->from(self::TABLE_PARTIAL_PAYMENTS)
            ->where('main_payment_id', '=', $partial_payment['main_payment_id'])
            ->execute()
            ->as_array();

        $cnt = 0;
        foreach ($partial_payments as $tpartial_payment) {
            if ($tpartial_payment['id'] != $partial_payment_id) {
                if ($tpartial_payment['payment_id'] == null) { // unpaid
                    ++$cnt;
                }
            }
        }

        $paymore_diff = $amount;
        $payless_each = round($paymore_diff / $cnt, 2);
        $payless = 0;
        foreach ($partial_payments as $i => $calc_partial_payment) {
            if ($calc_partial_payment['payment_id'] == null) { // unpaid
                if ($calc_partial_payment['id'] != $partial_payment_id) {
                    if ($i == $cnt - 1) {
                        $payless_each = $paymore_diff - $payless;
                    }
                    $partial_payments[$i]['payment_amount'] -= $payless_each;
                    $payless += $payless_each;
                } else {
                    $partial_payments[$i]['payment_amount'] += $amount;
                }

                $pp_break_down = self::calculate_price_breakdown(
                    $partial_payments[$i]['payment_amount'],
                    $commission['fixed_charge_amount'],
                    $commission['type'] == 'Percent' ? $commission['amount'] : 0,
                    $order['vat_rate'],
                    false
                );
                $partial_payments[$i]['commission_total'] = $pp_break_down['fee'];
                $partial_payments[$i]['vat_total'] = $pp_break_down['vat'];
                $partial_payments[$i]['total'] = $pp_break_down['total'];
            }
        }
        return $partial_payments;
    }

    public static function calculate_partial_payments_remaing($partial_payment_id)
    {
        $partial_payment = DB::select('*')
            ->from(self::TABLE_PARTIAL_PAYMENTS)
            ->where('id', '=', $partial_payment_id)
            ->execute()
            ->current();

        $partial_payments = DB::select('*')
            ->from(self::TABLE_PARTIAL_PAYMENTS)
            ->where('main_payment_id', '=', $partial_payment['main_payment_id'])
            ->execute()
            ->as_array();

        $balance = 0;
        $total = 0;
        $due_date = time();
        foreach ($partial_payments as $tpartial_payment) {
            if ($tpartial_payment['payment_id'] == null) { // unpaid
                $balance += $tpartial_payment['payment_amount'];
                $total += $tpartial_payment['total'];
                $due_date = max($due_date, strtotime($tpartial_payment['due_date']));
            }
        }

        return array('balance' => $balance, 'due_date' => date('d/m/Y', $due_date), 'total' => $total, 'due_date0' => $due_date);
    }

    public static function invite_payers_2($order_id, $payers, $comment)
    {
        $partial_payments = DB::select('partial_payments.*', 'orders.email', 'orders.firstname', 'orders.lastname', 'payments.order_id', 'orders.buyer_id')
            ->from(array(self::TABLE_ORDERS, 'orders'))
            ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')
            ->on('orders.id', '=', 'payments.order_id')
            ->join(array(self::TABLE_PARTIAL_PAYMENTS, 'partial_payments'), 'inner')
            ->on('payments.id', '=', 'partial_payments.main_payment_id')
            ->where('orders.id', '=', $order_id)
            ->and_where('partial_payments.payment_id', 'is', null)
            ->execute()
            ->as_array();

        $order = self::orderLoad($partial_payments[0]['order_id']);

        self::email_payers($order['id'], $comment, $payers);
    }

    public static function invite_payers($order_id, $payers, $comment)
    {
        $partial_payments = DB::select('partial_payments.*', 'orders.email', 'orders.firstname', 'orders.lastname', 'payments.order_id', 'orders.buyer_id')
            ->from(array(self::TABLE_ORDERS, 'orders'))
                ->join(array(self::TABLE_PAYMENTS, 'payments'), 'inner')
                    ->on('orders.id', '=', 'payments.order_id')
                ->join(array(self::TABLE_PARTIAL_PAYMENTS, 'partial_payments'), 'inner')
                    ->on('payments.id', '=', 'partial_payments.main_payment_id')
            ->where('orders.id', '=', $order_id)
            ->and_where('partial_payments.payment_id', 'is', null)
            ->execute()
            ->as_array();

        $payers[] = array(
            'email' => $partial_payments[0]['email'],
            'firstname' => $partial_payments[0]['firstname'],
            'lastname' => $partial_payments[0]['lastname']
        );
        //calculate new partial payments, delete old ones
        try {
            Database::instance()->begin();
            $order = self::orderLoad($partial_payments[0]['order_id']);
            $event = self::eventLoad($order['items'][0]['event_id']);
            $commission = Model_Event::commissionGet($event, $event['owned_by']);

            $emails = array();
            foreach ($partial_payments as $partial_payment) {
                $buyer_id = $partial_payment['buyer_id'];
                DB::delete(self::TABLE_PARTIAL_PAYMENTS)->where('id', '=', $partial_payment['id'])->execute();
                $new_payments = self::calculate_payment_split($partial_payment['payment_amount'], $payers);
                foreach ($payers as $i => $payer) {
                    $new_partial_payment = array(
                        'main_payment_id' => $partial_payment['main_payment_id'],
                        'payer_email' => $payer['email'],
                        'payer_name' => $payer['firstname'] . ' ' . $payer['lastname'],
                        'payment_amount' => $new_payments[$i]['payment'],
                        'due_date' => $partial_payment['due_date'],
                        'url_hash' => uniqid(),
                        'paymentplan_id' => $partial_payment['paymentplan_id']
                    );

                    $pp_break_down = self::calculate_price_breakdown(
                        $new_payments[$i]['payment'],
                        $commission['fixed_charge_amount'],
                        $commission['type'] == 'Percent' ? $commission['amount'] : 0,
                        $order['vat_rate'],
                        false
                    );
                    $new_partial_payment['commission_total'] = $pp_break_down['fee'];
                    $new_partial_payment['vat_total'] = $pp_break_down['vat'];
                    $new_partial_payment['total'] = $pp_break_down['total'];

                    $inserted = DB::insert(self::TABLE_PARTIAL_PAYMENTS)->values($new_partial_payment)->execute();
                    $emails[] = array(
                        'name' => $new_partial_payment['payer_name'],
                        'email' => $new_partial_payment['payer_email'],
                        'due_date' => $new_partial_payment['due_date'],
                        'amount' => $new_partial_payment['total'],
                        'id' => $inserted[0],
                        'url_hash' => $new_partial_payment['url_hash']
                    );
                }
            }
            Database::instance()->commit();

            try {
                //self::email_payment_plan($order['id']);
                self::email_payers($order['id'], $comment);
            } catch (Exception $exc) {
                Log::instance()->add(Log::ERROR, "Error sending payer emails.\n".$exc->getMessage()."n".$exc->getTraceAsString());
            }

            return true;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function update_unpaid_partial_payments($main_payment_id, $paymore_diff, $commission, $vat_rate)
    {
        $remaining_partial_payments = DB::select('*')
            ->from(self::TABLE_PARTIAL_PAYMENTS)
            ->where('main_payment_id', '=', $main_payment_id)
            ->and_where('payment_id', 'is', null)
            ->execute()
            ->as_array();

        $cnt = count($remaining_partial_payments);
        $payless_each = round($paymore_diff / $cnt, 2);
        $payless = 0;
        foreach ($remaining_partial_payments as $i => $remaining_partial_payment) {
            if ($i == $cnt - 1) {
                $payless_each = $paymore_diff - $payless;
            }
            $remaining_partial_payment['payment_amount'] -= $payless_each;
            $pp_break_down = self::calculate_price_breakdown(
                $remaining_partial_payment['payment_amount'],
                $commission['fixed_charge_amount'],
                $commission['type'] == 'Percent' ? $commission['amount'] : 0,
                $vat_rate,
                false
            );
            $remaining_partial_payment['commission_total'] = $pp_break_down['fee'];
            $remaining_partial_payment['vat_total'] = $pp_break_down['vat'];
            $remaining_partial_payment['total'] = $pp_break_down['total'];
            $payless += $payless_each;
            DB::update(self::TABLE_PARTIAL_PAYMENTS)
                ->set($remaining_partial_payment)
                ->where('id', '=', $remaining_partial_payment['id'])
                ->execute();
        }
    }

    public static function email_payment_plan($order_id)
    {
        $order = self::orderLoad($order_id);
        $organisers = (!empty($order['items']) && !empty($order['items'][0]['event_id'])) ? Model_Event::getEventOrganisers($order['items'][0]['event_id']) : [];

        $message_parameters = array();
        $message_parameters['address_1']       = $order['address_1'];
        $message_parameters['address_2']       = $order['address_2'];
        $message_parameters['base_url']        = Url::site();
        $message_parameters['buyer']           = $order['firstname'] . ' ' . $order['lastname'];
        $message_parameters['buyer_help_url']  = Url::site('support');
        $message_parameters['city']            = $order['city'];
        $message_parameters['country']         = Model_Event::getCountryName($order['country_id']);
        $message_parameters['county']          = $order['county'];
        $message_parameters['email']           = $order['email'];
        $message_parameters['eventname']       = $order['items'][0]['event'];
        $message_parameters['eventdate']       = date('j F Y', strtotime($order['items'][0]['starts']));
        $message_parameters['firstname']       = $order['firstname'];
        $message_parameters['logosrc']         = Model_Media::get_image_path('logo.png', 'content');
        $message_parameters['order_id']        = $order_id;
        $message_parameters['organiser_name']  = (count($organisers) > 0) ? htmlentities($organisers[0]['first_name'] . ' ' . $organisers[0]['last_name']) : '';
        $message_parameters['organiser_email'] = (count($organisers) > 0) ? $organisers[0]['email'] : '';
        $message_parameters['payer']           = $order['firstname'] . ' ' . $order['lastname'];
        $message_parameters['profile_url']     = Url::site('admin/profile/edit?section=contact');
        $message_parameters['project']         = Settings::instance()->get('company_title');
        $message_parameters['telephone']       = $order['telephone'];

        // Set default values, as these might not be declared later
        $message_parameters['nextpayment']     = '';
        $message_parameters['next_due_date']   = '';
        $message_parameters['finalpayment']    = '';
        $message_parameters['final_due_date']  = '';

        $links = '<table cellspacing="0" cellpadding="5" style="margin: 0 -5px;">
            <thead>
                <tr>
                    <th scope="col" style="font-weight: bold; padding: 5px; text-align: left;">Title</th>
                    <th scope="col" style="font-weight: bold; padding: 5px; text-align: left;">Amount</th>
                    <th scope="col" style="font-weight: bold; padding: 5px; text-align: left;">Due Date</th>
                    <th scope="col" style="font-weight: bold; padding: 5px; text-align: left;">Link</th>
                </tr>
            </thead>';
        $links .= '<tbody>';

        if (!empty($order['partialpayments'])) {
            foreach ($order['partialpayments'] as $partial_payment) {
                if ($partial_payment['payment_id'] > 0) {
                    $links .= '<tr>' .
                        '<td style="padding: 5px;">' . $partial_payment['title'] . ($partial_payment['payer_name'] ? ' - ' . $partial_payment['payer_name'] : '') . '</td>' .
                        '<td style="padding: 5px;">' . $order['currency'] . $partial_payment['total'] . '</td>' .
                        '<td style="padding: 5px;">' . ($partial_payment['due_date'] ? date('j F Y',
                            strtotime($partial_payment['due_date'])) : '') . '</td>' .
                        '<td style="padding: 5px;">' . __('Paid') . '</td>' .
                        '</tr>';
                } else {
                    $links .= '<tr>' .
                        '<td style="padding: 5px;">' . $partial_payment['title'] . ($partial_payment['payer_name'] ? ' - ' . $partial_payment['payer_name'] : '') . '</td>' .
                        '<td style="padding: 5px;">' . $order['currency'] . $partial_payment['total'] . '</td>' .
                        '<td style="padding: 5px;">' . date('j F Y',
                            strtotime($partial_payment['due_date'])) . '</td>' .
                        '<td style="padding: 5px;">' .
                        '<a href="' . URL::site('/checkout.html?' . http_build_query(array(
                                'order_id' => $order_id,
                                'partial_id' => $partial_payment['id'],
                                'url_hash' => $partial_payment['url_hash']
                            ))) . '" style="color: #00aa87; text-decoration: none;">' .
                        'make payment' .
                        '</a>' .
                        '</td>' .
                        '</tr>';
                }
            }

            foreach ($order['partialpayments'] as $partial_payment) {
                if ($partial_payment['payment_id'] > 0) {
                    continue;
                }
                $message_parameters['next_due_date'] = date('j F Y', strtotime($partial_payment['due_date']));
                $message_parameters['nextpayment'] = $order['currency'] . $partial_payment['total'];
                break;
            }
            foreach ($order['partialpayments'] as $partial_payment) {
                if ($partial_payment['payment_id'] > 0) {
                    continue;
                }
                $message_parameters['final_due_date'] = date('j F Y', strtotime($partial_payment['due_date']));
                $message_parameters['finalpayment'] = $order['currency'] . $partial_payment['total'];
            }
        }

        $links .= '</tbody>';
        $links .= '</table>';
        $message_parameters['links'] = ['value' => $links, 'html' => true];
        $recipients = array(
            array(
                'target_type' => 'EMAIL',
                'target' => $order['email']
            )
        );
        $mm = new Model_Messaging();

        $mm->send_template('event-paymentplan-group-booking-created', null, null, $recipients, $message_parameters);
    }
}
