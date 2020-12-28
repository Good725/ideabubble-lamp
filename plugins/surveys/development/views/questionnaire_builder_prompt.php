<li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-group-li" data-number="<?= $group_number ?>" data-type="prompt">
    <div class="col-xs-10" style="width: calc(100% - 2em);">
        <div class="border rounded p-2 bg-white">
            <div class="questionnaire-prompt-heading row p-0 vertically_center">
                <div class="col-xs-12 col-sm-1">
                    <span data-section_label="Text prompt name" class="questionnaire-group-name"
                          data-number="<?= $group_number ?>">
                    </span>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <?php
                    // Hidden fields
                    $attributes = $disabled ? ['disabled' => 'disabled'] : [];
                    echo Form::hidden('groups['.$group_number.'][id]', $group->id, $attributes);
                    $attributes['class'] = 'questionnaire-group-order';
                    echo Form::hidden('groups['.$group_number.'][order_id]', $group_number, $attributes);
                    echo Form::hidden('groups['.$group_number.'][type]', 'prompt', array('class' => 'questionnaire-group-type'));

                    // Name field
                    $attributes = [
                        'class' => 'questionnaire-builder-group',
                        'disabled' => $disabled,
                        'placeholder' => 'Enter text prompt title'
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
                        <span class="d-sm-none"><?= __('Show text') ?></span>
                        <span class="expanded-invert icon-angle-down"></span>
                    </button>
                </div>
            </div>

            <div class="collapse in" id="questionnaire-group-<?= $group_number ?>-collapsible">
                <ul class="questionnaire-group-questions">
                    <li class="row no-gutters vertically_center mt-2 questionnaire-li questionnaire-prompt-li">
                    <?php
                    echo Form::ib_textarea(null, 'prompts['.$group_number.'][text]', @$question->title, [
                        'class' => 'ckeditor-simple questionnaire-builder-question prompt-text',
                        'placeholder' => 'Enter text prompt',
                        'id'=> 'questionnaire-builder-prompt_'. $group_number
                    ]);
                    ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xs-1 text-center" style="width: 2em;">
        <button type="button" class="button--plain questionnaire-group-remove text-decoration-none"
                data-number="<?= $group_number ?>"
                data-toggle="modal" data-target="#question-group-delete-modal">
            <span class="icon_close" style="font-size: 1.5em;"></span>
        </button>
    </div>
</li>