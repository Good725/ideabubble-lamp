<?php defined('SYSPATH') or die('No direct script access.');

class Model_Users extends ORM
{
    const MAIN_TABLE = 'engine_users';
    const EXTERNAL_PROVIDERS_TABLE = 'engine_external_providers';
    const EXTERNAL_PROVIDER_USER_DATA_TABLE = 'external_provider_user_data';
    const TWO_STEP_AUTH_TABLE = 'engine_login_auth_codes';

	protected $_table_name = 'engine_users';

	protected $_belongs_to = array(
		'discount' => array('model' => 'discountFormat', 'foreign_key' => 'discount_format_id'),
		'role' => array('model' => 'roles', 'foreign_key' => 'role_id')
	);

    public static $on_delete_data_handlers = array();
    public static $on_download_data_handlers = array();

    public static function get_visible_users()
    {
        return DB::select()
            ->from('engine_users')
            ->where('deleted', '!=', '1')
            ->order_by('name')
            ->order_by('surname')
            ->execute()
            ->as_array();
    }

	//used for widgets
	public static function get_visible_users_not_ignite_or_ideabubble($user = NULL) {

		$users = DB::select()
				->from('engine_users')
				->where('deleted', '!=', '1')
				->and_where('email', 'NOT LIKE', '%ideabubble%')
				->and_where('email', 'NOT LIKE', '%ignite%');

		$result = $users
				->execute()
				->as_array();

		return $result;

	}

	public static function get_visible_users_for_daily_digest($user = NULL) {

		$users = DB::select()
				->from('engine_users')
				->where('deleted', '!=', '1')
				->and_where('daily_digest_email', '=', '1');

		$result = $users
				->execute()
				->as_array();

		return $result;

	}
	public static function get_user_actvitity_array($user_id){
		$revenue_array = array();
		for($i = 5; $i >= 0; $i--){
			$first = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', strtotime("-".$i." months")), 1, date('Y', strtotime("-".$i." months"))));
			$last = date('Y-m-t 23:59:59', mktime(0, 0, 0, date('m', strtotime("-".$i." months")), 1, date('Y', strtotime("-".$i." months"))));
			$first = strtotime($first);
			$last = strtotime($last);
			$revenue_array[] = DB::select('COUNT("*") as total_number')
				->from('engine_users')
				->join('engine_loginlogs', 'inner')
				->on('engine_users.email', '=', 'loginlogs.email')
				->where('engine_loginlogs.time', '>=', $first)
				->and_where('engine_loginlogs.time', '<=', $last)
				->and_where('engine_users.id', '=', $user_id)
				->execute()
				->as_array();
		}
		return $revenue_array;
	}
	//end used for widgets
	public static function get_user($user, $deleted = 0) {
        $select = DB::select()
            ->from('engine_users');
        if ($deleted !== null) {
            $select->where('deleted', '=', $deleted);
        }
		$users = $select
				->and_where('id', '=', $user)
				->execute();

		$result = $users->current();

		return $result;
	}

	public function update_user_data($userId, $userData) {

        //Create an instance of the Auth class
        Auth::instance()->instance();

        if (isset($userData[ 'trial_start_date' ]) && $userData[ 'trial_start_date' ] === '') {
            $userData[ 'trial_start_date' ] = null;
        }

        $valid_email = 0;
        if (isset($userData['email'])) {
            $valid_email = $this->check_email_user($userData['email'], $userId);
        }

        //Check if the email address field is valid
        if ($valid_email === 0) {

            //Are both passwords blank? Then update other user details.
            if (!isset($userData['password']) || ($userData[ 'password' ] === '' && $userData[ 'mpassword' ] === '')) {
                //remove the password & mpassword from the post array
                unset($userData[ 'password' ]);
                unset($userData[ 'mpassword' ]);
                unset($userData[ 'current_password' ]);

                //Send rest of data to database
                $query = DB::update('engine_users')->set($userData)->where('id', '=', $userId);
                $query->execute();

                //Set result to reflect change
                $result = TRUE;
                IbHelpers::set_message('Your new credentials have been updated.', 'success popup_box');
            }
            else {
                $existing_data = DB::select('*')
                    ->from(self::MAIN_TABLE)
                    ->where('id', '=', $userId)
                    ->execute()
                    ->current();
                if (@$userData['current_password'] != '' && $existing_data['password'] != Auth::instance()->hash($userData['current_password'])) {
                    $result = false;
                    IbHelpers::set_message('Your current password does not match!', 'error popup_box');
                } else //Check that the passwords are the same.
                if (( $userData[ 'password' ] === $userData[ 'mpassword' ] )) {
                    unset($userData['current_password']);
                    if (mb_strlen($userData[ 'password' ]) >= 8) {
                        //Hash the the password
                        $userData[ 'password' ] = Auth::instance()->hash($userData[ 'password' ]);

                        //remove the mpassword from the post array
                        unset($userData[ 'mpassword' ]);

                        //Send the new user data to the database
                        $query = DB::update('engine_users')->set($userData)->where('id', '=', $userId);
                        $query->execute();

                        //Set $result to following response
                        $result = TRUE;
                        IbHelpers::set_message('Your new credentials have been updated.', 'success popup_box');
                    }
                    else {
                        $result = FALSE;
                        IbHelpers::set_message('Your password is too short. It should be at least eight characters long.', 'error popup_box');
                    }
                }
                //Set $result to tell the user that the two passwords are not the same and that they need to input new ones
                else {
                    $result = FALSE;
                    IbHelpers::set_message('Your passwords do not match.', 'error popup_box');
                }
            }
        }
        //If the email address field is not valid
        else if ($valid_email === 1) {
            $result = FALSE;
            IbHelpers::set_message('The email address you entered is not valid.', 'error popup_box');
        }
        // Email address exist
        else {
            $result = FALSE;
            IbHelpers::set_message('The email address you entered is already in use.', 'error popup_box');
        }
        //Send response feedback to settings.php
        return $result;
    }

