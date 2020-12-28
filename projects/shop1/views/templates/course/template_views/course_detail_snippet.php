<?php
if (isset($_GET['id']) && (int)$_GET['id'] > 0)
{
	$id = Kohana::sanitize((int)$_GET['id']);
    $course = Model_Courses::get_detailed_info($id, FALSE);
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

	if ($event_options == '')
	{
		$event_options = '<option value="" data-schedule_id="">No schedules available</option>';
	}
	else
	{
		$event_options = '<option value="" data-schedule_id="">-- Select Schedule --</option>'.$event_options;
	}

}
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
<section id="course_details_page" class="content-section inner-content">

	<?php if (isset($course) AND ! is_null($course) AND $course != FALSE): ?>
		<form action="/checkout.html" method="get" id="course_details_form">
			<input type="hidden" name="schedule_id" value="" />
			<h1><?= $course['title'] ?></h1>

			<div class="course_details_buttons">
				<?php if (Settings::Instance()->get('course_enquiry_button')): ?>
					<a href="/contact-us.html?course_id=<?= $course['id'] ?>" type="button" class="secondary_button">Enquire</a>
				<?php endif; ?>
				<?php if (count($course['schedules']) != 0): ?>
					<button type="submit" class="booking_button" id="course_detail_booking_button" disabled="disabled">Book Now</button>
				<?php endif; ?>

				<div id="course_details_price_wrapper" style="visibility:hidden;">
					Fee: <span class="course_details_price" id="course_details_price"></span>
					<?php if (Request::user_agent('mobile')): ?>
						<div id="course_details_discount_info"></div>
					<?php else: ?>
						<span class="popover_icon">i<span class="popover" id="course_details_discount_info"></span></span>
					<?php endif; ?>
				</div>

			</div>

			<h2>Details</h2>
			<?php if (count($course['schedules']) == 0): ?>
				<p>Please contact the office for schedules regarding this course.</p>
			<?php else: ?>
				<label for="schedule_selector"><strong>Schedule</strong></label>
				<select name="event_id" class="validate[required]" id="schedule_selector">
					<?= $event_options ?>
				</select>
			<?php endif; ?>

			<?php if (count($images) > 0): ?>
				<h2>Images</h2>
				<ul class="image_gallery">
					<?php foreach ($images as $image): ?>
						<?php $src = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/'.$images[0]['file_name']; ?>
						<?php $thumbnail_src = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/_thumbs/'.$images[0]['file_name']; ?>
						<li>
							<a href="<?= $src ?>"><img src="<?= file_exists($thumbnail_src) ? $thumbnail_src : $src ?>" alt="" /></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if (trim($course['file_id']) != ''): ?>
				<h2>Documents</h2>
				<?php $link = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/docs/'.$course['file_id']; ?>
				<a href="<?= $link ?>" target="_blank"><?= $course['file_id'] ?></a>
			<?php endif; ?>

			<h2>Description</h2>

			<?= $course['description'] ?>
		</form>

	<?php else: ?>
		<div class="alert">Course not found.</div>
	<?php endif; ?>
</section>

<script>
	$(document).on("change", "#schedule_selector", function ()
	{
		$("#course_detail_booking_button").prop("disabled", true);
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
						$("#course_detail_booking_button").prop("disabled", false);
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