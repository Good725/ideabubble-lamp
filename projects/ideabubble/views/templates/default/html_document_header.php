<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="<?php echo $page_data['seo_description']; ?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords']; ?>">
    <meta name="author" content="http://ideabubble.ie">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="<?php echo @settings::instance()->get('google_webmaster_code') ?>"/>
    <meta name="msvalidate.01" content="<?php echo @settings::instance()->get('bing_webmaster_code') ?>"/>
    <title><?php echo $page_data['title']; ?></title>
    <link REL="shortcut icon" href="<?= URL::site() ?>assets/default/images/favicon.ico" type="image/ico"/>

	<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
		<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('gallery'); ?>css/lytebox.css"/>
	<?php endif; ?>

    <link href="<?= URL::site() ?>assets/default/css/bx_styles.css" rel="stylesheet" type="text/css"/>
    <link href="<?= URL::site() ?>assets/default/css/jquery.bxslider.css" rel="stylesheet" type="text/css"/>
    <link href="<?= URL::site() ?>assets/default/css/style.css" rel="stylesheet" type="text/css"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic' rel='stylesheet'
          type='text/css'>
    <style type="text/css">
        #background-header {
            position: absolute;
            overflow: hidden;
            height: 600px; /*Set with image height*/
            left: 0px;
        }

        #background-header img {
            position: absolute;
            display: none;
        }

        #background-header .current img {
            position: absolute;
            display: block;
        }

        #background-header ul {
            list-style: none outside none;
            margin-top: 0px;
        }

        #header-shadow {
            -moz-opacity: .80;
            filter: alpha(opacity=80);
            opacity: 0.8;
            background-color: #000000;
            width: 100%;
            height: 300px;
            position: absolute;
            top: 300px;
        }

        .current_div {
            display: block;
        }

        #btn_panel {
            position: relative;
            top: -235px;
        }

    </style>
    <?= settings::get_google_analitycs_script(); ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/default/js/jquery-1.7.2.min.js"><\/script>')</script>

	<?php if (strpos($page_data['content'], 'class="lytebox"')): ?>
		<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('gallery');?>js/lytebox.js"></script>
	<?php endif; ?>

    <script type="text/javascript" src='<?= URL::site() ?>assets/default/js/header_slider.js'></script>
    <script src="<?= URL::site() ?>assets/default/js/general.js">

    </script>
    <?= $page_data['head_html'] ?>
</head>