<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Formbuilder extends Controller_cms
{
    function before()
    {
        parent::before();

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array('icon' => 'forms', 'name' => 'Forms', 'link' => '/admin/formbuilder')
        ));
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Forms', 'link' => '/admin/formbuilder')
        );
        switch($this->request->action())
        {
            case 'index':
            case 'add_edit_form':
                $this->template->sidebar->tools = '<a href="/admin/formbuilder/add_edit_form"><button type="button" class="btn">Create Form</button></a>';
                break;
        }
    }

    public function action_index()
    {
        $formbuilder = new Model_Formbuilder();
        $results['plugin'] = Model_Plugin::get_plugin_by_name('formbuilder');
        $results['forms'] = $formbuilder->show_all_forms();
        $this->template->body = View::factory('list_forms',$results);
    }

    public function action_add_edit_form()
    {
        $form_id = $this->request->param('id');
        $form = new Model_Formbuilder($form_id);
        $results['plugin'] = Model_Plugin::get_plugin_by_name('formbuilder');
        $results['form'] = $form->load();
        $results['options'] = $form->get_options_as_array();

        $this->template->styles[URL::get_engine_plugin_assets_base('formbuilder').'css/validation.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('formbuilder').'css/styles.css'] = 'screen';

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('formbuilder') . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('formbuilder') . 'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('formbuilder') .'js/formbuilder.js"></script>';

        $this->template->body = View::factory('add_form',$results);
    }

    public function action_save()
    {
        $param = $this->request->param('id');
        $post = $this->request->post();
        $form = new Model_Formbuilder($param);
        $saved = $form->save($post,$param);

        if ($saved)
        {
            IbHelpers::set_message('Form successfully saved', 'success');
        }
        else
        {
            IbHelpers::set_message('Error saving form', 'error');
        }

        if ($post['return_action'] == 'save_and_exit')
            $this->request->redirect('/admin/formbuilder');

        if ($post['return_action'] == 'save')
            $this->request->redirect('/admin/formbuilder/add_edit_form?id=' . $form->get_id());
    }
}