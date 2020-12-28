<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?= __('Duplicate') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?= URL::overload_asset('css/cms.compiled.css') ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/forms.css', ['cachebust' => true]) ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/stylish.css', ['cachebust' => true]) ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/login-signup.css') ?>" />
    <?php if (Settings::instance()->get('cms_skin') != ''): ?>
        <link rel="stylesheet" href="<?= URL::get_engine_theme_assets_base().'css/styles.css' ?>" />
    <?php endif; ?>
    <link rel="stylesheet" href="<?= URL::overload_asset('css/project.css') ?>" />

    <script src="https://apis.google.com/js/platform.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>

    <script src="<?= URL::get_engine_assets_base(); ?>js/bootstrap-3.3.5.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <?php
    if (Settings::instance()->get('google_analytics_backend_tracking')) {
        echo Settings::get_google_analytics_script();
    }
    ?>

</head>
<div class="container login-form-container">
	<div class="modal show">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="theme-form">
					<div class="modal-header">
                        <a href="/"><img class="client-logo" src="<?= Ibhelpers::get_login_logo() ?>" alt="" /></a>
					</div>

                    <div class="modal-body form-horizontal">
                        <section class="form-section active">
					<form method="post">
						<div class="alert alert-info">
							<a class="close-btn"><i class="fa fa-times" aria-hidden="true"></i></a>
							<?php if ($wrong_mobile) { ?>
							<strong>Attention:</strong> Mobile number do not match. Please try again.
							<?php } else { ?>
							<strong>Attention:</strong> We already have your details in the system from a previous telephone or online booking.<br>Please confirm your mobile number to confirm your identity. We will then e-mail you a verification link.
							<?php } ?>
						</div>
						<script>
							$(".alert .close-btn").on("click", function(){
								$(this).parent().hide();
							});
						</script>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="input-group login-input-group">
                                    <label class="input-group-addon" for="login-email">
                                        <span class="sr-only"><?= __('Email') ?></span>
                                        <span class="fa fa-envelope icon-envelope"></span>
                                    </label>
                                    <input type="email" required="required" name="email" class="form-control input-lg" id="event-registration-email" name="email" value="<?=html::chars(@$email)?>" />

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="input-group login-input-group">
                                    <label class="input-group-addon" for="login-mobile">
                                        <span class="sr-only"><?= __('Mobile') ?></span>
                                        <span class="fa fa-mobile icon-mobile"></span>
                                    </label>
                                    <input type="tel" class="form-control input-lg" name="mobile" required="required" placeholder="Confirm Mobile Number" />

                                </div>
                            </div>
                        </div>

                        <div class="form-group login-buttons">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-lg continue-button" name="action" value="register"><?= __('Send') ?></button>
                            </div>
                        </div>

                        </section>
                    </div>


                        <div class="modal-footer">

                            <div class="text-center">

                            <div class="layout-login-alternative_option clearfix">
                                <p>Please contact the office to continue with this
                                    <span class="signup-text">
                                        <i class="fa fa-phone" aria-hidden="true"></i><a href="tel:44 84 95 654">+44 84 95 654</a>
                                        <a href="/admin/login/register">signup</a><a href="/admin/login">login</a>
                                    </span>
                                </p>
                            </div>


                          <?php $footer_links = (Model_Plugin::is_enabled_for_role('Administrator', 'menus') AND class_exists('Menuhelper')) ? Menuhelper::get_all_published_menus('login-form-links') : array(); ?>

                            <?php if ( ! empty($footer_links)): ?>
                                <ul class="list-inline login-links">
                                    <?php foreach ($footer_links as $link): ?>
                                        <li>
                                            <a href="/<?= $link['name_tag'] ?>"><?= $link['title'] ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                        <div class="poweredby">
                            <p><?= Settings::instance()->get('cms_copyright') ?></p>
                        </div>

                        </div>
                    </form>
				</div>

              </div>

			</div>
            
		</div>

	</div>

</div>
