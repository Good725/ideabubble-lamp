<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Cars extends Controller_Cms
{

    public function before()
    {
        parent::before();

        // Submenu items for cms
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array('Cars' => array(
            array('name' => 'Cars',       'link' => 'admin/cars'),
            array('name' => 'Categories', 'link' => 'admin/cars/categories')
        ));

        // Set up breadcrumbs
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home', 'link' => '/admin'),
            array('name' => 'Cars', 'link' => '/admin/cars')
        );
        switch($this->request->action())
        {
            case 'index':
            case 'add_edit_car':
                $this->template->sidebar->tools         = '<a href="/admin/cars/add_edit_car" class="btn">'.__('Add Car').'</a>';
                break;

            case 'categories':
            case 'add_edit_category':
                $this->template->sidebar->breadcrumbs[] = array('name' => 'Categories', 'link' => '/admin/cars/categories');
                $this->template->sidebar->tools         = '<a href="/admin/cars/add_edit_category" class="btn">'.__('Add Category').'</a>';
                break;
        }
    }


    public function action_index()
    {
        $cars = Model_Cars::get_all_cars();
        $this->template->body      = View::factory('/admin/list_cars')->bind('cars',$cars);
    }

    public function action_add_edit_car()
    {
        $id                        = $this->request->param('id');
        $car                       = Model_Cars::create($id);
		$categories                = Model_Carcategory::get_all();
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
        $this->template->body      = View::factory('/admin/add_edit_car')->bind('car',$car)->bind('categories',$categories);
    }

	public function action_save()
	{
		$post = $this->request->post();
		$car = Model_Cars::create()->set($post);
		($car->save())
			? IbHelpers::set_message('Vehicle #'.$car->get_id().' successfully saved.', 'success')
			: IbHelpers::set_message('Failed to save vehicle.', 'error');
		$redirect = (isset($post['redirect']) AND $post['redirect'] == 'exit') ? '' : 'add_edit_car/'.$car->get_id();
		$this->request->redirect('/admin/cars/'.$redirect);
	}

	public function action_delete()
	{
		$car = new Model_Cars($this->request->param('id'));
		$car->delete();
		$this->request->redirect('/admin/cars/');
	}

    public function action_categories()
    {
		$categories                = Model_Carcategory::get_all();
        $this->template->scripts[] = '';
		$this->template->body      = View::factory('/admin/list_categories')->set('categories',$categories);
    }

    public function action_add_edit_category()
    {
		$category                  = new Model_Carcategory($this->request->param('id'));
		$category                  = $category->get_instance();
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
        $this->template->body      = View::factory('/admin/add_edit_category')->set('category',$category);
    }

	public function action_save_category()
	{
		$post     = $this->request->post();
		$category = Model_Carcategory::create()->set($post);
		($category->save())
			? IbHelpers::set_message('Category #'.$category->get_id().' successfully saved.', 'success')
			: IbHelpers::set_message('Failed to save category.', 'error');
		$redirect = (isset($post['redirect']) AND $post['redirect'] == 'exit') ? 'categories' : 'add_edit_category/'.$category->get_id();
		$this->request->redirect('/admin/cars/'.$redirect);
	}

	public function action_delete_category()
	{
		$category = new Model_Carcategory($this->request->param('id'));
		$category->delete();
		$this->request->redirect('/admin/cars/categories');
	}

	public function action_ajax_toggle_publish()
	{
		$this->auto_render = FALSE;
		$category = new Model_Carcategory($this->request->param('id'));
		return $category->toggle_publish();
	}

	public function action_ajax_delete_category()
	{
		$this->auto_render = FALSE;
		$category = new Model_Carcategory($this->request->param('id'));
		return $category->delete();
	}

    public function action_cron()
    {
        $id = Settings::instance()->get('car_csv_template');
        $csv_handler = Model_CSV::create($id);
        $csv = Model_Cars::download_csv();
        $csv_handler->execute_csv_import($csv);
    }
}
?>