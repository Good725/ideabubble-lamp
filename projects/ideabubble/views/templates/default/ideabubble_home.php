<? include 'html_document_header.php'; ?>

<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?>">

    <?php
    include('cookie_policy-snippet.php');

    ?>
    <?= settings::get_google_analitycs_script(); ?>
    <div id="wrapper">
        <div id="page">
            <div id="container">
                <div id="header">
                    <div id="slider4">
                        <div id="header-container22">
                            <div id='background-header'>
                                <ul>
                                    <li><img src='media/photos/banners/banner1.jpg'/></li>
                                    <li><img src='media/photos/banners/banner2.jpg'/></li>
                                    <li><img src='media/photos/banners/banner3.jpg'/></li>
                                    <li><img src='media/photos/banners/banner4.jpg'/></li>
                                </ul>
                                <div id='header-shadow'></div>
                            </div>
                            <div id="header1">

                                <div id="logo">
                                    <a href="<?= URL::site() ?>"><img src="<?= URL::site() ?>assets/default/images/logo-ideabubble.png"/></a>
                                </div>

                                <div id="main-menu">
                                    <?php menuhelper::add_menu('main_menu', '', '<li class="phone"><span>(061)</span> 513 030</li>') ?>
                                </div>

                                <div id="header-slider-page">

                                    <div class="slider-inner">
                                        <?= Model_Pages::get_raw_page('homebanner1.html') ?>

                                    </div>

                                    <div class="slider-inner">
                                        <?= Model_Pages::get_raw_page('homebanner2.html') ?>

                                    </div>

                                    <div class="slider-inner">
                                        <?= Model_Pages::get_raw_page('homebanner3.html') ?>

                                    </div>
                                </div>
                                <div id="btn_panel">
                                    <a class="bx-prev" href="">prev</a>
                                    <a class="bx-next" href="">next</a>
                                </div>

                                <div id="header-slider-tabs">
                                    <?= Model_Panels::get_panels_feed('default'); ?>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div id="main">
                <div id="sideLt">
                    <?= Model_Testimonials::get_plugin_items_front_end_feed(); ?>
                    <div id="clients">
                        <h1>Clients</h1>
                        <?php sliderhelper::bxslider('media/photos/slider0/', 'slider0', "mode: 'fade', auto: true, controls: false") ?>
                    </div>
                </div>
                <div id="ct">
                    <div id="latest-work-slider">
                        <h1>Latest Work</h1>
                        <?php sliderhelper::bxslider('media/photos/slider2/', 'slider2', "mode: 'fade', auto: true, controls: true"); ?>

                        <div><?= $page_data['footer'] ?></div>
                    </div>
                </div>

                <?php include('html_document_footer.php'); ?>
            </div>
        </div>
    </div>
    <?= Settings::instance()->get('footer_html'); ?>
</body></html>
