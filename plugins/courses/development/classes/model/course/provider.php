<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Provider extends ORM
{
    protected $_table_name = 'plugin_courses_providers';
    protected $_deleted_column = 'delete';
}