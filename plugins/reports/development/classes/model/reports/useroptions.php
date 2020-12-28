<?php defined('SYSPATH') or die('No direct script access.');

// This is for setting user preferences for an individual report.
// e.g. the order it's to appear in on the dashboard for the user in question

class Model_Reports_UserOptions extends ORM
{
	protected $_table_name = 'plugin_reports_user_options';

	public function save(Validation $validation = NULL)
	{
		$this->set('date_modified', date('Y-m-d H:i:s'));
		parent::save();
	}
}
