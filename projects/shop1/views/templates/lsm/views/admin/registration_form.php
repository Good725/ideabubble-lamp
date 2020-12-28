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
        <?php if (Settings::instance()->get('cms_skin') != ''): ?>
            <link rel="stylesheet" href="<?= URL::get_engine_theme_assets_base().'css/styles.css' ?>" />
        <?php endif; ?>
        <link rel="stylesheet" href="<?= URL::overload_asset('css/project.css') ?>" />

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>

        <script src="<?= URL::get_engine_assets_base(); ?>js/bootstrap-3.3.5.min.js"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body class="layout-login layout-login-register">
        <div class="container login-form-container 12">
            <div class="modal show">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <a href="/"><img class="client-logo" src="<?= URL::overload_asset('img/client-logo.png') ?>" alt="" /></a>
                        </div>

                        <div class="modal-body form-horizontal">
                            <section class="form-section active">
                                <form method="post">
                                    <input type="hidden" name="validate" value="<?=html::chars(@$_REQUEST['validate'])?>" />
                                    <?= $alert ? $alert : ''?>

                                    <!-- <h1><?= __('Sign Up') ?></h1> -->

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="input-group login-input-group">
                                                <label class="input-group-addon" for="login-email">
                                                    <span class="sr-only"><?= __('Email') ?></span>
                                                    <span class="icon-envelope"></span>
                                                </label>    
                                                <input type="email" class="form-control input-lg" id="event-registration-email" name="email" value="<?= isset($_REQUEST['email']) ? html::chars($_REQUEST['email']) : '' ?>" placeholder="<?= __('Email *') ?>" required="required" />
                                             </div>
                                        </div>        
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="input-group login-input-group">
                                                <label class="input-group-addon" for="login-password">
                                                    <span class="sr-only"><?=__('Password') ?></span>
                                                    <span class="icon-lock"></span>
                                                </label>
                                                <input type="password" class="form-control input-lg" id="event-registration-password" name="password" value="<?= isset($_POST['password']) ? html::chars($_POST['password']) : '' ?>" placeholder="<?= __('Password *') ?>" required="required" />
                                        </div>
                                     </div>     
                                    </div>    
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="input-group login-input-group">
                                                <label class="input-group-addon" for="login-password">
                                                    <span class="sr-only"><?=__('Password') ?></span>
                                                    <span class="icon-lock"></span>
                                                </label>    
                                                <input type="password" class="form-control input-lg" id="event-registration-c_password" name="mpassword" value="" placeholder="<?= __('Confirm Password *') ?>" required="required" />
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

                                    <?php if ((int)Settings::instance()->get('cms_captcha_enabled') == 1) { ?>
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="form-group login-buttons">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary btn-lg btn-default continue-button" name="action" value="register"><?= __('Sign up') ?></button>
                                        </div>
                                    </div>
                                </form>
                            </section>
                        </div>

                        <div class="modal-footer">

                            <div class="text-center">
                                <div class="layout-login-alternative_option clearfix">
                                    <p><?=__('Already have an account?')?> <span class="signup-text"><a href="/admin/login">Log in</a></span></p>
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
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <script src="<?= URL::get_engine_assets_base() ?>js/jquery.backstretch.min.js"></script>
        <script>$.backstretch("<?= URL::overload_asset('img/background.jpg') ?>");</script>
    </body>
</html>
