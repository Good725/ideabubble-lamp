<?php defined('SYSPATH') or die('No direct script access.');

class Model_Levels extends Model
{
    const LEVEL_TABLE = 'plugin_courses_levels';
    public static function count_levels($search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (level like '%".$search."%' OR summary like '%".$search."%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as `count` FROM `plugin_courses_levels` WHERE `delete` = 0".$_search.";")
            ->execute()
            ->as_array();
        return $query['0']['count'];
    }


    public static function get_all_levels()
    {
        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_levels` WHERE `delete` = 0 AND `publish`=1 ORDER By `level`;")
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_levels($limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (`level` like '%".$search."%' OR summary like '%".$search."%')";
        }
        $_limit = ($limit > -1) ? ' LIMIT '.$offset.','.$limit : '';
        $query = DB::query(Database::SELECT,
        "SELECT `id`, `level`, `summary`, `publish` FROM `plugin_courses_levels` WHERE `delete` = 0 ".$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
            ->execute()
            ->as_array();
        $return = array();
        if (count($query) > 0)
        {
            $i=0;
            foreach ($query as $elem => $sub)
            {
                $return[$i]['level'] = '<a href="/admin/courses/edit_level/?id='.$sub['id'].'">'.$sub['level'].'</a>';
                $return[$i]['summary'] = '<a href="/admin/courses/edit_level/?id='.$sub['id'].'">'.$sub['summary'].'</a>';
                $return[$i]['edit'] = '<a href="/admin/courses/edit_level/?id='.$sub['id'].'">Edit</a>';
                if ($sub['publish'] == '1')
                {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="'.$sub['id'].'"><i class="icon-ok"></i></a>';
                }
                else
                {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="'.$sub['id'].'"><i class="icon-ban-circle"></i></a>';
                }
                $return[$i]['remove'] = '<a href="#" class="delete" data-id="'.$sub['id'].'">Delete</a>';
                $i++;
            }
        }

        return $return;
    }

    public static function get_level($id)
    {
        $data = DB::select()
            ->from('plugin_courses_levels')
            ->where('id', '=', $id)
            ->and_where('delete', '=', 0)
            ->execute()
            ->as_array();

        return $data[0];
    }

    public static function set_publish_level($id, $state)
    {
        if ($state == '1')
        {
            $published = 0;
        }
        else
        {
            $published = 1;
        }
        $logged_in_user = Auth::instance()->get_user();
        $query = DB::update("plugin_courses_levels")
            ->set(array(
                'publish' => $published,
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s')
            ))
            ->where('id', '=', $id)
            ->execute();
        $response = array();
        if ($query > 0)
        {
            $response['message'] = 'success';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }
        return $response;
    }

    public static function remove_level($id)
    {
        $logged_in_user = Auth::instance()->get_user();

        DB::update('plugin_courses_courses')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'level_id' => NULL
            ))
            ->where('level_id', '=', $id)
            ->execute();

        $ret = DB::update('plugin_courses_levels')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'delete' => 1
            ))
            ->where('id', '=', $id)
            ->execute();
        if ($ret > 0)
        {
            $response['message'] = 'success';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }
        return $response;
    }


    public static function save_level($data)
    {
		// add / update
		$save_action = 'add';
		$item_id = 0;
        unset($data['redirect']);
        //Add the necessary values to the $data array for update
        $logged_in_user = Auth::instance()->get_user();
        if ((int)$data['id'] > 0)
        {
            $id = (int)$data['id'];
            unset($data['id']);
            $data['modified_by'] = $logged_in_user['id'];
            $data['date_modified'] = date('Y-m-d H:i:s');
            $query = DB::update('plugin_courses_levels')
                ->set($data)
                ->where('id', '=', $id)
                ->execute();

			$save_action = 'update';
			$item_id = $id;
        }
        else
        {
            $data['created_by'] = $logged_in_user['id'];
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['delete'] = 0;
            $query = DB::insert('plugin_courses_levels', array_keys($data))
                ->values($data)
                ->execute();

			$save_action = 'add';
			$item_id = (isset($query[0]) AND $query[0] > 0)? $query[0] : 0;
        }

		// Set Successful / Not Successful Insert / Update Message
		if(
			($save_action == 'add' AND $query[0] > 0) OR
			($save_action == 'update' AND $query == 1)
		)
		{
			IbHelpers::set_message (
				'Level ID #'.$item_id.':  "'.$data['level'].'" has been '.(($save_action == 'add')? 'CREATED' : 'UPDATED' ).'.',
				'success popup_box'
			);
		}
		else
		{
			IbHelpers::set_message (
				'Sorry! There was a problem with '.(($save_action == 'add')? 'CREATION' : 'UPDATE' )
				.' of '.( ($item_id > 0)? 'Level ID #'.$item_id : 'Level' ).': "'.$data['level'].'".<br />'
				.'Please make sure, that form is filled properly and Try Again!',
				'error popup_box'
			);
		}

        return $item_id;
    }

    public static function validate_level($data)
    {
        //create empty errors array
        $errors = array();
        //check name must be min 3 chars
        if (@strlen($data['level']) < 3)
        {
            $errors[] = "Level name must contains min 3 characters";
        }
        return $errors;

    }

    public static function get_all_levels_html($get = false)
    {
        $query = DB::query(Database::SELECT, "SELECT
        `id`,
        `level` as `name`
        FROM
        `plugin_courses_levels`
        WHERE
        `delete` = 0
        AND
        `publish` = 1
        ORDER BY
        `name`")
            ->execute()
            ->as_array();
        $view =  View::factory(
            'front_end/dropdown_list',
            array(
                'items' => $query,
                'selected' => @$get['level']
            )
        );
        return $view;
    }

}
