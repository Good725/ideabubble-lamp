<?php
$product_url = ($url_title != '') ? $url_title : urlencode(str_replace(' ', '-', trim($title)));
$add_products_page = rtrim($_SERVER['REQUEST_URI'], '/');
$add_products_page = preg_replace('/\?.*/', '', $add_products_page);

if(strpos($_SERVER['REQUEST_URI'], Model_Product::get_products_plugin_page()) == false){
	$add_products_page = (strpos($add_products_page, '/ajax_get_products') !== FALSE) ? '/'.Model_Product::get_products_plugin_page() : $add_products_page;
}
?>
<div class="thumb_product">
	<div class="thumb_product_image">
		<a title="View product: '<?= $title ?>' details" href="<?= $add_products_page.'/'.$product_url ?>">
	        <?php if(isset($images[0])): ?>
	            <img alt="<?= $title ?>" src="<?= HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[0], Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR.'_thumbs')) ?>" />
	        <?php endif; ?>
	    </a>
	</div>
	<div class="thumb_product_info">
		<div class="thumb_product_title">
			<?=$title?>
		</div>
		<div class="thumb_product_price">
			<?php
			   if($offer_price < $price AND $display_offer == '1'){
				   echo '<span class="light_orange strike">Was €'.$price.'</span> NOW €'.$offer_price;
			   }
			   else{
				   echo '€'.$price;
			   }
			 ?>
		</div>
	</div>
</div>