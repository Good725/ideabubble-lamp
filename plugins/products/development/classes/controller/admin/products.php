<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Admin_Products extends Controller_Cms
{
    /**
     * Function to be executed before every action.
     */
    public function before()
    {
        parent::before();

        // Select the view
        $this->template->sidebar = View::factory('sidebar');

        // Fill the template
        $this->template->sidebar->menus = array
        (
            array
            (
                array('icon' => 'products', 'name' => 'Products'  , 'link' => '/admin/products'            ),
                array('icon' => 'category', 'name' => 'Categories', 'link' => '/admin/products/categories' ),
				array('icon' => 'discounts', 'name' => 'Discounts' , 'link' => '/admin/products/discounts'  ),
				array( 'name' => 'Matrices'  , 'link' => '/admin/products/matrices'   ),
                array( 'name' => 'Options'   , 'link' => '/admin/products/options'    ),
				array( 'name' => 'Postage'   , 'link' => '/admin/products/postage'    ),
				array( 'name' => 'Reviews'   , 'link' => '/admin/products/reviews'    ),
                array( 'name' => 'Stock'     , 'link' => '/admin/products/stock'      ),
				array( 'name' => 'Tags'      , 'link' => '/admin/products/tags'       ),
            )
        );

        if (Model_Plugin::is_enabled_for_role('Administrator', 'sitc')) {
            $this->template->sidebar->menus[0][] = array(
                'name' => 'Auto Feature',
                'link' => '/admin/products/auto_feature'
            );
        }

        // Set up breadcrumbs and tools
        $this->template->sidebar->breadcrumbs = array(
            array(
                'name' => 'Home',
                'link' => '/admin'
            ),
            array(
                'name' => 'Products',
                'link' => '/admin/products'
            )
        );
        switch($this->request->action())
        {
            case 'index':
                $this->template->sidebar->tools = '<a href="/admin/products/add_product"><button type="button" class="btn">Add Product</button></a>';
                break;

            case 'categories':
            case 'add_category':
            case 'edit_category':
                $this->template->sidebar->breadcrumbs[] = array('name' => 'Categories', 'link' => '/admin/products/categories');
                $this->template->sidebar->tools = '<a href="/admin/products/add_category"><button type="button" class="btn">Add Category</button></a>';
                break;

			case 'discounts':
				$this->template->sidebar->breadcrumbs[] = array('name' => 'Discounts', 'link' => '/admin/products/discounts');
				break;

            case 'options':
            case 'add_option':
            case 'edit_option':
                $this->template->sidebar->breadcrumbs[] = array('name' => 'Options', 'link' => '/admin/products/options');
                $this->template->sidebar->tools = '<a href="/admin/products/add_option"><button type="button" class="btn">Add Option</button></a>';
                break;

			case 'matrices':
				$this->template->sidebar->breadcrumbs[] = array('name' => 'Matrices', 'link' => '/admin/products/matrices');
				$this->template->sidebar->tools = '<a href="/admin/products/add_edit_matrix"><button type="button" class="btn">Add Matrix</button></a>';
				break;

			case 'reviews':
			case 'edit_review':
				$this->template->sidebar->breadcrumbs[] = array('name' => 'Reviews', 'link' => '/admin/products/reviews');
				$this->template->sidebar->tools = '<a href="/admin/products/edit_review"><button type="button" class="btn">Add Review</button></a>';
				break;

            case 'postage':
                $this->template->sidebar->breadcrumbs[] = array('name' => 'Postage', 'link' => '/admin/products/postage');
                break;

			case 'tags':
			case 'add_edit_tag':
				$this->template->sidebar->breadcrumbs[] = array('name' => 'Tags', 'link' => '/admin/products/tags');
				$this->template->sidebar->tools = '<a href="/admin/products/add_edit_tag"><button type="button" class="btn">Add Tag</button></a>';
        }


        // Commons
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/common.js"></script>';
    }

    /**
     * This is the default action.
     */
    public function action_index()
    {
        $this->action_products();
        $products_table_options = Session::instance()->get('products_table_options');
        Session::instance()->delete('products_table_options');
        $this->template->body->plugin = Model_Plugin::get_plugin_by_name('products');
        if($products_table_options){
            $this->template->body->products_table_options = $products_table_options;
        }
    }

    //
    // PRODUCTS
    //

    /**
     * List products.
     */
    public function action_products()
    {
        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/list_products.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/list_products.js"></script>';

        // View
        $this->template->body = View::factory('list_products');
		$this->template->body->categories = ORM::factory('Product_Category')->order_by('category')->find_all_undeleted();
    }

    public function action_autocomplete_products()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');

        $filter = array(
            'keyword' => $this->request->query('term')
        );
        $result = array();
        if (strlen($filter['keyword']) >= 2) {
            $products = Model_Product::search($filter, 0, 100);
            foreach ($products['products'] as $product) {
                $result[] = array(
                    'label' => $product['title'],
                    'value' => $product['id']
                );
            }
        }

        echo json_encode($result);
    }

    /**
     * Add a product.
     */
    public function action_add_product()
    {
            // Assets
            $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/add_edit_product.css'] = 'screen';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/add_edit_product.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';

            // View
            $this->template->body = View::factory('add_edit_product');

            $media = new Model_Media();
            $pages = new Model_Pages();
            $this->template->body->highlight      = isset($this->highlight) ? $this->highlight : array();
            $this->template->body->options_table = '';
            $this->template->body->youtube_videos = array();
            $this->template->body->categories     = ORM::factory('Product_Category')->where('deleted', '=', 0)->order_by('category')->find_all();
            $this->template->body->postal_formats = Model_PostageFormat:: get_all();
            $this->template->body->options        = Model_Option       :: get_all_groups();
            //$this->template->body->products       = Model_Product      :: get_all_list();
            $this->template->body->products = array();
            $this->template->body->size_guides    = $pages->get_all_pages();
            $this->template->body->matrices       = Model_Matrix::get_all_matrices_list();
            $this->template->body->documents      = $media->get_all_items_based_on('location', 'docs'  , 'as_details', '=');
            $this->template->body->images         = $media->get_all_items_based_on('location', Model_Product::MEDIA_IMAGES_FOLDER     , 'as_details', '=');
    }

    /**
     * Edit a product.
     */
    public function action_edit_product()
    {
            $this->action_add_product();

            if(isset($_GET['rows_per_page'])){
                Session::instance()->set('products_table_options', json_encode(array(
                    'rows_per_page'=>$_GET['rows_per_page'],
                    'sort_order'=>$_GET['sort_order'],
                    'sort_by'=>$_GET['sort_by'],
                    'page'=>$_GET['page'])));
            }

			$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
            $model = new Model_Product($id);
            $data  = $model->get_data();
            $table_options = $model->get_options_and_stock();
            $youtube_videos = $model->get_youtube_videos();
            $this->template->body->options_table = View::factory('stock_options')->bind('options_table',$table_options)->render();
            $data['category_ids' ] = json_encode($data['category_ids'  ]);
			$data['images'       ] = json_encode($data['images'        ]);
			$data['documents'    ] = json_encode($data['documents'     ]);
            $data['options'      ] = json_encode($data['options'       ]);
            $data['related_to'   ] = json_encode($data['related_to'    ]);
            $data['stock_options'] = json_encode($data['stock_options' ]);
            $data['videos'       ] = json_encode($data['youtube_videos']);
			$this->template->body->tags = Model_ProductTag::get_all_for_product($id);
            $this->template->body->youtube_videos = $youtube_videos;
            $this->template->body->data = $data;
    }

    /**
     * Save a new product or update an existing one.
     */
    public function action_save_product()
    {
            $data = $_POST;

            $data['category_ids'       ] = json_decode($_POST['category_ids'       ]);
			$data['images'             ] = json_decode($_POST['images'             ]);
			$data['documents'          ] = json_decode($_POST['documents'          ]);
            $data['options'            ] = json_decode($_POST['options'            ]);
            $data['related_to'         ] = json_decode($_POST['related_to'         ]);
            $data['stock_options'      ] = json_decode($_POST['stock_options'      ]);
            $data['youtube_videos'     ] = json_decode($_POST['youtube_videos'     ]);
			$data['tag_ids'            ] = isset($data['tag_ids']) ? array_unique($data['tag_ids']) : array();

            $data['quantity_enabled']    = (isset($data['quantity_enabled']) AND $data['quantity_enabled'] == "on") ? 1 : 0;
            $data['deleted'] = 0;

            $model = (is_numeric($data['id'])) ? new Model_Product($data['id']) : new Model_Product();
            $model->set_data($data);
            $errors = $model->validate();

            if ($data['sign_builder_data_url'] != '' AND class_exists('Model_Media') AND class_exists('Model_Presets'))
            {
                $file_name      = 'sign_builder_'.$model->get_id().'.png';
                $folder         = 'photos/products/';
                $upload_details = getimagesize($data['sign_builder_data_url']);
                $file_data      = array(
                    'name'     => $file_name,
                    'type'     => $upload_details['mime'],
                    'tmp_name' => $data['sign_builder_data_url'],
                    'error'    => 0
                );
                $presets_model  = new Model_Presets;

                $preset = $presets_model->get_preset_details('Sign Builder Previews');

                if (is_null($preset))
                {
                    $item_input_data = array(
                        'item_id'           => '',
                        'item_title'        => 'Sign Builder Previews',
                        'item_directory'    => 'products',
                        'item_height_large' => '',
                        'item_width_large'  => '400',
                        'item_action_large' => 'fitw',
                        'item_thumb'        => '1',
                        'item_height_thumb' => '',
                        'item_width_thumb'  => '200',
                        'item_action_thumb' => 'fitw',
                        'item_publish'      => '1'
                    );
                    $presets_model->add($item_input_data);
                    $preset = $presets_model->get_preset_details('Sign Builder Previews');
                }

                if ( ! is_null($preset))
                {
                    $media_model  = new Model_Media;
                    foreach ($preset as $key => $value)
                    {
                        $preset['preset_'.$key] = $value;
                        unset($preset[$key]);
                    }
                    if ($media_model->add_item_to_media($file_data, $preset))
                    {
                        $data['images'][] = $file_name;
                        $data['images'] = array_unique($data['images']);
                    }
                }
            }
            $model = (is_numeric($data['id'])) ? new Model_Product($data['id']) : new Model_Product();
            $model->set_data($data);
            $errors = $model->validate();

            if ($errors['results'] === TRUE AND $model->save())
            {
                IbHelpers::set_message('Operation successfully completed.', 'success popup_box');

                if(isset($data['redirect']) AND $data['redirect'] == 'self')
                {
                    $this->request->redirect('/admin/products/edit_product/?id='.$model->get_id());
                }
                else
                {
                    $this->request->redirect('/admin/products');
                }
            }
            else
            {
				// If the validate() function did not find anything wrong, but an error still occurred.
				if ($errors['results'] === TRUE)
				{
					IbHelpers::set_message('An error has occurred while saving the product. Please ask your administrator to check the system logs for more information.', 'error popup_box');
				}
				// If an error was found in the validate() function.
				else
				{
					IbHelpers::set_message($errors['results'], 'error popup_box');
					$this->highlight = $errors['ids'];
				}
				// $id = $model->get_id();
				// $redirect = ($id != '') ? 'edit_product?id='.$id : 'add_product';
				// $this->request->redirect('/admin/products/'.$redirect);

				// Don't redirect. This way the post data is retained, so the data the user submitted won't be reset.
				(is_numeric($_POST['id'])) ? $this->action_edit_product() : $this->action_add_product();
			}
    }

	/*
	 * AJAX function, called when the URL name field is changed
	 * Returns false and an error message, if the URL name is used by another product
	 * Returns true and no error message otherwise
	 */
	public function action_ajax_validate_url_title()
	{
		$this->auto_render = FALSE;
		$return['error']   = FALSE;
		$post              = $this->request->post();
		$id                = (isset($post['id']) AND $post['id'] != '') ? $post['id'] : NULL;
		$product           = new Model_Product($id);
		$product->set_url_title($post['url_title']);

		if ($product->check_for_duplicate_url_names())
		{
			$return['message'] = 'This URL is used by another product';
			$return['error'] = TRUE;
		}
		$this->response->body(json_encode($return));
	}

    /**
     * Toggle the product publish option.
     */
    public function action_ajax_toggle_product_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_Product::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     * Delete a product.
     */

	public function action_delete_product()
	{
		$id = $this->request->param('id');
		if ($id)
		{
			Model_Product::delete_object($id);
			IbHelpers::set_message('Product #'.$id.' successfully deleted', 'success popup_box');
		}
		$this->request->redirect('/admin/products/');
	}

    public function action_ajax_delete_product()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_Product::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

	/*
	 * Toggle if a product is featured or not
	 * Called by clicking star icons from the CMS product listing
	 * Creates an instance of the product object, changes the "featured" value and saves
	 */
	public function action_ajax_toggle_product_featured()
	{
		$this->auto_render = FALSE;
		Model_Product::toggle_featured_option($this->request->param('id'));
	}

    /**
     *
     */
    public function action_ajax_get_all_products()
    {
		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $this->response->body(Model_Product::ajaxGetAllProducts($_REQUEST));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_product()
    {
        $this->response->body(json_encode( (isset($_GET['id'])) ? Model_Product::get($_GET['id']) : '' ));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_product_media_base_location()
    {
        $this->response->body(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', Model_Product::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR.'_thumbs_cms'));
        $this->auto_render = FALSE;
    }

    //
    // CATEGORIES
    //

    /**
     * List all the categories.
     */
    public function action_categories()
    {
        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/list_categories.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/list_categories.js"></script>';

        // View
        $this->template->body = View::factory('list_categories');
    }

    /**
     * Add a category.
     */
    public function action_add_category()
    {
        try
        {
            // Assets
            $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/add_edit_category.css'] = 'screen';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/add_edit_category.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';

            // View
			$id = $this->request->query('id');
			if ($id == '')
			{
				$id = $this->request->post('id');
			}
			$category        = ORM::factory('Product_Category')->where('id', '=', $id)->find_undeleted();
			$use_config_file = (Settings::instance()->get('use_config_file') === '0');

			$this->template->body                = View::factory('add_edit_category');
			$this->template->body->category      = $category;
			$this->template->body->theme_options = $use_config_file ? Model_Settings::get_site_themes_as_options($category->theme, TRUE, TRUE) : '';

            $media = new Model_Media();

            $this->template->body->categories = ORM::factory('Product_Category')->find_all_undeleted();
			$this->template->body->products   = ORM::factory('Product_Product')->find_all_undeleted();
            $this->template->body->images     = $media->get_all_items_based_on('location','products', 'as_details', '=');
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/products/categories');
        }
    }

    /**
     * Edit a category.
     */
    public function action_edit_category()
    {
        try
        {
            $this->action_add_category();
		}
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/products/categories');
        }
    }

    /**
     * Save a new category or update an existing one.
     */
    public function action_save_category()
    {
		$id          = $this->request->post('id');
		$post        = $this->request->post();
		$category    = new Model_Product_Category($id);
		$saved       = $category->save_relationships($post);

		if ($saved)
		{
			IbHelpers::set_message('Operation successfully completed.', 'success popup_box');
			$this->request->redirect('/admin/products/edit_category?id='.$category->id);
		}
		else
		{
			IbHelpers::set_message('Unable to complete the requested operation. Please, review the fields.', 'info popup_box');
			(is_numeric($id)) ? $this->action_edit_category() : $this->action_add_category();
		}
    }

    /**
     * Toggle the category publish option.
     */
    public function action_ajax_toggle_category_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_Category::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     * Delete a category.
     */
    public function action_ajax_delete_category()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_Category::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_all_categories()
    {
        $this->response->body(json_encode(Model_Category::get_all()));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_category_media_base_location()
    {
        $this->response->body(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'products'.DIRECTORY_SEPARATOR.'_thumbs_cms'));
        $this->auto_render = FALSE;
    }

    //
    // OPTIONS
    //

    /**
     *
     */
    public function action_options()
    {
        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/list_options.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/list_options.js"></script>';

        // View
        $this->template->body = View::factory('list_options');
    }

    /**
     *
     */
    public function action_add_option()
    {
        try
        {
            // Assets
            $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/add_edit_option.css'] = 'screen';
            $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/add_edit_option.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';

            // View
            $this->template->body = View::factory('add_edit_option');

            $media = new Model_Media();

            $this->template->body->groups = Model_Option::get_all_groups();
            $this->template->body->images = $media->get_all_items_based_on('location', Model_Option::MEDIA_IMAGES_FOLDER, 'as_details', '=');
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/products/options');
        }
    }

    /**
     *
     */
    public function action_edit_option()
    {
        try
        {
            $this->action_add_option();

            $model = new Model_Option(isset($_GET['id']) ? $_GET['id'] : $_POST['id']);
            $data  = $model->get_data();

            $this->template->body->data = $data;
        }
        catch (Exception $e)
        {
            IbHelpers::set_message('Bad request.', 'error popup_box');

            $this->request->redirect('/admin/products/options');
        }
    }

    /**
     *
     */
    public function action_save_option()
    {
            $data = $_POST;
            $model = (is_numeric($data['id'])) ? new Model_Option($data['id']) : new Model_Option();
            $data['group_id'] = (strlen(trim($data['new_group'])) > 0) ?
                Model_Option::addOptionGroupIf($data['new_group'], $data['group_label']) : $data['group_id'];
            $model->set_data($data);

            if ($model->save())
            {
                IbHelpers::set_message('Operation successfully completed.', 'success popup_box');
                $option = $model->get_data();

                $this->request->redirect('/admin/products/edit_option?id='.$option['id']);
            }
            else
            {
                IbHelpers::set_message('Unable to complete the requested operation. Please, review the fields.', 'info popup_box');

                (is_numeric($_POST['id'])) ? $this->action_edit_option() : $this->action_add_option();
            }

    }

    /**
     *
     */
    public function action_ajax_toggle_option_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_Option::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_delete_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_Option::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_all_options()
    {
        $this->response->body(json_encode(Model_Option::get_all()));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_all_option_groups()
    {
        $this->response->body(json_encode( (isset($_GET['id'])) ? Model_Option::get_all_groups() : '' ));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_option()
    {
        $this->response->body(json_encode( (isset($_GET['id'])) ? Model_Option::get($_GET['id']) : '' ));
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_get_option_media_base_location()
    {
        $this->response->body(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', Model_Option::MEDIA_IMAGES_FOLDER.DIRECTORY_SEPARATOR.'_thumbs_cms'));
        $this->auto_render = FALSE;
    }

	/**
	 * Get all options in a given group
	 */
	public function action_ajax_get_group_options()
	{
		$this->auto_render = FALSE;
		$id = $this->request->param('id');
		$options = Model_Option::get_options_by_group_id($id);
		echo json_encode($options);
	}

    //
    // POSTAGE - MAIN
    //

    /**
     *
     */
    public function action_postage()
    {
        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/list_add_edit_postage.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/list_add_edit_postage.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';

        // View
        $this->template->body = View::factory('list_add_edit_postage');
    }

    //
    // POSTAGE - FORMAT
    //

    /**
     *
     */
    public function action_ajax_postage_add_format()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_PostageFormat();
            $model->set_data($data);

            $ok    = $model->save();
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_update_format()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_PostageFormat($data->id);
            $model->set_data($data);

            $ok    = $model->save();
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_toggle_format_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_PostageFormat::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_delete_format()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_PostageFormat::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_get_all_formats()
    {
        $this->response->body(json_encode(Model_PostageFormat::get_all()));
        $this->auto_render = FALSE;
    }

    //
    // POSTAGE - ZONE
    //

    /**
     *
     */
    public function action_ajax_postage_add_zone()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_PostageZone();
            $model->set_data($data);

            $ok    = $model->save();
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_update_zone()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_PostageZone($data->id);
            $model->set_data($data);

            $ok    = $model->save();
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_toggle_zone_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_PostageZone::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_delete_zone()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_PostageZone::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_get_all_zones()
    {
        $this->response->body(json_encode(Model_PostageZone::get_all()));
        $this->auto_render = FALSE;
    }

    //
    // POSTAGE - RATE
    //

    /**
     *
     */
    public function action_ajax_postage_add_rate()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_PostageRate();
            $model->set_data($data);

            $ok    = $model->save();
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_update_rate()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_PostageRate($data->id);
            $model->set_data($data);

            $ok    = $model->save();
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_toggle_rate_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_PostageRate::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_delete_rate()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_PostageRate::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_postage_get_all_rates()
    {
        $this->response->body(json_encode(Model_PostageRate::get_all()));
        $this->auto_render = FALSE;
    }

	public function action_ajax_postage_get_all_countries()
	{
		$this->response->body(json_encode(Model_PostageCountry::get_all()));
		$this->auto_render = FALSE;
	}

    //
    // DISCOUNT - MAIN
    //

    /**
     *
     */
    public function action_discounts()
    {
        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/list_add_edit_discount.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/list_add_edit_discount.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';

        // View
        $this->template->body = View::factory('list_add_edit_discount');

        // Data
        $this->template->body->types = Model_DiscountFormat::get_all_types();
        $this->template->body->categories = Model_Category::get_list();
        $roles = new Model_Roles;
        $this->template->body->roles = $roles->get_all_roles();
    }
	
	
	public function action_cart_discounts()
    {
		$types_description = Model_DiscountFormat::get_all_types();
		/*$discount_types[] = $types_description[6]; //Amount Based, % Price Discount
		$discount_types[] = $types_description[7]; //Amount Based, Free Shipping
		$discount_types[] = $types_description[8]; //Quantity Based, Discount*/
		$discount_type_id = Model_DiscountFormat::CART_BASED_PRICE_DISCOUNT; //for cart based price discount
		$free_shipping_type_id = Model_DiscountFormat::CART_BASED_FREE_SHIPPING; //for cart based free shipping
		$qty_type_id = Model_DiscountFormat::CART_BASED_QTY_DISCOUNT; //for cart based free shipping

		$discount_type_ids = array($discount_type_id, $free_shipping_type_id, $qty_type_id);

		$discount_types = DB::select('id', 'title', 'type_id')
							->from('plugin_products_discount_format')
							->where('publish', '=', 1)
							->where('deleted', '=', 0)
							->where('type_id', 'IN', $discount_type_ids)
							->where('deleted', '=', 0)->execute()->as_array();

		//echo Debug::vars($discount_type_ids);
		// Assets
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/cart_discount.js"></script>';

        // View
        $this->template->body = View::factory('cart_discount');

        // Data
        $this->template->body->discount_types = $discount_types;
    }
	
	public function action_ajax_show_discount_type_report()
    {
       $data['discount_type_id'] = $_POST['discount_types'];
       $data['format_type'] = $_POST['format_type'];
       echo $this->render_report_html($data);die;
    }
	
	public function render_report_html($data){
		$discount_type_id = $data['discount_type_id'];
		$format_type = $data['format_type'];

		if($format_type == 'Displayed'){
			$sql = "SELECT `t1`.*, `t2`.`cart_data`, `t2`.`form_data` FROM `plugin_products_discount_displayed` AS `t1` INNER JOIN `plugin_products_carts` AS `t2` ON (`t1`.`cart_id` = `t2`.`id`) WHERE FIND_IN_SET($discount_type_id,displayed_discount_type_ids)";
		}else{
			$sql = "SELECT `t1`.*, `t2`.`cart_data`, `t2`.`form_data`, `t3`.`discount`, `t3`.`discount_percentage` from plugin_products_discount_displayed t1 inner join plugin_products_carts t2 on t1.cart_id = t2.id left join plugin_products_discount_displayed_info t3 on t3.discount_displayed_id = t1.id where t3.discount_type_id = $discount_type_id";
		}
		/*if($format_type == 'Displayed'){
			$sql = "SELECT `t1`.*, `t2`.`cart_data`, `t2`.`form_data` FROM `plugin_products_discount_displayed` AS `t1` INNER JOIN `plugin_products_carts` AS `t2` ON (`t1`.`cart_id` = `t2`.`id`) WHERE FIND_IN_SET((SELECT GROUP_CONCAT(t3.type_id) FROM plugin_products_discount_format t3 WHERE id = $discount_type_id), displayed_discount_type_ids)";
		}else{
			$sql = "SELECT `t1`.*, `t2`.`cart_data`, `t2`.`form_data`, `t3`.`discount`, `t3`.`discount_percentage` from plugin_products_discount_displayed t1 inner join plugin_products_carts t2 on t1.cart_id = t2.id left join plugin_products_discount_displayed_info t3 on t3.discount_displayed_id = t1.id where t3.discount_type_id = (SELECT type_id FROM plugin_products_discount_format WHERE id = $discount_type_id)";
		}*/

		$results = DB::query(Database::SELECT, $sql)->execute()->as_array();
		$report_data['results'] = $results;
		$report_data['format_type'] = $format_type;
		
		return View::factory('ajax_show_discount_type_report', $report_data);
	}
	
	
	
	
    //
    // DISCOUNT - FORMAT
    //

    /**
     *
     */
    public function action_ajax_discount_add_format()
    {
        $id = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_DiscountFormat();
            $model->set_data($data);

            $id   = $model->save_discount_format();

            if($id && isset($data->role)){
                $model->save_users_roles_relationships($id, $data->role);
            }
        }

        $this->response->body($id ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_discount_update_format()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_DiscountFormat($data->id);
            $model->set_data($data);

            $ok    = $model->save_discount_format();

            if($data->id && isset($data->role)){
                $model->save_users_roles_relationships($data->id, $data->role);
            }
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_discount_toggle_format_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_DiscountFormat::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_discount_delete_format()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_DiscountFormat::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_discount_get_all_formats()
    {
        $this->response->body(json_encode(Model_DiscountFormat::get_all()));
        $this->auto_render = FALSE;
    }

    //
    // DISCOUNT - RATE
    //

    /**
     *
     */
    public function action_ajax_discount_add_rate()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_DiscountRate();
            $model->set_data($data);

            $ok    = $model->save();
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_discount_update_rate()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data  = json_decode($_POST['data']);

            $model = new Model_DiscountRate($data->id);
            $model->set_data($data);

            $ok    = $model->save();
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_discount_toggle_rate_publish_option()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_DiscountRate::toggle_publish_option($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_discount_delete_rate()
    {
        $ok = FALSE;

        if ( isset($_POST['data']) )
        {
            $data = json_decode($_POST['data']);
            $ok   = Model_DiscountRate::delete_object($data->id);
        }

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     *
     */
    public function action_ajax_discount_get_all_rates()
    {
        $this->response->body(json_encode(Model_DiscountRate::get_all()));
        $this->auto_render = FALSE;
    }

    public function action_stock()
    {
        // Assets
        $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/list_edit_stock.css'] = 'screen';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/list_stock.js"></script>';

        // View
        $this->template->body = View::factory('list_stock');

        // Data
        $this->template->body->stock = Model_Product::list_all_stock_options();
    }

    public function action_save_option_details()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $option = Model_ProductOption::instance()->set($post)->save();
        $response = $option === true ? 'true' : 'false';
        $this->response->body($response);
    }

    public function action_get_stock_option_rows()
    {
        $post = $this->request->post();
        $this->auto_render = false;
        if(empty($post['product_id']))
        {
            $options = Model_Product::get_new_product_options_id($post['option_id']);
        }
        else
        {
            $model = new Model_Product($post['product_id']);
            $options = $model->get_option_group_id($post['option_id']);
        }
        $this->response->body(View::factory('stock_options')->bind('options_table',$options));
    }

    public function action_set_is_stock_enabled()
    {
        $post = $this->request->post();
        Model_ProductOption::instance()->set_is_stock($post,true);
    }

    public function action_set_is_stock_disabled()
    {
        $post = $this->request->post();
        Model_ProductOption::instance()->set_is_stock($post,false);
    }

    public function action_update_stock()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $keys = array_keys($post);
        $ok = Model_ProductOption::update_options($post['product_id'],$post['option_id'],array($keys[2] => $post[$keys[2]]));
        $this->response->body($ok ? 'ok' : 'failed');
    }

    public function action_get_option_labels()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $labels = Model_Product::get_option_labels_id($post['group_id']);
        $this->response->body($labels);
    }

    public function action_get_group_label()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $this->response->body(Model_Product::get_group_label($post['group_name']));
    }

    public function action_get_youtube_video_id()
    {
        $this->auto_render = false;
        $post = $this->request->post();

		/*
		 * == Supported formats ==
		 * {id}
		 * youtube.com/v/{id}
		 * youtube.com/vi/{id}
		 * youtube.com/?v={id}
		 * youtube.com/?vi={id}
		 * youtube.com/watch?v={id}
		 * youtube.com/watch?vi={id}
		 * youtu.be/{id}
		 */

		preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $post['url'], $video_id);
		$video_id = isset($video_id[0]) ? $video_id[0] : $post['url'];

		$this->response->body($video_id);
    }

    public function action_matrices()
    {
        $this->action_list_matrices();
    }

    public function action_list_matrices()
    {
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/list_matrices.js"></script>';
        $matrices = Model_Matrix::get_all_matrices();
        $this->template->body = View::factory('list_matrices')->bind('matrices',$matrices);
    }

    public function action_add_edit_matrix()
    {
        $id = $this->request->param('id');
        $matrix = Model_Matrix::create($id);
        $option_groups = Model_Option::get_all_groups();
        $media = new Model_Media();
        $available_images          = $media->get_all_items_based_on('location', Model_Product::MEDIA_IMAGES_FOLDER     , 'as_details', '=');
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/add_edit_matrix.js"></script>';
        $this->template->styles[URL::get_engine_plugin_assets_base('products').'css/add_edit_matrix.css'] = 'screen';
        $this->template->body = View::factory('add_edit_matrix')->bind('matrix',$matrix)->bind('option_groups',$option_groups)->bind('available_images',$available_images);
    }

    public function action_generate_matrix()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $id = (isset($post['matrix_id']) AND is_numeric($post['matrix_id'])) ? $post['matrix_id'] : NULL;
        $x = Model_Option::get_options_by_group_id($post['option1']);
        $y = Model_Option::get_options_by_group_id($post['option2']);
        $option_groups = Model_Option::get_all_groups();
        $matrix = Model_Matrix::create($id);
        $data = $matrix->get_matrix_data();
        $html = View::factory('grid')->bind('y',$y)->bind('x',$x)->bind('post',$post)->bind('option_groups',$option_groups)->render();
        $this->response->body(json_encode(array('html' => $html,'data' => $data)));
    }

    public function action_generate_matrix_sub_option()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $options = Model_Option::get_options_by_group_id($post['option2']);
        $this->response->body(View::factory('sub_option')->bind('options',$options)->bind('option1',$post['option1']));
    }

    public function action_save_matrix()
    {
        $post = $this->request->post();
        $matrix = Model_Matrix::create()->set($post);
        $matrix->save();
        $this->request->redirect('/admin/products/list_matrices');
    }

    public function action_get_options_and_prices()
    {
        $post = $this->request->post();
        //$product_id = $post['product_id'];
        $option_id = $post['option_id'];
        Model_Matrix::get_all_available_options($option_id);
    }

    public function action_matrix_price()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $option1 = isset($post['option1']) ? $post['option1'] : NULL;
        $option2 = isset($post['option2']) ? $post['option2'] : NULL;
        $this->response->body(Model_Product::get_matrix_price($option1,$option2));
    }

    public function action_set_matrix()
    {
        $post = $this->request->post();
        $matrix = Model_Matrix::create($post['matrix_id']);
        if($post['action'] == "publish")
        {
            $matrix->set(array('enabled' => intval($post['value'])));
            $matrix->save();
        }
        else if($post['action'] == "delete")
        {
            $matrix->set(array('delete' => intval($post['value'])));
            $matrix->save();
        }
    }


	/*
	 * Tags
	 */

	public function action_tags()
	{
		$tags = Model_ProductTag::get_all();
		$view = View::factory('list_tags')->set('tags', $tags);
		$this->template->body = $view;
	}

	public function action_add_edit_tag()
	{
		$id   = $this->request->param('id');
		$tag  = new Model_ProductTag($id);
		$view = View::factory('add_edit_tag')->set('tag', $tag->get_instance())->render();
		$this->template->styles[URL::get_engine_assets_base().'css/validation.css'] = 'screen';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';
		$this->template->body = $view;
	}

	public function action_save_tag()
	{
		$post  = $this->request->post();
		$tag   = new Model_ProductTag($post['id']);
		$saved = $tag->set($post)->save();

		if ( ! $saved)
		{
			IbHelpers::set_message('Error saving tag.', 'error popup_box');
		}
		else
		{
			IbHelpers::set_message('Tag #'.$tag->get_id().' successfully saved', 'success popup_box');
		}

		$redirect = (isset($post['redirect']) AND $post['redirect'] == 'exit') ? 'tags' : 'add_edit_tag/'.$tag->get_id();

		$this->request->redirect('/admin/products/'.$redirect);
	}

	public function action_delete_tag()
	{
		$id  = $this->request->param('id');
		$tag = new Model_ProductTag($id);
		if ($tag->delete())
		{
			IbHelpers::set_message('Tag #'.$id.'"'.$tag->get_title().'" successfully deleted','success popup_box');
			$redirect = 'tags';
		}
		else
		{
			IbHelpers::set_message('Failed to delete tag #'.$id, 'error popup_box');
			$redirect = 'add_edit_tag/'.$id;
		}
		$this->request->redirect('/admin/products/'.$redirect);
	}

	// Toggle publish status from the list of tags
	public function action_ajax_toggle_publish_tag()
	{
		$this->auto_render = FALSE;
		$tag = new Model_ProductTag($this->request->param('id'));
		echo $tag->toggle_publish();
	}

	// Get the tags for an autocomplete list
	public function action_ajax_get_tags_ac()
	{
		$this->auto_render = FALSE;
		$post = $this->request->post();
		$like = isset($post['like']) ? $post['like'] : '';
		$tags = ($like == '') ? array() : Model_ProductTag::get_all_like($like);
		echo json_encode($tags);
	}


	public function action_ajax_get_product_categories()
	{
		$this->auto_render = FALSE;
		$product           = ORM::factory('Product_Product', $this->request->param('id'));
		$category_ids      = array();
		foreach ($product->categories->find_all() as $category)
		{
			$category_ids[] = $category->id;
		}
		$return['category_ids'] = $category_ids;
		$return['product_name'] = $product->title;

		echo json_encode($return);
	}

	public function action_ajax_save_product_categories()
	{
		$this->auto_render = FALSE;
		$product = ORM::factory('Product_Product', $this->request->post('id'));
		$product->save_relationships($this->request->post());
	}

    public function action_auto_feature()
    {
        $distributors = Model_SITC::get_distributor_list();
        $manufacturers = Model_SITC::get_manufacturer_list();
        $aflist = Model_Product::getAutoFeatureList();

        asort($distributors);
        asort($manufacturers);

        $post = $this->request->post();
        if (isset($post['save'])) {
            Model_Product::saveAutoFeature($post);
            $this->request->redirect('/admin/products/auto_feature');
        }
        $this->template->body = View::factory('auto_feature');
        $this->template->body->aflist = $aflist;
        $this->template->body->distributors = $distributors;
        $this->template->body->manufacturers = $manufacturers;
    }

	/*
	 * Reviews
	 */

	public function action_reviews()
	{
		// todo: make serverside
		$reviews = ORM::factory('Product_Review')->order_by('date_modified', 'desc')->order_by('date_created', 'desc')->find_all_undeleted();
		$view    = View::factory('list_reviews')->set('reviews', $reviews);
		$this->template->body = $view;
	}

	public function action_edit_review()
	{
		$id       = $this->request->param('id');
		$review   = ORM::factory('Product_Review')->where('id', '=', $id)->find_undeleted();
		$review->values($this->request->post());
		$products = ORM::factory('Product_Product')->find_all_undeleted();
		$view     = View::factory('add_edit_review')->set('review', $review)->set('products', $products);
		$this->template->body = $view;
	}

	public function action_save_review()
	{
		$id     = $this->request->param('id');
		$post   = $this->request->post();

		// Avoid JavaScript injection
		foreach ($post as $key => $value)
		{
			$post[$key] = htmlentities($value);
		}

		// Load review object and set post values
		$review = ORM::factory('Product_Review')->where('id', '=', $id)->find_undeleted();
		$review->values($post);


		try
		{
			// Save and display a message
			$review->save_with_moddate();
			IBHelpers::set_message('Review #'.$review->id.' successfully saved.', 'success popup_box');

			if ($this->request->post('redirect') == '1')
			{
				// If the user clicked save and exit, redirect to the list screen
				$this->request->redirect('/admin/products/reviews');
			}
			else
			{
				// Otherwise reopen the form with the review details loaded
				$this->request->redirect('/admin/products/edit_review/'.$review->id);
			}
		}
		catch (ORM_Validation_Exception $e)
		{
			foreach ($e->errors() as $field => $error)
			{
				IbHelpers::set_message('The "'.$field.'" field has not been filled out correctly.', 'danger popup_box');
			}
			self::action_edit_review();
		}
		catch (Exception $e)
		{
			// Problem saving. Write error to the system logs and display notice.
			Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
			IBHelpers::set_message('Error saving the review. Check the system logs for more information.', 'danger popup_box');
			self::action_edit_review();
		}
	}

	public function action_delete_review()
	{
		$id = $this->request->post('id');
		if ($id)
		{
			// Load a review object for the given ID
			$review = new Model_Product_Review($id);
			// Set its publish and delete values and save
			$review->set('publish', 0);
			$review->set('deleted', 1);
			$review->save_with_moddate();
			// Display success message
			IbHelpers::set_message('Review #'.$id.': "'.$review->title.'" successfully deleted', 'success popup_box');
		}
		else
		{
			// Display error message
			IbHelpers::set_message('Review #'.$id.' does not exist', 'danger popup_box');
		}
		// Redirect the user to the list screen when done
		$this->request->redirect('/admin/products/reviews');
	}

	public function action_ajax_get_reviews_datatable()
	{
		$this->auto_render = FALSE;
		$this->response->body(Model_Product_Review::get_for_datatable($this->request->query()));
	}


	public function action_ajax_toggle_review_publish()
	{
		$this->auto_render = FALSE;
		$id                = $this->request->param('id');
		$publish           = $this->request->query('publish');
		if ($id)
		{
			// Load review object for the given ID
			$review  = ORM::factory('Product_Review', $id);
			// Set its publish column to the provided value and save
			$review->set('publish', $publish);
			$review->save_with_moddate();
		}

		return $publish;
	}
    // AJAX function for generating sublist in the plugins' dropdown function add
    public function action_ajax_get_submenu($data_only = false)
    {
        $return['link']    = '';
        $return['items']   = array();
        $return['items'][] = array('icon_svg' => 'discounts', 'id' => 'discounts','title' => 'Discount');
        $return['items'][] = array('icon_svg' => 'discounts', 'id' => 'cart_discounts','title' => 'Cart Discount Report');

        if ($data_only) {
            return $return;
        } else {
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $this->response->body(json_encode($return));
        }
    }

}

