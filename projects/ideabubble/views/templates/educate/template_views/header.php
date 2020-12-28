<!DOCTYPE html>
<html>
<head>
	<?php $theme_folder_path = URL::site().'assets/'.Kohana::$config->load('config')->assets_folder_path; ?>
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
    <link REL="shortcut icon" href="<?= $theme_folder_path ?>/images/favicon.ico" type="image/ico"/>
    
    <!-- css ======================== -->
    <link href="<?= $theme_folder_path ?>/css/elegant-font-min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= $theme_folder_path ?>/css/style.css" rel="stylesheet" type="text/css"/>
    
    <!-- JS ======================== -->
    <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->   
    <?= settings::get_google_analitycs_script(); ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="<?= $theme_folder_path ?>/js/swiper.min.js"></script>
    <script type="text/javascript" src="<?= $theme_folder_path ?>/js/general.js"></script>

    <?= $page_data['head_html'] ?>
</head>

<body class="layout-<?= $page_data['layout'] ?><?= ( !empty($page_data['banner_slides'])) ? ' has_banner' : '' ?>">
<main class="page-wrapper">
    <header class="header">
        <div class="fix-container">
            <a href="javascript:void(0)" class="pull"><span aria-hidden="true" class="icon_menu"></span></a>
            <figure class="logo">
                <a href="<?= URL::site() ?>"><img src="<?= $theme_folder_path ?>/images/logo.png"/></a>
            </figure>
            <?php
            $settings_instance = Settings::instance();
            $telephone = $settings_instance->get('telephone');
            $show_talk_button = ($settings_instance->get('show_need_help_button') == 1);
            ?>
            <?php if ($telephone or $show_talk_button): ?>
                <div class="connect-to">
                    <ul>
                        <?php if ($telephone): ?>
                            <li>
                                <a href="tel:<?= $telephone ?>"><i aria-hidden="true" class="icon_phone"></i><span class="mob-hidden"><?= $telephone ?></span></a>
                            </li>
                        <?php endif; ?>

                        <?php if ($show_talk_button): ?>
                            <?php $talk_link = $settings_instance->get('need_help_page'); ?>
                            <li>
                                <a href="<?= Model_Pages::get_page_by_id($talk_link) ?>" class="btn-primary inverse small">
                                    <i class="icon_chat_alt" aria-hidden="true"></i>
                                    <span class="mob-hidden" id="lets-talk">let&#39;s talk</span>
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
