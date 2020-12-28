<aside class="sidebar">
	<div class="sidebar-course_menu">
		<h3>Courses</h3>
		<?php $courses = Model_Courses::get_all_published(); ?>
		<?php if (count($courses > 0)): ?>
			<ul class="course_menu">
				<?php foreach ($courses as $course): ?>
					<li><a href="/course-detail.html/?id=<?= $course['id'] ?>" title="<?= $course['title'] ?>"><?= $course['title'] ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<div class="sidebar-panels">
		<?= Model_Panels::get_panels_feed('content_left'); ?>
	</div>
</aside>