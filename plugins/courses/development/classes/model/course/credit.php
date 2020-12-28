<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Credit extends ORM
{
    protected $_table_name = 'plugin_courses_credits';

    protected $_created_by_column    = 'created_by';
    protected $_date_created_column  = 'created';
    protected $_modified_by_column   = 'updated_by';
    protected $_date_modified_column = 'updated';

    protected $_belongs_to = [
        'study_mode' => ['model' => 'Course_StudyMode', 'foreign_key' => 'study_mode_id']
    ];
}