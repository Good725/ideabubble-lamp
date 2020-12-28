<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Product extends Model
{
	// Media Folders
	const MEDIA_IMAGES_FOLDER = 'products';
	const MEDIA_DOCUMENTS_FOLDER = 'docs';
	const MEDIA_SIZE_GUIDES_FOLDER = 'content';

	// Tables
	const MAIN_TABLE = 'plugin_products_product';
	const TABLE_IMAGES = 'plugin_products_product_images';
	const TABLE_DOCUMENTS = 'plugin_products_product_documents';
	const TABLE_OPTIONS = 'plugin_products_product_options';
	const TABLE_RELATED_TO = 'plugin_products_product_related_to';
	const TABLE_CATEGORIES = 'plugin_products_product_categories'; // relationship table
	const TABLE_TAGS = 'plugin_products_product_tags'; // relationship table
	const TABLE_OPTIONS_MAIN = 'plugin_products_option';
	const TABLE_OPTIONS_DETAILS = 'plugin_products_option_details';
	const CATEGORY_TABLE = 'plugin_products_category';
	const TAG_TABLE = 'plugin_products_tags';
	const STORE_LOCATION_TABLE = 'plugin_products_store_location';
	const YOUTUBE_VIDEOS = 'plugin_products_youtube_videos';
	const AUTOFEATURE_TABLE = 'plugin_products_auto_featured';
	const TABLE_PRODUCT_DISCOUNT_RATE = 'plugin_products_discount_rate';
	const TABLE_PRODUCT_DISCOUNT_FORMAT = 'plugin_products_discount_format';

	//PDF Data - Using A5 as the base.
	public static $pdf_ratio = array(
		'A0' => array('width' => 841, 'height' => 1189, 'multiplier' => 4),
		'A1' => array('width' => 594, 'height' => 841, 'multiplier' => 2.8284),
		'A2' => array('width' => 420, 'height' => 594, 'multiplier' => 2),
		'A3' => array('width' => 297, 'height' => 420, 'multiplier' => 1.4142),
		'A4' => array('width' => 210, 'height' => 297, 'multiplier' => 1),
		'A5' => array('width' => 148, 'height' => 210, 'multiplier' => 0.7071),
	);

	// Fields
	private $main_table;
	private $category_ids;
	private $tag_ids;
	private $images;
	private $documents;
	private $options;
	private $related_to;
	private $stock_options;
	private $youtube_videos;

	/**
	 * The constructor for this class.
	 * @param null $id The identifier of the object to load.
	 * @throws Exception
	 */
	public function __construct($id = NULL)
	{
		$this->main_table = $this->get_table_columns(self::MAIN_TABLE);

		// Relationships
		$this->category_ids = NULL;
		$this->tag_ids = NULL;
		$this->images = NULL;
		$this->documents = NULL;
		$this->options = NULL;
		$this->related_to = NULL;
		$this->stock_options = NULL;
		$this->youtube_videos = NULL;

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
		$data = $this->main_table;

		// Relationships
		$data['category_ids'] = $this->category_ids;
		$data['tag_ids'] = $this->tag_ids;
		$data['images'] = $this->images;
		$data['documents'] = $this->documents;
		$data['options'] = $this->options;
		$data['related_to'] = $this->related_to;
		$data['stock_options'] = $this->stock_options;
		$data['youtube_videos'] = $this->youtube_videos;

		return $data;
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

		// Relationships
		$this->category_ids = (isset($data['category_ids'])   AND is_array($data['category_ids'])) ? $data['category_ids'] : array();
		$this->tag_ids = (isset($data['tag_ids'])        AND is_array($data['tag_ids'])) ? $data['tag_ids'] : array();
		$this->images = (isset($data['images'])         AND is_array($data['images'])) ? $data['images'] : array();
		$this->documents = (isset($data['documents'])      AND is_array($data['documents'])) ? $data['documents'] : array();
		$this->options = (isset($data['options'])        AND is_array($data['options'])) ? $data['options'] : array();
		$this->related_to = (isset($data['related_to'])     AND is_array($data['related_to'])) ? $data['related_to'] : array();
		$this->stock_options = (isset($data['stock_options'])  AND is_array($data['stock_options'])) ? $data['stock_options'] : array();
		$this->youtube_videos = (isset($data['youtube_videos']) AND is_array($data['youtube_videos'])) ? $data['youtube_videos'] : array();

		return $this;
	}

	/*
	 * The following functions change specific properties for an instance of the object
	 */
	public function set_id($id)
	{
		$this->main_table['id'] = $id;
	}

	public function set_title($value)
	{
		$this->main_table['title'] = $value;
		return $this;
	}

	public function set_url_title($value)
	{
		$this->main_table['url_title'] = $value;
		return $this;
	}

	public function set_featured($value)
	{
		$this->main_table['featured'] = $value;
		return $this;
	}

	/*
	 * The following functions get specific properties for an instance of the object
	 */
	public function get_id()
	{
		return $this->main_table['id'];
	}

	public function get_featured()
	{
		return $this->main_table['featured'];
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
				$activity = new Model_Activity;
				$activity->set_item_type('product');

				if ($this->main_table['id'] == NULL)
				{
					$ok = self::sql_insert_object($this->build_insert_array());
					($ok != FALSE) ? $this->set_id($ok) : NULL;
					$activity->set_action('create');
				}
				else
				{
					$ok = self::sql_update_object($this->main_table['id'], $this->build_update_array());
					$activity->set_action('update');
				}

				$activity->set_item_id($this->get_id())->save();

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
				Log::instance()->add(Log::ERROR, $e->getMessage()."\n".$e->getTraceAsString());
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

	public static function get_all_list()
	{
		return DB::select('id', 'title')
			->from(array(self::MAIN_TABLE, 't1'))
			->where('t1.deleted', '=', 0)
			->order_by('title', 'ASC')->execute()->as_array();
	}

	public function get_options_and_stock()
	{
		return DB::select('t3.id', 't3.title', 't3.product_code', 't5.category', 't1.group_id', 'groups.group', 't1.label', 't6.price', 't7.name', array('ogroups.group', 'option_group'), 't6.quantity', array('t1.id', 'option_id'), array('t6.publish', 'publish'))
            ->from(array(self::TABLE_OPTIONS_MAIN, 't1'))
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')
                    ->on('t1.group_id', '=', 'groups.id')
                ->join(array(self::TABLE_OPTIONS, 't2'), 'LEFT')
			        ->on('t2.option_group_id', '=', 't1.group_id')
                ->join(array(Model_Option::OPTION_GROUPS, 'ogroups'), 'left')
                    ->on('t2.option_group_id', '=', 'groups.id')
                ->join(array(self::MAIN_TABLE, 't3'), 'LEFT')
			        ->on('t3.id', '=', 't2.product_id')
			    ->join(array(self::TABLE_CATEGORIES, 't4'), 'LEFT')
			        ->on('t4.product_id', '=', 't2.id')
			    ->join(array(self::CATEGORY_TABLE, 't5'), 'LEFT')
			        ->on('t5.id', '=', 't4.category_id')
			    ->join(array(self::TABLE_OPTIONS_DETAILS, 't6'), 'LEFT OUTER')
			        ->on('t6.option_id', '=', 't1.id')->on('t6.product_id', '=', 't3.id')
			    ->join(array(self::STORE_LOCATION_TABLE, 't7'), 'LEFT OUTER')
			        ->on('t7.id', '=', 't6.location')
			->where('t2.is_stock', '=', 1)
			->and_where('t2.product_id', '=', $this->main_table['id'])->and_where('t1.deleted', '=', 0)->execute()->as_array();
	}

	public function get_option_group_id($group_id)
    {
        //Kohana doesn't support CROSS joins for no partiuclar reason. Class.
        return DB::query(Database::SELECT, "SELECT `t3`.`id`,`t3`.`title`,`t5`.`category`,`t1`.`group_id`,`t1groups`.`group`,`t1`.`label`,`t6`.`price`,`t7`.`name`,`t6`.`quantity`,`t1`.`id` AS `option_id`,`t6`.`publish` AS `publish`
        FROM `plugin_products_option` AS `t1`
        CROSS JOIN `plugin_products_product` AS `t3`
        LEFT JOIN `plugin_products_product_categories` AS `t4` ON (`t4`.`product_id` = `t3`.`id`)
        LEFT JOIN `plugin_products_category` AS `t5` ON(`t5`.`id` = `t4`.`category_id`)
        LEFT OUTER JOIN `plugin_products_option_details` AS `t6` ON(`t6`.`option_id` = `t1`.`id` AND `t6`.`product_id` = `t3`.`id`)
        LEFT OUTER JOIN `plugin_products_store_location` AS `t7` ON(`t7`.`id` = `t6`.`location`)
        LEFT JOIN `plugin_products_option_groups` t1groups ON t1.group_id = t1groups.id
        WHERE
            `t1`.`group_id` = '" . $group_id . "'
        AND `t3`.`id` = '".$this->main_table['id']."'
        AND `t1`.`deleted` = 0 ORDER BY `t1`.`label` ASC")->execute()->as_array();
    }

	public function quantity_enabled()
	{
		return ($this->main_table['quantity_enabled'] == "1") ? TRUE : FALSE;
	}

	public function validate()
	{
		$ok = TRUE;
		$errors = array();
		$ids = array();
		if (strlen($this->main_table['title']) == 0)
		{
			$errors[] = array('text' => 'The title cannot be blank.', 'id' => 'title');
			$ok = FALSE;
		}

		if (self::check_for_duplicate_url_names())
		{
			$errors[] = array('text' => 'The URL title is used by another product. Please set a different URL in the SEO tab or change it for the other product.', 'id' => 'url_title');
			$ok = FALSE;
		}

		if ($this->main_table['disable_purchase'] != 1
            AND (!is_numeric($this->main_table['price']) OR empty($this->main_table['price']) OR $this->main_table['price'] === '' OR $this->main_table['price'] < 0)
            AND $this->main_table['builder'] == 0)
		{
			$errors[] = array('text' => 'The price must be a numerical value', 'id' => 'price');
			$ok = FALSE;
		}

        if ($this->main_table['disable_purchase'] != 1
            AND (!is_numeric($this->main_table['offer_price']) OR empty($this->main_table['offer_price']) OR $this->main_table['offer_price'] === '' OR $this->main_table['offer_price'] < 0)
            AND $this->main_table['builder'] == 0)
        {
            $errors[] = array('text' => 'The offer price must be a numerical value', 'id' => 'offer_price');
            $ok = FALSE;
        }

		if (empty($this->category_ids))
		{
			$errors[] = array('text' => 'You must select at least one Category', 'id' => 'category');
			$ok = FALSE;
		}

		if (!empty($this->category_ids) AND is_array($this->category_ids))
		{
			$q = DB::select(array(DB::expr('LOWER(category)'), 'category'))->from(self::CATEGORY_TABLE)->where('id', 'IN', $this->category_ids)->execute()->as_array();
			foreach ($q AS $key => $category)
			{
				if (in_array(strtolower($this->main_table['title']), $category))
				{
					$errors[] = array('text' => 'The product title cannot be the same as a category title.', 'id' => 'title');
					$ok = FALSE;
				}
			}
		}

		$briedDesc = $this->main_table['brief_description'];
        $desc      = $this->main_table['description'];

        if (empty($briedDesc)) {
            $errors[] = array('text' => 'The summary cannot be blank.', 'id' => 'brief_description');
            $ok = FALSE;
        }

        if (empty($desc)) {
            $errors[] = array('text' => 'The description cannot be blank.', 'id' => 'description');
            $ok = FALSE;
        }

		if (!$ok)
		{
			$result = '<ul>';
			$ids = array();
			foreach ($errors AS $key => $error)
			{
				$result .= '<li>'.$error['text'].'</li>';
				$ids[] = $error['id'];
			}
			$result .= '</ul>';
		}
		else
		{
			$result = TRUE;
		}

		return array('results' => $result, 'ids' => $ids);
	}

	// Returns TRUE if duplicates are found, FALSE otherwise
	public function check_for_duplicate_url_names()
	{
		if (trim($this->main_table['url_title']) == '')
		{
			$url_title = self::format_url_title($this->main_table['title'], $this->main_table['product_code']);
		}
		else
		{
			$url_title = trim($this->main_table['url_title']);
		}

		$products = self::get_by_product_url($url_title, FALSE, TRUE, FALSE);
		$count = count($products);

		// If there is more than one product by this URL title, return TRUE
		// If there is one product by this URL title and it is not this product, return TRUE
		// Otherwise return FALSE
		return ($count > 1 OR ($count == 1 AND $products[0]['id'] != $this->main_table['id']));
	}

	public function get_quantity()
	{
		return $this->main_table['quantity'];
	}

	public function get_sign_builder_layers()
	{
		return $this->main_table['sign_builder_layers'];
	}

	public function get_youtube_videos()
	{
		return DB::select('product_id', 'video_id')->from(self::YOUTUBE_VIDEOS)->where('product_id', '=', $this->main_table['id'])->execute()->as_array();
	}

	public static function get_new_product_options_id($group_id)
    {
        return DB::select(
            'options.group_id',
            'groups.group',
            'label',
            array('options.id', 'option_id')
        )
            ->from(array(self::TABLE_OPTIONS_MAIN, 'options'))
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')
                    ->on('options.group_id', '=', 'groups.id')
            ->where('group_id', '=', $group_id)
            ->and_where('options.deleted', '=', 0)
            ->execute()
            ->as_array();
    }

	public static function get_option_labels_id($group_id)
    {
        $q = DB::select('label')->from(self::TABLE_OPTIONS_MAIN)->where('group_id', '=', $group_id)->and_where('deleted', '=', 0)->execute()->as_array();
        $result = '';

        foreach ($q AS $key => $label)
        {
            $result .= ' '.$label['label'].', ';
        }

        return $result;
    }

	public static function get_group_label($group_label)
	{
		$q = DB::select('group_label')
			->from(Model_Option::OPTION_GROUPS)
			->where('group', '=', $group_label)
			->and_where('group_label', '<>', '')
			->and_where('group_label', 'IS NOT', NULL)
			->execute()->as_array();
		return count($q) > 0 ? $q[0]['group_label'] : '';
	}

	public static function list_all_stock_options()
	{
		return DB::select(
			't3.id',
			't3.title',
			't5.category',
			't1.group_id',
            'groups.group',
			't1.label',
			't6.price',
			't7.name',
			't6.quantity',
			array('t1.id', 'option_id'),
			array('t3.price', 'product_price'),
            't6.publish'
		)
			->from(array(self::TABLE_OPTIONS_MAIN, 't1'))
			    ->join(array(self::TABLE_OPTIONS, 't2'), 'LEFT')
			        ->on('t2.option_group_id', '=', 't1.group_id')
			    ->join(array(self::MAIN_TABLE, 't3'), 'LEFT')
			        ->on('t3.id', '=', 't2.product_id')
			    ->join(array(self::CATEGORY_TABLE, 't5'), 'LEFT')
			        ->on('t5.id', '=', 't3.category_id')
			    ->join(array(self::TABLE_OPTIONS_DETAILS, 't6'), 'LEFT OUTER')
			        ->on('t6.option_id', '=', 't1.id')->on('t6.product_id', '=', 't3.id')
			    ->join(array(self::STORE_LOCATION_TABLE, 't7'), 'LEFT OUTER')
			        ->on('t7.id', '=', 't6.location')
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')
                    ->on('t1.group_id', '=', 'groups.id')
			->where('t2.is_stock', '=', 1)
			->and_where('t1.deleted', '=', 0)->and_where('t3.deleted', '=', 0)->execute()->as_array();
	}

	public static function check_builder_product($product_id)
	{
		$q = DB::select('builder')->from(self::MAIN_TABLE)->where('id', '=', $product_id)->execute()->as_array();
		return (count($q) > 0 AND $q[0]['builder'] == '1');
	}

	/**
	 * If the object identifier is NULL, return an array of associative arrays with all the non deleted objects. Otherwise, return an associative array with the specified object.
	 * @param int   $id            The object identifier or NULL to retrieve all the objects.
	 * @param array $where_clauses An optional array with where clauses (only AND). This is an array in this way: array(array($field, $condition, $value),...)
	 * @return array An array of associative arrays if the object identifier is NULL or an associative array, if the object identifier is not NULL.
	 */
	public static function get($id = NULL, $where_clauses = NULL, $order_by = NULL, $sort_by = NULL, $limit = NULL, $offset = NULL)
	{
		if (is_null($order_by))
		{
			$order_by = Settings::instance()->get('product_listing_order');
		}
		if (is_null($order_by) OR $order_by == '')
		{
			$order_by = 'order';
		}
		if (is_null($sort_by))
		{
			$sort_by = Settings::instance()->get('product_listing_sort');
		}
		if (is_null($sort_by) OR $sort_by == '')
		{
			$sort_by = 'ASC';
		}


		$q = DB::select(
			array('t1.id', 'id'),
			'title',
			't1.url_title',
			array('t1.order', 'order'),
			array('t1.date_entered', 'date_entered'),
			array('t1.date_modified', 'date_modified'),
			'price',
			'disable_purchase',
			'display_price',
			'offer_price',
			'display_offer',
			'featured',
			'brief_description',
			array('t1.description', 'description'),
			'product_code',
			'ref_code',
			't1.weight',
			't1.seo_title',
			't1.seo_keywords',
			't1.seo_description',
			't1.seo_footer',
			'postal_format_id',
			't1.use_postage',
			array('t1.publish', 'publish'),
			'out_of_stock',
			'out_of_stock_msg',
			'size_guide',
			'document',
			array('t1.min_width', 'min_width'),
			array('t1.min_height', 'min_height'),
			'builder',
			'sign_builder_layers',
			't3.thumb_url'
		)
			->from(array(self::MAIN_TABLE, 't1'))
			->join(array('plugin_sict_product_relation', 't2'), 'LEFT')
			->on('t1.id', '=', 't2.product_id')
			->join(array('plugin_sict_product', 't3'), 'LEFT')
			->on('t2.sict_product_id', '=', 't3.product_id')
			->where('t1.deleted', '=', 0);

		if ($order_by == 'order')
		{
			$q->order_by(DB::expr("CASE WHEN `order` = 0 THEN 1 ELSE 0 END ASC, `order` ".$sort_by.", `title` ASC"));
		}
		else
		{
			$q->order_by($order_by, $sort_by);
		}


		if ($id != NULL)
		{
			$q->where('t1.id', '=', $id);
		}

		if ($where_clauses != NULL)
		{
			for ($i = 0; $i < count($where_clauses); $i++)
			{
				$clause = $where_clauses[$i];

				$q->where('t1.'.$clause[0], $clause[1], $clause[2]);
			}
		}

		if ( ! is_null($limit))
		{
			$q->limit($limit);
		}
		if ( ! is_null($offset))
		{
			$q->offset($offset);
		}

		$r = $q->execute()->as_array();

		$size = count($r);

		// check if flat rate shipping is in effect for use later
		$flat_rate = Settings::instance()->get('shipping_flat_rate');

		$is_book = (isset(Kohana::$config->load('config')->template_folder_path) AND Kohana::$config->load('config')->template_folder_path == 'books');

        $checkout_model = new Model_Checkout;
		for ($i = 0; $i < $size; $i++)
		{
			$q = DB::select(array('SUM("quantity")', 'quantity'))->from(self::TABLE_OPTIONS_DETAILS)->where('product_id', '=', $r[$i]['id'])->execute()->as_array();

			if (count($q) > 0)
			{
				$r[$i]['quantity'] = $q[0]['quantity'] != NULL ? $q[0]['quantity'] : 'n/a';
			}
			else
			{
				$r[$i]['quantity'] = 'n/a';
			}

			// check if weight is O for flat rate postal option check
			$weight = $r[$i]['weight'];

			if ((!($weight > 0)) || (Empty($weight))) //if weight is 0 or empty
			{
				if ($flat_rate)
				{
					//override the weight to use 1 as a value >0 to force the shipping/postal rates to kick in
					$r[$i]['weight'] = 1;
				}
			}

			$sub = DB::select('a1.category')->from(array(self::CATEGORY_TABLE, 'a1'))->join(array(self::TABLE_CATEGORIES, 'a2'), 'LEFT')->on('a1.id', '=', 'a2.category_id')->where('a2.product_id', '=', $r[$i]['id'])->execute()->as_array();
			$r[$i]['category'] = '';

			foreach ($sub AS $key => $category)
			{
				$r[$i]['category'] .= $category['category'].', ';
			}

			$r[$i]['category'] = rtrim($r[$i]['category'], ', ');

			$tags = DB::select('tag.title')->from(array(self::TAG_TABLE, 'tag'))
				->join(array(self::TABLE_TAGS, 'product_tag'), 'LEFT')->on('product_tag.tag_id', '=', 'tag.id')
				->where('product_tag.product_id', '=', $r[$i]['id'])
				->execute()->as_array();
			$r[$i]['tags'] = '';

			foreach ($tags as $tag)
			{
				$r[$i]['tags'] .= $tag['title'].', ';
			}

			if ($is_book)
			{
				$r[$i]['author'] = Model_Product::get_author($r[$i]['id']);
			}

            $discounts = $checkout_model->apply_discounts_to_product($r[$i]);
            $r[$i]['discounts'] = $discounts['discounts'];
            $r[$i]['discount_total'] = $discounts['discount_total'];

			// Only show the local description
			if (Settings::instance()->get('localisation_content_active') == '1')
			{
				$r[$i]['description']       = Model_Localisation::get_ctag_translation($r[$i]['description'], I18n::$lang);
				$r[$i]['brief_description'] = Model_Localisation::get_ctag_translation($r[$i]['brief_description'], I18n::$lang);
			}
		}

		// Relationships
		for ($i = 0; $i < count($r); $i++)
		{
			self::sql_add_relationships($r[$i]);
		}

		return ($id == NULL OR count($r) == 0) ? $r : $r[0];
	}

	/**
	 * Select all products for product plugin
	 * @return string json products
	 */
	public static function ajaxGetAllProducts($filters)
	{
		$columns = array(
			0 => 'p.id',
			1 => 'p.id',
			2 => 'p.title',
			3 => 'p.product_code',
			4 => DB::expr("GROUP_CONCAT(`c`.`category` ORDER BY `c`.`category` SEPARATOR '<br />')"),
			5 => 'p.quantity',
			6 => 'p.order',
			7 => 'p.publish',
			8 => 'p.featured',
			10 => 'p.date_entered',
			11 => 'p.date_modified'
		);

		// When joining for a one-to-many relationship, e.g. categories, GROUP_BY() must be used to avoid duplicate records appearing in the listing
		// GROUP_CONCAT() can be used to put the "many" items in one cell and to sort by the "many" item.
		$q = DB::select(DB::expr('SQL_CALC_FOUND_ROWS p.*'), array(DB::expr("GROUP_CONCAT(`c`.`category` ORDER BY `c`.`category` SEPARATOR '<br />')"), 'categories'))
			->from(array(self::MAIN_TABLE, 'p'))
			->join(array(self::TABLE_CATEGORIES, 'a2'), 'LEFT')
			->on('p.id', '=', 'a2.product_id')
			->join(array(self::CATEGORY_TABLE, 'c'), 'LEFT')
			->on('c.id', '=', 'a2.category_id')
			->where('p.deleted', '=', 0)
			->group_by('p.id');

		// Search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
            $q->where_open();
			$q->where('p.title', 'like', '%'.$filters['sSearch'].'%');
            $q->or_where('p.product_code', 'like', '%'.$filters['sSearch'].'%');
            $q->or_where('p.id', 'like', '%'.$filters['sSearch'].'%');
            $q->where_close();
		}
		// Individual column search
        for ($i = 0; $i < count($columns); $i++)
        {
            if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
            {
                if ($i == 4 AND $filters['sSearch_'.$i] != '')
                {
                    $q->and_where('c.category','like','%'.$filters['sSearch_'.$i].'%');
                }
                else
                {
                    $q->and_where($columns[$i],'like','%'.$filters['sSearch_'.$i].'%');
                }
            }
        }

		if (@$filters['iDisplayLength'] == -1) {
			$filters['iDisplayLength'] = 100;
		}
		// Limit
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$q->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$q->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order by the most recently used column sort, then the second most recent, etc. and finally the date modified
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($columns[$filters['iSortCol_'.$i]] != '')
				{
					$q->order_by($columns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$q->order_by('p.date_modified', 'desc');

		$r = $q->execute()->as_array();

		$total = DB::select(DB::expr('FOUND_ROWS() as total'))->execute()->get('total');
		$size = count($r);
		$output = array(
			'sEcho'                => (int) @$_GET['sEcho'],
			'iTotalRecords'        => (int) $total,
			'iTotalDisplayRecords' => (int) $total,
			'aaData'               => array()
		);

		$project_media_folder = Kohana::$config->load('config')->project_media_folder;
		$base_path = ($project_media_folder) ? '/shared_media/'.$project_media_folder.'/media/' : '/media/';

		for ($i = 0; $i < $size; $i++)
		{
			$q = DB::select(array('SUM("quantity")', 'quantity'))
				->from(self::TABLE_OPTIONS_DETAILS)
				->where('product_id', '=', $r[$i]['id'])
				->execute()
				->as_array();

			if (count($q) > 0)
			{
				$r[$i]['quantity'] = ($q[0]['quantity'] != NULL) ? $q[0]['quantity'] : 'n/a';
			}
			else
			{
				$r[$i]['quantity'] = 'n/a';
			}

			$image = DB::select()->from(self::TABLE_IMAGES)->where('product_id', '=', $r[$i]['id'])->execute()->current();
			$image_html = ($image['file_name'] != '') ? '<img src="'.$base_path.'/photos/products/_thumbs_cms/'.$image['file_name'].'" alt="" />' : '';

			foreach ($r[$i] as $field => $value)
			{
				$r[$i][$field] = ($r[$i][$field] == NULL) ? '' : $r[$i][$field];
			}
			$output['aaData'][] = array(
				$r[$i]['image']=$image_html,
				'<span class="hidden">'.str_pad($r[$i]['id'],10,'0',STR_PAD_LEFT).'</span><a href="/admin/products/edit_product?id='.$r[$i]['id'].'">'.$r[$i]['id'].'</a>',
				'<a href="/admin/products/edit_product?id='.$r[$i]['id'].'">'.$r[$i]['title'].'</a>',
				$r[$i]['product_code'],
				$r[$i]['categories'],
				$r[$i]['quantity'],
				$r[$i]['order'],
				'<button type="button" class="btn-link publish" data-product-id="'.$r[$i]['id'].'"><span class="icon-'.($r[$i]['publish'] == 1 ? 'ok' : 'remove').'"></span></button>',
				'<label>
					<input class="star_checkbox toggle_featured" type="checkbox"'.(($r[$i]['featured'] == 1) ? ' checked="checked"' : '').' data-product_id="'.$r[$i]['id'].'" />
					<span class="icon-"></span>
				</label>',
				IBhelpers::relative_time_with_tooltip($r[$i]['date_entered']),
				IBHelpers::relative_time_with_tooltip($r[$i]['date_modified'] ? $r[$i]['date_modified'] : $r[$i]['date_entered']),
				'<div>
					<div><a href="/admin/products/edit_product?id='.$r[$i]['id'].'" class="edit-link"><span class="icon-pencil"></span>&nbsp;edit</a></div>
					<div><button type="button" class="btn-link manage-categories-button" data-product_id="'.$r[$i]['id'].'"><span class="icon-tag"></span> categories</button></div>
					<div><button type="button" class="btn-link delete" data-product-id="'.$r[$i]['id'].'"><span class="icon-remove-circle"></span>&nbsp;delete</button></div>
				</div>',

			);
		}

		return json_encode($output);
	}

	static function get_products_json($term, $published_only = FALSE)
	{
		/*$query = DB::select()
			->from('plugin_products_product')
			->where(DB::expr('CONCAT_WS(\' \',`product_code`,`title`)'), 'LIKE', '%'.$term.'%')
			->and_where('deleted', '=', 0);*/
		$query = DB::select('product.*','product_image.file_name')
			->from(array('plugin_products_product','product'))
			->join(array('plugin_products_product_images', 'product_image'))
			->on('product_image.product_id', '=', 'product.id')
			->where(DB::expr('CONCAT_WS(\' \',`product_code`,`title`)'), 'LIKE', '%'.$term.'%')
			->and_where('deleted', '=', 0);
		

		if ($published_only) $query->where('publish', '=', 1);

		$count = clone $query;

		$return['results'] = $query->select('product.id', 'title', 'url_title', 'product_code','product_image.file_name')->order_by('title')->limit(5)->execute()->as_array();
		$return['count'] = $count->select(array(DB::expr('count(*)'), 'count'))->execute()->get('count', 0);

		return json_encode($return);
	}

	static function get_categories_json($term, $published_only = FALSE)
	{
		$query = DB::select()
			->from('plugin_products_category')
			->where('category', 'LIKE', '%'.$term.'%')
			->and_where('deleted', '=', 0);

		if ($published_only) $query->where('publish', '=', 1);

		$count = clone $query;

		$return['results'] = $query->select('id', 'category')->order_by('category')->limit(5)->execute()->as_array();
		$return['count'] = $count->select(array(DB::expr('count(*)'), 'count'))->execute()->get('count', 0);

		return json_encode($return);
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

	public static function check_stock_levels($product_id, $option_id)
	{
		if (Settings::instance()->get('stock_enabled') == "TRUE")
		{
			$result = array();
			$q = DB::select('t1.quantity')->from(array(self::TABLE_OPTIONS_DETAILS, 't1'))
				->join(array(self::TABLE_OPTIONS_MAIN, 't3'), 'LEFT')->on('t3.id', '=', 't1.option_id')
				->join(array(self::TABLE_OPTIONS, 't2'), 'LEFT')->on('t2.option_group_id', '=', 't3.group_id')->on('t2.product_id', '=', 't1.product_id')
				->where('t1.product_id', '=', $product_id)->and_where('t1.option_id', '=', $option_id)->and_where('t1.publish', '=', 1)->and_where('t2.is_stock', '=', 1)->execute()->as_array();
			if (count($q) > 0)
			{
				$result['is_stock_item'] = TRUE;
				$result['quantity'] = $q[0]['quantity'];
			}
			else
			{
				if (!is_numeric($option_id))
				{
					$q = DB::select('quantity_enabled', 'quantity')->from(self::MAIN_TABLE)->where('id', '=', $product_id)->execute()->as_array();
					if ($q[0]['quantity_enabled'] == '1')
					{
						$result['is_stock_item'] = TRUE;
						$result['quantity'] = $q[0]['quantity'];
					}
					else
					{
						$result['is_stock_item'] = FALSE;
						$result['quantity'] = 0;
					}
				}
				else
				{
					$result['is_stock_item'] = FALSE;
					$result['quantity'] = 0;
				}
			}
		}
		else
		{
			$result['is_stock_item'] = FALSE;
			$result['quantity'] = 0;
		}

		return $result;

	}

	public static function check_stock_price($product_id, $option_id)
	{
		$q = DB::select('price')->from(self::TABLE_OPTIONS_DETAILS)->where('product_id', '=', $product_id)->and_where('option_id', '=', $option_id)->execute()->as_array();
		return count($q) > 0 ? $q[0]['price'] : FALSE;
	}

	public static function decrement_stock_level($product_id, $option_id, $amount, $previous_amount)
	{
		DB::update(self::TABLE_OPTIONS_DETAILS)->set(array('quantity' => ($previous_amount - $amount)))->where('option_id', '=', $option_id)->and_where('product_id', '=', $product_id)->execute();
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
			Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
		}

		return $ok;
	}

	/**
	 * Toggle the featured option of the specified object
	 * @param int $id The object identifier
	 * @return bool If the function succeeded
	 */
	public static function toggle_featured_option($id)
	{
		$ok = FALSE;
		try
		{
			$ok = DB::update(self::MAIN_TABLE)
				->set(array('featured' => DB::expr("IF (`featured` = 1, 0, 1)")))
				->where('id', '=', $id)
				->execute();
			$ok = ($ok == 1);
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
		}
		return $ok;
	}

	public static function get_store_locations()
	{
		$result = '';
		$q = DB::select('id', 'name')->from(self::STORE_LOCATION_TABLE)->execute()->as_array();
		foreach ($q AS $key => $store)
		{
			$result .= '<option value="'.$store['id'].'">'.$store['name'].'</option>';
		}
		return $result;
	}

	//
	// SNIPPETS
	//
	public static function get_products_plugin_page()
	{
		try
		{
			$page_id = Settings::instance()->get('products_plugin_page');

			// If the setting is not used, used "products.html". If the setting is used, use the page specified.
			$page = ($page_id == '') ? 'products.html' : Model_Pages::get_page_by_id($page_id);
			// If the page from the setting does not exist, use "products.html"
			$page = ($page == '') ? 'products.html' : $page;

			return $page;
		}
		catch (Exception $e)
		{
			return 'products.html';
		}
	}

	public static function get_random_products($number_of_products)
	{
		$q = DB::select(array('t1.id', 'id'), 'title', array('t2.category_id', 'category_id'), 'category', array('t1.order', 'order'), 'price', 'display_price', 'disable_purchase', 'offer_price', 'display_offer', 'featured', 'brief_description', array('t1.description', 'description'), 'product_code', 'ref_code', 'weight', 'seo_title', 'seo_keywords', 'seo_description', 'seo_footer', 'postal_format_id', array('t1.min_width', 'min_width'), array('t1.min_height', 'min_height'), array('t1.publish', 'publish'), 'out_of_stock', 'out_of_stock_msg', 'size_guide', 'document')
			->from(array(self::MAIN_TABLE, 't1'))
			->join(array(self::TABLE_CATEGORIES, 't2'), 'LEFT')
			->on('t2.product_id', '=', 't1.id')
			->join(array(Model_Category::MAIN_TABLE, 't3'), 'LEFT')
			->on('t2.category_id', '=', 't3.id')
			->where('t1.deleted', '=', 0)
			->and_where('t1.publish', '=', 1)
			->and_where('t1.featured', '=', 1)
			->order_by(DB::expr('RAND()'));

		$r = $q->execute()->as_array();
		$r = array_slice($r, 0, $number_of_products);

		for ($i = 0; $i < count($r); $i++)
		{
			foreach ($r[$i] as $field => $value)
			{
				$r[$i][$field] = ($r[$i][$field] == NULL) ? '' : $r[$i][$field];
			}
		}

		// Relationships
		for ($i = 0; $i < count($r); $i++)
		{
			self::sql_add_relationships($r[$i]);
		}

		return $r;
	}

	public static function get_product_images_by_category($category_id, $term = NULL)
	{
		$term = (is_null($term)) ? '' : $term;
		return DB::select('product.id', 'product.title', 'product_image.file_name', 'image.dimensions')
			->from(array('plugin_products_product', 'product'))
			->join(array('plugin_products_product_images', 'product_image'))
			->on('product_image.product_id', '=', 'product.id')
			->join(array('plugin_products_product_categories', 'product_category'))
			->on('product_category.product_id', '=', 'product.id')
			->join(array('plugin_products_category', 'category'))
			->on('product_category.category_id', '=', 'category.id')
			->join(array('plugin_media_shared_media', 'image'))
			->on(DB::expr('REPLACE(`product_image`.`file_name`, "%20", " ")'), '=', 'image.filename')
			->where('category.id', '=', $category_id)
			->where('image.location', '=', 'products')
			->where('product.title', 'like', '%'.$term.'%')
			->where('category.publish', '=', 1)
			->where('category.deleted', '=', 0)
			->where('product.publish', '=', 1)
			->where('product.deleted', '=', 0)
			->order_by('product.title')
			->execute()->as_array();

	}

	public static function search_like($search_like, $offset = 0, $limit = 0)
	{
		return DB::select()
			->from(array(self::MAIN_TABLE, 't1'))
			->join(array('plugin_sict_product_relation', 't2'))
			->on('t1.id', '=', 't2.product_id')
			->join(array('plugin_sict_product', 't3'))
			->on('t2.sict_product_id', '=', 't3.product_id')
			->where('publish', '=', 1)
			->where('deleted', '=', 0)
			->where('title', 'LIKE', '%'.$search_like.'%')
			->offset($offset)
			->limit($limit)
			->execute()
			->as_array();
	}

	public static function search($parameters, $offset = 0, $limit = 10)
	{
		$vat_rate = 1 + (float) Settings::instance()->get('vat_rate');
		$price_with_vat_sql = 'ROUND(product.price * '.$vat_rate.', 2)';

		$sql = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT product.*, cat1.category, cat2.category AS pcategory, '.$price_with_vat_sql.' as price_with_vat, sitc.thumb_url, sitc.img_url
					FROM plugin_products_product AS product
						INNER JOIN plugin_products_product_categories ON product.id = plugin_products_product_categories.product_id
						INNER JOIN plugin_products_category AS cat1 ON plugin_products_product_categories.category_id = cat1.id
						LEFT JOIN plugin_products_category AS cat2 ON if(cat1.parent_id = 0 or cat1.parent_id is null,cat1.id = cat2.id, cat1.parent_id = cat2.id )
						LEFT JOIN plugin_sict_product AS sitc ON product.product_code = sitc.sku';
		$where = array();
		$where[] = 'product.publish = 1';
		$where[] = 'product.deleted = 0';
		if (isset($parameters['minprice']) && is_numeric($parameters['minprice']))
		{
			$where[] = $price_with_vat_sql.' >= '.$parameters['minprice'];
		}
		if (isset($parameters['maxprice']) && is_numeric($parameters['maxprice']))
		{
			$where[] = $price_with_vat_sql.' <= '.$parameters['maxprice'];
		}
		if (isset($parameters['keyword']))
		{
			$keywords = preg_split('/[\ ,]+/i', trim(preg_replace('/[^\p{L}0-9\ ]/ui', '', $parameters['keyword'])));
			$match1 = array();
			$match2 = array();
			$ignore_words = array(
				"the",
				"at",
				"on",
				"in",
				"of",
				"as",
				"'s"
			);

			foreach ($keywords as $i => $keyword)
			{
				if (in_array(strtolower($keyword), $ignore_words) || strlen($keyword) == 1 || $keyword == '') { // remove too short things like "at" "'s" "on" ...
					unset($keywords[$i]);
				} else {
					if (substr($keyword, -3) == 'ies'){
						$match2[] = '+' . substr($keyword, 0, -3) . 'y' . '*';
					} else if (substr($keyword, -3) == 'ses' || substr($keyword, -3) == 'xes'){
						$match2[] = '+' . substr($keyword, 0, -2) . '*';
					} else if ($keyword[strlen($keyword) - 1] == 's') {
						$match2[] = '+' . substr($keyword, 0, -1) . '*'; /*'+' . $keyword . '* */
					} else {
						$match2[] = '+' . $keyword . '*';
					}
					$match1[] = '+' . $keyword . '*';
				}
			}

			$where[] = "(match(title,product_code) AGAINST(".Database::instance()->escape(implode(' ', $match1))." IN BOOLEAN MODE) or match(title,product_code) AGAINST(".Database::instance()->escape(implode(' ', $match2))." IN BOOLEAN MODE) or product_code = ".Database::instance()->escape(trim($parameters['keyword'])).")";
		}
		$cats = array();
		if (isset($parameters['category_id']))
		{
			foreach ($parameters['category_id'] as $i => $category_id)
			{
				if (!is_numeric($category_id))
				{
					unset($parameters['category_id'][$i]);
				}
			}
			foreach ($parameters['category_id'] as $i => $category_id)
			{
				$cat = 'cat2.id = '.$category_id;
				if (isset($parameters['sub_category_id'][$category_id]))
				{
					foreach ($parameters['sub_category_id'][$category_id] as $i => $sub_category_id)
					{
						if (!is_numeric($sub_category_id))
						{
							unset($parameters['sub_category_id'][$category_id][$i]);
						}
					}
					if (count($parameters['sub_category_id'][$category_id]) > 0)
					{
						$cat .= ' and cat1.id in ('.implode(',', $parameters['sub_category_id'][$category_id]).')';
					}
				}
				$cats[] = '('.$cat.')';
			}
		}
		if (count($cats))
		{
			$where[] = '('.implode(' or ', $cats).')';
		}
		if (isset($parameters['manufacturer_id']))
		{
			foreach ($parameters['manufacturer_id'] as $i => $manufacturer_id)
			{
				if (!is_numeric($manufacturer_id))
				{
					unset($parameters['manufacturer_id'][$i]);
				}
			}
			if (count($parameters['manufacturer_id']) > 0)
			{
				$where[] = 'product.manufacturer_id in ('.implode(',', $parameters['manufacturer_id']).')';
			}
		}
		if (isset($parameters['distributor_id']))
		{
			foreach ($parameters['distributor_id'] as $i => $distributor_id)
			{
				if (!is_numeric($distributor_id))
				{
					unset($parameters['distributor_id'][$i]);
				}
			}
			if (count($parameters['distributor_id']) > 0)
			{
				$where[] = 'product.distributor_id in ('.implode(',', $parameters['distributor_id']).')';
			}
		}
		if (isset($parameters['featured_only']))
		{
			$where[] = 'product.featured=1';
			$where[] = 'sitc.thumb_url is not null';
			$where[] = 'sitc.thumb_url <> \'\'';

		}
		if (count($where))
		{
			$sql .= ' where '.implode(' and ', $where);
		}
		
		$sql .= ' group by product.id limit '.$offset.', '.$limit;
		$products = DB::query(Database::SELECT, $sql)->execute()->as_array();
		$count = DB::query(Database::SELECT, 'select found_rows() as cnt')->execute()->get('cnt');
		return array('products' => $products, 'count' => $count);
	}

	public static function get_by_category($category = NULL, $product_id = NULL, $offset = 0, $limit = 0,$sortby= NULL)
	{
		$order_by = Settings::instance()->get('product_listing_order');
		$dir = Settings::instance()->get('product_listing_sort');
		if($sortby){
			$order_by=$sortby;
	    }else{	
		   $order_by = (empty($order_by)) ? 'order' : $order_by;
	    }
		$dir = (empty($dir)) ? 'ASC' : $dir;

		// $public_categories = DB::select()->from(self::CATEGORY_TABLE)->where('publish', '=', 1)->and_where('deleted', '=', 0);

		$query = DB::select('product.id', 'product.title', 'product.url_title', array('category.id', 'category_id'), 'category.category',
			'product.order', 'product.date_entered', 'product.price', 'product.display_price', 'product.disable_purchase',
			'product.offer_price', 'product.display_offer', 'product.featured', 'product.brief_description',
			'product.description', 'product.product_code', 'product.ref_code', 'product.weight', 'product.seo_title',
			'product.seo_keywords', 'product.seo_description', 'product.seo_footer', 'product.postal_format_id',
			'product.min_height', 'product.min_width', 'product.publish', 'product.out_of_stock', 'product.out_of_stock_msg',
			'product.size_guide', 'product.document', 'product.builder', 'product.sign_builder_layers', 'sict_product.thumb_url'
		)
			->from(array(self::MAIN_TABLE, 'product'))
			->join(array(self::TABLE_CATEGORIES, 'product_category'), 'LEFT')->on('product_category.product_id', '=', 'product.id')
			->join(array(self::CATEGORY_TABLE, 'category'), 'LEFT')->on('product_category.category_id', '=', 'category.id')
			->join(array('plugin_sict_product_relation', 'sict_rel'), 'LEFT')->on('product.id', '=', 'sict_rel.product_id')
			->join(array('plugin_sict_product', 'sict_product'), 'LEFT')->on('sict_rel.sict_product_id', '=', 'sict_product.product_id')
			->where('product.publish', '=', 1)->where('product.deleted', '=', 0)
			->where('category.publish', '=', 1)->where('category.deleted', '=', 0)
			->order_by('category.theme');

		if ($order_by == 'order')
		{
			$query->order_by(DB::expr("CASE WHEN `product`.`order` = 0 THEN 1 ELSE 0 END ASC, `product`.`order` ".$dir.", `product`.`title` ASC"));
		}
		else
		{
			$query->order_by($order_by, $dir);
		}

		(!is_null($product_id)) ? $query->where('product.id', '=', $product_id) : NULL;
		(!is_null($category)) ? $query->where('category.category', '=', $category) : NULL;

		// add limit and offset to query
		if ($limit)
		{
			$query->limit($limit);
			if ($offset)
			{
				$query->offset($offset);
			}
		}

		$r = $query->execute()->as_array();

        $checkout_model = new Model_Checkout;
		for ($i = 0; $i < count($r); $i++)
		{
			foreach ($r[$i] as $field => $value)
			{
				$r[$i][$field] = ($r[$i][$field] == NULL) ? '' : $r[$i][$field];
			}

            $discounts = $checkout_model->apply_discounts_to_product($r[$i]);
            $r[$i]['discounts'] = $discounts['discounts'];
            $r[$i]['discount_total'] = $discounts['discount_total'];

        }

		// Relationships
		for ($i = 0; $i < count($r); $i++)
		{
			self::sql_add_relationships($r[$i]);
		}

		return ($product_id == NULL OR count($r) == 0) ? $r : $r[0];

	}

	public static function get_by_product_url($product_url = NULL, $published_only = TRUE, $return_all = FALSE, $categories = TRUE)
	{
		$q = DB::select(array('t1.id', 'id'), 'title', 't1.url_title', array('t1.order', 'order'), 'price', 'display_price', 'disable_purchase', 'offer_price', 'display_offer', 'featured', 'brief_description', array('t1.description', 'description'), 'product_code', 'ref_code', 't1.weight', 't1.seo_title', 't1.seo_keywords', 't1.seo_description', 't1.seo_footer', 'postal_format_id', 't1.use_postage', array('t1.publish', 'publish'), array('t1.min_width', 'min_width'), array('t1.min_height', 'min_height'), 'out_of_stock', 'out_of_stock_msg', 'size_guide', 'document', 'quantity_enabled', 'quantity', 't1.builder', 't1.matrix', 't1.over_18', 't1.sign_builder_layers', array('t6.name', 'manufacturer_name'), 't5.sku', 't5.img_url', 't5.specification')
			->from(array(self::MAIN_TABLE, 't1'))
			->join(array('plugin_sict_product_relation', 't4'), 'LEFT')
			->on('t1.id', '=', 't4.product_id')
			->join(array('plugin_sict_product', 't5'), 'LEFT')
			->on('t4.sict_product_id', '=', 't5.product_id')
			->join(array('plugin_sict_manufacturer', 't6'), 'LEFT')
			->on('t5.manufacturer_id', '=', 't6.manufacturer_id')
			->where('t1.deleted', '=', 0);

		if ($categories)
		{
			$q
				->select(array('t3.id', 'category_id'), 'category')
				->join(array(self::TABLE_CATEGORIES, 't2'), 'LEFT')
				->on('t2.product_id', '=', 't1.id')
				->join(array(Model_Category::MAIN_TABLE, 't3'), 'LEFT')
				->on('t2.category_id', '=', 't3.id');
		}

		if ($published_only)
		{
			$q->where('t1.publish', '=', 1);
		}

		if (!is_null($product_url))
		{
			$q->where('t1.url_title', '=', $product_url);
            $q->where('t1.publish','=',1);
		}

		$q->order_by('t1.price', 'asc');
		$q->order_by('t1.id', 'desc');

		$r = $q->execute()->as_array();

		$is_book = (isset(Kohana::$config->load('config')->template_folder_path) AND Kohana::$config->load('config')->template_folder_path == 'books');
		for ($i = 0; $i < count($r); $i++)
		{
			foreach ($r[$i] as $field => $value)
			{
				$r[$i][$field] = ($r[$i][$field] == NULL) ? '' : $r[$i][$field];
			}

			if ($is_book)
			{
				$r[$i]['author'] = Model_Product::get_author($r[$i]['id']);
			}

            $checkout_model = new Model_Checkout;
            $discounts = $checkout_model->apply_discounts_to_product($r[$i]);
            $r[$i]['discounts'] = $discounts['discounts'];
            $r[$i]['discount_total'] = $discounts['discount_total'];

            // Only show the local description
			if (Settings::instance()->get('localisation_content_active') == '1')
			{
				$r[$i]['description']       = Model_Localisation::get_ctag_translation($r[$i]['description'], I18n::$lang);
				$r[$i]['brief_description'] = Model_Localisation::get_ctag_translation($r[$i]['brief_description'], I18n::$lang);
			}

		}

		// Relationships
		for ($i = 0; $i < count($r); $i++)
		{
			self::sql_add_relationships($r[$i]);
		}

		// Reviews
		if (isset($r[0]))
		{
			$r[0]['reviews'] = ORM::factory('Product_Review')
				->where('product_id', '=', $r[0]['id'])
				->order_by('date_created', 'desc')
				->find_all_published();

			$r[0]['average_rating'] = Model_Product_Review::get_average_product_rating($r[0]['id']);
		}

		return (is_null($product_url) OR count($r) == 0 OR $return_all) ? $r : $r[0];
	}

	public static function render_checkout_html($return_view = TRUE)
	{
		$checkout_model = new Model_Checkout();
		$products = $checkout_model->get_cart_details();
		/*echo '<pre>';
		print_r($products);
		echo '</pre>';*/
		$coupon_format_ids = array(Model_DiscountFormat::DISCOUNT_FORMAT_COUPON_PRICE, Model_DiscountFormat::DISCOUNT_FORMAT_COUPON_SHIPPING);
		$coupon_discounts = Model_DiscountRate::get(NULL, array(array('type_id', 'IN', $coupon_format_ids)));

		$data['accept_coupons'] = (count($coupon_discounts) > 0);
		$data['postage'] = Model_PostageZone::get_all_published();
		$data['counties'] = @$checkout_model->get_counties_as_options();
		$data['locations'] = (class_exists('Model_Location')) ? Model_Location::get_all_published() : array();
		$data['products_list'] = '';
		$data['postal_methods'] = FALSE;
		$data['use_postage'] = FALSE;
		$size_guide_id = Settings::instance()->get('default_size_guide');
		$data['size_guide'] = ($size_guide_id) ? Model_Pages::get_page_by_id($size_guide_id) : '';

		foreach ($data['postage'] as $postage)
		{
			$methods = array('courier', 'collect in store', 'ireland (courier )', 'for collection');
			if (in_array(strtolower($postage['title']), $methods))
			{
				$data['postal_methods'] = TRUE;
			}
		}

		if (!empty($products))
		{
			$data['number_of_items'] = $products->number_of_items;
			$data['cart_price'] = $products->cart_price;
			$data['shipping_price'] = $products->shipping_price;
			$data['subtotal'] = $products->subtotal;
			$data['subtotal2'] = $products->subtotal2;
			$data['vat'] = $products->vat;
			$data['vat_rate'] = $products->vat_rate;
			$data['discounts'] = $products->discounts;
			$data['final_price'] = $products->final_price;
			$data['gift_option'] = @$products->gift_option ?: false;
			$data['gift_price'] = $products->gift_price;
			//$data['final_price'] = $products->final_price -  $products->discounts;
			// If there is no final price yet then print the cart price
			if (!isset($data['final_price']) OR empty($data['final_price']))
			{
				$data['final_price'] = $products->cart_price;
			}
			$data['zone_id'] = @$products->zone_id;
			$data['paypal_enabled'] = Model_Checkout::is_paypal_enabled();
			foreach ($products->lines as $key => $line)
			{
				// Will resolve to TRUE, if at least product uses postage
				$data['use_postage'] = ($data['use_postage'] OR $line->product->use_postage);

				if (Model_Product::check_builder_product($line->product->id))
				{
					$data_line['line'] = $line;
					$data_line['line_id'] = $key;
					$data_line['product'] = Model_Product::get($line->product->id);
					$data['products_list'] .= View::factory('front_end/checkout_productline_html', $data_line);
				}
				else
				{
					$data_line['line'] = $line;
					$data_line['line_id'] = $key;
					$data_line['product'] = Model_Product::get($line->product->id);
					$data['products_list'] .= View::factory('front_end/checkout_productline_html', $data_line);
				}
			}
		}

		// Logged-in-user data
		$user = Auth::instance()->get_user();
		$data['user_data'] = ORM::factory('User', $user['id'])->as_array();

		// Set the CSS and JS Files for this View
		$custom_ui = ltrim(URL::get_skin_urlpath().'css/jquery-ui.custom.min.css', '/');
		$ui_theme = file_exists($custom_ui) ? '/'.$custom_ui : URL::get_engine_plugin_assets_base('products').'css/front_end/jqueryui-lightness/jquery-ui-1.10.3.min.css';

		$data['view_css_files'][] = '<link href="'.$ui_theme.'" rel="stylesheet">';
		$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/checkout.css'.'" rel="stylesheet">';
		$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/products_front_end_general.css'.'" rel="stylesheet">';
		$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/popup.css'.'" rel="stylesheet">';

		// For some reason there are two checkout JS files that need to be loaded
		$data['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/jquery-ui-1.10.3.min.js"></script>';
		$data['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/checkout.js"></script>';
		$data['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/checkout.js"></script>';

		$data['stripe']['enabled'] = (Settings::instance()->get('stripe_enabled') == 'TRUE');
		$data['realex_enabled'] = (Settings::instance()->get('enable_realex') == 1 AND Settings::instance()->get('realex_username') != '');

		if ($data['stripe']['enabled'])
		{
			require_once APPPATH.'/vendor/stripe/lib/Stripe.php';
			$data['stripe_testing'] = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
			$data['stripe']['secret_key'] = ($data['stripe_testing']) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
			$data['stripe']['publishable_key'] = ($data['stripe_testing']) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
			Stripe::setApiKey($data['stripe']['secret_key']);
		}
		$data['paypal_enabled'] = (isset($data['paypal_enabled']) AND $data['paypal_enabled'] == TRUE);
		$data['payment_method_count'] = $data['paypal_enabled'] ? 1 : 0;
		$data['payment_method_count'] += $data['realex_enabled'] ? 1 : 0;
		$data['payment_method_count'] += $data['stripe']['enabled'] ? 1 : 0;

		$data['default_payment'] = $data['stripe']['enabled'] ? 'Stripe' : ($data['realex_enabled'] ? 'Realex' : ($data['paypal_enabled'] ? 'PayPal' : NULL));


		if ($return_view)
		{
			return View::factory('front_end/checkout_html', $data);
		}
		else
		{
			return $data;
		}
	}

	/* funciton for getting discount cart based amount*/
	public static function get_discount_data_by_id($discount_type_id){
		if(!empty($discount_type_id) && $discount_type_id > 0){
			$chk_cart_discount = DB::select('t1.format_id', 't1.range_from', 't1.range_to', 't1.discount_rate_percentage', 't2.id', 't2.title', 't2.type_id')
			->from(array(self::TABLE_PRODUCT_DISCOUNT_RATE, 't1'))
			->join(array(self::TABLE_PRODUCT_DISCOUNT_FORMAT, 't2'), 'INNER')
			->on('t1.format_id', '=', 't2.id')
			->where('t1.deleted', '=', 0)
			->where('t1.publish', '=', 1)
			->where('t2.publish', '=', 1)
			->where('t2.deleted', '=', 0)
			->where('t2.type_id', '=', $discount_type_id)
			->order_by('t1.id', 'DESC')->execute()->as_array();
			return $chk_cart_discount;
		}else{
			return false;
		}	
	}

	public static function render_products_list_html($itemsperpage = 9, $show_all_products = FALSE, $featured_only = FALSE, $ajax = FALSE, $amount_of_items = NULL, $offset = NULL, $url = NULL,$sortby= NULL)
	{
		if (is_null($sortby))
		{
			$sortby = Session::instance()->get('products_feed_order');
		}
		
		$infinite_scrolling = (Settings::instance()->get('products_infinite_scroller') == 1);

		// While $products, fill the items
		// $_SERVER['REQUEST_URI' HOLDS Pluses and some URL Encoded EMPTY_SPACES, which when urldecode-(d) are messing up the product's name and It CANNOT BE FOUND in the DB
		// $parsed_url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		// Use SCRIPT_URL or SCRIPT_URI either will work. @NOTE: the SCRIPT_URL has the same Structure as the REQUEST_URI, ONLy difference is these URL encoded empty spaces

		//the parameter must remain for compatibility with existing shops...
		if (is_null($amount_of_items) )
		{
			if (Session::instance()->get('products_feed_items_per_page'))
			{
				$itemsperpage = Session::instance()->get('products_feed_items_per_page');
			}
			else
			{
				$itemsperpage_setting = Settings::instance()->get('products_per_page');
				if (is_numeric($itemsperpage_setting))
				{
					$itemsperpage = $itemsperpage_setting;
				}
			}

			$limit = $itemsperpage;
		}
		else
		{
			$itemsperpage = $amount_of_items;
			$limit = $amount_of_items;
		}
		
     
		$url = (is_null($url)) ? $_SERVER['SCRIPT_URL'] : $url;
        $parsed_url = explode('/', urldecode(trim($url, '/')));
		$parsed_url_length = count($parsed_url);
        $is_top_level = (count(array_unique($parsed_url)) == 1);
        $is_category = (count(Model_Category::get_by_name($parsed_url[$parsed_url_length - 1])) != 0);
		$is_product = (count(self::get_by_product_url($parsed_url[$parsed_url_length - 1])) != 0);
		$search_by = (!($is_category OR $is_product) AND isset($parsed_url[1])) ? $parsed_url[1] : '';
		$current_page = Request::initial()->query('page');
		$offset = (is_null($offset)) ? ($current_page ? $current_page * $itemsperpage : 0) : $offset;
		$vat_rate = Settings::instance()->get('vat_rate') ? Settings::instance()->get('vat_rate') : 0;

		$count = 0;
		$pages = 0;

		$max_title_length = intval(Settings::instance()->get('product_feed_title_truncation'));
		if ($max_title_length == 0)
		{
			$max_title_length = 20;
		}


		if ($is_category OR $show_all_products OR $search_by OR $is_top_level)
		{

			$session = Session::instance();
			$current_url = URL::site(Request::detect_uri(), TRUE).URL::query();
			$session->set('last_product_browsing_url', $current_url);

			if ($featured_only)
			{
				$products     = self::get(NULL, array(array('featured', '=', 1), array('publish', '=', 1)), NULL, NULL, $limit, $offset);
                $all_products = self::get(NULL, array(array('featured', '=', 1), array('publish', '=', 1)));
				$data['product_categories'] = '';
			}
			elseif ($show_all_products)
			{
                $products     = self::get_by_category(NULL, NULL, $offset, $limit);
                $all_products = self::get_by_category();
				$data['product_categories'] = '';
			}
			else if ($search_by)
			{
				$products     = self::search_like($search_by, $offset, $limit);
                $all_products = self::search_like($search_by);
				$data['product_categories'] = '';
			}
			else
			{
				$current_category = Model_Category::get_by_name($parsed_url[$parsed_url_length - 1]);
				if (isset($current_category[0]['id']) OR $is_top_level)
				{
                    $current_category[0] = isset($current_category[0]) ? $current_category[0] : NULL;
					$data['product_categories'] = '';
					$data['current_category'] = $current_category[0];

					// Only show the local description
					if (Settings::instance()->get('localisation_content_active') == '1')
					{
						$data['current_category']['information'] = Model_Localisation::get_ctag_translation($data['current_category']['information'], I18n::$lang);
						$data['current_category']['description'] = Model_Localisation::get_ctag_translation($data['current_category']['description'], I18n::$lang);
					}

					$current_mode      = Session::instance()->get('display_mode');
                    $all_subcategories = Model_Category::get_sub_categories($current_category[0]['id']);
                    $subcategories     = Model_Category::get_sub_categories($current_category[0]['id'], $limit, $offset);

                    if (count($all_subcategories))
                    {
                        if($current_mode=='list'){
                            $view='product_category_item_list_html';
                        }elseif($current_mode=='thumb'){
                            $view='product_category_item_thumb_html';
                        }else{
                            $view='product_category_item_html';
                        }
                        //$view = 'product_category_item_html';
                    }
                    elseif (Settings::instance()->get('product_listing_display') == 'vertical')
                    {
                        $view = 'product_item_vertical_html';
                    }
                    else
                    {
                        if($current_mode=='list'){
                                $view='product_item_list_html';
                        }elseif($current_mode=='thumb'){
                                $view='product_item_thumb_html';
                        }else{
                                $view='product_item_html';
                        }
                        //$view = 'product_item_html';
                    }

                    foreach ($subcategories as $key => $sub_category)
                    {
                        if ($count % $itemsperpage == 0)
                        {
                            $display_count = $count + 1;
                            $display = '';
                            $current = ' _current';
                            if ($display_count != 1)
                            {
                                $display = 'display:none;';
                                $current = '';
                            }
                            $pages++;
                            if ($ajax OR ($infinite_scrolling AND $key != 0)) ;
                            else
                            {
                                $data['product_categories'] .= '<div id="p'.$pages.'" class="pagedemo'.$current.' '.$current_mode.'" style="'.$display.'">';
                            }
                        }

                        $data['product_categories'] .= View::factory('front_end/'.$view, $sub_category);

                        if ($count % $itemsperpage == $itemsperpage - 1)
                        {
                            if ($ajax OR ($infinite_scrolling AND $key != 0)) ;
                            else
                            {
                                $data['product_categories'] .= '</div>';
                            }
                        }

                        $count++;
                    }
                    $limit -= count($subcategories);

					if ($infinite_scrolling)
					{
						$total_categories = Model_Category::get_sub_categories($current_category[0]['id']);
						$product_offset = ($offset - count($total_categories)) > 0 ? $offset - count($total_categories) : 0;
					}
					else
					{
						$product_offset = $offset;
					}

					$products     = self::get_by_category($current_category[0]['category'], NULL, $product_offset, $limit ,$sortby);
                    $all_products = self::get_by_category($current_category[0]['category'], NULL, 0, NULL ,$sortby);
                    
					$single_products_redirect = strtolower(Settings::instance()->get('single_product_redirect')) == "true" ? TRUE : FALSE;

					if (count($all_products) === 1 AND $single_products_redirect AND !$is_product)
					{
						$url = URL::site();
						foreach ($parsed_url as $key => $string)
						{
							$url .= $string.DIRECTORY_SEPARATOR;
						}

						$url .= str_replace(' ', '-', $products[0]['title']);

						Request::current()->redirect($url);
					}

					if ( ! $ajax AND empty($products) AND empty($subcategories))
					{
						IbHelpers::set_message('This category is empty, please check again later', 'info'); // 'error', 'info', 'success'
						echo IbHelpers::get_messages();
					}
				}
			}

			if ( ! empty($products) OR ! empty($subcategories))
			{
				$data['products'] = '';

				$is_book = (isset(Kohana::$config->load('config')->template_folder_path) AND Kohana::$config->load('config')->template_folder_path == 'books');

				foreach ($products as $product_data)
				{
					$current_mode=Session::instance()->get('display_mode');
					// Only show the local description
					if (Settings::instance()->get('localisation_content_active') == '1')
					{
						$product_data['description']       = Model_Localisation::get_ctag_translation($product_data['description'], I18n::$lang);
						$product_data['brief_description'] = Model_Localisation::get_ctag_translation($product_data['brief_description'], I18n::$lang);
					}

					if ($vat_rate)
					{
						$product_data['price_with_vat'] = round($product_data['price'] * (1 + $vat_rate), 2);
					}
					else
					{
						$product_data['price_with_vat'] = $product_data['price'];
					}
					if ($count >= $itemsperpage)
					{
						break;
					}

					$product_data['truncated_title'] = (strlen($product_data['title']) >= $max_title_length + 4)
						? trim(substr($product_data['title'], 0, $max_title_length))."..."
						: $product_data['title'];

					if ($count % $itemsperpage == 0)
					{
						$pages++;
						$div_opened = TRUE;
						$data['product_categories'] .= $ajax ? '' : '<div class="pagedemo _current '.$current_mode.'">';
					}

					if ($is_book)
					{
						$product_data['author'] = Model_Product::get_author($product_data['id']);
					}
                     
                    if($current_mode=='list'){
						$view='product_item_list_html';	 
					}elseif($current_mode=='thumb'){
						$view='product_item_thumb_html';
				    }else{
						$view='product_item_html';
				    }		
					$data['product_categories'] .= View::factory('front_end/'.((
						Settings::instance()->get('product_listing_display') == 'vertical')
						? 'product_item_vertical_html'
						: $view
					), $product_data);

					/* commented 20.03.2015
						if ($count % $itemsperpage == $itemsperpage - 1) {
						$display_count = $count + 1;
						$data['product_categories'] .= '</div>';
						$div_closed = true;
					}*/

					$count++;
				}
				$data['product_categories'] .= (isset($div_opened) AND  $div_opened AND ! $ajax) ? '</div>' : '';

				$data['count'] = $count;
				$data['itemsperpage'] = $itemsperpage;
				$data['pages'] = $pages;

				if (!$ajax AND Settings::instance()->get('products_infinite_scroller') != 1)
				{
                    $number_of_categories = isset($all_subcategories) ? count($all_subcategories) : 0;
                    $number_of_products   = isset($all_products)      ? count($all_products)      : 0;

                    $number_of_pages = (($number_of_categories > $number_of_products) ? $number_of_categories : ceil($number_of_products) / $itemsperpage);
					$paging_button = View::factory('front_end/paging/paging_button')
						->set('current_page',    $current_page)
						->set('page_after',      ceil($number_of_products / $itemsperpage) - $current_page)
                        ->set('number_of_pages', $number_of_pages);

					$pagination = View::factory('front_end/paging/index')
						->set('action', Request::initial()->uri())
						->set('paging_button', $paging_button);

					$data['product_categories'] = $pagination.$data['product_categories'].$pagination;
				}

				$data['base_file_path'] = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'products');

				// Set the CSS and JS Files for this View
				$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/products_front_end_general.css'.'" rel="stylesheet">';
				$data['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/products_front_end_general.js"></script>';

				//Add the items to the container view and return the HTML
				if ($ajax)
				{
					return $data['product_categories'];
				}
				else
				{
					return View::factory('front_end/product_category_list_html', $data);
				}
			}
            else if (empty($products))
            {
                if ($offset > 0) { // end of scroll
                    return '';
                } else {
                    IbHelpers::set_message(__('This product / category does not exist, please check again later'),
                        'info'); // 'error', 'info', 'success'
                    echo IbHelpers::get_messages();
                }
            }
		}
		else if ($is_product)
		{
			$products = self::get_by_product_url($parsed_url[$parsed_url_length - 1]);
			if (empty($products))
			{
				IbHelpers::set_message('This product does not exist, please check again later', 'info'); // 'error', 'info', 'success'
				echo IbHelpers::get_messages();
			}
			else
			{
				if (isset($products))
				{
					$model = new Model_Product($products['id']);
					$products['videos'] = $model->get_youtube_videos();
					$products['tags'] = Model_ProductTag::get_all_for_product($products['id']);

					if (isset($products['builder']) && $products['builder'] > 0)
					{
						$products['presets'] = Model_Media::get_presets_like('Signs - %');
						if (isset($products['images'][0]))
						{
							$products['builder_category_id'] = DB::select('preset_id')->from('plugin_media_shared_media')->where('filename', '=', $products['images'][0])->where('location', '=', 'products')->execute()->get('preset_id', 0);
						}
						// Temporary method to determine what kind of builder it is
						// To be fixed in MTEE-19
						$shirt_builder = $products['builder'] == 2;

						try
						{
							$over_18 = Session::instance()->get('over_18');
						}
						catch (Exception $e)
						{
							$over_18 = FALSE;
						}
						if (!$over_18 AND $products['over_18'] == 1)
						{
							$view = 'over_18_notice';
						}
						else if ($shirt_builder)
						{
							$view = 'tshirt_builder';
							$products['view_css_files'][] = '<link rel="stylesheet" href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/tshirt_builder.css'.'">';
							$products['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/tshirt_builder.js"></script>';
						}
						else
						{
							$view = 'sign_builder';
						}
					}
					else if (Settings::instance()->get('product_details_display') == 'wide')
					{
						$view = 'product_details_wide';
					}
					else
					{
						$view = 'product_details';
					}

					// Size guide
					if ($products['size_guide'] == '')
					{
						// Use the default, if one has not been defined at product-level
						$size_guide_id = Settings::instance()->get('default_size_guide');
						$products['size_guide'] = ($size_guide_id == '') ? '' : Model_Pages::get_page_by_id($size_guide_id);
					}
					$size_guide_data = Model_Pages::get_page($products['size_guide']);
					$products['size_guide_data'] = isset($size_guide_data[0]) ? $size_guide_data[0] : $size_guide_data;

					$products['products_plugin_page'] = Model_Product::get_products_plugin_page();
					$products['categories'] = array();
					foreach ($products['category_ids'] as $category_id)
					{
						$category = Model_Category::get($category_id);
						if (isset($category[0]))
						{
							if (isset($category[0]['parent_id']) AND $category[0]['parent_id'] != '')
							{
								$category[0]['parent'] = Model_Category::get($category[0]['parent_id']);
							}
							$products['categories'][] = $category[0];
						}
					}

					// Set the CSS and JS Files for this View
					$products['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/products_front_end_general.css'.'" rel="stylesheet">';
					$products['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/products_front_end_general.js"></script>';
					$products['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/jquery.jqzoom.min.js"></script>';
					$products['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/product_details.js"></script>';
					$products['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/checkout.js"></script>';
					$products['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/checkout.js"></script>';
					return View::factory('front_end/'.$view, $products);
				}
				else
				{
					IbHelpers::set_message('This product does not exist, please check again later', 'info'); // 'error', 'info', 'success'
					return IbHelpers::get_messages();
				}
			}
		}
        else if (!$is_product)
        {
            // Keep record of the URL for "Continue Shopping" buttons
            $session = Session::instance();
            $current_url = URL::site(Request::detect_uri(), TRUE).URL::query();
            $session->set('last_product_browsing_url', $current_url);
            IbHelpers::set_message('This product or category doesn\'t exist, please check again later', 'info'); // 'error', 'info', 'success'
            echo IbHelpers::get_messages();
        }
		else
		{
			IbHelpers::set_message('No product or category of this name.', 'info'); // 'error', 'info', 'success'
			echo IbHelpers::get_messages();
		}
	}

	public static function render_products_advanced_search_html($itemsperpage = 9, $show_all_products = FALSE, $featured_only = FALSE)
	{
		// While $products, fill the items
		// $_SERVER['REQUEST_URI' HOLDS Pluses and some URL Encoded EMPTY_SPACES, which when urldecode-(d) are messing up the product's name and It CANNOT BE FOUND in the DB
		// $parsed_url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		// Use SCRIPT_URL or SCRIPT_URI either will work. @NOTE: the SCRIPT_URL has the same Structure as the REQUEST_URI, ONLy difference is these URL encoded empty spaces

		$parsed_url = explode('/', urldecode(trim($_SERVER['SCRIPT_URL'], '/')));
		$parsed_url_length = count($parsed_url);
		$is_category = (count(Model_Category::get_by_name($parsed_url[$parsed_url_length - 1])) != 0);
		$is_product = (count(self::get_by_product_url($parsed_url[$parsed_url_length - 1])) != 0);
		$search_by = (!($is_category OR $is_product) AND isset($parsed_url[1])) ? $parsed_url[1] : '';
		$current_page = Request::initial()->query('page');
		$offset = $current_page ? $current_page * $itemsperpage : 0;
		$limit = $itemsperpage;

		// Keep record of the URL for "Continue Shopping" buttons
		$session = Session::instance();
		$current_url = URL::site(Request::detect_uri(), TRUE).URL::query();
		$session->set('last_product_browsing_url', $current_url);

		$count = 0;
		$pages = 0;
		$max_title_length = intval(Settings::instance()->get('product_feed_title_truncation'));
		if ($max_title_length == 0)
		{
			$max_title_length = 20;
		}

		//the parameter must remain for compatibility with existing shops...
		if (is_numeric(Settings::instance()->get('products_per_page')))
		{
			$itemsperpage = Settings::instance()->get('products_per_page');
		}

		$search_parameters = array();
		if (isset($_GET['featured_only']))
		{
			$search_parameters['featured_only'] = 1;
		}
		if (Request::initial()->query('keyword'))
		{
			$search_parameters['keyword'] = Request::initial()->query('keyword');
		}
		if (Request::initial()->query('minprice'))
		{
			$search_parameters['minprice'] = Request::initial()->query('minprice');
		}
		if (Request::initial()->query('maxprice'))
		{
			$search_parameters['maxprice'] = Request::initial()->query('maxprice');
		}
		if (Request::initial()->query('category'))
		{
			$search_parameters['category_id'] = Request::initial()->query('category');
		}
		if (Request::initial()->query('sub_category'))
		{
			$search_parameters['sub_category_id'] = Request::initial()->query('sub_category');
		}
		if (Request::initial()->query('brand'))
		{
			$search_parameters['manufacturer_id'] = Request::initial()->query('brand');
		}
		if (Request::initial()->query('distributor_id'))
		{
			$search_parameters['distributor_id'] = Request::initial()->query('distributor');
		}

		$search_result = self::search($search_parameters, $offset, $limit);
		$all_products = $search_result['products'];
		$product_count = $search_result['count'];
		$data['product_categories'] = '';
		$data['base_file_path']     = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'products');
		if (count($all_products) > 0)
		{
			$data['products'] = array();
			$pages = ceil($product_count / $itemsperpage);
			$data['product_categories'] .= '<div class="pagedemo  _current">';
			foreach ($all_products as $product_data)
			{
				// Only show the local description
				if (Settings::instance()->get('localisation_content_active') == '1')
				{
					$product_data['description']       = Model_Localisation::get_ctag_translation($product_data['description'], I18n::$lang);
					$product_data['brief_description'] = Model_Localisation::get_ctag_translation($product_data['brief_description'], I18n::$lang);
				}

				$product_data['images'] = self::get_images($product_data['id']);
				$product_data['documents'] = self::get_documents($product_data['id']);
				$product_data['truncated_title'] = (strlen($product_data['title']) >= $max_title_length + 4)
					? trim(substr($product_data['title'], 0, $max_title_length))."..."
					: $product_data['title'];


				$data['product_categories'] .= View::factory('front_end/'.((
					Settings::instance()->get('product_listing_display') == 'vertical')
					? 'product_item_vertical_html'
					: 'product_item_html'
				), $product_data);
			}
			$data['product_categories'] .= '</div>';

			$data['count'] = $count;
			$data['itemsperpage'] = $itemsperpage;
			$data['pages'] = $pages;

			$data['product_categories'] .= View::factory('templates/default/pagination2', array('current_page' => $current_page, 'pages' => $pages, 'count' => $product_count, 'itemsperpage' => $itemsperpage));

			// Set the CSS and JS Files for this View
			$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/products_front_end_general.css'.'" rel="stylesheet">';
			$data['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/products_front_end_general.js"></script>';

			//Add the items to the container view and return the HTML
			return View::factory('front_end/product_category_list_html', $data);
		}
		else
		{
			IbHelpers::set_message('No products found.', 'info'); // 'error', 'info', 'success'
			echo IbHelpers::get_messages();
		}
	}

	public static function render_products_category_html($cascade = TRUE)
	{
		//While $products, fill the items
		if ($cascade)
		{
			$all_product_categories = Model_Category::get_all();
		}
		else
		{
			$plain_array = array();
			$all_product_categories = Model_Category::get(NULL, NULL, $plain_array, 0, $cascade);
		}
		if (empty($all_product_categories))
		{
			IbHelpers::set_message('Test message', 'info'); // 'error', 'info', 'success'
			return IbHelpers::get_messages();
		}
		$data['product_categories'] = '';
		$data['base_file_path'] = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'products');

		$skip_category = FALSE;
		$current_depth = 0;
		$current_mode=Session::instance()->get('display_mode');
		if(empty($current_mode)){$current_mode='grid';}
			 if($current_mode=='list'){
						$view='product_category_item_list_html';	 
					}elseif($current_mode=='thumb'){
						$view='product_category_item_thumb_html';
				    }else{
						$view='product_category_item_html';
				    }	
		$data['product_categories'] .='<div class="'.$current_mode.'">';
		foreach ($all_product_categories as $product_category)
		{
				
			if ($product_category['depth'] <= $current_depth)
			{
				$skip_category = FALSE;
			}

			if (!$skip_category)
			{
				if ($product_category['publish'] != 0)
				{
					$data['product_categories'] .= View::factory('front_end/'.$view, $product_category);
				}
				else
				{
					$skip_category = TRUE;
					$current_depth = $product_category['depth'];
				}
			}
		}
		$data['product_categories'] .='</div>';
		$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/product_display_mode.css'.'" rel="stylesheet">';

		//Add the items to the container view and return the HTML
		return View::factory('front_end/product_category_list_html', $data);
	}

//    public static function render_product_details(){
//        IbHelpers::set_message('Test message', 'info'); // 'error', 'info', 'success'
//        return View::factory('front_end/product_details');
//    }

	public static function render_products_menu()
	{
		//IbHelpers::set_message('Test message', 'info'); // 'error', 'info', 'success'

		// Set the CSS and JS Files for this View
		$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/products_front_end_general.css'.'" rel="stylesheet">';

		return View::factory('front_end/products_categories_menu_html', $data);
	}

	public static function render_related_products_html($related_products = array())
	{
		$related_products_html = '';

		// Render Related Products for Product-Details View
		foreach ($related_products as $related_id)
		{
            $related_product = self::get($related_id, array(array('publish', '=', 1)));

			if ($related_product)
			{
				$related_products_html .= View::factory(
					'front_end/snippets/related_product',
					array(
						'product_data' => $related_product
					)
				)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
			}
		}

		// Return
		return $related_products_html;
	}

	public static function render_featured_products_html()
	{
		$featured_products_html = '';
		$featured_products = self::get(
			NULL,
            array(
                array('featured', '=', 1),
                array('publish', '=', 1)
            )
		);

		// check if there is data to send to view otherwise do not show h1 output
		if ( ! empty($featured_products))
		{
			// list is not empty.
			$featured_products_html = "<h1>FEATURED PRODUCTS</h1>";
		}

		// Render Related Products for Product-Details View
		foreach ($featured_products as $featured_product)
		{
			$config         = Kohana::$config->load('config');
			$page_theme     = ( ! empty($config->assets_folder_path)) ? $config->assets_folder_path : '';
			$product_themes = Model_Product::get_theme($featured_product['id'], TRUE);

			// If the product's theme dose not match the page's theme, do not display it in the feed
			// Unless the product has no themes, in which case it will display
			if (empty($product_themes) OR in_array($page_theme, $product_themes))
			{
				$featured_products_html .= View::factory('front_end/snippets/featured_product',array('product_data' => $featured_product))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
			}
		}

		// Return
		return $featured_products_html;
	}

	public static function render_minicart_view()
	{

		/*
		 * Render the Mini Cart View from the same LOCATION as this was loaded in the: engine/application/controller/action_get_template Function
		 * @TODO: AS long as the: engine/application/controller/action_get_template Function is used to REWRITE the Mini Cart view -=> this should be provided like bellow
		 */
		return View::factory('templates/default/mini_cart')->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
	}

	public static function get_option1()
	{
		return DB::select('t1.option1', 't1.price', 't1.price_adjustment', 't2.label', 't2.description')
			->from(array(Model_Matrix::MATRIX_OPTIONS_TABLE, 't1'))
			    ->join(array(self::TABLE_OPTIONS_MAIN, 't2'), 'LEFT')->on('t1.option1', '=', 't2.id')
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')->on('t2.group_id', '=', 'groups.id')
			->group_by('t1.option1')
			->where('t2.publish', '=', 1)
			->and_where('t2.deleted', '=', 0)
			->execute()->as_array();
	}

	public static function get_option2()
	{
		return DB::select('t1.option2', 't1.price', 't1.price_adjustment', 't2.label', 't2.description')
			->from(array(Model_Matrix::MATRIX_OPTIONS_TABLE, 't1'))
			    ->join(array(self::TABLE_OPTIONS_MAIN, 't2'), 'LEFT')->on('t1.option2', '=', 't2.id')
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')->on('t2.group_id', '=', 'groups.id')
			->group_by('t1.option2')
			->where('t2.publish', '=', 1)
			->and_where('t2.deleted', '=', 0)
			->execute()->as_array();
	}

	public static function get_option3()
	{

	}

	public static function get_dimensions($option)
	{
		// take out bracketed text
		$size = explode(' (', trim($option));
		$size = isset($size[0]) ? $size[0] : '';

		switch ($size)
		{
			case 'A0':
				$width = 841;
				$height = 1189;
				break;
			case 'A1':
				$width = 594;
				$height = 841;
				break;
			case 'A2':
				$width = 420;
				$height = 594;
				break;
			case 'A3':
				$width = 297;
				$height = 420;
				break;
			case 'A4':
				$width = 210;
				$height = 297;
				break;
			case 'A5':
				$width = 148;
				$height = 210;
				break;
			default:
				$width = $height = 0;
				$pattern = '/([0-9]+)\s*(mm|cm|m|in|ft|inches|feet)\s+[x|?]\s+([0-9]+)\s*(mm|cm|m|in|ft|inches|feet)/';
				if (preg_match($pattern, $size, $matches))
				{
					$width = self::convert_to_mm($matches[1], $matches[2]);
					$height = self::convert_to_mm($matches[3], $matches[4]);
				}
				break;
		}
		return array('width' => $width, 'height' => $height);
	}

	private static function convert_to_mm($value, $unit)
	{
		switch ($unit)
		{
			case 'mm':
				$scale = 1;
				break;
			case 'cm':
				$scale = 10;
				break;
			case 'm':
				$scale = 100;
				break;
			case 'in':
			case 'inches':
				$scale = 25.4;
				break;
			case 'ft':
			case 'feet':
				$scale = 304.8;
				break;
			default:
				$scale = 0;
				break;
		}
		return $value * $scale;
	}


	public static function get_option_2_list($option1, $matrix_id)
	{
        return DB::select('t1.option2', 't1.price', 't1.price_adjustment', 't2.label', 't2.description', 't2.value', array('groups.group', 'group'))
			->from(array(Model_Matrix::MATRIX_OPTIONS_TABLE, 't1'))
			    ->join(array(self::TABLE_OPTIONS_MAIN, 't2'), 'LEFT')->on('t1.option2', '=', 't2.id')
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')->on('t1.option2', '=', 'groups.id')
			->where('t1.option1', '=', $option1)
			->and_where('matrix_id', '=', $matrix_id)
			->and_where('t1.publish', '=', 1)
			->execute()->as_array();
	}

	public static function get_matrix_price($option1 = NULL, $option2 = NULL, $product_id = NULL)
	{
		//Needs to be expanded for option 3.
		$result = 0;
		$q = DB::select('price', 'price_adjustment', 'publish')->from(Model_Matrix::MATRIX_OPTIONS_TABLE)->where('option1', '=', $option1)->and_where('option2', '=', $option2);

		if (!is_null($product_id))
		{
			$r = self::get_product_matrix_id($product_id);
			$q->and_where('matrix_id', '=', $r);
		}

		$q = $q->execute()->as_array();
		if (count($q) > 0)
		{
			$result = ($q[0]['price_adjustment'] == 1 AND $q[0]['publish'] == 1) ? $q[0]['price'] : 0;
		}

		return $result;
	}

	public static function get_product_matrix_id($product_id)
	{
		$r = DB::select('matrix')->from('plugin_products_product')->where('id', '=', $product_id)->execute()->as_array();
		return count($r) > 0 ? $r[0]['matrix'] : NULL;
	}

	public static function get_matrix_option_details($option1 = NULL, $option2 = NULL, $matrix_id = NULL)
	{
		//Needs to be expanded for option 3.
		$result = array();
		$image = '';
		$q = DB::select('price', 'price_adjustment', 'publish', 'image')
			->from(Model_Matrix::MATRIX_OPTIONS_TABLE)
			->where('option1', '=', $option1)
			->and_where('option2', '=', $option2)
			->and_where('matrix_id', '=', $matrix_id)
			->execute()->as_array();
		if (count($q) > 0)
		{
			if ($q[0]['image'] != 0)
			{
				$image = Model_Media::get_image_filename($q[0]['image']);
				$image = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $image['filename'], $image['location']);
			}
			$result = array('price' => $q[0]['price'], 'price_adjustment' => $q[0]['price_adjustment'], 'pubish' => $q[0]['publish'], 'image' => $image);
		}

		return $result;
	}

	public static function get_matrix_options_price($option1, $option2, $product_id = NULL)
	{
		$q = DB::select('t1.label', 't1.group_id', 'groups.group', 't2.price')
            ->from(array(self::TABLE_OPTIONS_MAIN, 't1'))
                ->join(array(Model_Matrix::MATRIX_OPTIONS_TABLE, 't2'), 'LEFT')->on('t1.id', '=', 't2.option1')
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')->on('t1.group_id', '=', 'groups.id')
            ->where('t1.id', 'in', array($option1, $option2))
			->and_where_open()
				->and_where_open()
					->and_where('t2.option1', '=', $option1)
					->and_where('t2.option2', '=', $option2)
				->and_where_close()
				->or_where_open()
					->and_where('t2.option1', '=', $option2)
					->and_where('t2.option2', '=', $option1)
				->or_where_close()
			->and_where_close();

		if (!is_null($product_id))
		{
			$matrix_id = self::get_product_matrix_id($product_id);
			$q->and_where('matrix_id', '=', $matrix_id);
		}
		$q = $q->execute()->as_array();

		$o1 = DB::select('t1.label', 't1.group_id', 'groups.group')
            ->from(array(self::TABLE_OPTIONS_MAIN, 't1'))
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')->on('t1.group_id', '=', 'groups.id')
            ->where('t1.id', '=', $option1)
            ->execute()
            ->as_array();

        $o2 = DB::select('t1.label', 't1.group_id', 'groups.group')
            ->from(array(self::TABLE_OPTIONS_MAIN, 't1'))
            ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')->on('t1.group_id', '=', 'groups.id')
            ->where('t1.id', '=', $option2)
            ->execute()
            ->as_array();

        $q_price     = isset($q[0]['price'])     ? $q[0]['price']     : '';

		$o1_group    = isset($o1[0]['group'])    ? $o1[0]['group']    : '';
        $o1_group_id = isset($o1[0]['group_id']) ? $o1[0]['group_id'] : '';
		$o1_label    = isset($o1[0]['label'])    ? $o1[0]['label']    : '';

		$o2_group    = isset($o2[0]['group'])    ? $o2[0]['group']    : '';
        $o2_group_id = isset($o2[0]['group_id']) ? $o2[0]['group_id'] : '';
		$o2_label    = isset($o2[0]['label'])    ? $o2[0]['label']    : '';

		return array(
			array(
				'id'       => $option1,
                'group_id' => $o1_group_id.' &times; '.$o2_group_id,
				'group'    => $o1_group.' &times; '.$o2_group,
				'label'    => ' '.$o1_label.' &times; '.$o2_label,
				'price'    => $q_price,
				'id2'      => $option2
			)
		);
	}

	public static function prepare_pdf()
	{
		$cart = Session::instance()->get('MODEL_CHECKOUT_CART');
		$layers = Session::instance()->get('canvas');

		if (!empty($layers))
		{
			foreach ($layers AS $key => $product)
			{
				if (Model_Product::check_builder_product($product['id']) AND isset($product['layers_obj']))
				{
					$type = $product['canvas_size'];
					if ($type != '' AND $type != 'Please select')
					{
						if ($product['orientation'] == 'landscape')
						{
							$width = $product['height'];
							$height = $product['width'];
						}
						else
						{
							$width = $product['width'];
							$height = $product['height'];
						}
						$layers_obj = json_decode($product['layers_obj']);
						$multiplier = $product['width'] / 210;
						$type = in_array($type, array('A0', 'A1', 'A2', 'A3', 'A4', 'A5')) ? $type : NULL;
						$image = $product['layers'];

						// Build a PDF using the data for each layer to position elements
						$pdf = new Model_ProductPDF($width, $height, 14 * $multiplier, $type);
						$render = View::factory('builder_pdf')
							->bind('layers_obj', $layers_obj)
							->bind('background_color', $product['background_color'])
							->render();
						$filename = $product['timestamp'].'.pdf';
						@$pdf->set_compression(FALSE)
							->set_title($product->product->title)
							->set_filename($filename)
							->set_display_mode('fullpage')
							->set_multiplier($multiplier)
							->set_html($render)
							->generate_pdf();
						$layers[$key]['filename'] = $filename;

						// Build a PDF by using the image generated from the canvas
						/*
						$pdf_raster = new Model_ProductPDF($width, $height, 14 * $multiplier, $type);
						$render_raster = View::factory('builder_pdf')->bind('image', $image)->bind('id', $product['raw_file'])->render();
						$filename_raster = $product['timestamp'].'-raster.pdf';
						$pdf_raster->set_compression(FALSE)
							->set_title($product->product->title)
							->set_filename($filename_raster)
							->set_display_mode('fullpage')
							->set_multiplier($multiplier)
							->set_html($render_raster)
							->generate_pdf();
						$layers[$key]['filename_raster'] = $filename_raster;
						*/
						$layers[$key]['canvas_image'] = $product['canvas_image'];
					}
					else
					{
						unset($layers[$key]);
					}
				}
			}
		}

		Session::instance()->set('canvas', $layers);
	}

	public static function px_to_mm($px)
	{
		return $px * 3.779528;
	}

	public static function add_canvas($id, $canvas_size, $layers, $filename, $orientation, $width, $height, $layers_obj, $background_color = '#ffffff', $canvas_image = FALSE)
	{
		$timestamp = time();
		$session = Session::instance();
		$canvases = isset($_SESSION['canvas']) ? $_SESSION['canvas'] : array();
		$canvas = array(
			'id' => $id,
			'canvas_size' => $canvas_size,
			'width' => $width,
			'height' => $height,
			'orientation' => $orientation,
			'layers' => $layers,
			'raw_file' => $filename,
			'timestamp' => $timestamp,
			'layers_obj' => $layers_obj,
			'background_color' => $background_color,
			'canvas_image' => $canvas_image
		);
		$canvases[] = $canvas;
		$session->set('canvas', $canvases);
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
		$ok = ($ok AND (strlen($this->main_table['title']) > 0));
		// $ok = ($ok AND (strrpos($this->main_table['title'], '-') === FALSE));

		return $ok;
	}

	/**
	 * Return an array ready to be used in an INSERT statement.
	 * @return array The array.
	 */
	private function build_insert_array()
	{
		$logged_user = Auth::instance()->get_user();
		$date = date('Y-m-d H:i:s', time());

		$array = array
		(
			'date_entered'  => $date,
			'date_modified' => $date,
			'created_by'    => $logged_user['id'],
			'modified_by'   => $logged_user['id'],
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
			'date_modified' => date('Y-m-d H:i:s')
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
		$product_code = isset($array['product_code']) ? $array['product_code'] : '';

		$array['title'] = $this->main_table['title'];
		$array['url_title'] = ($this->main_table['url_title'] != '') ? $this->main_table['url_title'] : self::format_url_title($array['title'], $product_code);
		$array['category_id'] = $this->main_table['category_id']; // to deprecate
		$array['price'] = $this->main_table['price'];
		$array['display_price'] = $this->main_table['display_price'];
		$array['disable_purchase'] = $this->main_table['disable_purchase'];
		$array['offer_price'] = $this->main_table['offer_price'];
		$array['display_offer'] = $this->main_table['display_offer'];
		$array['featured'] = $this->main_table['featured'];
		$array['brief_description'] = $this->main_table['brief_description'];
		$array['description'] = $this->main_table['description'];
		$array['product_code'] = $this->main_table['product_code'];
		$array['ref_code'] = $this->main_table['ref_code'];
		$array['weight'] = $this->main_table['weight'];
		$array['seo_title'] = $this->main_table['seo_title'];
		$array['seo_keywords'] = $this->main_table['seo_keywords'];
		$array['seo_description'] = $this->main_table['seo_description'];
		$array['seo_footer'] = $this->main_table['seo_footer'];
		$array['postal_format_id'] = $this->main_table['postal_format_id'];
		$array['out_of_stock'] = $this->main_table['out_of_stock'];
		$array['out_of_stock_msg'] = $this->main_table['out_of_stock_msg'];
		$array['size_guide'] = $this->main_table['size_guide'];
		$array['document'] = $this->main_table['document'];
		$array['min_width'] = $this->main_table['min_width'];
		$array['min_height'] = $this->main_table['min_height'];
		$array['order'] = $this->main_table['order'];
		$array['use_postage'] = $this->main_table['use_postage'];
		$array['publish'] = $this->main_table['publish'];
		$array['deleted'] = $this->main_table['deleted'];
		$array['quantity_enabled'] = $this->main_table['quantity_enabled'];
		$array['quantity'] = $this->main_table['quantity'];
		$array['builder'] = $this->main_table['builder'];
		$array['matrix'] = $this->main_table['matrix'];
		$array['over_18'] = $this->main_table['over_18'];
		$array['sign_builder_layers'] = $this->main_table['sign_builder_layers'];

		// Relationships
		$array['category_ids'] = $this->category_ids;
		$array['tag_ids'] = $this->tag_ids;
		$array['images'] = $this->images;
		$array['documents'] = $this->documents;
		$array['options'] = $this->options;
		$array['related_to'] = $this->related_to;
		$array['stock_options'] = $this->stock_options;
		$array['youtube_videos'] = $this->youtube_videos;
		foreach ($array['youtube_videos'] AS $key => $value)
		{
			$array['youtube_videos'][$key]->product_id = $this->main_table['id'];
		}
	}

	/**
	 * Set the default values (for the INSERT and UPDATE statements) for this object.
	 * @param $array
	 */
	private function set_default_values(&$array)
	{
		$array['category_id'] = ($array['category_id'] == '') ? NULL : $array['category_id']; // to deprecate
		$array['postal_format_id'] = ($array['postal_format_id'] == '') ? NULL : $array['postal_format_id'];
		$array['display_price'] = ($array['display_price'] == '') ? 1 : $array['display_price'];
		$array['disable_purchase'] = ($array['disable_purchase'] == '') ? 0 : $array['disable_purchase'];
		$array['display_offer'] = ($array['display_offer'] == '') ? 0 : $array['display_offer'];
		$array['featured'] = ($array['featured'] == '') ? 0 : $array['featured'];
		$array['out_of_stock'] = ($array['out_of_stock'] == '') ? 0 : $array['out_of_stock'];
		$array['publish'] = ($array['publish'] == '') ? 1 : $array['publish'];
		$array['deleted'] = ($array['deleted'] == '') ? 0 : $array['deleted'];
		$array['weight'] = ($array['weight'] == '') ? 0 : $array['weight'];
		$array['min_width'] = ($array['min_width'] == '') ? 0 : $array['min_width'];
		$array['min_height'] = ($array['min_height'] == '') ? 0 : $array['min_height'];
		$array['order'] = ($array['order'] == '') ? 0 : $array['order'];
	}

	// convert name to URL-friendly name
	private function format_url_title($title, $sku)
	{
        $url = ($sku == '') ? $title : $title . '-' . $sku;
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', $url));
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
		$r = DB::select('id', 'title', 'url_title', 'category_id', 'price', 'display_price', 'disable_purchase', 'offer_price', 'display_offer', 'featured', 'brief_description', 'description', 'product_code', 'ref_code', 'weight', 'seo_title', 'seo_keywords', 'seo_description', 'seo_footer', 'postal_format_id', 'out_of_stock', 'out_of_stock_msg', 'size_guide', 'document', 'min_width', 'min_height', 'order', 'publish', 'deleted', 'date_modified', 'date_entered', 'modified_by', 'created_by', 'quantity', 'quantity_enabled', 'builder', 'matrix', 'over_18', 'sign_builder_layers', 'use_postage')
			->from(self::MAIN_TABLE)
			->where('id', '=', $id)
            ->and_where('deleted', '=' , 0)
			->execute()
			->as_array();

		// Relationships
		if (count($r) > 0)
		{
			self::sql_add_relationships($r[0]);
		}

		return (count($r) > 0) ? $r[0] : FALSE;
	}

	/**
	 * Insert a new object.
	 * @param array $insert_array The insert array.
	 * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
	 */
	private static function sql_insert_object($insert_array)
	{
		$category_ids = $insert_array['category_ids'];
		$tag_ids = isset($insert_array['tag_ids']) ? $insert_array['tag_ids'] : array();
		$images = $insert_array['images'];
		$documents = $insert_array['documents'];
		$options = $insert_array['options'];
		$stock_options = $insert_array['stock_options'];
		$related_to = $insert_array['related_to'];
		$videos = $insert_array['youtube_videos'];

		unset($insert_array['category_ids']);
		unset($insert_array['tag_ids']);
		unset($insert_array['images']);
		unset($insert_array['documents']);
		unset($insert_array['options']);
		unset($insert_array['related_to']);
		unset($insert_array['stock_options']);
		unset($insert_array['youtube_videos']);

		$r = DB::insert(self::MAIN_TABLE, array_keys($insert_array))
			->values(array_values($insert_array))
			->execute();

		$ok = ($r[1] == 1);

		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_CATEGORIES, array('product_id', 'category_id'), $r[0], $category_ids));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_TAGS, array('product_id', 'tag_id'), $r[0], $tag_ids));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_IMAGES, array('product_id', 'file_name'), $r[0], $images));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_DOCUMENTS, array('product_id', 'document_name'), $r[0], $documents));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_OPTIONS, array('product_id', 'option_group_id', 'required', 'is_stock'), $r[0], $options));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_RELATED_TO, array('product_id_1', 'product_id_2'), $r[0], $related_to));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_OPTIONS_DETAILS, array('product_id', 'option_id', 'quantity', 'location', 'price', 'publish'), $r[0], $stock_options));
		$ok = ($ok AND self::sql_insert_relationship(self::YOUTUBE_VIDEOS, array('product_id', 'video_id'), $r[0], $videos));

		return $ok ? $r[0] : FALSE;
	}

	/**
	 * Update the data of an existing object.
	 * @param int   $id           The identifier of the object.
	 * @param array $update_array The update array.
	 * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
	 */
	private static function sql_update_object($id, $update_array)
	{
		$category_ids = $update_array['category_ids'];
		$tag_ids = isset($update_array['tag_ids']) ? $update_array['tag_ids'] : array();
		$images = $update_array['images'];
		$documents = $update_array['documents'];
		$options = $update_array['options'];
		$related_to = $update_array['related_to'];
		$stock_options = $update_array['stock_options'];
		$videos = $update_array['youtube_videos'];

		unset($update_array['category_ids']);
		unset($update_array['tag_ids']);
		unset($update_array['images']);
		unset($update_array['documents']);
		unset($update_array['options']);
		unset($update_array['related_to']);
		unset($update_array['stock_options']);
		unset($update_array['youtube_videos']);

		$r = DB::update(self::MAIN_TABLE)
			->set($update_array)
			->where('id', '=', $id)
			->execute();

		$ok = ($r >= 0);

		$ok = ($ok AND self::sql_remove_relationship(self::TABLE_CATEGORIES, 'product_id', $id));
		$ok = ($ok AND self::sql_remove_relationship(self::TABLE_TAGS, 'product_id', $id));
		$ok = ($ok AND self::sql_remove_relationship(self::TABLE_IMAGES, 'product_id', $id));
		$ok = ($ok AND self::sql_remove_relationship(self::TABLE_DOCUMENTS, 'product_id', $id));
		$ok = ($ok AND self::sql_remove_relationship(self::TABLE_OPTIONS, 'product_id', $id));
		$ok = ($ok AND self::sql_remove_relationship(self::TABLE_RELATED_TO, 'product_id_1', $id));
		$ok = ($ok AND self::sql_remove_relationship(self::TABLE_RELATED_TO, 'product_id_2', $id));
		$ok = ($ok AND self::sql_remove_relationship_multiple(self::TABLE_OPTIONS_DETAILS, array('product_id', 'option_id'), $stock_options));
		$ok = ($ok AND self::sql_remove_relationship(self::YOUTUBE_VIDEOS, 'product_id', $id));

		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_CATEGORIES, array('product_id', 'category_id'), $id, $category_ids));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_TAGS, array('product_id', 'tag_id'), $id, $tag_ids));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_IMAGES, array('product_id', 'file_name'), $id, $images));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_DOCUMENTS, array('product_id', 'document_name'), $id, $documents));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_OPTIONS, array('product_id', 'option_group_id', 'required', 'is_stock'), $id, $options));
		$ok = ($ok AND self::sql_insert_relationship(self::TABLE_RELATED_TO, array('product_id_1', 'product_id_2'), $id, $related_to));
		$ok = ($ok AND self::sql_insert_relationship_generic(self::TABLE_OPTIONS_DETAILS, array('option_id', 'product_id', 'quantity', 'price', 'location', 'publish'), $stock_options));
		$ok = ($ok AND self::sql_insert_relationship(self::YOUTUBE_VIDEOS, array('product_id', 'video_id'), $r[0], $videos));

		return $ok;
	}

	/**
	 * Add the relationships for this model to the object passed by reference.
	 * @param array $object The object to add the relationships.
	 */
	private static function sql_add_relationships(&$object)
	{
		// Categories
		$s = DB::select('category_id')
			->from(self::TABLE_CATEGORIES)
			->where('product_id', '=', $object['id'])
			->execute()
			->as_array();
		for ($i = 0, $object['category_ids'] = array(); $i < count($s); $i++)
		{
			array_push($object['category_ids'], $s[$i]['category_id']);
		}

		// Tags
		$s = DB::select('tag_id')
			->from(self::TABLE_TAGS)
			->where('product_id', '=', $object['id'])
			->execute()
			->as_array();
		for ($i = 0, $object['tag_ids'] = array(); $i < count($s); $i++)
		{
			array_push($object['tag_ids'], $s[$i]['tag_id']);
		}

		// Images
		$s = DB::select('file_name')
			->from(self::TABLE_IMAGES)
			->where('product_id', '=', $object['id'])
			->execute()
			->as_array();

		for ($i = 0, $object['images'] = array(); $i < count($s); $i++)
		{
			array_push($object['images'], $s[$i]['file_name']);
		}

		// Documents
		$s = DB::select('document_name')
			->from(self::TABLE_DOCUMENTS)
			->where('product_id', '=', $object['id'])
			->execute()
			->as_array();

		for ($i = 0, $object['documents'] = array(); $i < count($s); $i++)
		{
			array_push($object['documents'], $s[$i]['document_name']);
		}

		// Options
		$s = DB::select('options.option_group_id', 'options.required', 'options.is_stock', array('groups.group', 'option_group'))
			->from(array(self::TABLE_OPTIONS, 'options'))
                ->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')
                    ->on('options.option_group_id', '=', 'groups.id')
			->where('product_id', '=', $object['id'])
            ->and_where('groups.deleted', '=', 0)
			->execute()
			->as_array();

		for ($i = 0, $object['options'] = array(); $i < count($s); $i++)
		{
			array_push($object['options'], array(
                'group_id'        => $s[$i]['option_group_id'],
                'option_group_id' => $s[$i]['option_group_id'],
                'group'           => $s[$i]['option_group'],
                'option_group'    => $s[$i]['option_group'],
                'required'        => $s[$i]['required'],
                'is_stock'        => $s[$i]['is_stock']
            ));
		}

		// Related To
		$q1 = DB::select(array('product_id_1', 'product_id'))
			->from(self::TABLE_RELATED_TO)
			->where('product_id_2', '=', $object['id']);

		$q2 = DB::select(array('product_id_2', 'product_id'))
			->from(self::TABLE_RELATED_TO)
			->where('product_id_1', '=', $object['id'])
			->union($q1);

		$s = $q2->execute()->as_array();

		for ($i = 0, $object['related_to'] = array(); $i < count($s); $i++)
		{
			array_push($object['related_to'], $s[$i]['product_id']);
		}

		// Stock Options
		$s = DB::select('t1.product_id', 't1.option_id', 't1.quantity', 't1.location', 't1.price', 't1.publish', 't3.option_group_id', array('groups.group', 'option_group'))
			->from(array(self::TABLE_OPTIONS_DETAILS, 't1'))
				->join(array(self::TABLE_OPTIONS_MAIN, 't2'))
					->on('t1.option_id', '=', 't2.id')
				->join(array(self::TABLE_OPTIONS, 't3'))
					->on('t3.option_group_id', '=', 't2.group_id')
				->join(array(Model_Option::OPTION_GROUPS, 'groups'), 'left')
					->on('t3.option_group_id', '=', 'groups.id')
			->where('t1.product_id', '=', $object['id'])
			->and_where('t3.is_stock', '=', 1)
			->and_where('t3.product_id', '=', $object['id'])
			->execute()
			->as_array();
		for ($i = 0, $object['stock_options'] = array(); $i < count($s); $i++)
		{
			array_push($object['stock_options'], array('product_id' => $s[$i]['product_id'], 'option_id' => $s[$i]['option_id'], 'quantity' => $s[$i]['quantity'], 'location' => $s[$i]['location'], 'price' => $s[$i]['price'], 'publish' => $s[$i]['publish'], 'option_group_id' => $s[$i]['option_group_id'], 'option_group' => $s[$i]['option_group']));
		}

		//YouTube Videos
		$s = DB::select('product_id', 'video_id')->from(self::YOUTUBE_VIDEOS)->where('product_id', '=', $object['id'])->execute()->as_array();
		for ($i = 0, $object['youtube_videos'] = array(); $i < count($s); $i++)
		{
			array_push($object['youtube_videos'], array('video_id' => $s[$i]['video_id']));
		}
	}

	/**
	 * Insert a new relationship between this model and another model.
	 * @param string $table   The table name.
	 * @param array  $columns The columns name.
	 * @param int    $id      The object identifier of this model.
	 * @param array  $values  The values to be inserted.
	 * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
	 */
	private static function sql_insert_relationship($table, $columns, $id, $values)
	{
		$ok = TRUE;

		if (count($values) > 0)
		{
			$q = DB::insert($table, $columns);

			foreach ($values as $v)
			{
				$a = array($id);

				if (!is_object($v))
				{
					array_push($a, $v);
				}
				else
				{
					foreach ((array) $v as $p => $u)
					{
						$i = array_search($p, $columns);

						if ($i !== FALSE)
						{
							$a[$i] = $u;
						}
					}
				}

				$q->values($a);
			}

			$r = $q->execute();
			$ok = ($r[1] == count($values)) ? TRUE : FALSE;
		}

		return $ok;
	}

	private static function sql_insert_relationship_generic($table, $columns, $data)
	{
		$ok = TRUE;
		if (count($data) > 0)
		{
			try
			{
				$insert = array();
				$r = DB::insert($table, $columns);
				foreach ($data AS $key => $row)
				{
					if (is_object($row))
					{
						$row = (array) $row;
					}
					$r->values($row);

				}
				$r->execute();
			}
			catch (Exception $e)
			{
				$ok = FALSE;
			}
		}
		return $ok;
	}

	/**
	 * Remove a relationship between this model and another model.
	 * @param string $table  The table name.
	 * @param string $column The column name.
	 * @param int    $id     The object identifier of this model.
	 * @return bool If the function succeeded, it returns TRUE. Otherwise, it returns FALSE.
	 */
	private static function sql_remove_relationship($table, $column, $id)
	{
		$r = DB::delete($table)
			->where($column, '=', $id)
			->execute();

		return ($r >= 0);
	}

	private static function sql_remove_relationship_multiple($table, $columns, $values)
	{
		$ok = TRUE;
		try
		{
			foreach ($values AS $valuekey => $value)
			{
				if (is_object($value))
				{
					$value = (array) $value;
				}
				$r = DB::delete($table);
				foreach ($columns AS $key => $column)
				{
					if ($key > 0)
					{
						$r->and_where($column, '=', $value[$column]);
					}
					else
					{
						$r->where($column, '=', $value[$column]);
					}
				}
				$r->execute();
			}
		}
		catch (Exception $e)
		{
			$ok = FALSE;
		}
		return $ok;
	}

	/*
   * Please use this function to generate a list of products for the sites html sitemap
   * @return String content
   */

	public static function get_products_html_sitemap()
	{

		$sitemap = '<ul id="products-sitemap"> <li>Products: <ul>';

		$products = self::factory('Product')->get();

		foreach ($products as $product)
		{
			//Add ONLY Published Pages to the sitemap
			if ($product['publish'] == 1)
			{
				//products.html/Cooking-and-Eating-m/Army-mess-tins
				$sitemap .= '<li><a href="/products.html/'.$product['category'].'/'.$product['url_title'].'">'.$product['title'].'</a></li>';
			}
		}
		//end foreach

		$sitemap .= '</ul></li></ul>';

		return $sitemap;
	}

	//end of function

	public static function get_products_xml_sitemap()
	{

		$sitemap = '';

		$products = self::factory('Product')->get();

		foreach ($products as $product)
		{
			//Add ONLY Published Pages to the sitemap
			if ($product['publish'] == 1)
			{
				//products.html/Cooking-and-Eating-m/Army-mess-tins
				$url = 'products.html/'.$product['category'].'/'.Model::factory('Pages')->filter_name_tag($product['title']).'.html';
				$sitemap .= '<url><loc>'.URL::site(htmlentities($url)).'</loc></url>';
			}
		}
		//end foreach

		return $sitemap;
	}

	//end of function


	public static function get_special_offers()
	{
		$products = DB::select('product.title', 'product.url_title', 'product.price', 'product.offer_price', 'plugin_products_category.category', 'plugin_products_product_images.file_name', 'product.display_price', 'product.disable_purchase', 'product.display_offer')
			->from(array('plugin_products_product', 'product'))
			->join('plugin_products_product_categories', 'LEFT')->on('plugin_products_product_categories.product_id', '=', 'product.id')
			->join('plugin_products_category', 'LEFT')->on('plugin_products_product_categories.category_id', '=', 'plugin_products_category.id')
			->join('plugin_products_product_images', 'LEFT OUTER')->on('product.id', '=', 'plugin_products_product_images.product_id')
			->where('product.display_offer', '=', '1')
			->and_where('product.deleted', '=', '0')
			->and_where('product.publish', '=', '1')
			->and_where('plugin_products_category.publish', '=', '1')
			->and_where('plugin_products_category.deleted', '=', '0')
			->group_by('product.id')
			->execute()->as_array();

        $checkout_model = new Model_Checkout;
        foreach ($products as $product)
        {
            $discounts = $checkout_model->apply_discounts_to_product($product);
            $product['discounts'] = $discounts['discounts'];
            $product['discount_total'] = $discounts['discount_total'];
        }

        return $products;
	}

	public static function render_special_offers()
	{
		$products = self::get_special_offers();

		$return  = '<div class="pagedemo" id="special-offers-feed">';
		foreach ($products as $product)
		{
			$return .= View::factory('front_end/product_item_html', $product);
		}
		$return .= '</div>';

		return $return;
	}

	public static function get_order_types()
	{
		$current = Settings::instance()->get('product_listing_order');
		$order_types = ''.
			'<option value="order"'.($current == 'order' ? ' selected="selected"' : '').'>System</option>'.
			'<option value="title"'.($current == 'title' ? ' selected="selected"' : '').'>Name</option>'.
			'<option value="date_entered"'.($current == 'date_entered' ? ' selected="selected"' : '').'>Date added</option>';

		return $order_types;
	}

	public static function get_sort_types()
	{
		$current = Settings::instance()->get('product_listing_sort');
		$sort_types = ''.
			'<option value="ASC"'.($current == 'ASC' ? ' selected="selected"' : '').'>Ascending</option>'.
			'<option value="DESC"'.($current == 'DESC' ? ' selected="selected"' : '').'>Descending</option>';

		return $sort_types;
	}

	public static function get_listing_display_types()
	{
		$current = Settings::instance()->get('product_listing_display');
		$display_types = ''.
			'<option value="horizontal"'.($current == 'horizontal' ? ' selected="selected"' : '').'>Horizontal</option>'.
			'<option value="vertical"'.($current == 'vertical' ? ' selected="selected"' : '').'>Vertical</option>';

		return $display_types;
	}

	public static function get_details_display_types()
	{
		$current = Settings::instance()->get('product_details_display');
		$display_types = ''.
			'<option value="standard"'.($current == 'standard' ? ' selected="selected"' : '').'>Standard</option>'.
			'<option value="wide"'.($current == 'wide' ? ' selected="selected"' : '').'>Wide</option>';

		return $display_types;
	}

	public static function get_builder_product_area($product_format)
	{
		$sizes = array('A0' => 10.76, 'A1' => 5.38, 'A2' => '2.69', 'A3' => 1.35, 'A4' => 0.65, 'A5' => 0.32, 'A6' => 0.17);
		return $sizes[$product_format];
	}

	public static function get_builder_products($category_id = NULL)
	{
		$q = DB::select(
			'product.id',
			'product.title',
			'image.file_name',
			array('product.sign_builder_layers', 'layers')
		)
			->from(array('plugin_products_product', 'product'))
			->join(array('plugin_products_product_images', 'image'), 'left')->on('image.product_id', '=', 'product.id')
			->join(array('plugin_products_product_categories', 'pc'), 'left')->on('pc.product_id', '=', 'product.id')
			->where('product.builder', '=', 1)
			->where('product.publish', '=', 1)
			->where('product.deleted', '=', 0);

		if (!is_null($category_id))
		{
			$q->where('pc.category_id', '=', $category_id);
		}

		$q = $q
			->distinct(TRUE)
			->order_by('title')
			->execute()
			->as_array();

		return $q;
	}

	public static function get_product_canvas_index($timestamp)
	{
		$cart = Session::instance()->get('canvas');
		$index = FALSE;

		foreach ($cart as $key => $item)
		{
			$index = $timestamp == $item['raw_file'] ? $key : FALSE;
			if (is_numeric($index))
			{
				break;
			}
		}

		return $index;
	}

	public static function calculate_vat($price, $vat_rate = NULL, $price_includes_vat = FALSE)
	{
		if ($vat_rate == NULL)
		{
			$vat_rate = Settings::instance()->get('vat_rate');
		}
		if ($price_includes_vat)
		{
			$vat = round(($price / (1 + $vat_rate)) * $vat_rate, 2);
		}
		else
		{
			$vat = round($price * $vat_rate, 2);
		}
		return $vat;
	}

	public static function calculate_total_price($price, $vat)
	{
		return round($price * (1 + $vat), 2);
	}

	public static function get_images($product_id)
	{
		return DB::select()->from(self::TABLE_IMAGES)->where('product_id', '=', $product_id)->execute()->as_array();
	}

	public static function get_documents($product_id)
	{
		return DB::select()->from(self::TABLE_DOCUMENTS)->where('product_id', '=', $product_id)->execute()->as_array();
	}

	/**
	 * Get the theme for a product page, based on its category
	 *
	 * @param	int			$id		the ID of the product
	 * @param	bool		$all	set to TRUE to return an array of all themes applicable to the product
	 * @return	array|string		if $all is TRUE, return an array of all themes, otherwise just return the first theme, if any
	 */
	public static function get_theme($id, $all = FALSE)
	{
		$themes = array();
		// Get the IDs of all the product's categories
		$categories = DB::select(array('category_id', 'id'))
			->from(self::TABLE_CATEGORIES)
			->where('product_id', '=', $id)
			->execute()->as_array();

		// Get the theme of each category.
		foreach ($categories as $category)
		{
			$category_theme = Model_Category::get_theme($category['id']);
			if ($category_theme != '') $themes[] = $category_theme;
		}

		return ($all) ? $themes : (isset($themes[0]) ? $themes[0] : '');
	}

	// Get the author of a product.
	// The "author" is the name of a category, which is used by the product and a subcategory of a category called "Authors"
	public static function get_author($id)
	{
		return DB::select(array('category.category', 'author'))
			->from(array(self::CATEGORY_TABLE, 'category'))
			->join(array(self::CATEGORY_TABLE, 'parent'))->on('category.parent_id', '=', 'parent.id')
			->join(array(self::TABLE_CATEGORIES, 'pc'))->on('pc.category_id', '=', 'category.id')
			->join(array(self::MAIN_TABLE, 'product'))->on('pc.product_id', '=', 'product.id')
			->where('parent.category', '=', 'Authors')
			->where('category.publish', '=', 1)
			->where('category.deleted', '=', 0)
			->where('product.id', '=', $id)
			->execute()
			->get('author', '');
	}

	public static function get_localisation_messages()
	{
		$products = DB::select('title')->from(self::MAIN_TABLE)->execute()->as_array();
		$messages = array();
		foreach($products as $product){
			$messages[] = $product['title'];
		}
		return $messages;
	}

	public static function showcase($product_url)
	{
		$product = Model_Product::get_by_product_url($product_url);
		if ($product)
		{
			ob_start();
			echo View::factory('front_end/showcased_block')->set('product', $product);
			return ob_get_clean();
		}
		else
		{
			return '<span class="inline-error">Error: no product called "'.$product_url.'".</span>';
		}
	}

	public static function saveAutoFeature($post)
	{
		DB::delete(self::AUTOFEATURE_TABLE)->execute();
		if (isset($post['max_price'])) {
			foreach ($post['max_price'] as $i => $max_price) {
				DB::insert(self::AUTOFEATURE_TABLE)
					->values(array(
						'manufacturer_id' => @$post['manufacturer_id'][$i] ? $post['manufacturer_id'][$i] : null,
						'distributor_id' => @$post['distributor_id'][$i] ? $post['distributor_id'][$i] : null,
						'min_price' => @$post['min_price'][$i] ? $post['min_price'][$i] : null,
						'max_price' => @$post['max_price'][$i] ? $post['max_price'][$i] : null,
                        'numbers' => @$post['numbers'][$i] ? $post['numbers'][$i] : null,
					))->execute();
			}
		}
	}

	public static function getAutoFeatureList()
	{
		return DB::select('*')->from(self::AUTOFEATURE_TABLE)->execute()->as_array();
	}

	public static function getProductsFromAutoFeature()
    {
        $afList = self::getAutoFeatureList();
        $products = array();
        foreach ($afList as $autofeature) {
            $select = DB::select(
                'product.id',
                'sict_product.thumb_url'
            )
                ->from(array('plugin_sict_product', 'sict_product'))
                    ->join(array('plugin_sict_product_relation', 'sict_product_relation'))
                        ->on('sict_product.product_id', '=', 'sict_product_relation.sict_product_id')
                    ->join(array('plugin_products_product', 'product'))
                        ->on('product.id', '=', 'sict_product_relation.product_id')
                ->where('product.publish', '=', 1)
                ->and_where('product.deleted', '=', 0)
                ->and_where('sict_product.thumb_url', 'is not', null)
                ->and_where('sict_product.thumb_url', '<>', '');

            if ($autofeature['manufacturer_id'] != null) {
                $select->and_where('product.manufacturer_id', '=', $autofeature['manufacturer_id']);
            }
            if ($autofeature['distributor_id'] != null) {
                $select->and_where('product.distributor_id', '=', $autofeature['distributor_id']);
            }
            if ($autofeature['max_price'] != null) {
                $select->and_where('product.price', '<=', $autofeature['max_price']);
            }
            if ($autofeature['min_price'] != null) {
                $select->and_where('product.price', '>=', $autofeature['min_price']);
            }
            if ($autofeature['numbers'] != null) {
                $select->limit($autofeature['numbers']);
            }
            $select->order_by(DB::expr("rand()"));
            $products = array_merge($products, $select->execute()->as_array());
        }
        return $products;
    }

    public static function setFeaturedProductsFromAutoFeature()
    {
        $ids = array();
        foreach (self::getProductsFromAutoFeature() as $product) {
            $ids[] = $product['id'];
        }
		if (count($ids) > 0) {
            try{
				Database::instance()->begin();
                DB::update(self::MAIN_TABLE)
                    ->set(array('featured' => 0))
                    ->where('featured', '=', 1)
                    ->execute();
                DB::update(self::MAIN_TABLE)
                    ->set(array('featured' => 1))
                    ->where('id', 'in', $ids)
                    ->execute();
				Database::instance()->commit();
            } catch(Exception $exc){
				Database::instance()->rollback();
                throw $exc;
            }

		}
        return $ids;
    }
}
