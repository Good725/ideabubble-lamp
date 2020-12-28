<?php
$banner_search = TRUE;
include 'template_views/header.php';
$course_categories = Model_Categories::get_all_published_categories();
?>

<?php // This is temporary, until we get have a solution for displaying maps ?>
<div class="contact-map margin--65" id="content_start">
	<div class="contact-map-overlay">
		<div class="row">
			<div class="contact-map-overlay-content">
				<div>
					<div class="page-content"><?= trim($page_data['content']) ?></div>					
				</div>
			</div>
		</div>
	</div>

	<div class="contact-map-map ">
		<iframe class="contact-map-iframe" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2420.020917967458!2d-8.617738683927303!3d52.65963997984087!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x485b5c6e5da844d9%3A0x20b7658157ab23cc!2sLimerick+School+of+Music!5e0!3m2!1sen!2sin!4v1488366804289" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
	</div>
</div>

<div class="row">
	<?php $form_identifier = 'contact_'; ?>
	<div class="page-content contact--wrap">
		<h2>Enquire Now</h2>

		<form action="#" method="post" class="contact-form" id="form-contact-us">
			<input type="hidden" value="Contact form" name="subject" />
			<input type="hidden" value="<?= Settings::instance()->get('company_title'); ?>" name="business_name" />
			<input type="hidden" value="/thank-you.html" name="redirect" />
			<input type="hidden" value="contact_us" name="trigger" />

			<div class="contact-form-row">
				<div class="contact-form-column contact-form-column--half">
					<div class="form-group">
						<label for="<?= $form_identifier ?>form_name"><?= __('Name*') ?></label>
						<input type="text" class="form-input validate[required]" id="<?= $form_identifier ?>form_name" name="<?= $form_identifier ?>form_name" />
					</div>
				</div>

				<div class="contact-form-column contact-form-column--half">
					<div class="form-group">
						<label for="<?= $form_identifier ?>form_address"><?= __('Address*') ?></label>
						<input type="text" class="form-input validate[required]" id="<?= $form_identifier ?>form_address" name="<?= $form_identifier ?>form_address" />
					</div>
				</div>
			</div>

			<div class="contact-form-row">
				<div class="contact-form-column contact-form-column--half">
					<div class="form-group">
						<label for="<?= $form_identifier ?>form_email_address"><?= __('Email*') ?></label>
						<input type="text" class="form-input validate[required,custom[email]]" id="<?= $form_identifier ?>form_email_address" name="<?= $form_identifier ?>form_email_address" />
					</div>
				</div>

				<div class="contact-form-column contact-form-column--half">
					<div class="form-group">
						<label for="<?= $form_identifier ?>form_tel"><?= __('What are you enquiring about? *') ?></label>
						<input type="text" class="form-input validate[required]" id="<?= $form_identifier ?>form_tel" name="<?= $form_identifier ?>form_tel" />
					</div>
				</div>

				<div class="contact-form-column contact-form-column--middle">
					<div class="form-group">
						<label for="<?= $form_identifier ?>form_message"><?= __('Message*') ?></label>
						<textarea class="form-input validate[required]" id="<?= $form_identifier ?>form_message" name="<?= $form_identifier ?>form_message" rows="2"></textarea>
					</div>

					<label>
						<input type="checkbox" id="<?= $form_identifier ?>form_add_to_list" name="<?= $form_identifier ?>form_add_to_list" value="1" data-id="<?= $form_identifier ?>form_add_to_list_span" />
						<?= __('Check this box if you want to subscribe to our Mailing List.') ?>
					</label>

					<div class="contact-form-bottom">
						<button type="submit" class="button button--send" id="submit-contact-us">Send</button>

						<div class="contact-form-required_note"><span>*</span> Required Fields</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
    $(document).ready(function(){
        $("#submit-contact-us").click(function(ev) {
            ev.preventDefault();
            if ($("#form-contact-us").validationEngine('validate'))
            {
                $('#form-contact-us').attr('action', '/frontend/formprocessor').submit();
            }
        });
    });
</script>

<?php include 'template_views/footer.php'; ?>
