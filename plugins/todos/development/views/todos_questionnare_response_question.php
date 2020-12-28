<?php $surveys = ORM::factory('Survey')->find_all_undeleted()->as_array('id', 'title');

// Types of questions that have expanded sections
$types_that_expand = ['checkbox', 'radio', 'select'];

// The expanded section for yes/no is only used with the safety plugin.
if (Model_Plugin::is_loaded('safety')) {
    $types_that_expand[] = 'yes_or_no';
}?>
<?php $number =1?>
<li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-question-li" data-number="<?= $number ?>">
    <div class="col-xs-12 col-sm-1 mb-1 mb-md-0">
        <span class="questionnaire-question-name" data-section_label="Question" data-number="<?= $number ?>"></span><?php $question->id ? $question->required : $number;?>
        <span class="show-required"></span>
    </div>
    <div class="col-xs-12 col-sm-11">
            <div class="border rounded p-2 bg-white">
                <div class="questionnaire-question-heading row vertically-center">
                    <?php $is_template = ($number == 0) ?>
                    <div class="col-xs-12 col-sm-10">
                            <span id="questionnaire-builder-question_<?=$number?>"><?=!empty($question->title) ? $question->title : 'Question text ' . $number?></span>
                    </div>
                    <div class="col-xs-12 col-sm-1">
                        <span class="questionnaire-question-marks"><?=!empty($result->question->received)
                        ? $result->question->received . '/'  . $question->question->mark : '3/3'?></span>
                    </div>
                    <div class="col-xs-12 col-sm-1">
                        <button type="button"
                                class="btn-link p-0 w-100"
                                data-toggle="collapse" data-target="#questionnaire-question-<?= $number ?>-collapsible"
                                aria-expanded="<?= $is_template ? 'true' : 'false' ?>">
                            <span class="d-sm-none">Show options</span>
                            <span class="expanded-invert icon-angle-down"></span>
                        </button>
                    </div>
                </div>
                <div class="collapse" id="questionnaire-question-<?= $number ?>-collapsible">

                    <div class="questionnaire-question-body row gutters vertically_center">
                        <div class="row">
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
                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <?php $answer_options = $question->answer->options->find_all_undeleted();
                                    $answer_options = count($answer_options) ? $answer_options : [
                                        new Model_AnswerOption(),
                                        new Model_AnswerOption(),
                                        new Model_AnswerOption(),
                                    ]
                                    ?>
                                    <ul class="questionnaire-question-answer-options-type list-unstyled">

                                        <?php foreach ($answer_options as $i => $answer_option):?>
                                            <li class="row mt-3 vertically_center questionnaire-option" data-option_number="<?= $i ?>">
                                                <?php echo !empty($answer_option->label) ? $answer_option->label  : 'Answer ' .$i;?>
                                            </li>
                                        <?php endforeach;?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                            <div class="col-xs-12 col-sm-3">
                            <div class="row">
                                <?php $answer_options = $question->answer->options->find_all_undeleted();
                                $answer_options = count($answer_options) ? $answer_options : [
                                    new Model_AnswerOption(),
                                    new Model_AnswerOption(),
                                    new Model_AnswerOption(),
                                ]
                                ?>
                                <?php foreach($answer_options as $i => $answer_option):?>
                                        <span><strong>Selected Answer</strong></span>
                                <?php endforeach?>
                            </div>
                        </div>
                            <div class="col-xs-12 col-sm-3">
                            <div class="row">

                                    <?php $answer_options = $question->answer->options->find_all_undeleted();
                                    $answer_options = count($answer_options) ? $answer_options : [
                                        new Model_AnswerOption(),
                                        new Model_AnswerOption(),
                                        new Model_AnswerOption(),
                                    ]
                                    ?>
                                    <?php foreach ($answer_options as $i => $answer_option):
                                        $name = 'questions['.$number.'][received]['.$i.']';
                                        $attributes = ['type' => 'number',
                                                        'min' =>'0',
                                                        'step' => '0.01'];
                                        echo Form::ib_input(null, $name, !empty($answer_option->score) ? $answer_option->score : 0, $attributes);
                                    endforeach;?>

                            </div>
                        </div>
                        </div>
                        <div class="row" style="width:100%;">
                                <h6>Feedback</h6>
                                <div class="col-xs-12 col-sm-12">
                                    <?php echo Form::ib_textarea(null, 'question_answers['.$number.'][title]', $question->title, [
                                        'class' => 'questionnaire-builder-question',
                                        'placeholder' => 'Examiner\'s comment',
                                        'id'=> 'questionnaire-builder-question_'.$number,
                                        'rows' => 7
                                    ]);?>
                                </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
