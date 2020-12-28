<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Homework extends ORM
{
	const TABLE_HOMEWORK = 'plugin_homework_homeworks';
	const TABLE_FILES    = 'plugin_homework_has_files';

	protected $_table_name = self::TABLE_HOMEWORK;
	protected $_date_created_column = 'created';
	protected $_modified_by_column = 'updated_by';
	protected $_date_modified_column = 'updated';

	public static function get($id)
	{
		$homework = DB::select('homeworks.*', 'events.schedule_id', 'events.datetime_start', array('schedules.name', 'schedule'))
			->from(array(self::TABLE_HOMEWORK, 'homeworks'))
			->join(array('plugin_courses_schedules_events', 'events'), 'inner')
			->on('homeworks.course_schedule_event_id', '=', 'events.id')
			->join(array('plugin_courses_schedules', 'schedules'), 'inner')
			->on('events.schedule_id', '=', 'schedules.id')
			->where('homeworks.id', '=', $id)
			->execute()
			->current();
		if ($homework)
		{
			$homework['files'] = DB::select('files.*', 'has.*', DB::expr("CONCAT_WS(' ', users.name, users.surname) AS author"))
				->from(array(self::TABLE_FILES, 'has'))
				->join(array(Model_Files::TABLE_FILE, 'files'))->on('has.file_id', '=', 'files.id')
				->join(array('engine_users', 'users'))->on('files.created_by', '=', 'users.id')
				->where('files.deleted', '=', 0)
				->and_where('has.homework_id', '=', $id)
				->execute()
				->as_array();
		}

		return $homework;
	}

	public static function save_homework($id, $course_schedule_event_id, $title, $description, $published, $deleted, $files)
	{
		$user                                 = Auth::instance()->get_user();
		$homework                             = array();
		$homework['course_schedule_event_id'] = $course_schedule_event_id;
		$homework['title']                    = $title;
		$homework['description']              = $description;
		$homework['published']                = $published;
		$homework['deleted']                  = $deleted;
		if (is_numeric($id))
		{
			$homework['updated']    = date('Y-m-d H:i:s');
			$homework['updated_by'] = $user['id'];
			DB::update(self::TABLE_HOMEWORK)->set($homework)->where('id', '=', $id)->execute();
		}
		else
		{
			$homework['created']    = date('Y-m-d H:i:s');
			$homework['created_by'] = $user['id'];
			$inserted               = DB::insert(self::TABLE_HOMEWORK)->values($homework)->execute();
			$id                     = $inserted[0];
		}
		DB::delete(self::TABLE_FILES)->where('homework_id', '=', $id)->execute();
		foreach ($files as $file_id)
		{
			DB::insert(self::TABLE_FILES)->values(array('homework_id' => $id, 'file_id' => $file_id))->execute();
		}

		return $id;
	}

    public static function search_for_datatable($filters = [], $args = [])
	{
        $term              = isset($filters['term'])        ? $filters['term']        : null;
        $limit_user_id     = isset($filters['user_id'])     ? $filters['user_id']     : null;
        $limit_contact_ids = isset($filters['contact_ids']) ? $filters['contact_ids'] : null;

        $limit  = isset($args['limit'])   ? $args['limit']   : 10;
        $offset = isset($args['offset'])  ? $args['offset']  : 0;
        $scol   = isset($args['sort_by']) ? $args['sort_by'] : 'events.datetime_start';
        $sdir   = (isset($args['direction']) && $args['direction'] == 'desc') ? 'desc' : 'asc';
        $return_query_only = isset($args['query_only']) ? $args['query_only'] : false;

		$sortCols = array(
			'trainer1',
			'schedule',
            'course.title',
			'events.datetime_start'
		);

        $scol = is_numeric($scol) ? $sortCols[$scol] : $scol;

		$q = DB::select(
			DB::expr('SQL_CALC_FOUND_ROWS homeworks.*'),
			'events.schedule_id',
			'events.datetime_start',
			array('schedules.name', 'schedule'),
			DB::expr("CONCAT_WS(' ', trainers1.first_name, trainers1.last_name) AS trainer1"),
            array('course.title', 'course')
		)
			->from(array(self::TABLE_HOMEWORK, 'homeworks'))
			->join(array('plugin_courses_schedules_events', 'events'), 'inner')
			->on('homeworks.course_schedule_event_id', '=', 'events.id')
			->join(array('plugin_courses_schedules', 'schedules'), 'inner')
			->on('events.schedule_id', '=', 'schedules.id')
            ->join(array('plugin_courses_courses', 'course'))
            ->on('schedules.course_id', '=', 'course.id')
        ;
		if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
			$q->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers1'), 'left')
					->on('schedules.trainer_id', '=', 'trainers1.id');
		} else {
			$q->join(array(Model_Contacts::TABLE_CONTACT, 'trainers1'), 'left')
				->on('schedules.trainer_id', '=', 'trainers1.id');
		}

        if (!empty($filters['published_only'])) {
            $q->and_Where('homeworks.published', '=', 1);
        }

		$q->and_where('homeworks.deleted', '=', 0);
		if ($term)
		{
			$q->and_where_open()
				->or_where('homeworks.title', 'like', '%'.$term.'%')
				->or_where('homeworks.description', 'like', '%'.$term.'%')
				->or_where('schedules.name', 'like', '%'.$term.'%')
				->or_where('trainers1.first_name', 'like', '%'.$term.'%')
				->or_where('trainers1.last_name', 'like', '%'.$term.'%')
				->and_where_close();
		}

		if ($limit_contact_ids) {
			if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
				$filter_students = DB::select('schedules.id')
						->from(array(Model_Contacts3::CONTACTS_TABLE, 'students'))
							->join(array('plugin_courses_schedules_has_students', 'has_student'))
								->on('students.id', '=', 'has_student.contact_id')
							->join(array('plugin_courses_schedules', 'schedules'))
								->on('has_student.schedule_id', '=', 'schedules.id')
						->where('students.id', 'in', $limit_contact_ids);
			} else {
				$filter_students = DB::select('schedules.id')
						->from(array(Model_Contacts::TABLE_CONTACT, 'students'))
							->join(array('plugin_courses_schedules_has_students', 'has_student'))
								->on('students.id', '=', 'has_student.contact_id')
							->join(array('plugin_courses_schedules', 'schedules'))
								->on('has_student.schedule_id', '=', 'schedules.id')
						->where('students.id', 'in', $limit_contact_ids);
			}

			$q->and_where_open();
			$q->or_where('schedules.id', 'in', $filter_students);
			$q->and_where_close();
		}

		if ($limit_user_id)
		{
			if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
				$filter_trainers = DB::select('schedules.id')
					->from(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'))
						->join(array(Model_Contacts3::TABLE_PERMISSION_LIMIT, 'permission'), 'inner')
							->on('permission.user_id', '=', DB::expr($limit_user_id))
							->on('permission.contact3_id', '=', 'trainers.id')
						->join(array('plugin_courses_schedules', 'schedules'))
							->on('schedules.trainer_id', '=', 'trainers.id');
				$filter_students = DB::select('schedules.id')
					->from(array(Model_Contacts3::CONTACTS_TABLE, 'students'))
						->join(array(Model_Contacts3::TABLE_PERMISSION_LIMIT, 'permission'), 'inner')
							->on('permission.user_id', '=', DB::expr($limit_user_id))
							->on('permission.contact3_id', '=', 'students.id')
						->join(array('plugin_courses_schedules_has_students', 'has_student'))
							->on('students.id', '=', 'has_student.contact_id')
						->join(array('plugin_courses_schedules', 'schedules'))
							->on('has_student.schedule_id', '=', 'schedules.id');
				$filter_parents = DB::select('schedules.id')
					->from(array(Model_Contacts3::CONTACTS_TABLE, 'parents'))
						->join(array(Model_Contacts3::TABLE_PERMISSION_LIMIT, 'permission'), 'inner')
							->on('permission.user_id', '=', DB::expr($limit_user_id))
							->on('permission.contact3_id', '=', 'parents.id')
						->join(array(Model_Contacts3::CONTACTS_TABLE, 'fmembers'), 'inner')
							->on('parents.family_id', '=', 'fmembers.family_id')
						->join(array('plugin_courses_schedules_has_students', 'has_student'))
							->on('fmembers.id', '=', 'has_student.contact_id')
						->join(array('plugin_courses_schedules', 'schedules'))
							->on('has_student.schedule_id', '=', 'schedules.id');
			} else {
				$filter_trainers = DB::select('schedules.id')
					->from(array(Model_Contacts::TABLE_CONTACT, 'trainers'))
						->join(array(Model_Contacts::TABLE_PERMISSION_LIMIT, 'permission'), 'inner')
							->on('permission.user_id', '=', DB::expr($limit_user_id))
							->on('permission.contact_id', '=', 'trainers.id')
						->join(array('plugin_courses_schedules', 'schedules'))
							->on('schedules.trainer_id', '=', 'trainers.id');
				$filter_students = DB::select('schedules.id')
					->from(array(Model_Contacts::TABLE_CONTACT, 'students'))
						->join(array(Model_Contacts::TABLE_PERMISSION_LIMIT, 'permission'), 'inner')
							->on('permission.user_id', '=', DB::expr($limit_user_id))
							->on('permission.contact_id', '=', 'students.id')
						->join(array('plugin_courses_schedules_has_students', 'has_student'))
							->on('students.id', '=', 'has_student.contact_id')
						->join(array('plugin_courses_schedules', 'schedules'))
							->on('has_student.schedule_id', '=', 'schedules.id');
				$filter_parents = DB::select('schedules.id')
					->from(array(Model_Contacts::TABLE_CONTACT, 'parents'))
						->join(array(Model_Contacts::TABLE_PERMISSION_LIMIT, 'permission'), 'inner')
							->on('permission.user_id', '=', DB::expr($limit_user_id))
							->on('permission.contact_id', '=', 'parents.id')
						->join(array(Model_Contacts::TABLE_HAS_RELATIONS, 'related'))
							->on('parents.id', '=', 'related.contact_2_id')
						->join(array(Model_Contacts::TABLE_CONTACT, 'students'))
							->on('related.contact_1_id', '=', 'students.id')
						->join(array('plugin_courses_schedules_has_students', 'has_student'))
							->on('students.id', '=', 'has_student.contact_id')
						->join(array('plugin_courses_schedules', 'schedules'))
							->on('has_student.schedule_id', '=', 'schedules.id');
			}

			$q->and_where_open();
			$q->or_where('schedules.id', 'in', $filter_trainers);
			$q->or_where('schedules.id', 'in', $filter_students);
			$q->or_where('schedules.id', 'in', $filter_parents);
			$q->and_where_close();
		}
		if ($scol & $sdir)
		{
			$q->order_by($scol, $sdir);
		}
		if ($limit > 0)
		{
			$q->limit($limit);
			$q->offset($offset);
		}
		if ($return_query_only) {
			return $q;
		}
		$rows = $q->execute()->as_array();

		$total = DB::select(DB::expr('FOUND_ROWS() as total'))->execute()->get('total');
		$data  = array(
			'sEcho'                => (int) @$_GET['sEcho'],
			'iTotalRecords'        => (int) $total,
			'iTotalDisplayRecords' => (int) $total,
			'aaData'               => array()
		);

		if (Auth::instance()->has_access('homework_edit') || Auth::instance()->has_access('homework_edit_limited'))
		{
			$edit_or_view = 'edit';
		}
		else
		{
			$edit_or_view = 'view';
		}

        $can_delete = Auth::instance()->has_access('homework_delete');

		foreach ($rows as $row)
		{
			$data['aaData'][] = array(
				$row['trainer1'],
				$row['schedule'],
				'<a href="/admin/homework/'.$edit_or_view.'/'.$row['id'].'" class="edit-link">'.$row['course'].'</a>',
				IbHelpers::relative_time_with_tooltip($row['datetime_start']),
                '<div class="text-sm-center">
                    <div class="action-btn">
                        <a><span class="icon-ellipsis-h" aria-hidden="true"></span></a>
                        <ul>
                            <li><a href="/admin/homework/'.$edit_or_view.'/'.$row['id'].'">View</a></li>'.
                            ($can_delete ? '<li><a class="delete-button" data-id="'.$row['id'].'">Delete</a></li>' : '').'
                        </ul>
                    </div>
                </div>'
			);
		}
		return $data;
	}
}