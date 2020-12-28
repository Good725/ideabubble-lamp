<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dashboards_Favorite extends ORM
{
	protected $_table_name = 'plugin_dashboards_favorites';
	protected $_belongs_to = array(
		'dashboard' => array('model' => 'Dashboard')
	);

}