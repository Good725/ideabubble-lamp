<?php
$_GET = Kohana::sanitize($_GET);
$location = false;
$title = false;
$level = false;
$category = false;
$args['location_ids'] = $args['category_ids'] = array();

if (isset($_GET['location']) AND strlen($_GET['location']) > 0) {
    $location = $_GET['location'];
    $args['location_ids'][0] = $location;
}
if (isset($_GET['title']) and strlen($_GET['title']) > 0) {
    $title = $_GET['title'];
    $args['keywords'] = $title;
}
if (isset($_GET['level']) AND strlen($_GET['level']) > 0) {
    $level = $_GET['level'];
    $args['level'] = $level;
}
if (isset($_GET['category']) AND strlen($_GET['category']) > 0)
{
    $category = $_GET['category'];
    $args['category_ids'][0] = $category;
}
if (isset($_GET['year']) AND strlen($_GET['year']) > 0) {
    $args['year'][0] = $_GET['year'];
}
if (isset($_GET['sort']) AND strlen($_GET['sort']) > 0)
{
    $sort = strtolower($_GET['sort']);
    $args['sort'] = $sort;
}
else
{
    $sort = "asc";
}
if(isset($_GET['page']))
{
    $page = (int)$_GET['page'];
    $args['offset'] = 10*($page-1);
}
else
{
    $page = 1;
}
if(isset($_GET['year']))
{
    $year = trim($_GET['year']);
}
else
{
    $year = FALSE;
}
//check if book now button is to be shown on settings toggle value
$product_enquiry = FALSE;

if (Settings::instance()->get('product_enquiry') == 1)
{
    $product_enquiry = TRUE;
}

// $courses = Model_Courses::get_courses_for_page($page, 10, $sort, $title, $location, $level, $category, FALSE,$year);

$args['timeslots'] = true;
$search = Model_Plugin::global_search($args);
?>

    <? /* <a id="clear-filter" class="buttonClear"><span><span>Clear filter</span></span></a> */ ?>
    <div id="sorter">
        Sort results:
        <a id="sort-asc" data-sort="asc" class="buttonAsc<?= ($sort = 'asc') ? ' current' : ''; ?>"><span>Ascending</span></a>
        <a id="sort-desc" data-sort="desc" class="buttonDesc<?= ($sort = 'desc') ? ' current' : ''; ?>"><span>Descending</span></a>
    </div>
<?php
if (is_array($courses['data']) AND count($courses['data']) > 0) {

    if (isset($courses['pages']) AND $courses['pages'] > 1) {
        echo View::factory(
            'templates/default/template_views/courses_listing_pagination_nav_view',
            array(
                'title'       => $title,
                'location'    => $location,
                'level'       => $level,
                'category'    => $category,
                'sort'        => $sort,
                'page'        => $page,
                'total_pages' => $courses['pages']
            )
        );
    }
    ?>
	<? require_once 'left_sidebar_filter.php';?>
    <div id="sorted">
        <?= View::factory('front_end/course_feed_items_snippet', array('search' => $search)) ?>
    </div>

    <?php
    if (isset($courses['pages']) AND $courses['pages'] > 1) {
        echo View::factory(
            'templates/default/template_views/courses_listing_pagination_nav_view',
            array(
                'title' => $title,
                'location' => $location,
                'level' => $level,
                'category' => $category,
                'sort' => $sort,
                'page' => $page,
                'total_pages' => $courses['pages']
            )
        );
    }
} else {
    ?>
    <p>There are no courses to display.</p>
<?php
}
?>