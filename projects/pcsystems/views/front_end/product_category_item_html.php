<?php
$request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
$request_uri = ($request_uri == '') ? '/home.html' : $request_uri;
$parsed_url = explode('/', trim($_SERVER['SCRIPT_URL'], '/'));
if(isset($parsed_url[0])){
    if(preg_match('/\.html$/i', $parsed_url[0], $matches) == 1){
        $request_uri = str_replace($parsed_url[0], Model_Product::get_products_plugin_page(), $request_uri);
    }
}

if(!isset($image) OR empty($image))
{
    $image = 'no_image_available.jpg';
    $no_image = ' no_image';
}
?>
<div class="thumb_product category<?= isset($no_image) ? $no_image : '' ?> product_featured">
	<div class="thumb_product_image">
		<a title="View category: '<?=$category?>'" href="<?=$request_uri.'/'.str_replace(' ', '-',$category)?>"><?php
			$filename = isset($images[1]) ? $images[1] : 'no_image_available.jpg';
			$local_filepath = DOCROOT . 'media/photos/' . Model_Product::MEDIA_IMAGES_FOLDER . '/';
            $urlpath = URL::Media('media').DIRECTORY_SEPARATOR.'photos'.DIRECTORY_SEPARATOR.Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR;
	        $image = file_exists($local_filepath.'_thumbs/'.$filename) ? $urlpath.'_thumbs/'.$filename : $urlpath.$filename;
            ?>
			<img alt="<?= $category ?>" src="<?= $image ?>" class="featured_image"/>
	  </a>
	</div>
	<div class="thumb_product_info">
		<div class="thumb_product_title">
            <a title="View category: '<?=$category?>'" href="<?=$request_uri.'/'.str_replace(' ', '-',$category)?>">
			<?
            if(strlen($category) >= 48){echo substr($category,0,45)."...";}
            else{echo $category;}
            ?>
             </a>
		</div>
	</div>
    <a title="View category: '<?=$category?>'" href="<?=$request_uri.'/'.str_replace(' ', '-',$category)?>" class="left btn_small_mid_bg gray-btn-a"><span class="strong">View Details »</span></a>
</div>