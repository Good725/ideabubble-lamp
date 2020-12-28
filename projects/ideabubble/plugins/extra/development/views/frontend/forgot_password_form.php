<h2>Reset Password</h2>
<form id="forgot_password_form" method="post" action="/admin/login/send_reset_email/" class="service-form">
        <label class="form-label" for="login-email">Email</label>
        <input type="hidden" name="redirect" value="<?=URL::site();?>reset-password-sent.html"/>
        <input type="text" id="login-email" class="validate[required]" name="email" placeholder="Enter your email address" value="<?= (isset($email)) ? HTML::chars($email) : ''; ?>" autofocus="autofocus" required="required" />

        <?php if (Settings::instance()->get('captcha_enabled')): ?>
            <p><label for="recaptcha_response_field">Please enter the captcha as below:</label></p>
            <script type="text/javascript">
                var RecaptchaOptions = { theme: 'clean' };
            </script>
            <?php
                require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                $captcha_public_key = Settings::instance()->get('captcha_public_key');
                echo recaptcha_get_html($captcha_public_key,NULL,TRUE);
            ?>
        <?php endif; ?>

        <button type="submit" id="reset_password_button">Confirm</button>
</form>