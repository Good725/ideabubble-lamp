<?php defined('SYSPATH') or die('No direct script access.');

class Model_Subjects extends Model
{
    const TABLE_SUBJECTS = 'plugin_courses_subjects';

    public static function get_all_subjects($args = array())
    {
        $result = DB::select('subject.*')
			->from(array('plugin_courses_subjects', 'subject'))
			->distinct(TRUE)
			->where('subject.deleted', '=', 0);

        if (!empty($args['publish']))
        {
            $result->where('subject.publish','=',1);
        }

		if (!empty($args['must_have_categories']) || !empty($args['category_ids']) || !empty($args['year_ids']) || !empty($args['location_ids']))
		{
			$result
				->join(array('plugin_courses_courses',    'course'  ))->on('course.subject_id',  '=', 'subject.id')
                ->join(array(Model_courses::TABLE_HAS_PROVIDERS, 'has_providers'), 'left')->on('has_providers.course_id', '=', 'course.id')
				->join(array('plugin_courses_categories', 'category'))->on('course.category_id', '=', 'category.id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('course.id', '=', 'schedules.course_id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'building'), 'left')->on('locations.parent_id', '=', 'building.id')
				->where('category.publish', '=', '1')
				->where('category.delete',  '=', '0')
                ->and_where('schedules.end_date', '>=', date('Y-m-d H:i:s'))
			;
		}

        if (!empty($args['category_ids']))
        {
            $result->where('category.id', 'in', $args['category_ids']);
        }

        if (!empty($args['year_ids']))
        {
            $result
                ->join(array(Model_Courses::TABLE_HAS_YEARS, 'has_years'), 'inner')
                    ->on('course.id', '=', 'has_years.course_id')
                ->join(array(Model_Years::YEARS_TABLE, 'years'), 'inner')
                    ->on('has_years.year_id', '=', 'years.id')
                ->and_where_open()
                    ->or_where('has_years.year_id', 'in', $args['year_ids'])
                    ->or_where('years.year', '=', 'All Levels')
                ->and_where_close();
        }

        if (!empty($args['location_ids'])) {
            $result->and_where_open();
            $result->or_where('schedules.location_id', 'in', $args['location_ids']);
            $result->or_where('building.id', 'in', $args['location_ids']);
            $result->and_where_close();
        }

        if (@$args['is_fulltime'] !== null && @$args['is_fulltime'] !== '') {
            $result->and_where('course.is_fulltime', '=', $args['is_fulltime']);
        }

        if (!empty($args['provider_ids'])) {
            $result->where('has_providers.provider_id', 'in', $args['provider_ids']);
        }

