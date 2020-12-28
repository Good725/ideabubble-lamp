<li>
	<div class="feed_item_tile">
        <span class="feed_item_event_date dark"><?= ( ! empty($feed_item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE')? date('F dS, Y', strtotime($feed_item_data['event_date'])) : ''?></span>
		<div class="feed_item_image">
            <?php if ( ! empty($feed_item_data['image'])): ?>
                <img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'_thumbs/'.$feed_item_data['image'], 'news') ?>"
                     alt="<?= (isset($feed_item_data['alt_text']))   ? $feed_item_data['alt_text']   : ''; ?>"
                     title="<?= (isset($feed_item_data['title_text'])) ? $feed_item_data['title_text'] : ''; ?>"
                     class="item_image" />
            <?php endif; ?>
        </div>
		<div class="feed_item_title"><?php echo $feed_item_data['title'];?></div>
		<div class="feed_item_summary">
			<p><?= substr(strip_tags($feed_item_data['summary']), 0, 50).'... '; ?></p>
			<a class="button button-primary read-more" href="/news/<?php echo $feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['news_url'];?>">Read More</a>
		</div>
	</div>
</li>