<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Output the html code of a menu structure in a list
 *
 * <ul class='[parameter or menu name]'>
 *
 * <li id='item_[id menu]' class='level_[nested level] item_[item number in the list] [first] [last] [current]>
 */
class menuhelper
{
    public static $query_sort = array();
    public static $level = 1;
    public static $category = '';


    /**
     * @static
     * @param string $category
     * @param string $class_name
     * @return View
     */
    public static function add_menu($category = '', $class_name = '', $last = '', $echo = TRUE)
    {
        self::$query_sort = array();
        if (empty($category)) {
            return '';
        }

        menuhelper::$category = $category;
        //Save the menu sorted in a array
        $mmenu = menuhelper::get_all_menus($category);


        if (empty($class_name)) {
            $class_name = str_replace(' ', '_', $category);
            $class_name = htmlspecialchars($category, ENT_QUOTES); //Prevent XSS
        }


        $lv0 = array();
        $item_number = 1;
        $first = TRUE;
        foreach ($mmenu as $item) {
            $item['last'] = '';
            $item['link'] = menuhelper::get_link($item);
            $item['current'] = menuhelper::set_selected_menu($item);
            if ($item['level'] === 1) {
                if ($first == TRUE) {
                    $item['first'] = 'first';
                    $first = FALSE;
                } else {
                    $item['first'] = '';
                }
                $item['item_number'] = $item_number++;
                $lv0[] = menuhelper::secure_filter_menu($item);
            }

        }
        if (count($lv0) > 0) {
            $lv0[count($lv0) - 1]['last'] = 'last';
        }

        $view = View::factory('snipet_menuhelper');
        $view->class_name = $class_name;
        $view->last = $last;
        $view->menu = $lv0;

        //check if to echo or return the result
        if ($echo) {
            echo $view;
        } else
            return $view;


//$m=array (
//    'item1',
//    'item2',
//    'item3' => array(
//        'subitem1',
//        'subitem2' => array(
//            'subsubitem1'
//            'subsubitem2'
//            'subsubitem3'
//        )
//    ),
//);

    }

    public static function add_menu_editable_heading($category = '', $class_name = '', $last = '', $echo = TRUE)
    {
        self::$query_sort = array();
        if (empty($category)) {
            return '';
        }

        menuhelper::$category = $category;
        //Save the menu sorted in a array
        $mmenu = menuhelper::get_all_published_menus($category);

        if (empty($mmenu) AND (Settings::instance()->get('main_menu_products') != 1 OR $category != 'main')) {
            return '';
        }

        if (empty($class_name)) {
            $class_name = str_replace(' ', '_', $category);
            $class_name = htmlspecialchars($category, ENT_QUOTES); //Prevent XSS
        }


        $lv0 = array();
        $item_number = 1;
        $first = TRUE;
        foreach ($mmenu as $item) {
            $item['last'] = '';
            $item['link'] = menuhelper::get_link($item);
            $item['current'] = menuhelper::set_selected_menu($item);
            if ($item['level'] === 1) {
                if ($first == TRUE) {
                    $item['first'] = 'first';
                    $first = FALSE;
                } else {
                    $item['first'] = '';
                }
                $item['item_number'] = $item_number++;
                $lv0[] = menuhelper::secure_filter_menu($item);
            }

        }
        if (count($lv0) > 0) {
            $lv0[count($lv0) - 1]['last'] = 'last';
        }

        $view = View::factory('snippet_menuhelper_editable_heading');
        $view->class_name = str_replace(' ', '-', $class_name);
        $view->last = $last;
        $view->menu = $lv0;

        //check if to echo or return the result
        if ($echo) {
            echo $view;
        } else
            return $view;

    }

