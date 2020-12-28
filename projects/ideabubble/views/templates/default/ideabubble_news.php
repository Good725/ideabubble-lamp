<? include 'html_document_header.php'; ?>


<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?>">
    <?php include('analyticstracking.php'); ?>
    <div id="wrapper">
        <div id="page">
            <div id="container">
                <div id="header">
                    <div id="header2">

                        <div id="logo">
                            <a href="<?= URL::site() ?>"><img src="<?= URL::site() ?>assets/default/images/logo-ideabubble.png"/></a>
                        </div>

                        <div id="main-menu">
                            <?php menuhelper::add_menu('main_menu', '', '<li class="phone"><span>(061)</span> 513 030</li>') ?>
                        </div>
                        <!--BANNER START-->
                        <div id="header-slider">
                            <?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']); //Helper banners ?>
                        </div>
                        <!--BANNER END-->
                        <div id="header-slider-tabs">
                            <?= Model_Panels::get_panels_feed('default'); ?>
                        </div>
                    </div>
                </div>
                <div id="main">
                    <div id="sideLt">

                        <?= Model_Testimonials::get_plugin_items_front_end_feed(); ?>

                        <div id="our-services">
                            <h1>Our Services</h1>
                            <?= Model_Panels::get_panels_feed('content_left'); ?>
                        </div>

                        <div id="clients">
                            <h1>Clients</h1>
                            <?php sliderhelper::bxslider('media/photos/slider0/', 'slider0', "mode: 'fade', auto: true, controls: false") ?>
                        </div>

                    </div>

                    <div id="ct">
                        <div id="page_content">
                            <?= $page_data['content'] ?>
                            <?php
                            /* Some Plugin Specific Content CWill be called Here */
                            //Load News - Data for the News Page
                            if ($page_data['name_tag'] == 'news.html') echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);


                            if ($page_data['name_tag'] == 'blog.html') { //get blogger articles and output to screen
                                $SearchURL = "http://blog.ideabubble.ie/feeds/posts/default?alt=rss";
                                $feed = utf8_encode(file_get_contents($SearchURL));
                                $xml = new SimpleXmlElement($feed);
                                $i = 0;
                                foreach ($xml->channel->item as $entry) {
                                    $i++;
                                    echo $entry->title;
                                    echo $entry->description;
                                    if ($i == 5) break; //show only 5
                                }
                            }

                            ?>
                        </div>
                    </div>
                    <?php include('html_document_footer.php'); ?>
                </div>
            </div>
        </div>
    </div>
    <?= Settings::instance()->get('footer_html'); ?>
</body></html>