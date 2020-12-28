<?php
$product_enquiry   = (Settings::instance()->get('course_enquiry_button') == 1);
$display_mode      = (empty($display_mode) OR ! in_array($display_mode, array('grid', 'list'))) ? 'list' : $display_mode;
$course_media_path  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'courses/');
//ob_clean();print_r($search['data']);exit;
/* Pagination */
if (!isset($search) && isset($courses)) {
	$search = $courses;
}
$per_page = Settings::instance()->get('courses_results_per_page');
$actual_pages_count = $search['total_count'] == 0 ? 0 : ceil($search['total_count'] / $per_page);
$current_page = $page;

$prev_button_active = ($current_page > 1);
$next_button_active = ($current_page < $actual_pages_count);

// Build the general GET URL for the course-list.html page (deprecated)
$pagination_base_url = '/course-list.html/';
$user = Auth::instance()->get_user();
$contact_id = 0;
if (@$user['id']) {
	if (Auth::instance()->has_access('contacts3_limited_view')) {
		$contact = current(Model_Contacts3::get_contact_ids_by_user($user['id']));
		$contact_id = $contact['id'];
	}
}
?>


<div class="course-list-header clearfix">
	<h1>Course List</h1>

	<div class="course-list-result_count"><?= $search['results_found'] ?></div>
	 <div class="course-list-display_options hidden--mobile hidden--tablet">
		<span><?= __('Sort:') ?></span>
		<ul>
			<li class="course-list-display-option">
				<input id="course-list-sort_asc" type="radio" name="course_list_sort" value="asc" />
				<label for="course-list-sort_asc" title="<?= __('Sort ascending') ?>">
					<span class="sr-only"><?= __('Sort ascending') ?></span>
				</label>
			</li>

			<li class="course-list-display-option">
				<input id="course-list-sort_desc" type="radio" name="course_list_sort" value="desc" checked="checked" />
				<label for="course-list-sort_desc" title="<?= __('Sort descending') ?>">
					<span class="sr-only"><?= __('Sort descending') ?></span>
				</label>
			</li>

			<li class="course-list-display-option">
				<input id="course-list-display_list" type="radio" name="course_list_display" value="list"<?= ($display_mode == 'list') ? ' checked="checked"' : '' ?> />
				<label for="course-list-display_list" title="<?= __('List view') ?>">
					<span class="sr-only"><?= __('List view') ?></span>
				</label>
			</li>

			<li class="course-list-display-option">
				<input id="course-list-display_grid" type="radio" name="course_list_display" value="grid"<?= ($display_mode == 'grid') ? ' checked="checked"' : '' ?> />
				<label for="course-list-display_grid" title="<?= __('Grid view') ?>">
					<span class="sr-only"><?= __('Grid view') ?></span>
				</label>
			</li>
		</ul>
	</div> 
</div>

<div class="course-list course-list--<?= $display_mode ?>">
	<?php foreach ($search['data'] as $course): ?>
		<?php
		$url_name = str_replace('%2F', '', urlencode($course['title']));
		$course['location'] = isset($course['schedules'][0]) ? $course['schedules'][0]['location'] : '';
		?>

		<div class="course-list-column">
			<div class="course-widget">
				<a class="course-widget-image" href="/course-detail/<?= $url_name ?>.html/?id=<?= $course['id']; ?>" tabindex="-1">
					<?php if ( ! empty($course['images'][0])): ?>
						<img src="<?= $course_media_path.$course['images'][0]['filename'] ?>" alt="" />
					<?php else: ?>
						<img src="<?= $course_media_path ?>course-placeholder.png" alt="" />
					<?php endif; ?>

					<div class="course-widget-price grid_only">
						<?= __('Price') ?> &euro;<span class="course-widget-price-amount"></span>
					</div>
				</a>
				<div class="course-widget-header grid_only">
					<h2 class="course-widget-title">
						<?= $course['title'] ?>
					</h2>
				</div>			

				<div class="course-widget-details">
					<div class="course-widget-header list_only">
						<h2 class="course-widget-title">
							<?= $course['title'] ?>
						</h2>
					</div>

					<div class="course-widget-description list_only"><?= $course['summary'] ?></div>

					<div class="course-widget-links">
						<a class="button button--view" href="/course-detail/<?= $url_name ?>.html/?id=<?= $course['id']; ?>"><?= __('More Info') ?></a>
						<?php
						if ( ! empty($contact_id)) {
							$in_wishlist = count(Model_KES_Wishlist::search(array('contact_id' => $contact_id, 'schedule_id' => $schedule['id'])));
						?>
						<a class="button button--book wishlist_add <?=$in_wishlist == 0 ? '' : 'hidden'?>" data-contact_id="<?=$contact_id?>" data-schedule_id="<?=$schedule['id']?>"><?= __('Add To Wishlist') ?></a>
						<a class="button button--cl_remove wishlist_remove <?=$in_wishlist == 0 ? 'hidden' : ''?>" data-contact_id="<?=$contact_id?>" data-schedule_id="<?=$schedule['id']?>"><?= __('Remove From Wishlist') ?></a>
						<?php
						}
						?>
						<?php if ( ! $product_enquiry): ?>
                            <?php $apply_now_link = trim(Settings::instance()->get('course_apply_link')); ?>

                            <?php if ($apply_now_link): ?>
                                <?php $apply_now_is_external = IbHelpers::is_external($apply_now_link); ?>
                                <a href="<?= $apply_now_link ?>"<?= $apply_now_is_external ? ' target="_blank"' : '' ?> class="button button--book"><?= __('Apply Now') ?></a>
                            <?php else: ?>
                                <button class="button button--book" type="button"><?= __('Apply Now') ?></button>
                            <?php endif; ?>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div><?php //.course-list ?>

<div class="pagination-wrapper">
	<input type="hidden" id="current_page" value="<?= $current_page ?>" />

	<ul class="pagination" role="navigation" aria-label="Pagination" id="search_results-pagination">
		<li class="pagination-prev">
			<a
				href="<?= $prev_button_active ? $pagination_base_url . urlencode($current_page - 1) : '#' ?>"
				<?= $prev_button_active ? '' : ' class="disabled"' ?>
				data-page="<?= $current_page - 1 ?>"
				>
				<span class="sr-only"><?= __('Previous') ?></span>
			</a>
		</li>

		<?php $radius = 3; ?>
		<?php for ($page = 1; $page <= $actual_pages_count; $page++) : ?>
			<?php if (($actual_pages_count == 2 * $radius + 1) OR ($page > $current_page - $radius AND $page < $current_page + $radius)): ?>
				<li>
					<a
						href="<?= $pagination_base_url.'?page='.urlencode($page) ?>"
						aria-label="Page <?= $page ?>"
						data-page="<?= $page ?>"
						<?= ($page == $current_page) ? ' class="current"' : '' ?>
						><?= $page ?></a>
				</li>
			<?php elseif (($actual_pages_count != 2 * $radius + 1) AND ($page == $current_page - $radius OR $page == $current_page + $radius)): ?>
				<li><a href="#" class="disabled">...</a></li>
			<?php endif; ?>
		<?php endfor; ?>

		<li class="pagination-next">
			<a
				href="<?= $next_button_active ? $pagination_base_url . urlencode($current_page + 1) : ''  ?>"
				<?= $next_button_active ? '' : ' class="disabled"' ?>
				data-page="<?= $current_page + 1 ?>"
				>
				<span class="sr-only"><?= __('Next') ?></span>
			</a>
		</li>
	</ul>
</div>
