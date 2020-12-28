<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="<?php echo $page_data['seo_description'];?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords'];?>">
    <meta name="author" content="http://ideabubble.ie">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="<?php echo @settings::instance()->get('google_webmaster_code') ?>" />
    <meta name="msvalidate.01" content="<?php echo @settings::instance()->get('bing_webmaster_code') ?>" />
    <title><?php echo $page_data['title'];?></title>
    <link REL="shortcut icon" href="<?=URL::site()?>assets/default/images/favicon.ico" type="image/ico"/>
    <link href="<?=URL::site()?>assets/default/css/bx_styles.css" rel="stylesheet" type="text/css"/>
    <link href="<?=URL::site()?>assets/default/css/jquery.bxslider.css" rel="stylesheet" type="text/css"/>
    <link href="<?=URL::site()?>assets/default/css/styles.css" rel="stylesheet" type="text/css"/>
    <link href="<?=URL::site()?>assets/default/css/validation.css" rel="stylesheet" type="text/css"/>
    <link href="<?=URL::site()?>assets/default/css/checkout.css" rel="stylesheet" type="text/css"/>
    <link href='//fonts.googleapis.com/css?family=Droid+Sans:400,700|Open+Sans:300,700,300italic|Open+Sans+Condensed:300,700,300italic' rel='stylesheet' type='text/css'>
    <?=settings::get_google_analitycs_script();?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/default/js/jquery-1.7.2.min.js"><\/script>')</script>
    <script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('products')?>js/checkout.js"></script>

    <script type="text/javascript" src="<?=URL::site()?>assets/default/js/checkout.js"></script>
	<link href="<?=URL::site()?>assets/default/css/validation.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="<?=URL::site()?>assets/default/js/jquery.validationEngine2.js"></script>
	<script type="text/javascript" src="<?=URL::site()?>assets/default/js/jquery.validationEngine2-en.js"></script>
	<script type="text/javascript" src="<?=URL::site()?>assets/default/js/general.js"></script>
    <?= Settings::instance()->get('head_html'); ?>
</head>

<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
<div id="container">
    <div id="header">
        <div id="header">
			<?php $number_of_items = Model_Checkout::get_cart_value('number_of_items'); ?>
			<div id="checkout_cart"<?= (Settings::instance()->get('cart_hidden_when_empty') == 1) ? ' class="minicart-hidden-when-empty"' : ''; ?> data-product_count="<?= $number_of_items ?>">
                <div class="minicart_icon"></div>
                <div class="mycart">
                    <span class="left">My Items: (<span id="mycart_items_amount"><?=Model_Checkout::get_cart_value('number_of_items')?></span>) </span>
                    <span class="right">Total: &euro;<span id="mycart_total_price"><?=Model_Checkout::get_cart_total_price_value()?></span></span>
                    <div class="cart_buttons cart_hidden" <?php if( (int)Model_Checkout::get_cart_value('number_of_items') < 1 ) echo 'style="display:none"'?>>
                        <div class="button2 left">
                            <span>
                                <span>
                                    <a href="<?=url::site()?>checkout.html">View Cart »</a>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="main_menu">
                <?php menuhelper::add_menu_editable_heading('main')?>
            </div>
        </div>
    <div id="main">
        <div id="banner">
            <!--<img src="assets/default/images/banner-1.jpg" border="0"/>-->
            <?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
        </div>
        <div id="sideLt">
            <div id="content-nav-top-padding"></div>
            <div class="products_menu">
                <?=Model_Product::render_products_menu()?>
            </div>
        </div>
        <div id="ct">
            <div id="checkout_messages"></div>
            <?=$page_data['content']?>
            <?php
            if($page_data['name_tag'] == 'news.html') echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
            if($page_data['name_tag'] == 'testimonials.html') echo '<h1>Testimonials</h1>' . Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
            ?>
            <?php if($page_data['name_tag'] == 'products.html' AND !empty($page_data['current_item_category']) AND !($page_data['current_item_category'] == 'order-prints') AND !($page_data['current_item_category'] == 'product_details')){
                echo Model_Product::render_products_list_html();
                echo "<style type='text/css' >#sideRt{display: none!important; width: 0 !important; } div#main #ct{width:736px !important;}</style>";

            }?>
            <?php if($page_data['name_tag'] == 'products.html' AND !empty($page_data['current_item_category']) AND ($page_data['current_item_category'] == 'product_details')){
                echo Model_Product::render_product_details();
            }?>
            <?php if($page_data['name_tag'] == 'products.html' AND empty($page_data['current_item_category'])){
                //Categories
                echo Model_Product::render_products_category_html();
                echo "<style type='text/css' >#sideRt{display: none!important; width: 0 !important; } div#main #ct{width:736px !important;}</style>";
            }
            ?>
            <?php if($page_data['name_tag'] == 'checkout.html') echo Model_Product::render_checkout_html() . '<style type="text/css">#sideRt{ display: none;}</style>'; ?>
            <?php if($page_data['name_tag'] == 'contact-us.html'): ?>
                <?php Model_Formprocessor::contactus(); ?>
            <?php endif;?>
        </div>
        <div id="sideRt">
            <div class="sideRt_panel">
                <?=Model_Panels::get_panels_feed('content_right');?>
            </div>
            <div class="gallery">
                <?php echo Model_Gallery::get_category_slider('home'); ?>
                <div><h1>FEATURED BRANDS</h1></div>
            </div>
        </div>
        <div id="order_online">
            Order online and have your order delivered to your door - <span class="orange">Look through our products below</span>
        </div>
        <div id="news" class="home_news">
            <?php echo Model_News::get_plugin_items_front_end_feed('home')?>
        </div>
        <div id="clear"></div>
    </div>
    <?php require 'footer.php' ?>
</div>
    <?= Settings::instance()->get('footer_html'); ?>
</body>
</html>