<?php defined('SYSPATH') or die('No direct script access.');

class Model_Roles extends ORM {

	protected $_table_name = 'engine_project_role';
	const MAIN_TABLE = 'engine_project_role';
	const ROLE_PERMISSIONS_TABLE = 'engine_role_permissions';

	protected $_has_many = array(
		'resource' => array('model' => 'resources', 'through' => 'engine_role_permissions', 'foreign_key' => 'role_id')
	);

	//List of roles in order of parent then child
	public $role_list = array();

	/*
	 * -----------------------
	 * Roles Table Functions -
	 * -----------------------
	 */

	// Select all user roles from the project_roles table
	public static function get_all_roles()
	{
		$user_counts = DB::select('role_id', DB::expr("count(*) as user_count"))
				->from(array(Model_Users::MAIN_TABLE, 'users'))
				->where('deleted', '=', 0)
				->group_by('role_id');
		$permission_counts = DB::select('role_id', DB::expr("count(*) as permission_count"))
				->from(array(Model_Roles::ROLE_PERMISSIONS_TABLE, 'rp'))
				->group_by('role_id');
		return DB::select('role.id', 'role.role','role.master_group', 'role.description', array("user_counts.user_count", "users"), "permission_counts.permission_count", 'role.publish', 'role.deleted')
			->from(array('engine_project_role','role'))
			->join(array($user_counts, 'user_counts'),'left')->on('user_counts.role_id', '=', 'role.id')
			->join(array($permission_counts, 'permission_counts'),'left')->on('permission_counts.role_id', '=', 'role.id')
			->group_by('role.id')
			->where('role.deleted', '=', 0)
			->execute()
			->as_array();
	}

    public function get_id_for_role($role)
    {
        $r = DB::select('id')
                 ->from('engine_project_role')
                 ->where('deleted', '=', 0)
                 ->where('role'   , '=', $role)
                 ->execute()
                 ->as_array();

        return count($r) > 0 ? $r[0]['id'] : NULL;
    }

	// Get the logged in users role.
	public static function get_user_roles() {

		//Get the users current users ID
		$var = Auth::instance()->get_user();
		$currentUser = $var['id'];

		//Get the current users role id and assign it to the $result_array[]
		$roles = DB::select('role_id')
				->from('engine_users')
				->where('id', '=', $currentUser)
				->execute()
				->as_array();

		//Return the users role
		return $roles[0]['role_id'];
	}

	// Get role data.
	public function get_role_data($id) {

		//Get the current users role id and assign it to the $result_array[ ]
		$roles = DB::select()
				->from('engine_project_role')
				->where((is_numeric($id) ? 'id' : 'role'), '=', $id)
				->execute()
				->as_array();

		$to_controller = $roles[0];

		//Return the users role
		return $to_controller;
	}

	// Update the role data
	public function update_role_data($id, $roleData) {

		//Send data to database
		$query = DB::update('engine_project_role')
				->set($roleData)
				->where('id', '=', $id)
				->execute();
	}

	// Add a new role to the system
	public function add_role($RoleData) {

		//Add the necessary values to the $RoleData array for insert
		$RoleData['publish'] = 1;
		$RoleData['deleted'] = 0;

		//Add User to database
		$query = DB::insert('engine_project_role', array('role', 'access_type', 'description', 'publish', 'deleted'))
				->values($RoleData)
				->execute();

		return $query[0];
	}

	// delete a new role to the system
	public function delete_role($id) {

		// Update for delete
		$update = array('deleted' => 1);

		// Make sure the role is not in use.
		$select = DB::select()
				->from('engine_users')
				->where('role_id', '=', $id)
				->where('deleted', '=', '0')
				->where('role_id', '=', $id)
				->execute();

		if ($select->count() > 0)
		{
			$return = 'This role is currently in use by ' . $select->count() . ' users and can not be deleted';
		}
		else
		{
			//Send data to database
			$query = DB::update('engine_project_role')
					->set($update)
					->where('id', '=', $id)
					->execute();

			$return = 'This role has been deleted';
		}

		return $return;
	}

	/*
	 * -----------------------------
	 * Permissions Table Functions -
	 * -----------------------------
	 */