    /**
     * @static
     * @param $parent
     * @return array
     */
    public static function submenu($parent)
    {
        $menu = menuhelper::$query_sort;
        $submenu_array = array();
        $get_lv = ((int)$parent['level']) + 1;
        $found = FALSE;
        $item_number = 1;
        $first = TRUE;
        foreach ($menu as $item) {
            if ((int)$item['level'] == $get_lv && $item['parent_id'] == $parent['id']) {
                $found = TRUE;
            } elseif ((int)$item['level'] != $get_lv) {
                $found = FALSE;
            }

            if ($found) {
                $item['last'] = '';
                $item['link'] = menuhelper::get_link($item);
                $item['current'] = menuhelper::set_selected_menu($item);
                if ($first == TRUE) {
                    $item['first'] = 'first';
                    $first = FALSE;
                } else {
                    $item['first'] = '';
                }
                $item['link'] = menuhelper::get_link($item);

                if ( ! empty($item['image_id']))
                {
                    $image = Model_Media::get_image_filename($item['image_id']);
                    $item['filename']= isset($image['filename']) ? $image['filename'] : '';
                    $item['location'] = isset($image['location']) ? $image['location'] : '';
                }
                else
                {
                    $item['filename'] = '';
                    $item['location'] = '';
                }

                $item['item_number'] = $item_number++;
                $item = menuhelper::secure_filter_menu($item);
                $submenu_array[] = $item;
            }
        }
        if (count($submenu_array) > 0) {
            $submenu_array[count($submenu_array) - 1]['last'] = 'last';
        }

        return $submenu_array;
    }

    public static function get_link($item)
    {
		static $localisation_system_active = null;
        static $localisation_language_count = null;

		if($localisation_system_active === null){
			$localisation_system_active = (Settings::instance()->get('localisation_system_active') == '1');
		}

        if ($localisation_language_count === null) {
            $localisation_language_count = count(i18n::get_allowed_languages());
        }

        if ($item['link_tag'] == '0')
		{
            $link = $item['link_url'];
        }
		elseif ($item['link_tag'] == '-1')
		{
			$link = '/'.Model_Pages::get_theme_home_page('name_tag');
		}
		else
		{
            $query = DB::query(Database::SELECT, 'SELECT name_tag FROM plugin_pages_pages WHERE id = :id')
                ->parameters(array(':id' => $item['link_tag']))->execute()->as_array();
            if (count($query) > 0) {
				$locale_append = "";
				if ($localisation_system_active && $localisation_language_count > 1){
					if(I18n::$lang){
						$locale_append = I18n::$lang . '/';
					}
				}

                $name_tag = $query[0]['name_tag'];

                // Remove the microsite suffix from the name tag
                if (!empty(Kohana::$config->load('config')->project_suffix)) {
                    $name_tag = preg_replace('/(.*)--(.*)/', '$1', $name_tag);
                }

                $link = URL::site() . $locale_append . $name_tag;
            }
        }

        if (empty($link)) {
            return '#';
        }
        return $link;
    }

    // Get saved attributes as an associative array
    public static function get_attributes($item)
    {
        return new SimpleXMLElement('<element ' . $item['html_attributes'] . '/>');
    }

    public static function is_active($item)
    {
        $page = Model_Pages::get_page(Request::$current->param('page'), true);

        if (!isset($page[0])) {
            return false;
        } else {
            $page = $page[0];
        }

        switch ($item['link_tag'] ) {
            case 0:
                // If a URL is saved in the menu item...

                if ($item['link_url'] == '#') {
                    return false;
                }

                $link_url    = trim($item['link_url'], '/');
                $link        = parse_url($item['link_url'], PHP_URL_HOST);
                $link_domain = str_ireplace('www.', '', $link['host']);
                $link_path   = trim(str_ireplace('.html', '', $link['path']), '/');

                $page_domain = trim(URL::base(), '/');
                $page_path   = trim($page['name_tag'], '/');

                // Check if the URL matches the current page as a relative path or as an absolute path.
                return ($page_path == str_ireplace('.html', '', $link_url) || ($link_domain == $page_domain && $link_path == $page_path));
                break;

            case -1:
                // If the link is to the home page, check if the current page is the home page.
                return ($page['id'] == Model_Pages::get_theme_home_page('id'));
                break;

            default:
                // If the menu item is directly connected to a page, see if it has the same ID as the current page.
                return ($page['id'] == $item['link_tag']);
                break;
        }
    }

    public static function attributes($item)
    {
        $return  = ' href="'.self::get_link($item).'"';
        $return .= ' target="'.$item['menus_target'].'"';
        $return .= ' data-id="'.$item['id'].'"';

        return $return;
    }

