<?php  require_once('template_views/header.php');?>
<!-- body start here -->
<div class="body-content">
	<section class="full-row">
		<div class="fix-container relative">
			<div class="theme-heading large" style="padding-top: 100px; padding-bottom: 60px;">
				<h2>Get in Touch.</h2>
			</div>
			<div class="small-container gray-bg">
				<div class="table-box shadow">
					<div class="tab-cell theme-form">
						<div class="padd-70">
							<h4>Arrange a demo to view our Educo Platform. Complete our form below and we will get in touch shortly</h4>
                            <?php $form_identifier = 'contact_'; ?>
                            <form action="/frontend/formprocessor" method="post" class="contact-form" id="form-contact-us">
                                <input name="redirect" value="/thank-you.html" type="hidden" />
                                <input name="failpage" value="" type="hidden" />
                                <input name="subject" value="Contact form" type="hidden">
                                <input name="business_name" value="Rent an Irish Cottage" type="hidden" />
                                <input name="redirect" value="thank-you.html" type="hidden" />
                                <input name="event" value="contact-form" type="hidden" />
                                <input name="trigger" value="custom_form" type="hidden" />
                                <input name="form_type" value="Contact Form" type="hidden" />
                                <input name="form_identifier" value="contact_" type="hidden" />
                                <input name="email_template" value="contactformmail" type="hidden" />

                                <div class="form-row">
                                    <label class="form-input">
                                        <span class="input-icon"><i aria-hidden="true" class="icon_profile"></i></span>
                                        <input type="text" class="form-input validate[required]" id="<?= $form_identifier ?>form_name" name="<?= $form_identifier ?>form_name" placeholder="<?= __('Name') ?>*" />
                                    </label>
                                </div>
                                <div class="form-row">
                                    <label class="form-input">
                                        <span class="input-icon"><i aria-hidden="true" class="icon_mail"></i></span>
                                        <input type="text" class="form-input validate[required,custom[email]]" id="<?= $form_identifier ?>form_email_address" name="<?= $form_identifier ?>form_email_address" placeholder="<?= __('E-mail') ?>*" />
                                    </label>
                                </div>
                                <div class="form-row">
                                    <label class="form-input">
                                        <span class="input-icon"><i aria-hidden="true" class="icon_mobile"></i></span>
                                        <input type="text" id="<?= $form_identifier ?>form_mobile" name="<?= $form_identifier ?>form_mobile" placeholder="<?= __('Mobile Phone') ?>" />
                                    </label>
                                </div>
                                <div class="form-row">
                                    <label class="form-input">
                                        <span class="input-icon"><i aria-hidden="true" class="icon_profile"></i></span>
                                        <input type="text" id="<?= $form_identifier ?>form_company" name="<?= $form_identifier ?>form_company" placeholder="<?= __('Company Name') ?>" />
                                    </label>
                                </div>
                                <div class="form-row">
                                    <label class="form-input">
                                        <textarea  id="<?= $form_identifier ?>form_message" name="<?= $form_identifier ?>form_message" placeholder="<?= __('Type your message here...') ?>"></textarea>
                                    </label>
                                </div>
                                <div class="form-row aligncenter">
                                    <button type="submit" class="btn-primary" id="submit-contact-us">Send</button>
                                </div>
                            </form>
						</div>
					</div>
					<div class="tab-cell contact-info ">
						<div class="padd-70 ">
							<?= $page_data['content'] ?>
						</div>
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
