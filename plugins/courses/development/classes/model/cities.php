<?php defined('SYSPATH') or die('No direct script access.');

class Model_Cities extends Model
{

    const TABLE_COUNTIES  = 'engine_counties';
    const TABLE_COURSE_COUNTIES = 'plugin_courses_counties';
    const TABLE_COURSE_CITIES =  'plugin_courses_cities';

    public static function get_cities($county, $search = false)
    {
        $_search = ' AND `county_id` = '.$county;
        if ($search)
        {
            $_search = " AND `name` like '%".$search."%'";
        }
        $query = DB::query(Database::SELECT,
        "SELECT `id`, `name` FROM `plugin_courses_cities` WHERE `delete` = 0 ".$_search)
            ->execute()
            ->as_array();

        return $query;
    }

    public static function get_counties($id = null, $field = 'id', $table = 'plugin_courses_counties')
    {
        if ($table == 'plugin_courses_counties') {
            $sql = 'SELECT `id`, `name` FROM `plugin_courses_counties` WHERE `delete` = 0';
        } else {
            $sql = 'SELECT `id`, `name` FROM `engine_counties` WHERE `deleted` = 0';
        }

        if (!is_null($id) && $field == 'id')
        {
            $sql .= ' AND `id` = '.$id;
            $query = DB::query(Database::SELECT, $sql)
                ->execute()
                ->get('name', 0);
        } elseif(!is_null($id) && $field == 'name') {
            $sql .= ' AND `name` LIKE "%'.$id . '%"';
            $query = DB::query(Database::SELECT, $sql)
                ->execute()
                ->as_array();
        } elseif(!is_null($id) && $field == 'code') {
            $sql .= ' AND `code` = "' .$id . '"';
            $query = DB::query(Database::SELECT, $sql)
                ->execute()
                ->as_array();
        } elseif(!is_null($id) && $field == 'country_code') {
            $sql .= ' AND `country_code` = "'. $id . '"';
            $query = DB::query(Database::SELECT, $sql)
                ->execute()
                ->as_array();
        } else {
            $query = DB::query(Database::SELECT, $sql)
                ->execute()
                ->as_array();
        }
        return $query;
    }


    public static function get_cities_for_county_html($county)
    {
        $query = DB::query(Database::SELECT,
            "SELECT `id`, `name` FROM `plugin_courses_cities` WHERE `delete` = 0 AND `county_id` = ".$county." ORDER BY name")
            ->execute()
            ->as_array();
        $return = "";
        if (is_array($query) && count($query) > 0)
        {
            $return = "";
            foreach ($query as $elem => $val)
            {
                $return .="<option value='".$val['id']."'>".$val['name']."</option>".PHP_EOL;
            }
        }
        else
        {
            $return .="<option value=''>No entries for specified county</option>";
        }
        return $return;
    }

    public static function ajax_save_city($data)
    {
        $logged_in_user = Auth::instance()->get_user();
            $data['created_by'] = $logged_in_user['id'];
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['delete'] = 0;
            $query = DB::insert('plugin_courses_cities', array_keys($data))
                ->values($data)
                ->execute();
        return json_encode($query);
    }

    public static function get_all_counties_html_options()
    {
        $ret = '';
        $query = DB::query(Database::SELECT, "SELECT
        `id`,
        `name`
        FROM
        `plugin_courses_counties`
        WHERE
        `publish` = 1
        AND
        `delete` = 0
        ORDER BY `name`
        ")
            ->execute()
            ->as_array();
        foreach ($query as $elem => $val)
        {
            $ret .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
        }
        return $ret;
    }
}