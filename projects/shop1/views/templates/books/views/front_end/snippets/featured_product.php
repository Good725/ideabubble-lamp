<?php
$product_plugin_page = Model_Product::get_products_plugin_page();
$product_url = ($product_data['url_title'] != '') ? $product_data['url_title'] : urlencode(str_replace(' ', '-', trim($product_data['title'])));
$product_url = '/'.$product_plugin_page.'/'.$product_url;
$featured_image = (!isset($product_data['images'][0]) OR empty($product_data['images'][0])) ? 'no_image_available.jpg' : $product_data['images'][0];
$src = $product_data['thumb_url'] ? $product_data['thumb_url'] : HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $featured_image, Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR));

?>

<div class="product-featured feed-product" id="feed-product-<?= $product_data['id'] ?>">
	<div class="feed-product-image-and-data">
		<div class="feed-product-image">
			<a href="<?= $product_url ?>">
				<img src="<?= $src ?>" alt="<?= $product_data['title'] ?>" title="<?= $product_data['title'] ?>"/>
			</a>
		</div>
		<div class="feed-product-data">
			<header>
				<h2 class="feed-product-title"><a href="<?= $product_url ?>"><?= $product_data['title'] ?></a></h2>
				<a href="/<?= $product_plugin_page.'/Authors/'.$product_data['author'] ?>" class="product-author"><?= $product_data['author'] ?></a>
			</header>

			<?php if ($product_data['display_price'] != 0 AND $product_data['disable_purchase'] != 1): ?>
				<div class="feed-product-details">
					<?php if ($product_data['display_offer'] != 0): ?>
						<div class="feed-product-price"><s>&euro;<?= $product_data['price'] ?></s></div>
						<div class="feed-product-price">&euro;<?= $product_data['offer_price'] ?></div>

                    <?php else: ?>
                        <?php if (isset($product_data['discount_total']) AND $product_data['discount_total'] != 0): ?>
                            <div class="feed-product-price"><s>&euro;<?= $product_data['price'] ?></s></div>
                            <div class="feed-product-price">&euro;<?= number_format($product_data['price'] - $product_data['discount_total'], 2) ?></div>
                        <?php else: ?>
                            <div class="feed-product-price">&euro;<?= $product_data['price'] ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="feed-product-description"><?= $product_data['brief_description'] ?></div>
	<div class="feed-product-actions">
		<a class="feed-product-more"
		   href="/<?= Model_Product::get_products_plugin_page().DIRECTORY_SEPARATOR.$product_url ?>"><span><?= __('More') ?></span></a>

		<button type="button" id="add_to_cart_button" class="product_btn feed-product-addtocart"
				onclick="validateQty_and_checkout('<?= $product_data['id'] ?>', 1, {})">
			<span><?= __('Add to Cart') ?></span>
		</button>

	</div>
</div>
