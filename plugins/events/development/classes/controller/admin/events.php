<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Events extends Controller_Cms
{

	public function before()
	{
		parent::before();

		$this->template->sidebar = View::factory('sidebar');
		$this->template->sidebar->breadcrumbs = array(
			array('name' => 'Home',   'link' => '/admin'),
			array('name' => 'Events', 'link' => '/admin/events')
		);
		$this->template->sidebar->menus = array
		(
			array(array('icon' => 'timetable', 'name' => 'Events'  , 'link' => '/admin/events'))
		);
        if (Auth::instance()->has_access('events_edit') || Auth::instance()->has_access('events_edit_limited')) {
            $this->template->sidebar->menus[0][] = array(
                'icon' => 'my-tickets',
                'name' => 'My Tickets',
                'link' => '/admin/events/mytickets'
            );

            $this->template->sidebar->menus[0][] = array(
                'icon' => 'invoices',
                'name' => 'Invoices',
                'link' => '/admin/events/invoices'
            );

            if (Auth::instance()->has_access('events_orders_view')){
                $this->template->sidebar->menus[0][] = array(
                    'icon' => 'orders',
                    'name' => 'Orders',
                    'link' => '/admin/events/orders'
                );
            }

            if (Auth::instance()->has_access('payments')){
                $this->template->sidebar->menus[0][] = array(
                    'icon' => 'payment',
                    'name' => 'Payments',
                    'link' => '/admin/events/payments'
                );
            }

            /*$this->template->sidebar->menus[0][] = array(
                'name' => 'Sold Tickets',
                'link' => '/admin/events/tickets'
            );*/

            if (Auth::instance()->has_access('lookups')) {
                $this->template->sidebar->menus[0][] = array(
                    'icon' => 'lookups',
                    'name' => 'Lookups',
                    'link' => '/admin/lookup'
                );
            }

            if (Auth::instance()->has_access('seo')) {
                $this->template->sidebar->menus[0][] = array(
                    'icon' => 'seo',
                    'name' => 'SEO',
                    'link' => '/admin/events/seo'
                );
            }
        }
	}

	public function action_index()
	{
        if (!Auth::instance()->has_access('events_index') && !Auth::instance()->has_access('events_index_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $searchParams = array();
            $searchParams['order_by'] = 'dates.starts';
            $searchParams['direction'] = 'desc';
            if (!Auth::instance()->has_access('events_index')) {
                $searchParams['owned_by'] = $user['id'];
            }

            $events = array();
            $this->template->styles[URL::get_engine_assets_base().'css/bootstrap-multiselect.css'] = 'multiselect';
			$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/list_events.js"></script>';
			$this->template->body = View::factory('/admin/list_events')->set('events', $events);
        }
	}

    public function action_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        if (!Auth::instance()->has_access('events_index') && !Auth::instance()->has_access('events_index_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $post = $this->request->query();
            $user = Auth::instance()->get_user();
            $searchParams = array();
            $searchParams['order_by'] = 'dates.starts';
            $searchParams['direction'] = 'desc';
            if (!Auth::instance()->has_access('events_index')) {
                $searchParams['owned_by'] = $user['id'];
            }

            if (@$post['sSearch']) {
                $searchParams['keyword2'] = $post['sSearch'];
            }
            $searchParams['limit'] = @$post['iDisplayLength'];
            $searchParams['offset'] = @$post['iDisplayStart'];
            $data = Model_Event::datatable($searchParams);
            $data['sEcho'] = $post['sEcho'];

            echo json_encode($data, JSON_PRETTY_PRINT);
        }
    }

    public function action_seo()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'SEO', 'link' => '/admin/events/seo');

        if (!Auth::instance()->has_access('events_index') && !Auth::instance()->has_access('events_index_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $seoEntries = $this->request->post('seo');

            if ($seoEntries) {
                foreach ($seoEntries as $seo) {
                    Model_Event::eventSaveSeo($seo);
                }
            }

            $user = Auth::instance()->get_user();
            $searchParams = array();
            if (!Auth::instance()->has_access('events_index')) {
                $searchParams['owned_by'] = $user['id'];
            }

            $events = Model_Event::search($searchParams);
            $this->template->body = View::factory('/admin/seo_list')->set('events', $events);
        }
    }

	public function action_edit_event()
	{
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Edit', 'link' => '#');

        $this->response->headers('cache-control', 'private, max-age=0, no-cache');
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $event_id = $this->request->param('id');
            $attendees = array();
            $tickets = array();
            if (is_numeric($event_id)) {
                $event = Model_Event::eventLoad($this->request->param('id'));
                if (!Auth::instance()->has_access('events_edit') && $event['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
                $attendees = Model_Event::ordersList(array(
                    'event_id' => $event_id,
                    'status' => 'PAID',
                ));

                $tickets = Model_Event::ticketsList(array(
                    'event_id' => $event_id,
                ));
            } else {
                $event = null;
            }
            
            $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='.Settings::instance()->get('google_map_key').'&libraries=places&sensor=false""></script>';
            //header('content-type: text/plain');print_r($event);exit;
            $categories = Model_Lookup::lookupList('Event Category');
            $topics = Model_Lookup::lookupList('Event Topic');
            $venues = Model_Event::getVenues();
            $account = Model_Event::accountDetailsLoad($user['id']);
            $this->template->styles[URL::get_engine_plugin_assets_base('events').'css/validation.css'] = 'screen';
            $this->template->styles[URL::get_engine_plugin_assets_base('events') . 'css/edit.css'] = 'screen';
            $countries = Model_Event::getCountryMatrix();
            $users = new Model_Users();
            $timezones = $users->generate_timezone_list();
            $commission = Model_Event::commissionGet($event, isset($event['owned_by']) ? $event['owned_by'] : $user['id']);
			$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/skycons.js"></script>';
			$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/jquery.validationEngine2.js"></script>';
			$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/jquery.validationEngine2-en.js"></script>';
			$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/edit.js?ts='.filemtime(ENGINEPATH.'plugins/events/development/assets/js/edit.js').'"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/multiple_upload.js"></script>';
			$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/image_edit.js"></script>';
            $this->template->body = View::factory('/admin/edit_event')
                ->set('event', $event)
                ->set('categories', $categories)
				->set('topics', $topics)
                ->set('countries', $countries)
                ->set('venues', $venues)
                ->set('commission', $commission)
                ->set('timezones', $timezones)
                ->set('attendees', $attendees)
                ->set('tickets', $tickets)
                ->set('account', $account)
                ->set('edit_seo', Auth::instance()->has_access('seo') ? true : false);
        }
	}

	// User is shown this screen, after they publish an event
	public function action_event_live()
	{
        $event = Model_Event::eventLoad($this->request->param('id'));
        $this->template->sidebar->breadcrumbs[] = array('name' => $event['name'], 'link' => '#');

        $user = Auth::instance()->get_user();
        $accountDetails = Model_Event::accountDetailsLoad($user['id']);
		$payment_method_reminder = ($accountDetails['id'] == null || ($accountDetails['stripe_auth'] == '' && ($accountDetails['iban'] == '' || $accountDetails['bic'] == '')));

		$view  = View::factory('/admin/event_live')
			->set('event', $event)
			->set('account', $accountDetails)
			->set('url', $event['url'])
			->set('payment_method_reminder', $payment_method_reminder)
		;
		$this->template->body = $view;
	}

	public function action_preview_event()
	{
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Preview', 'link' => '#');

		$event = Model_Event::eventLoad($this->request->param('id'));
		$url   = Model_Event::calculateUrlForEvent($event['name']);
		$view  = View::factory('/frontend/view_event')->set('event', $event)->set('url', $url);
		$this->template->body = $view;
	}

    public function action_duplicate_event()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Duplicate', 'link' => '/admin/events/duplicate_event/?id='.$this->request->query('id'));

        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You need access to the &quot;edit events&quot; feature to perform this action.", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $event_id = $this->request->query('id');
			$attendees = array();
            if (is_numeric($event_id)) {
                $event = Model_Event::eventLoad($event_id);
                if (!Auth::instance()->has_access('events_edit') && $event['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You do not have access to the event you are attempting to duplicate.", 'warning popup_box');
                    $this->request->redirect('/admin');
                }

				$attendees = Model_Event::ordersList(array(
					'event_id' => $event_id,
					'status' => 'PAID',
				));
            } else {
                $event = null;
            }
            if ($event) {
                $event['id'] = '';
                $event['name'] .= ' - clone';
                $event['ticket_types'] = array();
                $event['discounts'] = array();
                $event['dates'] = array();
                foreach ($event['tags'] as $i => $tags) {
                    $event['tags'][$i]['event_id'] = '';
                    $event['tags'][$i]['id'] = '';
                }
                $event['status'] = '';

                $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='.Settings::instance()->get('google_map_key').'"></script>';
                //header('content-type: text/plain');print_r($event);exit;
                $categories = Model_Lookup::lookupList('Event Category');
                $topics = Model_Lookup::lookupList('Event Topic');
                $venues = Model_Event::getVenues();
                $account = Model_Event::accountDetailsLoad($user['id']);
                $this->template->styles[URL::get_engine_plugin_assets_base('events') . 'css/validation.css'] = 'screen';
                $this->template->styles[URL::get_engine_plugin_assets_base('events') . 'css/edit.css'] = 'screen';
                $countries = Model_Event::getCountryMatrix();
                $users = new Model_Users();
                $timezones = $users->generate_timezone_list();
                $commission = Model_Event::commissionGet($event, isset($event['owned_by']) ? $event['owned_by'] : $user['id']);
                $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/skycons.js"></script>';
				$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/jquery.validationEngine2.js"></script>';
				$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/jquery.validationEngine2-en.js"></script>';
                $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('events') . 'js/edit.js?ts='.filemtime(ENGINEPATH.'plugins/events/development/assets/js/edit.js').'"></script>';
                $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/multiple_upload.js"></script>';
                $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/image_edit.js"></script>';
                $this->template->body = View::factory('/admin/edit_event')
                    ->set('event', $event)
                    ->set('categories', $categories)
                    ->set('topics', $topics)
                    ->set('countries', $countries)
                    ->set('venues', $venues)
                    ->set('commission', $commission)
                    ->set('timezones', $timezones)
                    ->set('attendees', $attendees)
                    ->set('account', $account)
                    ->set('edit_seo', Auth::instance()->has_access('seo') ? true : false);
            }
        }
    }

	public function action_save_event()
	{
        $action = $this->request->post('action');
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message('You need access to the "events_edit" permission to use this feature.', 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $post = $this->request->post();

            // If the URLs do not begin with a protocol, add "http://" to the beginning.
            if (!empty($post['venue']) && !empty($post['venue']['website']) && preg_match('/^(https?|ftp):\/\//i', $post['venue']['website']) === 0) {
                $post['venue']['website'] = 'http://'.$post['venue']['website'];
            }
            if (!empty($post['organizers'])) {
                foreach ($post['organizers'] as $key => $organizer) {
                    if (!empty($organizer['website']) && preg_match('/^(https?|ftp):\/\//i', $organizer['website']) === 0) {
                        $post['organizers'][$key]['website'] = 'http://'.$organizer['website'];
                    }
                }
            }

            if (is_numeric($post['id']) && !Auth::instance()->has_access('events_edit')) {
                $user = Auth::instance()->get_user();
                $event = Model_Event::eventLoad($post['id']);
                if($event['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }

            $eventId = Model_Event::eventSave($post);
            if(isset($post['action']) AND in_array($post['action'], array('save_stripe_connect', 'save_stripe_disconnect'))) {
                Session::instance()->set('stripe_connect_after_load_event_id', $eventId);
                if ($post['action'] == 'save_stripe_connect') {
                    $this->request->redirect('/admin/events/stripe_connect_start');
                } else {
                    $this->request->redirect('/admin/events/stripe_disconnect');
                }
                exit;
            }
            if ($action == 'preview') {
                $event = Model_Event::eventLoad($eventId);
                $this->request->redirect('/event/' . $event['url'] . '?preview=yes');
            } else {
                if(isset($post['action']) AND in_array($post['action'], array('make_offline'))) {
                    IbHelpers::set_message('The event has been successfully taken offline', 'success popup_box');
                } else {
                    IbHelpers::set_message('Event has been successfully saved', 'success popup_box');

                    $user = Auth::instance()->get_user();
                    $account = Model_Event::accountDetailsLoad($user['id']);
                    $account['use_stripe_connect'] = empty($account['use_stripe_connect']) && !empty($post['use_stripe_connect']) ? $post['use_stripe_connect'] : $account['use_stripe_connect'];
                    $account['iban'] = empty($account['iban']) && !empty($post['iban']) ? $post['iban'] : $account['iban'];
                    $account['bic'] = empty($account['bic']) && !empty($post['bic']) ? $post['bic'] : $account['bic'];
                    Model_Event::accountDetailsSave($account);
                }


                $message = 'New event posted';
                $link = 'https://' . $_SERVER['HTTP_HOST'] . '/event/' . $post['url'];
                $make_live = ($action == 'make_live' OR $action == 'make_live_and_tweet');

                // Post to Twitter
                if ($eventId AND $action == 'make_live_and_tweet') {
                    $tweet = new IbTwitterApi;

                    $tweet_posted = $tweet->post('statuses/update', array('status' => $message . "\n" . $link));
                    if (!isset($tweet_posted->errors) OR count($tweet_posted->errors) == 0) {
                        IbHelpers::set_message('Tweet has been posted', 'success popup_box');
                    } else {
                        IbHelpers::set_message('Error posting tweet: ' . $tweet_posted->errors[0]->message, 'danger popup_box');
                    }
                }

                // Post to Facebook
                if ($eventId AND $make_live AND Settings::instance()->get('facebook_api_access')) {
                    $fb = new IbFacebookApi();
                    $response = $fb->post_message($message . ': ' . $post['name'], $link);
                    if ($response !== false) {
                        IbHelpers::set_message('Facebook wall has been updated', 'success popup_box');
                    } else {
                        IbHelpers::set_message('Error posting to Facebook', 'danger popup_box');
                    }
                }

                // If they have made the event live, open a view which gives the URL and sharing options
                if ($eventId AND in_array($action, array('make_live', 'make_live_and_tweet'))) {
                    $this->request->redirect('/admin/events/event_live/' . $eventId);
                }

                $this->request->redirect('/admin/events/edit_event/' . $eventId);
            }
        }
	}

	public function action_ajax_toggle_event_publish()
	{
		$this->auto_render = FALSE;
		$id                = $this->request->param('id');
		$data['publish']   = $this->request->query('publish');
		$event             = new Model_Event($id);
		$event->set('publish', $data['publish']);
		echo $event->save_with_moddate() ? 1 : 0;
	}

	public function action_ajax_get_event_details()
	{
		$this->auto_render = FALSE;
		$event = Model_Event::eventLoad($this->request->param('id'));

		$event['expand_section'] = View::factory('/admin/list_events_expand')
			->set('event', $event)
			->set('skip_comments_in_beginning_of_included_view_file', TRUE)
			->render();

		echo json_encode($event);
	}

	public function action_delete_event()
	{
        if (!Auth::instance()->has_access('events_delete') && !Auth::instance()->has_access('events_delete_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $post = $this->request->post();
            if (is_numeric($post['id']) && !Auth::instance()->has_access('events_delete')) {
                $user = Auth::instance()->get_user();
                $event = Model_Event::eventLoad($post['id']);
                if($event['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }
            try {
                // Load event
                $event = new Model_Event($this->request->post('id'));
                // Set new values and save
                $event->set('publish', 0);
                $event->set('deleted', 1);
                $event->save_with_moddate();
                // Success message
                IbHelpers::set_message('Event #' . $event->id . ': "' . $event->name . '" successfully deleted',
                    'success popup_box');
            } catch (Exception $e) {
                // Problem deleting. Write error to the system logs and display notice.
                Log::instance()->add(Log::ERROR, $e->getMessage() . $e->getTraceAsString());
                IBHelpers::set_message('Error deleting event. Check the system logs for more information.', 'danger popup_box');
            }
            $this->request->redirect('/admin/events');
        }
	}

    public function action_event_status_set()
    {
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You need access to the &quot;edit events&quot; feature to perform this action.", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $post = $this->request->post();
            $user = Auth::instance()->get_user();
            if (is_numeric($post['id']) && !Auth::instance()->has_access('events_edit')) {
                $event = Model_Event::eventLoad($post['id']);
                if($event['owned_by'] != $user['id']) {
                    IbHelpers::set_message("You do not have access to the event that would be affected by this action.", 'warning popup_box');
                    $this->request->redirect('/admin');
                }
            }

            $params = array(
                'status' => $post['status'],
                'status_reason' => $post['status_reason'],
                'modified_by' => $user['id'],
                'date_modified' => date('Y-m-d H:i:s')
            );
            $params['is_onsale'] = ($post['status'] == Model_Event::EVENT_STATUS_LIVE) ? 1 : 0;

            DB::update(Model_Event::TABLE_EVENTS)->set($params)->where('id', '=', $post['id'])
                ->execute();
            if ($this->request->is_ajax()) {
                $this->auto_render = false;
                $this->response->headers('Content-Type', 'application/json; charset=utf-8');
                $event = Model_Event::eventLoad($post['id']);
                echo json_encode($event);
            } else {
                $this->request->redirect('/admin/events');
            }
        }
    }

    public function action_event_sale_end()
    {
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You need access to the &quot;edit events&quot; feature to perform this action.", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $post = $this->request->post();
            $user = Auth::instance()->get_user();
            if (is_numeric($post['id'])) {
                if (!Auth::instance()->has_access('events_edit')) {
                    $event = Model_Event::eventLoad($post['id']);
                    if ($event['owned_by'] != $user['id']) {
                        IbHelpers::set_message("You do not have access to the event that would be affected by this action.",
                            'warning popup_box');
                        $this->request->redirect('/admin');
                    }
                } else {
                    $event = Model_Event::eventLoad($post['id']);
                }

                $all_dates_sale_ended = true;
                foreach ($event['dates'] as $date) {
                    if ($date['is_onsale'] == 1 && $date['id'] != $post['date_id']) {
                        $all_dates_sale_ended = false;
                    }
                }

                if (isset($post['date_id']) && count($event['dates']) > 1 && $event['one_ticket_for_all_dates'] == 0) {
                    DB::update(Model_Event::TABLE_DATES)
                        ->set(array('is_onsale' => 0))
                        ->where('id', '=', $post['date_id'])
                        ->execute();
                }

                if ($all_dates_sale_ended) {
                    DB::update(Model_Event::TABLE_EVENTS)->set(array(
                        'status' => Model_Event::EVENT_STATUS_SALE_ENDED,
                        'is_onsale' => 0,
                        'modified_by' => $user['id'],
                        'date_modified' => date('Y-m-d H:i:s')
                    ))->where('id', '=', $post['id'])
                        ->execute();
                    session_commit();
                    $invoice = Model_Event::invoiceGenerate($event);
                    Model_Event::invoiceEmail($invoice);
                }
            }

            $this->request->redirect('/admin/events');
        }
    }

	public function action_register()
	{
		$this->request->redirect('/admin/registerevent');
	}

	public function action_coming_soon()
	{
		$this->template->body = '<div style="text-align: center;">
		<h1>Coming Soon</h1>
		</div>';
	}

	public function action_orders()
	{
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Orders', 'link' => '/admin/events/orders');

        if (!Auth::instance()->has_access('events_orders_view') || (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited'))) {
            IbHelpers::set_message("You need access to the &quot;edit events&quot; feature to perform this action.", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            Model_Event::set_paymentgw_fees();
            $user = Auth::instance()->get_user();
            $params = array('archived' => false);
            if (!Auth::instance()->has_access('events_edit')) {
                $params['owner_id'] = $user['id'];
            }
            $orders = Model_Event::ordersList($params);
            $this->template->body = View::factory('admin/list_orders');
            $this->template->body->orders = $orders;
        }
	}

    public function action_ajax_get_orders_datatable()
    {
        $this->auto_render = false;

        $can_view = Auth::instance()->has_access('events_orders_view') ;
        $can_edit = (Auth::instance()->has_access('events_edit') || Auth::instance()->has_access('events_edit_limited'));

        if ($can_view && $can_edit) {
            $params = array(
                'archived' => false,
                'filters'  => $this->request->query()
            );

            if (!Auth::instance()->has_access('events_edit')) {
                $user = Auth::instance()->get_user();
                $params['owner_id'] = $user['id'];
            }

            $orders = Model_Event::get_orders_for_datatable($params);

            echo json_encode($orders);
        }
    }

    public function action_order_details()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Orders',  'link' => '/admin/events/orders');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Details', 'link' => '#');

        if (!Auth::instance()->has_access('events_orders_view') || ((!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) || ! Auth::instance()->has_access('payments'))) {
            IbHelpers::set_message("You need access to the &quot;edit events&quot; and &quot;payments&quot; permissions to perform this action.", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $id = $this->request->param('id');
            $params = array();
            if (!Auth::instance()->has_access('events_edit')) {
                $order = Model_Event::orderLoad($id, $user['id']);
            } else {
                $order = Model_Event::orderLoad($id);
            }

			if ( ! $order)
			{
				IbHelpers::set_message(__('The selected order does not exist or you do not have access to it.'), 'warning popup_box');
				$this->request->redirect('/admin/events/orders');
			}

            if ($this->request->post('status')) {
                Model_Event::orderStatusChange($id, $this->request->post('status'), $this->request->post('status_reason'));
                $this->request->redirect('/admin/events/order_details/' . $id);
            }

            $this->template->body = View::factory('admin/view_order');
            $this->template->body->order = $order;
			$this->template->body->currencies = Model_Currency::getCurrencies(true);
			$this->template->body->full_access = Auth::instance()->has_access('events_edit');
        }
    }

    public function action_payments()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Payments',  'link' => '/admin/events/payments');

        if ((!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) || ! Auth::instance()->has_access('payments')) {
            IbHelpers::set_message("You need access to the &quot;payments&quot; and &quot;event edit&quot; permissions to access this feature", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            Model_Event::set_paymentgw_fees();
            $user = Auth::instance()->get_user();
            $params = array();
            if (!Auth::instance()->has_access('events_edit')) {
                $params['owner_id'] = $user['id'];
            }
            $payments = Model_Event::paymentsList($params);
            $this->template->body = View::factory('admin/list_payments');
            $this->template->body->payments = $payments;
        }
    }

    public function action_tickets_generate()
    {
        $this->auto_render = false;
        $id = $this->request->param('id');
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode(Model_Event::ticketsGenerate($id));
        Model_Event::eventsSoldUpdate();
    }

    public function action_ticket_qrcode()
    {
        $id = $this->request->param('id');
        $size = $this->request->query('size');
        if (!$size) {
            $size = 3;
        }
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'image/png');
        $image = Model_Event::qrcodeGenerate($id, $size);
        echo $image;
    }

    public function action_tickets()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Tickets',  'link' => '/admin/events/tickets');

        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $params = array();
            if (!Auth::instance()->has_access('events_edit')) {
                $params['owner_id'] = $user['id'];
            }
            $tickets = Model_Event::ticketsList($params);
            $this->template->body = View::factory('admin/list_tickets');
            $this->template->body->tickets = $tickets;
        }
    }

    public function action_mytickets()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'My Tickets',  'link' => '/admin/events/mytickets');

        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $params = array();
            $params['buyer_id'] = $user['id'];
            $tickets = Model_Event::ticketsList($params);
            $this->template->body = View::factory('admin/list_tickets');
            $this->template->body->tickets = $tickets;
        }
    }

	public function action_invoices()
	{
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Invoices',  'link' => '/admin/events/invoices');

        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $params = array();
            if (!Auth::instance()->has_access('events_edit')) {
                $params['owned_by'] = $user['id'];
            }


            $invoices = Model_Event::invoices($params);
            $invoiceUpdate = Auth::instance()->has_access('events_invoice_update');

            $this->template->body = View::factory('admin/list_invoices');
            $this->template->body->invoices = $invoices;
            $this->template->body->invoiceUpdate = $invoiceUpdate;
        }
	}

    public function action_statement_view()
    {
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $eventId = $this->request->query('event_id');
            $event = Model_Event::eventLoad($eventId);
            $user = Auth::instance()->get_user();
            if (!Auth::instance()->has_access('events_edit') && $event['owned_by'] != $user['id']) {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
            $this->auto_render = false;
            Model_Event::statementGenerate($event);
        }
    }

    public function action_receipt()
    {
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $orderId = $this->request->param('id');
            $order = Model_Event::orderLoad($orderId);
            $this->auto_render = false;
            $pdf = Model_Event::receiptGenerate($order);
            $this->response->headers('Content-Type', 'application/pdf');
            $this->response->headers('Content-Disposition', 'attachment; filename=receipt-' . $order['id'] . '.pdf');
            echo file_get_contents($pdf);
            unlink($pdf);
        }
    }

    public function action_invoice_generate()
    {
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $eventId = $this->request->query('event_id');
            $event = Model_Event::eventLoad($eventId);
            $user = Auth::instance()->get_user();
            if (!Auth::instance()->has_access('events_edit') && $event['owned_by'] != $user['id']) {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
            session_commit();
            $invoice = Model_Event::invoiceGenerate($event);
            Model_Event::invoiceEmail($invoice);
            $this->request->redirect('/admin/events/invoice_download?invoice_id=' . $invoice['id']);
        }
    }

    public function action_invoice_update()
    {
        if (!Auth::instance()->has_access('events_invoice_update')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $invoiceId = $this->request->param('id');
            $invoice = Model_Event::invoiceLoad($invoiceId);
            $post = $this->request->post();
            if (count($post) > 0) {
                $invoice = array('id' => $invoiceId);
                $invoice['due'] = @$post['due'] ? date::dmy_to_ymd($post['due']) : null;
                $invoice['completed'] = @$post['completed'] ? date::dmy_to_ymd($post['completed']) : null;
                Model_Event::invoiceUpdate($invoice);
                $this->request->redirect('/admin/events/invoice_update/' . $invoiceId);
            }
            $this->template->body = View::factory('admin/update_event_invoice');
            $this->template->body->invoice = $invoice;
        }
    }

    public function action_invoice_download()
    {
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $invoiceId = $this->request->query('invoice_id');
            $invoice = Model_Event::invoiceLoad($invoiceId);
            $user = Auth::instance()->get_user();
            if (!Auth::instance()->has_access('events_edit') && $invoice['event']['owned_by'] != $user['id']) {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
            if (Auth::instance()->has_access('events_orders_view')) {
                if (!$invoice['uticket_file_id']) {
                    $invoice = Model_Event::invoiceGenerate($invoice['event_id']);
                }
                Model_Files::download_file($invoice['uticket_file_id']);
            } else {
                if (!$invoice['file_id']) {
                    $invoice = Model_Event::invoiceGenerate($invoice['event_id']);
                }
                Model_Files::download_file($invoice['file_id']);
            }
        }
    }

    public function action_invoice_email()
    {
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $invoiceId = $this->request->post('invoice_id');
            $invoice = Model_Event::invoiceLoad($invoiceId);
            $user = Auth::instance()->get_user();
            if (!Auth::instance()->has_access('events_edit') && $invoice['event']['owned_by'] != $user['id']) {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
            $result = array();
            $result['success'] = Model_Event::invoiceEmail($invoice);
            $this->auto_render = false;
            $this->response->headers('Content-Type', 'application/json; charset=utf-8');
            echo json_encode($result);
        }
    }

	public function action_profile()
	{
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Profile',  'link' => '/admin/events/profile');
        self::action_coming_soon();
	}

	public function action_notifications()
	{
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Notifications',  'link' => '/admin/events/notifications');
        self::action_coming_soon();
	}

	public function action_account()
	{
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Account',  'link' => '/admin/events/account');
        self::action_coming_soon();
	}

    public function action_autocomplete_tag_list()
    {
        $tags = Model_Event::autocomplete_tag_list($this->request->query('term'));
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($tags);
    }

    public function action_autocomplete_organiser_list()
    {
        $limitUserId = null;
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            if (!Auth::instance()->has_access('events_edit')) {
                $limitUserId = $user['id'];
            }
        }
        $organisers = Model_Event::autocomplete_organiser_list($this->request->query('term'), $limitUserId);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($organisers);
    }

    public function action_geturl()
    {
        $categoryId = $this->request->post("category_id");
        $name = $this->request->post("name");
        $excludeId = $this->request->post("exclude_id");
        $url = Model_Event::calculateUrlForEvent($name, $excludeId);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode(array('url' => $url));
    }

    public function action_autocomplete_venue_list()
    {
        $limitUserId = null;
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            if (!Auth::instance()->has_access('events_edit')) {
                $limitUserId = $user['id'];
            }
        }
        $venues = Model_Event::autocomplete_venue_list($this->request->query('term'), $limitUserId);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($venues);
    }

    public function action_venue_geturl()
    {
        $venueId = $this->request->post("venue_id");
        $name = $this->request->post("name");
        $url = Model_Event::calculateUrlForVenue($name, $venueId);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode(array('url' => $url));
    }

    public function action_venue_details()
    {
        $venueId = $this->request->post("venue_id");
        $venue = Model_Event::venueLoad($venueId);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($venue);
    }

    public function action_account_details()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Account details',  'link' => '/admin/events/account_details');

        if (Auth::instance()->has_access('events_edit')) {
            $account = Model_Event::accountDetailsLoad($this->request->param('id'));
        } else {
            if (Auth::instance()->has_access('events_edit_limited')) {
                $user = Auth::instance()->get_user();
                $account = Model_Event::accountDetailsLoad($user['id']);
            } else {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
        }
        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            $account['notify_sms_on_buy_ticket'] = @$post['notify_sms_on_buy_ticket'] ? @$post['notify_sms_on_buy_ticket'] : 0;
            $account['notify_email_on_buy_ticket'] = @$post['notify_email_on_buy_ticket'] ? @$post['notify_email_on_buy_ticket'] : 0;
            $account['notify_email_on_event_enquiry'] = @$post['notify_email_on_event_enquiry'] ? @$post['notify_email_on_event_enquiry'] : 0;
            $account['use_stripe_connect'] = @$post['use_stripe_connect'] ? @$post['use_stripe_connect'] : 0;
            $account['iban'] = @$post['iban'];
            $account['bic'] = @$post['bic'];
            if (Auth::instance()->has_access('events_edit')) {
                $account['owner_id'] = $post['owner_id'];
                $account['status'] = $post['status'];
                if ($post['commission_type'] != '' && is_numeric($post['commission_amount']) && $post['commission_currency']) {
                    $account['commission_type'] = $post['commission_type'];
                    $account['commission_amount'] = $post['commission_amount'];
                    $account['commission_currency'] = $post['commission_currency'];
                } else {
                    $account['commission_type'] = null;
                    $account['commission_amount'] = null;
                    $account['commission_currency'] = null;
                }
            }
            Model_Event::accountDetailsSave($account);
            $this->request->redirect('/admin/events/account_details/' . $account['owner_id']);
        }
        $this->template->body = View::factory('/admin/event_account_details');
        $this->template->body->account = $account;
    }


    public function action_stripe_disconnect()
    {
        $eventIdToRedirect = Session::instance()->get('stripe_connect_after_load_event_id');
        if ($eventIdToRedirect) {
            Session::instance()->set('stripe_connect_after_load_event_id', null);
            $this->request->redirect('/admin/events/edit_event/' . $eventIdToRedirect);
        }

        if (Auth::instance()->has_access('events_edit') && $this->request->param('id')) {
            $account = Model_Event::accountDetailsLoad($this->request->param('id'));
        } else {
            if (Auth::instance()->has_access('events_edit_limited') || !$this->request->param('id')) {
                $user = Auth::instance()->get_user();
                $account = Model_Event::accountDetailsLoad($user['id']);
            } else {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
        }

        require_once APPPATH . '/vendor/stripe/lib/Stripe.php';

        $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
        $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
        $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
        Stripe::setApiKey($stripe['secret_key']);

        $curl = curl_init('https://connect.stripe.com/oauth/deauthorize');
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($curl, CURLOPT_USERPWD, $stripe['secret_key']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('client_id' => Settings::instance()->get('stripe_client_id'), 'stripe_user_id' => $account['stripe_auth']['stripe_user_id']));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);
        if (@$result['stripe_user_id'] == $account['stripe_auth']['stripe_user_id'] || @$result['error'] == 'invalid_client') {
            $account['stripe_auth'] = '';
            $account['use_stripe_connect'] = 0;
            Model_Event::accountDetailsSave($account);
        }
        $this->request->redirect('/admin/profile/edit?section=contact');
    }

    public function action_stripe_connect_start()
    {
        $stripeId = Settings::instance()->get('stripe_client_id');
        $urlParams = array(
            'response_type' => 'code',
            'client_id' => $stripeId,
            'scope' => 'read_write',
            'redirect_uri' => URL::site('/admin/events/stripe_connect_end')
        );
        $url = 'https://connect.stripe.com/oauth/authorize?' . http_build_query($urlParams);
        $this->request->redirect($url);
    }

    public function action_stripe_connect_end()
    {
        $error = $this->request->query('error');
        if ($error != '') {
            IBHelpers::set_message('Error authorizing stripe(' . $error . ')', 'error popup_box');
        } else {
            $scope = $this->request->query('scope');
            $code = $this->request->query('code');
            $stripeId = Settings::instance()->get('stripe_client_id');
            $stripeSecret = Settings::instance()->get('stripe_test_mode') == 'TRUE' ?
                Settings::instance()->get('stripe_test_private_key') :
                Settings::instance()->get('stripe_private_key');

            if ($code) {
                $token_request_body = array(
                    'grant_type' => 'authorization_code',
                    'client_id' => $stripeId,
                    'code' => $code,
                    'client_secret' => trim($stripeSecret)
                );

                try {
                    $req = curl_init('https://connect.stripe.com/oauth/token');
                    if (!defined('CURL_SSLVERSION_TLSv1_2')) {
                        define('CURL_SSLVERSION_TLSv1_2', 6);
                    }
                    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($req, CURLOPT_POST, true);
                    curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                    curl_setopt($req, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

                    $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
                    $resp = json_decode(curl_exec($req), true);
                    curl_close($req);

                    if (isset($resp['access_token'])) {
                        $user = Auth::instance()->get_user();
                        Model_Event::accountDetailsSave(array(
                            'owner_id' => $user['id'],
                            'use_stripe_connect' => 1,
                            'stripe_auth' => json_encode($resp)
                        ));
                    }
                } catch (Exception $exc) {
                    IBHelpers::set_message('Error authorizing stripe(' . $exc->getMessage() . ')', 'error  popup_box');
                }
            } else {
                IBHelpers::set_message('Error authorizing stripe', 'error popup_box');
            }
        }

        $eventIdToRedirect = Session::instance()->get('stripe_connect_after_load_event_id');
        if ($eventIdToRedirect) {
            Session::instance()->set('stripe_connect_after_load_event_id', null);
            $this->request->redirect('/admin/events/edit_event/' . $eventIdToRedirect);
        } else {
            $this->request->redirect('/admin/profile/edit?section=contact');
        }
    }

    public function action_lookups ()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Lookups',  'link' => '/admin/events/lookups');

        $user = Auth::instance()->get_user();
        $lookups = Model_Lookup::get_lookup_by_id($user['id']);
        $lookupsUpdate = Auth::instance()->has_access('lookups');
        $this->template->body = View::factory('admin/list_lookups');
        $this->template->body->lookups = $lookups;
        $this->template->body->lookupsUpdate = $lookupsUpdate;
    }

    public function action_ticket()
    {
        session_commit();
        $this->auto_render = false;

		if ( ! Auth::instance()->has_access('events_edit') AND ! Auth::instance()->has_access('events_edit_limited'))
		{
			IbHelpers::set_message("You do not have access to this feature.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

        $ticket_id = $this->request->query('ticket_id');
        $order_id = $this->request->query('order_id');
        $action = $this->request->query('action');
        $order = Model_Event::orderLoad($order_id);
		$user = Auth::instance()->get_user();
		$owner_id = isset($order['items'][0]) ? $order['items'][0]['event_owner_id'] : '';


		if ( ! Auth::instance()->has_access('events_edit') AND ! in_array($user['id'], array($order['buyer_id'], $owner_id)))
		{
			IbHelpers::set_message("You do not have access to this ticket.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

        $pdfs = Model_Event::ticketPDFGenerate($order, $ticket_id);
        if ($action == 'print' || $action == 'download') {
            if (count($pdfs) > 1) {
                $zip = new ZipArchive();
                $filename = Kohana::$cache_dir . "/tickets-" . $order['id'] . ".zip";

                if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
                    exit("cannot open <$filename>\n");
                }

                foreach ($pdfs as $i => $pdf) {
                    $zip->addFile($pdf['file'], 'ticket-' . $pdf['code'] . '-' . date('YmdHi', strtotime($pdf['date'])) . '.pdf');
                }
                $zip->close();
                $this->response->headers('Content-type', 'application/zip');
                $this->response->headers('Content-Disposition', 'attachment; filename=order' . $order_id . '.zip');
                echo file_get_contents($filename);
                unlink($filename);
            } else {
                $pdf = current($pdfs);
                $this->response->headers('Content-type', 'application/pdf');
                if ($action == 'download') {
                    $this->response->headers('Content-Disposition',
                        'attachment; filename=ticket-' . ($ticket_id ? $pdf['code'] . '-' . date('YmdHi', strtotime($pdf['date'])) : $order['id']) . '.pdf');
                }
                $this->response->headers('Content-type', 'application/pdf');
                echo file_get_contents($pdf['file']);
            }
        }
        if ($action == 'email') {
            $attachments = array();
            foreach ($pdfs as $i => $pdf) {
                $attachments[] = array(
                    'path' => $pdf['file'],
                    'name' => 'ticket-' . $pdf['code'] . '-' . date('YmdHi', strtotime($pdf['date'])) . '.pdf'
                );
            }
            $recipients = array(array('target_type' => 'CMS_USER', 'target' => $order['buyer']['id']));
            if ($this->request->post('recipient') != '') {
                $recipients = array(array('target_type' => 'EMAIL', 'target' => $this->request->post('recipient')));
            }
            if ($this->request->post('message') != '') {
                $body = array('content' => $this->request->post('message'), 'attachments' => $attachments);
            } else {
                $body = array('attachments' => $attachments);
            }
            $message = new Model_Messaging();
            $result = $message->send_template(
                'event-ticket',
                $body,
                null,
                $recipients
            );
            $this->response->headers('Content-Type', 'application/json; charset=utf-8');
            echo json_encode(array('result' => $result));
        }

        /*clean up tmp files*/
        foreach ($pdfs as $pdf) {
            unlink($pdf['file']);
        }
    }

    public function action_order_archive()
    {
        $orderId = $this->request->post('order_id');
        $archive = $this->request->post('archive');

		if ( ! Auth::instance()->has_access('events_edit')) // AND ! Auth::instance()->has_access('events_edit_limited'))
		{
			IbHelpers::set_message("You do not have access to this feature.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

		/*
		$order = Model_Event::orderLoad($orderId);
		$user = Auth::instance()->get_user();
		$owner_id = isset($order['items'][0]) ? $order['items'][0]['event_owner_id'] : '';

		if ( ! Auth::instance()->has_access('events_edit') AND ! in_array($user['id'], array($order['buyer_id'], $owner_id)))
		{
			IbHelpers::set_message("You do not have access to this ticket.", 'warning popup_box');
			$this->request->redirect('/admin');
		}
		*/

        Model_Event::orderSetArchived($orderId, $archive == 1 ? date('Y-m-d H:i:s') : null);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode(array('result' => true));
    }

	// Get a list of attendees for a given event and download them in CSV format
	public function action_download_attendee_csv()
	{
		if ( ! Auth::instance()->has_access('events_edit') AND ! Auth::instance()->has_access('events_edit_limited'))
		{
			IbHelpers::set_message("You do not have access to this feature.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

		$id    = $this->request->post('id');
        $filter = $this->request->post('csv_filter');
        $date_id = $this->request->post('date_id');
        if ($date_id) {
            $filter['date_id'] = $date_id;
        }
        $event = new Model_Event($id);
		$user  = Auth::instance()->get_user();

		if ( ! Auth::instance()->has_access('events_edit') AND $event->owned_by != $user['id'])
		{
			IbHelpers::set_message("You do not have access to the event you are performing this action on.", 'warning popup_box');
			$this->request->redirect('/admin');
		}


        $this->auto_render = FALSE;

        $order_by_lastname = $this->request->post('order_by_lastname');
		$attendees = $event->get_attendees('csv', $order_by_lastname, $this->request->post('csv_fields'), $filter);

        //Format Order No. This probably should be refactored as separate column in orders table
        if($attendees) foreach($attendees as $key => $attendee){
            if(!empty($attendee['Order No.'])) {
                $attendees[$key]['Order No.'] = str_pad($attendee['Order No.'], 8, '0', STR_PAD_LEFT);
            }
        }
		ExportCsv::export_report_data_array($this->response, $attendees, $event->name.'-attendees');
	}

	// Email everyone attending an event
	public function action_email_attendees()
	{
		if ( ! Auth::instance()->has_access('events_edit') AND ! Auth::instance()->has_access('events_edit_limited'))
		{
			IbHelpers::set_message("You do not have access to the &quot;email attendees&quot; feature.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

		$id    = $this->request->post('event_id');
        $date_id = $this->request->post('date_id');
		$event = new Model_Event($id);
		$user  = Auth::instance()->get_user();

		if ( ! Auth::instance()->has_access('events_edit') AND $event->owned_by != $user['id'])
		{
			IbHelpers::set_message("You do not have permission to use the &quot;email attendees&quot; feature, for other sellers&#39; events", 'warning popup_box');
			$this->request->redirect('/admin');
		}

		$this->auto_render = FALSE;
        $filters = array();
        if ($date_id) {
            $filters['date_id'] = $date_id;
        }
		$attendees         = $event->get_attendees(null, null, null, $filters);
		$messaging         = new Model_Messaging;
		$bcc               = array();
        $filter_order_ids  = false;

        if (is_array($this->request->post('order_id'))){
            $filter_order_ids = $this->request->post('order_id');
        }


		foreach ($attendees as $attendee) {
            if ($filter_order_ids === false || in_array($attendee['order_id'], $filter_order_ids)) {
                $bcc[] = array('target_type' => 'EMAIL', 'target' => $attendee['email'], 'x_details' => 'bcc');
            }
		}

		$sent = $messaging->send_template(
			'email-event-attendees', // template
			$this->request->post('message'), // message
			null,
			$bcc, // extra targets
			array(),
			$event->name.': '.$this->request->post('subject'), // subject
			$user['email'] // sender
		);

		if ($sent) {
			IbHelpers::set_message('An email has been sent to ' . ($filter_order_ids ? 'selected' : 'all') . ' attendees of &quot;'.$event->name.'&quot;.', 'success popup_box');
		} else {
			IbHelpers::set_message('Error sending email. Please contact an administrator, if this problem persists.', 'danger popup_box');
		}

        if ($this->request->post('redirect') == 'event_details') {
            $this->request->redirect('/admin/events/edit_event/' . $id);
        } else {
            $this->request->redirect('/admin/events');
        }
	}

	public function action_ajax_get_event()
	{
		$this->auto_render = FALSE;
		$event             = new Model_Event($this->request->param('id'));
		$data              = array();
		$data['event']     = $event->as_array();
		$data['attendees'] = $event->get_attendees();
		$data['attendee_html'] = View::factory('admin/attendee_bcc_list')->set('attendees', $data['attendees'])->render();

		echo json_encode($data);
	}


	// AJAX function for generating sublist in the plugins' dropdown
	public function action_ajax_get_submenu($data_only = false)
	{
		$return = array(
			'link' => '',
			'items' => array(
				array('id' => 'mytickets',  'title' => 'My Tickets', 'icon' => 'flaticon-ticket', 'icon_svg' => 'my-tickets'),
				array('id' => 'invoices', 'title' => 'Invoices', 'icon' => 'flaticon-invoice', 'icon_svg' => 'invoices'),
			)
		);

        if(Auth::instance()->has_access('events_orders_view')){
            $return['items'][] = array('id' => 'orders',  'title' => 'Orders', 'icon' => 'flaticon-padnote', 'icon_svg' => 'orders');
        }

        if ($data_only) {
            return $return;
        } else {
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $this->response->body(json_encode($return));
        }
	}

	public function action_ajax_delete_discount()
    {
        $this->auto_render = FALSE;
        Model_Event::deleteDiscount($this->request->post('id'));
    }

    public function action_payment_refund()
    {
		if ( ! Auth::instance()->has_access('events_edit'))
		{
			IbHelpers::set_message("You need access to the &quot;edit events&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

        $paymentId = $this->request->post('id');
        $reason = $this->request->post('reason');
        $amount = $this->request->post('amount');
        if (is_numeric($paymentId)) {
            $order = Model_Event::paymentRefund($paymentId, $amount, $reason);
            if ($order) {
                $this->request->redirect('/admin/events/order_details/' . $order['id']);
                return;
            }
        }

        $this->request->redirect('/admin/events/orders');
    }

    public function action_ticket_details()
    {
        $id = $this->request->param('id');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Tickets',  'link' => '/admin/events/tickets');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Details',  'link' => '/admin/events/ticket_details?id='.$id);

        $ticket = Model_Event::ticketLoad($id);
        $user = Auth::instance()->get_user();
        if (@$user['id'] != $ticket['buyer_id'] && @$user['id'] != $ticket['seller_id']) {
            $this->request->redirect('/events.html');
        } else {
            $allowToEnterStatus = (($user['id'] == $ticket['seller_id'] && Auth::instance()->has_access('events_edit_limited')) || Auth::instance()->has_access('events_edit') );
            $post = $this->request->post();
            if (@$post['action'] == 'update' && $allowToEnterStatus) {
                DB::update(Model_Event::TABLE_TICKETS)
                    ->set(array(
                        'checked_by' => $user['id'],
                        'checked' => @$post['checked'] ? 1 : 0,
                        'checked_note' => $post['checked_note']
                    ))->execute();
                $this->request->redirect('/admin/events/ticket_details/' . $id);
            }
            $view = View::factory('/admin/view_ticket');
            $view->ticket = $ticket;
            $view->url = $this->request->url();
            $view->allowToEnterStatus = $allowToEnterStatus;
            $this->template->body = $view;
        }
    }

    public function action_checkin()
    {
        $post = $this->request->post();
        $this->response->headers('cache-control', 'private, max-age=0, no-cache');
        if (!Auth::instance()->has_access('events_edit') && !Auth::instance()->has_access('events_edit_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $event_id = $post['event_id'];
            $event = Model_Event::eventLoad($this->request->param('id'));
            if (!Auth::instance()->has_access('events_edit') && $event['owned_by'] != $user['id']) {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
        }
        try {
            Database::instance()->begin();

            $now = date::now();
            $tickets = $this->request->post('ticket');
            $result = array();
            foreach ($tickets as $ticket_id => $ticket) {
                $ticket_details = Model_Event::ticketLoad($ticket_id);
                $allowToEnterStatus = ($this->user['id'] == $ticket_details['seller_id'] || Auth::instance()->has_access('events_edit'));
                $post = $this->request->post();
                if ($allowToEnterStatus) {
                    if ($ticket_details['checked']) {
                        $result[] = array(
                            'id' => $ticket['id'],
                            'checked' => 2,
                            'date' => $ticket_details['checked']
                        );
                    } else {
                        $check = array(
                            'checked_by' => $this->user['id'],
                            'checked' => @$ticket['checked'] ? $now : null,
                        );
                        if (@$ticket['note']) {
                            $check['checked_note'] = $ticket['note'];
                        }
                        DB::update(Model_Event::TABLE_TICKETS)->set($check)->where('id', '=', $ticket_id)->execute();
                        $result[] = array(
                            'id' => $ticket_id,
                            'checked' => 1,
                            'date' => $now
                        );
                    }
                } else {
                    $result[] = array(
                        'id' => $ticket['id'],
                        'checked' => 0
                    );
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
        }

        $this->request->redirect('/admin/events/edit_event/' . $event_id);
    }
}