    /**
     * Filter the array, prevent XSS atack.
     * @static
     * @param $item
     * @return array
     */
    public static function secure_filter_menu($item)
    {
		static $localisation_system_active = null;
		if($localisation_system_active === null){
			$localisation_system_active = Settings::instance()->get('localisation_system_active') == '1';
		}
		if($localisation_system_active){
			$item['title'] = __($item['title']);
		}
		$item['link'] = htmlspecialchars($item['link'], ENT_QUOTES); //Prevent XSS
        $item['title'] = htmlspecialchars($item['title'], ENT_QUOTES);
        return $item;
    }

    public static function set_selected_menu($item)
    {
        $current_tmp = explode(DIRECTORY_SEPARATOR, $_SERVER['PHP_SELF']);
        $current     = end($current_tmp);
		$parsed_link = parse_url($item['link']);
		$current     = (isset($parsed_link['path']) AND stripos($parsed_link['path'],$current) !== FALSE) ? 'current' : '';
        return $current;
    }

    /**
     *
     * Save the menu sorted in a array ($query_sort);
     * @param Array
     * @return Array
     */
    public static function get_all_menus($category)
    {

        $query = DB::query(Database::SELECT, 'SELECT * FROM plugin_menus WHERE deleted = 0 AND parent_id = 0 AND category = :category ORDER BY category, menu_order')
            ->parameters(array(':category' => $category))->execute()->as_array();


        foreach ($query as $res) {
            //If has submenu call similar function recursively
            if ($res['has_sub'] === "1") {
                $res['level'] = menuhelper::$level;
                menuhelper::$query_sort[] = $res;
                menuhelper::get_submenu($res, $category);
            } else {
                //Else add to the Array
                $res['level'] = menuhelper::$level;
                menuhelper::$query_sort[] = $res;
            }
        }
        return menuhelper::$query_sort;
    }

    /**
     *
     * Save the menu sorted in a array ($query_sort);
     * @param Array
     * @return Array
     */
    public static function get_all_published_menus($category)
    {
		// Reset, so there are no lingering items from previous calls
		menuhelper::$query_sort = array();

        // Check if there are items in this category with the microsite suffix
        $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';
        $query = self::get_items(array('category' => $category.'--'.$microsite_suffix, 'parent_id' => 0));

        if (!empty($query)) {
            $category = $category.'--'.$microsite_suffix;
        } else {
            // Otherwise check the unsuffixed category
            $query = self::get_items(array('category' => $category, 'parent_id' => 0));
        }

        foreach ($query as $res) {
            //If has submenu call similar function recursively
            if ($res['has_sub'] === "1") {
                $res['level'] = menuhelper::$level;
                menuhelper::$query_sort[] = $res;
                menuhelper::get_published_submenu($res, $category);
            } else {
                //Else add to the Array
                $res['level'] = menuhelper::$level;
                menuhelper::$query_sort[] = $res;
            }
        }
        return menuhelper::$query_sort;
    }

    public static function get_items($args = array())
    {
        $query = DB::select(
            'menu.id', 'menu.category', 'menu.title', 'menu.link_tag', 'menu.link_url', 'menu.html_attributes',
            'menu.has_sub', 'menu.parenT_id', 'menu.parent_id', 'menu.menu_order', 'menu.menus_target', 'menu.image_id',
            'media.filename', 'media.location', 'page.name_tag'
        )
            ->from(array('plugin_menus', 'menu'))
            ->join(array('plugin_media_shared_media', 'media'), 'left')->on('menu.image_id', '=', 'media.id')
            ->join(array('plugin_pages_pages',        'page' ), 'left')->on('menu.link_tag', '=', 'page.id')
            ->where('menu.deleted', '=', 0)
            ->where('menu.publish', '=', 1)
            ->order_by('menu.menu_order')
        ;

        if (!empty($args['category'])) {
            $query->where('menu.category', '=', $args['category']);
        }
        if (isset($args['parent_id'])) {
            $query->where('menu.parent_id', '=', $args['parent_id']);
        }

        return $query->execute()->as_array();
    }

