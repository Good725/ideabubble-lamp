<li>
	<?php $link = '/news/'.$feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['news_url']; ?>
	<div class="feed_item_tile">
		<span class="feed_item_title light"><?= $feed_item_data['title']; ?></span>
		<?php /*  Disabled as NOT Required in the template -=> enable if required ?>
		<span class="feed_item_event_date"><?php echo (!empty($feed_item_data['event_date']))? date('F dS, Y', strtotime($feed_item_data['event_date'])) : ''?></span>
		<?php */ ?>
		<div class="item_image">
			<?php if ( ! empty($feed_item_data['image'])): ?>
				<?php
				$image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'news'.DIRECTORY_SEPARATOR.'_thumbs');
				$image_path = file_exists($image_path) ? $image_path : str_replace('/_thumbs/', '/', $image_path);
				?>
				<a href="<?= $link ?>"><img src="<?= $image_path ?>" class="item_image" /></a>
			<?php endif; ?>
		</div>
		<div class="feed_item_summary">
			<p><?= $feed_item_data['summary'] ?></p>
		</div>
		<?php if (Settings::instance()->get('news_read_more') != 'FALSE'): ?>
			<span class="summary_item_read_more">
				<a class="read-more" href="<?= $link ?>">Read More »</a>
			</span>
		<?php endif; ?>
	</div>
</li>