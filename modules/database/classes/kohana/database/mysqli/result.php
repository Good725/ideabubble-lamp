<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MySQL database result.   See [Results](/database/results) for usage and examples.
 *
 * @package    Kohana/Database
 * @category   Query/Result
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Database_MySQLI_Result extends Database_Result {

	protected $_internal_row = 0;

	public function __construct($result, $sql, $as_object = FALSE, array $params = NULL)
	{
		parent::__construct($result, $sql, $as_object, $params);
		// Find the number of rows in the result
		$this->_total_rows = $result->num_rows;
	}

	public function __destruct()
	{
		if (is_resource($this->_result))
		{
			$this->_result->free();
		}
	}

	public function as_array($key = null, $value = null)
	{
		$results = array();

		if ($key === NULL AND $value === NULL) {
            if ($this->_as_object) {
                return parent::as_array($key, $value);
            } else {
                while ($row = $this->_result->fetch_assoc()){
                    $results[] = $row;
                }
            }

		} else if ($key === NULL) {
			// Indexed columns

			if ($this->_as_object) {
                return parent::as_array($key, $value);
			} else {
				while ($row = $this->_result->fetch_assoc()) {
					$results[] = $row[$value];
				}
			}
		} elseif ($value === NULL) {
			// Associative rows

			if ($this->_as_object) {
                return parent::as_array($key, $value);
			} else {
				while ($row = $this->_result->fetch_assoc()) {
					$results[$row[$key]] = $row;
				}
			}
		} else {
			// Associative columns

			if ($this->_as_object) {
                return parent::as_array($key, $value);
			} else {
				while ($row = $this->_result->fetch_assoc()) {
					$results[$row[$key]] = $row[$value];
				}
			}
		}

		return $results;
	}

	public function seek($offset)
	{
		$this->_current_row = $this->_internal_row = $offset;
		return $this->_result->data_seek($offset);
	}

	public function current()
	{
		if ($this->_current_row !== $this->_internal_row AND ! $this->seek($this->_current_row))
			return NULL;

		// Increment internal row for optimization assuming rows are fetched in order
		$this->_internal_row++;

		if ($this->_as_object === TRUE)
		{
			// Return an stdClass
			return $this->_result->fetch_object();
		}
		elseif (is_string($this->_as_object))
		{
			// Return an object of given class name
			if ($this->_object_params == null) {
				return $this->_result->fetch_object($this->_as_object);
			} else {
				return $this->_result->fetch_object($this->_as_object, $this->_object_params);
			}
		}
		else
		{
			// Return an array of the row
			return $this->_result->fetch_assoc();
		}
	}
	
	public function fields()
	{
		$fields = array();
		$fields_ = $this->_result->fetch_fields();
		foreach($fields_ as $field){
			$fields[] = $field->name;
		}
		return $fields;
	}

} // End Database_MySQL_Result_Select
