<?php defined('SYSPATH') or die('No direct script access.');

class Model_Locationtypes extends Model
{

    public static function count_location_types($search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (type like '%".$search."%' OR summary like '%".$search."%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as `count` FROM `plugin_courses_location_types` WHERE `delete` = 0".$_search.";")
            ->execute()
            ->as_array();
        return $query['0']['count'];
    }


    public static function get_location_types($limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (type like '%".$search."%' OR summary like '%".$search."%')";
        }
        $_limit = ($limit > -1) ? ' LIMIT '.$offset.','.$limit : '';
        $query = DB::query(Database::SELECT,
            "SELECT `id`, `type`, `summary`, `publish` as `pbl` FROM `plugin_courses_location_types` WHERE `delete` = 0 ".$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
            ->execute()
            ->as_array();
        $return = array();
        if (count($query) > 0)
        {
            foreach ($query as $elem => $sub)
            {
                $return[$elem] = array();
                foreach ($sub as $key => $val)
                {
                    $return[$elem][$key] = $val;
                }
                $return[$elem]['edit'] = '<a href="/admin/courses/edit_location_type/'.$return[$elem]['id'].'">Edit</a>';
                if ($return[$elem]['pbl'] == '1')
                {
                    $return[$elem]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="'.$return[$elem]['id'].'"><i class="icon-ok"></i></a>';
                }
                else
                {
                    $return[$elem]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="'.$return[$elem]['id'].'"><i class="icon-ban-circle"></i></a>';
                }
                $return[$elem]['remove'] = '<a href="#" class="delete" data-id="'.$return[$elem]['id'].'">Delete</a>';
            }
        }

        return $return;
    }

    public static function get_location_type($id)
    {
        $data = DB::select()
            ->from('plugin_courses_location_types')
            ->where('id', '=', $id)
            ->execute()
            ->as_array();

        return $data[0];
    }

    public static function set_publish_location_type($id, $state)
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
        $query = DB::update("plugin_courses_location_types")
            ->set(array(
                'published' => $published,
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s')
            ))
            ->where('id', '=', $id)
            ->execute();
        return $query;
    }

    public static function update_location_type($data, $id)
    {
        $logged_in_user = Auth::instance()->get_user();
        $data['modified_by'] = $logged_in_user['id'];
        $data['date_modified'] = date('Y-m-d H:i:s');
        return DB::update('plugin_courses_location_types')
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
    }

    public static function remove_location_type($id)
    {
        $logged_in_user = Auth::instance()->get_user();

        DB::update('plugin_courses_locations')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'location_type_id' => NULL
            ))
            ->where('location_type_id', '=', $id)
            ->execute();

        return DB::update('plugin_courses_location_types')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'delete' => 1
            ))
            ->where('id', '=', $id)
            ->execute();

    }


    public static function create_location_type($data)
    {
        //Add the necessary values to the $data array for update
        $logged_in_user = Auth::instance()->get_user();
        $data['created_by'] = $logged_in_user['id'];
        $data['date_created'] = date('Y-m-d H:i:s');
        $data['publish'] = 1;
        $data['deleted'] = 0;
        //Add Category to database
        $query = DB::insert('plugin_courses_location_types', array('type', 'summary', 'publish', 'deleted', 'created_by', 'date_created'))
            ->values($data)
            ->execute();

        return $query;
    }

    public static function validate_location_type($data)
    {
        //create empty errors array
        $errors = array();
        //check name must be min 3 chars
        if (@strlen($data['type']) < 3)
        {
            $errors[] = "Location type name must contains min 3 characters";
        }
        return $errors;

    }

    public static function ajax_save_type($data)
    {
        $logged_in_user = Auth::instance()->get_user();
        $data['created_by'] = $logged_in_user['id'];
        $data['date_created'] = date('Y-m-d H:i:s');
        $data['delete'] = 0;
        $query = DB::insert('plugin_courses_location_types', array_keys($data))
            ->values($data)
            ->execute();
        return json_encode($query);
    }

    public static function get_types_html()
    {
        $query = DB::query(Database::SELECT,
            "SELECT `id`, `type` FROM `plugin_courses_location_types` WHERE `delete` = 0 ORDER BY `type`")
            ->execute()
            ->as_array();
        $return = "";
        if (is_array($query) && count($query) > 0)
        {
            $return = "";
            foreach ($query as $elem => $val)
            {
                $return .="<option value='".$val['id']."'>".$val['type']."</option>".PHP_EOL;
            }
        }
        else
        {
            $return .="<option value=''>No entries</option>";
        }
        return $return;
    }

}