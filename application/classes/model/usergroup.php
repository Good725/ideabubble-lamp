<?php defined('SYSPATH') or die('No direct script access.');

class Model_Usergroup extends Model {

	public function get_usergroups($id = NULL) {

		if (is_null($id))
		{
			$users = DB::select()
					->from('engine_user_group')
					->where('deleted', '!=', '1')
					->and_where('publish', '=', '1')
					->execute()
					->as_array();
		}
		else
		{
			$users = DB::select()
					->from('engine_user_group')
					->where('deleted', '!=', '1')
					->and_where('publish', '=', '1')
					->and_where('id', '=', $id)
					->execute()
					->as_array();
			$users = $users[0];
		}
		return $users;
	}

	public static function get_as_options($selected_id=null)
	{
		$groups = DB::select()
				->from('engine_user_group')
				->execute()
				->as_array();

		$options = '';
        foreach ($groups as $group)
		{
			$options .= '<option value="' . $group['id'] . '"' . ($group['id'] == $selected_id
					? 'selected="selected"' : '') . '>' . $group['user_group'] . '</option>';
		}

		return $options;
	}


	public function add_usergroup($Data) {

		//Add the necessary values to the $Data array for insert
		$Data['publish'] = 1;
		$Data['deleted'] = 0;

		//Add Assay to database
		$assay_query = DB::insert('engine_user_group', array('user_group', 'description', 'publish', 'deleted'))
				->values($Data)
				->execute();

		return $assay_query;
	}

	public function update_usergroup($id, $Data) {

		//Send data to database
		$query = DB::update('engine_user_group')
				->set($Data)
				->where('id', '=', $id)
				->execute();
	}

	public function delete_usergroup($id) {

		$userData = array('deleted' => 1);;
		//Send data to database
		$query = DB::update('engine_user_group')
				->set($userData)
				->where('id', '=', $id)
				->execute();
	}
}