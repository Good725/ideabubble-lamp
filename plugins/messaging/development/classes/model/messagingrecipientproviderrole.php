<?php defined('SYSPATH') or die('No direct script access.');

class Model_MessagingRecipientProviderRole implements Model_MessagingRecipientProvider
{
	public function pid()
	{
		return "CMS_ROLE";
	}

	public function supports($driver)
	{
		return in_array($driver, array('sms', 'email', 'dashboard'));
	}

	public function get_by_id($id)
	{
		$data = DB::select('id', DB::expr('role as label'))
					->from('engine_project_role')
					->where('id', '=', $id)
					->execute()
					->as_array();
		if($data){
			return $data[0];
		} else {
			return null;
		}
	}
	
	public function get_by_label($label)
	{
		$data = DB::select('id', DB::expr('role as label'))
					->from('engine_project_role')
					->where('role', '=', $label)
					->execute()
					->as_array();
		if($data){
			return $data[0];
		} else {
			return null;
		}
	}
	
	public function search($term)
	{
		return DB::select('id', DB::expr('role as label'))
					->from('engine_project_role')
					->where('engine_project_role.deleted', '=', 0)
					->and_where('role', 'like', '%' . $term . '%')
					->execute()
					->as_array();
	}
	
	public function to_autocomplete($term, &$data)
	{
		foreach(DB::select('id', DB::expr('role as label'))
					->from('engine_project_role')
					->where('engine_project_role.deleted', '=', 0)
					->and_where('role', 'like', '%' . $term . '%')
					->order_by('role', 'asc')
					->limit(5)
					->execute()
					->as_array() as $role){
			$data[] = array('value' => $role['id'],
								'label' => $role['label'],
								'category' => $this->pid());
		}
	}

	protected function search_users($role)
	{
		return DB::select('engine_users.*')
					->from('engine_users')
					->join('engine_project_role', 'inner')->on('engine_users.role_id', '=', 'engine_project_role.id')
					->where('engine_project_role.role', '=', $role)
					->and_where('engine_users.deleted', '=', 0)
					->execute()
					->as_array();
	}
	
	protected function search_users_by_id($role_id)
	{
		return DB::select('engine_users.*')
					->from('engine_users')
					->join('engine_project_role', 'inner')->on('engine_users.role_id', '=', 'engine_project_role.id')
					->where('engine_project_role.id', '=', $role_id)
					->and_where('engine_users.deleted', '=', 0)
					->execute()
					->as_array();
	}

    public function resolve_final_targets($target, &$target_list, &$warnings)
    {
        $driver    = isset($target['driver']) ? $target['driver'] : '';
        $target_id = isset($target['target']) ? $target['target'] : '';

        if ($driver == 'dashboard') {
            $users = $this->search_users_by_id($target_id);
            foreach ($users as $user) {
                $target['target_type'] = 'CMS_USER';
                $target['target']      = (int)$user['id'];
                $target_list[]         = $target;
            }
        } else if ($driver == 'sms') {
            $users = $this->search_users_by_id($target_id);
            foreach($users as $user) {
                if ($user['mobile'] != '') {
                    $target['target_type'] = 'PHONE';
                    $target['target']      = $user['mobile'];
                    $target_list[]         = $target;
                } else {
                    $warnings[] = 'user:' . $user['id'] . ' does not have mobile set for ' . $driver . ' messaging';
                }
            }
        } else if ($driver == 'email') {
            $users = $this->search_users_by_id($target_id);
            foreach ($users as $user) {
                if ($user['email'] != '') {
                    $target['target_type'] = 'EMAIL';
                    $target['target']      = $user['email'];
                    $target_list[]         = $target;
                } else {
                    $warnings[] = 'user:' . $user['id'] . ' does not have email set for ' . $driver . ' messaging';
                }
            }
        } else {
            $warnings[] = $driver . ' messaging is not supported';
        }
    }

	public function message_details_column()
	{
		return "r.role";
	}

	public function message_details_join($query)
	{
		$query->join(array('engine_project_role', 'r'), 'left')->on('t.target', '=', 'r.id')->on('t.target_type', '=', DB::expr("'CMS_ROLE'"));
	}
}