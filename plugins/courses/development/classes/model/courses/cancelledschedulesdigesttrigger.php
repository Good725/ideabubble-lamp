<?php

class Model_Courses_Cancelledschedulesdigesttrigger extends Model_Automations_Trigger
{
    const NAME = 'Cancelled courses digest';
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
            array('categories.category', 'category'),
            array('course_types.type', 'type'),
            array('courses.title', 'course'),
            array('schedules.name', 'schedule'),
            DB::expr("IFNULL(counties.name, parent_counties.name) as county"),
            DB::expr("SUM(IF(has_schedules.id IS NULL, 0, 1)) as bookingcount"),
            DB::expr("IFNULL(schedules.fee_amount, 0) as fee"),
            DB::expr("SUM(IF(has_schedules.id IS NULL, 0, 1)) * IFNULL(schedules.fee_amount, 0) as totalincomelost"),
            array('navapi_events.remote_event_no', 'navisioneventcode'),
            DB::expr("DATE_FORMAT(schedules.start_date, '%d %M %Y') as startdate"),
            DB::expr("DATE_FORMAT(schedules.end_date, '%d %M %Y') as enddate"),
            array('schedules.date_modified', 'cancelleddate')
        )
            ->distinct('*')
            ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'left')
                    ->on('schedules.id', '=', 'has_schedules.schedule_id')
                    ->on('has_schedules.deleted', '=', DB::expr(0))
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('schedules.course_id', '=', 'courses.id')
                ->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')
                    ->on('courses.category_id', '=', 'categories.id')
                ->join(array('plugin_courses_types', 'course_types'), 'left')
                    ->on('courses.type_id', '=', 'course_types.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')
                    ->on('schedules.location_id', '=', 'locations.id')
                ->join(array('plugin_courses_counties', 'counties'), 'left')
                    ->on('locations.county_id', '=', 'counties.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'parent_locations'), 'left')
                    ->on('locations.parent_id', '=', 'parent_locations.id')
                ->join(array('plugin_courses_counties', 'parent_counties'), 'left')
                    ->on('parent_locations.county_id', '=', 'parent_counties.id')
                ->join(array(Model_NAVAPI::TABLE_EVENTS, 'navapi_events'), 'left')
                    ->on('schedules.id', '=', 'navapi_events.schedule_id')

            ->where('schedules.delete', '=', 0)
            ->and_where('schedules.schedule_status', '=', Model_Schedules::CANCELLED);

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
                            Model_Automations_Trigger::filter_date_interval_helper($select, 'schedules.date_modified', $condition['execute'], $operator, $interval[2], $interval[1]);
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
        $select->group_by('schedules.id');
        $records = $select->execute()->as_array();
        $result = array();
        if (count($records) > 0) {
            $tmpfile = tempnam(Kohana::$cache_dir, 'cancelledschedulesdigest');
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
                        'name' => 'cancelledschedulesdigest-' . date('Ymd') . '.csv'
                    )
                )
            );
        }
        return $result;
    }
}
