<?php if (count($events) > 0): ?>
	<div style="height: 258px;overflow-y: scroll;">
		<ul>
			<?php foreach ($events as $event): ?>
				<li class="list-group-item clearfix">
					<div class="calendar-date"><?= $event['Date']  ?></div>
					<div class="calendar-event-name"><?= $event['Title'] ?></div>
					<a class="calendar-link" href="<?= $event['Link'] ?>"><?= __('Read More') ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php else: ?>
	<div class="flex-center">
		<h3 style="color: #000;"><?= __('No data available') ?></h3>
	</div>
<?php endif; ?>