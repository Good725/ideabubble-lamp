<?php $logged_in_user = Auth::instance()->get_user(); ?>
<div class="cms_header navbar navbar-static-top">
    <?php if(Settings::instance()->get('use_header') == 1 AND Settings::instance()->get('engine_header') != ''): ?>
        <div class="logo">
            <img src="<?= URL::overload_asset(Settings::instance()->get('engine_header')) ?>" alt="CMS Header"/>
        </div>
    <?php endif; ?>
	<div class="navbar-inner">
        <? /* Temporary, until this is database driven */ ?>
        <?php if (strpos($_SERVER['HTTP_HOST'], 'kilmartin') !== FALSE): ?>
            <?php $app_mode = Cookie::get($logged_in_user['id'].'_application'); ?>
            <div id="application_switcher" class="dropdown" title="Toggle application mode">
                <ul>
                    <li <?= ($app_mode == 'ibcms' OR $app_mode == '') ? ' class="current"' : '' ?>><a href="#" data-choice="ibcms">IB CMS</a></li>
                    <li <?= ($app_mode == 'ibeducate')                ? ' class="current"' : '' ?>><a href="#" data-choice="ibeducate">IB Educate</a></li>
                </ul>
            </div>
        <?php endif; ?>
		<div class="">
			<div class="nav-collapse clearfix">
                <div class="logo left dropdown">
                    <a href="/<?=$logged_in_user['default_home_page']?>"><img src="<?= URL::overload_asset('img/logo.png') ?>" alt="Website Platform"/></a>
                    <ul><?
                        /* Temporary, until this is database driven */ ?>
                        <?php if (Cookie::get($logged_in_user['id'].'_application') == 'ibeducate'): ?>
                            <li><a href="/admin/contacts3">Contacts</a></li>
                            <li><a href="/admin/bookings">Bookings</a></li>
                        <?php else: ?>
                            <?= implode(' ', $menu) ?>
                        <?php endif; ?>
                    </ul>
                </div>

				<?php if (isset($available_dashboards) AND count($available_dashboards) > 0): ?>
					<div class="left">
						<div class="dropdown">
							<button class="btn-link btn-plain dropdown-toggle" type="button" data-toggle="dropdown">Dashboards
								<span class="caret"></span></button>
							<ul class="dropdown-menu">
								<?php foreach ($available_dashboards as $available_dashboard): ?>
									<li><a href="/admin/dashboards/view_dashboard/<?= $available_dashboard['id'] ?>"><?= $available_dashboard['title'] ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				<?php endif; ?>

                <?php /*
				<a class="btn" data-popup-open="popup-email" href="#">Email</a>
				<a class="btn cl_space" data-popup-open="popup-sms" href="#">SMS</a>
                */ ?>

                <ul id="user_tools" class="nav">

                    <?//Global search toggle option?>
                    <?php if(Settings::instance()->get('global_search') == 1): ?>
                    <li id="search2_wrapper">
                        <div id="resultscount"></div>
                        <input id="search2" placeholder="Enter text to search" />

						<input type="hidden" id="custom_search" value="<?= class_exists('Controller_Admin_Search') ? 1 : 0 ?>" />
                    </li>
                    <?php endif; ?>

                    <?php $help_links = explode("\n", trim(Settings::instance()->get('help_links'))); ?>
                    <?php if (sizeof($help_links) > 0 AND $help_links[0] != ''): ?>
                        <li>
                            <a><i class="icon-question-sign" title="<?= __('Help') ?>"></i><span>Help</span></a>
                            <ul>
                                <?php foreach($help_links as $link): ?>
                                    <?php
                                        $url = substr($link, strpos($link,'{')+1);
                                        $url = substr($url,0,strpos($url,'}'));
                                        $href = (strpos($url, "http://") === 0 OR strpos($url, "https://") === 0 )? $url : URL::site($url);
                                    ?>
                                    <li><a href="<?=$href;?>"
                                            target="<?= strpos($link,'[')!=-1 ? substr($link,strpos($link,'['),strpos($link,']')):'_self'; ?>">
                                            <?= substr($link,0,strpos($link,'{'));?>
                                        </a>
                                    </li>
                                <?php endforeach; // strpos($link,'}')?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (Auth::instance()->has_access('settings')): ?>
                    <li id="settings_icon"<?php if ($current_controller === 'settings' AND $current_action == 'index') { echo ' class="active"'; } ?>>
                        <?php if(Auth::instance()->has_access('settings')):?>
                            <a href="<?php echo URL::Site('admin/settings'); ?>"><i class="icon-cog" title="<?= __('Settings') ?>"></i><span><?=__('Settings')?></span></i></a>
                            <ul>
                                <li><a href="<?php echo URL::Site('admin/settings'); ?>"><?= __('Settings') ?></a></li>
                                <? // Generate this automatically, if feasible ?>
								<li><a href="/admin/settings/activities">Activities</a></li>
								<li><a href="/admin/settings/show_logs">App logs</a></li>
								<li><a href="/admin/calendars/index">Calendar</a></li>
								<li><a href="/admin/settings/crontasks">Cron</a></li>
								<li><a href="/admin/settings/csv">CSV</a></li>
								<li><a href="/admin/settings/list_logs">Dalm</a></li>
								<li><a href="/admin/settings/manage_feeds">Feeds</a></li>
								<li><a href="/admin/settings/ipwatcher_log">IP Watcher</a></li>
								<li><a href="/admin/settings/keyboardshortcuts">Shortcuts</a></li>
								<li><a href="/admin/settings/labels">Labels</a></li>
								<li><a href="/admin/settings/localisation_config">Localisation</a></li>
								<li><a href="/admin/settings/redirects">Redirects</a></li>
								<li><a href="/admin/settings/manage_resources">Resources</a></li>
								<li><a href="/admin/settings/manage_roles">User Roles</a></li>
								<li><a href="/admin/users">Users</a></li>
                            </ul>
                        <?php endif ?>
                    </li>
                    <?php endif; ?>

                    <li class="main_menu_logo<?= ($current_controller === 'settings' AND $current_action == 'users' AND $current_id == $logged_in_user['id']) ? ' active' : '' ?>" id="user_menu_dropdown">
                        <a class="user_tools_avatar">
                            <img src="<?= URL::get_avatar($logged_in_user['id']); ?>" alt="profile" width="23" height="23" title="<?= __('Profile: ').$logged_in_user['name'] ?>">
                            <span id="user_name"><?= $logged_in_user['name']; ?></span>
                        </a>
						<?php
						$notifications_html = implode(' ', $notification_links);
                        preg_match_all("/<span class=\"user_tools_notification_amount\".*>(.*)<\/span>/", $notifications_html, $notifications_amount);
                        ?>
						<?php if (trim($notifications_html) != '' AND isset($notifications_amount[1][0])): ?>
							<span class="user_tools_notification_amount" title="You have <?= $notifications_amount[1][0] ?> todos"><?= $notifications_amount[1][0]?></span>
						<?php endif; ?>
                        <ul>
                            <?php if(Auth::instance()->has_access('user_profile')): ?>
                                <li><a href="/admin/profile/edit?section=contact">Profile</a></li>
                            <?php endif; ?>
                            <?php if(Auth::instance()->has_access('my_activities')): ?>
							    <li><a href="/admin/settings/my_activities">Activities</a></li>
                            <?php endif; ?>
							<?php if (Model_Plugin::get_isplugin_enabled_foruser($logged_in_user['role_id'], 'todos')): ?>
								<li><a href="/admin/todos">Todos<?= (isset($notifications_amount[1][0]) AND $notifications_amount[1][0] != '') ? ' ('.$notifications_amount[1][0].')' : '' ?></a></li>
							<?php endif; ?>
                            <li><a href="<?php echo URL::Site('admin/login/logout'); ?>"><?= __('Logout')?></a></li>
                        </ul>
                    </li>
                </ul>

			</div>
            <style type="text/css">
                    /* TODO MOVE TO CSS FILE */
                    /*-------------------------------*
                        Dropdown Menu
                     *-------------------------------*/
                .main_menu_logo{ list-style:none; font-weight:bold; /* Clear floats */ float:left; width:100%; position:relative; z-index:5; }
                .main_menu_logo li{ float:left; margin-right:10px; position:relative; }
                .main_menu_logo a{ display:block; color: #08C; font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; line-height: 22px;
                    font-size: 13px; text-decoration: none; font-weight:normal;}
                .main_menu_logo h4{ color: #686868; font-weight: normal; padding: 7px 0; font-size: 14px; border-bottom: 1px solid darkgray;}
                    /*--- DROPDOWN ---*/
                .main_menu_logo ul{ list-style:none; position:absolute; left:-9999px; /* Hide off-screen when not needed (this is more accessible than display:none;) */
                    background-color: #E9EBEE; padding: 5px 20px 20px 20px; border-radius: 0px 1px 10px 10px; text-align: left; box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.4); margin-left: -25px}
                .main_menu_logo ul li{ padding-top:1px; /* Introducing a padding between the li and the a give the illusion spaced items */float:none;width: 150px; }
                .main_menu_logo ul li:hover{ cursor: pointer; color:white;}
                .main_menu_logo ul a{ white-space:nowrap; /* Stop text wrapping and creating multi-line dropdown items */ }
                .main_menu_logo li:hover ul{ /* Display the dropdown on hover */ left:0; /* Bring back on-screen when needed */ }
                .main_menu_logo li:hover ul li a:hover{ text-decoration: underline }
                    /*--- DROPDOWN L2---*/
                .main_menu_logo ul ul{display: none; }
                .main_menu_logo ul li:hover > ul{ cursor: pointer; color:white; display: inline; }


                    /*---- USER MENU DROPDOWN ---*/
                #user_menu_dropdown{ width: auto; margin: 0; padding: 0; }
                #user_menu_dropdown ul{ top: 40px; margin-left: -30px; width: 90px; margin-top: 0px; padding-top: 5px; }
                #user_menu_dropdown ul a{
                    width: 100px;
                }

                    /*------- Other CSS -------*/
                #menu_user{ height: 39px; }
                #menu_user img{ margin-top: 7px; }
                #user_name{ vertical-align: middle;}
            </style>
        </div>
	</div>
    <div id="navbar-level2">
        <div class="container">
            <?php if (isset($breadcrumbs) AND ! empty($breadcrumbs)): ?>
                <nav id="navbar-breadcrumbs">
                    <ol>
                        <?= implode($breadcrumbs); ?>
                    </ol>
                </nav>
            <?php endif; ?>
            <?php if (isset($plugin_tools) AND $plugin_tools != ''): ?>
                <div id="plugin_tools">
                    <?= $plugin_tools ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
        <?php
		$request_uri = $_SERVER['REQUEST_URI'];
		if (preg_match("~\bsms\b~",$request_uri) )
        {
		?>
			<div data-popup="messaging-sidebar-sms">
		<?php
				require_once('message_wrapper.php');
		?>
			</div>
		<?php	
        }
        ?>
</div>

<script type='text/javascript'>
    $(function()
    {
        $('#application_switcher').find('> ul > li > a').click(function(ev)
        {
            ev.preventDefault();
            $.ajax({
                url     : '/admin/settings/ajax_toggle_app/',
                data    : {
                    'user_id'     : <?= $logged_in_user['id'] ?>,
                    'application' : $(this).data('choice')
                },
                type     : 'post',
                dataType : 'json',
                async    : false
            }).done(function()
            {
                location.reload();
            });
        });
    });
</script>
