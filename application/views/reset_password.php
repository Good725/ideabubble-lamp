<div class="login-form-container">
	<div class="modal show">
		<div class="modal-dialog">
			<div class="modal-content login">
				<form id="reset_password_form" method="post" action="/admin/login/reset/" class="form-horizontal validate-on-submit">
					<input type="hidden" id="id" name="validation" value="<?= isset($id) ? $id : '' ?>"/>

					<div class="modal-header">
						<a href="/"><img class="client-logo" src="<?= Ibhelpers::get_login_logo()?>" alt="" /></a>
                        <?php $mobile_breadcrumbs = MenuArea::factory()->generate_mobile_breadcrumb_links(
                            array(
                                array('link' => '/admin/login', 'name' => 'Login'),
                                array('link' => '#', 'name' => __('Verification email'))
                            ));?>
                        <?= View::factory('mobile_breadcrumb')->set('mobile_breadcrumbs', $mobile_breadcrumbs); ?>
					</div>
					<div class="modal-body">
                        <?= (isset($alert)) ? $alert : '' ?>

						<script>
							$(".alert .close-btn").on("click", function(){
								$(this).parent().hide();
							});
						</script>

						<?php if ( ! isset($info)): ?>
                            <h3><?= __('Reset password') ?></h3>

							<input type="hidden" name="reset_url" value="<?= Request::detect_uri() ?>" />
							<fieldset>
								<div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $attributes = array(
                                            'type'  => 'password',
                                            'class' => 'validate[required,minSize[8]]',
                                            'id'    => 'reset_password_password',
                                        );
                                        $args = array('password_meter' => true);
                                        echo Form::ib_input(__('New password'), 'password', null, $attributes, $args);
                                        ?>
                                    </div>
								</div>

								<div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $attributes = array(
                                            'type'  => 'password',
                                            'class' => 'validate[equals[reset_password_password]',
                                            'id'    => 'reset_password_mpassword'
                                        );
                                        echo Form::ib_input(__('Confirm new password'), 'mpassword', null, $attributes, $args);
                                        ?>
                                    </div>
								</div>
							</fieldset>
						<?php else: ?>
                            <h3><?= __('Verification email sent!') ?></h3>

							<p><?= __('A confirmation link has been sent to your email address.') ?></p>

                            <p><?= __('Thank you') ?></p>
						<?php endif; ?>

						<div class="text-center">
                            <p><a href="/admin/login"><?= __('Return to the log-in page') ?></a></p>

							<?php if ( Model_Plugin::is_loaded('userreq')) : ?>
								<a class="left btn" href="<?php echo URL::Site('/admin/login/user_req/'); ?>"><?=__('Register new user')?></a>
							<?php endif ?>

							<?php if( ! isset($info)): ?>
								<button type="submit" id="reset_password_button" class="btn btn-success btn-lg"><?=__('Confirm')?></button>
							<?php endif; ?>
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

