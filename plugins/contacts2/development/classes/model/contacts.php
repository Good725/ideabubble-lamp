<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Contacts extends Model
{
    // Tables
    const TABLE_CONTACT = 'plugin_contacts_contact';
    const TABLE_MAILING_LIST = 'plugin_contacts_mailing_list';
    const TABLE_RELATIONS = 'plugin_contacts_relations';
    const TABLE_HAS_RELATIONS = 'plugin_contacts_has_relations';
    const TABLE_PERMISSION_LIMIT = 'plugin_contacts_users_has_permission';
    const TABLE_COMM_TYPES = 'plugin_contacts_communication_types';
    const TABLE_COMMS = 'plugin_contacts_communications';
    const TABLE_PREF_TYPES = 'plugin_contacts_preference_types';
    const TABLE_PREFS = 'plugin_contacts_preferences';

    // Fields
    private $id;
    private $title;
    private $first_name;
    private $last_name;
    private $email;
    private $mailing_list;
    private $phone;
    private $mobile;
    private $address1;
    private $address2;
    private $address3;
    private $address4;
    private $country_id;
    private $postcode;
    private $coordinates;
    private $dob;
    private $notes;
    private $publish;
    private $last_modification;
    private $relations = array();
    private $of_relations = array();
    private $permissions = array();
    private $communications = array();
    private $preferences = array();
    private $linked_user_id = null;
    public $next_id_for_insert = null;

    public $test_existing_email = true;
    protected static $extentions = array();
    //
    // PUBLIC FUNCTIONS
    //

    /**
     * @param int $id Contact identifier.
     */
    public function __construct($id = NULL)
    {
        if (!$id || !$this->load($id)) {
            $this->id = NULL;
            $this->title = NULL;
			$this->first_name = NULL;
            $this->last_name = NULL;
            $this->email = NULL;
            $this->mailing_list = NULL;
            $this->phone = NULL;
            $this->mobile = NULL;
            $this->notes = NULL;
            $this->publish = NULL;
            $this->last_modification = NULL;
        }
    }

    /**
     * Return an array(id, first_name, last_name, email, mailing_list, phone, mobile, notes, publish, last_modification) with the client's details.
     * @return array The array.
     */
    public function get_details()
    {
        $r = array
        (
            'id' => $this->id,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mailing_list' => $this->mailing_list,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'address3' => $this->address3,
            'address4' => $this->address4,
            'country_id' => $this->country_id,
            'postcode' => $this->postcode,
            'coordinates' => $this->coordinates,
            'notes' => $this->notes,
            'publish' => $this->publish,
            'last_modification' => $this->last_modification,
            'dob' => $this->dob,
            'relations' => $this->relations,
            'of_relations' => $this->of_relations,
            'communications' => $this->communications,
            'preferences' => $this->preferences,
            'linked_user_id' => $this->linked_user_id
        );

        return $r;
    }

    public function get_dob()
    {
        return $this->dob;
    }

    /**
     * @param string $first_name The contact's first name.
     */
    public function set_title($title)
    {
        $this->title = trim($title);
    }

    /**
     * @param string $first_name The contact's first name.
     */
    public function set_first_name($first_name)
    {
        $this->first_name = trim($first_name);
    }

    /**
     * @param string $last_name The contact's last name.
     */
    public function set_last_name($last_name)
    {
        $this->last_name = trim($last_name);
    }

    /**
     * @param string $email The contact's email.
     */
    public function set_email($email)
    {
        $this->email = trim($email);

        $not_found = true;
        foreach ($this->communications as $communication) {
            if ($communication['value'] == $email) {
                $not_found = false;
                break;
            }
        }

        if ($not_found) {
            $this->communications[] = array(
                'value' => $email,
                'type' => 'Email'
            );
        }
    }

    /**
     * @param string $mailing_list The contact's mailing list.
     */
    public function set_mailing_list($mailing_list)
    {
        $this->mailing_list = trim($mailing_list);
    }

    /**
     * @param string $phone The contact's phone.
     */
    public function set_phone($phone)
    {
        $this->phone = trim($phone);
    }

    /**
     * @param string $mobile The contact's mobile.
     */
    public function set_mobile($mobile)
    {
        $this->mobile = trim($mobile);

        $not_found = true;
        foreach ($this->communications as $communication) {
            if ($communication['value'] == $mobile) {
                $not_found = false;
                break;
            }
        }

        if ($not_found) {
            $this->communications[] = array(
                'value' => $mobile,
                'type' => 'Mobile'
            );
        }
    }

    public function set_linked_user_id($linked_user_id)
    {
        $this->linked_user_id = $linked_user_id;
    }

    public function set_address1($addr)
    {
        $this->address1 = trim($addr);
    }

    public function set_address2($addr)
    {
        $this->address2 = trim($addr);
    }

    public function set_address3($addr)
    {
        $this->address3 = trim($addr);
    }

    public function set_address4($addr)
    {
        $this->address4 = trim($addr);
    }

    public function set_country_id($addr)
    {
        $this->country_id = trim($addr);
    }

    public function set_postcode($addr)
    {
        $this->postcode = trim($addr);
    }

    public function set_coordinates($addr)
    {
        $this->coordinates = trim($addr);
    }


    /**
     * @param string $notes The contact's notes.
     */
    public function set_notes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @param int $publish The contact's publish option.
     */
    public function set_publish($publish)
    {
        $this->publish = $publish;
    }

    public function set_dob($dob)
    {
        $this->dob = $dob;
    }

    public function set_relations($relations)
    {
        $this->relations = array();
        if (!empty($relations))
        foreach ($relations['contact_2_id'] as $i => $contact_2_id) {
            $this->relations[] = array('contact_2_id' => $contact_2_id, 'relation_id' => $relations['relation_id'][$i]);
        }
    }

    public function set_permissions($user_ids)
    {
        $this->permissions = array();
        foreach ($user_ids as $i => $user_id) {
            $this->permissions[] = array('user_id' => $user_id);
        }
    }

    public function set_communications($communications)
    {
        foreach ($communications as $communication) {
            if ($communication['type_id'] == 1 && $this->email == '') {
                $this->email = $communication['value'];
            }
            if ($communication['type_id'] == 2 && $this->mobile == '') {
                $this->mobile = $communication['value'];
            }
        }
        $this->communications = $communications;
    }

    public function set_preferences($preferences)
    {
        $this->preferences = $preferences;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_address1()
    {
        return $this->address1;
    }

    public function get_address2()
    {
        return $this->address2;
    }

    public function get_address3()
    {
        return $this->address3;
    }

    public function get_address4()
    {
        return $this->address4;
    }

    public function get_country_id()
    {
        return $this->country_id;
    }

    public function get_postcode()
    {
        return $this->postcode;
    }


    /**
     * @return bool TRUE if the details are saved. Otherwise, FALSE.
     */
    public function save($arg = NULL)
    {
        $ok = FALSE;
        $db = Database::instance();

        if (!is_null($db) AND $db->begin()) {
            try {
                $insert_array = $this->build_insert_array();
                if ($this->id == NULL) {
                    //Check if contact already exist in the same mailing list
                    if (!$this->test_existing_email || !$this->sql_contact_exist($insert_array)) {
                        // Add a new contact
                        $ok = $this->sql_add_contact($insert_array);
                        $this->id = $ok;
                    } else {
                        //If the contact exist it will return true, will be transparent
                        $ok = true;
                    }

                } else {
                    // Update an existing contact
                    $ok = $this->sql_update_contact($this->id, $insert_array);
                }
                if($this->mailing_list == 'trainers' AND method_exists('Model_Schedules','ajax_create_events'))
                {
                    $trainer = new Model_Trainers();
                    $trainer->set($this->get_details());
                    $ok = $trainer->save();
                }

				if ($arg != 'contact_only')
				{
					DB::delete(self::TABLE_HAS_RELATIONS)->where('contact_1_id', '=', $this->id)->execute();
					foreach ($this->relations as $relation) {
                        unset($relation['contact_2']);
						$relation['contact_1_id'] = $this->id;
						DB::insert(self::TABLE_HAS_RELATIONS)->values($relation)->execute();
					}
				}

                DB::delete(self::TABLE_PERMISSION_LIMIT)->where('contact_id', '=', $this->id)->execute();
                foreach ($this->permissions as $permission) {
                    $permission['contact_id'] = $this->id;
                    DB::insert(self::TABLE_PERMISSION_LIMIT)->values($permission)->execute();
                }
                if (Auth::instance()->has_access('contacts2_view_limited', false)) {
                    $user = Auth::instance()->get_user();
                    $permission = array('user_id' => $user['id'], 'contact_id' => $this->id);
                    DB::insert(self::TABLE_PERMISSION_LIMIT)->values($permission)->execute();
                }

                $this->save_comms();

                $this->save_prefs();


                // If no errors, commit the transaction. Otherwise, throw an exception.
                if ($ok === FALSE)
                    throw new Exception();
                else {
                    $ok = $db->commit();
                }
            } catch (Exception $e) {
                // Rollback the transaction
                $db->rollback();
                throw $e;
            }
        }

        return $ok;
    }

    private function save_comms()
    {
        $comm_ids = array();
        foreach ($this->communications as $communication) {
            if ($communication['value'] != '') {
                if (is_numeric(@$communication['id'])) {
                    $comm_ids[] = $communication['id'];
                    DB::update(self::TABLE_COMMS)
                        ->set(array(
                            'value' => $communication['value']
                        ))
                        ->where('id', '=', $communication['id'])
                        ->execute();
                } else {
                    if (isset($communication['type'])) {
                        $communication['type_id'] = $this->get_communication_type_id($communication['type']);
                    }
                    $comm = DB::insert(self::TABLE_COMMS)
                        ->values(array(
                            'contact_id' => $this->id,
                            'type_id' => $communication['type_id'],
                            'value' => $communication['value']
                        ))
                        ->execute();
                    $comm_ids[] = $comm[0];
                }
            }
        }
        if ($comm_ids) {
            DB::delete(self::TABLE_COMMS)
                ->where('contact_id', '=', $this->id)
                ->and_where('id', 'not in', $comm_ids)
                ->execute();
        } else {
            DB::delete(self::TABLE_COMMS)
                ->where('contact_id', '=', $this->id)
                ->execute();
        }
    }

    private function save_prefs()
    {
        DB::delete(self::TABLE_PREFS)->where('contact_id', '=', $this->id)->execute();
        foreach ($this->preferences as $type_id => $value) {
            if (!is_numeric($type_id)) {
                $type_id = DB::select()->from(self::TABLE_PREF_TYPES)->where('name', '=', $type_id)->execute()->get('id');
            }
            if (is_array($value) && array_key_exists('type', $value)) $type_id = DB::select()->from(self::TABLE_PREF_TYPES)->where('name', '=', $value['type'])->execute()->get('id');
			if (is_array($value) && array_key_exists('type_id', $value)) $type_id = $value['type_id'];
			if (is_array($value) && array_key_exists('value',   $value)) $value   = $value['value'];

            DB::insert(self::TABLE_PREFS)
                ->values(array(
                    'contact_id' => $this->id,
                    'type_id' => $type_id,
                    'value' => $value
                ))
                ->execute();
        }
    }

	public function add_pref($pref, $value)
	{
        if (is_numeric($pref)) {
            $preference_id = $pref;
        } else {
            $preference_id = DB::select()->from(self::TABLE_PREF_TYPES)->where('name', '=', $pref)->execute()->get('id');
        }

		if ($preference_id)
		{
			DB::delete(self::TABLE_PREFS)
				->where('contact_id', '=', $this->id)
				->and_where('type_id', '=', $preference_id)
				->execute();

			DB::insert(self::TABLE_PREFS)
				->values(array(
					'contact_id' => $this->id,
					'type_id' => $preference_id,
					'value' => $value
				))
				->execute();
		}
	}

    //
    // STATIC/SERVICE FUNCTIONS (DO NOT ABUSE OF THEM)
    //

    /**
     * @return array An array with the names of the mailing lists.
     */
    public static function get_mailing_list_all()
    {
        $list = array();

        // Execute the query
        $r = DB::select('name')
            ->from(Model_Contacts::TABLE_MAILING_LIST);

        if (Auth::instance()->has_access('contacts2_index_limited')) {
            $r->where('name', 'in', array('Parent/Guardian', 'Student'));
        }

        $r = $r->order_by('id')
            ->execute()
            ->as_array();

        // Add the lists to the array
        for ($i = 0; $i < count($r); $i++) {
            array_push($list, $r[$i]['name']);
        }

        return $list;
    }

	public static function get_mailing_lists($args = array())
	{
		$q = DB::select()
			->from(self::TABLE_MAILING_LIST)
			->order_by('date_modified')
			->where('deleted', '=', 0);

        if (isset($args['id'])) {
            $q->where('id', '=', $args['id']);
        }

        if (isset($args['name'])) {
            $q->where('name', '=', $args['name']);
        }

        // If the name or ID is specified, only return one row
		if (isset($args['id']) || isset($args['name']))
		{
			$result = $q->execute()->current();
			if ( ! $result)
			{
				// If no results were found, get the column names, so a row of empty column-value pairings can be returned
				$result = Database::instance()->list_columns(self::TABLE_MAILING_LIST);
                foreach ($result as $key => $value)
                {
                    $result[$key] = '';
                }
			}
		}
		else
		{
			// No ID or name is selected, return multiple records in an array
			$result = $q->execute()->as_array();
		}

		return $result;
	}

	public static function save_mailing_list($data)
	{
		$return = array(
            'data'          => null,
            'success'       => true,
            'error_message' => ''
        );

		try
		{
            // See if the data is valid. Don't continue, if invalid.
            $validation = self::validate_mailing_list($data);
            if ( ! $validation['success'])
            {
                return $validation;
            }

            /* Save a record in the list table  */
			$user = Auth::instance()->get_user();
			$save_data['id']            = isset($data['id']) ? $data['id'] : '';
			$save_data['date_modified'] = date('Y-m-d H:i:s');
			$save_data['modified_by']   = $user['id'];

			if (isset($data['name']))    $save_data['name']    = $data['name'];
			if (isset($data['summary'])) $save_data['summary'] = $data['summary'];

			if ( ! empty($data['id']))
			{
				DB::update(self::TABLE_MAILING_LIST)->set($save_data)->where('id', '=', $data['id'])->execute();
				$id = $data['id'];
			}
			else
			{
				$save_data['date_created'] = $save_data['date_modified'];
				$save_data['created_by']   = $save_data['modified_by'];
				$saved = DB::insert(self::TABLE_MAILING_LIST)->values($save_data)->execute();
				$id = isset($saved[0]) ? $saved[0] : false;
			}

            /** Update contacts table to put the contacts in this list **/
            // Remove association from contacts, which were previously on the list, but have now been removed
            $q = DB::update(self::TABLE_CONTACT)
                ->set(array('mailing_list' => '1')) // a contact must have a mailing list, so move removed ones to the default list
                ->where('mailing_list', '=', $id);

            if ( ! empty($data['contact_ids']))
            {
                $q->and_where('id', 'not in', $data['contact_ids']);
            }

            $q->execute();

            // Add association to contacts, which have been added to the list
            if ( ! empty($data['contact_ids']))
            {
                DB::update(self::TABLE_CONTACT)
                    ->set(array('mailing_list' => $id))
                    ->where('mailing_list', '!=', $id)
                    ->and_where('id', 'in', $data['contact_ids'])
                    ->execute();
            }

            if ($id) {
                $return['data'] = self::get_mailing_lists(array('id' => $id));
            }
            else {
                Log::instance()->add(Log::ERROR, "Error saving mailing list. ID value not returned after saving.");
                $return['success']   = false;
                $return['error_message'] = __('Error retrieving the mailing list data. This error has been logged. If this problem continues, please contact the administration.');
            }

		}
		catch (Exception $e)
		{
            Log::instance()->add(Log::ERROR, "Error saving mailing list.\n".$e->getMessage()."n".$e->getTraceAsString());
            $return['success']   = false;
            $return['error_message'] = __('Error saving the mailing list. This error has been logged. If this problem continues, please contact the administration.');
		}

		return $return;
	}

    public static function validate_mailing_list($data)
    {
        $return['success']       = true;
        $return['error_message'] = '';

        if (empty($data['name']))
        {
            $return['success'] = false;
            $return['error_message'] = __('You have not entered a name for this mailing list.');
            return $return;
        }

        /* If this is a new list, see if its name is already in use */
        if (empty($data['id']))
        {
            $name   = isset($data['name']) ? $data['name'] : '';
            $exists = self::get_mailing_lists(array('name' => $name));

            if ( ! empty($exists['id']))
            {
                $return['success']       = false;
                $return['error_message'] = __('The name "$1" is already used by another mailing list', array('$1' => $name));
                return $return;
            }
        }

        return $return;
    }

    /**
     * @return array An array of arrays(id, first_name, last_name, email, mailing_list, phone, mobile, notes, publish, last_modification) with all the contact's details.
     */
    public static function get_contact_all($order_by = 'id',$direction = 'ASC', $check_permission_user_id = null)
    {
        $q = DB::select('t1.id', 'first_name', 'last_name', 'email', array('t2.name', 'mailing_list'), 'phone', 'mobile', 'notes', 't1.publish', 'last_modification', 'linked_user_id')
            ->from(array(Model_Contacts::TABLE_CONTACT, 't1'))
            ->join(array(Model_Contacts::TABLE_MAILING_LIST, 't2'), 'INNER')
            ->on('t1.mailing_list', '=', 't2.id')
            ->order_by($order_by,$direction);
        if (is_numeric($check_permission_user_id)) {
            $filter1 = DB::select('contact_id')
                ->from(self::TABLE_PERMISSION_LIMIT)
                ->where('user_id', '=', $check_permission_user_id);
            $filter2 = DB::select('contact_2_id')
                ->from(array(self::TABLE_PERMISSION_LIMIT, 'permissions'))
                    ->join(array(self::TABLE_HAS_RELATIONS, 'related1'), 'inner')->on('permissions.contact_id', '=', 'related1.contact_1_id')
                ->where('permissions.user_id', '=', $check_permission_user_id);
            $filter3 = DB::select('contact_1_id')
                ->from(array(self::TABLE_PERMISSION_LIMIT, 'permissions'))
                    ->join(array(self::TABLE_HAS_RELATIONS, 'related1'), 'inner')->on('permissions.contact_id', '=', 'related1.contact_2_id')
                ->where('permissions.user_id', '=', $check_permission_user_id);
            $q->and_where_open();
                $q->or_where('t1.id', 'in', $filter1);
                $q->or_where('t1.id', 'in', $filter2);
                $q->or_where('t1.id', 'in', $filter3);
            $q->and_where_close();
        }

        $r = $q->execute()->as_array();
        return $r;
    }

    /**
     * @param int $id Contact identifier.
     * @return bool TRUE if the function success. Otherwise, FALSE.
     */
    public static function delete_contact($id)
    {
        $ok = TRUE;

        try {
            $r = DB::delete(Model_Contacts::TABLE_CONTACT)
                ->where('id', '=', $id)
                ->execute();

            $ok = ($r == 1);
        } catch (Exception $e) {
            $ok = FALSE;
        }

        return $ok;
    }

    /**
     * @param int $id Contact identifier.
     * @return bool TRUE if the function success. Otherwise, FALSE.
     */
    public static function toggle_contact_publish($id)
    {
        $ok = TRUE;

        try {
            $r = DB::select('publish')
                ->from(Model_Contacts::TABLE_CONTACT)
                ->where('id', '=', $id)
                ->execute()
                ->as_array();

            if (count($r) == 1) {
                $publish = ($r[0]['publish'] == 1) ? 0 : 1;

                $r = DB::update(Model_Contacts::TABLE_CONTACT)
                    ->set(array('publish' => $publish))
                    ->where('id', '=', $id)
                    ->execute();

                $ok = ($r == 1);
            }
        } catch (Exception $e) {
            $ok = FALSE;
        }

        return $ok;
    }

    /**
     * @param int $id The contact identifier(s).
     * @return array The email addresses for the contacts requested.
     */
    public static function get_contact_email($id)
    {
        $r = DB::select('email')
            ->from(Model_Contacts::TABLE_CONTACT)
            ->where('id', '=', $id)
            ->execute()
            ->as_array();

        return (count($r) == 1) ? $r[0]['email'] : FALSE;
    }

    /**
     * @param array $id An array with the contact identifier(s).
     * @return array An array containing the email addresses for the contacts requested.
     */
    public static function get_contacts_email($id)
    {
        $email = array();

        if (count($id) > 0) {
            $r = DB::select('email')
                ->from(Model_Contacts::TABLE_CONTACT)
                ->where('id', 'IN', $id)
                ->execute()
                ->as_array();

            for ($i = 0; $i < count($r); $i++) {
                array_push($email, $r[$i]['email']);
            }
        }

        return $email;
    }

    /**
     * @return bool TRUE if the validation is successful. Otherwise, FALSE.
     * @throws Exception
     */
    public static function service_validate_submit()
    {
        try
		{
            $ok = isset($_POST);
			$_POST = Kohana::sanitize($_POST);

            // First name and last name are mandatory
            $first_name_ok = (strlen($_POST['first_name']) > 0);
            //$ok = ( $ok AND (strlen($_POST['last_name' ]) > 0) ); // Allowed in the DB
			if ( ! $first_name_ok)
			{
				$ok = FALSE;
				IbHelpers::set_message('You must supply a first name', 'error popup_box');
			}

            if (isset($_POST['email']) && trim($_POST['email']) != '') {
                // Check the email address
                $email_ok = (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
                if (!$email_ok) {
                    $ok = false;
                    IbHelpers::set_message('Invalid email address', 'error popup_box');
                }
            }

            // Check the mailing list
            if (strlen($_POST['new_mailing_list']) == 0)
			{
				$mailing_list = isset($_POST['mailing_list']) ? $_POST['mailing_list'] : '';
				if ($mailing_list)
				{
					// If it is a stored mailing list, it must exist
					$r = DB::select(array('COUNT("*")', 'n'))
						->from(Model_Contacts::TABLE_MAILING_LIST)
						->where('name', '=', $mailing_list)
						->execute()
						->get('n');
					$mailing_list_ok = ($r == 1);
				}
				else
				{
					$mailing_list_ok = FALSE;
				}

				if ( ! $mailing_list_ok)
				{
					$ok = FALSE;
					IbHelpers::set_message('Invalid mailing list', 'error popup_box');
				}
            }

            // Publish should be TRUE(1) or FALSE(0)
            $ok = ($ok AND ((@$_POST['publish'] == 1) OR (@$_POST['publish'] == 0)));
        } catch (Exception $e) {
            // Pass the exception to the caller. If we are here is (1) because there is a malformed request or (2) there is
            // a serious problem in the DB
            throw $e;
        }

        return $ok;
    }

    public static function get_contact($post)
    {
        if(!isset($post) OR is_null($post['id']))
        {
            return false;
        }

        try
        {
            $id = $post['id'];
            $query = DB::select('first_name','last_name','email','mailing_list','phone','mobile','notes', 'linked_user_id')->from('plugin_contacts_contact')->where('id','=',$id)->and_where('publish','=',1)->and_where('deleted','=',0)->execute()->as_array();
            return json_encode(isset($query[0]) ? $query[0] : null);
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    static function get_contacts_json($term, $user_id = null)
    {
        $query = DB::select('contacts.*')
            ->from(array('plugin_contacts_contact', 'contacts'))
            ->where(DB::expr('trim(concat_ws(\' \', `first_name`, `last_name`))'), 'LIKE', '%'.$term.'%')
            ->and_where('deleted', '=', 0);
        if ($user_id) {
            self::limited_user_access_filter($query, $user_id, 'contacts.id');
        }
        $count = clone $query;

        $return['results'] = $query
            ->select('id', array(DB::expr('trim(concat_ws(\' \', `first_name`, `last_name`))'), 'title'))
            ->order_by('title')
            ->limit(5)
            ->execute()
            ->as_array();
        $return['count']   = $count->select(array(DB::expr('count(*)'), 'count'))->execute()->get('count', 0);

        return json_encode($return);
    }

    //
    // PRIVATE FUNCTIONS
    //

    /**
     * @param int $id Contact identifier.
     * @return bool TRUE is the function success. Otherwise, FALSE.
     */
    private function load($id)
    {
        $ok = FALSE;

        // Get the contact details
        $r = $this->sql_get_contact_details($id);

        if ($r !== FALSE) {
            $ok = TRUE;

            // Store values into the class properties
            $this->id = $r[0]['id'];
            $this->title = $r[0]['title'];
            $this->first_name = $r[0]['first_name'];
            $this->last_name = $r[0]['last_name'];
            $this->email = $r[0]['email'];
            $this->mailing_list = $r[0]['mailing_list'];
            $this->phone = $r[0]['phone'];
            $this->mobile = $r[0]['mobile'];
            $this->address1 = $r[0]['address1'];
            $this->address2 = $r[0]['address2'];
            $this->address3 = $r[0]['address3'];
            $this->address4 = $r[0]['address4'];
            $this->country_id = $r[0]['country_id'];
            $this->postcode = $r[0]['postcode'];
            $this->coordinates = $r[0]['coordinates'];
            $this->notes = $r[0]['notes'];
            $this->publish = $r[0]['publish'];
            $this->dob = $r[0]['dob'];
            $this->last_modification = $r[0]['last_modification'];
            $this->linked_user_id = $r[0]['linked_user_id'];

            $this->relations = DB::select(
                "has.*",
                DB::expr("CONCAT_WS(' ', first_name, last_name, IF(email, CONCAT('<', email, '>'), null)) AS contact_2")
            )
                ->from(array(self::TABLE_HAS_RELATIONS, 'has'))
                    ->join(array(self::TABLE_CONTACT, 'rcontact'), 'inner')
                        ->on('has.contact_2_id', '=', 'rcontact.id')
                ->where('contact_1_id', '=', $this->id)
                ->and_where('has.deleted', '=', 0)
                ->execute()
                ->as_array();

            $this->of_relations = DB::select(
                "has.*",
                DB::expr("CONCAT_WS(' ', first_name, last_name, IF(email, CONCAT('<', email, '>'), null)) AS contact_1")
            )
                ->from(array(self::TABLE_HAS_RELATIONS, 'has'))
                    ->join(array(self::TABLE_CONTACT, 'rcontact'), 'inner')
                        ->on('has.contact_1_id', '=', 'rcontact.id')
                ->where('contact_2_id', '=', $this->id)
                ->and_where('has.deleted', '=', 0)
                ->execute()
                ->as_array();

            $this->communications = DB::select(
                "comms.*",
                array("types.name", "type")
            )
                ->from(array(self::TABLE_COMMS, 'comms'))
                    ->join(array(self::TABLE_COMM_TYPES, 'types'), 'inner')
                        ->on('comms.type_id', '=', 'types.id')
                ->where('comms.contact_id', '=', $this->id)
                ->and_where('comms.deleted', '=', 0)
                ->execute()
                ->as_array();

            $this->preferences = DB::select(
                array("types.id", 'type_id'),
                array("types.name", "type"),
                array("types.section", "section"),
                array("prefs.id", "id"),
                array("prefs.value", "value")
            )
                ->from(array(self::TABLE_PREF_TYPES, 'types'))
                    ->join(array(self::TABLE_PREFS, 'prefs'), 'left')
                        ->on('prefs.type_id', '=', 'types.id')
                        ->on('prefs.contact_id', '=', DB::expr($this->id))
                        ->on('prefs.deleted', '=', DB::expr(0))
                ->execute()
                ->as_array();
        }

        return $ok;
    }

    /**
     * @return array An array(first_name, last_name, email, mailing_list, phone, mobile, notes, publish) with all the
     * strings sanitized and ready to be inserted into the table.
     */
    private function build_insert_array()
    {
        // If the mailing list is already present, get the identifier
        $mailing_list = $this->mailing_list;
        $mailing_list_id = self::sql_get_mailing_list_id($mailing_list);

        if ($mailing_list_id === FALSE) // Important! '===' is mandatory or 0 will be erroneously interpreted as FALSE
        {
            // If not, add it
            $mailing_list_id = $this->sql_add_mailing_list($mailing_list);
        }

        // Create the array with all the values
        $details = array
        (
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mailing_list' => $mailing_list_id,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'address3' => $this->address3,
            'address4' => $this->address4,
            'country_id' => $this->country_id,
            'postcode' => $this->postcode,
            'coordinates' => $this->coordinates,
            'notes' => $this->notes,
            'publish' => $this->publish,
            'dob' => $this->dob,
            'linked_user_id' => $this->linked_user_id
        );

        return $details;
    }

    //
    // SQL FUNCTIONS
    //

    private function sql_get_contact_details($id)
    {
        $r = DB::select('t1.id', 't1.title', 't1.first_name', 't1.last_name', 't1.email', array('t2.name', 'mailing_list'), 't1.phone', 't1.mobile', 't1.address1', 't1.address2', 't1.address3', 't1.address4', 't1.country_id', 't1.postcode', 't1.coordinates', 't1.notes', 't1.publish', 't1.last_modification', 't1.dob', 't1.linked_user_id')
            ->from(array(Model_Contacts::TABLE_CONTACT, 't1'))
            ->where('t1.id', '=', $id)
            ->join(array(Model_Contacts::TABLE_MAILING_LIST, 't2'), 'INNER')
            ->on('t1.mailing_list', '=', 't2.id')
            ->execute()
            ->as_array();

        return (count($r) == 1) ? $r : FALSE;
    }

    /**
     * @param string $mailing_list The name of the mailing list to be added.
     * @return bool TRUE if the function success. Otherwise, FALSE.
     */
    private function sql_add_mailing_list($mailing_list)
    {
        $r = DB::insert(Model_Contacts::TABLE_MAILING_LIST, array('name'))
            ->values(array($mailing_list))
            ->execute();

        return ($r[1] == 1) ? $r[0] : FALSE;
    }

    /**
     * @param string $mailing_list The name of the mailing list to get the identifier for.
     * @return mixed The mailing list identifier on success. Otherwise, FALSE.
     */
    public static function sql_get_mailing_list_id($mailing_list)
    {
        $r = DB::select('id')
            ->from(Model_Contacts::TABLE_MAILING_LIST)
            ->where('name', '=', $mailing_list)
            ->execute()
            ->as_array();

        return (count($r) == 1) ? $r[0]['id'] : FALSE;
    }

    /**
     * @param array $details An array containing the contact's details.
     * @return mixed The identifier of the contact inserted. Otherwise, FALSE.
     */
    private function sql_add_contact($details)
    {
        if ($this->next_id_for_insert) {
            $details['id'] = $this->next_id_for_insert;
        }
        $r = DB::insert(Model_Contacts::TABLE_CONTACT, array_keys($details))
            ->values(array_values($details))
            ->execute();

        return ($r[1] == 1) ? $r[0] : FALSE;
    }

    /**
     * @param int $id The contact identifier.
     * @param array $details An array containing the contact's details.
     * @return bool
     */
    private function sql_update_contact($id, $details)
    {
        $r = DB::update(Model_Contacts::TABLE_CONTACT)
            ->set($details)
            ->where('id', '=', $id)
            ->execute();

        // Changed or not, assume update was successful (otherwise an exception will be raised)
        return TRUE;
    }

    /**
     * @param array $details An array containing the contact's details.
     * @return bool
     */
    private function sql_contact_exist($details)
    {
        try {
            // allow to have contacts without email
            if (trim($details['email']) == '') {
                return false;
            }

            $r = DB::select()
                ->from(Model_Contacts::TABLE_CONTACT)
                ->where('email', '=', $details['email'])
                ->and_where('mailing_list', '=', $details['mailing_list'])
                ->and_where('deleted', '=', 0)
                ->execute()
                ->get('id');

            if (empty($r)) {
                //mail doesn't exist
                return false;
            } else {
                if ($this->id == null) {
                    $this->id = $r['id'];
                }
                //mail already exist
                return true;
            }
        } catch (Exception $e) {
            //in case there is an error, it will return false, this mean that the contact will be added.
            //This is not a good way to handle the error but in case of fail this will not stop the php script.
            return false;
        }

    }

    /********************************************
     * Courses plugin methods
     *******************************************/

    public static function get_trainers()
    {
		$query = DB::query(Database::SELECT, "SELECT c.id, c.first_name, c.last_name, CONCAT(`c`.`first_name`, ' ', `c`.`last_name`) AS `full_name`
        FROM `plugin_contacts_contact` `c`
        JOIN `plugin_contacts_mailing_list` `m` ON `c`.`mailing_list` = `m`.`id`
        WHERE `c`.`publish` = 1
        AND   `m`.`name` = 'trainer'
        ORDER BY `c`.`first_name`
        ")
			->execute()
			->as_array();
		return $query;
    }

    public static function get_all_contacts_for_csv()
    {
        $query = DB::query(Database::SELECT, 'SELECT first_name,last_name,email,phone,mobile FROM plugin_contacts_contact ORDER BY first_name')->execute()->as_array();
        return $query;
    }

    public static function get_all_contacts_selected($id = NULL,$billing_contact = false)
    {
        $contacts = self::get_contact_all('first_name','ASC');
        $result = "";
        $user = "";

        if(is_object($id))
        {
            $customer = $id->load();
            if($billing_contact)
            {
                $id = $customer['billing_contact'];
            }
            else
            {
                $id = $customer['contact'];
            }
        }

        foreach($contacts AS $contact)
        {
            if($contact['id'] == $id)
            {
                $user = '<option value="'.$contact['id'].'">'.$contact['first_name'].' '.$contact['last_name'].'</option>';
            }
            else
            {
                $result.='<option value="'.$contact['id'].'">'.$contact['first_name'].' '.$contact['last_name'].'</option>';
            }
        }

        $result = '<option value="add">Add new contact</option>'.$result;

        if(is_numeric($id))
        {
            $result = $user.$result;
        }

        if($id == NULL OR !isset($id) OR $id == '')
        {
            $result = '<option value="">Choose a contact</option>'.$result;
        }

        return $result;
    }

    public static function get_last_inserted_contact_id()
    {
        $q = DB::select('id')->from(self::TABLE_CONTACT)->order_by('last_modification','DESC')->limit(1)->execute()->as_array();
        return $q[0]['id'];
    }

	public static function get_mailing_lists_as_options($default = NULL)
	{
		$lists = DB::select('id', 'name')->from('plugin_contacts_mailing_list')->execute()->as_array();

		$html = '<option value="">-- Please select --</option>';
		foreach ($lists as $list)
		{
			$selected = ( ! is_null($default) AND $list['name'] == $default) ? ' selected="selected"' : '';
			$html .= '<option value="'.$list['name'].'"'.$selected.'>'.$list['name'].'</option>';
		}
		return $html;
	}

    public static function registerExtention(ContactsExtention $extention)
    {
        self::$extentions[] = $extention;
    }

    public static function getExtentions()
    {
        return self::$extentions;
    }

    public static function getRelations($enabled_ony = true)
    {
        $q = DB::select('relations.*')
            ->from(array(self::TABLE_RELATIONS, 'relations'))
            ->where('relations.deleted', '=', 0);
        if ($enabled_ony) {
            $q->join(array(Model_Plugin::MAIN_TABLE, 'plugins'), 'inner')->on('relations.plugin_id', '=', 'plugins.id')
                ->join(array(Model_Resources::TABLE_RESOURCES, 'resources'), 'inner')->on('plugins.name', '=', 'resources.alias')
                ->join(array(Model_Resources::TABLE_HAS_PERMISSION, 'has_permission'), 'inner')->on('resources.id', '=', 'has_permission.resource_id')
                ->join(array('engine_project_role', 'roles'), 'inner')->on('has_permission.role_id', '=', 'roles.id')
                ->and_where('roles.role', '=', 'Administrator');
        }
        $relations= $q->order_by('relation')
            ->execute()
            ->as_array();

        $result = array();
        foreach ($relations as $relation) {
            $result[$relation['id']] = $relation['relation'];
        }
        return $result;
    }

    /*
     * can accept an id, an array of ids, an array of rows with id column
     * */
    public static function getPermissions($contacts)
    {
        $ids = array();
        if (is_array($contacts)) {
            foreach ($contacts as $contact) {
                if (is_array($contact)) {
                    $ids[] = $contact['id'];
                } else {
                    $ids = $contact;
                }
            }
        } else {
            $ids = array($contacts);
        }
        if (count($ids) == 0) {
            return array();
        }
        
        $permissions = DB::select('users.*')
            ->from(array(self::TABLE_PERMISSION_LIMIT, 'permissions'))
                ->join(array('engine_users', 'users'))->on('permissions.user_id', '=', 'users.id')
            ->where('permissions.contact_id', 'in', $ids)
            ->execute()
            ->as_array();
        return $permissions;
    }

    public static function removePermission($contact_id, $user_id)
    {
        DB::delete(self::TABLE_PERMISSION_LIMIT)
            ->where('contact_id', '=', $contact_id)
            ->and_where('user_id', '=', $user_id)
            ->execute();
    }

    public static function addPermission($contact_id, $user_id)
    {
        DB::insert(self::TABLE_PERMISSION_LIMIT)
            ->values(array('contact_id' => $contact_id, 'user_id' => $user_id))
            ->execute();
    }

    public static function autocomplete_list($term, $list = null)
    {
        $q = DB::select(
            array("contacts.id", "value"),
            DB::expr("CONCAT_WS(' ', first_name, last_name, IF(email, CONCAT('<', email, '>'), null)) AS label")
        )
            ->from(array(self::TABLE_CONTACT, 'contacts'))
            ->where('contacts.deleted', '=', 0)
            ->and_where_open()
                ->or_where('first_name', 'like', '%' . $term . '%')
                ->or_where('last_name', 'like', '%' . $term . '%')
            ->and_where_close();
        if ($list) {
            $q->join(array(self::TABLE_MAILING_LIST, 'list'), 'inner')->on('contacts.mailing_list', '=', 'list.id');
            $q->and_where('list.name', '=', $list);
        }
        $contacts = $q
            ->order_by('first_name')
            ->order_by('last_name')
            ->limit(20)
            ->execute()
            ->as_array();

        return $contacts;
    }

    public static function search($params)
    {
        $q = DB::select(
            'contacts.*',
            array('mlist.name', 'mail_list')
        )
            ->from(array(self::TABLE_CONTACT, 'contacts'))
                ->join(array(self::TABLE_MAILING_LIST, 'mlist'), 'left')
                    ->on('contacts.mailing_list', '=', 'mlist.id')
            ->where('contacts.deleted', '=', 0);

        if (@$params['email']) {
            $q->and_where('email', '=', $params['email']);
        }
        if (@$params['mobile']) {
            $q->and_where('mobile', '=', $params['mobile']);
        }
        if (@$params['phone']) {
            $q->and_where('phone', '=', $params['phone']);
        }
        if (@$params['first_name']) {
            $q->and_where('first_name', '=', $params['first_name']);
        }
        if (@$params['last_name']) {
            $q->and_where('last_name', '=', $params['last_name']);
        }
        if (@$params['user_id']) {
            self::limited_user_access_filter($q, $params['user_id'], 'contacts.id');
        }
        if (@$params['notes']) {
            $q->and_where('notes', 'like', '%' . $params['notes'] . '%');
        }

        if (@$params['limit']) {
            $q->limit($params['limit']);
        }
        $result = $q->execute()->as_array();
        return $result;
    }

    public static function get_datatable($filters)
    {
        $output    = array();
        // Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
        // These must be ordered, as they appear in the resultant table and there must be one per column
        $columns   = array();
        $columns[] = 'contacts.id';
        $columns[] = 'contacts.first_name';
        $columns[] = 'contacts.last_name';
        $columns[] = 'contacts.email';
        $columns[] = 'contacts.mobile';
        $columns[] = 'mlist.name';
        $columns[] = 'contacts.last_modification';


        $select = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS contacts.id'),
            'contacts.first_name',
            'contacts.last_name',
            'contacts.email',
            array('mlist.name', 'mailing_list'),
            'contacts.phone',
            'contacts.mobile',
            'contacts.notes',
            'contacts.publish',
            'contacts.last_modification'
        )
            ->from(array(Model_Contacts::TABLE_CONTACT, 'contacts'))
                ->join(array(Model_Contacts::TABLE_MAILING_LIST, 'mlist'), 'LEFT')
                    ->on('contacts.mailing_list', '=', 'mlist.id')
            ->where('contacts.deleted', '=', 0);
        if (is_numeric(@$filters['check_permission_user_id'])) {
            $filter1 = DB::select('contact_id')
                ->from(self::TABLE_PERMISSION_LIMIT)
                ->where('user_id', '=', $filters['check_permission_user_id']);
            $filter2 = DB::select('contact_2_id')
                ->from(array(self::TABLE_PERMISSION_LIMIT, 'permissions'))
                ->join(array(self::TABLE_HAS_RELATIONS, 'related1'), 'inner')->on('permissions.contact_id', '=', 'related1.contact_1_id')
                ->where('permissions.user_id', '=', $filters['check_permission_user_id']);
            $filter3 = DB::select('contact_1_id')
                ->from(array(self::TABLE_PERMISSION_LIMIT, 'permissions'))
                ->join(array(self::TABLE_HAS_RELATIONS, 'related1'), 'inner')->on('permissions.contact_id', '=', 'related1.contact_2_id')
                ->where('permissions.user_id', '=', $filters['check_permission_user_id']);
            $select->and_where_open();
            $select->or_where('contacts.id', 'in', $filter1);
            $select->or_where('contacts.id', 'in', $filter2);
            $select->or_where('contacts.id', 'in', $filter3);
            $select->and_where_close();
        }

        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $select->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $columns[$i] != '') {
                    $select->or_where($columns[$i], 'like', '%' . $filters['sSearch'] . '%');
                }
            }
            $select->and_where_close();
        }
        // Individual column search
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $filters['sSearch_' . $i] != '') {
                $select->and_where($columns[$i], 'like', '%'.$filters['sSearch_' . $i] . '%');
            }
        }

        // Don't allow "Show all" to work here. There are too many records for that.
        if (empty($filters['iDisplayLength']) || $filters['iDisplayLength'] == -1 || $filters['iDisplayLength'] > 100) {
            $filters['iDisplayLength'] = 10;
        }

        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $select->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $select->offset(intval($filters['iDisplayStart']));
            }
        }

        // Order
        if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if ($columns[$filters['iSortCol_' . $i]] != '') {
                    $select->order_by($columns[$filters['iSortCol_' . $i]], $filters['sSortDir_' . $i]);
                }
            }
        }
        $select->order_by('contacts.last_modification', 'desc');

        $results = $select->execute()->as_array();

        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords']        = count($results); // displayed results
        $output['aaData']               = array();

        foreach ($results as $result) {
            $row   = array();
            $row[] = $result['id'];
            $row[] = $result['first_name'];
            $row[] = $result['last_name'];
            $row[] = $result['email'];
            $row[] = $result['mobile'];
            $row[] = $result['mailing_list'];
            $row[] = $result['last_modification'];
            $row[] = '<span class="flaticon-eye"></span>';
            $row[] = '<span class="flaticon-remove-button"></span>';
            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($filters['sEcho']);

        return $output;
    }

	public function get_communications()
	{
		return $this->communications;
	}

	public function get_preferences()
	{
		return $this->preferences;
	}

    public static function get_communication_types()
    {
        $types = DB::select('*')
            ->from(self::TABLE_COMM_TYPES)
            ->execute()
            ->as_array();
        return $types;
    }

	public static function get_communication_type($name)
	{
		$type = DB::select()->from(self::TABLE_COMM_TYPES)->where('name', '=', $name)->execute()->as_array();
		if (isset($type[0]))
		{
			return $type[0];
		}
		else
		{
			return DB::query(NULL, 'SHOW FULL COLUMNS FROM '.self::TABLE_COMM_TYPES)->execute()->as_array();
		}
	}

    public static function get_communication_type_id($name)
    {
        $type = DB::select()->from(self::TABLE_COMM_TYPES)->where('name', '=', $name)->execute()->current();
        if ($type) {
            return $type['id'];
        } else {
            return null;
        }
    }

    public static function get_preference_types()
    {
        $types = DB::select('*')
            ->from(self::TABLE_PREF_TYPES)
            ->execute()
            ->as_array();
        return $types;
    }

    public static function add_relation($contact1_id, $contact2_id, $relation_id = null)
    {
        DB::delete(self::TABLE_HAS_RELATIONS)
            ->where('contact_1_id', '=', $contact1_id)
            ->and_where('contact_2_id', '=', $contact2_id)
            ->execute();
        $relation = array(
            'contact_1_id' => $contact1_id,
            'contact_2_id' => $contact2_id,
            'relation_id' => $relation_id
        );
        DB::insert(self::TABLE_HAS_RELATIONS)->values($relation)->execute();
    }

    public static function limited_user_access_filter($query, $user_id, $contact_id_field)
    {
        $filter1 = DB::select('contact_id')
            ->from(self::TABLE_PERMISSION_LIMIT)
            ->where('user_id', '=', $user_id);
        $filter2 = DB::select('contact_2_id')
            ->from(array(self::TABLE_PERMISSION_LIMIT, 'permissions'))
            ->join(array(self::TABLE_HAS_RELATIONS, 'related1'), 'inner')->on('permissions.contact_id', '=', 'related1.contact_1_id')
            ->where('permissions.user_id', '=', $user_id);
        $filter3 = DB::select('contact_1_id')
            ->from(array(self::TABLE_PERMISSION_LIMIT, 'permissions'))
            ->join(array(self::TABLE_HAS_RELATIONS, 'related1'), 'inner')->on('permissions.contact_id', '=', 'related1.contact_2_id')
            ->where('permissions.user_id', '=', $user_id);

        $query->and_where_open();
            $query->or_where($contact_id_field, 'in', $filter1);
            $query->or_where($contact_id_field, 'in', $filter2);
            $query->or_where($contact_id_field, 'in', $filter3);
        $query->and_where_close();
    }

	public static function get_contact_data_for_user($id)
	{
		$user = new Model_User($id);
		$linked_users = empty($user->id) ? array() : Model_Contacts::search(array('user_id' => $user->id));

		$children = NULL;
		$guardian = NULL;
		foreach ($linked_users as $linked_user)
		{
			if ($linked_user['mail_list'] != 'Parent/Guardian')
			{
				$child      = $linked_user;
				$child_data = new Model_Contacts($linked_user['id']);

				foreach ($child_data->get_preferences() as $preference)
				{
					switch ($preference['type'])
					{
						case 'Photo/Video Permission': $child['photo_consent']       = $preference['value']; break;
						case 'Medical information':    $child['medical_information'] = $preference['value']; break;
					}
				}
				$children[] = $child;
			}

			if ($linked_user['email'] == $user->email)
			{
				$guardian      = $linked_user;
				$guardian_data = new Model_Contacts($linked_user['id']);

				foreach ($guardian_data->get_communications() as $communication)
				{
					switch ($communication['type'])
					{
						case 'Mobile':          $guardian['mobile']          = $communication['value']; break;
						case 'Emergency Phone': $guardian['emergency_phone'] = $communication['value']; break;
					}
				}
			}
		}

		return array(
			'guardian' => $guardian,
			'children' => $children
		);

	}


    public static function import_old_email_mobile_to_new_communications()
    {
        // import emails
        DB::query(
            null,
            "insert into plugin_contacts_communications
	(contact_id, type_id, `value`, `deleted`)
	(select c.id, 1, c.email, 0
	from plugin_contacts_contact c
		left join plugin_contacts_communications o on c.id = o.contact_id and o.type_id = 1
	where c.email is not null and c.email <> '' and o.`value` is null)"
        );

        // import mobiles
        DB::query(
            null,
            "insert into plugin_contacts_communications
	(contact_id, type_id, `value`, `deleted`)
	(select c.id, 2, c.mobile, 0
	from plugin_contacts_contact c
		left join plugin_contacts_communications o on c.id = o.contact_id and o.type_id = 2
	where c.mobile is not null and c.mobile <> '' and o.`value` is null)"
        );

        // import mobiles
        DB::query(
            null,
            "insert into plugin_contacts_communications
	(contact_id, type_id, `value`, `deleted`)
	(select c.id, 3, c.phone, 0
	from plugin_contacts_contact c
		left join plugin_contacts_communications o on c.id = o.contact_id and o.type_id = 3
	where c.phone is not null and c.phone <> '' and o.`value` is null)"
        );
    }

    public static function link_user_to_existing_contact($email)
    {
        $user = DB::select('*')
            ->from(Model_Users::MAIN_TABLE)
            ->where('deleted', '=', 0)
            ->and_where('email', '=', $email)
            ->execute()
            ->current();

        $contact = DB::select('contacts.*')
            ->from(array(self::TABLE_CONTACT, 'contacts'))
                ->join(array(self::TABLE_COMMS, 'emails'), 'left')
                    ->on('contacts.id', '=', 'emails.contact_id')
                ->join(array(self::TABLE_PERMISSION_LIMIT, 'tpl'), 'left')
                    ->on('contacts.id', '=', 'tpl.contact_id')
            ->where('contacts.deleted', '=', 0)
            ->and_where('tpl.user_id', 'is', null)
            ->and_where_open()
                ->or_where('contacts.email', '=', $email)
                ->or_where('emails.value', '=', $email)
            ->and_where_close()
            ->order_by('contacts.id', 'desc')
            ->execute()
            ->current();

        if ($user && $contact) {
            DB::insert(self::TABLE_PERMISSION_LIMIT)
                ->values(array('user_id' => $user['id'], 'contact_id' => $contact['id']))
                ->execute();
        }
    }

    public static function get_linked_contact_to_user($user_id)
    {
        $contact = DB::select('contacts.*')
            ->from(array(self::TABLE_CONTACT, 'contacts'))
            ->where('linked_user_id', '=', $user_id)
            ->execute()
            ->current();
        return $contact;
    }

    public static function delete_user_data($user_id)
    {
        $contact = self::get_linked_contact_to_user($user_id);

        if ($contact) {
            DB::update(self::TABLE_CONTACT)
                ->set(
                    array(
                        'first_name' => '',
                        'last_name' => '',
                        'email' => '',
                    )
                )
                ->where('id', '=', $contact['id'])
                ->execute();

            DB::update(self::TABLE_COMMS)
                ->set(array('value' => ''))
                ->where('contact_id', '=', $contact['id'])
                ->execute();
        }
    }

    public static function bulk_transfer_delete($contacts)
    {

        try {
            Database::instance()->begin();

            foreach ($contacts as $contact_id => $details) {
                if ($details['action'] == 'delete') {
                    DB::update(self::TABLE_CONTACT)->set(array('deleted' => 1))->where('id', '=', $contact_id)->execute();
                } else if ($details['action'] == 'transfer') {
                    DB::update('plugin_courses_schedules_has_students')->set(array('contact_id' => $details['contact_id']))->where('contact_id', '=', $contact_id)->execute();
                    DB::update('plugin_courses_bookings')->set(array('student_id' => $details['contact_id']))->where('student_id', '=', $contact_id)->execute();
                    DB::update('plugin_transactions_transactions')->set(array('contact_id' => $details['contact_id']))->where('contact_id', '=', $contact_id)->execute();


                    DB::update(Model_Schedules::TABLE_SCHEDULES)->set(array('trainer_id' => $details['contact_id']))->where('trainer_id', '=', $contact_id)->execute();
                    DB::update(Model_ScheduleEvent::TABLE_TIMESLOTS)->set(array('trainer_id' => $details['contact_id']))->where('trainer_id', '=', $contact_id)->execute();

                    DB::update(self::TABLE_CONTACT)->set(array('deleted' => 1))->where('id', '=', $contact_id)->execute();
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
}
