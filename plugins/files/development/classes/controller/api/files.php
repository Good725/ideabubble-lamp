<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Api_Files extends Controller_Api
{
    public function action_download()
    {
        $this->auto_render = false;
        Model_Files::download_file($this->request->query('file_id'));
    }

    public function action_test()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'text/plain');
        
    }
}
