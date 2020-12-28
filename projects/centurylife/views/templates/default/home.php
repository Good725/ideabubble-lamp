<?php include 'html_document_header.php'; ?>
    <div id="wrapper">
        <?php
        include 'header.php';
        include 'nav_menu.php';
        ?>
        <div id="content">
            <div id="banner_wrapper" class="left">
                <!-- Model_PageBanner::render_frontend_banners($page_data['banner_photo']) -->
                <img src="<?= URL::site() ?>assets/default/images/banner.png" alt="static banner"/>
            </div>
            <div id="column-242" class="right l-margin-3">
                <div id="request_callback_wrapper">
                    <?php include 'template_views/request_call_back.php'; ?>
                </div>
                <div id="latest_news_wrapper">

                </div>
            </div>
            <div id="panels_wrapper" class="left">
                <?= Model_Panels::get_panels_feed('home_content'); ?>
            </div>
            <div id="user_content" class="left">
                <?= $page_data['content'] ?>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php include 'footer.php'; ?>
    </div>
<?php
include 'html_document_footer.php';
?>