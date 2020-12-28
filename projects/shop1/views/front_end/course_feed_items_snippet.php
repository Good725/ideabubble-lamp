<?php
$location_id           = ( ! empty($_GET['location'])) ? $_GET['location'] : FALSE;
$product_enquiry       = (Settings::instance()->get('product_enquiry') == 1);
$pagination_count      = ceil($courses['total_count'] / 10);
$course_enquiry_button = Settings::Instance()->get('course_enquiry_button');
$locations             = Model_Locations::get_locations_only();
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

	<div class="search_results-top">
		<div class="search_results-report">
			<?php if (isset($courses['results_found'])): ?>
				<p><?= $courses['results_found'] ?><?= ( ! empty($_GET['search'])) ? ' for <strong>'.Kohana::sanitize($_GET['search']).'</strong>' : '' ?>.</p>
			<?php endif; ?>
		</div>

		<form action="/course-list.html" class="search_results-location_selector">
			<input type="hidden" name="search" value="<?= isset($_GET['search']) ? $_GET['search']  : '' ?>" />

			<p>
				<label class="sr-only" for="search_results-location"><?= __('Filter by location') ?></label>
				<select class="search_results-location" id="search_results-location" name="location" onchange="this.form.submit();">
					<option value="">Select Location</option>
					<?php foreach ($locations as $location): ?>
						<option
							value="<?= $location['id'] ?>"
							<?= ($location['id'] == $location_id) ? ' selected="selected"' : '' ?>
							><?= $location['name'] ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</form>
	</div>

    <?php for ($i = 0; $i < count($courses['data']) AND $i < 10; $i++): ?>
        <?php
		$item          = $courses['data'][$i];
		$images        = Model_Courses::get_images($item['id'],1,0,'id','asc');
		$filename      = ((isset($images[0]) AND isset($images[0]['file_name']))) ? $images[0]['file_name'] : 'no_image_available.png';
		$src           = 'shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/'.$filename;
		$thumbnail_src = 'shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/_thumbs/'.$filename;
		?>

		<?php if ($layout == 'course_list_grid'): ?>
			<div class="course_result grid_course_result">
				<h3><a href="/course-detail.html?id=<?= $item['id'] ?>"><?= $item['title'] ?></a></h3>
				<div class="grid_course_result_summary">
					<a href="/course-detail.html?id=<?= $item['id'] ?>"><?= strip_tags(strtok($item['summary'], "\n")) ?></a>
				</div>

				<?php if (isset($images[0]['file_name'])): ?>
					<a href="/course-detail.html?id=<?= $item['id'] ?>"><img src="<?= $src ?>" alt="" class="course_result_image" /></a>
				<?php endif; ?>

				<form action="/checkout.html" method="get" id="course_result_form_<?= $item['id'] ?>">
					<input type="hidden" name="schedule_id" value=""/>

					<div>
						<label class="course_result_label" for="course_<?= $item['id'] ?>_start_date">Time &amp; date</label>
						<select class="course_result_start_date validate[required]" id="course_<?= $item['id'] ?>_start_date" data-id="<?= $item['id'] ?>" name="event_id"<?= (count($item['schedules']) < 1) ? ' style="visibility:hidden;' : '' ?>>
                            <?php include 'snippets/search_results_schedule_dropdown.php'; ?>
                        </select>
					</div>

					<input type="hidden" name="id" value="<?= $item['id'] ?>" />
					<div class="course_result_buttons">
						<a href="/course-detail.html?id=<?= $item['id'] ?>" class="primary_button view_course_button">View Course</a>
						<?php if ($course_enquiry_button): ?>
							<a href="/contact-us.html?course_id=<?= $item['id'] ?>" type="button" class="secondary_button">Enquire</a>
						<?php endif; ?>
						<button type="submit" class="booking_button course_result_booking_button" data-form="course_result_form_<?= $item['id'] ?>" disabled="disabled">Book Now</button>
					</div>
				</form>
			</div>

		<?php else: ?>
			<div class="course_result">

				<div class="course_result_image">
					<?php if (file_exists($thumbnail_src) OR file_exists($src)): ?>
						<a href="<?= $src ?>"><img src="<?= file_exists($thumbnail_src) ? $thumbnail_src : $src ?>" alt="" /></a>
					<?php endif; ?>
				</div>

				<div class="course_result_data">
					<div class="course_result_price_wrapper" id="course_result_<?= $item['id'] ?>_price_wrapper">
						Fee: <span class="course_result_price"></span>
						<?php if (Request::user_agent('mobile')): ?>
							<div class="course_result_discount_info"></div>
						<?php else: ?>
							<span class="popover_icon">i<span class="popover course_result_discount_info"></span></span>
						<?php endif; ?>
					</div>

					<form action="/checkout.html" method="get" id="course_result_form_<?= $item['id'] ?>">
						<input type="hidden" name="id" value="<?= $item['id'] ?>" />
						<input type="hidden" name="schedule_id" value=""/>
						<h3><?= $item['title'] ?></h3>

						<div>
							<label class="course_result_label" for="course_<?= $item['id'] ?>_start_date">Time &amp; date</label>
							<select class="course_result_start_date validate[required]" id="course_<?= $item['id'] ?>_start_date" data-id="<?= $item['id'] ?>" name="event_id"<?= (count($item['schedules']) < 1) ? ' style="visibility:hidden;' : '' ?>>
								<?php include 'snippets/search_results_schedule_dropdown.php'; ?>
							</select>
						</div>

						<div class="course_result_summary">
							<?= $item['summary'] ?>
						</div>

						<div class="course_result_buttons">
							<a href="/course-detail.html?id=<?= $item['id'] ?>" class="primary_button view_course_button">View Course</a>
							<?php if ($course_enquiry_button): ?>
								<a href="/contact-us.html?course_id=<?= $item['id'] ?>" type="button" class="secondary_button">Enquire</a>
							<?php endif; ?>
							<button type="submit" class="booking_button course_result_booking_button" data-form="course_result_form_<?= $item['id'] ?>" disabled="disabled">Book Now</button>
						</div>
					</form>
				</div>
			</div>
		<?php endif; ?>
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
