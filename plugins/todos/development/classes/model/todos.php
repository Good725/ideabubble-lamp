<?php defined('SYSPATH') or die('No direct script access.');

class Model_Todos extends Model
{
    const TODOS_TABLE = 'plugin_todos_todos2';
    const HAS_SCHEDULES_TABLE = 'plugin_todos_todos2_has_schedules';
    const HAS_SUBJECTS_TABLE = 'plugin_todos_todos2_has_subjects';
    const HAS_ACADEMICYEARS_TABLE = 'plugin_todos_todos2_has_academicyears';
    const HAS_TOPICS_TABLE = 'plugin_todos_todos2_has_topics';
    const HAS_SEAT_ROWS_TABLE = 'plugin_todos_todos2_has_seat_rows';
    const HAS_RESULTS_TABLE = 'plugin_todos_todos2_has_results';
    const HAS_PERMISSIONS_TABLE = 'plugin_todos_todos2_has_permissions';
    const GRADES_TABLE = 'plugin_todos_grades';
    const FAVORITES_TABLE = 'plugin_todos_todos2_is_favorite';
    const ASSIGNED_STUDENTS_TABLE = 'plugin_todos_todos2_has_assigned_contacts';
    const RELATED_TO_TABLE = 'plugin_todos_related_list';
    const TODOS_CATEGORY = 'plugin_todos_todos2_categories';

    public static function get($id = null, $all = false)
    {
        $id = (int)$id;
        $user = Auth::instance()->get_user();
        $todo = DB::select('todos.*', array(
            DB::expr("CONCAT(UCASE(left(todos.type, 1)), SUBSTRING(REPLACE(LOWER(todos.type), '-', ' '), 2))"),
            'todo_type_label'),
            array('todo_category.title', 'todo_category'),
            array('favorites.user_id', 'is_favorite'), array('related_to.title', 'related_title'),
            array('related_to.related_table_id_column', 'related_id_column'),
            array('related_to.related_table_name', 'related_table_name'),
            array('related_to.related_table_title_column', 'related_table_column'),
            array('author.name', 'author_name'),
            DB::expr("CONCAT_WS(' - ', buildings.name, rooms.name) as location"))
            ->from(array(self::TODOS_TABLE, 'todos'))
            ->join(array(self::FAVORITES_TABLE, 'favorites'), 'left')
            ->on('todos.category_id', '=','todos.category_id')
            ->join(array(self::TODOS_CATEGORY, 'todo_category'), 'left')
            ->on('todos.category_id', '=','todo_category.id')
            //->on('favorites.user_id', '=', DB::expr($user['id']))
            ->join(array(self::RELATED_TO_TABLE, 'related_to'), 'left')
            ->on('todos.related_to_id', '=', 'related_to.id')
            ->join(array('engine_users', 'author'), 'left')
            ->on('todos.created_by', '=', 'author.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'rooms'), 'left')->on('todos.location_id', '=', 'rooms.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'buildings'), 'left')->on('rooms.parent_id', '=', 'buildings.id')
            ->where('todos.id', '=', $id)
            ->execute()
            ->current();
        $todo['related_to_label'] = self::get_related_to_details_by_id($todo['related_to_id'],
           $todo['related_to'])['value'];
        
