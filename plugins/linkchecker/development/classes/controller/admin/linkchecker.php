<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Admin_Linkchecker extends Controller_Cms
{
    function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array(
            'Link Checker' => array(
                array(
                    'name' => 'Check',
                    'link' => 'admin/linkchecker'
                ),
                array(
                    'name' => 'Internal Urls',
                    'link' => 'admin/linkchecker/internalurls'
                )
            )
        );
    }


    public function action_index()
    {
        $post = $this->request->post();
        if (isset($post['replace'])) {
            Model_Linkchecker::replace($post['from'], $post['to']);
            $this->request->redirect('/admin/linkchecker');
        }
        $links = Model_Linkchecker::getLinks();
        $this->template->body = View::factory('linkchecker_list');
        $this->template->body->links = $links;
        $this->template->body->host = $_SERVER['HTTP_HOST'];
    }

    public function action_checkurl()
    {
        $url = $this->request->post('url');
        $result = Model_Linkchecker::checkUrl($url);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_internalurls()
    {
        $urls = Model_Linkchecker::getInternalUrls();
        $this->template->body = View::factory('internalurl_list');
        $this->template->body->urls = $urls;
    }
}