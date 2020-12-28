<?php include 'template_views/html_document_header.php'; ?>
    <body class="template-default">
    <?php include 'template_views/header.php'; ?>
    <div id="main">
        <?php include 'template_views/menu.php'; ?>
        <?php include 'template_views/search_bar_snippet.php'; ?>
        <div class="page_left">
            <div class="banner">
                <?php echo (strlen(Model_PageBanner::render_frontend_banners($page_data['banner_photo'])) === 0) ? "<div style='padding-top:18px;'></div>" : Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
            </div>

            <?= $page_data['content'] ?>
            <? if ($page_data['name_tag'] == 'contact-us.html') {
                include 'template_views/contact_us_snippet.php';
            } ?>
            <?php if ($page_data['name_tag'] == 'testimonials.html') {
                include 'template_views/testimonials_view_snippet.php';
            } ?>
            <?php
            if($page_data['name_tag'] == 'gallery.html')
            {
                echo Model_Gallery::get_category_images('main');
            }
            if($page_data['name_tag'] == 'pay-online.html')
            {
                require_once 'template_views/pay_online.php';
            }
            ?>
        </div>
        <?php include 'template_views/banner_calendar_snippet.php'; ?>
    </div>
    <?php include 'template_views/footer.php'; ?>
    <?= Settings::instance()->get('footer_html'); ?>
<?php include 'template_views/html_document_footer.php'; ?>