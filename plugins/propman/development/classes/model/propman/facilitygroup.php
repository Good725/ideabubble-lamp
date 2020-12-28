<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_FacilityGroup extends ORM {
	protected $_table_name = 'plugin_propman_facility_groups';

	protected $_has_many = array(
		'types' => array('model' => 'Propman_FacilityType', 'foreign_key' => 'facility_group_id'),
	);
}
