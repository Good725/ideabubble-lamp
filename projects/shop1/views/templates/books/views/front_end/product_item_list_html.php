<?php
$product_url = ($url_title != '') ? $url_title : str_replace(' ', '-', trim($title));
$add_products_page = rtrim($_SERVER['REQUEST_URI'], '/');
$add_products_page = preg_replace('/\?.*/', '', $add_products_page);
if (strpos($_SERVER['REQUEST_URI'], Model_Product::get_products_plugin_page()) == FALSE)
{
	$add_products_page = '/'.Model_Product::get_products_plugin_page();
}
?>
<a title="View product: '<?=$title?>' details" href="<?=$add_products_page.'/'.$product_url?>">
	<div class="left product_item_list listview">
		<div class="left product_item_list_image">
			<?php
				if(!isset($images[0]) OR empty($images[0])){
					$images[0] = 'no_image_available.jpg';
				}

				echo '<img alt="'.__($title).'" src="'.HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$images[0], Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR)).'">';
			?>
		</div>
		<div class="left product_item_list_info">
			<div class="product_item_list_title"><?= $truncated_title ?></div>
            <?php if (Settings::instance()->get('product_enquiry') != 1 AND $display_price != 0 AND $disable_purchase != 1): ?>
                <div class="product_item_list_price">
                    <?php
                       if ($offer_price < $price AND $display_offer == '1'){
                           echo '<span class="grey">Was €<span class="line-through">'.$price.'</span></span> <span class="yellow strong">NOW €'.$offer_price.'</span>';
                       }
                       else{
                           echo '<span class="yellow strong">€'.$price.'</span>';
                       }
                     ?>
                </div>
            <?php endif; ?>
		</div>
	</div>
</a>
