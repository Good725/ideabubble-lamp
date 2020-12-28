<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dashboards_Sharing extends ORM
{
	protected $_table_name = 'plugin_dashboards_sharing';
	protected $_belongs_to = array('Dashboard' => array());

}