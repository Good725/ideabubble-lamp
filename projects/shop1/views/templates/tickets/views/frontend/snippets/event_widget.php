<?php $dates = array_unique(array_filter(explode(';', $event['dates']))); ?>

<div class="widget widget--event upcoming-events-panel">
	<div class="widget-body clearfix">
		<div class="columns small-12 medium-3">
			<a href="/event/<?= $event['url'] ?>" tabindex="-1">
				<?php if ($event['image_media_id']): ?>
					<img src="<?=$event['image_media_url'] ?>" />
				<?php else: ?>
					<img src="/shared_media/<?= Kohana::$config->load('config')->project_media_folder ?>/media/photos/events/no_image_available.png" />
				<?php endif; ?>
			</a>
		</div>
		<div class="columns small-12 medium-6">
			<a href="/event/<?=$event['url']?>" tabindex="-1"><h4 class="text-primary"><?=html::entities($event['name'])?></h4></a>

			<?php if ($dates): ?>
				<p>
					<?php $currentYear = date('Y') ?>
					<?php foreach ($dates as $key => $date): ?>
						<?php
							$date = explode('|', $date);
							$startDate = $date[0];
							$endDate   = $date[1];

							if ($currentYear !== date('Y', strtotime($startDate)))
								$startYear = ' Y';
							else
								$startYear = '';

							if ($currentYear !== date('Y', strtotime($endDate)))
								$endYear = ' Y';
							else
								$endYear = '';
						?>
						<?= date("F j$startYear, g:ia", strtotime($startDate)) . ($endDate ? ' - ' . date("F j$endYear, g:ia", strtotime($endDate)) : '') ?><?= $key < count($dates) - 1 ? '<br />' : '' ?>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>
			<?php if ( ! empty($event['venue']) OR ! empty($event['city'])): ?>
				<address>
					<?php if ( ! empty($event['venue'])): ?>
						<span class="line"><?= $event['venue'] ?></span>
					<?php endif; ?>
					<?php if ( ! empty($event['city'])): ?>
						<span class="line"><?= $event['city'] ?></span>
					<?php endif; ?>
				</address>
			<?php endif; ?>
		</div>
        <div class="columns small-12 medium-3 text-center ticket_section">
            <?php if (isset($event['allocated']) AND isset($event['sold']) AND ($event['allocated'] - $event['sold'] <= 0)): ?>
                <a class="button" href="/event/<?= $event['url'] ?>">
                    <?= __('Sold Out') ?>
                </a>
            <?php elseif ($event['status'] == Model_Event::EVENT_STATUS_SALE_ENDED): ?>
                <a class="button" href="/event/<?= $event['url'] ?>">
                    <?= __('Sales Ended') ?>
                </a>
            <?php else: ?>
                <a class="button primary get_tickets" href="/event/<?= $event['url'] ?>">
                    <span class="sprite sprite-ticket"></span>
                    <?= __('Get tickets') ?>
                </a>
            <?php endif; ?>
        </div>
	</div>
</div>