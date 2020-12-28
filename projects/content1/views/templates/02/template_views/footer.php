<?php
$address1 = Settings::instance()->get('addres_line_1');
$address2 = Settings::instance()->get('addres_line_2');
$address3 = Settings::instance()->get('addres_line_3');
$telephone = Settings::instance()->get('telephone');
$fax = Settings::instance()->get('fax');
$mobile = Settings::instance()->get('mobile');
$email = Settings::instance()->get('email');
$newsletter = (Settings::instance()->get('newsletter_subscription_form') == 'FALSE') ? FALSE : TRUE;
if ($address1 . $telephone . $fax . $mobile . $email == '') {
    $contact_us = FALSE;
} else {
    $contact_us = TRUE;
}
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
            <?php if ($address1 != ""): ?>
                <address>
                    <span class="line"><?=$address1; ?></span>
                    <?=($address2 != '') ? '<span class="line">' . $address2 . '</span>' : ''; ?>
                    <?=($address3 != '') ? '<span class="line">' . $address3 . '</span>' : ''; ?>
                </address>
            <?php endif; ?>
            <ul>
                <?php if ($telephone != ''): ?>
                    <li>Phone: <?= $telephone ?></li>
                <?php endif; ?>
                <?php if ($fax != ''): ?>
                    <li>Fax: <?= $fax ?></li>
                <?php endif; ?>
                <?php if ($mobile != ''): ?>
                    <li>Mobile: <?= $mobile ?></li>
                <?php endif; ?>
                <?php if ($email != ''): ?>
                    <li>Email: <?= $email ?></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
    if ($newsletter) {
        include 'form_newsletter_signup.php';
    }
    ?>
    <div id="footer_logo">
        <img src="<?= URL::get_skin_urlpath(TRUE) ?>images/footer_logo.png" style="float: right;"
             alt="<?= Settings::instance()->get('company_title') ?>" height="70" />
    </div>
    <div id="ib_footer">
        <span class="left"><?= Settings::instance()->get('company_copyright'); ?></span>
        <span class="right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></span>
    </div>
</div>
<div id="clear_footer">
</div>