<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<!-- Model_Settings Get Setting for your caching
if true do nothing
else fill variable with time stamp
setup caching variable at the end of each resource that needs to be cached.
-->

<head>
    <?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $page_data['title'];?></title>
    <?php
        $disableCaching = Settings::instance()->get('enable_caching') == 'FALSE';
        $cache = $disableCaching ? '?cache='.time() : '';
    ?>
    <?php if ($disableCaching) : ?>
    <meta http-equiv="Cache-control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <?php endif; ?>
	<?php
	$localisation_content_active = Settings::instance()->get('localisation_content_active') == '1';
	if($localisation_content_active){
		$localisation_languages = Model_Localisation::languages_list();
		$generic_uri = substr(Request::$current->uri(), strlen(I18n::$lang) + 1);
		foreach($localisation_languages as $localisation_language){
			echo '<link rel="alternate" hreflang="' . $localisation_language['code'] . '" href="' . URL::site() . $localisation_language['code'] . '/' . $generic_uri . '" />' . "\n";
		}
	}
	?>
    <meta name="description" content="<?php echo $page_data['seo_description'];?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords'];?>">
    <meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
    <meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="shortcut icon" href="<?= URL::site() ?>assets/default/images/favicon.ico" type="image/ico"/>
    <?=settings::get_google_analitycs_script();?>
    <meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>" />
    <meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>" />
    <!-- modernizr library for IE 6-8 -->
    <!--[if lt IE 9]>
    <script src="<?=URL::site()?>assets/default/js/modernizr-2.5.3.js?<?=$cache?>"></script>
	<![endif]-->
    <link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/default/css/superfish.css<?=$cache?>" media="screen">
	<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>css/validation.css?<?=$cache?>"/>
	<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('payments') ?>css/front_end/styles.css" />
	<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('gallery') ?>css/lytebox.css<?=$cache?>"/>
	<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css<?=$cache?>" media="screen"/>
	<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/default/css/datepicker.css<?=$cache?>" media="screen"/>
	<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/default/css/paragridma.css<?=$cache?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('courses') ?>css/eventCalendar.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/default/css/eventCalendar_theme_responsive.css<?=$cache?>">
	<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/default/css/styles.css<?= $cache;?>"/>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js<?=$cache?>"></script>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/jquery.validationEngine2.js<?=$cache?>"></script>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>/js/jquery.validationEngine2-en.js<?=$cache?>"></script>
    <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/superfish.js<?=$cache?>"></script>
    <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/datepicker.js<?=$cache?>"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js<?=$cache?>"></script>
    <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('courses') ?>js/jquery.eventCalendar.js<?=$cache?>"></script>
    <script type="text/javascript" src="<?= URL::overload_asset('js/educate_template.js', ['cachebust' => true]) ?>"></script>
    <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/placeholder.js<?=$cache?>"></script>
	<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('payments') ?>js/front_end/payments.js"></script>
    <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('gallery') ?>js/lytebox.js"></script>
    <script type="text/javascript">
        // initialise plugins
        jQuery(function ()
        {
            jQuery('ul.sf-menu').superfish();
        });

    </script>
    <!-- for placer holder in IE -->
    <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/Placeholders.min.js<?= $cache;?>"></script>
    <!-- /for place holder in IE -->
    <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/custom-form-elements.js<?=$cache?>"></script>
    <script>
        $(document).ready(function ()
        {
            $("#navigation [href]").each(function ()
            {
                if (this.href == window.location.href)
                {
                    $(this).addClass("menu_active");
                }
            });
        });

        $(document).ready(function ()
        {

            // or with jQuery
            $('.main-content ol li').each(function ()
            {
                this.innerHTML = "<span>" + this.innerHTML + "</span>"
            })
        })
    </script>
    <script>
        //create dates for all events listed.
        var dates = [
            <?php
                $events = Model_Schedules::get_calendar_feed();
                $amt_events = count($events)-1;
                foreach($events AS $item => $element)
                {
                    $date = date("Y,n -1,j",strtotime($element['date']));
                    echo "new Date($date)";
                    if($item != $amt_events)
                    {
                    echo ",\n";
                    }
                }
            ?>];


        $(function() {

            $('#datepicker').datepicker({
                numberOfMonths: [1, 1],
                beforeShowDay: highlightDays
            }).click(function() {
                    $('.ui-datepicker-today a', $(this).next()).removeClass('ui-state-highlight ui-state-hover');
                });

            function highlightDays(date) {
                for (var i = 0; i < dates.length; i++) {
                    if (dates[i].getTime() == date.getTime()) {
                        return [true, 'highlight'];
                    }
                }
                return [true, ''];
            }
        });
    </script>
    <style type="text/css">
        #highlight, .highlight {
            /*background-color: #000000;*/
        }
        .highlight > a.ui-state-default {
            background: url("images/ui-bg_highlight-soft_25_ffef8f_1x100.png") repeat-x scroll 50% top ##A7C323 !important;
            border: 1px solid #F9DD34;
            color: #363636;
        }
        .ui-datepicker td a {padding:0;}
        .ui-datepicker {width:245px;}
        .ui-datepicker table {margin:0;}
    </style>
    <?= Settings::instance()->get('head_html'); ?>
</head>
