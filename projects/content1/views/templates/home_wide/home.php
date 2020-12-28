<?php include 'template_views/html_document_header.php'; ?>
<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
<div id="wrap">
    <div id="container">
        <?php include 'header.php' ?>
        <div id="main">
			<?php if (Settings::instance()->get('main_menu_products') == 1): ?>
				<div id="sideLt">
					<div class="panels_lt">
						<?= Model_Panels::get_panels_feed('home_left'); ?>
					</div>
				</div>
				<div id="ct">
					<div id="banner">
						<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
					</div>

					<div id="home_panels">
						<?=Model_Panels::get_panels_feed('home_content');?>
					</div>

					<div class="content"><?=$page_data['content']?></div>
				</div>
			<?php else: ?>
				<div id="ct">
					<div id="banner">
						<?= Model_Panels::get_panels_feed('home_left'); ?>
						<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
						<?= Model_Panels::get_panels_feed('home_right'); ?>
					</div>
					<div id="checkout_messages"></div>
					<div id="home_panels">
						<?=Model_Panels::get_panels_feed('home_content');?>
					</div>

					<?php if (Settings::instance()->get('home_page_products_feed') == 'TRUE'): ?>
						<div id="sideLt">
							<div class="panels_lt">
								<?= Model_Panels::get_panels_feed('content_left'); ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="content"><?=$page_data['content']?></div>
				</div>
			<?php endif; ?>
        </div>
        <div id="footer">
            <?php include 'footer.php' ?>
        </div>
    </div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>