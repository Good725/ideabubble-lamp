<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Event_Date extends ORM
{
	protected $_table_name = Model_Event::TABLE_DATES;
	protected $_belongs_to = array(
		'event' => array('model' => 'Event')
	);

}