<?php
$layout          = (isset($layout))          ? $layout          : '';
$sidebar_modules = (isset($sidebar_modules)) ? $sidebar_modules : array('resources', 'demo', 'news', 'customers', 'solutions');

?>
<?php include 'template_views/html_document_header.php'; ?>
<body class="layout-content<?= ($layout != '') ? ' layout-'.$layout : '' ?>">

<div class="wrapper">

	<div class="inner1">
		<div class="logo">
			<a href="/"><img src="<?= $page_data['logo'] ?>" width="224" height="106" alt="logo"></a>
		</div>

		<div class="inner1_header">

			<div class="nav_bg1">
				<div class="menu_icon1"><a href="#"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/menu-img.png" width="30" height="20" alt="menu"></a></div>
				<div class="navigation nav1">
					<?php menuhelper::add_menu_editable_heading('main'); ?>
				</div>
			</div>
			<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo'], FALSE) ?>
		</div>
		<div class="inner1_main">
			<div class="inner1_main_lef">
				<?php $alerts = Session::instance()->get('messages'); ?>
				<div class="message_area">
					<?php if ( ! is_null($alerts)): ?>
						<?php foreach( $alerts as $alert): ?>
							<div class="alert alert-<?= $alert['type'] ?>">
								<a class="close" data-dismiss="alert">&times;</a>
								<strong><?= ucfirst($alert['type']) ?>:</strong> <?= $alert['content'] ?>
							</div>
						<?php endforeach; ?>
						<?php Session::instance()->delete('messages') ?>
					<?php endif; ?>
				</div>

				<?= $page_data['content'] ?>

				<?php if ($page_data['name_tag'] == 'news.html'): ?>
					<div class="content_news_feed">
						<?= Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) ?>
					</div>
				<?php endif; ?>
				<?php if ($page_data['name_tag'] == 'testimonials.html'): ?>
					<div class="content_testimonials_feed">
						<?= Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) ?>
					</div>
				<?php endif; ?>

			</div>
			<?php if (isset($sidebar_modules[0])): ?>
				<div class="inner1_main_rit">
					<?php
					foreach ($sidebar_modules as $sidebar_module)
					{
						$file = 'template_views/module_'.$sidebar_module.'.php';
						include $file;
					}
					?>
				</div>
			<?php endif; ?>
		</div>

		<?php include 'footer.php' ?>
	</div>

</div>
<?php include 'template_views/html_document_footer.php'; ?>