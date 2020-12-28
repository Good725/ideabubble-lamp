<li class="upcoming-event-item">
	<?php if ($feed_item_data['event_date']): ?>
		<?php $date = strtotime($feed_item_data['event_date']) ?>
		<time class="upcoming-event-date" datetime="<?= date('Y-m-d H:i:s', $date) ?>" title="<?= date('d F Y', $date) ?>"">
			<?= date('j', $date) ?><br /><?= date('M', $date) ?>
		</time>
	<?php endif; ?>
	<div class="upcoming-event-content">
		<h4 class="upcoming-event-title">
			<a href="/news/<?= $feed_item_data['category']?>/<?= $feed_item_data['news_url'] ?>" title="<?= $feed_item_data['title'] ?>">
				<?= $feed_item_data['title'] ?>
			</a>
		</h4>
		<div class="upcoming-event-description">
			<?= $feed_item_data['summary'] ?>
		</div>
	</div>
</li>