    //Delete the user
    public function delete_user_data($userId)
    {

        $deleted = array( 'deleted' => 1, 'email' => DB::expr('CONCAT(email, " - ARCHIVE")') ); // The 'ARCHIVE' is per [RW-56]

        $query = DB::update('engine_users')->set($deleted)->where('id', '=', $userId)->execute();

        //Tell The user the selected user has been deleted.
        IbHelpers::set_message('The user has been deleted.', 'success popup_box');

        return $query;
    }

    //unDelete the user
    public function undelete_user_data($userId)
    {
        $user = $this->get_user($userId, null);
        $deleted = array('deleted' => 0, 'email' => str_replace(' - ARCHIVE', '', $user['email']) );

        $query = DB::update('engine_users')->set($deleted)->where('id', '=', $userId)->execute();

        //Tell The user the selected user has been deleted.
        IbHelpers::set_message('The user has been deleted.', 'success popup_box');

        return $query;
    }

    //Add User to users database
    public function add_user_data($userData)
    {

        //Set group_id to null if needs be
        if (!isset($userData[ 'group_id' ])) {
            $userData[ 'group_id' ] = NULL;
        }

        //Set can_login to bit type data
        if (isset($userData[ 'can_login' ])) {
            $userData[ 'can_login' ] = 1;
        }
        else {
            $userData[ 'can_login' ] = 0;
        }

        //Hash the the password
        Auth::instance()->instance();
        if (@$userData['password'] != '!' && @$userData['password'] != '') {
            $userData['password'] = Auth::instance()->hash($userData['password']);
        }

        //Add the necessary values to the $userData array for update
        $userData[ 'registered' ] = date('Y-m-d H:i:s');
        $userData[ 'deleted' ] = 0;

        // Add User to database
        // array('email', 'password', 'role_id', 'group_id', 'can_login', 'name', 'surname', 'address', 'phone', 'registered', 'deleted')
        $query1 = DB::insert('engine_users', array_keys($userData))->values($userData)->execute();

        return $query1;
    }

    /*   -------------------
     *   - CHECK FUNCTIONS -
     *   -------------------
     */

    //Check that the passwords given are the same
    public function check_passwords_match($p1, $p2)
    {
        //Check that the passwords are the same.
        if (( $p1 ) === ( $p2 )) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    //Check that the passwords given are set
    public function check_passwords_set($p1, $p2)
    {
        //Check that the passwords set.
        if (( isset($p1) ) && ( isset($p2) )) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    //Check if an email is well formed
    public function check_email_user($email, $id = NULL)
    {

        $result = Valid::email($email);

        if ($result == null) {
            $result = 1;
        }
        else {
            $result = 0; // valid
            $q = DB::select('email')->from('engine_users')->where('email', '=', $email);
            if (!is_null($id)) {
                $q->and_where('id', '!=', $id);
            }
            $answer = $q->execute()->as_array();
            $result = $answer ? $answer[ 0 ][ 'email' ] : 0;
        }

        return $result;
    }

    //Check if an email is set
    public function check_email_set($email)
    {

        if ($email == null) {
            $result = FALSE;
        }
        else {
            $result = TRUE;
        }
        return $result;
    }

    //Check if an is stored on the database
    public function check_email_used($email)
    {

        $valid = DB::select()->from('engine_users')->where('email', '=', $email)->execute();

        if ($valid->count() > 0) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public function get_user_by_email($email)
    {
        $users = DB::select()->from('engine_users')->where('deleted', '!=', '1')->and_where('email', '=', $email)->execute()->as_array();
        return ( sizeof($users) > 0 ) ? $users[ 0 ] : FALSE;
    }

    // Get all workflow items as an select option list
    public static function get_users_as_option($selected = null, $role = null, $html = true)
    {

        $drop_down_options = '';
        $q = DB::select("users.*", DB::expr("CONCAT_WS(' ', name, surname, '<', email , '>') AS full_name"))->from(array( self::MAIN_TABLE, 'users' ))->join(array( 'engine_project_role', 'roles' ), 'inner')->on('users.role_id', '=', 'roles.id')->and_where_open()->where('users.deleted', '=', 0)->or_where('users.id', '=', $selected)->and_where_close();
        if ($role) {
            if (is_numeric($role)) {
                $q->and_where('role_id', '=', $role);
            }
            else {
                $q->and_where('roles.role', '=', $role);
            }
        }
        $users = $q->execute()->as_array();

        if ($html) {
            $drop_down_options .= '<option value="0">Not Assigned</option>';

            foreach ($users as $user) {
                $drop_down_options .= '<option value="' . $user[ 'id' ] . '"' . ( $user[ 'id' ] == $selected ? 'selected="selected"' : '' ) . '> ' . $user[ 'id' ] . ' - ' . $user[ 'full_name' ] . '</option>';
            }
        }
        else {
            $drop_down_options = $users;
        }

        return $drop_down_options;
    }

    public static function set_user_password_validation($email)
    {
        $result = array();
        $q = DB::select('*')->from(self::MAIN_TABLE)->where('email', '=', $email)->execute()->as_array();
        if (count($q) > 0) {
            $sha1 = sha1(microtime(true));
            $result[ 'user_exists' ] = TRUE;
            $result[ 'user_id' ] = $q[ 0 ][ 'id' ];
            $result[ 'validation' ] = $sha1;
            $result[ 'email' ] = $email;
            $result[ 'first_name' ] = trim($q[ 0 ][ 'name' ]);
            $result[ 'surname' ] = trim($q[ 0 ][ 'surname' ]);
            $result[ 'name' ] = trim($q[ 0 ][ 'name' ] . ' ' . $q[ 0 ][ 'surname' ]);
            DB::update(self::MAIN_TABLE)->set(array( 'validation_code' => $sha1, 'status' => 2 ))->where('id', '=', $q[ 0 ][ 'id' ])->execute();
        }
        return $result;
    }

    public static function get_user_by_validation($validation_code)
    {
        $q = DB::select('id', 'email')->from(self::MAIN_TABLE)->where('validation_code', '=', $validation_code)->and_where('status', '=', 2)->execute()->as_array();
        return count($q) > 0 ? $q[ 0 ] : false;
    }

    public static function send_user_email_verification($user_id, $redirect = false)
    {
        $users_model = new Model_Users($user_id);
        $user = $users_model->get_user($user_id);
        $contact = ORM::factory('Contacts3_Contact')->where('linked_user_id', '=', $user_id)->find_undeleted();

        if ($user) {
            $salt     = Controller_Frontend_Users::SALT;
            $hash     = md5($user['email'].Controller_Frontend_Users::SALT);
            $redirect = $redirect ? '&redirect='.urlencode($redirect) : '';
            $link     = URL::site('frontend/users/registration_confirmation/'.$user_id.'?hash='.$hash.$redirect);
            $name     = $user['name'] ? $user['name'] : $user['email'];

            if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
                $messaging = new Model_Messaging();
                $parameters =  [
                    'link' => $link,
                    'name' => $name,
                    'contactfirstname' => $contact->first_name ?: $user['name'],
                    'contactlastname' => $contact->last_name ?: $user['surname'],
                ];

                $messaging->get_drivers();
                $messaging->send_template(
                    'user-email-verification',
                    null,
                    null,
                    [['target_type' => 'CMS_USER', 'target' => $user_id]],
                    $parameters);
            }
            return $user;
        }
        else {
            return false;
        }
    }

    public static function generate_timezone_list()
    {
        static $regions = array( DateTimeZone::AFRICA, DateTimeZone::AMERICA, DateTimeZone::ANTARCTICA, DateTimeZone::ASIA, DateTimeZone::ATLANTIC, DateTimeZone::AUSTRALIA, DateTimeZone::EUROPE, DateTimeZone::INDIAN, DateTimeZone::PACIFIC, );

        $timezones = array();
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }

        $timezone_offsets = array();
        foreach ($timezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[ $timezone ] = $tz->getOffset(new DateTime);
        }

        // Sort timezones by offset
        asort($timezone_offsets);

        $timezone_list = array();
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $timezone_list[ $timezone ] = "(${pretty_offset}) $timezone";
        }

        return $timezone_list;
    }

