<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * User authorization library. Handles user login and logout, as well as secure
 * password hashing.
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2010 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Auth {

	public $two_step_auth = null;
	public $user = null;

	// Auth instances
	protected static $_instance;

	/**
	 * Singleton pattern
	 *
	 * @return Auth
	 */
	public static function instance()
	{
		if ( ! isset(Auth::$_instance))
		{
			// Load the configuration for this type
			$config = Kohana::$config->load('auth');

			if ( ! $type = $config->get('driver'))
			{
				$type = 'file';
			}

			// Set the session class name
			$class = 'Auth_'.ucfirst($type);

			// Create a new session instance
			Auth::$_instance = new $class($config);
		}

		return Auth::$_instance;
	}

	protected $_session;

	protected $_config;

	/**
	 * Loads Session and configuration options.
	 *
	 * @return  void
	 */
	public function __construct($config = array())
	{
		// Save the config in the object
		$this->_config = $config;

		$this->_session = Session::instance($this->_config['session_type']);
	}

	abstract protected function _login($username, $password, $remember);

	abstract protected function _login_with_external_provider($username, $remember);

	abstract public function password($username);

	abstract public function check_password($password);

    /**
     * Get the logged-in contact, loaded using the Contacts3 contact ORM
     */
    public function get_contact()
    {
        $user = self::get_user();
        $user_orm = new Model_User(isset($user['id']) ? $user['id'] : null);
        return $user['id'] ? $user_orm->contacts->find_undeleted() : new Model_Contacts3_Contact();
    }

    /**
     * Get the logged-in contact, loaded using the Contacts3 model
     */
    public function get_contact3()
    {
        $user = self::get_user();
        $contact_id = isset($user['id']) ? Model_Contacts3::get_linked_contact_to_user($user['id'])['id'] : null;
        return new Model_Contacts3($contact_id);
    }


    /**
	 * Gets the currently logged in user from the session.
	 * Returns NULL if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_user($default = NULL)
	{
		return $this->_session->get($this->_config['session_key'], $default);
	}

    public function get_user_orm()
    {
        $user = self::get_user();
        return new Model_User(isset($user['id']) ? $user['id'] : null);
    }

	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE)
	{
		if (empty($password))
			return FALSE;

		return $this->_login($username, $password, $remember);
	}

	/**
	 * Attempt to log in a user with sms/email code
	 *
	 * @param   string   user_id to log in
	 * @param   string   code to check against
	 * @return  boolean
	 */
	public function step2_login($user_id, $code)
	{
		if (empty($code))
			return FALSE;

		return $this->_step2_login($user_id, $code);
	}

	/**
	 * Log out a user by removing the related session variables.
	 *
	 * @param   boolean  completely destroy the session
	 * @param   boolean  remove all tokens for user
	 * @return  boolean
	 */
	public function logout($destroy = FALSE, $logout_all = FALSE)
	{
		if ($destroy === TRUE)
		{
			// Destroy the session completely
			$this->_session->destroy();
		}
		else
		{
			// Remove the user from the session
			$this->_session->delete($this->_config['session_key']);

			// Regenerate session_id
			$this->_session->regenerate();
		}

		// Double check
		return ! $this->logged_in();
	}

	/**
	 * Check if there is an active session. Optionally allows checking for a
	 * specific role.
	 *
	 * @param   string   role name
	 * @return  mixed
	 */
	public function logged_in($role = NULL)
	{
		return ($this->get_user() !== NULL);
	}

	/**
	 * Creates a hashed hmac password from a plaintext password. This
	 * method is deprecated, [Auth::hash] should be used instead.
	 *
	 * @deprecated
	 * @param   string  plaintext password
	 */
	public function hash_password($password)
	{
		return $this->hash($password);
	}

	/**
	 * Perform a hmac hash, using the configured method.
	 *
	 * @param   string  string to hash
	 * @return  string
	 */
	public function hash($str)
	{
		if ( ! $this->_config['hash_key'])
			throw new Kohana_Exception('A valid hash key must be set in your auth config.');

		return hash_hmac($this->_config['hash_method'], $str, $this->_config['hash_key']);
	}

	protected function complete_login($user)
	{
		// Regenerate session_id
		$this->_session->regenerate();

		// Store username in session
		$this->_session->set($this->_config['session_key'], $user);

		return TRUE;
	}

} // End Auth
