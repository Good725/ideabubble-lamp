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
                        <?= Model_Panels::get_panels_feed('content_left');?>
					</div>

					<div class="fourth left">
                        <?php include 'template_views/form_view_newsletter_signup.php'; ?>
                    </div>

                    <div class="fifth left">
                        <?php include 'template_views/social_media_view.php'; ?>
					</div>
				</div>

				<div class="ct left">
					<div id="banner" class="left">
						<?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
					</div>
					<div class="clear left"></div>
					<div class="content ct_left left">
						<div class="left successful message_area" style="display:none;"></div>
						<?php
						echo $page_data['content'];

						/* Render OTHER Views based on the Page-Tag and Requested Information */
						// Render Checkout
						if ($page_data['name_tag'] == 'checkout.html' AND (Settings::instance()->get('product_enquiry') != 1))
                        {
                            echo Model_Product::render_checkout_html();
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