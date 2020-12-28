<?php $global_search_enabled = Settings::instance()->get('global_search') == 1 && Auth::instance()->has_access('global_search', false); ?>

<?php if(Settings::instance()->get('use_header') == 1 AND Settings::instance()->get('engine_header') != ''): ?>
    <div class="row logo">
        <img src="<?= URL::overload_asset(Settings::instance()->get('engine_header')) ?>" alt="CMS Header"/>
    </div>
<?php endif; ?>

<?php
$cms_logo = Settings::instance()->get('cms_logo');
if ($cms_logo) {
    $logo = Model_Media::get_image_path($cms_logo, 'logos');
}
else {
    $modern_theme_logo_exists = ($project_assets_folder_path AND file_exists($project_assets_folder_code_path.'/img/modern-logo.png'));
    $modern_site_logo_exists  = file_exists(DOCROOT.'assets/img/modern-logo.png');
    $logo = ($modern_theme_logo_exists OR $modern_site_logo_exists) ? URL::overload_asset('img/modern-logo.png') : URL::overload_asset('img/logo.png');
}

$mobile_logo = trim(Settings::instance()->get('site_mobile_logo'));

if ($mobile_logo) {
    $mobile_logo = Model_Media::get_image_path($mobile_logo, 'logos');
}

$has_cart = Model_Plugin::is_loaded('bookings') && Settings::instance()->get('show_cart_in_mobile_header');
$visibility_classes = 'hidden--tablet hidden--desktop hidden-sm hidden-md hidden-lg header-mobile';
?>

<div class="row gutters <?= $visibility_classes ?><?= $has_cart ? ' has_cart' : '' ?><?= $global_search_enabled ? ' has_search' : '' ?>" id="header-mobile">
    <div class="col-xs-3">
        <button type="button" class="mobile-menu-toggle button--plain" id="mobile-menu-toggle">
            <span class="fa fa-bars icon-bars"></span>
        </button>
    </div>

    <div class="col-xs-6 text-center">
        <a href="/" class="header-logo">
            <img src="<?= $mobile_logo ? $mobile_logo : $logo ?>" alt="<?= __('Home') ?>"/>
        </a>
    </div>

    <div class="col-xs-3">
        <?php if ($global_search_enabled): ?>
            <button type="button" class="header-icon button--plain header-search-toggle" data-hide_toggle="#header-search">
                <span class="icon_search"></span>
            </button>
        <?php endif; ?>

        <?php include Kohana::find_file('views', 'frontend/template_views/header_cart'); ?>
    </div>
</div>

