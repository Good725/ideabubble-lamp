<?php defined('SYSPATH') or die('No direct script access.');

class Model_Courses extends Model
{
    const TABLE_HAS_PROVIDERS = 'plugin_courses_courses_has_providers';
    const TABLE_COURSES = 'plugin_courses_courses';
    const TABLE_SUBJECTS = 'plugin_courses_subjects';
	const TABLE_HAS_YEARS = 'plugin_courses_courses_has_years';
    const TABLE_HAS_PAYMENTOPTIONS = 'plugin_courses_courses_has_paymentoptions';

	const MEDIA_IMAGES_FOLDER = 'courses';

	public static function get_all($filter = array())
	{
		$query = DB::query(
			Database::SELECT, 'SELECT
                    plugin_courses_courses.*,
                    plugin_courses_categories.category,
                    plugin_courses_categories.start_time,
                    plugin_courses_categories.end_time,
                    plugin_courses_categories.grinds_tutorial as payg,
                    GROUP_CONCAT(DISTINCT plugin_courses_years.year) as `year`,
                    plugin_courses_types.type,
                    GROUP_CONCAT(plugin_courses_providers.name) as provider,
                    plugin_courses_subjects.name as subject
            FROM plugin_courses_courses
            LEFT JOIN
                plugin_courses_categories
            ON
                plugin_courses_categories.id = plugin_courses_courses.category_id
                AND
                plugin_courses_categories.delete = 0
			LEFT JOIN
				plugin_courses_courses_has_years ON plugin_courses_courses.id = plugin_courses_courses_has_years.course_id
            LEFT JOIN
                plugin_courses_years
            ON
                plugin_courses_years.id = plugin_courses_courses_has_years.year_id
                AND
                plugin_courses_years.delete = 0
            LEFT JOIN
                plugin_courses_types
            ON
                plugin_courses_types.id = plugin_courses_courses.type_id
                AND
                plugin_courses_types.delete = 0
            LEFT JOIN
              plugin_courses_courses_has_providers
              ON
               plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
            LEFT JOIN
                plugin_courses_providers
            ON
                plugin_courses_providers.id = plugin_courses_courses_has_providers.provider_id
                AND
                plugin_courses_providers.delete = 0
            LEFT JOIN
                plugin_courses_subjects
            ON
                plugin_courses_subjects.id = plugin_courses_courses.subject_id
                AND
                plugin_courses_subjects.deleted = 0
            WHERE
                plugin_courses_courses.deleted = 0
                ' . (in_array(@$filter['is_fulltime'], array('YES', 'NO')) ? " AND plugin_courses_courses.is_fulltime='" . $filter['is_fulltime'] . "' " : '') . '
			GROUP BY plugin_courses_courses.id
            ORDER BY
                title
            '
		)->execute()->as_array();

		return $query;
	}

	public static function get_all_published($filter = array())
	{
		$query = DB::query(
			Database::SELECT, 'SELECT plugin_courses_courses.*,
                    plugin_courses_categories.category,
                    GROUP_CONCAT(DISTINCT plugin_courses_years.year) as `year`,
                    plugin_courses_types.type,
                    GROUP_CONCAT(plugin_courses_providers.name) as provider,
                    plugin_courses_categories.grinds_tutorial as payg,
                    plugin_courses_categories.start_time,
	                plugin_courses_categories.end_time
             FROM plugin_courses_courses
             LEFT JOIN
              plugin_courses_categories
              ON
                plugin_courses_categories.id = plugin_courses_courses.category_id
                AND plugin_courses_categories.delete = 0
                AND plugin_courses_categories.publish = 1
			LEFT JOIN plugin_courses_courses_has_years ON plugin_courses_courses.id = plugin_courses_courses_has_years.course_id
             LEFT JOIN
              plugin_courses_years
              ON
                plugin_courses_years.id = plugin_courses_courses_has_years.year_id
                AND plugin_courses_years.delete = 0
                AND plugin_courses_years.publish = 1
             LEFT JOIN
              plugin_courses_types
              ON
                plugin_courses_types.id = plugin_courses_courses.type_id
                AND plugin_courses_types.delete = 0
                AND plugin_courses_types.publish = 1
             LEFT JOIN
              plugin_courses_courses_has_providers
              ON
               plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
             LEFT JOIN
              plugin_courses_providers
              ON
                plugin_courses_providers.id = plugin_courses_courses_has_providers.provider_id
                AND plugin_courses_providers.delete = 0
             WHERE
                plugin_courses_courses.deleted = 0 AND plugin_courses_courses.publish = 1
            ' . (@is_numeric($filter['provider_id']) ? ' AND plugin_courses_courses_has_providers.provider_id=' . $filter['provider_id'] : '') . '
			 GROUP BY plugin_courses_courses.id
              ORDER BY
                title
            '
		)->execute()->as_array();

		return $query;
	}

	public static function get_contact_available_courses($categories, $subjects)
	{
		$result = array();
		if ((!is_null($categories)) AND (!is_null($subjects)))
		{
			$result = self::get_courses_based_on_selected_category_subject($categories, $subjects);
		}
		return $result;
	}

	public static function get_courses_based_on_selected_category_subject($categories, $subjects)
	{
		$categories = is_array($categories) ? $categories : (array) $categories;
		$subjects = is_array($subjects) ? $subjects : (array) $subjects;
		$result = DB::select('c.id', 'c.title', 'c1.category', array('c2.name', 'subject'))
			->from(array('plugin_courses_courses', 'c'))
			->join(array('plugin_courses_categories', 'c1'), 'INNER')
			->on('c1.id', '=', 'c.category_id')
			->join(array('plugin_courses_subjects', 'c2'), 'INNER')
			->on('c2.id', '=', 'c.subject_id')
			->where('c1.id', 'IN', $categories)
			->where('c2.id', 'IN', $subjects)
			->order_by('category')
			->order_by('subject')
			->order_by('title')
			->execute()
			->as_array();
		return $result;
	}

	public static function get_front_all()
	{
		$query = DB::query(
			Database::SELECT, 'SELECT `plugin_courses_schedules`.`id`,
                    `plugin_courses_schedules`.`start_date` as `date`,
                    `plugin_courses_schedules`.`fee_amount` as `amount`,
                    `plugin_courses_courses`.`title` as `title`,
                    `plugin_courses_courses`.`summary`,
                    `plugin_courses_courses`.`description`,
                    `plugin_courses_categories`.`category`,
                    `plugin_courses_levels`.`level`
             FROM `plugin_courses_schedules`
             LEFT JOIN
              plugin_courses_courses
              ON
                plugin_courses_courses.id = plugin_courses_schedules.course_id
                AND plugin_courses_courses.deleted = 1 AND plugin_courses_courses.publish = 1
             LEFT JOIN
              plugin_courses_categories
              ON
                plugin_courses_categories.id = plugin_courses_courses.category_id
                AND plugin_courses_categories.delete = 1 AND plugin_courses_categories.publish = 1
             LEFT JOIN
              plugin_courses_levels
              ON
                plugin_courses_levels.id = plugin_courses_courses.level_id
                AND plugin_courses_levels.delete = 1 AND plugin_courses_levels.publish = 1
             WHERE
                plugin_courses_schedules.delete = 0
              AND
                plugin_courses_schedules.publish = 1
             ORDER BY
                id
            '
		)->execute()->as_array();

		return $query;
	}

	static function get_courses_json($term)
	{
		$query = DB::select()
			->from('plugin_courses_courses')
			->where('title', 'LIKE', '%'.$term.'%')
			->where('deleted', '=', 0);
		$count = clone $query;

		$return['results'] = $query->select('id', 'title')->order_by('title')->limit(5)->execute()->as_array();
		$return['count'] = $count->select(array(DB::expr('count(*)'), 'count'))->execute()->get('count', 0);

		return json_encode($return);
	}


	public static function get_mixed_with_news($limit_total = 5)
	{
		$courses = self::get_for_feed($limit_total);
		$news = Model_News::get_feed_for_courses_plugin_frontend("News", $limit_total);
		var_dump($news);
		exit;
	}

	public static function get_for_feed($limit = 5, $offset = 0)
	{
        $limit = (int)$limit;
        $offset = (int)$offset;
		$query = DB::query(
			Database::SELECT, 'SELECT `plugin_courses_schedules`.`id`,
                    `plugin_courses_schedules`.`name` as `schedule_title`,
                    `plugin_courses_schedules`.`start_date` as `date`,
                    `plugin_courses_schedules`.`fee_amount` as `amount`,
                    `plugin_courses_courses`.`title` as `title`,
                    `plugin_courses_courses`.`summary`,
                    `plugin_courses_courses`.`description`,
                    `plugin_courses_categories`.`category`,
                    `plugin_courses_courses`.`summary`

             FROM `plugin_courses_schedules`
             LEFT JOIN
              plugin_courses_courses
              ON
                plugin_courses_courses.id = plugin_courses_schedules.course_id
                AND plugin_courses_courses.deleted = 0
                AND plugin_courses_courses.publish = 1
             LEFT JOIN
              plugin_courses_categories
              ON
                plugin_courses_categories.id = plugin_courses_courses.category_id
                AND plugin_courses_categories.delete = 0
                AND plugin_courses_categories.publish = 1
             LEFT JOIN
              plugin_courses_levels
              ON
                plugin_courses_levels.id = plugin_courses_courses.level_id
                AND plugin_courses_levels.delete = 0
                AND plugin_courses_levels.publish = 1
             WHERE
                plugin_courses_schedules.delete = 0
              AND
                plugin_courses_schedules.publish = 1
             ORDER BY
                id
             LIMIT
                '.$limit.'
            '
		)->execute()->as_array();

		return $query;
	}

	public static function count_courses($search = FALSE)
	{
		$_search = '';
		if ($search)
		{
			$_search = " AND
              (
                plugin_courses_courses.title like '%".$search."%'
              OR
                plugin_courses_courses.code like '%".$search."%'
              OR
                plugin_courses_categories.category like '%".$search."%'
              OR
                plugin_courses_years.year like '%".$search."%'
              OR
                plugin_courses_types.type like '%".$search."%'
              OR
                plugin_courses_providers.name like '%".$search."%'
              )";
		}
		$query = DB::query(
			Database::SELECT, "SELECT count(*) as count
             FROM plugin_courses_courses
             LEFT JOIN
              plugin_courses_categories
              ON
                plugin_courses_categories.id = plugin_courses_courses.category_id
                AND plugin_courses_categories.delete = 0
			LEFT JOIN plugin_courses_courses_has_years ON plugin_courses_courses.id = plugin_courses_courses_has_years.course_id
             LEFT JOIN
              plugin_courses_years
              ON
                plugin_courses_years.id = plugin_courses_courses_has_years.year_id
                AND plugin_courses_years.delete = 0
             LEFT JOIN
              plugin_courses_types
              ON
                plugin_courses_types.id = plugin_courses_courses.type_id
                AND plugin_courses_types.delete = 0
             LEFT JOIN
              plugin_courses_courses_has_providers
              ON
               plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
             LEFT JOIN
              plugin_courses_providers
              ON
                plugin_courses_providers.id = plugin_courses_courses_has_providers.provider_id
                AND plugin_courses_providers.delete = 0
             WHERE
                plugin_courses_courses.deleted = 0
              ".$_search."
              GROUP BY plugin_courses_courses.id"
		)->execute()->as_array();

		return @$query['0']['count'] ?: 0;
	}

