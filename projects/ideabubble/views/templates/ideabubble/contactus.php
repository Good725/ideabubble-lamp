<?php  require_once('template_views/header.php');?>
<!-- body start here -->
<div class="body-content">
	<section class="full-row">
		<div class="fix-container relative">
			<div class="theme-heading large padd-top-50">
				<h2>Get in Touch.</h2>
			</div>
			<div class="table-box white-bg shadow">
				<div class="tab-cell theme-form">
					<div class="padd-70">
						<h4>If you need any help from us let us know by filling out the form directly below.</h4>
						<?php $form_identifier = 'contact_'; ?>
						<form action="/frontend/formprocessor" method="post" class="contact-form" id="form-contact-us">

							<input name="redirect" value="" type="hidden">
							<input name="failpage" value="" type="hidden">
							<input name="subject" value="Contact form" type="hidden">
							<input name="business_name" value="Idea Bubble" type="hidden">
							<input name="redirect" value="thank-you.html" type="hidden">
							<input name="event" value="contact-form" type="hidden">
							<input id="trigger" name="trigger" value="custom_form" type="hidden">
							<input id="form_type" name="form_type" value="Contact Form" type="hidden">
							<input name="form_identifier" value="contact_" type="hidden">
							<input id="email_template" name="email_template" value="contactformmail" type="hidden">

							<div class="form-row">
                                <?php
                                $attributes = array('class' => 'validate[required]', 'id' => $form_identifier.'form_name', 'placeholder' => __('Name'));
                                $args = array('icon' => '<span aria-hidden="true" class="icon_profile"></span>');
                                echo Form::ib_input(NULL, $form_identifier.'form_name', NULL, $attributes, $args);
                                ?>
							</div>

							<div class="form-row">
                                <?php
                                $attributes = array('class' => 'validate[required,custom[email]]', 'id' => $form_identifier.'form_email_address', 'placeholder' => __('E-mail'));
                                $args = array('icon' => '<span aria-hidden="true" class="icon_mail"></span>');
                                echo Form::ib_input(NULL, $form_identifier.'form_email_address', NULL, $attributes, $args);
                                ?>
							</div>

							<div class="form-row">
                                <?php
                                $attributes = array('id' => $form_identifier.'form_mobile', 'placeholder' => __('Mobile Phone'));
                                $args = array('icon' => '<span aria-hidden="true" class="icon_mobile"></span>');
                                echo Form::ib_input(NULL, $form_identifier.'form_mobile', NULL, $attributes, $args);
                                ?>
							</div>

							<div class="form-row">
                                <?php
                                $attributes = array('id' => $form_identifier.'form_company', 'placeholder' => __('Company Name'));
                                $args = array('icon' => '<span aria-hidden="true" class="icon_profile"></span>');
                                echo Form::ib_input(NULL, $form_identifier.'form_company', NULL, $attributes, $args);
                                ?>
							</div>

							<div class="form-row">
                                <?php
                                $attributes = array('id' => $form_identifier.'form_message', 'placeholder' => __('Type your message here...'));
                                echo Form::ib_textarea(NULL, $form_identifier.'form_message', NULL, $attributes);
                                ?>
							</div>

                            <?php if (Settings::instance()->get('captcha_enabled')): ?>
                                <div class="form-row">
                                    <script src='https://www.google.com/recaptcha/api.js'></script>

                                    <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                                </div>
                            <?php endif; ?>

							<div class="form-row aligncenter">
								<button type="submit" class="btn-primary" id="submit-contact-us">Send</button>
							</div>
						</form>
					</div>
				</div>
				<div class="tab-cell contact-info ">
					<div class="padd-70 ">
						<?= $page_data['content'];?>
					</div>
				</div>
			</div>
		</div>
		<div class="google-map">
			<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2419.73102333456!2d-8.621919884187136!3d52.66483577984183!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x485b5c6388dc6549%3A0xe3502f591a512142!2sideabubble!5e0!3m2!1sen!2sin!4v1487154686539" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
		</div>
	</section>
</div>

<!-- footer start here -->
<?php  require_once('template_views/footer.php');?>
