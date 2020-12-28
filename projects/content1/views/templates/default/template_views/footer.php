<?php
$address1 = Settings::instance()->get('addres_line_1');
$address2 = Settings::instance()->get('addres_line_2');
$address3 = Settings::instance()->get('addres_line_3');
$telephone = Settings::instance()->get('telephone');
$fax = Settings::instance()->get('fax');
$mobile = Settings::instance()->get('mobile');
$email = Settings::instance()->get('email');
$newsletter = (Settings::instance()->get('newsletter_subscription_form') == 'FALSE') ? FALSE : TRUE;
$contact_us = ($address1.$address2.$address3 . $telephone . $fax . $mobile . $email != '');
?>
<div id="footer">
    <div id="company_signature" class="left">
        <?= Settings::instance()->get('company_signature'); ?>
    </div>
    <div id="footer_menu" class="left <?php if (!$newsletter) echo 'wide'; ?>">
        <?php menuhelper::add_menu_editable_heading('footer'); ?>
    </div>
    <?php if ($contact_us): ?>
        <div id="contact_details">
            <h3>Contact Us</h3>
			<?php if ($address1.$address2.$address3 != ""): ?>
				<address>
					<span class="line"><?=$address1; ?></span>
					<?=($address2 != '') ? '<span class="line">' . $address2 . '</span>' : ''; ?>
					<?=($address3 != '') ? '<span class="line">' . $address3 . '</span>' : ''; ?>
				</address>
			<?php endif; ?>
            <ul>
                <?php if ($telephone != ''): ?>
                    <li><span class="footer-contact-details-label">Phone:</span> <span class="footer-contact-details-value"><?= $telephone ?></span></li>
                <?php endif; ?>
                <?php if ($fax != ''): ?>
                    <li><span class="footer-contact-details-label">Fax:</span> <span class="footer-contact-details-value"><?= $fax ?></span></li>
                <?php endif; ?>
                <?php if ($mobile != ''): ?>
                    <li><span class="footer-contact-details-label">Mobile:</span> <span class="footer-contact-details-value"><?= $mobile ?></span></li>
                <?php endif; ?>
                <?php if ($email != ''): ?>
                    <li><span class="footer-contact-details-label">Email:</span> <span class="footer-contact-details-value"><?= $email ?></span></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
    if ($newsletter)
	{
        include 'form_newsletter_signup.php';
    }
    ?>
	<?php include 'social_media_group.php' ?>
	<?php if (file_exists(DOCROOT.'/assets/'.$assets_folder_path.'/images/footer_logo.png')): ?>
		<div id="footer_logo">
			<img src="<?= URL::get_skin_urlpath(TRUE) ?>images/footer_logo.png" style="float: right;" alt="<?= Settings::instance()->get('company_title') ?>" height="70" />
		</div>
	<?php endif; ?>
    <div id="ib_footer">
        <span class="left"><?= Settings::instance()->get('company_copyright'); ?></span>
        <span class="right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></span>
    </div>
</div>
<div id="clear_footer">
</div>