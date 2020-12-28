<div class="banner-search-column banner-search-column--<?= $field['type'] ?>">
    <div class="focus_group">
        <input
            type="text"
            class="form-input<?= !empty($field['required']) ? ' validate[required]' : '' ?>"
            id="banner-search-<?= $field['type'] ?>"
            placeholder="<?= $field['placeholder'] ?>"
            autocomplete="off"
            data-drilldown="#<?= (isset($field['use_columns'])) ? $field['use_columns'] : $field['type'] ?>-drilldown"
            data-type_search="<?= $field['type_search'] ?>" />

        <label for="banner-search-<?= $field['type'] ?>"><?= $field['label'] ?></label>
    </div>
</div>

<?php if (!empty($field['columns'])): ?>
    <div class="search-drilldown search-drilldown--<?= $field['type'] ?>" id="<?= $field['type'] ?>-drilldown">
        <button type="button" class="search-drilldown-close button--plain"></button>

        <?php foreach ($field['columns'] as $column): ?>
            <div class="search-drilldown-column search-drilldown-column--<?= $column['type'] ?>">
                <h3><?= $column['label'] ?></h3>

                <ul
                    class="list-unstyled"
                    id="<?= $field['type'] ?>-drilldown-<?= $column['type'] ?>-list"
                    <?= !empty($column['filtered_by']) ? 'data-filtered_by="'.$column['filtered_by'].'"' : '' ?>
                >
                    <?php if (!empty($column['items'])): ?>
                        <?php foreach ($column['items'] as $item): ?>
                            <li<?= !empty($column['filtered_by']) ? ' style="display: none;"' : '' ?>>
                                <a href="#" class="<?= $field['type'] ?>" data-id="<?= $item['id'] ?>">
                                    <?= htmlentities($item[(isset($column['items_name_key']) ? $column['items_name_key'] : 'name')]) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($column['all_text'])): ?>
                        <li><a href="#" data-id="all"><?= $column['all_text'] ?></a></li>
                    <?php endif; ?>
                </ul>

                <p class="search-drilldown-no_results<?= (!empty($column['items']) || !isset($column['items'])) ? ' hidden' : '' ?>"><?= __('No results found') ?></p>

                <?php if (isset($column['relies_on_text'])): ?>
                    <p class="search-drilldown-awaiting_selection"><?= $column['relies_on_text'] ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
