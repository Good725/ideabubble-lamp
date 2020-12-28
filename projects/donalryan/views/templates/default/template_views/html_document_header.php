<!DOCTYPE html>
<html>
<head>
        <?php $assets_folder_path = 'default' ?>
    <?php if (Settings::instance()->get('search_engine_indexing') === 'FALSE'): ?>
        <meta name="robots" content="NOINDEX, NOFOLLOW" />
    <?php endif; ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="<?php echo $page_data['seo_description'];?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords'];?>">
    <meta name="author" content="//ideabubble.ie">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="google-site-verification" content="<?php echo @settings::instance()->get('google_webmaster_code') ?>" />
    <meta name="msvalidate.01" content="<?php echo @settings::instance()->get('bing_webmaster_code') ?>" />
    <title><?php echo $page_data['title'];?></title>
    <link rel="shortcut icon" href="/assets/<?= $assets_folder_path ?>/images/favicon.ico" type="image/ico"/>
    <?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
        <link href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/searchbar.css" rel="stylesheet" type="text/css"/>
    <?php endif; ?>
    <link href="<?=URL::site()?>assets/<?= $assets_folder_path ?>/css/styles.css" rel="stylesheet" type="text/css"/>
    <link href='//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic' rel='stylesheet' type='text/css'>
    <?= settings::get_google_analitycs_script(); ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/jquery-1.7.2.min.js"><\/script>')</script>
    <script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('products')?>js/checkout.js"></script>
    <link href="<?=URL::site()?>assets/<?= $assets_folder_path ?>/css/validation.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>/js/jquery.validationEngine2.js"></script>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>/js/jquery.validationEngine2-en.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/respond.src.js"></script>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->
    <script type="text/javascript" src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
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
        <script src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/cookieconsent/cookieconsent.min.js"></script>
    <?php endif; ?>
    <?= Settings::instance()->get('head_html'); ?>
</head>
