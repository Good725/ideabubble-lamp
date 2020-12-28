<?php 
$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
$settings = Settings::instance()->get();
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Needs ======================== -->
    <meta charset="utf-8">
    <?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="<?php echo $page_data['seo_description']; ?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords']; ?>">
    <meta name="author" content="http://ideabubble.ie">
    
    <!-- Mobile Specific Metas ===================== -->        
    <meta content="width=device-width, initial-scale=1, user-scalable=no" name="viewport">
    <meta name="google-site-verification" content="<?php echo @settings::instance()->get('google_webmaster_code') ?>"/>
    <meta name="msvalidate.01" content="<?php echo @settings::instance()->get('bing_webmaster_code') ?>"/>
    <title><?php echo $page_data['title']; ?></title>

    <!-- Favicon ======================== -->
    <link REL="shortcut icon" href="<?= URL::site() ?>assets/ideabubble/images/favicon.ico" type="image/ico"/>
    
    <!-- css ======================== -->
    <link href="<?= URL::overload_asset('css/flaticon.css') ?>" rel="stylesheet" type="text/css"/>
    <link href="<?= URL::site() ?>assets/ideabubble/css/elegant-font-min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= URL::site() ?>assets/ideabubble/css/swiper.min.css" rel="stylesheet" type="text/css"/>   
    <link href="/assets/<?= $assets_folder_path ?>/css/style.css" rel="stylesheet" type="text/css"/> 

    <!-- JS ======================== -->
    <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->   
    <?= settings::get_google_analitycs_script(); ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="<?= URL::site() ?>assets/ideabubble/js/swiper.min.js"></script>
    <script src="/assets/default/js/typed.js"></script>
    <script src="/assets/<?= $assets_folder_path ?>/js/general.js"></script>
    <?php if (Settings::instance()->get('cookie_enabled') === 'TRUE'): ?>
        <!-- Cookie consent plugin by Silktide - http://silktide.com/cookieconsent -->
        <script type="text/javascript">
            <?php
            $cookie_message      = Settings::instance()->get('cookie_text');
            $cookie_dismiss_text = Settings::instance()->get('hide_notice_message');
            $cookie_link         = Settings::instance()->get('cookie_page');
            $cookie_link_text    = Settings::instance()->get('link_text');
            $cookie_message      = $cookie_message      ? $cookie_message      : 'This website uses cookies to ensure you get the best experience on our website';
            $cookie_dismiss_text = $cookie_dismiss_text ? $cookie_dismiss_text : 'Got it!';
            $cookie_link_text    = $cookie_link_text    ? $cookie_link_text    : 'More info';
            $cookie_consent_options = array(
                "message" => $cookie_message,
                "dismiss" => $cookie_dismiss_text,
                "learnMore" => $cookie_link_text,
                "link" => $cookie_link ? Model_Pages::get_page_by_id($cookie_link) : null,
                "theme" => "dark-bottom"
            );
            ?>
            window.cookieconsent_options = <?=json_encode($cookie_consent_options)?>; // use proper js encoding to handle special characters ' " \ / etc...
        </script>
        <script src="<?= URL::site() ?>assets/ideabubble/js/cookieconsent/cookieconsent.min.js"></script>
    <?php endif; ?>

    <?= $page_data['head_html'] ?>
</head>
<body class="layout-<?= $page_data['layout'] ?><?= ( !empty($page_data['banner_slides'])) ? ' has_banner' : '' ?>">
    <main class="page-wrapper">
        <header class="header">
            <div class="fix-container">
                <a href="javascript:void(0)" class="pull"><span aria-hidden="true" class="icon_menu"></span></a>
                <figure class="logo">
                    <a href="<?= URL::site() ?>"><img src="/assets/<?= $assets_folder_path ?>/images/logo.png"/></a>
                </figure>
                <?php
                $settings_instance = Settings::instance();
                $telephone = trim($settings_instance->get('telephone'));
                $email     = trim($settings_instance->get('email'));
                $show_talk_button = ($settings_instance->get('show_need_help_button') == 1);
                ?>
                <?php if ($telephone or $show_talk_button): ?>
                    <div class="connect-to">
                        <ul>
                            <?php if ($telephone): ?>
                                <li>
                                    <a href="tel:<?= $telephone ?>">
                                        <span aria-hidden="true" class="icon_phone"></span>
                                        <span class="mob-hidden"><?= $telephone ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($show_talk_button): ?>
                                <?php $talk_link = $settings_instance->get('need_help_page'); ?>
                                <li>
                                    <a href="<?= Model_Pages::get_page_by_id($talk_link) ?>" class="btn-primary inverse small">
                                        <span class="icon_chat_alt" aria-hidden="true"></span>
                                        <span class="mob-hidden">let&#39;s talk</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <nav class="navigation">
                    <?= menuhelper::add_menu_editable_heading('main');?>
                </nav>

            </div>
        </header><!-- header end -->

        <div class="quick_contact hidden--tablet hidden--desktop">
            <ul class="list-unstyled">
                <?php if ($telephone): ?>
                    <li class="quick_contact-item">
                        <a href="tel:<?= str_replace(' ', '', $settings['telephone']) ?>">
                            <span class="sr-only"><?= __('Phone') ?></span>
                            <span class="icon_phone"></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($email): ?>
                    <li class="quick_contact-item">
                        <a href="mailto:<?= $email ?>">
                            <span class="sr-only"><?= __('Email') ?></span>
                            <span class="icon_mail"></span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="quick_contact-item">
                    <a href="/contactus.html">
                        <span class="sr-only"><?= __('Location') ?></span>
                        <span class="icon_pin"></span>
                    </a>
                </li>
            </ul>
        </div>