<div <?= html::attributes($attributes) ?>>
    <?php $id_prefix = isset($input_attributes['id']) ? $input_attributes['id'] : ''; ?>

    <?php foreach ($options as $value => $label): ?>
        <?php $is_selected = (is_array($selected) && in_array($value, $selected)) || (!is_array($selected) && $value == $selected); ?>
        <?php
        // If individual options have their own names
        if (is_array($label)) {
            if (isset($label['name'])) {
                $input_attributes['name'] = $label['name'];
            }
            $label = isset($label['label']) ? $label['label'] : '';
        }
        ?>

        <label class="<?= $type ?>-icon">
            <input
                <?php
                if (!empty($id_prefix)) {
                    $input_attributes['id'] = $id_prefix . '-' . preg_replace('/[\s_]/', '_', htmlentities($value));
                }
                echo HTML::attributes($input_attributes);
                ?>
                value="<?= $value ?>"
                <?= $is_selected ? 'checked="checked"' : '' ?>
                />
            <span class="<?= $type ?>-icon-unchecked btn"><span><?= $label ?></span></span>
            <span class="<?= $type ?>-icon-checked btn <?= !empty($selected_class) ? $selected_class : 'btn-primary' ?>"><span><?= $label ?></span></span>
        </label>
    <?php endforeach; ?>
</div>