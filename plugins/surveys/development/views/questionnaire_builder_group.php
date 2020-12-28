<li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-group-li" data-number="<?= $group_number ?>" data-type="question">
    <div class="col-xs-10" style="width: calc(100% - 2em);">
        <div class="border rounded p-2 bg-white">
            <div class="questionnaire-group-heading row p-0 vertically_center">
                <div class="col-xs-12 col-sm-1">
                    <span data-section_label="Group" class="questionnaire-group-name"
                          data-number="<?= $group_number ?>"></span>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <?php
                    // Hidden fields
                    $attributes = $disabled ? ['disabled' => 'disabled'] : [];
                    echo Form::hidden('groups['.$group_number.'][id]', $group->id, $attributes);
                    $attributes['class'] = 'questionnaire-group-order';
                    echo Form::hidden('groups['.$group_number.'][order_id]', $group_number, $attributes);
                    echo Form::hidden('groups['.$group_number.'][type]', 'question', array('class' => 'questionnaire-group-type'));

                    // Name field
                    $attributes = [
                        'class' => 'questionnaire-builder-group',
                        'disabled' => $disabled,
                        'placeholder' => 'Enter group name'
                    ];
                    echo Form::ib_input(null, 'groups['.$group_number.'][title]', $group->title, $attributes);
                    ?>
                </div>

                <div class="col-xs-12 col-sm-offset-4 col-sm-1">
                    <button type="button"
                            class="btn-link p-0 w-100"
                            data-toggle="collapse" data-target="#questionnaire-group-<?= $group_number ?>-collapsible"
                            aria-expanded="true"
                        >
                        <span class="d-sm-none"><?= __('Show questions') ?></span>
                        <span class="expanded-invert icon-angle-down"></span>
                    </button>
                </div>
            </div>

            <div class="collapse in" id="questionnaire-group-<?= $group_number ?>-collapsible">
                <ul class="questionnaire-group-questions">
                    <?php
                    foreach ($group->has_questions->where('survey_id', '=', $questionnaire->id)->order_by('order_id')->find_all_undeleted() as $has_question) {
                        $question = $has_question->question->deleted ? new Model_Question : $has_question->question;
                        $disabled = false;
                        ++$number;
                        include 'questionnaire_builder_question.php';
                    }
                    ?>
                </ul>

                <div class="form-action-group questionnaire-question-add-form my-2">
                    <div class="bg-light rounded border p-3 text-center">
                        <button type="button" class="questionnaire-question-add-btn btn btn-lg btn-default" data-type="question"><?= __('Add question to group') ?></button>
                        <button type="button" class="questionnaire-group-clone-btn btn btn-lg btn-default" data-type="question"><?= __('Clone group') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-1 text-center" style="width: 2em;">
        <button type="button" class="button--plain questionnaire-group-remove text-decoration-none" data-number="<?= $group_number ?>" data-toggle="modal" data-target="#question-group-delete-modal">
            <span class="icon_close" style="font-size: 1.5em;"></span>
        </button>
    </div>
</li>