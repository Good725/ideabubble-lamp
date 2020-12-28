<?php include 'template_views/html_document_header.php' ?>
<body id="Page-home" class="home_layout">
	<div id="wrapper">
		<div id="page">
			<div id="container">

				<?php include 'template_views/header.php' ?>

				<!-- banner -->
				<div id="banner"><?= trim(Model_PageBanner::render_frontend_banners($page_data['banner_photo'])) ?></div>
				<!-- /banner -->

				<!-- main area -->
				<div id="main">
					<div id="home_panels" class="home_panels"><?= Model_Panels::get_panels_feed('home_content'); ?></div>
					<div class="ct"><?= trim($page_data['content']); ?></div>
				</div>
				<!-- /main area -->

				<?php include 'template_views/footer.php' ?>
			</div>
		</div>
	</div>
</body>
<?php include 'template_views/html_document_footer.php' ?>
