<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Notes2  extends Controller_Cms
{
    public function action_search()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json charset=utf-8');
        $params = $this->request->query();
        $notes = Model_Notes::search($params);
        echo json_encode($notes);
    }

    public function action_save()
    {
        $post = $this->request->post();
        $result = Model_Notes::create($post['type'], $post['reference_id'], $post['note']);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json charset=utf-8');
        echo json_encode($result);
    }
}