<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Rollcall extends Controller_Api
{
    protected $user = null;

    public function before()
    {
        parent::before();
    }

    public function action_schedules()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_rollcall') && !$auth->has_access('courses_rollcall_limited')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $post = $this->request->query();
        $user = $auth->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
        $params = array();
        if (!$auth->has_access('courses_rollcall')) {
            $params['trainer_id'] = @$contact['id'];
        } else {
            if (@$post['trainer_id']) {
                $params['trainer_id'] = $post['trainer_id'];
            }
        }

        if (@$post['before']) {
            $params['before'] = $post['before'] . ' 23:59:59';
        }

        if (@$post['after']) {
            $params['after'] = $post['after'] . ' 00:00:00';
        }

        if (@$post['keyword']) {
            $params['keyword'] = $post['keyword'];
        }

        if (@$post['booked'] == 1) {
            $params['booked'] = 1;
        }

        $schedules = Model_Schedules::search($params);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['schedules'] = $schedules;
    }

    public function action_dates()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_rollcall') && !$auth->has_access('courses_rollcall_limited')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $post = $this->request->query();
        $user = $auth->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
        $params = array();
        if (!$auth->has_access('courses_rollcall')) {
            $params['trainer_id'] = @$contact['id'];
        } else {
            if (@$post['trainer_id']) {
                $params['trainer_id'] = $post['trainer_id'];
            }
        }

        if (@$post['schedule_id']) {
            $params['schedule_id'] = $post['schedule_id'];
        }

        if (@$post['before']) {
            $params['before'] = $post['before'] . ' 23:59:59';
        }

        if (@$post['after']) {
            $params['after'] = $post['after'] . ' 00:00:00';
        }

        if (@$post['date']) {
            $params['before'] = $post['date'] . ' 23:59:59';
            $params['after'] = $post['date'] . ' 00:00:00';
        }

        if (@$post['keyword']) {
            $params['keyword'] = $post['keyword'];
        }

        if (@$post['booked'] == 1) {
            $params['booked'] = 1;
        }

        $timeslots = Model_ScheduleEvent::search($params);
        $dates = array();
        foreach ($timeslots as $timeslot) {
            $date = date('Y-m-d', strtotime($timeslot['datetime_start']));
            $dates[$date] = $date;
        }
        $dates = array_values($dates);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['dates'] = $dates;
    }

    public function action_timeslots()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_rollcall') && !$auth->has_access('courses_rollcall_limited')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $post = $this->request->query();
        $user = $auth->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
        $params = array();
        if (!$auth->has_access('courses_rollcall')) {
            $params['trainer_id'] = @$contact['id'];
        } else {
            if (@$post['trainer_id']) {
                $params['trainer_id'] = $post['trainer_id'];
            }
        }

        if (@$post['schedule_id']) {
            $params['schedule_id'] = $post['schedule_id'];
        }

        if (@$post['before']) {
            $params['before'] = $post['before'] . ' 23:59:59';
        }

        if (@$post['after']) {
            $params['after'] = $post['after'] . ' 00:00:00';
        }

        if (@$post['date']) {
            $params['before'] = $post['date'] . ' 23:59:59';
            $params['after'] = $post['date'] . ' 00:00:00';
        }

        if (@$post['keyword']) {
            $params['keyword'] = $post['keyword'];
        }

        if (@$post['booked'] == 1) {
            $params['booked'] = 1;
        }

        $params['availability'] = 1;
        $timeslots = Model_ScheduleEvent::search($params);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['timeslots'] = $timeslots;
    }

    public function action_students()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_rollcall') && !$auth->has_access('courses_rollcall_limited')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $post = $this->request->query();
        $user = $auth->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
        $params = array();
        if (!$auth->has_access('courses_rollcall')) {
            $params['trainer_id'] = @$contact['id'];
        } else {
            if (@$post['trainer_id']) {
                $params['trainer_id'] = $post['trainer_id'];
            }
        }

        if (@$post['timeslot_id']) {
            $params['timeslot_id'] = $post['timeslot_id'];
        }
        if (@$post['date']) {
            $params['date'] = $post['date'];
        }
        if (@$post['status']) {
            $params['status'] = explode(',', $post['status']);
        }


        if (@$post['keyword']) {
            $params['keyword'] = $post['keyword'];
        }

        $students = Model_KES_Bookings::rollcall_list($params);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['students'] = $students;
    }

    public function action_student_update()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('courses_rollcall') && !$auth->has_access('courses_rollcall_limited')) {
            $this->response_data['success'] = 0;
            $this->response_data['msg'] = 'Access Denied';
            return;
        }

        $post = $this->request->post();
        $user = $auth->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
        $params = array();
        if (!$auth->has_access('courses_rollcall')) {
            $params['trainer_id'] = @$contact['id'];
        } else {
            if (@$post['trainer_id']) {
                $params['trainer_id'] = $post['trainer_id'];
            }
        }

        $booking_id = $post['booking_id'];
        $booking_item_id = $post['booking_item_id'];
        $status = $post['status'];
        $note = @$post['note'];
        $paid = @$post['paid'];
        $arrived = @$post['arrived'] ?: null;
        $left = @$post['left'] ?: null;
        $temporary_absences = @$_POST['temporary_absences'] ? json_encode($_POST['temporary_absences'], JSON_PRETTY_PRINT): null;
        $planned_arrival = @$post['planned_arrival'] ?: null;
        $planned_leave = @$post['planned_leave'] ?: null;

        $bookings = array();
        $booking = array();
        $booking['id'] = $booking_id;
        $booking['note'] = $note;
        $booking['amount'] = $paid;
        $booking['items'] = array(
            array(
                'id' => $booking_item_id,
                'status' => $status,
                'arrived' => $arrived,
                'left' => $left,
                'temporary_absences' => $temporary_absences,
                'planned_arrival' => $planned_arrival,
                'planned_leave' => $planned_leave
            )
        );
        $bookings[] = $booking;
        if (!$auth->has_access('courses_finance')) {
            $update_accounts = false;
        }
        Model_KES_Bookings::rollcallUpdate($bookings, $update_accounts);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
    }
}