        if ($todo && $all) {
            
            $todo['has_schedules'] = DB::select('has_schedules.schedule_id', 'schedules.name', DB::expr("CONCAT_WS(' - ', buildings.name, rooms.name) as location"))
                ->from(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('has_schedules.schedule_id',
                    '=', 'schedules.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'rooms'), 'left')->on('schedules.location_id', '=',
                    'rooms.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'buildings'), 'left')->on('rooms.parent_id', '=',
                    'buildings.id')

                ->where('todo_id', '=', $id)
                ->execute()
                ->as_array();
            
            $todo['has_subjects'] = DB::select('has_subjects.subject_id', 'subjects.name')
                ->from(array(self::HAS_SUBJECTS_TABLE, 'has_subjects'))
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'inner')->on('has_subjects.subject_id', '=',
                    'subjects.id')
                ->where('todo_id', '=', $id)
                ->execute()
                ->as_array();
            
            $todo['has_academicyears'] = DB::select('has_academicyears.academicyear_id', 'academicyears.title')
                ->from(array(self::HAS_ACADEMICYEARS_TABLE, 'has_academicyears'))
                ->join(array(Model_AcademicYear::TABLE_ACADEMICYEARS, 'academicyears'),
                    'inner')->on('has_academicyears.academicyear_id', '=', 'academicyears.id')
                ->where('todo_id', '=', $id)
                ->execute()
                ->as_array();
            
            // Get the assigned students to the todo
            $todo['has_assigned_contacts'] =  self::get_assignees_assigned_to_todo(@$todo['id']);
            if (count($todo['has_assigned_contacts']) == 1 && count($todo['has_schedules']) == 0) {
                $todo['assigned_contacts_type'] = "Person";
            } else {
                if (count($todo['has_schedules']) > 0) {
                    $todo['assigned_contacts_type'] = "Group";
                    $todo['assigned_schedule_students'] = Model_Schedules::get_students(array_column($todo['has_schedules'], 'schedule_id'));
                }
            }
            $todo['examiners'] =  $trainers = Model_Contacts3::get_teachers(array(
               'publish' => 1,
           ));
            $todo['has_assigned_examiners'] = self::get_examiners_assigned_to_todo(@$todo['id']);

                $result_query = DB::select(
                    'results.*',
                    'students.first_name',
                    'students.last_name',
                    array('examiners.first_name', 'examiner_first_name'),
                    array('examiners.last_name', 'examiner_last_name'),
                    'results.status',
                    array('schedules.name', 'schedule'),
                    array('courses.title', 'course'),
                    array('subjects.name', 'subject'),
                    array('levels.level', 'level')
                )
                    ->from(array(self::HAS_RESULTS_TABLE, 'results'))
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                    ->on('results.student_id', '=', 'students.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'examiners'), 'left')
                    ->on('results.examiner_id', '=', 'examiners.id')
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                    ->on('results.schedule_id', '=', 'schedules.id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('schedules.course_id', '=', 'courses.id')
                    ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                    ->on('courses.subject_id', '=', 'subjects.id')
                    ->join(array('plugin_courses_levels', 'levels'), 'left')
                    ->on('courses.level_id', '=', 'levels.id')
                    ->where('results.todo_id', '=', $id)
                    ->and_where('results.deleted' , '=', 0);
                $user_group = new Model_Roles($user['role_id']);
                $user_object = new Model_User($user['id']);
                if ($user_group->role != 'Administrator' && $user_group->role != 'Super User') {
                    $result_query->and_where('results.examiner_id', '=', $user_object->get_contact()->id);
                }
                $todo['results'] = $result_query->execute()
                    ->as_array();

            if (count($todo['has_schedules'])) {
                $schedule_ids = array();
                $saved_student_ids = array();
                foreach ($todo['results'] as $result) {
                    $saved_student_ids[] = $result['student_id'];
                }
                foreach ($todo['has_schedules'] as $has_schedule) {
                    $schedule_ids[] = $has_schedule['schedule_id'];
                }
                $students = $todo['has_assigned_contacts'] ?? Model_Schedules::get_students($schedule_ids);
                foreach ($students as $student) {
                    if (!in_array($student['contact_id'], $saved_student_ids)) {
                        // If there are no subjects have one empty record
                        $subjects = empty($todo['has_subjects']) ? [['subject_id' => '']] : $todo['has_subjects'];

                        // Result per student, per subject
                        foreach ($subjects as $subject) {
                            $todo['results'][] = array(
                                'id' => '',
                                'todo_id' => $todo['id'],
                                'schedule' => $student['schedule'],
                                'schedule_id' => $student['schedule_id'],
                                'student_id' => $student['contact_id'],
                                'first_name' => $student['first_name'],
                                'last_name' => $student['last_name'],
                                'level_id' => $student['level_id'],
                                'level' => $student['level'],
                                'subject_id' => $subject['subject_id'],
                                'result' => '',
                                'grade' => '',
                                'points' => '',
                                'comment' => ''
                            );
                        }
                    }
                }
            }
            
            $todo['permissions'] = DB::select('permissions.*', DB::expr("IFNULL(roles.role, 'Everyone') as role"))
                ->from(array(self::HAS_PERMISSIONS_TABLE, 'permissions'))
                ->join(array(Model_Roles::MAIN_TABLE, 'roles'), 'left')->on('permissions.role_id', '=', 'roles.id')
                ->where('permissions.todo_id', '=', $id)
                ->execute()
                ->as_array();
        }
        return $todo;
    }
    
    public static function save($todo)
    {
        $user = Auth::instance()->get_user();
        $todo['updated_by'] = @$user['id'];
        $todo['updated'] = date::now();
        if (!is_numeric(@$todo['id'])) {
            $todo['created_by'] = $todo['updated_by'];
            $todo['created'] = $todo['updated'];
            $inserted = DB::insert(self::TODOS_TABLE)
                ->values($todo)
                ->execute();
            $id = $inserted[0];
        } else {
            DB::update(self::TODOS_TABLE)
                ->set($todo)
                ->where('id', '=', $todo['id'])
                ->execute();
            $id = $todo['id'];
        }
        return $id;
    }
    
    public static function save_has_courses($todo_id, $has_courses)
    {
        DB::delete(self::HAS_COURSES_TABLE)
            ->where('todo_id', '=', $todo_id)
            ->execute();
        
        foreach ($has_courses as $course_id) {
            DB::insert(self::HAS_COURSES_TABLE)
                ->values(array('todo_id' => $todo_id, 'course_id' => $course_id))
                ->execute();
        }
    }
    
    public static function save_academicyears($todo_id, $has_academicyears)
    {
        DB::delete(self::HAS_ACADEMICYEARS_TABLE)
            ->where('todo_id', '=', $todo_id)
            ->execute();
        
        foreach ($has_academicyears as $academicyear_id) {
            DB::insert(self::HAS_ACADEMICYEARS_TABLE)
                ->values(array('todo_id' => $todo_id, 'academicyear_id' => $academicyear_id))
                ->execute();
        }
    }

    public static function save_assigned_students($todo_id, $assigned_students = array())
    {
        $previous_assigned_contacts = array_keys(DB::select('contact_id')
            ->from(self::ASSIGNED_STUDENTS_TABLE)
            ->and_where("todo_id", '=', $todo_id)
            ->and_where('role', '=', 'Student' )
            ->execute()->as_array('contact_id'));
        DB::delete(self::ASSIGNED_STUDENTS_TABLE)
            ->where("todo_id", '=', $todo_id)
            ->and_where('role', '=', 'Student')
            ->execute();
        if ($assigned_students) {
            foreach ($assigned_students as $assigned_student_id) {
                if (!in_array($assigned_student_id, $previous_assigned_contacts)) {
                    $user = Model_Contacts3::get_user_by_contact_id($assigned_student_id);
                    if($user !== null) {
                        self::send_user_todo_assigned_alert($user['id'], $todo_id);
                    }
                }
                $inserted = DB::insert(self::ASSIGNED_STUDENTS_TABLE)
                    ->values(array(
                        'todo_id' => $todo_id,
                        'contact_id' => $assigned_student_id,
                        'role' => 'Student'
                    ))->execute();
            }
        }
    }

    public static function save_assigned_examiners($todo_id, $assigned_examiners = array()) {
        $previous_assigned_contacts = array_keys(DB::select('contact_id')
            ->from(self::ASSIGNED_STUDENTS_TABLE)
            ->where("todo_id", '=', $todo_id)
            ->and_where('role', '=', 'Examiner' )
            ->execute()->as_array('contact_id'));
        DB::delete(self::ASSIGNED_STUDENTS_TABLE)
            ->where("todo_id", '=', $todo_id)
            ->and_where('role', '=', 'Examiner')
            ->execute();
        if ($assigned_examiners) {
            foreach ($assigned_examiners as $assigned_examiner_id) {
                if (!in_array($assigned_examiner_id, $previous_assigned_contacts)) {
                    $user = Model_Contacts3::get_user_by_contact_id($assigned_examiner_id);
                    if($user !== null) {
                        self::send_user_todo_assigned_alert($user['id'], $todo_id);
                    }
                }
                $inserted = DB::insert(self::ASSIGNED_STUDENTS_TABLE)
                    ->values(array(
                        'todo_id' => $todo_id,
                        'contact_id' => $assigned_examiner_id,
                        'role' => 'Examiner'
                    ))->execute();
            }
        }
    }

    public static function send_user_todo_assigned_alert($user_id, $todo_id)
    {
        $user = new Model_User($user_id);
        if (Auth::instance()->role_has_access($user->role_id, 'todos_edit')) {
            $todo_link = URL::site("/admin/todos/edit/{$todo_id}");
        } else {
            $todo_link = URL::site("/admin/todos/view/{$todo_id}");
        }
        $todo = self::get($todo_id);
        $messaging = new Model_Messaging();
        $params = [
            'todo_title' => $todo['title'],
            'author' => $todo['author_name'],
            'todo_link' => $todo_link,
        ];
        $recipients[] = [
            'target_type' => 'CMS_USER',
            'target' => $user_id,
        ];
        $messaging->send_template('todo_assigned_alert_user', null, null, $recipients, $params);
        return true;
    }

    public static function save_has_schedules($todo_id, $has_schedules, $assignee_type = false)
    {
        DB::delete(self::HAS_SCHEDULES_TABLE)
            ->where('todo_id', '=', $todo_id)
            ->execute();
        if($assignee_type != "Person") {
            foreach ($has_schedules as $schedule_id) {
                DB::insert(self::HAS_SCHEDULES_TABLE)
                    ->values(array('todo_id' => $todo_id, 'schedule_id' => $schedule_id))
                    ->execute();
            }
        }
    }
    
    public static function save_has_subjects($todo_id, $has_subjects)
    {
        DB::delete(self::HAS_SUBJECTS_TABLE)
            ->where('todo_id', '=', $todo_id)
            ->execute();
        
        foreach ($has_subjects as $subject_id) {
            DB::insert(self::HAS_SUBJECTS_TABLE)
                ->values(array('todo_id' => $todo_id, 'subject_id' => $subject_id))
                ->execute();
        }
    }
    
    public static function save_results($todo_id, $results)
    {
        if (!empty($results)) {
            foreach ($results as $result) {
                $result['todo_id'] = $todo_id;
                $id = @$result['id'];
                unset($result['id']);
                unset($result['result_id']);
                if ($id > 0) {
                    DB::update(self::HAS_RESULTS_TABLE)
                        ->set($result)
                        ->where('id', '=', $id)
                        ->execute();
                } else {
                    DB::insert(self::HAS_RESULTS_TABLE)
                        ->values($result)
                        ->execute();
                }
            }
        }
    }
    
    public static function save_permissions($todo_id, $roles)
    {
        DB::delete(self::HAS_PERMISSIONS_TABLE)
            ->where('todo_id', '=', $todo_id)
            ->execute();

        if (empty($roles)) {
            $roles = array(null);
        }
        
        foreach ($roles as $role) {
            DB::insert(self::HAS_PERMISSIONS_TABLE)
                ->values(array('todo_id' => $todo_id, 'role_id' => $role))
                ->execute();
        }
    }
    
    public static function save_from_post($post)
    {
        try {
            Database::instance()->begin();
            $user = Auth::instance()->get_user();
            $todo = array();
            $previos_assignees = array();
            if (is_numeric(@$post['id'])) {
                $previos_assignees = self::get_assignees_assigned_to_todo($post['id']);
                $todo['id'] = @$post['id'];
            }
            $todo['category_id'] = (is_numeric(@$post['category_id'])) ? $post['category_id'] : null;
            $todo['type'] = !empty($post['type']) ? $post['type'] : "Assignment";
            $todo['grading_type'] = $post['grading_type'] ?? "%+Grade+Points";
            $todo['mode'] = $post['mode'] ?? "Theory";
            $todo['file_uploads'] = $post['file_uploads'] ?? 0;
            $todo['title'] = $post['title'];
            $todo['summary'] = $post['summary'];
            $post['date'] = ($post['date'] == "") ? date("Y-m-d") : $post['date'];
            $todo['datetime'] = $post['date'] . ' ' . @$post['time_start'] ?? "00:00:00";
            $todo['datetime_end'] = $post['date'] . ' ' . @$post['time_end'] ?? "00:00:00";
            $todo['location_id'] = $post['location_id'];
            $todo['content_id'] = @$post['content_id'];
            $todo['status'] = $post['status'];
            $todo['priority'] = $post['priority'];
            $todo['delivery_mode'] = @$post['delivery_mode'];
            $todo['allow_manual_grading'] = !empty($post['allow_manual_grading']) && $post['allow_manual_grading'] == 'Manual';
            $todo['related_to_id'] = $post['related_to_id'];
            $todo['related_to'] = @$post['related_to_value'];
            $todo['course_id'] = @$post['course_id'];
            $todo ['results_published_datetime'] = date('Y-m-d H:i:s',
                strtotime("{$post['results_published_date']} {$post['results_published_time']}"));

            if (isset($post['grading_schema_id'])) $todo['grading_schema_id'] = $post['grading_schema_id'];
            
            if (@$post['user_id']) {
                $todo['owned_by'] = $post['user_id'];
            }
            $assigned_students = array();
            if ((@$post['type'] == 'State-Exam' || @$post['assignee-type'] == 'Group') && isset($post['student-picker-multiselect'])) {
                foreach ($post['student-picker-multiselect'] as $student_id) {
                    $assigned_students[] = $student_id;
                }
            } else {
                if (!empty($post['todo-group-student-search-autocomplete-id1'])) {
                    $assigned_students = array($post['todo-group-student-search-autocomplete-id1']);
                }
            }
            $assigned_examiners = array();
            if ((in_array(@$post['type'], array('State-Exam', 'Class-Test', 'Term-Assessment')) && isset($post['examiner-picker-multiselect']))) {
                foreach ($post['examiner-picker-multiselect'] as $examiner_id) {
                    $assigned_examiners[] = $examiner_id;
                }
            }

            $post['has_schedules'] = isset($post['has_schedules']) ? $post['has_schedules'] : [];
            
            if (@$post['todo-group-schedule-search-autocomplete-id1']) {
                $post['has_schedules'][] = $post['todo-group-schedule-search-autocomplete-id1'];
            }

            array_unique($post['has_schedules']);

            //$todo['published'] = @$post['published'] ?: 0;
            
            $id = self::save($todo);
            
            DB::delete(self::FAVORITES_TABLE)
                ->where('user_id', '=', $user['id'])
                ->and_where('todo_id', '=', $id)
                ->execute();
            
            if (@$post['favorite'] == 1) {
                DB::insert(self::FAVORITES_TABLE)
                    ->values(array('todo_id' => $id, 'user_id' => $user['id']))
                    ->execute();
            }

            // If there is only one linked schedule, assume it is the schedule used by the result
            if (!empty($post['has_schedules']) && !empty($post['result']) && count($post['has_schedules']) == 1) {
                foreach ($post['result'] as &$result) {
                    $result['schedule_id'] = $post['has_schedules'][0];
                }
            }
//            self::save_has_courses($id, @$post['has_courses'] ?: array());
            self::save_assigned_students($id, $assigned_students);
            self::save_assigned_examiners($id, $assigned_examiners);
            self::save_has_schedules($id, $post['has_schedules'] ?: array(), @$post['assignee-type']);
            self::save_has_subjects($id, @$post['has_subjects'] ?: array());
            self::save_academicyears($id, @$post['academicyear_ids'] ?: array());
            if ($post['result']) {
                if (empty($assigned_students)) {
                    self::save_results($id, array());
                } else {
                    $results = $post['result'];
                    $students = $assigned_students;
                    foreach ($post['result'] as $key => $result) {
                        if (!in_array($result['student_id'], $students)) {
                            self::delete_results($result['id']);
                            unset($results[$key]);
                        }
                    }
                    $student_ids = array_column($results, 'student_id');
                    foreach($students as $student) {
                        if(!in_array($student, $student_ids)) {
                            $student_id = $student;
                            $schedule_students  = Model_Schedules::get_students($post['has_schedules']);
                            foreach($schedule_students as $schedule_student) {
                                if ($schedule_student['student_id'] == $student_id) {
                                    $student = $schedule_student;
                                }
                            }
                            $subjects = empty($todo['has_subjects']) ? [['subject_id' => '']] : $todo['has_subjects'];
                            // Result per student, per subject
                            foreach ($subjects as $subject) {
                                $results[] = array(
                                    'id' => '',
                                    'todo_id' => $id,
                                    'schedule_id' => isset($student['schedule_id']) ? $student['schedule_id'] : 0,
                                    'student_id' => isset($student['student_id']) ? $student['student_id'] : $student,
                                    'level_id' => isset($student['level_id']) ? @$student['level_id'] : null,
                                    'subject_id' => $subject['subject_id'],
                                    'examiner_id' => null,
                                    'result' => '',
                                    'grade' => '',
                                    'points' => '',
                                    'comment' => ''
                                );
                            }
                        }
                    }
                    self::save_results($id, @$results ?: array());
                }
            } else {
                if (!is_numeric(@$post['id'])) {

                    if (!empty($assigned_students)) {
                        $examiner_number = 0;
                        $students = $assigned_students ?? Model_Schedules::get_students($post['has_schedules']);
                        foreach ($students as $student) {
                            if (!is_array($student)) {
                                $student_id = $student;;
                                $schedule_students  = Model_Schedules::get_students($post['has_schedules']);

                                foreach($schedule_students as $schedule_student) {
                                    if ($schedule_student['student_id'] == $student_id) {
                                        $student = $schedule_student;
                                        }
                                    }

                                }
                                // If there are no subjects have one empty record
                                $subjects = empty($todo['has_subjects']) ? [['subject_id' => '']] : $todo['has_subjects'];
                                // Result per student, per subject
                                foreach ($subjects as $subject) {
                                    $todo['results'][] = array(
                                        'id' => '',
                                        'todo_id' => $id,
                                        'schedule_id' => isset($student['schedule_id']) ? $student['schedule_id'] : $student,
                                        'student_id' => @$student['student_id'],
                                        'level_id' => @$student['level_id'],
                                        'subject_id' => $subject['subject_id'],
                                        'examiner_id' => $assigned_examiners[$examiner_number],
                                        'result' => '',
                                        'grade' => '',
                                        'points' => '',
                                        'comment' => ''
                                    );
                                    if ($examiner_number == count($assigned_examiners) - 1 ) {
                                        $examiner_number = 0;
                                    } else {
                                        $examiner_number++;
                                    }
                                }
                            }
                        }
                    } elseif (!empty($assigned_students)) {
                    $students = $assigned_students;
                    foreach ($students as $student) {
                        if (!is_array($student)) {
                            $student_id = $student;
                            $schedule_students  = Model_Schedules::get_students($post['has_schedules']);
                            foreach($schedule_students as $schedule_student) {
                                if ($schedule_student['student_id'] == $student_id) {
                                    $student = $schedule_student;
                                }
                            }

                        }
                        // If there are no subjects have one empty record
                        $subjects = empty($todo['has_subjects']) ? [['subject_id' => '']] : $todo['has_subjects'];
                        // Result per student, per subject
                        foreach ($subjects as $subject) {
                            $todo['results'][] = array(
                                'id' => '',
                                'todo_id' => $id,
                                'schedule_id' => isset($student['schedule_id']) ? $student['schedule_id'] : 0,
                                'student_id' => isset($student['student_id']) ? $student['student_id'] : $student,
                                'level_id' => isset($student['level_id']) ? @$student['level_id'] : null,
                                'subject_id' => $subject['subject_id'],
                                'examiner_id' => null,
                                'result' => '',
                                'grade' => '',
                                'points' => '',
                                'comment' => ''
                            );
                        }
                    }
                }
                self::save_results($id, @$todo['results'] ?: array());
            }
            self::save_permissions($id, @$post['role_id']);

            Database::instance()->commit();
            $assignees = self::get_assignees_assigned_to_todo($id);
            $alert_assignees = array();
            foreach ($assignees as $assignee) {
                $alert = true;
                foreach ($previos_assignees as $previos_assignee) {
                    if ($previos_assignee['contact_id'] == $assignee['contact_id']) {
                        $alert = false;
                        break;
                    }
                }
                if ($alert) {
                    $alert_assignees[] = $assignee;
                }
            }

            if ($todo['type'] == 'Task') {
                Model_Automations::run_triggers(Model_Todos_TaskSaveTrigger::NAME, array('todo_id' => $id, 'assignees' => $alert_assignees));
            } else if ($todo['type'] == 'Exam' || $todo['type'] == 'Class-Test'  || $todo['type'] == 'State-Exam' || $todo['type'] == 'Term-Assessment') {
                Model_Automations::run_triggers(Model_Todos_ExamSaveTrigger::NAME, array('todo_id' => $id, 'assignees' => $alert_assignees));
            } else if ($todo['type'] == 'Assignment') {
                Model_Automations::run_triggers(Model_Todos_AssignmentSaveTrigger::NAME, array('todo_id' => $id, 'assignees' => $alert_assignees));
            }
            if (count($alert_assignees) > 0) {
                if ($todo['type'] == 'Task') {
                    Model_Automations::run_triggers(Model_Todos_TaskAssignedTrigger::NAME, array('todo_id' => $id, 'assignees' => $alert_assignees));
                } else if ($todo['type'] == 'Exam' || $todo['type'] == 'Class-Test'  || $todo['type'] == 'State-Exam' || $todo['type'] == 'Term-Assessment') {
                    Model_Automations::run_triggers(Model_Todos_AssesmentAssignedTrigger::NAME, array('todo_id' => $id, 'assignees' => $alert_assignees));
                } else if ($todo['type'] == 'Assignment') {
                    Model_Automations::run_triggers(Model_Todos_AssignmentAssignedTrigger::NAME, array('todo_id' => $id, 'assignees' => $alert_assignees));
                }
            }

            return $id;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
    
    public static function save_limited_from_post($post)
    {
        $contact = Auth::instance()->get_contact();

        if(isset($post['assignee_id'])) {
            $assigned_students = array($post['assignee_id']);
            self::save_assigned_students($post['id'], $assigned_students);
        }

        $has_assignee = new Model_Todo_HasAssignee(['todo_id' => $post['id'], 'contact_id' => $contact->id]);
        $has_assignee->set('status', $post['status']);
        $has_assignee->save_with_moddate();

        if (!empty($post['file_id'])) {
            $file_submission = new Model_Todo_FileSubmission();
            $file_submission->todo_id    = $post['id'];
            $file_submission->contact_id = $contact->id;
            $file_submission->file_id    = $post['file_id'];
            $file_submission->version    = $file_submission->calculate_version();
            $file_submission->save_with_moddate();
        }
    }

    public static function duplicate($id)
    {
        $todo = self::get($id);
        unset($todo['id']);
        $todo['title'] = $todo['title'] . ' - clone';
        $columns = Database::instance()->list_columns('plugin_todos_todos2');
        $todo_save_data = array_intersect_key($todo, $columns);
        $id = self::save($todo_save_data);
        return $id;
    }
    
    public static function search($params = array())
    {
        $sortColumns = array();
        $sortColumns[] = 'todos.title';
        $sortColumns[] = 'todo_category.title';
        $sortColumns[] = 'todos.type';
        $sortColumns[] = 'schedules.name';
        $sortColumns[] = DB::expr('CONCAT(reporter.first_name," ",reporter.last_name)');
        if (isset($params['contact_id'])) {
            $sortColumns[] = 'todos.status';
            $sortColumns[] = 'todos.datetime';
        } else {
            $sortColumns[] = '';
            $sortColumns[] = 'todos.status';
            $sortColumns[] = 'todos.datetime';
            $sortColumns[] = 'todos.updated';
        }
        
        // Find plugin_todo info but also find if it has multiple or many students
        $select = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS todos.*'),
            array(DB::expr('todo_category.title'),'todo_category'),
            DB::expr("CONCAT(UCASE(left(todos.type, 1)), SUBSTRING(REPLACE(LOWER(todos.type), '-', ' '), 2)) as todo_type_label"),
            DB::expr("GROUP_CONCAT(DISTINCT schedules.name) as schedules"),
            DB::expr("CONCAT(reporter.first_name, ' ', reporter.last_name) as reporter_name"),
            DB::expr("COUNT(distinct assigned_contacts.contact_id) as num_assigned_contacts"),
            DB::expr("if(COUNT(distinct assigned_contacts.contact_id) = 1, CONCAT(assignee.first_name, ' ', assignee.last_name), '') as assigned_contact"),
            DB::expr("GROUP_CONCAT(CONCAT(assignee.first_name, ' ', assignee.last_name)) as assignee_contacts"),
            DB::expr("CONCAT_WS(' - ', buildings.name, rooms.name) as location"),
            DB::expr("GROUP_CONCAT(assignee_users.id) as assignee_user_ids")
        )
            ->from(array(self::TODOS_TABLE, 'todos'))
            ->join(array(self::TODOS_CATEGORY, 'todo_category'), 'left')->on('todos.category_id', '=',
                'todo_category.id')
            ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'left')->on('todos.id', '=',
                'has_schedules.todo_id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'reporter'), 'left')->on('todos.created_by', '=',
                'reporter.linked_user_id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')->on('has_schedules.schedule_id', '=',
                'schedules.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'rooms'), 'left')->on('todos.location_id', '=',
                'rooms.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'buildings'), 'left')->on('rooms.parent_id', '=',
                'buildings.id')
            ->join(array(self::ASSIGNED_STUDENTS_TABLE, 'assigned_contacts'), 'left')
            ->on('todos.id', '=', 'assigned_contacts.todo_id')
            ->on('assigned_contacts.role', '=', DB::expr('"Student"'))
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'assignee'), 'left')->on('assigned_contacts.contact_id', '=',
                'assignee.id')
            ->join(array(Model_Users::MAIN_TABLE, 'assignee_users'), 'left')->on('assignee.linked_user_id', '=', 'assignee_users.id')
            ->where('todos.deleted', '=', 0);
        