</li>
<?php $number =2?>
<li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-question-li" data-number="<?= $number ?>">
    <div class="col-xs-12 col-sm-1 mb-1 mb-md-0">
        <span class="questionnaire-question-name" data-section_label="Question" data-number="<?= $number ?>"></span><?php $question->id ? $question->required : $number;?>
        <span class="show-required"></span>
    </div>
    <div class="col-xs-12 col-sm-11">
            <div class="border rounded p-2 bg-white">
                <div class="questionnaire-question-heading row vertically-center">
                    <?php $is_template = ($number == 0) ?>
                    <div class="col-xs-12 col-sm-10">
                            <span id="questionnaire-builder-question_<?=$number?>"><?=!empty($question->title) ? $question->title : 'Question text ' . $number?></span>
                    </div>
                    <div class="col-xs-12 col-sm-1">
                        <span class="questionnaire-question-marks"><?=!empty($result->question->received)
                        ? $result->question->received . '/'  . $question->question->mark : '1/1'?></span>
                    </div>
                    <div class="col-xs-12 col-sm-1">
                        <button type="button"
                                class="btn-link p-0 w-100"
                                data-toggle="collapse" data-target="#questionnaire-question-<?= $number ?>-collapsible"
                                aria-expanded="<?= $is_template ? 'true' : 'false' ?>">
                            <span class="d-sm-none">Show options</span>
                            <span class="expanded-invert icon-angle-down"></span>
                        </button>
                    </div>
                </div>
                <div class="collapse" id="questionnaire-question-<?= $number ?>-collapsible">

                    <div class="questionnaire-question-body row gutters vertically_center">
                        <div class="row">
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
                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <?php $answer_options = $question->answer->options->find_all_undeleted();
                                    $answer_options = count($answer_options) ? $answer_options : [
                                        new Model_AnswerOption(),
                                        new Model_AnswerOption(),
                                        new Model_AnswerOption(),
                                    ]
                                    ?>
                                    <ul class="questionnaire-question-answer-options-type list-unstyled">

                                        <?php foreach ($answer_options as $i => $answer_option):?>
                                            <li class="row mt-3 vertically_center questionnaire-option" data-option_number="<?= $i ?>">
                                                <?php echo !empty($answer_option->label) ? $answer_option->label  : 'Answer ' .$i;?>
                                            </li>
                                        <?php endforeach;?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                            <div class="col-xs-12 col-sm-3">
                            <div class="row">
                                <?php $answer_options = $question->answer->options->find_all_undeleted();
                                $answer_options = count($answer_options) ? $answer_options : [
                                    new Model_AnswerOption(),
                                    new Model_AnswerOption(),
                                    new Model_AnswerOption(),
                                ]
                                ?>
                                <?php foreach($answer_options as $i => $answer_option):?>
                                    <?php if($i ==2 || $i ==3 ):?>
                                        <div><strong>Selected Answer</strong></div>
                                    <?php endif?>
                                <?php endforeach?>
                            </div>
                        </div>
                            <div class="col-xs-12 col-sm-3">
                            <div class="row">

                                    <?php $answer_options = $question->answer->options->find_all_undeleted();
                                    $answer_options = count($answer_options) ? $answer_options : [
                                        new Model_AnswerOption(),
                                        new Model_AnswerOption(),
                                        new Model_AnswerOption(),
                                    ]
                                    ?>
                                    <?php foreach ($answer_options as $i => $answer_option):
                                        $name = 'questions['.$number.'][received]['.$i.']';
                                        $attributes = ['type' => 'number',
                                            'min' =>'0',
                                            'step' => '0.01'];
                                        echo Form::ib_input(null, $name, !empty($answer_option->score) ? $answer_option->score : 0, $attributes);
                                    endforeach;?>

                            </div>
                        </div>
                        </div>
                        <div class="row" style="width:100%;">
                                <h6>Feedback</h6>
                                <div class="col-xs-12 col-sm-12">
                                    <?php echo Form::ib_textarea(null, 'questions['.$number.'][title]', $question->title, [
                                        'class' => 'questionnaire-builder-question',
                                        'placeholder' => 'Examiner\'s comment',
                                        'id'=> 'questionnaire-builder-question_'.$number,
                                        'rows' => 7
                                    ]);?>
                                </div>
                        </div>
                    </div>
                </div>

            </div>
    </div>
