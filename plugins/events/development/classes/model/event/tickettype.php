<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Event_TicketType extends ORM
{
	protected $_table_name = Model_Event::TABLE_HAS_TICKET_TYPES;
	protected $_belongs_to = array(
		'event' => array('model' => 'Event')
	);

}