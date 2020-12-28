<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_Photo extends ORM {

	protected $_table_name = 'plugin_media_shared_media';

	protected $_has_many = array(
		'properties'  => array(
			'model'       => 'Propman',
			'through'     => Model_Propman::PROPERTIES_HAS_MEDIA_TABLE,
			'foreign_key' => 'media_id',
			'far_key'     => 'property_id'
		)
	);
}
