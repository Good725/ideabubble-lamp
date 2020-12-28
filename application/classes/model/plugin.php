<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Plugin extends Model
{
    // Tables
    const MAIN_TABLE             = 'engine_plugins';
    const ROLES_TABLE            = 'engine_project_role';

    /**
     * @var
     */
    private static $loaded_plugins = NULL;

    //
    // PUBLIC
    //

    /**
     * @param bool $cms
     * @return array
     */
    public static function get($cms = FALSE)
    {
        $plugins = ($cms) ? self::get_authorized_cms() : self::get_all();

        if ($plugins !== NULL)
        {
            foreach ($plugins as $plugin)
            {
                self::$loaded_plugins[] = $plugin['name'];

                if ($cms AND $plugin['show_on_dashboard'] == 1)
                {
                    MenuArea::factory()->register_link($plugin['name'], $plugin['friendly_name'], null, $plugin['icon'], $plugin['flaticon'], (isset($plugin['svg']) ? $plugin['svg'] : ''));
                }
            }
        }
        if (Auth::instance()->get_user()) {
            MenuArea::factory()->register_link('/profile/edit?section=contact', 'Profile', 'profile', null, 'avatar', 'profile');
            $menu_plugins = self::get_authorized_cms();
            foreach ($menu_plugins as $plugin) {
                if ($plugin['show_on_dashboard'] == 1) {
                    MenuArea::factory()->register_link($plugin['name'], $plugin['friendly_name'], NULL, $plugin['icon'], $plugin['flaticon'], (isset($plugin['svg']) ? $plugin['svg'] : ''));
                }
            }
        }

        return self::$loaded_plugins;
    }

    /**
     * @return array
     */
    public static function get_all($alpha = TRUE)
    {
        try {
            if ($alpha) {
                $r = DB::select('id', 'name', 'friendly_name', 'show_on_dashboard', 'requires_media', 'media_folder',
                    'note_title', 'note_body')
                    ->from(self::MAIN_TABLE)
                    ->order_by('friendly_name')
                    ->order_by('order')
                    ->execute()
                    ->as_array();
            } else {
                $order = DB::select('id', 'name', 'friendly_name', 'show_on_dashboard', 'requires_media',
                    'media_folder', 'note_title', 'note_body')
                    ->from(self::MAIN_TABLE)
                    ->where('order', '!=', 'NULL')
                    ->order_by('order')
                    ->execute()
                    ->as_array();
                $nulls = DB::select('id', 'name', 'friendly_name', 'show_on_dashboard', 'requires_media',
                    'media_folder', 'note_title', 'note_body')
                    ->from(self::MAIN_TABLE)
                    ->where('order', '=', 'NULL')
                    ->order_by('friendly_name')
                    ->execute()
                    ->as_array();
                $r = array_merge($order, $nulls);
            }
        } catch (Exception $exc) {
            //if a dalm error exists and the model related to above queries are not executed yet then
            //this function causes a deadlock
            //this segment is to prevent this deadlock until dalm errors will be cleared
            if ($alpha) {
                $r = DB::select('id', 'name', 'friendly_name', 'show_on_dashboard', 'requires_media', 'media_folder',
                    DB::expr("'' AS note_title"), DB::expr("'' AS note_body"))
                    ->from(self::MAIN_TABLE)
                    ->order_by('friendly_name')
                    ->order_by('order')
                    ->execute()
                    ->as_array();
            } else {
                $order = DB::select('id', 'name', 'friendly_name', 'show_on_dashboard', 'requires_media',
                    'media_folder', DB::expr("'' AS note_title"), DB::expr("'' AS note_body"))
                    ->from(self::MAIN_TABLE)
                    ->where('order', '!=', 'NULL')
                    ->order_by('order')
                    ->execute()
                    ->as_array();
                $nulls = DB::select('id', 'name', 'friendly_name', 'show_on_dashboard', 'requires_media',
                    'media_folder', DB::expr("'' AS note_title"), DB::expr("'' AS note_body"))
                    ->from(self::MAIN_TABLE)
                    ->where('order', '=', 'NULL')
                    ->order_by('friendly_name')
                    ->execute()
                    ->as_array();
                $r = array_merge($order, $nulls);
            }
        }
        return $r;
    }

    /**
     * @return array
     */
    public static function get_dashboard_plugins_icons()
    {
        $plugins = self::get_authorized_cms(Settings::instance()->get('featured_item_order')==1);

		$menu_links = MenuArea::factory()->get_links();

        if ($plugins !== NULL)
        {
            $plugin_with_icons = array();
            foreach ($plugins as $plugin)
            {
                $flaticon = ( ! empty($plugin['flaticon'])) ? $plugin['flaticon'] : '';
                if ($plugin['show_on_dashboard'] == 1 AND (trim(($plugin['icon']) != "" OR trim(($flaticon) != ""))))
                {
					// If a plugin name has been overwritten in the menu, the overwrite will be applied here too.
					if (isset($menu_links[$plugin['name']]))
					{
						$plugin['friendly_name'] = $menu_links[$plugin['name']]['name'];
					}

                    $plugin_with_icons[] = $plugin;
                }
            }
        }
        return $plugin_with_icons;
    }

    public static function get_off_dashboard_plugins_icons()
    {
        $plugins = self::get_hidden_dashboard_icons();

        if ($plugins !== NULL)
        {
            $plugin_with_icons = array();
            foreach ($plugins as $plugin)
            {

                if ($plugin['show_on_dashboard'] == 1)
                {
                    $plugin_with_icons[] = $plugin;
                }
            }
        }
        return $plugin_with_icons;
    }

    public static function get_plugin_by_id($plugin_id)
    {
        $q = DB::select('id','name','friendly_name')->from('engine_plugins')->where('id','=',$plugin_id)->execute()->as_array();
        return count($q) > 0 ? $q[0]['name'] : '';
    }

    /**
     * @return array
     */
    public static function get_plugin_by_name($name)
    {
        $r = DB::select('id', 'name', 'friendly_name', 'show_on_dashboard', 'requires_media', 'media_folder', 'icon','note_title','note_body')
            ->from(self::MAIN_TABLE)
            ->where('show_on_dashboard', '=', '1')
            ->and_where('name', '=', $name)
            ->order_by('order')
            ->execute()
            ->as_array();

        return (isset($r[0]) ? $r[0] : null);
    }

    /**
     * @return array
     */
    public static function get_all_roles()
    {
        $r = DB::select('id', 'role', 'description', 'publish', 'deleted')
                 ->from(self::ROLES_TABLE)
                 ->where('deleted', '=', 0)
                 ->order_by('role')
                 ->execute()
                 ->as_array();

        return $r;
    }

    /**
     * @param $plugin_name
     * @return bool
     */
    public static function is_loaded($plugin_name)
    {
        return self::$loaded_plugins === NULL ? FALSE : (array_search($plugin_name, self::$loaded_plugins) !== FALSE);
    }

    /**
     * @param $plugin
     * @param $option
     * @return null
     */
    public static function get_plugin_option($plugin, $option)
    {
        $options = Kohana::$config->load('config')->get('Plugin_options');

        return isset($options[$plugin][$option]) ? $options[$plugin][$option] : NULL;
    }

    /**
     * @param $id
     * @param $plugin
     * @return mixed
     */
    public static function get_isplugin_enabled_foruser($user_id, $plugin)
    {
		if (is_null($user_id) OR $id = 'current')
		{
			$user = Auth::instance()->get_user();
			$role_id = $user['role_id'];
		}

        $has_permission = DB::select('has_permissions.*')
            ->from(array(self::MAIN_TABLE, 'plugins'))
                ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                    ->on('plugins.name', '=', 'resources.alias')
                ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permissions'), 'inner')
                    ->on('resources.id', '=', 'has_permissions.resource_id')
            ->where('plugins.name', '=', $plugin)
            ->and_where('has_permissions.role_id', '=', $role_id)
            ->execute()
            ->current();

        return $has_permission != null;
    }

    //
    // PRIVATE
    //

    /**
     * @return array
     */
    private static function get_authorized_cms($alpha = TRUE)
    {
        $logged_user = Auth::instance()->get_user();

        // get features available ordered by order column
        if ($alpha) {
            $plugins = DB::select('plugins.*', DB::expr("1 as enabled"))
                ->from(array(self::MAIN_TABLE, 'plugins'))
                    ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                        ->on('plugins.name', '=', 'resources.alias')
                    ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permissions'), 'inner')
                        ->on('resources.id', '=', 'has_permissions.resource_id')
                ->where('has_permissions.role_id', '=', $logged_user['role_id'])
                ->order_by('plugins.friendly_name')
                ->execute()
                ->as_array();
        }
        else {
            $plugins = DB::select('plugins.*', DB::expr("1 as enabled"))
                ->from(array(self::MAIN_TABLE, 'plugins'))
                ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                ->on('plugins.name', '=', 'resources.alias')
                ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permissions'), 'inner')
                ->on('resources.id', '=', 'has_permissions.resource_id')
                ->where('has_permissions.role_id', '=', $logged_user['role_id'])
                ->order_by(DB::expr("IF(plugins.order is null, 1, 0)"))
                ->order_by('plugins.order')
                ->order_by('plugins.friendly_name')
                ->execute()
                ->as_array();
        }
        return $plugins;
    }

    private static function get_hidden_dashboard_icons($alpha = TRUE)
    {
        $logged_user = Auth::instance()->get_user();

        // get features available ordered by order column
        if ($alpha) {
            $plugins = DB::select('plugins.*', DB::expr("1 as enabled"))
                ->from(array(self::MAIN_TABLE, 'plugins'))
                    ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                        ->on('plugins.name', '=', 'resources.alias')
                    ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permissions'), 'inner')
                        ->on('resources.id', '=', 'has_permissions.resource_id')
                        ->on('has_permissions.role_id', '=', DB::expr($logged_user['role_id']))
                ->where('has_permissions.role_id', 'is', null)
                ->order_by('plugins.friendly_name')
                ->execute()
                ->as_array();
        }
        else {
            $plugins = DB::select('plugins.*', DB::expr("1 as enabled"))
                ->from(array(self::MAIN_TABLE, 'plugins'))
                    ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                        ->on('plugins.name', '=', 'resources.alias')
                    ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permissions'), 'inner')
                        ->on('resources.id', '=', 'has_permissions.resource_id')
                        ->on('has_permissions.role_id', '=', DB::expr($logged_user['role_id']))
                ->where('has_permissions.role_id', 'is', null)
                ->order_by(DB::expr("IF(plugins.order is null, 1, 0)"))
                ->order_by('plugins.order')
                ->order_by('plugins.friendly_name')
                ->execute()
                ->as_array();
        }
        return $plugins;
    }


    public static function is_enabled_for_role($role, $plugin, $cached = true)
    {
        static $roles_plugins = null;

        if ($cached) {
            if ($roles_plugins == null) {
                $roles_plugins = DB::select(DB::expr('LOWER(plugins.name) as name'), DB::expr('LOWER(roles.role) as role'))
                    ->from(array(self::MAIN_TABLE, 'plugins'))
                        ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                            ->on('plugins.name', '=', 'resources.alias')
                        ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permissions'), 'inner')
                            ->on('resources.id', '=', 'has_permissions.resource_id')
                        ->join(array(Model_Roles::MAIN_TABLE, 'roles'), 'inner')
                            ->on('has_permissions.role_id', '=', 'roles.id')
                    ->execute()
                    ->as_array();
                /*foreach ($roles_plugins as $i => $roles_plugin) {
                    $roles_plugins[$i]['name'] = strtolower($roles_plugin['name']);
                    $roles_plugins[$i]['role'] = strtolower($roles_plugin['role']);
                }*/
            }
            $role = strtolower($role);
            $plugin = strtolower($plugin);
            foreach ($roles_plugins as $roles_plugin) {
                if ($roles_plugin['name'] == $plugin && $roles_plugin['role'] == $role) {
                    return true;
                }
            }
        } else {

            // check if user has this feature enabled
            $exists = DB::select(DB::expr('count(*) as `exists`'))
                ->from(array(self::MAIN_TABLE, 'plugins'))
                    ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')
                        ->on('plugins.name', '=', 'resources.alias')
                    ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permissions'), 'inner')
                        ->on('resources.id', '=', 'has_permissions.resource_id')
                    ->join(array(Model_Roles::MAIN_TABLE, 'roles'), 'inner')
                        ->on('has_permissions.role_id', '=', 'roles.id')
                ->where('roles.role', '=', $role)
                ->and_where('plugins.name', '=', $plugin)
                ->execute()
                ->get('exists');
            return $exists > 0 ? true : false;
        }
    }


    public static function global_search($params)
    {
        $results = array(
            'total_count' => 0,
            'all_data'    => array(),
            'data'        => array()
        );

        $params['offset'] = isset($params['offset']) ? $params['offset'] : 0;
        $params['limit']  = isset($params['limit'])  ? $params['limit']  : Settings::instance()->get('courses_results_per_page');
        $params['limit']  = ($params['limit'] == 0) ? 12 : (int) $params['limit'];
        $params['unique_courses']  = true;
        $params['cancelled_schedules'] = false;
        if (Model_Plugin::is_enabled_for_role('Administrator', 'events')) {
            $search = Model_Event::get_for_global_search($params);
            $results['total_count'] += $search['total_count'];
            $results['all_data']     = array_merge($results['all_data'], $search['all_data']);
            $results['data']         = array_merge($results['data'],     $search['data']);
        }

        if (Model_Plugin::is_enabled_for_role('Administrator', 'courses')) {
            $search = Model_Courses::filter($params);
            $results['total_count'] += $search['total_count'];
            $results['all_data']     = array_merge($results['all_data'], $search['all_data']);
            $results['data']         = array_merge($results['data'],     $search['data']);
        }

        $total  = count($results['all_data']);
        $limit  = $params['limit'];
        $offset = $params['offset'];

        if ($total == 0) {
            $results['results_found'] = __('No results found');
        }
        else {
            $results['results_found'] = __('Showing results $1 to $2 of $3', array(
                '$1' => 1 + $offset,
                '$2' => ($limit + $offset > $total) ? $total : $limit + $offset,
                '$3' => $total
            ));
        }

        return $results;
    }
}
