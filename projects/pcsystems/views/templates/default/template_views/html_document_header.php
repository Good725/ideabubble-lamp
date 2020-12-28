<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="<?php echo $page_data['seo_description'];?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords'];?>">
    <meta name="author" content="//ideabubble.ie">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="<?php echo @settings::instance()->get('google_webmaster_code') ?>" />
    <meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>" />
    <title><?php echo $page_data['title'];?></title>
    <link REL="shortcut icon" href="<?=URL::get_skin_urlpath(FALSE)?>images/favicon.ico" type="image/ico"/>
    <link href="<?=URL::get_skin_urlpath(TRUE)?>/css/normalize.css" rel="stylesheet" type="text/css"/>
	<link href="<?=URL::get_skin_urlpath(TRUE)?>/css/validation.css" rel="stylesheet" type="text/css"/>
	<link href="<?=URL::get_skin_urlpath(TRUE)?>/css/jquery.nouislider.min.css" rel="stylesheet" type="text/css"/>
    <?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
        <link href="<?= URL::get_engine_plugin_assets_base('products') ?>css/front_end/searchbar.css" rel="stylesheet" type="text/css"/>
    <?php endif; ?>
    <link href="<?=URL::get_skin_urlpath(TRUE)?>/css/styles.css" rel="stylesheet" type="text/css"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic" rel="stylesheet" type="text/css" />
    <?=settings::get_google_analitycs_script();?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?=URL::get_skin_urlpath(TRUE)?>/js/jquery-1.7.2.min.js"><\/script>')</script>
    <script type="text/javascript" src="<?=URL::get_skin_urlpath(TRUE)?>/js/jquery.validationEngine2.js"></script>
    <script type="text/javascript" src="<?=URL::get_skin_urlpath(TRUE)?>/js/jquery.validationEngine2-en.js"></script>
	<script type="text/javascript" src="<?=URL::get_skin_urlpath(TRUE)?>/js/jquery.nouislider.all.min.js"></script>

	<?php if ($page_data['layout'] == 'products' AND Settings::instance()->get('sharethis_id') != ''): ?>
		<script type="text/javascript">var switchTo5x=true;</script>
		<script type="text/javascript" src="//w.sharethis.com/button/buttons.js"></script>
		<script type="text/javascript">stLight.options({publisher: "<?= Settings::instance()->get('sharethis_id') ?>", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
	<?php endif; ?>

	<?php if ($page_data['layout'] == 'checkout' OR $page_data['layout'] == 'products'): ?>
        <script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('products')?>js/checkout.js"></script>
    <?php endif; ?>
    <?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
        <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/searchbar.js"></script>
    <?php endif; ?>
    <script type="text/javascript" src="<?=URL::get_skin_urlpath(TRUE)?>/js/general.js"></script>
    <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
    <script type="text/javascript" src="<?=URL::get_skin_urlpath(TRUE)?>/js/pcSystemMain.js"></script>
    <?= Settings::instance()->get('head_html'); ?>
</head>