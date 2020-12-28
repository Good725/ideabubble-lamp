<?php
// Render CSS Files for THIS View
if (isset($view_css_files))
{
	foreach ($view_css_files as $css_item_html) echo $css_item_html;
}
// Render JS Files for This View
if (isset($view_js_files))
{
	foreach ($view_js_files as $js_item_html) echo $js_item_html;
}
?>
<!-- products-breadcrumbs -->
<div id="breadcrumb-nav">
    <?=IbHelpers::breadcrumb_navigation();?>
</div>

<div id="product_details_view" class="left row">
	<form id="ProductDetailsForm" name="ProductDetailsForm">
		<div id="product_category" class="row left">
			<h2><?=$category?></h2>
		</div>

		<div class="row left">
			<div id="product_images" class="left">
				<div id="product_image" class="left row">
					<?php
					if( ! isset($images[0]) OR empty($images[0]))
                    {
                        $filepath  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/'));
                        $images[0] = (file_exists($filepath.'not_image_available.jpg') AND ! file_exists($filepath.'no_image_available.jpg')) ? 'not_image_available.jpg' : 'no_image_available.jpg';
					}
					echo '<a class="jqzoom"'
						  .' href="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[0], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/')).'"'
						  .' id="zoomImage" style="outline-style: none; text-decoration: none; cursor: crosshair; display: block; position: relative; height: 32px; width: 10px;"'
						  .' title="'.$images[0].'">';
					echo '<img src="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[0], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/')).'" id="prodImage_0" alt="'.$images[0].'">';
					echo '</a>';
					?>
				</div>
				<div id="product_thumbs_area" class="left row">
					<?php
						for($i = 1; $i < count($images); $i++){
							echo '<img onclick="imageTrick(\''.$i.'\')"'
									.' id="prodImage_'.$i.'"'
								 	.' class="left prod_thumb"'
									.' src="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[$i], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR.'_thumbs/', '/')).'"'
									.' alt="'.$images[$i].'" />';
						}
					?>
				</div>
			</div>

			<div id="product_purchase_details" class="left">
				<h2>Product Details:</h2>
				<p><span class="left strong">Product Name:</span><br /><h1 id="product_title" class="left yellow"><?=$title?></h1></p>

				<?php $purchase_enabled = (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1); ?>
                <?php if ($purchase_enabled OR Settings::instance()->get('purchase_disabled_show_prices')): ?>
                    <div id="product_price" class="left row">
                        <?php
                            if($display_offer != 0){
                                echo '<p><span class="left strong">Price:</span> <span class="grey line-through">€'.$price.'</span></p>';
                                echo '<p><span class="left strong">Offer Price:</span> '
                                     .'<span class="left strong yellow" data-product_price="'. $offer_price .'" id="final_price">€'.$offer_price.'</span></p>';
                            }
                            else{
                                echo '<p><span class="left strong">Price:</span>'
                                     .'<span class="left strong yellow" data-product_price="'. $price .'" id="final_price">€'.$price.'</span>'
                                     .'</p>';
                                echo '<p><span class="left strong">Offer Price:</span> <span class="left grey">n/a</span></p>';
                            }
                        ?>
                    </div>

                    <div id="product_qty" class="left row">
                        <p>
                            <span class="left strong">Quantity:</span>
                            <input type="text" id="qty" value="1" class="txtbox left validate[required,custom[onlyNumberSp],min[1]]" name="qty" size="2" />
                        </p>
                    </div>
                <?php endif; ?>

				<div id="product_options" class="left row">
					<?php $option_groups = Model_Option::get_all_groups_by_product($id);
						foreach($option_groups AS $index => $option_group){
							$option_group_shot_name = explode(" ",$option_group['option_group']);
							$options = Model_Option::get_options_by_field('group', $option_group['option_group']);
							echo '<label for="',$option_group['option_group'],'" class="text_label">',$option_group_shot_name[0],': </label>';
							echo '<div class="prod_option_item left">';
							echo '<select id="option_',$index,'" name="',$option_group['option_group'],'" class="prod_option';
							if(!empty($option_group['required'])){
								echo ' validate[dropdown,min[1]]';
							}
							echo '" onchange="updateProductOptionPrice()">';
										echo '<option value="0">Choose...</option>';
										foreach($options as $option){
											$label = ((float)$option['price'] > 0) ? $option['label'] ." + &euro;". $option['price'] : $option['label'];
								echo '<option value="',$option['id'],'" data-option_price="'. $option['price'].'"'.(($option['default'] == 1) ? ' selected="selected"' : '').'>',$label, '</option>';
							}
							echo '</select>';
							echo '</div>';
						}
					?>
				</div>

				<div id="product_purchase_buttons" class="left row">
                    <?php if (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1): ?>
                        <div id="add_to_cart_button" class="left product_btn" onclick="validateQty(<?=$id?>, $('#qty').val(), getOptions());">
                            <span class="left btn_big_left_bg">&nbsp;</span>
                            <span class="left btn_big_mid_bg">Add to <span class="strong">Cart &raquo;</span></span>
                            <span class="left btn_big_right_bg">&nbsp;</span>
                        </div>
                        <div id="purchase_button" class="left product_btn" onclick="validateQty_and_checkout(<?=$id?>, $('#qty').val(), getOptions());">
                            <span class="left btn_big_left_bg">&nbsp;</span>
                            <span class="left btn_big_mid_bg">Buy <span class="strong">Now &raquo;</span></span>
                            <span class="left btn_big_right_bg">&nbsp;</span>
                        </div>
                    <?php endif; ?>
					<div id="continue_shopping" class="left product_btn">
						<a href="/<?= $products_plugin_page ?>">CONTINUE SHOPPING &raquo;</a>
					</div>
					<br/><div class="left successful message_area" style="display:none;"></div>
				</div>

				<?=((isset($size_guide) AND trim($size_guide) != '') ? '<div id="product_sizeguide" class="left row"><a href="'.URL::base().$size_guide.'">View the Size Guide</a></div>' : '')?>
			 </div>
		</div>

		<div class="row left">
			<div id="messageBar" style="clear: both;"></div>
		</div>

		<div id="product_description" class="row left">
			<h2>Product Information:</h2>
			<?=$description?>
		</div>

		<? if (isset($related_to) AND count($related_to) > 0){ ?>
		<div id="products_related" class="row left">
			<h2>You might also like:</h2>
			<?
			echo Model_Product::render_related_products_html($related_to);
			?>
		</div>
		<? }?>
	</form>
</div>
