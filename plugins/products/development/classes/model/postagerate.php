<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_PostageRate extends Model
{
    // Tables
    const MAIN_TABLE = 'plugin_products_postage_rate';

    // Fields
    private $main_table;

    /**
     * The constructor for this class.
     * @param null $id The identifier of the object to load.
     * @throws Exception
     */
    public function __construct($id = NULL)
    {
        $this->main_table = $this->get_table_columns(self::MAIN_TABLE);

        if (isset($id))
        {
            if ( ! $this->load($id) )
                throw new Exception(get_class().': Unable to initialize the class.');
        }
    }

    /**
     * Return an associative array with the data.
     * @return array The associative array.
     */
    public function get_data()
    {
        return $this->main_table;
    }

    /**
     * Set the data for this object.
     * @param array $data An associative array containing the data.
     */
    public function set_data($data)
    {
        foreach ($data as $field => $value)
        {
            if (array_key_exists($field, $this->main_table))
            {
                $this->main_table[$field] = is_string($value) ? trim($value) : $value;
            }
        }
    }

    /**
     * Save the object.
     * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
     * @throws Exception
     */
    public function save()
    {
        $ok = FALSE;
        $db = Database::instance();

        if ( ! is_null($db) AND $this->check_for_save() AND $db->begin() )
        {
            try
            {
                if ($this->main_table['id'] == NULL)
                {
                    $ok = self::sql_insert_object($this->build_insert_array());
                }
                else
                {
                    $ok = self::sql_update_object($this->main_table['id'], $this->build_update_array());
                }

                if ($ok !== FALSE)
                {
                    $ok = $db->commit();
                }
                else
                    throw new Exception();
            }
            catch (Exception $e)
            {
                Log::instance()->add(Log::ERROR, $e->getTraceAsString());

                $db->rollback();
            }
        }

        return $ok;
    }

    //
    // STATIC/SERVICE FUNCTIONS (DO NOT ABUSE OF THEM)
    //

    /**
     * Return an array of associative arrays with all the non deleted objects.
     * @return array The array of associative arrays.
     */
    public static function get_all()
    {
        return self::get();
    }

    /**
     * Mark the specified object as deleted.
     * @param int $id The object identifier.
     * @param array $where_clauses An optional array with where clauses (only AND). This is an array in this way: array(array($field, $condition, $value),...)
     * @return array If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
     */
    public static function get($id = NULL, $where_clauses = NULL)
    {
        $q = DB::select('t1.id', 't1.country_id', array(DB::expr('COALESCE(`country`.`name`, "All")'), 'country'), 't1.format_id', array(DB::expr('COALESCE(`t2`.`title`, "All")'), 'format'), array('t3.id', 'zone_id'), array('t3.title', 'zone'), 'weight_from', 'weight_to', 'price', 't1.publish', 't1.date_modified', 't1.date_entered', 't1.modified_by', 't1.created_by')
			->from(array(self::MAIN_TABLE, 't1'))
			->join(array('engine_countries',         'country'), 'LEFT')->on('t1.country_id', '=', 'country.id')
			->join(array(Model_PostageFormat::MAIN_TABLE, 't2'), 'LEFT')->on('t1.format_id',  '=', 't2.id')
			->join(array(Model_PostageZone  ::MAIN_TABLE, 't3'), 'LEFT')->on('t1.zone_id'  ,  '=', 't3.id')
			->where('t1.deleted', '=', 0)
			->order_by('t1.id')
			->order_by('weight_from');

        if ($id != NULL)
        {
            $q->where('t1.id', '=', $id);
        }

        if ($where_clauses != NULL)
        {
            for ($i = 0; $i < count($where_clauses); $i++)
            {
                $clause = $where_clauses[$i];

                // If the table name is not specified, assume the main table
                $clause[0] = (is_string($clause[0]) AND strpos($clause[0], '.') == FALSE) ? 't1.'.$clause[0] : $clause[0];

                $q->where($clause[0], $clause[1], $clause[2]);
            }
        }

        $r = $q->execute()->as_array();

		if ($where_clauses AND count($r) > 1)
		{
			// If a rate applying to all countries and a rate applying a specific country is found, just use the rate for the  specific country
			$all_country_rates = array();
			foreach ($r as $key => $rate)
			{
				if ($rate['country_id'] == 0) $all_country_rates[] = $key;
			}
			if (count($all_country_rates) < count($r))
			{
				foreach ($all_country_rates as $i) unset($r[$i]);
			}
		}

        return ($id == NULL OR count($r) == 0) ? array_values($r) : reset($r);
    }

    /**
     * Mark the specified object as deleted.
     * @param int $id The object identifier.
     * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
     */
    public static function delete_object($id)
    {
        $ok = FALSE;

        try
        {
            $r = DB::update(self::MAIN_TABLE)
                     ->set(array('deleted' => 1))
                     ->where('id', '=', $id)
                     ->execute();

            $ok = ($r > 0);
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());
        }

        return $ok;
    }

    /**
     * Toggle the publish option of the specified object.
     * @param int $id The object identifier.
     * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
     */
    public static function toggle_publish_option($id)
    {
        $ok = FALSE;

        try
        {
            $r = DB::select('publish')
                     ->from(self::MAIN_TABLE)
                     ->where('id', '=', $id)
                     ->execute()
                     ->as_array();

            if (count($r) == 1)
            {
                $publish = ($r[0]['publish'] == 1) ? 0 : 1;

                $r = DB::update(self::MAIN_TABLE)
                         ->set(array('publish' => $publish))
                         ->where('id', '=', $id)
                         ->execute();

                $ok = ($r == 1);
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());
        }

        return $ok;
    }

    //
    // PRIVATE FUNCTIONS
    //

    /**
     * Return an array with the name of all the columns the specified table.
     * @param string $table The table name.
     * @return array The array.
     */
    private function get_table_columns($table)
    {
        $array   = array();
        $columns = Database::instance()->list_columns($table);

        foreach ($columns as $column => $description)
        {
            $array[$column] = NULL;
        }

        return $array;
    }

    /**
     * Load an object.
     * @param int $id The object identifier.
     * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
     */
    private function load($id)
    {
        $data = self::sql_get_object($id);

        if ($data != FALSE)
        {
            $this->set_data((array) $data);
        }

        return ($data != FALSE);
    }

    /**
     * Check if the data of this object is valid.
     * @return bool It the data is valid, this function returns TRUE. Otherwise, it returns FALSE.
     */
    private function check_for_save()
    {
        $ok = TRUE;

        $ok = ( $ok AND (strlen($this->main_table['format_id']) > 0) AND ctype_digit($this->main_table['format_id']));
        $ok = ( $ok AND (strlen($this->main_table['zone_id'  ]) > 0) AND ctype_digit($this->main_table['zone_id'  ]));

        return $ok;
    }

    /**
     * Return an array ready to be used in an INSERT statement.
     * @return array The array.
     */
    private function build_insert_array()
    {
        $logged_user = Auth::instance()->get_user();

        $array = array
        (
            'date_entered' => date('Y-m-d H:i:s', time()),
            'created_by'   => $logged_user['id'],
        );

        $this->set_common_values ($array);
        $this->set_default_values($array);

        return $array;
    }

    /**
     * Return an array ready to be used in an UPDATE statement.
     * @return array The array.
     */
    private function build_update_array()
    {
        $logged_user = Auth::instance()->get_user();

        $array = array
        (
            'modified_by' => $logged_user['id'],
        );

        $this->set_common_values ($array);
        $this->set_default_values($array);

        return $array;
    }

    /**
     * Set the common values (for the INSERT and UPDATE statements) for this object.
     * @param array $array The array.
     */
    private function set_common_values(&$array)
    {
		$array['country_id' ] = $this->main_table['country_id' ];
        $array['format_id'  ] = $this->main_table['format_id'  ];
        $array['zone_id'    ] = $this->main_table['zone_id'    ];
        $array['weight_from'] = $this->main_table['weight_from'];
        $array['weight_to'  ] = $this->main_table['weight_to'  ];
        $array['price'      ] = $this->main_table['price'      ];
        $array['publish'    ] = $this->main_table['publish'    ];
    }

    /**
     * Set the default values (for the INSERT and UPDATE statements) for this object.
     * @param $array
     */
    private function set_default_values(&$array)
    {
        $array['publish'] = ($array['publish'] == '') ? 1 : $array['publish'];
    }

    //
    // SQL FUNCTIONS
    //

    /**
     * Return an associative array with the data of the specified object identifier.
     * @param int $id The identifier of the object.
     * @return array|bool The array if the object is present in the database. Otherwise, it returns FALSE.
     */
    private static function sql_get_object($id)
    {
        $r = DB::select('id', 'country_id', 'format_id', 'zone_id', 'weight_from', 'weight_to', 'price', 'publish', 'deleted', 'date_modified', 'date_entered', 'modified_by', 'created_by')
                 ->from(self::MAIN_TABLE)
                 ->where('id', '=', $id)
                 ->execute()
                 ->as_array();

        return (count($r) > 0) ? $r[0] : FALSE;
    }

    /**
     * Insert a new object.
     * @param array $insert_array The insert array.
     * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
     */
    private static function sql_insert_object($insert_array)
    {
        $r = DB::insert(self::MAIN_TABLE, array_keys($insert_array))
                 ->values(array_values($insert_array))
                 ->execute();

        return ($r[1] == 1) ? $r[0] : FALSE;
    }

    /**
     * Update the data of an existing object.
     * @param int $id The identifier of the object.
     * @param array $update_array The update array.
     * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
     */
    private static function sql_update_object($id, $update_array)
    {
        $r = DB::update(self::MAIN_TABLE)
                 ->set($update_array)
                 ->where('id', '=', $id)
                 ->execute();

        return ($r >= 0);
    }
}