    public static function autocomplete_list($term = '', $role_ids = null)
    {
        $q = DB::select(array( "id", "value" ), DB::expr("CONCAT_WS(' ', name, surname, '<', email, '>') AS label"))->from(self::MAIN_TABLE)->where('deleted', '=', 0);
        if ($term) {
            $q->and_where_open();
            $q->or_where('name', 'like', '%' . $term . '%');
            $q->or_where('surname', 'like', '%' . $term . '%');
            $q->or_where('email', 'like', '%' . $term . '%');
            $q->or_where('phone', 'like', '%' . $term . '%');
            $q->or_where('mobile', 'like', '%' . $term . '%');
            $q->and_where_close();
        }
        if ($role_ids) {
            $q->and_where('role_id', 'in', $role_ids);
        }
        $q->order_by('name')->order_by('surname');
        $q->limit(50);
        $users = $q->execute()->as_array();
        return $users;
    }

    /**
     * Generate a random password
     * Password will be eight characters, including at least one uppercase letter, lowercase letter, number and symbol.
     */
    public function random_password()
    {
        $length = 8;
        $sets = [
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'abcdefghijklmnopqrstuvwxyz',
            '1234567890',
            '~!@#$%^&*(){}[],./-?'
        ];

        $password = '';

        // Ensure the password gets at least one character from each group.
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }

        // Fill the remaining spots with other random characters from any group.
        while(strlen($password) < $length) {
            $random_set = $sets[array_rand($sets)];
            $password .= $random_set[array_rand(str_split($random_set))];
        }

        // Randomise the order. So the first four characters can appear at any point in the password.
        return str_shuffle($password);
    }

