<?php

class Model_Bookings_Bookingdigesttrigger extends Model_Automations_Trigger
{
    const NAME = 'Booking digest';
    protected $joined = array();
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('booking_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->initiator = Model_Automations_Trigger::INITIATOR_CRON;
        $this->is_digest = true;

        $this->filters = array(
            array('field' => 'course_type_id', 'label' => 'Course Type', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_category_id', 'label' => 'Course Category', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_subject_id', 'label' => 'Course Subject', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_id', 'label' => 'Course', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'schedule_id', 'label' => 'Schedule', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'trainer_id', 'label' => 'Trainer', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'booking_date_interval', 'label' => 'Booking Date', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'start_date_interval', 'label' => 'Start Date(Relative)', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'start_date', 'label' => 'Start Date', 'operators' => array('=' => '=', '>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'end_date_interval', 'label' => 'End Date(Relative)', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'end_date', 'label' => 'End Date', 'operators' => array('=' => '=', '>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'schedule_capacity', 'label' => 'Schedule Capacity (%)', 'operators' => array('>=' => 'MORE', '<=' => 'LESS')),
        );
        $this->generated_message_params = array(
        );

        $this->repeat_fields = array(
            array(
                'field' => 'schedule_id',
                'label' => 'Schedule',
            ),
            array(
                'field' => 'trainer_id',
                'label' => 'Trainer',
            ),
            array(
                'field' => 'student_id',
                'label' => 'student',
            ),
        );
    }

    protected function _join_courses()
    {
        $joined = in_array('courses', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'courses';
            $this->_join_schedule();
            $this->filter_select->select_array(
                array(
                    array('courses.id', 'courseid'),
                    array('courses.title', 'course'),
                    'categories.category',
                    array('subjects.name', 'subject'),
                    'levels.level',
                )
            )
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('schedules.course_id', '=', 'courses.id')
                ->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')
                    ->on('courses.category_id', '=', 'categories.id')
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                    ->on('courses.subject_id', '=', 'subjects.id')
                ->join(array(Model_Levels::LEVEL_TABLE, 'levels'), 'left')
                    ->on('courses.level_id', '=', 'levels.id');
        }
    }

    protected function _join_has_schedule()
    {
        $joined = in_array('has_schedule', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'has_schedule';
            $this->filter_select
                ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'left')
                    ->on('bookings.booking_id', '=', 'has_schedules.booking_id')
                    ->on('has_schedules.booking_status', '<>', DB::expr(3));
        }
    }

    protected function _join_schedule()
    {
        $joined = in_array('schedule', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'schedule';
            $this->_join_has_schedule();
            $this->filter_select->select_array(
                array(
                    array('schedules.id', 'scheduleid'),
                    array('schedules.name', 'schedule'),
                    DB::expr("DATE_FORMAT(schedules.start_date, '%d %M %Y') as startdate"),
                    DB::expr("DATE_FORMAT(schedules.end_date, '%d %M %Y') as enddate"),
                )
            )->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                ->on('has_schedules.schedule_id', '=', 'schedules.id');
        }
    }

    protected function _join_schedule_location()
    {
        $joined = in_array('schedule_location', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'schedule_location';
            $this->_join_schedule();
            $this->filter_select->select_array(
                array(
                    array("schedule_parent_locations.name", "location"),
                    array("schedule_locations.name", "sublocation"),
                )
            )->join(array(Model_Locations::TABLE_LOCATIONS, 'schedule_locations'), 'left')
                ->on('schedules.location_id', '=', 'schedule_locations.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'schedule_parent_locations'), 'left')
                ->on('schedule_locations.parent_id', '=', 'schedule_parent_locations.id');
        }
    }

    protected function _join_trainer()
    {
        $joined = in_array('trainer', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'trainer';
            $this->_join_schedule();
            $this->filter_select
                ->select_array(
                    array(
                        array('schedules.trainer_id', 'trainerid'),
                        DB::expr('CONCAT_WS(" ", trainers.first_name, trainers.last_name) as trainername'),
                        array('trainer_emails.value', 'traineremail'),
                        DB::expr("CONCAT_WS('', trainer_mobiles.country_dial_code, trainer_mobiles.dial_code, trainer_mobiles.value) as trainermobile")
                    )
                )
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'left')
                    ->on('schedules.trainer_id', '=', 'trainers.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'trainer_mobiles'), 'left')
                    ->on('trainers.notifications_group_id', '=', 'trainer_mobiles.group_id')
                    ->on('trainer_mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'trainer_emails'), 'left')
                    ->on('trainers.notifications_group_id', '=', 'trainer_emails.group_id')
                    ->on('trainer_emails.notification_id', '=', DB::expr(1));
        }
    }

    protected function _join_delegate()
    {
        $joined = in_array('delegate', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'delegate';
            $this->_join_has_schedule();
            $this->filter_select->select_array(
                array(
                    DB::expr('IF(students.id is null, lead_bookers.first_name, students.first_name) as studentfirstname'),
                    DB::expr('IF(students.id is null, lead_bookers.last_name, students.last_name) as studentlastname'),
                    DB::expr('IF(students.id is null, CONCAT_WS(" ", lead_bookers.first_name, lead_bookers.last_name), CONCAT_WS(" ", students.first_name, students.last_name)) as studentname'),
                    DB::expr('IF(students.id is null, lead_bookers.id, students.id) as studentid'),
                    DB::expr('IF(students.id is null, lead_booker_emails.value, emails.value) as studentemail'),
                    DB::expr('IF(students.id is null, CONCAT_WS(\'\', lead_booker_mobiles.country_dial_code, lead_booker_mobiles.dial_code, lead_booker_mobiles.value), CONCAT_WS(\'\', mobiles.country_dial_code, mobiles.dial_code, mobiles.value)) as studentmobile'),

                    DB::expr('CONCAT_WS(" ", lead_bookers.first_name, lead_bookers.last_name) as leadbookername'),
                    array('bookings.contact_id', 'leadbookerid'),
                    array('lead_booker_emails.value', 'leadbookeremail'),
                    DB::expr("CONCAT_WS('', lead_booker_mobiles.country_dial_code, lead_booker_mobiles.dial_code, lead_booker_mobiles.value) as leadbookermobile")
                )
            )
                ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_students'), 'left')
                    ->on('has_schedules.booking_id', '=', 'has_students.booking_id')
                    ->on('has_students.deleted', '=', DB::expr(0))
                    ->on('has_students.cancelled', '=', DB::expr(0))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'left')
                    ->on('has_students.contact_id', '=', 'students.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')
                    ->on('students.notifications_group_id', '=', 'emails.group_id')
                    ->on('emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobiles'), 'left')
                    ->on('students.notifications_group_id', '=', 'mobiles.group_id')
                    ->on('mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'lead_bookers'), 'left')
                    ->on('bookings.contact_id', '=', 'lead_bookers.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'lead_booker_emails'), 'left')
                    ->on('lead_bookers.notifications_group_id', '=', 'lead_booker_emails.group_id')
                    ->on('lead_booker_emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'lead_booker_mobiles'), 'left')
                    ->on('lead_bookers.notifications_group_id', '=', 'lead_booker_mobiles.group_id')
                    ->on('lead_booker_mobiles.notification_id', '=', DB::expr(2))
                ->group_by('has_students.contact_id');
        }
    }

    protected function _join_delagate_parent()
    {
        $joined = in_array('delagate_parent', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'delagate_parent';
            $this->_join_delegate();
            $this->filter_select->select_array(
                array(
                    DB::expr('CONCAT_WS(" ", parents.first_name, parents.last_name) as parentname'),
                    array('parents.id', 'parentid'),
                    array('parent_emails.value', 'parentemail'),
                    DB::expr("CONCAT_WS('', parent_mobiles.country_dial_code, parent_mobiles.dial_code, parent_mobiles.value) as parentmobile")
                )
            )
                ->join(array(Model_Contacts3::FAMILY_TABLE, 'families'), 'left')
                    ->on('students.family_id', '=', 'families.family_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'parents'), 'left')
                    ->on('families.primary_contact_id', '=', 'parents.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'parent_mobiles'), 'left')
                    ->on('parents.notifications_group_id', '=', 'parent_mobiles.group_id')
                    ->on('parent_mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'parent_emails'), 'left')
                    ->on('parents.notifications_group_id', '=', 'parent_emails.group_id')
                    ->on('parent_emails.notification_id', '=', DB::expr(1));
        }


    }

    protected function _join_delegate_address()
    {
        $joined = in_array('delegate_address', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'delegate_address';
            $this->_join_delegate();
            $this->filter_select->select_array(
                array(
                    array('student_addresses.address1', 'studentaddress1'),
                    array('student_addresses.address2', 'studentaddress2'),
                    array('student_addresses.address3', 'studentaddress3'),
                    array('student_addresses.postcode', 'studentpostcode'),
                    array('student_addresses.town', 'studenttown'),
                    array('student_address_counties.name', 'studentcounty'),
                )
            )
                ->join(array(Model_Residence::ADDRESS_TABLE, 'student_addresses'), 'left')
                    ->on('students.residence', '=', 'student_addresses.address_id')
                ->join(array('engine_counties', 'student_address_counties'), 'left')
                    ->on('student_addresses.county', '=', 'student_address_counties.id');
        }
    }

    public function filter($data, $sequence)
    {
        $this->joined = array(); // reset joins from previos calls
        $variables = array(
            'bookingid',
            'tuapplication',
            'leadbookername',
            'leadbookerid',
            'leadbookeremail',
            'leadbookermobile',
            'studentname',
            'studentfirstname',
            'studentlastname',
            'studentid',
            'studentemail',
            'studentmobile',
            'studentaddress1',
            'studentaddress2',
            'studentaddress3',
            'studentpostcode',
            'studenttown',
            'studentcounty',
            'parentname',
            'parentid',
            'parentemail',
            'parentmobile',
            'courseid',
            'course',
            'category',
            'subject',
            'level',
            'scheduleid',
            'schedule',
            'location',
            'sublocation',
            'trainername',
            'trainerid',
            'traineremail',
            'trainermobile',
            'startdate',
            'enddate',
        );

        $display_parent_tags = Settings::instance()->get('contacts_create_family') == 1;
        if (!$display_parent_tags) {
            foreach ($this->generated_message_params as $i => $param) {
                if (strpos($param, 'parent') != false) {
                    unset($this->generated_message_params[$i]);
                }
            }
            $this->generated_message_params = array_values($this->generated_message_params);
        }

        $this->filter_select = DB::select(
            array('bookings.booking_id', 'bookingid')
        )
            ->distinct('*')
            ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
            ->where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', '<>', 3)
            ->and_where('bookings.booking_status', '<>', 6)
            ->group_by('bookings.booking_id');

        $select = $this->filter_select;
        if (isset($data['booking_id'])) {
            $select->and_where('bookings.booking_id', (is_array($data['booking_id']) ? 'in' : '='), $data['booking_id']);
        }

        if (
            in_array('courseid', $variables) ||
            in_array('course', $variables) ||
            in_array('category', $variables) ||
            in_array('subject', $variables) ||
            in_array('level', $variables)
        ) {
            $this->_join_courses();
        }

        if (
            in_array('scheduleid', $variables) ||
            in_array('schedule', $variables) ||
            in_array('startdate', $variables) ||
            in_array('enddate', $variables) ||
            in_array('location', $variables) ||
            in_array('sublocation', $variables)
        ) {
            $this->_join_schedule();
        }

        if (
            in_array('location', $variables) ||
            in_array('sublocation', $variables)
        ) {
            $this->_join_schedule_location();
        }

        if (
            in_array('traineremail', $variables) ||
            in_array('trainermobile', $variables) ||
            in_array('trainername', $variables) ||
            in_array('trainerid', $variables)
        ) {
            $this->_join_trainer();
        }

        if (
            in_array('studentaddress1', $variables) ||
            in_array('studentaddress2', $variables) ||
            in_array('studentaddress3', $variables) ||
            in_array('studentpostcode', $variables) ||
            in_array('studenttown', $variables) ||
            in_array('studentcounty', $variables) ||
            in_array('studentname', $variables) ||
            in_array('studentfirstname', $variables) ||
            in_array('studentlastname', $variables) ||
            in_array('studentemail', $variables) ||
            in_array('studentmobile', $variables) ||
            in_array('studentname', $variables) ||
            in_array('studentid', $variables)
        ) {
            $this->_join_delegate();
            if (
                in_array('parentemail', $variables) ||
                in_array('parentmobile', $variables) ||
                in_array('parentname', $variables) ||
                in_array('parentid', $variables)
            ) {
                $this->_join_delagate_parent();
            }
            if (
                in_array('studentaddress1', $variables) ||
                in_array('studentaddress2', $variables) ||
                in_array('studentaddress3', $variables) ||
                in_array('studentpostcode', $variables) ||
                in_array('studenttown', $variables) ||
                in_array('studentcounty', $variables)
            ) {
                $this->_join_delegate_address();
            }
        }

        $select = $this->filter_select;
        $conditions_mode = $sequence['conditions_mode'];
        $conditions = $sequence['conditions'];

        $condition_function = 'and_where';
        $condition_open_function = 'and_where_open';
        $condition_close_function = 'and_where_close';
        if (!empty($conditions)) {
            if ($conditions_mode == 'OR') {
                $select->and_where_open();
                $condition_function = 'or_where';
                $condition_open_function = 'and_where_open';
                $condition_close_function = 'and_where_close';
            } else {
                $select->and_where_open();
            }
            foreach ($conditions as $condition) {
                $operator = $condition['operator'];
                if (count($condition['values']) > 1) {
                    foreach ($condition['values'] as $value) {
                        $condition['val'][] = $value['val'];
                    }
                } else {
                    foreach ($condition['values'] as $value) {
                        $condition['val'] = $value['val'];
                    }
                }
                if (is_array($condition['val'])) {
                    if ($operator == '=') {
                        $operator = 'in';
                    }
                    if ($operator == '<>') {
                        $operator = 'not in';
                    }
                }
                switch ($condition['field']) {
                    case 'course_type_id':
                        $this->_join_courses();
                        call_user_func(array($select, $condition_function), 'courses.type_id', $operator,
                            $condition['val']);
                        break;
                    case 'course_category_id':
                        $this->_join_courses();
                        call_user_func(array($select, $condition_function), 'courses.category_id', $operator,
                            $condition['val']);
                        break;
                    case 'course_subject_id':
                        $this->_join_courses();
                        call_user_func(array($select, $condition_function), 'courses.subject_id', $operator,
                            $condition['val']);
                        break;
                    case 'course_id':
                        $this->_join_courses();
                        call_user_func(array($select, $condition_function), 'schedules.course_id', $operator,
                            $condition['val']);
                        break;
                    case 'schedule_id':
                        $this->_join_schedule();
                        call_user_func(array($select, $condition_function), 'schedules.id', $operator,
                            $condition['val']);
                        break;
                    case 'trainer_id':
                        $this->_join_trainer();
                        call_user_func(array($select, $condition_function), 'schedules.trainer_id', $operator,
                            $condition['val']);
                        break;
                    case 'booking_date_interval':
                        call_user_func(array($select, $condition_function . '_open'));
                        if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                            Model_Automations_Trigger::filter_date_interval_helper($select, 'bookings.created_date',
                                $condition['execute'], $operator, $interval[2], $interval[1]);
                        }
                        call_user_func(array($select, $condition_function . '_close'));
                        break;
                    case 'start_date_interval':
                        $this->_join_schedule();
                        call_user_func(array($select, $condition_function . '_open'));
                        if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                            Model_Automations_Trigger::filter_date_interval_helper($select, 'schedules.start_date',
                                $condition['execute'], $operator, $interval[2], $interval[1]);
                        }
                        call_user_func(array($select, $condition_function . '_close'));
                        break;
                    case 'start_date':
                        $this->_join_schedule();
                        call_user_func(array($select, $condition_function), DB::expr('cast(schedules.start_date as date)'), $operator, $condition['val']);
                        break;
                    case 'end_date_interval':
                        $this->_join_schedule();
                        call_user_func(array($select, $condition_function . '_open'));
                        if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                            Model_Automations_Trigger::filter_date_interval_helper($select, 'schedules.end_date',
                                $condition['execute'], $operator, $interval[2], $interval[1]);
                        }
                        call_user_func(array($select, $condition_function . '_close'));
                        break;
                    case 'end_date':
                        $this->_join_schedule();
                        call_user_func(array($select, $condition_function), DB::expr('cast(schedules.end_date as date)'), $operator, $condition['val']);
                        break;
                    case 'schedule_capacity':
                        $this->_join_schedule();
                        $capacity_sub_query = DB::select('bookings.booking_id', DB::expr('count(*) as multiplier'))
                            ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                            ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'), 'left')
                            ->on('bookings.booking_id', '=', 'has_delegates.booking_id')
                            ->on('has_delegates.deleted', '=', 0)
                            ->group_by('bookings.booking_id');
                        $capacity_query = DB::select('schedules.id',
                            DB::expr('floor((sum(std_count.multiplier) / schedules.max_capacity) * 100) as capacity'))
                            ->from(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'))
                            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                            ->on('has_schedules.schedule_id', '=', 'schedules.id')
                            ->join(array($capacity_sub_query, 'std_count'), 'inner')
                            ->on('has_schedules.booking_id', '=', 'std_count.booking_id')
                            ->where('has_schedules.deleted', '=', 0)
                            ->and_where('has_schedules.booking_status', '<>', 3)
                            ->group_by('has_schedules.schedule_id');
                        $select->join(array($capacity_query, 'schedule_capacity'), 'inner')
                            ->on('schedules.id', '=', 'schedule_capacity.id');
                        call_user_func(array($select, $condition_function), 'schedule_capacity.capacity', $operator,
                            $condition['val']);
                        break;
                    //////////////////
                    case 'task_status':
                    case 'assessment_status':
                    case 'assignment_status':
                        $select
                            ->join(array(Model_Todos::ASSIGNED_STUDENTS_TABLE, 'todo_has_contacts'), 'inner')
                            ->on('bookings.contact_id', '=', 'todo_has_contacts.contact_id')
                            ->and_where('todo_has_contacts.status', $operator, $condition['val']);
                        break;
                    case 'survey_status':
                        $select
                            ->join(array(Model_SurveyResult::RESULT_TABLE, 'survey_results'), 'left')
                            ->on('bookings.contact_id', '=', 'survey_results.survey_author')
                            ->and_where('survey_results.survey_id', '=', $condition['val']);
                        if ($condition['operator'] == 'pending') {
                            $select->and_where('survey_results.survey_id', 'is', null);
                        } else {
                            $select->and_where('survey_results.survey_id', 'is not', null);
                        }
                        break;
                    case 'transaction_type':
                        /*$select
                            ->join(array(Model_KES_Bookings::TRANSACTIONS_TABLE, 'transactions'), 'inner')
                                ->on('bookings.booking_id', '=', 'transactions.booking_id')
                            ->and_where('transactions.type', $operator, $condition['val']);*/
                        $select->and_where('bookings.payment_method', $operator, $condition['val']);
                        break;
                    case 'booking_start_date':
                        $start_end_sub_query = DB::select(
                            'items.booking_id',
                            DB::expr('min(timeslots.datetime_start) as booking_start_datetime'),
                            DB::expr('max(timeslots.datetime_start) as booking_end_datetime')
                        )
                            ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'))
                            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                            ->on('items.period_id', '=', 'timeslots.id')
                            ->where('items.delete', '=', 0)
                            ->and_where('items.booking_status', '<>', 3)
                            ->group_by('items.booking_id');
                        $select
                            ->join(array($start_end_sub_query, 'start_end'), 'inner')
                            ->on('bookings.booking_id', '=', 'start_end.booking_id')
                            ->and_where('start_end.booking_start_datetime', $operator, $condition['val']);
                        break;
                    case 'booking_start_date':
                        $start_end_sub_query = DB::select(
                            'items.booking_id',
                            DB::expr('min(timeslots.datetime_start) as booking_start_datetime'),
                            DB::expr('max(timeslots.datetime_start) as booking_end_datetime')
                        )
                            ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'))
                            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                            ->on('items.period_id', '=', 'timeslots.id')
                            ->where('items.delete', '=', 0)
                            ->and_where('items.booking_status', '<>', 3)
                            ->group_by('items.booking_id');
                        $select
                            ->join(array($start_end_sub_query, 'start_end'), 'inner')
                            ->on('bookings.booking_id', '=', 'start_end.booking_id')
                            ->and_where('start_end.booking_end_datetime', $operator, $condition['val']);
                        break;
                    case 'application_status':
                        $this->_join_delegate();
                        $select
                            ->join(array(Model_KES_Bookings::BOOKING_APPLICATIONS, 'applications'), 'left')
                                ->on('bookings.booking_id', '=', 'applications.booking_id')
                                ->on('applications.delegate_id', '=', 'students.id')
                            ->and_where('applications.status', $operator, $condition['val']);
                    default:
                        throw new Exception("Unknown condition field: " . $condition['field']);
                        break;
                }
            }
            if ($conditions_mode == 'OR') {
                $select->and_where_close();
            } else {
                $select->and_where_close();
            }
        }
        $bookings = $select->execute()->as_array();
        if (in_array('tuapplication', $variables)) {
            $form_page    = new Model_Page(Settings::instance()->get('accreditation_application_page'));
            foreach ($bookings as $i => $booking) {
                $bookings[$i]['tuapplication'] = URL::base() . $form_page->name_tag . '?booking_id=' . $booking['bookingid'] . '&contact_id=' . $booking['studentid'];
            }
        }
        $tmpfile = tempnam(Kohana::$cache_dir, 'boookingdigest');
        $fid = fopen($tmpfile, "c+");
        if (count($bookings) > 0) {
            fputcsv($fid, array_keys($bookings[0]), ',', '"');
            foreach ($bookings as $booking) {
                fputcsv($fid, $booking, ',', '"');
            }
            fclose($fid);
            return array(
                array(
                    'automation_digest' => true,
                    'automation_attachments' => array(
                        array(
                            'path' => $tmpfile,
                            'name' => 'bookingdigest-' . date('Ymd') . '.csv'
                        )
                    )
                )
            );
        } else {
            return array();
        }
    }
}
