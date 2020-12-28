<?php include 'template_views/header.php'; ?>

	<div class="content-wrapper content content-with-sidebar compact-cols">
		<aside class="col-xsmall-12 col-small-12 col-medium-3 sidebar">
			<?php include 'template_views/course_menu.php' ?>
			<div class="sidebar-panels">
				<?= Model_Panels::get_panels_feed('content_left'); ?>
			</div>
		</aside>
		<div class="col-xsmall-12 col-small-12 col-medium-9">
			<?= $page_data['content'] ?>
			<?php include 'template_views/booking_form.php'; ?>
		</div>
	</div>


<?php include 'template_views/footer.php' ?>
