<?php

class Model_Todos_AssignmentAssignedTrigger extends Model_Todos_TaskAssignedTrigger
{
    const NAME = 'an Assignment is assigned';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('todo_id');
        $this->multiple_results = true;

        $this->filters = array(
            array('field' => 'todo_status', 'label' => 'Todo Status', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_type_id', 'label' => 'Course Type', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_category_id', 'label' => 'Course Category', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_subject_id', 'label' => 'Course Subject', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'course_id', 'label' => 'Course', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'schedule_id', 'label' => 'Schedule', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'trainer_id', 'label' => 'Trainer', 'operators' => array('=' => 'IS', '<>' => 'NOT')),
            array('field' => 'end_date_interval', 'label' => 'Due Date(Relative)', 'operators' => array('>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
            array('field' => 'end_date', 'label' => 'Due Date(Exact)', 'operators' => array('=' => '=', '>=' => '>=', '<=' => '<=', '>' => '>', '<' => '<')),
        );

        $this->generated_message_params = array(
            '@id@',
            '@title@',
            '@type@',
            '@mode@',
            '@status@',
            '@priority@',
            '@summary@',
            '@gradingtype@',
            '@duedate@',
            '@starttime@',
            '@endtime@',
            '@date@',
            '@location@',
            '@locationcounty@',
            '@assigneeid@',
            '@assigneename@',
            '@assigneemobile@',
            '@assigneeemail@',
            '@scheduleid@',
            '@schedule@',
            '@courseid@',
            '@course@',
            '@category@',
            '@subject@',
            '@trainerid@',
            '@trainername@',
            '@trainerfirstname@',
            '@trainerlastname@',
            '@traineremail@',
            '@trainermobile@'
        );
    }

    public function filter($data, $sequence)
    {
        $conditions_mode = $sequence['conditions_mode'];
        $conditions = $sequence['conditions'];
        $join_schedule = false;
        foreach ($conditions as $condition) {
            switch ($condition['field']) {
                case 'course_type_id':
                case 'course_category_id':
                case 'course_subject_id':
                case 'course_id':
                case 'schedule_id':
                case 'trainer_id':
                    $join_schedule = true;
                    break;
                default:
                    break;
            }
        }

        $variables = Model_Automations::get_sequence_variables($sequence);

        $this->no_duplicate_email_tag = array('id');

        $this->filter_select = DB::select(
            'todos.id',
            'todos.title',
            'todos.type',
            'todos.mode',
            'todos.status',
            'todos.priority',
            'todos.summary',
            array('todos.grading_type', 'gradingtype'),
            array('todos.datetime_end', 'duedate'),
            DB::expr("DATE_FORMAT(todos.datetime, '%d %M %Y') as date"),
            DB::expr("DATE_FORMAT(todos.datetime, '%H:%i') as starttime"),
            DB::expr("DATE_FORMAT(todos.datetime_end, '%H:%i') as endtime"),
            DB::expr('CONCAT_WS(" - ", parent_locations.name, locations.name) as location'),
            DB::expr("IFNULL(parent_counties.name, counties.name) as locationcounty")

        )->from(array(Model_Todos::TODOS_TABLE, 'todos'))
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')
                ->on('todos.location_id', '=', 'locations.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'parent_locations'), 'left')
                ->on('locations.parent_id', '=', 'parent_locations.id')
            ->join(array('plugin_courses_counties', 'counties'), 'left')
                ->on('locations.county_id', '=', 'counties.id')
            ->join(array('plugin_courses_counties', 'parent_counties'), 'left')
                ->on('parent_locations.county_id', '=', 'parent_counties.id')
        ->where('todos.deleted', '=', 0);

        if (isset($data['todo_id'])) {
            $this->filter_select->and_where('todos.id', (is_array($data['todo_id']) ? 'in' : '='), $data['todo_id']);
        }

        if (
            in_array('assigneeid', $variables) ||
            in_array('assigneename', $variables) ||
            in_array('assigneemobile', $variables) ||
            in_array('assigneeemail', $variables)
        ) {
            $this->no_duplicate_email_tag[] = 'assigneeid';
            $this->filter_select->select_array(
                array(
                    'assignees.status',
                    array('assignees.contact_id', 'assigneeid'),
                    DB::expr('CONCAT_WS(" ", contacts.first_name, contacts.last_name) as assigneename'),
                    DB::expr("CONCAT_WS('', mobiles.country_dial_code, mobiles.dial_code, mobiles.value) as assigneemobile"),
                    array('emails.value', 'assigneeemail')
                )
            )->join(array(Model_Todos::ASSIGNED_STUDENTS_TABLE, 'assignees'), 'left')
                ->on('todos.id', '=', 'assignees.todo_id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                ->on('assignees.contact_id', '=', 'contacts.id')
            ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobiles'), 'left')
                ->on('contacts.notifications_group_id', '=', 'mobiles.group_id')
                ->on('mobiles.notification_id', '=', DB::expr(2))
            ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')
                ->on('contacts.notifications_group_id', '=', 'emails.group_id')
                ->on('emails.notification_id', '=', DB::expr(1));
        }

        if (
            in_array('scheduleid', $variables) ||
            in_array('schedule', $variables) ||
            in_array('courseid', $variables) ||
            in_array('course', $variables) ||
            in_array('category', $variables) ||
            in_array('subject', $variables) ||
            in_array('trainerid', $variables) ||
            in_array('trainername', $variables) ||
            in_array('trainerfirstname', $variables) ||
            in_array('trainerlastname', $variables) ||
            in_array('traineremail', $variables) ||
            in_array('trainermobile', $variables) ||
            $join_schedule
        ) {
            $this->filter_select->select_array(
                array(
                    array('has_schedules.schedule_id', 'scheduleid'),
                    array('schedules.name', 'schedule'),
                    array('schedules.course_id', 'courseid'),
                    array('courses.title', 'course'),
                    'categories.category',
                    array('subjects.name', 'subject'),
                    array('schedules.trainer_id', 'trainerid')
                )
            )->join(array(Model_Todos::HAS_SCHEDULES_TABLE, 'has_schedules'), 'left')
                ->on('todos.id', '=', 'has_schedules.todo_id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                ->on('has_schedules.schedule_id', '=', 'schedules.id')
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                ->on('schedules.course_id', '=', 'courses.id')
            ->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')
                ->on('courses.category_id', '=', 'categories.id')
            ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                ->on('courses.subject_id', '=', 'subjects.id');
            if (
                in_array('trainername', $variables) ||
                in_array('trainerfirstname', $variables) ||
                in_array('trainerlastname', $variables) ||
                in_array('traineremail', $variables) ||
                in_array('trainermobile', $variables)
            ) {
                $this->filter_select->select_array(
                    array(
                        DB::expr('CONCAT_WS(" ", trainers.first_name, trainers.last_name) as trainername'),
                        DB::expr("CONCAT_WS('', trainer_mobiles.country_dial_code, trainer_mobiles.dial_code, trainer_mobiles.value) as trainermobile"),
                        array('trainer_emails.value', 'traineremail'),
                        array('trainers.first_name', 'trainerfirstname'),
                        array('trainers.last_name', 'trainerlastname'),
                    )
                )->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers') ,'left')
                    ->on('schedules.trainer_id', '=', 'trainers.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'trainer_mobiles'), 'left')
                    ->on('trainers.notifications_group_id', '=', 'trainer_mobiles.group_id')
                    ->on('trainer_mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'trainer_emails'), 'left')
                    ->on('trainers.notifications_group_id', '=', 'trainer_emails.group_id')
                    ->on('trainer_emails.notification_id', '=', DB::expr(1));
            }
        }

        if (empty($conditions)) {
            return $this->filter_select->execute()->as_array();
        }

        if (isset($data['todo_id'])) {
            $this->filter_select->and_where('todos.id', (is_array($data['todo_id']) ? 'in' : '='), $data['todo_id']);
        }

        $condition_function = 'and_where';
        $condition_open_function = 'and_where_open';
        $condition_close_function = 'and_where_close';
        if ($conditions_mode == 'OR') {
            $this->filter_select->and_where_open();
            $condition_function = 'or_where';
            $condition_open_function = 'and_where_open';
            $condition_close_function = 'and_where_close';
        } else {
            $this->filter_select->and_where_open();
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
                case 'todo_status':
                    call_user_func(array($this->filter_select, $condition_function), 'todos.status', $operator, $condition['val']);
                    break;
                case 'course_type_id':
                    call_user_func(array($this->filter_select, $condition_function), 'courses.type_id', $operator, $condition['val']);
                    break;
                case 'course_category_id':
                    call_user_func(array($this->filter_select, $condition_function), 'courses.category_id', $operator, $condition['val']);
                    break;
                case 'course_subject_id':
                    call_user_func(array($this->filter_select, $condition_function), 'courses.subject_id', $operator, $condition['val']);
                    break;
                case 'course_id':
                    call_user_func(array($this->filter_select, $condition_function), 'schedules.course_id', $operator, $condition['val']);
                    break;
                case 'schedule_id':
                    call_user_func(array($this->filter_select, $condition_function), 'schedules.id', $operator, $condition['val']);
                    break;
                case 'trainer_id':
                    call_user_func(array($this->filter_select, $condition_function), 'schedules.trainer_id', $operator, $condition['val']);
                    break;
                case 'end_date_interval':
                    call_user_func(array($this->filter_select, $condition_function . '_open'));
                    if (preg_match('/(\d+)\s*(minute|hour|day|week|month)/', $condition['val'], $interval)) {
                        Model_Automations_Trigger::filter_date_interval_helper($this->filter_select, 'todos.datetime_end', $condition['execute'], $operator, $interval[2], $interval[1]);
                    }
                    call_user_func(array($this->filter_select, $condition_function . '_close'));
                    break;
                case 'end_date':
                    call_user_func(array($this->filter_select, $condition_function), 'todos.datetime_end', $operator, $condition['val']);
                    break;
                default:
                    throw new Exception("Unknown condition field: " . $condition['field']);
                    break;
            }
        }
        if ($conditions_mode == 'OR') {
            $this->filter_select->and_where_close();
        } else {
            $this->filter_select->and_where_close();
        }
        return $this->filter_select->execute()->as_array();
    }
}