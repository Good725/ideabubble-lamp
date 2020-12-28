<?= (isset($alert)) ? $alert : '' ?>
<?php
if (isset($alert)) {
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>

<div>
    <form class="form-horizontal validate-on-submit" id="todo-edit-form" name="todo-edit-form"
          method="post">
        <div class="form-group clearfix">
            <div class="col-xs-12">
                <?= Form::ib_input(__("{$todo['todo_type_label']} title"), 'title', @$todo['title'],
                    array('class' => 'validate[required]', 'id' => 'todo-title', 'disabled' => 'disabled')) ?>
            </div>
        </div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#todo-details-tab" aria-controls="todo-details-tab" role="tab"
                   data-toggle="tab"><?= __('Details') ?></a>
            </li>

            <?php if (!empty($content) && $content->id): ?>
                <li role="presentation">
                    <a href="#todo-material-tab" aria-controls="todo-material-tab" role="tab"
                       data-toggle="tab"><?= __('Content') ?></a>
                </li>
            <?php endif; ?>
            <?php if(@$todo['file_uploads']):?>
            <li role="presentation">
                <a href="#todo-files-tab" aria-controls="todo-files-tab" role="tab" data-toggle="tab">
                    <?= __('Files') ?>
                </a>
            </li>
            <?php endif?>

            <?php //removing my results tab for now, it will show only the user logged in results which they can access elsewhere
            if (false) : ?>
                <li role="presentation" class="">
                    <a href="#todo-results-tab" aria-controls="todo-results-tab" role="tab"
                       data-toggle="tab"><?= __('My results') ?></a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content">
            <!-- details -->
            <div role="tabpanel" class="tab-pane active" id="todo-details-tab">

                <div class="form-group vertically_center">
                    <div class="col-xs-12 col-sm-2"><?= __('Type') ?></div>

                    <div class="col-xs-12 col-sm-10 col-md-8">
                        <?php
                        echo Form::ib_input(null, 'type', @$todo['todo_type_label'],
                            array('disabled' => 'disabled', 'id' => 'todo-type'))
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="todo-summary"
                           class="col-sm-2 control-label text-left"><?= __('Summary') ?></label>
                    <div class="col-sm-8">
                        <?= Form::ib_textarea(null, 'summary', @$todo['summary'],
                            array('id' => 'todo-summary', 'rows' => 4, 'disabled' => 'disabled')) ?>
                    </div>
                </div>
                <?php if(Auth::instance()->has_access('todos_list')) : ?>
                    <div class="form-group todo-type-section<?= (isset($todo['type']) && (in_array($todo['type'],
                            array("Task")))) ? '' : ' hidden' ?>"
                         data-todo_type="Task Assignment">
                        <label class="col-sm-2" for="location_id">Change assignee</label>
    
                        <div class="col-xs-12 col-sm-3">
                            <?php
                            $assignees_select = array();
                            foreach ($todo['has_assigned_contacts'] as $assignee) {
                                $assignees_select[$assignee['contact_id']] = "{$assignee['first_name']} {$assignee['last_name']}";
                            }
                            $assignees_select[Model_Contacts3::get_linked_contact_to_user($todo['created_by'])['id']] = $todo['author_name'];
                            echo Form::ib_select(null, 'assignee_id', $assignees_select, @$todo['has_assigned_contacts'][0]['contact_id'],
                                array('id' => 'assignee_id'));
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                $column1_grid = 'col-xs-8 col-sm-10 col-md-9';
                $column2_grid = 'col-xs-4 col-sm-2  col-md-3';
                ?>

                <div class="form-group todo-type-section<?= (isset($todo['type']) && (in_array($todo['type'],
                        array("Task", "Assignment")))) ? '' : ' hidden' ?>"
                     data-todo_type="Task Assignment">
                    <label class="col-sm-2" for="location_id">Status</label>

                    <div class="col-xs-12 col-sm-3">
                        <?php
                        $select_status = ['Open' => 'Open', 'In progress' => 'In progress', 'Done' => 'Done'];
                        echo Form::ib_select(null, 'status', $select_status, $todo_has_assignee->status,
                            array('id' => 'status_id'));
                        ?>
                    </div>
                </div>
                <div class="form-group todo-type-section<?= (isset($todo['type']) && ($todo['type'] == "Task")) ? '' : ' hidden' ?>"
                     data-todo_type="Task">
                    <label class="col-sm-2" for="location_id">Regarding</label>
                    <div class="col-xs-12 col-sm-3">
                        <?php
                        echo Form::ib_input(null, 'related_to_list', $todo['related_title'] ?? "",
                            array('id' => 'related_to_list', 'disabled', 'disabled'));
                        ?>
                    </div>
                    <div class="col-sm-5">
                        <?= Form::ib_input(null, 'related_to', $todo['related_to_label'] ?? "",
                            array('id' => 'related_to', 'placeholder' => 'Type to select', 'disabled', 'disabled')) ?>
                    </div>
                </div>
                <!-- Priority -->
                <div class="form-group todo-type-section<?= (isset($todo['type']) && $todo['type'] == 'Task') ? '' : ' hidden' ?>"
                     data-todo_type="Task">
                    <label class="col-sm-2" for="location_id">Priority</label>

                    <div class="col-xs-12 col-sm-3">
                        <?php
                        echo Form::ib_input(null, 'priority', "",
                            array('id' => 'priority_id', 'disabled' => 'disabled'));
                        ?>
                    </div>
                </div>
                <!-- Date -->
                <div>
                    <div class="form-group vertically_center">
                        <?php $date_label = in_array(@$todo['type'], array('Task', 'Exam'))
                            ? __('Due date') : __('Date'); ?>
                        <label class="col-sm-2" for="todo-date"><?= $date_label ?></label>

                        <div class="col-sm-4 col-md-3">
                            <?php
                            $value = @$todo['datetime'] ? date('Y-m-d',
                                strtotime($todo['datetime'])) : date('Y-m-d');
                            $attributes = array(
                                'autocomplete' => 'off',
                                'class' => 'datetimepicker form-datepicker date',
                                'placeholder' => $date_label,
                                'id' => 'todo-date',
                                'disabled' => 'disabled'
                            );
                            echo Form::ib_input(null, 'date', $value, $attributes,
                                array('disabled' => 'disabled', 'right_icon' => '<span class="icon-calendar"></span>'));
                            ?>
                        </div>
                    </div>

                    <div class="form-group vertically_center">
                        <label class="col-sm-2 <?= (in_array(@$todo['type'],
                            array("Task", "Assignment"))) ? ' hidden ' : '' ?>" for="todo-time_start"><?= __('Start time') ?></label>

                        <div class="col-sm-4 col-md-3 <?= (in_array(@$todo['type'],
                            array("Task", "Assignment"))) ? ' hidden ' : '' ?>">
                            <?php
                            $value = !empty($todo['datetime']) ? date('H:i', strtotime($todo['datetime'])) : '';
                            $attributes = array(
                                'autocomplete' => 'off',
                                'class' => 'datetimepicker time',
                                'id' => 'todo-time_start',
                                'disabled' => 'disabled'
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
                                'id' => 'todo-time_end',
                                'disabled' => 'disabled'
                            );
                            echo Form::ib_input(null, 'time_end', $value, $attributes,
                                array('right_icon' => '<span class="icon-time"></span>'));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="todo-type-section form-group vertically_center <?= (isset($todo['type']) && (in_array($todo['type'],
                        array("Class-Test", "Assignment")))) ? "" : " hidden" ?>"
                     data-todo_type="Class-Test Assignment">
                    <label class="col-xs-12 col-sm-2" for="todo-mode"><?= __('Mode') ?></label>

                    <div class="col-xs-12 col-sm-10 col-md-8">
                        <?= Form::ib_input(null, 'todo-mode', @$todo['mode'],
                            array('disabled' => 'disabled', 'class' => 'validate[required]', 'id' => 'todo-mode'));
                        ?>
                    </div>
                </div>

                <div class="todo-type-section<?= (isset($todo['type']) && $todo['type'] == 'State-Exam') ? '' : ' hidden' ?>"
                     data-todo_type="State-Exam">
                    <div class="form-group vertically_center">
                        <label class="col-sm-2"><?= __('Academic year') ?></label>

                        <div class="col-sm-8">
                            <?php
                            
                            //                        echo Form::ib_input(__('Academic year'), 'academicyear_ids[]', $options);
                            ?>
                        </div>
                    </div>
                    
                    <?php
                    $type = 'subject';
                    $label = __('Subject(s)');
                    $list = array();
                    if (isset($todo['has_subjects'])) {
                        foreach ($todo['has_subjects'] as $key => $has_subject) {
                            $list[$key]['id'] = $has_subject['subject_id'];
                            $list[$key]['name'] = $has_subject['name'];
                        }
                    }
                    $autocomplete_list = true;
                    $disabled = true;
                    include 'snippets/autocomplete.php';
                    ?>

                </div>
                <!-- location -->
                <div class="form-group vertically_center <?= (isset($todo['type']) && (!in_array($todo['type'],
                        array("Task", "Assignment")))) ? "" : " hidden" ?>">
                    <label class="col-sm-2" for="location_id">Location</label>

                    <div class="col-xs-12 col-sm-5">
                        <?php
                        
                        echo Form::ib_input(__('Location'), 'location_id', $location['name'],
                            array('id' => 'location_id', 'disabled' => 'disabled'));
                        ?>
                    </div>
                </div>
            </div>


            <?php if (!empty($content) && $content->id): ?>
                <!-- content -->
                <div role="tabpanel" class="tab-pane" id="todo-material-tab">
                    <?php
                    echo View::factory('/admin/my_content')
                        ->set('allow_skipping', $content->allow_skipping)
                        ->set('content', $content)
                        ->set('open_section', 0);
                    ?>
                </div>
            <?php endif; ?>
            <?php if(@$todo['file_uploads']):?>
                <div role="tabpanel" class="tab-pane" id="todo-files-tab">
                <table class="table table-striped dataTable">
                    <thead>
                        <tr>
                            <th scope="col">File name</th>
                            <th scope="col">Created</th>
                            <th scope="col">Size</th>
                            <th scope="col">Version</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?= htmlspecialchars($submission->media->filename) ?></td>
                                <td><?= IbHelpers::relative_time_with_tooltip($submission->date_created) ?></td>
                                <td><?= $submission->media->format_size() ?></td>
                                <td><?= $submission->version ?></td>
                                <td>
                                    <?php
                                    $file = $submission->media;
                                    echo View::factory('snippets/btn_dropdown')
                                        ->set('type', 'actions')
                                        ->set('options', [
                                            ['type' => 'link', 'title' => 'Download', 'attributes' => ['href' => $file->get_url(), 'download' => $file->filename]]
                                        ])
                                        ->render();
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="row gutters">
                    <div class="col-sm-offset-1 col-sm-10">
                        <input type="hidden" name="file_id" id="todo-edit-form-file_id" />

                        <?php
                        $form = new IbForm('todo-edit-form', '#', 'post', ['layout' => 'vertical']);
                        $uploader_args = [
                            'browse_directory' => false,
                            'duplicate'   => 0,
                            'onsuccess'   => 'todo_file_uploaded',
                            'presetmodal' => 'no',
                            'single'      => true
                        ];
                        echo $form->image_uploader('Upload your file', null, null, [], $uploader_args);
                        ?>
                    </div>
                </div>
            </div>
            <?php endif?>
            <!-- results -->
            <?php if (false): // See above at the tab declaration for why this has been removed ?>
            <div role="tabpanel" class="tab-pane" id="todo-results-tab">
                <table class="table" id="todo-results"
                       data-student-index="<?= is_array(@$todo['logged_in_contact_results']) ? count($todo['logged_in_contact_results']) : 0 ?>">
                    <thead>
                        <tr>
                            <th scope="col"><?= __('Name') ?></th>
                            <th scope="col"><?= __('Schedule') ?></th>
                            <th scope="col" class="result" style="width: 10%;"><?= __('Result') ?></th>
                            <th scope="col" class="questions"><?=__('Questions')?></th>
                            <th scope="col" class="grade"><?= __('Grade') ?></th>
                            <th scope="col" class="points"><?= __('Points') ?></th>
                            <th scope="col"><?= __('Comment') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="todo-result hidden">
                            <td><span class="student_name"></span></td>
                            <td>
                                <span class="course"></span>
                                <input type="hidden" name="result[row_index][schedule_id]" class="schedule_id"/>
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
                            <td>
                                <input type="text" name="result[row_index][comment]" class="comment"/>
                            </td>
                        </tr>
                    <?php
                    foreach ($todo['logged_in_contact_results'] as $result_index => $result) {
                        $grade = Model_Todos::get_grade_from_percent($result['result'], $grades, false);
                        $points = $result['points'];
                    ?>
                        <tr class="todo-result">
                            <td>
                                <span class="student_name"><?= $result['first_name'] . ' ' . $result['last_name'] ?></span>
                            </td>
                            <td class="result">
                                <span><?= $result['result'] ?></span></td>
                            <td class="questions"><?=!empty($result['questions_answered'])
                                    ? $result['questions_answered'] . '/' . $result['questions'] :  '10/10' ?></td>
                            <td class="grade">
                                <span><?= $grade['grade'] ?></span></td>
                            <td class="points"><span><?= $points ?></span></td>
                            <td class="comment"><span><?= html::chars(@$result['comment']) ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <input type="hidden" id="id" name="id" value="<?= @$todo['id'] ?>"/>
            
            <div class="form-action-group text-center <?= ((in_array(@$todo['type'],
                    array("Task", "Assignment")))) ? '' : ' hidden' ?>"">
                <button type="submit" class="btn btn-primary save_button" name="action" value="save">Save</button>
            </div>
    </form>
</div>

<style>
    <?php // temporary ?>
    .uploaded_image > a[href*=".pdf"], .uploaded_image > a[href*=".doc"] {display: none;}
</style>

<script>
    function todo_file_uploaded(filename, filepath, data)
    {
        console.log(data);
        $('#todo-edit-form-file_id').val(data.media_id);
    }
</script>