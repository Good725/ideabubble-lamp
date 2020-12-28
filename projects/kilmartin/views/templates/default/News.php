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
            <?php include 'template_views/news_front_end_list.php'; ?>
        </div>
        <?php include 'template_views/banner_calendar_snippet.php'; ?>
    </div>
    <?php include 'template_views/footer.php'; ?>
    <?= Settings::instance()->get('footer_html'); ?>
<?php include 'template_views/html_document_footer.php'; ?>