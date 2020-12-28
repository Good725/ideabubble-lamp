<?php include 'template_views/html_document_header.php'; ?>
<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
<div id="wrap">
    <div id="container">
        <?php include 'header.php' ?>
        <div id="main">
            <div id="sideLt">
                <?php if(Kohana::$config->load('config')->get('db_id') == 'wellsense'): ?>
                    <div>
                        <?=menuhelper::add_menu_editable_heading('left','side_menu');?>
                    </div>
                <?php endif;?>
                <div class="panels_lt">
					<?php $show_products = (Settings::instance()->get('products_menu') === FALSE OR Settings::instance()->get('products_menu') == 1) ?>
					<?php if ($show_products): ?>
	                    <div class="specials_offers"><h1>PRODUCTS</h1></div>
					<?php endif; ?>
                    <div class="products_menu">
                        <?php if ( ! $show_products): ?>
                            <div>
                                <?= menuhelper::add_menu_editable_heading('left','ul_level_1'); ?>
                            </div>
                        <?php else: ?>
                            <?= Model_Product::render_products_menu(); ?>
                        <?php endif; ?>
                    </div>
                    <?=Model_Panels::get_panels_feed('content_left');?>
                </div>
            </div>
            <div id="ct">
                <div id="banner">
                    <?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
                </div>

                <div id="ct_left" class="column">
                    <div id="checkout_messages"></div>
                    <div class="content">
                        <?=$page_data['content']?>
                        <?php
                        /* Some Plugin Specific Content CWill be called Here */
                        //Load News - Data for the News Page
                        if($page_data['name_tag'] == 'news.html') echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
                        ?>
                    </div>
                    <?php if($page_data['name_tag'] == 'contact-us.html') Model_Formprocessor::contactus(); ?>

                    <?php if($page_data['name_tag'] == 'testimonials.html') echo '<div class="content"><h1>Testimonials</h1>' . Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) . '</div>';?>
                </div>
            </div>
        </div>
        <div id="footer">
            <?php include 'footer.php' ?>
        </div>
    </div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>