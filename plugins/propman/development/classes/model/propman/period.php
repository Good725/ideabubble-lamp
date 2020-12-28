<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_Period extends ORM {
	protected $_table_name = Model_Propman::PERIODS_TABLE;
	protected $_publish_column = 'published';

	protected $_has_many = array(
		'rate_cards'  => array('model' => 'Propman_RateCards')
	);
}
