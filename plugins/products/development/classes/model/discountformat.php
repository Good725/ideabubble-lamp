<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_DiscountFormat extends ORM
{
    protected $_table_name = 'plugin_products_discount_format';

    protected $_has_many = array('users' => array('model' => 'users', 'foreign_key' => 'discount_format_id'));

    const TABLE_HAS_CATEGORY = 'plugin_products_discount_format_has_categories';

    // Format Types
    const DISCOUNT_FORMAT_AMOUNT_PRICE            =  1;
    const DISCOUNT_FORMAT_AMOUNT_SHIPPING         =  2;
    const DISCOUNT_FORMAT_COUPON_PRICE            =  3;
	const DISCOUNT_FORMAT_COUPON_SHIPPING         =  4;
	const DISCOUNT_FORMAT_FIRST_PURCHASE_PRICE    =  5;
	const DISCOUNT_FORMAT_FIRST_PURCHASE_SHIPPING =  6;
	const CART_BASED_PRICE_DISCOUNT =  7;
	const CART_BASED_FREE_SHIPPING =  8;
	const CART_BASED_QTY_DISCOUNT =  9;

    // Format Types Description
    public static $types_description = array
    (
        self::DISCOUNT_FORMAT_AMOUNT_PRICE            => 'Amount Based, % Price Discount',
        self::DISCOUNT_FORMAT_AMOUNT_SHIPPING         => 'Amount Based, % Shipping Discount',
        self::DISCOUNT_FORMAT_COUPON_PRICE            => 'Coupon Based, % Price Discount',
		self::DISCOUNT_FORMAT_COUPON_SHIPPING         => 'Coupon Based, % Shipping Discount',
		self::DISCOUNT_FORMAT_FIRST_PURCHASE_PRICE    => 'First Purchase % Price Discount',
		self::DISCOUNT_FORMAT_FIRST_PURCHASE_SHIPPING => 'First Purchase % Shipping Discount',
		// self::CART_BASED_PRICE_DISCOUNT => 'Amount Based, % Cart Discount(Cart)', // these are practically same as 1 and 2
		// self::CART_BASED_FREE_SHIPPING => 'Amount Based, Free Shipping(Cart)', // these are practically same as 1 and 2
		self::CART_BASED_QTY_DISCOUNT => 'Quantity Based, Discount(Cart)',
    );

    // Tables
    const MAIN_TABLE = 'plugin_products_discount_format';

    // Fields
    private $main_table;
    private $has_category;

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

        if (is_array(@$data->has_category)) {
            $this->has_category = $data->has_category;
        }
    }

    /**
     * Save the object.
     * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
     * @throws Exception
     */
    public function save_discount_format()
    {
        $ok = FALSE;
        $db = Database::instance();

        if ( ! is_null($db) AND $this->check_for_save() AND $db->begin() )
        {
            try
            {
                if ($this->main_table['id'] == NULL)
                {
                    $ok = $id = self::sql_insert_object($this->build_insert_array());
                }
                else
                {
                    $id = $this->main_table['id'];
                    $ok = self::sql_update_object($this->main_table['id'], $this->build_update_array());
                }

                if ($ok) {
                    self::sql_save_categories($id, $this->has_category);
                }

                if ($ok !== FALSE)
                {
                    $db->commit();
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
     * If the object identifier is NULL, return an array of associative arrays with all the non deleted objects. Otherwise, return an associative array with the specified object.
     * @param int $id The object identifier or NULL to retrieve all the objects.
     * @param array $where_clauses An optional array with where clauses (only AND). This is an array in this way: array(array($field, $condition, $value),...)
     * @return array An array of associative arrays if the object identifier is NULL or an associative array, if the object identifier is not NULL.
     */
    public static function get($id = NULL, $where_clauses = NULL, $within_date_range = FALSE)
    {
        $q = DB::select('id', 'title', 'description', 'type_id', 'code', 'publish', 'date_modified', 'date_entered', 'modified_by', 'created_by', 'date_available_from', 'date_available_till')
                ->from(self::MAIN_TABLE)
                ->where('deleted', '=', 0)
                ->order_by('id');

        if ($id != NULL)
        {
            $q->where('id', '=', $id);
        }

		if ($within_date_range)
		{
			$q
				->and_where_open()
					->where('date_available_from', '<=', DB::expr("NOW()"))
					->or_where('date_available_from', '=', '')
				->and_where_close()
				->and_where_open()
					->where('date_available_till', '>=', DB::expr("NOW()"))
					->or_where('date_available_till', '=', '')
				->and_where_close();
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

        for ($i = 0; $i < count($r); $i++)
        {
            $r[$i]['type'] = ($r[$i]['type_id'] == NULL) ? '' : self::$types_description[$r[$i]['type_id']];
            $r[$i]['date_available_from'] = ($r[$i]['date_available_from'] == "0000-00-00 00:00:00") ? 'Not set' : $r[$i]['date_available_from'];
            $r[$i]['date_available_till'] = ($r[$i]['date_available_till'] == "0000-00-00 00:00:00") ? 'Not set' : $r[$i]['date_available_till'];
            $r[$i]['has_category'] = DB::select('*')->from(self::TABLE_HAS_CATEGORY)->where('discountformat_id', '=', $r[$i]['id'])->execute()->as_array();
            foreach ($r[$i]['has_category'] as $x => $has_category) {
                $r[$i]['has_category'][$x] = $has_category['category_id'];
            }
            $r[$i]['role'] = self::get_users_roles_relationships($r[$i]['id']);
        }

        return ($id == NULL OR count($r) == 0) ? $r : $r[0];
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
     *
     */
    public static function get_all_types()
    {
        $array = array();

        foreach (self::$types_description as $id => $description)
        {
            array_push($array, array
            (
                'id'          => $id,
                'description' =>$description,
            ));
        }

        return $array;
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
        $array['title'                  ] = $this->main_table['title'       ];
        $array['description'            ] = $this->main_table['description' ];
        $array['type_id'                ] = $this->main_table['type_id'     ];
        $array['code'                   ] = $this->main_table['code'        ];
        $array['publish'                ] = $this->main_table['publish'     ];
        $array['date_available_from'    ] = $this->main_table['date_available_from'];
        $array['date_available_till'      ] = $this->main_table['date_available_till'];
    }

    /**
     * Set the default values (for the INSERT and UPDATE statements) for this object.
     * @param $array
     */
    private function set_default_values(&$array)
    {
        $array['type_id'] = ($array['type_id'] == '') ? NULL : $array['type_id'];
        $array['publish'] = ($array['publish'] == '') ? 1    : $array['publish'];
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
        $r = DB::select('id', 'title', 'description', 'type_id', 'code', 'publish', 'deleted', 'date_modified', 'date_entered', 'modified_by', 'created_by', 'date_available_from', 'date_available_till')
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

    private static function sql_save_categories($discountformat_id, $has_categories = array())
    {
        DB::delete(self::TABLE_HAS_CATEGORY)
            ->where('discountformat_id', '=', $discountformat_id)
            ->execute();
        if (is_array(@$has_categories)) {
            foreach ($has_categories as $category_id) {
                DB::insert(self::TABLE_HAS_CATEGORY)
                    ->values(array(
                        'discountformat_id' => $discountformat_id,
                        'category_id' => $category_id
                    ))
                    ->execute();
            }
        }
    }

    /**
     * Get user roles for the discount.
     * @param int $id The identifier of the object.
     * @return array|bool The array if the objects is present in the database. Otherwise, it returns FALSE.
     */
    private static function get_users_roles_relationships($id = NULL) {
        $userRoles = false;
        $relation_table = 'plugin_products_discount_format_users_roles';
        $roles_table = 'engine_project_role';
        $discount_table = 'plugin_products_discount_format';
        try {
            $userRoles = DB::select(
                $roles_table . '.id',
                $roles_table . '.role',
                $roles_table . '.description')
                ->from($roles_table)
                ->join($relation_table, 'LEFT')
                ->on($relation_table . '.usersrole_id', '=', 'engine_project_role.id')
                ->where($roles_table . '.publish', '=', 1)
                ->and_where($roles_table . '.deleted', '!=', 1)
                ->join($discount_table)
                ->on($relation_table . '.discountformat_id', '=', $discount_table . '.id')
                ->where($relation_table . '.discountformat_id', '=', $id)
                ->execute()
                ->as_array();
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());
        }
        return $userRoles;
    }


    /**
     * Get discount for the user group.
     * @param int $id The identifier of the object.
     * @return array|bool The array if the objects is present in the database. Otherwise, it returns FALSE.
     */
    public static function get_discounts_relationships($id = NULL) {
        $discounts = false;
        $relation_table = 'plugin_products_discount_format_users_roles';
        $discount_table = 'plugin_products_discount_format';
       // try {
            $discounts = DB::select(
                $discount_table . '.id',
                $discount_table . '.title',
                $discount_table . '.description',
                $discount_table . '.type_id',
                $discount_table . '.code',
                $discount_table . '.date_available_from',
                $discount_table . '.date_available_till')
                ->from($discount_table)
                ->join($relation_table, 'LEFT')
                ->on($relation_table . '.discountformat_id', '=', $discount_table . '.id')
                ->where($discount_table . '.publish', '=', 1)
                ->and_where($discount_table . '.deleted', '!=', 1)
                ->join('engine_project_role')
                ->on($relation_table . '.usersrole_id', '=', 'engine_project_role.id')
                ->where($relation_table .'.usersrole_id', '=', $id)
                ->execute()
                ->as_array();

//        }
//        catch (Exception $e) {
//            Log::instance()->add(Log::ERROR, $e->getTraceAsString());
//        }
        return $discounts;
    }

    public function save_users_roles_relationships($id, $roles) {
        try
        {
            DB::delete('plugin_products_discount_format_users_roles')
                ->where('discountformat_id', '=', $id)->execute();

            $none = false;
            foreach ($roles as $role) {
                if($role == 'none'){
                    $none = true;
                }
            }
            if(!$none) {
                $query = DB::insert('plugin_products_discount_format_users_roles', array('usersrole_id', 'discountformat_id'));
                foreach ($roles as $role) {
                    $query->values(array($role, $id));
                }
                $query->execute();
            }
        }
        catch (Exception $e)
        {
                Log::instance()->add(Log::ERROR, $e->getTraceAsString());
        }
    }
}
