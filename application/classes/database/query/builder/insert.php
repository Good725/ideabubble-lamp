<?php defined('SYSPATH') or die('No direct script access.');

class Database_Query_Builder_Insert extends Kohana_Database_Query_Builder_Insert {

	/**
	 * If column names are not set then column names will be taken from value array key names.
	 *
	 * @param array $values
	 * @return $this|Database_Query_Builder_Insert
	 */
	public function values(array $values)
	{
		if (!$this->_columns) {
			$this->_columns = array_keys($values);
//			IbHelpers::die_r($this->_columns);
		}

		parent::values($values);
		return $this;
	}

}
