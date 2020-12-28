<?php defined('SYSPATH') or die('No direct script access.');

class Model_Calendar_Type extends ORM {
    protected $_table_name = 'engine_calendar_types';

    protected $_has_many = array(
            'event'  => array(
				'model' => 'Calendar_Event'
				)
        );

}