	//Get the permissions for a given role_id
	public function get_permissions($roleId) {

		$permissions = DB::select('permission_code')
				->from('engine_permissions')
				->where('role_id', '=', $roleId);

		$result = $permissions->execute()->as_array();
		return $result;
	}

    public static function get_name($id)
    {
        $q = DB::select('role')->from('engine_project_role')->where('id','=',$id)->execute()->as_array();
        return count($q) > 0 ? $q[0]['role'] : '';
    }

	// Add role permissions to database
	public function process_permissions($permission_data) {

		// Save the role id into a new var and remove it from the post data array
		$role_id = $permission_data['role_id'];
		unset($permission_data['role_id']);

		foreach ($permission_data as $key => $value)
		{
			// Check to see if the permission is already in the database
			$is_set_on_db = $this->check_database($role_id, $key);

			if ($value == 'on')
			{ // Is this permission already set on the database?
				if ($is_set_on_db == 'FALSE')
				{
					$this->insert_permission($role_id, $key);
					$result = 'SUCCESS';
				}
			}
			// Delete opperation
			elseif ($value == 'off')
			{
				$this->delete_permission($role_id, $key);
				$result = 'SUCCESS';
			}
			// Something odd was posted to the controller - was not 'on / off'
			else
			{
				$result = 'FALE';
			}
		}
		// Send the result to the controller
		return $result;
	}

	// Is this permission already on the database?
	public function check_database($role_id, $key) {

		$permissions = DB::select()
				->from('engine_permissions')
				->where('role_id', '=', $role_id)
				->and_where('permission_code', '=', $key);

		$test_condition = $permissions->execute()->as_array();

		if ((isset($test_condition)) AND (count($test_condition) > 0))
		{
			$result = 'TRUE';
		}
		else
		{
			$result = 'FALSE';
		}

		return $result;
	}

	// Insert the given permission into the database
	public function insert_permission($role_id, $key) {

		$insert = array('role_id' => $role_id, 'permission_code' => $key);

		$query = DB::insert('engine_permissions', array('role_id', 'permission_code'))
				->values($insert)
				->execute();
	}

	// Delete the given permission from the database
	public function delete_permission($role_id, $key) {

		if (is_array($key))
		{
			$query = DB::delete('engine_permissions')
					->where('role_id', '=', $role_id)
					->and_where('permission_code', 'IN', $key)
					->execute();
		}
		else
		{
			$query = DB::delete('engine_permissions')
					->where('role_id', '=', $role_id)
					->and_where('permission_code', '=', $key)
					->execute();
		}

	}

	/*
				 * --------------------------------------------
				 * Backup of all hierarchical roles function  -
				 * --------------------------------------------
				 */

	//		//Generates a listing of the roles that the current logged in user can access
	//	public function build_roles($current = NULL) {
	//
	//		// Fetch a copy of the roles table
	//		$roles_db = DB::select()
	//				->from('roles')
	//				->execute();
	//
	//		$refs = array();
	//
	//		//List of all roles
	//		$list = array();
	//
	//		while ($data = $roles_db->current())
	//		{
	//			$thisref = &$refs[$data['id']];
	//
	//			$thisref['parent_id'] = $data['parent_id'];
	//			$thisref['name'] = $data['name'];
	//			if ($data['parent_id'] == 0)
	//			{
	//				$list[$data['id']] = &$thisref;
	//			}
	//			else
	//			{
	//				$refs[$data['parent_id']]['children'][$data['id']] = &$thisref;
	//			}
	//			$roles_db->next();
	//		}
	//
	//		//Add the current logged in users children roles
	//		$users_children_array = $this->find_user_children($list, $this->get_user_roles());
	//
	//		//Simplify the multidemontional array
	//		$simple_roles_array = $this->simplify_roles($users_children_array);
	//
	//		return $simple_roles_array;
	//	}

	//
	//	// This function flattens the multidemontional roles array
	//	function find_user_children(&$complex_array, $role_id) {
	//
	//		foreach ($complex_array as $id => $array)
	//		{
	//			if ($id == $role_id)
	//			{
	//				return array($id => $array);
	//			}
	//
	//			if ((isset($array['children'])) && (is_array($array['children'])))
	//			{
	//				return $this->find_user_children($array['children'], $role_id);
	//			}
	//		}
	//	}
	//
	//	//This function flattens the multidemontional roles array
	//	function simplify_roles(&$complex_array) {
	//
	//		foreach ($complex_array as $id => $array)
	//		{
	//			$this->role_list[$id] = $array['name'];
	//
	//			if ((isset($array['children'])) && (is_array($array['children'])))
	//			{
	//				$this->simplify_roles($array['children']);
	//			}
	//		}
	//
	//		return $this->role_list;
	//	}
	//