//    private static function get_external_provider_data( $provider_id ){
//        $res = array();
//            $res = DB::select("data")
//                ->from('engine_external_providers')
//                ->where('id', '=', $provider_id)
//                ->and_where('disabled', '=', 0)
//                ->execute()
//                ->as_array();
//
//        return $res[0];
//    }

    private static function get_facebook_login_app_id( $env ){
        $res = array();
            $res = DB::select($env)
                ->from('engine_settings')
                ->where('variable', '=', 'facebook_appId')
                ->execute()
                ->as_array();

        return $res[0];
    }

    private static function get_facebook_login_secret( $env ){
        $res = array();
        $res = DB::select($env)
            ->from('engine_settings')
            ->where('variable', '=', 'facebook_secret')
            ->execute()
            ->as_array();

        return $res[0];
    }

    private static function get_google_login_client_id($env){
        $res = array();
        $res = DB::select($env)
            ->from('engine_settings')
            ->where('variable', '=', 'google_login_client_id')
            ->execute()
            ->as_array();

        return $res[0];
    }

    public static function get_facebook_data( ){
        switch (Kohana::$environment) {
            case Kohana::PRODUCTION:
                $env = 'value_live';
                break;

            case Kohana::STAGING:
                $env = 'value_stage';
                break;

            case Kohana::TESTING:
                $env = 'value_test';
                break;

            default:
                $env = 'value_dev';
                break;
        }

        $res = array();
        $appId = self::get_facebook_login_app_id($env);
        $secret = self::get_facebook_login_secret($env);
        if(sizeof($appId) == 1 AND sizeof($secret) == 1){
            $res['appId'] = $appId[$env];
            $res['secret'] = $secret[$env];
        }

        return $res;
    }

    public static function get_google_data( ){
        switch (Kohana::$environment) {
            case Kohana::PRODUCTION:
                $env = 'value_live';
                break;

            case Kohana::STAGING:
                $env = 'value_stage';
                break;

            case Kohana::TESTING:
                $env = 'value_test';
                break;

            default:
                $env = 'value_dev';
                break;
        }
        $client_id = self::get_google_login_client_id($env);
        $res = array();
        if(sizeof($client_id) == 1){
            $res['client_id'] = $client_id[$env];
        }

        return $res;
    }

    private function save_external_provider_data($provider_id = null, $provider_user_id = null, $user_id = null)
    {
        $result = DB::query(Database::INSERT, 'INSERT INTO external_provider_user_data (provider_id, provider_user_id, user_id) VALUES (:provider_id, :provider_user_id, :user_id)')->bind(':provider_id', $provider_id)->bind(':provider_user_id', $provider_user_id)->bind(':user_id', $user_id)->execute();

        if (sizeof($result) > 0) {
            return true;
        }
        return false;
    }

    public static function get_external_provider_user_id($provider_id = null, $provider_user_id_token = null)
    {

        if ($provider_id == 1) {
            require_once Kohana::find_file('vendor', 'facebook/php-sdk/src/facebook');

            $facebook_data = Model_Users::get_facebook_data();
            $config = array();
            $config[ 'appId' ] = $facebook_data['appId'];
            $config[ 'secret' ] = $facebook_data['secret'];

            $facebook = new Facebook($config);
            $access_token = FALSE;

            return $facebook->getUser();
        }
        else if ($provider_id == 2) {
            require_once Kohana::find_file('vendor', 'gapi/index');

            $google_data = Model_Users::get_google_data();

            // Get $id_token via HTTPS POST.
            $client = new Google_Client();
            $client->setClientId($google_data['client_id']);
            $payload = $client->verifyIdToken($provider_user_id_token);
            if ($payload) {
                $userid = $payload->getUserId();
                return $userid;

            }
        }
        return null;
    }

    public static function check_external_provider_data($provider_id = null, $provider_user_id = null)
    {
        $result = DB::query(Database::SELECT, ' SELECT `engine_external_providers`.`disabled`, `external_provider_user_data`.`provider_user_id`,external_provider_user_data.user_id 
                                                FROM `engine_external_providers` 
                                                LEFT JOIN `external_provider_user_data` 
                                                    ON (`engine_external_providers`.`id` = `external_provider_user_data`.`provider_id` AND `external_provider_user_data`.`provider_user_id` = :provider_user_id) 
                                                WHERE `engine_external_providers`.`id` = :provider_id')->bind(':provider_id', $provider_id)->bind(':provider_user_id', $provider_user_id)->execute()->as_array();
        return $result[ 0 ];
    }

    public function register_user($data)
    {


        $external_user_id =  @$data['external_user_id'];
        $external_provider_id = @$data['external_provider_id'];
        $send_verification_email = isset($data['send_verification_email']) ? $data['send_verification_email'] : true;
        if (@$data['email_verified'] == 1) {
            $send_verification_email = 0;
        }

        $roles = new Model_Roles();
        if (Settings::instance()->get('engine_enable_organisation_signup_flow') == '1'
            && $data['role_id'] == $roles->get_id_for_role('Org rep')) {
            $send_verification_email = 0;
        }
        if($external_provider_id == 2){
            $external_user_id = Model_Users::get_external_provider_user_id($external_provider_id,$external_user_id );
        }


        // if user using external provider
        $user_is_valid_for_external_provider = false;
        if (!empty($external_user_id) AND !empty($external_provider_id)) {
            $result = self::check_external_provider_data($external_provider_id, $external_user_id);
            if ($result['disabled'] == 0 AND is_null($result['provider_user_id'])) {
                $data['mpassword'] = $data['password'] = $this->random_password();
                $user_is_valid_for_external_provider = true;
            }
        }
        $newUser = array();
        $newUser['name']           = isset($data['name'])           ? $data['name']           : '';
        $newUser['surname']        = isset($data['surname'])        ? $data['surname']        : '';
        $newUser['email']          = isset($data['email'])          ? $data['email']          : '';
        $newUser['password']       = isset($data['password'])       ? $data['password']       : '';
        $newUser['mpassword']      = isset($data['mpassword'])      ? $data['mpassword']      : '';
        $newUser['email_verified'] = isset($data['email_verified']) ? $data['email_verified'] : 0;
        $newUser['register_source']= isset($data['register_source'])? $data['register_source'] : 'web';
        $newUser['can_login']      = isset($data['can_login'])      ? $data['can_login']      : 0;
		$newUser['signup_newsletter']      = isset($data['signup_newsletter'])      ? $data['signup_newsletter']      : 0;
        $newUser['mobile']         = isset($data['mobile']) ? $data['mobile'] : '';
        $newUser['country_dial_code_mobile']         = isset($data['country_dial_code_mobile']) ? $data['country_dial_code_mobile'] : '';
        $newUser['dial_code_mobile']         = isset($data['dial_code_mobile']) ? $data['dial_code_mobile'] : '';

        if (!empty($data['role_id'])) {
            $newUser['role_id'] = $data['role_id'];
        } else {
            $roles = new Model_Roles();
            $newUser['role_id'] = $roles->get_id_for_role('External User');
        }
        if (Settings::instance()->get('two_step_authorization')) {
            $sms_resource = Model_Resources::get_by_alias('user_auth_2step_sms');
            $email_resource = Model_Resources::get_by_alias('user_auth_2step_email');
            if (Model_Roles::has_permission($newUser['role_id'], $sms_resource['id'])) {
                $newUser['two_step_auth'] = 'SMS';
            } elseif(Model_Roles::has_permission($newUser['role_id'], $email_resource['id'])) {
                $newUser['two_step_auth'] = 'Email';
            } else {
                $newUser['two_step_auth'] = 'None';
            }
        } else {
            $newUser['two_step_auth'] = 'None';
        }

        $success = FALSE;
        $error = '';
        $userId = '';
        if ($this->check_email_set($newUser['email']) == FALSE) {
            $error = 'The email address has not been set.';
        } else if ($this->check_email_used($newUser['email']) == TRUE) {
            if(Settings::instance()->get('company_title') != FALSE) {
                $error = 'We see that you are already a customer of ' . Settings::instance()->get('company_title') .
                    '. Please enter the required details below to access your account';
            }

            else
                $error = "This account already exists, please log in or change your login details and try again.";
        } else if ($this->check_email_user($newUser['email']) == 1) {
            $error = 'This email address is not valid.';
        } else if ($this->check_passwords_set($newUser['password'], $newUser['mpassword']) == FALSE) {
            $error = 'Please fill in both password fields.';
        } else if ($this->check_passwords_match($newUser['password'], $newUser['mpassword']) == FALSE) {
            $error = 'The passwords you entered do not match.';
        } else if (strlen($newUser['password']) < 8 && $newUser['password'] != '!') {
            $error = 'This password is too short, please enter a password with a minimum of 8 characters.';
        } else if (!preg_match('/[A-Z]/', $newUser['password']) && $newUser['password'] != '!') {
            $error = 'This password must contain at least one capital letter.';
        } else if (!preg_match('/[a-z]/', $newUser['password']) && $newUser['password'] != '!') {
            $error = 'This password must contain at least one lower case letter.';
        } else if (!preg_match('/\d/', $newUser['password']) && $newUser['password'] != '!') {
            $error = 'This password must contain at least one numerical digit.';
        } else {
            unset($newUser['mpassword']);
            $inserted = $this->add_user_data($newUser);
            $userId = $inserted[0];

            // if user using external provider
            if ($user_is_valid_for_external_provider AND !empty($external_user_id) AND !empty($external_provider_id) AND $userId) {
                $this->save_external_provider_data($external_provider_id, $external_user_id, $userId);
            }

            $success = TRUE;
            $account = array();
            $account['owner_id'] = $userId;
            $account['stripe_auth'] = '';
            $account['status'] = 'ENABLED';
            if (class_exists('Model_Event')) {
                Model_Event::accountDetailsSave($account);
            }

            if ($send_verification_email) {
                $redirect = isset($data['redirect']) ? $data['redirect'] : '';
                $redirect .= (parse_url($redirect, PHP_URL_QUERY) ? '&' : '?').'registered=success';
                Model_Users::send_user_email_verification($userId, $redirect);
            }
        }

        return array('success' => $success, 'error' => $error, 'id' => $userId, 'data' => $newUser);
    }

    public function validate_user($data)
    {
        $success = true;
        $error = '';
        $user_id = null;

        $user = DB::select('*')
            ->from(self::MAIN_TABLE)
            ->where('email', '=', $data['email'])
            ->and_where('validation_code', '=', $data['validate'])
            ->execute()
            ->current();
        if ($user) {
            $user_id = $user['id'];
            Auth::instance()->instance();
            DB::update(self::MAIN_TABLE)
                ->set(
                    array(
                        'can_login' => 1,
                        'email_verified' => 1,
                        'password' => Auth::instance()->hash($data['password']),
                        'registered' => date::now(),
                        'deleted' => 0
                    )
                )
                ->where('id', '=', $user_id)
                ->execute();

            $invitation = ORM::factory('Contacts3_Invitation')->where('invited_email', '=', $data['email'])->find();
            // Link the invited contact to the user
            $invitation->invited_contact->set('linked_user_id', $user_id)->save();
            // Flag the invitation as accepted
            $invitation->set('status', 'Accepted')->save();
        } else {
            $success = false;
            $error = __('User validation was failed');
        }
        return array('success' => $success, 'error' => $error, 'id' => $user_id, 'data' => $user);
    }

    public static function search($params = array())
    {
        $columns   = array();
        $columns[] = 'users.id';
        $columns[] = DB::expr("CONCAT_WS(' ', users.name, users.surname, users.email)");
        if(empty($params['role_id'])){
            $columns[] = 'roles.role';
        }
        $columns[] = "users.registered";
        $columns[] = DB::expr("FROM_UNIXTIME(users.last_login, '%Y-%m-%d %H:%i:%s')");
        $columns[] = 'users.register_source';
        $columns[] = 'users.name';
        $columns[] = 'users.surname';

        $select = DB::select(DB::expr('SQL_CALC_FOUND_ROWS users.id'), 'users.email',
            "users.registered",
            DB::expr("FROM_UNIXTIME(users.last_login, '%Y-%m-%d %H:%i:%s') as `last_login`"), 'users.register_source',
            DB::expr('CONCAT_WS(" ", users.name, users.surname) as `full_contact_name`'),
            array('roles.role', 'role'))
            ->from(array(self::MAIN_TABLE, 'users'))
                ->join(array(Model_Roles::MAIN_TABLE, 'roles'), 'left')
                    ->on('users.role_id', '=', 'roles.id');
        if (@$params['role_id']) {
            $select->and_where('users.role_id', '=', $params['role_id']);
        }
        if (@$params['name']) {
            $select->and_where('users.name', 'like', '%' . $params['name'] . '%');
        }
        if (@$params['surname']) {
            $select->and_where('users.surname', 'like', '%' . $params['surname'] . '%');
        }

        // Global search
        if (isset($params['sSearch']) AND $params['sSearch'] != '') {
            $select->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($params['bSearchable_' . $i]) AND $params['bSearchable_' . $i] == "true" AND $columns[$i] != '') {
                    $select->or_where($columns[$i], 'like', '%' . $params['sSearch'] . '%');
                }
            }
            $select->and_where_close();
        }
        // Individual column search
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($params['bSearchable_' . $i]) AND $params['bSearchable_' . $i] == "true" AND $params['sSearch_' . $i] != '') {
                $params['sSearch_' . $i] = preg_replace('/\s+/', '%',
                    $params['sSearch_' . $i]); //replace spaces with %
                $select->and_where($columns[$i], 'like', '%' . $params['sSearch_' . $i] . '%');
            }
        }
        
        if (empty($params['iDisplayLength']) || $params['iDisplayLength'] == -1 || $params['iDisplayLength'] > 100) {
            $params['iDisplayLength'] = 10;
        }

        // Limit. Only show the number of records for this paginated page
        if (isset($params['iDisplayLength'])) {
            $select->limit(intval($params['iDisplayLength']));
            if (isset($params['iDisplayStart'])) {
                $select->offset(intval($params['iDisplayStart']));
            }
        }
        if (@$params['email']) {
            $select->and_where('users.email', 'like', '%' . $params['email'] . '%');
        }
        if (isset($params['iSortCol_0']) AND is_numeric($params['iSortCol_0'])) {
            for ($i = 0; $i < $params['iSortingCols']; $i++) {
                if ($columns[$params['iSortCol_' . $i]] != '') {
                    $select->order_by($columns[$params['iSortCol_' . $i]], $params['sSortDir_' . $i]);
                }
            }
        }
    
        
        
        $select->where('users.deleted', '=', 0);
        $select->order_by('users.last_login', 'desc')->order_by('users.id');

        $users = $select->execute()->as_array();
        return $users;
    }

    public static function search_datatable($params = array())
    {
        $users = self::search($params);

        $output = array();
        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords'] = count($users); // displayed results
        $output['aaData'] = array();

        foreach ($users as $user) {
            $anchor_link = '<a href="/admin/usermanagement/user/' . $user['id'] . '" class="edit-link">';
            
            $row   = array();
            $row[] = $anchor_link . $user['id'] . '</a>';
            // full name with email of user beneath it
            $row[] = $anchor_link . $user['full_contact_name'] .
                '<br><span style="font-size: 11px;">' . $user['email'] . '</a>';
            if(empty($params['role_id'])){
                $row[] = $anchor_link . $user['role'] . '</a>';
            }

            $row[] = $anchor_link . IbHelpers::relative_time_with_tooltip($user['registered']) . '</a>';
            $loginlogs = ORM::factory('Loginlogs')->where('time', '>',
                DB::expr("UNIX_TIMESTAMP('{$user['last_login']}')"))->where('email', '=', $user['email'])
                ->where('success', '=', DB::expr('0'))->find_all();
            $loginlogs_amount = ($loginlogs->count() > 0) ? $loginlogs->count() . ' failed attempts' : '';
            $row[] = $anchor_link . ($user['last_login']) ? IbHelpers::relative_time_with_tooltip($user['last_login']) . "</br>{$loginlogs_amount}" : __("Never Logged In.") . '</a>';
            $row[] = $anchor_link . $user['register_source'] . '</a>';

            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($params['sEcho']);

        return $output;
    }

    public static function search_datatable_old($params = array())
    {
        $isPruductsLoaded = Model_Plugin::is_loaded('products');
        $users = self::search($params);

        $output = array();
        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords'] = count($users); // displayed results
        $output['aaData'] = array();

        foreach ($users as $user) {
            $row   = array();
            $row[] = '<a href="' . URL::Site('admin/users/edit/' . $user['id']) . '">' . $user['id'] . '</a>';
            $row[] = '<a href="' . URL::Site('admin/users/edit/' . $user['id']) . '">' . $user['email'] . '</a>';
            $row[] = '<a href="' . URL::Site('admin/users/edit/' . $user['id']) . '">' . $user['role'] . '</a>';
            $row[] = '<a href="' . URL::Site('admin/users/edit/' . $user['id']) . '">' . Date::less_fuzzy_span(strtotime($user['registered'])) . '</a>';
            $row[] = '<a href="' . URL::Site('admin/users/edit/' . $user['id']) . '">' . $user['can_login'] . '</a>';
            $row[] = '<a href="' . URL::Site('admin/users/edit/' . $user['id']) . '">' . Date::less_fuzzy_span($user['last_login']) . '</a>';
            $row[] = '<a href="' . URL::Site('admin/users/edit/' . $user['id']) . '">' . ($user['email_verified'] == 1 ? 'Yes' : 'No') . '</a>';
            $row[] = '<a class="resend-verify" data-user-id="' . $user['id'] . '">resend</a>';
            if($isPruductsLoaded){
                $row[] = '<a href="' . URL::Site('admin/users/edit/' . $user['id']) . '">' . ($user['discount_format_id'] ? 'Yes' : 'No') . '</a>';
            }
            $row[] = '<a class="reset-password" data-user-id="' . $user['id'] . '">reset password</a>';
            if(Auth::instance()->has_access('login_as')) {
                $row[] = '<a href="/admin/users/login_as?user_id=' . $user['id'] . '" class="login-as-user" data-user-id="' . $user['id'] . '">login as</a>';
            }

            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($params['sEcho']);

        return $output;
    }

    public static function invite($post)
    {
        $send = @$post['send'];
        if (@$post['emails'] && !@$post['resend']) {
            $emails = preg_split('/[\,\s]+/', $post['emails']);
        } elseif(@$post['send-login-invite-email'] && !@$post['resend']) {
            $emails = preg_split('/[\,\s]+/', $post['send-login-invite-email']);
            $send = true;
        } else{
            $emails = array();
        }
        $message = $post['message'];
        $role_id = @$post['role_id'];

        $mm = new Model_Messaging();
        $salt = Controller_Frontend_Users::SALT;

        if (@$post['resend']) {
            $u = new Model_Users();
            $user = $u->get_user($post['resend'], null);
            $hash = md5($user[ 'email' ] . $salt);
            $link = URL::site('/admin/login/register?' . http_build_query(array('email' => $user['email'], 'validate' => $hash)));

            $mm->send_template(
                'login-invitation-email',
                $message,
                null,
                [['target_type' => 'EMAIL', 'target' => $user[ 'email' ]]],
                ['link' => $link]
            );

            $result = array('ignored_emails' => array(), 'successful_emails' => array($user[ 'email' ]));
            return $result;
        }
        try {
            $result = array('ignored_emails' => array(), 'successful_emails' => array());
            Database::instance()->begin();

            $new_user = array();
            $new_user['name'] = '';
            $new_user['surname'] = '';
            $new_user['email'] = '';
            $new_user['password'] = '!';
            $new_user['email_verified'] = 0;
            $new_user['can_login'] = 0;
            $new_user['signup_newsletter'] = 0;
            $new_user['role_id'] = $role_id;
            $new_user['validation_code'] = '';
            if (Settings::instance()->get('two_step_authorization')) {
                $sms_resource = Model_Resources::get_by_alias('user_auth_2step_sms');
                $email_resource = Model_Resources::get_by_alias('user_auth_2step_email');
                if (Model_Roles::has_permission($new_user['role_id'], $sms_resource['id'])) {
                    $new_user['two_step_auth'] = 'SMS';
                } elseif(Model_Roles::has_permission($new_user['role_id'], $email_resource['id'])) {
                    $new_user['two_step_auth'] = 'Email';
                } else {
                    $new_user['two_step_auth'] = 'None';
                }
            } else {
                $newUser['two_step_auth'] = 'None';
            }

            foreach ($emails as $email) {
                $contact = Model_Contacts3::get_existing_contact_by_email_and_mobile($email, '') ?? false;
                $contact_object = new Model_Contacts3($contact['id']);
                $notifications = $contact_object->get_contact_notifications();
                foreach($notifications as $notification) {
                    if ($notification['type_stub'] == 'mobile') {
                        $new_user['dial_code_mobile'] = $notification['dial_code'];
                        $new_user['country_dial_code_mobile'] = $notification['country_dial_code'];
                        $new_user['mobile'] = $notification['value'];
                    }
                }
                if ($contact) {
                    $new_user['name'] = $contact['first_name'];
                    $new_user['surname'] = $contact['last_name'];
                } else {
                    $new_user['name'] = $new_user['surname'] = '';
                }
                $new_user['email'] = $email;
                $hash = md5($new_user['email'] . $salt);
                $new_user['validation_code'] = $hash;
                $email_exists = DB::select('*')
                    ->from(self::MAIN_TABLE)
                    ->where('email', '=', $email)
                    ->execute()
                    ->current();
                $user_id = null;
                if ($email_exists) {
                    $result['ignored_emails'][] = $email;
                } else {
                    $inserted = DB::insert(self::MAIN_TABLE)
                        ->values($new_user)
                        ->execute();
                    $user_id = $inserted[0];
                    $result['successful_emails'][] = $email;
                    $role = '';
                    $roles = new Model_Roles();
                    if ($roles->get_name($role_id) == 'Teacher') {
                        $role = 'Teacher';
                    } else if ($roles->get_name($role_id) == 'Student') {
                        $role = 'Student';
                    } else if ($roles->get_name($role_id) == 'Mature Student') {
                        $role = 'Mature Student';
                    }
                    // Add the user to the invitation DB
                    Model_Contacts3::get_linked_contact_to_user(Auth::instance()->get_user()['id']);
                    $contact_inserted = DB::insert(Model_Contacts3::INVITATIONS_TABLE)
                        ->values(
                            array(
                                'invited_by_contact_id' => Model_Contacts3::get_linked_contact_to_user(Auth::instance()->get_user()['id'])['id'] ?? '-1',
                                'invited_email' => $email,
                                'invited_contact_id' => $contact['id'],
                                'status' => 'Wait',
                            )
                        )
                        ->execute();

                    foreach (Controller_Admin_Login::$run_after_external_register as $call) {
                        call_user_func(
                            $call,
                            array(
                                'email' => $email,
                                'role_id' => $role_id,
                                'role' => $role
                            ),
                            array('id' => $user_id, 'success' => true)
                        );
                    }
                }


                if ($send && $user_id) {
                    $link = URL::site('/admin/login/register?' . http_build_query(array('email' => $email, 'validate' => $hash)));
                    $rmessage = str_replace('@link@', $link, $message);
                    $mm->send(
                        'email',
                        null,
                        @Settings::instance()->get('default_email_sender'),
                        array(array('target_type' => 'EMAIL', 'target' => $email)),
                        $rmessage,
                        'User Registration'
                    );
                }
            }

            Database::instance()->commit();
            return $result;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function create_contacts_if_not_linked($user)
    {
        if (!is_array($user)) {
            $user = self::get_user($user);
        }
        $result = array(

        );

        $contact = Model_Contacts::get_linked_contact_to_user($user['id']);
        if (!$contact) {
            $contact = DB::select('contacts.*')
                ->from(array(Model_Contacts::TABLE_CONTACT, 'contacts'))
                ->join(array(Model_Contacts::TABLE_PERMISSION_LIMIT, 'tpl'), 'inner')
                    ->on('contacts.id', '=', 'tpl.contact_id')
                ->where('contacts.deleted', '=', 0)
                ->and_where('tpl.user_id', '=', $user['id'])
                ->execute()
                ->current();
            if ($contact) {
                DB::update(Model_Contacts::TABLE_CONTACT)
                    ->set(array('linked_user_id' => $user['id']))
                    ->where('id', '=', $contact['id'])
                    ->execute();
            }
        }
        if (!$contact && false) {
            $contact = new Model_Contacts();
            $contact->set_first_name($user['name'] ?: ' ');
            $contact->set_last_name($user['surname'] ?: ' ');
            $contact->set_email($user['email']);
            $contact->test_existing_email = false;
            $contact->set_linked_user_id($user['id']);
            $contact->set_permissions(array($user['id']));
            $contact->set_mailing_list('default');
            if ($user['role'] == 'Administrator') {
                $contact->set_mailing_list('Admin');
            } else if ($user['role'] == 'External User') {
                $contact->set_mailing_list('Customer');
            } else if ($user['role'] == 'Parent/Guardian') {
                $contact->set_mailing_list('Parent/Guardian');
            } else if ($user['role'] == 'Student') {
                $contact->set_mailing_list('Student');
            }
            $contact->save();
            $result['contact'] = DB::select('contacts.*')
                ->from(array(Model_Contacts::TABLE_CONTACT, 'contacts'))
                ->where('id', '=', $contact->get_id())
                ->execute()
                ->current();
        } else {
            $result['contact'] = $contact;
        }

        $contact3 = Model_Contacts3::get_linked_contact_to_user($user['id']);
        if (!$contact3) {
            $contact3 = Model_Contacts3::get_contact_ids_by_user($user['id']);
            if (isset($contact3[0])) {
                $contact3 = $contact3[0];
                DB::update(Model_Contacts3::CONTACTS_TABLE)
                    ->set(array('linked_user_id' => $user['id']))
                    ->where('id', '=', $contact3['id'])
                    ->execute();
            } else {
                $contact3 = null;
            }
        }
        if (!$contact3) {
            $contact3_id = Model_Contacts3::create_for_user($user);

            $result['contact3'] = DB::select('contacts.*')
                ->from(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'))
                ->where('id', '=', $contact3_id)
                ->execute()
                ->current();
        } else {
            $result['contact3'] = $contact3;
        }
    }

    public static function delete_data($user_id)
    {
        $delete_id = uniqid("deleted - ");
        DB::update(self::MAIN_TABLE)
            ->set(
                array(
                    'email' => $delete_id,
                    'deleted' => 1,
                    'can_login' => 0,
                    'name' => '',
                    'surname' => '',
                    'phone' => '',
                    'mobile' => '',
                    'company' => '',
                    'address' => '',
                    'address_2' => '',
                    'address_3' => '',
                    'eircode' => '',
                    'password' => '!',
                    'date_modified' => date::now()
                )
            )
            ->where('id', '=', $user_id)
            ->execute();
        foreach (self::$on_delete_data_handlers as $delete_data_handler) {
            call_user_func($delete_data_handler, $user_id);
        }
    }

    public static function download_data($user_id)
    {
        $user = Model_Users::get_user($user_id);
        $data = array();
        $data['user'] = array (
            'email' => $user['email'],
            'first_name' => $user['name'],
            'last_name' => $user['surname'],
            'phone' => $user['phone'],
            'mobile' => $user['mobile'],
            'address' => $user['address'],
            'address_2' => $user['address_2'],
            'address_3' => $user['address_3'],
            'timezone' => $user['timezone']
        );

        foreach (self::$on_download_data_handlers as $download_data_handler) {
            $d_data = call_user_func($download_data_handler, $user_id);
            $data = array_merge($d_data, $data);
        }

        return $data;
    }

    public static function two_step_auth_code_create($user_id)
    {
        $timeout_seconds = Settings::instance()->get('engine_two_step_auth_timeout_seconds');
        $data = array(
            'user_id' => $user_id,
            'created' => date::now(),
            'code' => mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9),
            'valid' => 1,
            'expires' => date('Y-m-d H:i:s', time() + $timeout_seconds)
        );
        DB::update(self::TWO_STEP_AUTH_TABLE)
            ->set(array('valid' => 0))
            ->where('user_id', '=', $user_id)
            ->and_where('valid', '=', 1)
            ->execute();
        DB::insert(self::TWO_STEP_AUTH_TABLE)->values($data)->execute();
        return $data['code'];
    }

    public static function two_step_auth_code_send($user, $code)
    {
        $mm = new Model_Messaging();
        if ($user['two_step_auth'] == 'SMS') {
            $template = 'two-step-auth-sms';
        } else if ($user['two_step_auth'] == 'Email') {
            $template = 'two-step-auth-email';
        } else {
            return false;
        }
        return $mm->send_template($template, null, null, array(array('target_type' => 'CMS_USER', 'target' => $user['id'])), array('code' => $code));
    }

    public static function two_step_auth_check($user_id, $code)
    {
        $code = DB::select('*')
            ->from(self::TWO_STEP_AUTH_TABLE)
            ->where('user_id', '=', $user_id)
            ->and_where('code', '=', $code)
            ->and_where('valid', '=', 1)
            ->and_where('expires', '>=', date::now())
            ->execute()
            ->current();

        if ($code) {
            DB::update(self::TWO_STEP_AUTH_TABLE)->set(array('valid' => 0))->where('id', '=', $code['id'])->execute();
            return true;
        } else {
            return false;
        }
    }
}
