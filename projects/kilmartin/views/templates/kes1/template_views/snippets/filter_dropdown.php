<?php
$checked_items      = (isset($args) && isset($args[$filter['name'].'_ids'])) ? $args[$filter['name'].'_ids'] : array();
$is_filtered        = (!empty($is_filtered) || !empty($checked_items));
$checked_item_names = array();
$filter['options']  = is_array($filter['options']) ? $filter['options'] : $filter['options']->as_array();
?>

<?php ob_start(); ?>
    <ul class="dropdown-menu features-dropdown-menu-id" role="menu">
        <?php foreach ($filter['options'] as $option): ?>
            <li class="search-filter-dropdown-item">
                <?php
                $option = is_array($option) ? $option : $option->as_array();
                $checked = in_array($option['id'], $checked_items);
                $attributes = array(
                    'class' => 'search-filter-checkbox',
                    'id' => 'filter_'.$filter['label'].'_id_'.$option['id'],
                    'data-type' => $filter['name'],
                    'data-id' => $option['id']
                );

                if ($checked) {
                    $checked_item_names[$option['id']] = $option[$filter['option_name']];
                }

                echo Form::ib_checkbox($option[$filter['option_name']], null, null, $checked, $attributes);
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php $list_html = ob_get_clean(); ?>

<div class="dropdown search-filter-dropdown<?= count($checked_items) ? ' filter-active' : '' ?>" data-autodismiss="false">
    <button class="btn-dropdown" type="button" data-toggle="dropdown" aria-expanded="false">
        <span class="search-filter-label"><?= $filter['label'] ?></span>
        <span class="search-filter-amount hidden--mobile"><?= count($checked_items) ? count($checked_items) : '' ?></span>
        <span class="arrow_caret-down search-filter-dropdown-icon"></span>

        <span class="search-filter-selected_items hidden--tablet hidden--desktop">
            <?php foreach ($checked_item_names as $id => $name): ?>
                <span data-id="<?= $id ?>"><?= $name ?></span>
            <?php endforeach; ?>
        </span>
    </button>

    <?= $list_html ?>
</div>

<?php $filter_count += count($checked_items); ?>