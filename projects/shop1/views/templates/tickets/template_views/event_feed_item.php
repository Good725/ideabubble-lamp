<?php

// should be moved to the controller/model
$category = Model_Lookup::get_label('Event Category', $event->category_id);
$topic = Model_Lookup::get_label('Event Topic', $event->topic_id);
?>
<div class="columns small-12 medium-6 large-4">
	<div class="events_feed-item">
		<a href="/event/<?= $event->url ?>" title="<?= $event->name ?>" tabindex="-1">
			<figure class="events_feed-image">
				<?php if ($event->image_media_id): ?>
					<img src="/frontend/events/event_image/<?=$event->id ?>" />
				<?php else: ?>
					<img src="<?= $event->get_image('full_url') ?>" />
				<?php endif; ?>
				<figcaption class="events_feed-date">
					<span><?= date('d', strtotime($event->starts)) ?></span>
					<?= date('M', strtotime($event->starts)) ?>
				</figcaption>
			</figure>
		</a>
		<h5 class="events_feed-title">
			<a href="/event/<?= $event->url ?>"><?= $event->name ?></a>
		</h5>
		<div class="events_feed-description">
			<p><?= $event->venue->name ?></p>
			<?php $city = trim($event->venue->city) ?>
			<?php if ( !empty($city) ): ?>
				<p><?= $city ?></p>
			<?php endif; ?>
		</div>

		<div class="events_feed-tags">
			<ul>
				<?php if ( ! empty($category)): ?>
					<li><?= $category ?></li>
				<?php endif; ?>

				<?php if ( ! empty($topic)): ?>
					<li><?= $topic ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>