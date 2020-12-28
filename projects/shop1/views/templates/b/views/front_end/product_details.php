<!-- products-breadcrumbs -->
<?php
$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;

$ok             = ($disable_purchase != 1 AND Settings::instance()->get('product_enquiry') != 1);
$stock_disabled = (Settings::instance()->get('stock_enabled') != 'TRUE');
$in_stock       = ((Settings::instance()->get('stock_enabled') == 'TRUE' AND ((isset($options) AND count($options) > 0) OR (isset($quantity) AND isset($quantity_enabled) AND $quantity_enabled == '1'))));
$in_stock       = ($in_stock XOR $stock_disabled);
$ok             = ($ok AND $in_stock);
if(Settings::instance()->get('stock_enabled') == 'TRUE')
{
    if(Settings::instance()->get('override_stock') == 'TRUE' AND !((isset($options) AND count($options) > 0) OR (isset($quantity) AND isset($quantity_enabled) AND $quantity_enabled == '1')))
    {
        $ok = false;
    }
}
?>
<div id="product">
	<div class="bck-title-area">
		<div class="bck-area"><a href="javascript:history.back(1)"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/back.png"/>BACK</a></div>
		<div class="product-name-area"><?= $title ?></div>
	</div>
	<?php $filepath = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'','products'); ?>
	<?php if (count($images) > 1): ?>
		<div class="product-lft">
			<div class="proThum-area left" id="product_thumbs_area">
				<?php foreach ($images as $key => $image): ?>
					<a class="prod_thumb" href="#" data-image="<?= $filepath.$image ?>">
						<img src="<?= $filepath.'_thumbs/'.$image ?>" alt="<?= $image ?>" class="productAltImage" id="prodImage_<?= $key ?>" data-zoom-image="<?= $filepath.$image ?>" />
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="product-rgt">
		<form id="ProductDetailsForm" name="ProductDetailsForm">

			<div class="product-images" id="product_image">
				<?php ( ! isset($images[0]) OR empty($images[0])) ? $images[0] = 'no_image_available.jpg' : NULL; ?>
				<img src="<?= $filepath.$images[0] ?>" class="left" id="prodImage_0" alt="<?= $images[0] ?>" data-zoom-image="<?= $filepath.$images[0] ?>" />
			</div>
			<div id="product_details">
				<div id="product_description">
					<?php if (isset($brief_description) AND $brief_description != ''): ?>
						<div class="product_description_text product_brief_description_text">
							<p><?= nl2br($brief_description) ?></p>
						</div>
					<?php endif; ?>
					<?php $option_groups = ($matrix != '' AND $matrix != 0) ? Model_Matrix::get_matrix_option_groups($matrix) : Model_Option::get_all_groups_by_product($id); ?>

					<?php
				  //  $option_groups = Model_Option::get_all_groups_by_product($id);

					foreach($option_groups AS $index => $option_group)
					{
						if($option_group['option_group'] == "Text")
						{
							echo '<input type="text" name="text_input" class="option_text_input validate[required]"/>';
							$option_group_shot_name = explode(" ",$option_group['option_group']);
							$options = array();
						}
						else
						{
							$option_group_shot_name = explode(" ",$option_group['option_group']);
							if(Settings::instance()->get('stock_enabled') == "TRUE")
							{
								if($option_group['is_stock'] == "1")
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
						echo '<label for="',$option_group['option_group'],'" class="text_label">',(($option_group['group_label'] !== NULL AND !empty($option_group['group_label'])) ? $option_group['group_label'] : $option_group_shot_name[0]),': </label>';
						echo '<div class="prod_option_item left">';
						echo '<select id="option_',$index,'" name="',$option_group['option_group'],'" class="prod_option';
						if (!empty($option_group['required'])){
							echo ' validate[required]';
						}
						echo '" onchange="updateProductOptionPrice(this,'.$id.')">';
						echo '<option value="">Choose...</option>';
						foreach($options as $option)
						{
							$label = ((float)$option['price'] > 0) ? $option['label'] ." + &euro;". number_format($option['price'], 2) : $option['label'];
							echo '<option value="',$option['id'],'" data-option_price="'.$option['price'].'"'.(($option['default'] == 1) ? ' selected="selected"' : '').'>',$label, '</option>';
						}
						echo '</select>';
						echo '</div>';
						echo '<div class="notice"></div>';
					}
					?>
					<?php $purchase_enabled = (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1); ?>
				<?php if ($purchase_enabled OR Settings::instance()->get('purchase_disabled_show_prices')): ?>
					<div id="product_price">
						<?php
						if (class_exists('Model_Currency') AND count(Model_Currency::getRates()) > 0) {
							$currencies             = Model_Currency::getCurrencies(true);
							$pcurrency              = Model_Currency::getPreferredCurrency(true);
							$currency_symbol        = $currencies[$pcurrency]['symbol'];
							$offer_price_to_display = number_format((isset($offer_price) ? Model_Currency::convert($offer_price) : 0), 2);
							$price_to_display       = number_format(Model_Currency::convert($price), 2);
							$conversion_rate        = Model_Currency::convert(1);
						}
						else
						{
							$currency_symbol = '&euro;';
							$offer_price_to_display = number_format((isset($offer_price) ? $offer_price : 0), 2);
							$price_to_display       = number_format($price, 2);
							$conversion_rate        = 1;
						}
						?>

						<?php if ($display_offer != 0): ?>
							<dl>
								<dt><?= __('Price') ?></dt>
								<dd id="product_offer_price"><s>&nbsp;<?= $currency_symbol ?><?= $price_to_display ?>&nbsp;</s></dd>

								<dt><?= __('Offer Price') ?></dt>
								<dd
									id="final_price"
									data-product_price="<?= $offer_price ?>"
									data-conversion="<?= $conversion_rate ?>"
									data-currency="<?= $currency_symbol ?>"
									><?= $currency_symbol ?><?= $offer_price_to_display ?></dd>
							</dl>
						<?php else: ?>
							<div
								id="final_price"
								data-product_price="<?= $price ?>"
								data-conversion="<?= $conversion_rate ?>"
								data-currency="<?= $currency_symbol ?>"
								><?= $currency_symbol ?><?= $price_to_display ?></div>
						<?php endif; ?>

					</div>
				<?php endif; ?>
					<?php if($ok): ?>
					   <?php $quantity_dropdown = (isset($quantity_enabled) AND $quantity_enabled == '1' AND count($options) == 0 AND $quantity < 11) ? ($quantity+1): 11; ?>
							<input type='button' value='&minus;' class='qtyminus' field='qty' />
							<input id="qty" type="text" name="qty"  value="1" class='qty' data-mx="<?php echo $quantity_dropdown; ?>"/>
							<input type='button' value='+' class='qtyplus' field='qty' />
					<?php endif; ?>
					 <?php
						$url_parts    = explode('/', trim($_SERVER['SCRIPT_URL'], '/'));
						$product_name = end($url_parts);
						?>

						<?php if($ok): ?>
							<div id="product_purchase">
								<button type="button" id="add_to_cart_button" onclick="validateQty(<?=$id?>, $('#qty').val(), getOptions());">
									Add to Cart &raquo;
								</button>
							</div>
							<div class="successful" id="add-to-cart-success-message" style="display: none;">
								<?php
								$continue_shopping_url = Session::instance()->get('last_product_browsing_url');
								$continue_shopping_url = ($continue_shopping_url == '') ? '/products.html' : $continue_shopping_url;
								?>
								<p>Your item has been added to your cart. Would you like to <a href="/checkout.html">checkout</a> or <a href="<?= $continue_shopping_url ?>">continue shopping</a>?</p>
							</div>
						<?php else: ?>
							<?php if ( ! $in_stock): ?>
								<div class="notice notice_bad out_of_stock_notice" style="display:block;">Sorry, this product is out of stock.</div>
							<?php endif; ?>
						<?php endif; ?>
				</div>

					<div id="wishlist_button"><a href="<?=URL::site()?>?pid=<?= $id ?>&pname=<?= $product_name ?>">ADD TO WISHLIST</a></div>
					<div id="product_social_media">
					<div class="fb-like"
						 data-href="<?= substr(URL::site(),0,-1).$_SERVER["REQUEST_URI"] ?>"
						 data-width="280"
						 data-height="22"
						 data-colorscheme="light"
						 data-layout="button"
						 data-action="like"
						 data-show-faces="false"
						 data-send="false"></div>

					<? if (strpos($_SERVER['HTTP_HOST'],'ambersos') !== FALSE): ?>
						<!-- AddThis Button BEGIN -->
						<div class="addthis_toolbox addthis_default_style addthis_32x32_style" style="height: 100px;">
							<a href="https://www.addthis.com/bookmark.php?v=250&amp;username=ra-<?= Settings::instance()->get('addthis_id') ?>" class="addthis_button_compact">Share</a>
							<a class="addthis_button_preferred_1"></a>
							<a class="addthis_button_preferred_2"></a>
							<a class="addthis_button_preferred_3"></a>
							<a class="addthis_button_preferred_4"></a>
						</div>
						<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#username=ra-<?= Settings::instance()->get('addthis_id') ?>"></script>
						<!-- AddThis Button END -->
					<?php endif; ?>
				</div>
			</div>


			<?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
			<script>
				jQuery(document).ready(function(){

					$('.qtyplus').click(function(e){
						e.preventDefault();
						fieldName = $(this).attr('field');
						var currentVal = parseInt($('input[name='+fieldName+']').val());
						var maxval=$('#'+fieldName).attr('data-mx');
						if (currentVal < maxval) {
							$('input[name='+fieldName+']').val(currentVal + 1);
						}
					});
					$(".qtyminus").click(function(e) {
						e.preventDefault();
						fieldName = $(this).attr('field');
						var currentVal = parseInt($('input[name='+fieldName+']').val());
						if (!isNaN(currentVal) && currentVal > 1) {
							$('input[name='+fieldName+']').val(currentVal - 1);
						}
					});
				});

			</script>
			<script type="text/javascript" src="<?= URL::get_skin_urlpath(TRUE) ?>js/jquery.elevateZoom-3.0.8.min.js"></script>
			<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/products_front_end_general.js"></script>
			<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('products') ?>js/front_end/product_details.js"></script>
			<script type="text/javascript" src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/checkout.js"></script>
		</form>

		<div style="clear: both;">
			<div class="tab-area product-details-tab-area">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#desc">Description</a></li>
					<li><a data-toggle="tab" href="#guide">Size Guide</a></li>
					<?php if (isset($reviews) AND Settings::instance()->get('enable_customer_reviews') == '1'): ?>
						<li>
							<a data-toggle="tab" href="#reviews">Reviews <sup class="review-total"><?= count($reviews) ?></sup></a>
						</li>
					<?php endif; ?>
				</ul>
				<div class="tab-content">
					<div id="desc" class="tab-pane fade in active">
						<?= isset($description) ? $description : '' ?>
					</div>
					<div id="guide" class="tab-pane fade">
						<?= (isset($size_guide_data) AND isset($size_guide_data['content'])) ? $size_guide_data['content'] : ''; ?>
					</div>
					<?php if (isset($reviews) AND Settings::instance()->get('enable_customer_reviews') == '1'): ?>
						<div id="reviews" class="tab-pane fade">
							<?= View::factory('front_end/list_product_reviews')->set('reviews', $reviews); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if (isset($reviews)): ?>
				<?= View::factory('front_end/add_product_review')
					->set('product_id', $id)
					->set('count_ratings', count($reviews))
					->set('average_rating', (isset($average_rating) ? $average_rating : 0));
				?>
			<?php endif; ?>
		</div>

		<?php if (isset($related_to) AND count($related_to) > 0): ?>
			<div id="products_related">
				<h2>People Also Viewed</h2>
				<div class="related-products-feed">
					<?= Model_Product::render_related_products_html($related_to); ?>
				</div>
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
</div>