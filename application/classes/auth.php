<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Auth extends Kohana_Auth {

    public static $run_after_login = array();

	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 * Uses remote authentication if the user email address is an ideabubble address
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE) {
		if (empty($password))
			return FALSE;

		// check if the email address is @ideabubble.ie. If so use remote login
		$domain = explode('@', $username);

//		if (isset($domain[1]) && $domain[1] === 'ideabubble.ie')
//		{
//			exit('To do: Remote logins&hellip;');
//		}
//		a:17:{s:2:"id";s:2:"32";s:7:"role_id";s:1:"1";s:8:"group_id";N;s:5:"email";s:20:"peter@ideabubble.com";s:8:"password";s:64:"35115efd4dade421f24d40816dd5d0380600dd3e65e264986ba6a18a8e64bc69";s:6:"logins";s:3:"143";s:10:"last_login";s:10:"1344442649";s:11:"logins_fail";s:1:"0";s:9:"last_fail";N;s:4:"name";s:5:"Peter";s:7:"surname";s:0:"";s:7:"address";s:0:"";s:5:"phone";s:0:"";s:10:"registered";s:19:"2012-05-03 15:34:15";s:9:"can_login";s:1:"1";s:7:"deleted";s:1:"0";s:9:"idle_time";i:1344447215;}
				// Store username in session
//		$this->_session->set($this->_config['session_key'], $user);

		$login = $this->_login($username, $password, $remember);

		if ($login)
		{
			$activity = new Model_Activity();
			$activity->set_item_type('user')->set_action('login')->save();

            foreach (self::$run_after_login as $call) {
                call_user_func($call);
            }
		}

		return $login;
	}

    public function login_with_external_provider($user, $remember = true) {

        $login = $this->_login_with_external_provider($user, $remember);

        if ($login)
        {
            $activity = new Model_Activity();
            $activity->set_item_type('user')->set_action('login')->save();
        }

        return $login;
    }

	/*
	 * 	PERMISSION BASED FUNCTIONS
	 */

	// Return bool on role id match - can be used to check if role id is the same as the users role id
	public function check_role_bool($roleId) {
		$currentUser = Auth::instance()->get_user();
		if ($roleId == $currentUser['role_id']){
			return true;
		}
		return false;
	}

	// Check to see if the given role code is in the permissions table
	// -----------------------
	// Corrected version is: have_permission($code)
	// migrate to that new function.public
	// Current function should be abandoned and deleted.public

	public function check_permission_code($code) {
		if (Kohana::$environment != Kohana::PRODUCTION) {
			throw new Exception ('check_permission_code is deprecated. use has_access instead');
		}
		//Get the users current users Role_ID
		$var = Auth::instance()->get_user();
		$Users_role_id = $var['role_id'];

		//Get the current users role id and assign it to the $result_array[]
		$permission_check = DB::select()
				->from('engine_permissions')
				->where('role_id', '=', $Users_role_id)
				->and_where('permission_code', '=', $code)
				->execute();
		//IbHelpers::die_r($permission_check);

		if ($permission_check->count() > 0 || $this->check_for_super_level() == 'TRUE')
		{
			$return = 'TRUE';
		}
		else
		{
			$return = 'FALSE';
		}

		return $return;
	}

	// Check to see if the given role code is in the permissions table
	public static function have_permission($code) {
		if (Kohana::$environment != Kohana::PRODUCTION) {
			throw new Exception ('have_permission is deprecated. use has_access instead');
		}

		//Get the users current users Role_ID
		$user = Auth::instance()->get_user();
		$Users_role_id = $user['role_id'];

		//Get the current users role id and assign it to the $result_array[]
		$permission_check = DB::select()
				->from('engine_permissions')
				->where('role_id', '=', $Users_role_id)
				->and_where('permission_code', '=', $code)
				->execute();

		if ($permission_check->count() > 0 || Auth::instance()->check_for_super_level() == 'TRUE')
		{
		 	return true;
		}
		else
		{
			return false;
		}
	}

	// Check to see if the given user role has super user access
	public function check_for_super_level() {

		//Get the users current users Role_ID
		$var = Auth::instance()->get_user();
		$Users_role_id = $var['role_id'];

		//Get the current users role id and assign it to the $result_array[]
		$permission_check = DB::select()
				->from('engine_permissions')
				->where('role_id', '=', $Users_role_id)
				->and_where('permission_code', '=', 'super_level')
				->execute();

		if ($permission_check->count() > 0)
		{
			$return = 'TRUE';
		}
		else
		{
			$return = 'FALSE';
		}

		return $return;
	}

	protected static $permission_cache = null;
	protected static $resource_cache = null;
	/*
         * Check group access to resource
         */
	public function has_access($resource_alias, $dieNotFound = true, $cached_result = true)
	{
		if(!$resource_alias) return true;
		$user = Auth::instance()->get_user();
		return $this->role_has_access($user['role_id'], $resource_alias, $dieNotFound, $cached_result);
	}
	
	public function role_has_access($role_id, $resource_alias, $dieNotFound = true, $cached_result = true)
    {
        if(!$resource_alias) return true;
    
        if ($cached_result) {
            if (self::$permission_cache === null) {
                self::$permission_cache = DB::select('*')
                    ->from('engine_role_permissions')
                    ->execute()
                    ->as_array();
            
            }
            if (self::$resource_cache === null) {
                $resources_ = DB::select('*')
                    ->from('engine_resources')
                    ->execute()
                    ->as_array();
                self::$resource_cache = array();
                foreach ($resources_ as $resource) {
                    self::$resource_cache[$resource['alias']] = $resource;
                }
            }
        
            $resource_id = @self::$resource_cache[$resource_alias]['id'];
            if (!$resource_id) {
                return false;
            }
        
            foreach (self::$permission_cache as $permission) {
                if ($permission['resource_id'] == $resource_id && $role_id == $permission['role_id']) {
                    return true;
                }
            }
            return false;
        
        } else {
            //echo Debug::vars($resource_alias);
            $resource = ORM::factory('Resources')->where('alias', '=', $resource_alias)->find();
    
            if ($dieNotFound && !$resource->loaded()) {
                return false;
            }
    
            $result = (bool)(int)DB::select(array('COUNT("*")', 'total'))
                ->from('engine_role_permissions')
                ->where('role_id', '=', $role_id)
                ->and_where('resource_id', '=', $resource->id)
                ->execute()->get('total');
            //echo Debug::vars($user['group_id']);
            //echo Debug::vars($resource->id);
    
            return $result;
        }
    }

    public function reload_user_data()
    {
		$user = self::get_user();
		$user = Model_Users::get_user($user['id']);
		$cfg = Kohana::$config->load('auth');
		$user['idle_time'] = time() + $cfg['idle_time'];
		Session::instance()->set($cfg['session_key'], $user);
        //$this->_session->delete($this->_config['session_key']);
        //$this->_session->regenerate();
        //$this->_session = Session::instance($this->_config['session_type']);
    }
}