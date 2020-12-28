<?php defined('SYSPATH') or die('No direct script access.');

class Model_Schedules extends Model
{
    const TABLE_SCHEDULES = 'plugin_courses_schedules';
    const TABLE_TIMESLOTS = 'plugin_courses_schedules_events';
    const TABLE_HAS_COURSES = 'plugin_courses_schedules_has_courses';

	const HAS_ENGINE_EVENTS = 'plugin_courses_schedules_has_engine_calendar_events';
    const TABLE_HAS_PAYMENTOPTIONS = 'plugin_courses_schedules_has_paymentoptions';

    CONST CONFIRMED                 = 1;
    CONST CANCELLED                 = 2;
    CONST IN_PROGRESS               = 3;
    CONST COMPLETED                 = 4;

	public static function count_schedules($search = FALSE)
	{
		$_search = '';
		if ($search)
		{
			$_search = " AND ("
				."`plugin_courses_schedules`.`name` like '%".$search."%'"
				." OR `plugin_courses_locations`.`name` LIKE '%".$search."%'"
				." OR `duration` like '%".$search."%'"
				." OR `start_date` like '%".$search."%'"
				." OR `end_date` like '%".$search."%'"
				." OR `min_capacity` like '%".$search."%'"
				." OR `max_capacity` like '%".$search."%'"
				." OR `fee_amount` like '%".$search."%'"
				.")";
		}
		$query = DB::query(Database::SELECT,
			"SELECT count(*) as `count` FROM `plugin_courses_schedules`
            	LEFT JOIN `plugin_courses_courses` ON `plugin_courses_schedules`.`course_id` = `plugin_courses_courses`.`id`
            	LEFT JOIN `plugin_courses_locations` ON `plugin_courses_schedules`.`location_id` = `plugin_courses_locations`.`id`
             WHERE `plugin_courses_schedules`.`delete` = 0".$_search.";")
			->execute()
			->as_array();
		return $query['0']['count'];
	}

	public static function get_schedules($limit, $offset, $sort, $dir, $search = FALSE, $filters)
	{
		DB::query(null, "drop temporary table if exists tmp_schedule_availability")->execute();
		DB::query(null, "create temporary table tmp_schedule_availability (schedule_id INT PRIMARY KEY, capacity INT, bookings INT)")->execute();
		DB::query(null, "insert into tmp_schedule_availability (select s.id, s.max_capacity as capacity, SUM(IF(hs.schedule_id, 1, 0)) as cnt from plugin_courses_schedules s
	left join plugin_ib_educate_booking_has_schedules hs on s.id = hs.schedule_id and hs.deleted = 0 and hs.booking_status <> 3
	left join plugin_ib_educate_bookings b on hs.booking_id = b.booking_id and b.`delete` = 0 and b.booking_status <> 3
	where s.`delete` = 0 and s.booking_type = 'Whole Schedule'
	group by s.id)")->execute();
		DB::query(null, "insert into tmp_schedule_availability (select s.id, capacity.capacity, SUM(IF(i.booking_item_id and b.delete = '0',1,0)) as cnt from plugin_courses_schedules s
	inner join plugin_courses_schedules_events t on s.id = t.schedule_id
	inner join (
								select s.id, sum(if(t.max_capacity, t.max_capacity, s.max_capacity)) as capacity from plugin_courses_schedules s
									inner join plugin_courses_schedules_events t on s.id = t.schedule_id
									where s.`delete` = 0 and t.`delete` = 0 and s.booking_type = 'One Timeslot'
							group by s.id
						) capacity on s.id = capacity.id
	left join plugin_ib_educate_booking_items i on t.id = i.period_id and i.booking_status <> 3 and i.`delete` = 0
	left join plugin_ib_educate_bookings b on i.booking_id = b.booking_id and b.booking_status <> 3 and b.`delete` = 0

	where s.`delete` = 0 and t.`delete` = 0 and s.booking_type = 'One Timeslot'
	group by s.id)")->execute();

		$columns   = array();
		$columns[] = '`plugin_courses_schedules`.`id`';
		$columns[] = '`plugin_courses_courses`.`title`';
		$columns[] = '`plugin_courses_schedules`.`name`';
		$columns[] = '`plugin_courses_categories`.`category`';
		$columns[] = '`plugin_courses_schedules`.`fee_amount`';
		$columns[] = '`plugin_courses_locations`.`name`';
        $columns[] = '`plugin_courses_schedules_status`.`title`';
		$columns[] = '`plugin_courses_schedules`.`start_date`';
		$columns[] = '`plugin_courses_repeat`.`name`';
		$columns[] = NULL; // Times would require having
		$columns[] = "CONCAT_WS(`contact`.`first_name`,' ',`contact`.`last_name`)";
		$columns[] = NULL; // Is confirmed
		$columns[] = '`plugin_courses_schedules`.`date_modified`';
		$columns[] = '`plugin_courses_schedules`.`amendable`';
		$columns[] = 'tmp_schedule_availability.capacity';
		$columns[] = 'tmp_schedule_availability.bookings';
        $sanitized_input = array();

        $_search = '';
        if ($search) {
            $_search .= " AND (";
            for ($i = 0; $i < count($columns); $i++) {
                if(isset($columns[$i])) {
                    $_search .= ($i != 0) ? " OR " : "";
                    $_search .= "REPLACE(REPLACE({$columns[$i]}, '-', ''), ' ', '') LIKE :search_{$i}";
                    $sanitized_input[":search_{$i}"] = str_replace(' ', '', str_replace('-', '', "%$search%"));
                }
            }
            $_search .= ")";
        }
		$_limit = ($limit > -1) ? ' LIMIT '.$offset.','.$limit : '';
		$select = "SELECT SQL_CALC_FOUND_ROWS `plugin_courses_schedules`.`id`,
            `plugin_courses_schedules`.*,
            `plugin_courses_courses`.`title` as `course`,
            `plugin_courses_schedules_status`.`title` as `status_label`,
            `plugin_courses_categories`.`category` as `category`,
            CONCAT_WS ('&nbsp;/&nbsp;', plocation.`name`, `plugin_courses_locations`.`name`) as `location`,
            CONCAT_WS (' ', `contact`.`first_name`, `contact`.`last_name`) as `trainer`,
            `plugin_courses_repeat`.`name` as `repeat_name`,
            tmp_schedule_availability.capacity,
            tmp_schedule_availability.bookings
            FROM `plugin_courses_schedules`
                LEFT JOIN `plugin_courses_schedules_status` ON `plugin_courses_schedules`.`schedule_status` = `plugin_courses_schedules_status`.`id`
                LEFT JOIN `plugin_courses_courses` ON `plugin_courses_schedules`.`course_id` = `plugin_courses_courses`.`id`
                LEFT JOIN `plugin_courses_locations` ON `plugin_courses_schedules`.`location_id` = `plugin_courses_locations`.`id`
                LEFT JOIN `plugin_courses_locations` plocation ON `plugin_courses_locations`.`parent_id` = `plocation`.`id`
                LEFT JOIN `plugin_courses_categories` ON `plugin_courses_courses`.`category_id` = `plugin_courses_categories`.`id`
                LEFT JOIN `plugin_courses_repeat` ON `plugin_courses_schedules`.`repeat` = `plugin_courses_repeat`.`id`
                LEFT JOIN tmp_schedule_availability ON tmp_schedule_availability.schedule_id = plugin_courses_schedules.id ";
		if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
		{
			$select .= "LEFT JOIN `plugin_contacts3_contacts` `contact` ON `plugin_courses_schedules`.`trainer_id` = `contact`.`id`
                WHERE ";
		}
		else
		{
			$select .= "LEFT JOIN `plugin_contacts_contact` `contact` ON `plugin_courses_schedules`.`trainer_id` = `contact`.`id` WHERE ";
		}

        for ($i = 0; $i < count($columns); $i++) {
            if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $filters['sSearch_' . $i] != '' AND $columns[$i] != '') {
                $select .= "REPLACE(REPLACE(".$columns[$i] . ", '-', ''), ' ', '') LIKE :column_filter_search_{$i} AND ";
                $sanitized_input[":column_filter_search_{$i}"] = str_replace(' ', '', str_replace('-', '', "%{$filters['sSearch_' . $i]}%"));
            }
        }
		
		if (is_numeric(@$filters['course_id']))
		{
			$select .= "`plugin_courses_schedules`.`course_id`"." = ".$filters['course_id']." AND ";
		}

		if (is_numeric(@$filters['course_id']))
		{
			$select .= "`plugin_courses_schedules`.`course_id`"." = ".$filters['course_id']." AND ";
		}

		if (is_numeric(@$filters['owned_by'])) {
			$select .= " plugin_courses_schedules.owned_by = " . $filters['owned_by'] . " AND ";
		}

		$query = DB::query(Database::SELECT, $select."`plugin_courses_schedules`.`delete` = 0".$_search."
            ORDER BY ".$sort." ".$dir." ".$_limit);

        foreach ($sanitized_input as $key => $value) {
            $query->param($key, $value);
        }
		$query = $query->execute()->as_array();

		$output['iTotalRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$return                  = array();
		if (count($query) > 0)
		{
			$i = 0;
			foreach ($query as $elem => $sub)
			{
				$course_titles = DB::select(DB::expr('GROUP_CONCAT(courses.title) as titles'))
					->from(array(self::TABLE_HAS_COURSES, 'has_courses'))
						->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
							->on('has_courses.course_id', '=', 'courses.id')
					->where('has_courses.schedule_id', '=', $sub['id'])
					->execute()
					->get('titles');
				//get dates & times for this schedule - not pretty but works.. $sub['id']
				$get_dates_and_times = DB::query(Database::SELECT,
					"SELECT `datetime_start` FROM plugin_courses_schedules_events WHERE `schedule_id` = ".$sub['id'].
						" AND `publish` = 1 AND `delete` = 0")->execute()->as_array();

				if (count($get_dates_and_times) > 1)
				{
					$time_and_date = '<span class="more_info" data-schedule="'.
						$sub['id'].'">Multiple Dates</span><div class="schedule_dates_list" id="more_times_'.
						$sub['id'].'">';
					foreach ($get_dates_and_times AS $t_a_d => $element)
					{
						$time_and_date .= date('D jS M g:i', strtotime($element['datetime_start']))."<br/>";
					}
					$time_and_date .= '</div>';
				}
				else
				{
					$time_and_date = date('l j/M g:ia', strtotime($sub['start_date']));
				}
				// manage tinyint Yes/No's
				if ($sub['is_fee_required'] == 1)
				{
					$sub['is_fee_required'] = 'Yes';
				}
				else $sub['is_fee_required'] = 'No';
				// manage tinyint Yes/No's
				if ($sub['is_confirmed'] == 1)
				{
					$sub['is_confirmed'] = 'Yes';
				}
				else
				{
					$sub['is_confirmed'] = 'No';
				}
				$fee      = ($sub['is_fee_required'] == 'Yes' AND $sub['fee_amount'] > 0) ? '&euro;'.$sub['fee_amount'] : 'No';
				$modified = IbHelpers::relative_time_with_tooltip($sub['date_modified']);

				$return[$i]['id']            = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['id'].'</a>';
				$return[$i]['course']        = $course_titles ? $course_titles : '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['course'].'</a>';
				$return[$i]['name']          = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['name'].'</a>';
				$return[$i]['category']      = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['category'].'</a>';
				$return[$i]['fee']           = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$fee.'</a>';
				$return[$i]['repeat_name']   = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['repeat_name'].'</a>';
                $return[$i]['status_label']  = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['status_label'].'</a>';
				$return[$i]['start_date']    = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['start_date'].'</a>';
				$return[$i]['location']      = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['location'].'</a>';
				$return[$i]['times']         = $time_and_date;
				$return[$i]['trainer']       = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['trainer'].'</a>';
				$return[$i]['confirmed']     = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$sub['is_confirmed'].'</a>';
				$return[$i]['last_modified'] = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'.$modified.'</a>';

                $action_options = [
                    ['type' => 'link', 'title' => 'View/Edit',   'attributes' => ['class' => 'edit-link', 'href' => '/admin/courses/edit_schedule/?id='. $sub['id']]],
                    ['type' => 'link', 'title' => 'Duplicate', 'attributes' => ['class' => 'duplicate-link', 'href' => '/admin/courses/duplicate_schedule/?id='. $sub['id']]]];
                $schedule_bookings = Model_KES_Bookings::search(array('schedule_id'    => $sub['id'],
                    'booking_status' => [Model_KES_Bookings::CONFIRMED, Model_KES_Bookings::INPROGRESS, Model_KES_Bookings::COMPLETED]
                ));

                if (count($schedule_bookings) === 0) {
                    if ($sub['schedule_status'] != Model_Schedules::CANCELLED) {
                        $action_options[] =  ['type' => 'link', 'title' => 'Cancel',   'attributes' => ['class' => 'cancel-link', 'href' => '/admin/courses/cancel_schedule/?id='. $sub['id']]];
                    } else {
                        $action_options[] =  ['type' => 'link', 'title' => 'Delete',   'attributes' => ['class' => 'delete-link', 'href' => '/admin/courses/remove_schedule/?id='. $sub['id']]];
                    }
                }

                $return[$i]['actions'] = View::factory('snippets/btn_dropdown')
                    ->set('type', 'actions')
                    ->set('options', $action_options)->render();
				$return[$i]['availability'] = '<a href="/admin/courses/edit_schedule/?id='.$sub['id'].'">'. ((int)$sub['bookings']) . '/' . ((int)$sub['capacity']).'</a>';

				if ($sub['publish'] == '1')
				{
					$return[$i]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="'.$sub['id'].'"><i class="icon-ok"></i></a>';
				}
				else
				{
					$return[$i]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="'.$sub['id'].'"><i class="icon-ban-circle"></i></a>';
				}
				$i++;
			}
		}

		$output['aaData'] = $return;
		return $output;
	}

	public static function get_listing_for_front($past = FALSE)
	{
		//get category id
		$parsed_url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		$cat        = str_ireplace(".html", "", end($parsed_url));
		$category   = Model_Categories::get_from_breadcrumbs($cat);
		if ($past !== FALSE)
		{
			$_past = '';
		}
		else
		{
			$_past = ' AND `plugin_courses_schedules`.`end_date` >= current_date()';
		}
		if ($category)
		{

			$query = DB::query(Database::SELECT,
				"SELECT `plugin_courses_schedules`.`name`,
            `plugin_courses_schedules`.`start_date`,
            `plugin_courses_schedules`.`id`,
            DATE_FORMAT(`plugin_courses_schedules`.`start_date`, '%Y') as `year`,
            DATE_FORMAT(`plugin_courses_schedules`.`start_date`, '%b') as `month`,
            `plugin_courses_courses`.`title`, `plugin_courses_courses`.`summary`,
            `plugin_courses_categories`.`category`,
            `plugin_courses_schedules_events`.`datetime_start` AS `schedule_day`
            FROM
            `plugin_courses_schedules`
            LEFT JOIN
            `plugin_courses_courses`
            ON
            `plugin_courses_schedules`.`course_id` = `plugin_courses_courses`.`id`
            LEFT JOIN
            `plugin_courses_categories`
            ON
            `plugin_courses_categories`.`id` = `plugin_courses_courses`.`category_id`
            LEFT JOIN
            `plugin_courses_schedules_events`
            ON
            `plugin_courses_schedules_events`.`schedule_id` = `plugin_courses_schedules`.`id`
            WHERE
            `plugin_courses_schedules`.`publish` = 1 AND `plugin_courses_schedules`.`delete` = 0 AND
            `plugin_courses_schedules_events`.`datetime_start` >= NOW()
            AND `plugin_courses_courses`.`category_id` = ".$category['id'].$_past." GROUP BY `plugin_courses_schedules`.`id` ORDER BY start_date ASC")
				->execute()
				->as_array();
			return $query;
		}
		else
		{
			return FALSE;
		}

	}

	public static function get_all_published_for_autocomplete($term, $include_id = FALSE)
	{
		$term     = Database::instance()->real_escape($term);
		$query    = DB::query(Database::SELECT, "
        SELECT DISTINCT `title`,`plugin_courses_courses`.`id` FROM
            `plugin_courses_courses`
            LEFT JOIN plugin_courses_schedules ON plugin_courses_courses.id = plugin_courses_schedules.course_id
            WHERE `plugin_courses_courses`.`publish` = 1
            AND `plugin_courses_courses`.`deleted` = 0
            AND (`plugin_courses_courses`.`title` like '%".$term."%'
            OR `plugin_courses_courses`.`summary` like '%".$term."%')
            AND `plugin_courses_schedules`.`end_date` >= current_date()")
			->execute()
			->as_array();
		$response = array();
		if (is_array($query) && count($query) > 0)
		{
			foreach ($query as $key => $val)
			{
				$response[] = ($include_id) ? "{$val['id']} - {$val['title']}": $val['title'];
			}
		}
		return $response;
	}

	public static function copy_timeslots($from_schedule_id, $to_schedule_id)
	{
        Database::instance()->begin();
		$timeslots = DB::select('*')
            ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
            ->where('schedule_id', '=', $from_schedule_id)
            ->and_where('delete', '=', 0)
            ->order_by('datetime_start')
            ->execute()
            ->as_array();

        $interval = DB::select('*')
            ->from('plugin_courses_schedules_has_intervals')
            ->where('schedule_id', '=', $from_schedule_id)
            ->execute()
            ->current();

        $interval_rows = DB::select('*')
            ->from('plugin_courses_schedules_intervals')
            ->where('interval_id', '=', $interval['id'])
            ->and_where('deleted', '=', 0)
            ->execute()
            ->as_array();

        DB::delete('plugin_courses_schedules_has_intervals')->where('schedule_id', '=', $to_schedule_id)->execute();
        $new_interval_inserted = DB::insert('plugin_courses_schedules_has_intervals')
            ->values(
                array(
                    'schedule_id' => $to_schedule_id,
                    'custom_frequency' => $interval['custom_frequency']
                )
            )->execute();

        $new_interval_id = $new_interval_inserted[0];
        foreach ($interval_rows as $interval_row) {
            unset($interval_row['id']);
            $interval_row['interval_id'] = $new_interval_id;

            DB::insert('plugin_courses_schedules_intervals')->values($interval_row)->execute();
        }

        DB::update(Model_Schedules::TABLE_TIMESLOTS)
            ->set(
                array('delete' => 1, 'date_modified' => date::now(), 'modified_by' => 1)
            )->where('schedule_id', '=', $to_schedule_id)
            ->execute();
        $new_timetable_inserted = DB::insert('plugin_courses_timetable')
            ->values(array('timetable_name' => 'schedule ' . $to_schedule_id))
            ->execute();
        $now = date::now();
        foreach ($timeslots as $timeslot) {
            unset($timeslot['id']);
            $timeslot['date_created'] = $timeslot['date_modified'] = $now;
            $timeslot['created_by'] = $timeslot['modified_by'] = 1;
            $timeslot['timetable_id'] = $new_timetable_inserted[0];
            $timeslot['schedule_id'] = $to_schedule_id;
            DB::insert(Model_ScheduleEvent::TABLE_TIMESLOTS)->values($timeslot)->execute();
        }

        Database::instance()->commit();
	}

	public static function get_front_one()
	{
		$id    = (int) $_GET['id'];
		$query = DB::query(Database::SELECT,
			'SELECT `plugin_courses_schedules`.`id`,
                    `plugin_courses_schedules`.`start_date`,
                    `plugin_courses_schedules`.`end_date`,
                    `plugin_courses_schedules`.`is_fee_required`,
                    `plugin_courses_schedules`.`fee_amount`,
                    `plugin_courses_schedules`.`fee_per`,
                    0 as `start_date`,
                    `plugin_courses_schedules`.`course_id`,
                    `plugin_courses_courses`.`title`,
                    `plugin_courses_courses`.`book_button`,
                    `plugin_courses_courses`.`file_id`,
                    `plugin_courses_courses`.`summary`,
                    `plugin_courses_courses`.`description`,
                    `plugin_courses_levels`.`level`,
                    `plugin_courses_categories`.`category`,
                    `plugin_courses_years`.`year`,
                    `plugin_courses_types`.`type`,
                    `plugin_courses_study_modes`.`study_mode`,
                    (SELECT `image` FROM `plugin_courses_courses_images`
                    WHERE `course_id` = `plugin_courses_schedules`.`course_id`
                    ORDER by `plugin_courses_courses_images`.`id` LIMIT 1) as `image`,
                    `plugin_courses_providers`.`name` as `provider`
             FROM `plugin_courses_schedules`
             LEFT JOIN
              `plugin_courses_courses`
              ON
                `plugin_courses_courses`.`id` = `plugin_courses_schedules`.`course_id`
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
              `plugin_courses_study_modes`
              ON
                `plugin_courses_study_modes`.`id` = `plugin_courses_schedules`.`study_mode_id`
             LEFT JOIN
              `plugin_courses_levels`
              ON
                `plugin_courses_levels`.`id` = `plugin_courses_courses`.`level_id`
             LEFT JOIN
              `plugin_courses_providers`
              ON
                `plugin_courses_providers`.`id` = `plugin_courses_courses`.`provider_id`
             WHERE
                `plugin_courses_schedules`.`delete` = 0 AND `plugin_courses_schedules`.`publish` = 1 AND `plugin_courses_schedules`.`id` = '.$id." AND plugin_courses_schedules.`end_date` >= DATE(NOW())")
			->execute()
			->as_array();
		if (is_array($query) AND count($query) > 0)
		{
			$course_id                = $query['0']['course_id'];
			$list                     = DB::query(Database::SELECT,
				'SELECT
                    `id`,
                    `start_date`,
                    `plugin_courses_schedules`.`max_capacity`,
                    `is_fee_required`,
                    `fee_per`,
                    (
                      SELECT COUNT(1)
                      FROM plugin_courses_bookings_migrate
                      WHERE plugin_courses_bookings_migrate.`schedule_id` = `plugin_courses_schedules`.`id`
                    ) AS `number_booked`,
                    `fee_amount`
                FROM
                `plugin_courses_schedules`
                 WHERE
                    `delete` = 0 AND `publish` = 1
                    AND `course_id` = '.$course_id.'
                    AND plugin_courses_schedules.`end_date` >= DATE(NOW())
                    ORDER BY `start_date`')
				->execute()
				->as_array();
			$query['0']['start_date'] = $list;
			return $query['0'];
		}
		else
		{
			return FALSE;
		}

	}

	public static function get_listing_for_feed($past = FALSE)
	{
		$query = DB::select(
			array('plugin_courses_schedules.id', 'id'),
			array('plugin_courses_schedules.name', 'name'),
			array('plugin_courses_schedules.start_date', 'start_date'),
			array('plugin_courses_schedules_events.datetime_start', 'datetime_start'),
			array('plugin_courses_schedules_events.datetime_end', 'datetime_end'),
			array('plugin_courses_courses.title', 'title'),
			array('plugin_courses_courses.summary', 'summary'),
			array('plugin_courses_categories.category', 'category')
		)
			->from('plugin_courses_schedules')
			->join('plugin_courses_courses', 'left')
			->on('plugin_courses_schedules.course_id', '=', 'plugin_courses_courses.id')
			->join('plugin_courses_categories', 'left')
			->on('plugin_courses_categories.id', '=', 'plugin_courses_courses.category_id')
			->join('plugin_courses_schedules_events', 'left')
			->on('plugin_courses_schedules.id', '=', 'plugin_courses_schedules_events.schedule_id')
			->where('plugin_courses_schedules.publish', '=', 1)
			->and_where('plugin_courses_schedules.delete', '=', 0)
			->where('plugin_courses_schedules_events.publish', '=', 1)
			->and_where('plugin_courses_schedules_events.delete', '=', 0);

		if ($past == FALSE)
		{
			$query = $query->and_where('plugin_courses_schedules_events.datetime_start', '>=', DB::expr('CURRENT_DATE()'));
		}

		$query = $query->order_by('datetime_start', 'asc')->execute()->as_array();

		return $query;
	}

	static function get_schedules_json($term)
	{
		$query = DB::select()
			->from('plugin_courses_schedules')
			->where('name', 'LIKE', '%'.$term.'%')
			->where('delete', '=', 0);
		$count = clone $query;

		$return['results'] = $query
			->select('id', array('name', 'title'))
			->order_by('name')
			->limit(5)
			->execute()
			->as_array();
		$return['count']   = $count->select(array(DB::expr('count(*)'), 'count'))
			->execute()->get('count', 0);

		return json_encode($return);
	}

	public static function duplicate_schedule($id)
	{
		$data                  = self::get_schedule($id);
		$data['name']          = $data['name'].' - Clone';
		$data['id']            = NULL;
		$data['duplicate']     = $id;
		$data['date_created']  = date("Y-m-d H:i:s", time());
		$data['date_modified'] = date("Y-m-d H:i:s", time());
		$data['timetable_id']  = NULL;
		$duplicate             = self::save_schedule($data);
		if ($data['repeat'] == 6)
		{
			$intervals    = Model_Schedules::get_custom_repeat($id);
			$has_interval = array('schedule_id' => $duplicate[0], 'custom_frequency' => $intervals[0]['custom_frequency']);
			$interval_id  = DB::insert('plugin_courses_schedules_has_intervals', array_keys($has_interval))->values($has_interval)->execute();
			foreach ($intervals as $key => $interval)
			{
				unset($interval['custom_frequency']);
				unset($interval['id']);
				$interval['interval_id'] = $interval_id[0];
				DB::insert('plugin_courses_schedules_intervals', array_keys($interval))->values($interval)->execute();
			}
		}
		return $duplicate;
	}

	public static function get_schedule($id, $args = array())
	{
		$q = DB::select('schedule.*', array('location.name', 'location'), array('plocation.name', 'plocation'),
            array('plocation.id', 'building_id'), ['courses_schedules_status.title', 'schedule_status_label'])
			->from(array('plugin_courses_schedules', 'schedule'))
            ->join(array('plugin_courses_locations', 'location'), 'left')
                ->on('schedule.location_id', '=', 'location.id')
            ->join(array('plugin_courses_locations', 'plocation'), 'left')
                ->on('location.parent_id', '=', 'plocation.id')
            ->join(array('plugin_courses_schedules_status', 'courses_schedules_status'), 'left')
                ->on('schedule.schedule_status', '=', 'courses_schedules_status.id')
            ->where('schedule.id', '=', $id)
            ->where('schedule.delete', '=', 0);

        if ( ! empty($args['published'])) {
            $q->where('schedule.publish', '=', 1);
        }

        $data = $q->execute()->current();

        if ($data) {
            $data['has_courses'] = DB::select('*')
                ->from(self::TABLE_HAS_COURSES)
                ->where('schedule_id', '=', $id)
                ->execute()
                ->as_array();
            foreach ($data['has_courses'] as $i => $has_course) {
                $data['has_courses'][$i] = $has_course['course_id'];
            }

			$data['timeslots'] = DB::select('*')
					->from(self::TABLE_TIMESLOTS)
					->where('schedule_id', '=', $id)
					->and_where('delete', '=', 0)
					->order_by('datetime_start', 'asc')
					->execute()
					->as_array();
			$data['topics'] = DB::select('topic.*')
					->from(array('plugin_courses_topics', 'topic'))
					->join(array('plugin_courses_schedules_have_topics', 'has_topic'))
					->on('has_topic.topic_id', '=', 'topic.id')
					->where('has_topic.schedule_id', '=', $id)
					->where('topic.deleted',       '=', 0)
					->where('has_topic.deleted',   '=', 0)
					->execute()
					->as_array();
            $data['paymentoptions'] = DB::select('*')
                ->from(self::TABLE_HAS_PAYMENTOPTIONS)
                ->where('schedule_id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->order_by('months', 'asc')
                ->execute()
                ->as_array();
            foreach ($data['paymentoptions'] as $i => $paymentoption) {
                if ($paymentoption['interest_type'] == 'Custom') {
                    $data['paymentoptions'][$i]['custom_payments'] = @json_decode($data['paymentoptions'][$i]['custom_payments'], true) ?: array();
                }
            }
            //header('content-type: text/plain');print_r($data['paymentoptions']);exit;
            return $data;
        }
        else {
            // If no results are found, return an empty associative array
            $return  = array('location' => '', 'plocation' => '', 'building_id' => '');
            $columns = Database::instance()->list_columns('plugin_courses_schedules');

            foreach ($columns as $column => $data) {
                $return[$column] = '';
            }

            return $return;
        }
	}

    public static function get_timeslots($filters)
    {
        $q = DB::select(
            'timeslot.*',
			'schedule.booking_type',
			'schedule.trial_timeslot_free_booking',
			'schedule.deposit',
			'schedule.fee_per',
			array('schedule.fee_amount', 'schedule_fee_amount'),
            array(DB::expr("DATE(`timeslot`.`datetime_start`)"), 'start_date'),
            array(DB::expr("TIMEDIFF(`datetime_end`, `datetime_start`)"), 'duration'),
            array(DB::expr("IF(`schedule`.`payment_type` = 1, 'Pre-pay', 'PAYG')"), 'payment_type'),
            'schedule.fee_per',
            array(DB::expr("CONCAT (`trainer`.`first_name`, ' ', `trainer`.`last_name`)"), 'trainer'),
            array('location.name', 'location'),
            array('room.name', 'room')

        )
            ->from(array(self::TABLE_TIMESLOTS, 'timeslot'))
            ->join(array('plugin_courses_schedules',  'schedule'), 'LEFT')->on('timeslot.schedule_id', '=', 'schedule.id')
            ->join(array('plugin_contacts3_contacts', 'trainer' ), 'LEFT')->on('timeslot.trainer_id',  '=', 'trainer.id')
            ->join(array('plugin_courses_locations',  'room'    ), 'LEFT')->on('schedule.location_id', '=', 'room.id')
            ->join(array('plugin_courses_locations',  'location'), 'LEFT')->on('room.parent_id',       '=', 'location.id')
        ;

        if (!empty($filters['course_id'])) {
            $q->where('schedule.course_id', '=', $filters['course_id']);
        }
        if (!empty($filters['schedule_id'])) {
            $q->where('timeslot.schedule_id', '=', $filters['schedule_id']);
        }

        if (isset($params['book_on_website'])) {
            $q->and_where('schedules.book_on_website', '=', $params['book_on_website']);
        }

        $q
            ->where('timeslot.delete', '=', 0)
            ->order_by('timeslot.datetime_start', 'asc')
            ->execute()
            ->as_array();

        return $q->execute()->as_array();
    }

	public static function get_schedule_blackout_events($schedule_id)
	{
		$events = DB::select('*')
			->from(self::HAS_ENGINE_EVENTS)
			->where('schedule_id', '=', $schedule_id)
			->execute()
			->as_array();
		$result = array();
		foreach ($events as $event)
		{
			$result[] = $event['engine_calendar_event_id'];
		}
		return $result;
	}

	public static function save_schedule($data)
	{
		// add / update
		$save_action = 'add';
		$item_id     = 0;
		//Add the necessary values to the $data array for update
		$logged_in_user = Auth::instance()->get_user();
		$duplicate      = (isset($data['duplicate']) ? $data['duplicate'] : FALSE);
		$topic_ids = isset($data['topic_ids']) ? $data['topic_ids'] : array();
        $has_courses = @$data['course_ids'];
        $paymentoptions = @$data['paymentoption'];
        unset($data['paymentoption']);

		unset($data['duplicate']);
		unset($data['schedule_id']);
		unset($data['new_frequency']);
		unset($data['timetable_hidden']);
		unset($data['timetable_post_name']);
		unset($data['trainer_hidden']);
		unset($data['timetable_id']);
        unset($data['topic_ids']);

        if (!Auth::instance()->has_access('courses_schedule_amendable')) {
            unset($data['amendable']);
        }

        // Get all columns in the schedules table
		$columns = Arr::column(DB::query(Database::SELECT, 'SHOW FULL COLUMNS FROM `plugin_courses_schedules`')->execute()->as_array(), 'Field');
        $update_data = array();
        if (is_array($data['fee_amount'])) {
			$data['fee_amount'] = array_sum($data['fee_amount']) / count($data['fee_amount']);
		}

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
			$data['modified_by']   = $logged_in_user['id'];
			$data['date_modified'] = date('Y-m-d H:i:s');

            // Only use data that has a corresponding column in the schedules table
			foreach ($data as $key => $value)
			{
				if (in_array($key, $columns))
				{
					$update_data[$key] = $value;
				}
			}
			$previos_data = DB::select("*")->from(self::TABLE_SCHEDULES)->where('id', '=', $id)->execute()->current();
			if ($previos_data['max_capacity'] < $update_data['max_capacity']) {
				Model_Automations::run_triggers(Model_Courses_Schedulespaceavailabletrigger::NAME, array('schedule_id' => $id));
			}
            $query = DB::update('plugin_courses_schedules')
				->set($update_data)
				->where('id', '=', $id)
				->execute();

			$save_action = 'update';
			$item_id     = $id;
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
			$data['created_by']   = $logged_in_user['id'];
            $data['date_modified'] = $data['date_created'] = date('Y-m-d H:i:s');
			$data['delete']       = 0;
			unset($data['duration']);

			// Only use data that has a corresponding column in the schedules table
			foreach ($data as $key => $value)
			{
				if (in_array($key, $columns))
				{
					$update_data[$key] = $value;
				}
			}

			$query       = DB::insert('plugin_courses_schedules', array_keys($update_data))
				->values($update_data)
				->execute();
			$save_action = 'add';

			$item_id = (isset($query[0]) AND $query[0] > 0) ? $query[0] : 0;
		}

        if ($item_id)
        {
            DB::delete(self::TABLE_HAS_COURSES)->where('schedule_id', '=', $item_id)->execute();
            if ($has_courses) {
                foreach ($has_courses as $has_course_id) {
                    DB::insert(self::TABLE_HAS_COURSES)
                        ->values(array('course_id' => $has_course_id, 'schedule_id' => $item_id))
                        ->execute();
                }
            }

            $paymentoption_ids = array();
            if (is_array($paymentoptions))
                foreach ($paymentoptions as $paymentoption) {
                    $paymentoption['schedule_id'] = $item_id;
                    if (@$paymentoption['interest_type'] == 'Custom') {
						$paymentoption['custom_payments'] = array_values($paymentoption['custom_payments']);
						foreach ($paymentoption['custom_payments'] as $ci => $custom_payment) {
							$paymentoption['custom_payments'][$ci]['total'] = $paymentoption['custom_payments'][$ci]['amount'] + $paymentoption['custom_payments'][$ci]['interest'];
						}
                        $paymentoption['custom_payments'] = json_encode($paymentoption['custom_payments'], JSON_PRETTY_PRINT);
                    } else {
                        $paymentoption['custom_payments'] = '';
                    }
                    if (@$paymentoption['id'] > 0) {
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
                ->where('schedule_id', '=', $item_id);
            if (count($paymentoption_ids) > 0) {
                $deleteq->and_where('id', 'not in', $paymentoption_ids);
            }
            $deleteq->execute();

            // Get the topics the schedule had before this update
            $existing_topics = DB::select()
                ->from('plugin_courses_schedules_have_topics')
                ->where('schedule_id', '=', $item_id)
                ->where('deleted', '=', 0)
                ->execute()
                ->as_array();

            $existing_topic_ids = array();
            foreach ($existing_topics as $existing_topic) {
                $existing_topic_ids[] = $existing_topic['topic_id'];
            }

            // See what topics have been added and removed
            $delete_topic_ids = array_diff($existing_topic_ids, $topic_ids);
            $new_topic_ids    = array_diff($topic_ids, $existing_topic_ids);

            // Delete topics that have been removed
            if (count($delete_topic_ids)) {
                DB::update('plugin_courses_schedules_have_topics')
                    ->set('deleted', 1)
                    ->set('deleted_at', date('Y-m-d H:i:s'))
                    ->where('schedule_id', '=', $item_id)
                    ->where('topic_id',    'in', $delete_topic_ids)
                    ->where('delete',      '=', 0)
                    ->execute();
            }

            // Insert topics that have been added
            if (count($new_topic_ids)) {
                $q = DB::insert('plugin_courses_schedules_have_topics', array('schedule_id', 'topic_id'));
                foreach ($new_topic_ids as $topic_id) {
                    $q->values(array($item_id, $topic_id));
                }
                $q->execute();
            }

        }

		// Set Successful / Not Successful Insert / Update Message
		if (
			($save_action == 'add' AND $query[0] > 0) OR
			($save_action == 'update' AND $query == 1)
		)
		{
			DB::update('plugin_courses_schedules_events')->set(array('trainer_id' => $data['trainer_id']))->where('schedule_id', '=', $item_id)->and_where('trainer_id', '=', 0)->execute();
			if ($duplicate)
			{
				IbHelpers::set_message(
					'Schedule ID #'.$item_id.':  "'.$data['name'].'" has been cloned. Please select a start and end date and generate the timetable to complete the cloning.',
					'warning popup_box'
				);
			}
			else
			{
				IbHelpers::set_message(
					'Schedule ID #'.$item_id.':  "'.$data['name'].'" has been '.(($save_action == 'add') ? 'CREATED' : 'UPDATED').'.',
					'success popup_box'
				);
			}
		}
		else
		{
			IbHelpers::set_message(
				'Sorry! There was a problem with '.(($save_action == 'add') ? 'CREATION' : 'UPDATE')
					.' of '.(($item_id > 0) ? 'ID #'.$item_id : 'Schedule').': "'.$data['name'].'".<br />'
					.'Please make sure, that form is filled properly and Try Again!',
				'error popup_box'
			);
		}

		return $item_id;
	}

	public static function get_schedule_timetable($schedule = NULL)
	{
		$timetable_id = NULL;
		if ((!is_null($schedule)) AND is_numeric($schedule))
		{
			$timetable    = DB::select('timetable_id')->from('plugin_courses_schedules_events')->where('schedule_id', '=', $schedule)->execute()->as_array();
			$timetable_id = $timetable[0]['timetable_id'];
		}
		else
		{
			$timetable_id = 'new';
		}
		return $timetable_id;
	}

	public static function get_active_timetable($schedule)
	{
		$query = DB::select('plugin_courses_timetable.id')
			->from('plugin_courses_timetable')
			->join('plugin_courses_schedules_events', 'LEFT')
			->on('plugin_courses_schedules_events.timetable_id', '=', 'plugin_courses_timetable.id')
			->where('plugin_courses_schedules_events.schedule_id', '=', $schedule)
			->and_where('plugin_courses_schedules_events.delete', '=', 0)
			->limit(1)
			->execute()->as_array();
		return (is_array($query) AND count($query) > 0) ? $query[0]['id'] : 'new';
	}

	public static function copy_timetable($timetable_id, $schedules = NULL)
	{
		//Some data checking first.
		if (!isset($timetable_id) AND is_numeric($timetable_id))
		{
			throw new Exception("Timetable ID not set.");
		}
		if (empty($schedules))
		{
			throw new Exception("Schedules not set.");
		}
		if (isset($schedules) AND !is_array($schedules))
		{
			throw new Exception("Schedules must be an array.");
		}

		$single = DB::select('schedule_id')
			->from('plugin_courses_schedules_events')
			->where('timetable_id', '=', $timetable_id)
			->and_where('delete', '=', 0)
			->limit(1)->execute()->as_array();
		if (count($single) > 0)
		{
			$id    = $single[0]['schedule_id'];
			$i     = 0;
			$query = DB::select('datetime_start', 'datetime_end')
				->from('plugin_courses_schedules_events')
				->where('timetable_id', '=', $timetable_id)
				->and_where('schedule_id', '=', $id)
				->and_where('delete', '=', 0)
				->and_where('publish', '=', 1)
				->execute()->as_array();
			if (count($query) === 0)
			{
				throw new Exception("No Previous Timetable - Parameters [Timetable ID: ".$timetable_id." ---- Schedule: ".$id."]");
			}
			if ($schedules === NULL)
			{
				return $query;
			}
			$table_columns = array("schedule_id", "datetime_start", "datetime_end", "publish", "delete", "timetable_id");
			$start_date    = $query[0]['datetime_start'];
			$end_date      = $query[count($query) - 1]['datetime_end'];
			foreach ($schedules AS $schedule)
			{
				DB::update('plugin_courses_schedules')
					->set(array('start_date' => $start_date, 'end_date' => $end_date))
					->where('id', '=', $schedule['schedule_id'])
					->execute();
				foreach ($query AS $fullday => $part_day)
				{
					$table_rows   = array();
					$table_rows[] = $schedule;
					$table_rows[] = $part_day['datetime_start'];
					$table_rows[] = $part_day['datetime_end'];
					$table_rows[] = "1";
					$table_rows[] = "0";
					$table_rows[] = $id;
					DB::insert('plugin_courses_schedules_events', $table_columns)
						->values($table_rows)
						->execute();
				}
				$i++;
			}
		}
	}

	public static function set_publish_schedule($id, $state)
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
		$query          = DB::update("plugin_courses_schedules")
			->set(array(
				'publish'       => $published,
				'modified_by'   => $logged_in_user['id'],
				'date_modified' => date('Y-m-d H:i:s')
			))
			->where('id', '=', $id)
			->execute();
		$response       = array();
		if ($query > 0)
		{
			$response['message'] = 'success';
		}
		else
		{
			$response['message']   = 'error';
			$response['error_msg'] = 'An error occurred! Please contact with support!';
		}
		return $response;
	}

	public static function remove_schedule($id)
	{
		$logged_in_user = Auth::instance()->get_user();
		DB::update('plugin_courses_schedules_events')
			->set(array(
				'modified_by'   => $logged_in_user['id'],
				'date_modified' => date('Y-m-d H:i:s'),
				'delete'        => 1,
				'publish'       => 0
			))
			->where('schedule_id', '=', $id)
			->execute();
		$ret = DB::update('plugin_courses_schedules')
			->set(array(
				'modified_by'   => $logged_in_user['id'],
				'date_modified' => date('Y-m-d H:i:s'),
				'delete'        => 1
			))
			->where('id', '=', $id)
			->execute();
		if ($ret > 0)
		{
			$response['message'] = 'success';
		}
		else
		{
			$response['message']   = 'error';
			$response['error_msg'] = 'An error occurred! Please contact with support!';
		}
		return $response;
	}

	public static function validate_schedule($data)
	{
		//create empty errors array
		$errors = array();
		//check name must be min 3 chars
		if (@strlen($data['name']) < 3)
		{
			IbHelpers::set_message("Schedule name must contains min 3 characters", 'error popup_box');
			$errors[] = IbHelpers::get_messages();
		}
		return $errors;

	}

	public static function get_front_list_for_search($search)
	{
		$search = Database::instance()->real_escape($search);
		$list   = DB::query(Database::SELECT,
			"SELECT
              `plugin_courses_schedules`.`name`,
              `plugin_courses_schedules`.`start_date`,
              `plugin_courses_schedules`.`id`,
              DATE_FORMAT(`plugin_courses_schedules`.`start_date`, '%Y') as `year`,
              DATE_FORMAT(`plugin_courses_schedules`.`start_date`, '%b') as `month`,
              `plugin_courses_courses`.`title`, `plugin_courses_courses`.`summary`,
              `plugin_courses_categories`.`category`
              FROM `plugin_courses_schedules`
            LEFT JOIN
            `plugin_courses_courses`
            ON
            `plugin_courses_schedules`.`course_id` = `plugin_courses_courses`.`id`
            LEFT JOIN
            `plugin_courses_categories`
            ON
            `plugin_courses_categories`.`id` = `plugin_courses_courses`.`category_id`
            WHERE
            `plugin_courses_schedules`.`publish` = 1 AND `plugin_courses_schedules`.`delete` = 0 AND `plugin_courses_courses`.`title` = '".$search."'  AND `plugin_courses_schedules`.`end_date` >= current_date()")
			->execute()
			->as_array();

		$view = View::factory(
			'front_end/courses_list_for_search',
			array(
				'list' => $list
			));

		return $view;
	}

	public static function get_details()
	{
		$id     = (int) $_GET['id'];
		$course = self::get_one_for_details($id);
		$view   = View::factory(
			'front_end/course_details',
			array(
				'course' => $course
			)
		);
		return $view;

	}

	public static function get_one_for_details($id, $cached = true, $timeslot_id = null)
	{
		static $cache = array();

		if (!is_numeric($id))
			return false;

        if ($cached && isset($cache[$id]) && $timeslot_id == null) {
            return $cache[$id];
        }

		$schedule = DB::query(Database::SELECT,
			'SELECT `plugin_courses_schedules`.`id`,
                    plugin_courses_schedules.course_id,
                    plugin_courses_schedules.owned_by,
                    plugin_courses_schedules.name AS schedule,
                    plugin_courses_schedules.allow_sales_quote AS allow_sales_quote,
                    `plugin_courses_schedules`.`start_date`,
                    `plugin_courses_schedules`.`start_date` AS datetime_start,
                    `plugin_courses_schedules`.`end_date`,
                    `plugin_courses_schedules`.`weekdays_monday`,
                    `plugin_courses_schedules`.`weekdays_tuesday`,
                    `plugin_courses_schedules`.`weekdays_wednesday`,
                    `plugin_courses_schedules`.`weekdays_thursday`,
                    `plugin_courses_schedules`.`weekdays_friday`,
                    `plugin_courses_schedules`.`weekdays_saturday`,
                    `plugin_courses_schedules`.`weekdays_sunday`,
                    `plugin_courses_schedules`.`is_fee_required`,
                    `plugin_courses_schedules`.`fee_amount`,
                    `plugin_courses_schedules`.`fee_per`,
                    `plugin_courses_schedules`.`deposit`,
                    `plugin_courses_schedules`.`payment_type`,
                    `plugin_courses_schedules`.`amendable`,
                    `plugin_courses_schedules`.`trial_timeslot_free_booking`,
                    `plugin_courses_schedules`.`display_timeslots_in_cart`,
                    `plugin_courses_schedules`.`charge_per_delegate`,
                    plugin_courses_schedules.booking_type,
                    plugin_courses_schedules.is_group_booking,
                    plugin_courses_schedules.max_capacity,
                    plugin_courses_schedules.min_capacity,
                    plugin_courses_schedules.deposit,
                    `plugin_courses_courses`.`title` AS course,
                    `plugin_courses_courses`.`title` AS title,
                    `plugin_courses_courses`.`summary`,
                    `plugin_courses_courses`.`description`,
                    `plugin_courses_levels`.`level`,
                    `plugin_courses_categories`.`category`,
                    `plugin_courses_categories`.`checkout_alert`,
                    `plugin_courses_years`.`year`,
                    `plugin_courses_types`.`type`,
                    `plugin_courses_study_modes`.`study_mode`,
                    GROUP_CONCAT(`plugin_courses_providers`.`name`) as `provider`,
                    CONCAT_WS(\' \', plugin_courses_locations.name, plocation.name) AS `room`,
                    CONCAT_WS(\' \', plocation.name, plugin_courses_locations.name) AS `location`,
					IF(plugin_courses_schedules.booking_type = \'Whole Schedule\', \'all\', plugin_courses_schedules_events.id) AS `event_id`,
					IF(plugin_courses_schedules.booking_type = \'Whole Schedule\', plugin_courses_schedules.start_date, plugin_courses_schedules_events.datetime_start) AS `start_date`,
					IF(plugin_courses_schedules.booking_type = \'Whole Schedule\', plugin_courses_schedules.end_date, plugin_courses_schedules_events.datetime_end) AS `end_date`,
					`plugin_courses_repeat`.`name` AS `repeat`
             FROM `plugin_courses_schedules`
             LEFT JOIN
              `plugin_courses_courses`
              ON
                `plugin_courses_courses`.`id` = `plugin_courses_schedules`.`course_id`
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
              `plugin_courses_study_modes`
              ON
                `plugin_courses_study_modes`.`id` = `plugin_courses_schedules`.`study_mode_id`
             LEFT JOIN
              `plugin_courses_levels`
              ON
                `plugin_courses_levels`.`id` = `plugin_courses_courses`.`level_id`
             LEFT JOIN
              plugin_courses_courses_has_providers
              ON
              plugin_courses_courses.id = plugin_courses_courses_has_providers.course_id
             LEFT JOIN
              `plugin_courses_providers`
              ON
                `plugin_courses_providers`.`id` = `plugin_courses_courses_has_providers`.`provider_id`
              LEFT JOIN
              `plugin_courses_locations`
              ON
                `plugin_courses_locations`.`id` = `plugin_courses_schedules`.`location_id`
              LEFT JOIN
              `plugin_courses_locations` plocation
              ON
                `plocation`.`id` = `plugin_courses_locations`.`parent_id`
              LEFT JOIN
               `plugin_courses_schedules_events`
              ON
                `plugin_courses_schedules_events`.`schedule_id` = `plugin_courses_schedules`.`id` and plugin_courses_schedules_events.delete = 0
              LEFT JOIN
               `plugin_courses_repeat`
              ON
                `plugin_courses_repeat`.`id` = `plugin_courses_schedules`.`repeat`                                
             WHERE
                `plugin_courses_schedules`.`id` = '.$id .
            ($timeslot_id ? " and plugin_courses_schedules_events.id = " . $timeslot_id : ""))
			->execute()
			->current();

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
        if ($cached && $timeslot_id == null) {
            $cache[$id] = $schedule;
        }
		return $schedule;
	}

	public static function feed_all_schedues_for_training($course_id)
	{
		$list = DB::query(Database::SELECT,
			'SELECT `id`, `start_date`
            FROM
            `plugin_courses_schedules`
             WHERE
                `delete` = 0 AND `publish` = 1 AND `course_id` = '.$course_id." ORDER BY `start_date`")
			->execute()
			->as_array();
		return $list;
	}

	public static function ajax_save_schedule($data)
	{
		//Add the necessary values to the $data array for update
		$logged_in_user     = Auth::instance()->get_user();
		$data['start_date'] = date("Y-m-d", strtotime($data['start_date']));
		$data['end_date']   = date("Y-m-d", strtotime($data['end_date']));
		unset($data['new_frequency']);
		$data['start_date'] = $data['start_date']." ".$data['start_hour'];
		unset($data['start_hour']);
		$data['end_date'] = $data['end_date']." ".$data['end_hour'];
		unset($data['end_hour']);

		foreach ($data as $k => $val)
		{
			if ($val == '')
			{
				$data[$k] = NULL;
			}
		}
		$data['created_by']   = $logged_in_user['id'];
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['delete']       = 0;
		$query                = DB::insert('plugin_courses_schedules', array_keys($data))
			->values($data)
			->execute();
		if ($query)
		{
			$response['message'] = 'success';
			$response['id']      = $query['0'];
		}
		else
		{
			$response['message'] = 'Error while saving schedule!';
		}
		return json_encode($response);
	}

	public static function ajax_create_events($data)
	{
		//determine start date
		$start_date = strtotime($data['start_date']);
		//determine end date
		$end_date = strtotime($data['end_date']);
		//determine days
		$days = array();
		if (isset($data['monday']) AND $data['monday'] == '1')
		{
			$days[] = 1;
		}
		if (isset($data['tuesday']) AND $data['tuesday'] == '1')
		{
			$days[] = 2;
		}
		if (isset($data['wednesday']) AND $data['wednesday'] == '1')
		{
			$days[] = 3;
		}
		if (isset($data['thursday']) AND $data['thursday'] == '1')
		{
			$days[] = 4;
		}
		if (isset($data['friday']) AND $data['friday'] == '1')
		{
			$days[] = 5;
		}
		if (isset($data['saturday']) AND $data['saturday'] == '1')
		{
			$days[] = 6;
		}
		if (isset($data['sunday']) AND $data['sunday'] == '1')
		{
			$days[] = 7;
		}
		if (count($days) > 1)
		{
			$dates = array();
			$r     = 0;
			while ($start_date < $end_date)
			{
				if (in_array(date("N", $start_date), $days))
				{
					$dates[$r]['schedule_id'] = $data['schedule_id'];
					if (date("N", $start_date) == 1)
					{
						$dates[$r]['datetime_start'] = date("Y-m-d", $start_date)." ".$data['start_monday'];
						$dates[$r]['datetime_end']   = date("Y-m-d", $start_date)." ".$data['end_monday'];
					}
					elseif (date("N", $start_date) == 2)
					{
						$dates[$r]['datetime_start'] = date("Y-m-d", $start_date)." ".$data['start_tuesday'];
						$dates[$r]['datetime_end']   = date("Y-m-d", $start_date)." ".$data['end_tuesday'];
					}
					elseif (date("N", $start_date) == 3)
					{
						$dates[$r]['datetime_start'] = date("Y-m-d", $start_date)." ".$data['start_wednesday'];
						$dates[$r]['datetime_end']   = date("Y-m-d", $start_date)." ".$data['end_wednesday'];
					}
					elseif (date("N", $start_date) == 4)
					{
						$dates[$r]['datetime_start'] = date("Y-m-d", $start_date)." ".$data['start_thursday'];
						$dates[$r]['datetime_end']   = date("Y-m-d", $start_date)." ".$data['end_thursday'];
					}
					elseif (date("N", $start_date) == 5)
					{
						$dates[$r]['datetime_start'] = date("Y-m-d", $start_date)." ".$data['start_friday'];
						$dates[$r]['datetime_end']   = date("Y-m-d", $start_date)." ".$data['end_friday'];
					}
					elseif (date("N", $start_date) == 6)
					{
						$dates[$r]['datetime_start'] = date("Y-m-d", $start_date)." ".$data['start_saturday'];
						$dates[$r]['datetime_end']   = date("Y-m-d", $start_date)." ".$data['end_saturday'];
					}
					elseif (date("N", $start_date) == 7)
					{
						$dates[$r]['datetime_start'] = date("Y-m-d", $start_date)." ".$data['start_sunday'];
						$dates[$r]['datetime_end']   = date("Y-m-d", $start_date)." ".$data['end_sunday'];
					}

					$r++;
				}
				$start_date += 86400;
			}
		}
		if (count($dates) > 0)
		{
			$response = array();
			foreach ($dates as $elem => $event)
			{
				$response = self::insert_event($event);
			}
		}
		return $response;
	}

	public static function get_repeat_as_options($repeat_id = NULL, $removed = NULL)
	{
		$q = DB::select('id', 'name')->from('plugin_courses_repeat');
		if (!is_null($removed))
		{
			$q->where('id', '!=', $removed)
				->where('id', '>', 2);
		}
		$q      = $q->execute()->as_array();
		$result = '';
		foreach ($q AS $key => $row)
		{
			$result .= '<option value="'.$row['id'].'" '.(($repeat_id == $row['id']) ? 'selected="selected"' : '').'>'.$row['name'].'</option>';
		}
		return $result;
	}

	public static function get_custom_frequency($schedule_id = NULL)
	{
		$result   = '';
		$selected = NULL;
		if ($schedule_id >= 0)
		{
			$selected = DB::select('custom_frequency')->from('plugin_courses_schedules_has_intervals')->where('schedule_id', '=', $schedule_id)->execute()->get('custom_frequency');
		}
		$q = DB::select('id', 'name')->from('plugin_courses_repeat')
			->where('id', '<', 6)
			->where('id', '>', 2)
			->execute()->as_array();
		foreach ($q AS $key => $row)
		{
			$result .= '<option value="'.$row['id'].'" '.(($selected == $row['id']) ? 'selected="selected"' : '').'>'.$row['name'].'</option>';
		}
		return $result;
	}

	public static function insert_event($data)
	{
		$logged_in_user       = Auth::instance()->get_user();
		$data['created_by']   = $logged_in_user['id'];
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['delete']       = 0;
		$data['publish']      = 1;
		$query                = DB::insert('plugin_courses_schedules_events', array_keys($data))
			->values($data)
			->execute();
		return $query;
	}

	public static function get_events($limit, $offset, $sort, $dir, $search = FALSE)
	{
		$_search = '';
		if ($search)
		{
			$_search = " AND (`plugin_courses_schedules_events`.`datetime_start` like '%".$search."%' OR `plugin_courses_schedules_events`.`datetime_end` like '%".$search."%'  OR `plugin_courses_schedules`.`name` like '%".$search."%')";
		}
		$_limit = ($limit != -1) ? ' LIMIT '.$offset.','.$limit : '';

		$query  = DB::query(Database::SELECT, "SELECT
        `plugin_courses_schedules_events`.`id`,
        `plugin_courses_schedules_events`.`datetime_start`,
        `plugin_courses_schedules_events`.`datetime_end`,
        `plugin_courses_schedules_events`.`publish`,
        `plugin_courses_schedules`.`name` as `schedule`
        FROM
        `plugin_courses_schedules_events`
        LEFT JOIN `plugin_courses_schedules`
        ON
        `plugin_courses_schedules_events`.`schedule_id` = `plugin_courses_schedules`.`id`
        WHERE
        `plugin_courses_schedules_events`.`delete` = 0 ".$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
			->execute()
			->as_array();
		$return = array();
		if (count($query) > 0)
		{
			$i = 0;
			foreach ($query as $elem => $sub)
			{
				$return[$i]['schedule']       = $sub['schedule'];
				$return[$i]['datetime_start'] = $sub['datetime_start'];
				$return[$i]['datetime_end']   = $sub['datetime_end'];
				if ($sub['publish'] == '1')
				{
					$return[$i]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="'.$sub['id'].'"><i class="icon-ok"></i></a>';
				}
				else
				{
					$return[$i]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="'.$sub['id'].'"><i class="icon-ban-circle"></i></a>';
				}
				$return[$i]['remove'] = '<a href="#" class="delete-event" data-id="'.$sub['id'].'">Delete</a>';
				$i++;
			}
		}
		return $return;
	}

	public static function set_publish_event($id, $state)
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
		$query          = DB::update("plugin_courses_schedules_events")
			->set(array(
				'publish'       => $published,
				'modified_by'   => $logged_in_user['id'],
				'date_modified' => date('Y-m-d H:i:s')
			))
			->where('id', '=', $id)
			->execute();
		$response       = array();
		if ($query > 0)
		{
			$response['message'] = 'success';
		}
		else
		{
			$response['message']   = 'error';
			$response['error_msg'] = 'An error occured! Please contact with support!';
		}
		return $response;
	}

	public static function get_count_events($search = FALSE)
	{
		$_search = '';
		if ($search)
		{
			$_search = " AND (`plugin_courses_schedules_events`.`datetime_start` like '%".
				$search."%' OR `plugin_courses_schedules_events`.`datetime_end` like '%".
				$search."%'  OR `plugin_courses_schedules`.`name` like '%".
				$search."%')";
		}
		$query = DB::query(Database::SELECT,
			"SELECT count(*) as `count` FROM `plugin_courses_schedules_events` WHERE `plugin_courses_schedules_events`.`delete` = 0".$_search.";")
			->execute()
			->as_array();
		return $query['0']['count'];
	}

	public static function remove_event($id)
	{
		$logged_in_user = Auth::instance()->get_user();
		$ret            = DB::update('plugin_courses_schedules_events')
			->set(array(
				'modified_by'   => $logged_in_user['id'],
				'date_modified' => date('Y-m-d H:i:s'),
				'delete'        => 1
			))
			->where('id', '=', $id)
			->execute();
		if ($ret > 0)
		{
			$response['message'] = 'success';
		}
		else
		{
			$response['message']   = 'error';
			$response['error_msg'] = 'An error occured! Please contact with support!';
		}
		return $response;
	}

	public static function get_dates_for_location($id, $sid = FALSE, $lid)
	{
		$return = array();

		$date_sql = 'SELECT
                schedules.`id` as `schedule_id`,
                events.`id` as `event_id`,
                events.`datetime_start` as `datetime_start`,
                events.datetime_end,
                locations.`name` as `location`,
                t4.name AS `repeat`
            FROM `plugin_courses_schedules_events` events
            JOIN `plugin_courses_schedules` schedules ON events.`schedule_id` = schedules.`id`
            JOIN `plugin_courses_locations` locations ON schedules.`location_id` = locations.`id`
            LEFT JOIN plugin_courses_repeat AS t4 ON schedules.repeat = t4.id
            WHERE `course_id` = '.$id;
		if ($lid != 0)
		{
			$date_sql .= ' AND `location_id` = '.$lid;
		}
		$date_sql .= ' AND schedules.`publish` = 1 AND schedules.`delete` = 0
            AND    events.`publish` = 1 AND    events.`delete` = 0
            ORDER BY schedules.`id` ASC, `datetime_start` ASC';

		$dates = DB::query(Database::SELECT, $date_sql)->execute()->as_array();
		if (is_array($dates))
		{
			$return['message']  = 'success';
			$return['response'] = '<option value="">TIME &amp; DATE</option>';
			$current_schedule   = '';
			$schedules_dates    = array();
			foreach ($dates as $date)
			{
				if ($current_schedule != $date['schedule_id'])
				{
					$current_schedule                      = $date['schedule_id'];
					$schedules_dates[$date['schedule_id']] = array();
				}
				switch (Settings::instance()->get('course_website_display'))
				{
					case 0: // All Date
						$schedules_dates[] = $schedule;
						break;
					case 1: // Next Date
						$start = date("Y-m-d H:i:s");
						if ($schedule['start_date'] >= $start AND count($schedules_dates) < 1)
						{
							$schedules_dates[] = $schedule;
						}
						break;
					case 2: // Next 7 days
						$start = date("Y-m-d H:i:s");
						$end   = date("Y-m-d H:i:s", strtotime('+ 1 week'));
						if ($schedule['start_date'] >= $start AND $schedule['start_date'] <= $end)
						{
							$schedules_dates[] = $schedule;
						}
						break;
					case 3: // Next 30 days
						$start = date("Y-m-d H:i:s");
						$end   = date("Y-m-d H:i:s", strtotime('+ 30 day'));
						if ($schedule['start_date'] >= $start AND $schedule['start_date'] <= $end)
						{
							$schedules_dates[] = $schedule;
						}
						break;
					case 4: // Next 90 days
						$start = date("Y-m-d H:i:s");
						$end   = date("Y-m-d H:i:s", strtotime('+ 90 day'));
						if ($schedule['start_date'] >= $start AND $schedule['start_date'] <= $end)
						{
							$schedules_dates[] = $schedule;
						}
						break;
					case 5: // Next 365 days
						$start = date("Y-m-d H:i:s");
						$end   = date("Y-m-d H:i:s", strtotime('+ 365 day'));
						if ($schedule['start_date'] >= $start AND $schedule['start_date'] <= $end)
						{
							$schedules_dates[] = $schedule;
						}
						break;
				}
			}
			foreach ($schedules_dates as $schedule)
			{
				foreach ($schedule as $date)
				{
					$return['response'] .= '<option value="'.$date['schedule_id'].'" data-event_id="'.$date['event_id'].'" class="date_option"';
					if ((isset($sid) AND (int) $sid > 0) AND $sid == $date['schedule_id'])
					{
						$return['response'] .= " selected='selected'";
					}
					$return['response'] .= '>';
					if (isset($date['repeat']) AND (!is_null($date['repeat'])))
					{
						echo $date['location'] ?>,
						<?=
						date("H:i D", strtotime($date['date'])).((isset($date['datetime_end'])) ? ' - '.
							date("H:i D", strtotime($date['datetime_end'])) : '').', '.$date['repeat']
						;
					}
					else
					{
						echo $date['location'] ?>,
						<?=
						date("H:i D, jS F", strtotime($date['date'])).((isset($date['datetime_end'])) ? ' - '.
							date("H:i D, jS F", strtotime($date['datetime_end'])) : '')
						;
					}
					$return .= '</option>'.PHP_EOL;
				}
			}
		}
		else
		{
			$return['message'] = 'fail';
		}
		return json_encode($return);

	}

	public static function get_locations_for_data($id, $sid = FALSE)
	{
		$return = array();
		$query  = DB::query(Database::SELECT, "SELECT `plugin_courses_schedules`.`id`,
        `plugin_courses_locations`.`name` as `location`
        FROM
        `plugin_courses_schedules`
        LEFT JOIN
        `plugin_courses_locations`
        ON
        `plugin_courses_locations`.`id` = `plugin_courses_schedules`.`location_id`
        WHERE
        `course_id` = ".$id." GROUP BY plugin_courses_locations.name")
			->execute()
			->as_array();
		if (is_array($query))
		{
			$return['message']  = 'success';
			$return['response'] = '<option value="">LOCATION</option>';
			foreach ($query as $k => $v)
			{
				$return['response'] .= '<option value="'.$v['id'].'"';
				if ((isset($sid) AND (int) $sid > 0) AND $sid == $v['id'])
				{
					$return['response'] .= " selected='selected'";
				}
				$location = $v['location'] ? $v['location'] : 'Not specified';
				$return['response'] .= '>'.$location.'</option>'.PHP_EOL;
			}
		}
		else
		{
			$return['message'] = 'fail';
		}
		return json_encode($return);
	}

	public static function get_all_dates_for_location($cid)
	{
		$return = array();
		$query  = DB::query(Database::SELECT, "SELECT `id`,
        `start_date`
        FROM
        `plugin_courses_schedules`
        WHERE
        `course_id` =".$cid)
			->execute()
			->as_array();
		if (is_array($query))
		{
			$return['message']  = 'success';
			$return['response'] = '<option value="">TIME &amp; DATE</option>';
			foreach ($query as $k => $v)
			{
				$return['response'] .= '<option value="'.$v['id'].'"';
				$return['response'] .= '>'.date("l, jS F Y", strtotime($v['start_date'])).'</option>'.PHP_EOL;
			}
		}
		else
		{
			$return['message'] = 'fail';
		}
		return json_encode($return);

	}

	public static function get_all_locations_for_data($cid)
	{
		$return = array();
		$query  = DB::query(Database::SELECT, "SELECT `plugin_courses_schedules`.`id`,
        `plugin_courses_locations`.`name` as `location`
        FROM
        `plugin_courses_schedules`
        LEFT JOIN
        `plugin_courses_locations`
        ON
        `plugin_courses_locations`.`id` = `plugin_courses_schedules`.`location_id`
        WHERE
        `plugin_courses_schedules`.`course_id` = ".$cid)
			->execute()
			->as_array();
		if (is_array($query))
		{
			$return['message']  = 'success';
			$return['response'] = '<option value="">LOCATION</option>';
			foreach ($query as $k => $v)
			{
				$return['response'] .= '<option value="'.$v['id'].'"';
				if ((isset($sid) AND (int) $sid > 0) AND $sid == $v['id'])
				{
					$return['response'] .= " selected='selected'";
				}
				$location = $v['location'] ? $v['location'] : 'Not specified';
				$return['response'] .= '>'.$location.'</option>'.PHP_EOL;
			}
		}
		else
		{
			$return['message'] = 'fail';
		}
		return json_encode($return);
	}

	public static function get_details_for_schedule($id)
	{
		$return = array();

		$select = "SELECT
        `plugin_courses_schedules`.`id`,
        `plugin_courses_schedules_events`.`id` as `event_id`,
        `plugin_courses_schedules`.`description`,
        `plugin_courses_schedules`.`start_date`,
        `plugin_courses_schedules_events`.`datetime_start`,
        `plugin_courses_schedules`.`duration`,
        `plugin_courses_repeat`.`name` AS `repeat`,
        `plugin_courses_schedules`.`weekdays_monday`,
        `plugin_courses_schedules`.`weekdays_tuesday`,
        `plugin_courses_schedules`.`weekdays_wednesday`,
        `plugin_courses_schedules`.`weekdays_thursday`,
        `plugin_courses_schedules`.`weekdays_friday`,
        `plugin_courses_schedules`.`weekdays_saturday`,
        `plugin_courses_schedules`.`weekdays_sunday`,
        `plugin_courses_locations`.`name` as `location_name`,
        CONCAT (`contact`.`first_name`, ' ', `contact`.`last_name`) AS `trainer_name`,
        `plugin_courses_locations`.`address1` as `location_address1`,
        `plugin_courses_locations`.`address2` as `location_address2`,
        `plugin_courses_locations`.`address3` as `location_address3`,
        `plugin_courses_counties`.`name` as `location_county`,
        `plugin_courses_cities`.`name` as `location_city`,
        `plugin_courses_location_types`.`type` as `location_type`,
        CONCAT (`contact`.`first_name`, ' ', `contact`.`last_name`) as `trainer`
        FROM
        `plugin_courses_schedules`
        LEFT JOIN
        `plugin_courses_locations`
        ON
        `plugin_courses_schedules`.`location_id` = `plugin_courses_locations`.`id`
        LEFT JOIN
        `plugin_courses_schedules_events`
        ON
        `plugin_courses_schedules_events`.`schedule_id` = `plugin_courses_schedules`.`id`
        LEFT JOIN
        `plugin_courses_location_types`
        ON
        `plugin_courses_locations`.`location_type_id` = `plugin_courses_location_types`.`id`
        LEFT JOIN
        `plugin_courses_counties`
        ON
        `plugin_courses_locations`.`county_id` = `plugin_courses_counties`.`id`
        LEFT JOIN
        `plugin_courses_cities`
        ON
        `plugin_courses_locations`.`city_id` = `plugin_courses_cities`.`id`
        LEFT JOIN
        `plugin_contacts_contact`
        ON
        `plugin_courses_schedules`.`trainer_id` = `plugin_contacts_contact`.`id`
        LEFT JOIN
        `plugin_courses_repeat`
        ON
        `plugin_courses_repeat`.`id` = `plugin_courses_schedules`.`repeat` ";
		if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
		{
			$select .= "LEFT JOIN `plugin_contacts3_contacts` `contact` ON `plugin_courses_schedules_events`.`trainer_id` = `contact`.`id` "
				."LEFT JOIN `plugin_contacts3_contact_has_roles` `has_role` ON  `has_role`.`contact_id` = `contact`.`id` "
				."WHERE `has_role`.`role_id` = 4 AND `contact`.`id` = `plugin_courses_schedules`.`trainer_id` AND ";
		}
		else
		{
			$select .= "LEFT JOIN
                `plugin_courses_trainers`
                ON
                `plugin_courses_schedules_events`.`trainer_id` = `plugin_courses_trainers`.`id`
                WHERE ";
		}

//        $query = DB::query(Database::SELECT, $select . " WHERE " );

		if (!is_null($id))
		{
			$select .= "`plugin_courses_schedules_events`.`schedule_id` = ".$id." AND";
		}

		$query = DB::query(Database::SELECT, $select."
        `plugin_courses_schedules_events`.`publish` = 1
        AND
        `plugin_courses_schedules_events`.`delete` = 0"
		)->execute()->as_array();
		if (is_array($query) AND count($query) > 0)
		{
			$start_d               = explode(" ", $query['0']['datetime_start']);
			$start_t               = explode(":", $start_d['1']);
			$return['id']          = $query['0']['id'];
			$return['description'] = $query['0']['description'];
			$return['date']        = "Date: ".date("l, jS F Y", strtotime($query['0']['datetime_start']));
			$return['duration']    = "Duration: ".$query['0']['duration'];
			if (isset($query['0']['frequency']) && !is_null($query['0']['frequency']))
			{
				$return['frequency'] = "Frequency: ".$query['0']['frequency'];
			}
			else
			{
				$return['frequency'] = '';
			}
			$q                    = DB::select(array('plugin_courses_repeat.name', 'repeat'), array(DB::expr('CONCAT(plugin_courses_trainers.first_name,\' \',plugin_courses_trainers.last_name)'), 'full_name'))->from('plugin_courses_repeat')
				->join('plugin_courses_schedules', 'LEFT')->on('plugin_courses_schedules.repeat', '=', 'plugin_courses_repeat.id')
				->join('plugin_courses_trainers', 'LEFT')->on('plugin_courses_schedules.trainer_id', '=', 'plugin_courses_trainers.id')
				->where('plugin_courses_schedules.id', '=', $query[0]['id'])->execute()->as_array();
			$repeat               = (count($q) > 0) ? $q[0]['repeat'] : '';
			$return['start_time'] = "Start time: ".$start_t['0'].":".$start_t['1'];
			$return['location']   = "Location: ".$query['0']['location_name']; // KES-296 . " " . $query['0']['location_address1'] . " " . $query['0']['location_address2'] . " " . $query['0']['location_address3'] . "<br>" . $query['0']['location_city'] . ", co. " . $query['0']['location_county'];
			$return['trainer']    = "Trainer: ".((!is_null($query[0]['trainer_name'])) ? $query[0]['trainer_name'] : $query['0']['trainer']);
			$return['trainer']    = ((isset($q[0]['full_name']) AND !empty($q[0]['full_name'])) ? "Trainer: ".$q[0]['full_name'] : $return['trainer']);
			$return['repeat']     = $repeat;
			$return['message']    = 'success';
			$events               = array();
			if (!is_null($id))
			{
				$events = DB::query(Database::SELECT,
					"SELECT *
                    FROM `plugin_courses_schedules_events`
                    WHERE `schedule_id` = ".$id."
                    AND `delete` = 0 AND `publish` = 1
                    ORDER BY `datetime_start`")
					->execute()
					->as_array();
			}

			if (is_array($events) AND count($events) > 0)
			{
				$return['days'] = "Days: ".count($events)." days";
				$_times         = 0;
				foreach ($events as $ev => $vv)
				{
					$_times = $_times + (strtotime($vv['datetime_end']) - strtotime($vv['datetime_start']));
				}
				$return['time'] = "Time: " . ($_times / 3600) . " hours";
			}
			else
			{
				$return['days'] = "Days: ".'0';
				$return['time'] = "Hours: ".'0';
			}
		}
		else
		{
			$return['message'] = 'failed';
		}
		return json_encode($return);
	}

	public static function get_details_for_schedule_event($id)
	{
		if (!is_numeric($id)) {
			return array(
				'message' => __('No schedule selected')
			);
		}

		$return = array();

		$select = "SELECT
        `plugin_courses_schedules`.`id`,
        `plugin_courses_schedules`.`book_on_website`,
        plugin_courses_schedules.is_group_booking,
        `plugin_courses_schedules_events`.`id` as `event_id`,
        `plugin_courses_schedules`.`description`,
        `plugin_courses_schedules`.`allow_purchase_order`,
        `plugin_courses_schedules`.`allow_credit_card`,
        `plugin_courses_schedules`.`allow_sales_quote`,
        `plugin_courses_schedules`.`start_date`,
        `plugin_courses_schedules_events`.`datetime_start`,
        `plugin_courses_schedules`.`duration`,
        `plugin_courses_repeat`.`name` AS `repeat`,
        `plugin_courses_schedules`.`weekdays_monday`,
        `plugin_courses_schedules`.`weekdays_tuesday`,
        `plugin_courses_schedules`.`weekdays_wednesday`,
        `plugin_courses_schedules`.`weekdays_thursday`,
        `plugin_courses_schedules`.`weekdays_friday`,
        `plugin_courses_schedules`.`weekdays_saturday`,
        `plugin_courses_schedules`.`weekdays_sunday`,
        `plugin_courses_locations`.`name` as `location_name`,
        IFNULL(CONCAT (`contact`.`first_name`, ' ', `contact`.`last_name`),CONCAT (`contactm`.`first_name`, ' ', `contactm`.`last_name`)) AS `trainer_name`,
        `plugin_courses_locations`.`address1` as `location_address1`,
        `plugin_courses_locations`.`address2` as `location_address2`,
        `plugin_courses_locations`.`address3` as `location_address3`,
        `plugin_courses_counties`.`name` as `location_county`,
        `plugin_courses_cities`.`name` as `location_city`,
        `plugin_courses_location_types`.`type` as `location_type`,
        CONCAT (`contact`.`first_name`, ' ', `contact`.`last_name`) as `trainer`
        FROM
        `plugin_courses_schedules`
        INNER JOIN
        `plugin_courses_schedules_events`
        ON
        `plugin_courses_schedules_events`.`schedule_id` = `plugin_courses_schedules`.`id`
        LEFT JOIN
        `plugin_courses_locations`
        ON
        `plugin_courses_schedules`.`location_id` = `plugin_courses_locations`.`id`
        LEFT JOIN
        `plugin_courses_location_types`
        ON
        `plugin_courses_locations`.`location_type_id` = `plugin_courses_location_types`.`id`
        LEFT JOIN
        `plugin_courses_counties`
        ON
        `plugin_courses_locations`.`county_id` = `plugin_courses_counties`.`id`
        LEFT JOIN
        `plugin_courses_cities`
        ON
        `plugin_courses_locations`.`city_id` = `plugin_courses_cities`.`id`
        LEFT JOIN
        `plugin_courses_repeat`
        ON
        `plugin_courses_repeat`.`id` = `plugin_courses_schedules`.`repeat`
        LEFT JOIN `plugin_contacts3_contacts` `contact` ON `plugin_courses_schedules_events`.`trainer_id` = `contact`.`id`
        LEFT JOIN `plugin_contacts3_contact_has_roles` `has_role` ON  `has_role`.`contact_id` = `contact`.`id` AND `has_role`.`role_id` = 4 AND `contact`.`id` = `plugin_courses_schedules`.`trainer_id`
        LEFT JOIN `plugin_contacts3_contacts` `contactm` ON `plugin_courses_schedules`.`trainer_id` = `contactm`.`id`
        LEFT JOIN `plugin_contacts3_contact_has_roles` `has_rolem` ON  `has_rolem`.`contact_id` = `contactm`.`id` AND `has_rolem`.`role_id` = 4 AND `contactm`.`id` = `plugin_courses_schedules`.`trainer_id`
        WHERE
        `plugin_courses_schedules_events`.`id` = ".$id." AND
        `plugin_courses_schedules_events`.`publish` = 1
        AND
        `plugin_courses_schedules_events`.`delete` = 0";

		$query = DB::query(Database::SELECT, $select)->execute()->as_array();
		if (is_array($query) AND count($query) > 0)
		{
			$start_d               = explode(" ", $query['0']['datetime_start']);
			$start_t               = explode(":", $start_d['1']);
			$return['id']          = $query['0']['id'];
			$return['is_group_booking'] = $query['0']['is_group_booking'];
            $return['allow_purchase_order'] = $query['0']['allow_purchase_order'];
            $return['allow_credit_card'] = $query['0']['allow_credit_card'];
            $return['allow_sales_quote'] = $query['0']['allow_sales_quote'];
			$return['description'] = $query['0']['description'];
            $return['book_on_website'] = $query['0']['book_on_website'];
			$return['date']        = "Date: ".date("l, jS F Y", strtotime($query['0']['datetime_start']));
			$return['duration']    = "Duration: ".$query['0']['duration'];
			if (isset($query['0']['frequency']) && !is_null($query['0']['frequency']))
			{
				$return['frequency'] = "Frequency: ".$query['0']['frequency'];
			}
			else
			{
				$return['frequency'] = '';
			}
			$q                    = DB::select(array('plugin_courses_repeat.name', 'repeat'), array(DB::expr('CONCAT(plugin_courses_trainers.first_name,\' \',plugin_courses_trainers.last_name)'), 'full_name'))->from('plugin_courses_repeat')
				->join('plugin_courses_schedules', 'LEFT')->on('plugin_courses_schedules.repeat', '=', 'plugin_courses_repeat.id')
				->join('plugin_courses_trainers', 'LEFT')->on('plugin_courses_schedules.trainer_id', '=', 'plugin_courses_trainers.id')
				->where('plugin_courses_schedules.id', '=', $query[0]['id'])->execute()->as_array();
			$repeat               = (count($q) > 0) ? $q[0]['repeat'] : '';
			$return['start_time'] = "Start time: ".$start_t['0'].":".$start_t['1'];
			$return['location']   = "Location: ".$query['0']['location_name']; // KES-296 . " " . $query['0']['location_address1'] . " " . $query['0']['location_address2'] . " " . $query['0']['location_address3'] . "<br>" . $query['0']['location_city'] . ", co. " . $query['0']['location_county'];
			$return['trainer']    = "Trainer: ".((!is_null($query[0]['trainer_name'])) ? $query[0]['trainer_name'] : $query['0']['trainer']);
			$return['trainer']    = ((isset($q[0]['full_name']) AND !empty($q[0]['full_name'])) ? "Trainer: ".$q[0]['full_name'] : $return['trainer']);
			$return['repeat']     = $repeat;
			$return['message']    = 'success';
			$events               = array();
			if (!is_null($id))
			{
				$events = DB::query(Database::SELECT,
					"SELECT *
                    FROM `plugin_courses_schedules_events`
                    WHERE `schedule_id` = ".$id."
                    AND `delete` = 0 AND `publish` = 1
                    ORDER BY `datetime_start`")
					->execute()
					->as_array();
			}

			if (is_array($events) AND count($events) > 0)
			{
				$return['days'] = "Days: ".count($events)." days";
				$_times         = 0;
				foreach ($events as $ev => $vv)
				{
					$_times = $_times + (strtotime($vv['datetime_end']) - strtotime($vv['datetime_start']));
				}
				$return['time'] = "Time: " . ($_times / 3600) . " hours";
			}
			else
			{
				$return['days'] = "Days: ".'0';
				$return['time'] = "Hours: ".'0';
			}
		}
		else
		{
			$return['message'] = 'failed';
		}
		return json_encode($return);
	}

	public static function get_course_and_schedule_short($id, $event_id = NULL)
	{

		$query = DB::select(
			array('plugin_courses_schedules.id', 'id'),
			array('plugin_courses_schedules_events.id', 'event_id'),
			array('plugin_courses_schedules.name', 'name'),
			array('plugin_courses_schedules.start_date', 'start_date'),
			array('plugin_courses_schedules_events.datetime_start', 'datetime_start'),
			array('plugin_courses_courses.title', 'title'),
			array('plugin_courses_schedules.is_fee_required', 'is_fee_required'),
			array('plugin_courses_schedules.fee_amount', 'fee_amount'),
            DB::expr("IF(plugin_courses_schedules.fee_per = 'Timeslot', plugin_courses_schedules_events.fee_amount, plugin_courses_schedules.fee_amount) AS fee_amount"),
            array('plugin_courses_schedules.fee_per', 'fee_per'),
			array('plugin_courses_locations.name', 'location')
		)
			->from('plugin_courses_schedules')
			->join('plugin_courses_locations', 'left')
			->on('plugin_courses_locations.id', '=', 'plugin_courses_schedules.location_id')
			->join('plugin_courses_courses', 'left')
			->on('plugin_courses_courses.id', '=', 'plugin_courses_schedules.course_id')
			->join('plugin_courses_schedules_events')
			->on('plugin_courses_schedules_events.schedule_id', '=', 'plugin_courses_schedules.id')
			->where('plugin_courses_schedules.id', '=', ':id')
			->and_where('plugin_courses_schedules.publish', '=', 1)
			->and_where('plugin_courses_schedules.delete', '=', 0)
			->and_where('plugin_courses_courses.publish', '=', 1)
			->and_where('plugin_courses_courses.deleted', '=', 0)
			->and_where('plugin_courses_locations.publish', '=', 1)
			->and_where('plugin_courses_locations.delete', '=', 0)
			->and_where('plugin_courses_schedules_events.publish', '=', 1)
			->and_where('plugin_courses_schedules_events.delete', '=', 0)
			->bind(':id', $id);

		if (!is_null($event_id))
		{
			$query = $query->and_where('plugin_courses_schedules_events.id', '=', ':eid')->bind(':eid', $event_id);
		}
		$schedule = $query->execute()->current();

		return $schedule;
	}

	public static function get_event_details($id)
	{
		$event = DB::select(
            'schedule_fee_amount',
            'schedule_fee_per',
            'schedule_allow_price_override',
			'event.id', 'event.datetime_start', 'event.datetime_end',
			'event.schedule_id', array('schedule.name', 'schedule'), 'schedule.is_fee_required', 'schedule.fee_per',
            DB::expr("IF(schedule.fee_per = 'Timeslot', IFNULL(event.fee_amount, schedule.fee_amount), schedule.fee_amount) AS fee_amount"),
			'schedule.course_id', array('course.title', 'course'),
			'schedule.location_id', array('location.name', 'location'),
			DB::expr("DATE_FORMAT(event.datetime_start, '%W %d %M') as date_formatted")
		)
			->from(array('plugin_courses_schedules_events', 'event'))
			->join(array('plugin_courses_schedules', 'schedule'))->on('event.schedule_id', '=', 'schedule.id')
			->join(array('plugin_courses_courses', 'course'))->on('schedule.course_id', '=', 'course.id')
			->join(array('plugin_courses_locations', 'location'), 'left')->on('schedule.location_id', '=', 'location.id')
			->where('event.id', '=', ':id')
			->bind(':id', $id)
			->execute()
			->current();

        if ($event) {
            if ($event['fee_amount'] == null && $event['schedule_fee_amount'] != null) {
                $event['fee_amount'] = $event['schedule_fee_amount'];
                $event['fee_per'] = $event['schedule_fee_per'];
            }
        }
		return $event;
	}

	public static function render_calendar($month = FALSE, $year = FALSE)
	{
		if ($month === FALSE)
		{
			$month = date('m');
		}
		if ($year === FALSE)
		{
			$year = date('Y');
		}

		$calendar  = new Calendar($month, $year);
		$schedules = self::get_front_events($month, $year);
		if (is_array($schedules) AND count($schedules) > 0)
		{
			foreach ($schedules as $s_elem => $s_val)
			{
				$calendar->attach(
					$calendar->event($s_val['title'])
						->condition('timestamp', date('U', strtotime($s_val['datetime_start'])))
						->output("<span style='background-color:blue;display:block;' class='blue_day' title='".$s_val['name']."<br>".$s_val['location']."'>&nbsp;&nbsp;&nbsp;&nbsp;</span>")
				);
			}
		}
		$news = Model_News::get_news_for_calendar_feed($month, $year);
		if (is_array($news) AND count($news) > 0)
		{
			foreach ($news as $n_elem => $n_val)
			{
				$calendar->attach(
					$calendar->event($n_val['title'])
						->condition('timestamp', date('U', strtotime($n_val['date_publish'])))
						->output("<span style='background-color:green;display:block;' class='green_day' title='".$n_val['summary']."'>&nbsp;&nbsp;&nbsp;&nbsp;</span>")
				);
			}
		}
		$view = View::factory(
			'front_end/snippets/calendar_snippet',
			array(
				'content' => $calendar->render()
			));
		return $view;
	}

	public static function get_front_events($month = FALSE, $year = FALSE)
	{
		if ($month === FALSE)
		{
			$month = date("m");
		}
		if ($year === FALSE)
		{
			$year = date("Y");
		}
		$query = DB::query(Database::SELECT, "SELECT
        `plugin_courses_schedules_events`.`datetime_start`,
        `plugin_courses_schedules`.`name`,
        `plugin_courses_locations`.`name` as `location`,
        `plugin_courses_courses`.`title`
        FROM
        `plugin_courses_schedules_events`
        LEFT JOIN
        `plugin_courses_schedules`
        ON
        `plugin_courses_schedules_events`.`schedule_id` = `plugin_courses_schedules`.`id`
        LEFT JOIN
        `plugin_courses_locations`
        ON
        `plugin_courses_schedules`.`location_id` = `plugin_courses_locations`.`id`
        LEFT JOIN
        `plugin_courses_courses`
        ON
        `plugin_courses_schedules`.`course_id` = `plugin_courses_courses`.`id`
        WHERE
        DATE_FORMAT(`plugin_courses_schedules_events`.`datetime_start`, '%Y-%m') = '".$year."-".$month."'
        AND
        `plugin_courses_schedules`.`delete` = 0
        AND
        `plugin_courses_schedules`.`publish` = 1
        AND
        `plugin_courses_schedules_events`.`delete` = 0
        AND
        `plugin_courses_schedules_events`.`publish` = 1
        ORDER BY
        `plugin_courses_schedules_events`.`datetime_start`")
			->execute()
			->as_array();
		return $query;
	}

	public static function get_schedule_price_by_id($id, $event_id = null)
	{
        if (is_numeric($event_id)) {
            $schedule = DB::query(Database::SELECT,
                "SELECT
                    is_fee_required,IF(fee_per = 'Timeslot', IFNULL(e.fee_amount, s.fee_amount), s.fee_amount) AS fee_amount,fee_per,book_on_website,
                    schedule_is_fee_required, schedule_fee_amount, schedule_fee_per, schedule_allow_price_override, allow_purchase_order, allow_credit_card, allow_sales_quote
                  FROM plugin_courses_schedules s
                    INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id
                    INNER JOIN plugin_courses_courses ON s.course_id = plugin_courses_courses.id
                  WHERE e.id = " . $event_id . " LIMIT 1")
                ->execute()->current();
        } else {
            $schedule = DB::query(Database::SELECT,
                "SELECT
                    is_fee_required,fee_amount,fee_per,book_on_website,
                    schedule_is_fee_required, schedule_fee_amount, schedule_fee_per, schedule_allow_price_override, 
                    allow_purchase_order, allow_credit_card, allow_sales_quote
                  FROM plugin_courses_schedules
                    INNER JOIN plugin_courses_courses ON plugin_courses_schedules.course_id = plugin_courses_courses.id
                  WHERE plugin_courses_schedules.id = " . $id . " LIMIT 1")
                ->execute()->current();
        }
        $return = array('book_on_website' => $schedule['book_on_website']);
        $return['fee_per'] = $schedule['fee_per'];
		$return['fee_amount'] = $schedule['fee_amount'];
        if ($schedule['schedule_allow_price_override'] == 0 && $schedule['fee_amount'] == null) {
            $schedule['is_fee_required'] = $schedule['schedule_is_fee_required'];
            $schedule['fee_per'] = $schedule['schedule_fee_per'];
            $schedule['fee_amount'] = $schedule['schedule_fee_amount'];
        }

		$return['discount'] = 0;
        $return['allow_purchase_order'] = $schedule['allow_purchase_order'];
        $return['allow_credit_card'] = $schedule['allow_credit_card'];
        $return['allow_sales_quote'] = $schedule['allow_sales_quote'];
        $return['allow_booking'] = ($return['allow_purchase_order'] || $return['allow_credit_card'] || $return['allow_sales_quote']) ? '1' : '0';
        $return['discount'] = 0;
		$discounts = Model_CourseBookings::get_available_discounts(null, array(array('id' => $id, 'fee' => $return['fee_amount'], 'discount' => 0, 'prepay' => 1)));
		if (isset($discounts[0])) {
			if ($discounts[0]['discount'] > 0) {
				$return['discount'] = $discounts[0]['discount'];
			}
		}

        $return['is_wishlisted'] = Auth::instance()->get_user_orm()->get_contact()->has_wishlisted($id);

		$return['fee_amount'] = ($schedule['is_fee_required'] == '1') ? "€" . strval($schedule['fee_amount']) : "No Fee";
		$return['price'] = ($schedule['is_fee_required'] == '1') ? "€" . strval($schedule['fee_amount'] - $return['discount']) : "No Fee";


		return $return;
	}

	public static function get_trainer_by_id($id)
	{
        $id = (int)$id;
		$return = DB::query(Database::SELECT, "SELECT CONCAT(c.first_name,' ',c.last_name) AS trainer FROM plugin_contacts3_contacts c INNER JOIN plugin_courses_schedules s ON c.id = s.trainer_id WHERE s.id =".$id." LIMIT 1")
			->execute()->current();
		return $return;
	}

	public static function save_timetable_and_schedule($timeslots, $schedule_id, $timetable_id, $blackout_event_ids, $new_timetable = FALSE, $return_id = FALSE)
	{
		//header('content-type: text/plain');print_r(func_get_args());exit;
        $message        = '';
		$calendar_dates = self::get_calendar_dates($blackout_event_ids);

		$user          = Auth::instance()->get_user();
		$success       = TRUE;
		$table_columns = array("schedule_id", "datetime_start", "datetime_end", "monitored", "fee_amount", "publish", "delete", "timetable_id", "trainer_id", "modified_by", "topic_id", "location_id");
		DB::delete(self::HAS_ENGINE_EVENTS)
			->where('schedule_id', '=', $schedule_id)
			->execute();
		if (is_array($blackout_event_ids))
		{
			foreach ($blackout_event_ids as $blackout_event_id)
			{
				DB::insert(self::HAS_ENGINE_EVENTS, array('schedule_id', 'engine_calendar_event_id'))
					->values(array($schedule_id, $blackout_event_id))
					->execute();
			}
		}
		$event_ids = array();
		foreach ($timeslots AS $timeslot)
		{
			$date          = date("Y-m-d", strtotime($timeslot['datetime_start']));
			$valid         = self::check_event_for_calendar_dates($date, $calendar_dates);
			if (!$valid)
			{
				$message .= 'This date:'.date('d M', strtotime($date)).' is a Blackout Date.'."\n";
			}
			else
			{
                $blade = '';
				// Data does not exist in the schedule                  INSERT
				if (!is_numeric(@$timeslot['id']))
				{
					$blade = 'create';
				}
				// Data exist in schedule_event and updated data        UPDATE
				if (is_numeric(@$timeslot['id']))
				{
                    $blade = 'update';
                    if (@$timeslot['delete'] == 1) {
                        $blade = 'delete';
                    }
				}


				switch ($blade)
				{
                    case 'create':
                        $row = array();
                        $row['datetime_start'] = $timeslot['datetime_start'];
                        $row['datetime_end'] = $timeslot['datetime_end'];
                        $row['monitored'] = $timeslot['monitored'];
                        $row['trainer_id'] = $timeslot['trainer_id'];
						$row['topic_id'] = @$timeslot['topic_id'];
                        $row['fee_amount'] = $timeslot['fee_amount'] ? $timeslot['fee_amount'] : null;
						$row['max_capacity'] = @$timeslot['max_capacity'] ?: null;
                        $row['schedule_id'] = $schedule_id;
                        $row['timetable_id'] = $timetable_id;
                        $row['location_id'] = @$timeslot['location_id'];

                        $row['date_created'] = date::now();
                        $row['created_by'] = $user['id'];
						$answer       = DB::insert('plugin_courses_schedules_events')
                            ->values($row)
                            ->execute();
						$event_ids[]  = $answer[0];
						// Update KES Bookings
						if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
						{
							$bookings = DB::select('has_schedules.booking_id', 'schedules.booking_type', 'booking.booking_status', array('applications.data', 'application'))
                                ->from(array('plugin_ib_educate_booking_has_schedules', 'has_schedules'))
                                    ->join(array(self::TABLE_SCHEDULES, 'schedules'), 'inner')
                                        ->on('has_schedules.schedule_id', '=', 'schedules.id')
                                    ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'booking'), 'inner')
                                        ->on('has_schedules.booking_id', '=','booking.booking_id')
									->join(array(Model_KES_Bookings::BOOKING_APPLICATIONS, 'applications'), 'left')
										->on('booking.booking_id', '=', 'applications.booking_id')
								->where('schedule_id', '=', $schedule_id)
                                ->and_where('has_schedules.deleted', '=', 0)
                                ->execute()->as_array();
							// ADD period attending
							foreach ($bookings as $key => $booking)
							{
                                if ($booking['booking_type'] == 'Whole Schedule') {
									$insert_booking_item = true;
									if ($booking['application'] != '') {
										$booking['application'] = @json_decode($booking['application'], true);
										if (@$booking['application']['has_period']) {
											$timeslot_match_period = DB::select('*')
												->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
												->where('id', '=', $answer[0])
												->and_where('delete', '=', 0)
												->and_where(DB::expr("CONCAT_WS(',', DATE_FORMAT(datetime_start, '%a %H:%i'), trainer_id)"), 'in', $booking['application']['has_period'])
												->execute()
												->current();
											if (!$timeslot_match_period) {
												$insert_booking_item = false;
											}
										}
									}
									if ($insert_booking_item) {
										$booking_item_id = DB::insert(
											'plugin_ib_educate_booking_items',
											array(
												'attending',
												'booking_id',
												'period_id',
												'booking_status'
											)
										)->values(
											array(
												1,
												$booking['booking_id'],
												$answer[0],
												$booking['booking_status']
											)
										)->execute();
										// Add a note for Created timeslots
										$table_id = Model_EducateNotes::get_table_link_id_from_name('plugin_ib_educate_booking_items');
										$notes = array(
												'note' => 'This timeslot added after booking',
												'link_id' => $booking_item_id[0],
												'table_link_id' => $table_id
										);
										DB::insert('plugin_contacts3_notes', array_keys($notes))->values($notes)->execute();
									}
                                }
							}
						}
						break;
					case 'update':
                        $row['datetime_start'] = $timeslot['datetime_start'];
                        $row['datetime_end'] = $timeslot['datetime_end'];
                        $row['monitored'] = $timeslot['monitored'];
                        $row['trainer_id'] = $timeslot['trainer_id'];
						$row['topic_id'] = @$timeslot['topic_id'];
                        $row['fee_amount'] = $timeslot['fee_amount'] ? $timeslot['fee_amount'] : null;
                        $row['max_capacity'] = @$timeslot['max_capacity'] ?: null;
                        $row['schedule_id'] = $schedule_id;
                        $row['timetable_id'] = $timetable_id;
                        $row['location_id'] = @$timeslot['location_id'];

                        $answer = DB::update('plugin_courses_schedules_events')
							->set($row)
							->where('id', '=', $timeslot['id'])
							->execute();
						$event_ids[]  = $timeslot['id'];
						break;
					case 'delete':
						$answer = DB::update('plugin_courses_schedules_events')
							->set(array('delete' => 1, 'publish' => 0))
							->where('id', '=', $timeslot['id'])
							->execute();
						// Update KES Bookings
						if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
						{
							// Update period attending
							DB::update('plugin_ib_educate_booking_items')
								->set(array('attending' => 0))
								->where('period_id', '=', $timeslot['id'])
								->execute();
							$booking_item_ids = DB::select('booking_item_id')
								->from('plugin_ib_educate_booking_items')
								->where('period_id', '=', $timeslot['id'])
								->execute()
								->as_array();
							foreach ($booking_item_ids as $booking_item_id) {
								// Add a note for Cancelled timeslots
								$table_id = Model_EducateNotes::get_table_link_id_from_name('plugin_ib_educate_booking_items');
								$notes = array(
										'note' => 'The timeslot has been cancelled',
										'link_id' => $booking_item_id['booking_item_id'],
										'table_link_id' => $table_id
								);
								DB::insert('plugin_contacts3_notes', array_keys($notes))->values($notes)->execute();
							}
						}
						break;
                    default:
                        $event_ids[]  = $timeslot['id'];
                        $answer = $timeslot['id'];
                        break;
				}
				if (!$answer)
				{
					$success = FALSE;
				}
				;
				unset($table_rows);
			}
		}

		if ($event_ids) {
			DB::update('plugin_courses_schedules_events')
					->set(array('delete' => 1, 'publish' => 0))
					->where('schedule_id', '=', $schedule_id)
					->and_where('id', 'not in', $event_ids)
					->execute();
			$event_id_printer = "(";
            foreach($event_ids as $event_id) {
                $event_id_printer .= $event_id ;
                $event_id_printer .= (($event_ids[count($event_ids) - 1] !== $event_id)) ? ', ' : '';
            }
            $event_id_printer .= ")";
            DB::query(Database::UPDATE, "UPDATE `plugin_ib_educate_booking_items` booking_items
                INNER JOIN `plugin_courses_schedules_events` `schedule_event`
                    ON `booking_items`.`period_id` = `schedule_event`.id
                SET
                `booking_items`.`delete` = 1
                WHERE schedule_event.schedule_id = " . $schedule_id . " AND " .
                "booking_items.period_id NOT IN {$event_id_printer} AND " .
                "booking_items.booking_status <> 5"
                )
                ->execute();
            
			/*DB::delete(self::TABLE_TIMESLOTS)
					->where('schedule_id', '=', $schedule_id)
					->and_where('id', 'not in', $event_ids)
					->execute();*/
		}

		if ($message != '')
		{
			IbHelpers::set_message($message, 'error popup_box');
			$errors = array(IbHelpers::get_messages());
		}

		// Removed Copy timetable See Stac-99
		$timetable_name = DB::select('timetable_name')
			->from('plugin_courses_timetable')
			->where('id', '=', $timetable_id)
			->execute()
			->get('timetable_name');

		$result = array('schedule' => ($return_id) ? $schedule_id : $success, 'message' => isset($errors) ? $errors : '');
		return $result;

	}

	public static function get_all_schedules_by_timetable($timetable_id = NULL, $schedule_id = NULL, $with_id = FALSE)
	{
		$query = DB::select('schedule_id', 'id')
			->from('plugin_courses_schedules_events');
		if (!is_null($timetable_id))
		{
			$query->where('timetable_id', '=', $timetable_id);
		}
		else
		{
			$query->where('schedule_id', '=', $schedule_id);
		}
		$query  = $query->and_where('delete', '=', 0)
			->and_where('publish', '=', 1)
//            ->group_by('schedule_id')
			->execute()
			->as_array();
		$result = array();
		foreach ($query AS $schedules => $schedule)
		{

			$result[] = $with_id ? array('schedule_id' => $schedule['schedule_id'], 'id' => $schedule['id']) : $schedule['schedule_id'];
		}
		return $result;
	}

	public static function get_timetables()
	{
		$query  = DB::select('id', 'timetable_name')
			->from('plugin_courses_timetable')
			->where('publish', '=', '1')
			->and_where('delete', '=', '0')
			->execute()
			->as_array();
		$result = '<option value="select">---Select a timetable---</option>';
		foreach ($query AS $timetable => $time)
		{
			$result .= '<option value="'.$time['id'].'">'.$time['timetable_name'].'</option>';
		}
		return $result;
	}

	public static function get_timetable_dates($timetable_id, $schedule = null)
	{
        Model_KES_Bookings::tmp_booking_count();
        $result = array();
		if (($timetable_id === 'select' || $timetable_id === 'new') && $schedule == null)
		{

		}
		else {
			if ($schedule == null) {
				$get_schedule = DB::select('schedule_id', 'fee_per')
						->from('plugin_courses_schedules_events')
						->join('plugin_courses_schedules', 'inner')
						->on('plugin_courses_schedules_events.schedule_id', '=', 'plugin_courses_schedules.id')
						->where('timetable_id', '=', $timetable_id)
						->and_where('plugin_courses_schedules_events.delete', '=', 0)
						->limit(1)
						->execute()
						->as_array();
				if (count($get_schedule) === 0) {
					return $result;
				}
				$schedule = $get_schedule[0]['schedule_id'];
			}
            $result = DB::select('*', 'tmp_timeslot_booking_counts.booking_count')
				->from(array('plugin_courses_schedules_events', 'timeslots'))
                    ->join('tmp_timeslot_booking_counts', 'left')->on('timeslots.id', '=', 'tmp_timeslot_booking_counts.timeslot_id')
				->where('schedule_id', '=', $schedule)
				->and_where('delete', '=', 0)
				->order_by('datetime_start', 'asc')
				->execute()->as_array();
		}

        return $result;
	}

	public static function get_timetable_id_by_name($timetable_name)
	{
		$query = DB::select('id')
			->from('plugin_courses_timetable')
			->where('timetable_name', '=', $timetable_name)
			->execute()->as_array();
		return isset($query) ? $query[0]['id'] : NULL;
	}

	public static function create_timetable($timetable_name)
	{
		$timetables = DB::select('id')
			->from('plugin_courses_timetable')
			->where('timetable_name', '=', $timetable_name)
			->execute()
			->as_array();
		if (count($timetables) > 0)
		{
			return FALSE;
		}
		DB::insert('plugin_courses_timetable', array('timetable_name'))->values(array($timetable_name))->execute();
		return TRUE;
	}

	public static function lec_get_days_for_booking($course_id)
	{
		$sql    = "SELECT
                plugin_courses_schedules_events.`datetime_start`,
                plugin_courses_schedules_events.`schedule_id`,
                plugin_courses_schedules.`is_fee_required`,
                plugin_courses_schedules.`fee_per`,
                IF(plugin_courses_schedules.`fee_per` = 'Timeslot', plugin_courses_schedules_events.`fee_amount`, plugin_courses_schedules.`fee_amount`) AS `fee_amount` ".
			"FROM plugin_courses_schedules_events ".
			"INNER JOIN plugin_courses_schedules ON plugin_courses_schedules.`id` = plugin_courses_schedules_events.`schedule_id` ".
			"WHERE plugin_courses_schedules_events.`delete` = 0 ".
			"AND plugin_courses_schedules_events.`publish` = 1 ".
			"AND plugin_courses_schedules.`course_id` = :course_id ".
			"AND datetime_start > '".date('Y-m-d H:i:s')."' ".
			"ORDER BY datetime_start";
		$query  = DB::query(DATABASE::SELECT, $sql)
			->bind(':course_id', $course_id)
			->execute()->as_array();
		$result = "";
		$i      = 0;
		foreach ($query AS $timetable_day)
		{
			$result .= '<option
                value="'.$timetable_day['schedule_id'].'"
                data-id="'.$timetable_day['schedule_id'].'"
                data-number="'.$i.'"
                data-is_fee="'.$timetable_day['is_fee_required'].'"
                data-fee_per="' . $timetable_day['fee_per'] . '"
                data-fee_amount="'.$timetable_day['fee_amount'].'"
            >'.date("H:ia l, jS F Y", strtotime($timetable_day['datetime_start'])).'</option>';
			$i++;
		}
		return (count($query) > 0) ? $result : 0;
	}

	/**
	 * @param string $id
	 * @return string
	 */
	public static function get_trainers($id = NULL)
	{
		if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
		{
			$q = Model_Contacts3::get_teachers();
		}
		else
		{
			$q = Model_Contacts::get_trainers();
		}
		$result   = '<option value="">Select a trainer</option>';
		$selected = '';
		foreach ($q AS $key => $trainer)
		{
			$selected = ($trainer['id'] == $id) ? 'selected="selected"' : '';
			$result .= '<option '.$selected.' value="'.$trainer['id'].'">'.$trainer['first_name'].' '.$trainer['last_name'].'</option>';
		}
		return $result;
	}

	public static function calculate_frequency($frequency, $start_date = NULL, $end_date = NULL, $custom_days = '', $duration = 0, $timeslots = NULL, $custom_repeat = NULL, $trainer_id = NULL, $course_id, $blackout_event_ids, $fee_per, $fee_amount, $location_id = null)
	{
		$days    = array();
		$results = array();
		if (is_array($custom_days))
		{
			usort($custom_days, array('Model_Schedules', "sort_days"));
		}
//        $result = '<tr><th>Order</th><th>Day</th><th>Date</th><th style="width:240px;">Start Time</th><th style="width:240px;">End Time</th><th>Trainer</th><th></th></tr>';
		$result     = '';
		$start_date = date("Y-m-d H:i", strtotime($start_date));
		$start_time = date('H:i', strtotime($start_date));
		$end_date   = date("Y-m-d H:i", strtotime($end_date));
		$end_time   = date('H:i', strtotime($end_date));
		switch ($frequency)
		{
			case '1':
				$days = self::calculate_days(FALSE, $start_date, $end_date);
				break;
			case '2':
				$days = self::calculate_days(TRUE, $start_date, $end_date);
				break;
			case '3':
				$days = self::calculate_weeks($start_date, $end_date);
				break;
			case '4':
				$days = self::calculate_fortnight($start_date, $end_date);
				break;
			case '5':
				$days = self::calculate_monthly($start_date, $end_date);
				break;
			case '6':
				$days = self::calculate_custom($start_date, $end_date, $custom_days, $duration, $timeslots, $custom_repeat);
				break;
			default:
				throw new Exception("Thing not found");
				break;
		}

		$message             = '';
		$calendar_dates      = self::get_calendar_dates($blackout_event_ids);
		$category            = DB::select('start_date', 'end_date', 'category', 'start_time', 'end_time')
			->from(array('plugin_courses_categories', 'cat'))
			->join(array('plugin_courses_courses', 'course'), 'RIGHT')
			->on('cat.id', '=', 'course.category_id')
			->where('course.id', '=', $course_id)
			->execute()
			->current();
		$check_category_date = FALSE;
		$category_start      = $category_end = '';
		if ($category)
		{
			if (!is_null($category['start_date']) AND !is_null($category['end_date']))
			{
				$check_category_date = TRUE;
			}
			if (!is_null($category['start_time']))
			{
				$category_start = ' disabled="disabled"';
			}
			if (!is_null($category['end_time']))
			{
				$category_end = ' disabled="disabled"';
			}
		}

		$timeslots = array();
		if (array_key_exists('slots', $days))
		{
			foreach ($days['slots'] as $key => $slot)
			{
				$start = $slot['start_time'];

				$valid = self::check_event_for_calendar_dates($start, $calendar_dates);

				if ($check_category_date)
				{
					$cat_date_valid = self::check_event_for_category_dates($start, $slot['end_time'], $category['start_date'], $category['end_date']);
				}
				else
				{
					$cat_date_valid = TRUE;
				}

                if (!$valid) {
                    continue;
                }
                $timeslots[] = array(
						'datetime_start' => $slot['start_time'] . ':00',
						'datetime_end' => $slot['end_time'] . ':00',
						'monitored' => 1,
						'fee_amount' => ($fee_per == 'Timeslot') ? $fee_amount : '',
						'trainer_id' => (@$slot['trainer'] ? $slot['trainer'] : $trainer_id),
						'location_id' => (@$slot['location_id'] ? $slot['location_id'] : $location_id),
						'blackout' => !$valid
				);
			}
		}
		else
		{
			foreach ($days AS $key => $day)
			{
				$valid = self::check_event_for_calendar_dates($day, $calendar_dates);
                if (!$valid) {
                    continue;
                }
				$timeslots[] = array(
						'datetime_start' => $day . ':00',
						'datetime_end' => date('Y-m-d', strtotime($day)) . ' ' . $end_time . ':00',
						'monitored' => 1,
						'fee_amount' => ($fee_per == 'Timeslot') ? $fee_amount : '',
						'trainer_id' => $trainer_id,
						'blackout' => !$valid
				);
			}
		}

		foreach ($timeslots as $i => $timeslot) {
			if (!@$timeslots[$i]['location_id']) {
				$timeslots[$i]['location_id'] = $location_id;
			}
		}
		$results = array('status' => 'success', 'result' => $timeslots, 'message' => $message);
		return $results;
	}

	public static function get_schedule_location_id($schedule_id)
	{
		$q = DB::select('location_id')->from('plugin_courses_schedules')->where('id', '=', $schedule_id)->execute()->as_array();
		return count($q) > 0 ? $q[0]['location_id'] : 0;
	}

	public static function calculate_days($weekends = TRUE, $start_date, $end_date)
	{
		$begin = new DateTime($start_date);
		$end   = new DateTime($end_date);
		//$end->modify( '+1 day' );
		$result   = array();
		$interval = new DateInterval('P1D');
        $date     = $begin;
        while ($date <= $end) {
			if ($weekends)
			{
                $result[] = $date->format('Y-m-d H:i');
			}
			else
			{
				if (date("N", strtotime($date->format('Y-m-d'))) <= 5)
				{
                    $result[] = $date->format('Y-m-d H:i');
				}
			}
            $date->add($interval);
		}
		return $result;
	}

	public static function calculate_weeks($start_date, $end_date)
	{
        $begin = new DateTime($start_date);
        $end   = new DateTime($end_date);
        //$end->modify( '+1 day' );
        $result   = array();
        $interval = new DateInterval('P1W');
        $date     = $begin;
        while ($date <= $end) {
            $result[] = $date->format('Y-m-d H:i');
            $date->add($interval);
        }
        return $result;
	}

	public static function calculate_fortnight($start_date, $end_date)
	{
		$begin = new DateTime($start_date);
		$end   = new DateTime($end_date);
		//$end->modify( '+1 day' );
		$result   = array();
		$interval = new DateInterval('P2W');
        $date     = $begin;
        while ($date <= $end) {
            $result[] = $date->format('Y-m-d H:i');
            $date->add($interval);
        }
		return $result;
	}

	public static function calculate_monthly($start_date, $end_date)
	{
		$begin = new DateTime($start_date);
		$end   = new DateTime($end_date);
		//$end->modify( '+1 day' );
		$result   = array();
		$interval = new DateInterval('P1M');
        $date     = $begin;
        while ($date <= $end) {
            $result[] = $date->format('Y-m-d H:i');
            $date->add($interval);
        }
		return $result;
	}

	public static function calculate_custom($start_date, $end_date, $custom_days, $duration, $timeslots, $custom_repeat)
	{
		$begin  = new DateTime($start_date);
		$result = array();
		if ($duration > 0)
		{
			$custom_repeat = 'duration';
		}
		if ($custom_repeat == '')
		{
			$result[] = date("Y-m-d H:i", strtotime($begin->format('Y-m-d H:i')));
		}
		else
		{
			$result = array('start_time' => array(), 'end_time' => array(), 'trainer' => array());
            foreach ($timeslots as $mmm => $timeslot)
			{
				// If the first date in the range is the same day of the week as the schedule, use it as the first date
				if (date('l', strtotime($start_date)) == $timeslot[0])
				{
					$first = new DateTime($start_date);
				}
				// Otherwise use the next date that falls on that particular day of the week
				else
				{
					$first = new DateTime(date('Y-m-d', strtotime('next '.$timeslot[0], strtotime($start_date))));
				}
				$start       = new DateTime($timeslot[1]);
				$end         = new DateTime($timeslot[2]);
				$first_start = date("Y-m-d H:i", strtotime($first->format('Y-m-d').' '.$start->format('H:i')));
				$first_end   = date("Y-m-d H:i", strtotime($first->format('Y-m-d').' '.$end->format('H:i')));
				switch ($custom_repeat)
				{
					case '1':
						$result['start_time'][] = self::calculate_days(FALSE, $first_start, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['end_time'][]   = self::calculate_days(FALSE, $first_start, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['trainer'][]    = $timeslot[3];
						$result['location_id'][] = @$timeslot[6];
						break;
					case '2':
						$result['start_time'][] = self::calculate_days(TRUE, $first_start, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['end_time'][]   = self::calculate_days(TRUE, $first_start, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['trainer'][]    = $timeslot[3];
						$result['location_id'][] = @$timeslot[6];
						break;
					case '3':
                        $result['start_time'][] = self::calculate_weeks($first_start, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['end_time'][]   = self::calculate_weeks($first_end, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['trainer'][]    = $timeslot[3];
						$result['location_id'][] = @$timeslot[6];
						break;
					case '4':
						$result['start_time'][] = self::calculate_fortnight($first_start, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['end_time'][]   = self::calculate_fortnight($first_end, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['trainer'][]    = $timeslot[3];
						$result['location_id'][] = @$timeslot[6];
						break;
					case '5':
						$result['start_time'][] = self::calculate_monthly($first_start, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['end_time'][]   = self::calculate_monthly($first_end, date('Y-m-d', strtotime($end_date)) . ' ' . $end->format('H:i'));
						$result['trainer'][]    = $timeslot[3];
						$result['location_id'][] = @$timeslot[6];
						break;
					case 'duration':
						$result = array('start_time' => array(), 'end_time' => array(), 'trainer' => array());
						for ($i = 0; $i < $duration; $i++)
						{
							$result['start_time'][] = $first_start;
							$result['end_time'][]   = $first_end;
							$first_start            = date("Y-m-d H:i", strtotime($first_start->format('Y-m-d H:i'), "+1 week"));
							$first_end              = date("Y-m-d H:i", strtotime($first_end->format('Y-m-d H:i'), "+1 week"));
						}
						break;
					default:
						throw new Exception("Thing not found");
						break;
				}
			}
		}
		$slots = array();
		foreach ($result['start_time'] as $key => $timeslot)
		{
			foreach ($timeslot as $k => $start)
			{
				$slots[] = array(
					'start_time' => $start,
					'end_time'   => $result['end_time'][$key][$k],
					'trainer'    => $result['trainer'][$key],
					'location_id' => $result['location_id'][$key]
				);
			}
		}
		usort($slots, function ($a, $b)
		{
			$a = strtotime($a['start_time']);
			$b = strtotime($b['start_time']);
			return (($a == $b) ? (0) : (($a > $b) ? (1) : (-1)));
		});
		return array('slots' => $slots); //$result;
	}

	public static function get_duration($schedule_id) {
        $schedule = DB::select('*')
            ->from(self::TABLE_SCHEDULES)
            ->where('id', '=', $schedule_id)
            ->execute()
            ->current();

        if (empty($schedule)) {
            return '';
        }
        $timeslots = self::get_all_schedule_timeslots($schedule_id);
        if (empty($timeslots)) {
            //if there is no timeslots - calculate days between start and end of schedule
            $schedule_date_start = new DateTime($schedule['start_date']);
            $schedule_date_end = new DateTime($schedule['start_date']);
            $date_diff = $schedule_date_end->diff($schedule_date_start);

            $duration = $date_diff->days;
        } else {
            $timeslot_days = array();
            foreach($timeslots as $timeslot) {
                $timeslot_date_start = date('Y-m-d', strtotime($timeslot['datetime_start']));
                $timeslot_date_end = date('Y-m-d', strtotime($timeslot['datetime_end']));
                if ($timeslot_date_start == $timeslot_date_end) {
                    //if date of timeslots is the same , so it happens in one add start date to the array
                    if (!in_array($timeslot_date_start, $timeslot_days)) {
                        $timeslot_days[] = $timeslot_date_start;
                    }
                } else {
                    //if session has more than one day, add both of days to duration days
                    if (!in_array($timeslot_date_start, $timeslot_days)) {
                        $timeslot_days[] = $timeslot_date_start;
                    }
                    if (!in_array($timeslot_date_end, $timeslot_days)) {
                        $timeslot_days[] = $timeslot_date_end;
                    }
                }
            }
            $duration = count($timeslot_days);
        }

        return $duration;
    }
	/**
	 * Build the custom timetable selection Tables
	 * @param $selected_days
	 * @return string
	 */
	public static function get_custom_timetable($selected_days)
	{
		$days = array('mon' => 'Monday', 'tues' => 'Tuesday', 'wed' => 'Wednesday', 'thur' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday');
		$html = '<ul id="daily_frequency" class="row navbar-sub">';
		foreach ($selected_days as $key => $day)
		{
			$html .= '<li>'.$day.'</li>';
		}
		$html .= '</ul><div class="form-group " id="all_frequencies_selection">';
		$html .= '';
		foreach ($selected_days as $key => $day)
		{
			$html .= '<div class="timetable_day_selection ';
			$html .= '" id="'.$day.'">'
				.'<h3>'.$day.' Timetable</h3>'
				.'<input type="hidden" value="'.$day.'" class="day_name"/>'
				.'<table class="frequency_table"><tr>'
				.'<th>Day</th><th>Start Time</th><th>End Time</th><th>Teacher</th><th>Remove</th></tr>'
				.'</table><button class="btn add_timeslot" type="button">New Time Slot</button>'
				.'</div>';
		}
		$html .= '</div>';
		return $html;
	}

	public static function add_custom_day_timeslots($data)
	{
		$day  = $data['day'];
		$list = '<li class="active"><a data-toggle="tab" href="'.$day.'">'.$day.'</a></li>';
		$tab  = '<div class="tab-pane active'
			.'" id="'.$day.'">'
			.'<h3>'.$day.' Timetable</h3>'
			.'<input type="hidden" value="'.$day.'" class="day_name"/>'
			.'<table class="frequency_table"><tr>'
			.'<th>Day</th><th>Start Time</th><th>End Time</th><th>Teacher</th><th>Remove</th></tr>'
			.'</table><button class="btn add_timeslot" type="button">New Time Slot</button>'
			.'</div>';
		return array('list' => $list, 'tab' => $tab);
	}

	/**
	 * Build a new table row to add a time slot to the day
	 * @param $day
	 * @param $trainer
	 * @return string
	 */
	public static function get_new_timeslot_row($day, $trainer, $row, $course_id, $location_id = null)
	{
		$category       = DB::select('start_date', 'end_date', 'category', 'start_time', 'end_time')
			->from(array('plugin_courses_categories', 'cat'))
			->join(array('plugin_courses_courses', 'course'), 'RIGHT')
			->on('cat.id', '=', 'course.category_id')
			->where('course.id', '=', $course_id)
			->execute()
			->current();
		$category_start = $category_end = '"';
		if ($category != null && $category != '00:00')
		{
			if (!is_null($category['start_time']))
			{
				$category_start = date('H:i', strtotime($category['start_time'])).'"';
			}
			if (!is_null($category['end_time']))
			{
				$category_end = date('H:i', strtotime($category['end_time'])).'"';
			}
		}

		$html = '<tr data-day_row="'.$day.'-'.$row.'" class="new-slot" >'
			.'<td>'.$day.'</td>'
			.'<td><input type="text" name="start_time" class="form-control timepicker start_time time_range_picker" value="'.$category_start.'/></td>'
			.'<td><input type="text" name="end_time" class="form-control timepicker end_time time_range_picker" value="'.$category_end.'/></td>'
			.'<td><select class="form-control trainer_select">'.self::get_trainers($trainer).'</select></td>'
			.'<td><select class="form-control room_select">' . html::optionsFromRows('value', 'label', Model_Locations::autocomplete_locations(), $location_id) . '</select>'
			.'<td class="delete_me"><span class="icon-times"></span></td>'
			.'<input type="hidden" name="interval_id" value=""/>'
			.'</tr>';
		return $html;
	}

	static function sort_days($a, $b)
	{
		return (strtotime($a) < strtotime($b)) ? -1 : +1;
	}

	public static function get_schedules_feed($data)
	{
		$include_past_timeslots = @$data['timeslots_range'] == 'past';
		$only_next_timeslot = @$data['timeslots_range'] == 'upcoming';
		$schedule_id = $data['schedule_id'];
		$schedule_name = $data['schedule_name'];
		$location    = $data['location'];
		$category    = $data['category'];
		$year        = $data['year'];
		$booking_id  = $data['booking_id'];
		$subject     = $data['subject'];
		$result      = array();
		$start_time  = !isset($data['datetime_start']) ? date('Y-m-d H:i:s', time()) : date('Y-m-d H:i:s', strtotime($data['datetime_start']));
		$end_time    = !isset($data['datetime_end']) ? date('Y-m-d 00:00:00', strtotime("+1 Week", time())) : date('Y-m-d H:i:s', strtotime($data['datetime_end']));

		$q = DB::select(
			array('t1.id', 'schedule_id'),
			array('t2.id', 'period_id'),
			't1.name',
			array('t4.name', 'subject'),
			't2.datetime_start', 't2.datetime_end',
			array(DB::expr('COALESCE(t1.max_capacity,t3.capacity)'), 'capacity'),
			't1.location_id',
			array('t3.name', 'room'),
            array('t5.name', 'location'),
			't1.max_capacity',
			't1.booking_type',
			't0.category_id',
			'category.category',
			't4.color',
			DB::expr('GROUP_CONCAT(DISTINCT cy.year) as `year`'),
			't0.title',
			'contact.first_name',
			'contact.last_name',
            't1.attend_all_default',
            't1.is_group_booking'
		)
			->from(array('plugin_courses_courses', 't0'))
				->join(array('plugin_courses_courses_has_years', 'has_years'), 'left')
					->on('t0.id', '=', 'has_years.course_id')
			->join(array('plugin_courses_years', 'cy'), 'LEFT')->on('has_years.year_id', '=', 'cy.id')
			->join(array('plugin_courses_categories', 'category'), 'LEFT')->on('t0.category_id', '=', 'category.id')
			->join(array('plugin_courses_schedules', 't1'), 'INNER')->on('t1.course_id', '=', 't0.id')
			->join(array('plugin_courses_schedules_events', 't2'), 'INNER')->on('t2.schedule_id', '=', 't1.id')
			->join(array('plugin_courses_locations', 't3'), 'LEFT')->on('t3.id', '=', 't1.location_id')
            ->join(array('plugin_courses_locations', 't5'), 'LEFT')->on('t3.parent_id', '=', 't5.id')
			->join(array('plugin_courses_subjects', 't4'), 'LEFT')->on('t0.subject_id', '=', 't4.id');
		if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
		{
			$q->join(array('plugin_contacts3_contacts', 'contact'), 'LEFT')->on('t2.trainer_id', '=', 'contact.id');
		}
		else
		{
			$q->join(Array('plugin_courses_trainers', 'contact'), 'LEFT')->on('t2.trainer_id', '=', 'contact.id');
		}
		$q->and_where('t2.delete', '=', 0);
		if ((!empty($schedule_id)) OR (!empty($booking_id)))
		{
			$q->and_where_open();
			if (!empty($booking_id))
			{
				$q->join(array('plugin_ib_educate_booking_has_schedules', 'ibs'), 'left')->on('t1.id', '=', 'ibs.schedule_id');
				$q->join(array('plugin_ib_educate_bookings', 'ib'), 'left')->on('ibs.booking_id', '=', 'ib.booking_id');
				$q->or_where('ib.booking_id', '=', $booking_id);
			}
			if (is_array($schedule_id)) {
				$q->or_where('t1.id', 'in', $schedule_id);
			} else {
				$q->or_where('t1.id', '=', $schedule_id);
			}
			$q->and_where_close();
		}
		else
		{
            $q->and_where('t0.publish', '=', 1);
            $q->and_where('t1.publish', '=', 1);

			if (!empty($schedule_name))
			{
				$q->where('t1.name', 'like', '%' . $schedule_name . '%');
			}

			if (!empty($location))
			{
				$children = DB::select('id')->from('plugin_courses_locations')->where('parent_id', '=', $location)->where('publish', '=', 1)->execute()->as_array();
				$locs     = array_merge(array($location), $children);
				$q->where('t1.location_id', 'IN', $locs);
			}

			if (!empty($category))
			{
				$q->where('t0.category_id', '=', $category);
			}
			if (!empty($subject))
			{
				$q->where('t0.subject_id', '=', $subject);
			}

			// Remove the room type to view the schedule
			//$q->where('t3.location_type_id','=',2);

			if (!empty($year))
			{
				$q->where('has_years.year_id', '=', $year);
			}
		}

		$now  = date("Y-m-d H:i:s");
		$date = $now > $start_time ? $now : $start_time;
		if (!$booking_id && !$include_past_timeslots) {
			$q->where('datetime_start', '>=', $date);
			$q->where('datetime_end', '<=', $end_time);
		}
		$q->order_by('cy.year', 'desc');
		$q->order_by('t2.datetime_start', 'asc');
		$q->order_by('t4.name', 'asc'); // Subject name
		$q->order_by('t1.name', 'asc'); // Schedule Name
		$q->order_by('contact.first_name', 'asc');
		$q->order_by('contact.last_name', 'asc');
		$q->group_by('t1.id');
		$q->group_by('t2.id');

		$q = $q->execute()->as_array();

        $schedules_timeslot_counts = array();

        foreach ($q as $key => $row) {
            if (!isset($schedules_timeslot_counts[$row['schedule_id']])) {
                $schedules_timeslot_counts[$row['schedule_id']] = 0;
            }
            $schedules_timeslot_counts[$row['schedule_id']] += 1;
        }

		foreach ($q as $key => $row)
		{
			$capacity = FALSE;
			if (is_null($row['max_capacity']) OR $row['max_capacity'] == '')
			{
				$c        = DB::select('capacity')
					->from('plugin_courses_locations')
					->where('id', '=', $row['location_id'])
					->execute()
					->current();
				$capacity = $c['capacity'] == 0 ? TRUE : FALSE;
			}

			$r = DB::select(array(DB::expr('count(booking_item_id)'), 'count'))
				->from(Model_KES_Bookings::BOOKING_ITEMS_TABLE)
				->where('period_id', '=', $row['period_id'])
				->and_where('delete', '=', 0)
				->execute()->current();

			$enquiries = DB::select(array(DB::expr('count(t1.booking_item_id)'), 'count'))
				->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 't1'))
				->join(array(Model_KES_Bookings::BOOKING_TABLE, 't2'))
				->on('t1.booking_id', '=', 't2.booking_id')
				->where('t1.period_id', '=', $row['period_id'])
				->and_where('t2.booking_status', '=', 2)
				->execute()
				->current();

			$result[] = array(
				'schedule_id'      => $row['schedule_id'],
				'period_id'        => $row['period_id'],
				'name'             => $row['name'],
				'datetime_start'   => $row['datetime_start'],
				'datetime_end'     => $row['datetime_end'],
				'room_no'          => $row['room'],
                'location'          => $row['location'],
				'category_id'      => $row['category_id'],
				'category'         => $row['category'],
				'places_available' => ($capacity) ? '&infin;' : $row['capacity'] - $r['count'],
				'no_of_enquiries'  => $enquiries['count'],
				'color'            => $row['color'],
				'year'             => $row['year'],
				'title'            => $row['title'],
				'teacher'          => $row['first_name'].' '.$row['last_name'],
				'booking_type'     => $row['booking_type'],
                'timeslots_count' => $schedules_timeslot_counts[$row['schedule_id']],
                'attend_all_default' => $row['attend_all_default'],
                'is_group_booking' => $row['is_group_booking']
			);
		}

		return $result;
	}

	public static function get_booked_schedules_feed($data = NULL, $all = FALSE)
	{
		$result = array();
		if (!empty($data))
		{
			$start_time = date('Y-m-d H:i:s', strtotime($data['datetime_start']));
			$end_time   = date('Y-m-d H:i:s', strtotime($data['datetime_end']));

			$schedules = $data['schedules'];

			$events = DB::select()->from('plugin_courses_schedules_events')->where('delete', '=', 0);

			$q = DB::select(
				array('t1.id', 'schedule_id'),
				array('t2.id', 'period_id'),
				't1.name',
				't2.datetime_start',
				't2.datetime_end',
				array(DB::expr('COALESCE(t1.max_capacity,t3.capacity)'), 'capacity'),
				array('t3.name', 'location'),
				't0.category_id',
				'category.category',
				't4.color',
				DB::expr('GROUP_CONCAT(DISTINCT cy.year) as `year`'),
				'contact.first_name',
				'contact.last_name',
				't1.booking_type',
                't1.attend_all_default',
				't1.is_group_booking'
			)
				->from(array('plugin_courses_courses', 't0'))
				->join(array('plugin_courses_courses_has_years', 'has_years'), 'left')
						->on('t0.id', '=', 'has_years.course_id')
				->join(array('plugin_courses_years', 'cy'))->on('has_years.year_id', '=', 'cy.id')
				->join(array('plugin_courses_categories', 'category'))->on('t0.category_id', '=', 'category.id')
				->join(array('plugin_courses_schedules', 't1'))->on('t1.course_id', '=', 't0.id')
				->join(array($events, 't2'))->on('t2.schedule_id', '=', 't1.id')
				->join(array('plugin_courses_locations', 't3'), 'LEFT')->on('t3.id', '=', 't1.location_id')
				->join(array('plugin_courses_subjects', 't4'), 'LEFT')->on('t0.subject_id', '=', 't4.id');
			if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3'))
			{
				$q->join(array('plugin_contacts3_contacts', 'contact'), 'LEFT')->on('t2.trainer_id', '=', 'contact.id');
			}
			else
			{
				$q->join(Array('plugin_courses_trainers', 'contact'), 'LEFT')->on('t2.trainer_id', '=', 'contact.id');
			}
			if (!is_null($schedules))
			{
				$q->where('t1.id', 'IN', $schedules);
			}
			if (@$data['booking_id'] == '' && @$data['schedule_id'] == '' && @$data['publish'] == 1) {
				$q->and_where('courses.publish', '=', 1);
				$q->and_where('t1.publish', '=', 1);
			}
			if ( ! $all)
			{
				$q->where('t2.datetime_start', '>=', $start_time);
				$q->where('t2.datetime_end', '<=', $end_time);
			}
			$q->order_by('t4.name', 'asc');
			$q->group_by('t1.id');
			$q = $q->execute()->as_array();

			foreach ($q as $key => $row)
			{
				$r         = DB::select(array(DB::expr('count(booking_item_id)'), 'count'))
					->from(Model_KES_Bookings::BOOKING_ITEMS_TABLE)
					->where('period_id', '=', $row['period_id'])
					->and_where('delete', '=', 0)->execute()->current();
				$enquiries = DB::select(array(DB::expr('count(t1.booking_item_id)'), 'count'))
					->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 't1'))
					->join(array(Model_KES_Bookings::BOOKING_TABLE, 't2'))->on('t1.booking_id', '=', 't2.booking_id')
					->where('t1.period_id', '=', $row['period_id'])
					->and_where('t2.booking_status', '=', 1)->execute()->current();
				$status    = DB::select('t1.attending')
					->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 't1'))
					->where('t1.period_id', '=', $row['period_id'])
					->and_where('t1.delete', '=', 0)->execute()->current();
				$result[]  = array(
					'schedule_id'     => $row['schedule_id'], 'period_id' => $row['period_id'], 'name' => $row['name'],
					'datetime_start'  => $row['datetime_start'], 'datetime_end' => $row['datetime_end'],
					'room_no'         => $row['location'], 'category_id' => $row['category_id'],
					'category'        => $row['category'], 'places_available' => $row['capacity'] - $r['count'],
					'no_of_enquiries' => $enquiries['count'], 'color' => $row['color'],
					'attending'       => $status['attending'],
					'year'            => $row['year'],
					'teacher'         => $row['first_name'].' '.$row['last_name'],
					'booking_type'    => $row['booking_type'],
                    'attend_all_default' => $row['attend_all_default'],
                    'is_group_booking' => $row['is_group_booking']
				);
			}
		}
		return $result;
	}

	public static function sort_schedule_feed(&$schedules)
	{
		usort($schedules, function ($a, $b)
		{
			$timea = strtotime($a['datetime_start']);
			$timeb = strtotime($b['datetime_start']);
			$daya  = strtotime(date('Y-m-d 00:00:00', $timea));
			$dayb  = strtotime(date('Y-m-d 00:00:00', $timeb));

			if ($daya == $dayb)
			{
				$year_cmp = strcasecmp($a['year'], $b['year']);
				if ($year_cmp == 0)
				{
					$name_cmp = strcasecmp($a['name'], $b['name']);
					if ($name_cmp == 0)
					{
						if ($timea == $timeb)
						{
							return 0;
						}
						else
						{
							return $timea < $timeb ? 1 : -1;
						}
					}
					else
					{
						return $name_cmp;
					}
				}
				else
				{
					return -$year_cmp;
				}
			}
			else
			{
				return $timea > $timeb ? 1 : -1;
			}
		});
	}

	public static function get_feed_length($data, $post)
	{
		if (count($data) > 0)
		{
			$size          = count($data);
			$first_day     = strtotime(date("Y-m-d H:i:s", strtotime($post['datetime_start'])));
			$last_day      = strtotime(date("Y-m-d H:i:s", strtotime($post['datetime_end'])));
			$datediff      = $first_day - $last_day;
			$largest       = (count($data) == 1) ? 1 : 0;
			$current_count = 0;
			$current_date  = '';
			foreach ($data as $key => $row)
			{
				if (date('Y-m-d', strtotime($row['datetime_start'])) == $current_date)
				{
					$current_count++;
					$current_date = date('Y-m-d', strtotime($row['datetime_start']));
				}
				else
				{
					if ($current_count > $largest)
					{
						$largest = $current_count;
					}
					$current_count = 1;
					$current_date  = date('Y-m-d', strtotime($row['datetime_start']));
				}
			}

			$days = abs(ceil($datediff / (60 * 60 * 24))) + 1;
			return array('size' => $days, 'first_day' => $first_day, 'last_day' => $last_day, 'rows' => $largest);
		}
		else
		{
			return array('size' => 28, 'first_day' => strtotime(date("Y-m-d 00:00:00", strtotime($post['datetime_start']))), 'last_day' => strtotime(date("Y-m-d 00:00:00", strtotime($post['datetime_end']))), 'rows' => 0);
		}
	}

	public static function get_calendar_feed()
	{
		//we're getting the schedule start dates, news [condition] date_to_publish (1st priority) OR date_created
		//for now we limit this from today onwards.
		$query = DB::query(Database::SELECT, "SELECT date_publish AS `date`,
                                            `id` AS news_id,
                                            title AS news_title,
                                            '' AS `name`,
                                            '' AS course_name FROM plugin_news
                                            WHERE date_publish <= NOW()
                                            UNION
                                            SELECT start_date AS `date`,
                                            '' AS news_id,
                                            '' AS news_title,
                                            `id` AS course_id,
                                            `name` AS course_name
                                            FROM plugin_courses_schedules
                                            WHERE start_date >= NOW()
                                            ORDER BY `date`")->execute()->as_array();
		return $query;
	}

	public static function get_all_schedule_timeslots($schedule_id, $event_id = null, $filter_from_event_id = false)
	{
        $from_event = null;
        if ($filter_from_event_id) {
            $from_event = DB::select('*')
                ->from(self::TABLE_TIMESLOTS)
                ->where('id', '=', $filter_from_event_id)
                ->execute()
                ->current();
        }
		$q = DB::select(
			't1.id', 't1.datetime_start', 't1.datetime_end',
			't2.name', 't3.title',
			DB::expr("IF(t2.`fee_per` = 'Timeslot', t1.`fee_amount`, t2.`fee_amount`) AS `fee`"),
            't2.fee_per',
			't2.run_off_schedule',
			't2.payment_type',
			array(DB::expr('NOW()'), 'date'),
			't2.booking_type'
		)
			->from(array('plugin_courses_schedules_events', 't1'))
			->join(array('plugin_courses_schedules', 't2'))->on('t1.schedule_id', '=', 't2.id')
			->join(array('plugin_courses_courses', 't3'))->on('t3.id', '=', 't2.course_id')
			->where('t1.schedule_id', '=', $schedule_id)
			->and_where('t1.publish', '=', 1)
			->and_where('t1.delete', '=', 0);
		if ($event_id !== null) {
			$q->and_where('t1.id', '=', $event_id);
		}
        if ($from_event) {
            $q->and_where('t1.datetime_start', '>=', $from_event['datetime_start']);
        }
//            ->and_where('t1.datetime_start','>=',DB::expr('CURDATE()'))
		$q = $q->order_by('t1.datetime_start')
			->execute()
			->as_array();

		$capacity = self::get_schedule_room_capacity($schedule_id);

		$get_next_schedule = FALSE;
		$next_schedule     = 0;

        $schedule_booking_count = null;
		foreach ($q as $key => $value)
		{
            if ($value['booking_type'] == 'One Timeslot') {
                $booking_count = self::get_schedule_booking_count($schedule_id, $value['booking_type'] == 'One Timeslot' ? $value['id'] : null);
            } else {
                if ($schedule_booking_count === null) {
                    $schedule_booking_count = (int)self::get_schedule_booking_count($schedule_id, null);
                }
                $booking_count = $schedule_booking_count;
            }

			$q[$key]['schedule_id']     = $schedule_id;
			$q[$key]['course_title']    = $value['title'];
			$q[$key]['schedule_title']  = $value['name'];
			$q[$key]['day']             = date('D', strtotime($value['datetime_start']));
			$q[$key]['date']            = date('M j', strtotime($value['datetime_start']));
			$q[$key]['time']            = date('H:i', strtotime($value['datetime_start'])).' - '.date('H:i', strtotime($value['datetime_end']));
			$q[$key]['period_id']       = $q[$key]['id'];
			$q[$key]['available_slots'] = $capacity > 0 ? $capacity - $booking_count : null;
			$q[$key]['fee']             = $value['fee'];
			$q[$key]['payment_type']    = $value['payment_type'];
			$q[$key]['today']           = $value['date'];
		}

		if ($get_next_schedule)
		{
			$s = DB::select(
				't1.id', 't1.datetime_start', 't1.datetime_end',
				't2.name', 't3.title',
				array('t2.fee_amount', 'fee'),
				't2.run_off_schedule',
				't2.payment_type',
				array(DB::expr('NOW()'), 'date')
			)
				->from(array('plugin_courses_schedules_events', 't1'))
				->join(array('plugin_courses_schedules', 't2'))->on('t1.schedule_id', '=', 't2.id')
				->join(array('plugin_courses_courses', 't3'))->on('t3.id', '=', 't2.course_id')
				->where('t1.schedule_id', '=', $next_schedule)
				->and_where('t1.publish', '=', 1)
				->and_where('t1.delete', '=', 0)
				->order_by('t1.datetime_start')->execute()->as_array();

			foreach ($s as $key => $value)
			{
                $booking_count = self::get_schedule_booking_count($schedule_id, $value['id']);
				$s[$key]['schedule_id']     = $schedule_id;
				$s[$key]['course_title']    = $value['title'];
				$s[$key]['schedule_title']  = $value['name'];
				$s[$key]['day']             = date('D', strtotime($value['datetime_start']));
				$s[$key]['date']            = date('M j', strtotime($value['datetime_start']));
				$s[$key]['time']            = date('H:i', strtotime($value['datetime_start'])).' - '.date('H:i', strtotime($value['datetime_end']));
				$s[$key]['period_id']       = $s[$key]['id'];
                $q[$key]['available_slots'] = $capacity > 0 ? $capacity - $booking_count : null;
				$s[$key]['fee']             = $value['fee'];
				$s[$key]['payment_type']    = $value['payment_type'];
				$r[$key]['today']           = $value['date'];
			}
			$q = array_merge($q, $s);
		}

		return $q;
	}

	public static function get_period_details(&$data, $all_timeslots = true)
	{
		if (count($data) > 0)
		{
			$schedule_ids = array();
			$period_ids   = array();
			$q            = DB::select(
				array('t1.id', 'course_id'),
				't1.title',
				't2.name',
				't3.datetime_start',
				't3.datetime_end',
				't3.id',
				array('t2.id', 'schedule_id'),
                't3.fee_amount',
                't2.fee_per'
			)
				->from(array('plugin_courses_courses', 't1'))
				->join(array('plugin_courses_schedules', 't2'), 'LEFT')
				->on('t1.id', '=', 't2.course_id')
				->join(array('plugin_courses_schedules_events', 't3'))
				->on('t3.schedule_id', '=', 't2.id');
			foreach ($data as $schedule_id => $periods)
			{
				$schedule_ids[] = $schedule_id;
				foreach ($periods as $period_id => $period_data)
				{
					if ($period_data['attending'] == 1)
					{
						$period_ids[] = $period_id;
					}
				}
			}
			$q->where_open();
				if ($period_ids) {
					$q->or_where('t3.id', 'in', $period_ids);
				}
				$q->or_where('t2.booking_type', '=', 'Whole Schedule');
			$q->where_close();
			$q->where('t3.schedule_id', 'IN', $schedule_ids)
				->and_where('t3.delete', '=', 0)
				->and_where('t3.publish', '=', 1)
				->order_by('t3.datetime_start', 'asc');
			$q = $q->execute()->as_array();

			foreach ($q as $index => $item)
			{
				$q[$index]['attend'] = (in_array($item['id'], $period_ids)) ? 1 : 0;
				$q[$index]['note']   = isset($data[$item['schedule_id']][$item['id']]['note']) ? $data[$item['schedule_id']][$item['id']]['note'] : '';
			}
			$data = $q;
		}
		else
		{
			$data = array();
		}
	}

	public static function _flush_cart()
	{
		Session::instance()->delete(Model_KES_Bookings::BOOKING_CART);
	}

	public static function _cart_offers()
	{
		$cart = Session::instance()->get(Model_KES_Bookings::BOOKING_CART);
		return (is_object($cart) AND isset($cart->offers) AND is_array($cart->offers)) ? $cart->offers : array();
	}

	public static function _update_discounts($offer)
	{
		$cart = Session::instance()->get(Model_KES_Bookings::BOOKING_CART);
		if (!isset($cart->offers) OR !is_array($cart->offers))
		{
			$cart->offers = array();
		}
		$cart->offers[] = $offer;

		Session::instance()->set(Model_KES_Bookings::BOOKING_CART, $cart);
	}

	public static function _rebuild_cart($booking_data, $fee_data, $custom_discount, $client_id, $ignored_discounts = array())
	{
		$cart                    = new stdClass();
		$cart->id                = time();
		$cart->lines             = new stdClass();
		$cart->custom_discount   = $custom_discount;
		$cart->ignored_discounts = $ignored_discounts;
		$cart->client            = new stdClass();
		$cart->client->id        = $client_id;

		foreach ($fee_data as $fee)
		{
			$periods_attending = array();
			foreach ($booking_data[$fee['id']] as $period_id => $period_data)
			{
				$periods_attending[] = $period_id;
			}

            $line = array();
            $line['periods_attending'] = $periods_attending;
            $line['fee']               = $fee['fee'];
            $line['schedule_name']     = $fee['name'];
            $line['is_prepay_item']    = (bool) $fee['prepay'];
            $line['discount']          = 0;
            $line['discounts']         = array();

            $line['total']             = $line['fee'] - $line['discount'];
			$cart->lines->$fee['id']   = $line;

        }

		Session::instance()->set(Model_KES_Bookings::BOOKING_CART, $cart);
	}

	public static function check_booking_times($bookings)
	{
		$result = array();
		if (!empty($bookings) AND !is_null($bookings))
		{
			foreach ($bookings as $key => $schedule)
			{

			}
		}
	}

	public static function get_all_contact_booked_dates($contact_id)
	{
		$q = DB::select(
			't1.name',
			't2.datetime_start',
			array(DB::expr('GROUP_CONCAT(DISTINCT t1.name)'), 'names'),
			array('t4.booking_status', 'booking_status')
		)
			->from(array('plugin_courses_schedules', 't1'))
			->join(array('plugin_courses_schedules_events', 't2'))
			->on('t1.id', '=', 't2.schedule_id')
			->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 't3'))
			->on('t3.period_id', '=', 't2.id')
			->join(array(Model_KES_Bookings::BOOKING_TABLE, 't4'))
			->on('t4.booking_id', '=', 't3.booking_id')
			->join(array(Model_KES_Bookings::CONTACTS_TABLE, 't5'))
			->on('t5.id', '=', 't4.contact_id')
			->where('t5.id', '=', $contact_id)
			->and_where('t3.delete', '=', 0)
			->and_where('t4.delete', '=', 0)
			->and_where('t5.delete', '=', 0)
			->group_by(DB::expr('CAST(t2.datetime_start AS DATE)'))
			->execute()->as_array();

		foreach ($q as $key => $value)
		{
			$q[$key]['day']   = intval(date('j', strtotime($value['datetime_start'])));
			$q[$key]['month'] = intval(date('n', strtotime($value['datetime_start']))) - 1;
			$q[$key]['year']  = intval(date('Y', strtotime($value['datetime_start'])));
		}

		return $q;
	}

	public static function check_room_availability($room_id, $timetable_data)
	{
		foreach ($timetable_data as $key => $value)
		{
			$index = stripos($value, '-');
			$date  = date('d-m-Y', strtotime(str_replace('/', '-', substr($value, 0, ($index)))));
			$time  = explode('-', substr($value, $index + 1));
			$q     = DB::select('t1.id')
				->from(array('plugin_courses_schedules', 't1'))
				->join(array('plugin_courses_locations', 't2'), 'LEFT')
				->on('t1.location_id', '=', 't2.id')
				->join(array('plugin_courses_schedules_events', 't3'), 'LEFT')
				->on('t3.schedule_id', '=', 't1.id')
				->where('t3.datetime_start', '>=', date('Y-m-d H:i:s', strtotime($date." ".$time[0])))
				->and_where('t3.datetime_end', '<=', date('Y-m-d H:i:s', strtotime($date." ".$time[1])))
				->and_where('t2.id', '=', $room_id)
				->and_where('t2.location_type_id', '=', 2)
				->and_where('t3.delete', '=', 0)
				->execute()
				->as_array();
			if (count($q) > 0)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	public static function get_schedule_category($schedule_id)
	{
		$q = DB::select('t1.id')
			->from(array('plugin_courses_categories', 't1'))
			->join(array('plugin_courses_courses', 't2'), 'LEFT')->on('t1.id', '=', 't2.category_id')
			->join(array('plugin_courses_schedules', 't3'), 'LEFT')->on('t3.course_id', '=', 't2.id')
			->where('t3.id', '=', $schedule_id)
            ->or_where('t2.id', '=', $schedule_id)
            ->execute()->as_array();
		return count($q) > 0 ? $q[0]['id'] : 0;
	}

	public static function get_schedule_room_capacity($schedule_id)
	{
		$r = DB::select('max_capacity')
			->from('plugin_courses_schedules')
			->where('id', '=', $schedule_id)
			->execute()->current();

		if (!empty($r) AND $r['max_capacity'] == NULL)
		{
			$location = DB::select('location_id')
				->from('plugin_courses_schedules')
				->where('id', '=', $schedule_id)
				->execute()->current();
			if (count($location) > 0)
			{
				$location_capacity = DB::select('capacity')
					->from('plugin_courses_locations')
					->where('id', '=', $location['location_id'])
					->execute()
					->current();
				$r['max_capacity'] = count($location_capacity) > 0 ? $location_capacity['capacity'] : 0;
			}
		}

		return !empty($r) ? $r['max_capacity'] : 0;
	}

    public static function get_all_schedules($filters = [])
    {
        $q = DB::select('id', 'name')->from('plugin_courses_schedules')->where('delete', '=', 0);

        if (isset($filters['publish'])) {
            $q->where('publish', '=', $filters['publish']);
        }

        if (!empty($filters['course_id'])) {
            $q->where('course_id', '=', $filters['course_id']);
        }

        return $q->execute()->as_array();
    }

	public static function get_booking_status_label($status_id)
	{
		$q = DB::select('title')
			->from(Model_KES_Bookings::BOOKING_STATUS_TABLE)
			->where('status_id', '=', $status_id)
			->execute()
			->as_array();
		return count($q) > 0 ? $q[0]['title'] : '';
	}

	public static function get_calendar_dates($blackout_event_ids = 'none')
	{
		$dates = array();
		if ($blackout_event_ids !== 'none' && $blackout_event_ids != null)
		{
			$answers = ORM::factory('Calendar_Event')->get_all_published_dates('courses', $blackout_event_ids);
			foreach ($answers as $key => $answer)
			{
				$dates[] = array('start_date' => $answer['start_date'], 'end_date' => $answer['end_date']);
			}
		}
		return $dates;
	}

	public static function check_event_for_calendar_dates($event, $calendar)
	{
		$answer = TRUE;
        $event_date = date('Y-m-d', strtotime($event));
		foreach ($calendar as $key => $date)
		{
			if ($event_date != date('Y-m-d', strtotime($date['start_date'])))
			{
				$answer = ($answer == TRUE) ? TRUE : FALSE;
			}
			else
			{
				$answer = FALSE;
			}
		}
		return $answer;
	}

	public static function check_event_for_category_dates($event_start, $event_end, $start, $end)
	{
		$answer = TRUE;
		if ($event_start < date('Y-m-d', strtotime($start)))
		{
			$answer = FALSE;
		}
		if ($event_end > date('Y-m-d', strtotime($end)))
		{
			$answer = FALSE;
		}
		return $answer;
	}

	public static function delete_custom_repeat($schedule_id)
	{
		$interval = DB::select('id')->from('plugin_courses_schedules_has_intervals')->where('schedule_id', '=', $schedule_id)->execute()->as_array();
		if ($interval)
		{
			$update = array('publish' => 0, 'deleted' => 1);
			DB::delete('plugin_courses_schedules_has_intervals')->where('schedule_id', '=', $schedule_id)->execute();
			return DB::update('plugin_courses_schedules_intervals')->set($update)->where('interval_id', '=', $interval[0]['id'])->execute();
		}
		else
		{
			return TRUE;
		}
	}

	public static function get_custom_repeat($schedule)
	{
		$result = array();
		if (!is_null($schedule))
		{
			$interval = DB::select('id')->from('plugin_courses_schedules_has_intervals')->where('schedule_id', '=', $schedule)->execute()->as_array();
			if ($interval)
			{
				$result = DB::select(
						'has.custom_frequency', 'has.id', 'i.day', 'i.start_time', 'i.end_time', 'i.trainer_id',
						array('i.id', 'interval_id'),
						'i.location_id'
				)
					->from(array('plugin_courses_schedules_has_intervals', 'has'))
					->join(array('plugin_courses_schedules_intervals', 'i'))
					->on('has.id', '=', 'i.interval_id')
					->where('has.schedule_id', '=', $schedule)
					->order_by(DB::expr("case when day = 'Monday' then 1
                    when day = 'Tuesday' then 2
                    when day = 'Wednesday' then 3
                    when day = 'Thursday' then 4
                    when day = 'Friday' then 5
                    when day = 'Saturday' then 6
                    when day = 'Sunday' then 7
                    end asc
                "))
					->order_by('i.start_time')
					->where('has.id', '=', $interval[0]['id'])
					->where('i.publish', '=', 1)
					->where('i.deleted', '=', 0)
					->order_by('i.day')
					->execute()
					->as_array();
			}
		}
		if (!$result)
		{
			$events           = DB::select('datetime_start', 'datetime_end', 'trainer_id', 'location_id')
				->from('plugin_courses_schedules_events')
				->where('schedule_id', '=', $schedule)
				->and_where('delete', '=', 0)
				->order_by('datetime_start', 'ASC')
				->execute()
				->as_array();
			$custom_frequency = NULL;
            if (count($events) == 0) {
                return array();
            }
			$rows             = sizeof($events);
			$first            = $events[0]['datetime_start'];
			foreach ($events as $key => $event)
			{
				if ($key <= $rows)
				{
					if (strtotime($event['datetime_start']) == strtotime($first.' +1 week'))
					{
						$custom_frequency = 3;
						$rows             = $key;
						unset($events[$key]);
					}
					if (strtotime($event['datetime_start']) == strtotime($first.' +2 week'))
					{
						$custom_frequency = 4;
						$rows             = $key;
						unset($events[$key]);
					}
					if (strtotime($event['datetime_start']) == strtotime($first.' +1 month'))
					{
						$custom_frequency = 5;
						$rows             = $key;
						unset($events[$key]);
					}
				}
				else
				{
					unset($events[$key]);
				}
			}
			$custom_frequency = is_null($custom_frequency) ? 3 : $custom_frequency;
			$custom_intervals = array();
			foreach ($events as $key => $event)
			{
				$custom_intervals[$key]['day']        = date('l', strtotime($event['datetime_start']));
				$custom_intervals[$key]['start_time'] = date('H:i', strtotime($event['datetime_start']));
				$custom_intervals[$key]['end_time']   = date('H:i', strtotime($event['datetime_end']));
				$custom_intervals[$key]['trainer_id'] = $event['trainer_id'];
				$custom_intervals[$key]['location_id'] = $event['location_id'];
			}
			$interval = DB::select('id')->from('plugin_courses_schedules_has_intervals')->where('schedule_id', '=', $schedule)->execute()->as_array();
			if ($interval)
			{
				$interval_id = $interval[0]['id'];
			}
			else
			{
				$has_interval = array('schedule_id' => $schedule, 'custom_frequency' => $custom_frequency);
				$interval_id  = DB::insert('plugin_courses_schedules_has_intervals', array_keys($has_interval))->values($has_interval)->execute();
				$interval_id  = $interval_id[0];
			}
			foreach ($custom_intervals as $key => $slot)
			{
				$custom_intervals[$key]['interval_id'] = $interval_id;
			}
			foreach ($custom_intervals as $key => $slot)
			{
				DB::insert('plugin_courses_schedules_intervals', array_keys($slot))->values($slot)->execute();
			}
		}

		return $result;
	}

	public static function get_custom_interval_html($schedule)
	{
		$days = array();
		if (!is_null($schedule))
		{
			$days   = array();
			$result = Model_Schedules::get_custom_repeat($schedule);
			if ($result)
			{
				$row = NULL;
				foreach ($result as $key => $interval)
				{
					if (!array_key_exists($interval['day'], $days))
					{
//                        $days[] = $interval['day'];
						$days[$interval['day']] = '';
						$row                    = 1;
					}
					else
					{
						$row++;
					}
					$days[$interval['day']] .=
						'<tr data-day_row="'.$interval['day'].'-'.$row.'" class="new-slot" >'
							.'<td>'.$interval['day'].'</td>'
							.'<td><input type="text" name="start_time" class="form-control timepicker start_time time_range_picker" value="'
							.$interval['start_time'].'" /></td>'
							.'<td><input type="text" name="end_time" class="form-control timepicker end_time time_range_picker" value="'
							.$interval['end_time'].'" /></td>'
							.'<td><select class="form-control trainer_select">'.self::get_trainers($interval['trainer_id']).'</select></td>'
							.'<td><select class="form-control room_select"><option value=""></option>'.html::optionsFromRows('value', 'label', Model_Locations::autocomplete_locations(), @$interval['location_id']).'</select></td>'
							.'<td class="delete_me"><span class="icon-times"></span></td>'
							.'<input type="hidden" name="interval_id" value="'
							.$interval['interval_id'].'"/>'
							.'</tr>';
				}
			}
		}
		return $days;
	}

	public static function save_custom_intervals($intervals, $schedule_id, $custom_frequency)
	{
		if ($intervals == '')
		{
			return TRUE;
		}
		$update      = array();
		$insert      = array();
		$interval_id = DB::select('id')->from('plugin_courses_schedules_has_intervals')->where('schedule_id', '=', $schedule_id)->execute()->get('id');
		if (!$interval_id)
		{
			$interval_id = DB::insert('plugin_courses_schedules_has_intervals', array('schedule_id', 'custom_frequency'))->values(array('schedule_id' => $schedule_id, 'custom_frequency' => $custom_frequency))->execute();
		}
		foreach ($intervals as $key => $interval)
		{
			$interval['id']          = $interval['interval_id'];
			$interval['interval_id'] = $interval_id;
			if ($interval['id'] == '')
			{
				$insert[] = $interval;
			}
			else
			{
				$update[] = $interval;
			}
		}

		$in = DB::insert('plugin_courses_schedules_intervals', array_keys($insert))->values($insert)->execute();
		$up = DB::update('plugin_courses_schedules_intervals')->set($update)->execute();

		return ($in AND $up) ? TRUE : FALSE;
	}

	public static function cleanup_duplicate_intervals()
	{
		return DB::query(Database::DELETE, 'DELETE ii
FROM plugin_courses_schedules_intervals ii
INNER JOIN
	(SELECT si.*,ci.cnt, ci.mid
	FROM plugin_courses_schedules_intervals si
		INNER JOIN
			(	SELECT count(*) AS cnt,interval_id, day, start_time, end_time, trainer_id, max(id) AS mid
				FROM plugin_courses_schedules_intervals
				WHERE deleted=0
				GROUP BY interval_id, day, start_time, end_time, trainer_id
				HAVING cnt > 1
				ORDER BY cnt) ci ON si.interval_id = ci.interval_id AND si.`day` = ci.`day` AND si.start_time = ci.start_time AND si.end_time = ci.end_time AND si.trainer_id = ci.trainer_id) di
			ON ii.interval_id = di.interval_id AND ii.`day` = di.`day` AND ii.start_time = di.start_time AND ii.end_time = di.end_time AND ii.trainer_id = di.trainer_id AND ii.id <> di.mid;
')->execute();
	}

	public static function cleanup_duplicate_events()
	{
		return DB::query(Database::DELETE, 'DELETE ee
FROM plugin_courses_schedules_events ee
INNER JOIN
	(SELECT se.*,ce.cnt,ce.mid
	FROM plugin_courses_schedules_events se
		INNER JOIN
			(	SELECT count(*) AS cnt,schedule_id, timetable_id, datetime_start, datetime_end, trainer_id, max(id) AS mid
				FROM plugin_courses_schedules_events
				WHERE `delete`=0
				GROUP BY schedule_id, timetable_id, datetime_start, datetime_end, trainer_id
				HAVING cnt > 1
				ORDER BY cnt) ce ON se.schedule_id = ce.schedule_id AND se.timetable_id = ce.timetable_id AND se.datetime_start = ce.datetime_start AND se.datetime_end = ce.datetime_end AND se.trainer_id = ce.trainer_id) de
			ON ee.schedule_id = de.schedule_id AND ee.timetable_id = de.timetable_id AND ee.datetime_start = de.datetime_start AND ee.datetime_end = de.datetime_end AND ee.trainer_id = de.trainer_id AND ee.id <> de.mid;
')->execute();
	}

	public static function fix_timeslots_without_trainers()
	{
		return DB::query(
			Database::UPDATE,
			'UPDATE plugin_courses_schedules_events e
              SET trainer_id = (SELECT s.trainer_id FROM plugin_courses_schedules s WHERE s.id = e.schedule_id)
              WHERE e.trainer_id = 0'
		)->execute();
	}

    public static function get_schedule_booking_count($schedule_id, $timeslot_id = null)
    {
        $cnt = 0;
        if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
            $cntq = DB::select(DB::expr('count(*) as cnt'))
                ->from(array('plugin_ib_educate_bookings', 'b'))
                    ->join(array('plugin_ib_educate_booking_has_schedules', 's'), 'inner')
                        ->on('b.booking_id', '=', 's.booking_id')
                        ->on('s.schedule_id', '=', DB::expr($schedule_id ? $schedule_id : 'null'));
            if ($timeslot_id) {
                $cntq->join(array('plugin_ib_educate_booking_items', 'i'), 'inner')
                    ->on('b.booking_id', '=', 'i.booking_id')
                    ->on('i.delete', '=', DB::expr(0));
				$cntq->and_where('i.period_id', '=', $timeslot_id);
            }
            $cnt += $cntq
                ->where('b.delete', '=', 0)
                ->and_where('s.deleted', '=', 0)
                ->and_where('b.booking_status', 'in', array(2, 4))
                ->execute()
                ->get('cnt');

        }

        $cnt += Model_CourseBookings::bookings_quantity_select($schedule_id, $timeslot_id)->execute()->get('quantity');

        return $cnt;
    }


	public static function search($params = array())
	{
		$searchq = DB::select(
			DB::expr('DISTINCT schedules.*'),
			'categories.category',
			array('courses.title', 'course'),
			array('rooms.id', 'room_id'),
			array('rooms.name', 'room'),
			array('buildings.id', 'building_id'),
			array('buildings.name', 'building'),
			DB::expr("CONCAT_WS(' ', trainers.first_name, trainers.last_name) AS `trainer`")
		)
				->from(array(self::TABLE_SCHEDULES, 'schedules'))
					->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
						->on('schedules.course_id', '=', 'courses.id')
					->join(array(Model_Locations::TABLE_LOCATIONS, 'rooms'), 'left')
						->on('schedules.location_id', '=', 'rooms.id')
					->join(array(Model_Locations::TABLE_LOCATIONS, 'buildings'), 'left')
						->on('buildings.id', '=', 'rooms.parent_id')
					->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')
						->on('courses.category_id', '=', 'categories.id')
					->join(array((Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? Model_Contacts3::CONTACTS_TABLE : Model_Contacts::TABLE_CONTACT), 'trainers'), 'left')
						->on('schedules.trainer_id', '=', 'trainers.id')
				->where('schedules.delete', '=', 0)
				->and_where('courses.deleted', '=', 0);

		if (@$params['schedule_id']) {
			$searchq->and_where('schedules.id', 'in', $params['schedule_id']);
		}

        if (@$params['schedule_status']) {
            $searchq->and_where('schedules.schedule_status', 'in', $params['schedule_status']);
        }

		if (@$params['course_id']) {
			$searchq->and_where('schedules.course_id', 'in', $params['course_id']);
		}

		if (@$params['location_id']) {
			$searchq->and_where_open()
				->or_where('rooms.id', 'in', $params['location_id'])
				->or_where('buildings.id', 'in', $params['location_id'])
				->and_where_close();
		}

		if (@$params['building_id']) {
			$searchq->and_where('buildings.id', 'in', $params['building_id']);
		}

		if (@$params['room_id']) {
			$searchq->and_where('rooms.id', 'in', $params['room_id']);
		}

		if (isset($params['book_on_website'])) {
			$searchq->and_where('schedules.book_on_website', '=', $params['book_on_website']);
		}

        if (!empty($params['term'])) {
            $searchq->and_where('schedules.name', 'like', '%'.$params['term'].'%');
        }

        if (isset($params['publish'])) {
            $searchq->and_where('schedules.publish', '=', $params['publish']);
        }

        if (!empty($params['limit'])) {
            $searchq->limit($params['limit']);
        }

        if (!empty($params['order_by'])) {
            $dir = (isset($params['direction']) && $params['direction'] = 'desc') ? 'desc' : 'asc';
            $searchq->order_by($params['order_by'], $dir);
        }

		if (@$params['after'] || @$params['before'] || @$params['trainer_id'] || @$params['booked'] == 1) {
			$searchq->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
				->on('schedules.id', '=', 'timeslots.schedule_id');
			$searchq->and_where('timeslots.delete', '=', 0);
		}

        if (@$params['keyword'])
        {
            $keywords = preg_split('/[\ ,]+/i', trim(preg_replace('/[^a-z0-9\ ]/i', '', $params['keyword'])));
            $match1 = array();
            $match2 = array();
            foreach ($keywords as $i => $keyword) {
                if (strlen($keyword) < 3) { // remove too short things like "at" "'s" "on" ...
                    unset($keywords[$i]);
                } else {
                    if (substr($keyword, -3) == 'ies'){
                        $match2[] = '+' . substr($keyword, 0, -3) . 'y' . '*';
                    } else if (substr($keyword, -3) == 'ses' || substr($keyword, -3) == 'xes'){
                        $match2[] = '+' . substr($keyword, 0, -2) . '*';
                    } else if ($keyword[strlen($keyword) - 1] == 's') {
                        $match2[] = '+' . substr($keyword, 0, -1) . '*'; /*'+' . $keyword . '* */
                    } else {
                        $match2[] = '+' . $keyword . '*';
                    }
                    $match1[] = '+' . $keyword . '*';
                }
            }

            $searchq->and_where_open();

            if (!empty($keywords)) {
                $match1 = Database::instance()->escape(implode(' ', $match1));
                $match2 = Database::instance()->escape(implode(' ', $match2));
                // Separate terms, enclose in quotes to stop special characters causing problems, "+" before each term
                $searchq->or_where(DB::expr('match(`courses`.`title`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $searchq->or_where(DB::expr('match(`courses`.`title`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
                $searchq->or_where(DB::expr('match(`schedules`.`name`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $searchq->or_where(DB::expr('match(`schedules`.`name`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
            } else {
                $searchq->or_where('courses.title', 'like', '%' . $params['keyword'] . '%');
                $searchq->or_where('schedules.name', 'like', '%' . $params['keyword'] . '%');
            }
            $searchq->and_where_close();
        }

		if (@$params['after']) {
			$searchq->and_where('timeslots.datetime_end', '>=', $params['after']);
		}
		if (@$params['before']) {
			$searchq->and_where('timeslots.datetime_end', '<=', $params['before']);
		}

		if (@$params['trainer_id']) {
			$searchq->and_where_open();
			$searchq->or_where('schedules.trainer_id', '=', $params['trainer_id']);
			$searchq->or_where('timeslots.trainer_id', '=', $params['trainer_id']);
			$searchq->and_where_close();
		}

		if (@$params['publish']) {
			$searchq->and_where('schedules.publish', '>=', $params['publish']);
		}

		if (@$params['booked'] == 1) {
			$searchq->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'booked_schedules'), 'inner')
                ->on('schedules.id', '=', 'booked_schedules.schedule_id');
            $searchq->and_where('booked_schedules.booking_status', 'in', array(2,4,5))
                ->and_where('booked_schedules.deleted', '=', 0);

            $searchq->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'booked_timeslots'), 'inner')
                ->on('timeslots.id', '=', 'booked_timeslots.period_id')
                ->on('booked_schedules.booking_id', '=', 'booked_timeslots.booking_id');
            $searchq->and_where('booked_timeslots.booking_status', 'in', array(2,4,5))
                ->and_where('booked_timeslots.delete', '=', 0);
		}
		$searchq->order_by('schedules.name');
		$result = $searchq->execute()->as_array();
		return $result;
	}


	public static function get_zones($schedule_id)
	{
        return DB::select(
			'plugin_courses_schedules_have_zones.row_id',
			array('plugin_courses_rows.name', 'row_name'),
			'plugin_courses_schedules_have_zones.zone_id',
			array('plugin_courses_zones.name', 'zone_name'),
            'plugin_courses_schedules_have_zones.schedule_id',
            'plugin_courses_schedules_have_zones.price'
		)
				->from('plugin_courses_schedules_have_zones')
					->join('plugin_courses_rows', 'left')
						->on('plugin_courses_rows.id', '=', 'plugin_courses_schedules_have_zones.row_id')
					->join('plugin_courses_zones', 'left')
						->on('plugin_courses_zones.id', '=', 'plugin_courses_schedules_have_zones.zone_id')
				->where('plugin_courses_schedules_have_zones.schedule_id', '=', $schedule_id)
				->and_where('plugin_courses_zones.deleted', '=', 0)
                ->order_by('plugin_courses_rows.id')
                ->execute()->as_array();

	}

    public static function delete_schedule_zones($schedule_id)
    {
        return DB::delete('plugin_courses_schedules_have_zones')
            ->where('plugin_courses_schedules_have_zones.schedule_id', '=', $schedule_id)
            ->execute();
    }

    public static function save_schedule_zones($zone_rows,$zone_zones,$zone_prices,$schedule_id)
    {
        // add_edit_rows
            $sql_query = "INSERT INTO plugin_courses_schedules_have_zones (plugin_courses_schedules_have_zones.row_id, plugin_courses_schedules_have_zones.zone_id, plugin_courses_schedules_have_zones.schedule_id,plugin_courses_schedules_have_zones.price) VALUES ";
            for ($i = 0; $i < sizeof($zone_rows); $i++) {
                if ($i == 0) {
                    $sql_query .= "(" . Database::instance()->escape($zone_rows[ $i ]) . ", " . Database::instance()->escape($zone_zones[ $i ]) . ", " . $schedule_id . ", " . Database::instance()->escape($zone_prices[ $i ]). ")";
                }
                else {
                    $sql_query .= ",(" . Database::instance()->escape($zone_rows[ $i ]) . ", " . Database::instance()->escape($zone_zones[ $i ]) . ", " . $schedule_id . ", " . Database::instance()->escape($zone_prices[ $i ]) . ")";
                }
            }
            return DB::query(Database::INSERT, $sql_query)->execute();

    }

    public static function delete_schedule_zones_with_specified_rows($arr_of_rows)
    {
        return DB::delete('plugin_courses_schedules_have_zones')
            ->where('plugin_courses_schedules_have_zones.row_id', 'in', $arr_of_rows)
            ->execute();
    }

    public static function get_all_dates_for_schedules($schedule_ids=Array(),$start_date = null, $end_date = null){

        if(sizeof($schedule_ids)>0){
            $schedule_ids=implode(',',$schedule_ids);
            $sql =" SELECT plugin_courses_schedules.id, plugin_courses_schedules.`course_id`,
                        plugin_courses_schedules_events.id AS event_id,
                        DATE(plugin_courses_schedules_events.datetime_start) AS `start_date`,
                        DATE(plugin_courses_schedules_events.datetime_end) AS `end_date`,
                        DATE_FORMAT(plugin_courses_schedules_events.datetime_start,'%H:%i') AS `start_time`,
	                    DATE_FORMAT(plugin_courses_schedules_events.datetime_end,'%H:%i') AS `end_time`,
                        plugin_courses_schedules.fee_amount AS schedule_fee_amount,
	                    IFNULL(plugin_courses_schedules_events.`fee_amount`,plugin_courses_schedules.`fee_amount`) AS time_slot_fee,
	                    plugin_courses_schedules.`fee_per`,
	                    plugin_courses_schedules.booking_type,
	                    plugin_courses_schedules.payment_type,
	                    plugin_courses_schedules.amendable,
	                    plugin_courses_schedules.attend_all_default,
	                    plugin_courses_schedules.trial_timeslot_free_booking
                FROM plugin_courses_schedules
                JOIN plugin_courses_schedules_events ON (plugin_courses_schedules_events.schedule_id = plugin_courses_schedules.id AND plugin_courses_schedules_events.publish=1 AND plugin_courses_schedules_events.delete=0)
                WHERE plugin_courses_schedules.id IN ($schedule_ids) ";

            if ($start_date) {
                $sql .= " \nAND DATE(plugin_courses_schedules_events.datetime_start) >= :start_date";
            }

            if ($end_date) {
                $sql .= " \nAND DATE(plugin_courses_schedules_events.datetime_start) <= :end_date";
            }

            $sql .= " \nORDER BY plugin_courses_schedules_events.datetime_start ASC";

            $query = DB::query(Database::SELECT, $sql);
            $query->param(':start_date', $start_date);
            $query->param(':end_date', $end_date);

            return $query->execute()->as_array();
        }else{
            return array();
        }
    }

    /**
     * get schedules time slots for given duration (dates)
     * @param int $schedule_id
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public static function get_all_time_slots_for_given_duration($schedule_id,$start_date, $end_date){

            $sql =" SELECT  plugin_courses_schedules.id, 
                        plugin_courses_schedules.`course_id`, 
                        plugin_courses_courses.`title` AS course_title,
                        plugin_courses_schedules_events.id AS event_id,
                        DATE(plugin_courses_schedules_events.datetime_start) AS `start_date`,
                        DATE(plugin_courses_schedules_events.datetime_end) AS `end_date`,
                        DATE_FORMAT(plugin_courses_schedules_events.datetime_start,'%H:%i') AS `start_time`,
                        DATE_FORMAT(plugin_courses_schedules_events.datetime_end,'%H:%i') AS `end_time`,
                        plugin_courses_schedules.fee_amount AS schedule_fee_amount,
                        plugin_courses_schedules_events.`fee_amount` AS time_slot_fee,
                        plugin_courses_schedules.`fee_per`,
                        plugin_courses_locations.`name` AS room,
                        LL.`name` AS location,
                        plugin_courses_schedules_events.`trainer_id`,
                        CONCAT(plugin_contacts3_contacts.`first_name`, ' ', plugin_contacts3_contacts.`last_name`) AS trainer,
                        plugin_courses_schedules.`payment_type`,
                        plugin_courses_schedules.booking_type,
                        plugin_courses_schedules.amendable
                    
                    FROM plugin_courses_schedules
                    LEFT JOIN plugin_courses_locations ON (plugin_courses_locations.id = plugin_courses_schedules.`location_id` AND plugin_courses_locations.`delete`=0 AND plugin_courses_locations.`publish`=1)
                    LEFT JOIN plugin_courses_locations LL ON (plugin_courses_locations.parent_id = LL.`id` AND LL.`delete`=0 AND LL.`publish`=1)
                    JOIN plugin_courses_schedules_events ON (plugin_courses_schedules_events.schedule_id = plugin_courses_schedules.id AND plugin_courses_schedules_events.publish=1 AND plugin_courses_schedules_events.delete=0)
                    JOIN plugin_courses_courses ON (plugin_courses_schedules.`course_id` = plugin_courses_courses.id AND plugin_courses_courses.`deleted` = 0 AND plugin_courses_courses.`publish` = 1)
                    LEFT JOIN plugin_contacts3_contacts ON (plugin_courses_schedules_events.`trainer_id` = plugin_contacts3_contacts.`id` AND plugin_contacts3_contacts.`is_inactive`=0 AND plugin_contacts3_contacts.`publish`=1 AND plugin_contacts3_contacts.`delete`=0)
                    WHERE  plugin_courses_schedules.id = ".$schedule_id."
                    AND DATE(plugin_courses_schedules_events.datetime_start) >= :start_date
                    AND DATE(plugin_courses_schedules_events.datetime_end) <= :end_date
                    ORDER BY start_time ";

            $query = DB::query(Database::SELECT, $sql);
            $query->param(':start_date', $start_date);
            $query->param(':end_date', $end_date);
            return $query->execute()->as_array();

    }


    public static function get_course_schedule_details_for_date($course_id, $schedule_id,$date, $filters = []){

            $sql =" SELECT  plugin_courses_schedules.id,
                        plugin_courses_schedules.id as schedule_id,
                        plugin_courses_schedules_events.id as event_id,
                        plugin_courses_schedules.`course_id`, 
                        plugin_courses_schedules.booking_type,
                        plugin_courses_schedules.description,
                        plugin_courses_schedules.attend_all_default,
                        plugin_courses_schedules.display_timeslots_on_frontend,
                        plugin_courses_schedules.display_timeslots_in_cart,
                        plugin_courses_schedules.trial_timeslot_free_booking,
                        plugin_courses_courses.id AS course_id,
                        plugin_courses_courses.`title` AS course_title,
                        plugin_courses_courses.`summary` AS course_summary,
                        plugin_courses_years.`id` AS `year_id`,
                        plugin_courses_years.`year`,
                        plugin_courses_levels.`level`,
                        plugin_courses_schedules_events.datetime_start,
                        plugin_courses_schedules_events.datetime_end,
                        DATE(plugin_courses_schedules_events.datetime_start) AS `start_date`,
                        DATE(plugin_courses_schedules_events.datetime_end) AS `end_date`,
                        DATE_FORMAT(plugin_courses_schedules_events.datetime_start,'%H:%i') AS `start_time`,
                        DATE_FORMAT(plugin_courses_schedules_events.datetime_end,'%H:%i') AS `end_time`,
                        plugin_courses_schedules.fee_amount AS schedule_fee_amount,
                        plugin_courses_schedules_events.`fee_amount` AS time_slot_fee,
                        plugin_courses_schedules.`fee_per`,
                        plugin_courses_locations.`name` AS room,
                        LL.`name` AS location,
                        plugin_courses_schedules_events.`trainer_id`,
                        CONCAT(plugin_contacts3_contacts.`first_name`, ' ', plugin_contacts3_contacts.`last_name`) AS trainer,
                        plugin_courses_schedules.`payment_type`,
                        IF(`plugin_courses_schedules`.`payment_type` = 1, 'Pre-pay', 'PAYG') AS `payment_type_name`,
                        plugin_courses_schedules.`amendable`,
                        plugin_courses_schedules.`is_group_booking`,
                        plugin_courses_schedules.`min_capacity`,
                        plugin_courses_schedules.`max_capacity`,
                        plugin_courses_schedules.`deposit`,
                        TIMEDIFF(`datetime_end`, `datetime_start`) AS `duration`
                    FROM plugin_courses_schedules
                    LEFT JOIN plugin_courses_locations ON (plugin_courses_locations.id = plugin_courses_schedules.`location_id` AND plugin_courses_locations.`delete`=0 AND plugin_courses_locations.`publish`=1)
                    LEFT JOIN plugin_courses_locations LL ON (plugin_courses_locations.parent_id = LL.`id` AND LL.`delete`=0 AND LL.`publish`=1)
                    JOIN plugin_courses_courses ON (`plugin_courses_courses`.id = plugin_courses_schedules.`course_id` AND plugin_courses_courses.`publish`=1 AND plugin_courses_courses.`deleted`=0)
                    JOIN plugin_courses_schedules_events ON (plugin_courses_schedules_events.schedule_id = plugin_courses_schedules.id AND plugin_courses_schedules_events.publish=1 AND plugin_courses_schedules_events.delete=0)
                    LEFT JOIN plugin_contacts3_contacts ON (plugin_courses_schedules_events.`trainer_id` = plugin_contacts3_contacts.`id` AND plugin_contacts3_contacts.`is_inactive`=0 AND plugin_contacts3_contacts.`publish`=1 AND plugin_contacts3_contacts.`delete`=0)
                    LEFT JOIN plugin_courses_years ON (plugin_courses_years.id = plugin_courses_courses.`year_id` AND plugin_courses_years.`delete`=0 AND plugin_courses_years.`publish`=1)
                    LEFT JOIN plugin_courses_levels ON (plugin_courses_levels.id = plugin_courses_courses.`level_id` AND plugin_courses_levels.`delete`=0 AND plugin_courses_levels.`publish`=1)
                    WHERE plugin_courses_schedules.`delete` = 0 ";

			if ($course_id) {
				$sql .= " AND plugin_courses_schedules.`course_id` = " . (int)$course_id . " ";
			}

            if ($schedule_id) {
                $sql .= " AND plugin_courses_schedules.id = ".(int)$schedule_id;
            }

            if ($date) {
                $sql .= " AND DATE(plugin_courses_schedules_events.datetime_start) = :date";
            }

            if (!empty($filters['book_on_website'])) {
                $sql .= " AND `plugin_courses_schedules`.`book_on_website` = ".(int)$filters['book_on_website'];
            }

            if (!empty($filters['event_id'])) {
                $sql .= " AND `plugin_courses_schedules_events`.`id` = ".(int)$filters['event_id'];
            }

            if (!empty($filters['publish'])) {
                $sql .= " AND `plugin_courses_schedules`.`publish` = ".(int)$filters['publish'];
            }

            if (!empty($filters['after'])) {
                $sql .= " AND DATE(plugin_courses_schedules_events.datetime_start) >= :after";
            }

            $sql .= " ORDER BY plugin_courses_schedules_events.datetime_start ";

            $query = DB::query(Database::SELECT, $sql);
            $query->param(':date', $date);
            if (!empty($filters['after'])) {
                $query->param(':after', $filters['after']);
            }

            $schedules = $query->execute()->as_array();

            foreach ($schedules as $i => $schedule) {
                $schedules[$i]['paymentoptions'] = DB::select('*')
                    ->from(self::TABLE_HAS_PAYMENTOPTIONS)
                    ->where('schedule_id', '=', $schedule['id'])
                    ->and_where('deleted', '=', 0)
                    ->order_by('months', 'asc')
                    ->execute()
                    ->as_array();
            }
            return $schedules;
    }

    public static function get_whole_schedule_events($schedule_id){
        if(!$schedule_id){
            return array();
        }
        $query = DB::select('*', DB::expr("DATE_FORMAT(datetime_start, '%W %d %M') as date_formatted"))
				->from(array(self::TABLE_TIMESLOTS, 't'))
				->where('t.schedule_id', '=', $schedule_id)
				->and_where('t.delete', '=', 0)
				->and_where('t.publish', '=', 1)
				->order_by('t.datetime_start', 'asc');
        return $query->execute()->as_array();
    }

    public static function get_whole_schedule_events_count($schedule_id){
        if(!$schedule_id){
            return array();
        }
        $query = DB::select('*', DB::expr("DATE_FORMAT(datetime_start, '%W %d %M') as date_formatted"))
            ->from(array(self::TABLE_TIMESLOTS, 't'))
            ->where('t.schedule_id', '=', $schedule_id)
            ->and_where('t.delete', '=', 0)
            ->and_where('t.publish', '=', 1)
            ->and_where('t.datetime_start', '>=', DB::expr('CURDATE()'))
            ->order_by('t.datetime_start', 'asc');
        return $query->execute()->as_array();
    }


    /**
     * Get the number of available seats for a given timeslot
     *
     * @param int   $timeslot_id - the ID of the timeslot to be checked
     * @param array $args        - associative array of additional arguments
     *                  int  ['row_id']    - filter the check to a particular row
     *                  int  ['zone_id']   - filter the check to a particular row
     *
     * @return array
     *                  int  ['total']     - the total number of seats
     *                  int  ['booked']    - the number of booked seats
     *                  int  ['remaining'] - the number of remaining seats
     */
    public static function get_remaining_seats($timeslot_id, $args = array())
    {
        $q = DB::select(array(DB::expr("SUM(`row`.`seats`)"), 'seats'))
            ->from(array(self::TABLE_TIMESLOTS,                 'timeslot'))
            ->join(array(self::TABLE_SCHEDULES,                 'schedule'), 'LEFT')->on('timeslot.schedule_id', '=', 'schedule.id')
            ->join(array('plugin_courses_schedules_have_zones', 'shz'     ), 'LEFT')->on('shz.schedule_id',      '=', 'schedule.id')
            ->join(array('plugin_courses_zones',                'zone'    ), 'LEFT')->on('shz.zone_id',          '=', 'zone.id')->on('zone.deleted', '=', DB::expr("0"))
            ->join(array('plugin_courses_rows',                 'row'     ), 'LEFT')->on('shz.row_id',           '=', 'row.id')
            ->where('timeslot.id', '=', $timeslot_id)
        ;

        if ( ! empty($args['row_id'] )) $q->where('row.id',  '=', $args['row_id'] );
        if ( ! empty($args['zone_id'])) $q->where('zone.id', '=', $args['zone_id']);

        $total_seats = (int) $q->execute()->get('seats', 0);

        if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings'))
        {
            $q= DB::select(array(DB::expr("COUNT(`item`.`booking_item_id`)"), 'count'))
                ->from(array('plugin_ib_educate_booking_items',     'item'   ))
                ->join(array('plugin_ib_educate_bookings',          'booking'), 'LEFT')->on('item.booking_id',        '=', 'booking.booking_id')
                ->join(array('plugin_ib_educate_bookings_status',   'status' ), 'LEFT')->on('booking.booking_status', '=', 'status.status_id')
                ->join(array('plugin_courses_rows',                 'row'    ), 'LEFT')->on('item.seat_row_id',       '=', 'row.id')
                ->join(array('plugin_courses_schedules_have_zones', 'shz'    ), 'LEFT')->on('shz.row_id',             '=', 'row.id')
                ->join(array('plugin_courses_zones',                'zone'   ), 'LEFT')->on('shz.zone_id',            '=', 'zone.id')->on('zone.deleted', '=', DB::expr("0"))
                ->where('item.delete',    '=',  0)
                ->where('item.attending', '=',  1)
                ->where('item.period_id', '=',  $timeslot_id)
                ->where('status.title',   'IN', array('In Progress', 'Completed', 'Confirmed'));

            if ( ! empty($args['row_id']))  $q->where('row.id',  '=', $args['row_id'] );
            if ( ! empty($args['zone_id'])) $q->where('zone.id', '=', $args['zone_id']);

            $seats_booked = (int) $q->execute()->get('count', 0);
        }
        else
        {
            $seats_booked = 0;
        }

        $available = $total_seats - $seats_booked;

        return array(
            'total'      => $total_seats,
            'booked'     => $seats_booked,
            'available'  => ($available < 0) ? 0 : $available,
            'overbooked' => ($available < 0)
        );

    }

    /**
     * Validation function to check if the number of seats
     *
     * @param $booking_items - the booking items variable generated by the checkout submission
     *
     * @return array
     *            boolean  ['valid']  - True, if there are no issues with the seating. False, otherwise.
     *            array    ['errors'] - List of error messages, if any
     */
    public static function validate_seats($booking_items)
    {
        $valid  = true;
        $errors = array();

        $seats = array();
        if ( ! empty($booking_items)) {
            foreach ($booking_items as $schedule_id => $timeslots) {
                foreach ($timeslots as $timeslot_id => $timeslot) {}
                if ( ! empty($timeslot['seat_row_id'])) {
                    @$seats[$timeslot_id][$timeslot['seat_row_id']] += 1;
                }
            }
        }

        foreach ($seats as $timeslot_id => $timeslot_seats) {
            foreach ($timeslot_seats as $seat_row_id => $qty) {
                $remaining = Model_Schedules::get_remaining_seats($timeslot_id, array('row_id' => $seat_row_id));
                if ($remaining['available'] < $qty) {
                    $valid = false;
                    $errors[] = __('Not enough seats left in zone $1', array('$1' => $seat_row_id));
                }
            }
        }

        return array(
            'valid'  => $valid,
            'errors' => $errors
        );
    }


	public static function get_possible_years($location_ids = array(), $is_fulltime = null, $provider_ids = null)
	{
		$selectq = DB::select('years.*')
			->distinct('*')
			->from (array(Model_Years::YEARS_TABLE, 'years'))
				->join(array(Model_Courses::TABLE_HAS_YEARS, 'has_years'), 'inner')->on('years.id', '=', 'has_years.year_id')
				->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')->on('has_years.course_id', '=', 'courses.id')
				->join(array(Model_courses::TABLE_HAS_PROVIDERS, 'has_providers'), 'left')->on('has_providers.course_id', '=', 'courses.id')
				->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('courses.id', '=', 'schedules.course_id')
				->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id')
				->join(array(Model_Locations::TABLE_LOCATIONS, 'building'), 'left')->on('locations.parent_id', '=', 'building.id')
			->where('schedules.delete', '=', 0)
			->and_where('schedules.publish', '=', 1)
			->and_where('schedules.end_date', '>=', date('Y-m-d H:i:s'))
			->and_where('courses.publish', '=', 1)
			->and_where('courses.deleted', '=', 0);
		if (count($location_ids) > 0) {
			$selectq->and_where_open();
				$selectq->or_where('schedules.location_id', 'in', $location_ids);
				$selectq->or_where('building.id', 'in', $location_ids);
			$selectq->and_where_close();
		}
        if ($is_fulltime !== null && $is_fulltime !== '') {
            $selectq->and_where('courses.is_fulltime', '=', $is_fulltime);
        }
		if (!empty($provider_ids)) {
			$selectq->and_where('has_providers.provider_id', 'IN', $provider_ids);
		}

		$selectq->order_by(DB::expr("year = 'All Levels'"), 'desc');
		$selectq->order_by('year');

		$years = $selectq->execute()->as_array();
		return $years;
	}

    public static function calculate_fee_for_schedule($schedule_id, $timeslot_ids)
    {
        $fee = null;
        $schedule = DB::select('*')
            ->from(self::TABLE_SCHEDULES)
            ->where('id', '=', $schedule_id)
            ->execute()
            ->current();
        if ($schedule['fee_per'] == 'Schedule') {
            $fee = $schedule['fee_amount'];
        } else {
            $timeslots = DB::select('*')
                ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                ->where('id', 'in', $timeslot_ids)
                ->execute()
                ->as_array();

            $fee = 0;
            if ($schedule['fee_per'] == 'Timeslot') {
                foreach ($timeslots as $timeslot) {
                    $fee += $timeslot['fee_amount'] ? $timeslot['fee_amount'] : $schedule['fee_amount'];
                }
            }
            if ($schedule['fee_per'] == 'Day') {
                $days = array();
                foreach ($timeslots as $timeslot) {
                    $day = date('Y-m-d', strtotime($timeslot['datetime_start']));
                    if (!in_array($day, $days)) {
                        $days[] = $day;
                    }
                }
                $fee = count($days) * $schedule['fee_amount'];
            }
        }

        return $fee;
    }

	public static function get_students($schedule_id)
	{
		if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
			$select = DB::select(
				array('students.id', 'student_id'),
				'students.title',
				'students.first_name',
				'students.last_name',
				'bookings.booking_id',
                array('courses.title', 'course'),
				'has_schedules.schedule_id',
				array('schedules.name' ,'schedule'),
                array('levels.id', 'level_id'),
                array('levels.level', 'level')
			)
				->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
					->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
						->on('bookings.booking_id', '=', 'has_schedules.booking_id')
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('has_schedules.schedule_id', '=', 'schedules.id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                        ->on('schedules.course_id', '=', 'courses.id')
                    ->join(array('plugin_courses_levels', 'levels'), 'left')
                        ->on('courses.level_id', '=', 'levels.id')
					->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
						->on('bookings.contact_id', '=', 'students.id')
				->and_where('bookings.delete', '=', 0)
				->and_where('has_schedules.deleted', '=', 0)
				->and_where('has_schedules.booking_status', 'in', array(2,5))
				->and_where('bookings.booking_status', 'in', array(2,5));

            if (!empty($schedule_id)) {
                if (is_array($schedule_id)) {
                    $select->and_where('has_schedules.schedule_id', 'in', $schedule_id);
                } else {
                    $select->and_where('has_schedules.schedule_id', '=', $schedule_id);
                }
            }
            $result = $select->group_by('students.id')->execute()->as_array('student_id');
            $select_delegates = DB::select(
                array('students.id', 'student_id'),
                'students.title',
                'has_delegates.cancelled',
                'students.first_name',
                'students.last_name',
                'bookings.booking_id',
                array('courses.title', 'course'),
                'has_schedules.schedule_id',
                array('schedules.name' ,'schedule'),
                array('levels.id', 'level_id'),
                array('levels.level', 'level')
            )
                ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                    ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
                        ->on('bookings.booking_id', '=', 'has_schedules.booking_id')
                    ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'), 'inner')
                        ->on('bookings.booking_id', '=', 'has_delegates.booking_id')
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('has_schedules.schedule_id', '=', 'schedules.id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                        ->on('schedules.course_id', '=', 'courses.id')
                    ->join(array('plugin_courses_levels', 'levels'), 'left')
                        ->on('courses.level_id', '=', 'levels.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                        ->on('has_delegates.contact_id', '=', 'students.id')
                ->and_where('bookings.delete', '=', 0)
                ->and_where('has_schedules.deleted', '=', 0)
                ->and_where('has_schedules.booking_status', 'in', array(2,5))
                ->and_where('bookings.booking_status', 'in', array(2,5))
                ->and_where('has_delegates.deleted', '=' , 0)
                ->and_where('has_delegates.cancelled', '=' , 0);
            if(!empty($schedule_id)) {
                if (is_array($schedule_id)) {
                    $select_delegates->and_where('has_schedules.schedule_id', 'in', $schedule_id);
                } else {
                    $select_delegates->and_where('has_schedules.schedule_id', '=', $schedule_id);
                }
            }
            $delegates_result = $select_delegates->group_by('students.id')->execute()->as_array('student_id');
            return !empty($delegates_result) ? array_values($delegates_result) : array_values($result);
		} else {
            return array();
		}
  
	}
	
	public static function get_trainers_in_active_schedules($start_date = false, $end_date = false) {
        $select = DB::select(array('cs.trainer_id', 'id'))
            ->from(array(self::TABLE_SCHEDULES, 'cs'))
            ->join(array('plugin_ib_educate_booking_has_schedules', 'bhs'), 'inner')
            ->on('bhs.schedule_id', '=', 'cs.id')
            ->join(array('plugin_ib_educate_booking_items', 'bi'), 'inner')
            ->on('bi.booking_id', '=', 'bhs.booking_id')
            ->join(array('plugin_courses_schedules_events', 'cse'), 'inner')
            ->on('cse.id', '=', 'bi.period_id');
        if($start_date && $end_date) {
            $select->where('cse.datetime_start', '>=', $start_date)
                ->where('cse.datetime_end', '<=', $end_date);
        }
        $trainers = $select->where('cs.trainer_id', 'is not', null)
            ->group_by('cs.trainer_id')
            ->execute()->as_array();
        return $trainers;
    }
}