<div class="row usermenu-wrapper hidden--mobile hidden-xs">
    <input type="hidden" id="logged_in_user_id" value="<?= $logged_in_user['id'] ?>" />

    <div class="sidebar-header">
        <div class="sidebar-logo logo-dropdown left">
            <div class="left usermenu-item">
                <button class="sidebar-toggle mobile-menu-toggle" id="sidebar-toggle">
                    <span class="flaticon-bars"></span>
                </button>
            </div>

            <div class="left usermenu-item">
                <a href="/admin" class="sidebar-logo-link">
                    <img src="<?= $logo ?>" alt="Dashboard" />
                </a>

                <ul class="dropdown-menu"><?= implode(' ', $header->menu) ?></ul>
            </div>
        </div>
    </div>

    <?php if ($user_details->role->access_type == 'Front end'): ?>
        <?php $header_menu_1 = Menuhelper::get_all_published_menus('header 1'); ?>

        <?php foreach ($header_menu_1 as $level1_item): ?>
            <?php if ($level1_item['parent_id'] == 0): ?>
                <div class="usermenu-item header-item header-menu-section left">
                    <a href="<?= menuhelper::get_link($level1_item) ?>" class="header-menu-expand"><?= $level1_item['title'] ?></a>

                    <div class="header-menu header-menu--<?= preg_replace('/\W+/','',strtolower(strip_tags($level1_item['category']))); ?>">
                        <div class="row header-menu-row">
                            <?php
                            $list = menuhelper::submenu($level1_item);
                            echo View::factory('front_end/snippets/menu_list')->set('list', $list)->set('level', 1);
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>

        <?php
        $dashboard_user = Auth::instance()->get_user();
        if (isset($header->available_dashboards) AND count($header->available_dashboards) > 0 && Auth::instance()->has_access('dashboards')):
        ?>
            <div id="dashboards-usermenu-btn" class="usermenu-item left">
                <div class="dropdown">
                    <button class="dropdown-toggle dashboards-dropdown-btn" type="button" data-toggle="dropdown"><?= __('Dashboards') ?>
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <?php foreach ($header->available_dashboards as $available_dashboard): ?>
                            <li><a href="/admin/dashboards/view_dashboard/<?= $available_dashboard['id'] ?>"><?= $available_dashboard['title'] ?></a></li>
                        <?php endforeach; ?>
                        <?php if (Model_Plugin::get_isplugin_enabled_foruser($dashboard_user['role_id'], 'dashboards')): ?>
                            <li role="presentation" class="divider"></li>
                            <li><a href="/admin/dashboards"><?= __('Manage Dashboards') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        <?php endif; ?>
    <?php endif; ?>


    <?php
    if (Model_Plugin::is_enabled_for_role('Administrator', 'Insurance'))
    {
        include Kohana::find_file('views', 'snippet_add_new_policy_with_customer_select');
    }
    ?>


	<?php
    $settings_instance = Settings::instance();
    $auth_instance = Auth::instance();
    ?>
	<?php if ($auth_instance->has_access('cms_action_button_1') && trim($settings_instance->get('cms_heading_button_text')) AND trim($settings_instance->get('cms_heading_button_link'))): ?>
		<div class="usermenu-item left btn-header-action-wrapper">
			<a href="<?= $settings_instance->get('cms_heading_button_link') ?>" class="btn btn-header-action btn-header-action-1">
				<span><?= $settings_instance->get('cms_heading_button_text') ?></span>
			</a>
		</div>
	<?php endif; ?>

    <?php if ($auth_instance->has_access('cms_action_button_2') && trim($settings_instance->get('cms_heading_button_text_2')) AND trim($settings_instance->get('cms_heading_button_link_2'))): ?>
        <div class="usermenu-item left btn-header-action-wrapper">
            <a href="<?= $settings_instance->get('cms_heading_button_link_2') ?>" class="btn btn-header-action btn-header-action-1">
                <span><?= $settings_instance->get('cms_heading_button_text_2') ?></span>
            </a>
        </div>
    <?php endif; ?>

	<?php if (($settings_instance->get('view_website') == 1) &&  (Auth::instance()->has_access('view_website_frontend'))): ?>
        <div class="usermenu-item left">
            <a href="<?= URL::Site(); ?>" class="btn view-website-btn"><?= __('View Website') ?></a>
        </div>
    <?php endif; ?>

    <?php //Global Search option ?>
    <?php if ($global_search_enabled): ?>
        <div class="usermenu-item searchbar-wrapper left">
            <div class="searchbar-inner">
                <input id="search2" placeholder="Search" />
                <label for="search2">
                    <span class="searchbar-icon" id="searchbar-icon">
                        <span class="flaticon-search"></span>
                    </span>
                    <span class="sr-only">Search</span>
                </label>
            </div>
        </div>
    <?php endif; ?>

    <div class="user-tools-wrapper right">
        <?php if ($user_details->role->access_type == 'Front end'): ?>
            <?php $header_menu_2 = Menuhelper::get_all_published_menus('header 2'); ?>

            <?php foreach ($header_menu_2 as $key => $level1_item): ?>
                <?php if ($level1_item['parent_id'] == 0): ?>
                    <div class="usermenu-item header-item header-menu-section left">
                        <a href="<?= menuhelper::get_link($level1_item) ?>" class="header-menu-expand"><?= $level1_item['title'] ?></a>

                        <div class="header-menu header-menu--<?= preg_replace('/\W+/','',strtolower(strip_tags($level1_item['category']))); ?>">
                            <div class="row header-menu-row">
                                <?php
                                $list = menuhelper::submenu($level1_item);
                                echo View::factory('front_end/snippets/menu_list')->set('list', $list)->set('level', 1);
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <ul class="user-tools">
            <?php if (Auth::instance()->has_access('user_tools_messages') AND Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')): ?>
                <li class="usermenu-item user-tools-messages">
                    <div class="dropdown">
                        <a href="#" class="user-tools-link" id="user-tools-messaging-expand" data-toggle="" aria-haspopup="true" aria-expanded="false">
                            <span class="user-tools-icon messages_icon flaticon-bell" title="<?= __('Notifications') ?>"></span>
                            <span class="user-tool-text"><?= __('Notifications') ?></span>
                            <span class="user_tools_notification_amount" id="message-notifications-amount"></span>
                        </a>

                        <div class="dropdown-menu pull-right user-notifications-dropout" id="user-notifications-dropout" aria-labelledby="user-tools-messaging-expand">
                            <aside class="bulletin-menu-wrapper pt-3">
                                <h3 class="px-3">Notifications</h3>

                                <div>
                                    <div id="bulletin-tab-pane-messaging">
                                        <ul class="user-notifications-dropout" id="user-notifications-dropout">
                                            <li id="message-notification-list"></li>
                                            <li id="message-notification-view_message"></li>
                                        </ul>
                                    </div>

                                    <?php /*
                                    <?php if (Model_Plugin::get_isplugin_enabled_foruser($dashboard_user['role_id'], 'chat')): ?>
                                        <div id="bulletin-tab-pane-chat">
                                            <div class="bulletin-chat-wrapper">
                                                <ul>
                                                    <?php foreach($recent_conversations as $conversation): ?>
                                                        <li class="bulletin-list-item">
                                                            <button
                                                                class="bulletin-chat-open"
                                                                data-room_id="<?= $conversation['id'] ?>"
                                                                data-room_name="<?= $conversation['name'] ?>"
                                                                data-read="<?= $conversation['last_message_read'] ?>"
                                                                >
                                                                <span class="bulletin-list-item-name"><?= $conversation['last_message'] ?></span>
                                                            <span class="bulletin-list-item-time">
                                                                <span><?= $conversation['last_message_sender'] ?></span>,
                                                                <time datetime="<?= $conversation['last_message_date'] ?>">
                                                                    <?= IbHelpers::relative_time_with_tooltip($conversation['last_message_date']) ?>
                                                                </time>
                                                            </span>
                                                            </button>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    */ ?>
                                </div>
                            </aside>
                        </div>
                    </div>
                </li>

                <?php if (Settings::instance()->get('messaging_popout_menu') == '1' && (Auth::instance()->has_access('messaging_access_own_mail') || Auth::instance()->has_access('messaging_view'))): ?>
                    <li class="usermenu-item">
                        <a href="#" type="button" class="btn-link user-tools-link" id="user-tools-messaging-sidebar-expand">
                            <span class="user-tools-icon icon-inbox"></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (Auth::instance()->has_access('user_tools_help')): ?>
                <?php $help_links = explode("\n", trim(Settings::instance()->get('help_links'))); ?>
                <?php if (sizeof($help_links) > 0 AND $help_links[0] != ''): ?>
                    <li class="usermenu-item user-tools-help">
                        <a href="#" class="dropdown-toggle user-tools-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="user-tools-icon icon-question" title="<?= __('Help') ?>"></span>
                            <span class="user-tool-text"><?= __('Help') ?></span>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <?php foreach($help_links as $link): ?>
                                <?php
                                $url = substr($link, strpos($link,'{')+1);
                                $url = substr($url,0,strpos($url,'}'));
                                $href = (strpos($url, "http://") === 0 OR strpos($url, "https://") === 0 )? $url : URL::site($url);
                                ?>
                                <li><a href="<?=$href;?>"
                                       target="<?= strpos($link,'[')!=-1 ? substr($link,strpos($link,'['),strpos($link,']')):'_self'; ?>">
                                        <?= __(substr($link,0,strpos($link,'{')));?>
                                    </a>
                                </li>
                            <?php endforeach; // strpos($link,'}')?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

                <li class="usermenu-item user-tools-avatar">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle user-tools-link" id="user-tools-profile-expand" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="profile-section-avatar" src="<?= URL::get_avatar($logged_in_user['id']); ?>" alt="profile" width="22" height="22" title="<?= __('Profile: ').$logged_in_user['name'] ?>" />
                            <span class="user-tool-text profile-section-name"><?= ($logged_in_user['name'] != '') ? $logged_in_user['name'] : $logged_in_user['email'] ?></span>
                        </a>
                        <ul class="dropdown-menu pull-right user-tools-messaging-dropdown-menu" aria-labelledby="user-tools-profile-expand">
                            <?php $divider = FALSE;?>
                            <?php if(Auth::instance()->has_access('user_profile')): $divider = TRUE; ?>
                                <li<?= (isset($current_controller) AND $current_controller == 'profile') ? ' class="active"' : '' ?>><a href="/admin/profile/edit?section=contact">Profile</a></li>
                            <?php endif; ?>
                            <?php if (Auth::instance()->has_access('settings')) { ?>
                                <li data-controller="settings" class="sidebar-menu-li<?= (isset($current_controller) AND $current_controller == 'settings' AND $current_action == 'activities') ? ' sidebar-menu-li--current active' : '' ?>">
                                    <a href="/admin/settings/activities" title="<?= __('System') ?>">
                                        <?= __('System') ?>
                                    </a>
                                </li>
                                <li data-controller="settings" class="sidebar-menu-li<?= (isset($current_controller) AND $current_controller == 'settings' AND $current_action != 'activities') ? ' sidebar-menu-li--current active' : '' ?>">
                                    <a href="/admin/settings" title="<?= __('Settings') ?>">
                                        <?= __('Settings') ?>
                                    </a>
                                </li>
                                <li data-controller="usermanagement" class="sidebar-menu-li<?= (isset($current_controller) AND $current_controller == 'usermanagement') ? ' sidebar-menu-li--current active' : '' ?>">
                                    <a href="/admin/usermanagement/users" title="<?= __('User management') ?>">
                                        <?= __('User Management') ?>
                                    </a>
                                </li>
                            <?php } ?>
                            <?= implode(' ', $header->notification_links); ?>

                            <?php if (Auth::instance()->has_access('courses_view_mycourses')): ?>
                                <li><a href="/admin/courses/my_courses">My Courses</a></li>
                            <?php endif; ?>

                            <?php if($divider): ?>
                                <li role="presentation" class="divider"></li>
                            <?php endif; ?>

                            <li><a href="/admin/login/logout"><?= __('Log out') ?></a></li>
                        </ul>
                    </div>
                </li>
        </ul>

        <?php if (Settings::instance()->get('cms_skin') == 'kes'): ?>
            <?php if (isset($GLOBALS['ibcms_right_panels']) && count($GLOBALS['ibcms_right_panels']) > 0) { ?>
            <div class="usermenu-item user-msg-wrapper">
                <span class="user-chat-dropdown-toggle">
                    <span class="icon icon-ellipsis-h" aria-hidden="true"></span>
                </span>

                <div class="user-chat-dropdown">
                    <?php
                    function array_swap(&$array, $swap_a, $swap_b)
                    {
                        list($array[$swap_a], $array[$swap_b]) = array($array[$swap_b], $array[$swap_a]);
                    }

                    $array_files = array();

                    foreach ($GLOBALS['ibcms_right_panels'] as $rwidget) {
                        if (isset($rwidget['view'])) {
                            $array_files[] = $rwidget['view'][0];
                        }
                    }

                    if (!empty($array_files)) {
                        if (count($array_files) >= 3) {
                            array_swap($array_files, 1, 2);
                        }

                        foreach ($array_files as $rwidget_view) {
                            include $rwidget_view;
                        }
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
        <?php endif; ?>
    </div>
</div>
