<?php $referal  = @$_SERVER["HTTP_REFERER"]; ?>
<?php if (empty($embedded)): ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            <title><?= __('Register') ?></title>

            <meta name="viewport" content="width=device-width, initial-scale=1">

            <link rel="stylesheet" href="<?= URL::overload_asset('css/cms.compiled.css') ?>" />
            <link rel="stylesheet" href="<?= URL::overload_asset('css/stylish.css') ?>" />
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
        </head>

        <body class="layout-login <?php if(strrpos($referal,"/checkout")){
            echo "layout-login-back-white";
        }
        ?>">
<?php endif; ?>

        <div class="container login-form-container">
            <div class="modal show">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <a href="/"><img class="client-logo" src="<?= URL::overload_asset('img/client-logo.png') ?>" alt="" /></a>
                        </div>

                        <div class="modal-body form-horizontal">
                            <section class="form-section active">
                                <form id="kilmartin_sighn_up_form" method="post" action="/admin/login/register<?= ( ! empty($redirect) && trim($redirect)) ? '?redirect='.$redirect : '' ?>">
                                    <input type="hidden" name="external_user_id" id="external_user_id_on_sign_up" />
                                    <input type="hidden" name="external_provider_id"  id="external_provider_id_on_sign_up"/>
                                    <input type="hidden" name="redirect" value="<?= ( ! empty($redirect)) ? trim($redirect) : '' ?>" />
                                    <input type="hidden" name="invite_member" value="<?=html::chars(@$_REQUEST['invite_member'])?>" />
                                    <input type="hidden" name="invite_hash" value="<?=html::chars(@$_REQUEST['invite_hash'])?>" />

                                    <?= @$alert ? $alert : ''?>
                                    <?php echo $add_or = '';?>
                                    <?php if(Settings::instance()->get('google_login') OR Settings::instance()->get('facebook_login')) : ?>
                                        <?php $add_or = 'Or '; ?>
                                        <h1><?= __('Sign Up') ?></h1>
                                    <?php endif; ?>

                                    <?php
                                    $facebook_data = Model_Users::get_facebook_data();
                                    if(Settings::instance()->get('facebook_login') && $facebook_data['appId']!='' && $facebook_data['secret']!='') : ?>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <a id="sign_up_with_facebook" href="#" class="social-btn fb--btn"><i class="fa fa-facebook icon-facebook"></i>Log in with Facebook</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                    $google_data = Model_Users::get_google_data();
                                    if(Settings::instance()->get('google_login') && $google_data['client_id']!='') : ?>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <a href="#"  id="sign_up_with_google" class="social-btn gplus--btn"><i class="fa fa-google-plus icon-google-plus"></i>Log in with Google</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <h2><?php echo $add_or;?>Sign up with your email</h2>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="input-group login-input-group">
                                                <label class="input-group-addon" for="login-email">
                                                    <span class="sr-only"><?= __('Email') ?></span>
                                                    <span class="fa fa-envelope icon-envelope"></span>
                                                </label>    
                                                <input type="email" class="form-control input-lg" id="event-registration-email" name="email" value="<?= isset($_REQUEST['email']) ? html::chars($_REQUEST['email']) : '' ?>" placeholder="<?= __('Email *') ?>" required="required" />
                                               
                                             </div>
                                            <div id="email_error" class="errorlist" style="display: none"><p>Please provide a valid e-mail address.</p></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="input-group login-input-group">
                                                <label class="input-group-addon" for="login-password">
                                                    <span class="sr-only"><?=__('Password') ?></span>
                                                    <span class="fa fa-lock icon-lock"></span>
                                                </label>
                                                <input id="pswd" type="password" class="form-control  input-lg" id="event-registration-password" name="password" value="<?= isset($_POST['password']) ? $_POST['password'] : '' ?>" placeholder="<?= __('Password *') ?>" required="required" />
                                                <label class="view-pwd">
                                                   <a href="#" class="showPass" data-target="#pswd">
                                                        <span class="fa fa-eye icon icon-eye" aria-hidden="true"></span>
                                                    </a>
                                                </label>
                                        </div>
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

                                    <?php if ((int)Settings::instance()->get('cms_captcha_enabled') == 1) { ?>
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="form-group login-buttons">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary btn-lg continue-button" name="action" value="register"><?= __('Sign up') ?></button>
                                        </div>
                                    </div>
                                </form>
                                
                            </section>
                        </div>

                        <div class="modal-footer">

                            <div class="text-center">

                                <?php if( ! strrpos($referal, '/checkout')):?>
                                    <div class="layout-login-alternative_option clearfix">
                                        <p>
                                            <?=__('Already have an account?')?> <span class="signup-text">
                                                 <?php if (empty($embedded)): ?>
                                                    <a href="/admin/login"><?= __('Log in') ?></a>
                                                <?php else: ?>
                                                    <a href="#" data-toggle="login_modal" data-target="#login-tab-login"><?= __('Log in') ?></a>
                                                <?php endif; ?>
                                            </span>
                                        </p>
                                    </div>
                                <?endif;?>
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
        <script>
            (function ($) {
                var connected_to_facebook = false;
                //  facebook
                window.fbAsyncInit = function() {
                    FB.init({
                        appId      : '<?= $facebook_data['appId'] ?>',
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

                function submit_facebook_sign_up() {
                    FB.api("/me",{fields:"email"},function (result) {
                        $("#external_user_id_on_sign_up").val(result.id);
                        $("#external_provider_id_on_sign_up").val(1);
                        $("#event-registration-email").val(result.email);
                        $("#kilmartin_sighn_up_form").submit();

                    })
                };

                // google
                function onSignIn(googleUser) {
                    var profile = googleUser.getBasicProfile();
        //        $("#external_user_id_on_sign_up").val(profile.getId());
                    $("#external_user_id_on_sign_up").val( googleUser.getAuthResponse().id_token);
                    $("#external_provider_id_on_sign_up").val(2);
                    $("#event-registration-email").val(profile.getEmail());
                    $("#kilmartin_sighn_up_form").submit();
                }

                $(document).ready(function() {

                    $("#sign_up_with_facebook").on('click',function (e) {
                        $("#external_user_id_on_sign_up").after('<input type="hidden" name="action" value="register"/>');
                        e.preventDefault();
                        if(!connected_to_facebook ){
                            FB.login(function(response) {
                                if (response.status === 'connected') {
                                    submit_facebook_sign_up();
                                }else{
                                    // error
                                }
                            }, {scope: 'public_profile,email'});
                        }else{
                            submit_facebook_sign_up();
                        }
                    });

                    // init google
                    var google_auth = null;
                    gapi.load('auth2', function () {
                        auth2 = gapi.auth2.init({
                            client_id: "<?= $google_data['client_id']?>",
                            fetch_basic_profile: true,
                            scope: 'profile'
                        });
                        google_auth = auth2;
                    });

                    $("#sign_up_with_google").on('click',function (e) {
                        e.preventDefault();
                        e.preventDefault();
                        // Sign the user in, and then retrieve their ID.
                        google_auth.signIn().then(function (googleUser) {
                            onSignIn(googleUser);
                        });
                    });


                    $('#pswd').keyup(function() {
                    });
                    $('#pswd').focus(function() {

                    });
                    $('#pswd').blur(function() {

                    });
                    $('#pswd').keyup(function() {

                    }).focus(function() {

                    }).blur(function() {

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

                    $( ".showPass" )
                        .mouseup(function(e) {
                            e.preventDefault();
                            $(this.getAttribute('data-target')).attr('type', 'password');
                        })
                        .mousedown(function(e) {
                            e.preventDefault();
                            $(this.getAttribute('data-target')).attr('type', 'text');
                        });



                    $('#event-registration-email').focusout(function(e) {
                        e.preventDefault();
                        if (validateEmail($(this))) {
                            $('#email_error').hide();
                        }else{
                            $('#email_error').show();
                        }
                    });


                });

                function validateEmail(field) {
                    var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
                    return email_regex.test(field.val());
                }
            })(jQuery);

            $(document).ready(function(){
                $('.showPass').css({'opacity': '0.2', 'cursor': 'not-allowed'});
                $('#pswd').keyup(function(){
                    $('.showPass').css('opacity', this.value == "" ? '0.2' : '1.0');
                    $('.showPass').css('cursor', this.value == "" ? 'not-allowed' : 'pointer');
                })
            });
        </script>

<?php if (empty($embedded)): ?>
        </body>
    </html>
<?php endif; ?>