<? include 'html_document_header.php'; ?>

<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?>">
<?php include('analyticstracking.php'); ?>
<div id="wrapper">
    <div id="page">
        <div id="container">
            <div id="header">
                <div id="header2">
                    <div id="logo">
                        <a href="<?= URL::site() ?>"><img
                                src="<?= URL::site() ?>assets/default/images/logo-ideabubble.png"/></a>
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


                <div id="ct">

                    <?= $page_data['content'] ?>
                </div>

                <div id="sideRt">
                    <div id="our-services">
                        <h1>Our Services</h1>
                        <?= Model_Panels::get_panels_feed('content_left'); ?>
                    </div>
                    <?= Model_News::get_plugin_items_front_end_feed('News') ?>

                    <div id="what-we-do">
                        <h1>Partner with you</h1>
                        <?= Model_Panels::get_panels_feed('content_right'); ?>
                    </div>

                    <div id="clients">
                        <h1>Clients</h1>
                        <?php sliderhelper::bxslider('media/photos/slider0/', 'slider0', "mode: 'fade', auto: true, controls: false") ?>

                    </div>

                </div>
                <?php include('html_document_footer.php'); ?>
            </div>
        </div>
    </div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>