<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Api_Media extends Controller_Api
{
    public function action_get_url()
    {
        $id = $this->request->query('id');
        $media_folder = Kohana::$config->load('config')->project_media_folder;
        $media = DB::select('medias.*')
            ->from(array(Model_Media::TABLE_MEDIA, 'medias'))
            ->where('id', '=', $id)
            ->execute()
            ->current();
        $item_path = Model_Media::get_path_to_media_item_admin($media_folder, $media['filename'], $media['location']);

        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        $this->response_data['success'] = 1;
        $this->response_data['url'] = $item_path;
    }

    public function action_test()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'text/plain');
        
    }
}
