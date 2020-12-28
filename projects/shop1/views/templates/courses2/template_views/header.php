<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
		$apply_now_link = Settings::instance()->get('course_apply_link');
		$apply_now_link = trim($apply_now_link) ? $apply_now_link : '/checkout.html';
		$assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
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
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css" rel="stylesheet" type="text/css" />
		<?= settings::get_google_analitycs_script(); ?>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/jquery-1.7.2.min.js"><\/script>')</script>
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
		<!--[if lt IE 9]>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/respond.src.js"></script>
		<script src="//ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
		<![endif]-->
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
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
	<body class="layout-<?= $page_data['layout']?> <?= (trim($page_data['banner_photo']) != '') ? ' has-banner' : '' ?>">
		<div class="wrapper">

			<div class="header-wrapper">

				<div class="header-top">
					<div class="content-wrapper">
						<div class="col-xsmall-2 col-small-1 col-medium-0 header-expand-wrapper">
							<button type="button" class="nav-toggle" id="nav-toggle">
								<span></span>
							</button>
						</div>
						<div class="col-xsmall-10 col-small-11 col-medium-12 header-login">
							<ul>
								<li><a href="/admin/login"><?= __('Login') ?></a></li>
							</ul>
						</div>

					</div>
				</div>


				<header class="main-header<?= (trim($page_data['banner_photo']) != '') ? ' main-header-with-banner' : '' ?>">

					<div class="content-wrapper">

						<div class="col-xsmall-2 col-small-2 col-medium-1 header-logo-wrapper">
							<a href="/<?= $page_data['theme_home_page']; ?>">
								<img src="/assets/<?= $assets_folder_path ?>/images/logo.png" class="logo-image" />
							</a>
						</div>
						<div class="col-xsmall-10 col-small-6 col-medium-7">
							<div class="header-slogan"><?= Settings::instance()->get('company_slogan') ?></div>
						</div>

						<div class="col-xsmall-12 col-small-4 col-medium-4 header-action-buttons">
							<?php if ($page_data['layout'] == 'course_list'): ?>
								<form class="col-medium-8 input-with-addon header-search-wrapper" method="get" action="/search-results.html">
									<label class="sr-only" for="header-search-input"><?= __('Search Courses') ?></label>
									<input type="text" class="input-styled" id="header-search-input" name="keywords" placeholder="<?= __('Search Courses') ?>" />
									<button type="submit" class="input-addon button-secondary header-search-button"><?= __('Search') ?></button>
								</form>
							<?php else: ?>
								<a href="https://www.vecnet.ie/weblogin/ApacheAuthTicket/?back=http%3a%2f%2fwww.vecnet.ie%2fmuslimerickcity%2fwebmusic%2fwelcome.html" class="button-secondary"><?= __('Pay Online') ?></a>
							<?php endif; ?>
							<a href="<?= $apply_now_link ?>" class="button-primary header-apply-now-button"><?= __('Apply Now') ?></a>

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
					</div>

					<div class="menu-wrapper-outer" id="menu-wrapper-outer">
						<nav class="menu-wrapper">
							<div class="content-wrapper">
								<?= menuhelper::add_menu_editable_heading('main', 'main-menu')?>
							</div>
						</nav>
					</div>

				</header>
			</div><?php // .header-wrapper ?>

			<?php if (trim($page_data['banner_photo']) != ''): ?>
				<form action="<?= URL::base() ?>search-results.html#search-criteria">
					<div class="banner-section-wrapper">
						<div class="content-wrapper banner-section">
							<?php if (in_array($page_data['layout'], array('home', 'searchresults'))):
								$news_model = new Model_News;?>
								<?php $news_items = is_callable(array($news_model, 'get_all_items_front_end')) ? $news_model->get_all_items_front_end() : array(); ?>
								<?php if (count($news_items) > 0): ?>
									<div class="clearfix banner-news-wrapper">
										<strong class="banner-news-label">News and Events:</strong>

										<div class="banner-news-feed-wrapper">
											<ul class="banner-news-feed">
												<?php foreach ($news_items as $news_item): ?>
													<li>
														<div class="banner-news-feed-summary">
															<span><?= $news_item['event_date'] ? date('l jS F', strtotime($news_item['event_date'])).':' : '' ?></span>
															<span><?= $news_item['summary'] ?></span>
														</div>
														<a class="banner-news-feed-link" href="/news.html/<?= $news_item['category'] ?>/<?= $news_item['title'] ?>"><?= __('Read more') ?></a>
													</li>
												<?php endforeach ?>
											</ul>
										</div>

									</div>

								<?php endif; ?>
							<?php endif; ?>

							<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo'], FALSE) ?>

							<?php if (in_array($page_data['layout'], array('home', 'searchresults'))): ?>

								<?php if (isset($news_items) AND count($news_items) > 0): ?>
									<script>
										$('.banner-news-feed').bxSlider({
											mode: 'vertical',
											speed: '500',
											pause: '8000',
											auto: true,
											controls: false,
											orientation: 'vertical'
										});
									</script>
								<?php endif; ?>

								<div class="home-search-wrapper" id="search-criteria">
									<div class="content-wrapper">
										<div class="compact-cols home-search home-search-desktop">
											<div class="compact-cols clearfix search-row">
												<div class="col-xsmall-12 col-small-6 col-medium-7">
													<label class="sr-only" for="home-search-destination"><?= __('Keywords') ?></label>
													<input type="text" class="input-styled search-destination" id="home-search-destination" placeholder="<?= __('Enter search keyword') ?>" name="keywords" value="<?= (isset($_GET['keywords'])? $_GET['keywords'] : '') ?>"/>
												</div>
												<div class="col-xsmall-12 col-small-6 col-medium-3">
													<label class="sr-only" for="home-search-course_type"><?= __('Course Type') ?></label>
													<div class="select">
														<?php $categories = Model_Categories::get_all_published_categories(); ?>
														<select class="input-styled search-course_type" id="home-search-course_type" name="category_ids[]">
															<option value=""><?= __('Select your course type') ?></option>
															<?php foreach ($categories as $category): ?>
																<option value="<?= $category['id'] ?>"><?= $category['category'] ?></option>
															<?php endforeach; ?>
														</select>
													</div>
												</div>
												<div class="col-xsmall-12 col-small-12 col-medium-2">
													<button class="button-primary home-search-button" type="submit"><span><?= __('Find a Course') ?></span></button>
												</div>
											</div>
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
				if ($_GET['contact_type'] == 'callback' AND trim($_GET['property_id']))
				{
					$preload_message = 'Property: '.$_GET['property_id']."\n".__('I am interested in this property. Please call me back.');
				}
				elseif ($_GET['contact_type'] == 'email' AND trim($_GET['property_id']))
				{
					$preload_message = 'Property: '.$_GET['property_id']."\n".__('I am interested in this property. Please contact me by email.');
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


