<?php

class Model_Courses_Schedulespaceavailabletrigger extends Model_Automations_Trigger
{
    const NAME = 'a Course space available';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('schedule_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->initiator = Model_Automations_Trigger::INITIATOR_USER;
        $this->multiple_results = true;

        $this->filters = array(
            array('field' => 'course_type_id', 'label' => 'Course Type', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_category_id', 'label' => 'Course Category', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_subject_id', 'label' => 'Course Subject', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_id', 'label' => 'Course', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'schedule_id', 'label' => 'Schedule', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'trainer_id', 'label' => 'Trainer', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'schedule_capacity', 'label' => 'Schedule Capacity(%)', 'operators' => array('>=' => 'MORE', '<=' => 'LESS')),
        );
        $this->generated_message_params = array(
            '@contactname@',
            '@contactfirstname@',
            '@contactlastname@',
            '@contactid@',
            '@contactemail@',
            '@contactmobile@',
            '@contactaddress1@',
            '@contactaddress2@',
            '@contactaddress3@',
            '@contactpostcode@',
            '@contacttown@',
            '@contactcounty@',
            '@course@',
            '@category@',
            '@subject@',
            '@level@',
            '@scheduleid@',
            '@schedule@',
            '@courseurl@',
            '@location@',
            '@sublocation@',
            '@trainername@',
            '@trainerfirstname@',
            '@trainerlastname@',
            '@trainerid@',
            '@traineremail@',
            '@trainermobile@',
            '@startdate@',
            '@enddate@',
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

    public function filter($data, $sequence)
    {
        $variables = Model_Automations::get_sequence_variables($sequence);

        $this->filter_select = DB::select(
            array('schedules.id', 'scheduleid'),
            array('schedules.name', 'schedule'),
            DB::expr("DATE_FORMAT(schedules.start_date, '%d %M %Y') as startdate"),
            DB::expr("DATE_FORMAT(schedules.end_date, '%d %M %Y') as enddate"),
            array('courses.title', 'course'),
            DB::expr('CONCAT("http://' . $_SERVER['HTTP_HOST'] . '/course-detail/", schedules.name, "/?id=", schedules.course_id, "&schedule_id=", schedule_id) as courseurl'),
            'categories.category',
            'levels.level',
            array('subjects.name', 'subject'),
            array('schedules.trainer_id', 'trainerid'),
            DB::expr('CONCAT_WS(" ", trainers.first_name, trainers.last_name) as trainername'),
            array('trainers.first_name', 'trainerfirstname'),
            array('trainers.last_name', 'trainerlastname'),
            array('trainer_emails.value', 'traineremail'),
            DB::expr("CONCAT_WS('', trainer_mobiles.country_dial_code, trainer_mobiles.dial_code, trainer_mobiles.value) as trainermobile")
        )
            ->distinct('*')
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
               ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('schedules.course_id', '=', 'courses.id')
                ->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')
                    ->on('courses.category_id', '=', 'categories.id')
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                    ->on('courses.subject_id', '=', 'subjects.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'left')
                    ->on('schedules.trainer_id', '=', 'trainers.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'trainer_mobiles'), 'left')
                    ->on('trainers.notifications_group_id', '=', 'trainer_mobiles.group_id')
                    ->on('trainer_mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'trainer_emails'), 'left')
                    ->on('trainers.notifications_group_id', '=', 'trainer_emails.group_id')
                    ->on('trainer_emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Levels::LEVEL_TABLE, 'levels'), 'left')
                    ->on('courses.level_id', '=', 'levels.id')
                ->join(array('plugin_courses_waitlist', 'waitlist'), 'left')
                    ->on('schedules.id', '=', 'waitlist.schedule_id')
            ->where('schedules.delete', '=', 0)
            ->and_where('waitlist.deleted', '=', 0)
            ->group_by('schedules.id')
            ->group_by('waitlist.contact_id');

        if (isset($data['schedule_id'])) {
            $this->filter_select->and_where('schedules.id', (is_array($data['schedule_id']) ? 'in' : '='), $data['schedule_id']);
        }

        if (
            in_array('location', $variables) ||
            in_array('sublocation', $variables)
        ) {
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

        if (
            in_array('contactemail', $variables) ||
            in_array('contactmobile', $variables) ||
            in_array('contactname', $variables) ||
            in_array('contactid', $variables) ||
            in_array('contactaddress1', $variables) ||
            in_array('contactaddress2', $variables) ||
            in_array('contactaddress3', $variables) ||
            in_array('contactpostcode', $variables) ||
            in_array('contacttown', $variables) ||
            in_array('contactcounty', $variables)
        ) {
            $this->filter_select
                ->select_array(
                    array(
                        array('waitlist.contact_id', 'contactid'),

                        DB::expr('IF(contacts.id is null, waitlist.name, contacts.first_name) as contactfirstname'),
                        DB::expr('IF(contacts.id is null, waitlist.surname, contacts.last_name) as contactlastname'),
                        DB::expr('IF(contacts.id is null, CONCAT_WS(" ", waitlist.name, waitlist.surname), CONCAT_WS(" ", contacts.first_name, contacts.last_name)) as contactname'),
                        DB::expr('IF(contacts.id is null, waitlist.email, emails.value) as contactemail'),
                        DB::expr('IF(contacts.id is null, waitlist.phone, CONCAT_WS(\'\', mobiles.country_dial_code, mobiles.dial_code, mobiles.value)) as contactmobile'),
                        DB::expr('IF(contacts.id is null, waitlist.address, contact_addresses.address1) as contactaddress1'),
                        DB::expr('IF(contacts.id is null, "", contact_addresses.address2) as contactaddress2'),
                        DB::expr('IF(contacts.id is null, "", contact_addresses.address3) as contactaddress3'),
                        DB::expr('IF(contacts.id is null, "", contact_addresses.postcode) as contactpostcode'),
                        DB::expr('IF(contacts.id is null, "", contact_addresses.town) as contacttown'),
                        DB::expr('IF(contacts.id is null, "", contact_address_counties.name) as contactcounty')
                    )
                )
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                    ->on('waitlist.contact_id', '=', 'contacts.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')
                    ->on('contacts.notifications_group_id', '=', 'emails.group_id')
                    ->on('emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobiles'), 'left')
                    ->on('contacts.notifications_group_id', '=', 'mobiles.group_id')
                    ->on('mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Residence::ADDRESS_TABLE, 'contact_addresses'), 'left')
                    ->on('contacts.residence', '=', 'contact_addresses.address_id')
                ->join(array('engine_counties', 'contact_address_counties'), 'left')
                    ->on('contact_addresses.county', '=', 'contact_address_counties.id');
        }

        $select = $this->filter_select;
        $conditions_mode = $sequence['conditions_mode'];
        $conditions = $sequence['conditions'];
        if (empty($conditions)) {
            $records = $select->execute()->as_array();
            return $records;
        }

        $condition_function = 'and_where';
        $condition_open_function = 'and_where_open';
        $condition_close_function = 'and_where_close';
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
                    call_user_func(array($select, $condition_function), 'courses.type_id', $operator, $condition['val']);
                    break;
                case 'course_category_id':
                    call_user_func(array($select, $condition_function), 'courses.category_id', $operator, $condition['val']);
                    break;
                case 'course_subject_id':
                    call_user_func(array($select, $condition_function), 'courses.subject_id', $operator, $condition['val']);
                    break;
                case 'course_id':
                    call_user_func(array($select, $condition_function), 'schedules.course_id', $operator, $condition['val']);
                    break;
                case 'schedule_id':
                    call_user_func(array($select, $condition_function), 'schedules.id', $operator, $condition['val']);
                    break;
                case 'trainer_id':
                    call_user_func(array($select, $condition_function), 'schedules.trainer_id', $operator, $condition['val']);
                    break;
                case 'start_date_interval':
                    call_user_func(array($select, $condition_function . '_open'));
                    if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                        Model_Automations_Trigger::filter_date_interval_helper($select, 'schedules.start_date', $condition['execute'], $operator, $interval[2], $interval[1]);
                    }
                    call_user_func(array($select, $condition_function . '_close'));
                    break;
                case 'start_date':
                    call_user_func(array($select, $condition_function), DB::expr('cast(schedules.start_date as date)'), $operator, $condition['val']);
                    break;
                case 'end_date_interval':
                    call_user_func(array($select, $condition_function . '_open'));
                    if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                        Model_Automations_Trigger::filter_date_interval_helper($select, 'schedules.end_date', $condition['execute'], $operator, $interval[2], $interval[1]);
                    }
                    call_user_func(array($select, $condition_function . '_close'));
                    break;
                case 'end_date':
                    call_user_func(array($select, $condition_function), DB::expr('cast(schedules.end_date as date)'), $operator, $condition['val']);
                    break;
                case 'schedule_capacity':
                    $capacity_sub_query = DB::select('bookings.booking_id', DB::expr('count(*) as multiplier'))
                        ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                        ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'), 'left')
                        ->on('bookings.booking_id', '=', 'has_delegates.booking_id')
                        ->on('has_delegates.deleted', '=', 0)
                        ->group_by('bookings.booking_id');
                    $capacity_query = DB::select('schedules.id', DB::expr('floor((sum(std_count.multiplier) / schedules.max_capacity) * 100) as capacity'))
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
                    call_user_func(array($select, $condition_function), 'schedule_capacity.capacity', $operator, $condition['val']);
                    break;
                case 'application_status':
                    $select
                        ->join(array(Model_KES_Bookings::BOOKING_APPLICATIONS, 'applications'), 'inner')
                        ->on('bookings.booking_id', '=', 'applications.booking_id')
                        ->and_where('bookings.booking_id', 'is not', null);

                    if ('no_submit' == $condition['val']) {
                        $select->and_where('applications.status_id', '=', 1);
                    } else {
                        $select->and_where('applications.status_id', '<>', 1);
                    }
                    break;
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
        $records = $select->execute()->as_array();
        return $records;
    }
}
