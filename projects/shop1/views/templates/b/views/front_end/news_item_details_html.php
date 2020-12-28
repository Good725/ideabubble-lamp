<?php
if ((empty($item_data['image']) OR ! file_exists(DOCROOT.'media/photos/news/'.$item_data['image'])) AND file_exists(DOCROOT.'media/photos/news/news_default.png'))
{
    $item_data['image'] = 'news_default.png';
}
?>

<div class="news-details">
    <div class="news-details-body">
        <h2 class="news-details-title"><?=( ! empty($item_data['seo_title'])) ? $item_data['seo_title'] : $item_data['title']?></h2>

        <?php if ( ! empty($item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE'): ?>
            <div class="news-details-date"><?= date('F dS, Y', strtotime($item_data['event_date'])) ?></div>
        <?php endif; ?>

        <div class="news-details-summary"><?=$item_data['summary']?></div>

        <?php if( ! empty($item_data['image'])): ?>
            <div class="news-details-image">
                <img
                    src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news') ?>"
                    alt="<?=   (isset($item_data['alt_text']))   ? $item_data['alt_text']   : ''; ?>"
                    title="<?= (isset($item_data['title_text'])) ? $item_data['title_text'] : ''; ?>"
                    class="item_image" />
            </div>
        <?php endif; ?>

        <div class="news-details-content"><?=$item_data['content']?></div>
    </div>

    <div class="news-details-footer">
        <?php if (isset($item_data['prev']) and ! empty($item_data['prev']->id)): ?>
            <a class="news-details-prev" href="/news/<?= $item_data['category'] ?>/<?= $item_data['prev']->title ?>"><?= __('Previous Page') ?></a>
        <?php endif; ?>

        <?php if (isset($item_data['next']) and ! empty($item_data['next']->id)): ?>
            <a class="news-details-next" href="/news/<?= $item_data['category'] ?>/<?= $item_data['next']->title ?>"><?= __('Next Page') ?></a>
        <?php endif; ?>


        <a href="/news/<?= $item_data['category'] ?>" class="news-return_link"><?=__('Return back to Blog')?></a>
    </div>
</div>