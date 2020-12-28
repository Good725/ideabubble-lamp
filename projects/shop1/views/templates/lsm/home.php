<?php
$banner_search = TRUE;
include 'template_views/header.php';
$course_categories = Model_Categories::get_all_published_categories();
?>

<?php $news = Model_News::get_all_items_front_end(); ?>
<?php if ( ! empty($news)): ?>
	<section class="news-section">
		<div class="row">
			<div class="swiper-container" id="news-slider">

				<a class="news-slider-title-link" href="/news.html">
					<h2 class="news-slider-title"><?= __('News and Events') ?></h2>
				</a>

				<div class="swiper-wrapper">
					<?php foreach ($news as $news_item): ?>
						<div class="swiper-slide">
							<p class="news-slider-summary"><?= $news_item['summary'] ?></p>
							<a class="news-slider-link" href="/news/<?= $news_item['category'] ?>/<?= $news_item['title'] ?>"><?= __('Read more') ?></a>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="swiper-button-next"></div>
				<div class="swiper-button-prev"></div>

				<div class="swiper-pagination"></div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
$panel_model = new Model_Panels();
$home_panels = $panel_model->get_panels('home_content', (Settings::instance()->get('localisation_content_active') == '1'));
?>
<?php if (count($home_panels) > 0): ?>
	<?php $panel_path = $panel_path  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'panels/'); ?>
	<section class="panels-section">
		<div class="row">
			<div class="panels-feed panels-feed--home panels-feed--home_content">
				<?php foreach ($home_panels as $panel): ?>
					<div class="column">
						<div class="panel">
							<a class="panel-image" href="<?= $panel['link_url'] ?>" tabindex="-1">
								<img src="<?= $panel_path.$panel['image'] ?>" alt="" />
							</a>
							<h3 class="panel-title"><?= $panel['title'] ?></h3>
							<a href="<?= $panel['link_url'] ?>" class="panel-link button"><?= __('View More') ?></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php $bars = Menuhelper::get_all_published_menus('Bars'); ?>
<?php if ($bars): ?>
	<section class="bars-section">
		<div class="row">
			<div class="panels-feed panels-feed--home panels-feed--bars">
				<?php foreach ($bars as $bar): ?>
					<?php $icon = (strpos($bar['title'], 'Contact') !== false) ? 'phone' : ((strpos($bar['title'], 'Courses') !== false) ? 'music' : 'file-text' )?>
					<div class="column">
						<a href="<?= menuhelper::get_link($bar) ?>" class="bar" data-id="<?= $bar['id'] ?>">
							<div class="bar-icon">
								<span class="fa fa-<?= $icon ?>"></span>
							</div>
							<div class="bar-text"><?= $bar['title'] ?></div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (count($course_categories)): ?>
	<?php $course_category_path  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'courses/'); ?>
	<section class="carousel-section">
		<div class="swiper-button-prev" id="courses-carousel-prev"></div>
		<div class="swiper-button-next" id="courses-carousel-next"></div>
		<div class="row">
			<div class="swiper-container" id="courses-carousel">
				<div class="swiper-wrapper">
					<?php foreach ($course_categories as $category): ?>
						<div class="swiper-slide">
							<div class="panel">
								<h3 class="panel-title"><?= $category['category'] ?></h3>
								<a href="/course-list.html?category=<?= $category['id'] ?>" class="panel-image" tabindex="-1">
									<img src="<?= $course_category_path.$category['file_id'] ?>" alt="" />
								</a>
								<a href="/course-list.html?category=<?= $category['id'] ?>" class="panel-link button">View Courses</a>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="swiper-pagination"></div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php include 'template_views/footer.php'; ?>
