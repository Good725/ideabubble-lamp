<?php
// The sidebar is on the opposite side than the "content", which is what the setting is for.
$sidebar_location = (Settings::instance()->get('content_location') == 'left') ? 'right' : 'left';
?>

<aside class="sidebar sidebar--<?= $sidebar_location ?>">
	<div class="sidebar-section sidebar-section--calendar">
		<h2><?= __('Calendar') ?></h2>

		<div id="sidebar-calendar"></div>
	</div>

	<?php $news = Model_News::get_all_items_front_end(null, 'news', Settings::instance()->get('news_feed_item_count')); ?>
	<?php if (count($news)): ?>
		<div class="sidebar-section sidebar-section--news">
			<h2><?= __('News') ?></h2>
			<ul class="sidebar-news-list">
				<?php foreach ($news as $news_item): ?>
					<li>
						<a class="sidebar-news-link" href="/news.html/<?= $news_item['category'] ?>/<?= $news_item['news_url'] ?>"><?= $news_item['title'] ?></a>
						<p><?= $news_item['summary'] ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</aside>