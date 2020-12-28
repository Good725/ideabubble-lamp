<?php
$news_items = isset($news_items) ? $news_items : ORM::factory('News_Item')->where('media_type', '=', $media_type)->find_all_frontend();
$total      = isset($total) ? $total : count($news_items);

$filter_media_types = ORM::factory('News_Item')->get_enum_options('media_type');

// Use the "items per page" setting for articles. Use 3 for everything else.
if (empty($media_type) || $media_type == 'Article') {
    $items_per_page = (int) Settings::instance()->get('news_feed_item_count');
    $items_per_page = $items_per_page ? $items_per_page : 3;
} else {
    $items_per_page = 3;
}


$pagination_count = ceil($total / $items_per_page);
$current_page     = isset($current_page) ? $current_page : 1;

$theme        = Model_Engine_Theme::get_current_theme();
$bg_class     = 'bg-white';
$text_class   = 'text-black';
$button_class = 'text-primary';

switch ($media_type) {
    case 'Video':   $button_text = __('Watch now'); break;
    case 'Podcast': $button_text = __('Listen now'); break;
    default:        $button_text = __('Read more'); break;
}
?>

<div
    class="row gutters d-md-flex flex-wrap"
    data-media_type="<?= $media_type ?>"
    data-limit="<?= $items_per_page ?>"
    data-total="<?= $total ?>"
>
    <?php for ($i = 0; $i < $items_per_page && $i < count($news_items); $i++): ?>
        <?php $item = $news_items[$i]; ?>
        <div class="news-category-column col-sm-4 d-md-flex">
            <?php
            echo View::factory('front_end/snippets/news_item_embed')
                ->set(compact('item', 'bg_class', 'text_class', 'button_class', 'button_text'))
            ?>
        </div>
    <?php endfor; ?>
</div>

<?php if ($pagination_count > 1): ?>
    <?= View::factory('front_end/pagination')->set([
        'current_page' => $current_page,
        'total_pages' => $pagination_count
    ]) ?>

<?php endif; ?>
