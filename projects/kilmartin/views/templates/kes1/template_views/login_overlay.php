<?php
$bookings_interview_login_require = (int)Settings::instance()->get('bookings_interview_login_require');
$display_login = Settings::instance()->get('bookings_checkout_login_require') == 1;
if (@$confirmation == 'subscription') {
    $display_login = false;
}
if ($bookings_interview_login_require == 0 && @$interview) {
    $display_login = false;
}
if ( ! Auth::instance()->logged_in() && $display_login):
?>
    <?php
    $social_media_logins = (Settings::instance()->get('google_login') || Settings::instance()->get('facebook_login'));
    $offers_text         = Settings::instance()->get('login_form_offers_text');
    $just_registered     = (isset($_GET['registered']) && $_GET['registered'] == 'success');
    $modal_open          = ($just_registered || (isset($_GET['modal']) && $_GET['modal'] == 'login'));
    ?>
    <div class="login-popup" id="displayWrapperAndIframe">
        <div class="guest-user-wrapper">
            <div class="guest-user-bg">
                <div class="row">
                    <h3>Log in or sign up to continue with your booking</h3>
                    <button type="button" class="button button--pay" data-toggle="login_modal" data-target="#login-tab-login">Log in</button>
                    <button type="button" class="button button--continue" data-toggle="login_modal" data-target="#login-tab-signup">Sign up</button>
                </div>
            </div>
        </div>

        <div id="login_popup_open" class="sectionOverlay"<?= $modal_open ? ' style="display: block;"' : '' ?>>
            <div class="overlayer"></div>
            <div class="screenTable">
                <div class="screenCell">
                    <div class="sectioninner guest-user zoomIn<?= $social_media_logins ? ' social' : '' ?><?= $offers_text ? ' has_offers_text' : '' ?>">
                        <a class="basic_close"><span class="icon_close" aria-hidden="true"></span></a>

                        <div class="popup-content guest-user<?= $social_media_logins ? ' social' : '' ?>">
                            <?php
                            $login_view = View::factory('login')
                                ->set('embedded',       true)
                                ->set('guest_redirect', isset($guest_redirect) ? $guest_redirect : '')
                                ->set('redirect',       $_SERVER['REQUEST_URI'])
                                ->set('alert',          isset($alert) ? $alert : '');
                            if (Settings::instance()->get('engine_enable_external_register') == '1') {
                                $login_view->set('org_industries', Model_Organisation::get_organisation_industries())
                                ->set('org_sizes', Model_Organisation::get_organisation_sizes())
                                ->set('job_functions',Model_Contacts3::get_job_functions());
                            }
                            echo $login_view;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
