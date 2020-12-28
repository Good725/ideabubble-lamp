<h2>Reset Password</h2>
<form id="forgot_password_form" method="post" action="/admin/login/reset/" class="service-form">
    <input type="hidden" id="reset_password_id" name="validation" value="<?= isset($_GET['validate']) ? $_GET['validate'] : '';?>"/>
    <input type="hidden" name="redirect" value="customer-login.html"/>
    <?php if(!isset($info)): ?>
        <fieldset>
            <div>
                <label for="reset_password_password" class="form-label">New Password</label>
                <input id="reset_password_password" type="password" name="password"/>
            </div>
            <div>
                <label for="reset_password_mpassword" class="form-label">Confirm New Password</label>
                <input id="reset_password_mpassword" type="password" name="mpassword"/>
            </div>

            <button type="submit" id="reset_password_button">Confirm</button>
        </fieldset>
    <?php else: ?>
        <p>A confirmation link has been sent to your email address.</p>
    <?php endif; ?>
</form>