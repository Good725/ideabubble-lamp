<section>
	<div class="panel post">
		<a href="/news/<?= $item_data['category'].'/'.$item_data['news_url'] ?>">
			<h1><?= $item_data['title'] ?></h1>
		</a>
		<?php if (trim($item_data['summary'])): ?>
			<p><?= $item_data['summary'] ?> [&#133;]</p>
		<?php endif; ?>

		<?php if ($item_data['event_date'] AND Settings::instance()->get('show_news_date') == 'TRUE'): ?>
			<small><i><time datetime="<?= date('Y-m-d', strtotime($item_data['event_date'])) ?>"><?= date('F jS, Y', strtotime($item_data['event_date'])) ?></time></i></small>
		<?php endif; ?>
	</div>
</section>