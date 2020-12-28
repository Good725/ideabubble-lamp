<?php defined('SYSPATH') or die('No direct access allowed.');

// check URL and switch based on the app mode environment to decide TEMPLATE/PROJECT NAME
$current_url_parts = parse_url(URL::base());
$current_url_host = explode('.', $current_url_parts['host']); //var to manage the splitting or URL parts
$cur_host = $current_url_host[0];

//ignore www part for project stub look ups if present (ie production mode)
if ($cur_host === 'www') {
    $cur_host = strtolower($current_url_host[1]);
}

$sprint = '';
$http_host = explode('.', strtolower($_SERVER['HTTP_HOST']));
if (count($http_host) > 3 && in_array($http_host[2], ['dev', 'test', 'uat']) && !is_numeric($http_host[1])) { // hosts like sprint$.uticket.test.ibplatform.ie
    $cur_host = $http_host[1];
} else if (count($http_host) > 3 && in_array($http_host[1], ['dev', 'test', 'uat'])) { // hosts like uticket.test.ibplatform.ie
    $cur_host = $http_host[0];
}

$project_id = $cur_host;
$config = [];
$shared_media_suffix = '';
$db_suffix = '';

if ($cur_host == 'newsite') {
    $cur_host = $current_url_host[1];
    $db_suffix = '_newsite';
    $shared_media_suffix = '_newsite';
} else {
    $db_suffix = '';
    $shared_media_suffix = '';
}

