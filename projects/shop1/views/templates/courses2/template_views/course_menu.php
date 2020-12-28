<?php $categories = Model_Categories::get_all_published_categories(); ?>
<?php $current_categories = isset($_GET['category_ids']) ? $_GET['category_ids'] : array(); ?>
<?php if (count($categories) > 0): ?>
	<div class="sidebar-course-menu-wrapper">
		<h3>Courses</h3>
		<ul class="sidebar-course-menu">
			<?php foreach ($categories as $category): ?>
				<li class="sidebar-course-category<?= in_array($category['id'], $current_categories) ? ' selected' : '' ?>">
					<a href="/search-results.html?category_ids[]=<?= $category['id'] ?>" title="<?= $category['category'] ?>"><?= $category['category'] ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>