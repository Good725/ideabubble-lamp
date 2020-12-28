<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?= __('Register') ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" href="<?= URL::overload_asset('css/cms.compiled.css') ?>" />
        <link rel="stylesheet" href="<?= URL::overload_asset('css/form.css', ['cachebust' => true]) ?>" />
        <link rel="stylesheet" href="<?= URL::overload_asset('css/stylish.css', ['cachebust' => true]) ?>" />
		<?php if (Settings::instance()->get('cms_skin') != ''): ?>
			<link rel="stylesheet" href="<?= URL::get_engine_theme_assets_base().'css/styles.css' ?>" />
		<?php endif; ?>
		<link rel="stylesheet" href="<?= URL::overload_asset('css/project.css') ?>" />

		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>

		<script src="<?= URL::get_engine_assets_base(); ?>js/bootstrap-3.3.5.min.js"></script>
        <script src="<?= URL::get_engine_assets_base(); ?>js/forms.js"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<style>
			.form-section {
				display: none;
			}
			.form-section.active {
				display: block;
			}
			.start-selling-button {
				font-size: 20px;
			}
		</style>
        <?php
        if (Settings::instance()->get('google_analytics_backend_tracking')) {
            echo Settings::get_google_analytics_script();
        }
        ?>
	</head>
	<body class="layout-login">
		<div class="container login-form-container">
			<div class="modal show">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<a href="/"><img class="client-logo" src="<?= Ibhelpers::get_login_logo() ?>" alt="" /></a>
						</div>

						<div class="modal-body form-horizontal">
							<section class="form-section active">
								<form method="post">
									<input type="hidden" name="validate" value="<?=html::chars(@$_REQUEST['validate'])?>" />
									<input type="hidden" name="invite_member" value="<?=html::chars(@$_REQUEST['invite_member'])?>" />
									<input type="hidden" name="invite_hash" value="<?=html::chars(@$_REQUEST['invite_hash'])?>" />
									<?= $alert ? $alert : ''?>
									<div class="form-group">
										<div class="col-sm-12 text-center event-registration-login_text">
											<p><?=__('Already have an account?')?><br /><a href="/admin/login?redirect=/admin/events/edit_event"><?=__('Log in?')?></a> </p>
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-12">
											<div class="input-group login-input-group">
												<label class="input-group-addon" for="event-registration-email">
													<span class="sr-only"><?=__('Email')?></span>
													<span class="flaticon-envelope"></span>
												</label>
												<input type="email" class="form-control input-lg" id="event-registration-email" name="email" value="<?= isset($_REQUEST['email']) ? html::chars($_REQUEST['email']) : '' ?>" placeholder="<?= __('Email') ?>" required="required" />
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-12">
											<div class="input-group login-input-group">
												<label class="input-group-addon" for="event-registration-password">
													<span class="sr-only"><?=__('Password')?>"</span>
													<span class="flaticon-lock"></span>
												</label>
												<input type="password" class="form-control input-lg" id="event-registration-password" name="password" value="<?= isset($_POST['password']) ? html::chars($_POST['password']) : '' ?>"" placeholder="<?= __('Password') ?>" required="required" />
											</div>
										</div>
									</div>

									<?php if(Model_Plugin::is_enabled_for_role('Administrator', 'dcs')) { ?>
									<div class="form-group">
										<div class="col-sm-12">
											<div class="input-group login-input-group">
												<label class="input-group-addon" for="event-dcs-family_id">
													<span class="sr-only"><?=__('DCS Family Id')?>"</span>
													<span class="flaticon-envelope"></span>
												</label>
												<input type="text" class="form-control input-lg" id="event-dcs-family_id" name="dcs_family_id" value="<?= isset($_REQUEST['dcs_family_id']) ? html::chars($_REQUEST['dcs_family_id']) : '' ?>" placeholder="<?= __('DCS Family Id') ?>" />
											</div>
										</div>
									</div>
									<?php } ?>

									<div class="form-group">
										<div class="col-sm-12 text-center">
											<div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-12 text-center">
                                            <label style="margin: 0;">
                                                <label class="checkbox-styled">
                                                    <input type="checkbox" name="signup_newsletter" value="1" checked/>
                                                    <span class="checkbox-icon"></span>
                                                </label>
                                                I would like to sign up to newsletter
                                            </label>
										</div>
									</div>

									<div class="form-group login-buttons">
										<div class="col-sm-12">
											<button type="submit" class="btn btn-primary btn-lg continue-button" name="action" value="register"><?= __('Sign up') ?></button>
										</div>
									</div>
								</form>
							</section>
						</div>

                        <div class="modal-footer">
                            <div class="poweredby text-center">
                                <p><?= Settings::instance()->get('cms_copyright') ?></p>
                            </div>
                        </div>

					</div>
				</div>
			</div>
		</div>
	</body>
</html>