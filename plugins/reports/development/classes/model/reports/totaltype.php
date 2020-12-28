<?php defined('SYSPATH') or die('No direct script access.');
class Model_Reports_TotalType extends ORM
{
	protected $_table_name = 'plugin_reports_total_types';
	protected $_has_many = array(
		'sparkline' => array ('model' => 'Reports_Sparkline', 'foreign_key' => 'total_type_id')
	);}
