<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Location extends ORM
{
    // Tables
    const MAIN_TABLE          = 'plugin_locations_location';

    // Default Type
    const DEFAULT_TYPE        = 'Default';

    // Fields
    private $main_table;

    // ORM properties
    protected $_table_name = self::MAIN_TABLE;
    protected $_date_created_column = 'date_entered';
    protected $_belongs_to = [
        'city' => ['model' => 'locations_city']
    ];

    /**
     * The constructor for this class.
     * @param null $id The identifier of the object to load.
     * @throws Exception
     */
    public function __construct($id = NULL)
    {
        parent::__construct();

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
    public function save_location()
    {
        $ok = FALSE;
        $db = Database::instance();

        if ( ! is_null($db) AND $this->validate_for_save() AND $db->begin() )
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
     * Return an array of associative arrays with all the non deleted and published objects.
     * @return array The array of associative arrays.
     */
    public static function get_all_published()
    {
        return self::get(NULL, array(array('publish', '=', '1')));
    }

    /**
     * If the object identifier is NULL, return an array of associative arrays with all the non deleted objects. Otherwise, return an associative array with the specified object.
     * @param int $id The object identifier or NULL to retrieve all the objects.
     * @param array $where_clauses An optional array with where clauses (only AND). This is an array in this way: array(array($field, $condition, $value),...)
     * @return array An array of associative arrays if the object identifier is NULL or an associative array, if the object identifier is not NULL.
     */
    public static function get($id = NULL, $where_clauses = NULL)
    {
        $q = DB::select('id', 'title', 'type', 'address_1', 'address_2', 'address_3', 'county', 'phone', 'email', 'map_reference', 'publish', 'deleted', 'date_modified', 'date_entered', 'modified_by', 'created_by')
                 ->from(self::MAIN_TABLE)
                 ->where('deleted', '=', 0)
                 ->order_by('id');

        if ($id != NULL)
        {
            $q->where('id', '=', $id);
        }

        if ($where_clauses != NULL)
        {
            for ($i = 0; $i < count($where_clauses); $i++)
            {
                $clause = $where_clauses[$i];

                $q->where($clause[0], $clause[1], $clause[2]);
            }
        }

        $r = $q->execute()->as_array();

        return ($id == NULL OR count($r) == 0) ? $r : $r[0];
    }

    public function get_note($column = null)
    {
        if ($this->id) {
            $notes = Model_Notes::search(['type' => 'Logistic transfer', 'reference_id' => $this->id]);
        }

        $note = (!empty($notes)) ? $notes[0] : ['id' => null, 'note' => null];

        if ($column) {
            return isset($note[$column]) ? $note[$column] : null;
        } else {
            return $note;
        }
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

    /**
     * Return an array with all the types.
     * @return array The array.
     */
    public static function get_all_types()
    {
        $r = DB::select('type')
                 ->distinct('type')
                 ->from(self::MAIN_TABLE)
                 ->where('deleted', '=', 0)
                 ->order_by('type')
                 ->execute()
                 ->as_array();

        for ($i = 0, $list = array(); $i < count($r); $i++)
        {
            array_push($list, $r[$i]['type']);
        }

        return $list;
    }

    public static function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'location.id',
            'location.title',
            'city.name',
            'location.county',
            'location.date_modified',
            ''
        ];

        $model = new Model_Location();
        $query = $model
            ->join(['plugin_locations_cities', 'city'], 'left')->on('location.city_id', '=', 'city.id')
            ->order_by('location.date_modified', 'desc')
            ->where('location.deleted', '=', 0)
            ->apply_datatable_args($datatable_args, $column_definitions);

        // Count all records, ignoring limit and offset
        $query->reset(false);
        $count_all = $query->count_all();

        $results = $query->find_all();
        $results->_count_all = $count_all;

        $rows = [];
        foreach ($results as $result) {
            $rows[] = [
                $result->id,
                htmlentities($result->title),
                htmlentities($result->city->name),
                htmlentities($result->county),
                IbHelpers::relative_time_with_tooltip($result->date_modified),
                '<div class="action-btn">
                    <a class="btn" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="icon-ellipsis-h" aria-hidden="true"></span>
                    </a>
                    <ul class="dropdown-menu">
                       <li><button type="button" class="edit-link location-modal-toggle" data-id="'.$result->id.'">'. __('Edit') . '</button></li>
                       <li><button type="button" class="location-delete-modal-toggle" data-id="'.$result->id.'">'. __('Delete') . '</button></li>
                    </ul>
                </div>'
            ];
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $results->_count_all,
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
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
     * Validate the data of this object.
     * @return bool It the data is valid, this function returns TRUE. Otherwise, it returns FALSE.
     */
    private function validate_for_save()
    {
        $ok = TRUE;
        $ok = ( $ok AND (strlen($this->main_table['title']) > 0) );

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
        $array['title'        ] = $this->main_table['title'        ];
        $array['type'         ] = $this->main_table['type'         ];
        $array['address_1'    ] = $this->main_table['address_1'    ];
        $array['address_2'    ] = $this->main_table['address_2'    ];
        $array['address_3'    ] = $this->main_table['address_3'    ];
        $array['county'       ] = $this->main_table['county'       ];
        $array['phone'        ] = $this->main_table['phone'        ];
        $array['email'        ] = $this->main_table['email'        ];
        $array['map_reference'] = $this->main_table['map_reference'];
        $array['publish'      ] = $this->main_table['publish'      ];
    }

    /**
     * Set the default values (for the INSERT and UPDATE statements) for this object.
     * @param $array
     */
    private function set_default_values(&$array)
    {
        $array['type'   ] = ($array['type'    ] == '') ? self::DEFAULT_TYPE : $array['type'   ];
        $array['publish'] = ($array['publish' ] == '') ?                  1 : $array['publish'];
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
        $r = DB::select('id', 'title', 'type', 'address_1', 'address_2', 'address_3', 'county', 'phone', 'email', 'map_reference', 'publish', 'deleted', 'date_modified', 'date_entered', 'modified_by', 'created_by')
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
