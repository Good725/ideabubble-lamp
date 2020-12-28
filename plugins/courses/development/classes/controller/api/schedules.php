<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Schedules extends Controller_Api
{
    protected $user = null;

    public function before()
    {
        parent::before();
    }

    public function action_details()
    {
        $get = $this->request->query();

        $schedule = Model_Schedules::get_schedule($get['id']);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['schedule'] = $schedule;
    }

    public function action_list()
    {
        $user = Auth::instance()->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
        $params = array();
        $params['trainer_id'] = @$contact['id'];

        $schedules = Model_Schedules::search($params);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['schedules'] = $schedules;
    }
}