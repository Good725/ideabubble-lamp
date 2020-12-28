<?php defined('SYSPATH') OR die('No Direct Script Access');
/**
 * Login/Logout Controller.
 *
 * @package Cms
 */
$GLOBALS['autologin'] = str_replace(array('www.', '.'), array('', '_'), $_SERVER['HTTP_HOST']) . '_autologin';
Class Controller_Admin_Login extends Controller_Template
{

	public static $run_after_external_register = array();
	public static $run_before_external_register = array();
	public $template = 'template-login';

	function before() {
		$this->response->headers(array(
				'Cache-Control' => 'no-cache, no-store, must-revalidate',
				'Pragma' => 'no-cache',
				'Expires' => '0'
		));

		$external_user_id =  @$_POST['external_user_id'];
        $external_provider_id = @$_POST['external_provider_id'];

		if($external_provider_id == 2){
            $external_user_id = Model_Users::get_external_provider_user_id($external_provider_id,$external_user_id );
        }

        if( !empty( $external_user_id ) AND !empty($external_provider_id) ) {
            $result = Model_Users::check_external_provider_data($external_provider_id, $external_user_id);
            if ($result[ 'disabled' ] == 0 AND is_null($result[ 'user_id' ])) {
                $this->request->post('action','register');
                $this->request->action('register');
            }
        }

		parent::before();

		I18n::init_user_lang();

		$title = 'CourseCo :: Login';
		View::bind_global('title', $title);


		$scripts = array(
            '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2.js"></script>',
            '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2-en.js"></script>'
		);

		$this->template->scripts = $scripts;

        $this->template->styles  = array(
            URL::get_engine_assets_base() . 'css/validation.css' => 'screen',
        );
	}

	/**
	 * Logs the user out and redirects them to the sites home page
	 */
	Public function action_logout() {
		// Turn off auto rendering template for the logout function
		$this->auto_render = FALSE;

		// Delete the login redirect value
		Session::instance()->delete('login_redirect');
		$user = Auth::instance()->get_user();

		if (class_exists('Model_Checkout'))
		{
			Model_Checkout::empty_cart();
		}

		Session::instance()->delete('allow_attendance_edit');

		// Perform the logout
		if (Auth::instance()->logout())
		{
			$activity = new Model_Activity();
			$activity->set_item_type('user')->set_action('logout')->set_user_id($user['id'])->save();

            if ($this->request->query('redirect')) {
                $redirect = $this->request->query('redirect');
            }
            else {
                $redirect = $this->request->query('auto') ? '/admin/login?auto=yes' : '/';
            }
            if (!URL::is_internal($redirect)) {
                $redirect = '/';
            }
			$this->request->redirect($redirect);
		}

	}

	function after()
	{
		// Load the messages from the IbHelper.
		$messages = IbHelpers::get_messages();

		// If there are messages
		if ($messages)
		{
			// Add the message to the alert string if it exists
			if (isset($this->template->body->alert))
			{
				$this->template->body->alert = $this->template->body->alert . $messages;
			}
			// Else create an alert string
			else
			{
				$this->template->body->alert = $messages;
			}
		}
        $this->template->skip_comments_in_beginning_of_included_view_file = true; //Exclude comments in this file, prevent IE issues when the first line in the website is a comment
		parent::after();

	}

    /**
     * New user registration form & processing
     *
     * @return void
     */

	public function action_user_req() {
		$this->template->body = Request::factory('admin/userreq/user_req/')->post($this->request->post())->execute()->body();
	}

	public function action_user_req_ok() {
		$this->template->body = Request::factory('admin/userreq/user_req_ok/')->post($this->request->post())->execute()->body();
	}

    public function action_forgot_password()
    {
        $this->template->body = View::factory('forgot_password');
    }

    public function action_send_reset_email()
    {
        $post = $this->request->post();

        if(empty($post['email']))
        {
            $alert = IbHelpers::alert('The email address entered is blank.', 'error popup_box');
            $this->template->body = View::factory('forgot_password')->bind('alert',$alert);
            return 0;
        }

		$post['email'] = htmlentities($post['email']); // Clean user input before it goes into the message body

		if ( ! filter_var($post['email'], FILTER_VALIDATE_EMAIL))
        {
            $alert = IbHelpers::alert(__('Please enter a valid email address.'), 'danger popup_box');
            $this->template->body = View::factory('forgot_password')->bind('alert',$alert)->bind('email',$post['email']);;
            return 0;
        }

		if (Settings::instance()->get('cms_captcha_enabled')) {
			$formprocessor_model = new Model_Formprocessor();
			if (!$formprocessor_model->captcha_check($post)) {
				$alert = IbHelpers::alert('The captcha entered is incorrect.', 'error popup_box');
				if (!isset($post['redirect'])) {
					$this->template->body = View::factory('forgot_password')->bind('alert', $alert)->bind('email',
							$post['email']);
				} else {
					$this->request->redirect($post['redirect']);
				}
				return 0;
			}
		}

        $result        = Model_Users::set_user_password_validation($post['email']);

        $login_max_failed_attempts_from_ip = Settings::instance()->get('login_max_failed_attempts_from_ip');
        $login_max_failed_attempts_duration_minutes = Settings::instance()->get('login_max_failed_attempts_duration_minutes');
        $failed_cnt = DB::select(DB::expr('count(*) as cnt'))
            ->from('engine_loginlogs')
            ->where('ip_address', '=', ip2long($_SERVER['REMOTE_ADDR']))
            ->and_where('success', '=', 0)
            ->and_where('time', '>=', time() - (60 * $login_max_failed_attempts_duration_minutes))
            ->execute()
            ->get('cnt');
        // log to failed login attempt if wrong email entered
        if ($failed_cnt >= $login_max_failed_attempts_from_ip) {
            $nouser = array('id' => 0);
            $error_id = Model_Errorlog::save(null, "SECURITY");
            $true = false;
            //do not set any message indicating email is wrong
            $this->template->body = View::factory('reset_password')->bind('info', $true)->bind('user_exists', $nouser);
            return;
        }

		if (empty($result['user_exists']))
		{
            DB::insert('engine_loginlogs')
                ->values(
                    [
                        'ip_address' => ip2long($_SERVER['REMOTE_ADDR']),
                        'email' => $post['email'],
                        'time' => time(),
                        'success' => 0,
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'session' => session_id()
                    ]
                )->execute();
            $true = false;
            //do not set any message indicating email is wrong
            $this->template->body = View::factory('reset_password')->bind('info', $true);
			return;
		}

		$use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');
		$email_body    = View::factory('email/reset_cms_email', array('form' => $result))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

		if ($use_messaging) // Use the messaging plugin
		{
			$extra_targets = array(
				array('id' => NULL,'template_id' => NULL,'target_type' => 'EMAIL','target' => $post['email'],'x_details' => 'to','date_created' => NULL)
			);

			$result['site_url'] = URL::site();

			$messaging_model = new Model_Messaging;
			$messaging_model->send_template('reset_cms_password', '', NULL, $extra_targets, $result);
		}
		else // Use the notifications plugin
		{
			$event_id = Model_Notifications::get_event_id('reset_cms_password');
			if ($event_id !== FALSE AND $result['user_exists'])
			{
				$url = parse_url(URL::site());
				$model = new Model_Notifications($event_id);
				$model->set_from('no-reply@'.str_replace('www.','',$url['host']));
				$model->send_to($post['email'], $email_body, 'Password Reset Confirmation');
				unset($result['validation']);
			}
		}




        if(!$result['user_exists'] AND !isset($post['redirect']))
        {
            $alert = IbHelpers::alert('This user does not exist.', 'error');
            $this->template->body = View::factory('forgot_password')->bind('alert',$alert);
            return 0;
        }

        $true = true;
        if(!isset($post['redirect']))
        {
            $this->template->body = View::factory('reset_password')->bind('info', $true)->bind('user_exists',$result['user_exists']);
        }
        else
        {
            $this->request->redirect($post['redirect']);
        }
    }

    public function action_reset_password_form()
    {
        $id = $this->request->param('id');
        $this->template->body = View::factory('reset_password')->bind('id',$id);
    }

    public function action_reset()
    {
        $post = $this->request->post();
        $redirect = isset($post['redirect']) ? $post['redirect'] : '';
        $user_details = Model_Users::get_user_by_validation($post['validation']);

        $error = '';
        if (strlen($post['password']) < 8) {
            $error = 'This password is too short, please enter a password with a minimum of 8 characters.';
        } else if (!preg_match('/[A-Z]/', $post['password'])) {
            $error = 'This password must contain at least one capital letter.';
        } else if (!preg_match('/[a-z]/', $post['password'])) {
            $error = 'This password must contain at least one lower case letter.';
        } else if (!preg_match('/\d/', $post['password'])) {
            $error = 'This password must contain at least one numerical digit.';
        }
        if ($error != '') {
            IbHelpers::set_message($error, 'error popup_box');
            $reset_link = $this->request->post('reset_url');
            $redirect = $reset_link ? $reset_link : $redirect;
            $this->request->redirect($redirect);
        }

        $password = array(
            'email'          => $user_details['email'],
            'email_verified' => 1,
            'mpassword'      => $post['mpassword'],
            'password'       => $post['password'],
			'status'         => 1,
			'can_login'      => 1
        );
        $user = new Model_Users();
        $result = $user->update_user_data($user_details['id'],$password);


		if ( ! $result)
		{
			$reset_link = $this->request->post('reset_url');
			$redirect = $reset_link ? $reset_link : $redirect;
			$this->request->redirect($redirect);
		}
        if ($redirect != '')
        {
            $this->request->redirect(URL::site().$redirect);
        }
        else
        {
            $this->template->body = View::factory('login');
        }
    }

	public function action_payment()
	{
		// If the project has a payment login form view, display it
		if (Kohana::find_file('views', 'payment_login'))
		{
			$this->template->body = View::factory('payment_login');
		}
		// Otherwise redirect to the CMS
		else
		{
			$this->request->redirect('/admin');
		}
	}

	public function action_register($action=null)
	{
		$post = $this->request->post();

        $redirect = $this->request->query('redirect');
        $redirect = $redirect ? $redirect : $this->request->post('redirect');
        $redirect = $redirect ? $redirect : '/';
        $redirect = urldecode($redirect);

        $post['redirect'] = $redirect;
        $organisation_data = array();
        //when registering an org we use database to store password, because on second stane it is unsafe to send it back to form
        if(!empty($post['signup'])) {
            $organisation_data = Model_Contacts3::get_temporary_signup_data($post['signup']);
        }
        if (empty($post['password']) && !empty($organisation_data)) {
            $post['password'] = $organisation_data['password'];
        }

        $this->template->redirect = $redirect;

        $external_provider_id = @$post['external_provider_id'];
        $external_user_id = @$post['external_user_id'];

        if( !empty($external_provider_id) ){
            $external_user_id = Model_Users::get_external_provider_user_id($external_provider_id, $external_user_id);
        }

        if( !empty( $external_user_id ) AND !empty($external_provider_id) ){
            $result = Model_Users::check_external_provider_data($external_provider_id,$external_user_id);
            if( $result['disabled']==0 AND !is_null($result['provider_user_id'])){
                return $this->action_index();
            }
        }

		$alert = '';

        $ac = ($action) ? $action : @$post['action'];
		if ($ac == 'register')
        {
			if ( ! $this->check_recaptcha())
            {
				$alert = 'Select captcha option please.';
			}
            else
            {
				try {
					$email_verified = false;
					Database::instance()->begin();
					$registered = array('success' => true, 'error' => '');
					if (Model_Plugin::is_enabled_for_role('Administrator', 'dcs')) {
						$dcs_contact = Model_DCS::check_existing_dcs_contact($post);
						if (!$dcs_contact) {
							$registered['success'] = false;
							$registered['error'] = __('DCS data not found!');
						}
					}
					if ($registered['success'])
					foreach (self::$run_before_external_register as $call) {
						$registered = call_user_func($call, $post);
						if (@$registered['email_verified']) {
							$email_verified = 1;
						}
						if (!$registered['success']) {
							$this->request->redirect($registered['redirect']);
							break;
						}
					}

					if ($registered['success']) {
						if ($email_verified) {
							$post['email_verified'] = 1;
						}
                        if (!empty($organisation_data) && Settings::instance()->get('engine_enable_organisation_signup_flow')) {
                            $post['email_verified'] = 1;
                        }
						$users = new Model_Users();
						//Fix for one password field form

                        $post['mpassword'] = isset($post['mpassword']) ? $post['mpassword'] : $post['password'];
						$roles = new Model_Roles();

                        $existing_contact = new Model_Contacts3(Model_Contacts3::get_by_email($post['email'])['id'] ?? '');
                        $existing_contact_type = new Model_Contacts3_Type($existing_contact->get_type());

                        // unset($post['role']); // Model_Contacts3::create_contact_for_external_register() needs to be updated to not rely on this.
                        if (isset($post['role_id'])) {
                            $role = $roles->get_role_data($post['role_id']);
                            if (@$role['allow_frontend_register'] != 1) {
                                unset($post['role_id']);
                            }
                        } else if (isset($post['role']) && @$post['contact-type'] !== 'organisation') {
							$role = $roles->get_role_data($post['role']);
							if (@$role['allow_frontend_register'] == 1) {
                                $post['role_id'] = $role['id'];
                                $post['role'] = $role['role'];
                            }
						} else if (Settings::instance()->get('engine_enable_external_register') == '1' && @$post['contact-type'] == 'organisation') {
                            $post['role_id'] = $roles->get_id_for_role('Org rep');
                            $post['role'] = 'Org rep';
                        } else if (!empty($existing_contact->get_roles()) || $existing_contact_type->name == 'org_rep') {
                            $contact_roles = $existing_contact->get_roles();

                            // If the existing contact does not have a role, but is type "org_rep", also make "org_rep" their role.
                            if (!empty($contact_roles)) {
                                $contact_role = new Model_Contacts3_Role($contact_roles[0]);
                                $post['role_id'] = $contact_roles[0];
                                $post['role'] = $contact_role->name;
                            } else {
                                $post['role_id'] = $roles->get_id_for_role('Org rep');
                                $post['role'] = 'Org rep';
                            }
                        } else {
                            $post['role_id'] = $roles->get_id_for_role(Settings::instance()->get('website_frontend_register_role'));
                            $post['role'] = Settings::instance()->get('website_frontend_register_role');
                        }
                        $mu = new Model_Users();
                        $euser = $mu->get_user_by_email($post['email']);

                        // If the user already exists, don't change their role.
                        if (!empty($euser['id'])) {
                            $post['role_id'] = $euser['role_id'];
                            $post['role'] = $roles->get_role_data($euser['role_id'])['role'];
                        }

						if (@$post['invite_member'] && @$post['invite_hash'] && $euser) {
							if (Model_Contacts3::invite_accept(@$post['invite_member'], @$post['invite_hash'])) {
								$mu = new Model_Users();
								$euser = $mu->get_user_by_email($post['email']);
								$euser['email_verified'] = 1;
								$euser['role_id'] = $post['role_id'];
								$euser['password'] = $post['password'];
								$euser['mpassword'] = $post['mpassword'];
								$mu->update_user_data($euser['id'], $euser);
                                $registered = array('success' => true, 'error' => null, 'id' => $euser['id'], 'data' => $euser);
								IbHelpers::set_message(__('You have joined successfully'), 'info');
							}
						} else if (@$post['validate'] != '') {
							$registered = $users->validate_user($post);
						} else {
							$registered = $users->register_user($post);
						}

                        if (!Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') && $registered['success']) {

							$dcs_link = false;
							if (Model_Plugin::is_enabled_for_role('Administrator', 'dcs')) {
								if (@$post['dcs_family_id']) {
									$dcs_linked = Model_DCS::link_existing_dcs_contact_to_user($post, $registered['id']);
								}
							}
							if (!$dcs_linked) {
								$contact = new Model_Contacts();
								$contact->set_title('');
								$contact->set_first_name(isset($post['name']) ? $post['name'] : '');
								$contact->set_last_name(isset($post['surname']) ? $post['surname'] : '');
								$contact->set_email($post['email']);
								$contact->set_address1('');
								$contact->set_address2('');
								$contact->set_address3('');
								$contact->set_address4('');
								$contact->set_mobile('');
								$contact->set_notes('');
								$contact->test_existing_email = false;
								$contact->set_mailing_list('Parent/Guardian');
								$contact->set_permissions(array($registered['id']));
								$contact->save();
								$contact_id = $contact->get_id();

								$family_id = Model_Families::set_family('', $post['email'], 1, 0, $contact_id);
								Model_Families_Members::add_family_member($family_id, $contact_id, 'Parent');
							}
                        }

                        if (@$post['invite_member'] && @$post['invite_hash'] && $euser) {

                        } else {
                            foreach (self::$run_after_external_register as $call) {
                                $result = call_user_func($call, $post, $registered);
                            }
                        }
					}
					Database::instance()->commit();
				} catch (Exception $exc) {
					Database::instance()->rollback();
					throw $exc;
				}

				if ($registered['success'])
                {
					if (isset($post['signup_newsletter']) && Settings::instance()->get('mailchimp_list_id') != '' && Settings::instance()->get('mailchimp_apikey') != '') {
						$mc = new Mailchimp();
						$mc->add_to_list($post['email'], 'subscribed', $_SERVER['REMOTE_ADDR']);
					}

					if (@$post['validate'] != ''
                        || @$registered['verified']
                        || $email_verified
                        || (Settings::instance()->get('engine_enable_organisation_signup_flow') && $post['role'] == 'Org rep')) {
						$message = __('Successfully registered.<br />You can login now.');
					} else {
						$message = __('Nearly there!<br>') .
								__('We have sent an email to ' . $post['email'] . ', have a look for it, click the link to activate your account ') .
								"<br>Didn't get the email?  " . ' <a href="/admin/login/resend_verification_email/' . $registered['id'] . '" class="text-nowrap"><strong>' . __('Please  re-send it') . '</strong></a>';
					}
                    IbHelpers::set_message($message, 'success popup_box');

                    $redirect .= (parse_url($redirect, PHP_URL_QUERY) ? '&' : '?').'registered=success';

					$this->request->redirect($redirect);
                }
                else if ( ! empty($registered['error']))
                {
                    $alert = $registered['error'];
                }
            }
		}

        if ($alert) {
            Ibhelpers::set_message($alert, 'danger popup_box');
        }


        if (!empty($registered['error']) && (strpos($registered['error'], 'already a customer') != -1
                || strpos($registered['error'], 'account already exists')!= -1) ) {
            $this->template->body = View::factory('login')->set('mode', 'login')->set('redirect', $redirect);
        } else {
            $this->template->body = View::factory('login')->set('mode', 'signup')->set('redirect', $redirect);
        }

        if (Settings::instance()->get('engine_enable_external_register') == '1') {
            $this->template->body->org_industries = Model_Organisation::get_organisation_industries();
            $this->template->body->org_sizes = Model_Organisation::get_organisation_sizes();
            $this->template->body->job_functions = Model_Contacts3::get_job_functions();
        }
        $this->template->body->alert = IbHelpers::get_messages();
	}

    public function action_ajax_get_organisations()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->post();
        $api_turned_on = Settings::instance()->get('organisation_integration_api');
        $organisation_data = array();
        if (!empty($filters['signup'])) {
            $organisation_data = Model_Contacts3::get_temporary_signup_data($filters['signup']);
        }
        $organisation_data_type = 'local';
        if (!empty($organisation_data) && !empty($organisation_data['domain_is_blacklisted'])) {
            $organisations = array();
        } else {
            if ($api_turned_on) {
                //check if organization is present in CDS (IBEC Member = true)
                $cds_api = new Model_CDSAPI();
                if (!empty($filters['domain_name'])) {
                    $organisations = array();
                    if ( Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                        $organisations = $cds_api->search_accounts('sp_homepage', 'www.' . $filters['domain_name'], true);
                    }
                    if (!empty($organisations)) {
                        $organisation_data_type =  'api';
                    }
                } elseif (!empty($filters['name'])) {
                    $organisations = array();
                    if ( Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                        $organisations = $cds_api->search_accounts('name', $filters['name']);
                    }
                    if (!empty($organisations)) {
                        $organisation_data_type =  'api';
                    }
                } else {
                    $organisations = array();
                }
            } else {
                //if API integration is turned off, use scenario without integration
                $organisations =  $this->_get_local_organizations($filters);
            }
        }
        $organisations_datatables = array();
        if (!empty($organisations)) {
            if ($api_turned_on && $organisation_data_type == 'api') {
                foreach ($organisations as $organisation) {
                    $org_data = array();
                    $org_data['id'] = !empty($organisation['synced_value']) ? $organisation['synced_value'] : $organisation['accountid'];
                    $org_data['synced'] = !empty($organisation['synced_value']) ? 1 : 0;
                    $org_data['name'] = $organisation['name'];
                    $address = '';
                    $address .= $organisation['address1_line1'];
                    if (!empty($organisation['address1_line2'])) {
                        $address .= ',' . $organisation['address1_line2'];
                    }
                    if (!empty($organisation['address1_line3'])) {
                        $address .= ',' . $organisation['address1_line3'];
                    }
                    if (!empty($organisation['address1_city']))  {
                        $address .= ',' . $organisation['address1_city'];
                    }
                    if (!empty($organisation['address1_county']))  {
                        $address .= ',' . $organisation['address1_county'];
                    }
                    if (!empty($organisation['address1_country']))  {
                        $address .= ',' . $organisation['address1_country'];
                    }
                    $org_data['address'] = $address;
                    $organisations_datatables[] = $org_data;
                }
            } else {
                foreach ($organisations as $organisation) {
                    $org_data = array();
                    $org_data['id'] = $organisation['id'];
                    $org_data['name'] = $organisation['first_name'];
                    $org_data['synced'] = true;
                    if (!empty($organisation['residence'])) {
                        $residence = new Model_Residence($organisation['residence']);
                        $org_data['address'] = $residence->get_address1();
                    } else {
                        $org_data['address'] = '';
                    }
                    $organisations_datatables[] = $org_data;
                }
            }

        }
        $this->response->body(json_encode($organisations_datatables));
    }

    private function _get_local_organizations ($filters = array()) {
        if (empty($filters['name']) && empty($filters['domain_name'])) {
            $organisations = Model_Contacts3::search(array(
                'type' => Model_Contacts3::find_type('Organisation')['contact_type_id'],
                'delete'=> 0,
                'publish' => 1));
        } elseif(empty($filters['domain_name'])) {
            $organisations = Model_Contacts3::search(array(
                'first_name' => $filters['name'],
                'type' => Model_Contacts3::find_type('Organisation')['contact_type_id'],
                'delete' => 0,
                'publish' => 1));
        } elseif(empty($filters['name'])) {
            $organisations = Model_Contacts3::search(array(
                'domain_name' => $filters['domain_name'],
                'type' => Model_Contacts3::find_type('Organisation')['contact_type_id'],
                'delete'  =>0,
                'publish' =>1
            ));
        } else {
            $organisations = Model_Contacts3::search(array(
                'first_name' => $filters['name'],
                'domain_name' => $filters['domain_name'],
                'type' => Model_Contacts3::find_type('Organisation')['contact_type_id'],
                'delete' => 0,
                'publish' => 1));
        }

        return $organisations;
    }

    public function action_ajax_get_organisation() {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $request = $this->request->post();
        $result = array();
        $api_turned_on = Settings::instance()->get('organisation_integration_api');
        if ($api_turned_on) {
            if (!empty($request['id'])) {
                $cds_api = new Model_CDSAPI();
                if (!empty($request['synced'])) {
                    if ( Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                        $organisation = $cds_api->get_account($request['id']);
                    } else {
                        $organisation = array();
                    }
                } else {
                    if ( Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                        $organisation = $cds_api->get_account_by_remote_id($request['id']);
                    } else {
                        $organisation = array();
                    }
                }
                $county_code = $organisation['sp_countycode'];
                $county_name = $organisation['address1_county'];
                if (!empty($county_code)) {
                    $county = Model_Cities::get_counties(trim($county_code), 'code');
                } else {
                    $county = '';
                }

                if (!empty($county)) {
                    $county = reset($county);
                }
                $country_code = @$organisation['sp_countrycode'];
                $countries = Model_Country::get_countries(3);
                if (!empty($country_code) && array_key_exists($country_code, $countries)) {
                    $country  = $countries[$country_code]['id'];
                } else {
                    $country = $organisation['address1_country'];
                }

                $domain = parse_url($organisation['sp_homepage']);
                $domain_name = !empty($domain['host']) ? $domain['host'] : $domain['path'];
                $domain_name = str_replace('www.','', $domain_name);
                $result['id']              = !empty($organisation['synced_value']) ? $organisation['synced_value'] : $organisation['accountid'];
                $result['name']            = $organisation['name'];
                $result['domain_name']     = $domain_name;
                $result['address1']        = trim($organisation['address1_line1']);
                $result['address2']        = trim($organisation['address1_line2']);
                $result['address3']        = trim($organisation['address1_line3']);
                $result['city']            = $organisation['address1_city'];
                $result['postcode']        = $organisation['address1_postalcode'];
                $result['county']          = !empty($county) && is_array($county) ? $county['id'] : $county;
                $result['country']         = $country;
                $result['synced']          = $organisation['synced_value'];
            }
        } else {
            if (!empty($request['id']) && is_numeric($request['id'])) {
                $organisation = Model_Organisation::get_org_by_contact_id($request['id']);
                $contact_obj = $organisation->get_contact();
                $contact = $contact_obj ->get_instance();
                $address = $contact_obj->get_address()->get_instance();
                $result['id']          = $contact['id'];
                $result['name']        = $contact['first_name'];
                $result['domain_name'] = $contact['domain_name'];
                $result['address1']    = $address['address1'];
                $result['address2']    = $address['address2'];
                $result['address3']    = $address['address3'];
                $result['city']        = $address['town'];
                $result['postcode']    = $address['postcode'];
                $result['county']      = $address['county'];
                $result['country']     = $address['country'];
                $result['synced']      = true;
            }
        }
        $this->response->body(json_encode($result));
    }

    public function action_ajax_save_organisation_data(){
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $request = $this->request->post();

        if (empty($request['domain_name'])) {
            $error_message = 'Email must not be empty';
            $this->response->body(json_encode(array('result' => 'error', 'message' => $error_message)));
            IbHelpers::set_message($error_message, 'warning popup_box');
            return true;
        }
        $domain_is_blacklisted = Model_Contacts3::is_blacklisted_domain($request['domain_name']);
        $request['domain_is_blacklisted'] = $domain_is_blacklisted;
        $request['signup'] = md5($request['name'].time());
        Model_Contacts3::save_temporary_signup_data($request['signup'], $request);
        Session::instance()->set('login_redirect', $request['return_url']);
        $link = URL::site('admin/login/?signup=' . $request['signup']);

        $parameters = [
            'link' => $link,
            'name' => $request['first_name'] . ' ' . $request['last_name'],
            'contactfirstname' => $request['first_name'],
            'contactlastname'  => $request['last_name'],
        ];

        if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
            $messaging = new Model_Messaging();
            $messaging->get_drivers();
            $messaging->send_template(
                'user-email-verification',
                null,
                null,
                array(
                    array(
                        'target_type' => 'EMAIL',
                        'target' => $request['email']
                    )
                ),
                $parameters
            );
        }
        $message = __('Nearly there!<br>') .
            __('We have sent an email to ' . $request['email'] . ', have a look for it, click the link to finish registration ');
        IbHelpers::set_message($message, 'success popup_box');

        echo json_encode(array(
            'result' => 'success',
            'blacklisted' => $domain_is_blacklisted,
            'signup'=> $request['signup']));
        exit;
    }

    public function action_ajax_get_counties() {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $request = $this->request->post();
        if (empty($request) || empty($request['country'])) {
            return false;
        }
        $country = Model_Country::get_country_code($request['country'], 3);
        if (empty($country)) {
            return false;
        }
        $county = array();
        $counties = Model_Cities::get_counties($country['alpha3'], 'country_code');
        if (!empty($request['county'])) {
            $county = Model_Cities::get_counties($request['county'], 'code');
            if (!empty($county)) {
                $county = reset($county);
            }
        }
        $this->response->body(json_encode(array('result' => 'success','counties' => $counties, 'county' => $county)));

    }

    public function action_ajax_check_signup_param(){
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $request = $this->request->post();
        if (empty($request['signup'])) {
            $error_message = 'Empty signup session';
            echo (json_encode(array('result' => 'error', 'message' => $error_message)));
            IbHelpers::set_message($error_message, 'warning popup_box');
            return false;
        }
        $organisation_data = Model_Contacts3::get_temporary_signup_data($request['signup']);
        if (empty($organisation_data)
            && !isset($organisation_data['signup'])
            && empty($organisation_data['signup'])) {
            $error_message = 'Signup session expired';
            IbHelpers::set_message($error_message, 'warning popup_box');
            echo (json_encode(array('result' => 'error', 'message' => $error_message)));
        } elseif($organisation_data['signup'] != $request['signup']) {
            $error_message = 'Signup session expired';
            IbHelpers::set_message($error_message, 'warning popup_box');
            echo (json_encode(array('result' => 'error', 'message' => $error_message)));
        } else {
            $result = $organisation_data;
            $result['result'] = 'success';
            unset($result['password']);
            echo (json_encode($result));
        }
        return false;
    }

	/*
	 * Payment login form
	 */

	public function action_index()
	{
        $return = array();
        $return['success'] = 0;
		$redirect = $this->request->query('redirect');
		$redirect = $redirect ? $redirect : $this->request->post('redirect');
		$redirect = $redirect ? $redirect : Session::instance()->get('login_redirect');
		// check if the user is already logged in and redirect to their default home page
		if (Auth::instance()->logged_in())
		{
			if (!$redirect)
			{
				$redirect = $redirect ? URL::site($redirect) : $this->get_default_page();
			}

			$this->request->redirect($redirect);
		}

		$data = $this->request->post();

		$external_provider_id = @$data['external_provider_id'];
        $external_user_id = @$data['external_user_id'];


        if( !empty($external_provider_id) ){
            $external_user_id = Model_Users::get_external_provider_user_id($external_provider_id,$external_user_id);
        }

        // if logged in with external provider
        if( !empty( $external_user_id ) AND !empty($external_provider_id) ){
            $result = Model_Users::check_external_provider_data($external_provider_id,$external_user_id);

            if( $result['disabled']==0 AND !is_null($result['user_id'])){
                $users_model = new Model_Users();
                $ext_user_data = $users_model->get_user($result['user_id']);

                // For get request init language field to default value set by client
                $data['lang'] = I18n::get_default_language();

                // Set default language for app (will store it in cookie)
                I18n::set_default_language(@$data['lang']);

                $remember = TRUE;

                $post = Validation::factory($_POST);

                if(Auth::instance()->login_with_external_provider($ext_user_data,$remember)) {
                    $user = Auth::instance()->get_user();
					$roles = new Model_Roles();
					$role = $roles->get_role_data($user['role_id']);
					if (@$role['allow_api_login'] == 0) {
						Cookie::set($GLOBALS['autologin'], FALSE);
						Auth::instance()->logout(TRUE, TRUE);
						$return['error'] = __('Not allowed to login');
						IbHelpers::set_message($return['error'], 'danger popup_box');
					} else if($user['email_verified']){
                        // User logged in ok so check if user has default page set
                        // Get the login redirect from the session if it exists

                        $redirect = $redirect ? URL::site($redirect) : $this->get_default_page();

                        if ($this->request->post('ajax'))
                        {
                            $return['success'] = 1;
                            $return['redirect'] = $redirect;
                        }
                        else
                        {
                            Session::instance()->set('login_redirect', null);
                            $this->request->redirect($redirect);
                        }
                    }
                    else
                    {
                        // If email hasn't been verified, we need log out user completely
                        Cookie::set($GLOBALS['autologin'], FALSE);
                        Auth::instance()->logout(TRUE, TRUE);
                        $post->error('email', 'not_empty');
                        $return['error'] = __('Your account has not been verified, be sure to check your email for the verification link');
                        IbHelpers::set_message($return['error'], 'danger popup_box');
                    }

                } else {
                    // Set an error message
                    $post->error('email', 'not_empty');
                    $return['error'] = __('Unable to log in with external provider, please contact the support.');
                    IbHelpers::set_message($return['error'], 'danger popup_box');
                }
            }


        }else if($data) {  // check if any POST values have been sent (i.e. have login credentials been sent)

			// Setup the validation rules
			$post = Validation::factory($_POST)
				->rule('email', 'not_empty')
			    ->rule('email', 'Valid::email')
			    ->rule('password', 'not_empty');

			// Assign the POST data
			$data = $post->data();

			// Set default language for app (will store it in cookie)
			I18n::set_default_language(@$data['lang']);

			// Remember The users login in the DB?
			$remember = (isset($data['remember']) && $data['remember'] === 'remember');

			// Validate the POST
			if ($post->check())
			{
				// check if the email and password are correct?
				$auth = Auth::instance();
                if($auth->login($data['email'], $data['password'], $remember)) {
                    $user = Auth::instance()->get_user();
                    Model_Contacts3::organisation_membership_update($auth->user['id']);
                    if ($auth->two_step_auth) {
                        $user =  new Model_User($auth->two_step_auth['id']);
                        $sms_resource = Model_Resources::get_by_alias('user_auth_2step_sms');
                        $email_resource = Model_Resources::get_by_alias('user_auth_2step_email');
                        if ($auth->two_step_auth['two_step_auth'] == 'SMS' && Model_Roles::has_permission($auth->two_step_auth['role_id'], $sms_resource['id'])) {
                            if ($user->mobile) {
                                $code = Model_Users::two_step_auth_code_create($auth->two_step_auth['id']);
                                Model_Users::two_step_auth_code_send($auth->two_step_auth, $code);
                                IbHelpers::set_message(__('Please enter authentication code'), 'info');
                                $this->request->redirect(
                                    '/admin/login/step2?' .
                                    http_build_query(array('user_id' => $auth->two_step_auth['id'], 'redirect' => $redirect))
                                );
                            } else {
                                $auth_data = array('email' =>$data['email'], 'password' => $data['password'], 'remember' => $remember);
                                Session::instance()->set('auth_data', $auth_data);
                                $this->request->redirect(
                                    '/admin/login/set_phone?' .
                                    http_build_query(array('user_id' => $auth->two_step_auth['id'], 'redirect' => $redirect))
                                );
                            }
                        } elseif($auth->two_step_auth['two_step_auth'] == 'Email' && Model_Roles::has_permission($auth->two_step_auth['role_id'], $email_resource['id'])) {
                            $code = Model_Users::two_step_auth_code_create($auth->two_step_auth['id']);
                            Model_Users::two_step_auth_code_send($auth->two_step_auth, $code);
                            IbHelpers::set_message(__('Please enter authentication code'), 'info');
                            $this->request->redirect(
                                '/admin/login/step2?' .
                                http_build_query(array('user_id' => $auth->two_step_auth['id'], 'redirect' => $redirect))
                            );
                        }
					}
                    $roles = new Model_Roles();
					$role = $roles->get_role_data($user['role_id']);
					if (@$role['allow_frontend_login'] == 0) {
						Cookie::set($GLOBALS['autologin'], FALSE);
						Auth::instance()->logout(TRUE, TRUE);
						$return['error'] = __('Not allowed to login');
						IbHelpers::set_message($return['error'], 'danger popup_box');
					} else if($user['email_verified']){
						if (@$post['invite_member'] && @$post['invite_hash']) {
							if (Model_Contacts3::invite_accept(@$post['invite_member'], @$post['invite_hash'])) {
								IbHelpers::set_message(__('You have joined successfully'), 'info');
							}
						}
						// User logged in ok so check if user has default page set
						// Get the login redirect from the session if it exists

                        $redirect = $redirect ? URL::site($redirect) : $this->get_default_page();

                        if ($this->request->post('ajax'))
                        {
                            $return['success'] = 1;
                            $return['redirect'] = $redirect;
                        }
                        else
                        {
							Session::instance()->set('login_redirect', null);
                            $this->request->redirect($redirect);
                        }
					}
                    else
                    {
                        // If email hasn't been verified, we need log out user completely
                        Cookie::set($GLOBALS['autologin'], FALSE);
                        Auth::instance()->logout(TRUE, TRUE);
                        $post->error('email', 'not_empty');
                        $return['error'] = __('Your account has not been verified yet. Please check your inbox, spam / junk folders for your verification link.').
                            ' <a href="/admin/login/resend_verification_email/' . $user['id'] . '" class="text-nowrap"><strong>'.__('Resend email').'</strong></a>';
                        IbHelpers::set_message($return['error'], 'danger popup_box');
                    }

	            } else {
		            // Set an error message
		            $post->error('email', 'not_empty');
                    $return['error'] = __('The username or password you entered is incorrect');
                    IbHelpers::set_message($return['error'], 'danger popup_box');

                }
			}
            else {
                $return['error'] = __('The user name should be an e-mail');
                IbHelpers::set_message($return['error'], 'danger popup_box');
            }

			if (!isset($return['error']) && is_array($post->errors('login'))) {
				foreach ($post->errors('login') as $error) {
					IbHelpers::set_message($error, 'danger popup_box');
				}
			}

            if ($this->request->post('ajax')) {
                $this->auto_render = false;
                echo json_encode($return);
            }

		} else {
			// For get request init language field to default value set by client
			$data['lang'] = I18n::get_default_language();

			if ($this->request->query('invite_member') && $this->request->query('invite_hash')) {
				if (Model_Contacts3::invite_check($this->request->query('invite_member'), $this->request->query('invite_hash'))) {
					IbHelpers::set_message(__('Please enter your password to complete joining family'), 'info');
				}
			}

			if ($this->request->query('email')) {
				$data['email'] = $this->request->query('email');
			}
		}

		$this->template->redirect = $redirect;

		if (isset($remember)) {
			$this->template->remember = $remember;
		}
  
		$this->template->body = View::factory('login')->set('redirect', $redirect);
        $this->template->body->alert = IbHelpers::get_messages();
		$this->template->body->data = $data;
        if (Settings::instance()->get('engine_enable_external_register') == '1') {
            $this->template->body->org_industries = Model_Organisation::get_organisation_industries();
            $this->template->body->org_sizes = Model_Organisation::get_organisation_sizes();
            $this->template->body->job_functions = Model_Contacts3::get_job_functions();
        }
        //This header will be send for check if the session is closed, "ajaxError" and "ajaxSuccess" check the header
        $this->response->headers('login_header', '1');
	}

    /**
     * Purpose : to check what default home page exists for a user if any
     * @return string this is the url to redirect
     */
    public function get_default_page()
    {
        $logged_in_user = Auth::instance()->get_user();
        $default_home_page = $logged_in_user['default_home_page'];

        if(!$default_home_page)
        {
            //load default dashboard if no default home page set.
            $redirect = "admin";
        }
        else
        {
            //set default home page to load
            $redirect = $default_home_page;
        }

        //return path to load
        return $redirect;

    }

	protected function check_recaptcha(){
		if (!Settings::instance()->get('cms_captcha_enabled')) {
			return true;
		}
		//check recaptcha 2 response (author : michael@ideabubble.ie)
		$key = Settings::instance()->get('captcha_private_key');

        // check that recaptcha was selected
		if(isset($_POST['g-recaptcha-response']))
			$captcha = $_POST['g-recaptcha-response'];
		if(!$captcha){
			return false;
		}

		//validate recaptcha response
		try {
			$url = 'https://www.google.com/recaptcha/api/siteverify';

			$data = array(
				'secret'   => $key,
				'response' => $_POST['g-recaptcha-response'],
				'remoteip' => $_SERVER['REMOTE_ADDR']
			);
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data)
				)
            );
			$context  = stream_context_create($options);
			$response = file_get_contents($url, false, $context);
			$res = json_decode($response, TRUE);

			if($res['success'] == 'true')
			{
				// recaptcha passed ok so let it pass
				return true;
			}
			else
			{
				return false;
			}

		}
		catch (Exception $e) {
			echo $e;
			return false;
		}
	}
	public function action_duplicate_contact()
    {
		$post = $this->request->post();
		$wrong_mobile = false;
		$contact = null;
		$tries = (int)Session::instance()->get('contact_register_tries');
		$registered = null;
		if (@$post['email'] && $post['mobile']) {
			$contact = Model_Contacts3::get_existing_contact_by_email_and_mobile($post['email'], $post['mobile']);
			if (!$contact) {
				$wrong_mobile = true;
				++$tries;
				Session::instance()->set('contact_register_tries', $tries);
			} else {
				Session::instance()->set('contact_register_tries', null);
				if (@$post['password'] && @$post['mpassword']) {
					$data = array(
						'email' => $post['email'],
						'password' => $post['password'],
						'mpassword' => $post['mpassword']
					);

					$roles = new Model_Roles();
					$data['role_id'] = $roles->get_id_for_role(Settings::instance()->get('website_frontend_register_role'));
					$data['email_verified'] = 1;
					$users = new Model_Users();
					$registered = $users->register_user($data);

					$contact3 = new Model_Contacts3($contact['id']);
					$contact3->set_permissions(array($registered['id']));
					$contact3->save();

					DB::update('engine_users')->set(array('default_home_page' => '/admin'))->where('id', '=', $registered['id'])->execute();
				}
			}
		}
		if ($wrong_mobile && $tries > 3) {
			$this->template->body = View::factory('mobile_number_not_match');
		} else if ($contact) {
			if (!$registered || $registered['error']) {
				$this->template->body = View::factory('duplicate_contact_reset_password');
				$this->template->body->email = $post['email'];
				$this->template->body->mobile = $post['mobile'];
				if ($registered['error']) {
					$this->template->body->alert = IbHelpers::set_message($registered['error'], 'error');
				}
			} else {
				$this->template->body = View::factory('duplicate_contact_reset_password_updated');
				$this->template->body->email = $post['email'];
				$this->template->body->mobile = $post['mobile'];
			}
		} else {
			$this->template->body = View::factory('login_duplicate_contact');
			$this->template->body->email = $this->request->query('email');
			$this->template->body->wrong_mobile = $wrong_mobile;
		}
    }

    public function action_resend_verification_email()
    {
        $user_id = $this->request->param('id');
        Model_Users::send_user_email_verification($user_id);
        $message = __('Verification email sent. Please check your email for your verification link.').
            __('This may be in your spam / junk folder.').
            ' <a href="/admin/login/resend_verification_email/' . $user_id . '" class="text-nowrap"><strong>'.__('Resend email').'</strong></a>';

        IbHelpers::set_message($message, 'success popup_box');
        $this->request->redirect('/admin/login');
    }

	public function action_step2()
	{
		$user_id = $this->request->query('user_id');
        if ($user_id == '') {
            $user_id = $this->request->post('user_id');
        }
        $redirect = $this->request->query('redirect');
        if (!is_numeric($user_id)) {
            $error_id = Model_Errorlog::save(null, "SECURITY");
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin/login/logout');
        }

        $code = $this->request->post('code');
        $this->template->body = View::factory('login_step2');
        $this->template->body->wrong_code = false;
        if ($code != '') {
            $auth = Auth::instance();
            if ($auth->step2_login($user_id, $code)) {
                $this->request->redirect(!empty($redirect) && $redirect != '/admin/login' ? $redirect : '/');

            } else {
                IbHelpers::set_message(__('Wrong authentication code'), 'error');
                $this->template->body->wrong_code = true;

            }
        }
        $user = Model_Users::get_user($user_id);
		$this->template->body->alert = IbHelpers::get_messages();
		$this->template->body->user_id = $user_id;
		$this->template->body->user = $user;
		$this->template->body->redirect = $redirect;
		//This header will be send for check if the session is closed, "ajaxError" and "ajaxSuccess" check the header
		$this->response->headers('login_header', '1');
	}

	
	public function action_set_phone() {
        $user_id = $this->request->query('user_id');
        $redirect = $this->request->query('redirect');
        if ($user_id == '') {
            $user_id = $this->request->post('user_id');
        }
        $post = $this->request->post();
        if (!empty($post)) {
            $valid = true;
            if (empty($post['country_dial_code_mobile'])) {
                IbHelpers::set_message('Please select country code');
                $valid = false;
            }
            if (empty($post['dial_code_mobile'])) {
                IbHelpers::set_message('Please select or enter area code');
                $valid = false;
            }
            if (empty($post['mobile'])) {
                IbHelpers::set_message('Please enter your phone number');
                $valid = false;
            }
            if ($valid) {
                $user_obj = new Model_User($user_id);
                $user_obj->set('country_dial_code_mobile', $post['country_dial_code_mobile']);
                $user_obj->set('dial_code_mobile', $post['dial_code_mobile']);
                $user_obj->set('mobile', $post['mobile']);
                $notifications = array(
                    'id' => 'new',
                    'notification_id' => 2,
                    'dial_code'=> trim(@$post['dial_code_mobile']),
                    'country_dial_code' => trim(@$post['country_dial_code_mobile']),
                    'value' => trim(@$post['mobile'])
                );
                $contact = $user_obj->get_contact();
                $contacts = new Model_Contacts3($contact->id);
                $contacts->insert_notification($notifications);
                $contacts->save();
                $user_obj->save();
                $auth_data = Session::instance()->get('auth_data');
                $auth = Auth::instance();
                $logged_in = false;
                if (!empty($auth_data)) {
                    $logged_in = $auth->login($auth_data['email'], $auth_data['password'], $auth_data['remember']);
                }
                Session::instance()->delete('auth_data');
                if(!$logged_in) {
                    $this->request->redirect(
                        '/admin/login/' );
                }
                $code = Model_Users::two_step_auth_code_create($auth->two_step_auth['id']);
                Model_Users::two_step_auth_code_send($auth->two_step_auth, $code);
                IbHelpers::set_message(__('Please enter authentication code'), 'info');

                $this->request->redirect(
                    '/admin/login/step2?' .
                    http_build_query(array('user_id' => $auth->two_step_auth['id'], 'redirect' => $redirect))
                );
            }
        }

        $user = Model_Users::get_user($user_id);
        $redirect = $this->request->query('redirect');
        $this->template->body = View::factory('login_set_phone');
        $this->template->body->alert = IbHelpers::get_messages();
        $this->template->body->user_id = $user_id;
        $this->template->body->user = $user;
        $this->template->body->redirect = $redirect;
        //This header will be send for check if the session is closed, "ajaxError" and "ajaxSuccess" check the header
        $this->response->headers('login_header', '1');
    }

    public function action_ajax_resend_auth_code() {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        if (!isset($post['user_id'])) {
            echo json_encode(array());
            exit;
        }

        $user = Model_Users::get_user($post['user_id']);
        $code = Model_Users::two_step_auth_code_create($user['id']);
        Model_Users::two_step_auth_code_send($user, $code);

        $text = '<p>A message with a verification code has been re-sent to' . str_repeat('*', strlen($user['country_dial_code_mobile']
                    .$user['dial_code'].$user['mobile']) - 2) . substr($user['mobile'], -2) . '. Enter the code to continue.</p>';
        $this->response->body(json_encode(array('result' => 'success', 'text' => $text)));
    }

    public function action_ajax_get_dial_codes(){
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $result = Controller_Frontend_Contacts3::get_dial_codes($post['country_code'], $post['phone_type']);
        echo json_encode($result);
        exit;
    }
}
