<?php $filepath = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', Model_Product::MEDIA_IMAGES_FOLDER); ?>
<div class="product-details-view" id="product_details_view">
	<form id="ProductDetailsForm" name="ProductDetailsForm">
		<div class="product-image-and-description">
			<div class="product-images" id="product_images">
				<div id="product_image" class="main-product-image">
					<?php $images[0] = (isset($images[0]) AND !empty($images[0])) ? $images[0] : 'no_image_available.png'; ?>
					<img src="<?= $filepath.$images[0] ?>" data-zoom-image="<?= $filepath.$images[0] ?>"
						 id="prodImage_0" alt="<?= $images[0] ?>"/>
				</div>
				<div id="product_thumbs_area">
					<?php if (count($images) > 1): ?>
						<?php for ($i = 1; $i < count($images); $i++): ?>
							<a class="prod_thumb" href="#" data-image="<?= $filepath.$images[$i] ?>"
							   data-zoom-image="<?= $filepath.$images[$i] ?>">
								<img
									id="prodImage_<?= $i ?>"
									src="<?= $filepath.$images[$i] ?>"
									alt="<?= $images[$i] ?>"
									/>
							</a>
						<?php endfor; ?>
					<?php endif; ?>
				</div>
			</div>

			<div class="product-purchase-details" id="product_purchase_details">
				<header class="product-header">
					<h1 class="product-title" id="product_title"><?= $title ?></h1>
					<?php if (isset($author) AND $author != ''): ?>
						<a href="/<?= Model_Product::get_products_plugin_page() ?>/Authors/<?= $author ?>" class="product-author"><?= $author ?></a>
					<?php endif; ?>
				</header>

				<?php if (isset($product_code) AND $product_code != ''): ?>
					<div class="product-code">
						<span><?= __('Style Code') ?></span>
						<span><?= $product_code ?></span>
					</div>
				<?php endif; ?>

				<?php $purchase_enabled = (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1); ?>
				<?php if ($purchase_enabled OR Settings::instance()->get('purchase_disabled_show_prices')): ?>
					<div class="product-price" id="product_price">
						<?php if ($display_offer != 0): ?>
							<p><span><?= __('Price') ?></span> <span><s>&euro;<?= $price ?></s></span></p>
							<p>
								<span><?= __('Offer Price') ?></span>
								<span data-product_price="<?= $offer_price ?>"
									  id="final_price">&euro;<?= number_format($offer_price - (isset($discount_total) ? $discount_total : 0), 2) ?></span>
							</p>
                        <?php else: ?>

                            <?php if (isset($discount_total) AND $discount_total != 0): ?>
                                <p>
                                    <span><?= __('Price') ?></span> <span><s>&euro;<?= $price ?></s></span>
                                    <span data-product_price="<?= $price ?>"
                                      id="final_price">&euro;<?= number_format($price - $discount_total, 2) ?></span>
                                </p>
                            <?php else: ?>
                                <p>
                                    <span class="price-label"><?= __('Price') ?></span>
                                    <span data-product_price="<?= $price ?>" id="final_price">&euro;<?= $price ?></span>
                                </p>
                            <?php endif;  ?>
                        <?php endif; ?>
					</div>

					<div class="product-description"><?= $description ?></div>
				<?php endif; ?>

                <?php $option_groups = Model_Option::get_all_groups_by_product($id); ?>

                <?php if (count($option_groups)): ?>
                    <div class="product-options" id="product_options">
                        <?php foreach ($option_groups AS $index => $option_group): ?>
                            <div class="product-option-group">
                                <?php
                                $options = Model_Option::get_options_by_field('group', $option_group['option_group']);
                                $color_option = (strpos(strtolower($option_group['option_group']), 'color') !== FALSE OR strpos(strtolower($option_group['option_group']), 'colour') !== FALSE);
                                ?>

                                <label class="product-option-label" for="option_<?= $index ?>"><?= $option_group['group_label'] ?></label>

                                <div class="prod_option_item">
                                    <select
                                        id="option_<?= $index ?>" name="<?= $option_group['option_group'] ?>"
                                        class="prod_option<?= (!empty($option_group['required'])) ? ' validate[dropdown,min[1]]' : '' ?>"
                                        onchange="updateProductOptionPrice()"
                                        style="display: none;"
                                        >
                                        <option value="0"><?= __('-- Please select --') ?></option>
                                        <?php foreach ($options as $option): ?>
                                            <?php $label = ((float) $option['price'] > 0) ? $option['label']." + &euro;".$option['price'] : $option['label']; ?>
                                            <option value="<?= $option['id'] ?>" data-option_price="<?= $option['price'] ?>"<?= ($option['default'] == 1) ? ' selected="selected"' : '' ?>><?= __($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="product-footer">
                    <?php if ($purchase_enabled OR Settings::instance()->get('purchase_disabled_show_prices')): ?>
                        <div class="product-option-group">
                            <label for="qty" class="product-option-label"><?= __('Quantity') ?></label>

                            <div class="prod_option_item">
                                <select id="qty" name="qty">
                                    <?php $quantity_dropdown = (isset($quantity_enabled) AND $quantity_enabled == '1' AND count($options) == 0 AND $quantity < 11) ? ($quantity + 1) : 11; ?>
                                    <?php for ($i = 1; $i < $quantity_dropdown; ++$i): ?>
                                        <option><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="product-purchase-buttons" id="product_purchase_buttons">
                        <?php if (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1): ?>
                            <button type="button" id="purchase_button" class="product_btn buy-now-button add-to-cart-button"
                                    onclick="validateQty_and_checkout(<?= $id ?>, $('#qty').val(), getOptions());">
                                <span><?= __('Add to Cart') ?></span>
                            </button>
                        <?php endif; ?>

                        <div class="successful message_area" style="display:none;"></div>
                    </div>
                </div>

                <div class="sharing">
                    <span class="sharing-label"><?= __('Share') ?></span>

                    <ul class="list-inline">
                        <li>
                            <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode(URL::base().$_SERVER['REQUEST_URI']) ?>" class="sharing-link" title="<?= __('Share on Facebook') ?>">
                                <span class="sr-only"><?= __('Share on Facebook') ?></span>

                                <span class="sharing-icon">
                                    <span class="fa fa-facebook"></span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(URL::base().$_SERVER['REQUEST_URI'])  ?>" class="sharing-link" title="<?= __('Share on Twitter') ?>">
                                <span class="sr-only"><?= __('Share on Twitter') ?></span>

                                <span class="sharing-icon">
                                    <span class="fa fa-twitter"></span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="mailto:?subject=<?= rawurlencode($title) ?>&body=<?= urlencode(URL::base().$_SERVER['REQUEST_URI']) ?>" class="sharing-link" title="<?= __('Email') ?>">
                                <span class="sr-only"><?= __('Email') ?></span>

                                <span class="sharing-icon">
                                    <span class="fa fa-envelope"></span>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
			</div>
		</div>

		<div id="messageBar"></div>

		<?php if (isset($related_to) AND count($related_to) > 0): ?>
			<div id="products_related">
				<h2><?= __('You might also like') ?></h2>
				<div class="pagedemo">
					<?= Model_Product::render_related_products_html($related_to); ?>
				</div>
			</div>
		<?php endif; ?>
	</form>
</div>

<?php
// Render JS files for this view
if (isset($view_js_files))
{
	foreach ($view_js_files as $js_item_html) echo $js_item_html;
}
?>
<script>
	$('.prod_thumb').on('click', function(ev)
	{
		ev.preventDefault();

		if (document.getElementsByClassName('prod_thumb').length > 1)
		{
			var big_image = document.getElementById('prodImage_0');
			var thumbnail = this.getElementsByTagName('img')[0];
			var old_src = big_image.src;

			big_image.src = thumbnail.src;
			thumbnail.src = old_src;
		}
	});
</script>