    /**
     * Get a menu as an array with nested arrays for submenus
     * @param array $args
     * @return mixed
     */
    public static function get_nested_menu($args = array())
    {
        if (is_string($args)) {
            $args = array('category' => $args);
        }

        $category  = isset($args['category'])  ? $args['category']  : '';
        $parent_id = isset($args['parent_id']) ? $args['parent_id'] : 0;
        $level     = isset($args['level'])     ? $args['level']     : 1;
        $depth     = isset($args['depth'])     ? $args['depth']     : 5;

        $menu      = self::get_items(array('category' => $category, 'parent_id' => $parent_id));


        foreach ($menu as &$item) {
            $item['submenu'] = ($level > $depth) ? array() : self::get_nested_menu(array('category' => $category, 'parent_id' => $item['id'], 'level' => $level+1, 'depth' => $depth));
        }

        return $menu;
    }




    /**
     * Save the menu sorted in a array ($query_sort), recursive calls.
     *
     * @static
     * @param $current_row
     * @param $category
     */
    public static function get_submenu($current_row, $category)
    {
        menuhelper::$level++;
        $query = DB::query(Database::SELECT, 'SELECT * FROM plugin_menus WHERE deleted = 0 AND publish = 1 AND parent_id = :parent_id AND category = :category ORDER BY category, menu_order');

        $query->parameters(array(
            ':parent_id' => $current_row['id'],
            ':category' => $category
        ));

        $query = $query->execute()->as_array();
        foreach ($query as $res) {
            //If has submenu call similar function recursively
            if ($res['has_sub'] === "1") {
                $res['level'] = menuhelper::$level;
                menuhelper::$query_sort[] = $res;
                menuhelper::get_submenu($res, $category);
            } else {
                //Else add to the Array
                $res['level'] = menuhelper::$level;
                menuhelper::$query_sort[] = $res;
            }
        }
        menuhelper::$level--;

    }

    /**
     * Save the menu sorted in a array ($query_sort), recursive calls.
     *
     * @static
     * @param $current_row
     * @param $category
     */
    public static function get_published_submenu($current_row, $category)
    {
        menuhelper::$level++;
        $query = DB::query(Database::SELECT, 'SELECT * FROM plugin_menus WHERE publish = 1 AND deleted = 0 AND parent_id = :parent_id AND category = :category ORDER BY category, menu_order');

        $query->parameters(array(
            ':parent_id' => $current_row['id'],
            ':category' => $category
        ));

        $query = $query->execute()->as_array();
        foreach ($query as $res) {
            //If has submenu call similar function recursively
            if ($res['has_sub'] === "1") {
                $res['level'] = menuhelper::$level;
                menuhelper::$query_sort[] = $res;
                menuhelper::get_submenu($res, $category);
            } else {
                //Else add to the Array
                $res['level'] = menuhelper::$level;
                menuhelper::$query_sort[] = $res;
            }
        }
        menuhelper::$level--;
    }

	/**
	 * Get child or sibling submenus, by specifying a page id
	 *
	 * @param	$page_id	int		The ID of the page
	 * @param	$category	string	The name of the menu category
	 * @return	array
	 */
	public static function get_submenus_for_page($page_id, $category)
	{
		// Get the menu item corresponding to the supplied page ID
		$current = DB::select()
			->from('plugin_menus')
			->where('link_tag', '=', $page_id)
			->where('category', '=', $category)
			->where('publish',  '=', 1)
			->where('deleted',  '=', 0)
			->execute()
			->current();

		// If the menu item doesn't exist, return nothing
		if ( ! $current)
		{
			return array();
		}
		else
		{
			// If the item has children, get the children. Otherwise get its siblings
			$parent_id = ($current['has_sub'] == 1) ? $current['id'] : $current['parent_id'];

			// Don't get siblings for top-level menus
			if ($parent_id == 0)
			{
				return array();
			}

			return DB::select('menu.id', 'menu.title', 'menu.link_url', array('page.id', 'page_id'), array('page.name_tag', 'page_url'))
				->from(array('plugin_menus', 'menu'))
				->join(array('plugin_pages_pages', 'page'), 'LEFT')
				->on('menu.link_tag', '=', 'page.id')
				->where('menu.category', '=', $category)
				->where('menu.parent_id', '=', $parent_id)
				->where('menu.publish',  '=', 1)
				->where('menu.deleted',  '=', 0)
				->order_by('menu.menu_order')
				->execute()
				->as_array();
		}
	}

}