<div id="special_offers_section">
	<?
	$add_products_page = rtrim($_SERVER['REQUEST_URI'], '/');
	if (strpos($_SERVER['REQUEST_URI'], Model_Product::get_products_plugin_page()) == FALSE)
	{
		$add_products_page = '/'.Model_Product::get_products_plugin_page();
	}
	$products = Model_Product::get_special_offers();
	?>
	<?php foreach($products AS $product): ?>
		<?php
		$image = (empty($product['file_name'])) ? 'not_image_available.jpg' : $product['file_name'];
		$product_url = $add_products_page.'/'.$product['category'].'/'.$product['url_title'];
		?>
		<a title="View product: '<?= $product['title'] ?>' details" href="<?= $product_url ?>">
			<div class="left product_item_list">
				<div class="left product_item_list_image">
					<img alt="<?= $product['title'] ?>" src="<?= HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$image, Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR.'_thumbs')) ?>" />
				</div>
				<div class="left product_item_list_info">
					<div class="product_item_list_title"><?= (strlen($product['title']) >= 20) ? substr($product['title'],0,20)."..." : $product['title']; ?></div>
					<div class="product_item_list_price">
						<span class="grey">Was &euro;<span class="line-through"><?= $product['price'] ?></span></span>
						<span class="yellow strong">NOW &euro;<?= $product['offer_price'] ?></span>
					</div>
				</div>
			</div>
		</a>
	<?php endforeach; ?>
</div>