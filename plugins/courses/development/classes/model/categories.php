<?php defined('SYSPATH') or die('No direct script access.');

class Model_Categories extends Model
{
    const TABLE_CATEGORIES = 'plugin_courses_categories';

    public static function count_categories($search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (category like '%" . $search . "%' OR summary like '%" . $search . "%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as `count` FROM `plugin_courses_categories` WHERE `delete` = 0 " . $_search . ";")
            ->execute()
            ->as_array();
        return $query['0']['count'];
    }

    public static function get_categories_without_parent()
    {
        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_categories` WHERE `delete` = 0 AND `parent_id` is NULL ORDER BY `category` ASC")
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_categories_tutorial()
    {
        return DB::select()->from('plugin_courses_categories')->where('grinds_tutorial','=',1)->execute()->as_array();
    }

    public static function get_all_categories($order_by = 'category')
    {
        if ($order_by != 'RAND()')
        {
            $order_by = "`".$order_by."`";
        }

        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_categories` WHERE `delete` = 0 ORDER BY $order_by;")
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_all_published_categories($args = [])
    {
        $query = DB::select()
            ->from('plugin_courses_categories')
            ->where('publish', '=', 1)
            ->where('delete', '=', 0)
            ->order_by('order', 'asc')
            ->order_by('category');

        if (isset($args['include_empty']) && !$args['include_empty']) {
            $query = DB::select('category.*')
                ->from(array('plugin_courses_courses',    'course'))
                ->join(array('plugin_courses_categories', 'category'))->on('course.category_id', '=', 'category.id')
                ->where('category.publish', '=', 1)
                ->where('category.delete',  '=', 0)
                ->group_by('category.id')
                ->order_by('order', 'asc')
                ->order_by('category');
            ;

            $provider_ids = Model_Providers::get_providers_for_host();

            if ($provider_ids) {
                $query
                    ->join(array('plugin_courses_courses_has_providers', 'chp'))->on('chp.course_id', '=', 'course.id')
                    ->where('chp.provider_id', 'IN', $provider_ids);
            }
        }

        return $query->execute()->as_array();
    }

    public static function get_categories($limit, $offset, $sort, $dir, $search = false)
    {
        $items = self::get_main_categories($limit, $offset, $sort, $dir, $search);
        $list = array();
        if (is_array($items) AND count($items) > 0) {
            foreach ($items as $p_key => $parent) {
                //get subcategory list for all parent elements
                $list[] = self::get_subcategories($parent, $limit, $offset, $sort, $dir, $search);
            }
        }
        //prepare array to return
        $return = array();
        if (is_array($list) AND count($list) > 0) {
            //go trough list
            $i = 0;
            foreach ($list as $elem => $val) {
                $modified = IbHelpers::relative_time_with_tooltip($val['date_modified']);
                $return[$i]['category'] = '<a href="/admin/courses/edit_category/?id=' . $val['id'] . '">' . $val['category'] . '</a>';
                $return[$i]['summary'] = '<a href="/admin/courses/edit_category/?id=' . $val['id'] . '">' . $val['summary'] . '</a>';
                $return[$i]['start_time'] = '<a href="/admin/courses/edit_category/?id=' . $val['id'] . '">' . (is_null($val['start_time']) ? '' : date('H:i',strtotime($val['start_time'])) ). '</a>';
                $return[$i]['end_time'] = '<a href="/admin/courses/edit_category/?id=' . $val['id'] . '">' . (is_null($val['end_time']) ? '' : date('H:i',strtotime($val['end_time'])) ). '</a>';
                $return[$i]['order'] = '<a href="/admin/courses/edit_category/?id=' . $val['id'] . '">' . (is_null($val['order']) ? '' : $val['order']). '</a>';
                if ($val['tut'] == '1')
                {
                    $return[$i]['grinds_tutorial'] = '<a href="#" class="tutorial" data-grinds_tutorial="1" data-id="' . $val['id'] . '"><i class="icon-ok"></i></a>';
                }
                else
                {
                    $return[$i]['grinds_tutorial'] = '<a href="#" class="tutorial" data-grinds_tutorial="0" data-id="' . $val['id'] . '"><i class="icon-ban-circle"></i></a>';
                }
                if ($val['pbl'] == '1') {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="' . $val['id'] . '"><i class="icon-ok"></i></a>';
                } else {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="' . $val['id'] . '"><i class="icon-ban-circle"></i></a>';
                }
                $return[$i]['last_modified'] = '<a href="/admin/courses/edit_category/?id=' . $val['id'] . '">'.$modified.'</a>';
                $return[$i]['actions'] = '<a href="/admin/courses/edit_category/?id=' . $val['id'] . '">Edit</a>'
                    .'<a href="#" class="delete" data-id="' . $val['id'] . '">Delete</a>';
                $i++;
                if (is_array($val['sub']) AND count($val['sub']) > 0) {
                    foreach ($val['sub'] as $selem => $sval) {
                        $return[$i]['category'] = '<a href="/admin/courses/edit_category/?id=' . $sval['id'] . '"><i class="icon-arrow-right margin-left"/><span class="margin-left">' . $sval['category'] . '</span></a>';
                        $return[$i]['summary'] = '<a href="/admin/courses/edit_category/?id=' . $sval['id'] . '">' . $sval['summary'] . '</a>';
                        $return[$i]['edit'] = '<a href="/admin/courses/edit_category/?id=' . $sval['id'] . '">Edit</a>';
                        $return[$i]['start_time'] = '';
                        $return[$i]['end_time'] = '';
                        $return[$i]['grinds_tutorial'] = '';
                        $return[$i]['last_modified'] = '';
                        $return[$i]['actions'] = '';
                        if ($sval['pbl'] == '1') {
                            $return[$i]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="' . $sval['id'] . '"><i class="icon-ok"></i></a>';
                        } else {
                            $return[$i]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="' . $sval['id'] . '"><i class="icon-ban-circle"></i></a>';
                        }
                        $return[$i]['remove'] = '<a href="#" class="delete" data-id="' . $sval['id'] . '">Delete</a>';
                        $i++;
                    }
                }
            }
        }


        return $return;
    }


    private static function get_main_categories($limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (
            `plugin_courses_categories`.`category` like '%" . $search . "%' OR `plugin_courses_categories`.`summary` like '%" . $search . "%'
            ) OR (
            `cats`.`category` like '%" . $search . "%' OR `cats`.`summary` like '%" . $search . "%')";
        }
        $_limit = ($limit > -1) ? ' LIMIT ' . $offset . ',' . $limit : '';
        $query = DB::query(Database::SELECT,
            "SELECT `plugin_courses_categories`.`id`, `plugin_courses_categories`.`category`,`plugin_courses_categories`.`grinds_tutorial` as `tut`, `plugin_courses_categories`.`summary`, `plugin_courses_categories`.`publish` as `pbl`, `plugin_courses_categories`.`start_date`, `plugin_courses_categories`.`end_date`, `plugin_courses_categories`.`start_time`, `plugin_courses_categories`.`end_time`, `plugin_courses_categories`.`date_modified`, `plugin_courses_categories`.`order`
            FROM `plugin_courses_categories`
            LEFT JOIN
            `plugin_courses_categories` `cats`
            ON
            `plugin_courses_categories`.`id` = `cats`.`parent_id`
            WHERE `plugin_courses_categories`.`parent_id` IS NULL AND `plugin_courses_categories`.`delete` = 0 " . $_search . " ORDER BY " . $sort . " " . $dir . " " . $_limit)
            ->execute()
            ->as_array();
        return $query;
    }

    private static function get_subcategories($in, $limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (`category` like '%" . $search . "%' OR `summary` like '%" . $search . "%')";
        }
        $_limit = ($limit > -1) ? ' LIMIT ' . $offset . ',' . $limit : '';
        $query = DB::query(Database::SELECT,
            "SELECT `id`, `category`, `summary`, `publish` as `pbl` , `start_date`, `end_date`, `date_modified` FROM `plugin_courses_categories` WHERE `parent_id` = " . $in['id'] . " AND `delete` = 0 " . $_search . " ORDER BY " . $sort . " " . $dir . " " . $_limit)
            ->execute()
            ->as_array();
        $in['sub'] = $query;
        return $in;
    }

    public static function get_category($id)
    {
        $data = DB::select()
            ->from('plugin_courses_categories')
            ->where('id', '=', $id)
            ->execute()
            ->as_array();

        return $data[0];
    }

    /**
     * Purpose: to GET the table ID for the item
     * @param $category
     * @return mixed
     */
    public static function get_id($category_name = NULL)
    {
        if ($category_name != NULL) {
            $db_category_name = str_replace('-', ' ', trim($category_name)); //REPLACE HYPHENS WITH SPACES AND TRIM ON PHP AND SQL LEVEL

            $q = DB::select('id')
                ->from('plugin_courses_categories')
                ->where('delete', '=', 0)
                ->where('publish', '=', 1)
                ->where('category', '=', $db_category_name);

            $r = $q->execute()->as_array();

            return $r[0]['id'];
        }

        return null;

    }

    public static function set_publish_category($id, $state)
    {
        if ($state == '1') {
            $published = 0;
        } else {
            $published = 1;
        }
        $logged_in_user = Auth::instance()->get_user();
        $query = DB::update("plugin_courses_categories")
            ->set(array(
                'publish' => $published,
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s')
            ))
            ->where('id', '=', $id)
            ->execute();
        $response = array();
        if ($query > 0) {
            $response['message'] = 'success';
        } else {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occured! Please contact with support!';
        }
        return $response;
    }

    public static function set_tutorial_category($id, $state)
    {
        if ($state == '1') {
            $tutorial = 0;
        } else {
            $tutorial = 1;
        }
        $logged_in_user = Auth::instance()->get_user();
        $query = DB::update("plugin_courses_categories")
            ->set(array(
                'grinds_tutorial' => $tutorial,
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s')
            ))
            ->where('id', '=', $id)
            ->execute();
        $response = array();
        if ($query > 0) {
            $response['message'] = 'success';
        } else {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occured! Please contact with support!';
        }
        return $response;
    }

    public static function remove_category($id)
    {
        $logged_in_user = Auth::instance()->get_user();
        $data = DB::select()
            ->from('plugin_courses_categories')
            ->where('parent_id', '=', $id)
            ->execute()
            ->as_array();
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $elem => $val) {
                DB::update('plugin_courses_courses')
                    ->set(array(
                        'modified_by' => $logged_in_user['id'],
                        'date_modified' => date('Y-m-d H:i:s'),
                        'deleted' => 1
                    ))
                    ->where('category_id', '=', $val['id'])
                    ->execute();

            }
        }
        DB::update("plugin_courses_categories")
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'delete' => 1
            ))
            ->where('parent_id', '=', $id)
            ->execute();

        DB::update('plugin_courses_courses')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'deleted' => 1
            ))
            ->where('category_id', '=', $id)
            ->execute();

        $ret = DB::update('plugin_courses_categories')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'delete' => 1
            ))
            ->where('id', '=', $id)
            ->execute();
        if ($ret > 0) {
            $response['message'] = 'success';
        } else {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }
        return $response;
    }


    public static function save_category($data)
    {
        // add / update
        $save_action = 'add';
        $item_id = 0;
        unset($data['redirect']);
        $data['start_date'] = $data['start_date'] == '' ? NULL : date('Y-m-d H:i:s',strtotime($data['start_date']));
        $data['end_date'] = $data['end_date'] == '' ? NULL : date('Y-m-d H:i:s',strtotime($data['end_date']));
        $data['start_time'] = $data['start_time'] == '' ? null : date('H:i:s',strtotime($data['start_time']));
        $data['end_time'] = $data['end_time'] == '' ? null : date('H:i:s',strtotime($data['end_time']));
        //Add the necessary values to the $data array for update
        $logged_in_user = Auth::instance()->get_user();
        if ((int)$data['id'] > 0) {
            $id = (int)$data['id'];
            unset($data['id']);
            if ((int)$data['parent_id'] == 0) {
                $data['parent_id'] = NULL;
            }
            $data['modified_by'] = $logged_in_user['id'];
            $data['date_modified'] = date('Y-m-d H:i:s');
            $query = DB::update('plugin_courses_categories')
                ->set($data)
                ->where('id', '=', $id)
                ->execute();

            $save_action = 'update';
            $item_id = $id;
        } else {
            if ((int)$data['parent_id'] == 0) {
                $data['parent_id'] = NULL;
            }
            $data['created_by'] = $logged_in_user['id'];
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['delete'] = 0;
            $query = DB::insert('plugin_courses_categories', array_keys($data))
                ->values($data)
                ->execute();

            $save_action = 'add';
            $item_id = (isset($query[0]) AND $query[0] > 0) ? $query[0] : 0;
        }

        // Set Successful / Not Successful Insert / Update Message
        if (
            ($save_action == 'add' AND $query[0] > 0) OR
            ($save_action == 'update' AND $query == 1)
        ) {
            $updates_timeslots = '';
            /* disabling this as too dangerous
            if ($data['start_time'] != '' AND $data['end_time'] != '')
            {
                $schedules = DB::select('s.id')->from(array('plugin_courses_schedules','s'))
                    ->join(array('plugin_courses_courses','c'))
                    ->on('s.course_id','=','c.id')
                    ->where('c.category_id','=',$item_id)
                    ->execute()
                    ->as_array();
                if (count($schedules) > 0) {
                    $events = DB::select('id', 'datetime_end', 'datetime_start')
                        ->from('plugin_courses_schedules_events')
                        ->where('schedule_id', 'IN', $schedules)
                        ->where('datetime_end', '>', date("Y-m-d H:i:s"))
                        ->execute()
                        ->as_array();
                    $user = Auth::instance()->get_user();
                    if (count($events) > 0) {
                        foreach ($events as $k => $event) {
                            $update = array(
                                'datetime_start' => (date('Y-m-d',
                                        strtotime($event['datetime_start'])) . ' ' . date('H:i:s',
                                        strtotime($data['start_time']))),
                                'datetime_end' => (date('Y-m-d',
                                        strtotime($event['datetime_end'])) . ' ' . date('H:i:s',
                                        strtotime($data['end_time']))),
                                'date_modified' => date("Y-m-d H:i:s"),
                                'modified_by' => $user['id']
                            );
                            DB::update('plugin_courses_schedules_events')->set($update)->where('id', '=',
                                $event['id'])->execute();
                        }
                        $updates_timeslots = ' And ' . count($events) . ' timeslots have been updated with the category default start and end times';
                    }
                }
            }*/

            IbHelpers::set_message(
                'Category ID #' . $item_id . ':  "' . $data['category'] . '" has been ' . (($save_action == 'add') ? 'CREATED' : 'UPDATED') . '.' . $updates_timeslots,
                'success popup_box'
            );
        } else {
            IbHelpers::set_message(
                'Sorry! There was a problem with ' . (($save_action == 'add') ? 'CREATION' : 'UPDATE')
                . ' of ' . (($item_id > 0) ? 'Category ID #' . $item_id : 'Category') . ': "' . $data['category'] . '".<br />'
                . 'Please make sure, that form is filled properly and Try Again!',
                'error popup_box'
            );
        }

        return $item_id;
    }

    public static function validate_category($data)
    {
        //create empty errors array
        $errors = array();
        //check name must be min 3 chars
        if (@strlen($data['category']) < 3) {
            $errors[] = "Category name must contains min 3 characters";
        }
        return $errors;

    }


    public static function get_front_categories()
    {
        //colors
        $colors = array('grn', 'blue', 'orng', 'red', 'purple', 'lightblue', 'lightorange', 'lightred');

        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_categories` WHERE `delete` = 0 AND `publish` = 1 order by `category`")
            ->execute()
            ->as_array();

        $view = $plugin_feed_html = View::factory(
            'front_end/category_list',
            array(
                'items' => $query,
                'colors' => $colors
            )
        );
        return $view;
    }

    public static function get_by_name($name = false)
    {
        if ($name !== false) {
            $data = DB::select()
                ->from('plugin_courses_categories')
                ->where('category', '=', $name)
                ->execute()
                ->as_array();

            return $data[0];
        } else {
            return false;
        }
    }


    /**
     * @param string $breadcrumbs
     * @return array|null
     */
    public static function get_from_breadcrumbs($breadcrumbs)
    {
        $category = NULL;

        $breadcrumbs = explode('/', $breadcrumbs);
        $categories = self::get_all_categories();

        if (($m = count($breadcrumbs)) > 0 AND ($n = count($categories)) > 0) {
            for ($i = $j = 0, $found = TRUE; $i < $m AND $found; $i++) {
                $found = FALSE;

                while (!$found AND $j < $n) {
                    if (IbHelpers::generate_friendly_url($categories[$j++]['category']) == $breadcrumbs[$i]) {
                        $found = TRUE;
                    }
                }
            }

            $category = ($found) ? $categories[--$j] : NULL;
        }

        return $category;
    }

    public static function get_all_categories_html($get = false)
    {
        $query = DB::query(Database::SELECT,
            "SELECT `id`, `category` AS `name` FROM `plugin_courses_categories` WHERE `delete` = 0 AND `publish` = 1 ORDER BY `name`;")
            ->execute()
            ->as_array();
        $view = View::factory(
            'front_end/dropdown_list',
            array(
                'items' => $query,
                'selected' => @$get['category']
            )
        );
        return $view;
    }

}
