<?php defined('SYSPATH') or die('No Direct Script Access.');

class ContactMessagingExtention extends  ContactsExtention
{
    protected $cache = array();
    public function menus($array)
    {
        return $array;
    }

    public function required_js()
    {
        return array(
            URL::get_engine_plugin_assets_base('messaging') . 'js/messaging.js?ts='     . filemtime(ENGINEPATH.'plugins/messaging/development/assets/js/messaging.js'),
            URL::get_engine_plugin_assets_base('messaging') . 'js/list_messages.js?ts=' . filemtime(ENGINEPATH.'plugins/messaging/development/assets/js/list_messages.js')
        );
    }

    public function getData($contact, $request = null)
    {
        if (isset($this->cache['data'])) {
            return $this->cache['data'];
        } else {
            $data = array();
            $mm = new Model_Messaging();
            $data['use_columns'] = array('actions', 'from', 'subject', 'to', 'folder', 'status', 'last_activity', 'info');
            $data['parameters']  = array(
                'targets' => array(array('target_type' =>'CMS_CONTACT', 'target' => $contact['id']), array('target_type' => 'EMAIL', 'target' => $contact['email']), array('target_type' => 'PHONE', 'target' => $contact['mobile'])),
                'ftargets' => array(array('target_type' =>'EMAIL', 'target' => $contact['email']), array('target_type' => 'MOBILE', 'target' => $contact['mobile']))
            );
            $data['signatures'] = Model_Signature::search();
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
                'name' => 'messages',
                'title' => 'Messages',
                'view' => 'messaging_list_ajax',
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