<?php
$user = Auth::instance()->get_user();
$contact_id = 0;
if (@$user['id']) {
	if (Auth::instance()->has_access('contacts3_limited_view')) {
		$contact = current(Model_Contacts3::get_contact_ids_by_user($user['id']));
		$contact_id = $contact['id'];
	}
}
?>

<?php include 'template_views/header.php'; ?>

<div class="row">
    <?php
    $current_step = 'details';
    include 'views/checkout_progress.php'
    ?>

	<div class="page-content">
		<?= $page_data['content'] ?>
		<?php
		$last_search_parameters = Session::instance()->get('last_search_params');
        $course_object = ORM::factory('Course')->where('id', '=', @$_GET['id'])->find_published();
        $course = ( ! empty($_GET['id'])) ? Model_Courses::get_detailed_info((int)$_GET['id'], true, true) : NULL;
		if ( ! empty($course))
		{
			$schedule_id       = ( ! empty($_GET['schedule_id']) AND $_GET['schedule_id'] > 0) ? $_GET['schedule_id'] : NULL;
			$course_media_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'courses');
			$product_enquiry   = (Settings::instance()->get('course_enquiry_button') == 1);

            $banner_filename = '';
			if (!empty($course['banner'])) {
                $banner_filename = $course['banner'];
            } elseif (!empty($course['banners']) && isset($course['banners'][0])) {
                $banner_filename = $course['banners'][0]['filename'];
            }
		}
		?>
		<?php if ( ! empty($course)): ?>
            <?php include 'template_views/course_details_options.php'; ?>

            <div class="course-header">
				<h1><?= $course['title'] ?> - <small>(ID: #<?= $course['id'] ?>)</small></h1>
				<a class="course-results-link" href="/course-list.html?<?=$last_search_parameters != null ? http_build_query($last_search_parameters) : ''?>">
					<span class="link-text"><?= __('Back to search results') ?></span>
					<span class="fa fa-chevron-right"></span></a>
			</div>

			<?php if ( ! empty($banner_filename)): ?>
				<div class="course-banner">
					<img class="course-banner-image" src="<?= $course_media_path.$banner_filename ?>" alt="" />
				</div>
			<?php endif; ?>

            <div class="course-details-summary-wrapper" id="fixed_sidebar-positioner">
                <div class="course-details-summary">
                    <div class="hidden" id="course-details-timeslots-wrapper">
                        <h2><?= __('Location') ?></h2>

                        <div id="course-details-timeslots-location"></div>

                        <h2><?= __('Dates') ?></h2>

                        <div id="course-details-timeslots-dates"></div>
                    </div>

                    <?php if (isset($course['summary']) && trim($course['summary'])): ?>
                        <h2><?= __('Course Summary') ?></h2>

                        <?= $course['summary'] ?>
                    <?php endif; ?>

                    <?php if (isset($course['description']) AND trim($course['description'])): ?>
                        <h2><?= __('Course Description') ?></h2>

                        <?= $course['description'] ?>
                    <?php endif; ?>
                </div>

                <div class="row fixed_sidebar-wrapper" id="fixed_sidebar-wrapper">
                    <div class="fixed_sidebar" id="fixed_sidebar">
                        <form
                            method="post"
                            action="#" data-checkout-type="<?= Settings::instance()->get('checkout_customization')?>"
                            id="selectcform"
                            data-action="<?= Model_Plugin::is_enabled_for_role('Administrator', 'Contacts3') ? '/booking-form' : '/checkout' ?>"
                            >
                            <div class="fixed_sidebar-header" id="fixed_sidebar-header">
                                <?php if ($cheapest !== false): ?>
                                    <div class="course-details-price course-details-price--from">
                                        <strong class="price">From &euro;<?= number_format($cheapest, 2) ?></strong>
                                    </div>
                                <?php endif; ?>

                                <div class="course-details-price course-details-price--normal hidden">
                                    <s><strong class="price">&euro;</strong></s>
                                </div>

                                <div class="course-details-price course-details-price--online">
                                    <strong class="price"></strong>
                                </div>
                            </div>

                            <div class="fixed_sidebar-content" id="fixed_sidebar-content">
                                <?php if ($course['year'] || $course['level']): ?>
                                    <div><?= $course['year'] ?> <?= $course['level'] ?></div>

                                    <?php if ($course['category']): ?>
                                        <div><?= $course['category'] ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($course['type']): ?>
                                    <?= $course['type'] ?>
                                <?php endif; ?>

                                <label for="schedule_selector">Select session</label>

                                <div class="select">
                                    <select name="interested_in_schedule_id" class="form-input validate[required]" id="schedule_selector">
                                        <option value=""><?= __('Select Schedule') ?></option>
                                        <?= $schedule_options ?>
                                    </select>
                                </div>
                            </div>

                            <div class="hidden fixed_sidebar-content" id="display_group_booking_input">
                                <label for="num_delegates"><?=__('Number of delegates')?></label>
                                <input type="number" min="1" class="form-input" name="num_delegates" id="num_delegates" value="" />
                            </div>
                            <div class="fixed_sidebar-footer">
                                <?php if ( ! empty($course['schedules'])): ?>
                                    <?php if (Settings::instance()->get('course_enquiry_button') == 1): ?>
                                        <button type="submit" formaction="/contact-us.html" formmethod="get" class="button button--enquire"><?= __('Enquire Now') ?></button>
                                    <?php endif; ?>
                                    <?php if ($account_bookings && $contact_id): ?>
                                        <?php $in_wishlist = count(Model_KES_Wishlist::search(array('contact_id' => $contact_id, 'schedule_id' => $schedule_id))); ?>

                                        <button class="button course-banner-button cl_bg wishlist_add<?= $in_wishlist == 0 ? '' : ' hidden' ?>" data-contact_id="<?= $contact_id ?>" data-schedule_id="<?=$schedule_id?>"><?= __('Add To Wishlist') ?></button>

                                        <button class="button button--cl_remove course-banner-button cl_bg wishlist_remove<?= $in_wishlist == 0 ? ' hidden' : '' ?>" data-contact_id="<?=$contact_id?>" data-schedule_id="<?=$schedule_id?>"><?= __('Remove From Wishlist') ?></button>
                                    <?php endif; ?>
                                    <?php if (Settings::instance()->get('courses_enable_bookings') == 1): ?>
                                        <button
                                            type="submit"
                                            class="button button--book"
                                            data-title="<?= urlencode($course['title']) ?>"
                                            id="book-course"
                                            data-id="0"
                                            disabled="disabled"
                                            ><?= __('Book Now') ?>
                                        </button>
                                    <?php endif; ?>
                                    <button type="submit" formaction="/add-to-waitlist.html" id="add_to_waitlist_button" formmethod="get" class="button cl_bg w-100 hidden"><?= __('Add to Waitlist') ?></button>

                                <?php endif; ?>
                                <?php if ($course_object->file_id || $course_object->use_brochure_template): ?>
                                    <div class="course-details-menu-footer">
                                        <button type="button" class="button--plain" data-toggle="modal" data-target="#course-details-brochure-modal">
                                            Download Brochure <span class="fa fa-file-pdf-o"></span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

		<?php endif; ?>
	</div>
</div>
<?php ob_start(); ?>
<form id="brochure_download" class="" action="/frontend/formprocessor/course_brochure_download" method="post">
    <script>
        $("#brochure_download").on("submit", function(){
            if ($(this).validationEngine('validate')) {
                $("#course-details-brochure-modal").modal("hide");
            } else {
                return false;
            }

        });
    </script>
    <input type="hidden" name="course_id" value="<?=$course['id']?>" />
    <input type="hidden" name="schedule_id" value="" />

    <div class="form-row">
        <img src="<?= Model_Media::get_image_path(Settings::instance()->get('site_logo'), 'logos') ?>" width="200" alt="" class="d-block m-auto" />

        <h6 class="mb-2 text-primary" style="font-size: 1rem; line-height: 1.375;">To download a copy of our course brochure, please fill in your details:</h6>
    </div>

    <div class="form-row">
        <?= Form::ib_input('First name', 'first_name', null, ['class' => 'validate[required]', 'id' => 'course-details-brochure-first_name']) ?>
    </div>

    <div class="form-row">
        <?= Form::ib_input('Last name', 'last_name', null, ['class' => 'validate[required]', 'id' => 'course-details-brochure-last_name']) ?>
    </div>

    <div class="form-row">
        <?= Form::ib_input('Email', 'email', null, ['class' => 'validate[required,custom[email]]', 'id' => 'course-details-brochure-email']) ?>
    </div>

    <div class="form-row">
        <?= Form::ib_input('Phone number', 'telephone', null, ['id' => 'course-details-brochure-telephone']) ?>
    </div>

    <div class="form-row">
        <p class="mb-0" style="font-size: 11px;">
            <?= __(
                'By submitting this form, you agree that we may use your data to contact you with information related to this specific course. To learn more, see our {{privacy policy}}.',
                [
                    '$1' => Settings::instance()->get('company_name'),
                    '{{' => !empty( Settings::instance()->get('company_site')) ? '<a href="' . Settings::instance()->get('company_site') . '/privacy-statement" target="_blank">' : '<a href="/privacy-policy">',
                    '}}' => '</a>'
                ]
            ) ?>
        </p>
    </div>

    <div>
        <button type="submit" class="button bg-success w-100">Submit</button>
    </div>
</form>
<?php $modal_body = ob_get_clean(); ?>
<?php
echo View::factory('front_end/snippets/modal')
    ->set('class', 'course-details-brochure-modal')
    ->set('id', 'course-details-brochure-modal')
    ->set('width', '395px')
    ->set('body', $modal_body)
;
?>
<?php include 'views/footer.php'; ?>
