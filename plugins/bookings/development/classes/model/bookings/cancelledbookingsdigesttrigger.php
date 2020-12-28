<?php

class Model_Bookings_Cancelledbookingsdigesttrigger extends Model_Automations_Trigger
{
    const NAME = 'Cancelled bookings digest';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('booking_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->initiator = Model_Automations_Trigger::INITIATOR_CRON;
        $this->multiple_results = true;

        $this->filters = array(
            array('field' => 'course_type_id', 'label' => 'Course Type', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_category_id', 'label' => 'Course Category', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_subject_id', 'label' => 'Course Subject', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_id', 'label' => 'Course', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'schedule_id', 'label' => 'Schedule', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'trainer_id', 'label' => 'Trainer', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'cancel_date_interval', 'label' => 'Cancelled Date(Relative)', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
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

    public function filter($data, $sequence)
    {
        $variables = Model_Automations::get_sequence_variables($sequence);

        $select = DB::select(
            array('bookings.booking_id', 'bookingid'),
            array('booking_status.title', 'status'),
            DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as leadbooker"),
            DB::expr("IFNULL(counties.name, parent_counties.name) as county"),
            array('transaction_types.type', 'bookingtype'),
            array('courses.title', 'course'),
            array('schedules.name', 'schedule'),
            DB::expr("DATE_FORMAT(schedules.start_date, '%d %M %Y') as startdate"),
            DB::expr("DATE_FORMAT(schedules.end_date, '%d %M %Y') as enddate"),
            array('transactions.id', 'transactionid'),
            array('transactions.updated', 'cancelleddate'),
            array('transactions.amount', 'transactionamount'),
            array('navapi_events.remote_event_no', 'navisioncode'),
            //DB::expr("GROUP_CONCAT(CONCAT_WS(' ',`delegates`.`first_name`, `delegates`.`last_name`)) AS cancelleddelegates")
            DB::expr("CONCAT_WS(' ',`delegates`.`first_name`, `delegates`.`last_name`) AS cancelleddelegate"),
            array('has_delegates.cancel_reason_code', 'cancelreason')
        )
            ->distinct('*')
            ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
                    ->on('bookings.booking_id', '=', 'has_schedules.booking_id')
                ->join(array(Model_KES_Bookings::BOOKING_STATUS_TABLE, 'booking_status'), 'left')
                    ->on('has_schedules.booking_status', '=', 'booking_status.status_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('schedules.id', '=', 'has_schedules.schedule_id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('schedules.course_id', '=', 'courses.id')
                ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'), 'left')
                    ->on('bookings.booking_id', '=', 'has_delegates.booking_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'delegates'), 'left')
                    ->on('has_delegates.contact_id', '=', 'delegates.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                    ->on('bookings.contact_id', '=', 'contacts.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')
                    ->on('schedules.location_id', '=', 'locations.id')
                ->join(array('plugin_courses_counties', 'counties'), 'left')
                    ->on('locations.county_id', '=', 'counties.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'parent_locations'), 'left')
                    ->on('locations.parent_id', '=', 'parent_locations.id')
                ->join(array('plugin_courses_counties', 'parent_counties'), 'left')
                    ->on('parent_locations.county_id', '=', 'parent_counties.id')
                ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                    ->on('bookings.booking_id', '=', 'transactions.booking_id')
                ->join(array(Model_Kes_Transaction::TABLE_HAS_SCHEDULES, 'transaction_schedules'), 'left')
                    ->on('transactions.id', '=', 'transaction_schedules.transaction_id')
                    ->on('has_schedules.schedule_id', '=', 'transaction_schedules.schedule_id')
                ->join(array(Model_Kes_Transaction::TYPE_TABLE, 'transaction_types'), 'inner')
                    ->on('transactions.type', '=', 'transaction_types.id')
                ->join(array(Model_NAVAPI::TABLE_EVENTS, 'navapi_events'), 'left')
                    ->on('schedules.id', '=', 'navapi_events.schedule_id')

            ->where('bookings.delete', '=', 0)
            ->and_where('has_schedules.deleted', '=', 0)
            ->and_where('transactions.deleted', '=', 0)
            ->and_where('transactions.type', 'in', array(3,4,5,6))
            ->and_where('has_delegates.cancelled', '=', 1);

        if (isset($data['booking_id'])) {
            $this->filter_select->and_where('bookings.booking_id', (is_array($data['booking_id']) ? 'in' : '='), $data['booking_id']);
        }

        $conditions_mode = $sequence['conditions_mode'];
        $conditions = $sequence['conditions'];

        if (!empty($conditions)) {
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
                        call_user_func(array($select, $condition_function), 'courses.type_id', $operator,
                            $condition['val']);
                        break;
                    case 'course_category_id':
                        call_user_func(array($select, $condition_function), 'courses.category_id', $operator,
                            $condition['val']);
                        break;
                    case 'course_subject_id':
                        call_user_func(array($select, $condition_function), 'courses.subject_id', $operator,
                            $condition['val']);
                        break;
                    case 'course_id':
                        call_user_func(array($select, $condition_function), 'schedules.course_id', $operator,
                            $condition['val']);
                        break;
                    case 'schedule_id':
                        call_user_func(array($select, $condition_function), 'schedules.id', $operator,
                            $condition['val']);
                        break;
                    case 'trainer_id':
                        call_user_func(array($select, $condition_function), 'schedules.trainer_id', $operator,
                            $condition['val']);
                        break;
                    case 'cancel_date_interval':
                        call_user_func(array($select, $condition_function . '_open'));
                        if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                            Model_Automations_Trigger::filter_date_interval_helper($select, 'transactions.updated', $condition['execute'], $operator, $interval[2], $interval[1]);
                        }
                        call_user_func(array($select, $condition_function . '_close'));
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
        }
        $select
            ->group_by('bookings.booking_id')
            ->group_by('transactions.id')
            ->group_by('has_delegates.contact_id');
        $records = $select->execute()->as_array();
        $result = array();
        if (count($records) > 0) {
            $tmpfile = tempnam(Kohana::$cache_dir, 'cancelledbookingsdigest');
            $fid = fopen($tmpfile, "c+");
            fputcsv($fid, array_keys($records[0]), ',', '"');
            foreach ($records as $record) {
                fputcsv($fid, $record, ',', '"');
            }
            fclose($fid);

            $result[] = array(
                'automation_digest' => true,
                'automation_attachments' => array(
                    array(
                        'path' => $tmpfile,
                        'name' => 'cancelledbookingsdigest-' . date('Ymd') . '.csv'
                    )
                )
            );
        }
        return $result;
    }
}
