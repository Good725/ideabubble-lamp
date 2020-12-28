<?php if (count($list)): ?>
    <?php $list_class = ($level == 1) ? 'menu--'.preg_replace('/\W+/', '', strtolower(strip_tags($list[0]['category']))) : 'level'.$level; ?>

    <ul class="<?= $list_class ?>">
        <?php foreach ($list as $item): ?>
            <li id="menu-item--<?= $item['id'] ?>" class="level_<?= $level ?><?= $item['has_sub'] ? ' has_submenu' : '' ?>">
                <a href="<?= menuhelper::get_link($item) ?>" target="<?= $item['menus_target'] ?>"><?= $item['title'] ?></a>

                <?php if ($item['has_sub']): ?>
                    <button type="button" class="submenu-expand"></button>
                <?php endif; ?>

                <?php
                // "5" is an arbitrary level chosen to avoid infinite loops
                if ($level < 5) {
                    $sublist = menuhelper::submenu($item);
                    echo View::factory('front_end/snippets/menu_list')->set('list', $sublist)->set('level', $level + 1);
                }
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>