<?php
$depth = isset($depth) ? $depth : 1;
$edit_button_at_depth = isset($edit_button_at_depth) ? $edit_button_at_depth : 2;
$full_form = !empty($args['full_form']);

if ($full_form) {
    $form = new IbForm('content-edit', '/admin/content/save/'.$content->id);
    $form->name_field = 'name';
    $form->cancel_url = '/admin/content/';
//    $form->delete_url = '/admin/content/delete/'.$content->id;
    $form->load_data($content);

    echo '<div class="alert_area">' . (isset($alert) ? $alert : '') . '</div>';

    echo $form->start();
}
?>

<style>
    .content-topics-list {
        counter-reset: section;
    }

    .content-topic-name:before {
        content: attr(data-section_label) ' ' counter(section) ': ';
        counter-increment: section;
    }

    .content-topics-list .content-topics-list .content-topic-name:before {
        content: attr(data-section_label) ' ' counters(section, '.') ': '
    }

    <?php // Only make these buttons visible from the desired depth downwards ?>
    .content-topic-add-modal-trigger { visibility: hidden; }
    .content-topic-details { display: none; }
    <?php
    $selector = '';
    for ($i = 0; $i < $edit_button_at_depth; $i++) {
        $selector .= ' .content-topics-list';
    }
    ?>
    <?= $selector ?> .content-topic-add-modal-trigger { visibility: visible;}
    <?= $selector ?> .content-topic-details { display: inline-block; }

</style>

<div id="content-alert_area"></div>

