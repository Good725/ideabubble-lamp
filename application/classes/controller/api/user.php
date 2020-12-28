<?php defined('SYSPATH') OR die('No Direct Script Access');

$GLOBALS['autologin'] = str_replace(array('www.', '.'), array('', '_'), $_SERVER['HTTP_HOST']) . '_autologin';
class Controller_Api_User extends Controller_Api
{

    public function before()
    {
        parent::before();

        /*if (!Model_Api::is_enabled('engine')) {
            throw new Exception('Not Enabled');
        }*/
    }

    public function after()
    {
        if (Auth::instance()->has_access('login_as')) {
            $this->response_data['has_login_as'] = true;
        }
        $login_as_return_email = Session::instance()->get('login_as_return_id');
        if ($login_as_return_email) {
            $this->response_data['login_as_return_email'] = $login_as_return_email;
        }

        parent::after();
    }

    public function action_list()
    {
        if (!Auth::instance()->has_access('login_as')) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Permission Denied');
        } else {
            $this->response_data['success'] = true;
            $this->response_data['users'] = DB::select('users.id', 'users.email', 'users.can_login', 'roles.role')
                ->from(array(Model_Users::MAIN_TABLE, 'users'))
                ->join(array(Model_Roles::MAIN_TABLE, 'roles'), 'inner')->on('users.role_id', '=', 'roles.id')
                ->where('users.deleted', '=', 0)
                ->execute()
                ->as_array();
        }
    }

    public function action_login_as()
    {
        if (!Auth::instance()->has_access('login_as')) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Permission Denied');
        } else {
            $currentUser = Auth::instance()->get_user();
            $user_id = $this->request->post('user_id');
            $modelUsers = new Model_Users;
            $user = $modelUsers->get_user($user_id);

            if (!$user) {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = __('Invalid user id');
            } else {
                if (Auth::instance()->logout()) {
                    $this->response_data['success'] = true;
                    $this->response_data['msg'] = '';
                    Auth::instance()->force_login($user['email'], true);
                    Session::instance()->set('login_as_return_id', $currentUser['email']);
                }
            }
        }
    }

    public function action_login_back()
    {
        $loginBackEmail = Session::instance()->get('login_as_return_id');
        if(!$loginBackEmail){
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Permission Denied');
        }

        if (Auth::instance()->logout()) {
            Auth::instance()->force_login($loginBackEmail, false);
            Session::instance()->set('login_as_return_id', null);
            $this->response_data['success'] = true;
            $this->response_data['msg'] = '';
        }
    }

    /***
     * @method POST
     * @param email required
     * @param password required
     *
     * @return
     * {
     *  success: bool
     *  msg: text
     *  user_id: int
     * }
     */
    public function action_login()
    {
        $data = $this->request->post();

        if (!isset($data['email'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Email');
            return;
        }
        if (!isset($data['password'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Password');
            return;
        }

        $auth = Auth::instance();
        if ($auth->login($data['email'], $data['password'], @$data['remember'] == 1)) {
            if ($auth->two_step_auth) {
                $code = Model_Users::two_step_auth_code_create($auth->two_step_auth['id']);
                Model_Users::two_step_auth_code_send($auth->two_step_auth, $code);
                $this->response_data['success'] = true;
                $this->response_data['msg'] = __('');
                $this->response_data['user_id'] = $auth->two_step_auth['id'];
                $this->response_data['step2_check'] = true;

            } else {
                $user = Auth::instance()->get_user();
                if ($user['email_verified']) {
                    $roles = new Model_Roles();
                    $role = $roles->get_role_data($user['role_id']);
                    if (@$role['allow_api_login'] == 1) {
                        $this->response_data['success'] = true;
                        $this->response_data['msg'] = __('Logged In');
                        $this->response_data['user_id'] = $user['id'];
                        $this->response_data['step2_check'] = false;
                        //on each login update membership status for organisation.
                        Model_Contacts3::organisation_membership_update($user['id']);
                    } else {
                        Cookie::set($GLOBALS['autologin'], false);
                        Auth::instance()->logout(true, true);
                        $this->response_data['success'] = false;
                        $this->response_data['msg'] = __('API Login not allowed');
                    }
                } else {
                    // If email hasn't been verified, we need log out user completely
                    Cookie::set($GLOBALS['autologin'], false);
                    Auth::instance()->logout(true, true);
                    $this->response_data['success'] = false;
                    $this->response_data['msg'] = __('Email Not Verified');
                }
            }
        } else {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Invalid email or password');
        }

    }

    public function action_step2()
    {
        $user_id = $this->request->post('user_id');
        $code = $this->request->post('code');
        if ($user_id == '') {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing user_id');
            return;
        }
        if ($code == '') {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing code');
            return;
        }

        $auth = Auth::instance();
        if ($auth->step2_login($user_id, $code)) {
            //on each login update membership status for organisation.
            Model_Contacts3::organisation_membership_update($user_id);
            $this->response_data['success'] = true;
            $this->response_data['msg'] = __('Logged In');
        } else {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Wrong authentication code');
        }
    }

    /***
     * @method POST
     * @return
     * {
     *  success: true
     *  msg: text
     * }
     */
    public function action_logout()
    {
        Cookie::set($GLOBALS['autologin'], false);
        Auth::instance()->logout(true, true);

        $this->response_data['success'] = true;
        $this->response_data['msg'] = __('Logged Out');
    }

    /***
     * @method POST
     * @param email required
     * @param password required
     * @param mpassword required
     *
     * @return
     * {
     *  success: bool
     *  msg: text
     *  user_id: int
     * }
     */
    public function action_register()
    {
        $post = $this->request->post();

        $registered = array('success' => true, 'error' => '');
        foreach (Controller_Admin_Login::$run_before_external_register as $call) {
            $registered = call_user_func($call, $post);
            if (!$registered['success']) {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = __('Error');
                break;
            }
        }

        if (!isset($post['email'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Email');
            return;
        }

        if (!isset($post['password'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Password');
            return;
        }

        if (!isset($post['mpassword'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Password Confirmation');
            return;
        }

        $roles = new Model_Roles();
        $user = new Model_Users();

        if ($user->check_email_used($post['email'])){
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Already Registered');
            return;
        }

        $role_id = null;
        $roles = new Model_Roles();
        if (isset($post['role_id'])) {
            $role = $roles->get_role_data($post['role_id']);
            if (@$role['allow_frontend_register'] != 1) {
                unset($post['role_id']);
            } else {
                $role_id = $post['role_id'];
            }
        } else if (isset($post['role'])) {
            $role = $roles->get_role_data($post['role']);
            if (@$role['allow_frontend_register'] == 1) {
                $role_id = $role['id'];
            }
        }

        if (!$role_id) {
            $role_id = $roles->get_id_for_role(Settings::instance()->get('website_frontend_register_role'));
        }
        
        $registered = $user->register_user(
            array(
                'email' => $post['email'],
                'password' => $post['password'],
                'mpassword' => $post['mpassword'],
                'role_id' => $role_id,
                'register_source' => 'api'
            )
        );

        foreach (Controller_Admin_Login::$run_after_external_register as $call) {
            call_user_func($call, $post, $registered);
        }

        $this->response_data['success'] = $registered['success'];
        if ($registered['success']) {
            $this->response_data['msg'] = __('Registered');
            $this->response_data['user_id'] = $registered['id'];
        } else {
            $this->response_data['msg'] = $registered['error'];
        }
    }

    /***
     * @method POST
     * @param verification required
     * @param password required
     * @param mpassword required
     * @return
     * {
     *  success: bool
     *  msg: text
     * }
     */
    public function action_resetpw()
    {
        $post = $this->request->post();

        if (!isset($post['validation'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Validation Code');
            return;
        }

        if (!isset($post['password'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Password');
            return;
        }

        if (!isset($post['mpassword'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Password Confirmation');
            return;
        }

        if ($post['password'] != $post['mpassword']) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Passwords do not match');
            return;
        }

        $user_details = Model_Users::get_user_by_validation($post['validation']);
        if ($user_details) {
            $password = array(
                'email'          => $user_details['email'],
                'email_verified' => 1,
                'mpassword'      => $post['mpassword'],
                'password'       => $post['password']
            );
            $user = new Model_Users();
            $result = $user->update_user_data($user_details['id'], $password);

            if ($result) {
                $this->response_data['success'] = true;
                $this->response_data['msg'] = __('Password has been set');
            } else {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = __('Unexpected Error');
            }
        } else {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Invalid Validation Code');
            return;
        }
    }

    /***
     * @method POST
     * @param email required
     *
     * @return
     * {
     *  success: bool
     *  msg: text
     * }
     */
    public function action_forgotpw()
    {
        $post = $this->request->post();

        if (!isset($post['email'])) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Missing Email');
            return;
        }

        $result = Model_Users::set_user_password_validation($post['email']);
        if (@$result['user_exists']) {
            $result['site_url'] = URL::site();
            $extra_targets = array(
                array('target_type' => 'EMAIL', 'target' => $post['email'], 'x_details' => 'to')
            );

            $messaging_model = new Model_Messaging;
            $messaging_model->send_template('reset_cms_password', '', NULL, $extra_targets, $result);

            $this->response_data['success'] = true;
            $this->response_data['msg'] = __('Check your inbox to reset password');
        } else {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Unknown user');
        }
    }

    /***
     * @method GET|POST
     *
     */
    public function action_profile()
    {
        $user = Auth::instance()->get_user();
        $user = Model_Users::get_user($user['id']);
        if (!$user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        $result = false;
        $post = $this->request->post();
        if (!empty($post)) {
            $post = html::clean_array($post);
            $model    = new Model_Users();
            $data = $user;

            if (@$post['name']) {
                $data['name'] = $post['name'];
            }

            if (@$post['surname']) {
                $data['surname'] = $post['surname'];
            }
            if (@$post['email']) {
                $data['email'] = $post['email'];
            }
            if (@$post['phone']) {
                $data['phone'] = $post['phone'];
            }
            if (@$post['address']) {
                $data['address'] = $post['address'];
            }
            if (@$post['avatar']) {
                $data['avatar'] = $post['avatar'];
            }
            if (@$post['use_gravatar']) {
                $data['use_gravatar'] = (@$post['use_gravatar'] == 1 OR $post['avatar'] == '') ? 1 : 0;
            }
            if (@$post['eircode']) {
                $data['eircode'] = $post['eircode'];
            }
            if (isset($post['timezone'])) {
                $data['timezone'] = $post['timezone'];
            }
            if (@$post['password']) {
                $data['password'] = $post['password'];
            } else {
                $data['password'] = '';
            }
            if (@$post['mpassword']) {
                $data['mpassword'] = $post['mpassword'];
            } else {
                $data['mpassword'] = '';
            }
            if (@$post['default_home_page']) {
                $data['default_home_page'] = $post['default_home_page'];
            }
            if (@$post['default_dashboard_id']) {
                $data['default_dashboard_id'] = isset($post['default_dashboard_id']) ? $post['default_dashboard_id'] : null;
            }
            if (@$post['auto_logout_minutes']) {
                $data['auto_logout_minutes'] = $post['auto_logout_minutes'];
            }
            if (@$post['user_column_profile']) {
                $data['user_column_profile'] = $post['user_column_profile'];
            }
            $data['date_modified'] = date('Y-m-d H:i:s');
            if (@$post['default_eprinter']) {
                $data['default_eprinter'] = @$post['default_eprinter'];
            }
            if (@$post['default_messaging_signature']) {
                $data['default_messaging_signature'] = @$post['default_messaging_signature'];
            }

            $result = $model->update_user_data($user['id'], $data);

            if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
                $imap_settings = @$post['imap'];
                if (@$imap_settings['username'] && @$imap_settings['host']) {
                    $imap_settings['deleted'] = 0;
                    $imap_settings['user_id'] = $user['id'];
                    $imap_exists = DB::select('*')
                        ->from('plugin_messaging_imap_accounts')
                        ->where('user_id', '=', $user['id'])
                        ->execute()
                        ->current();
                    if ($imap_exists) {
                        DB::update('plugin_messaging_imap_accounts')
                            ->set($imap_settings)
                            ->where('user_id', '=', $user['id'])
                            ->execute();
                    } else {
                        DB::insert('plugin_messaging_imap_accounts')
                            ->values($imap_settings)
                            ->execute();
                    }
                } else {
                    DB::update('plugin_messaging_imap_accounts')
                        ->set(array('deleted' => 1))
                        ->where('user_id', '=', $user['id'])
                        ->execute();
                }
            }

            foreach (Controller_Admin_Profile::$extraSections as $extraSection) {
                $extraSection->save($user['id'], $post);
            }

            Auth::instance()->reload_user_data();
        }

        $user = Model_Users::get_user($user['id']);
        $profile = array();
        $profile['id'] = $user['id'];
        $profile['email'] = $user['email'];
        $profile['name'] = $user['name'];
        $profile['surname'] = $user['surname'];
        $profile['country'] = $user['country'];
        $profile['timezone'] = $user['timezone'];
        $profile['county'] = $user['county'];
        $profile['address'] = $user['address'];
        $profile['eircode'] = $user['eircode'];
        $profile['address_2'] = $user['address_2'];
        $profile['address_3'] = $user['address_3'];
        $profile['phone'] = $user['phone'];
        $profile['mobile'] = $user['mobile'];
        $profile['company'] = $user['company'];
        $profile['avatar'] = $user['avatar'];
        $profile['use_gravatar'] = $user['use_gravatar'];
        $profile['signup_newsletter'] = $user['signup_newsletter'];
        $profile['registered'] = $user['registered'];
        $profile['default_home_page'] = $user['default_home_page'];
        $profile['default_dashboard_id'] = $user['default_dashboard_id'];
        $profile['datatable_length_preference'] = $user['datatable_length_preference'];
        $profile['auto_logout_minutes'] = $user['auto_logout_minutes'];
        $profile['heard_from'] = $user['heard_from'];
        $profile['default_eprinter'] = $user['default_eprinter'];
        $profile['default_messaging_signature'] = $user['default_messaging_signature'];

        foreach ($profile as $key => $value) {
            if ($value === null) {
                $profile[$key] = '';
            }
        }

        if (class_exists('Model_Event')) {
            $account = Model_Event::accountDetailsLoad($user['id']);
            $profile['account'] = array(
                'qr_scan_mode' => $account['qr_scan_mode'],
                'use_stripe_connect' => $account['use_stripe_connect'],
                'iban' => $account['iban'],
                'bic' => $account['bic'],
                //'stripe_auth' => $account['stripe_auth']
            );

            foreach ($profile['account'] as $key => $value) {
                if ($value === null) {
                    $profile['account'][$key] = '';
                }
            }
        }
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['profile'] = $profile;
        $this->response_data['result'] = $result;
    }

    public function action_stripe_connect_begin()
    {
        $stripeId = Settings::instance()->get('stripe_client_id');
        $urlParams = array(
            'response_type' => 'code',
            'client_id' => $stripeId,
            'scope' => 'read_write',
            'redirect_uri' => URL::site('/api/user/stripe_connect_pass'),
        );
        $url = 'https://connect.stripe.com/oauth/authorize?' . http_build_query($urlParams);
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['goto_url'] = $url;
    }

    public function action_stripe_connect_pass()
    {
        header('content-type: text/html; charset=utf-8');
        $params = $this->request->query();
        $html = '<html>
    <head>
        <script>window.location.href="uticket://' . http_build_query($params) . '";</script>
    </head>
    <body>
    <a href="uticket://success=' . http_build_query($params) . '">return to app</a>
    </body>
    </html>';
        echo $html;
        exit;
    }

    public function action_stripe_connect_complete()
    {
        $success = 0;
        $msg = '';
        $error = $this->request->query('error');
        if ($error != '') {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = 'Error authorizing stripe(' . $error . ')';
            $success = 0;
            $msg = $error;
        } else {
            $scope = $this->request->query('scope');
            $code = $this->request->query('code');
            $stripeId = Settings::instance()->get('stripe_client_id');
            $stripeSecret = Settings::instance()->get('stripe_test_mode') == 'TRUE' ?
                Settings::instance()->get('stripe_test_private_key') :
                Settings::instance()->get('stripe_private_key');

            if ($code) {
                $token_request_body = array(
                    'grant_type' => 'authorization_code',
                    'client_id' => $stripeId,
                    'code' => $code,
                    'client_secret' => trim($stripeSecret)
                );

                try {
                    $req = curl_init('https://connect.stripe.com/oauth/token');
                    if (!defined('CURL_SSLVERSION_TLSv1_2')) {
                        define('CURL_SSLVERSION_TLSv1_2', 6);
                    }
                    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($req, CURLOPT_POST, true);
                    curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                    curl_setopt($req, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

                    $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
                    $resp = json_decode(curl_exec($req), true);
                    curl_close($req);

                    if (isset($resp['access_token'])) {
                        $user = Auth::instance()->get_user();
                        Model_Event::accountDetailsSave(array(
                            'owner_id' => $user['id'],
                            'use_stripe_connect' => 1,
                            'stripe_auth' => json_encode($resp)
                        ));
                        $this->response_data['success'] = true;
                        $this->response_data['msg'] = 'Stripe connect completed';
                    }
                } catch (Exception $exc) {
                    $this->response_data['success'] = false;
                    $this->response_data['msg'] = 'Error authorizing stripe(' . $exc->getMessage() . ')';
                }
            } else {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = 'Error authorizing stripe. No Code Received';
            }
        }
    }

    public function action_stripe_disconnect()
    {
        $user = Auth::instance()->get_user();
        $user = Model_Users::get_user($user['id']);
        if (!$user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        $account = Model_Event::accountDetailsLoad($user['id']);
        if (!@$account['stripe_auth']['stripe_user_id']) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Connected');
            return;
        }

        require_once APPPATH . '/vendor/stripe/lib/Stripe.php';

        $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
        $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
        $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
        Stripe::setApiKey($stripe['secret_key']);

        $curl = curl_init('https://connect.stripe.com/oauth/deauthorize');
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($curl, CURLOPT_USERPWD, $stripe['secret_key']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('client_id' => Settings::instance()->get('stripe_client_id'), 'stripe_user_id' => $account['stripe_auth']['stripe_user_id']));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);
        if (@$result['stripe_user_id'] == $account['stripe_auth']['stripe_user_id'] || @$result['error'] == 'invalid_client') {
            $account['stripe_auth'] = '';
            $account['use_stripe_connect'] = 0;
            Model_Event::accountDetailsSave($account);
        }
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
    }

    public function action_avatar()
    {
        $user = Auth::instance()->get_user();
        $user = Model_Users::get_user($user['id']);
        if (!$user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $tmp_dir = Kohana::$cache_dir . '/' . time() . '-' . mt_rand(10000, 99999);
            mkdir($tmp_dir);
            $tmp_image = $tmp_dir . '/' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $tmp_image);
            $filename = 'user_' . $user['id'] . '.jpg';

            $media = new Model_Media();
            $preset = Model_Presets::get_preset_details('Avatars');
            $preset_wh_ratio = $preset['width_large'] / $preset['height_large'];

            $dimage = imagecreatefromstring(file_get_contents($tmp_image));
            $w = imagesx($dimage);
            $h = imagesy($dimage);
            imagedestroy($dimage);

            $image_wh_ratio = $w / $h;

            if ($preset_wh_ratio < 1) {
                $sw = $w;
                $sh = $h * $preset_wh_ratio;
            } else if ($preset_wh_ratio > 1) {
                $sh = $h;
                $sw = $w * $preset_wh_ratio;
            } else {
                if ($w < $h) {
                    $sw = $w;
                    $sh = $w * $preset_wh_ratio;
                } else {
                    $sh = $h;
                    $sw = $h * $preset_wh_ratio;
                }
            }
            $sx = 0;
            $sy = 0;
            if ($sw < $w) {
                $sx = ($w - $sw) / 2;
            }
            if ($sh < $h) {
                $sy = ($h - $sh) / 2;
            }

            $params = array();
            $params['imageSource'] = $tmp_image;
            $params['imageX'] = 0;
            $params['imageY'] = 0;
            $params['imageW'] = $w;
            $params['imageH'] = $h;
            $params['data']['selector_w'] = $sw;
            $params['data']['selector_h'] = $sh;
            $params['selectorX'] = $sx;
            $params['selectorY'] = $sy;
            $params['imageRotate'] = 0;
            $params['viewPortW'] = $w;
            $params['viewPortH'] = $h;
            $params['filename'] = $filename;

            $result = $media->cropzoom_save($params, $filename, $preset);

            DB::update(Model_Users::MAIN_TABLE)
                ->set(
                    array(
                        'use_gravatar' => 0,
                        'avatar' => $filename
                    )
                )
                ->where('id', '=', $user['id'])
                ->execute();
            unlink($tmp_image);
            rmdir($tmp_dir);
            $this->response_data['success'] = true;
            $this->response_data['msg'] = '';
            $this->response_data['result'] = $result;
        }
    }

    public function action_roles()
    {
        $roles = Model_Roles::get_all();

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['result'] =  array();
        foreach ($roles as $role) {
            if ($role['allow_api_register'] == 1) {
                $this->response_data['result'][] = array(
                    'id' => $role['id'],
                    'role' => $role['role']
                );
            }
        }
    }

    public function action_upload_avatar()
    {
        $user = Auth::instance()->get_user();
        $user = Model_Users::get_user($user['id']);
        if (!$user) {
            $this->response->status(403);
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Not Logged In');
            return;
        }

        $plugin_model = new Model_Media();
        reset($_FILES);
        $file = current($_FILES);
        $errors = array();
        $preset = Model_Presets::get_preset_details('Avatars');
        $media_preset = array();
        foreach($preset as $field => $value) {
            $media_preset['preset_' . $field] = $value;
        }
        $directory = 'photos';
        $directories = array($directory);
        if ($directory == 'photos') {
            $directories = array('content', 'avatars');
        }
        $original_filename = $file['name'];
        //$file['name'] = Model_Media::get_filename_suggestion($file['name'], $directories);
        $file['name'] = $user['id'] . '-' . date('YmdHis') . ' - ' . $file['name'];
        $validation_errors = $plugin_model->validate_media_item($file, $media_preset, $ajax = true);

        $media_id = null;
        if (sizeof($validation_errors) == 0) {
            $media_id = @$plugin_model->add_item_to_media($file, $media_preset);
        } else {
            $errors[] = $validation_errors;
        }

        if (count($errors) > 0) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = implode("\r\n", $validation_errors);
            $this->response_data['avatar'] = null;
        } else {
            $media_project_folder = Kohana::$config->load('config')->project_media_folder;
            $shared_media = (!empty($media_project_folder) AND is_string($media_project_folder) AND $media_project_folder !== "") ? "/shared_media/".Kohana::$config->load('config')->project_media_folder : "";

            $data = array(
                'files' => array($file['name']),
                'original_filenames' => array($original_filename),
                'errors' => $errors,
                'shared_media' => $shared_media,
                'media_id' => $media_id
            );

            DB::update(Model_Users::MAIN_TABLE)
                ->set(array('avatar' => $file['name'], 'use_gravatar' => 0))
                ->where('id', '=', $user['id'])
                ->execute();
            $this->response_data['success'] = true;
            $this->response_data['msg'] = '';
            $this->response_data['avatar'] = $data;
        }
    }
    
    public function action_role()
    {
        $user = Auth::instance()->get_user();
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $mr = new Model_Roles();
        $this->response_data['role'] = $mr->get_role_data($user['role_id']);
        $this->response_data['permissions'] = Model_Roles::permissions($user['role_id']);
    }

    public function action_noop()
    {
        $user = Auth::instance()->get_user();
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['user_id'] = $user['id'];
    }

    private function _organisation_membership_update($user_id) {

        //on each login update membership status for organisation.
        $contact = Model_Contacts3::get_linked_contact_to_user($user_id);
        if (!empty($contact)) {
            $contact = new Model_Contacts3($contact['id']);
            $organisation = $contact->get_linked_organisation();
            if ($organisation->get_id() > 0) {
                if (Settings::instance()->get('organisation_api_control_membership')
                    && Settings::instance()->get('organisation_integration_api')
                    && Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                    $membership_status = false;
                    $cds = new Model_CDSAPI();
                    $cds_account = $cds->get_account($organisation->get_id());
                    if (!empty($cds_account)) {
                        $membership_status = @$cds_account['sp_membershipstatus'];
                    }
                    $organisation->update_membership_for_organisation($membership_status);
                }

            }
        }
    }
}