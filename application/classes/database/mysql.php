<?php defined('SYSPATH') or die('No direct script access.');

class Database_MySQL extends Kohana_Database_MySQL {

	public function get_connection() {
		return $this->_connection;
	}
}
