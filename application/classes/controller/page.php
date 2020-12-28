<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Page extends Controller
{
    protected static $actionAliases = array();
    public static function addActionAlias($url, $class, $action)
    {
        $url = str_replace('.html', '', $url);
        self::$actionAliases[$url] = array('class' => $class, 'action' => $action);
    }

	public static function getActionAliases()
	{
		return self::$actionAliases;
	}

	public function action_index()
	{
		$localisation_content_active = Settings::instance()->get('localisation_content_active') == '1';
		$params = $this->request->param();
		$page = $this->request->param('page');
		$localisation_lang = $this->request->param('localisation_lang');
		$localisation_languages = Model_Localisation::languages_list();
		if ($localisation_content_active)
		{
			if (!$localisation_lang)
			{
				$lang = Cookie::get('lang');
				if ($lang == null) {
					$lang = Model_Localisation::preferedLanguage(Model_Localisation::languages_list_codes());
				}
                $localisation_lang = $lang;
				I18n::set_default_language($lang);
				$query = $this->request->query();
				$uri = Request::$current->uri();
				if (count($query) > 0) {
					if (strpos($uri, '?') !== false) {
						$uri .= '&';
					} else {
						$uri .= '?';
					}
					$uri .= http_build_query($query);
				}
                if (count($localisation_languages) > 1) {
                    $this->request->redirect($lang . '/' . $uri);
                }
			}
			I18n::$lang = $localisation_lang;
			Cookie::set('lang', $localisation_lang);
		}

        // check if an actual controller is mapped to an alias
        // e.g. cpayment.html => frontend/propman/custom_payment
        if (isset(self::$actionAliases[$page])) {
            $class = self::$actionAliases[$page]['class'];
            $action = self::$actionAliases[$page]['action'];
			$this->request->directory(stripos(self::$actionAliases[$page]['class'], 'Controller_Admin_') !== false ? 'admin' : 'frontend');
			$this->request->controller(strtolower(str_ireplace(array('Controller_Admin_', 'Controller_Frontend_'), '', self::$actionAliases[$page]['class'])));
			$this->request->action(str_ireplace('action_', '', self::$actionAliases[$page]['action']));
			$call = new $class($this->request, $this->response);
            $call->before();
            $view = $call->$action();
            $call->after();
            $this->response->body($view);
            return;
        }


		//do the 301/302 check here.
        /// for redirects like products.html/hp-250-g4 => products.html/hp-250-g4-n0z91ea-abu
        $redirect = new Model_PageRedirect($this->request->uri());
        $has_redirect = $redirect->check_redirect()->get_redirect();
        if (isset($has_redirect['to']) && $has_redirect['to'] != '') {
			$query = $this->request->query();
			if (count($query) > 0) {
				if (strpos($has_redirect['to'], '?') !== false) {
					$has_redirect['to'] .= '&';
				} else {
					$has_redirect['to'] .= '?';
				}
				$has_redirect['to'] .= http_build_query($query);
			}

            if (filter_var($has_redirect['to'], FILTER_VALIDATE_URL)) {
                // If the redirect is an absolute URL, redirect straight to it.
                $this->request->redirect($has_redirect['to'], $has_redirect['type']);
            } else {
                // Otherwise, assume it is a relative URL.
                $this->request->redirect(URL::site().$has_redirect['to'], $has_redirect['type']);
            }
        }
        /// for redirects like productz.html => products.html
		$redirect = new Model_PageRedirect($page);
		$has_redirect = $redirect->check_redirect()->get_redirect();
		if (!is_null($has_redirect['to']) AND is_array($has_redirect) AND count($has_redirect) == 2)
		{
            if (filter_var($has_redirect['to'], FILTER_VALIDATE_URL)) {
                // If the redirect is an absolute URL, redirect straight to it.
                $this->request->redirect($has_redirect['to'], $has_redirect['type']);
            } else {
                // Otherwise, assume it is a relative URL.
                $this->request->redirect(URL::site() . (I18n::$lang ? I18n::$lang . '/' : '') . $has_redirect['to'], $has_redirect['type']);
            }
		}

		$enable_frontend = Settings::instance()->get('enable_frontend');

		if ($enable_frontend === 'FALSE' || Model_Plugin::is_loaded('pages') === FALSE)
		{
			$this->request->redirect('admin');
		}

		if ($this->request->query('clear_cart') AND class_exists('Model_Checkout'))
		{
			Model_Checkout::empty_cart();
		}

		$model = new Model_Pages();

		//Get page name tag
		$page = $this->request->param('page');
		$extension = $this->request->param('ext');


		//Get Item Category and ID if passed
		$current_item_identifier = $this->request->param('item_identifier');
		$current_item_category = $this->request->param('item_category');

		//Check if the URL have extension (example: test.html) in this case add to the $page
		//Also check if the page have categories, in this case, assume the extension is "html" example: "/news/News/looking-back-at-2012.html" instead of "/news.html/News/looking-back-at-2012.html"
		if (!empty($extension))
		{
			$page .= '.'.$extension;
		}
		elseif (!empty($current_item_category))
		{
			$page .= '.html';
		}

		// if no page url is sent
		if (empty($page))
		{
			//if no page url is sent then check for default
			$default_home_page = Settings::instance()->get('default_home_page');

			if (!empty($default_home_page))
			{
				//load default home page in settings for the site
				$page = Model_Pages::get_page_by_id($default_home_page);
			}
			else
			{
				$page = 'home.html';
			}

			// SEO: avoid www.example.com/ and www.example.com/home.html being treated as duplicate content
			$this->request->redirect(URL::base().$page.URL::query(), 301 ); // set 301 redirect
		}

		//Prevent to load the product list from the cache, this happend if "back" button is pressed after a purchase
		if ($page == 'checkout.html')
		{
			$this->response->headers(array(
				'Cache-Control' => 'no-cache, no-store, must-revalidate',
				'Pragma' => 'no-cache',
				'Expires' => '0'
			));
		}

        // If the URL query string indicates this is a draft and the user has permission to view drafts, load the draft page instead.
        $is_draft = (bool) $this->request->query('draft') && Auth::instance()->has_access('pages_save_draft');

        //Get this page's details
        $sql_res = $model->get_page($page, [
            'published_only' => true,
            'draft' => $is_draft
        ]);

        $original_content = $sql_res[0]['content'];

		$data_helper_call = null;
		if ($sql_res[0]['data_helper_call']) {
			$data_helper_call = $sql_res[0]['data_helper_call'];
		}
		if ($sql_res === false || !isset($sql_res[0]['publish']) OR $sql_res[0]['publish'] == 0) {
			$sql_res = $model->get_page('error404.html');
			$exc = new HTTP_Exception_404();
			Model_Errorlog::save($exc);
		}

		if ($sql_res[0]['nocache']) {
			$this->response->headers(array(
					'Cache-Control' => 'no-cache, no-store, must-revalidate',
					'Pragma' => 'no-cache',
					'Expires' => '0'
			));
		}

		// enforce ssl
		if ($sql_res[0]['force_ssl'] == 1 && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')) {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				throw new Exception("SSL must be used!");
			} else {
				$sslUrl = URL::base('https') . $page . URL::query();				
				$this->request->redirect($sslUrl, 301); // set 301 redirect
			}
		}
        if (Model_Plugin::is_loaded('courses')) {
            // Parse black editor, localisation and short tags
            $sql_res[0]['content'] = IbHelpers::parse_page_content($sql_res[0]['content']);

            // Additional fields for the contact form
            if (isset($_GET['interested_in_course_id'])) {
                $sql_res[0]['content'] = Model_Courses::add_course_selector_to_form($sql_res[0]['content'], $_GET['interested_in_course_id']);
            }

            if (!empty($_GET['interested_in_schedule_id'])) {
                $sql_res[0]['content'] = Model_Courses::add_schedule_to_form($sql_res[0]['content'], $_GET['interested_in_schedule_id']);
            }

            if ($sql_res[0]['subject_id'] && strpos($sql_res[0]['content'], '/course-list') > 0) {
                $sql_res[0]['content'] = str_replace('/course-list"', '/course-list?subject='.$sql_res[0]['subject_id'].'"', $sql_res[0]['content']);
                $sql_res[0]['content'] = str_replace('/course-list.html"', '/course-list.html?subject='.$sql_res[0]['subject_id'].'"', $sql_res[0]['content']);
            }
            else if ($sql_res[0]['course_category_id'] && strpos($sql_res[0]['content'], '/course-list') > 0) {
                $sql_res[0]['content'] = str_replace('/course-list"', '/course-list?category='.$sql_res[0]['course_category_id'].'"', $sql_res[0]['content']);
                $sql_res[0]['content'] = str_replace('/course-list.html"', '/course-list.html?category='.$sql_res[0]['course_category_id'].'"', $sql_res[0]['content']);
            }
            // Pre-fill some details on the accreditation-application page
            if ($sql_res[0]['id'] == Settings::instance()->get('accreditation_application_page')) {
                $booking_id   = isset($_GET['booking_id']) ? $_GET['booking_id'] : null;
                $contact_id   = isset($_GET['contact_id']) ? $_GET['contact_id'] : null;
                if ($contact_id && Auth::instance()->get_contact()->id != $contact_id) {
                    Auth::instance()->logout();
                    $this->request->redirect('/admin/login?redirect=' . rawurlencode( $page . '/?booking_id=' . $booking_id . '&contact_id=' . $contact_id));
                }
                else {
                    $booking = new Model_Booking_Booking($booking_id);
                    $schedule = $booking->schedules->find();
                    $counties_options = '<option value=""></option>' . Model_Cities::get_all_counties_html_options();
                    $countries_options = '<option value=""></option>' .  Model_Country::get_country_as_options();
                    $nationalities_options = '<option value=""></option>' .  Model_Country::get_nationalities_as_options();
                    $sql_res[0]['content'] = str_replace('name="country">', 'name="country">' . $countries_options, $sql_res[0]['content']);
                    $sql_res[0]['content'] = str_replace('name="county">', 'name="county">' . $counties_options, $sql_res[0]['content']);
                    $sql_res[0]['content'] = str_replace('name="nationality">', 'name="nationality">' . $nationalities_options, $sql_res[0]['content']);
                    $sql_res[0]['content'] = str_replace('name="booking_id"', 'name="booking_id" value="'.$booking_id.'"', $sql_res[0]['content']);
                    $sql_res[0]['content'] = str_replace('name="contact_id"', 'name="contact_id" value="'.$contact_id.'"', $sql_res[0]['content']);
                    $sql_res[0]['content'] = str_replace('name="schedule_id"', 'name="schedule_id" value="'.$schedule->id.'"', $sql_res[0]['content']);
                    $sql_res[0]['content'] = str_replace('name="schedule_name"', 'name="schedule_name" value="'.$schedule->name.'"', $sql_res[0]['content']);
                }
            }
        }

        // If the setting to auto-prefix pages with an AddThis is toolbox is enabled...
        // ... add the toolbox, if it has not already been added to the page.
        $already_has_toolbox = strpos($original_content, '{addthis_toolbox') !== false;
        $auto_add_toolbox    = Settings::instance()->get('auto_addthis_on_pages');
        $is_content_page     = strpos($sql_res[0]['layout'], 'content') === 0;

        if (!$already_has_toolbox && $auto_add_toolbox && $is_content_page) {
            $sql_res[0]['content'] = IbHelpers::addthis_toolbox() . $sql_res[0]['content'];
        }

		if ($sql_res[0]['x_robots_tag'] != '') {
			$this->response->headers('X-Robots-Tag', $sql_res[0]['x_robots_tag']);
		}

		//Get current url for interpretation and id's needed for seo queries
		$current = $this->request->uri();
		$article_id = $this->request->initial()->param('article_id');
		$url = trim(Request::current()->url(),'/');
		$product_url = $news_url = urldecode(substr($url, strrpos($url, '/') + 1)); // content after the last slash
		$news_url = explode(".", $news_url); // content after the last slash, excluding the ".html"
		$news_title = $news_url[0];

		// If the theme has been overwritten at page level
		if ($sql_res[0]['theme'] != '' AND Settings::instance()->get('use_config_file') === '0')
		{
			Kohana::$config->load('config')->set('assets_folder_path', $sql_res[0]['theme']);
		}

		//Build the SEO fields based on page name
		if ((strpos($current, 'articles') !== FALSE) and ($article_id <> '')) //if we are viewing an ARTICLES details page
		{
			//get ARTICLES SEO fields for article in question
			$article_data = Model_Article::get($article_id);

			//check that the return is not null before setting
			if (!empty($article_data))
			{
				$sql_res[0]['title'] = $article_data['seo_title'];
				$sql_res[0]['seo_keywords'] = $article_data['seo_keywords'];
				$sql_res[0]['seo_description'] = $article_data['seo_description'];
			}
		}
		elseif ((strpos($current, 'products') !== FALSE) and ($product_url <> '')) //if we are viewing a PRODUCTS details page
		{
			// Get PRODUCTS SEO fields
			$product_data = Model_Product::get_by_product_url($product_url);

			//check that the return is not null before setting
			if ( ! empty($product_data))
			{
				$sql_res[0]['title'] = $product_data['seo_title'];
				$sql_res[0]['seo_keywords'] = $product_data['seo_keywords'];
				$sql_res[0]['seo_description'] = $product_data['seo_description'];
				$sql_res[0]['product_data'] = $product_data;

				// If the theme has been overwritten by the product's category
				if (isset($product_data['id']) AND Settings::instance()->get('use_config_file') === '0')
				{
					$theme = Model_Product::get_theme($product_data['id']);
				}
			}
			else // Not a product. Check if it's a product category.
			{
				$category_data = Model_Category::get_by_name($product_url);
				$category_data = isset($category_data[0]) ? $category_data[0] : NULL;

				if ( ! empty($category_data['seo_title']))
				{
					$sql_res[0]['title'] = $category_data['seo_title'];
				}
				if ( ! empty($category_data['seo_keywords']))
				{
					$sql_res[0]['seo_keywords'] = $category_data['seo_keywords'];
				}
				if ( ! empty($category_data['seo_description']))
				{
					$sql_res[0]['seo_description'] = $category_data['seo_description'];
				}

				// If the theme has been overwritten at product category level
				if (isset($category_data['id']) AND Settings::instance()->get('use_config_file') === '0')
				{
					$theme = Model_Category::get_theme($category_data['id']);
				}
			}

			// If the product or category has a theme set
			if (isset($theme) AND $theme != '')
			{
				Kohana::$config->load('config')->set('assets_folder_path', $theme);
			}


		}
		elseif ((strpos($current, 'news') !== FALSE) and ($news_title <> ''))
		{ //if we are viewing a NEWS details page
			//get NEWS SEO fields
			//replace "-" with spaces and upper case the entire title for comparison
			$clean_newstitle = strtolower(str_replace('-', ' ', $news_title));
			//get news item from new cleaned string for news
            $news_id = Model_News::get_news_id($clean_newstitle);
			$news_data = Model_News::get_all_items_front_end($news_id, $current_item_category);

			//check that the return is not null before setting
			if (!empty($news_data))
			{
				//have a value so lets set seo fields for the view to display
				$sql_res[0]['title'] = $news_data[0]['seo_title'] ? $news_data[0]['seo_title'] : $news_data[0]['title'];
				$sql_res[0]['seo_keywords'] = $news_data[0]['seo_keywords'];
				$sql_res[0]['seo_description'] = $news_data[0]['seo_description'];
			}
		}

		// SEO settings
		$seo_title_text = Settings::instance()->get('seo_title_text');
		$seo_title_position = Settings::instance()->get('seo_title_text_position');
		$seo_title_text_separator = Settings::instance()->get('seo_title_text_separator');
		$seo_keywords = Settings::instance()->get('seo_keywords');
		$seo_description = Settings::instance()->get('seo_description');
		$sql_res[0]['head_html'] = Settings::instance()->get('head_html');

		$sql_res[0]['page_title'] = $sql_res[0]['title'];

		if ($seo_title_text != '')
		{
			if ($seo_title_position == 'right')
			{
				$sql_res[0]['title'] = $sql_res[0]['title'].' '.$seo_title_text_separator.' '.$seo_title_text;
			}
			else
			{
				$sql_res[0]['title'] = $seo_title_text.' '.$seo_title_text_separator.' '.$sql_res[0]['title'];
			}
		}
		if ($sql_res[0]['seo_keywords'] == '')
		{
			$sql_res[0]['seo_keywords'] = $seo_keywords;
		}
		if ($sql_res[0]['seo_description'] == '')
		{
			$sql_res[0]['seo_description'] = $seo_description;
		}

		//Assets will be flagged if already implemented in the page here
        $sql_res[0]['assets_implemented'] = array('browser_sniffer' => false);
		// Logo
		$custom_logo = Settings::instance()->get('site_logo');
		if ($custom_logo == '')
		{
			$logo = URL::get_skin_urlpath().'images/logo.png';
		}
		else
		{
			$logo = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $custom_logo, 'logos');
		}
		$sql_res[0]['logo'] = $logo;

		$sql_res[0]['theme_home_page'] = Model_Pages::get_theme_home_page('name_tag');
		
		$sql_res[0]['common_head_data'] = View::factory("common_head_data",array('page_data' => $sql_res[0]));

		//Return
		if ($sql_res === false || $sql_res[0]['publish'] == 0)
			$this->response->body("Page not found");
		else
		{
			//Set Current Item Category and ID
			$sql_res[0]['current_item_category'] = $current_item_category;
			$sql_res[0]['current_item_identifier'] = $current_item_identifier;

			//if html sitemap requested load that
			if ($page == 'sitemap.html')
			{
				$sql_res[0]['content'] = View::factory("sitemap_html");
			}

			//if xml sitemap requested load that and avoid page template response
			if ($page == 'sitemap.xml')
			{
				//turn off html view output comments
				$view = View::factory('sitemap_xml')->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
				// return view
				$this->response->body($view);
				//set output to XML
				$this->response->headers('Content-Type', 'text/xml');
			}
			else
			{
				//Return corresponding View
				if (isset(Kohana::$config->load('config')->template_folder_path) AND Kohana::$config->load('config')->template_folder_path != '' AND Kohana::$config->load('config')->template_folder_path != NULL)
				{
					$template_folder = Kohana::$config->load('config')->template_folder_path;
				}
				else
				{
					$template_folder = 'default';
				}

				try
				{
					try {
						$sql_res[0]['content'] = preg_replace_callback('/\<a.*?\>/i', 'ibhelpers::fix_target_link', $sql_res[0]['content']);
					} catch (Exception $e) {

					}
					// If the layout is controlled via the CMS
					$layout = ORM::factory('Engine_Layout')->where('id', '=', $sql_res[0]['layout_id'])->find_published();
					if ($layout->use_db_source == 1)
					{
						$view = new View();
						$view->setSource($layout->get_full_source());
						$view->bind('page_data', $sql_res[0]);
					}
					else
					{
						$view = View::factory('templates/'.$template_folder.'/'.$sql_res[0]['layout'], array('page_data' => $sql_res[0]));
					}

				}
				catch (Exception $e)
				{
					// If the layout does not exist, use the default layout
                    $default_page_layout = Model_Engine_layout::get_default_layout();
					$default_page_layout_name = ($default_page_layout->layout == '') ? 'content' : $default_page_layout->layout;
					$view = View::factory('templates/'.$template_folder.'/'.$default_page_layout_name, array('page_data' => $sql_res[0]));
				}


				if (class_exists('Model_News_Category') and $sql_res[0]['name_tag'] == 'news.html')
				{
					$view->set('news_categories', ORM::factory('News_Category')->where('publish', '=', 1)->where('delete', '=', 0)->order_by('order')->find_all());
				}

				$layout = ( ! empty($sql_res[0]['layout'])) ? $sql_res[0]['layout'] : '';

				if ($layout == 'orderhistory' AND class_exists('Model_Cart'))
				{
					$view->set('order_history', Model_Cart::shopping_history(NULL, FALSE));
				}

				if ($layout == 'checkout' AND class_exists('Model_Product'))
				{
					$view->set('countries', ORM::factory('PostageCountry')->find_all_published());
					$view->set('checkout_data', Model_Product::render_checkout_html(FALSE));
				}


				elseif ($sql_res[0]['layout'] == 'home' AND Model_Plugin::is_enabled_for_role('Administrator', 'Events'))
				{

				}

                if (strtolower($sql_res[0]['layout']) == 'news') {
                    $current_item_category = isset($current_item_category) ? $current_item_category : 'News';
                    $news = Model_News::get_all_items_front_end($current_item_identifier, $current_item_category);
                    $items_per_page  = (int) Settings::instance()->get('news_feed_item_count');
                    $items_per_page  = $items_per_page ? $items_per_page : 10;
                    $number_of_pages = ceil(count($news) / $items_per_page);
                    $view->set('items_per_page', $items_per_page)->set('number_of_pages', $number_of_pages);
                }

                $view->set('theme', Model_Engine_Theme::get_current_theme());

				if ($layout AND class_exists('Model_Propman'))
				{
					if ($layout == 'propertydetails')
					{
						$property = ORM::factory('Propman')->where('url', '=', $this->request->param('item_category'))->find_published();
						$view->set('property_data', $property);
					}
					elseif ($layout == 'searchresults')
					{
						$building_types = ORM::factory('Propman_BuildingType')->find_all_published();
						$search_results = Model_Propman::search_results($this->request->query());
						$view->set('building_types', $building_types);
						$view->set('search_results', $search_results);
					}
					elseif ($layout == 'bookingpage')
					{
						$property  = ORM::factory('Propman')->where('id', '=', $this->request->query('property_id'))->find_published();
						$query['check_in']  = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->query('check_in'))));
						$query['check_out'] = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->query('check_out'))));
						$query['guests']    = $this->request->query('guests');
						$view->set(array('property_data' => $property, 'query' => $query));
					}
				}

				// Parse the HTML for campaign-layout pages.
				// Get all images inside links and put the results in an array
				if ($layout == 'campaign')
				{
					$content = $sql_res[0]['content'];
					$preset_model = new Model_Presets;
					$media_model = new Model_Media;
					$dom = new DOMDocument;
					libxml_use_internal_errors(true); // do not generate errors for not wellformed htmls
					$doc->strictErrorChecking = false;
					// Get the page content as HTML
					$dom->loadHTML($content);

					$gallery = array();

					// Get all links from the page content
					foreach ($dom->getElementsByTagName('a') as $link)
					{
						// find images inside the link
						$images = $link->getElementsByTagName('img');
						foreach ($images as $image) ; // just use the last image (if any)
						if (isset($image))
						{
							$filename_parts = explode('/', $image->getAttribute('src'));
							$file = $media_model->get_by_filename(end($filename_parts), 'campaign');

							$preset = $preset_model->get_all_items_admin($file['preset_id']);
							$preset = isset($preset[0]) ? $preset[0] : FALSE;

							$item['href'] = $link->getAttribute('href');
							$item['src'] = $image->getAttribute('src');
							$item['image'] = $file;
							$item['preset'] = $preset;

							$gallery[] = $item;
						}

					}

					$view->set('campaign_gallery', $gallery);
				}

				if (isset(Kohana::$config->load('config')->template_folder_path) AND Kohana::$config->load('config')->template_folder_path == 'systems')
				{
					// Get categories for search
					$array = array();
					$top_level_categories = Model_Category::get(NULL, NULL, $array, 0, FALSE);
					$view->set('top_level_categories', $top_level_categories);
				}

				if (isset(Kohana::$config->load('config')->template_folder_path) AND Kohana::$config->load('config')->template_folder_path == 'books')
				{
					// Get all top level categories relevant to the theme
					$product_categories = ORM::factory('Product_Category')->where_top_level()->where_in_theme()->order_by('order')->order_by('category')->find_all_published();

					$view
						->set('product_categories', $product_categories)
						->set('products_page', Model_Product::get_products_plugin_page())
						->set('localisation_languages', $localisation_languages);
				}

				if ($data_helper_call) {
					call_user_func($data_helper_call, $view);
				}

                // The page loaded as an ORM object
                $page_object = new Model_Page($sql_res[0]['id']);
                $view->set('page_object', $page_object);

				$this->response->body($view);


			}


		}
        
		//var_dump($_SERVER);
		//var_dump(is_dir('../views/layouts/'));

		//$model = new Model_Pages();
		//$msg2 = $model->get_template();

		//$this->response->body(View::factory('layouts/template22'));
	}
}
