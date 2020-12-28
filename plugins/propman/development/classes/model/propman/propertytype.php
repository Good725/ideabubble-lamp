<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_PropertyType extends ORM {
	protected $_table_name = 'plugin_propman_property_types';
	protected $_publish_column = 'published';

	protected $_has_many = array(
		'properties' => array('model' => 'Propman'),
	);
}
