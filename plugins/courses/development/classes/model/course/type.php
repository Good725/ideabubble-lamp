<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Type extends ORM
{
    protected $_deleted_column = 'delete';

    protected $_table_name = 'plugin_courses_types';

    protected $_has_many = [
        'courses' => ['model' => 'Course', 'foreign_key' => 'type_id'],
    ];
}