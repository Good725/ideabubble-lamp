<div class="survey-input-wrapper survey-input-text-wrapper">
    <input
        type="text"
        name="<?= (isset($name) && $name !== null) ? $name : 'temporary_'.$question->id ?>"
        value="<?= isset($response) ? htmlspecialchars($response) : '' ?>"
        class="form-input survey-input<?= $question->required ? ' validate[required]' : '' ?>"
        id="temporary_<?= $question->id ?>"
        />
</div>
