<?php
Class Controller_Admin_Projects extends Controller_cms{

    function before()
    {
        parent::before();
    }

    public function action_index()
    {
        $projects = Model_Projects::get_all_projects();
        $this->template->body = View::factory('list_projects',array("projects" => $projects));
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Projects::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Projects::get_breadcrumbs();
        $this->template->sidebar->tools = '<a href="/admin/projects/add_edit_project"><button type="button" class="btn">Create Project</button></a>';
    }

    public function action_add_edit_project()
    {
        $project = new Model_Projects($this->request->param('id'));
        $this->template->styles[URL::get_engine_plugin_assets_base('projects').'css/projects.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('projects').'js/projects.js"/>';
        $this->template->body = View::factory('add_edit_project',array(
            "project"   => $project->load(),
            'images'    => $project->get_project_images(),
            'unrelated' => $project->get_unrelated_projects(),
            'related'   => $project->get_related_projects())
        );
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Projects::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Projects::get_breadcrumbs();
        $this->template->sidebar->tools = '<a href="/admin/projects/add_edit_project"><button type="button" class="btn">Create Project</button></a>';
    }

    public function action_save()
    {
        $post          = $this->request->post();
        $project       = new Model_Projects();
        $project->set($post);
        $project_saved = $project->save();

        if ($project_saved)
        {
            IbHelpers::set_message('Project #'.$project->get_id().': '.$project->get_name().' saved', 'success popup_box');
            $image_ids = (isset($post['image_ids'])) ? $post['image_ids'] : array();
            $project->save_images($image_ids);
        }
        else
        {
            IbHelpers::set_message('Project failed to saved', 'error popup_box');
        }

        $this->request->redirect($project->destination($post));
    }

	public function action_delete()
	{
		$project = new Model_Projects($this->request->param('id'));
		$project->delete();
		IbHelpers::set_message('Project #'.$project->get_id().': '.$project->get_name().' deleted', 'success popup_box');
		$this->request->redirect('/admin/projects/');
	}

    public function action_categories()
    {
        $category = new Model_Projects_Categories($this->request->param('id'));
        $this->template->styles[URL::get_engine_plugin_assets_base('projects').'css/projects.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('projects').'js/projects.js"/>';
        $this->template->body = View::factory('list_categories',array("categories" => Model_Projects_Categories::get_all_categories()));
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Projects::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Projects::get_breadcrumbs();
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Categories', 'link' => '/admin/projects/categories');
        $this->template->sidebar->tools = '<a href="/admin/projects/add_edit_category"><button type="button" class="btn">Create Category</button></a>';
    }

    public function action_add_edit_category()
    {
        $category = new Model_Projects_Categories($this->request->param('id'));
        $this->template->styles[URL::get_engine_plugin_assets_base('projects').'css/projects.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('projects').'js/categories.js"/>';
        $this->template->body = View::factory('add_edit_category',array("category" => $category->get()));
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Projects::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Projects::get_breadcrumbs();
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Categories', 'link' => '/admin/projects/categories');
        $this->template->sidebar->tools = '<a href="/admin/projects/add_edit_category"><button type="button" class="btn">Create Category</button></a>';
    }

    public function action_save_category()
    {
        $post = $this->request->post();
        $category = new Model_Projects_Categories();
        $category->load($post);
        $category->save();
        $this->request->redirect("/admin/projects/categories/".$category->get_id());
    }

    public function action_ajax_get_project_as_related()
    {
        $post = $this->request->post();
        $this->auto_render = FALSE;
        $project = new Model_Projects($post['project_id']);
        $project->add_relation($post['current']);
        echo View::factory('related_projects',array('project' => $project));
    }

    public function action_ajax_remove_related_project()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $project = new Model_Projects($post['project_id']);
        echo $project->remove_relation($post['current']);
    }

    public function action_ajax_add_image_tr()
    {
        $this->auto_render = FALSE;
        $image_model       = new Model_Media();
        $images            = $image_model->get_all_items_admin($this->request->param('id'));
        $return            = (sizeof($images) > 0) ? (string) View::factory('snippets/image_tr')->set('image', $images[0]) : '';
        $this->response->body($return);
    }
}
?>
