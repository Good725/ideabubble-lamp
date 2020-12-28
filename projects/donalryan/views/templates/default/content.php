<?php include 'template_views/html_document_header.php'; ?>
    <body class="content_layout">
    <div id="container" class="container">
        <?php include 'header.php' ?>

        <div id="content" class="content_area">
            <div id="banner" class="banner_area">
                <?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>

                <div id="home_panels" class="panel_area content_panel_area">
                    <?= Model_Panels::get_panels_feed('content_center'); ?>
                </div>
            </div>

            <div class="content">
                <?=$page_data['content']?>
                <?php
                /* Some Plugin Specific Content CWill be called Here */
                //Load News - Data for the News Page
                if($page_data['name_tag'] == 'news.html') echo '' ,Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
                ?>
            </div>

        </div><!-- /content -->

        <?php include 'footer.php' ?>

    </div>
    <?= Settings::instance()->get('footer_html'); ?>
    </body>
<?php include 'template_views/html_document_footer.php'; ?>