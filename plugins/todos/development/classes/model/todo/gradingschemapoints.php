<?php defined('SYSPATH') or die('No direct script access.');

class Model_Todo_GradingSchemaPoints extends ORM
{
    protected $_table_name = 'plugin_todos_grading_schema_points';

    protected $_belongs_to = [
        'grade'   => ['model' => 'Todo_Grade',         'foreign_key' => 'grade_id'],
        'level'   => ['model' => 'Course_Level',       'foreign_key' => 'level_id'],
        'schema'  => ['model' => 'Todo_GradingSchema', 'foreign_key' => 'schema_id'],
        'subject' => ['model' => 'Course_Subject',     'foreign_key' => 'subject_id'],
    ];
}