<?php
if (isset($_GET['url']))
{
	$request_uri = $_GET['url'];
}
else
{
	$request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
    $request_uri = preg_replace('/\?.*/', '', $request_uri);
	$request_uri = ($request_uri == '') ? '/home.html' : $request_uri;
	$parsed_url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
	if(isset($parsed_url[0])){
		if(preg_match('/\.html$/i', $parsed_url[0], $matches) == 1){
			$request_uri = str_replace($parsed_url[0], Model_Product::get_products_plugin_page(), $request_uri);
		}
	}
}
// todo: move to the model file
$image_url_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', Model_Product::MEDIA_IMAGES_FOLDER);
$image_folder_path = PROJECTPATH.'www/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/'.Model_Product::MEDIA_IMAGES_FOLDER.'/';

$has_image = true;
if (empty($image)) {
    $image = 'no_image_available.jpg';
    $has_image = false;
}

$thumb_exists = file_exists($image_folder_path.'_thumbs/'.$image);
$thumb_width = 0;

if ($thumb_exists) {
    $thumb_size = getimagesize($image_folder_path.'_thumbs/'.$image);
    $thumb_width = isset($thumb_size[0]) ? $thumb_size[0] : 0;
}
?>
<div class="thumb_product category<?= $has_image ? '' : ' no_image' ?>">
    <div class="thumb_product_image">
        <a title="View category: '<?=__($category)?>'" href="<?=$request_uri.'/'.str_replace(' ', '-',$category)?>">
            <img alt="" src="<?= ($thumb_exists && $thumb_width >= 200) ? $image_url_path.'_thumbs/'.$image : $image_url_path.$image ?>">
        </a>
    </div>
    <a href="<?=$request_uri.'/'.str_replace(' ', '-',$category)?>" class="thumb_product_info">
        <div class="thumb_product_title">
            <?= (strlen($category) >= 48) ? substr(__($category), 0, 45)."..." : __($category); ?>
        </div>
    </a>
</div>