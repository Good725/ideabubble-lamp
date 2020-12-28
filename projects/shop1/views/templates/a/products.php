<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php
	$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;

	if (Settings::instance()->get('search_engine_indexing') === 'FALSE')
	{
		echo "<meta name='robots' content='NOINDEX, NOFOLLOW' />";
	}
	?>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="description" content="<?= $page_data['seo_description']; ?>">
	<meta name="keywords" content="<?= $page_data['seo_keywords']; ?>">
	<meta name="author" content="//ideabubble.ie">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
	<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>
	<title><?= $page_data['title'];?></title>
	<link rel="shortcut icon" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/favicon.ico"
		  type="image/ico"/><?php if (class_exists('Model_Media')): ?>
		<link rel="stylesheet" type="text/css" href="/frontend/media/fonts">
	<?php endif; ?>
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/normalize.css" rel="stylesheet" type="text/css" />
	<link href="<?= URL::overload_asset('css/jquery.datetimepicker.css') ?>" rel="stylesheet" type="text/css" />
	<link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/structure.css" rel="stylesheet" type="text/css" />
	<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
		<link href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/searchbar.css" rel="stylesheet" type="text/css"/>
	<?php endif; ?>
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
	<![endif]-->
	<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/searchbar.js"></script>
	<?php endif; ?>
	<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
	<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
    <?php include APPPATH.'views/background_switcher.php' ?>
	<?= Settings::instance()->get('head_html'); ?>
</head>

<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?>">
<div id="container">
	<?php include 'header.php' ?>
	<div id="main">
		<?php $parsed_url = explode('/', urldecode(trim($_SERVER['SCRIPT_URL'], '/'))); ?>
		<div id="sideLt">
			<div class="panels_lt">
				<?php $products_menu_enabled = (Settings::instance()->get('products_menu') === FALSE OR Settings::instance()->get('products_menu') == 1); ?>
				<?php if ($products_menu_enabled OR Kohana::$config->load('config')->get('db_id') == 'lionprint'): ?>
					<div class="specials_offers">
						<?php if (Kohana::$config->load('config')->get('db_id') != 'lionprint'): ?>
							<h1>Products</h1>
						<?php else: ?>
							<h1>Quick Links</h1>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="products_menu">
                    <?php if ( ! $products_menu_enabled): ?>
						<div>
							<?= menuhelper::add_menu_editable_heading('left', 'ul_level_1'); ?>
						</div>
					<?php else: ?>
                        <?= Model_Product::render_products_menu(); ?>
					<?php endif; ?>
				</div>
				<?=Model_Panels::get_panels_feed('content_left');?>
			</div>
		</div>
		<div id="ct">
			<div id="banner">
				<?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
			</div>
			<div id="checkout_messages"></div>
			<div class="content">
				<?=$page_data['content']?>
				<?php
				/* Some Plugin Specific Content CWill be called Here */
				//Load News - Data for the News Page
				if ($page_data['name_tag'] == 'news.html') echo '<h1>News</h1>', Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
				?>
			</div>
			<?php if ($page_data['name_tag'] == 'products.html')
			{
				echo '<div class="products">'.Model_Product::render_products_list_html().'</div>';
			}?>
			<?php if ($page_data['name_tag'] == 'checkout.html') echo Model_Product::render_checkout_html().'<style type="text/css">#sideRt{ display: none;}</style>'; ?>
			<?php if ($page_data['name_tag'] == 'contact-us.html') Model_Formprocessor::contactus(); ?>

			<?php if ($page_data['name_tag'] == 'loyalty-registration-form.html') echo request::factory('/frontend/shop1/render_registration_html')->execute(); ?>
			<?php if ($page_data['name_tag'] == 'members-area.html') echo request::factory('/frontend/shop1/render_members_area_html')->execute(); ?>
			<?php if ($page_data['name_tag'] == 'login.html') echo request::factory('/frontend/shop1/login_html')->execute(); ?>
		</div>
		<div class="clear"></div>
	</div>
	<div id="footer">
		<?php include 'footer.php' ?>
	</div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>
