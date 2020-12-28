<?php defined('SYSPATH') or die('No direct script access.');

/**
 */
class Model_SurveyHasGroup extends ORM {
    protected $_table_name = 'plugin_survey_has_groups';
    protected $_date_created_column = 'created_on';
    protected $_date_modified_column = 'updated_on';
    protected $_modified_by_column = 'updated_by';

    protected $_belongs_to = array(
        'survey'   => array('model' => 'Survey'),
        'group' => array('model' => 'Group')
    );
}