<?php require_once('header_top.php');?>
<?php
$user = Auth::instance()->get_user();
$logo = trim(Settings::instance()->get('site_logo'));

if ($logo) {
    // Get image from the settings
    $logo = Model_Media::get_image_path($logo, 'logos');
} else {
    // Get image from the assets folder
    $logo = '/assets/'.$assets_folder_path.'/images/logo.png';
}

$mobile_logo = trim(Settings::instance()->get('site_mobile_logo'));

if ($mobile_logo) {
    $mobile_logo = Model_Media::get_image_path($mobile_logo, 'logos');
}
$hide_menus = ($page_data['layout'] == 'landing_page' && Settings::instance()->get('show_menu_on_landing_page'));
?>
        <header class="header">
            <?php // Mobile header ?>
            <div class="row gutters hidden--tablet hidden--desktop" id="header-mobile">
                <div class="col-xs-3">
                    <button type="button" class="mobile-menu-toggle button--plain" id="mobile-menu-toggle">
                        <span class="fa fa-bars"></span>
                    </button>
                </div>

                <div class="col-xs-6">
                    <a href="/" class="header-logo">
                        <img src="<?= $mobile_logo ? $mobile_logo : $logo ?>" alt="<?= __('Home') ?>"/>
                    </a>
                </div>

                <div class="col-xs-3">
                    <?php
                    $course_bookings_enabled = Model_Plugin::is_enabled_for_role('Administrator', 'Bookings') && Model_Plugin::is_enabled_for_role('Administrator', 'Courses');
                    $is_checkout = (Request::current()->action() == 'checkout');
                    ?>

                    <?php include Kohana::find_file('views', 'frontend/template_views/header_cart'); ?>
                </div>
            </div>

            <?php // Desktop and tablet header ?>
            <?php $top_nav = $hide_menus ? [] : Menuhelper::get_all_published_menus('top nav'); ?>

            <?php if (!empty($top_nav)): ?>
                <div class="row hidden--mobile header-top-nav" id="header-top-nav">
                    <ul>
                        <?php foreach ($top_nav as $top_nav_item): ?>
                            <li class="header-top-nav-li">
                                <a href="<?= menuhelper::get_link($top_nav_item) ?>"
                                    <?= Menuhelper::is_active($top_nav_item) ? ' class="active"' : '' ?>
                                >
                                    <?= htmlspecialchars(__($top_nav_item['title'])) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <?php if (Settings::instance()->get('site_searchbar')): ?>
                            <?php $has_top_nav_search = true; ?>
                            <li class="header-top-nav-li-search">
                                <button type="button" class="button--plain top-nav-searchbar-button" id="top-nav-searchbar-button">
                                    <span class="sr-only"><?= htmlentities(__('Show searchbar')) ?></span>
                                    <img src="<?= URL::overload_asset('img/search.svg') ?>" />
                                </button>

                                <form action="/search_results" class="top-nav-searchbar-wrapper" id="top-nav-searchbar-wrapper">
                                    <label class="sr-only" for="top-nav-searchbar"><?= __('Search') ?></label>
                                    <input type="text" name="term" class="form-input top-nav-searchbar" placeholder="<?= __('Search') ?>" id="top-nav-searchbar" />
                                </form>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row hidden--mobile" id="header-desktop">
                <div class="header-left">
                    <a href="/" class="header-item header-logo">
                        <img src="<?= $logo ?>" alt="<?= __('Home') ?>" />
                    </a>

                    <div class="header-item">
                        <?php $header_menu_1 = $hide_menus ? [] : Menuhelper::get_all_published_menus('header 1'); ?>

                        <?php foreach ($header_menu_1 as $level1_item): ?>
                            <?php if ($level1_item['parent_id'] == 0): ?>
                                <div class="header-item header-menu-section">
                                    <?php $list = menuhelper::submenu($level1_item); ?>

                                    <?php if (!empty($list)): ?>
                                        <a href="<?= menuhelper::get_link($level1_item) ?>" class="header-menu-expand"><?= htmlentities(__($level1_item['title'])) ?></a>

                                        <?php
                                        // Should really be handled outside of the view
                                        $has_sublists = false;
                                        foreach ($list as $item) { if (menuhelper::submenu($item)) $has_sublists = true; }
                                        ?>

                                        <div class="header-menu header-menu--<?= preg_replace('/\W+/','',strtolower(strip_tags($level1_item['category']))); ?><?= $has_sublists ? ' has_submenus' : ''?>">
                                            <div class="row header-menu-row">
                                                <?= View::factory('front_end/snippets/menu_list')->set('list', $list)->set('level', 1); ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?= menuhelper::get_link($level1_item) ?>"<?= Menuhelper::is_active($level1_item) ? ' class="active"' : '' ?>><?= htmlentities(__($level1_item['title'])) ?></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php $header_menu = Menuhelper::get_all_published_menus('header'); ?>

                <?php if ($header_menu): ?>
                    <div class="header-actions">
                        <?php foreach ($header_menu as $header_menu_item): ?>
                            <div class="header-item header-action">
                                <a href="<?= menuhelper::get_link($header_menu_item) ?>"
                                   class="button button--continue<?= Menuhelper::is_active($header_menu_item) ? ' active' : '' ?>"
                                    >
                                    <?= htmlentities(__($header_menu_item['title'])) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="header-right">
                    <?php if (empty($has_top_nav_search) && Settings::instance()->get('site_searchbar')): ?>
                        <div class="header-item header-action">
                            <form class="site-search" action="/search_results" method="get">
                                <?php
                                $term = isset($_GET['term']) ? htmlspecialchars($_GET['term']) : '';
                                $attributes = ['placeholder' => __('Search'), 'class' => 'site-search-input'];
                                $search_button = '<button type="submit" class="btn-link button--plain" style="color: inherit;"><span class="flip-horizontally"><span class="icon_search"></span></span></button>';
                                $args = ['right_icon' => $search_button, 'invert_right_icon' => true, 'group_class' => 'mx-auto'];
                                echo Form::ib_input(null, 'term', $term, $attributes, $args);
                                ?>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if (!$hide_menus && !$user && Settings::instance()->get('frontend_login_link')): ?>
                        <?php
                        $login_text = __(trim(Settings::instance()->get('frontend_login_link_text')));
                        $login_text = $login_text ? $login_text : __('Log in');
                        ?>

                        <div class="header-item header-action header-action--login">
                            <a href="/admin/login" class="button button--continue"><?= $login_text ?></a>
                        </div>
                    <?php endif; ?>

                    <?php $header_menu_2 = $hide_menus ? [] : Menuhelper::get_all_published_menus('header 2'); ?>

                    <?php foreach ($header_menu_2 as $key => $level1_item): ?>
                        <?php if ($level1_item['parent_id'] == 0): ?>
                            <div class="header-item header-menu-section">
                                <a href="<?= menuhelper::get_link($level1_item) ?>" class="header-menu-expand"><?= htmlentities(__($level1_item['title'])) ?></a>

                                <?php $list = menuhelper::submenu($level1_item); ?>

                                <?php
                                // Should really be handled outside of the view
                                $has_sublists = false;
                                foreach ($list as $item) { if (menuhelper::submenu($item)) $has_sublists = true; }
                                ?>

                                <?php if (!empty($list)): ?>
                                    <div class="header-menu header-menu--<?= preg_replace('/\W+/','',strtolower(strip_tags($level1_item['category']))); ?><?= $has_sublists ? ' has_submenus' : ''?>">
                                        <div class="row header-menu-row">
                                            <?= View::factory('front_end/snippets/menu_list')->set('list', $list)->set('level', 1); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php
                    $userlinks = array(
                        'accounts'   => ['url' => '/accounts.html',    'icon' => 'flaticon-settings',       'text' => 'Accounts',   'permission' => 'contacts3_frontend_accounts'],
                        'attendance' => ['url' => '/admin/contacts3/attendance', 'icon' => 'flaticon-pencil-square', 'text' => 'Attendance',   'permission' => 'contacts3_frontend_attendance'],
                        'bookings'   => ['url' => '/bookings.html',    'icon' => 'flaticon-invoice',        'text' => 'Bookings',   'permission' => 'contacts3_frontend_bookings'],
                        'my_courses' => ['url' => '/admin/courses/my_courses',                              'text' => 'My Courses', 'permission' => 'courses_view_mycourses'],
                        'timesheets' => ['url' => '/admin/timesheets', 'icon' => 'flaticon-time',           'text' => 'Timesheets', 'permission' => 'contacts3_frontend_timesheets'],
                        'timetables' => ['url' => '/timetables.html',  'icon' => 'flaticon-calendar-dates', 'text' => 'Timetables', 'permission' => 'contacts3_frontend_timetables'],
                        'wishlist'   => ['url' => '/wishlist.html',    'icon' => 'flaticon-heart',          'text' => 'Wishlist',   'permission' => 'contacts3_frontend_wishlist'],
                    );
                    ?>

                    <?php if (!$hide_menus && $user): ?>
                        <div class="header-item header-menu-section header-menu-section--account">
                            <a href="#" class="header-menu-expand">
                                <img src="<?= URL::get_avatar($user['id']); ?>" alt="<?= __('Account') ?>" width="35" height="35" title="<?= __('Account: ').$user['name'] ?>" />
                            </a>

                            <div class="header-menu header-menu--account">
                                <div class="row header-menu-row">
                                    <ul class="level1">
                                        <li class="level_1"><a href="/admin"><?= __('My account') ?></a></li>
                                        <li class="level_1"><a href="/admin/profile/edit?section=contact"><?= __('Profile') ?></a></li>

                                        <?php foreach ($userlinks as $userlink): ?>
                                            <?php if (empty($userlink['permission']) || Auth::instance()->has_access($userlink['permission'])): ?>
                                                <li class="level_1<?= (trim($userlink['url'], '/') == trim($_SERVER['REQUEST_URI'], '/')) ? ' active' : '' ?>">
                                                    <a href="<?= $userlink['url'] ?>"><?= $userlink['text'] ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <li class="level_1"><a href="/admin/login/logout?redirect=<?= urlencode(Request::detect_uri()) ?>"><?= __('Log out') ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </header>

        <?php include Kohana::find_file('views', 'mobile_menu'); ?>

        <?php
        if (strtolower($page_data['layout']) != 'home') {
            $page_title =  !empty($page_data['page_title']) ? $page_data['page_title'] : (isset($page_data['name_tag']) ? str_replace('.html', '', $page_data['name_tag']) : '');
            $mobile_breadcrumbs = MenuArea::factory()->generate_mobile_breadcrumb_links(array(
                array(
                    array('link' => !empty($breadcrumb_prev_url) ? $breadcrumb_prev_url : '/', 'name' => '')
                ),
                array('link' => !empty($breadcrumb_next_url) ? $breadcrumb_next_url : '/', 'name' => ''),
                array('link' => '#', 'name' => !empty($breadcrumb_title) ? $breadcrumb_title : $page_title)
            ));
            echo View::factory('mobile_breadcrumb')
                ->set('mobile_breadcrumbs',$mobile_breadcrumbs);
        }
        ?>

        <?php if ($mobile_footer_menu): ?>
            <div class="quick_contact hidden--tablet hidden--desktop">
                <ul class="list-unstyled">
                    <?php if ($user && !empty($userlinks)): ?>
                        <?php foreach ($userlinks as $userlink): ?>
                            <?php if (Auth::instance()->has_access($userlink['permission'])): ?>
                                <li class="quick_contact-item has_text">
                                    <a href="<?= $userlink['url'] ?>">
                                        <span class="quick_contact-item-icon <?= $userlink['icon'] ?>"></span>
                                        <span class="quick_contact-item-text"><?= $userlink['text'] ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <li class="quick_contact-item has_text">
                            <a href="/admin/profile/edit?section=contact">
                                <span class="quick_contact-item-icon flaticon-cog"></span>
                                <span class="quick_contact-item-text"><?= __('Settings') ?></span>
                            </a>
                        </li>
                    <?php else: ?>
                        <?php if ( ! empty($settings['telephone']) && trim($settings['telephone'])): ?>
                            <li class="quick_contact-item has_text">
                                <a href="tel:<?= preg_replace('/[^0-9]/', '', $settings['telephone']) ?>">
                                    <span class="quick_contact-item-icon icon_phone"></span>
                                    <span class="quick_contact-item-text"><?= __('Call us') ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ( ! empty($settings['email']) && trim($settings['email'])): ?>
                            <li class="quick_contact-item has_text">
                                <a href="mailto:<?= str_replace(' ', '', $settings['email']) ?>">
                                    <span class="quick_contact-item-icon icon_mail"></span>
                                    <span class="quick_contact-item-text"><?= __('Email us') ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="quick_contact-item has_text">
                            <a href="/contact-us.html#content_start">
                                <span class="quick_contact-item-icon icon_pin"></span>
                                <span class="quick_contact-item-text"><?= __('Find us') ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php include 'banner.php'; ?>

        <?php
        ob_start();
        include 'success.php';
        $success_page = trim(ob_get_clean());
        $page_data['content'] = trim($page_data['content'] . $success_page);
        ?>

		<div class="content<?= $success_page ? ' is_success_page' : '' ?>">
