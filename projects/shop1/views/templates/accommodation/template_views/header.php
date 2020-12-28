<!DOCTYPE html>
<html lang="en">
	<head>
		<?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
		<?php if (Settings::instance()->get('search_engine_indexing') === 'FALSE'): ?>
			<meta name="robots" content="noindex, nofollow"/>
		<?php endif; ?>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="description" content="<?= $page_data['seo_description']; ?>"/>
		<meta name="keywords" content="<?= $page_data['seo_keywords']; ?>"/>
		<meta name="author" content="//ideabubble.ie">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
		<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
		<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>
		<title><?= $page_data['title']; ?></title>
		<link rel="shortcut icon" href="/assets/<?= $assets_folder_path ?>/images/favicon.ico" type="image/ico"/>
		<link rel="canonical" href="<?= str_replace('.html.html', '.html', URL::base().$page_data['name_tag'].'.html') ?>" />

		<?php // CSS ?>
		<?php if (class_exists('Model_Media')): ?>
			<link rel="stylesheet" type="text/css" href="/frontend/media/fonts"/>
		<?php endif; ?>
		<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
			<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('gallery'); ?>css/lytebox.css"/>
		<?php endif; ?>
		<link href="<?= URL::overload_asset('css/jquery.datetimepicker.css') ?>" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="<?= URL::site() ?>assets/shared/css/daterangepicker/daterangepicker.min.css" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/photoswipe.min.css" rel="stylesheet" type="text/css" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/validation.css" rel="stylesheet" type="text/css" />
		<link href="<?= URL::site() ?>assets/shared/css/browserorg/style.css" rel="stylesheet" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css" rel="stylesheet" type="text/css" />

		<?php // JS ?>
		<?= settings::get_google_analitycs_script(); ?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base() ?>js/libs/jquery-1.7.2.min.js"><\/script>')</script>
		<!--[if lt IE 9]>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/respond.src.js"></script>
		<script src="//ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
		<![endif]-->

		<script src="<?= URL::site() ?>assets/shared/js/daterangepicker/jquery.datetimepicker.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/photoswipe.min.js"></script>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/checkout.js"></script>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('payments') ?>js/front_end/payments.js"></script>
		<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
			<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('gallery');?>js/lytebox.js"></script>
		<?php endif; ?>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/forms.js"></script>

		<script type="text/javascript">
			var test_mode = <?= (Settings::instance()->get('browser_sniffer_testmode') == 1) ? 'true' : 'false' ?>;
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

        <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <script src="/assets/<?= $assets_folder_path ?>/js/overlappingmarkerspiderfier.min.js"></script>
        <script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
		<?= Settings::instance()->get('head_html'); ?>

	</head>
	<body class="layout-<?= $page_data['layout']?> <?= (trim($page_data['banner_photo']) != '') ? ' has-banner' : '' ?>">
		<div class="wrapper">

            <?php $alerts = Session::instance()->get('messages'); ?>
            <?php if ( ! is_null($alerts)): ?>
                <div class="alerts">
                    <div class="content-wrapper">
                        <?php foreach( $alerts as $alert): ?>
                            <div class="alert alert-<?= $alert['type'] ?>">
                                <button type="button" class="alert-close" data-dismiss="alert">&times;</button>
                                <strong><?= ucfirst($alert['type']) ?>:</strong> <?= $alert['content'] ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php Session::instance()->delete('messages') ?>
            <?php endif; ?>

			<header class="main-header<?= (trim($page_data['banner_photo']) != '') ? ' main-header-with-banner' : '' ?>">

				<div class="content-wrapper header-top">
					<div class="col-xsmall-2 col-medium-0 header-expand-wrapper">
						<button type="button" class="nav-toggle" id="nav-toggle">
							<span></span>
						</button>
					</div>

					<div class="col-xsmall-3 col-medium-3 header-logo-wrapper">
						<a href="/<?= $page_data['theme_home_page']; ?>">
							<img src="/assets/<?= $assets_folder_path ?>/images/logo.png" class="logo-image" />
						</a>
					</div>

					<div class="col-xsmall-7 col-medium-6 header-slogan-wrapper">
						<?= Settings::instance()->get('company_slogan') ?>
					</div>

					<?php
					$facebook_url    = trim(Settings::instance()->get('facebook_url'));
					$twitter_url     = trim(Settings::instance()->get('twitter_url'));
					$youtube_url     = trim(Settings::instance()->get('youtube_url'));

					// If the user specified a full URL use that. If they only supplied the username, create a URL using it.
					$facebook_url    = (strpos($facebook_url, 'facebook.com') > -1 OR $facebook_url == '') ? $facebook_url : 'https://www.facebook.com/'.$facebook_url;
					$twitter_url     = (strpos($twitter_url,  'twitter.com')  > -1 OR $twitter_url  == '') ? $twitter_url  : 'https://www.twitter.com/'.$twitter_url;
					$youtube_url     = (strpos($youtube_url,  'youtube.com')  > -1 OR $youtube_url  == '') ? $youtube_url  : 'https://www.youtube.com/user/'.$youtube_url;
					?>

					<div class="col-xsmall-12 col-medium-3 compact-cols">
						<div class="col-xsmall-12 col-small-6 col-medium-12 header-translation-wrapper">
							<?php if ($facebook_url): ?>
								<a href="<?= $facebook_url ?>" class="header-social-icon">
									<img src="/assets/<?= $assets_folder_path ?>/images/social/facebook-icon-square.png" />
								</a>
							<?php endif; ?>
							<?php if ($twitter_url): ?>
								<a href="<?= $twitter_url ?>" class="header-social-icon">
									<img class="header-social-icon" src="/assets/<?= $assets_folder_path ?>/images/social/twitter-icon-square.png" />
								</a>
							<?php endif; ?>
							<div class="google-translate-element" id="google_translate_element"></div>
						</div>
						<form class="col-xsmall-12 col-small-6 col-medium-12 header-search-wrapper" action="/search-results.html" method="get">
							<label class="input-with-addon header-search-wrapper">
								<span class="sr-only"><?= __('Quick Property Search') ?></span>
								<input type="text" class="header-search" placeholder="<?= __('Quick Property Search') ?>" name="keywords" />
								<button type="submit" class="input-addon header-search-button" title="<?= __('Click to search') ?>">
									<span class="sr-only"><?= __('Click to search') ?></span>
								</button>
							</label>
						</form>
						<script type="text/javascript">
							function googleTranslateElementInit() {
								new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
							}
						</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
					</div>
				</div>

				<div class="menu-wrapper-outer" id="menu-wrapper-outer">
					<nav class="menu-wrapper">
						<div class="content-wrapper">
							<?= menuhelper::add_menu_editable_heading('main', 'main-menu')?>
						</div>
					</nav>
				</div>

			</header>

			<?php if (trim($page_data['banner_photo']) != ''): ?>
				<form action="<?= URL::base() ?>search-results.html#search-criteria">
					<div class="banner-section-wrapper">
						<div class="banner-section">
							<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo'], FALSE) ?>

							<?php if (in_array($page_data['layout'], array('home', 'searchresults'))): ?>
								<div class="home-search-wrapper" id="search-criteria">
									<div class="content-wrapper compact-cols home-search home-search-desktop">
										<div class="search-row clearfix">
											<div class="col-medium-<?= ($page_data['layout'] == 'searchresults') ? 3 : 5 ?> col-small-12 col-xsmall-12">
												<label class="sr-only" for="home-search-destination"><?= __('Where do you want to go?') ?></label>
												<input type="text" class="input-styled search-destination" id="home-search-destination" placeholder="<?= __('Where do you want to go?') ?>" name="keywords" value="<?= (isset($_GET['keywords'])? $_GET['keywords'] : '') ?>"/>
											</div>
											<div class="col-xsmall-12 col-medium-4 compact-cols daterangepicker" id="search-date-range">
												<div class="col-medium-6 col-small-12 col-xsmall-12">
													<label class="sr-only" for="home-search-check_in"><?= __('Check in') ?></label>
													<input type="text" class="input-styled daterangepicker-start" id="home-search-check_in" placeholder="<?= __('Check in') ?>" name="check_in" value="<?= (isset($_GET['check_in'])? $_GET['check_in'] : '') ?>"/>
												</div>
												<div class="col-medium-6 col-small-12 col-xsmall-12">
													<label class="sr-only" for="home-search-check_out"><?= __('Check out') ?></label>
													<input type="text" class="input-styled daterangepicker-end" id="home-search-check_out" placeholder="<?= __('Check out') ?>" name="check_out" value="<?= (isset($_GET['check_out'])? $_GET['check_out'] : '') ?>"/>
												</div>
											</div>
											<div class="col-medium-2 col-small-12 col-xsmall-12">
												<label class="sr-only" for="home-search-number_of_guests"><?= __('Number of guests') ?></label>
												<div class="select">
													<select class="input-styled search-number_of_guests" id="home-search-number_of_guests" name="guests">
														<?php $guests = isset($_GET['guests']) ? $_GET['guests'] : 1 ?>
														<?php for ($i = 1; $i <= 10; $i++): ?>
															<option value="<?= $i ?>"<?= ($guests == $i) ? ' selected="selected"' : ''?>>
																<?= $i ?> <?= ($i == 1) ? 'Guest' : 'Guests' ?>
															</option>
														<?php endfor; ?>
													</select>
												</div>
											</div>
											<div class="col-medium-1 col-small-12 col-xsmall-12">
												<button class="home-search-button" type="submit"><span><?= __('Search') ?></span></button>
											</div>
											<?php if ($page_data['layout'] == 'searchresults'): ?>
												<div class="col-medium-2 col-small-12 col-xsmall-12">
													<button type="button" class="button-link more-filters-button" id="more-filters-button"><?= __('More Filters') ?></button>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="more-filter-row">
						<div class="content-wrapper">
							<div class="col-xsmall-10 more-filters" id="more-filters">
								<?php if (isset($building_types) and count($building_types) > 0): ?>
									<div class="form-group filter-row">
										<label class="col-xsmall-3"><?= __('Property Type') ?></label>
										<div class="col-xsmall-9 space-between-cols">
											<?php $selected_building_types = (isset($_GET['building_types']) AND is_array($_GET['building_types'])) ? $_GET['building_types'] : array(); ?>
											<?php foreach ($building_types as $building_type): ?>
												<div class="col-xsmall-6 col-small-4">
													<label>
														<input
															type="checkbox"
															name="building_types[]"
															value="<?= $building_type->id ?>"
															<?= in_array($building_type->id, $selected_building_types) ? ' checked="checked"' : '' ?>
														/>
														<?= $building_type->name ?>
													</label>
												</div>
											<?php endforeach; ?>

										</div>
									</div>
								<?php endif; ?>

								<div class="apply-section">
									<button class="button-primary apply-filter-button" id="apply-filter-button" type="submit"><span>Apply Filters</span></button>
									<button class="cancel-filter-button" id="cancel-filter-button" type="button"><span>Cancel</span></button>
								</div>

							</div>
							<script type="text/javascript">
								$(document).ready(function(){
									$("#more-filters-button").click(function(){
										$("#more-filters").slideToggle("fast", "linear", function () {});
									});
									$("#cancel-filter-button").click(function(){
										$("#more-filters").slideUp();
										$(".filter-item").prop('checked', false);
										$("#more-filters-size").prop('selectedIndex', 0);
									});
								});
							</script>
						</div>
					</div>
				</form>
			<?php endif; ?>

			<?php if ($page_data['layout'] == 'propertydetails'): ?>
				<?php $property_photos = $property_data->get_photos(); ?>
				<div class="property-banner-wrapper">
					<figure class="property-banner">
						<?php if (isset($property_photos[0])): ?>
							<img class="property-banner-image" src="<?= $property_photos[0]->filepath ?>" alt="" />
						<?php endif; ?>
					</figure>
				</div>
			<?php endif; ?>

			<?php
			// Autofill out formbuilder field, depending on URL query
			if (isset($_GET['contact_type']) AND isset($_GET['property_id']))
			{
				$property = ORM::factory('Propman')->where('id', '=', $_GET['property_id'])->find_published();

				if ($_GET['contact_type'] == 'callback' AND trim($_GET['property_id']))
				{
					$preload_message = 'Property #'.$property->id.': '.$property->name."\n".__('I am interested in this property. Please call me back.');
				}
				elseif ($_GET['contact_type'] == 'email' AND trim($_GET['property_id']))
				{
					$preload_message = 'Property #'.$property->id.': '.$property->name."\n".__('I am interested in this property. Please contact me by email.');
				}

				if (isset($preload_message))
				{
					$page_data['content'] = preg_replace(
						'#<textarea(.*name=\"contact_form_message\".*)>.*</textarea>#',
						'<textarea\1>'.$preload_message.'</textarea>',
						$page_data['content']);
				}
			}
			?>

			<main class="main-content">
				<?php ob_start(); ?>
					<?php if (Settings::instance()->get('show_submenu_in_sidebar') == 1): ?>
						<?php $submenu = Menuhelper::get_submenus_for_page($page_data['id'], 'main'); ?>
						<?php if (count($submenu) > 0): ?>
							<div class="side-menu" id="side-menu">
								<div class="side-menu-inner" id="side-menu-inner">
									<h3 class="side-menu-title"><?= $page_data['title'] ?></h3>
									<ul>
										<?php foreach ($submenu as $menu_item): ?>
											<li class="side-menu-li_1<?= ($page_data['id'] == $menu_item['page_id']) ? ' current' : '' ?>">
												<a href="<?= ($menu_item['page_url'] != '') ? '/'.$menu_item['page_url'] : $menu_item['link_url'] ?>"><?= $menu_item['title'] ?></a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						<?php endif;?>
					<?php endif; ?>
					<?= Model_Panels::get_panels_feed('home_left'); ?>
				<?php
				$sidebar = ob_get_clean();
				$sidebar = preg_replace('/<!--(.*)-->/Uis', '', $sidebar); // remove HTML comments, so we can check if truly empty
				?>

				<?php if (trim($sidebar)): ?>
					<div class="content-wrapper content content-with-sidebar compact-cols">
						<aside class="col-xsmall-12 col-small-4 col-medium-3 sidebar"><?= $sidebar ?></aside>
						<div class="col-xsmall-12 col-small-8 col-medium-9"><?= trim($page_data['content']) ?></div>
					</div>
				<?php else: ?>
					<div class="content-wrapper content"><?= trim($sidebar) ?><?= trim($page_data['content']) ?></div>
				<?php endif; ?>

				<div class="content-wrapper">
