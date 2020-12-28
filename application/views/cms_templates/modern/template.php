<?php
$logged_in_user       = Auth::instance()->get_user();
$breadcrumbs          = MenuArea::factory()->generate_breadcrumb_links();
$mobile_breadcrumbs   = MenuArea::factory()->generate_mobile_breadcrumb_links();
$template_settings    = Settings::instance()->get();

$config = Kohana::$config->load('config');
$project_assets_folder_path = isset($config->assets_folder_path) ? $config->assets_folder_path.'/' : '';
$project_assets_folder_code_path = PROJECTPATH.'www/assets/'.$project_assets_folder_path;
$request_uri          = trim($_SERVER['REQUEST_URI'], '/');

?>
<!DOCTYPE html><!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]--><!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]--><!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
	<head>
        <script>
            if (window.top.location.host != window.location.host) {
                window.top.location.href = window.location.href;
            }
        </script>
        <?php if (Settings::instance()->get('site_favicon')): ?>
            <link rel="shortcut icon" href="<?= Model_Media::get_image_path(Settings::instance()->get('site_favicon'), 'favicons', array('cachebust' => true)); ?>" type="image/ico" />
        <?php else: ?>
            <link rel="shortcut icon" href="/assets/<?= $project_assets_folder_path ?>images/favicon.ico" type="image/ico" />
        <?php endif; ?>

		<script>
			window.ibcms = {};
            <?php
            $loggedUser = Auth::instance()->get_user();
            $userModel = new Model_Users();
            $loggedUserData = $userModel->get_user($loggedUser['id']);
            ?>
            window.ibcms.settings = {}
            window.ibcms.settings.contacts_create_family = <?=Settings::instance()->get('contacts_create_family')?>;
            window.ibcms.user = <?=json_encode($loggedUserData);?>;
            window.ibcms.date_format = '<?php
                    switch(\Settings::instance()->get('date_format')){
                        case 'Y-m-d': echo 'yyyy-mm-dd';break;
                        case 'm/d/Y': echo 'mm/dd/yyyy';break;
                        case 'd/m/Y': echo 'dd/mm/yyyy';break;
                        case 'd-m-Y': echo 'dd-mm-yyyy';break;
                        default:      echo 'dd-mm-yyyy';break;
                    } ?>';
            window.ibcms.max_attachment_size_mb = <?=\Settings::instance()->get('messaging_max_attachment_size_mb')?>;
		</script>
		<?php
		$theme_css = URL::overload_asset('css/template3.css');
		include('html_head.php');
		?>
		<script>
        function slide_right_msg()
        {
            $('.alert[class*="alert-"]:not(.alert-stay)').addClass('fadeOutRight');
            setTimeout(function(){ $('.popup_box:not(.alert-stay) .close').trigger('click'); }, 1000);
        }

        function go_to_by_scroll12()
        {
            var $alerts = $('.alert[class*="alert-"]');
            if ($alerts.length) {
                $('html,body').animate({scrollTop: $alerts.offset().top}, 'slow');
                $alerts.addClass('popup_box').fadeIn("slow");
                setTimeout(function(){ slide_right_msg() }, 10000);
            }
        }

        function remove_popbox()
        {
            setTimeout(function(){ slide_right_msg() }, 12000);
        }
        </script>
	</head>
	<?php $column = Settings::get_column_toggle_setting() ?>
	<body<?= (isset($current_controller)) ? ' id="'.$current_controller.'"' : '' ?>
        class="<?= 'env-mode' . Kohana::$environment ?>
        layout-mode-<?= (isset($template_settings['fluid_layout_cms']) AND $template_settings['fluid_layout_cms'] == '1') ? 'fluid' : 'fixed' ?>
        theme-<?= $template_settings['cms_skin'] ?>
        sidebar-behavior--<?= Settings::instance()->get('page_change_on_sidebar_submenu_open') ? 'new_page' : 'current_page' ?>
        <?=
        ($column == 'none' ) ? ' sidebar-collapsed sidebar-preference-collapsed ' : '' ?><?=
        ($column == '2_col')? ' 2_col_setting' : ''?><?=
        (Settings::instance()->get('column_toggle')) == '2_col' ? ' 2_col_setting' : '' ?><?=
        ($column == '3_col') ? '' : ''?>">

        <div class="hidden" id="sprite-wrapper">
            <script>
                $.get(
                    "/api/theme/get_spritesheet",
                    function (response) {
                        $("#sprite-wrapper").append(response);
                    }
                )
            </script>
        </div>

		<div class="page-wrapper" id="page-wrapper">
			<?php
			$current_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
			$cms_skin = Settings::instance()->get('cms_skin');
			?>
			<div class="navigation-menu">
				<div class="page-container"><?php include 'usermenu.php' ?></div>
			</div>

            <?php
            if (isset($mobile_breadcrumbs)) {
                echo View::factory('mobile_breadcrumb')
                    ->set('mobile_breadcrumbs', $mobile_breadcrumbs);
            }
            ?>

            <?php if (!empty($global_search_enabled)): ?>
                <div class="header-search hidden hidden-sm hidden-md hidden-lg" id="header-search">
                    <?php
                    $attributes = array('placeholder' => __('Search'), 'id' => 'search2-mobile');
                    $args = array('icon' => '<span class="flaticon-search"></span>');
                    echo Form::ib_input(null, null, null, $attributes, $args);
                    ?>
                </div>
            <?php endif; ?>

			<div class="page-container" id="page-container">
				<?php if (isset($alert)): ?>
                    <?= $alert ?>
                    <script>remove_popbox();</script>
				<?php endif; ?>

				<div class="page-container-columns">
                    <div class="menus-column">
                        <?php
                        $sidebar_menu = empty($sidebar_menu) ? MenuArea::factory()->generate_icon_links() : $sidebar_menu;

                        $sidebar_items = array(
                            'bookings'   => array('url' => '/bookings.html',    'icon' => 'flaticon-invoice',        'text' => 'Bookings',   'permission' => 'contacts3_frontend_bookings'),
                            'accounts'   => array('url' => '/accounts.html',    'icon' => 'flaticon-settings',       'text' => 'Accounts',   'permission' => 'contacts3_frontend_accounts'),
                            'timesheets' => array('url' => '/admin/timesheets', 'icon' => 'flaticon-time',           'text' => 'Timesheets', 'permission' => 'contacts3_frontend_timesheets'),
                            'timetables' => array('url' => '/timetables.html',  'icon' => 'flaticon-calendar-dates', 'text' => 'Timetables', 'permission' => 'contacts3_frontend_timetables'),
                            'attendance' => array('url' => '/admin/contacts3/attendance', 'icon' => 'flaticon-pencil-square', 'text' => 'Attendance',   'permission' => 'contacts3_frontend_attendance'),
                            'wishlist'   => array('url' => '/wishlist.html',    'icon' => 'flaticon-heart',          'text' => 'Wishlist',   'permission' => 'contacts3_frontend_wishlist'),
                            'myschedules'   => array('url' => '/admin/courses/schedules', 'icon' => 'flaticon-heart','text' => 'My Schedules','permission' => 'courses_schedule_edit_limited'),
                        );

                        echo View::factory('mobile_menu')
                        ->set('class', 'sidebar-menu-wrapper')
                        ->set('id', 'sidebar-menu-wrapper')
                        ->set('is_backend', true)
                        ->set('sidebar_menu', $sidebar_menu)
                        ->set('user', Auth::instance()->get_user())
                        ->set('userlinks', $sidebar_items);
                        ?>

                        <?php if (!empty($sidebar->menus)): ?>
                            <div class="sidebar-menu-active-sublist">
                                <ul class="list-unstyled">

                                    <?php foreach ($sidebar->menus as $menu_items): ?>
                                        <?php foreach ($menu_items as $menu_item): ?>
                                            <?php $is_active = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/') == trim($menu_item['link'], '/') || trim($_SERVER['REQUEST_URI'], '/') == trim($menu_item['link'], '/'); ?>
                                            <li<?= $is_active ? ' class="active"' : '' ?>>
                                                <a href="<?= (strpos($menu_item['link'], 'admin/') == 0 ? '/' : '').$menu_item['link'] ?>" class="svg-color-hover" rel="tooltip" data-trigger="hover" data-toggle="tooltip" data-placement="right" title="<?= strip_tags($menu_item['name']) ?>">
                                                    <?php
                                                    $icon = empty($menu_item['icon']) ? 'settings' : $menu_item['icon'];
                                                    echo Ibhelpers::svg_sprite($icon, array('color' => $is_active));
                                                    ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
		
					<main class="main-content-wrapper">
                        <div class="container">
                            <div class="navbar-breadcrumbs-wrapper">
                                <?php if ( ! empty($breadcrumbs)): ?>
                                    <?php $current_crumb = end($sidebar->breadcrumbs); ?>
                                    <nav id="navbar-breadcrumbs"<?= (count($sidebar->breadcrumbs) > 1) ? ' class="hidden-xs"' : '' ?>>
                                        <ol>
                                            <?= implode($breadcrumbs); ?>
                                        </ol>

                                        <h1><?= $current_crumb['name'] ?></h1>
                                    </nav>
                                <?php endif; ?>

                                <?php if (isset($header->plugin_tools) AND $header->plugin_tools != ''): ?>
                                    <div id="plugin_tools">
                                        <?= $header->plugin_tools ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($header->header_actions)): ?>
                                    <div class="header-actions"><?= $header->header_actions ?></div>
                                <?php endif; ?>
                            </div>

                            <?php
                                $is_dashboard  = (str_replace('/', '', $_SERVER["REQUEST_URI"]) == 'admin');
                                $twitter_feed  = ($is_dashboard AND Settings::instance()->get('ib_twitter_feed'));
                                $feedback_form = ($is_dashboard AND Settings::instance()->get('display_feedback_form'));
                                $has_right_panels = !empty($GLOBALS['ibcms_right_panels']);
                            ?>

                            <div class="row gutters page-content">
                                <div class="<?php echo ($twitter_feed)? 'col-md-9' : 'col-md-12'; ?>">
                                    <?= View::factory('html_content_area')->set(array(
                                        'show_welcome_text' => isset($show_welcome_text) ? $show_welcome_text : TRUE,
                                        'body' => $body,
                                        'on' => isset($on) ? $on : '',
                                        'off' => isset($off) ? $off : '',
                                        'jira' => isset($jira) ? $jira : ''))
                                    ?>
                                </div>
                                <?php if ($twitter_feed): ?>
                                    <div class="col-md-3">
                                        <?php if ($twitter_feed): ?>
                                            <div id="ibTwitterFeed">
                                                <?php
												$apc_cache_key = 'cms_twitter_api_data:' . $_SERVER['HTTP_HOST'];
                                                /**
                                                 * Cache results from the Twitter API. Re-check every hour.
                                                 * We want to avoid calling the API on every page load, so we don't exceed the rate limit
                                                 **/

                                                // If alternative PHP caching has been set up and the cached data exists
                                                if (function_exists('apc_exists') AND apc_exists($apc_cache_key))
                                                {
                                                    // Get the cached data
                                                    $twitter_api_data = apc_fetch($apc_cache_key);
                                                }
                                                else
                                                {
                                                    // Get the data from the API
                                                    $twitter_api = new IbTwitterApi(
                                                        Settings::instance()->get('twitter_api_key_right'),
                                                        Settings::instance()->get('twitter_api_secret_key_right'),
                                                        Settings::instance()->get('twitter_api_access_token_right'),
                                                        Settings::instance()->get('twitter_api_secret_access_token_right')
                                                    );
                                                    $twitter_api_data = array(
                                                        'account' => $twitter_api->get('account/settings'),
                                                        'tweets'  => $twitter_api->get_tweets()
                                                    );

                                                    if (function_exists('apc_exists'))
                                                    {
                                                        // Cache the data for one hour
                                                        apc_store($apc_cache_key, $twitter_api_data, 60 * 60);
                                                    }
                                                }

                                                $tweets = $twitter_api_data['tweets'];
                                                $account = $twitter_api_data['account'];
                                                ?>

                                                <div class="panel twitter-panel">
                                                    <?php if (is_array($tweets) AND count($tweets) > 0): ?>
                                                        <?php $twitter_account = $tweets[0]->user; ?>

                                                        <div class="panel-heading">
                                                            <h2>
                                                                <span class="icon-twitter"></span>
                                                                <strong>Tweets</strong>
                                                                by <a href="http://twitter.com/<?= $twitter_account->screen_name ?>" rel="author">@<?= $twitter_account->screen_name ?></a>
                                                            </h2>
                                                        </div>

                                                        <div class="panel-body">
                                                            <?php foreach ($tweets as $tweet): ?>
                                                                <div class="twitter-panel-tweet">
                                                                    <div class="tweet-heading">
                                                                        <img src="<?= $twitter_account->profile_image_url ?>" alt="" width="30" />
                                                                        <span class="tweet-account_name"><?= $twitter_account->name ?></span>
                                                                        <a class="tweet-screen_name" href="http://twitter.com/<?= $twitter_account->screen_name ?>" rel="author">@<?= $twitter_account->screen_name ?></a>
                                                                        <span class="icon-twitter"></span>
                                                                    </div>
                                                                    <div class="tweet-message">
                                                                        <?= nl2br($tweet->text) ?>
                                                                    </div>
                                                                    <div class="tweet-footer">
                                                                        <a class="tweet-date" href="http://twitter.com/<?= $tweet->user->screen_name ?>/status/<?= $tweet->id ?>">
                                                                            <?= date('j F Y', strtotime($tweet->created_at)) ?>
                                                                        </a>

                                                                        <div class="tweet-actions">
                                                                            <a class="tweet-like" href="https://twitter.com/intent/like?tweet_id=<?= $tweet->id ?>" target="_blank" title="Like">
                                                                                <span class="sr-only">Like</span>
                                                                                <span class="icon-heart"></span>
                                                                            </a>

                                                                            <a class="tweet-retweet" href="https://twitter.com/intent/retweet?tweet_id=740935501599920128" title="Retweet">
                                                                                <span class="sr-only">Retweet</span>
                                                                                <span class="icon-arrow-right"></span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="panel-body">
                                                            <p><?= __('No Tweets to display') ?></p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($feedback_form): ?>
                                <div class="row gutters">
                                    <div class="col-lg-12" style="clear: both;">
                                        <div class="panel panel-default panel-feedback_form">

                                            <form action="/admin/dashboard/send_feedback" method="post">
                                                <div class="panel-heading">
                                                    <h2><?= __('Feedback Form') ?></h2>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="form-group">
                                                        <label class="sr-only" for="feedback-form-comment"><?= __('What do you like or dislike about our service?') ?></label>
                                                        <textarea class="form-control" id="feedback-form-comment" name="comment" rows="4" placeholder="<?= __('What do you like or dislike about our service?') ?>"></textarea>
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn btn-primary btn-lg"><?= __('Send') ?></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
					</main>
					<?php
					if ($has_right_panels) {
					?>
					<div class="tpl-right-panel" style="position: absolute; top:0px; right: 0px; overflow: visible;">
					<?php
						foreach ($GLOBALS['ibcms_right_panels'] as $rwidget) {
							if (isset($rwidget['css'])) {
								foreach ($rwidget['css'] as $rwidget_css) {
									echo '<link rel="stylesheet" type="text/css" href="' . $rwidget_css . '" />';
								}
							}
							if (isset($rwidget['js'])) {
								foreach ($rwidget['js'] as $rwidget_js) {
									echo '<script src="' . $rwidget_js . '"></script>';
								}
							}

							/*if (isset($rwidget['view'])) {
								foreach ($rwidget['view'] as $rwidget_view) {
									include $rwidget_view;
								}
							}*/
						}
					?>
					</div>
					<?php
					}
					?>
				</div>
			</div>

			<?= $footer; ?>

		</div>
		<input type="hidden" id="ib_project_name" value="<?= PROJECTNAME ?>" />
		<input type="hidden" id="custom_search" value="<?= class_exists('Controller_Admin_Search') ? 1 : 0 ?>" />
		<input type="hidden" id="datatable_default_length" value="<?= ( ! empty($user_details->datatable_length_preference)) ? $user_details->datatable_length_preference : 10 ?>" />

		<!-- Begin Footer JS section
			====================================================================== -->

		<!-- Bootstrap style JS script-->
		<script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-3.3.5.min.js"></script>

		<!-- Scripts concatenated and minified via ant build script-->
		<script src="<?= URL::get_engine_assets_base() ?>js/jquery.dataTables.min.js"></script>
		<script src="<?= URL::get_engine_assets_base() ?>js/ckeditor/ckeditor.js"></script>
		<script src="<?= URL::get_engine_assets_base() ?>js/plugins.js"></script>
		<script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-datepicker.js"></script>
        <script src="<?= URL::get_engine_assets_base() ?>js/combobox.js"></script>
        <script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-toggle/bootstrap-toggle.min.js"></script>
        <script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-multiselect.js"></script>
        <script src="<?= URL::overload_asset('js/forms.js', ['cachebust' => true]) ?>"></script>

		<!-- 3rd party plugins -->
		<?php
        // Add in any plugin specific scripts...
		if (isset($scripts) && is_array($scripts)) {
            $scripts_echoed = array();
            foreach ($scripts as $script) {
                if (array_search($script, $scripts_echoed) === false) {
                    echo $script . "\n";
                    $scripts_echoed[] = $script;
                }
            }
		}
		?>
		<script src="<?php echo URL::get_engine_assets_base(); ?>js/script.js?ts=<?= filemtime(ENGINEPATH.'application/assets/shared/js/script.js') ?>"></script>

		<script src="<?php echo URL::get_engine_assets_base(); ?>js/codes-import.js"></script>

		<!-- Our scripts don't put inline if possible -->
		<!-- end scripts-->

		<!-- End JS section -->

		<div id="profiler" style="display:none;">
			<?php echo View::factory('profiler/stats') ?>
		</div>

        <?php include Kohana::find_file('views/snippets', 'datatable_multiselect') ?>

		<?php echo IbHelpers::t_tag(Settings::instance()->get('cms_footer_html')); ?>

		<?php if (Settings::instance()->get('slaask_api_access_cms')): ?>
			<?php $slaask_api_key = trim(Settings::instance()->get('slaask_api_key')); ?>
			<?php if ($slaask_api_key): ?>
				<script src='https://cdn.slaask.com/chat.js'></script>
				<script>
					_slaask.identify('<?=str_replace("'", "\\'", $loggedUserData['name'] . ' ' . $loggedUserData['surname'])?>', {
						user_id: <?=$loggedUserData['id']?>,
						email: '<?=$loggedUserData['email']?>'
					});

					_slaask.init('<?= $slaask_api_key ?>');

					document.addEventListener('slaask.ready', function (e) {
						console.log(e.detail);
					}, false);

					document.addEventListener('slaask.open', function (e) {
						console.log(e.detail);
					}, false);

					document.addEventListener('slaask.close', function (e) {
						console.log(e.detail);
					}, false);

					document.addEventListener('slaask.sent_message', function (e) {
						console.log(e.detail);
					}, false);

					document.addEventListener('slaask.received_message', function (e) {
						console.log(e.detail);
					}, false);

				</script>
			<?php endif; ?>
		<?php endif; ?>

        <div class="modal fade" tabindex="-1" role="dialog" id="auto-logout-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?= __('Inactive for too long') ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?= __('For your protection, we have logged you out as you have been inactive for more than ' .
                                $loggedUserData['auto_logout_minutes'] .
                                ' minutes. You need to login again to continue') ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="/admin/login" class="btn"><?= __('Log in') ?></a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ( ! empty($template_settings['messaging_popout_menu'])) echo View::factory('messaging_popout'); ?>

        <?php
        if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging') AND Auth::instance()->has_access('chat'))
        {
            echo View::factory('admin/chat_join_modal');
        }
        ?>
	</body>
</html>
