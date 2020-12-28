<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Frontend_Events extends Controller
{
    public function before()
    {
        $this->progress_links = array(
            'home'     => array('title' => __('Home'),      'link' => '/'),
            'results'  => array('title' => __('Results'),   'link' => Model_Event::get_search_url(true)),
            'details'  => array('title' => __('Details'),   'link' => false),
            'checkout' => array('title' => __('Checkout'),  'link' => '/checkout'),
            'title'    => array('title' => __('Thank you'), 'link' => false),
        );

        parent::before();
    }

    public function action_index()
    {
        Session::instance()->set('last_event_search_params', $this->request->query());

        $searchParams = array();
		$searchParams['keyword']    = $this->request->query('term');
        $searchParams['county_ids'] = $this->request->query('county_id');
        $searchParams['event_id']   = $this->request->query('event_id');

		// Search a single date
		if ($this->request->query('date')) {
			// Convert d/m/Y to Y-m-d
			$date = implode('-', array_reverse(explode('/', $this->request->query('date'))));
			// Use the start and end of the day as the date range
			$searchParams['after']  = $date.' 00:00:00';
			$searchParams['before'] = $date.' 23:59:59';
		}

		// Search a date range
		if ($this->request->query('date_after')) {
			$date = implode('-', array_reverse(explode('/', $this->request->query('date_after'))));
			$searchParams['after']  = $date.' 00:00:00';
			$searchParams['events_only'] = TRUE;
		}
		if ($this->request->query('date_before')) {
			$date = implode('-', array_reverse(explode('/', $this->request->query('date_before'))));
			$searchParams['before'] = $date.' 23:59:59';
			$searchParams['events_only'] = TRUE;
		}
        if ($this->request->query('category_id')) {
            $searchParams['category_id'] = $this->request->query('category_id');
			$searchParams['events_only'] = TRUE;
        }

        $tags = trim($this->request->query('tags'));
        if ($tags != '') {
            $searchParams['tags'] = preg_split('/\s*,\s*/', $tags);
            array_walk(
                $searchParams['tags'],
                function($tag){
                    return trim($tag);
                }
            );
        }
        if (is_array($this->request->query('tag'))) {
            foreach ($this->request->query('tag') as $tag) {
                $searchParams['tags'][] = trim($tag);
            }
        }

        $search = Model_Plugin::global_search($searchParams);

        $total = DB::select(DB::expr('FOUND_ROWS() as total'))->execute()->get('total');
        $categories = Model_Lookup::lookupList('Event Category');

        $view = View::factory('/frontend/list_events');
		$view->page_data = array(
			'seo_description' => '',
			'seo_keywords' => '',
			'title' => __('Search events'),
			'content' => '',
			'layout' => 'content',
			'banner_photo' => '',
			'theme_home_page' => '',
			'name_tag' => ''
		);
		$view->page_data['common_head_data'] = View::factory('common_head_data', array('page_data' => $view->page_data));

        if ($this->request->query('event_id')) {
            $view->event_object = ORM::factory('Event')
                ->where('id', '=', $this->request->query('event_id'))
                ->find_published();
        }

        $view->alerts           = IbHelpers::get_messages();
        $view->breadcrumb_title = __('Search events');
        $view->categories       = $categories;
        $view->filter_counties  = Model_Event_Venue::get_active_counties();
        $view->filter_event_categories = Model_Event::getCategories();
        $view->progress_links   = $this->progress_links;
        $view->progress_links['results']['active'] = true;
        $view->search           = $search;
        $view->search_results   = $search['data'];
        $view->theme            = Model_Engine_Theme::get_current_theme();
        $view->total            = $total;

        return $view;
    }

    public function action_event_image()
    {
        session_commit();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'image/png');

        $eventId = $this->request->param('id');
        $mediaId = DB::select('image_media_id')->from(Model_Event::TABLE_EVENTS)->where('id', '=', $eventId)->execute()->get('image_media_id');
        $path = Model_Media::get_path_to_id($mediaId);
        if ($path) {
            $this->request->redirect($path);
        } else {
            $img = imagecreatetruecolor(1, 1);
            imagepng($img);
            imagedestroy($img);
        }
    }

    public function action_organiser_image()
    {
        session_commit();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'image/png');
        $id = $this->request->param('id');
        $mediaId = DB::select('profile_media_id')->from(Model_Event::TABLE_ORGANIZERS)->where('contact_id', '=', $id)->execute()->get('profile_media_id');
        $path = Model_Media::get_path_to_id($mediaId);
        if ($path) {
            $this->request->redirect($path);
        } else {
			$path = URL::site();
			if (Kohana::$config->load('config')->project_media_folder != '')
			{
				$path .= 'shared_media/'.Kohana::$config->load('config')->project_media_folder;
			}
			$path .= '/media/photos/events/no_image_available.png';

			$this->request->redirect($path);
        }
    }

    public function action_organiser_banner()
    {
        session_commit();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'image/png');
        $id = $this->request->param('id');
        $mediaId = DB::select('banner_media_id')->from(Model_Event::TABLE_ORGANIZERS)->where('contact_id', '=', $id)->execute()->get('banner_media_id');
        $path = Model_Media::get_path_to_id($mediaId);
        if ($path) {
            $this->request->redirect($path);
        } else {
            $img = imagecreatetruecolor(1, 1);
            imagepng($img);
            imagedestroy($img);
        }
    }
    
    public function action_venue_image()
    {
        session_commit();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'image/png');
        $id = $this->request->param('id');
        $eventId = $this->request->param('id');
        $mediaId = DB::select('image_media_id')->from(Model_Event::TABLE_EVENTS)->where('id', '=', $eventId)->execute()->get('image_media_id');
        $path = Model_Media::get_path_to_id($mediaId);
        if ($path) {
            $this->request->redirect($path);
        } else {
            $img = imagecreatetruecolor(1, 1);
            imagepng($img);
            imagedestroy($img);
        }
    }
    public function action_event()
    {
        $params = $this->request->param();
        $eventUrl = str_replace('.html', '', $params['item_category']);
        Model_Event::orderClearPending();
        $event = Model_Event::eventLoadFromUrl($eventUrl, null);
        $event_object = new Model_Event($event['id']);

        $xRobotTag = array($event['x_robots_tag']);

        if ($event['is_public'] != 1) {
            array_merge($xRobotTag, array('nofollow', 'noindex'));
        }

        $this->response->headers('X-Robots-Tag', implode(', ', $xRobotTag));

        $account = Model_Event::accountDetailsLoad($event['owned_by']);

        if (empty($account['id'])){
            $event = NULL;
        }

        $title = isset($event['name']) ? $event['name'] : __('Event not found');

        $commission = Model_Event::commissionGet($event, $event['owned_by']);
        $view = View::factory('/frontend/view_event');
        $view->page_data = array(
            'seo_description' => @$event['seo_description'],
            'seo_keywords' => @$event['seo_keywords'],
            'title' => $title,
            'content' => '',
            'footer'  => @$event['footer'],
            'layout' => 'event',
            'banner_photo' => '',
            'theme_home_page' => '',
            'name_tag' => ''
        );
		$view->page_data['common_head_data'] = View::factory('common_head_data', array(
            'page_data'  => $view->page_data,
            'event'      => $event,
            'item_owner' => new Model_User($event['owned_by'])
        ));

        $view->progress_links = $this->progress_links;
        $view->progress_links['details']['active'] = true;

        $view->account             = $account;
        $view->banner_items        = array($event_object->get_banner_data());
        $view->breadcrumb_prev_url = Model_Event::get_search_url(true);
        $view->breadcrumb_title    = $title;
        $view->commission          = $commission;
        $view->event               = $event;
        $view->event_object        = $event_object;
        $view->has_mobile_banner   = false;
        $view->mobile_footer_menu  = false;
        $view->preview             = $this->request->query('preview') == 'yes';
        $view->theme               = Model_Engine_Theme::get_current_theme();

        return $view;
    }

    public function action_venue()
    {
        $params = $this->request->param();
        $venueUrl = str_replace('.html', '', $this->request->uri());
        $venueUrl = str_replace('venue/', '', $venueUrl);
        $venue = Model_Event::getVenue($venueUrl);
        $events = array();
        $search = null;
        $total_events = 0;
        $results_per_page = 10;
        if ($venue) {
            $search = Model_Event::get_for_global_search(array(
                'venue_id'   => $venue['id'],
                'order_by'   => 'dates.starts',
                'direction'  => 'asc',
                'whole_site' => false
            ));

            $events       = $search['data'];
            $total_events = $search['total_count'];
        }

        $title = isset($venue['name']) ? $venue['name'] : __('Venue not found');

        $view = View::factory('/frontend/view_venue');
        $view->page_data = array(
            'seo_description' => '',
            'seo_keywords' => '',
            'title' => $title,
            'content' => '',
            'layout' => 'content',
            'banner_photo' => '',
            'theme_home_page' => '',
            'name_tag' => ''
        );

		$view->page_data['common_head_data'] = View::factory('common_head_data', array('page_data' => $view->page_data));

        $view->breadcrumb_title = $title;
        $view->events = $events;
        $view->results_per_page = $results_per_page;
        $view->search = $search;
        $view->theme = Model_Engine_Theme::get_current_theme();
        $view->total_events = $total_events;
        $view->venue = $venue;
        $view->venue_object = New Model_Event_Venue(isset($venue['id']) ? $venue['id'] : null);

        return $view;
    }

    public function action_organiser()
    {
        $params = $this->request->param();
        $organiserUrl = str_replace('.html', '', $params['item_category']);
        $organiser = Model_Event::getOrganiser($organiserUrl);
        $events = array();
        $search = null;
        $total_events = 0;
        $results_per_page = 10;
        $title =  isset($organiser['first_name']) ? trim($organiser['first_name'].' '.$organiser['last_name']) : __('Organiser not found');
        if ($organiser) {
            $search = Model_Event::get_for_global_search(array(
                'contact_id' => $organiser['contact_id'],
                'direction'  => 'asc',
                'whole_site' => false
            ));
            $events = $search['data'];
            $total_events = $search['total_count'];
        }
        $view = View::factory('/frontend/view_organiser');
        $view->page_data = array(
            'seo_description' => '',
            'seo_keywords' => '',
            'title' => $title,
            'content' => '',
            'layout' => 'content',
            'banner_photo' => '',
            'theme_home_page' => '',
            'name_tag' => ''
        );
        $view->breadcrumb_title = $title;
		$view->page_data['common_head_data'] = View::factory('common_head_data', array('page_data' => $view->page_data));
		$view->organiser = $organiser;
        $view->organiser_object = new Model_Event_Organizer($organiser['id']);
        $view->events = $events;
        $view->search = $search;
        $view->results_per_page = $results_per_page;
        $view->total_events = $total_events;
        $view->theme = Model_Engine_Theme::get_current_theme();

        return $view;
    }

    public function action_myevents()
    {
        if (!Auth::instance()->has_access('events_index') && !Auth::instance()->has_access('events_index_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning');
            $this->request->redirect('/admin');
        } else {
            $user = Auth::instance()->get_user();
            $searchParams = array();
            $searchParams['owned_by'] = $user['id'];

            $events = Model_Event::search($searchParams);
            $view = View::factory('/frontend/list_myevents')
                ->set('events', $events)
                ->set('theme', Model_Engine_Theme::get_current_theme());
            return $view;
        }
    }

    public function action_edit_myevent()
    {

    }

    public function action_ajax_get_event_counties()
    {
        $this->auto_render = false;
        $id = $this->request->query('id');
        $counties = Model_Event_Venue::get_active_counties(array('event_id' => $id));

        echo json_encode(array_values($counties));
    }


    public function action_ajax_get_more_events()
    {
        $this->auto_render = false;

        $type   = $this->request->query('type');
        $id     = $this->request->query('id');
        $offset = $this->request->query('offset');

        $results_per_page = 10;
        $valid_types      = array('venue', 'contact');
        $return           = array('found' => 0, 'html' => '', 'remaining' => 0);

        if ($id && in_array($type, $valid_types)) {
            $filters = array(
                $type.'_id'  => (int) $id,
                'after'      => date('Y-m-d H:i:s'),
                'publish'    => 1,
                'is_public'  => 1,
                'order_by'   => 'dates.starts',
                'direction'  => 'asc',
                'limit'      => $results_per_page,
                'offset'     => $offset
            );
            $events = Model_Event::search($filters);
            $total_found = Model_Event::getSearchTotal();

            $html = '';
            foreach ($events as $event) {
                $html .= View::factory('frontend/snippets/event_widget')->set('event', $event)->render();
            }

            $return = array(
                'found'     => count($events),
                'html'      => $html,
                'remaining' => $total_found - $offset - count($events)
            );
        }

        echo json_encode($return);
    }

    public function action_checkout()
    {
        $this->response->headers(array(
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Robots-Tag' => 'nofollow, noindex'
        ));

        $session_data = Session::instance()->get('event_checkout_post_data');
        $post = is_array($session_data) ? $_POST + $session_data : $_POST;
        $partial_id = is_numeric(@$_REQUEST['partial_id']) ? @$_REQUEST['partial_id'] : null;
        $partial_id_hash = @$_REQUEST['url_hash'];

        $partial_payment = null;
        if ($partial_id) {
            $partial_payment = Model_Event::get_order_from_partial_payment_id($partial_id, $partial_id_hash);
            $redirect_partial_payment = false;
            if (!$partial_payment && @$_REQUEST['order_id']) { // partial payment id is no longer valid, can be changed due to new added payers or over paid
                $redirect_partial_payment = true;
            }
            if (@$partial_payment['partial_payment']['payment_id']) { // a payment has already been made for this link. try next unpaid
                $redirect_partial_payment = true;
            }
            if ($redirect_partial_payment) {
                $order_redirect = Model_Event::orderLoad($_REQUEST['order_id']);
                if ($order_redirect) {
                    foreach ($order_redirect['partialpayments'] as $partialpayment) {

                        if ($partialpayment['payment_id'] == null) {
                            $this->request->redirect('/checkout.html?partial_id=' . $partialpayment['id'] . '&url_hash=' . $partialpayment['url_hash']);
                            exit;
                        }
                    }
                }
                $this->request->redirect('/');
            }
        }


        if ($partial_payment) {
            $eventId = $partial_payment['order']['items'][0]['event_id'];
            $items = array();
            foreach ($partial_payment['order']['items'] as $item) {
                $idates = array();
                foreach ($partial_payment['order']['idates'] as $idate) {
                    if ($idate['order_item_id'] == $item['id']) {
                        $idates[] = $idate['date_id'];
                    }
                }
                $items[] = array(
                    'ticket_type_id' => $item['ticket_type_id'],
                    'quantity' => $item['quantity'],
                    'dates' => $idates
                );
            }
        } else {
            $eventId = isset($post['event_id']) ? $post['event_id'] : '';
            if (isset($post['item'])) {
                foreach ($post['item'] as $i => $item) {
                    if (!@$item['quantity']) {
                        unset ($post['item'][$i]);
                    }
                }
            }
            $items = isset($post['item']) ? $post['item'] : '';
        }
        $discountCode = isset($post['discount_code']) ? $post['discount_code'] : '';
        $event        = Model_Event::eventLoad($eventId);
        $event_object = new Model_Event($event['id']);

        if (!$event) {
            IbHelpers::set_message("No such event", 'warning');
            $this->request->redirect('/events.html');
        }

        if (!empty($_POST['event_id'])) {
            $session_instance = Session::instance();
            $session_instance->set('event_checkout_post_data', $_POST);
        }

        $countdown_seconds = 0;
        if ( ! empty($event['count_down_seconds'])) {
            $countdown_seconds = $event['count_down_seconds'];
        }

        if (empty($countdown_seconds)) {
            $countdown_setting = Settings::instance()->get('events_checkout_countdown');
            $countdown_seconds = Model_Event::hhmmss_to_seconds($countdown_setting);
        }

        $order      = Model_Event::orderCalculate($event, $items, $discountCode, $partial_id == null);
        $currencies = Model_Currency::getCurrencies(true);
        $currency   = isset($order['currency']) ? $currencies[$order['currency']]['symbol'] : 'EUR';
        $subscribe_preference = new Model_Preferences;
        $cards = array();

        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->auto_render = false;
            $this->response->headers('Content-Type', 'application/json; charset=utf-8');
            echo json_encode(array('order' => $order));
        } else {
            $view = View::factory('/frontend/buy_event');
            $view->page_data = array(
                'seo_description' => '',
                'seo_keywords' => '',
                'title' => __('Confirm Booking'),
                'content' => '',
                'layout' => 'content',
                'banner_photo' => '',
                'theme_home_page' => '',
                'name_tag' => ''
            );
            $view->page_data['common_head_data'] = View::factory('common_head_data', array(
                'page_data'  => $view->page_data,
                'event'      => $event,
                'item_owner' => new Model_User($event['owned_by']),
                'event_type' => 'InitiateCheckout' // In this context, "event" refers to the action being performed
            ));

            $view->progress_links    = $this->progress_links;
            $view->progress_links['details']['link'] = $event_object->get_url();
            $view->progress_links['checkout']['active'] = true;

            // $view->banner_items        = array($event_object->get_banner_data());
            $view->breadcrumb_prev_url = $event_object->get_url();
            $view->checkout_error      = isset($order['error']) ? $order['error'] : null;
            $view->currency            = $currency;
            $view->countdown_seconds   = $countdown_seconds;
            $view->event               = $event;
            $view->event_object        = $event_object;
            $view->order               = $order;
            $view->partial_payment     = $partial_payment;
            $view->price_breakdown     = $order;
            $view->fullpayment_price_breakdown = $order;
			$view->success_redirect    = Model_Payments::get_thank_you_page() . (@$partial_payment == null && $event['enable_multiple_payers'] == 'YES' ? '?invite_friends=1' : '');
            $view->theme               = Model_Engine_Theme::get_current_theme();
            $view->subscribe_preference = $subscribe_preference->load(array('stub' => 'marketing_updates'))->get(true);
            $view->cards               = $cards;

            foreach ($items as $item) {
                foreach ($event['ticket_types'] as $ticket_type) {
                    if ($item['ticket_type_id'] == $ticket_type['id'] && @$ticket_type['paymentplan'][0]) {
                        $commission = Model_Event::commissionGet($event, $event['owned_by']);
                        $paymentplan = Model_Event::calculate_payment_plan(
                            $order['subtotal'],
                            $ticket_type['paymentplan'],
                            $commission,
                            $order['vat_rate']
                        );

                        $view->price_breakdown['total'] = 0;
                        $view->price_breakdown['subtotal'] = 0;
                        $view->price_breakdown['vat'] = 0;
                        $view->price_breakdown['commission'] = 0;
                        foreach ($paymentplan as $ppayment) {
                            $view->price_breakdown['total'] += $ppayment['total'];
                            $view->price_breakdown['subtotal'] += $ppayment['payment_amount'];
                            $view->price_breakdown['vat'] += $ppayment['vat'];
                            $view->price_breakdown['commission'] += $ppayment['fee'];
                        }
                        $view->paymentplan = $paymentplan;
                        $deposit_breakdown = $paymentplan[0];
                        $view->deposit_breakdown = $deposit_breakdown;
                        //header('content-type: text/plain');print_r($paymentplan);print_r($view->price_breakdown);print_r($deposit_breakdown);exit;
                    }
                }
            }
            $user = Auth::instance()->get_user();
            if(!empty($user['id'])){
                $view->user = $user;
                $view->checkoutDetails = Model_Event::checkoutDetailsLoad($user['id']);
            }

            return $view;
        }
    }

    /**
     * AJAX function for updating the price breakdown on the checkout
     */
    public function action_refresh_breakdown()
    {
        try {
            // Get the session details for the order. Merge them with the users latest form changes.
            $session_data   = Session::instance()->get('event_checkout_post_data');
            $post           = $this->request->post();
            $post           = is_array($session_data) ? $post + $session_data : $post;

            // Load the order and recalculate the price.
            $eventId        = isset($post['event_id'])      ? $post['event_id']      : '';
            $items          = isset($post['item'])          ? $post['item']          : '';
            $discountCode   = isset($post['discount_code']) ? $post['discount_code'] : '';
            $event          = Model_Event::eventLoad($eventId);
            $order          = Model_Event::orderCalculate($event, $items, $discountCode);

            // Return the data that's needed to update the DOM.
            $discount_label = ($order['discount_type'] == 'Fixed') ? $order['currency'].$order['discount_type_amount'] : ($order['discount_type_amount'] + 0).'%';
            $return         = array(
                'currency'       => $order['currency'],
                'discount_label' => __('$1 Discount', array('$1' => $discount_label)),
                'discount_type'  => $order['discount_type'],
                'discount'       => $order['discount'],
                'commission'     => $order['commission'],
                'vat'            => $order['vat'],
                'total'          => $order['total'],
                'error'          => false
            );
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error refreshing checkout breakdown\n".$e->getMessage())->write();
            $return = array(
                'error' => true,
                'error_message' => __(
                    'Error updating checkout. If this problem continuse, please $1. This error has been logged.',
                    array('$1' => '<a href="/contact-us.html">'.__('contact the administration').'</a>')
                )
            );
        }
        echo json_encode($return);
    }

    public function action_check_order_queue()
    {
        session_commit();
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $id = $this->request->post('id');
        if (!$id) {
            $id = @$_SESSION['event_ticket_order_queue_id'];
        }
        $expire_seconds = 600;
        $queue_count = Model_Queue::get_wait_count('event_ticket_order', $id);
        if ($queue_count == 0) {
            unset($_SESSION['event_ticket_order_queue_id']);
        } else {
            $_SESSION['event_ticket_order_queue_id'] = $id;
        }
        echo json_encode(
            array('count' => $queue_count, 'id' => $_SESSION['event_ticket_order_queue_id']),
            JSON_PRETTY_PRINT
        );
    }

    public function add_order_queue(&$queue_count, &$queue_id)
    {
        $queue_id = null;
        $expire_seconds = 600;

        if (@$_SESSION['event_ticket_order_queue_id']) {
            $queue_id = $_SESSION['event_ticket_order_queue_id'];
            $queue_count = Model_Queue::get_wait_count('event_ticket_order', $queue_id);
            if ($queue_count == 0) {
                $queue_id = null;
            }
        }

        if ($queue_id == null) {
            $queue_id = Model_Queue::create('event_ticket_order', date::now(), 'WAIT', date('Y-m-d H:i:s', time() + $expire_seconds));
            $queue_count = Model_Queue::get_wait_count('event_ticket_order', $queue_id);
            $_SESSION['event_ticket_order_queue_id'] = $queue_id;
        }
    }

    public function action_add_order_queue()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $this->add_order_queue($queue_count, $queue_id);

        echo json_encode(
            array('count' => $queue_count, 'id' => $queue_id),
            JSON_PRETTY_PRINT
        );
    }

    public function action_complete_3ds2()
    {
        $post = $this->request->post();
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        //$result = $post;
        $result = Model_Event::checkStripePaymentIntent($post['payment_intent']['id']);
        echo json_encode($result);
    }

    public function action_process_order()
    {
        ignore_user_abort(1);
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $max_queue_count = (int)Settings::instance()->get('checkout_max_queue_count');
        $queue_id = $this->request->query('queue_id');
        $queue_count = Model_Queue::get_wait_count('event_ticket_order', $queue_id);
        if ($queue_count == 0) {
            $this->add_order_queue($queue_count, $queue_id);
            $queue_count = Model_Queue::get_wait_count('event_ticket_order', $queue_id);
        }
        if ($queue_count > $max_queue_count) {
            $result = array('error' => __('Server is too busy. Please try again later.'), 'queue_count' => $queue_count, 'max_queue_count' => $max_queue_count, 'queue_id' => $queue_id);
            echo json_encode($result);
            return;
        }
        $expire_seconds = 30;
        if ($queue_id == null) {
            $queue_id = Model_Queue::create('event_ticket_order', date::now(), 'WAIT', date('Y-m-d H:i:s', time() + $expire_seconds));
            $_SESSION['event_ticket_order_queue_id'] = $queue_id;
        }
        session_commit();

        $locked = (int)DB::select(DB::expr("GET_LOCK('process_order', $expire_seconds) as locked"))->execute()->get('locked');
        if ($locked != 1) {
            session_start();
            //Model_Queue::set_status($queue_id, 'EXPIRED');
            //unset($_SESSION['event_ticket_order_queue_id']);
            $result = array('error' => __('Server is too busy. Please try again later.'), 'queue_count' => $queue_count, 'max_queue_count' => $max_queue_count, 'queue_id' => $queue_id);
            echo json_encode($result);
            return;
        }
        session_start();

        $post = $this->request->post();

        // If CATPCHA is enabled and this fails validation, don't continue
        if (!Model_Formprocessor::captcha_check($post)) {
            DB::select(DB::expr("RELEASE_LOCK('process_order')"))->execute();
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');

            $result = array('error' => __('Please make sure you have filled out the CAPTCHA correctly and try again. If this problem persists, please $1', array(
                '$1' => '<a href="/contact-us.html" target="_blank">'.__('contact the administration').'</a>'
            )));
            echo json_encode($result);
            //Model_Queue::set_status($queue_id, 'EXPIRED');
            //unset($_SESSION['event_ticket_order_queue_id']);
            return;
        }

        // Normalise the data that comes from the educate template to suit the events plugin
        // todo: update the database, so consistent columns are used
        if (!isset($post['firstname']) && isset($post['first_name'])) {
            $post['firstname'] = $post['first_name'];
        }
        if (!isset($post['lastname']) && isset($post['last_name'])) {
            $post['lastname'] = $post['last_name'];
        }
        if (!isset($post['address']) && (isset($post['address1']) || isset($post['address2']))) {
            $post['address'] = trim((isset($post['address1'])? $post['address1']  : '').(isset($post['address2'])? "\n".$post['address2']  : '').(isset($post['address3'])? "\n".$post['address3']  : ''));
        }

		IbHelpers::strip_tags_array($post);
        $result = array();
        $new_user = FALSE;

        $user = Auth::instance()->get_user();

        if (empty($user['id']))
        {
            $new_user = TRUE;
            $user_model = new Model_users;
            $new_user_data = $post;
            $new_user_data['can_login'] = 1;
            $registered = $user_model->register_user($new_user_data);

            if ( ! empty($registered['error']))
            {
                $result['error'] = $registered['error'];
            }
            else if ( ! Auth::instance()->login($post['email'], $post['password']))
            {
                $result['error'] = __('Error logging into your new account');
            }
            else
            {
                $user = Auth::instance()->get_user();
            }
        }

        $stale_payments = Model_Event::checkStalePayments(array('buyer_id' => $user['id']));

        if ( ! empty($result['error']))
        {
            DB::select(DB::expr("RELEASE_LOCK('process_order')"))->execute();
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            echo json_encode($result);
            Model_Queue::set_status($queue_id, 'EXPIRED');
            unset($_SESSION['event_ticket_order_queue_id']);
            return json_encode($result);
        }

        $cc = array();
        $cc['name'] = $post['firstname'] . ' ' . $post['lastname'];
        $cc['number'] = str_replace(array('-', ' '), '', trim($post['ccNum']));
        $cc['cvc'] = $post['ccCVC'];
        $cc['year'] = $post['ccYear'];
        $cc['month'] = $post['ccMonth'];
        $cc['type'] = $post['ccType'];

        if (isset($post['ccv'])) {
            $cc['cvc'] = $post['ccv'];
        }
        if (isset($post['ccExpMM'])) {
            $cc['month'] = $post['ccExpMM'];
        }
        if (isset($post['ccExpYY'])) {
            $cc['year'] = $post['ccExpYY'];
        }

        if($post['saveCheckout']){
            $checkoutDetails = array(
                'firstname' => $post['firstname'],
                'lastname' => $post['lastname'],
                'address' => $post['address'],
                'city' => '' . @$post['town'],
                'county' => $post['county'],
                'country_id' => $post['country_id'],
                'postcode' => $post['postcode'],
                'telephone' => $post['telephone'],
                'email' => $post['email'],
                'comments' => $post['comments']
            );
            Model_Event::checkoutDetailsSave($user['id'], $checkoutDetails);
        }

        if (is_numeric(@$post['partial_payment_id'])) {
            $partial_payment = Model_Event::get_order_from_partial_payment_id($post['partial_payment_id']);

            $event = Model_Event::eventLoad($partial_payment['order']['items'][0]['event_id']);
            $account = Model_Event::accountDetailsLoad($event['owned_by']);
            $commission = Model_Event::commissionGet($event, $event['owned_by']);

            $result = $this->process_partial_payment($account, $partial_payment, $cc, $post['paymore_amount'], $post);
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            echo json_encode($result);
            Model_Queue::set_status($queue_id, 'EXPIRED');
            unset($_SESSION['event_ticket_order_queue_id']);
            DB::select(DB::expr("RELEASE_LOCK('process_order')"))->execute();
            return;
        }
        $event = Model_Event::eventLoad($post['event_id']);
        $account = Model_Event::accountDetailsLoad($event['owned_by']);
        $commission = Model_Event::commissionGet($event, $event['owned_by']);

		// Set up an array of ticket types for the event being booked
		$event_tickets = array();
		foreach ($event['ticket_types'] as $item) $event_tickets[$item['id']] = $item;

        $duplicate_order = null;
        if (!@$post['skip_duplicate_test'] && Settings::instance()->get('events_warn_duplicate_order') == 1) {
            $duplicate_order = Model_Event::checkDuplicateOrder($post['email'], $post['item']);
        }

        if ($duplicate_order) {
            $dt = date(Settings::instance()->get('date_format'), strtotime($duplicate_order['starts'])) . ' ' . date('H:i', strtotime($duplicate_order['starts']));
            $result['error'] = __('You have already bought ' . $duplicate_order['quantity'] . ' ticket(s) for event ' . $duplicate_order['event'] . '(' . $dt . ')');
            $result['duplicate_warning'] = 1;
            $result['duplicate_order'] = $duplicate_order;
            $result['stale_payments'] = $stale_payments;
        } else {
             // Validate the ticket types being purchased
             foreach ($post['item'] as $item) {
                 if (!isset($event_tickets[$item['ticket_type_id']])) {
                     $result['error'] = __('The ticket being purchased, does not correspond to the selected event.');
                 } else {
                     foreach ($item['dates'] as $date) {
                         if ($event_tickets[$item['ticket_type_id']]['dates_quantity_remaining'][$date]['quantity'] < $item['quantity']) {
                             $result['error'] = __('There are not enough tickets left in stock to complete your order.');
                         }
                     }
                 }
             }
         }


		// If an error is found, prematurely exit the function and return the error message
		if ( ! empty($result['error']))
		{
			$this->auto_render = false;
			$this->response->headers('Content-type', 'application/json; charset=utf-8');
			echo json_encode($result);
            Model_Queue::set_status($queue_id, 'EXPIRED');
            unset($_SESSION['event_ticket_order_queue_id']);
            DB::select(DB::expr("RELEASE_LOCK('process_order')"))->execute();
			return;
		}

        $order = array();
        $order['buyer_id'] = $user['id'];
        $order['account_id'] = $account['id'];
        $order['status'] = 'Processing';
        $order['status_reason'] = 'New Order';
        $order['total'] = $post['total'];
        $order['currency'] = 'EUR';
        $order['firstname'] = @$post['firstname'] ? @$post['firstname'] : @$post['ccName'];
        $order['lastname'] = @$post['lastname'];
        $order['email'] = $post['email'];
        $order['address_1'] = @$post['address'];
        $order['address_2'] = @$post['address_2'];
        $order['city'] = '' . $post['town'];
        $order['country_id'] = $post['country_id'];
        $order['county'] = isset($post['county']) ? $post['county'] : null;
        $order['county_id'] = @$post['county_id'];
        $order['eircode'] = $post['postcode'];
        $order['telephone'] = $post['telephone'];
        $order['comments'] = isset($post['comments']) ? $post['comments'] : null;
        $order['commission_type'] = $commission['type'];
        $order['commission_amount'] = $commission['amount'];
        $order['commission_fixed_charge_amount'] = $commission['fixed_charge_amount'];
        $order['ip4'] = ip2long($_SERVER['REMOTE_ADDR']);
        $order['discount_code'] = @$post['discount_code'];
        $payers = @$post['payer'];
        $use_payment_plan = (int)@$post['use_payment_plan'];
        $paymentplan_id = @$post['paymentplan_id'];
        $paymore_amount = @$post['paymore_amount'];
        $total = Model_Event::orderCalculate($event, $post['item'], $post['discount_code']);
        if ($total['error']) {
            $result = array('error' => $total['error']);
        } else {
            $order['total'] = $total['total'];
            $order['currency'] = $total['currency'];
            $order['vat_total'] = $total['vat'];
            $order['vat_rate'] = $total['vat_rate'];
            $order['commission_total'] = $total['commission'];
            $order['discount'] = $total['discount'];
        }
        if ($use_payment_plan && $paymentplan_id) {
            foreach ($event['ticket_types'] as $ticket_type) {
                if ($ticket_type['id'] == $paymentplan_id) {
                    $tt_paymentplan = $ticket_type['paymentplan'];
                }
            }
            $commission = Model_Event::commissionGet($event, $event['owned_by']);
            $paymentplan = Model_Event::calculate_payment_plan(
                $total['subtotal'],
                $tt_paymentplan,
                $commission,
                $order['vat_rate'],
                @$paymore_amount ?: 0
            );

            $order['total'] = 0;
            $order['subtotal'] = 0;
            $order['vat_total'] = 0;
            $order['commission_total'] = 0;
            foreach ($paymentplan as $ppayment) {
                $order['total'] += $ppayment['total'];
                $order['subtotal'] += $ppayment['payment_amount'];
                $order['vat_total'] += $ppayment['vat'];
                $order['commission_total'] += $ppayment['fee'];
            }
        }

        $result = Model_Event::orderSave($account, $order, $total['items'], $cc, $payers, $use_payment_plan ? $paymentplan : false, $paymore_amount);

		if ($result AND ! $result['error'])
		{

		}

        try {
            if (@$post['signup_newsletter'] && Settings::instance()->get('mailchimp_list_id') != '' && Settings::instance()->get('mailchimp_apikey') != '') {
                $mc = new Mailchimp();
                $result['mailchimp'] = $mc->add_to_list($post['email'], 'subscribed', $_SERVER['REMOTE_ADDR']);
            }

            if ($new_user) {
                // User needs to verify their email before they can conduct further actions with this account
                Auth::instance()->logout();
            }
        } catch (Exception $exc) {

        }

        DB::select(DB::expr("RELEASE_LOCK('process_order')"))->execute();
        Model_Queue::set_status($queue_id, 'PROCESSED');
        unset($_SESSION['event_ticket_order_queue_id']);
        $_SESSION['last_order_result'] = $result;
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_invite_payers()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $response = Model_Event::invite_payers_2(
            $post['order_id'],
            $post['payer'],
            $post['comment']
        );
        echo json_encode($response);
    }

    public function action_paymore_update_calculate()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        if ($this->request->post('partial_payment_id')) {
            $response = Model_Event::calculate_paymore_partial_payments(
                $this->request->post('partial_payment_id'),
                $this->request->post('amount')
            );
        } else {
            $paymore = $this->request->post('amount');
            $event = Model_Event::eventLoad($this->request->post('use_payment_plan'));
            $tickettype_id = $this->request->post('paymentplan_id');
            foreach ($event['ticket_types'] as $ticket_type) {
                if ($ticket_type['id'] == $tickettype_id) {
                    $tt_paymentplan = $ticket_type['paymentplan'];
                }
            }
            $items = $this->request->post('items');
            $order = Model_Event::orderCalculate($event, $items, '');
            $commission = Model_Event::commissionGet($event, $event['owned_by']);
            $paymentplan = Model_Event::calculate_payment_plan(
                $order['subtotal'],
                $tt_paymentplan,
                $commission,
                $order['vat_rate'],
                $paymore
            );

            $response = $paymentplan;
        }
        echo json_encode($response);
    }

    public function process_partial_payment($account, $partial_payment, $cc, $paymore = null, $checkoutdetails)
    {
        $order = $partial_payment['order'];
        $partial_payment_update = array(

        );
        $payment = array();
        if ($paymore) {
            $event = Model_Event::eventLoad($order['items'][0]['event_id']);
            $commission = Model_Event::commissionGet($event, $event['owned_by']);
            $partial_payment_update['payment_amount'] = $partial_payment['partial_payment']['payment_amount'] + $paymore;
            $pp_break_down = Model_Event::calculate_price_breakdown(
                $partial_payment_update['payment_amount'],
                $commission['fixed_charge_amount'],
                $commission['type'] == 'Percent' ? $commission['amount'] : 0,
                $order['vat_rate'],
                false
            );
            $partial_payment_update['commission_total'] = $pp_break_down['fee'];
            $partial_payment_update['vat_total'] = $pp_break_down['vat'];
            $partial_payment_update['total'] = $pp_break_down['total'];
            $payment['amount'] = $partial_payment_update['total'];
        } else {
            $payment['amount'] = $partial_payment['partial_payment']['total'];
        }
        $payment['order_id']            = $order['id'];
        $paymore_diff                   = $paymore ?: 0;
        $payment['currency']            = $order['currency'];
        $payment['status']              = 'Processing';
        $payment['status_reason']       = 'New Payment';
        $payment['credit_card_type']    = $cc['type'];
        $payment['cc_last_four_digits'] = isset($cc['number']) ? substr(trim($cc['number']), -4) : null;
        $payment['created']             = $payment['created'] = date::now();
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
        $result = array();
        $inserted = DB::insert(Model_Event::TABLE_PAYMENTS)->values($payment)->execute();
        $payment['id'] = $inserted[0];
        $partial_payment_update['payment_id'] = $payment['id'];
        
        DB::update(Model_Event::TABLE_PARTIAL_PAYMENTS)
            ->set($partial_payment_update)
            ->where('id', '=', $partial_payment['partial_payment']['id'])
            ->execute();

        if ($payment['id']) {
            $result['payment_id'] = $payment['id'];
            $result['order_id'] = $order['id'] ;
            if (@$partial_payment_update['commission_total'] > 0) {
                $payment['application_fee'] = $partial_payment_update['commission_total'] + $partial_payment_update['vat_total'];
            } else {
                $payment['application_fee'] = $partial_payment['partial_payment']['commission_total'] + $partial_payment['partial_payment']['vat_total'];
            }
            $processed_payment = Model_Event::paymentProcess($account, $order, $payment, $cc, $partial_payment['partial_payment']['id']);

            if (isset($processed_payment['success']) && $processed_payment['success']) {
                if ($paymore) {
                    Model_Event::update_unpaid_partial_payments($partial_payment['partial_payment']['main_payment_id'], $paymore_diff, $commission, $order['vat_rate']);
                }
                $payment['status'] = 'PAID';
                $payment_fee = Model_Event::calculate_paymentgw_fee($payment);
                Model_Event::set_payment_fee($payment['id'], $payment_fee);

                if (@$processed_payment['payment_intent_secret']) {
                    $result['payment_intent_secret'] = $processed_payment['payment_intent_secret'];
                    $result['payment_public_key'] = $processed_payment['payment_public_key'];
                } else {
                    $result = Model_Event::sendPartialPaymentProcessedEmail($partial_payment['id'], $result);
                }
            } else {
                if (!empty($processed_payment['is_public_error']) && !empty($processed_payment['error'])) {
                    $result['error'] = $processed_payment['error'];
                } else {
                    $result['error'] = __('Error processing payment. If this problem continues, please contact the administration and use this order number for reference, $1.', array('$1' => '<strong>'.$order['id'].'</strong>'));
                }
            }
        }
        return $result;
    }

    public function action_order_hang_check()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode(array('last_order_result' => @$_SESSION['last_order_result']));
    }

    public function action_email_order_status()
    {
        $post = $this->request->post();
        $order_id = $post['order_id'];

        session_commit();
        ignore_user_abort(true);
        set_time_limit(0);
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $response = array(
            'order_id' => $order_id,
            'order_status' => $_SESSION['order_status_' . $order_id]
        );
        echo json_encode($response);
    }

    public function action_email_order()
    {
        $post = $this->request->post();
        $order_id = $post['order_id'];

        $_SESSION['order_status_' . $order_id] = 'started';
        session_commit();
        ignore_user_abort(true);
        set_time_limit(0);
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $this->email_order($order_id);
        echo json_encode(array('done' => true));

        session_start();
        $_SESSION['order_status_' . $order_id] = 'completed';
        session_commit();
    }

    public function email_order($order_id)
    {
        $lock_file = Kohana::$cache_dir . '/email_order_event_' . $order_id . '.lock';
        $lock = fopen($lock_file, 'c+');
        if (!flock($lock, LOCK_EX|LOCK_NB)) {
            return;
        }

        $order_result = Model_Event::orderLoad($order_id);
        $event_id = $order_result['items'][0]['event_id'];

        $user = Auth::instance()->get_user();
        $event = Model_Event::eventLoad($event_id);
        $account = Model_Event::accountDetailsLoad($event['owned_by']);
        $commission = Model_Event::commissionGet($event, $event['owned_by']);

        $pdf_ok = true;
        $attachments = array();
        try {
            $pdfs = Model_Event::ticketPDFGenerate($order_result);
            foreach ($pdfs as $i => $pdf) {
                $attachments[] = array(
                    'path' => $pdf['file'],
                    'name' => 'ticket-' . $pdf['code'] . '-' . date('YmdHi', strtotime($pdf['date'])) . '.pdf'
                );
            }
        } catch (Exception $exc) {
            //ignore missing template errors
            Model_Errorlog::save($exc);
            $pdf_ok = false;
        }

        try {
            $receipt_pdf = Model_Event::receiptGenerate($order_result);
            $attachments[] = array(
                'path' => $receipt_pdf,
                'name' => 'receipt-' . str_pad($order_id, 8, '0', STR_PAD_LEFT) . '.pdf'
            );
        } catch (Exception $exc) {
            //ignore missing template errors
            Model_Errorlog::save($exc);
            $pdf_ok = false;
        }
        $message_attachments = array();
        //$message_attachments = null;

        // Get parameters for the email message
        $event = new Model_Event($event_id);

        $event_load = Model_Event::eventLoad($event_id);
        $items = array();
        foreach ($order_result['items'] as $item) {
            $calculate_item = array(
                'ticket_type_id' => $item['ticket_type_id'],
                'quantity' => $item['quantity'],
                'dates' => array()
            );
            foreach($order_result['idates'] as $idate) {
                if ($idate['order_item_id'] == $item['id']) {
                    $calculate_item['dates'][] = $idate['date_id'];
                }
            }
            $items[] = $calculate_item;
        }
        $order_calculated = Model_Event::orderCalculate($event_load, $items, $order_result['discount_code']);

        $buyer_orders_table = View::factory('frontend/ticket_orders_table')
            ->set('event',    $event)
            ->set('items',    $order_result['items'])
            ->set('order',    $order_result)
            ->set('order_calculated', $order_calculated)
            ->set('show_net', FALSE)
            ->render();

        $seller_orders_table = View::factory('frontend/ticket_orders_table')
            ->set('event',    $event)
            ->set('items',    $order_result['items'])
            ->set('order',    $order_result)
            ->set('order_calculated', $order_calculated)
            ->set('show_net', TRUE)
            ->render();

        $email_parameters = $order_result;
        IbHelpers::htmlspecialchars_array($email_parameters);
        $email_parameters['date']               = date('F j, Y');
        $email_parameters['logosrc']            = URL::overload_asset('img/client-logo-black.png');
        $email_parameters['order_id']           = $order_id;
        $email_parameters['order_description']  = $order_result['description'];
        $email_parameters['profile_url']        = Url::site('admin/profile/edit?section=contact');
        $email_parameters['orders_url']         = Url::site('admin/events/orders');
        $email_parameters['organizer_help_url'] = Url::site('support');
        $email_parameters['buyer_help_url']     = Url::site('support');
        $email_parameters['download_url']       = Url::site('/admin/events/mytickets');
        $email_parameters['booking_price']      = $order_result['total'] - $order_result['vat_total'] - $order_result['commission_total'] + $order_result['discount'];
        $email_parameters['booking_fee']        = $order_result['vat_total'] + $order_result['commission_total'] - $order_result['discount'];
        $email_parameters['cms_email']          = $user['email'];
        $email_parameters['country']            = Model_Event::getCountryName($order_result['country_id']);
        $email_parameters['address_1']          = $order_result['address_1'];
        $email_parameters['address_2']          = $order_result['address_2'];
        $email_parameters['billing_name']       = $order_result['firstname'] . ' ' . $order_result['lastname'];
        $email_parameters['county']             = $order_result['county'];
        $email_parameters['note']               = ['value' => $event->email_note, 'html' => true];
        $email_parameters['firstname']          = $order_result['firstname'];
        $email_parameters['lastname']           = $order_result['lastname'];
        $email_parameters['customer_email']     = $order_result['email'];
        $email_parameters['customer_phone']     = $order_result['telephone'];

        $organisers = Model_Event::getEventOrganisers($event_id);

        if (count($organisers) > 0) {
            $email_parameters['organiser_name'] = $organisers[0]['first_name'] . ' ' . $organisers[0]['last_name'];
            $email_parameters['organiser_email'] = $organisers[0]['email'];
        } else {
            $email_parameters['organiser_name'] = '';
            $email_parameters['organiser_email'] = '';
        }

        /* Send the emails */
        // Seller
        $extra_recipients = array();
        foreach ($organisers as $organiser)
        {
            // If the linked contact has an email address, send a message to the contact
            if ($organiser['contact_email'] != '') {
                $extra_recipients[] = array(
                    'target_type' => 'CMS_CONTACT',
                    'target' => $organiser['id']
                );
            }
            // If the linked contact does not have an email address, but the organiser has an email address,
            // send a message directly to that email.
            else if ($organiser['email'] != '')
            {
                $extra_recipients[] = array(
                    'target_type' => 'EMAIL',
                    'target' => $organiser['email']
                );
            }
        }

        $messaging = new Model_Messaging;
        $email_parameters['orders_table'] = ['value' => $seller_orders_table, 'html' => true];
        if (@$account['notify_email_on_buy_ticket'] == 1) {
            $messaging->send_template(
                'ticket-purchased-seller',
                $message_attachments,
                NULL,
                $extra_recipients,
                $email_parameters
            );
        }

        // Buyer
        $extra_recipients = array(
            array('target_type' => 'EMAIL','target' => $order_result['email'],'id' => NULL,'template_id' => NULL,'x_details' => 'to','date_created' => NULL)
        );
        $email_parameters['orders_table'] = ['value' => $buyer_orders_table, 'html' => true];

        $message_attachments['attachments'] = $attachments;
        $nid = $messaging->send_template(
            'ticket-purchased-buyer',
            $message_attachments,
            NULL,
            $extra_recipients,
            $email_parameters
        );

        if ($pdf_ok) {
            $message_id = $messaging->getMessageIdFromNotification($nid);
            DB::update(Model_Event::TABLE_ORDERS)
                ->set(array('email_id' => $message_id))
                ->where('id', '=', $order_id)
                ->execute();
        }

        flock($lock, LOCK_UN);
        fclose($lock);
        unlink($lock_file);

        return $message_id;
    }

    public function action_mytickets()
    {
        $user = Auth::instance()->get_user();
        if (!$user || !isset($user['id'])) {
            $this->request->redirect('/events.html');
        } else {
            $tickets = Model_Event::ticketsList(array('buyer_id' => $user['id']));
            $view = View::factory('/frontend/list_mytickets');
            $view->tickets = $tickets;
            return $view;
        }
    }

    public function action_ticket()
    {
        $url = str_replace('.html', '', $this->request->param('item_category'));
        $ticket = Model_Event::ticketLoadFromUrlParam($url);
        $user = Auth::instance()->get_user();
        if (@$user['id'] != $ticket['buyer_id'] && @$user['id'] != $ticket['seller_id']) {
            $this->request->redirect('/events.html');
        } else {
            $allowToEnterStatus = ($user['id'] == $ticket['seller_id']);
            $post = $this->request->post();
            if (@$post['action'] == 'update' && $allowToEnterStatus) {
                DB::update(Model_Event::TABLE_TICKETS)
                    ->set(array(
                        'checked_by' => $user['id'],
                        'checked' => @$post['checked'] ? 1 : 0,
                        'checked_note' => $post['checked_note']
                    ))
                    ->where('id', '=', $ticket['id'])
                    ->execute();
                $this->request->redirect('/ticket/' . $url);
            }
            $view = View::factory('/frontend/view_ticket');
            $view->ticket = $ticket;
            $view->url = $this->request->url();
            $view->allowToEnterStatus = $allowToEnterStatus;
            return $view;
        }
    }

    public function action_qrcode()
    {
        $urlToCode = $this->request->query('url');
        $size = $this->request->query('size');

        require_once APPPATH . 'vendor/tcpdf/tcpdf.php';
        require_once APPPATH . 'vendor/tcpdf/tcpdf_barcodes_2d.php';

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'image/png');
        $barcodeobj = new TCPDF2DBarcode($urlToCode, 'QRCODE,H');
        $qrcodeImage = $barcodeobj->getBarcodePNGData($size, $size, array(0,0,0));
        echo $qrcodeImage;
    }

    public function action_registration()
    {
        $post = $this->request->post();
        $error = '';
        if (@$post['action'] == 'register') {
            $newUser = array();
            $newUser['name'] = $post['first_name'];
            $newUser['surname'] = $post['last_name'];
            $newUser['email'] = $post['email'];
            $newUser['password'] = $post['password'];
            $newUser['mpassword'] = $post['password'];

            $users = new Model_Users();
            $roles = new Model_Roles();
            $newUser['role_id'] = $roles->get_id_for_role('External User');
            $newUser['email_verified'] = 1;
            $newUser['can_login'] = 1;

            if ($users->check_email_set($newUser['email']) == FALSE) {
                $error = __('The email address has not been set.');
            } else if ($users->check_email_used($newUser['email']) == TRUE) {
                $error = __('This account already exists, please login or change your login details and try again.');
            } else if ($users->check_email_user($newUser['email']) == 1) {
                $error = __('This email address is not valid.');
            } else if ($users->check_passwords_set($newUser['password'], $newUser['mpassword']) == FALSE) {
                $error = __('Please fill in both password fields.');
            } else if ($users->check_passwords_match($newUser['password'], $newUser['mpassword']) == FALSE) {
                $error = __('The passwords you entered do not match.');
            } else if (strlen($newUser['password']) < 8) {
                $error = __('This password is too short, please enter a password with a minimum of 8 characters.');
            } else {
                unset($newUser['mpassword']);
                $inserted = $users->add_user_data($newUser);
                $userId = $inserted[0];
                $account = array();
                $account['owner_id'] = $userId;
                $account['stripe_auth'] = '';
                $account['status'] = 'ENABLED';
                Model_Event::accountDetailsSave($account);
                Model_Users::send_user_email_verification($userId);
                Auth::instance()->login($newUser['email'], $newUser['password']);
            }

            /*if ($post['organizer']['url']) {
                Model_Event::organizerSave(
                    $userId,
                    null,
                    $post['organizer']['name'],
                    '',
                    $post['email'],
                    '',
                    $post['mobile'],
                    $post['organizer']['url'],
                    @$post['organizer']['facebook'],
                    @$post['organizer']['twitter'],
                    @$post['organizer']['linkedin']
                );
            }*/
            ibhelpers::set_message(__('You have successfully registered'), 'success');
            $this->request->redirect('/events.html');
        }

        $view = View::factory('/frontend/registration_form');
        $view->alert = IBHelpers::alert($error, 'error');
        return $view;
    }

    public function action_login()
    {
        $post = $this->request->post();
        $error = '';
        if (@$post['login']) {
            $logged = Auth::instance()->login($post['email'], $post['password'], @$post['remember']);
            if ($logged) {
                $this->request->redirect('/events.html');
            }
        }

        $view = View::factory('/frontend/login_form');
        $view->error = $error;
        return $view;
    }

    public function action_logout()
    {
        $this->auto_render = false;

        // Delete the login redirect value
        Session::instance()->delete('login_redirect');
        $user = Auth::instance()->get_user();

        // Perform the logout
        if (Auth::instance()->logout()) {
            $activity = new Model_Activity();
            $activity->set_item_type('user')->set_action('logout')->set_user_id($user['id'])->save();

            $auto = $this->request->query('auto');
            // Redirect to the login page
            $this->request->redirect('/login.html' . ($auto ? '?auto=yes' : ''));
        }
    }

    public function action_myprofile()
    {
        $post = $this->request->post();
        $error = '';
        if (@$post['action'] == 'update') {

        }

        $view = View::factory('/frontend/edit_myprofile');
        return $view;
    }

    public function action_check_organizer_url()
    {
        $url = $this->request->post('url');
        $result = Model_Event::organizerUrlGet($url);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_stripe_event_handler()
    {

    }

	// Results to display below the searchbar as the user types
	public function action_ajax_search_autocomplete()
	{
		$this->auto_render = FALSE;
		$searchParams = array();
		$searchParams['publish'] = 1;
		$searchParams['is_public'] = 1;
		$searchParams['offset'] = 0;
		$searchParams['limit'] = 10;
		$searchParams['status'] = array(Model_Event::EVENT_STATUS_LIVE, Model_Event::EVENT_STATUS_SALE_ENDED);
		$searchParams['keyword'] = $this->request->query('term');
		$searchParams['whole_site'] = 1;
		$response = Model_Event::search($searchParams);

		echo json_encode($response);
	}

    public function action_autocomplete_tag_list()
    {
        $tags = Model_Event::autocomplete_tag_list($this->request->query('term'));
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($tags);
    }

    public function action_contact()
    {
        $this->auto_render = false;
        $post = $this->request->post();

        $formprocessor     = new Model_Formprocessor();
        if (!$formprocessor->captcha_check($post)) {
            $return = [
                'success' => false,
                'error_message' => __('Error validating CAPTCHA.'),
            ];

            echo json_encode($return);
            return json_encode($return);
        }

        if(isset($post['event_id']) || isset($post['venue_id'])) {
            $event = Model_Event::contact_form_handler($post);
            IbHelpers::set_message('Your enquiry has been received');
            if ($event) {
                $this->request->redirect('/event/' . $event['url']);
            } else {
                if ($post['venue_id']) {
                    $venue = Model_Event::venueLoad($post['venue_id']);
                    $this->request->redirect('/venue/' . $venue['url']);
                } else {
                    $this->request->redirect('/events.html');
                }
            }
        }
        else {
            $event = Model_Event::contact_form_handler_organiser($post);
            IbHelpers::set_message('Your enquiry has been received');
            if ($event) {
                $this->request->redirect('/organiser/' . $post['organiser_id']);
            } else {
                $this->request->redirect('/events.html');
            }
        }

    }

    public function action_calculate_price_breakdown()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $query = $this->request->query();
        $result = Model_Event::calculate_price_breakdown(
            $query['price'], $query['fee_fixed'], $query['fee_percent'], $query['vat_rate'], $query['absorb_fee']
        );
        echo json_encode($result);
    }

    public function action_cron_email_order_check()
    {
        $lock_file = Kohana::$cache_dir . '/cron_email_order_check.lock';
        $lock = fopen($lock_file, 'c+');
        if (!flock($lock, LOCK_EX|LOCK_NB)) {
            return;
        }
        set_time_limit(0);
        ignore_user_abort(1);

        $this->auto_render = false;
        header('content-type: text/plain; charset=utf-8');
        try {
            $payments = Model_Event::checkStalePayments();
            foreach ($payments as $payment) {
                $email_id = $this->email_order($payment['order_id']);
                if ($email_id) {
                    echo 'Sent email for order ' . $payment['order_id'] . "\n";
                }
            }

            $orders = DB::select('*')
                ->from(Model_Event::TABLE_ORDERS)
                ->where('email_id', 'is', null)
                ->and_where('status', '=', 'PAID')
                ->and_where('created', '>=', DB::expr('date_sub(now(), interval 1 day)'))
                ->order_by('id', 'desc')
                ->execute()
                ->as_array();
            foreach ($orders as $order) {
                $email_id = $this->email_order($order['id']);
                if ($email_id) {
                    echo 'Sent email for order ' . $order['id'] . "\n";
                }
            }
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
        flock($lock, LOCK_UN);
        fclose($lock);
        unlink($lock_file);
    }

    public function action_calculate_multiple_payers()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $amount = (float)$this->request->post('amount');
        $payers = (int)$this->request->post('payers');
        $use_payment_plan = (int)$this->request->post('use_payment_plan');

        if ($use_payment_plan) {
            $event = Model_Event::eventLoad($use_payment_plan);
        }
        $remains = $amount;
        $payment_each = round($remains / $payers, 2);

        $payments = array();
        for ($i = 0 ; $i < $payers ; ++$i) {
            $payment = array(
                'amount' => ($payers == $i + 1) ? $remains : $payment_each,
                'email' => ''
            );
            $payments[] = $payment;
            $remains -= $payment_each;
        }

        echo json_encode($payments, defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0);
    }

    public function action_test()
    {
        header('content-type: text/plain');
        $commission = array();
        $commission['type'] = 'Percent';
        $commission['amount'] = 4.5;
        $commission['currency'] = 'EUR';
        $commission['fixed_charge_amount'] = 0.59;
        $x = Model_Event::calculate_payment_plan(
            100,
            array(
                array('percent' => 10, 'date' => null, 'title' => 'Deposit'),
                array('percent' => 30, 'date' => '2018-09-10', 'title' => 'First'),
                array('percent' => 60, 'date' => '2018-09-30', 'title' => 'Last')
            ),
            $commission,
            0.23
        );
        print_r($x);
        exit;
    }
}

?>
