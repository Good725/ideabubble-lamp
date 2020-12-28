<?php include 'template_views/html_document_header.php'; ?>
<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?>">
    <div id="container">
        <?php include 'header.php' ?>
        <div id="main">
            <?php if (Settings::instance()->get('column_menu') == TRUE AND Settings::instance()->get('column_menu') == 1): ?>
                <div id="sideLt">
                    <div class="panels_lt">
						<?php $products_menu_enabled = (Settings::instance()->get('products_menu') === FALSE OR Settings::instance()->get('products_menu') == 1); ?>
						<?php if ($products_menu_enabled OR Kohana::$config->load('config')->get('db_id') == 'lionprint'): ?>
							<div class="specials_offers">
								<?php if (Kohana::$config->load('config')->get('db_id') != 'lionprint'): ?>
									<h1>Products</h1>
								<?php else: ?>
									<h1>Quick Links</h1>
								<?php endif; ?>
							</div>
						<?php endif; ?>
                        <div class="products_menu">
                            <?php if ( ! $products_menu_enabled): ?>
                                <div>
                                    <?= menuhelper::add_menu_editable_heading('left', 'ul_level_1'); ?>
                                </div>
                            <?php else: ?>
                                <?= Model_Product::render_products_menu(); ?>
                            <?php endif; ?>
                        </div>
                        <?= Model_Panels::get_panels_feed('content_left'); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div id="ct"<? if (Kohana::$config->load('config')->get('db_id') != 'garretts') {
                echo ' class="fullwidth"';
            } ?>>
                <div id="banner">
                    <?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']); //Helper banners ?>
                </div>
                <div id="checkout_messages"></div>
                <div id="home_panels">
                    <?= Model_Panels::get_panels_feed('home_content'); ?>
                </div>
                <div class="clear"></div>
                <div class="content">
                    <?= $page_data['content'] ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div id="footer">
            <?php include 'footer.php' ?>
        </div>
    </div>
    <?= Settings::instance()->get('footer_html'); ?>
</body></html>
