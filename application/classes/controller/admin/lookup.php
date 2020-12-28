<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Lookup extends Controller_Head
{
    public function before()
    {
        parent::before();
        if (!Auth::instance()->has_access('lookups')) {
            IbHelpers::set_message("You don't have permission!", 'warning');
            $this->request->redirect('/admin');
        }
    }

    public function action_index ()
    {
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Lookups',  'link' => '/admin/lookup');

        $lookups = Model_Lookup::lookupList();
        $this->template->body = View::factory('content/lookup/list_lookups');
        $this->template->body->lookups = $lookups;
        $this->template->body->lookupsUpdate = Auth::instance()->has_access('lookups');
    }

    public function action_edit_lookup()
    {
        if($this->request->post()){
            $values['field_id'] = $this->request->post('field_id');
            $values['label'] = $this->request->post('label');
            $values['value'] = $this->request->post('value');
            $values['is_default'] = $this->request->post('default') == 'on' ? 1 : 0;
            $values['updated'] = date('Y-m-d H:i:s');
            Model_Lookup::update_lookup($values, $this->request->post('id'));
            $this->request->redirect('/admin/lookup');
        }
        $lookup = Model_Lookup::lookupLoad($this->request->param('id'));
        $field_names = Model_Lookup::get_lookup_field_names();
        $view  = View::factory('content/lookup/edit_lookup')
            ->set('lookup', $lookup)
            ->set('field_names',$field_names);
        $this->template->body = $view;
    }

    public function action_clone_lookup()
    {
        Model_Lookup::cloneLookup($this->request->param('id'));
        $this->request->redirect('/admin/lookup');
    }

    public function action_make_default_lookup()
    {
        $lookup = Model_Lookup::lookupLoad($this->request->param('id'));

        if (!empty($lookup)) {
            $values['field_id'] = $lookup['field_id'];
            $values['is_default'] = 1;
            Model_Lookup::update_lookup($values, $this->request->param('id'));
        }
        $this->request->redirect('/admin/lookup');
    }

    public function action_set_unique_value_lookup()
    {
        Model_Lookup::makeUniqueValue($this->request->param('id'));
        $this->request->redirect('/admin/lookup');
    }

    public function action_delete_lookup()
    {
        Model_Lookup::deleteLookup($this->request->param('id'));
        $this->request->redirect('/admin/lookup');
    }

    public function action_publish_lookup()
    {
        $values['public'] = 1;
        Model_Lookup::update_lookup($values, $this->request->param('id'));
        $this->request->redirect('/admin/lookup');
    }

    public  function action_create_lookup()
    {
        $this->template->styles    = array (
            URL::get_engine_assets_base().'css/list_settings.css' => 'screen',
            URL::get_engine_assets_base().'css/bootstrap-multiselect.css' => 'screen',
        );

        $lookup_insert['id'] = '';
        $field_names = Model_Lookup::get_lookup_field_names();
        if($this->request->post()){

            $user = Auth::instance()->get_user();
            $values['label'] = $this->request->post('label');
            $values['field_id'] = $this->request->post('field_id');
            $values['value'] = $this->request->post('value');
            $values['is_default'] = $this->request->post('default') ? true:false;
            $values['created'] = date('Y-m-d H:i:s');
            $values['updated'] = date('Y-m-d H:i:s');
            $values['autor'] = $user['id'];
            Model_Lookup::create_lookup($values);
            $this->request->redirect('admin/lookup');
        }
        $view  = View::factory('content/lookup/create_lookups')
               ->set('field_names',$field_names);
        $this->template->body = $view;
    }
}
