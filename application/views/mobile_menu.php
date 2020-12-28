<?php
$user_orm = ORM::factory('users', isset($user['id']) ? $user['id'] : null);
$plugin_submenu_parents = $lv3_menu_parents = array();
$active_plugin_submenu = false;
$class = isset($class) ? $class : 'hidden--tablet hidden--desktop hidden-sm hidden-md hidden-lg';
$id = isset($id) ? $id : 'mobile-menu';

$sidebar_menu = empty($sidebar_menu) ? MenuArea::factory()->generate_icon_links() : $sidebar_menu;

foreach ($sidebar_menu as $sidebar_plugin) {
    // Take note of plugins with submenus for when the submenus are added later
    if ($sidebar_plugin['has_submenu'] && $sidebar_plugin['submenu']) {
        $plugin_submenu_parents[] = $sidebar_plugin;

        if ($sidebar_plugin['active']) {
            $active_plugin_submenu = $sidebar_plugin;
        }
    }
}
$class .= $active_plugin_submenu ? ' mobile-menu--level3_expanded' : '';
?>

<div class="mobile-menu <?= $class ?>" id="<?= $id ?>">
    <?php if ($user || Settings::Instance()->get('frontend_login_link')): ?>
        <div class="mobile-menu-top">
            <div class="mobile-menu-top-heading">
                <?php if ($user): ?>
                    <span class="mobile-menu-top-avatar">
                        <img src="<?= URL::get_avatar($user['id']); ?>" alt="" width="32" height="32" />
                    </span>

                    <div class="mobile-menu-top-title">
                        <?php if (trim($user['name'])): ?>
                            <strong class="mobile-menu-top-username"><?= $user['name'].' '.$user['surname'] ?></strong>
                        <?php else: ?>
                            <a href="/admin/profile/edit?section=contact"><?= __('update your profile') ?></a>
                        <?php endif; ?>

                        <?php if ($user['registered']): ?>
                            <div class="mobile-menu-top-subtitle">
                                <small><?= __('Member since $1', array('$1' => IbHelpers::relative_time_with_tooltip($user['registered']))) ?></small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <a href="/admin/login/logout" class="mobile-menu-top-action"><?= __('Log out') ?></a>
                    </div>
                <?php else: ?>
                    <span class="mobile-menu-top-avatar">
                        <span class="icon_profile"></span>
                    </span>

                    <span class="mobile-menu-top-actions">
                        <?php
                        if (Settings::instance()->get('engine_enable_external_register')) {
                            echo __('$1 or $2', array(
                                '$1' => '<a href="/admin/login?mode=signup&redirect=/'.$page_data['name_tag'].'"><strong>'.__('Sign up').'</strong></a>',
                                '$2' => '<a href="/admin/login?mode=login&redirect=/'. $page_data['name_tag'].'"><strong>'.__('Log in').'</strong></a>'
                            ));
                        } else {
                            echo '<a href="/admin/login?redirect=/'.$page_data['name_tag'].'"><strong>'.__('Log in').'</strong></a>';
                        }
                        ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php /* Show plugins and other permission-controlled links */ ?>
    <?php if ($user): ?>
        <?php
        $settings_instance       = Settings::instance();
        $auth_instance           = Auth::instance();
        $has_action_button_1     = ($auth_instance->has_access('cms_action_button_1')   && trim($settings_instance->get('cms_heading_button_text'))   && trim($settings_instance->get('cms_heading_button_link')));
        $has_action_button_2     = ($auth_instance->has_access('cms_action_button_2')   && trim($settings_instance->get('cms_heading_button_text_2')) && trim($settings_instance->get('cms_heading_button_link_2')));
        $has_view_website_button = ($auth_instance->has_access('view_website_frontend') && $settings_instance->get('view_website') == 1 && !empty($is_backend));
        ?>

        <?php if ($has_action_button_1 || $has_action_button_2): ?>
            <div class="row mobile-menu-action_buttons">
                <?php if ($has_action_button_1): ?>
                    <div class="form-group">
                        <a class="btn btn-primary-outline button button--continue button--full inverse" href="<?= $settings_instance->get('cms_heading_button_link') ?>">
                            <?= $settings_instance->get('cms_heading_button_text') ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($has_action_button_2): ?>
                    <div class="form-group">
                        <a class="btn btn-primary-outline button button--continue button--full inverse"  href="<?= $settings_instance->get('cms_heading_button_link_2') ?>">
                            <span><?= $settings_instance->get('cms_heading_button_text_2') ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mobile-menu-list mobile-menu-userlinks" id="mobile-menu-userlinks">
            <ul>
                <li class="level_1 has_submenu expanded">
                    <a href="/admin" class="hidden--tablet hidden--desktop">
                        <?= __('My Account') ?>
                    </a>

                    <?php if (!empty($sidebar_menu) || !empty($userlinks)): ?>
                        <ul class="level2">
                            <li class="level_2 has_icon">
                                <a href="<?= !empty($is_backend) ? '/admin' : '/' ?>">
                                    <span class="plugin-menu-icon">
                                        <?= Ibhelpers::svg_sprite('home') ?>
                                    </span>
                                    <?= __('Home') ?>
                                </a>
                            </li>

                            <?php foreach ($sidebar_menu as $sidebar_plugin): ?>
                                <?php if (strtolower($sidebar_plugin['name']) != 'profile'): ?>
                                    <li class="level_2 has_icon<?= $sidebar_plugin['has_submenu'] ? ' has_submenu' : '' ?><?= $sidebar_plugin['active'] ? ' active' : '' ?>">
                                        <a href="/admin/<?= $sidebar_plugin['url'] ?>" data-id="plugin-<?= $sidebar_plugin['name'] ?>">
                                            <span class="plugin-menu-icon"><?= $sidebar_plugin['has_icon'] ? $sidebar_plugin['icon_html'] : '' ?></span>

                                            <?= $sidebar_plugin['name'] ?>

                                            <?php if ($sidebar_plugin['has_submenu']): ?>
                                                <span class="submenu-expand"><span class="arrow_caret-right"></span></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php foreach ($userlinks as $userlink): ?>
                                <?php if (Auth::instance()->has_access($userlink['permission'])): ?>
                                    <li class="level_2 has_icon<?= (trim($userlink['url'], '/') == trim($_SERVER['REQUEST_URI'], '/')) ? ' active' : '' ?>">
                                        <a href="<?= $userlink['url'] ?>">
                                            <span class="plugin-menu-icon <?= $userlink['icon'] ?>"></span>
                                            <?= $userlink['text'] ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>

                <?php if ($user): ?>
                    <li class="level_1 has_submenu expanded">
                        <a href="/admin/profile/edit" class="hidden--tablet hidden--desktop">
                            <?= __('Profile') ?>
                        </a>

                        <ul class="level2">
                            <?php if (Auth::instance()->has_access('settings')): ?>
                                <li class="level_2 has_icon">
                                    <a href="/admin/settings">
                                        <span class="plugin-menu-icon">
                                            <?= Ibhelpers::svg_sprite('settings') ?>
                                        </span>

                                        <?= __('Settings') ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <li class="level_2 has_icon has_submenu">
                                <a href="/admin/profile/edit?section=contact" data-id="plugin-Profile">
                                    <span class="plugin-menu-icon">
                                        <?= Ibhelpers::svg_sprite('profile') ?>
                                    </span>

                                    <?= __('Profile') ?>
                                    <span class="submenu-expand"><span class="arrow_caret-right"></span></span>
                                </a>
                            </li>

                            <li class="level_2 has_icon">
                                <a href="/admin/login/logout">
                                    <span class="plugin-menu-icon">
                                        <?= IbHelpers::svg_sprite('logout'); ?>
                                    </span>

                                    <?= __('Log out') ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="mobile-menu-list">
        <?php
        // If the user is logged-out or has "front end" as their access type, show the menus-plugin links
        $show_header_menus = $header_menu_1 = $header_menu_2 = $header_menu = $header_nav = false;
        $lv = 1;
        if (!$user_orm->id || $user_orm->role->access_type == 'Front end') {
            $header_menu_1     = Menuhelper::get_nested_menu('header 1');
            $header_menu_2     = Menuhelper::get_nested_menu('header 2');
            $header_menu       = !empty($is_backend) ? array() : Menuhelper::get_nested_menu('header'); // This only shows on the front end
            $header_nav        = MenuHelper::get_nested_menu('top nav');

            $show_header_menus = ($header_menu_1 || $header_menu_2 || $header_menu);
        }
        ?>

        <?php if ($show_header_menus || !empty($has_view_website_button)): ?>
            <ul>
                <?php
                if ($show_header_menus) {
                    $menu = $header_menu_1;
                    include 'snippets/mobile_menu_list.php';

                    $menu = $header_menu_2;
                    include 'snippets/mobile_menu_list.php';

                    $menu = $header_menu;
                    include 'snippets/mobile_menu_list.php';

                    $menu = $header_nav;
                    include 'snippets/mobile_menu_list.php';
                }
                ?>

                <?php if (!empty($has_view_website_button)): ?>
                    <li class="level_1 hidden--desktop hidden--tablet">
                        <a href="/"><?= __('View Website') ?></a>
                    </li>
                <?php endif; ?>

                <?php if (Settings::instance()->get('site_searchbar')): ?>
                    <li class="level_1 hidden--tablet hidden--desktop menu-search-wrapper">
                        <form class="site-search" action="/search_results" method="get">
                            <?php
                            $term = isset($_GET['term']) ? htmlspecialchars($_GET['term']) : '';
                            $attributes = ['placeholder' => __('Search'), 'class' => 'site-search-input'];
                            $search_button = '<button type="submit" class="btn-link button--plain" style="color: inherit;"><span class="flip-horizontally"><span class="icon_search"></span></span></button>';
                            $args = ['right_icon' => $search_button, 'invert_right_icon' => true, 'group_class' => 'mx-auto'];
                            echo Form::ib_input(null, 'term', $term, $attributes, $args);
                            ?>
                        </form>
                    </li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </div>

    <?php /* Plugin submenus and menu-plugin level-3 menus. These are hidden by default and revealed when their parent item is clicked. */ ?>
    <div class="mobile-menu-level3-section" id="mobile-menu-level3-section">
        <?php $current_url = trim($_SERVER['REQUEST_URI'], '/') ?>

        <?php foreach ($plugin_submenu_parents as $plugin_submenu_parent): ?>
            <div class="mobile-menu-list<?= $plugin_submenu_parent['active'] ? '' : ' hidden' ?>" data-parent_id="plugin-<?= $plugin_submenu_parent['name'] ?>">
                <?php
                // If the link already begins with "/admin/", prevent double up.
                $parent_link = str_replace('/admin//admin/', '/admin/', '/admin/'.$plugin_submenu_parent['url']);
                ?>
                <div class="mobile-menu-top">
                    <div class="mobile-menu-top-heading">
                        <button type="button" class="mobile-menu-back button--plain" id="mobile-menu-back" title="<?= __('Back') ?>">
                            <strong>
                                <span class="icon-angle-left fa fa-angle-left"></span>
                                <span class="sr-only"><?= __('Back') ?></span>
                            </strong>
                        </button>

                        <a href="<?= $parent_link ?>">
                            <strong class="mobile-menu-top-title"><?= $plugin_submenu_parent['name'] ?></strong>
                        </a>
                    </div>
                </div>

                <ul class="level3">
                    <?php foreach ($plugin_submenu_parent['submenu']['items'] as $submenu_item): ?>
                        <?php
                        $active = ($current_url == trim($submenu_item['link'], '/'));
                        $icon = !empty($submenu_item['icon_svg']) ? $submenu_item['icon_svg'] : '';
                        ?>

                        <li class="level_3<?= $icon ? ' has_icon' : '' ?><?= $active ? ' active' : '' ?>">
                            <a href="<?= $submenu_item['link'] ?>">
                                <?php if ($icon): ?>
                                    <span class="plugin-menu-icon"><?= Ibhelpers::svg_sprite($icon) ?></span>
                                <?php endif; ?>

                                <?= $submenu_item['title'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <?php foreach ($lv3_menu_parents as $lv3_menu_parent): ?>
            <div class="hidden mobile-menu-list" data-parent_id="<?= $lv3_menu_parent['id'] ?>">
                <div class="mobile-menu-top">
                    <div class="mobile-menu-top-heading">
                        <button type="button" class="mobile-menu-back button--plain" id="mobile-menu-back" title="<?= __('Back') ?>">
                            <strong>
                                <span class="arrow_caret-left"></span>
                                <span class="sr-only"><?= __('Back') ?></span>
                            </strong>
                        </button>

                        <a <?= menuhelper::attributes($lv3_menu_parent); ?>>
                            <strong class="mobile-menu-top-title"><?= $lv3_menu_parent['title'] ?></strong>
                        </a>
                    </div>
                </div>

                <ul class="level3">
                    <?php
                    $lv = 3;
                    $menu = $lv3_menu_parent['submenu'];
                    include 'snippets/mobile_menu_list.php';
                    ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>
