<? require_once 'template_views/header_html_document.php'; ?>
    <body id="bdy-<?= $page_data['layout'] ?>" class="bdy-<?= $page_data['category'] ?>">
        <div id="wrapper">
            <?
            require_once 'template_views/header.php';
            require_once 'template_views/menu_nav.php';
            ?>
            <div id="content">
                <div id="content_area" class="content_area <?=(Settings::instance()->get('content_location') == 'right') ? 'right' : 'left'?>">
                    <div id="banner_wrapper" class="left">
                        <?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
                    </div>
                    <div id="content_panels_wrapper" class="panels_wrapper left">
                        <?= Model_Panels::get_panels_feed('home_content'); ?>
                    </div>
                    <div id="user_content" class="user_content right">
                        <?= $page_data['content'] ?>
                    </div>
                </div>
                <? require_once 'template_views/column_1.php'; ?>
                <div style="clear:both;"></div>
            </div>
            <? require_once 'template_views/footer.php'; ?>
        </div>
        <?= Settings::instance()->get('footer_html'); ?>
    </body>
<? require_once 'template_views/footer_html_document.php'; ?>