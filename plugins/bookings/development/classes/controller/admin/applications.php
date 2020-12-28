<?php

class Controller_Admin_Applications extends Controller_Cms
{
    function before()
    {
        parent::before();

        if (!Auth::instance()->has_access('applications')) {
            Ibhelpers::set_message('You need access to the <code>applications</code> permission to access this feature.', 'error popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home',         'link' => '/admin'],
            ['name' => 'Applications', 'link' => '/admin/applications']
        ];

        if (true) { // Add permission check
            $menus[0] = ['name' => 'All applications', 'link' => '/admin/applications',            'icon' => 'all-requests'];
            $menus[2] = ['name' => 'All interviews',   'link' => '/admin/applications/interviews', 'icon' => 'credit'];
            $menus[4] = ['name' => 'All offers',       'link' => '/admin/applications/offers',     'icon' => 'manage-all-todos'];
        }

        if (true) { // Add permission check
            $menus[1] = ['name' => 'My applications', 'link' => '/admin/applications/my_applications', 'icon' => 'study-mode'];
            $menus[3] = ['name' => 'My interviews',   'link' => '/admin/applications/my_interviews',   'icon' => 'my-requests'];
            $menus[5] = ['name' => 'My offers',       'link' => '/admin/applications/my_offers',       'icon' => 'settlements'];
        }

        // Order items by array key. e.g. 0, 2, 4, 1, 3, 5 =>  0, 1, 2, 3, 4, 5
        ksort($menus);

        // Remove gaps in array keys. e.g. 1, 3, 5 => 0, 1, 2
        $this->template->sidebar->menus = [array_values($menus)];

        $this->daterange_start = date('Y-01-01');
        $this->daterange_end   = date('Y-12-31');
    }

    function after()
    {
        $this->template->body->status_groups = Model_Booking_Application::get_all_statuses();
        $this->template->body->daterange_start = $this->daterange_start;
        $this->template->body->daterange_end   = $this->daterange_end;

        parent::after();
    }

