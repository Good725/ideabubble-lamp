<?php defined('SYSPATH') or die('No direct script access.');
class Model_Reports_ChartType extends ORM
{
	protected $_table_name = 'plugin_reports_chart_types';
	protected $_has_many = array(
		'sparkline' => array ('model' => 'Reports_Sparkline', 'foreign_key' => 'chart_type_id')
	 );

}
