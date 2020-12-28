<div class="alert alert-error" id="email_error" style="display:none;">
This email address is already in use.
</div>
<form class="service-form" id="customer_registration_form" action="#" method="post">
    <div>
        <label class="form-label" for="registration_form_first_name">First Name</label>
        <input id="registration_form_first_name" class="validate[required]" type="text" name="contact_first_name" placeholder="Enter your first name" autofocus="autofocus" />
    </div>

    <div>
        <label class="form-label" for="registration_form_last_name">Last Name</label>
        <input id="registration_form_last_name" class="validate[required]" type="text" name="contact_last_name" placeholder="Enter your last name" />
    </div>

    <div>
        <label class="form-label" for="registration_form_company">Company</label>
        <input id="registration_form_company" type="text" name="company_title" placeholder="Enter your company name" />
    </div>


    <div>
        <label class="form-label" for="registration_form_phone">Phone Number</label>
        <input id="registration_form_phone" type="text" name="contact_phone" placeholder="Enter your phone number" />
    </div>

    <div>
        <label class="form-label" for="registration_form_email">Email</label>
        <input id="registration_form_email" class="validate[required,custom[email]]" type="text" name="contact_email" placeholder="Enter your email" />
    </div>

    <div>
        <label class="form-label" for="registration_form_password">Password</label>
        <input id="registration_form_password" class="validate[required,minSize[8]]" type="password" name="password" placeholder="Enter your password" />
    </div>

    <div>
        <label class="form-label" for="registration_form_password2">Retype Password</label>
        <input id="registration_form_password2" class="validate[required,equals[registration_form_password]]" type="password" name="password2" placeholder="Retype your password" />
    </div>

    <div>
        <label class="form-label" for="registration_form_package">Package</label>
        <select id="registration_form_package" class="validate[required]" required="required">
            <option value="">Please select</option>
            <option value="starter"<?= (isset($_GET['type']) && $_GET['type'] == 'starter') ? ' selected="selected"' : '' ?>>Starter</option>
            <option value="premium"<?= (isset($_GET['type']) && $_GET['type'] == 'premium') ? ' selected="selected"' : '' ?>>Premium</option>
            <option value="shop"<?=    (isset($_GET['type']) && $_GET['type'] == 'shop')    ? ' selected="selected"' : '' ?>>Shop</option>
        </select>
    </div>

    <?php
    $captcha_enabled = Settings::instance()->get('captcha_enabled');
    if ($captcha_enabled)
    {
        require_once ENGINEPATH.'/plugins/formprocessor/development/classes/model/recaptchalib.php';
        $captcha_public_key = Settings::instance()->get('captcha_public_key');
        echo recaptcha_get_html($captcha_public_key);
        echo recaptcha_get_html($captcha_public_key,NULL,TRUE);
    }
    ?>

    <button id="registration_form_submit" type="submit">Create Account</button>

    <? // Temporary ?>
    <input type="hidden" name="id" value="new" />
    <input type="hidden" name="contact" value="" />
    <input type="hidden" name="billing_first_name" value="" />
    <input type="hidden" name="billing_last_name" value="" />
    <input type="hidden" name="billing_email" value="" />
    <input type="hidden" name="billing_phone" value="" />
</form>
<script type="text/javascript" src="<?= URL::get_project_plugin_assets_base('extra').'js/frontend/general.js'?>"></script>