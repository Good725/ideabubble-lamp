<?php

class Model_Bookings_Rollcalldigesttrigger extends Model_Automations_Trigger
{
    const NAME = 'Rollcall digest';
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
            array('field' => 'start_date_interval', 'label' => 'Start Date(Relative)', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<'))
        );
        $this->generated_message_params = array(
            '@trainerid@',
            '@traineremail@',
            '@trainerfirstname@',
            '@trainerlastname@',
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
        $schedule_trainer_ids = DB::select('trainer_id')
            ->distinct('trainer_id')
            ->from(Model_Schedules::TABLE_SCHEDULES)
            ->where('delete', '=', 0)
            ->execute()
            ->as_array('trainer_id');
        $timeslot_trainer_ids = DB::select('trainer_id')
            ->distinct('trainer_id')
            ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
            ->where('delete', '=', 0)
            ->execute()
            ->as_array('trainer_id');
        $trainer_ids = array_merge(array_keys($schedule_trainer_ids), array_keys($timeslot_trainer_ids));
        $trainer_ids = array_unique($trainer_ids);

        $result = array();
        foreach ($trainer_ids as $trainer_id) {
            $trainer = DB::select(
                array('trainers.first_name', 'trainerfirstname'),
                array('trainers.last_name', 'trainerlastname'),
                array('emails.value', 'traineremail')
            )
                ->from(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'))
                    ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')
                        ->on('trainers.notifications_group_id', '=', 'emails.group_id')
                        ->on('emails.notification_id', '=', DB::expr(1))
                ->where('trainers.id', '=', $trainer_id)
                ->execute()
                ->current();
            $select = DB::select(
                array('timeslots.datetime_start', 'timeslotstartdate'),
                array('timeslots.datetime_end', 'timeslotenddate'),
                DB::expr('DATE_FORMAT(timeslots.datetime_start, "%W") as day'),
                'categories.category',
                array('courses.title', 'course'),
                array('schedules.name', 'schedule'),
                array("schedule_parent_locations.name", "location"),
                array("schedule_locations.name", "sublocation"),
                DB::expr("CONCAT_WS(' ', delegates.first_name, delegates.last_name) as delegatename")
            )
                ->from(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'))
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('timeslots.schedule_id', '=', 'schedules.id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                        ->on('schedules.course_id', '=', 'courses.id')
                    ->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')
                        ->on('courses.category_id', '=', 'categories.id')
                    ->join(array(Model_Locations::TABLE_LOCATIONS, 'schedule_locations'), 'left')
                        ->on('schedules.location_id', '=', 'schedule_locations.id')
                    ->join(array(Model_Locations::TABLE_LOCATIONS, 'schedule_parent_locations'), 'left')
                        ->on('schedule_locations.parent_id', '=', 'schedule_parent_locations.id')
                    ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'booking_items'), 'inner')
                        ->on('timeslots.id', '=', 'booking_items.period_id')
                    ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                        ->on('bookings.booking_id', '=', 'booking_items.booking_id')
                    ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'), 'left')
                        ->on('bookings.booking_id', '=', 'has_delegates.booking_id')
                        ->on('has_delegates.deleted', '=', DB::expr(0))
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'delegates'), 'left')
                        ->on('has_delegates.contact_id', '=', 'delegates.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'leadbookers'), 'inner')
                        ->on('bookings.contact_id', '=', 'leadbookers.id')

                ->where('timeslots.delete', '=', 0)
                ->and_where('schedules.delete', '=', 0)
                ->and_where('booking_items.delete', '=', 0)
                ->and_where('bookings.delete', '=', 0)
                ->and_where('booking_items.booking_status', '<>', 3);

            if ($trainer_id == null) {
                $select
                    ->and_where_open()
                    ->and_where('timeslots.trainer_id', 'is', null)
                    ->and_where('schedules.trainer_id', 'is', null)
                    ->and_where_close();
            } else {
                $select
                    ->and_where_open()
                        ->or_where('timeslots.trainer_id', '=', $trainer_id)
                        ->or_where_open()
                            ->and_where('schedules.trainer_id', '=', $trainer_id)
                            ->and_where('timeslots.trainer_id', 'is', null)
                            ->and_where('timeslots.trainer_id', '=', 0)
                        ->or_where_close()
                    ->and_where_close();
            }
            $select->order_by('timeslots.datetime_start', 'asc');

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
                                Model_Automations_Trigger::filter_date_interval_helper($select, 'timeslots.datetime_start', $condition['execute'], $operator, $interval[2], $interval[1]);
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
            $bookings = $select->execute()->as_array();

            if (count($bookings) > 0) {
                $tmpfile = tempnam(Kohana::$cache_dir, 'trainerrollcalldigest-' . $trainer_id);
                $fid = fopen($tmpfile, "c+");
                fputcsv($fid, array_keys($bookings[0]), ',', '"');
                foreach ($bookings as $booking) {
                    fputcsv($fid, $booking, ',', '"');
                }
                fclose($fid);

                $result[] = array(
                    'automation_digest' => true,
                    'automation_attachments' => array(
                        array(
                            'path' => $tmpfile,
                            'name' => 'trainerrollcalldigest-' . $trainer_id . '-' . date('Ymd') . '.csv'
                        )
                    ),
                    'trainerid' => $trainer_id,
                    'trainerfirstname' => $trainer['trainerfirstname'],
                    'trainerlastname' => $trainer['trainerlastname'],
                    'traineremail' => $trainer['traineremail']
                );
            }
        }
        return $result;
    }
}
