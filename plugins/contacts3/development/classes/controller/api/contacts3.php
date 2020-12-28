<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Contacts3 extends Controller_Api
{
    protected $user = null;

    public function before()
    {
        parent::before();

        $user = Auth::instance()->get_user();
        $this->user = Model_Users::get_user($user['id']);
        $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
        $this->contact = new Model_Contacts3(@$contacts[0]['id']);
    }

    public function action_send_feedback()
    {
        $post = $this->request->post();
        $msg = null;
        $tmp = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $tmp = tempnam(Kohana::$cache_dir, "image");
            Kohana::$cache_dir . '/tmp';
            move_uploaded_file($_FILES['image']['tmp_name'], $tmp);
            $msg = array(
                'attachments' => array(
                    array(
                        'path' => $tmp,
                        'name' => $_FILES['image']['name'],
                        'type' => $_FILES['image']['type']
                    )
                )
            );
        }

        $mm = new Model_Messaging();
        $result = $mm->send_template(
            'api-feedback',
            $msg,
            null,
            array(),
            array(
                'subject' => html::entities($post['subject']),
                'message' => html::entities($post['message']),
            )
        );
        if ($tmp) {
            unlink($tmp);
        }

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = 'sent';
        $this->response_data['result'] = $result;
    }

    public function action_send_contact_us()
    {
        $post = $this->request->post();
        $mm = new Model_Messaging();
        $result = $mm->send_template(
            'api-contact',
            null,
            null,
            array(),
            array(
                'name' => html::entities($post['name']),
                'email' => html::entities($post['email']),
                'subject' => html::entities($post['subject']),
                'message' => html::entities($post['message']),
            ),
            null,
            null,
            $post['email']
        );

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = 'sent';
        $this->response_data['result'] = $result;
    }

    public function action_bookings()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();
        $params = array();
        if (@$get['contact_id']) {
            $params['contact_id'] = $get['contact_id'];
        } else {
            if ($this->contact->get_id()) {
                $params['contact_id'] = $this->contact->get_id();
            } else {
                $this->response_data['success'] = 1;
                $this->response_data['msg'] = '';
                $this->response_data['bookings'] = array();
                return;
            }
        }
        if (@$get['after']) {
            $params['after'] = $get['after'];
        }
        if (@$get['before']) {
            $params['before'] = $get['before'];
        }

        $bookings = Model_KES_Bookings::search($params);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['bookings'] = $bookings;
    }

    public function action_timetable()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $auth = Auth::instance();

        $post = $this->request->query();
        $result = array();
        $user = $auth->get_user();
        $search_params = array();

        if ($auth->has_access('courses_rollcall') || $auth->has_access('courses_rollcall_limited')) {
            if (@$post['trainer_id']) {
                $search_params['trainer_id'] = $search_params['trainer_id'];
            } else {
                $search_params['trainer_id'] = $this->contact->get_id();
            }
        }

        if (@$post['contact_ids']) {
            $search_params['contact_ids'] = $post['contact_ids'];
        } else {
            if ($this->contact->get_id()) {
                $search_params['contact_ids'] = array($this->contact->get_id());
            } else {
                $this->response_data['success'] = 1;
                $this->response_data['msg'] = '';
                $this->response_data['timetable'] = array();
                return;
            }
        }

        if (@$post['before']) {
            $search_params['before'] = $post['before'];
        }
        if (@$post['after']) {
            $search_params['after'] = $post['after'];
        }
        if (@$post['schedule_id']) {
            $search_params['schedule_id'] = $post['schedule_id'];
        }
        if (@$post['booking_id']) {
            $search_params['booking_id'] = $post['booking_id'];
        }
        if (@$post['weekdays']) {
            $search_params['weekdays'] = $post['weekdays'];
        }
        if (isset($post['attending'])) {
            $search_params['attending'] = $post['attending'];
        }
        if (@$post['timeslot_status']) {
            $search_params['timeslot_status'] = $post['timeslot_status'];
        }

        $timetable = array();
        if ($auth->has_access('courses_rollcall') || $auth->has_access('courses_rollcall_limited')) {
            $params['availability'] = 1;
            $timeslots = Model_ScheduleEvent::search($search_params);
            foreach ($timeslots as $row) {
                $timetable[] = array(
                    'timeslot_id' => $row['id'],
                    'schedule_id' => $row['schedule_id'],
                    'color' => $row['color'],
                    'title' => $row['schedule'],
                    'location' => $row['location'],
                    'room' => $row['room'],
                    'building' => $row['building'],
                    'start' => $row['datetime_start'],
                    'end' => $row['datetime_end'],
                    'course' => $row['course'],
                    'schedule' => $row['schedule'],
                    'trainer_id' => $row['trainer_id'],
                    'trainer' => $row['trainer'],
                );
            }
        }

        $data = Model_Contacts3::get_timetable_data($search_params);
        foreach ($data as $row) {
            $timetable[] = array(
                'booking_item_id' => $row['booking_item_id'],
                'color' => $row['color'],
                'title' => $row['title'],
                'attending' => $row['attending'],
                'timeslot_status' => $row['timeslot_status'],
                'first_name' => $row['first_name'],
                'location' => $row['location'],
                'room' => $row['room'],
                'building' => $row['building'],
                'outstanding' => $row['outstanding'],
                'start' => $row['start'],
                'end' => $row['end'],
                'course_id' => $row['course_id'],
                'schedule_id' => $row['schedule_id'],
                'trainer_id' => $row['trainer_id'],
                'trainer' => $row['trainer'],
            );
        }
        //$timetable = array($timetable[0], $timetable[1]);

        $mytimes = Model_Mytime::search($search_params);
        $mytimes = Model_Mytime::get_all_timeslots($mytimes);
        foreach ($mytimes as $mytime) {
            $timetable[] = array(
                'mytime_id' => $mytime['mytime_id'],
                'color' => $mytime['color'],
                'title' => $mytime['description'],
                'first_name' => $mytime['first_name'],
                'start' => $mytime['start'],
                'end' => $mytime['end']
            );
        }

        usort(
            $timetable,
            function($t1, $t2) {
                $t1 = strtotime($t1['start']);
                $t2 = strtotime($t2['start']);
                return $t1 - $t2;
            }
        );


        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['timetable'] = $timetable;
    }

    public function action_analytics()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();
        $contact_id = @$get['contact_id'];
        if (!$contact_id) {
            $contact_id = $this->contact->get_id();
        }
        $items = Model_KES_Bookings::get_booking_items_family($contact_id, null, @$get['before'] ?: null, @$get['after'] ?: null);
        $stats = array(
            'category' => array(),
            'subject' => array(),
            'trainer' => array(),
            'course' => array()
        );

        $category = 0;
        $subject = 0;
        $trainer = 0;
        $course = 0;
        $categoryq = 0;
        $subjectq = 0;
        $trainerq = 0;
        $courseq = 0;

        foreach ($items as $item) {
            $time_diff = strtotime($item['datetime_end']) - strtotime($item['datetime_start']);
            if (!isset($stats['category'][$item['category']])) {
                $stats['category'][$item['category']] = array('name' => $item['category'], 'minutes' => 0, 'quantity' => 0, 'schedule_ids' => array());
                $cat = Model_Categories::get_by_name($item['category']);
                $stats['category'][$item['category']]['image'] = $cat['file_id'];
            }
            if (!isset($stats['subject'][$item['subject']])) {
                $stats['subject'][$item['subject']] = array('name' => $item['subject'], 'minutes' => 0, 'quantity' => 0, 'schedule_ids' => array());
            }
            if (!isset($stats['trainer'][$item['trainer']])) {
                $stats['trainer'][$item['trainer']] = array('name' => $item['trainer'], 'minutes' => 0, 'quantity' => 0, 'schedule_ids' => array());
            }
            if (!isset($stats['course'][$item['course']])) {
                $stats['course'][$item['course']] = array('name' => $item['course'], 'minutes' => 0, 'quantity' => 0, 'course_ids' => array(), 'schedule_ids' => array());
            }

            $stats['category'][$item['category']]['schedule_ids'][$item['schedule_id']] = $item['schedule_id'];
            $stats['category'][$item['category']]['quantity'] = count($stats['category'][$item['category']]['schedule_ids']);

            $stats['subject'][$item['subject']]['schedule_ids'][$item['schedule_id']] = $item['schedule_id'];
            $stats['subject'][$item['subject']]['quantity'] = count($stats['subject'][$item['subject']]['schedule_ids']);

            $stats['trainer'][$item['trainer']]['schedule_ids'][$item['schedule_id']] = $item['schedule_id'];
            $stats['trainer'][$item['trainer']]['quantity'] = count($stats['trainer'][$item['trainer']]['schedule_ids']);

            $stats['course'][$item['course']]['course_ids'][$item['course_id']] = $item['course_id'];
            $stats['course'][$item['course']]['schedule_ids'][$item['schedule_id']] = $item['schedule_id'];
            $stats['course'][$item['course']]['quantity'] = count($stats['course'][$item['course']]['course_ids']);

            $category += $time_diff;
            $subject += $time_diff;
            $trainer += $time_diff;
            $course += $time_diff;
            $stats['category'][$item['category']]['minutes'] += $time_diff;
            $stats['subject'][$item['subject']]['minutes'] += $time_diff;
            $stats['trainer'][$item['trainer']]['minutes'] += $time_diff;
            $stats['course'][$item['course']]['minutes'] += $time_diff;
        }

        foreach ($stats['category'] as $i => $seconds) {
            $categoryq += $stats['category'][$i]['quantity'];
            $stats['category'][$i]['minutes'] = ceil($stats['category'][$i]['minutes'] / 60);
        }
        foreach ($stats['subject'] as $i => $seconds) {
            $subjectq += $stats['subject'][$i]['quantity'];
            $stats['subject'][$i]['minutes'] = ceil($stats['subject'][$i]['minutes'] / 60);
        }
        foreach ($stats['trainer'] as $i => $seconds) {
            $trainerq += $stats['trainer'][$i]['quantity'];
            $stats['trainer'][$i]['minutes'] = ceil($stats['trainer'][$i]['minutes'] / 60);
        }
        foreach ($stats['course'] as $i => $seconds) {
            $courseq += $stats['course'][$i]['quantity'];
            $stats['course'][$i]['minutes'] = ceil($stats['course'][$i]['minutes'] / 60);
        }
        $stats['category'] = array('data' => array_values($stats['category']));
        $stats['category']['total_minutes'] = ceil($category / 60);
        $stats['category']['total_quantity'] = $categoryq;
        $stats['subject'] = array('data' => array_values($stats['subject']));
        $stats['subject']['total_minutes'] = ceil($subject / 60);
        $stats['subject']['total_quantity'] = $subjectq;
        $stats['trainer'] = array('data' => array_values($stats['trainer']));
        $stats['trainer']['total_minutes'] = ceil($trainer / 60);
        $stats['trainer']['total_quantity'] = $trainerq;
        $stats['course'] = array('data' => array_values($stats['course']));
        $stats['course']['total_minutes'] = ceil($course / 60);
        $stats['course']['total_quantity'] = $courseq;


        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['stats'] = $stats;
    }

    public function action_trainer_analytics()
    {
        set_time_limit(0);
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();
        $trainer_id = @$get['trainer_id'];
        if (!$trainer_id && !Auth::instance()->has_access('courses_rollcall')) {
            $trainer_id = $this->contact->get_id();
        }
        $params = array();
        if (@$get['before']) {
            $params['before'] = $get['before'];
        }
        if (@$get['after']) {
            $params['after'] = $get['after'];
        }
        if ($trainer_id) {
            $params['trainer_id'] = $trainer_id;
        }
        if (@$get['schedule_id']) {
            $params['schedule_id'] = $get['schedule_id'];
        }
        if (@$get['timeslot_id']) {
            $params['timeslot_id'] = $get['timeslot_id'];
        }

        $stats = Model_KES_Bookings::analytics($params);

        $params = array();
        if ($trainer_id) {
            $params['trainer_id'] = $trainer_id;
        }
        if (@$get['before']) {
            $params['before'] = $get['before'];
        }
        if (@$get['after']) {
            $params['after'] = $get['after'];
        }
        if (@$get['schedule_id']) {
            $params['schedule_id'] = $get['schedule_id'];
        }
        if (@$get['timeslot_id']) {
            $params['timeslot_id'] = $get['timeslot_id'];
        }
        $trainer_bookings = Model_KES_Bookings::search2($params);
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['stats'] = $stats;
        $this->response_data['stats']['bookings']['bookings'] = 0;
        $this->response_data['stats']['bookings']['confirmed'] = 0;
        $this->response_data['stats']['bookings']['paid'] = 0;
        $this->response_data['stats']['bookings']['topics'] = array();
        $this->response_data['stats']['bookings']['receipts'] = 0;
        $this->response_data['stats']['bookings']['rental_due'] = 0;

        $tx = new Model_Kes_Transaction();
        $trainer_schedule_ids = array();
        $booking_schedule_uniq_ids = array();
        foreach ($trainer_bookings as $trainer_booking) {
            if (isset($booking_schedule_uniq_ids[$trainer_booking['booking_id']])) {
                continue;
            }

            if (in_array($trainer_booking['booking_status'], array(2,4,5))) {
                $this->response_data['stats']['bookings']['confirmed'] += 1;
                if ($trainer_booking['payment_type'] == 1) { // prepay
                    $booking_transaction = $tx->get_transaction(null, $trainer_booking['booking_id'], $trainer_booking['schedule_id']);
                    if ($booking_transaction != null) {
                        if ($booking_transaction['outstanding'] == 0) {
                            $this->response_data['stats']['bookings']['paid'] += 1;
                        }
                    }

                    if ($tx->calculate_outstanding_balance($trainer_booking['booking_id']) == 0) {
                        $this->response_data['stats']['bookings']['paid'] += 1;
                    }

                    foreach ($trainer_booking['timeslots'] as $tbtimeslot) {
                        if ($tbtimeslot['topic_id']) {
                            $this->response_data['stats']['bookings']['topics'][$tbtimeslot['topic_id']] = $tbtimeslot['topic_id'];
                        }
                    }
                } else { // payg
                    foreach ($trainer_booking['timeslots'] as $tbtimeslot) {
                        if ($tbtimeslot['topic_id']) {
                            $this->response_data['stats']['bookings']['topics'][$tbtimeslot['topic_id']] = $tbtimeslot['topic_id'];
                        }
                        if ($tbtimeslot['transaction_id']) {
                            $tbtimeslot_transaction = $tx->get_transaction($tbtimeslot['transaction_id']);
                            if ($tbtimeslot_transaction['outstanding'] == 0) {
                                $this->response_data['stats']['bookings']['paid'] += 1;
                            }
                        }
                    }
                }
                if ((float)$trainer_booking['rental_fee'] > 0) {
                    $this->response_data['stats']['bookings']['receipts'] = $stats['transaction']['total'];
                    $this->response_data['stats']['bookings']['rental_due'] += round($stats['transaction']['total'] * ((float)$trainer_booking['rental_fee'] / 100.0), 2);
                } else {
                    $this->response_data['stats']['bookings']['receipts'] = $stats['transaction']['total'];
                    $this->response_data['stats']['bookings']['rental_due'] += 0;
                }
            }
            $trainer_schedule_ids[] = $trainer_booking['schedule_id'];
            $this->response_data['stats']['bookings']['bookings'] += 1;
            $booking_schedule_uniq_ids[$trainer_booking['booking_id']] = true;
        }

        if (count($trainer_schedule_ids)) {
            $stopics = DB::select('*')
                ->distinct('*')
                ->from('plugin_courses_schedules_have_topics')
                ->where('schedule_id', 'in', $trainer_schedule_ids)
                ->execute()
                ->as_array();
            foreach($stopics as $stopic) {
                $this->response_data['stats']['bookings']['topics'][$stopic['topic_id']] = $stopic['topic_id'];
            }
        }
        $this->response_data['stats']['bookings']['topics'] = count($this->response_data['stats']['bookings']['topics']);
    }

    public function action_countries()
    {
        $countries = Model_Country::get_countries();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['countries'] = $countries;
    }

    public function action_nationalities()
    {
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['nationalities'] = Model_Country::$nationalities;
    }

    public function action_notification_types()
    {
        $notification_types = Model_Contacts3::get_notification_types();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['notification_types'] = $notification_types;
    }

    public function action_preference_types()
    {
        $preference_types = Model_Preferences::get_all_preferences();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['preference_types'] = $preference_types;
    }

    public function action_roles()
    {
        $roles = Model_Contacts3::get_all_roles();
        $settings = Model_Contacts3::load_settings();
        foreach ($roles as $r => $role) {
            $roles[$r]['preferences'] = array();
            foreach ($settings as $s => $setting) {
                $roles[$r]['preferences'][] = array(
                    'group' => $setting['group'],
                    'preference' => $setting['preference'],
                    'label' => $setting['label'],
                    'allowed' => $setting['allowed']
                );
            }
        }
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['roles'] = $roles;
    }

    public function action_schools()
    {
        $schools = Model_Providers::get_all_schools();
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['schools'] = $schools;
    }

    public function action_details()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();
        $post = $this->request->post();

        $saved = null;
        $id = @$get['id'];
        if (@$post['id']) {
            $id = $post['id'];
        }
        if (!$id) {
            $id = $this->contact->get_id();
        }

        if (!empty($post)) {
            $contact = new Model_Contacts3($id);
            if (!isset($post['preferences'])) {
                $post['preferences'] = array();
            }
            $contact->load($post);
            $contact->address->load($post);
            $saved = $contact->save();
        }
        $contact = new Model_Contacts3($id);
        $data = $contact->get_instance();
        $address = new Model_Residence($contact->get_residence());
        $data['address'] = $address->get_instance();
        $data['notifications'] = $contact->get_contact_notifications();
        $data['preferences'] = $contact->get_preferences();
        $data['subject_preferences'] = $contact->get_subject_preferences();
        foreach ($data['subject_preferences'] as $si => $sp) {
            $data['subject_preferences'][$si] = array(
                'subject_id' => $sp['subject_id'],
                'level_id' => $sp['level_id']
            );
        }
        $data['course_type_preferences'] = $contact->get_course_type_preferences();
        $data['courses_subject_preferences'] = $contact->get_courses_subject_preferences();
        $linked_user_id = $contact->get_linked_user_id();
        if ($linked_user_id) {
            $linked_user = Model_Users::get_user($linked_user_id);
            $linked_user['avatar'] = Url::get_avatar($linked_user_id);
            $data['user'] = array(
                'id' => $linked_user['id'],
                'avatar' => $linked_user['avatar'],
                'role_id' => $linked_user['role_id'],
                'last_login' => date('Y-m-d H:i:s', $linked_user['last_login'])
            );
        } else {
            $data['user'] = null;
        }

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['data'] = $data;
    }

    public function action_family_members()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $get = $this->request->query();

        $id = @$get['id'];
        if (!$id) {
            $id = $this->contact->get_id();
        }

        $contact = new Model_Contacts3($id);
        $members = $contact->get_family_members($contact->get_family_id());
        $ids = array();
        foreach($members as $member) {
            $ids[] = array(
                'id' => $member['id'],
                'first_name' => $member['first_name'],
                'last_name' => $member['last_name'],
                'has_roles' => $member['has_roles']
            );
        }

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['ids'] = $ids;
    }

    public function action_invite_member()
    {
        if (!$this->user) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $post = $this->request->post();
        $email = $post['email'];
        $name = @$post['name'];

        $result = Model_Contacts3::invite_member($email, $this->contact->get_id(), $name);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['sent'] = $result;
    }

    public function action_next_countdown()
    {
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['title'] = Settings::instance()->get('countdown_title');
        $this->response_data['datetime'] = Settings::instance()->get('countdown_datetime');
    }

    public function action_counties()
    {
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';

        $this->response_data['counties'] = Model_Residence::get_all_counties();
    }
}