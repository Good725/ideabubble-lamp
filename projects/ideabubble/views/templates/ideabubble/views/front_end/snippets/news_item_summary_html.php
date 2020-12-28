<?php
$settings_instance = Settings::instance();
$image_url_path    = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'news');
$image_folder_path = PROJECTPATH.'www/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/news/';
?>
<article class="news-feed-item">
    <?php if ($settings_instance->get('images_in_news_feed')): ?>
        <div class="news-feed-image">
            <?php if ( ! empty($item_data['image'])): ?>
                <?php if (file_exists($image_folder_path.'_thumbs/'.$item_data['image'])): ?>
                    <img src="<?= $image_url_path.'_thumbs/'.$item_data['image'] ?>" alt="" />
                <?php else: ?>
                    <img src="<?= $image_url_path.$item_data['image'] ?>" alt="" />
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="news-feed-text">
        <header>
            <h3 class="news-feed-title"><?= $item_data['title'] ?></h3>
            <?php if ( ! empty($item_data['event_date']) AND $settings_instance->get('show_news_date') == 'TRUE'): ?>
                <time class="news-feed-time" datetime="<?= date('Y-m-d', strtotime($item_data['event_date'])) ?>">
                    <span class="news-feed-time-icon">
                        <span class="icon_clock_alt" aria-hidden="true"></span>
                    </span>
                    <?= date('F j, Y', strtotime($item_data['event_date'])) ?>
                </time>
            <?php endif; ?>
        </header>

        <p class="news-feed-summary"><?= trim($item_data['summary']) ?></p>

        <?php if ($settings_instance->get('news_read_more') != 'FALSE'): ?>
            <a class="btn-primary news-feed-read_more" href="/news/<?= $item_data['category'].'/'.$item_data['news_url'] ?>"><?=__('Read more') ?></a>
        <?php endif; ?>
    </div>
</article>
