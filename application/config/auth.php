<?php defined('SYSPATH') or die('No direct access allowed.');

$lifetime  = IbHelpers::duration_to_seconds(Settings::instance()->get('login_lifetime'));
$idle_time = IbHelpers::duration_to_seconds(Settings::instance()->get('login_idle_time'));
return array(
	'driver'       => 'mysqli',
	'hash_method'  => 'sha256',
	'hash_key'     => 'b31542ZE3 Always code as if the guy who ends up maintaining your code will be a violent psychopath who knows where you live. a1919e716a02',
	'lifetime'     => $lifetime  ? $lifetime  : (60*60*24), // time (in sec) for how long a login lasts
	'idle_time'    => $idle_time ? $idle_time : (60*30),    // time that you will be signed out after with no activity
	'session_type' => Session::$default,
	'session_key'  => 'admin_user',
);

