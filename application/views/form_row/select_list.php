<div class="form-group">
    <div class="<?= $form->label_size ?> control-label">
        <label for="<?= $attributes['id'] ?>"><?= htmlspecialchars($label) ?></label>
    </div>

    <div
        class="<?= $form->input_size_small ?>"
        <?= $form->popover_attributes($args) ?>
    >
        <?= Form::ib_select(null, $name, $options, $selected, $attributes, $args); ?>
    </div>
</div>