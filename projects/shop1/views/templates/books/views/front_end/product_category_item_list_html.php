<?php
	$request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
	$parsed_url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
	if(isset($parsed_url[0])){
		if(preg_match('/\.html$/i', $parsed_url[0], $matches) == 1){
			$request_uri = str_replace($parsed_url[0], Model_Product::get_products_plugin_page(), $request_uri);
		}
	}
?>
<a title="View category: '<?=__($category)?>'" href="<?=$request_uri.'/'.str_replace(' ', '-',$category)?>">
	<div class="left product_category ll">
		<div class="left product_category_image">

				<?php
					if(!isset($image) OR empty($image)){
						$image = 'no_image_available.jpg';
					}
					echo '<img alt="',__($category),'" src="',Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$image, Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR),'">';
				?>
		</div>
		<div class="left clear_left product_category_info">
			<div class="product_category_title">
				<?
				if(strlen($category) >= 48){echo substr(__($category),0,45)."...";}
				else{echo __($category);}
				?>
			</div>
		</div>
	</div>
</a>
