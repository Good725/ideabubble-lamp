<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_SuitabilityType extends ORM {
	protected $_table_name = 'plugin_propman_suitability_types';
	protected $_publish_column = 'published';

	protected $_belongs_to = array(
		'group' => array('model' => 'Propman_SuitabilityGroup')
	);
}
