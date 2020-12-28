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
					<div id="banner">
						<?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
					</div>

					<?php include 'template_views/products_featured_view.php'; ?>
					<div class="left successful message_area" style="display:none;"></div>
					<div class="clear left"></div>
					<div class="content left"><?= $page_data['content'] ?></div>
				</div>
				<div class="sideLt left">
					<div class="first left">
						<?php include 'template_views/menu_left_view.php'; ?>
						<?php include 'template_views/products_menu_view.php'; ?>
					</div>

					<div class="second left">
						<a href="<?=URL::site()?>special-offers.html">
							<img src="<?=URL::get_skin_urlpath(TRUE)?>images/special-offer.jpg" alt="Special Offer" title="Special Offer" />
						</a>
					</div>

					<div class="third left">
						<?= Model_Panels::get_panels_feed('home_left');?>
					</div>

					<div class="fourth left">
						<?php include 'template_views/form_view_newsletter_signup.php'; ?>
					</div>

					<div class="fifth left">
						<?php include 'template_views/social_media_view.php'; ?>
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