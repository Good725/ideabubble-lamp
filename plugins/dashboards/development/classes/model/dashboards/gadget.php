<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dashboards_Gadget extends ORM
{
	protected $_table_name = 'plugin_dashboards_gadgets';
	protected $_belongs_to = array(
		'dashboard'   => array('model' => 'Dashboard',        'foreign_key' => 'dashboard_id'),
		'gadget_type' => array('model' => 'Dashboard_Gadget', 'foreign_key' => 'type_id')
	);

    function get_report()
    {
        $report = new Model_Reports($this->gadget_id);
        $report->get(true);
        $report->get_widget(true);

        return $report;
    }

}