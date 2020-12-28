<?php defined('SYSPATH') or die('No Direct Script Access.');

class ContactTodosExtention extends  ContactsExtention
{
    protected $cache = array();
    public function menus($array)
    {
        return $array;
    }

    public function required_js()
    {
        return array(
        );
    }

    public function getData($contact, $request = null)
    {
        if (isset($this->cache['data'])) {
            return $this->cache['data'];
        } else {
            $data = array();
            $todos = new Model_Todos();
            $rid = Model_Todos::get_related_to('contacts2');
            $data = array(
                'todos' => $todos->get_all_related_todos($rid['id'], $contact['id']),
                'related_plugin_name' => 'contacts2',
                'related_to_id' => $contact['id'],
                'return_url' => '/admin/contacts2/edit/' . $contact['id']
            );
            $this->cache['data'] = $data;
            return $data;
        }
    }

    public function saveData($contact_id, $post)
    {
    }

    public function getTabs($contact_details)
    {
        return array(
            array(
                'name' => 'todos',
                'title' => 'Todos',
                'view' => 'list_related_todos',
            )
        );
    }

    public function getFieldsets($contact_details)
    {
        return array(
        );
    }

    public function is_container()
    {
        return false;
    }

    public function get_container()
    {
        return  false;
    }
}