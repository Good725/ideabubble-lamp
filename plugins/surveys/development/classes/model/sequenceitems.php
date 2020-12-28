<?php defined('SYSPATH') or die('No direct script access.');

/**
 */
class Model_SequenceItems extends ORM {
    protected $_table_name = 'plugin_survey_sequence_items';

    protected $_has_many = array(
        'survey'   => array(
            'model'   => 'Survey',
            'through' => 'survey_id',
        ),
        'question' => array(
            'model'   => 'Question',
            'through' => 'question_id'
        ),
    );
}