<?php $user = Auth::instance()->get_user(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
        <?php $assets_folder_code_path = PROJECTPATH.'www/assets/'.$assets_folder_path; ?>
        <?= isset($page_data['common_head_data']) ? $page_data['common_head_data'] : ''; ?>
		<link href="<?= URL::overload_asset('css/jquery.datetimepicker.css') ?>" rel="stylesheet" type="text/css" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/jquery-ui.css" rel="stylesheet" type="text/css" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css?ts=<?= filemtime($assets_folder_code_path.'/css/styles.css') ?>" rel="stylesheet" type="text/css" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/footer.css" rel="stylesheet" type="text/css" />
		<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/fonts.css" rel="stylesheet" type="text/css" />
		<script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
		<script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
		<script src="<?= URL::site() ?>engine/shared/js/libs/jquery-ui-1.11.4.min.js"></script>
		<script src="<?= URL::site() ?>assets/shared/js/daterangepicker/jquery.datetimepicker.js"></script>
		<script type="text/javascript" src="/assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
		<script type="text/javascript" src="/assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
		<script type="text/javascript" src="/assets/<?= $assets_folder_path ?>/js/foundation.min.js"></script>
        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/forms.js"></script>
        <script src="/assets/shared/js/swiper.min.js"></script>
        <?= Settings::get_google_analytics_script(); ?>
		<script type="text/javascript" src="/assets/<?= $assets_folder_path ?>/js/general.js?ts=<?= filemtime($assets_folder_code_path.'/js/general.js') ?>"></script>

		<?php if(!empty($event)): ?>
			<?php
				$size = array('', '');
				if(!empty($event['image_media_url']))
                {
                    // Check if the image 404s, before trying to get its size
                    $handle = curl_init($event['image_media_url']);
                    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
                    $response = curl_exec($handle);
                    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                    if ($httpCode !== 404)
                    {
                        $size = getimagesize($event['image_media_url']);
                    }

                    curl_close($handle);
				}
			?>
			<meta property="og:url"                content="<?= URL::base() ?>event/<?= $event['url'] ?>" />
			<meta property="og:type"               content="article" />
			<meta property="og:title"              content="<?= $event['name'] ?>" />
			<meta property="og:description"        content="<?= (trim(strip_tags($event['seo_description']))) ? trim(strip_tags($event['seo_description'])) : trim(strip_tags($event['description'])) ?>" />
			<meta property="og:image"              content="<?= $event['image_media_url'] ?>" />
			<meta property="og:image:width"        content="<?= $size[0] ?>" />
			<meta property="og:image:height"       content="<?= $size[1] ?>" />
		<?php endif; ?>
	</head>
	<body class="layout-<?= isset($page_data['layout']) ? $page_data['layout'] : 'content' ?> <?= ( ! empty($user['id'])) ? ' is_logged_in' : 'is_logged_out' ?>">
		<div class="wrapper">

			<div class="header-container">
				<header class="header">
					<div class="row collapse">
						<div class="logo-image">
							<a href="/" class="logo">
								<img src="/assets/<?= $assets_folder_path ?>/images/logo1.png" alt="<?= __('Home') ?>" />
							</a>
						</div>
						<div class="event-buttons">
							<?php if ( ! empty($user['id'])): ?>
								<a href="/admin/events/edit_event/new" class="button secondary register_event">
							<?php else: ?>
								<a href="/admin/login/register" class="button secondary register_event">
							<?php endif; ?>
									<span class='event-button'>
										<span class="flaticon-plus"></span>
										<span class='event_create'><?= __('Create Event') ?></span>
									</span>
								</a>

							<div class="header-links-wrapper">
								<button type="button" class="header-links-collapse" id="header-links-collapse">
									<span class="flaticon-bars"></span>
								</button>

								<?php
								$mh = new menuhelper;
								$menu_items = $mh->get_all_published_menus('main');
								?>

								<ul class="header-links" id="header-links">
									<?php if ( ! empty($user['id'])): ?>
										<li><a href="/admin"><?= __('My Account') ?></a></li>
									<?php else: ?>
										<li><a href="/admin/login?redirect=/" class="login-menu"><?= __('Log in / Sign up') ?></a></li>
									<?php endif; ?>
									<?php foreach ($menu_items as $menu_item): ?>
										<li><a href="<?= menuhelper::get_link($menu_item) ?>"><?= $menu_item['title'] ?></a></li>
									<?php endforeach; ?>
									<?php if ( ! empty($user['id'])): ?>
										<li><a href="/admin/login/logout" class="login-menu"><?= __('Log out') ?></a></li>
									<?php endif; ?>
								</ul>
							</div>

							<div class="header-search">
								<button type="button" class="button primary header_search-button" id="searchbar-button">
									<span class="show-for-sr"><?= __('Search') ?></span>
									<span class="sprite sprite-search"></span>
								</button>
								<form class="searchbar-wrapper" id="searchbar-wrapper" action="/events/" method="get">
									<label>
										<span class="show-for-sr"><?= __('Enter a keyword to search') ?></span>
										<input type="text" name="term" class="form_field searchbar" autocomplete="off" placeholder="<?= __('Enter a keyword to search') ?>" />
									</label>
								</form>
							</div>

							<?php if ( ! empty($user['id'])): ?>
								<a href="/admin/profile/edit?section=contact" class="header-avatar">
									<img src="<?= URL::get_avatar($user['id']); ?>" alt="profile" width="50" height="50" title="<?= __('Profile: ').$user['name'] ?>" />
								</a>
							<?php endif; ?>

						</div>
					</div>
				</header>
			</div>