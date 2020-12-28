<?php $settings = Settings::instance(); ?>
<div class="summary_item_tile">
	<div class="summary_item_details">
		<header>
			<h2 class="summary_item_title"><?= $item_data['seo_title'] ? htmlentities($item_data['seo_title']) : $item_data['title'] ?></h2>
			<?php if ( ! empty($item_data['event_date']) AND $settings->get('show_news_date') == 'TRUE'): ?>
				<time datetime="<?= date('Y-m-d', strtotime($item_data['event_date'])) ?>"><?= date('F jS, Y', strtotime($item_data['event_date'])) ?></time>
			<?php endif; ?>
		</header>

        <?php if ($settings->get('images_in_news_feed')): ?>
            <div class="summary_item_image">
                <?php if ($item_data['image']): ?>
                    <img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news/') ?>" />
                <?php endif; ?>
            </div>
        <?php endif; ?>

		<div class="summary_item_summary">
			<p>
				<?= $item_data['summary'] ?>
			</p>

            <?php if ($settings->get('news_read_more') != 'FALSE'): ?>
                <a class="news-read_more" href="/news/<?= $item_data['category'].'/'.$item_data['news_url'];?>"><?=__('Read More') ?></a>
            <?php endif; ?>
		</div>
	</div>
</div>