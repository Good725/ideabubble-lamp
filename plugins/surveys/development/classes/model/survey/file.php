<?php defined('SYSPATH') or die('No direct script access.');

class Model_Survey_File extends ORM {
	protected $_table_name = 'plugin_files_file';

	protected $_has_many = array(
		'surveys' => array('model' => 'Survey', 'through' => 'result_template_id')
	);
}
