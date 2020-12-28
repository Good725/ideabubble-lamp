<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dashboards_Gadgettype extends ORM
{
	protected $_table_name = 'plugin_dashboards_gadget_types';
	protected $_has_many   = array(
		'gadget'           => array('model' => 'Dashboards_Gadget', 'foreign_key' => 'type_id'),
	);
}