<div class="form-group">
    <div class="<?= $form->label_size ?> control-label">
        <label for="<?= $attributes['id'] ?>"><?= htmlspecialchars($label) ?></label>
    </div>

    <div class="<?= $form->input_size_small ?>">
        <?= Form::ib_daterangepicker($start_name, $end_name, $start_date, $end_date, $attributes, $args) ?>
    </div>
</div>
