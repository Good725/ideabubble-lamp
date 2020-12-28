<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" prefix="og: http://ogp.me/ns#">
<head>
	<?php
	$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;

	if (Settings::instance()->get('search_engine_indexing') === 'FALSE')
	{
		echo "<meta name='robots' content='NOINDEX, NOFOLLOW' />";
	}
	?>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="description" content="<?php echo $page_data['seo_description']; ?>">
	<meta name="keywords" content="<?php echo $page_data['seo_keywords']; ?>">
	<meta name="author" content="//ideabubble.ie">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
	<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>

	<?php if ( ! empty($page_data['product_data'])): ?>
		<meta property="og:title" content="<?= $page_data['product_data']['title'] ?>" />
		<meta property="og:type" content="product" />
		<?php if (empty($page_data['product_data']['brief_description'])): ?>
			<meta property="og:description" content="<?= strip_tags($page_data['product_data']['description']) ?>" />
		<?php else: ?>
			<meta property="og:description" content="<?= strip_tags($page_data['product_data']['brief_description']) ?>" />
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( ! empty($page_data['product_data']['images'][1])): ?>
		<?php $base_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'products'); ?>
		<meta property="og:image" content="<?= $base_path.'/'.$page_data['product_data']['images'][1] ?>" />
		<meta property="og:image:width" content="500" />
		<meta property="og:image:height" content="500" />
		<meta property="og:image:type" content="image/png" />
	<?php endif; ?>

	<title><?= $page_data['title'];?></title>
	<link rel="shortcut icon" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/favicon.ico" type="image/ico" />
	<?php if (class_exists('Model_Media')): ?>
		<link rel="stylesheet" type="text/css" href="/frontend/media/fonts">
	<?php endif; ?>
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/normalize.css" rel="stylesheet" type="text/css" />
	<link href="<?= URL::overload_asset('css/jquery.datetimepicker.css') ?>" rel="stylesheet" type="text/css" />
	<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
		<link href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/searchbar.css" rel="stylesheet" type="text/css"/>
	<?php endif; ?>
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/structure.css" rel="stylesheet" type="text/css" />
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css" rel="stylesheet" type="text/css"/>
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/validation.css" rel="stylesheet" type="text/css"/>
	<?=settings::get_google_analitycs_script();?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/jquery-1.7.2.min.js"><\/script>')</script>
	<script src="<?= URL::site() ?>assets/shared/js/daterangepicker/jquery.datetimepicker.js"></script>
	<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/checkout.js"></script>
	<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
	<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/forms.js"></script>
	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/respond.src.js"></script>
	<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
	<![endif]-->
	<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/searchbar.js"></script>
	<?php endif; ?>
	<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
	<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
    <?php include APPPATH.'views/background_switcher.php' ?>
	<?= Settings::instance()->get('head_html'); ?>
</head>


<?php
switch ($page_data['name_tag'])
{
	case 'news.html':
		$extra_content = Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
		break;
	case 'products.html':
		$extra_content = '';
		break;
	case 'checkout.html':
		$extra_content = Model_Product::render_checkout_html();
		break;
	case 'contact-us.html':
		$extra_content = Model_Formprocessor::contactus();
		break;
	case 'loyalty-registration-form.html':
		$extra_content = request::factory('/frontend/shop1/render_registration_html')->execute();
		break;
	case 'members-area.html':
		$extra_content = request::factory('/frontend/shop1/render_members_area_html')->execute();
		break;
	case 'login.html':
		$extra_content = request::factory('/frontend/shop1/login_html')->execute();
		break;
	case 'online-returns.html':
		$extra_content = View::factory('/front_end/online_returns_form');
		break;
	default:
		$extra_content = '';
}
?>

<body id="<?= $page_data['layout'] ?>"
	  class="<?= $page_data['category'] ?> pagename-<?= str_replace('.html', '', $page_data['name_tag']) ?>">
<div id="wrap">
	<div id="container">
		<?php include PROJECTPATH.'/views/templates/'.Kohana::$config->load('config')->template_folder_path.'/header.php'; ?>
		<div id="main">
			<?php if (Settings::instance()->get('main_menu_products') == 1): ?>
				<div id="sideLt">
					<div class="panels_lt">
						<?= Model_Panels::get_panels_feed('home_left'); ?>
					</div>
				</div>
				<div id="ct">
					<div id="banner">
						<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
					</div>

					<div id="home_panels">
						<?=Model_Panels::get_panels_feed('home_content');?>
					</div>

					<div class="content">
						<?= $page_data['content'] ?>
					</div>

					<?php
					if ($page_data['id'] == Settings::instance()->get('products_plugin_page'))
					{
						if (!empty($page_data['current_item_category']) AND !($page_data['current_item_category'] == 'product_details'))
						{
							$extra_content .= '<div class="products">'.Model_Product::render_products_list_html().'</div>';
						}
						if (empty($page_data['current_item_category']))
						{
							$extra_content .= '<div class="products">'.Model_Product::render_products_category_html(FALSE).'</div>';
						}
					}
					?>
					<?= $extra_content ?>
				</div>
			<?php else: ?>

				<div id="sideLt">
					<?php if (Kohana::$config->load('config')->get('db_id') == 'wellsense'): ?>
						<div>
							<?=menuhelper::add_menu_editable_heading('left', 'side_menu');?>
						</div>
					<?php endif;?>
					<div class="panels_lt">
						<?php $show_products = (Settings::instance()->get('products_menu') === FALSE OR Settings::instance()->get('products_menu') == 1) ?>
						<?php if ($show_products): ?>
							<div class="specials_offers"><h1>PRODUCTS</h1></div>
						<?php endif; ?>
						<div class="products_menu">
							<div class="products_menu">
								<?php if ( ! $show_products): ?>
									<div>
										<?= menuhelper::add_menu_editable_heading('left', 'ul_level_1'); ?>
									</div>
								<?php else: ?>
									<?= Model_Product::render_products_menu(); ?>
								<?php endif; ?>
							</div>
						</div>
						<?=Model_Panels::get_panels_feed('content_left');?>
					</div>
				</div>
				<div id="ct">
					<div id="banner">
						<?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
					</div>
					<div id="checkout_messages"></div>
					<div class="clear"></div>
					<div class="content">
						<?= $page_data['content'] ?>
					</div>
					<?php
					if ($page_data['id'] == Settings::instance()->get('products_plugin_page'))
					{
						if (!empty($page_data['current_item_category']) AND !($page_data['current_item_category'] == 'product_details'))
						{
							$extra_content .= '<div class="products">'.Model_Product::render_products_list_html().'</div>';
						}
						if (empty($page_data['current_item_category']))
						{
							$extra_content .= '<div class="products">'.Model_Product::render_products_category_html(FALSE).'</div>';
						}
					}
					?>
					<?= $extra_content ?>

				</div>
			<?php endif; ?>

		</div>
		<div id="footer">
			<?php include PROJECTPATH.'/views/templates/'.Kohana::$config->load('config')->template_folder_path.'/footer.php'; ?>
		</div>
	</div>
</div>

<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>