switch ($cur_host) {
    /*------------------------------------*\
      #Shop1 projects
    \*------------------------------------*/
    case 'teethingsos':
    case 'ambersos':
        $project_id = 'ambersos';
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '28',
            'template_folder_path' => 'b',
        ];
        break;

    case 'tippecanoe' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '01',
            'template_folder_path' => 'default'
        ];
        break;

    case 'quinlanireland' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '02',
            'template_folder_path' => '2col'
        ];
        break;

    case 'murphyenterprises' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '03',
            'template_folder_path' => 'home_wide'
        ];
        break;

    case 'garretts' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '04',
            'template_folder_path' => 'a'
        ];
        break;

    case 'tadghoflynnjewellers' :
    case 'tadghoflynn' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '05',
            'template_folder_path' => 'home_wide'
        ];
        break;

    case 'wellsense':
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '06',
            'template_folder_path' => 'home_wide'
        ];
        break;

    case 'pdsl' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '07',
            'template_folder_path' => 'home_wide'
        ];
        break;

    case 'grettalspetals' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '08',
            'template_folder_path' => 'home_wide'
        ];
        break;

    case 'screengrafix' :
    case 'screenprinter' :
    case 'designyoursign' :
        $project_id = 'screengrafix';
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '09',
            'template_folder_path' => 'a'
        ];
        break;

    case 'mr-tee' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '10',
            'template_folder_path' => 'home_wide'
        ];
        break;

    case 'lionprint' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path' => '11',
            'template_folder_path' => 'a'
        ];
        break;

    case 'navanballet' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path' => '12',
            'template_folder_path' => 'a'
        ];
        break;

    case 'henchyinsurances' :
    case 'henchyinsurance' :
        $project_id = 'henchyinsurances';
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '13',
            'template_folder_path' => 'home_wide'
        ];
        break;

    case 'regeneron' :
        $project_folder = 'shop1';
        $config = [
            'print_notification'   => 'printing',
            'assets_folder_path'   => '14',
            'template_folder_path' => 'a'
        ];
        break;


    case 'horganpharmacygroup' :
    case 'horganpharmacy' :
        $project_id = 'horganpharmacy';
        $project_folder = 'shop1';
        $config = [
            'express_repeat_prescriptions' => 'express_repeat_prescriptions',
            'assets_folder_path' => '15',
            'template_folder_path' => 'home_wide'
        ];
        break;

    case 'snclavalin' :
        $project_folder = 'shop1';
        $config = [
            'assets_folder_path'   => '19',
            'template_folder_path' => 'a'
        ];
        break;

    case 'flynnsurgical' :
        $project_folder = 'shop1';
        $config = [
            'successful_payment_seller_bookings' => 'successful-payment-seller',
            'successful_payment_customer_bookings' => 'successful-payment-customer',
            'assets_folder_path'    => '17',
            'template_folder_path'  => 'systems',
            'db_needs_ib_fullindex' => TRUE
        ];
        break;

    case 'leabharbreac' :
        $project_folder = 'shop1';
        $config = [
            'successful_payment_seller_bookings' => 'successful-payment-seller',
            'successful_payment_customer_bookings' => 'successful-payment-customer',
            'assets_folder_path'   => '20',
            'template_folder_path' => 'books',
        ];
        break;

    case 'rapecrisis' :
        $project_folder = 'shop1';
        break;

    case 'rentacottage':
    case 'rentanirishcottage':
        $project_id = 'rentacottage';
        $project_folder = 'shop1';
        break;

    case 'nevsail' :
    case 'nevsailwatersports' :
        $project_id = 'nevsail';
        $project_folder = 'shop1';
        break;

    case 'shannonside' :
    case 'shannonsidegalv' :
        $project_id = 'shannonside';
        $project_folder = 'shop1';
        break;

    case 'uticket' :
        $project_folder = 'shop1';
        break;

    case 'lsomusic' :
    case 'limerickschoolofmusic' :
        $project_id = 'lsomusic';
        $project_folder = 'shop1';
        break;

    case 'dinasona' :
    case 'smsdonate' :
        $project_id = 'smsdonate';
        $project_folder = 'shop1';
        break;

    case 'iha':
    case 'ironoverload':
    case 'haemochromatosis-ir':
        $project_id = 'iha';
        $project_folder = 'shop1';
        $config = [
            'successful_payment_seller_bookings' => 'successful-payment-seller',
            'successful_payment_customer_bookings' => 'successful-payment-customer'
        ];
        break;

    case 'stactraining' :
    case 'stacfirstaidcourses':
    case 'stac':
        $project_id = 'stac';
        $project_folder = 'shop1';
    $config = [
        'successful_payment_seller_bookings' => 'successful-payment-seller',
        'successful_payment_customer_bookings' => 'successful-payment-customer'
    ];
        break;

    case 'pallaskenry':
    case 'salesianag':
        $project_id = 'pallaskenry';
        $project_folder = 'shop1';
        $config = [
            'booking' => 'successful-payment-customer',
        ];
        break;

    case 'saoirsetreatmentcenter':
    case 'saoirse':
        $project_id = 'saoirse';
        $project_folder = 'shop1';
        break;

    case 'slsireland':
        $project_folder = 'shop1';
        $config = [
            'successful_payment_seller_bookings' => 'successful-payment-seller',
            'successful_payment_customer_bookings' => 'successful-payment-customer',
            'booking' => 'successful-payment-customer'
        ];
        break;

    case 'voiceworksstudio':
        $project_folder = 'shop1';
        $config = [
            'booking' => 'successful-payment-customer'
        ];
        break;

    case 'ibec':
    case 'ibecacademy':
    case 'ibectraining':
        $project_id = 'ibec';
        $project_folder = 'shop1';
        break;

    case 'ibplatform':
    case 'courseco':
    case 'runforliam':
        $project_folder = 'shop1';
        break;




    /*------------------------------------*\
      #Content1 projects
    \*------------------------------------*/
    case 'ailesbury' :
    case 'ailesburyhairclinic':
        $project_id = 'ailesbury';
        $project_folder = 'content1';
        break;

    case 'colmduffy' :
        $project_id = 'colmduffy';
        $project_folder = 'content1';
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => '04',
        ];
        break;

    case 'atomicchicken' :
    case 'atomicchickenband':
        $project_id = 'atomicchicken';
        $project_folder = 'content1';
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => '07',
        ];
        break;

    case 'mcnamaracarpentry' :
        $project_id = 'mcnamaracarpentry';
        $project_folder = 'content1';
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => '08',
        ];
        break;

    case 'johnnyosullivan' :
    case 'johnnyosullivanpainters' :
        $project_id = 'johnnyosullivan';
        $project_folder = 'content1';
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => '09',
        ];
        break;

    case 'sarahokennedy' :
        $project_id = 'sarahokennedy';
        $project_folder = 'content1';
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => '10',
        ];
        break;

    case 'bniheritage' :
        $project_id = 'heritage';
        $project_folder = 'content1';
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => '11',
        ];
        break;

    case 'fdc' :
    case 'fdcgroup' :
        $project_id = 'fdcgroup';
        $project_folder = 'content1';
        $config = [
            'assets_folder_path' => '12',
            'template_folder_path' => 'wide_banner',
        ];
        break;

    case 'unitedmetals' :
        $project_id = 'unitedmetals';
        $project_folder = 'content1';
        $config = [
            'assets_folder_path' => '13',
            'template_folder_path' => 'home_wide',
        ];
        break;

    case 'recognitionexpress' :
        $project_id = 'recognitionexpress';
        $project_folder = 'content1';
        $config = [
            'assets_folder_path' => '14',
            'template_folder_path' => 'default',
        ];
        break;

    case 'learningcentre' :
        $project_id = 'learningcentre';
        $project_folder = 'content1';
        $config = [
            'assets_folder_path' => '15',
            'template_folder_path' => 'dated',
        ];
        break;

    case 'colaistenanonagle' :
    case 'presentationlimerick':
        $project_id = 'colaistenanonagle';
        $project_folder = 'content1';
        $config = [
            'assets_folder_path' => '16',
            'template_folder_path' => 'default',
        ];
        break;

    case 'mayfieldcommunityschool':
    case 'mayfieldcs':
        $project_id = 'mayfieldcommunityschool';
        $project_folder = 'content1';
        $config = [
            'assets_folder_path' => '17',
            'template_folder_path' => 'default',
        ];
        break;

    case 'mulrooneys':
        $project_id = 'mulrooneys';
        $project_folder = 'content1';
        $config = [
            'assets_folder_path' => '18',
            'template_folder_path' => 'default',
        ];
        break;

    case 'premierhairgroup' :
    case 'phrc':
        $project_id = 'premierhairgroup';
        $project_folder = 'content1';
        break;

    case 'studentaccomodationlimerick':
    case 'groody':
    case 'groodystudentpark':
    case 'groodyvillage':
        $project_id = 'groody';
        $project_folder = 'content1';
        break;

    case 'brookfieldcollege':
    case 'brookfieldlanguage':
    case 'brookfieldinternational':
        // Language site shares database and media with regular site, but has a few different config settings.
        // Each site has different subdomain for test and UAT. They have different top-level domains for live.
        $tld = pathinfo($_SERVER['SERVER_NAME'], PATHINFO_EXTENSION); // top-level-domain

        $project_id = $cur_host;
        $project_folder = 'content1';

        switch ($tld) {
            case 'it'  : $microsite_id = 'brookfieldlanguage';      break;
            case 'com' : $microsite_id = 'brookfieldinternational'; break;
            default    : $microsite_id = ($cur_host == 'brookfieldcollege') ? null : $cur_host; break;
        }

        $config = [
            'microsite_id' => $microsite_id,
            'successful_payment_seller_bookings' => 'successful-payment-seller',
            'successful_payment_customer_bookings' => 'successful-payment-customer',
            'booking' => 'booking',
            'template_folder_path' => '04',
            'assets_folder_path' => '32',
            'project_media_folder' => 'brookfieldcollege',
            'db_id' => 'brookfieldcollege',
            'project_suffix' => ($microsite_id == 'brookfieldlanguage' || $microsite_id == 'brookfieldinternational') ? 'lang' : 'coll',
            'fulltime_course_booking_enable' => true,
        ];
        break;


    case 'courseco-demo':
    case 'culleninsurances':
    case 'icse':
    case 'paqit':
    case 'smartmarketing':
        $project_folder = 'content1';
        break;

    /*------------------------------------*\
      #Custom projects
    \*------------------------------------*/
    case 'julies':
    case 'kes':
    case 'kilmartin':
    case 'keslanguage':
        $project_id   = ($cur_host == 'keslanguage') ? $cur_host : 'kilmartin';
        $microsite_id = ($cur_host == 'keslanguage') ? $cur_host : null;
        $project_folder = 'kilmartin';
        $config = [
            'microsite_id' => $microsite_id,
            'db_id' => 'kilmartin',
            'project_media_folder' => 'kilmartin',
            'template_folder_path' => 'kes1',
            'assets_folder_path' => 'kes1',
            'booking' => 'booking',
            'project_suffix' => ($microsite_id == 'keslanguage' ? 'lang' : 'coll'),
            'successful-payment-seller-bookings' => 'successful_payment_seller_bookings',
            'successful-payment-customer-bookings' => 'successful_payment_customer_bookings',
            'successful_payment_seller_bookings' => 'successful-payment-seller',
            'successful_payment_customer_bookings' => 'successful-payment-customer',
            'no_reply_email' => 'Notifications <no-reply@kes.ie>',
        ];
        break;

    case 'ideabubble':
        $project_folder = 'ideabubble'; break;
        break;

    case 'ibeducate':
    case 'educo':
        $project_folder = 'ideabubble';
        break;

    case 'centurylife':
        $project_folder = 'centurylife';;
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => 'default',
        ];
        break;

    case 'donalryan':
        $project_folder = 'donalryan';
        $config = [
            'no_reply_email' => 'Notifications <no-reply@donalryan.ie>',
            'template_folder_path' => 'default',
            'assets_folder_path' => 'default',
        ];
        break;

    case 'pcsystems':
        $project_folder = 'pcsystems';
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => 'default',
        ];
        break;

    case 'radarstores':
        $project_folder = 'centurylife';
        $config = [
            'template_folder_path' => 'default',
            'assets_folder_path' => 'default',
        ];
        break;

    case 'tangotelecom':
        $project_folder = 'tangotelecom';
        $config = [
            'no_reply_email' => 'Notifications <no-reply@tangotelecom.com>',
            'template_folder_path' => 'default',
            'assets_folder_path' => 'default',
        ];
        break;

    /*------------------------------------*\
     # Default
    \*------------------------------------*/
    default:
        $project_folder = getenv('project_folder');
        if ($project_folder == '') {
            $project_folder = 'shop1';
        }
        $config = [
            'template_folder_path' => '04',
            'assets_folder_path' => '44',
        ];
        break;
}

