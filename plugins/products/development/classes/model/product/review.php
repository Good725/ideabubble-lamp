<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Product_Review extends ORM
{
	protected $_table_name = 'plugin_products_reviews';

	protected $_belongs_to = array(
		'product'  => array('model' => 'Product_Product'),
		'creator'  => array('model' => 'User', 'foreign_key' => 'created_by'),
		'modifier' => array('model' => 'User', 'foreign_key' => 'modified_by')
	);


	public function save(Validation $validation = NULL)
	{
		// If this is the first save, set the IP address of the reviewer
		if ( ! $this->id)
		{
			$this->ip_address = Request::$client_ip;
		}

		// Continue with the regular ORM save function
		parent::save();
	}

	public function rules()
	{
		return array(
			'product_id' => array(array('not_empty')),
			'rating'     => array(array('not_empty')),
			'title'      => array(array('not_empty')),
			'review'     => array(array('not_empty')),
			'author'     => array(array('not_empty')),
			'email'      => array(array('not_empty'), array('email'))
		);
	}

	// Display a review, after applying formatting
	public function print_review()
	{
		$review = $this->review;

		// Normalize line endings
		$review = str_replace("\r", "\n", str_replace("\r\n", "\n", $review)); // Convert all line-endings to UNIX format
		$review = preg_replace("/\n{2,}/", "\n\n", $review); // Don't allow out-of-control blank lines

		// Convert double line breaks to HTML paragraphs and single line breaks to br
		$review = preg_replace('/\n(\s*\n)+/', '</p><p>', $review);
		$review = preg_replace('/\n/', '<br />', $review);
		$review = '<p>'.$review.'</p>';

		return $review;
	}

	//
	/**
	 * Print the rating in the form of radio buttons, which can be styled to look like stars	 *
	 * @param null 		$number		If set, IDs will be suffixed with this number.
	 * 								This should be used if there are multiple ratings on the same page
	 * 								to ensure the IDs are unique.
	 * @return string
	 */
	public function render_stars($number = NULL)
	{
		return View::factory('snippets/review_stars')
			->set('rating', $this->rating)
			->set('number', $number);
	}

	// Get the total number of published reviews for a product and the average rating
	public static function get_average_product_rating($product_id)
	{
		return DB::select(array(DB::expr('AVG(`rating`)'), 'average'))
			->from('plugin_products_reviews')
			->where('product_id', '=', $product_id)
			->where('publish', '=', 1)
			->where('deleted', '=', 0)
			->execute()
			->get('average', 0);
	}

	public static function get_for_datatable($filters)
	{
		$columns   = array();
		$columns[] = 'review.id';
		$columns[] = 'review.title';
		if ( ! isset($filters['product_id']))
		{
		 	// Don't display the "Product" column, when listing reviews for a specific product
			$columns[] = 'product.title';
		}
		$columns[] = 'review.rating';
		$columns[] = 'review.author';
		$columns[] = 'review.email';
		$columns[] = 'review.date_created';
		$columns[] = 'review.date_modified';
		$columns[] = DB::expr("IF (`creator`.`email` = '', `review`.`ip_address`, `creator`.`email`)"); // created by
		$columns[] = ''; // actions
		$columns[] = 'review.publish';

		$q = DB::select(
			'review.id',
			'review.title',
			array('product.title', 'product'),
			'review.rating',
			'review.author',
			'review.email',
			'review.date_created',
			'review.date_modified',
			array(DB::expr("IF (`creator`.`email` IS NULL, `review`.`ip_address`, `creator`.`email`)"), 'creator'),
			'review.publish'
		)
			->from(array('plugin_products_reviews', 'review'))
			->join(array('plugin_products_product', 'product'), 'left')->on('review.product_id', '=', 'product.id')
			->join(array('engine_users', 'creator'), 'left')->on('review.created_by', '=', 'creator.id')
			->where('review.deleted', '=', 0)
			;

		if (isset($filters['product_id']))
		{
			$q->where('product_id', '=', $filters['product_id']);
		}

			// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$q->and_where_open();
			for ($i = 0; $i < count($columns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $columns[$i] != '')
				{
					$q->or_where($columns[$i],'like','%'.$filters['sSearch'].'%');
				}
			}
			$q->and_where_close();
		}
		// Individual column search
		for ($i = 0; $i < count($columns); $i++)
		{
			if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
			{
				if ($columns[$i] instanceof Database_Expression AND strpos($columns[$i]->value(), 'GROUP_CONCAT') !== FALSE)
				{
					$q->having($columns[$i], 'like', '%'.$filters['sSearch_'.$i].'%');
				}
				else
				{
					$q->and_where($columns[$i], 'like', '%'.$filters['sSearch_'.$i].'%');
				}
			}
		}

		// $q_all will be used to count the total number of records.
		// It's largely the same as the main query, but won't be paginated
		$q_all = clone $q;

		// Limit. Only show the number of records for this paginated page
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$q->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$q->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order
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
		$q->order_by('review.date_modified', 'desc');

		$results = $q->execute()->as_array();

		$output['iTotalDisplayRecords'] = $q_all->execute()->count(); // total number of results
		$output['iTotalRecords']        = $q->execute()->count(); // displayed results
		$output['aaData']               = array();

		// Data to appear in the outputted table cells
		foreach ($results as $result)
		{
			$row   = array();
			$row[] = $result['id'];
			$row[] = $result['title'];
			if ( ! isset($filters['product_id']))
			{
				// Don't display the "Product" column, when listing reviews for a specific product
				$row[] = $result['product'];
			}
			$row[] = $result['rating'];
			$row[] = $result['author'];
			$row[] = $result['email'];
			$row[] = IbHelpers::relative_time_with_tooltip($result['date_created']);
			$row[] = IbHelpers::relative_time_with_tooltip($result['date_modified']);
			$row[] = $result['creator'];
			$row[] = View::factory('snippets/review_actions')
				->set('id', $result['id'])
				->set('skip_comments_in_beginning_of_included_view_file', TRUE)
				->render();
			$row[] = '<button type="button" class="btn-link publish-btn" data-id="'.$result['id'].'">
						<span class="hidden publish-value">'.$result['publish'].'</span>
						<span class="publish-icon icon-'.($result['publish'] ? 'ok' : 'ban-circle').'"></span>
					</button>';
			$output['aaData'][] = $row;
		}

		$output['sEcho'] = isset($filters['sEcho']) ? intval($filters['sEcho']) : 0;

		return json_encode($output);

	}

}
