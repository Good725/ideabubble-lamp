<div class="item_news">
    <div class="item_image">
        <?php
        if(!empty($item_data['image'])){
            echo '<img src="'. Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news').'" class="item_image" />';
        }
        ?>
    </div>
	<div class="item_title"><h1><?=$item_data['title']?></h1></div>
	<div class="item_content"><?=$item_data['content']?></div>
	<a href="/news/<?php echo $item_data['category'] . '.html';?>" class="return_link strong">Return back to <?=ucfirst($item_data['category'])?></a>
</div>