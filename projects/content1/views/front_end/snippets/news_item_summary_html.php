<div class="summary_item_tile">
    <header>
        <h2 class="summary_item_title"><?= $item_data['title'];?></h2>
        <p class="summary_item_event_date"><?= ( ! empty($item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE')? '<time>'.date('F dS, Y', strtotime($item_data['event_date'])).'</time>' : ''?></p>
    </header>
	<div class="summary_item_summary">
		<p>
			<?= $item_data['summary'] ?>
            <?php if (Settings::instance()->get('news_read_more') != 'FALSE'): ?>
                <a class="read-more strong" href="/news/<?php echo $item_data['category'].DIRECTORY_SEPARATOR.$item_data['news_url'];?>">Read More &raquo;</a>
            <?php endif; ?>
		</p>
		<?php if (isset($item_data['image']) AND ! empty($item_data['image'])): ?>
			<?php
			$src = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news/_thumbs');
			$src = file_exists($src) ? $src : str_replace('/_thumbs/', '/', $src);
			?>

			<img src="<?= $src ?>" alt="<?= $item_data['alt_text'] ?>" title="<?= $item_data['title_text'] ?>" />
		<?php endif; ?>
	</div>
</div>