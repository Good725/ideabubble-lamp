<?php $sidebar_modules = (isset($sidebar_modules)) ? $sidebar_modules : array('products', 'panels', 'news', 'customers'); ?>
<?php include 'template_views/html_document_header.php'; ?>
<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
    <!-- Wrapper -->
	<div id="wrapper" class="wrapper">
		<!-- Page -->
        <div id="page" class="page">
			<?php include 'template_views/header_view.php'; ?>
			<?php include 'template_views/menu_main_view.php'; ?>

			<!-- Main Content -->
			<div id="main" class="main">
				<?php if (isset($sidebar_modules[0])): ?>
					<div id="sidebar" class="sidebar <?= (isset($sidebar_location) AND $sidebar_location == 'right') ? 'sideRt' : 'sideLt' ?>">
						<a href="#" id="sidebar-expand" class="sidebar-expand">
							<span class="sidebar-expand-icon">☰</span>
							<span class="sidebar-expand-text">Hide Options</span>
						</a>
						<div id="sidebar-inner" class="sidebar-inner">
							<?php foreach ($sidebar_modules as $sidebar_module): ?>
								<div class="sidebar-module sidebar-module-<?= $sidebar_module ?>">
									<?php include 'template_views/module_'.$sidebar_module.'.php'; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<div id="ct" class="ct">
					<div id="banner" class="left">
						<?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
					</div>
					<div class="clear left"></div>
					<div class="content">
						<?php $alerts = Session::instance()->get('messages'); ?>
						<div class="successful message_area">
							<?php if ( ! is_null($alerts)): ?>
								<?php foreach ($alerts as $alert): ?>
									<div class="alert alert-<?= $alert['type'] ?>">
										<a class="close" data-dismiss="alert">&times;</a>
										<strong><?= ucfirst($alert['type']) ?>:</strong> <?= $alert['content'] ?>
									</div>
								<?php endforeach; ?>
								<?php Session::instance()->delete('messages') ?>
							<?php endif; ?>
						</div>

						<?php
						//Display Page Content
						echo $page_data['content'];

						/*
						 * @TODO: Add corresponding Form views, based on the Pagetag: $page_data['name_tag']
						 * @TODO: Add RIGHT Column with News Feeds etc. for the Content Pages
						 * @TODO: Wire UP: Products and Checkout Pages
						 */
                        switch ($page_data['name_tag'])
						{
							case 'news.html':
                            	require_once 'template_views/news_view.php';
								break;

							case 'special-offers.html':
                            	require_once 'template_views/special_offers_view.php';
								break;

                        	case 'testimonials.html':
								echo Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
								break;

							case 'checkout.html':
								echo Model_Product::render_checkout_html();
								break;
						}
						?>
					</div>
				</div>
			</div>
			<!-- /Main Content -->
			<?php include 'template_views/footer_view.php';?>
        </div>
		<!-- /Page -->
    </div>
	<!-- /Wrapper -->
    <?= Settings::instance()->get('footer_html'); ?>
</body>
<?php include 'template_views/html_document_footer.php'; ?>