<?php defined('SYSPATH') or die('No direct script access.');

class Model_Autotimetables extends Model
{
    const MAIN_TABLE = 'plugin_courses_autotimetables';
    const CATEGORIES_TABLE = 'plugin_courses_categories';
    const LOCATIONS_TABLE = 'plugin_courses_locations';
    const YEARS_TABLE = 'plugin_courses_years';
    const TIMETABLE_YEARS_TABLE = 'plugin_courses_autotimetables_years';

    public static function save_autotimetable($data)
    {
        $logged_in_user = Auth::instance()->get_user();

        // Prepare data for update/insert query
        $query_data['name']          = @$data['name'];
        $query_data['description']   = @$data['description'];
        $query_data['modified_by']   = $logged_in_user['id'];
        $query_data['date_modified'] = date('Y-m-d H:i:s');
        $query_data['publish']       = @$data['publish'];

        if (isset($data['date_start']) AND $data['date_start'] != '') {
            $query_data['date_start']    = date('Y-m-d', strtotime($data['date_start']));
        }
        if (isset($data['date_end']) AND $data['date_end'] != '') {
            $query_data['date_end']      = date('Y-m-d', strtotime($data['date_end']));
        }
        if (isset($data['category_id']) AND $data['category_id'] != '') {
            $query_data['category_id']   = $data['category_id'];
        }
        if (isset($data['location_id']) AND $data['location_id'] != '') {
            $query_data['location_id']   = $data['location_id'];
        }

        // ID > 0, means it's an existing timetable
        if ((int)$data['id'] > 0)
        {
            $query = DB::update(self::MAIN_TABLE)->set($query_data)->where('id', '=', $data['id'])->execute();
            $item_id = $data['id'];
            $save_action = 'update';
        }
        else
        {
            $query_data['created_by']   = $query_data['modified_by'];
            $query_data['date_created'] = $query_data['date_modified'];
            $query_data['deleted']      = 0;

            $query = DB::insert(self::MAIN_TABLE, array_keys($query_data))->values($query_data)->execute();
            $item_id = (isset($query[0]) AND $query[0] > 0) ? $query[0] : 0;
            $save_action = 'add';
        }

        // Update the autotimetables_years table
        if (isset($data['years']))
        {
            $years = $data['years'];
            $old_years = array();
            $old_years_data = DB::select()->from(self::TIMETABLE_YEARS_TABLE)->where('autotimetable_id', '=', $item_id)->execute()->as_array();
            foreach ($old_years_data as $old_year_data)
            {
                array_push($old_years, $old_year_data['year_id']);
            }

            $add_years = array_diff($years, $old_years);
            foreach ($add_years as $add_year)
            {
                DB::insert(self::TIMETABLE_YEARS_TABLE, array('autotimetable_id', 'year_id'))->values(array($item_id, $add_year))->execute();
            }

            $remove_years = array_diff($old_years, $years);
            if (count($remove_years) > 0)
            {
                DB::delete(self::TIMETABLE_YEARS_TABLE)->where('year_id', 'IN', $remove_years)->and_where('autotimetable_id', '=', $item_id)->execute();
            }
        }

        // Set successful / not successful insert / update message
        if (($save_action == 'add' AND $query[0] > 0) OR ($save_action == 'update' AND $query == 1))
        {
            IbHelpers::set_message('Time table ID #' . $item_id . ':  "' . $data['name'] . '" has been ' . (($save_action == 'add') ? 'CREATED' : 'UPDATED') . '.', 'success popup_box');
        }
        else
        {
            IbHelpers::set_message('Sorry! There was a problem with ' . (($save_action == 'add') ? 'CREATION' : 'UPDATE') . ' of ' . (($item_id > 0) ? 'Time Table ID #' . $item_id : 'Time Table') . ': "' . $data['name'] . '".<br />' . 'Please make sure, that form is filled properly and try again.', 'error popup_box');
        }

        return $query;
    }

    public static function count_autotimetables($search = false)
    {
        $_search = '';
        if ($search) {
            $_search = " AND (name like '%".$search."%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as `count` FROM `".self::MAIN_TABLE."` WHERE `deleted` = 0 ".$_search.";")
            ->execute()
            ->as_array();

        return $query['0']['count'];
    }

