<div class="form-group">
    <div class="<?= $form->label_size ?> control-label">
        <label for="<?= $display_attributes['id'] ?>"><?= htmlspecialchars($label) ?></label>
    </div>

    <div class="<?= $form->input_size_small ?>">
        <?= Form::ib_datepicker(null, $name, $value, $hidden_attributes, $display_attributes, $args); ?>
    </div>
</div>