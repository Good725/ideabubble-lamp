<?php
/*$to = 'sales@ideabubble.ie';
$subject = 'Website Enquery';
$headers = 'From: contact@ideabubble.ie';
if(isset($_POST['message'])){
    $mail_message = "";
    $mail_message .= "Name: ". $_POST['name'] . PHP_EOL;
    $mail_message .= "Phone: ". $_POST['phone']. PHP_EOL;
    $mail_message .= "Email: ". $_POST['email']. PHP_EOL;
    $mail_message .= "Message: ". $_POST['message']. PHP_EOL;
    if(mail($to, $subject, $mail_message,$headers)){
        $mail_status = 'Subcription success';
    }
}
else{
    $mail_status = 'Error in the contact';
}*/
if (isset($_GET['status'])) {
    $mail_status = ($_GET['status'] == 1) ? 'Contact was successful' : 'Error in the contact';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="<?php echo $page_data['seo_description']; ?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords']; ?>">
    <meta name="author" content="http://ideabubble.ie">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="<?php echo @settings::instance()->get('google_webmaster_code') ?>"/>
    <meta name="msvalidate.01" content="<?php echo @settings::instance()->get('bing_webmaster_code') ?>"/>
    <title><?php echo $page_data['title']; ?></title>
    <link REL="shortcut icon" href="<?= URL::site() ?>assets/default/images/favicon.ico" type="image/ico"/>
    <link href="<?= URL::site() ?>assets/default/css/bx_styles.css" rel="stylesheet" type="text/css"/>
    <link href="<?= URL::site() ?>assets/default/css/jquery.bxslider.css" rel="stylesheet" type="text/css"/>
    <link href="<?= URL::site() ?>assets/default/css/style.css" rel="stylesheet" type="text/css"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic' rel='stylesheet'
          type='text/css'>
    <?= settings::get_google_analitycs_script(); ?>
    <style type="text/css">
        #header2 {
            margin: 0 auto;
            width: 940px;
            position: relative;
        }

        #header-slider-tabs {
            margin-top: 20px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min"></script>
    <script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/default/js/jquery-1.7.2.min.js"><\/script>')</script>
    <script src="<?= URL::site() ?>assets/default/js/general.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#slider1').bxSlider({
            });
        });
    </script>
</head>

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
                    <div><?= $mail_status ?></div>
                    <?= $page_data['content'] ?>
                </div>

                <div id="sideRt">
                    <?= Model_News::get_plugin_items_front_end_feed('News') ?>

                    <div id="what-we-do">
                        <h2>What We Do...</h2>
                        <?= Model_Panels::get_panels_feed('content_right'); ?>
                    </div>


                    <div id="clients">
                        <h2>Clients</h2>
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