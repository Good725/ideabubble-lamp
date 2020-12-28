<?php defined('SYSPATH') or die('No direct script access.');

class Model_Engine_Template extends ORM
{
    protected $_table_name = 'engine_site_templates';
    protected $_has_many = array(
        'layouts' => array('model' => 'Engine_Layout')
    );
    protected $_belongs_to = array(
        'creator'     => array('model' => 'User', 'foreign_key' => 'created_by'),
        'last_editor' => array('model' => 'User', 'foreign_key' => 'modified_by')
    );
}
