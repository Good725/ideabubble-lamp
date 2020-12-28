<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
	<?= $page_data['common_head_data']; ?>
	<script src="https://checkout.stripe.com/checkout.js"></script>
	<?php if (class_exists('Model_Media')): ?>
		<link rel="stylesheet" type="text/css" href="/frontend/media/fonts"/>
	<?php endif; ?>
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/normalize.css" rel="stylesheet" type="text/css" />
	<link href="<?= URL::overload_asset('css/jquery.datetimepicker.css') ?>" rel="stylesheet" type="text/css" />
	<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
		<link href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/searchbar.css" rel="stylesheet" type="text/css" />
	<?php endif; ?>
	<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
		<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('gallery'); ?>css/lytebox.css"/>
	<?php endif; ?>
	<link href="<?= URL::get_engine_plugin_assets_base('payments') ?>css/front_end/styles.css" rel="stylesheet" type="text/css" />
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/structure.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/bootstrap.min.css">
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css" rel="stylesheet" type="text/css" />
	<link href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic" rel="stylesheet" type="text/css" />
	<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
    <script src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/bootstrap.min.js"></script>
	<?= settings::get_google_analitycs_script(); ?>
	<script src="<?= URL::site() ?>assets/shared/js/daterangepicker/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/forms.js"></script>
	<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/checkout.js"></script>
	<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('payments') ?>js/front_end/payments.js"></script>
	<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
		<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('gallery');?>js/lytebox.js"></script>
	<?php endif; ?>
	
	<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/searchbar.js"></script>
	<?php endif; ?>
	<script type="text/javascript" src="<?= URL::get_skin_urlpath(TRUE) ?>js/jquery.elevateZoom-3.0.8.min.js"></script>
	<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
	<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
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
