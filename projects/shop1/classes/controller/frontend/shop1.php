<?php defined('SYSPATH') OR die('No Direct Script Access');

final class Controller_Frontend_Shop1 extends Controller
{
    public function action_render_delivery_date()
    {
        $model = new Model_Shop1();

        $html_options = $model->calculatedeliverydate();

        $this->response->body($html_options);
    }

    public function action_login_html()
    {
        $htlm = View::factory('front_end/paybackloyalty_login')->render();
        $this->response->body($htlm);
    }

    public function action_render_registration_html()
    {
        $htlm = View::factory('front_end/paybackloyalty_registration_form')->render();
        $this->response->body($htlm);
    }

    public function action_render_members_area_html()
    {
        $htlm = View::factory('front_end/paybackloyalty_members_area')->render();
        $this->response->body($htlm);
    }

    public function action_ajax_get_upcoming()
    {
        $this->auto_render = FALSE;
        $o_Event = new Model_Event;
        $events = $o_Event->limit(16)->offset($this->request->post('upcoming_offset'))->find_all_upcoming();
        $data = array();

        foreach($events as $event){
            $data[] = View::factory('templates/tickets/template_views/event_feed_item_upcoming')->set('event', $event)->render();
        }

        echo json_encode($data);
    }
}
/**/