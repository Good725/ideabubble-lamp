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

if (Settings::instance()->get('facebook_url').Settings::instance()->get('twitter_url').Settings::instance()->get('linkedin_url').
    Settings::instance()->get('flickr_url').Settings::instance()->get('pinterest_button') != '')
{
    $social_media = TRUE;
}
else
{
    $social_media = FALSE;
}

?>

<div class="footer_body">
    <div class="contact_footer">
        <h4 class="contact_us">Contact Us</h4>
        <address>
            <?= (($address_line_1 != '') ? '<span class="line">'.$address_line_1.'</span>' : '') ?>
            <?= (($address_line_2 != '') ? '<span class="line">'.$address_line_2.'</span>' : '') ?>
            <?= (($address_line_3 != '') ? '<span class="line">'.$address_line_3.'</span>' : '') ?>
            <?= (($address_line_4 != '') ? '<span class="line">'.$address_line_4.'</span>' : '') ?>
        </address>
        <div>
            <?php if (settings::instance()->get('telephone')  != ''): ?>
                <p><span class="label">Phone</span> <?= settings::instance()->get('telephone') ?></p>
            <?php endif; ?>
            <?php if (settings::instance()->get('mobile')  != ''): ?>
                <p><span class="label">Mobile</span> <?= settings::instance()->get('mobile') ?></p>
            <?php endif; ?>
            <?php if (settings::instance()->get('fax')  != ''): ?>
                <p><span class="label">Fax</span> <?= settings::instance()->get('fax') ?></p>
            <?php endif; ?>
            <?php if (settings::instance()->get('email')  != ''): ?>
                <p><span class="label">Email</span> <?= settings::instance()->get('email') ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="connect_with_us">
        <h4 class="quick_links_header">Quick Links</h4>
        <div class="menu_footer">
            <?php menuhelper::add_menu_editable_heading('footer')?>
        </div>
        <?php if ($social_media): ?>
            <div class="connect_with_us_wrapper">
                <h5>Connect With Us</h5>
                <div>
                    <?php if (Settings::instance()->get('facebook_url') != '') : ?>
                        <span><a target="_blank" href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url'); ?>"><img src="/assets/<?= $assets_folder_path ?>/images/facebook_icon.png" /></a></span>
                    <?php endif; ?>
                    <?php if (Settings::instance()->get('twitter_url') != '') : ?>
                        <span><a target="_blank" href="https://twitter.com/<?= Settings::instance()->get('twitter_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/twitter_icon.png" /></a></span>
                    <?php endif; ?>
                    <?php if (Settings::instance()->get('linkedin_url') != '') : ?>
                        <span><a target="_blank" href="https://linkedin.com/<?= Settings::instance()->get('linkedin_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/linkedin_icon.png" /></a></span>
                    <?php endif; ?>
                    <?php if (Settings::instance()->get('flickr_url') != '') : ?>
                        <span><a target="_blank" href="https://www.flickr.com/photos/<?= Settings::instance()->get('flickr_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/flickr_icon.png" /></a></span>
                    <?php endif; ?>
                    <?= Settings::instance()->get('pinterest_button'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if (Settings::instance()->get('newsletter_subscription_form') != 'FALSE'): ?>
        <div id="newsletter_wrapper">
            <h4 id="newsletter_header">Sign up to our newsletter</h4>
            <?php $form_identifier = 'newsletter_signup_' ?>
            <form id="form-newsletter" action="<?= URL::Site(); ?>frontend/formprocessor" method="post">
                <input type="hidden" name="subject"         value="Newsletter Signup Form" />
                <input type="hidden" name="business_name"   value="<?= Settings::instance()->get('company_title'); ?>" />
                <input type="hidden" name="redirect"        value="thank-you-newsletter.html" />
                <input type="hidden" name="trigger"         value="add_to_list" />
                <input type="hidden" name="form_identifier" value="<?= $form_identifier ?>" />
                <div class="row">
                    <div class="label"><label for="<?= $form_identifier ?>form_name">Name</label></div>
                    <input type="text" name="<?=$form_identifier;?>form_name" id="<?= $form_identifier ?>form_name" class="validate[required]"/>
                </div>
                <div class="row">
                    <div class="label"><label for="<?= $form_identifier ?>form_email_address">Email</label></div>
                    <input type="text" name="<?=$form_identifier;?>form_email_address" id="<?= $form_identifier ?>form_email_address" class="validate[required,custom[email]]" />
                </div>
                <input type="submit" name="submit-newsletter" value="Submit" id="submit-newsletter" class="submit" />
            </form>
        </div>
    <?php endif; ?>
	<div class="footer_bottom_wrapper">
		<?php menuhelper::add_menu_editable_heading('footer_bottom') ?>
	</div>
    <?php if (file_exists(DOCROOT.'/assets/'.$assets_folder_path.'/images/footer_logo.png')): ?>
        <div id="footer_logo"><img src="/assets/<?= $assets_folder_path ?>/images/footer_logo.png" alt="" /></div>
    <?php endif; ?>
</div>
<div class="footer_copyright">
    <div class="footer_copyright_left"><?= Settings::instance()->get('company_copyright') ?></div>
    <div class="footer_copyright_right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></div>
</div>
