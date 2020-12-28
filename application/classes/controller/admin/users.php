<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Users extends Controller_Head
{
    public static $run_after_edit = array();

    public function action_index() {
        if (!Auth::instance()->has_access('user_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }

        $users = ORM::factory('users')->where('deleted', '=', '0')->find_all();
        $this->template->body = View::factory('content/settings/list_users');
        $this->template->body->users = array();
        $this->template->body->alert = IbHelpers::get_messages();
        $this->template->body->isPruductsLoaded = Model_Plugin::is_loaded('products');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Users', 'link' => '/admin/users');
        $this->template->sidebar->tools = '<a href="/admin/users/add_user"><button type="button" class="btn">Add New User</button></a>';
    }

    public function action_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json');

        if (!Auth::instance()->has_access('user_view')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->response->status(403);
            $this->request->redirect('/admin');
        }

        $params = $this->request->query();
        $result = Model_Users::search_datatable_old($params);
        echo json_encode($result);
    }

    public function action_edit(){
        if (!Auth::instance()->has_access('user_edit')) {
            ibhelpers::set_message('You do not have permission', 'error');
            $this->request->redirect('/admin');
        }
        $id = $this->request->param('id');
        if ($id != NULL) {
			$messages = null;
			if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){
				$messaging = new Model_Messaging();
				$messages = $messaging->search_messages(array('target' => $id, 'target_type' => 'CMS_USER'));
			}
			
            $users = new Model_Users();
            $user = $users->get_user($id);

            if (empty($user['id'])) {
                IbHelpers::set_message(__('User does not exist or has been deleted'), 'error');
                $this->request->redirect('admin/users');
            }

            $roles = new Model_Roles();
            $users_roles = $roles->get_all_roles();
            $currentUserRole = $roles->get_role_data($user['role_id']);

            $usergroups = new Model_Usergroup();
            $companies = $usergroups->get_usergroups();

            if(Model_Plugin::is_loaded('products') AND class_exists('Model_DiscountFormat')){
                $discounts = ORM::factory('discountformat');
            }

            $this->template->body = View::factory('content/settings/edit_user');
            $this->template->body->alert = null;
			$this->template->body->messages = $messages;

            if ($this->request->post()) {
                if ($this->request->post('delete')) {
                    $users->delete_user_data($id);
                    $this->request->redirect('admin/users');
                } else {
                    $postData = $this->request->post();
                    if (Kohana::$config->load('config')->get('daily_digest_enabled')) {
                        if(empty($postData['daily_digest_email'])){
                            $postData['daily_digest_email'] = 0;
                        }
                    }
                    if(isset($postData['discount_format_id']) AND $postData['discount_format_id'] AND $user['discount_format_id'] != $postData['discount_format_id']){
                        $discount = $discounts->get($postData['discount_format_id']);
                        $this->send_approve_discount_email($user, $discount);
                    }
                    $response = $users->update_user_data($id, $postData);
                    foreach (self::$run_after_edit as $run) {
                        call_user_func($run, $id, $postData);
                        //$run($id, $postData);
                    }
					$this->request->redirect('admin/users/edit/' . $id);
                }

                if (isset($response)) {
                    $this->template->body->alert = $response;
                }
                else {
                    $this->template->body->alert = null;
                }
            }

            $this->template->body->users = $user;
            if(Model_Plugin::is_loaded('products') AND class_exists('Model_DiscountFormat')) {
                $this->template->body->discounts = $discounts->get_all();
            }
            $this->template->body->currentUserRole = $currentUserRole;
            $this->template->body->users_roles = $users_roles;
            $this->template->body->companies = $companies;
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Users', 'link' => '/admin/users/');
            $this->template->sidebar->tools = '<a href="/admin/users/add_user"><button type="button" class="btn">Add New User</button></a>';
        } else {
            $this->request->redirect('admin/users');
        }
    }

	public function action_ajax_send_password_reset()
	{
		$post = $this->request->post();
		header('Content-Type: application/json; charset=utf-8');
		$user_model = new Model_Users();
		$user = $user_model->get_user($post['user_id']);
		if($user){
			$result = Model_Users::set_user_password_validation($user['email']);

            $use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');

            if ($use_messaging) // Use the messaging plugin
            {
                $extra_targets = array(
                    array('id' => NULL, 'template_id' => NULL, 'target_type' => 'CMS_USER', 'target' => $user['id'], 'x_details' => 'to', 'date_created' => NULL)
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
                    $model->send_to($user['email'],View::factory('email/reset_cms_email', array('form' => $result))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render(),'Password reset information for your Website CMS Login - please keep details confidential');
                    unset($result['validation']);
                }
            }
		}
		echo json_encode($user);
		exit();
	}
	
	public function action_ajax_resend_verify()
	{
		$post = $this->request->post();
		header('Content-Type: application/json; charset=utf-8');
		$result = Model_Users::send_user_email_verification($post['user_id']);
		echo json_encode($result);
		exit();
	}

    public function action_add_user() {

        // Create an instance of the Users & Roles model
        $users = new Model_Users();
        $roles = new Model_Roles();
        $usergroups = new Model_Usergroup();

        // Get the roles that are editable by the current logged in user.
        $users_roles = $roles->get_all_roles();

        // Get the companies available for this project
        $companies = $usergroups->get_usergroups();

        // Load post data into $postData
        $postData = $this->request->post();

        //Run the delete function from the users model
        if ($postData != NULL)
        {
            //Is the email address set?
            if ($users->check_email_set($postData['email']) == FALSE)
            {
                //Tell the user the email address has not been set
                //Set message here
                $result = IbHelpers::alert('The email address has not been set.', 'error popup_box');
            }
            //Is the email in use?
            else if ($users->check_email_used($postData['email']) == TRUE)
            {
                //Tell the user the email address is already in use
                //Set message here
                $result = IbHelpers::alert('The email you entered is already in use.', 'error popup_box');
            }
            // Check if the email is well formed
            else if ($users->check_email_user($postData['email']) == 1)
            {
                //Tell the user the email address is not valid
                //Set message here
                $result = IbHelpers::alert('This email address is not valid.', 'error popup_box');
            }
            //is the password set
            else if ($users->check_passwords_set($postData['password'], $postData['mpassword']) == FALSE)
            {
                // Tell the user the passwords are set
                // Set message
                $result = IbHelpers::alert('Please fill in both password fields.', 'error popup_box');
            }
            // Do the passwords match?
            else if ($users->check_passwords_match($postData['password'], $postData['mpassword']) == FALSE)
            {
                // Tell the user the passwords do not match
                // Set message
                $result = IbHelpers::alert('The passwords you entered do not match.', 'error popup_box');
            }
            // Is the password long enough?
            else if (strlen($postData['password']) < 8)
            {
                // Tell the user the passwords is too short
                // Set message
                $result = IbHelpers::alert('Your password must be at least 8 characters long.', 'error popup_box');
            }
            else
            {
                // Unset mpassword
                unset($postData['mpassword']);

                //Write user to the database
                $user_id = $users->add_user_data($postData);
                $user_id = $user_id[0];

                $role_id = $postData['role_id'];
                $role = '';
                $roles = new Model_Roles();
                if ($roles->get_name($role_id) == 'Teacher') {
                    $role = 'Teacher';
                } else if ($roles->get_name($role_id) == 'Student') {
                    $role = 'Student';
                } else if ($roles->get_name($role_id) == 'Mature Student') {
                    $role = 'Mature Student';
                }

                foreach (Controller_Admin_Login::$run_after_external_register as $call) {
                    $result = call_user_func(
                        $call,
                        array(
                            'email' => $postData['email'],
                            'name' => $postData['name'],
                            'surname' => $postData['surname'],
                            'role_id' => $role_id,
                            'role' => $role
                        ),
                        array('id' => $user_id, 'success' => true)
                    );
                }

                // tell the user they were successful
                IbHelpers::set_message('The user has been added to the CMS.', 'success popup_box');
                $this->request->redirect('/admin/users');
            }

            // Load the body here.
            $this->template->body->alert = $result;

            if (!empty($companies))
            {
                // Load roles into the view.
                $this->template->body->companies = $companies;
            }
        }
        else
        {
            //Load add user page to screen
            $this->template->body = View::factory('content/settings/add_user');

            // Load Companies
            if (!empty($companies)) {
                // Load roles into the view.
                $this->template->body->companies = $companies;
            }
            // Load roles into the view.
            $this->template->body->users_roles = $users_roles;
            if(Model_Plugin::is_loaded('products') AND class_exists('Model_DiscountFormat')) {
                $this->template->body->discounts = ORM::factory('discountformat')->get_all();
            }

        }
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Users', 'link' => '/admin/users');
        $this->template->sidebar->tools = '<a href="/admin/users/add_user"><button type="button" class="btn">Add New User</button></a>';
    }

    private function send_approve_discount_email($user, $discount){
        $event_id = Model_Notifications::get_event_id('approval_discount');
        if ($event_id !== FALSE) {
            $model = new Model_Notifications($event_id);
            $ok = $model->send(View::factory('content/settings/discount_approval_email', array('discount' => $discount))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render(), null, is_array($user) ? $user['email'] : $user->email);
        }
    }

    public function action_login_as()
    {
        if (!Auth::instance()->has_access('login_as')) {
            return false;
        }

        $currentUser = Auth::instance()->get_user();
        $user_id = $this->request->query('user_id');
        $modelUsers = new Model_Users;
        $user = $modelUsers->get_user($user_id);

        if (!$user) {
            $this->request->redirect('/admin/users');
        }

        if (Auth::instance()->logout()) {
            Auth::instance()->force_login($user['email'], true);
            Session::instance()->set('login_as_return_id', $currentUser['email']);
            $this->request->redirect($user['default_home_page']);
        }
    }

    public function action_login_back()
    {
        $loginBackEmail = Session::instance()->get('login_as_return_id');
        if(!$loginBackEmail){
            return false;
        }

        if (Auth::instance()->logout()) {
            Auth::instance()->force_login($loginBackEmail, false);
            Session::instance()->set('login_as_return_id', null);
            $this->request->redirect('/admin/usermanagement/users');
        }
    }
}
