<!-- Newsletter Signup Form -->
<div class="newsletter-box left">
	<h1>Newsletter Signup</h1>
	<?php $form_identifier = 'newsletter_signup_'?>
	<div class="left clear">
		<form id="form-newsletter" class="left" method="post">
			<input type="hidden" value="Newsletter Signup Form" name="subject">
			<input type="hidden" value="<?= Settings::instance()->get('company_title') ?>" name="business_name">
			<input type="hidden" value="thank-you-newsletter.html" name="redirect">
			<input type="hidden" value="add_to_list" name="trigger">
			<input type="hidden" value="<?=$form_identifier?>" name="form_identifier">

			<span class="inputBox left clear_left">
				<input name="<?=$form_identifier?>form_name" id="<?=$form_identifier?>form_name" class="validate[required]" type="text" placeholder="Name"/>
			</span>
			<span class="inputBox left clear_left">
				<input name="<?=$form_identifier?>form_email_address" id="<?=$form_identifier?>form_email_address" class="validate[required,custom[email]]" type="text" placeholder="Email"/>
			</span>
			<input name="submit-newsletter" id="submit-newsletter" type="submit" class="left" value="Sign Up"/>
		</form>
	</div>
</div>
<!-- /Newsletter Signup Form -->
