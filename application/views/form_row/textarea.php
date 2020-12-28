<div class="form-group">
    <div class="<?= $form->label_size ?> control-label">
        <label for="<?= $attributes['id'] ?>"><?= htmlspecialchars($label) ?></label>
    </div>

    <div class="<?= $form->input_size_large ?>">
        <?= Form::ib_textarea(null, $name, $body, $attributes, $double_encode); ?>
    </div>
</div>