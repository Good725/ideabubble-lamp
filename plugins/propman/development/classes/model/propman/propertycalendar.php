<?php defined('SYSPATH') or die('No direct script access.');

class Model_Propman_PropertyCalendar extends ORM {
    protected $_table_name = 'plugin_propman_properties_calendar';
    protected $_publish_column = 'available';

    protected $_belongs_to = array(
        'properties' => array('model' => 'Propman'),
    );
}
