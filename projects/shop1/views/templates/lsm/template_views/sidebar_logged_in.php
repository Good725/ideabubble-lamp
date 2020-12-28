<div class="db-sidebar">
    <ul class="sidebar-menu">
            <?php
                if ($sidebar->frontend_menus) foreach ($sidebar->frontend_menus as $menu) {
            ?>
                    <li class="<?= $menu['icon'] ?>">
                        <a href="<?php echo URL::Site($menu['link']); ?>" class="<?php echo strstr($menu['link'], $current_action) ? 'active' : ''; ?>"><?php echo $menu['name']; ?></a>
                    </li>
            <?php
                }
            ?>
    </ul>
</div>
