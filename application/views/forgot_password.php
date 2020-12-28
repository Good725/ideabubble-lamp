<div class="login-form-container">
	<div class="modal show">
		<div class="modal-dialog">
			<div class="modal-content login">
				<form id="forgot_password_form" method="post" action="/admin/login/send_reset_email/" class="form-horizontal validate-on-submit">
					<div class="modal-header">
						<a href="/">
                            <img class="client-logo" src="<?= Ibhelpers::get_login_logo() ?>" alt=""/>
                        </a>
                        <?php $mobile_breadcrumbs = MenuArea::factory()->generate_mobile_breadcrumb_links(
                                array(
                                    array('link' => '/admin/login', 'name' => 'Login'),
                                    array('link' => '#', 'name' => 'Forgot Password')
                                ));?>
                        <?= View::factory('mobile_breadcrumb')->set('mobile_breadcrumbs', $mobile_breadcrumbs); ?>
					</div>

					<div class="modal-body">
						<?= (isset($alert)) ? $alert : '' ?>

                        <h3 class="hidden-xs"><?= __('Forgot password') ?></h3>

						<fieldset>
							<div class="form-group">
								<p class="col-sm-12">
                                    <label for="login-email" style="font-size: 16px; font-weight: 300;"><?= __('No worries... Just give us your e-mail and we\'ll send you a link') ?></label>
                                </p>
							</div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php
                                    $value = (isset($email)) ? HTML::chars($email) : '';
                                    $attributes = array(
                                        'id'          => 'email',
                                        'autofocus'   => 'autofocus',
                                        'class'       => 'validate[required,custom[email]]'
                                    );
                                    echo Form::ib_input(__('E-mail'), 'email', $value, $attributes);
                                    ?>
                                </div>
                            </div>

							<script>
								var RecaptchaOptions = {
									theme: 'clean'
								};
							</script>
							<?php
							$captcha_enabled = Settings::instance()->get('cms_captcha_enabled');
							if ($captcha_enabled)
							{
								require_once ENGINEPATH.'/plugins/formprocessor/development/classes/model/recaptchalib.php';
								$captcha_public_key = Settings::instance()->get('captcha_public_key');
							}
							?>
							<?php if ($captcha_enabled): ?>
								<div>
									<p>Please enter the captcha as below:</p>
									<?= recaptcha_get_html($captcha_public_key,NULL,TRUE) ?>
								</div>
							<?php endif; ?>
						</fieldset>

						<div class="text-center my-4">
							<?php if ( Model_Plugin::is_loaded('userreq')) : ?>
								<a class="left btn btn-lg" href="<?= URL::Site('/admin/login/user_req/'); ?>"><?=__('Register new user')?></a>
							<?php endif ?>

							<button type="submit" id="reset_password_button" class="btn btn-success btn-lg btn--full"><?=__('Send E-mail')?></button>
						</div>

                        <div class="login-return hidden-xs">
                            <p class="text-center"><a href="/admin/login"><?= __('Return to the log-in page') ?></a></p>
                        </div>
					</div>

                    <?php if (false): // future of this section is uncertain ?>
                        <div class="modal-footer">
                            <div class="text-center">
                                <div class="poweredby">
                                    <p><?= Settings::instance()->get('cms_copyright') ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
				</form>

			</div>
		</div>
	</div>
</div>

