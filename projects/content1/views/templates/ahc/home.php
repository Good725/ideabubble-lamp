<?php include 'template_views/header.php'; ?>

	<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo'], FALSE); ?>

	<?php
	$panel_model = new Model_Panels();
	$pages_model = new Model_Pages();
	$home_panels = $panel_model->get_panels('home_content', (Settings::instance()->get('localisation_content_active') == '1'));
	$panel_path  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'panels/');
	?>

	<div class="page-content home-boxes">
		<div class="row">
			<?php foreach ($home_panels as $panel): ?>

				<?php
				$video_panel       = (strpos($panel['text'], '<video') > -1 OR strpos($panel['text'], '<iframe') > -1);
				$news_panel        = (strpos($panel['text'], '{newsfeed') > -1);
				$testimonial_panel = (strpos($panel['text'], 'only-on-active testimonial') > -1);
				if ($panel['link_id'] != '0' AND ! empty($panel['link_id']))
				{
					$page = $pages_model->get_page_data( $panel['link_id'] );
					$panel['link_url'] = (isset($page[0]['name_tag'])) ? '/'.$page[0]['name_tag'] : $panel['link_url'];
				}

				$classes  = $video_panel       ? ' video'            : '';
				$classes .= $panel['image']    ? ' panel-with-image' : '';
				$classes .= $news_panel        ? ' alt'              : '';
				$classes .= $testimonial_panel ? ' success-box'      : '';
				?>

				<div class="medium-<?= ($panel['image'] OR $news_panel) ? 4 : 8 ?> columns">
					<?php if (trim($panel['link_url'])): ?><a href="<?= $panel['link_url'] ?>"><?php endif; ?>
						<div class="panel no-padding<?= $classes?>"<?php if ($panel['image']): ?> style="background-image: url('<?= $panel_path.$panel['image'] ?>');"<?php endif; ?>>
							<?php if ($video_panel): ?>
								<h4 class="video"></h4>
							<?php elseif ($news_panel): ?>
								<h4 class="latest-news"><?= $panel['title'] ?></h4>
							<?php elseif ($testimonial_panel): ?>
								<h4 class="testimonials"><?= $panel['title'] ?></h4>
							<?php else: ?>
								<h4><?= $panel['title'] ?></h4>
							<?php endif; ?>
                            <div class="panel-text">
                                <?= IbHelpers::expand_short_tags($panel['text']) ?>
                            </div>
						</div>

					<?php if (trim($panel['link_url'])): ?></a><?php endif; ?>
				</div>

			<?php endforeach; ?>
		</div>
	</div>

<?php include 'template_views/footer.php'; ?>