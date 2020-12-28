<?php
$banner_search = true;
$course_categories = Model_Categories::get_all_published_categories();
include 'template_views/header.php';
?>

<?php // This is temporary, until we get have a solution for displaying maps ?>
<div class="contact-map" id="content_start">
	<div class="contact-map-overlay">
		<div class="row">
			<div class="contact-map-overlay-content">
				<div>
					<h2>Where to find us in Limerick:</h2>
					<p>Kilmartin Educational Services is located on<br />83 O&#39;Connell St., Limerick.</p>
				</div>
			</div>
		</div>
	</div>

	<div class="contact-map-map">
		<iframe class="contact-map-iframe" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2419.998975625315!2d-8.632925448867036!3d52.659996079741966!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x485b5c6390461f8f%3A0x87b921b3f59e8e5b!2sKilmartin+Educational+Services!5e0!3m2!1sen!2sie!4v1456482212273"></iframe>
	</div>
</div>

<div class="row">
	<div class="contact-columns">
		<div class="contact-column">
			<h2>Limerick Opening Hours<br />(During the school term):</h2>

			<ul class="list-unstyled">
				<li>Monday to Thursday: 9.00 am to 10.00 pm</li>
				<li>Friday: 9.00 am to 9.00 pm</li>
				<li>Saturday: 8.30 am to 6.00 pm</li>
				<li>Sunday (From September 25th): 10.00 am to 4.00 pm</li>
			</ul>
		</div>
		<div class="contact-column">
			<h2>Limerick Summer Opening Hours<br />(During July/August):</h2>

			<dl>
				<dt>Limerick Opening Hours for July</dt>
				<dd>
					<ul class="list-unstyled">
						<li>Monday to Thursday: 10.00 am to 3.00 pm</li>
						<li>Closed at weekends</li>
					</ul>
				</dd>

				<dt>Limerick Opening Hours for August</dt>
				<dd>
					<ul class="list-unstyled">
						<li>Monday to Thursday: 9.00 am to 5.00 pm</li>
						<li>Closed at weekends</li>
					</ul>
				</dd>
			</dl>
		</div>
	</div>
</div>

<div class="contact-map">
	<div class="contact-map-overlay">
		<div class="row">
			<div class="contact-map-overlay-content">
				<div>
					<h2>Where to find us in Ennis:</h2>
					<p>Kilmartin Educational Services is located on<br />6A Bindon St., Ennis.</p>
				</div>
			</div>
		</div>
	</div>

	<div class="contact-map-map">
		<iframe class="contact-map-iframe" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2409.7031700817884!2d-8.987620584037826!3d52.84573347987714!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x485b12d2a9f8c2d5%3A0x3f8bb7d5b62b9070!2sKilmartin+Educational+Services!5e0!3m2!1sen!2sie!4v1456482826779"></iframe>
	</div>
</div>


<div class="row">
	<div class="contact-columns">
		<div class="contact-column">
			<h2>Ennis Opening Hours<br />(During the school term):</h2>

			<ul class="list-unstyled">
				<li>Monday to Friday: 30 minutes before the first class begins</li>
				<li>Saturday: 9.00 am to 4.00 pm</li>
			</ul>
		</div>

		<div class="contact-column">
			<h2>Ennis Summer Opening Hours<br />(During July/August):</h2>

			<ul class="list-unstyled">
				<li>Closed during July and August</li>
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-content"><?= trim($page_data['content']) ?></div>
</div>

