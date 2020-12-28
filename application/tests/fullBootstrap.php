<?php

/**
* This bootstrap runs the application with all dependencies and modules via CLI
* To execute  particular test you can use the following command :
*
*        phpunit --bootstrap=fullBootstrap.php  test_classes/%TestClassName%.php
*
*  where %TestClassName% should be replaced by the by the test's name
*/

session_save_path('');

// subfolder under wms-engine/system/
$sysPathSuffix = "3-2/";

// check cache folder (required by framework)
$cacheFolder = __DIR__  . "/../cache/";
if (!is_dir($cacheFolder)) {
    mkdir($cacheFolder, 0777);
} 

if (getenv('PROJECTPATH')) {
    if (!defined('PROJECTPATH'))
        define('PROJECTPATH', getenv('PROJECTPATH'));
}
// assign project path  (required by framework)
if (!defined('PROJECTPATH'))
	define('PROJECTPATH' , __DIR__ . '/../../');

// assign dependency path  (required by framework)
if (!defined('DEPENDENCYPATHS'))
	define('DEPENDENCYPATHS', serialize(array(PROJECTPATH . 'application/vendor/')));

// assign enginepath path  (required by framework)
if (!defined('ENGINEPATH')) {
	define('ENGINEPATH', realpath('../../') . '/');
}

// assign HTTP_HOST (required by framework)
if (getenv('HTTP_HOST')) {
    $_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
} else {
    $_SERVER['HTTP_HOST'] = 'websitecms.dev';
}

if (getenv('REQUEST_URI')) {
    $_SERVER['REQUEST_URI'] = getenv('REQUEST_URI');
} else {
    $_SERVER['REQUEST_URI'] = '/';
}

// start application with all modules
require_once(__DIR__ . "/../../modules/unittest/bootstrap_all_modules.php");
