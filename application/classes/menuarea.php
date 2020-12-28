<?php
/**
 * Purpose: Meny area representation (header menu left side).
 * Rendered in cms.php and shown in header.php.
 */

class MenuArea
{
	private static $menu_area;
	private $registered_links = array();
    private $breadcrumb_links = array();
    private $mobile_breadcrumb_links = array();
	private $sub_menu_links = array();

	public function __construct() {
		// Temporary disable dashboard for ibis staging
        /*
		if ( !(PROJECTNAME=='ibis' && Kohana::$environment == Kohana::STAGING)) {
			$this->register_link('/', 'Dashboard');
		}*/
	}

	/**
	 * return existing MenyArea object or create new one
	 */
	public static function factory() {
		return self::$menu_area ? self::$menu_area : self::$menu_area = new MenuArea();
	}

	/**
	 * @param $url
	 * @param $name Visible name in menu
	 * @param $activecontroller Menu entry will be marked as "active" if current controller will be equal with this parameter value
	 * @return void
	 */
	public function register_link($url, $name, $activecontroller = null, $icon = null, $flaticon = null, $svg = null)
	{
		$this->registered_links[$url] = array('url' => $url, 'name' => $name, 'controller' => ($activecontroller ? $activecontroller : $url), 'icon' => $icon, 'flaticon' => $flaticon, 'svg' => $svg);

        // Sort alphabetically by "name" column
        uasort($this->registered_links , function($a, $b){ return strcmp($a['name'], $b['name']);});
	}

	public function override_link($url, $params)
	{
		if (isset($this->registered_links[$url])) {
			$old = $this->registered_links[$url];
			$new = array_merge($old, $params);
			$this->registered_links[$url] = $new;
		}
	}

	public function get_links()
	{
		return $this->registered_links;
	}

	public function unregister_link($url = null)
	{
		if ($url !== null && $url != 'all') {
			unset($this->registered_links[$url]);
		} else {
			$this->registered_links = array();
		}
	}

	/**
	 * function should be called by header renderer
	 */
	public function generate_links($current_controller = NULL) {
		if ($current_controller)
		{
			$current_controller = strtolower($current_controller);
		}

		$links = array();

		$active_set = false;
		foreach ($this->registered_links as $entry)
		{
			$new_link = '<li data-controller="'.$entry['controller'].'" class="sidebar-menu-li ';

            if (class_exists('Controller_Admin_'.$entry['controller']))
            {
                $class = new ReflectionClass('Controller_Admin_'.$entry['controller']);
                if ($class->hasMethod('action_ajax_get_submenu'))
                {
                    $new_link .= 'has_submenu ';
                }
            }

			if (!$active_set) {
				if (Request::$current && $entry['controller'] == $current_controller . '/' . Request::$current->action()) {
					$new_link .= 'sidebar-menu-li--current active ';
					$active_set = true;
				} else {
					// If this is the current page add the active class
					if ($current_controller === $entry['controller']) {
						$new_link .= 'sidebar-menu-li--current active ';
					}
				}
			}

			$new_link .= '"><a class="maz" href="' . URL::Site('admin/' . $entry['url']) . '">' . __($entry['name']) . '</a></li>';
            $new_link = str_replace('class=""', '', $new_link);
            $links[] = $new_link;
		}
		//echo __('Hello, :user', array(':user' => $username));

		return $links;
	}

    /**
     * function should be called by header renderer
     */
    public function generate_links_h4($current_controller = NULL) {
        if ($current_controller)
        {
            $current_controller = strtolower($current_controller);
        }

        $links = array();

        foreach ($this->registered_links as $entry)
        {
            $links[] = "<li";

            // If this is the current page add the active class
            if ($current_controller === $entry['controller'])
            {
                $links[] .= " class='active'";
            }

            $links[] .= '><a href="' . URL::Site('admin/' . $entry['url']) . '"><h4>' . $entry['name'] . '</h4></a></li>';
        }

        return $links;
    }

