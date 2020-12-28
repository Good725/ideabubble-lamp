<?= (isset($alert)) ? $alert : '' ?>
<?php if (isset($alert)) : ?>
    <script>
        remove_popbox();
    </script>
<?php endif; ?>

<div>
    <form class="form-horizontal validate-on-submit" id="todo-edit-form" name="todo-edit-form" method="post">
        <div class="form-group clearfix">
            <div class="col-xs-12">
                <?= Form::ib_input(__("{$todo['todo_type_label']} title"), 'title', @$todo['title'],
                    array('class' => 'validate[required]', 'id' => 'todo-title')) ?>
            </div>
        </div>
        
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#todo-details-tab" aria-controls="todo-details-tab" role="tab" data-toggle="tab"><?= __('Details') ?></a>
            </li>
            <?php if ($todo['type'] != "Task"): ?>
                <li role="presentation">
                    <a href="#todo-results-tab" aria-controls="todo-results-tab" role="tab" data-toggle="tab"><?= __('Results') ?></a>
                </li>
            <?php endif; ?>
            <li role="presentation">
                <a href="#todo-permissions-tab" aria-controls="todo-permissions-tab" role="tab" data-toggle="tab"><?= __('Permissions') ?></a>
            </li>
            <?php if (in_array($todo['type'], array('State-Exam', 'Exam', 'Class-Test', 'Term-Assessment', 'Assignment'))
                && Auth::instance()->has_access('todos_content_tab')): ?>
                <li role="presentation">
                    <a href="#todo-content-tab" aria-controls="todo-content-tab" role="tab" data-toggle="tab"><?= __('Content') ?></a>
                </li>
            <?php endif; ?>
            <li role="presentation">
                <a href="#todo-activity-tab" aria-controls="todo-activity-tab" role="tab" data-toggle="tab"><?= __('Activity') ?></a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- details -->
            <div role="tabpanel" class="tab-pane active" id="todo-details-tab">



                <?php if(
                        Settings::instance()->get('todos_site_allow_online_exams') &&
                        isset($todo['type']) &&
                        (in_array($todo['type'], array('State-Exam', 'Exam', 'Class-Test', 'Term-Assessment')))):?>
                    <div class="delivery-mode-section form-group vertically_center"
                         data-todo_delivery_mode="Delivery">
                        <label class="col-xs-12 col-sm-2" for="delivery-mode"><?= __('Delivery') ?> <span>*</span></label>

                        <div class="col-xs-12 col-sm-10 col-md-8">
                            <?php
                            $options = array(
                                'Classroom' => __('Classroom'),
                                'Online' => __('Online')
                            );
                            echo Form::btn_options('delivery_mode', $options, @$todo['delivery_mode'], false,
                                array('class' => 'validate[required]', 'id' => 'delivery-mode'));
                            ?>
                        </div>
                    </div>
                    <div id="todo_location" class="form-group vertically_center <?= (isset($todo['type']) && (!in_array($todo['type'],
                            array("Task", "Assignment"))) && (empty($todo['delivery_mode']) || $todo['delivery_mode'] != 'Online')) ? "" : " hidden" ?>">
                            <label class="col-sm-2" for="location_id">Location</label>

                            <div class="col-xs-12 col-sm-5">
                            <?php
                            $rooms = $locations;
                            $options = array();
                            foreach ($locations as $location) {
                                if ($location['parent_id'] == null) {
                                    $options[$location['id']] = $location['name'];

                                    foreach ($rooms as $room) {
                                        if ($room['parent_id'] == $location['id']) {
                                            $options[$room['id']] = $location['name'] . ', ' . $room['name'];
                                        }
                                    }
                                }
                            }

                            echo Form::ib_select(__('Location'), 'location_id', $options, @$todo['location_id'],
                                array('id' => 'location_id'));
                            ?>
                        </div>
                        </div>
                <?php endif?>
                <div class="form-group vertically_center">
                    <?php if ($todo['type'] == "Task" || $todo['type'] == "Assignment"): ?>
                        <input type="hidden" name="type" value="<?= $todo['type'] ?>">
                    <?php else: ?>
                        <?php if(Settings::instance()->get('todos_site_display_tests')
                        || Settings::instance()->get('todos_site_display_assesments')
                        || Settings::instance()->get('todos_site_display_exams')):?>
                        <div class="col-xs-12 col-sm-2"><?= __('Type') ?> <span>*</span></div>

                        <div class="col-xs-12 col-sm-10 col-md-8">
                            <?php
                            $options = array();
                            if(Settings::instance()->get('todos_site_display_tests')) {
                                $options['Class-Test'] = __('Class test');
                            }
                            if (Settings::instance()->get('todos_site_display_assesments')) {
                                $options['Term-Assessment'] = __('Term assessment');
                            }
                            if (Settings::instance()->get('todos_site_display_exams')) {
                                $options['State-Exam'] = __('Final exam');
                            }
                            echo Form::btn_options('type', $options, @$todo['type'], false,
                                array('class' => 'validate[required]', 'id' => 'todo-type'))
                            ?>
                        </div>
                        <?php endif;?>
                    <?php endif; ?>
                </div>
                <div class="todo-type-section form-group vertically_center <?= (isset($todo['type']) && (in_array($todo['type'],
                        array("Class-Test", "Assignment")))) ? "" : " hidden" ?>"
                     data-todo_type="Class-Test Assignment">
                    <label class="col-xs-12 col-sm-2" for="todo-mode"><?= __('Mode') ?> <span>*</span></label>

                    <div class="col-xs-12 col-sm-10 col-md-8">
                        <?php
                        $options = array(
                            'Theory' => __('Theory'),
                            'Practical' => __('Practical')
                        );
                        if(Settings::instance()->get('todos_site_allow_oral_assignments') ) {
                            $options['Aural'] = __('Aural');
                            $options['Oral']  = __('Oral');
                        }
                        echo Form::btn_options('mode', $options, @$todo['mode'], false,
                            array('class' => 'validate[required]', 'id' => 'todo-mode'));
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12 col-sm-2"><?= __('Upload file') ?></div>

                    <div class="col-xs-12 col-sm-10">
                        <input type="hidden" name="file_uploads" value="0" />
                        <?php
                        echo Form::ib_checkbox_switch(null, 'file_uploads', 1, (bool) $todo_object->file_uploads);
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="todo-category"
                           class="col-sm-2 control-label text-left"><?= __('Category') ?></label>
                    <div class="col-sm-8">
                        <input type="hidden" name="category_id" value="<?= @$todo['category_id'] ?>" id="todo-category-id"/>
                        <?= Form::ib_input(null, 'category', @$todo['todo_category'],
                            array('id' => 'todo-category-search-autocomplete')) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="todo-summary"
                           class="col-sm-2 control-label text-left"><?= __('Summary') ?></label>
                    <div class="col-sm-8">
                        <?= Form::ib_textarea(null, 'summary', @$todo['summary'],
                            array('id' => 'todo-summary', 'rows' => 4)) ?>
                    </div>
                </div>
                
                <?php
                $column1_grid = 'col-xs-8 col-sm-10 col-md-9';
                $column2_grid = 'col-xs-4 col-sm-2  col-md-3';
                ?>

                <div class="todo-type-section<?= (isset($todo['type']) && (in_array($todo['type'], array("Task", "Class-Test", "Assignment", "Term-Assessment", "State-Exam", "Exam")))) ? '' : ' hidden' ?>" data-todo_type="Class-Test Assignment State-Exam Exam">
                    <div class="form-group vertically_center todo-type-section<?= (isset($todo['type']) && (in_array($todo['type'], array("Class-Test", "Assignment", "Term-Assessment", "State-Exam", "Exam")))) ? '' : ' hidden' ?>"
                         data-todo_type="Class-Test Assignment">
                        <div class="col-xs-12 col-sm-2"><?= __('Assign to a') ?></div>
                        <div class="col-xs-12 col-sm-10 col-md-8 assignee-selection">
                            <?php
                            $options = array(
                                'Group' => __('Group'),
                                'Person' => __('Person')
                            );
                            echo Form::btn_options('assignee-type', $options,
                                @$todo['assigned_contacts_type'] ?? "Group", false,
                                array('class' => 'validate[required]', 'id' => 'assignee-type'))
                            ?>
                        </div>
                    </div>

                    <?php
                    $assignee_section_visible = (
                        @$todo['assigned_contacts_type'] == "Group" ||
                        (isset($todo['type'])
                            && (in_array($todo['type'], ["Class-Test", "Assignment", "Term-Assessment", "State-Exam", "Exam"]))
                            && @$todo['assigned_contacts_type'] != "Person")
                    );
                    ?>

                    <div class="todo-type-assignee-section<?= $assignee_section_visible ? "" : " hidden" ?>" data-todo_assignee_type="Group">


                        <div class="form-group todo-type-assignee-section todo-type-section<?= $assignee_section_visible ? '' : ' hidden' ?>" id="todo-schedule-multiple-wrapper" data-todo_type="State-Exam" data-todo_assignee_type="Group">
                            <div class="col-md-2">
                                <label>Select schedules</label>
                            </div>

                            <div class="col-md-8">
                                <?php
                                $filters = ['publish' => '1'];
                                if (!Auth::instance()->has_access('courses_schedule_edit')) {
                                    $filters['trainer_id'] = Auth::instance()->get_contact()->id;
                                }
                                $schedules = Model_Schedules::search($filters);
                                $attributes = ['multiple' => 'multiple', 'id' => 'todo-group-schedule-search-multiselect'];
                                $args = [
                                    'multiselect_options' => [
                                        'enableCaseInsensitiveFiltering' => true,
                                        'enableFiltering' => true,
                                        'includeSelectAllOption' => true,
                                        'numberDisplayed' => 3,
                                        'selectAllText' => __('ALL')
                                    ]
                                ];

                                $options = '';
                                $has_schedule_ids = array_column($todo['has_schedules'], 'schedule_id');
                                foreach ($schedules as $schedule) {
                                    $options .= '<option
                                        value="'.$schedule['id'].'"'.
                                        (in_array($schedule['id'], $has_schedule_ids) ? 'selected' : '').
                                        '>#'.htmlspecialchars($schedule['id'].' - '.$schedule['name']). '</option>';
                                }
                                ?>

                                <?= Form::ib_select('Select schedules', 'has_schedules[]', $options, null, $attributes, $args); ?>
                            </div>
                        </div>


                        <div class="student-picker form-group vertically_center">
                            <label class="col-xs-12 col-sm-2" for="todo-mode"><?= __('Assignees') ?></label>
                            <div class="col-sm-8">

                                <?php
                                $contacts_selected_ids = array();
                                if (isset($todo['assigned_schedule_students'])) {
                                    $options = array();
                                    foreach ($todo['assigned_schedule_students'] as $schedule_student) {
                                        $options[$schedule_student['student_id']] = "{$schedule_student['student_id']} - {$schedule_student['first_name']} - {$schedule_student['last_name']}";
                                    }
                                    foreach ($todo['has_assigned_contacts'] as $assigned_contact) {
                                        $contacts_selected_ids[] = $assigned_contact['contact_id'];
                                    }
                                } else {
                                    $options = array("Select a schedule first");
                                }
                                $args = [
                                    'multiselect_options' => [
                                        'enableCaseInsensitiveFiltering' => true,
                                        'enableClickableOptGroups' => true,
                                        'enableFiltering' => true,
                                        'includeSelectAllOption' => true,
                                        'maxHeight' => 460,
                                        'numberDisplayed' => 1,
                                        'selectAllText' => __('ALL')
                                    ]
                                ];
                                echo Form::ib_select(__('Select assignees'), 'student-picker-multiselect[]', $options,
                                    $contacts_selected_ids,
                                    array('class' => 'multiple_select', 'multiple' => 'multiple'), $args);
                                ?>
                            </div>
                        </div>
                        <div class="examiner-picker form-group vertically_center">
                            <label class="col-xs-12 col-sm-2" for="todo-mode"><?= __('Examiners') ?></label>
                            <div class="col-sm-8">
                                <?php
                                $contacts_selected_ids = array();
                                $disable_group_examiners = @$todo['assigned_contacts_type'] == "Person";
                                $attributes = array('class' => 'multiple_select', 'multiple' => 'multiple');
                                if ($disable_group_examiners) {
                                    $attributes['disabled'] = 'disabled';
                                }
                                if (isset($todo['examiners'])) {
                                    $examiner_options = array();
                                    foreach ($todo['examiners'] as $examiner) {
                                        $examiner_options[$examiner['id']] = "{$examiner['id']} - {$examiner['first_name']} - {$examiner['last_name']}";
                                    }
                                    foreach ($todo['has_assigned_examiners'] as $assigned_examiner) {
                                        $examiners_selected_ids[] = $assigned_examiner['contact_id'];
                                    }
                                } else {
                                    $examiner_options = array("No possible examiners...");
                                }
                                $args = [
                                    'multiselect_options' => [
                                        'enableCaseInsensitiveFiltering' => true,
                                        'enableClickableOptGroups' => true,
                                        'enableFiltering' => true,
                                        'includeSelectAllOption' => true,
                                        'maxHeight' => 460,
                                        'numberDisplayed' => 1,
                                        'selectAllText' => __('ALL')
                                    ]
                                ];
                                echo Form::ib_select(__('Select examiners'), 'examiner-picker-multiselect[]', $examiner_options,
                                    $examiners_selected_ids, $attributes, $args);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="todo-type-section todo-type-assignee-section<?= (@$todo['assigned_contacts_type'] == "Person" || $todo['type'] == "Task") ? "" : " hidden" ?>"
                     data-todo_type="Task" data-todo_assignee_type="Person">
                    <?php
                    if ((isset($todo['has_assigned_contacts'][0]) &&
                        count($todo['has_assigned_contacts']) == 1)) {
                        $autocomplete_input_value = "{$todo['has_assigned_contacts'][0]['first_name']} {$todo['has_assigned_contacts'][0]['last_name']}";
                        $hidden_value = $todo['has_assigned_contacts'][0]['contact_id'];
                    }
                    $type = 'group-student';
                    $label = __('Assignee');
                    $placeholder = __('Search for an assignee');
                    $list = array();
                    $autocomplete_list = false;
                    include 'snippets/autocomplete.php';
                    ?>
                    <div class="examiner-picker form-group vertically_center">
                        <label class="col-xs-12 col-sm-2" for="todo-mode"><?= __('Examiners') ?></label>
                        <div class="col-sm-8">
                            <?php
                            $contacts_selected_ids = array();
                            if (isset($todo['examiners'])) {
                                $examiner_options = array();
                                foreach ($todo['examiners'] as $examiner) {
                                    $examiner_options[$examiner['id']] = "{$examiner['id']} - {$examiner['first_name']} - {$examiner['last_name']}";
                                }
                                foreach ($todo['has_assigned_examiners'] as $assigned_examiner) {
                                    $examiners_selected_ids[] = $assigned_examiner['contact_id'];
                                }
                            } else {
                                $examiner_options = array("No possible examiners...");
                            }
                            $args = [
                                'multiselect_options' => [
                                    'enableCaseInsensitiveFiltering' => true,
                                    'enableClickableOptGroups' => true,
                                    'enableFiltering' => true,
                                    'includeSelectAllOption' => true,
                                    'maxHeight' => 460,
                                    'numberDisplayed' => 1,
                                    'selectAllText' => __('ALL')
                                ]
                            ];
                            echo Form::ib_select(__('Select examiners'), 'examiner-picker-multiselect[]', $examiner_options,
                                $examiners_selected_ids,
                                array('class' => 'multiple_select', 'multiple' => 'multiple'), $args);
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group todo-type-section hidden" data-todo_type="Task">
                    <label class="col-sm-2" for="location_id">Regarding</label>
                    <div class="col-xs-12 col-sm-3 hidden">
                        <?php
                        $options = array();
                        foreach ($related_to_types as $related_to_type) {
                            $options[$related_to_type['id']] = $related_to_type['title'];
                        }
                        echo Form::ib_select(__('Regarding'), 'related_to_id', $options, @$todo['related_to_id'],
                            array('id' => 'related_to_id', 'class'=>'hidden'));
                        ?>
                    </div>
                    <div class="col-sm-5">
                        <input type="hidden" id="related_to_value" name="related_to_value" value="<?= @$todo['related_to_value'] ?>" />
                        <?= Form::ib_input(null, 'related_to_label', @$todo['related_to_label'],
                            array(
                                'id' => 'related_to',
                                'placeholder' => 'Type to select',
                                'class' => 'autocomplete'
                            )) ?>
                    </div>
                </div>
                <div class="form-group todo-type-section<?= (isset($todo['type']) && (in_array($todo['type'], array("Task", "Assignment")))) ? '' : ' hidden' ?>"
                     data-todo_type="Task Assignment">
                    <label class="col-sm-2" for="location_id">Status</label>

                    <div class="col-xs-12 col-sm-3">
                        <?php
                        $options = array(
                            'Open' => __('Open'),
                            'In progress' => __('In progress'),
                            'Done' => __('Done')
                        );
                        echo Form::ib_select(__('Status'), 'status', $options, @$todo['status'], ['id' => 'status_id']);
                        ?>
                    </div>
                </div>
                <!-- Priority -->
                <div class="form-group todo-type-section<?= (isset($todo['type']) && $todo['type'] == 'Task') ? '' : ' hidden' ?>"
                     data-todo_type="Task">
                    <label class="col-sm-2" for="location_id">Priority</label>

                    <div class="col-xs-12 col-sm-3">
                        <?php
                        $options = array(
                            'Normal' => __('Normal'),
                            'Low' => __('Low'),
                            'High' => __('High')
                        );
                        echo Form::ib_select(__('Priority'), 'priority', $options, @$todo['priority'],
                            array('id' => 'priority_id'));
                        ?>
                    </div>
                </div>
                <!-- Date -->
                <div>
                    <div class="form-group vertically_center">
                        <?php $date_label = in_array(@$todo['type'], array('Task', 'Exam')) || @$todo['delivery_mode'] == 'Online'
                                ? __('Due date') : __('Date'); ?>
                        <label class="col-sm-2" id="todo-date-level" for="todo-date"><?= $date_label ?></label>

                        <div class="col-sm-4 col-md-3">
                            <?php
                            $value = @$todo['datetime'] ? date('Y-m-d',
                                strtotime($todo['datetime'])) : date('Y-m-d');
                            $attributes = array(
                                'autocomplete' => 'off',
                                'class' => 'datetimepicker form-datepicker date',
                                'placeholder' => $date_label,
                                'id' => 'todo-date'
                            );
                            echo Form::ib_input(null, 'date', $value, $attributes,
                                array('right_icon' => '<span class="icon-calendar"></span>'));
                            ?>
                        </div>
                    </div>

                    <div class="form-group vertically_center">
                       
                            <label class="col-sm-2 <?= (in_array(@$todo['type'], array("Task", "Assignment"))) ? ' hidden ' : '' ?>" id="todo-time-start-label" for="todo-time_start"><?= __('Start time') ?></label>
    
                            <div class="col-sm-4 col-md-3 <?= (in_array(@$todo['type'],
                                array("Task", "Assignment")))  ? ' hidden ' : '' ?>">
                                <?php
                                $value = !empty($todo['datetime']) ? date('H:i', strtotime($todo['datetime'])) : '';
                                $attributes = array(
                                    'autocomplete' => 'off',
                                    'class' => 'datetimepicker time',
                                    'placeholder' => 'Start time',
                                    'id' => 'todo-time_start'
                                );
                                echo Form::ib_input(null, 'time_start', $value, $attributes,
                                    array('right_icon' => '<span class="icon-time"></span>'));
                                ?>
                            </div>
                        <label class="col-sm-2" for="todo-time_end"><?= __('End time') ?></label>

                        <div class="col-sm-4 col-md-3">
                            <?php
                            $value = @$todo['datetime_end'] ? date('H:i', strtotime($todo['datetime_end'])) : '';
                            $attributes = array(
                                'autocomplete' => 'off',
                                'class' => 'datetimepicker time',
                                'placeholder' => 'End time',
                                'id' => 'todo-time_end'
                            );
                            echo Form::ib_input(null, 'time_end', $value, $attributes,
                                array('right_icon' => '<span class="icon-time"></span>'));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="todo-type-section<?= (isset($todo['type']) && $todo['type'] == 'State-Exam') ? '' : ' hidden' ?>"
                     data-todo_type="State-Exam">
                    <div class="form-group vertically_center">
                        <label class="col-sm-2"><?= __('Academic year') ?></label>

                        <div class="col-sm-8">
                            <?php
                            $options = array();
                            foreach ($academic_years as $academic_year) {
                                $options[$academic_year['id']] = $academic_year['title'];
                            }
                            $selected = array();
                            if (@is_array($todo['has_academicyears'])) {
                                foreach ($todo['has_academicyears'] as $academicyear) {
                                    $selected[] = $academicyear['academicyear_id'];
                                }
                            }
                            
                            echo Form::ib_select(__('Select academic year'), 'academicyear_ids[]', $options, $selected,
                                array('multiple' => 'multiple'));
                            ?>
                        </div>
                    </div>
    
                    <?php
                    $type = 'subject';
                    $label = __('Subject(s)');
                    $list = array();
                    $column1_grid = "col-sm-8";
                    if (isset($todo['has_subjects'])) {
                        foreach ($todo['has_subjects'] as $key => $has_subject) {
                            $list[$key]['id'] = $has_subject['subject_id'];
                            $list[$key]['name'] = $has_subject['name'];
                        }
                    }
                    $autocomplete_list = true;
                    include 'snippets/autocomplete.php';
                    ?>

                </div>
                </div>

            <?php if(isset($todo['type']) && ($todo['type'] == 'State-Exam' || $todo['type'] == 'Exam')):?>

                <?php if(!Settings::instance()->get('todos_site_allow_online_exams')):?>
                <div id="todo_location" class="form-group vertically_center <?= (isset($todo['type']) && (!in_array($todo['type'],
                        array("Task", "Assignment")))) ? "" : " hidden" ?>">
                    <label class="col-sm-2" for="location_id">Location</label>

                    <div class="col-xs-12 col-sm-5">
                        <?php
                        $rooms = $locations;
                        $options = array();
                        foreach ($locations as $location) {
                            if ($location['parent_id'] == null) {
                                $options[$location['id']] = $location['name'];

                                foreach ($rooms as $room) {
                                    if ($room['parent_id'] == $location['id']) {
                                        $options[$room['id']] = $location['name'] . ', ' . $room['name'];
                                    }
                                }
                            }
                        }

                        echo Form::ib_select(__('Location'), 'location_id', $options, @$todo['location_id'],
                            array('id' => 'location_id'));
                        ?>
                    </div>
                </div>
                <?php endif?>
            <?php endif?>
            <?php if ($todo['type'] != "Task"): ?>
                <!-- results -->
                <div role="tabpanel" class="tab-pane" id="todo-results-tab">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            <?= __('Grading schema') ?>
                        </label>

                        <div class="col-sm-6">
                            <?php
                            $options = $grading_schemas->as_options(['selected' => @$todo['grading_schema_id']]);
                            echo Form::ib_select(null, 'grading_schema_id', $options, null, ['id' => 'todo-edit-schema_id']);
                            ?>
                        </div>
                    </div>
                    <?php if(Settings::instance()->get('todos_site_allow_online_exams') && isset($todo['type']) && ($todo['type'] == 'State-Exam' || $todo['type'] == 'Exam')):?>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?= __('Grading') ?></label>

                            <div class="col-xs-12 col-sm-10">
                            <?php $options = array(
                            'Automatic' => __('Automatic'),
                            'Manual' => __('Manual')
                            );
                            $value = 'Automatic';
                            if (@$todo['allow_manual_grading']) {
                                $value = 'Manual';
                            }
                            echo Form::btn_options('allow_manual_grading', $options, $value, false,
                            array('id' => 'allow-manual-grading'));
                            ?>
                            </div>
                        </div>
                    <?php endif?>

                    <div class="form-group vertically_center">
                        <label class="col-sm-2 control-label">
                            <?= __('Results published') ?>
                        </label>

                        <div class="col-sm-3">
                            <?php
                                $attributes = [
                                    'class' => 'results_published_datetime results_published_time',
                                    'id' => 'results_published_time',
                                ];
                                $date = ($todo['results_published_datetime'] !== null) ?
                                    date('Y-m-d', strtotime($todo['results_published_datetime'])) : null;
                                echo Form::ib_datepicker('Date', 'results_published_date',
                                    $date, $attributes);
                            ?>
                        </div>
                        <div class="col-sm-3">
                            <?php
                                $attributes = [
                                    'autocomplete' => 'off',
                                    'class' => 'form-timepicker results_published_datetime results_published_time',
                                    'id' => 'results_published_time',
                                ];
                                $time = ($todo['results_published_datetime'] !== null) ? date('H:i',
                                    strtotime($todo['results_published_datetime'])) : null;
                                echo Form::ib_input('Time', 'results_published_time', $time, $attributes);
                            ?>
                        </div>

                    </div>

                    <table class="table" id="todo-results"
                           data-student-index="<?= is_array(@$todo['results']) ? count($todo['results']) : 0 ?>">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('ID') ?></th>
                                <th scope="col"><?= __('Name') ?></th>
                                <th scope="col"><?= __('Subject') ?></th>
                                <th scope="col"><?= __('Level') ?></th>
                                <th scope="col"><?= __('Questions')?></th>
                                <th scope="col"><?= __('Marks')?></th>
                                <th scope="col" class="result" style="width: 7em;"><?= __('Result') ?></th>
                                <th scope="col" class="grade"  style="width: 7em;"><?= __('Grade')  ?></th>
                                <th scope="col" class="points" style="width: 7em;"><?= __('Points') ?></th>
                                <th scope="col"><?= __('Comment') ?></th>
                                <th scope="col"><?= __('View') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="todo-result hidden">
                                <td>
                                    <span class="student_id"></span>
                                    <input type="hidden" name="result[row_index][student_id]" class="student_id" />
                                    <span class="course"></span>
                                    <input type="hidden" name="result[row_index][schedule_id]" class="schedule_id"/>
                                    <input type="hidden" name="result[row_index][id]" class="result_id" />
                                </td>
                                <td><span class="student_name"></span></td>
                                <td>
                                    <select name="result[row_index][subject_id]" class="todo-edit-subject_id" readonly>
                                        <?= $subjects->as_options() ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="result[row_index][level_id]" class="todo-edit-level_id" readonly>
                                        <?= $levels->as_options(['name_column' => 'level']) ?>
                                    </select>
                                </td>
                                <td class="result">
                                    <input type="text" name="result[row_index][result]" class="result"/>
                                </td>
                                <td class="grade">
                                    <select name="result[row_index][grade]" class="grade">
                                        <option value=""><?= html::optionsFromRows('grade', 'grade', $grades, null) ?>
                                    </select>
                                </td>
                                <td class="points">
                                    <input type="text" name="result[row_index][points]" class="points"/>
                                </td>
                                <td><input type="text" name="result[row_index][comment]" class="comment"/></td>
                            </tr>
                            <?php if (is_array(@$todo['results'])): ?>
                                <?php foreach ($todo['results'] as $result_index => $result): ?>
                                    <?php $grade_points = $grading_schema->get_result($result); ?>

                                    <tr class="todo-result">
                                        <td>
                                            <span class="student_id"><?= $result['student_id'] ?></span>
                                            <input type="hidden" name="result[<?= $result_index ?>][student_id]" class="student_id" value="<?= $result['student_id'] ?>"/>
                                            <input type="hidden" name="result[<?= $result_index ?>][id]" class="result_id" value="<?= @$result['id'] ?>"/>
                                            <input type="hidden" name="result[<?= $result_index ?>][examiner_id]" class="examiner_id" value="<?= @$result['examiner_id'] ?>"/>

                                        </td>

                                        <td>
                                            <span class="student_name"><?= htmlspecialchars($result['first_name'] . ' ' . $result['last_name']) ?></span>
                                        </td>
                                        <td>
                                            <select name="result[<?= $result_index ?>][subject_id]" class="todo-edit-subject_id">
                                                <?= $subjects->as_options(['selected' => $result['subject_id']]) ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="result[<?= $result_index ?>][level_id]" class="todo-edit-level_id">
                                                <?= $levels->as_options(['name_column' => 'level', 'selected' => $result['level_id']]) ?>
                                            </select>
                                        </td>
                                        <td class="questions">
                                            <?=!empty($result['questions_answered'])
                                                ? $result['questions_answered'] . '/' . $result['questions'] :  '10/10' ?></td>

                                        <td class="marks">
                                            <?=!empty($result['marks_received'])
                                                ? $result['marks_received'] . '/' . $result['marks_available'] :  '10/10' ?></td>

                                        <td class="result">
                                            <input type="text"
                                                   name="result[<?= $result_index ?>][result]"
                                                   class="result" value="<?= $result['result'] ?>" />
                                        </td>

                                        <td class="grade">
                                            <select disabled="disabled" name="result[<?= $result_index ?>][grade]" class="grade">
                                                <option value=""></option>
                                                <?= html::optionsFromRows('grade', 'grade', $grades, $grade_points['grade']->grade) ?>
                                            </select>
                                        </td>
                                        <td class="points">
                                            <input disabled="disabled" type="text"
                                                   name="result[<?= $result_index ?>][points]"
                                                   class="points" value="<?= $grade_points['points'] ?>"/
                                        </td>
                                        <td>
                                            <input type="text" name="result[<?= $result_index ?>][comment]" class="comment"
                                                   value="<?= html::chars(@$result['comment']) ?>"/>
                                        </td>
                                        <td class="actions">
                                            <button type="button" class="btn-link p-0 w-100" data-toggle="collapse" data-target="#result_<?=$result['student_id']?>" aria-expanded="true">
                                                <span class="d-sm-none">View</span>
                                                <span class="expanded-invert icon-angle-down"></span>
                                            </button>
                                        </td>

                                    </tr>
                                    <tr class="collapse" id="result_<?=$result['student_id']?>">
                                        <td colspan="11">
                                            <?php
                                            $group_number = 0;
                                            include 'todos_questionnaire_response_group.php';?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <!-- permissions -->
            <div role="tabpanel" class="tab-pane" id="todo-permissions-tab">
                <h2><?= __('Share With') ?></h2>

                <div class="form-group">
                    <label class="col-xs-2"><?= __('Favourite') ?></label>

                    <div class="col-xs-10">
                        <label class="checkbox-icon">
                            <input type="checkbox" name="favorite"
                                   value="1" <?= !empty($todo['is_favorite']) ? ' checked="checked"' : '' ?>/>
                            <span class="checkbox-icon-unchecked icon-star-o"></span>
                            <span class="checkbox-icon-checked icon-star"></span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2"><?= __('Shares') ?></div>

                    <div class="col-sm-10">
                        <table id="permissions-table">
                            <tbody>
                            <tr class="role hidden">
                                <td><span class="role"></span><input type="hidden" name="role_id[]" class="role_id"/>
                                </td>
                                <td>&nbsp;<button type="button" class="btn-link delete" title="<?= __('Remove') ?>">
                                        <span class="icon-trash"></span></button>
                                </td>
                            </tr>
                            <?php if (is_array(@$todo['permissions'])) {
                                foreach ($todo['permissions'] as $permission) { ?>
                                    <tr class="role">
                                        <td><span class="role"><?= $permission['role'] ?></span><input type="hidden"
                                                                                                       name="role_id[]"
                                                                                                       class="role_id"
                                                                                                       value="<?= $permission['role_id'] ?>"/>
                                        </td>
                                        <td>&nbsp;<button type="button" class="btn-link delete"
                                                          title="<?= __('Remove') ?>"><span class="icon-trash"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2" for="permision-roles"><?= __('Add Share') ?></label>

                    <div class="col-sm-4">
                        <?php
                        $options = array('' => __('Everyone'));
                        foreach (Model_Roles::get_all() as $role) {
                            $options[$role['id']] = $role['role'];
                        }
                        echo Form::ib_select(null, null, $options, null, array('id' => 'permission-roles'));
                        ?>
                    </div>

                    <div class="col-sm-4">
                        <button type="button" class="btn form-btn" id="add-permission"><?= __('Add') ?></button>
                    </div>
                </div>
            </div>
            <?php if (in_array($todo['type'], array('State-Exam', 'Exam', 'Class-Test', 'Term-Assessment', 'Assignment')) && Auth::instance()->has_access('todos_content_tab')): ?>
                <!-- content -->
                <div role="tabpanel" class="tab-pane" id="todo-content-tab">
                    <?= $todo['content']->render_editor(['edit_button_at_depth' => 2]) ?>
                </div>
            <?php endif; ?>

            <div role="tabpanel" class="tab-pane" id="todo-activity-tab">
                <table class="table table-striped dataTable">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Assignee</th>
                            <th scope="col">Status</th>
                            <?php if ($todo_object->file_uploads): ?>
                                <th scope="col">File</th>
                                <th scope="col">File size</th>
                            <?php endif; ?>
                            <th scope="col">Created</th>
                            <th scope="col">Updated</th>
                            <?php if ($todo_object->file_uploads): ?>
                                <th scope="col">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($todo_object->has_assignees->find_all() as $has_assignee): ?>
                            <tr>
                                <td><?= $has_assignee->id ?></td>
                                <td><?= htmlentities($has_assignee->assignee->get_full_name()) ?></td>
                                <td><?= htmlentities($has_assignee->status) ?></td>
                                <?php if ($todo_object->file_uploads): ?>
                                    <td><?= htmlentities($has_assignee->get_last_file()->media->filename) ?></td>
                                    <td><?= $has_assignee->get_last_file()->media->format_size() ?></td>
                                <?php endif; ?>
                                <td><?= IbHelpers::relative_time_with_tooltip($has_assignee->get_first_file()->date_created) ?></td>
                                <td><?= IbHelpers::relative_time_with_tooltip($has_assignee->get_last_file()->date_modified) ?></td>
                                <?php if ($todo_object->file_uploads): ?>
                                    <td>
                                        <?php
                                        $file = $has_assignee->get_last_file()->media;

                                        if ($file->id && $file->get_url()) {
                                            echo View::factory('snippets/btn_dropdown')
                                                ->set('type', 'actions')
                                                ->set('options', [
                                                    ['type' => 'link', 'title' => 'Download', 'attributes' => ['href' => $file->get_url(), 'download' => $file->filename]]
                                                ])
                                                ->render();
                                        }
                                        ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <input type="hidden" id="id" name="id" value="<?= @$todo['id'] ?>"/>

        <div class="form-action-group text-center">
            <button type="submit" class="btn btn-primary save_button" name="action" value="save">Save</button>
        </div>
    </form>
</div>

<div class="modal fade" id="location-add-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>New Location</h3>
            </div>
            <div class="modal-body">
                <?= View::factory(
                    'form_location',
                    array(
                        'locations' => Model_Locations::get_locations_without_parent(),
                        'counties' => Model_Cities::get_counties(),
                        'types' => Model_Locations::get_location_types(),
                        'rows' => array(),
                        'data' => array(),
                        'modal' => 1
                    )
                ) ?>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

