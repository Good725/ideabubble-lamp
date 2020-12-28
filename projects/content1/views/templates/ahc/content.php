<?php include 'template_views/header.php'; ?>
	<div class="page-content">
		<div class="row">
			<div class="small-12 columns">
				<?php $descendants = Model_Pages::get_ancestors($page_data['id']); ?>

				<p class="breadcrumbs">
					<span xmlns:v="http://rdf.data-vocabulary.org/#">
						<span typeof="v:Breadcrumb">
							<a href="<?= $page_data['theme_home_page'] ?>" rel="v:url" property="v:title"><?= Settings::instance()->get('company_title') ?></a>

							<?php foreach ($descendants as $descendant): ?>
								&gt; <a href="/<?= $descendant['name_tag'] ?>"><?= $descendant['title'] ?></a>
							<?php endforeach; ?>

							&gt; <strong class="breadcrumb_last"><?= $page_data['title'] ?></strong>
						</span>
					</span>
				</p>

			</div>
		</div>
		<div class="row">
			<div class="medium-8 columns">
				<?php if (trim($page_data['content'])): ?>
					<section>
						<div class="panel post"><?= trim($page_data['content']) ?></div>
					</section>
				<?php endif; ?>

				<?php
				if (isset($news_listing) AND $news_listing)
				{
					echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
				}
				if (isset($testimonials_listing) AND $testimonials_listing)
				{
					echo Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
				}
				?>

 				<?php
				$related_pages = Model_Pages::get_related_pages($page_data['id']);

				function print_children($item)
				{
					echo '<li><a href="/'.$item['name_tag'].'">'.$item['title'].'</a>';

					if (count($item['children']) > 0)
					{
						echo '<ul class="sub-pages">';
						foreach ($item['children'] as $child)
						{
							print_children($child);
						}
						echo '</ul>';
					}
					echo '</li>';
				}

				?>
				<?php if (count($related_pages) > 0 AND count($related_pages[0]['children']) > 0): ?>
					<div class="panel">
						<h2><?= __('Related Pages') ?></h2>
						<ul class="related-pages">
							<?php foreach ($related_pages as $related_page) print_children($related_page); ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
			<div class="medium-4 columns">
				<aside>
					<?php
					$panel_model = new Model_Panels();
					$pages_model = new Model_Pages();
					$side_panels = $panel_model->get_panels('content_right', (Settings::instance()->get('localisation_content_active') == '1'));
					$panel_path  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'panels/');
					?>

					<?php foreach ($side_panels as $panel): ?>
						<?php
						$video_panel       = (strpos($panel['text'], '<video') > -1 OR strpos($panel['text'], '<iframe') > -1);
						$news_panel        = (strpos($panel['text'], '{newsfeed') > -1);
						$testimonial_panel = (strpos($panel['text'], '{testimonialsfeed') > -1);
						if ($panel['link_id'] != '0' AND ! empty($panel['link_id']))
						{
							$page = $pages_model->get_page_data( $panel['link_id'] );
							$panel['link_url'] = (isset($page[0]['name_tag'])) ? '/'.$page[0]['name_tag'] : $panel['link_url'];
						}

						$classes  = $video_panel       ? ' video'             : '';
						$classes .= $panel['image']    ? ' panel-with-image'  : '';
						$classes .= $news_panel        ? ' alt'               : '';
						$classes .= $testimonial_panel ? ' testimonial-panel' : '';
						?>

						<div class="panel<?= $classes ?>"<?php if ($panel['image']): ?> style="background-image: url('<?= $panel_path.$panel['image'] ?>');"<?php endif; ?>>
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
					<?php endforeach; ?>
				</aside>
			</div>
		</div>
	</div>
<?php include 'template_views/footer.php'; ?>