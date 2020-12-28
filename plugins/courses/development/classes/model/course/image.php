<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Image extends ORM
{
    protected $_table_name = 'plugin_courses_courses_images';

    protected $_belongs_to = [
        'course' => ['model' => 'course', 'foreign_key' => 'course_id']
    ];
}