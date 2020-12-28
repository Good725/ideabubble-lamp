<?php
$product_url = ($url_title != '') ? $url_title : str_replace(' ', '-', trim($title));
$add_products_page = rtrim($_SERVER['REQUEST_URI'], '/');
$add_products_page = preg_replace('/\?.*/', '', $add_products_page);
if (strpos($_SERVER['REQUEST_URI'], Model_Product::get_products_plugin_page()) == FALSE)
{
	$add_products_page = '/'.Model_Product::get_products_plugin_page();
}
if( ! isset($images[0]) OR empty($images[0]))
{
	$images[0] = 'no_image_available.jpg';
}
?>
<a title="View product: '<?=$title?>' details" href="<?=$add_products_page.'/'.$product_url?>">
	<div class="left product_item_list listview">
		<div class="left product_item_list_image">
			<img alt="" src="<?= HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[0], Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR.'_thumbs')) ?>" />
		</div>
		<div class="left product_item_list_info">
			<h3 class="product_item_list_title"><?= $title ?></h3>
            <?php if (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1): ?>
                <div class="product_item_list_price">
				<?php
				if (class_exists('Model_Currency') AND count(Model_Currency::getRates()) > 0) {
					$currencies = Model_Currency::getCurrencies(true);
					$pcurrency = Model_Currency::getPreferredCurrency(true);
				?>
					<?php if ($offer_price < $price AND $display_offer == '1'): ?>
						<span class="grey">Was <?=$currencies[$pcurrency]['symbol']?><span class="line-through"><?= Model_Currency::convert($price)?></span></span> <span
								class="yellow strong">NOW <?=$currencies[$pcurrency]['symbol']?><?= Model_Currency::convert($offer_price) ?></span>
					<?php else: ?>
						<span class="yellow strong"><?=$currencies[$pcurrency]['symbol']?><?= Model_Currency::convert($price) ?></span>
					<?php endif; ?>
				<?php
				} else {
				?>
					<?php if ($offer_price < $price AND $display_offer == '1'): ?>
						<span class="grey">Was &euro;<span class="line-through"><?= number_format($price, 2) ?></span></span> <span
								class="yellow strong">NOW &euro;<?= number_format($offer_price, 2) ?></span>
					<?php else: ?>
						<span class="yellow strong">&euro;<?= number_format($price, 2) ?></span>
					<?php endif; ?>
				<?php
				}
				?>
                </div>
            <?php endif; ?>

			<?php if (Session::instance()->get('display_mode') == 'list' AND trim($brief_description)): ?>
				<div class="product_item_list_description">
					<p><?= $brief_description ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</a>