        $result = $result->order_by('subject.name', 'asc')->execute()->as_array() ;
        return $result;
    }

    public static function get_subject($id)
    {
        $data = DB::select()->from('plugin_courses_subjects')->where('id', '=', $id)->and_where('deleted', '=', 0)
            ->execute()->as_array();

        if (isset($data[0])) {
            if ($data[0]['cycle'] != '') {
                $data[0]['cycle'] = explode(',', $data[0]['cycle']);
                $data[0]['cycle'] = array_combine(array_values($data[0]['cycle']), $data[0]['cycle']);
            } else {
                $data[0]['cycle'] = array();
            }
            return $data[0];
        }
        else {
            // If no records are found, return an array of empty values for each column
            $return = array();
            $columns = Database::instance()->list_columns('plugin_courses_subjects');
            foreach ($columns as $column => $data) {
                $return[$column] = '';

            }
            return $return;
        }
    }

    public static function get_datatable($limit, $offset, $sort, $dir, $search = FALSE)
    {

        $query = DB::select()->from('plugin_courses_subjects')
            ->where('deleted', '=', 0)
            ->and_where_open()
                ->or_where('name', 'like', '%'.$search.'%')
                ->or_where('cycle', 'like', '%'.$search.'%')
            ->and_where_close()
            ->order_by($sort, $dir);

		if ($limit > -1)
		{
			$query->limit($limit)->offset($offset);
		}

		$query = $query->execute()->as_array();

        $return = array();
        if (count($query) > 0)
        {
            $i = 0;
            foreach ($query as $elem => $sub)
            {
                $return[$i]['id']            = '<a href="/admin/courses/edit_subject/'.$sub['id'].'">'.$sub['id']           .'</a>';
                $return[$i]['color']         = '<a href="/admin/courses/edit_subject/'.$sub['id'].'"><span class="color_label" style="background-color:'.$sub['color'].';">'.$sub['color'].'</span></a>';
                $return[$i]['cycle']         = '<a href="/admin/courses/edit_subject/'.$sub['id'].'">'.($sub['cycle'] == 'Junior,Senior' ? 'Both' : $sub['cycle']) .'</a>';
                $return[$i]['name']          = '<a href="/admin/courses/edit_subject/'.$sub['id'].'">'.$sub['name']         .'</a>';
                $return[$i]['date_created']  = '<a href="/admin/courses/edit_subject/'.$sub['id'].'">'.$sub['date_created'] .'</a>';
                $return[$i]['date_modified'] = '<a href="/admin/courses/edit_subject/'.$sub['id'].'">'.$sub['date_modified'].'</a>';
                $return[$i]['edit']          = '<a href="/admin/courses/edit_subject/'.$sub['id'].'"><i class="icon-pencil"></i></a>';
                $return[$i]['publish']       = '<a href="#" class="publish" data-publish="'.$sub['publish'].'" data-id="'.$sub['id'].'"><i class="icon-'.(($sub['publish'] == 1) ? 'ok' : 'ban-circle').'"></i></a>';
                $return[$i]['delete']        = '<a href="#" class="delete" data-id="'.$sub['id'].'"><i class="icon-remove-circle"></i></a>';

                $i++;
            }
        }
        return $return;
    }

    public static function count_subjects($search = '')
    {
        return DB::select(DB::expr('COUNT(*)'))->from('plugin_courses_subjects')
            ->where('deleted', '=', 0)->and_where('name', 'like', '%'.$search.'%')
            ->execute()->get('COUNT(*)', 0);
    }

    public static function save($data)
    {
        $logged_in_user        = Auth::instance()->get_user();
        $data['modified_by']   = $logged_in_user['id'];
        $data['date_modified'] = date('Y-m-d H:i:s');
        unset($data['redirect']);

        if (isset($data['cycle']) && is_array($data['cycle'])) {
            $data['cycle'] = implode(',', $data['cycle']);
        }

        if ((int)$data['id'] > 0)
        {
            $id          = (int)$data['id'];
            unset($data['id']);
            $query       = DB::update('plugin_courses_subjects')->set($data)->where('id', '=', $id)->execute();
            $save_action = 'update';
            $item_id     = $id;
        }
        else
        {
            $data['created_by']   = $data['modified_by'];
            $data['date_created'] = $data['date_modified'];
            $data['deleted']      = 0;
            $query                = DB::insert('plugin_courses_subjects', array_keys($data))->values($data)->execute();
            $save_action          = 'add';
            $item_id              = (isset($query[0]) AND $query[0] > 0)? $query[0] : 0;
        }

        if(($save_action == 'add' AND $query[0] > 0) OR ($save_action == 'update' AND $query == 1))
        {
            $message = 'Subject ID #'.$item_id.':  "'.$data['name'].'" has been '.(($save_action == 'add')? 'created' : 'updated' ).'.';
            $result  = 'success';
            IbHelpers::set_message ($message, 'success popup_box');
        }
        else
        {
            $message = 'Error '.(($save_action == 'add') ? 'creating' : 'updating').' subject'.(($item_id > 0)? ' ID #'.$item_id. ' ' : '').': '.$data['name'];
            $result  = 'error popup_box';
        }

        // IbHelpers::set_message($message, $result);
        return $item_id;
    }

    public static function validate_subject($data)
    {
        $errors = array();
        if (isset($data['name']) AND strlen($data['name']) < 3)
        {
            $errors[] = 'Name must contain at least three characters.';
        }
        return $errors;
    }

    public static function set_publish($id, $state)
    {
        $published            =  ($state == '1') ? 0 : 1;
        $logged_in_user      = Auth::instance()->get_user();
        $updates             = array('publish' => $published,'modified_by' => $logged_in_user['id'],'date_modified' => date('Y-m-d H:i:s'));
        $query               = DB::update('plugin_courses_subjects')->set($updates)->where('id', '=', $id)->execute();
        $response['message'] = ($query > 0) ? 'success' : 'error';
        ($query == 0) ? $response['error_msg'] = 'An error occurred. Please contact support.' : NULL;

        return $response;
    }

    public static function delete($id)
    {
        $logged_in_user      = Auth::instance()->get_user();
        $updates             = array('deleted' => 1,'modified_by' => $logged_in_user['id'],'date_modified' => date('Y-m-d H:i:s'));
        $query               = DB::update('plugin_courses_subjects')->set($updates)->where('id', '=', $id)->execute();
        $response['message'] = ($query > 0) ? 'success' : 'error';
        $response['redirect'] = '/admin/courses/subjects';
        ($query == 0) ? $response['error_msg'] = 'An error occurred. Please contact support.' : NULL;

        return $response;
    }
}
