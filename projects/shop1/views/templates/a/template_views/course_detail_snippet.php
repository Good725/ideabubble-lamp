<?php
if (isset($_GET['id']) && (int)$_GET['id'] > 0)
{
	$id = Kohana::sanitize((int)$_GET['id']);
    $course = Model_Courses::get_detailed_info($id, false);
	$images = Model_Courses::get_images($id,-1,0,'id','asc');
	$event_options = '';

	if ( ! empty($course['schedules']) && is_array($course['schedules']))
	{
		foreach ($course['schedules'] as $schedule)
		{
			$check_date = ($schedule['booking_type'] == 'Whole Schedule') ? $schedule['end_date'] : $schedule['start_date'];
			if (( ! $schedule['max_capacity'] OR $schedule['bookings'] < $schedule['max_capacity']) AND $check_date > date('Y-m-d 23:59:59'))
			{
                $event_options .= '<option value="' . $schedule['event_id'] . '" data-schedule_id="' . $schedule['id'] . '">' .
                    $schedule['location'] . ' - ' .
                    (date('H:i', strtotime($schedule['start_date'])) != '00:00' ?
                        date('l H:i', strtotime($schedule['start_date'])) . ((date('H:i', strtotime($schedule['end_date'])) != '00:00') ? '–'.date('H:i', strtotime($schedule['end_date'])) : '')
                        : '' ) .
                    ' - '.
                    (( ! is_null($schedule['repeat'])) ? $schedule['repeat'] : date('jS F Y',strtotime($schedule['start_date']))).
                    '</option>';
			}
		}
	}

	if ($event_options == '')
	{
		$event_options = '<option value="" data-schedule_id="">No schedules available</option>';
	}
	else
	{
		$event_options = '<option value="" data-schedule_id="">Please select (Location - Time - Date)</option>'.$event_options;
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

	<?php if ( ! empty($course)): ?>
		<form action="/course-checkout.html" method="get" id="course_details_form">
            <input type="hidden" name="schedule_id" value="" />
			<h1><?= $course['title'] ?></h1>

			<div class="course_details-inner">
				<div class="course_details-data">
					<h2>Details:</h2>

					<?php if (count($course['schedules']) == 0): ?>
						<p>Please contact the office for schedules regarding this course.</p>
					<?php else: ?>
						<div class="course_details-row course_details-schedule">
							<label for="schedule_selector">Schedule</label>
							<div class="select">
								<select name="event_id" class="validate[required]" id="schedule_selector">
									<?= $event_options ?>
								</select>
							</div>
						</div>
					<?php endif; ?>

					<?php if (count($images) > 0): ?>
						<?php
						$filepath = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/photos/courses/';


						$gallery_data = array();
						foreach ($images as $key => $image)
						{
							$gallery_data[] = array( 'src' => $filepath.$image['file_name'], 'w' => $image['width'], 'h' => $image['height']);
						}
						?>

						<div class="course_details-row">
							<img src="<?= $filepath.$images[0]['file_name'] ?>" alt="" />

							<ul class="image_gallery">
								<?php for ($i = 1; $i < count($images) AND $i <= 3; $i++): ?>
								<?php $src = $filepath.'_thumbs/'.$images[$i]['file_name']; ?>
								<li>
									<?php if ($i < count($images) - 1 AND $i <= 2): ?>
										<img src="<?= $src ?>" alt="" />
									<?php else: ?>
										<figure class="see-all-photos-wrapper">
											<img class="image-full" src="<?= $src ?>" />
											<figcaption class="see-all-photos-caption">
												<button
													type="button"
													class="button-link see-all-photos-button photoswipe-button"
													data-images="<?= htmlentities(json_encode($gallery_data)) ?>"
													>See all photos</button>
											</figcaption>
										</figure>
									<?php endif; ?>
								</li>
								<?php endfor; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php if (trim($course['file_id']) != ''): ?>
						<h2>Documents:</h2>
						<?php $link = '/shared_media/'.Kohana::$config->load('config')->project_media_folder.'/media/docs/'.$course['file_id']; ?>
						<a href="<?= $link ?>" target="_blank"><?= $course['file_id'] ?></a>
					<?php endif; ?>

					<h2>Description:</h2>

					<div class="course-details-description">
						<?= trim($course['description']) ? $course['description'] : $course['summary'] ?>
					</div>

				</div>
				<div class="course_details-buttons">
					<div class="course-discount popover_icon hidden" id="course_details_discount" style="display: none;">
						i<span class="popover course-discount_info" id="course_details_discount_info"></span>
					</div>

					<div class="price-wrapper" style="display: none; margin-bottom: 20px">
						Fee: <span class="course_details_price" id="course_details_price"></span>
					</div>

					<?php if (Settings::Instance()->get('course_enquiry_button')): ?>
						<a href="/contact-us.html?course_id=<?= $course['id'] ?>" type="button" class="course_enquiry_button">Enquire</a>
					<?php endif; ?>
					<?php if (count($course['schedules']) != 0): ?>
						<button type="submit" class="booking_button" style="display: none" id="course_detail_booking_button" disabled="disabled">Book</button>
					<?php endif; ?>
				</div>
			</div>
		</form>

	<?php else: ?>
		<div class="alert">Course not found.</div>
	<?php endif; ?>
</section>

<link href="<?= URL::site() ?>assets/default/css/photoswipe.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/photoswipe.min.js"></script>
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="pswp__bg"></div>
	<div class="pswp__scroll-wrap">
		<div class="pswp__container">
			<div class="pswp__item"></div>
			<div class="pswp__item"></div>
			<div class="pswp__item"></div>
		</div>

		<div class="pswp__ui pswp__ui--hidden">
			<div class="pswp__top-bar">
				<div class="pswp__counter"></div>

				<button class="pswp__button pswp__button--close" title="<?= __('Close (Esc)') ?>"></button>
				<button class="pswp__button pswp__button--share" title="<?= __('Share') ?>"></button>
				<button class="pswp__button pswp__button--fs" title="<?= __('Toggle fullscreen') ?>"></button>
				<button class="pswp__button pswp__button--zoom" title="<?= __('Zoom in/out') ?>"></button>

				<div class="pswp__preloader">
					<div class="pswp__preloader__icn">
						<div class="pswp__preloader__cut">
							<div class="pswp__preloader__donut"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
				<div class="pswp__share-tooltip"></div>
			</div>

			<button class="pswp__button pswp__button--arrow--left" title="<?= __('Previous (arrow left)') ?>"></button>
			<button class="pswp__button pswp__button--arrow--right" title="<?= __('Next (arrow right)') ?>"></button>

			<div class="pswp__caption">
				<div class="pswp__caption__center"></div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on("change", "#schedule_selector", function ()
	{
        var form = this.form;
		var timeslot_id = this.value;
		var schedule_id   = this[this.selectedIndex].getAttribute('data-schedule_id');
		$('#course_details_discount').addClass('hidden').hide();
        $('.price-wrapper').hide();
        $('#course_detail_booking_button').hide().prop('disabled', true);
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
						$('#course_details_discount').removeClass('hidden').show();
						document.getElementById('course_details_price').innerHTML = (data.is_free) ? 'Free' : '&euro;'+data.fee;
						document.getElementById('course_details_discount_info').innerHTML = data.discount_info;
                        $('.price-wrapper').show();
                        $('#course_detail_booking_button').prop('disabled', false).show();
					}
				}
			);
		}
		else
		{
			document.getElementById('course_details_price').innerHTML = '';
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