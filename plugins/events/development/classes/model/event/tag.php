<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Event_Tag extends ORM
{
	protected $_publish_column = 'published';
	protected $_table_name = Model_Event::TABLE_TAGS;

}