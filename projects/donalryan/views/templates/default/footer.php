<?php
$social_media = (Settings::instance()->get('facebook_url').Settings::instance()->get('twitter_url').Settings::instance()->get('linkedin_url').Settings::instance()->get('flickr_url').Settings::instance()->get('pinterest_button') != '');
?>

<div class="footer_body">
    <div class="footer_links">
        <div class="quick_links_wrapper">
            <h3>Quick Links</h3>
            <?php menuhelper::add_menu_editable_heading('footer') ?>
        </div>
        <div class="twitter_feed_wrapper">
            <a class="twitter-timeline"
               width="300"
               height="250"
               href="https://twitter.com/NissanTipperary" data-widget-id="674632892874661888">Tweets by @NissanTipperary</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>
    </div>
    <div class="footer_contact">
        <div class="footer_contact_list">
            <h3>Contact Us</h3>
            <ul>
                <li>
                    <h4>Nenagh</h4>
                    <ul>
                        <li>Limerick Road, Nenagh, Co. Tipperary</li>
                        <li>Tel: 067 43000</li>
                        <li>Email: sales@donalryan.ie</li>
                        <li>Mon-Fri 9.00am-6.00pm</li>
                        <li>Sat 10.00am-4.00pm</li>
                        <li>Sun Closed</li>
                        <li>Or later by appointment</li>
                    </ul>
                </li>
                <li>
                    <h4>Thurles</h4>
                    <ul>
						<li>Killinan, Thurles, Co Tipperary</li>
                        <li>Tel: 0504 21400</li>
                        <li>Email: michael@donalryan.ie</li>
                        <li>Mon-Fri 9.00am-5.30pm</li>
                        <li>Sat 10.00am-3.00pm</li>
                        <li>Or later by appointment</li>
                    </ul>
                </li>
                <li>
                    <h4>Roscrea</h4>
                    <ul>
						<li>Old Dublin Road, Roscrea, Co Tipperary</li>
                        <li>Tel: 0505 22335</li>
                        <li>Email: kevin@donalryan.ie</li>
                        <li>Mon-Fri 9.00am-5.30pm</li>
                        <li>Sat 10.00am-3.00pm</li>
                        <li>To see the location of our<br />Showrooms, <a href="#">click here</a>.</li>
                    </ul>
                </li>
            </ul>
        </div>

        <?php if (Settings::instance()->get('newsletter_subscription_form') != 'FALSE'): ?>
            <div id="newsletter_wrapper" class="newsletter_wrapper">
                <h3>Join Our newsletter</h3>
                <?php $form_identifier = 'newsletter_signup_' ?>
                <form id="form-newsletter" action="<?= URL::Site(); ?>frontend/formprocessor" method="post">
                    <input type="hidden" name="subject"         value="Newsletter Signup Form" />
                    <input type="hidden" name="business_name"   value="<?= Settings::instance()->get('company_title'); ?>" />
                    <input type="hidden" name="redirect"        value="thank-you-newsletter.html" />
                    <input type="hidden" name="trigger"         value="add_to_list" />
                    <input type="hidden" name="form_identifier" value="<?= $form_identifier ?>" />
                    <div class="row">
                        <div class="label"><label for="<?= $form_identifier ?>form_name">Name</label></div>
                        <input type="text" name="<?=$form_identifier;?>form_name" id="<?= $form_identifier ?>form_name" class="validate[required]" placeholder="your name..."/>
                    </div>
                    <div class="row">
                        <div class="label"><label for="<?= $form_identifier ?>form_email_address">Email</label></div>
                        <input type="text" name="<?=$form_identifier;?>form_email_address" id="<?= $form_identifier ?>form_email_address" class="validate[required,custom[email]]" placeholder="your email address..." />
                    </div>

                    <?php if (Settings::instance()->get('captcha_enabled') && Settings::instance()->get('newsletter_subscription_captcha')): ?>
                        <script src='https://www.google.com/recaptcha/api.js'></script>

                        <div class="captcha-section hidden" id="newsletter-captcha-section">
                            <div class="row">
                                <input type="text" class="sr-only" id="form-newsletter-captcha-hidden" tabindex="-1" />

                                <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button type="submit" name="submit-newsletter" id="submit-newsletter" class="primary_button submit">Sign Up</button>
                </form>
            </div>
        <?php endif; ?>

    </div>


    <?php if (file_exists(DOCROOT.'/assets/'.$assets_folder_path.'/images/footer_logo.png')): ?>
        <div id="footer_logo"><img src="/assets/<?= $assets_folder_path ?>/images/footer_logo.png" alt="" /></div>
    <?php endif; ?>

    <div class="footer_copyright">
        <div class="footer_copyright_left"><?= Settings::instance()->get('company_copyright') ?></div>
        <div class="footer_copyright_right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></div>
    </div>


</div>
