<?php
$default_attributes = [];
if (isset($placeholder)) {
    $default_attributes['placeholder'] = $placeholder;
}
$name_attributes = isset($name_attributes) ? $name_attributes + $default_attributes : $default_attributes;

$publish_field = isset($publish_field) ? $publish_field : 'publish';
?>
<div class="form-row gutters vertically_center">
    <div class="<?= $publish_field !== false ? 'col-xs-9 col-sm-10 col-md-11' : 'col-sm-12' ?>">
        <?= Form::ib_input(
            null,
            isset($name_field)  ? $name_field  : 'name',
            isset($name) ? $name : null,
            $name_attributes
        ); ?>
    </div>

    <?php if ($publish_field !== false): ?>
        <div class="col-xs-3 col-sm-2 col-md-1">
            <input type="hidden" name="<?= $publish_field ?>" value="0" />
            <label title="<?= __('Publish') ?>">
                <span class="sr-only"><?= __('Publish') ?></span>
                <?= Form::ib_checkbox_switch(null, $publish_field, 1, (!isset($published) || $published == 1)); ?>
            </label>
        </div>
    <?php endif; ?>
</div>