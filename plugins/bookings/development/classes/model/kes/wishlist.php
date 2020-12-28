<?php
class Model_KES_Wishlist extends Model
{
    const WISHLIST_TABLE = 'plugin_courses_wishlist';

    public static function add($contact_id, $course_id, $schedule_id, $timeslot_id, $user = null)
    {
        if ($user == null) {
            $user = Auth::instance()->get_user();
        }

        $ucontacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        $ucontact = current($ucontacts);

        $booked_schedule = Model_Schedules::get_one_for_details($schedule_id);
        $message_params = array();
        $message_params['url'] = URL::site('/wishlist.html');
        $message_params['class'] = $booked_schedule['course'] . ' - ' . $booked_schedule['schedule'];

        $contact = new Model_Contacts3($contact_id);
        $message_params['name'] = $contact->get_first_name() . ' ' . $contact->get_last_name();
        $family_id = $contact->get_family_id();
        $parents_to_notify = Model_Contacts3::get_family_members($family_id, array('bookings'));

        if (count($parents_to_notify) > 0) {
            $recipients = array();
            foreach ($parents_to_notify as $parent_to_notify) {
                $recipients[] = array('target_type' => 'CMS_CONTACT3', 'target' => $parent_to_notify['id']);
            }

            $mm = new Model_Messaging();
            $mm->send_template(
                'wishlist-added',
                null,
                date::now(),
                $recipients,
                $message_params
            );

        }

        $existsq = DB::select('*')
            ->from(self::WISHLIST_TABLE)
            ->where('contact_id', '=', $contact_id);
        if ($course_id) {
            $existsq->and_where('course_id', '=', $course_id);
        }
        if ($schedule_id) {
            $existsq->and_where('schedule_id', '=', $schedule_id);
        }
        if ($timeslot_id) {
            $existsq->and_where('timeslot_id', '=', $timeslot_id);
        }
        $exists = $existsq->execute()->current();
        if ($exists) {
            $updateq = DB::update(self::WISHLIST_TABLE)
                ->set(
                    array(
                        'updated' => date::now(),
                        'deleted' => 0,
                        'updated_by' => $user['id']
                    )
                )
                ->where('contact_id', '=', $contact_id);
            if ($course_id) {
                $updateq->and_where('course_id', '=', $course_id);
            }
            if ($schedule_id) {
                $updateq->and_where('schedule_id', '=', $schedule_id);
            }
            if ($timeslot_id) {
                $updateq->and_where_open();
                $updateq->or_where('timeslot_id', '=', $timeslot_id);
                $updateq->or_where('timeslot_id', 'is', null);
                $updateq->and_where_close();
            }
            $updateq->execute();
            $id = $exists['id'];
        } else {
            $inserted = DB::insert(self::WISHLIST_TABLE)
                ->values(array(
                    'contact_id' => $contact_id,
                    'course_id' => $course_id,
                    'schedule_id' => $schedule_id,
                    'timeslot_id' => $timeslot_id ? $timeslot_id : null,
                    'created' => date::now(),
                    'created_by' => $user['id'],
                    'updated' => date::now(),
                    'updated_by' => $user['id'],
                    'deleted' => 0
                ))
                ->execute();
            $id = $inserted[0];
        }

        $tags = array(
            array('tag' => 'WISHLIST', 'description' => 'Wishlist')
        );

        $fields = array();
        $course = Model_Courses::get_course($course_id);
        $schedule = Model_Schedules::get_schedule($schedule_id);
        if (@$course['code']) {
            $tags[] = array(
                'tag' => $course['code'],
                'description' => $course['title']
            );
        }
        /*$tags[] = array(
            'tag' => 'Course' . $course_id,
            'description' => $course['title']
        );
        $tags[] = array(
            'tag' => $course['title'],
            'description' => $course['title']
        );
        $tags[] = array(
            'tag' => 'Schedule' . $schedule_id,
            'description' => $schedule['name']
        );$tags[] = array(
            'tag' => $schedule['name'],
            'description' => $schedule['name']
        );*/


        Model_Automations::run_triggers(
            Model_Bookings_Wishlistaddtrigger::NAME,
            array(
                'contact_id' => $contact_id,
                'tags' => $tags,
                'fields' => $fields
            )
        );

        return $id;
    }

    public static function remove($contact_id, $course_id = null, $schedule_id = null, $timeslot_id = null, $user = null)
    {
        if ($user == null) {
            $user = Auth::instance()->get_user();
        }

        $q = DB::update(self::WISHLIST_TABLE)
            ->set(
                array(
                    'deleted' => 1,
                    'updated_by' => $user['id'],
                    'updated' => date::now()
                )
            )
            ->where('contact_id', '=', $contact_id);
        if ($course_id) {
            $q->and_where('course_id', '=', $course_id);
        }
        if ($schedule_id) {
            $q->and_where('schedule_id', '=', $schedule_id);
        }
        if ($timeslot_id) {
            $q->and_where('timeslot_id', '=', $timeslot_id);
        }
        return $q->execute();
    }

    public static function search($params = array())
    {
        $select = DB::select(
            'schedules.*',
            array('schedules.name', 'schedule'),
            array('courses.title', 'course'),
            'wishlist.*'
        )
            ->from(array(self::WISHLIST_TABLE, 'wishlist'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('wishlist.schedule_id', '=', 'schedules.id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('schedules.course_id', '=', 'courses.id');

        if (@$params['contact_id']) {
            $select->and_where('wishlist.contact_id', '=', $params['contact_id']);
        }
        if (@$params['contact_ids']) {
            $select->and_where('wishlist.contact_id', 'in', $params['contact_ids']);
        }
        if (@$params['schedule_id']) {
            $select->and_where('wishlist.schedule_id', '=', $params['schedule_id']);
        }
        if (@$params['timeslot_id']) {
            $select->and_where('wishlist.timeslot_id', '=', $params['timeslot_id']);
        }
        $select->and_where('wishlist.deleted', '=', 0);

        $wishlist = $select->execute()->as_array();
        return $wishlist;
    }
}
