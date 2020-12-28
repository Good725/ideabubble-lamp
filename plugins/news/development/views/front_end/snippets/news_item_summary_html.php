<div class="summary_item_tile">
    <header>
        <h2 class="summary_item_title light" style="display: inline;"><?php echo $item_data['title'];?></h2>
        <p class="summary_item_event_date dark" style="display: inline; margin-left: 15px;"><?php echo (!empty($item_data['event_date']) AND Settings::instance()->get('show_news_date') == 'TRUE')? '<time>'.date('F dS, Y', strtotime($item_data['event_date'])).'</time>' : ''?></p>
    </header>
	<div class="summary_item_summary">
		<p>
			<?= $item_data['summary'] ?>
            <?php if (Settings::instance()->get('news_read_more') != 'FALSE'): ?>
                <a class="read-more strong" href="/news/<?php echo $item_data['category'].DIRECTORY_SEPARATOR.$item_data['news_url'];?>"><?=__('Read More') ?></a>
            <?php endif; ?>
		</p>
	</div>
</div>