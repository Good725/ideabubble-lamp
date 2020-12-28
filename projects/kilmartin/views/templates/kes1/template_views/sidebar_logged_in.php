<?php if ( ! empty($sidebar->frontend_menus)): ?>
    <div class="db-sidebar">
        <ul class="sidebar-menu">
            <?php foreach ($sidebar->frontend_menus as $menu): ?>
                <li>
                    <a href="<?= URL::Site($menu['link']); ?>"<?= strstr($menu['link'], $current_action) ? ' class="active"' : '' ?>>
                        <span class="<?= $menu['icon'] ?>"></span>&nbsp;
                        <?= $menu['name'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>