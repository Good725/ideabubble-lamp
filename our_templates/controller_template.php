<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_ extends Controller_Head {

    public function before()
    {
        parent::before();
// submenu items for cms
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array(
            '' => array(
                array(
                    'name' => '' ,
                    'link' => 'admin/'
                ),
            )
        );

        // Set up breadcrumbs
        $this->template->sidebar->breadcrumbs = array(
            array(
                'name' => 'Home',
                'link' => '/admin'
            ),
            array(
                'name' => '',
                'link' => '/admin/'
            )
        );

        switch($this->request->action())
        {
            case '':
                $this->template->sidebar->breadcrumbs[] = array('name' => '', 'link' => '/admin/surveys/');
                $this->template->sidebar->tools = '<a href="/admin//"><button type="button" class="btn" id=""></button></a>';
                break;
        }

        // Default Script
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('') .'js/______.js"></script>';
        // Default Styles
        $this->template->styles    = array(URL::get_engine_plugin_assets_base('').'css/____.css' => 'screen');
    }

    public function action_index()
    {
        $this->template->body = View::factory('');
        $this->template->body->data ;
        $this->template->sidebar->breadcrumbs[] = array('name' =>'','link' =>'');
        $this->template->sidebar->tools = '<a href="/admin/........./add_edit_"><button type="button" class="btn">Add </button></a>';
    }

    public function action_()
    {
        //additional scripts
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base() . 'js/_______.js"></script>';

        //select template to display
        $this->template->body = View::factory('_______list');
    }

    public function action_ajax_get_all_()
    {
        $post = $this->request->post();
        if (isset($post['sEcho']))
            $return['sEcho'] = $post['sEcho'];
        $return['iTotalRecords'] = ORM::factory('')->count_all();
        $sort = 'date_modified';

        // Use the column id's for the search items
        switch ($post['iSortCol_0'])
        {
            case 0: $sort = 'id';
                break;
            case 1: $sort = '';
                break;
        }
        $model = new Model_OurTemplate();
        $return['aaData'] = $model->get_all_($post['iDisplayLength'], $post['iDisplayStart'], $sort, $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = $return['iTotalRecords'];
        echo json_encode($return);
        exit;
    }

    public function action_save_()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            $data = ORM::factory('', $this->request->post('id'));
            $data->values($post);
            $data->set('updated_by', $user['id']);
            $data->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $data->set('created_by', $user['id']);
                $data->set('created_on', date("Y-m-d H:i:s"));
            }
            $data->save();
            IbHelpers::set_message('The ____: ' . $post[''] . ' was ' . is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.', 'success');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/____/index');
            }
            else
            {
               $this->request->redirect('/admin/____/add_edit_ /' . $data->id);
            }
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Error saving ______.', 'error');
            $this->request->redirect('admin/____/index');
        }
    }

    public function action_add_edit_()
    {
        $id = $this->request->param('id');
        $data  = ORM::factory('',$id);
        
        $this->template->body = VIEW::factory('');
        $this->template->body->____ = $data;
    }

    public function action_publish_()
    {
        $data = $this->request->post();
        $result = array('status'=>'error') ;
        $logged_in_user      = Auth::instance()->get_user();
        $item = ORM::factory('', $data['id']);
        $item->set('updated_by',$logged_in_user['id']);
        $item->set('publish',$item->publish == 1 ? 0 : 1 );
        $item->save();
        $answer = ORM::factory('', $data['id']);
        if ($answer)
        {
            $result['status']='success';
        }
        exit(json_encode($result));
    }

    public function action_delete_()
    {
        $data = $this->request->post();
        $result = array('status'=>'error') ;
        $logged_in_user      = Auth::instance()->get_user();
        $item = ORM::factory('', $data['id']);
        $item->set('updated_by',$logged_in_user['id']);
        $item->set('deleted',$item->deleted == 1 ? 0 : 1 );
        $item->save();
        $answer = ORM::factory('', $data['id']);
        if ($answer)
        {
            $result['status']='success';
        }
        exit(json_encode($result));
    }
}