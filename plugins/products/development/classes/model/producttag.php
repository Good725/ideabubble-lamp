<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_ProductTag extends Model
{
	/* Private member data */
	private $id            = NULL;
	private $title         = NULL;
	private $description   = '';
	private $information   = '';
	private $order         = '';
	private $created_by    = NULL;
	private $modified_by   = NULL;
	private $date_created  = NULL;
	private $date_modified = NULL;
	private $publish       = 1;
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
	 * Return data from database for this card.
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
			'title'         => $this->title,
			'description'   => $this->description,
			'information'   => $this->information,
			'order'         => $this->order,
			'created_by'    => $this->created_by,
			'modified_by'   => $this->modified_by,
			'date_created'  => $this->date_created,
			'date_modified' => $this->date_modified,
			'publish'       => $this->publish,
			'deleted'       => $this->deleted
		);
	}

	public function get_id()
	{
		return $this->id;
	}

	public function get_title()
	{
		return $this->title;
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

	public function set_title($value = NULL)
	{
		$this->title = $value;
		return $this;
	}

	public function set_description($value = NULL)
	{
		$this->description = $value;
		return $this;
	}

	public function set_information($value = NULL)
	{
		$this->information = $value;
		return $this;
	}

	public function set_order($value = NULL)
	{
		$this->order = $value;
		return $this;
	}

	public function set_modified_by($value = NULL)
	{
		$this->modified_by = $value;
		return $this;
	}

	public function set_publish($value = NULL)
	{
		$this->publish = is_numeric($value) ? intval($value) : $this->publish;
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

	public function save()
	{
		$valid = $this->validate();
		if ($valid == TRUE)
		{
			if (is_numeric($this->id))
			{
				return self::update();
			}
			else
			{
				return self::add();
			}
		}
		else
		{
			return $valid;
		}
	}


	private function add()
	{
		// Set the logged-in user as the first and most recent editor
		// Set now as the first and most recent edit dates
		$logged_in_user      = Auth::instance()->get_user();
		$this->created_by    = $logged_in_user['id'];
		$this->modified_by   = $this->created_by;
		$this->date_created  = date('Y-m-d H:i:s');
		$this->date_modified = $this->date_created;
		return $this->sql_insert();
	}

	private function update()
	{
		// Set the logged-in user as the most recent editor
		// Set now as the most recent edit date
		$logged_in_user      = Auth::instance()->get_user();
		$this->modified_by   = $logged_in_user['id'];
		$this->date_modified = date('Y-m-d H:i:s');
		return $this->sql_update();
	}

	public function toggle_publish()
	{
		$this->publish = ($this->publish == 1) ? 0 : 1;
		$this->save();
		return $this->publish;
	}

	public function delete()
	{
		$this->set_deleted(1);
		$this->set_publish(0);
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
		$q = DB::insert('plugin_products_tags',array_keys($this->get_instance()))
			->values($this->get_instance())
			->execute();
		$this->set_id($q[0]);
		return $q[0];
	}

	private function sql_get_details()
	{
		return DB::select_array(array_keys($this->get_instance()))
			->from('plugin_products_tags')
			->where('id', '=', $this->id)
			->execute()
			->current();
	}

	private function sql_update()
	{
		return DB::update('plugin_products_tags')->set($this->get_instance())->where('id','=',$this->id)->execute();
	}

	public static function get_all()
	{
		return DB::select()->from('plugin_products_tags')->where('deleted','=',0)->execute()->as_array();
	}

	public static function get_all_for_product($id,$published_only = FALSE)
	{
		$query = DB::select('tag.*')
			->from(array('plugin_products_tags','tag'))
			->join(array('plugin_products_product_tags','product_tag'))->on('product_tag.tag_id', '=', 'tag.id')
			->where('product_tag.product_id','=', $id)
			->and_where('tag.deleted', '=', 0)
			->order_by('tag.title');

		if ($published_only)
		{
			$query->and_where('tag.publish','=',1);
		}

		return $query
			->execute()
			->as_array();
	}

	// Get all published tags, whose name contains a specified string
	public static function get_all_like($like)
	{
		return DB::select()
			->from('plugin_products_tags')
			->where('title', 'like', '%'.$like.'%')
			->and_where('publish', '=', 1)
			->and_where('deleted','=',0)
			->order_by('title')
			->execute()->as_array();
	}


	static function get_products_for_autocomplete($like)
	{
		$tags =  DB::select()
			->from('plugin_products_tags')
			->and_where('publish', '=', 1)
			->and_where('deleted','=',0)
			->order_by('title');

		$query = DB::select()
			->from(array('plugin_products_product','product'))
			->join(array('plugin_products_product_tags','product_tag'),'left')->on('product_tag.product_id','=','product.id')
			->join(array($tags,'tag'),'left')->on('product_tag.tag_id','=','tag.id')
			->where('tag.title','like','%'.$like.'%')
			->where('product.publish','=',1)
			->where('product.deleted','=', 0);


		$count = clone $query;

		$return['results'] = $query->select('product.id', 'product.title',array('tag.id','tag_id'),array('tag.title','tag'))->order_by('tag.title')->limit(5)->execute()->as_array();
		$return['count']   = $count->select(array(DB::expr('count(*)'), 'count'))->execute()->get('count', 0);
		return $return;
	}


}