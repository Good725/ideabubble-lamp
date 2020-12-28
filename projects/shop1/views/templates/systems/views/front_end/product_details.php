<!-- products-breadcrumbs -->
<?php
$ok             = ($disable_purchase != 1 AND Settings::instance()->get('product_enquiry') != 1);
$stock_disabled = (Settings::instance()->get('stock_enabled') != 'TRUE');
$in_stock       = ((Settings::instance()->get('stock_enabled') == 'TRUE' AND ((isset($options) AND count($options) > 0) OR (isset($quantity) AND isset($quantity_enabled) AND $quantity_enabled == '1'))));
$in_stock       = ($in_stock XOR $stock_disabled);
$ok             = ($ok AND $in_stock);
$vat_rate       = Settings::instance()->get('vat_rate');
$media_path     = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/'));
if (Settings::instance()->get('stock_enabled') == 'TRUE')
{
    if (Settings::instance()->get('override_stock') == 'TRUE' AND !((isset($options) AND count($options) > 0) OR (isset($quantity) AND isset($quantity_enabled) AND $quantity_enabled == '1')))
    {
        $ok = false;
    }
}
?>
<div class="breadcrumb-nav" id="breadcrumb-nav"><?= trim(''.IbHelpers::breadcrumb_navigation()) ?></div>
<form id="ProductDetailsForm" name="ProductDetailsForm">
    <div id="product">
        <h1><?=$title?></h1>
        <div id="product_image">
            <?php $images[0] = ( ! isset($images[0]) OR empty($images[0])) ? $images[0] = 'no_image_available.jpg' : $images[0]; ?>
            <img src="<?= $media_path.$images[0] ?>" class="left" id="prodImage_0" alt="<?= $images[0] ?>" />
            <div class="proThum-area">
                <?php for ($i = 1; $i < count($images); $i++): ?>
					<img onclick="imageTrick('<?= $i ?>')" src="<?= $media_path.'_thumbs/'.$images[$i] ?>"
						 alt="<?= $images[$i] ?>" class="productAltImage left" id="prodImage_<?= $i ?>"
						/>
				<?php endfor; ?>
            </div>
        </div>
        <div id="product_details" class="product-info">
            <div class="product-description">
                <p><?= $brief_description ?></p>
            </div>

            <?php if (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1): ?>
				<?php if ($display_offer != 0): ?>
                	<div id="product_price">
						<div class="product-original-price">
							<span class="product-excl">
								<span class="product-price-label">Original Price</span>
								<span class="product-price"><s><span>&euro;<?= number_format($price, 2) ?></span></s></span>
							</span>
							<span class="product-incl">
								<span class="product-price-label">Incl. Vat</span>
								<span class="product-price"><s>&euro;<?= number_format(Model_Product::calculate_total_price($price, $vat_rate), 2) ?></s></span>
							</span>
						</div>
						<div class="product-final-price">
							<span class="product-excl">
								<span class="product-price-label">Our Price</span>
								<span class="product-price" id="product-nonvat-price">&euro;<?= number_format($offer_price, 2) ?></span>
							</span>
							<span class="product-incl">
								<span class="product-price-label">Incl. Vat</span>
								<span id="final_price" data-product_price="<?= Model_Product::calculate_total_price($offer_price, $vat_rate) ?>" class="product-price">&euro;<?= Model_Product::calculate_total_price($offer_price, $vat_rate) ?></span>
							</span>
						</div>

					</div>
				<?php else: ?>
					<div class="product-price-no-offer">
						<span class="product-excl">
							<span class="product-price-label">Excl. VAT</span>
							<span class="product-price" id="product-nonvat-price">&euro;<?= number_format($price, 2) ?></span>
						</span>
						<div class="product-incl">
							<span class="product-price-label">Incl. VAT</span>
							<span class="product-price" id="final_price" data-product_price="<?= Model_Product::calculate_total_price($price, $vat_rate) ?>">&euro;<?= number_format(Model_Product::calculate_total_price($price, $vat_rate), 2) ?></span>
						</div>
					</div>
				<?php endif; ?>
            <?php endif; ?>

			<input type="hidden" id="product-matrix-id" value="<?= $matrix ?>" />
			<input type="hidden" id="product-vat-rate" value="<?= Settings::instance()->get('vat_rate') ?>" />

			<?php // Really belongs in the model file
			$option_groups = ($matrix != '' AND $matrix != 0) ? Model_Matrix::get_matrix_option_groups($matrix) : Model_Option::get_all_groups_by_product($id);
			foreach ($option_groups AS $index => $option_group)
			{
				echo '<div class="option-group">';
				if ($option_group['option_group'] == "Text")
				{
					echo '<input type="text" name="text_input" class="option_text_input validate[required]" />';
					$option_group_shot_name = explode(" ",$option_group['option_group']);
					$options = array();
				}
				else
				{
					$option_group_shot_name = explode(" ",$option_group['option_group']);
					if (Settings::instance()->get('stock_enabled') == "TRUE")
					{
						if ($option_group['is_stock'] == "1")
						{
							$options = Model_Option::get_stock_options($option_group['option_group'],$id);
						}
						else
						{
							$options = Model_Option::get_options_by_field('group', $option_group['option_group']);
						}
					}
					else
					{
						$options = Model_Option::get_options_by_field('group', $option_group['option_group']);
					}
				}
				echo '<label for="',$option_group['option_group'],'" class="option-group-label">',(($option_group['group_label'] !== NULL AND !empty($option_group['group_label'])) ? $option_group['group_label'] : $option_group_shot_name[0]),'</label>';
				echo '<div class="option-group-control">';
				echo '<select id="option_',$index,'" name="',$option_group['option_group'],'" class="prod_option';
				if (!empty($option_group['required'])){
					echo ' validate[required]';
				}
				echo '" onchange="updateProductOptionPrice(this,'.$id.')">';
				echo '<option value="">Choose...</option>';
				foreach ($options as $option)
				{
					$label = ((float)$option['price'] > 0) ? $option['label'] ." + &euro;". $option['price'] : $option['label'];
					echo '<option value="',$option['id'],'" data-option_price="'. $option['price'] .'"'.(($option['default'] == 1) ? ' selected="selected"' : '').'>',$label, '</option>';
				}
				echo '</select>';
				echo '</div>';
				echo '<div class="notice"></div>';
				echo '</div>';
			}
			?>


            <?php if ($ok): ?>
				<div class="option-group">
					<label for="qty" class="option-group-label product-qty">Qty</label>
					<div class="option-group-control">
						<input type="number" class="product-qty-field" id="qty" name="qty" value="1" min="1" max="<?= (isset($quantity_enabled) AND $quantity_enabled == '1' AND count($options) == 0 AND $quantity < 11) ? $quantity : 10 ?>">
					</div>

				</div>
            <?php endif; ?>

            <div>
                <?php if($ok): ?>
                    <div id="product_purchase" class="product_purchase">
						<button type="button" class="button button-primary" id="purchase_button" onclick="validateQty(<?= $id ?>, $('#qty').val(), getOptions());">
							<span>Add to Cart</span>
						</button>
						<button type="button" class="button button-secondary" onclick="validateQty_and_checkout(<?= $id ?>, $('#qty').val(), getOptions());">
							<span>Buy Now</span>
						</button>
						<?php
						$continue_shopping_url = Session::instance()->get('last_product_browsing_url');
						$continue_shopping_url = ($continue_shopping_url == '') ? '/search.html' : $continue_shopping_url;
                        $url_parts    = explode('/', trim($_SERVER['SCRIPT_URL'], '/'));
                        $product_name = end($url_parts);
                        ?>
                        <a href="<?=URL::site()?>contact-us.html?pid=<?= $id ?>&pname=<?= $product_name ?>" class="button button-default">Contact Us</a>
						<div class="successful" id="add-to-cart-success-message" style="display: none;">
							<p>Your item has been added to your cart. Would you like to <a href="/checkout.html">checkout</a> or <a href="<?= $continue_shopping_url ?>">continue shopping</a>?</p>
						</div>
                    </div>
                <?php else: ?>
                    <?php if ( ! $in_stock): ?>
                        <div class="notice notice_bad out_of_stock_notice" style="display:block;">Sorry, this product is out of stock.</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="out_of_stock" style="display:none;">
                This product/option combination is out of stock.
            </div>

            <div id="product_social_media">
                <?php if (Settings::instance()->get('sharethis_id') != ''): ?>
					<div class="sharethis_buttons">
						<?= Settings::instance()->get('sharethis_buttons') ?>
					</div>
				<?php endif; ?>
            </div>

            <?php
            $url_parts    = explode('/', trim($_SERVER['SCRIPT_URL'], '/'));
            $product_name = end($url_parts);
			$size_guide   = (isset($size_guide_data) AND isset($size_guide_data['content'])) ? trim($size_guide_data['content']) : '';
            ?>
        </div>

		<?php if (trim($description)): ?>
			<div class="product-details-description"><?= trim($description) ?></div>
		<?php endif; ?>

		<?php if (count($documents) > 0): ?>
			<div class="product-details-technical-details">
				<h3>View Technical Details</h3>
				<?php $doc_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'docs'); ?>
				<ul>
					<?php foreach ($documents as $document): ?>
						<li><a href="<?= $doc_path.$document ?>">Download &quot;<?= $document ?>&quot;</a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if (trim($size_guide)): ?>
			<div class="product-details-size_guide"><?= trim($size_guide) ?></div>
		<?php endif; ?>

        <?php if (isset($related_to) AND count($related_to) > 0): ?>
            <div class="product-detail-related" id="products_related">
                <h2>Related Products</h2>
                <?= Model_Product::render_related_products_html($related_to); ?>
            </div>
        <?php endif; ?>
        <div id="youtube_videos">
            <?php
            $product = new Model_Product($id);
            $videos = $product->get_youtube_videos();
            if(count($videos) > 0)
            {
                echo '<h1>Product Videos</h1>';
            }
            foreach($videos AS $key=>$video):
                ?>
                <div class="youtube_video left">
                    <object width="240" height="158" data="https://www.youtube.com/v/<?=$video['video_id'];?>" type="application/x-shockwave-flash"><param name="allowFullScreen" value="true"/><param name="src" value="https://www.youtube.com/v/<?=$video['video_id'];?>"/></object>
                </div>
            <?php
            endforeach;
            ?>
        </div>
    </div>
    <?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
    <script type="text/javascript" src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/product_details.js"></script>
    <script type="text/javascript" src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/checkout.js"></script>

</form>
