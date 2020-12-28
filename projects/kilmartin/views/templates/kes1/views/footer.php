<?php if (!Settings::instance()->get('shared_footer') && isset($kohana_view_data) && !empty($kohana_view_data['is_backend'])): ?>
    <?php include APPPATH.'views/footer.php'; ?>
<?php else: ?>
        <?php
        $assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
        $settings = Settings::instance()->get();
        ?>
        <?php if (trim(Settings::instance()->get('page_footer'))): ?>
            <div class="page-footer">
                <div class="row page-content">
                    <?= IbHelpers::expand_short_tags(Model_Localisation::get_ctag_translation(Settings::instance()->get('page_footer'), I18n::$lang)) ?>
                </div>
            </div>
        <?php endif; ?>

    </div><?php // .content ?>

	<footer class="footer<?= trim(Settings::instance()->get('page_footer')) ? ' mt-0' : '' ?>" id="footer">
        <?php ob_start(); ?>
            <?php $footer_logo = trim(Settings::instance()->get('site_footer_logo')); ?>

            <?php if ($footer_logo): ?>
                <?php $mobile_footer_logo = trim(Settings::instance()->get('site_mobile_footer_logo')); ?>

                <div class="footer-logo">
                    <?php if ($mobile_footer_logo): ?>
                        <img src="<?= Model_Media::get_image_path($footer_logo,        'logos') ?>" alt="" class="hidden--mobile" />
                        <img src="<?= Model_Media::get_image_path($mobile_footer_logo, 'logos') ?>" alt="" class="hidden--tablet hidden--desktop" />
                    <?php else: ?>
                        <img src="<?= Model_Media::get_image_path($footer_logo,        'logos') ?>" alt="" />
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (trim(Settings::instance()->get('company_slogan'))): ?>
                <p class="footer-slogan"><?= Settings::instance()->get('company_slogan') ?></p>
            <?php endif; ?>

            <?php
            $panel_model = new Model_Panels();
            $footer_panels = $panel_model->get_panels('footer');
            ?>
            <?php if (count($footer_panels) > 0): ?>
                <div class="footer-stats-row clearfix">
                    <ul class="footer-stats-list">
                        <?php foreach ($footer_panels as $panel): ?>
                            <li class="footer-stat"><?= Model_Localisation::get_ctag_translation($panel['text'], I18n::$lang) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
            $app_store_link   = trim(Settings::instance()->get('app_store_link'));
            $google_play_link = trim(Settings::instance()->get('google_play_link'));
            ?>

            <?php if ($app_store_link || $google_play_link): ?>
                <div class="footer-apps">
                    <h2><?= __('Download our app on') ?></h2>
                    <?php if ($app_store_link): ?>
                        <a href="<?= $app_store_link ?>" target="_blank">
                            <img src="<?= URL::get_engine_assets_base().'img/app-store-badge.svg' ?>" alt="<? __('Download on the App Store') ?>" />
                        </a>
                    <?php endif; ?>

                    <?php if ($google_play_link): ?>
                        <a href="<?= $google_play_link ?>" target="_blank">
                            <img src="<?= URL::get_engine_assets_base().'img/google-play-badge.svg' ?>" alt="<?= __('Get it on Google Play') ?>" />
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php $footer_stats_section = ob_get_clean(); ?>

        <?php if (trim($footer_stats_section)): ?>
            <div class="footer-stats clearfix">
                <?= $footer_stats_section ?>
            </div>
        <?php endif; ?>

		<?php
        $social_media['facebook_url']  = trim($settings['facebook_url']);
        $social_media['twitter_url']   = trim($settings['twitter_url']);
        $social_media['linkedin_url']  = trim($settings['linkedin_url']);
        $social_media['instagram_url'] = trim($settings['instagram_url']);
        $social_media['snapchat_url']  = trim($settings['snapchat_url']);
        $social_media['youtube_url']   = trim($settings['youtube_url']);

		if (count(array_filter($social_media))): ?>
			<div class="footer-social">
				<div class="row">
                    <h2><?= __('Follow us') ?></h2>

                    <ul class="social-icons">
                        <?php if (!empty($social_media['facebook_url'])): ?>
                            <?php $url = (strpos($social_media['facebook_url'], 'http') === 0) ? $social_media['facebook_url'] : 'http://facebook.com/'.$social_media['facebook_url']; ?>
                            <li>
                                <a target="_blank" href="<?= $url ?>" title="<?= __('Facebook') ?>">
                                    <span class="show-for-sr"><?= __('Facebook') ?></span>
                                    <span class="social-icon social-icon--facebook"></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($social_media['twitter_url'])): ?>
                            <?php $url = (strpos($social_media['twitter_url'], 'http') === 0) ? $social_media['twitter_url'] : 'http://twitter.com/'.$social_media['twitter_url']; ?>
                            <li>
                                <a target="_blank" href="<?= $url ?>" title="<?= __('Twitter') ?>">
                                    <span class="show-for-sr"><?= __('Twitter') ?></span>
                                    <span class="social-icon social-icon--twitter"></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($social_media['linkedin_url'])): ?>
                            <?php $url = (strpos($social_media['linkedin_url'], 'http') === 0) ? $social_media['linkedin_url'] : 'http://linkedin.com/'.$social_media['linkedin_url']; ?>
                            <li>
                                <a target="_blank" href="<?= $url ?>" title="<?= __('LinkedIn') ?>">
                                    <span class="show-for-sr"><?= __('LinkedIn') ?></span>
                                    <span class="social-icon social-icon--linkedin"></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($social_media['snapchat_url'])): ?>
                            <?php $url = (strpos($social_media['snapchat_url'], 'http') === 0) ? $social_media['snapchat_url'] : 'http://snapchat.com/add/'.$social_media['snapchat_url']; ?>
                            <li>
                                <a target="_blank" href="<?= $url ?>" title="<?= __('Snapchat') ?>">
                                    <span class="show-for-sr"><?= __('Snapchat') ?></span>
                                    <span class="social-icon social-icon--snapchat"></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($social_media['instagram_url'])): ?>
                            <?php $url = (strpos($social_media['instagram_url'], 'http') === 0) ? $social_media['instagram_url'] : 'http://instagram.com/'.$social_media['instagram_url']; ?>
                            <li>
                                <a target="_blank" href="<?= $url ?>" title="<?= __('Instagram') ?>">
                                    <span class="show-for-sr"><?= __('Instagram') ?></span>
                                    <span class="social-icon social-icon--instagram"></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($social_media['youtube_url'])): ?>
                            <?php $url = (strpos($social_media['youtube_url'], 'http') === 0) ? $social_media['youtube_url'] : 'https://youtube.com/'.$social_media['youtube_url']; ?>
                            <li>
                                <a target="_blank" href="<?= $url ?>" title="<?= __('YouTube') ?>">
                                    <span class="show-for-sr"><?= __('YouTube') ?></span>
                                    <span class="social-icon social-icon--youtube"></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
				</div>
			</div>
		<?php endif; ?>

        <?php $footer_bottom_menu = Menuhelper::get_all_published_menus('footer_bottom'); ?>

        <?php ob_start(); ?>
            <?php if ($footer_bottom_menu): ?>
                <?php $image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'menus'); ?>

                <div class="footer-credit_cards">
                    <?php foreach ($footer_bottom_menu as $menu_item): ?>
                        <?php
                        $menu_text = ($menu_item['filename'])
                            ? '<img src="'.$image_path.$menu_item['filename'].'" alt="'.$menu_item['title'].'" title="'.__($menu_item['title']).'" />'
                            : htmlentities(__($menu_item['title']));
                        ?>

                        <?php if (empty($menu_item['link_tag']) && empty($menu_item['link_url'])): ?>
                            <?= $menu_text ?>
                        <?php else: ?>
                            <a
                                href="<?= menuhelper::get_link($menu_item) ?>"
                                <?= $menu_item['html_attributes'] ?>
                            >
                                <?= $menu_text ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php $footer_bottom = ob_get_clean(); ?>

        <div class="hidden--tablet hidden--desktop hidden-sm hidden-md hidden-lg hidden-xl"><?= $footer_bottom ?></div>

        <?php $footer_bottom_panels = $panel_model->get_panels('footer_bottom'); ?>

        <?php if (count($footer_bottom_panels) > 0): ?>
            <div class="footer-columns footer-columns--footer_bottom">
                <div class="container">
                    <div class="row gutters">
                        <?php foreach ($footer_bottom_panels as $panel): ?>
                            <div class="footer-column has_sublist">
                                <button type="button" class="footer-column-title"><?= htmlentities($panel['title']) ?></button>

                                <div class="footer-column-content">
                                    <?= IbHelpers::expand_short_tags($panel['text']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="hidden--mobile hidden-xs"><?= $footer_bottom ?></div>
        <?php else: ?>
            <div class="footer-columns">
                <div class="container">
                    <div class="row gutters">
                        <div class="footer-column has_sublist footer-column--contact">
                            <button type="button" class="footer-column-title"><?= __('Contact details') ?></button>

                            <ul class="footer-column-content">
                                <?php if (!empty($settings['addres_line_1'])): ?>
                                    <li>
                                        <h4><?= htmlentities($settings['addres_line_1']) ?></h4>

                                        <?php if (!empty($settings['addres_line_2'])): ?>
                                            <span class="footer-address-line"><?= trim(str_replace('&lt;br /&gt;', '<br />', htmlentities($settings['addres_line_2']))) ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($settings['addres_line_3'])): ?>
                                            <span class="footer-address-line"><?= trim(str_replace('&lt;br /&gt;', '<br />', htmlentities($settings['addres_line_3']))) ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($settings['addres_line_4'])): ?>
                                            <span class="footer-address-line"><?= trim(str_replace('&lt;br /&gt;', '<br />', htmlentities($settings['addres_line_4']))) ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($settings['addres_line_5'])): ?>
                                            <span class="footer-address-line"><?= trim(str_replace('&lt;br /&gt;', '<br />', htmlentities($settings['addres_line_5']))) ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($settings['address2_line_1'])): ?>
                                    <li>
                                        <h4><?= htmlentities($settings['address2_line_1']) ?></h4>

                                        <?php if (!empty($settings['address2_line_2'])): ?>
                                            <span class="footer-address-line"><?= trim(htmlentities($settings['address2_line_2'])) ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($settings['address2_line_3'])): ?>
                                            <span class="footer-address-line"><?= trim(htmlentities($settings['address2_line_3'])) ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($settings['address2_line_4'])): ?>
                                            <span class="footer-address-line"><?= trim(htmlentities($settings['address2_line_4'])) ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($settings['address2_line_5'])): ?>
                                            <span class="footer-address-line"><?= trim(htmlentities($settings['address2_line_5'])) ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($settings['address3_line_1'])): ?>
                                    <li>
                                        <h4><?= htmlentities($settings['address3_line_1']) ?></h4>

                                        <?php if (!empty($settings['address3_line_2'])): ?>
                                            <span class="footer-address-line"><?= trim(htmlentities($settings['address3_line_2'])) ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($settings['address3_line_3'])): ?>
                                            <span class="footer-address-line"><?= trim(htmlentities($settings['address3_line_3'])) ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <dl class="footer-contact-items clearfix">
                                        <?php if (trim($settings['telephone'])): ?>
                                            <dt><?= __('Tel') ?></dt>
                                            <dd><?= $settings['telephone'] ?></dd>
                                        <?php endif; ?>

                                        <?php if (isset($settings['mobile']) && trim($settings['mobile'])): ?>
                                            <dt><?= __('Mobile') ?></dt>
                                            <dd><?= $settings['mobile'] ?></dd>
                                        <?php endif; ?>

                                        <?php if (trim($settings['fax'])): ?>
                                            <dt><?= __('Fax') ?></dt>
                                            <dd><?= $settings['fax'] ?></dd>
                                        <?php endif; ?>

                                        <?php if (trim($settings['email'])): ?>
                                            <dt><?= __('Email') ?></dt>
                                            <dd>
                                                <a href="mailto:<?= trim($settings['email']) ?>"><?= $settings['email'] ?></a>
                                            </dd>
                                        <?php endif; ?>
                                    </dl>
                                </li>
                            </ul>

                            <?php if (Settings::instance()->get('footer_contact_html')): ?>
                                <div class="footer-contact-more">
                                    <?= IbHelpers::parse_page_content(Settings::instance()->get('footer_contact_html')) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php $footer_links = Menuhelper::get_items(array('category' => 'footer')); ?>

                        <?php foreach ($footer_links as $footer_group): ?>
                            <?php if (empty($footer_group['parent_id'])): ?>
                                <?php ob_start(); ?>
                                    <?php foreach ($footer_links as $footer_link): ?>
                                        <?php if (isset($footer_link['parent_id']) AND $footer_link['parent_id'] == $footer_group['id']): ?>
                                            <li>
                                                <a
                                                    href="<?= menuhelper::get_link($footer_link); ?>"
                                                    <?= $footer_link['html_attributes'] ?>
                                                ><?= htmlentities(__($footer_link['title'])) ?></a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php $sublist = ob_get_clean(); ?>

                                <?php if (trim($sublist)): ?>
                                    <div class="footer-column has_sublist" data-group="<?= $footer_group['title'] ?>">
                                        <button type="button" class="footer-column-title"><?= htmlentities(__($footer_group['title'])) ?></button>
                                        <ul class="footer-column-content">
                                            <?= $sublist ?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <div class="footer-column" data-group="<?= $footer_group['title'] ?>">
                                        <a
                                            <?php
                                            $attributes = array();
                                            foreach (menuhelper::get_attributes($footer_group) as $attr_name => $attr_value) {
                                                $attributes[$attr_name]  = $attr_value;
                                            }
                                            $attributes['class'] = ($attributes['class'] ?: '') . 'footer-column-title';
                                            $attributes['href']  = $attributes['href'] ?: menuhelper::get_link($footer_group);
                                            echo html::attributes($attributes);
                                            ?>
                                        ><?= htmlentities(__($footer_group['title'])) ?></a>
                                    </div>
                                <?php endif; ?>
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

                        <?php $has_subscription_form = (Settings::instance()->get('newsletter_subscription_form') !== 'FALSE'); ?>

                        <?php if ($has_subscription_form): ?>
                            <div class="footer-column has_sublist footer-column--newsletter">
                                <button type="button" class="footer-column-title"><?= __('Newsletter signup') ?></button>
                                <?php $form_identifier = 'newsletter_signup_'; ?>

                                <form class="footer-column-content newsletter-signup-form" id="form-newsletter" method="post">
                                    <input type="hidden" name="subject" value="Newsletter Subscription Form" />
                                    <input type="hidden" name="business_name" value="<?= Settings::instance()->get('company_name') ?>" />
                                    <input type="hidden" name="redirect" value="subscription-thank-you.html" />
                                    <input type="hidden" name="trigger" value="add_to_list" />
                                    <input type="hidden" name="form_identifier" value="<?= $form_identifier ?>" />

                                    <div class="form-group">
                                        <input type="text" class="form-input validate[required]" id="newsletter-form-name" name="<?= $form_identifier ?>form_name" placeholder="<?= __('Name') ?>" />
                                    </div>

                                    <div class="form-group">
                                        <input type="text" class="form-input validate[required,custom[email]]" id="newsletter-form-email" name="<?= $form_identifier ?>form_email_address" placeholder="<?= __('E-mail') ?>" />
                                    </div>

                                    <?php if (Settings::instance()->get('captcha_enabled') && Settings::instance()->get('newsletter_subscription_captcha')): ?>
                                        <script src='https://www.google.com/recaptcha/api.js'></script>

                                        <div class="captcha-section hidden" id="newsletter-captcha-section">
                                            <div class="form-group">
                                                <input type="text" class="sr-only" id="form-newsletter-captcha-hidden" tabindex="-1" />

                                                <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (Settings::instance()->get('newsletter_signup_terms')): ?>
                                        <div class="form-group">
                                            <label class="newsletter-signup-terms d-flex">
                                                <?= Form::ib_checkbox(null, 'terms', 1, false, ['class' => 'validate[required]']) ?>
                                                <div class="newsletter-signup-terms-text"><?= Settings::instance()->get('newsletter_signup_terms') ?></div>
                                            </label>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <button type="submit" class="button button--newsletter" id="submit-newsletter"><?= htmlentities(__('Submit')) ?></button>
                                    </div>
                                </form>

                                <?php if (trim($footer_bottom)): ?>
                                    <div class="hidden--mobile hidden-xs"><?= $footer_bottom ?></div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php if (trim($footer_bottom)): ?>
                                <div class="hidden--mobile hidden-xs"><?= $footer_bottom ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer-copyright">
            <div class="row">
                <div class="footer-copyright-company"><?= $settings['company_copyright'] ?></div>
                <div class="footer-copyright-cms"><?= ($settings['cms_copyright'] == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a>' : $settings['cms_copyright']; ?></div>
            </div>
        </div>

        <?php if (Settings::instance()->get('footer_bottom_html')): ?>
            <div class="footer-bottom">
                <div class="row">
                    <?= IbHelpers::parse_page_content(Settings::instance()->get('footer_bottom_html')) ?>
                </div>
            </div>
        <?php endif; ?>
    </footer>
</div><?php // .wrapper ?>
        <?php $is_backend = (isset($is_backend) && $is_backend === true); ?>

        <?php if (!$is_backend): ?>
            <script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-3.3.5.min.js"></script>
            <script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-toggle/bootstrap-toggle.min.js"></script>
            <script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-multiselect.js"></script>
            <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
            <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/jquery.validationEngine2.js"></script>
            <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/jquery.validationEngine2-en.js"></script>
            <script type="text/javascript" src="<?= URL::overload_asset('js/forms.js', ['cachebust' => true]) ?>"></script>
        <?php endif; ?>
		<script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/daterangepicker/jquery.datetimepicker.js"></script>
		<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('courses') ?>js/jquery.eventCalendar.js"></script>
        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/swiper.min.js?ts=<?= @filemtime(APPPATH.'assets/shared/js/swiper.min.js') ?>"></script>
        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/jquery.bootpag.min.js"></script>
        <script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/js.cookie.js"></script>
        <script type="text/javascript" src="<?= URL::overload_asset('js/educate_template.js', ['cachebust' => true]) ?>"></script>
        <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('payments') ?>js/front_end/payments.js"></script>
        <script src="<?= URL::get_engine_assets_base() ?>js/jquery.dataTables.min.js"></script>
        <script src="<?= URL::get_engine_assets_base() ?>js/plugins.js"></script>

    <?php
    if (((Settings::instance()->get('browser_sniffer_backend') && $is_backend) || (Settings::instance()->get('browser_sniffer_frontend') && !$is_backend))
        && (!isset($page_data['assets_implemented']['browser_sniffer']) || !$page_data['assets_implemented']['browser_sniffer'])): ?>
        <!-- Browser detection -->
        <script type="text/javascript">
            var test_mode = <?= (Settings::instance()->get('browser_sniffer_testmode') == 1) ? 'true' : 'false' ?>;
            var $buoop_unsupported_browsers = <?= json_encode(Settings::instance()->get('browser_sniffer_unsupported_options')) ?>;
            var $buoop_rcmd_browser = '<?= Settings::instance()->get('browser_sniffer_recommended_browser') ?>';
            var $buoop_vs = {
                i: '<?= Settings::instance()->get('browser_sniffer_version_ie') ?>',
                f: '<?= Settings::instance()->get('browser_sniffer_version_firefox') ?>',
                o: '<?= Settings::instance()->get('browser_sniffer_version_opera') ?>',
                c: '<?= Settings::instance()->get('browser_sniffer_version_chrome') ?>',
                s: '<?= Settings::instance()->get('browser_sniffer_version_safari') ?>'
            }
        </script>
        <script src="<?= URL::get_engine_assets_base(); ?>js/browserorg/main.js"></script>
        <link rel="stylesheet" href="<?= URL::get_engine_assets_base() ?>css/browserorg/style.css">
    <?php
        $page_data['assets_implemented']['browser_sniffer'] = true;
        View::bind_global('page_data', $page_data);
        endif; ?>

        <?php if (Auth::instance()->has_access('messaging_access_own_mail') || Auth::instance()->has_access('messaging_view')) : ?>
            <script src="<?= URL::get_engine_plugin_assets_base('messaging') ?>js/check_notifications.js"></script>
        <?php endif; ?>

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

		<?php
        if (!$is_backend) {
            echo  Settings::instance()->get('footer_html');
        }
        ?>
	</body>
</html>
<?php endif; ?>