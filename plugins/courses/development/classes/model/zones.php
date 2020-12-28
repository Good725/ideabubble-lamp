<?php defined('SYSPATH') or die('No direct script access.');

class Model_Zones extends Model
{
    private static $model_zones_table = 'plugin_courses_zones';

    public static function get_all_zones()
    {
        return  DB::select()
            ->from(self::$model_zones_table)
            ->where('deleted', '=', 0)
            ->order_by('name', 'ASC')
            ->execute()
            ->as_array();
    }

    public static function count_zones($search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (name like '%".$search."%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as `count` FROM `plugin_courses_zones` WHERE `deleted` = 0".$_search.";")
            ->execute()
            ->as_array();
        return $query['0']['count'];
    }

    public static function get_zones($limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (`name` like '%".$search."%')";
        }

        $_limit = ($limit != -1) ? ' LIMIT '.$offset.','.$limit : '';

        $query = DB::query(Database::SELECT,
            "SELECT `id`, `name`, `price` FROM `plugin_courses_zones` WHERE `deleted` = 0 ".$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
            ->execute()
            ->as_array();
        $return = array();
        if (count($query) > 0)
        {
            $i=0;
            foreach ($query as $elem => $sub)
            {
                $return[$i]['name'] = $sub['name'];

                $return[$i]['edit'] = '<a class="action_edit_zone" data-toggle="modal" data-id="'.$sub['id'] .'"  href="#">Edit</a>';
                $return[$i]['remove'] = '<a href="#" class="delete" data-id="'.$sub['id'].'">Delete</a>';
                $i++;
            }
        }

        return $return;
    }

    public static function save_zone($data)
    {
        // add / update
        $save_action = 'add';
        $item_id = 0;
        unset($data['redirect']);
        //Add the necessary values to the $data array for update
//        $logged_in_user = Auth::instance()->get_user();

//            $data['created_by'] = $logged_in_user['id'];
//            $data['date_created'] = date('Y-m-d H:i:s');
            $data['deleted'] = 0;
            $query = DB::insert(self::$model_zones_table, array_keys($data))
                ->values($data)
                ->execute();

            $save_action = 'add';
            $item_id = (isset($query[0]) AND $query[0] > 0)? $query[0] : 0;


        // Set Successful / Not Successful Insert / Update Message
        if(
            ($save_action == 'add' AND $query[0] > 0)
        )
        {
            IbHelpers::set_message (
                'Zone ID #'.$item_id.':  "'.$data['name'].'" has been CREATED.',
                'success popup_box'
            );
        }
        else
        {
            IbHelpers::set_message (
                'Sorry! There was a problem with '.(($save_action == 'add')? 'CREATION' : 'UPDATE' )
                .' of '.( ($item_id > 0)? 'Zone ID #'.$item_id : 'Zone' ).': "'.$data['name'].'".<br />'
                .'Please make sure, that form is filled properly and Try Again!',
                'error popup_box'
            );
        }

        return $item_id;
    }

    public static function update_zone($id,$name)
    {
//        $logged_in_user = Auth::instance()->get_user();

        $ret = DB::update(self::$model_zones_table)
            ->set(array(
                'name' => ':name'
            ))
            ->where('id', '=',':id')
            ->parameters(array(
                ':id' => $id,
                ':name' => $name
            ))
            ->execute();

        if ($ret > 0)
        {
            $response['message'] = 'success';
            $response['redirect'] = '/admin/courses/zones';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }

        return $response;
    }

    public static function get_zone_by_id($id = NULL)
    {
        $query =  DB::select()
            ->from(self::$model_zones_table)
            ->where('id', '=', $id)
            ->execute()
            ->as_array();
        return $query[0];
    }

    public static function remove_zone($id)
    {
//        $logged_in_user = Auth::instance()->get_user();

        DB::delete('plugin_courses_schedules_have_zones')
            ->where('plugin_courses_schedules_have_zones.zone_id', '=', $id)
            ->execute();

        $ret = DB::delete(self::$model_zones_table)
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

}
