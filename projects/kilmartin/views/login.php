<?php
/**
 * This file is deprecated. Kilmartin will use the same login view as everyone else.
 * This file can be deleted after all functionality has been transferred to the engine view
 */
?>

<?php if (true || !Settings::instance()->get('account_managed_course_bookings')): ?>
    <?php include APPPATH.'views/login.php'; ?>
<?php else: ?>
    <link rel="stylesheet" href="<?= URL::overload_asset('css/login-signup.css') ?>" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php
    $referal = @$_SERVER["HTTP_REFERER"];
    $offers_text = Settings::instance()->get('login_form_offers_text') ;
    $social_media_logins = (bool) (Settings::instance()->get('google_login') OR Settings::instance()->get('facebook_login'));
    if ($social_media_logins) {
        $facebook_data = Model_Users::get_facebook_data();
        $google_data = Model_Users::get_google_data();
    }
    $mode = (isset($_GET['mode']) && $_GET['mode'] == 'signup') ? 'signup' : 'login';
    ?>

    <div class="login-form-container login-form">
        <div class="modal show">
            <div
                class="modal-dialog<?= ($offers_text) ? ' modal-dialog-wide' : '' ?><?= $social_media_logins ? ' social-media' : '' ?>"
                id="login-form-modal-dialog"
                <?php if ($social_media_logins): ?>
                    <?= isset($facebook_data['appId'])   ? 'data-facebook_app_id="'.$facebook_data['appId'].'"'    : '' ?>
                    <?= isset($google_data['client_id']) ? 'data-google_client_id="'.$google_data['client_id'].'"' : '' ?>
                <?php endif; ?>
                >
                <div class="modal-content login">
                    <div class="login-columns">
                        <?php if (!empty($offers_text)): ?>
                            <div class="login-column login-column--offers"><?= $offers_text ?></div>
                        <?php endif; ?>

                        <div class="login-column login-column--form">
                            <div class="modal-header">
                                <ul class="nav nav-tabs" id="login-form-tabs">
                                    <li<?= ($mode == 'signup' ? ' class="active"' : '')?>><a href="#kilmartin_signup_form" data-toggle="tab">Sign up</a></li>
                                    <li<?= ($mode == 'login'  ? ' class="active"' : '')?>><a href="#kilmartin_login_form" data-toggle="tab">Log in</a></li>
                                </ul>
                            </div>

                            <?= (isset($alert)) ? $alert : '' ?>

                            <?php if (empty($offers_text)): ?>
                                <img class="client-logo<?= $social_media_logins ? ' social-media' : '' ?>" src="<?= Ibhelpers::get_login_logo() ?>" alt="" />
                            <?php endif; ?>

                            <div class="tab-content">
                                <form class="tab-pane<?= ($mode == 'login' ? ' active' : '')?>" id="kilmartin_login_form" name="login_form" method="post" action="/admin/login/<?= ( ! empty($redirect) && trim($redirect)) ? '?redirect='.$redirect : '' ?>">
                                    <input type="hidden" name="redirect" value="<?= ( ! empty($redirect)) ? trim($redirect) : '' ?>" />

                                    <div class="modal-body">
                                        <input id="external_user_id_on_log_in" type="hidden" name="external_user_id" value="" />
                                        <input id="external_provider_id_on_log_in" type="hidden" name="external_provider_id" value="" />
                                        <input type="hidden" name="invite_member" value="<?=html::chars(@$_REQUEST['invite_member'])?>" />
                                        <input type="hidden" name="invite_hash" value="<?=html::chars(@$_REQUEST['invite_hash'])?>" />

                                        <div<?= $social_media_logins ? ' class="social_media_login_active"' : '' ?>>
                                            <?php if ($social_media_logins) : ?>
                                                <div class="social_media_login">
                                                    <h2 class="login-title"><?= __('Log in to your account') ?></h2>

                                                    <?php if (Settings::instance()->get('facebook_login') && $facebook_data['appId']!='' && $facebook_data['secret']!='') : ?>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <a href="#" id="log_in_with_facebook" class="social-btn fb--btn"><span class="fa fa-facebook icon-facebook"></span> Log in with Facebook</a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (Settings::instance()->get('google_login') && $google_data['client_id']!='') : ?>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <a href="#" id="log_in_with_google" class="social-btn gplus--btn"><span class="fa fa-google-plus icon-google-plus"></span> Log in with Google</a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="login_details">
                                                <?php if ($social_media_logins) : ?>
                                                    <div class="login-divider">
                                                        <p class="login-divider-text"><?= __('Or log in with your e-mail') ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <fieldset>
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <div class="input-group login-input-group">
                                                                <label class="input-group-addon" for="login-email">
                                                                    <span class="sr-only"><?=__('Email')?></span>
                                                                    <span class="fa fa-envelope icon-envelope"></span>
                                                                </label>
                                                                <!-- add and remove "error"  -->
                                                                <input type="text" class="form-control input-lg error" id="login-email" name="email" autofocus="autofocus" placeholder="Email" value="<?= (isset($data['email'])) ? HTML::chars($data['email']) : ''; ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <div class="input-group login-input-group">
                                                                <label class="input-group-addon" for="login-password">
                                                                    <span class="sr-only"><?=__('Password')?></span>
                                                                    <span class="fa fa-lock icon-lock"></span>
                                                                </label>

                                                                <input required type="password" class="form-control input-lg" id="login-password" name="password" placeholder="<?=__('Password')?>" autocomplete="off" />

                                                                <span class="input-group-btn view-pwd">
                                                                    <button type="button" class="showPass active" data-target="#login-password">
                                                                        <span class="sr-only"><?= __('Show password?') ?></span>

                                                                        <span class="fa fa-eye icon icon-eye" aria-hidden="true"></span>
                                                                    </button>
                                                                </span>
                                                            </div>
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

                                                    <div class="form-group login-buttons">
                                                        <div class="col-sm-6">
                                                            <input type="submit" class="button btn btn-lg btn-primary" id="login_button" name="login" value="<?=__('Log in')?>"/>
                                                        </div>

                                                        <div class="col-sm-6 remember-me">
                                                            <label style="margin: 0; font-size: 14px;">
                                                                <input type="hidden" name="remember" value="dont-remember"/><?php // Default value for checkbox ?>
                                                                <label class="checkbox-styled">
                                                                    <input id="optionsCheckbox" type="checkbox" name="remember" value="remember"<?= (isset($remember) AND $remember === FALSE) ? '' : ' checked'; ?> />
                                                                    <span class="checkbox-icon"></span>
                                                                </label>
                                                                <?= __('Keep me signed in for 1 day.') ?>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="text-center" style="font-size: 14px; margin-top: 1.5em;">
                                                        <p>
                                                            <a href="/admin/login/forgot_password/" id="passwordlink"<?= (empty($embedded)) ? '' : ' target="_blank"' ?>>
                                                                <span><?=__('Forgot your password?')?></span>
                                                            </a>

                                                            <?php if (Settings::instance()->get('engine_cant_login_mailto')) : ?>
                                                                <br /><a href="mailto:<?=Settings::instance()->get('engine_cant_login_mailto')?>">Can&apos;t login?</a>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>

                                                    <input type="hidden" name="redirect" value="<?= (isset($redirect)) ? $redirect : '' ?>" />
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <div class="text-center">
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
                                </form><?php // end of login ?>

                                <form class="tab-pane<?= ($mode == 'signup' ? ' active' : '')?>" id="kilmartin_signup_form" method="post" action="/admin/login/register<?= ( ! empty($redirect) && trim($redirect)) ? '?redirect='.$redirect : '' ?>">
                                    <input type="hidden" name="external_user_id" id="external_user_id_on_sign_up" />
                                    <input type="hidden" name="external_provider_id"  id="external_provider_id_on_sign_up"/>
                                    <input type="hidden" name="redirect" value="<?= ( ! empty($redirect)) ? trim($redirect) : '' ?>" />

                                    <div class="modal-body">
                                        <div<?= $social_media_logins ? ' class="social_media_login_active"' : '' ?>>
                                            <?php if ($social_media_logins): ?>
                                                <h2 class="login-title"><?= __('Create your account ') ?></h2>

                                                <div class="social_media_login">
                                                    <?php if (Settings::instance()->get('facebook_login') && $facebook_data['appId']!='' && $facebook_data['secret']!='') : ?>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <a id="sign_up_with_facebook" href="#" class="social-btn fb--btn"><i class="fa fa-facebook icon-facebook"></i>Sign up with Facebook</a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (Settings::instance()->get('google_login') && $google_data['client_id']!='') : ?>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <a href="#"  id="sign_up_with_google" class="social-btn gplus--btn"><i class="fa fa-google-plus icon-google-plus"></i>Sign up with Google</a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="login_details">
                                                <?php if ($social_media_logins) : ?>
                                                    <div class="login-divider">
                                                        <p class="login-divider-text"><?= __('Or log in with your e-mail') ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <fieldset>
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <div class="input-group login-input-group">
                                                                <label class="input-group-addon" for="login-email">
                                                                    <span class="sr-only"><?= __('Email') ?></span>
                                                                    <span class="fa fa-envelope icon-envelope"></span>
                                                                </label>

                                                                <input type="email" class="form-control input-lg" id="event-registration-email" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>" placeholder="<?= __('Email *') ?>" required="required" />
                                                            </div>

                                                            <div id="email_error" class="errorlist" style="display: none"><p>Please provide a valid e-mail address.</p></div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <div class="input-group login-input-group">
                                                                <label class="input-group-addon" for="event-registration-password">
                                                                    <span class="sr-only"><?=__('Password') ?></span>
                                                                    <span class="fa fa-lock icon-lock"></span>
                                                                </label>

                                                                <input id="pswd" type="password" class="form-control input-lg" id="event-registration-password" name="password" value="<?= isset($_POST['password']) ? $_POST['password'] : '' ?>" placeholder="<?= __('Password *') ?>" required="required" />

                                                                <span class="input-group-btn view-pwd">
                                                                    <button type="button" class="showPass" data-target="#pswd">
                                                                        <span class="sr-only"><?= __('Show password?') ?></span>

                                                                        <span class="fa fa-eye icon icon-eye" aria-hidden="true"></span>
                                                                    </button>
                                                                </span>
                                                            </div>

                                                            <div id="pswd_info">
                                                                <a class="close-pswd-info" href="javascript:void(0)">
                                                                    <span class="fa fa-times icon icon-times" aria-hidden="true"></span>
                                                                </a>

                                                                <h4>Password strength: <span id="password_strength">Weak</span></h4>

                                                                <div class="t_strength_meter">
                                                                    <span class="veryweak" style="display: block"></span>
                                                                    <span class="medium" style="display: none"></span>
                                                                    <span class="strong" style="display: none"></span>
                                                                </div>

                                                                <ul>
                                                                    <li id="length" class="invalid">Be at least 8 characters</li>
                                                                    <li id="letter" class="invalid">At least one lower case letter</li>
                                                                    <li id="capital" class="invalid">At least one upper case letter</li>
                                                                    <li id="number" class="invalid">At least one number</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <div class="input-group login-input-group">
                                                                <label class="input-group-addon" for="login-password">
                                                                    <span class="sr-only"><?=__('Password') ?></span>
                                                                    <span class="fa fa-lock icon-lock"></span>
                                                                </label>

                                                                <input type="password" class="form-control input-lg" id="event-registration-c_password" name="mpassword" value="<?= isset($_POST['mpassword']) ? $_POST['mpassword'] : '' ?>" placeholder="<?= __('Confirm Password *') ?>" required="required" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php if ((int) Settings::instance()->get('cms_captcha_enabled') == 1): ?>
                                                        <div class="form-group">
                                                            <div class="col-sm-12 text-center">
                                                                <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="form-group login-buttons">
                                                        <div class="col-sm-12">
                                                            <button type="submit" class="button btn btn-primary btn-lg btn-primary" name="action" value="register"><?= __('Sign up') ?></button>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <div class="text-center">
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
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($embedded)): ?>
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
    <?php endif; ?>
    <script src="https://apis.google.com/js/platform.js"></script>

    <script>
        (function ($) {
            var connected_to_facebook = false;
            var $modal_dialog =  $('#login-form-modal-dialog');
            //  facebook
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : $modal_dialog.data('facebook_app_id'),
                    cookie     : true,
                    xfbml      : true,
                    version    : 'v2.8'
                });
    //        FB.AppEvents.logPageView();

                FB.getLoginStatus(function(response) {
                    connected_to_facebook = !(response.status == 'not_authorized' || response.status == 'unknown');
                });
            };
            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            function submit_facebook_log_in() {
                FB.api("/me",{fields:"email"},function (result) {
                    $("#external_user_id_on_log_in").val(result.id);
                    $("#login-email").val(result.email);
                    $("#external_provider_id_on_log_in").val(1);
                    $("#kilmartin_login").submit();
                })
            }

            // google
            function onSignIn(googleUser) {
                var profile = googleUser.getBasicProfile();
                $("#external_user_id_on_log_in").val( googleUser.getAuthResponse().id_token );
                $("#login-email").val(profile.getEmail());
                $("#external_provider_id_on_log_in").val(2);
                $("#kilmartin_login").submit();
            }

            $(document).ready(function(){

               $("#log_in_with_facebook").on('click',function (e) {
                    e.preventDefault();
                   if(!connected_to_facebook ){
                       FB.login(function(response) {
                           if (response.status === 'connected') {
                               submit_facebook_log_in();
                               connected_to_facebook = false;
                           }else{
                               // error
                           }
                       }, {scope: 'public_profile,email'});
                   }else{
                       submit_facebook_log_in();
                       connected_to_facebook = false;
                   }
                });

               // init google
               var google_auth = null;
                gapi.load('auth2', function () {
                    auth2 = gapi.auth2.init({
                        client_id: $modal_dialog.data('google_client_id'),
                        fetch_basic_profile: true,
                        scope: 'profile'
                    });
                    google_auth = auth2;
                });

                //  google
                $("#log_in_with_google").on('click',function (e) {
                    e.preventDefault();
                    // Sign the user in, and then retrieve their ID.
                    google_auth.signIn().then(function (googleUser) {
                        onSignIn(googleUser);
                    });

                });

                $('.showPass')
                    .on('mouseup touchend', function() {
                        $(this.getAttribute('data-target')).attr('type', 'password');
                    })
                    .on('mousedown touchstart', function() {
                        $(this.getAttribute('data-target')).attr('type', 'text');
                    });

                $("#backstretch img").css({"-webkit-filter":'blur(7px)'});

                $("#login_button").click(function(){
                    $("#backstretch img").css({"-webkit-filter":'blur(0px)'});
                    setTimeout(function(){
                        $("#backstretch img").css({"-webkit-filter":'blur(7px)'});
                    },10000);
                });

                $('#login-email').focusout(function(e) {
                    e.preventDefault();
                    if (validateEmail($(this))) {
                        $(this).css('border-color','#adadad');
                    }else{
                        $(this).css('border-color','#dd4b39');
                    }
                });

                <?php
                if (@$_REQUEST['auto']) {
                ?>
                $("#auto-logout-modal").modal();
                <?php
                }
                ?>
            });

            function validateEmail(field) {
                var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
                return email_regex.test(field.val());
            }
        })(jQuery);

        $(document).ready(function(){
            // "show password" buttons disabled, unless a password has been typed
            $('.showPass').prop('disabled', true);
            $('[type="password"]').on('change keyup input', function()
            {
                $(this).find('\+ .view-pwd .showPass').prop('disabled', (this.value == ''));
            });
        });

        $('input[type=password]').keyup(function() {

        }).focus(function() {
            $('#pswd_info').show();
        }).blur(function() {
            $('#pswd_info').hide();
        });
        $('#pswd').on('input',function(e){
            var pswd = $("#pswd").val();
            if (pswd.length < 8 ) {
                $('#length').removeClass('valid').addClass('invalid');
            } else {
                $('#length').removeClass('invalid').addClass('valid');
            }
            if(pswd.match(/[a-z]/) ) {
                $('#letter').removeClass('invalid').addClass('valid');
            } else {
                $('#letter').removeClass('valid').addClass('invalid');
            }
            if (pswd.match(/[A-Z]/) ) {
                $('#capital').removeClass('invalid').addClass('valid');
            } else {
                $('#capital').removeClass('valid').addClass('invalid');
            }
            if (pswd.match(/\d/) ) {
                $('#number').removeClass('invalid').addClass('valid');
            } else {
                $('#number').removeClass('valid').addClass('invalid');
            }
            if (pswd.length = 8 && pswd.match(/[a-z]/) && pswd.match(/[A-Z]/) && pswd.match(/\d/) ) {
                document.getElementById('password_strength').innerHTML = 'Good';
                $('.veryweak').hide();
                $('.medium').show();
                $('.strong').hide();
            }
            if(pswd.length > 9 && pswd.match(/[a-z]/) && pswd.match(/[A-Z]/) && pswd.match(/\d/) && (pswd.match(/[\!\@\#\$\%\^\&\*\(\)\_\+]/)) || pswd.length > 14){
                document.getElementById('password_strength').innerHTML = 'Strong';
                $('.veryweak').hide();
                $('.medium').hide();
                $('.strong').show();
            }
            if(pswd.length < 8 || !pswd.match(/[a-z]/) || !pswd.match(/[A-Z]/) || !pswd.match(/\d/)){
                document.getElementById('password_strength').innerHTML = 'Weak';
                $('.veryweak').show();
                $('.medium').hide();
                $('.strong').hide();
            }
        })
            .focusout(function(e) {
                var pswd = $(this).val();
                e.preventDefault();
                if (pswd.length > 8 && pswd.match(/[a-z]/) && pswd.match(/[A-Z]/) && pswd.match(/\d/) ) {
                    $('#invalid_password').hide();
                }else{
                    $('#invalid_password').show();
                }
            });
    </script>
<?php endif; ?>