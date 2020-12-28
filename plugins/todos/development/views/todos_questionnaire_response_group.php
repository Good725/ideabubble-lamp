<li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-group-li" data-number="<?= $group_number ?>">
    <div class="col-xs-10" style="width: calc(100% - 2em);">
        <div class="border rounded p-2 bg-light">
            <div class="questionnaire-group-heading row p-0 vertically_center">
                <div class="col-xs-12 col-sm-1">
                    <span data-section_label="Group" class="questionnaire-group-name"
                          data-number="<?= $group_number ?>"></span>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <?php
                    // Hidden fields
                    $attributes = $disabled ? ['disabled' => 'disabled'] : [];
                    echo Form::hidden('groups['.$group_number.'][id]', null, $attributes);
                    $attributes['class'] = 'questionnaire-group-order';
                    echo Form::hidden('groups['.$group_number.'][order_id]', $group_number, $attributes);

                    // Name field
                    $attributes = [
                        'class' => 'questionnaire-builder-group',
                        'disabled' => $disabled,
                        'placeholder' => 'Enter group name'
                    ];
                    echo Form::ib_input(null, 'groups['.$group_number.'][title]', '', $attributes);
                    ?>
                </div>

                <div class="col-xs-12 col-sm-offset-4 col-sm-1">
                    <button type="button"
                            class="btn-link p-0 w-100"
                            data-toggle="collapse" data-target="#questionnaire-group-<?= $group_number ?>-collapsible"
                            aria-expanded="true">
                        <span class="d-sm-none"><?= __('Show questions') ?></span>
                        <span class="expanded-invert icon-angle-down"></span>
                    </button>
                </div>
            </div>

            <div class="collapse in" id="questionnaire-group-<?= $group_number ?>-collapsible">
                <ul class="questionnaire-group-questions">
                     <?php
                        $question = new Model_Question();
                        $has_question = new Model_SurveyHasQuestion();
                        $disabled = true;
                        $number = 0;
                        include 'todos_questionnare_response_question.php';
                        ?>
                </ul>
                <div class="row">
                    <br/>
                    <div class="form-action-group text-center">
                        <button type="button" class="btn btn-secondary submit_button" name="action" value="save">Submit Grade</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</li>