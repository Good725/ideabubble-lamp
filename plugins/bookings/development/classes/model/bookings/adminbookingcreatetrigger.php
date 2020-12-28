<?php

class Model_Bookings_Adminbookingcreatetrigger extends Model_Automations_Trigger
{
    const NAME = 'a back-office Booking occurs';
    protected $joined = array();
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('booking_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;

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
            array('field' => 'schedule_capacity', 'label' => 'Schedule Capacity (%)', 'operators' => array('>=' => 'MORE', '<=' => 'LESS')),
            array('field' => 'application_status', 'label' => 'Application Status', 'operators' => array('=' => 'IS'))
        );
        $this->generated_message_params = array(
            '@bookingid@',
            '@tuapplication@',
            '@leadbookerfirstname@',
            '@leadbookerlastname@',
            '@leadbookername@',
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
            '@courseid@',
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
            '@starttime@',
            '@enddate@',
            '@endtime@',
            '@duration@',
            '@listalltimeslotdatesforschedule@',
            '@discountvalue@',
			'@discounttitle@',
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

    protected function _join_applications()
    {
        $joined = in_array('applications', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'applications';
            $this->_join_schedule();
            $this->filter_select->select_array(
                array(
                    array('applications.id', 'applicationid'),
                )
            )
                ->join(array(Model_KES_Bookings::BOOKING_APPLICATIONS, 'applications'), 'left')
                    ->on('bookings.booking_id', '=', 'applications.booking_id');
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
            $first_timeslots = DB::select(
                'schedule_id',
                DB::expr('min(datetime_start) as min_starttime'),
                DB::expr('min(datetime_end) as min_endtime')
            )
                ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                ->where('delete', '=', 0)
                ->group_by('schedule_id');
            $this->joined[] = 'schedule';
            $this->_join_has_schedule();
            $this->filter_select->select_array(
                array(
                    array('schedules.id', 'scheduleid'),
                    array('schedules.name', 'schedule'),
                    DB::expr("DATE_FORMAT(schedules.start_date, '%d %M %Y') as startdate"),
                    DB::expr("DATE_FORMAT(schedules.end_date, '%d %M %Y') as enddate"),
                    DB::expr("DATE_FORMAT(first_timeslots.min_starttime, '%H:%i') as starttime"),
                    DB::expr("DATE_FORMAT(first_timeslots.min_endtime, '%H:%i') as endtime")
                )
            )->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                ->on('has_schedules.schedule_id', '=', 'schedules.id')
            ->join(array($first_timeslots, 'first_timeslots'), 'left')
                ->on('schedules.id', '=', 'first_timeslots.schedule_id');
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
                        array('trainers.first_name', 'trainerfirstname'),
                        array('trainers.last_name', 'trainerlastname'),
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

    protected function _join_leadbooker()
    {
        $joined = in_array('leadbooker', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'leadbooker';
            $this->_join_has_schedule();
            $this->filter_select->select_array(
                array(
                    DB::expr('CONCAT_WS(" ", lead_bookers.first_name, lead_bookers.last_name) as leadbookername'),
                    array('lead_bookers.first_name', 'leadbookerfirstname'),
                    array('lead_bookers.last_name', 'leadbookerlastname'),
                    array('bookings.contact_id', 'leadbookerid'),
                    array('lead_booker_emails.value', 'leadbookeremail'),
                    DB::expr("CONCAT_WS('', lead_booker_mobiles.country_dial_code, lead_booker_mobiles.dial_code, lead_booker_mobiles.value) as leadbookermobile"),
                    array('lead_booker_addresses.address1', 'leadbookeraddress1'),
                    array('lead_booker_addresses.address2', 'leadbookeraddress2'),
                    array('lead_booker_addresses.address3', 'leadbookeraddress3'),
                    array('lead_booker_addresses.postcode', 'leadbookerpostcode'),
                    array('lead_booker_addresses.town', 'leadbookertown'),
                    array('lead_booker_address_counties.name', 'leadbookercounty'),
                )
            )
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'lead_bookers'), 'inner')
                    ->on('bookings.contact_id', '=', 'lead_bookers.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'lead_booker_emails'), 'left')
                    ->on('lead_bookers.notifications_group_id', '=', 'lead_booker_emails.group_id')
                    ->on('lead_booker_emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'lead_booker_mobiles'), 'left')
                    ->on('lead_bookers.notifications_group_id', '=', 'lead_booker_mobiles.group_id')
                    ->on('lead_booker_mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Residence::ADDRESS_TABLE, 'lead_booker_addresses'), 'left')
                    ->on('lead_bookers.residence', '=', 'lead_booker_addresses.address_id')
                ->join(array('engine_counties', 'lead_booker_address_counties'), 'left')
                    ->on('lead_booker_addresses.county', '=', 'lead_booker_address_counties.id');
        }
    }

    protected function _join_rollcall()
    {
        $joined = in_array('rollcall', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'rollcall';
            $this->_join_delegate();
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
    }

    protected function _join_delegate()
    {
        $joined = in_array('delegate', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'delegate';
            $this->_join_leadbooker();
            $this->_join_has_schedule();
            $this->filter_select->select_array(
                array(
                    DB::expr('IF(students.id is null, lead_bookers.first_name, students.first_name) as studentfirstname'),
                    DB::expr('IF(students.id is null, lead_bookers.last_name, students.last_name) as studentlastname'),
                    DB::expr('IF(students.id is null, CONCAT_WS(" ", lead_bookers.first_name, lead_bookers.last_name), CONCAT_WS(" ", students.first_name, students.last_name)) as studentname'),
                    DB::expr('IF(students.id is null, lead_bookers.id, students.id) as studentid'),
                    DB::expr('IF(students.id is null, lead_booker_emails.value, emails.value) as studentemail'),
                    DB::expr('IF(students.id is null, CONCAT_WS(\'\', lead_booker_mobiles.country_dial_code, lead_booker_mobiles.dial_code, lead_booker_mobiles.value), CONCAT_WS(\'\', mobiles.country_dial_code, mobiles.dial_code, mobiles.value)) as studentmobile')
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
                    array('parent_mobiles.value', 'parentmobile'),
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

    protected function _join_duration()
    {
        $joined = in_array('duration', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'duration';
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

            $this->filter_select->select_array(
                array(
                    array('duration_subquery.days', 'listalltimeslotdatesforschedule'),
                    array('duration_subquery.daycount', 'duration'),
                )
            )
                ->join(array($duration_subquery, 'duration_subquery'), 'left')
                    ->on('bookings.booking_id', '=', 'duration_subquery.booking_id');
        }
    }

    protected function _join_discount()
    {
        $joined = in_array('discount', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'discount';
            $discount_subquery = DB::select(
                'bookings.booking_id',
                DB::expr("SUM(booking_discounts.amount) as discount"),
                DB::expr("GROUP_CONCAT(discounts.title) as discounttitle")
            )
                ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                ->join(array(Model_KES_Bookings::DISCOUNTS, 'booking_discounts'), 'inner')
                    ->on('bookings.booking_id', '=', 'booking_discounts.booking_id')
                ->join(array(Model_KES_Discount::DISCOUNTS_TABLE, 'discounts'), 'inner')
                    ->on('booking_discounts.discount_id', '=', 'discounts.id')
                ->where('bookings.delete', '=', 0)
                ->and_where('booking_discounts.amount', '>', 0)
                ->group_by('bookings.booking_id');

            $this->filter_select->select_array(
                array(
                    array('discount_subquery.discount', 'discountvalue'),
                    array('discount_subquery.discounttitle', 'discounttitle'),
                )
            )
                ->join(array($discount_subquery, 'discount_subquery'), 'left')
                ->on('bookings.booking_id', '=', 'discount_subquery.booking_id');
        }
    }

    protected function _join_accreditedby()
    {
        $joined = in_array('accreditedby', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'accreditedby';
            $this->_join_courses();
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
    }

    protected function _join_fee()
    {
        $joined = in_array('fee', $this->joined);
        if ($joined == false) {
            $this->joined[] = 'fee';
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
                        'fee_subquery.feeperson', 'fee_subquery.feebooking'
                    )
                )
                ->join(array($fee_subquery, 'fee_subquery'), 'left')
                ->on('bookings.booking_id', '=', 'fee_subquery.booking_id');
        }
    }

    public function filter($data, $sequence)
    {
        $this->joined = array(); // reset joins from previos calls
        $variables = Model_Automations::get_sequence_variables($sequence);

        $this->no_duplicate_email_tag = array('bookingid');

        $this->filter_select = DB::select(
            array('bookings.booking_id', 'bookingid')
        )
            ->distinct('*')
            ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
            ->where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', '<>', 3)
            ->group_by('bookings.booking_id');

        $select = $this->filter_select;
        if (isset($data['booking_id'])) {
            $select->and_where('bookings.booking_id', (is_array($data['booking_id']) ? 'in' : '='), $data['booking_id']);
        }
        if (isset($data['application_id'])) {
            $this->_join_applications();
            $select->and_where('applications.id', (is_array($data['application_id']) ? 'in' : '='), $data['application_id']);
        }
        if (isset($data['delegate_id'])) {
            $this->_join_delegate();
            $select->and_where('has_students.contact_id', (is_array($data['delegate_id']) ? 'in' : '='), $data['delegate_id']);
            if (isset($data['application_id'])) {
                $select->and_where('applications.delegate_id', (is_array($data['delegate_id']) ? 'in' : '='), $data['delegate_id']);
            }
        }

        if (in_array('discountvalue', $variables) || in_array('discounttitle', $variables)) {
            $this->_join_discount();
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

        if (in_array('accreditedby', $variables)) {
            $this->_join_accreditedby();
        }

        if (in_array('feeperson', $variables) || in_array('feebooking', $variables)) {
            $this->_join_fee();
        }

        if (
            in_array('scheduleid', $variables) ||
            in_array('schedule', $variables) ||
            in_array('startdate', $variables) ||
            in_array('enddate', $variables) ||
            in_array('starttime', $variables) ||
            in_array('endtime', $variables) ||
            in_array('location', $variables) ||
            in_array('sublocation', $variables)
        ) {
            $this->_join_schedule();
        }

        if (
            in_array('duration', $variables) ||
            in_array('listalltimeslotdatesforschedule', $variables)
        ) {
            $this->_join_duration();
        }

        if (
            in_array('dayspresent', $variables) ||
            in_array('daysabsent', $variables)
        ) {
            $this->_join_rollcall();
        }

        if (
            in_array('location', $variables) ||
            in_array('sublocation', $variables) ||
            in_array('locationcounty', $variables) ||
            in_array('address1', $variables) ||
            in_array('address2', $variables) ||
            in_array('address3', $variables)
        ) {
            $this->_join_schedule_location();
        }

        if (
            in_array('traineremail', $variables) ||
            in_array('trainermobile', $variables) ||
            in_array('trainername', $variables) ||
            in_array('trainerfirstname', $variables) ||
            in_array('trainerlastname', $variables) ||
            in_array('trainerid', $variables)
        ) {
            $this->_join_trainer();
        }

        if (
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
            in_array('leadbookercounty', $variables)
        ) {
            $this->_join_leadbooker();
        }

        if (
            in_array('dayspresent', $variables) ||
            in_array('daysabsent', $variables) ||
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
            $this->no_duplicate_email_tag[] = 'studentid';
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
        if (empty($conditions)) {
            $bookings = $select->execute()->as_array();
            if (in_array('tuapplication', $variables)) {
                $form_page    = new Model_Page(Settings::instance()->get('accreditation_application_page'));
                foreach ($bookings as $i => $booking) {
                    $bookings[$i]['tuapplication'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $form_page->name_tag . '?booking_id=' . $booking['bookingid'] . '&contact_id=' . $booking['studentid'];
                }
            }
            return $bookings;
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
                    $this->_join_courses();
                    call_user_func(array($select, $condition_function), 'courses.type_id', $operator, $condition['val']);
                    break;
                case 'course_category_id':
                    $this->_join_courses();
                    call_user_func(array($select, $condition_function), 'courses.category_id', $operator, $condition['val']);
                    break;
                case 'course_subject_id':
                    $this->_join_courses();
                    call_user_func(array($select, $condition_function), 'courses.subject_id', $operator, $condition['val']);
                    break;
                case 'course_id':
                    $this->_join_courses();
                    call_user_func(array($select, $condition_function), 'schedules.course_id', $operator, $condition['val']);
                    break;
                case 'schedule_id':
                    $this->_join_schedule();
                    call_user_func(array($select, $condition_function), 'schedules.id', $operator, $condition['val']);
                    break;
                case 'trainer_id':
                    $this->_join_trainer();
                    call_user_func(array($select, $condition_function), 'schedules.trainer_id', $operator, $condition['val']);
                    break;
                case 'start_date_interval':
                    $this->_join_schedule();
                    call_user_func(array($select, $condition_function . '_open'));
                    if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                        Model_Automations_Trigger::filter_date_interval_helper($select, 'schedules.start_date', $condition['execute'], $operator, $interval[2], $interval[1]);
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
                        Model_Automations_Trigger::filter_date_interval_helper($select, 'schedules.end_date', $condition['execute'], $operator, $interval[2], $interval[1]);
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
                                ->on('has_delegates.deleted', '=', DB::expr(0))
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
                        ->join(array(Model_KES_Bookings::BOOKING_APPLICATIONS, 'applications'), 'inner')
                            ->on('bookings.booking_id', '=', 'applications.booking_id')
                            ->on('applications.delegate_id', '=', 'students.id');

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
        $bookings = $select->execute()->as_array();
        if (in_array('tuapplication', $variables)) {
            $form_page    = new Model_Page(Settings::instance()->get('accreditation_application_page'));
            foreach ($bookings as $i => $booking) {
                $bookings[$i]['tuapplication'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $form_page->name_tag . '?booking_id=' . $booking['bookingid'] . '&contact_id=' . $booking['studentid'];
            }
        }
        return $bookings;
    }
}
