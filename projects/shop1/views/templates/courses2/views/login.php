<div class="container login-form-container">
	<div class="modal show">
		<div class="modal-dialog">
			<div class="modal-content login">
				<form name="login_form" method="post" action="/admin/login/" class="form-horizontal">
					<div class="modal-header clearfix">
						<img class="client-logo left" src="<?= URL::overload_asset('images/logo.png') ?>" alt=""/>
						<div class="left">
							<h1><?= Settings::instance()->get('company_name') ?></h1>
							<p><?= Settings::instance()->get('company_slogan') ?></p>
						</div>
					</div>
					<div class="modal-body">

						<?php //This is needed to display any error that might be loaded into the messages queue ?>
						<?= (isset($alert)) ? $alert : '' ?>
						<fieldset>
							<div class="form-group">
								<label class="sr-only" for="login-email"><?=__('Email')?></label>

								<div class="col-sm-12">
									<input type="text" class="form-control input-lg" id="login-email" name="email" autofocus="autofocus" placeholder="Email" value="<?php echo (isset($data['email'])) ? HTML::chars($data['email']) : ''; ?>" required/>
								</div>
							</div>

							<div class="form-group">
								<label class="sr-only" for="login-password"><?=__('Password')?></label>

								<div class="col-sm-12">
									<input required type="password" class="form-control input-lg" id="login-password" name="password" placeholder="<?=__('Password')?>" autocomplete="off" />
								</div>
							</div>

							<div class="form-group login-buttons">
								<div>
									<input type="submit" class="form-control btn btn-lg btn-primary" id="login_button" name="login" value="<?=__('Log in')?>"/>
								</div>

								<div>
									<label style="margin: 0;">
										<input type="hidden" name="remember" value="dont-remember"/><!-- Default value for checkbox -->
										<input id="optionsCheckbox" type="checkbox" name="remember" value="remember"<?= (isset($remember) AND $remember === FALSE) ? '' : ' checked'; ?> />
										Keep me signed in for <?= Settings::instance()->get('login_lifetime') ?>.
									</label>
								</div>
							</div>

							<?php if (i18n::is_multi_language()) : ?>
								<div class="form-group">
									<label class="sr-only" for="login_language"><?=__('Select language')?></label>

									<div class="col-sm-12">
										<select class="form-control" id="login_language" name="lang" onChange="document.login_form.submit()">
											<?= i18n::get_allowed_languages_as_options(@$data['lang']);?>
										</select>
									</div>
								</div>
							<?php endif; ?>

							<div class="col-sm-12 text-center">
								<p><a href="/admin/login/forgot_password/" id="passwordlink"><span><?=__('Forgot your password?')?></span></a></p>
							</div>

							<input type="hidden" name="redirect" value="<?= (isset($redirect)) ? $redirect : '' ?>" />

						</fieldset>

					</div>
					<div class="modal-footer">
						<div class="poweredby">
							<p><?= Settings::instance()->get('cms_copyright') ?></p>
						</div>
					</div>
				</form>

			</div>

		</div>

	</div>

</div>

<div class="modal fade" tabindex="-1" role="dialog" id="auto-logout-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= __('Inactive for too long') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= __('For your protection, we have logged you out as you have been inactive. You need to login again to continue') ?></p>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal"><?= __('Login') ?></button>
			</div>
		</div>
	</div>
</div>