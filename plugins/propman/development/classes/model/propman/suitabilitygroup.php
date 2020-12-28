<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_SuitabilityGroup extends ORM {
	protected $_table_name = 'plugin_propman_suitability_groups';

	protected $_has_many = array(
		'types' => array('model' => 'Propman_SuitabilityType', 'foreign_key' => 'suitability_group_id'),
	);
}
