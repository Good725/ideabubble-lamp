<div class="survey-input-wrapper survey-input-radio-group">
    <?php
    $options = $question->answer->options->find_all_published();
    $name = (isset($name) && $name !== null) ? $name : 'temporary_'.$question->id;
    ?>
    <?php foreach ($options as $option): ?>
        <div>
            <label class="survey-radio-input-wrap">
                <input
                    type="checkbox"
                    name="<?= $name ?>[]"
                    class="survey-input<?= $question->required ? ' validate[groupRequired[q'.$question->id.']]' : '' ?>"
                    id="temporary_<?= $question->id ?>_<?= $option->id ?>" value="<?= $option->id ?>" />
                <span class="survey-input-helper"></span>
            </label>
            <label for="temporary_<?= $question->id ?>_<?= $option->id ?>"><?= $option->label ?></label>
        </div>
    <?php endforeach; ?>
</div>