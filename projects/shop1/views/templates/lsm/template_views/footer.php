			<footer class="footer">

				<div class="footer-columns">
					<div class="row">
						<div class="footer-logo">
							<img src="/assets/<?= $assets_folder_path ?>/images/footer-logo.png" alt="" />
						</div>
						<div class="footer-column footer-column--contact">
							<h3 class="footer-column-title"><?= __('Contact Us') ?></h3>
 
							<ul>
								<li>
									<h4>Limerick School of Music</h4>
									<p><i class="fa fa-map-marker" aria-hidden="true"></i>Mulgrave Street <br/>Limerick, V94 HV02</p>
								</li>
								<? if(!empty($settings['telephone'])): ?>
									<li><p><i class="fa fa-phone" aria-hidden="true"></i><?= $settings['telephone'] ?></p></li>
								<? endif; ?>
								<? if(!empty($settings['email'])): ?>
									<li><p><i class="fa fa-envelope" aria-hidden="true"></i><?= $settings['email'] ?></p></li>
								<? endif; ?>
							</ul>
						</div>

						<?php $footer_links = Menuhelper::get_all_published_menus('footer'); ?>

						<?php foreach ($footer_links as $footer_group): ?>
							<?php if (empty($footer_group['parent_id'])): ?>
								<div class="footer-column" data-group="<?= $footer_group['title'] ?>">
									<h3 class="footer-column-title"><?= $footer_group['title'] ?></h3>

									<ul>
										<?php foreach ($footer_links as $footer_link): ?>
											<?php if (isset($footer_link['parent_id']) AND $footer_link['parent_id'] == $footer_group['id']): ?>
												<li><a href="<?= menuhelper::get_link($footer_link); ?>"><?= $footer_link['title'] ?></a></li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>

                        <?php if (trim(Settings::instance()->get('facebook_url')) && Settings::instance()->get('footer_facebook_feed') == 1): ?>
                            <div id="fb-root"></div>
                            <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2"></script>

                            <div class="footer-column footer-column--facebook">
                                <div class="fb-page" data-href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url') ?>"
                                     data-tabs="timeline" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false" data-height="400">
                                    <blockquote cite="https://www.facebook.com/<?= Settings::instance()->get('facebook_url') ?>" class="fb-xfbml-parse-ignore">
                                        <a href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url') ?>"><?= Settings::instance()->get('company_name') ?></a>
                                    </blockquote>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php $footer_bottom_menu = Menuhelper::get_all_published_menus('footer_bottom'); ?>

                    <?php if ($footer_bottom_menu): ?>
                        <?php $image_path = Model_Media::get_image_path(null, 'menus'); ?>

                        <div class="footer-credit_cards">
                            <div class="row">
                                <?php foreach ($footer_bottom_menu as $menu_item): ?>
                                    <?php
                                    $menu_text = ($menu_item['filename'])
                                        ? '<img src="'.$image_path.$menu_item['filename'].'" alt="'.$menu_item['title'].'" title="'.__($menu_item['title']).'" />'
                                        : htmlentities(__($menu_item['title']));
                                    ?>

                                    <?php if (empty($menu_item['link_tag']) && empty($menu_item['link_url'])): ?>
                                        <?= $menu_text ?>
                                    <?php else: ?>
                                        <a href="<?= menuhelper::get_link($menu_item) ?>">
                                            <?= $menu_text ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
				</div>
				<?php
				$social_media['facebook_url']  = trim($settings['facebook_url']);
				$social_media['twitter_url']   = trim($settings['twitter_url']);
				$social_media['instagram_url'] = trim($settings['instagram_url']);
				$social_media['snapchat_url']  = trim($settings['snapchat_url']);
				?>

				<?php
				$social_media = array_filter($social_media);
				if (count($social_media) && !empty($social_media)): ?>
					<div class="footer-social">
						<div class="row">
							<h2>Connect with us</h2>

							<ul class="social-icons">
								<?php if (@$social_media['facebook_url']): ?>
									<li>
										<a target="_blank" href="http://facebook.com/<?= $social_media['facebook_url'] ?>" title="<?= __('Facebook') ?>">
											<span class="show-for-sr"><?= __('Facebook') ?></span>
											<span class="social-icon social-icon--facebook"></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if (@$social_media['twitter_url']): ?>
									<li>
										<a target="_blank" href="http://twitter.com/<?= $social_media['twitter_url'] ?>" title="<?= __('Twitter') ?>">
											<span class="show-for-sr"><?= __('Twitter') ?></span>
											<span class="social-icon social-icon--twitter"></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if (@$social_media['snapchat_url']): ?>
									<li>
										<a target="_blank" href="http://snapchat.com/add/<?= $social_media['snapchat_url'] ?>" title="<?= __('Snapchat') ?>">
											<span class="show-for-sr"><?= __('Snapchat') ?></span>
											<span class="social-icon social-icon--snapchat"></span>
										</a>
									</li>
								<?php endif; ?>

								<?php if (@$social_media['instagram_url']): ?>
									<li>
										<a target="_blank" href="http://instagram.com/<?= $social_media['instagram_url'] ?>" title="<?= __('Instagram') ?>">
											<span class="show-for-sr"><?= __('Instagram') ?></span>
											<span class="social-icon social-icon--instagram"></span>
										</a>
									</li>
								<?php endif; ?>
							</ul>

						</div>

					</div>
				<?php endif; ?>
				<div class="footer-copyright">
					<div class="row">
						<div class="footer-copyright-company"><?= $settings['company_copyright'] ?></div>
						<div class="footer-copyright-cms"><?= ($settings['cms_copyright'] == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a>' : $settings['cms_copyright']; ?></div>
					</div>
				</div>
			</footer>
		</div><?php // .wrapper ?>


		<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/jquery.validationEngine2.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/default/js/jquery.validationEngine2-en.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/shared/js/daterangepicker/jquery.datetimepicker.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.eventCalendar.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/swiper.min.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/scripts.js"></script>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('payments') ?>js/front_end/payments.js"></script>
        <script src="<?= URL::get_engine_assets_base() ?>js/jquery.dataTables.min.js"></script>
        <script src="<?= URL::get_engine_assets_base() ?>js/plugins.js"></script>


        <!-- 3rd party plugins -->
        <?php
        // Add in any plugin specific scripts...
        if (isset($scripts) && is_array($scripts)) {
            foreach ($scripts as $script) {
                echo $script . PHP_EOL;
            }
        }
        ?>

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
