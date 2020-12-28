<div class="item_tile">
	<div class="item_image">
		<?php
			if(!empty($item_data['image'])){
				echo '<img src="'. Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'testimonials').'" class="item_image" />';
			}
		?>
	</div>
	<div class="item_content"><?=$item_data['content']?></div>
	<div class="item_content signature"><?=$item_data['item_signature']?></div>
	<div class="item_content signature"><?=$item_data['item_company']?></div>
	<a href="/testimonials.html/<?php echo $item_data['category'].DIRECTORY_SEPARATOR;?>" class="return_link strong"><?=__('Return back to')?> <?=ucfirst($item_data['category'])?></a>
</div>