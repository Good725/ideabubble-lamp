<?php
$location              = (isset($_GET['location']) AND strlen($_GET['location']) > 0) ? $_GET['location'] : FALSE;
$product_enquiry       = (Settings::instance()->get('product_enquiry') == 1);
$results_per_page =     Settings::instance()->get('courses_results_per_page');
$pagination_count      = ceil($courses['total_count'] / $results_per_page);
$course_enquiry_button = Settings::Instance()->get('course_enquiry_button');
$url                   = strtok($_SERVER['REQUEST_URI'], '?');
?>
<?php if (isset($courses['data']) AND ! is_null($courses['data']) AND ! empty($courses['data'])): ?>
    <?php if ($pagination_count > 1): ?>
        <?php
        $params = $_GET;
        $current_page = (isset($courses['page']) AND $courses['page']) ? $courses['page'] : 1;
        ?>
        <div class="filter_pagination">
            <div class="filter_pagination-links">
                <?php $params['page'] = $current_page - 1 ?>
                <a class="filter_pagination-prev<?= ($params['page'] < 1) ? ' disabled' : ''; ?>" href="<?= $url.'?'.http_build_query($params) ?>">
                    <span>Previous</span>&zwnj;
                </a>

                <ul>
                    <?php for ($params['page'] = 1; $params['page'] <= $pagination_count; $params['page']++): ?>
                        <li>
                            <a href="<?= $url.'?'.http_build_query($params) ?>"<?= ($current_page == $params['page']) ? ' class="current"' : ''; ?>><?= $params['page'] ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>

                <?php $params['page'] = $current_page + 1 ?>
                <a class="filter_pagination-next<?= ($params['page'] > $pagination_count) ? ' disabled' : ''; ?>" href="<?= $url.'?'.http_build_query($params) ?>">
                    &zwnj;<span>Next</span>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($courses['results_found'])): ?>
        <p><?= $courses['results_found'] ?><?= (isset($_GET['search'])) ? ' for <strong>'.Kohana::sanitize($_GET['search']).'</strong>' : '' ?>.</p>
    <?php endif; ?>

    <?php for ($i = 0; $i < count($courses['data']) AND $i < $results_per_page; $i++): ?>
        <?php
		$item          = $courses['data'][$i];
		$images        = Model_Courses::get_images($item['id'],1,0,'id','asc');
		$filename      = ((isset($images[0]) AND isset($images[0]['file_name']))) ? $images[0]['file_name'] : 'no_image_available.png';
		$src           = 'shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/'.$filename;
		$thumbnail_src = 'shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/_thumbs/'.$filename;
		?>

		<div class="course_result">

			<h2><?= $item['title'] ?></h2>

			<div class="course-discount popover_icon" style="display: none;">
				i<span class="popover course-discount_info"></span>
			</div>

			<div class="course_result-inner">
				<div class="course_result-image">
					<?php if (file_exists($thumbnail_src) OR file_exists($src)): ?>
						<a href="<?= $src ?>"><img src="<?= file_exists($thumbnail_src) ? $thumbnail_src : $src ?>" alt="" /></a>
					<?php endif; ?>
				</div>

				<div class="course_result-data">

					<form action="/course-checkout.html" method="get" class="validate-on-submit" id="course_result-form_<?= $item['id'] ?>">
						<input type="hidden" name="id" value="<?= $item['id'] ?>" />
                        <input type="hidden" name="schedule_id" value="" />

						<div class="course_result-row">
							<div class="course_result-schedules">
								<div class="select">
									<label class="sr-only" for="course_<?= $item['id'] ?>_start_date">Schedule ( Location - Time - Date)</label>
									<select class="course_result-start_date validate[required]" id="course_<?= $item['id'] ?>_start_date" data-id="<?= $item['id'] ?>" name="event_id"<?= (count($item['schedules']) < 1) ? ' style="visibility:hidden;"' : '' ?>>
										<?php if (is_array($item['schedules']) AND count($item['schedules']) > 0): ?>
											<option value="">Schedule ( Location - Time - Date)</option>
											<?php foreach ($item['schedules'] as $schedule): ?>
												<?php if ($schedule['booking_type'] == 'One Timeslot') { ?>
													<?php foreach($schedule['timeslots'] as $timeslot) { ?>
												<option value="<?= $timeslot['id'] ?>" data-schedule_id="<?= $schedule['id'] ?>"><?= $schedule['location'] . ' - ' . date('H:i', strtotime($timeslot['datetime_start'])) . ' ' . date('D j F Y', strtotime($timeslot['datetime_start']));?></option>
													<?php } ?>
												<?php } else { ?>
												<option value="<?= $schedule['event_id'] ?>" data-schedule_id="<?= $schedule['id'] ?>"><?= $schedule['location'] . ' - ' .
														(date('H:i', strtotime($schedule['start_date'])) != '00:00' ? date('H:i', strtotime($schedule['start_date'])) : '') .
														' ' . date('D j F Y', strtotime($schedule['start_date'])) .
													(( ! is_null($schedule['repeat'])) ? ' - ' . $schedule['repeat']: '');
													?></option>
												<?php } ?>
											<?php endforeach; ?>
										<?php else: ?>
											<option value="">No dates and times defined</option>
										<?php endif; ?>
									</select>
								</div>
							</div>

							<div class="course_result-price-wrapper" id="course_result_<?= $item['id'] ?>-price-wrapper">
								<div class="price-wrapper">Fee: <span class="course_result-price"></span></div>
							</div>
						</div>

						<div class="course_result-row">
							<div class="course_result-summary"><?= trim($item['summary']) ?></div>
						</div>

						<div class="course_result-row course_result-buttons">
							<a href="/course-detail.html?id=<?= $item['id'] ?>" class="primary_button view_course_button">View Course</a>
							<?php if ($course_enquiry_button): ?>
								<a href="/contact-us.html?course_id=<?= $item['id'] ?>" type="button" class="course_enquiry_button">Enquire</a>
							<?php endif; ?>
							<button type="submit" class="booking_button" data-form="course_result-form_<?= $item['id'] ?>">Book</button>
						</div>
					</form>
				</div>
			</div>
		</div>
    <?php endfor; ?>

    <?php if ($pagination_count > 1): ?>
        <div class="filter_pagination">
            <div class="filter_pagination-links">
                <?php $params['page'] = $current_page - 1 ?>
                <a class="filter_pagination-prev<?= ($params['page'] < 1) ? ' disabled' : ''; ?>" href="<?= $url.'?'.http_build_query($params) ?>">
                    <span>Previous</span>&zwnj;
                </a>

                <ul>
                    <?php for ($params['page'] = 1; $params['page'] <= $pagination_count; $params['page']++): ?>
                        <li>
                            <a href="<?= $url.'?'.http_build_query($params) ?>"<?= ($current_page == $params['page']) ? ' class="current"' : ''; ?>><?= $params['page'] ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>

                <?php $params['page'] = $current_page + 1 ?>
                <a class="filter_pagination-next<?= ($params['page'] > $pagination_count) ? ' disabled' : ''; ?>" href="<?= $url.'?'.http_build_query($params) ?>">
                    &zwnj;<span>Next</span>
                </a>
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="message">
        <p>No results found.</p>
    </div>
<?php endif; ?>
