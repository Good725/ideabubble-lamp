<?php
$logged_in_user = Auth::instance()->get_user();
?>
<div class="navbar navbar-static-top">
	<div class="navbar-inner">
        <? /* Temporary, until this is database driven */ ?>
        <?php 
			$cms_skin = Settings::instance()->get('cms_skin');
			if ($cms_skin == 'kes'): ?>
            <?php $app_mode = Cookie::get($logged_in_user['id'].'_application'); ?>
            <div id="application_switcher" class="dropdown" title="Toggle application mode">
                <ul>
                    <li <?= ($app_mode == 'ibcms' OR $app_mode == '') ? ' class="current"' : '' ?>><a href="#" data-choice="ibcms">IB CMS</a></li>
                    <li <?= ($app_mode == 'ibeducate') ? ' class="current"' : '' ?>><a href="#" data-choice="ibeducate">IB Educate</a></li>
                </ul>
            </div>
        <?php endif; ?>
		<div class="">
			<div class="nav-collapse">
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

                <ul id="user_tools" class="nav">

                    <li id="search2_wrapper">
                        <div id="resultscount"></div>
                        <input id="search2" placeholder="Enter text to search" />
                    </li>


                    <li>
                        <a><i class="icon-question-sign" title="<?= __('Help') ?>"></i><span>Help</span></a>
                        <ul>
                            <li><a href="http://wiki.ideabubble.ie/confluence/display/WPP/WPP+Users%27s+Guide" target="_blank">User's Guide</a></li>
                        </ul>
                    </li>

                    <li id="settings_icon"<?php if ($current_controller === 'settings' AND $current_action == 'index') { echo ' class="active"'; } ?>>
                        <?php if(Auth::instance()->has_access('settings')):?>
                            <a href="<?php echo URL::Site('admin/settings'); ?>"><i class="icon-cog" title="<?= __('Settings') ?>"></i><span><?=__('Settings')?></span></i></a>
                            <ul>
                                <?= implode(' ', $notification_links); ?>
                                <li><a href="<?php echo URL::Site('admin/settings'); ?>">Settings</a></li>
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

                    <li class="main_menu_logo<?php if ($current_controller === 'settings' AND $current_action == 'users' AND $current_id == $logged_in_user['id']) { echo ' active'; } ?>" id="user_menu_dropdown">
                        <a class="user_tools_avatar">
                            <img src="<?= URL::get_avatar($logged_in_user['id']); ?>" alt="profile" width="23" height="23" title="<?= __('Profile: ').$logged_in_user['name'] ?>">
                            <span id="user_name"><?php echo $logged_in_user['name']; ?></span>
                        </a>
                        <ul>
                            <li><a href="/admin/profile/edit?section=contact">Profile</a></li>
                            <li><a href="<?php echo URL::Site('admin/login/logout'); ?>">Logout</a></li>
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
                <div class="plugin_tools" id="plugin_tools">
                    <?= $plugin_tools ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="navbar-sub">
		<div class="container">
			<ul class="">
				<?=implode(' ', $submenu)?>
            </ul>
		</div>
	</div>

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

    $.widget( "custom.catcomplete", $.ui.autocomplete,
    {
        _renderMenu: function( ul, items )
        {
            ul.addClass('ideabubble_site_search');
            var that = this,
                currentCategory = "";
            $.each( items, function( index, item )
            {
                if ( typeof item.category != 'undefined')
                {
                    if ( item.category != currentCategory )
                    {
                        ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    if(item.label == undefined){
                        item.label = 'NULL';
                    }
                    that._renderItem( ul, item );
                }
            });
        },
        _renderItem:function(ul, item){
            var search_value = $('#search2:visible, #search2-mobile:visible').val().toLowerCase();
            return $( "<li></li>" )
                .data( "item.autocomplete", item )
                //.hover(function(){$('#search2').val($(this).children().html())})
                .append("<a href='"+item.link+"'>" + item.label.toLowerCase().replace(search_value, '<strong>'+search_value +'</strong>') + "</a>" )
                .appendTo( ul );
        }
    });
    $(function()
    {
        var xhr = null;
        var searchValue = $('#search2:visible, #search2-mobile:visible').val();
        $(document).ready(function(){
            searchValue = '';
        });
        $("#search2, #search2-mobile").catcomplete({
            source: function (request, response) {
                if (!xhr) {
                    xhr = $.ajax({
                        url: "/admin/searchbar/ajax_getresults/"+searchValue,
                        timeout: 20000,
                        data: request,
                        dataType: "json",
                        delay:200,
                        success: function (data) {
                            xhr = null;
                            response(data);
                        },
                        error: function () {
                            $(this).removeClass("searchboxwait");
                            $('#resultscount').css({'display':'block'});
                            response([]);
                        }
                    });
                }
            },
            minLength: 0,
            search: function(event, ui) {
                $(this).addClass("searchboxwait");
                $('#resultscount').hide();

            },
            open: function(event, ui) {
                $(this).removeClass("searchboxwait");
                $('#resultscount').show();
            },
            response: function(event, ui){
                if(ui.content.length == 0){
                    $('#resultscount').html('No Results.');
                    $(this).removeClass("searchboxwait");
                }
                else
                {
                    if(ui.content[0]['label'] == 'No Results.'){
                        $('#resultscount').html('No Results.');
                        $(this).removeClass("searchboxwait");
                    }
                    else{
                        console.log(ui.content);
                        $('#resultscount').html(ui.content[ui.content.length-1]['value']+' results');
                    }
                }
            }
        });
    });
</script>
