<?php
$args = Kohana::sanitize($_GET);
$args['offset'] = (isset($args['page']) AND $args['page'] > 1) ? ($args['page'] - 1) * 10 : 0;
if (isset($args['category_ids']) AND ! array_filter($args['category_ids'])) unset($args['category_ids']);
$courses = Model_Courses::filter($args);

?>
<?php if (is_array($courses['data']) AND count($courses['data']) > 0): ?>
	<?= View::factory('front_end/course_feed_items_snippet', array('courses' => $courses, 'layout' => $page_data['layout'])) ?>
<?php else: ?>
	<p>There are no courses to display.</p>
<?php endif; ?>
