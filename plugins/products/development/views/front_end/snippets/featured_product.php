<?php $product_url = ($product_data['url_title'] != '') ? $product_data['url_title'] : urlencode(str_replace(' ', '-', trim($product_data['title']))); ?>
<div id="featured_<?=$product_data['id']?>" class="left product_featured">
	<a href="<?= URL::base().Model_Product::get_products_plugin_page().DIRECTORY_SEPARATOR.$product_url ?>" title="View product: '<?= $product_data['title'] ?>' details">
		<?php
		// Check if Product has an Image
		if (!isset($product_data['images'][0]) OR empty($product_data['images'][0]))
		{
			$featured_image = 'not_image_available.jpg';
		}
		else
		{
			// Check if Product has More than 1 image and return a Random one for this snippet
			if (count($product_data['images']) == 1)
			{
				$featured_image = $product_data['images'][0];
			}
			else
			{
				$featured_image = $product_data['images'][array_rand($product_data['images'], 1)];
			}
		}
		// Render the image for this Product
        $src = $product_data['thumb_url'] ? $product_data['thumb_url'] : HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$featured_image, Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR.'_thumbs'));
		echo '<img alt="'.$product_data['title'].'"'
			 	.' class="left featured_image"'
			    .' src="' . $src . '">';
		// Render the Title of this Product
		echo '<div class="left featured_title">'.$product_data['title'].'</div>';
		// Render Product Price fields
		if($product_data['display_price'] != 0 AND $product_data['disable_purchase'] != 1){
			echo '<div class="left featured_details">';
			if($product_data['display_offer'] != 0){
				echo '<span class="left grey line-through">€'.$product_data['price'].'</span>';
				echo '<span class="left strong yellow">€'.$product_data['offer_price'].'</span>';
			}
			else{
				echo '<span class="left strong yellow">€'.$product_data['price'].'</span>';
			}
			echo '</div>';
		}

		// Render Add To Cart Button
		echo '<div id="add_to_cart_button" class="left product_btn">'
					.'<span class="left btn_small_left_bg">&nbsp;</span>'
					.'<span class="left btn_small_mid_bg"><span class="strong">View Details &raquo;</span></span>'
					.'<span class="left btn_small_right_bg">&nbsp;</span>'
			.'</div>';


		// @TODO: add the button
		/*
		 <div id="add_to_cart_button" class="left product_btn" onclick="alert('open Product Details view now')">
			<span class="left btn_small_left_bg">&nbsp;</span>
			<span class="left btn_small_mid_bg"><span class="strong">View Details &raquo;</span></span>
			<span class="left btn_small_right_bg">&nbsp;</span>
		 </div>
		 */
		?>
	</a>
</div>