        $select->group_by('todos.id');

        if (isset($params['id'])) {
            $select->and_where('todos.id', (is_array($params['id']) ? 'in' : '='), $params['id']);
        }

        if (@$params['type']) {
            $select->and_where('todos.type', (is_array($params['type']) ? 'in' : '='), $params['type']);
        }

        if (@$params['keyword']) {
            $select->and_where('todos.title', 'like', '%' . $params['keyword'] . '%');
        }
        
        if (@$params['schedule_id']) {
            $select->and_where('schedules.id', '=', $params['schedule_id']);
        }
        
        if (@$params['course_id']) {
            $select->and_where('courses.id', '=', $params['course_id']);
        }
        
        if (@$params['trainer_id']) {
            $select->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=',
                'timeslots.schedule_id')
                ->and_where_open()
                ->or_where('schedules.trainer_id', '=', $params['trainer_id'])
                ->or_where('timeslots.trainer_id', '=', $params['trainer_id'])
                ->and_where_close();
        }
        
        if (@$params['student_id']) {
            $select->join(array(self::HAS_RESULTS_TABLE, 'has_results'), 'inner')->on('has_results.todo_id', '=',
                'todos.id');
            if (is_array($params['student_id'])) {
                $select->and_where('has_results.student_id', 'in', $params['student_id']);
            } else {
                $select->and_where('has_results.student_id', '=', $params['student_id']);
            }
            $select->group_by('has_results.student_id');
        }
        
        if (@$params['contact_id']) {
            $select
                ->join(array(self::ASSIGNED_STUDENTS_TABLE, 'has_assigned_contacts'), 'left')
                    ->on('todos.id', '=', 'has_assigned_contacts.todo_id')
                ->and_where_open()
                    ->where('has_assigned_contacts.contact_id', '=', $params['contact_id'])
                    ->or_where('reporter.id', '=', $params['contact_id'])
                ->and_where_close();
        }
        if (@$params['location_id']) {
            $select->and_where_open()
                ->or_where('rooms.id', 'in', $params['location_id'])
                ->or_where('buildings.id', 'in', $params['location_id'])
                ->and_where_close();
        }
        
        if (@$params['building_id']) {
            $select->and_where('buildings.id', 'in', $params['building_id']);
        }
        
        if (@$params['room_id']) {
            $select->and_where('rooms.id', 'in', $params['room_id']);
        }

        if (@$params['user_id']) {
            $select->join(array(self::HAS_PERMISSIONS_TABLE, 'to_users'), 'inner')->on('todos.id', '=', 'to_users.todo_id');
            $select->and_where_open();
            $select->or_where('todos.created_by', '=', $params['user_id']);
            //$select->or_where('to_users.to_user_id', '=', $params['user_id']);
            $select->and_where_close();
        }
        
        if (@$params['after']) {
            $select->and_where('todos.datetime', '>=', $params['after']);
        }
        if (@$params['before']) {
            $select->and_where('todos.datetime', '<=', $params['before']);
        }
        
        if (@$params['offset']) {
            $select->offset($params['offset']);
        }
        
        if (@$params['limit'] > 0 && @$params['limit'] <= 1000) {
            $select->limit($params['limit']);
        } else {
            $select->limit(100);
        }
        
        if (isset($params['iSortCol_0'])) {
            for ($i = 0; $i < $params['iSortingCols']; $i++) {
                if ($sortColumns[$params['iSortCol_' . $i]] != '') {
                    if (in_array($params['sSortDir_' . $i], array('asc', 'desc'))) {
                        $select->order_by($sortColumns[$params['iSortCol_' . $i]], $params['sSortDir_' . $i]);
                    }
                }
            }
        }
        
        if (@$params['sSearch'] != '') {
            $todo_cols = DB::query(Database::SELECT,
                'SHOW FULL COLUMNS FROM ' . self::TODOS_TABLE)->execute()->as_array();
            $select->and_where_open();
            foreach ($todo_cols as $todo_col) {
                $select->or_where("todos.{$todo_col['Field']}", 'LIKE', "%{$params['sSearch']}%");
            }
            $select->or_where("schedules.name", 'LIKE', "%{$params['sSearch']}%");
            $select->or_where(DB::expr("concat(assignee.first_name, ' ', assignee.last_name)"), 'LIKE',
                "%{$params['sSearch']}%");
            $select->or_where(DB::expr("concat(reporter.first_name, ' ', reporter.last_name)"), 'LIKE',
                "%{$params['sSearch']}%");
            $select->and_where_close();
        }

        $todos = $select->execute()->as_array();
        DB::query(null, "SET @found_todos = FOUND_ROWS()")->execute();
        return $todos;
    }
    
    public static function list_datatable($params = array())
    {
        $todos = self::search($params);
        $result = array();
        foreach ($todos as $i => $todo) {
            if (empty($todo['assigned_contact'])) {
                $assignees = self::get_assignees_assigned_to_todo($todo['id']);
                if (count($assignees) == 0) {
                    $assignees_html = "None";
                } else {
                    $assignees_html = '<span class="more_info">Multiple</span>
                        <div class="assignees_list" style="display: none; position: absolute; width: 150px; border: 1px solid black; background-color: white;">';
                    foreach ($assignees as $assignee) {
                        $assignees_html .= "{$assignee['first_name']} {$assignee['last_name']} <br>";
                    }
                    $assignees_html .= "</div>";
                }
            } else {
                $assignees_html = $todo['assigned_contact'];
            }
            $todo_object = new Model_Todo_Item($todo['id']);
            $assignee_object = null;
            if (isset($params['contact_id'])) {
                $assignee_object = $todo_object->has_assignees->where('contact_id', '=', $params['contact_id'])->find();
            }
            $result[$i] = array();
            $result[$i]['title'] = $todo['title'];
            $result[$i]['delivery_mode'] = $todo['delivery_mode'];
            $result[$i]['category'] = $todo['todo_category'];
            $result[$i]['type'] = $todo['todo_type_label'];
            $result[$i]['schedule'] = $todo['schedules'];
            $result[$i]['reporter'] = $todo['reporter_name'];
            $result[$i]['status'] = isset($params['contact_id']) && !empty($assignee_object) ? $assignee_object->status : $todo['status'];
            $result[$i]['date'] = IbHelpers::relative_time_with_tooltip($todo['datetime_end']);
            $result[$i]['assignee'] = $assignees_html;
            $result[$i]['updated'] = IbHelpers::relative_time_with_tooltip($todo['updated']);
            $content = new Model_Content("117");

            $can_edit = (
                (Auth::instance()->has_access('todos_edit_limited') && Auth::instance()->get_user()['id'] == $todo['created_by'])
                || Auth::instance()->has_access('todos_edit')
            );

            if (isset($params['contact_id'])) {
                $result[$i]['actions'] = '<div class="dropdown action-btn">
                    <a class="btn" href="#todo_table-actions-' . $todo['id'] . '" data-toggle="collapse" role="button" aria-expanded="false">
                        <span class="sr-only">Actions</span>
                        <span class="icon-ellipsis-h" aria-hidden="true"></span>
                    </a>
                    <ul class="dropdown-menu collapse" id="todo_table-actions-' . $todo['id'] . '">
                        <li>
                            <a data-id="' . $todo['id'] . '" href="/admin/todos/view/' . $todo['id'] . '" class="edit-link">' . __('View') . '</a>
                        </li>';

                if ($can_edit) {
                    $result[$i]['actions'] .= '<li>
                        <a data-id="' . $todo['id'] . '" href="/admin/todos/edit/' . $todo['id'] . '" class="edit-link">' . __('Edit') . '</a>
                    </li>';
                }

                if ($todo['content_id']) {
                    $content_label = $content->count_user_complete_subsections() ? 'Continue' : 'Start';
                    $result[$i]['actions'] .= '<li><a href="/admin/todos/my_todo/' . $todo['id'] . '">'. $content_label . '</a></li>';
                }
                $result[$i]['actions'] .=  '</ul></div>';
            } else {
                $result[$i]['assignee'] = $assignees_html;
                $result[$i]['updated'] = IbHelpers::relative_time_with_tooltip($todo['updated']);
                // Don't allow people with limited edit access to see the edit action unless they created the todo
                $edit_action = (Auth::instance()->has_access('todos_edit_limited') && Auth::instance()->get_user()['id'] == $todo['created_by']) ||
                (Auth::instance()->has_access('todos_edit'))
                    ? '<li> <a data-id="' . $todo['id'] . '" href="/admin/todos/edit/' . $todo['id'] .
                    '" class="edit-link">' . __('Edit') . '</a> </li><li>
                            <a data-id="' . $todo['id'] . '" href="/admin/todos/delete/' . $todo['id'] . '" class="delete">Delete</a>
                        </li>' : '';
                
                $result[$i]['actions'] = '<div class="dropdown action-btn">
                    <a class="btn" href="#todo_table-actions-' . $todo['id'] . '" data-toggle="collapse" role="button" aria-expanded="false">
                        <span class="sr-only">Actions</span>
                        <span class="icon-ellipsis-h" aria-hidden="true"></span>
                    </a>
                    <ul class="dropdown-menu collapse" id="todo_table-actions-' . $todo['id'] . '">'
                    . $edit_action .
                    '<li>
                            <a data-id="' . $todo['id'] . '" href="/admin/todos/clone/' . $todo['id'] . '" class="clone">Clone</a>
                        </li>
                        <li>
                            <a data-id="' . $todo['id'] . '" href="/admin/todos/email/' . $todo['id'] . '" class="email">Email results</a>
                        </li>
                    </ul>
                </div>';
            }
        }
        return $result;
    }
    
    public static function results($params = array())
    {
        $sortColumns = array();
        $sortColumns[] = 'todos.title';
        $sortColumns[] = DB::expr("CONCAT(UCASE(left(todos.type, 1)), SUBSTRING(REPLACE(LOWER(todos.type), '-', ' '), 2))");
        $sortColumns[] = 'todos.datetime';
        $sortColumns[] = DB::expr("CONCAT(students.first_name, ' ', students.last_name)");
        $sortColumns[] = 'has_results.result';
        $sortColumns[] = 'has_results.grade';
        $sortColumns[] = 'has_results.comment';
        
        $select = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS todos.*'),
            DB::expr("CONCAT(UCASE(left(todos.type, 1)), SUBSTRING(REPLACE(LOWER(todos.type), '-', ' '), 2)) as todo_type_label"),
            ['has_results.id', 'result_id'],
            'has_results.schedule_id',
            'has_results.student_id',
            'has_results.result',
            'has_results.grade',
            'has_results.comment',
            'students.first_name',
            'students.last_name',
            ['schedules.name', 'schedule']
        )
            ->from(array(self::TODOS_TABLE, 'todos'))
            ->where('todos.deleted', '=', 0);
        
        $select
            ->join(array(self::HAS_RESULTS_TABLE, 'has_results'), 'inner')->on('has_results.todo_id', '=', 'todos.id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')->on('has_results.schedule_id', '=',
                'schedules.id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')->on('has_results.student_id', '=',
                'students.id');
        
        if (@$params['keyword']) {
            $select->and_where('todos.title', 'like', '%' . $params['keyword'] . '%');
        }
        
        if (@$params['todo_id']) {
            $select->and_where('todos.id', '=', $params['todo_id']);
        }
        
        if (@$params['student_id']) {
            if (is_array($params['student_id'])) {
                $select->and_where('has_results.student_id', 'in', $params['student_id']);
            } else {
                $select->and_where('has_results.student_id', '=', $params['student_id']);
            }
        }
        
        if (@$params['offset']) {
            $select->offset($params['offset']);
        }
        
        if (@$params['limit'] > 0 && @$params['limit'] <= 1000) {
            $select->limit($params['limit']);
        } else {
            $select->limit(100);
        }
    
        if (isset($params['iSortCol_0'])) {
            for ($i = 0; $i < $params['iSortingCols']; $i++) {
                if ($sortColumns[$params['iSortCol_' . $i]] != '') {
                    $params['sSortDir_' . $i] = strtolower($params['sSortDir_' . $i]);
                    if (!in_array($params['sSortDir_' . $i], ['asc', 'desc'])) {
                        $params['sSortDir_' . $i] = 'asc';
                    }
                    $select->order_by($sortColumns[$params['iSortCol_' . $i]], $params['sSortDir_' . $i]);
                }
            }
        }

        if ($params['my'] == 'true' && !Auth::instance()->has_access('todos_view_results')) {
            $select->and_where_open()
                        ->or_where('todos.results_published_datetime', '<', DB::expr('now()'))
                        ->or_where('todos.results_published_datetime', 'is', null)
                    ->and_where_close();
        }

        if (@$params['sSearch'] != '') {
            $select->and_where_open();
            foreach($sortColumns as $sortColumn) {
                $select->or_where($sortColumn, 'LIKE', "%{$params['sSearch']}%");
            }
            $select->and_where_close();
        }
        
        $select->group_by('todos.id')->group_by('has_results.student_id');

        $todos = $select->execute()->as_array();

        // Get the grade for each result
        foreach ($todos as &$todo) {
            $result = new Model_Todo_Result($todo['result_id']);
            $todo['grade'] = $result->get_result('grade_name');
        }

        DB::query(null, "SET @found_results = FOUND_ROWS()")->execute();
        return $todos;
    }
    
    public static function results_datatable($params = array())
    {
        $todos = self::results($params);
        $result = array();
        foreach ($todos as $i => $todo) {
            $result_item = array();
            $result_item['title'] = $todo['title'];
            $result_item['type'] = $todo['todo_type_label'];
            $result_item['datetime'] = IbHelpers::relative_time_with_tooltip($todo['datetime']);
            if($params['extended_results']) {
                $result_item['examiner'] = '';
            }
            $result_item['student'] = $todo['first_name'] . ' ' . $todo['last_name'];
            if($params['extended_results']) {
                $result_item['schedule'] = 'Schedule';
                $result_item['questions'] = 'Questions';
                $result_item['mark'] = 'Mark';
            }
            $result_item['result'] = $todo['result'];
            $result_item['grade'] = $todo['grade'];
            if($params['extended_results']) {
                $result_item['status'] = '';
            }
            $result_item['comment'] = $todo['comment'];
            if($params['extended_results']) {

                $result_item['actions'] = View::factory('snippets/btn_dropdown')
                    ->set('type', 'actions')
                    ->set('options', [
                        [
                            'type' => 'link',
                            'icon' => 'pencil',
                            'title' => 'Edit',
                            'attributes' =>
                                [
                                    'class' => 'edit-link',
                                    'href' => '/admin/todos/edit/' . $todo['id']
                                ]
                        ],
                        [
                            'type' => 'link',
                            'icon' => 'eye',
                            'title' => 'View results',
                            'attributes' =>
                                [
                                    'class' => 'view-link',
                                    'href' => '/admin/todos/edit/' . $todo['id'] . '#todo-results-tab'
                                ]
                        ],
                        [
                            'type' => 'link',
                            'icon' => 'link',
                            'title' => 'Assign',
                            'attributes' => [
                                'class' => 'assign-result-link',
                                'href' => '/admin/todos/edit/' . $todo['id']
                            ]
                        ]
                    ])->render();
            }
            $result[] =$params['extended_results'] ? array_values($result_item) : $result_item;
        }
        return $result;
    }

    public static function delete_results($result_id = null, $todo_id = null) {
        if (!is_numeric($result_id)) {
            return false;
        }
        DB::update(self::HAS_RESULTS_TABLE)
            ->set(array('deleted' => 1))
            ->where('id', '=', $result_id)
            ->execute();
        return true;
    }
    
    public static function email_results($todo_id)
    {
        $results = Model_Todos::results(array('todo_id' => $todo_id));
        
        $mm = new Model_Messaging();
        foreach ($results as $result) {
            $recipients = array(
                array('target_type' => 'CMS_CONTACT3', 'target' => $result['student_id'])
            );
            $c3 = new Model_Contacts3($result['student_id']);
            if (!$c3->get_is_primary()) {
                $f = new Model_Family($c3->get_family_id());
                $primary_id = $f->get_primary_contact_id();
                $recipients[] = array('target_type' => 'CMS_CONTACT3', 'target' => $primary_id);
            }
            $mm->send_template(
                'todo-result-email',
                null,
                null,
                $recipients,
                array(
                    'todo' => $result['title'],
                    'type' => $result['type'],
                    'mode' => $result['mode'],
                    'student' => $result['first_name'] . ' ' . $result['last_name'],
                    'result' => $result['result'],
                    'grade' => $result['grade'] ?? "N/A",
                    'comment' => $result['comment']
                )
            );
        }
        
        return $results;
    }
    
    public static function grades_list()
    {
        $grades = DB::select('*')
            ->from(self::GRADES_TABLE)
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();
        
        return $grades;
    }
    
    public static function grades_save_post($post)
    {
        $grades = $post['grade'];
        $ids = array();
        
        foreach ($grades as $grade) {
            foreach ($grade as $field => $value) {
                if ($value == "") {
                    $grade[$field] = null;
                }
            }
            if (@$grade['id']) {
                DB::update(self::GRADES_TABLE)
                    ->set($grade)
                    ->where('id', '=', $grade['id'])
                    ->execute();
                $ids[] = $grade['id'];
            } else {
                $inserted = DB::insert(self::GRADES_TABLE)
                    ->values($grade)
                    ->execute();
                $ids = $inserted[0];
            }
        }
        
        DB::update(self::GRADES_TABLE)
            ->set(array('deleted' => 1))
            ->where('id', 'not in', $ids);
    }
    
    public static function get_grade_from_percent($percent, $grades, $ho)
    {
        foreach ($grades as $grade) {
            if ($grade['percent_min'] <= $percent && $grade['percent_max'] >= $percent) {
                if ($ho) {
                    if ($grade['points_h'] !== null) {
                        return $grade;
                    }
                } else {
                    if ($grade['points_h'] === null) {
                        return $grade;
                    }
                }
            }
        }

        // Save details related to this assignees to-to submission
        $has_assignee = new Model_Todo_HasAssignee(['todo_id' => $post['id'], 'contact_id' => $contact->id]);
        $has_assignee->set('status', $post['status']);
        $has_assignee->save_with_moddate();

        // Save newly uploaded files
        if (!empty($post['file_id'])) {
            $file_submission = new Model_Todo_FileSubmission();
            $file_submission->todo_id    = $post['id'];
            $file_submission->contact_id = $contact->id;
            $file_submission->file_id    = $post['file_id'];
            $file_submission->version    = $file_submission->calculate_version();
            $file_submission->save_with_moddate();
        }
    }

    
    public static function get_related_to($title = false)
    {
        $result = DB::select('*')
            ->from(self::RELATED_TO_TABLE)
            ->where('deleted', '=', 0);
        if ($title) {
            $result->and_where('title', '=', $title)
                ->execute()->current();
        } else {
            $result = $result->execute()->as_array();
        }
        
        return $result;
    }
    
    public static function get_related_to_autocomplete($related_to, $term)
    {
        $relation = DB::select('*')
            ->from(self::RELATED_TO_TABLE)
            ->where('id', '=', $related_to)
            ->execute()
            ->current();
        
        if ($relation && $relation['related_table_name']) {
            $result = DB::select(array(DB::expr($relation['related_table_id_column']), 'id'),
                array(DB::expr($relation['related_table_title_column']), 'value'))
                ->from($relation['related_table_name'])
                ->where($relation['related_table_deleted_column'], '=', 0)
                ->having('value', 'like', '%' . $term . '%')
                ->order_by('value', 'asc')
                ->limit(10)
                ->execute()
                ->as_array();
        } else {
            $result = array();
        }
        return $result;
    }
    
    public static function get_related_to_details_by_id($related_to, $id)
    {
        $relation = DB::select('*')
            ->from(self::RELATED_TO_TABLE)
            ->where('id', '=', $related_to)
            ->or_where('title', '=', $related_to)
            ->execute()
            ->current();
        
        if ($relation && $relation['related_table_name'] && $id) {
            $result = DB::select(array(DB::expr($relation['related_table_id_column']), 'id'),
                array(DB::expr($relation['related_table_title_column']), 'value'))
                ->from($relation['related_table_name'])
                ->where($relation['related_table_deleted_column'], '=', 0)
                ->and_where($relation['related_table_id_column'], '=', $id)
                ->execute()
                ->current();
        } else {
            $result = null;
        }
        return $result;
    }
    
    public static function get_assignees_assigned_to_todo($todo_id, $schedules = array())
    {
        $select = DB::select(
            array('contacts.id', 'contact_id'),
            'contacts.title',
            'contacts.first_name',
            'contacts.last_name',
            array('schedules.name', 'schedule'),
            array('has_results.id', 'id'),
            array('has_results.level_id', 'level_id'),
            array('has_results.subject_id', 'subject_id'),
            array('has_results.result', 'result'),
            array('has_results.points', 'points'),
            array('has_results.comment', 'comment'),
            array('linked_users.id', 'user_id')
        )
            ->from(array(self::ASSIGNED_STUDENTS_TABLE, 'assigned_contacts'))
            ->join(array(self::HAS_RESULTS_TABLE, 'has_results'), 'LEFT')
            ->on('assigned_contacts.contact_id', '=', 'has_results.student_id')
            ->on('has_results.todo_id', '=', 'assigned_contacts.todo_id')
            ->join(array(self::TODOS_TABLE, 'todo'), 'LEFT')
            ->on('has_results.todo_id', '=', 'todo.id')
            ->join(array('plugin_courses_schedules', 'schedules'), 'LEFT')
            ->on('has_results.schedule_id', '=', 'schedules.id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'LEFT')
            ->on('assigned_contacts.contact_id', '=', 'contacts.id')
            ->join(array(Model_Users::MAIN_TABLE, 'linked_users'), 'left')->on('contacts.linked_user_id', '=', 'linked_users.id')
            ->and_where('assigned_contacts.todo_id', '=', $todo_id)
            ->and_where('assigned_contacts.role', '=', 'Student');
        if (is_array($schedules)) {
            foreach ($schedules as $schedule) {
                $select->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'INNER')
                    ->on('todo.id', '=', 'has_schedules.todo_id');
                $select->and_where_open();
                $select->or_where('has_schedules.schedule_id', '=', $schedule['schedule_id']);
                $select->and_where_close();
            }
        } else {
            $select->and_where('has_schedules.schedule_id', '=', $schedules['schedule_id']);
        }
        
        $select->group_by("contacts.id");
        return $select->execute()->as_array();
    }

    public static function get_examiners_assigned_to_todo($todo_id, $schedules = array()) {
        $select = DB::select(
        array('contacts.id', 'contact_id'),
            'contacts.title',
            'contacts.first_name',
            'contacts.last_name',
            array('linked_users.id', 'user_id'))
            ->from(array(self::ASSIGNED_STUDENTS_TABLE, 'assigned_contacts'))
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'LEFT')
            ->on('assigned_contacts.contact_id', '=', 'contacts.id')
            ->join(array(Model_Users::MAIN_TABLE, 'linked_users'), 'left')->on('contacts.linked_user_id', '=', 'linked_users.id')
            ->and_where('assigned_contacts.todo_id', '=', $todo_id)
            ->and_where('assigned_contacts.role', '=', 'Examiner');
        $select->group_by("contacts.id");
        return $select->execute()->as_array();
    }
    
    // Depreciated todo plugins below
    
    public function get_todo($id, $user_id = null)
    {
        $select = $this->get_todos_select_query();
        $select->and_where('todos.id', '=', $id);
        if (is_numeric($user_id)) {
            $select->and_where_open();
            $select->or_where('todos.created_by', '=', $user_id);
            $select->or_where('to_users.to_user_id', '=', $user_id);
            $select->and_where_close();
        }
        $result = $select->execute()->as_array();
        
        if (count($result) == 0) {
            throw new Exception('No such to do: #' . $id);
        }
        
        // add calculated fields: status_color
        $result = $this->add_calculated_fields($result);
        
        return $result[0];
    }
    
    
    public function get_todos_select_query()
    {
        $q = DB::select(
            "todos.*",
            DB::expr("GROUP_CONCAT(to_users.to_user_id) AS to_user_ids"),
            DB::expr("CONCAT_WS(' ', from_user.name, from_user.surname) AS from_user_name"),
            DB::expr("GROUP_CONCAT(CONCAT_WS(' ', users_to.name, users_to.surname)) AS to_user_name"),
            DB::expr("IF(relates.related_open_link_url IS NOT NULL, REPLACE(relates.related_open_link_url, '#ID#', todos.related_to_id), '') AS url")
        )
            ->from(array('plugin_todos_todos2', 'todos'))
            ->join(array('plugin_todos_to_users', 'to_users'), 'left')
            ->on('todos.id', '=', 'to_users.todo_id')
            ->join(array('engine_users', 'users_to'), 'left')
            ->on('to_users.to_user_id', '=', 'users_to.id')
            ->join(array('engine_users', 'from_user'), 'left')
            ->on('todos.created_by', '=', 'from_user.id')
            ->join(array('plugin_todos_related_list', 'relates'), 'left')
            ->on('todos.related_to', '=', 'relates.id')
            ->where('todos.deleted', '=', 0)
            ->group_by('todos.id')
            ->order_by('todos.priority', 'desc');
        return $q;
    }
    
    public function get_all_todos()
    {
        //TODO: initial sort order by calculated field $status_order
        
        $user = Auth::instance()->get_user();
        
        $filter = DB::select(DB::expr('DISTINCT todo_id'))
            ->from('plugin_todos_to_users')
            ->where('to_user_id', '=', $user['id']);
        
        $select = $this->get_todos_select_query();
        $select->join(array($filter, 'filter'), 'inner')->on('todos.todo_id', '=', 'filter.todo_id');
        //$select->and_where('to_users.to_user_id', '=', $user['id']);
        $result = $select->execute()->as_array();
        
        // add calculated fields: status_color
        return $this->add_calculated_fields($result);
    }
    
    public function get_all_todos_for_all_users()
    {
        $select = $this->get_todos_select_query();
        $result = $select->execute()->as_array();
        
        // add calculated fields: status_color
        return $this->add_calculated_fields($result);
    }
    
    
    public function get_all_related_todos($plugin_name, $related_to_id)
    {
        
        $query = DB::query(Database::SELECT, "select p.*
            , concat(from_user.name ,' ' , from_user.surname) as from_user_name
            , concat(to_user.name ,' ' , to_user.surname) as to_user_name
            from plugin_todos p, engine_users from_user, engine_users to_user, plugin_todos_to_users to_users
            where p.from_user_id=from_user.`id` and p.related_to = :plugin_name and related_to_id= :related_to_id and p.deleted = '0' and p.id = to_users.todo_id and to_users.`to_user_id`=to_user.`id`")
            ->param(':plugin_name', $plugin_name)
            ->param(':related_to_id', $related_to_id)
            ->execute()
            ->as_array();
        
        // add calculated fields: status_color
        return $this->add_calculated_fields($query);
        
    }
    
    private function add_calculated_fields(&$query)
    {
        
        foreach ($query as $key => $todo) {
            $status_color = '';
            $status_order = '';
            if (($todo['status_id'] == 'Open' || $todo['status_id'] == 'In Progress') && ($todo['priority_id'] == 'High' || $todo['priority_id'] == 'Normal')) {
                $status_color = 'red';
                $status_order = 2;
            } elseif (($todo['status_id'] == 'Open' || $todo['status_id'] == 'In Progress') && ($todo['priority_id'] == 'Low')) {
                $status_color = 'yellow';
                $status_order = 1;
            }
            
            $query[$key]['status_color'] = $status_color;
            $query[$key]['status_order'] = $status_order;
        }
        
        return $query;
        
    }
    
    
    public function get_users_as_options($selected_user_ids)
    {
        if ($selected_user_ids != '' && is_string($selected_user_ids)) {
            $selected_user_ids = explode(',', $selected_user_ids);
        }
        $users = DB::select('id', DB::expr("CONCAT_WS(' ', name, surname) AS username"))
            ->from('engine_users')
            ->where('deleted', '=', 0)
            ->order_by('username', 'ASC')
            ->execute()
            ->as_array();
        
        $options = html::optionsFromRows('id', 'username', $users, $selected_user_ids);
        
        return $options;
    }
    
    
    public static function get_related_todos_count()
    {
        $user = Auth::instance()->get_user();
        
        $cnt = DB::select(DB::expr('count(*) as cnt'))
            ->from(array('plugin_todos', 'todos'))
            ->join(array('plugin_todos_to_users', 'to_users'), 'inner')
            ->on('todos.todo_id', '=', 'to_users.todo_id')
            ->where('to_users.to_user_id', '=', $user['id'])
            ->execute()
            ->get('cnt');
        return $cnt;
    }
    
    public function validate($todo)
    {
        // Is the todo title set?
        if (!$todo['title']) {
            IbHelpers::set_message('To Do is not saved! Title is missing!', 'error');
        } else {
            return true; // validation OK
        }
        
        return false; // validation NOT OK
        
    }
    
    public function add_todo($todo, $related_to, $related_to_id)
    {
        $to_user_ids = isset($todo['to_user_id']) ? $todo['to_user_id'] : array();
        $to_user_ids = is_array($to_user_ids) ? $to_user_ids : array($to_user_ids);
        
        unset ($todo['to_user_id']);
        $todo['due_date'] = date::dmy_to_ymd($todo['due_date']);
        $date = explode('-', $todo['due_date']);
        if (!isset($date['0']) OR !isset($date['1']) OR !isset($date['2']) OR !checkdate((int)$date['1'],
                (int)$date['2'], (int)$date['0'])) {
            $todo['due_date'] = null;
        }
        
        $todo['contact_id'] = isset($todo['contact_id']) ? $todo['contact_id'] : '';
        
        try {
            Database::instance()->begin();
            
            $query = DB::insert('plugin_todos', array(
                'title',
                'details',
                'from_user_id',
                'status_id',
                'priority_id',
                'type_id',
                'due_date',
                'related_to',
                'related_to_id',
                'related_to_text',
                'contact_id',
                'date_created'
            ))
                ->values(array(
                    $todo['title'],
                    $todo['details'],
                    $todo['from_user_id'],
                    $todo['status_id'],
                    $todo['priority_id'],
                    $todo['type_id'],
                    $todo['due_date'],
                    $related_to,
                    $related_to_id,
                    @$todo['related_to_text'],
                    $todo['contact_id'],
                    DB::expr('NOW()')
                ))
                ->execute();
            $todo_id = $query[0];
            foreach ($to_user_ids as $to_user_id) {
                DB::insert('plugin_todos_to_users')
                    ->values(array('todo_id' => $todo_id, 'to_user_id' => $to_user_id))->execute();
            }
            
            $activity = new Model_Activity;
            $activity->set_action('create')->set_item_type('todos')->set_item_id($todo_id)->save();
            
            Database::instance()->commit();
        } catch (Exception $exc) {
            Log::instance()->add(Log::ERROR,
                "Error adding todo\n" . $exc->getMessage() . "\n" . $exc->getTraceAsString());
            Database::instance()->rollback();
            throw $exc;
        }
        
        return $query[0];
    }
    
    
    public function update_todo($todo_id, $todo)
    {
        $to_user_ids = isset($todo['to_user_id']) ? $todo['to_user_id'] : array();
        unset ($todo['to_user_id']);
        unset ($todo['related_to_plugin']);
        $todo['due_date'] = date::dmy_to_ymd($todo['due_date']);
        $date = explode('-', $todo['due_date']);
        if (!isset($date['0']) OR !isset($date['1']) OR !isset($date['2']) OR !checkdate((int)$date['1'],
                (int)$date['2'], (int)$date['0'])) {
            $todo['due_date'] = null;
        }
        
        $todo['date_updated'] = DB::expr('NOW()');
        
        try {
            Database::instance()->begin();
            DB::update('plugin_todos')->set($todo)->where('todo_id', '=', $todo_id)->execute();
            DB::delete('plugin_todos_to_users')->where('todo_id', '=', $todo_id)->execute();
            
            if (is_array($to_user_ids)) {
                foreach ($to_user_ids as $to_user_id) {
                    DB::insert('plugin_todos_to_users')->values(array(
                        'todo_id' => $todo_id,
                        'to_user_id' => $to_user_id
                    ))->execute();
                }
            } else {
                DB::insert('plugin_todos_to_users')->values(array(
                    'todo_id' => $todo_id,
                    'to_user_id' => $to_user_ids
                ))->execute();
            }
            
            
            $activity = new Model_Activity;
            $activity->set_action('update')->set_item_type('todos')->set_item_id($todo_id)->save();
            
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            Log::instance()->add(Log::ERROR,
                "Error updating todo\n" . $exc->getMessage() . "\n" . $exc->getTraceAsString());
            throw $exc;
        }
        return true;
        
    }
    
    public function delete_todo($todo_id)
    {
        try {
            $query = DB::update('plugin_todos')
                ->set(array('deleted' => '1', 'date_updated' => DB::expr('NOW()')))
                ->where('todo_id', '=', $todo_id)
                ->execute();
            
            $activity = new Model_Activity;
            $activity->set_action('delete')->set_item_type('todos')->set_item_id($todo_id)->save();
            IbHelpers::set_message('The To Do has been deleted', 'success');
        } catch (exception $e) {
            IbHelpers::set_message('The To Do is not deleted', 'error');
            Log::instance()->add(Log::ERROR,
                "Error deleting todo\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
    
    //Contact To Do
    public function validate_contact_todo($post)
    {
        $date = explode('-', date::dmy_to_ymd($post['due_date']));
        
        if (!$post['title']) {
            IbHelpers::set_message('To Do is not saved! Title is missing!', 'error');
            return false;
        } elseif (!isset($date['0']) OR !isset($date['1']) OR !isset($date['2']) OR !checkdate((int)$date['1'],
                (int)$date['2'], (int)$date['0'])) {
            if (!empty($date['0'])) {
                IbHelpers::set_message('To Do is not saved! Date is not valid!', 'error');
                return false;
            }
            return true;
        } else {
            return true;
        }
    }
    
    public function add_contact_todo($todo)
    {
        try {
            $user = Auth::instance()->get_user();
            $todo['from_user_id'] = $user['id'];
            $todo['related_to_text'] = '';
            self::add_todo($todo, 'contacts', $todo['contact_id']);
            return true;
        } catch (exception $e) {
            IbHelpers::set_message('Error adding todo. Please ask an administrator to check the application logs.',
                'danger');
            Log::instance()->add(Log::ERROR, "Error adding todo\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }
    
    public function add_account_todo($todo)
    {
        try {
            $user = Auth::instance()->get_user();
            $todo['from_user_id'] = $user['id'];
            $todo['related_to_text'] = '';
            self::add_todo($todo, 'accounts', $todo['transaction_id']);
            return true;
        } catch (exception $e) {
            IbHelpers::set_message('Error adding todo. Please ask an administrator to check the application logs.',
                'danger');
            Log::instance()->add(Log::ERROR, "Error adding todo\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }
    
    public function add_policy_todo($todo)
    {
        try {
            $user = Auth::instance()->get_user();
            $todo['from_user_id'] = $user['id'];
            $todo['related_to_text'] = '';
            self::add_todo($todo, 'policy', $todo['policy_id']);
            return true;
        } catch (exception $e) {
            IbHelpers::set_message('Error adding todo. Please ask an administrator to check the application logs.',
                'danger');
            Log::instance()->add(Log::ERROR, "Error adding todo\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }
    
    public function add_claim_todo($todo)
    {
        try {
            $user = Auth::instance()->get_user();
            $todo['from_user_id'] = $user['id'];
            $todo['related_to_text'] = '';
            self::add_todo($todo, 'claim', $todo['claim_id']);
            return true;
        } catch (exception $e) {
            IbHelpers::set_message('Error adding todo. Please ask an administrator to check the application logs.',
                'danger');
            Log::instance()->add(Log::ERROR, "Error adding todo\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }
    
    //TODOS TAB
    public function get_todos_related_to_customer($contact_id)
    {
        try {
            $query = DB::query(Database::SELECT, "select p.*
            , concat(from_user.name ,' ' , from_user.surname) as from_user_name
            , concat(to_user.name ,' ' , to_user.surname) as to_user_name
            from plugin_todos p
            join plugin_todos_to_users on plugin_todos_to_users.todo_id = p.todo_id
			join engine_users from_user on p.from_user_id=from_user.`id`
			JOIN engine_users to_user ON `plugin_todos_to_users`.`to_user_id`=to_user.`id`
            where contact_id = :contact_id and p.deleted = '0'
            ORDER BY status_id ASC, CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC, priority_id DESC")
                ->param(':contact_id', $contact_id)
                ->execute()
                ->as_array();
            
            return $this->add_calculated_fields($query);
            
        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR,
                "Error fetching todo list.\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            return '';
        }
        
    }
    
    public function get_todos_related_to_claim($claim_id)
    {
        try {
            $query = DB::query(Database::SELECT, "SELECT p.*,
                CONCAT(from_user.name ,' ' , from_user.surname) AS from_user_name,
                CONCAT(to_user.name ,' ' , to_user.surname) AS to_user_name
                FROM plugin_todos p
                JOIN plugin_todos_to_users ON plugin_todos_to_users.todo_id = p.todo_id
                JOIN engine_users from_user ON p.from_user_id=from_user.`id`
                JOIN engine_users to_user ON `plugin_todos_to_users`.`to_user_id`=to_user.`id`
                WHERE (p.`related_to` = 2 OR p.`related_to` = 'claim')
                AND p.`related_to_id` = :claim_id
                AND p.deleted = '0'
                ORDER BY status_id ASC,
                CASE WHEN due_date IS NULL THEN 1 ELSE 0 END,
                due_date ASC,
                priority_id DESC")
                ->param(':claim_id', $claim_id)
                ->execute()
                ->as_array();
            
            return $this->add_calculated_fields($query);
            
        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR,
                "Error fetching todo list.\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            return '';
        }
        
    }
    
    public function get_todos_related_to_policy($policy_id)
    {
        try {
            $query = DB::query(Database::SELECT, "SELECT p.*,
                CONCAT(from_user.name ,' ' , from_user.surname) AS from_user_name,
                CONCAT(to_user.name ,' ' , to_user.surname) AS to_user_name
                FROM plugin_todos p
                JOIN plugin_todos_to_users ON plugin_todos_to_users.todo_id = p.todo_id
                JOIN engine_users from_user ON p.from_user_id=from_user.`id`
                JOIN engine_users to_user ON `plugin_todos_to_users`.`to_user_id`=to_user.`id`
                WHERE (p.`related_to` = 4 OR p.`related_to` = 'policy')
                AND p.`related_to_id` = :policy_id
                AND p.deleted = '0'
                ORDER BY status_id ASC,
                CASE WHEN due_date IS NULL THEN 1 ELSE 0 END,
                due_date ASC,
                priority_id DESC")
                ->param(':policy_id', $policy_id)
                ->execute()
                ->as_array();
            
            return $this->add_calculated_fields($query);
            
        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR,
                "Error fetching todo list.\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            return '';
        }
        
    }
    
    public function get_last_todos($amount, $contact_id)
    {
        try {
            $query = DB::query(Database::SELECT, "select title
            from plugin_todos
            where contact_id = :contact_id and deleted = '0' ORDER BY due_date ASC LIMIT :amount")
                ->param(':contact_id', $contact_id)
                ->param(':amount', $amount)
                ->execute()
                ->as_array();
            
            return $query;
            
        } catch (Exception $e) {
            Log::instance()->add(Log::ERROR,
                "Error fetching todo list.\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
            return '';
        }
    }
    
    public static function get_all_todos_for_dashboard($user_id)
    {
        return DB::select()
            ->from('plugin_todos')
            ->join('engine_users', 'inner')
            ->on('plugin_todos.to_user_id', '=', 'engine_users.id')
            ->join('users_view', 'inner')
            ->on('plugin_todos.from_user_id', '=', 'users_view.id')
            ->where('due_date', '!=', null)
            ->and_where('plugin_todos.to_user_id', '=', $user_id)
            ->and_where('plugin_todos.status_id', '=', 'Open')
            ->order_by('due_date', 'ASC')
            ->limit(4)
            ->execute()
            ->as_array();
    }
    
    public static function todo_contacts3_relation($family_id = 0, $contact_id = 0, $todo_id = 0)
    {
        DB::insert('plugin_contacts3_todos_relation', array('family_id', 'user_id', 'todo_id'))->values(array(
            'family_id' => $family_id,
            'user_id' => $contact_id,
            'todo_id' => $todo_id
        ))->execute();
    }
    
    public static function get_all_educate_todos($family_id, $contact_id)
    {
        $searchq = DB::select(
            'todos.*',
            DB::expr("CONCAT_WS(' ', engine_users.name,  engine_users.surname) AS reporter"),
            DB::expr("CONCAT_WS(' ', plugin_contacts3_contacts.first_name,  plugin_contacts3_contacts.last_name) AS assignee"),
            array(DB::expr("CONCAT(UCASE(left(todos.type, 1)), SUBSTRING(REPLACE(LOWER(todos.type), '-', ' '), 2))"),
                'todo_type_label'))
            ->from(array(self::TODOS_TABLE, 'todos'))
            ->join(self::ASSIGNED_STUDENTS_TABLE, 'LEFT')
                ->on('todos.id', '=', 'plugin_todos_todos2_has_assigned_contacts.todo_id')
            ->join('engine_users',  'LEFT')
                ->on('todos.created_by', '=', 'engine_users.id')
            ->join('plugin_contacts3_contacts',  'INNER')
                ->on('plugin_contacts3_contacts.id', '=', 'plugin_todos_todos2_has_assigned_contacts.contact_id')
            ->where('todos.deleted', '=', 0);
        
        if ($family_id) {
            $searchq->and_where('plugin_contacts3_contacts.family_id', '=', $family_id);
        }
        if ($contact_id) {
            $searchq->and_where('plugin_todos_todos2_has_assigned_contacts.contact_id', '=', $contact_id);
        }
        
        return $searchq->execute()->as_array();
    }
    
    public static function get_related_to_list()
    {
        $result = DB::select('*')
            ->from('plugin_todos_related_list')
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();
        return $result;
    }
    
    public static function update_exam_status() {
        DB::update(self::TODOS_TABLE)->set(array('status' => 'Done'))->where('datetime', '<', DB::expr('current_timestamp'))
            ->and_where_open()->or_where('type', '=', 'Class-Test')->or_where('type', '=', 'Term-Assessment')
            ->or_where('type', '=', 'State-Exam')->and_where_close()->execute();
    }
}