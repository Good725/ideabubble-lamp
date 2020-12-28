<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Settings extends Controller_Head {

    protected $_crud_items = [
        'externalrequest' => [
            'name' => 'External request',
            'model' => 'ExternalRequest',
            'delete_permission' => false,
            'edit_permission'   => false,
        ],
		'errorlog' => [
				'name' => 'Error Log',
				'model' => 'Errorlogs',
				'delete_permission' => false,
				'edit_permission'   => false,
		]
    ];
	protected $allow_script_actions = array('index');

    public function action_import()
    {
        $access = Auth::instance()->has_access('settings_index');
        if( ! $access)
        {
            throw new Kohana_Exception('You do not have access to this resource.', array(), 501);
        }

        if (isset($_FILES['settings']) && $_FILES['settings']['error'] == UPLOAD_ERR_OK) {
            $tmp_file = tempnam(Kohana::$cache_dir, 'tmp_settings');
            move_uploaded_file($_FILES['settings']['tmp_name'], $tmp_file);
            $json = file_get_contents($tmp_file);
            $values = json_decode($json, true);
            $m = new Model_Settings();
            $m->import($values);
            IbHelpers::set_message('Settings have been imported');
            $this->request->redirect('/admin/settings');
        }

        $this->template->body      = View::factory('content/settings/import');
        $this->template->styles    = array_merge($this->template->styles, array (
            URL::get_engine_assets_base().'css/list_settings.css' => 'screen',
            URL::get_engine_assets_base().'css/bootstrap-multiselect.css' => 'screen',
            URL::get_engine_assets_base().'css/spectrum.min.css' => 'screen'
        ));

        $this->template->sidebar->tools = '<a href="/admin/settings/export" class="btn" target="_blank">Export</a> <a href="/admin/settings/import" class="btn"">Import</a>';

    }

    public function action_export()
    {
        $access = Auth::instance()->has_access('settings_index');
        if( ! $access)
        {
            throw new Kohana_Exception('You do not have access to this resource.', array(), 501);
        }

        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $this->response->headers('Content-disposition', 'attachment; filename="' . $_SERVER['HTTP_HOST'] . '-' . date('YmdHis') . '-settings.json"');
        $values = Settings::instance()->get();
        echo json_encode($values, defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : null);
    }

    public function action_mysqldump()
    {
        if (Kohana::$environment == Kohana::PRODUCTION) {
            echo "only for dev/test/uat";
            exit;
        }
        $cfg = Kohana::$config->load('database')->default;
        $cfg['connection']['hostname'];
        $cfg['connection']['username'];
        $cfg['connection']['password'];
        $dir = Kohana::$cache_dir;
        chdir($dir);
        $file = $dir . '/' . $cfg['connection']['database'] . '-' . date('YmdHis') . '.sql';
        $cmd = 'mysqldump -u"' . $cfg['connection']['username'] . '" -p"' . $cfg['connection']['password'] . '" ' . $cfg['connection']['database'] . ' ' . ' -h"' . $cfg['connection']['hostname'] . '" > ' . $file;
        set_time_limit(0);
        exec($cmd, $output);
        $cmdgz = "gzip " . $file;
        exec($cmdgz, $outputgz);
        $size = filesize($file . ".gz");

        //echo $cmd;exit;
        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment; filename="' . basename($file . ".gz") . '"');
        header('Content-Length:' . $size);
        readfile($file . ".gz");
        @unlink($file . ".gz");
        @unlink($file);
        exit;
    }
    
	public function action_index()
	{

        $access = Auth::instance()->has_access('settings_index');
		if( ! $access)
		{
			throw new Kohana_Exception('You do not have access to this resource.', array(), 501);
		}

		// Load the body here.
		$this->template->body      = View::factory('content/settings/list_settings');
		$this->template->styles    = array_merge($this->template->styles, array (
            URL::get_engine_assets_base().'js/codemirror/merged/codemirror.css' => 'screen',
			URL::get_engine_assets_base().'css/list_settings.css' => 'screen',
			URL::get_engine_assets_base().'css/bootstrap-multiselect.css' => 'screen',
            URL::get_engine_assets_base().'css/spectrum.min.css' => 'screen'
		));
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/codemirror/merged/codemirror.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/list_settings.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/spectrum.min.js"></script>';
		$settings                  = new Model_Settings();

		// If post data is sent pass it to the updater
		if ($this->request->post())
		{
			$this->template->body->alert = $settings->update($this->request->post());
			$this->request->redirect($this->request->uri().URL::query());
		}

        $config = Kohana::$config->load('config');

        $this->template->sidebar->tools = '<a href="/admin/settings/export" class="btn" target="_blank">Export</a> <a href="/admin/settings/import" class="btn">Import</a>';
        $this->template->body->is_microsite = (!empty($config->microsite_id));
		$this->template->body->forms = $settings->build_form();
		$this->template->body->display_group = $this->request->query('group');

	}

	public function action_users()
    {
        Log::instance()->add(Log::WARNING, "Deprecated link: /admin/usermanagement/users; referer: " . @$_SERVER['HTTP_REFERER']);
        $this->request->redirect('/admin/usermanagement/users');
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
				$result = IbHelpers::alert('The email address has not been set.', 'error');
			}
			//Is the email in use?
			else if ($users->check_email_used($postData['email']) == TRUE)
			{
				//Tell the user the email address is already in use
				//Set message here
				$result = IbHelpers::alert('This account already exists, please login or change your login details and try again.', 'error');
			}
			// Check if the email is well formed
			else if ($users->check_email_user($postData['email']) == 1)
			{
				//Tell the user the email address is not valid
				//Set message here
				$result = IbHelpers::alert('This email address is not valid.', 'error');
			}
			//is the password set
			else if ($users->check_passwords_set($postData['password'], $postData['mpassword']) == FALSE)
			{
				// Tell the user the passwords are set
				// Set message
				$result = IbHelpers::alert('Please fill in both password fields.', 'error');
			}
			// Do the passwords match?
			else if ($users->check_passwords_match($postData['password'], $postData['mpassword']) == FALSE)
			{
				// Tell the user the passwords do not match
				// Set message
				$result = IbHelpers::alert('The passwords you entered do not match.', 'error');
			}
			// Is the password long enough?
			else if (strlen($postData['password']) < 8)
			{
				// Tell the user the passwords is too short
				// Set message
				$result = IbHelpers::alert('This password is too short, please enter a password with a minimum of 8 characters.', 'error');
			}
			else
			{
				// Unset mpassword
				unset($postData['mpassword']);

				//Write user to the database
				$users->add_user_data($postData);

				// tell the user they were successful
				$result = IbHelpers::alert('The user has been added to the CMS.', 'success');
			}

			//Load add user page to screen
			$this->template->body = View::factory('content/settings/add_user');

			// Load the body here.
			$this->template->body->alert = $result;

			// Load roles into the view.
			$this->template->body->users_roles = $users_roles;

			if (!empty($companies))
			{
				// Load roles into the view.
				$this->template->body->companies = $companies;
			}
		}
		else
		{
			// Load the body here
			$this->template->body = View::factory('content/settings/add_user');
			// Load roles into the view.
			$this->template->body->users_roles = $users_roles;

			if (!empty($companies))
			{
				// Load roles into the view.
				$this->template->body->companies = $companies;
			}
		}
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Users', 'link' => '/admin/settings/users');
        $this->template->sidebar->tools = '<a href="/admin/settings/add_user"><button type="button" class="btn">Add New User</button></a>';
	}

	/* Developer: David
		  *
		  * Display all roles below the level of the current logged in user
		  *
		  * Allow Edit, Delete and Adding of roles
		  * 	- Enter Role name / Edit Role name
		  *
		  * Edit the permissions of the displayed roles by clicking on them or an Edit button
		  *
		  */
	public function action_manage_roles() {

		$roles = new Model_Roles();

		$role_data = $roles->get_all_roles();

		$this->template->body = View::factory('content/settings/manage_roles');

		$this->template->body->alert = null;
		$this->template->body->roles = $role_data;
        $this->template->sidebar->breadcrumbs[] = array('name' => 'User Roles', 'link' => '/admin/settings/manage_roles');
        $this->template->sidebar->tools = '<a href="/admin/settings/add_role"><button type="button" class="btn">Add Role</button></a>';
	}

	public function action_edit_role() {

		// Get the passed in role ID
		$role_id = $id = $this->request->param('id');
		// Get post data
		$post_data = $this->request->post();
		// Create instance of the roles model
		$roles = new Model_Roles();

		// The the roles data for the edit screen
		$role_data = $roles->get_role_data($id);

		$controllers = ORM::factory('Resources')->where('type_id', '=', 0)->find_all();

		$role = ORM::factory('Roles', $role_id);
		if(!$role->loaded()) die('Role doesn\'t exist!');
		/*
		 * Select all resources with type "Code" (type_id=2) - see config/resource_types.php
		 */
		$code_pieces = ORM::factory('Resources')->where('type_id','=',2)
				->order_by('name', 'ASC')->find_all();

		if ($post_data) {
			if (isset($post_data['delete'])) {
				$message = $roles->delete_role($id);
				if($message=="This role has been deleted"){
					IbHelpers::set_message($message, 'success popup_box');
					$this->request->redirect('/admin/settings/manage_roles');
				  }else{
					IbHelpers::set_message($message, 'error popup_box');
			     }		  
				 
			}
			// Is the role field set?
			elseif (($post_data['role']) == '') {
				// Tell the user the role name has not been set
				IbHelpers::alert('The role name has not been set.', 'error');
			} else {
				// Update role data
				$update_data['access_type']          = isset($post_data['access_type'])          ? $post_data['access_type']          : '';
				$update_data['role']       		     = isset($post_data['role'])                 ? $post_data['role']                 : '';
				$update_data['description']		     = isset($post_data['description'])          ? $post_data['description']          : '';
				$update_data['publish']    		     = isset($post_data['publish'])              ? $post_data['publish']              : '';
				$update_data['master_group']         = isset($post_data['master_group'])         ? $post_data['master_group']         : '';
				$update_data['default_dashboard_id'] = isset($post_data['default_dashboard_id']) ? $post_data['default_dashboard_id'] : '';
				$roles->update_role_data($id, $update_data);

                DB::delete('engine_role_permissions')->where('role_id', '=', $role_id)->execute();
                if($this->request->post('resource')) {
                    foreach($this->request->post('resource') as $resource_id => $value) {
                        DB::insert('engine_role_permissions', array('role_id', 'resource_id'))
                            ->values(array($role_id, $resource_id))
                            ->execute();
                    }
                }
				if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){
					Model_Messaging::set_activity_alert_list('CMS_ROLE', $id, @$post_data['activity_alert']);
				}
				// Tell the user they were successful
				IbHelpers::set_message('The role has been updated.', 'success popup_box');

				$redirect = (isset($post_data['save_and_exit'])) ? 'manage_roles' : 'edit_role/'.$id;
				$this->request->redirect('/admin/settings/'.$redirect);
			}
		} 
		$activity_actions = Model_Activity::get_action_list();
		$activity_item_types = Model_Activity::get_item_type_list();
		if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){
			$activity_alerts = Model_Messaging::get_activity_alert_list('CMS_ROLE', $id);
		}

		$this->template->body                   = View::factory('content/settings/edit_role')->set('controllers', $controllers)->set('code_pieces', $code_pieces);
		$this->template->body->role             = ORM::factory('Roles', $id);
		$this->template->body->activity_actions = $activity_actions;
		$this->template->body->activity_item_types = $activity_item_types;
		$this->template->body->controllers = $controllers;
		$this->template->body->code_pieces = $code_pieces;
		$this->template->body->dashboards = Model_Dashboard::get_user_accessible(FALSE, $id);

		if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){
			$this->template->body->activity_alerts = $activity_alerts;
		}
		$this->template->body->alert            = IBHelpers::get_messages();
		$this->template->sidebar->breadcrumbs[] = array('name' => 'User Roles', 'link' => '/admin/settings/manage_roles');
		$this->template->sidebar->tools         = '<a href="/admin/settings/add_role"><button type="button" class="btn">Add Role</button></a>';
		$this->template->body->plugins = Model_Plugin::get_all();
		$this->template->body->roles = Model_Plugin::get_all_roles();
		// $this->template->body->matrix = Model_Plugin::get_matrix();
		
	}

	// Add a role to the system
	public function action_add_role() {

		// Create an instance of the Roles model
		$roles = new Model_Roles();

		// Load post data into $postData
		$postData = $this->request->post();

		// If there is post data check to see if the role field is set.
		if ($postData != NULL)
		{
			//Is the role field set?
			if (($postData['role']) == '')
			{
				//Tell the user the role name has not been set
				//Set message here
				$result = IbHelpers::alert('The role name has not been set.', 'error');
			}
			else
			{
				//Write user to the database
                $roleId = $roles->add_role($postData);
				// tell the user they were successful
				$result = IbHelpers::alert('The role has been added to the CMS.', 'success');
                $this->request->redirect('/admin/settings/edit_role/' . $roleId);
			}

			//Load add role page to screen
			$this->template->body = View::factory('content/settings/add_role');
			// Load the body alerts here.
			$this->template->body->alert = $result;

		}
		else
		{
			// Load the body here
			$this->template->body = View::factory('content/settings/add_role');
		}
        $this->template->sidebar->breadcrumbs[] = array('name' => 'User Roles', 'link' => '/admin/settings/manage_roles');
        $this->template->sidebar->tools = '<a href="/admin/settings/add_role"><button type="button" class="btn">Add Role</button></a>';
	}
    //delete roles
    public function action_delete_role() {
	   $id = $this->request->param('id');
	   $roles = new Model_Roles();
	   $message = $roles->delete_role($id);
	   echo $message; die;
   }
    //changing publish status of Role
    public function action_publish_role() {
	   $id = $this->request->post('roleid');
	   $publish = $this->request->post('publish');
	   $message=$publish ? 'Role Unpublished Successfully':'Role Published Successfully';
	   $update = array('publish' => $publish ? 0:1);
	   $query = DB::update('engine_project_role')
					->set($update)
					->where('id', '=', $id)
					->execute();
	   echo $message;die;
   }
	/* Developer: David
		  *
		  * Display all core and plugin settings
		  *
		  * Save Role Permissions
		  *
		  */

	public function action_redirects()
    {
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/redirect.js"></script>';
        $this->template->styles = array_merge($this->template->styles, array (URL::get_engine_assets_base().'css/redirect.css' => 'screen'));

        $this->template->body = View::factory('content/settings/add_edit_redirects');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Redirects', 'link' => '/admin/settings/redirects');
    }

    public function action_redirect_view()
    {
        $this->auto_render = false;
        $this->response->body(View::factory('content/settings/redirect_snippet'));
    }

    public function action_save_redirects()
    {
        $post = $this->request->post();

		// add new redirects
		$newRedirects = Arr::get($post, 'newRedirect', array());
		$newRedirectsFrom = Arr::get($newRedirects, 'from', array());
		foreach($newRedirectsFrom as $key => $newRedirectFrom){
			Model_PageRedirect::save_redirect($newRedirectFrom, $newRedirects['to'][$key], $newRedirects['type'][$key]);
		}

		// update old redirects
		$oldRedirects = Arr::get($post, 'oldRedirect', array());
		$oldRedirectsFrom = Arr::get($oldRedirects, 'from', array());
		foreach($oldRedirectsFrom as $id => $oldRedirectFrom){
			Model_PageRedirect::update_redirect($id, $oldRedirectFrom, $oldRedirects['to'][$id], $oldRedirects['type'][$id]);
		}

		// remove redirects
		$deleteRedirect = Arr::get($post, 'deleteRedirect', array());
		foreach($deleteRedirect as $id){
			Model_PageRedirect::delete_redirect($id);
		}

        $this->request->redirect('/admin/settings/redirects');
    }

	/* Developer: David
		  * USER GROUP FUNCTIONS
		  */

	public function action_manage_usergroups() {

		// Create inst of model
		$usergroup_model = new Model_Usergroup();

		// Get all user groups
		$usergroups = $usergroup_model->get_usergroups();

		$this->template->body = View::factory('content/settings/manage_usergroups');
		// Send users to view
		$this->template->body->usergroups = $usergroups;
	}

	public function action_add_usergroup() {
		//Get post data
		$post_data = $this->request->post();

		// Create models
		$model = new Model_Usergroup();

		if ($post_data)
		{
			if ($post_data['user_group'] == '')
			{
				IbHelpers::set_message('You must a company name', 'error popup_box');
			}
			elseif ($post_data['description'] == '')
			{
				IbHelpers::set_message('You must provide a company description', 'error popup_box');
			}
			else
			{
				$model->add_usergroup($post_data);
				IbHelpers::set_message('User Group added', 'success popup_box');
			}
		}

		// Display the empty view with any set alert
		$this->template->body = View::factory('content/settings/add_usergroup');
	}

	public function action_edit_usergroup() {

		//Get post data
		$id = $this->request->param('id');
		$post_data = $this->request->post();

		// Create inst of model
		$usergroup_model = new Model_Usergroup();

		// Setup an empty alert
		$alert = '';

		if ($post_data)
		{
			if (isset($post_data['delete']))
			{
				$usergroup_model->delete_usergroup($id);
				$alert = IbHelpers::set_message('User Group deleted', 'success popup_box');
				$this->request->redirect('admin/settings/manage_usergroups/');
			}
			else
			{
				$usergroup_model->update_usergroup($id, $post_data);
				// Now add any alerts
				$alert = IbHelpers::alert('User Group updated', 'success');
			}
		}

		$data = $usergroup_model->get_usergroups($id);

		// Add the alerts to the body
		$this->template->body->alert = $alert;
		$this->template->body = View::factory('content/settings/edit_usergroup');
		// Send users to view
		$this->template->body->usergroups = $data;
	}

	/*
	 * USER REQ FUNCTIONS
	 */
	public function action_list_userreq() {
		$this->template->body = Request::factory('admin/userreqsettings/list_userreq/')->post($this->request->post())->execute()->body();
	}

	public function action_edit_userreq() {
		$this->template->body = Request::factory('admin/userreqsettings/edit_userreq/' . $this->request->param('id'))->post($this->request->post())->execute()->body();
	}

	public function action_list_userreq_codes() {
		$this->template->body = Request::factory('admin/userreqsettings/list_userreq_codes/')->post($this->request->post())->execute()->body();
	}

	public function action_add_userreq_codes() {
		$this->template->body = Request::factory('admin/userreqsettings/add_userreq_codes/')->post($this->request->post())->execute()->body();
	}

	/*
	 *   Debug Functions
	 */
	public function action_debug_kohana() {
		$this->template->body = View::factory('content/settings/debug_kohana');
	}

	public function action_debug_system() {
		$this->template->body = View::factory('content/settings/debug_system');
	}

    public function action_list_logs() {
        $old_config = Kohana::$config->load('config');
        $this->template->sidebar->tools = '<a class="btn" href="/admin/settings/fix_sqlsecurity">Fix Sql Security</a>';
		$post = $this->request->post();
		if(isset($post['delete_models']) && isset($post['model_id'])){
			Model_DALM::delete_models($_POST['model_id']);
			$this->request->redirect('/admin/settings/list_logs');
		}
		$charset_vars = DB::query(Database::SELECT, "show variables like '%character%'")->execute()->as_array();
        $this->template->body = View::factory( 'content/settings/list_logs');
        $this->template->body->data = Model_DALM::get_dalm_model();
        $this->template->body->data2 = Model_DALM::get_dalm_statements();
		$this->template->body->missing_models = Model_DALM::get_missing_models();
		$this->template->body->charset_vars = $charset_vars;
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Dalm', 'link' => '/admin/settings/list_logs');
        Kohana::$config->_groups['config'] = $old_config;
    }

    public function action_list_activation_codes() {
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/user_activation_codes.js"></script>';
        $codes_model = new Model_Code();
        $codes = $codes_model->get_codes();

        $groups = $codes_model->get_groups()->as_array('id', 'user_group');

        $roles = $codes_model->get_roles()->as_array('id', 'role');

        //echo Debug::vars($groups);

        $this->template->body = View::factory('content/settings/list_activation_codes')
                ->bind('codes', $codes)
                ->bind('groups', $groups)
                ->bind('roles', $roles);

    }

    public function action_get_activation_codes_list()
    {
        $this->auto_render = false;
        $get = $this->request->query();
        $result = array();
        $codes = new Model_Code();
        $data = $codes->get_code_range($get);
        $result['iTotalRecords'] = count($data['count']);
        $result['aaData'] = $data['data'];
        $result['iTotalDisplayRecords'] = $result['iTotalRecords'];
        $result['sEcho'] = (int) $_GET['sEcho'];
        $result['sColumns'] = 'Code,Group,Role,Date Added,Published,Edit,Delete';
        $this->response->body(json_encode($result));
    }

    public function action_import_activation_codes() {

        if(isset($_FILES['file']) && $_POST['group_id']!='empty'  && $_POST['role_id']!='empty') {

            $codes = Csv::decode($_FILES['file']['tmp_name'], ',', FALSE);
            $date = date('Y-m-d');
            set_time_limit(0);

            $i = 0; $d = 0;
            foreach($codes as $code) {

                $unique = !DB::select(array(DB::expr('COUNT(code)'), 'total'))
                          ->from('user_act_codes')
                          ->where('code', '=', $code[0])
                          ->execute()
                          ->get('total');

                if($unique) {

                  $query = DB::insert('user_act_codes', array('group_id', 'role_id', 'code', 'date_added'))
                          ->values(array($_POST['group_id'], $_POST['role_id'], $code[0], $date))
                          ->execute();

                  $i++;
                } else {
                  $d++;
                }
            }
            echo 'Success. '.$i.' code(s) saved. '.$d.' duplicated.';

        } else {
            echo 'The form is invalid.';
        }

        $this->auto_render = FALSE;

    }

    public function action_delete_activation_code() {


        DB::delete('user_act_codes')->where('id', '=', $this->request->param('id'))->execute();

        $alert = IbHelpers::set_message('Code deleted', 'success popup_box');

        $this->request->redirect(URL::base().'admin/settings/list_activation_codes');

        $this->auto_render = FALSE;

    }//end of action_userreq_code_del()

    public function action_status_update_activation_code() {

        $published = DB::select('published')
                ->from('user_act_codes')
                ->where('id', '=', $this->request->param('id'))
                ->execute()->get('published');

        echo Debug::vars($published);


        DB::update('user_act_codes')
                ->set(array('published' => !$published))
                ->where('id', '=', $this->request->param('id'))
                ->execute();

        $alert = IbHelpers::set_message('Code status changed', 'success popup_box');

        $this->request->redirect(URL::base().'admin/settings/list_activation_codes');

        $this->auto_render = FALSE;
    }

    public function action_edit_activation_code() {

            $codes_model = new Model_Code();

            $code = $codes_model->get_single_code($this->request->param('id'));

            //echo Debug::vars($code);

            $alert = '';

            if($this->request->method()=='POST') {

                $codes_model->update_single_code($this->request->param('id'), $this->request->post());

                $alert = IbHelpers::set_message('Code edited', 'success popup_box');

                $this->request->redirect(URL::base().'admin/settings/list_activation_codes');

            }

            $groups = $codes_model->get_groups()->as_array('id', 'user_group');
            $roles = $codes_model->get_roles()->as_array('id', 'role');

            $this->template->body = View::factory('content/settings/edit_activation_code');
            $this->template->body->alert = $alert;
            $this->template->body->groups = $groups;
            $this->template->body->roles = $roles;
            $this->template->body->code = $code;

    }// end of action_userreq_code_edit()



    /*
     * Action for listing application logs
     */
    public function action_show_logs() {
        
        $logs_query['logs_from']  = !empty( $this->request->query('logs_from') ) ? $this->request->query('logs_from') : '';
        $logs_query['logs_to']    = !empty( $this->request->query('logs_to') ) ? $this->request->query('logs_to') : '';
        $logs_query['logs_type']  = !empty( $this->request->query('logs_type') ) ? $this->request->query('logs_type') : '';
        $logs = Logs_Logs::factory()->create_dir_array();
        if( !empty( $logs_query['logs_from'] ) || !empty( $logs_query['logs_to'] ) || !empty( $logs_query['logs_type'] ) ) {
            $logs = Logs_Logs::factory()->filter_array( $logs, $logs_query );
        }


        $this->template->body = View::factory('content/settings/show_logs')
                ->bind('logs', $logs);
        $this->template->sidebar->breadcrumbs[] = array('name' => 'App Logs', 'link' => '/admin/settings/show_logs');

    }//end of action_show_logs()

    public function action_logs()
    {
        $this->template->sidebar->breadcrumbs[] = ['name' => 'Logs', 'link' => '/admin/settings/logs'];

        $this->template->body = View::factory('iblisting')->set([
            'columns'             => ['ID', 'Created', 'Type', 'URL', 'Status', 'Message', 'Data', 'Duration'],
            //'filter_menu_options' => false,
            'daterangepicker'     => true,
            'id_prefix'           => 'system-logs',
            'plugin'              => 'settings',
            'reports'             => [],
            'top_filter'          => false,
            'searchbar_on_top'    => false,
            'type'                => 'externalrequest',
        ]);
    }

	public function action_errorlogs()
	{
		$this->template->sidebar->breadcrumbs[] = ['name' => 'Error Logs', 'link' => '/admin/settings/error_logs'];

		$this->template->body = View::factory('iblisting')->set([
				'columns'             => ['id',
						'type',
						'file',
						'line',
						'host',
						'url',
						'referer',
						'dt',
						'ip',
						'browser',
						'details'
				],
			//'filter_menu_options' => false,
				'daterangepicker'     => true,
				'id_prefix'           => 'system-errorlogs',
				'plugin'              => 'settings',
				'reports'             => [],
				'top_filter'          => false,
				'searchbar_on_top'    => false,
				'type'                => 'errorlog',
		]);
	}

    public function action_manage_feeds()
    {
        $model = new Model_Feeds();
        $feeds = $model->get_feed_data();

        $this->template->body = View::factory('content/settings/manage_feeds');
        $this->template->body->feeds = $feeds;
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Feeds', 'link' => '/admin/settings/manage_feeds');
        $this->template->sidebar->tools = '<a href="/admin/settings/add_feed"><button type="button" class="btn">Add Feed</button></a>';
    }

    public function action_add_feed()
    {
        $postData = $this->request->post();
        unset($postData['redirect']);
        $plugins = DB::select()->from('engine_plugins')->execute();

        // Run this if the user clicks "Save"
        if ($postData != NULL)
        {
            $feed = new Model_Feeds();
            if (!isset($postData['name']) OR $postData['name'] == '')
            {
                IbHelpers::set_message('Please add a title.', 'error popup_box');
            }
            elseif ($feed->add_feed($postData))
            {
                IbHelpers::set_message('Feed successfully added.', 'success popup_box');
                $this->request->redirect('admin/settings/manage_feeds');
            }
            else
            {
                IbHelpers::set_message('Feed could not be added.', 'error popup_box');
            }
        }

        $this->template->body = View::factory('content/settings/edit_feed');
        $this->template->body->plugins = $plugins;
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Feeds', 'link' => '/admin/settings/manage_feeds');
        $this->template->sidebar->tools = '<a href="/admin/settings/add_feed"><button type="button" class="btn">Add Feed</button></a>';
    }

    public function action_edit_feed()
    {
        $postData = $this->request->post();
        $id = $this->request->param('id');
        if ($id == '') {
            $id = $postData['id'];
        }
        $plugins = DB::select()->from('engine_plugins')->execute();
        $model = new Model_Feeds();


        // Editor view
        if ((!isset($id) OR (int)$id < 1) AND $postData == NULL)
        {
            IbHelpers::set_message('Feed does not exist.', 'error popup_box');
        }
        // Save is clicked
        else
        {
            // Run this if the user clicks "Save"
            if ($postData != NULL)
            {
                if (!isset($postData['name']) OR $postData['name'] == '')
                {
                    IbHelpers::set_message('Please add a title.', 'error popup_box');
                }
                elseif ($model->edit_feed($postData))
                {
                    IbHelpers::set_message('Feed successfully edited.', 'success popup_box');
                    $this->request->redirect($postData['redirect']);
                }
                else
                {
                    IbHelpers::set_message('Feed could not be edited.', 'error popup_box');
                }
            }
            $feed = $model->get_feed_data($id);
            $this->template->body = View::factory('content/settings/edit_feed');
            $this->template->body->feed = $feed;
            $this->template->body->plugins = $plugins;
            $this->template->sidebar->breadcrumbs[] = array('name' => 'Feeds', 'link' => '/admin/settings/manage_feeds');
            $this->template->sidebar->tools = '<a href="/admin/settings/add_feed"><button type="button" class="btn">Add Feed</button></a>';
        }
    }

    public function action_publish_feed()
    {
        $id = $this->request->param('id');
        if (!isset($id) OR (int)$id < 1)
        {
            $msg = '<strong>Error: </strong> Feed does not exist';
        }
        else
        {
            $feed = new Model_Feeds();
            $msg  = $feed->change_publish_status ($id);
        }
        $this->auto_render = FALSE;
        $this->response->body ($msg);
    }

    public function action_delete_feed()
    {
        $id = $this->request->param('id');
        if (!isset($id) OR (int)$id < 1)
        {
            $msg = '<strong>Error: </strong> Feed does not exist';
        }
        else
        {
            $feed = new Model_Feeds();
            $msg = $feed->delete_feed($id);
        }
        $this->response->body($msg);
        $this->auto_render = FALSE;
    }

    public function action_ajax_toggle_app()
    {
        // Temporary
        $this->auto_render = FALSE;
        $data = $this->request->post();
        Cookie::set($data['user_id'].'_application', $data['application']);

        Cookie::get($data['user_id'].'_application');
    }

    public function action_clear_dalm()
    {
        $this->auto_render = false;
        $id = $this->request->param('id');
        Model_DALM::clear_error($id);
        $this->request->redirect('/admin/settings/list_logs');
    }

	public function action_ignore_dalm_error()
	{
		$this->auto_render = false;
		Model_DALM::ignoreQuery(
			$this->request->post('id'),
			base64_decode($this->request->post('query'))
		);
		$this->request->redirect('/admin/settings/list_logs');
	}

    public function action_labels()
    {
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/labels.js"></script>';
        $this->template->sidebar->tools = '<button type="button" class="btn" onclick="add_label();">Add Label</button>';
        $labels = Model_Label::get_all_labels();
        $this->template->body = View::factory( 'content/settings/labels')->bind('labels',$labels);
    }

    public function action_add_label()
    {
        $post = $this->request->post();
        $label = Model_Label::create();
        $label->set_label($post['label']);
        $label->save();
    }

    public function action_crontasks()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Cron', 'link' => '/admin/settings/crontasks');
        $this->template->sidebar->tools = '<a href="/admin/settings/manage_crontask/new" class="btn">Add Cron Task</a>';
        $crontasks = Model_Cron::get_all_crontasks();
        $this->template->body = View::factory('content/settings/list_crontasks')->bind('tasks',$crontasks);
    }

    public function action_manage_crontask()
    {
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/cron.js"></script>';
        $id = $this->request->param('id');
        $crontask = Model_Cron::create($id);
        $frequencies = Model_Cron::get_all_frequencies();
		$availableActions = Model_Cron::getAvailableCronActions();
		$logs = Model_Cron::get_logs($id);
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Cron', 'link' => '/admin/settings/crontasks');
        $this->template->sidebar->breadcrumbs[] = array('name' => $crontask->get_title(), 'link' => '/admin/settings/manage_crontask/' . $id);
		$this->template->sidebar->tools = '<a href="/admin/settings/manage_crontask/new" class="btn">Add Cron Task</a>';
        $this->template->body = View::factory('content/settings/manage_crontasks')
										->bind('frequencies', $frequencies)
										->bind('crontask', $crontask)
										->bind('logs', $logs)
										->bind('availableActions', $availableActions);
    }

    public function action_save_crontask()
    {
        $post = $this->request->post();
        $data = array();
        $data['id'] = $post['id'];
        $data['title'] = $post['title'];
        $data['frequency'] = $post['frequency'];
        $data['plugin_id'] = $post['plugin_id'];
        $data['action'] = $post['controller_action'];
        $data['publish'] = $post['publish'];
		$data['send_email_on_complete'] = $post['send_email_on_complete'];
        $data['extra_parameters'] = $post['extra_parameters'];
        $crontask = Model_Cron::create()->set($data);
        $crontask->save();
        $this->request->redirect('/admin/settings/manage_crontask/'.$crontask->get_id());
    }

    public function action_get_group_settings()
    {
        $data = $this->request->post('group_name');
        $this->auto_render = false;
        $settings = new Model_Settings();
        $forms = $settings->build_form($data);
        $this->response->body(View::factory('content/settings/group_settings')->bind('forms',$forms));
    }

    public function action_csv()
    {
        $data = Model_CSV::get_all_csvs();
        $this->template->sidebar->breadcrumbs[] = array('name' => 'CSV', 'link' => 'admin/settings/csv');
        $this->template->sidebar->tools = '<a href="/admin/settings/manage_csv/new" class="btn">Add CSV Template</a>';
        $this->template->body = View::factory('content/settings/list_csv')->bind('csvs',$data);
    }

    public function action_manage_csv()
    {
        $mysql_tables = Model_CSV::get_all_database_tables();
        $id     = $this->request->param('id');
        $csv    = Model_CSV::create($id);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/csv.js"></script>';
        $this->template->sidebar->tools = '<a href="/admin/settings/manage_csv/new" class="btn">Add CSV Template</a>';
        $this->template->body           = View::factory('content/settings/manage_csv')->bind('csv',$csv)->bind('tables',$mysql_tables);
    }

    public function action_save_csv()
    {
        $this->auto_render = false;
        $data = $this->request->post();
        $csv = Model_CSV::create()->set($data);
        if($csv->save())
        {
            $this->request->redirect('/admin/settings/csv');
        }
        else
        {
            $this->request->redirect('/admin/settings/csv');
        }
    }

    public function action_get_columns_from_table()
    {
        $this->auto_render  = false;
        $data               = $this->request->post('table');
        $response           = Model_CSV::get_all_table_columns($data);
        $this->response->body(json_encode(array('columns' => $response)));
    }

	/*
	 * Setting up permissions for selected group
	 */
	public function action_set_permissions() {

		$role_id = $this->request->param('id');

		$resources_array = array();

		//get all controllers from database (need it for view)
		$controllers = ORM::factory('Resources')->where('type_id', '=', 0)->find_all();

		$role = ORM::factory('Roles', $role_id);

		if(!$role->loaded()) die('Role doesn\'t exist!');

		/*
		 * Select all resources with type "Code" (type_id=2) - see config/resource_types.php
		 */
		$code_pieces = ORM::factory('Resources')->where('type_id','=',2)
		->order_by('name', 'ASC')->find_all();

		if($this->request->method()=='POST') {

			DB::delete('engine_role_permissions')->where('role_id', '=', $role_id)->execute();

			if($this->request->post('resource')) {

				foreach($this->request->post('resource') as $resource_id => $value) {

					DB::insert('engine_role_permissions', array('role_id', 'resource_id'))
						->values(array($role_id, $resource_id))
						->execute();
				}
			}
		}


		$this->template->body = View::factory('content/settings/role_permissions')
			->bind('controllers', $controllers)
			->bind('role', $role)
			->bind('code_pieces', $code_pieces);


	}

	/*
	 * List of resources
	 */
	public function action_manage_resources()
	{
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Resources', 'link' => '/admin/settings/manage_resources');
		$this->template->sidebar->tools = '<a class="btn" href="'.Route::url('admin', array('directory' => 'admin', 'controller' => 'settings', 'action' => 'add_resources')).'">Add new resource</a>';

		$resources = ORM::factory('Resources')->order_by('parent_controller', 'ASC')->find_all();
		$resource_types = Kohana::$config->load('resource_types');

		//echo Debug::vars($resource_types);

		$this->template->body = View::factory('content/settings/list_resources')
			->bind('resources', $resources)
			->bind('resource_types', $resource_types);
	}

	public function action_add_resources() {

		$this->template->sidebar->breadcrumbs[] = array('name' => 'Resources', 'link' => '/admin/settings/manage_resources');

		//getting all controllers to create controller select in view
		$controllers = DB::select()->from('engine_resources')->where('type_id', '=', 0)->execute()->as_array('id', 'name');
		$this->template->body = View::factory('content/settings/add_resource')
			->bind('controllers', $controllers);

		$resource = ORM::factory('Resources', $this->request->param('id'));

		if($resource->loaded()) {
			//if editing resource
			$data = $resource->as_array();
		} else {
			//if creating new resource
			$data = $this->request->post();
		}

		//passing data to view
		$this->template->body->data = $data;

		//if form submitted
		if($this->request->method()=='POST') {

			//validation. Details in model_resource
			$validated = $resource->resourceValidation($this->request->post());

			if($validated->check()) {

				$resource->values($this->request->post());
				$resource->save();

				IbHelpers::set_message('Resource saved!', 'success popup_box');

			} else {
				//taking errors from messages directory
				$error = $validated->errors('resources');
				$error = reset($error); //get first error

				//sending error toview
				$this->template->body->alert = IbHelpers::alert($error, 'error popup_box');
			}
		}
	}//End of add_resources action

	/*
	 * Activities
	 */
	public function action_activities()
	{
		if ( ! Auth::instance()->has_access('settings_activities'))
		{
			$this->request->redirect('/admin/settings/my_activities');
		}
		else
		{
			$activities = array();
			$this->template->sidebar->breadcrumbs[] = array('name' => 'Activities', 'link' => '/admin/settings/activities');
			$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'/js/list_activities.js"></script>';
			$this->template->body = View::factory('content/settings/list_activities')
				->set('server_side', TRUE);
		}
	}

	public function action_my_activities()
	{
		$activities = array();
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Activities', 'link' => '/admin/settings/my_activities');
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'/js/list_activities.js"></script>';
		$this->template->body = View::factory('content/settings/list_activities')
			->set('activities', $activities)
			->set('server_side', TRUE);
	}

	// Get one page of activities for a datatable.
	// Serverside, so thousands of records aren't added to the DOM on the initial page load
	public function action_ajax_get_activities_datatable()
	{
		$this->auto_render = FALSE;
		$this->response->body(Model_Activity::get_for_datatable($this->request->query()));
	}

	// Set cookies to remember if the sidebars are expanded or collapsed for the user
	public function action_ajax_save_sidebar_state()
	{
		$this->auto_render = FALSE;
		$expand = (bool) $this->request->param('id');
		$user = Auth::instance()->get_user();
		if ( ! $expand)
		{
			Cookie::set($user['id'].'_hide_sidebar', '1');
			Cookie::set($user['id'].'_hide_bulletin', '1');
		}
		else
		{
			Cookie::delete($user['id'].'_hide_sidebar');
			Cookie::delete($user['id'].'_hide_bulletin');
		}
	}

	public function action_ipwatcher_log()
	{
		$this->template->sidebar->breadcrumbs[] = array('name' => 'IP Watcher', 'link' => '/admin/settings/ipwatcher_log');
		$this->template->sidebar->menus = array(array(
			array('name' => 'Log', 'link' => '/admin/settings/ipwatcher_log'),
			array('name' => 'Blacklist', 'link' => '/admin/settings/ipwatcher_blacklist'),
			array('name' => 'Whitelist', 'link' => '/admin/settings/ipwatcher_whitelist'),
			array('name' => 'UserAgent Whitelist', 'link' => '/admin/settings/ipwatcher_ua_whitelist'),
			array('name' => 'GeoIP DB', 'link' => '/admin/settings/ipwatcher_geoipdb')
		));
		$this->template->body = View::factory('content/settings/ipwatcher_log');
	}

	public function action_ipwatcher_blacklist()
	{
		$post = $this->request->post();
		if(isset($post['block'])){
			try {
				if (!file_exists(Kohana::$cache_dir . '/geoip/GeoLite2-City.mmdb')) {
					Model_Ipwatcher::update_geoip_db();
				}
			} catch (Exception $exc) {

			}

			$logged_in_user = Auth::instance()->get_user();
			Model_Ipwatcher::block($post['ip'], $logged_in_user['id'], $post['reason']);
			IbHelpers::set_message($post['ip'] . ' has been added to blacklist', 'success popup_box');
		}
		$this->template->sidebar->breadcrumbs[] = array('name' => 'IP Watcher', 'link' => '/admin/settings/ipwatcher_log');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Blacklist', 'link' => '/admin/settings/ipwatcher_blacklist');
		$this->template->sidebar->menus = array(array(
			array('name' => 'Log', 'link' => '/admin/settings/ipwatcher_log'),
			array('name' => 'Blacklist', 'link' => '/admin/settings/ipwatcher_blacklist'),
			array('name' => 'Whitelist', 'link' => '/admin/settings/ipwatcher_whitelist'),
			array('name' => 'UserAgent Whitelist', 'link' => '/admin/settings/ipwatcher_ua_whitelist'),
			array('name' => 'GeoIP DB', 'link' => '/admin/settings/ipwatcher_geoipdb')
		));
		$this->template->body = View::factory('content/settings/ipwatcher_blacklist');
	}
	
	public function action_ipwatcher_whitelist()
	{
		$post = $this->request->post();
		if(isset($post['add'])){
			$logged_in_user = Auth::instance()->get_user();
			Model_Ipwatcher::whitelist_add($post['ip'], $logged_in_user['id'], $post['reason']);
			IbHelpers::set_message($post['ip'] . ' has been added to whitelist', 'success popup_box');
		}
		$this->template->sidebar->breadcrumbs[] = array('name' => 'IP Watcher', 'link' => '/admin/settings/ipwatcher_log');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Whitelist', 'link' => '/admin/settings/ipwatcher_whitelist');
		$this->template->sidebar->menus = array(array(
			array('name' => 'Log', 'link' => '/admin/settings/ipwatcher_log'),
			array('name' => 'Blacklist', 'link' => '/admin/settings/ipwatcher_blacklist'),
			array('name' => 'Whitelist', 'link' => '/admin/settings/ipwatcher_whitelist'),
			array('name' => 'UserAgent Whitelist', 'link' => '/admin/settings/ipwatcher_ua_whitelist'),
			array('name' => 'GeoIP DB', 'link' => '/admin/settings/ipwatcher_geoipdb')
		));
		$this->template->body = View::factory('content/settings/ipwatcher_whitelist');
	}
	
	public function action_ipwatcher_ua_whitelist()
	{
		$post = $this->request->post();
		if(isset($post['add'])){
			$logged_in_user = Auth::instance()->get_user();
			Model_Ipwatcher::ua_whitelist_add($post['user_agent'], $logged_in_user['id'], $post['reason']);
			IbHelpers::set_message($post['user_agent'] . ' has been added to useragent whitelist', 'success popup_box');
		}
		$this->template->sidebar->breadcrumbs[] = array('name' => 'IP Watcher', 'link' => '/admin/settings/ipwatcher_log');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'UserAgent Whitelist', 'link' => '/admin/settings/ipwatcher_ua_whitelist');
		$this->template->sidebar->menus = array(array(
			array('name' => 'Log', 'link' => '/admin/settings/ipwatcher_log'),
			array('name' => 'Blacklist', 'link' => '/admin/settings/ipwatcher_blacklist'),
			array('name' => 'Whitelist', 'link' => '/admin/settings/ipwatcher_whitelist'),
			array('name' => 'UserAgent Whitelist', 'link' => '/admin/settings/ipwatcher_ua_whitelist'),
			array('name' => 'GeoIP DB', 'link' => '/admin/settings/ipwatcher_geoipdb')
		));
		$this->template->body = View::factory('content/settings/ipwatcher_ua_whitelist');
	}
	
	public function action_ipwatcher_unblock()
	{
		Model_Ipwatcher::unblock($this->request->query('ip'));
		IbHelpers::set_message($this->request->query('ip') . ' has been removed from blacklist', 'success popup_box');
		$this->request->redirect('/admin/settings/ipwatcher_blacklist');
	}
	
	public function action_ipwatcher_whitelist_remove()
	{
		Model_Ipwatcher::whitelist_remove($this->request->query('ip'));
		IbHelpers::set_message($this->request->query('ip') . ' has been removed from whitelist', 'success popup_box');
		$this->request->redirect('/admin/settings/ipwatcher_whitelist');
	}

	public function action_ipwatcher_ua_whitelist_remove()
	{
		Model_Ipwatcher::ua_whitelist_remove($this->request->query('user_agent'));
		IbHelpers::set_message($this->request->query('user_agent') . ' has been removed from useragent whitelist', 'success popup_box');
		$this->request->redirect('/admin/settings/ipwatcher_ua_whitelist');
	}

	public function action_ipwatcher_ajax_get_log_datatable()
	{
		$this->auto_render = FALSE;
		$this->response->body(Model_Ipwatcher::get_log_datatable($this->request->query()));
	}

	public function action_ipwatcher_ajax_get_blacklist_datatable()
	{
		$this->auto_render = FALSE;
		$this->response->body(Model_Ipwatcher::get_blacklist_datatable($this->request->query()));
	}
	
	public function action_ipwatcher_ajax_get_whitelist_datatable()
	{
		$this->auto_render = FALSE;
		$this->response->body(Model_Ipwatcher::get_whitelist_datatable($this->request->query()));
	}
	
	public function action_ipwatcher_ajax_get_ua_whitelist_datatable()
	{
		$this->auto_render = FALSE;
		$this->response->body(Model_Ipwatcher::get_ua_whitelist_datatable($this->request->query()));
	}

	public function action_ipwatcher_geoipdb()
	{
		if($this->request->query('update')){
			Model_Ipwatcher::update_geoip_db();
		}
		$this->template->sidebar->breadcrumbs[] = array('name' => 'IP Watcher', 'link' => '/admin/settings/ipwatcher_log');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'GeoIP DB', 'link' => '/admin/settings/ipwatcher_geoipdb');
		$this->template->sidebar->menus = array(array(
			array('name' => 'Log', 'link' => '/admin/settings/ipwatcher_log'),
			array('name' => 'Blacklist', 'link' => '/admin/settings/ipwatcher_blacklist'),
			array('name' => 'UserAgent Whitelist', 'link' => '/admin/settings/ipwatcher_ua_whitelist'),
			array('name' => 'GeoIP DB', 'link' => '/admin/settings/ipwatcher_geoipdb')
		));
		$this->template->body = View::factory('content/settings/ipwatcher_geoipdb');
		$this->template->body->geoip_db_status = Model_Ipwatcher::geoip_db_status();
		if($this->request->query('ip')){
			$this->template->body->ip = $this->request->query('ip');
			$this->template->body->geocity = Model_Ipwatcher::get_geoip($this->request->query('ip'));
		}
	}
	
	public function action_keyboardshortcuts()
	{
		$post = $this->request->post();
		if(isset($post['save'])){
			Model_Keyboardshortcut::save_all($post);
		}
		$shortcuts = Model_Keyboardshortcut::get_all();
		$this->template->body = View::factory('content/settings/list_keyboardshortcuts');
		$this->template->body->shortcuts = $shortcuts;
	}
	
	public function action_localisation_config()
	{
		$post = $this->request->post();
		if(isset($post['update'])){
			$new_settings = array();
			$new_settings['localisation_content_active'] = isset($post['localisation_content_active']) ? $post['localisation_content_active'] : 0;
			$new_settings['localisation_system_active'] = isset($post['localisation_system_active']) ? $post['localisation_system_active'] : 0;
			$new_settings['localisation_content_default_language'] = isset($post['localisation_content_default_language']) ? $post['localisation_content_default_language'] : '';
			$new_settings['localisation_system_default_language'] = isset($post['localisation_system_default_language']) ? $post['localisation_system_default_language'] : '';
			
			$model_settings = new Model_Settings();
			$model_settings->update($new_settings);
			$this->request->redirect('/admin/settings/localisation_config');
		}
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Localisation', 'link' => '/admin/settings/localisation_config');
		$this->template->sidebar->menus = array(array(
			array('name' => 'Localisation', 'link' => '/admin/settings/localisation_config'),
			array('name' => 'Languages', 'link' => '/admin/settings/localisation_languages'),
			array('name' => 'Translations', 'link' => '/admin/settings/localisation_system')
		));
		$this->template->body = View::factory('content/settings/localisation_config');
	}
	
	public function action_localisation_languages()
	{
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Localisation', 'link' => '/admin/settings/localisation_config');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Languages', 'link' => '/admin/settings/localisation_languages');
		$this->template->sidebar->menus = array(array(
			array('name' => 'Localisation', 'link' => '/admin/settings/localisation_config'),
			array('name' => 'Languages', 'link' => '/admin/settings/localisation_languages'),
			array('name' => 'Translations', 'link' => '/admin/settings/localisation_system')
		));
		$this->template->body = View::factory('content/settings/localisation_languages');
		$this->template->body->languages = Model_Localisation::languages_list();
	}
	
	public function action_localisation_language_add()
	{
		$post = $this->request->post();
		if(isset($post['add'])){
			$id = Model_Localisation::language_add($post['code'], $post['title']);
			if($id){
				IbHelpers::set_message($post['title'] . ' has been added', 'success popup_box');
			} else {
				IbHelpers::set_message($post['title'] . ' was not added', 'error popup_box');
			}
		}
		$this->request->redirect('/admin/settings/localisation_languages');
	}
	
	public function action_localisation_language_remove()
	{
		$post = $this->request->post();
		if(isset($post['remove'])){
			$result = Model_Localisation::language_remove($post['language_id']);
			if($result){
				IbHelpers::set_message($post['language_id'] . ' has been removed', 'success popup_box');
			} else {
				IbHelpers::set_message($post['language_id'] . ' was not removed', 'error popup_box');
			}
		}
		$this->request->redirect('/admin/settings/localisation_languages');
	}
	
	public function action_localisation_system()
	{
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Localisation', 'link' => '/admin/settings/localisation_config');
		$this->template->sidebar->breadcrumbs[] = array('name' => 'System', 'link' => '/admin/settings/localisation_system');
		$this->template->sidebar->menus = array(array(
			array('name' => 'Localisation', 'link' => '/admin/settings/localisation_config'),
			array('name' => 'Languages', 'link' => '/admin/settings/localisation_languages'),
			array('name' => 'Translations', 'link' => '/admin/settings/localisation_system')
		));
		$this->template->body = View::factory('content/settings/localisation_system');
		$this->template->body->languages = Model_Localisation::languages_list();
	}
	
	public function action_localisation_system_scan()
	{
		$success = Model_Localisation::message_scan();
		echo "scan completed:" . ($success ? 'yes' : 'no');
		exit();
	}
	
	public function action_localisation_system_import()
	{
		$languages = I18n::get_allowed_languages();
		foreach($languages as $language){
			Model_Localisation::language_add($language, I18n::$languageCodes[$language]);
			I18n::import_from_files($language);
		}
		echo "import completed";
		exit();
	}

	public function action_localisation_export()
	{
		$data = Model_Localisation::get_translations_datatable(array('sEcho' => 1, 'noinput' => true));
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename=translations-' . date('YmdHis') . '.csv');
        $tmpname = tempnam('/tmp', 'csvexport');
        IbHelpers::save_csv($data['aaData'], $tmpname);
        readfile($tmpname);
        unlink($tmpname);
		exit();
	}

	public function action_localisation_importcsv()
	{
        if (@$_FILES['csv']) {
            $csv = Kohana::$cache_dir . '/' . date('YmdHis') . '-tmp.csv';
            move_uploaded_file($_FILES['csv']['tmp_name'], $csv);
            $fcsv = fopen($csv, "r");
            $first = fgetcsv($fcsv, null, ',', '"');
            $lang_ids = [];
            foreach ($first as $li => $code) {
                $lang_id = DB::select('id')->from('engine_localisation_languages')->where('code', '=', $code)->execute()->get('id');
                if ($lang_id) {
                    $lang_ids[$lang_id] = $li;
                }
            }

            while(!feof($fcsv)) {
                $row = fgetcsv($fcsv, null, ',', '"');
                $message_id = DB::select("id")
                    ->from('engine_localisation_messages')
                    ->where('message', '=', $row[0])
                    ->execute()
                    ->get('id');
                if (!$message_id) {
                    $inserted = DB::insert('engine_localisation_messages')->values(array('message' => $row[0]))->execute();
                    $message_id = $inserted[0];
                }
                foreach ($lang_ids as $lang_id => $col) {
                    try {
                        DB::insert('engine_localisation_translations')
                            ->values(
                                array(
                                    'message_id' => $message_id,
                                    'language_id' => $lang_id,
                                    'translation' => $row[$col]
                                )
                            )->execute();
                    } catch (Exception $exc) {
						if (stripos($exc->getMessage(), 'Duplicate entry') !== false) {
							DB::update('engine_localisation_translations')
								->set(array('translation' => $row[$col]))
								->where('message_id', '=', $message_id)
								->and_where('language_id', '=', $lang_id)
								->execute();
						}

                    }
                }
            }

            fclose($fcsv);
            unlink($csv);
            $this->request->redirect('/admin/settings/localisation_system');
        }
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Localisation', 'link' => '/admin/settings/localisation_config');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Import CSV', 'link' => '/admin/settings/localisation_importcsv');
        $this->template->sidebar->menus = array(array(
            array('name' => 'Localisation', 'link' => '/admin/settings/localisation_config'),
            array('name' => 'Languages', 'link' => '/admin/settings/localisation_languages'),
            array('name' => 'Translations', 'link' => '/admin/settings/localisation_system')
        ));
        $this->template->body = View::factory('content/settings/localisation_importcsv');
        $this->template->body->languages = Model_Localisation::languages_list();
	}
	
	public function action_localisation_ajax_get_translations_datatable()
	{
		$this->auto_render = FALSE;
		$this->response->body(json_encode(Model_Localisation::get_translations_datatable($this->request->query())));
	}

	public function action_localisation_translations_update()
	{
		$post = $this->request->post();
		$changes = json_decode($post['changes'], true);
		$response = Model_Localisation::translations_bulk_update($changes);
		echo "updated";
		exit();
	}

	public function action_localisation_clearall()
	{
		Model_Localisation::clearAll();
		echo "updated";
		exit();
	}

	public function action_dalm_audit()
	{
		header('content-type: text/plain; charset=utf-8');
		$report = Model_DALM::db_audit(!$this->request->query('refresh'));
		//print_r($report);exit();
        $this->template->body = View::factory('content/settings/dalm_audit');
        $this->template->body->report = $report;
		//exit();
	}

	public function action_direct_edit()
	{
		if (!preg_match('/\.dev$/i', $_SERVER['HTTP_HOST'])){ // allow only on .dev
			exit();
		}
		$file = $this->request->query('file');
		if ($content = $this->request->post('content')) {
			$file = $this->request->post('file');
			file_put_contents($file, $content);
			echo $file . " has been updated";
			exit();
		}
		$find = $this->request->query('find');
		$content = file_get_contents($file);
		header('content-type: text/plain; charset=utf-8');
		echo $content;
		exit();
	}

	public function action_phpinfo()
	{
		phpinfo();exit();
	}

    public function action_session()
    {
        header('content-type: text/plain');print_r($_SESSION);exit();
    }

	public function action_apcinfo()
	{
		header('content-type: text/plain');print_r(apc_cache_info('user'));exit;
	}

	public function action_fix_sqlsecurity()
	{
		Model_Dalm::replaceViewSqlSecurityDefiners();
        Model_Dalm::replaceRoutineSqlSecurityDefiners();
        IbHelpers::set_message('SQL SECURITY \'DEFINER\'s has been replaced with \'INVOKER\'s', 'info popup_box');
        /*$this->auto_render = false;
        $this->response->headers('Content-Type', 'text/plain; charset=utf-8');
        echo 'done';*/
        $this->request->redirect('/admin/settings/list_logs');
    }

	public function action_ajax_get_submenu($data_only = false)
	{
		if ( ! Auth::instance()->has_access('settings')) {
            if ($data_only) {
                $this->response->body('');
            } else {
                return null;
            }
		}
		else {
			$return = array(
				'link'  => '',
				'items' => array(
					array('id' => '../show_logs', 'title' => 'App logs'),
					array('id' => '../dbsync', 'title' => 'DB Sync'),
					array('id' => '../automations', 'title' => 'Automations'),
					array('id' => '../../calendars/index', 'title' => 'Calendar'),
					array('id' => '../crontasks', 'title' => 'Cron'),
					array('id' => '../csv', 'title' => 'CSV'),
					array('id' => '../list_logs', 'title' => 'Dalm'),
					array('id' => '../manage_feeds', 'title' => 'Feeds'),
					array('id' => '../ipwatcher_log', 'title' => 'IP Watcher'),
					array('id' => '../keyboardshortcuts', 'title' => 'Shortcuts'),
					array('id' => '../labels', 'title' => 'Labels'),
					array('id' => '../localisation_config', 'title' => 'Localisation'),
					array('id' => '../redirects', 'title' => 'Redirects'),
					array('id' => '../manage_resources', 'title' => 'Resources'),
					array('id' => '../manage_roles', 'title' => 'User Roles'),
					array('id' => '../../users', 'title' => 'Users'),
				)
			);

			$return = array(
					'link'  => '',
					'items' => array()
			);

            if ($data_only) {
                return $return;
            } else {
                $this->auto_render = false;
                $this->response->headers('Content-type', 'application/json; charset=utf-8');
                $this->response->body(json_encode($return));
            }
		}
	}

	public function action_clear_errorlog()
	{
		$this->auto_render = false;
		Model_Errorlog::delete_old($this->request->post('type'), $this->request->post('before'));
		echo 'done';
	}

    public function action_website()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Website', 'link' => '/admin/settings/website');

        $this->template->body = View::factory('settings/website_management');
    }

    /*
     * Layouts functions
     */
    public function action_layouts()
    {
        $this->template->sidebar->tools = '<a href="/admin/settings/edit_layout" class="btn">Create Layout</a>';
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Website', 'link' => '/admin/settings/website');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Layouts', 'link' => '/admin/settings/layouts');

        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('pages').'js/list_layouts.js"></script>';
        $this->template->body      = View::factory('settings/list_layouts');
    }

    // Edit layout screen
    public function action_edit_layout($clone = false)
    {
        $this->template->sidebar->tools = '<a href="/admin/settings/edit_layout" class="btn">Create Layout</a>';
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Website', 'link' => '/admin/settings/website');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Layouts', 'link' => '/admin/settings/layouts');

        $this->template->styles    = array_merge($this->template->styles, array (URL::get_engine_assets_base().'js/codemirror/merged/codemirror.css' => 'screen'));
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/codemirror/merged/codemirror.js"></script>';

        $id               = $this->request->param('id');
        $layout           = ORM::factory('Engine_Layout', $id)->where('deleted', '=', 0);
        $templates        = ORM::factory('Engine_Template')->order_by('title')->find_all_undeleted();
        $default_template = ORM::factory('Engine_Template')->where('stub', '=', Settings::instance()->get('template_folder_path'))->find_undeleted();

        // If this is a clone, unset the ID, so the original doesn't get overwritten. Append " -clone" to the title give it a new name.
        if ($clone) {
            $layout->set('id', null);
            $layout->set('layout', $layout->layout.' - clone');
        }

        // Put the layout data in a view
        $this->template->body = View::factory('settings/edit_layout')
            ->set('default_template', $default_template)
            ->set('layout',           $layout)
            ->set('templates',        $templates
            );
    }

    public function action_clone_layout()
    {
        self::action_edit_layout(true);
    }

    public function action_save_layout()
    {
        // Get the layout as an object
        $id     = $this->request->param('id');
        $layout = ORM::factory('Engine_Layout', $id);

        // Set new data and save
        $layout->values($this->request->post());
        $saved = $layout->save_with_moddate();

        // Display message
        if ($saved) {
            IbHelpers::set_message('Layout #'.$layout->id.': &quot;'.$layout->layout.'&quot; saved.', 'success popup_box');
        }
        else {
            IbHelpers::set_message('Error saving layout.', 'danger popup_box');
        }

        // Redirect
        $redirect = ($this->request->post('redirect') != '') ? $this->request->post('redirect')  : '/admin/settings/edit_layout/'.$layout->id;
        $this->request->redirect($redirect);
    }

    public function action_dbsync()
    {
        $this->template->body = View::factory('content/settings/dbsync');
    }

    public function action_delete_layout()
    {
        $id = $this->request->param('id');

        try {
            $layout = new Model_Engine_Layout($id);

            $layout->set('deleted', 1);
            $layout->save_with_moddate();

            IbHelpers::set_message('Layout #'.$layout->id.': &quot;'.$layout->layout.'&quot; deleted.', 'success popup_box');

            $this->request->redirect('/admin/settings/layouts');
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error deleting layout.\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Error deleting layout #'.$id.'. See the application logs for more information.', 'danger popup_box');
            $this->request->redirect('/admin/settings/edit_layout/'.$id);
        }
    }

    /* Get data for the datatables serverside */
    public function action_ajax_get_layouts_datatable()
    {
        $this->auto_render = false;
        $this->response->body(Model_Engine_Layout::get_for_datatable($this->request->query()));
    }

    public function action_ajax_delete_layout()
    {
        $this->auto_render = false;
        $id = $this->request->param('id');

        try {
            $layout = new Model_Engine_Layout($id);
            if ($layout->id) {
                $layout->set('deleted', 1);
                $layout->save_with_moddate();
            }
            echo 1;
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error deleting layout.\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Error deleting layout #'.$id.'. See the application logs for more information.', 'danger popup_box');
            echo 0;
        }
    }

    public function action_ajax_toggle_layout_publish()
    {
        $this->auto_render = false;
        $id = $this->request->param('id');

        try {
            $layout = ORM::factory('Engine_Layout')->where('id', '=', $id)->find_undeleted();

            if ($layout->id) {
                $layout->set('publish', (($layout->publish + 1) % 2));
                $layout->save_with_moddate();
            }

            echo $layout->publish;
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error publishing/unpublishing layout.\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Error changing layout #'.$id.'. See the application logs for more information.', 'danger popup_box');
            echo '-1';
        }
    }

    /**
     * Templates
     */
    public function action_templates()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Website', 'link' => '/admin/settings/website');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Templates', 'link' => '/admin/settings/templates');
        $this->template->sidebar->tools = '<a href="/admin/settings/edit_template" class="btn">Create Template</a>';

        $templates = ORM::factory('Engine_Template')->order_by('date_modified', 'desc')->find_all_undeleted();
        $this->template->body = View::factory('settings/list_templates')->set('templates', $templates);
    }

    public function action_edit_template($clone = false)
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Website', 'link' => '/admin/settings/website');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Templates', 'link' => '/admin/settings/templates');
        $this->template->sidebar->tools = '<a href="/admin/settings/edit_template" class="btn">Create Template</a>';

        $this->template->styles    = array_merge($this->template->styles, array (URL::get_engine_assets_base().'js/codemirror/merged/codemirror.css' => 'screen'));
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/codemirror/merged/codemirror.js"></script>';

        $id = $this->request->param('id');
        $template = ORM::factory('Engine_Template')->where('id', '=', $id)->find_undeleted();

        if ($clone) {
            $template->set('id', null);
            $template->set('stub', $template->stub.'_clone');
            $template->set('title', $template->title.' - clone');
        }

        $this->template->body = View::factory('settings/edit_template')->set('template', $template);
    }

    public function action_clone_template()
    {
        self::action_edit_template(true);
    }

    public function action_save_template()
    {
        $id = $this->request->param('id');
        try {
            $template = ORM::factory('Engine_Template', $id);
            $is_new   = empty($template->id);
            $template->values($this->request->post());
            $template->set('type', 'website');
            $template->save_with_moddate();

            $id = $template->id;

            IbHelpers::set_message('Template #'.$template->id.': &quot;'.$template->title.'&quot; saved.', 'success popup_box');

            // If this is a new template, create a "home" layout for it
            if ($is_new) {
                $layout = new Model_Engine_Layout();
                $layout->set('layout', 'home');
                $layout->set('template_id', $template->id);
                $layout->set('use_db_source', 1);
                $layout->save_with_moddate();

                IbHelpers::set_message('Layout #'.$layout->id.': &quot;'.$layout->layout.'&quot; saved.', 'success popup_box');
            }
        }
        catch (Exception $e) {
            IbHelpers::set_message('Error saving template. See the application logs for more information.', 'danger popup_box');
            Log::instance()->add(Log::ERROR, "Error saving template.\n".$e->getMessage()."\n".$e->getTraceAsString());
        }


        $this->request->redirect('/admin/settings/edit_template/'.$id);
    }

    /**
     * Themes
     */
    public function action_themes()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Website', 'link' => '/admin/settings/website');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Themes', 'link' => '/admin/settings/themes');
        $this->template->sidebar->tools = '<a href="/admin/settings/edit_theme" class="btn">Create Theme</a>';

        $themes = ORM::factory('Engine_Theme')->order_by('date_modified', 'desc')->find_all_undeleted();
        $this->template->body = View::factory('settings/list_themes')->set('themes', $themes);
    }

    public function action_edit_theme($clone = false)
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Website', 'link' => '/admin/settings/website');
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Themes', 'link' => '/admin/settings/themes');
        $this->template->sidebar->tools = '<a href="/admin/settings/edit_theme" class="btn">Create Theme</a>';

        $this->template->styles    = array_merge($this->template->styles, array (URL::get_engine_assets_base().'js/codemirror/merged/codemirror.css' => 'screen'));
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/codemirror/merged/codemirror.js"></script>';

        $id              = $this->request->param('id');
        $theme           = ORM::factory('Engine_Theme')->where('id', '=', $id)->find_undeleted();
        // Get all variables
        $variables       = ORM::factory('Engine_Theme_Variable')->find_all_published()->as_array('id');
        // Get the values this theme has selected for any of the variables it may have filled out
        // Use "variable_id" as the array key to make it easier to compare with the array of all variables
        $theme_variables = $theme->variables->find_all()->as_array('variable_id');
        $templates       = ORM::factory('Engine_Template')->find_all_undeleted();

        if ($clone) {
            $theme->set('id', null);
            $theme->set('stub', $theme->stub.'_clone');
            $theme->set('title', $theme->title.' - clone');
        }

        $this->template->body = View::factory('settings/edit_theme')
            ->set('templates', $templates)
            ->set('theme', $theme)
            ->set('theme_variables', $theme_variables)
            ->set('variables', $variables)
        ;
    }

    public function action_clone_theme()
    {
        self::action_edit_theme(true);
    }

    public function action_save_theme()
    {
        $id = $this->request->param('id');
        $post = $this->request->post();

        try {
            $theme = new Model_Engine_Theme($id);
            $theme->values($post);
            $theme->save_with_variables($post['variables']);
            $id = $theme->id;

            IbHelpers::set_message('Theme '.$theme->title.' saved.', 'success popup_box');
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error saving theme.\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Error saving theme. See the application logs for more information.', 'danger popup_box');
        }

        $this->request->redirect('/admin/settings/edit_theme/'.$id);
    }


}
