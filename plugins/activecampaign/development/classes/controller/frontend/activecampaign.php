<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Activecampaign extends Controller
{
    public function action_contact_save()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf-8');
        try {
            $post = $this->request->post();
            if (isset($post['contact'])) {
                Model_Activecampaign::create_from_remote($post['contact']);
            }
        } catch (Exception $exc) {
            throw $exc;
            //echo $exc->getMessage();
        }
    }
}