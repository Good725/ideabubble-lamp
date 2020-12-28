<?php include 'template_views/html_document_header.php'; ?>
    <body class="content_layout">
    <div id="container" class="container">
        <?php include 'header.php' ?>

        <div id="content" class="content_area">
            <div id="banner" class="banner_area">
                <?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); //Helper banners ?>

                <div id="home_panels" class="panel_area content_panel_area">
                    <?= Model_Panels::get_panels_feed('content_center'); ?>
                </div>
            </div>

            <div class="content">
                <?= $page_data['content'] ?>
            </div>
            <?php if ($page_data['name_tag'] == 'products.html' AND !empty($page_data['current_item_category']) AND !($page_data['current_item_category'] == 'product_details')) {
                echo '<div class="products">' . Model_Product::render_products_list_html() . '</div>';
            }?>
            <?php if ($page_data['name_tag'] == 'products.html' AND empty($page_data['current_item_category'])) {
                //Categories
                echo '<div class="products">' . Model_Product::render_products_category_html(FALSE) . '</div>';
            }
            ?>
            <?php if ($page_data['name_tag'] == 'checkout.html') echo Model_Product::render_checkout_html(); ?>
            <?php if ($page_data['name_tag'] == 'contact-us.html') Model_Formprocessor::contactus(); ?>

            <?php if ($page_data['name_tag'] == 'loyalty-registration-form.html') echo request::factory('/frontend/shop1/render_registration_html')->execute(); ?>
            <?php if ($page_data['name_tag'] == 'members-area.html') echo request::factory('/frontend/shop1/render_members_area_html')->execute(); ?>
            <?php if ($page_data['name_tag'] == 'login.html') echo request::factory('/frontend/shop1/login_html')->execute(); ?>

        </div>
        <!-- /content -->

        <?php include 'footer.php' ?>

    </div>
    <?= Settings::instance()->get('footer_html'); ?>
    </body>
<?php include 'template_views/html_document_footer.php'; ?>