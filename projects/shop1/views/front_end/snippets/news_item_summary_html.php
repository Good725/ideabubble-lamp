<div class="summary_item_tile">
	<?php if (Settings::instance()->get('images_in_news_feed')): ?>
		<div class="summary_item_image">
			<?php if ($item_data['image']): ?>
				<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news/_thumbs/') ?>" />
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="summary_item_details">
		<header>
			<h2 class="summary_item_title"><?= $item_data['title'];?></h2>
			<?php if ( ! empty($item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE'): ?>
				<time datetime="<?= date('Y-m-d', strtotime($item_data['event_date'])) ?>"><?= date('F jS, Y', strtotime($item_data['event_date'])) ?></time>
			<?php endif; ?>
		</header>
		<div class="summary_item_summary">
            <?= $item_data['summary'] ?>
            <?php if (Settings::instance()->get('news_read_more') != 'FALSE'): ?>
                <a class="read-more" href="/news/<?= $item_data['category'].'/'.$item_data['news_url'];?>"><?=__('Read More') ?></a>
            <?php endif; ?>
		</div>
	</div>
</div>