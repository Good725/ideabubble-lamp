<?php
if (settings::instance()->get('address_line_1') != '')
{
	$address_line_1 = settings::instance()->get('address_line_1');
	$address_line_2 = settings::instance()->get('address_line_2');
	$address_line_3 = settings::instance()->get('address_line_3');
}
else
{
	$address_line_1 = settings::instance()->get('addres_line_1');
	$address_line_2 = settings::instance()->get('addres_line_2');
	$address_line_3 = settings::instance()->get('addres_line_3');
}

$social_media = (Settings::instance()->get('facebook_url').Settings::instance()->get('twitter_url').Settings::instance()->get('linkedin_url').Settings::instance()->get('flickr_url').Settings::instance()->get('pinterest_button') != '');
$mobile = trim(Settings::instance()->get('mobile'));
$phone  = trim(Settings::instance()->get('telephone'));
$email  = trim(Settings::instance()->get('email'));
?>
<div class="footer_wrapper">
	<div class="footer_main">
		<ul class="footer_sections">
			<li class="footer_section footer_section_contact">
				<h4>Contact Us</h4>
				<address>
					<?= $address_line_1; ?><br />
					<?= $address_line_2; ?><br />
					<?= $address_line_3; ?><br />
				</address>

				<div class="footer_contact_details">
					<?php if ($phone != ''): ?>
						<p><span>Phone</span> <?= $phone ?></p>
					<?php endif; ?>
					<?php if ($mobile != ''): ?>
						<p><span>Mobile</span> <?= $mobile ?></p>
					<?php endif; ?>
					<?php if ($email != ''): ?>
						<p><span>Email</span> <?= $email?></p>
					<?php endif; ?>
				</div>
			</li>

			<li class="footer_section foot_section_testimonials">
				<h4>Testimonials</h4>
				<!--
				<div class="img2"><a href="#"><img src="images/test_img1.jpg" alt=""></a></div>
				<p>‘Had a great day with Seamus and the team" <em>Mary O Connell</em></p>
				-->

				<?= str_replace('<h1>Latest Testimonials</h1>', '', Model_Testimonials::get_plugin_items_front_end_feed()); ?>

			</li>

			<li class="footer_section footer_section_quick_links">
				<h4>Quick Links</h4>
				<?= menuhelper::add_menu_editable_heading('footer') ?>
			</li>
		</ul>
		<div class="footer_section_signup">
			<h4>Sign up to our Newsletter</h4>
			<form class="form-horizontal form-newsletter" id="form-newsletter" method="post" action="<?= URL::site(); ?>frontend/formprocessor">
				<input type="hidden" name="subject"       value="Newsletter Signup">
				<input type="hidden" name="business_name" value="<?= Settings::instance()->get('company_title') ?>">
				<input type="hidden" name="redirect"      value="thank-you-newsletter.html">
				<input type="hidden" name="trigger"       value="add_to_list">

				<div class="form_control_group">
					<label for="newsletter_signup_name" class="form_label">Name</label>
					<div class="form_controls">
						<input class="validate[required]" id="newsletter_signup_name" name="name" type="text" />
					</div>
				</div>

				<div class="form_control_group">
					<label for="newsletter_signup_email" class="form_label">Email</label>
					<div class="form_controls">
						<input class="validate[required]" id="newsletter_signup_email" name="email" type="text" />
					</div>
				</div>

				<button type="submit" class="primary_button" id="submit-newsletter">Submit</button>
			</form>
		</div>
	</div>

	<div class="footer_sub">
		<div class="footer_bottom_wrapper">
			<?php menuhelper::add_menu_editable_heading('footer_bottom') ?>
		</div>

		<?php if ($social_media): ?>
			<div class="footer_social_media">
				<h4>Connect With Us</h4>
				<ul>
					<?php if (Settings::instance()->get('facebook_url') != '') : ?>
						<li>
							<a href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url'); ?>">
								<img src="/assets/<?= $assets_folder_path ?>/images/facebook_icon.png" alt="Facebook" />
							</a>
						</li>
						<?php if (Settings::instance()->get('twitter_url') != '') : ?>
							<li>
								<a href="https://twitter.com/<?= Settings::instance()->get('twitter_url') ?>">
									<img src="/assets/<?= $assets_folder_path ?>/images/twitter_icon.png" alt="Twitter" /></a></li>
						<?php endif; ?>
						<?php if (Settings::instance()->get('linkedin_url') != '') : ?>
							<li><a href="https://linkedin.com/<?= Settings::instance()->get('linkedin_url') ?>">
									<img src="/assets/<?= $assets_folder_path ?>/images/linkedin_icon.png" alt="LinkedIn" /></a>
								</li>
						<?php endif; ?>
						<?php if (Settings::instance()->get('flickr_url') != '') : ?>
							<li><a href="https://www.flickr.com/photos/<?= Settings::instance()->get('flickr_url') ?>">
									<img src="/assets/<?= $assets_folder_path ?>/images/flickr_icon.png" alt="Flickr" /></a></li>
						<?php endif; ?>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</div>

<div class="footer_copyright">
	<div class="footer_copyright_company"><?= Settings::instance()->get('company_copyright') ?></div>
	<div class="footer_copyright_cms"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></div>
</div>