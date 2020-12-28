<?php
$image_name      = empty($item_data['image']) ? 'news-placeholder.png' : $item_data['image'];
$media_model     = new Model_Media();
$news_media_path = $media_model->get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'news');
$image_exists    = file_exists($media_model->get_media_absolute_path().'/photos/news/'.$image_name);
$article_url     = '/news/'.$item_data['category'].'/'.$item_data['news_url'];

if ($item_data['summary_has_image']){
    preg_match_all('/<img[^>]+>/', $item_data['summary'], $matched_img_strings, PREG_SET_ORDER);
    foreach($matched_img_strings as $matched_img_string){
        $item_data['summary'] = str_replace($matched_img_string[0],
            '<a href="'.$article_url.'" class="news-result-link news-result-link-override" tabindex="-1">'.$matched_img_string[0].'</a>',
            $item_data['summary']
        );
    }
}
?>

<?php
// Redundant, but necessary to avoid a conflict in a hurry. The above declaration can be removed after the next release.
$image_exists    = file_exists($media_model->get_media_absolute_path().'/photos/news/'.$image_name);
?>

<div class="news-result">
    <div class="news-result-inner">
        <a class="news-result-text news-result-link" tabindex="-1" href="<?=$article_url?>">
            <?php if (!$image_exists && !empty($item_data['event_date'])): ?>
                <span class="news-result-date"><?= date('<\s\p\a\n>M</\s\p\a\n> j<\s\u\p>S</\s\u\p> Y', strtotime($item_data['event_date'])) ?></span>
            <?php endif; ?>

            <h2 class="news-result-title"><?= $item_data['title'] ?></h2>
        </a>

        <?php if ($image_exists): ?>
            <div class="news-result-image">
                <figure>
                    <a class="news-result-link " tabindex="-1" href="<?= $article_url ?>">
                        <img src="<?= $news_media_path.$image_name ?>" alt="<?= $item_data['title_text'] ?>" title="<?= $item_data['alt_text'] ?>"/>
                    </a>

                    <?php if ( ! empty($item_data['event_date'])): ?>
                        <figcaption class="news-result-date"><?= date('<\s\p\a\n>M</\s\p\a\n><\b\r />j<\s\u\p>S</\s\u\p> Y', strtotime($item_data['event_date'])) ?></figcaption>
                    <?php endif; ?>
                </figure>
            </div>
        <?php endif; ?>

        <div class="news-result-text">
            <?php if (!empty($item_data['original_summary'])): ?>
                <div class="news-result-summary"><?= nl2br(htmlspecialchars($item_data['original_summary'])) ?></div>
            <?php endif; ?>

            <a class="news-result-read_more button button--send" href="<?= $article_url ?>"><?= __('Read More') ?></a>
        </div>
    </div>
</div>