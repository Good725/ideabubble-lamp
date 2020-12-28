<?php

class Controller_Admin_Bookings extends Controller_Cms
{
    public $no_session_close_ajax_actions = array(
        'ajax_check_year',
        'ajax_process_booking'
    );
    function before()
    {
        parent::before();

        $menus = [];
        $menus[] = ['icon' => 'bookings',    'name' => 'Bookings',    'link' => '/admin/bookings'];
        if (Auth::instance()->has_access('bookings_discounts')) {
            $menus[] = ['icon' => 'discounts', 'name' => 'Discounts', 'link' => '/admin/bookings/list_discounts'];
        }
        $menus[] = ['icon' => 'settlements', 'name' => 'Settlements', 'link' => '/admin/bookings/list_settlements'];

        $this->template->sidebar = View::factory('sidebar');
        // The next portion of code is unnecessary as "menu" is set again later.
        // This is only kept in place to avoid a code conflict. It can be removed at a later point.
        $this->template->sidebar->menus = array(array(
            array('icon' => 'bookings', 'name' => 'Bookings', 'link' => '/admin/bookings'),
            array('icon' => 'discounts', 'name' => 'Discounts', 'link' => '/admin/bookings/list_discounts'),
            array('icon' => 'settlements', 'name' => 'Settlements', 'link' => '/admin/bookings/list_settlements')));
        $this->template->sidebar->breadcrumbs = array(array('name' => 'Home', 'link' => '/admin'), array('name' => 'Bookings', 'link' => '/admin/bookings'));
        //$this->template->sidebar->tools = '<a href="/admin/bookings/add_edit_discount/"><button type="button" class="btn">Add Discount</button></a>';
        switch ($this->request->action()) {
            case 'index':
                $this->template->sidebar->tools = '<a href="/admin/bookings/add_edit_booking/"><button type="button" class="btn">Add Booking</button></a>';
                break;
            case 'list_discounts':
                $this->template->sidebar->tools = '<a href="/admin/bookings/add_edit_discount/"><button type="button" class="btn">Add Discount</button></a>';
                break;
        }
        $this->template->sidebar->menus = [$menus];

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('courses') . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/fullcalendar.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/js.cookie.js"></script>';
        //Model_KES_Bookings::all_set_inprogress_completed();
        if (Settings::instance()->get('courses_schedule_interviews_enabled') == 1) {
            //IbHelpers::runonce('Model_KES_Bookings::import_interview_old_data');
        }
    }

    public function action_lsmtimeslotclean()
    {
        set_time_limit(0);
        DB::query(database::UPDATE, 'update plugin_courses_schedules set attend_all_default=0')->execute();

        $applications = DB::select('*')
            ->from('plugin_ib_educate_bookings_has_applications')
            //->where('id', '=', '19209')
            ->execute()
            ->as_array();

        header('content-type: text/plain');
        foreach ($applications as $application) {
            $data = json_decode($application['data'], true);
            $schedule_ids = DB::select('*')
                ->from(Model_KES_Bookings::BOOKING_SCHEDULES)
                ->where('booking_id', '=', $application['booking_id'])
                ->and_where('deleted', '=', 0)
                ->execute()
                ->as_array(null, 'schedule_id');
            $booking = DB::select('*')
                ->from(Model_KES_Bookings::BOOKING_TABLE)
                ->where('booking_id', '=', $application['booking_id'])
                ->execute()
                ->current();
            //header('content-type: text/plain');
            //print_r($schedule_ids);
            //print_r($data);
            //exit;*/
            if (@$data['has_period']) {
                /*$periods = array();
                foreach ($data['has_period'] as $period) {
                    $period = explode(',', $period);
                    $periods[] = "'" . $period[0] . "'";
                }*/
                $periods = $data['has_period'];
                Model_KES_Bookings::assign_application_schedules($application['booking_id'], $schedule_ids, $periods, $booking['contact_id']);
            }
        }
        exit;
    }
    