<div id="content-tree" data-edit_button_depth="<?= isset($edit_button_at_depth) ? $edit_button_at_depth : 2 ?>">

    <div class="panel panel-default" style="width: calc(100% - 2em);">
        <label class="panel-heading w-100" style="cursor: pointer;">
            <h4 class="panel-title">
                <span class="icon-cog"></span>
                Settings

                <button type="button" class="btn-link right p-0 border-0" data-toggle="collapse" data-target="#content-tree-settings">
                    <span class="expanded-invert">
                        <span class="icon-angle-up"></span>
                    </span>
                </button>
            </h4>
        </label>

        <div id="content-tree-settings" class="panel-collapse collapse">
            <div class="panel-body">
                <?php if (isset($args) && !empty($args['schedule_id'])): ?>
                    <div class="mb-3">
                        <div>Preview</div>
                        <a href="/admin/courses/my_course/<?= $args['schedule_id'] ?>" target="_blank">
                            <?= URL::base() ?>admin/courses/my_course/<?= $args['schedule_id'] ?>
                        </a>
                    </div>

                    <div class="row gutters mb-3">
                        <div class="col-sm-12">
                            <label for="content-label">Available from</label>
                        </div>

                        <div class="col-sm-6">
                            <?= Form::ib_input('Days before schedule starts', 'available_days_before', $content->available_days_before, ['type' => 'number', 'id' => 'content-available_days_before']) ?>
                        </div>

                        <div class="col-sm-6">
                            <?= Form::ib_input('Days after schedule ends', 'available_days_after', $content->available_days_after, ['type' => 'number', 'id' => 'content-available_days_after']) ?>
                        </div>

                    </div>

                <?php endif; ?>
                <h4>Content</h4>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label for="content-label">Section labels</label>

                        <?= Form::ib_input(null, null, $content->label, ['id' => 'content-label', 'placeholder' => 'e.g. Chapter']) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <label for="content-allow_skipping">Allow skipping</label>

                        <div>
                            <?= Form::ib_checkbox_switch(null, 'allow_skipping', 1, (bool) $content->allow_skipping , ['id' => 'content-allow_skipping']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label">Only display content between these dates</label>
                    </div>
                    <div class="row" style="padding-left: 0;">
                        <div class="col-xs-10 col-sm-6">
                        <?php
                        $hidden_attributes = ['id' => 'content-add-modal-available_from'];
                        $display_attributes = ['placeholder' => 'Start date'];
                        $args = ['icon' => '<span class="icon-calendar"></span>'];
                        echo Form::ib_datepicker(null, null, null, $hidden_attributes, $display_attributes, $args);
                        ?>
                        </div>
                        <div class="col-xs-2 col-sm-3">
                                <?php
                                $value = !empty($todo['datetime']) ? date('H:i', strtotime($todo['datetime'])) : '';
                                $attributes = array(
                                    'autocomplete' => 'off',
                                    'class' => 'datetimepicker time',
                                    'placeholder' => 'Start time',
                                    'id' => 'content-time_start'
                                );
                                echo Form::ib_input(null, 'content[time_start]', $value, $attributes,
                                    array('right_icon' => '<span class="icon-time"></span>'));
                                ?>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 0;">
                        <div class="col-xs-10 col-sm-6">
                        <?php
                        $hidden_attributes = ['id' => 'content-add-modal-available_to'];
                        $display_attributes = ['placeholder' => 'End date'];
                        $args = ['icon' => '<span class="icon-calendar"></span>'];
                        echo Form::ib_datepicker(null, null, null, $hidden_attributes, $display_attributes, $args);
                        ?>
                        </div>
                        <div class="col-xs-2 col-sm-3">
                            <?php
                            $value = !empty($todo['datetime']) ? date('H:i', strtotime($todo['datetime'])) : '';
                            $attributes = array(
                                'autocomplete' => 'off',
                                'class' => 'datetimepicker time',
                                'placeholder' => 'End time',
                                'id' => 'content-time_end'
                            );
                            echo Form::ib_input(null, 'content[time_end]', $value, $attributes,
                                array('right_icon' => '<span class="icon-time"></span>'));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label">Only display content in this range</label>
                    </div>

                    <div class="col-sm-6">
                        <?php
                        $attributes = ['id' => 'content-add-modal-available_days_before'];
                        echo Form::ib_input('Days before schedule starts', null, null, $attributes);
                        ?>
                    </div>

                    <div class="col-sm-6">
                        <?php
                        $attributes = ['id' => 'content-add-modal-available_days_after'];
                        echo Form::ib_input('Days after schedule ends', null, null, $attributes);
                        ?>
                    </div>
                </div>
                <h4><?=__('Questionnaire')?></h4>
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label text-left" for="content-add-modal-has_survey">Assessment</label>
                        <?= Form::ib_checkbox_switch(null, null, 1, false, ['id' => 'content-add-modal-has_survey']) ?>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        $options = html::optionsFromRows('id', 'title', $surveys, '', ['value' => '', 'label' => 'Please select']);
                        echo Form::ib_select(null, null, $options, null, ['id' => 'content-add-modal-survey']) ?>
                    </div>
                </div>
                <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label text-left" for="content-shuffle-questions">Shuffle Question Order</label>
                            <div>
                                <?= Form::ib_checkbox_switch(null, 'shuffle_questions', 1, false, ['id' => 'content-shuffle-questions']) ?>
                            </div>
                        </div>
                    <div class="col-sm-6">
                            <label class="control-label text-left" for="content-shuffle-questions">Shuffle Group Order</label>
                            <div>
                                <?= Form::ib_checkbox_switch(null, 'shuffle_groups', 1, false, ['id' => 'content-shuffle-groups']) ?>
                            </div>
                        </div>
                </div>
                <div class="row">
                     <div class="col-sm-6">
                            <label class="control-label text-left" for="content-add-modal-has_survey">Respondent can see marks</label>
                             <?php
                            echo Form::ib_radio('Immediately after each submission', 'questionnaire[respondent_marks]', 'immediately', true, array('class' => 'questionnaire_marks', 'id' => 'content-release-mark_immediately'));
                            echo Form::ib_radio('Later after review', 'questionnaire[respondent_marks]', 'after_review', false, array('class' => 'questionnaire_marks', 'id'=>'content-release-mark_after_review'));
                            ?>
                            </div>

                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label class="control-label text-left" for="respondent_can_see">Respondent can see</label>
                        <br/>
                        <ul class="list-unstyled">
                        <?php $options = [
                            'missed' => 'Missed questions',
                            'correct' => 'Correct answers',
                            'points' => 'Point values'];

                        foreach($options as $option_id => $option) {
                            echo '<li>' . Form::ib_checkbox($option, 'respondent_can_see[]', $option_id, NULL, array('class' => 'validate[required]', 'id' => 'respondent_can_see_' . $option_id)) . '</li>';
                        }
                        ?>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label class="control-label text-left" for="content-add-modal-has_survey">No of attempts</label>
                        <?php
                            $attributes = ['id' => 'content-max-attempts'];
                            $options = array(
                                    'unlimited' => 'Unlimited',
                               );
                            for ($i = 1; $i <= 10; $i++){
                                $options[$i] = $i;
                            }
                            echo Form::ib_select(null, 'questionnaire[max_attempts]', $options, null, $attributes);
                            ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                            <label class="control-label text-left" for="content-show-timer">Show Timer</label>
                            <?= Form::ib_checkbox_switch(null, null, 1, false, ['id' => 'content-show-timer']) ?>
                        </div>

                </div>
                <div class="row">
                    <div class="col-sm-6">
                            <label class="control-label text-left" for="content-show-progress">Show Progress</label>
                            <?= Form::ib_checkbox_switch(null, null, 1, false, ['id' => 'content-show-progress']) ?>
                        </div>

                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label class="control-label text-left" for="content-allow-late-submissions">Late Submissions</label>
                        <?= Form::ib_checkbox_switch(null, null, 1, false, ['id' => 'content-allow-late-submissions']) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <ul class="content-topics-list">
        <?php
        if ($content->id) {
            foreach ($content->children->order_by('order')->find_all_undeleted() as $topic) {
                echo View::factory('admin/snippets/content_tree_topic')
                    ->set('label', $content->label)
                    ->set('topic', $topic)
                    ->set('depth', $depth)
                    ->render();
            }
        }
        ?>
    </ul>

    <div class="content-topic-add-form row gutters mt-2" style="padding-right: 2em;">
        <input type="hidden" class="content-topic-add-parent_id" name="<?= !empty($args['id_field']) ? $args['id_field'] : 'content_id'?>" value="<?= $content->id ?>" id="content-master_id" />
        <input type="hidden" class="content-topic-add-depth" value="<?= $depth-1 ?>" />

        <div class="col-sm-4">
            <?= Form::ib_input(null, null, null, ['class' => 'content-topic-add-name', 'placeholder' => 'Topic name']) ?>
        </div>

        <div class="col-sm-4 right text-right">
            <button type="button" class="content-topic-add-btn btn btn-lg btn-default">Add topic</button>
        </div>
    </div>
</div>

<?php ob_start() ?>
    <button type="button" class="btn btn-lg btn-danger" id="content-delete-modal-confirm">Delete</button>
    <button type="button" class="btn btn-lg btn-cancel" data-dismiss="modal" style="box-shadow: none;">Cancel</button>
<?php $delete_modal_buttons = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')
    ->set('id',     'content-delete-modal')
    ->set('title',  'Confirm delete')
    ->set('body',   '<p>Are you sure you want to delete this topic and all of its sub topics?</p>')
    ->set('footer', $delete_modal_buttons);
?>

<?php ob_start() ?>
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-sm-12">
                <label class="control-label"for="content-add-modal-name">Name</label>

                <?= Form::ib_input(null, null, null, ['placeholder' => 'Enter name', 'id' => 'content-add-modal-name']) ?>
            </div>
        </div>

        <div class="form-group mb-0">
            <div class="col-sm-12">
                <label class="mb-2 control-label">Type</label>

                <ul class="list-inline" id="content-add-modal-types">
                    <?php foreach ($types as $type): ?>
                        <li class="mr-3">
                            <?php
                            $checked = ($type->name == 'text');
                            $attributes = ['class' => 'content-add-modal-type', 'data-type' => $type->name];
                            echo Form::ib_radio($type->friendly_name, 'content_type_id', $type->id, $checked, $attributes);
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="form-group hidden hidden--audio hidden--questionnaire hidden--text hidden--video">
            <div class="col-sm-12">
                <?= View::factory('multiple_upload',
                    array(
                        'browse_directory' => 'docs',
                        'directory'        => 'docs',
                        'duplicate'        => 0,
                        'include_js'       => true,
                        'name'             => 'content_pdf',
                        'onsuccess'        => 'add_content_file',
                        'single'           => true
                    )
                ) ?>
            </div>
        </div>

        <div class="form-group hidden hidden--pdf hidden--questionnaire hidden--text hidden--video">
            <div class="col-sm-12">
                <?= View::factory('multiple_upload',
                    array(
                        'browse_directory' => 'audios',
                        'directory'        => 'audios',
                        'duplicate'        => 0,
                        'include_js'       => false,
                        'name'             => 'content_audio',
                        'onsuccess'        => 'add_content_file',
                        'single'           => true
                    )
                ) ?>
            </div>
        </div>

        <div class="form-group hidden hidden--audio hidden--pdf hidden--questionnaire hidden--text">
            <div class="col-sm-12">
                <?= View::factory('multiple_upload',
                    array(
                        'browse_directory' => 'videos',
                        'directory'        => 'videos',
                        'duplicate'        => 0,
                        'include_js'       => false,
                        'name'             => 'content_video',
                        'onsuccess'        => 'add_content_file',
                        'single'           => true
                    )
                ) ?>

            </div>
        </div>

        <input type="hidden" id="content-add-modal-file_id" />
        <input type="hidden" id="content-add-modal-file_url_hidden" />

        <div class="form-group hidden hidden--questionnaire hidden--text">
            <div class="col-sm-12">
                <label class="control-label text-left" for="content-add-modal-file_url">Link to file</label>
            </div>

            <div class="col-sm-9">
                <?php
                $attributes = ['class' => 'content-add-modal-autosave', 'id' => 'content-add-modal-file_url'];
                echo Form::ib_input(null, null, null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group hidden hidden--audio hidden--pdf hidden--questionnaire hidden--video">
            <div class="col-sm-12">
                <label class="control-label text-left" for="content-add-modal-text">Text</label>

                <?php
                $attributes = ['class' => 'content-add-modal-autosave ckeditor', 'id' => 'content-add-modal-text'];
                echo Form::ib_textarea(null, null, null, $attributes);
                ?>
            </div>
        </div>

        <div class="form-group hidden hidden--audio hidden--pdf hidden--text hidden--video px-2">
            <input type="hidden" name="survey_id" value="<?= $content->survey_id ? $content->survey_id : '' ?>"
                id="content-add-modal-survey_id" />
                    <div id="content-add-modal-survey-wrapper">
                        <?= View::factory('questionnaire_builder'); ?>
                    </div>
        </div>

        <div class="form-group">
            <?php
            /* Parked until auto-reordering of divs has been set up.
            <div class="col-sm-6">
                <label class="control-label" for="content-add-modal-order">Order</label>

                <?= Form::ib_input(null, null, null, ['type' => 'number', 'id' => 'content-add-modal-order']) ?>
            </div>
            */
            ?>

            <div class="col-sm-6">
                <label class="control-label" for="content-add-modal-duration">Duration</label>

                <?php
                $attributes = ['id' => 'content-add-modal-duration'];
                echo Form::ib_input(null, null, null, $attributes, ['right_icon' => '<span class="icon-time"></span>']);
                ?>
            </div>

            <?php if (count($learning_outcomes)): ?>
                <div class="col-sm-6">
                    <label class="control-label" for="content-add-modal-learning_outcomes">Learning outcomes</label>

                    <?php
                    $options = [];
                    $selected = [];
                    foreach ($learning_outcomes as $number => $learning_outcome) {
                        $options[$learning_outcome->id] = 'LO'.$number.': '.$learning_outcome->title;
                    }

                    $attributes = ['class' => 'content-add-modal-autosave', 'id' => 'content-add-modal-learning_outcomes', 'multiple' => 'multiple'];
                    echo Form::ib_select(null, null, $options, null, $attributes);
                    ?>
                </div>
            <?php endif; ?>
        </div>


    </div>
<?php $modal_body = ob_get_clean(); ?>

<?php ob_start() ?>
    <button type="button" class="btn btn-lg btn-primary" id="content-add-modal-submit">Save</button>
    <button type="button" class="btn btn-lg btn-cancel" data-dismiss="modal" style="box-shadow: none;">Cancel</button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
if ($full_form) {
    echo $form->action_buttons();
    echo $form->end();
}

echo View::factory('snippets/modal')
    ->set('id',     'content-add-modal')
    ->set('size',   'lg')
    ->set('title',  'Add content')
    ->set('body',   $modal_body)
    ->set('footer', $modal_footer)
?>

<script src="<?= URL::get_engine_plugin_asset('content', 'js/content.js') ?>"></script>
