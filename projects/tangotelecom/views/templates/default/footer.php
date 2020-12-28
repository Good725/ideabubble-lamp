<div class="footer_wrapper">
    <div class="footer_in">
        <div class="footer_column footer_social">
            <?php include 'template_views/social_media.php' ?>
        </div>
        <div class="footer_column footer_contact">
            <h3>Contact Us</h3>
            <ul>
                <li>Tango Telecom Limited</li>
                <?php if ($settings->get('addres_line_1') != ''): ?>
                    <li><?= $settings->get('addres_line_1') ?></li>
                <?php endif; ?>
                <?php if ($settings->get('addres_line_2') != ''): ?>
                    <li><?= $settings->get('addres_line_2') ?></li>
                <?php endif; ?>
                <?php if ($settings->get('addres_line_3') != ''): ?>
                    <li><?= $settings->get('addres_line_3') ?></li>
                <?php endif; ?>

            </ul>
            <?php if ($settings->get('telephone') != ''): ?>
                <p><span> T: </span> <?= $settings->get('telephone') ?></p>
            <?php endif; ?>
            <?php if ($settings->get('mobile') != ''): ?>
                <p><span> M: </span> <?= $settings->get('mobile') ?></p>
            <?php endif; ?>
            <?php if ($settings->get('fax') != ''): ?>
                <p><span> F: </span> <?= $settings->get('fax') ?></p>
            <?php endif; ?>
            <?php if ($settings->get('email') != ''): ?>
                <p><span> E: </span> <?= $settings->get('email') ?></p>
            <?php endif; ?>
            <?php if ($settings->get('company_copyright') != ''): ?>
                <small><?= $settings->get('company_copyright') ?></small>
            <?php endif; ?>
        </div>
        <div class="footer_column footer_links">
            <h3>Quick Links</h3>
            <?php menuhelper::add_menu_editable_heading('footer') ?>
        </div>
        <div class="footer_column footer_signup">
            <?php if ($settings->get('newsletter_subscription_form') != 'FALSE'): ?>
                <h3>Newsletter Signup</h3>
                <?php $form_identifier = 'newsletter_signup_' ?>
                <form id="form-newsletter" action="<?= URL::Site(); ?>frontend/formprocessor" method="post">
                    <input type="hidden" name="subject" value="Newsletter Signup Form"/>
                    <input type="hidden" name="business_name" value="<?= $settings->get('company_title'); ?>"/>
                    <input type="hidden" name="redirect" value="thank-you-newsletter.html"/>
                    <input type="hidden" name="trigger" value="add_to_list"/>
                    <input type="hidden" name="form_identifier" value="<?= $form_identifier ?>"/>

                    <label>
                        <input class="validate[required] textfield" id="<?= $form_identifier ?>form_name" type="text"
                               name="<?= $form_identifier ?>form_name" placeholder="Name"/>
                    </label>
                    <label>
                        <input class="validate[required,custom[email]] textfield"
                               id="<?= $form_identifier ?>form_email_address" type="text"
                               name="<?= $form_identifier ?>form_email_address" placeholder="Email"/>
                    </label>

                    <?php if (Settings::instance()->get('captcha_enabled') && Settings::instance()->get('newsletter_subscription_captcha')): ?>
                        <script src='https://www.google.com/recaptcha/api.js'></script>

                        <div class="captcha-section hidden" id="newsletter-captcha-section">
                            <input type="text" class="sr-only" id="form-newsletter-captcha-hidden" tabindex="-1" />

                            <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="button" id="submit-newsletter">Submit&raquo;</button>
                </form>
            <?php endif; ?>
            <a href="#">
                <img src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/footer_logo.png"
                     class="footer_logo" alt="">
            </a>

            <p><?= ($settings->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></p>
        </div>
    </div>
</div>