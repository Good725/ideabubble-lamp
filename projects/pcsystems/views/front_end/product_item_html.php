<?php
$product_url       = ($url_title != '') ? $url_title : urlencode(str_replace(' ', '-', trim($title)));
$add_products_page = rtrim($_SERVER['SCRIPT_URL'], '/');
$feed_item_length  = Settings::instance()->get('product_feed_title_truncation');
$feed_item_length  = ($feed_item_length == 0 OR empty($feed_item_length)) ? 20 : $feed_item_length;

if (strpos($_SERVER['REQUEST_URI'], Model_Product::get_products_plugin_page()) == FALSE)
{
    $add_products_page = '/' . Model_Product::get_products_plugin_page();
}

$no_image = FALSE;
if ( ! isset($images[0]) OR empty($images[0]))
{
    $images[0] = 'no_image_available.jpg';
    $no_image = TRUE;
}
?>
<div class="thumb_product<?= ($no_image) ? ' no_image' : '' ?>  <?= (isset($builder) AND $builder == 1) ? 'builder_product' : ''; ?> product_featured">
    <div class="thumb_product_image">
        <a title="View product: '<?= $title ?>' details" href="<?= $add_products_page.'/'.$product_url ?>"><?php
            $filename = isset($images[1]) ? $images[1] : $images[0];
            $local_filepath = DOCROOT . 'media/photos/' . Model_Product::MEDIA_IMAGES_FOLDER . '/';
            $urlpath = URL::Media('media') . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . Model_Product::MEDIA_IMAGES_FOLDER . DIRECTORY_SEPARATOR;

            if (isset($thumb_url) AND $thumb_url != '') {
                $image = $thumb_url;
            } else {
                $image = file_exists($local_filepath.'_thumbs/'.$filename) ? $urlpath.'_thumbs/'.$filename : $urlpath.$filename;
            }
            ?>
            <img alt="<?= $title ?>" src="<?= $image ?>" class="featured_image"/>
        </a>
    </div>
    <div class="thumb_product_info">
        <a title="View product: '<?= $title ?>' details" href="<?= $add_products_page.'/'.$product_url ?>">
            <div class="thumb_product_title"><?= $title ?></div>
            <?php if (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1): ?>
                <div class="thumb_product_price">
                    <?php if ($offer_price < $price AND $display_offer == '1'): ?>
                        <s class="thumb_product_discount_price">&euro;<?= $price ?></s>
                        <span class="thumb_product_final_price">&euro;<?= $offer_price ?></span>
                    <?php else: ?>
                        <span class="thumb_product_final_price">&euro;<?= $price ?></span>
                    <?php endif; ?>
					<?php if ($price != $price_with_vat): ?>
						<span class="product-excl">Incl. VAT: <span class="orange">&euro;<?= trim($price_with_vat) ?></span></span>
					<?php endif; ?>
                </div>
            <?php endif; ?>
        </a>
    </div>

    <a href="<?= $add_products_page.'/'.$product_url ?>" class="button button-primary buy-now-button">Buy Now</a>
</div>