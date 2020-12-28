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
						<div class="first">
							<?php include 'template_views/menu_left_view.php'; ?>
							<div class="products_menu">
								<h3>Categories</h3>
								<?= Model_Product::render_products_menu(); ?>
							</div>
							<?php
							// include 'template_views/menu_left_view.php';
							// include 'template_views/products_menu_view.php';
							?>
							<?=Model_Panels::get_panels_feed('content_left');?>
						</div>

						<div class="second">
							<?= Model_Panels::get_panels_feed('home_left'); ?>
						</div>
					</div>
				</div>
				<div id="ct" class="ct">
					<div id="banner" class="main-banner">
						<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
					</div>
					<?php include 'template_views/products_featured_view.php'; ?>
					<div class="left successful message_area" style="display:none;"></div>
					<div class="content">
						<?= $page_data['content'] ?>
						<div>
							<?= Model_Product::render_products_list_html(8, TRUE, TRUE); // move to controller ?>
						</div>
					</div>
                    <div class="footer_logos_wrapper">
                        <?= Settings::instance()->get('footer_logos_text') ?>
                        <?= menuhelper::add_menu_editable_heading('company_logos', 'footer_logos') ?>
                    </div>
				</div>

			</div>
			<!-- /Main Content -->
			<?php include 'template_views/footer_view.php';?>
        </div>
		<!-- /Page -->
    </div>
	<!-- /Wrapper/ -->
    <?= Settings::instance()->get('footer_html'); ?>
</body>
<?php include 'template_views/html_document_footer.php'; ?>