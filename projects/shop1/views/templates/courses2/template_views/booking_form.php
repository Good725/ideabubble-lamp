<?php
unset($course);
$form_data        = Kohana::sanitize($_GET);
$paypal_enabled   = (Settings::instance()->get('enable_paypal') == 1 AND Settings::instance()->get('paypal_email')    != '');
$realex_enabled   = (Settings::instance()->get('enable_realex') == 1 AND Settings::instance()->get('realex_username') != '');
$paypal_test_mode = (Settings::instance()->get('paypal_test_mode') == 1);
?>
<?php
if (isset($form_data['course_id']))
{
	$course = Model_Courses::get_detailed_info($form_data['course_id']);
}

if (isset($form_data['event_id']))
{
	$event  = Model_Schedules::get_event_details($form_data['event_id']);
}
?>

<form class="booking-form-wrapper" id="course-booking-form" action="/frontend/formprocessor" method="post">

	<input name="event" value="contact-form" type="hidden" id="">
	<?php /*<input name="form_type" value="Contact Form" id="form_type" type="hidden">
	<input type="hidden" name="email_template" id="email_template" value="contactformmail">*/?>

	<input type="hidden" name="subject"     value="New booking">
	<input type="hidden" name="event"       value="post_contactForm">
	<input type="hidden" name="trigger"     value="booking2">
	<?php if ( ! empty($event)): ?>
		<input type="hidden" name="event_id"        value="<?= $event['id'] ?>">
		<input type="hidden" name="ids"             value="<?= $event['id'] ?>" />
		<input type="hidden" name="schedule_id"     value="<?= $event['schedule_id'] ?>" />
		<input type="hidden" name="schedule"        value="<?= $event['schedule'] ?>" />
		<input type="hidden" name="schedule_name"   value="<?= $event['schedule'] ?>" />
		<input type="hidden" name="course_location" value="<?= $event['location'] ?>" />
	<?php endif; ?>

	<input type="hidden" name="title"       value="Course Booking" />
	<input type="hidden" name="custom"      value="" />

	<input type="hidden" name="redirect"    value="<?= URL::site() ?>booking-thank-you.html" />
	<input type="hidden" name="return_url"  value="<?= URL::site() ?>booking-thank-you.html" />
	<input type="hidden" name="cancel_url"  value="<?= URL::site() ?>" />

	<?php if ( ! empty ($course)): ?>
		<input type="hidden" name="course_id"    value="<?= $course['id'] ?>" />
		<input type="hidden" name="course_name"  value="<?= $course['title'] ?>" />
	<?php endif; ?>

	<div class="col-xsmall-12">
		<table class="booking-table">
			<thead>
				<tr>
					<th scope="col">Title</th>
					<?php if ( ! empty($event)): ?>
						<th scope="col">Location</th>
						<th scope="col">Time</th>
					<?php endif; ?>
				</tr>
			</thead>

			<tbody>
				<tr>
					<?php if ( ! empty($course)): ?>
						<td><?= $course['title'] ?></td>
					<?php else: ?>
						<td>
							<div class="form-group">
								<label class="col-small-3" for="checkout-course"><?= __('Select a course') ?></label>

								<div class="col-small-3">
									<?php $courses = Model_Courses::get_all_published(); ?>
									<div class="select">
										<select class="input-styled validate[required]" id="checkout-course" name="course_id">
											<option value=""><?= __('Please select') ?></option>
											<?php foreach ($courses as $course_option): ?>
												<?php if ($course_option['book_button']): ?>
													<option value="<?= $course_option['id'] ?>"><?= $course_option['title'] ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										</select>
									</div>
									<input type="hidden" name="course_name" id="checkout-hidden-course_name" />
								</div>
							</div>
							<script>
								$('$checkout-course').on('change', function()
								{
									var selected = this[this.selectedIndex];
									if (selected.value)
									{
										document.getElementById('checkout-hidden-course_name').value = selected.innerHTML;
									}
								});
							</script>
						</td>
					<?php endif; ?>
					<?php if ( ! empty($event)): ?>
						<td><?= $event['location'] ?></td>
						<td><?= date('H:i:s, D j F Y', strtotime($event['datetime_start'])) ?> &ndash;<br /><?= date('H:i:s, D j F Y', strtotime($event['datetime_end'])) ?></td>
					<?php endif; ?>
				</tr>
			</tbody>

		</table>
	</div>

	<section class="clearfix">
		<div class="col-xsmall-12">
			<h2>1. Student Details</h2>
		</div>

		<div>
			<div class="col-xsmall-12 col-small-6 col-medium-6">
				<div class="form-group">
					<label class="col-xsmall-4 label-required" for="checkout-first_name">First name</label>
					<div class="col-xsmall-8">
						<input type="text" class="input-styled validate[required]" id="checkout-first_name" name="name" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xsmall-4 label-required" for="checkout-surname">Surname</label>
					<div class="col-xsmall-8">
						<input type="text" class="input-styled validate[required]" id="checkout-surname" name="surname" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xsmall-4 label-required" for="checkout-dob">Date of Birth</label>
					<div class="col-xsmall-8">
						<input type="text" class="input-styled validate[required] booking-datepicker" id="checkout-dob" name="dob" placeholder="dd/mm/yyyy" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xsmall-4 label-required" for="checkout-gender">Gender</label>
					<div class="col-xsmall-8">
						<div class="select">
							<select class="input-styled validate[required]" id="checkout-gender" name="gender">
								<option value="">SELECT GENDER</option>
								<option value="male">Male</option>
								<option value="female">Female</option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xsmall-12 col-small-6 col-medium-6">
				<div class="form-group">
					<label clss="col-xsmall-4 label-required" for="checkout-address_1">Address Line&nbsp;1</label>
					<div class="col-xsmall-8">
						<input type="text" class="input-styled validate[required]" id="checkout-address_1" name="address_1" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xsmall-4 label-required" for="checkout-address_2">Line 2</label>
					<div class="col-xsmall-8">
						<input type="text" class="input-styled validate[required]" id="checkout-address_2" name="address_2" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xsmall-4 label-required" for="checkout-address_3">Line 3</label>
					<div class="col-xsmall-8">
						<input type="text" class="input-styled validate[required]" id="checkout-address_3" name="address_3" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xsmall-4" for="checkout-address_4">Line 4</label>
					<div class="col-xsmall-8">
						<input type="text" class="input-styled" id="checkout-address_4" name="address_4" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xsmall-4" for="checkout-address_5">Line 5</label>
					<div class="col-xsmall-8">
						<input type="text" class="input-styled" id="checkout-address_5" name="address_5" />
					</div>
				</div>
			</div>
		</div>

		<div class="col-xsmall-12 col-medium-6">
			<div class="form-group">
				<label class="col-xsmall-4 form-label" for="checkout-school_level">School Level</label>
				<div class="col-xsmall-8">
					<div class="select">
						<select class="input-styled" name="school_level" id="checkout-school_level">
							<option value="">SELECT School Level</option>
							<option value="primary">(a) Primary</option>
							<option value="junior_cert">(b) Junior Cert.</option>
							<option value="leaving_cert">(c) Leaving Cert.</option>
							<option value="3rd_level">3rd Level</option>
							<option value="n/a">NA</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-4 form-label" for="checkout-school_level">Current School</label>
				<div class="col-xsmall-8">
					<input type="text" class="input-styled" id="checkout-school_level" name="current_school" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-12 form-label" for="checkout-future_secondary_school">If still attending primary school, future secondary school</label>
			</div>
			<div class="form-group">
				<div class="col-xsmall-0 col-small-4">&nbsp;</div>
				<div class="col-xsmall-12 col-small-8">
					<input type="text" class="input-styled" id="checkout-future_secondary_school" name="future_secondary_school" />
				</div>
			</div>

		</div>

	</section>

	<section class="clearfix">
		<div class="col-xsmall-12">
			<h2>2. Application Information</h2>
		</div>

		<div class="col-xsmall-12">
			<div class="form-group">
				<label class="col-xsmall-12 col-small-6" for="checkout-is_beginner">Please tick if you are a beginner.</label>
				<div class="col-xsmall-12 col-small-6">
					<input type="checkbox" id="checkout-is_beginner" name="is_beginner" />
				</div>
			</div>

			<div class="form-group">
				<div class="col-xsmall-12 col-small-6 label-required">Are any family members already attending?</div>
				<div class="col-xsmall-12 col-small-6">
					<label>
						<input type="radio" class="validate[required]" id="checkout-family_members_attending_yes" name="family_members_attending" value="1" /> Yes
					</label>
					<label>
						<input type="radio" class="validate[required]" id="checkout-family_members_attending_no" name="family_members_attending" value="0" /> No
					</label>
				</div>
			</div>

			<div class="form-group">
				<div class="col-xsmall-12 col-small-6 label-required">Do you have a piano at home?</div>
				<div class="col-xsmall-12 col-small-6">
					<label>
						<input type="radio" class="validate[required]" id="checkout-has_piano-yes" name="has_piano" value="1" /> Yes
					</label>
					<label>
						<input type="radio" class="validate[required]" id="checkout-has_piano-no" name="has_piano" value="0" /> No
					</label>
				</div>
			</div>

			<div class="form-group">
				<div class="col-xsmall-12 col-small-6 label-required">Have you had music tuition previously?</div>
				<div class="col-xsmall-12 col-small-6">
					<label>
						<input type="radio" class="validate[required]" id="has_previous_tuition_yes" name="has_previous_tuition" value="1" /> Yes
					</label>
					<label>
						<input type="radio" class="validate[required]" id="has_previous_tuition_no" name="has_previous_tuition" value="0" /> No
					</label>
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-12 col-small-6" for="checkout-previous_tuition_details">If yes, please give details of exams/standard/instrument</label>
				<div class="col-xsmall-12 col-small-6 col-medium-5">
					<textarea class="input-styled" id="checkout-previous_tuition_details" name="previous_tuition_details"></textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-12 col-small-6" for="checkout-other_details">Other information in relation to the applicant</label>
				<div class="col-xsmall-12 col-small-6 col-medium-5">
					<textarea class="input-styled" id="checkout-other_details" name="other_details"></textarea>
				</div>
			</div>

		</div>

	</section>

	<section class="clearfix labels-right">
		<div class="col-xsmall-12">
			<h2>3. Parent/Guardian Details</h2>
		</div>

		<div class="col-xsmall-12 col-medium-6">
			<h3>Parent/Guardian 1</h3>

			<div class="form-group">
				<label class="col-xsmall-6 form-label label-required" for="checkout-guardian_title">Title</label>
				<div class="col-xsmall-6">
					<div class="select">
						<select class="input-styled validate[required]" id="checkout-guardian_title" name="guardian_title">
							<option value="">SELECT TITLE</option>
							<option value="Mr.">Mr.</option>
							<option value="Mrs.">Mr.</option>
							<option value="Ms.">Ms.</option>
							<option value="Miss.">Miss.</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label label-required" for="checkout-guardian_first_name">First Name</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled validate[required]" id="checkout-guardian_first_name" name="guardian_first_name" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label label-required" for="checkout-guardian_surname">Surname</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled validate[required]" id="checkout-guardian_surname" name="guardian_surname" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label label-required" for="checkout-guardian_phone">Home Phone</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled validate[required]" id="checkout-guardian_phone" name="guardian_phone" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label label-required" for="checkout-guardian_mobile">Mobile Phone</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled validate[required]" id="checkout-guardian_mobile" name="guardian_mobile" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label label-required" for="checkout-guardian_email">Email</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled validate[required]" id="checkout-guardian_email" name="guardian_email" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label label-required" for="checkout-guardian_email2">Confirm Email</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled validate[required]" id="checkout-guardian_email2" name="guardian_email2" />
				</div>
			</div>

			<div class="form-group">
				<div class="col-xsmall-6" style="text-align: right;">
					<input type="checkbox" id="checkout-show-guardian-address" />
				</div>
				<label class="col-xsmall-6" for="checkout-show-guardian-address">Address is different from student</label>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian-county">County</label>
				<div class="col-xsmall-6">
					<div class="select">
						<select class="input-styled" id="checkout-guardian-county" name="guardian_county">
							<option value="">Select County</option>
							<option value="Armagh">Armagh</option>
							<option value="Carlow">Carlow</option>
							<option value="Cavan">Cavan</option>
							<option value="Clare">Clare</option>
							<option value="Cork">Cork</option>
							<option value="Derry">Derry</option>
							<option value="Donegal">Donegal</option>
							<option value="Down">Down</option>
							<option value="Dublin">Dublin</option>
							<option value="Fermanagh">Fermanagh</option>
							<option value="Galway">Galway</option>
							<option value="Kerry">Kerry</option>
							<option value="Kildare">Kildare</option>
							<option value="Kilkenny">Kilkenny</option>
							<option value="Laois">Laois</option>
							<option value="Leitrim">Leitrim</option>
							<option value="Limerick">Limerick</option>
							<option value="Longford">Longford</option>
							<option value="Louth">Louth</option>
							<option value="Mayo">Mayo</option>
							<option value="Meath">Meath</option>
							<option value="Monaghan">Monaghan</option>
							<option value="Offaly">Offaly</option>
							<option value="Roscommon">Roscommon</option>
							<option value="Sligo">Sligo</option>
							<option value="Tipperary">Tipperary</option>
							<option value="Tyrone">Tyrone</option>
							<option value="Waterford">Waterford</option>
							<option value="Westmeath">Westmeath</option>
							<option value="Wexford">Wexford</option>
							<option value="Wicklow">Wicklow</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label label-required" for="checkout-guardian_emergency_number">Emergency Contact No.</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled validate[required]" id="checkout-guardian_emergency_number" name="guardian_emergency_number" />
				</div>
			</div>


		</div>


		<div class="col-xsmall-12 col-medium-6">
			<h3>Parent/Guardian 2</h3>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian2_title">Title</label>
				<div class="col-xsmall-6">
					<div class="select">
						<select class="input-styled" id="checkout-guardian2_title" name="guardian2_title">
							<option value="">SELECT TITLE</option>
							<option value="Mr.">Mr.</option>
							<option value="Mrs.">Mr.</option>
							<option value="Ms.">Ms.</option>
							<option value="Miss.">Miss.</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian2_first_name">First Name</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled" id="checkout-guardian2_first_name" name="guardian2_first_name" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian2_surname">Surname</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled" id="checkout-guardian2_surname" name="guardian2_surname" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian2_phone">Home Phone</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled" id="checkout-guardian2_phone" name="guardian2_phone" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian2_mobile">Mobile Phone</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled" id="checkout-guardian2_mobile" name="guardian2_mobile" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian2_email">Email</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled" id="checkout-guardian2_email" name="guardian2_email" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian2_email2">Confirm Email</label>
				<div class="col-xsmall-6">
					<input type="text" class="input-styled" id="checkout-guardian2_email2" name="guardian2_email2" />
				</div>
			</div>

			<div class="form-group">
				<div class="col-xsmall-6" style="text-align: right;">
					<input type="checkbox" id="checkout-show-guardian2-address" />
				</div>
				<label class="col-xsmall-6" for="checkout-show-guardian2-address">Address is different from student</label>
			</div>

			<div class="form-group">
				<label class="col-xsmall-6 form-label" for="checkout-guardian2-county">County</label>
				<div class="col-xsmall-6">
					<div class="select">
						<select class="input-styled" id="checkout-guardian2-county" name="guardian2_county">
							<option value="">Select County</option>
							<option value="Armagh">Armagh</option>
							<option value="Carlow">Carlow</option>
							<option value="Cavan">Cavan</option>
							<option value="Clare">Clare</option>
							<option value="Cork">Cork</option>
							<option value="Derry">Derry</option>
							<option value="Donegal">Donegal</option>
							<option value="Down">Down</option>
							<option value="Dublin">Dublin</option>
							<option value="Fermanagh">Fermanagh</option>
							<option value="Galway">Galway</option>
							<option value="Kerry">Kerry</option>
							<option value="Kildare">Kildare</option>
							<option value="Kilkenny">Kilkenny</option>
							<option value="Laois">Laois</option>
							<option value="Leitrim">Leitrim</option>
							<option value="Limerick">Limerick</option>
							<option value="Longford">Longford</option>
							<option value="Louth">Louth</option>
							<option value="Mayo">Mayo</option>
							<option value="Meath">Meath</option>
							<option value="Monaghan">Monaghan</option>
							<option value="Offaly">Offaly</option>
							<option value="Roscommon">Roscommon</option>
							<option value="Sligo">Sligo</option>
							<option value="Tipperary">Tipperary</option>
							<option value="Tyrone">Tyrone</option>
							<option value="Waterford">Waterford</option>
							<option value="Westmeath">Westmeath</option>
							<option value="Wexford">Wexford</option>
							<option value="Wicklow">Wicklow</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="clearfix labels-right">
		<div class="col-xsmall-12">
			<div class="form-group">
				<label class="col-xsmall-12 col-small-6 col-medium-4 form-label" for="checkout-hear_about">How did you hear about us?</label>
				<div class="col-xsmall-12 col-small-6 col-medium-4">
					<div class="select">
						<select class="input-styled" id="checkout-hear_about" name="hear_about">
							<option value="">SELECT</option>
							<option value="advertisement">Advertisement</option>
							<option value="booklet">Booklet</option>
							<option value="email-newsletter">Email/Newsletter</option>
							<option value="facebook">Facebook</option>
							<option value="family-friend">Family or Friend</option>
							<option value="magazine">Magazine Article</option>
							<option value="newspaper">Newpaper Story</option>
							<option value="twitter">Twitter</option>
							<option value="youtube">YouTube</option>
							<option value="site_or_search_engine">Website/Search Engine</option>
							<option value="other">Other</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group checkout-hear_about-indicate" id="checkout-hear_about-indicate-advertisement" style="display: none;">
				<label class="col-xsmall-12 col-small-6 col-medium-4 form-label" for="checkout-hear_about-advertisement">Please indicate which advert</label>
				<div class="col-xsmall-12 col-small-6 col-medium-4">
					<input class="input-styled" type="text" name="hear_about_advertisement" id="checkout-hear_about-advertisement" />
				</div>
			</div>

			<div class="form-group checkout-hear_about-indicate" id="checkout-hear_about-indicate-magazine" style="display: none;">
				<label class="col-xsmall-12 col-small-6 col-medium-4 form-label" for="checkout-hear_about-magazine">Please indicate which magazine</label>
				<div class="col-xsmall-12 col-small-6 col-medium-4">
					<input class="input-styled" type="text" name="hear_about_magazine" id="checkout-hear_about-magazine" />
				</div>
			</div>

			<div class="form-group checkout-hear_about-indicate" id="checkout-hear_about-indicate-site-search" style="display: none;">
				<label class="col-xsmall-12 col-small-6 col-medium-4 form-label" for="checkout-hear_about-site_or_search_engine">Please indicate which magazine</label>
				<div class="col-xsmall-12 col-small-6 col-medium-4">
					<input class="input-styled" type="text" name="hear_about_site_or_search_engine" id="checkout-hear_about-site_or_search_engine" />
				</div>
			</div>

			<div class="form-group checkout-hear_about-indicate" id="checkout-hear_about-indicate-other" style="display: none;">
				<label class="col-xsmall-12 col-small-6 col-medium-4 form-label" for="checkout-hear_about-other">Please describe</label>
				<div class="col-xsmall-12 col-small-6 col-medium-4">
					<input class="input-styled" type="text" name="hear_about_other" id="checkout-hear_about-other" />
				</div>
			</div>

		</div>
	</section>

	<section class="clearfix">
		<div class="form-group col-xsmall-12">
			<label class="col-xsmall-12">
				<input type="checkbox" />
				Please confirm you have read and accept the <a href="/terms-and-conditions.html">terms and conditions</a> by ticking this box.
			</label>
		</div>

		<div class="form-group col-xsmall-12">
			<label class="col-xsmall-12">
				<input type="checkbox" />
				Please confirm you have read and accept the <a href="/data-protection-act.html">data protection act</a> by ticking this box.
			</label>
		</div>
	</section>

	<section class="booking-form-buttons">
		<button type="button" class="button-default" id="booking-form-back">Back</button>
		<button type="submit" class="button-primary"><?= __('Apply Now') ?></button>
	</section>

</form>


<script>
	$('#checkout-hear_about').on('change', function()
	{
		$('.checkout-hear_about-indicate').hide();
		$('#checkout-hear_about-indicate-'+$(this).val()).show();
	});

	$('#course-booking-form').on('submit', function(ev)
	{
		ev.preventDefault();
		if ($(this).validationEngine('validate'))
		{
			this.submit();
		}
	});

	$('.booking-datepicker').datetimepicker({
		timepicker:false,
		format:'d/m/Y',
		mask: true
	});

	$('#booking-form-back').on('click', function()
	{
		window.history.back();
	});

</script>