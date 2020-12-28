<!-- Newsletter Signup Form -->
<div id="newsletter_wrapper" class="right">
    <div id="newsletter_header">
        Newsletter Signup<br/><br/>
    </div>
    <?php $form_identifier = 'newsletter_signup_' ?>
    <form method="post" id="form-newsletter--" action="<?= URL::Site(); ?>frontend/formprocessor">
        <input type="hidden" value="Newsletter Signup Form" name="subject" />
        <input type="hidden" value="<?= Settings::instance()->get('company_title'); ?>" name="business_name" />
        <input type="hidden" value="thank-you-newsletter.html" name="redirect" />
        <input type="hidden" value="add_to_list" name="trigger" />
        <input type="hidden" value="<?= $form_identifier ?>" name="form_identifier" />
        <input type="text" name="<?= $form_identifier ?>form_name" id="<?= $form_identifier ?>form_name" class="validate[required]" placeholder="Name"/><br/><br/>
        <input type="text" name="<?= $form_identifier ?>form_email_address" id="<?= $form_identifier ?>form_email_address" class="validate[required,custom[email]]" placeholder="Email"/><br/><br/>
        <input type="submit" name="submit-newsletter" id="submit-newsletter" class="submit right" value="Subscribe"/>
    </form>
</div>
<!-- /Newsletter Signup Form -->
