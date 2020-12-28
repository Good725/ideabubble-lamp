<?php include 'template_views/header.php'; ?>
<div class="home-panels">
	<div class="row">
		<?php
		$panel_model    = new Model_Panels();
		$pages_model    = new Model_Pages();
		$localize       = (Settings::instance()->get('localisation_content_active') == '1');
		$panel_path     = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'panels/');
		$home_panels    = $panel_model->get_panels('home_content', $localize);
		$side_panels    = $panel_model->get_panels('home_right',   $localize);
		$service_panels = $panel_model->get_panels('home_left',    $localize);
		?>

		<?php foreach ($home_panels as $i => $panel): ?>

			<?php
			if ($panel['link_id'] != '0' AND ! empty($panel['link_id']))
			{
				$page = $pages_model->get_page_data( $panel['link_id'] );
				$panel['link_url'] = (isset($page[0]['name_tag'])) ? '/'.$page[0]['name_tag'] : $panel['link_url'];
			}
			?>

			<div class="medium-4 columns ib-panel">
				<?php if (trim($panel['link_url'])): ?><a href="<?= $panel['link_url'] ?>"><?php endif; ?>

					<h5 class="ib-panel-title"><?= $panel['title'] ?></h5>
					<?php if (trim($panel['image'])): ?>
						<img src="<?= $panel_path.$panel['image'] ?>" alt="" />
					<?php endif; ?>

				<?php if (trim($panel['link_url'])): ?></a><?php endif; ?>

				<div class="ib-panel-caption">
					<?= IbHelpers::expand_short_tags($panel['text']) ?>
				</div>
			</div>

		<?php endforeach; ?>
	</div>
</div>
<div class="row">
	<div class="small-12 columns page-content">
		<?php if (count($side_panels) > 0): ?>
			<div class="small-12 medium-4 columns side-panels">
				<?php foreach ($side_panels as $panel): ?>

					<?php
					if ($panel['link_id'] != '0' AND ! empty($panel['link_id']))
					{
						$page = $pages_model->get_page_data( $panel['link_id'] );
						$panel['link_url'] = (isset($page[0]['name_tag'])) ? '/'.$page[0]['name_tag'] : $panel['link_url'];
					}
					?>

					<div class="ib-panel">
						<?php if (trim($panel['link_url'])): ?><a href="<?= $panel['link_url'] ?>"><?php endif; ?>

							<h5 class="ib-panel-title"><?= $panel['title'] ?></h5>
							<?php if (trim($panel['image'])): ?>
								<img src="<?= $panel_path.$panel['image'] ?>" alt="" />
							<?php endif; ?>

						<?php if (trim($panel['link_url'])): ?></a><?php endif; ?>

						<div class="ib-panel-caption">
							<?= IbHelpers::expand_short_tags($panel['text']) ?>
						</div>
					</div>

				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php if (count($service_panels) > 0): ?>
	<?php $panel_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'panels'); ?>
	<div class="services-section">
		<div class="row">
			<h1 class="services-title">Our Services</h1>

			<div class="services-panels">

				<?php foreach ($service_panels as $service_panel): ?>
					<?php
					if ( ! empty($service_panel['link_id']))
					{
						$page = $pages_model->get_page_data($service_panel['link_id'] );
						$service_panel['link_url'] = (isset($page[0]['name_tag'])) ? '/'.$page[0]['name_tag'] : $service_panel['link_url'];
					}
					?>

					<div class="ib-panel">
						<div class="ib-panel-image">
							<?php if ( ! empty($service_panel['link_url'])): ?>
								<a href="<?= $service_panel['link_url'] ?>" tabindex="0">
									<img class="our-services-image" src="<?= $panel_path.$service_panel['image'] ?>" />
								</a>
							<?php else: ?>
								<img class="our-services-image" src="<?= $panel_path.$service_panel['image'] ?>" />
							<?php endif; ?>
						</div>

						<?php if ( ! empty($service_panel['link_url'])): ?>
							<a href="<?= $service_panel['link_url'] ?>" tabindex="0" class="ib-panel-title"><?= $service_panel['title'] ?></a>
						<?php else: ?>
							<div class="ib-panel-title"><?= $service_panel['title'] ?></div>
						<?php endif; ?>

						<div class="ib-panel-text">
							<?= $service_panel['text'] ?>
						</div>

						<div class="ib-panel-link">
							<?php if ( ! empty($service_panel['link_url'])): ?>
								<a href="<?= $service_panel['link_url'] ?>" class="button button--gold"><?= __('More info') ?></a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<div class="about-block">
    <div class="row" style="height: 100%">
        <div class="about-block-text">
            <?= $page_data['content'] ?>
        </div>
    </div>
</div>
<?php include 'template_views/footer.php'; ?>
