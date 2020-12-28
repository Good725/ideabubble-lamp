<?php defined('SYSPATH') or die('No direct script access.');

class Model_Coursecredits extends Model
{
    const CREDITS_TABLE = 'plugin_courses_credits';
    const HAS_SCHEDULES_TABLE = 'plugin_courses_credits_has_schedules';

    public static function save($params)
    {

        try {
            Database::instance()->begin();

            $data = array();
            if (!isset($params['user_id'])) {
                $user = Auth::instance()->get_user();
                $params['user_id'] = $user['id'];
            }

            $data['academicyear_id'] = $params['academicyear_id'];
            $data['course_id'] = $params['course_id'];
            $data['subject_id'] = $params['subject_id'];
            $data['type'] = $params['type'];
            $data['credit'] = $params['credit'];
            $data['hours'] = $params['hours'];

            $id = null;
            $now = date::now();
            $data['updated'] = $now;
            $data['updated_by'] = $params['user_id'];
            if (!@$params['id']) {
                $data['created'] = $now;
                $data['created_by'] = $params['user_id'];
                $inserted = DB::insert(self::CREDITS_TABLE)->values($data)->execute();
                $id = $inserted[0];
            } else {
                DB::update(self::CREDITS_TABLE)->set($data)->where('id', '=', $params['id'])->execute();
                $id = $params['id'];
                DB::delete(self::HAS_SCHEDULES_TABLE)->where('credit_id', '=', $id)->execute();
            }

            if (@$params['schedule_ids']) {
                foreach ($params['schedule_ids'] as $schedule_id) {
                    DB::insert(self::HAS_SCHEDULES_TABLE)
                        ->values(
                            array(
                                'credit_id' => $id,
                                'schedule_id' => $schedule_id
                            )
                        )
                        ->execute();
                }
            }

            Database::instance()->commit();
            return $id;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function load($id)
    {
        $data = DB::select(
            'credits.*',
            array('courses.title', 'course')
        )
            ->from(array(self::CREDITS_TABLE, 'credits'))
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('credits.course_id', '=', 'courses.id')
            ->where('credits.id', '=', $id)
            ->execute()
            ->current();
        if ($data) {
            $data['schedules'] = DB::select(
                'has_schedules.schedule_id',
                array('schedules.name', 'schedule')
            )
                ->from(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'))
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->where('has_schedules.credit_id', '=', $id)
                ->execute()
                ->as_array();

        }
        return $data;
    }

    public static function delete($id)
    {
        $deleted = DB::delete(self::CREDITS_TABLE)
            ->where('id', '=', $id)
            ->execute();
        return $deleted;
    }

    public static function search($params = array())
    {
        $searchq = DB::select(
            DB::expr("SQL_CALC_FOUND_ROWS credits.*"),
            array('courses.title', 'course'),
            array('subjects.name', 'subject'),
            array('academicyears.title', 'academicyear')
        )
            ->from(array(self::CREDITS_TABLE, 'credits'))
            ->distinct('credits.id')
            ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'inner')
                ->on('credits.id', '=', 'has_schedules.credit_id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                ->on('has_schedules.schedule_id', '=', 'schedules.id')
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                ->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                ->on('credits.course_id', '=', 'courses.id')
            ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                ->on('credits.subject_id', '=', 'subjects.id')
            ->join(array(Model_AcademicYear::TABLE_ACADEMICYEARS, 'academicyears'), 'left')
                ->on('credits.academicyear_id', '=', 'academicyears.id')
            ->where('credits.deleted', '=', 0)
            ->and_where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);

        if (@$params['subject_id']) {
            $searchq->and_where_open();
            if (is_array($params['subject_id'])) {
                //$searchq->or_where('courses.subject_id', 'in', $params['subject_id']);
                //$searchq->or_where('schedules.subject_id', 'in', $params['subject_id']);
                $searchq->or_where('credits.subject_id', 'in', $params['subject_id']);
            } else {
                //$searchq->or_where('courses.subject_id', '=', $params['subject_id']);
                //$searchq->or_where('schedules.subject_id', '=', $params['subject_id']);
                $searchq->or_where('credits.subject_id', '=', $params['subject_id']);
            }
            $searchq->and_where_close();
        }

        if (@$params['course_id']) {
            if (is_array($params['course_id'])) {
                $searchq->and_where('credits.course_id', 'in', $params['course_id']);
            } else {
                $searchq->and_where('credits.course_id', '=', $params['course_id']);
            }
        }

        if (@$params['trainer_id']) {
            if (is_array($params['trainer_id'])) {
                $searchq->and_where('schedules.trainer_id', 'in', $params['trainer_id']);
            } else {
                $searchq->and_where('schedules.trainer_id', '=', $params['trainer_id']);
            }
        }

        if (@$params['schedule_ids']) {
            $searchq->and_where('has_schedules.schedule_id', 'in', $params['schedule_ids']);
        }

        if (@$params['type']) {
            if (is_array($params['type'])) {
                $searchq->and_where('credits.type', 'in', $params['type']);
            } else {
                $searchq->and_where('credits.type', '=', $params['type']);
            }
        }

        if (@$params['date']) {
            $searchq->and_where('timeslots.datetime_start', '>=', $params['date'])
                ->and_where('timeslots.datetime_end', '<=', $params['date'] . ' 23:59:59');
        }

        if (@$params['before']) {
            $searchq->and_where('timeslots.datetime_end', '<=', $params['before'] . ' 23:59:59');
        }

        if (@$params['after']) {
            $searchq->and_where('timeslots.datetime_start', '>=', $params['after'] . ' 00:00:00');
        }

        if (@$params['keyword']) {
            $searchq->and_where_open()
                ->or_where('courses.title', 'like', '%' . $params['keyword'] . '%')
                ->or_where('schedules.name', 'like', '%' . $params['keyword'] . '%')
                ->and_where_close();
        }

        if (@$params['offset']) {
            $searchq->offset($params['offset']);
            if (@$params['limit']) {
                $searchq->limit($params['limit']);
            }
        }

        $credits = $searchq->execute()->as_array();
        return $credits;
    }

    public static function datatable($params)
    {
        $data = self::search($params);
        $result = array();
        $result['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total');
        $result['iTotalRecords']        = count($data);
        $result['aaData']               = array();
        foreach ($data as $row) {
            $trow = array();
            $trow[] = $row['academicyear'];
            $trow[] = $row['course'];
            $trow[] = $row['subject'];
            $trow[] = $row['type'];
            $trow[] = $row['credit'];
            $trow[] = $row['hours'];
            $trow[] = '<a class="btn view" data-id="' . $row['id'] . '">' . __('View') . '</a>';
            $result['aaData'][] = $trow;
        }
        return $result;
    }

    public static function trainer_totals($params = array())
    {
        $searchq = DB::select(
            'has_schedules.schedule_id'
        )
            ->from(array(self::CREDITS_TABLE, 'credits'))
            ->distinct('has_schedules.schedule_id')
                ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'inner')
                    ->on('credits.id', '=', 'has_schedules.credit_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('schedules.id', '=', 'timeslots.schedule_id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('credits.course_id', '=', 'courses.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'inner')
                    ->on('schedules.trainer_id', '=', 'trainers.id')
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                    ->on('credits.subject_id', '=', 'subjects.id')
                ->join(array(Model_AcademicYear::TABLE_ACADEMICYEARS, 'academicyears'), 'left')
                    ->on('credits.academicyear_id', '=', 'academicyears.id')
            ->where('credits.deleted', '=', 0)
            ->and_where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);

        if (@$params['subject_id']) {
            $searchq->and_where_open();
            if (is_array($params['subject_id'])) {
                //$searchq->or_where('courses.subject_id', 'in', $params['subject_id']);
                //$searchq->or_where('schedules.subject_id', 'in', $params['subject_id']);
                $searchq->or_where('credits.subject_id', 'in', $params['subject_id']);
            } else {
                //$searchq->or_where('courses.subject_id', '=', $params['subject_id']);
                //$searchq->or_where('schedules.subject_id', '=', $params['subject_id']);
                $searchq->or_where('credits.subject_id', '=', $params['subject_id']);
            }
            $searchq->and_where_close();
        }

        if (@$params['course_id']) {
            if (is_array($params['course_id'])) {
                $searchq->and_where('credits.course_id', 'in', $params['course_id']);
            } else {
                $searchq->and_where('credits.course_id', '=', $params['course_id']);
            }
        }

        if (@$params['schedule_ids']) {
            $searchq->and_where('has_schedules.schedule_id', 'in', $params['schedule_ids']);
        }

        if (@$params['type']) {
            if (is_array($params['type'])) {
                $searchq->and_where('credits.type', 'in', $params['type']);
            } else {
                $searchq->and_where('credits.type', '=', $params['type']);
            }
        }

        if (@$params['date']) {
            $searchq->and_where('timeslots.datetime_start', '>=', $params['date'])
                ->and_where('timeslots.datetime_end', '<=', $params['date'] . ' 23:59:59');
        }

        if (@$params['before']) {
            $searchq->and_where('timeslots.datetime_end', '<=', $params['before'] . ' 23:59:59');
        }

        if (@$params['after']) {
            $searchq->and_where('timeslots.datetime_start', '>=', $params['after'] . ' 00:00:00');
        }

        if (@$params['trainer_id']) {
            if (is_array($params['trainer_id'])) {
                $searchq->and_where('schedules.trainer_id', 'in', $params['trainer_id']);
            } else {
                $searchq->and_where('schedules.trainer_id', '=', $params['trainer_id']);
            }
        }

        $totalsq = DB::select(
            'schedules.trainer_id',
            'trainers.first_name',
            'trainers.last_name',
            DB::expr("SUM(credits.credit) as total_credit"),
            DB::expr("SUM(credits.hours) as total_hours")
        )
            ->from(array(self::CREDITS_TABLE, 'credits'))
            ->distinct('has_schedules.schedule_id')
                ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'inner')
                    ->on('credits.id', '=', 'has_schedules.credit_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->join(array($searchq, 'schedules_filterered'), 'inner')
                    ->on('schedules.id', '=', 'schedules_filterered.schedule_id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('credits.course_id', '=', 'courses.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'inner')
                    ->on('schedules.trainer_id', '=', 'trainers.id')
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                    ->on('credits.subject_id', '=', 'subjects.id')
                ->join(array(Model_AcademicYear::TABLE_ACADEMICYEARS, 'academicyears'), 'left')
                    ->on('credits.academicyear_id', '=', 'academicyears.id')
            ->where('credits.deleted', '=', 0)
            ->and_where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->group_by('schedules.trainer_id');

        $totals = $totalsq->execute()->as_array();

        return $totals;
    }

    public static function get_calendar($params = array())
    {
        $calendarq = DB::select(
            'credits.id',
            'credits.credit',
            'credits.hours',
            'has_schedules.schedule_id',
            'schedules.trainer_id',
            'trainers.first_name',
            'trainers.last_name',
            array('courses.title', 'course'),
            array('schedules.name', 'schedule'),
            array('timeslots.datetime_start', 'start'),
            array('timeslots.datetime_end', 'end')
        )
            ->from(array(self::CREDITS_TABLE, 'credits'))
                ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'inner')
                    ->on('credits.id', '=', 'has_schedules.credit_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('schedules.id', '=', 'timeslots.schedule_id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('credits.course_id', '=', 'courses.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'inner')
                    ->on('schedules.trainer_id', '=', 'trainers.id')
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                    ->on('credits.subject_id', '=', 'subjects.id')
                ->join(array(Model_AcademicYear::TABLE_ACADEMICYEARS, 'academicyears'), 'left')
                    ->on('credits.academicyear_id', '=', 'academicyears.id')
                ->where('credits.deleted', '=', 0)
                ->and_where('courses.deleted', '=', 0)
                ->and_where('schedules.delete', '=', 0)
                ->and_where('timeslots.delete', '=', 0);

        if (@$params['subject_id']) {
            $calendarq->and_where_open();
            if (is_array($params['subject_id'])) {
                $calendarq->or_where('courses.subject_id', 'in', $params['subject_id']);
                $calendarq->or_where('schedules.subject_id', 'in', $params['subject_id']);
                $calendarq->or_where('credits.subject_id', 'in', $params['subject_id']);
            } else {
                $calendarq->or_where('courses.subject_id', '=', $params['subject_id']);
                $calendarq->or_where('schedules.subject_id', '=', $params['subject_id']);
                $calendarq->or_where('credits.subject_id', '=', $params['subject_id']);
            }
            $calendarq->and_where_close();
        }

        if (@$params['course_id']) {
            if (is_array($params['course_id'])) {
                $calendarq->and_where('credits.course_id', 'in', $params['course_id']);
            } else {
                $calendarq->and_where('credits.course_id', '=', $params['course_id']);
            }
        }

        if (@$params['schedule_ids']) {
            $calendarq->and_where('has_schedules.schedule_id', 'in', $params['schedule_ids']);
        }

        if (@$params['type']) {
            if (is_array($params['type'])) {
                $calendarq->and_where('credits.type', 'in', $params['type']);
            } else {
                $calendarq->and_where('credits.type', '=', $params['type']);
            }
        }

        if (@$params['date']) {
            $calendarq->and_where('timeslots.datetime_start', '>=', $params['date'])
                ->and_where('timeslots.datetime_end', '<=', $params['date'] . ' 23:59:59');
        }

        if (@$params['before']) {
            $calendarq->and_where('timeslots.datetime_end', '<=', $params['before'] . ' 23:59:59');
        }

        if (@$params['after']) {
            $calendarq->and_where('timeslots.datetime_start', '>=', $params['after'] . ' 00:00:00');
        }

        if (@$params['trainer_id']) {
            if (is_array($params['trainer_id'])) {
                $calendarq->and_where('schedules.trainer_id', 'in', $params['trainer_id']);
            } else {
                $calendarq->and_where('schedules.trainer_id', '=', $params['trainer_id']);
            }
        }

        $calendar = $calendarq->execute()->as_array();
        foreach ($calendar as $i => $day) {
            $calendar[$i]['title'] = $day['first_name'] . ' ' . $day['last_name'] . ' - ' . $day['schedule'];
        }
        return $calendar;
    }

    public static function get_calendar_totals($params = array())
    {
        $calendarq = DB::select(
            DB::expr("SUM(credits.credit) as credit_total"),
            DB::expr("SUM(credits.hours) as hours_total"),
            DB::expr("DATE_FORMAT(timeslots.datetime_start, '%Y-%m-%d') as start")
        )
            ->from(array(self::CREDITS_TABLE, 'credits'))
            ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'inner')
            ->on('credits.id', '=', 'has_schedules.credit_id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
            ->on('has_schedules.schedule_id', '=', 'schedules.id')
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
            ->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
            ->on('credits.course_id', '=', 'courses.id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'inner')
            ->on('schedules.trainer_id', '=', 'trainers.id')
            ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
            ->on('credits.subject_id', '=', 'subjects.id')
            ->join(array(Model_AcademicYear::TABLE_ACADEMICYEARS, 'academicyears'), 'left')
            ->on('credits.academicyear_id', '=', 'academicyears.id')
            ->where('credits.deleted', '=', 0)
            ->and_where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);

        if (@$params['subject_id']) {
            $calendarq->and_where_open();
            if (is_array($params['subject_id'])) {
                $calendarq->or_where('courses.subject_id', 'in', $params['subject_id']);
                $calendarq->or_where('schedules.subject_id', 'in', $params['subject_id']);
                $calendarq->or_where('credits.subject_id', 'in', $params['subject_id']);
            } else {
                $calendarq->or_where('courses.subject_id', '=', $params['subject_id']);
                $calendarq->or_where('schedules.subject_id', '=', $params['subject_id']);
                $calendarq->or_where('credits.subject_id', '=', $params['subject_id']);
            }
            $calendarq->and_where_close();
        }

        if (@$params['course_id']) {
            if (is_array($params['course_id'])) {
                $calendarq->and_where('credits.course_id', 'in', $params['course_id']);
            } else {
                $calendarq->and_where('credits.course_id', '=', $params['course_id']);
            }
        }

        if (@$params['schedule_ids']) {
            $calendarq->and_where('has_schedules.schedule_id', 'in', $params['schedule_ids']);
        }

        if (@$params['type']) {
            if (is_array($params['type'])) {
                $calendarq->and_where('credits.type', 'in', $params['type']);
            } else {
                $calendarq->and_where('credits.type', '=', $params['type']);
            }
        }

        if (@$params['date']) {
            $calendarq->and_where('timeslots.datetime_start', '>=', $params['date'])
                ->and_where('timeslots.datetime_end', '<=', $params['date'] . ' 23:59:59');
        }

        if (@$params['before']) {
            $calendarq->and_where('timeslots.datetime_end', '<=', $params['before'] . ' 23:59:59');
        }

        if (@$params['after']) {
            $calendarq->and_where('timeslots.datetime_start', '>=', $params['after'] . ' 00:00:00');
        }

        if (@$params['trainer_id']) {
            if (is_array($params['trainer_id'])) {
                $calendarq->and_where('schedules.trainer_id', 'in', $params['trainer_id']);
            } else {
                $calendarq->and_where('schedules.trainer_id', '=', $params['trainer_id']);
            }
        }

        $calendarq->group_by('start');
        $calendar = $calendarq->execute()->as_array();

        foreach ($calendar as $i => $dt) {
            if (@$params['unit'] == 'hours') {
                $calendar[$i]['title'] = $dt['hours_total'];
            } else {
                $calendar[$i]['title'] = $dt['credit_total'];
            }
        }
        return $calendar;
    }

    public static function stats($params = array())
    {
        $stats = array(
            'target' => 0,
            'to_schedule' => 0,
            'planned' => 0,
            'completed' => 0,
            'pending' => 0
        );

        $targetq = DB::select(
            "credits.id",
            DB::expr("count(*) as timeslot_count")
        )
            ->distinct("credits.id")
            ->from(array(self::CREDITS_TABLE, 'credits'))
                ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'inner')
                    ->on('credits.id', '=', 'has_schedules.credit_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('schedules.id', '=', 'timeslots.schedule_id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('credits.course_id', '=', 'courses.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'left')
                    ->on('schedules.trainer_id', '=', 'trainers.id')
                ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')
                    ->on('credits.subject_id', '=', 'subjects.id')
                ->join(array(Model_AcademicYear::TABLE_ACADEMICYEARS, 'academicyears'), 'left')
                    ->on('credits.academicyear_id', '=', 'academicyears.id')
            ->where('credits.deleted', '=', 0)
            ->and_where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);

        if (@$params['subject_id']) {
            $targetq->and_where_open();
            if (is_array($params['subject_id'])) {
                $targetq->or_where('courses.subject_id', 'in', $params['subject_id']);
                $targetq->or_where('schedules.subject_id', 'in', $params['subject_id']);
                $targetq->or_where('credits.subject_id', 'in', $params['subject_id']);
            } else {
                $targetq->or_where('courses.subject_id', '=', $params['subject_id']);
                $targetq->or_where('schedules.subject_id', '=', $params['subject_id']);
                $targetq->or_where('credits.subject_id', '=', $params['subject_id']);
            }
            $targetq->and_where_close();
        }

        if (@$params['course_id']) {
            if (is_array($params['course_id'])) {
                $targetq->and_where('credits.course_id', 'in', $params['course_id']);
            } else {
                $targetq->and_where('credits.course_id', '=', $params['course_id']);
            }
        }

        if (@$params['schedule_ids']) {
            $targetq->and_where('has_schedules.schedule_id', 'in', $params['schedule_ids']);
        }

        if (@$params['type']) {
            if (is_array($params['type'])) {
                $targetq->and_where('credits.type', 'in', $params['type']);
            } else {
                $targetq->and_where('credits.type', '=', $params['type']);
            }
        }

        if (@$params['date']) {
            $targetq->and_where('timeslots.datetime_start', '>=', $params['date'])
                ->and_where('timeslots.datetime_end', '<=', $params['date'] . ' 23:59:59');
        }

        if (@$params['before']) {
            $targetq->and_where('timeslots.datetime_end', '<=', $params['before'] . ' 23:59:59');
        }

        if (@$params['after']) {
            $targetq->and_where('timeslots.datetime_start', '>=', $params['after'] . ' 00:00:00');
        }

        if (@$params['trainer_id']) {
            if (is_array($params['trainer_id'])) {
                $targetq->and_where('schedules.trainer_id', 'in', $params['trainer_id']);
            } else {
                $targetq->and_where('schedules.trainer_id', '=', $params['trainer_id']);
            }
        }
        $targetq->group_by('credits.id');

        $stats['target'] = DB::select(
            DB::expr(
                @$params['unit'] == 'credit' ?
                    "SUM(credits.credit * filter.timeslot_count) as target"
                    :
                    "SUM(credits.hours * filter.timeslot_count) as target"
            )
        )
            ->from(array(self::CREDITS_TABLE, 'credits'))
                ->join(array($targetq, 'filter'), 'inner')->on('credits.id', '=', 'filter.id')
                ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'inner')
                    ->on('credits.id', '=', 'has_schedules.credit_id')
            ->execute()
            ->get('target');

        $stats['to_schedule'] = DB::select(DB::expr("count(*) as cnt"))
            ->from(array(Model_Courses::TABLE_COURSES, 'courses'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                    ->on('courses.id', '=', 'schedules.course_id')
                    ->on('schedules.delete', '=', DB::expr(0))
                ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'left')
                    ->on('schedules.id', '=', 'timeslots.schedule_id')
                    ->on('timeslots.delete', '=', DB::expr(0))
            ->where('courses.deleted', '=', 0)
            ->and_where('timeslots.id', 'is', null)
            ->execute()
            ->get('cnt');

        $stats['planned'] = (int)DB::select(DB::expr("count(*) as cnt"))
            ->from(array(Model_Courses::TABLE_COURSES, 'courses'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('courses.id', '=', 'schedules.course_id')
                    ->on('schedules.delete', '=', DB::expr(0))
                ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('schedules.id', '=', 'timeslots.schedule_id')
                    ->on('timeslots.delete', '=', DB::expr(0))
            ->where('courses.deleted', '=', 0)
            ->and_where('schedules.start_date', '>', date::now())
            ->group_by('schedules.id')
            ->execute()
            ->get('cnt');

        $stats['completed'] = (int)DB::select(DB::expr("count(*) as cnt"))
            ->from(array(Model_Courses::TABLE_COURSES, 'courses'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('courses.id', '=', 'schedules.course_id')
                    ->on('schedules.delete', '=', DB::expr(0))
                ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('schedules.id', '=', 'timeslots.schedule_id')
                    ->on('timeslots.delete', '=', DB::expr(0))
            ->where('courses.deleted', '=', 0)
            ->and_where('schedules.end_date', '<=', date::now())
            ->group_by('schedules.id')
            ->execute()
            ->get('cnt');

        $stats['pending'] = (int)DB::select(DB::expr("count(*) as cnt"))
            ->from(array(Model_Courses::TABLE_COURSES, 'courses'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('courses.id', '=', 'schedules.course_id')
                    ->on('schedules.delete', '=', DB::expr(0))
                ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('schedules.id', '=', 'timeslots.schedule_id')
                    ->on('timeslots.delete', '=', DB::expr(0))
            ->where('courses.deleted', '=', 0)
            ->and_where('schedules.start_date', '<=', date::now())
            ->and_where('schedules.end_date', '>', date::now())
            ->group_by('schedules.id')
            ->execute()
            ->get('cnt');
        return $stats;
    }
}