    public function action_index()
    {
        $stylesheets = array(
            URL::get_engine_plugin_assets_base('bookings') . 'admin/css/fullcalendar.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings') . 'admin/css/eventCalendar.css' => 'screen',
            URL::get_engine_plugin_assets_base('contacts3') . 'css/validation.css' => 'screen',
            URL::get_engine_plugin_assets_base('contacts3') . 'css/contacts.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings') . 'admin/css/bookings.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/moment.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/fullcalendar.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/jquery.eventCalendar.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts3') . 'js/jquery.validationEngine2.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts3') . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=' . Settings::instance()->get('google_map_key') . '&libraries=places&sensor=false"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts3') . 'js/maps.js"></script>';

        if (!$this->request->query('old_ui')) {
            $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/bootstrap.daterangepicker.min.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/timetable_view.js' . '"></script>';
        }
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('contacts3', 'js/contacts.js', ['cachebust' => true]) . '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('contacts3', 'js/list_contacts.js', ['cachebust' => true]) . '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('bookings', 'admin/js/bookings.js', ['cachebust' => true]) . '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/list_bookings.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/jquery.eventCalendar.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts3') . 'js/families.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/accounts.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('contacts3') . '/js/documents.js"></script>';

        $bookings = array();
        $alert = IbHelpers::get_messages();
        $this->template->alert = $alert;
        $this->template->body = View::factory('/admin/bookings/list_bookings')->bind('bookings', $bookings);
        $this->template->body->alert = $alert;
    }

    public function action_add_edit_booking()
    {
        $this->template->sidebar->breadcrumbs[] = ['name' => 'Create a new booking', 'link' => '#'];

        //setcookie('SpryMedia_DataTables_booking_schedules_list_table_', null);
        $query = $this->request->query();
        $id = $this->request->param('id');
        $booking = Model_KES_Bookings::create($id);
        $course_locations = Model_Locations::get_all_locations(null);
        $categories = Model_Categories::get_all_published_categories();
        $years = Model_Years::get_all_years();
        $rooms = Model_KES_Bookings::get_all_rooms();
        $subjects = Model_Subjects::get_all_subjects(array('publish' => true));
        $coupons = Model_KES_Discount::search(array('is_coupon' => 1));
        $contact_id = $booking->get_contact_details_id();
        $contact_id = ($contact_id == '' AND isset($query['contact'])) ? $query['contact'] : $contact_id;
        $existing_bookings = $contact_id ? Model_KES_Bookings::get_contact_family_bookings(null, $contact_id) : array();
        $transfer_booking_id = @$query['transfer_booking_id'];
        $edit_booking_id = @$query['edit_booking_id'];
        $link_contacts_bookings = Settings::instance()->get('link_contacts_to_bookings');
        foreach ($existing_bookings as $i => $existing_booking) {
            foreach ($existing_booking['schedules'] as $ii => $eschedule) {
                $etransaction = ORM::factory('Kes_Transaction')
                    ->get_transaction(null, $existing_booking['booking_id'], $eschedule['id']);
                $existing_bookings[$i]['schedules'][$ii]['default_transfer_credit'] = @$etransaction['payed'];
            }
        }

        $contact_data = new Model_Contacts3($contact_id);
        $periods = Model_KES_Bookings::get_all_booking_periods($booking->get_booking_id());
        $first_period = isset($periods[0]) ? $periods[0]['datetime_start'] : NULL;
        $multiple = ORM::factory('Kes_Transaction')->booking_has_multiple_transaction($id);

        $bill_payer_flag = '';
        $additional_flags = '';
        $booking_status_label = '';
        if (is_numeric($id)) {
            $billed = ORM::factory('Kes_Transaction')->booking_is_billed($id);
            if ($billed) {
                if ($bill_payer_flag = ORM::factory('Kes_Transaction')->bill_payer_full_name($id)) {
                    $bill_payer_flag = '<span class="label location-flag">Bill Payer: ' . ORM::factory('Kes_Transaction')->bill_payer_full_name($id) . '</span>';
                }
            }
            $count = $booking->get_additional_booking_details();

            if (!empty($count) && !empty($count->outside_of_school)) {
                $label = Model_KES_Bookings::get_label_text(6);
                if ($label) {
                    $additional_flags = '<span class="label location-flag">' . $label . '</span>';
                }
            }
            $booking_status = $booking->get_booking_status();
            $booking_status_label = Model_Schedules::get_booking_status_label($booking_status);
        }
        $stylesheets= array(
            URL::get_engine_plugin_assets_base('bookings') . 'admin/css/fullcalendar.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings') . 'admin/css/eventCalendar.css' => 'screen',
            URL::get_engine_plugin_assets_base('contacts3') . 'css/validation.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings') . 'admin/css/bookings.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=' . Settings::instance()->get('google_map_key') . '&libraries=places&sensor=false"></script>';

        if (!$this->request->query('old_ui')) {
            $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/bootstrap.daterangepicker.min.js"></script>';
            $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/timetable_view.js' . '"></script>';
        }

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts3') . 'js/maps.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/fullcalendar.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts3') . 'js/jquery.validationEngine2.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts3') . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/jquery.eventCalendar.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('bookings', 'admin/js/bookings.js', ['cachebust' => true]) . '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('contacts3', 'js/contacts.js', ['cachebust' => true]) . '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('contacts3', 'js/families.js', ['cachebust' => true]) . '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('contacts3', 'js/list_contacts.js', ['cachebust' => true]) .'"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/accounts.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('contacts3') . '/js/documents.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/list_bookings.js"></script>';

        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base().'js/timetable_view.js' . '"></script>';

        $this->template->body = View::factory('/admin/bookings/add_edit_booking')
            ->bind('booking', $booking)
            ->bind('course_locations', $course_locations)
            ->bind('categories', $categories)
            ->bind('years', $years)
            ->bind('contact_data', $contact_data)
            ->bind('rooms', $rooms)
            ->bind('first_period', $first_period)
            ->bind('multiple_transaction', $multiple)
            ->bind('subjects', $subjects)
            ->bind('existing_bookings', $existing_bookings)
            ->bind('transfer_booking_id', $transfer_booking_id)
            ->bind('edit_booking_id', $edit_booking_id)
            ->bind('coupons', $coupons)
            ->bind('bill_payer_flag', $bill_payer_flag)
            ->bind('additional_flags', $additional_flags)
            ->bind('booking_status_label', $booking_status_label)
            ->bind('link_contacts_to_bookings_access', $link_contacts_bookings)
            ->bind('potential_booking_application', Model_KES_Bookings::get_application_details($id));
    }

    public function action_find_customer()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $data = $this->request->query('term');
        $type = $this->request->query('type');
        $linked_contact_id = $this->request->query('linked_contact_id');
        $customer = Model_Contacts3::get_by_term($data, $type, $linked_contact_id);
        $this->response->body(json_encode($customer));
    }

    public function action_email_subscription()
    {
        $booking_id = $this->request->post('booking_id');

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        Model_KES_Bookings::email_subscription_link($booking_id);
        echo json_encode(array('booking_id' => $booking_id));
    }

    public function action_find_course()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json');
        $data = $this->request->query();
        $courses = DB::select('c.id', DB::expr("CONCAT(c.id, ' - ', c.title) AS `value`"), 'c.subject_id', 't.type')
            ->from(array(Model_Courses::TABLE_COURSES, 'c'))
            ->join(array('plugin_courses_types', 't'), 'left')
            ->on('c.type_id', '=', 't.id')
            ->where_open()
            ->or_where('c.title', 'like', '%' . $data['term'] . '%')
            ->or_where('c.id', 'like', '%' . $data['term'] . '%')
            ->where_close()
            ->and_where('c.deleted', '=', 0)
            ->order_by('c.title', 'asc')
            ->execute()
            ->as_array();
        $this->response->body(json_encode($courses));
    }

    public function action_find_category()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Tye', 'application/json');
        $data = $this->request->query();
        $courses = DB::select('id', DB::expr("CONCAT(id, ' - ', category) AS `value`"))
            ->from('plugin_courses_categories')
            ->where_open()
            ->or_where('category', 'like', '%' . $data['term'] . '%')
            ->or_where('id', 'like', '%' . $data['term'] . '%')
            ->where_close()
            ->order_by('category', 'asc')
            ->execute()
            ->as_array();
        $this->response->body(json_encode($courses));
    }


    public function action_find_schedule()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Tye', 'application/json');
        $data = $this->request->query();
        $result = Model_Courses::get_booking_search_term($data);
        $this->response->body(json_encode($result));
    }

    public function action_test()
    {
        $this->template->body = View::factory('admin/test');
    }

    public function action_get_schedules_list()
    {
        $this->auto_render = FALSE;
        $post = $this->request->post();

        if ($post['booking_id'] == '' || $post['edit_booking_id'] != '') {
            $post['datetime_end'] = date('Y-m-d 00:00:00', strtotime('+1year'));
            $data['schedules'] = Model_Schedules::get_schedules_feed($post);
            if (@$post['contact_id']) {
                $schedule_id = Model_KES_Bookings::get_all_contact_schedule_id($post['contact_id']);
                if (count($schedule_id) > 0) {
                    $search = array(
                        'contact_id' => $post['contact_id'],
                        'schedules' => $schedule_id,
                        'datetime_start' => !isset($post['datetime_start']) ? date('Y-m-d H:i:s', time()) : date('Y-m-d H:i:s', strtotime($post['datetime_start'])),
                        'datetime_end' => !isset($post['datetime_end']) ? date('Y-m-d 00:00:00', strtotime("+1 Week", time())) : date('Y-m-d H:i:s', strtotime($post['datetime_end']))
                    );
                    $data['all_bookings'] = Model_Schedules::get_booked_schedules_feed($search, TRUE);
                    foreach ($data['schedules'] as $key => $schedule) {
                        foreach ($data['all_bookings'] as $booked) {
                            if ($booked['schedule_id'] == $schedule['schedule_id'] AND $booked['period_id'] == $schedule['period_id']) {
                                $data['schedules'][$key]['attending'] = $booked['attending'];
                            }
                        }
                    }
                }
            }
        } else {
            $data['schedules'] = Model_Schedules::get_booked_schedules_feed($post, TRUE);
        }
        $data['length'] = Model_Schedules::get_feed_length($data['schedules'], $post);
        $data['current_bookings'] = $this->request->post('current_bookings');
        $data['booking_id'] = $post['booking_id'];
        $calendar_events = array();

        $current_bookings = array();
        if (@$post['contact_id']) {
            // Get all currently-booked items
            $current_bookings = Model_KES_Bookings::get_booking_items_family($post['contact_id'], NULL);
        }


        $schedules_timeslot_counts = array();
        foreach ($data['schedules'] as $i => $schedule) {
            if (!isset($schedules_timeslot_counts[$schedule['schedule_id']])) {
                $schedules_timeslot_counts[$schedule['schedule_id']] = 0;
            }
            $schedules_timeslot_counts[$schedule['schedule_id']] += 1;
        }
        foreach ($data['schedules'] as $i => $schedule) {
            if (strtotime($schedule['datetime_start']) > strtotime($schedule['datetime_end'])) {
                $data['schedules'][$i]['datetime_end'] = date('Y-m-d H:i:s', strtotime($schedule['datetime_end']) + 86400);
            }
            $data['schedules'][$i]['timeslots_count'] = $schedules_timeslot_counts[$schedule['schedule_id']];
        }

        foreach ($current_bookings as $booking_item) {
            if (!isset($schedules_timeslot_counts[$booking_item['schedule_id']])) {
                $schedules_timeslot_counts[$booking_item['schedule_id']] = 0;
            }
            $schedules_timeslot_counts[$booking_item['schedule_id']] += 1;
        }
        foreach ($current_bookings as $i => $booking_item) {
            if (strtotime($booking_item['datetime_start']) > strtotime($booking_item['datetime_end'])) {
                $current_bookings[$i]['datetime_end'] = date('Y-m-d H:i:s', strtotime($booking_item['datetime_end']) + 86400);
            }
            $current_bookings[$i]['timeslots_count'] = $schedules_timeslot_counts[$booking_item['schedule_id']];
        }


        $current_booking_ids = array();
        $i = 0;
        foreach ($current_bookings as $booking_item) {
            if ($booking_item['booking_status'] == Model_KES_Bookings::CANCELLED) {
                continue;
            }
            $calendar_events[$i] = $booking_item;
            $calendar_events[$i]['title']  = date('H:i', strtotime($booking_item['datetime_start'])).' '.$booking_item['schedule'];
            $calendar_events[$i]['start']  = $booking_item['datetime_start'];
            $calendar_events[$i]['end']    = $booking_item['datetime_end'];
            $calendar_events[$i]['booked'] = true;

            // So the schedule colour does not dictate the colour of the calendar event
            if (!empty($booking_item['color'])) {
                $calendar_events[$i]['schedule_color'] = $booking_item['color'];
                unset($calendar_events[$i]['color']);
            }

            $current_booking_ids[] = $booking_item['schedule_id'];

            $i++;
        }

        foreach ($data['schedules'] as $booking_item) {
            $added = false;
            foreach ($calendar_events as $calendar_event) {
                if ($calendar_event['schedule_id'] == $booking_item['schedule_id'] && $calendar_event['period_id'] == $booking_item['period_id']) {
                    $added = true;
                    break;
                }
            }

            if (!$added) {
                $calendar_events[$i] = $booking_item;
                $calendar_events[$i]['title'] = date('H:i', strtotime($booking_item['datetime_start'])).' '.$booking_item['name'];
                $calendar_events[$i]['start'] = $booking_item['datetime_start'];
                $calendar_events[$i]['end'] = $booking_item['datetime_end'];
                $calendar_events[$i]['booked'] = false;

                // So the schedule colour does not dictate the colour of the calendar event
                if (!empty($booking_item['color'])) {
                    $calendar_events[$i]['schedule_color'] = $booking_item['color'];
                    unset($calendar_events[$i]['color']);
                }

                $i++;
            }
        }

        $remaining_timeslots = array();
        $dup_events = $calendar_events;
        foreach ($calendar_events as $i => $calendar_event) {
            $calendar_events[$i]['timeslots_count'] = 0;
            foreach ($dup_events as $dup_event) {
                if ($dup_event['schedule_id'] == $calendar_event['schedule_id']) {
                    if (strtotime($dup_event['datetime_start']) >= strtotime($calendar_event['datetime_start'])) {
                        ++$calendar_events[$i]['timeslots_count'];
                    }
                }
            }
        }

        if (@$post['timeslots_range'] == 'upcoming') {
            $delete_slots = array();
            
            foreach ($calendar_events as $i => $calendar_event) {
                if (@$calendar_event['booked'] != 1) {
                    if (!isset($delete_slots[$calendar_event['schedule_id']])) {
                        $delete_slots[$calendar_event['schedule_id']] = true;
                    } else {
                        unset($calendar_events[$i]);
                    }
                }
            }
        }
        $data['calendar_events'] = array_values($calendar_events);

        $this->response->body(View::factory('/admin/bookings/booking_calendar', $data));
    }

    public function action_get_recurring_schedule()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $schedule_id = $this->request->post('schedule_id');
        $booking_type = $this->request->post('booking_type');
        $event_id = $this->request->post('event_id');
        $filter_from_event_id = $this->request->post('filter_from_event_id');
        $schedule = new Model_Course_Schedule($schedule_id);
        $timeslots = Model_Schedules::get_all_schedule_timeslots($schedule_id, $booking_type == 'One Timeslot' ? $event_id : null, $filter_from_event_id);
        $this->response->body(json_encode([
            'learning_mode'  => $schedule->learning_mode->value,
            'course_title'   => $schedule->course->title,
            'date'           => date('Y-m-d H:i:s'),
            'day'            => date('D'),
            'fee'            => $schedule->fee_amount,
            'fee_per'        => $schedule->fee_per,
            'payment-type'   => $schedule->payment_type,
            'schedule_id'    => $schedule->id,
            'schedule_title' => $schedule->name,
            'timeslots'      => $timeslots,
        ]));
    }

    public function action_get_period_table_html()
    {
        session_commit();
        $this->auto_render = FALSE;
        $data = $this->request->post('periods');
        $booking_type = $this->request->post('booking_type');
        $data = json_decode($data, TRUE);
        Model_Schedules::get_period_details($data, $booking_type == 'One Timeslot' ? false : true);
        $this->response->body(json_encode(array('html' => View::factory('/admin/bookings/confirmed_period_table')->bind('periods', $data)->render())));
    }

    public function action_get_course_details()
    {
        $this->auto_render = false;
        $schedule_id = $this->request->post('schedule_id');
        $course = Model_Courses::get_course_details_by_schedule($schedule_id);
        $this->response->body(json_encode($course));
    }

    public function action_get_order_table_html()
    {
        session_commit();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $bookings = $this->request->post('booking');
        $booking_id = $this->request->post('booking_id');
        $client_id = $this->request->post('client_id');
        $discounts = $this->request->post('discounts');
        if (isset($discounts['null'])) {
            $discounts[null] = $discounts['null'];
            unset($discounts['null']);
        }
        $booking = new Model_Booking_Booking($booking_id);
        $delegate_ids = $this->request->post('delegate_ids');

        if (!$bookings) {
            $bookings = array();
        }

        // Booked delegates. (Exclude cancelled ones.)
        $booked_delegates = Model_KES_Bookings::get_delegates($booking_id);
        $number_of_delegates = $booking_id ? count($booked_delegates) : count($delegate_ids);

        foreach ($bookings as $schedule_id => $timeslots) {
            foreach ($timeslots as $timeslot_id => $timeslot) {
                $bookings[$schedule_id][$timeslot_id]['number_of_delegates'] = $number_of_delegates;
            }
        }
        $data = Model_KES_Bookings::get_order_data($bookings, $discounts, $client_id, $booking_id);

        $can_add_discount = Auth::instance()->has_access('booking_add_discount');

        foreach ($data as $key => $line) {
            if ($line['id'] != null) {
                $data[$key]['created_date'] = IbHelpers::relative_time_with_tooltip($booking->created_date);
                if (Settings::instance()->get('cart_special_requirements_enable') == 1) {
                    $extra_data = $booking->extra_data;
                    if (!empty($extra_data)) {
                        $extra_data = json_decode($extra_data, 1);
                        $data[$key]['special_requirements'] = !empty($extra_data) && !empty($extra_data['special_requirements'])
                            ? $extra_data['special_requirements'] : '';

                    } else {
                        $data[$key]['special_requirements'] = '';
                    }
                }
                if (Settings::instance()->get('how_did_you_hear_enabled') == 1) {
                    $data[$key]['how_did_you_hear'] = $booking->how_did_you_hear;
                }
                $data[$key]['details'] = Model_Schedules::get_one_for_details($line['id']);
                if ($booking_id && $line['type'] == 'schedule' && $line['prepay'] == false) {
                    $next_payment_date = Model_KES_Bookings::get_next_payment_date($booking_id, $line['id']);
                    $data[$key]['next_payment']['date'] = date('d M Y', strtotime($next_payment_date));
                    $data[$key]['next_payment']['fulldate'] = $next_payment_date;
                }
                if (!empty($data[$key]['details']['start_date'])) {
                    $data[$key]['details']['start_date'] = '<span title="'.IbHelpers::formatted_time($data[$key]['details']['start_date']).'">'
                    .date('d/M/Y', strtotime($data[$key]['details']['start_date']))
                    .'</span>';
                }
                $min_date = '';
                if (!empty($data[$key]['timeslot_details'])) {
                    foreach($data[$key]['timeslot_details'] as $timeslot_id => $timeslot) {
                        if (empty($min_date)) {
                            $min_date = $timeslot['datetime_start'];
                        } else {
                            if (!empty($timeslot['datetime_start']) && $timeslot['datetime_start'] < $min_date) {
                                $min_date = $timeslot['datetime_start'];
                            }
                        }
                    }
                }
                if (!empty($min_date)) {
                    $data[$key]['details']['start_date'] = date('d/M/Y', strtotime($min_date));
                }
            } else {
                $data[$key]['details'] = array();
            }

            $data[$key]['can_add_discount'] = $can_add_discount;
        }

        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data);
        //$this->response->body(View::factory('/admin/bookings/order_table')->bind('bookings',$data)->render());
    }

    public function action_get_offers_and_discounts()
    {
        $this->auto_render = FALSE;
        $booking_id = $this->request->post('booking_id');
        if ($booking_id == '' OR is_null($booking_id)) {
            $cart = Session::instance()->get(Model_KES_Bookings::BOOKING_CART);
            $offers = isset($cart->offers) ? $cart->offers : array();
            $ignored_discounts = isset($cart->ignored_discounts) ? $cart->ignored_discounts : array();
        } else {
            $discounts = ORM::factory('Kes_Bookings')->get_booking_discounts($booking_id);
            $offers = array();
            $ignored_discounts = array();
            foreach ($discounts as $discount) {
                $offers[] = array('id' => $discount['discount_id'], 'title' => $discount['title'], 'amount' => $discount['amount']);
                if ($discount['status'] != '') {
                    $ignored_discounts[] = $discount['discount_id'];
                }
            }
        }
        $this->response->body(json_encode(array('cart_offers' => $offers, 'ignored_discounts' => $ignored_discounts)));
    }

    protected function trigger_checkout($booking_id, $contact_id, $data = null)
    {
        $ac_tags[] = array('tag' => 'Booking', 'description' => 'Booking');
        $ac_schedules = DB::select('schedules.id', 'schedules.course_id', array('schedules.name', 'schedule'), array('courses.title', 'course'), 'courses.code', 'schedules.start_date', 'schedules.fee_amount')
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
            ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
            ->on('schedules.id', '=', 'has_schedules.schedule_id')
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
            ->on('schedules.course_id', '=', 'courses.id')
            ->where('has_schedules.booking_id', '=', $booking_id)
            ->execute()
            ->as_array();
        foreach ($ac_schedules as $ac_schedule) {
            if ($ac_schedule['code']) {
                $ac_tags[] = array(
                    'tag' => $ac_schedule['code'],
                    'description' => $ac_schedule['course']
                );
            }
            if ($ac_schedule['start_date']) {
                $ac_tags[] = array(
                    'tag' => $ac_schedule['start_date'],
                    'description' => 'Schedule Start'
                );
            }
            if ($ac_schedule['fee_amount']) {
                $ac_tags[] = array(
                    'tag' => $ac_schedule['fee_amount'],
                    'description' => 'Fee'
                );
            }
        }

        if (@$data['booking_status'] == 6) {
            if (Model_Automations::check_duplicate(0, 'booking' . $booking_id) == false) {
                Model_Automations::run_triggers(
                    Model_Bookings_Adminquotecreatetrigger::NAME,
                    array(
                        'booking_id' => $booking_id,
                        'tags' => $ac_tags,
                    )
                );
                if (count(Model_Automations::$run_messages) == 0) {
                    Model_Automations::log_run(array('id' => 0), null, array(), array(), 'booking' . $booking_id);
                }
            }
        } else {
            if (@$data['payment_method'] != 'invoice' || @$data['invoice_details'] != '') {
                if (Model_Automations::check_duplicate(0, 'booking' . $booking_id) == false ||
                    $data['previos_booking_status'] != $data['booking_status'] ||
                    $data['previos_payment_method'] != $data['payment_method'] ||
                    $data['previos_invoice_details'] != $data['invoice_details']
                ) {
                    Model_Automations::run_triggers(
                        Model_Bookings_Adminbookingcreatetrigger::NAME,
                        array(
                            'booking_id' => $booking_id,
                            'tags' => $ac_tags,
                        )
                    );
                    if (count(Model_Automations::$run_messages) == 0) {
                        Model_Automations::log_run(array('id' => 0), null, array(), array(), 'booking' . $booking_id);
                    }
                }
            }
        }
        if (count(Model_Automations::$run_messages)) {
            $this->ajax_messages = Model_Automations::$run_messages;
            foreach (Model_Automations::$run_messages as $amessage) {
                IbHelpers::set_message($amessage, 'warning popup_box');
            }
        }
        Model_Automations::run_triggers(
            Model_Bookings_Checkouttrigger::NAME,
            array(
                'contact_id' => $contact_id,
                'tags' => $ac_tags,
            )
        );
        $transactions = Model_Kes_Transaction::get_contact_transactions(null, null, $booking_id);
        foreach ($transactions as $transaction) {
            Model_Automations::run_triggers(
                Model_Bookings_Checkouttrigger::NAME,
                array(
                    'transaction_id' => $transaction['id'],
                    'tags' => $ac_tags,
                )
            );
        }
    }

    /**
     * Check if the course year and student year of study match
     * If true process the booking
     * Else will return the data
     */
    public function action_ajax_check_year()
    {
        $this->auto_render = FALSE;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        header('Content-type: application/json; charset=utf-8');
        $data = $this->request->post();
        $student_ids = @$data['student_ids'];
        if (!isset($data['payment_method'])) {
            $data['payment_method'] = 'invoice';
        }
        unset($data['student_ids']);
        if (is_string($data['booking_items'])) {
            $data['booking_items'] = json_decode($data['booking_items'], TRUE);
        }
        if (Settings::instance()->get('cart_special_requirements_enable') == 1
            && isset($data['special_requirements'])) {
            $data['extra_data']['special_requirements'] = @$data['special_requirements'];
            unset ($data['special_requirements']);
        }
        if (isset($data['schedules']) AND is_array($data['schedules'])) {

            $schedules = array();
            foreach ($data['schedules'] as $key => $schedule) {
                if (isset($schedules[$schedule['schedule_id']])) {
                    unset($data['schedules'][$key]);
                } else {
                    $schedules[$schedule['schedule_id']] = $key;
                }
            }
            unset ($schedules);

            // remove rogue data; any schedules that are not in the booking list
            foreach ($data['schedules'] as $key => $schedule) {
                $schedule_id = $schedule['schedule_id'];
                if (!isset($data['booking_items']) OR !array_key_exists($schedule_id, $data['booking_items'])) {
                    unset($data['schedules'][$key]);
                }
            }
        }
        $data['schedules'] = isset($data['schedules']) ? $data['schedules'] : array();
        if (@$data['booking_id']) {
            $previos_data = DB::select('*')
                ->from(Model_KES_Bookings::BOOKING_TABLE)
                ->where('booking_id', '=', $data['booking_id'])
                ->execute()
                ->current();
            if ($previos_data) {
                $data['previos_booking_status'] = $previos_data['booking_status'];
                $data['previos_payment_method'] = $previos_data['payment_method'];
                $data['previos_invoice_details'] = $previos_data['invoice_details'];
            }
        }
        if (is_array($student_ids)) {
            $results = array();
            foreach ($student_ids as $student_id) {
                $data['contact_id'] = $student_id;
                $result = $this->process_booking($data);
                $results[] = $result;
            }
            echo json_encode($results);
        } else {

        $contact3 = new Model_Contacts3($data['contact_id']);
        $data['year_study'] = $contact3->get_year_id();
        $schedule_years = array();
        foreach ($data['booking_items'] as $key => $schedules) {
            $schedule_years[] = Model_KES_Bookings::get_schedule_details($data['year_study'], $key);
        }
        $matched = TRUE;
        $schedules = array();
        if ($data['year_study'] != '') {
            $study_year = DB::select('year')->from('plugin_courses_years')->where('id', '=', $data['year_study'])->execute()->as_array();
            $study_year = $study_year[0]['year'];
        } else {
            $study_year = 'Unspecified';
        }

        $matched_schedules = array();
        $perform_match_checks = Settings::instance()->get('bookings_display_booking_warning');

        foreach ($schedule_years AS $schedule_year) {
            if ($schedule_year['study_year_id'] != $schedule_year['year_id']) {
                $matched = $perform_match_checks ? false : true;
                $schedules[] = 'Schedule: ' . $schedule_year['name'] . ' is a ' . $schedule_year['year'] . ' course.';
            } else {
                $matched_schedules[] = 'Schedule: ' . $schedule_year['name'] . ' is a ' . $schedule_year['year'] . ' course.';
            }
        }

        if ($data['type'] == 7) {
            if ($matched) {
                $result = array('status' => 'bill', 'message' => '', 'data' => $data, 'matched' => TRUE);
            } else {
                $result = array('status' => 'bill', 'data' => $data, 'matched' => FALSE,
                    'message' => 'Schedules for ' . $study_year . ' courses.<br><ul>',
                    'unmatched_message' => 'Please note this contact study for ' . $study_year . ' courses.<br>The Schedules booked are:<ul>');
                foreach ($schedules as $schedule) {
                    $result['unmatched_message'] .= '<li>' . $schedule . '</li>';
                }
                foreach ($matched_schedules as $matched_schedule) {
                    $result['message'] .= '<li>' . $matched_schedule . '</li>';
                }
                $result['message'] .= '</ul>';
                $result['unmatched_message'] .= '</ul>Click Proceed to book anyway, or Cancel to ammend your booking';
            }
            $s = Model_Schedules::get_course_and_schedule_short($data['schedule_id']);
            $result['schedule_name'] = $s['name'];
            $result['course_title'] = $s['title'];
            $contact_data = new Model_Contacts3($data['contact_id']);
            $result['student'] = $contact_data->get_contact_name();
            echo json_encode($result);
        } else if ($matched) {
            $result = $this->process_booking($data);
            if (@$result['booking_id']) {
                $this->trigger_checkout($result['booking_id'], $data['contact_id'], $data);
            }

            echo json_encode($result);
        } else {
            $result = array(
                'status' => 'unmatched',
                'message' => 'Please note this contact study for ' . $study_year . ' courses.<br>The Schedules booked are:<ul>',
                'data' => $data
//                'schedules'=>$schedules
            );
            foreach ($schedules as $schedule) {
                $result['message'] .= '<li>' . $schedule . '</li>';
            }
            $result['message'] .= '</ul>Click Proceed to book anyway, or Cancel to ammend your booking';
            echo json_encode($result);
        }

        }
        exit;
    }

    /**
     * Process the booking
     * @param $data
     * @return array
     */
    public static function process_booking($data)
    {
        try {
            Database::instance()->begin();

            $redirect = (isset($data['redirect']) AND $data['redirect'] != '') ? $data['redirect'] : 'book';
            $previous_status = null;
            if ($redirect == 'update' && $data['booking_status'] == 2) {
                $previous_status = DB::select('booking_status')
                    ->from(Model_KES_Bookings::BOOKING_TABLE)
                    ->where('booking_id', '=', $data['booking_id'])
                    ->execute()
                    ->get('booking_status');
                if ($previous_status == 6) {
                    $previous_sq_transaction = DB::select('*')
                        ->from(Model_Kes_Transaction::TRANSACTION_TABLE)
                        ->where('booking_id', '=', $data['booking_id'])
                        ->execute()
                        ->current();
                    /*$previous_transaction_object = new Model_Kes_Transaction($previous_sq_transaction['id']);
                    $type_id = DB::select('id')
                        ->from(Model_Kes_Transaction::TYPE_TABLE)
                        ->where('type', '=', 'Journal Credit')
                        ->execute()
                        ->get('id');
                    $previous_transaction_object->set('type', $type_id);
                    $previous_transaction_object->save();*/
                    $new_booking_transaction = $previous_sq_transaction;
                    unset($new_booking_transaction['id']);
                    /*$new_booking_transaction['type'] = 1;
                    $new_booking_transaction['created'] = date::now();
                    $new_booking_transaction['updated'] = date::now();
                    $new_booking_transaction['payment_due_date'] = date::now();
                    DB::insert(Model_Kes_Transaction::TRANSACTION_TABLE)->values($new_booking_transaction)->execute();*/
                }

                // Status change to "confirmed" => run the `confirm()` function. (mark delegates as planning on attending)
                $booking = new Model_Booking_Booking($data['booking_id']);
                $booking->confirm(['delegate_ids' => $data['delegate_ids']]);
            }
            if(Model_Plugin::is_enabled_for_role('Administrator', 'navapi')) {
                $leadbooker = DB::select(array('leadbooker_emails.value', 'email'), 'leadbookers.first_name')
                    ->from(array(Model_Contacts3::CONTACTS_TABLE, 'leadbookers'))
                        ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'leadbooker_emails'), 'left')
                            ->on('leadbookers.notifications_group_id', '=', 'leadbooker_emails.group_id')
                            ->on('leadbooker_emails.notification_id', '=', DB::expr(1))
                    ->where('leadbookers.id', '=', $data['contact_id'])
                    ->execute()
                    ->current();
                if ($leadbooker['email'] == '') {
                    $result = array(
                        'status' => 'error',
                        'message' => __('Lead booker email is not set'),
                        'transaction_id' => '',
                        'booking_id',
                        'amount' => 0,
                        'transactions' => array()
                    );
                    return $result;
                }
                if ($leadbooker['first_name'] == '') {
                    $result = array(
                        'status' => 'error',
                        'message' => __('Lead booker First name is not set'),
                        'transaction_id' => '',
                        'booking_id',
                        'amount' => 0,
                        'transactions' => array()
                    );
                    return $result;
                }
                if(Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                    $org_booking = false;
                    $organization_type = Model_Contacts3::find_type('organisation');
                    $related_contact_ids = Model_Contacts3::get_parent_related_contacts($data['contact_id']);
                    foreach ($related_contact_ids as $related_contact_id) {
                        $org_contact = new Model_Contacts3($related_contact_id);
                        if ($org_contact->get_type() == $organization_type['contact_type_id']) {
                            $org_booking = true;
                            $company_contact_id = $org_contact->get_id();
                            break;
                        }
                    }
                    $cds = new Model_CDSAPI();
                    $company = '';
                    $company_account = $cds->get_account($company_contact_id);
                    if ($company_account) {
                        $company = '' . (@$company_account['sp_companyno'] ? @$company_account['sp_companyno'] : @$company_account['sp_tablekey']);
                    }
                    if ($org_booking) {
                        if ($company == '' && $data['booking_status'] != 6 && !$data['frontend_booking']) {
                            $result = array(
                                'status' => 'error',
                                'message' => __('CDS Company is not set'),
                                'transaction_id' => '',
                                'booking_id',
                                'amount' => 0,
                                'transactions' => array()
                            );
                            return $result;
                        }
                    }
                }
            }
            $result = array(
                'status' => '',
                'message' => '',
                'transaction_id' => '',
                'booking_id',
                'amount' => 0,
                'transactions' => array()
            );
            $schedules = $data['schedules'];
            if (!is_numeric(@$data['billing_address_id'])) {
                $contact_residence = DB::select('residence', 'billing_residence_id')
                    ->from(Model_Contacts3::CONTACTS_TABLE)
                    ->where('id', '=', $data['bill_payer'] ? $data['bill_payer'] : $data['contact_id'])
                    ->execute()
                    ->current();
                $billing_residence_id_clone = $contact_residence['billing_residence_id'] ?: $contact_residence['residence'];
                if ($billing_residence_id_clone) {
                    $billing_address = new Model_Residence($billing_residence_id_clone);
                    $billing_address->set_address_id(null);
                    $billing_address->save();

                    $data['billing_address_id'] = $billing_address->get_address_id();
                }
            }
            
            $booking_items = isset($data['booking_items']) ? $data['booking_items'] : null;
            $seats_validation = Model_SChedules::validate_seats($booking_items);

            if ( ! $seats_validation['valid'])
            {
                $result['status'] = 'error';
                $result['message'] = implode("\n", $seats_validation['errors']);
                return $result;
            }

            $cancel_booking_schedules = @$data['cancel_booking_schedule'];
            if ($data['booking_status'] == 6) {
                $data['payment_method'] = 'sales_quote';
            }
            $booking = Model_KES_Bookings::create();
           // die('<pre>' . print_r($data ,1  ) . '</pre>');
            $answer = $booking->set($data)->book();

            $booking_id = $booking->get_booking_id();
            $result['message'] = 'Booking #' . $booking_id . ' has been saved.';

            $result['booking_id'] = $booking_id;

            $transaction = ORM::factory('Kes_Transaction');
            if (isset($data['discounts']['null'])) { // a workaround for some js gimmick
                $data['discounts'][null] = $data['discounts']['null'];
                unset($data['discounts']['null']);
            }

            $has_payg = false;
            $payg_frontend_booking_transaction = array (
                'booking_id' => $booking_id,
                'amount'     => 0,
                'fee'        => 0,
                'type'       => 2,
                'schedule'   => array(),
                'deposit'    => array(),
                'discount'   => 0.0,
                'total'      => 0,
            );
            if (@$data['frontend_booking']) {
                $payg_frontend_booking_transaction = array (
                    'booking_id' => $booking_id,
                    'amount'     => 0,
                    'fee'        => (float)Settings::instance()->get('course_payg_booking_fee'),
                    'type'       => 2,
                    'schedule'   => array(),
                    'discount'   => 0.0,
                    'total'      => (float)Settings::instance()->get('course_payg_booking_fee'),
                    'deposit'    => array()
                );
            }

            $txresults = array();
            if ($answer['result']) {
                $result['status'] = 'success';
                $transactions_schedules = array();
                $payer = is_numeric(@$data['bill_payer']) ? $data['bill_payer'] : null;

                $booking_subtotal = $data['amount'] + ($data['discount'] ? $data['discount'] : 0);
                $booking_level_discount = 0;
                if (isset($data['discounts'][null])) {
                    foreach ($data['discounts'][null] as $discount) {
                        if ($discount['ignore'] != 1) {
                            $booking_level_discount += $discount['amount'];
                        }
                    }
                }
                $booking_level_discount_left = $booking_level_discount;

                if (is_array(@$data['courses']))
                if (count($data['courses']) > 0) {

                }

                $scount = count($schedules);
                foreach ($schedules as $si => $schedule)
                {
                    $last_schedule = ($si == ($scount - 1));

                    $seat_cost = 0;
                    $booking_schedule_id = isset($schedule['id']) ? $schedule['id'] : $schedule['schedule_id'];
                    $previously_booked = Model_KES_Bookings::check_existing_booking($data['contact_id'], $booking_schedule_id, null, $booking_id);
                    $first_timeslot = null;
                    $number_of_delegates = 0;
                    if (!empty($data['booking_items'])) {
                        foreach ($data['booking_items'][$booking_schedule_id] as $timeslot_id => $event) {
                            if (isset($data['delegate_ids'])) {
                                $number_of_delegates = count($data['delegate_ids']);
                            }
                            if ($event['number_of_delegates'] > 0) {
                                $number_of_delegates = $event['number_of_delegates'];
                            }
                            if (!$first_timeslot) {
                                $first_timeslot = Model_ScheduleEvent::get($timeslot_id);
                            }
                            if (!empty($event['seat_row_id'])) {
                                $seat_cost .= DB::select('price')
                                    ->from('plugin_courses_schedules_have_zones')
                                    ->where('schedule_id', '=', $schedule['id'])
                                    ->where('row_id', '=', $event['seat_row_id'])
                                    ->execute()->get('price', 0);
                            }
                        }
                    }

                    $schedule_details = DB::select('*')
                        ->from('plugin_courses_schedules')
                        ->where('id', '=', $schedule['schedule_id'])
                        ->execute()
                        ->current();
                    if (isset($data['delegate_ids']) && $schedule_details['is_group_booking'] == 1) {
                        $number_of_delegates = count($data['delegate_ids']);
                    }
                    if ($previously_booked) {
                        $schedule_details['trial_timeslot_free_booking'] = 0;
                    }
                    $schedule_details['paymentoptions'] = DB::select('*')
                        ->from(Model_Schedules::TABLE_HAS_PAYMENTOPTIONS)
                        ->where('schedule_id', '=', $schedule['schedule_id'])
                        ->execute()
                        ->as_array();
                    
                    if (@$schedule_details['trial_timeslot_free_booking'] == 1 && isset($data['booking_items'][$schedule['schedule_id']]) && count($data['booking_items'][$schedule['schedule_id']]) == 1) {
                        $class_cost = 0;
                    } else if ($schedule_details['fee_per'] == 'Timeslot') {
                        $schedule_booking_item_ids = array();
                        if (isset($data['booking_items'][$schedule['schedule_id']])) {
                            foreach ($data['booking_items'][$schedule['schedule_id']] as $booking_item_id => $booking_item) {
                                $schedule_booking_item_ids[] = $booking_item_id;
                            }
                        }
                        $class_cost_q = DB::select(DB::expr("SUM(IFNULL(events.fee_amount, schedules.fee_amount)) AS total"))
                            ->from(array('plugin_courses_schedules', 'schedules'))
                            ->join(array('plugin_courses_schedules_events', 'events'), 'inner')
                            ->on('schedules.id', '=', 'events.schedule_id')
                            ->where('schedules.id', '=', $schedule['schedule_id'])
                            ->and_where('events.delete', '=', 0);
                        if (count($schedule_booking_item_ids) > 0) {
                            $class_cost_q->and_where('events.id', 'in', $schedule_booking_item_ids);
                        }
                        $class_cost = $class_cost_q->execute()->get('total');
                    } else if ($schedule_details['fee_per'] == 'Day') {
                        $class_cost = Model_Schedules::calculate_fee_for_schedule($schedule_details['id'], array_keys($data['booking_items'][$schedule['schedule_id']]));
                    } else {
                        $class_cost = $schedule_details['fee_amount'];
                    }

                    if ($number_of_delegates > 0 && $schedule_details['charge_per_delegate'] == 1) {
                        $class_cost = $number_of_delegates * $class_cost;
                    }

                    $is_payg = ($schedule_details['payment_type'] == 2);
                    if ($is_payg) {
                        $has_payg = true;
                        if ($schedule_details['deposit'] > 0 && $class_cost > 0) {
                            $payg_frontend_booking_transaction['amount'] += (float)$schedule_details['deposit'];
                            $payg_frontend_booking_transaction['total'] += (float)$schedule_details['deposit'];
                            $payg_frontend_booking_transaction['deposit'][] = (float)$schedule_details['deposit'];
                        } else {
                            $payg_frontend_booking_transaction['deposit'][] = 0;
                        }
                        $payg_frontend_booking_transaction['schedule'][] = $schedule['schedule_id'];
                    } else {
                        $amount = $seat_cost;
                        $amount += $is_payg ? 0 : $class_cost; // "Pay as you go" class costs are paid as you attend

                        $prepay_transaction = array(
                            'booking_id' => $booking_id,
                            'amount' => $amount,
                            'fee' => $seat_cost,
                            'type' => 1,
                            'schedule' => array($schedule['schedule_id']),
                            'discount' => 0.0,
                            'total' => $class_cost + $seat_cost,
                            'first_timeslot' => $first_timeslot,
                            'deposit' => array($schedule['deposit'])
                        );

                        if (@$data['paymentoption'][$schedule['schedule_id']]) {
                            foreach ($schedule_details['paymentoptions'] as $paymentoption) {
                                if ($paymentoption['id'] == $data['paymentoption'][$schedule['schedule_id']]) {
                                    $prepay_transaction['paymentoption'] = $paymentoption;
                                }
                            }
                        }


                        if (isset($data['discounts'])) {
                            foreach ($data['discounts'] as $discount_schedule_id => $sdiscounts) {
                                if ($discount_schedule_id == $schedule['schedule_id'] || $discount_schedule_id == null) {
                                    foreach ($sdiscounts as $discount) {
                                        if ($discount['ignore'] != 1) {
                                            if ($discount_schedule_id != null) {
                                                $prepay_transaction['discount'] += floatval($discount['amount']);
                                            } else {
                                                if ($last_schedule) {
                                                    $bdiscount = $booking_level_discount_left;
                                                } else {
                                                    $bdiscount = round($discount['amount'] * ($class_cost / $booking_subtotal),
                                                        2);
                                                }
                                                $prepay_transaction['discount'] += $bdiscount;
                                                $booking_level_discount_left -= $bdiscount;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $prepay_transaction['total'] -= $prepay_transaction['discount'];

                        if (@$data['amendable']) {
                            $course_amend_fee_percent = (float)Settings::instance()->get('course_amend_fee_percent');
                            $amendable_fee = round($prepay_transaction['total']  * ($course_amend_fee_percent / 100), 2);
                            $prepay_transaction['total'] += $amendable_fee;
                            $prepay_transaction['fee'] += $amendable_fee;
                        }


                        if (is_numeric(@$data['bill_payer'])) {
                            $payer = $data['bill_payer'];
                            $types = DB::select('id')
                                ->from('plugin_bookings_transactions_types')
                                ->where('type', '=', 'Billed Booking')
                                ->execute()
                                ->as_array();
                            $prepay_transaction['type'] = $types[0]['id'];
                            $prepay_transaction['payer'] = $payer;
                        }

                        array_push($transactions_schedules, $prepay_transaction);
                    }
                }
                
                // save discounts
                if (isset($data['discounts'])) {
                    foreach ($data['discounts'] as $discount_schedule_id => $sdiscounts) {
                        foreach ($sdiscounts as $discount) {
                            $status = '';
                            if ($discount['ignore'] == 1) {
                                $status = 'ignored_discount';
                            }

                            $booking->save_discounts(
                                $booking_id,
                                $discount_schedule_id,
                                $discount['id'] == 'custom' ? null : $discount['id'],
                                $status,
                                floatval($discount['amount']),
                                @$discount['memo']
                            );
                            if($discount['id'] == "custom")
                            {
                                $user_id =  Auth::instance()->get_user()['id'];
                                $activity = new Model_Activity();
                                $activity
                                    ->set_action('use')
                                    ->set_item_type('custom_discount')
                                    ->set_item_id($booking_id)
                                    ->set_user_id($user_id)
                                    ->set_scope_id($data['contact_id'] ?? '0')
                                    ->save();
                            }
                        }
                    }
                }

                // Save records of what discounts are to be ignored
                $ignored_discounts = isset($data['ignored_discounts']) ? $data['ignored_discounts'] : array();
                Model_Kes_IgnoredDiscount::update_ignored_list($booking_id, $ignored_discounts);

                //save transactions
                $errors = array();
                $messages = array();

                if (@$data['frontend_booking'] && $has_payg) {
                    $transactions_schedules[] = $payg_frontend_booking_transaction;
                }
                $booking_amount = 0;
                foreach ($transactions_schedules as $tx_index => $transactions_schedule) {
                    if ($tx_index == 0 && @$data['frontend_booking']) {
                        if ($data['payment_method'] == 'sms') {
                            $transactions_schedule['total'] += $data['sms_booking_fee'];
                            $transactions_schedule['fee'] += $data['sms_booking_fee'];
                        }
                        if ($data['payment_method'] == 'cc') {
                            $transactions_schedule['total'] += $data['cc_booking_fee'];
                            $transactions_schedule['fee'] += $data['cc_booking_fee'];
                        }
                    }

                    $amount = $transactions_schedule['amount'];
                    $existing_transaction = $transaction->get_schedule_transactions($booking_id, $transactions_schedule['schedule'][0]);
                    if ($existing_transaction && $existing_transaction[0]) {
                        $booking_amount += $transactions_schedule['total'];
                        if ($existing_transaction[0]['total'] != $transactions_schedule['total']) {
                            $existing_transaction[0]['total'] = $transactions_schedule['total'];
                            $existing_transaction[0]['discount'] = $transactions_schedule['discount'];
                            $existing_transaction[0]['amount'] = $transactions_schedule['amount'];
                            $existing_transaction[0]['type'] = $transactions_schedule['type'];
                            $transaction->save_history($existing_transaction[0]['id'], $existing_transaction[0]);
                        }
                    } else {
                        $paymentoption = null;
                        if (@$transactions_schedule['paymentoption']) {
                            $paymentoption = $transactions_schedule['paymentoption'];
                            unset($transactions_schedule['paymentoption']);
                        }
                        if (@$data['payment_method'] == 'cc') {
                            $transactions_schedule['payment_method'] = 'cc';
                        }

                        $status_name = Model_Schedules::get_booking_status_label($data['booking_status']);

                        $is_sales_quote = !empty($data['is_sales_quote']) || $status_name == 'Sales Quote';

                        if ($is_sales_quote) {
                            $transactions_schedule['type'] = Model_Kes_Transaction::find_type_id("Quote");
                        }
                        //if (!$is_sales_quote) {
                            $txresult = $transaction->create_transaction($transactions_schedule, $payer);
                        //}


                        if (!empty($txresult['transaction'])) {
                            $booking_amount += $transactions_schedule['total'];
                            $txresults[] = array(
                                'total' => $transactions_schedule['total'],
                                'id' => $txresult['transaction']
                            );
                            if ($paymentoption) {
                                if ($paymentoption['interest_type'] == 'Custom') {
                                    $paymentoption['custom_payments'] = @json_decode($paymentoption['custom_payments'], true);
                                    $installments = array();
                                    foreach ($paymentoption['custom_payments'] as $cpi => $custom_payment) {
                                        if ($cpi == 0 && $paymentoption['deposit'] == 0) {
                                            $paymentoption['deposit'] = (float)$custom_payment['total'];
                                        }
                                        $installments[] = array(
                                            'amount' => (float)$custom_payment['amount'],
                                            'interest' => (float)$custom_payment['interest'],
                                            'total' => (float)$custom_payment['total'],
                                            'due' => $custom_payment['due_date'],
                                        );
                                    }
                                    Model_Kes_Payment::save_payment_plan(null, $txresult['transaction'], $transactions_schedule['total'], $paymentoption['deposit'], 0, $paymentoption['months'], 'custom', 'Fixed', $paymentoption['interest_rate'], date::now(), $installments);
                                } else {
                                    Model_Kes_Payment::save_payment_plan(
                                        null, $txresult['transaction'], $transactions_schedule['total'], $paymentoption['deposit'], 0, $paymentoption['months'], 'months', 'Percent', $paymentoption['interest_rate'], date::now(), null,
                                        $paymentoption['start_after_first_timeslot'] ? date('Y-m-d', strtotime($transactions_schedule['first_timeslot']['datetime_start'])) : null
                                    );
                                }

                                $result['amount'] += $paymentoption['deposit'];
                            } else {
                                $result['amount'] += $transactions_schedule['total'];
                            }
                            $contact = new Model_Contacts3($payer);
                            $payer_name = $contact->get_contact_name();

                            $messages[] = 'Transaction #' . $txresult['transaction'] . ' were created successfully on' . $payer_name . ' account.';
                            $result['transaction_id'] = $txresult['transaction'];
                        }
                    }
                }

                DB::update(Model_KES_Bookings::BOOKING_TABLE)
                    ->set(array('amount' => $booking_amount))
                    ->where('booking_id', '=', $booking_id)
                    ->execute();
                if (class_exists('Model_Document') && count($schedules) > 0) {
                    $doc_helper = new Model_Docarrayhelper();
                    $doc1 = null;
                    try {
                        $doc1 = ORM::factory('Document')
                            ->auto_generate_document($doc_helper->booking_document($booking_id), 0, true);
                    } catch (Exception $exc) {
                        $result['message'] .= $exc->getMessage();
                    }
                    if ($doc1) {
                        $result['message'] .= ' Document Booking Created.';
                    } else {
                        $errors[] = 'An error happened when creating the document.';
                    }

                    $booking_schedules = $booking->get_booking_schedules();

                    foreach ($booking_schedules as $booking_schedule) {
                        // Sending of emails to customer about booking, when saving an existing booking the checkbox hidden and is not checked
                        $data['schedule_id'] = $booking_schedule['schedule_id'];
                        // Send emails to the customer depending on the type of booking, do not send them if the admin wants to give the customer a deposit
                        if(@$data['send_backend_booking_emails'] !== '0' & @$_COOKIE['bookingActionButtonClicked'] !== 'booking_book_and_pay') {
                            $booking->send_booking_emails($data);
                        }

                        $doc_helper = new Model_Docarrayhelper();
                        $teacher_booking_params = $doc_helper->teacher_booking_confirmation2($booking_schedule['id']);
                        $doc2 = null;
                        try {
                            $doc2 = ORM::factory('Document')->auto_generate_document($teacher_booking_params);
                        } catch (Exception $exc) {
                            $result['message'] .= $exc->getMessage();
                        }
                        if ($doc2) {
                            $result['message'] .= 'Document Teacher Booking Confirmation Created.';
                        } else {
                            $errors[] = 'An error happened when creating the document.';
                        }
                        if ($booking_schedule['category'] == 'Grinds/Tutorials' && class_exists('Model_Messaging')) {
                            $mm = new Model_Messaging();
                            try {
                                $mm->send_template(
                                    'teacher-booking-create-notification',
                                    null,
                                    date('Y-m-d H:i:s'),
                                    array(
                                        array(
                                            'target_type' => 'CMS_CONTACT3',
                                            'target' => $teacher_booking_params['contact_id']
                                        )
                                    ),
                                    array('booking_schedule_id' => $booking_schedule['id'])
                                );
                                $result['message'] .= ' Teacher notification were sent to the trainer(s).';
                            } catch (Exception $exc) {
                                $result['message'] .= ' Teacher notification was failed.';
                            }
                        }
                    }
                }
            } else {
                $result = array('status' => 'error', 'message' => 'Could not save the booking.');
            }

            if ($result['status'] == 'success' && is_array($cancel_booking_schedules)) {
                $pay_from_credits = array();
                foreach ($cancel_booking_schedules as $cancel_booking_schedule) {
                    $transfer_booking_id = (int)$cancel_booking_schedule['booking_id'];
                    $transfer_schedule_id = (int)$cancel_booking_schedule['schedule_id'];
                    $transfer_credit = $cancel_booking_schedule['credit'];

                    DB::update(Model_KES_Bookings::BOOKING_SCHEDULES)
                        ->set(array('booking_status' => 3))
                        ->where('booking_id', '=', $transfer_booking_id)
                        ->and_where('schedule_id', '=', $transfer_schedule_id)
                        ->execute();
                    $timeslots = DB::select('id')
                        ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                        ->where('schedule_id', '=', $transfer_schedule_id);
                    DB::update(Model_KES_Bookings::BOOKING_ITEMS_TABLE)
                        ->set(array('booking_status' => 3))
                        ->where('booking_id', '=', $transfer_booking_id)
                        ->and_where('period_id', 'in', $timeslots)
                        ->execute();
                    $cancelled_has_other_schedules = DB::select('*')->from(Model_KES_Bookings::BOOKING_SCHEDULES)
                        ->where('booking_id', '=', $transfer_booking_id)
                        ->and_where('booking_status', '<>', 3)
                        ->execute()
                        ->as_array();
                    if (count($cancelled_has_other_schedules) == 0) {
                        DB::update(Model_KES_Bookings::BOOKING_TABLE)
                            ->set(array('booking_status' => 3))
                            ->where('booking_id', '=', $transfer_booking_id)
                            ->execute();
                    }

                    $transfer_cancel_transaction = ORM::factory('Kes_Transaction')
                        ->get_transaction(null, $transfer_booking_id, $transfer_schedule_id);

                    if (@$transfer_cancel_transaction['id']) {
                        $transfer_cancel_data = array();
                        $transfer_cancel_data['contact_id'] = $data['contact_id'];
                        $transfer_cancel_data['transaction_id'] = $transfer_cancel_transaction['id'];
                        $transfer_cancel_data['booking_id'] = $transfer_booking_id;
                        $transfer_cancel_data['credit_amount'] = is_numeric($transfer_credit) ? $transfer_credit : $transfer_cancel_transaction['payed'];
                        $transfer_cancel_data['transaction_balance'] = 0;
                        $transfer_cancel_data['credit_payment'] = $transfer_cancel_data['credit_amount'] > 0 ? "yes" : "no";
                        $transfer_cancel_data['credit_destination'] = "family";
                        $transfer_cancel_data['note'] = "Transferred to Booking #" . $result['booking_id'];
                        $transfer_cancel_transaction_result = Model_Kes_Transaction::cancel_transaction($transfer_cancel_data);

                        if ($transfer_cancel_transaction['payed'] > 0) {
                            $pay_from_credits[] = array(
                                'cancel_transaction_id' => $transfer_cancel_transaction['id'],
                                'payed' => $transfer_cancel_transaction['payed'],
                                'credit' => $transfer_cancel_data['credit_amount'],
                                'credit_journal' => $transfer_cancel_transaction_result['credit_journal'],
                                'contact_id' => $transfer_cancel_data['contact_id'],
                                'family_id' => $transfer_cancel_transaction['family_id']
                            );
                        }
                    }
                }

                foreach ($txresults as $ti => $tx) {
                    foreach ($pay_from_credits as $pi => $pay_from_credit) {
                        if ($pay_from_credit['credit'] > 0) {
                            $transfer_payment_data = array();
                            $transfer_payment_data['credit'] = 1;
                            $transfer_payment_data['id'] = '';
                            $transfer_payment_data['transaction_id'] = $tx['id'];
                            $transfer_payment_data['transaction_balance'] = min($pay_from_credit['credit'],
                                $tx['total']);
                            $transfer_payment_data['amount'] = min($pay_from_credit['credit'], $tx['total']);
                            $transfer_payment_data['type'] = 'transfer';
                            $transfer_payment_data['bank_fee'] = 0;
                            $transfer_payment_data['status'] = 5;
                            $transfer_payment_data['note'] = 'Transferred from booking #' . $transfer_booking_id;
                            $transfer_payment_data['name_cheque'] = '';
                            $transfer_payment_data['ccName'] = '';
                            $transfer_payment_data['ccType'] = '';
                            $transfer_payment_data['ccNum'] = '';
                            $transfer_payment_data['ccv'] = '';
                            $transfer_payment_data['ccExpMM'] = '';
                            $transfer_payment_data['ccExpYY'] = '';
                            $transfer_payment_data['create_journal'] = '';
                            $transfer_payment_data['journal_type'] = 'contact';
                            $transfer_payment_data['credit_transaction'] = $pay_from_credit['credit_journal'];
                            $transfer_payment_data['contact_id'] = $pay_from_credit['contact_id'];
                            $transfer_payment_data['family_id'] = $pay_from_credit['family_id'];
                            ORM::factory('Kes_Payment')->use_credit($transfer_payment_data);

                            $pay_from_credits[$pi]['credit'] -= $transfer_payment_data['amount'];
                            $txresults[$ti]['total'] -= $transfer_payment_data['amount'];
                        }
                    }
                }
            }

            $linked_contacts = array();
            if(isset($data['coordinator_contact_id']) && $data['coordinator_contact_id'] != "")
                $linked_contacts[] = $data['coordinator_contact_id'];
            
            if(isset($data['host_family_contact_id']) && $data['host_family_contact_id'] != "")
                $linked_contacts[] = $data['host_family_contact_id'];
    
            if (isset($data['agent_contact_id']) && $data['agent_contact_id'] != "")
                $linked_contacts[] = $data['agent_contact_id'];
            
            Model_KES_Bookings::link_contacts_to_booking($booking_id, $linked_contacts);
            
            $result['case'] = $redirect;
            $result['transactions'] = $txresults;
            Database::instance()->commit();
            return $result;
        }
        catch (Exception $exc)
        {
            Database::instance()->rollback();
            Model_errorlog::save($exc);
            Log::instance()->add(Log::ERROR, $exc->getMessage().$exc->getTraceAsString());
            $result['status'] = 'error';
            $result['message'] = __('Unexpected internal error. Please try again. If this problem continues, please ask an administrator to check the error logs.');

            return $result;
        }
    }

    /**
     * Function used to process the booking when year of study and course year do not match
     */
    public function action_ajax_process_booking()
    {
        $this->auto_render = FALSE;
        $this->template->body = null;

        if (class_exists('Debug')) { // a workaround to prevent loading phpdocxpro debug class instead of kohana debug

        }

        $data = $this->request->post();
        if (isset($data['schedules']) AND is_array($data['schedules'])) {
            //workaround forKES-1607: clear duplicate schedules
            $schedules = array();
            foreach ($data['schedules'] as $key => $schedule) {
                if (isset($schedules[$schedule['schedule_id']])) {
                    unset($data['schedules'][$key]);
                } else {
                    $schedules[$schedule['schedule_id']] = $key;
                }
            }
            unset ($schedules);

            // remove rogue data; any schedules that are not in the booking list
            foreach ($data['schedules'] as $key => $schedule) {
                $schedule_id = $schedule['schedule_id'];
                if (!isset($data['booking_items']) OR !array_key_exists($schedule_id, $data['booking_items'])) {
                    unset($data['schedules'][$key]);
                }
            }
        }
        $student_ids = $data['student_ids'];
        unset($data['student_ids']);
        if (!isset($data['payment_method'])) {
            $data['payment_method'] = 'invoice';
        }
        if (@$data['booking_id']) {
            $previos_data = DB::select('*')
                ->from(Model_KES_Bookings::BOOKING_TABLE)
                ->where('booking_id', '=', $data['booking_id'])
                ->execute()
                ->current();
            if ($previos_data) {
                $data['previos_booking_status'] = $previos_data['booking_status'];
                $data['previos_payment_method'] = $previos_data['payment_method'];
                $data['previos_invoice_details'] = $previos_data['invoice_details'];
            }
        }
        try {
            if (is_array($student_ids)) {
                $results = array();
                foreach ($student_ids as $student_id) {
                    $data['contact_id'] = $student_id;
                    $result = $this->process_booking($data);
                    if (@$result['booking_id']) {
                        $this->trigger_checkout($result['booking_id'], $data['contact_id'], $data);
                    }
                    $results[] = $result;
                }
                echo json_encode($results);
            } else {
                $result = $this->process_booking($data);
                if (@$result['booking_id']) {
                    $this->trigger_checkout($result['booking_id'], $data['contact_id'], $data);
                }
            }

        } catch (Exception $exc) {
            $result = array('exception' => $exc->getMessage() . ':' . $exc->getTraceAsString());
        }
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit;
    }

    public function action_ajax_cancel()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();

        $transac = new Model_Kes_Transaction();
        $transaction = $transac->get_booking_transaction($data['booking_id']);

        if ($transaction <= 0) {
            $booking = Model_KES_Bookings::create($data['booking_id']);
            $answer = $booking->cancel();
            if ($answer['result']) {
                $result = array('status' => 'success', 'message' => 'Booking cancelled, and transaction settled.');
            } else {
                $result = array('status' => 'error', 'message' => 'There was an error cancelling the booking');
            }
        } else {
            $result = array('status' => 'outstanding', 'message' => 'The Booking cannot be cancelled. Please pay the outstanding balance of €' . $transaction . '. Or cancel the Transaction.');
        }
        echo json_encode($result);
    }

    public function action_delete()
    {
        $id = $this->request->post('delete_booking_id');
        $booking = Model_KES_Bookings::create($id);
        $booking->delete();
        $alert = IbHelpers::alert('Booking Deleted', 'success');
        $bookings = array();
        $this->template->body = View::factory('/admin/list_bookings')->bind('alert', $alert)->bind('bookings', $bookings);
    }

    // Return booking ID locations for a given customer ID
    // Used for adding flags next to a contact's name
    public function action_get_booking_locations()
    {
        $this->auto_render = FALSE;
        $contact_id = $this->request->post('contact_id');
        $locations = Model_KES_Bookings::get_booking_locations($contact_id);
        $this->response->body(json_encode($locations));
    }

    public function action_prev_week()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $this->response->body(json_encode(array('datetime_start' => date('d-m-Y', strtotime("-1 Week", strtotime($post['datetime_start']))), 'datetime_end' => date('d-m-Y', strtotime("-1 Week", strtotime($post['datetime_end']))))));
    }

    public function action_next_week()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $this->response->body(json_encode(array('datetime_start' => date('d-m-Y', strtotime("+1 Week", strtotime($post['datetime_start']))), 'datetime_end' => date('d-m-Y', strtotime("+1 Week", strtotime($post['datetime_end']))))));
    }

    public function action_get_times()
    {
        $time = $this->request->post('type');
        $this->auto_render = FALSE;
        $date = null;

        switch ($time) {
            case 'last_week':
                $date = array('datetime_start' => date('d-m-Y', strtotime('LAST WEEK')), 'datetime_end' => date('d-m-Y', strtotime('LAST WEEK + 6 DAYS')));
                break;
            case 'next_week':
                $date = array('datetime_start' => date('d-m-Y', strtotime('NEXT WEEK')), 'datetime_end' => date('d-m-Y', strtotime('NEXT WEEK + 6 DAYS')));
                break;
            case 'current_week':
            default:
                $date = array('datetime_start' => date('d-m-Y', strtotime('THIS WEEK')), 'datetime_end' => date('d-m-Y', strtotime('THIS WEEK + 6 DAYS')));
                break;
        }

        $this->response->body(json_encode($date));
    }


    //
    // DISCOUNTS
    //


    public function action_list_discounts()
    {
        Ibhelpers::permission_redirect('bookings_discounts');

        $discounts   = Model_KES_Discount::get_all_discounts_for_listing();
        $stylesheets = array(
            URL::get_engine_plugin_assets_base('bookings') . 'admin/css/eventCalendar.css' => 'screen',
            URL::get_engine_plugin_assets_base('contacts3') . 'css/contacts.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings') . 'admin/css/bookings.css' => 'screen'
        );
        $this->template->sidebar->breadcrumbs = array(array('name' => 'Home', 'link' => 'admin'), array('name' => 'Bookings', 'link' => 'admin/bookings'), array('name'=>'Discounts', 'link'=>'/admin/bookings/list_discounts'));
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'js/list_discounts.js"></script>';
        $this->template->body = View::factory('/admin/discounts/list_discounts')->bind('discounts', $discounts);
    }

    public function action_add_edit_discount()
    {
        $id = $this->request->param('id');
        $discount = Model_KES_Discount::create($id);
        // $discount_types = Model_KES_Discount::get_all_discount_types();
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/image_edit.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/multiple_upload.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('bookings') . 'admin/js/add_edit_discount.js"></script>';
        $stylesheets = array(
            URL::get_engine_plugin_assets_base('contacts3') . 'css/bootstrap-multiselect.css' => 'screen',
            URL::get_engine_plugin_assets_base('contacts3') . 'css/validation.css' => 'screen',
        );
        $this->template->sidebar->breadcrumbs = array(array('name' => 'Home', 'link' => 'admin'), array('name' => 'Bookings', 'link' => 'admin/bookings'), array('name'=>'Discounts', 'link'=>'/admin/bookings/list_discounts'));
        $this->template->styles = array_merge($this->template->styles, $stylesheets);
        $this->template->body   = View::factory('/admin/discounts/add_edit_discount')->bind('discount', $discount);
    }

    public function action_save_discount()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        if (@$post['course_date_from']) {
            @$post['course_date_from'] = date::dmy_to_ymd(@$post['course_date_from']);
        }
        if (@$post['course_date_to']) {
            @$post['course_date_to'] = date::dmy_to_ymd(@$post['course_date_to']);
        }
        if (isset($post['for_contacts'])) {// some cleanup, handle empty input
            if ($post['for_contacts'][0] == 0 || $post['for_contacts'][0] == '') {
                unset ($post['for_contacts'][0]);
            }
        }

        if (@$post['has_schedules'] || @$post['has_courses'] || @$post['categories']) {
            $post['is_package'] = 1;
        }
        $id = Model_KES_Discount::create()->set($post)->save();
        if ($post['redirect'] == 'save' AND is_numeric($id)) {
            $this->request->redirect('/admin/bookings/add_edit_discount/' . $id);
        } else {
            $this->request->redirect('/admin/bookings/list_discounts');
        }


    }

    public function action_cancel_booking()
    {
        $this->auto_render = false;
        $data = $this->request->post('booking_id');
    }

    public function action_get_booking_labels()
    {
        $this->auto_render = false;
        $data = $this->request->post('booking_id');
        $booking_status = Model_KES_Bookings::create($data)->get_booking_status();
        $status = Model_Schedules::get_booking_status_label($booking_status);
        $this->response->body(json_encode(array('status' => $status)));
    }

    public function action_get_booking_amount()
    {
        $this->auto_render = false;
        $data = $this->request->post('booking_id');
        $booking_amount = Model_KES_Bookings::create($data)->get_booking_amount();
        $this->response->body(json_encode(array('amount' => $booking_amount)));
    }

    public function action_get_additional_booking_label()
    {
        session_commit();
        $this->auto_render = false;
        $data = $this->request->post();
        $return = '';
        $bookings = Model_KES_Bookings::create($data['booking_id']);
        $count = $bookings->get_additional_booking_details();
        if (count($count) > 0) {
            $label = Model_KES_Bookings::get_label_text(6);
            $return = '<span class="label location-flag">' . $label . '</span>';
        }

        $this->response->body($return);
    }

    public function action_get_bill_payer()
    {
        session_commit();
        $this->auto_render = false;
        $data = $this->request->post();
        $return = '';
        $billed = ORM::factory('Kes_Transaction')->booking_is_billed($data['booking_id']);
        if ($billed) {
            $label = ORM::factory('Kes_Transaction')->bill_payer_full_name($data['booking_id']);
            $return = '<span class="label location-flag">Bill Payer: ' . $label . '</span>';
        }
        $this->response->body($return);
    }

    /**
     * Used to display family details under the list of bookings, when a booking is clicked
     */
    public function action_ajax_display_family_details()
    {
        $this->auto_render = FALSE;
        $family_id = $this->request->param('id');
        if (is_null($family_id)) {
            $booking_id = $this->request->post('booking_id');
            $booking = new Model_KES_Bookings($booking_id);
            $contact_id = $booking->get_contact_details_id();
            $contact = new Model_Contacts3($contact_id);
            $family_id = $contact->get_family_id();
        } else {
            $contact_id = NULL;
            $contact = new Model_Contacts3();
        }
        $family = new Model_Family($family_id);
        $nonchildren = $family->get_nonchildren();
        $view = View::factory('/admin/list_contacts_details')->set(array(
            'contact' => $contact,
            'family' => $family,
            'nonchildren' => $nonchildren,
            'notifications' => $family->get_contact_notifications(),
            'family_members' => ((is_null($family_id))
                ? Model_Contacts3::get_all_contacts(array(array('contact.id', '=', $contact_id)))
                : Model_Contacts3::get_all_contacts(array(array('contact.family_id', '=', $family_id)))
            ),
            'residence' => new Model_Residence($family->get_residence()),
            'notification_types' => Model_Contacts3::get_notification_types(),
            'alert' => IbHelpers::get_messages()
        ));
        $this->response->body($view);
    }

    public function action_ajax_filter_rooms()
    {
        $this->auto_render = FALSE;
        $selected_id = $this->request->param('id');
        $location_id = $this->request->query('location_id');
        $sublocation_ids = Model_Locations::get_all_sublocation_ids($location_id);
        $rooms = Model_KES_Bookings::get_all_rooms();
        $return = '<option value="">All Rooms</option>';
        foreach ($rooms as $room) {
            if (in_array($room['id'], $sublocation_ids) OR $location_id == '') {
                $selected = ($room['id'] == $selected_id) ? ' selected="selected"' : '';
                $return .= '<option value="' . $room['id'] . '"' . $selected . '>' . $room['name'] . '</option>';
            }
        }
        echo $return;
    }

    public function action_ajax_toggle_discount_publish()
    {
        $this->auto_render = FALSE;
        try {
            // Set new publish value and save
            $discount = new Model_KES_Discount($this->request->post('id'));
            $discount->set_publish($this->request->post('publish'))->save();
            IbHelpers::set_message('Discount #' . $discount->get_id() . ' ' . (($discount->get_publish() != 1) ? 'unpublished' : 'published') . '.', 'success');
        } catch (Exception $e) {
            IbHelpers::set_message('Failed to change publish status', 'error');
        }

        echo IbHelpers::get_messages();
    }

    public function action_ajax_delete_discount()
    {
        $this->auto_render = FALSE;
        try {
            $discount = new Model_KES_Discount($this->request->param('id'));
            $discount->set_publish(0)->set_delete(1)->save();
            IbHelpers::set_message('Discount #' . $discount->get_id() . ' deleted', 'success');
        } catch (Exception $e) {
            IbHelpers::set_message('Failed to delete discount', 'error');
        }
        echo IbHelpers::get_messages();
    }

    public function action_ajax_get_datatable()
    {
        session_commit();
        $this->auto_render = FALSE;
        $this->response->body(Model_KES_Bookings::get_for_datatable($this->request->query()));
    }

    public function action_list_settlements()
    {
        $settlements = Model_Kes_Settlement::list_settlements();
        $this->template->body = View::factory('/admin/list_settlements')->bind('settlements', $settlements);
    }

    public function action_settlement_details()
    {
        $settlement_data = Model_Kes_Settlement::settlement_details($this->request->query('id'));
        //header('content-type: text/plain');print_r($settlement_data);die();
        $this->template->body = View::factory('/admin/settlement_details')->bind('settlement_data', $settlement_data);
    }

    public function action_ajax_rollcall_update()
    {
        $bookings = $this->request->post('rollcall');
        //$bookings = json_decode($post['json'], true);
        //$update_accounts = @$post['dont_update_accounts'] != '1';
        //$update_attendance = @$post['dont_update_attendance'] != '1';
        if (!Auth::instance()->has_access('courses_finance')) {
            $update_accounts = false;
        }
        Model_KES_Bookings::rollcallUpdate($bookings);
        echo "Roll Call has been updated";
        exit();
    }

    public function action_show_cancel_booking_multiple()
    {
        $this->auto_render = false;
        $data = $this->request->post();
        $booking_id = $data['booking_id'];
        $dschedules = @$data['schedule_id'] ?: array();

        $booking = Model_KES_Bookings::get_details($booking_id);

        if ($booking && count($booking['schedules']) > 0) {
            foreach ($booking['schedules'] as $i => $eschedule) {
                $etransaction = ORM::factory('Kes_Transaction')
                    ->get_transaction(null, $booking['booking_id'], $eschedule['id']);
                $booking['schedules'][$i]['default_transfer_credit'] = @$etransaction['payed'];
                $booking['schedules'][$i]['outstanding'] = @$etransaction['outstanding'];
            }
        }


        $view = View::factory('admin/snippets/cancel_booking_multiple_modal_form')
            ->set('booking', $booking)
            ->set('dschedules', $dschedules);

        echo $view;
    }

    public function action_cancel_booking_multiple()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $alerts = [];
        $booking_id = $this->request->post('booking_id');
        $reason_code = $this->request->post('reason_code');
        $credit_to_family_id = $this->request->post('credit_to_family_id');
        $note = $this->request->post('note');

        /* Cancel the schedules */
        $cb_schedules = $this->request->post('cancel_booking_schedule');
        if (!empty($cb_schedules))
        foreach ($cb_schedules as $i => $cb_schedule) {
            if (!isset($cb_schedule['confirm']) || $cb_schedule['confirm'] == 0) {
                unset($cb_schedules[$i]);
            }
        }
        $cb_schedules = array_values($cb_schedules);

        if (!empty($cb_schedules)) {
            $results = Model_KES_Bookings::cancel_booking_schedules([
                'schedules' => $cb_schedules,
                'note' => $note,
                'reason_code' => $reason_code,
                'credit_to_family_id' => $credit_to_family_id
            ]);

            $alerts[] = ['type' => 'success', 'message' => 'Schedule has been cancelled'];
        }

        /* Cancel the delegates */
        $cb_delegate_ids = array_keys($this->request->post('cancel_booking_delegate') ?? []);

        if (!empty($cb_delegate_ids)) {
            Model_KES_Bookings::cancel_booking_delegates([
                'booking_id'   => $booking_id,
                'delegate_ids' => $cb_delegate_ids,
                'note'         => $note,
                'reason_code'  => $reason_code,
                'credit_to_family_id' => $credit_to_family_id,
                'do_not_trigger_available' => $cb_schedules
            ]);

            $alerts[] = ['type' => 'success', 'message' => 'Delegates have been cancelled'];
        }

        echo json_encode(['alerts' => $alerts]);
    }

    public function action_fix_period_booking_items()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'text/plain; charset=utf-8');

        Model_KES_Bookings::fix_period_bookings();
        echo "period bookings have been updated";
    }

    public function action_validate_coupon()
    {
        $post = $this->request->post();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $result = array(
            'success' => Model_KES_Discount::validate_coupon($post['discount']['code'])
        );
        echo json_encode($result);
    }

    public function action_coupon_autocomplete()
    {
        $term = $this->request->query('term');
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $discounts = Model_KES_Discount::search(array('term' => $term));
        $result = array();
        foreach ($discounts as $discount) {
            $result[] = array('id' => $discount['id'], 'label' => $discount['code']);
        }
        echo json_encode($result);
    }

    public function action_check_schedule_capacity()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $schedule_id = $this->request->post('schedule_id');
        $timeslot_id = $this->request->post('timeslot_id');
        $contact_id = $this->request->post('contact_id');
        $quantity = $this->request->post('quantity');
        if (!is_numeric($quantity)) {
            $quantity = 1;
        }

        if ($quantity == 1 && count(Model_KES_Bookings::check_duplicate_booking($contact_id, $schedule_id, array($timeslot_id))) > 0) {
            $result['error'] = 'Duplicate booking';
            $result['duplicate'] = true;
        } else {
            $result = Model_KES_Bookings::check_schedule_capacity($this->request->post());
        }

        echo json_encode($result);
    }

    public function action_alert()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json');

        $return = array(
            'success'   => false,
            'message'   => '',
            'timeslots' => array()
        );

        try
        {
            $post = $this->request->post();
            $course_id = @$post['course_id'];
            $schedule_id = @$post['schedule_id'];
            $trainer_id = @$post['trainer_id'];
            $interval = @$post['interval'];
            $interval_amount = (int)$post['interval_amount'];
            $type = $post['type'];
            $message = $post['message'];

            $selectq = DB::select(
                'e.id',
                array('c.title', 'course'),
                array('s.name', 'schedule'),
                array('e.datetime_start', 'datetime'),
                DB::expr("CONCAT_WS(' ', pl.`name`, l.name) AS `location`"),
                DB::expr("CONCAT_WS(' ', t.title, t.first_name, t.last_name) AS `trainer`"),
                array('t.id', 'trainer_id'),
                array('n.value', 'mobile')
            )
                ->from(array('plugin_courses_schedules', 's'))
                ->join(array('plugin_courses_courses', 'c'), 'inner')->on('s.course_id', '=', 'c.id')
                ->join(array('plugin_courses_schedules_events', 'e'), 'inner')->on('s.id', '=', 'e.schedule_id')
                ->join(array('plugin_contacts3_contacts', 't'), 'inner')->on('e.trainer_id', '=', 't.id')
                ->join(array('plugin_contacts3_contact_has_notifications', 'n'), 'inner')->on('t.notifications_group_id', '=', 'n.group_id')->on('n.notification_id', '=', DB::expr(2))
                ->join(array('plugin_courses_locations', 'l'), 'left')->on('s.location_id', '=', 'l.id')
                ->join(array('plugin_courses_locations', 'pl'), 'left')->on('l.parent_id', '=', 'pl.id')
                ->where('s.delete', '=', 0)
                ->and_where('c.deleted', '=', 0)
                ->and_where('e.delete', '=', 0)
                ->and_where('e.datetime_start', '>=', date::now())
                ->and_where('e.datetime_start', '<=', DB::expr('\'DATE_ADD(NOW(),INTERVAL ' . $interval_amount . ' ' . $interval . '\''));

            if ($course_id) {
                $selectq->and_where('s.course_id', 'in', array($course_id));
            }

            if ($schedule_id) {
                $selectq->and_where('s.id', 'in', array($schedule_id));
            }

            if ($trainer_id) {
                $selectq->and_where('t.id', 'in', array($trainer_id));
            }

            $timeslots = $selectq->execute()->as_array();
            foreach ($timeslots as $timeslot) {
                $rmessage = $message;
                foreach ($timeslot as $param => $value) {
                    $rmessage = str_replace('$' . $param, $value, $rmessage);
                }

                if (@$type['sms']) {
                    $mm = new Model_Messaging();
                    $mm->send(
                        'sms',
                        'default',
                        null,
                        array(
                            array('target_type' => 'CMS_CONTACT3', 'target' => $timeslot['trainer_id'])
                        ),
                        $rmessage,
                        '',
                        date::now()
                    );
                }
                if (@$type['email']) {
                    $mm = new Model_Messaging();
                    $mm->send(
                        'email',
                        'default',
                        @Settings::instance()->get('default_email_sender'),
                        array(
                            array('target_type' => 'CMS_CONTACT3', 'target' => $timeslot['trainer_id'])
                        ),
                        $rmessage,
                        '',
                        date::now()
                    );
                }
            }

            if (count($timeslots)) {
                $return['success']   = true;
                $return['message']   = __('Message has been sent.');
                $return['timeslots'] = $timeslots;
            }
            else
            {
                $return['success'] = false;
                $return['message'] = __('There are no timeslots matching the criteria you have specified. Because of this no messages could be sent.');
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, 'Error sending alert'."\n".$e->getMessage()."\n".$e->getTraceAsString());

            $return['success'] = false;
            $return['message'] = __('Unexpected internal error. Please try again. If this problem continues, please ask an administrator to check the error logs.');
            
        }

        echo json_encode($return);
    }

    public function action_search_schedules_datatable()
    {
        $this->auto_render = false;
        session_commit();
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        if (isset($post['sEcho'])) {
            $return['sEcho'] = $post['sEcho'];
        }

        $sort = 0;
        switch ($post['iSortCol_0'])
        {
            case 0: $sort = 'id';
                break;
            case 1: $sort = 'course';
                break;
            case 2: $sort = 'name';
                break;
            case 3: $sort = 'category';
                break;
            case 4: $sort = 'fee_amount';
                break;
            case 5: $sort = 'repeat_name';
                break;
            case 6: $sort = 'start_date';
                break;
            case 7: $sort = 'location';
                break;
            case 9: $sort = 'trainer';
                break;
            case 10: $sort = 'is_confirmed';
                break;
            case 11: $sort = 'date_modified';
                break;
        }
        $return = Model_KES_Bookings::search_schedules($post['iDisplayLength'], $post['iDisplayStart'], $post['iSortCol_0'], $post['sSortDir_0'], $post['sSearch'],$post);

        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
    }

    public function action_get_schedule_details()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $return = Model_KES_Bookings::get_schedule($post['schedule_id'], @$post['timeslot_ids']);

        echo json_encode($return);
    }

    public function action_send_payment_plan_due_emails()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $transaction_ids = $this->request->post('transaction_id');

        $payments = array();
        foreach ($transaction_ids as $transaction_id) {
            $payments = array_merge($payments, Model_KES_Bookings::send_payment_plan_due_email($transaction_id));
        }
        echo json_encode($payments, JSON_PRETTY_PRINT);
    }

    public function action_ajax_search_schedules()
    {
        $this->auto_render = false;
        $return = array();

        $schedules = Model_Schedules::search(array(
            'publish' => 1,
            'term' => $this->request->query('term'),
            'order_by' => 'schedules.name',
            'limit' => 10,
        ));

        foreach ($schedules as $schedule) {
            $return[] = array('title' => $schedule['name']);
        }

        echo json_encode($return);
    }

    public function action_ajax_search_trainers()
    {
        $this->auto_render = false;
        $return = array();

        $trainers = Model_Contacts3::get_teachers(array(
            'publish' => 1,
            'term' => $this->request->query('term'),
            'limit' => 10
        ));

        foreach ($trainers as $trainer) {
            $return[] = array('title' => trim($trainer['first_name'].' '.$trainer['last_name']));
        }

        echo json_encode($return);
    }

    public function action_ajax_load_application_details()
    {
        $this->auto_render = false;
        $application_id = $this->request->post('application_id');
        $type = $this->request->post('type');
        $contact_id = $this->request->post('contact_id');

        if ($application_id == 'new') {
            $application = array(
                'booking_id' => 'new',
                'interview_status' => null,
                'fulltime' => $type == 'fulltime',
                'schedules' => array(),
                'status_id' => 'new'
            );
        } else {
            $application = Model_KES_Bookings::get_application_details($application_id);
        }

        $transactions = Model_Kes_Transaction::search(array('booking_id' => $application['booking_id']));
        $custom_checkout = Settings::instance()->get('checkout_customization');
        $view = View::factory('admin/bookings/edit_application_details');
        $view->application = $application;
        $view->extra_data = @$application['extra_data'];
        $view->contact_id = $contact_id;
        $view->custom_checkout = $custom_checkout;
        $view->transactions = $transactions;
        echo $view;
    }

    public function action_update_application()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $post = $this->request->post();
        $success = Model_KES_Bookings::update_application($post);
        echo json_encode(array('success' => $success));
    }

    public function action_check_duplicate()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $data = array(
            'duplicate' => true
        );

        $duplicate_bookings = Model_KES_Bookings::check_duplicate_booking(
            $this->request->post('contact_id'),
            $this->request->post('schedule_id')
        );
        if (count($duplicate_bookings) == 0) {
            $data['duplicate'] = false;
        }
        echo json_encode($data);
    }

    public function action_import_interview_old_data()
    {
        $this->auto_render = false;
        Model_KES_Bookings::import_interview_old_data();
    }

    public function action_interviewscsv()
    {
        header('content-type: text/plain');
        header('Content-Disposition: attachment; filename="interviews-' . date("YmdHis") . '.csv"');
        $interviews = DB::select('*')
            ->from('plugin_messaging_messages')
            ->where('subject', '=', 'New Course Interview Application')
            ->order_by('id', 'asc')
            ->execute()
            ->as_array();
        $tmp  = tmpfile();
        $first = true;
        foreach ($interviews as $interview) {
            $interview = json_decode($interview['form_data'], true);
            $row = array(
                'First Name' => $interview['student_first_name'],
                'Last Name' => $interview['student_last_name'],
                'Address' => $interview['address1'] . ' ' . $interview['address2'] . ' ' . $interview['town'] . ' ' . $interview['postcode'],
                'Country' => $interview['country'],
                'Email' => $interview['student_email'],
                'Mobile' => $interview['mobile'],
                'DOB' => $interview['student']['dob'],
                'Nationality' => $interview['student']['nationality_id'],
                'Special Needs' => is_array($interview['student']['preferences_medical']) ? (in_array(14, $interview['student']['preferences_medical']) ? 'Yes' : 'No') : 'No',
                'Course CODE' => $interview['course_code'],
                'Course' => DB::select('title')->from('plugin_courses_courses')->where('code', '=', $interview['course_code'])->execute()->get('title')
            );
            if ($first) {
                $first = false;
                fputcsv($tmp, array_keys($row));
            }
            fputcsv($tmp, $row);
        }
        fseek($tmp, 0, 0);
        while(!feof($tmp)) {
            echo fgets($tmp);
        }
        fclose($tmp);
        exit;
    }

    public function action_set_interview_timeslot()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $booking_id = $this->request->post('booking_id');
        $timeslot_id = $this->request->post('timeslot_id');
        $id = Model_KES_Bookings::set_interview_timeslot($booking_id, $timeslot_id);
        echo json_encode(array('id' => $id));
    }

    public function action_set_interview_timeslots_missing()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        Model_KES_Bookings::set_interview_timeslots_missing();
    }

    public function action_set_interview_status_bulk()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $interviews = $this->request->post('interviews');
        Model_KES_Bookings::set_interview_status_bulk($interviews);
        echo json_encode($interviews, JSON_PRETTY_PRINT);
    }


    public function action_interview_details_doc()
    {
        $this->auto_render = false;
        $booking_id = $this->request->query('booking_id');

        $da = new Model_Docarrayhelper();
        $details = $da->interview_details($booking_id);

        $document = new Model_Document();
        $document->doc_gen_and_storage(
            Model_Files::getFileId('/templates/Interview_Details'),
            $details,
            '',
            'interview_details',
            0,
            '',
            1,
            true
        );

        if (@$document->generated_documents['url_docx']) {
            $this->response->headers('Content-disposition', 'attachment; filename="' . $document->generated_documents['file'] . '"');
            $this->response->headers('Content-type', 'application/vnd.openxmlformats…ent.wordprocessingml.document');
            readfile($document->generated_documents['url_docx']);
        }
    }

    public function action_booking_linked_schedules()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $booking_id = $this->request->post('booking_id');
        $schedules = DB::select('schedules.id', 'schedules.name')
            ->distinct('schedules.id')
            ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'))
                ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('items.period_id', '=', 'timeslots.id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('timeslots.schedule_id', '=', 'schedules.id')
            ->where('items.delete', '=', 0)
            ->and_where('items.booking_id', '=', $booking_id)
            ->order_by('schedules.name')
            ->execute()
            ->as_array();

        echo json_encode($schedules, JSON_PRETTY_PRINT);
    }

    public function action_send_accreditation_application_email()
    {
        $this->auto_render = false;
        $booking_id   = $this->request->param('id');
        $delegate_id  = $this->request->param('toggle');
        $booking      = new Model_Booking_Booking($booking_id);
        $contact      = $delegate_id ? new Model_Contacts3_Contact($delegate_id) : $booking->applicant;
        $messaging    = new Model_Messaging();
        $form_page    = new Model_Page(Settings::instance()->get('accreditation_application_page'));
        $project_name = Settings::instance()->get('project_name');
        $theme        = Model_Engine_Theme::get_current_theme();
        //create application record and save it with Enquiry status (asked to fill meant)
        $application = new Model_Booking_Application();
        $application->booking_id = $booking_id;
        $application->delegate_id = $delegate_id;
        $data = array();
        $schedule = $booking->schedules->find();
        $data['has_course_id']   = $schedule->course_id;
        $data['has_schedule_id'] = $schedule->id;
        $data['contact_id'] = $contact->id;
        $data['booking_id'] = $booking_id;
        $data['schedule_id'] = $schedule->id;
        $application->data = json_encode($data);
        $status_id = Model_KES_Bookings::ENQUIRY;
        if (@$data['has_course_id']) {
        $cinserted = DB::insert(Model_KES_Bookings::BOOKING_COURSES)
            ->values(array(
                'booking_id' => $booking_id,
                'course_id' => $data['has_course_id'],
                'deleted' => 0,
                'booking_status' => 1
            ))->execute();
        }
        $application->status_id = $status_id;
        $application->save_with_history('status_id', $status_id);
        // Need to replace this with something more secure.
        $registration_link = URL::base().$form_page->name_tag.'?booking_id='.$booking_id.'&contact_id='.$contact->id;

        // Prepare information for the email
        $recipients  = [['target_type' => 'CMS_CONTACT3', 'target' => $contact->id, 'x_details' => 'to']];
        $template_parameters = [
            'end_date'          => IbHelpers::formatted_time($booking->get_end_date(), ['time' => false]),
            'name'              => $contact->get_full_name(),
            'project_name'      => $project_name ? $project_name : URL::base(),
            'primary_color'     => $theme->get_variable('primary'),
            'registration_link' => $registration_link,
            'schedule_name'     => $booking->schedules->find()->name,
            'site_link'         => URL::base(),
        ];

        $messaging->send_template('course_accreditation_application', null, null, $recipients, $template_parameters);
        echo "sent";
    }
}

?>