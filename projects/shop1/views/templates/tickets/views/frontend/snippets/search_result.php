<div class="widget widget--event upcoming-events-panel">
	<div class="widget-body clearfix">
		<div class="columns small-12 medium-3"></div>
		<div class="columns small-12 medium-6">
			<a href="<?= $result['link'] ?>" tabindex="-1"><h4 class="text-primary upcoming_event-title"><?=html::entities($result['label'])?></h4></a>
		</div>
		<div class="columns small-12 medium-3 text-center ticket_section">
			<a class="button primary" href="<?= $result['link'] ?>" style="min-width: 120px;">
				<?= __('View') ?>
			</a>
		</div>
	</div>
</div>