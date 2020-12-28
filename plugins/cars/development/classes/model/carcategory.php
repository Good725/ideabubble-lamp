<?php

class Model_Carcategory extends Model implements Interface_Ideabubble
{
	/*** CLASS CONSTANTS ***/
	const MAIN_TABLE       = 'plugin_cars_categories';
	const ID_COLUMN        = 'id';
	const EDIT_ACTION      = '/admin/cars/add_edit_category/';

	/*** PRIVATE MEMBER DATA ***/
	private $id            = NULL;
	private $title         = '';
	private $order         = 0;
	private $summary       = '';
	private $description   = '';
	private $created_by    = NULL;
	private $modified_by   = NULL;
	private $date_created  = NULL;
	private $date_modified = NULL;
	private $publish       = 1;
	private $deleted       = 0;
	private static $errors = array();

	/*** PUBLIC FUNCTIONS ***/

	function __construct($id = null)
	{
		if(is_numeric($id))
		{
			$this->set_id($id);
			$this->get(true);
		}
	}

	/**
	 * @param $data
	 * @return $this
	 */
	public function set($data)
	{
		foreach($data as $key=>$item)
		{
			if(property_exists($this,$key))
			{
				$this->{$key} = $item;
			}
		}

		return $this;
	}

	/**
	 * @param $id
	 * @return $this
	 */
	public function set_id($id)
	{
		$this->id = is_numeric($id) ? intval($id) : $this->id;
		return $this;
	}

	/**
	 * @return null
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_title()
	{
		return $this->title;
	}

	/**
	 * Save this instance to the database. Insert new row or overwrite existing.
	 * @return bool
	 */
	public function save()
	{
		$ok = TRUE;
		self::_lock_for_write();

		Database::instance()->begin();
		try
		{
			$logged_in_user      = Auth::instance()->get_user();
			$this->modified_by   = $logged_in_user['id'];
			$this->date_modified = date('Y-m-d H:i:s');

			if (is_numeric($this->id))
			{
				$this->_sql_update();
			}
			else
			{
				$this->created_by    = $this->modified_by;
				$this->date_created  = $this->date_modified;

				$q = $this->_sql_insert();
				$this->set_id($q[0]);
			}
			Database::instance()->commit();
		}
		catch(Exception $e)
		{
			$ok = FALSE;
			self::_write_error(array('Save' => $e->getMessage()));
			Log::instance()->add(Log::ERROR,'Unable to save category. \n'.$e->getMessage())->write();
			Database::instance()->rollback();
			throw $e;
		}

		self::_unlock_tables();
		return $ok;
	}

	public function toggle_publish()
	{
		$this->publish = ($this->publish == 1) ? 0 : 1;
		$this->save();
		return $this->publish;
	}

	public function delete()
	{
		$this->deleted = 1;
		$this->publish = 0;
		return $this->save();
	}

	/**
	 * @param $autoload
	 * @return array
	 */
	public function get($autoload)
	{
		$data = $this->_sql_get();

		if ($autoload)
		{
			$this->set($data);
		}

		return $data;
	}

	/**
	 * @return bool
	 */
	public function validate()
	{
		return true;
	}

	/**
	 * @return array
	 */
	public function get_instance()
	{
		return array(
			'id'            => $this->id,
			'title'         => $this->title,
			'order'         => $this->order,
			'description'   => $this->description,
			'summary'       => $this->summary,
			'date_created'  => $this->date_created,
			'date_modified' => $this->date_modified,
			'created_by'    => $this->created_by,
			'modified_by'   => $this->modified_by,
			'publish'       => $this->publish,
			'deleted'       => $this->deleted
		);
	}



	/*** PRIVATE FUNCTIONS ***/

	private function _sql_get()
	{
		self::_lock_for_read();
		$q = DB::select_array(array_keys($this->get_instance()))->from(self::MAIN_TABLE)->where('id','=',$this->id)->where('deleted','=',0)->execute()->as_array();
		self::_unlock_tables();

		return (count($q) > 0) ? $q[0] : array();
	}

	/**
	 *
	 */
	private function _sql_update()
	{
		DB::update(self::MAIN_TABLE)->set($this->get_instance())->where(self::ID_COLUMN,'=',$this->id)->execute();
	}

	/**
	 * @return object
	 */
	private function _sql_insert()
	{
		return DB::insert(self::MAIN_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
	}

	/*** PUBLIC STATIC FUNCTIONS ***/

	public static function create($id = null)
	{
		return new self($id);
	}

	/**
	 * @return mixed
	 */
	public static function get_all()
	{
		self::_lock_for_read();

		$q = DB::select()->from(self::MAIN_TABLE)->where('deleted','=',0)->execute()->as_array();

		self::_unlock_tables();

		return $q;
	}

	/*** PRIVATE STATIC FUNCTIONS ***/

	/**
	 *  Lock tables for writing.
	 */
	private static function _lock_for_write()
	{
		DB::query(null,'SET AUTOCOMMIT = 0')->execute();
		DB::query(null,'LOCK TABLES '.self::MAIN_TABLE.' WRITE')->execute();
	}

	/**
	 *  Unlock tables.
	 */
	private static function _unlock_tables()
	{
		DB::query(null,'UNLOCK TABLES')->execute();
		DB::query(null,'SET AUTOCOMMIT = 1')->execute();
	}

	/**
	 *  Lock tables for reading.
	 */
	private static function _lock_for_read()
	{
		DB::query(null,'SET AUTOCOMMIT = 0')->execute();
		DB::query(null,'LOCK TABLES '.self::MAIN_TABLE.' READ')->execute();
	}

	/**
	 * @param $error
	 */
	private static function _write_error($error)
	{
		self::$errors[] = $error;
	}
}
?>