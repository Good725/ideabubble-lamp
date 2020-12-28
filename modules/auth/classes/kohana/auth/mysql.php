<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * ORM Auth driver.
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
$GLOBALS['autologin'] = str_replace(array('www.', '.'), array('', '_'), $_SERVER['HTTP_HOST']) . '_autologin';
abstract class Kohana_Auth_Mysql extends Auth {

	public function __construct($id = NULL)
	{
		parent::__construct($id);
		if (mt_rand(1, 200) === 1)
		{
			// Do garbage collection
			$this->delete_expired_tokens();
		}
	}

	/**
	 * Checks if a session is active.
	 *
	 * @param   mixed    $role Role name string, role ORM object, or array with role names
	 * @return  boolean
	 */
	public function logged_in($role = NULL)
	{
		// Get the user from the session
		$user = $this->get_user();

		if ( ! $user)
		{
			return FALSE;
		}
		else
		{
			return $user;
		}
	}

	/**
	 * Logs a user in. Added logging function call on fail and success
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */

	protected function _login($user, $password, $remember)
	{

		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = DB::Select()
					->from('engine_users')
					->where('email', '=', $username)
					->and_where('deleted', '!=', '1')
					->and_where('can_login', '=', '1')
					->execute();

			if ($user->count() === 0)
			{
				return FALSE;
			}

			$user = $user->current();

		}

		if (is_string($password))
		{
			// Create a hashed password
			$password = $this->hash($password);
		}

