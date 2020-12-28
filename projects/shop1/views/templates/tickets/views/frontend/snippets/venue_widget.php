<div class="widget widget--event upcoming-events-panel">
	<div class="widget-body clearfix">
		<div class="columns small-12 medium-3">
			<a href="/venue/<?= $venue['name'] ?>" tabindex="-1">
				<?php if ($venue['image_media_id'] > 0): ?>
                	<img class="col-sm-6" src="<?=$venue['image_media_url']?>" alt="" />
				<?php else: ?>
					<img src="/shared_media/<?= Kohana::$config->load('config')->project_media_folder ?>/media/photos/events/no_image_available.png" />
				<?php endif; ?>
			</a>
		</div>
		<div class="columns small-12 medium-6">
			<a href="/venue/<?=$venue['name']?>" tabindex="-1"><h4 class="text-primary upcoming_event-title"><?=html::entities($venue['name'])?></h4></a>
		</div>
		<div class="columns small-12 medium-3 text-center ticket_section">
			<a class="button primary get_tickets" href="/venue/<?= $venue['url'] ?>">
				<span class="sprite sprite-ticket"></span>
				<?= __('Details') ?>
			</a>
		</div>
	</div>
</div>