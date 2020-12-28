<?php
ini_set('memory_limit','2048M');

/**
 * Get the project dependencies. If one of the is unsatisfied, then abort the script.
 */
$dependencies      = array();
$dependencies_path = array();

define('DEPENDENCYPATHS', serialize($dependencies_path));

/**
 * Get the absolute path to the engine. If we cannot find the path to the engine, then abort the script.
 */
if (!defined('ENGINEPATH')) {
    define('ENGINEPATH', realpath(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR);
}

if ( ! is_dir(ENGINEPATH))
    die();

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 *
 * @link http://kohanaframework.org/guide/about.install#application
 */
$application = 'application';

/**
 * The directory in which your modules are located.
 *
 * @link http://kohanaframework.org/guide/about.install#modules
 */
$modules = 'modules';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @link http://kohanaframework.org/guide/about.install#system
 */
$system = 'system/3-2';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @link http://kohanaframework.org/guide/about.install#ext
 */
if (!defined('EXT')) {
    define('EXT', '.php');
}

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @link http://www.php.net/manual/errorfunc.configuration#ini.error-reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 *
 * When using a legacy application with PHP >= 5.3, it is recommended to disable
 * deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
 */
//error_reporting(E_ALL | E_STRICT);
error_reporting(E_ALL & ~E_NOTICE);
/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 */

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('APPPATH', realpath(ENGINEPATH.$application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath(ENGINEPATH.$modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath(ENGINEPATH.$system).DIRECTORY_SEPARATOR);

// Clean up the configuration vars
unset($application, $modules, $system);

if (file_exists('install'.EXT))
{
    // Load the installation check
    return include 'install'.EXT;
}

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_TIME'))
{
    define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('KOHANA_START_MEMORY'))
{
    define('KOHANA_START_MEMORY', memory_get_usage());
}

// Bootstrap the application
require APPPATH.'bootstrap'.EXT;

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
echo Request::factory()
    ->execute()
    ->send_headers(TRUE)
    ->body();
