<?php
$surveys = ORM::factory('Survey')->find_all_undeleted()->as_array('id', 'title');

// Types of questions that have expanded sections
$types_that_expand = ['checkbox', 'radio', 'select'];

// The expanded section for yes/no is only used with the safety plugin.
if (Model_Plugin::is_loaded('safety') || Settings::instance()->get('todos_site_allow_online_exams')) {
    $types_that_expand[] = 'yes_or_no';
}
?>
<div class="row">
<input type="hidden" id="questionnaire-builder-types_that_expand" value="<?= htmlspecialchars(json_encode($types_that_expand)) ?>" />
<input type="hidden" id="questionnaire-builder-banner_image" value="" />
    <div class="col-sm-12">
        <?= View::factory('multiple_upload',
            [
                'browse_directory' => 'image',
                'directory'        => 'image',
                'duplicate'        => 0,
                'include_js'       => true,
                'name'             => 'questionnaire_banner_image',
                'onsuccess'        => 'subject_image_uploaded',
                'preset'           => 'courses',
                'id'               => 'form_add_edit_subject-image-preview',
                'single'           => true
            ]
        ) ?>
    </div>
</div>
<div class="row">
        <ul class="questionnaire-wrapper" id="questionnaire-wrapper">
            <?php
            $number = 0;
            $group_number = 0;
            // Grouped questions
            if (isset($questionnaire)) {
                foreach ($questionnaire->get_groups() as $group) {
                    $disabled = false;
                    ++$group_number;
                    if ($group->type == 'prompt') {
                        include 'questionnaire_builder_prompt.php';
                    } else {
                        include 'questionnaire_builder_group.php';
                    }
                }
            }

            // Ungrouped questions
            /* No longer supported. Everything should go in a group.
            if (isset($questionnaire)) {
                foreach ($questionnaire->ungrouped_questions()->find_all_undeleted() as $has_question) {
                    $question = $has_question->question->deleted ? new Model_Question : $has_question->question;
                    $disabled = false;
                    ++$number;
                    include 'questionnaire_builder_question.php';
                }
            }
            */
            ?>
        </ul>


        <div class="form-action-group questionnaire-question-add-form my-2">
            <div class="bg-light rounded border p-3 text-center">
                <button type="button" class="questionnaire-question-add-btn btn btn-lg btn-default" data-type="group"
                        id="questionnaire-builder-add-group">Add group</button>
                <button type="button" class="questionnaire-question-add-btn btn btn-lg btn-default" data-type="group"
                        id="questionnaire-builder-add-prompt">Add text prompt</button>

                <?php /*
        <button type="button" class="questionnaire-question-add-btn btn btn-lg btn-default" data-type="question"
                id="questionnaire-builder-add">Add question</button>
        */ ?>
            </div>
        </div>

        <ul class="hidden" id="questionnaire-group-template">
            <?php
            $group = new Model_Group();
            $has_group = new Model_SurveyHasGroup();
            $disabled = true;
            $group_number = 0;
            include 'questionnaire_builder_group.php';
            ?>
        </ul>

        <ul class="hidden" id="questionnaire-question-template">
            <?php
            $question = new Model_Question();
            $has_question = new Model_SurveyHasQuestion();
            $disabled = true;
            $number = 0;
            include 'questionnaire_builder_question.php';
            ?>
        </ul>
        <ul class="hidden" id="questionnaire-prompt-template">
            <?php
            $group = new Model_Group();
            $has_group = new Model_SurveyHasGroup();
            $disabled = true;
            $group_number = 0;
            include 'questionnaire_builder_prompt.php';
            ?>
        </ul>

        <?php ob_start() ?>
        <button type="button" class="btn btn-lg btn-danger" id="question-group-delete-modal-confirm">Delete</button>
        <button type="button" class="btn btn-lg btn-cancel" data-dismiss="modal">Cancel</button>
        <?php $delete_modal_buttons = ob_get_clean(); ?>

        <?php
        echo View::factory('snippets/modal')
            ->set('id',     'question-group-delete-modal')
            ->set('title',  'Confirm delete')
            ->set('body',   '<p>Are you sure you want to delete this group and all of its questions?</p>')
            ->set('footer', $delete_modal_buttons);
        ?>

        <?php ob_start() ?>
        <button type="button" class="btn btn-lg btn-danger" id="question-delete-modal-confirm">Delete</button>
        <button type="button" class="btn btn-lg btn-cancel" data-dismiss="modal">Cancel</button>
        <?php $delete_modal_buttons = ob_get_clean(); ?>

        <?php
        echo View::factory('snippets/modal')
            ->set('id',     'question-delete-modal')
            ->set('title',  'Confirm delete')
            ->set('body',   '<p>Are you sure you want to delete this question and all of its answer options?</p>')
            ->set('footer', $delete_modal_buttons);
        ?>
</div>


<style>
    .questionnaire-wrapper {
        counter-reset: group question;
    }

    .questionnaire-field-preview .form-checkbox,
    .questionnaire-field-preview .form-radio {
        margin-bottom: 0;
    }

    .questionnaire-wrapper .row.gutters {
        margin-left: -5px;
        margin-right: -5px;
    }

    .questionnaire-wrapper .row.gutters > [class*="col-"] {
        padding-left: 5px;
        padding-right: 5px;
    }

    .questionnaire-question-name:before {
        content: 'Question ' counter(question);
        counter-increment: question;
    }

    .questionnaire-group-name:before {
        content: 'Group\a0' counter(group);
        counter-increment: group;
    }

    @media screen and (min-width: 768px) {
        .questionnaire-question-name:before {
            content: 'Q' counter(question) ': ';
        }
    }
</style>


<script src="<?= URL::get_engine_plugin_asset('surveys', 'js/edit_questionnaire.js', ['cachebust' => true])?>"></script>