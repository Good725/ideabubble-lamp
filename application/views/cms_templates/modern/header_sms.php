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
								<li><a href="/admin/usermanagement/users">User Management</a></li>
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
		$classes[] = '<i class="fa fa-envelope-o" aria-hidden="true"></i> <span class="counts">5</span>';
		$classes[] = '<i class="fa fa-paper-plane" aria-hidden="true"></i> <span class="counts">3</span>';
		$classes[] = '<i class="fa fa-star" aria-hidden="true"></i>';
		$classes[] = '<i class="fa fa-paper-plane" aria-hidden="true"></i>';
		$classes[] = '<i class="fa fa-calendar" aria-hidden="true"></i>';
		$classes[] = '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
		$classes[] = '<i class="fa fa-bug" aria-hidden="true"></i>';
		$classes[] = '<i class="fa fa-users" aria-hidden="true"></i>';
		$classes[] = '<i class="fa fa-trash" aria-hidden="true"></i>';
		$classes[] = '<i class="fa fa-cog" aria-hidden="true"></i>';
		$classes[] = '<i class="fa fa-wrench" aria-hidden="true"></i>';
		if (preg_match("~\bmessaging\b~",$request_uri) )
        {
        ?>
        <div class="message-wrapper">
            <div class="container">
                <div class="message-close">
                    <a href="javascript:void(0)"><i class="fa fa-times" aria-hidden="true"></i> 
                        <span>ESC</span>
                    </a>
                </div>
                <div class="table-wrapper">
                    <div class="tab-cell message--nav">
                        <h3>Michael O’Callaghan <a class="pullBtn" href="javascript:void(0);"><i class="fa fa-plus-circle" aria-hidden="true"></i></i></a>
                            <ul class="toggle-box mail--list">
                                <li><a href="#">Template <i class="fa fa-check" aria-hidden="true"></i></a></li>
                                <li><a href="#">Send an Email <i class="fa fa-check" aria-hidden="true"></i></a></li>
                                <li><a class="detail-btn" href="javascript:void(0)" rel="send-sms">Send an SMS <i class="fa fa-check" aria-hidden="true"></i></a></li>
                                <li><a  href="#">Send an Alert <i class="fa fa-check" aria-hidden="true"></i></a></li>
                            </ul>

                        </h3>
                        <ul class="">

                         <?php    
        					$i = 0;
                            foreach ($submenu_message as $entry)
        					{
        						echo '<li><a href="' . URL::Site( $entry['url']) . '">' .$classes[$i] . __($entry['name']) . '</a></li>';
        						$i++;
        					}
                        ?>
                        <li class="dropdown-nav"><a class="pullBtn" href="javascript:void(0)">Templates</a>
                            <ul class="toggle-box sub--nav">
                                <li><a href="#">Template 1</a></li>
                                <li><a href="#">Template 2</a></li>
                                <li><a href="#">Template 3</a></li>
                                <li><a href="#">Template 4</a></li>
                            </ul>

                        </li>

                        </ul>
                    </div> 
                    <div class="tab-cell grayBg">
                        <div class="search-wrap">
                            <input type="text" placeholder="Search">
                            <button class="search-btn">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="body-area">
                            <div class="toptitle">
                                <div class="action">
                                    <label><input type="checkbox" class="checked-all">Select All</label>
                                </div>
                                <div class="filter">
                                    <a href="javascript:void(0);" class="pullBtn">Filter <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                    <ul class="toggle-box filter--nav">
                                        <li><a href="#">Email + SMS <i class="fa fa-check" aria-hidden="true"></i></a></li>
                                        <li><a href="#">SMS <i class="fa fa-check" aria-hidden="true"></i></a></li>
                                        <li><a href="#" class="active">E-mail <i class="fa fa-check" aria-hidden="true"></i></a></li>
                                    </ul>
                                </div>
                                <ul class="messaging-sidebar-actions">
                                    <li><a href="#"><i class="fa fa-trash" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa fa-bug" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa fa-star-o" aria-hidden="true"></i></li></a></li>
                                    <li><a href="#"><i class="fa fa-envelope" aria-hidden="true"></i></a></li>
                                    <li><a href="#"><i class="fa fa-envelope-open" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>                        
                            <ul class="medialist">
                                <li class="unread">
                                    <div class="grid first">
                                        <ul class="user-action">
                                            <li><i class="fa fa-envelope" aria-hidden="true"></i></li>
                                            <li><input type="checkbox" class="input-field"></li>
                                            <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                        </ul>
                                    </div>
                                    <div class="grid second">
                                        <h4>Maja Otic</h4>
                                        <p>Subject Line</p>
                                    </div>
                                    <span class="grid third">08:00</span>
                                </li>
                                <li class="unread">
                                    <div class="grid first">
                                        <ul class="user-action">
                                            <li><i class="fa fa-mobile" aria-hidden="true"></i></li>
                                            <li><input type="checkbox" class="input-field"></li>
                                            <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                        </ul>
                                    </div>
                                    <div class="grid second">
                                        <h4>Stephen King</h4>
                                        <p>Subject Line</p>
                                    </div>
                                    <span class="grid third">Yesterday</span>
                                </li>
                                <li>
                                    <div class="grid first">
                                        <ul class="user-action">
                                            <li><i class="fa fa-envelope" aria-hidden="true"></i></li>
                                            <li><input type="checkbox" class="input-field"></li>
                                            <li class="starred"><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                        </ul>
                                    </div>
                                    <div class="grid second">
                                        <h4>Maja Otic</h4>
                                        <p>Subject Line</p>
                                    </div>
                                    <span class="grid third">10/19</span>
                                </li>
                                <li>
                                    <div class="grid first">
                                        <ul class="user-action">
                                            <li><i class="fa fa-envelope" aria-hidden="true"></i></li>
                                            <li><input type="checkbox" class="input-field"></li>
                                            <li class="starred"><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                        </ul>
                                    </div>
                                    <div class="grid second">
                                        <h4>Maja Otic</h4>
                                        <p>Subject Line</p>
                                    </div>
                                    <span class="grid third">10/19</span>
                                </li>
                                <li>
                                    <div class="grid first">
                                        <ul class="user-action">
                                            <li><i class="fa fa-envelope" aria-hidden="true"></i></li>
                                            <li><input type="checkbox" class="input-field"></li>
                                            <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                        </ul>
                                    </div>
                                    <div class="grid second">
                                        <h4>Bernadette O’Connor</h4>
                                        <p>Subject Line</p>
                                    </div>
                                    <span class="grid third">10/19</span>
                                </li>

                            </ul>
                        </div>
                        <div class="pagination-wrap">
                            <span class="grid-1">Show 4 of 1267 items</span>
                            <ul class="pagination-btn">
                                <li><a href="#"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-cell">
                        <div class="mail-wrap"> 
                            <div class="top-head">
                                <div class="f-left">
                                    <ul>
                                        <li><a href="#"><i class="fa fa-reply" aria-hidden="true"></i></a></li>                                        
                                        <li><a href="#"><i class="fa fa-share" aria-hidden="true"></i></a></li>                                        
                                    </ul>
                                </div>
                                <div class="f-right">
                                    <ul>
                                        <li><a href="javascript:void(0)" class="add-btn" rel="check-link"><i class="fa fa-link" aria-hidden="true"></i></a></li>
                                        <li><a href="javascript:void(0)" class="basic_close"><i class="fa fa-times" aria-hidden="true"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="user-info">
                                <div class="left-section">
                                    <figure class="imgbox">
                                        <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/user-img.jpg">
                                    </figure>
                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                </div>  
                                <div class="right-section">
                                    <ul>
                                        <li><label>From:</label>Jane Doe (+381 160 94 39)</li>
                                        <li class="time">08:00</li>
                                    </ul>
                                    <h5><label>Subject:</label>SMS notification about policy</h5>                                   
                                </div>
                            </div>
                            <div id="check-link" class="add-files-wrap read--links">
                                <div class="link-wrap">
                                    <div class="grid">Select</div>
                                    <div class="grid">
                                        <div class="select-wrap">
                                            <select>
                                                <option>Contacts</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid">    
                                        <a href="#" class="white-btn">Add</a>
                                    </div>    
                                </div>    
                            </div>
                            <div class="descbody">
                               These are the assumptions we have made to make the quoting process easier for you to perform.
                            </div>
                        </div>
                        <div id="send-sms" class="content-box create--email">
                                <div class="top-head">
                                    <div class="f-left">
                                        <ul class="tabs-pills">
                                            <li><a href="javascript:void(0)" rel="write" class=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                <span class="hide-small-scr">Message</span></a>
                                            </li>
                                            <li><a href="javascript:void(0)" rel="Schedule"><i class="fa fa-clock-o" aria-hidden="true"></i>
                                                <span class="hide-small-scr">Schedule</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="f-right">
                                        <ul>
                                            <li><a href="javascript:void(0)" rel="add-link" class="add-btn"><i class="fa fa-link" aria-hidden="true"></i></a></li>
                                            <li><a href="javascript:void(0)" class="detail-btn" rel="add-attachment"><i class="fa fa-paperclip" aria-hidden="true"></i></a></li>
                                            <li><a href="javascript:void(0)" class="basic_close"><i class="fa fa-times" aria-hidden="true"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="write" class="tabs-pills-content">
                                    <div class="write-email">
                                        <form>
                                            <div class="top">
                                                <div class="full-cols">
                                                    <div class="grid-2">From</div>
                                                    <div class="grid-2">
                                                        <div class="fields-wrap">
                                                            <input type="text" placeholder="Michael (+385 123 4567)"> 
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="full-cols">
                                                    <div class="grid-2">To</div>
                                                    <div class="grid-2">
                                                        <div class="fields-wrap">
                                                            <input type="text" placeholder="Type to add contact or contact list"> 
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="full-cols">
                                                    <div class="grid-2">Subject</div>
                                                    <div class="grid-2">
                                                        <div class="fields-wrap">
                                                            <input type="text" placeholder="Subject"> 
                                                        </div>
                                                    </div>    
                                                </div>
                                        
                                            
                                                <div id="add-link" class="full-cols add-files-wrap">
                                                    <div class="grid-2">Link to</div>
                                                    <div class="grid-2">
                                                        <div class="fields-wrap">
                                                            <div class="link-wrap">
                                                                <div class="grid">Select</div>
                                                                <div class="grid">
                                                                    <div class="select-wrap">
                                                                        <select>
                                                                            <option>Contacts</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="grid">    
                                                                    <a href="#" class="white-btn">Add</a>
                                                                </div>    
                                                            </div>
                                                        </div>
                                                    </div>    
                                                </div>
                                            </div>
                                            <div class="middle padding10">
                                                <textarea>Type your message here</textarea>
                                            </div>
                                  
                                            <div class="bottom-btn">
                                                <input class="border-btn" type="button" value="Save as template"><br/>
                                                <button class="border-btn">
                                                    Save as Draft <i class="fa fa-angle-down" aria-hidden="true"></i>
                                                </button>
                                                <button class="border-btn">
                                                    Send <i class="fa fa-angle-down" aria-hidden="true"></i>
                                                </button>
                                                <a href="#" class="cancel-btn">Cancel</a>  
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div id="Schedule" class="tabs-pills-content">
                                    <div class="padding10">
                                        <div class="full-grid margin-bottom-15"> 
                                            <h2 class="theme-heading f-left">Schedule</h2>
                                            <div class="btn-group btn-group-slide f-left" data-toggle="buttons">
                                                <label class="btn btn-plain  active">
                                                    <input type="radio" checked="checked" value="1" name="publish">Yes
                                                </label>
                                                <label class="btn btn-plain ">
                                                    <input type="radio" value="0" name="publish">No
                                                </label>
                                            </div>
                                        </div>   
                                        <div class="full-grid margin-bottom-15">   
                                            <h2 class="theme-heading">Interval</h2>
                                            <ul class="set-interval">
                                                <li>
                                                    Minutes
                                                    <label class="icon-wrap">
                                                        <input value="02:00am" type="text">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    </label>
                                                </li>
                                                <li>
                                                    Hours
                                                    <label class="icon-wrap">
                                                        <input value="02:00am" type="text">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    </label>
                                                </li>
                                                <li>
                                                    Dates
                                                    <label class="icon-wrap">
                                                        <input value="02:00am" type="text">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </label>
                                                </li>
                                                <li>
                                                    Months
                                                    <label class="icon-wrap">
                                                        <input value="02:00am" type="text">
                                                       <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </label>
                                                </li>
                                            </ul>     
                                            

                                        </div>
                                        <div class="sub-title">
                                        <h3>Days in Week</h3>
                                        </div>
                                        <ul class="week-name">
                                            <li><a href="#" class="disable">S</a></li>
                                            <li><a href="#">M</a></li>
                                            <li><a href="#">T</a></li>
                                            <li><a class="selected" href="#">W</a></li>
                                            <li><a href="#" class="selected">T</a></li>
                                            <li><a href="#">F</a></li>
                                            <li><a href="#" class="disable">S</a></li>
                                        </ul>
                                        <div class="bottom-btn">
                                            <input class="border-btn" type="button" value="Save as template"><br/>
                                            <button class="border-btn">
                                                Save as Draft <i class="fa fa-angle-down" aria-hidden="true"></i>
                                            </button>
                                            <button class="border-btn">
                                                Send <i class="fa fa-angle-down" aria-hidden="true"></i>
                                            </button>
                                            <a href="#" class="cancel-btn">Cancel</a>  
                                        </div>
                                    </div>
                                </div>
                              
                         </div><!-- send email end -->
                    </div>
                   
                    <div class="tab-cell last"></div>
                     
                </div>
            </div>
        <!-- popup html -->

        <div id="popup" class="sectionOverlay">
            <div class="overlayer"></div>
            <div class="screenTable">
                <div class="screenCell">
                    <div class="sectioninner">
                        <a class="popup_close"><i class="fa fa-times" aria-hidden="true"></i></a>
                        <div class="popup-content">
                             <ul class="attachment-slider">
                                <li>
                                    <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/slider-img.jpg">
                                    <div class="slider-caption">
                                        <h4>The Name of Image</h4>
                                        <p>Nullam pretium auctor massa ut consequat. Etiam hendrerit iaculis mattis. Proin tincidunt<br/> gravida augue in tempor. Proin blandit, elit a consectetur tincidunt, velit quam tristique risus, eget</p>
                                    </div>
                                </li>
                                <li>
                                    <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/slider-img2.jpg">
                                     <div class="slider-caption">
                                        <h4>The Name of Image</h4>
                                        <p>Nullam pretium auctor massa ut consequat. Etiam hendrerit iaculis mattis. Proin tincidunt<br/> gravida augue in tempor. Proin blandit, elit a consectetur tincidunt, velit quam tristique risus, eget</p>
                                    </div>
                                </li>
                                <li>
                                    <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/slider-img3.jpg">
                                     <div class="slider-caption">
                                        <h4>The Name of Image</h4>
                                        <p>Nullam pretium auctor massa ut consequat. Etiam hendrerit iaculis mattis. Proin tincidunt<br/> gravida augue in tempor. Proin blandit, elit a consectetur tincidunt, velit quam tristique risus, eget</p>
                                    </div>
                                </li>
                                <li>
                                    <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/slider-img2.jpg">
                                     <div class="slider-caption">
                                        <h4>The Name of Image</h4>
                                        <p>Nullam pretium auctor massa ut consequat. Etiam hendrerit iaculis mattis. Proin tincidunt<br/> gravida augue in tempor. Proin blandit, elit a consectetur tincidunt, velit quam tristique risus, eget</p>
                                    </div>
                                </li>
                            </ul>
                            <ul class="slider-nav">
                                <li>
                                    <div class="imgbox">
                                        <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/slider-img.jpg">
                                    </div>
                                </li>
                                <li>
                                    <div class="imgbox">    
                                        <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/slider-img2.jpg">
                                    </div>
                                </li>
                                <li>
                                    <div class="imgbox">
                                        <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/slider-img3.jpg">
                                    </div>
                                </li>
                                <li>
                                    <div class="imgbox">    
                                        <img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/slider-img2.jpg">
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <!--  popup end  -->
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