    public function action_index($args = [])
    {
        $args['user_only'] = isset($args['user_only']) ? $args['user_only'] : false;

        $this->template->sidebar->breadcrumbs[] = ['name' => ($args['user_only'] ? 'My applications' : 'All applications'), 'link' => '#'];

        $stylesheets = array(
            URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('timeoff').'css/timeoff.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = URL::get_engine_plugin_asset('bookings', 'admin/js/applications.js', ['cachebust' => true, 'script_tags' => true]);
        $this->template->body      = View::factory('admin/applications/applications');

        $this->template->body->access_actions = !$args['user_only']; // User's access to the actions (edit, change status) column
        $this->template->body->courses        = Model_Courses::get_all_published();
        $this->template->body->schedules      = Model_Schedules::get_all_schedules(['publish' => '1']);
        $this->template->body->stage          = 'application';
        $this->template->body->reports        = Model_Booking_Application::get_application_reports(['start_date' => $this->daterange_start,'end_date' => $this->daterange_end]);
    }

    public function action_my_applications()
    {
        self::action_index(['user_only' => true]);
    }

    public function action_interviews($args = [])
    {
        $args['user_only'] = isset($args['user_only']) ? $args['user_only'] : false;

        $this->template->sidebar->breadcrumbs[] = ['name' => ($args['user_only'] ? 'My interviews' : 'All interviews'), 'link' => '#'];

        $stylesheets = array(
            URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('timeoff').'css/timeoff.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = URL::get_engine_plugin_asset('bookings', 'admin/js/applications.js', ['cachebust' => true, 'script_tags' => true]);
        $this->template->body      = View::factory('admin/applications/applications');

        $this->template->body->academic_periods = Model_AcademicYear::get_all();
        $this->template->body->access_actions   = !$args['user_only'];
        $this->template->body->courses          = Model_Courses::get_all_published();
        $this->template->body->reports          = Model_Booking_Application::get_interview_reports(['start_date' => $this->daterange_start,'end_date' => $this->daterange_end]);
        $this->template->body->schedules        = Model_Schedules::get_all_schedules(['publish' => '1']);
        $this->template->body->stage            = 'interview';
    }

    public function action_my_interviews()
    {
        self::action_interviews(['user_only' => true]);
    }

    public function action_offers($args = [])
    {
        $args['user_only'] = isset($args['user_only']) ? $args['user_only'] : false;

        $this->template->sidebar->breadcrumbs[] = ['name' => $args['user_only'] ? 'My offers' : 'All offers', 'link' => '#'];

        $stylesheets = array(
            URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('timeoff').'css/timeoff.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = URL::get_engine_plugin_asset('bookings', 'admin/js/applications.js', ['cachebust' => true, 'script_tags' => true]);
        $this->template->body      = View::factory('admin/applications/applications');

        $this->template->body->access_actions = !$args['user_only'];
        $this->template->body->courses        = Model_Courses::get_all_published();
        $this->template->body->schedules      = Model_Schedules::get_all_schedules(['publish' => '1']);
        $this->template->body->reports        = Model_Booking_Application::get_interview_reports(['start_date' => $this->daterange_start,'end_date' => $this->daterange_end]);
        $this->template->body->stage          = 'offer';
    }

    public function action_my_offers()
    {
        self::action_offers(['user_only' => true]);
    }

    public function action_ajax_get_submenu()
    {
        $return['items'] = [];

        if (true) { // Add permission check
            $return['items'][0] = ['title' => 'All applications', 'link' => '/admin/applications',            'icon_svg' => 'all-requests'];
            $return['items'][2] = ['title' => 'All interviews',   'link' => '/admin/applications/interviews', 'icon_svg' => 'credit'];
            $return['items'][4] = ['title' => 'All offers',       'link' => '/admin/applications/offers',     'icon_svg' => 'manage-all-todos'];
        }

        if (true) { // Add permission check
            $return['items'][1] = ['title' => 'My applications', 'link' => '/admin/applications/my_applications', 'icon_svg' => 'study-mode'];
            $return['items'][3] = ['title' => 'My interviews',   'link' => '/admin/applications/my_interviews',   'icon_svg' => 'my-requests'];
            $return['items'][5] = ['title' => 'My offers',       'link' => '/admin/applications/my_offers',       'icon_svg' => 'settlements'];
        }

        // Order items by array key. e.g. 0, 1, 2, 3, 4, 5
        ksort($return['items']);

        // Remove gaps in array keys. e.g. 1, 3, 5 => 0, 1, 2
        $return['items'] = array_values($return['items']);

        return $return;
    }

    public function action_ajax_get_interview_details()
    {
        $this->auto_render = false;
        $application = ORM::factory('Booking_Application')->find_filtered(['booking_id' => $this->request->query('booking_id')]);
        $schedule_id = isset($application->schedule_id) ? $application->schedule_id : '';
        $timeslots   = Model_KES_Bookings::get_schedule_timeslots($schedule_id);

        echo json_encode([
            'academic_period_id' => isset($application->academic_year_id)  ? $application->academic_year_id   : '',
            'applicant_name'     => isset($application->applicant_name)    ? $application->applicant_name     : '',
            'booking_id'         => $application->booking_id,
            'course_id'          => isset($application->course_id)         ? $application->course_id          : '',
            'interview_slot_id'  => isset($application->interview_slot_id) ? $application->interview_slot_id  : '',
            'schedule_id'        => $schedule_id,
            'timeslots'          => self::prepare_timeslots_for_ajax_response($timeslots)
        ]);
    }

    public function action_ajax_get_schedule_timeslots()
    {
        $this->auto_render = false;
        $timeslots = Model_KES_Bookings::get_schedule_timeslots($this->request->query('schedule_id'));
        echo json_encode(self::prepare_timeslots_for_ajax_response($timeslots));
    }

    // Reduce the amount of data that gets returned and format a label
    function prepare_timeslots_for_ajax_response($timeslots)
    {
        foreach ($timeslots as &$timeslot) {
            $label  = date('D j M Y H:i', strtotime($timeslot['datetime_start']));
            $label .= trim($timeslot['trainer']) ? ' - '.trim($timeslot['trainer']) : '';
            $label .= trim($timeslot['room'])    ? ' - '.trim($timeslot['room'])    : '';
            $label .= ($timeslot['max_capacity'] > 0) ? ' (' . ((int)$timeslot['booking_count']) . ' / ' . $timeslot['max_capacity'] . ')' : '';

            $timeslot = [
                'booking_count'  => $timeslot['booking_count'],
                'id'             => $timeslot['id'],
                'datetime_start' => $timeslot['datetime_start'],
                'datetime_end'   => $timeslot['datetime_end'],
                'room'           => $timeslot['room'],
                'trainer'        => $timeslot['trainer'],
                'label'          => $label,
                'max_capacity'   => $timeslot['max_capacity']
            ];
        }

        return $timeslots;
    }

    public function action_ajax_get_reports_data()
    {
        $this->auto_render = false;
        $filters = $this->request->query('filters');
        $stage   = isset($filters['stage']) ? $filters['stage'] : '';

        switch ($stage) {
            case 'interview': echo json_encode(Model_Booking_Application::get_interview_reports($filters));   break;
            case 'offer':     echo json_encode(Model_Booking_Application::get_offer_reports($filters));       break;
            default:          echo json_encode(Model_Booking_Application::get_application_reports($filters)); break;
        }
    }

    public function action_ajax_get_applications_datatable()
    {
        $this->auto_render = false;
        $filters = $this->request->query('filters');
        $filters['interview_statuses'] = ['Pending'];

        echo json_encode(Model_Booking_Application::get_for_datatable($filters, $this->request->query()));
    }

    public function action_ajax_get_interviews_datatable()
    {
        $this->auto_render = false;
        $filters = $this->request->query('filters');
        $filters['application_statuses'] = ['Accepted'];
        $filters['offer_statuses']       = ['Pending'];

        echo json_encode(Model_Booking_Application::get_for_interviews_datatable($filters, $this->request->query()));
    }

    public function action_ajax_get_offers_datatable()
    {
        $this->auto_render = false;
        $filters = $this->request->query('filters');
        $filters['interview_statuses']    = ['Interviewed'];
        $filters['registration_statuses'] = ['Pending'];

        echo json_encode(Model_Booking_Application::get_for_offers_datatable($filters, $this->request->query()));
    }

    public function action_ajax_update_status()
    {
        $this->auto_render = false;

        try {
            $query       = $this->request->query();
            $application = new Model_Booking_Application($query['booking_id']);
            $saved       = $application->save_with_history($query['status_group'], $query['status']);
            $message     = __('Status has been updated to "$1"', ['$1' => $query['status']]);
        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error updating status.\n".$e->getMessage()."\n".$e->getTraceAsString());
            $saved = false;
            $message = 'Error updating status. If this problem continues, please ask an administrator to check the application logs';
        }

        echo json_encode(['success' => (bool) $saved, 'message' => $message]);
    }

    public function action_ajax_change_interview_date()
    {
        $this->auto_render = false;
        $booking_id     = $this->request->query('booking_id');
        $period_id      = $this->request->query('period_id');
        $saved          = false;
        $email_sent     = false;
        $email_message  = '';

        $db = Database::instance();
        $db->commit();

        try {
            $booking_item   = ORM::factory('Booking_Item')->where('booking_id', '=', $booking_id)->find();
            $period         = Model_ScheduleEvent::get($period_id);
            $date_formatted = $period['datetime_start'] ? date('l j F Y H:i', strtotime($period['datetime_start'])) : 'unset';

            if ($booking_item->period_id == $period_id) {
                $message = __('The interview is already scheduled for $1.', ['$1' => $date_formatted]);
            } else {
                // Update the booking item
                $booking_item->set('period_id', $period_id);
                $saved = $booking_item->save();

                // Keep track of changes made to the booking
                $application = new Model_Booking_Application($booking_id);
                $application->save_with_history('period_id', $period_id);

                $message = __('Interview date has been changed to $1.', ['$1' => $date_formatted]);

                if ($this->request->query('send_email')) {
                    $email_sent = Model_KES_Bookings::send_interview_schedule_email($booking_id);
                    if ($email_sent) {
                        $email_message = __('An email has been sent to $1', ['$1' => $application->booking->applicant->get_full_name()]);
                    } else {
                        $email_message = __('Error sending email');
                    }
                }
            }

        } catch (Exception $e) {
            // If there is an error, undo all changes
            $db->rollback();

            // Log and display and error
            Log::instance()->add(Log::ERROR, "Error changing interview date.\n".$e->getMessage()."\n".$e->getTraceAsString());
            $message = 'Error changing interview date. If this problem continues, please ask an administrator to check the application logs';
        }

        echo json_encode([
            'success'       => (bool) $saved,
            'message'       => $message,
            'email_sent'    => (bool) $email_sent,
            'email_message' => $email_message
        ]);
    }
}