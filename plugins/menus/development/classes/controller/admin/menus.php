<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Menus extends Controller_cms
{
    function before() {
        parent::before();

		if ( ! Auth::instance()->has_access('menus'))
		{
			IbHelpers::set_message("You need access to the &quot;menus&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array();
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Menus', 'link' => '/admin/menus')
        );
        switch($this->request->action())
        {
            case 'index':
                $this->template->sidebar->tools = '<a href="" class="" id="add_menu"><button type="button" class="btn btn-primary">Add Menu Item</button></a>';
                break;
        }
    }
    public function action_index(){
        //Create an instance of the pages module
        $menus = new Model_Menus();
        // Get menus from the database
        $results['menus'] = $menus->get_all_menus();
        //Get pages list from the database
        $results['pages'] = $menus->get_pages_list();

        $results['pages_dropdown'] = $menus->get_pages_list_dropdown();
        $media = new Model_Media();
        $available_images = $media->get_all_items_based_on('location','menus','details','=');

        //Loads the CSS and  javascript files
        if(isset($this->template->styles) && is_array($this->template->styles))
            $this->template->styles = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('menus') .'css/menu_list.css' => 'screen'));
        else
            $this->template->styles= array(URL::get_engine_plugin_assets_base('menus') .'css/menu_list.css' => 'screen');

        $this->template->scripts[] = '<script src="'. URL::overload_asset('js/floatactionbar.js', ['cachebust' => true]) .'"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_asset('menus', 'js/menu_list.js', ['cachebust' => true]) . '"></script>';

        //Send database attributes to the view and load the body here.
        $this->template->body =  View::factory('menu_list', $results);
        $this->template->body->plugin = Model_Plugin::get_plugin_by_name('menus');
        $this->template->body->available_images = $available_images;
    }

    /**
     * Ajax function, print the rigth dropdown menu in the new menu layer, for the current chosen menu.
     */
    public function action_get_option_dropdown(){
        $current_menu = $this->request->post();
        $menus = new Model_Menus();

        $selector_html = $menus->get_option_dropdown($current_menu);

        //Prevent load template, for RAW output.
        $this->auto_render = FALSE;
        $this->response->body($selector_html);
    }

    /**
     * Update menus and returns to the same page
     */
    public function action_save_menus(){
        $menus = new Model_Menus();
        $success = $menus->set_menu_data($this->request->post());
        if($success){
            IbHelpers::set_message('The page has been updated.', 'success popup_box');
        }
        else{
            IbHelpers::set_message('The page could not be updated', 'error popup_box');
        }

        $this->request->redirect('admin/menus');
    }

    public function action_save_new_menu(){

        $menu = new Model_Menus();

        $post_data = $this->request->post();

        $res = $menu->set_new_menu_data($post_data);

        if($res){
            IbHelpers::set_message('The menu has been added', 'success popup_box');
        }
        else{
            IbHelpers::set_message('The menu could not be added', 'error popup_box');
        }
        $this->request->redirect('admin/menus');
    }

    /**
     * Ajax call: Change the publish status of the selected page.
     */
    public function action_publish(){
        $id =  $this->request->param('id');
        if(!isset($id) || (int)$id < 1){
            $msg = "error";
        }
        else{
            $page = new Model_Menus();
            $msg = $page->change_published_status($id);
        }
        $this->auto_render = FALSE;
        $this->response->body($msg);
    }

    /**
     * Ajax call: Delete the selected menu
     */
    public function action_delete_menu(){
        $menu = new Model_Menus();
        $id = $this->request->param('id');
        $count = $menu->delete_menu($id);
        $this->auto_render = FALSE;
        //Return the number of deleted row. 1 = success, 0 = error
        $this->response->body($count);
    }

    function after()
    {
        // Load the messages from the IbHelper.
        $messages = IbHelpers::get_messages();

        // If there are messages
        if ($messages)
        {
            // Add the message to the alert string if it exists
            if (isset($this->template->body->alert))
            {
                $this->template->body->alert = $this->template->body->alert . $messages;
            }
            // Else create an alert string
            else
            {
                $this->template->body->alert = $messages;
            }
        }

        parent::after();

    }
}
