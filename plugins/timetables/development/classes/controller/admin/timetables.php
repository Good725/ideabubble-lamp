<?php

class Controller_Admin_Timetables extends Controller_Cms
{
    protected $_plugin = 'timetables';

    protected $_crud_items = [
        'timetable' => [
            'name' => 'Timetable',
            'model' => 'Timetables',
            'delete_permission' => false,
            'edit_permission'   => false,
        ]
    ];

    function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => 'admin'),
            array('name' => 'Timetables', 'link' => 'admin/timetables')
        );
    }

    public function action_index($args = [])
    {
        if (!Auth::instance()->has_access('timetables_view_all') && !Auth::instance()->has_access('timetables_view_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }
        $stylesheets = array(
            URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings').'admin/css/fullcalendar.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings').'admin/css/bookings.css' => 'screen',
            URL::get_engine_plugin_assets_base('timeoff').'css/timeoff.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('bookings').'admin/js/fullcalendar.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/jquery.eventCalendar.js"></script>';

        $academic_years = Model_AcademicYear::get_all();

        // Only show "my" events, if I don't have permission to view all or if I specifically requested that I only see mine.
        $my = (!Auth::instance()->has_access('timetables_view_all') || $this->request->query('my') == 1) || !empty($args['my']);

        if ($my) {
            $this->template->sidebar->breadcrumbs[] = ['name' => 'My Timetables', 'link' => 'admin/timetables?my=1'];
        }

        $user = Auth::instance()->get_user();
        $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        $contact = current($contacts);
        $me = $contact ? $contact : null;

        if (!$this->request->query('old_ui')) {
            $views = $my ? ['calendar'] : ['calendar', 'overview'];
            $add_timeslot_button = !$my && Auth::instance()->has_access('timetables_add_slot');
            $id_prefix = 'timetables-calendar';

            $this->template->sidebar->tools = View::factory('iblisting_mode_toggle',
                compact('views', 'add_timeslot_button', 'id_prefix'));

            $filter_types = $my
                ? ['blackouts', 'courses', 'family_members', 'schedules']
                : ['activities', 'blackouts', 'courses', 'locations', 'schedules', 'statuses', 'topics', 'trainers'];

            $filter_args = [
                'contact_id' => $contact['id'],
                'selected_contact_id' => $my ? $contact['id'] : null,
                'selected_activity' => 'booking'
            ];

            $filter_menu_options = Model_Timetables::get_filter_options($filter_types, $filter_args);

            $reports = [
                ['amount' => 0, 'text' => 'Hours capacity'],
                ['amount' => 0, 'text' => 'Hours scheduled'],
                ['amount' => 0, 'text' => 'Hours logged'],
                ['amount' => 0, 'text' => 'Hours of leave'],
                ['amount' => 0, 'text' => 'Left']
            ];

            $this->template->body = View::factory('iblisting')->set([
                'add_timeslot_button' => $add_timeslot_button,
                'checkbox_table'      => true, // Style checkboxes to appear outside of the table
                'columns'             => ['Staff', 'Schedule', 'Day', 'Date', 'Time', 'Location', 'Status', 'Hrs', 'Total h', 'Actions'],
                'daterangepicker'     => true,
                'filter_menu_options' => $filter_menu_options,
                'id_prefix'           => $id_prefix,
                'plugin'              => 'timetables',
                'popover_mode'        => $my ? 'read' : 'edit',
                'reports'             => $reports,
                'type'                => 'timetable', // See $_crud_items
                'show_mine_only'      => $my,
                'timeslots_url'       => '/admin/timetables/load_data',
                'views'               => $views,
            ]);
        }
        //
        else {
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('timetables').'js/timetables.js"></script>';
            $this->template->body      = View::factory('admin/view_timetables', [
                'academic_years' => $academic_years,
                'my' => $my,
                'me' => $me
            ]);
        }
    }

    public function action_planner()
    {
        $auth = Auth::instance();
        $user = $auth->get_user();
        if (!Auth::instance()->has_access('timetables_view_planner')) {
            IbHelpers::set_message('You need access to the "timetables_view_planner" to use this feature.', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->sidebar->breadcrumbs[] = ['name' => 'Planner', 'link' => '/admin/timetables/planner'];

        $stylesheets = array(
            URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('bookings').'admin/css/fullcalendar.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('timeoff').'css/timeoff.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/timepicker.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/forms.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/eventCalendar.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('courses').'css/eventCalendar_theme_responsive.css'] = 'screen';

        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/bootstrap-timepicker.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/jquery.eventCalendar.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/jquery.timeago.js"></script>';

        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('bookings').'admin/js/fullcalendar.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('courses').'js/schedules_form.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('timetables').'js/timetable_planner.js"></script>';


        $engineCalendarEvents = Model_Calendar_Event::getEventList('courses');
        $blackout_calendar_event_ids = array();
        foreach($engineCalendarEvents as $engineCalendarEvent){
            $blackout_calendar_event_ids[] = $engineCalendarEvent['id'];
        }

        $this->template->body = View::factory(
            'admin/timetable_planner',
            array(
                'blackout_calendar_event_ids' => $blackout_calendar_event_ids,
                'engineCalendarEvents' => $engineCalendarEvents,
            )
        );

        /* Dummy data */
        $this->template->body->courses = [
            ['id' => 1, 'name' => 'Course in Agri',             'pending' => 300],
            ['id' => 2, 'name' => 'Agricultural Mechanisation', 'pending' => 90],
            ['id' => 3, 'name' => 'Drystock management',        'pending' => 200]
        ];
        $this->template->body->locations = Model_Timetables::locations_list();
        $this->template->body->schedules = Model_Timetables::schedules_list();
        $this->template->body->staff = Model_Timetables::trainer_list();
        $this->template->body->trainers = Model_Contacts3::get_teachers();
        if (!$auth->has_access('courses_schedule_edit')) {
            $franchisee_contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
            $this->template->body->trainers = array($franchisee_contact);
        }
        $this->template->body->topics = Model_Timetables::topic_list();
        $this->template->body->topics_all = Model_Topics::get_all_topics();
    }

    public function action_autocomplete_contacts()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        if (!Auth::instance()->has_access('timetables_view_all') && !Auth::instance()->has_access('timetables_view_limited')) {
            $contacts = array();
        } else if (Auth::instance()->has_access('timetables_view_all')) {
            $contacts = Model_Contacts3::autocomplete_list($this->request->query('term'));
        } else {
            $user = Auth::instance()->get_user();
            $contact_ids = Model_Contacts3::get_contact_ids_by_user($user['id']);
            $contacts = array();
            foreach ($contact_ids as $contact_id) {
                $c3 = new Model_Contacts3($contact_id);
                $contacts[] = array(
                    'value' => $contact_id,
                    'label' => $c3->get_first_name() . ' ' . $c3->get_last_name()
                );
            }
        }

        echo json_encode($contacts);
    }

    public function action_autocomplete_schedules()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $trainer_id = null;
        $auth = Auth::instance();
        if (!$auth->has_access('courses_schedule_edit')) {
            $user = $auth->get_user();
            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                $trainer = Model_Contacts3::get_linked_contact_to_user($user['id']);
            } else {
                $trainer = Model_Contacts::get_linked_contact_to_user($user['id']);
            }
            if ($trainer) {
                $trainer_id = $trainer['id'];
            }
        }

        echo json_encode(Model_courses::autocomplete_search_schedules($this->request->query('term'), $trainer_id, false, true,
            $this->request->query('course_id')));
    }

    public function action_autocomplete_locations()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $trainer_id = null;
        $auth = Auth::instance();
        $children_identifier = $this->request->query('children_identifier') ?? '0';
        echo json_encode(Model_Locations::autocomplete_list($this->request->query('term'), $children_identifier));
    }

    public function action_load_data()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        if (!Auth::instance()->has_access('timetables_view_all') && !Auth::instance()->has_access('timetables_view_limited')) {
            $this->response->status(403);
            exit;
        }

        $post = $this->request->post('filters') ? $this->request->post('filters') : $this->request->post();

        $view = @$post['view'] ?: 'people';
        $params = array();
        if (@$post['date_start']) {
            $params['date_start'] = $post['date_start'] . ' 00:00:00';
        }
        if (@$post['date_end']) {
            $params['date_end'] = $post['date_end'] . ' 23:59:59';
        }
        if (!empty($post['start_date'])) {
            $params['date_start'] = $post['start_date'] . ' 00:00:00';
        }
        if (!empty($post['end_date'])) {
            $params['date_end'] = $post['end_date'] . ' 23:59:59';
        }

        if (@$post['view_filter_id']) {
            if ($view == 'courses') {
                $params['schedule_id'] = $post['view_filter_id'];
            } else if ($view == 'locations') {
                $params['location_id'] = $post['view_filter_id'];
            } else {
                $params['trainer_id'] = $post['view_filter_id'];
            }
        }

        if (!empty($post['schedule_id'])) {
            $params['schedule_id'] = $post['schedule_id'];
        }

        if (!empty($post['course_id'])) {
            $params['course_id'] = $post['course_id'];
        }

        if (!empty($post['location_id'])) {
            $params['location_id'] = $post['location_id'];
        }

        if (!empty($post['topic_id'])) {
            $params['topic_id'] = $post['topic_id'];
        }

        if (!empty($post['trainer_id'])) {
            $params['trainer_id'] = $post['trainer_id'];
        }

        if (!empty($post['family_members'])) {
            foreach ($post['family_members'] as $family_member_id) {
                $fm = new Model_Contacts3($family_member_id);
                if ($fm->has_role('teacher')) {
                    if (empty($post['trainer_id'])) {
                        $params['trainer_id'] = $post['family_members'][0];
                    }
                } else {
                    $params['student_id'] = $post['family_members'];
                }
            }
        }

        // If you do not have the "view all" permission, you may only view for your family.
        if (!Auth::instance()->has_access('timetables_view_all')) {
            $contact = new Model_Contacts3(Auth::instance()->get_contact()->id);
            $family_member_ids = $contact->get_family()->get_member_ids() + [$contact->get_id()];
            if ($contact->has_role('teacher')) {
                $params['trainer_id'] = $family_member_ids;
            } else {
                // If some family members have been manually selected, verify that
                // they are indeed within the family.
                if (isset($params['student_id'])) {
                    $params['student_id'] = array_intersect($family_member_ids, $params['student_id']);
                }

                // Even if no family members have been selected, only search within the family.
                if (empty($params['student_id'])) {
                    $params['student_id'] = $family_member_ids;
                }
            }
        }

        if (!empty($post['statuses']))       { $params['statuses']       = $post['statuses'];       }
        if (!empty($post['blackouts']))      { $params['blackouts']      = $post['blackouts'];      }
        if (!empty($post['activities']))     { $params['activities']     = $post['activities'];     }
        if (!empty($post['show_mine_only'])) { $params['show_mine_only'] = $post['show_mine_only']; }

        if ($view == 'courses') {
            $data = Model_Timetables::get_data_courses($params);
        } else if ($view == 'locations') {
            $data = Model_Timetables::get_data_locations($params);
        } else {
            $data = Model_Timetables::get_data_people($params);
        }
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function action_ajax_get_submenu()
    {
        $return['items'] = [
            ['title' => 'My timetables', 'link' => '/admin/timetables?my=1',    'icon_svg' => 'my-requests'],
        ];
        
        if (Auth::instance()->has_access('timetables_view_planner')) {
            $return['items'][] = array(
                'title' => 'Planner',
                'link' => '/admin/timetables/planner',
                'icon_svg' => 'timetable'
            );
        }
        
        if (Auth::instance()->has_access('timetables_view_all')) {
            $return['items'][] = array('title' => 'All timetables', 'link' => '/admin/timetables', 'icon_svg' => 'all-requests');
        }

        return $return;
    }

    public function action_save_slot()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $post = $this->request->post();

        $user = Auth::instance()->get_user();
        $id = Model_Timetables::save_slot($post, $user['id']);

        echo json_encode(array('id' => $id), JSON_PRETTY_PRINT);
    }

    public function action_get_calendar()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $query = $this->request->post();

        $user = Auth::instance()->get_user();

        $params = array();
        if (@$query['before']) {
            $params['before'] = $query['before'];
        }
        if (@$query['after']) {
            $params['after'] = $query['after'];
        }
        if (@$query['course_id']) {
            $params['course_id'] = $query['course_id'];
        }
        if (@$query['schedule_id']) {
            $params['schedule_id'] = $query['schedule_id'];
        }
        if (@$query['trainer_id']) {
            $params['trainer_id'] = $query['trainer_id'];
        }
        if (@$query['location_id']) {
            $params['location_id'] = $query['location_id'];
        }
        if (@$query['topic_id']) {
            $params['topic_id'] = $query['topic_id'];
        }

        $result = array();
        if (Settings::instance()->get('timetable_conflict_detection') == 1) {
            $conflicts = Model_Timetables::conflicting_slots($params['after'], $params['before'], @$params['schedule_id']);
        } else {
            $conflicts = array();
        }
        if (count($conflicts) > 0) {
            $params['not_id'] = $conflicts;
            $slots_nonconflict = Model_Timetables::search_slot($params);
            unset($params['not_id']);
            $params['id'] = $conflicts;
            $slots_conflict = Model_Timetables::search_slot($params);
            foreach($slots_conflict as $i => $slot) {
                $slots_conflict[$i]['conflict'] = 1;
                $slots_conflict[$i]['status'] = 'Conflict';
            }
            $result['slots'] = array_merge($slots_conflict, $slots_nonconflict);
        } else {
            $result['slots'] = Model_Timetables::search_slot($params);
        }

        foreach ($result['slots'] as $i => $slot) {
            $result['slots'][$i]['start'] = $slot['datetime_start'];
            $result['slots'][$i]['end'] = $slot['datetime_end'];
            $result['slots'][$i]['title'] = $slot['topic'];
            $result['slots'][$i]['attending'] = '';
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function action_planner_data()
    {
        if (!Auth::instance()->has_access('timetables_view_planner')) {
            IbHelpers::set_message('You need access to the "timetables_view_planner" to use this feature.',
                'warning popup_box');
            $this->request->redirect('/admin/timetables?my=1');
        }
        
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $params = $this->request->post();
        if (@$params['course_id'] && !is_array($params['course_id'])) {
            $params['course_id'] = explode(',', $params['course_id']);
        }
        if (@$params['schedule_id'] && !is_array($params['schedule_id'])) {
            $params['schedule_id'] = explode(',', $params['schedule_id']);
        }
        if (@$params['trainer_id'] && !is_array($params['trainer_id'])) {
            $params['trainer_id'] = explode(',', $params['trainer_id']);
        }
        if (@$params['location_id'] && !is_array($params['location_id'])) {
            $params['location_id'] = explode(',', $params['location_id']);
        }
        if (@$params['topic_id'] && !is_array($params['topic_id'])) {
            $params['topic_id'] = explode(',', $params['topic_id']);
        }
        $result = Model_Timetables::datatable($params);
        echo json_encode($result);
    }

    public function action_remove_slot()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $id = $this->request->post('id');
        Model_Timetables::save_slot(array('delete' => 1, 'id' => $id));
        echo json_encode(array('id' => $id));
    }
    
    public function action_ignore_timeslots()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
    
        $timeslots = $this->request->post('timeslots');
        Model_Timetables::ignore_timeslots($timeslots);
        echo json_encode(array('id' => $id));
    }
    
    public function action_approve_slot()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $id = $this->request->post('id');
        Model_Timetables::save_slot(array('available' => 1, 'id' => $id));
        echo json_encode(array('id' => $id));
    }

    public function action_save_timeslots()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $course_id = $post['course_id'];
        $schedule_id = $post['schedule_id'];
        $location_id = $post['location_id'];
        $topic_id = $post['topic_id'];
        $post['timeslots'] = json_decode($post['timeslots'], true);
        $timetable_id = null;
        $blackoutEventIds = array();

        try {
            Database::instance()->begin();
            Model_Schedules::save_timetable_and_schedule($post['timeslots'], $schedule_id, $timetable_id, $blackoutEventIds, false, true);
            if ($location_id) {
                DB::update(Model_Schedules::TABLE_SCHEDULES)
                    ->set(array('location_id' => $location_id))
                    ->where('id', '=', $schedule_id)
                    ->execute();
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }

        echo json_encode($post, JSON_PRETTY_PRINT);
    }

    public function action_resolve_timeslots()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();

        $timeslots = $post['timeslots'];
        Database::instance()->begin();
        foreach ($timeslots as $timeslot) {
            Model_Timetables::save_slot($timeslot);
        }
        Database::instance()->commit();
        echo json_encode($post, JSON_PRETTY_PRINT);
    }

    public function action_get_conflicts()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $post = $this->request->post();
        $schedule_id = $post['schedule_id'];
        $timeslot_id = $post['timeslot_id'];

        $params = array('calculate_rows' => true);
        $schedule = Model_Schedules::get_schedule($schedule_id);

        $timeslot = new Model_Course_Schedule_Event($timeslot_id);

        if ($timeslot_id) {
            $params['before'] = $timeslot->datetime_end;
            $params['after'] = $timeslot->datetime_start;
        } else {
            $params['after'] = @$schedule['timeslots'][0]['datetime_start'];
            $params['before'] = @$schedule['timeslots'][count($schedule['timeslots']) - 1]['datetime_end'];
        }
        $params['conflicts'] = true;
        $conflict_ids = Model_Timetables::conflicting_slots($params['after'], $params['before'], null, $timeslot_id);

        $result = array(
            'schedule' => $schedule,
            'timeslot' => [
                'id' => $timeslot->id,
                'location_id' => $timeslot->location_id,
                'trainer_id' => $timeslot->trainer_id
            ]
        );

        if (count($conflict_ids) == 0){
            $result['conflicts'] = [];
        } else {
            $params['id'] = $conflict_ids;
            $result['conflicts'] = Model_Timetables::search_slot($params);
        }
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
    }