<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment  setup --------------------------------------------------------
if (isset($_REQUEST['error_reporting'])) {
    error_reporting((int)$_REQUEST['error_reporting']);
}
// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
    // Application extends the core
    require APPPATH.'classes/kohana'.EXT;
}
else
{
    // Load empty core extension
    require SYSPATH.'classes/kohana'.EXT;
}

/**
 * Set  the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('Europe/Dublin');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_IE.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
    Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
    'base_url'   => isset($_SERVER["HTTP_HOST"]) ? ( (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER["HTTP_HOST"].substr($_SERVER["PHP_SELF"], 0, strpos($_SERVER["PHP_SELF"], "index.php")) ) : '',
    'index_file' => '',
    'cache_life' => 86400, // 1 day (in seconds) = 60 seconds * 60 minutes * 24 hours
    'caching' => Kohana::$environment != Kohana::DEVELOPMENT // false only for development mode
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
$logdir = APPPATH . '/logs/' . preg_replace('/[^a-z0-9\-\_\.]+/', '-', strtolower(@$_SERVER['HTTP_HOST'])) . '/';
Kohana::$log->attach(new Log_File($logdir));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    'auth'                => MODPATH.'auth',                // Basic authentication
    'cache'               => MODPATH.'cache',               // Caching with multiple backends
    'database'            => MODPATH.'database',            // Database access
    'kohana-i18n-manager' => MODPATH.'kohana-i18n-manager', // i18n manager
    'unittest'            => MODPATH.'unittest',            // Unit Testing
    'orm'                 => MODPATH.'orm',                 // ORM
));

/**
 * Set the Cookie Salt to a random string.
 */
Cookie::$salt = '7ecd2ffca332b32fbf616db42bfd67299b01dec6d9451af4f371146ccf180cd4';
Cookie::$domain = str_replace('www.', '', parse_url(URL::base(), PHP_URL_HOST));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

// Routes to be defined only in the CLI environment
if (Kohana::$is_cli)
{
    // CRON
    Route::set('cron', 'cron(/<action>(/<id>))')
        ->defaults(array(
            'controller' => 'cron',
            'action'     => 'index'
        ));

    // Set the environment
    $options = CLI::options('kohana_env');

    if (isset($options['kohana_env']))
    {
        $environment = $options['kohana_env'];

        Kohana::$environment = (ctype_digit($environment)) ? $environment : constant('Kohana::' . strtoupper($environment));
    }
}

Route::set('robots', 'robots.txt')->defaults(array('controller' => 'robots','action'     => 'robots',));


// CMS
Route::set('admin', '<directory>(/<controller>(/<action>(/<id>(/<toggle>))))',
    array(
        'directory'  => 'admin'
    ))
    ->defaults(array(
        'controller' => 'dashboard',
        'action'     => 'index'
    ));

// Static assets
Route::set('assets', 'assets/<theme>/<filepath>.<ext>', [
        'filepath' => '[a-zA-Z0-9\-\_\/]+', // alphanumeric, hyphens, underscores and forward slashes
    ])
    ->defaults(array(
        'directory'  => 'frontend',
        'controller' => 'assets',
        'action'     => 'static'
    ));

Route::set('assets', 'assets/<theme>/<filepath>', [
        'filepath' => '[a-zA-Z0-9\-\_\.\/]+', // alphanumeric, hyphens, underscores, dots and forward slashes
    ])
    ->defaults(array(
        'directory'  => 'frontend',
        'controller' => 'assets',
        'action'     => 'static'
    ));

// Plugins Frontend Controller
Route::set('frontend', '<directory>(/<controller>(/<action>(/<id>)))',
    array(
        'directory' => 'frontend'
    ))
    ->defaults(array(
        'action'    => 'index'
    ));

// Plugins Frontend Controller
Route::set('api', '<directory>(/<controller>(/<action>))',
    array(
        'directory' => 'api'
    ))
    ->defaults(array(
        'action'    => 'index'
    ));

/**
 * codeception routing to ensure it avoid the normal routing when scripts are running via command line
// */
if (Kohana::$environment == Kohana::TESTING)
{
    Kohana::$is_cli = false;
}

Kohana::initialize_project();
//end bootstrap.php.
