<?php defined('SYSPATH') or die('No direct script access.');

class Model_Locations extends Model
{
    const TABLE_LOCATIONS = 'plugin_courses_locations';

    public static function count_locations($search = false, $owned_by = null)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (`name` like '%".$search."%' OR `summary` like '%".$search."%' OR `address1` like '%".$search."%' OR `address2` like '%".$search."%'  OR `address3` like '%".$search."%')";
        }
        if (is_numeric($owned_by)) {
            $_search .= " AND plugin_courses_locations.owned_by=$owned_by";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as `count` FROM `plugin_courses_locations` WHERE `plugin_courses_locations`.`delete` = 0".$_search.";")
            ->execute()
            ->as_array();
        return $query['0']['count'];
    }


    public static function get_locations_without_parent($owned_by = null, $provider_ids = null)
    {
        $query = DB::query(
            Database::SELECT,
            "SELECT
                DISTINCT `plugin_courses_locations`.*, `plugin_courses_cities`.`name` as `city`
              FROM `plugin_courses_locations`
                LEFT JOIN plugin_courses_locations clocations on plugin_courses_locations.id = clocations.parent_id
                LEFT JOIN `plugin_courses_cities` ON `plugin_courses_locations`.`city_id` = `plugin_courses_cities`.`id`
                LEFT JOIN plugin_courses_schedules schedules ON (schedules.location_id = plugin_courses_locations.id or schedules.location_id = clocations.id)
                LEFT JOIN plugin_courses_courses courses on schedules.course_id = courses.id
                LEFT JOIN plugin_courses_courses_has_providers has_providers ON courses.id = has_providers.course_id
              WHERE `plugin_courses_locations`.`delete` = 0 AND `plugin_courses_locations`.`parent_id` is NULL" .
            (is_numeric($owned_by) ? " AND `plugin_courses_locations`.`owned_by`=$owned_by" : "") .
            (!empty($provider_ids) ? " AND `has_providers`.`provider_id` IN (".implode(',', $provider_ids).")" : "") .
            " ORDER BY plugin_courses_locations.name"
        )
            ->execute()
            ->as_array();
        return $query;
    }


    public static function get_locations_only()
    {
        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_locations` WHERE `delete` = 0 AND `publish`  != 0 ORDER BY `name`")
            ->execute()
            ->as_array();
        return $query;
    }

    /**
     * Get The Locations for classes where they are not School
     * @return mixed
     */
    public static function get_locations_with_parent()
    {
        $locations = DB::select('l.id','l.name', 'l.address1', 'l.address2', 'l.address3', 'l.county_id', 'l.city_id',
            'l.capacity', 'l.location_type_id', 'l.parent_id', 'l.summary', 'l.email', 'l.phone', 'l.description',
            'l.date_created', 'l.date_modified', 'l.created_by', 'l.modified_by', 'l.publish', 'l.delete', 't.type')
            ->from(array('plugin_courses_locations', 'l'))
            ->join(array('plugin_courses_location_types', 't'), 'left')
            ->on('l.location_type_id', '=', 't.id')
            ->where('t.id','<',3)
            ->where('l.delete', '=', 0)
            ->where('l.publish','=',1)
            ->execute()
            ->as_array();
        foreach ( $locations as $key=>$location)
        {
            if ( ! is_null($location['parent_id']) OR $location['parent_id'] != '' )
            {
                $parent = DB::select('name')
                    ->from(array('plugin_courses_locations', 'l'))
                    ->where('id','=',$location['parent_id'])
                    ->execute()
                    ->as_array();
                $locations[$key]['name'] = $parent[0]['name'].' - '.$location['name'];
            }
        }
        $sort = array();
        foreach($locations as $k=>$v)
        {
            $sort['name'][$k] = $v['name'];
        }
        array_multisort($sort['name'], SORT_ASC, $locations);
        return $locations;
    }

    public static function get_locations_where($where_clauses)
    {
        $q = DB::select('l.id', 'l.name', 'l.address1', 'l.address2', 'l.address3', 'l.county_id', 'l.city_id',
            'l.capacity', 'l.location_type_id', 'l.parent_id', 'l.summary', 'l.email', 'l.phone', 'l.description',
            'l.date_created', 'l.date_modified', 'l.created_by', 'l.modified_by', 'l.publish', 'l.delete', 't.type',
            'l.directions', 'l.lat', 'l.lng')
            ->from(array('plugin_courses_locations', 'l'))
            ->join(array('plugin_courses_location_types', 't'), 'left')->on('l.location_type_id', '=', 't.id')
            ->where('l.delete', '=', 0);

        foreach ($where_clauses as $clause)
        {
            $q = $q->where($clause[0], $clause[1], $clause[2]);
        }

        return $q->execute()->as_array();
    }

    public static function get_locations($limit, $offset, $sort, $dir, $search = false, $owned_by = null)
    {
        $_search = '';
        if ($search)
        {
            $search = Database::instance()->real_escape($search);
            $_search = " AND (
        `plugin_courses_locations`.`name` like '%".$search."%' OR
        `plugin_courses_locations`.`summary` like '%".$search."%' OR
        `plugin_courses_locations`.`address1` like '%".$search."%' OR
        `plugin_courses_locations`.`address2` like '%".$search."%'  OR
        `plugin_courses_locations`.`address3` like '%".$search."%')
        OR (
        `parentloc`.`name` like '%".$search."%' OR
        `parentloc`.`summary` like '%".$search."%' OR
        `parentloc`.`address1` like '%".$search."%' OR
        `parentloc`.`address2` like '%".$search."%'  OR
        `parentloc`.`address3` like '%".$search."%')";
        }
        if (is_numeric($owned_by)) {
            $_search .= " AND plugin_courses_locations.owned_by=$owned_by ";
        }
        $_limit = ($limit > -1) ? ' LIMIT '.$offset.','.$limit : '';
        $query = DB::query(Database::SELECT, "SELECT DISTINCT
            `parentloc`.`name` as `parent`,
            `plugin_courses_locations`.`id`,
            `plugin_courses_locations`.`name`,
            `plugin_courses_counties`.`name` as `county`,
            `plugin_courses_cities`.`name` as `city`,
            `plugin_courses_location_types`.`type`,
            CONCAT(`parentloc`.`name`,' ',`plugin_courses_locations`.`name`) as `full_name`,
            (SELECT count(*) from `plugin_courses_locations` `subs` WHERE `plugin_courses_locations`.`id` = `subs`.`parent_id`) as `subcount`
            FROM `plugin_courses_locations`
                LEFT JOIN `plugin_courses_locations` `parentloc` ON `plugin_courses_locations`.`parent_id` = `parentloc`.`id`
                LEFT JOIN `plugin_courses_cities` ON `plugin_courses_locations`.`city_id` = `plugin_courses_cities`.`id`
                LEFT JOIN `plugin_courses_location_types` ON `plugin_courses_locations`.`location_type_id` = `plugin_courses_location_types`.`id`
                LEFT JOIN `plugin_courses_counties` ON `plugin_courses_locations`.`county_id` = `plugin_courses_counties`.`id`
                WHERE `plugin_courses_locations`.`delete` = 0 ".$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
                ->execute()
                ->as_array();
            $list = $query;
        //prepare array to return
        $return = array();
        if (is_array($list) AND count($list) > 0)
        {
            //go through list
            $i=0;
            foreach ($list as $elem => $val)
            {
                $return[$i]['parent'] = '<a href="/admin/courses/edit_location/?id='.$val['id'].'">'.(($val['parent']===$val['name'])? '' : $val['parent']).'</a>';
                $return[$i]['name'] = '<a href="/admin/courses/edit_location/?id='.$val['id'].'">'.$val['name'].'</a>';
                $return[$i]['city'] = '<a href="/admin/courses/edit_location/?id='.$val['id'].'">'.$val['city'].'</a>';
                $return[$i]['county'] = '<a href="/admin/courses/edit_location/?id='.$val['id'].'">'.$val['county'].'</a>';
                $return[$i]['edit'] = '<a href="/admin/courses/edit_location/?id='.$val['id'].'">Edit</a>';
                $return[$i]['remove'] = '<a href="#" class="delete" data-id="'.$val['id'].'">Delete</a>';
                $i++;
            }
        }


        return $return;
    }

    private static function get_main_locations($limit, $offset, $sort, $dir, $search = false, $owned_by = null)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (
            `plugin_courses_locations`.`name` like '%".$search."%' OR
            `plugin_courses_locations`.`summary` like '%".$search."%' OR
            `plugin_courses_locations`.`address1` like '%".$search."%' OR
            `plugin_courses_locations`.`address2` like '%".$search."%'  OR
            `plugin_courses_locations`.`address3` like '%".$search."%')
            OR (
            `locs`.`name` like '%".$search."%' OR
            `locs`.`summary` like '%".$search."%' OR
            `locs`.`address1` like '%".$search."%' OR
            `locs`.`address2` like '%".$search."%'  OR
            `locs`.`address3` like '%".$search."%')";
        }

        if (is_numeric($owned_by)) {
            $_search .= " AND plugin_courses_locations.owned_by=$owned_by";
        }
        $_limit = ($limit > -1) ? ' LIMIT '.$offset.','.$limit : '';
        $query = DB::query(Database::SELECT, "SELECT DISTINCT
        `plugin_courses_locations`.`id`,
        `plugin_courses_locations`.`name`,
        `plugin_courses_counties`.`name` as `county`,
        `plugin_courses_cities`.`name` as `city`,
        `plugin_courses_location_types`.`type`,
        (SELECT count(*) from `plugin_courses_locations` `subs` WHERE `plugin_courses_locations`.`id` = `subs`.`parent_id`) as `subcount`
        FROM `plugin_courses_locations`
            LEFT JOIN `plugin_courses_cities` ON `plugin_courses_locations`.`city_id` = `plugin_courses_cities`.`id`
            LEFT JOIN `plugin_courses_location_types` ON `plugin_courses_locations`.`location_type_id` = `plugin_courses_location_types`.`id`
            LEFT JOIN `plugin_courses_counties` ON `plugin_courses_locations`.`county_id` = `plugin_courses_counties`.`id`
            LEFT JOIN `plugin_courses_locations` `locs` ON `plugin_courses_locations`.`id` = `locs`.`parent_id`
            WHERE `plugin_courses_locations`.`delete` = 0 AND `plugin_courses_locations`.`parent_id` IS NULL".$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
            ->execute()
            ->as_array();
        return $query;
    }

    private static function get_sublocations($in, $limit, $offset, $sort, $dir, $search = false, $owned_by = null)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (
            `plugin_courses_locations`.`name` like '%".$search."%' OR
            `plugin_courses_locations`.`summary` like '%".$search."%' OR
            `plugin_courses_locations`.`address1` like '%".$search."%' OR
            `plugin_courses_locations`.`address2` like '%".$search."%'  OR
            `plugin_courses_locations`.`address3` like '%".$search."%') ";
        }
        if (is_numeric($owned_by)) {
            $_search .= " AND plugin_courses_locations.owned_by=$owned_by";
        }
        $_limit = ($limit > -1) ? ' LIMIT '.$offset.','.$limit : '';
        $query = DB::query(Database::SELECT,
            "SELECT `plugin_courses_locations`.`id`, `plugin_courses_locations`.`name`, `plugin_courses_counties`.`name` as `county`, `plugin_courses_cities`.`name` as `city`, `plugin_courses_location_types`.`type`, `parent`.`name` as parent FROM `plugin_courses_locations`
            LEFT JOIN `plugin_courses_cities` ON `plugin_courses_locations`.`city_id` = `plugin_courses_cities`.`id`
            LEFT JOIN `plugin_courses_location_types` ON `plugin_courses_locations`.`location_type_id` = `plugin_courses_location_types`.`id`
            LEFT JOIN `plugin_courses_counties` ON `plugin_courses_locations`.`county_id` = `plugin_courses_counties`.`id`
            LEFT JOIN `plugin_courses_locations` parent ON `plugin_courses_locations`.`parent_id` = `plugin_courses_locations`.`id`
            WHERE `plugin_courses_locations`.`delete` = 0 AND `plugin_courses_locations`.`parent_id` = ".$in['id'].$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
            ->execute()
            ->as_array();
        $in['sub'] = $query;
        return $in;
    }

	/*
	 * Recursively get all sublocations
	 * Returns a list of IDs of all descendants of the specified location
	 * @param id			int		the ID of the specified location
	 * @param include_self	bool	whether or not to include the specified location in the results
	 */
	public static function get_all_sublocation_ids($id, $include_self = FALSE)
	{
		$return                = $include_self ? array($id) : array();
		$parent_ids            = array($id);
		$last_descendant_found = FALSE;

		/*
		 * Get the specified locations, sublocations.
		 * If there are none, we're done
		 * If there are some, get their sublocations and repeat
		 */
		// < 10 to prevent infinite loops. There shouldn't be any infinite loops anyway.
		$i = 0;
		while ( ! $last_descendant_found AND $i < 10)
		{
			$child_ids = self::get_child_ids($parent_ids);
			if (count($child_ids) > 0)
			{
				$parent_ids = array_diff($child_ids, $return); // remove duplicates
				$return     = array_merge($return, $child_ids);
			}
			else
			{
				$last_descendant_found = TRUE;
			}
			$i++;
		}
		return $return;

	}

	/*
	 * Returns a list of IDs of direct children of a specified location(s)
	 * @param parent_ids	array	the IDs of the parent location
	 */
	public static function get_child_ids($parent_ids)
	{
		$child_ids = array();
		$q = DB::select()->from('plugin_courses_locations')
            ->where('parent_id', 'in', $parent_ids)
            ->where('delete','=',0)
            ->execute()->as_array();
		foreach ($q as $location)
		{
			$child_ids[] = $location['id'];
		}
		return $child_ids;
	}


    public static function get_location($id)
    {
        if (!is_numeric($id)) {
            return array();
        }
        $query = DB::query(Database::SELECT,
            "SELECT `plugin_courses_locations`.*, `plugin_courses_counties`.`name` as `county`, `plugin_courses_cities`.`name` as `city`, `plugin_courses_location_types`.`type`, `parent`.`name` as parent FROM `plugin_courses_locations`
            LEFT JOIN `plugin_courses_cities` ON `plugin_courses_locations`.`city_id` = `plugin_courses_cities`.`id`
            LEFT JOIN `plugin_courses_location_types` ON `plugin_courses_locations`.`location_type_id` = `plugin_courses_location_types`.`id`
            LEFT JOIN `plugin_courses_counties` ON `plugin_courses_locations`.`county_id` = `plugin_courses_counties`.`id`
            LEFT JOIN `plugin_courses_locations` parent ON `plugin_courses_locations`.`parent_id` = `plugin_courses_locations`.`id`
            WHERE `plugin_courses_locations`.`delete` = 0 AND `plugin_courses_locations`.`id` =".$id)
            ->execute()
            ->as_array();
        if(sizeof($query)>0){
            return $query[0];
        }else{
            return array();
        }
    }

    public static function remove_location($id)
    {
        $logged_in_user = Auth::instance()->get_user();

        DB::update('plugin_courses_locations')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'delete' => 1
            ))
            ->where('parent_id', '=', $id)
            ->execute();

       // delete rows
       DB::delete('plugin_courses_rows')
            ->where('plugin_courses_rows.location_id', '=', $id)
            ->execute();

        $ret = DB::update('plugin_courses_locations')
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

    public static function save_location($data)
    {

        // get row data
        $add_edit_rows = false;
        if (isset($data['added_rows_names']) AND isset($data['added_rows_seats'])) {
            $added_rows_names = explode(",", $data['added_rows_names']);
            $added_rows_seats = explode(",", $data['added_rows_seats']);
            $added_rows = array();
            if (sizeof($added_rows_names) == sizeof($added_rows_seats)) {
                if (sizeof($added_rows_names)>0) {
                    $add_edit_rows = true;
                    $row = array();
                    for ($i = 0; $i < sizeof($added_rows_seats); $i++) {
                        array_push($row, $added_rows_names[ $i ]);
                        array_push($row, $added_rows_seats[ $i ]);
                        array_push($added_rows, $row);
                        $row = array();
                    }
                }
            }
            else {
                return 0;
            }


            unset($data[ 'added_rows_names' ]);
            unset($data['added_rows_seats']);
        }

		// add / update
		$save_action = 'add';
		$item_id = 0;
        unset($data['redirect']);
        //Add the necessary values to the $data array for update
        unset($data['new_type']);
        unset($data['new_city']);
        $logged_in_user = Auth::instance()->get_user();
        if ((int)$data['id'] > 0)
        {
            $id = (int)$data['id'];
            unset($data['id']);
            if ((int)$data['parent_id'] == 0)
            {
                $data['parent_id'] = NULL;
            }
            $data['modified_by'] = $logged_in_user['id'];
            $data['date_modified'] = date('Y-m-d H:i:s');
            $query = DB::update('plugin_courses_locations')
                ->set($data)
                ->where('id', '=', $id)
                ->execute();

			$save_action = 'update';
			$item_id = $id;
        }
        else
        {
            if ((int)$data['parent_id'] == 0)
            {
                $data['parent_id'] = NULL;
            }
            $data['created_by'] = $logged_in_user['id'];
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['delete'] = 0;
            $query = DB::insert('plugin_courses_locations', array_keys($data))
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
				'Location ID #'.$item_id.':  "'.$data['name'].'" has been '.(($save_action == 'add')? 'CREATED' : 'UPDATED' ).'.',
				'success popup_box'
			);

			// add_edit_rows
            self::delete_rows($item_id);
            if ($add_edit_rows) {

                $sql_query = "INSERT INTO plugin_courses_rows (plugin_courses_rows.name, plugin_courses_rows.`seats`, plugin_courses_rows.`location_id`) VALUES ";
                for ($i = 0; $i < sizeof($added_rows); $i++) {
                    if ($i == 0) {
                        $sql_query .= "(" . Database::instance()->escape($added_rows[ $i ][ 0 ]) . ", " . Database::instance()->escape($added_rows[ $i ][ 1 ]) . ", " . $item_id . ")";
                    }
                    else {
                        $sql_query .= ",(" . Database::instance()->escape($added_rows[ $i ][ 0 ]) . ", " . Database::instance()->escape($added_rows[ $i ][ 1 ]) . ", " . $item_id . ")";
                    }
                }
                $query = DB::query(Database::INSERT, $sql_query)->execute();
            }

		}
		else
		{
			IbHelpers::set_message (
				'Sorry! There was a problem with '.(($save_action == 'add')? 'CREATION' : 'UPDATE' )
				.' of '.( ($item_id > 0)? 'Location ID #'.$item_id : 'Location' ).': "'.$data['name'].'".<br />'
				.'Please make sure, that form is filled properly and Try Again!',
				'error popup_box'
			);
		}

        return $item_id;
    }

    public static function validate_location($data)
    {
        //create empty errors array
        $errors = array();
        //check name must be min 3 chars
        if (@strlen($data['name']) < 3)
        {
            $errors[] = "Location name must contains min 3 characters";
        }
        if (@strlen($data['address1']) < 3)
        {
            $errors[] = "Location address must contains min 3 characters";
        }
        return $errors;

    }

    public static function get_location_types()
    {
        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_location_types` WHERE `delete` = 0 ORDER BY `type`")
            ->execute()
            ->as_array();

        return $query;
    }

    public static function get_all_locations_html($get = false)
    {
        $query = DB::query(Database::SELECT, "SELECT
        `id`,
        `name`
        FROM
        `plugin_courses_locations`
        WHERE
        `delete` = 0
        AND
        `publish` = 1
        AND
        `parent_id` IS NULL
        ORDER BY
        `name`")
            ->execute()
            ->as_array();
        $childs = DB::query(Database::SELECT, "SELECT
        `id`,
        `parent_id`,
        `name`
        FROM
        `plugin_courses_locations`
        WHERE
        `delete` = 0
        AND
        `publish` = 1
        AND
        `parent_id` IS NOT NULL
        ORDER BY
        `name`")
            ->execute()
            ->as_array();
        $childs = array();
        $view =  View::factory(
            'front_end/parent_dropdown_list',
            array(
                'parents' => $query,
                'childs' => $childs,
                'location' => @$get['location']
            )
        );
        return $view;
    }

    public static function get_all_locations($location_type_id = 1)
    {
        $query = DB::select()->from('plugin_courses_locations')->where('delete','=',0)->and_where('publish','=',1);
        if ($location_type_id){
            $query->and_where('location_type_id', '=', $location_type_id);
        }
        return $query->execute()->as_array();
    }

    public static function get_all_locations_with_children()
    {
        $locations = DB::select()->from('plugin_courses_locations')->where('delete','=',0)->and_where('publish','=',1)->and_where('parent_id','!=','')->execute()->as_array();
        return $locations;
    }

	public static function get_locations_with_sublocation_ids()
	{
		$sublocations = DB::select()->from('plugin_courses_locations')->where('delete', '=', 0)->where('parent_id', '!=', '');
		return DB::select('l.id', 'l.name', 'l.parent_id', array(DB::expr(' GROUP_CONCAT(`s`.`id`)'), 'sublocations'))
			->from(array('plugin_courses_locations', 'l'))
			->join(array($sublocations, 's'),'LEFT')->on('s.parent_id', '=', 'l.id')
			->where('l.delete', '=', 0)
			->group_by('l.id')
			->order_by(DB::expr('IF(isnull(`l`.`parent_id`), 0, 1)')) // put top-level locations first
			->order_by('l.name')
			->execute()
			->as_array();
	}

    public static function get_parent_locations()
    {
//        $locations = DB::select()->from('plugin_courses_locations')->where('delete', '=', 0)->where('parent_id', '=', '')->execute()->as_array();
        $locations = DB::query(Database::SELECT,"SELECT * FROM `plugin_courses_locations` WHERE `delete` = 0 AND ISNULL(`parent_id`)")->execute()->as_array();
        return $locations;
    }

    public static function get_children_locations($id)
    {
        $sublocations = array();
        if ( ! is_null($id))
        {
            $sublocations = DB::select()
                ->from('plugin_courses_locations')
                ->where('delete', '=', 0)
                ->where('parent_id', '=', $id)
                ->order_by('name', 'asc')
                ->execute()
                ->as_array();
        }
        return $sublocations;
    }

    public static function get_parent_location_id($id)
    {
        $parent = DB::select('parent_id')->from('plugin_courses_locations')->where('id', '=', $id)->execute()->as_array();
        $location = $parent[0]['parent_id']!=''? $parent[0]['parent_id'] : $id;
        return $location;
    }

    public static function get_location_name_or_parent($id)
    {
        $result = '';
        if ( ! is_null($id))
        {
            $location = DB::select()->from('plugin_courses_locations')->where('id','=',$id)->execute()->as_array();
            if ($location)
            {
                if ($location[0]['parent_id'] == 'Null' OR $location[0]['parent_id'] == '')
                {
                    $result = $location[0]['name'];
                }
                else
                {
                    $location = DB::select()->from('plugin_courses_locations')->where('id','=',$location[0]['parent_id'])->execute()->as_array();
                    if ( $location)
                    {
                        $result = $location[0]['name'];
                    }
                }
            }
        }
        return $result;
    }

    public static function get_children_location_html($id,$location_id)
    {
        $html = '';
        if ( ! is_null($id))
        {
            $html .= '<option value="">Select Sub Location</option>';
            $locations = self::get_children_locations($id);
            foreach($locations as $key=>$location)
            {
                $selected = ( $location['id'] == $location_id) ? ' selected="selected"' : '' ;
                $html .= '<option data-parent_id="'.$location['parent_id'].'" value="'.$location['id'].'"'.$selected.'>'.$location['name'].'</option>';
            }
        }
        return $html;
    }

    public static function get_rows($location_id = null)
    {
        return DB::select()
            ->from('plugin_courses_rows')
            ->where('plugin_courses_rows.location_id', '=', $location_id)
            ->execute()
            ->as_array();
    }

    public static function delete_rows($location_id = null)
    {
        $rows  = self::get_rows($location_id);
        $arr_of_rows = array();

        foreach ($rows as $row){
            array_push($arr_of_rows,$row['id']);
        }

        if (sizeof($arr_of_rows)>0) {
            Model_Schedules::delete_schedule_zones_with_specified_rows($arr_of_rows);
        }

        return  DB::delete('plugin_courses_rows')
            ->where('plugin_courses_rows.location_id', '=', $location_id)
            ->execute();
    }
    
    public static function autocomplete_list($term = null, $children_identifier = '0')
    {
        // Children identifier 0 = include children, 1 = exclude children, 2 = exclude parents
        if($children_identifier !== '2') {
            $parent_locations = DB::select(array('id', 'value'), array('name', 'label'))
                ->from(self::TABLE_LOCATIONS)
                ->where('delete', '=', 0)
                ->and_where('parent_id', 'is', null);
            if ($term) {
                $parent_locations->and_where('name', 'like', '%' . $term . '%');
            }
            $parent_locations = $parent_locations->execute()->as_array();
        } else {
            $parent_locations = array();
        }
        if($children_identifier !== '1') {
            $select_column = ($children_identifier === '2') ? "children.name as label" : "CONCAT(parents.name, ' ', children.name) as label";
            $child_locations = DB::select(array('children.id', 'value'),
                DB::expr($select_column))
                ->from(array(self::TABLE_LOCATIONS, 'parents'))
                ->join(array(self::TABLE_LOCATIONS, 'children'), 'inner')->on('parents.id', '=', 'children.parent_id')
                ->where('children.delete', '=', 0)
                ->and_where('children.parent_id', 'is not', null);
            if ($term) {
                $child_locations->and_where_open();
                $child_locations->or_where('children.name', 'like', '%' . $term . '%');
                if($children_identifier !== '2') {
                    $child_locations->or_where('parents.name', 'like', '%' . $term . '%');
                }
                $child_locations->and_where_close();
            }
            $child_locations = $child_locations->execute()->as_array();
        } else {
            $child_locations = array();
        }
       
        return array_merge($parent_locations, $child_locations);
    }

    public static function autocomplete_locations($term = null)
    {
        $select = DB::select(
            array('locations.id', 'value'),
            DB::expr("CONCAT_WS(' - ', plocations.name, locations.name) as label")
        )
            ->from(array(self::TABLE_LOCATIONS, 'locations'))
                ->join(array(self::TABLE_LOCATIONS, 'plocations'), 'left')
                    ->on('locations.parent_id', '=', 'plocations.id')
            ->where('locations.delete', '=', 0);

        if ($term) {
            $select->and_where_open()
                ->or_where('locations.name', 'like', '%' . $term. '%')
                ->or_where('plocations.name', 'like', '%' . $term . '%')
                ->and_where_close();
        }
        $select->order_by('plocations.name')->order_by('locations.name');
        $locations = $select->execute()->as_array();
        return $locations;
    }
}
