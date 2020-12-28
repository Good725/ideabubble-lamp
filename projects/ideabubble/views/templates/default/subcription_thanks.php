<?php

$to = 'sales@ideabubble.ie';
$subject = 'Subcription';
$headers = 'From: subcription@ideabubble.ie';
if (isset($_POST['subcription_email']) && Kohana_Valid::email($_POST['subcription_email'])) {
    $mail_message = $_POST['subcription_email'];
    if (mail($to, $subject, $mail_message, $headers)) {
        $mail_status = 'Subcription success';
    }
} else {
    $mail_status = 'Error in the subcription';
}

include 'html_document_header.php'; ?>


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
                    <div id="latest-news">
                        <h2>Latest News</h2>
                        <ul id="slider1">
                            <li>
                                <span class="light">New website launched this week</span>
                                <span class="dark">July 19, 2012</span>

                                <p>Quisque et rhoncus elit. Mauris facilisis tortor non justo condimentum
                                    condimentum.</p>

                                <p>Nunc congue nisl nec ligula sollicitudin lacinia. Pellentesque in lectus magna,
                                    dignissim mattis mauris. Nullam egestas... <a class="read-more" href="#">Read More
                                        »</a></p>
                            </li>
                            <li>
                                <span class="light">New website launched this week</span>
                                <span class="dark">July 19, 2012</span>

                                <p>Quisque et rhoncus elit. Mauris facilisis tortor non justo condimentum
                                    condimentum.</p>

                                <p>Nunc congue nisl nec ligula sollicitudin lacinia. Pellentesque in lectus magna,
                                    dignissim mattis mauris. Nullam egestas... <a class="read-more" href="#">Read More
                                        »</a></p>
                            </li>

                        </ul>
                    </div>
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