	public function generate_icon_links()
	{
		$current_controller = strtolower(Request::current()->controller());
		$links = array();

		foreach ($this->registered_links as $entry)
		{
            $submenu = self::get_submenu($entry['controller']);
            // It also isn't active if it contains a .html and the action = index due to 'bookings.html' and 'timetables.html' in contacts3 plugin
			$active = (($current_controller === $entry['controller'] || $entry['controller'] == $current_controller . '/' . Request::current()->action())
                && ((strpos(Request::current()->uri(), '.html') !== -1 && Request::current()->action() === 'index')
                    || strpos(Request::current()->uri(),'.html') === false));

            if (!empty($entry['svg'])) {
                $icon = Ibhelpers::svg_sprite($entry['svg']);
            } elseif ($entry['flaticon']) {
                $icon = '<span class="flaticon-'.$entry['flaticon'].'"></span>';
            } elseif ($entry['icon']) {
                $icon = '<span style="background-image: url(\''.URL::overload_asset('img/dashboard/grey/inactive_') . $entry['icon'].'\');"></span>';
            } else {
                $icon = false;
            }

            $data = $entry;
            $data['active']      = $active;
            $data['has_icon']    = !!$icon;
            $data['has_submenu'] = !empty($submenu['items']);
            $data['icon_html']   = $icon;
            $data['submenu']     = $submenu;

            $links[] = $data;
		}

		return $links;
	}


	public function register_submenu_link($url, $name, $activecontroller = null, $icon = null) {
		$this->sub_menu_links[$url] = array('url' => $url, 'name' => $name, 'controller' => ($activecontroller ? $activecontroller : $url), 'icon' => $icon);
	}


	public function get_sub_menu_links($breadcrumbs = NULL)
    {
		return $this->sub_menu_links;
	}	
	public function generate_sub_menu_links($breadcrumbs = NULL)
    {
		$links = array();

		foreach ($this->sub_menu_links as $entry)
		{
			$link                = '<li';
			$entry_url           = explode('/', ltrim($entry['url'], '/'));
			if (count($entry_url) >= 2) {
                $entry_action        = $entry_url[1].'/'.(isset($entry_url[2]) ? $entry_url[2] : 'index');
            } else {
                $entry_action = '/index';
            }
            
            $matches_url         = ($entry_action == Request::current()->controller().'/'.Request::current()->action());

            $matches_breadcrumbs = FALSE;
            //if ( ! is_null($breadcrumbs))
            //{
                //$last_crumb          = end($breadcrumbs);
                //$matches_breadcrumbs = (ltrim($entry['url'], '/') == ltrim($last_crumb['link'], '/'));
                /* TODO: Check breadcrumbs for matches starting at the end
                for ($i = sizeof($breadcrumbs) - 1; $i >= 0 AND ! $matches_breadcrumbs; $i--)
                {
                    $matches_breadcrumbs = (ltrim($entry['url'], '/') == ltrim($breadcrumbs[$i]['link'], '/'));
                }
                */
           // }
			// If this is the current page add the active class
            if(Request::current()->action() == 'add_edit_discount' && $entry['name'] == 'Discounts')
            {
				$link .= " class='active'";
			}

            if ($matches_url OR $matches_breadcrumbs)
			{
				$link .= " class='active'";
			}

			$links[] = $link.'><a href="' . URL::Site( $entry['url']) . '">' . __($entry['name']) . '</a></li>';
		}
		//var_dump($links);
		return $links;
	}

	/**
	 * Will register 2nd level menu based on sidebar.
	 *
	 * If there is only one group entry then:
	 *      1. each link name will be menu entry
	 *      2. each name will be menu name
	 * else if there is more than one group
	 *      1. group name will be menu entry name
	 *      2. first link will be menu link
	 *
	 * 			'Users' => array(
	 				array(
	 					'name' => 'Manage Users',
	 					'link' => 'admin/settings/users'
	 				),
	 				array(
	 					'name' => 'Add User',
	 					'link' => 'admin/settings/add_user'
	 				),
	 			),
	 *
	 *
	 *             'Settings' => array(
	                 array(
	                     'name' => 'Platforms',
	                     'link' => 'admin/technopathsettings/products_cat'
	                 ),
	                 array(
	                     'name' => 'Instruments',
	                     'link' => 'admin/technopathsettings/products_subcat'
	                 ),
	                 array(
	                     'name' => 'Products',
	                     'link' => 'admin/technopathsettings/list_products'
	                 ),
	 *
	 * @param $menus
	 */
	public function register_sidmenu_as_sub_links($menus) {
		//var_dump($menus);
		if (count($menus)==1) {
			$menus = $menus[key($menus)]; // get first element
			foreach ($menus as  $menu) {
                $icon = isset($menu['icon']) ? $menu['icon'] : null;
				$this->register_submenu_link($menu['link'], $menu['name'], null, $icon);
			}

		} else {
			foreach ($menus as $group => $menu) {
                $icon = isset($menu['icon']) ? $menu['icon'] : null;
				$this->register_submenu_link($menu[0]['link'], $group, null, $icon);
			}

		}
		//var_dump($this->sub_menu_links);
	}

