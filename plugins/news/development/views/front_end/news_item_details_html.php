<?php
if ((empty($item_data['image']) OR ! file_exists(DOCROOT.'media/photos/news/'.$item_data['image'])) AND file_exists(DOCROOT.'media/photos/news/news_default.png'))
{
    $item_data['image'] = 'news_default.png';
}
?>

<div class="item_tile">
	<div class="item_title"><h2><?=$item_data['title']?></h2></div>
	<div class="item_event_date"><?=(!empty($item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE')? date('F dS, Y', strtotime($item_data['event_date'])) : ''?></div>
	<div class="item_summary"><?=$item_data['summary']?></div>
	<div class="item_image">
		<?php if( ! empty($item_data['image'])): ?>
			<img
                src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news') ?>"
                alt="<?=   (isset($item_data['alt_text']))   ? $item_data['alt_text']   : ''; ?>"
                title="<?= (isset($item_data['title_text'])) ? $item_data['title_text'] : ''; ?>"
                class="item_image" />
        <?php endif; ?>
	</div>
	<div class="item_content"><?=$item_data['content']?></div>
	<div class="item_content"></div>
	<a href="/news/<?= $item_data['category'] ?>" class="return_link strong"><?=__('Return back to')?> <?=ucfirst($item_data['category'])?></a>
</div>