<?php

//Session::instance()->set('last_search_params', $_GET);
include 'template_views/header.php';
$selected_location = $selected_category = '';
$filters = Model_Courses::get_available_filters();
$filter_categories = $filters['categories'];
$filter_course_counties = $filters['course_counties'];
$filter_levels     = $filters['levels'];
$filter_locations  = $filters['locations'];
$filter_subjects   = $filters['subjects'];
$filter_types      = $filters['types'];
$filter_years      = $filters['years'];

if (!empty($_GET[ 'location' ])) {
    foreach ($filter_locations as $filter_location) {
        if ($_GET[ 'location' ] == $filter_location[ 'id' ]) {
            $selected_location = '<li><span class="remove" data-category="location" data-id="' . $filter_location[ 'id' ] . '" onclick="remove_criteria(this)">x</span> <span class="category">location</span>: ' . $filter_location[ 'name' ] . '</li>';
        }
    }
}

if (!empty($_GET[ 'location_ids' ])) {
    foreach ($filter_locations as $filter_location) {
        foreach ($_GET[ 'location_ids' ] as $location_idx)
            if ($location_idx == $filter_location[ 'id' ]) {
                $selected_location = '<li><span class="remove" data-category="location" data-id="' . $filter_location[ 'id' ] . '" onclick="remove_criteria(this)">x</span> <span class="category">location</span>: ' . $filter_location[ 'name' ] . '</li>';
            }
    }
}
if (count($_GET) > 0) {
    $last_search_parameters = $_GET;
    Session::instance()->set('last_search_params', $last_search_parameters);
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
$args[ 'location_ids' ] = $args[ 'category_ids' ] = array();

if (!empty($_GET['county_id'])) {
    $args['county_ids'] = array($_GET['county_id']);
}
if (!empty($_GET[ 'location' ])) {
    $args[ 'location_ids' ][ 0 ] = $location = $_GET[ 'location' ];
}
if (!empty($_GET[ 'location_ids' ])) {
    $args[ 'location_ids' ] = $_GET[ 'location_ids' ];
}
if (!empty($_GET[ 'title' ])) {
    $args[ 'keywords' ] = $title = $_GET[ 'title' ];
}
if (!empty($_GET[ 'keywords' ])) {
    $args[ 'keywords' ] = $title = $_GET[ 'keywords' ];
}
if (!empty($_GET[ 'level' ])) {
    $args[ 'level' ] = $level = $_GET[ 'level' ];
}
if (!empty($_GET[ 'category' ])) {
    $args[ 'category_ids' ][ 0 ] = $category = $_GET[ 'category' ];
}
if (!empty($_GET[ 'category_ids' ])) {
    $args[ 'category_ids' ] = $category = $_GET[ 'category_ids' ];
}
if (!empty($_GET[ 'course' ])) {
    $args[ 'course_ids' ][ 0 ] = $course = $_GET[ 'course' ];
}
if (!empty($_GET[ 'course_ids' ])) {
    $args[ 'course_ids' ] = $course = $_GET[ 'course_ids' ];
}
if (!empty($_GET[ 'year' ])) {
    $args[ 'year_ids' ][ 0 ] = $year = $_GET[ 'year' ];
}
if (!empty($_GET[ 'year_ids' ])) {
    $args[ 'year_ids' ] = $year = $_GET[ 'year_ids' ];
}
if (!empty($_GET['subject'])) {
    $args['subject_ids'][0] = $subject = $_GET['subject'];
}
if (!empty($_GET['subject_ids'])) {
    $args['subject_ids'] = $subject = $_GET['subject_ids'];
}
if (!empty($_GET[ 'sort' ])) {
    $args[ 'sort' ] = $sort = strtolower($_GET[ 'sort' ]);
}

if (!empty($_GET[ 'page' ])) {
    $page = (int)$_GET[ 'page' ];
    $args[ 'offset' ] = Settings::instance()->get('courses_results_per_page') * ( $page - 1 );
}

//ob_clean();header('content-type: text/plain');print_r($args);exit;
$args[ 'timeslots' ] = true;
$args[ 'limit' ] = Settings::instance()->get('courses_results_per_page');
$args['book_on_website'] = true;
$search = isset($search) ? $search : Model_Plugin::global_search($args);
$product_enquiry = ( Settings::instance()->get('course_enquiry_button') == 1 );

if (Settings::instance()->get('single_course_redirect')) {
    // If the initial search only yields one result, go straight to the course details page
    $exactly_one_result = (isset($search['total_count']) && $search['total_count'] == 1);
    $result_is_course   = (!empty($search['data']) && $search['data'][0]['type'] == 'course');
    $has_query_string   = !empty($_SERVER['QUERY_STRING']);

    // Only do this if the query string contains filters. (Don't do it when remembering filters via sessions/cookies.)
    // This way the user only gets redirected when they are consciously opening a filtered page.
    // Otherwise if their session/cookie contains filters that yield one result, they will constantly get redirected...
    // ... and not be able to return to the course list.
    if ($exactly_one_result && $result_is_course && $has_query_string) {
        header('Location: /course-detail/'.urlencode($search['data'][0]['title']).'/?id='.$search['data'][0]['id']);
        die();
    }
}
?>

<div class="fullwidth course-list-intro<?= (trim($page_data['content'])) ? ' has_content' : '' ?>">
    <?php if (trim($page_data['content'])): ?>
        <div class="row content_area">
            <div class="page-content"><?= trim($page_data['content']) ?></div>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php
        $current_step = 'results';
        include 'views/checkout_progress.php'
        ?>
    </div>
</div>

<div class="content-columns">
    <div class="row content-columns">
        <aside class="course-list-sidebar sidebar" id="sidebar">

            <div class="hidden--tablet hidden--desktop clearfix">
                <button type="button" class="button--plain course-filters-toggle" id="course-filters-toggle" data-hide_toggle="#course-list-sidebar-content" data-hide_toggle-class="hidden--mobile">
                    <?= file_get_contents(ENGINEPATH.'plugins/courses/development/assets/images/filter_icon.svg') ?>
                </button>
            </div>

            <div class="course-list-sidebar-content hidden--mobile" id="course-list-sidebar-content" data-hide_toggle-click_away="1" data-hide_toggle-trigger="#course-filters-toggle">
                <?php
                $filter_groups = Settings::instance()->get('course_list_filters');
                $filter_groups = is_array($filter_groups) ? $filter_groups : null;
                ?>

                <div class="sidebar-section">
                    <h2 class="sidebar-section-title">
                        <?= ($filter_groups === null || in_array('keyword', $filter_groups))
                            ? __(Settings::instance()->get('search_category_label_keyword'))
                            : __('Search') ?>
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
                                        <span class="search-criteria-value"><?= $course_data[ 'title' ] ?></span>
                                        <input type="hidden" name="course_ids" value="<?= $course ?>"
                                               class="filter-course_ids"/>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($event_object)): ?>
                                    <li class="search-criteria-li" data-id="event_ids-<?= $event_object->id ?>">
                                        <button class="button--plain search-criteria-remove">
                                            <span class="fa fa-times"></span>
                                        </button>
                                        <span class="search-criteria-category"><?= __('Event') ?></span>
                                        <span class="search-criteria-value"><?= $event_object->name ?></span>
                                        <input type="hidden" name="event_ids" value="<?= $event_object->id ?>"
                                               class="filter-event_ids" />
                                    </li>
                                <?php endif; ?>

                                <li id="search-criteria-reset-li">
                                    <button class="button--plain search-criteria-reset">
                                        <span class="fa fa-times"></span>
                                        <?= __('Reset Criteria') ?>
                                    </button>
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

                        <?php if ($filter_groups === null || in_array('keyword', $filter_groups)): ?>
                            <label class="sr-only" for="course-filter-keyword"><?= __('Keyword') ?></label>
                            <div class="course-filter-keyword-input-wrapper input_group">
                                <input class="form-input" type="text" placeholder="<?= __('Keyword') ?>"
                                       id="course-filter-keyword"
                                       value="<?= @$_GET[ 'title' ] ? @$_GET[ 'title' ] : @$_GET[ 'keywords' ] ?>"/>
                                <div class="input_group-icon">
                                    <span class="icon_search flip-horizontally"></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                // Counties
                if ($filter_groups === null || in_array('event_counties', $filter_groups)) {
                    if (!empty ($filter_counties)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_event_counties'));
                        $filter_name = 'county_ids';
                        $filter_items = $filter_counties;
                        $filter_item_key = 'name';
                        $default = !empty($args['county_ids']) ? $args['county_ids'][0] : '';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }

                // Locations
                if ($filter_groups === null || in_array('locations', $filter_groups)) {
                    if (!empty ($filter_locations)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_locations'));
                        $filter_name = 'location_ids';
                        $filter_items = $filter_locations;
                        $filter_item_key = 'name';
                        $default = !empty($args['location_ids']) ? $args['location_ids'][0] : '';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }

                // Years
                if ($filter_groups === null || in_array('years', $filter_groups)) {
                    if (!empty ($filter_years)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_years'));
                        $filter_name = 'year_ids';
                        $filter_items = $filter_years;
                        $filter_item_key = 'year';
                        $default = !empty($args['year_ids']) ? $args['year_ids'][0] : '';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }

                // Categories (courses)
                if ($filter_groups === null || in_array('course_categories', $filter_groups)) {
                    if (!empty ($filter_categories)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_course_categories'));
                        $filter_name = 'category_ids';
                        $filter_items = $filter_categories;
                        $filter_item_key = 'category';
                        $default = !empty($args['category_ids']) ? $args['category_ids'][0] : '';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }

                // Categories (events)
                if ($filter_groups === null || in_array('event_categories', $filter_groups)) {
                    if (!empty($filter_event_categories)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_event_categories'));
                        $filter_name = 'category_ids';
                        $filter_items = $filter_event_categories;
                        $filter_item_key = 'label';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }

                // Subject Levels
                if ($filter_groups === null || in_array('levels', $filter_groups)) {
                    if (!empty ($filter_levels)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_levels'));
                        $filter_name = 'level_ids';
                        $filter_items = $filter_levels;
                        $filter_item_key = 'level';
                        $default = !empty($args['level']) ? $args['level'] : '';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }

                // Subjects
                if ($filter_groups === null || in_array('subjects', $filter_groups)) {
                    if (!empty ($filter_subjects)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_subjects'));
                        $filter_name = 'subject_ids';
                        $filter_items = $filter_subjects;
                        $filter_item_key = 'name';
                        $default = !empty($args['subject_ids']) ? $args['subject_ids'] : '';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }

                // Course counties
                if ($filter_groups === null || in_array('course_counties', $filter_groups)) {
                    if (!empty ($filter_course_counties)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_course_counties'));
                        $filter_name = 'course_county_ids';
                        $filter_items = $filter_course_counties;
                        $filter_item_key = 'name';
                        $default = !empty($args['course_county_ids']) ? $args['course_county_ids'] : '';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }

                // Types
                if ($filter_groups === null || in_array('types', $filter_groups)) {
                    if (!empty ($filter_types)) {
                        $filter_title = __(Settings::instance()->get('search_category_label_types'));
                        $filter_name = 'type_ids';
                        $filter_items = $filter_types;
                        $filter_item_key = 'type';
                        $default = !empty($args['type_ids']) ? $args['type_ids'] : '';
                        include 'template_views/snippets/sidebar_filter.php';
                    }
                }
                ?>
            </div>
        </aside>

        <div class="content_area">
            <div id="course-results">
                <?php include 'views/front_end/course_feed_items_snippet.php'; ?>
            </div>
        </div><?php //.content_area ?>
    </div><?php // .row ?>
</div><?php // .content-columns ?>

<?php if (trim($page_data['footer'])): ?>
    <div class="page-footer row content_area pb-0">
        <div class="page-content"><?= IbHelpers::parse_page_content($page_data['footer']) ?></div>
    </div>
<?php endif; ?>

<?php include 'views/footer.php'; ?>
