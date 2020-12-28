</main>
</div>
<?php
if (settings::instance()->get('address_line_1') != '')
{
	$address_line_1 = settings::instance()->get('address_line_1');
	$address_line_2 = settings::instance()->get('address_line_2');
	$address_line_3 = settings::instance()->get('address_line_3');
	$address_line_4 = settings::instance()->get('address_line_4');
}
else
{
	$address_line_1 = settings::instance()->get('addres_line_1');
	$address_line_2 = settings::instance()->get('addres_line_2');
	$address_line_3 = settings::instance()->get('addres_line_3');
	$address_line_4 = settings::instance()->get('addres_line_4');
}
?>
<footer class="footer">
	<div class="footer-links">
		<div class="footer-contact-wrapper">
			<h3 class="footer-section-header"><?= __('Contact Us') ?></h3>

			<div class="footer-section-body">
				<div class="footer-contact-address">
					<address>
						<?= (($address_line_1 != '') ? '<span class="address-line">'.$address_line_1.'</span>' : '') ?>
						<?= (($address_line_2 != '') ? '<span class="address-line">'.$address_line_2.'</span>' : '') ?>
						<?= (($address_line_3 != '') ? '<span class="address-line">'.$address_line_3.'</span>' : '') ?>
						<?= (($address_line_4 != '') ? '<span class="address-line">'.$address_line_4.'</span>' : '') ?>
					</address>
				</div>

				<div class="footer-contact-details">
					<?php if (settings::instance()->get('telephone') != ''): ?>
						<p><span class="label"><?= __('Phone') ?></span> <?= settings::instance()->get('telephone') ?>
						</p>
					<?php endif; ?>
					<?php if (settings::instance()->get('mobile') != ''): ?>
						<p><span class="label"><?= __('Mobile') ?></span> <?= settings::instance()->get('mobile') ?></p>
					<?php endif; ?>
					<?php if (settings::instance()->get('fax') != ''): ?>
						<p><span class="label"><?= __('Fax') ?></span> <?= settings::instance()->get('fax') ?></p>
					<?php endif; ?>
					<?php if (settings::instance()->get('email') != ''): ?>
						<p><span class="label"><?= __('Email') ?></span> <a title="Email us direct"
																		  href="mailto:<?= settings::instance()->get('email') ?>"><?= settings::instance()->get('email') ?></a>
						</p>
					<?php endif; ?>
				</div>

                <div class="footer-contact-socialmedia">
                    <ul class="list-inline">
                        <?php if (Settings::instance()->get('facebook_url') != '') : ?>
                            <li><a target="_blank"
                                   href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url') ?>">
                                    <span class="sr-only"><?= __('Facebook') ?></span>
                                    <span class="fa fa-facebook"></span>
                                </a></li>
                        <?php endif; ?>

                        <?php if (Settings::instance()->get('googleplus_url') != ''): ?>
                            <li><a target="_blank"
                                   href="https://plus.google.com/<?= Settings::instance()->get('googleplus_url') ?>">
                                    <span class="sr-only"><?= __('Google Plus') ?></span>
                                    <span class="fa fa-googleplus"></span>
                                </a></li>
                        <?php endif; ?>

                        <?php if (Settings::instance()->get('twitter_url') != '') : ?>
                            <li><a target="_blank"
                                   href="https://twitter.com/<?= Settings::instance()->get('twitter_url') ?>">
                                    <span class="sr-only"><?= __('Twitter') ?></span>
                                    <span class="fa fa-twitter"></span>
                                </a></li>
                        <?php endif; ?>

                        <?php if (Settings::instance()->get('linkedin_url') != '') : ?>
                            <li><a target="_blank"
                                   href="https://linkedin.com/<?= Settings::instance()->get('linkedin_url') ?>"><img
                                        src="/assets/<?= $assets_folder_path ?>/images/linkedin_icon.png"
                                        alt="<?= __('Linkedin') ?>"/></a></li>
                        <?php endif; ?>

                        <?php if (Settings::instance()->get('flickr_url') != '') : ?>
                            <li><a target="_blank"
                                   href="https://www.flickr.com/photos/<?= Settings::instance()->get('flickr_url') ?>">
                                    <span class="sr-only"><?= __('Flickr') ?></span>
                                    <span class="fa fa-flickr"></span>
                                </a></li>
                        <?php endif; ?>

                        <?php if (Settings::instance()->get('email')): ?>
                            <li><a target="_blank"
                                   href="mailto:<?= Settings::instance()->get('email') ?>">
                                    <span class="sr-only"><?= __('Email') ?></span>
                                    <span class="fa fa-envelope"></span>
                                </a></li>
                        <?php endif; ?>

                    </ul>
                </div>
			</div>
		</div>

		<div class="footer-menu-wrapper">
			<?php $twitter_feed_id = Settings::instance()->get('twitter_feed_id'); ?>
			<?php if ($twitter_feed_id): ?>
				<div class="twitter-feed-wrapper">
					<div class="footer-section-header"><?= __('Twitter') ?></div>
					<div class="footer-section-body">
						<a class="twitter-timeline" href="#" data-widget-id="<?= $twitter_feed_id ?>"><?= __('Tweets') ?></a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					</div>
				</div>
			<?php endif; ?>
			<?php menuhelper::add_menu_editable_heading('footer', 'footer-links-inner')?>
		</div>

		<div class="footer-subscribe-wrapper">
			<?php if (Settings::instance()->get('newsletter_subscription_form') != 'FALSE'): ?>
				<h3 class="footer-section-header"><?= __('Sign up to our newsletter') ?></h3>
				<div class="footer-section-body">
					<?php $form_identifier = 'newsletter_signup_' ?>
					<form id="form-newsletter" action="<?= URL::Site(); ?>frontend/formprocessor" method="post">
						<input type="hidden" name="subject" value="Newsletter Signup Form"/>
						<input type="hidden" name="business_name" value="<?= Settings::instance()->get('company_title'); ?>"/>
						<input type="hidden" name="redirect" value="thank-you-newsletter.html"/>
						<input type="hidden" name="trigger" value="add_to_list"/>
						<input type="hidden" name="form_identifier" value="<?= $form_identifier ?>"/>

						<div class="newsletter-form-group">
							<label class="newsletter-form-label"
								   for="<?= $form_identifier ?>form_email_address"><?= __('Email') ?></label>

							<div class="newsletter-form-control">
								<input type="text" name="<?= $form_identifier ?>form_email_address"
									   id="<?= $form_identifier ?>form_email_address"
									   class="validate[required,custom[email]]" placeholder="<?= __('Email') ?>"/>
							</div>
						</div>

						<div class="newsletter-form-group">
							<label class="newsletter-form-label"
								   for="<?= $form_identifier ?>form_name"><?= __('Name') ?></label>

							<div class="newsletter-form-control">
								<input type="text" name="<?= $form_identifier; ?>form_name"
									   id="<?= $form_identifier ?>form_name" class="validate[required]"
									   placeholder="<?= __('Name') ?>"/>
							</div>
						</div>

                        <?php if (Settings::instance()->get('captcha_enabled') && Settings::instance()->get('newsletter_subscription_captcha')): ?>
                            <script src='https://www.google.com/recaptcha/api.js'></script>

                            <button type="button" class="newsletter-form-submit submit" id="newsletter-captcha-toggle"><?= __('Submit') ?></button>

                            <div class="hidden" id="newsletter-captcha-section">
                                <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key'); ?>" data-size="compact"></div>

                                <input type="submit" name="submit-newsletter" value="<?= __('Submit') ?>" class="newsletter-form-submit submit" id="submit-newsletter" />
                            </div>
                        <?php else: ?>
                            <input type="submit" name="submit-newsletter" value="<?= __('Submit') ?>" class="newsletter-form-submit submit" id="submit-newsletter" />
                        <?php endif; ?>
					</form>
				</div>
			<?php endif; ?>

		</div>

	</div>
	<div class="footer_copyright">
		<div class="footer_copyright_left"><?= Settings::instance()->get('company_copyright') ?></div>
		<div class="footer_copyright_right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></div>
	</div>
</footer>
</div>
</html>
