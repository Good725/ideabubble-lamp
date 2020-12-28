<?php

class Model_Mytime extends Model
{
    const MYTIME_TABLE = 'plugin_contacts3_mytime';
    const MYTIME_SUBJECTS_TABLE = 'plugin_contacts3_mytime_subjects';

    public static function save($id, $contact_id, $availability, $subjects, $description, $start_date, $start_time, $end_date, $end_time, $color, $days = array(), $user = null)
    {
        if ($user == null) {
            $user = Auth::instance()->get_user();
        }
        $data = array(
            'contact_id' => $contact_id,
            'availability' => $availability,
            'color' => $color,
            'description' => $description,
            'start_date' => $start_date,
            'start_time' => $start_time,
            'end_date' => $end_date,
            'end_time' => $end_time,
            'days' => implode(',', $days),
            'updated_by' => $user['id'],
            'updated' => date::now(),
            'deleted' => 0
        );

        if (is_numeric($id)) {
            DB::update(self::MYTIME_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        } else {
            $data['created_by'] = $user['id'];
            $data['created'] = date::now();
            $inserted = DB::insert(self::MYTIME_TABLE)
                ->values($data)
                ->execute();
            $id = $inserted[0];
        }

        if ($subjects) {
            DB::update(self::MYTIME_SUBJECTS_TABLE)
                ->set(array('deleted' => 1))
                ->where('mytime_id', '=', $id)
                ->and_where('subject_id', 'not in', $subjects)
                ->execute();
            foreach ($subjects as $subject_id) {
                $exists = DB::select('*')
                    ->from(self::MYTIME_SUBJECTS_TABLE)
                    ->where('mytime_id', '=', $id)
                    ->and_where('subject_id', '=', $subject_id)
                    ->execute()
                    ->as_array();
                if (!$exists) {
                    DB::insert(self::MYTIME_SUBJECTS_TABLE)
                        ->values(array('mytime_id' => $id, 'subject_id' => $subject_id))
                        ->execute();
                }
            }
        } else {
            DB::update(self::MYTIME_SUBJECTS_TABLE)
                ->set(array('deleted' => 1))
                ->where('mytime_id', '=', $id)
                ->execute();
        }

        return $id;
    }

    public static function delete($id, $user = null)
    {
        if ($user == null) {
            $user = Auth::instance()->get_user();
        }
        $data = array(
            'updated_by' => $user['id'],
            'updated' => date::now(),
            'deleted' => 1
        );

        $updated = DB::update(self::MYTIME_TABLE)
            ->set($data)
            ->where('id', (is_array($id) ? 'in' : '='), $id)
            ->execute();
        return $updated;
    }

    public static function search($params = array())
    {
        $select = DB::select('mytime.*', 'contacts.first_name')
            ->from(array(self::MYTIME_TABLE, 'mytime'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('mytime.contact_id', '=', 'contacts.id')
            ->where('mytime.deleted', '=', 0);

        if (@$params['contact_id']) {
            $select->and_where('mytime.contact_id', '=', $params['contact_id']);
        }
        if (@$params['contact_ids']) {
            $select->and_where('mytime.contact_id', 'in', $params['contact_ids']);
        }
        if (@$params['before']) {
            $select->and_where('mytime.start_date', '<=', $params['before']);
        }
        if (@$params['after']) {
            $select->and_where('mytime.end_date', '>=', $params['after']);
        }

        $select->order_by('mytime.start_date')->order_by('mytime.start_time');

        $result = $select->execute()->as_array();

        foreach ($result as $i => $mytime) {
            $result[$i]['days'] = $result[$i]['days'] ? explode(',', $result[$i]['days']) : array();
            $result[$i]['subjects'] = DB::select('subjects.*', 'has.subject_id')
                ->from(array(self::MYTIME_SUBJECTS_TABLE, 'has'))
                    ->join(array('plugin_courses_subjects', 'subjects'), 'inner')->on('has.subject_id', '=', 'subjects.id')
                ->where('mytime_id', '=', $mytime['id'])
                ->and_where('has.deleted', '=', 0)
                ->execute()
                ->as_array();
            if (count($result[$i]['subjects'])) {
                $result[$i]['description'] = array();
                foreach ($result[$i]['subjects'] as $subject) {
                    $result[$i]['description'][] = $subject['name'];
                }
                $result[$i]['description'] = implode(', ', $result[$i]['description']);
            }
        }
        return $result;
    }

    public static function get_timeslots_one($mytime)
    {
        if (is_numeric($mytime)) {
            $mytime = DB::select('mytime.*', 'contacts.first_name')
                ->from(array(self::MYTIME_TABLE, 'mytime'))
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                        ->on('mytime.contact_id', '=', 'contacts.id')
                    ->where('mytime.id', '=', $mytime)->execute()->current();
            if ($mytime['days']) {
                $mytime['days'] = explode(',', $mytime['days']);
            }
        }
        if ($mytime == null) {
            return array();
        }

        $result = array();
        if ($mytime['start_date'] == $mytime['end_date']) { // just one entry
            $time = strtotime($mytime['start_date'] . ' ' . $mytime['start_time']);
            $mytime['mytime_id'] = $mytime['id'];
            $mytime['title'] = $mytime['description'];
            $mytime['start'] = date('Y-m-d H:i:s', $time);
            $mytime['end'] = date('Y-m-d', $time) . ' ' . $mytime['end_time'];
            $result[] = $mytime;
        } else {
            $time = strtotime($mytime['start_date'] . ' ' . $mytime['start_time']);
            $end_datetime = strtotime($mytime['end_date'] . ' ' . $mytime['end_time']);
            while ($time < $end_datetime) {
                $add = true;
                if ($mytime['days']) {
                    $weekday = date('l', $time);
                    if (!in_array($weekday, $mytime['days'])) {
                        $add = false;
                    }
                }
                if ($add) {
                    $row = $mytime;
                    $row['mytime_id'] = $mytime['id'];
                    $row['title'] = $mytime['description'];
                    $row['start'] = date('Y-m-d H:i:s', $time);
                    $row['end'] = date('Y-m-d', $time) . ' ' . $mytime['end_time'];
                    $result[] = $row;
                }
                $time = strtotime("+1 day", $time);
            }
        }

        return $result;
    }

    public static function get_all_timeslots($rows)
    {
        $result = array();
        foreach ($rows as $row) {
            $timeslots = self::get_timeslots_one($row);
            foreach ($timeslots as $timeslot) {
                $result[] = $timeslot;
            }
        }
        return $result;
    }

    public static function check_conflicting_entries($contact_id, $start_date, $start_time, $end_date, $end_time, $days = array(), $mytime_id = null)
    {
        $mytimes = self::search(array('contact_id' => $contact_id));
        $all_timeslots = self::get_all_timeslots($mytimes);
        $booked_items = Model_KES_Bookings::get_booking_items_family($contact_id);
        $conflicts = array();
        foreach ($all_timeslots as $timeslot) {
            if ($timeslot['mytime_id'] == $mytime_id) {
                continue;
            }

            $tstart = strtotime($timeslot['start_date'] . ' ' . $timeslot['start_time']);
            $tend = strtotime($timeslot['start_date'] . ' ' . $timeslot['end_time']);

            $time = strtotime($start_date . ' ' . $start_time);
            $end_datetime = strtotime($end_date . ' ' . $end_time);
            while ($time < $end_datetime) {
                if (count($days) > 0) {
                    $weekday = date('l', $time);
                    if (!in_array($weekday, $days)) {
                        $time = strtotime("+1 day", $time);
                        continue;
                    }
                }
                if ($time >= $tstart && $time <= $tend) {
                    $conflicts[] = $timeslot;
                }
                $time = strtotime("+1 day", $time);
            }

        }
        foreach ($booked_items as $timeslot) {

            $tstart = strtotime($timeslot['datetime_start']);
            $tend = strtotime($timeslot['datetime_end']);

            $time = strtotime($start_date . ' ' . $start_time);
            $end_datetime = strtotime($end_date . ' ' . $end_time);
            while ($time < $end_datetime) {
                if (count($days) > 0) {
                    $weekday = date('l', $time);
                    if (!in_array($weekday, $days)) {
                        $time = strtotime("+1 day", $time);
                        continue;
                    }
                }
                if ($time >= $tstart && $time <= $tend) {
                    $timeslot['start'] = date('h:ia', strtotime($timeslot['datetime_start']));
                    $conflicts[] = $timeslot;
                }
                $time = strtotime("+1 day", $time);
            }

        }
        return $conflicts;
    }
}