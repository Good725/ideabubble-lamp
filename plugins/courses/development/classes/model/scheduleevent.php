<?php defined('SYSPATH') or die('No direct script access.');

class Model_ScheduleEvent extends ORM
{
    const TABLE_SCHEDULES = 'plugin_courses_schedules';
	const TABLE_TIMESLOTS = 'plugin_courses_schedules_events';

	protected $_table_name = 'plugin_courses_schedules_events';

	public static function bulkDelete($ids)
	{
		DB::update('plugin_courses_schedules_events')
			->set(array('delete' => 1))
			->where('id', 'in', $ids)
			->execute();
	}

    public static function get($timeslot_id)
    {
        $timeslot = DB::select('*')->from(self::TABLE_TIMESLOTS)->where('id', '=', $timeslot_id)->where('delete', '=', 0)->execute()->current();

        if (!isset($timeslot['id'])) {
            // If no results are found, return an empty associative array
            $columns = Database::instance()->list_columns('plugin_courses_schedules');

            $timeslot = [];
            foreach ($columns as $column => $data) {
                $timeslot[$column] = '';
            }
        }

        return $timeslot;
    }

	public static function search($params = array())
	{
		$searchq = DB::select(
				DB::expr('DISTINCT timeslots.*'),
				DB::expr("DATE_FORMAT(datetime_start, '%a %H:%i') as period"),
				DB::expr("DATE_FORMAT(datetime_end, '%H:%i') as period_end"),
				'categories.category',
				array('courses.title', 'course'),
				array('schedules.name', 'schedule'),
				DB::expr('IF(timeslots.max_capacity, timeslots.max_capacity,schedules.max_capacity) as max_capacity'),
				array('rooms.id', 'location_id'),
				array('rooms.name', 'room'),
				array('buildings.id', 'building_id'),
				array('buildings.name', 'building'),
				DB::expr("CONCAT_WS(' ', buildings.name, rooms.name) as location"),
				DB::expr("CONCAT_WS(' ', IF(timeslot_trainers.id, timeslot_trainers.first_name, trainers.first_name), IF(timeslot_trainers.id, timeslot_trainers.last_name, trainers.last_name)) AS `trainer`"),
				DB::expr('IFNULL(statsq.cnt, 0) as booking_count'),
				array('subjects.name', 'subject'),
				'subjects.color'
		)
				->from(array(self::TABLE_SCHEDULES, 'schedules'))
				->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
				->on('schedules.course_id', '=', 'courses.id')
				->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
				->on('courses.subject_id', '=', 'subjects.id')
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
			$searchq->and_where('schedules.id', (is_array($params['schedule_id']) ? 'in' : '='), $params['schedule_id']);
		}

		if (@$params['course_id']) {
			$searchq->and_where('schedules.course_id', '=', $params['course_id']);
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

		if (!empty($params['term'])) {
			$searchq->and_where('schedules.name', 'like', '%'.$params['term'].'%');
		}

		if (!empty($params['limit'])) {
			$searchq->limit($params['limit']);
		}

		if (!empty($params['order_by'])) {
			$dir = (isset($params['direction']) && $params['direction'] = 'desc') ? 'desc' : 'asc';
			$searchq->order_by($params['order_by'], $dir);
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

		$searchq->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
				->on('schedules.id', '=', 'timeslots.schedule_id')
			->join(array((Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? Model_Contacts3::CONTACTS_TABLE : Model_Contacts::TABLE_CONTACT), 'timeslot_trainers'), 'left')
				->on('timeslots.trainer_id', '=', 'timeslot_trainers.id');

		$searchq->and_where('timeslots.delete', '=', 0);

		if (@$params['after']) {
			$searchq->and_where('timeslots.datetime_end', '>=', $params['after']);
		}
		if (@$params['before']) {
			$searchq->and_where('timeslots.datetime_end', '<=', $params['before']);
		}

		$statsq = DB::select(DB::expr("count(*) as 'cnt'"), array("timeslots.id", 'timeslot_id'))
				->from(array(self::TABLE_TIMESLOTS, 'timeslots'))
					->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'), 'inner')
						->on('timeslots.id', '=', 'items.period_id')
				->where('timeslots.delete', '=', 0)
				->and_where('items.delete', '=', 0)
				->and_where('items.booking_status', 'in', array(2,4,5))
				->group_by('timeslots.id');
		$searchq->join(array($statsq, 'statsq'), 'left')
			->on('timeslots.id', '=', 'statsq.timeslot_id');

		if (@$params['trainer_id']) {
			$searchq->and_where_open();
			$searchq->or_where('schedules.trainer_id', '=', $params['trainer_id']);
			$searchq->or_where('timeslots.trainer_id', '=', $params['trainer_id']);
			$searchq->and_where_close();
		}

		if (@$params['booked'] == 1) {
			$searchq->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'booked_timeslots'), 'inner')
					->on('timeslots.id', '=', 'booked_timeslots.period_id');
            $searchq->and_where('booked_timeslots.booking_status', 'in', array(2,4,5))
                ->and_where('booked_timeslots.delete', '=', 0);
		}

		$searchq->order_by('timeslots.datetime_start');
		$result = $searchq->execute()->as_array();
		return $result;
	}

	public static function get_periods($schedule_id)
	{
		$timeslots = self::search(array('schedule_id' => $schedule_id));
		$periods = [];
		foreach ($timeslots as $timeslot) {
			$period = trim($timeslot['period'] . ' ' . $timeslot['trainer']);
			if (!isset($periods[$period])) {
                $periods[$period] = array(
                    'period' => $timeslot['period'],
					'period_end' => $timeslot['period_end'],
					'trainer' => $timeslot['trainer'],
                    'trainer_id' => $timeslot['trainer_id'],
                    'booking_count' => $timeslot['booking_count'],
                    'max_capacity' => $timeslot['max_capacity']
                );
            }
		}
        return $periods;
	}
}