<?php defined('SYSPATH') or die('No Direct Script Access.');


final class Controller_Frontend_Users extends Controller
{
	const SALT = '09g0)di56E4.pQtbr86(Ufr5xer>O&hn_RFedbnH\B5*iymrJq-mqj<gdpb4^pdircAb5yte#|';

    function action_login()
    {
        $this->auto_render = FALSE;

        $post     = $this->request->post();
        $post     = Validation::factory($post)->rule('email', 'not_empty')->rule('email', 'Valid::email')->rule('password', 'not_empty');
        $email    = isset ($post['email'])    ? $post['email']    : '';
        $password = isset ($post['password']) ? $post['password'] : '';
        $users    = new Model_Users();
        $user     = $users->get_user_by_email($email);
        $message  = '';

        if ($email == '')
        {
            $message = 'You must enter an email address';
        }
        elseif ( ! filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $message = 'Invalid email address';
        }
        elseif ( ! $user)
        {
            $message = 'Email is not registered';
        }
        elseif ($user['email_verified'] == 0)
        {
            $message = 'This email has not been verified. Please check your email inbox';
        }
        elseif ($password == '')
        {
            $message = 'You must enter a password';
        }
        elseif ( ! Auth::instance()->login($email, $password))
        {
            $message = 'The supplied username and password do not match.';
        }

        if ($message != '')
        {
            IbHelpers::set_message($message, 'error');
            $this->request->redirect('/login.html');
        }
        else
        {
			// If there is an order history page, redirect to that
			$redirect = $this->request->post('redirect');

			if (empty($redirect))
			{
				$pages = Model_Pages::get_by_layout('orderhistory');
				$redirect = isset($pages[0]) ? '/'.$pages[0]['name_tag'] : $redirect;
			}
            //on each login update membership status for organisation.
            $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
            if (!empty($contact)) {
                $organisation = $contact->get_linked_organisation();
                if (!empty($organisation)) {
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
			if ( ! empty($redirect))
			{
				$this->request->redirect($redirect);
			}
			else
			{
				$this->request->redirect('/');
			}

            //todo add default home page
        }
    }

    function action_logout()
    {
        $this->auto_render = FALSE;
        Session::instance()->delete('login_redirect');
        Auth::instance()->logout();

		if (class_exists('Model_Checkout'))
		{
			Model_Checkout::empty_cart();
		}

        $this->request->redirect('/');
    }


    public function action_registration_confirmation()
    {
        $this->auto_render = FALSE;
        $id    = $this->request->param('id');
        $hash  = $this->request->query('hash');
        $users = new Model_Users();
        $user  = $users->get_user($id);
        $set_pw= $user['password'] == '!';
        $hash2 = md5($user['email'].self::SALT);

        if ($set_pw) {
            $redirect = '/admin/';
        } else {
            // If a redirect has been specified, use that, otherwise send the user to the login form
            $redirect = urldecode($this->request->query('redirect'));
            $redirect = $redirect ? $redirect : Settings::instance()->get('website_frontend_login_url');
            $redirect = $redirect ? $redirect : '/admin/login';
        }

        if ($user['email_verified'] == 1) {
            IbHelpers::set_message('Email is already verified.', 'danger popup_box');
        }
        elseif ($hash == $hash2) {
            $user['email_verified']   = 1;
            $user['can_login']        = 1;
            $user['password']         = $user['mpassword'] = '';
            $user['trial_start_date'] = date('Y-m-d H:i:s');
            $users->update_user_data($id, $user);
            Model_Contacts::link_user_to_existing_contact($user['email']);

            IbHelpers::set_message(__('Your account has now been activated'), 'success popup_box');

            if ($set_pw) {
                // If the user has not set a password, send them to a form with a password field.
                // Use a URL hash to verify it is them.
                $validation = Model_Users::set_user_password_validation($user['email']);
                IbHelpers::set_message(__('Please set your password'), 'info popup_box');
                $query = ['email' => $user['email'], 'validate' => $validation['validation'], 'redirect' => $redirect];
                $redirect = URL::site('/admin/login/register?' . http_build_query($query));
            }
        }
        else {
            IbHelpers::set_message('Verification failed', 'error');
        }

        $this->request->redirect($redirect);
    }

    public function action_ajax_register_user()
    {
        $post          = $this->request->post();
        $post['email'] = (isset($post['email1']) && isset($post['email2'])) ? $post['email1'].'@'.$post['email2'] : $post['email'];
        $valid         = $this->validate_new_user($post, FALSE);

        if ( ! $valid['success'])
        {
            $response['status']  = 'error';
            $response['message'] = $valid['message'];
        }
        else
        {
            $user_id = $this->create_account($post);

			try
			{
                Database::instance()->begin();
				// Send verification email
				$hash       = md5($post['email'].self::SALT);
                $redirect   = urlencode($this->request->post('redirect'));
				$link       = URL::site('frontend/users/registration_confirmation/'.$user_id.'?hash='.$hash.($redirect ? '&redirect='.$redirect : ''));
				$email_body = View::factory('content/frontend/registration_confirmation_email')->set('link', $link)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
				$subject    = 'Email confirmation for '.$post['email'].' on '.URL::base();
				$from       = Settings::instance()->get('account_verification_sender');
				$from       = ($from == '') ? 'noreply@ideabubble.ie' : $from;
				IbHelpers::send_email($from, $post['email'], NULL, NULL, $subject, $email_body); // $from, $to, $cc, $bcc, $subject, $message
                Database::instance()->commit();
			}
			catch(Exception $e)
			{
				$response['status']  = 'error';
				$response['message'] = 'Error sending validation email.';

                Database::instance()->rollback();
                throw $e;
			}

			if ( ! isset($response['status']) OR $response['status'] != 'error')
			{
				$response['status']  = 'success';
				$response['message'] = 'A validation email has been sent.';
			}
        }
        $this->response->body(json_encode($response));
    }

    private function validate_new_user($data, $captcha = FALSE)
    {
        $users_model         = new Model_Users;
        $formprocessor_model = new Model_Formprocessor;
        $return['success']   = FALSE;
        $return['message']   = '';

        if ( ! filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        {
            $return['message'] = 'Invalid email address.';
        }
        elseif ($users_model->get_user_by_email($data['email']))
        {
            $return['message'] = 'Email is already registered.';
        }
        elseif ($data['password'] == '')
        {
            $return['message'] = 'You must supply a password.';
        }
        elseif ($data['password'] !== $data['password2'])
        {
            $return['message'] = 'Passwords do not match.';
        }
		elseif (strlen($data['password']) < 8)
		{
			$return['message'] = 'A password must be at least 8 characters long.';
		}
        elseif ($captcha AND ! $formprocessor_model->captcha_check($data))
        {
            $return['message'] = 'Your captcha failed validation. Please verify you enter the correct details.';
        }
        else
        {
            $return['success'] = TRUE;
        }
        return $return;
    }

    private function create_account($data)
    {
        $users_model                 = new Model_Users;
        $roles_model                 = new Model_Roles;

        $user_data['name']           = isset($data['name'])      ? $data['name']      : '';
        $user_data['surname']        = isset($data['surname'])   ? $data['surname']   : '';
        $user_data['email']          = $data['email'];
        $user_data['password']       = $data['password'];
        $user_data['phone']          = isset($data['phone'])     ? $data['phone']     : '';
        $user_data['mobile']         = isset($data['mobile'])    ? $data['mobile']    : '';
        $user_data['address']        = isset($data['address'])   ? $data['address']   : '';
        $user_data['address_2']      = isset($data['address_2']) ? $data['address_2'] : '';
        $user_data['address_3']      = isset($data['address_3']) ? $data['address_3'] : '';
        $user_data['country']        = isset($data['country'])   ? $data['country']   : '';
        $user_data['county']         = isset($data['country'])   ? $data['country']   : '';
        $user_data['company']        = isset($data['company'])   ? $data['company']   : '';
        $user_data['default_home_page'] = isset($data['default_home_page'])   ? $data['default_home_page']   : '';

        $role_id                     = $roles_model->get_id_for_role('Basic');
        $user_data['role_id']        = is_null($role_id) ? $roles_model->get_id_for_role('External User') : $role_id;

        $user_data['email_verified'] = 0;
        $add_user                    = $users_model->add_user_data($user_data);
        $user_id                     = isset($add_user[0]) ? $add_user[0] : NULL;
        return $user_id;
    }
}
