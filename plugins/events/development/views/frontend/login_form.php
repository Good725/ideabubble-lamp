<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?= __('Register') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?= URL::overload_asset('css/cms.compiled.css') ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/forms.css', ['cachebust' => true]) ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/stylish.css', ['cachebust' => true]) ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/project.css') ?>" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>

    <script src="<?= URL::get_engine_assets_base(); ?>js/bootstrap-3.3.5.min.js"></script>
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
</head>
<body>
<div class="container login-form-container">
    <div class="modal show">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body form-horizontal">
                    <section class="form-section active">
                        <div class="container login-form-container">
                            <div class="modal show">
                                <div class="modal-dialog">
                                    <div class="modal-content login">
                                        <form name="login_form" method="post" action="/login.html" class="form-horizontal">
                                            <div class="modal-header">
                                                <a href="/"><img class="client-logo" src="<?= Ibhelpers::get_login_logo() ?>" alt=""/></a>
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

                                                    <div class="form-group" style="display: flex; align-items: center;">
                                                        <div class="col-sm-4">
                                                            <input type="submit" class="form-control btn btn-lg btn-primary" id="login_button" name="login" value="<?=__('Log in')?>"/>
                                                        </div>

                                                        <div class="col-sm-8">
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
                                                        <p><a href="mailto:support@uticket.ie">Can&apos;t log in?</a></p>
                                                    </div>

                                                    <input type="hidden" name="redirect" value="<?= (isset($redirect)) ? $redirect : '' ?>" />

                                                </fieldset>

                                            </div>
                                            <div class="modal-footer">
                                                <?php /*if ( Model_Plugin::is_loaded('userreq')) : ?>
							<a class="left btn" href="<?php echo URL::Site('/admin/login/user_req/'); ?>"><?=__('Register new user')?></a>
						<?php endif */ ?>

                                                <div class="text-center">
                                                    <p>Need an account? <a href="/registration.html">Sign up.</a></p>

                                                    <ul class="list-inline">
                                                        <li>Terms of use</li>
                                                        <li>Support</li>
                                                        <li>Privacy policy</li>
                                                    </ul>
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

                        <?php if (@$_REQUEST['auto']) { ?>
                            <script>
                                $(document).ready(function(){
                                    $("#auto-logout-modal").modal();
                                });
                            </script>
                        <?php } ?>
                    </section>
                </div>

            </div>
        </div>
    </div>
</div>
</body>
</html>
