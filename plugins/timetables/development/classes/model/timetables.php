<?php

class Model_Timetables
{
    const TABLE_IGNORES = 'plugin_courses_schedules_timeslots_ignored_conflicts';

    protected static function set_bookings(&$data, $params = [])
    {
        if (!empty($params['date_start'])) {
            $params['after'] = $params['date_start'];
        }
        if (!empty($params['date_end'])) {
            $params['before'] = $params['date_end'];
        }

        $slots = Model_Timetables::search_slot($params);
        foreach ($slots as $i => $slot) {
            $slot['title']    = $slot['schedule'] ? $slot['schedule'] : $slot['topic'];
            $slot['start']    = $slot['datetime_start'];
            $slot['end']      = $slot['datetime_end'];
            $slot['type']     = 'booking';

            $data['calendar'][] = $slot;
        }

    }

    protected static function set_blackouts(&$data, $params = array())
    {
        $blackouts = Model_Calendar_Event::search(null, @$params['date_start'], @$params['date_end'], @$params['blackouts']);

        foreach ($blackouts as $blackout) {
            $data['calendar'][] = array(
                'title' => $blackout['title'],
                'type' => 'blackout-'.$blackout['type'],
                'start' => $blackout['start_date'],
                'end' => $blackout['end_date'],
                'allDay' => true
            );
        }
    }

    protected static function set_timeslots(&$data, $params = array())
    {
        $timeslots_params = array();
        $timeslots_params['after'] = @$params['date_start'];
        $timeslots_params['before'] = @$params['date_end'];
        $timeslots_params['trainer_id'] = @$params['trainer_id'];
        $timeslots_params['course_id'] = @$params['course_id'];
        $timeslots_params['schedule_id'] = @$params['schedule_id'];
        $timeslots_params['location_id'] = is_array(@$params['location_id']) ? $params['location_id'] : (@$params['location_id'] ? array(@$params['location_id']) : null);

        $timeslots = Model_ScheduleEvent::search($timeslots_params);
        foreach ($timeslots as $timeslot) {
            $data['calendar'][] = array(
                'title' => trim($timeslot['room'] . ' - ' . $timeslot['course'] . ' - ' . $timeslot['schedule'] . ' - ' . $timeslot['trainer'], ' -'),
                'type' => 'timesheet',
                'start' => $timeslot['datetime_start'],
                'end' => $timeslot['datetime_end']
            );
        }
    }

    protected static function set_exams(&$data, $params = array())
    {
        $exams_params = array();
        $exams_params['after'] = @$params['date_start'];
        $exams_params['before'] = @$params['date_end'];
        $exams_params['trainer_id'] = @$params['trainer_id'];
        $exams_params['course_id'] = @$params['course_id'];
        $exams_params['schedule_id'] = @$params['schedule_id'];
        $exams_params['location_id'] = is_array(@$params['location_id']) ? $params['location_id'] : (@$params['location_id'] ? array(@$params['location_id']) : null);

        $exams = Model_Todos::search($exams_params);
        foreach ($exams as $exam) {
            $data['calendar'][] = array(
                'title' => $exam['title'],
                'type' => 'exam',
                'start' => $exam['datetime'],
                'end' => $exam['datetime_end']
            );
        }
    }

    protected static function set_timeoff_requests(&$data, $params = array())
    {
        $timeoff_params = new Ideabubble\Timeoff\Dto\RequestSearchDto();
        $timeoff_params->startDate = @$params['date_start'];
        $timeoff_params->endDate = @$params['date_end'];
        $timeoff_params->staffId = @$params['student_id'] ? @$params['student_id'] : @$params['trainer_id'];

        $dispatcher = timeoff_event_dispatcher();
        $gen = new \Ideabubble\Timeoff\Kohana\KohanaGenerator();
        $departmentRepository = new \Ideabubble\Timeoff\Kohana\KohanaDepartmentRepository();
        $scheduleRepository = new \Ideabubble\Timeoff\Kohana\KohanaScheduleEventRepository();
        $staffService = new \Ideabubble\Timeoff\StaffService(new \Ideabubble\Timeoff\Kohana\KohanaStaffRepository($dispatcher));
        $configService = new \Ideabubble\Timeoff\ConfigService(new \Ideabubble\Timeoff\Kohana\KohanaConfigRepository($dispatcher));
        $configService = $configService;
        $departmentService = new \Ideabubble\Timeoff\DepartmentService($departmentRepository);
        $requestService = new \Ideabubble\Timeoff\RequestService(
            $configService,
            new \Ideabubble\Timeoff\Kohana\KohanaRequestRepository($dispatcher),
            new \Ideabubble\Timeoff\Kohana\KohanaNoteRepository($dispatcher),
            $staffService,
            $departmentRepository,
            $scheduleRepository,
            $gen
        );

        $timeoffs = $requestService->findAll($timeoff_params);

        $contact_ids = array();
        foreach ($timeoffs as $timeoff) {
            $contact_ids[] = $timeoff->getStaffId();
        }
        if (!empty($contact_ids)) {
            $contactx = Model_Contacts3::get_all_contacts(array(array('contact.id', 'in', $contact_ids)));
        } else {
            $contactx = array();
        }
        $contacts = array();
        foreach ($contactx as $contact) {
            $contacts[$contact['id']] = $contact;
        }

        foreach ($timeoffs as $timeoff) {
            $contact_id = $timeoff->getStaffId();
            $data['calendar'][] = array(
                'title' => $contacts[$contact_id]['first_name'] . ' ' . $contacts[$contact_id]['last_name'] . ' - ' . $timeoff->getStatus(),
                'type' => 'timeoff',
                'start' => $timeoff->getPeriod()->getStartDate(),
                'end' => $timeoff->getPeriod()->getEndDate(),
                'allDay' => true,
                'contact_id' => $contact_id
            );
        }
    }

    public static function set_todos(&$data, $params = array())
    {

        $t = new Model_Todos();
        $todos_select = $t->get_todos_select_query();
        //$todos_select->and_where('todos.status_id', '<>', 'Closed');
        if (@$params['date_start']) {
            $todos_select->and_where('todos.datetime_end', '>=', $params['date_start']);
        }
        if (@$params['date_end']) {
            $todos_select->and_where('todos.datetime_end', '<=', $params['date_end']);
        }
        $todos_select->and_where('todos.datetime_end', 'is not', null);

        $todos = $todos_select->execute()->as_array();

        foreach ($todos as $todo) {
            $data['calendar'][] = array(
                'title' => $todo['title'],
                'type' => 'todo',
                'start' => $todo['datetime'],
                'end' => $todo['datetime_end'],
                'allDay' => true
            );
        }
    }

    protected static function set_timesheet_requests(&$data, $params = array())
    {
        $select = DB::select('*')
            ->from(array(Model_Timeoff::TIMESHEETS_TABLE, 'timesheets'));

        if (@$params['before']) {
            $select->and_where('timesheets.period_start_date', '<=', $params['before']);
        }
        if (@$params['after']) {
            $select->and_where('timesheets.period_end_date', '>=', $params['after']);
        }

        if (@$params['trainer_id'] || @$params['student_id']) {
            $staff_ids = [];
            if (isset($params['trainer_id'])) {
                if (is_array($params['trainer_id'])) {
                    $staff_ids = $staff_ids + $params['trainer_id'];
                } else {
                    $staff_ids[] = $params['trainer_id'];
                }
            }

            if (isset($params['student_id'])) {
                if (is_array($params['student_id'])) {
                    $staff_ids = $staff_ids + $params['student_id'];
                } else {
                    $staff_ids[] = $params['student_id'];
                }
            }


            $select->and_where('timesheets.staff_id', 'in', $staff_ids);

        }
        $timesheets = $select->execute()->as_array();
        foreach ($timesheets as $timesheet) {
            $data['calendar'][] = array(
                'title' => $timesheet['note'],
                'type' => 'timesheet',
                'start' => $timesheet['period_start_date'],
                'end' => $timesheet['period_end_date']
            );
        }
    }

