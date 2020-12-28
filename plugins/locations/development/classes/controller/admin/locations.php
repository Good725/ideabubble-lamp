<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Admin_Locations extends Controller_Cms
{
    /**
     * Function to be executed before every action.
     */
    public function before()
    {
        parent::before();

        // Commons
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('locations').'js/common.js"></script>';

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array('icon' => 'location', 'name' => 'Locations', 'link' => '/admin/locations')
        ));
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',      'link' => '/admin'),
            array('name' => 'Locations', 'link' => '/admin/locations')
        );
        switch($this->request->action())
        {
            case 'index':
            case 'add':
            case 'edit':
            case 'locations':
                $this->template->sidebar->tools = '<a href="/admin/locations/add"><button type="button" class="btn">Add Location</button></a>';
                break;
        }
    }

    /**
     * This is the default action.
     */
    public function action_index()
    {
        $this->action_locations();
    }

    /**
     * List.
     */
    public function action_locations()
    {
        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('locations').'css/list_locations.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('locations').'js/list_locations.js"></script>';

        // get icon
        $results['plugin'] = Model_Plugin::get_plugin_by_name('locations');

        // View
        $this->template->body = View::factory('list_locations', $results);
    }

    /**
     * Add.
     */
    public function action_add()
    {
        try
        {
            // Assets
            $this->template->styles[URL::get_engine_assets_base().'css/validation.css'] = 'screen';
			$this->template->styles[URL::get_engine_plugin_assets_base('locations').'css/add_edit_location.css'] = 'screen';
			$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('locations').'js/add_edit_location.js"></script>';
			$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
			$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';

            // View
            $this->template->body        = View::factory('add_edit_location');
            $this->template->body->types = Model_Location::get_all_types();
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/locations');
        }
    }

    /**
     * Edit.
     */
    public function action_edit()
    {
        try
        {
            $this->action_add();

            $model = new Model_Location(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            $data  = $model->get_data();

            $this->template->body->data = $data;
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/locations');
        }
    }

    /**
     * Save.
     */
    public function action_save()
    {
        try
        {
            $data = $_POST;
            $data['type'] = (strlen($data['new_type']) > 0) ? $data['new_type'] : $data['type'];

            $model = (is_numeric($data['id'])) ? new Model_Location($data['id']) : new Model_Location();
            $model->set_data($data);

            if ($model->save_location())
            {
                IbHelpers::set_message('Operation successfully completed.', 'success popup_box');

                $this->request->redirect('/admin/locations');
            }
            else
            {
                IbHelpers::set_message('Unable to complete the requested operation. Please, review the fields.', 'info popup_box');

                (is_numeric($_POST['id'])) ? $this->action_edit() : $this->action_add();
            }
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/locations');
        }
    }

    /**
     * Toggle publish option.
     */
    public function action_ajax_toggle_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']))
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_Location::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     * Delete.
     */
    public function action_ajax_delete()
    {
        $ok = FALSE;

        if ( isset($_POST['data']))
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_Location::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     * Get all.
     */
    public function action_ajax_get_all()
    {
        $this->response->body(json_encode(Model_Location::get_all()));
        $this->auto_render = FALSE;
    }
}