$config['project_folder'] = isset($project_folder) ? $project_folder : 'shop1';

$config['add_to_list_admin_notification'] = isset($config['add_to_list_admin_notification']) ? $config['add_to_list_admin_notification'] : 'add_to_list_admin_notification';
$config['contact_notification']           = isset($config['contact_notification'])           ? $config['contact_notification']           : 'contact-form';
$config['contact_notification_callback']  = isset($config['contact_notification_callback'])  ? $config['contact_notification_callback']  : 'contact_notification_callback';
$config['engine_plugins']                 = isset($config['engine_plugins'])                 ? $config['engine_plugins']                 : '*';
$config['enquiry_form']                   = isset($config['enquiry_form'])                   ? $config['enquiry_form']                   : 'enquiry_form';
$config['payment_mailing_list']           = isset($config['payment_mailing_list'])           ? $config['payment_mailing_list']           : 'admins';
$config['products_plugin_page']           = isset($config['products_plugin_page'])           ? $config['products_plugin_page']           : 'products.html';
$config['project_media_folder']           = (isset($config['project_media_folder'])          ? $config['project_media_folder']           : $project_id).$shared_media_suffix;
$config['successful_payment_customer']    = isset($config['successful_payment_customer'])    ? $config['successful_payment_customer']    : 'successful-payment-customer';
$config['successful_payment_seller']      = isset($config['successful_payment_seller'])      ? $config['successful_payment_seller']      : 'successful-payment-seller';

