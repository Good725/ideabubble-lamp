<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Product_Category extends ORM
{
	protected $_table_name          = 'plugin_products_category';
	protected $_date_created_column = 'date_entered';

    protected $_has_many = [
        'children'  => ['model' => 'Product_Category', 'foreign_key' => 'parent_id'],
        'inventory' => ['model' => 'Inventory',        'foreign_key' => 'category_id'],
        'products'  => ['model' => 'Product_Product', 'through' => 'plugin_products_product_categories', 'foreign_key' => 'category_id'],
    ];

	protected $_belongs_to = array(
		'parent' => array('model' => 'Product_Category')
	);

	// Filter that can be used with the ORM query builder to get top level categories
	// e.g. ORM::factory('Product_Category')->where_top_level()->find_all();
	function where_top_level()
	{
		return $this
			->and_where_open()
				->where('parent_id', '=', 0)
				->or_where('parent_id', 'is', NULL)
			->and_where_close();
	}

	// Filter that can be used with the ORM query builder to get categories in the current theme
	// e.g. $category->children->where_in_theme()->find_all_published();
	// This will select categories using the current theme or no theme.
	// This will not check the theme of the category's parents! So a category whose parent is in a different theme could still appear.
	function where_in_theme()
	{
		$theme = Kohana::$config->load('config')->get('assets_folder_path');
		return $this
			->and_where_open()
				->where('theme', '=', $theme)
				->or_where('theme', '=', '')
				->or_where('theme', 'is', NULL)
			->and_where_close();
	}

	function save_relationships($data)
	{
		$db          = Database::instance();
		$saved = FALSE;
		if ( ! is_null($db) AND $db->begin())
		{
			try
			{
				// Save the category
				$this->values($data)->save_with_moddate();

				/* Save the product-category relationships */
				$product_ids = isset($data['product_ids']) ? $data['product_ids'] : array();

				// Remove existing relationships
 				DB::delete('plugin_products_product_categories')->where('category_id', '=', $this->id)->execute();
				// Add new relationships
				if (count($product_ids) > 0)
				{
					$this->add('products', array_unique($product_ids));
				}

				$db->commit();
				$saved = TRUE;
			}
			catch (Exception $e)
			{
				Log::instance()->add(Log::ERROR, $e->getTraceAsString());
				// If one save fails, rollback all changes
				$db->rollback();
				$saved = FALSE;
			}
		}
		return $saved;
	}

}