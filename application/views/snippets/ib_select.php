<?php
$args['icon']        = isset($args['icon'])        ? $args['icon']        : '';
$args['right_icon']  = isset($args['right_icon'])  ? $args['right_icon']  : '';
$arrow_position      = isset($arrow_position)      ? $arrow_position      : '';
$is_active           = isset($is_active)           ? $is_active           : false;
$is_multiple         = isset($is_multiple)         ? $is_multiple         : false;
$multiselect_options = isset($multiselect_options) ? $multiselect_options : '';
$required            = isset($required)            ? $required            : false;
$select_html         = isset($select_html)         ? $select_html         : '';

$overlay = !empty($args['overlay']) ? '<span class="form-input-overlay">'.$args['overlay'].'</span>' : '';

?>

<?php if ((!empty($args['icon']) || !empty($args['right_icon'])) && $label): ?>
    <!-- Icon and label -->

    <label class="input_group">
        <?php if (!empty($args['icon'])): ?>
            <span class="input_group-icon"><?= $args['icon'] ?></span>
        <?php endif; ?>

        <span
            class="form-select<?= $is_multiple ? ' form-select--multiple' : '' ?><?= (!empty($args['plain']) ? ' form-select--plain' : '') ?><?= $arrow_position ?>"
            <?= $multiselect_options ?>
        >
            <?= $overlay ?>

            <span class="form-input form-input--select form-input--pseudo<?= $is_active ? ' form-input--active' : '' ?>">
                <span class="form-input--pseudo-label<?= $required ? ' label--mandatory' : '' ?>"><?= $label ?></span>
                <?= $select_html ?>
            </span>
        </span>

        <?php if (!empty($args['right_icon'])): ?>
            <span class="input_group-icon"><?= $args['right_icon'] ?></span>
        <?php endif; ?>
    </label>

<?php elseif (!empty($args['icon']) || !empty($args['right_icon'])): ?>
    <!-- Icon and no label -->

    <label class="input_group">
        <?php if (!empty($args['icon'])): ?>
            <span class="input_group-icon"><?= $args['icon'] ?></span>
        <?php endif; ?>

        <span class="form-select<?= $is_multiple ? ' form-select--multiple' : '' ?><?= $arrow_position ?>"<?= $multiselect_options ?>>
            <?= !empty($args['mask'])  ? '<span class="form-select-mask">'.$args['mask'].'</span>' : '' ?>

            <?= $overlay ?>

            <?php if ($is_multiple): ?>
                <span class="form-input form-input--select form-input--pseudo"><?= $select_html ?></span>
            <?php else: ?>
                <?= $select_html ?>
            <?php endif; ?>
        </span>

        <?php if (!empty($args['right_icon'])): ?>
            <span class="input_group-icon"><?= $args['right_icon'] ?></span>
        <?php endif; ?>
    </label>

<?php elseif (trim($label)): ?>
    <!-- Label and no icon -->

    <label
        class="form-select<?= $is_multiple ? ' form-select--multiple' : '' ?><?= (!empty($args['plain']) ? ' form-select--plain' : '') ?><?= $arrow_position ?>"
        <?= $multiselect_options ?>
    >
        <?= $overlay ?>

        <span class="form-input form-input--select form-input--pseudo<?= $is_active ? ' form-input--active' : '' ?>">
            <span class="form-input--pseudo-label<?= $required ? ' label--mandatory' : '' ?>"><?= $label ?></span>
            <?= $select_html ?>
        </span>
    </label>

<?php else: ?>
    <!-- No label or icon -->

    <label class="form-select<?= $is_multiple ? ' form-select--multiple' : '' ?><?= $arrow_position ?>"<?= $multiselect_options ?>>
        <?= !empty($args['mask'])  ? '<span class="form-select-mask">'.$args['mask'].'</span>' : '' ?>

        <?= $overlay ?>

        <?php if ($is_multiple): ?>
            <span class="form-input form-input--select form-input--pseudo"><?= $select_html ?></span>
        <?php else: ?>
            <?= $select_html ?>
        <?php endif; ?>
    </label>
<?php endif; ?>