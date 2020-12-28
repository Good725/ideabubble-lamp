<?php
$user                  = Auth::instance()->get_user();
$apply_now_link        = trim(Settings::instance()->get('course_apply_link'));
$apply_now_is_external = IbHelpers::is_external($apply_now_link);
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
	<div class="page-content">
		<?= $page_data['content'] ?>
		<?php
		$last_search_parameters = Session::instance()->get('last_search_params');
		$course = ( ! empty($_GET['id'])) ? Model_Courses::get_detailed_info((int)$_GET['id'], true, true) : NULL;
		if ( ! empty($course))
		{
			$schedule_id       = ( ! empty($_GET['schedule_id']) AND $_GET['schedule_id'] > 0) ? $_GET['schedule_id'] : NULL;
			$course_media_path  = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'courses/');
			$banner_filename   = ( ! empty($course['banners']) AND isset($course['banners'][0])) ? $course['banners'][0]['filename'] : 'course-banner-placeholder.png';
			$product_enquiry   = (Settings::instance()->get('course_enquiry_button') == 1);
		}
		?>
		<?php if ( ! empty($course)): ?>

			<div class="course-header">
				<h1><?= $course['title'] ?>  <!-- - <small>(ID: #<?= $course['id'] ?>)</small> --></h1>
				<a class="course-results-link" href="/course-list.html?<?=$last_search_parameters != null ? http_build_query($last_search_parameters) : ''?>">
					<span class="link-text"><?= __('Back to search results') ?></span>
					<span class="fa fa-angle-right"></span></a>

			</div>
			<div class="course-wrap">
				<?php if ( ! empty($banner_filename)): ?>
				<div class="course-banner">
					<?php if ( ! empty($course['images'][0])): ?>
						<img src="<?= $course_media_path.$course['images'][0]['filename'] ?>" alt="" />
					<?php else: ?>
						<img src="<?= $course_media_path ?>course-placeholder.png" alt="" />
					<?php endif; ?>

					<!-- <div class="course-banner-overlay">
						<form method="post" action="#" id="selectcform">
							<div class="course-banner-overlay-row">
								<h2 class="course-banner-overlay-title">
									<?= $course['year'] ?> <?= $course['level'] ?><br />
									<?= $course['category'] ?><br />
									<?= $course['type'] ?>
								</h2>
								<div>
									<?php if ( ! empty($course['schedules'])): ?>
										<button class="button course-banner-button" data-title="<?= urlencode($course['title']) ?>" id="enquire-course" data-id="0" disabled="disabled"><?= __('Enquire Now') ?></button>
										<?php
										if (@$contact_id) {
											$in_wishlist = count(Model_KES_Wishlist::search(array('contact_id' => $contact_id, 'schedule_id' => $schedule_id)));
										?>
										<button class="button course-banner-button cl_bg wishlist_add <?=$in_wishlist == 0 ? '' : 'hidden'?>" data-contact_id="<?=$contact_id?>" data-schedule_id="<?=$schedule_id?>"><?= __('Add To Wishlist') ?></button>
										<button class="button button--cl_remove course-banner-button cl_bg wishlist_remove <?=$in_wishlist == 0 ? 'hidden' : ''?>" data-contact_id="<?=$contact_id?>" data-schedule_id="<?=$schedule_id?>"><?= __('Remove From Wishlist') ?></button>
										<?php
										}
										?>

										<?php if ( ! $product_enquiry): ?>
											<button class="button button--book course-banner-button" data-title="<?= urlencode($course['title']) ?>" id="book-course" data-id="0" disabled="disabled"><?= __('Book Now') ?></button>
										<?php endif; ?>

									<?php endif; ?>
								</div>
							</div>

							<?php if ( ! empty($course['schedules'])): ?>
								<div class="course-banner-overlay-row">
									<label class="sr-only" for="schedule_selector"><?= __('Select schedule') ?></label>

									<select class="form-input validate[required]" id="schedule_selector">
										<option value=""><?= __('Select Schedule') ?></option>
	                                    <?php foreach ($course['schedules'] as $schedule): ?>
	                                        <?php
											$start_date = ((isset($schedule['timeslots']) AND isset($schedule['timeslots'][0])) ? $schedule['timeslots'][0]['datetime_start'] : $schedule['start_date']);
	                                        $end_date   = ((isset($schedule['timeslots']) AND isset($schedule['timeslots'][0])) ? $schedule['timeslots'][0]['datetime_end']   : $schedule['end_date']);
	                                        ?>
	                                        <?php if ($schedule['repeat']): ?>
	                                            <?php
	                                            $duration_in_seconds = date('U', strtotime($end_date)) - date('U', strtotime($start_date));
												$duration_in_seconds = strtotime($end_date) - strtotime($start_date);
												$duration_h = floor($duration_in_seconds / 3600);
												$duration_m = (($duration_in_seconds % 3600) / 60);
												$duration = ($duration_in_seconds > 0) ? ($duration_h . ($duration_m == 30 ? '.5' : '') . "h " . ($duration_m > 0 && $duration_m != 30 ? $duration_m . 'm' : '')) : FALSE;
	                                            ?>

	                                            <option value="<?= $schedule['id'] ?>" data-fee="<?= $schedule['fee_amount'] ?>" data-event_id="<?=$schedule['event_id']?>">
	                                                <?= date('D - H:i', strtotime($start_date)) ?>
	                                                <?= $schedule['location']     ? ' - '.$schedule['location']     : '' ?>
	                                                <?= ( ! empty($schedule['trainer_name'])) ? ' - '.@$schedule['trainer_name'] : '' ?>
	                                                <?= $duration                 ? ' - '.$duration                 : '' ?>
	                                                <?= $schedule['fee_amount']   ? ' - €'.$schedule['fee_amount']  : '' ?>
	                                            </option>
	                                        <?php else: ?>
	                                            <option value="<?= $schedule['id'] ?>" data-fee="<?= $schedule['fee_amount'] ?>" data-event_id="<?=$schedule['event_id']?>">
	                                                <?= date('D - d/m/Y - H:i', strtotime($start_date)) ?>
	                                                <?= $schedule['location']     ? ' - '.$schedule['location']     : '' ?>
	                                                <?= ( ! empty($schedule['trainer_name'])) ? ' - '.@$schedule['trainer_name'] : '' ?>
	                                                <?= $schedule['fee_amount']   ? ' - €'.$schedule['fee_amount']  : '' ?>
	                                            </option>
	                                        <?php endif; ?>
	                                    <?php endforeach; ?>
									</select>
									<div class="price_wrapper">
										Price: <span class="price"></span>
									</div>
								</div>
							<?php endif; ?>
						</form>
					</div> -->

				</div>
				<?php endif; ?>
				<div class="course-details">
					<?php if (isset($course['summary']) AND trim($course['summary'])): ?>
						<h2><?= __('Course Summary') ?></h2>
						<?= $course['summary'] ?>
					<?php endif; ?>

					<?php if (isset($course['description']) AND trim($course['description'])): ?>
						<h2><?= __('Course Description') ?></h2>
						<?= $course['description'] ?>
					<?php endif; ?>
                    <?php if ($apply_now_link): ?>
                        <a href="<?= $apply_now_link ?>"<?= $apply_now_is_external ? ' target="_blank"' : '' ?> class="button button--book course-banner-button"><?= __('Apply Now') ?></a>
                    <?php else: ?>
                        <button class="button button--book course-banner-button" data-title="<?= urlencode($course['title']) ?>" id="book-course" data-id="0" disabled="disabled"><?= __('Apply Now') ?></button>
                    <?php endif; ?>
				</div>
			</div>

		<?php endif; ?>
	</div>
</div>

<?php include 'template_views/footer.php'; ?>
