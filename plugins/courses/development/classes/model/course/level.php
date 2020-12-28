<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Level extends ORM
{
    protected $_table_name = 'plugin_courses_levels';
    protected $_deleted_column = 'delete';

    public function get_short_name()
    {
        if ($this->short_name) {
            return $this->short_name;
        } else {
            return substr($this->level, 0, 1);
        }
    }
}