    public static function get_data_courses($params = array())
    {
        $data = array();
        $data['reports'] = array(
        );
        $data['calendar'] = array(

        );

        self::set_reports_location_course($data, $params);

        if ($params['activities'] == null || in_array('booking', @$params['activities'])) {

        }
        if ($params['activities'] == null || in_array('holiday', @$params['activities'])) {
            self::set_blackouts($data, $params);
        }
        if ($params['activities'] == null || in_array('timeslot', @$params['activities'])) {
            self::set_timeslots($data, $params);
        }
        if ($params['activities'] == null || in_array('exam', @$params['activities'])) {
            self::set_exams($data, $params);
        }
        if ($params['activities'] == null || in_array('timeoff', @$params['activities'])) {
            self::set_timeoff_requests($data, $params);
        }
        return $data;
    }

    public static function get_data_locations($params = array())
    {
        $data = array();
        $data['reports'] = array();
        $data['calendar'] = array();

        self::set_reports_location_course($data, $params);

        if ($params['activities'] == null || in_array('holiday', @$params['activities'])) {
            self::set_blackouts($data, $params);
        }
        if ($params['activities'] == null || in_array('timeslot', @$params['activities'])) {
            self::set_timeslots($data, $params);
        }
        if ($params['activities'] == null || in_array('exam', @$params['activities'])) {
            self::set_exams($data, $params);
        }
        if ($params['activities'] == null || in_array('timeoff', @$params['activities'])) {
            self::set_timeoff_requests($data, $params);
        }

        return $data;
    }

    private static function set_reports_location_course(&$data, $params = array())
    {
        $period = date('j/M/Y', strtotime($params['date_start'])) . ' – ' . date('j/M/Y', strtotime($params['date_end']));

        $schedule_capacity_select = DB::select(DB::expr("sum(schedules.max_capacity) as timeslot_capacity"))
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id')
            ->where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);
        self::set_schedule_timeslot_params($schedule_capacity_select, $params);
        $timeslot_capacity = $schedule_capacity_select->execute()->get('timeslot_capacity');
        $data['reports'][] = array(
            'amount' => $timeslot_capacity,
            'title' => 'Slot Capacity',
            'period' => $period
        );

