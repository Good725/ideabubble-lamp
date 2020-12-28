<div class="item_news">
    <div class="item_image">
        <?php if ( ! empty($item_data['image'])): ?>
            <img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news') ?>"
                 alt="<?=   (isset($item_data['alt_text']))   ? $item_data['alt_text']   : ''; ?>"
                 title="<?= (isset($item_data['title_text'])) ? $item_data['title_text'] : ''; ?>"
                 class="item_image" />
        <?php endif; ?>
    </div>
	<div class="item_title"><h2><?=$item_data['title']?></h2></div>
	<div class="item_content"><?=$item_data['content']?></div>
	<a href="/news/<?php echo $item_data['category'] . '.html';?>" class="return_link strong">Return back to <?=ucfirst($item_data['category'])?></a>
</div>