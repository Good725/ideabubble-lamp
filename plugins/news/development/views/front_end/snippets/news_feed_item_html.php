<?php
if ((empty($feed_item_data['image']) OR ! file_exists(DOCROOT.'media/photos/news/'.$feed_item_data['image'])) AND file_exists(DOCROOT.'media/photos/news/news_default.png'))
{
    $feed_item_data['image'] = 'news_default.png';
}
?>
<li>
	<div class="feed_item_tile">
        <div class="item_image">
            <?php if ( ! empty($feed_item_data['image'])): ?>
                <img
                    src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$feed_item_data['image'], 'news') ?>"
                    alt="<?=   (isset($feed_item_data['alt_text']))   ? $feed_item_data['alt_text']   : ''; ?>"
                    title="<?= (isset($feed_item_data['title_text'])) ? $feed_item_data['title_text'] : ''; ?>"
                    class="item_image"
                    />
            <?php endif; ?>
        </div>
		<span class="feed_item_title light"><?= $feed_item_data['title'];?></span>
		<span class="feed_item_event_date dark"><?= ( ! empty($feed_item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE')? date('F dS, Y', strtotime($feed_item_data['event_date'])) : ''?></span>
		<div class="feed_item_summary">
 			<p>
				<?= $feed_item_data['summary'] ?>...
                <?php if (Settings::instance()->get('news_read_more') != 'FALSE'): ?>
                    <a class="read-more" href="/news/<?php echo $feed_item_data['category'].DIRECTORY_SEPARATOR.$feed_item_data['news_url'];?>"><?=__('Read More')?> &raquo;</a>
                <?php endif; ?>
			</p>
		</div>
	</div>

</li>