<div class="form-group">
    <div class="<?= $form->label_size ?> control-label">
        <label for="<?= $attributes['id'] ?>"><?= htmlspecialchars($label) ?></label>
    </div>

    <div class="<?= $form->input_size_small ?>">
        <div
            class="btn-group btn-group-slide"
            data-toggle="buttons"
            id="<?= $attributes['id'] ?>"
            <?= $form->popover_attributes($args) ?>
        >
            <label class="btn btn-plain<?= $checked ? ' active' : '' ?>">
                <input
                    type="radio"
                    name="<?= $name ?>"
                    value="1"
                    id="<?= $attributes['id'] ?>-yes"
                    <?= $checked ? ' checked="checked"' : '' ?>
                    ><?= __('Yes') ?>
            </label>
            <label class="btn btn-plain<?= !$checked ? ' active' : '' ?>">
                <input
                    type="radio"
                    name="<?= $name ?>"
                    value="0"
                    id="<?= $attributes['id'] ?>-no"
                    <?= !$checked ? ' checked="checked"' : '' ?>
                    ><?= __('No') ?>
            </label>
        </div>
    </div>
</div>