<?php
$product_plugin_page = Model_Product::get_products_plugin_page();
$product_url = ($url_title != '') ? $url_title : urlencode(str_replace(' ', '-', trim($title)));
$product_url = '/'.$product_plugin_page.'/Authors/'.$author.'/'.$product_url;
$featured_image = (!isset($images[0]) OR empty($images[0])) ? 'no_image_available.jpg' : $images[0];
$src = $thumb_url ? $thumb_url : HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $featured_image, Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR));
?>

<div class="feed-product thumb_product" id="feed-product-<?= $id ?>">
	<div class="feed-product-image-and-data">
		<div class="feed-product-image">
			<a href="<?= $product_url ?>">
				<img src="<?= $src ?>" alt="" title="<?= $title ?>"/>
			</a>
		</div>
		<div class="feed-product-data">
			<header>
				<h2 class="feed-product-title"><a href="<?= $product_url ?>"><?= $title ?></a></h2>
				<?php if (isset($author)): ?>
					<a href="/<?= $product_plugin_page.'/Authors/'.$author ?>" class="product-author"><?= $author ?></a>
				<?php endif; ?>
			</header>

			<?php if ($display_price != 0 AND $disable_purchase != 1): ?>
				<div class="feed-product-details">
					<?php if ($display_offer != 0): ?>
						<div class="feed-product-price"><s>&euro;<?= $price ?></s></div>
						<div class="feed-product-price">&euro;<?= $offer_price ?></div>
                    <?php else: ?>
                        <?php if (isset($discount_total) AND $discount_total != 0): ?>
                            <div class="feed-product-price"><s>&euro;<?= $price ?></s></div>
                            <div class="feed-product-price">&euro;<?= number_format($price - $discount_total, 2) ?></div>
                        <?php else: ?>
                            <div class="feed-product-price">&euro;<?= $price ?></div>
                        <?php endif; ?>
                   <?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="feed-product-description"><?= $brief_description ?></div>
	<div class="feed-product-actions">
		<a class="feed-product-more"
		   href="/<?= Model_Product::get_products_plugin_page().DIRECTORY_SEPARATOR.$product_url ?>"><span><?= __('More') ?></span></a>

		<button type="button" id="add_to_cart_button" class="product_btn feed-product-addtocart"
				onclick="validateQty_and_checkout('<?= $id ?>', 1, {})">
			<span><?= __('Add to Cart') ?></span>
		</button>

	</div>
</div>