// Connection parameters
$config['db_id']            = (isset($config['db_id'])           ? $config['db_id']            : $project_id).$db_suffix;
$vhost_db_id = getenv('vhost_db_id');
if ($vhost_db_id != '') {
    $config['db_id'] = $vhost_db_id;
}
$config['db_prod_hostname'] = isset($config['db_prod_hostname']) ? $config['db_prod_hostname'] : getenv('dbhostname');
$config['db_prod_username'] = isset($config['db_prod_username']) ? $config['db_prod_username'] : getenv('dbusername');
$config['db_prod_password'] = isset($config['db_prod_password']) ? $config['db_prod_password'] : getenv('dbpassword');

$GLOBALS['project'] = $project_id;
$config['sprint'] = $sprint;


if (!defined('PROJECTPATH')) {
    define('PROJECTPATH', ENGINEPATH.'projects'.DIRECTORY_SEPARATOR.$config['project_folder'].DIRECTORY_SEPARATOR);
}

if (!defined('PROJECTNAME')) {
    define('PROJECTNAME', $project_id);
}

if (Kohana::$environment == Kohana::DEVELOPMENT || stripos($_SERVER['HTTP_HOST'], '.dev') !== false) {
	$notifications_email = ''; // do not email dalm errors on .dev
} else if (Kohana::$environment == Kohana::TESTING) {
	$notifications_email = '';
} else if (Kohana::$environment == Kohana::STAGING) {
	$notifications_email = 'support@ideabubble.ie';
} else {
	$notifications_email = 'support@ideabubble.ie';
}

$config['Permissions']       = ['System' => ['super_level', 'access_settings']];
$config['DALM']              = ['notifications_email' => $notifications_email];
$config['ib_powered_by']     = 'Powered by IB Platform v';
$config['ib_engine_version'] = '2.6.9.3';

return $config;