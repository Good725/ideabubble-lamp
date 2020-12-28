<?php include 'template_views/header.php'; ?>
<?php
if ( ! empty($_GET['id']) AND (int)$_GET['id'] > 0)
{
	$event_id = (isset($_GET['eid']) AND $_GET['eid'] > 0) ? $_GET['eid'] : NULL;
	$schedule = Model_Schedules::get_course_and_schedule_short($_GET['id'], $event_id);
	if ($schedule)
	{
		// If a specific date has not been specified, use the start date
		$date = (is_null($event_id)) ? $schedule['start_date'] : $schedule['datetime_start'];
		$product_enquiry = (Settings::instance()->get('product_enquiry') == 1);
	}
}?>

<div class="row content-columns">
	<?php include 'template_views/sidebar.php'; ?>
	<div class="content_area">
		<div class="page-content"><?= trim($page_data['content']) ?></div>

		<?php if ( ! empty($schedule)): ?>
			<form class="booking-form" id="booking_form" method="post">
				<input type="hidden" id="subject"  name="subject"       value="Booking form" />
				<input type="hidden"               name="business_name" value="Kilmartin Education Services" />
				<input type="hidden" id="redirect" name="redirect"      value="payment.html" />
				<input type="hidden"               name="event"         value="post_contactForm" />
				<input type="hidden" id="trigger"  name="trigger"       value="booking" />
				<input type="hidden"               name="schedule_id"   value="<?= $schedule['id'] ?>"/>
				<input type="hidden"               name="event_id"      value="<?= $schedule['event_id'] ?>"/>
				<input type="hidden"               name="training"      value="<?= $schedule['title'] ?>">
				<input type="hidden"               name="price"         value="<?= $schedule['fee_amount'] ?>">
				<input type="hidden"               name="schedule"      value="<?= $schedule['name'] ?>, <?= date("Y-m-d H:i", strtotime($date)) ?>, <?= $schedule['location'] ?>" />

				<div class="booking-section booking-section--table">
					<table class="table booking-table">
						<thead>
							<tr>
								<th scope="col"><?= __('Courses') ?></th>
								<th scope="col">
									<span class="hidden--tablet hidden--desktop"><?= __('Price') ?></span>
									<span class="hidden--mobile"><?= __('Total Price') ?></span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<p><a href="" target="_blank"><?= $schedule['title'] ?> (<?= $schedule['name'] ?>)</a></p>
									<p><?= date("D. h:ia, n M Y", strtotime($date)) ?></p>
									<p><?= $schedule['location'] ?></p>
								</td>
								<td>&euro;<?= number_format($schedule['fee_amount'], 2) ?></td>
							</tr>
						</tbody>
					</table>

				</div>

				<h2><?= __('Guardian Details 1') ?></h2>

				<div class="booking-section">
					<div class="booking-row">
						<div class="booking-column booking-column--half">
							<label class="sr-only" for="booking_form-guardian_title"><?= __('Title') ?></label>

							<div class="form-group">
								<div class="select">
									<select name="guardian_title" class="form-input" id="booking_form-guardian_title">
										<option value="">Title</option>
										<option value="Mr">Mr</option>
										<option value="Ms">Ms</option>
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="booking-row">
						<div class="booking-column booking-column--half">
							<div class="form-group form-group--required">
								<label class="sr-only" for="booking_form-guardian_first_name"><?= __('First Name') ?></label>
								<input id="booking_form-guardian_first_name" name="guardian_first_name" class="form-input validate[required]" type="text" placeholder="<?= __('First Name') ?> *" />
							</div>
						</div>

						<div class="booking-column booking-column--half">
							<div class="form-group form-group--required">
								<label class="sr-only" for="booking_form-guardian_last_name"><?= __('Last Name') ?></label>
								<input id="booking_form-guardian_last_name" name="guardian_last_name" class="form-input validate[required]" type="text" placeholder="<?= __('Last Name') ?> *" />
							</div>
						</div>
					</div>

					<div class="booking-row">
						<div class="booking-column booking-column--half" id="booking_form-guardian_address" data-sync_with="#booking_form-student_address" data-sync="on">

							<div class="form-group">
								<label class="sr-only" for="booking_form-guardian_relationship_to_student"><?= __('Relationship to student') ?></label>
								<div class="select">
									<select name="guardian_relationship_to_student" class="form-input" id="booking_form-guardian_relationship_to_student">
										<option value=""><?= __('Relationship to student') ?></option>
										<option value="parent">Parent</option>
										<option value="uncle">Uncle</option>
										<option value="other">Other - please specify</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="sr-only" for="booking_form-guardian_address1"><?= __('Address line 1') ?></label>
								<input class="form-input" id="booking_form-guardian_address1" name="guardian_address1" data-field="address1" placeholder="<?= __('Address line 1') ?>" />
							</div>

							<div class="form-group">
								<label class="sr-only" for="booking_form-guardian_address2"><?= __('Address line 2') ?></label>
								<input class="form-input" id="booking_form-guardian_address2" name="guardian_address2" data-field="address2" placeholder="<?= __('Address line 2') ?>" />
							</div>

							<div class="form-group">
								<label class="sr-only" for="booking_form-guardian_address3"><?= __('Address line 3') ?></label>
								<input class="form-input" id="booking_form-guardian_address3" name="guardian_address3" data-field="address3" placeholder="<?= __('Address line 3') ?>" />
							</div>

							<div class="form-group">
								<label class="sr-only" for="booking_form-guardian_city"><?= __('City') ?></label>
								<input type="text" class="form-input" id="booking_form-guardian_city" name="guardian_city" data-field="city" placeholder="<?= __('City') ?>" />
							</div>

							<div class="form-group">
								<label class="sr-only" for="booking_form-guardian_county" data-field="county"><?= __('County') ?></label>

								<div class="select">
									<select class="form-input" id="booking_form-guardian_county" name="guardian_county">
										<option value="">County</option>
										<?= Model_Cities::get_all_counties_html_options() ?>
									</select>
								</div>
							</div>

						</div>

						<div class="booking-column booking-column--half" id="booking_form-guardian_contact" data-sync_with="#booking_form-student_contact" data-sync="on">
							<div class="form-group guardian_relationship_to_student_other" style="visibility: hidden;">
								<label class="sr-only" for="booking_form-guardian_relationship_to_student_other"><?= __('Please Specify relationship to student') ?></label>
								<input type="text" class="form-input" id="booking_form-guardian_relationship_to_student_other" name="guardian_relationship_to_student_other" placeholder="Please Specify relationship to student" disabled="disabled" />
							</div>

							<div class="form-group form-group--required">
								<label class="sr-only" for="booking_form-guardian_email"><?= __('E-mail') ?></label>
								<input type="text" class="form-input validate[required,custom[email]]" id="booking_form-guardian_email" name="guardian_email" data-field="email" placeholder="<?= __('E-mail') ?> *" />
							</div>

							<div class="form-group form-group--required">
								<label class="sr-only" for="booking_form-guardian_mobile"><?= __('Mobile') ?></label>
								<input type="text" class="form-input validate[required,custom[irishMobile]]" id="booking_form-guardian_mobile" data-field="mobile" name="guardian_mobile" placeholder="<?= __('Mobile') ?> *" />
							</div>

							<div class="form-group">
								<label class="sr-only" for="booking_form-guardian_phone"><?= __('Phone') ?></label>
								<input type="text" class="form-input" id="booking_form-guardian_phone" name="guardian_phone" data-field="phone" placeholder="<?= __('Phone') ?>" />
							</div>

							<div class="form-group">
								<div class="booking-preferred">
									<p><?= __('Preferred Contact Method'); ?></p>
									<p>
										<label>
											<input type="checkbox" data-id="guardian_preferred_sms" id="guardian_preferred_sms" name="guardian_preferred_sms" data-field="preferred_sms" value="Yes" />
											<?= __('SMS') ?>
										</label>
										<label>
											<input type="checkbox" data-id="guardian_preferred_email" id="guardian_preferred_email" name="guardian_preferred_email" data-field="preferred_email" value="Yes" />
											<?= __('E-mail') ?>
										</label>
										<label>
											<input type="checkbox" data-id="guardian_preferred_post" id="guardian_preferred_post" name="guardian_preferred_post" data-field="preferred_post" value="Yes" />
											<?= __('Post') ?>
										</label>
									</p>
								</div>
							</div>
						</div>
					</div>

					<div class="booking-row">
						<div class="booking-column">
							<p class="booking-required_field-note"><span>*</span> <?= __('Required Fields') ?></p>
						</div>
					</div>
				</div>

				<h2><?= __('Student Details') ?></h2>

				<div class="booking-section">
					<div class="booking-row">
						<div class="booking-column booking-column--half">
							<label class="sr-only" for="booking_form-student_title"><?= __('Title') ?></label>

							<div class="form-group">
								<div class="select">
									<select name="student_title" class="form-input" id="booking_form-student_title">
										<option value="">Title</option>
										<option value="Mr">Mr</option>
										<option value="Ms">Ms</option>
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="booking-row">
						<div class="booking-column booking-column--half">
							<div class="form-group form-group--required">
								<label class="sr-only" for="booking_form-student_first_name"><?= __('First Name') ?></label>
								<input id="booking_form-student_first_name" name="student_first_name" class="form-input validate[required]" type="text" placeholder="<?= __('First Name') ?> *" />
							</div>
						</div>

						<div class="booking-column booking-column--half">
							<div class="form-group form-group--required">
								<label class="sr-only" for="booking_form-student_last_name"><?= __('Last Name') ?></label>
								<input id="booking_form-student_last_name" name="student_last_name" class="form-input validate[required]" type="text" placeholder="<?= __('Last Name') ?> *" />
							</div>
						</div>
					</div>

					<div class="booking-row">
						<div class="booking-column booking-column--half">
							<div class="form-group">
								<label class="sr-only" for="booking_form-student_date_of_birth"><?= __('Date of Birth') ?></label>
								<input type="text" class="form-input datepicker" id="booking_form-student_date_of_birth" name="student_date_of_birth" placeholder="<?= __('Date of Birth') ?> *" />
							</div>
						</div>
					</div>

					<div class="booking-row">
						<div class="booking-column booking-column--half">
							<div class="form-group">
								<label class="booking-use_guardian">
									<?= __('Use different address than guardian') ?>
									<input type="checkbox" class="booking-use_guardian-toggle" id="use_guardian_address" name="use_guardian_address" data-target="#booking_form-student_address" />
								</label>
							</div>

							<div class="hidden" id="booking_form-student_address" data-sync_with="#booking_form-guardian_address" data-sync="on">

								<div class="form-group">
									<label class="sr-only" for="booking_form-student_address1"><?= __('Address line 1') ?></label>
									<input class="form-input" id="booking_form-student_address1" name="student_address1" data-field="address1" placeholder="<?= __('Address line 1') ?>" />
								</div>

								<div class="form-group">
									<label class="sr-only" for="booking_form-student_address2"><?= __('Address line 2') ?></label>
									<input class="form-input" id="booking_form-student_address2" name="student_address2" data-field="address2" placeholder="<?= __('Address line 2') ?>" />
								</div>

								<div class="form-group">
									<label class="sr-only" for="booking_form-student_address3"><?= __('Address line 3') ?></label>
									<input class="form-input" id="booking_form-student_address3" name="student_address3" data-field="address3" placeholder="<?= __('Address line 3') ?>" />
								</div>

								<div class="form-group">
									<label class="sr-only" for="booking_form-student_city"><?= __('City') ?></label>
									<input type="text" class="form-input" id="booking_form-student_city" name="student_city" data-field="city" placeholder="<?= __('City') ?>" />
								</div>

								<div class="form-group">
									<label class="sr-only" for="booking_form-student_county"><?= __('County') ?></label>

									<div class="select">
										<select class="form-input" id="booking_form-student_county" name="student_county" data-field="county">
											<option value="">County</option>
											<?= Model_Cities::get_all_counties_html_options() ?>
										</select>
									</div>
								</div>
							</div>

						</div>

						<div class="booking-column booking-column--half">
							<div class="form-group">
								<label class="booking-use_guardian">
									<?= __('Use different contact details than guardian') ?>
									<input type="checkbox" class="booking-use_guardian-toggle" id="use_guardian_address2" name="use_guardian_address2" data-target="#booking_form-student_contact" />
								</label>
							</div>

							<div class="hidden" id="booking_form-student_contact" data-sync_with="#booking_form-guardian_contact" data-sync="on">

								<div class="form-group form-group--required">
									<label class="sr-only" for="booking_form-student_email"><?= __('E-mail') ?></label>
									<input type="text" class="form-input validate[required,custom[email]]" id="booking_form-student_email" name="student_email" data-field="email" placeholder="<?= __('E-mail') ?> *" />
								</div>

								<div class="form-group form-group--required">
									<label class="sr-only" for="booking_form-student_mobile"><?= __('Mobile') ?></label>
									<input type="text" class="form-input validate[required,custom[irishMobile]]" id="booking_form-student_mobile" name="student_mobile" data-field="mobile" placeholder="<?= __('Mobile') ?> *" />
								</div>

								<div class="form-group">
									<label class="sr-only" for="booking_form-student_phone"><?= __('Phone') ?></label>
									<input type="text" class="form-input" id="booking_form-student_phone" name="student_phone" data-field="phone" placeholder="<?= __('Phone') ?>" />
								</div>

								<div class="form-group">
									<div class="booking-preferred">
										<p><?= __('Preferred Contact Method'); ?></p>
										<p>
											<label>
												<input type="checkbox" data-id="student_preferred_sms" id="student_preferred_sms" name="student_preferred_sms" data-field="preferred_sms" value="Yes" />
												<?= __('SMS') ?>
											</label>
											<label>
												<input type="checkbox" data-id="student_preferred_email" id="student_preferred_email" name="student_preferred_email" data-field="preferred_email" value="Yes" />
												<?= __('E-mail') ?>
											</label>
											<label>
												<input type="checkbox" data-id="student_preferred_post" id="student_preferred_post" name="student_preferred_post" data-field="preferred_post" value="Yes" />
												<?= __('Post') ?>
											</label>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="booking-row">
						<div class="booking-column">
							<p class="booking-required_field-note"><span>*</span> <?= __('Required Fields') ?></p>
						</div>
					</div>
				</div>

				<div class="booking-section booking-section--actions">
					<a href="/" class="button button--cancel" id="reset-booking"><?= __('Cancel') ?></a>
					<button type="submit" class="button button--book" id="enquiring-course" data-id="<?= $_GET['id'] ?>" data-event_id=""><?= __('Enquire Now') ?></button>
					<?php if ((isset($schedule['fee_amount'])) AND ( ! $product_enquiry)): ?>
						<button class="button" id="booking-course" data-id="<?= $_GET['id'] ?>">Book Now</button>
					<?php endif ?>
				</div>

			</form>
		<?php else: ?>
			<p>No schedule selected or the selected schedule does not exist. Please visit <a href='/course-list.html'>Course list</a> and select course and schedule.</p>
		<?php endif; ?>
	</div>
</div>

<?php include Kohana::find_file('views', 'footer'); ?>
