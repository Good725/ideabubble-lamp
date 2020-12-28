<?php $colorpicker = !empty($args['colorpicker']) ? ' form-input--colorpicker' : '' ?>
<?php if (($args['icon'] || $args['right_icon']) && $label): ?>
    <?php // icon and label ?>
    <label class="input_group<?= (isset($args['fullwidth']) AND ! $args['fullwidth']) ? ' autowidth' : '' ?><?= isset($args['group_class']) ? ' '.$args['group_class'] : '' ?>">
        <?php if ($args['icon']): ?>
            <span<?= HTML::attributes($args['icon_attributes']) ?>><?= $args['icon'] ?></span>
        <?php endif; ?>

        <span class="form-input form-input--text form-input--pseudo<?= $is_active ? ' form-input--active' : '' ?><?= ! empty($attributes['readonly']) ? ' readonly' : '' ?><?= ! empty($attributes['disabled']) ? ' disabled' : '' ?>">
            <span class="form-input--pseudo-label<?= ! empty($args['required']) ? ' label--mandatory' : '' ?>"><?= $label ?></span>
            <input<?= HTML::attributes($attributes) ?> />
        </span>

        <?php if ($args['right_icon']): ?>
            <span<?= HTML::attributes($args['right_icon_attributes']) ?>><?= $args['right_icon'] ?></span>
        <?php endif; ?>
    </label>
<?php elseif (trim($args['icon']) || trim($args['right_icon'])): ?>
    <?php // icon and no label ?>
    <label class="input_group<?= (isset($args['fullwidth']) AND ! $args['fullwidth']) ? ' autowidth' : '' ?><?= isset($args['group_class']) ? ' '.$args['group_class'] : '' ?>">
        <?php if (trim($args['icon'])): ?>
            <span<?= HTML::attributes($args['icon_attributes']) ?>><?= $args['icon'] ?></span>
        <?php endif; ?>

        <input<?= HTML::attributes($attributes) ?> />

        <?php if (trim($args['right_icon'])): ?>
            <span<?= HTML::attributes($args['right_icon_attributes']) ?>><?= $args['right_icon'] ?></span>
        <?php endif; ?>
    </label>
<?php elseif (trim($label) || $colorpicker): ?>
    <?php // label and no icon ?>
    <label class="form-input form-input--text form-input--pseudo<?= $is_active ? ' form-input--active' : '' ?><?= ! empty($attributes['readonly']) ? ' readonly' : '' ?><?= ! empty($attributes['disabled']) ? ' disabled' : '' ?><?= ( ! empty($args['type_select']) ? ' form-select' : '')?><?= $colorpicker ?><?= isset($args['group_class']) ? ' '.$args['group_class'] : '' ?>">
        <span class="form-input--pseudo-label<?= ! empty($args['required']) ? ' label--mandatory' : '' ?>"><?= $label ?></span>
        <input<?= HTML::attributes($attributes) ?> />
    </label>
<?php else: ?>
    <input <?= HTML::attributes($attributes)?> />
<?php endif; ?>

<?php
if ($attributes['type'] == 'password' && !empty($args['password_meter'])) {
    include 'password_strength.php';
}
?>