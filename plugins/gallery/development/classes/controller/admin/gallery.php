<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Admin_Gallery extends Controller_Cms
{
    function before()
    {
        parent::before();

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array('icon' => 'galleries', 'name' => 'Gallery', 'link' => '/admin/gallery')
        ));
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Gallery', 'link' => '/admin/gallery')
        );
        switch($this->request->action())
        {
            case 'index':
            case 'add':
            case 'edit':
                $this->template->sidebar->tools = '<a href="/admin/gallery/add"><button type="button" class="btn">Add Gallery</button></a>';
                break;
        }
    }


    /**
     * Entry point.
     */
    public function action_index()
    {
        // Default action
        $this->action_list();
    }

    /**
     * List galleries.
     */
    public function action_list()
    {
        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('gallery').'css/gallery.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('gallery').'js/gallery.js"></script>';

        //get the icon
        $results['plugin'] = Model_Plugin::get_plugin_by_name('gallery');

        // Select the view
        $this->template->body = View::factory('list_gallery', $results);

        // If is set, get the category from the request
        $category = (isset($_POST['category']) AND $_POST['category'] != 'all') ? $_POST['category'] : NULL;

        // Fill the template
        $this->template->body->category_list = Model_Gallery::get_categories();
        $this->template->body->galleries     = Model_Gallery::get_gallery_all($category);
    }

    /**
     * Add gallery.
     */
    public function action_add()
    {
        try
        {
            // Assets
            $this->template->styles[URL::get_engine_plugin_assets_base('gallery').'css/gallery.css'] = 'screen';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('gallery').'js/gallery.js"></script>';

            // Select the view
            $this->template->body = View::factory('add_edit_gallery');

            // Load the required classes
            $media = new Model_Media();

            // Fill the template
            $this->template->body->action        = 1;
            $this->template->body->category_list = Model_Gallery::get_categories();
            $this->template->body->image_list    = $media->get_all_items_based_on('location', 'gallery', 'as_details', '=');
        }
        catch (Exception $e)
        {
            // Bad request
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/gallery/');
        }
    }

    /**
     * Edit contact.
     */
    public function action_edit()
    {
        try
        {
            // Assets
            $this->template->styles[URL::get_engine_plugin_assets_base('gallery').'css/gallery.css'] = 'screen';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('gallery').'js/gallery.js"></script>';

            // Select the view
            $this->template->body = View::factory('add_edit_gallery');

            // Load the required classes
            $media   = new Model_Media();
            $gallery = new Model_Gallery($this->request->param('id'));

            // Fill the template
            $this->template->body->action          = 2;
            $this->template->body->field_data      = $gallery->get_data();
            $this->template->body->category_list   = Model_Gallery::get_categories();
            $this->template->body->image_list      = $media->get_all_items_based_on('location', 'gallery', 'as_details', '=');
        }
        catch (Exception $e)
        {
            // Bad request
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/gallery/');
        }
    }

    /**
     * Save.
     */
    public function action_save()
    {
        $ok = FALSE;
		$errors = array();

        try
        {
			$errors = Model_Gallery::service_validate_submit();
			$ok = (count($errors) == 0);
			if (count($errors) == 0)
            {
                // Load the contact or create a new one
                $gallery = (is_numeric($_POST['id'])) ? new Model_Gallery($_POST['id']) : new Model_Gallery();

                // Set the data
                $gallery->set_photo_name ($_POST['photo_name']);
                $gallery->set_category   ( (strlen($_POST['new_category']) > 0) ? $_POST['new_category'] : $_POST['category'] );
                $gallery->set_title      ($_POST['title'     ]);
                $gallery->set_order      ($_POST['order'     ]);
                $gallery->set_publish    ($_POST['publish'   ]);

                // Save
                $ok = $gallery->save();
            }
        }
        catch (Exception $e)
        {
            // Bad request
			IbHelpers::set_message('An error has occurred while saving the gallery. If this error persists, please ask your administrator to check the system logs for more information.', 'danger popup_box');

            $this->request->redirect('/admin/gallery/');
        }

        if ($ok)
        {
            // Operation completed
            IbHelpers::set_message((is_numeric($_POST['id'])) ? 'Gallery successfully updated.' : 'Gallery successfully added.', 'success popup_box');

            $this->request->redirect('/admin/gallery/');
        }
        else
        {
			// Operation not completed
			foreach ($errors as $error)
			{
				IbHelpers::set_message($error, 'danger popup_box');
			}

            // Call the proper function
            (is_numeric($_POST['id'])) ? $this->action_edit() : $this->action_add();
        }
    }

    /**
     * Toggle gallery publish.
     */
    public function action_ajax_toggle_publish()
    {
        $ok = FALSE;

        $id = $this->request->param('id');

        if ( isset($id) )
        {
            $ok = Model_Gallery::toggle_gallery_publish($id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     * Delete gallery.
     */
    public function action_ajax_delete()
    {
        $ok = FALSE;

        $id = $this->request->param('id');

        if ( isset($id) )
        {
            $ok = Model_Gallery::delete_gallery($id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }
}
