<div class="survey-input-wrapper survey-input-textarea-wrapper">
    <textarea
        name="<?= (isset($name) && $name !== null) ? $name : 'temporary_'.$question->id ?>"
        class="survey-input<?= $question->required ? ' validate[required]' : '' ?>"
        id="temporary_<?= $question->id ?>"
    ><?= isset($response) ? htmlspecialchars(trim($response)) : '' ?></textarea>
</div>
