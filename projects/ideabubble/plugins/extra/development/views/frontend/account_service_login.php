<?php echo IbHelpers::get_messages(); ?>
<h2>Login</h2>
<?php if (Auth::instance()->logged_in()): ?>
    <p>You are already logged in</p>
<?php else: ?>
    <form class="service-form" action="frontend/extra/login" method="post">
        <div>
            <label class="form-label" for="login_email">User Name</label>
            <input id="login_email" class="validate[required]" type="text" name="email" placeholder="Enter your email" autofocus="autofocus" />
        </div>

        <div>
            <label class="form-label" for="login_password">Password</label>
            <input id="login_password" class="validate[required]" type="password" name="password" placeholder="Enter your password" />
        </div>

        <div class="action_buttons">
            <a href="/reset-password.html" id="forgot_password_link" class="forgot_password_link">Forgot your Password?</a>

            <button id="login_submit" type="submit">Login</button>
            <a href="/customer-registration.html" id="register_account"><button type="button">Register</button></a>
        </div>

    </form>
<?php endif; ?>