    public static function get_all_autotimetables()
    {
        $query = DB::select()->from(self::MAIN_TABLE)->where('deleted', '=', 0)->execute()->as_array();
        return $query;
    }

    public static function get_autotimetable($id)
    {
        $query = DB::select()->from(self::MAIN_TABLE)->where('id', '=', $id)->execute()->as_array();

        $return = $query[0];
        $return['years'] = DB::select(
            array(self::YEARS_TABLE.'.id', 'id'),
            array(self::YEARS_TABLE.'.year', 'year'),
            array(self::TIMETABLE_YEARS_TABLE.'.autotimetable_id', 'att_id')
        )
            ->from(self::YEARS_TABLE)
            ->join(self::TIMETABLE_YEARS_TABLE)
            ->on(self::YEARS_TABLE.'.id', '=', self::TIMETABLE_YEARS_TABLE.'.year_id')
            ->where(self::TIMETABLE_YEARS_TABLE.'.autotimetable_id', '=', $id)
            ->and_where(self::YEARS_TABLE.'.delete', '=', 0)
            ->execute()
            ->as_array();

        return $return;
    }

    public static function get_autotimetables($limit = '', $offset = '', $sort = 'id', $dir = 'ASC', $search = false, $column_filters = array())
    {
        $columns   = array();
        $columns[] = '`att`.`id`';
        $columns[] = '`att`.`name`';
        $columns[] = '`category`.`category`';
        $columns[] = 'location.name';
        $columns[] = '`att`.`date_start`';
        $columns[] = '`att`.`date_end`';
        $sanitized_input = array();

        $_search = '';
        if($search){
            $_search .= " AND (";
            for ($i = 0; $i < count($columns); $i++) {
                $_search .= ($i != 0) ? " OR " : "";
                $_search .= "{$columns[$i]} LIKE :search_{$i}";
                $sanitized_input[":search_{$i}"] = "%$search%";
            }
            $_search .= ")";
        }

        $column_search = '';
        for ($i = 0; $i < count($column_filters); $i++) {
            if (isset($column_filters[$i]) AND $column_filters[$i] != '' AND isset ($columns[$i]) AND $columns[$i] != '') {
                $column_search .= ' AND ';
                $column_search .= $columns[$i] . " LIKE :column_filter_search_{$i} ";
                $sanitized_input[":column_filter_search_{$i}"] = "%$column_filters[$i]%";
            }
        }

        $_limit = ($limit > -1) ? ' LIMIT ' . $offset . ',' . $limit : '';
        $query = DB::query(Database::SELECT,
            'SELECT att.id AS id, '."\n".
                'att.name AS name, '."\n".
                'att.category_id AS category_id, '."\n".
                'category.category AS category, '."\n".
                'att.location_id AS location_id, '."\n".
                'location.name AS location, '."\n".
                'att.date_start AS date_start, '."\n".
                'att.date_end AS date_end, '."\n".
                'att.publish AS publish, '."\n".
                'att.deleted AS deleted'."\n".
            'FROM `'.self::MAIN_TABLE.'` att '."\n".
            'LEFT JOIN `'.self::CATEGORIES_TABLE.'` category '."\n".
            'ON `att`.`category_id` = `category`.`id` '."\n".
            'LEFT JOIN `'.self::LOCATIONS_TABLE.'` location '."\n".
            'ON `att`.`location_id` = `location`.`id` '."\n".
            'WHERE `att`.`deleted` = 0 '."\n".$_search."\n".
            $column_search .
            'ORDER BY '.$sort.' '.$dir.' '.$_limit);

        foreach($sanitized_input as $key => $value){
            $query->param($key, $value);
        }
        $query = $query->execute()->as_array();
        return $query;
    }

