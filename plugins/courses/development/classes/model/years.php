<?php defined('SYSPATH') or die('No direct script access.');

class Model_Years extends Model
{

    const YEARS_TABLE = 'plugin_courses_years';

    public static function count_years($search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (year like '%".$search."%' OR summary like '%".$search."%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as `count` FROM `plugin_courses_years` WHERE `delete` = 0".$_search.";")
            ->execute()
            ->as_array();
        return $query['0']['count'];
    }


    public static function get_all_years()
    {
        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_years` WHERE `delete` = 0 AND `publish`=1 ORDER By `year` = 'All Levels' DESC , `year`;")
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_years($limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (`year` like '%".$search."%' OR summary like '%".$search."%')";
        }

		$_limit = ($limit != -1) ? ' LIMIT '.$offset.','.$limit : '';

        $query = DB::query(Database::SELECT,
            "SELECT `id`, `year`, `summary`, `publish` FROM `plugin_courses_years` WHERE `delete` = 0 ".$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
            ->execute()
            ->as_array();
        $return = array();
        if (count($query) > 0)
        {
            $i=0;
            foreach ($query as $elem => $sub)
            {
                $return[$i]['year'] = '<a href="/admin/courses/edit_year/?id='.$sub['id'].'">'.$sub['year'].'</a>';
                $return[$i]['summary'] = '<a href="/admin/courses/edit_year/?id='.$sub['id'].'">'.$sub['summary'].'</a>';
                $return[$i]['edit'] = '<a href="/admin/courses/edit_year/?id='.$sub['id'].'">Edit</a>';
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

    public static function get_year($id)
    {
        $data = DB::select()
            ->from('plugin_courses_years')
            ->where('id', '=', $id)
            ->execute()
            ->current();

        return $data;
    }

    public static function get_years_where($where_clauses)
    {
        $query = DB::select()->from('plugin_courses_years')->where('delete', '=', 0);

        foreach ($where_clauses as $clause)
        {
            $query = $query->where($clause[0], $clause[1], $clause[2]);
        }

        return $query->execute()->as_array();
    }

    public static function set_publish_year($id, $state)
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
        $query = DB::update("plugin_courses_years")
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

    public static function remove_year($id)
    {
        $logged_in_user = Auth::instance()->get_user();

        DB::update('plugin_courses_courses')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'year_id' => NULL
            ))
            ->where('year_id', '=', $id)
            ->execute();

        $ret = DB::update('plugin_courses_years')
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


    public static function save_year($data)
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
            $query = DB::update('plugin_courses_years')
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
            $query = DB::insert('plugin_courses_years', array_keys($data))
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
				'Year ID #'.$item_id.':  "'.$data['year'].'" has been '.(($save_action == 'add')? 'CREATED' : 'UPDATED' ).'.',
				'success popup_box'
			);
		}
		else
		{
			IbHelpers::set_message (
				'Sorry! There was a problem with '.(($save_action == 'add')? 'CREATION' : 'UPDATE' )
				.' of '.( ($item_id > 0)? 'Year ID #'.$item_id : 'Year' ).': "'.$data['year'].'".<br />'
				.'Please make sure, that form is filled properly and Try Again!',
				'error popup_box'
			);
		}

        return $item_id;
    }

    public static function validate_year($data)
    {
        //create empty errors array
        $errors = array();
        //check name must be min 3 chars
        if (@strlen($data['year']) < 3)
        {
            $errors[] = "Year name must contains min 3 characters";
        }
        return $errors;

    }

}
