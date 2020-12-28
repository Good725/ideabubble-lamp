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
                        ?>
                    <div id="contact-form">
                        <h1>Send us an enquiry</h1>

                        <form action="" id="contact_form" method="POST">
                            <table>
                                <tbody>
                                <tr>
                                    <td valign="top"><label>Name:</label></td>
                                    <td><input type="text" value="" name="name" class="textbox"></td>
                                </tr>
                                <tr>
                                    <td valign="top"><label>Phone:</label></td>
                                    <td><input type="text" value="" name="phone" class="textbox"></td>
                                </tr>
                                <tr>
                                    <td valign="top"><label>Email:</label></td>
                                    <td><input type="text" value="" name="email" class="textbox"></td>
                                </tr>
                                <tr>
                                    <td valign="top"><label>Message:</label></td>
                                    <td><textarea cols="40" rows="6" name="message" class="textbox"></textarea></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input id="contact_form_submit_btn" type="submit" value="Send" name="submit"
                                               class="button-send"></td>
                                </tr>
                                </tbody>
                            </table>
                            <input type="hidden" name="email-body" value="">
                            <input type="hidden" name="redirect-to" value="<?= URL::site('contact-thanks') ?>">
                        </form>
                    </div>
                </div>

                <div id="sideRt">
                    <?= Model_Testimonials::get_plugin_items_front_end_feed(); ?>


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