<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Frontend_Notifications extends Controller
{
    public function action_test()
    {
        $ok = FALSE;
        $id = Model_Notifications::get_event_id('contact-form');

        if ($id)
        {
            $event = new Model_Notifications($id);
            $ok    = $event->send($_POST['email-body']);
        }

        $this->request->redirect($_POST['redirect-to'].'?status='.($ok ? 1 : 0));
    }
}