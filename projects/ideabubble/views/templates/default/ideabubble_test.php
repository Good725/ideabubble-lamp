<? include 'html_document_header.php'; ?>

<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?>">
<?php include('analyticstracking.php'); ?>
<div id="wrapper">
    <div id="page">
        <div id="container">
            <div id="header">
                <div id="header-container">

                    <div id="header1">

                        <div id="logo">
                            <a href="<?= URL::site() ?>"><img
                                    src="<?= URL::site() ?>assets/default/images/logo-ideabubble.png"/></a>
                        </div>

                        <div id="main-menu">
                            <?php echo menuhelper::add_menu('main_menu', '', '<li class="phone"><span>(061)</span> 513 030</li>') //->add_last_item('<li class="phone"><span>(061)</span> 513 030</li>')?>
                            <!--<ul>
                                <li><a href="">HOME</a></li><li><a href="">ABOUT US</a></li><li><a href="">SERVICES</a></li><li><a href="">CLIENTS</a></li><li><a href="">NEWS</a></li><li class="last"><a href="">CONTACT</a></li><li class="phone"><span>(061)</span> 513 030</li>
                            </ul>-->
                        </div>

                        <div id="header-slider">
                            <img src="<?= URL::site() ?>assets/default/images/slide1.png"/>
                            <a class="what-we-do" href=""></a>
                        </div>

                        <div id="header-slider-tabs">
                            <?= Model_Panels::get_panels_feed('default'); ?>
                        </div>

                    </div>

                </div>
            </div>
            <div id="main">
                <div id="sideLt">

                    <div id="testimonials">
                        <h2>Testimonials</h2>
                        <img src="<?= URL::site() ?>assets/default/images/testimonials-logo.jpg"/>
                        <span class="light">Pat Lavin</span>
                        <span class="dark">Chartered Accountant (FCA)</span>

                        <p>Ullam aliquet semper nisl ac faucibus. Quisque vitae sapien id massa egestas consectetur. Sed
                            sit amet augue eu nunc hendrerit varius. Quisque et rhoncus elit. Mauris facilisis tortor
                            non justo condimentum condimentum.</p>

                        <p>Nunc congue nisl nec ligula sollicitudin lacinia. Pellentesque in lectus magna, dignissim
                            mattis mauris. Nullam egestas, nisl ut semper varius, magna lacus pretium lorem eget
                            pharetra.</p>
                        <span class="lunch">Lunch Website: <a href="" target="_blank">www.odcl.ie</a></>
                    </div>
                </div>


                <div id="ct">

                    <div id="latest-work-slider">
                        <h2>Latest Work</h2>
                        <ul id="slider2">
                            <li>
                                <img src="<?= URL::site() ?>assets/default/images/slide1.jpg"/>
                            </li>
                            <li>
                                <img src="<?= URL::site() ?>assets/default/images/slide1.jpg"/>
                            </li>
                            <li>
                                <img src="<?= URL::site() ?>assets/default/images/slide1.jpg"/>
                            </li>
                            <li>
                                <img src="<?= URL::site() ?>assets/default/images/slide1.jpg"/>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php include('html_document_footer.php'); ?>
            </div>
            <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/jquery-1.7.2.min.js"></script>
            <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/jquery.bxSlider.js"></script>
            <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/general.js"></script>
        </div>
    </div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>