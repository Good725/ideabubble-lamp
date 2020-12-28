<?php

class Model_Bookings_Waitlistdigesttrigger extends Model_Automations_Trigger
{
    const NAME = 'Waitlist digest';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('schedule_id');
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
            array('field' => 'schedule_capacity', 'label' => 'Schedule Capacity(%)', 'operators' => array('>=' => 'MORE', '<=' => 'LESS')),
            array('field' => 'submit_date_interval', 'label' => 'Submit Date(Relative)', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
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

        $this->filter_select = DB::select(
            array('schedules.id', 'scheduleid'),
            array('schedules.name', 'schedule'),
            array('courses.title', 'course')
        )
            ->distinct('*')
            ->from(array('plugin_courses_waitlist', 'waitlist'))
               ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                    ->on('schedules.id', '=', 'waitlist.schedule_id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('schedules.course_id', '=', 'courses.id')
            ->where('waitlist.deleted', '=', 0);

        if (isset($data['schedule_id'])) {
            $this->filter_select->and_where('schedules.id', (is_array($data['schedule_id']) ? 'in' : '='), $data['schedule_id']);
        }

        $this->filter_select
            ->select_array(
                array(
                    array('waitlist.contact_id', 'contactid'),
                    DB::expr("CONCAT_WS(' ', waitlist.name, waitlist.surname) as name"),
                    DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as organization"),
                    DB::expr('waitlist.email as email')
                )
            )
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                ->on('waitlist.contact_id', '=', 'contacts.id');


        $select = $this->filter_select;
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
                    case 'submit_date_interval':
                        call_user_func(array($select, $condition_function . '_open'));
                        if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                            Model_Automations_Trigger::filter_date_interval_helper($select, 'waitlist.date_modified',
                                $condition['execute'], $operator, $interval[2], $interval[1]);
                        }
                        call_user_func(array($select, $condition_function . '_close'));
                        break;
                    case 'schedule_capacity':
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
        $records = $select->execute()->as_array();
        $result = array();
        if (count($records) > 0) {
            $tmpfile = tempnam(Kohana::$cache_dir, 'waitlistdigest');
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
                        'name' => 'waitlistdigest-' . date('Ymd') . '.csv'
                    )
                )
            );
        }
        return $result;
    }
}
