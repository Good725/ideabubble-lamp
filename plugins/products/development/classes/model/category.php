<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Category extends Model
{
	// Media Folders
	const MEDIA_IMAGES_FOLDER = 'content';

	// Tables
	const MAIN_TABLE = 'plugin_products_category';

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
	 * If the object identifier is NULL, return an array of associative arrays with all the non deleted objects. Otherwise, return an associative array with the specified object.
	 * @param int   $id          The object identifier or NULL to retrieve all the objects.
	 * @param int   $parent_id   This argument is used internally and should not to be used.
	 * @param array $plain_array This argument is used internally and should not to be used.
	 * @param int   $depth       This argument is used internally and should not to be used.
	 * @return array An array of associative arrays if the object identifier is NULL or an associative array, if the object identifier is not NULL.
	 */
	public static function get($id = NULL, $parent_id = NULL, &$plain_array = array(), $depth = 0, $cascade = TRUE, $published_only = FALSE)
	{
		$q = DB::select('id', 'category', 'description', 'information', 'image', 'order', 'publish', 'parent_id', 'theme', 'date_modified', 'date_entered', 'modified_by', 'created_by')
			->from(self::MAIN_TABLE)
			->where('deleted', '=', 0);

		if ($id != NULL)
		{
			$q->where('id', '=', $id);
		}
		else
		{
			if ($parent_id == 0)
			{
				$q
					->and_where_open()
						->where('parent_id', '=', 0)
						->or_where('parent_id', '=', NULL)
					->and_where_close();
			}
			else
			{
				$q->where('parent_id', '=', $parent_id);
			}

		}

		if ($published_only)
		{
			$q->where('publish', '=', 1);
		}

		$order_by = Settings::instance()->get('product_listing_order');
		switch ($order_by)
		{
			case ''            : $order_by = 'order'        ; break;
			case 'title'       : $order_by = 'category'     ; break;
			case 'date_entered': $order_by = 'date_modified'; break;
		}
		$direction = Settings::instance()->get('product_listing_sort');
		if ($direction == '')
		{
			$direction = 'ASC';
		}
		if ($order_by == 'order')
		{
			$q->order_by(DB::expr("CASE WHEN `order` = 0 THEN 1 ELSE 0 END ASC, `order` ".$direction.", `category` ASC"));
		}
		else
		{
			$q->order_by($order_by, $direction);
		}

		$r = $q->execute()->as_array();

		$project_media_folder = Kohana::$config->load('config')->project_media_folder;
		$base_path = ($project_media_folder) ? '/shared_media/'.$project_media_folder.'/media/' : '/media/';

		$config = Kohana::$config->load('config');

		$page_theme = ( ! empty($config->assets_folder_path)) ? Kohana::$config->load('config')->assets_folder_path : '';
		$count = count($r); // Get the fixed number now, as this could shrink, as elements get unset.
		for ($i = 0; $i < $count; $i++)
		{
			if ($published_only)
			{
				$category_theme = Model_Category::get_theme($r[$i]['id']);
				if ($category_theme != '' AND $category_theme != $page_theme)
				{
					unset($r[$i]);
					continue;
				}
			}

			$r[$i]['depth'] = $depth;
			$r[$i]['image_html'] = ($r[$i]['image'] != '') ? '<a href="'.$base_path.'/photos/products/_thumbs_cms/'.$r[$i]['image'].'"><img src="'.$base_path.'/photos/products/_thumbs_cms/'.$r[$i]['image'].'" alt="" /></a>' : '';

			array_push($plain_array, $r[$i]);

			if ($r[$i]['id'] != $parent_id AND $cascade)
			{
				self::get(NULL, $r[$i]['id'], $plain_array, $depth + 1);
			}
		}

		return $plain_array;
	}

	public static function get_list()
	{
		$categories = DB::select(
			'categories0.id',
			DB::expr("CONCAT_WS(' - ', categories2.category, categories1.category, categories0.category) as category")
		)
			->from(array(self::MAIN_TABLE, 'categories0'))
				->join(array(self::MAIN_TABLE, 'categories1'), 'left')->on('categories0.parent_id', '=', 'categories1.id')
				->join(array(self::MAIN_TABLE, 'categories2'), 'left')->on('categories1.parent_id', '=', 'categories2.id')
			->where('categories0.deleted', '=', 0)
			->order_by('category')
			->execute()
			->as_array();
        return $categories;
	}

    public static function get_product_categories_with_parents($id)
    {
        $categories = array();
        $pcategories = DB::select('category_id')
            ->from('plugin_products_product_categories')
            ->where('product_id', '=', $id)
            ->execute()
            ->as_array();

        foreach ($pcategories as $pcategory) {
            if ((int)$pcategory['category_id'] != 0) {
                $categories[] = $pcategory['category_id'];
            }
        }

        if (count($categories) > 0) {
            while (true) {
                $pcategories = DB::select('parent_id')
                    ->from('plugin_products_category')
                    ->where('id', 'in', $categories)
                    ->execute()
                    ->as_array();
                $added = false;
                foreach ($pcategories as $pcategory) {
                    if (!in_array($pcategory['parent_id'], $categories)) {
                        if ((int)$pcategory['parent_id'] != 0) {
                            $categories[] = $pcategory['parent_id'];
                            $added = true;
                        }
                    }
                }
                if (!$added) {
                    break;
                }
            }
        }

        return $categories;
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
				->or_where('parent_id', '=', $id)
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
	// SNIPPET FUNCTIONS
	//

	public static function get_by_name($category_name = NULL)
	{
		$q = DB::select()
			->from(self::MAIN_TABLE)
			->where('deleted', '=', 0)
			->where('publish', '=', 1)
			->order_by('order', 'ASC')
			->order_by('id');
		if ($category_name != NULL)
		{
			$db_category_name = str_replace('-', ' ', trim($category_name)); //REPLACE HYPHENS WITH SPACES AND TRIM ON PHP AND SQL LEVEL

			// Check if the name matches the URL name.
			// Otherwise check if the name with spaces and hyphens replaced matches the main name
			$q
				->and_where_open()
					->where('url_title', '=', $category_name)
					->or_where(DB::expr("TRIM(REPLACE(category, '-', ' '))"), '=', $db_category_name)
				->and_where_close();
		}

		$r = $q->execute()->as_array();

		return $r;
	}

	public static function has_subcategories($category = NULL)
	{
		$q = DB::select(array('COUNT("*")', 'sub_cat_count'))
			->from(self::MAIN_TABLE)
			->where('deleted', '=', 0)
			->and_where('publish', '=', 1);
		if ($category != NULL)
		{
			$q->where('parent_id', '=', $category);
		}

		$r = $q->execute()->as_array();
		if (!empty($r[0]['sub_cat_count']))
		{
			return TRUE;
		}

		return FALSE;
	}

	public static function has_parent($category = NULL)
	{
		$q = DB::select(array('COUNT("*")', 'parent_cat_count'))
			->from(self::MAIN_TABLE)
			->where('deleted', '=', 0)
			->and_where('publish', '=', 1)
			->and_where('parent_id', 'IS NOT', NULL)
			->and_where('parent_id', '!=', 0);

		if ($category != NULL)
		{
			$q->where('id', '=', $category);
		}

		$r = $q->execute()->as_array();
		if ($r[0]['parent_cat_count'] == 0)
		{
			return FALSE;
		}

		return TRUE;
	}

	public static function get_sub_categories($category = NULL, $limit = NULL, $offset = NULL)
	{
		$q = DB::select('id', 'category', 'description', 'information', 'image', 'order', 'publish', 'parent_id', 'theme', 'date_modified', 'date_entered', 'modified_by', 'created_by')
			->from(self::MAIN_TABLE)
			->where('deleted', '=', 0)
			->where('publish', '=', 1)
			->order_by('order', 'ASC')
			->order_by('id');
		if ($category != NULL)
		{
			$q->where('parent_id', '=', $category);
		}
		if (!is_null($limit))
		{
			$q->limit($limit);
		}
		if (!is_null($offset))
		{
			$q->offset($offset);
		}

		$r = $q->execute()->as_array();

		return $r;
	}

	public static function get_sign_builder_categories()
	{
		return DB::select('category.id', array('category.category', 'name'))
			->from(array(self::MAIN_TABLE, 'category'))
			->join(array('plugin_products_product_categories', 'pc'))->on('pc.category_id', '=', 'category.id')
			->join(array('plugin_products_product', 'product'))->on('pc.product_id', '=', 'product.id')
			->where('category.publish', '=', 1)
			->where('category.deleted', '=', 0)
			->where('product.builder', '=', 1)
			->distinct(TRUE)
			->order_by('category.category')
			->execute()
			->as_array();
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
		$ok = ($ok AND (strlen($this->main_table['category']) > 0));

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
		$array['category'] = $this->main_table['category'];
		$array['description'] = $this->main_table['description'];
		$array['information'] = $this->main_table['information'];
		$array['image'] = $this->main_table['image'];
		$array['order'] = $this->main_table['order'];
		$array['publish'] = $this->main_table['publish'];
		$array['parent_id'] = $this->main_table['parent_id'];
		$array['theme'] = $this->main_table['theme'];
	}

	/**
	 * Set the default values (for the INSERT and UPDATE statements) for this object.
	 * @param $array
	 */
	private function set_default_values(&$array)
	{
		$array['order'] = ($array['order'] == '') ? 0 : $array['order'];
		$array['publish'] = ($array['publish'] == '') ? 1 : $array['publish'];
		$array['parent_id'] = ($array['parent_id'] == '') ? NULL : $array['parent_id'];
		$array['theme'] = ($array['theme'] == '') ? NULL : $array['theme'];
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
		$r = DB::select('id', 'category', 'description', 'information', 'image', 'order', 'publish', 'parent_id', 'theme', 'deleted', 'date_modified', 'date_entered', 'modified_by', 'created_by')
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

	/**
	 * Get the theme of a category.
	 * If the category, does not have a theme, recurse through its parent categories, until one is found, if any
	 * @param       $category_id - the ID of the category to check
	 * @param array $checked     - an array of categories that have already been checked, in this recursion
	 * @return string
	 */
	public static function get_theme($category_id, $checked = array())
	{
		// If this category has already been checked, exit now to prevent an infinite loop
		if (in_array($category_id, $checked)) return '';

		// Get category data
		$category = DB::select('id', 'theme', 'parent_id')->from(self::MAIN_TABLE)->where('id', '=', $category_id)->execute()->current();

		// If no category is found, there's nothing left to check.
		if (!$category) return '';

		// If the category has a theme. Return that.
		if ($category['theme'] != '') return $category['theme'];

		// If the category, does not have a theme, check its parent
		$checked[] = $category_id;
		return self::get_theme($category['parent_id'], $checked);
	}

	public static function get_localisation_messages()
	{
		$categories = DB::select('category')->from(self::MAIN_TABLE)->execute()->as_array();
		$messages = array();
		foreach($categories as $category){
			$messages[] = $category['category'];
		}
		return $messages;
	}
}
