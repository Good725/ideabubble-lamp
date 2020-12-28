<?php defined('SYSPATH') or die('No direct script access.');

class Model_Messaging_Starred extends ORM
{
	protected $_table_name = 'plugin_messaging_message_stars';

	// ORM delete() function does not work when there is no primary key
	public function unstar()
	{
		return DB::delete($this->_table_name)->where('message_id', '=', $this->message_id)->where('user_id', '=', $this->user_id)->execute();
	}
}
