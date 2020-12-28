		<?php $settings = Settings::instance(); ?>
		<footer class="footer">
			<div class="row">
				<div class="columns ">
					<?= $page_data['footer'] ?>
				</div>
				<div class="footer-panels">
					<div class="columns small-12 medium-6 large-3 footer-panel footer-panel--contact">
						<img src="/assets/<?= $assets_folder_path ?>/images/logo.png" alt="uTicket" />
						<address class="footer-address">
							<span class="line"><?php echo Settings::instance()->get('addres_line_1'); ?></span>
							<span class="line"><?php echo Settings::instance()->get('addres_line_2'); ?></span>
							<span class="line"><?php echo Settings::instance()->get('addres_line_3'); ?></span>
							<span class="line"><?php echo Settings::instance()->get('addres_line_4'); ?></span>
							<span class="line"><?php echo Settings::instance()->get('addres_line_5'); ?></span>
						</address>

						<div class="row collapse">
							<div class="columns small-4 text-primary">Email</div>
							<div class="columns small-8"><a href="mailto:hello@uticket.ie">hello@uticket.ie</a></div>

							<div class="columns small-4 text-primary">Call us</div>
							<div class="columns small-8">+353 21 4193033</div>
						</div>
					</div>

					<?php $menu_items = menuhelper::get_all_published_menus('footer'); ?>
					<?php if ( ! empty($menu_items)): ?>
						<div class="columns small-12 medium-6 large-2 footer-panel footer-panel--menu">
							<ul class="list-unstyled">
								<?php foreach ($menu_items as $menu_item): ?>
									<?php if ($menu_item['category'] == 'footer'): ?>
										<li><a href="<?= Menuhelper::get_link($menu_item) ?>"><?= $menu_item['title'] ?></a></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<div class="columns small-12 medium-6 large-3 footer-panel footer-panel--social">
						<?php
                        if ($settings->get('twitter_api_access'))
                        {
                            echo IbTwitterApi::embed_feed();
                        }
                        ?>

						<?php
						$social_media['facebook_url']  = trim($settings->get('facebook_url'));
						$social_media['twitter_url']   = trim($settings->get('twitter_url'));
						$social_media['instagram_url'] = trim($settings->get('instagram_url'));
						$social_media['facebook_url']  = trim($settings->get('facebook_url'));
						$social_media['snapchat_url']  = trim($settings->get('snapchat_url'));
						array_filter($social_media);
						?>

						<?php if (count($social_media) > 0): ?>
							<ul class="social_media-list">
								<?php if ( ! empty($social_media['twitter_url'])): ?>
									<li>
										<a target="_blank" href="http://twitter.com/<?= $social_media['twitter_url'] ?>" title="<?= __('Twitter') ?>">
											<span class="show-for-sr"><?= __('Twitter') ?></span>
											<span class="flaticon-twitter"></span>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( ! empty($social_media['facebook_url'])): ?>
									<li>
										<a target="_blank" href="http://facebook.com/<?= $social_media['facebook_url'] ?>" title="<?= __('Facebook') ?>">
											<span class="show-for-sr"><?= __('Facebook') ?></span>
											<span class="flaticon-facebook"></span>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( ! empty($social_media['instagram_url'])): ?>
									<li>
										<a target="_blank" href="http://instagram.com/<?= $social_media['instagram_url'] ?>" title="<?= __('Instagram') ?>">
											<span class="show-for-sr"><?= __('Instagram') ?></span>
											<span class="flaticon-instagram"></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if ( ! empty($social_media['snapchat_url'])): ?>
									<li>
										<a target="_blank" href="http://snapchat.com/add/<?= $social_media['snapchat_url'] ?>" title="<?= __('Snapchat') ?>">
											<span class="show-for-sr"><?= __('Snapchat') ?></span>
											<span class="flaticon-snapchat"></span>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>

					</div>
					<?php if ( ! in_array(Settings::instance()->get('newsletter_subscription_form'), array('FALSE', '0'))): ?>
						<div class="columns small-12 medium-6 large-4 footer-panel footer-panel--subscribe">

							<?php if (count($social_media) > 0): ?>
								<ul class="social_media-list">
									<?php if ( ! empty($social_media['twitter_url'])): ?>
										<li>
											<a target="_blank" href="http://twitter.com/<?= $social_media['twitter_url'] ?>" title="<?= __('Twitter') ?>">
												<span class="show-for-sr"><?= __('Twitter') ?></span>
												<span class="flaticon-twitter"></span>
											</a>
										</li>
									<?php endif; ?>
									<?php if ( ! empty($social_media['facebook_url'])): ?>
										<li>
											<a target="_blank" href="http://facebook.com/<?= $social_media['facebook_url'] ?>" title="<?= __('Facebook') ?>">
												<span class="show-for-sr"><?= __('Twitter') ?></span>
												<span class="flaticon-facebook"></span>
											</a>
										</li>
									<?php endif; ?>
									<?php if ( ! empty($social_media['instagram_url'])): ?>
										<li>
											<a target="_blank" href="http://instagram.com/<?= $social_media['instagram_url'] ?>" title="<?= __('Instagram') ?>">
												<span class="show-for-sr"><?= __('Instagram') ?></span>
												<span class="flaticon-instagram"></span>
											</a>
										</li>
									<?php endif; ?>

									<?php if ( ! empty($social_media['snapchat_url'])): ?>
										<li>
											<a target="_blank" href="http://snapchat.com/add/<?= $social_media['snapchat_url'] ?>" title="<?= __('Snapchat') ?>">
												<span class="show-for-sr"><?= __('Snapchat') ?></span>
												<span class="flaticon-snapchat"></span>
											</a>
										</li>
									<?php endif; ?>
								</ul>
							<?php endif; ?>

							<form action="/frontend/formprocessor" method="post" class="validate-on-submit subscription_form" id="subscription_form">
								<input type="hidden" name="trigger" value="mailchimp_add" />
								<input type="hidden" name="contact_form_name" value="" />
								<label class="input-with-icon">
									<span class="show-for-sr"><?= __('Submit Email') ?></span>
									<span class="input-icon flaticon-envelope-alt"></span>
									<input
										type="text"
										class="form_field hollow validate[required,custom[email]]"
										id="subscription_form-email"
										name="contact_form_email_address"
										placeholder="<?= __('Submit Email') ?>"
										data-prompt-position="topLeft"
										/>
								</label>
								<button id="newsletter-button" type="submit" class="button secondary">
									<span class="flaticon-newsletter"></span>
									<span id="newsletter-button-text"><?= __('Subscribe to Newsletter') ?></span>
								</button>
							</form>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="footer-copyright">
				<div class="row">
					<div class="columns small-12 medium-6"><?= $settings->get('company_copyright') ?></div>
					<div class="columns small-12 medium-6 medium-text-right"><?= $settings->get('cms_copyright') ?></div>
				</div>
			</div>
		</footer>
		</div><?php // .wrapper ?>

		<?php if (Settings::instance()->get('slaask_api_access_frontend')): ?>
			<?php $slaask_api_key = trim(Settings::instance()->get('slaask_api_key')); ?>
			<?php if ($slaask_api_key): ?>
				<script src='https://cdn.slaask.com/chat.js'></script>
				<script>
					_slaask.init('<?= $slaask_api_key ?>');
				</script>
			<?php endif; ?>
		<?php endif; ?>
	</body>
</html>