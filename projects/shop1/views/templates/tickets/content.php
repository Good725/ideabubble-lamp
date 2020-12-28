<?php include 'template_views/header.php' ?>
    <div class="row">
       <div class="content-main-wrapper">
            <div id="content-banner" class="pricing-banner" >
                <?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
            </div>

            <div class="content_wrapper">
                <?= $page_data['content']; ?>
                <?php
                if (str_replace('.html', '', $page_data['name_tag']) == 'news') {
                    require_once 'template_views/news_view.php';
                }
                if (str_replace('.html', '', $page_data['name_tag']) == 'timeout') {
                    require_once 'template_views/timeout.php';
                }
                ?>
            </div>
        </div>
     </div>

    <?php include 'template_views/success.php'; ?>
<?php include 'template_views/footer.php' ?>

