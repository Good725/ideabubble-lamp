<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Spec_Material extends ORM
{
    protected $_table_name = 'plugin_courses_specs_have_recommended_material';

    protected $_belongs_to = [
        'product' => ['model' => 'product_product', 'foreign_key' => 'product_id'],
        'spec'    => ['model' => 'course_spec',     'foreign_key' => 'spec_id'],
    ];
}