</li>
<?php $number = 3?>
<li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-question-li" data-number="<?= $number ?>">
    <div class="col-xs-12 col-sm-1 mb-1 mb-md-0">
        <span class="questionnaire-question-name" data-section_label="Question" data-number="<?= $number ?>"></span><?php $question->id ? $question->required : $number;?>
        <span class="show-required"></span>
    </div>
    <div class="col-xs-12 col-sm-11">
        <div class="border rounded p-2 bg-white">
            <div class="questionnaire-question-heading row vertically-center">
                <?php $is_template = ($number == 0) ?>
                <div class="col-xs-12 col-sm-10">
                    <span id="questionnaire-builder-question_<?=$number?>"><?=!empty($question->title) ? $question->title : 'Question text ' . $number?></span>
                </div>
                <div class="col-xs-12 col-sm-1">
                        <span class="questionnaire-question-marks"><?=!empty($result->question->received)
                                ? $result->question->received . '/'  . $question->question->mark : '1/1'?></span>
                </div>
                <div class="col-xs-12 col-sm-1">
                    <button type="button"
                            class="btn-link p-0 w-100"
                            data-toggle="collapse" data-target="#questionnaire-question-<?= $number ?>-collapsible"
                            aria-expanded="<?= $is_template ? 'true' : 'false' ?>">
                        <span class="d-sm-none">Show options</span>
                        <span class="expanded-invert icon-angle-down"></span>
                    </button>
                </div>
            </div>
            <div class="collapse" id="questionnaire-question-<?= $number ?>-collapsible">

                <div class="questionnaire-question-body row gutters vertically_center">
                        <div class="row" style="width:100%;">
                            <div class="col-xs-12 col-sm-9 mb-3 mb-md-0">
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
                            <div class="row vertically_center">
                                <div class="col-xs-12 col-sm-12">
                                        <span class="questionnaire-question-answer-options-type list-unstyled">
                                            My answer to the question is ABCS
                                        </span>
                                </div>

                            </div>
                    </div>
                            <div class="col-xs-12 col-sm-3">
                            <div class="row vertically_center">


                                <?php
                                $name = 'questions['.$number.'][received]['.$i.']';
                                $attributes = ['type' => 'number',
                                    'min' =>'0',
                                    'step' => '0.01'];
                                echo Form::ib_input(null, $name, !empty($answer_option->score) ? $answer_option->score : 0, $attributes);
                                ?>

                            </div>
                        </div>
                        </div>
                        <div class="row" style="width:100%;">
                        <h6>Feedback</h6>
                        <div class="col-xs-12 col-sm-12">
                            <?php echo Form::ib_textarea(null, 'questions['.$number.'][title]', $question->title, [
                                'class' => 'questionnaire-builder-question',
                                'placeholder' => 'Examiner\'s comment',
                                'id'=> 'questionnaire-builder-question_'.$number,
                                'rows' => 7
                            ]);?>
                        </div>
                    </div>
                    </div>

            </div>
        </div>
    </div>
</li>
<?php $number = 4?>
<li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-question-li" data-number="<?= $number ?>">
    <div class="col-xs-12 col-sm-1 mb-1 mb-md-0">
        <span class="questionnaire-question-name" data-section_label="Question" data-number="<?= $number ?>"></span><?php $question->id ? $question->required : $number;?>
        <span class="show-required"></span>
    </div>
    <div class="col-xs-12 col-sm-11">
        <div class="border rounded p-2 bg-white">
            <div class="questionnaire-question-heading row vertically-center">
                <?php $is_template = ($number == 0) ?>
                <div class="col-xs-12 col-sm-10">
                    <span id="questionnaire-builder-question_<?=$number?>"><?=!empty($question->title) ? $question->title : 'Question text ' . $number?></span>
                </div>
                <div class="col-xs-12 col-sm-1">
                        <span class="questionnaire-question-marks"><?=!empty($result->question->received)
                                ? $result->question->received . '/'  . $question->question->mark : '1/1'?></span>
                </div>
                <div class="col-xs-12 col-sm-1">
                    <button type="button"
                            class="btn-link p-0 w-100"
                            data-toggle="collapse" data-target="#questionnaire-question-<?= $number ?>-collapsible"
                            aria-expanded="<?= $is_template ? 'true' : 'false' ?>">
                        <span class="d-sm-none">Show options</span>
                        <span class="expanded-invert icon-angle-down"></span>
                    </button>
                </div>
            </div>
            <div class="collapse" id="questionnaire-question-<?= $number ?>-collapsible">
                <div class="questionnaire-question-body row gutters vertically_center">
                    <div class="row">
                        <div class="col-xs-12 col-sm-9 mb-3 mb-md-0">
                            <?php
                            // ID
                            $attributes = $disabled ? ['disabled' => 'disabled'] : [];
                            echo Form::hidden('questions_responses['.$number.'][id]', $question->id, $attributes);

                            // Order number
                            $attributes['class'] = 'questionnaire-question-order';
                            echo Form::hidden('questions_responses['.$number.'][order_id]', $has_question->order_id, $attributes);

                            // Group number
                            $attributes['class'] = 'questionnaire-question-group_number';
                            echo Form::hidden('questions_responses['.$number.'][group_number]', $group_number, $attributes);
                            ?>
                            <div class="row vertically_center">
                                <div class="col-xs-12 col-sm-12">
                                        <div class="answer" style="overflow-y:auto;height: 300px; ">
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc a finibus arcu, porttitor semper mi. Aliquam massa ipsum, imperdiet a pulvinar eget, maximus at augue. Proin ut aliquet neque. Aenean quam lorem, ornare venenatis ultricies id, aliquet non mi. Cras placerat nisl lacus, ut scelerisque ipsum elementum eu. Aenean aliquam placerat ipsum, et mattis dui venenatis quis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce nibh ligula, suscipit molestie dignissim eu, tristique at erat. In massa risus, pulvinar efficitur ante eu, pharetra porta dolor. Nunc semper tempus neque, sed luctus quam sollicitudin eu. Fusce id molestie mi. Nulla in dui gravida, aliquam lorem ac, sodales ex. Pellentesque ultrices quis felis vehicula hendrerit. Donec lacinia consequat cursus. Aenean porttitor gravida neque ut cursus.

