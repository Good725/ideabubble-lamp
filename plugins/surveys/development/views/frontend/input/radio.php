<div class="survey-input-wrapper survey-input-radio-group">
    <?php $options = $question->answer->options->find_all_published(); ?>
    <?php foreach ($options as $option): ?>
        <div>
            <label class="survey-radio-input-wrap">
                <input
                    type="radio"
                    name="<?= (isset($name) && $name !== null) ? $name : 'temporary_'.$question->id ?>[]"
                    value="<?= $option->id ?>"
                    class="survey-input<?= $question->required ? ' validate[groupRequired[q'.$question->id.']]' : '' ?>"
                    id="temporary_<?= $question->id ?>_<?= $option->id ?>"
                    <?= (!empty($response) && $response == $option->id) ? ' checked="checked"' : '' ?>
                    />
                <span class="survey-input-helper"></span>
            </label>
            <label for="temporary_<?= $question->id ?>_<?= $option->id ?>"><?= htmlentities($option->label) ?></label>
        </div>
    <?php endforeach; ?>
</div>