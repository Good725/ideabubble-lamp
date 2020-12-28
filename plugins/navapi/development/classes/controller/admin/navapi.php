<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Navapi extends Controller_Cms
{
    public function action_getbooking()
    {
        session_commit();
        $this->auto_render = false;
        $this->response->headers('content-type', 'application/json; charset=utf-8');
        $id = $this->request->query('id');
        $na = new Model_NAVAPI();
        $booking = $na->get_booking($id);
        echo json_encode($booking);
    }

    public function action_sync_events()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'text/plain');
        $na = new Model_NAVAPI();
        $na->event_sync();
    }

    public function action_events_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'application/json');

        $navapi = new Model_NAVAPI();
        $filters = $this->request->query();
        $results = $navapi->get_for_datatable($filters);
        echo json_encode($results);
    }

    public function action_test()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'text/plain');
        $na = new Model_NAVAPI();
        //$na->auth();
        //$events = $na->event_list();
        //print_r($events);
        //print_r($na->create_booking(134));
        //print_r($na->create_transaction(136));
        $na->create_payment(65);
        print_r($na);
    }
}