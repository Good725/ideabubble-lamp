			</main><?php // .main-content ?>

			<?php
			$has_signup_form = (Settings::instance()->get('newsletter_subscription_form') != 'FALSE');
			$company_title   = trim(Settings::instance()->get('company_title'));
			$address1        = trim(Settings::instance()->get('addres_line_1'));
			$address2        = trim(Settings::instance()->get('addres_line_2'));
			$address3        = trim(Settings::instance()->get('addres_line_3'));
			$telephone       = trim(Settings::instance()->get('telephone'));
			$mobile          = trim(Settings::instance()->get('mobile'));
			$fax             = trim(Settings::instance()->get('fax'));
			$email           = trim(Settings::instance()->get('email'));
			$twitter_feed_id = trim(Settings::instance()->get('twitter_feed_id'));

			?>
			<footer class="main-footer" id="main-footer">
				<div class="content-wrapper">
					<div class="footer-menus">
						<div class="col-small-12 col-medium-3 footer-contact-wrapper">
							<h3><?= __('Contact Us') ?></h3>

							<?php if ($address1 OR $address2 OR $address3): ?>
								<div>
									<ul>
										<?php if ($company_title): ?><li><?= $company_title ?></li><?php endif; ?>
										<?php if ($address1): ?><li><?= $address1 ?></li><?php endif; ?>
										<?php if ($address2): ?><li><?= $address2 ?></li><?php endif; ?>
										<?php if ($address3): ?><li><?= $address3 ?></li><?php endif; ?>
									</ul>
								</div>
							<?php endif; ?>
							<?php if ($telephone OR $mobile OR $fax OR $email): ?>
								<dl class="footer-contact-list">
									<?php if ($telephone): ?>
										<dt><?= __('Phone') ?></dt>
										<dd><?= $telephone ?></dd>
									<?php endif; ?>

									<?php if ($mobile): ?>
										<dt><?= __('Mobile') ?></dt>
										<dd><?= $mobile ?></dd>
									<?php endif; ?>

									<?php if ($fax): ?>
										<dt><?= __('Fax') ?></dt>
										<dd><?= $fax ?></dd>
									<?php endif; ?>

									<?php if ($email): ?>
										<dt><?= __('Email') ?></dt>
										<dd><?= $email ?></dd>
									<?php endif; ?>
								</dl>
							<?php endif; ?>

							<?php if (trim($facebook_url.$twitter_url.$youtube_url)): ?>
								<div class="social-media-icons">
									<h4><?= __('Connect With Us') ?></h4>

									<?php if ($facebook_url): ?>
										<a href="<?= $facebook_url ?>"><img src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/social/facebook-icon.png" alt="<?= __('Join us on Facebook') ?>" title="<?= __('Join us on Facebook') ?>" /></a>
									<?php endif; ?>
									<?php if ($twitter_url): ?>
										<a href="<?= $twitter_url ?>"><img src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/social/twitter-icon.png" alt="Twitter" title="Twitter" /></a>
									<?php endif; ?>
									<?php if ($youtube_url): ?>
										<a href="<?= $youtube_url ?>"><img src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/social/youtube-icon.png" alt="YouTube" title="YouTube" /></a>
									<?php endif; ?>
									<?= menuhelper::add_menu_editable_heading('footer_icons', 'footer-icons') ?>
								</div>
							<?php endif; ?>

						</div>
						<div class="col-small-12 col-medium-<?= ($has_signup_form OR $twitter_feed_id) ? 6 : 9 ?>">
							<?php menuhelper::add_menu_editable_heading('footer', 'footer-links-inner')?>
						</div>

						<?php if ($has_signup_form): ?>
							<div class="col-small-12 col-medium-3">
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
										<input type="submit" name="submit-newsletter" value="<?= __('Submit') ?>" id="submit-newsletter"
											   class="newsletter-form-submit submit"/>
									</form>
								</div>

							</div>
						<?php endif; ?>

						<?php if ($twitter_feed_id): ?>
							<div class="col-small-12 col-medium-3 twitter-feed-wrapper">
								<div class="twitter-feed-inner">
									<a class="twitter-timeline" href="#" data-widget-id="<?= $twitter_feed_id ?>"><?= __('Tweets') ?></a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
								</div>
							</div>
						<?php endif; ?>

					</div>
					<!--this div holds the footer image -->
					<img class="footer_logo" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/LCETB_LOGO.png"/>
				</div>
				<div class="footer-copyright">
					<div class="content-wrapper compact-cols">
						<?php $cms_copyright = Settings::instance()->get('cms_copyright'); ?>
						<div class="col-xsmall-12 col-medium-6 company-copyright">
							<?= Settings::instance()->get('company_copyright') ?>
						</div>
						<div class="col-xsmall-12 col-medium-6 cms-copyright">
							<?= ($cms_copyright == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : $cms_copyright ?>
						</div>
					</div>
				</div>
			</footer>

		</div><?php // .wrapper ?>

		<?php if ($page_data['layout'] == 'home'): ?>
			<div class="col-medium-0">
				<div class="modal search-modal" id="search-modal" role="dialog" tabindex="-1">
					<form class="modal-dialog" action="<?= URL::base() ?>search-results.html#search-criteria">
						<header class="modal-header">
							<button type="button" class="button-link modal-close">&times;</button>
							<h2 class="modal-heading"><?= __('Search') ?></h2>
						</header>

						<div class="modal-body">
							<div class="col-xsmall-12">
								<label class="sr-only" for="search-modal-destination"><?= __('Keywords') ?></label>
								<input type="text" class="input-styled" id="search-modal-destination" placeholder="<?= __('Keywords') ?>" />
							</div>

							<div class="col-xsmall-6">
								<label class="sr-only" for="search-modal-check_in"><?= __('Check in') ?></label>
								<input type="text" class="input-styled datepicker" id="search-modal-check_in" placeholder="<?= __('Check in') ?>" />
							</div>

							<div class="col-xsmall-6">
								<label class="sr-only" for="search-modal-check_out"><?= __('Check out') ?></label>
								<input type="text" class="input-styled datepicker" id="search-modal-check_out" placeholder="<?= __('Check out') ?>" />
							</div>

							<div class="col-xsmall-12">
								<label class="sr-only" for="search-modal-number_of_guests"><?= __('Course type') ?></label>
								<div class="select">
									<select class="input-styled search-number_of_guests" id="search-modal-number_of_guests" name=guests>
										<option value=""><?= __('Course Type') ?></option>
									</select>
								</div>
							</div>
							<div class="col-xsmall-12">
								<button type="submit" class="button-full button-primary"><?= __('Search') ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		<?php endif; ?>

		<div class="modal modal-centered view-wishlist-modal" id="view-wishlist-modal" role="dialog" tabindex="-1">
			<div class="modal-dialog">
				<header class="modal-header">
					<button type="button" class="button-link modal-close">&times;</button>
					<h2 class="modal-heading"><?= __('Wishlist') ?></h2>
				</header>
				<div class="modal-body" id="view-wishlist-modal-body"></div>
				<footer class="modal-footer">
					<button type="button" class="button-primary modal-close"><?= __('OK') ?></button>
				</footer>
			</div>
		</div>

		<?php // Image slider dialog ?>
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

		<script src="<?= URL::site() ?>assets/shared/js/daterangepicker/jquery.daterangepicker.min.js"></script>
	</body>
</html>