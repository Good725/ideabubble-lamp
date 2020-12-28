<?php
/**
 * Wrapper for the Facebook API https://github.com/facebookarchive/facebook-php-sdk
 *
 *
 * We're currently using a deprecated version, since the latest version
 * https://github.com/facebook/facebook-php-sdk-v4 does not work on PHP 5.3
 * Instructions for upgrading are in the comments.
 */

defined('SYSPATH') or die('No direct script access.');

require_once(APPPATH.'vendor/facebook/php-sdk/src/facebook.php');
// Replace with the below if we upgrade.
// require_once(APPPATH.'vendor/facebook/php-sdk-v4/src/Facebook/autoload.php');

class IbFacebookApi extends Facebook // replace with Facebook/Facebook if we upgrade
{
	protected $_app_id;
	protected $_app_secret;
	protected $_access_token;
	protected $_default_graph_version;
	protected $_user;

	public function __construct()
	{
		$settings                     = Settings::instance()->get();
		$this->_app_id                = $settings['facebook_api_app_id'];
		$this->_app_secret            = $settings['facebook_api_secret_id'];
		$this->_access_token          = $settings['facebook_api_access_token'];
		$this->_default_graph_version = 'v2.6';
		$config = array(
			'appId'  => $this->_app_id,
			'secret' => $this->_app_secret,
		);
		$this->_user = $this->getUser();

		parent::__construct($config);
	}

	/**
	 * Post a message
	 * @param         $message - message to send
	 * @param  null   $link    - link to a web page that the message refers to
	 * @param  string $target  - the user ID of the target. Use 'me' to post to your own wall
	 * @return mixed           - the decoded response or FALSE if there is an error (which will be written to the app logs)
	 */
	public function post_message($message, $link = NULL, $target = 'me')
	{
		try {
			$config = array(
				'access_token' => $this->_access_token,
				'message'      => $message,
				'link'         => $link,
			);

			return $this->api('/'.$target.'/feed', 'POST', $config);
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, 'Error posting to Facebook: '.$e->getMessage().$e->getTraceAsString())->write();
			return FALSE;
		}
	}


	/*
	 * Replace the construct and post_message functions with the below code if we upgrade
	 *
	public function __construct($config = array())
	{
		if (empty($config))
		{
			$settings                     = Settings::instance()->get();
			$this->_app_id                = $settings['facebook_api_app_id'];
			$this->_app_secret            = $settings['facebook_api_secret_id'];
			$this->_access_token          = $settings['facebook_api_access_token'];
			$this->_default_graph_version = 'v2.6';
			$config = array(
				'app_id'                => $this->_app_id,
				'app_secret'            => $this->_app_secret,
				'default_graph_version' => $this->_default_graph_version
			);
		}

		parent::__construct($config);
	}

	public function post_message($message, $link)
	{
		try {
			$params = ['link' => $link,'message' => $message];
			parent::post('/me/feed', $params, $this->_access_token);
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
			$error = 'Facebook graph returned an error: ' . $e->getMessage().$e->getTraceAsString();
		}
		catch(Facebook\Exceptions\FacebookSDKException $e) {
			$error = 'Facebook SDK returned an error: ' . $e->getMessage().$e->getTraceAsString();
		}
		catch(Exception $e) {
			$error = 'Error posting to Facebook: '.$e->getMessage().$e->getTraceAsString();
		}

		if (isset($error))
		{
			Log::instance()->add(Log::ERROR, $error)->write();
			return FALSE;
		}
	}
	*/

}
