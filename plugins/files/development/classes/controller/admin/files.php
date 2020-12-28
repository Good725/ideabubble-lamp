<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Admin_Files extends Controller_Cms
{
    function before()
    {
        parent::before();

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array('icon' => 'files', 'name' => 'Files', 'link' => '/admin/files')
        ));
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Files', 'link' => '/admin/files')
        );
        switch($this->request->action())
        {
            case 'index':
            case 'add':
            case 'edit':
            case 'list_directory':
                $this->template->sidebar->tools = '<label for="txtDirectoryName"></label>
                    <input type="text" class="input-medium" id="txtDirectoryName" placeholder="New Directory..."/>
                    <button class="btn btn-default" id="btnCreateDirectory">Create Directory</button>
                    <button class="btn btn-default" id="btnAddFile">Add New File...</button>';
                break;
        }
    }


    /**
     * This is the default action.
     */
    public function action_index()
    {
        $this->action_list_directory();
    }

    /**
     *
     */
    public function action_list_directory()
    {
		if ( ! Auth::instance()->has_access('files_view'))
		{
			IbHelpers::set_message('You need access to the &quot;view files&quot; permission to view this page.', 'warning popup_box');
			$this->request->redirect('/admin');
		}

        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('files').'css/list_directory.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('files').'js/bootbox.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('files').'js/common.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('files').'js/list_directory.js"></script>';

        // get icon
        $results['plugin'] = Model_Plugin::get_plugin_by_name('files');

        // View
        $this->template->body               = View::factory('list_directory', $results);
        $this->template->body->directory_id = $this->request->query('directory_id') !== NULL ? $this->request->query('directory_id') : Model_Files::get_root_directory_id();
    }

    /**
     *
     */
    public function action_add()
    {
        try
        {
			if ( ! Auth::instance()->has_access('files_edit'))
			{
				IbHelpers::set_message('You need access to the &quot;edit files&quot; permission to perform this action', 'warning popup_box');
				$this->request->redirect('/admin');
			}

            // Assets
            $this->template->styles[URL::get_engine_plugin_assets_base('files').'css/add_edit_file.css'] = 'screen';

            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('files').'js/common.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('files').'js/add_edit_file.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';

            // View
            $this->template->body       = View::factory('add_edit_file');
            $this->template->body->data = array('parent_id' => $this->request->query('parent_id'));
        }
        catch (Exception $exception)
        {
            IbHelpers::set_message($exception->getMessage(), 'error popup_box');

            $this->request->redirect('/admin/files');
        }
    }

    public function action_copy_from_media()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $media_id = $this->request->post('media_id');
        $filename = $this->request->post('filename');
        $file_id = Model_Files::copy_from_media($media_id, '/tmp/' . time(), $filename);
        echo json_encode(array('file_id' => $file_id, 'name' => $filename));
    }
    /**
     *
     */
    public function action_edit()
    {
        try
        {
			if ( ! Auth::instance()->has_access('files_view'))
			{
				IbHelpers::set_message('You need access to the &quot;view files&quot; permission to view this page.', 'warning popup_box');
				$this->request->redirect('/admin');
			}

            $this->action_add();

            $this->template->body->data['file_id'  ] = $this->request->query('file_id');
            $this->template->body->data['file_name'] = Model_Files::get_file_name($this->request->query('file_id'));
        }
        catch (Exception $exception)
        {
            IbHelpers::set_message($exception->getMessage(), 'error popup_box');

            $this->request->redirect('/admin/files');
        }
    }

    /**
     *
     */
    public function action_save()
    {
        try
        {
			if ( ! Auth::instance()->has_access('files_edit'))
			{
				IbHelpers::set_message('You need access to the &quot;edit files&quot; permission to perform this action', 'warning popup_box');
				$this->request->redirect('/admin');
			}

            if (is_numeric( $this->request->post('file_id') ))
            {
                Model_Files::update_file($this->request->post('file_id'  ), $this->request->post('file_name'), $_FILES['version_file']['error'] == UPLOAD_ERR_NO_FILE ? NULL : $_FILES['version_file']);

                IbHelpers::set_message(Model_Files_Messages::FILE_UPDATED, 'success popup_box');
            }
            else
            {
                Model_Files::create_file($this->request->post('parent_id'), $this->request->post('file_name'), $_FILES['version_file']['error'] == UPLOAD_ERR_NO_FILE ? NULL : $_FILES['version_file']);

                IbHelpers::set_message(Model_Files_Messages::FILE_CREATED, 'success popup_box');
            }

            $this->request->redirect('/admin/files/list_directory?directory_id='.$this->request->post('parent_id'));
        }
        catch (Exception $exception)
        {
            IbHelpers::set_message($exception->getMessage(), 'error popup_box');

            (is_numeric( $this->request->post('file_id') )) ? $this->action_edit() : $this->action_add();
        }
    }

    /**
     *
     */
    public function action_download_file()
    {
        $file_id = $this->request->query('file_id');
        if (!Auth::instance()->has_access('files_view')) {
            //if no view all permission then check if a file is shared
            if (!Model_Contacts3_Files::is_shared(Auth::instance()->get_contact3()->get_id(), $file_id)) {
                $error_id = Model_Errorlog::save(null, 'SECURITY');
                IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                $this->request->redirect('/admin');
            }
        }
        Model_Files::download_file($file_id);
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_download_version()
    {
        $version_id = $this->request->query('version_id');
        $version = Model_Files::_sql_get_version_details($version_id);
        if (!Auth::instance()->has_access('files_view')) {
            //if no view all permission then check if a file is shared
            if (!Model_Contacts3_Files::is_shared(Auth::instance()->get_contact3()->get_id(), $version['file_id'])) {
                $error_id = Model_Errorlog::save(null, 'SECURITY');
                IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                $this->request->redirect('/admin');
            }
        }
        Model_Files::download_version($version_id);
        $this->auto_render = FALSE;
    }

    //
    // AJAX
    //

    /**
     *
     */
    public function action_ajax_list_directory()
    {
		if (Auth::instance()->has_access('files_view')) {
			$this->response->body(Model_Files::ajax_list_directory($this->request->query('directory_id')));
		}

        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_path_breadcrumbs()
    {
        $this->response->body(Model_Files::ajax_get_path_breadcrumbs($this->request->query('directory_id')));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_create_directory()
    {
		$this->auto_render = FALSE;
		if (Auth::instance()->has_access('files_edit_directory'))
		{
			$this->response->body(Model_Files::ajax_create_directory($this->request->post('parent_id'), $this->request->post('name')));
		}

    }

    /**
     *
     */
    public function action_ajax_remove_directory()
    {
		$this->auto_render = FALSE;
		if (Auth::instance()->has_access('files_edit_directory') AND Auth::instance()->has_access('files_delete'))
		{
       		$this->response->body(Model_Files::ajax_remove_directory($this->request->post('directory_id')));
		}
    }

    /**
     *
     */
    public function action_ajax_remove_file()
    {
		if (Auth::instance()->has_access('files_delete'))
		{
			if(!$this->request->post('file_id')){
				$id = $this->request->query('file_id');
				$this->response->body(Model_Files::ajax_remove_file($id));
				$this->request->redirect($this->request->referrer());
			}
			$this->response->body(Model_Files::ajax_remove_file($this->request->post('file_id')));
			$this->auto_render = FALSE;
		}
    }

    /**
     *
     */
    public function action_ajax_get_versions()
    {
        $this->response->body(Model_Files::ajax_get_versions($this->request->query('file_id')));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_set_active_version()
    {
        $this->response->body(Model_Files::ajax_set_active_version($this->request->post('file_id'), $this->request->post('version_id')));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_remove_version()
    {
        $this->response->body(Model_Files::ajax_remove_version($this->request->post('version_id')));
        $this->auto_render = FALSE;
    }

    public function action_test()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'text/plain');
        
    }
}
