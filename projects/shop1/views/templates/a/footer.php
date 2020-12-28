<?php
if (settings::instance()->get('address_line_1') != '') {
    $address_line_1 = settings::instance()->get('address_line_1');
    $address_line_2 = settings::instance()->get('address_line_2');
    $address_line_3 = settings::instance()->get('address_line_3');
    $address_line_4 = settings::instance()->get('address_line_4');
} else {
    $address_line_1 = settings::instance()->get('addres_line_1');
    $address_line_2 = settings::instance()->get('addres_line_2');
    $address_line_3 = settings::instance()->get('addres_line_3');
    $address_line_4 = settings::instance()->get('addres_line_4');
}

?>

<div class="footer_body">

    <?php if (Settings::instance()->get('row_bottom') == TRUE AND Settings::instance()->get('row_bottom') == 1): ?>
        <div class="contact_footer">
            <h1 class="contact_us">Contact Us</h1>

            <p><?= $address_line_1; ?></p>
            <p><?= $address_line_2; ?></p>
            <p><?= $address_line_3; ?></p>
            <p><?= $address_line_4; ?></p>
            <div class="white">
                <p>T: <?php echo @settings::instance()->get('telephone') ?></p>
                <?php if (Settings::instance()->get('fax') != '') : ?>
                    <p>F: <?php echo @settings::instance()->get('fax') ?></p>
                <?php endif; ?>
                <p>E: <?php echo @settings::instance()->get('email') ?></p>
            </div>
            <?php if($assets_folder_path == 24){ ?>
                <div class="footer_social" id="left-social-footer">
                    <span>FIND US ON</span>
                    <?php if (Settings::instance()->get('facebook_url') != '') : ?>
                        <span><a style="border:0;" href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url'); ?>"><img src="/assets/<?= $assets_folder_path ?>/images/icons/facebook_icon.png"/></a></span>
                    <?php endif; ?>
                    <?php if (Settings::instance()->get('twitter_url') != '') : ?>
                        <span><a style="border:0;" href="https://twitter.com/<?= Settings::instance()->get('twitter_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/icons/twitter_icon.png"/></a></span>
                    <?php endif; ?>
					<?php if (isset($tripadvisor_url) AND $tripadvisor_url != ''): ?>
						<a href="<?= $tripadvisor_url ?>" title="Tripadvisor"><img src="/assets/<?= $assets_folder_path ?>/images/icons/3_icon.png" alt="Tripadvisor"></a>
					<?php endif; ?>
                </div>
                <img class="footer_custom_icons_1" src="<?= URL::site().'assets/'.$assets_folder_path.'/images/icons/footer_custom_icons_1.png'; ?>" alt="">
            <?php } ?>
            <?php if($assets_folder_path == 25){ ?>
            <div class="footer_social">
                <?php if (Settings::instance()->get('facebook_url') != '') : ?>
                    <a href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url'); ?>"><img src="<?= URL::site().'assets/'.$assets_folder_path.'/images/icons/facebook_icon.png'; ?>" alt=""></a>
                <?php endif; ?>
            </div>
            <?php } ?>
        </div>

        <?php if (Kohana::$config->load('config')->get('db_id') == 'garretts') : ?>
            <div class="contact_footer">
                <h1>&nbsp;</h1>

                <p>Unit 3,</p>
                <p>Castletroy Park</p>
                <p>Commercial Centre</p>
                <p>Castletroy</p>
                <div class="white">
                    <p>T: 061 216127</p>
                    <p>F: 061 216117</p>
                    <p>E: order@garretts.ie</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="connect_with_us">
            <h1>Useful Links</h1>

            <div class="menu_footer">
                <?php menuhelper::add_menu_editable_heading('footer') ?>
            </div>
            <?php if (Settings::instance()->get('facebook_url') . Settings::instance()->get('twitter_url') . Settings::instance()->get('pinterest_button') != '' && $assets_folder_path != 24 && $assets_folder_path != 25)  : ?>
                <div class="connect_with_us_wrapper">
                    <h1>Connect With Us</h1>

                    <div>
                        <?php if (Settings::instance()->get('facebook_url') != '') : ?>
                            <span><a style="border:0;" href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url'); ?>"><img src="/assets/<?= $assets_folder_path ?>/images/facebook_icon.png"/></a></span>
                        <?php endif; ?>
                        <?php if (Settings::instance()->get('twitter_url') != '') : ?>
                            <span><a style="border:0;" href="https://twitter.com/<?= Settings::instance()->get('twitter_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/twitter_icon.png"/></a></span>
                        <?php endif; ?>
                        <?= Settings::instance()->get('pinterest_button'); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="sign_up">
            <?php if (Settings::instance()->get('newsletter_subscription_form') != 'FALSE'): ?>
                <div class="newsletter-signup-wrapper">
                    <h1>Sign up to our newsletter</h1>
                    <?php $form_identifier = 'newsletter_signup_' ?>
                    <form class="form-newsletter" id="form-newsletter" action="/frontend/formprocessor" method="post">
                        <input type="hidden" name="subject"         value="Newsletter Signup Form" />
                        <input type="hidden" name="business_name"   value="<?= Settings::instance()->get('company_title'); ?>" />
                        <input type="hidden" name="redirect"        value="thank-you-newsletter.html" />
                        <input type="hidden" name="trigger"         value="add_to_list" />
                        <input type="hidden" name="form_identifier" value="<?= $form_identifier ?>" />

                        <ul>
                            <li>
                                <input name="contact_form_name" id="contact_form_name" class="validate[required]" type="text" placeholder="Name"/>
                            </li>
                            <li>
                                <input name="contact_form_email_address" id="contact_form_email_address" class="validate[required,custom[email]]" type="text" placeholder="Email"/>
                            </li>
                            <li>
                                <?php if (Settings::instance()->get('captcha_enabled') && Settings::instance()->get('newsletter_subscription_captcha')): ?>
                                    <script src='https://www.google.com/recaptcha/api.js'></script>

                                    <div class="captcha-section hidden" id="newsletter-captcha-section" style="display: none;">
                                        <input type="text" class="captcha-section-hidden_field validate[required]" id="form-newsletter-captcha-hidden" tabindex="-1" style="position: absolute; z-index: -1;"/>

                                        <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key'); ?>" data-size="compact"></div>
                                    </div>

                                    <script>
                                        $("#form-newsletter").on('submit', function()
                                        {
                                            var $captcha_section = $('#newsletter-captcha-section');

                                            // If the CAPTCHA has been filled out, fill out the field checked by the validation
                                            if (grecaptcha && grecaptcha.getResponse().length !== 0) {
                                                $('#form-newsletter-captcha-hidden').val(1);
                                            }

                                            if ($captcha_section.length && $captcha_section.hasClass('hidden')) {
                                                // CAPTCHA exists but isn't visible
                                                $captcha_section.removeClass('hidden').show();
                                                return false;
                                            }
                                            else if ( ! $("#form-newsletter").validationEngine('validate')) {
                                                // Form fields failed validation
                                                return false;
                                            }
                                            else {
                                                // Form is valid
                                                $('#form-newsletter').submit();
                                            }
                                        });
                                    </script>

                                <?php endif; ?>

                                <input name="submit-newsletter" id="submit-newsletter" type="submit" class="right" value="Submit"/>
                            </li>
                        </ul>
                    </form>
                </div>
            <?php endif; ?>
            <?php if (file_exists(DOCROOT . '/assets/' . $assets_folder_path . '/images/footer_logo.png')): ?>
                <div id="footer_logo"><img src="/assets/<?= $assets_folder_path ?>/images/footer_logo.png" alt=""/>
                </div>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    <?php endif; ?>
</div>
<div class="clear"></div>
<div class="footer_copyright">
    <div class="footer_copyright_left"><?= Settings::instance()->get('company_copyright') ?></div>
    <div class="footer_copyright_right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></div>
    <div class="clear"></div>
</div>