		// If the passwords match, perform a login
		if ($user['password'] === $password)
		{

			if ($remember === TRUE)
			{

				$token = $this->create_token();

				// Token data
				$data = array(
					'user_id'    => $user['id'],
					'expires'    => time() + $this->_config['lifetime'],
					'user_agent' => sha1(Request::$user_agent),
					'token'      => $this->hash($token),
				);

				// Create a new autologin token
				DB::insert('engine_user_tokens', array('user_id', 'expires', 'user_agent', 'token'))
						->values($data)
						->execute();

				// Set the autologin cookie
				Cookie::$httponly = true;
				Cookie::set($GLOBALS['autologin'], $token, $this->_config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			// Log the login
			$this->login_log($username, TRUE);

			return TRUE;
		}

		// Log the failed attempt
        DB::update('engine_users')
            ->set(array('logins_fail' => DB::expr('logins_fail + 1'), 'last_fail' => time()))
            ->where('id', '=', $user['id'])
            ->execute();

		$this->login_log($username);

		return FALSE;
	}

	/**
	 * Log a failed or successful login.
	 * session data: user_id, username, roles.
	 */
	protected function login_log($user, $success=FALSE)
	{
		if ($success)
		{
			$success = 1;
		}
		else
		{
			$success = 0;
		}

		$db = Database::instance();
		$db->escape($user_agent = json_encode(Request::user_agent(array('browser', 'version', 'robot', 'mobile', 'platform'))));
		$session = Session::instance();

		$sql_log = ('INSERT INTO `engine_loginlogs` (`ip_address`, `email`, `time`, `success`, `user_agent`, `session`) VALUES (INET_ATON('.$db->escape(Request::$client_ip).'), '.$db->escape($user).', '.time().', '.$success.', \''.$user_agent.'\', '.$db->escape($session->id()).')');

		// Execute the request as a type=LOG
		return $db->query(Database::LOG, $sql_log);

	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    username string, or user ORM object
	 * @param   boolean  mark the session as forced
	 * @return  boolean
	 */
	public function force_login($user, $mark_session_as_forced = FALSE)
	{
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = DB::Select()
					->from('engine_users')
					->where('email', '=', $username)
					->execute();
			$user = current($user);
		}

		if ($mark_session_as_forced === TRUE)
		{
			// Mark the session as forced, to prevent users from changing account information
			$this->_session->set('auth_forced', TRUE);
		}

		// Run the standard completion
		$this->complete_login($user);
	}

	/**
	 * Logs a user in, based on the authautologin cookie.
	 *
	 * @return  mixed
	 */
	public function auto_login()
	{
		//exit('auto login :-(');
		if ($token = Cookie::get($GLOBALS['autologin']))
		{
			$token_hash = $this->hash($token);

			// Load the hash of the token and user
			$token = DB::Select()
					->from('engine_user_tokens')
					->where('token', '=', $token_hash)
					->execute();

			if ($token->count() > 0)
			{

				$token = $token->current();

				if ($token['user_agent'] === sha1(Request::$user_agent))
				{
					$new_token = $this->create_token();

					// Save the token to create a new unique token
					DB::update('engine_user_tokens')
							->set(array('token' => $this->hash($new_token)))
							->where('token', '=', $token['token'])
							->execute();

					// Set the new token
                    Cookie::$httponly = true;
					Cookie::set($GLOBALS['autologin'], $new_token, $token['expires'] - time());

					// Load the user
					$user = DB::Select()
							->from('engine_users')
							->where('id', '=', $token['user_id'])
							->and_where('deleted', '!=', '1')
							->and_where('can_login', '=', '1')
							->execute();

					if ($user->count() === 0)
					{
						return FALSE;
					}

					$user = $user->current();

					// Complete the login with the found data
					$this->complete_login($user);

					// Automatic login was successful
					return $user;
				}

				// Token is invalid
				$this->delete_token($token['token']);
			}
		}

		return FALSE;
	}

	/**
	 * Gets the currently logged in user from the session (with auto_login check).
	 * Returns FALSE if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_user($default = NULL)
	{

		$user = parent::get_user($default);

		// If the user is not in the session OR the session has idled out.
		if ($user AND time() < $user['idle_time'])
		{
			// Update the idle time in the session
			$user['idle_time'] = time() + $this->_config['idle_time'];
			$this->_session->set($this->_config['session_key'], $user);

			return $user;
		}
		else
		{

			// check for "remembered" login
			return $this->auto_login();
		}

	}

	/**
	 * Log a user out and remove any autologin cookies.
	 *
	 * @param   boolean  completely destroy the session
	 * @param	boolean  remove all tokens for user
	 * @return  boolean
	 */
	public function logout($destroy = FALSE, $logout_all = FALSE)
	{
		// Set by force_login()
		$this->_session->delete('auth_forced');

		if ($token = Cookie::get($GLOBALS['autologin']))
		{
			// Delete the autologin cookie to prevent re-login
			Cookie::delete($GLOBALS['autologin']);

			// Clear the autologin token from the database
			$token = DB::Select()
					->from('engine_user_tokens')
					->where('token', '=', $token)
					->execute();

			if ($token->count() > 0 AND $logout_all)
			{
				$this->delete_token_user($token->get('user_id'));
			}
			elseif ($token->count() > 0)
			{
				$this->delete_token($token->get('token'));
			}
		}

		return parent::logout($destroy);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username string, or user ORM object
	 * @return  string
	 */
	public function password($user)
	{
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = DB::Select('password')
					->from('engine_users')
					->where('email', '=', $username)
					->execute();
		}

		return $user['password'];
	}

	/**
	 * Complete the login for a user by incrementing the logins and setting
	 * session data: user_id, username, roles.
	 *
	 * @param   object  user ORM object
	 * @return  void
	 */
	protected function complete_login($user)
	{

		// Update the number of logins and set the last login date
		DB::update('engine_users')
				->set(array('logins' => DB::expr('logins + 1'), 'last_login' => time()))
				->where('id', '=', $user['id'])
				->execute();

		// Set the idle time in the session
		$user['idle_time'] = time() + $this->_config['idle_time'];

		parent::complete_login($user);

		return TRUE;
	}

	/**
	 * Compare password with original (hashed). Works for current (logged in) user
	 *
	 * @param   string  $password
	 * @return  boolean
	 */
	public function check_can_login($user)
	{
		// Update the number of logins and set the last login date
		DB::update('engine_users')
				->set(array('logins' => DB::expr('logins + 1'), 'last_login' => time()))
				->where('id', '=', $user['id'])
				->execute();

		return ($user->password);
	}

	/**
	 * Compare password with original (hashed). Works for current (logged in) user
	 *
	 * @param   string  $password
	 * @return  boolean
	 */
	public function check_password($password)
	{
		$user = $this->get_user();

		if ( ! $user)
			return FALSE;

		return ($this->hash($password) === $user->password);
	}

	protected function create_token()
	{
		// Generate a unique token
		do
		{
			$token = sha1(uniqid(Text::random('alnum', 32), TRUE));
		}
		while(DB::Select()->from('engine_user_tokens')->where('token', '=', $this->hash($token))->execute()->count() > 0);

		return $token;
	}

	/**
	 * Deletes a specific token.
	 *
	 */
	public function delete_token($token)
	{
		// Delete all tokens for this user
		DB::delete('engine_user_tokens')
				->where('token', '=', $token)
				->execute();
	}


	/**
	 * Deletes all expired tokens for a user.
	 *
	 */
	public function delete_token_user($user_id)
	{
		// Delete all tokens for this user
		DB::delete('engine_user_tokens')
				->where('user_id', '=', $user_id)
				->execute();
	}


	/**
	 * Deletes all expired tokens.
	 *
	 */
	public function delete_expired_tokens()
	{
		// Delete all expired tokens
		DB::delete('engine_user_tokens')
			->where('expires', '<', time())
			->execute();
	}

} // End Auth Mysql