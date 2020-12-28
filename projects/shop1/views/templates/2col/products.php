<?php include 'template_views/html_document_header.php'; ?>
<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
    <!-- Wrapper -->
	<div id="wrapper">
		<!-- Page -->
        <div id="page" class="left">
			<?php include 'template_views/header_view.php'; ?>
			<?php include 'template_views/menu_main_view.php'; ?>

			<!-- Main Content -->
			<div id="main" class="left">

				<div class="ct left">
					<div id="banner" class="left">
						<?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
					</div>
					<div class="clear left"></div>
					<div class="content ct_left left">
						<?php
						//Display Page Content
						echo $page_data['content'];

						/* Render OTHER Views based on the Page-Tag and Requested Information */
						// Render Products
						if($page_data['name_tag'] == 'products.html'){
							// Render Products Categories
							if (!isset($page_data['current_item_category']) OR (isset($page_data['current_item_category']) AND trim($page_data['current_item_category']) == ''))
							{
								echo '<div class="products">'. Model_Product::render_products_category_html() . '</div>';
							}
							// Render Product Category
							if (isset($page_data['current_item_category']) AND $page_data['current_item_category'] != 'product_details')
							{
								echo '<div class="products">' . Model_Product::render_products_list_html() . '</div>';
							}
						}
						?>
					</div>
				</div>

				<div class="sideLt left">
					<div class="first left">
						<?php
						include 'template_views/menu_left_view.php';
						include 'template_views/products_menu_view.php';
						if (Settings::instance()->get('sidebar_news_feed') == 1 )
						{
							echo Model_News::get_plugin_items_front_end_feed('News');
						}
						?>
					</div>

					<div class="second left">
						<?= Model_Panels::get_panels_feed('content_left');?>
					</div>
					<div class="third left">
						<?php include 'template_views/social_media_view.php'; ?>
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