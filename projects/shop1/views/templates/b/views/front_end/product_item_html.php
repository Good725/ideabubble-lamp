<?php 
$product_url       = ($url_title != '') ? $url_title : urlencode(str_replace(' ', '-', trim($title)));
$add_products_page = rtrim($_SERVER['REQUEST_URI'], '/');
$add_products_page = preg_replace('/\?.*/', '', $add_products_page);
$add_products_page = (strpos($add_products_page, '/ajax_get_products') !== FALSE) ? '/products.html' : $add_products_page;
$feed_item_length  = Settings::instance()->get('product_feed_title_truncation');
$feed_item_length  = ($feed_item_length == 0 OR empty($feed_item_length)) ? 20 : $feed_item_length;
$systems_layout    = (Kohana::$config->load('config')->template_folder_path == 'systems');

if(strpos($_SERVER['REQUEST_URI'], Model_Product::get_products_plugin_page()) == false){
	$add_products_page = '/'.Model_Product::get_products_plugin_page();
}

$no_image = FALSE;
if( ! isset($images[0]) OR empty($images[0]))
{
	$images[0] = 'no_image_available.jpg';
	$no_image = TRUE;
}
?>
<div class="thumb_product<?= ($no_image) ? ' no_image' : '' ?>  <?=(isset($builder) AND $builder == 1) ? 'builder_product' : '';?><?= ($systems_layout) ? ' product_featured' : '' ?>">
	<div class="thumb_product_image">
		<a title="View product: '<?=__($title)?>' details" href="<?=$add_products_page.'/'.$product_url?>">
            <?php
            $filename = (strpos($_SERVER['HTTP_HOST'],'mr-tee') !== FALSE AND isset($images[1])) ? $images[1] : $images[0];
            $filename = (is_array($filename) AND array_key_exists('file_name', $filename)) ? $filename['file_name'] : $filename;
            $filepath = HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR));
            $image    = (file_exists($filepath.'_thumbs/'.$filename)) ? $filepath.'_thumbs/'.$filename : $filepath.$filename;
            ?>
            <img alt="" src="<?= $image ?>"<?= $systems_layout ? ' class="featured_image"' : '' ?> />
	    </a>
	</div>
	<a href="<?=$add_products_page.'/'.$product_url?>" title="<?= __($title) ?>" class="thumb_product_info">
		<div class="thumb_product_title">
			<?= $title ?>
		</div>
		<?php $purchase_enabled = (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1); ?>
		<?php if ($purchase_enabled OR Settings::instance()->get('purchase_disabled_show_prices')): ?>
			<div class="thumb_product_price">
			<?php
			if (class_exists('Model_Currency') AND count(Model_Currency::getRates()) > 0) {
				$currencies = Model_Currency::getCurrencies(true);
				$pcurrency = Model_Currency::getPreferredCurrency(true);
			?>
				<?php if ($offer_price < $price AND $display_offer == '1'): ?>
					<span class="thumb_product_display_price"><s><?=$currencies[$pcurrency]['symbol']?><?= number_format(Model_Currency::convert($price), 2) ?></s></span><br />
					<span class="thumb_product_offer_price"><?=$currencies[$pcurrency]['symbol']?><?= number_format(Model_Currency::convert($offer_price), 2) ?></span>
				<?php else: ?>
					<?=$currencies[$pcurrency]['symbol']?><?= number_format(Model_Currency::convert($price), 2) ?>
				<?php endif; ?>
			<?php
			} else {
			?>
				<?php if ($offer_price < $price AND $display_offer == '1'): ?>
					<span class="thumb_product_display_price"><s>&euro;<?= number_format($price, 2) ?></s></span><br />
					<span class="thumb_product_offer_price">&euro;<?= number_format($offer_price, 2) ?></span>
				<?php else: ?>
					&euro;<?= number_format($price, 2) ?>
				<?php endif; ?>
			<?php
			}?>
				<div class="view_product_button secondary_button" style="display:none;">View Details</div>
			</div>
        <?php endif; ?>
	</a>

	<?php if ($systems_layout): ?>
		<a href="<?= $add_products_page.'/'.$product_url ?>" class="button button-primary buy-now-button"><span>Buy Now</span></a>
	<?php endif; ?>
</div>
