
<div class="widget widget--event upcoming-events-panel">
	<div class="widget-body clearfix">
		<div class="columns small-12 medium-3">
			<a href="/organiser/<?= $organiser['first_name'] . ' ' . $organiser['last_name'] ?>" tabindex="-1">
				<?php if ($organiser['profile_media_id'] > 0): ?>
				<?php $organiser['profile_media_url'] = Model_Media::get_path_to_id($organiser['profile_media_id']) ?>
                <img class="org_profile" src="<?=$organiser['profile_media_url']?>" alt="" width="100" height="100"/>
				<?php else: ?>
					<img src="/shared_media/<?= Kohana::$config->load('config')->project_media_folder ?>/media/photos/events/no_image_available.png" />
				<?php endif; ?>
			</a>
		</div>
		<div class="columns small-12 medium-6">
			<a href="/organiser/<?=$organiser['first_name'] . ' ' . $organiser['last_name']?>" tabindex="-1"><h4 class="text-primary"><?=html::entities($organiser['first_name'])?></h4></a>
		</div>
		<div class="columns small-12 medium-3 text-center ticket_section">
			<a class="button primary get_tickets" href="/organiser/<?= $organiser['url'] ?>">
				<span class="sprite sprite-ticket"></span>
				<?= __('Details') ?>
			</a>
		</div>
	</div>
</div>