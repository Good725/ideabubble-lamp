<?php
if (count($_GET) == 0){
    $last_search_parameters = Session::instance()->get('last_search_params');
    if (!empty($last_search_parameters)) {
        $_GET = $last_search_parameters;
    }
}
//Session::instance()->set('last_search_params', $_GET);
include 'template_views/header.php';
$filter_locations = Model_Locations::get_locations_without_parent();
$filter_years = Model_Years::get_all_years();
$filter_categories = Model_Categories::get_all_categories();
$filter_levels = Model_Levels::get_all_levels();
$selected_location = $selected_category = '';

if ( ! empty($_GET['location']))
{
	foreach ($filter_locations as $filter_location)
	{
		if ($_GET['location'] == $filter_location['id'])
		{
			$selected_location = '<li><span class="remove" data-category="location" data-id="'.$filter_location['id'].
				'" onclick="remove_criteria(this)">x</span> <span class="category">location</span>: '.$filter_location['name'].'</li>';
		}
	}
}

if (!empty($_GET['location_ids'])) {
    foreach ($filter_locations as $filter_location) {
        foreach ($_GET['location_ids'] as $location_idx)
        if ($location_idx == $filter_location['id']) {
            $selected_location = '<li><span class="remove" data-category="location" data-id="'.$filter_location['id'].
                '" onclick="remove_criteria(this)">x</span> <span class="category">location</span>: '.$filter_location['name'].'</li>';
        }
    }
}

$_GET = Kohana::sanitize($_GET);
$location = false;
$title = false;
$level = false;
$category = false;
$course = false;
$year = false;
$sort = 'asc';
$page = 1;
$args['location_ids'] = $args['category_ids'] = array();

if ( ! empty($_GET['location']))     { $args['location_ids'][0] = $location = $_GET['location']; }
if ( ! empty($_GET['location_ids'])) { $args['location_ids']    = $_GET['location_ids']; }
if ( ! empty($_GET['title']))        { $args['keywords']        = $title    = $_GET['title'];    }
if ( ! empty($_GET['keywords']))     { $args['keywords']        = $title    = $_GET['keywords'];    }
if ( ! empty($_GET['level']))        { $args['level']           = $level    = $_GET['level'];    }
if ( ! empty($_GET['category']))     { $args['category_ids'][0] = $category = $_GET['category']; }
if ( ! empty($_GET['category_ids'])) { $args['category_ids'   ] = $category = $_GET['category_ids']; }
if ( ! empty($_GET['course']))       { $args['course_ids'][0]   = $course   = $_GET['course']; }
if ( ! empty($_GET['course_ids']))   { $args['course_ids']      = $course   = $_GET['course_ids']; }
if ( ! empty($_GET['year']))         { $args['year'][0]         = $year     = $_GET['year'];     }
if ( ! empty($_GET['year_ids']))     { $args['year']            = $year     = $_GET['year_ids'];     }
if ( ! empty($_GET['sort']))         { $args['sort']            = $sort     = strtolower($_GET['sort']); }

if ( ! empty($_GET['page']))
{
	$page = (int)$_GET['page'];
	$args['offset'] = Settings::instance()->get('courses_results_per_page') * ($page - 1);
}

$args['timeslots'] = true;
$args['limit'] = Settings::instance()->get('courses_results_per_page');
$courses = Model_Courses::filter($args);

