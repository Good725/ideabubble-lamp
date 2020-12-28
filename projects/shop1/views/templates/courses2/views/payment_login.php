<div class="container login-form-container payment-login-form-container">
	<div class="modal show">
		<div class="modal-dialog">
			<div class="modal-content login">
				<form name="login_form" method="post" action="#" class="form-horizontal">
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
								<label class="col-sm-12 control-label" for="payment-login-username"><?=__('Username')?></label>

								<div class="col-sm-12">
									<div class="input-group input-group-username">
										<span class="input-group-addon"><span class="icon-user"></span></span>
										<input type="text" class="form-control input-lg" id="payment-login-username" name="username" autofocus="autofocus" placeholder="Username" required />
									</div>
									<a href="#"><?= __('Forgotten Username?') ?></a>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-12 control-label" for="payment-login-password"><?=__('Enter 3 digits of 6-digit pin')?></label>

								<div class="col-sm-12">
									<div class="input-group password-digit-fields">
										<div class="input-group-addon"><span class="icon-lock"></span></div>
										<input type="text" class="form-control input-lg" maxlength="1" />
										<input type="text" class="form-control input-lg" maxlength="1" />
										<input type="text" class="form-control input-lg" maxlength="1" disabled placeholder="&times;" />
										<input type="text" class="form-control input-lg" maxlength="1" />
										<input type="text" class="form-control input-lg" maxlength="1" disabled placeholder="&times;" />
										<input type="text" class="form-control input-lg" maxlength="1" disabled placeholder="&times;" />
									</div>
									<a href="#"><?= __('Reset or forgotten PIN?') ?></a>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-12 control-label" for="payment-login-mobile"><?= __('Enter last 4 digits of mobile number') ?></label>
								<div class="col-sm-12">
									<div class="input-group">
										<div class="input-group-addon"><span class="icon-lock"></span></div>
										<input type="text" class="form-control input-lg" id="payment-login-mobile" />
									</div>
								</div>
							</div>

							<div class="form-group" style="display: flex; align-items: center;">
								<div class="col-sm-4">
									<input type="submit" class="form-control btn btn-lg btn-primary" id="login_button" name="login" value="<?=__('Log in')?>"/>
								</div>
							</div>

						</fieldset>

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
<script>
	// After typing a digit, move onto the next field and highlight its contents, if any
	$('.password-digit-fields').on('keypress', ':input', function()
	{
		$(this).find('\~ input:not(:disabled):first').focus().select();
	});
</script>