<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Api_Content extends Controller_Api
{
    public function action_details()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $content = new Model_Content($this->request->query('id'));
        $data  = $content->as_array();
        $data['content'] = str_replace("href='//", "href='" . URL::site(), $data['content']);
        $data['content'] = str_replace("href='/", "href='" . URL::site(), $data['content']);
        $data['content'] = str_replace('href="//', 'href="' . URL::site(), $data['content']);
        $data['content'] = str_replace('href="/', 'href="' . URL::site(), $data['content']);
        $data['content'] = str_replace('src="//', 'src="' . URL::site(), $data['content']);
        $data['content'] = str_replace('src="/', 'src="' . URL::site(), $data['content']);
        $data['type'] = $content->type->name;
        $data['duration_formatted'] = $content->get_duration_formatted();
        $data['children'] = Model_Content::get_children_tree($this->request->query('id'));

        $this->response_data['content'] = $data;
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
    }
}