<?php
$settings_instance = Settings::instance();
?>

<div class="news-page">
    <header class="news-page-heading">
        <h1 class="new-page-title"><?= $item_data['title'] ?></h1>

        <dl class="list-inline">
            <?php if ( ! empty($item_data['author']) AND trim($item_data['author'])): ?>
                <dt><?= __('By') ?></dt>
                <dd><?= trim($item_data['author']) ?></dd>
            <?php endif; ?>

            <?php if ( ! empty($item_data['event_date']) AND $settings_instance->get('show_news_date') == 'TRUE'): ?>
                <?php $time = strtotime($item_data['event_date']); ?>
                <?php if ($time): ?>
                    <dt><?= __('Posted on') ?></dt>
                    <dd><time datetime="<?= date('Y-m-d', $time) ?>"><?= date('d/m/Y', $time) ?></time></dd>
                <?php endif; ?>
            <?php endif; ?>
        </dl>
    </header>

    <div class="news-page-summary"><?= $item_data['summary'] ?></div>

    <?php if ( ! empty($item_data['image'])): ?>
        <?php $image_folder_path = PROJECTPATH.'www/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/news/'; ?>

        <?php if (file_exists($image_folder_path.$item_data['image'])): ?>
            <div class="news-page-image">
                <img
                    src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news') ?>"
                    alt="<?=   (isset($item_data['alt_text']))   ? $item_data['alt_text']   : '' ?>"
                    title="<?= (isset($item_data['title_text'])) ? $item_data['title_text'] : '' ?>"
                    />
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="news-page-content"><?= $item_data['content'] ?></div>

    <div class="news-page-footer">
        <div class="news-page-prev">
            <?php if (isset($item_data['prev']) && ! empty($item_data['prev']->id)): ?>
                <a href="/news/<?= $item_data['category'] ?>/<?= $item_data['prev']->get_url_name() ?>"><?= __('Previous News') ?></a>
            <?php endif; ?>
        </div>

        <div class="news-page-curr">
            <a href="/news/<?= $item_data['category'] ?>" class="btn-primary news-page-return_link"><?=__('Return back to $1', array('$1' => $item_data['category'])) ?></a>
        </div>

        <div class="news-page-next">
            <?php if (isset($item_data['next']) && ! empty($item_data['next']->id)): ?>
                <a href="/news/<?= $item_data['category'] ?>/<?= $item_data['next']->get_url_name() ?>"><?= __('Next News') ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>