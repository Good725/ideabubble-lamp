<?php include 'template_views/header.php' ?>
	<div class="clearfix">
		<div class="col-xsmall-12 col-small-12 col-medium-9"><?= trim($page_data['content']) ?></div>
	</div>
	<?php
	$categories = Model_Categories::get_all_published_categories();
	$base_file_path = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/';
	?>
	<?php if (count($categories) > 0): ?>
		<div class="space-between-cols course-category-listing">
			<?php foreach ($categories as $category): ?>
				<div class="col-xsmall-6 col-small-4 col-medium-3">
					<a class="course-category-item" href="/search-results?category_ids[]=<?= $category['id'] ?>">
						<img src="<?= (trim($category['file_id'])) ? $base_file_path.$category['file_id'] : $base_file_path.'no_image_available.png' ?>" />
						<div class="course-category-caption">
							<?= $category['category'] ?>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

<?php include 'template_views/footer.php' ?>