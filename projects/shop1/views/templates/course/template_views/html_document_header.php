<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>

	<?php if (Settings::instance()->get('search_engine_indexing') === 'FALSE'): ?>
		<meta name='robots' content='NOINDEX, NOFOLLOW'/>
	<?php endif; ?>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="description" content="<?php echo $page_data['seo_description']; ?>">
	<meta name="keywords" content="<?php echo $page_data['seo_keywords']; ?>">
	<meta name="author" content="//ideabubble.ie">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="google-site-verification" content="<?= Settings::instance()->get('google_webmaster_code') ?>"/>
	<meta name="msvalidate.01" content="<?= Settings::instance()->get('bing_webmaster_code') ?>"/>
	<title><?= $page_data['title']; ?></title>
	<link rel="shortcut icon" href="/assets/<?= $assets_folder_path ?>/images/favicon.ico" type="image/ico"/>
	<?php if (class_exists('Model_Media')): ?>
		<link rel="stylesheet" type="text/css" href="/frontend/media/fonts"/>
	<?php endif; ?>


    <?php if ($page_data['layout'] == 'home'): ?>
        <link rel="stylesheet" href="/assets/<?= $assets_folder_path ?>/css/eventCalendar.css" />
        <link rel="stylesheet" href="/assets/<?= $assets_folder_path ?>/css/eventCalendar_theme_responsive.css" />
    <?php endif; ?>
	<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
		<link rel="stylesheet" href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/searchbar.css"
			  type="text/css"/>
	<?php endif; ?>
	<link rel="stylesheet" href='//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic'
		  type='text/css'>
	<link rel="stylesheet" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/validation.css"
		  type="text/css"/>
	<link rel="stylesheet" href="<?= URL::site() ?>assets/shared/css/browserorg/style.css" />
	<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
		<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('gallery');?>js/lytebox.js"></script>
	<?php endif; ?>
	<link rel="stylesheet" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css" type="text/css"/>

	<?= settings::get_google_analitycs_script(); ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base() ?>js/libs/jquery-1.7.2.min.js"><\/script>')</script>
    <?php if ($page_data['layout'] == 'home'): ?>
        <script src="/assets/<?= $assets_folder_path ?>/js/jquery.eventCalendar.js"></script>
    <?php endif; ?>
	<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/checkout.js"></script>
	<script type="text/javascript" src="/assets/16/js/jquery.validationEngine2.js"></script>
	<script type="text/javascript" src="/assets/16/js/jquery.validationEngine2-en.js"></script>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/forms.js"></script>
	<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/searchbar.js"></script>
	<?php endif; ?>
	<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
		<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('gallery');?>js/lytebox.js"></script>
	<?php endif; ?>
	<script type="text/javascript" src="<?= URL::site() ?>assets/16/js/general.js"></script>
	<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
    <?php include APPPATH.'views/background_switcher.php' ?>
	<?= Settings::instance()->get('head_html'); ?>
	<!-- Browser detection -->
	<script type="text/javascript">
		var test_mode = <?= (Settings::instance()->get('browser_sniffer_testmode') == 1) ? 'true' : 'false'; ?>;
	</script>
	<script src="<?= URL::site() ?>assets/shared/js/browserorg/main.js"></script>
	<?php if (Settings::instance()->get('cookie_enabled') === 'TRUE'): ?>
		<!-- Cookie consent plugin by Silktide - http://silktide.com/cookieconsent -->
		<script type="text/javascript">
			<?php
            $cookie_message      = Settings::instance()->get('cookie_text');
            $cookie_dismiss_text = Settings::instance()->get('hide_notice_message');
            $cookie_link         = Settings::instance()->get('cookie_page');
            $cookie_link_text    = Settings::instance()->get('link_text');
            $cookie_message      = $cookie_message      ? $cookie_message      : 'This website uses cookies to ensure you get the best experience on our website';
            $cookie_dismiss_text = $cookie_dismiss_text ? $cookie_dismiss_text : 'Got it!';
            $cookie_link_text    = $cookie_link_text    ? $cookie_link_text    : 'More info';
            $cookie_consent_options = array(
                "message" => $cookie_message,
                "dismiss" => $cookie_dismiss_text,
                "learnMore" => $cookie_link_text,
                "link" => $cookie_link ? Model_Pages::get_page_by_id($cookie_link) : null,
                "theme" => "dark-bottom"
            );
            ?>
			window.cookieconsent_options = <?=json_encode($cookie_consent_options)?>; // use proper js encoding to handle special characters ' " \ / etc...
		</script>
		<script src="<?= URL::site() ?>assets/shared/js/cookieconsent/cookieconsent.min.js"></script>
	<?php endif; ?>
</head>