$product_enquiry = (Settings::instance()->get('course_enquiry_button') == 1);
?>

	<div class="content-columns">
		<div class="row content-columns">
			<aside class="sidebar" id="sidebar">
				<div class="sidebar-section">
					<h2 class="sidebar-section-title">
						<?= __('Search') ?>
						<button type="button" class="sidebar-section-collapse">
							<span class="fa fa-chevron-down"></span>
						</button>
					</h2>

					<div class="sidebar-section-content">

						<div id="course_filter_criteria">
							<ul class="list-unstyled search-filter-list">
								<?php if ($course): ?>
									<?php $course_data = Model_Courses::get_course($course); ?>
									<li class="search-criteria-li" data-id="course_ids-<?= $course ?>">
										<button class="button--plain search-criteria-remove">
											<span class="fa fa-times"></span>
										</button>
										<span class="search-criteria-category"><?= __('Course') ?></span>
										<span class="search-criteria-value"><?= $course_data['title'] ?></span>
										<input type="hidden" name="course_ids" value="<?= $course ?>" class="filter-course_ids" />
									</li>
								<?php endif; ?>

								<li id="search-criteria-reset-li">
									<button class="button--plain search-criteria-reset">
										<span class="fa fa-times"></span>
									</button>
									<?= __('Reset Criteria') ?>
								</li>
							</ul>
							<div class="hidden" id="course_filter_criteria_template">
								<button class="button--plain search-criteria-remove">
									<span class="fa fa-times"></span>
								</button>
								<span class="search-criteria-category"></span>
								<span class="search-criteria-value"></span>
							</div>
						</div>

						<div class="input_group">
							<div>
								<label class="sr-only" for="course-filter-keyword"><?= __('Keyword') ?></label>
								<input class="form-input" type="text" placeholder="<?= __('Keyword') ?>" id="course-filter-keyword" value="<?= @$_GET['title'] ? @$_GET['title'] : @$_GET['keywords'] ?>" />
							</div>
							<div class="input_group-icon">
								<span class="fa fa-search"></span>
							</div>
						</div>
					</div>
				</div>

				<?php
				// Locations
				if ( ! empty ($filter_locations))
				{
					$filter_title    = __('Locations');
					$filter_singular = __('Location');
					$filter_name     = 'location_ids';
					$filter_items    = $filter_locations;
					$filter_item_key = 'name';
					$default         = ( ! empty($_GET['location'])) ? $_GET['location'] : '';
                    $default         = (!empty($_GET['location_ids'])) && !$default ? $_GET['location_ids'] : '';
					include 'template_views/snippets/sidebar_filter.php';
				}

				// Years
				if ( ! empty ($filter_years))
				{
					$filter_title    = __('Years');
					$filter_singular = __('Year');
					$filter_name     = 'year_ids';
					$filter_items    = $filter_years;
					$filter_item_key = 'year';
					$default         = ( ! empty($_GET['year'])) ? $_GET['year'] : '';
                    $default         = (!empty($_GET['year_ids'])) && !$default ? $_GET['year_ids'] : '';
					include 'template_views/snippets/sidebar_filter.php';
				}

				// Categories
				if ( ! empty ($filter_categories))
				{
					$filter_title    = __('Class Types');
					$filter_singular = __('Class Type');
					$filter_name     = 'category_ids';
					$filter_items    = $filter_categories;
					$filter_item_key = 'category';
					$default         = ( ! empty($_GET['category'])) ? $_GET['category'] : '';
                    $default         = (!empty($_GET['category_ids'])) && !$default ? $_GET['category_ids'] : '';
					include 'template_views/snippets/sidebar_filter.php';
				}

				// Subject Levels
				if ( ! empty ($filter_levels))
				{
					$filter_title    = __('Subject Levels');
					$filter_singular = __('Level');
					$filter_name     = 'level_ids';
					$filter_items    = $filter_levels;
					$filter_item_key = 'level';
					$default         = ( ! empty($_GET['level'])) ? $_GET['level'] : '';
                    $default         = (!empty($_GET['level_ids'])) && !$default ? $_GET['level_ids'] : '';
					include 'template_views/snippets/sidebar_filter.php';
				}
				?>
			</aside>

			<div class="content_area">
				<div class="page-content"><?= $page_data['content'] ?></div>

				<div id="course-results">
					<?php include 'views/front_end/course_feed_items_snippet.php'; ?>
				</div>
			</div><?php //.content_area ?>
		</div><?php // .row ?>
	</div><?php // .content-columns ?>
<?php include 'template_views/footer.php'; ?>