	public static function get_courses($limit, $offset, $sort, $dir, $search = FALSE, $column_filters = array())
	{
        $columns   = array();
        $columns[] = '`plugin_courses_courses`.`title`';
        $columns[] = '`plugin_courses_courses`.`code`';
        $columns[] = '`plugin_courses_years`.`year`';
        $columns[] = '`plugin_courses_levels`.`level`';
        $columns[] = '`plugin_courses_categories`.`category`';
        $columns[] = '`plugin_courses_subjects`.`name`';
        $columns[] = '`plugin_courses_types`.`type`';
        $columns[] = '`plugin_courses_providers`.`name`';
        $columns[] = '`plugin_courses_topics`.`name`';
        $sanitized_input = array();

        $_search = '';
        if ($search) {
            $_search .= " AND (";
            for ($i = 0; $i < count($columns); $i++) {
                $_search .= ($i != 0) ? " OR " : "";
                $_search .= "REPLACE(REPLACE({$columns[$i]}, '-', ''), ' ', '') LIKE :search_{$i}";
                $sanitized_input[":search_{$i}"] = "%".str_replace(' ', '', str_replace('-', '', $search))."%";
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

		$_limit = ($limit > -1) ? ' LIMIT '.$offset.','.$limit : '';
		$query = DB::query(
			Database::SELECT, 'SELECT SQL_CALC_FOUND_ROWS plugin_courses_courses.*,
                    plugin_courses_categories.category,
                    GROUP_CONCAT(DISTINCT plugin_courses_years.year) as `year`,
                    plugin_courses_types.type,
                    plugin_courses_levels.level,
                    GROUP_CONCAT(plugin_courses_providers.name) AS provider,
                    plugin_courses_subjects.name AS subject,
                    CASE
                        WHEN plugin_courses_topics.name IS NOT NULL
                        THEN GROUP_CONCAT(plugin_courses_topics.name SEPARATOR ", ")
                        ELSE ""
                    END AS topics

             FROM plugin_courses_courses
             LEFT JOIN
              plugin_courses_categories
              ON
                plugin_courses_categories.id = plugin_courses_courses.category_id
                AND plugin_courses_categories.delete = 0
			 LEFT JOIN plugin_courses_courses_has_years ON plugin_courses_courses.id = plugin_courses_courses_has_years.course_id
             LEFT JOIN
              plugin_courses_years
              ON
                plugin_courses_years.id = plugin_courses_courses_has_years.year_id
                AND plugin_courses_years.delete = 0
             LEFT JOIN
              plugin_courses_types
              ON
                plugin_courses_types.id = plugin_courses_courses.type_id
                AND plugin_courses_types.delete = 0
             LEFT JOIN
              plugin_courses_levels
              ON
                plugin_courses_levels.id = plugin_courses_courses.level_id
                AND plugin_courses_levels.delete = 0
             LEFT JOIN
              plugin_courses_courses_has_providers
              ON plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
             LEFT JOIN
              plugin_courses_providers
              ON
                plugin_courses_providers.id = plugin_courses_courses_has_providers.provider_id
                AND plugin_courses_providers.delete = 0
             LEFT JOIN
                plugin_courses_subjects
              ON
                plugin_courses_subjects.id = plugin_courses_courses.subject_id
                AND
                plugin_courses_subjects.deleted = 0

             LEFT JOIN
                plugin_courses_courses_has_topics
              ON
                plugin_courses_courses_has_topics.course_id = plugin_courses_courses.id
                AND
                plugin_courses_courses_has_topics.deleted = 0

             LEFT JOIN
                plugin_courses_topics
              ON
                plugin_courses_courses_has_topics.topic_id = plugin_courses_topics.id
                AND
                plugin_courses_topics.deleted = 0

             WHERE
                plugin_courses_courses.deleted = 0
              '.$_search.$column_search.'
             GROUP BY plugin_courses_courses.id
             ORDER BY
                '.$sort.' '.$dir.'
             '.$_limit
		);

        foreach ($sanitized_input as $key => $value) {
            $query->param($key, $value);
        }
		$query = $query->execute()->as_array();
		$return = array();
		if (count($query) > 0)
		{
			$i = 0;
            foreach ($query as $elem => $sub)
            {
                $return[$i]['title'] = '<a href="/admin/courses/edit_course/?id='.$sub['id'].' "class="edit-link">'.$sub['title'].'</a>';
                $return[$i]['code'] = $sub['code'];
                $return[$i]['year'] = $sub['year'];
                $return[$i]['level'] = $sub['level'];
                $return[$i]['category'] = $sub['category'];
                $return[$i]['subject'] = $sub['subject'];
                $return[$i]['type'] = $sub['type'];
                $return[$i]['provider'] = $sub['provider'];
                $return[$i]['topics'] = $sub['topics'];
                $return[$i]['edit'] = '<a href="/admin/courses/edit_course/?id=' . $sub['id'] . '"><i class="icon-pencil"></i></a>';
                if ($sub['publish'] == '1') {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="' . $sub['id'] . '"><i class="icon-ok"></i></a>';
                } else {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="' . $sub['id'] . '"><i class="icon-ban-circle"></i></a>';
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
                                             <a href="/admin/courses/duplicate_course/?id=' . $sub['id'] . '">Duplicate</a>
                                         </button>
                                     </li>
                                     <li>
                                         <button class="btn-link">
                                             <a href="#" class="delete" data-id="' . $sub['id'] . '">Delete</a>
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

	public static function get_course_name_by_schedule_id($id)
	{
		$query = DB::query(Database::SELECT, 'SELECT title
        FROM plugin_courses_courses
        LEFT JOIN plugin_courses_schedules
        ON plugin_courses_schedules.course_id = plugin_courses_courses.id AND plugin_courses_courses.deleted = 0
        WHERE plugin_courses_schedules.id = :id AND plugin_courses_schedules.delete = 0
        LIMIT 1')->parameters(array(':id' => $id))->execute()->as_array();

		return (isset($query[0]['title'])) ? $query[0]['title'] : "Invalid Course ID";
	}

	public static function get_course_name($id)
	{
		if (empty($id))
		{
			$name = array("title" => "invalid course id");
		}
		else
		{
			$name = DB::query(Database::SELECT, 'SELECT title FROM plugin_courses_courses WHERE id = :id AND deleted = 0 ORDER BY `title` ASC')->parameters(array(':id' => $id))->execute()->as_array();
		}

		return (isset($name[0]['title']) AND trim($name[0]['title']) != '') ? $name[0]['title'] : 'invalid course id';
	}

	public static function set_publish_course($id, $state)
	{
        $activity = new Model_Activity;
        $activity->set_item_type('course');
		if ($state == '1')
		{
			$published = 0;
            $activity->set_action('unpublish');
		}
		else
		{
			$published = 1;
            $activity->set_action('publish');
		}
		$logged_in_user = Auth::instance()->get_user();
		$query = DB::update("plugin_courses_courses")->set(
			array(
				'publish' => $published, 'modified_by' => $logged_in_user['id'], 'date_modified' => date('Y-m-d H:i:s')
			)
		)->where('id', '=', $id)->execute();
		$response = array();
		if ($query > 0)
		{
			$response['message'] = 'success';
            $activity->set_item_id($id)->save();
		}
		else
		{
			$response['message'] = 'error';
			$response['error_msg'] = 'An error occurred! Please contact with support!';
		}

		return $response;
	}


	public static function remove_course($id)
	{
        $bookings = Model_KES_Bookings::get_bookings([['column' => 't3.id', 'op' => '=', 'value' => $id]]);
        if (count($bookings) > 0 && false) {
            $response['message'] = 'error';
            $response['error_msg'] = 'You cannot delete a course that has bookings';
        } else {
            $logged_in_user = Auth::instance()->get_user();
            DB::update('plugin_courses_courses_has_topics')
                ->set(array('plugin_courses_courses_has_topics.deleted' => 1))
                ->where('plugin_courses_courses_has_topics.course_id', '=', $id)
                ->execute();

            DB::update("plugin_courses_schedules")->set(
                array(
                    'modified_by' => $logged_in_user['id'], 'date_modified' => date('Y-m-d H:i:s'), 'delete' => 1
                )
            )->where('course_id', '=', $id)->execute();

            $ret = DB::update('plugin_courses_courses')->set(
                array(
                    'modified_by' => $logged_in_user['id'], 'date_modified' => date('Y-m-d H:i:s'), 'deleted' => 1
                )
            )->where('id', '=', $id)->execute();
            if ($ret > 0)
            {
                $response['message'] = 'success';
                $activity = new Model_Activity();
                $activity
                    ->set_item_type('course')
                    ->set_action('delete')
                    ->set_item_id($id)
                    ->save();
            }
            else
            {
                $response['message'] = 'error';
                $response['error_msg'] = 'An error occurred! Please contact with support!';
            }
        }


		return $response;
	}

	public static function duplicate_course($id)
	{
		$data = self::get_course($id);
		$data['title'] = "Copy ".$data['title'];
		$data['id'] = 0;

		return self::save_course($data);
	}

	public static function get_course($id)
	{
		$data = DB::select('course.*', 'category.category')
            ->from(array('plugin_courses_courses', 'course'))
            ->join(array('plugin_courses_categories', 'category'), 'left')->on('course.category_id', '=', 'category.id')
            ->where('course.id', '=', $id)
            ->where('course.deleted', '=', 0)
            ->execute()
            ->current();
		if ($data) {
			$data['year_ids'] = array();
			$year_ids = DB::select('*')
					->from(self::TABLE_HAS_YEARS)
					->where('course_id', '=', $id)
					->execute()
					->as_array();
			foreach ($year_ids as $year_id) {
				$data['year_ids'][] = $year_id['year_id'];
			}
			$data['has_providers'] = array();
            $data['accredited_by'] = array();
			$accrediation_bodies = Model_Providers::get_accreditation_bodies();
			$accrediation_bodies_ids = array();
			foreach($accrediation_bodies as $accrediation_body) {
                $accrediation_bodies_ids[] = $accrediation_body['id'];
            }
            if (!empty($accrediation_bodies_ids)) {
                $provider_ids = DB::select('*')
                    ->from(self::TABLE_HAS_PROVIDERS)
                    ->where('course_id', '=', $id)
                    ->where('provider_id' , 'NOT IN' , $accrediation_bodies_ids)
                    ->execute()
                    ->as_array();
                foreach ($provider_ids as $provider_id) {
                    $data['has_providers'][] = $provider_id['provider_id'];
                }
                $accredited_by_ids = DB::select('*')
                    ->from(self::TABLE_HAS_PROVIDERS)
                    ->where('course_id', '=', $id)
                    ->where('provider_id' , 'IN' , $accrediation_bodies_ids)
                    ->execute()
                    ->as_array();
                foreach ($accredited_by_ids as $accredited_by_id) {
                    $data['accredited_by'][] = $accredited_by_id['provider_id'];
                }
            } else {
                $provider_ids = DB::select('*')
                    ->from(self::TABLE_HAS_PROVIDERS)
                    ->where('course_id', '=', $id)
                    ->execute()
                    ->as_array();
                foreach ($provider_ids as $provider_id) {
                    $data['has_providers'][] = $provider_id['provider_id'];
                }
                $data['accredited_by'] = array();
            }
			$data['topics'] = DB::select('topic.*')
					->from(array('plugin_courses_topics', 'topic'))
					->join(array('plugin_courses_courses_has_topics', 'has_topic'))
					->on('has_topic.topic_id', '=', 'topic.id')
					->where('has_topic.course_id', '=', $id)
					->where('topic.deleted',       '=', 0)
					->where('has_topic.deleted',   '=', 0)
					->execute()
					->as_array();

            $data['paymentoptions'] = DB::select('*')
                ->from(self::TABLE_HAS_PAYMENTOPTIONS)
                ->where('course_id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->order_by('months', 'asc')
                ->execute()
                ->as_array();
			foreach ($data['paymentoptions'] as $i => $paymentoption) {
				if ($paymentoption['interest_type'] == 'Custom') {
					$data['paymentoptions'][$i]['custom_payments'] = @json_decode($data['paymentoptions'][$i]['custom_payments'], true) ?: array();
				}
			}
		}
		return $data;
	}

	public static function save_course($data)
	{
        $user = Auth::instance()->get_user();
        $activity = new Model_Activity();
        $activity->set_user_id($user['id'])->set_item_type('course');
        $has_providers = @$data['has_providers'] ?: array();
        $accredited_by = @$data['accredited_by'] ?: array();
        unset ($data['has_providers']);
        unset ($data['accredited_by']);
		// add / update
		$save_action = 'add';
		$item_id = 0;
		unset($data['redirect']);
		//Add the necessary values to the $data array for update
		$logged_in_user = Auth::instance()->get_user();
        $year_ids = isset($data['year_id']) ? (array) $data['year_id'] : array();
		unset ($data['year_id']);
		if (isset($year_ids[0])) {
			$data['year_id'] = $year_ids[0];
		}
        
        $paymentoptions = @$data['paymentoption'];
		unset($data['paymentoption']);
        unset($data['paymentoptions']);
		unset($data['schedule_table_length']);
        unset($data['images_table_length']);
        // If form has a Course ID, it will update the Course
        if ((int) $data['id'] > 0)
		{
			$id = (int) $data['id'];
			unset($data['id']);
			foreach ($data as $k => $val)
			{
				if ($val == '')
				{
					$data[$k] = NULL;
				}
			}

            $data['modified_by'] = $logged_in_user['id'];
			$data['date_modified'] = date('Y-m-d H:i:s');
			$query = DB::update('plugin_courses_courses')->set($data)->where('id', '=', $id)->execute();

			$save_action = 'update';
			$item_id = $id;
            $activity->set_action('update')->set_item_id($item_id)->save();
		}
        // Else create the Course
		else
		{
			foreach ($data as $k => $val)
			{
				if ($val == '')
				{
					$data[$k] = NULL;
				}
			}
			$data['created_by'] = $logged_in_user['id'];
			$data['date_created'] = date('Y-m-d H:i:s');
			$data['deleted'] = 0;
			unset($data['year_ids']);
			unset($data['topics']);
			unset($data['category']);
			$query = DB::insert('plugin_courses_courses', array_keys($data))->values($data)->execute();
			$save_action = 'add';
			$item_id = (isset($query[0]) AND $query[0] > 0) ? $query[0] : 0;
            $activity->set_action('create')->set_item_id($item_id)->save();
		}

        DB::delete(self::TABLE_HAS_PROVIDERS)->where('course_id', '=', $item_id)->execute();
        foreach ($has_providers as $provider_id) {
            DB::insert(self::TABLE_HAS_PROVIDERS)->values(array('course_id' => $item_id, 'provider_id' => $provider_id))->execute();
        }
        foreach($accredited_by as $accreditation_id) {
            DB::insert(self::TABLE_HAS_PROVIDERS)->values(array('course_id' => $item_id, 'provider_id' => $accreditation_id))->execute();
        }
        
		DB::delete(self::TABLE_HAS_YEARS)->where('course_id', '=', $item_id)->execute();
		foreach ($year_ids as $year_id) {
			DB::insert(self::TABLE_HAS_YEARS)->values(array('course_id' => $item_id, 'year_id' => $year_id))->execute();
		}

        $paymentoption_ids = array();
        if (is_array($paymentoptions))
        foreach ($paymentoptions as $paymentoption) {
			if (is_array(@$paymentoption['custom_payments'])) {
				foreach ($paymentoption['custom_payments'] as $ci => $custom_payment) {
					$paymentoption['custom_payments'][$ci]['total'] = $paymentoption['custom_payments'][$ci]['amount'] + $paymentoption['custom_payments'][$ci]['interest'];
				}
				$paymentoption['custom_payments'] = json_encode($paymentoption['custom_payments'], JSON_PRETTY_PRINT);
			}
            $paymentoption['course_id'] = $item_id;
            if ($paymentoption['id'] > 0) {
                DB::update(self::TABLE_HAS_PAYMENTOPTIONS)
                    ->set($paymentoption)
                    ->where('id', '=', $paymentoption['id'])
                    ->execute();
                $paymentoption_ids[] = $paymentoption['id'];
            } else {
                $inserted = DB::insert(self::TABLE_HAS_PAYMENTOPTIONS)
                    ->values($paymentoption)
                    ->execute();
                $paymentoption_ids[] = $inserted[0];
            }
        }
        $deleteq = DB::update(self::TABLE_HAS_PAYMENTOPTIONS)
            ->set(array('deleted' => 1))
            ->where('course_id', '=', $item_id);
        if (count($paymentoption_ids) > 0) {
            $deleteq->and_where('id', 'not in', $paymentoption_ids);
        }
        $deleteq->execute();


		// Set Successful / Not Successful Insert / Update Message
		if (($save_action == 'add' AND $query[0] > 0) OR
			($save_action == 'update' AND $query == 1)
		)
		{
			IbHelpers::set_message(
				'Course ID #'.$item_id.':  "'.$data['title'].'" has been '.(($save_action == 'add') ? 'CREATED' : 'UPDATED').'.', 'success popup_box'
			);
		}
		else
		{
			IbHelpers::set_message(
				'Sorry! There was a problem with '.(($save_action == 'add') ? 'CREATION' : 'UPDATE').' of '.(($item_id > 0) ? 'Course ID #'.$item_id : 'Course').': "'.$data['title'].'".<br />'.'Please make sure, that form is filled properly and Try Again!', 'error popup_box'
			);
		}

		return $item_id;
	}

	public static function validate_year($data)
	{
		//create empty errors array
		$errors = array();
		//check name must be min 3 chars
		if (@strlen($data['title']) < 3)
		{
			$errors[] = "Course name must contains min 3 characters";
		}

		return $errors;

	}

	public static function get_front_list_for_category($category_name)
	{
		$category = Model_Category::get_by_name($category_name);

		$view = View::factory(
			'front_end/courses_list_for_category', array(
				'category' => $category
			)
		);

		return $view;
	}

	public static function get_front_course_details($category_name, $item_id)
	{
		$view = View::factory(
			'front_end/courses_course_details'
		);

		return $view;

	}

	public static function count_images($id, $search)
	{
		$_search = '';
		if ($search)
		{
			$_search = " AND plugin_courses_courses_images.image like '%".$search."%'";
		}
		$query = DB::query(
			Database::SELECT, "SELECT count(*) as count
             FROM plugin_courses_courses_images
             WHERE
                plugin_courses_courses_images.course_id = ".$id." AND plugin_courses_courses_images.deleted = 0
              ".$_search
		)->execute()->as_array();

		return $query['0']['count'];
	}

	public static function get_images($id, $limit, $offset, $sort, $dir, $search = FALSE)
	{
		$_search = '';
		if ($search)
		{
			$_search = " AND `course_image`.image like '%".$search."%'";
		}
		$_limit = ($limit > -1) ? ' LIMIT '.$offset.','.$limit : '';
		$query = DB::query(
			Database::SELECT, 'SELECT `course_image`.*, SUBSTRING_INDEX(`media`.`dimensions`, \'x\', 1) `width`, SUBSTRING_INDEX(`media`.`dimensions`, \'x\', -1)  `height`
             FROM plugin_courses_courses_images `course_image`
             LEFT JOIN `plugin_media_shared_media` `media`
             ON (`course_image`.`image` = `media`.`filename` AND `media`.`location` = \'courses\')
             WHERE
                `course_image`.`course_id` = '.$id.'
              '.$_search.'
             AND
              	`course_image`.deleted = 0
             ORDER BY
                '.$sort.' '.$dir.'
             '.$_limit
		)->execute()->as_array();
		$return = array();
		if (count($query) > 0)
		{
			$i = 0;
			foreach ($query as $elem => $sub)
			{
				$return[$i]['width'] = $sub['width'];
				$return[$i]['height'] = $sub['height'];
				$return[$i]['thumbnail'] = '<img src="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $sub['image'], 'courses'.DIRECTORY_SEPARATOR.'_thumbs_cms').'" alt="'.$sub['image'].'" />';
				$return[$i]['file_name'] = $sub['image'];
				$return[$i]['remove'] = '<a href="#" class="delete_image" data-id="'.$sub['id'].'">Delete</a>';
				$i++;
			}
		}

		return $return;
	}

	public static function ajax_save_course($data)
	{
		$logged_in_user = Auth::instance()->get_user();
		if (self::get_course_valid($data) === TRUE)
		{
			$return['error'] = self::get_course_valid($data);

			return json_encode($return);
		}
		else
		{
			foreach ($data as $k => $val)
			{
				if ($val == '')
				{
					$data[$k] = NULL;
				}
			}
			$data['created_by'] = $logged_in_user['id'];
			$data['date_created'] = date('Y-m-d H:i:s');
			$data['deleted'] = 0;
			$query = DB::insert('plugin_courses_courses', array_keys($data))->values($data)->execute();
			$return['message'] = 'success';
			$return['course'] = $query['0'];

			return json_encode($return);
		}
	}

	public static function get_course_valid($data)
	{
		$error = FALSE;
		$message = '<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button>';
		if (!isset($data['title']) && strlen(trim($data['title'])) < 1)
		{
			$error = TRUE;
			$message .= "<strong>Warning</strong>Title cannot be empty!<br />";
		}
		if (!isset($data['category_id']) && (int) $data['category_id'] < 1)
		{
			$error = TRUE;
			$message .= "<strong>Warning</strong>Please select category!<br />";
		}

		$message .= "</div>";
		if ($error === FALSE)
		{
			return FALSE;
		}
		else
		{
			return $message;
		}

	}

	public static function save_image($data)
	{
		$logged_in_user = Auth::instance()->get_user();
		foreach ($data as $k => $val)
		{
			if ($val == '')
			{
				$data[$k] = NULL;
			}
		}
		$data['created_by'] = $logged_in_user['id'];
		$data['date_created'] = date('Y-m-d H:i:s');
		$query = DB::insert('plugin_courses_courses_images', array_keys($data))->values($data)->execute();
        $data['created_by'] = $logged_in_user['id'];
        $data['date_created'] = date('Y-m-d H:i:s');
        $activity = new Model_Activity();
        $activity
            ->set_item_type('course_image')
            ->set_action('add')
            ->set_item_id($query[0] ?? '0')
            ->set_scope_id($data['course_id'])
            ->save();
		return $query;
	}

    public static function save_topic($data)
    {
        foreach ($data as $k => $val)
        {
            if ($val == '')
            {
                $data[$k] = NULL;
            }
        }

        $check_query =  DB::query(
            Database::SELECT, '
                SELECT plugin_courses_courses_has_topics.`id`
                FROM `plugin_courses_courses_has_topics`
                WHERE `plugin_courses_courses_has_topics`.`deleted` = 0
                AND `plugin_courses_courses_has_topics`.`course_id` = :course_id
                AND `plugin_courses_courses_has_topics`.`topic_id` = :topic_id  '
        )
            ->param(':course_id',$data['course_id'])
            ->param(':topic_id',$data['topic_id'])
            ->execute()
            ->as_array();

        if (sizeof($check_query)==0) {
            $query = DB::insert('plugin_courses_courses_has_topics', array_keys($data))->values($data)->execute();
        }else{
            $query =  $check_query;
        }

        return $query;
    }

	public static function remove_image($id)
	{
        $course_id = DB::select('course_id')->from('plugin_courses_courses_images')->where('id', '=', $id)->execute()->get('course_id');
		$ret = DB::query(Database::UPDATE, "UPDATE `plugin_courses_courses_images` set `deleted` = 1 where id = ".$id)->execute();
		if ($ret > 0)
		{
			$response['message'] = 'success';
			$activity = new Model_Activity();
            $activity
                ->set_item_type('course_image')
                ->set_action('delete')
                ->set_item_id($id)
                ->set_scope_id($course_id)
                ->save();
		}
		else
		{
			$response['message'] = 'error';
			$response['error_msg'] = 'An error has occurred. Please contact support.';
		}

		return $response;
	}

    public static function remove_topic($data)
    {
        $query =  DB::query(
            Database::SELECT, '
                SELECT plugin_courses_courses_has_topics.`id`
                FROM `plugin_courses_courses_has_topics`
                WHERE `plugin_courses_courses_has_topics`.`deleted` = 0
                AND `plugin_courses_courses_has_topics`.`course_id` = :course_id 
                AND `plugin_courses_courses_has_topics`.`topic_id` = :topic_id  '
        )
            ->param(':course_id',$data['course_id'])
            ->param(':topic_id',$data['topic_id'])
            ->execute()
            ->as_array();

        $id = $query[0]['id'];

        $ret = DB::query(Database::UPDATE, "UPDATE `plugin_courses_courses_has_topics` set `deleted` = 1 where id = ".$id)->execute();
        if ($ret > 0)
        {
            $response['message'] = 'success';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error has occurred. Please contact support.';
        }

        return $response;
    }


	public static function filter($args)
	{
		$results_per_page = Settings::instance()->get('courses_results_per_page');
		$course_display = Settings::instance()->get('course_website_display');
        $nav_only = Settings::instance()->get('only_display_navision_courses');
		if (@$args['limit']) {
			$results_per_page = $args['limit'];
		}
		if (!isset($args['offset'])) {
			$args['offset'] = 0;
		}

        $media_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'courses');

		$return['data'] = $return['all_data'] = array();
		// Get all courses matching the supplied years, categories and levels
		$courses = DB::select(
			DB::expr('SQL_CALC_FOUND_ROWS plugin_courses_courses.*'),
			array('plugin_courses_levels.id', 'level_id'),
			array('plugin_courses_levels.level', 'level'),
			array('plugin_courses_categories.id', 'category_id'),
			array('plugin_courses_categories.category', 'category'),
			array('plugin_courses_years.id', 'year_id'),
			DB::expr('GROUP_CONCAT(DISTINCT plugin_courses_years.year) as `year`'),
			array('plugin_courses_types.type', 'types'),
			DB::expr('GROUP_CONCAT(DISTINCT plugin_courses_providers.name) as provider'),
            array('plugin_courses_schedules.start_date', 'date_start'),
            array('plugin_courses_schedules.end_date', 'date_end'),
            ['plugin_courses_schedules.id', 'schedule_id'],
            ['plugin_courses_schedules.location_id', 'schedule_location_id'],
            [DB::expr("IFNULL(`parent_location`.`county_id`, `plugin_courses_locations`.`county_id`)"), 'course_county_id']
		)
			->distinct(TRUE)
			->from('plugin_courses_courses')
			->join('plugin_courses_courses_has_years', 'left')->on('plugin_courses_courses.id', '=', 'plugin_courses_courses_has_years.course_id')
			->join('plugin_courses_years', 'LEFT')->on('plugin_courses_years.id', '=', 'plugin_courses_courses_has_years.year_id')
				->on('plugin_courses_years.delete', '=', DB::expr('0'))
				->on('plugin_courses_years.publish', '=', DB::expr('1'))
			->join('plugin_courses_categories', 'LEFT')->on('plugin_courses_categories.id', '=', 'plugin_courses_courses.category_id')
				->on('plugin_courses_categories.delete', '=', DB::expr('0'))
				->on('plugin_courses_categories.publish', '=', DB::expr('1'))
			->join('plugin_courses_levels', 'LEFT')->on('plugin_courses_levels.id', '=', 'plugin_courses_courses.level_id')
				->on('plugin_courses_levels.delete', '=', DB::expr('0'))
				->on('plugin_courses_levels.publish', '=', DB::expr('1'))
			->join('plugin_courses_types', 'LEFT')->on('plugin_courses_types.id', '=', 'plugin_courses_courses.type_id')
				->on('plugin_courses_types.delete', '=', DB::expr('0'))
				->on('plugin_courses_types.publish', '=', DB::expr('1'))
            ->join('plugin_courses_courses_has_providers', 'left')->on('plugin_courses_courses.id', '=', 'plugin_courses_courses_has_providers.course_id')
			->join('plugin_courses_providers', 'LEFT')->on('plugin_courses_providers.id', '=', 'plugin_courses_courses_has_providers.provider_id')
				->on('plugin_courses_providers.delete', '=', DB::expr('0'))
				->on('plugin_courses_providers.publish', '=', DB::expr('1'))
			->join('plugin_courses_schedules', 'LEFT')->on('plugin_courses_courses.id', '=', 'plugin_courses_schedules.course_id')
				->on('plugin_courses_schedules.delete', '=', DB::expr('0'))
				->on('plugin_courses_schedules.publish', '=', DB::expr('1'))
            ->join([Model_NAVAPI::TABLE_EVENTS, 'nav_event'], $nav_only ? 'inner' : 'left')
                ->on('nav_event.schedule_id', '=', 'plugin_courses_schedules.id');

        if (isset($args['book_on_website']) AND $args['book_on_website'] === true) {
            $courses = $courses->on('plugin_courses_schedules.book_on_website', '=', DB::expr('1'));
        }
        
        $courses = $courses
            ->join('plugin_courses_locations', 'LEFT')
                ->on('plugin_courses_locations.id', '=', 'plugin_courses_schedules.location_id')
            ->join(['plugin_courses_locations', 'parent_location'], 'LEFT')
                ->on('parent_location.id', '=', 'plugin_courses_locations.parent_id')
            ->join(['engine_lookup_values', 'learning_mode'], 'left')
                ->on('plugin_courses_schedules.learning_mode_id', '=', 'learning_mode.id')

            ->where('plugin_courses_courses.publish', '=', 1)
            ->and_where('plugin_courses_courses.deleted', '=', 0);

		if (isset($args['year_ids']) AND !empty($args['year_ids']))
		{
			$courses = $courses->and_where('plugin_courses_years.id', 'IN', $args['year_ids']);
		}
		if ( ! empty($args['category_ids']) AND ! empty($args['category_ids'][0]))
		{
			$courses = $courses->and_where('plugin_courses_categories.id', 'IN', $args['category_ids']);
		}
		if ( ! empty($args['subject_ids']) AND ! empty($args['subject_ids'][0]))
		{
			$courses = $courses->and_where('plugin_courses_courses.subject_id', 'IN', $args['subject_ids']);
		}
		if ( ! empty($args['level_ids']))
		{
			$courses = $courses->and_where('plugin_courses_levels.id', 'IN', $args['level_ids']);
		}
        if ( ! empty($args['type_ids']) AND ! empty($args['type_ids'][0])) {
            $courses = $courses->and_where('plugin_courses_courses.type_id', 'IN', $args['type_ids']);
        }
		if ( ! empty($args['course_ids']))
		{
			$courses = $courses->and_where('plugin_courses_courses.id', 'IN', $args['course_ids']);
		}
		if (isset($args['keywords']) AND $args['keywords'] != '')
		{
			$courses = $courses->and_where('plugin_courses_courses.title', 'LIKE', '%'.$args['keywords'].'%');
		}
        if (isset($args['cancelled_schedules']) AND $args['cancelled_schedules'] === false) {
            $courses = $courses->where('plugin_courses_schedules.schedule_status', '<>', Model_Schedules::CANCELLED);
        }
		if (isset($args['location_ids']) AND !empty($args['location_ids']))
		{
			if (!is_array($args['location_ids'])) {
				$args['location_ids'] = array($args['location_ids']);
			}

			$courses
					->and_where_open()
						->or_where('plugin_courses_schedules.location_id', 'IN', $args['location_ids'])
						->or_where('plugin_courses_locations.parent_id', 'IN', $args['location_ids'])
					->and_where_close();
		}

        if (isset($args['course_county_ids']) && !empty($args['course_county_ids'])) {
            if (!is_array($args['course_county_ids'])) {
                $args['course_county_ids'] = array($args['course_county_ids']);
            }

            $courses
                ->and_where_open()
                    ->where('plugin_courses_locations.county_id', 'IN', $args['course_county_ids'])
                    ->or_where('parent_location.county_id', 'IN', $args['course_county_ids'])
                ->and_where_close();
        }

        if (!empty($args['unstarted_only'])) {
            $courses->where('plugin_courses_schedules.start_date', '>=', date('Y-m-d H:i:s'));
        }

        // Schedule must be either self-paced or fall within a particular date range to be visible.
        if ($course_display != 0) {
            $courses->and_where_open();
            $courses->where('learning_mode.value', '=', 'self_paced');
            if ($course_display > 0) {
                $courses->or_where_open()->and_where_open();
                switch ($course_display) {
                    case 0: // All Date

                        break;
                    case 1: // Next Date
                        $start = date("Y-m-d H:i:s");
                        //$courses->and_where('plugin_courses_schedules.end_date', '>=', $start);
                        $courses->where(DB::expr("1"), '=', '1'); // dummy condition
                        break;
                    case 2: // Next 7 days
                        $start = date("Y-m-d H:i:s");
                        $end = date("Y-m-d H:i:s", strtotime('+ 1 week'));
                        $courses->and_where('plugin_courses_schedules.end_date', '>=', $start)
                            ->and_where('plugin_courses_schedules.start_date', '<=', $end);
                        break;
                    case 3: // Next 30 days
                        $start = date("Y-m-d H:i:s");
                        $end = date("Y-m-d H:i:s", strtotime('+ 30 day'));
                        $courses->and_where('plugin_courses_schedules.end_date', '>=', $start)
                            ->and_where('plugin_courses_schedules.start_date', '<=', $end);
                        break;
                    case 4: // Next 90 days
                        $start = date("Y-m-d H:i:s");
                        $end = date("Y-m-d H:i:s", strtotime('+ 90 day'));
                        $courses->and_where('plugin_courses_schedules.end_date', '>=', $start)
                            ->and_where('plugin_courses_schedules.start_date', '<=', $end);
                        break;
                    case 5: // Next 365 days
                        $start = date("Y-m-d H:i:s");
                        $end = date("Y-m-d H:i:s", strtotime('+ 365 day'));
                        $courses->and_where('plugin_courses_schedules.end_date', '>=', $start)
                            ->and_where('plugin_courses_schedules.start_date', '<=', $end);
                        break;
                }
                $courses->and_where_close()->or_where_close();
            }
            $courses->and_where_close();
        }

		if (isset($args['sort']) AND strtolower($args['sort']) == 'desc')
		{
			$courses = $courses->order_by('title', 'DESC');
		}
		else
		{
			$courses = $courses->order_by('title', 'ASC');
		}

        if (!empty($args['group_by'])) {
            $courses->group_by($args['group_by']);
        }
        else {
            $courses->group_by('plugin_courses_courses.id');
            if (!@$args['unique_courses']) {
                $courses->group_by('plugin_courses_schedules.id');
            }
        }

        // 'all_data' contains all results.
        // 'data' contains results for just the current page.
        // Only loop through 'data' when getting further information.
        $return['all_data'] = $courses->execute()->as_array();

        $courses->limit($results_per_page);
        $courses->offset($args['offset']);

		$courses = $courses->execute()->as_array();
		$total_count = DB::select(DB::expr('FOUND_ROWS() AS cnt'))->execute()->get('cnt');

		// Get all schedules for each course, matching the supplied location
		if (is_array($courses) && count($courses) > 0)
		{
            $return['data'] = $courses;

			for ($i = 0 + $args['offset']; $i < $total_count AND $i < $results_per_page + $args['offset']; $i++)
			{

                $schedules = DB::select(
                    array('plugin_courses_schedules.id', 'id'),
                    array('plugin_courses_schedules.allow_purchase_order', 'allow_purchase_order'),
                    array('plugin_courses_schedules.allow_credit_card', 'allow_credit_card'),
                    array('plugin_courses_schedules.allow_sales_quote', 'allow_sales_quote'),
                    array('plugin_courses_repeat.name', 'repeat'),
                    DB::expr("IF(plugin_courses_schedules.booking_type = 'Whole Schedule', 'all', plugin_courses_schedules_events.id) AS `event_id`"),
                    DB::expr("IF(plugin_courses_schedules.booking_type = 'Whole Schedule', plugin_courses_schedules.start_date, plugin_courses_schedules_events.datetime_start) AS `start_date`"),
                    DB::expr("IF(plugin_courses_schedules.booking_type = 'Whole Schedule', plugin_courses_schedules.end_date, plugin_courses_schedules_events.datetime_end) AS `end_date`"),
                    array('plugin_courses_schedules.end_date', 'schedule_end_date'),
                    array('plugin_courses_schedules.is_fee_required', 'is_fee_required'),
                    array('plugin_courses_schedules.fee_amount', 'fee_amount'),
                    array('plugin_courses_schedules.fee_per', 'fee_per'),
                    array('plugin_courses_schedules.location_id', 'location_id'),
                    array('plugin_courses_locations.name', 'location'),
                    array(DB::expr("CONCAT(contact.first_name,' ', contact.last_name)"), 'trainer_name'),
					'plugin_courses_schedules.booking_type'
                )
                    ->from('plugin_courses_schedules')
                    ->join('plugin_courses_schedules_events', 'LEFT')
                    ->on('plugin_courses_schedules.id', '=', 'plugin_courses_schedules_events.schedule_id')
                    ->join('plugin_courses_locations', 'LEFT')
                    ->on('plugin_courses_locations.id', '=', 'plugin_courses_schedules.location_id')
                    ->join('plugin_courses_repeat', 'LEFT')
                    ->on('plugin_courses_schedules.repeat', '=', 'plugin_courses_repeat.id');

                if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
                {
                    $trainer_origin = (Settings::instance()->get('only_show_primary_trainer_course_dropdown') === '1') ? 'plugin_courses_schedules' : 'plugin_courses_schedules_events';
                    $schedules->join(array('plugin_contacts3_contacts', 'contact'), 'LEFT')
                        ->on('contact.id', '=', "{$trainer_origin}.trainer_id")
                        ->join(array('plugin_contacts3_contact_has_roles', 'has_role'), 'LEFT')
                        ->on('contact.id', '=', 'has_role.contact_id')
                        ->on('has_role.role_id', '=', DB::expr(4));
                }
                else
                {
                    $schedules->join(array('plugin_courses_trainers', 'contact'), 'LEFT')
                        ->on('contact.id', '=', 'plugin_courses_schedules_events.trainer_id')
                        ->on('contact.delete', '=', DB::expr('0'));
                }
                $schedules->where('plugin_courses_schedules.publish', '=', 1)
                    ->and_where('plugin_courses_schedules.delete', '=', 0)
                    ->and_where('plugin_courses_schedules_events.publish', '=', 1)
                    ->and_where('plugin_courses_schedules_events.delete', '=', 0)
                    ->and_where('plugin_courses_schedules.course_id', '=', $return['data'][$i - $args['offset']]['id'])
                    ->and_where('plugin_courses_schedules_events.datetime_start', '>', DB::expr('CURDATE()'));

                if (isset($args['book_on_website']) && $args['book_on_website'] === true) {
                    $schedules->where('plugin_courses_schedules.book_on_website', '=', 1);
                }

                if (isset($args['location_ids']) AND !empty($args['location_ids']))
                {
                    $schedules
                        ->and_where_open()
                        ->and_where('plugin_courses_schedules.location_id', 'IN', $args['location_ids'])
                        ->or_where('plugin_courses_locations.parent_id', 'IN', $args['location_ids'])
                        ->and_where_close();
                }

                if (!empty($args['unstarted_only'])) {
                    $schedules->where('plugin_courses_schedules.start_date', '>=', date('Y-m-d H:i:s'));
                }

                if (isset($args['cancelled_schedules']) AND $args['cancelled_schedules'] === false)
                {
                    $schedules->where('plugin_courses_schedules.schedule_status', '<>', Model_Schedules::CANCELLED);
                }

                switch ($course_display) {
                    case 2: // Next 7 days
                        $end = date("Y-m-d H:i:s", strtotime('+ 1 week'));
                        break;
                    case 3: // Next 30 days
                        $end = date("Y-m-d H:i:s", strtotime('+ 30 day'));
                        break;
                    case 4: // Next 90 days
                        $end = date("Y-m-d H:i:s", strtotime('+ 90 day'));
                        break;
                    case 5: // Next 365 days
                        $end = date("Y-m-d H:i:s", strtotime('+ 365 day'));
                        break;
                    default:
                        $end = FALSE;
                }

                if ($end)
                {
                    $schedules
                        ->and_where(
                            DB::expr("IF(plugin_courses_schedules.booking_type = 'Whole Schedule', plugin_courses_schedules.end_date, plugin_courses_schedules_events.datetime_end)"),
                            '<=', $end
                        );

                }

                $schedules->group_by('plugin_courses_schedules.id');
                $schedules = $schedules
                    ->order_by('plugin_courses_schedules_events.datetime_start', 'asc')
                    ->execute()
                    ->as_array();
                $schedules_dates = array();
                $locations = array();

                foreach ($schedules as $si => &$schedule)
                {
                    if (empty($locations[$schedule['location_id']]))
                    {
                        $locations[$schedule['location_id']] = Model_Locations::get_location_name_or_parent($schedule['location_id']);
                    }

                    $schedule['missed_classes'] = DB::select(DB::expr('count(*) as cnt'))
                        ->from('plugin_courses_schedules_events')
                        ->where('schedule_id', '=', $schedule['id'])
                        ->and_where('delete', '=', 0)
                        ->and_where('publish', '=', 1)
                        ->and_where('datetime_start', '<=', date('Y-m-d H:i:s'))
                        ->execute()
                        ->get('cnt');
                    $schedule['remaining_classes'] = DB::select(DB::expr('count(*) as cnt'))
                        ->from('plugin_courses_schedules_events')
                        ->where('schedule_id', '=', $schedule['id'])
                        ->and_where('delete', '=', 0)
                        ->and_where('publish', '=', 1)
                        ->and_where('datetime_start', '>', date('Y-m-d H:i:s'))
                        ->execute()
                        ->get('cnt');

                    if (@$args['timeslots'] OR $schedule['booking_type'] == 'One Timeslot') {
                        $schedule['timeslots'] = DB::select('*')
                            ->from('plugin_courses_schedules_events')
                            ->where('schedule_id', '=', $schedule['id'])
                            ->and_where('delete', '=', 0)
                            ->and_where('publish', '=', 1)
                            ->and_where('datetime_start', '>', date('Y-m-d H:i:s'))
                            ->order_by('datetime_start', 'asc')
                            ->execute()
                            ->as_array();
                    }
                }

                $location = isset($schedules[0]) ? Model_Locations::get_location_name_or_parent($schedules[0]['location_id']) : '';

                $images = DB::select('image.*')
                    ->from(array('plugin_courses_courses_images', 'course_image'))
                    ->join(array('plugin_media_shared_media', 'image'), 'left')
                    ->on('course_image.image', '=', 'image.filename')
                    ->join(array('plugin_media_shared_media_photo_presets', 'preset'), 'left')
                    ->on('image.preset_id', '=', 'preset.id')
                    ->where('course_image.course_id', '=', $return['data'][$i - $args['offset']]['id'])
                    ->where('course_image.deleted', '=', 0)
                    ->where('preset.title', 'not like', '%banner%')
                    ->execute()
                    ->as_array();

                $banners = DB::select('image.*')
                    ->from(array('plugin_courses_courses_images', 'course_image'))
                    ->join(array('plugin_media_shared_media', 'image'), 'left')
                    ->on('course_image.image', '=', 'image.filename')
                    ->join(array('plugin_media_shared_media_photo_presets', 'preset'), 'left')
                    ->on('image.preset_id', '=', 'preset.id')
                    ->where('course_image.course_id', '=', $return['data'][$i - $args['offset']]['id'])
                    ->where('course_image.deleted', '=', 0)
                    ->where('preset.title', 'like', '%banner%')
                    ->execute()
                    ->as_array();

                $topics = DB::select('topic.*')
                    ->from(array('plugin_courses_topics', 'topic'))
                    ->join(array('plugin_courses_courses_has_topics', 'has_topic'))
                    ->on('has_topic.topic_id', '=', 'topic.id')
                    ->where('has_topic.course_id', '=', $return['data'][$i - $args['offset']]['id'])
                    ->where('topic.deleted',       '=', 0)
                    ->where('has_topic.deleted',   '=', 0)
                    ->execute()
                    ->as_array();

                $item = $return['data'][$i - $args['offset']];


                $item['banners']   = $banners;
                $item['images']    = $images;
                $item['locations'] = $locations;
                $item['schedules'] = $schedules;
                $item['topics']    = $topics;

                $search_result_times = self::get_search_result_times($item);

                // The next few values are needed for the search_result.php
                $url_name = str_replace('%2F', '', urlencode($item['title']));
                if ($search_result_times['same_location']) {
                    $location = $location_text = $search_result_times['same_location'];
                }
                elseif (isset($item['locations']) && count($item['locations']) > 1)  {
                    $location = 'all';
                    $location_text =  __('Multiple locations');
                }
                elseif (isset($item['locations']) && count($item['locations']) == 1) {
                    $location =  $location_text = reset($item['locations']);
                }
                else {
                    $location = '';
                    $location_text = '';
                }
                $has_tags = ($item['year'] || $item['level']);

                $item['location']      = $location;
                $item['type']          = 'course';
                $item['link']          = '/course-detail/'.$url_name.'/?id='.$item['id'];
                $item['image']         = $media_path.( ! empty($item['images'][0]) ? $item['images'][0]['filename'] : 'course-placeholder.png');
                $item['image_overlay'] = $location_text;
                $item['image_overlay_name'] = $location;
                $item['subtitle']      = $item['level'] ? $item['year'].' '.$item['level'] : false;
                $item['tags']          = $has_tags ? array($item['year'], $item['category'], $item['level']) : array();
                $item['times_options'] = $search_result_times['options'];
                $item['same_fee']      = ($search_result_times['same_fee'] != false);
                $item['price_amount']  = $search_result_times['cheapest_fee'];
                $item['summary']       = !$has_tags ? $item['summary'] : false;
                $item['button_text']   = __('View Details');
                $item['course']        = new Model_Course($item['id']);

                $return['data'][$i - $args['offset']] = $item;

                unset($schedules);
                unset($schedules_dates);
            }
		}

		$return['total_count'] = $total_count;
		$return['page'] = ($results_per_page == 0) ? 1 : 1 + $args['offset'] / $results_per_page;
		$first_result = 1 + $args['offset'];
		$last_result = ($results_per_page + $args['offset'] > $return['total_count']) ? $return['total_count'] : $results_per_page + $args['offset'];
		if (count($return['all_data']) == 0)
		{
			$return['results_found'] = __('No results found');
		}
		else
		{
			$return['results_found'] = 'Showing results '.$first_result.' to '.$last_result.' of '.$total_count;
		}

		return $return;
	}

    public static function get_available_filters()
    {
        // Get all results with no filters applied
        $results = self::filter(['group_by' => 'plugin_courses_schedules.id', 'book_on_website' => true, 'cancelled_schedules' => false]);
        $category_ids = [];
        $course_ids   = [];
        $course_county_ids = [];
        $location_ids = [];
        $level_ids    = [];
        $schedule_ids = [];
        $subject_ids  = [];
        $type_ids     = [];
        $year_ids     = [];

        $cycles = [];

        // Loop through each result to find the options for each filter that can actually yield results
        foreach ($results['all_data'] as $result) {
            $category_ids[] = $result['category_id'];
            $course_ids[]   = $result['id'];
            $course_county_ids[] = $result['course_county_id'];
            $level_ids[]    = $result['level_id'];
            $location_ids[] = $result['schedule_location_id'];
            $schedule_ids[] = $result['schedule_id'];
            $subject_ids[]  = $result['subject_id'];
            $type_ids[]     = $result['type_id'];
            $year_ids[]     = $result['year_id'];

            if ($result['cycle']) {
                $cycles[$result['cycle']] = $result['cycle'];
            }
        }

        $category_ids = array_unique($category_ids);
        $course_ids   = array_unique($course_ids);
        $course_county_ids = array_unique($course_county_ids);
        $level_ids    = array_unique($level_ids);
        $location_ids = array_unique($location_ids);
        $schedule_ids = array_unique($schedule_ids);
        $subject_ids  = array_unique($subject_ids);;
        $type_ids     = array_unique($type_ids);
        $year_ids     = array_unique($year_ids);

        if ($location_ids) {
            $locations = ORM::factory('Course_Location')
                ->and_where_open()
                    ->where('id', 'in', $location_ids)
                    ->or_where('parent_id', 'in', $location_ids)
                ->and_where_close()
                ->find_all_published();
        } else {
            $locations = [];
        }

        $top_level_locations = [];
        foreach ($locations as $location) {
            if ($location->parent_id == '') {
                $top_level_locations[$location->id] = $location->as_array();
            } elseif ($location->parent->parent_id == '') {
                $top_level_locations[$location->parent->id] = $location->parent->as_array();
            } elseif ($location->parent->parent->parent_id == '') {
                $top_level_locations[$location->parent->parent->id] = $location->parent->parent->as_array();
            }
        }

        if ($course_ids) {
            $topics = DB::select('topic.*')
                ->distinct(true)
                ->from(['plugin_courses_courses_has_topics', 'cht'])
                ->join(['plugin_courses_topics', 'topic'])->on('cht.topic_id', '=', 'topic.id')
                ->where('cht.course_id', 'in', $course_ids)
                ->execute()->as_array();
        } else {
            $topics = [];
        }

        if ($subject_ids) {
            $trainers = DB::select('trainer.*', [DB::expr("`trainer`.`first_name`, ' ', `trainer`.`last_name`"), 'full_name'])
                ->distinct(true)
                ->from(['plugin_courses_schedules_events', 'event'])
                ->join(['plugin_contacts3_contacts', 'trainer'])->on('event.trainer_id', '=', 'trainer.id')
                ->where('event.schedule_id', 'in', $schedule_ids)
                ->execute()->as_array();
            ;
        } else {
            $trainers = [];
        }

        $categories      = $category_ids      ? ORM::factory('Course_Category')->where('id', 'in', $category_ids     )->order_by('order', 'asc')->find_all_published()->as_array() : [];
        $courses         = $course_ids        ? ORM::factory('Course'         )->where('id', 'in', $course_ids       )->find_all_published()->as_array() : [];
        $course_counties = $course_county_ids ? ORM::factory('Course_County'  )->where('id', 'in', $course_county_ids)->find_all_published()->as_array() : [];
        $subjects        = $subject_ids       ? ORM::factory('Course_Subject' )->where('id', 'in', $subject_ids      )->find_all_published()->as_array() : [];
        $types           = $type_ids          ? ORM::factory('Course_Type'    )->where('id', 'in', $type_ids         )->find_all_published()->as_array() : [];
        $levels          = $level_ids         ? ORM::factory('Course_Level'   )->where('id', 'in', $level_ids        )->find_all_published()->as_array() : [];
        $years           = $year_ids          ? ORM::factory('Course_Year'    )->where('id', 'in', $year_ids         )->find_all_published()->as_array() : [];

        return [
            'categories'      => $categories,
            'courses'         => $courses,
            'course_counties' => $course_counties,
            'cycles'          => $cycles,
            'levels'          => $levels,
            'locations'       => array_values($top_level_locations),
            'subjects'        => $subjects,
            'topics'          => $topics,
            'trainers'        => $trainers,
            'types'           => $types,
            'years'           => $years
        ];

    }

    public static function get_search_result_times($course)
    {
        $options = array();

        $course['schedules'] = isset($course['schedules']) ? $course['schedules'] : array();

        $same_fee = null;
        $same_location = null;
        $cheapest = null;

        foreach ($course['schedules'] as $schedule) {
            if (isset($schedule['timeslots']) && count($schedule['timeslots']) > 1 && $schedule['booking_type'] == 'One Timeslot') {
                foreach ($schedule['timeslots'] as $stimeslot) {
                    if (strtotime($stimeslot['datetime_start']) < time()) continue;

                    $fee_amount = $stimeslot['fee_amount'] ?: $schedule['fee_amount'];
                    $discount = 0;
                    //Model_CourseBookings is not used bookings plugin is enabled.
                    if (!Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
                        $discounts = Model_CourseBookings::get_available_discounts(null, array(
                            array(
                                'id' => $schedule['id'],
                                'fee' => $fee_amount,
                                'discount' => 0,
                                'prepay' => 1
                            )
                        ));
                        if (isset($discounts[0])) {
                            if ($discounts[0]['discount'] > 0) {
                                $discount = $discounts[0]['discount'];
                            }
                        }
                    }

                    $item_fee = $fee_amount - $discount;

                    $option_text  = date('D - d/m/Y - H:i', strtotime($stimeslot['datetime_start']));
                    $option_text .= $schedule['trainer_name'] ? ' - '.$schedule['trainer_name'] : '';
                    $option_text .= $schedule['location']     ? ' - '.$schedule['location']     : '';
                    $option_text .= $item_fee ? ' - €'.$item_fee : '';

                    $options[] = array(
                        'text'       => $option_text,
                        'attributes' => array(
                            'value'         => $schedule['id'],
                            'data-fee'      => $item_fee,
                            'data-event_id' => $stimeslot['id']
                        )
                    );

                    // Check the fee and location are the same for each iteration of the loop
                    $same_fee      = ($same_fee      === null || ($same_fee      !== false && $same_fee      == $item_fee            )) ? $item_fee             : false;
                    $same_location = ($same_location === null || ($same_location !== false && $same_location == $schedule['location'])) ? $schedule['location'] : false;

                    if ($cheapest === null || $item_fee < $cheapest) {
                        $cheapest = $item_fee;
                    }

                }
            } else {
                if (is_array($schedule) AND array_key_exists('id', $schedule)) {
                    $start_date = ((isset($schedule['timeslots']) AND isset($schedule['timeslots'][0])) ? $schedule['timeslots'][0]['datetime_start'] : $schedule['start_date']);
                    $end_date   = ((isset($schedule['timeslots']) AND isset($schedule['timeslots'][0])) ? $schedule['timeslots'][0]['datetime_end']   : $schedule['end_date']);
                    $discount = 0;
					//Model_CourseBookings is not used bookings plugin is enabled.
					if (!Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
						$discounts = Model_CourseBookings::get_available_discounts(null, array(
								array(
										'id' => $schedule['id'],
										'fee' => $schedule['fee_amount'],
										'discount' => 0,
										'prepay' => 1
								)
						));
						if (isset($discounts[0])) {
							if ($discounts[0]['discount'] > 0) {
								$discount = $discounts[0]['discount'];
							}
						}
					}

                    $option_text = '';

                    if ($schedule['repeat']) {
                        $duration_in_seconds = strtotime($end_date) - strtotime($start_date);
                        $duration_h = floor($duration_in_seconds / 3600);
                        $duration_m = (($duration_in_seconds % 3600) / 60);
                        $duration = ($duration_in_seconds > 0) ? ($duration_h . ($duration_m == 30 ? '.5' : '') . "h " . ($duration_m > 0 && $duration_m != 30 ? $duration_m . 'm' : '')) : FALSE;

                        if (Settings::instance()->get('show_start_date_for_repeating_timeslots')) {
                            $option_text .= date('D - H:i j/M/Y', strtotime($start_date));
                            $option_text .= $schedule['location']     ? ' - '.$schedule['location']     : '';
                            $option_text .= $duration                 ? ' - '.$duration                 : '';
                            $option_text .= $schedule['fee_amount']   ? ' - €'.$schedule['fee_amount']  : '';

                        } else {
                            $option_text .= date('D - H:i', strtotime($start_date));
                            $option_text .= $schedule['location']     ? ' - '.$schedule['location']     : '';
                            $option_text .= $schedule['trainer_name'] ? ' - '.$schedule['trainer_name'] : '';
                            $option_text .= $duration                 ? ' - '.$duration                 : '';
                            $option_text .= $schedule['fee_amount']   ? ' - €'.$schedule['fee_amount']  : '';
                        }
                    } else {
                        $option_text .=  date('D - d/m/Y - H:i', strtotime($start_date));
                        $option_text .=  $schedule['location']     ? ' - '.$schedule['location']     : '';
                        $option_text .=  $schedule['trainer_name'] ? ' - '.$schedule['trainer_name'] : '';
                        $option_text .=  $schedule['fee_amount']   ? ' - €'.($schedule['fee_amount'] - $discount) : '';
                    }

                    $item_fee = $schedule['fee_amount'] - $discount;

                    $options[] = array(
                        'text'       => $option_text,
                        'attributes' => array(
                            'value'    => $schedule['id'],
                            'data-fee' => $item_fee
                        )
                    );

                    // Check the fee and location are the same for each iteration of the loop
                    $same_fee      = ($same_fee === null      || ($same_fee      !== false && $same_fee      == $item_fee            )) ? $item_fee             : false;
                    $same_location = ($same_location === null || ($same_location !== false && $same_location == $schedule['location'])) ? $schedule['location'] : false;

                    if ($cheapest === null || $item_fee < $cheapest) {
                        $cheapest = $item_fee;
                    }
                }
            }
        }

        // If all items are the same fee or same location, don't include the same text in each option
        if ($same_fee !== false) {
            foreach ($options as $key => &$option) {
                $option['text'] = preg_replace('/ - €'.$same_fee.'$/', '', $option['text']);
            }
        }

        return [
            'cheapest_fee'  => $cheapest,
            'options'       => $options,
            'same_fee'      => $same_fee,
            'same_location' => $same_location
        ];
    }

	public static function get_courses_for_page($page, $limit, $sort, $title = FALSE, $location = FALSE, $level = FALSE, $category = FALSE, $exclude_past_dates = FALSE, $year = FALSE)
	{
		if ($sort == 'asc')
		{
			$order = " ORDER BY `plugin_courses_courses`.`title` asc";
		}
		else
		{
			$order = " ORDER BY `plugin_courses_courses`.`title` desc";
		}
		$_search = "";
		$_ssearch = "";
		if ($title !== FALSE)
		{
			$_search .= " AND `plugin_courses_courses`.`title` like '%".Database::instance()->real_escape(urldecode($title))."%'";
		}
		if ($level !== FALSE)
		{
			$_search .= " AND `plugin_courses_courses`.`level_id` = ".(int) $level;
		}
		if ($category !== FALSE)
		{
			if (is_string($category) AND !is_numeric($category))
			{
				$q = DB::select('id')->from('plugin_courses_categories')->where('category', '=', trim(urldecode($category)))->execute()->as_array();
				if (count($q) > 0)
				{
					$category = $q[0]['id'];
					$_search .= " AND `plugin_courses_courses`.`category_id` = ".$category;
				}
			}
			elseif (is_numeric($category))
			{
				$_search .= " AND `plugin_courses_courses`.`category_id` = ".(int) $category;
			}
		}
		if ($year !== FALSE)
		{
			if (is_string($year) AND !is_numeric($year))
			{
				$_search .= " AND `plugin_courses_years`.`year` = '".trim(urldecode($year))."'";
			}
			elseif (is_numeric($year))
			{
				$_search .= " AND `plugin_courses_years`.`id` = ".(int) $year;
			}
		}
		// @NOTE: THIS FILTER: $location IS USED FOR THE COURSE-SCHEDULES SQL QUERY, or the $courses_secondary_query
		if ($location !== FALSE)
		{
			if (is_string($location) AND !is_numeric($location))
			{
				$q = DB::select('id')->from('plugin_courses_locations')->where('name', '=', trim(urldecode($location)))->and_where('delete', '=', '0')->and_where('publish', '=', '1')->execute()->as_array();
				if (count($q) > 0)
				{
					$location = $q[0]['id'];
					$_ssearch .= " AND (`plugin_courses_locations`.`id` = ".$location." OR `plugin_courses_locations`.`parent_id` = ".$location.")";
				}
			}
			elseif (is_numeric($location))
			{
				$_ssearch .= " AND (`plugin_courses_locations`.`id` = ".(int) $location." OR `plugin_courses_locations`.`parent_id` = ".(int) $location.")";
			}
		}
		if ($exclude_past_dates !== FALSE)
		{
			$_search .= " AND `plugin_courses_schedules`.`end_date` > CURDATE()";
			$_ssearch .= " AND `plugin_courses_schedules`.`end_date` > CURDATE()";
		}
		$from = ($page * $limit) - $limit;
		$to = $page * $limit;
		$_limit = " LIMIT ".$from.", ".$to;

		// Get Number of Pages for the Main Query
		$return['pages'] = self::count_courses_pages($limit, $_ssearch);
		$locations_filter_where = (trim($_ssearch) != '') ? ' AND `plugin_courses_schedules`.`location_id` = '.(int) $location.' ' : '';


		// Get Data with Courses to be Listed - COURSES MAIN QUERY
		$return['data'] = array();
		$courses = DB::query(
			Database::SELECT, 'SELECT DISTINCTROW `plugin_courses_courses`.*,
                    `plugin_courses_categories`.`category`,
                    `plugin_courses_years`.`year`,
                    `plugin_courses_types`.`type`,
                    `plugin_courses_levels`.`level`,
                    GROUP_CONCAT(`plugin_courses_providers`.`name`) as `provider`
             FROM `plugin_courses_courses`
             LEFT JOIN
              `plugin_courses_categories`
              ON
                `plugin_courses_categories`.`id` = `plugin_courses_courses`.`category_id`
             LEFT JOIN
              `plugin_courses_years`
              ON
                `plugin_courses_years`.`id` = `plugin_courses_courses`.`year_id`
             LEFT JOIN
              `plugin_courses_types`
              ON
                `plugin_courses_types`.`id` = `plugin_courses_courses`.`type_id`
             LEFT JOIN
              `plugin_courses_levels`
              ON
                `plugin_courses_levels`.`id` = `plugin_courses_courses`.`level_id`
             LEFT JOIN
              plugin_courses_courses_has_providers
              ON plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
             LEFT JOIN
              `plugin_courses_providers`
              ON
                `plugin_courses_providers`.`id` = `plugin_courses_courses_has_providers`.`provider_id`
            LEFT JOIN
              `plugin_courses_schedules`
              ON
                `plugin_courses_courses`.`id` = `plugin_courses_schedules`.`course_id`
             WHERE
                `plugin_courses_courses`.`deleted` = 0
        		AND `plugin_courses_courses`.`publish` = 1'.$_search.$locations_filter_where.$order.$_limit . '
        		GROUP BY `plugin_courses_courses`.`id`'
		)->execute()->as_array();
		if (is_array($courses) && count($courses) > 0)
		{
			$i = 0;
			foreach ($courses as $course => $val)
			{
				// Get the Schedules for this Course - COURSES SECONDARY QUERY
				$course_schedules = DB::query(
					Database::SELECT, "SELECT
				`plugin_courses_schedules`.`id`,
				`plugin_courses_schedules_events`.`id` as `event_id`,
				`plugin_courses_schedules_events`.`datetime_start` as `start_date`,
				`plugin_courses_schedules`.`is_fee_required`,
				IF(`plugin_courses_schedules`.`fee_per` = 'Timeslot', `plugin_courses_schedules_events`.`fee_amount`, `plugin_courses_schedules`.`fee_amount`) AS `fee_amount`,
				`plugin_courses_schedules`.`fee_per`,
				`plugin_courses_locations`.`id` as `location_id`,
				`plugin_courses_locations`.`name` as `location`
				FROM
				`plugin_courses_schedules`
				JOIN
				`plugin_courses_schedules_events`
				ON
				`plugin_courses_schedules_events`.`schedule_id` = `plugin_courses_schedules`.`id`
				RIGHT JOIN
				`plugin_courses_locations`
				ON
				`plugin_courses_locations`.`id` = `plugin_courses_schedules`.`location_id`
				WHERE
				`plugin_courses_schedules`.`delete` = 0
				AND
				`plugin_courses_schedules`.`publish` = 1
				AND
				`plugin_courses_schedules_events`.`delete` = 0
				AND
				`plugin_courses_schedules_events`.`publish` = 1
				AND
				`plugin_courses_schedules`.`course_id` = ".$val['id']
					.$_ssearch."
				ORDER BY
				`plugin_courses_schedules`.`start_date`"
				)->execute()->as_array();

				// Add this Course to be LISTED ONLY if it HAS a Schedule(s) - APPLY THIS FILTER ONLY WHEN $_ssearch IS NOT EMPTY_STRING
				if (trim($_ssearch) != '')
				{
					if (count($course_schedules) > 0)
					{
						$return['data'][$i]['title'] = $val['title'];
						$return['data'][$i]['id'] = $val['id'];
						$return['data'][$i]['price'] = @$val['price'] ? $val['price'] : '';
						$return['data'][$i]['year'] = $val['year'] ? $val['year'] : '';
						$return['data'][$i]['level'] = $val['level'] ? $val['level'] : '';
						$return['data'][$i]['category'] = $val['category'] ? $val['category'] : '';
						$return['data'][$i]['type'] = $val['type'] ? $val['type'] : '';
						$return['data'][$i]['summary'] = $val['summary'] ? $val['summary'] : '';
						$return['data'][$i]['schedules'] = $course_schedules;
						unset($course_schedules);
						$i++;
					}
					else
					{
						unset($course_schedules);
						continue;
					}
				}
				else
				{
					$return['data'][$i]['title'] = $val['title'];
					$return['data'][$i]['id'] = $val['id'];
					$return['data'][$i]['year'] = $val['year'] ? $val['year'] : '';
					$return['data'][$i]['level'] = $val['level'] ? $val['level'] : '';
					$return['data'][$i]['category'] = $val['category'] ? $val['category'] : '';
					$return['data'][$i]['type'] = $val['type'] ? $val['type'] : '';
					$return['data'][$i]['summary'] = $val['summary'] ? $val['summary'] : '';
					$return['data'][$i]['schedules'] = $course_schedules;

					unset($course_schedules);
					$i++;
				}
			}
		}

		// Return
		return $return;
	}

	public static function count_courses_pages($per_page = NULL, $_ssearch)
	{
		if (is_null($per_page))
		{
			$per_page = Settings::instance()->get('courses_results_per_page');
		}

		$query = DB::query(
			Database::SELECT, "SELECT DISTINCTROW `plugin_courses_courses`.*
        FROM `plugin_courses_courses`
        LEFT JOIN
              plugin_courses_categories
              ON
                plugin_courses_categories.id = plugin_courses_courses.category_id
			LEFT JOIN plugin_courses_courses_has_years ON plugin_courses_courses.id = plugin_courses_courses_has_years.course_id
             LEFT JOIN
              plugin_courses_years
              ON
                plugin_courses_years.id = plugin_courses_courses_has_years.year_id
             LEFT JOIN
              plugin_courses_types
              ON
                plugin_courses_types.id = plugin_courses_courses.type_id
             LEFT JOIN
              plugin_courses_courses_has_providers
              ON
               plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
             LEFT JOIN
              plugin_courses_providers
              ON
                plugin_courses_providers.id = plugin_courses_courses_has_providers.provider_id
             LEFT JOIN
              plugin_courses_schedules
              ON
                plugin_courses_schedules.course_id = plugin_courses_courses.id
             LEFT JOIN
              plugin_courses_locations
              ON
                plugin_courses_locations.id = plugin_courses_schedules.location_id
        WHERE
        `plugin_courses_courses`.`deleted` = 0
        AND
        `plugin_courses_courses`.`publish` = 1".$_ssearch
		)->execute()->as_array();
		$pages = ceil(count($query) / $per_page);

		return $pages;
	}

	public static function get_detailed_info($id, $group_by_id = true, $timeslots = false, $show_primary_trainer = false,
        $exclude_cancelled_schedules = true, $args = array())
	{
        $last_search_parameters = Session::instance()->get('last_search_params');
		$return['data'] = array();

        $nav_only = Settings::instance()->get('only_display_navision_courses');

		$course = DB::query(
			Database::SELECT, 'SELECT `plugin_courses_courses`.*,
                    `plugin_courses_categories`.`category`,
                    `plugin_courses_years`.`year`,
                    `plugin_courses_types`.`type`,
                    `plugin_courses_levels`.`level`,
                    GROUP_CONCAT(`plugin_courses_providers`.`name`) as `provider`,
                    `plugin_courses_courses`.`file_id`,
                    `plugin_courses_courses`.`banner`
             FROM `plugin_courses_courses`
             LEFT JOIN
              `plugin_courses_categories`
              ON
                `plugin_courses_categories`.`id` = `plugin_courses_courses`.`category_id`
             LEFT JOIN
              `plugin_courses_years`
              ON
                `plugin_courses_years`.`id` = `plugin_courses_courses`.`year_id`
             LEFT JOIN
              `plugin_courses_types`
              ON
                `plugin_courses_types`.`id` = `plugin_courses_courses`.`type_id`
             LEFT JOIN
              `plugin_courses_levels`
              ON
                `plugin_courses_levels`.`id` = `plugin_courses_courses`.`level_id`
             LEFT JOIN
              plugin_courses_courses_has_providers
              ON plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
             LEFT JOIN
              `plugin_courses_providers`
              ON
                `plugin_courses_providers`.`id` = `plugin_courses_courses_has_providers`.`provider_id`
             WHERE
                `plugin_courses_courses`.`deleted` = 0
        AND
        `plugin_courses_courses`.`publish` = 1
        AND
        `plugin_courses_courses`.`id` = '.Kohana::sanitize($id).'
        GROUP BY `plugin_courses_courses`.`id`
        ORDER BY `plugin_courses_courses`.`title`'
		)->execute()->as_array();
		if (is_array($course) && count($course) == 1)
		{
            // Used later when schedules are filtered. (Put here, so the same code isn't run repeatedly in the for loop)
            $course_website_display_setting = Settings::instance()->get('course_website_display');
            $start = date("Y-m-d H:i:s");
            $end = null;
            switch ($course_website_display_setting) {
                case 1:  $start = null; break;
                case 2:  $end = date("Y-m-d H:i:s", strtotime('+ 1 week'));  break;
                case 3:  $end = date("Y-m-d H:i:s", strtotime('+ 30 day'));  break;
                case 4:  $end = date("Y-m-d H:i:s", strtotime('+ 90 day'));  break;
                case 5:  $end = date("Y-m-d H:i:s", strtotime('+ 365 day')); break;
            }

			$i = 0;
			foreach ($course as $val)
			{
				$return['data'][$i]['title'] = $val['title'];
				$return['data'][$i]['id'] = $val['id'];
				$return['data'][$i]['year'] = $val['year'] ? $val['year'] : '';
				$return['data'][$i]['level'] = $val['level'] ? $val['level'] : '';
				$return['data'][$i]['category'] = $val['category'] ? $val['category'] : '';
				$return['data'][$i]['type'] = $val['type'] ? $val['type'] : '';
				$return['data'][$i]['summary'] = $val['summary'] ? $val['summary'] : '';
				$return['data'][$i]['banner'] = $val['banner'] ? trim($val['banner']) : '';
				$return['data'][$i]['description'] = $val['description'] ? $val['description'] : '';
				$return['data'][$i]['file_id'] = $val['file_id'];
				$return['data'][$i]['book_button'] = $val['book_button'];
                $return['data'][$i]['third_party_link'] = $val['third_party_link'];
                $return['data'][$i]['payment_option_selected'] = false;
                $sql = "SELECT
                `plugin_courses_schedules`.`id`,
                `plugin_courses_schedules`.owned_by,
                plugin_courses_schedules.booking_type,
                plugin_courses_schedules.is_group_booking,
                plugin_courses_schedules.allow_purchase_order,
                plugin_courses_schedules.allow_credit_card,
                plugin_courses_schedules.allow_sales_quote,
                IF(plugin_courses_schedules.booking_type = 'Whole Schedule', 'all', plugin_courses_schedules_events.id) AS `event_id`,
				IF(plugin_courses_schedules.booking_type = 'Whole Schedule', plugin_courses_schedules.start_date, plugin_courses_schedules_events.datetime_start) AS `start_date`,
				IF(plugin_courses_schedules.booking_type = 'Whole Schedule', plugin_courses_schedules.end_date, plugin_courses_schedules_events.datetime_end) AS `end_date`,
                `plugin_courses_schedules`.`is_fee_required`,
                IF(`plugin_courses_schedules`.`fee_per` = 'Timeslot', IFNULL(`plugin_courses_schedules_events`.`fee_amount`, `plugin_courses_schedules`.`fee_amount`), `plugin_courses_schedules`.`fee_amount`) AS `fee_amount`,
                `plugin_courses_schedules`.`fee_per`,
                `plugin_courses_schedules`.`min_capacity`,
                `plugin_courses_schedules`.`max_capacity`,
                `plugin_courses_schedules`.`book_on_website`,
                `plugin_courses_repeat`.`name` AS `repeat`,
                `plugin_courses_locations`.`name` as `location`,
                CONCAT_WS(' ', trainers.first_name, trainers.last_name) AS trainer_name,
                `learning_mode`.`value` AS `learning_mode`,
                `delivery_mode`.`value` AS `delivery_mode`
                FROM
                `plugin_courses_schedules`
                LEFT JOIN
                `plugin_courses_schedules_events`
                ON
                `plugin_courses_schedules_events`.`schedule_id` = `plugin_courses_schedules`.`id`

                ".($nav_only ? 'INNER' : 'LEFT')." JOIN
                `plugin_navapi_events`
                ON
                `plugin_navapi_events`.`schedule_id` = `plugin_courses_schedules`.`id`

                LEFT JOIN
                `plugin_courses_locations`
                ON
                `plugin_courses_locations`.`id` = `plugin_courses_schedules`.`location_id`
                LEFT JOIN
                `plugin_courses_repeat`
                ON
                `plugin_courses_repeat`.`id` = `plugin_courses_schedules`.`repeat`";
                if ($show_primary_trainer === true)
                {
                    $sql .= " LEFT JOIN
                    plugin_contacts3_contacts trainers
                    ON plugin_courses_schedules.trainer_id = trainers.id";
                } else if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
                {
                    $sql .= " LEFT JOIN
                    plugin_contacts3_contacts trainers
                    ON plugin_courses_schedules_events.trainer_id = trainers.id";
                }
                else 
                {
                    $sql .= " LEFT JOIN
                    plugin_courses_trainers trainers
                    ON (plugin_courses_schedules_events.trainer_id = trainers.id AND trainers.delete = 0)
                    ";
                }


                $sql .= "
                LEFT JOIN
                    `engine_lookup_values` `learning_mode`
                    ON `plugin_courses_schedules`.`learning_mode_id` = `learning_mode`.`id`
                LEFT JOIN
                    `engine_lookup_values` `delivery_mode`
                    ON `plugin_courses_schedules`.`delivery_mode_id` = `delivery_mode`.`id`
                WHERE
                `plugin_courses_schedules`.`delete` = 0
                AND
                `plugin_courses_schedules`.`publish` = 1
                AND
                `plugin_courses_schedules`.`course_id` = ".$val['id']."
                AND
                (
                    (
                        `plugin_courses_schedules_events`.`datetime_end` > NOW()
                        AND `plugin_courses_schedules_events`.`delete` = 0
                        AND `plugin_courses_schedules_events`.`publish` = 1
                    )
                    OR `learning_mode`.`value` = 'self_paced'
                ) ";

                if (is_numeric(@$last_search_parameters['location'])) {
                    $sql .= " AND (plugin_courses_locations.id = " . $last_search_parameters['location'] . " OR plugin_courses_locations.parent_id = " . $last_search_parameters['location'] . ") ";
                }
                if($exclude_cancelled_schedules) {
                    $schedule_cancelled = Model_Schedules::CANCELLED;
                    $sql .= " AND `plugin_courses_schedules`.`schedule_status` <> {$schedule_cancelled} ";
                }

                if (isset($args['book_on_website']) && $args['book_on_website'] === true) {
                    $sql .= " AND `plugin_courses_schedules`.`book_on_website` = 1";
                }

                if ($group_by_id)
                {
                    $sql .= " GROUP BY `plugin_courses_schedules`.`id` ";
                }
                else
                {
                    $sql .= " GROUP BY IF(plugin_courses_schedules.booking_type = 'Whole Schedule', CONCAT('s', plugin_courses_schedules.id), CONCAT('e', plugin_courses_schedules_events.id))";
                }

                $sql .= "ORDER BY
                `plugin_courses_schedules_events`.`datetime_start`";

                $schedules    = DB::query(
                    Database::SELECT, $sql
                )->execute()->as_array();

                $schedules_dates = array();
                $schedules_ids = array();
                foreach($schedules as $schedule) {
                    if ( ! in_array($schedule['id'], $schedules_ids)) {
                        $schedules_ids[] = $schedule['id'];
                    }
                }

                if ($nav_only && count($schedules) == 0) {
                    return false;
                }

                foreach($schedules_ids as $s_id) {
                    foreach ($schedules as $schedule)
					{
                        $return['data'][$i]['payment_option_selected'] =
                            ($schedule['allow_credit_card'] === '1' || $schedule['allow_purchase_order'] === '1' || $schedule['allow_sales_quote'] === '1')
                                ? true
                                : $return['data'][$i]['payment_option_selected'];

						if ($val['schedule_allow_price_override'] == 0 && $schedule['owned_by'] != null && $schedule['fee_amount'] == null) {
							$schedule['fee_amount'] = $val['schedule_fee_amount'];
							$schedule['fee_per'] = $val['schedule_fee_per'];
						}
						if ($timeslots) {
						    if (!empty($args['timeslot_order'])) {
                                $schedule['timeslots'] = DB::select('*')
                                    ->from('plugin_courses_schedules_events')
                                    ->where('schedule_id', '=', $schedule['id'])
                                    ->and_where('delete', '=', 0)
                                    ->and_where('publish', '=', 1)
                                    ->order_by('datetime_start', $args['timeslot_order'])
                                    ->execute()
                                    ->as_array();
                            } else {
                                $schedule['timeslots'] = DB::select('*')
                                    ->from('plugin_courses_schedules_events')
                                    ->where('schedule_id', '=', $schedule['id'])
                                    ->and_where('delete', '=', 0)
                                    ->and_where('publish', '=', 1)
                                    ->order_by('datetime_start', 'asc')
                                    ->execute()
                                    ->as_array();
                            }

						}
						$check_date = ($schedule['booking_type'] == 'Whole Schedule') ? $schedule['end_date'] : $schedule['start_date'];

                        if (!$group_by_id || $schedule['id'] == $s_id)
						{
                            switch ($course_website_display_setting) {
                                case 0: // All Date
                                    $schedules_dates[] = $schedule;
                                    break;
                                case 1: // Next Date
                                    if (($check_date >= $start && count($schedules_dates) < 1) || $schedule['learning_mode'] == 'self_paced') {
                                        $schedules_dates[] = $schedule;
                                    }
                                    break;
                                case 2: // Next 7 days
                                case 3: // Next 30 days
                                case 4: // Next 90 days
                                case 5: // Next 365 days
                                    if (($check_date >= $start && $schedule['start_date'] <= $end) || $schedule['learning_mode'] == 'self_paced') {
                                        $schedules_dates[] = $schedule;
                                    }
                                    break;
                            }
                        }
                    }
                    if (!$group_by_id) break;
                }
                $return['data'][$i]['schedules'] = $schedules_dates;


				$images = DB::select('image.*')
					->from(array('plugin_courses_courses_images', 'course_image'))
					->join(array('plugin_media_shared_media', 'image'), 'left')
					->on('course_image.image', '=', 'image.filename')
					->join(array('plugin_media_shared_media_photo_presets', 'preset'), 'left')
					->on('image.preset_id', '=', 'preset.id')
					->where('course_image.course_id', '=', $val['id'])
					->where('course_image.deleted', '=', 0)
					->where('preset.title', 'not like', '%banner%')
					->execute()
					->as_array();

				$banners = DB::select('image.*')
					->from(array('plugin_courses_courses_images', 'course_image'))
					->join(array('plugin_media_shared_media', 'image'), 'left')
					->on('course_image.image', '=', 'image.filename')
					->join(array('plugin_media_shared_media_photo_presets', 'preset'), 'left')
					->on('image.preset_id', '=', 'preset.id')
					->where('course_image.course_id', '=', $val['id'])
					->where('course_image.deleted', '=', 0)
					->where('preset.title', 'like', '%banner%')
					->execute()
					->as_array();


				$return['data'][$i]['images']  = $images;
				$return['data'][$i]['banners'] = $banners;

				$i++;
			}

			return $return['data']['0'];
		}
		else
		{
			return FALSE;
		}
	}

	public static function get_locations_and_levels_for_course($course_title, $location, $level)
	{
		//get locations ids
		$ids = DB::query(
			Database::SELECT, "SELECT
        `location_id`
        FROM
        `plugin_courses_schedules`
        LEFT JOIN
        `plugin_courses_courses`
        ON
        `plugin_courses_schedules`.`course_id` = `plugin_courses_courses`.`id`
        WHERE
        `plugin_courses_courses`.`title` like '%".Database::instance()->real_escape($course_title)."%'
        AND
        `plugin_courses_schedules`.`location_id` IS NOT NULL
        "
		)->execute()->as_array();
		$_add = "";
		if (is_array($ids) AND count($ids) > 0)
		{
			$_ids = array();
			foreach ($ids as $__key => $__value)
			{
				$_ids[] = $__value['location_id'];
			}
			$_sbk = implode(", ", $_ids);
			$_add .= " AND (`id` IN (".$_sbk.")) ";
		}

		//get locations
		$query = DB::query(
			Database::SELECT, "SELECT
        `id`,
        `name`
        FROM
        `plugin_courses_locations`
        WHERE
        `delete` = 0
        AND
        `publish` = 1
        ".$_add."
        ORDER BY
        `name`"
		)->execute()->as_array();
		$return['locations'] = "<option value=''>LOCATION</option>";
		if (is_array($query) AND count($query) > 0)
		{
			foreach ($query as $eloc => $vloc)
			{
				$return['locations'] .= "<option value='".$vloc['id']."'";
				if (isset($location) AND (int) $location > 0)
				{
					if ($location == $vloc['id'])
					{
						$return['locations'] .= " selected='selected'";
					}
				}
				$return['locations'] .= ">".$vloc['name']."</option>";
			}
		}
		//get levels
		$query2 = DB::query(
			Database::SELECT, "SELECT
        `id`,
        `level` as `name`
        FROM
        `plugin_courses_levels`
        WHERE
        `delete` = 0
        AND
        `publish` = 1
        AND
        `id` IN (SELECT `level_id` FROM `plugin_courses_courses` WHERE `title` like '%".Database::instance()->real_escape($course_title)."%')
        ORDER BY
        `name`"
		)->execute()->as_array();
		$return['levels'] = "<option value=''>LEVEL</option>";
		if (is_array($query2) AND count($query2) > 0)
		{
			foreach ($query2 as $elev => $vlev)
			{
				$return['levels'] .= "<option value='".$vlev['id']."'";
				if (isset($level) AND (int) $level > 0)
				{
					if ($level == $vlev['id'])
					{
						$return['levels'] .= " selected='selected'";
					}
				}
				$return['levels'] .= ">".$vlev['name']."</option>";
			}
		}
		$return['success'] = '1';

		return json_encode($return);
	}

	public static function get_booking_search_term($data)
	{
		$location = $data['location'];
		$category = $data['category'];
		$year = $data['year'];
		$term = $data['term'];
		$course_id = @$data['course_id'];
		$result = array();
		$q = DB::select(
            DB::expr('DISTINCT(`schedule`.`id`)'),
			array('course.title', 'course'), 'course.category_id', 'course.year_id',
			'schedule.course_id', array('schedule.name', 'schedule'), 'schedule.location_id', 'schedule.start_date',
			'category.category', DB::expr('GROUP_CONCAT(DISTINCT year.year) as `year`'), array('location.name', 'location'), array('subject.id','subject_id'),
            array('trainer.id', 'trainer_id'), array(DB::expr("CONCAT(`trainer`.`first_name`, ' ', `trainer`.`last_name`)"), 'trainer'),
			'schedule.fee_per', 'schedule.fee_amount'
		)
			->from(array('plugin_courses_courses',          'course'  ))
			->join(array('plugin_courses_categories',       'category'), 'LEFT')->on('course.category_id',   '=', 'category.id')
			->join(array('plugin_courses_courses_has_years','has_years'), 'LEFT')->on('course.id',          '=', 'has_years.course_id')
			->join(array('plugin_courses_years',            'year'    ), 'LEFT')->on('has_years.year_id',    '=', 'year.id')
			->join(array('plugin_courses_schedules',        'schedule'), 'LEFT')->on('schedule.course_id',   '=', 'course.id')
            ->join(array('plugin_courses_schedules_events', 'event'   ), 'LEFT')->on('schedule.id',          '=', 'event.schedule_id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE,   'trainer' ), 'LEFT')->on('event.trainer_id',     '=', 'trainer.id')
                                                                                ->on('trainer.delete',       '=', DB::expr("0"))
            ->join(array('plugin_courses_subjects',         'subject' ), 'LEFT')->on('course.subject_id',    '=', 'subject.id')
			->join(array('plugin_courses_locations',        'location'), 'LEFT')->on('schedule.location_id', '=', 'location.id');

		// Only get published schedules and courses
		$q
			->where('schedule.publish', '=', 1)
			->and_where('schedule.delete', '=', 0)
			->and_where('course.publish', '=', 1);
		
		if(@$data['ignore_fee'] == false) {
            $q->and_where_open()
                    ->or_where('schedule.is_fee_required', '<>', 1)
                    ->or_where('schedule.fee_amount', '>', 0)
                ->and_where_close();
        }


        if (empty($data['all_time'])) {
            // Only confirmed courses
            $q->where('schedule.is_confirmed', '=', 1);

            // Only schedules with timeslots in the future
            $q->where('event.datetime_start', '>' ,date("Y-m-d H:i:s"));
        }

		if (!empty($location))
		{
			$sublocations = Model_Locations::get_all_sublocation_ids($location, TRUE);
			$q->where('schedule.location_id', 'in', $sublocations);
		}

		if ($course_id) {
			$q->and_where('course.id', '=', $course_id);
		}

		if (!empty($category))
		{
			$q->where('course.category_id', '=', $category);
		}

		if (!empty($year))
		{
			$q->and_where_open();
				$q->or_where('has_years.year_id', '=', $year);
				$q->or_where('year.year', '=', 'All Levels');
			$q->and_where_close();
		}

		if (@$data['payment_type']) {
			$q->and_where('schedule.payment_type', '=', $data['payment_type']);
		}

		if (is_numeric(@$data['trainer_id'])) {
			$q->and_where('schedule.trainer_id', '=', $data['trainer_id']);
		}

		$q->where(DB::expr("CONCAT_WS(' - ', `course`.`id`, `category`.`category`, `location`.`name`, `schedule`.`id`, `schedule`.`name`, CONCAT(`trainer`.`first_name`, ' ', `trainer`.`last_name`))"), 'LIKE', '%'.$term.'%');

		$q->group_by('schedule.id');
		$q = $q->execute()->as_array();

		foreach ($q as $row)
		{
            $add_labal = (@$data['num_students_in_schedule'] == "true")
                ? " (" . count(Model_Schedules::get_students($row['id'])) . ")" : "";
            $result[] = array(
                'id'          => $row['id'],
                'category_id' => $row['category_id'],
                'location_id' => $row['location_id'],
                'year_id'     => $row['year_id'],
                'subject_id'  => $row['subject_id'],
                'value'       => "{$row['course_id']} - {$row['course']} - {$row['id']} - {$row['schedule']}",
                'label'       => "{$row['id']} - {$row['category']} - {$row['location']} - {$row['schedule']} - {$row['trainer']}" . $add_labal,
                'start_date'  => $row['start_date'],
                'fee_per'     => $row['fee_per'],
                'fee_amount'  => $row['fee_amount']
            );
		}

		return $result;
	}

	public static function get_locations_and_categories_for_course($course_title, $location, $category)
	{
		//get locations ids
		$ids = DB::query(
			Database::SELECT, "SELECT
        `location_id`
        FROM
        `plugin_courses_schedules`
        LEFT JOIN
        `plugin_courses_courses`
        ON
        `plugin_courses_schedules`.`course_id` = `plugin_courses_courses`.`id`
        WHERE
        `plugin_courses_courses`.`title` like '%".Database::instance()->real_escape($course_title)."%'
        AND
        `plugin_courses_schedules`.`location_id` IS NOT NULL
        "
		)->execute()->as_array();
		$_add = "";
		if (is_array($ids) AND count($ids) > 0)
		{
			$_ids = array();
			foreach ($ids as $__key => $__value)
			{
				$_ids[] = $__value['location_id'];
			}
			$_sbk = implode(", ", $_ids);
			$_add .= " AND (`id` IN (".$_sbk.")) ";
		}

		//get locations
		$query = DB::query(
			Database::SELECT, "SELECT
        `id`,
        `name`
        FROM
        `plugin_courses_locations`
        WHERE
        `delete` = 0
        AND
        `publish` = 1
        ".$_add."
        ORDER BY
        `name`"
		)->execute()->as_array();
		$return['locations'] = "<option value=''>LOCATION</option>";
		if (is_array($query) AND count($query) > 0)
		{
			foreach ($query as $eloc => $vloc)
			{
				$return['locations'] .= "<option value='".$vloc['id']."'";
				if (isset($location) AND (int) $location > 0)
				{
					if ($location == $vloc['id'])
					{
						$return['locations'] .= " selected='selected'";
					}
				}
				$return['locations'] .= ">".$vloc['name']."</option>";
			}
		}
		//get categories
		$query2 = DB::query(
			Database::SELECT, "SELECT
        `id`,
        `category` as `name`
        FROM
        `plugin_courses_categories`
        WHERE
        `delete` = 0
        AND
        `publish` = 1
        AND
        `id` IN (SELECT `category_id` FROM `plugin_courses_courses` WHERE `title` like '%".Database::instance()->real_escape($course_title)."%')
        ORDER BY
        `name`"
		)->execute()->as_array();
		$return['categories'] = "<option value=''>CATEGORY</option>";
		if (is_array($query2) AND count($query2) > 0)
		{
			foreach ($query2 as $elev => $vlev)
			{
				$return['categories'] .= "<option value='".$vlev['id']."'";
				if (isset($category) AND (int) $category > 0)
				{
					if ($category == $vlev['id'])
					{
						$return['categories'] .= " selected='selected'";
					}
				}
				$return['categories'] .= ">".$vlev['name']."</option>";
			}
		}
		$return['success'] = '1';

		return json_encode($return);
	}

	/***
	 * Purpose : To BUILD a view for the course listing by category
	 * @param $category_name
	 * @return View
	 */
	public static function get_front_list_by_category($category_name)
	{
		//get category id from DB
		$category_id = Model_Categories::get_id($category_name);

		//get list of courses to pass to the course list view
		$list = Model_Courses::get_courses_by_category_id($category_id);

		$view = View::factory(
			'front_end/courses_list_for_category',
			array('list' => $list
			)
		);

		return $view;
	}

	/***
	 * Purpose : to GET a list of courses based on a category ID
	 * @param $category_id
	 * @return mixed
	 */
	public static function get_courses_by_category_id($category_id)
	{
		$data = DB::query(Database::SELECT,
			"SELECT * FROM `plugin_courses_courses` WHERE `deleted` = 0 AND `publish` = 1 AND `category_id` = ".$category_id." order by `title`")
			->execute()
			->as_array();

		return $data;
	}
    
    public static function get_course_by_subject_id($id)
    {
        return DB::select(array('course.id', 'id'), array('course.title', 'value'), array('course.type_id', 'type'))
            ->from(array(Model_Subjects::TABLE_SUBJECTS, 'subject'))
            ->join(array('plugin_courses_courses', 'course'), 'inner')->on('course.subject_id', '=', 'subject.id')
            ->where('subject.id', '=', $id)->execute()->as_array();
    }

	public static function get_course_image($course_id)
	{
		$query = DB::select('image')->from('plugin_courses_courses_images')->where('course_id', '=', $course_id)->order_by('date_created', 'DESC')->limit(1)->execute()->as_array();
		if (count($query) == 1)
		{
			return $query[0]['image'];
		}
		else
		{
			return 'Education.jpg';
		}
	}

	public static function get_courses_based_on_location()
	{
		$q = DB::select('id', 'name')->from('plugin_courses_locations')->where('delete', '=', 0)->execute()->as_array();
		$result = array();
		foreach ($q AS $location => $key)
		{
			$r = DB::select('plugin_courses_courses.id', 'plugin_courses_courses.title')->from('plugin_courses_courses')
				->join('plugin_courses_schedules', 'LEFT')
				->on('plugin_courses_courses.id', '=', 'plugin_courses_schedules.course_id')
				->where('plugin_courses_courses.deleted', '=', '0')
				->and_where('plugin_courses_courses.publish', '=', '1')
				->and_where('plugin_courses_schedules.publish', '=', '1')
				->and_where('plugin_courses_schedules.delete', '=', '0')
				->and_where('plugin_courses_schedules.location_id', '=', $key['id'])
				->execute()
				->as_array();
			$result[$key['name']][] = $r;
		}
		return $result;
	}

	public static function get_courses_based_on_year()
	{
		$q = DB::select('id', 'year')->from('plugin_courses_years')->where('delete', '=', '0')->and_where('publish', '=', '1')->execute()->as_array();
		$result = array();
		foreach ($q AS $year => $key)
		{
			$r = DB::select('plugin_courses_courses.id', 'plugin_courses_courses.title')->from('plugin_courses_courses')
					->join('plugin_courses_courses_has_years', 'inner')
						->on('plugin_courses_courses_has_years.course_id', '=', 'plugin_courses_courses.id')
				->where('plugin_courses_courses.deleted', '=', '0')
				->and_where('plugin_courses_courses.publish', '=', '1')
				->and_where('plugin_courses_courses_has_years.year_id', '=', $key['id'])
				->execute()
				->as_array();
			$result[$key['year']][] = $r;
		}
		return $result;
	}

    public static function get_year($id)
    {
        $data = DB::select()->from('plugin_courses_years')->where('id', '=', $id)->execute()->as_array();

        return isset($data[0]) ? $data[0] : false;
    }

	public static function get_courses_based_on_category()
	{
		$q = DB::select('id', 'category')->from('plugin_courses_categories')->where('delete', '=', '0')->and_where('publish', '=', '1')->execute()->as_array();
		$result = array();
		foreach ($q AS $category => $key)
		{
			$r = DB::select('plugin_courses_courses.id', 'plugin_courses_courses.title')->from('plugin_courses_courses')
				->join('plugin_courses_categories', 'LEFT')
				->on('plugin_courses_categories.id', '=', 'plugin_courses_courses.category_id')
				->join('plugin_courses_courses_has_years', 'inner')
					->on('plugin_courses_courses_has_years.course_id', '=', 'plugin_courses_courses.id')
				->where('plugin_courses_courses.deleted', '=', '0')
				->and_where('plugin_courses_courses.publish', '=', '1')
				->and_where('plugin_courses_courses_has_years.year_id', '=', $key['id'])
				->execute()
				->as_array();
			$result[$key['category']][] = $r;
		}
		return $result;
	}

	public static function get_course_details_by_schedule($schedule_id)
	{
		$result = DB::select('c.id', 'c.title', 'c1.category', 'c.summary', 'c.description', array(DB::expr("CONCAT(s.id,' - ',s.name)"), 'schedule'), array('c2.name', 'subject'))
			->from(array('plugin_courses_schedules', 's'))
			->join(array('plugin_courses_courses', 'c'), 'LEFT')
			->on('s.course_id', '=', 'c.id')
			->join(array('plugin_courses_categories', 'c1'), 'LEFT')
			->on('c.category_id', '=', 'c1.id')
			->join(array('plugin_courses_subjects', 'c2'), 'LEFT')
			->on('c.subject_id', '=', 'c2.id')
			->where('s.id', '=', $schedule_id)
			->execute()
			->as_array();
//        $q = DB::select('t1.title','t1.summary','t1.description')->from(array('plugin_courses_courses','t1'))->join(array('plugin_courses_schedules','t2'),'LEFT')->on('t2.course_id','=','t1.id')->where('t2.id','=',$schedule_id)->execute()->as_array();
		return count($result) > 0 ? $result[0] : array('title' => '', 'summary' => '', 'description' => '', 'category' => '', 'schedule' => '', 'subject' => '', 'id' => '');
	}

    public static function get_finder_modes($selected = null)
    {
        // Until we can dedicate a spot in the database for this
        $finder_modes = array(
            'none'             => __('None'),
            'event_promoter'   => __('Event promoter'),
            'secondary_school' => __('Secondary school'),
            'training_company' => __('Training company')
        );

        $options = '<option value="">'.__('Please select').'</option>';
        foreach ($finder_modes as $key => $name) {
            $options .= '<option value="'.$key.'"'.($key == $selected ? ' selected="selected"' : '').'>'.$name.'</option>';
        }

        return $options;
    }

    public static function finder_mode_settings($mode)
    {
        $provider_ids = Model_Providers::get_providers_for_host();

        switch ($mode) {

            case 'event_promoter':
                $search = Model_Event::get_for_global_search(array(
                    'direction' => 'asc',
                    'group_by' => 'events.id',
                    'whole_site' => false)
                );
                $fields = array(
                    'event'    => array(
                        'type'        => 'event',
                        'label'       => __('Event name'),
                        'placeholder' => __('Search'),
                        'type_search' => '#event-drilldown-event-list',
                        'required'    => false,
                        'columns'     => array(
                            array(
                                'type'           => 'event',
                                'label'          => __('Pick an event'),
                                'items'          => $search['all_data'],
                                'items_name_key' => 'title'
                            ),
                            array(
                                'type'           => 'county',
                                'label'          => __('Pick a location'),
                                'all_text'       => __('All locations'),
                                'items'          => Model_Event_Venue::get_active_counties(),
                            )
                        )
                    ),
                    'county' => array(
                        'type'           => 'county',
                        'label'          => __('Location'),
                        'placeholder'    => __('Search'),
                        'type_search'    => '#event-drilldown-county-list',
                        'required'       => false,
                        'use_columns'    => 'event' // Use same columns as the "event" field.
                    ),
                );
                break;

            case 'training_company':
                $locations = Model_Locations::get_locations_without_parent();
                $location_ids = array_column($locations, 'id');
                $courses = self::filter(['location_ids' => $location_ids])['all_data'];

                $fields = array(
                    'location' => array(
                        'type'        => 'location',
                        'label'       => __('Location'),
                        'placeholder' => empty($locations) ? __('Search') : __('e.g. $1', ['$1' => $locations[array_rand($locations)]['name']]),
                        'type_search' => '#location-drilldown-location-list',
                        'required'    => true,
                        'columns'     => array(
                            array(
                                'type'           => 'location',
                                'label'          => __('Pick a location'),
                                'all_text'       => __('All locations'),
                                'items'          => $locations,
                            ),
                            array(
                                'type'           => 'course',
                                'label'          => __('Pick a Course'),
                                'filtered_by'    => '#location-drilldown-location-list',
                                'relies_on_text' => __('Select a location first.')
                            )
                        )
                    ),
                    'course' => array(
                        'type'        => 'course',
                        'label'       => (!in_array(__('finder/Subject'), ['', 'finder/Subject'])) ? __('finder/Subject') : __('Subject'),
                        'placeholder' => empty($courses) ? __('Search') : __('e.g. $1', ['$1' => $courses[array_rand($courses)]['title']]),
                        'type_search' => '#location-drilldown-location-list',
                        'required'    => true,
                        'use_columns' => 'location' // Use the same columns as the "location" field.
                    )
                );
                break;

            default:
                $locations = Model_Locations::get_locations_without_parent(null, $provider_ids);
                $location_ids = array_column($locations, 'id');
                $subjects = Model_Subjects::get_all_subjects(['location_ids' => $location_ids]);

                $fields = array(
                    'location' => array(
                        'type'        => 'location',
                        'label'       => __('Location'),
                        'placeholder' => empty($locations) ? __('Search') : __('e.g. $1', ['$1' => $locations[array_rand($locations)]['name']]),
                        'type_search' => '#location-drilldown-location-list',
                        'required'    => true,
                        'columns'     => array(
                            array(
                                'type'           => 'location',
                                'label'          => __('Pick a location'),
                                'all_text'       => __('All locations'),
                                'items'          => $locations,
                            )
                        )
                    ),
                    'subject' => array(
                        'type'        => 'subject',
                        'label'       => (!in_array(__('finder/Subject'), ['', 'finder/Subject'])) ? __('finder/Subject') : __('Subject'),
                        'placeholder' => empty($subjects) ? __('Search') : __('e.g. $1', ['$1' => $subjects[array_rand($subjects)]['name']]),
                        'type_search' => '#subject-drilldown-subject-list',
                        'required'    => false,
                        'columns'     => array(
                            array(
                                'type'           => 'year',
                                'label'          => __('Year'),
                                'items'          => Model_Years::get_all_years(),
                                'items_name_key' => 'year'
                            ),
                            array(
                                'type'           => 'category',
                                'label'          => __('Course Type'),
                                'filtered_by'    => '#subject-drilldown-year-list',
                                'relies_on_text' => __('Select a year first.'),
                                'items'          => Model_Categories::get_all_published_categories(['include_empty' => false]),
                                'items_name_key' => 'category'
                            ),
                            array(
                                'type'           => 'subject',
                                'label'          => __('Subject'),
                                'filtered_by'    => '#subject-drilldown-category-list',
                                'relies_on_text' => __('Select a course first.')
                            )
                        )
                    )
                );
                break;
        }

        return $fields;

    }

	public static function autocomplete_search_schedules($term, $trainer_id = null, $long_title = true, $from_now = true, $course_id = null)
	{
		$select = DB::select(
				DB::expr("DISTINCT schedules.id as value"),
                $long_title ?
                    DB::expr("CONCAT_WS(' ', '#', schedules.id, ' - ', schedules.name, ' - ' , trainers.first_name, trainers.last_name, ' - ', DATE_FORMAT(min(events.datetime_start), '%a %H:%i')) AS label")
                    :
                    DB::expr("schedules.name AS label"),
				'schedules.location_id',
				'schedules.trainer_id',
				DB::expr("CONCAT_WS(' ', trainers.first_name, trainers.last_name) AS trainer"),
				array('locations.name', 'location')
		)
				->from(array('plugin_courses_schedules', 'schedules'))
				->join(array('plugin_courses_schedules_events', 'events'), 'left')
					->on('schedules.id', '=', 'events.schedule_id')
					->on('events.delete', '=', DB::expr(0))
				->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id');
		if ($term != '') {
			$select->and_where_open();
			$select->or_where('schedules.name', 'like', '%' . $term . '%');
			$select->or_where(DB::expr("CONCAT_WS(' ', trainers.first_name, trainers.last_name)"), 'like', '%' . $term . '%');
			$select->and_where_close();
		}
		if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
			$select->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'left')->on('schedules.trainer_id', '=', 'trainers.id');
		} else {
			$select->join(array(Model_Contacts::TABLE_CONTACT, 'trainers'), 'left')->on('schedules.trainer_id', '=', 'trainers.id');
		}

        if ($trainer_id) {
			$select->and_where_open()
            	->or_where('schedules.trainer_id', '=', $trainer_id)
					->or_where('events.trainer_id', '=', $trainer_id)
				->and_where_close();
        }

		if ($from_now) {
			$select->and_where('events.datetime_start', '>=', date::now());
		}
		if ($course_id) {
			$select->and_where('schedules.course_id', '=', $course_id);
		}
		$q = $select->and_where('schedules.delete', '=', 0)
                ->group_by('schedules.id');
		return $q->execute()->as_array();
	}

	public static function autocomplete_search_schedule_events($schedule_id, $include_event_id = null)
	{
		$q = DB::select(
				array("events.id", "value"),
				DB::expr("events.datetime_start AS label")
		)
				->from(array('plugin_courses_schedules_events', 'events'))
				->where('schedule_id', '=', $schedule_id)
				->and_where('delete', '=', 0);
		if ($include_event_id) {
			$q->and_where_open();
			$q->or_where('datetime_start', '>=', date('Y-m-d H:i:s'));
			$q->or_where('id', '=', $include_event_id);
			$q->and_where_close();
		} else {
			$q->and_where('datetime_start', '>=', date('Y-m-d H:i:s'));
		}
		return $q->execute()->as_array();
	}

	private static function get_list_by_category($category_id)
	{
		$query = DB::query(
			Database::SELECT, "SELECT `plugin_courses_courses`.*,
                    `plugin_courses_categories`.`category`,
                    `plugin_courses_years`.`year`,
                    `plugin_courses_types`.`type`,
                    GROUP_CONCAT(`plugin_courses_providers`.name) as provider`
             FROM `plugin_courses_courses`
             LEFT JOIN
              `plugin_courses_categories`
              ON
                `plugin_courses_categories`.`id` = `plugin_courses_courses`.`category_id`
             LEFT JOIN
              `plugin_courses_years`
              ON
                `plugin_courses_years`.`id` = `plugin_courses_courses`.`year_id`
             LEFT JOIN
              `plugin_courses_types`
              ON
                `plugin_courses_types`.`id` = `plugin_courses_courses`.`type_id`
             LEFT JOIN
              plugin_courses_courses_has_providers
              ON plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
             LEFT JOIN
              `plugin_courses_providers`
              ON
                `plugin_courses_providers`.`id` = `plugin_courses_courses_has_providers`.`provider_id`
             WHERE
                `plugin_courses_courses`.`deleted` = 0
              AND
                `plugins_courses_courses`.`category_id` = ".$category_id." ORDER BY `plugins_courses_courses`.`id`"
		)->execute()->as_array();

		return $query;
	}

    public static function account_managed_course_bookings_redirect()
    {
        if (Settings::instance()->get('account_managed_course_bookings') == 1) {
            Request::$current->redirect('/available-results.html');
        }
    }

    public static function get_course_title_from_code($code, $args = [])
    {
        $q = DB::select('title')
            ->from(self::TABLE_COURSES)
            ->where('code', '=', $code)
            ->where('deleted', '=', 0);

        if (!empty($args['publish'])) {
            $q->where('publish', '=', $args['publish']);
        }

        $title = $q  ->execute()->get('title');
        return $title;
    }

    public static function get_course_list_code($args = [])
    {
        $q = DB::select('code', 'title')
            ->from(self::TABLE_COURSES)
            ->where('deleted', '=', 0);

        if (!empty($args['publish'])) {
            $q->where('publish', '=', $args['publish']);
        }

        $list = $q
            ->order_by('title')
            ->execute()
            ->as_array();

        $courses = array();
        foreach ($list as $course) {
            $courses[$course['code']] = $course['code'] . ' - ' . $course['title'];
        }
        return $courses;
    }

    public static function add_course_selector_to_form($html, $course_id)
    {
        $course_id = Kohana::sanitize($course_id);
        $course    = @Model_Courses::get_course($course_id);

        if (isset($course['id'])) {
            // Get list of courses that appear on the front and build a dropdown
            $courses = Model_Courses::filter([]);
            $options = ['' => __('Please select')];
            foreach ($courses['all_data'] as $course) {
                $options[$course['id']] = !empty($course['code']) ? $course['code'].': '.$course['title'] : $course['title'];
            }
            $course_dropdown = '<li class="contact_form-interested_in_course_id-li">
                    <label for="contact_form-interested_in_course_id">Interested in</label>
                    <label class="form-select clear">
                        <select class="form-input" name="interested_in_course_id" id="contact_form-interested_in_course_id" disabled="disabled">'.html::optionsFromArray($options, $course_id).'</select>
                        <input type="hidden" name="interested_in_course_id" value="'.$course_id.'" />
                    </label>
                </li>';

            // Load the page HTML as a DOMDocument object
            $dom = new DOMDocument();
            $dom->loadHTML($html);

            // Create a fragment for the HTML for the dropdown
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML($course_dropdown);

            // Target the list inside the formbuilder form
            $xpath = new DOMXPath($dom);
            $form_list = $xpath->query('//form[@action="frontend/formprocessor/"]/ul')->item(0);
            $first_list_item = $xpath->query('//form[@action="frontend/formprocessor/"]/ul/li')->item(0);

            // Add the dropdown before the first item in the form
            if ($form_list) {
                $form_list->insertBefore($fragment, $first_list_item);

                $html = $dom->saveHTML();
            }
        }

        return $html;
    }

    public static function add_schedule_to_form($html, $schedule_id)
    {
        $schedule_id = Kohana::sanitize($schedule_id);
        $schedule    = @Model_Schedules::get_schedule($schedule_id);

        if (isset($schedule['id'])) {
            $field = '<li class="contact_form-interested_in_schedule-li">
                    <label for="contact_form-interested_in_schedule">Interested in</label>
                    <input type="text" id="contact_form-interested_in_schedule" readonly="readonly" disabled="disabled" value="Schedule #'.$schedule['id'].': '.htmlspecialchars($schedule['name']).'" />
                    <input type="hidden" name="interested_in_schedule_id" value="'.$schedule['id'].'" />
                </li>';

            // Load the page HTML as a DOMDocument object
            $dom = new DOMDocument();
            $dom->loadHTML($html);

            // Create a fragment for the HTML for the dropdown
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML($field);

            // Target the list inside the formbuilder form
            $xpath = new DOMXPath($dom);
            $form_list = $xpath->query('//form[@action="frontend/formprocessor/"]/ul')->item(0);
            $first_list_item = $xpath->query('//form[@action="frontend/formprocessor/"]/ul/li')->item(0);

            // Add the dropdown before the first item in the form
            if ($form_list) {
                $form_list->insertBefore($fragment, $first_list_item);
                $html = $dom->saveHTML();
            }
        }

        return $html;
    }

    public static function get_for_eventcalendar()
    {
        $results = [];

        if (Settings::instance()->get('courses_in_calendar')) {
            $timetable_schedules_q = DB::select(
                'plugin_courses_schedules_events.schedule_id',
                array('plugin_courses_schedules_events.datetime_start','start_date'),
                'plugin_courses_schedules.course_id',
                array('plugin_courses_locations.name', 'location'),
                'plugin_courses_schedules.name',
                'plugin_courses_courses.title',
                'plugin_courses_schedules.start_date_only'
            )
                ->from('plugin_courses_schedules_events')
                ->distinct(true)
                ->join('plugin_courses_schedules','LEFT')->on('plugin_courses_schedules.id','=','plugin_courses_schedules_events.schedule_id')
                ->join('plugin_courses_locations','LEFT')->on('plugin_courses_schedules.location_id','=','plugin_courses_locations.id')
                ->join('plugin_courses_courses','LEFT')->on('plugin_courses_schedules.course_id','=','plugin_courses_courses.id')
                ->join('plugin_courses_categories','LEFT')->on('plugin_courses_courses.category_id','=','plugin_courses_categories.id')
                ->where('plugin_courses_schedules_events.publish','=','1')
                ->and_where('plugin_courses_schedules_events.delete','=','0')
                ->and_where('plugin_courses_locations.publish', '=', 1)
                ->and_where('plugin_courses_schedules.publish', '=', 1)
                ->and_where('plugin_courses_schedules.delete', '=', 0)
                ->and_where('plugin_courses_categories.display_in_calendar', '=', 1)
                ->order_by('plugin_courses_schedules_events.datetime_start', 'ASC');

            $provider_ids = Model_Providers::get_providers_for_host();
            if ($provider_ids) {
                $timetable_schedules_q
                    ->join(array(Model_Courses::TABLE_HAS_PROVIDERS, 'has_providers'), 'inner')
                    ->on('plugin_courses_courses.id', '=', 'has_providers.course_id')
                    ->where('has_providers.provider_id', 'in', $provider_ids);
            }

            $timetable_schedules = $timetable_schedules_q->execute()->as_array();

            $ignore = array();
            foreach($timetable_schedules AS $element) {
                if (!in_array($element['schedule_id'], $ignore)) {
                    $results[] = [
                        'date' => $element['start_date'],
                        'type' => 'Schedule',
                        'title' => $element['title'],
                        'location' => $element['location'],
                        'url' => URL::site().'course-detail/'.urlencode($element['title']).'.html/?id='.$element['course_id']
                    ];
                }

                // If the schedule has been set to only show the first date, ignore any further dates in that schedule
                if ($element['start_date_only'] == 1) {
                    $ignore [] = $element['schedule_id'];
                }
            }

        }

        return $results;
    }

    public static function update_schedule_status($schedule_id = false)
    {
        try {
            DB::query(null, "SELECT GET_LOCK('all_set_inprogress_completed', 300)")->execute();
            Database::instance()->begin();

            DB::query(null, "DROP TEMPORARY TABLE IF EXISTS first_schedule_event_dates")->execute();
            DB::query(null, "CREATE TEMPORARY TABLE first_schedule_event_dates (schedule_id INT, dt DATETIME) ENGINE=MEMORY")->execute();
            $first_schedule_dates_sql = "INSERT INTO first_schedule_event_dates
                (SELECT 
                schedules.id, IF(schedules.start_date > MIN(schedule_events.datetime_start),
                    schedules.start_date,
                    MIN(schedule_events.datetime_start)) AS first_dt
                FROM
                plugin_courses_schedules schedules
                    INNER JOIN
                plugin_courses_schedules_status schedule_status ON schedules.schedule_status = schedule_status.id
                    INNER JOIN
                plugin_courses_schedules_events schedule_events ON schedules.id = schedule_events.schedule_id 
                    WHERE
                schedule_events.`delete` = 0 and schedules.`delete` = 0";
            if(is_numeric($schedule_id)) {
                $first_schedule_dates_sql .= " and schedules.id = {$schedule_id} ";
            }
            $first_schedule_dates_sql .= " GROUP BY schedules.id)";
            DB::query(null, $first_schedule_dates_sql)->execute();

            // If Schedule timeslot is in past then set to in progress
            DB::query(null, "UPDATE plugin_courses_schedules `schedules`
                    INNER JOIN
                plugin_courses_schedules_status schedules_status ON schedules.schedule_status = schedules_status.id
                    INNER JOIN
                first_schedule_event_dates ON schedules.id = first_schedule_event_dates.schedule_id 
                    SET 
                schedules.schedule_status = 
                    (SELECT 
                        id
                    FROM
                        plugin_courses_schedules_status
                    WHERE
                        title = 'In Progress')
                    WHERE
                schedules_status.title = 'Confirmed'
                AND first_schedule_event_dates.dt <= NOW()
                AND schedules.schedule_status <> (SELECT 
                        id
                    FROM
                        plugin_courses_schedules_status
                    WHERE
                        title = 'Cancelled')")->execute();

            // If Schedule is in progress and last timeslot is passed then set to completed
            DB::query(null, "DROP TEMPORARY TABLE IF EXISTS last_schedule_event_dates")->execute();
            DB::query(null, "CREATE TEMPORARY TABLE last_schedule_event_dates (schedule_id INT, dt DATETIME) ENGINE=MEMORY")->execute();
            $last_schedule_dates_sql = "INSERT INTO last_schedule_event_dates
                (SELECT 
                schedules.id, MAX(schedule_events.datetime_start) AS last_dt
                FROM
                plugin_courses_schedules schedules
                    INNER JOIN
                plugin_courses_schedules_status schedule_status ON schedules.schedule_status = schedule_status.id
                    INNER JOIN
                plugin_courses_schedules_events schedule_events ON schedules.id = schedule_events.schedule_id 
                    WHERE
                schedule_events.`delete` = 0 and schedules.`delete` = 0";
            if(true) {
                $last_schedule_dates_sql .= " and schedules.id = {$schedule_id} ";
            }
            $last_schedule_dates_sql .= " GROUP BY schedules.id) ";

            DB::query(null, "INSERT INTO last_schedule_event_dates
                (SELECT 
                schedules.id, MAX(schedule_events.datetime_start) AS last_dt
                FROM
                plugin_courses_schedules schedules
                    INNER JOIN
                plugin_courses_schedules_status schedule_status ON schedules.schedule_status = schedule_status.id
                    INNER JOIN
                plugin_courses_schedules_events schedule_events ON schedules.id = schedule_events.schedule_id 
                    WHERE
                schedule_events.`delete` = 0 and schedules.`delete` = 0
                GROUP BY schedules.id)")->execute();


            DB::query(null, "UPDATE plugin_courses_schedules `schedules`
                    INNER JOIN
                plugin_courses_schedules_status schedules_status ON schedules.schedule_status = schedules_status.id
                    INNER JOIN
                last_schedule_event_dates ON schedules.id = last_schedule_event_dates.schedule_id 
                    SET 
                schedules.schedule_status = 
                    (SELECT 
                        id
                    FROM
                        plugin_courses_schedules_status
                    WHERE
                        title = 'Completed')
                    WHERE
                schedules_status.title = 'In Progress'
                AND last_schedule_event_dates.dt <= NOW()
                AND schedules.schedule_status <> (SELECT 
                        id
                    FROM
                        plugin_courses_schedules_status
                    WHERE
                        title = 'Cancelled')")->execute();

            Database::instance()->commit();

            DB::query(null, "SELECT RELEASE_LOCK('all_set_inprogress_completed')")->execute();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            DB::query(null, "SELECT RELEASE_LOCK('all_set_inprogress_completed')")->execute();
            throw $exc;
        }
    }

}
