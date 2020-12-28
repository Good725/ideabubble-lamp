<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_FrontEnd_Bookings extends Controller_Template
{
    function before()
    {
        parent::before();
    }

    public function action_dbg_cart()
    {
        $cart = Session::instance()->get('ibcart');
        if (!$cart || $this->request->query('clear')) {
            $cart = array(
                'booking' => array(),
                'booking_id' => null,
                'client_id' => null,
                'discounts' => array(),
                'courses' => array()
            );
            Session::instance()->set('ibcart', $cart);
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'text/plain; charset=utf-8');
        print_r($cart);
    }

    public function action_add_to_cart()
    {
        $get = $this->request->query();
        $cart = Session::instance()->get('ibcart');
        if (!$cart || @$get['reset_cart'] === '1') {
            $cart = array(
                'booking' => array(),
                'booking_id' => null,
                'client_id' => null,
                'discounts' => array(),
                'courses' => array()
            );
        }

        if (!$cart['booking']) {
            $cart['booking'] = array();
        }

        if (!@$cart['courses']) {
            $cart['courses'] = array();
        }

        $cart['client_id'] = $this->request->post('student_id');
        $cart['client_id'] = $cart['client_id'] ? $cart['client_id'] : Auth::instance()->get_contact()->id;
        //print_r($cart['booking']);
        //print_r($this->request->post('booking'));
        //$cart['booking'] = array_merge($cart['booking'], $this->request->post('booking'));
        if ($this->request->post('override')) {
            $cart['booking'] = $this->request->post('booking');
            $cart['courses'] = $this->request->post('courses');
        } else {
            $add_schedules = $this->request->post('booking');
            if (is_array($add_schedules))
            foreach ($add_schedules as $schedule_id => $add_timeslots) {
                foreach ($add_timeslots as $add_timeslot_id => $add_timeslot) {
                    if (!isset($cart['booking'][$schedule_id])) {
                        $cart['booking'][$schedule_id] = array();
                    }

                    $cart['booking'][$schedule_id][$add_timeslot_id] = $add_timeslot;
                }
            }

            $add_courses = $this->request->post('courses');
            if (is_array($add_courses))
            foreach ($add_courses as $add_course) {
                $cart['courses'][$add_course['course_id']] = $add_course;
            }
        }

        if (!$cart['booking']) {
            $cart['booking'] = array();
        }

        if (!$cart['courses']) {
            $cart['courses'] = array();
        }
        
        if (@$get['add_to_cart_schedule_id']) {
            $schedule = Model_Schedules::get_schedule($get['add_to_cart_schedule_id']);
            $cart['booking'][$schedule['id']] = array();
            $fee_amount = ($schedule['booking_type'] === "Whole Schedule") ? $schedule['fee_amount'] : '0';
            $num_delegates = (isset($get['num_delegates']) && is_numeric($get['num_delegates'])) ? $get['num_delegates'] : '1';
            foreach ($schedule['timeslots'] as $timeslot) {
                if (strtotime($timeslot['datetime_start']) < time()) {
                    continue;
                }
                if (@$get['add_to_cart_timeslot_id'] > 0) {
                    if ($get['add_to_cart_timeslot_id'] == $timeslot['id']) {
                        $timeslot['attending'] = 1;
                        $timeslot['fee'] = $timeslot['fee'] ?? $fee_amount;
                        $timeslot['number_of_delegates'] = $num_delegates;
                        $cart['booking'][$schedule['id']][$timeslot['id']] = $timeslot;

                    }
                } else {
                    $timeslot['attending'] = 1;
                    $timeslot['fee'] = $timeslot['fee'] ?? $fee_amount;
                    $timeslot['number_of_delegates'] = $num_delegates;
                    $cart['booking'][$schedule['id']][$timeslot['id']] = $timeslot;
                }
            }

            $cart['number_of_delegates'] = $num_delegates;
        }

        Session::instance()->set('ibcart', $cart);

        if (@$get['add_to_cart_schedule_id']) {
            $this->request->redirect('/checkout');
        }

        $new_student_params = $this->request->post('new_student_params');
        //die('<pre>' . print_r($cart, 1) . '</pre>');
        $this->auto_render = false;
        $data = self::get_cart_data($cart['booking'], $cart['booking_id'], $cart['client_id'], $cart['discounts'], $cart['courses'], $new_student_params);
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data);
    }

    public function action_remove_from_cart()
    {
        $cart = Session::instance()->get('ibcart');
        if (!$cart) {
            $cart = array(
                'booking' => array(),
                'booking_id' => null,
                'client_id' => Auth::instance()->get_contact()->id,
                'discounts' => array(),
                'courses' => array()
            );
        }

        $cart['client_id'] = $this->request->post('student_id');
        $cart['client_id'] = $cart['client_id'] ? $cart['client_id'] : Auth::instance()->get_contact()->id;

        $remove_booking = $this->request->post('booking');
        foreach ($remove_booking as $schedule_id => $timeslots) {
            foreach ($timeslots as $timeslot_id => $timeslot) {
                unset ($cart['booking'][$schedule_id][$timeslot_id]);
            }
            if (empty($cart['booking'][$schedule_id])) {
                unset ($cart['booking'][$schedule_id]);
            }
        }
        Session::instance()->set('ibcart', $cart);

        $this->auto_render = false;
        $data = self::get_cart_data($cart['booking'], $cart['booking_id'], $cart['client_id'], $cart['discounts'], $cart['courses']);
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data);
    }

    public function get_session_cart_data()
    {
        $cart = Session::instance()->get('ibcart');
        if ($cart) {
            $cart['client_id'] = $this->request->post('student_id');
            $cart['client_id'] = $cart['client_id'] ? $cart['client_id'] : Auth::instance()->get_contact()->id;
            $data = self::get_cart_data($cart['booking'], $cart['booking_id'], $cart['client_id'], $cart['discounts'], $cart['courses']);
        } else {
            $data = null;
        }
        return $data;
    }

    public static function get_cart_data($bookings, $booking_id, $client_id, $discounts, $courses, $new_student_params = array())
    {
        foreach ($bookings as $scheduleId => $events) {
            $schedule_event_count = count($events);
            //print_r($events);exit;
            $booked_previosly = null;
            if ($client_id) {
                $booked_previosly = Model_KES_Bookings::check_existing_booking($client_id, $scheduleId);
            }
            if (array_key_exists("isScheduleOnly", $events)) {
                $bookings[$scheduleId]=array();
                $schedules = Model_Schedules::get_all_dates_for_schedules(array($scheduleId), "1970-01-01", "2500-01-01");

                foreach ($schedules as $i => $schedule) {
                    $event = array();
                    $event['attending'] = '1';
                    $event['note'] = ' ';
                    if ($booked_previosly) {
                        $schedule['trial_timeslot_free_booking'] = 0;
                    }
                    if ($schedule['trial_timeslot_free_booking'] == 1 && $schedule_event_count == 1) {
                        $event['fee'] = 0;
                    } else {
                        $event['fee'] = $schedule["schedule_fee_amount"];
                    }
                    $event['prepay'] = $schedule["payment_type"] == 1 ? true : false;


                    $bookings[$scheduleId][$schedule['event_id']]=$event;

                }


            } else {
                foreach ($events as $timeslot_id => $event) {
                    $event_details = DB::select('e.*', 's.fee_per', array('s.fee_amount', 'schedule_fee_amount'), 's.trial_timeslot_free_booking')
                        ->from(array('plugin_courses_schedules_events', 'e'))
                            ->join(array('plugin_courses_schedules', 's'), 'inner')->on('e.schedule_id', '=', 's.id')
                        ->where('e.id', '=', $timeslot_id)
                        ->execute()
                        ->current();
                    if ($booked_previosly) {
                        $event_details['trial_timeslot_free_booking'] = 0;
                    }
                    if ($event_details['fee_per'] == 'Timeslot') {
                        if ($event_details['trial_timeslot_free_booking'] == 1 && $schedule_event_count == 1) {
                            $bookings[$scheduleId][$timeslot_id]['fee'] = 0;
                        } else {
                            $bookings[$scheduleId][$timeslot_id]['fee'] = $event_details['fee_amount'] ?: $event_details['schedule_fee_amount'];
                        }
                    }
                }
            }

        }


        if (isset($discounts['null'])) {
            $discounts[null] = $discounts['null'];
            unset($discounts['null']);
        }

        $cart_session = Session::instance()->get('ibcart');
        $number_of_delegates = isset($cart_session['number_of_delegates']) ? $cart_session['number_of_delegates'] : null;

        $data = Model_KES_Bookings::get_order_data($bookings, $discounts, $client_id, $booking_id, null, $courses, $new_student_params, $number_of_delegates);
        foreach ($data as $key => $line) {
            $data[$key]['discounts'] = array_values($line['discounts']);
            if ($line['id'] != null) {
                if ($line['type'] == 'schedule') {
                    if (@$line['details']['booking_type'] == 'One Timeslot') {
                        $ltimeslot = current($line['timeslot_details']);
                        $data[$key]['details'] = Model_Schedules::get_one_for_details($line['id'], true, $ltimeslot['id']);
                    } else {
                        $data[$key]['details'] = Model_Schedules::get_one_for_details($line['id']);
                    }
                    if (!empty($data[$key]['details']['start_date'])) {
                        $data[$key]['details']['start_date'] = date('d/M/Y', strtotime($data[$key]['details']['start_date']));
                    }
                    $data[$key]['details']['paymentoption'] = $line['paymentoption_id'] ? DB::select('*')
                        ->from(Model_Schedules::TABLE_HAS_PAYMENTOPTIONS)
                        ->where('id', '=', $line['paymentoption_id'])
                        ->execute()
                        ->current() : null;

                    $data[$key]['details']['paymentoptions'] = DB::select('*')
                        ->from(Model_Schedules::TABLE_HAS_PAYMENTOPTIONS)
                        ->where('schedule_id', '=', $line['id'])
                        ->and_where('deleted', '=', 0)
                        ->execute()
                        ->as_array();
                }
                if ($line['type'] == 'course') {
                    $data[$key]['details'] = DB::select('*')
                        ->from(Model_Courses::TABLE_COURSES)
                        ->where('id', '=', $line['id'])
                        ->execute()
                        ->current();

                    $data[$key]['details']['paymentoption'] = $line['paymentoption_id'] ? DB::select('*')
                        ->from(Model_Courses::TABLE_HAS_PAYMENTOPTIONS)
                        ->where('id', '=', $line['paymentoption_id'])
                        ->execute()
                        ->current() : null;

                    $data[$key]['details']['paymentoptions'] = DB::select('*')
                        ->from(Model_Courses::TABLE_HAS_PAYMENTOPTIONS)
                        ->where('course_id', '=', $line['id'])
                        ->and_where('deleted', '=', 0)
                        ->execute()
                        ->as_array();
                }

                $data[$key]['number_of_delegates'] = $number_of_delegates;

            } else {
                $data[$key]['details'] = array();
            }
        }

        return $data;
    }


    // Check if any of the user's existing bookings or cart items have a scheduling conflict with the item
    public function action_ajax_check_for_booking_overlap()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        // Default return value, everything okay
        $return = array('message' => '', 'success' => true);

        try
        {
            // Call the model function that compares the times of the user's current bookings with the new item
            $contact_id = $this->request->post('contact_id');
            $event_id   = $this->request->post('event_id');
            $args       = array('check_permission' => true, 'check_cart' => true);
            $conflict_check = Model_KES_Bookings::check_for_booking_conflict($contact_id, $event_id, $args);

            // If the user does not have permission to view details on the person they are booking for, give an error
            // rather than allow them to potentially see someone else's timeslots
            if ( ! $conflict_check['has_permission'])
            {
                $return['success'] = false;
                $return['message'] = __(
                    'You do not have permission to view details for this contact. If this message is incorrect, please $1.',
                    array('$1' => '<a href="/contact-us.html" target="_blank">'.__('contact the administration').'</a>')
                );
            }
            // If the user has booked something else at this time, return a warning and a list of bookings it conflicts with
            else if ($conflict_check['has_conflict'])
            {
                $message = __('The time of this event interferes with the time of the following items you have booked. Are you sure you want to continue?').'<ul>';
                foreach ($conflict_check['overlapping_events'] as $overlap)
                {
                    $start_date = date('H:i jS F', strtotime($overlap['datetime_start']));
                    $end_date   = date('H:i jS F Y', strtotime($overlap['datetime_end']));
                    $message .= '<li>"'.$overlap['name'].'" '.$start_date.' &ndash; '.$end_date.'</li>';
                }
                $message .= '</ul>';

                $return['success'] = false;
                $return['message'] = $message;
            }
        }
        // If any other error occurs, add debugging information to the application logs and show a general error to the end user
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, 'Error checking for booking conflict'."\n".$e->getMessage()."\n".$e->getTraceAsString());

            $return['success'] = false;
            $return['message'] = __(
                'Unexpected internal error. Please try again. If this problem continues, please $1.',
                array('$1' => '<a href="/contact-us.html" target="_blank">'.__('contact the administration').'</a>')
            );
        }

        echo json_encode($return);
    }

    public function action_get_order_table_html()
    {
        $this->auto_render = false;
        $cart = Session::instance()->get('ibcart');
        //die('<pre>' . print_r($cart, 1) . '</pre>');
        if (!$cart) {
            $cart = array(
                'booking' => array(),
                'booking_id' => null,
                'client_id' => null,
                'discounts' => array(),
                'courses' => array()
            );
        }
        $cart['client_id'] = $this->request->post('student_id');
        $cart['client_id'] = $cart['client_id'] ? $cart['client_id'] : Auth::instance()->get_contact()->id;
        if (!$cart['client_id']) {
            $user = Auth::instance()->get_user();
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            $contact = new Model_Contacts3(isset($contacts[0]['id']) ? $contacts[0]['id'] : null);
            if ($contact->has_role('student')) {
                $cart['client_id'] = $contact->get_id();
            } else {
                $family_members = Model_Contacts3::get_family_members($contact->get_family_id());
                $students = array();

                foreach ($family_members as $family_member) {
                    if (in_array('student', $family_member['has_roles']) || in_array('mature',
                            $family_member['has_roles'])
                    ) {
                        $students[] = $family_member['id'];
                    }
                }
                if (count($students) == 1) {
                    $cart['client_id'] = $students[0];
                }
            }
        }
        Session::instance()->set('ibcart', $cart);
        $data = self::get_cart_data($cart['booking'], $cart['booking_id'], $cart['client_id'], $cart['discounts'], $cart['courses']);
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data);
    }

    public function action_blank()
    {
        die;
    }


    public function action_checkout()
    {
        if ($this->is_external_referer()){
            $error_id = Model_Errorlog::save(null, "SECURITY");
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/');
        }
        $this->template = View::factory('frontend/checkout_with_overlay');

        $page = ORM::factory('Page')->where_name('checkout')->find_published();;
        $this->template->page_object = $page;

        $this->template->page_data = array(
            'seo_description' => '',
            'seo_keywords'    => '',
            'title'           => __('Checkout'),
            'page_title'      => __('Checkout'),
            'content'         => $page->content,
            'layout'          => 'checkout',
            'banner_photo'    => '',
            'theme_home_page' => '',
            'name_tag'        => ''
        );

        $this->template->current_step = 'checkout';
        if (Settings::instance()->get('account_managed_course_bookings')) {
            $this->template->progress_links = array(
                'home'      => array('title' => __('Home'),          'link' => '/'),
                'results'   => array('title' => __('Availability'),  'link' => '/available-results.html'),
                'checkout'  => array('title' => __('Checkout'),      'link' => false, 'active' => true),
                'thank_you' => array('title' => __('Thank you'),     'link' => false)
            );
        }

        $this->template->theme = Model_Engine_Theme::get_current_theme();

        $cart = Session::instance()->get('ibcart');
        $post = $this->request->post();
        $get = $this->request->query();
        $contact = null;
        $contacts = array();
        if (@$get['confirmation'] == 'subscription' && is_numeric(@$get['booking_id'])) {
            $cart = array(
                'booking' => array(),
                'booking_id' => null,
                'client_id' => null,
                'discounts' => array(),
                'courses' => array()
            );
            $cart['booking'] = Model_KES_Bookings::cart_items_from_booking($get['booking_id']);
            $booking = new Model_KES_Bookings($get['booking_id']);
            $contacts = array(array('id' => $booking->get_contact_details_id()));
            $contact = new Model_Contacts3($booking->get_contact_details_id());
            Session::instance()->set('ibcart', $cart);
        } else if(isset($get['schedule_id']) && is_numeric($get['schedule_id'])) {
            $cart = array(
                'booking' => array(),
                'booking_id' => null,
                'client_id' => null,
                'discounts' => array(),
                'courses' => array()
            );
            $cart['booking'][$get['schedule_id']] = Model_Schedules::get_schedule($get['schedule_id'])['timeslots'];
            Session::instance()->set('ibcart', $cart);
        } else {
            if (empty($post) && !isset($cart['booking']) && !empty($cart['booking']) && !isset($cart['courses']) && count($cart['courses']) == 0) {
                $post = Session::instance()->get('last_checkout_post');
            } else {
                Session::instance()->set('last_checkout_post', $post);
            }
        }
        $custom_checkout = Settings::instance()->get('checkout_customization');
        $user            = Auth::instance()->get_user();
        if ($contact == null) {
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            $contact = new Model_Contacts3(isset($contacts[0]['id']) ? $contacts[0]['id'] : null);
        }
        $cards           = Model_Contacts3::get_cards($contact->get_id());
        $guardian        = null;
        $contact->get_primary_contact();
        $is_guardian     = $contact->has_role('guardian');
        $interview = (int)$this->request->query('interview');
        if (count($contacts)) {
            $family = new Model_Family($contact->get_family_id());
            $family_members = Model_Contacts3::get_family_members($contact->get_family_id());
            $students = array();

            foreach ($family_members as $family_member) {
                if (in_array('student', $family_member['has_roles']) || in_array('mature', $family_member['has_roles'])) {

                    if ($family_member['id'] == $contacts[0]['id'] || $is_guardian) {
                        $students[] = $family_member;
                    }
                    if (count($family_members) == 1 && in_array('mature', $family_member['has_roles'])) {
                        $guardian = new Model_Contacts3($family_member['id']);
                    }
                }
                if (in_array('guardian', $family_member['has_roles'])) {
                    if ($family_member['is_primary'] == 1 || $guardian == null) {
                        $guardian = new Model_Contacts3($family_member['id']);
                    }
                }
            }
        } else {
            $family = null;
            $family_members = array();
            $students = array();
        }

        // Default the guardian / lead booker to the logged-in contact
        if (!isset($guardian)) {
            $guardian = Auth::instance()->get_contact3();
        }

        $contact_email   = $contact->get_email();
        $booking_items   = ( ! empty($post['booking_items'])) ? $post['booking_items'] : array();
        if (count($booking_items) == 0 && !in_array($custom_checkout, ['bcfe', 'sls'])) {
            if (!$cart) {
                ibhelpers::set_message(__('Your cart is empty.'), 'warning');
                $this->request->redirect('/available-results.html');
            }

            $booking_items = $cart['booking'];
        }
        $discounts       = @$post['discounts'] ? @$post['discounts'] : array();
        $bookings        = array();
        $payg_bookings   = array();
        $prepay_bookings = array();
        $has_fulltime = false;
        if (isset($cart['courses'])) {
            $has_fulltime = count($cart['courses']) > 0;
        }
        $selected_student_id = @$post['student_id'];
        $count_seat_options  = 0; // Number of booking items with seating-select options
        if ($booking_items)
        {
            $schedule_cache = array(); // use cache to decrease memory usage when there are schedules with hundreds of timeslots.
            foreach ($booking_items as $schedule_id => $events) {
                if (!isset($schedule_cache[$schedule_id])) {
                    $schedule_cache[$schedule_id] = Model_Schedules::get_schedule($schedule_id);
                }
                $previosly_booked = Model_KES_Bookings::check_existing_booking($contact->get_id(), $schedule_id);
                if ($previosly_booked) {
                    $schedule_cache[$schedule_id]['trial_timeslot_free_booking'] = 0;
                }

                $schedule = new Model_Course_Schedule($schedule_id);

                // A "self-paced" schedule won't necessarily have timeslots
                // Ensure it gets a cart item
                if (empty($events) && $schedule->learning_mode->value == 'self_paced') {
                    $booking_item = [
                        'schedule_id' => $schedule->id,
                        'fee' => $schedule->fee_amount
                    ];
                    if (strtolower(Model_KES_Bookings::$payment_types[$schedule->payment_type]) == 'pre-pay') {
                        // Items on the checkout are grouped if they are for the same schedule, on the same day
                        $prepay_bookings[$schedule_id][] = $booking_item;
                    } else {
                        $payg_bookings[$schedule_id][] = $booking_item;
                    }
                }


                foreach ($events as $key => $booking_item) {
                    $booking_item['schedule_id'] = $schedule_id;
                    $booking_item['zones']    = Model_Schedules::get_zones($schedule_id);
                    $booking_item['schedule'] = &$schedule_cache[$schedule_id];
                    $booking_item['event']    = Model_Schedules::get_event_details($key);
                    if ($booking_item['schedule']['fee_per'] == 'Timeslot') {
                        $booking_item['fee'] = $booking_item['event']['fee_amount'] ?: $booking_item['schedule']['fee_amount'];
                    } else {
                        $booking_item['fee'] = $booking_item['schedule']['fee_amount'];
                    }
                    foreach ($booking_item['zones'] as $i => $zone) {
                        $args = array('zone_id' => $zone['zone_id'], 'row_id' => $zone['row_id']);
                        $booking_item['zones'][$i]['seats'] = Model_Schedules::get_remaining_seats($key, $args);
                    }

                    if (count($booking_item['zones'])) {
                        $count_seat_options += 1;
                    }

                    $bookings[$key] = $booking_item;
                    if (strtolower(Model_KES_Bookings::$payment_types[$booking_item['schedule']['payment_type']]) == 'pre-pay') {
                        // Items on the checkout are grouped if they are for the same schedule, on the same day
                        $prepay_bookings[$schedule_id.'-'.$booking_item['event']['date_formatted']][$key] = $booking_item;
                    } else {
                        $payg_bookings[$schedule_id.'-'.$booking_item['event']['date_formatted']][$key] = $booking_item;
                    }
                }
            }
        }

        $privacy_policy_page = Model_Pages::get_page_data(Settings::instance()->get('privacy_policy_page'));
        $privacy_policy_page = (isset($privacy_policy_page[0]) && is_array($privacy_policy_page[0])) ? $privacy_policy_page[0] : $privacy_policy_page;

        $subscribe_preference = new Model_Preferences;

        $realex_enabled         = (bool) Settings::instance()->get('enable_realex');
        $mobile_payment_enabled = (bool) Settings::instance()->get('enable_mobile_payments');
        $cash_payment_enabled = (bool) Settings::instance()->get('bookings_checkout_cash_enabled');
        if ($cash_payment_enabled) {
            $ips = Settings::instance()->get('bookings_checkout_cash_ips');
            $ips = preg_split('/[\s\,]+/', $ips);
            foreach($ips as $i => $ip) {
                $ip = trim($ip);
                if ($ip == '') {
                    unset($ips[$i]);
                } else {
                    $ips[$i] = $ip;
                }
            }
            if (count($ips) == 0 || !in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                $cash_payment_enabled = false;
            }
        }
        $org_contact = null;
        $cds_account = null;
        $billing_contact = $contact;
        if ($contact != null) {
            if (Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                $organization_type = Model_Contacts3::find_type('organisation');
                $related_contact_ids = Model_Contacts3::get_parent_related_contacts($contact->get_id());
                foreach ($related_contact_ids as $related_contact_id) {
                    $org_contact = new Model_Contacts3($related_contact_id);

                    if ($org_contact->get_type() == $organization_type['contact_type_id']) {
                        $cds = new Model_CDSAPI();
                        $cds_account = $cds->get_account($org_contact->get_id());
                       $new_address =  array(
                            'address1' => $cds_account['address1_line1'],
                            'address2' => $cds_account['address1_line2'],
                            'address3' => $cds_account['address1_line3'],
                            'country' => $cds_account['address1_country'],
                            'postcode' => $cds_account['address1_postalcode'],
                            'county' => @Model_Residence::get_county_id($cds_account['sp_countycode'], 'plugin_courses_counties', 'code'),
                            'town' => $cds_account['address1_city'],
                        );
                        if (!empty($cds_account)) {
                            $org_contact->billing_address->load($new_address);
                            $org_contact->billing_address->save();
                        }
                        break;
                    }
                    $org_contact = null;
                }
                $billing_contact = $org_contact;
            }
        }

        $credit_payment_enabled = Settings::instance()->get('bookings_enable_credit_booking') == 1;

        $messages = IbHelpers::get_messages();

        isset($this->template->alert)
            ? $this->template->alert .= $messages
            : $this->template->alert  = $messages;
        $this->template->contact                = $contact;
        $this->template->org_contact            = $org_contact;
        $this->template->cds_account            = $cds_account;
        $this->template->billing_contact       = $billing_contact;
        $this->template->logged_contact         = $contact;
        $this->template->guardian               = $guardian;
        $this->template->subscribe_preference   = $subscribe_preference->load(array('stub' => 'marketing_updates'))->get(true);
        $this->template->user_data              = $user;
        $this->template->family                 = $family;
        $this->template->family_members         = $family_members;
        $this->template->students               = $students;
        $this->template->cards                  = $cards;
        $this->template->email                  = $contact_email ? $contact_email : $user['email'];
        $this->template->confirmation           = @$get['confirmation'];
        $this->template->booking_id             = @$get['booking_id'];
        $this->template->counties               = Model_Residence::get_all_counties('plugin_courses_counties');
        $this->template->prepay_bookings        = $prepay_bookings;
        $this->template->payg_bookings          = $payg_bookings;
        $this->template->bookings               = $bookings;
        $this->template->credit_payment_enabled = $credit_payment_enabled;
        $this->template->discounts              = $discounts;
        $this->template->realex_enabled         = $realex_enabled;
        $this->template->mobile_payment_enabled = $mobile_payment_enabled;
        $this->template->payments_enabled       = ($realex_enabled || $mobile_payment_enabled);
        $this->template->selected_student_id    = $selected_student_id;
        $this->template->count_seat_options     = $count_seat_options;
        $this->template->privacy_policy_page    = $privacy_policy_page;
        $this->template->cart                   = $this->get_session_cart_data();
        $this->template->cash_payment_enabled   = $cash_payment_enabled;
        $this->template->has_fulltime           = $has_fulltime;
        $this->template->interview              = $interview;
        $this->template->cart_session_info      = Session::instance()->get('ibcart');
        $this->template->how_did_you_hear       = Model_Lookup::lookupList('How did you hear', ['public' => 1]);
    }

    public function action_cron_booking_schedule_start_reminder()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf-8');
        set_time_limit(0);
        ignore_user_abort(1);

        Model_KES_Bookings::send_booking_schedule_start_reminders();
    }

    public function action_host_application()
    {
        $this->template = View::factory('frontend/host_application');
        $this->template->theme = Model_Engine_Theme::get_current_theme();

        $page_data = Model_Pages::get_page('host-application');

        $this->template->countries = Model_Country::get_countries();

        if (!empty($page_data[0])) {
            $this->template->page_data = $page_data[0];
        } else {
            $this->template->page_data = [
                'seo_description' => '',
                'seo_keywords'    => '',
                'title'           => __('Host application'),
                'page_title'      => __('Host application'),
                'content'         => '',
                'layout'          => 'content',
                'banner_photo'    => '',
                'theme_home_page' => '',
                'name_tag'        => ''
            ];
        }
    }

    public function action_submit_host_application()
    {
        $post      = $this->request->post();
        $data      = Model_Realexpayments::clean_sensitive_data($post);
        $host      = Model_KES_Bookings::create_host_application($data);
        $message   = View::factory('email/host_application_admin', ['data' => $data])->render();
        $messaging = new Model_Messaging();

        $extra_targets = [
            ['target_type' => 'EMAIL', 'target' => $post['email'], 'x_details' => 'to']
        ];
        $messaging->send_template('host_application_applicant', null, null, $extra_targets, $data);

        //TODO: Save Host Contact Sub Type here

        $this->request->redirect('thank-you');
    }

    public function action_allpoints_create_new_transaction()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $amount = $this->request->post('amount');
        $mobile = $this->request->post('mobile');
        $operator = $this->request->post('operator');

        $tx = Model_Allpoints::create_transaction($amount, $mobile, $operator, 'KES booking');
        $allapi = new Model_Allpoints();
        $mm = new Model_Messaging();
        $template = $mm->get_notification_template('sms-payment-verification');
        $message = str_replace('$code', $tx['verification_code'], $template['message']);
        $sent = $allapi->sendsms($message, $mobile);
        if (@$sent['responseText'] == 'Success.') {
            unset ($tx['verification_code']);
            echo json_encode($tx);
        } else {
            echo json_encode(array('error' => 'Unable to send verification sms'));
        }
    }

    public function action_send_parent_auth_code()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $amount = $this->request->post('amount');
        $mobile = $this->request->post('mobile');
        $student_id = $this->request->post('student_id');

        $sent = Model_KES_Bookings::send_sms_auth_code($student_id, $mobile, $amount);
        if (@is_numeric($sent['id'])) {
            unset ($sent['code']);
            echo json_encode($sent);
        } else {
            echo json_encode(array('error' => 'Guardian mobile does not match existing records'));
        }
    }

    public function action_checkout_file_upload()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        try {
            Model_Files::create_directory(1, "tmp");
        } catch (Exception $exc) {

        }
        $tmp_dir_id = Model_Files::get_directory_id("/tmp");

        if (@$_FILES['file']['error'] === 0) {
            $filename = uniqid();
            $extension = explode(".", $_FILES["file"]["name"]);
            $filename .= "." . end($extension);
            $id = Model_Files::create_file($tmp_dir_id, $filename, $_FILES['file']);
            $response = array('file_id' => $id);
        } else {
            $response = array('error' => @$_FILES['file']['error']);
        }
        echo json_encode($response, defined("JSON_PRETTY_PRINT") ? JSON_PRETTY_PRINT : 0);
    }

    public function action_pay_application_fee()
    {
        $this->template = View::factory('frontend/fulltime_course_application_payment');
        $this->template->page_data = array(
            'seo_description' => '',
            'seo_keywords'    => '',
            'title'           => __('Payment'),
            'content'         => '',
            'layout'          => 'content',
            'banner_photo'    => '',
            'theme_home_page' => '',
            'name_tag'        => ''
        );
        $this->template->theme = Model_Engine_Theme::get_current_theme();

        $booking_id = $this->request->query('booking_id');
        $hash = $this->request->query('hash');
        $booking = Model_KES_Bookings::get_booking_hash($booking_id, $hash);
        if (!$booking) {
            $this->request->redirect('/');
        }

        $application = Model_KES_Bookings::get_application_details_by_booking_id($booking_id);
        $student = new Model_Contacts3($booking['contact_id']);
        $family = new Model_Family($student->get_family_id());
        $guardian = new Model_Contacts3($family->get_primary_contact_id());
        $cards = Model_Contacts3::get_cards($student->get_id());
        $privacy_policy_page = Model_Pages::get_page_data(Settings::instance()->get('privacy_policy_page'));
        $privacy_policy_page = (isset($privacy_policy_page[0]) && is_array($privacy_policy_page[0])) ? $privacy_policy_page[0] : $privacy_policy_page;

        $realex_enabled         = (bool) Settings::instance()->get('enable_realex');
        $mobile_payment_enabled = (bool) Settings::instance()->get('enable_mobile_payments');
        $cash_payment_enabled   = (bool) Settings::instance()->get('bookings_checkout_cash_enabled');
        if ($cash_payment_enabled) {
            $ips = Settings::instance()->get('bookings_checkout_cash_ips');
            $ips = preg_split('/[\s\,]+/', $ips);
            foreach($ips as $i => $ip) {
                $ip = trim($ip);
                if ($ip == '') {
                    unset($ips[$i]);
                } else {
                    $ips[$i] = $ip;
                }
            }
            if (count($ips) == 0 || !in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                $cash_payment_enabled = false;
            }
        }

        $messages = IbHelpers::get_messages();

        isset($this->template->alert)
            ? $this->template->alert .= $messages
            : $this->template->alert  = $messages;

        $this->template->booking                = $booking;
        $this->template->booking_id             = $this->request->query('booking_id');
        $this->template->confirmation           = $this->request->query('confirmation');
        $this->template->application            = $application;
        $this->template->application_payment    = true;
        $this->template->student                = $student;
        $this->template->contact                = $student;
        $this->template->cards                  = $cards;
        $this->template->selected_student_id    = $student->get_id();
        $this->template->family                 = $family;
        $this->template->guardian               = $guardian;
        $this->template->counties               = Model_Residence::get_all_counties();
        $this->template->realex_enabled         = $realex_enabled;
        $this->template->mobile_payment_enabled = $mobile_payment_enabled;
        $this->template->payments_enabled       = ($realex_enabled || $mobile_payment_enabled);
        $this->template->privacy_policy_page    = $privacy_policy_page;
        $this->template->cash_payment_enabled   = $cash_payment_enabled;
    }

    public function action_application_payment()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $post = $this->request->post();
        $booking_id = $post['booking_id'];
        $booking = DB::select('*')
            ->from(Model_KES_Bookings::BOOKING_TABLE)
            ->where('booking_id', '=', $booking_id)
            ->execute()
            ->current();
        if (!$booking) {
            exit;
        }

        $application = Model_KES_Bookings::get_application_details_by_booking_id($booking_id);

        $amount = 0;
        foreach ($post['paymentoption'] as $course_id => $use_paymentoption_id) {
            foreach ($application['courses'] as $course) {
                if ($course['course_id'] == $course_id) {
                    $transaction_id = DB::select('transaction_id')
                        ->from(Model_Kes_Transaction::TABLE_HAS_COURSES)
                            ->join(Model_Kes_Transaction::TRANSACTION_TABLE)->on(Model_Kes_Transaction::TABLE_HAS_COURSES . '.transaction_id', '=', Model_Kes_Transaction::TRANSACTION_TABLE . '.id')
                        ->where('course_id', '=', $course_id)
                        ->and_where('booking_id', '=', $booking_id)
                        ->execute()
                        ->get('transaction_id');

                    if ($use_paymentoption_id == 0) {
                        $amount += $course['fulltime_price'];
                    } else {
                        foreach ($course['paymentoptions'] as $payment_option) {
                            if ($payment_option['id'] == $use_paymentoption_id) {
                                if ($payment_option['interest_type'] != 'Custom') {
                                    $amount += $payment_option['deposit'];
                                    $terms = $payment_option['months'];
                                    Model_Kes_Payment::save_payment_plan(null, $transaction_id, $course['fulltime_price'], $payment_option['deposit'], 0, $terms, 'months', $payment_option['interest_type'], $payment_option['interest_rate'], date::now(), array(), null, false);
                                } else {
                                    $custom_payments = json_decode($payment_option['custom_payments'], true);
                                    $amount = $custom_payments[0]['total'];
                                    $ppamount = 0;
                                    $pptotal = 0;
                                    foreach ($custom_payments as $i => $custom_payment) {
                                        $custom_payments[$i]['due'] = $custom_payment['due_date'];
                                        $ppamount += $custom_payment['amount'];
                                        $pptotal += $custom_payment['total'];
                                    }
                                    Model_Kes_Payment::save_payment_plan(null, $transaction_id, $ppamount, 0, 0, count($custom_payments), 'custom', 'Fixed', 0, 0, $custom_payments, null, false);
                                }
                            }
                        }
                    }
                }
            }
        }

        $payment_status = Model_KES_Payment::get_payment_status(array('status' => 'Payment'));
        $payment_data = array(
            'credit' => 1,
            'transaction_id' => $transaction_id,
            'transaction_balance' => $amount,
            'amount' => $amount,
            'type' => @$post['payment_method'] == 'sms' ? 'sms' : 'card',
            'bank_fee' => 0,
            'status' => $payment_status['id'],
            'note' => '',
            'name_cheque' => '',
            'ccType' => isset($post['ccType']) ? $post['ccType'] : '',
            'ccName' => isset($post['ccName']) ? $post['ccName'] : '',
            'ccNum' => isset($post['ccNum']) ? preg_replace('/\D/', '', $post['ccNum']) : '',
            'ccv' => isset($post['ccv']) ? $post['ccv'] : '',
            'ccExpMM' => isset($post['ccExpMM']) ? $post['ccExpMM'] : '',
            'ccExpYY' => isset($post['ccExpYY']) ? $post['ccExpYY'] : '',
            'create_journal' => '',
            'journal_type' => '',
            'credit_transaction' => '',
            'contact_id' => $booking['contact_id'],
        );

        $payment_data['kes_booking_id'] = $booking['booking_id'];
        $payment = Controller_Admin_Payments::save_payment($payment_data);

        echo json_encode(array(
            'booking' => $booking,
            'payment' => $payment,
            'redirect'   => '/thankyou?id=' . $booking_id
        ));
    }

    public function action_cron_autopayments()
    {
        $this->auto_render = false;
        set_time_limit(0);
        ignore_user_abort(1);

        $this->auto_render = false;
        header('content-type: text/plain; charset=utf-8');
        try {
            $result = Model_KES_Bookings::process_auto_recurring_payments();
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function action_cron_paymentplan_reminder()
    {
        $this->auto_render = false;
        set_time_limit(0);
        ignore_user_abort(1);

        $this->auto_render = false;
        header('content-type: text/plain; charset=utf-8');
        try {
            $result = Model_KES_Bookings::send_payment_plan_reminders();
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public function action_test_charge()
    {
        $this->auto_render = false;
        set_time_limit(0);
        ignore_user_abort(1);

        header('content-type: text/plain; charset=utf-8');
        $rv = new Model_Realvault();
        $r = $rv->charge(
            'dev12349', 1, 'EUR', '4242424242424242', '1120', 'VISA', 'test test', '123', ['type' => 'fixed', 'sequence' => 'first']
        );
        echo $rv->last_request;
        echo $rv->last_response;
        exit;
    }

    public function action_cron_update_booking_status()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf-8');
        set_time_limit(0);
        ignore_user_abort(1);

        Model_Courses::update_schedule_status();
        Model_KES_Bookings::all_set_inprogress_completed();
    }

    public function action_submit_application()
    {
        $schedule   = new Model_Course_Schedule($this->request->post('schedule_id'));
        $booking_id = $this->request->post('booking_id');
        $delegate_id = $this->request->post('contact_id');
        $application_id = DB::select('id')
            ->from(Model_KES_Bookings::BOOKING_APPLICATIONS)
            ->where('booking_id', '=', $booking_id)
            ->and_where('delegate_id', '=', $delegate_id)
            ->execute()
            ->get('id');

        // Save the application
        $application = new Model_Booking_Application($application_id);
        $application->booking_id = $booking_id;
        $application->delegate_id = $delegate_id;
        $data = $this->request->post();
        $application->data = json_encode($data);
        $status_id = Model_KES_Bookings::COMPLETED;
        $application->status_id = $status_id;
        //save application as completed
        $application->save_with_history('status_id', $status_id);
        Model_Automations::run_triggers(Model_Bookings_Tuapplicationsubmittedtrigger::NAME, array('booking_id' => $booking_id, 'delegate_id' => $delegate_id, 'application_id' => $application_id));

        IbHelpers::set_message('Application has been saved.', 'success popup_box');
        if ($this->request->is_ajax()) {
            $this->auto_render = false;
            $this->response->headers('Content-Type', 'application/json; charset=utf-8');
            echo json_encode(array('success' => true));
        } else {
            $this->request->redirect('/application-thank-you.html');
        }
    }
}
