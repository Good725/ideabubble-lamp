<?php
defined('SYSPATH')or die('No direct script access.');

class Model_Menus extends Model_Database
{

	/*               *
	 * Backend Model *
	 *               */

//Variables for save the menu order and nested
	private $query_sort = array();
	private $level = 0;

	/**
	 *
	 * Get all the menus
	 *
	 * @return Array
	 */
	public function get_all_menus()
	{

		$query = DB::query(Database::SELECT, 'SELECT * FROM `plugin_menus` WHERE `deleted` = 0 AND `parent_id` = 0
ORDER BY
   CASE
     WHEN `category`=\'Main\' THEN 0
     ELSE 1
   END,
   `category` ASC, `menu_order`')->execute()->as_array();


		foreach ($query as $res)
		{
			//Add the submenu dropdown html code to each row
			$res['submenu'] = $this->get_submenu_dropdown($res);

			//If has submenu call similar function recursively
			if ($res['has_sub'] === "1")
			{
				$res['level'] = $this->level;
				$this->query_sort[] = $res;
				$this->get_submenu($res);
			}
			else
			{
				//Else add to the Array
				$res['level'] = $this->level;
				$this->query_sort[] = $res;
			}

		}
		;

		return $this->query_sort;
	}


	/**
	 * Get the submenu of the current menu. recursive calls
	 *
	 * @param $current_row
	 */
	public function get_submenu($current_row)
	{
		$this->level++;
		$query = DB::query(Database::SELECT, 'SELECT * FROM plugin_menus WHERE deleted = 0 AND parent_id = :parent_id ORDER BY category, menu_order');

		$query->parameters(array(
			':parent_id' => $current_row['id']
		));

		$query = $query->execute()->as_array();
		foreach ($query as $res)
		{
			//Add the submenu dropdown html code to each row
			$res['submenu'] = $this->get_submenu_dropdown($res);

			//If has submenu call similar function recursively
			if ($res['has_sub'] === "1")
			{
				$res['level'] = $this->level;
				$this->query_sort[] = $res;
				$this->get_submenu($res);
			}
			else
			{
				//Else add to the Array
				$res['level'] = $this->level;
				$this->query_sort[] = $res;
			}
		}
		$this->level--;

	}

	/**
	 * Get the submenu "submenu of" dropdown.
	 *
	 *
	 * @param $row
	 * @return string
	 */
	private function get_submenu_dropdown($row)
	{
		$query = DB::query(Database::SELECT, 'SELECT * FROM plugin_menus WHERE category = :category AND id != :id AND deleted = 0')->parameters(array(':category' => $row['category'], ':id' => $row['id']))->execute()->as_array();

		$options = '
<select class="form-control" name="parent_id[]">
<option value="0" >--NONE--</option>';
		foreach ($query as $res)
		{
			if ($row['parent_id'] === $res['id'])
			{
				$options .= '<option selected="selected" value="'.$res['id'].'" >'.$res['title'].'</option>';
			}
			else
			{
				$options .= '<option value="'.$res['id'].'" >'.$res['title'].'</option>';
			}
		}
		return $options;

	}

	/**
	 * Get all pages. For display in the selector / menu link
	 *
	 * @return string
	 */
	public function get_pages_list()
	{
		$query = DB::query(Database::SELECT, 'SELECT name_tag,id FROM plugin_pages_pages WHERE deleted = 0 AND publish = 1 ORDER BY name_tag')->execute()->as_array();
		return $query;
	}

	public function get_pages_list_dropdown()
	{
		$pages = $this->get_pages_list();

		// new menu dropdown selector
		$options = '
<select name="link_tag" class="form-control">
<option value="0">URL</option>
<option value="separator" disabled="disabled">------------------</option>';


		//Make selector for pages link
		foreach ($pages as $page)
		{
			$options .= '<option value="'.$page['id'].'" >'.$page['name_tag'].'</option>';
		}
		$options .= '</select>';

		return $options;
	}


	/**
	 * @param $post_data
	 * @return bool
	 */
	public function set_new_menu_data($post_data)
	{
		if (empty($post_data))
			return FALSE;
		$query = DB::query(Database::INSERT, 'INSERT INTO plugin_menus (
            category,title,link_tag,link_url,has_sub,parent_id,menu_order,publish,deleted,date_modified,date_entered,created_by,modified_by,menus_target,image_id
        )
        VALUES(:category,:title,:link_tag,:link_url,:has_sub,:parent_id,:menu_order,:publish,:deleted,NOW(),NOW(),:created_by,:modified_by,:menus_target,:image_id

        )');

		// Set menu target
		if (isset($post_data['open_in_window']) && $post_data['open_in_window'] == '1')
		{
			$target = '_blank';
		}
		else
		{
			$target = '_self';
		}
		$url = $post_data['link_url'];

		// If it is the first menu record, can chose submenu, don't get the post data.
		if (!isset($post_data['parent_id']))
		{
			$post_data['parent_id'] = 0;
		}


		$count = $query->parameters(array(
			':category' => $post_data['category'],
			':title' => $post_data['title'],
			':link_tag' => $post_data['link_tag'],
			':link_url' => $url,
			':has_sub' => 0,
			':parent_id' => $post_data['parent_id'],
			':menu_order' => (int) $post_data['menu_order'],
			':publish' => (int) $post_data['publish'],
			':deleted' => 0,
			':created_by' => $_SESSION['admin_user']['id'],
			':modified_by' => $_SESSION['admin_user']['id'],
			':menus_target' => $target,
			':image_id' => $post_data['menu_image']
		))->execute();

		$this->update_submenu();
		return $count;
	}


	/**
	 * Save changes in the menu (pmenu table)
	 * @param $post_data
	 * @return int
	 */
	public function set_menu_data($post_data)
	{

		$db = Database::instance('default');

		// Begin transaction

		$db->begin();
		try
		{
			$i = 0;
			foreach ($post_data['title'] as $row)
			{
				$post_data['title'][$i];

				$query = DB::query(Database::UPDATE, '
                    UPDATE plugin_menus
                    SET title = :title,
                    title = :title,
                    link_url = :link_url,
                    html_attributes = :html_attributes,
                    menu_order = :menu_order,
                    parent_id = :parent_id,
                    category = :category,
                    link_tag = :link_tag,
                    modified_by = :modified_by,
                    date_modified = NOW(),
                    menus_target = :menus_target,
                    image_id = :menu_image
                    WHERE id = :id
                    LIMIT 1
                    ');

				//how to open the menu link
				if (@!empty($post_data['open_in_window']) && in_array($post_data['id'][$i], $post_data['open_in_window']))
				{
					$target = '_blank';
				}
				else
				{
					$target = '_self';
				}

				$url = $post_data['link_url'][$i];

				$query->parameters(array(
					':id' => $post_data['id'][$i],
					':title' => $post_data['title'][$i],
					':link_url' => $url,
					':link_tag' => $post_data['link_tag'][$i],
					':html_attributes' => $post_data['html_attributes'][$i],
					':menu_order' => (int) $post_data['menu_order'][$i],
					':parent_id' => (int) $post_data['parent_id'][$i],
					':category' => $post_data['category'][$i],
					':modified_by' => $_SESSION['admin_user']['id'],
					':menus_target' => $target,
					':menu_image' => $post_data['menu_image'][$i]
				));

				$query->execute();

				$i++;
			}


			// Insert successful commit the changes
			$db->commit();
			$success = TRUE;
			//Update parent row when it have a submenu
			$this->update_submenu();
		}
		catch (Database_Exception $e)
		{
			// Insert failed. Rolling back changes...
			$db->rollback();
			$success = FALSE;

            Log::instance()->add(Log::ERROR, 'Error saving menu: '.$e->getMessage()."\n".$e->getTraceAsString())->write();
		}
		return $success;
	}

	/**
	 * Update the menu levels, call from "set_menu_data()" and "set_new_menu_data()"
	 */
	private function update_submenu()
	{
		$query = DB::query(Database::SELECT, 'SELECT DISTINCT(parent_id) FROM plugin_menus WHERE parent_id > 0 AND deleted = 0')->execute()->as_array();
		foreach ($query as $row)
		{
			$query_update = DB::query(Database::UPDATE, 'UPDATE plugin_menus SET has_sub = 1 WHERE id = :id AND deleted = 0')->parameters(array(':id' => $row['parent_id']))->execute();
		}

		$query = DB::query(Database::SELECT, 'SELECT id FROM plugin_menus WHERE has_sub = 1 AND deleted = 0')->execute()->as_array();
		foreach ($query as $row)
		{

			$res_num = DB::query(Database::SELECT, 'SELECT COUNT(id) as num FROM plugin_menus WHERE parent_id = :id AND deleted = 0')->parameters(array(':id' => $row['id']))->execute()->as_array();

			if ($res_num[0]['num'] == 0)
			{
				$query_update = DB::query(Database::UPDATE, 'UPDATE plugin_menus SET has_sub = 0 WHERE id = :id LIMIT 1')->parameters(array(':id' => $row['id']))->execute();
			}
		}
	}

	/**
	 * Get the dropdown menu of the selected group
	 * @param $group
	 * @return string, HTML dropdown menu
	 */
	public function get_option_dropdown($group)
	{
		$select_menu = '<select class="form-control" name="parent_id"><option value="0">--NONE--</option>'.PHP_EOL;

		$query = DB::query(Database::SELECT, 'SELECT title,id FROM plugin_menus WHERE category = :group AND deleted = 0')->parameters(array(':group' => $group['category']))->execute()->as_array();
		foreach ($query as $row)
		{
			$select_menu .= '<option value="'.$row['id'].'">'.$row['title'].'</option>';
		}
		$select_menu .= '</select>';

		return $select_menu;
	}

	/**
	 * Change the published status of the selected page. AJAX
	 *
	 * @param $id
	 * @return string
	 */
	public function change_published_status($id)
	{
		$query = DB::query(Database::SELECT, 'SELECT publish FROM plugin_menus WHERE id = :id')->parameters(array(':id' => $id));
		$query_res = $query->execute()->as_array();
		if (empty($query_res))
		{
			$str_res = 'Error, the selected page doesn\'t exist';
		}
		else
		{
			$str_res = $query_res[0]['publish'];
			if ($str_res == 0)
			{
				$total_rows = DB::update('plugin_menus')->set(array('publish' => '1'))->where('id', '=', $id)->limit(1)->execute();
			}
			else
			{
				$total_rows = DB::update('plugin_menus')->set(array('publish' => '0'))->where('id', '=', $id)->limit(1)->execute();
			}
			if ($total_rows > 0)
			{
				$str_res = 'success';
			}
			else
			{
				$str_res = 'Error: Can\'t update the database';
			}

		}

		return $str_res;
	}

	/**
	 * Delete the menu with id = $id
	 * @param $id
	 * @return string (success|error)
	 */
	public function delete_menu($id)
	{
		//Remove the children recursively
		$query = DB::query(Database::SELECT, 'SELECT id FROM plugin_menus WHERE parent_id = :id')->parameters(array(':id' => $id))->execute();
		foreach ($query as $row)
		{
			$this->delete_menu($row['id']);
		}


		$query = DB::query(Database::UPDATE, 'UPDATE plugin_menus SET deleted = 1 WHERE id = :id LIMIT 1')->parameters(array(':id' => $id))->execute();
		if ($query > 0)
		{
			$res_status = 'success';
		}
		else
		{
			$res_status = 'error';
		}
		$this->update_submenu();
		return $res_status;
	}

	public static function getMenus()
	{
		$query = DB::query(Database::SELECT, "SELECT id,category FROM plugin_menus WHERE publish = '1' AND deleted = '0' GROUP BY category")->execute()->as_array();
		$query_size = count($query);
		$i = 0;
		$result = "";
		$result = '<option value="none">Select a menu</option>';
		while ($i < $query_size)
		{
			$result .= '<option value="'.$query[$i]['category'].'">'.$query[$i]['category'].'</option>';
			$i++;
		}
		return $result;
	}

	public static function getSubMenu($menu_category)
	{
		$query = DB::query(Database::SELECT, "SELECT id,title FROM plugin_menus WHERE parent_id = '0' AND category = '".$menu_category."'")->execute()->as_array();
		$query_size = count($query);
		$i = 0;
		$result = "";
		$result = '<option value="none">Select a submenu</option>';
		while ($i < $query_size)
		{
			$result .= '<option value="'.$query[$i]['id'].'">'.$query[$i]['title'].'</option>';
			$i++;
		}
		echo $result;
	}
	
	public static function get_localisation_messages()
	{
		$menu_titles = DB::select('title')->from('plugin_menus')->execute()->as_array();
		$messages = array();
		foreach($menu_titles as $menu_title){
			$messages[] = $menu_title['title'];
		}
		return $messages;
	}
}

?>