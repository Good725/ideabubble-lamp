<?php $product_url = ($product_data['url_title'] != '') ? $product_data['url_title'] : str_replace(' ', '-', trim($product_data['title'])); ?>
<div id="related_<?=$product_data['id']?>" class="left product_related">
	<a href="/<?= Model_Product::get_products_plugin_page().DIRECTORY_SEPARATOR.$product_url ?>" title="View product: '<?= $product_url ?>' details">
		<?php
		// Check if Product has an Image
		if ( ! isset($product_data['images'][0]) OR empty($product_data['images'][0]))
		{
            $filepath      = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, '/'));
            $related_image = (file_exists($filepath.'not_image_available.jpg') AND ! file_exists($filepath.'no_image_available.jpg')) ? 'not_image_available.jpg' : 'no_image_available.jpg';
		}
		else
		{
			// Return a randomly chosen image from the product
			$related_image = $product_data['images'][array_rand($product_data['images'], 1)];
		}
		?>
		<img alt="<?= $product_data['title'] ?>" class="related_image"
			 src="<?= HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$related_image, Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR.'_thumbs')) ?>" />
	</a>
</div>