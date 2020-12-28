<div class="summary_item_tile">
	<header>
		<h2 class="summary_item_title"><?= $item_data['title'];?></h2>
		<p class="summary_item_event_date"><?= ( ! empty($item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE')? '<time>'.date('F dS, Y', strtotime($item_data['event_date'])).'</time>' : ''?></p>
	</header>
	<div class="summary_item_summary">
		<p>
			<?= preg_replace('#(<br\s*?/?>\s*?){2,}#', "</p>\n<p>", nl2br($item_data['summary'])); ?>
			<?php if (Settings::instance()->get('news_read_more') != 'FALSE'): ?>
				<a class="button button-primary read-more" href="/news/<?= $item_data['category'].DIRECTORY_SEPARATOR.$item_data['news_url'];?>">Read More</a>
			<?php endif; ?>
		</p>
	</div>
</div>

