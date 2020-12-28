<?php
$last_params       = Session::instance()->get('last_search_params');
$product_enquiry   = (Settings::instance()->get('course_enquiry_button') == 1);
$sorting_order     = isset($last_params['sort']) ? $last_params['sort'] : null;

if (empty($sorting_order) || !in_array($sorting_order, ['asc', 'desc'])) {
    $sorting_order = Settings::instance()->get('course_search_default_sorting_direction') ?: 'asc';
}
if (empty($display_mode) || !in_array($display_mode, ['grid', 'list'])) {
    $display_mode = Settings::instance()->get('course_search_default_layout') ?: 'grid';
}


/* Pagination */
$per_page = Settings::instance()->get('courses_results_per_page');
$per_page = $per_page ? $per_page : 12;
$actual_pages_count = 1;

if (isset($search) && isset($search['total_count'])) {
    $actual_pages_count = empty($search['total_count']) ? 0 : ceil($search['total_count'] / $per_page);
}

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
$account_bookings = Settings::instance()->get('account_managed_course_bookings');
?>

<div class="course-list-header clearfix">
	<div class="course-list-result_count page-content"><?= isset($search) && isset($search['results_found']) ? $search['results_found'] : '' ?></div>

	<div class="course-list-display_options hidden--mobile hidden--tablet" data-mobile_mode="<?= Settings::instance()->get('course_list_mode_mobile') ?: 'grid' ?>">
		<span><?= __('Sort:') ?></span>

		<ul>
			<li class="course-list-display-option">
				<input id="course-list-sort_asc" type="radio" name="course_list_sort" value="asc"<?= ($sorting_order == 'asc') ? ' checked="checked"' : '' ?> />
				<label for="course-list-sort_asc" title="<?= __('Sort ascending') ?>">
					<span class="sr-only"><?= __('Sort ascending') ?></span>
                    <span class="fa fa-sort-amount-asc"></span>
				</label>
			</li>

			<li class="course-list-display-option">
				<input id="course-list-sort_desc" type="radio" name="course_list_sort" value="desc"<?= ($sorting_order == 'desc') ? ' checked="checked"' : '' ?> />
				<label for="course-list-sort_desc" title="<?= __('Sort descending') ?>">
					<span class="sr-only"><?= __('Sort descending') ?></span>
                    <span class="fa fa-sort-amount-desc"></span>
				</label>
			</li>

			<li class="course-list-display-option">
				<input id="course-list-display_list" type="radio" name="course_list_display" value="list"<?= ($display_mode == 'list') ? ' checked="checked"' : '' ?> />
				<label for="course-list-display_list" title="<?= __('List view') ?>">
					<span class="sr-only"><?= __('List view') ?></span>
                    <span class="fa fa-bars"></span>
				</label>
			</li>

			<li class="course-list-display-option">
				<input id="course-list-display_grid" type="radio" name="course_list_display" value="grid"<?= ($display_mode == 'grid') ? ' checked="checked"' : '' ?> />
				<label for="course-list-display_grid" title="<?= __('Grid view') ?>">
					<span class="sr-only"><?= __('Grid view') ?></span>
                    <span class="fa fa-th"></span>
				</label>
			</li>
		</ul>
	</div>
</div>

<div class="course-list course-list--<?= $display_mode ?>">
    <?php
    if (isset($search) && isset($search['data'])) {
        foreach ($search['data'] as $result) {

            if (!empty($page_data) && $page_data['layout'] == 'course_list2') {
                include 'snippets/search_result2.php';
            } else {
                include 'snippets/search_result.php';
            }
        }
    }
    ?>
</div>

<?php
if (!empty($search) && empty($events_only)) {
    include Kohana::find_file('views', '/frontend/snippets/contact_organizer_modal');
    include Kohana::find_file('views', '/frontend/snippets/contact_venue_modal');
}
?>

<div class="pagination-wrapper">
	<input type="hidden" id="current_page" value="<?= $current_page ?>" />

    <?php if ($actual_pages_count > 1): ?>
        <ul class="pagination" role="navigation" aria-label="Pagination" id="search_results-pagination">
            <li class="pagination-prev">
                <a
                    href="#"
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
                            href="#"
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
                    href="#"
                    <?= $next_button_active ? '' : ' class="disabled"' ?>
                    data-page="<?= $current_page + 1 ?>"
                    >
                    <span class="sr-only"><?= __('Next') ?></span>
                </a>
            </li>
        </ul>
    <?php endif; ?>
</div>
