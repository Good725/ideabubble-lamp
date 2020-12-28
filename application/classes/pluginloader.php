<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Load plugins
 *
 * @category   Settings
 * @author     Diarmuid
 */
class PluginLoader
{
    private static $plugins; //loaded plugins

    public static function load($additional_plugins = NULL)
    {
        self::$plugins = self::get_plugins();

        if (is_array($additional_plugins) && self::$plugins) {
            $plugins = array_merge(self::$plugins, $additional_plugins);
        }

        if (is_array(self::$plugins)) {
            Kohana::plugins(self::$plugins);
        }
    }

    public static function load_frontend($additional_plugins = NULL)
    {
        self::$plugins = self::get_frontend_plugins();

        if (is_array($additional_plugins) && self::$plugins) {
            $plugins = array_merge(self::$plugins, $additional_plugins);
        }

        if (is_array(self::$plugins)) {
            Kohana::plugins(self::$plugins);
        }
    }

    public static function  is_loaded($plugin_name)
    {
        return isset(self::$plugins[$plugin_name]);
    }

    public static function get_plugins()
    {
        $result = DB::select()
            ->from('engine_plugins')
            ->where('enabled', '=', '1')
//                ->or_where( 'id','=',6)
//                ->or_where( 'id','=',5)
            ->and_where('is_backend', '=', '1')
            ->execute();

        if ($result->count() > 0) {

            $plugins = array();

            foreach ($result as $plugin) {
                $plugins[$plugin['name']] = $plugin['version'];
                // dont show menu link if menu name is not setup
                if ($plugin['menu']) {
                    MenuArea::factory()->register_link($plugin['name'], $plugin['menu'], NULL, $plugin['icon'], $plugin['flaticon'], (isset($plugin['svg']) ? $plugin['svg'] : ''));
                }
            }
        } else {
            return FALSE;
        }

        return $plugins;
    }

    public static function get_frontend_plugins()
    {
        $result = DB::select()
            ->from('engine_plugins')
            ->where('enabled', '=', '1')
            ->and_where('is_frontend', '=', '1')
            ->execute();

        if ($result->count() > 0) {

            $plugins = array();

            foreach ($result as $plugin) {
                $plugins[$plugin['name']] = $plugin['version'];
                // dont show menu link if menu name is not setup
                if ($plugin['menu']) {
                    MenuArea::factory()->register_link($plugin['name'], $plugin['menu'], NULL, $plugin['icon'], $plugin['flaticon'], (isset($plugin['svg']) ? $plugin['svg'] : ''));
                }
            }
        } else {
            return FALSE;
        }

        return $plugins;
    }

    /**
     * Plugins could have options that turn on or of part of functionality. Options are stored in config files and could be overwritten by concrete project.
     * Options are in form:
     *     'Plugin_options' => array( 'products' => array('linked_to_files' => false) ),
     *
     * @static
     * @param $plugin
     * @param $option
     * @return null
     */
    public static function get_plugin_option($plugin, $option)
    {
        $options = Kohana::$config->load('config')->get('Plugin_options');
        return isset($options[$plugin][$option]) ? $options[$plugin][$option] : null;
    }
}