    public static function get_autotimetables_as_html($limit, $offset, $sort, $dir, $search = false, $search_filters = array())
    {
        $items = self::get_autotimetables($limit, $offset, $sort, $dir, $search, $search_filters);
        $return = array();

        if (is_array($items) AND count($items) > 0)
        {
            $i = 0;
            $select_anchor = "/admin/courses/edit_autotimetable/?id=";
            foreach ($items as $item => $val)
            {
                $return[$i]['id']         = "<a href='{$select_anchor}{$val['id']}' class='edit-link'>{$val['id']}</a>";
                $return[$i]['name']       = $val['name'];
                $return[$i]['category']   = $val['category'];
                $return[$i]['location']   = $val['location'];
                $return[$i]['date_start'] = $val['date_start'];
                $return[$i]['date_end']   = $val['date_end'];
                $return[$i]['edit'] = "<a href='{$select_anchor}{$val['id']}'><i class='icon-pencil'></i></a>";
                if ($val['publish'] == '1') {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="' . $val['id'] . '"><i class="icon-ok"></i></a>';
                } else {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="' . $val['id'] . '"><i class="icon-ban-circle"></i></a>';
                }
                $return[$i]['actions'] = '<td>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn--full dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span class="sr-only"><?= __(\'Actions\') ?></span>
                                    <span class="icon-ellipsis-h" aria-hidden="true"></span>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                        <button class="btn-link">
                                            <a href="#" class="delete" data-id="' . $val['id'] . '">Delete</a>
                                        </button>       
                                    </li>
                                </ul>
                            </div>
                        </td>';
                $i++;
            }
        }

        return $return;
    }

    public static function get_course_data($id)
    {
        $att_data = self::get_autotimetable($id);

        $years = array();
        foreach ($att_data['years'] as $year)
        {
            array_push($years, $year['id']);
        }

        try
        {
            $timetable_results = DB::select(
				array('plugin_courses_courses.id','course_id'),
				array('plugin_courses_courses.title','title'),
				array(DB::expr('DATE(plugin_courses_schedules_events.datetime_start)'),'date'),
				array('has_years.year_id','year_id'),
				array('plugin_courses_schedules.id', 'schedule_id')
            )->from('plugin_courses_courses')
            ->distinct(TRUE)
            ->join('plugin_courses_schedules','LEFT')
            ->on('plugin_courses_schedules.course_id','=','plugin_courses_courses.id')
            ->join('plugin_courses_schedules_events','LEFT')
            ->on('plugin_courses_schedules_events.schedule_id','=','plugin_courses_schedules.id')
            ->join(array(Model_Courses::TABLE_HAS_YEARS, 'has_years'), 'inner')
                ->on('plugin_courses_courses.id', '=', 'has_years.course_id')
            ->where('plugin_courses_courses.category_id', '=', $att_data['category_id'])
            ->and_where('plugin_courses_schedules.start_date', '>=', $att_data['date_start'])
            ->and_where('plugin_courses_schedules.end_date','<=',$att_data['date_end'])
            ->and_where('plugin_courses_schedules.location_id','=',$att_data['location_id'])
            ->and_where('plugin_courses_schedules_events.datetime_start','IS NOT',NULL)
            ->and_where('plugin_courses_schedules_events.datetime_start','<>','')
            ->and_where('plugin_courses_schedules_events.datetime_start', '>=', $att_data['date_start'])
            ->and_where('plugin_courses_schedules_events.datetime_end', '<=', $att_data['date_end'])
            ->and_where('plugin_courses_schedules_events.delete','=','0')
            ->and_where('plugin_courses_schedules_events.publish','=','1')
            ->order_by('date');

            if(!empty($years)){
                $timetable_results->and_where('has_years.year_id', 'IN', $years);
            }

            $timetable_results->execute()->as_array();
            return $timetable_results;
            /*$return = DB::select(
                array('courses.id', 'course_id'),
                array('courses.title', 'title'),
                array(DB::expr('DATE(schedules.start_date)'), 'date'),
                array('courses.year_id', 'year_id')
            )
                ->distinct(TRUE)
                ->from(array('plugin_courses_courses', 'courses'))
                ->join(array('plugin_courses_schedules', 'schedules'))
                ->on('schedules.course_id', '=', 'courses.id')
                ->where('courses.category_id', '=', $att_data['category_id'])
                ->and_where('schedules.start_date', '>=', $att_data['date_start'])
                ->and_where('schedules.end_date','<=',$att_data['date_end'])
                ->and_where('courses.year_id', 'IN', $years)
                ->and_where('schedules.location_id','=',$att_data['location_id'])
                ->order_by('date')
                ->execute()
                ->as_array();

            return $return;*/
        }
        catch(Exception $e){
            //no point in catching an exception if nothing is going to be done with it.
            throw new Exception($e->getMessage());
            $error = '';
        }
    }

