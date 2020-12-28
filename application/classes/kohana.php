<?php defined('SYSPATH') or die('No Direct Script Access.');


final class Kohana extends Kohana_Core
{
    public static function auto_load($class)
    {
        try
        {
            // Transform the class name into a path
            $file = str_replace('_', '/', strtolower($class));

            if ($path = Kohana::find_file('classes', $file))
            {

                /*this caused some unexpected issues and needs more debugging
                 * if (preg_match('#/plugins/(.+?)/development/classes/controller/(.+)#i', $path, $plugin_controller) && Request::$current->action() != 'login') {
                    $auth = Auth::instance();
                    $user = $auth->get_user();
                    if (!Model_Plugin::get_isplugin_enabled_foruser($user['role_id'], $plugin_controller[1])) {
                        IbHelpers::set_message(sprintf(__("You have no permission for '%s' plugin"), $plugin_controller[1]), 'error');
                        if (stripos($plugin_controller[2], 'admin/') !== false) {
                            Request::$current->redirect('/admin');
                        } else {
                            Request::$current->redirect('/');
                        }
                        exit;
                    }
                }*/
                // Load the class file
                require $path;

                // Class has been found
                return TRUE;
            }

            // Class is not in the filesystem
            return FALSE;
        }
        catch (Throwable $e)
        {
            Kohana_Exception::handler($e);
            die;
        }
        catch (Exception $e)
        {
            Kohana_Exception::handler($e);
            die;
        }
    }

    public static function initialize_project()
    {
        $request    = Request::factory();

        $directory  = $request->directory ();
        $controller = $request->controller();

        Kohana::$config->load('config');

        // Add the project path to Kohana CFS
        array_unshift(Kohana::$_paths, PROJECTPATH);

        /*
         * 1. DATA ABSTRACTION LAYER MANAGER
         */

        Model_DALM::update_db();

		// Overwrite config variables with settings, if use_config_file is switched off
		if (Settings::instance()->get('use_config_file') === '0')
		{
			$settings = Settings::get_config_overwrite_settings();
			$config = Kohana::$config->load('config');
			foreach ($settings as $variable => $value)
			{
				$config->set($variable, $value);
			}
		}

		if (isset(Kohana::$config->load('config')->template_folder_path))
		{
			array_unshift(Kohana::$_paths, PROJECTPATH.'views/templates/'.Kohana::$config->load('config')->template_folder_path.'/');
		}

        /*
         * 2. PLUGINS LOADING
         */

        // Get the plugins to be loaded
        $plugins = Model_Plugin::get($directory == 'admin' AND Auth::instance()->get_user());
        if ($plugins == null) {
            $plugins = array();
        }
        // scan all engine plugin directories for dependencies
        {
            /*
             * Add the plugins to the search directory
             */

            // Start a new list of include paths
            $paths = array();
            $required_engine_plugins = self::get_required_engine_plugins();
            $project_plugins = array();
            if (file_exists(PROJECTPATH . 'plugins')) {
                $project_plugins = array_values(array_diff(scandir(PROJECTPATH . 'plugins'), array('.', '..')));
            }

            // Search path (1. Project Plugins, 2. Another Project Plugins, 3. Engine Plugins)
            $search_path = array_merge(array(PROJECTPATH), unserialize(DEPENDENCYPATHS), array(ENGINEPATH));

            // AdaptiveSearch: if the controller that made the request is a plugin, move it to the first position of the array
            if (($i = array_search($controller, $plugins)) !== FALSE)
            {
                foreach ($search_path as $path)
                {
                    $plugin_path = $path.'plugins'.DIRECTORY_SEPARATOR.$controller.DIRECTORY_SEPARATOR.'development'.DIRECTORY_SEPARATOR; /* ToDo Remove 'development' */

                    if (is_dir($plugin_path))
                    {
                        $paths[] = $plugin_path;

                        break;
                    }
                }

                unset($plugins[$i]);
            }

            // Search the remaining plugins
            foreach ($search_path as $path)
            {
                foreach ($plugins as $plugin)
                {
                    if ($path == ENGINEPATH && !in_array($plugin, $required_engine_plugins)) {
                        continue;
                    }
                    $plugin_path = $path.'plugins'.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'development'.DIRECTORY_SEPARATOR; /* ToDo Remove 'development' */

                    if (is_dir($plugin_path))
                    {
                        $paths[] = $plugin_path;
                    }
                }
            }

            foreach ($required_engine_plugins as $eplugin) {
                if ($eplugin != '.' && $eplugin != '..') {
                    $eplugin_path = ENGINEPATH . '/plugins/' . $eplugin . '/development/';
                    if (!in_array($eplugin_path, $paths)) {
                        $paths[] = $eplugin_path;
                    }
                }
            }

            foreach ($project_plugins as $pplugin) {
                if ($pplugin != '.' && $pplugin != '..') {
                    $pplugin_path = PROJECTPATH . '/plugins/' . $pplugin . '/development/';
                    if (!in_array($pplugin_path, $paths)) {
                        $paths[] = $pplugin_path;
                    }
                }
            }

            // Merge: 1. App, 2. Engine, 3. Kohana, 4. Active Plugin, 5. Project Plugins, 6. Another Project Plugins, 7. Engine Plugins
            Kohana::$_paths = array_merge(Kohana::$_paths, $paths);

            // Initialize the plugins
            foreach ($paths as $path) {
                $init = $path.'init'.EXT;

                if (is_file($init)) {
                    require_once $init;
                }
            }

            Model_Automations::load();

            $apis = array();
            foreach(Kohana::$_paths as $path) {
                if (file_exists($path . '/classes/controller/api')) {
                    $apis[] = realpath($path . '/classes/controller/api');
                }
            }
            $apis = array_unique($apis);
            //print_r($apis);
        }

        /*
         * 3. PROJECT INITIALIZATION
         */

        $init = PROJECTPATH.'config'.DIRECTORY_SEPARATOR.'init'.EXT;

        if (is_file($init))
        {
            require_once $init;
        }

        /*
         * 4. SET THE DEFAULT ROUTE
         */
        Route::set('page_category_item', '<page>(.<ext>)(/<item_category>(.<cat_ext>)(/<item_identifier>))',
            array(
                'item_identifier' => '.*'
            ))
            ->defaults(array(
                'controller'      => 'page',
                'action'          => 'index'
            ));

        Route::set('page', '<page>',
            array(
                'page'       => '.*'
            ))
            ->defaults(array(
                'controller' => 'page',
                'action'     => 'index'
            ));

        // Enable menu icons in the Dashboard
        DashBoard::factory()->register_menu_icons();

        // Be transparent!
        Request::$initial = NULL;

		// If a language cookie has been set
		if ($directory != 'admin' AND Cookie::get('lang'))
		{
			I18n::lang(Cookie::get('lang'));
		}

        $currency_locale = Settings::Instance()->get('currency_format');
        if ($currency_locale)
        {
            setlocale(LC_MONETARY, $currency_locale);
        }
    }

    public static function get_required_engine_plugins()
    {
        $required_plugins = array();
        $engine_plugins = Kohana::$config->load('config')->get('engine_plugins');

        if ($engine_plugins != NULL AND is_dir(ENGINEPATH . 'plugins')) {
            $required_plugins = $engine_plugins === '*' ? array_values(array_diff(scandir(ENGINEPATH . 'plugins'), array('.', '..'))) : explode(',', $engine_plugins);
        }

        return $required_plugins;
    }
}
