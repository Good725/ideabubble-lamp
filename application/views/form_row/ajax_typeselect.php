<div class="form-group">
    <div class="<?= $form->label_size ?> control-label">
        <label for="<?= $attributes['id'] ?>"><?= htmlspecialchars($label) ?></label>
    </div>

    <div class="<?= $form->input_size_small ?>">
        <div class="form-typeselect-wrapper">
            <?= Form::input($name, $hidden_value, $hidden_attributes); ?>

            <?= Form::ib_input(null, $name.'-display', $display_value, $display_attributes, $args); ?>

            <button type="button" class="btn-link form-typeselect-clear">
                <span class="fa fa-remove-circle icon-remove-circle"></span>
            </button>
        </div>
    </div>
</div>