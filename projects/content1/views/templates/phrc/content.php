<?php include 'template_views/header.php'; ?>
<div class="home-panels">
	<div class="row">
		<?php
		$panel_model    = new Model_Panels();
		$pages_model    = new Model_Pages();
		$panel_path     = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'panels/');
		$content_panels = $panel_model->get_panels('content_centre', (Settings::instance()->get('localisation_content_active') == '1'));
		$side_panels    = $panel_model->get_panels('content_right',  (Settings::instance()->get('localisation_content_active') == '1'));
		?>

		<?php foreach ($content_panels as $panel): ?>

			<?php
			if ($panel['link_id'] != '0' AND ! empty($panel['link_id']))
			{
				$page = $pages_model->get_page_data( $panel['link_id'] );
				$panel['link_url'] = (isset($page[0]['name_tag'])) ? '/'.$page[0]['name_tag'] : $panel['link_url'];
			}
			?>

			<div class="medium-4 columns ib-panel">
				<?php if (trim($panel['link_url'])): ?><a href="<?= $panel['link_url'] ?>"><?php endif; ?>

					<h5><?= $panel['title'] ?></h5>
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
	<div class="small-12 medium-12 columns page-content">
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

							<h4 class="ib-panel-title"><?= $panel['title'] ?></h4>
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
		<?= $page_data['content'] ?>
		<?php if ($page_data['layout'] == 'newslisting') echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']); ?>
	</div>
</div>
<?php include 'template_views/footer.php'; ?>
