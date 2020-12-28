<div class="survey-wrapper" data-id="<?= $survey->id ?>" <?= ($survey->course_id != NULL) ? "data-course-id='{$survey->course_id}'" : '' ?>>
    <div class="validationEngineContainer" id="survey-page">
        <div class="survey-question-blocks" id="survey-question-blocks" data-group="<?= $group; ?>">
            <?php foreach($questions as $key=>$question) :?>
                <div class="survey-question-block" data-question_id="<?= $question->id?>" data-type="<?= $question->answer->type->stub ?>">
                    <div class="survey-question-options">
                        <div class="survey-question-text">
                            <span class="survey-question-number"><?= intval($question_number)+$key ?></span>
                            <span><?= $question->title ?></span>
                        </div>
                        <?php
                        $response = (isset($responses) && isset($responses[$question->id])) ? $responses[$question->id] : null;
                        include 'input/'.$question->answer->type->stub.'.php';
                        unset($name);
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="survey-navigation-buttons">
            <?php // hide the "Prev" button for the first question/group ?>
            <?php if (!empty($previous_question_id) || $survey->get_previous_group($group)->id): ?>
                <button type="button" class="button survey-button-back" id="survey-question-prev">Prev</button>
            <?php endif; ?>
            <button type="button" class="button survey-button-forward" id="survey-question-next">Next</button>
        </div>
    </div>

    <!-- modal boxes -->
    <div class="survey-modal" id="survey-modal-required">
        <div class="survey-modal-dialog">
            <div class="survey-modal-content">
                <div class="survey-modal-header">
                    <button type="button" class="survey-modal-close survey-modal-close-icon" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="survey-modal-title">Answer required</h4>
                </div>
                <div class="survey-modal-body">
                    <p>You must supply an answer in order to proceed.</p>
                </div>
                <div class="survey-modal-footer text-center">
                    <button type="button" class="btn btn-default btn-lg survey-button-back survey-modal-close" style="min-width: 5em;">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="survey-modal" id="survey-modal-complete">
        <div class="survey-modal-dialog">
            <div class="survey-modal-content">
                <div class="survey-modal-header">
                    <button type="button" class="survey-modal-close survey-modal-close-icon" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="survey-modal-title">Survey complete</h4>
                </div>
                <div class="survey-modal-body">
                    <p id="download">You have reached the end of the survey. Do you want to download a document with your responses or review your answers?</p>
                    <p id="thank-you">You have reached the end of the survey. Thank you for taking time to answer.</p>

                    <div id="survey-complete-result-wrapper">
                        <p>Here are your results.</p>

                        <div id="survey-complete-result"></div>
                    </div>
                </div>
                <div class="survey-modal-footer">
                    <button type="button" class="survey-button-back survey-modal-close">Go back</button>
                    <span id="download_doc"><a href="/frontend/surveys/finish_survey/<?= $survey->id ?>" class="survey-button-forward" id="survey-download-button" data-id="<?= $survey->id ?>">Download Document</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php //todo: move CSS to separate files ?>
<style>
    .survey-modal {
        background: rgba(0,0,0,.3);
        display: none;
        overflow: hidden;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1050;
    }
    .survey-modal-dialog {
        max-width: 600px;
        margin: 30px auto;
    }
    .survey-modal-content {
        position: relative;
        background: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 6px;
    }
    .survey-modal-header,
    .survey-modal-body,
    .survey-modal-footer {
        padding: .5em 1em;
    }
    .survey-modal-header {
        border-bottom: 1px solid rgba(0,0,0,.2);
        padding-top: .75em;
    }
    .survey-modal-footer {
        border-top: 1px solid rgba(0,0,0,.2);
    }
    .survey-modal-title.survey-modal-title {
        margin-top: 0;
    }
    .survey-modal-close-icon {
        background: none;
        border: none;
        color: #000;
        cursor: pointer;
        float: right;
        font-size: 1em;
        opacity: 0.2;
        text-shadow: 0 1px 0 #fff;
    }
</style>