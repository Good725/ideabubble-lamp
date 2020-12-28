<?php defined('SYSPATH') or die('No Direct Script Access.');

abstract class FamiliesExtention
{
    abstract public function getData($family_details, $request = null);
    abstract public function saveData($family_id, $post);
    abstract public function getTabs($family_details);
    abstract public function getFieldsets($family_details);

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