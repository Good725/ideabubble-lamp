<?php $file_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', Model_Product::MEDIA_IMAGES_FOLDER); ?>
<div class="showcased-product">
	<header>
		<h3><?= $product['title'] ?></h3>
		<span class="subtitle"><a href="/products.html/<?= $product['url_title'] ?>" title="<?= $product['title'] ?>"><?= strtok(str_replace('</p>', '', str_replace('<p>', '', $product['brief_description'])), "\n"); ?></a></span>
	</header>
	<?php if (isset($product['images'][0])): ?>
		<a href="/products.html/<?= $product['url_title'] ?>">
			<img alt="<?= $product['title'] ?>" title="<?=$product['title'] ?>" src="<?= $file_path.'/'.$product['images'][0] ?>" />
		</a>
	<?php endif; ?>
</div>