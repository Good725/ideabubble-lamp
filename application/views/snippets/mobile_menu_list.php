<?php foreach ($menu as $lv1_item): ?>
    <li
        class="level_<?= $lv ?><?= $lv1_item['submenu']  ? ' has_submenu' : '' ?>"
        <?php if (isset($lv1_item['category'])): ?>
            data-menu="<?= $lv1_item['category'] ?>"
        <?php endif; ?>
    >
        <a<?= menuhelper::attributes($lv1_item) ?>><?= $lv1_item['title'] ?></a>

        <?php if (!empty($lv1_item['submenu'])): ?>
            <button type="button" class="button--plain submenu-expand">
                <span class="sr-only"><?= __('expand/collapse') ?></span>
                <span class="submenu-expand-icon arrow_caret-right"></span>
            </button>

            <ul class="level<?= $lv + 1 ?>">
                <?php foreach ($lv1_item['submenu'] as $lv2_item): ?>
                    <li class="level_<?= $lv + 1 ?><?= $lv2_item['submenu']  ? ' has_submenu' : '' ?>">
                        <a<?= menuhelper::attributes($lv2_item) ?>>
                            <span><?= $lv2_item['title'] ?></span>
                            <?php if (!empty($lv2_item['submenu'])): ?>
                                <span class="submenu-expand"><span class="arrow_caret-right"></span></span>
                                <?php
                                // Take note of Level 3 menus. These will be rendered later.
                                $lv3_menu_parents[] = $lv2_item;
                                ?>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>