Etiam vel sapien lobortis, aliquet ex et, sagittis elit. Sed at nunc sit amet dui aliquet gravida ut nec ex. Fusce convallis aliquet urna, ac porttitor tortor eleifend quis. Proin faucibus iaculis est vitae venenatis. Ut id feugiat tortor. Integer sollicitudin varius metus, quis molestie risus rhoncus in. Suspendisse potenti.

Aliquam ut ex eget mi consequat hendrerit non in dui. Integer sed laoreet purus. Cras vel enim sit amet mi pulvinar egestas. Suspendisse luctus urna ac risus volutpat, quis sodales nunc luctus. Fusce ac quam feugiat, maximus mauris a, consequat sapien. In ut neque libero. Fusce nec interdum urna. Praesent ac tincidunt ante. Etiam aliquet malesuada justo sit amet ultricies. Donec eleifend velit sed pharetra dictum. Donec scelerisque ut magna id consequat. Nulla porta diam nisi, eget fermentum nibh blandit accumsan. Praesent sed luctus purus. Maecenas lacinia, sem vel efficitur cursus, sem nunc pulvinar urna, at consectetur nulla urna nec elit. Ut est tortor, commodo vel felis nec, pretium placerat metus. Nam pretium arcu justo, sit amet condimentum velit convallis in.

Proin non enim eu mauris ultricies luctus fringilla eget risus. Vivamus at lorem a tortor pellentesque eleifend ac non nibh. Nunc a aliquet libero. Phasellus leo elit, vulputate sit amet urna ut, pulvinar ultricies turpis. Maecenas molestie a nulla in tempus. Morbi ac egestas diam, in placerat augue. Maecenas ultricies varius neque eget semper.

Suspendisse venenatis leo ut quam varius, sit amet imperdiet ante imperdiet. Curabitur vitae sagittis mauris. Ut imperdiet justo sed elementum efficitur. Nulla vel vehicula nibh. Nullam varius augue sed eros vestibulum, id scelerisque augue blandit. Nullam ut arcu sagittis, fringilla purus eu, pulvinar justo. Aenean in nibh sit amet arcu tristique feugiat vitae sit amet odio. Mauris condimentum urna sed est vehicula finibus. Quisque metus tortor, fringilla a interdum vel, efficitur eu ligula. Ut id urna magna. Nulla tincidunt congue nisi, a tincidunt mi mattis non. Maecenas euismod consectetur libero vitae ornare. Quisque id dapibus neque. Vivamus eget.
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                            <div class="row vertically_center">


                                <?php
                                $name = 'questions_responses['.$number.'][received]['.$i.']';
                                $attributes = ['type' => 'number',
                                    'min' =>'0',
                                    'step' => '0.01'];
                                echo Form::ib_input(null, $name, !empty($answer_option->score) ? $answer_option->score : 0, $attributes);
                                ?>

                            </div>
                        </div>
                    </div>
                    <div class="row" style="width:100%;">
                        <h6>Feedback</h6>
                        <div class="col-xs-12 col-sm-12">
                            <?php echo Form::ib_textarea(null, 'questions_responses['.$number.'][title]', $question->title, [
                                'class' => 'questionnaire-builder-question',
                                'placeholder' => 'Examiner\'s comment',
                                'id'=> 'questionnaire-builder-question_'.$number,
                                'rows' => 7
                            ]);?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</li>