    public static function set_publish_autotimetable($id, $state)
    {
        if ($state == '1') {
            $published = 0;
        } else {
            $published = 1;
        }
        $logged_in_user = Auth::instance()->get_user();
        $query = DB::update(self::MAIN_TABLE)
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

    public static function remove_autotimetable($id)
    {
        $logged_in_user = Auth::instance()->get_user();

        $ret = DB::update(self::MAIN_TABLE)
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'deleted' => 1
            ))
            ->where('id', '=', $id)
            ->execute();

        if ($ret > 0)
        {
            $response['message'] = 'success';
            $response['redirect'] = '/admin/courses/autotimetables';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }

        return $response;
    }

    public static function render_autotimetable($id)
    {
        $data = Model_Autotimetables::get_autotimetable($id);
        $preview_data = Model_Autotimetables::get_course_data($id);

        /* BUILD TIMETABLE */
        // Get all years
        if (isset($data['years']))
        {
            sort($data['years']);
        }

        // Get all unique dates
        $previous_date = ' ';
        $dates = array();
        foreach ($preview_data as $course)
        {
            $date = date('D jS M', strtotime($course['date']));
            if (isset($course['date']) AND $date != $previous_date)
            {
                $previous_date = $course['date'];
                array_push($dates, $course['date']);
            }
        }
        $dates = array_unique($dates);

        // Get all schedules per-date, per-year
        $schedules = array();
        foreach ($dates as $date)
        {
            foreach($data['years'] as $year)
            {
                $schedules[$date][$year['id']] = array();
                foreach($preview_data as $event)
                {
                    if ($event['year_id'] == $year['id'] AND $event['date'] == $date)
                    {
                        array_push($schedules[$date][$year['id']], $event);
                    }
                }
            }
        }

        // Open table, thead, tr
        $return = '<table class="table table-striped autotimetable_list">
                            <caption>'.$data['name'].'</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>';

        // Loop through each year, printing it in a th
        foreach ($data['years'] as $year)
        {
            $return .= '<th scope="col">'.$year['year'].'</th>';
        }

        // Close tr, thead, open tbody
        $return .= '</tr></thead><tbody>';

        // Loop through each date
        foreach ($dates as $date)
        {
            // Get the year with the most schedules
            $max = 0;
            foreach ($schedules[$date] as $years)
            {
                if (count($years) > $max)
                {
                    $max = count($years);
                }
            }

            // Loop form i = 0 to the number of schedules in that year
            for ($i = 0; $i < $max; $i++)
            {
                if ($i == 0)
                {
                    $return .= '<tr class="new_date"><td>'.date('D jS M', strtotime($date)).'</td>';
                }
                else
                {
                    $return .= '<tr><td></td>';
                }

                foreach ($data['years'] as $year)
                {
                    $return .= '<td><a href="/course-detail/'.@$schedules[$date][$year['id']][$i]['title'].'.html/?id='.@$schedules[$date][$year['id']][$i]['course_id'].'&schedule_id='.@$schedules[$date][$year['id']][$i]['schedule_id'].'">'.@$schedules[$date][$year['id']][$i]['title'].'</a></td>';
                }
                $return .= '</tr>';
            }
        }
        $return .= '<tbody>';

        if (isset($data['years']) AND isset($data['description']))
        {
            $return .= '<tfoot>
                        <tr>
                            <td colspan="'.(count($data['years']) + 1).'">'.$data['description'].'</td>
                        </tr>
                    </tfoot>';
        }

        $return .= '</table>';

        return $return;
    }
}