        $schedule_count_select = DB::select(DB::expr("count(*) as timeslot_count"))
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id')
            ->where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);
        self::set_schedule_timeslot_params($schedule_count_select, $params);
        $timeslot_count = $schedule_count_select->execute()->get('timeslot_count');
        $data['reports'][] = array(
            'amount' => $timeslot_count,
            'title' => 'Slots Scheduled',
            'period' => $period
        );

        $schedule_completed_select = DB::select(DB::expr("count(*) as timeslot_count"))
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id')
            ->where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);
        self::set_schedule_timeslot_params($schedule_completed_select, $params);
        $schedule_completed_select->and_where('timeslots.datetime_end', '<=', date::now());
        $timeslot_count = $schedule_completed_select->execute()->get('timeslot_count');
        $data['reports'][] = array(
            'amount' => $timeslot_count,
            'title' => 'Slots Completed',
            'period' => $period
        );

        $schedule_available_select = DB::select(DB::expr("count(*) as timeslot_count"))
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id')
            ->where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);
        self::set_schedule_timeslot_params($schedule_available_select, $params);
        $schedule_available_select->and_where('timeslots.datetime_end', '>=', date::now());
        $timeslot_count = $schedule_available_select->execute()->get('timeslot_count');
        $data['reports'][] = array(
            'amount' => $timeslot_count,
            'title' => 'Slots Available',
            'period' => $period
        );

        $schedule_filter_sub_select = DB::select(
            'timeslots.schedule_id',
            'timeslots.datetime_start',
            'timeslots.datetime_end',
            'timeslots.id',
            'schedules.location_id'
        )
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);
        $schedule_filter_sub_select2 = clone $schedule_filter_sub_select;
        self::set_schedule_timeslot_params($schedule_filter_sub_select, $params);
        $conflict_count = DB::select(DB::expr("count(*) as conflict_count"))
            ->from(array($schedule_filter_sub_select, 'timeslots1'))
            ->join(array($schedule_filter_sub_select2, 'timeslots2'))
            ->on('timeslots1.location_id', '=', 'timeslots2.location_id')
            ->on('timeslots1.id', '<>', 'timeslots2.id')
            ->and_where_open()
            ->or_where_open()
            ->and_where('timeslots1.datetime_start', '>=', DB::expr('timeslots2.datetime_start'))
            ->and_where('timeslots1.datetime_start', '<=', DB::expr('timeslots2.datetime_end'))
            ->or_where_close()
            ->or_where_open()
            ->and_where('timeslots1.datetime_end', '>=', DB::expr('timeslots2.datetime_start'))
            ->and_where('timeslots1.datetime_end', '<=', DB::expr('timeslots2.datetime_end'))
            ->or_where_close()
            ->and_where_close()
            ->execute()
            ->get('conflict_count');
        $data['reports'][] = array(
            'amount' => $conflict_count,
            'title' => 'Slot Conflicts',
            'period' => $period
        );
    }

    private static function set_schedule_timeslot_params($select, $params = array())
    {
        if (@$params['date_start']) {
            $select->and_where('timeslots.datetime_start', '>=', $params['date_start']);
        }
        if (@$params['date_end']) {
            $select->and_where('timeslots.datetime_end', '<=', $params['date_end']);
        }
        if (@$params['schedule_id']) {
            if (!is_array($params['schedule_id'])) {
                $params['schedule_id'] = [$params['schedule_id']];
            }
            $select->and_where('timeslots.schedule_id', 'in', $params['schedule_id']);
        }
        if (@$params['location_id']) {
            if (!is_array($params['location_id'])) {
                $params['location_id'] = [$params['location_id']];
            }
            $select->and_where('schedules.location_id', 'in', $params['location_id']);
        }
        if (@$params['trainer_id']) {
             if (!is_array($params['trainer_id'])) {
                $params['trainer_id'] = [$params['trainer_id']];
            }
            $select->and_where_open();
            $select->or_where('schedules.trainer_id', 'in', $params['trainer_id']);
            $select->or_where('timeslots.trainer_id', 'in', $params['trainer_id']);
            $select->and_where_close();
        }
    }

    public static function get_data_people($params = array())
    {
        $data = array();
        $data['reports'] = [];
        $data['calendar'] = [];

        self::set_reports_people($data, $params);

        if ($params['activities'] == null || in_array('booking', @$params['activities'])) {
            self::set_bookings($data, $params);
        }

        self::set_blackouts($data, $params);

        // "My timetables" view to only show bookings and blackouts
        if (empty($params['show_mine_only'])) {
            if ($params['activities'] == null || in_array('timeslot', @$params['activities'])) {
                self::set_timeslots($data, $params);
            }
            if ($params['activities'] == null || in_array('exam', @$params['activities'])) {
                self::set_exams($data, $params);
            }
            if ($params['activities'] == null || in_array('timeoff', @$params['activities'])) {
                self::set_timeoff_requests($data, $params);
            }
            if ($params['activities'] == null || in_array('timesheet', @$params['activities'])) {
                self::set_timesheet_requests($data, $params);
            }
            if ($params['activities'] == null || in_array('todo', @$params['activities'])) {
                self::set_todos($data, $params);
            }
        }

        return $data;
    }

    private static function set_reports_people(&$data, $params = array())
    {
        $period = date('j/M/Y', strtotime($params['date_start'])) . ' – ' . date('j/M/Y', strtotime($params['date_end']));
        $global_log_per_week = DB::select('*')
            ->from(Model_Timeoff::CONFIG_TABLE)
                ->where('level', '=', 'global')
            ->execute()
            ->as_array();

        $timeoff_select = DB::select(
            /*'contacts.id',
            'contacts.first_name',
            'contacts.last_name',
            DB::expr("IFNULL(contact_config.value, IFNULL(department_config.value, business_config.value)) as log_hours_per_week"),
            array('contact_config.value', 'contact_log_hours_per_week'),
            array('department_config.value', 'department_log_hours_per_week'),
            array('business_config.value', 'business_log_hours_per_week'),
            array('departments.first_name', 'department'),
            array('businesses.first_name', 'business')*/
            DB::expr("SUM(IFNULL(contact_config.value, IFNULL(department_config.value, business_config.value))) as log_hours_per_week")
        )
            ->from(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'))
                ->join(array(Model_Contacts3::CONTACT_RELATIONS_TABLE, 'dept_link'), 'left')
                    ->on('contacts.id', '=', 'dept_link.child_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'departments'), 'left')
                    ->on('dept_link.parent_id', '=', 'departments.id')
                ->join(array(Model_Contacts3::CONTACT_RELATIONS_TABLE, 'business_link'), 'left')
                    ->on('departments.id', '=', 'business_link.child_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'businesses'), 'left')
                    ->on('business_link.parent_id', '=', 'businesses.id')
                ->join(array(Model_Timeoff::CONFIG_TABLE, 'contact_config'), 'left')
                    ->on('contacts.id', '=', 'contact_config.item_id')
                    ->on('contact_config.name', '=', DB::expr("'timeoff.log_hours_per_week'"))
                ->join(array(Model_Timeoff::CONFIG_TABLE, 'department_config'), 'left')
                    ->on('departments.id', '=', 'department_config.item_id')
                    ->on('department_config.name', '=', DB::expr("'timeoff.log_hours_per_week'"))
                ->join(array(Model_Timeoff::CONFIG_TABLE, 'business_config'), 'left')
                    ->on('businesses.id', '=', 'business_config.item_id')
                    ->on('business_config.name', '=', DB::expr("'timeoff.log_hours_per_week'"))
            ->where('contacts.delete', '=', 0)
            ->and_where('contacts.type', '=', 1);
        if (@$params['trainer_id']) {
            $operator = is_array($params['trainer_id']) ? 'in' : '=';
            $timeoff_select->and_where('contacts.id', $operator, $params['trainer_id']);
        }
        //echo $timeoff_select;exit;
        $log_hours_per_week = $timeoff_select->execute()->get('log_hours_per_week');

        $timediff = strtotime($params['date_end']) - strtotime($params['date_start']);
        $days = ceil($timediff / 86400);
        $weeks = ceil($days / 7);
        $capacity = $weeks * $log_hours_per_week;
        $data['reports'][] = array(
            'amount' => $capacity,
            'title' => 'Hours capacity',
            'period' => $period
        );


        $scheduled_timeslots_select = DB::select(DB::expr("SUM(timeslots.datetime_end - timeslots.datetime_start) as total_time"))
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id')
            ->where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);
        self::set_schedule_timeslot_params($scheduled_timeslots_select, $params);
        $scheduled_time = $scheduled_timeslots_select->execute()->get('total_time');
        $data['reports'][] = array(
            'amount' => self::format_time_amount($scheduled_time),
            'title' => 'Hours scheduled',
            'period' => $period
        );

        $timesheets_select = DB::select(DB::expr("SUM(timesheets.period_end_date - timesheets.period_start_date) as total_time"))
            ->from(array(Model_Timeoff::TIMESHEETS_TABLE, 'timesheets'))
            //->where('timesheets.deleted', '=', 0)
            ->and_where('timesheets.status', '=', 'approved');
        if (!empty($params['trainer_id'])) {
            $operator = is_array($params['trainer_id']) ? 'in' : '=';
            $timeoff_select->and_where('timesheets.staff_id', $operator, $params['trainer_id']);
        }
        if (@$params['date_start']) {
            $timesheets_select->and_where('timesheets.period_start_date', '>=', $params['date_start']);
        }
        if (@$params['date_end']) {
            $timesheets_select->and_where('timesheets.period_end_date', '<=', $params['date_end']);
        }
        $logged_time = $timesheets_select->execute()->get('total_time');
        $data['reports'][] = array(
            'amount' => self::format_time_amount($logged_time),
            'title' => 'Hours Logged',
            'period' => $period
        );

        $timeoff_select = DB::select(DB::expr("SUM(timeoff.duration) as total_leave"))
            ->from(array(Model_Timeoff::REQUESTS_TABLE, 'timeoff'))
            //->where('timeoff.deleted', '=', 0)
            ->and_where('timeoff.status', '=', 'approved');

        if (@$params['trainer_id']) {
            $operator = is_array($params['trainer_id']) ? 'in' : '=';
            $timeoff_select->and_where('timeoff.staff_id', $operator, $params['trainer_id']);
        }
        if (@$params['date_start']) {
            $timeoff_select->and_where('timeoff.period_start_date', '>=', $params['date_start']);
        }
        if (@$params['date_end']) {
            $timeoff_select->and_where('timeoff.period_end_date', '<=', $params['date_end']);
        }
        $total_leave = $timeoff_select->execute()->get('total_leave');
        $data['reports'][] = array(
            'amount' => (int)$total_leave,
            'title' => 'Hours of leave',
            'period' => $period
        );

        $left_time = ((int)substr($scheduled_time, 0, -4) * 60) + (int)substr($scheduled_time, -4, -2)
        -
        ((int)substr($logged_time, 0, -4) * 60) + (int)substr($logged_time, -4, -2);

        $hours   = floor($left_time / 60);
        $minutes = ($left_time % 60);

        $data['reports'][] = array(
            'amount' => ($hours == 0 && $minutes == 0) ? '0m' : (($hours > 0 ? $hours.'h ' : '') . ($minutes > 0 ? $minutes.'m' : '')),
            'title' => 'Left',
            'period' => $period
        );
    }

    // Convert time amount as it is stored in the database to a readable format
    static function format_time_amount($input)
    {
        if (!$input) {
            $input = "000000";
        }

        $hours   = (int)substr($input,  0, -4);
        $minutes = (int)substr($input, -4, -2);

        if ($hours == 0 && $minutes == 0) {
            return '0m';
        } else {
            return trim(($hours > 0 ? $hours.'h ' : '') . ($minutes > 0 ? $minutes.'m' : ''));
        }
    }

    /* Render a list of items as HTML options inside optgroups, split by status. */
    public static function status_optgroups($list, $select_all = false)
    {
        $html = '';
        $option_groups = [];
        $selected_string = ($select_all === true) ? "selected" : "";
        foreach ($list as $list_item) {
            if (isset($option_groups[$list_item['status']])) {
                $option_groups[$list_item['status']][] = $list_item;
            } else {
                $option_groups[$list_item['status']] = [$list_item];
            }
        }

        foreach ($option_groups as $label => $options) {
            $html .= '<optgroup label="'.$label.' ('.count($options).')">';

            foreach ($options as $option) {
                $name = (@$option['department'] ? $option['department'] . ': ' : '') . $option['name'];
                $html .= "<option value={$option['id']} $selected_string>";
                $html .= htmlspecialchars(
                    '<span class="nowrap-ellipsis" title="'.$name.'" style="display: inline-block; margin-bottom: -4px; width: calc(100% - 7em);">'.
                        $name.
                    '</span>'.
                    '<span class="timetable-status-text" data-status="Pending" style="display: inline-block; overflow: auto; margin-bottom: -4px;">'.$option['pending'].' pending</span>'
                );
                $html .= '</option>';
            }

            $html .= '</optgroup>';
        }

        return $html;
    }

    public static function save_slot($data, $user_id = null)
    {
        if ($user_id == null) {
            $user = Auth::instance()->get_user();
            $user_id = $user['id'];
        }

        $slot = arr::set($data, 'id', 'schedule_id', 'trainer_id', 'datetime_start', 'datetime_end', 'topic_id', 'publish', 'delete', 'location_id');
        $slot['modified_by'] = $user_id;
        $slot['date_modified'] = date::now();
        if (!@$slot['id']) {
            $slot['created_by'] = $slot['modified_by'];
            $slot['date_created'] = $slot['date_modified'];
            $inserted = DB::insert(Model_ScheduleEvent::TABLE_TIMESLOTS)->values($slot)->execute();
            $slot_id = $inserted[0];
        } else {
            DB::update(Model_ScheduleEvent::TABLE_TIMESLOTS)->set($slot)->where('id', '=', $slot['id'])->execute();
            $slot_id = $slot['id'];
        }
        if (@$data['ignored_conflicts']) {
            DB::delete(self::TABLE_IGNORES)->where('id', '=', $slot['id'])->execute();
            foreach ($data['ignored_conflicts'] as $ignored_conflict_id) {
                DB::insert(self::TABLE_IGNORES)
                    ->values(array('slot_1_id' => $slot_id, 'slot_2_id' => $ignored_conflict_id))
                    ->execute();
            }
        }
        return $slot_id;
    }
    
    public static function ignore_timeslots($timeslots)
    {
        $inserted = array();
        foreach($timeslots as $timeslot)
        {
            $insert_validator = DB::select('slot_1_id', 'slot_2_id')->from(self::TABLE_IGNORES)
                ->where_open()
                    ->or_where('slot_1_id', '=', $timeslot['conflicts'][0]['id'])
                    ->or_where('slot_1_id', '=', $timeslot['conflicts'][1]['id'])
                ->where_close()
                ->where_open()
                ->or_where('slot_2_id', '=', $timeslot['conflicts'][0]['id'])
                ->or_where('slot_2_id', '=', $timeslot['conflicts'][1]['id'])
                ->where_close()->execute()->count();
            if($insert_validator > 0) {
                continue;
            }
            $insert_array = array('slot_1_id' => $timeslot['conflicts'][0]['id'], 'slot_2_id' => $timeslot['conflicts'][1]['id'] ?? '0');
            $inserted = DB::insert(self::TABLE_IGNORES)->values($insert_array)->execute();
        }
        
        return $inserted;
    }

    public static function search_slot($params = array())
    {
        $conflict_ids = array();
        if (@$params['student_id'] == null) {
            if (Settings::instance()->get('timetable_conflict_detection') == 1) {
                $conflict_ids = Model_Timetables::conflicting_slots($params['after'], $params['before'], null, null, @$params['trainer_id']);
            }
        }

        $select = DB::select(
            (@$params['calculate_rows'] ? DB::expr('SQL_CALC_FOUND_ROWS slots.*') : 'slots.*'),
            array('courses.title', 'course'),
            array('schedules.name', 'schedule'),
            'schedules.booking_type',
            'contacts.first_name', 'contacts.last_name',
            DB::expr('CONCAT_WS(" ", contacts.first_name, contacts.last_name) as contact'),
            array('topics.name', 'topic'),
            DB::expr("IF(slots.location_id, slocations.name, locations.name) as location"),
            DB::expr("IF(slots.location_id, slocations.id, locations.id) as location_id"),
            DB::expr("IF(slots.location_id, splocations.name, plocations.name) as plocation"),
            DB::expr("IF(slots.location_id, splocations.id, plocations.id) as plocation_id"),
            DB::expr('IFNULL(ssubjects.name, csubjects.name) as subject'),
            DB::expr("SUM(if(booking_items.booking_item_id, 1, 0)) as booking_count"),
            DB::expr("0 as conflict"),
            DB::expr("if(slots.max_capacity, slots.max_capacity, schedules.max_capacity) as max_capacity"),
            array('ayears.title', 'academic_year'),
            'schedules.academic_year_id',
            ['statuses.title', 'status']
        )
            ->from(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'slots'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                    ->on('slots.schedule_id', '=', 'schedules.id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('schedules.course_id', '=', 'courses.id')
                ->join(array(Model_Topics::TABLE_TOPICS, 'topics'), 'left')
                    ->on('slots.topic_id', '=', 'topics.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')
                    ->on('schedules.location_id', '=', 'locations.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'plocations'), 'left')
                    ->on('locations.parent_id', '=', 'plocations.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'slocations'), 'left')
                    ->on('slots.location_id', '=', 'slocations.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'splocations'), 'left')
                    ->on('slocations.parent_id', '=', 'splocations.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                    ->on('slots.trainer_id', '=', 'contacts.id')
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'ssubjects'), 'left')
                    ->on('schedules.subject_id', '=', 'ssubjects.id')
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'csubjects'), 'left')
                    ->on('courses.subject_id', '=', 'csubjects.id')
                ->join(array(Model_AcademicYear::TABLE_ACADEMICYEARS, 'ayears'), 'left')
                    ->on('schedules.academic_year_id', '=', 'ayears.id')
                ->join(['plugin_courses_schedules_status', 'statuses'])
                    ->on('schedules.schedule_status', '=', 'statuses.id')
            ->where('slots.delete', '=', 0);

            $select->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'booking_items'), 'left')
                ->on('slots.id', '=', 'booking_items.period_id')
                ->on('booking_items.booking_status', '<>', DB::expr(3))
                ->on('booking_items.delete', '=', DB::expr(0));

        if (@$params['id']) {
            $select->and_where('slots.id', (is_array($params['id']) ? 'in' : '='), $params['id']);
        }

        if (@$params['not_id']) {
            $select->and_where('slots.id', (is_array($params['not_id']) ? 'not in' : '<>'), $params['not_id']);
        }

        if (@$params['academicyear_id']) {
            $select->and_where('schedules.academicyear_id', (is_array($params['academicyear_id']) ? 'in' : '='), $params['academicyear_id']);
        }

        if (@$params['location_id']) {
            $select->and_where('schedules.location_id', (is_array($params['location_id']) ? 'in' : '='), $params['location_id']);
        }

        if (@$params['schedule_id']) {
            $select->and_where('slots.schedule_id', (is_array($params['schedule_id']) ? 'in' : '='), $params['schedule_id']);
        }

        if (@$params['course_id']) {
            $select->and_where('courses.id', (is_array($params['course_id']) ? 'in' : '='), $params['course_id']);
        }

        if (@$params['category_id']) {
            $select->and_where('courses.category_id', (is_array($params['category_id']) ? 'in' : '='), $params['category_id']);
        }

        if (@$params['trainer_id']) {
            $select->and_where_open()
                ->or_where('slots.trainer_id', (is_array($params['trainer_id']) ? 'in' : '='), $params['trainer_id'])
                ->or_where('schedules.trainer_id', (is_array($params['trainer_id']) ? 'in' : '='), $params['trainer_id'])
                ->and_where_close();
        }

        if (@$params['before']) {
            $select->and_where('slots.datetime_start', '<=', $params['before']);
        }

        if (@$params['after']) {
            $select->and_where('slots.datetime_start', '>=', $params['after']);
        }

        if (@$params['topic_id']) {
            $select->and_where('slots.topic_id', (is_array($params['topic_id']) ? 'in' : '='), $params['topic_id']);
        }

        if (@$params['student_id']) {
            $student_id = $params['student_id'];

            $operator = (is_array($student_id) ? 'in' : '=');

            $select
                ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                    ->on('slots.id', '=', 'items.period_id')
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                    ->on('items.booking_id', '=', 'bookings.booking_id')
                ->join([Model_KES_Bookings::DELEGATES_TABLE,  'bhd'], 'left')
                    ->on('bhd.booking_id', '=', 'bookings.booking_id')

                ->and_where_open()
                    // Student is the lead booker
                    ->and_where_open()
                        ->and_where('bookings.contact_id', $operator, $student_id)
                        ->and_where('items.delete', '=', 0)
                        ->and_where('items.booking_status', '<>', Model_KES_Bookings::CANCELLED)
                        ->and_where('bookings.delete', '=', 0)
                        ->and_where('bookings.booking_status', '<>', Model_KES_Bookings::CANCELLED)
                    ->and_where_close()

                    // Or student is a delegate
                    ->or_where_open()
                        ->where('bhd.contact_id', $operator, $student_id)
                        ->and_where('items.delete', '=', 0)
                        ->and_where('items.booking_status', '<>', Model_KES_Bookings::CANCELLED)
                        ->and_where('bookings.delete', '=', 0)
                        ->and_where('bookings.booking_status', '<>', Model_KES_Bookings::CANCELLED)
                        ->and_where('bhd.cancelled', '<>', 1)
                    ->or_where_close()

                    // Or "student" is the teacher
                    ->or_where('slots.trainer_id', (is_array($params['trainer_id']) ? 'in' : '='), $student_id)
                    ->or_where('schedules.trainer_id', (is_array($params['trainer_id']) ? 'in' : '='), $student_id)
                ->and_where_close()
            ;

        }

        if (@$params['offset'] > 0) {
            $select->offset($params['offset']);
        }
        if (@$params['limit'] > 0) {
            $select->limit($params['limit']);
        }

        if (@$params['keyword']) {
            $select->and_where_open()
                ->or_where('schedules.name', 'like', '%' . $params['keyword'] . '%')
                ->or_where('courses.title', 'like', '%' . $params['keyword'] . '%')
                ->or_where('locations.name', 'like', '%' . $params['keyword'] . '%')
                ->or_where('plocations.name', 'like', '%' . $params['keyword'] . '%')
                ->or_where('contacts.first_name', 'like', '%' . $params['keyword'] . '%')
                ->or_where('contacts.last_name', 'like', '%' . $params['keyword'] . '%')
                ->and_where_close();
        }

        if (!empty($params['statuses']) && array_intersect($params['statuses'], ['done', 'booked', 'cancelled', 'conflict', 'pending'])) {
            $select->and_where_open();
                if (in_array('done', $params['statuses'])) {
                    // Items in the past
                    $select
                        ->or_where_open()
                            ->where('slots.datetime_start', '<=', date('Y-m-d H:i:s'))
                            ->and_where('statuses.title', '!=', 'Cancelled')
                        ->or_where_close();
                }

                if (in_array('booked', $params['statuses'])) {
                    // Published items in the future
                    $select
                        ->or_where_open()
                            ->where('slots.datetime_start', '>', date('Y-m-d H:i:s'))
                            ->and_where('slots.publish', '!=', '0')
                            ->and_where('statuses.title', '!=', 'Cancelled')
                        ->or_where_close();
                }

                if (in_array('cancelled', $params['statuses'])) {
                    $select
                        ->or_where_open()
                            ->where('statuses.title', '=', 'Cancelled')
                        ->or_where_close();
                }

                if (in_array('conflict', $params['statuses'])) {

                    if (!empty($conflict_ids)) {
                        $select
                            ->or_where_open()
                                ->where('slots.id', 'in', $conflict_ids)
                            ->or_where_close();
                    } else {
                        $select
                            ->or_where_open()
                                ->where('slots.id', '=', '-1')
                            ->or_where_close();
                    }
                }

                if (in_array('pending', $params['statuses'])) {
                    // Unpublished items in the future
                    $select
                        ->or_where_open()
                            ->where('slots.datetime_start', '>', date('Y-m-d H:i:s'))
                            ->and_where('statuses.title', '!=', 'Cancelled')
                        ->or_where_close();
                }

            $select->and_where_close();
        }

        $select->group_by('slots.id');
        $select->order_by('slots.datetime_start');

        $slots = $select->execute()->as_array();
        DB::query(null, "set @found_rows=found_rows()")->execute();
        $slot_ids = array_column($slots, 'id');

        if (count($slot_ids) > 0) {
            $attendance_counts_query = DB::select('rollcall.timeslot_id', DB::expr("count(*) as count"))
                ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'rollcall'))
                    ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                        ->on('rollcall.booking_id', '=', 'bookings.booking_id')
                    ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'delegates'), 'inner')
                        ->on('rollcall.booking_id', '=', 'delegates.booking_id')
                        ->on('rollcall.delegate_id', '=', 'delegates.contact_id')
                ->where('bookings.delete', '=', 0)
                ->and_where('rollcall.delete', '=', 0)
                ->and_where('rollcall.booking_status', 'in', array(2,4,5))
                ->group_by('rollcall.timeslot_id');
            if (@$params['student_id']) {
                $attendance_counts_query->and_where('rollcall.delegate_id', '=', $params['student_id']);
            }
            $attendance_counts = $attendance_counts_query->execute()->as_array('timeslot_id');

            $ignored_conflicts = DB::select('*')
                ->from(self::TABLE_IGNORES)
                ->where('slot_1_id', 'in', $slot_ids)
                ->or_where('slot_2_id', 'in', $slot_ids)
                ->execute()
                ->as_array();

            $timeslot_counters = array_count_values(array_column($slots, 'schedule_id'));

            foreach ($slots as $i => $slot) {
                $slots[$i]['ignored_conflicts'] = array();
                foreach ($ignored_conflicts as $ignored_conflict) {
                    if ($ignored_conflict['slot_1_id'] == $slot['id']) {
                        $slots[$i]['ignored_conflicts'][] = $ignored_conflict['slot_2_id'];
                    }
                    if ($ignored_conflict['slot_2_id'] == $slot['id']) {
                        $slots[$i]['ignored_conflicts'][] = $ignored_conflict['slot_1_id'];
                    }
                }

                $slots[$i]['timeslots_count'] = $timeslot_counters[$slot['schedule_id']];

                if ($slots[$i]['status'] == 'Cancelled') {
                    $slots[$i]['status'] = 'Cancelled';
                } else if (in_array($slot['id'], $conflict_ids)) {
                    $slots[$i]['status'] = 'Conflict';
                } else if (new DateTime($slots[$i]['datetime_start']) <= new DateTime()) {
                    $slots[$i]['status'] = 'Done';
                } else if ($slots[$i]['conflict'] > 0 || @$params['conflicts']) {
                    $slots[$i]['status'] = 'Conflict';
                } else if ($slots[$i]['publish'] == 0) {
                    $slots[$i]['status'] = 'Pending';
                } else {
                    $slots[$i]['status'] = 'Booked';
                }

                /*too slow when there are schedules with too many slots
                 * $slot_orm = new Model_Course_Schedule_Event($slots[$i]['id']);
                $slots[$i]['attending'] = $slot_orm->count_attending();*/
                $slots[$i]['attending'] = 0;
                if (isset($attendance_counts[$slot['id']])) {
                    $slots[$i]['attending'] = $attendance_counts[$slot['id']];
                }
            }
        }

        return $slots;
    }

    public static function conflicting_slots($after, $before, $schedule_id = null, $timeslot_id = null, $trainer_id = null)
    {
        $check = array('location', 'trainer');
        if ($timeslot_id) {
            $timeslot = new Model_Course_Schedule_Event($timeslot_id);
            $before = $timeslot->datetime_end;
            $after = $timeslot->datetime_start;
        }
        $after = $after ? Database::instance()->escape($after) : null;
        $before = $before ? Database::instance()->escape($before) : null;
        DB::query(null, 'drop temporary table if exists slots1')->execute();
        DB::query(null, 'drop temporary table if exists slots2')->execute();
        DB::query(null,
            "create temporary table slots1 (id int auto_increment primary key, schedule_id int, datetime_start datetime, datetime_end datetime, location_id int, trainer_id int, KEY(schedule_id), KEY(datetime_start), KEY(location_id), KEY(trainer_id))"
        )->execute();
        DB::query(null,
            "create temporary table slots2 (id int auto_increment primary key, schedule_id int, datetime_start datetime, datetime_end datetime, location_id int, trainer_id int, KEY(schedule_id), KEY(datetime_start), KEY(location_id), KEY(trainer_id))"
        )->execute();
        $trainer_id_sql =  "";
        if($trainer_id) {
            if (is_array($trainer_id)) {
                $trainer_id_sql = " and (timeslots.trainer_id IN(" . implode(',', $trainer_id) . ") or schedules.trainer_id IN (".implode(',', $trainer_id)."))";
            } else {
                $trainer_id_sql = " and (timeslots.trainer_id = ". $trainer_id . " or schedules.trainer_id=" . $trainer_id .") " ;
            }
        }

        $insert_sql = "insert into slots1 (id, schedule_id, datetime_start, datetime_end, location_id, trainer_id)" .
            " (select timeslots.id, timeslots.schedule_id, timeslots.datetime_start, timeslots.datetime_end, ifnull(timeslots.location_id, schedules.location_id), ifnull(timeslots.trainer_id, schedules.trainer_id)
                    from plugin_courses_schedules_events timeslots
                        inner join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id
                    where timeslots.`delete` = 0 and timeslots.datetime_start >= " . $after . " and timeslots.datetime_start <= " . $before . $trainer_id_sql . ")";

        try {
            DB::query(null, $insert_sql)->execute();
        } catch (Exception $exc) {
            throw $exc;
        }
        DB::query(null, 'insert into slots2 (select * from slots1)')->execute();
        $timeslot_count = DB::select(DB::expr("count(*) as count"))->from("slots1")->execute()->get("count");
        $conflicts = DB::select(
            array('slots1.id', 'slot_1_id'),
            array('slots2.id', 'slot_2_id')
        )
            ->from('slots1')
                ->join('slots2', 'inner')
                    ->on('slots1.id', '<>', 'slots2.id')
                ->join(array(self::TABLE_IGNORES, 'ignored_conflict_slot_1'), 'left')
                    ->on('slots1.id', '=', 'ignored_conflict_slot_1.slot_1_id')
            ->where('slots1.id', '<>', DB::expr('slots2.id'))
            ->and_where_open();
        if (in_array('trainer', $check)) {
            $conflicts->or_where("slots1.trainer_id", '=', DB::expr("slots2.trainer_id"));
        }
        if (in_array('location', $check)) {
            $conflicts->or_where('slots1.location_id', '=', DB::expr('slots2.location_id'));
        }
        $conflicts->and_where_close();

		//@todo
        if ($timeslot_count > 1000) {//if there are too many timeslots just make a basic comparison; optimize later for better code
            $conflicts->and_where('slots1.datetime_start', '=', DB::expr('slots2.datetime_start'));
        } else {
            $conflicts->and_where_open()
                ->or_where_open()
                    ->and_where('slots1.datetime_start', '>=', DB::expr('slots2.datetime_start'))
                    ->and_where('slots1.datetime_start', '<', DB::expr('slots2.datetime_end'))
                ->or_where_close()
                ->or_where_open()
                    ->and_where('slots1.datetime_end', '>', DB::expr('slots2.datetime_start'))
                    ->and_where('slots1.datetime_end', '<=', DB::expr('slots2.datetime_end'))
                ->or_where_close()
                ->or_where_open()
                    ->and_where('slots1.datetime_start', '<=', DB::expr('slots2.datetime_start'))
                    ->and_where('slots1.datetime_end', '>', DB::expr('slots2.datetime_start'))
                ->or_where_close()
                ->or_where_open()
                    ->and_where('slots1.datetime_start', '<', DB::expr('slots2.datetime_end'))
                    ->and_where('slots1.datetime_end', '>=', DB::expr('slots2.datetime_end'))
                ->or_where_close()
                ->and_where_close();
        }
        if (@$schedule_id) {
            $conflicts->and_where('slots1.schedule_id', (is_array($schedule_id) ? 'in' : '='), $schedule_id);
        }

        if (!empty($timeslot_id)) {
            $conflicts
                ->and_where_open()
                    ->where('slots1.id', '=', $timeslot_id)
                    ->or_where('slots2.id', '=', $timeslot_id)
                ->and_where_close();
        }

        $conflicts = $conflicts->execute()->as_array();

        $ids = array();
        foreach ($conflicts as $conflict) {
            $ignore_conflict_filter = DB::select('slot_1_id', 'slot_2_id')->from(self::TABLE_IGNORES)
                ->or_where_open()
                ->where('slot_1_id', '=', $conflict['slot_1_id'])
                ->where('slot_2_id', '=', $conflict['slot_2_id'])
                ->or_where_close()
                ->or_where_open()
                ->where('slot_2_id', '=', $conflict['slot_1_id'])
                ->where('slot_1_id', '=', $conflict['slot_2_id'])
                ->or_where_close()->execute()->count();
            if($ignore_conflict_filter > 0)
            {
                continue;
            }
            $ids[] = $conflict['slot_1_id'];
            $ids[] = $conflict['slot_2_id'];
        }
        $ids = array_values(array_unique($ids));
        return $ids;
    }

    public static function get_slot($id)
    {
        $slots = self::search_slot(array('id' => $id));
        $slot = @$slots[0];
        return $slot;
    }

    public static function datatable($filter)
    {
        $params = array('calculate_rows' => true);
        if (@$filter['before']) {
            $params['before'] = $filter['before'];
        }
        if (@$filter['after']) {
            $params['after'] = $filter['after'];
        }

        if (@$filter['end_date'])   { $params['before'] = $filter['end_date'];   }
        if (@$filter['start_date']) { $params['after']  = $filter['start_date']; }

        // Temporary legacy support for old UI.
        // The old UI has separate tables for conflicts and non-conflicts.
        $old_ui = (
            !empty($_SERVER['HTTP_REFERER']) &&
            (strpos($_SERVER['HTTP_REFERER'], 'old_ui=') || strpos($_SERVER['HTTP_REFERER'], 'timetable/planner'))
        );

        if ($old_ui) {
            if (@$filter['status'] == 'conflict') {
                $params['conflicts'] = true;
                $params['after'] = date::now();
                if (Settings::instance()->get('timetable_conflict_detection') == 1) {
                    $params['id'] = self::conflicting_slots($params['after'], $params['before'], @$params['schedule_id'], null, @$filter['trainer_id']);
                }
            } else {
                if (Settings::instance()->get('timetable_conflict_detection') == 1) {
                    $params['not_id'] = self::conflicting_slots($params['after'], $params['before'], @$params['schedule_id'], null, @$filter['trainer_id']);
                }
            }
        }

        if (@$filter['sSearch']) {
            $params['keyword'] = $filter['sSearch'];
        }
        if (@$filter['iDisplayStart']) {
            $params['offset'] = $filter['iDisplayStart'];
        }
        if (@$filter['iDisplayLength']) {
            $params['limit'] = $filter['iDisplayLength'];
        }
        if (@$filter['course_id']) {
            $params['course_id'] = $filter['course_id'];
        }
        if (@$filter['schedule_id']) {
            $params['schedule_id'] = $filter['schedule_id'];
        }
        if (@$filter['trainer_id']) {
            $params['trainer_id'] = $filter['trainer_id'];
        }
        if (@$filter['location_id']) {
            $params['location_id'] = $filter['location_id'];
        }
        if (@$filter['topic_id']) {
            $params['topic_id'] = $filter['topic_id'];
        }

        if (!empty($filter['statuses'])) {
            $params['statuses'] = $filter['statuses'];
        }

        if (@$params['conflicts'] && count($params['id']) == 0) {
            $data = array();
            $total = 0;
        } else {
            $data = self::search_slot($params);
            $total = DB::select(DB::expr("@found_rows as total"))->execute()->get('total');
        }
        $result = array(
            'iTotalDisplayRecords' => $total,
            'iTotalRecords' => count($data),
            'aaData' => array(),
            'sEcho' => $filter['sEcho']
        );
//header('content-type: text/plain');print_r($data);exit;
        foreach ($data as $rdata) {
            $row = array();
            $row[] = Form::ib_checkbox(null, 'slot['.$rdata['id'].']') . $rdata['contact'];
            $row[] = $rdata['schedule'];
            $row[] = date('D', strtotime($rdata['datetime_start']));
            $row[] = IbHelpers::formatted_time($rdata['datetime_start'], ['time' => false]);
            $row[] = date('H:i', strtotime($rdata['datetime_start'])) . ' - ' . date('H:i', strtotime($rdata['datetime_end']));
            $row[] = $rdata['location'];
            $row[] = '<span class="timetable-planner-timeslot-status" data-status="' . $rdata['status'] . '">' . $rdata['status'] . '</span>';
            $row[] = '-';
            $row[] = '-';
            $links = '';
            if (@$filter['status'] == 'conflict' || $rdata['status'] == 'Conflict') {
                $links .= '<button type="button" class="btn btn-primary resolve" data-id="' . $rdata['id'] . '" data-schedule_id="' . $rdata['schedule_id'] . '">' . __('Resolve') . '</button>';
            }
            if (@$filter['status'] == 'pending') {
                $links .= '<button type="button" class="btn btn-primary approve" data-id="' . $rdata['id'] . '">' . __('Approve') . '</button>';
            }
            $links .= '<button type="button" class="btn btn-cancel remove timetable-slot-remove" data-id="' . $rdata['id'] . '">' . __('Remove') . '</button>';
            if (@$filter['status'] == 'conflict') {
                $links .= '<button type="button" class="btn btn-primary ignore" data-id="' . $rdata['id'] . '" data-schedule_id="' . $rdata['schedule_id'] . '">' . __('Ignore') . '</button>';
            }
            $row[] = $links;
            $result['aaData'][] = $row;
        }
        return $result;
    }

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        /* This function should use something similar to
         * `Model_Safety_Incident::get_for_datatable`
         * But for legacy support it is instead calling the old datatable function.
         */

        $params = $_GET + $_GET['filters'];
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
        $result = self::datatable($params);

        return $result;
    }

    public static function locations_list()
    {
        $locations = DB::select('locations.id', 'locations.name', DB::expr("count(*) as cnt"))
            ->from(array(Model_Locations::TABLE_LOCATIONS, 'locations'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('locations.id', '=', 'schedules.location_id')
            ->where('schedules.delete', '=', 0)
            ->and_where('locations.delete', '=', 0)
            ->group_by('locations.id')
            ->order_by('locations.name')
            ->execute()
            ->as_array();
        foreach($locations as $i => $location) {
            $locations[$i]['pending'] = 1;
            $locations[$i]['status'] = 'Available'; //Booked, Preferred, Available
        }
        return $locations;
    }

    public static function schedules_list($args = [])
    {
        $q = DB::select('schedules.id', 'schedules.name', 'schedules.course_id')
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'bookings_has_schedules'), 'left')
                    ->on('schedules.id', '=', 'bookings_has_schedules.schedule_id')
                    ->on('bookings_has_schedules.booking_status', '<>', DB::expr(3))
                    ->on('bookings_has_schedules.deleted', '=', DB::expr(0))
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'left')
                    ->on('bookings_has_schedules.booking_id', '=', 'bookings.booking_id')
                    ->on('bookings.booking_status', '<>', DB::expr(3))
                    ->on('bookings.delete', '<>', DB::expr(3))
                ->join(['plugin_ib_educate_bookings_has_delegates', 'has_delegates'], 'left')
                    ->on('has_delegates.booking_id', '=', 'bookings.booking_id')
                ->join([Model_Schedules::TABLE_TIMESLOTS, 'events'], 'left')
                    ->on('events.schedule_id', '=', 'schedules.id')
            ->where('schedules.delete', '=', 0);

        if (!empty($args['ids'])) {
            $q->where('schedules.id', 'in', $args['ids']);
        }

        if (!empty($args['contact_id'])) {
            $q
                ->and_where_open()
                    ->where('bookings.contact_id', '=', $args['contact_id'])
                    ->or_where('has_delegates.contact_id', '=', $args['contact_id'])
                    ->or_where('schedules.trainer_id', '=', $args['contact_id'])
                    ->or_where('events.trainer_id', '=', $args['contact_id'])
                ->and_where_close();
        }

        $schedules = $q
            ->group_by('schedules.id')
            ->order_by('schedules.name')
            ->execute()
            ->as_array();
        foreach ($schedules as $i => $schedule) {
            $schedule_time = Model_Timetables::get_hours_status_for_schedule($schedule['id']);
            $schedules[$i]['booked'] = $schedule_time['time_booked'];
            $schedules[$i]['done'] = $schedule_time['time_done'];
            $schedules[$i]['hours'] = $schedule_time['hours'];
            $total_hours = $schedule_time['time_done'] + $schedule_time['time_booked'];
            $schedules[$i]['pending'] = ($schedules[$i]['hours'] > $total_hours) ? $schedules[$i]['hours'] - $schedule_time['time_done'] - $schedule_time['time_booked'] : '0';
        }
        return $schedules;
    }

    public static function trainer_list()
    {
        $trainers = DB::select('trainers.id', DB::expr("CONCAT_WS(' ',trainers.first_name, trainers.last_name) as name"), DB::expr("SUM(if(bookings_has_schedules.schedule_id is null, 0, 1)) as booked"))
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'inner')
                    ->on('schedules.trainer_id', '=', 'trainers.id')
                ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'bookings_has_schedules'), 'left')
                    ->on('schedules.id', '=', 'bookings_has_schedules.schedule_id')
                    ->on('bookings_has_schedules.booking_status', '<>', DB::expr(3))
                    ->on('bookings_has_schedules.deleted', '=', DB::expr(0))
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'left')
                    ->on('bookings_has_schedules.booking_id', '=', 'bookings.booking_id')
                    ->on('bookings.booking_status', '<>', DB::expr(3))
                    ->on('bookings.delete', '<>', DB::expr(3))
            ->where('schedules.delete', '=', 0)
            ->group_by('trainers.id')
            ->order_by('trainers.first_name')
            ->order_by('trainers.last_name')
            ->execute()
            ->as_array();
        foreach ($trainers as $i => $trainer) {
            $trainers[$i]['pending'] = 1;
            $trainers[$i]['status'] = 'All';
        }
        return $trainers;
    }

    public static function topic_list()
    {
        $topics = DB::select('topics.id', 'topics.name', DB::expr("count(*) as pending"))
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                ->join(array('plugin_courses_schedules_have_topics', 'have_topics'), 'inner')
                    ->on('schedules.id', '=', 'have_topics.schedule_id')
                ->join(array(Model_Topics::TABLE_TOPICS, 'topics'), 'inner')
                    ->on('have_topics.topic_id', '=', 'topics.id')
            ->where('schedules.delete', '=', 0)
            ->group_by('topics.id')
            ->order_by('topics.name')
            ->execute()
            ->as_array();
        foreach ($topics as $i => $topic) {
            $topics[$i]['status'] = 'All';
        }
        return $topics;
    }
    
    public static function get_hours_status_for_schedule($schedule_id)
    {
        // Select all schedule timeslots times that have passed and are pending for that schedule
        $return = array();
        $return['time_done'] = DB::select(
            array(
                DB::expr("SUM(time_to_sec(timediff(`timeslots`.`datetime_end`, `timeslots`.`datetime_start`)))"),
                'time_seconds_done'
            ))
            ->from(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'))
            ->join(array(Model_ScheduleEvent::TABLE_SCHEDULES, 'schedules'), 'left')
            ->on('schedules.id', '=', 'timeslots.schedule_id')
            ->where('timeslots.schedule_id', '=', $schedule_id)
            ->where('schedules.delete', '=', 0)
            ->where('timeslots.datetime_end', '<', DB::expr('NOW()'))
            ->execute()
            ->current()["time_seconds_done"] / 3600;
        $return['time_done'] = round($return['time_done'], 2) ?? '0';
        $return['time_booked'] =  DB::select(
            array(DB::expr("SUM(time_to_sec(timediff(`timeslots`.`datetime_end`, `timeslots`.`datetime_start`)))"),'time_seconds_pending'))
            ->from(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'))
            ->join(array(Model_ScheduleEvent::TABLE_SCHEDULES, 'schedules'), 'left')
            ->on('schedules.id', '=', 'timeslots.schedule_id')
            ->where('timeslots.schedule_id', '=', $schedule_id)
            ->where('schedules.delete', '=', 0)
            ->where('timeslots.delete', '=', 0)
            ->where('timeslots.datetime_end', '>', DB::expr('NOW()'))
            ->execute()
            ->current()["time_seconds_pending"] / 3600;
        $return['time_booked'] = round($return['time_booked'], 2) ?? '0';
        $return['hours'] = DB::select(
            array(DB::expr("sum(c.hours)"),'hours'))->from(array(Model_Coursecredits::HAS_SCHEDULES_TABLE, 'cs'))
            ->join(array(Model_Coursecredits::CREDITS_TABLE, 'c'), 'left')
            ->on('cs.credit_id', '=', 'c.id')->where("cs.schedule_id", "=", $schedule_id)
            ->execute()->current()['hours'] ?? '0';
        return $return;
    }

    public static function get_filter_options($filters = [], $args = [])
    {
        $filter_menu_options = [];

        if (in_array('activities', $filters)) {
            $activities = [
                'booking'   => __('Bookings'),
                'timeslot'  => __('Timeslots'),
                'exam'      => __('Exams'),
                'todo'      => __('Todos'),
                'timesheet' => __('Timesheets'),
                'timeoff'   => __('Timeoff'),
            ];

            $activity_options = [];
            foreach ($activities as $name => $label) {
                $activity_options[$name] = '<span class="timetable-activities-item" data-type="'.$name.'"></span> '.htmlentities($label);
            }

            $filter_menu_options[] = ['name' => 'activities', 'label' => 'Activities', 'options' => $activity_options, 'html' => true, 'selected' => @$args['selected_activity']];
        }

        if (in_array('blackouts', $filters)) {
            $calendar_types = ORM::factory('Calendar_type')->order_by('title')->find_all_published();

            $options = [];
            foreach ($calendar_types as $type) {
                $options[$type->title] = '<span class="timetable-activities-item " data-type="blackout-'.htmlspecialchars($type->title).'"></span> '.htmlentities($type->title);
            }

            $filter_menu_options[] =  ['name' => 'blackouts', 'label' => 'Blackouts',  'options' => $options, 'html' => true];
        }

        if (in_array('courses', $filters)) {
            if (!empty($args['selected_contact_id'])) {
                $schedule_args['contact_id'] = $args['selected_contact_id'];
                // Only get courses relevant to the selected contact (ones where they are a lead booker, delegate or trainer)
                $schedules = Model_Timetables::schedules_list(!empty($schedule_args) ? $schedule_args : []);
                $course_ids = array_column($schedules, 'course_id');
                $courses = empty($course_ids)
                    ? []
                    : ORM::factory('Course')->order_by('course.title')->where('id', 'in', $course_ids)->find_all()->as_array();
            } else {
                $courses = ORM::factory('Course')->order_by('course.title')->find_all_undeleted()->as_array();
            }

            $options = [];
            foreach ($courses as $course) {
                $options[$course->id] = $course->title;
            }

            $filter_menu_options[] = [
                'name' => 'course_id',
                'label' => 'Courses',
                'options' => $options
            ];
        }


        if (in_array('family_members', $filters)) {
            if (!empty($args['contact_id'])) {
                $c = new Model_Contacts3($args['contact_id']);
                $family_members = $c->family ? $c->family->get_members() : [$c];
                $family_member_options = [];

                foreach ($family_members as $member) {
                    $family_member_options[$member->get_id()] = $member->get_first_name() . ' ' . $member->get_last_name();
                }

                $filter_menu_options[] = [
                    'name' => 'family_members',
                    'label' => 'Members',
                    'options' => $family_member_options,
                    'selected' => isset($args['selected_contact_id']) ? $args['selected_contact_id'] : ''
                ];
            }
        }

        if (in_array('locations', $filters)) {
            $locations = Model_Timetables::locations_list();
            $location_options = [];
            foreach ($locations as $location) {
                $location_options[$location['id']] = $location['name'];
            }

            $filter_menu_options[] = ['name' => 'location_id', 'label' => 'Location',   'options' => $location_options];
        }

        if (in_array('schedules', $filters)) {
            $schedule_args = [];

            if (!empty($args['selected_schedule_id'])) {
                $schedule_args['ids'] = $args['selected_schedule_id'];
            }
            if (!empty($args['selected_contact_id'])) {
                $schedule_args['contact_id'] = $args['selected_contact_id'];
            }

            $schedules = !empty($schedule_args)
                ? Model_Timetables::schedules_list($schedule_args)
                : Model_Timetables::schedules_list();

            $schedule_options = [];
            foreach ($schedules as $schedule) {
                $schedule_statuses = [
                    ['name' => 'Done',    'amount' => $schedule['done']],
                    ['name' => 'Booked',  'amount' => $schedule['booked']],
                    ['name' => 'Pending', 'amount' => $schedule['pending']]
                ];
                $progress_bar = View::factory('snippets/progress_bar')->set('statuses', $schedule_statuses)->set('total_hours', $schedule['hours']);

                $schedule_options[$schedule['id']] =
                    '<span class="form-filter-overflow form-filter-text" title="' . htmlspecialchars($schedule['name']) . '">' .
                    htmlspecialchars($schedule['name']) .
                    '</span>' . $progress_bar;
            }

            $filter_menu_options[] = [
                'name' => 'schedule_id',
                'label' => 'Schedules',
                'options' => $schedule_options,
                'selected' => isset($args['selected_schedule_id']) ? $args['selected_schedule_id'] : [],
                'html' => true
            ];
        }

        if (in_array('statuses', $filters)) {
            $statuses = [
                'booked'    => __('Scheduled'),
                'done'      => __('Done'),
                'conflict'  => __('Conflict'),
                'cancelled' => __('Cancelled'),
            ];
            $options = [];
            foreach ($statuses as $name => $label) {
                $options[$name] = '<span class="timetable-activities-item" data-status="'.$name.'"></span> '.htmlspecialchars($label);
            }

            $filter_menu_options[] = [
                'name'     => 'statuses',
                'label'    => 'Statuses',
                'options'  => $options,
                'selected' => isset($args['statuses']) ? $args['statuses'] : [],
                'html'     => true
            ];
        }

        if (in_array('topics', $filters)) {
            $topics = Model_Timetables::topic_list();
            $topic_options = [];
            foreach ($topics as $topic) {
                $topic_options[$topic['id']] = $topic['name'];
            }

            $filter_menu_options[] = ['name' => 'topic_id', 'label' => 'Topics', 'options' => $topic_options];
        }

        if (in_array('trainers', $filters)) {
            if (!Auth::instance()->has_access('courses_schedule_edit')) {
                $user = Auth::instance()->get_user();
                $franchisee_contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
                $trainers = [$franchisee_contact];
            } else {
                $trainers = Model_Contacts3::get_teachers();
            }
            $trainer_options = [];
            foreach ($trainers as $trainer) {
                $trainer_options[$trainer['id']] = $trainer['full_name'];
            }

            $filter_menu_options[] = ['name' => 'trainer_id', 'label' => 'Trainers',   'options' => $trainer_options];

        }

        return $filter_menu_options;
    }
}