    public function register_breadcrumb_links($crumbs)
    {
        foreach ($crumbs as $crumb)
        {
            $this->breadcrumb_links[] = array('url' => $crumb['link'], 'name' => $crumb['name']);
        }
    }

    public function generate_breadcrumb_links()
    {
        $links = array();
        foreach ($this->breadcrumb_links as $entry)
        {
            $links[] = '<li><a href="' . URL::Site( $entry['url']) . '">' . __($entry['name']) . '</a></li>';
        }
        return $links;
    }

    public function register_mobile_breadcrumb_links($crumbs)
    {
        $prev_crumb = $crumbs[count($crumbs) - 2];
        $cur_crumb = end($crumbs);
        $this->mobile_breadcrumb_links['prev_url'] = array('url' => $prev_crumb['link'], 'name' => $prev_crumb['name']);
        $this->mobile_breadcrumb_links['curr_url'] = array('url' => $cur_crumb['link'], 'name' => $cur_crumb['name']);
    }

    public function generate_mobile_breadcrumb_links($breadcrumbs = array())
    {
        if (!empty($breadcrumbs)) {
            $prev_crumb = $breadcrumbs[count($breadcrumbs) - 2];
            $prev_url = !empty($prev_crumb) ?
                '<a href="' . URL::Site($prev_crumb['link'])
                . '" class="mobile-breadcrumbs-prev"><span class="arrow_caret-left"></span></a>' : '' ;
            $cur_crumb = end($breadcrumbs);
            $title = !empty($cur_crumb) ?
                '<h2 class="mobile-breadcrumbs-title"> '.$cur_crumb['name'].'</h2>' : '';
            $mobile_breadcrumbs = '<div class="col-xs-2 text-left">' . $prev_url .  '</div>' .
                '<div class="col-xs-8 text-center">' . $title . '</div>';
        } else {
            $next_url = !empty($this->mobile_breadcrumb_links['next_url']) ?
            '<div class="col-xs-2 text-right">' .
                '<a href="'. URL::Site($this->mobile_breadcrumb_links['next_url']['url']) .
                '" class="mobile-breadcrumbs-next"><span class="arrow_caret-right"></span></a>' .
                '</div>' : '';
            $prev_url = !empty($this->mobile_breadcrumb_links['prev_url']) ?
                '<a href="' .  URL::Site($this->mobile_breadcrumb_links['prev_url']['url'])
                . '" class="mobile-breadcrumbs-prev"><span class="arrow_caret-left"></span></a>' : '';
            $title = !empty($this->mobile_breadcrumb_links['curr_url']) ?
                '<h2 class="mobile-breadcrumbs-title"> '. __($this->mobile_breadcrumb_links['curr_url']['name']).'</h2>' : '' ;
            $mobile_breadcrumbs =
                '<div class="col-xs-2 text-left">' .$prev_url . '</div>' .
                '<div class="col-xs-8 text-center">' . $title . '</div>' .
                $next_url;
        }
        //die('<pre>' . print_r($mobile_breadcrumbs, 1) . '</pre>');
        return $mobile_breadcrumbs;
    }



    public static function get_submenu($controller_name)
    {
        $submenu = array('link'  => '', 'items' => array());

        if (class_exists('Controller_Admin_'.$controller_name)) {
            $class = new ReflectionClass('Controller_Admin_'.$controller_name);

            if ($class->hasMethod('action_ajax_get_submenu')) {
                $response   = new Response();
                $controller = $class->newInstanceArgs(array(Request::$current, $response));
                $submenu    = $controller->action_ajax_get_submenu(true);

                if (isset($submenu['items'])) {
                    foreach ($submenu['items'] as &$item) {
                        if (empty($item['link'])) {
                            $item['link'] = preg_replace('#/+#','/', '/admin/'.$controller_name.'/'.$submenu['link'].'/'.$item['id']);
                        }
                    }
                }
            }
        }

        return $submenu;
    }

}

