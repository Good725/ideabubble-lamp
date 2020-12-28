<div class="form-filter dropdown" data-autodismiss="false">
    <div class="form-input form-filter-input d-flex" data-toggle="dropdown" aria-expanded="false">
        <span class="d-block nowrap mr-1">
            <span class="icon-filter"></span>
            <span class="form-filter-label"><?= __('Filter by') ?></span>
        </span>

        <span class="form-filter-selected nowrap-ellipsis">
            <span class="form-filter-selected-item form-filter-selected-item-template hidden
                         d-inline-block border bg-light mr-1 rounded">
                <input type="hidden" class="form-filter-selected-field" />

                <strong class="filter-item-label"></strong>

                <span class="filter-item-title"></span>

                <button type="button" class="filter-item-close button--plain">
                    <span class="icon_close"></span>
                </button>
            </span>
        </span>
    </div>

    <div class="dropdown-menu">
        <div class="form-filter-menu">
            <div class="p-2 mb-3 border-bottom text-center text-uppercase">
                <strong style="font-size: .8125rem;">Filter by</strong>
            </div>

            <ul class="form-filter-menu-list dropdown-menu-list list-unstyled">
                <?php foreach ($options as $group): ?>
                    <li
                        class="form-filter-category"
                        data-group="<?= $group['name'] ?>[]"
                        <?php if (count($group['options']) > 10): ?>
                            <?php
                            $data = [];
                            foreach ($group['options'] as $id => $label) {
                                $html = empty($group['html']) ? htmlspecialchars($label) : $label;
                                $is_html = !empty($group['html']);
                                $data[] = compact('id', 'html', 'is_html');
                            }
                            ?>
                            data-options="<?= htmlspecialchars(json_encode($data)) ?>"
                        <?php endif; ?>
                    >
                        <a href="#"><?= htmlspecialchars($group['label']) ?></a>
                    </li>

                    <?php
                    $selected_options = isset($group['selected'])
                        ? (is_array($group['selected']) ? $group['selected'] : [$group['selected']])
                        : false;
                    ?>

                    <?php $i = 0; ?>
                    <?php foreach ($group['options'] as $value => $label): ?>
                        <?php
                        if ($i >= 10) {
                            continue;
                        }
                        $is_selected = ($selected_options !== false && in_array($value, $selected_options));
                        ?>

                        <li data-group="<?= $group['name'] ?>[]" data-id="<?= $value ?>"<?= $is_selected ? ' class="selected"' : '' ?>>
                            <button type="button" class="btn-link w-100 text-left">
                                <?php
                                $label = empty($group['html']) ? htmlspecialchars($label) : $label;
                                echo Form::ib_checkbox($label, $group['name'].'[]', $value, $is_selected); ?>
                            </button>
                        </li>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="form-filter-submenu hidden">
            <div class="p-2 border-bottom text-center">
                <button type="button" class="btn-link left form-filter-submenu-back">
                    <span class="icon-angle-left"></span> <?= __('Back') ?>
                </button>

                <strong style="font-size: .8125rem;" class="form-filter-submenu-title text-uppercase">Filter by</strong>
            </div>

            <div class="px-4 py-2">
                <?= Form::ib_input(null, null, null, ['class' => 'form-filter-search', 'placeholder' => 'Search']) ?>
            </div>

            <ul class="form-filter-submenu-list dropdown-menu-list list-unstyled"></ul>

            <button type="button" class="form-filter-submenu-load_more btn btn-link bg-white border-0 w-100 hidden">Load more</button>
        </div>
    </div>
</div>
