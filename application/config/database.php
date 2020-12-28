<?php defined('SYSPATH') or die('No Direct Script Access.');

$db_id = Kohana::$config->load('config')->db_id;
$sprint = Kohana::$config->load('config')->sprint;
$db_needs_ib_fullindex = Kohana::$config->load('config')->get('db_needs_ib_fullindex');
$http_host = explode('.', str_ireplace('www.', '', $_SERVER['HTTP_HOST']));

switch (Kohana::$environment)
{
    case Kohana::PRODUCTION:
        if ($http_host[1] == 'stage') {
            $db = 'wms_stage_' .  $db_id;
        } else {
            $db = 'wms_production_' . $db_id;
        }

        $hostname = Kohana::$config->load('config')->db_prod_hostname;
        $username = Kohana::$config->load('config')->db_prod_username;
        $password = Kohana::$config->load('config')->db_prod_password;
        $profiling = false;
        break;


    case Kohana::STAGING:
        $profiling = false;

        if ($http_host[1] == 'stage' || stripos($_SERVER['HTTP_HOST'], '.ideabubble.net') !== false) {
            $db = 'wms_stage_' .  $db_id;
        }
        else if ($http_host[2] == 'uat') {
            // Versioned databases
            $db = 'wms_staging_'.$db_id.'_'.$http_host[1];
        }
        else {
            $db = 'wms_staging_' . ($sprint ? 'sprint' . $sprint . '_' : '') . $db_id;
        }

        if (stripos($_SERVER['HTTP_HOST'], '.ideabubble.net') !== false)
        {
            $hostname = Kohana::$config->load('config')->db_prod_hostname;
            $username = Kohana::$config->load('config')->db_prod_username;
            $password = Kohana::$config->load('config')->db_prod_password;
        }
        else //UAT
        {
            $hostname = getenv('dbhostname');
            $username = getenv('dbusername');
            $password = getenv('dbpassword');
        }
        break;

    case Kohana::TESTING:
        if ($http_host[1] == 'testnext') {
            $db = 'wms_testnext_' . ($sprint ? 'sprint' . $sprint . '_' : '') . $db_id;
        }
        else if ($http_host[2] == 'testnext') {
            // Versioned databases
            $db = 'wms_testnext_'.$db_id.'_'.$http_host[1];
        }
        else {
            $db = 'wms_testing_' . ($sprint ? 'sprint' . $sprint . '_' : '') . $db_id;
        }

        $hostname = getenv('dbhostname');
        $username = getenv('dbusername');
        $password = getenv('dbpassword');
        $profiling = false;
        break;


    default:
        if ($http_host[2] == 'dev') {
            // Versioned databases
            $db = 'wms_development_'.$db_id.'_'.$http_host[1];

        } else {
            $db = 'wms_development_' . ($sprint ? 'sprint' . $sprint . '_' : '') . $db_id;
        }

		$hostname = getenv('dbhostname');
		$username = getenv('dbusername');
		$password = getenv('dbpassword');
        $profiling = false;
        break;
}
//kohana $profiling is useless
$profiling = false;
return array
(
    'default' => array
    (
        'type'         => 'mysqli',
        'connection'   => array
        (
            'hostname'   => $hostname,
            'database'   => $db,
            'username'   => $username,
            'password'   => $password,
            'persistent' => false,
        ),
        'table_prefix' => '',
        'charset'      => 'utf8',
        'caching'      => true,
        'profiling'    => $profiling,
    ),
);
