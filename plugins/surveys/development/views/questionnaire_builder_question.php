<?php if (!isset($answer_types)) {
    $answer_types_names = array();
$answer_types = ORM::factory('Answertype')->order_by('title')->find_all();
foreach($answer_types as $answer_type) {
    $answer_types_names[$answer_type->id] = $answer_type->stub;
}
}?>
<li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-question-li" data-number="<?= $number ?>">
    <div class="col-xs-10" style="width: calc(100% - 2em);">
        <div class="border rounded p-2 bg-white question">
            <div class="questionnaire-question-heading row gutters vertically_center">
                <div class="col-xs-12 col-sm-1 mb-1 mb-md-0">
                    <span class="questionnaire-question-name" data-section_label="Question" data-number="<?= $number ?>"
                        ></span>
                </div>

                <div class="col-xs-12 col-sm-6 mb-3 mb-md-0">
                    <?php
                    // ID
                    $attributes = $disabled ? ['disabled' => 'disabled'] : [];
                    echo Form::hidden('questions['.$number.'][id]', $question->id, $attributes);

                    // Order number
                    $attributes['class'] = 'questionnaire-question-order';
                    echo Form::hidden('questions['.$number.'][order_id]', $has_question->order_id, $attributes);

                    // Group number
                    $attributes['class'] = 'questionnaire-question-group_number';
                    echo Form::hidden('questions['.$number.'][group_number]', $group_number, $attributes);
                    ?>
                    <?php
                    echo Form::ib_textarea(null, 'questions['.$number.'][title]', $question->title, [
                        'class' => 'ckeditor-simple questionnaire-builder-question',
                        'placeholder' => 'Enter question',
                        'id'=> 'questionnaire-builder-question_'. $group_number . '_' . $number
                    ]);
                    ?>
                </div>

                <div class="col-xs-12 col-sm-4">
                    <div class="row gutters mb-0">
                        <div class="col-xs-12 col-sm-6 mb-3 mb-md-0">
                            <?php
                            $name = 'questions['.$number.'][type_id]';
                            $options = '';
                            $selected_type = $question->answer->type->stub ? $question->answer->type->stub : 'radio';
                            foreach ($answer_types as $answer_type) {
                                $options .= '<option
                            value="'.$answer_type->id.'"
                            data-type="'.$answer_type->stub.'"'.
                                    (($answer_type->stub == $selected_type) ? 'selected' : '').
                                    '>'.$answer_type->title.'</option>';
                            }
                            echo Form::ib_select(null, $name, $options, null, ['class' => 'questionnaire-builder-question-type']);
                            ?>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <?php
                            $name = 'questions['.$number.'][required]';
                            $options = [0 => 'Optional', 1 => 'Required'];
                            $selected = $question->id ? $question->required : 1;
                            echo Form::ib_select(null, $name, $options, $selected, ['class' => 'questionnaire-builder-required']);
                            ?>
                        </div>
                    </div>
                    <div class="row gutters mb-0">
                        <div class="col-xs-12 col-sm-12">
                            <?php $name = 'questions['.$number.'][max_score]';
                               $attributes = [
                                   'class' =>'questionnaire-question-max_score',
                                   'type' => 'number',
                                   'min' => 0,
                                   'readonly' => true];
                               echo Form::ib_input('Total Mark', $name, @$question->max_score, $attributes);
                               ?>
                        </div>
                    </div>
                </div>

                <?php $is_template = ($number == 0) ?>

                <div class="col-xs-12 col-sm-1">
                    <button type="button"
                            class="btn-link p-0 w-100<?= !in_array($selected_type, $types_that_expand) ? ' hidden' : ' ' ?>"
                            data-toggle="collapse" data-target="#questionnaire-question-<?= $number ?>-collapsible"
                            aria-expanded="<?= $is_template ? 'true' : 'false' ?>"
                        >
                        <span class="d-sm-none">Show options</span>
                        <span class="expanded-invert icon-angle-down"></span>
                    </button>
                </div>
            </div>

            <div class="questionnaire-question-answer-options text-center collapse<?= $is_template ? ' in' : '' ?>"
                 id="questionnaire-question-<?= $number ?>-collapsible"
                 aria-expanded="<?= $is_template ? 'true' : 'false' ?>"
                >
                <!-- Textbox -->
                <div class="questionnaire-question-answer-options-type hidden px-3 pt-3 pb-2" data-type="input">
                    
                </div>

                <!-- Textarea  -->
                <ul class="questionnaire-question-answer-options-type list-unstyled px-3 pt-3 pb-2 hidden" data-type="textarea">

                </ul>

                <!-- Yes/no toggle  -->
                <?php if(Settings::instance()->get('todos_site_allow_online_exams')):?>
                    <ul class="questionnaire-question-answer-options-type list-unstyled px-3 pt-3 pb-2 hidden" data-type="yes_or_no">
                        <li class="row gutters mt-3 vertically_center questionnaire-option" data-option_number="0">
                                <div class="col-xs-2 col-sm-1 questionnaire-field-preview">
                                     <?= Form::ib_radio(null, null, false, null, ['disabled' => true]) ?>
                                </div>

                                <div class="col-xs-10 col-sm-7">
                                    <input type="hidden"
                                         name="questions[<?= $number ?>][answer_options][0][id]"
                                           value="<?= $answer_option->id ?>"/>

                                         <?php
                                            $name = 'questions['.$number.'][answer_options][0][label]';
                                            $attributes = [
                                                'placeholder' => 'Enter an answer choice',
                                                'readonly' => true];
                                            echo Form::ib_input(null, $name, 'Yes', $attributes);
                                                ?>
                                </div>

                                <div class="col-xs-2 d-md-none">Mark</div>

                                <div class="col-xs-10 col-sm-2 my-3 my-md-0">
                                        <?php
                                        $name = 'questions['.$number.'][answer_options][0][score]';
                                        $value = $answer_option->score;
                                        echo Form::ib_input(null, $name, $value, [
                                            'type' => 'number',
                                            'min' => 0,
                                            'placeholder' => 'Mark',
                                            'class' => 'answer-score answer-toggle',
                                            'id' =>'answer_score_' . $number . '_0']);
                                        ?>
                                </div>
                        </li>
                        <li class="row gutters mt-3 vertically_center questionnaire-option" data-option_number="1">
                                <div class="col-xs-2 col-sm-1 questionnaire-field-preview">
                                     <?= Form::ib_radio(null, null, false, null, ['disabled' => true]) ?>
                                </div>

                                <div class="col-xs-10 col-sm-7">
                                    <input type="hidden"
                                         name="questions[<?= $number ?>][answer_options][1][id]"
                                           value="<?= $answer_option->id ?>"/>

                                         <?php
                                            $name = 'questions['.$number.'][answer_options][1][label]';
                                            $attributes = [
                                                'placeholder' => 'Enter an answer choice',
                                                'readonly' => true
                                            ];
                                            echo Form::ib_input(null, $name, 'No', $attributes);
                                                ?>
                                </div>

                                <div class="col-xs-2 d-md-none">Mark</div>

                                <div class="col-xs-10 col-sm-2 my-3 my-md-0">
                                        <?php
                                        $name = 'questions['.$number.'][answer_options][1][score]';
                                        $value = $answer_option->score;
                                        echo Form::ib_input(null, $name, $value, [
                                            'type' => 'number',
                                            'min' => 0,
                                            'placeholder' => 'Mark',
                                            'class' => 'answer-score answer-toggle',
                                            'id' =>'answer_score_' . $number . '_1' ]);
                                        ?>
                                </div>
                        </li>
                    </ul>
                <?php else:?>
                    <div class="row vertically_center" style="width: calc(100% - 2rem);">
                        <div class="col-sm-3">
                            <label>Child survey</label>
                        </div>

                        <div class="col-sm-9">
                            <?php
                            $survey_options = ['' => '-- Please select --'] + $surveys;
                            echo Form::ib_select(null, 'questions['.$number.'][child_survey_id]', $survey_options, $question->child_survey_id); ?>
                        </div>
                    </div>
                <?php endif?>
                <!-- Checkboxes -->
                <ul class="questionnaire-question-answer-options-type list-unstyled hidden" data-type="checkbox">
                    <?php
                    $answer_options = array();
                    if (array_key_exists($question->answer->type_id, $answer_types_names) && $answer_types_names[$question->answer->type_id] == 'checkbox') {
                        $answer_options = $question->answer->options->order_by('order_id')->find_all_undeleted();
                    }
                    $answer_options = count($answer_options) ? $answer_options : [
                        new Model_AnswerOption(),
                        new Model_AnswerOption(),
                        new Model_AnswerOption(),
                    ];
                    ?>

                    <?php foreach ($answer_options as $i => $answer_option): ?>
                        <?php $i++; ?>
                        <li class="row gutters mt-3 vertically_center questionnaire-option" data-option_number="<?= $i ?>">
                            <div class="col-xs-2 col-sm-1 questionnaire-field-preview">
                                <?= Form::ib_checkbox(null, null, false, null, ['disabled' => true]) ?>
                            </div>

                            <div class="col-xs-10 col-sm-7">
                                <input
                                    type="hidden"
                                    name="questions[<?= $number ?>][answer_options][<?= $i ?>][id]"
                                    value="<?= $answer_option->id ?>"
                                    />
                                <input
                                    type="hidden"
                                    name="questions[<?= $number ?>][answer_options][<?= $i ?>][order_id]"
                                    value="<?= $answer_option->order_id ?>"
                                    />

                                <?php
                                $name = 'questions['.$number.'][answer_options]['.$i.'][label]';
                                $value = $answer_option->label;
                                $attributes = ['placeholder' => 'Enter an answer choice'];
                                echo Form::ib_input(null, $name, $value, $attributes);
                                ?>
                            </div>

                            <div class="col-xs-2 d-md-none">Mark</div>

                            <div class="col-xs-10 col-sm-2 my-3 my-md-0">
                                <?php
                                $name = 'questions['.$number.'][answer_options]['.$i.'][score]';
                                $value = $answer_option->score;
                                $attributes = [];
                                echo Form::ib_input(null, $name, $value, [
                                    'type' => 'number',
                                    'min' => 0,
                                    'placeholder' => 'Mark',
                                    'class' => 'answer-score answer-checkbox',
                                    'id' =>'answer_score_' . $number . '_' . $i
                                ]);
                                ?>
                            </div>

                            <div class="col-xs-12 col-sm-2">
                                <button type="button" class="btn btn-default form-btn questionnaire-add-option"
                                        style="min-width: 0;" title="Add option">
                                    <span class="icon-plus"></span>
                                    <span class="d-md-none">Add</span>
                                </button>

                                <button type="button" class="btn btn-default form-btn questionnaire-remove-option"
                                        style="min-width: 0;" title="Remove option">
                                    <span class="icon-minus"></span>
                                    <span class="d-md-none">Remove</span>
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Radio buttons -->
                <ul class="questionnaire-question-answer-options-type list-unstyled<?= $is_template ? '' : ' hidden' ?>" data-type="radio">
                    <?php
                    //die('<pre>' . print_r(array_key_exists($question->answer->type_id, $answer_types_names), 1) . '</pre>');

                    if (array_key_exists($question->answer->type_id, $answer_types_names) && $answer_types_names[$question->answer->type_id] == 'radio') {
                        $answer_options = $question->answer->options->order_by('order_id')->find_all_undeleted();
                    }
                    $answer_options = count($answer_options) ? $answer_options : [
                        new Model_AnswerOption(),
                        new Model_AnswerOption(),
                        new Model_AnswerOption(),
                    ];

                    ?>

                    <?php foreach ($answer_options as $i => $answer_option): ?>

                        <li class="row gutters mt-3 vertically_center questionnaire-option" data-option_number="<?= $i ?>">
                            <div class="col-xs-2 col-sm-1 questionnaire-field-preview">
                                <?= Form::ib_radio(null, null, false, null, ['disabled' => true]) ?>
                            </div>

                            <div class="col-xs-10 col-sm-7">
                                <input type="hidden"
                                       name="questions[<?= $number ?>][answer_options][<?= $i ?>][id]"
                                       value="<?= $answer_option->id ?>"
                                    />

                                <?php
                                $name = 'questions['.$number.'][answer_options]['.$i.'][label]';
                                $value = $answer_option->label;
                                $attributes = ['placeholder' => 'Enter an answer choice'];
                                echo Form::ib_input(null, $name, $value, $attributes);
                                ?>
                            </div>

                            <div class="col-xs-2 d-md-none">Mark</div>

                            <div class="col-xs-10 col-sm-2 my-3 my-md-0">
                                <?php
                                $name = 'questions['.$number.'][answer_options]['.$i.'][score]';
                                $value = $answer_option->score;
                                echo Form::ib_input(null, $name, $value, [
                                    'type' => 'number',
                                    'min' => 0,
                                    'class' => 'answer-score answer-radio',
                                    'id' =>'answer_score_' . $number . '_' . $i]);

                                ?>
                            </div>

                            <div class="col-xs-12 col-sm-2">
                                <button type="button" class="btn btn-default form-btn questionnaire-add-option"
                                        style="min-width: 0;" title="Add option">
                                    <span class="icon-plus"></span>
                                    <span class="d-md-none">Add</span>
                                </button>

                                <button type="button" class="btn btn-default form-btn questionnaire-remove-option"
                                        style="min-width: 0;" title="Remove option">
                                    <span class="icon-minus"></span>
                                    <span class="d-md-none">Remove</span>
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Select list -->
                <ul class="questionnaire-question-answer-options-type list-unstyled hidden" data-type="select">
                    <?php foreach ($answer_options as $i => $answer_option): ?>
                        <li class="row gutters mt-3 vertically_center questionnaire-option" data-option_number="<?= $i ?>">
                            <div class="col-xs-2 col-sm-1 questionnaire-field-preview">
                                Option
                            </div>

                            <div class="col-xs-10 col-sm-7">
                                <input
                                    type="hidden"
                                    name="questions[<?= $number ?>][answer_options][<?= $i ?>][id]"
                                    value="<?= $answer_option->id ?>"

                                    />

                                <?php
                                $name = 'questions['.$number.'][answer_options]['.$i.'][label]';
                                $value = $answer_option->label;
                                $attributes = ['placeholder' => 'Enter an answer choice'];
                                echo Form::ib_input(null, $name, $value, $attributes);
                                ?>
                            </div>

                            <div class="col-xs-2 d-md-none">Mark</div>

                            <div class="col-xs-10 col-sm-2 my-3 my-md-0">
                                <?php
                                $name = 'questions['.$number.'][answer_options]['.$i.'][score]';
                                $value = $answer_option->score;
                                echo Form::ib_input(null, $name, $value, [
                                    'type' => 'number',
                                    'min' => 0,
                                    'placeholder' => 'Mark',
                                    'class' => 'answer-score answer-select',
                                    'id' =>'answer_score_' . $number . '_' . $i]);
                                ?>
                            </div>

                            <div class="col-xs-12 col-sm-2">
                                <button type="button" class="btn btn-default form-btn questionnaire-add-option"
                                        style="min-width: 0;" title="Add option">
                                    <span class="icon-plus"></span>
                                    <span class="d-md-none">Add</span>
                                </button>

                                <button type="button" class="btn btn-default form-btn questionnaire-remove-option"
                                        style="min-width: 0;" title="Remove option">
                                    <span class="icon-minus"></span>
                                    <span class="d-md-none">Remove</span>
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="questionnaire-question-footer row gutters">
                <div class="bg-white p-3 text-center">
                    <button type="button" class="questionnaire-question-clone-btn btn btn-lg btn-default" id="clone_<?=$number?>" data-type="question">Clone question</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-1 text-center" style="width: 2em;">
        <button type="button" class="button--plain questionnaire-question-remove text-decoration-none" data-number="<?= $number ?>" data-toggle="modal" data-target="#question-delete-modal">
            <span class="icon_close" style="font-size: 1.5em;"></span>
        </button>
    </div>
</li>
