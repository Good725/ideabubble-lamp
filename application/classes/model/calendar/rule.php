<?php defined('SYSPATH') or die('No direct script access.');

class Model_Calendar_Rule extends ORM {
    protected $_table_name = 'engine_calendar_rules';

	protected $_has_many = array(
		'event'  => array(
			'model' => 'Calendar_Event',
			'through' => 'rule_id',
		)
	);

}