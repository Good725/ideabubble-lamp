<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_RateCard extends ORM {
	protected $_table_name = Model_Propman::RATECARDS_TABLE;
	protected $_publish_column = 'published';

	protected $_belongs_to = array(
		'period' => array('model' => 'Propman_Period')
	);

	protected $_has_many = array(
		'properties'  => array(
			'model'       => 'Propman',
			'through'     => Model_Propman::PROPERTIES_HAS_RATECARDS_TABLE,
			'foreign_key' => 'ratecard_id'
		)
	);
}
