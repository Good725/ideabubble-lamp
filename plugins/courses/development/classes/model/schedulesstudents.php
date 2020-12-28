<?php defined('SYSPATH') or die('No direct script access.');

class Model_SchedulesStudents extends Model
{
	const REGISTRATION = 'plugin_courses_schedules_has_students';

    public static function get_contact_table_name()
    {
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            return Model_Contacts3::CONTACTS_TABLE;
        } else {
            return Model_Contacts::TABLE_CONTACT;
        }
    }

	public static function get($id)
	{
        $registration = DB::select(
            'has.*',
            array('schedules.name', 'schedule'),
            DB::expr("CONCAT_WS(' ', first_name, last_name) AS student")
        )
            ->from(array(self::REGISTRATION, 'has'))
                ->join(array(self::get_contact_table_name(), 'students'), 'inner')->on('has.contact_id', '=', 'students.id')
                ->join(array('plugin_courses_schedules', 'schedules'), 'inner')->on('has.schedule_id', '=', 'schedules.id')
            ->where('has.id', '=', $id)
            ->execute()
            ->current();

        return $registration;
	}

	public static function save($id, $contact_id, $schedule_id, $status, $notes)
	{
        $registration = array();
        $registration['contact_id'] = $contact_id;
        $registration['schedule_id'] = $schedule_id;
        $registration['status'] = $status;
        $registration['notes'] = $notes;

        $user = Auth::instance()->get_user();
        if (is_numeric($id)) {
            $registration['updated'] = date('Y-m-d H:i:s');
            $registration['updated_by'] = $user['id'];
            DB::update(self::REGISTRATION)->set($registration)->where('id', '=', $id)->execute();
        } else {
            $registration['created'] = date('Y-m-d H:i:s');
            $registration['created_by'] = $user['id'];
            $inserted = DB::insert(self::REGISTRATION)->values($registration)->execute();
            $id = $inserted[0];
        }

        return $id;
	}

	public static function search_for_datatable($term = '', $offset = 0, $limit = 10, $scol = 0, $sdir = 'asc')
	{
        $sortCols = array(
            'has.id',
            'first_name',
            'last_name',
            'schedule',
            'has.status',
            'has.updated'
        );
        $q = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS has.*'),
            array('schedules.name', 'schedule'),
            'first_name',
            'last_name'
        )
            ->from(array(self::REGISTRATION, 'has'))
            ->join(array(self::get_contact_table_name(), 'students'), 'inner')->on('has.contact_id', '=', 'students.id')
            ->join(array('plugin_courses_schedules', 'schedules'), 'inner')->on('has.schedule_id', '=', 'schedules.id');

        if ($term) {
            $q->and_where_open()
                ->or_where('schedules.name', 'like', '%' . $term . '%')
                ->or_where('students.first_name', 'like', '%' . $term . '%')
                ->or_where('students.last_name', 'like', '%' . $term . '%')
                ->and_where_close();
        }
        if ($scol & $sdir) {
            $q->order_by($sortCols[$scol], $sdir);
        }
		if ($limit >= 0)
		{
			$q->limit($limit);
			$q->offset($offset);

		}
        $rows = $q->execute()->as_array();

        $total = DB::select(DB::expr('FOUND_ROWS() as total'))->execute()->get('total');
        $data = array(
            'sEcho'                => (int) @$_GET['sEcho'],
            'iTotalRecords'        => (int) $total,
            'iTotalDisplayRecords' => (int) $total,
            'aaData'               => array()
        );

        foreach ($rows as $row) {
            $data['aaData'][] = array(
                '<a href="/admin/courses/student_schedule_registration/' . $row['id'] . '">' . $row['id'] . '</a>',
                '<a href="/admin/courses/student_schedule_registration/' . $row['id'] . '">' . $row['first_name'] . '</a>',
                '<a href="/admin/courses/student_schedule_registration/' . $row['id'] . '">' . $row['last_name'] . '</a>',
                '<a href="/admin/courses/student_schedule_registration/' . $row['id'] . '">' . $row['schedule'] . '</a>',
                '<a href="/admin/courses/student_schedule_registration/' . $row['id'] . '">' . $row['status'] . '</a>',
                '<a href="/admin/courses/student_schedule_registration/' . $row['id'] . '">' . $row['updated'] . '</a>'
            );
        }
        return $data;

	}

    public static function search($params = array())
    {
        $q = DB::select(
            'has.*',
            array('schedules.name', 'schedule'),
            'first_name',
            'last_name'
        )
            ->from(array(self::REGISTRATION, 'has'))
            ->join(array(self::get_contact_table_name(), 'students'), 'inner')->on('has.contact_id', '=', 'students.id')
            ->join(array('plugin_courses_schedules', 'schedules'), 'inner')->on('has.schedule_id', '=', 'schedules.id');

        if (@$params['contact_id']) {
            $q->where('has.contact_id', '=', $params['contact_id']);
        }
        if (@$params['schedule_id']) {
            $q->where('has.schedule_id', '=', $params['schedule_id']);
        }
        $rows = $q->execute()->as_array();

        return $rows;
    }
}