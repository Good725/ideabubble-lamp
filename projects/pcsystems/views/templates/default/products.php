<?php include 'template_views/html_document_header.php'; ?>
<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
    <!-- Wrapper -->
	<div id="wrapper" class="wrapper">
		<!-- Page -->
        <div id="page" class="page">
			<?php include 'template_views/header_view.php'; ?>

			<!-- Main Content -->
			<div id="main" class="main">
				<div id="sidebar" class="sidebar">
					<a href="#" id="sidebar-expand" class="sidebar-expand">
						<span class="sidebar-expand-icon">☰</span>
						<span class="sidebar-expand-text">Hide Options</span>
					</a>
					<div id="sidebar-inner" class="sidebar-inner">
						<div class="first left">
							<?php include 'template_views/menu_left_view.php'; ?>
							<?php include 'template_views/products_menu_view.php'; ?>
						</div>

						<div class="second left">
							<?= Model_Panels::get_panels_feed('content_left'); ?>
						</div>
					</div>
				</div>

				<div id="ct" class="ct">
					<div id="banner" class="left">
						<?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
					</div>
					<div class="clear left"></div>
					<div class="content">
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
								echo '<div class="products">' . Model_Product::render_products_list_html(12) . '</div>';
							}
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