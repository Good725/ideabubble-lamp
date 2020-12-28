<?php
$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
$settings = Settings::instance()->get();
$user = Auth::instance()->get_user();
?><!doctype html>
<html lang="en">
	<head>
		<?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title><?= $page_data['title'] ?></title>
		<?php $localisation_content_active = Settings::instance()->get('localisation_content_active') == '1'; ?>
		<?php if ($localisation_content_active): ?>
			<?php
			$localisation_languages = Model_Localisation::languages_list();
			$generic_uri = substr(Request::$current->uri(), strlen(I18n::$lang) + 1);
			?>
			<?php foreach($localisation_languages as $localisation_language): ?>
				<link rel="alternate" hreflang="<?= $localisation_language['code'] ?>" href="<?= URL::site() . $localisation_language['code'] . '/' . $generic_uri ?>" />
			<?php endforeach; ?>
		<?php endif; ?>
		<meta name="description" content="<?= $page_data['seo_description'];?>">
		<meta name="keywords" content="<?= $page_data['seo_keywords'];?>">
		<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
		<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		<link rel="shortcut icon" href="<?= URL::site() ?>/assets/lsm/images/favicon.ico" type="image/ico"/>
		<?=settings::get_google_analitycs_script();?>
		<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>" />
		<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>" />

		<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/default/css/validation.css" />
		<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/shared/css/jquery.datetimepicker.min.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/eventCalendar.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/swiper.min.css" />
		<link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css" />

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
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
		<?= Settings::instance()->get('head_html'); ?>
	</head>
	<body class="template-lsm layout-<?= $page_data['layout'] ?>">
		<div class="wrapper">
