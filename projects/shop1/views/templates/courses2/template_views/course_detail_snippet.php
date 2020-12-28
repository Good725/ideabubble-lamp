<?php
if (isset($_GET['id']) && (int)$_GET['id'] > 0)
{
	$id = Kohana::sanitize((int)$_GET['id']);
    $course = Model_Courses::get_detailed_info($id);
	$images = Model_Courses::get_images($id,5,0,'id','asc');
	$event_options = '';

	if (isset($course['schedules']) && is_array($course['schedules']) && count($course['schedules']) > 0)
	{
		foreach ($course['schedules'] as $schedule)
		{
			if ($schedule['bookings'] < $schedule['max_capacity'] AND $schedule['start_date'] > date('Y-m-d 23:59:59'))
			{
				$event_options .= '<option value="'.$schedule['event_id'].'" data-schedule_id="'.$schedule['id'].'">'.
				$schedule['location'].', '.date("l H:i ", strtotime($schedule['start_date'])).
				((isset($schedule['end_date'])) ? ' - '.date("H:i", strtotime($schedule['end_date'])) : '').', ('.
				(( ! is_null($schedule['repeat'])) ? $schedule['repeat'] : date('jS F Y',strtotime($schedule['start_date']))).
				')</option>';
			}
		}
	}

	if ($event_options != '')
	{
		$event_options_string = '<option value="" data-schedule_id="">-- Select Schedule --</option>'.$event_options;
	}

}

$apply_now_link = Settings::instance()->get('course_apply_link');
preg_match_all('/(\w+)=(.*)/',$apply_now_link, $apply_now_fields); // get query string values, so we can print them as hidden form fields
?>
<?php /*
<div class="breadcrumbs">
	<ul>
		<li><a href="/">Home</a></li>
		<li><a href="/course-list.html">Courses</a></li>
		<?php if ($course['title'] != ''): ?>
			<li><a href="#"><?= $course['title'] ?></a></li>
		<?php endif; ?>
		<li></li>
	</ul>
</div>
*/ ?>
<section id="course_details_page">
	<?php if (isset($course) AND ! empty($course)): ?>
		<form action="<?= $apply_now_link ?>" method="get" class="course-details-form" id="course_details_form">
			<?php if (isset($apply_now_fields[2])): ?>
				<?php foreach ($apply_now_fields[1] as $key => $value): ?>
					<input type="hidden" name="<?= $apply_now_fields[1][$key] ?>" value="<?= $apply_now_fields[2][$key] ?>" />
				<?php endforeach; ?>
			<?php endif; ?>

			<input type="hidden" name="course_id" value="<?= $course['id'] ?>" />
            <input type="hidden" name="schedule_id" value="" />

			<div class="col-xsmall-12">
				<h1><?= $course['title'] ?></h1>

				<?php if (trim($event_options)): ?>
					<label for="schedule_selector"><strong>Schedule</strong></label>
					<select name="event_id" class="validate[required]" id="schedule_selector">
						<?= $event_options ?>
					</select>
				<?php endif; ?>
			</div>

			<div class="col-xsmall-12">

				<?php if (isset($course['banner']) AND trim($course['banner'])): ?>
					<?php $filepath = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/'.$course['banner']; ?>
					<?php if (file_exists(PROJECTPATH.'www/'.$filepath)): ?>
						<img src="<?= $filepath ?>" alt="" />
					<?php endif; ?>
				<?php endif; ?>

				<h2>Description</h2>

				<?= $course['description'] ?>

				<?php if (count($images) > 0): ?>
					<h2>Images</h2>
					<div class="space-between-cols image_gallery">
						<?php $filepath = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/'; ?>
						<?php foreach ($images as $image): ?>
							<?php $src = $filepath.$image['file_name']; ?>
							<?php $thumbnail_src = $filepath.'_thumbs/'.$image['file_name']; ?>
							<a class="col-xsmall-12 col-small-2 col-medium-3" href="<?= $src ?>" data-width="">
								<img class="image-full" src="<?= $thumbnail_src ?>" alt="" />
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if (trim($course['file_id']) != ''): ?>
					<h2>Documents</h2>
					<?php $link = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/docs/'.$course['file_id']; ?>
					<a href="<?= $link ?>" target="_blank"><?= $course['file_id'] ?></a>
				<?php endif; ?>
			</div>

			<div class="col-xsmall-12 course_details_buttons">
				<?php if (Settings::instance()->get('course_enquiry_button')): ?>
					<a href="/contact-us.html?course_id=<?= $course['id'] ?>" type="button" class="button-secondary">Enquire</a>
				<?php endif; ?>
				<?php if ($course['book_button']): ?>
					<button type="submit" class="button-primary booking_button" id="course_detail_booking_button" disabled="disabled"><?= __('Apply Now') ?></button>
				<?php endif; ?>
			</div>

			<div id="course_details_price_wrapper" style="visibility:hidden;">
				Fee: <span class="course_details_price" id="course_details_price"></span>
				<?php if (Request::user_agent('mobile')): ?>
					<div id="course_details_discount_info"></div>
				<?php else: ?>
					<span class="popover_icon">i<span class="popover" id="course_details_discount_info"></span></span>
				<?php endif; ?>
			</div>
		</form>

	<?php else: ?>
		<div class="alert">Course not found.</div>
	<?php endif; ?>
</section>

<script>
	$(document).on("change", "#schedule_selector", function ()
	{
        var form = this.form;
		var timeslot_id = this.value;
		var schedule_id   = this[this.selectedIndex].getAttribute('data-schedule_id');
		var price_wrapper = document.getElementById('course_details_price_wrapper');
		price_wrapper.style.visibility = 'hidden';
		if (schedule_id)
		{
			$.post(
                '/frontend/courses/ajax_get_schedule_price_and_discount',
                {
                    schedule_id : schedule_id,
                    timeslot_id : timeslot_id
                },
				function (data) {
                    data = JSON.parse(data);
                    if (data.success)
                    {
                        form.schedule_id.value = schedule_id;
                        document.getElementById('course_details_price').innerHTML = (data.is_free) ? 'Free' : '&euro;'+data.fee;
                        document.getElementById('course_details_discount_info').innerHTML = data.discount_info;
                        price_wrapper.style.visibility = 'visible';
                    }
                }
			);
		}
	});

	$('#course_detail_booking_button').on('click', function(ev)
	{
		ev.preventDefault();
		var $form = $('#course_details_form');
		if ($(this).parents('form').validationEngine('validate'))
		{
			$form.submit();
		}
	});
</script>
