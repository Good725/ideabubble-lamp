<?php
$name = (isset($name) && $name !== null) ? $name : 'temporary_'.$question->id;
$value = isset($response) ? $response : '';
?>
<div class="survey-input-wrapper survey-input-yes_or_no">
    <input type="hidden" name="<?= $name ?>" value="" />

    <div
        class="survey-input-yes_or_no-options"
        id="survey-question-<?= $question->id ?>-options"
    >
        <?php
        $options = ['yes' => 'Yes', 'no' => 'No'];
        $class = 'survey-input'.($question->required ? ' validate[groupRequired[q'.$question->id.']]' : '');
        $attributes = ['class' => $class, 'id' => 'survey-question-' . $question->id . '-option'];

        if ($question->child_survey_id) {
            $attributes['data-child_id'] = $question->child_survey_id;
        }

        $group_attributes = ['class' => 'stay_inline'];
        echo Form::btn_options($name, $options, $value, false, $attributes, $group_attributes); ?>
    </div>
</div>