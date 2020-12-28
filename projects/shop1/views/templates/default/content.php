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
						<?=Model_Panels::get_panels_feed('content_left');?>
					</div>

					<div class="third left">
						<?php include 'template_views/form_view_newsletter_signup.php'; ?>
					</div>

					<div class="fourth left">
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
						//Display Page Content
						echo $page_data['content'];

						/*
						 * @TODO: Add corresponding Form views, based on the Pagetag: $page_data['name_tag']
						 * @TODO: Add RIGHT Column with News Feeds etc. for the Content Pages
						 * @TODO: Wire UP: Products and Checkout Pages
						 */
                        if($page_data['name_tag'] == 'news.html'){
                            require_once 'template_views/news_view.php';}
                        if($page_data['name_tag'] == 'special-offers.html'){
                            require_once 'template_views/special_offers_view.php';}
                        if($page_data['name_tag'] == 'testimonials.html') echo Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
                        if($page_data['name_tag'] == 'contact-us.html') Model_Formprocessor::contactus();
                        ?>
					</div>
					<?php if (Settings::instance()->get('sidebar_news_feed') == 1 ): ?>
						<div class="ct_right left">
							<?= Model_News::get_plugin_items_front_end_feed('News'); ?>
						</div>
					<?php endif; ?>
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