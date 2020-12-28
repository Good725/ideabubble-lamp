<? include 'html_document_header.php'; ?>


<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?>">
<?php include('analyticstracking.php'); ?>
<script type="text/javascript">
    $(function () {
        $('#slider1').bxSlider({
        });
    });
</script>
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
                    <?php
                    switch ($page_data['name_tag'])
                    {
                        case 'registration-successful.html':
                            echo Model_Customers::render_registration_successful();
                            break;
                        case 'customer-registration.html':
                            echo Model_Customers::render_customer_registration_form();
                            break;
                        case 'customer-login.html':
                            echo Model_Customers::render_customer_login_form();
                            break;
                        case 'customer-payment.html':
                            echo Model_Customers::render_customer_payment_form();
                            break;
                        case 'reset-password.html':
                            echo Model_Customers::render_password_reset_form();
                            break;
                        case 'new-password.html':
                            echo Model_Customers::render_new_password_form();
                            break;
                        case 'reset-password-sent.html':
                            echo '<p>A link to set your new password has been sent to your email.</p>';
                            break;
                        case 'select-licence':
                            echo Model_Customers::render_licenses_table();
                            break;
                    }

                    ?>
                    <?=
                    //display any message if present
                    IbHelpers::get_messages();

                    //load includes to memory for inclusion
                    //todo write a general content output function to encapsulate all the widget loading process
                    ob_start();
                    include('pay_online_form.php');
                    $widgets = array(ob_get_clean());

                    //initialise marker(s) that will need replacing
                    $markers = array("&lt;[payonlineform]&gt;");

                    //display rendered output with widget(s) embedded if marker(s) found in content
                    echo(str_replace($markers, $widgets, $page_data['content']));

                   // echo Model_Product::render_checkout_html();
                    ?>
                    <script type="text/javascript">var shared_assets = "<?=URL::get_engine_assets_base() ?>";   </script>

                    <script type="text/javascript" src="<?=URL::get_project_assets_base()?>assets/default/js/jquery-ui-1.10.3.min.js"></script>
                    <script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('products')?>js/checkout.js"></script>
                    <script type="text/javascript" src="<?=URL::get_project_assets_base()?>assets/default/js/checkout.js"></script>

                </div>
                <div id="sideRt">
                    <div id="our-services">
                        <h1>Our Services</h1>
                        <?= Model_Panels::get_panels_feed('content_left'); ?>
                    </div>


                    <div id="clients">
                        <h1>Clients</h1>
                        <?php sliderhelper::bxslider('media/photos/slider0/', 'slider0', "mode: 'fade', auto: true, controls: false") ?>

                    </div>
                    <h1>Pay Online</h1>

                    <p></p>


                </div>
                <?php include('html_document_footer.php'); ?>
            </div>
        </div>
    </div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>