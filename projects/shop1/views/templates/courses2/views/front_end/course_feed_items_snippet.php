<?php
$location              = (isset($_GET['location']) AND strlen($_GET['location']) > 0) ? $_GET['location'] : FALSE;
$product_enquiry       = (Settings::instance()->get('product_enquiry') == 1);
$pagination_count      = ceil($courses['total_count'] / 10);
$current_page          = isset($_GET['page']) ? $_GET['page'] : '';
$query_string          = str_replace('&page='.$current_page, '', str_replace('?page='.$current_page, '', URL::query()));
$course_enquiry_button = Settings::Instance()->get('course_enquiry_button');

$apply_now_link = Settings::instance()->get('course_apply_link');
preg_match_all('/(\w+)=(.*)/',$apply_now_link, $apply_now_fields); // get query string values, so we can print them as hidden form fields
?>
<?php if (isset($courses['data']) AND ! is_null($courses['data']) AND ! empty($courses['data'])): ?>
    <?php if ($pagination_count > 1): ?>
        <div class="filter_pagination text-center">
			<?php if ($current_page > 1): ?>
				<a class="prev" href="<?= Request::detect_uri().$query_string ?><?= $query_string ? '&' : '?' ?>page=<?= $current_page - 1 ?>">prev</a>
			<?php else: ?>
				<a class="prev disabled" href="#">prev</a>
			<?php endif; ?>
            <ul class="list-inline">
                <?php for ($i = 1; $i <= $pagination_count; $i++): ?>
                    <li>
						<a href="<?= Request::detect_uri().$query_string ?><?= $query_string ? '&' : '?' ?>page=<?= $i ?>"
							<?= ($current_page == $i) ? ' class="current"' : ''; ?>><?= $i ?></a>
					</li>
                <?php endfor; ?>
            </ul>
			<?php if ($current_page != $pagination_count): ?>
				<a class="next" href="<?= Request::detect_uri().$query_string ?><?= $query_string ? '&' : '?' ?>page=<?= $current_page + 1 ?>">next</a>
			<?php else: ?>
				<a class="next disabled" href="#">next</a>
			<?php endif; ?>
        </div>
    <?php endif; ?>

	<div class="course-results">
		<?php
		$project_folder = Kohana::$config->load('config')->project_media_folder;
		$url_path       = 'shared_media/'.$project_folder.'/media/photos/courses/';
		$code_path      = PROJECTPATH.'www/'.$url_path;
		?>
		<?php for ($i = 0; $i < count($courses['data']) AND $i < 10; $i++): ?>
			<?php
			// todo: make a model function for getting the thumbnail
			$item     = $courses['data'][$i];
			$images   = Model_Courses::get_images($item['id'],1,0,'id','asc');
			$filename = (isset($images[0]) AND isset($images[0]['file_name'])) ? $images[0]['file_name'] : '';

			if ($filename AND file_exists($code_path.'_thumbs/'.$filename)) {
				$src = $url_path.'_thumbs/'.$filename;
			}
			elseif ($filename AND file_exists($code_path.$filename)) {
				$src = $url_path.$filename;
			}
			elseif (file_exists($code_path.'_thumbs/'.$filename)) {
				$src = $url_path.'_thumbs/no_image_available.png';
			}
			else {
				$src = $url_path.'no_image_available.png';
			}

			// Get the next date for the course
			$time = $date = '';
			if (is_array($item['schedules']) AND count($item['schedules']) > 0)
			{
				$found = FALSE;
				for ($j = 0; $j < count($item['schedules']) AND ! $found; $j++)
				{
					$time  = date('H:i', strtotime($item['schedules'][0]['start_date']));
					$date  = date('j F Y', strtotime($item['schedules'][0]['start_date']));
					$found = strtotime($item['schedules'][0]['start_date']) > strtotime(date('Y-m-d H:i:s'));
				}
			}
			?>

			<div class="course-result">

				<div class="col-xsmall-12 col-small-3 col-medium-3 compact-cols course-result-image">
					<div class="col-xsmall-12 col-small-0">
						<a class="course-result-heading" href="/course-detail.html?id=<?= $item['id'] ?>">
							<h2><?= $item['title'] ?></h2>
						</a>
					</div>
					<a href="/course-detail.html?id=<?= $item['id'] ?>">
						<img src="<?= $src ?>" alt="" />
					</a>
				</div>

				<div class="col-xsmall-12 col-small-9 col-medium-9 compact-cols course-result-data">
					<form action="<?= $apply_now_link ?>" method="get" class="col-xsmall-12 compact-cols" id="course_result_form_<?= $item['id'] ?>">
						<?php if (isset($apply_now_fields[2])): ?>
							<?php foreach ($apply_now_fields[1] as $key => $value): ?>
								<input type="hidden" name="<?= $apply_now_fields[1][$key] ?>" value="<?= $apply_now_fields[2][$key] ?>" />
							<?php endforeach; ?>
						<?php endif; ?>

						<input type="hidden" name="id" value="<?= $item['id'] ?>" />

						<div class="col-xsmall-0 col-small-12">
							<a class="course-result-heading" href="/course-detail.html?id=<?= $item['id'] ?>">
								<h2><?= $item['title'] ?></h2>
							</a>
						</div>

						<div>
							<?= $item['summary'] ?>
						</div>

						<div class="course-result-actions">
							<?php if ($item['description_button']): ?>
								<a href="/course-detail.html?id=<?= $item['id'] ?>" class="button-default"><?= __('Course Description') ?></a>
							<?php endif; ?>
							<?php if ($course_enquiry_button): ?>
								<a href="/contact-us.html?course_id=<?= $item['id'] ?>" class="button-secondary"><?= __('Enquire') ?></a>
							<?php endif; ?>
                            <?php if ($item['book_button']): ?>
							    <button type="submit" class="button-primary" data-form="course_result_form_<?= $item['id'] ?>"><?= __('Apply Now') ?></button>
                            <?php endif; ?>
						</div>
					</form>
				</div>
			</div>

		<?php endfor; ?>

	</div>

	<?php if (isset($courses['results_found'])): ?>
		<p class="text-center"><?= $courses['results_found'] ?><?= (isset($_GET['search'])) ? ' for <strong>'.Kohana::sanitize($_GET['search']).'</strong>' : '' ?>.</p>
	<?php endif; ?>

	<?php if ($pagination_count > 1): ?>
		<div class="filter_pagination text-center">
			<?php if ($current_page > 1): ?>
				<a class="prev" href="<?= Request::detect_uri().$query_string ?><?= $query_string ? '&' : '?' ?>page=<?= $current_page - 1 ?>">prev</a>
			<?php else: ?>
				<a class="prev disabled" href="#">prev</a>
			<?php endif; ?>
			<ul class="list-inline">
				<?php for ($i = 1; $i <= $pagination_count; $i++): ?>
					<li>
						<a href="<?= Request::detect_uri().$query_string ?><?= $query_string ? '&' : '?' ?>page=<?= $i ?>"
							<?= ($current_page == $i) ? ' class="current"' : ''; ?>><?= $i ?></a>
					</li>
				<?php endfor; ?>
			</ul>
			<?php if ($current_page != $pagination_count): ?>
				<a class="next" href="<?= Request::detect_uri().$query_string ?><?= $query_string ? '&' : '?' ?>page=<?= $current_page + 1 ?>">next</a>
			<?php else: ?>
				<a class="next disabled" href="#">next</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php endif; ?>