<div class="row">
	<?php $form_identifier = 'contact_'; ?>
	<form action="#" method="post" class="contact-form" id="form-contact-us">
		<input type="hidden" value="Contact form" name="subject" />
		<input type="hidden" value="Kilmartin Education Services" name="business_name" />
		<input type="hidden" value="contact-us-thank-you.html" name="redirect" />
		<input type="hidden" value="contact_us" name="trigger" />

		<div class="contact-form-row">
			<div class="contact-form-column contact-form-column--half">
				<div class="form-group">
					<label class="sr-only" for="<?= $form_identifier ?>form_name"><?= __('Name') ?></label>
					<input type="text" class="form-input validate[required]" id="<?= $form_identifier ?>form_name" name="<?= $form_identifier ?>form_name" placeholder="<?= __('Name') ?> *" />
				</div>
			</div>

			<div class="contact-form-column contact-form-column--half">
				<div class="form-group">
					<label class="sr-only" for="<?= $form_identifier ?>form_address"><?= __('Address') ?></label>
					<input type="text" class="form-input validate[required]" id="<?= $form_identifier ?>form_address" name="<?= $form_identifier ?>form_address" placeholder="<?= __('Address') ?> *" />
				</div>
			</div>
		</div>

		<div class="contact-form-row">
			<div class="contact-form-column contact-form-column--half">
				<div class="form-group">
					<label class="sr-only" for="<?= $form_identifier ?>form_email_address"><?= __('Email') ?></label>
					<input type="text" class="form-input validate[required,custom[email]]" id="<?= $form_identifier ?>form_email_address" name="<?= $form_identifier ?>form_email_address" placeholder="<?= __('Email') ?> *" />
				</div>
			</div>

			<div class="contact-form-column contact-form-column--half">
				<div class="form-group">
					<label class="sr-only" for="<?= $form_identifier ?>form_tel"><?= __('Phone') ?></label>
					<input type="text" class="form-input validate[required,custom[phone]]" id="<?= $form_identifier ?>form_tel" name="<?= $form_identifier ?>form_tel" placeholder="<?= __('Phone') ?> *" />
				</div>
			</div>

            <?php
            $message = '';
            if (isset($_GET['interested_in_course_id']) && class_exists('Model_Courses')) {
                $course_id = Kohana::sanitize($_GET['interested_in_course_id']);
                $course = @Model_Courses::get_course($course_id);
                if (isset($course['id'])) {
                    $message .= 'I am interested in hearing more about Course #'.$course['id'].': '.$course['title']."\n";
                }
            }
            if (isset($_GET['interested_in_schedule_id']) && class_exists('Model_Courses')) {
                $schedule_id = Kohana::sanitize($_GET['interested_in_schedule_id']);
                $schedule = @Model_Schedules::get_schedule($schedule_id);
                if (isset($course['id'])) {
                    $message .= 'I am interested in hearing more about Schedule #'.$schedule['id'].': '.$schedule['name']."\n";
                }
            }
            ?>

			<div class="contact-form-column contact-form-column--middle">
				<div class="form-group">
					<label class="sr-only" for="<?= $form_identifier ?>form_message"><?= __('Comments') ?></label>
					<textarea class="form-input validate[required]" id="<?= $form_identifier ?>form_message" name="<?= $form_identifier ?>form_tel" placeholder="<?= __('Comments') ?> *" rows="2"><?= $message ?></textarea>
				</div>

				<label>
					<input type="checkbox" id="<?= $form_identifier ?>form_add_to_list" name="<?= $form_identifier ?>form_add_to_list" value="1" data-id="<?= $form_identifier ?>form_add_to_list_span" />
					<?= __('I would like to sign up to the newsletter') ?>
				</label>

				<div class="contact-form-bottom">
					<?php
					$captcha_enabled = Settings::instance()->get('captcha_enabled');
					if ($captcha_enabled) {
						require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
						$captcha_public_key = Settings::instance()->get('captcha_public_key');
						echo recaptcha_get_html($captcha_public_key);
					}
					?>
					<button type="submit" class="button button--send" id="submit-contact-us">Send E-mail</button>

					<div class="contact-form-required_note"><span>*</span> Required Fields</div>
				</div>
			</div>
		</div>
	</form>
</div>

<?php include Kohana::find_file('views', 'footer'); ?>
