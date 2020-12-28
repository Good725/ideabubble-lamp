<? require_once 'template_views/header_html_document.php'; ?>
<?php $content_location = (Settings::instance()->get('content_location') == 'right') ? 'right' : 'left'; ?>

    <body id="bdy-<?= $page_data['layout'] ?>" class="bdy-<?= $page_data['category'] ?>">
        <div id="wrapper">
            <?
            require_once 'template_views/header.php';
            require_once 'template_views/menu_nav.php';
            ?>
            <div id="content">
                <div id="content_area" class="<?=(Settings::instance()->get('content_location') == 'right') ? 'right' : 'left'?>">
                    <div id="banner_wrapper" class="<?= $content_location ?>">
                        <?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
                    </div>
                    <div id="content_panels_wrapper" class="panels_wrapper left">
                        <?= Model_Panels::get_panels_feed('content_content'); ?>
                    </div>
                    <div id="user_content" class="<?= $content_location ?>">
                        <?php
                        echo $page_data['content'];
                        switch ($page_data['name_tag']) {
                            case 'request-a-callback.html':
                                require_once 'template_views/form_request_a_callback.php';
                                break;
                            case 'news.html':
                                require_once 'template_views/news_view.php';
                                break;
                            case 'testimonials.html':
                                echo Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
                                break;
                            case 'contact-us.html':
                                if(Kohana::$config->load('config')->get('db_id') == 'lsomusic')
                                {
                                    echo View::factory('template_views/lso_contact_us');
                                }
                                else
                                {
                                    Model_Formprocessor::contactus();
                                }
                                break;
                            case 'gallery.html':
                                echo Model_Gallery::get_category_images("default");
                                break;
                            case 'ailesbury-online-consultation.html':
                                Model_Formprocessor::consultation_form();
                                break;
                            case 'courses.html':
                                if (@isset($page_data['current_item_category']) AND @!isset($page_data['current_item_identifier'])) : ?>
                                    <?= Model_Courses::get_front_list_by_category($page_data['current_item_category']); ?>
                                <?php else : ?>
                                    <? //=Model_Categories::get_front_categories(); ?>
                                    <? require_once 'template_views/category_list_main.php'; ?>
                                <?php endif;
                        }
                        ?>
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