	public static function has_permission($role_id, $resource_id)
	{
		return DB::select('*')
			->from('engine_role_permissions')
			->where('role_id', '=', $role_id)
			->and_where('resource_id', '=', $resource_id)
			->execute()
			->current();
	}

    public static function save_post($post)
    {
        try {
            Database::instance()->begin();
            $id = $post['id'];
			$exists = DB::select('*')
				->from(Model_Roles::MAIN_TABLE)
				->where('role', '=', $post['role'])
				->execute()
				->current();
			if ($exists && !is_numeric($id)) {
				$id = $exists['id'];
			}
            if (is_numeric($id)) {
                DB::update(Model_Roles::MAIN_TABLE)
                    ->set(
                        array('role' => $post['role'], 'description' => $post['description'], 'allow_frontend_register' => @$post['allow_frontend_register'] ?: 0, 'allow_api_register' => @$post['allow_api_register'] ?: 0, 'allow_api_login' => @$post['allow_api_login'] ?: 0, 'allow_frontend_login' => @$post['allow_frontend_login'] ?: 0, 'deleted' => 0, 'default_dashboard_id' => @$post['default_dashboard_id'])
                    )
                    ->where('id', '=', $id)
                    ->execute();
            } else {
                $id = DB::insert(Model_Roles::MAIN_TABLE)
                    ->values(
                        array('role' => $post['role'], 'description' => $post['description'], 'allow_frontend_register' => @$post['allow_frontend_register'] ?: 0, 'allow_api_register' => @$post['allow_api_register'] ?: 0, 'allow_api_login' => @$post['allow_api_login'] ?: 0, 'allow_frontend_login' => @$post['allow_frontend_login'] ?: 0, 'default_dashboard_id' => @$post['default_dashboard_id'])
                    )
                    ->execute();
                $id = $id[0];
            }

            DB::delete('engine_role_permissions')
                ->where('role_id', '=', $id)
                ->execute();

            if (isset($post['resource'])) {
                foreach ($post['resource'] as $resource_id => $checked) {
                    if ($checked) {
                        DB::insert('engine_role_permissions')
                            ->values(array('role_id' => $id, 'resource_id' => $resource_id))
                            ->execute();
                    }
                }
            }

			$activity = new Model_Activity();
			$activity->set_item_id($id);
			$activity->set_item_type('permissions');
			$activity->set_action('update');
			$activity->save();
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
        return $id;
    }

    public static function get_settings_options($selected = null)
    {
        $options = '<option value="">-- Please select --</option>';

        $roles = DB::select('*')
            ->from(self::MAIN_TABLE)
            ->where('deleted', '=', 0)
            ->order_by('role')
            ->execute()
            ->as_array();

        foreach ($roles as $role) {
            $options .= '<option value="' . html::chars($role['role']) . '"' . (($selected == $role['role']) ? ' selected="selected"' : '') . '>' . $role['role'] . '</option>';
        }

        return $options;
    }

	public static function get_all()
	{
		$roles = DB::select('*')
				->from(self::MAIN_TABLE)
				->where('deleted', '=', 0)
				->order_by('id', 'asc')
				->execute()
				->as_array();
		return $roles;
	}

	public static function permissions($role_id)
	{
		$role_id = (int)$role_id;

		$permissions = DB::query(Database::SELECT, "select o.alias as permission, if(x.resource_id, 1, 0) enabled
	from engine_resources o left join
	(select rp.resource_id, r.role from engine_project_role r inner join engine_role_permissions rp on r.id = rp.role_id where r.id = $role_id) x on o.id = x.resource_id
order by permission
")->execute()->as_array();
		$result = array();
		foreach ($permissions as $i => $permission) {
			$result[$permission['permission']] = $permission['enabled'] == 1;
		}
		return $result;
	}
}
