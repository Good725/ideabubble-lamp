<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Gallery extends Model
{
    // Tables
    const TABLE_GALLERY = 'plugin_gallery_gallery';

    // Fields
    private $id;
    private $photo_name;
    private $category;
    private $title;
    private $order;
    private $publish;

    //
    // PUBLIC FUNCTIONS
    //

    /**
     * @param int $id The gallery identifier.
     */
    public function __construct($id = NULL)
    {
        if (isset($id))
        {
            // If we cannot load the gallery, go to hell
            if ( ! $this->load($id) )
                throw new Exception(get_class().': Unable to initialize the class.');
        }
        else
        {
            $this->id         = NULL;
            $this->photo_name = NULL;
            $this->category   = NULL;
            $this->title      = NULL;
            $this->order      = NULL;
            $this->publish    = NULL;
        }
    }

    /**
     * @return array An array(id, photo_name, category, title, order, publish) with the client's details.
     */
    public function get_data()
    {
        $r = array
        (
            'id'           => $this->id,
            'photo_name'   => $this->photo_name,
            'category'     => $this->category,
            'title'        => $this->title,
            'order'        => $this->order,
            'publish'      => $this->publish,
        );

        return $r;
    }

    /**
     * @param string $photo_name The photo name.
     */
    public function set_photo_name($photo_name)
    {
        $this->photo_name = trim($photo_name);
    }

    /**
     * @param string $category The gallery category.
     */
    public function set_category($category)
    {
        $this->category = trim($category);
    }

    /**
     * @param string $title The gallery title.
     */
    public function set_title($title)
    {
        $this->title = trim($title);
    }

    /**
     * @param string $order The gallery display order.
     */
    public function set_order($order)
    {
        $this->order = $order;
    }

    /**
     * @param int $publish The publish option.
     */
    public function set_publish($publish)
    {
        $this->publish = (int) $publish;
    }

    /**
     * @return bool TRUE if the details are saved. Otherwise, FALSE.
     */
    public function save()
    {
        $ok = FALSE;
        $db = Database::instance();

        if ( ! is_null($db) AND $db->begin() )
        {
            try
            {
                if ($this->id == NULL)
                {
                    // Add a new gallery
                    $ok = $this->sql_add_gallery($this->build_insert_array());
                }
                else
                {
                    // Update an existing gallery
                    $ok = $this->sql_update_gallery($this->id, $this->build_update_array());
                }

                // If no errors, commit the transaction. Otherwise, throw an exception.
                if ($ok === FALSE)
                    throw new Exception();
                else
                {
                    $ok = $db->commit();
                }
            }
            catch (Exception $e)
            {
                // Rollback the transaction
                $db->rollback();
            }
        }

        return $ok;
    }

    //
    // STATIC/SERVICE FUNCTIONS (DO NOT ABUSE OF THEM)
    //

    /**
     * @return array An array with the categories.
     */
    public static function get_categories()
    {
        $list = array();

        // Execute the query
        $r = DB::select('category')
                 ->distinct('category')
                 ->from(Model_Gallery::TABLE_GALLERY)
                 ->where('deleted', '=', 0)
                 ->order_by('category')
                 ->execute()
                 ->as_array();

        // Add the lists to the array
        for ($i = 0; $i < count($r); $i++)
        {
            array_push($list, $r[$i]['category']);
        }

        return $list;
    }

    /**
     * @return array An array(id, photo_name, category, title, order, publish, date_modified, date_entered, created_by, modified_by) with all the contact's details.
     */
    public static function get_gallery_all($category = NULL)
    {
        // Build partially the query
        $r = DB::select('id', 'photo_name', 'category', 'title', 'order', 'publish', 'date_modified', 'date_entered', 'created_by', 'modified_by')
                 ->from(Model_Gallery::TABLE_GALLERY)
                 ->where('deleted', '=', 0)
                 ->order_by('order');

        // If category is specified, then add to the where clause
        if ($category != NULL)
        {
            $r->where('category', '=', $category);
        }

        // Return the result of the query execution
        return $r->execute()->as_array();
    }

    /**
     * @param int $id The gallery identifier.
     * @return bool TRUE if the function success. Otherwise, FALSE.
     */
    public static function delete_gallery($id)
    {
        try
        {
            $r = DB::update(Model_Gallery::TABLE_GALLERY)
                     ->set(array('deleted' => 1))
                     ->where('id', '=', $id)
                     ->execute();

            $ok = ($r == 1);
        }
        catch (Exception $e)
        {
            $ok = FALSE;
        }

        return $ok;
    }

    /**
     * @param int $id The gallery identifier.
     * @return bool TRUE if the function success. Otherwise, FALSE.
     */
    public static function toggle_gallery_publish($id)
    {
        $ok = TRUE;

        try
        {
            $r = DB::select('publish')
                     ->from(Model_Gallery::TABLE_GALLERY)
                     ->where('id', '=', $id)
                     ->execute()
                     ->as_array();

            if (count($r) == 1)
            {
                $publish = ($r[0]['publish'] == 1) ? 0 : 1;

                $r = DB::update(Model_Gallery::TABLE_GALLERY)
                         ->set(array('publish' => $publish))
                         ->where('id', '=', $id)
                         ->execute();

                $ok = ($r == 1);
            }
        }
        catch (Exception $e)
        {
            $ok = FALSE;
        }

        return $ok;
    }

    /**
     * @return array of errors
     * @throws Exception
     */
    public static function service_validate_submit()
    {
        try
        {
			$errors = array();

            // Photo name is mandatory
            if ( ! isset($_POST['photo_name']) OR (trim($_POST['photo_name']) == '') OR ($_POST['photo_name'] == 'dummy'))
			{
				$errors[] = __('You must select a picture.');
			}

			// Title is mandatory
			if ( ! isset($_POST['title']) OR (trim($_POST['title']) == ''))
			{
				$errors[] = __('You must select a title.');
			}

			// Order is mandatory and should be an integer
			if ( ! isset($_POST['order']) OR ! is_numeric($_POST['order']) OR ! ctype_digit($_POST['order']))
			{
				$errors[] = __('You must select enter a numeric value for "order".');
			}

            // Publish should be TRUE(1) or FALSE(0)
            if ( ! isset($_POST['publish']) OR (($_POST['publish'] != 1) AND ($_POST['publish'] != 0)))
			{
				$errors[] = __('Invalid value entered for "publish".');
			}
        }
        catch (Exception $e)
        {
            // Pass the exception to the caller. If we are here is (1) because there is a malformed request or (2) there is
            // a serious problem in the DB

			// Write the error to the system logs
			Log::instance()->add(Log::ERROR, $e->getMessage()."\n".$e->getTraceAsString());

			throw $e;
        }

        return $errors;
    }

    // HELPERS

    /**
     * This function display a JS slider with the provided category
     * @param null $category
     * @param string $gallery_element_html OPTIONAL
     * @param string $gallery_container_html OPTIONAL
     * @return HTML UL
     */
    public static function get_category_slider($category = NULL, $gallery_element_html = 'gallery_element_html' ,$gallery_container_html = 'gallery_container_html' ){
        $html = '';
        $html_li = '';
        $images = Model_Gallery::get_gallery_all($category);
        foreach($images as $image){
            $data_element['image'] = $image;
            $data_element['image']['url'] = URL::site() .Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'gallery') . $image['photo_name'];
            $html_li .= View::factory('front_end/' . $gallery_element_html, $data_element);
        }
        $elements['elements'] = $html_li;
        $html .= View::factory('front_end/' . $gallery_container_html, $elements);

        return $html;
    }

    public static function get_category_images($category)
    {
		if ($category == NULL)
		{
			// If there is no category, check if one is specified in the URL
			$url_parts = explode('/', Request::detect_uri());
			$category = isset($url_parts[2]) ? $url_parts[2] : NULL;
		}

		if ($category == NULL)
		{
			// If no category has been specified, show a list of all gallery categories
			$categories = self::get_categories();
			$galleries = array();
			foreach ($categories as $category)
			{
				if ($category != '') $galleries[$category] = self::get_gallery_all($category);
			}
			$filepath = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'gallery');
			return View::factory('front_end/view_galleries')->set('galleries', $galleries)->set('filepath', $filepath);
		}
		else
		{
			// If a category has been specified, show the gallery for that category
			$images = Model_Gallery::get_gallery_all($category);
			return View::factory('front_end/gallery_category_listing',array('images' =>$images));
		}

    }

    //
    // PRIVATE FUNCTIONS
    //

    /**
     * @param int $id The gallery identifier.
     * @return bool TRUE is the function success. Otherwise, FALSE.
     */
    private function load($id)
    {
        $ok = FALSE;

        // Get the gallery
        $r = $this->sql_get_gallery($id);

        if ($r !== FALSE)
        {
            $ok = TRUE;

            // Store values into the class properties
            $this->id         = $r[0]['id'        ];
            $this->photo_name = $r[0]['photo_name'];
            $this->category   = $r[0]['category'  ];
            $this->title      = $r[0]['title'     ];
            $this->order      = $r[0]['order'     ];
            $this->publish    = $r[0]['publish'   ];
        }

        return $ok;
    }

    /**
     * @return array An array ready to be used in an INSERT.
     */
    private function build_insert_array()
    {
        // Get the current user
        $logged_user = Auth::instance()->get_user();

        // Create the array with all the values
        $insert_array = array
        (
            'photo_name'   => $this->photo_name,
            'category'     => $this->category,
            'title'        => $this->title,
            'order'        => $this->order,
            'publish'      => $this->publish,
            'date_entered' => date('Y-m-d H:i:s', time()),
            'created_by'   => $logged_user['id'],
        );

        return $insert_array;
    }

    /**
     * @return array An array ready to be used in an UPDATE.
     */
    private function build_update_array()
    {
        // Get the current user
        $logged_user = Auth::instance()->get_user();

        // Create the array with all the values
        $update_array = array
        (
            'photo_name'  => $this->photo_name,
            'category'    => $this->category,
            'title'       => $this->title,
            'order'       => $this->order,
            'publish'     => $this->publish,
            'modified_by' => $logged_user['id'],
        );

        return $update_array;
    }

    //
    // SQL FUNCTIONS
    //

    /**
     * @param int $id The gallery identifier.
     * @return bool TRUE is the function success. Otherwise, FALSE.
     */
    private function sql_get_gallery($id)
    {
        // Execute the query
        $r = DB::select('id', 'photo_name', 'category', 'title', 'order', 'publish', 'date_modified', 'date_entered', 'created_by', 'modified_by')
                 ->from(Model_Gallery::TABLE_GALLERY)
                 ->where('id', '=', $id)
                 ->execute()
                 ->as_array();

        return (count($r) == 1) ? $r : FALSE;
    }

    /**
     * @param array $details An insert array.
     * @return mixed The identifier of the gallery inserted. Otherwise, FALSE.
     */
    private function sql_add_gallery($insert_array)
    {
        $r = DB::insert(Model_Gallery::TABLE_GALLERY, array_keys($insert_array))
                 ->values(array_values($insert_array))
                 ->execute();

        return ($r[1] == 1) ? $r[0] : FALSE;
    }

    /**
     * @param int $id The gallery identifier.
     * @param array $details An update array.
     * @return bool TRUE is the function success. Otherwise, FALSE.
     */
    private function sql_update_gallery($id, $update_array)
    {
        $r = DB::update(Model_Gallery::TABLE_GALLERY)
                 ->set($update_array)
                 ->where('id', '=', $id)
                 ->execute();

        // Changed or not, assume update was successful (otherwise an exception will be raised)
        return TRUE;
    }
}