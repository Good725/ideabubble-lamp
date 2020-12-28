<?php
$link      = '/news/'.$feed_item_data['category'].'/'.$feed_item_data['news_url'];
$image     = ( ! empty($feed_item_data['image'])) ? Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'news') : '';
$read_more = (Settings::instance()->get('news_read_more') != 'FALSE');
?>
<span class="dummy"></span>
<div class="row news-feed-item">
	<div class="small-12 medium-12 columns news-feed-item-summary-wrapper">
		<h5 class="news-feed-item-title"><?= $feed_item_data['title'] ?></h5>
		<?php if ($image): ?>
			<div class="small-12 medium-6 columns news-feed-item-image-wrapper">
				<a href="<?= $link ?>">
					<img src="<?= $image ?>" />
				</a>
			</div>
		<?php endif; ?>
		<p>
			<?= nl2br($feed_item_data['summary']) ?>
			<?php if ($read_more): ?>
				&hellip; <a href="<?= $link ?>" class="read-more">Read more</a>
			<?php endif; ?>
		</p>
	</div>
</div>