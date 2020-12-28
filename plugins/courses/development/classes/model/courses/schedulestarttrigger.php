<?php

class Model_Courses_Schedulestarttrigger extends Model_Automations_Trigger
{
    const NAME = 'a Course Starts';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('schedule_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->initiator = Model_Automations_Trigger::INITIATOR_CRON;
        $this->multiple_results = true;

        $this->filters = array(
            array('field' => 'payment_method', 'label' => 'Booking Type', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_type_id', 'label' => 'Course Type', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_category_id', 'label' => 'Course Category', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_subject_id', 'label' => 'Course Subject', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_id', 'label' => 'Course', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'schedule_id', 'label' => 'Schedule', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'trainer_id', 'label' => 'Trainer', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'start_date_interval', 'label' => 'Start Date(Relative)', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'start_date', 'label' => 'Start Date', 'operators' => array('=' => '=', '>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'end_date_interval', 'label' => 'End Date(Relative)', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'end_date', 'label' => 'End Date', 'operators' => array('=' => '=', '>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'schedule_capacity', 'label' => 'Schedule Capacity(%)', 'operators' => array('>=' => 'MORE', '<=' => 'LESS')),
            array('field' => 'application_status', 'label' => 'Application Status', 'operators' => array('=' => 'IS'))
        );
        $this->generated_message_params = array(
            '@bookingid@',
            '@tuapplication@',
            '@leadbookername@',
            '@leadbookerfirstname@',
            '@leadbookerlastname@',
            '@leadbookerid@',
            '@leadbookeremail@',
            '@leadbookermobile@',
            '@leadbookeraddress1@',
            '@leadbookeraddress2@',
            '@leadbookeraddress3@',
            '@leadbookerpostcode@',
            '@leadbookertown@',
            '@leadbookercounty@',
            '@studentname@',
            '@studentfirstname@',
            '@studentlastname@',
            '@studentid@',
            '@studentemail@',
            '@studentmobile@',
            '@studentaddress1@',
            '@studentaddress2@',
            '@studentaddress3@',
            '@studentpostcode@',
            '@studenttown@',
            '@studentcounty@',
            '@parentname@',
            '@parentid@',
            '@parentemail@',
            '@parentmobile@',
            '@course@',
            '@category@',
            '@subject@',
            '@level@',
            '@scheduleid@',
            '@schedule@',
            '@location@',
            '@locationcounty@',
            '@address1@',
            '@address2@',
            '@address3@',
            '@sublocation@',
            '@trainername@',
            '@trainerfirstname@',
            '@trainerlastname@',
            '@trainerid@',
            '@traineremail@',
            '@trainermobile@',
            '@startdate@',
            '@enddate@',
            '@starttime@',
            '@endtime@',
            '@duration@',
            '@listalltimeslotdatesforschedule@',
            '@accreditedby@',
            '@feeperson@',
            '@feebooking@',
            '@dayspresent@',
            '@daysabsent@'
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

        $this->no_duplicate_email_tag = array('scheduleid');
        $first_timeslots = DB::select(
            'schedule_id',
            DB::expr('min(datetime_start) as min_starttime'),
            DB::expr('min(datetime_end) as min_endtime')
        )
            ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
            ->where('delete', '=', 0)
            ->group_by('schedule_id');

        $this->filter_select = DB::select(
            array('schedules.id', 'scheduleid'),
            array('schedules.name', 'schedule'),
            DB::expr("DATE_FORMAT(schedules.start_date, '%d %M %Y') as startdate"),
            DB::expr("DATE_FORMAT(schedules.end_date, '%d %M %Y') as enddate"),
            DB::expr("DATE_FORMAT(first_timeslots.min_starttime, '%H:%i') as starttime"),
            DB::expr("DATE_FORMAT(first_timeslots.min_endtime, '%H:%i') as endtime"),
            array('courses.title', 'course'),
            'categories.category',
            'levels.level',
            array('subjects.name', 'subject'),
            array('schedules.trainer_id', 'trainerid'),
            array('trainers.first_name', 'trainerfirstname'),
            array('trainers.last_name', 'trainerlastname'),
            DB::expr('CONCAT_WS(" ", trainers.first_name, trainers.last_name) as trainername'),
            array('trainer_emails.value', 'traineremail'),
            DB::expr("CONCAT_WS('', trainer_mobiles.country_dial_code, trainer_mobiles.dial_code, trainer_mobiles.value) as trainermobile")
        )
            ->distinct('*')
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                ->join(array($first_timeslots, 'first_timeslots'), 'left')
                    ->on('schedules.id', '=', 'first_timeslots.schedule_id')
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
            ->where('schedules.delete', '=', 0)
            ->group_by('schedules.id');

        if (isset($data['schedule_id'])) {
            $this->filter_select->and_where('schedules.id', (is_array($data['schedule_id']) ? 'in' : '='), $data['schedule_id']);
        }

        if (in_array('accreditedby', $variables)) {
            $accreditor_subquery = DB::select('has_providers.course_id',DB::expr("GROUP_CONCAT(providers.name) as providers"))
                ->from(array(Model_Providers::TABLE_PROVIDERS, 'providers'))
                    ->join(array(Model_Courses::TABLE_HAS_PROVIDERS, 'has_providers'), 'inner')
                        ->on('providers.id', '=', 'has_providers.provider_id')
                    ->join(array('plugin_courses_providers_types', 'provider_types'), 'inner')
                        ->on('providers.type_id', '=', 'provider_types.id')
                ->where('provider_types.type', '=', 'Accreditation Body')
                ->group_by('has_providers.course_id');
            $this->filter_select
                ->select_array(
                    array(array('accreditor_subquery.providers', 'accreditedby'))
                )
                ->join(array($accreditor_subquery, 'accreditor_subquery'), 'left')
                ->on('courses.id', '=', 'accreditor_subquery.course_id');
        }

        if (
            in_array('location', $variables) ||
            in_array('sublocation', $variables) ||
            in_array('locationcounty', $variables) ||
            in_array('address1', $variables) ||
            in_array('address2', $variables) ||
            in_array('address3', $variables)
        ) {
            $this->filter_select->select_array(
                array(
                    array("schedule_parent_locations.name", "location"),
                    array("schedule_locations.name", "sublocation"),
                    DB::expr("IFNULL(schedule_parent_counties.name, schedule_counties.name) as locationcounty"),
                    DB::expr("IFNULL(schedule_parent_locations.address1, schedule_locations.address1) as address1"),
                    DB::expr("IFNULL(schedule_parent_locations.address2, schedule_locations.address2) as address2"),
                    DB::expr("IFNULL(schedule_parent_locations.address3, schedule_locations.address3) as address3")
                )
            )->join(array(Model_Locations::TABLE_LOCATIONS, 'schedule_locations'), 'left')
                    ->on('schedules.location_id', '=', 'schedule_locations.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'schedule_parent_locations'), 'left')
                    ->on('schedule_locations.parent_id', '=', 'schedule_parent_locations.id')
                ->join(array('plugin_courses_counties', 'schedule_counties'), 'left')
                    ->on('schedule_locations.county_id', '=', 'schedule_counties.id')
                ->join(array('plugin_courses_counties', 'schedule_parent_counties'), 'left')
                    ->on('schedule_parent_locations.county_id', '=', 'schedule_parent_counties.id');
        }

        if (
            in_array('dayspresent', $variables) ||
            in_array('daysabsent', $variables) ||
            in_array('studentemail', $variables) ||
            in_array('studentmobile', $variables) ||
            in_array('studentname', $variables) ||
            in_array('studentid', $variables) ||
            in_array('bookingid', $variables) ||
            in_array('tuapplication', $variables) ||
            in_array('studentaddress1', $variables) ||
            in_array('studentaddress2', $variables) ||
            in_array('studentaddress3', $variables) ||
            in_array('studentpostcode', $variables) ||
            in_array('studenttown', $variables) ||
            in_array('studentcounty', $variables) ||
            in_array('leadbookerfirstname', $variables) ||
            in_array('leadbookerlastname', $variables) ||
            in_array('leadbookername', $variables) ||
            in_array('leadbookerid', $variables) ||
            in_array('leadbookeremail', $variables) ||
            in_array('leadbookermobile', $variables) ||
            in_array('leadbookeraddress1', $variables) ||
            in_array('leadbookeraddress2', $variables) ||
            in_array('leadbookeraddress3', $variables) ||
            in_array('leadbookerpostcode', $variables) ||
            in_array('leadbookertown', $variables) ||
            in_array('leadbookercounty', $variables) ||
            in_array('duration', $variables) ||
            in_array('listalltimeslotdatesforschedule', $variables) ||
            in_array('feeperson', $variables) ||
            in_array('feebooking', $variables) ||
            in_array('payment_method', array_column($sequence['conditions'], 'field')) ||
            in_array('application_status', array_column($sequence['conditions'], 'field'))
        ) {
            $this->no_duplicate_email_tag[] = 'studentid';
            $this->filter_select->group_by('bookings.booking_id');

            $duration_subquery = DB::select(
                'bookings.booking_id',
                DB::expr("GROUP_CONCAT(distinct DATE_FORMAT(timeslots.datetime_start, '%d %M %Y')) as  days"),
                DB::expr("count(distinct DATE_FORMAT(timeslots.datetime_start, '%d %M %Y')) as daycount")
            )
                ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                    ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                        ->on('bookings.booking_id', '=', 'items.booking_id')
                    ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                        ->on('items.period_id', '=', 'timeslots.id')
                ->where('bookings.delete', '=', 0)
                ->and_where('items.delete', '=', 0)
                ->and_where('timeslots.delete', '=', 0)
                ->and_where('items.booking_status', '<>', 3)
                ->group_by('bookings.booking_id');

            $this->filter_select
                ->select_array(
                    array(
                        array('bookings.booking_id', 'bookingid')
                    )
            )->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
                    ->on('schedules.id', '=', 'has_schedules.schedule_id')
                    ->on('has_schedules.deleted', '=', DB::expr(0))
                    ->on('has_schedules.booking_status', '<>', DB::expr(3))
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                    ->on('has_schedules.booking_id', '=', 'bookings.booking_id')
                    ->on('bookings.delete', '=', DB::expr(0));

            if (in_array('leadbookerfirstname', $variables) ||
                in_array('leadbookerlastname', $variables) ||
                in_array('leadbookername', $variables) ||
                in_array('leadbookerid', $variables) ||
                in_array('leadbookeremail', $variables) ||
                in_array('leadbookermobile', $variables) ||
                in_array('leadbookeraddress1', $variables) ||
                in_array('leadbookeraddress2', $variables) ||
                in_array('leadbookeraddress3', $variables) ||
                in_array('leadbookerpostcode', $variables) ||
                in_array('leadbookertown', $variables) ||
                in_array('leadbookercounty', $variables)) {
                $this->filter_select
                    ->select_array(
                        array(
                            DB::expr('CONCAT_WS(" ", lead_bookers.first_name, lead_bookers.last_name) as leadbookername'),
                            array('lead_bookers.first_name', 'leadbookerfirstname'),
                            array('lead_bookers.last_name', 'leadbookerlastname'),
                            array('bookings.contact_id', 'leadbookerid'),
                            array('lead_booker_emails.value', 'leadbookeremail'),
                            DB::expr("CONCAT_WS('', lead_booker_mobiles.country_dial_code, lead_booker_mobiles.dial_code, lead_booker_mobiles.value) as leadbookermobile"),
                            array('leadbooker_addresses.address1', 'leadbookeraddress1'),
                            array('leadbooker_addresses.address2', 'leadbookeraddress2'),
                            array('leadbooker_addresses.address3', 'leadbookeraddress3'),
                            array('leadbooker_addresses.postcode', 'leadbookerpostcode'),
                            array('leadbooker_addresses.town', 'leadbookertown'),
                            array('leadbooker_address_counties.name', 'leadbookercounty')
                        )
                    )->join(array(Model_Contacts3::CONTACTS_TABLE, 'lead_bookers'), 'inner')
                        ->on('bookings.contact_id', '=', 'lead_bookers.id')
                    ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'lead_booker_emails'), 'left')
                        ->on('lead_bookers.notifications_group_id', '=', 'lead_booker_emails.group_id')
                        ->on('lead_booker_emails.notification_id', '=', DB::expr(1))
                    ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'lead_booker_mobiles'), 'left')
                        ->on('lead_bookers.notifications_group_id', '=', 'lead_booker_mobiles.group_id')
                        ->on('lead_booker_mobiles.notification_id', '=', DB::expr(2))
                    ->join(array(Model_Residence::ADDRESS_TABLE, 'leadbooker_addresses'), 'left')
                        ->on('lead_bookers.residence', '=', 'leadbooker_addresses.address_id')
                    ->join(array('engine_counties', 'leadbooker_address_counties'), 'left')
                        ->on('leadbooker_addresses.county', '=', 'leadbooker_address_counties.id');
            }
            if (in_array('dayspresent', $variables) ||
                in_array('daysabsent', $variables) ||
                in_array('studentemail', $variables) ||
                in_array('studentmobile', $variables) ||
                in_array('studentname', $variables) ||
                in_array('studentid', $variables) ||
                in_array('tuapplication', $variables) ||
                in_array('studentaddress1', $variables) ||
                in_array('studentaddress2', $variables) ||
                in_array('studentaddress3', $variables) ||
                in_array('studentpostcode', $variables) ||
                in_array('studenttown', $variables) ||
                in_array('studentcounty', $variables)) {
                $this->filter_select
                    ->select_array(
                        array(
                            DB::expr('students.first_name as studentfirstname'),
                            DB::expr('students.last_name as studentlastname'),
                            DB::expr('CONCAT_WS(" ", students.first_name, students.last_name) as studentname'),
                            DB::expr('students.id as studentid'),
                            DB::expr('emails.value as studentemail'),
                            DB::expr('CONCAT_WS(\'\', mobiles.country_dial_code, mobiles.dial_code, mobiles.value) as studentmobile'),
                            DB::expr('student_addresses.address1 as studentaddress1'),
                            DB::expr('student_addresses.address2 as studentaddress2'),
                            DB::expr('student_addresses.address3 as studentaddress3'),
                            DB::expr('student_addresses.postcode as studentpostcode'),
                            DB::expr('student_addresses.town as studenttown'),
                            DB::expr('student_address_counties.name as studentcounty'),

                            array('duration_subquery.daycount', 'duration'),
                            array('duration_subquery.days', 'listalltimeslotdatesforschedule'),
                        )
                    )
                    ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_students'), 'inner')
                        ->on('has_schedules.booking_id', '=', 'has_students.booking_id')
                        ->on('has_students.deleted', '=', DB::expr(0))
                        ->on('has_students.cancelled', '=', DB::expr(0))
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                        ->on('has_students.contact_id', '=', 'students.id')
                    ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')
                        ->on('students.notifications_group_id', '=', 'emails.group_id')
                        ->on('emails.notification_id', '=', DB::expr(1))
                    ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobiles'), 'left')
                        ->on('students.notifications_group_id', '=', 'mobiles.group_id')
                        ->on('mobiles.notification_id', '=', DB::expr(2))
                    ->join(array(Model_Residence::ADDRESS_TABLE, 'student_addresses'), 'left')
                        ->on('students.residence', '=', 'student_addresses.address_id')
                    ->join(array('engine_counties', 'student_address_counties'), 'left')
                        ->on('student_addresses.county', '=', 'student_address_counties.id')
                    ->join(array($duration_subquery, 'duration_subquery'), 'left')
                        ->on('bookings.booking_id', '=', 'duration_subquery.booking_id')
                    ->group_by('has_students.contact_id');
            }

            if (
                in_array('dayspresent', $variables) ||
                in_array('daysabsent', $variables)
            ) {
                $absent_days_subquery =  DB::select(
                    'rollcall.delegate_id', 'timeslots.schedule_id',
                    DB::expr("date_format(timeslots.datetime_start, '%Y-%m-%d') as date")
                )
                    ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'rollcall'))
                            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                        ->on('rollcall.timeslot_id', '=', 'timeslots.id')
                    ->where('rollcall.delete', '=', 0)
                    ->and_where('rollcall.attendance_status', '=', 'Absent')
                    ->group_by('rollcall.delegate_id')
                    ->group_by('timeslots.schedule_id')
                    ->group_by('date');
                $absent_days_count_subquery = DB::select('delegate_id', 'schedule_id', DB::expr("IFNULL(count(*),0) as daysabsent"))
                    ->from(array($absent_days_subquery, 'absent_days_subquery'))
                    ->group_by('delegate_id')
                    ->group_by('schedule_id');
                $present_days_subquery =  DB::select(
                    'rollcall.delegate_id', 'timeslots.schedule_id',
                    DB::expr("date_format(timeslots.datetime_start, '%Y-%m-%d') as date")
                )
                    ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'rollcall'))
                        ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                            ->on('rollcall.timeslot_id', '=', 'timeslots.id')
                    ->where('rollcall.delete', '=', 0)
                    ->and_where('rollcall.attendance_status', '<>', 'Absent')
                    ->and_where('rollcall.attendance_status', 'is not', null)
                    ->group_by('rollcall.delegate_id')
                    ->group_by('timeslots.schedule_id')
                    ->group_by('date');
                $present_days_count_subquery = DB::select('delegate_id', 'schedule_id', DB::expr("IFNULL(count(*),0) as dayspresent"))
                    ->from(array($present_days_subquery, 'present_days_subquery'))
                    ->group_by('delegate_id')
                    ->group_by('schedule_id');
                $this->filter_select->select_array(
                    array(
                        DB::expr('ifnull(present_days.dayspresent, 0) as dayspresent'),
                        DB::expr('ifnull(absent_days.daysabsent, 0) as daysabsent')
                    )
                )->join(array($absent_days_count_subquery, 'absent_days'), 'left')
                        ->on('has_students.contact_id', '=', 'absent_days.delegate_id')
                        ->on('has_schedules.schedule_id', '=', 'absent_days.schedule_id')
                    ->join(array($present_days_count_subquery, 'present_days'), 'left')
                        ->on('has_students.contact_id', '=', 'present_days.delegate_id')
                        ->on('has_schedules.schedule_id', '=', 'present_days.schedule_id');
            }

            if (in_array('feeperson', $variables) || in_array('feebooking', $variables)) {
                $fee_subquery = DB::select(
                    'bookings.booking_id',
                    DB::expr("round(bookings.amount / count(*), 2) as feeperson"),
                    array('bookings.amount', 'feebooking')
                )
                    ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                        ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'), 'left')
                           ->on('bookings.booking_id', '=', 'has_delegates.booking_id')
                            ->on('has_delegates.deleted', '=', DB::expr(0))
                            ->on('has_delegates.cancelled', '=', DB::expr(0))
                    ->group_by('bookings.booking_id');
                $this->filter_select
                    ->select_array(
                        array(
                            array('fee_subquery.feeperson', 'feeperson'),
                            array('bookings.amount', 'feebooking')
                        )
                    )
                        ->join(array($fee_subquery, 'fee_subquery'), 'left')
                            ->on('bookings.booking_id', '=', 'fee_subquery.booking_id');
            }
            if (
                in_array('parentemail', $variables) ||
                in_array('parentmobile', $variables) ||
                in_array('parentname', $variables) ||
                in_array('parentid', $variables)
            ) {
                $this->filter_select
                    ->select_array(
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

        $select = $this->filter_select;
        $conditions_mode = $sequence['conditions_mode'];
        $conditions = $sequence['conditions'];
        if (empty($conditions)) {
            $records = $select->execute()->as_array();
            if (in_array('tuapplication', $variables)) {
                $form_page    = new Model_Page(Settings::instance()->get('accreditation_application_page'));
                foreach ($records as $i => $record) {
                    $records[$i]['tuapplication'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $form_page->name_tag . '?booking_id=' . $record['bookingid'] . '&contact_id=' . $record['studentid'];
                }
            }
            return $records;
        }


        if (isset($data['schedule_id'])) {
            $select->and_where('schedules.id', (is_array($data['schedule_id']) ? 'in' : '='), $data['schedule_id']);
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
                case 'payment_method':
                    call_user_func(array($select, $condition_function), 'bookings.payment_method', $operator, $condition['val']);
                    break;
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
                    if (preg_match('/(\d+)\s*(min|hour|day|week|month)/', $condition['val'], $interval)) {
                        call_user_func(array($select, $condition_function . '_open'));
                        Model_Automations_Trigger::filter_date_interval_helper($select, 'schedules.start_date', $condition['execute'], $operator, $interval[2], $interval[1]);
                        call_user_func(array($select, $condition_function . '_close'));
                    }
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
                    call_user_func(array($select, $condition_function), DB::expr('cast(schedules.start_date as date)'), $operator, $condition['val']);
                    break;
                case 'schedule_capacity':
                    $capacity_sub_query = DB::select('bookings.booking_id', DB::expr('count(*) as multiplier'))
                        ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                            ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'), 'left')
                                ->on('bookings.booking_id', '=', 'has_delegates.booking_id')
                                ->on('has_delegates.deleted', '=', DB::expr(0))
                                ->on('has_delegates.cancelled', '=', DB::expr(0))
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
                    $select->select_array(
                        array(
                            'capacity'
                        )
                    );
                    call_user_func(array($select, $condition_function), 'schedule_capacity.capacity', $operator, $condition['val']);
                    break;
                case 'application_status':
                    $select
                        ->join(array(Model_KES_Bookings::BOOKING_APPLICATIONS, 'applications'), 'inner')
                            ->on('bookings.booking_id', '=', 'applications.booking_id')
                            ->on('applications.delegate_id', '=', 'students.id')
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
        if (in_array('tuapplication', $variables)) {
            $form_page    = new Model_Page(Settings::instance()->get('accreditation_application_page'));
            foreach ($records as $i => $record) {
                $records[$i]['tuapplication'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $form_page->name_tag . '?booking_id=' . $record['bookingid'] . '&contact_id=' . $record['studentid'];
            }
        }
        return $records;
    }
}