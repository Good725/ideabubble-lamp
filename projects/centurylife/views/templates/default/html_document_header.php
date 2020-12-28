<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_data['title'];?></title>
    <meta name="description" content="<?php echo $page_data['seo_description'];?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords'];?>">
    <meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
    <meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>
    <?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
    <link href="<?=URL::site();?>assets/default/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="<?=URL::site();?>assets/default/css/validation.css" rel="stylesheet" type="text/css"/>
    <script src="//code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/jquery.validationEngine2.js"></script>
    <script type="text/javascript" src="<?= URL::get_engine_assets_base()  ?>assets/default/js/jquery.validationEngine2-en.js"></script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-48157038-1', 'centurylife.ie');
        ga('send', 'pageview');
    </script>
    <?= Settings::instance()->get('head_html'); ?>
</head>
<body>