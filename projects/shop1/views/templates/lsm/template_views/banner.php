<section class="banner-section <?= ( ! empty($page_data['banner_slides'])) ? 'banner-section--sequence' : 'banner-section--single'?>">
	<?php if ( ! empty($page_data['banner_slides']) OR ( ! empty($page_data['banner_image']))): ?>
		<?php $banners_path  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'banners/'); ?>
		<?php if ( ! empty($page_data['banner_slides'])): ?>
			<div class="swiper-container" id="home-banner-swiper">
				<div class="swiper-wrapper">
					<?php foreach ($page_data['banner_slides'] as $slide): ?>
						<div class="swiper-slide">
							<div class="banner">
								<div class="banner-image" style="background-image:url('<?= $banners_path.$slide['image'] ?>');">
									<?php if(!empty($slide['html'])): ?>
										<div class="row">
											<div class="banner-caption">
												<?= $slide['html']; ?>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( ! empty($page_data['banner_sequence_data']['controls'])): ?>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
				<?php endif; ?>

				<?php if ( ! empty($page_data['banner_sequence_data']['pagination'])): ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>
			</div>
		<?php elseif ( ! empty ($page_data['banner_image'])): ?>
			<div class="banner">
				<div class="banner-image" style="background-image:url('<?= $banners_path.$page_data['banner_image'] ?>');">
				</div>
			</div>
		<?php endif; ?>
	<?php else:?>
		<div class="banner banner-static">
			<div class="banner-image" style="background-image:url('/assets/<?= $assets_folder_path ?>/images/banner-img.jpg');">
				<div class="row">
					<div class="banner-caption">
						Your inspiration begins here....
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	
	<?php if (strtolower($page_data['layout']) == 'home' OR ! empty($banner_search)): ?>
		<div class="banner-search">
			<div class="row">
				<h2 class="banner-search-title">
					<span class="fa fa-search"></span>
					<?= __('Find Your Courses') ?>
				</h2>
				<form action="/course-list.html" class="validate-on-submit" id="banner-search-form">
					<div class="banner-search-column banner-search-column--location">
						<div class="input_group">
							<span class="input_group-icon">
								<span class="fa fa-search"></span>
							</span>
							<div class="focus_group">
                                <label class="sr-only" for="banner-search-keyword"><?= __('Keyword') ?></label>
								<input type="text" class="form-input" name="keywords" id="banner-search-keyword" placeholder="<?= __('Enter Search Keyword') ?>" autocomplete="off" />
							</div>
						</div>
					</div>

                    <?php
                    $subjects          = Model_Subjects::get_all_subjects(array('publish' => true, 'must_have_categories' => true));
                    $subjects_found    = (bool) count($subjects);
                    $course_categories = Model_Categories::get_all_published_categories();
                    $categories_found  = (bool) count($course_categories);
                    $type_search       = $subjects_found ? 'subject' : ($categories_found ? 'category' : 'course');
                    ?>

					<div class="banner-search-column banner-search-column--subject">
						<div class="input_group">
							<span class="input_group-icon">
								<span class="fa fa-music"></span>
							</span>
							<div class="focus_group">
								<input type="hidden" name="course" id="banner-search-subject_id" />
								<input type="text" class="form-input" id="banner-search-subject" placeholder="<?= __('Select Your Course Type') ?>" autocomplete="off"
									   data-drilldown="#subject-drilldown" data-type_search="#subject-drilldown-<?= $type_search ?>-list" />
							</div>
						</div>
					</div>

					<div class="search-drilldown search-drilldown--subject" id="subject-drilldown">
						<button type="button" class="search-drilldown-close button--plain"></button>

                       	<div class="search-drilldown-column<?= $subjects_found ? '' : ' hidden' ?>">
							<div>
								<h3><?= __('Pick a subject') ?></h3>
								<ul class="list-unstyled" id="subject-drilldown-subject-list">
									<?php foreach ($subjects as $subject): ?>
										<li><a href="#" data-id="<?= $subject['id'] ?>"><?= $subject['name'] ?></a></li>
									<?php endforeach; ?>
								</ul>
								<p class="search-drilldown-no_results<?= ( ! empty($subjects)) ? ' hidden' : '' ?>"><?= __('No results found.') ?></p>
							</div>
						</div>

						<div class="search-drilldown-column search-drilldown-column--category">
							<div>
								<h3><?= __('Pick a class type') ?></h3>
								<ul class="list-unstyled<?= ( ! $subjects_found AND $categories_found) ? '' : ' hidden' ?>" id="subject-drilldown-category-list" data-filtered_by="#subject-drilldown-subject-list">
									<?php if ( ! empty($course_categories)): ?>
										<?php foreach ($course_categories AS $category): ?>
											<li><a href="#" data-id="<?= $category['id'] ?>"><?= $category['category'] ?></a></li>
										<?php endforeach; ?>
									<?php endif; ?>
								</ul>
								<p class="search-drilldown-no_results<?= $categories_found ? ' hidden' : '' ?>"><?= __('No results found.') ?></p>
								<p class="search-drilldown-awaiting_selection<?= $subjects_found ? '' : ' hidden' ?>"><?= __('Select a subject first.') ?></p>
							</div>
						</div>
						<div class="search-drilldown-column search-drilldown-column--course">
							<div>
								<h3><?= __('Pick a course') ?></h3>
								<ul class="list-unstyled" id="subject-drilldown-course-list" data-filtered_by="#subject-drilldown-category-list">
								</ul>
								<p class="search-drilldown-no_results hidden"><?= __('No results found.') ?></p>
								<p class="search-drilldown-awaiting_selection"><?= __('Select a class type first.') ?></p>
							</div>
						</div>
					</div>

					<div class="banner-search-column banner-search-column--continue">
						<button type="submit" class="button button--continue"><?= __('Continue') ?></button>
					</div>
				</form>
			</div>
		</div>
	<?php endif; ?>
</section>
