<li>
	<div class="feed_item_tile">
		<?php if (Settings::instance()->get('show_news_date') == 'TRUE'): ?>
        	<?php  if ($feed_item_data['date_publish'] != '0000-00-00 00:00:00' AND ! is_null($feed_item_data['date_publish'])): ?>
            	<div class="feed_item_date black"><?= date('jS M Y', strtotime($feed_item_data['date_publish'])) ?></div>
			<?php elseif ($feed_item_data['event_date'] != '0000-00-00 00:00:00' AND ! is_null($feed_item_data['event_date'])) : ?>
            	<div class="feed_item_event_date black"><?= date('jS M Y', strtotime($feed_item_data['event_date'])) ?></div>
            <?php endif; ?>
        <?php endif; ?>
		<div class="feed_item_image">
            <?php if( ! empty($feed_item_data['image'])): ?>
                <img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'_thumbs/'.$feed_item_data['image'], 'news') ?>"
                     alt="<?= (isset($feed_item_data['alt_text']))   ? $feed_item_data['alt_text']   : ''; ?>"
                     alt="<?= (isset($feed_item_data['title_text'])) ? $feed_item_data['title_text'] : ''; ?>"
                     class="item_image" />
            <?php endif; ?>
        </div>
		<div class="feed_item_title"><?php echo $feed_item_data['title'];?></div>
		<div class="feed_item_summary">
			<p><?= substr(strip_tags($feed_item_data['summary']), 0, 50).'... '; ?></p>
			<a class="read-more" href="/news/<?= $feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['news_url'];?>"><?=__('Read More')?></a>
		</div>
	</div>

</li>