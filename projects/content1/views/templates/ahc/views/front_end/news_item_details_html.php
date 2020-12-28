<?php
if ((empty($item_data['image']) OR ! file_exists(DOCROOT.'media/photos/news/'.$item_data['image'])) AND file_exists(DOCROOT.'media/photos/news/news_default.png'))
{
	$item_data['image'] = 'news_default.png';
}
?>

<section>
	<div class="panel post">
		<h1><?= $item_data['title'] ?></h1>
		<?php if ( ! empty($item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE'): ?>
			<small><i><time datetime="<?= date('Y-m-d', strtotime($item_data['event_date'])) ?>"><?= date('F jS, Y', strtotime($item_data['event_date'])) ?></time></i></small>
		<?php endif; ?>

		<?= $item_data['content'] ?>

		<?php if( ! empty($item_data['image'])): ?>
			<img
				src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news') ?>"
				alt="<?=   (isset($item_data['alt_text']))   ? $item_data['alt_text']   : ''; ?>"
				title="<?= (isset($item_data['title_text'])) ? $item_data['title_text'] : ''; ?>"
				/>
		<?php endif; ?>
	</div>
</section>