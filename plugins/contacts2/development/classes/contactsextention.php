<?php defined('SYSPATH') or die('No Direct Script Access.');

abstract class ContactsExtention
{
    abstract public function getData($contact_details, $request = null);
    abstract public function saveData($contact_id, $post);
    abstract public function getTabs($contact_details);
    abstract public function getFieldsets($contact_details);

    public function menus($array)
    {
        return $array;
    }

    public function required_js()
    {
        return array();
    }

    public function is_container()
    {
        return false;
    }

    public function get_container()
    {
        return null;
    }
}