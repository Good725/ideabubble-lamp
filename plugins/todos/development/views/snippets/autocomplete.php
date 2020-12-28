<?php $type_plural = isset($type_plural) ? $type_plural : $type.'s';
$hidden_value = $hidden_value ?? "";
$autocomplete_input_value = $autocomplete_input_value ?? "";?>

<div class="form-group" id="todo-<?= $type_plural ?>">
    <label for="todo-<?= $type ?>-search-autocomplete-id1" class="control-label text-left col-md-2">
        <?= $label ?>
    </label>

    <div class="col-md-8">
        <div class="form-group vertically_center">
            <div class="<?= $column1_grid ?>">
                <input type="hidden" id="todo-<?= $type ?>-search-autocomplete-id1"
                       name="todo-<?= $type ?>-search-autocomplete-id1" <?= ($hidden_value !== "") ? "value='{$hidden_value}'" : ''; ?>/>

                <?php
                $attributes = [
                    'id' => 'todo-'.$type.'-search-autocomplete1',
                    'disabled' => !empty($disabled),
                    'placeholder' => isset($placeholder) ? $placeholder : __('Search for a $1', array('$1' => $type))
                ];
                ?>

                <?= Form::ib_input(null, null, $autocomplete_input_value, $attributes, array('icon' => '<span class="icon-search"></span>')) ?>
            </div>

            <?php if($autocomplete_list == true) : ?>
                <div class="<?= $column2_grid ?>">
                    <button
                        type="button"
                        id="todo-<?= $type ?>-add"
                        class="btn btn--full form-btn add"
                        <?= !empty($disabled) ? ' disabled="disabled"' : '' ?>
                    ><?=__('Add')?></button>
                </div>
            <?php endif; ?>
        </div>

        <div id="todo-<?= $type_plural ?>-list">
            <?php if (is_array($list)): ?>
                <?php foreach ($list as $item): ?>
                    <div class="form-group todo-<?= $type ?>">
                        <div class="<?= $column1_grid ?>">
                            <input type="hidden" name="has_<?= $type_plural ?>[]" class="<?= $type ?>-id" value="<?= $item['id']?>" />
                            <span class="<?= $type ?>-name"><?= $item['name'] ?></span>
                        </div>

                        <div class="<?= $column2_grid ?> text-center">
                            <button
                                type="button"
                                class="btn-link remove"
                                <?= !empty($disabled) ? ' disabled="disabled" style="cursor: not-allowed;"' : '' ?>
                            ><?= __('remove') ?></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group todo-<?= $type ?> hidden" id="todo-<?= $type ?>-template">
            <div class="<?= $column1_grid ?>">
                <input type="hidden" name="has_<?= $type_plural ?>[]" class="<?= $type ?>-id" />
                <span class="<?= $type ?>-name"></span>
            </div>

            <div class="<?= $column2_grid ?> text-center">
                <button type="button" class="btn-link remove"><?= __('remove') ?></button>
            </div>
        </div>
    </div>
</div>

<?php
unset($type);
unset($type_plural);
unset($placeholder);
unset($label);
unset($list);
unset($hidden_value);
unset($autocomplete_input_value);
?>