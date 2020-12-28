<?php defined('SYSPATH') or die('No direct script access.');

class Model_Engine_Theme_HasVariable extends ORM
{
    protected $_table_name = 'engine_site_theme_has_variables';

    protected $_belongs_to = array(
        'theme'    => array('model' => 'Engine_Theme'),
        'variable' => array('model' => 'Engine_Theme_Variable')
    );

}