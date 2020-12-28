<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Cardorder extends Model
{
	/* Private member data */
	private $id            = NULL;
	private $printed       = 0;
	private $created_by    = NULL;
	private $modified_by   = NULL;
	private $date_created  = NULL;
	private $date_modified = NULL;
	private $deleted       = 0;

	public function __construct($id = NULL)
	{
		if ( ! is_null($id) AND is_numeric($id))
		{
			$this->set_id($id);
			$this->get(true);
		}
	}

	/**
	 * Dynamically set member data.
	 * @param $data
	 */
	public function set($data)
	{
		foreach($data AS $key => $value)
		{
			if (property_exists($this, $key))
			{
				$this->{$key} = $value;
			}
		}
		return $this;
	}

	/**
	 * Return data from database for this order.
	 * @param  $autoload
	 * @return array
	 */
	public function get($autoload = FALSE)
	{
		$data = $this->get_details();
		if ($autoload)
		{
			$this->set($data);
		}
		return $data;
	}

	public function get_instance()
	{
		return array(
			'id'            => $this->id,
			'printed'       => $this->printed,
			'created_by'    => $this->created_by,
			'modified_by'   => $this->modified_by,
			'date_created'  => $this->date_created,
			'date_modified' => $this->date_modified,
			'deleted'       => $this->deleted
		);
	}

	public function get_id()
	{
		return $this->id;
	}

	/**
	 * @param array $value
	 * @return $this
	 */
	public function set_id($value = NULL)
	{
		$this->id = is_numeric($value) ? intval($value) : $this->id;
		return $this;
	}
	public function set_printed($value = NULL)
	{
		$this->printed = is_numeric($value) ? intval($value) : $this->printed;
		return $this;
	}
	public function set_modified_by($value = NULL)
	{
		$this->modified_by = $value;
		return $this;
	}
	public function set_deleted($value = NULL)
	{
		$this->deleted = is_numeric($value) ? intval($value) : $this->deleted;
	}

	public function update_date_modified()
	{
		$this->date_modified = date('Y-m-d H:i:s');
		return $this;
	}

	public function validate()
	{
		return TRUE;
	}

	public function add()
	{
		// Set the logged-in user as the first and most recent editor
		// Set now as the first and most recent edit dates
		$logged_in_user      = Auth::instance()->get_user();
		$this->created_by    = $logged_in_user['id'];
		$this->modified_by   = $logged_in_user['id'];
		$this->date_created  = date('Y-m-d H:i:s');
		$this->date_modified = date('Y-m-d H:i:s');
		return $this->sql_insert();
	}

	public function save()
	{
		// Set the logged-in user as the most recent editor
		// Set now as the most recent edit date
		$logged_in_user      = Auth::instance()->get_user();
		$this->modified_by   = $logged_in_user['id'];
		$this->date_modified = date('Y-m-d H:i:s');
		return $this->sql_update();
	}

	public function delete()
	{
		$this->set_deleted(1);
		return $this->save();
	}

	/*
	 * Private functions
	 */
	private function get_details()
	{
		return $this->sql_get_details();
	}

	private function sql_insert()
	{
		$q = DB::insert('plugin_cardbuilder_orders',array_keys($this->get_instance()))->values($this->get_instance())->execute();
		$this->set_id($q[0]);
		return $q[0];
	}

	private function sql_get_details()
	{
		return DB::select_array(array_keys($this->get_instance()))
			->from('plugin_cardbuilder_orders')
			->where('id', '=', $this->id)
			->execute()
			->current();
	}

	private function sql_update()
	{
		return DB::update('plugin_cardbuilder_orders')->set($this->get_instance())->where('id','=',$this->id)->execute();
	}

	/*
	 * Public static functions
	 */
	public static function get_all()
	{
		return DB::select()->from('plugin_cardbuilder_orders')->where('deleted','=',0)->execute()->as_array();
	}

}