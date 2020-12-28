<?php
$args = array();
if ( ! empty($_GET['search']))
{
	$args['keywords'] = Kohana::sanitize($_GET['search']);
}
if ( ! empty($_GET['location']))
{
	$args['location_ids'] = Kohana::sanitize($_GET['location']);
}
if ( ! empty($_GET['page']) and is_numeric($_GET['page']))
{
    $args['offset'] = ($_GET['page'] - 1) * 10;
}

$courses = Model_Courses::filter($args);
?>
<?php if (is_array($courses['data']) AND count($courses['data']) > 0): ?>
	<div class="course_results">
        <?= View::factory('front_end/course_feed_items_snippet', array('courses' => $courses, 'layout' => $page_data['layout'])) ?>
    </div>
<?php else: ?>
	<p>There are no courses to display.</p>
<?php endif; ?>
