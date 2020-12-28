<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Products extends Controller_Template
{
	public $template = 'plugin_template';

	/**
	 *
	 */
	public function action_checkout_add_to_cart()
	{
		$model = new Model_Checkout();
		$this->response->body(json_encode($model->add_to_cart(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}
	
	/**
	 *
	 */
	public function action_checkout_delete_from_cart()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->delete_from_cart(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}

	/**
	 *
	 */
	public function action_checkout_modify_cart()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->modify_cart(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}

	/**
	 *
	 */
	public function action_checkout_set_postal_zone()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->set_postal_zone(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}

	public function action_checkout_set_country()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->set_country(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}

	public function action_checkout_set_delivery_method()
	{
		$model = new Model_Checkout;
		$post = $this->request->post();
		$this->response->body(json_encode($model->set_delivery_method(json_decode($post['data']))));
		$this->auto_render = FALSE;
	}

	public function action_checkout_set_store_id()
	{
		$model = new Model_Checkout;
		$post = $this->request->post();
		$this->response->body(json_encode($model->set_store_id(json_decode($post['data']))));
		$this->auto_render = FALSE;
	}

	/**
	 *
	 */
	public function action_checkout_set_location()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->set_location(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}

	/**
	 *
	 */
	public function action_checkout_set_coupon_code()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->set_coupon_code(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}

	/**
	 *
	 */
	public function action_checkout_set_delivery_time()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->set_delivery_time(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}

    public function action_checkout_set_gift_option()
    {
        $model = new Model_Checkout();

        $this->response->body(json_encode($model->set_gift_option(json_decode($_POST['data']))));
        $this->auto_render = FALSE;
    }

	/**
	 *
	 */
	public function action_checkout_get_paypal_form()
	{
		$this->auto_render = FALSE;
		$data = json_decode($_POST['data']);
		parse_str($data->form_data, $form_data);

		// Log the cart. The cart ID will be needed in the PayPal callback to get the checkout details
		if (Settings::instance()->get('cart_logging') == "TRUE")
		{
			$user        = Auth::instance()->get_user();
			$checkout    = new Model_Checkout;
			$cart        = $checkout->get_cart();
			//echo '<pre>';
			//print_r($cart->data);die;
			$cart_report = new Model_Cart($cart->data->id);
			$details     = array(
				'id'            => $checkout->get_cart_id(),
				'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
				'ip_address'    => $_SERVER['REMOTE_ADDR'],
				'user_id'       => isset($user['id']) ? $user['id'] : NULL,
				'cart_data'     => json_encode($cart),
				'form_data'     => json_encode($form_data),
				'paid'          => 0,
				'date_created'  => date('Y-m-d H:i:s'),
				'date_modified' => date('Y-m-d H:i:s')
			);
			$cart_report->set($details);
			$cart_report_products = new Model_Cartitems(NULL);
			$cart_report_products->set_cart_id($cart_report->get_id());
			if (isset($cart->data->lines))
			{
				$cart_report_products->set($cart->data->lines);
			}
			$cart_report->save();
			$cart_report_products->save();
			$data->custom = $cart_report->get_id();

			/* save the cart discount */
			$discount_displayed_arr = array();
			$user = Auth::instance()->get_user();
			if(!empty($user) && isset($user['id']) && !empty($user['id'])){
				$user_id = $user['id'];
			}else{ //else get random int number for user id
				$user_id = rand();
			}	
			$discount_displayed_arr['user_id'] = $user_id;
			$discount_displayed_arr['cart_id'] = $cart->data->id;
			$discount_displayed_arr['session_id'] = session_id();
			$discount_displayed_arr['ip'] = $_SERVER['REMOTE_ADDR'];
			if(isset($cart->data->displayed_discount_type_ids)){
				$discount_displayed_arr['displayed_discount_type_ids'] = $cart->data->displayed_discount_type_ids;
			}	

			$results = DB::insert('plugin_products_discount_displayed', array_keys($discount_displayed_arr))->values($discount_displayed_arr)->execute();
			$discount_displayed_id = $results[0]; // last insert id

			/* save info table data*/
			/* save cart_based_price_discounts*/
			if(isset($cart->data->cart_based_price_discounts) && !empty($cart->data->cart_based_price_discounts) && sizeof($cart->data->cart_based_price_discounts) > 0)
			{
				foreach($cart->data->cart_based_price_discounts as $save_arr)
				{
					$discount_displayed_info_arr = array();
					$discount_displayed_info_arr['discount_displayed_id'] = $discount_displayed_id;
					$discount_displayed_info_arr['discount_type_id'] = $save_arr['id'];
					$discount_displayed_info_arr['discount_percentage'] = $save_arr['discount_rate_percentage'];
					$discount_displayed_info_arr['discount'] = $cart->data->cart_price*$save_arr['discount_rate_percentage']/100;
					DB::insert('plugin_products_discount_displayed_info', array_keys($discount_displayed_info_arr))->values($discount_displayed_info_arr)->execute();
				}
			}
			
			if(isset($cart->data->cart_based_free_shipping_discounts) && !empty($cart->data->cart_based_free_shipping_discounts) && sizeof($cart->data->cart_based_free_shipping_discounts) > 0)
			{
				foreach($cart->data->cart_based_free_shipping_discounts as $save_arr)
				{
					$discount_displayed_info_arr = array();
					$discount_displayed_info_arr['discount_displayed_id'] = $discount_displayed_id;
					$discount_displayed_info_arr['discount_type_id'] = $save_arr['id'];
					$discount_displayed_info_arr['discount_percentage'] = 0;
					$discount_displayed_info_arr['discount'] = 0;
					DB::insert('plugin_products_discount_displayed_info', array_keys($discount_displayed_info_arr))->values($discount_displayed_info_arr)->execute();
				}
			}
			
			if(isset($cart->data->cart_based_qty_discounts) && !empty($cart->data->cart_based_qty_discounts) && sizeof($cart->data->cart_based_qty_discounts) > 0)
			{
				foreach($cart->data->cart_based_qty_discounts as $save_arr)
				{
					$discount_displayed_info_arr = array();
					$discount_displayed_info_arr['discount_displayed_id'] = $discount_displayed_id;
					$discount_displayed_info_arr['discount_type_id'] = $save_arr['id'];
					$discount_displayed_info_arr['discount_percentage'] = $save_arr['discount_rate_percentage'];
					$discount_displayed_info_arr['discount'] = $cart->data->cart_price*$save_arr['discount_rate_percentage']/100;
					DB::insert('plugin_products_discount_displayed_info', array_keys($discount_displayed_info_arr))->values($discount_displayed_info_arr)->execute();
				}
			}
		}

		$model                   = new Model_Checkout();
		$return                  = $model->get_paypal_data($data);
		$return->data->test_mode = (Settings::instance()->get('paypal_test_mode') == 1);

		$this->response->body(json_encode($return));
	}

	/**
	 *
	 */
	public function action_checkout_get_line_id()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->get_line_id(json_decode($_POST['data']))));
		$this->auto_render = FALSE;
	}

	/**
	 * @return JSON the cart session variable
	 */

	public function action_checkout_get_cart_summary()
	{
		$model = new Model_Checkout();

		$this->response->body(json_encode($model->get_cart()));
		$this->auto_render = FALSE;
	}

	/**
	 *
	 */
	public function action_render_checkout_html()
	{
		$checkout_model = new Model_Checkout();
		$products = $checkout_model->get_cart_details();

		$data['postage'] = Model_PostageZone::get_all_published();

		if (class_exists('Model_Location'))
		{
			$data['locations'] = Model_Location::get_all_published();
		}
		else
		{
			$data['locations'] = array();
		}

		$data['products_list'] = '';

		if (!empty($products))
		{
			$data['number_of_items'] = $products->number_of_items;
			$data['cart_price'] = $products->cart_price;
			$data['shipping_price'] = $products->shipping_price;
			$data['final_price'] = $products->final_price;
			$data['subtotal'] = $products->subtotal;
			$data['subtotal2'] = $products->subtotal2;
			$data['discounts'] = $products->discounts;

			if (!isset($data['final_price']) OR empty($data['final_price']))
			{
				$data['final_price'] = $products->cart_price;
			}

			$data['zone_id'] = @$products->zone_id;
			$data['paypal_enabled'] = Model_Checkout::is_paypal_enabled();

			foreach ($products->lines as $key => $line)
			{
				$data_line['line'] = $line;
				$data_line['line_id'] = $key;
				$data_line['product'] = Model_Product::get($line->product->id);

				$data['products_list'] .= View::factory('front_end/checkout_productline_html', $data_line);
			}
		}

		// Set the CSS and JS Files for this View
		$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/jqueryui-lightness/jquery-ui-1.10.3.min.css" rel="stylesheet">';
		$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/checkout.css'.'" rel="stylesheet">';
		$data['view_css_files'][] = '<link href="'.URL::get_engine_plugin_assets_base('products').'css/front_end/products_front_end_general.css" rel="stylesheet">';
		// For SOME Reason there is 2 Checkout JS Files that need to be loaded
		$data['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/jquery-ui-1.10.3.min.js"></script>';
		$data['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/checkout.js"></script>';
		$data['view_js_files'][] = '<script type="text/javascript" src="'.URL::get_engine_plugin_assets_base('products').'js/front_end/checkout.js"></script>';

		$this->template->body = View::factory('front_end/checkout_html', $data);
	}

	public function action_check_stock_levels()
	{
		$this->auto_render = FALSE;
		$post = $this->request->post();
		$product_id = isset($post['product_id']) ? $post['product_id'] : NULL;
		$option_id = isset($post['option_id']) ? $post['option_id'] : NULL;
		$result = Model_Product::check_stock_levels($product_id, $option_id);
		$this->response->body(json_encode($result));
	}

	public function action_available_images()
	{
		$this->auto_render = FALSE;
		$id = $this->request->param('id');
		$term = $this->request->query('term');
		$media_model = new Model_Media();
		$images = $media_model->get_images_by_preset($id, $term, 'Signs -');
		$filepath = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', '').'products/_thumbs/';
		$filepath = str_replace(URL::base(), '/', $filepath);
		echo (string) View::factory('front_end/available_images')->set(array('products' => $images, 'filepath' => $filepath));
	}

	public function action_matrix_price()
	{
		$this->auto_render = FALSE;
		$post = $this->request->post();
		$option1 = isset($post['option1']) ? $post['option1'] : NULL;
		$option2 = isset($post['option2']) ? $post['option2'] : NULL;
		$product_id = isset($post['product_id']) ? $post['product_id'] : NULL;
		$this->response->body(Model_Product::get_matrix_price($option1, $option2, $product_id));
	}

	public function action_add_canvas()
	{
		$this->auto_render = FALSE;
		$post = $this->request->post();
		$error = '';

		if (Settings::instance()->get('sign_builder_area_restriction') == 1)
		{
			switch (Settings::instance()->get('sign_builder_area_units'))
			{
				case 'mm':
					$coeff = 1;
					break;
				case 'cm':
					$coeff = 10;
					break;
				case 'm' :
					$coeff = 100;
					break;
				case 'in':
					$coeff = 25.4;
					break;
				case 'ft':
					$coeff = 304.8;
					break;
				default  :
					$coeff = 1;
					break;
			}
			$min_area = Settings::instance()->get('sign_builder_min_area') * $coeff * $coeff;
			$max_area = Settings::instance()->get('sign_builder_max_area') * $coeff * $coeff;
			$area = $post['width'] * $post['height'];

			$error = ($area < $min_area) ? '<p>Sign is too small. Minimum area is '.$min_area.' mm&sup2;</p>' : $error;
			$error = ($area > $max_area) ? '<p>Sign is too large. Maximum area is '.$max_area.' mm&sup2;</p>' : $error;
		}

		if ($error != '')
		{
			$this->response->body(json_encode(array('error' => $error)));
		}
		else
		{
			/*
             * $post['layers']     image generated by the layers
             * $post['layers_obj'] object containing the data for each layer
             */

			$filename     = time();
			$image        = $post['layers'];
			$data         = $image;
			$canvas_image = FALSE;
			if (strpos($data, ';'))
			{
				list(, $data) = explode(';', $data);
				if (strpos($data, ','))
				{
					list(, $data) = explode(',', $data);
					$data         = str_replace(' ', '+', $data);
					$data         = base64_decode(chunk_split($data));
					$canvas_image = '/tmp/product_'.$filename.'.png';

					file_put_contents($canvas_image, $data);
				}
			}

			Model_Product::add_canvas(
				$post['id'],
				$post['canvas_size'],
				$data,
				$filename,
				$post['orientation'],
				$post['width'],
				$post['height'],
				$post['layers_obj'],
				$post['background_color'],
				$canvas_image
			);
			//$this->response->headers("content-type: image/png");
			$this->response->body(json_encode(array('timestamp' => $filename)));
		}
	}

	public function action_ajax_search_autocomplete()
	{
		$this->auto_render = FALSE;
		$get = $this->request->query();
		$term = isset($get['term']) ? $get['term'] : '';

		if ($term != '')
		{
			$product_model = new Model_Product();
			$products = json_decode($product_model->get_products_json($term, TRUE));
			$categories = json_decode($product_model->get_categories_json($term, TRUE));
			$tagged_products = Model_ProductTag::get_products_for_autocomplete($term, TRUE);
			$ac_items = array();
			$link = Model_Product::get_products_plugin_page();
			if ($products->count > 0)
			{
				$show_pre=0;
                if (Settings::instance()->get('product_search_img_preview') == 'TRUE'): 
                $show_pre=1;
                endif;
				foreach ($products->results as $product)
				{
					$product = (array) $product;
                    if (isset($product['url_title']) AND trim($product['url_title']))
                    {
                        $url_title = $product['url_title'];
			        }
                    else
                    {
                        $url_title = urlencode(str_replace(' ', '-', $product['title']));
                    }

					$ac_item['id'] = $product['id'];
					$ac_item['category'] = 'Products';
					$ac_item['label'] = preg_replace("/($term)/i", "<b>$1</b>", trim($product['product_code'].' '.$product['title']));
					$ac_item['link'] = '/'.$link.'/'.$url_title;
					$ac_item['file_name']= Model_Media::get_path_to_media_item_admin(Kohana::$config->load("config")->project_media_folder,$product['file_name'], rtrim(Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR, "/"));
					$ac_item['preview_status'] = $show_pre;
					$ac_items[] = $ac_item;
				}
			}
			$show_pre=0;
			if ($categories->count > 0)
			{
				foreach ($categories->results as $category)
				{
					$category = (array) $category;
					$ac_item['id'] = $category['id'];
					$ac_item['category'] = 'Categories';
					$ac_item['label'] = preg_replace("/($term)/i", "<b>$1</b>", $category['category']);
					$ac_item['link'] = '/'.$link.'/'.urlencode(str_replace(' ', '-', $category['category']));
					$ac_item['preview_status'] = $show_pre;
					$ac_items[] = $ac_item;
				}
			}
			if ($tagged_products['count'] > 0)
			{
				foreach ($tagged_products['results'] as $product)
				{
					$ac_item['id'] = $product['id'];
					$ac_item['category'] = 'Labels';
					$ac_item['label'] = preg_replace("/($term)/i", "<b>$1</b>", trim(trim($product['tag']).': '.$product['title']));
					$ac_item['link'] = '/'.$link.'/'.urlencode(str_replace(' ', '-', $product['title']));
					$ac_item['preview_status'] = $show_pre;
					$ac_items[] = $ac_item;
				}

			}
		}
		else
		{
			$ac_items['results'] = array();
			$ac_items['count'] = 0;

		}
		echo json_encode($ac_items);

	}

	public function action_get_matrix_option_details()
	{
		$this->auto_render = false;
		$query   = $this->request->query();
		$option1 = $query['option1'];
		$option2 = $query['option2'];
		$matrix  = $query['matrix_id'];
		$data    = Model_Product::get_matrix_option_details($option1, $option2, $matrix);
		$this->response->body(json_encode($data));
	}

	public function action_get_option_1_list()
	{
		$this->auto_render = FALSE;
		$post = $this->request->post();
		$option1 = $post['option2'];
		$matrix = $post['matrix_id'];
		$data = Model_Product::get_option_1_list($option1, $matrix);
		$this->response->body(json_encode($data));
	}

	public function action_get_option_2_list()
	{
		$this->auto_render = FALSE;
		$post = $this->request->post();
		$option1 = $post['option1'];
		$matrix = $post['matrix_id'];
		$data = Model_Product::get_option_2_list($option1, $matrix);
		$this->response->body(json_encode($data));
	}

	public function action_stripe_charge()
	{
		require_once APPPATH.'/vendor/stripe/lib/Stripe.php';
		$stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
		$stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
		$stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
		Stripe::setApiKey($stripe['secret_key']);

		$this->auto_render = FALSE;
		$post = $this->request->post();
		$token = $post['token'];
		$customer = Stripe_Customer::create(array(
			'description' => 'Customer for '.$token['email'],
			'card' => $token['id']
		));

		$checkout_model = new Model_checkout();
		$checkout = $checkout_model->get_cart();
		$products = $checkout->data->lines;
		$amount = 0;
		foreach ($products as $product)
		{
			$amount .= $product->product->price;
		}


		try
		{
			$charge = Stripe_Charge::create(array(
				'customer' => $customer->id,
				'amount' => $amount * 100, // convert to cents
				'currency' => 'eur'
			));
			$return = array('success' => TRUE, 'amount' => $amount);
		}
		catch (Stripe_CardError $e)
		{
			$return = array('success' => FALSE, 'error' => $e);
		}

		return json_encode($return);

	}

	public function action_set_over_18()
	{
		Session::instance()->set('over_18', TRUE);
	}

	public function action_get_latest_pdf()
	{
		$this->auto_render = FALSE;
		Model_Product::prepare_pdf();
		$canvas = Session::instance()->get('canvas');
		$canvas_size = count($canvas);
		$image = $canvas[$canvas_size - 1];
		$this->response->send_file('/var/tmp/'.$image['filename'], $image['filename'], array('mime_type' => File::mime_by_ext('pdf')));
	}

	// Moved to cardbuilder plugin. @TODO Remove this function later
	public static function render_card_builder()
	{
		return View::factory('front_end/card_builder');
	}

	public function action_check_quantity_price()
	{
		$this->auto_render = FALSE;
		$quantity = $this->request->post('quantity');
		$price = $this->request->post('base_price');

		if (method_exists('Model_Discount', 'get_all_discounts'))
		{
			$product = new stdClass();
			$product->price = $price;
			$product->quantity = $quantity;
			$lines = array();
			$lines[] = $product;
			$cart = new stdClass();
			$cart->lines = $lines;
			Session::instance()->set('offers_cart', $cart);
			$discounts = Model_Discount::get_all_discounts();
			$amount = 0;
			foreach ($discounts as $item)
			{
				$discount = Model_Discount::create($item['id']);
				$amount += $discount->apply_discount(9);
				if ($amount != 0)
				{
					$discount_to_apply = $discount;
					break;
				}
			}
			$this->response->body(json_encode(array(
					'amount' => $amount,
					'percent_off' => isset($discount_to_apply) ? $discount_to_apply->get_y() : 0,
					'total_amount' => (($price * $quantity) - $amount),
                    'unit_price' => ($price - ($amount / $quantity))
                )
			));
		}
	}

	public function action_ajax_list_sign_builders()
	{
		$this->auto_render = FALSE;
		$id = $this->request->param('id');
		$sign_builders = Model_Product::get_builder_products($id);
		$filepath = HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR));
		$view = View::factory('front_end/list_sign_builders')->set('sign_builders', $sign_builders)->set('filepath', $filepath);
		$this->response->body($view);
	}

	public function action_ajax_update_county_list()
	{
		$this->auto_render = FALSE;
		$post = $this->request->post();
		$region = isset($post['zone']) ? $post['zone'] : '';
		$region = explode('(', $region);
		$region = isset($region[0]) ? trim($region[0]) : '';
		$checkout_model = new Model_Checkout;

		switch ($region)
		{
			case 'ROI':
			case 'Republic of Ireland':
				$return = @$checkout_model->get_counties_as_options(NULL, 'ROI');
				break;

			case 'NI':
			case 'Northern Ireland':
				$return = @$checkout_model->get_counties_as_options(NULL, 'NI');
				break;

			case 'Ireland':
				$return = @$checkout_model->get_counties_as_options(NULL, NULL);
				break;

			default:
				$return = '';
		}

		echo json_encode($return);
	}


	public function action_get_subcategories()
	{
		$id = $this->request->query('id');
		$result = DB::select()->from('plugin_products_category')->where('parent_id', '=', $id)->execute();
		$options = '';

		foreach ($result as $row)
		{
			$options .= '<option value = "'.$row['id'].'" >'.$row['category'].'</option >';
		}

		exit(json_encode($options));
	}

	public function action_search_by_subcategory()
	{
		$id = $this->request->query('id');
		$value = $this->request->query('value');
		$result = '';

		if (is_numeric($id))
		{
			$dbResult = DB::select('name')
				->from('plugin_sict_product')
				->where('category_id', '=', $id)
				->where('name', 'LIKE', '%'.$value.'%')
				->limit(15)
				->execute();

			foreach ($dbResult as $dbRow)
			{
				$result .= '<a href="/'.Model_Product::get_products_plugin_page().'/'.$dbRow['name'].'">'.$dbRow['name'].'</a>';
			}
		}

		exit(json_encode($result));
	}

	public function action_ajax_get_products()
	{
		$this->auto_render = FALSE;
		$amount        = $this->request->query('amount');
		$url           = $this->request->query('url');
		$featured_only = $this->request->query('featured');
		$show_all      = $this->request->query('featured');

		echo '';
		if ($amount != '' AND $url !== '')
		{
			$offset = $this->request->query('offset');
			echo Model_Product::render_products_list_html($amount, $show_all, $featured_only, TRUE, $amount, $offset, $url);
		}

	}

	public function action_cron_autofeature()
	{
        $this->auto_render = false;
        $ids = Model_Product::setFeaturedProductsFromAutoFeature();
        echo "Featured Products have been updated\n";
        print_r($ids);
	}

	public function action_ajax_get_applied_discount_html()
	{
		$model = new Model_Checkout();
		echo $model->render_applied_discount_html();die;
	}

	public function action_ajax_get_discount_html()
	{
		$model = new Model_Checkout();
		echo $model->render_discount_html();die;
	}
	
	

	public function action_change_mode()
	{
        $mode="";
		if(isset($_POST['mode'])){
		$mode=$_POST['mode'];
	    }
		if($mode=='list'){
			Session::instance()->set('display_mode','list');
	    }elseif($mode=='thumb'){
			Session::instance()->set('display_mode','thumb');
	    }elseif($mode=='grid'){
			Session::instance()->set('display_mode','grid');
	    }
	    die;	
	}
	public function action_get_products_filter()
	{
		$this->auto_render = FALSE;
		$page_items        = $this->request->post('page_item');
		$sort_order        = $this->request->post('sort_order');
		$url               = $this->request->post('url');
		$session           = Session::instance();
		$session->set('products_feed_items_per_page', $page_items);
		$session->set('products_feed_order', $sort_order);

		echo '';
		if ($url !== '')
		{
			echo Model_Product::render_products_list_html($page_items, FALSE, FALSE, FALSE, NULL, NULL, $url,$sort_order);
		}
        die;
	}
			

	public function action_add_review()
	{
		$post = $this->request->post();

		// Avoid JavaScript injection
		foreach ($post as $key => $value)
		{
			$post[$key] = htmlentities($value);
		}

		try
		{
			// Create a review object
			$review = new Model_Product_Review();
			// Set the post data as its columns
			$review->values($post);
			// Set the date created and date modified
			// (save_with_moddate() is not used here, because there's no logged-in user)
			$review->set('date_created',  date('Y-m-d H:i:s'));
			$review->set('date_modified', date('Y-m-d H:i:s'));

			try
			{
				$review->save();
			}
			catch (ORM_Validation_Exception $e)
			{
				foreach ($e->errors() as $field => $error)
				{
					IbHelpers::set_message('The "'.$field.'" field has not been filled out correctly.', 'danger');
				}
			}

			// Send a message
			if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging'))
			{
				$messaging         = new Model_Messaging;
				$message_variables = array(
					'product_url'      => URL::site().'products.html/'.$review->product->url_title,
					'product_title'    => $review->product->title,
					'rating'           => $review->rating,
					'title'            => $review->title,
					'author'           => $review->author,
					'email'            => $review->email,
					'review'           => $review->print_review(),
					'edit_review_link' => URL::site().'admin/products/edit_review/'.$review->id
				);

				$messaging->send_template('product-review-posted', null, null, array(), $message_variables);
			}

			// Notify the user that their review won't appear until it has been approved.
			if ($review->publish == 0)
			{
				IBHelpers::set_message(__('Your review has been saved and will appear after it has been approved.'));
			}
		}
		catch (Exception $e)
		{
			// Problem saving. Write error to the system logs and display notice.
			Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
			IBHelpers::set_message('Error saving the review. If this problem persists, please contact the site administrator', 'danger');
		}

		// Redirect the user to the specified page
		$this->request->redirect($this->request->post('return_page'));

	}
}
