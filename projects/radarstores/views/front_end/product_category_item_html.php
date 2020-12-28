<div class="thumb_product">
	<?php
		$request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
		$parsed_url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		$parsed_url = preg_replace('/\?.*/', '', $parsed_url);
		if(isset($parsed_url[0])){
			if(preg_match('/\.html$/i', $parsed_url[0], $matches) == 1){
				$request_uri = str_replace($parsed_url[0], Model_Product::get_products_plugin_page(), $request_uri);
			}
		}
	?>
	<div class="thumb_product_image">
		<a title="View category: '<?=$category?>'" href="<?=$request_uri.'/'.str_replace(' ', '-',$category)?>">
			<?php
				if(isset($image)){
					echo '<img alt="',$category,'" src="',Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$image, Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR),'">';
				}
			?>
	  </a>
	</div>
	<div class="thumb_product_info">
		<div class="thumb_product_title">
			<?=$category?>
		</div>
	</div>
</div>