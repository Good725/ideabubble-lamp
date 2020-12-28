<? if ($out_of_stock == 1): ?>
<!--    <div id="checkout_messages"><div class="checkout_message_error"><a class="close">×</a>Error adding the product, please try again</div></div>-->
    <? echo IbHelpers::alert($out_of_stock_msg != '' ? $out_of_stock_msg : 'This Product is Currently Out Of Stock.', 'warning') ?>
<? endif; ?>
<!-- products-breadcrumbs -->
<div id="breadcrumb-nav">
    <?=IbHelpers::breadcrumb_navigation();?>
</div>
<form id="ProductDetailsForm" name="ProductDetailsForm">
<div id="product">
    <div id="product_image">
        <?php
        if(isset($images[0])){
            echo '<img src="',Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[0], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/')),'" class="left" id="prodImage_0" alt="',$images[0],'">';
        }
        ?>
		<div class="proThum-area left">
			<?php
				for($i = 1; $i < count($images); $i++){
					$third_img = '';
					if($i % 3 == 0){
						$third_img = ' third_img';
					}
					echo '<img onclick="imageTrick(\'',$i,'\')" src="',Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[$i], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/')),'" alt="',$images[$i],'" class="productAltImage left',$third_img,'" id="prodImage_',$i,'">';
				}
			?>
		</div>
    </div>
    <div id="product_details">
        <div id="product_price" class="bold_text">
            <div class="float_left">
                Price
            </div>
            <div class="float_right white">
				<?php
					if($display_price != 0){
						if($display_offer != 0){
							echo '<span class="line-through">€',$price,'</span>';
							echo '<span class="grey">&nbsp;Offer Price:&nbsp;</span>';
							echo '<span class="" data-product_price="'. $offer_price .'" id="final_price">€',$offer_price,'</span>';
						}
						else{
							echo '<span class="text left purple" data-product_price="'. $price .'" id="final_price">€',$price,'</span>';
						}
					}
				?>
            </div>
        </div>
        <div id="product_description">
            <h1>PRODUCT DETAILS</h1>
            <p>
                <?=$description?>
            </p>
            <? if ($out_of_stock != 1): ?>
			<?php
			$option_groups = Model_Option::get_all_groups_by_product($id);
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
                                if($option['publish'] == '0'){
                                    continue;
                                }
                                $label = ((float)$option['price'] > 0) ? $option['label'] ." + &euro;". $option['price'] : $option['label'];
					echo '<option value="',$option['id'],'" data-option_price="'. $option['price'] .'"'.(($option['default'] == 1) ? ' selected="selected"' : '').'>',$label, '</option>';
				}
				echo '</select>';
				echo '</div>';

			}
			?>
			<span class="text_label left clear_left grey">Quantity:</span>
			<input type="text" id="qty" value="1" class="txtbox left validate[required,custom[onlyNumberSp],min[1]]" name="qty">
            <? endif ?>
        </div>
        <div id="product_purchase">
            <? if ($out_of_stock != 1): ?>
            <div id="add_to_cart_button" onclick="validateQty(<?=$id?>, $('#qty').val(), getOptions());">
                Add to Cart >>
            </div>
            <div id="purchase_button" onclick="validateQty(<?=$id?>, $('#qty').val(), getOptions());">
                Buy Now >>
            </div>
            <? endif ?>
            <div id="continue_shopping">
                <a href="/products.html">CONTINUE SHOPPING >></a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?=URL::site()?>assets/default/js/product_details.js"></script>
</form>