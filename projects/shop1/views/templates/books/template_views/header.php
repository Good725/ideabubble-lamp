<!DOCTYPE html>
<html lang="en">
	<head>
		<?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
		<?php if (Settings::instance()->get('search_engine_indexing') === 'FALSE'): ?>
			<meta name="robots" content="noindex, nofollow"/>
		<?php endif; ?>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="author" content="//ideabubble.ie">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
		<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
		<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>

        <?php if ( ! empty($page_data['product_data'])): ?>
            <?php
            $seo_description = (empty($page_data['product_data']['brief_description']))
                ? strip_tags($page_data['product_data']['description'])
                : htmlentities($page_data['product_data']['brief_description']);
            ?>

            <title><?= trim($page_data['product_data']['title'].' '.Settings::instance()->get('seo_title_text_separator').' '.Settings::instance()->get('seo_title_text')) ?></title>
            <meta name="description" content="<?= $seo_description; ?>" />

            <meta property="og:title" content="<?= $page_data['product_data']['title'] ?>" />
            <meta property="og:type" content="book" />
            <meta property="og:book:author" content="<?= $page_data['product_data']['author'] ?>" />
            <meta property="og:description" content="<?= $seo_description ?>" />

            <meta name="twitter:card" content="summary" />
            <meta name="twitter:title" content="<?= $page_data['product_data']['title'] ?>" />
            <meta name="twitter:description" content="<?= strlen($seo_description) > 200 ? substr($seo_description, 0, 197).'...' : $seo_description ?>" />

            <?php if ( ! empty($page_data['product_data']['images']) && ! empty($page_data['product_data']['images'][0])): ?>
                <?php
                // More ideally, this should be handled in the controller.
                $base_path  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'products');
                $image_url  = $base_path.$page_data['product_data']['images'][0];
                $image      = Model_Media::get_by_filename($page_data['product_data']['images'][0], 'products');
                $dimensions = explode('x',$image['dimensions']);
                $width      = isset($dimensions[0]) ? $dimensions[0] : 1;
                $height     = isset($dimensions[1]) ? $dimensions[1] : 1.5;
                ?>

                <meta property="og:image" content="<?= $image_url ?>" />
                <meta property="og:image:width" content="200" />
                <meta property="og:image:height" content="<?= round(200 / $width * $height) ?>" />
                <meta property="og:image:type" content="<?= $image['mime_type'] ?>" />

                <meta name="twitter:image:src" content="<?= $image_url ?>" />
            <?php endif; ?>
        <?php else: ?>
            <title><?= $page_data['title']; ?></title>

            <meta name="description" content="<?= $page_data['seo_description']; ?>"/>
            <meta name="keywords" content="<?= $page_data['seo_keywords']; ?>"/>

            <meta property="og:title" content="<?= $page_data['title'] ?>" />
            <meta property="og:type" content="article" />
            <meta property="og:description" content="<?= $page_data['seo_description'] ?>" />

            <meta name="twitter:card" content="summary" />
            <meta name="twitter:title" content="<?= $page_data['title'] ?>" />
            <meta name="twitter:description" content="<?= $page_data['seo_description'] ?>" />
        <?php endif; ?>

        <?= i18n::get_canonical_link(); ?>
        <?= i18n::get_alternate_links(); ?>

        <link rel="shortcut icon" href="/assets/<?= $assets_folder_path ?>/images/favicon.ico" type="image/ico" />
        <?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
            <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('gallery'); ?>css/lytebox.css"/>
        <?php endif; ?>
        <link rel="stylesheet" type="text/css" href="/assets/<?= $assets_folder_path ?>/css/validation.css" />
        <link rel="stylesheet" type="text/css" href="/assets/<?= $assets_folder_path ?>/css/styles.css" />
        <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('products').'css/front_end/product_display_mode.css' ?>" />
        <?= Settings::get_google_analitycs_script() ?>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/jquery-1.7.2.min.js"><\/script>')</script>
		<script src="<?= URL::site() ?>assets/shared/js/daterangepicker/jquery.datetimepicker.js"></script>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/checkout.js"></script>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('payments') ?>js/front_end/payments.js"></script>
		<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
			<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('gallery');?>js/lytebox.js"></script>
		<?php endif; ?>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/forms.js"></script>
		<!--[if lt IE 9]>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/respond.src.js"></script>
		<script src="//ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
		<![endif]-->
		<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
			<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/searchbar.js"></script>
		<?php endif; ?>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
		
		<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
		<script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
		<?= Settings::instance()->get('head_html'); ?>

		<!-- Browser detection -->
		<script type="text/javascript">
			var test_mode = <?= (Settings::instance()->get('browser_sniffer_testmode') == 1) ? 'true' : 'false' ?>;
		</script>
		<script src="<?= URL::site() ?>assets/shared/js/browserorg/main.js"></script>
		<link rel="stylesheet" href="<?= URL::site() ?>assets/shared/css/browserorg/style.css" />
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
	<body class="layout-<?= $page_data['layout']?>">
		<div class="wrapper">
			<header class="header">
				<button type="button" class="sidebar-toggle" id="sidebar-toggle"><span></span></button>
				<div class="header-logo">
					<a href="/">
						<img src="/assets/<?= $assets_folder_path ?>/images/logo.png" class="logo_image">
					</a>
				</div>

				<div class="header-title">
					<div class="header-company-title"><?= Settings::instance()->get('company_title') ?></div>
					<div class="header-company-slogan"><?= Settings::instance()->get('company_slogan') ?></div>
				</div>

				<div class="header-shopping-area">
					<?php $number_of_items = Model_Checkout::get_cart_value('number_of_items'); ?>

					<div>
						<div class="header-cart-wrapper<?= (Settings::instance()->get('cart_hidden_when_empty')) ? ' minicart-hidden-when-empty' : ''; ?>" data-product_count="<?= $number_of_items ?>">
							<div class="header-cart">
								<div class="header-cart-amount"><span><?= $number_of_items ?></span></div><!--
								--><div class="header-cart-total">&euro;<span id="mycart_total_price"><?= number_format(Model_Checkout::get_cart_total_price_value(), 2) ?></span></div><!--
								--><a href="/checkout.html" class="header-cart-link">
									<span class="shopping-bag-text"><?= __('Checkout') ?></span>
								</a>
							</div>
						</div>

						<?php if (Settings::instance()->get('localisation_content_active') === '1'): ?>
							<div class="header-language-switcher">
								<ul>
									<?php
									$url = Request::current()->uri().URL::query();
									$url = substr($url, strpos($url, '/'));
									?>
									<?php foreach ($localisation_languages as $ln): ?>
										<?php
										$ln['title'] = ($ln['title'] == 'united kingdom') ? 'English' : $ln['title'];
										$ln['iso'] = ($ln['code'] == 'gb') ? 'en' : $ln['code'];
										?>
										<li><a href="/<?= $ln['code'] ?><?= $url ?>" rel="alternate" class="language-option" hreflang="<?= $ln['iso'] ?>"><span><?= __($ln['title']) ?></span></a></li>
									<?php endforeach; ?>

								</ul>
							</div>
						<?php endif; ?>
					</div>

					<div class="searchbar-wrapper" id="product_searchbar_wrapper">
						<label for="product-searchbar"><?= __('Enter keyword') ?></label>
						<input type="text" class="product-searchbar product_searchbar" id="product-searchbar" placeholder="<?= __('Enter book name or author') ?>"><!--
						 --><button type="button" class="product-searchbutton" id="product-searchbutton"><span><?= __('Search') ?></span>&nbsp;</button>
					</div>

				</div>
			</header>
			<nav class="main-nav">
				<?= menuhelper::add_menu_editable_heading('main', 'main_menu')?>
			</nav>

			<div class="content-wrap">
				<aside class="sidebar">
					<div class="sidebar-menus">
						<?php // loop through top level categories ?>
						<?php foreach ($product_categories as $product_category): ?>
							<?php $subcategories = $product_category->children->where_in_theme()->order_by('order')->order_by('category')->find_all_published(); ?>

							<?php // if the category has children in this theme, display it and its children  ?>
							<?php if (count($subcategories) > 0): ?>
								<div class="sidebar-menu" data-sidebar-category="<?= $product_category->category ?>">
									<h3><?= __($product_category->category) ?></h3>
									<ul>
										<?php foreach ($subcategories as $subcategory): ?>
											<li><a href="/<?= $products_page ?>/<?= $product_category->category?>/<?= $subcategory->category ?>" title="<?= __($subcategory->category) ?>"><?= __($subcategory->category) ?></a></li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>
						<?php endforeach;; ?>
					</div>

					<div class="sidebar-panels">
						<?= Model_Panels::get_panels_feed('content_left'); ?>
					</div>
				</aside>

				<main class="main-content">
					<div class="banner-section"><?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']) ?></div>
					<div class="content"><?= trim($page_data['content']) ?><?php
						if ($page_data['layout'] == 'newslisting')
							echo trim(Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']));
						?></div>
