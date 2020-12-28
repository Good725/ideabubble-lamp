<?php
if ((empty($item_data['image']) OR ! file_exists(DOCROOT.'media/photos/news/'.$item_data['image'])) AND file_exists(DOCROOT.'media/photos/news/news_default.png'))
{
    $item_data['image'] = 'news_default.png';
}
$show_date = ( ! empty($item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE');
?>

<div class="news_item_tile">
	<?php if ($item_data['category'] != 'News'): ?>
		<ol class="news_breadcrumbs">
			<li><a href="/news.html">News</a></li>
			<li><a href="/news/<?= $item_data['category'].'.html' ?>"><?= $item_data['category'] ?></a></li>
		</ol>
	<?php endif; ?>
	<header class="item_title">
		<h2><?= $item_data['title'] ?></h2>
		<?php if ($show_date): ?>
			<time class="news_item_date" datetime="<?= date('Y-m-d H:i', strtotime($item_data['event_date'])) ?>"><?= date('F dS, Y', strtotime($item_data['event_date'])) ?></time>
		<?php endif; ?>
	</header>
	<div class="item_image">
		<?php if ( ! empty($item_data['image'])): ?>
			<img
                src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], 'news') ?>"
                alt="<?=   (isset($item_data['alt_text']))   ? $item_data['alt_text']   : ''; ?>"
                title="<?= (isset($item_data['title_text'])) ? $item_data['title_text'] : ''; ?>"
                class="item_image" />
        <?php endif; ?>
	</div>
	<div class="news_item_content"><?=$item_data['content']?></div>
	<a href="/news/<?php echo $item_data['category'] . '.html';?>" class="return_link">Return back to <?=ucfirst($item_data['category'])?></a>
</div>