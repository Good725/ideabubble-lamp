<?php if (Request::current()->directory() != 'admin'): ?>
    <link rel="stylesheet" href="<?= URL::overload_asset('css/login-signup.css') ?>" />
<?php endif; ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php
$referal = @$_SERVER["HTTP_REFERER"];
$offers_text = Settings::instance()->get('login_form_offers_text') ;
$social_media_logins = (bool) (Settings::instance()->get('google_login') OR Settings::instance()->get('facebook_login'));
if ($social_media_logins) {
    $facebook_data = Model_Users::get_facebook_data();
    $google_data = Model_Users::get_google_data();
}
$mode = isset($mode) ? $mode : (isset($_GET['mode']) ? $_GET['mode'] : '');
$mode = isset($mode) ? $mode : (isset($_GET['registered']) && $_GET['registered'] == 'success' ? 'login' : '');
$mode = ($mode == 'signup') ? 'signup' : 'login';

$redirect = isset($redirect) ? trim($redirect) : '';
$redirect = (substr($redirect, 0, 7) === 'http://' || substr($redirect, 0, 8) === 'https://' || substr($redirect, 0, 1) === '/') ? $redirect : '/'.$redirect;

if (isset($_POST['guest_redirect'])) {
    $guest_redirect = $_POST['guest_redirect'];
} else {
    $guest_redirect = isset($guest_redirect) ? trim($guest_redirect) : '';
    $guest_redirect = (substr($guest_redirect, 0, 7) === 'http://' || substr($guest_redirect, 0, 8) === 'https://' || substr($guest_redirect, 0, 1) === '/' || !$guest_redirect) ? $guest_redirect : '/'.$guest_redirect;
}

// This view gets called from at least 2 different places that needs the below code.
$role_select = array();
foreach(ORM::factory('Roles')->where('allow_frontend_register', '=', '1')->find_all()->as_array() as $role_option) {
    $role_select[$role_option->id] = $role_option->role;
}
    $validation_user = new Model_User();
    $validation_user = $validation_user->where('validation_code', '=', @$_REQUEST['validate'])->where('validation_code',
        'is not', null)->find()->as_array();

?>

