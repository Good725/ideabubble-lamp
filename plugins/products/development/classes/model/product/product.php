<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Product_Product extends ORM
{
	protected $_table_name = 'plugin_products_product';
	protected $_date_created_column  = 'date_entered';

	protected $_has_many = array(
		'categories' => array('model' => 'Product_Category', 'through' => 'plugin_products_product_categories', 'foreign_key' => 'product_id'),
		'reviews'    => array('model' => 'Product_Review'),

        'material' => ['model' => 'product', 'far_key' => 'product_id', 'foreign_key' => 'spec_id', 'through' => 'plugin_courses_specs_have_recommended_material']
    );


    function save_relationships($data)
	{
		$db    = Database::instance();
		$saved = FALSE;
		if ( ! is_null($db) AND $db->begin())
		{
			try
			{
				// Save the product
				$this->values($data)->save_with_moddate();

				/* Save the product-category relationships */
				$category_ids = isset($data['category_ids']) ? $data['category_ids'] : array();

				// Remove existing relationships
				DB::delete('plugin_products_product_categories')->where('product_id', '=', $this->id)->execute();
				// Add new relationships
				if (count($category_ids) > 0)
				{
					$this->add('categories', array_unique($category_ids));
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

	function get_average_rating()
	{
		$total = 0;
		$count = 0;
		foreach ($this->reviews->find_all_published() as $review)
		{
			$total += $review->rating;
			$count++;
		}
		$average = ($count == 0) ? 0 : $total / $count;

		return $average;
	}

}