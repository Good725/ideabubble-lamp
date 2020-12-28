<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 27/11/2014
 * Time: 12:16
 */
class Model_Cars extends Model implements Interface_Ideabubble
{
    /*** CLASS CONSTANTS ***/
    const CARS_TABLE        = 'plugin_cars_cars';
    const ID_COLUMN         = 'id';
    const EDIT_ACTION       = '/admin/cars/add_edit_car/';
    const NISSAN_CSV_URL    = 'http://test/test.csv';

    /*** PRIVATE MEMBER DATA ***/
    private $id                 = null;
    private $title              = '';
    private $category_id        = null;
    private $publish            = 1;
    private $import_overwrite   = 1;
    private $make               = '';
    private $model              = '';
    private $price              = 0;
    private $engine             = '';
    private $body_type          = '';
    private $transmission       = '';
    private $year               = '';
    private $color              = '';
    private $mileage            = '';
    private $no_of_owners       = '';
    private $location           = '';
    private $doors              = 0;
    private $nct_expiry         = '';
    private $type               = '';
    private $odometer           = '';
    private $no_of_seats        = 0;
    private $extra              = '';
    private $comments           = '';
    private $additional_info    = '';
	private $dealer_id          = NULL;
	private $dealer_domain      = '';
	private $description        = '';
	private $stock              = '';
	private $is_kilometer       = NULL;
	private $options            = '';
	private $fuel               = '';
	private $photo              = '';
	private $category           = '';
    private $seo_title          = '';
    private $seo_keywords       = '';
    private $seo_description    = '';
    private $seo_footer         = '';
    private static $errors      = array();

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
            if(is_numeric($this->id))
            {
                $this->_sql_update_car();
            }
            else
            {
                $q = $this->_sql_insert_car();
                $this->set_id($q[0]);
            }
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            $ok = FALSE;
            self::_write_error(array('Save' => $e->getMessage()));
            Log::instance()->add(Log::ERROR,'Unable to save Vehicle. \n'.$e->getMessage())->write();
            Database::instance()->rollback();
            throw $e;
        }

        self::_unlock_tables();
        return $ok;
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
        $data = $this->_sql_get_car();

        if($autoload)
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
        return array('id' => $this->id,
			'title' => $this->title,
			'category_id' => $this->category_id,
			'publish' => $this->publish,
			'import_overwrite' => $this->import_overwrite,
			'make' => $this->make,
			'model' => $this->model,
			'price' => $this->price,
			'engine' => $this->engine,
			'body_type' => $this->body_type,
			'transmission' => $this->transmission,
			'year' => $this->year,
			'color' => $this->color,
			'mileage' => $this->mileage,
			'no_of_owners' => $this->no_of_owners,
			'location' => $this->location,
			'doors' => $this->doors,
			'nct_expiry' => $this->nct_expiry,
			'type' => $this->type,
			'odometer' => $this->odometer,
			'no_of_seats' => $this->no_of_seats,
			'extra' => $this->extra,
			'comments' => $this->comments,
			'additional_info' => $this->additional_info,
			'dealer_id' => $this->dealer_id,
			'dealer_domain' => $this->dealer_domain,
			'description' => $this->description,
			'stock' => $this->stock,
			'is_kilometer' => $this->is_kilometer,
			'options' => $this->options,
			'fuel' => $this->fuel,
			'photo' => $this->photo,
			'category' => $this->category
        );
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function get_publish()
    {
        return $this->publish;
    }

    /**
     * @return int
     */
    public function get_import_overwrite()
    {
        return $this->import_overwrite;
    }

    /**
     * @return string
     */
    public function get_make()
    {
        return $this->make;
    }

    /**
     * @return string
     */
    public function get_model()
    {
        return $this->model;
    }

    /**
     * @return int
     */
    public function get_price()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function get_engine()
    {
        return $this->engine;
    }

    /**
     * @return string
     */
    public function get_body_type()
    {
        return $this->body_type;
    }

    /**
     * @return string
     */
    public function get_transmission()
    {
        return $this->transmission;
    }

    /**
     * @return string
     */
    public function get_year()
    {
        return $this->year;
    }

    /**
     * @return string
     */
    public function get_color()
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function get_mileage()
    {
        return $this->mileage;
    }

    /**
     * @return string
     */
    public function get_no_of_owners()
    {
        return $this->no_of_owners;
    }

    /**
     * @return string
     */
    public function get_location()
    {
        return $this->location;
    }

    /**
     * @return int
     */
    public function get_doors()
    {
        return $this->doors;
    }

    /**
     * @return string
     */
    public function get_nct_expiry_date()
    {
        return $this->nct_expiry;
    }

    /**
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function get_odometer()
    {
        return $this->odometer;
    }

    /**
     * @return string
     */
    public function get_no_of_seats()
    {
        return $this->no_of_owners;
    }

    /**
     * @return string
     */
    public function get_extra()
    {
        return $this->extra;
    }

    /**
     * @return string
     */
    public function get_additional_info()
    {
        return $this->additional_info;
    }

    /**
     * @return string
     */
    public function get_comments()
    {
        return $this->comments;
    }

	/**
	 * @return string
	 */
	public function dealer_id()
	{
		return $this->dealer_id;
	}

	/**
	 * @return string
	 */
	public function dealer_domain()
	{
		return $this->dealer_domain;
	}

	/**
	 * @return string
	 */
	public function get_description()
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function get_stock()
	{
		return $this->stock;
	}

	/**
	 * @return string
	 */
	public function get_is_kilometer()
	{
		return $this->is_kilometer;
	}

	/**
	 * @return string
	 */
	public function get_options()
	{
		return $this->options;
	}

	/**
	 * @return string
	 */
	public function get_fuel()
	{
		return $this->fuel;
	}

	/**
	 * @return string
	 */
	public function get_photo()
	{
		return $this->photo;
	}

	/**
	 * @return array
	 */
	public function get_photos()
	{
		return explode(',', $this->photo);
	}

	/**
	 * @return string
	 */
	public function get_category()
	{
		return $this->category;
	}

	public function get_category_id()
	{
		return $this->category_id;
	}

    /**
     * @return string
     */
    public function get_seo_title()
    {
        return $this->seo_title;
    }

    /**
     * @return string
     */
    public function get_seo_keywords()
    {
        return $this->seo_keywords;
    }

    /**
     * @return string
     */
    public function get_seo_description()
    {
        return $this->seo_description;
    }

    /**
     * @return string
     */
    public function get_seo_footer()
    {
        return $this->seo_footer;
    }

    /*** PRIVATE FUNCTIONS ***/

    private function _sql_get_car()
    {
        self::_lock_for_read();
        $q = DB::select_array(array_keys($this->get_instance()))->from(self::CARS_TABLE)->where('id','=',$this->id)->execute()->as_array();
        self::_unlock_tables();

        return count($q) > 0 ? $q[0] : array();
    }

    /**
     *
     */
    private function _sql_update_car()
    {
        DB::update(self::CARS_TABLE)->set($this->get_instance())->where(self::ID_COLUMN,'=',$this->id)->execute();
    }

    /**
     * @return object
     */
    private function _sql_insert_car()
    {
        return DB::insert(self::CARS_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
    }

    /*** PUBLIC STATIC FUNCTIONS ***/

    public static function create($id = null)
    {
        $car = new self($id);
		$car->import_overwrite = 0;
		return $car;
    }

    public static function download_csv()
    {
        $lines = array();
        $row = 1;
        if(($handle = fopen(self::get_car_csv_url(), "r")) !== FALSE)
        {
            while(($data = fgetcsv($handle, null, ",")) !== FALSE)
            {
                $row++;
                $lines[] = $data;
            }
            fclose($handle);
        }
        $head = array_shift($lines);
        return array('data' => array_values($lines),'head' => $head);
    }

    /**
     * @return mixed
     */
    public static function get_all_cars()
    {
        self::_lock_for_read();

        $q = DB::select()->from(self::CARS_TABLE)->execute()->as_array();

        self::_unlock_tables();

        return $q;
    }

	public static function get_search_results($filters, $limit = 10, $offset = 0)
	{
		$q = DB::select()->from(self::CARS_TABLE);

		// Make
		if (isset($filters['make']) AND $filters['make'] != '')
		{
			$q = $q->where('make', '=', $filters['make']);
		}

		// Model
		if (isset($filters['model']) AND $filters['model'] != '')
		{
			$q = $q->where('model', '=', $filters['model']);
		}

		// Fuel
		if (isset($filters['fuel']) AND $filters['fuel'] != '')
		{
			$q = $q->where('fuel', '=', $filters['fuel']);
		}

		// Year
		if (isset($filters['min_year']) AND $filters['min_year'] != '')
		{
			$q = $q->where('year', '>=', $filters['min_year']);
		}
		if (isset($filters['max_year']) AND $filters['max_year'] != '')
		{
			$q = $q->where('year', '<=', $filters['max_year']);
		}

		// Price
		if (isset($filters['min_price']) AND $filters['min_price'] != '')
		{
			$q = $q->where('price', '>=', $filters['min_price']);
		}
		if (isset($filters['max_price']) AND $filters['max_price'] != '')
		{
			$q = $q->where('price', '<=', $filters['max_price']);
		}

		$q = $q
			->where('publish', '=', 1)
			->and_where_open()
				->where('delete', '=', 0)
				->or_where('delete', 'IS', NULL)
			->and_where_close()
			->order_by('id')
			->limit($limit)
			->offset($offset)
			->execute()
			->as_array();

		return $q;
	}

	public static function count_search_results($filters)
	{
		$q = DB::select(array(DB::expr('count(*)'),'count'))->from(self::CARS_TABLE);

		if (isset($filters['make']) AND $filters['make'] != '')
		{
			$q = $q->where('make', '=', $filters['make']);
		}

		if (isset($filters['model']) AND $filters['model'] != '')
		{
			$q = $q->where('model', '=', $filters['model']);
		}

		if (isset($filters['fuel']) AND $filters['fuel'] != '')
		{
			$q = $q->where('fuel', '=', $filters['fuel']);
		}

		$q = $q
			->where('publish', '=', 1)
			->and_where_open()
				->where('delete', '=', 0)
				->or_where('delete', 'IS', NULL)
			->and_where_close()
			->order_by('id')
			->execute()
			->get('count', 0);

		return $q;
	}


	public static function get_all_makes()
	{
		self::_lock_for_read();
		$q = DB::select('make')
			->from(self::CARS_TABLE)
			->distinct(TRUE)
			->where('make', '!=', '')
			->where('publish', '=', 1)
			->and_where_open()
                ->where('delete', '=', 0)
                ->or_where('delete', 'IS', NULL)
			->and_where_close()
			->execute()->as_array();
		self::_unlock_tables();

		return $q;
	}

	public static function get_all_models()
	{
		self::_lock_for_read();
		$q = DB::select('model')
			->from(self::CARS_TABLE)
			->distinct(TRUE)
			->where('model', '!=', '')
			->where('publish', '=', 1)
			->and_where_open()
                ->where('delete', '=', 0)
                ->or_where('delete', 'IS', NULL)
			->and_where_close()
			->execute()->as_array();
		self::_unlock_tables();

		return $q;
	}

	public static function get_models_by_make($make)
	{
		self::_lock_for_read();
		$q = DB::select('model')
			->from(self::CARS_TABLE)
			->distinct(TRUE)
			->where('model', '!=', '')
			->where('publish', '=', 1)
			->and_where_open()
				->where('delete', '=', 0)
				->or_where('delete', 'IS', NULL)
			->and_where_close();
		if ($make != '')
		{
			$q = $q->where('make','=', $make);
		}
		$q = $q->execute()->as_array();
		self::_unlock_tables();

		return $q;
	}

	public static function get_all_fuels()
	{
		self::_lock_for_read();
		$q = DB::select('fuel')
			->from(self::CARS_TABLE)
			->distinct(TRUE)
			->where('fuel', '!=', '')
			->where('publish', '=', 1)
			->and_where_open()
				->where('delete', '=', 0)
				->or_where('delete', 'IS', NULL)
			->and_where_close()
			->execute()->as_array();
		self::_unlock_tables();

		return $q;
	}

    public static function get_car_csv_url()
    {
        return Settings::instance()->get('car_csv_url') == null ? self::NISSAN_CSV_URL : Settings::instance()->get('car_csv_url');
    }

    /*** PRIVATE STATIC FUNCTIONS ***/

    /**
     *  Lock tables for writing.
     */
    private static function _lock_for_write()
    {
        DB::query(null,'SET AUTOCOMMIT = 0')->execute();
        DB::query(null,'LOCK TABLES '.self::CARS_TABLE.' WRITE')->execute();
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
        DB::query(null,'LOCK TABLES '.self::CARS_TABLE.' READ')->execute();
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