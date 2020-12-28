<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Option extends Model
{
	// Media Folders
	const MEDIA_IMAGES_FOLDER = 'content';

	// Tables
	const MAIN_TABLE = 'plugin_products_option';
	const OPTIONS_DETAILS_TABLE = 'plugin_products_option_details';
	const PRODUCTS_OPTIONS = 'plugin_products_product_options';
	const OPTION_GROUPS = 'plugin_products_option_groups';

	// Default Group
	const DEFAULT_GROUP = 'Default';

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
			if (!$this->load($id))
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

		if (!is_null($db) AND $this->check_for_save() AND $db->begin())
		{
			try
			{
				// If this option is save as the default, remove the "default" flag from the previous default, if any
				if ($this->main_table['default'] == 1)
				{
					DB::update(self::MAIN_TABLE)
						->set(array('default' => 0))
						->where('group_id', '=', $this->main_table['group_id'])
						->and_where('default', '=', 1)
						->execute();
				}

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
				$db->rollback();
                throw $e;
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
	 * @param int   $id            The object identifier or NULL to retrieve all the objects.
	 * @param array $where_clauses An optional array with where clauses (only AND). This is an array in this way: array(array($field, $condition, $value),...)
	 * @return array An array of associative arrays if the object identifier is NULL or an associative array, if the object identifier is not NULL.
	 */
	public static function get($id = NULL, $where_clauses = NULL)
	{
		$q = DB::select(
            'options.id',
            'options.label',
            'groups.group',
            'options.value',
            'options.description',
            'options.message',
            'options.image',
            'options.price',
            'options.default',
            'options.publish',
            'options.date_modified',
            'options.date_entered',
            'options.modified_by',
            'options.created_by',
            'options.group_id'
        )
			->from(array(self::MAIN_TABLE, 'options'))
                ->join(array(self::OPTION_GROUPS, 'groups'))
                    ->on('options.group_id', '=', 'groups.id')
			->where('options.deleted', '=', 0)
			->order_by('options.id');

		if ($id != NULL)
		{
			$q->where('options.id', '=', $id);
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
	 * Return an array with all the groups.
	 * @return array The array.
	 */
	public static function get_all_groups()
	{
		$rows = self::getOptionGroups();

        $list = array();
		foreach ($rows as $row) {
            $list[$row['id']] = $row['group'];
		}

		return $list;
	}

	//
	// SNIPPET FUNCTIONS
	//
	/**
	 * Return an array with all the groups based on product id.
	 * @param $product_id id of product the groups should relate to
	 * @return array The array
	 */
	public static function get_all_groups_by_product($product_id)
	{
		$r = DB::select('t1.option_group_id', array('groups.group', 'option_group'), 't1.required', 't1.is_stock', array(DB::expr('COALESCE(`groups`.`group_label`, "")'),'group_label'))
			->distinct('option_group_id')
			->from(array('plugin_products_product_options', 't1'))
			    ->join(array(self::MAIN_TABLE, 't2'))->on('t1.option_group_id', '=', 't2.group_id')
                ->join(array(self::OPTION_GROUPS, 'groups'), 'left')->on('t2.group_id', '=', 'groups.id')
			->and_where('product_id', '=', $product_id)
			->and_where('t2.publish', '=', 1)
			->and_where('t2.deleted', '=', 0)
			->order_by('option_group')
			->execute()
			->as_array();

		for ($i = 0, $list = array(); $i < count($r); $i++)
		{
			array_push($list, $r[$i]);
		}

		return $list;
	}

	public static function get_options_by_field($field = NULL, $field_value = NULL, $published_only = FALSE)
	{
		$q = DB::select(
            'options.id',
            'options.label',
            'groups.group',
            'groups.group_label',
            'options.value',
            'options.description',
            'options.message',
            'options.image',
            'options.price',
            'options.default',
            'options.publish',
            'options.date_modified',
            'options.date_entered',
            'options.modified_by',
            'options.created_by',
            'options.group_id'
        )
            ->from(array(self::MAIN_TABLE, 'options'))
                ->join(array(self::OPTION_GROUPS, 'groups'))
                    ->on('options.group_id', '=', 'groups.id')
            ->where('options.deleted', '=', 0);

		if ($field != NULL AND $field_value != NULL)
		{
			$q->where($field, '=', $field_value);
		}
		if ($published_only)
		{
			$q->where('options.publish', '=', 1);
		}

		$r = $q->execute()->as_array();

		return $r;
	}

	public static function get_stock_options($option_name, $product_id)
	{
		return DB::select('t2.id', 't1.price', 't1.default', 't2.label')->from(array(self::OPTIONS_DETAILS_TABLE, 't1'))
			->join(array(self::MAIN_TABLE, 't2'), 'LEFT')->on('t1.option_id', '=', 't2.id')
				->join(array(self::OPTION_GROUPS, 'groups'), 'left')->on('t2.group_id', '=', 'groups.id')
			->where('t1.location', '=', 1)->and_where('t1.publish', '=', 1)->and_where('groups.group', '=', $option_name)->and_where('t1.product_id', '=', $product_id)
			->execute()->as_array();
	}

	public static function get_options_by_group($option_group, $matrix_id = FALSE, $option_number = 1)
	{
		if ($matrix_id)
		{
			$result = DB::select('t1.id', 't1.label', 't1.group', 't1.value', 't1.description', 't1.message', 't1.price', 't1.default', 't1.image','t1.group_label')
				->distinct('t1.id')
				->from(array(self::MAIN_TABLE, 't1'))
				->join(array(Model_Matrix::MATRIX_OPTIONS_TABLE, 't2'), 'LEFT')
				->on('t1.id', '=', 't2.option'.$option_number)
				->where('group', '=', $option_group)
				->and_where('t2.matrix_id', '=', $matrix_id)
				->and_where('t2.publish', '=', 1)
				->execute()->as_array();
		}
		else
		{
			$result = DB::select('id', 'label', 'group', 'value', 'description', 'message', 'price', 'default', 'image','group_label')
				->from(self::MAIN_TABLE)
				->where('group', '=', $option_group)
				->and_where('publish', '=', 1)
				->and_where('deleted', '!=', 1)
				->execute()->as_array();
		}
		return $result;
	}

    public static function get_options_by_group_id($group_id, $matrix_id = FALSE, $option_number = 1)
    {
        $select = DB::select(
            'options.id',
            'options.label',
            'groups.group',
            'groups.group_label',
            'options.value',
            'options.description',
            'options.message',
            'options.image',
            'options.price',
            'options.default',
            'options.publish',
            'options.date_modified',
            'options.date_entered',
            'options.modified_by',
            'options.created_by',
            'options.group_id'
        )
			->distinct(TRUE)
            ->from(array(self::MAIN_TABLE, 'options'))
                ->join(array(self::OPTION_GROUPS, 'groups'))
                    ->on('options.group_id', '=', 'groups.id')
            ->where('options.deleted', '=', 0)
            ->and_where('options.publish', '=', 1)
            ->and_where('options.group_id', '=', $group_id);
        if ($matrix_id)
        {
            $select->join(array(Model_Matrix::MATRIX_OPTIONS_TABLE, 'moptionx'), 'left')
                ->on('options.id', '=', 'moptionx.option' . $option_number)
				->where('moptionx.matrix_id', '=', $matrix_id)
				->where('moptionx.publish', '=', 1);
            $result = $select
                ->execute()
                ->as_array();
        }
        else
        {
            $result = $select
                ->execute()
                ->as_array();
        }
        return $result;
    }

	public static function is_colour_group($id)
	{
		$options = Model_Option::get_options_by_group_id($id);
		return (isset($options[0]) AND (strpos(strtolower($options[0]['group']), 'color') > -1 OR strpos(strtolower($options[0]['group']), 'colour') > -1));
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
		$array = array();
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
		$ok = ($ok AND (strlen($this->main_table['label']) > 0));

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
			'created_by' => $logged_user['id'],
		);

		$this->set_common_values($array);
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

		$this->set_common_values($array);
		$this->set_default_values($array);

		return $array;
	}

	/**
	 * Set the common values (for the INSERT and UPDATE statements) for this object.
	 * @param array $array The array.
	 */
	private function set_common_values(&$array)
	{
        $array['group_id']    = $this->main_table['group_id'];
        $array['label']       = $this->main_table['label'];
        $array['value']       = $this->main_table['value'];
        $array['description'] = $this->main_table['description'];
        $array['message']     = $this->main_table['message'];
        $array['image']       = $this->main_table['image'];
        $array['price']       = $this->main_table['price'];
        $array['default']     = $this->main_table['default'];
        $array['publish']     = $this->main_table['publish'];
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
        $option = DB::select(
            'options.id',
            'options.label',
            'groups.group',
            'options.value',
            'options.description',
            'options.message',
            'options.image',
            'options.price',
            'options.default',
            'options.publish',
            'options.date_modified',
            'options.date_entered',
            'options.modified_by',
            'options.created_by',
            'options.group_id',
            'groups.group_label'
        )
            ->from(array(self::MAIN_TABLE, 'options'))
                ->join(array(self::OPTION_GROUPS, 'groups'))
                    ->on('options.group_id', '=', 'groups.id')
            ->where('options.id', '=', $id)
            ->and_where('options.deleted', '=', 0)
            ->execute()
            ->current();

		return $option;
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
	 * @param int   $id           The identifier of the object.
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

    public static function getOptionGroups()
    {
        $groups = DB::select('*')
            ->from(self::OPTION_GROUPS)
            ->where('deleted', '=', 0)
            ->order_by('group_label')
            ->execute()
            ->as_array();
		return $groups;
    }

    public static function addOptionGroupIf($group, $groupLabel)
    {
        $existingId = DB::select('id')
            ->from(self::OPTION_GROUPS)
            ->where('group', '=', $group)
            ->execute()
            ->get('id');
        if (!$existingId) {
            $inserted = DB::insert(self::OPTION_GROUPS)
                ->values(array(
                    'group' => $group,
                    'group_label' => $groupLabel
                ))
                ->execute();
            return $inserted[0];
        } else {
            return $existingId;
        }
    }

    public static function get_group_label_id($group_id)
    {
        $q = DB::select(
            array(DB::expr("IF(group_label<>'',`group_label`,`group`)"), 'group_label')
        )
            ->from(Model_Option::OPTION_GROUPS)
            ->where('id', '=', $group_id)
            ->execute()->as_array();
        return count($q) > 0 ? $q[0]['group_label'] : '';
    }
}
