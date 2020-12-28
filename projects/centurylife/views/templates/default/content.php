<?php include 'html_document_header.php'; ?>
    <div id="wrapper">
        <?php
        include 'header.php';
        include 'nav_menu.php';
        ?>
        <div id="content">
            <div id="banner_wrapper" class="left">
                <?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
            </div>
            <div id="column-242" class="right l-margin-3">
                <div id="request_callback_wrapper">
                    <?php include 'template_views/request_call_back.php'; ?>
                </div>
                <div id="latest_news_wrapper">
                    <div id="latest_news_header">
                        LATEST NEWS
                    </div>
                    <div id="latest_news_content">
                        <?= Model_News::get_plugin_items_front_end_feed('News') ?>
                    </div>
                    <script type="text/javascript"
                            src="//static.polskiquote.websitecms.dev/engine/plugins/pages/sliders/bxslider/jquery.bxslider.js"></script>
                    <script type="text/javascript">
                        jQuery(function () {
                            var auto_slide;
                            //Display news slider if there is more than one news
                            jQuery(function () {
                                if ($('#slider_home_news li').size() > 1) {
                                    auto_slide = true;
                                }
                                else {
                                    auto_slide = false;
                                }
                                $('#slider1').bxSlider({mode: 'fade', auto: true, controls: false});
                                $(".bx-has-pager").remove();
                            });
                        });
                    </script>
                </div>
            </div>
            <div id="panels_wrapper" class="left">
                <?= Model_Panels::get_panels_feed('home_content'); ?>
            </div>
            <div id="user_content" class="left">
                <?= $page_data['content'] ?>
                <?php if ($page_data['name_tag'] == 'news.html') {
                    echo '<div id="news_section">';
                    echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
                    echo '</div>';
                }
                if ($page_data['name_tag'] == 'contact-us.html') {
                    require_once 'template_views/contact_us.php';
                }
                ?>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php include 'footer.php'; ?>
    </div>
<?php
include 'html_document_footer.php';
?>