<div class="login-form-container login-form">
    <div class="modal show">
        <div class="modal-dialog">
            <div class="modal-content login">
                <div class="modal-header">
                    <img class="client-logo<?= $social_media_logins ? ' social-media' : '' ?>" src="<?= Ibhelpers::get_login_logo() ?>" alt="" />

                    <?php if (Settings::instance()->get('engine_enable_external_register') == '1') :?>
                        <ul class="nav nav-tabs" id="login-form-tabs">
                            <li<?= ($mode == 'signup' ? ' class="active"' : '')?>><a href="#login-tab-signup" data-toggle="tab"><?= __('Sign up') ?></a></li>
                            <li<?= ($mode == 'login'  ? ' class="active"' : '')?>><a href="#login-tab-login" data-toggle="tab"><?= __('Log in') ?></a></li>
                        </ul>
                    <?php endif; ?>
                </div>

                <?= (isset($alert)) ? $alert : '' ?>

                <div class="tab-content">
                    <form class="form-horizontal validate-on-submit tab-pane<?= ($mode == 'login' ? ' active' : '')?>" id="login-tab-login" name="login_form" method="post" action="/admin/login/<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>">
                        <input type="hidden" name="redirect" value="<?= html::chars($redirect) ?>" />
                        <input type="hidden" name="guest_redirect" value="<?= html::chars($guest_redirect) ?>" />
                        <input type="hidden" name="invite_member" value="<?=html::chars(@$_REQUEST['invite_member'])?>" />
                        <input type="hidden" name="invite_hash" value="<?=html::chars(@$_REQUEST['invite_hash'])?>" />

                        <div class="modal-body">
                            <?php if ($social_media_logins) : ?>
                                <div class="social_media_login">
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

                                <div class="login-divider">
                                    <p class="login-divider-text"><?= __('Or log in with your e-mail') ?></p>
                                </div>
                            <?php endif; ?>

                            <fieldset>
                                <?php if (Settings::instance()->get('login_form_intro_text')): ?>
                                    <div class="form-group mb-3">
                                        <div class="col-sm-12"><?= Settings::instance()->get('login_form_intro_text') ?></div>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $value = (isset($data['email'])) ? HTML::chars($data['email']) : '';
                                        if ($value == '' && @$_REQUEST['validate']) {
                                            $value = ($validation_user['email']) ? $validation_user['email'] : html::chars($_REQUEST['email']);
                                        }
                                        $attributes = array(
                                            'autofocus' => 'autofocus',
                                            'class'     => 'validate[required,custom[email]]',
                                            'id'        => 'login-email'
                                        );
                                        if($validation_user['email']) {
                                            $attributes['readonly1'] = 'readonly1';
                                        }
                                        echo Form::ib_input(__('Your email'), 'email', $value, $attributes, ['id' => 'email','readonly' => 'readonly']);
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group login-password-wrapper">
                                    <div class="col-sm-12">
                                        <?php ob_start() ?>
                                            <span class="view-pwd">
                                                <button type="button" class="btn-link button--plain showPass" data-target="#login-password">
                                                    <span class="sr-only"><?= __('Show password?') ?></span>

                                                    <span class="fa fa-eye icon icon-eye" aria-hidden="true"></span>
                                                </button>
                                            </span>
                                        <?php $password_icon = ob_get_clean(); ?>

                                        <input type="hidden" name="can_view_password" value="1" />

                                        <?php
                                        $attributes = array(
                                            'autocomplete' => 'off',
                                            'class'        => 'validate[required]',
                                            'id'           => 'login-password',
                                            'type'         => 'password'
                                        );
                                        $args = array('right_icon' => $password_icon);
                                        echo Form::ib_input(__('Password'), 'password', '', $attributes, $args);
                                        ?>
                                    </div>
                                </div>

                                <?php if (i18n::is_multi_language()): ?>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <?php
                                            $options  = '<option value=""></option>';
                                            $options .= i18n::get_allowed_languages_as_options(@$data['lang']);
                                            $attributes = array(
                                                'id'       => 'login_language',
                                                'onChange' => 'document.login_fom.submit()'
                                            );
                                            echo Form::ib_select(__('Choose your language'), 'lang', $options, null, $attributes);
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <div class="col-sm-12 login-remember_me">
                                        <input type="hidden" name="remember" value="dont-remember" /><!-- Value when unchecked -->

                                        <?php
                                        $label = __('Keep me for signed in for $1.', array('$1' => Settings::instance()->get('login_lifetime')));
                                        $checked = (isset($remember) && $remember === false);
                                        echo Form::ib_checkbox_switch($label, 'remember', 'remember', $checked, array(), 'left');
                                        ?>
                                    </div>
                                </div>

                                <?php
                                $disclaimer_text = trim(Settings::instance()->get('sign_up_disclaimer_text'));
                                $has_ssl         = true; // IbHelpers::has_ssl();
                                ?>

                                <?php ob_start(); ?>
                                    <?php if ($disclaimer_text || $has_ssl): ?>
                                        <div class="form-group vertically_center login-disclaimer">
                                            <?php if ($disclaimer_text): ?>
                                                <div class="<?= $has_ssl ? 'col-xs-8 col-sm-9' : 'col-xs-12' ?>"><?= $disclaimer_text ?></div>
                                            <?php endif; ?>

                                            <?php if ($has_ssl): ?>
                                                <div class="<?= $disclaimer_text ? 'col-xs-4 col-sm-3' : 'col-xs-12' ?>" style="display: flex; justify-content: flex-end;">
                                                    <img src="<?= URL::get_engine_assets_base().'img/comodo-secure-logo.png' ?>" alt="Comodo Secure" style="width: 69px;height:39px;max-width:none;" />
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php $disclaimer_and_ssl_block = ob_get_contents(); ?>

                                <div class="form-group login-buttons">
                                    <div class="col-sm-12">
                                        <input type="submit" class="button btn btn-lg btn--full btn-success" id="login_button" name="login" value="<?=__('Log in')?>" />
                                    </div>
                                </div>

                                <?php $cant_login_mailto = trim(Settings::instance()->get('engine_cant_login_mailto')); ?>

                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <p class="login-form-forgot_password"><a href="/admin/login/forgot_password/" id="passwordlink"><strong><?=__('Forgot password?')?></strong></a></p>

                                        <?php if ($cant_login_mailto): ?>
                                            <p><a href="mailto:<?= $cant_login_mailto ?>"><?= __('Can\'t log in') ?></a></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <input type="hidden" name="redirect" value="<?= (isset($redirect)) ? html::chars($redirect) : '' ?>" />
                                <input type="hidden" name="guest_redirect" value="<?= html::chars($guest_redirect) ?>" />
                            </fieldset>

                            <?php if (Settings::instance()->get('engine_enable_external_register') == '1'): ?>
                                <div class="form-group clearfix">
                                    <div class="col-sm-12 layout-login-alternative_option text-center">
                                        <p>
                                            <?= __('Do you need an account?') ?>
                                            <span class="signup-text">
                                                <a href="#login-tab-signup" data-toggle="tab"><?= __('Sign up now!') ?></a>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>

                    <form class="form-horizontal validate-on-submit tab-pane<?= ($mode == 'signup' ? ' active' : '')?>" id="login-tab-signup" method="post" action="/admin/login/register<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>">
                        <input type="hidden" name="external_user_id" id="external_user_id_on_sign_up" />
                        <input type="hidden" name="external_provider_id"  id="external_provider_id_on_sign_up"/>
                        <input type="hidden" name="redirect" value="<?= html::chars($redirect) ?>" />
                        <input type="hidden" name="guest_redirect" value="<?= html::chars($guest_redirect) ?>" />
                        <input type="hidden" name="invite_member" value="<?=html::chars(@$_REQUEST['invite_member'])?>" />
                        <input type="hidden" name="invite_hash" value="<?=html::chars(@$_REQUEST['invite_hash'])?>" />
                        <input type="hidden" name="validate" value="<?=html::chars(@$_REQUEST['validate'])?>" />
                        <div class="modal-body">
                            <div class="individual-sign-up-section">
                                <?php if (Settings::instance()->get('signup_form_intro_text') && empty($validation_user['id'])): ?>
                                    <div class="form-group">
                                        <div class="col-sm-12"><?= Settings::instance()->get('signup_form_intro_text') ?></div>
                                    </div>
                                <?php endif; ?>
                            <?php if (Settings::instance()->get('engine_enable_org_register') && empty($validation_user['id'])): ?>
                                <div class="form-group mb-3">
                                    <div class="col-sm-12" style="font-size: 16px;">
                                        <div class="contact-type-selection">
                                            <p style="margin: 0 0 .5em;"><?= __('I am an') ?></p>
                                            <?php $options = array('individual' => __('Individual'),
                                                'organisation' => __('Organisation'));
                                            $input_attributes = array('class' => 'validate[required]');
                                            $group_attributes = array('class' => 'stay_inline');
                                            echo Form::btn_options('contact-type', $options, null, false,
                                                    $input_attributes, $group_attributes);
                                            $input_attributes = array('class' => 'validate[required]');
                                            $group_attributes = array('class' => 'stay_inline hidden individual_role_selection mt-3 mb-0');
                                            // If there is only one item role enabled in the sign up, automatically select it
                                            $selected = (count($role_select) == 1) ? key($role_select) : null;
                                            echo Form::btn_options('role', $role_select, $selected, false, $input_attributes,
                                                $group_attributes);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif(Settings::instance()->get('account_managed_course_bookings') && !isset($validation_user['id'])): ?>
                                <div class="form-group">
                                    <div class="col-sm-12" style="font-size: 16px;">
                                        <p style="margin: 0 0 .5em;"><?= __('I am a') ?></p>
                                        <?= Form::btn_options('role', $role_select, null, false,
                                            array('class' => 'validate[required]'),
                                            array('class' => 'stay_inline'));
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <?php
                                            if (@$_REQUEST['validate']) {
                                                $value = html::chars($validation_user['name']);
                                            } else {
                                                if (isset($_POST['name'])) {
                                                    $value = $_POST['name'];
                                                } else {
                                                    $value = '';
                                                }
                                            }
                                            $attributes = array('id' => 'first-name');
                                        ?>
                                        <?= Form::ib_input(__('First name'), 'name', $value, $attributes) ?>
                                    </div>

                                    <div class="col-xs-6">
                                        <?php if (@$_REQUEST['validate']) {
                                            $value = html::chars($validation_user['surname']);
                                        } else {
                                            if (isset($_POST['surname'])) {
                                                $value = $_POST['surname'];
                                            } else {
                                                $value = '';
                                            }

                                        }
                                        $attributes = array('id' => 'last-name');
                                        ?>
                                        <?= Form::ib_input(__('Last name'), 'surname', $value, $attributes) ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $value = '';
                                            $attributes = array('class' => 'validate[required,custom[email]]');
                                        if (@$_REQUEST['validate']) {
                                            $value = html::chars($validation_user['email']);
                                            $attributes['readonly'] = 'readonly';
                                        }
                                        $attributes['id'] = 'email';

                                        echo Form::ib_input(__('Your email'), 'email', $value, $attributes);
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12 login-password-wrapper">
                                        <?php ob_start() ?>
                                            <span class="view-pwd">
                                                <button type="button" class="btn-link button--plain showPass" data-target="#pswd">
                                                    <span class="sr-only"><?= __('Show password?') ?></span>

                                                    <span class="fa fa-eye icon icon-eye" aria-hidden="true"></span>
                                                </button>
                                            </span>
                                        <?php $password_icon = ob_get_clean(); ?>

                                        <input type="hidden" name="can_view_password" value="1" />

                                        <?php
                                        $value      = (isset($data['password'])) ? $data['password'] : '';
                                        $attributes = array('type' => 'password', 'class' => 'validate[required, custom[customPassword]]', 'id' => 'pswd');
                                        $args       = array('right_icon' => $password_icon, 'password_meter' => true);
                                        echo Form::ib_input(__('Password'), 'password', $value, $attributes, $args)
                                        ?>
                                    </div>
                                </div>

                                <?php if ((int)Settings::instance()->get('cms_captcha_enabled') == 1) { ?>
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <div class="g-recaptcha" data-sitekey="<?= Settings::instance()->get('captcha_public_key') ?>"></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php // Check to see if an org variable is set, if one is set then they all must be
                            if ($org_industries && Settings::instance()->get('engine_enable_org_register')): ?>
                            <div class="org-sign-up-section hidden">
                                <div class="form-group">
                                    <div class="col-sm-12"><h3>Please enter your job title & organisation details - this
                                            will help us to personalise our service.</h3></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $attributes = array('class' => 'validate[required]', 'id' => 'job_title');
                                        echo Form::ib_input(__('Job title'), 'job_title', '', $attributes);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $attributes = array(
                                            'class' => 'validate[required]',
                                            'id' => 'org-name'
                                        );
                                        echo Form::ib_input(__('Organisation name'), 'org_name', '', $attributes);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $options = array();
                                        $options[''] = 'Please select';
                                        foreach (@$org_industries as $org_industry) {
                                            $options[$org_industry['id']] = $org_industry['label'];
                                        }
                                        $attributes = array('id' => 'org-industry');
                                        echo Form::ib_select(__('Industry'), 'org_industry', $options, null,
                                            $attributes);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $options = array();
                                        $options[''] = 'Please select';
                                        foreach (@$org_sizes as $org_size) {
                                            $options[$org_size['id']] = $org_size['label'];
                                        }
                                        $attributes = array('id' => 'org-size');
                                        echo Form::ib_select(__('Organisation size'), 'org_size', $options, null,
                                            $attributes);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php
                                        $options = array();
                                        $options[''] = 'Please select';
                                        foreach (@$job_functions as $job_function) {
                                            $options[$job_function['id']] = $job_function['label'];
                                        }
                                        $attributes = array('id' => 'job-function');
                                        echo Form::ib_select(__('Job function'), 'job_function', $options, null,
                                            $attributes);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(Settings::instance()->get('engine_enable_organisation_signup_flow') == '1'):?>
                                <div class="org-check-section hidden">
                                <div class="simplebox" id="existing_organisations">
                                    <p>Please choose any entries below that match you organisation info</p>

                                </div>
                            </div>
                                <div class="org-details-section hidden">
                                    <input type="hidden" name="selected_organisation" id="selected_organisation" value=""/>
                                    <input type="hidden" name="synced_organisation" id="synced_organisation" value=""/>
                                    <input type="hidden" name="domain_blacklisted" id="domain_blacklisted" value=""/>
                                    <input type="hidden" name="signup" id="signup" value=""/>
                                    <div class="form-group">
                                    <div class="col-sm-12">

                                        <?php $attributes = array(
                                            'class' => 'validate[required]',
                                            'id' => 'org-details-name',
                                            'disabled' => 'disabled')?>
                                            <?= Form::ib_input(__('Organisation Name'), 'organisation_name', '', $attributes) ?>
                                    </div>
                                </div>
                                    <div class="form-group hidden">
                                    <div class="col-sm-4">
                                        <?php $attributes = array(
                                            'class' => '',
                                            'id' => 'org-details-www',
                                            'disabled' => 'disabled')?>
                                        <?= Form::ib_input(__('www'), 'www', '', $attributes) ?>

                                    </div>
                                    <div class="col-sm-8">
                                        <?php $attributes = array(
                                            'class'=> '',
                                            'id' => 'org-details-domain-name')?>
                                        <?= Form::ib_input(__('Organisation Website'), 'domain_name', '', $attributes) ?>
                                    </div>
                                </div>
                                    <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php $attributes = array(
                                            'class' => 'validate[required]',
                                            'id' => 'org-details-address1')?>
                                        <?= Form::ib_input(__('Address 1'), 'address1', '', $attributes) ?>
                                    </div>
                                </div>
                                    <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php $attributes = array(
                                            'id' => 'org-details-address2')?>
                                        <?= Form::ib_input(__('Address 2'), 'address2', '', $attributes) ?>
                                    </div>
                                </div>
                                    <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php $attributes = array(
                                            'id' => 'org-details-address3')?>
                                        <?= Form::ib_input(__('Address 3'), 'address3', '', $attributes) ?>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <?php $attributes = array(
                                                'class' => 'validate[required]',
                                                'id' => 'org-details-country');
                                            $countries = Model_Country::get_countries();
                                            $countries_options = array('' => 'Please select...');
                                            foreach ($countries as $key => $country) {
                                                $countries_options[$country['id']] = $country['name'];
                                            }
                                            ?>
                                            <?= Form::ib_select(__('Country'), 'country', $countries_options, null, $attributes); ?>
                                        </div>
                                    </div>
                                    <div class="form-group hidden">
                                    <div class="col-sm-12">
                                        <?php $county_options = array();
                                            $county_options[''] = 'Please select...';
                                            $counties = Model_Cities::get_counties();
                                            foreach($counties as $county) {
                                                $county_options[$county['id']] = $county['name'];
                                            }
                                            $attributes = array(
                                                    'id' => 'org-details-county');
                                        ?>

                                        <?= Form::ib_select(__('County'), 'county', $county_options , null, $attributes);?>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <?php $attributes = array(
                                                'class' => 'validate[required]',
                                                'id' => 'org-details-city')?>
                                            <?= Form::ib_input(__('City'), 'city', '', $attributes) ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php $attributes = array(

                                            'id' => 'org-details-post_code')?>
                                        <?= Form::ib_input(__('Post Code'), 'postcode', '', $attributes) ?>
                                    </div>
                                </div>

                                 </div>
                            <?php endif;?>
                            <?= $disclaimer_and_ssl_block; ?>
                            <div class="form-group login-buttons">
                                <div class="col-sm-12">
                                    <button type="submit" class="button btn btn-lg btn--full btn-success" name="action"
                                            id="sign_up_button" value="register"><?= __('Sign up') ?></button>
                                </div>
                            </div>
                            <div class="form-group org-back-to-personal-details org-sign-up-section hidden mb-0">
                                <div class="col-sm-12">
                                    <button type="button" class="mobile-menu-back button--plain" id="mobile-menu-back"
                                            title="Back">
                                        <strong>
                                            <span class="icon-angle-left fa fa-angle-left"></span>
                                            <span class="sr-only">Back</span>
                                        </strong>
                                    </button>
                                    <strong class="org-sign-up-back-header" style="cursor: pointer;">Go back</strong>
                                </div>
                            </div>
                        </div>
                    </form>


                <div class="modal-footer">
                    <?php $continue_as_guest_text = __('Continue as a guest').' &nbsp; <span class="fa fa-angle-right icon-angle-right"></span>' ?>

                    <?php if (Request::current()->directory() == 'admin'): ?>
                        <?php $is_admin_redirect = strpos('/'.$redirect.'/', '/admin/') !== false ?>
                        <a href="<?= ($guest_redirect && $guest_redirect != '/') ? html::chars($guest_redirect) : ($is_admin_redirect ? '/' : html::chars($redirect)) ?>"><?= $continue_as_guest_text ?></a>
                    <?php elseif (!empty($guest_redirect)): ?>
                        <a href="<?= html::chars($guest_redirect) ?>"><?= $continue_as_guest_text ?></a>
                    <?php else: ?>
                        <button type="button" class="btn-link button--plain cancel"><?= $continue_as_guest_text ?></button>
                    <?php endif; ?>

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

                    <?php if (false): // future of this section is uncertain ?>
                        <div class="poweredby">
                            <p><?= Settings::instance()->get('cms_copyright') ?></p>
                        </div>
                    <?php endif; ?>
                </div>
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
                <button class="btn" data-dismiss="modal"><?= __('Log in') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    var selected_existing_organisation = null;
    var domain_name = '';
    var count_organisations_found = 0;
    function capitalize_input()
    {
        var value = this.value.toLowerCase();
        value = value.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1);});
        // Capitalise the letter after "Mc" or "O'". e.g. "McMahon" and "O'Mahony"
        this.value = value.replace(/(Mc|O')([a-z])/g, function(txt, $1, $2){return $1+$2.toUpperCase();});
    }
    $(document).ready(function() {
        $("#first-name, #last-name, #org-name").on("change", capitalize_input);
        $('.showPass')
            .on('mouseup touchend', function() {
                $(this.getAttribute('data-target')).attr('type', 'password');
            })
            .on('mousedown touchstart', function() {
                $(this.getAttribute('data-target')).attr('type', 'text');
            });

        <?php if(Settings::instance()->get('engine_enable_external_register') == '1'): ?>
            $('.contact-type-selection').on('click', '[name="contact-type"]', function () {
                if(this.value == 'organisation') {
                    $('.contact-type-selection').data('contact-type', this.value);
                    $('#sign_up_button').text('Continue');
                    $('.individual_role_selection').addClass('hidden');
                } else {
                    $('.contact-type-selection').data('contact-type', this.value);
                    $('#sign_up_button').text('Sign up');
                    $('.individual_role_selection').removeClass('hidden');
                }
            });
        $(document).on('change','input[type=radio][name=existing_organisation]', function(){
            if ($('input[type=radio][name=existing_organisation]:checked').val() == 'none') {
                $('#sign_up_button').text('Continue');
            } else {
                $('#sign_up_button').text('Verify and Continue');
            }
        });
        $(document).on('change', '#org-details-country', function(){
            var country = $(this).val();
            get_counties(country);
        });
        function get_counties(country, county = '', disabled = false) {
            if (country === '') {
                $('#org-details-county').find('option').remove();
                $('#org-details-county').append('<option value="">Please select...</option>');
                $('#org-details-county').removeClass('validate[required]');
                $('#org-details-county').closest('div.form-group').addClass('hidden');
            } else if(country !== 'IE' && country !== 'NIR') {
                $('#org-details-county').find('option').remove();
                $('#org-details-county').append('<option value="">Please select...</option>');
                $('#org-details-county').removeClass('validate[required]');
                $('#org-details-county').closest('div.form-group').addClass('hidden');
            } else {
                var data = {
                    country: country
                };
                if(county != '') {
                    data.county = county;
                }
                $.ajax(
                    {url: "/admin/login/ajax_get_counties",
                        data: data,
                        type: "POST",
                        success: function (response) {
                            if (response.length === 0) {
                                $('#org-details-county')
                                    .find('option')
                                    .remove();
                                $('#org-details-county').append('<option value="">Please select...</option>');
                                $('#org-details-county').removeClass('validate[required]');
                                $('#org-details-county').closest('div.form-group').addClass('hidden');
                            } else {
                                $('#org-details-county')
                                    .find('option')
                                    .remove();
                                if(!response.counties || response.counties.length === 0) {
                                    $('#org-details-county').append('<option value="">Please select...</option>')
                                    $('#org-details-county').removeClass('validate[required]');
                                    $('#org-details-county').closest('div.form-group').addClass('hidden');
                                } else {
                                    $('#org-details-county').append('<option value="">Please select...</option>')
                                    if (country == 'IE' || country == 'NIR') {
                                        $.each(response.counties, function(key, county) {
                                            var option = '<option value="' + county.id + '">' + county.name + '</option>';
                                            $('#org-details-county').append(option);
                                        });
                                        if (response.county.id) {
                                            $('#org-details-county').val(response.county.id);
                                        } else {
                                            $('#org-details-county').val(county);
                                        }
                                        if (disabled) {
                                            $('#org-details-county').attr('disabled', 'disabled');
                                        }  else {
                                            $('#org-details-county').removeAttr('disabled');
                                        }
                                        $('#org-details-county').addClass('validate[required]');
                                        $('#org-details-county').closest('div.form-group').removeClass('hidden');
                                    } else {
                                        $('#org-details-county').removeClass('validate[required]');
                                        $('#org-details-county').closest('div.form-group').addClass('hidden');
                                    }
                                }
                            }
                        }
                    });
            }
        }
            <?php if(Settings::instance()->get('engine_enable_organisation_signup_flow') == '1'):?>
        var signup_session_string = window.location.search;
        if (signup_session_string !== undefined) {
            var urlParams = new URLSearchParams(signup_session_string);
            var signup_param = urlParams.get('signup');
            $('#signup').val(signup_param);
            if (signup_param) {
                window.disableScreenDiv.style.visibility = "visible";
                $('.individual-sign-up-section').addClass('hidden');
                $('.org-sign-up-section').addClass('hidden');
                $('.org-check-section').removeClass('hidden');
                $('.org-details-section').addClass('hidden');
                $('.org-back-to-personal-details').addClass('hidden');
                $('.contact-type-selection').data('contact-type','organisation');
                $('input[name=contact-type][value=organisation]').attr('checked', true);
                $('#sign_up_button').text('Verify and Continue');
                $('#login-form-tabs a[href="#login-tab-signup"]').tab('show');
                $('#pswd').removeClass['validate[required]'];
                var email = $('#email').val();
                var ind = email.indexOf("@");
                domain_name = email.substr(ind+1);

                $.ajax(
                    {
                        url: "/admin/login/ajax_check_signup_param",
                        data: {
                            //here we  param name, name to check if the session for this url existis and receive data to continue
                            signup: signup_param
                        },
                        type: "POST",
                        success: function (response) {
                            window.disableScreenDiv.style.visibility = "visible";
                            if (response.result=='success') {
                                console.log(response);
                                $('#first-name').val(response.first_name);
                                $('#last-name').val(response.last_name);
                                $('#email').val(response.email);
                                $('#job-function').val(response.job_function);
                                $('#job_title').val(response.job_title);
                                $('#org-name').val(response.org_name);
                                $('#org-industry').val(response.org_industry);
                                $('#org-size').val(response.org_size);
                                $('#domain_blacklisted').val(response.domain_is_blacklisted);
                                $('#org-details-domain-name').val(response.domain_name);
                                domain_name = response.domain_name;
                                $.ajax(
                                    {
                                        url: "/admin/login/ajax_get_organisations",
                                        data: {
                                            //here we  send organisation name to filter only similar
                                            name: $('#org-name').val(),
                                            domain_name: domain_name,
                                            signup: signup_param
                                        },
                                        type: "POST",
                                        success: function (response) {
                                            if (response.length == 0) {
                                                count_organisations_found = 0;
                                                $('#org-details-name').val($('#org-name').val());
                                                $('#org-details-domain-name').val(domain_name);
                                                $('#org-details-name').removeAttr('disabled');
                                                $('#org-details-domain-name').removeAttr('disabled');
                                                $('#org-details-name').attr('readonly', true);
                                                $('#org-details-domain-name').attr('readonly', true);
                                                $('#org-details-address1').removeAttr('disabled');
                                                $('#org-details-address2').removeAttr('disabled');
                                                $('#org-details-address3').removeAttr('disabled');
                                                $('#org-details-city').removeAttr('disabled');
                                                $('#org-details-post_code').removeAttr('disabled');
                                                $('#org-details-county').removeAttr('disabled');
                                                $('#org-details-country').removeAttr('disabled');
                                                $('.individual-sign-up-section').addClass('hidden');

                                                $('.org-sign-up-section').addClass('hidden');
                                                $('.org-check-section').addClass('hidden');
                                                $('.org-details-section').removeClass('hidden');
                                                $('.org-back-to-personal-details').addClass('hidden');
                                            } else {
                                                $.each(response, function(el, organisation) {
                                                    var organisation_address = organisation.address ? ', ' + organisation.address : '';
                                                    var organisation_id = organisation.id.cms_id !== undefined ? organisation.id.cms_id : organisation.id;
                                                    console.log(organisation_id);
                                                    var organisation_el = '<div class="form-group">' +
                                                        '<div class="col-sm-12">' +
                                                        '<label class="form-radio">' +
                                                        '<input type="radio" data-synced="' + organisation.synced + '" name="existing_organisation"' +
                                                        ' id="existing_organisation_'+ organisation_id + '" value="' + organisation_id + '"/>' +
                                                        '<span class="form-radio-helper"></span>'  +
                                                        '<span class="form-radio-label">' + organisation.name + ' ' + organisation_address +  '</span>' +
                                                        '</label></div></div>';
                                                    $('#existing_organisations').append(organisation_el);
                                                });

                                                count_organisations_found = response.length;
                                                $('#existing_organisations').append('' +
                                                    '<div class="form-group">' +
                                                    '<div class="col-sm-12">'+
                                                    '<label class="form-radio">' +
                                                    '<input type="radio" name="existing_organisation" id="existing_organisation_none" value="none"/>' +
                                                    '<span class="form-radio-helper"></span>'  +
                                                    '<span class="form-radio-label">None of Above</span>' +
                                                    '</label>' +
                                                    '</div>' +
                                                    '</div>');
                                                if (selected_existing_organisation) {
                                                    $('#' + selected_existing_organisation).attr('checked', true);
                                                }
                                            }
                                            window.disableScreenDiv.style.visibility = "hidden";
                                        }
                                    });
                            } else {
                                window.location.href = '/admin/login';
                            }
                        }
                    });
            }
        }

                $('#login-tab-signup').validationEngine({
            onValidationComplete: function (form, status) {
                <?php // This function, for some reason gets executed twice. The below conditions satisfies the intended
                // workflow so the form gets submitted when it is intended ?>
                if(form !== undefined
                    && (status
                        && $('.contact-type-selection').data('contact-type') == 'organisation'
                        && $('.org-details-section').hasClass('hidden'))){
                    $('#org-details-name').val($('#org-name').val());
                    var email = $('#email').val();
                    var ind = email.indexOf("@");
                    domain_name = email.substr(ind+1);
                    $('#org-details-domain-name').val(domain_name);
                    if (!$('.individual-sign-up-section').hasClass('hidden')) {
                        $('.individual-sign-up-section').addClass('hidden');
                        $('.org-sign-up-section').removeClass('hidden');
                        $('.org-check-section').addClass('hidden');
                        $('.org-details-section').addClass('hidden');
                        $('.org-back-to-personal-details').removeClass('hidden');
                        $('#sign_up_button').text('Continue');
                        document.cookie="domain_name=" + domain_name;
                        document.cookie="first_name=" + $('#first-name').val();
                        document.cookie="last_name=" + $('#last-name').val();
                        document.cookie="email=" + $('#email').val();
                        return false;
                    }
                    if (!$('.org-sign-up-section').hasClass('hidden')) {
                        $('.individual-sign-up-section').addClass('hidden');
                        $('.org-sign-up-section').addClass('hidden');
                        $('.org-check-section').removeClass('hidden');
                        $('.org-details-section').addClass('hidden');
                        $('.org-back-to-personal-details').removeClass('hidden');
                        $('#sign_up_button').text('Verify and Continue');
                        if ($('#existing_organisations').find('input').length > 0) {
                            selected_existing_organisation = $('input[name="existing_organisation"]:checked').attr('id');
                            $('#existing_organisations').find('input').closest('div.form-group').remove();
                        } else {
                            selected_existing_organisation = null;
                        }
                        var signup_data = {};
                        signup_data.domain_name = domain_name;
                        signup_data.first_name = $('#first-name').val();
                        signup_data.last_name = $('#last-name').val();
                        signup_data.email = $('#email').val();
                        signup_data.password = $('#pswd').val();
                        signup_data.job_title = $('#job_title').val();
                        signup_data.org_name = $('#org-name').val();
                        signup_data.org_industry = $('#org-industry').val();
                        signup_data.org_size = $('#org-size').val();
                        signup_data.job_function = $('#job-function').val();
                        signup_data.domain_is_blacklisted = $('#domain_blacklisted').val();
                        signup_data.return_url = window.location.pathname;
                        $.ajax(
                            {url: "/admin/login/ajax_save_organisation_data",
                                data: signup_data,
                                type: "POST",
                                success: function (response) {
                                    window.location.href='/admin/login';
                                }
                            }
                        );
                        return false;

                    }
                    if (!$('.org-check-section').hasClass('hidden')) {
                        var selected_existing_organisation = $('input[name="existing_organisation"]:checked').val();
                        var selected_existing_organisation_synced = $('input[name="existing_organisation"]:checked').data('synced');
                        if (selected_existing_organisation !== undefined && selected_existing_organisation !== 'none') {
                            $('#selected-organisation').val($('#existing_organisation_' + selected_existing_organisation).val());
                            $('#org-details-name').val($('#existing_organisation_' + selected_existing_organisation).
                                closest('label').find('.form-radio-label').html());
                            $.ajax(
                                {
                                    url: "/admin/login/ajax_get_organisation",
                                    data: {
                                        id: selected_existing_organisation,
                                        synced: selected_existing_organisation_synced
                                    },
                                    type: "POST",
                                    success: function (response) {
                                        if (response && response.id) {
                                            $('#org-details-name').val(response.name).attr('disabled','disabled');
                                            $('#org-details-domain-name').val(response.domain_name).attr('disabled','disabled');
                                            $('#org-details-address1').val(response.address1).attr('disabled','disabled');
                                            $('#org-details-address2').val(response.address2).attr('disabled','disabled');
                                            $('#org-details-address3').val(response.address3).attr('disabled','disabled');
                                            $('#org-details-city').val(response.city).attr('disabled','disabled');
                                            $('#org-details-post_code').val(response.postcode).attr('disabled','disabled');
                                            $('#org-details-country').val(response.country).attr('disabled','disabled');
                                            get_counties(response.country, response.county, true);
                                            $('#selected_organisation').val(response.id);
                                            $('#synced_organisation').val(response.synced);
                                        } else {
                                            $('#org-details-name').removeAttr('disabled');
                                            $('#org-details-domain-name').removeAttr('disabled');
                                            $('#org-details-name').attr('readonly', true);
                                            $('#org-details-domain-name').attr('readonly', true);
                                            $('#org-details-address1').removeAttr('disabled');
                                            $('#org-details-address2').removeAttr('disabled');
                                            $('#org-details-address3').removeAttr('disabled');
                                            $('#org-details-city').removeAttr('disabled');
                                            $('#org-details-post_code').removeAttr('disabled');
                                            $('#org-details-county').removeAttr('disabled');
                                            $('#org-details-country').removeAttr('disabled');
                                            $('#selected_organisation').val('');
                                            $('#synced_organisation').val(false);
                                        }
                                    }
                                });
                        } else {
                            $('#org-details-name').val($('#org-name').val());
                            $('#org-details-domain-name').val(domain_name);
                            $('#org-details-name').removeAttr('disabled');
                            $('#org-details-domain-name').removeAttr('disabled');
                            $('#org-details-name').attr('readonly', true);
                            $('#org-details-domain-name').attr('readonly', true);
                            $('#org-details-address1').removeAttr('disabled');
                            $('#org-details-address2').removeAttr('disabled');
                            $('#org-details-address3').removeAttr('disabled');
                            $('#org-details-city').removeAttr('disabled');
                            $('#org-details-post_code').removeAttr('disabled');
                            $('#org-details-county').removeAttr('disabled');
                            $('#org-details-country').removeAttr('disabled');
                            $('#selected_organisation').val('');
                            $('#synced_organisation').val(false);
                            if (selected_existing_organisation == 'none')  {
                                $('#org-details-address1').val('');
                                $('#org-details-address2').val('');
                                $('#org-details-address3').val('');
                                $('#org-details-city').val('');
                                $('#org-details-post_code').val('');
                                $('#org-details-county').val('');
                                $('#org-details-country').val('');
                                $('#selected_organisation').val('');
                                $('#synced_organisation').val(false);
                            }

                        }
                        $('.individual-sign-up-section').addClass('hidden');

                        $('.org-sign-up-section').addClass('hidden');
                        $('.org-check-section').addClass('hidden');
                        $('.org-details-section').removeClass('hidden');
                        $('.org-back-to-personal-details').removeClass('hidden');
                        $('#sign_up_button').text('Sign Up');
                        return false;
                    }
                } else if (status && !($('.contact-type-selection').data('contact-type') == 'organisation'
                    && $('.org-details-section').hasClass('hidden'))){
                    return true;
                }
            }
        });
                $('#login-tab-signup').on('click', '.org-back-to-personal-details', function(ev) {
                if (!$('.individual-sign-up-section').hasClass('hidden')) {
                    $('#sign_up_button').text('Continue');
                    $('.org-back-to-personal-details').addClass('hidden');

                }
                if (!$('.org-sign-up-section').hasClass('hidden')) {
                    $('.individual-sign-up-section').removeClass('hidden');
                    $('.org-sign-up-section').addClass('hidden');
                    $('.org-check-section').addClass('hidden');
                    $('.org-details-section').addClass('hidden');
                    $('.org-back-to-personal-details').addClass('hidden');
                    $('#sign_up_button').text('Continue');
                }
                if (!$('.org-check-section').hasClass('hidden')) {
                    $('.individual-sign-up-section').addClass('hidden');
                    $('.org-sign-up-section').removeClass('hidden');
                    $('.org-check-section').addClass('hidden');
                    $('.org-details-section').addClass('hidden');
                    $('#sign_up_button').text('Continue');
                }
                if (!$('.org-details-section').hasClass('hidden')) {
                    if (count_organisations_found == 0) {
                        $('.individual-sign-up-section').addClass('hidden');
                        $('.org-sign-up-section').removeClass('hidden');
                        $('.org-check-section').addClass('hidden');
                        $('.org-details-section').addClass('hidden');
                        $('.org-back-to-personal-details').addClass('hidden');
                        $('#sign_up_button').text('Continue');
                    } else {
                        $('.individual-sign-up-section').addClass('hidden');
                        $('.org-sign-up-section').addClass('hidden');
                        $('.org-check-section').removeClass('hidden');
                        $('.org-details-section').addClass('hidden');
                        $('.org-back-to-personal-details').addClass('hidden');
                        $('#sign_up_button').text('Verify and Continue');
                    }

                }
                });
            <?php else:?>
                $('#login-tab-signup').validationEngine({
                    onValidationComplete: function (form, status) {
                     <?php // This function, for some reason gets executed twice. The below conditions satisfies the intended
                        // workflow so the form gets submitted when it is intended ?>
                        if(form !== undefined && (status && $('.contact-type-selection').data('contact-type') == 'organisation' && $('.org-sign-up-section').hasClass('hidden'))){
                            $('.individual-sign-up-section').toggleClass('hidden');
                            $('.org-sign-up-section').toggleClass('hidden');
                            $('#sign_up_button').text('Sign up');
                        } else if (status && !($('.contact-type-selection').data('contact-type') == 'organisation' && $('.org-sign-up-section').hasClass('hidden'))){
                            return true;
                        }
                    }
                });
                $('#login-tab-signup').on('click', '.org-back-to-personal-details', function(ev) {
                    $('.individual-sign-up-section').toggleClass('hidden');
                    $('.org-sign-up-section').toggleClass('hidden');
                    $('#sign_up_button').text('Continue');
                });
            <?php endif?>

        <?php
        endif;
        if (@$_REQUEST['auto']) {
        ?>
        $("#auto-logout-modal").modal();
        <?php
        }
         ?>
    });
</script>

