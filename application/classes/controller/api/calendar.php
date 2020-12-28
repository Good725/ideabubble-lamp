<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Calendar extends Controller_Api
{

    public function before()
    {
        parent::before();
    }

    public function action_events()
    {
        $events = Model_Calendar_Event::get_all_published_dates();
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['events'] = $events;
    }
}