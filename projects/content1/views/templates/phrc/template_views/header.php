<!DOCTYPE html>
<?php // The "no-js" class gets replaced with "js", after Modernizr runs ?>
<!--[if IE 8]> <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if IE 9]> <html class="no-js lt-ie10" lang="en" > <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en" > <!--<![endif]-->
	<head>
		<?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
		<?= $page_data['common_head_data']; ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		<link rel="canonical" href="<?= str_replace('.html.html', '.html', URL::base().$page_data['name_tag'].'.html') ?>" />

		<meta property="og:locale" content="en_GB" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="<?= $page_data['title'] ?>" />
		<meta property="og:description" content="<?= $page_data['seo_description'] ?>" />
		<meta property="og:url" content="<?= str_replace('.html.html', '.html', URL::base().'/'.$page_data['name_tag'].'.html') ?>" />
		<meta property="og:site_name" content="<?= Settings::instance()->get('company_name') ?>" />

		<link rel="stylesheet" href="/assets/<?= $assets_folder_path ?>/css/styles.css" type="text/css" media="all" id="main-css" />

		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
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

	<body class="layout-<?= $page_data['layout'] ?> page page-id-<?= $page_data['id'] ?> page-template-<?= $page_data['category'] ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="position:absolute" />
		<header class="header">
			<div class="backdrop">
				<?php $telephone = trim(Settings::instance()->get('telephone')); ?>
				<?php if ($telephone): ?>
					<div class="row">
						<span class="header-telephone">Call <?= $telephone ?></span>
					</div>
				<?php else: ?>
					&nbsp;
				<?php endif; ?>
			</div>
			<div class="row header-logos">
				<div class="small-6 columns">
					<a href="/<?= $page_data['theme_home_page'] ?>">
						<img src="/assets/<?= $assets_folder_path ?>/images/logo.png" class="logo-image" />
					</a>
				</div>

				<div class="small-6 columns text-right">
					<img src="/assets/<?= $assets_folder_path ?>/images/wts-logo.png" />
				</div>
			</div>

			<div id="menu-wrapper">
				<div class="home-menu" id="home-menu">
					<div class="row collapse header-menu">
						<div class="title-bar" data-responsive-toggle="main-menu" data-hide-for="medium">
							<button class="menu-icon" type="button" data-toggle></button>
							<div class="title-bar-title">Menu</div>
						</div>

						<div class="medium-9 columns" id="main-menu">
							<?= menuhelper::add_menu_editable_heading('main', 'menu') ?>
						</div>
						<div class="medium-3 columns text-center consulation-button-position">
							<a href="/book-consultation.html" class="consultation-button"><?= __('Book a Consultation') ?></a>
						</div>
					</div>
				</div>
			</div>

		</header>
		<main class="main-content">
			<div class="page-banner"><?= Model_PageBanner::render_frontend_banners($page_data['banner_photo'], FALSE); ?></div>

