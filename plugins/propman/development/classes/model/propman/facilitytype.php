<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_FacilityType extends ORM {
	protected $_table_name = 'plugin_propman_facility_types';
	protected $_publish_column = 'published';

	protected $_belongs_to = array(
		'group' => array('model' => 'Propman_FacilityGroup')
	);
}
