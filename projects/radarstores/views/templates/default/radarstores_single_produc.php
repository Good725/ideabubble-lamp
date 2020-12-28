<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="<?php echo $page_data['seo_description'];?>">
    <meta name="keywords" content="<?php echo $page_data['seo_keywords'];?>">
    <meta name="author" content="http://ideabubble.ie">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_data['title'];?></title>
    <link REL="shortcut icon" href="<?=URL::site()?>assets/default/images/favicon.ico" type="image/ico"/>
    <link href="<?=URL::site()?>assets/default/css/bx_styles.css" rel="stylesheet" type="text/css"/>
    <link href="<?=URL::site()?>assets/default/css/jquery.bxslider.css" rel="stylesheet" type="text/css"/>
    <link href="<?=URL::site()?>assets/default/css/styles.css" rel="stylesheet" type="text/css"/>
	<link href='//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic' rel='stylesheet' type='text/css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?=URL::site()?>assets/default/js/jquery-1.7.2.min.js"><\/script>')</script>
	<script type="text/javascript" src="<?=URL::get_engine_plugin_assets_base('products')?>js/checkout.js"></script>
	<link href="<?=URL::site()?>assets/default/css/validation.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="<?=URL::site()?>assets/default/js/checkout.js"></script>
	<script type="text/javascript" src="<?=URL::site()?>assets/default/js/jquery.validationEngine2.js"></script>
	<script type="text/javascript" src="<?=URL::site()?>assets/default/js/jquery.validationEngine2-en.js"></script>
	<script type="text/javascript" src="<?=URL::site()?>assets/default/js/general.js"></script>
    <?= Settings::instance()->get('head_html'); ?>
</head>

<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
<div id="container">
	<div id="header">
		<div id="main_menu">
			<?php menuhelper::add_menu_editable_heading('main')?>
		</div>
	</div>
	<div id="main">
		<div id="sideLt">
			<div id="content-nav-top-padding"></div>
			<div class="products_menu">
			   <?=Model_Product::render_products_menu()?>
		   </div>
		</div>
		<div id="ct">
			<div id="breadcrumb-nav">
				HIKING >> TIMING >> STOPWATCH >> SILVA STOPWATCH
			</div>
			<div id="product">
				<div id="product_image">
					<img src="<?=URL::site()?>assets/default/images/large-stopwatch.png" border="0"/>
				</div>
				<div id="product_details">
					<div id="product_price" class="bold_text">
						<div class="float_left">
							Price
						</div>
						<div class="float_right white">
							€24.99
						</div>
					</div>
					<div id="product_description">
						<h1>PRODUCT DETAILS</h1>
						<p>
							Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus eu orci turpis, quis convallis sapien.
						</p>
						<div class="product_drop_down_container">
							<label class="bold_text" for="product_colour">
								COLOUR
							</label>
							<select id="product_colour">
								<option value="white">WHITE</option>
							</select>
						</div>
						<div class="product_drop_down_container">
							<label class="bold_text" for="product_quantity">QUANTITY</label>
							<select id="product_quantity">
								<option value="1">1</option>
							</select>

						</div>
					</div>
					<div id="product_purchase">
						<div id="add_to_cart_button">
							Add to Cart >>
						</div>
						<div id="purchase_button">
							Buy Now >>
						</div>
						<div id="continue_shopping">
							CONTINUE SHOPPING >>
						</div>
					</div>
				</div>
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
	<div id="footer">
		<?php require 'footer.php' ?>
	</div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>