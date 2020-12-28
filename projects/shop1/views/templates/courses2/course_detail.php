<?php include 'template_views/header.php' ?>

	<div class="content-wrapper content content-with-sidebar compact-cols">
		<aside class="col-xsmall-12 col-small-12 col-medium-3 sidebar">
			<?php include 'template_views/course_menu.php'; ?>
		</aside>
		<div class="col-xsmall-12 col-small-12 col-medium-9">
			<?= $page_data['content'] ?>
			<div class="course-details">
				<?php include 'template_views/course_detail_snippet.php'; ?>
			</div>
		</div>
	</div>

<?php include 'template_views/footer.php' ?>