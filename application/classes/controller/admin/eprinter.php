<?php defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Eprinter extends Controller_Head
{
    public function before()
    {
        parent::before();
    }

    public function action_index()
    {
        $eprinters = Model_Eprinter::search();

        $this->template->body = View::factory('content/list_eprinters');
        $this->template->body->eprinters = $eprinters;
    }

    public function action_save()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $id = $post['id'];
        if (is_numeric($id)) {
            Model_Eprinter::update($id, $post);
        } else {
            $email = $post['email'];
            $location = $post['location'];
            $tray = $post['tray'];
            $published = $post['published'];
            Model_Eprinter::add($location, $tray, $email, $published);
        }

        $this->request->redirect('/admin/eprinter');
    }

    public function action_remove()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $id = $post['id'];
        Model_Eprinter::remove($id);
    }
}