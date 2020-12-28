<?php defined('SYSPATH') or die('No direct script access.');

/**
 */
class Model_AnswerOption extends ORM {
    protected $_table_name = 'plugin_survey_answer_options';

	protected $_belongs_to = array(
		'answer'   => array('model' => 'Answer')
	);

    protected $_date_created_column  = 'created_on';
    protected $_modified_by_column   = 'updated_by';
    protected $_date_modified_column = 'updated_on';
}