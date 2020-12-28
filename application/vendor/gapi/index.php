<?php
set_include_path(APPPATH.'/vendor/gapi/src/');
require_once 'Google/Client.php';
require_once 'Google/Service/Analytics.php';
require_once 'Google/Service.php';

function get_google_data($date_from,$date_to,$metrics,$options = array())
{
    $google_auth = new Google_Auth_AssertionCredentials('706680743683-rsidcfg9n6ljk8qb0dkq5oghkg628sho@developer.gserviceaccount.com',array('https://www.googleapis.com/auth/analytics.readonly'),file_get_contents('c8cd40c6ec84e00ff5253923e4a13d13869f765b-privatekey.p12',true));
    $token = Session::instance()->get('token');
    $projectId = Settings::instance()->get('google_project_id');//'8338285 38912788';
    $client = new Google_Client();
    $client->setApplicationName(Settings::instance()->get('google_application_name'));
    $client->setAssertionCredentials($google_auth);
    $client->setClientId(Settings::instance()->get('google_client_id'));
    $client->setAccessType(Settings::instance()->get('google_access_type'));
    $service = new Google_Service_Analytics($client);

    if(isset($_GET['logout']))
    {
        Session::instance()->delete('token');
        die('Logged out.');
    }

    if(isset($_GET['code']))
    {
        $client->authenticate($_GET['code']);
        Session::instance()->set('token',$client->getAccessToken());
    }

    if(!is_null($token))
    {
        $token = Session::instance()->get('token');
        $client->setAccessToken($token);
    }
    return $service->data_ga->get('ga:'.$projectId, $date_from, $date_to, $metrics, $options);
}
?>