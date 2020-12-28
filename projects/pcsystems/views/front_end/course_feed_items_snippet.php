<?php
$location         = (isset($_GET['location']) AND strlen($_GET['location']) > 0) ? $_GET['location'] : FALSE;
$product_enquiry  = (Settings::instance()->get('product_enquiry') == 1);
$pagination_count = ceil($courses['total_count'] / 10);
?>
<?php if (isset($courses['data']) AND ! is_null($courses['data']) AND ! empty($courses['data'])): ?>
    <?php if ($pagination_count > 1): ?>
        <div class="filter_pagination">
            <a class="prev" href="#" onclick="filter_offset('prev')">prev</a>
            <a class="next" href="#" onclick="filter_offset('next')">next</a>
            <ul>
                <?php for($i = 1; $i <= $pagination_count; $i++): ?>
                    <li><a href="#" data-page="<?= $i ?>"<?= (isset($courses['page']) AND $courses['page'] == $i) ? ' class="current"' : ''; ?> onclick="filter_offset(<?= ($i-1)*10 ?>)"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($courses['results_found'])): ?>
        <p><?= $courses['results_found'] ?><?= (isset($_GET['search'])) ? ' for <strong>'.Kohana::sanitize($_GET['search']).'</strong>' : '' ?>.</p>
    <?php endif; ?>

    <?php for ($i = 0; $i < count($courses['data']) AND $i < 10; $i++): ?>
        <?php $item = $courses['data'][$i]; ?>
		<div class="course_result">

			<div class="course_result_image">
				<a href="#">
					<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'courses','', '')?>/_thumbs/basic_first_aid.jpg" alt="" />
				</a>
				<span class="course_result_price_wrapper" style="display:none;">Fee: &euro;<span class="course_result_price"></span> <em>i</em></span>
			</div>

			<div class="course_result_data">
				<form action="/checkout.html" method="get">
					<input type="hidden" name="id" value="<?= $item['id'] ?>" />
					<h3><?= $item['title'] ?></h3>

					<div>
						<label class="course_result_label" for="course_<?= $item['id'] ?>_location">Location</label>
						<select id="course_<?= $item['id'] ?>_location" name="location">
							<?php if (is_array($item['schedules']) AND count($item['schedules']) > 0): ?>
								<?php $location_items = array(); ?>
								<option value="">-- All Locations --</option>
								<?php foreach ($item['schedules'] as $sval): ?>
									<?php $location_items[] = array('id' => $sval['location_id'], 'name' => $sval['location']); ?>
								<?php endforeach; ?>

								<?php foreach (array_unique($location_items) as $location_item): ?>
									<option value="<?= $location_item['id'] ?>"<?= ($location_item['id'] == $location) ? ' selected="selected"' : '' ?>>
										<?= $location_item['name'] ?>
									</option>
								<?php endforeach; ?>
							<?php else: ?>
								<option value="">No locations defined</option>
							<?php endif; ?>
						</select>
					</div>

					<div>
						<label class="course_result_label" for="course_<?= $item['id'] ?>_start_date">Time &amp; date</label>
						<select class="validate[required]" id="course_<?= $item['id'] ?>_start_date" name="event_id"<?= (count($item['schedules']) < 1) ? ' style="visibility:hidden;' : '' ?>>
							<?php if (is_array($item['schedules']) AND count($item['schedules']) > 0): ?>
								<option value="">-- Time &amp; Date --</option>
								<?php foreach ($item['schedules'] as $schedule): ?>
									<option value="<?= $schedule['event_id'] ?>" data-schedule_id="<?= $schedule['id'] ?>">
										<?= $schedule['location'].' - '.date('H:i D j F Y', strtotime($schedule['start_date'])).(( ! is_null($schedule['repeat'])) ? ' - '.$schedule['repeat']: ''); ?>
									</option>
								<?php endforeach; ?>
							<?php else: ?>
								<option value="">No dates and times defined</option>
							<?php endif; ?>
						</select>
					</div>

					<div class="course_result_summary">
						<?= $item['summary'] ?>
					</div>

					<div class="course_result_buttons">
						<a href="/course-detail.html?id=<?= $item['id'] ?>" class="primary_button view_course_button">View Course</a>
						<a href="/contact-us.html?course_id=<?= $item['id'] ?>" type="button" class="secondary_button">Enquire</a>
						<button type="submit" class="booking_button">Book Now</button>
					</div>
				</form>
			</div>
		</div>
    <?php endfor; ?>

    <?php if ($pagination_count > 1): ?>
        <div class="filter_pagination">
            <a class="prev" href="#" onclick="filter_offset('prev')">prev</a>
            <a class="next" href="#" onclick="filter_offset('next')">next</a>
            <ul>
                <?php for($i = 1; $i <= $pagination_count; $i++): ?>
                    <li><a href="#" data-page="<?= $i ?>" <?= (isset($courses['page']) AND $i == $courses['page']) ? ' class="current"' : ''; ?>onclick="filter_offset(<?= ($i-1)*10 ?>)"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="message">
        <p>No results found.</p>
    </div>
<?php endif; ?>