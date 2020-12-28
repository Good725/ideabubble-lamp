<?php defined('SYSPATH') or die('No Direct Script Access.');
/**
 * Class Model_Contacts3
 * This class has been designed to be flexible.
 * There cannot be any circumstances in which it would need to be modified; with the exception of additional functionality.
 * Ensure data passed in is correct and is well defined before being entered into the model.
 */
final class Model_Contacts3 extends Model implements Interface_Contacts3
{
    /**
     ** ----- CONSTANT VALUES -----
     **/

    CONST CONTACTS_TABLE                        = 'plugin_contacts3_contacts';
    CONST CONTACTS_TYPE_TABLE                   = 'plugin_contacts3_contact_type';
    CONST CONTACTS_TYPE_COLUMNS_TABLE           = 'plugin_contacts3_contact_type_columns';
    CONST CONTACTS_TYPE_COLUMNS_RELATION_TABLE  = 'plugin_contacts3_contact_type_has_columns';
	CONST CONTACTS_SUBTYPE_TABLE                = 'plugin_contacts3_contacts_subtypes';
    CONST FAMILY_TABLE                          = 'plugin_contacts3_family';
    CONST ROLE_TABLE                            = 'plugin_contacts3_roles';
	CONST CONTACT_ROLE_RELATION_TABLE           = 'plugin_contacts3_contact_has_roles';
    CONST CONTACT_FAMILY_RELATION_TABLE         = 'plugin_contacts3_contact_has_family';
    CONST ADDRESS_TABLE                         = 'plugin_contacts3_residences';
    CONST CONTACT_NOTIFICATION_RELATION_TABLE   = 'plugin_contacts3_contact_has_notifications';
    CONST NOTIFICATIONS_TABLE                   = 'plugin_contacts3_notifications';
    CONST CONTACT_PREFERENCES_RELATION_TABLE    = 'plugin_contacts3_contact_has_preferences';
    CONST PREFERENCES_TABLE                     = 'plugin_contacts3_preferences';
    CONST COURSE_TYPES_TABLE                    = 'plugin_courses_course_types';
    CONST CONTACT_COURSE_TYPE_RELATION_TABLE    = 'plugin_contacts3_contact_has_course_type_preferences';
    CONST SUBJECTS_TABLE                        = 'plugin_curses_subjects';
    CONST CONTACT_SUBJECT_RELATION_TABLE        = 'plugin_contacts3_contact_has_subject_preferences';
    CONST CONTACT_COURSE_SUBJECT_PREFERENCE     = 'plugin_contacts3_contact_has_course_subject_preferences';
    const TABLE_PERMISSION_LIMIT                = 'plugin_contacts3_users_has_permission';
    const INVITATIONS_TABLE                     = 'plugin_contacts3_invitations';
    const CONTACT_RELATIONS_TABLE               = 'plugin_contacts3_relations';
    const PAYMENTGW_TABLE = 'plugin_contacts3_has_paymentgw';
    const HAS_CARDS_TABLE = 'plugin_contacts3_has_paymentgw_has_card';
    const CONTACT_JOB_FUNCTIONS_TABLE            = 'plugin_contacts3_job_functions';
    const CONTACT_BLACKLIST                      = 'plugin_contacts3_blacklist';
    const TEMPORARY_SIGNUP_TABLE                 = 'plugin_contacts3_temporary_signup_data';

    public static $mobile_provider_codes = array('083', '085', '086', '087', '088', '089');

    /**
     ** ----- PRIVATE MEMBER DATA -----
     **/

    private $id                      = NULL;
    private $title                   = '';
    private $type                    = NULL;
	private $subtype_id              = NULL;
    private $first_name              = '';
    private $last_name               = '';
    private $date_of_birth           = '';
    private $family_id               = NULL;
    private $school_id               = NULL;
    private $academic_year_id        = NULL;
    private $year_id                 = NULL;
    private $points_required         = NULL;
    private $residence               = NULL;
    private $billing_residence_id    = NULL;
    private $notifications_group_id  = NULL;
    private $is_primary              = 0;
    private $is_flexi_student        = 0;
    private $publish                 = 1;
    private $delete                  = 0;
    private $date_created            = '';
    private $date_modified           = '';
    private $created_by              = NULL;
    private $modified_by             = NULL;
    private $pps_number              = NULL;
    private $notifications           = array();
    private $preferences             = array();
    private $course_type_preferences = array();
    private $subject_preferences     = array();
	private $roles                   = array();
    private $student_id              = null;
    private $course_subject_preference = array();
    private $timeoff_config          = array();
    private $is_inactive             = 0;
    private $permissions             = array();
    private $nationality             = '';
    private $gender                  = '';
    private $cycle                   = '';
    private $occupation              = '';
    private $courses_i_would_like    = '';
    private $linked_user_id          = '';
    private $domain_name             = '';
    private $is_public_domain        = 0;
    private $tags                    = array();
    // Used to verify if the tags have been loaded. Avoids risk of wiping existing tags when saving.
    private $tags_set                = false;
    private $contact_relations       = array();
    private $job_title = '';
    private $job_function_id = null;
    private $hourly_rate = '';
    private $api_type    = '';
    private $api_id      = '';

    public $trigger_save = false;
    /**
     ** ----- PUBLIC MEMBER DATA -----
     **/

    public $address = NULL;
    public $billing_address = null;
    /**
     ** ----- PUBLIC FUNCTIONS -----
     **/

    function __construct($id = NULL)
    {
        $this->set_id($id);
        $this->get(true);
    }

    /**
     * @purpose To fill object member data.
     * @param $data
     * @return $this
     */
    public function load($data)
    {
		$data = self::normalize_notification_data($data);
        foreach ($data AS $key => $value)
        {
            if (property_exists($this,$key))
            {
                $this->{$key} = ($value == '') ? NULL : $value;
            }
        }
        $this->address = new Model_Residence($this->residence);
        $this->billing_address = new Model_Residence($this->billing_residence_id);
        return $this;
    }

    public function get($autoload = FALSE)
    {
        $data                            = $this->_sql_get_contact();
        $this->notifications_group_id    = @$data['notifications_group_id'];
        $data['notifications']           = $this->get_contact_notifications();
        $data['preferences']             = $this->_sql_get_preferences();
        $data['course_type_preferences'] = $this->_sql_get_course_type_preferences();
        $data['subject_preferences']     = $this->_sql_get_subject_preferences();
		$data['roles']                   = $this->_sql_get_role_ids();
        $data['course_subject_preference']  = $this->_sql_get_course_subject_preferences();
        $data['permissions']             = $this->_sql_get_permissions();
        $data['contact_relations']       = $this->_sql_get_contact_relations();
        $data['tags']                    = $this->_sql_get_tags();
        if($autoload)
        {
            $this->load($data);
        }
        return $this;
    }

    public function save($validate = true)
    {
        if ($validate) {
            $ok = $this->validate();
        } else {
            $ok = true;
        }
        if($ok)
        {
            Database::instance()->begin();
            try
            {
                if (Settings::instance()->get('contacts_create_family') != 1) {
                    if (Model_Contacts3::find_type('Family')['contact_type_id'] == $this->type) {
                        $this->type = Model_Contacts3::find_type('Student')['contact_type_id'];
                    }
                }
                $this->set_residence($this->address->save());
                $this->set_billing_residence_id($this->billing_address->save('Billing'));
                $this->set_date_modified();
                $this->save_contact_details();
                $c2_id = $this->sync_contacts2();
                if(is_numeric($this->id))
                {
                    $this->_sql_update_contact();
                }
                else
                {
                    $this->set_date_created();
                    $this->_sql_insert_contact($c2_id);
                }
                if ($this->is_primary == 1) {
                    DB::update(Model_Contacts3::CONTACTS_TABLE)
                        ->set(array('is_primary' => 0))
                        ->where('family_id', '=', $this->family_id)
                        ->and_where('id', '<>', $this->id)
                        ->execute();
                    DB::update(Model_Family::FAMILY_TABLE)
                        ->set(array('primary_contact_id' => $this->id))
                        ->where('family_id', '=', $this->family_id)
                        ->execute();
                }
                //Other Saving Functions
				$this->_sql_save_roles();
                $this->_sql_save_preferences();
                $this->_sql_save_course_type_preferences();
                $this->_sql_save_subject_preferences();
                $this->_sql_save_course_subject_preferences();
                $this->_sql_save_permissions();
                $this->_sql_save_tags();
                $this->save_contact_relations();
                Database::instance()->commit();
                if ($this->trigger_save) {
                    Model_Automations::run_triggers(Model_Contacts3_Contactsavetrigger::NAME, array('contact_id' => $this->id));
                }
            }
            catch(Exception $e)
            {
                Database::instance()->rollback();
                throw $e;
            }
        }
        return $ok;
    }

    protected function sync_contacts2()
    {
        if (is_numeric($this->id)) {
            $contact = new Model_Contacts($this->id);
        } else {
            $next_id_1 = DB::select(array('AUTO_INCREMENT', 'next_id'))
                ->from('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA', '=', DB::expr('DATABASE()'))
                ->and_where('TABLE_NAME', '=', 'plugin_contacts_contact')
                ->execute()
                ->get('next_id');
            $next_id_2 = DB::select(array('AUTO_INCREMENT', 'next_id'))
                ->from('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA', '=', DB::expr('DATABASE()'))
                ->and_where('TABLE_NAME', '=', 'plugin_contacts3_contacts')
                ->execute()
                ->get('next_id');
            $next_id = max($next_id_1, $next_id_2);
            $next_id = max(100000, $next_id);
            $contact = new Model_Contacts();
            $contact->next_id_for_insert = $next_id;
        }

        // Set the data
        $contact->set_title($this->title);
        $contact->set_first_name($this->first_name);
        $contact->set_last_name($this->last_name);
        $contact->set_email((is_array(@$_POST['email'])) ? null : @$_POST['email']);
        $contact->set_mailing_list(1);
        $contact->set_phone(@$_POST['phone']);
        $contact->set_mobile(@$_POST['mobile']);
        $contact->set_address1(@$_POST['address1']);
        $contact->set_address2(@$_POST['address2']);
        $contact->set_address3(@$_POST['address3']);
        $contact->set_address4(@$_POST['town']);
        if (isset($_POST['country'])) {
            $contact->set_country_id($_POST['country']);
        }
        if (isset($_POST['postcode'])) {
            $contact->set_postcode($_POST['postcode']);
        }
        if (isset($_POST['coordinates'])) {
            $contact->set_coordinates($_POST['coordinates']);
        }
        $contact->set_dob(@$_POST['date_of_birth']);
        $contact->set_publish(@$_POST['publish']);
        $ok = $contact->save();
        return $contact->get_id();
    }

    public static function cards_list($contact_id)
    {
        $cards = DB::select('cards.*')
            ->from([self::HAS_CARDS_TABLE, 'cards'])
                ->join([self::PAYMENTGW_TABLE, 'gw'], 'inner')->on('cards.has_paymentgw_id', '=', 'gw.id')
            ->where('contact_id', '=', $contact_id)
            ->execute()
            ->as_array();
        return $cards;
    }

    public static function card_delete($id)
    {
        $gw = DB::select('gw.*')
            ->from([self::HAS_CARDS_TABLE, 'cards'])
            ->join([self::PAYMENTGW_TABLE, 'gw'], 'inner')->on('cards.has_paymentgw_id', '=', 'gw.id')
            ->where('cards.id', (is_array($id) ? 'in' : '='), $id)
            ->execute()
            ->as_array();

        DB::delete(self::HAS_CARDS_TABLE)
            ->where('id', (is_array($id) ? 'in' : '='), $id)
            ->execute();
        foreach ($gw as $gateway) {
            DB::delete(self::PAYMENTGW_TABLE)
                ->where('id', '=', $gateway['id'])
                ->execute();
        }
        DB::delete(Model_KES_Bookings::HAS_CARD_TABLE)
            ->where('card_id', (is_array($id) ? 'in' : '='), $id)
            ->execute();
    }

    public static function get_domain_blacklist()
    {
         return DB::select('*')
                ->from(self::CONTACT_BLACKLIST)
                ->where('delete', '=', 0)
                ->execute()
                ->as_array();
    }

    public static function is_blacklisted_domain($domain_name) {
        $backlisted = DB::select('id')
            ->from(self::CONTACT_BLACKLIST)
            ->where('domain_name', '=', $domain_name)
            ->and_where('delete', '=', 0)
            ->execute()
            ->as_array();
        return !empty($backlisted);
    }

    public function save_contact_details()
    {
        $family = new Model_Family($this->family_id);
        if ($family->get_family_count() < 1) {
            $this->is_primary = 1;
        }
        $family_notification = $family->get_notifications_group_id();

        if ($family_notification == $this->notifications_group_id) { // "use family" will be no more. create a new id
            $this->notifications_group_id = null;
        }
        if ($this->get_notifications_group_id() == '' AND ! empty($this->notifications))
        {
            $group = $this->_sql_insert_contact_details_group();
            $this->set_notifications_group_id($group[0]);
        }
        if ($family_notification == null) {
            $family->set_notifications_group_id($this->get_notifications_group_id());
            $family->save();
            $family_notification = $this->get_notifications_group_id();
        }

        if ( ($family_notification == $this->notifications_group_id AND $this->is_primary == 1) OR ($family_notification != $this->notifications_group_id) OR count($family->get_members()) < 2)
        {
            $notifications = $this->get_contact_details_instance();
            $existing_notifications = $new_notifications = array();
            foreach ($notifications as $notification)
            {
                ($notification['id'] == 'new' OR $notification['id'] == '') ? $new_notifications[] = $notification : $existing_notifications[] = $notification;
            }
            $notifications_ids = $this->_sql_insert_contact_details($new_notifications);
            $this->_sql_update_contact_details($existing_notifications);
            foreach ($existing_notifications as $existing_notification) {
                $notifications_ids[] = $existing_notification['id'];
            }
            $deleteq = DB::update(self::CONTACT_NOTIFICATION_RELATION_TABLE)
                ->set(
                    array('deleted' => 1)
                )
                ->where('group_id', '=', $this->notifications_group_id);
            if (count($notifications_ids) > 0) {
                $deleteq->and_where('id', 'not in', $notifications_ids);
            }
            $deleteq->execute();
        }
    }

    protected function save_contact_relations()
    {
        DB::delete(self::CONTACT_RELATIONS_TABLE)
            ->where('child_id', '=', $this->id)
            ->execute();
        foreach ($this->contact_relations as $contact_relation) {
            if (!isset($contact_relation['child_id'])) {
                $contact_relation['child_id'] = $this->id;
            }
            DB::insert(self::CONTACT_RELATIONS_TABLE)
                ->values($contact_relation)
                ->execute();
        }
    }

    public function get_instance()
    {
        return array(
            'id'                     => $this->id,
            'title'                  => $this->title,
            'type'                   => $this->type,
            'subtype_id'             => $this->subtype_id,
            'is_flexi_student'       => $this->is_flexi_student,
            'first_name'             => $this->first_name,
            'last_name'              => $this->last_name,
            'student_id'             => $this->student_id,
            'date_of_birth'          => $this->date_of_birth,
            'family_id'              => $this->family_id,
            'school_id'              => $this->school_id,
            'year_id'                => $this->year_id,
            'points_required'        => $this->points_required,
            'residence'              => $this->residence,
            'billing_residence_id'   => $this->billing_residence_id,
            'notifications_group_id' => $this->notifications_group_id,
            'is_primary'             => $this->is_primary,
            'publish'                => $this->publish,
            'delete'                 => $this->delete,
            'date_created'           => $this->date_created,
            'date_modified'          => $this->date_modified,
            'created_by'             => $this->created_by,
            'modified_by'            => $this->modified_by,
            'pps_number'             => $this->pps_number,
            'academic_year_id'       => $this->academic_year_id,
            'is_inactive'            => $this->is_inactive,
            'nationality'            => $this->nationality,
            'gender'                 => $this->gender,
            'cycle'                  => $this->cycle,
            'courses_i_would_like'   => $this->courses_i_would_like,
            'linked_user_id'         => $this->linked_user_id,
            'occupation'             => $this->occupation,
            'hourly_rate'            => $this->hourly_rate,
            'job_title'              => $this->job_title,
            'job_function_id'        => $this->job_function_id,
            'domain_name'            => $this->domain_name
        );
    }

    public function get_contact_details_instance()
    {
        $return = array();
        foreach ($this->notifications as $notification)
        {
            $return[] = array(
                'id'              => $notification['id'],
                'group_id'        => $this->notifications_group_id,
                'notification_id' => $notification['notification_id'],
                'dial_code'       => isset($notification['dial_code']) ? $notification['dial_code'] : '',
                'country_dial_code'       => isset($notification['country_dial_code']) ? $notification['country_dial_code'] : '',
                'value'           => $notification['value'],
                'date_created'    => $this->date_created,
                'date_modified'   => $this->date_modified,
                'created_by'      => $this->created_by,
                'modified_by'     => $this->modified_by
            );
        }
        return $return;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function delete()
    {
		try
		{
			$this->set_delete(1);
			$this->set_publish(0);
			$this->set_date_modified();
			$this->_sql_update_contact();

            $activity = new Model_Activity();
            $activity->set_item_id($this->id);
            $activity->set_item_type('contact3');
            $activity->set_action('delete');
            $activity->save();

            $othersInFamily = self::get_all_contacts(
                array(
                    array('contact.family_id', '=', $this->family_id),
                    array('contact.id', '<>', $this->id)
                )
            );

            if (count($othersInFamily) == 0) {
                $family = new Model_Family($this->family_id);
                $family->delete();
            }
            Model_Automations::run_triggers(Model_Contacts3_Contactdeletetrigger::NAME, array('contact_id' => $this->id));
			return TRUE;
		}
		catch (Exception $e)
		{
			return FALSE;
		}
    }

    public function validate()
    {
        $valid = TRUE;
        $this->date_of_birth = ($this->date_of_birth == '') ? NULL : date('Y-m-d',strtotime($this->date_of_birth));

        foreach ($this->notifications as $notification)
        {
            // TODO: Use names, rather than IDs
            if (($notification['notification_id'] == '2' || $notification['notification_id'] == '3') && !preg_match('/^([\+][0-9]{1,3}[ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$/', $notification['value']))
            {
                IbHelpers::set_message('A phone number must contain only numbers', 'error');
                $valid = FALSE;
            }
        }
        if ($this->first_name == '')
        {
            IbHelpers::set_message('First name is required', 'error');
            $valid = FALSE;
        }
        if ($this->last_name == '' && $this->type == 1)
        {
            IbHelpers::set_message('Last name is required', 'error');
            $valid = FALSE;
        }

        return $valid;
    }

	public function set_column($column, $value)
	{
		$this->{$column} = $value;
		return $this;
	}

    public function set_contact_relations($contact_relations)
    {
        $this->contact_relations = $contact_relations;
    }

    public function get_contact_relations()
    {
        return $this->contact_relations;
    }

    protected function _sql_get_contact_relations($child_parent = 'child_id')
    {
        $relations = DB::select('*')
            ->from(self::CONTACT_RELATIONS_TABLE)
            ->where($child_parent, '=', $this->id)
            ->execute()
            ->as_array();
        return $relations;
    }

    public function get_contact_relations_details($filter = false)
    {
        $ids = array();
        foreach ($this->contact_relations as $rel) {
            $ids[] = $rel['parent_id'];
        }
        $contacts = array();
        if (count($ids) > 0) {
            $select = DB::select('contacts.id', DB::expr("CONCAT_WS(' ', IF(types.label = 'Department', pcontacts.first_name, ''), contacts.first_name, contacts.last_name) AS name"))
                ->from(array(self::CONTACTS_TABLE, 'contacts'))
                ->join(array(self::CONTACTS_TYPE_TABLE, 'types'), 'left')->on('contacts.type', '=', 'types.contact_type_id')
                ->join(array(self::CONTACT_RELATIONS_TABLE, 'rel'), 'left')->on('rel.child_id', '=', 'contacts.id')
                ->join(array(self::CONTACTS_TABLE, 'pcontacts'), 'left')->on('rel.parent_id', '=', 'pcontacts.id')
                ->where('contacts.id', 'in', $ids);
            if(@$filter['contact_type']) {
                $select->where('types.label', '=', $filter['contact_type']);
            }
            $contacts = $select->execute()->as_array();
            foreach ($contacts as $i => $contact) {
                foreach ($this->contact_relations as $rel) {
                    if ($rel['parent_id'] == $contact['id']) {
                        $contacts[$i] = array_merge($contact, $rel);
                    }
                }
            }
        }
        return $contacts;
    }

    public static function get_contact_relations_child_details($parent_id)
    {
        $select = DB::select(
            "contacts.id",
            DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) AS name")
        )
            ->from(array(self::CONTACTS_TABLE, 'contacts'))
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'rel'), 'inner')->on('rel.child_id', '=', 'contacts.id')
            ->join(array(self::CONTACTS_TYPE_TABLE, 'types'), 'left')->on('contacts.type', '=', 'types.contact_type_id')
            ->where('rel.parent_id', '=', $parent_id);

        $contacts = $select->execute()->as_array();

        $select = DB::select(
            "contacts.id",
            DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) AS name")
        )
            ->from(array(self::CONTACTS_TABLE, 'contacts'))
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'rel'), 'inner')->on('rel.child_id', '=', 'contacts.id')
            ->join(array(self::CONTACTS_TYPE_TABLE, 'types'), 'left')->on('contacts.type', '=', 'types.contact_type_id')
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'rel2'), 'inner')->on('rel.parent_id', '=', 'rel2.child_id')
            ->where('rel2.parent_id', '=', $parent_id);
        $contacts2 = $select->execute()->as_array();

        $contacts = array_merge($contacts, $contacts2);
        $ids = array($parent_id);
        foreach ($contacts as $contact) {
            $ids[] = $contact['id'];
        }
        $contacts = self::get_all_contacts(array(array('contact.id', 'in', $ids)));
        return $contacts;
    }

    public function set_id($id)
    {
        $this->id = (is_numeric($id) AND $id > 0) ? (int) $id : NULL;
    }

    public function set_title($title)
    {
        $this->last_name = (is_string($title) AND trim(strlen($title)) > 0) ? $title : '';
    }

    public function set_type($type)
    {
        $this->type = is_numeric($type) ? $type : NULL;
    }

    public function set_api_id($api_id) {
        $this->id = $api_id;
    }
	
	public function set_subtype_id($subtype_id)
    {
        $this->subtype_id = is_numeric($subtype_id) ? $subtype_id : NULL;
    }

    public function set_first_name($first_name)
    {
        $this->first_name = (is_string($first_name) AND trim(strlen($first_name)) > 0) ? $first_name : '';
    }

    public function set_last_name($last_name)
    {
        $this->last_name = (is_string($last_name) AND trim(strlen($last_name)) > 0) ? $last_name : '';
    }

    public function set_pps_number($pps_number)
    {
        $this->pps_number = (is_string($pps_number) AND trim(strlen($pps_number)) > 0 ) ? $pps_number : '' ;
    }

    public function set_date_of_birth($date_of_birth)
    {
        $this->date_of_birth = strtotime($date_of_birth) ? date('Y-m-d',strtotime($date_of_birth)) : date::dmy_to_ymd($date_of_birth);
    }

    public function set_family_id($id)
    {
        $this->family_id = is_numeric($id) ? $id : NULL;
		return $this;
    }

    public function set_school_id($school_id)
    {
        $this->school_id = is_numeric($school_id) ? $school_id : NULL;
    }

    public function set_academic_year_id($academic_year_id)
    {
        $this->academic_year_id = is_numeric($academic_year_id) ? $academic_year_id : NULL;
    }
    public function set_year_id($id)
    {
        $this->year_id = is_numeric($id) ? $id : NULL;
    }

    public function set_points_required($points_required)
    {
        $this->points_required = is_numeric($points_required) ? (int) $points_required : NULL;
    }

    public function get_points_required()
    {
        return $this->points_required;
    }

    public function set_courses_i_would_like($courses_i_would_like)
    {
        $this->courses_i_would_like = $courses_i_would_like;
    }

    public function get_courses_i_would_like()
    {
        return $this->courses_i_would_like;
    }

    public function set_linked_user_id($linked_user_id)
    {
        $this->linked_user_id = $linked_user_id;
    }

    public function get_linked_user_id()
    {
        return $this->linked_user_id;
    }

    public function set_residence($residence)
    {
        $this->residence = is_numeric($residence) ? (int) $residence : NULL;
    }
    
    public function set_billing_residence_id($billing_residence_id)
    {
       $this->billing_residence_id = $billing_residence_id;
    }
    
    public function set_billing_address($billing_address)
    {
        $this->billing_address = $billing_address;
    }
    
    public function set_notifications_group_id($id)
    {
        $this->notifications_group_id = is_numeric($id) ? $id : NULL;
        return $this;
    }

    public function set_is_primary($is_primary)
    {
        $this->is_primary = $is_primary === 1 ? 1 : 0;
    }

    public function set_is_flexi_student($is_flexi_student)
    {
        $this->is_flexi_student = $is_flexi_student === 1 ? 1 : 0;
    }

    public function set_publish($publish)
    {
        $this->publish = $publish === 0 ? 0 : 1;
    }

    public function set_delete($delete)
    {
        $this->delete = $delete === 1 ? 1 : 0;
    }

    public function set_date_created()
    {
        $this->date_created = date('Y-m-d H:i:s',time());
        return $this;
    }

    public function set_date_modified($date = null)
    {
        $this->date_modified = $date ?: date('Y-m-d H:i:s',time());
        return $this;
    }

    public function set_created_by($created_by)
    {
        $this->created_by = is_numeric($created_by) ? (int) $created_by : NULL;
        return $this;
    }

    public function set_modified_by($modified_by)
    {
        $this->modified_by = is_numeric($modified_by) ? (int) $modified_by : NULL;
        return $this;
    }

    public function set_preferences($preferences)
    {
        $this->preferences = $preferences;
        return $this;
    }

    public function set_course_type_preferences($preferences)
    {
        $this->course_type_preferences = $preferences;
    }

    public function set_subject_preferences($preferences)
    {
        $this->subject_preferences = $preferences;
        return $this;
    }

    public function set_is_inactive($is_inactive)
    {
        $this->is_inactive = $is_inactive == 1 ? 1 : 0;
    }

    public function set_nationality($nationality)
    {
        $this->nationality = $nationality;
    }

    public function set_gender($gender)
    {
        $this->gender = $gender;
    }

    public function set_cycle($cycle)
    {
        $this->cycle = $cycle;
    }

    public function set_occupation($occupation)
    {
        $this->occupation = $occupation;
    }

    public function set_is_special_member($special_member = false) {
        $special_member_tag = Model_Contacts3_Tag::get_tag_by_name('special_member');
        if (!empty($special_member_tag)) {
            if ($special_member) {
                if (!$this->has_tag($special_member_tag)) {
                    $this->delete_tags(array($special_member_tag));
                    $this->append_tags($special_member_tag);
                    $this->tags_set = true;
                }
            } else {
                if ($this->has_tag($special_member_tag)) {
                    $this->delete_tags(array($special_member_tag));
                    $this->tags_set = true;
                }
            }
        }
    }

	public function add_role($role)
	{
		if( ! in_array($role,$this->roles)){
			$this->roles[] = $role;
		}
	}

    public function add_role_by_stub($roleStub)
    {
        $allRoles = $this->get_all_roles();
        foreach($allRoles as $role){
            if ($role['stub'] == $roleStub && !in_array($role['id'], $this->roles)) {
                $this->roles[] = $role['id'];
            }
        }
    }
	
	public function remove_role($role)
	{
		$key = array_search($role, $this->roles);
		if($key !== false){
			unset($this->roles[$key]);
			$this->roles = array_values($this->roles);
		}
	}
	
	public function get_roles()
	{
		return $this->roles;
	}
 
    public function get_roles_stubs($include_labels = false)
    {
        $allRoles = $this->get_all_roles();
        $contactRoles = array();
        foreach($allRoles as $role){
            if(in_array($role['id'], $this->roles)){
                if($include_labels === false) {
                    $contactRoles[$role['id']] = $role['stub'];
                } else {
                    $contactRoles[$role['id']] = array('stub' => $role['stub'], 'name' => $role['name']);
                }
            }
        }
        return $contactRoles;
    }

    public function get_is_teacher()
    {
        $query = DB::select()->from(array(self::CONTACTS_TABLE,'c'))
            ->join(array(self::CONTACT_ROLE_RELATION_TABLE,'r'),'INNER')
            ->on('c.id','=','r.contact_id')
            ->where('r.role_id','=',4)
            ->where('c.id','=',$this->id)
            ->execute()
            ->as_array();
        return $query ? TRUE : FALSE ;
    }

    public function set_course_subject_preferences($preferences)
    {
        $this->course_subject_preference = $preferences;
    }

    public static function set_notification_deleted($notification)
    {
        return DB::update(self::CONTACT_NOTIFICATION_RELATION_TABLE)->set(array('publish'=>0,'deleted'=>1))->where('id','=',$notification)->execute();
    }

    public function set_family_primary_contact($family_id,$contact_id)
    {
        $status         = FALSE;
        $family = new Model_Family($family_id);
        $contact = new Model_Contacts3($contact_id);
        $old_residence = $family->get_residence();
        $old_notification = $family->get_notifications_group_id();
        $old_primary = $family->get_primary_contact_id();
        // Unset and set primary contact
        $remove         = DB::update('plugin_contacts3_contacts')->set(array('is_primary'=>0))->where('family_id','=',$family_id)->execute();
        $add            = DB::update('plugin_contacts3_contacts')->set(array('is_primary'=>1))->where('id','=',$contact_id)->execute();
//        $family = new Model_Family($contact->get_family_id());
//        $family->set_primary_contact_id($contact_id);
//        $family->set_residence($contact->get_residence());
//        $family->set_notifications_group_id($contact->get_notifications_group_id());
//        $family->save();
        // Update all family members
        $update_residence  = DB::update('plugin_contacts3_contacts')->set(array('residence'=>$contact->get_residence()))->where('family_id','=',$family_id)->where('residence','=',$old_residence)->where('id','!=',$old_primary)->execute();
        $update_notification = DB::update('plugin_contacts3_contacts')->set(array('notifications_group_id'=>$contact->get_notifications_group_id()))->where('family_id','=',$family_id)->where('notifications_group_id','=',$old_notification)->where('id','!=',$old_primary)->execute();
        if ($remove AND $add AND $update_residence AND $update_notification)
        {
            $status = TRUE;
        }
        return $status ;
    }

    public function is_new_contact()
    {
        return is_numeric($this->id) ? FALSE : TRUE;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_type()
    {
        return $this->type;
    }
	
	public function get_subtype_id()
    {
        return $this->subtype_id;
    }

    public function get_subtype()
    {
        return DB::select()->from(self::CONTACTS_SUBTYPE_TABLE)->where('id', '=', $this->subtype_id)->execute()->as_array();
    }

    public function get_api_type() {
        return $this->api_type;
    }

    public function get_api_id() {
        return $this->api_id;
    }
    
    public function get_total_minutes_assigned_schedule($start_date, $end_date)
    {
        $total_sum = DB::select(DB::expr('SUM(time_to_sec(timediff(cse.datetime_end, cse.datetime_start))) as "total_sum"'))
                ->from(array('plugin_courses_schedules', 'cs'))
                ->join(array('plugin_ib_educate_booking_has_schedules', 'bhs'))
                ->on('bhs.schedule_id', '=', 'cs.id')
                ->join(array('plugin_ib_educate_booking_items', 'bi'))
                ->on('bi.booking_id', '=', 'bhs.booking_id')
                ->join(array('plugin_courses_schedules_events', 'cse'))
                ->on('cse.id', '=', 'bi.period_id')
                ->where('cs.trainer_id', '=', $this->id);
        if (isset($start_date) && isset($end_date)) {
            $total_sum = $total_sum->where('cse.datetime_end', 'BETWEEN', array($start_date, $end_date));
        } else {
            $total_sum = $total_sum->where('cse.datetime_end', '<', DB::expr('CURRENT_TIMESTAMP'));
        }
        $total_sum = $total_sum->execute()->get("total_sum") ?? 0;
        $total_sum_hrs = $total_sum / 60;
        return $total_sum_hrs;
    }
    
    public static function get_subtypes($filtered_displayed_subtypes = FALSE)
    {
        $rows = DB::select()->from(self::CONTACTS_SUBTYPE_TABLE);
        if ($filtered_displayed_subtypes) {
            $rows->where('display_subtype', '=', '1');
        }
        $rows = $rows->execute()->as_array();
        return $rows;
    }

    public static function find_subtype($type)
    {
        $row = DB::select()->from(self::CONTACTS_SUBTYPE_TABLE)
            ->where('subtype', '=', $type)
            ->execute()
            ->current();
        return $row;
    }
    
    public static function find_type($type)
    {
        $row = DB::select()->from(self::CONTACTS_TYPE_TABLE)
            ->where('label', '=', $type)
            ->or_where('name', '=', $type)
            ->or_where('display_name', '=', $type)
            ->execute()
            ->current();
        return $row;
    }
    
    public static function get_contact_type($id)
    {
        $type = DB::select()
            ->from(self::CONTACTS_TYPE_TABLE)
            ->where('contact_type_id', '=', $id)
            ->execute()->current();
        return $type;
    }
    
    public static function delete_contact_type($contact_type_id)
    {
        $row = DB::delete(self::CONTACTS_TYPE_TABLE)
            ->where('contact_type_id', '=', $contact_type_id)
            ->execute();
        return $row;
    }
    
    public static function save_contact_type($contact_type)
    {
        $contact_type_id = $contact_type['id'] ?? null;
        unset($contact_type['id']);
        if (!empty($contact_type_id)) {
            DB::update(self::CONTACTS_TYPE_TABLE)
                ->set($contact_type)->where('contact_type_id', '=', $contact_type_id)
                ->execute();
        } else {
            DB::insert(self::CONTACTS_TYPE_TABLE)
                ->values($contact_type)->execute();
        }
    }
    
    public function get_first_name()
    {
        return $this->first_name;
    }

    public function get_last_name()
    {
        return $this->last_name;
    }

    public function get_date_of_birth()
    {
        if ($this->date_of_birth == '' || $this->date_of_birth == '0000-00-00 00:00:00' || $this->date_of_birth == '1970-01-01 00:00:00')
            return NULL;
        else
            return date('d-m-Y',strtotime($this->date_of_birth));
    }

    public function get_family_id()
    {
        return $this->family_id;
    }

    public function get_family()
    {
        return new Model_family($this->family_id);
    }

    public function get_school_id()
    {
        return $this->school_id;
    }

    public function get_academic_year_id()
    {
        return $this->academic_year_id;
    }

    public function get_year_id()
    {
        return $this->year_id;
    }

    public function get_residence()
    {
        return $this->residence;
    }
    
    public function get_billing_residence_id()
    {
        return $this->billing_residence_id;
    }
    
    public function get_billing_address()
    {
        // If the contact is an org_rep, get the organisation's billing address
        if (!$this->has_role('organisation')) {
            $organisation = $this->get_organisation();

            if ($organisation->get_billing_residence_id()) {
                return new Model_Residence($organisation->get_billing_residence_id());
            }
        }

        return new Model_Residence($this->billing_residence_id);
    }

    /**
     * Determine if the contact has permission to edit the billing address.
     * @return bool
     */
    public function can_edit_billing_address()
    {
        // Organisations can edit their own addresses
        if ($this->has_role('organisation')) {
            return true;
        }
        // If a contact is linked to an organisation, with a billing address, that will be used as the contact address.
        else if (!empty($this->get_organisation()->get_billing_residence_id())) {
            // The contact can only edit said address, if they have the necessary permission.
            return Auth::instance()->has_access('user_profile_organisation_edit_billing_address');
        }

        // By default, contacts can edit the billing address.
        return true;
    }

    // Get the organisation the contact is associated with.
    public function get_organisation()
    {
        $organisations = $this->get_contact_relations_details(['contact_type' => 'organisation']);
        $organisation_id = (!empty($organisations)) ? $organisations[0]['id'] : null;

        return new Model_Contacts3($organisation_id);
    }

    
	public function get_address_line1($address_id)
	{
		return DB::select('address1')->from(self::ADDRESS_TABLE)->where('address_id','=',$address_id)->execute()->get('address1', '');
	}
	public function get_address_line2($address_id)
	{
		return DB::select('address2')->from(self::ADDRESS_TABLE)->where('address_id','=',$address_id)->execute()->get('address2', '');
	}
	public function get_address_line3($address_id)
	{
		return DB::select('address3')->from(self::ADDRESS_TABLE)->where('address_id','=',$address_id)->execute()->get('address3', '');
	}
	public function get_address_town($address_id)
	{
		return DB::select('town')->from(self::ADDRESS_TABLE)->where('address_id','=',$address_id)->execute()->get('town', '');
	}
	public function get_address_county($address_id)
	{
		return DB::select('name')->from(array('engine_counties','c'))
			->join(array(self::ADDRESS_TABLE,'a'))->on('c.id','=','a.county')
			->where('a.address_id','=',$address_id)
			->execute()->get('name', '');
	}
	public function get_address_postcode($address_id)
	{
		return DB::select('postcode')->from(self::ADDRESS_TABLE)->where('address_id','=',$address_id)->execute()->get('postcode', '');
	}
	public function get_address_country($address_id)
	{
		return DB::select('name')
			->from(array('countries','c'))->join(array(self::ADDRESS_TABLE,'a'))->on('c.code','=','a.county')
			->where('address_id','=',$address_id)
            ->or_where('code', '=', $address_id)
			->execute()->get('name', '');
	}
    public function get_address_details($address_id)
    {
        $address = DB::select()->from(self::ADDRESS_TABLE)->where('address_id','=',$address_id)->execute()->as_array();
        return $address;
    }

    public function get_notifications_group_id()
    {
        return $this->notifications_group_id;
    }

    public function get_is_primary()
    {
        return $this->is_primary;
    }

    public function get_is_inactive()
    {
        return $this->is_inactive;
    }

    public function get_nationality()
    {
        return $this->nationality;
    }

    public function get_gender()
    {
        return $this->gender;
    }

    public function get_cycle()
    {
        return $this->cycle;
    }

    public function get_occupation()
    {
        return $this->occupation;
    }

    public function get_is_flexi_student()
    {
        return $this->is_flexi_student;
    }

    public function get_preferences()
    {
        return $this->preferences;
    }

    public function get_preferences_ids()
    {
        $contact_preference_ids = array();
        foreach ($this->preferences as $preference) {
            $contact_preference_ids[] = $preference['preference_id'] ?? $preference;
        }
        return $contact_preference_ids;
    }

    public function get_course_type_preferences()
    {
        return $this->course_type_preferences;
    }

    public function get_course_types_preferences_ids()
    {
        $course_types_preference_ids = array();
        foreach ($this->course_type_preferences as $course_types_preference) {
            $course_types_preference_ids[] = $course_types_preference['course_type_id'];
        }
        return $course_types_preference_ids;
    }

    public function get_subject_preferences()
    {
        return $this->subject_preferences;
    }

    public function get_subject_preferences_ids()
    {
        $subject_preference_ids = array();
        foreach ($this->subject_preferences as $subject_preference) {
            $subject_preference_ids[] = $subject_preference['subject_id'];
        }
        return $subject_preference_ids;
    }

    public function get_courses_subject_preferences()
    {
        return $this->course_subject_preference;
    }

    public function get_address()
    {
        return $this->address;
    }

    public function get_contact_notifications($stub = null)
    {
        $select = DB::select('cn.id','cn.value','cn.country_dial_code', 'cn.dial_code', 'cn.notification_id',array('n.id', 'type_id'),array('n.name','type_text'),array('n.stub','type_stub'))
            ->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'cn'))
                ->join(array(self::NOTIFICATIONS_TABLE, 'n'), 'LEFT')->on('cn.notification_id', '=', 'n.id')
                ->join(array(self::CONTACTS_TABLE, 'c'), 'INNER')->on('cn.group_id', '=', 'c.notifications_group_id')
            ->and_where_open()
                ->or_where('cn.group_id', '=', $this->notifications_group_id)
                ->or_where('c.id', '=', $this->id)
            ->and_where_close()
            ->where('n.deleted', '=', 0)
            ->where('cn.deleted', '=', 0);
        if ($stub) {
            $select->and_where('n.stub', '=', $stub);
        }
        return $select->execute()->as_array();
    }

    public static function get_notification($id)
    {
        $q = DB::select()->from(self::CONTACT_NOTIFICATION_RELATION_TABLE)->where('id', '=', $id)->where('deleted', '=', 0)->execute();

        if (isset($q[0])) {
            return $q[0];
        } else {
            return DB::query(NULL, 'SHOW FULL COLUMNS FROM '.self::CONTACT_NOTIFICATION_RELATION_TABLE)->execute()->as_array();
        }
    }

    public function get_email()
    {
        foreach ($this->notifications as $notification) {
            // These numbers are unfortunately hardcoded and used inconsistently
            if (in_array($notification['notification_id'], array('1')) && filter_var(trim($notification['value']), FILTER_VALIDATE_EMAIL)) {
                return trim($notification['value']);
            }
        }
        return null;
    }

    public function get_phone()
    {
        foreach ($this->notifications as $notification) {
            // These numbers are unfortunately hardcoded and used inconsistently
            if (in_array($notification['notification_id'], array('3'))) {
                return trim($notification['value']);
            }
        }
        return null;
    }

    public function get_notification_by_type($type)
    {
        foreach ($this->notifications as $notification) {
            // These numbers are unfortunately hardcoded and used inconsistently
            if ($notification['notification_id'] == $type) {
                return trim($notification['value']);
            }
        }
        return null;
    }

    public function get_mobile($args = array())
    {
        $args['components'] = isset($args['components']) ? $args['components'] : false;

        $mobile = DB::select('*')
            ->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'cn'))
            ->join(array(self::NOTIFICATIONS_TABLE, 'n'), 'LEFT')->on('cn.notification_id', '=', 'n.id')
            ->where('cn.group_id', '=', $this->notifications_group_id)
            ->where('n.stub', '=', 'mobile')
            ->where('n.deleted', '=', 0)
            ->where('cn.deleted', '=', 0)
            ->execute()
            ->current();


        // If the components argument is specified, split the provider code from the number
        // and return the components
        if ($args['components'])
        {
            if (isset($mobile['dial_code'])) {
                $return = array('country_code' => '', 'code' => '', 'number' => $mobile['value'], 'full_number' => '');
                $return['country_code'] = $mobile['country_dial_code'] ? $mobile['country_dial_code'] : '';
                $return['code'] = $mobile['dial_code'] ? $mobile['dial_code'] : '';
                $return['full_number'] = $return['country_code'] . $return['code'] . $mobile['value'];
            } else {
                $return = array('country_code' => '', 'code' => '', 'number' => $mobile['value'], 'full_number' => $mobile['value']);
                //$mobile = ($mobile == 0) ? null : trim($mobile);

                foreach (self::$mobile_provider_codes as $code)
                {
                    if (strpos($mobile['value'], $code) === 0) {
                        $return['code'] = $code;
                        $return['number'] = substr($mobile['value'], strlen($code));
                    }
                }
            }
        }
        else {
            $return = $mobile['value'];
        }
        return $return;
    }

    /**
     * Get the full mobile number, including area codes and formatting.
     *
     * @param $separator string - Text to appear between segments of the number e.g. hyphen or space
     * @return string
     */
    public function get_mobile_number($separator = '')
    {
        $number = $this->get_mobile(array('components' => true));
        $return = '';

        if (!empty($number['country_code'])) {
            // Add the country code, prefixed with a "+"
            $return .= '+' . $number['country_code'];
        }

        if (!empty($number['code'])) {
            if (!empty($number['country_code'])) {
                // If the country code has been specified, trim the leading zero from the local area code
                $return .= $separator . ltrim($number['code'], '0');
            } else {
                // Otherwise, use the local area code, including the leading zero.
                $return .= $number['code'];
            }
        }

        $return .= $separator . $number['number'];

        return trim($return, $separator);
    }

    /**
     * Get the concatenated country code columns for use in a MySQL query
     *
     * @param $table string - The table name or alias
     * @param $separator string - Separator to use between the portions of the number
     * @return string
     */
    public static function country_code_columns($table, $separator = '')
    {
        // concatenate the three fields
        return "CONCAT_WS(".
            "'$separator',".
            // Put a "+" before the country code
            "CONCAT('+', `$table`.`country_dial_code`),".
            // Trim the leading 0 from the area code
             "TRIM(LEADING '0' FROM `$table`.`dial_code`),".
             "`$table`.`value`".
         ")";
    }

    /**
     * Gets the contact's type and subtype
     * @param $id
     * @return mixed
     */
    public function get_contact_types()
    {
        $result = DB::select('contact_type.contact_type_id', array('contact_type.label', 'contact_type_label'),
            array('contact_subtype.id', 'contact_subtype_id'),
            array('contact_subtype.subtype', 'contact_subtype_label'))
            ->from(array(self::CONTACTS_TABLE, 'contact'))
            ->join(array(self::CONTACTS_TYPE_TABLE, 'contact_type'), 'left')
            ->on('contact.type', '=', 'contact_type.contact_type_id')
            ->join(array(self::CONTACTS_SUBTYPE_TABLE, 'contact_subtype'), 'left')
            ->on('contact.subtype_id', '=', 'contact_subtype.id')
            ->where('contact.id', '=', $this->id)
            ->execute()->current();
        return $result;
    }
    
    public function get_contact_name()
    {
        return $this->title . ' ' . $this->first_name . ' ' . $this->last_name ;
    }

    public function get_billed_organization()
    {
        $billed = DB::select('id','title', 'first_name', 'last_name', 'family_id', 'type')
            ->from(self::CONTACTS_TABLE)
            ->where('type', '=', 2)
            ->execute()
            ->as_array();
        return $billed;
    }

    public function get_pps_number()
    {
        return $this->pps_number;
    }

    public function get_primary_contact()
    {
        $family_id = self::get_family_id_by_contact_id($this->id);

        // Only perform the query if the contact has a family
        if ( ! is_null($family_id))
        {
            $primary = DB::select(array('primary_contact_id','primary'))->from('plugin_contacts3_family')->where('family_id','=',$family_id)->execute()->as_array();
            $result = $primary[0]['primary'];
        }
        return isset($result) ? $result : '';
    }
    
    public function get_tags()
    {
        return $this->tags;
    }
    
    public function set_tags($tags)
    {
        $array = [];
        foreach ($tags as $tag) {
            if ($tag instanceof Model_Contacts3_Tag) {
                // Already a tag, add it
                $array[] = $tag;
            } elseif (!empty($tag['id'])) {
                // Tag ID => load the tag and add it
                $array[] = new Model_Contacts3_Tag($tag['id']);
            } elseif (isset($tag['label'])) {
                // Tag name => check if the tag exists, add the existing tag or create a new one
                $label = trim($tag['label']);
                $new_tag = new Model_Contacts3_Tag(['label' => $label]);
                $new_tag->set_label($label);
                if (empty($new_tag->get_name())) {
                    $new_tag->set_name(str_replace('-', '_', Ibhelpers::slugify($label)));
                }
                $new_tag->save();
                $array[] = $new_tag;
            }
        }
        
        $this->tags_set = true;
        $this->tags = $array;
    }

    public function get_student_id()
    {
        return $this->student_id;
    }

    public function set_student_id($value)
    {
        $this->student_id = $value;
    }

    public static function uses_student_ids()
    {
        return ORM::factory('Contacts3_Contact')
            ->where('student_id', 'is not', null)
            ->where_undeleted()
            ->count_all() > 0;
    }

    public function has_tag($tag, $force_sql = false)
    {
        $has_tag = false;
        if (!empty($this->tags) && !$force_sql) {
            foreach($this->tags as $existing_tag) {
                if($existing_tag->get_id() == $tag->get_id()) {
                    $has_tag = true;
                }
            }
        } else {
            $existing_tags = $this->_sql_get_tags();
            if (!empty($existing_tags)) {
                foreach($existing_tags as $existing_tag) {
                    if($existing_tag->get_id() == $tag->get_id()) {
                        $has_tag = true;
                    }
                }
            }
        }
        return $has_tag;
    }

    public function append_tags($tags)
    {
        $this->tags = array_merge($this->tags, array($tags));
        return $this->tags;
    }

    public function delete_tags($tags)
    {
        foreach($this->tags as $key => $existing_tag) {
            foreach($tags as $tag) {
                if ($tag->get_id() == $existing_tag->get_id()) {
                    unset($this->tags[$key]);
                }
            }
        }
        return $this->tags;
    }
    
    public function get_hourly_rate()
    {
        return $this->hourly_rate;
    }
    
    public function get_job_title()
    {
        return $this->job_title;
    }
    
    public function get_job_function_id()
    {
        return $this->job_function_id;
    }

    public function get_domain_name() {
        return $this->domain_name;
    }

    public function set_domain_name($domain_name) {
        $this->domain_name = $domain_name;
    }

    public function get_is_public_domain() {
        return $this->is_public_domain;
    }

    public function set_is_public_domain($is_public_domain) {
        $this->is_public_domain = $is_public_domain;
    }
    /**
     * Check if the contact has a specified role
     * @param string $role - name of the role, used in the `stub` column of the database table
     * @return array
     */
    public function has_role($role = '')
    {
        if (empty($role)) return false;

        $has_role = DB::select()
            ->from(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'))
            ->join(array(self::ROLE_TABLE, 'role'))->on('has_role.role_id', '=', 'role.id')
            ->where('has_role.contact_id', '=', $this->id)
            ->and_where('role.stub', '=', $role)
            ->execute()
            ->as_array();

        return (count($has_role) > 0);
    }

    public function test_delete()
    {
        $result = array(
            'ok' => false,
            'message' => '',
            'familyContacts' => array()
        );

        if (!is_numeric($this->id)) {
            $result['message'] = 'Please select a contact';
        } else {
            $result['contact'] = $this->get_instance();
            if ($this->is_primary) {
                $familyContacts = self::get_all_contacts(
                    array(
                        array('contact.family_id', '=', $this->family_id),
                        array('contact.id', '<>', $this->id)
                    )
                );
                if (count($familyContacts) == 0) {
                    $result['ok'] = true;
                } else {
                    $othersOnlyChildren = true;
                    foreach ($familyContacts as $familyContact) {
                        if (
                            strpos($familyContact['stub'], 'guardian') !== false
                            ||
                            strpos($familyContact['stub'], 'mature') !== false
                        ){
                            $othersOnlyChildren = false;
                            break;
                        }
                    }

                    if ($othersOnlyChildren) {
                        $result['message'] = 'Please delete the other contacts in the family before you can delete a primary contact';
                    } else {
                        $result['message'] = 'Please select another contact as primary contact and guardian role';
                    }
                }
                $result['familyContacts'] = $familyContacts;
            } else {
                $result['ok'] = true;
            }
        }

        return $result;
    }

    public function get_linked_organisation()
    {
        $linked_organisation = $this->get_contact_relations_details(array('contact_type' => 'organisation'));
        if(count($linked_organisation) === 0) {
            return new Model_Contacts3();
        } else {
            return new Model_Contacts3(current($linked_organisation)['id']);
        }
    }

    /**
     * Function to be used to update membership status for all contacts related to Organisation
     * @param $membership_status
     * @return bool
     * @throws Exception
     */
    public function update_membership_for_organisation($membership_status) {
        if (!$this->get_type() == self::find_type('Organisation')['contact_type_id']) {
            return false;
        }
        $this->set_is_special_member($membership_status);
        $this->save();
        $organisation_related_contacts =  self::get_child_related_contacts($this->get_id());
        if (!empty($organisation_related_contacts)) {
            foreach ($organisation_related_contacts as $org_related_contact_id) {
                $org_rep_contact = new Model_Contacts3($org_related_contact_id);
                $org_rep_contact->set_is_special_member($membership_status);
                $org_rep_contact->save();
            }
        }
        return true;
    }
    /**
     ** ----- PUBLIC STATIC FUNCTIONS -----
     **/

    public static function instance($id = NULL)
    {
        return new self($id);
    }

    public static function get_all_contacts($where_clauses = array(), $limit = false)
    {
        $query   = DB::select(
            'contact.id','contact.title','contact.first_name','contact.last_name','contact.is_primary', 'contact.family_id','contact.date_created', 'contact.date_modified',array(DB::expr('group_concat(concat(primary.title, " ", primary.first_name, " ", primary.last_name) SEPARATOR "\n")'), 'primary_contacts'),
            [DB::expr("CONCAT_WS(' ', `contact`.`first_name`, `contact`.`last_name`)" ), 'full_name'],
			array('type.label','type'),
			array('subtype.subtype','subtype'),
			array('family.family_name', 'family'),
			'address.address1', 'address.address2', 'address.address3', 'address.town', 'address.county', 'address.country', 'address.postcode', 'address.coordinates',
            array(DB::expr("IF(`c_notif_m`.`country_dial_code` IS NOT NULL AND `c_notif_m`.`country_dial_code` != '' ,CONCAT_WS(' ', '+', `c_notif_m`.`country_dial_code`, `c_notif_m`.`dial_code`, `c_notif_m`.`value`), '')" ), 'mobile'),
            array('c_notif_e.value', 'email'),
			array('school.id', 'school_id'), array('school.name', 'school'),
			array('year.id','year_id'), 'year.year',
			array(DB::expr('group_concat(DISTINCT(has_role.role_id) SEPARATOR ",")'), 'role_ids'),
			array(DB::expr('group_concat(DISTINCT(role.name) SEPARATOR ",")'), 'role'),
			array(DB::expr('group_concat(DISTINCT(role.stub) SEPARATOR ",")'), 'stub')
		)
            ->from(array(self::CONTACTS_TABLE,              'contact'))
            ->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'LEFT')->on('contact.id', '=', 'has_role.contact_id')
            ->join(array(self::ROLE_TABLE,          'role'   ), 'LEFT')->on('has_role.role_id', '=', 'role.id')
			->join(array(self::CONTACTS_TYPE_TABLE, 'type'   ), 'LEFT')->on('contact.type',      '=', 'type.contact_type_id')
			->join(array(self::CONTACTS_SUBTYPE_TABLE,'subtype' ), 'LEFT')->on('contact.subtype_id','=','subtype.id')
            ->join(array(self::FAMILY_TABLE,        'family' ), 'LEFT')->on('contact.family_id', '=', 'family.family_id')
            ->join(array(self::ADDRESS_TABLE,       'address'), 'LEFT')->on('contact.residence', '=', 'address.address_id')
			->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'c_notif_m'), 'LEFT')->on('contact.notifications_group_id', '=', 'c_notif_m.group_id')->on('c_notif_m.deleted', '=', DB::expr(0))->on('c_notif_m.notification_id', '=', DB::expr(2))
            ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'c_notif_e'), 'LEFT')->on('contact.notifications_group_id', '=', 'c_notif_e.group_id')->on('c_notif_e.deleted', '=', DB::expr(0))->on('c_notif_e.notification_id', '=', DB::expr(1))
			->join(array('plugin_courses_locations', 'school'), 'LEFT')->on('contact.school_id', '=', 'school.id')->on('school.publish',  '=', DB::expr(1))->on('school.delete',  '=', DB::expr(0))->on('school.location_type_id', '=', DB::expr(10))
            ->join(array('plugin_courses_years',    'year'   ), 'LEFT')->on('contact.year_id',   '=', 'year.id')->on('year.publish', '=', DB::expr(1))->on('year.delete', '=', DB::expr(0))
            ->join(array(self::CONTACTS_TABLE,      'primary'), 'LEFT')->on('family.family_id',  '=', 'primary.family_id')->on('primary.is_primary', '=', DB::expr(1))->on('primary.publish', '=', DB::expr(1))->on('primary.delete', '=', DB::expr(0))
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'business_relations'), 'LEFT')->on('contact.id', '=', 'business_relations.child_id')
            ->join([Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE, 'cht'], 'left')->on('cht.contact_id', '=', 'contact.id')
            ->join([Model_Contacts3_Tag::CONTACT_TAG_TABLE,     'tag'], 'left')->on('cht.tag_id', '=', 'tag.id')
            ->group_by('contact.id')
            ->where('contact.delete', '=', 0)
            ->order_by('date_modified', 'desc');
        
        $query = self::where_clauses($query, $where_clauses);
        
        if (is_numeric($limit)) {
            $query->limit($limit);
        }
        return $query->execute()->as_array();
    }

	/** Get all members of a family
	 * @param int   $family_id  the ID of the family to get members from
	 * @return array of contacts
	 */
	public static function get_family_members($family_id, $has_preference = array())
	{
        if ($family_id == null) {
            return array();
        }
		$members_q = DB::select('contacts.*', 'year.year', DB::expr('GROUP_CONCAT(roles.stub) AS `has_roles`'))
			->from(array(self::CONTACTS_TABLE, 'contacts'))
                ->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_roles'), 'left')->on('contacts.id', '=', 'has_roles.contact_id')
                ->join(array(self::ROLE_TABLE, 'roles'), 'left')->on('has_roles.role_id', '=', 'roles.id')
                ->join(array('plugin_courses_years', 'year'), 'left')->on('contacts.year_id', '=', 'year.id')
			->where('contacts.family_id', '=', $family_id)
			->and_where('contacts.delete', '=', 0)
            ->group_by('contacts.id');

        if (!empty($has_preference)) {
            $members_q->join(array(self::CONTACT_PREFERENCES_RELATION_TABLE, 'has_preferences'), 'inner')
                ->on('contacts.id', '=', 'has_preferences.contact_id');
            $members_q->join(array(self::PREFERENCES_TABLE, 'preferences'), 'inner')
                ->on('has_preferences.preference_id', '=', 'preferences.id');
            $members_q->and_where('preferences.stub', 'in', $has_preference);
        }

        $members = $members_q->execute()->as_array();

        foreach ($members as $i => $member) {
            $members[$i]['has_roles'] = explode(',', $members[$i]['has_roles']);
            $members[$i]['notifications'] = DB::select('notifications.*')
                ->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'notifications'))
                ->where('deleted', '=', 0)
                ->and_where('notification_id', '=', 2)
                ->and_where('group_id', '=', $member['notifications_group_id'])
                ->execute()
                ->as_array();
        }
        return $members;
	}

	/*
	 * Get results for datatable
	 *
	 */
	public static function get_for_datatable($filters)
	{
        $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';
        //$has_student_id_column = Model_Contacts3::uses_student_ids();
        $contacts3_list_display_student_id = Settings::instance()->get('contacts3_list_display_student_id') == 1;

		$output    = array();
		// Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
		// These must be ordered, as they appear in the resultant table and there must be one per column
		$columns   = array();
		$columns[] = 'contact.id';

        if ($contacts3_list_display_student_id) {
            $columns[] = 'contact.student_id';
        }

        $columns[] = DB::expr("CONCAT_WS(' ', `contact`.`title`, `contact`.`first_name`, `contact`.`last_name`)");
        $columns[] = 'type.label';
		$columns[] = 'role.name';
        $columns[] = 'tag.label';
		$columns[] =  DB::expr("IF(`mobile`.`country_dial_code` IS NOT NULL AND`mobile`.`country_dial_code` != '' ,CONCAT_WS(' ', '+', `mobile`.`country_dial_code`, `mobile`.`dial_code`, `mobile`.`value`), '')" );
        if (Settings::instance()->get('contacts_create_family') == 1) {
            $columns[] = 'family.family_name';
        }
		$columns[] = DB::expr("if(contact_invitation.invited_email is not null, 'Invite pending', if(user.id is not null, 'Has site access', 'No site access'))");
		$columns[] = 'contact.is_primary';
		$columns[] = NULL; // not searching the primary contacts column, as it would need a HAVING, rather than a WHERE
		$columns[] = DB::expr("CONCAT_WS(', ', `address`.`address1`, `address`.`address2`)");
		$columns[] = 'school.name';
		$columns[] = 'year.year';
		$columns[] = 'contact.date_modified';

		// Two small queries to get these IDs, rather than adding more JOINs to the master query
		$mobile_notif_id         = @DB::select('id')->from(self::NOTIFICATIONS_TABLE)->where('stub', '=', 'mobile')->execute()->get('id', 0);
		$school_location_type_id = DB::select('id')
            ->from('plugin_courses_providers_types')
            ->where('type', '=', 'School')
            ->where('publish', '=', 1)->where('delete', '=', 0)
            ->execute()->get('id', 0);

		$q   = DB::select(
			DB::expr('SQL_CALC_FOUND_ROWS contact.id'),
			array(DB::expr("CONCAT_WS(' ', `contact`.`title`, `contact`.`first_name`, `contact`.`last_name`)"), 'full_name'),
			'contact.is_primary',
			array(DB::expr("if(contact_invitation.invited_email is not null, 'Invite pending', if(user.id is not null, 'Has site access', 'No site access')) as `user_status`")),
			array(DB::expr("IF(`contact`.`is_primary` = 1, 'yes', 'no')"), 'primary'),
			'contact.family_id', 'contact.date_created', 'contact.date_modified',
			// array(DB::expr('group_concat(concat(primary.title, " ", primary.first_name, " ", primary.last_name) SEPARATOR "\n")'), 'primary_contacts'),
			array('type.label','type'),
			array('family.family_name', 'family'),
			'address.address1', 'address.address2',
			array(DB::expr("IF(`mobile`.`country_dial_code` IS NOT NULL AND`mobile`.`country_dial_code` != '' ,CONCAT_WS(' ', '+', `mobile`.`country_dial_code`, `mobile`.`dial_code`, `mobile`.`value`), '')" ), 'mobile'),
			array('school.id', 'school_id'), array('school.name', 'school'),
			array('year.id','year_id'), 'year.year',
			array(DB::expr('group_concat(DISTINCT(has_role.role_id) SEPARATOR ",")'), 'role_ids'),
			array(DB::expr('group_concat(DISTINCT(role.name) SEPARATOR ",")'), 'role'),
			array(DB::expr('group_concat(DISTINCT(role.stub) SEPARATOR ",")'), 'stub'),
            'contact.student_id',
            [DB::expr("GROUP_CONCAT(DISTINCT `tag`.`label` ORDER BY `tag`.`label` SEPARATOR '\n')"), 'tags']
		)
			->from(array(self::CONTACTS_TABLE,      'contact'))
			->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'LEFT')->on('contact.id', '=', 'has_role.contact_id')
            ->join(array(self::ROLE_TABLE,          'role'   ), 'LEFT')->on('has_role.role_id', '=', 'role.id')
			->join(array(self::CONTACTS_TYPE_TABLE, 'type'   ), 'LEFT')->on('contact.type',      '=', 'type.contact_type_id')
			->join(array(self::FAMILY_TABLE,        'family' ), 'LEFT')->on('contact.family_id', '=', 'family.family_id')
			->join(array(self::ADDRESS_TABLE,       'address'), 'LEFT')->on('contact.residence', '=', 'address.address_id')
			->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobile'), 'LEFT')->on('mobile.group_id',   '=', 'contact.notifications_group_id')->on('mobile.deleted', '=', DB::expr('0'))->on('mobile.notification_id', '=', DB::expr('2'))
			->join(array('plugin_courses_providers', 'school'), 'LEFT')->on('contact.school_id', '=', 'school.id')->on('school.publish', '=', DB::expr('1'))->on('school.delete', '=', DB::expr('0'))->on('school.type_id', '=', DB::expr('2'))
			->join(array('plugin_courses_years', 'year'), 'LEFT')->on('contact.year_id',   '=', 'year.id')->on('year.publish', '=', DB::expr('1'))->on('year.delete', '=', DB::expr('0'))
			->join(array('plugin_contacts3_invitations', 'contact_invitation'), 'left')->on('contact_invitation.invited_contact_id', '=', 'contact.id')->on('status', '=', DB::expr('"Wait"'))
            ->join(array('engine_users', 'user'), 'LEFT')->on('contact.linked_user_id', '=', 'user.id')
            ->join([Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE, 'cht'], 'left')->on('cht.contact_id', '=', 'contact.id')
            ->join([Model_Contacts3_Tag::CONTACT_TAG_TABLE,     'tag'], 'left')->on('cht.tag_id', '=', 'tag.id')
            ->group_by('contact.id')
			->where('contact.delete', '=', 0);

        if (is_numeric(@$filters['check_permission_user_id'])) {
            $filter1 = DB::select('contact3_id')
                ->from(self::TABLE_PERMISSION_LIMIT)
                ->where('user_id', '=', $filters['check_permission_user_id']);
            $q->and_where_open();
            $q->or_where('contact.id', 'in', $filter1);
            $q->and_where_close();
        }

		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$q->and_where_open();
			for ($i = 0; $i < count($columns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $columns[$i] != '')
				{
                    $filters['sSearch'] = preg_replace('/\s+/', '%', $filters['sSearch']);
					$q->or_where($columns[$i],'like','%'.$filters['sSearch'].'%');
				}
			}
			$q->and_where_close();
		}
		// Individual column search
		for ($i = 0; $i < count($columns); $i++)
		{
			if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
			{
                $filters['sSearch_'.$i] = preg_replace('/\s+/', '%', $filters['sSearch_'.$i]); //replace spaces with %
				$q->and_where($columns[$i],'like','%'.$filters['sSearch_'.$i].'%');
			}
		}

		// Don't allow "Show all" to work here. There are too many records for that.
		if (empty($filters['iDisplayLength']) OR $filters['iDisplayLength'] == -1)
		{
			$filters['iDisplayLength'] = 10;
		}

		// Limit. Only show the number of records for this paginated page
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$q->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$q->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order
		if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0']))
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($columns[$filters['iSortCol_'.$i]] != '')
				{
					$q->order_by($columns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$q->order_by('contact.date_modified', 'desc');
		$results = $q->execute()->as_array();

		$output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$output['iTotalRecords']        = count($results); // displayed results
		$output['aaData']               = array();

		foreach ($results as $result)
		{
            // Get primary contacts for each result.
			// Using JOINS/subqueries to get this in the master query is significantly slower when contacts are in the thousands
			$primary_contacts_string = '';
			// If the type of contact is an organisation, their primary contacts are org reps
			if($result['type'] == "Organisation") {
                $primary_contacts = DB::select(array('c3_ind.first_name', 'first_name'), array('c3_ind.last_name'))
                    ->from(array(self::CONTACTS_TABLE, 'c_org'))
                    ->join(array(self::CONTACT_RELATIONS_TABLE, 'c3_r'), 'inner')->on('c_org.id', '=',
                        'c3_r.parent_id')
                    ->join(array(self::CONTACTS_TABLE, 'c3_ind'), 'inner')->on('c3_r.child_id', '=',
                        'c3_ind.id')
                    ->join(array(self::CONTACTS_TYPE_TABLE, 'c3_ind_type'), 'left')->on('c3_ind.type', '=',
                        'c3_ind_type.contact_type_id')
                    ->where('c_org.id', '=', $result['id'])
                    ->where('c3_ind_type.name', '=', 'org_rep')
                    ->order_by('c3_ind.first_name', 'asc')
                    ->limit(3)
                    ->execute()->as_array();
            } else if(!empty($result['family_id'])) {
                $primary_contacts = DB::select('first_name', 'last_name')
                    ->from('plugin_contacts3_contacts')
                    ->where('is_primary', '=', 1)
                    ->where('family_id', '=', $result['family_id'])
                    ->where('delete', '=', 0)
                    ->limit(3)
                    ->execute()
                    ->as_array();
            } else {
                $primary_contacts = array();
            }
			
			foreach ($primary_contacts as $primary_contact)
			{
				$primary_contacts_string .= $primary_contact['first_name'].' '.$primary_contact['last_name'].'<br />';
			}
			// Strip trailing br
			$primary_contacts_string = preg_replace('{(<br />)+$}i', '', $primary_contacts_string);

			$row   = array();
			$row[] = $result['id'];
            if ($contacts3_list_display_student_id) {
                $row[] = htmlspecialchars($result['student_id']);
            }
            /*if ($has_cds) {
                $row[] = $result['remote_id'];
            }*/
            $row[] = '<a href="/admin/contacts3/add_edit_contact/'.$result['id'].'">'.$result['full_name'].'</a>';
            $row[] = $result['type'];
			$row[] = $result['role'];
            $row[] = str_replace("\n", '<br />', $result['tags']);
			$row[] = $result['mobile'];
            if (Settings::instance()->get('contacts_create_family') == 1) {
                $row[] = $result['family'];
            }
            $row[] = $result['user_status'];
			$row[] = ($result['is_primary']) ? 'yes' : 'no';
			$row[] = $primary_contacts_string;
			$row[] = trim(trim($result['address1'].', '.$result['address2']), ',');
			$row[] = ($result['school'] != 0 OR ! is_null($result['school'])) ? $result['school'] : '';
			$row[] = $result['year'];
			$row[] = date($date_format, strtotime($result['date_modified']));
			$output['aaData'][] = $row;
		}
		$output['sEcho'] = intval($filters['sEcho']);

		return json_encode($output);
	}
    
    public static function get_for_organisation_datatable($filters)
    {
        $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';
        $output = array();
        // Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
        // These must be ordered, as they appear in the resultant table and there must be one per column
        $columns = array();
        $columns[] = 'contact.id';
        $columns[] = DB::expr("CONCAT_WS(' ', `contact`.`title`, `contact`.`first_name`, `contact`.`last_name`)");
        if(Settings::instance()->get('display_sub_contact_types') == '1')
        {
        $columns[] = DB::expr("IF(subtype.subtype <> '0', subtype.subtype, 'General')");
        }
        $columns[] = DB::expr("IF(`mobile`.`country_dial_code` IS NOT NULL AND`mobile`.`country_dial_code` != '' ,CONCAT_WS(' ', '+', `mobile`.`country_dial_code`, `mobile`.`dial_code`, `mobile`.`value`), '')");
        $columns[] = DB::expr("CONCAT_WS(', ', `address`.`address1`, `address`.`address2`)");

       
        $q = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS contact.id'),
            array(
                DB::expr("CONCAT_WS(' ', `contact`.`title`, `contact`.`first_name`, `contact`.`last_name`)"),
                'full_name'
            ),
            array(DB::expr("IF(subtype.subtype <> '0', subtype.subtype, 'General')"), 'subtype'),
            array(DB::expr("IF(`mobile`.`country_dial_code` IS NOT NULL AND`mobile`.`country_dial_code` != '' ,CONCAT_WS(' ', '+', `mobile`.`country_dial_code`, `mobile`.`dial_code`, `mobile`.`value`), '')"), 'mobile' ),
           'address.address1', 'address.address2')
           ->from(array(self::CONTACTS_TABLE, 'contact'))
            ->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'LEFT')->on('contact.id', '=',
                'has_role.contact_id')
            ->join(array(self::ROLE_TABLE, 'role'), 'LEFT')->on('has_role.role_id', '=', 'role.id')
            ->join(array(self::CONTACTS_TYPE_TABLE, 'type'), 'LEFT')->on('contact.type', '=', 'type.contact_type_id')
            ->join(array(self::CONTACTS_SUBTYPE_TABLE, 'subtype'), 'LEFT')->on('contact.subtype_id', '=', 'subtype.id')
            ->join(array(self::ADDRESS_TABLE, 'address'), 'LEFT')->on('contact.residence', '=', 'address.address_id')
            ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobile'), 'LEFT')->on('mobile.group_id', '=',
               'contact.notifications_group_id')->on('mobile.deleted', '=', DB::expr('0'))->on('mobile.notification_id',
               '=', DB::expr('2'))
            ->group_by('contact.id')
            ->where('contact.delete', '=', 0)
            ->where('type.label', '=', 'Organisation');
        
        if (is_numeric(@$filters['check_permission_user_id'])) {
            $filter1 = DB::select('contact3_id')
                ->from(self::TABLE_PERMISSION_LIMIT)
                ->where('user_id', '=', $filters['check_permission_user_id']);
            $q->and_where_open();
            $q->or_where('contact.id', 'in', $filter1);
            $q->and_where_close();
        }
       
        // Don't allow "Show all" to work here. There are too many records for that.
        if (empty($filters['iDisplayLength']) OR $filters['iDisplayLength'] == -1) {
            $filters['iDisplayLength'] = 10;
        }
    
        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $q->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $columns[$i] != '') {
                    $filters['sSearch'] = preg_replace('/\s+/', '%', $filters['sSearch']);
                    $q->or_where($columns[$i], 'like', '%' . $filters['sSearch'] . '%');
                }
            }
            $q->and_where_close();
        }
        // Individual column search
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $filters['sSearch_' . $i] != '') {
                $filters['sSearch_' . $i] = preg_replace('/\s+/', '%',
                    $filters['sSearch_' . $i]); //replace spaces with %
                $q->and_where($columns[$i], 'like', '%' . $filters['sSearch_' . $i] . '%');
            }
        }
        
        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $q->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $q->offset(intval($filters['iDisplayStart']));
            }
        }
        // Order
        if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if ($columns[$filters['iSortCol_' . $i]] != '') {
                    $q->order_by($columns[$filters['iSortCol_' . $i]], $filters['sSortDir_' . $i]);
                }
            }
        }
        $q->order_by('contact.date_modified', 'desc');
        
        $results = $q->execute()->as_array();
        
        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT,
            'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords'] = count($results); // displayed results
        $output['aaData'] = array();
        
        foreach ($results as $result) {
            $row = array();
            $row[] = $result['id'];
            $row[] = $result['full_name'];
            if (Settings::instance()->get('display_sub_contact_types') == '1') {
                $row[] = $result['subtype'];
            }
            $row[] = $result['mobile'];
            $row[] = trim(trim($result['address1'] . ', ' . $result['address2']), ',');
            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($filters['sEcho']);
        
        return json_encode($output);
    }
    
    public static function get_for_organisation_members_datatable($filters)
    {
        $output = array();
        // Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
        // These must be ordered, as they appear in the resultant table and there must be one per column
        $columns = array();
        $columns[] = 'c3.first_name';
        $columns[] = 'c3.last_name';
        $columns[] = 'IF(`mobile`.`country_dial_code` IS NOT NULL AND `mobile`.`country_dial_code` != \'\' , CONCAT_WS(\' \', \'+\', `mobile` . `country_dial_code`, `mobile` . `dial_code`, `mobile` . `value`), `mobile`. `value`) ';
        $columns[] = 'email.value';
    
        $q = DB::select(
            array("c3.first_name", 'first_name'),
            array("c3.last_name", 'last_name'),
            array("email.value", 'email'),
            array(DB::expr("IF(`mobile`.`country_dial_code` IS NOT NULL AND `mobile`.`country_dial_code` != '' , CONCAT_WS(' ', '+', `mobile` . `country_dial_code`, `mobile` . `dial_code`, `mobile` . `value`), `mobile`. `value`)"), 'mobile'))
            ->from(array(self::CONTACT_RELATIONS_TABLE, 'c3r'))
            ->join(array(self::CONTACTS_TABLE, 'c3'), 'INNER')->on('c3r.child_id', '=',
                'c3.id')
            ->join(array('plugin_contacts3_notification_groups', 'c3n'), 'LEFT')->on('c3n.id', '=', 'c3.notifications_group_id')
            ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobile'), 'LEFT')->on('c3n.id', '=', 'mobile.group_id')
            ->on('mobile.notification_id', '=', DB::expr('(SELECT id FROM plugin_contacts3_notifications
            WHERE `name` = "mobile")'))
            ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'email'), 'LEFT')->on('c3n.id', '=',
                'email.group_id')
            ->on('email.notification_id', '=', DB::expr('(SELECT id FROM plugin_contacts3_notifications
            WHERE `name` = "email")'))
            ->group_by('c3.id')
            ->where('c3.delete', '=', 0);
    
        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $q->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $columns[$i] != '') {
                    $filters['sSearch'] = preg_replace('/\s+/', '%', $filters['sSearch']);
                    $q->or_where($columns[$i], 'like', '%' . $filters['sSearch'] . '%');
                }
            }
            $q->and_where_close();
        }
        
        // Don't allow "Show all" to work here. There are too many records for that.
        if (empty($filters['iDisplayLength']) OR $filters['iDisplayLength'] == -1) {
            $filters['iDisplayLength'] = 10;
        }
        
        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $q->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $q->offset(intval($filters['iDisplayStart']));
            }
        }
        // Order
        if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if (in_array(strtolower($filters['sSortDir_' . $i]), array('asc', 'desc'))) {
                    if ($columns[$filters['iSortCol_' . $i]] != '') {
                        $q->order_by($columns[$filters['iSortCol_' . $i]], $filters['sSortDir_' . $i]);
                    }
                }
            }
        }
        $q->order_by('c3.date_modified', 'desc');
        if(isset($filters['org_contact_id']) && is_numeric($filters['org_contact_id'])) {
            $q->where('c3r.parent_id', '=', $filters['org_contact_id']);
        }
        $results = $q->execute()->as_array();
        
        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT,
            'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords'] = count($results); // displayed results
        $output['aaData'] = array();
        
        foreach ($results as $result) {
            $row = array();
            $row[] = $result['first_name'];
            $row[] = $result['last_name'];
            $row[] = $result['email'] ?? '';
            $row[] = $result['mobile'] ?? '';
            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($filters['sEcho']);
        
        return json_encode($output);
    }
    public static function get_for_department_datatable($filters)
    {
        $output = array();
        // Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
        // These must be ordered, as they appear in the resultant table and there must be one per column
        $columns = array();
        $columns[] = 'contact.id';
        $columns[] = DB::expr("CONCAT_WS(' ', `contact`.`first_name`, `contact`.`last_name`)");
        
        $q = DB::select(
            'contact.id',
            DB::expr("CONCAT_WS(' ', `contact`.`first_name`, `contact`.`last_name`) as 'name'"))
            ->from(array(self::CONTACTS_TABLE, 'contact'))
            ->join(array(self::CONTACTS_TYPE_TABLE, 'contact_type'), 'LEFT')
            ->on('contact.type', '=', 'contact_type.contact_type_id')
            ->group_by('contact.id')
            ->where('contact.delete', '=', 0)
            ->where('contact_type.label', '=', 'Department');
        
        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $q->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $columns[$i] != '') {
                    $filters['sSearch'] = preg_replace('/\s+/', '%', $filters['sSearch']);
                    $q->or_where($columns[$i], 'like', '%' . $filters['sSearch'] . '%');
                }
            }
            $q->and_where_close();
        }
        
        // Don't allow "Show all" to work here. There are too many records for that.
        if (empty($filters['iDisplayLength']) OR $filters['iDisplayLength'] == -1) {
            $filters['iDisplayLength'] = 10;
        }
        
        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $q->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $q->offset(intval($filters['iDisplayStart']));
            }
        }
        // Order
        if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if ($columns[$filters['iSortCol_' . $i]] != '') {
                    $q->order_by($columns[$filters['iSortCol_' . $i]], $filters['sSortDir_' . $i]);
                }
            }
        }
        $q->order_by('contact.date_modified', 'desc');
        
        $results = $q->execute()->as_array();
        
        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT,
            'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords'] = count($results); // displayed results
        $output['aaData'] = array();
        
        foreach ($results as $result) {
            $row = array();
            $row[] = $result['id'];
            $row[] = $result['name'];
            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($filters['sEcho']);
        
        return json_encode($output);
    }

    public static function get_all_contacts_by_phone_number($number)
    {
        return DB::select('c.id', 'c.title', 'c.first_name', 'c.last_name')
            ->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'cn'))
            ->join(array(self::CONTACTS_TABLE,      'c'))->on('cn.group_id',        '=', 'c.id')
            ->join(array(self::NOTIFICATIONS_TABLE, 'n'))->on('cn.notification_id', '=', 'n.id')
            ->where('cn.value', '=', $number)->and_where('n.stub', 'IN', array('mobile', 'landline'))
            ->and_where('cn.publish', '=', 1)->and_where('cn.deleted', '=', 0)
            ->and_where('c.publish',  '=', 1)->and_where('c.delete',   '=', 0)
            ->and_where('n.publish',  '=', 1)->and_where('n.deleted',  '=', 0)
            ->execute()->as_array();
    }

    public static function get_contact_type_by_name($contact_name = 'Family')
    {
        $contact_name = ucfirst(strtolower($contact_name));
        $q = DB::select()->from(self::CONTACTS_TYPE_TABLE)
            ->where('label','=',$contact_name)
            ->or_where('name', '=', $contact_name)
            ->execute()->as_array();
        return count($q) > 0 ? $q[0] : NULL;
    }
    
    public static function get_contacts_by_subtype($subtype_name, $contact_term = FALSE) {
        $q = DB::select(array(DB::expr("CONCAT(c.first_name, ' ', c.last_name)"), 'label'), array('c.id', 'id'))->from(array(self::CONTACTS_TABLE, 'c'))->where('s.subtype', '=', $subtype_name);
        if($contact_term != FALSE) {
            $q = $q->where(DB::expr("CONCAT(c.first_name, ' ', c.last_name)"), 'like',
                '%' . $contact_term . '%');
        }
        $q = $q->join(array('plugin_contacts3_contacts_subtypes', 's'), 'INNER')->on('s.id', '=', 'c.subtype_id')->execute()->as_array();
        return $q;
    }
    
    public static function get_types($including_unpublished = false)
    {
        $rows = DB::select()->from(self::CONTACTS_TYPE_TABLE);
        if(!$including_unpublished) {
            $rows->where('publish', '=', '1');
        }
        $result = $rows->order_by('display_name', 'asc')->execute()->as_array();
        return $result;
    }
    
    public static function get_contact_types_datatable($filters)
    {
        $output = array();
        $columns = array();
        $columns[] = 'contact_types.contact_type_id';
        $columns[] = 'contact_types.label';
        $columns[] = 'contact_types.publish';
        
        $q = DB::select()->from(array(self::CONTACTS_TYPE_TABLE, 'contact_types'));
        
        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $q->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $columns[$i] != '') {
                    $filters['sSearch'] = preg_replace('/\s+/', '%', $filters['sSearch']);
                    $q->or_where($columns[$i], 'like', '%' . $filters['sSearch'] . '%');
                }
            }
            $q->and_where_close();
        }
        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $q->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $q->offset(intval($filters['iDisplayStart']));
            }
        }
        // Order
        if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if ($columns[$filters['iSortCol_' . $i]] != '') {
                    $q->order_by($columns[$filters['iSortCol_' . $i]], $filters['sSortDir_' . $i]);
                }
            }
        }
        $q->where('contact_types.publish', '=', 1 );
        $results = $q->order_by('display_name', 'asc')->execute()->as_array();
        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT,
            'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT,
            'SELECT count(contact_type_id) AS total from plugin_contacts3_contact_type where publish = 1')->execute()->get('total'); // total number of results
    
        $output['aaData'] = array();
    
        foreach ($results as $result) {
            $row = array();
            $row[] = $result['contact_type_id'];
            $row[] = $result['label'];
            if ($result['publish'] == '1') {
                $row[] = '<a href="#" class="publish" data-publish="0" data-id="' . $result['contact_type_id'] . '"><i class="icon-ok"></i></a>';
            } else {
                $row[] = '<a href="#" class="publish" data-publish="1" data-id="' . $result['contact_type_id'] . '"><i class="icon-ban-circle"></i></a>';
            }
            $row[] = ($result['deletable'] != '1') ? '' :
                 View::factory('snippets/btn_dropdown')
                ->set('title', ['text' => '<span class="icon-ellipsis-h"></span>', 'html' => true])
                ->set('sr_title', 'Actions')
                ->set('btn_type', 'outline-primary')
                ->set('options_align', 'right')
                /**/
                ->set('options', [
                    [
                        'type' => 'button',
                        'attributes' => ['class' => 'contact-type-toggle', 'data-id' => $result['contact_type_id']],
                        'title' => ['html' => true, 'text' => '<span class="icon-pencil contact-type-toggle"></span> Edit']
                    ],
                    ['type' => 'button', 'attributes' => ['class' => 'delete', 'data-id' => $result['contact_type_id']],
                    'title' => ['html' => true, 'text' => '<span class="icon-close"></span> Delete']
                ]
                ])->render();
            $output['aaData'][] = $row;
        }
        $output['sEcho'] = intval($filters['sEcho']);
        return $output;
    }
    
    public static function get_contact_type_columns($contact_type_id) {
        $result = DB::select('contact_columns.id', 'contact_columns.name', 'contact_columns.label', 'contact_columns.table_column')
            ->from(array(self::CONTACTS_TYPE_COLUMNS_RELATION_TABLE, 'has_columns'))
                ->join(array(SELF::CONTACTS_TYPE_TABLE, 'contact_type'), 'inner')
                     ->on('has_columns.contact_type_id' ,'=', 'contact_type.contact_type_id')
                 ->join(array(SELF::CONTACTS_TYPE_COLUMNS_TABLE, 'contact_columns'))
                     ->on('has_columns.contact_type_column_id', '=', 'contact_columns.id')
            ->where('contact_type.contact_type_id', '=', $contact_type_id)->order_by('has_columns.priority', 'asc');
        return $result->execute()->as_array();
    }

    public static function get_generic_contact_type_datatable($filters) {
	    $type = self::find_type(str_replace('_', ' ', $filters['contact_type']));
	    $table_columns = Model_Contacts3::get_contact_type_columns($type['contact_type_id']);
	    $select_array = array();
	    $columns = array();
	    
        for($i = 0; $i < count($table_columns); $i ++) {
            if ($i == 0) {
                $select_array[] = array(DB::expr('SQL_CALC_FOUND_ROWS '. $table_columns[$i]['table_column']),
                    $table_columns[$i]['label']);
            } else {
                $select_array[] = array(DB::expr($table_columns[$i]['table_column']), $table_columns[$i]['label']);
            }
            
            $columns[] = DB::expr($table_columns[$i]['table_column']);
        }
        // Negative to using generics is we have to use so many joins to account for all possible scenarios...
	    $q = DB::select_array($select_array)
            ->from(array(self::CONTACTS_TABLE, 'contact'))
            ->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'LEFT')->on('contact.id', '=',
                'has_role.contact_id')
            ->join(array(self::ROLE_TABLE, 'role'), 'LEFT')->on('has_role.role_id', '=', 'role.id')
            ->join(array(self::CONTACTS_TYPE_TABLE, 'type'), 'LEFT')->on('contact.type', '=', 'type.contact_type_id')
            ->join(array(self::CONTACTS_SUBTYPE_TABLE, 'subtype'), 'LEFT')->on('contact.subtype_id', '=', 'subtype.id')
            ->join(array(self::FAMILY_TABLE, 'family'), 'LEFT')->on('contact.family_id', '=', 'family.family_id')
            ->join(array(self::ADDRESS_TABLE, 'address'), 'LEFT')->on('contact.residence', '=', 'address.address_id')
            ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobile'), 'LEFT')->on('mobile.group_id', '=',
                'contact.notifications_group_id')->on('mobile.deleted', '=', DB::expr('0'))
            ->on(DB::expr('mobile.notification_id in (2, 3)'), '', DB::expr(''))
            ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')->on('contact.notifications_group_id',
                '=', 'emails.group_id')->on('emails.notification_id', '=', DB::expr(1))
            ->join(array('plugin_courses_providers', 'school'), 'LEFT')->on('contact.school_id', '=',
                'school.id')->on('school.publish', '=', DB::expr('1'))->on('school.delete', '=',
                DB::expr('0'))->on('school.type_id', '=', DB::expr('2'))
            ->join(array('plugin_courses_years', 'year'), 'LEFT')->on('contact.year_id', '=',
                'year.id')->on('year.publish', '=', DB::expr('1'))->on('year.delete', '=', DB::expr('0'))
            ->join(array('countries', 'countries'), 'LEFT')->on('address.country' , '=', 'countries.code')
            ->join(array('engine_counties', 'counties'), 'LEFT')->on('address.county', '=', 'counties.id')
            // Get contact's possible bookings
            ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'LEFT')->on('contact.id', '=', 'bookings.contact_id')
            // Get booking's schedules
            ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'booking_schedules'), 'LEFT')->on('bookings.booking_id', '=',
                'booking_schedules.booking_id')
            // Get schedule's name
            ->join(array('plugin_courses_schedules', 'schedule'), 'LEFT')->on('booking_schedules.schedule_id',
                '=', 'schedule.id')
            ->join(array(Model_Courses::TABLE_COURSES, 'course'), 'LEFT')->on('schedule.course_id',
                '=', 'course.id')
            ->join(array(Model_Categories::TABLE_CATEGORIES, 'course_category'), 'LEFT')->on('course.category_id',
                '=', 'course_category.id')
            // Get contact's possible department - "department_contact"
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'contact_department_relation_id'), 'LEFT')
                ->on('contact.id', '=', 'contact_department_relation_id.child_id')->on('contact_department_relation_id.role', '=',
                DB::expr('"staff"'))
            ->join(array(self::CONTACTS_TABLE, 'department_contact'), 'LEFT')->on('contact_department_relation_id.parent_id', '=', 'department_contact.id')
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'contact_business_relation_id'), 'LEFT')
            ->on('department_contact.id', '=', 'contact_business_relation_id.child_id')->on('contact_business_relation_id.position',
                '=', DB::expr('"business"'))
            // Get that contact's possible business - "business_contact"
            ->join(array(self::CONTACTS_TABLE, 'business_contact'),
                'LEFT')->on('contact_business_relation_id.parent_id', '=', 'contact.id')
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'contact_department_business_relation_id'), 'LEFT')
            ->on('contact.id', '=', 'contact_department_business_relation_id.parent_id')->on('contact_department_business_relation_id.position',
                '=', DB::expr('"business"'))
            // Get the business' possible department - "business_department_contact"
            ->join(array(self::CONTACTS_TABLE, 'business_department_contact'),
                'LEFT')->on('contact_department_business_relation_id.child_id', '=', 'business_department_contact.id')
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'contact_main_department_contact_business_relation_id'), 'LEFT')
            ->on('business_department_contact.id', '=',
                'contact_main_department_contact_business_relation_id.parent_id')
            ->on('contact_main_department_contact_business_relation_id.role', '=', DB::expr('"staff"'))
            // Get the business' department's possible contact - "main_department_business_contact"
            ->join(array(self::CONTACTS_TABLE, 'main_department_business_contact'),
                'LEFT')->on('contact_main_department_contact_business_relation_id.child_id', '=', 'main_department_business_contact.id')
            // Organisation's contact
            ->join(array(self::CONTACT_RELATIONS_TABLE, 'organisation_relation_id'), 'LEFT')
                ->on('contact.id', '=', 'organisation_relation_id.child_id')
                ->on('organisation_relation_id.position', '=', DB::expr('"organisation"'))
            ->join(array(self::CONTACTS_TABLE, 'linked_organisation_contact'), 'LEFT')
                ->on('organisation_relation_id.parent_id', '=', 'linked_organisation_contact.id')
            ->group_by('contact.id');
        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '') {
            $q->and_where_open();
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($filters['bSearchable_' . $i]) AND $filters['bSearchable_' . $i] == "true" AND $columns[$i] != '') {
                    $filters['sSearch'] = preg_replace('/\s+/', '%', $filters['sSearch']);
                    $q->or_where($columns[$i], 'like', '%' . $filters['sSearch'] . '%');
                }
            }
            $q->and_where_close();
        }
        
        // Don't allow "Show all" to work here. There are too many records for that.
        if (empty($filters['iDisplayLength']) OR $filters['iDisplayLength'] == -1) {
            $filters['iDisplayLength'] = 10;
        }
    
        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1) {
            $q->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])) {
                $q->offset(intval($filters['iDisplayStart']));
            }
        }
    
        // Order
        if (isset($filters['iSortCol_0']) AND is_numeric($filters['iSortCol_0'])) {
            for ($i = 0; $i < $filters['iSortingCols']; $i++) {
                if ($columns[$filters['iSortCol_' . $i]] != '') {
                    $q->order_by($columns[$filters['iSortCol_' . $i]], $filters['sSortDir_' . $i]);
                }
            }
        }
    
        $q->order_by('contact.date_modified', 'desc')
            ->where('contact.delete', '=', 0)
                ->and_where_open()
                    ->where('type.contact_type_id', '=', $type['contact_type_id'])
                    ->or_where('subtype.subtype', '=', $type['label'])
                ->and_where_close();
        $results =   $q->execute()->as_array();
        $output['iTotalDisplayRecords'] = DB::query(Database::SELECT,
            'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $output['iTotalRecords'] = count($results); // displayed results
        $output['aaData'] = array();
        foreach($results as $result) {
            $result_row = array();
            foreach($table_columns as $table_column) {
                $result_row[] = $result[$table_column['label']];
            }
            $output['aaData'][] = $result_row;
        }
        return $output;
    }
    public static function temporary_salutation_dropdown()
    {
        return array('Mr','Mrs','Miss','Ms');
    }
    
    public static function is_contact_has_privilege_preference($contact_id, $preference_stub){
        $privileges_preferences = Model_Preferences::get_family_preferences();
        $contact_privileges_preferences = Model_Contacts3::get_contact_privileges_preferences($contact_id);

        foreach($privileges_preferences as $preference){
            if($preference['stub'] == $preference_stub){
                foreach($contact_privileges_preferences as $contact_privileges){
                    if($contact_privileges['preference_id'] == $preference['id']){
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function get_contact_privileges_preferences($contact_id)
    {

        return DB::select('preference_id')->from(self::CONTACT_PREFERENCES_RELATION_TABLE)
            ->join(self::PREFERENCES_TABLE)
            ->on(self::CONTACT_PREFERENCES_RELATION_TABLE.'.preference_id','=',self::PREFERENCES_TABLE.'.id')
            ->where(self::PREFERENCES_TABLE.'.group','=',"family_permission")
            ->where('contact_id', '=', $contact_id)
            ->execute()->as_array();
    }

    public function has_preference($privilege_name)
    {
        $results = DB::select()
            ->from(array(self::CONTACT_PREFERENCES_RELATION_TABLE, 'contact_preference'))
            ->join(array(self::PREFERENCES_TABLE, 'preference'))
            ->on('contact_preference.preference_id', '=' ,'preference.id')
            ->where('contact_id', '=', $this->id)
            ->where('preference.stub', '=', $privilege_name)
            ->execute()
            ->as_array();

        return (count($results) > 0);
    }

    public static function get_notification_types()
    {
        return DB::select('id', 'name', 'stub')->from(self::NOTIFICATIONS_TABLE)
            ->where('publish', '=', 1)->and_where('deleted', '=', 0)
            ->execute()->as_array();
    }

    public static function get_all_roles($family_staff = null)
    {
        $roles = DB::select('id', 'name', 'stub')->from(self::ROLE_TABLE)
	            ->where('publish', '=', 1)->and_where('deleted', '=', 0)
    	        ->execute()->as_array();
		if($family_staff == 1){
			return array_slice($roles, 0, 3, true);
		} else if($family_staff == 2){
			return array_slice($roles, 3, 3, true);
		} else {
			return $roles;
		}
    }

    public static function get_contact_role_by_name($name)
    {
        return DB::select()->from(self::ROLE_TABLE)->where('name', '=', $name)
            ->where('deleted', '=', 0)->execute()->current();
    }
    
    public static function get_by_term($term, $type = null, $linked_contact_id = null)
    {
        /**
         * Terms:
         * Mobile - Family
         */
        $result = array();
        $q = DB::select(
            't1.family_id', 't1.id','t1.first_name','t1.last_name','t2.country_dial_code', 't2.dial_code','t2.value','has_role.role_id',
            'address.address1', 'address.address2', 'address.address3', 'address.country', 'address.county', 'address.postcode', 'address.town',
            array('emails.value', 'email'),
            DB::expr('GROUP_CONCAT(DISTINCT roles.name) as role'),
            array('organisations.id', 'organisation_id'),
            'organisations.primary_biller_id',
            ['organisation_contacts.id', 'organisation_contact_id'],
            [DB::expr("CONCAT(IFNULL(`organisation_contacts`.`first_name`, ''), ' ', IFNULL(`organisation_contacts`.`last_name`, ''))"), 'organisation_contact_name']
        )
            ->from(array(self::CONTACTS_TABLE,'t1'))
            ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'t2'), 'left')->on('t1.notifications_group_id','=','t2.group_id')->on('t2.notification_id', '=', DB::expr(2))
            ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'emails'), 'left')->on('t1.notifications_group_id','=','emails.group_id')->on('emails.notification_id', '=', DB::expr(1))
            ->join(array(Model_Residence::ADDRESS_TABLE,'address'), 'left')->on('t1.residence','=','address.address_id')
			->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'left')->on('t1.id', '=', 'has_role.contact_id')
            ->join(array(self::ROLE_TABLE, 'roles'), 'left')->on('has_role.role_id', '=', 'roles.id')
            ->join(array(self::CONTACTS_TYPE_TABLE, 'types'), 'left')->on('t1.type', '=', 'types.contact_type_id')
            ->join(array(Model_Organisation::CONTACT_ORGANISATION_TABLE, 'organisations'), 'left')->on('t1.id', '=', 'organisations.contact_id')
            ->join([self::CONTACT_RELATIONS_TABLE, 'relations'], 'left')
                ->on('relations.child_id', '=', 't1.id')
                ->on('relations.position', '=', DB::expr("'organisation'"))
            ->join([self::CONTACTS_TABLE, 'organisation_contacts'], 'left')
                ->on('relations.parent_id', '=', 'organisation_contacts.id')
            //->where('t2.notification_id','=',2)
            ->and_where_open()
            ->where('t1.first_name','LIKE','%'.$term.'%')
            ->or_where('t1.last_name','LIKE','%'.$term.'%')
            ->or_where('t2.value','LIKE','%'.$term.'%')
            ->or_where('emails.value','LIKE','%'.$term.'%')
            ->or_where(DB::expr("CONCAT(t1.first_name, ' ', t1.last_name)"), '=', $term)
            ->or_where(DB::expr("CONCAT(organisation_contacts.first_name, ' ', organisation_contacts.last_name)"), '=', $term)
            ->and_where_close();
        if ($type) {
            $q->and_where('types.name', '=', $type);
        }
        if ($linked_contact_id) {
            $q->and_where('relations.parent_id', '=', $linked_contact_id);
        }

            $q = $q
            ->and_where('t1.delete', '=', 0)
            ->and_where('t1.publish', '=', 1)
			->group_by('t1.id')
            ->limit(100)
            ->execute()->as_array();
        foreach($q AS $key=>$item)
        {
            $phone_number = !empty($q[$key]['country_dial_code']) ? '+' . $q[$key]['country_dial_code'] .$q[$key]['dial_code']. $q[$key]['value']: $q[$key]['value'];
            $contact = array(
                'id' => $q[$key]['id'],
                'value' => $q[$key]['first_name'].' '.$q[$key]['last_name'].' - ' . $phone_number . ' - '.$q[$key]['role'].' - '.$q[$key]['organisation_contact_name'],
                'label' => $q[$key]['first_name'].' '.$q[$key]['last_name'].' - '. $phone_number . ' - '.$q[$key]['role'].' - '.$q[$key]['organisation_contact_name'],
                'first_name' => $q[$key]['first_name'],
                'last_name' => $q[$key]['last_name'],
                'mobile' => $phone_number,
                'address1' => $q[$key]['address1'],
                'address2' => $q[$key]['address2'],
                'address3' => $q[$key]['address3'],
                'country' => $q[$key]['country'],
                'county' => $q[$key]['county'],
                'postcode' => $q[$key]['postcode'],
                'town' => $q[$key]['town'],
                'email' => $q[$key]['email'],
                'role' => $q[$key]['role'],
                'family_id' => $q[$key]['family_id'],
                'organisation_id' => $q[$key]['organisation_id'],
                'primary_biller_id' => $q[$key]['primary_biller_id'],
                'organisation_contact_id' => $q[$key]['organisation_contact_id'],
                'organisation_contact_name' => $q[$key]['organisation_contact_name'],
            );
            if (!isset($result[$q[$key]['id']])) {
                $result[$q[$key]['id']] = $contact;
            }

            if (strpos($contact['role'], 'Guardian') !== false) {
                $children = DB::select('t1.family_id', 't1.id','t1.first_name','t1.last_name','t2.country_dial_code', 't2.dial_code', 't2.value','has_role.role_id', 'address.address1', 'address.address2', 'address.address3', 'address.country', 'address.county', 'address.postcode', 'address.town', array('emails.value', 'email'), DB::expr('GROUP_CONCAT(DISTINCT roles.name) as role'))
                    ->from(array(self::CONTACTS_TABLE,'t1'))
                    ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'t2'), 'left')->on('t1.notifications_group_id','=','t2.group_id')->on('t2.notification_id', '=', DB::expr(2))
                    ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'emails'), 'left')->on('t1.notifications_group_id','=','emails.group_id')->on('emails.notification_id', '=', DB::expr(1))
                    ->join(array(Model_Residence::ADDRESS_TABLE,'address'), 'left')->on('t1.residence','=','address.address_id')
                    ->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'INNER')->on('t1.id', '=', 'has_role.contact_id')
                    ->join(array(self::ROLE_TABLE, 'roles'), 'left')->on('has_role.role_id', '=', 'roles.id')
                    //->where('t2.notification_id','=',2)
                    ->where('t1.family_id', '=', $q[$key]['family_id'])
                    ->and_where('t1.id', '<>', $q[$key]['id'])
                    ->and_where('roles.name', 'in', array('Student', 'Mature'))
                    ->and_where('t1.delete', '=', 0)
                    ->and_where('t1.publish', '=', 1)
                    ->group_by('t1.id')
                    ->execute()->as_array();
                foreach ($children as $child) {
                    unset($result[$child['id']]);
                    $child_phone_number = !empty($child['country_dial_code']) ? '+' . $child['country_dial_code'] . $child['dial_code'] . $child['value'] : $child['value'];
                    $result[$child['id']] = array(
                        'id' => $child['id'],
                        'value' => '        * ' . $child['first_name'].' '.$child['last_name'].' - '. $child_phone_number,
                        'label' => '        * ' . $child['first_name'].' '.$child['last_name'].' - ' . $child_phone_number . ' - '.$child['role'],
                        'first_name' => $child['first_name'],
                        'last_name' => $child['last_name'],
                        'mobile' => $child_phone_number,
                        'address1' => $child['address1'],
                        'address2' => $child['address2'],
                        'address3' => $child['address3'],
                        'country' => $child['country'],
                        'county' => $child['county'],
                        'postcode' => $child['postcode'],
                        'town' => $child['town'],
                        'email' => $child['email'],
                        'role' => $child['role'],
                        'family_id' => $child['family_id']
                    );
                }
            }
        }
        $result = array_values($result);
        return $result;
    }

    public function is_special_member() {
        $special_member_tag = Model_Contacts3_Tag::get_tag_by_name('special_member');
        if (!empty($special_member_tag)) {
            return $this->has_tag($special_member_tag);
        } else {
            return false;
        }

    }
    public static function get_family_id_by_contact_id($contact_id)
    {
        $q = DB::select('family_id')->from(self::CONTACTS_TABLE)->where('id','=',$contact_id)->execute()->as_array();
        return count($q) > 0 ? $q[0]['family_id'] : NULL;
    }

    public static function get_siblings($contact_id)
    {
        $family_id = self::get_family_id_by_contact_id($contact_id);

		// Only perform the query if the contact has a family
		if ( ! is_null($family_id))
		{
			// Get members of the same family, other than the specified contact
			$siblings = DB::select('id','first_name','last_name')
				->from(self::CONTACTS_TABLE)
				->where('family_id', '=', $family_id)
				->where('id', '!=', $contact_id)
				->execute()
				->as_array();
		}
		else
		{
			$siblings = array();
		}

		return $siblings;
    }

    public static function get_contact_phone_number($contact_id)
    {
        $q = DB::select(
            array(DB::expr('IF(`t1`.`country_dial_code` IS NOT NULL AND `t1`.`country_dial_code` != \'\' , CONCAT_WS(\' \', \'+\', `t1` . `country_dial_code`, `t1` . `dial_code`, `t1` . `value`), `t1` . `value`) as `phone`')))
            ->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'t1'))
            ->join(array(self::CONTACTS_TABLE,'t2'))->on('t1.group_id','=','t2.notifications_group_id')
            ->where('t2.id','=',$contact_id)
            ->and_where('t1.notification_id', '=', 2)->execute()->as_array();
        return count($q) > 0 ? $q[0]['phone'] : '';
    }

    /**
     ** ----- PRIVATE FUNCTIONS -----
     **/

    private static function where_clauses($query, $where_clauses)
    {
        foreach ($where_clauses as $clause)
        {
            if ($clause == 'open')
            {
                $query = $query->where_open();
            }
            elseif ($clause == 'close')
            {
                $query = $query->where_close();
            }
            elseif (isset($clause[3]) AND $clause[3] == 'or')
            {
                $query = $query->or_where($clause[0], $clause[1], $clause[2]);
            }
            else
            {
                $query = $query->and_where($clause[0], $clause[1], $clause[2]);
            }
        }
        return $query;
    }

    private function _sql_get_contact()
    {
        $query = DB::select()->from(self::CONTACTS_TABLE)->where('id','=',$this->id);
        $q = $query->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    private function _sql_insert_contact($id = null)
    {
        $values = $this->get_instance();
        if ($id) {
            $values['id'] = $id;
        }
        if (!isset($values['created_by'])) {
            $user = Auth::instance()->get_user();
            $values['created_by'] = $user['id'];
        }
        $q = DB::insert(self::CONTACTS_TABLE)
            ->values($values)
            ->execute();
        $this->set_id($id ? $id : $q[0]);
    }

    private function _sql_update_contact()
    {
        $values = $this->get_instance();
        if (!isset($values['modified_by'])) {
            $user = Auth::instance()->get_user();
            $values['modified_by'] = $user['id'];
        }
        DB::update(self::CONTACTS_TABLE)->set($values)->where('id','=',$this->id)->execute();
    }

    /**
     * Save a contact's mobile number, email address etc. - adding new records
     */
    private function _sql_insert_contact_details($details)
    {
        $ids = array();
        if (isset($details[0]))
        {
            foreach ($details as $detail) {
                $exists = DB::select('*')
                    ->from(self::CONTACT_NOTIFICATION_RELATION_TABLE)
                    ->where('group_id', '=', $detail['group_id'])
                    ->and_where('value', '=', $detail['value'])
                    ->and_where('notification_id', '=', $detail['notification_id'])
                    ->and_where('deleted', '=', 0)
                    ->execute()
                    ->current();
                if (!$exists) {
                    $inserted = DB::insert(self::CONTACT_NOTIFICATION_RELATION_TABLE, array_keys($details[0]))
                        ->values($detail)
                        ->execute();
                    $ids[] = $inserted[0];
                } else {
                    $ids[] = $exists['id'];
                }
            }
        }
        return $ids;
    }

    /**
     * Save a contact's mobile number, email address etc. - adding a new record - updating existing records
     */
    private function _sql_update_contact_details($details)
    {
        foreach ($details as $detail)
        {
            DB::update(self::CONTACT_NOTIFICATION_RELATION_TABLE)->set($detail)->where('id', '=', $detail['id'])->execute();
        }
    }

    private function _sql_insert_contact_details_group()
    {
        return DB::insert('plugin_contacts3_notification_groups', array('deleted'))->values(array(0))->execute();
    }

    private function _sql_get_preferences()
    {
        return DB::select('id', 'contact_id', 'preference_id', 'value', 'notification_type')->from(self::CONTACT_PREFERENCES_RELATION_TABLE)
            ->where('contact_id', '=', $this->id)->and_where('publish', '=', 1)->and_where('deleted', '=', 0)
            ->execute()->as_array();
    }

    private function _sql_get_permissions()
    {
        $permissions = DB::select('*')
            ->from(self::TABLE_PERMISSION_LIMIT)
            ->where('contact3_id', '=', $this->id)
            ->execute()
            ->as_array();
        $result = array();

        foreach ($permissions as $permission) {
            $result[] = $permission['user_id'];
        }

        return $result;
    }

    private function _sql_get_course_type_preferences()
    {
        return DB::select('id', 'contact_id', 'course_type_id', 'value')->from(self::CONTACT_COURSE_TYPE_RELATION_TABLE)
            ->where('contact_id', '=', $this->id)->and_where('publish', '=', 1)->and_where('deleted', '=', 0)
            ->execute()->as_array();
    }

    private function _sql_get_course_subject_preferences()
    {
        return DB::select('id', 'contact_id', 'course_subject_id', 'value')->from(self::CONTACT_COURSE_SUBJECT_PREFERENCE)
            ->where('contact_id', '=', $this->id)->and_where('publish', '=', 1)->and_where('deleted', '=', 0)
            ->execute()->as_array();
    }

    private function _sql_get_subject_preferences()
    {
        return DB::select('id', 'contact_id', 'subject_id', 'value', 'level_id')
            ->from(self::CONTACT_SUBJECT_RELATION_TABLE)
            ->where('contact_id', '=', $this->id)->and_where('publish', '=', 1)->and_where('deleted', '=', 0)
            ->execute()->as_array();
    }

	private function _sql_get_role_ids()
	{
		$has_roles = DB::select('*')->from(self::CONTACT_ROLE_RELATION_TABLE)->where('contact_id', '=', $this->id)->execute()->as_array();
		$ids = array();
		foreach($has_roles as $has_role){
			$ids[] = $has_role['role_id'];
		}
		return $ids;
	}

    private function _sql_save_roles()
    {
        if(is_numeric($this->id) && !empty($this->roles)){
            DB::delete(self::CONTACT_ROLE_RELATION_TABLE)
                ->where('contact_id', '=', $this->id)
                ->and_where('role_id', 'not in', $this->roles)
                ->execute();
        }
        if (!empty($this->roles)) {
            foreach ($this->roles as $role_id) {
                try {
                    DB::insert(self::CONTACT_ROLE_RELATION_TABLE,
                        array('contact_id', 'role_id'))->values(array($this->id, $role_id))->execute();
                } catch (Exception $e) {
                }
            }
        }
    }

    private function _sql_save_preferences()
    {
        $query = DB::delete(self::CONTACT_PREFERENCES_RELATION_TABLE)->where('contact_id', '=', $this->id);
        $query->execute();
        $insertt = false;
        if (count($this->preferences) > 0)
        {
            $insert = DB::insert(self::CONTACT_PREFERENCES_RELATION_TABLE, array('contact_id', 'preference_id', 'value', 'notification_type'));

            foreach ($this->preferences as $preference)
            {
                $notification_type = null;
                if (is_array($preference)) {
                    $preference_id = isset($preference['preference_id']) ? $preference['preference_id'] : isset($preference['id']);
                    $notification_type = isset($preference['notification_type']) ? $preference['notification_type'] : null;
                    if (@$preference['value'] == null && !is_numeric($preference['preference_id'])) {
                        continue;
                    }
                    $insertt = true;
                } else {
                    $insertt = true;
                    $preference_id = $preference;
                }

                $insert = $insert->values(array($this->id, $preference_id, 1, $notification_type));
            }
            if ($insertt) {
                $insert->execute();
            }
        }
    }

    private function _sql_save_course_type_preferences()
    {
        DB::delete(self::CONTACT_COURSE_TYPE_RELATION_TABLE)->where('contact_id', '=', $this->id)->execute();

        if (count($this->course_type_preferences) > 0)
        {
            $insert = DB::insert(self::CONTACT_COURSE_TYPE_RELATION_TABLE, array('contact_id', 'course_type_id', 'value'));
            foreach ($this->course_type_preferences as $preference)
            {
                if (is_array($preference)) {
                    if (isset($preference['course_type_id'])) {
                        $insert = $insert->values(array($this->id, $preference['course_type_id'], 1));
                    } else {
                        foreach ($preference as $preference2) {
                            $insert = $insert->values(array($this->id, $preference2, 1));
                        }
                    }
                } else {
                    $insert = $insert->values(array($this->id, $preference, 1));
                }
            }
            $insert->execute();
        }
    }

    private function _sql_save_course_subject_preferences()
    {
        DB::delete(self::CONTACT_COURSE_SUBJECT_PREFERENCE)->where('contact_id', '=', $this->id)->execute();

        if (count($this->course_subject_preference) > 0)
        {
            $insert = DB::insert(self::CONTACT_COURSE_SUBJECT_PREFERENCE, array('contact_id', 'course_subject_id', 'value'));
            foreach ($this->course_subject_preference as $preference)
            {
                if (is_numeric($preference)) {
                    $insert = $insert->values(array($this->id, $preference, 1));
                }
            }
            $insert->execute();
        }
    }

    private function _sql_save_subject_preferences()
    {
        DB::delete(self::CONTACT_SUBJECT_RELATION_TABLE)->where('contact_id', '=', $this->id)->execute();

        if (count($this->subject_preferences) > 0)
        {
            foreach ($this->subject_preferences as $preference)
            {
                if (is_array($preference)) {
                    if ($preference['subject_id'] > 0) {
                        $insert = DB::insert(self::CONTACT_SUBJECT_RELATION_TABLE, array('contact_id', 'subject_id', 'value', 'level_id'));
                        $insert = $insert->values(array($this->id, $preference['subject_id'], 1, @$preference['level_id']));
                        $insert->execute();
                    }
                } else {
                    $insert = DB::insert(self::CONTACT_SUBJECT_RELATION_TABLE, array('contact_id', 'subject_id', 'value'));
                    $insert = $insert->values(array($this->id, $preference, 1));
                    $insert->execute();
                }
            }
        }
    }

    private function _sql_save_permissions()
    {
        if (count($this->permissions)) {
            DB::delete(self::TABLE_PERMISSION_LIMIT)
                ->where('contact3_id', '=', $this->id)
                ->execute();
            foreach ($this->permissions as $user_id) {
                DB::insert(self::TABLE_PERMISSION_LIMIT)
                    ->values(array(
                        'user_id' => $user_id,
                        'contact3_id' => $this->id
                    ))->execute();
            }
        }
    }
    
    function _sql_save_tags()
    {
        $added_tags = [];
        
        // Make sure tags have been loaded, so we don't accidentally delete everything.
        if ($this->tags_set) {
            DB::delete(Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE)
                ->where('contact_id', '=', $this->id)
                ->execute();
            if (count($this->tags)) {
                foreach ($this->tags as $tag) {
                    if($tag instanceof Model_Contacts3_Tag && $tag->get_label() && !in_array($tag->get_id(), $added_tags)) {
                        DB::insert(Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE)
                            ->values(array(
                                'contact_id' => $this->id,
                                'tag_id' => $tag->get_id()
                            ))->execute();
                        $added_tags[] = $tag->get_id();
                    }
                }
            }
        } else {
            if (count($this->tags)) {
                foreach ($this->tags as $tag) {
                    if($tag instanceof Model_Contacts3_Tag && $tag->get_label() && !in_array($tag->get_id(), $added_tags)) {
                        if(!$this->has_tag($tag, 1)) {
                            DB::insert(Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE)
                                ->values(array(
                                    'contact_id' => $this->id,
                                    'tag_id' => $tag->get_id()
                                ))->execute();
                        }
                        $added_tags[] = $tag->get_id();
                    }
                }
            }
        }
    }

    private function _sql_get_tags() {
        $has_tags_query = DB::select('tag_id')
            ->from(Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE)
            ->where('contact_id', '=', $this->id);
        //echo(Debug::vars((string) $has_tags_query));
        $has_tags = $has_tags_query->execute()->as_array();
        $tags = array();
        if (!empty($has_tags)) {
            foreach($has_tags as $has_tag) {
               $tag = new Model_Contacts3_Tag($has_tag['tag_id']);
               if (!empty($tag)) {
                   $tags[] = $tag;
               }
            }
        }
        return $tags;
    }

    public function get_timeoff_config()
    {
        $timeoff_config = DB::select('*')
            ->from('plugin_timeoff_config')
            ->where('level', '=', 'contact')
            ->where('item_id', '=', $this->get_id())
            ->execute()
            ->as_array();

        foreach ($timeoff_config as $tc) {
            $timeoff_config[$tc['name']] = $tc;
        }

        $this->timeoff_config = $timeoff_config;
        return $timeoff_config;
    }

    public function count_timeoff_booked($year)
    {
        $minutes_booked = \DB::select([\DB::expr("SUM(`duration`)"), 'total'])->from('plugin_timeoff_requests')
            ->where('staff_id', '=', $this->id)
            ->where('period_start_date', '>=', $year.'-01-01 00:00:00')
            ->where('period_end_date', '<', ($year+1).'-01-01 00:00:00')
            ->execute()->get('total');

        return $minutes_booked;
    }

    public function count_timeoff_days_remaining($year, $day_length)
    {
        if (empty($this->timeoff_config)) {
            $this->get_timeoff_config();
        }

        if (isset($this->timeoff_config['timeoff.annual_leave'])) {
            $annual_leave = IbHelpers::time_to_days($this->timeoff_config['timeoff.annual_leave']['value'], $day_length);
        } else {
            $annual_leave = 20;
        }

        $minutes_booked = $this->count_timeoff_booked($year);
        $days_booked    = $minutes_booked / (60 * $day_length);

        return $annual_leave - $days_booked;
    }

	/**
	 * @purpose Merge the arrays containing notification details into a more usable format
	 * e.g. before: data = [
	 * 		contactdetail_id': [8, 9],
	 *		contactdetail_value: [0123456789, example@example.com],
	 *		contactdetail_type_id: [2, 1],
	 * 		...
	 * 	]
	 * after: data = [
	 * 		notifications:
	 * 			[id: 1, notification_id: 2, value: 0123456789],
	 *			[id: 2, notification_id: 1, value: example@example.com],
	 * 		...
	 * 	]
	 * @return array
	 */
	public static function normalize_notification_data($data)
	{
		if (isset($data['contactdetail_type_id']) AND isset($data['contactdetail_value']))
		{
			$data['notifications'] = array();
			for ($i = 0; $i < sizeof($data['contactdetail_value']); $i++)
			{
				if (isset($data['contactdetail_id'][$i]))
				{
					$data['notifications'][$i]['id']          = $data['contactdetail_id'][$i];
				}
				$data['notifications'][$i]['notification_id'] = $data['contactdetail_type_id'][$i];
				$data['notifications'][$i]['value']           = $data['contactdetail_value'][$i];
			}
			unset($data['contactdetail_id']);
			unset($data['contactdetail_type_id']);
			unset($data['contactdetail_value']);
		}
		return $data;
	}

                 /***         SEEDING FUNCTION          ***/
    /**
     * Bulk Insert into the database
     */
    public static function database_bulk_insert_contacts($test = false)
	{
		//header('content-type: text/plain; charset=utf-8');
		ini_set('max_execution_time', 0);
		ob_start();
		//echo "mem:" . memory_get_usage() . "\n";
		$csv = '../database_seeding/full_contact_list.csv';
		$csv = fopen($csv, 'r');
		
		$fix_relationships = array (
									'Mother' => 'Mother',
									'Father' => 'Father',
                                    'Step Father' => 'Father',
									'Mother..........(Limk' => 'Mother',
									'Step Mother' => 'Mother',
									'Student' => 'Mature',
                                    'Cousin' => 'Mother',
                                    'Daughter' => 'Mother',
									'' => '',
									'Mature' => 'Mature',
									'Niamh Foley' => 'Mature',
									'Fostermother' => 'Mother',
									'Guardian' => 'Mother',
									'Fater' => 'Father',
									'MATURE STUDENT' => 'Mature',
									'SISTER' => 'Mother',
									'STUDENT' => 'Mature',
									'Grandmother' => 'Mother',
                                    'Gran' => 'Mother',
									'Sister' => 'Mother',
									'Guidance Counsellor' => 'Father',
									'Foster Mother' => 'Mother',
									'Moither' => 'Mother',
									'Grandad' => 'Father',
									'Self' => 'Mature',
                                    'Himself' => 'Mature',
									'Uncle' => 'Father',
									'Supervisor' => 'Father',
									'Career Guidance Counsellor' => 'Father',
									'Mature Student' => 'Mature',
									'father' => 'Father',
									'Mary' => 'Mother',
									'Guardian / Grandmother' => 'Mother',
									'mother' => 'Mother',
									'Darragh Crocker' => 'Father',
									'Moth' => 'Mother',
									'Mothe' => 'Mother',
									'Mothde' => 'Mother',
									'Nicholas Hayes' => 'Father',
									'self' => 'Mature',
									'Friend of Niamh Harkin' => 'Father',
									'Mothert' => 'Mother',
									'Grandfather - 087 7429824' => 'Father',
									'Mother......(Co Tipp)' => 'Mother',
									'Aunt' => 'Mother',
									'Stepmother' => 'Mother',
									'Grandfather' => 'Father',
									'Dad' => 'Father',
									'Ms' => 'Mother',
									'Care Worker' => 'Mother',
                                    'Laura' => 'Mother',
                                    'Mother..........(Limk)' => 'Mother',
                                    'Dale' => 'Father',
                                    'Neighbour' => 'Father',
                                    'Foster Carer for Exchange Student' =>'Mother',
                                    'Deputy Principal' => 'Mother',
                                    'Dale' => 'Father',
                                    'Friend of Niamh Harkin' => 'Mother',
                                    'Nicholas Hayes' => 'Mother',
                                    'Self' => 'Mother',
                                    'Career Guidance Counsellor' => 'Mother',
                                    'Guidance teacher' => 'Father',
                                    'Moother' =>'Mother',
                                    'Miother' => 'Mother',
                                    'Stephen Hickey' => 'Father',
                                    '' => 'Mother',
                                    'Motgher' => 'Mother',
                                    'Parent' => 'Mother'
									);
		$roles = array('Guardian' => 1, 'Student' => 2, 'Mature' => 3);
		$county_cache = array();
		$res = new Model_Residence();
		$now = date('Y-m-d H:i:s');
		$user = Auth::instance()->get_user();
		$user_id = $user['id'];
	
		if($csv){
			$columns = fgetcsv($csv, 0, ',');
			$i = 1;
			$students = array();
			
			while($row = fgetcsv($csv, 0, ',')){
				$student = array();
				foreach($columns as $i => $column){
					$student[$column] = trim(trim(@$row[$i], "*"));
				}
				$students[] = $student;
			}
			fclose($csv);
			
			echo "<b>Skipping records below!</b><br>";
			echo "<b>Empty full names:</b><br>\n";
			//echo "|" . implode("|", $columns) . "|\n";
			echo "<table border=1><tr><td>" . implode("</td><td>", $columns) . "</td></tr>";
			foreach($students as $i => $student){
				if($student['full_name'] == ''){
                    if ($student['student_full_name'] != '') {
                        $students[$i]['full_name'] = $student['student_full_name'];
                        $students[$i]['first_name'] = $student['student_first_name'];
                        $students[$i]['relationship'] = 'self';
                    } else {
                        echo "<tr><td>" . implode("</td><td>", $student) . "</td></tr>";
                        unset($students[$i]);
                    }
				}
			}
			echo "</table><br>";
			
			echo "<b>Full/First name mismatches:</b><br>\n";
			echo "<table border=1><tr><td>" . implode("</td><td>", $columns) . "</td></tr>";
			foreach($students as $i => $student){
				if($student['full_name'] != '' && stripos($student['full_name'], $student['first_name']) === false AND $student['first_name'] != ''){
					echo "<tr><td>" . implode("</td><td>", $student) . "</td></tr>";
					unset($students[$i]);
				}
			}
			echo "</table><br>";
			
			echo "<b>Student Full/First name mismatches:</b>\n";
			echo "<table border=1><tr><td>" . implode("</td><td>", $columns) . "</td></tr>";
			foreach($students as $i => $student){
				if($student['student_first_name'] != '' && stripos($student['student_full_name'], $student['student_first_name']) === false){
					echo "<tr><td>" . implode("</td><td>", $student) . "</td></tr>";
					unset($students[$i]);
				}
			}
			echo "</table><br>";
			
			//echo "mem:" . memory_get_usage() . "\n";
			usort($students, function($s1, $s2){
				return strcasecmp($s1['full_name'], $s2['full_name']);
			});
			
			$families = array();
			foreach($students as $student){
				if(!isset($families[$student['full_name'].$student['address1']])){
					$families[$student['full_name'].$student['address1']] = array('import' => array());
				}
				$families[$student['full_name'].$student['address1']]['import'][] = $student;
			}
			//echo count($students) . " students<br>";
			unset($students);
			//echo '<pre>'.print_r($families,1).'</pre>';
//			echo "<br /><hr /><b>Saving " . count($families) . " families:</b><br>";
			
			Database::instance()->begin();
			try{
				echo "<table border=1><tr><th>Family</th><th>Primary</th><th>Mother</th><th>Father</th><th>Students</th></tr>";
				foreach($families as $fi => $family){
					echo "<tr>";
					$phones = array();
					$emails = array();
					$mobiles = array();
					$residences = array();
					$primary_contact = null;
					$fstudents = array();
					$mother = null;
					$father = null;
					$mature = null;
					$rfamily = array('family_name' => '',
									'publish' => 1,
									'delete' => 0,
									'date_created' => $now,
									'date_modified' => $now,
									'created_by' => $user_id,
									'modified_by' => $user_id);
					
					foreach($family['import'] as $student){
						if($primary_contact == null){
							$primary_contact = array('title' => '',
														'type' => 1,
														'subtype_id' => 1,
														'first_name' => '',
														'last_name' => '',
														'is_primary' => 1,
														'publish' => 1,
														'delete' => 0,
														'date_created' => $now,
														'date_modified' => $now,
														'created_by' => $user_id,
														'modified_by' => $user_id);
							$fullname = $student['full_name'];
							if(preg_match('/^(mr|mrs|ms)\s+/i', $fullname, $title)){
								$primary_contact['title'] = $title[1];
								$fullname = preg_replace('/^(mr|mrs|ms)\s+/i', '', $fullname);
							}
							$primary_contact['first_name'] = $student['first_name'];
							$primary_contact['last_name'] = trim(str_ireplace($student['first_name'], '', $fullname));
                            if (isset($fix_relationships[$student['relationship']]) ) {
                                $relationship = $fix_relationships[$student['relationship']];
                            } else {
                                if (trim($student['father_first_name']) != '') {
                                    $relationship = 'Father';
                                } else if (trim($student['mother_first_name']) != '') {
                                    $relationship = 'Mother';
                                }
                            }

							if($relationship == 'Mother'){
								$mother = &$primary_contact;
								$primary_contact['role_id'] = 1;
								$mobiles[] = $student['mother_mobile'] ? $student['mother_mobile'] : ($student['father_mobile'] ? $student['father_mobile'] : $student['student_mobile']);
								if(!$father && $student['father_first_name']){
									$father = array('first_name' => $student['father_first_name'],
													'mobile' => $student['father_mobile']);
								}
							} else if($relationship == 'Father'){
								$father = &$primary_contact;
								$primary_contact['role_id'] = 1;
								$mobiles[] = $student['father_mobile'] ? $student['father_mobile'] : ($student['mother_mobile'] ? $student['mother_mobile'] : $student['student_mobile']);
								if(!$mother && $student['mother_first_name']){
									$mother = array('first_name' => $student['mother_first_name'],
													'mobile' => $student['mother_mobile']);
								}
							} else {
								$mature = &$primary_contact;
								$primary_contact['role_id'] = 3;
								$mobiles[] = $student['student_mobile'];
								if($student['school'] != ''){
									$school_id = DB::select('id')->from('plugin_courses_providers')->where('name', '=', $student['school'])->and_where('type_id', '=', 2)->execute()->get('id');
									if(!$school_id){
										$school = array('name' => $student['school'],
														'type_id' => 2,
														'publish' => 1,
														'delete' => 0,
														'date_created' => $now,
														'date_modified' => $now,
														'created_by' => $user_id,
														'modified_by' => $user_id);
										$school_result = DB::insert('plugin_courses_providers',array_keys($school))->values($school)->execute();
										$school_id = $school_result[0];
									}
									$primary_contact['school_id'] = $school_id;
								}
								if($student['year'] != ''){
									$year_id = DB::select('id')->from('plugin_courses_years')->where('year', '=', $student['year'])->execute()->get('id');
									if(!$year_id){
										$year = array('year' => $student['year'],
														'publish' => 1,
														'delete' => 0,
														'date_created' => $now,
														'date_modified' => $now,
														'created_by' => $user_id,
														'modified_by' => $user_id);
										$year_result = DB::insert('plugin_courses_years',array_keys($year))->values($year)->execute();
										$year_id = $year_result[0];
									}
									$primary_contact['year_id'] = $year_id;
								}
							}
						}
						if($mature == null){
							$fstudents[] = $student;
						}
						
						if($student['address1'] != null && $student['address1'] != '-0'){
							if(!isset($residences[$student['address1'].$student['address2'].$student['address3'].$student['address4'].$student['address5']])){
								$residence = array();
								$residences[$student['address1'].$student['address2'].$student['address3'].$student['address4'].$student['address5']] = &$residence;
								$residence = array('address1' => $student['address1'],
													'address2' => $student['address2'],
													'address3' => $student['address3'],
													'town' => $student['address4'],
													'country' => 'IE',
													'county' => $student['address5'],
													'publish' => 1,
													'delete' => 0);
								if ($student['address5'] == '')
								{
									if ($student['address4'] == '')
									{
										if ($student['address3'] == '')
										{
											$residence['county'] = $student['address2'];
											$residence['town'] = $student['address1'];
											$residence['address2'] = '';
										}
										else
										{
											$residence['county'] = $student['address3'];
											$residence['town'] = $student['address2'];
											$residence['address3'] = '';
											$residence['address2'] = '';
										}
									}
									else
									{
										$residence['county'] = $student['address4'];
										$residence['town'] = $student['address3'];
										$residence['address3'] = '';
									}
								}
								$residence['county'] = substr($residence['county'], strpos($residence['county'], ' '));
								$county_id = isset($county_cache[$residence['county']]) ? $county_cache[$residence['county']] : $county_cache[$residence['county']] = Model_Residence::get_county_id($residence['county']);
								$residence['county'] = $county_id;
								$address_id = DB::insert('plugin_contacts3_residences', array_keys($residence))->values($residence)->execute();
								$residence['address_id'] = $address_id[0];
	
							}
						}
						if($student['phone_number'] != null){
							if(!isset($phones[$student['phone_number']])){
								$phones[] = $student['phone_number'];
							}
						}
						if($student['email'] != null){
							if(!isset($emails[$student['email']])){
								$emails[] = $student['email'];
							}
						}
					}
					$residences = array_values($residences);
					if(count($residences)){
						$primary_contact['residence'] = $residences[0]['address_id'];
						$rfamily['residence'] = $residences[0]['address_id'];
					}
					$rfamily['family_name'] = $primary_contact['last_name'];
					$family_id = DB::insert('plugin_contacts3_family', array_keys($rfamily))->values($rfamily)->execute();
					$rfamily['family_id'] = $family_id[0];
					echo "<td>#" . $rfamily['family_id'] . "; ". $rfamily['family_name'] . "; " . @$residences[0]['address1'] . "</td>";
					
					$primary_contact['family_id'] = $rfamily['family_id'];
					$notification_group = array('publish' => 1,
												'deleted' => 0,
												'date_created' => $now,
												'date_modified' => $now,
												'created_by' => $user_id,
												'modified_by' => $user_id
												);
					$notification_group_id = DB::insert('plugin_contacts3_notification_groups', array_keys($notification_group))->values($notification_group)->execute();
					$primary_contact['notifications_group_id'] = $notification_group_id[0];
					$has_role = array('role_id' => $primary_contact['role_id']);
					unset($primary_contact['role_id']);
					$primary_contact_id = DB::insert('plugin_contacts3_contacts', array_keys($primary_contact))->values($primary_contact)->execute();
					$primary_contact['id'] = $primary_contact_id[0];
					$has_role['contact_id'] = $primary_contact['id'];
					DB::insert(self::CONTACT_ROLE_RELATION_TABLE, array_keys($has_role))->values($has_role)->execute();
					
					
					echo "<td>#" . $primary_contact['id'] . "; ". $primary_contact['title'] . ' ' . $primary_contact['first_name'] . ' ' . $primary_contact['last_name'] . "</td>";
					$rfamily['primary_contact_id'] = $primary_contact['id'];
					DB::update('plugin_contacts3_family')->set($rfamily)->where('family_id', '=', $rfamily['family_id'])->execute();
					$preference = array('publish' => 1,
										'deleted' => 0,
										'date_created' => $now,
										'date_modified' => $now,
										'created_by' => $user_id,
										'modified_by' => $user_id,
										'contact_id' => $primary_contact['id']);
					
					for($preference_id = 1 ; $preference_id <= 5; ++$preference_id){
						$preference['preference_id'] = $preference_id;
						DB::insert('plugin_contacts3_contact_has_preferences', array_keys($preference))->values($preference)->execute();
					}
                    $preference['preference_id'] = 15;
                    DB::insert('plugin_contacts3_contact_has_preferences',array_keys($preference))->values($preference)->execute();
					
					foreach($emails as $email){
						$notification = array('value' => $email,
												'contact_id' => $primary_contact['id'],
												'publish' => 1,
												'deleted' => 0,
												'date_created' => $now,
												'date_modified' => $now,
												'created_by' => $user_id,
												'modified_by' => $user_id,
												'notification_id' => 1,
												'group_id' => $notification_group_id[0]
												);
						
						DB::insert('plugin_contacts3_contact_has_notifications', array_keys($notification))->values($notification)->execute();
					}
					foreach($phones as $phone){
						$notification = array('value' => preg_replace('/[^0-9]/', '', $phone),
												'contact_id' => $primary_contact['id'],
												'publish' => 1,
												'deleted' => 0,
												'date_created' => $now,
												'date_modified' => $now,
												'created_by' => $user_id,
												'modified_by' => $user_id,
												'notification_id' => 3,
												'group_id' => $notification_group_id[0]
												);
						
						DB::insert('plugin_contacts3_contact_has_notifications', array_keys($notification))->values($notification)->execute();
					}
					foreach($mobiles as $mobile){
						if($mobile != ''){
							$notification = array('value' => preg_replace('/[^0-9]/', '', $mobile),
												'contact_id' => $primary_contact['id'],
												'publish' => 1,
												'deleted' => 0,
												'date_created' => $now,
												'date_modified' => $now,
												'created_by' => $user_id,
												'modified_by' => $user_id,
												'notification_id' => 2,
												'group_id' => $notification_group_id[0]
												);
						
							DB::insert('plugin_contacts3_contact_has_notifications', array_keys($notification))->values($notification)->execute();
						}
					}
					if($mother && $mother != $primary_contact){
                        if($mother['mobile'] != '') {
                            $notification_group_id = DB::insert('plugin_contacts3_notification_groups', array_keys($notification_group))->values($notification_group)->execute();
                            $notification_group_id = $notification_group_id[0];
                        } else {
                            $notification_group_id = $primary_contact['notifications_group_id'];
                        }
						$gcontact = array('title' => '',
												'type' => 1,
												'subtype_id' => 1,
												'first_name' => $mother['first_name'],
												'last_name' => '',
												'is_primary' => 0,
												'publish' => 1,
												'delete' => 0,
												'date_created' => $now,
												'date_modified' => $now,
												'created_by' => $user_id,
												'modified_by' => $user_id,
												'notifications_group_id' => $notification_group_id,
                                                'residence' => @$primary_contact['residence']);
						$gcontact['family_id'] = $rfamily['family_id'];
						$gcontact_id = DB::insert('plugin_contacts3_contacts', array_keys($gcontact))->values($gcontact)->execute();
						$has_role = array('role_id' => 1, 'contact_id' => $gcontact_id[0]);
						DB::insert(self::CONTACT_ROLE_RELATION_TABLE, array_keys($has_role))->values($has_role)->execute();
						if($mother['mobile'] != ''){
							$notification = array('value' => preg_replace('/[^0-9]/', '', $mother['mobile']),
													'contact_id' => $gcontact_id[0],
													'publish' => 1,
													'deleted' => 0,
													'date_created' => $now,
													'date_modified' => $now,
													'created_by' => $user_id,
													'modified_by' => $user_id,
													'notification_id' => 2,
													'group_id' => $notification_group_id
													);
							
							DB::insert('plugin_contacts3_contact_has_notifications', array_keys($notification))->values($notification)->execute();
						}
					}
					if($father && $father != $primary_contact){
                        if($father['mobile'] != '') {
                            $notification_group_id = DB::insert('plugin_contacts3_notification_groups', array_keys($notification_group))->values($notification_group)->execute();
                            $notification_group_id = $notification_group_id[0];
                        } else {
                            $notification_group_id = $primary_contact['notifications_group_id'];
                        }
						$gcontact = array('title' => '',
												'type' => 1,
												'subtype_id' => 1,
												'first_name' => $father['first_name'],
												'last_name' => '',
												'is_primary' => 0,
												'publish' => 1,
												'delete' => 0,
												'date_created' => $now,
												'date_modified' => $now,
												'created_by' => $user_id,
												'modified_by' => $user_id,
												'notifications_group_id' => $notification_group_id,
                                                'residence' => @$primary_contact['residence']);
						$gcontact['family_id'] = $rfamily['family_id'];
						$gcontact_id = DB::insert('plugin_contacts3_contacts', array_keys($gcontact))->values($gcontact)->execute();
						$has_role = array('role_id' => 1, 'contact_id' => $gcontact_id[0]);
						DB::insert(self::CONTACT_ROLE_RELATION_TABLE, array_keys($has_role))->values($has_role)->execute();
						if($father['mobile'] != ''){
							$notification = array('value' => preg_replace('/[^0-9]/', '', $father['mobile']),
													'contact_id' => $gcontact_id[0],
													'publish' => 1,
													'deleted' => 0,
													'date_created' => $now,
													'date_modified' => $now,
													'created_by' => $user_id,
													'modified_by' => $user_id,
													'notification_id' => 2,
													'group_id' => $notification_group_id
													);
							
							DB::insert('plugin_contacts3_contact_has_notifications', array_keys($notification))->values($notification)->execute();
						}
					}
					echo "<td>" . ( $mother ? @$mother['title'] . ' ' . @$mother['first_name'] . ' ' . @$mother['last_name'] : "" ) . "</td>";
					echo "<td>" . ( $father ? @$father['title'] . ' ' . @$father['first_name'] . ' ' . @$father['last_name'] : "" ) . "</td>";
					if(!$mature){
						echo "<td>";
						foreach($family['import'] as $student){
                            if($student['student_mobile'] != '') {
                                $notification_group_id = DB::insert('plugin_contacts3_notification_groups', array_keys($notification_group))->values($notification_group)->execute();
                                $notification_group_id = $notification_group_id[0];
                            } else {
                                $notification_group_id = $primary_contact['notifications_group_id'];
                            }
							$scontact = array('title' => '',
												'type' => 1,
												'subtype_id' => 1,
												'first_name' => '',
												'last_name' => '',
												'is_primary' => 0,
												'publish' => 1,
												'delete' => 0,
												'date_created' => $now,
												'date_modified' => $now,
												'created_by' => $user_id,
												'modified_by' => $user_id,
												'notifications_group_id' => $notification_group_id,
                                                'residence' => @$primary_contact['residence']);
							$scontact['family_id'] = $rfamily['family_id'];
							if($student['student_first_name'] != ''){
								$scontact['first_name'] = $student['student_first_name'];
								$scontact['last_name'] = trim(str_ireplace($student['student_first_name'], '', $student['student_full_name']));
							} else {
								$scontact['last_name'] = $student['student_full_name'];
							}
							echo $scontact['first_name'] . ' ' . $scontact['last_name'] . ", ";
							if($student['school'] != ''){
								$school_id = DB::select('id')->from('plugin_courses_providers')->where('name', '=', $student['school'])->and_where('type_id', '=', 2)->execute()->get('id');
								if(!$school_id){
									$school = array('name' => $student['school'],
													'type_id' => 2,
													'publish' => 1,
													'delete' => 0,
													'date_created' => $now,
													'date_modified' => $now,
													'created_by' => $user_id,
													'modified_by' => $user_id);
									$school_result = DB::insert('plugin_courses_providers',array_keys($school))->values($school)->execute();
									$school_id = $school_result[0];
								}
								$scontact['school_id'] = $school_id;
							}
							if($student['year'] != ''){
								$year_id = DB::select('id')->from('plugin_courses_years')->where('year', '=', $student['year'])->execute()->get('id');
								if(!$year_id){
									$year = array('year' => $student['year'],
													'publish' => 1,
													'delete' => 0,
													'date_created' => $now,
													'date_modified' => $now,
													'created_by' => $user_id,
													'modified_by' => $user_id);
									$year_result = DB::insert('plugin_courses_years',array_keys($year))->values($year)->execute();
									$year_id = $year_result[0];
								}
								$scontact['year_id'] = $year_id;
							}
							$scontact_id = DB::insert('plugin_contacts3_contacts', array_keys($scontact))->values($scontact)->execute();
							$has_role = array('role_id' => 2, 'contact_id' => $scontact_id[0]);
							DB::insert(self::CONTACT_ROLE_RELATION_TABLE, array_keys($has_role))->values($has_role)->execute();
							if($student['student_mobile'] != ''){
								$notification = array('value' => preg_replace('/[^0-9]/', '', $student['student_mobile']),
														'contact_id' => $scontact_id[0],
														'publish' => 1,
														'deleted' => 0,
														'date_created' => $now,
														'date_modified' => $now,
														'created_by' => $user_id,
														'modified_by' => $user_id,
														'notification_id' => 2,
														'group_id' => $notification_group_id
														);
								
								DB::insert('plugin_contacts3_contact_has_notifications', array_keys($notification))->values($notification)->execute();
							}
						}
						echo "</td>";
					} else {
						echo "<td>Mature</td>";
					}
					echo "</tr>";
					unset($mother);
					unset($father);
					unset($mature);
					//echo $primary_contact['title'] . ' ' . $primary_contact['first_name'] . ' '  . $primary_contact['last_name'] . "\n";
				}
				echo "</table>";
                if ($test) {
                    Database::instance()->rollback();
                } else {
                    Database::instance()->commit();
                }
			} catch(Exception $e){
                Database::instance()->rollback();
				throw $e;
            }
		} else {
		
		}
		die();
	}

    /**
     * Copy trainers from contacts table to Contacts3 as a Teacher
     */
    public static function copy_trainers_as_teachers()
    {
        $trainers = DB::select('c0.id','c0.first_name','c0.last_name','c0.email','c0.phone','c0.mobile')->from(array('plugin_contacts_contact','c0'))
            ->join(array('plugin_contacts_mailing_list','c1'))
                ->on('c0.mailing_list','=','c1.id')
            ->where('c1.name','=','trainer')
            ->execute()->as_array();
        foreach ($trainers as $trainer)
        {
            $family = new Model_Family();
            $family->load(array('family_name'=>$trainer['last_name']));
            $saved = $family->save();
            if ($saved)
            {
                $family_id = $family->get_id();
            }
            $notifications = array();
            if ( ! empty($trainer['email']) OR $trainer['email'] != '')
            {
                $notifications[0]=array('id'=>'new','notification_id'=>1,'value'=>$trainer['email']);
            }
            if ( ! empty($trainer['phone']) OR $trainer['phone'] != '')
            {
                $notifications[1]=array('id'=>'new','notification_id'=>3,'value'=>intval(str_replace('-','',$trainer['phone'])));
            }
            if ( ! empty($trainer['mobile']) OR $trainer['mobile'] != '' )
            {
                $notifications[2]=array('id'=>'new','notification_id'=>2,'value'=>intval(str_replace('-','',$trainer['mobile'])));
            }
            $data = array(
                'id'=>'',
                'type'=>1,
                'family_id'=>$family_id,
                'is_primary'=>1,
                'title'=>NULL,
                'first_name'=>$trainer['first_name'],
                'last_name'=>($trainer['last_name'] == '')? 'KES Teacher' : $trainer['last_name'],
                'address_id'=>NULL,
                'date_of_birth'=>'',
                'roles'=>array(1,4),
                'school_id'=>'',
                'year_id'=>'',
                'subtype_id'=>2,
                'notifications_group_id'=>'',
                'notes'=>'',
                'notifications'=>$notifications
            );
            $contact = new Model_Contacts3($data['id']);
            $contact->load($data);
            $contact->address->load($data);
            $contact->save();
            $teacher_id = $contact->get_id();
//			$has_role = array('contact_id' => $teacher_id, 'role_id' => 4);
//			DB::insert(self::CONTACT_ROLE_RELATION_TABLE, array_keys($has_role))->values($has_role)->execute();
				
            DB::update('plugin_courses_schedules')->set(array('trainer_id'=>$teacher_id))->where('trainer_id','=',$trainer['id'])->execute();
            DB::update('plugin_courses_schedules_events')->set(array('trainer_id'=>$teacher_id))->where('trainer_id','=',$trainer['id'])->execute();
        }
//        DB::query(Database::SELECT,"UPDATE plugin_contacts3_contact_has_notifications SET `value`= CONCAT('0',`value`) WHERE notification_id = 2 AND LENGTH(value) = 9")->execute();
    }

    public static function get_teachers($params = array())
    {
        $query = DB::select('contact.id', 'contact.first_name', 'contact.last_name', [DB::expr("CONCAT(`contact`.`first_name`, ' ', `contact`.`last_name`)"), 'full_name'])
            ->from(array(self::CONTACTS_TABLE, 'contact'))
            ->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'INNER')
                ->on('contact.id', '=', 'has_role.contact_id')
                ->on('has_role.role_id', '=', DB::expr(4))
            ->where('contact.delete', '=', 0)
            ->order_by('contact.first_name','ASC')
            ->order_by('contact.last_name','ASC');

        if (!empty($params['term'])) {
            $query->where(DB::expr("CONCAT(`contact`.`first_name`, ' ', `contact`.`last_name`)"), 'like', '%'.$params['term'].'%');
        }
        if (isset($params['publish'])) {
            $query->where('contact.publish', '=', $params['publish']);
        }

        if (!empty($params['limit'])) {
            $query->limit($params['limit']);
        }

        $teachers = $query->execute()->as_array();
        return $teachers;
    }

    public static function import_teachers_details()
    {
        $csv = '../database_seeding/staff_import.csv';
        $csv = fopen($csv, 'r');
        if($csv)
        {
            $columns = fgetcsv($csv, 0, ',');
            $i = 1;
            $teachers_details = array();
            while ($row = fgetcsv($csv, 0, ','))
            {
                $contact = array();
                foreach ($columns as $i => $column)
                {
                    $contact[$column] = trim($row[$i]);
                }
//                DB::insert('tmp_import_teachers_details', array_keys($student))->values($student)->execute();
                $teachers_details[] = $contact;
            }
            fclose($csv);
        }
//        echo '<h2>Data Inserted in Temp Import</h2>';
//        $teachers_details = DB::select()->from('tmp_import_teachers_details')->execute()->as_array();

        $family = new Model_Family();
        $no_family_name = 'No Family Teacher';
        $family->load(array('family_name'=>$no_family_name));
        $saved = $family->save();
        if ($saved)
        {
            $no_family_id = $family->get_id();
        }

        echo "<table border=1><tr><th>ID</th><th>Teacher</th><th>Action</th></tr>";
        foreach($teachers_details as $teacher)
        {
            $teacher['last_name'] = $teacher['first_name']!=''?substr($teacher['full_name'],strpos($teacher['full_name'], $teacher['first_name'])+strlen($teacher['first_name'])):$teacher['full_name'];
            $teacher['first_name'] = trim($teacher['first_name']);
            $teacher['last_name'] = trim($teacher['last_name']);

            /*** Get The Residence Formated ***/
            $address = array(
                'address1'=>$teacher['address1'],
                'address2'=>$teacher['address2'],
                'address3'=>$teacher['address3'],
                'address4'=>$teacher['address4'],
                'address5'=>$teacher['address5'],
            );
            if ($address['address5']=='')
            {
                if ($address['address4']=='')
                {
                    if ($address['address3']=='')
                    {
                        $address['address5']=$address['address2'];
                        $address['address4']=$address['address1'];
                        $address['address2']='';
                    }
                    else
                    {
                        $address['address5']=$address['address3'];
                        $address['address4']=$address['address2'];
                        $address['address3']='';
                        $address['address2']='';
                    }
                }
                else
                {
                    $address['address5']=$address['address4'];
                    $address['address4']=$address['address3'];
                    $address['address3']='';
                }
            }
            $address['address5'] = substr($address['address5'],strpos($address['address5'],' '));
            $county_id = Model_Residence::get_county_id($address['address5']);

            $notifications = array();
            if ( ! empty($teacher['email']) OR $teacher['email'] != '')
            {
                $notifications[0]=array('id'=>'new','notification_id'=>1,'value'=>$teacher['email']);
            }
            if ( ! empty($teacher['phone_number']) OR $teacher['phone_number'] != '' )
            {
                $notifications[2]=array('id'=>'new','notification_id'=>2,'value'=>intval(str_replace('-','',$teacher['phone_number'])));
            }

            $roles = array(1,4);
            if ($teacher['additional_staff'] == 'Yes')
            {
                $roles = array(1);
            }
            if ($teacher['supervisor'] == 'Yes')
            {
                $roles[] = 5;
            }
            if ($teacher['admin_ennis'] == 'Yes')
            {
                $roles[] = 6;
            }

            $teacher_data = array(
                'id'=>'',
                'type'=>1,
                'family_id'=>empty($teacher['last_name'])?$no_family_id:'',
                'is_primary'=>1,
                'title'=>NULL,
                'first_name'=>empty($teacher['first_name'])?$teacher['last_name']:$teacher['first_name'],
                'last_name'=>empty($teacher['last_name'])?$no_family_name:$teacher['last_name'],
                'address_id'=>'',
                'date_of_birth'=>'',
                'school_id'=>'',
                'year_id'=>'',
                'notifications_group_id'=>'',
                'subtype_id'=>2,
                'notes'=>'',
                'roles'=>$roles,
                'notifications'=>$notifications,
                'address1'=>$address['address1'],
                'address2'=>$address['address2'],
                'address3'=>$address['address3'],
                'town'  =>$address['address4'],
                'country'=>'IE',
                'county'=>$county_id,
                'pps_number'=>trim(str_replace(' ','',$teacher['pps']))
            );

            // Check If teacher exist
            $contacts3 = DB::select('c.id','c.residence','c.family_id')
                ->from(array('plugin_contacts3_contacts','c'))->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'INNER')
                ->on('c.id', '=', 'has_role.contact_id')
                ->where('first_name','LIKE',$teacher['first_name'])
                ->where('last_name','LIKE',$teacher['last_name'])
                ->execute()
                ->as_array();
            if ($contacts3)
            {
                $teacher_data['id']=$contacts3[0]['id'];
                $teacher_data['family_id']=$contacts3[0]['family_id'];
                $teacher_data['address_id']=$contacts3[0]['residence'];
                $teacher_data['residence']=$contacts3[0]['residence'];
                $action = 'Updated';
            }
            else
            {
                $family = new Model_Family();
                $family->load(array('family_name'=>$teacher['last_name']));
                $saved = $family->save();
                if ($saved)
                {
                    $family_id = $family->get_id();
                }
                $teacher_data['family_id']=$family_id;
                $action = 'Created';
            }
            $contact = new Model_Contacts3($teacher_data['id']);
            $contact->load($teacher_data);
            $contact->address->load($teacher_data);
            $contact->save();
            echo '<tr><td>'.$contact->get_id().'</td><td>'.$teacher_data['first_name'] . ' ' . $teacher_data['last_name'].'</td><td>'.$action.'</td></tr>';
        }
//        DB::query(Database::SELECT,"UPDATE plugin_contacts3_contacts SET family_id = ".$no_family_id." WHERE ISNULL(plugin_contacts3_contacts.family_id)")->execute();
//        DB::query(Database::SELECT,"DROP TABLE tmp_import_teachers_details")->execute();
    }

    public static function import_contact_bookings()
    {
        // CSV file will need to have the data sorted by contact_id then schedule_id
        //header('content-type: text/plain; charset=utf-8');
        ini_set('max_execution_time', 0);
        ob_start();
        //echo "mem:" . memory_get_usage() . "\n";
        $csv = '../database_seeding/booking_import.csv';
        $csv = fopen($csv, 'r');

        if($csv)
        {
            $columns = fgetcsv($csv, 0, ',');
            $bookings = array();

            while ($row = fgetcsv($csv, 0, ','))
            {
                $booking = array();
                foreach ($columns as $i => $column)
                {
                    $booking[$column] = trim($row[$i]);
                }
                $bookings[] = $booking;
            }
            fclose($csv);

            // Sort bookings
            $sort = array();
            foreach($bookings as $key => $row) {
                $schedule_sort[$key] = $row['schedule_id'];
                $contacts[$key] = $row['contact_id'];
            }
            # sort by contact_id then schedule_id
            array_multisort($contacts, SORT_ASC, $schedule_sort, SORT_ASC,$bookings);

            Database::instance()->begin();
            try
            {
                echo "<table border=1><tr><th>System Student Name</th><th>Contact ID</th><th>File Student Name</th><th>Schedule</th><th>Cost</th><th>Transaction</th><th>Payment</th></tr>";
                foreach ( $bookings as $key => $booking)
                {
					$booking_date = DateTime::createFromFormat('d/m/y', $booking['date']);
					$booking['date'] = $booking_date->format('Y-m-d');
                    $contact = new Model_Contacts3($booking['contact_id']);
                    $student = $contact->get_contact_name();
                    echo "<tr><td>".$student."</td><td>".$booking['contact_id']."</td><td>".$booking['student_name']."</td><td>".$booking['schedule_id']."</td>";
                    $schedule = DB::select('fee_amount','payment_type')
                        ->from('plugin_courses_schedules')
                        ->where('id','=',$booking['schedule_id'])
                        ->execute()->as_array();
                    $schedule_cost = $schedule[0]['fee_amount'];
                    echo "<td>".$schedule_cost."</td>";

                    // Check if booking exist in case of multiple payments
                    $booked = DB::select('booking_id')
                        ->from('plugin_ib_educate_bookings')
                        ->where('contact_id','=',$booking['contact_id'])
                        ->where('schedule_id','=',$booking['schedule_id'])
                        ->where('booking_status','!=',3)
                        ->execute()->as_array();
                    if ( ! $booked)
                    {
                        // Insert Booking
                        $booking_insert = array(
                            'contact_id'     => $booking['contact_id'],
                            'amount'         => $schedule_cost,
                            'created_date'   => $booking['date'],
                            'modified_date'  => NULL,
                            'schedule_id'    => $booking['schedule_id'],
                            'booking_status' => 2
                        );
                        $booking_id = DB::insert('plugin_ib_educate_bookings',array_keys($booking_insert))->values($booking_insert)->execute();

                        $b_schedule = array('booking_id' => $booking_id[0], 'schedule_id' => $booking['schedule_id']);
                        DB::insert('plugin_ib_educate_booking_has_schedules',array_keys($b_schedule))
                            ->values($b_schedule)
                            ->execute();

                        // Get schedule timeslots
                        $schedule_events = DB::select('id','datetime_start')->from('plugin_courses_schedules_events')->where('schedule_id','=',$booking['schedule_id'])->execute()->as_array();
                        $table_id = Model_EducateNotes::get_table_link_id_from_name('plugin_ib_educate_booking_items');

                        // Insert all timeslots as attending
                        foreach ($schedule_events as $event)
                        {
                            $attending = $event['datetime_start'] > date("Y-m-d H:i:s") ? 1 : 0;
                            $periods = array('booking_id' => $booking_id[0], 'period_id' => $event['id'],'attending'=>$attending);
                            $booking_item_id = DB::insert('plugin_ib_educate_booking_items',array_keys($periods))->values($periods)->execute();

                            if ($attending == 0)
                            {
                                $notes = array('note'=>'Imported After Start Date','link_id'=>$booking_item_id[0],'table_link_id'=>$table_id);
                                DB::insert('plugin_contacts3_notes',array_keys($notes))->values($notes)->execute();
                            }
                        }

                        // Create transactions
                        $ti = array(
                            'booking_id' => $booking_id[0],
                            'amount'     => $schedule_cost,
                            'total'      => $schedule_cost,
                            'contact_id' => $booking['contact_id'],
                            'created'    => $booking['date'],
                            'type'       => $schedule[0]['payment_type']
                        );
                        $transaction = DB::insert('plugin_bookings_transactions',array_keys($ti))->values($ti) ->execute();
                        $transaction = is_array($transaction) ? $transaction[0] : $transaction;
                        $ts = array('transaction_id'=>$transaction,'schedule_id'=>$booking['schedule_id']);
                        DB::insert('plugin_bookings_transactions_has_schedule',array_keys($ts))->values($ts)->execute();
                    }
                    else  // Already booked get the transaction
                    {
                        $transaction = DB::select('t.id')
                            ->from(array('plugin_bookings_transactions','t'))
                            ->join(array('plugin_bookings_transactions_has_schedule','s'))->on('t.id','=','s.transaction_id')
                            ->where('t.contact_id','=',$booking['contact_id'])->and_where('s.schedule_id','=',$booking['schedule_id'])
                            ->execute()
                            ->as_array();
                        $transaction = $transaction[0]['id'];
                    }
                    echo "<td>" . $transaction . "</td><td>";
                    if ($booking['amount']!='' AND $booking['amount'] > 0)
                    {
                        // Insert Payment
                        $pay = array(
                            'transaction_id' => $transaction,
                            'type'           => strtolower($booking['payment_type']),
                            'amount'         => $booking['amount'],
                            'bank_fee'       => 0,
                            'status'         => 2,
                            'created'        => $booking['date']
                        );
                        $payment = DB::insert('plugin_bookings_transactions_payments',array_keys($pay))->values($pay)->execute();
                        echo $payment[0];
                    }
                        echo "</td></tr>";

                }
                echo "</table>";
                Database::instance()->commit();
            }
            catch(Exception $e)
            {
                Database::instance()->rollback();
                throw $e;
            }
        }
        else {}
        die();
    }

    public static function is_same_name($name1a, $name2a)
    {
        $name1a = str_ireplace(array('x000B', '_'), '', $name1a);
        $name1a = preg_replace('/\s+/', ' ', trim($name1a, '" '));
        $name2a = str_ireplace(array('x000B', '_'), '', $name2a);
        $name2a = preg_replace('/\s+/', ' ', trim($name2a, '" '));

        $name1a = strtolower($name1a);
        $name2a = strtolower($name2a);
        $common_words = array(' o ', " o'", " mc ", " mc'");
        $name1 = str_replace($common_words, ' ', $name1a);
        $name2 = str_replace($common_words, ' ', $name2a);
        similar_text($name1a, $name2a, $similarity1);
        similar_text($name1, $name2, $similarity2);
        $similarity = max($similarity1, $similarity2);

        if($similarity > 85){
            //echo "similarity:$similarity:" . $name1, " : ",$name2,"\n";
            return true;
        }

        $ldiff = levenshtein($name1, $name2);
        if($ldiff < (strlen($name1) / 4)){
            //echo "ldiff:$ldiff:" . $name1, " : ",$name2,"\n";
            return true;
        }

        $common_words = array('o', 'mc');
        $name1 = str_replace($common_words, '', $name1a);
        $name2 = str_replace($common_words, '', $name2a);

        $s1 = preg_split('/\s+/', strtolower($name1));
        $s2 = preg_split('/\s+/', strtolower($name2));

        // remove common words;
        foreach($s1 as $i => $word){
            if(in_array($word, $common_words)){
                unset($s1[$i]);
            }
        }
        $s1 = array_values($s1);
        foreach($s2 as $i => $word){
            if(in_array($word, $common_words)){
                unset($s2[$i]);
            }
        }
        $s2 = array_values($s2);

        sort($s1);
        sort($s2);

        $soundex1 = array();
        $soundex2 = array();

        foreach($s1 as $word){
            $soundex1[] = soundex($word);
        }
        foreach($s2 as $word){
            $soundex2[] = soundex($word);
        }
        if($soundex1 == $soundex2){
            //echo "soundex:" . $name1, " : ",$name2, ":", implode("", $soundex1), ":", implode("", $soundex2),"\n";
            return true;
        }

        if(count($s2) > count($s1)){
            foreach($s2 as $i => $word){

            }
        }
        return false;
    }
    
    public static function cleanup_duplicates_get_suggestions($role_id = null, $like = '')
    {
        DB::query(null, "UPDATE `plugin_contacts3_contacts` SET first_name=REPLACE(TRIM(first_name), '  ', ' ')")->execute();
        DB::query(null, "UPDATE `plugin_contacts3_contacts` SET last_name=REPLACE(TRIM(last_name), '  ', ' ')")->execute();

        $suggestions = array();
        if ($role_id) {
            $contactsq = DB::select('contacts.*')
                ->from(array(self::CONTACTS_TABLE, 'contacts'))
                    ->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'hrole'), 'inner')->on('contacts.id', '=', 'hrole.contact_id')
                ->where('hrole.role_id', '=', $role_id)
                ->and_where('contacts.delete', '=', 0)
                ->order_by(DB::expr('length(first_name) + length(last_name)'), 'desc')
                ->order_by('first_name', 'asc')
                ->order_by('last_name', 'asc')
                ->order_by('id', 'asc');
        } else {
            $contactsq = DB::select('*')
                ->from(self::CONTACTS_TABLE)
                ->order_by(DB::expr('length(first_name) + length(last_name)'), 'desc')
                ->order_by('first_name', 'asc')
                ->order_by('last_name', 'asc')
                ->order_by('id', 'asc');
        }
        if ($like) {
            $contactsq->and_where('first_name', 'like', $like . '%');
        }
        $contacts_ = $contactsq->execute()->as_array();
        $contacts = array();
        foreach($contacts_ as $contact){
            $contact['fullname'] = $contact['first_name'] . ' ' . $contact['last_name'];
            $contacts[$contact['id']] = $contact;
        }
        unset($contacts_);
        $replaced = array();
        foreach($contacts as $i => $contact1){
            if(isset($replaced[$contact1['id']]))continue;

            foreach($contacts as $j => $contact2){
                if($contact1['id'] == $contact2['id'] || isset($replaced[$contact2['id']]))continue;

                if($contact1['fullname'] == $contact2['fullname'] || self::is_same_name($contact1['fullname'], $contact2['fullname'])){
                    if(@$contact1['correct_name'] || $contact1['id'] < $contact2['id'] || strlen($contact1['fullname']) > strlen($contact2['fullname'])){
                        if(isset($replaced[$contact1['id']])){
                            $replaced[$contact2['id']] = $replaced[$contact1['id']];
                        } else {
                            $replaced[$contact2['id']] = $contact1['id'];
                        }
                        $suggestions[$contact2['fullname']] = $contact1['fullname'];
                    } else {
                        if(isset($replaced[$contact2['id']])){
                            $replaced[$contact1['id']] = $replaced[$contact2['id']];
                        } else {
                            $replaced[$contact1['id']] = $contact2['id'];
                        }
                        $suggestions[$contact1['fullname']] = $contact2['fullname'];
                    }
                    break;
                }
            }
        }

        //header('content-type: text/plain');
        //print_r($schools);
        //print_r($replaced);
        //ksort($suggestions);
        //print_r($suggestions);
        foreach($replaced as $from => $to){
            //echo $schools[$from]['name'] . ' => ' . $schools[$to]['name'] . "\n";
        }

        return array('contacts' => $contacts, 'replaced' => $replaced);
    }

    public static function cleanup_duplicates($post)
    {
        //header('content-type: text/plain');print_r($post);die();
        DB::query(null, "UPDATE `plugin_contacts3_contacts` SET first_name=REPLACE(TRIM(first_name), '  ', ' ')")->execute();
        DB::query(null, "UPDATE `plugin_contacts3_contacts` SET last_name=REPLACE(TRIM(last_name), '  ', ' ')")->execute();

        Database::instance()->begin();
        foreach($post['first_name'] as $id => $first_name){
            DB::update('plugin_contacts3_contacts')
                ->set(array('first_name' => $first_name, 'last_name' => $post['last_name'][$id]))
                ->where('id', '=', $id)->execute();
        }
        foreach($post['replace'] as $from => $to){
            if($to){
                DB::update('plugin_courses_schedules')->set(array('trainer_id' => $to))->where('trainer_id', '=', $from)->execute();
                DB::update('plugin_courses_schedules_events')->set(array('trainer_id' => $to))->where('trainer_id', '=', $from)->execute();
                DB::update('plugin_contacts3_contacts')->set(array('delete' => 1))->where('id', '=', $from)->execute();
                $activity = new Model_Activity();
                $activity->set_item_id($from);
                $activity->set_item_type('user');
                $activity->set_action('delete');
                $activity->save();
            }
        }
        Database::instance()->commit();
    }

    public static function bulk_transfer_delete($contacts)
    {

        try {
            Database::instance()->begin();

            foreach ($contacts as $contact_id => $details) {
                if ($details['action'] == 'delete') {
                    DB::update('plugin_contacts3_contacts')->set(array('delete' => 1))->where('id', '=', $contact_id)->execute();
                    $activity = new Model_Activity();
                    $activity->set_item_id($contact_id);
                    $activity->set_item_type('user');
                    $activity->set_action('delete');
                    $activity->save();
                } else if ($details['action'] == 'transfer') {
                    $contact3 = new Model_Contacts3($details['contact_id']);
                    $details['family_id'] = $contact3->get_family_id();
                    DB::update('plugin_ib_educate_bookings')->set(array('bill_payer' => $details['contact_id']))->where('bill_payer', '=', $contact_id)->execute();
                    DB::update('plugin_ib_educate_bookings')->set(array('contact_id' => $details['contact_id']))->where('contact_id', '=', $contact_id)->execute();
                    DB::update('plugin_bookings_transactions')->set(array('contact_id' => $details['contact_id'], 'family_id' => $details['family_id']))->where('contact_id', '=', $contact_id)->execute();


                    DB::update('plugin_courses_schedules')->set(array('trainer_id' => $details['contact_id']))->where('trainer_id', '=', $contact_id)->execute();
                    DB::update('plugin_courses_schedules_events')->set(array('trainer_id' => $details['contact_id']))->where('trainer_id', '=', $contact_id)->execute();

                    DB::update('plugin_contacts3_contacts')->set(array('delete' => 1))->where('id', '=', $contact_id)->execute();
                    $activity = new Model_Activity();
                    $activity->set_item_id($contact_id);
                    $activity->set_item_type('user');
                    $activity->set_action('delete');
                    $activity->save();
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function get_permissions()
    {
        return $this->permissions;
    }

    public function set_permissions($user_ids)
    {
        $this->permissions = $user_ids;
    }

    /*
     * can accept an id, an array of ids
     * */
    public static function get_contact_ids_by_user($user_ids)
    {
        $ids = array();
        if (!is_array($user_ids)) {
            $ids = array($user_ids);
        }
        if (count($ids) == 0) {
            return array();
        }

        // practically there will be just one contact mapped to a user, though we may want to have MxN relations later. make it flexible
        $contacts3 = DB::select('contacts.*')
            ->from(array(self::CONTACTS_TABLE, 'contacts'))
                ->join(array(self::TABLE_PERMISSION_LIMIT, 'permissions'), 'inner')->on('contacts.id', '=', 'permissions.contact3_id')
            ->where('permissions.user_id', 'in', $ids)
            ->execute()
            ->as_array();
        return $contacts3;
    }

    public static function get_all_family_members_for_guardian_by_user($user_ids)
    {
        $contacts3 = self::get_contact_ids_by_user($user_ids);
        $family_ids = array();
        foreach ($contacts3 as $contact3) {
            $family_ids[] = $contact3['family_id'];
        }

        $family_ids = empty($family_ids) ? array(-1) : $family_ids;

        $result = $contacts3;

        $tmp = array();
        if ($family_ids) {
            $children_subquery = DB::select('has_role.*')
                ->from(array(Model_Contacts3::CONTACT_ROLE_RELATION_TABLE, 'has_role'))
                ->join(array(Model_Contacts3::ROLE_TABLE, 'role'))->on('has_role.role_id', '=', 'role.id')
                ->where('role.stub', '=', 'student');

            $tmp = DB::select('contact.*', array(DB::expr('IF(`child_role`.`role_id`, 1, 0)'), 'is_child'))
                ->from(array(self::CONTACTS_TABLE, 'contact'))
                ->join(array($children_subquery, 'child_role'), 'left')->on('child_role.contact_id', '=', 'contact.id')
                ->where('contact.delete', '=', 0)
                ->and_where('contact.family_id', 'in', $family_ids)
                ->order_by('contact.is_primary', 'desc') // primary contacts first
                ->order_by('is_child', 'asc') // non-children before children
                ->order_by('contact.first_name', 'asc')
                ->execute()
                ->as_array();
        }
        foreach ($tmp as $tcontact) {
            $found = false;
            foreach ($contacts3 as $contact) {
                if ($tcontact['id'] == $contact['id']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $result[] = $tcontact;
            }
        }
        return $result;
    }

    public static function get_all_family_members_ids_for_guardian_by_user($user_id){
        $family = self::get_all_family_members_for_guardian_by_user($user_id);
        $members_ids = array();
        if($family) foreach($family as $member){
            $members_ids[] = $member['id'];
        }

        return $members_ids;
    }

    public static function get_user_by_contact_id($contact_id)
    {
        $user = DB::select('users.*')
            ->from(array('engine_users', 'users'))
            ->join(array(self::TABLE_PERMISSION_LIMIT, 'permissions'), 'inner')->on('users.id', '=', 'permissions.user_id')
            ->where('permissions.contact3_id', '=', $contact_id)
            ->execute()
            ->as_array();
        return array_shift($user);
    }

    public static function get_primary_contact_id_by_user_id($user_id)
    {
        $contacts3 = DB::select('contacts.*')
            ->from(array(self::CONTACTS_TABLE, 'contacts'))
            ->join(array(self::TABLE_PERMISSION_LIMIT, 'permissions'), 'inner')->on('contacts.id', '=', 'permissions.contact3_id')
            ->where('permissions.user_id', '=', $user_id)
            ->and_where('contacts.is_primary', '=', 1)
            ->execute()
            ->as_array();

        return array_shift($contacts3);
    }

    public static function get_by_email($email)
    {
        return DB::select('contacts.*')
            ->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'notificationse'))
            ->join(array(self::CONTACTS_TABLE, 'contacts'))->on('notificationse.deleted', '=', db::expr(0))
            ->where('notificationse.value', '=', $email)
            ->and_where_open()
            ->or_where('notificationse.group_id', '=', DB::expr('contacts.notifications_group_id'))
            ->or_where('notificationse.contact_id', '=', DB::expr('contacts.id'))
            ->and_where_close()
            ->and_where('contacts.delete', '=', 0)
            ->order_by('contacts.id', 'desc')
            ->execute()
            ->current();
    }

    public static function create_contact_for_external_register($post, $registered)
    {
        if ($registered['success']) {

            $family = null;
            $invited_by_contact3 = null;
            $invited_contact3 = null;
            if (@$post['invite_member'] && @$post['invite_hash']) {
                $invitation = self::invite_accept($post['invite_member'], $post['invite_hash']);
                if ($invitation !== false) {
                    if ($invitation['invited_contact_id']) {
                        $invited_contact3 = new Model_Contacts3($invitation['invited_contact_id']);
                        $invited_contact3->set_permissions(array($registered['id']));
                        $invited_contact3->set_linked_user_id($registered['id']);
                        $invited_contact3->save();
                        $family = new Model_Family($invited_contact3->get_family_id());
                    } else {
                        $invited_by_contact3 = new Model_Contacts3($invitation['invited_by_contact_id']);
                        $family = new Model_Family($invited_by_contact3->get_family_id());
                    }
                }
            }

            $existing_contact = self::get_by_email($post['email']);

            $create_contact = true;
            if ($existing_contact) {
                $create_contact = false;
                if ($existing_contact['linked_user_id'] == 0) {
                    $family = new Model_Family($existing_contact['family_id']);
                }
            }

            if ($family == null && $create_contact) {
                $family_data = array();
                $family_data['family_id'] = '';
                $family_data['family_primary_contact_id'] = '';
                $family_data['new_family'] = '1';
                $family_data['contact_detail_id'][] = 'new';
                $family_data['contact_detail_value'][] = $post['email'];
                $family_data['contact_detail_type_id'][] = 1;
                $family_data['notifications_group_id'] = '';
                $family = new Model_Family();
                $family->load(array('family_name' => !empty($post['surname']) ? $post['surname'] :$post['email']));

                if (Settings::instance()->get('contacts_create_family') == 1) {
                    $family->save();
                }
            }

            $contact_data = array();
            $contact_data['family_id'] = isset($family) ? $family->get_id() : null;
            $roles = new Model_Roles();

            if (@$post['role'] == 'Teacher') {
                $contact_data['roles'][] = 4;
                $user_role_id = $roles->get_id_for_role('Teacher');
                $contact_data['type'] = Model_Contacts3::find_type('Staff')['contact_type_id'];
            } else if (@$post['role'] == 'Mature Student') {
                $contact_data['roles'][] = 1;
                $contact_data['roles'][] = 3;
                $user_role_id = $roles->get_id_for_role('Mature Student');
                if (Settings::instance()->get('course_new_student_is_flexi') == 1) {
                    $contact_data['is_flexi_student'] = 1;
                }
                $contact_data['type'] = Model_Contacts3::find_type('Student')['contact_type_id'];
            } else if (@$post['role'] == 'Student') {
                $contact_data['roles'][] = 2;
                $user_role_id = $roles->get_id_for_role('Student');
                if (Settings::instance()->get('course_new_student_is_flexi') == 1) {
                    $contact_data['is_flexi_student'] = 1;
                }
                $contact_data['type'] = Model_Contacts3::find_type('Student')['contact_type_id'];
            } else if (@$post['role'] == 'Org rep') {
                $contact_data['roles'][] = self::get_contact_role_by_name($post['role'])['id'];
                $user_role_id = $roles->get_id_for_role('Org rep');
                $contact_data['type'] = Model_Contacts3::find_type('Org rep')['contact_type_id'];
            } else if(@$post['contact-type'] == 'individual') {
                if((isset($post['role']) && count(self::get_contact_role_by_name($post['role'])) > 0)) {
                    $contact_data['roles'][] = self::get_contact_role_by_name($post['role'])['id'];
                }
                $user_role_id = $roles->get_id_for_role($post['role']);
                $contact_data['type'] = (Model_Contacts3::find_type($post['role']) !== null)
                    ? Model_Contacts3::find_type($post['role'])['contact_type_id']
                    : Model_Contacts3::find_type('Student')['contact_type_id'];
            } else if ($invited_contact3) {
                if (in_array('supervisor', $invited_contact3->get_roles_stubs())) {
                    $user_role_id = $roles->get_id_for_role('Manager');
                } else if (in_array('admin', $invited_contact3->get_roles_stubs())) {
                    $user_role_id = $roles->get_id_for_role('Manager');
                } else if (in_array('teacher', $invited_contact3->get_roles_stubs())) {
                    $user_role_id = $roles->get_id_for_role('Teacher');
                } else {
                    $contact_data['roles'][] = 1;
                    $user_role_id = $roles->get_id_for_role('Parent/Guardian');
                }
            } else if(@$post['contact-type'] == 'organisation') {
                $contact_data['roles'][] = self::get_contact_role_by_name('Org rep')['id'];
                if (!isset($post['role_id'])) {
                    $user_role_id = $roles->get_id_for_role('Org rep');
                } else {
                    $user_role_id = $post['role_id'];
                }
            } else {
                $contact_data['roles'][] = 1;
                if (!isset($post['role_id'])) {
                    $user_role_id = $roles->get_id_for_role('Parent/Guardian');
                } else {
                    $user_role_id = $post['role_id'];
                }
            }

            if (!empty($existing_contact['id'])) {
                // roles are to stay the same as they were before...
                $existing_roles = $existing_contact['roles'];
                $contact_data['type'] = $existing_contact['type'];
                $type = new Model_Contacts3_Type($contact_data['type']);

                // Overwrite the default roles, with the current roles, if there are any.
                if (!empty($existing_roles)) {
                    $contact_data['roles'] = $existing_roles;
                }

                // If the contact has no role, but has the org_rep type, also make org_rep their role.
                if (empty($existing_roles) && $type->name == 'org_rep') {
                    $contact_data['roles'] = [self::get_contact_role_by_name('org_rep')['id']];
                    $contact_data['role'] = 'Org rep';
                    $user_role_id = $roles->get_id_for_role('Org rep');
                }
            }

            $user_params = array('role_id' => $user_role_id);
            if ($invited_by_contact3) {
                $user_params['email_verified'] = 1;
                $registered['verified'] = true;
            }
            DB::update('engine_users')
                ->set($user_params)
                ->where('id', '=', $registered['id'])
                ->execute();
            if(@$post['contact-type'] == 'individual') {
                $contact_data['type'] = Model_Contacts3::find_type('Student')['contact_type_id'] ?? Model_Contacts3::find_type('Family')['contact_type_id'] ?? 1;
            } else if(!isset($contact_data['type'])) {
                $contact_data['type'] = Model_Contacts3::find_type('Family')['contact_type_id'];
            }
           
            $contact_data['subtype_id'] = 1;
            $contact_data['notifications'] = array(
                array(
                    'id' => 'new',
                    'value' => $post['email'],
                    'notification_id' => 1
                )
            );

            $existing_first_name = $existing_contact['first_name'] ? $existing_contact['first_name'] : 'first_name';
            $existing_last_name  = $existing_contact['last_name']  ? $existing_contact['last_name'] : 'last_name';
            // name/first_name and surname/last_name indexes are inconsistent between the users and contacts tables
            $contact_data['first_name'] = !empty($post['name'])    ? $post['name']    : $existing_first_name;
            $contact_data['last_name']  = !empty($post['surname']) ? $post['surname'] : $existing_last_name;

            $settings = Settings::instance();
            $contact_data['preferences'] = [];
            if(!empty(Settings::instance()->get('contact_default_preferences'))) {
                foreach(Settings::instance()->get('contact_default_preferences') as $preference_id) {
                    $contact_data['preferences'][] = [
                        'preference_id' => $preference_id,
                        'value' => 1
                    ];
                }
            }
            if (in_array($settings->get('contacts3_emergency_notification'), array('SMS', 'BOTH')) ){
                $contact_data['preferences'][] = array(
                    'preference_id' => 1,
                    'notification_type' => 'sms',
                    'value' => 1
                );
            }
            if (in_array($settings->get('contacts3_emergency_notification'), array('EMAIL', 'BOTH')) ){
                $contact_data['preferences'][] = array(
                    'preference_id' => 1,
                    'notification_type' => 'email',
                    'value' => 1
                );
            }
            if (in_array($settings->get('contacts3_absentee_notification'), array('SMS', 'BOTH')) ){
                $contact_data['preferences'][] = array(
                    'preference_id' => 3,
                    'notification_type' => 'sms',
                    'value' => 1
                );
            }
            if (in_array($settings->get('contacts3_absentee_notification'), array('EMAIL', 'BOTH')) ){
                $contact_data['preferences'][] = array(
                    'preference_id' => 3,
                    'notification_type' => 'email',
                    'value' => 1
                );
            }
            if (in_array($settings->get('contacts3_account_notification'), array('SMS', 'BOTH')) ){
                $contact_data['preferences'][] = array(
                    'preference_id' => 2,
                    'notification_type' => 'sms',
                    'value' => 1
                );
            }
            if (in_array($settings->get('contacts3_account_notification'), array('EMAIL', 'BOTH')) ){
                $contact_data['preferences'][] = array(
                    'preference_id' => 2,
                    'notification_type' => 'email',
                    'value' => 1
                );
            }
            if (!empty($contact_data['roles']) && array_search(1, $contact_data['roles']) !== false) {
                $contact_data['preferences'][] = array(
                    'preference_id' => Model_Preferences::get_stub_id('bookings'),
                    'notification_type' => 'email',
                    'value' => 1
                );
            }
            if ($invited_contact3 == null && $create_contact) {
                $contact_relations = array();
                if(Settings::instance()->get('engine_enable_external_register') == '1' && $post['role'] == 'Org rep') {
                    $contact_data['job_title'] = $post['job_title'];
                    $contact_data['job_function_id'] = $post['job_function'];
                    $rs = new Model_Remotesync();
                    $cds = new Model_CDSAPI();
                    $post_domain = !empty($post['domain_name']) ? parse_url($post['domain_name']) : substr(strrchr($post['email'], "@"), 1);
                    if (is_array($post_domain)) {
                        $post_domain_name = !empty($post_domain['host']) ? $post_domain['host'] : $post_domain['path'];
                        $post_domain_name = str_replace('www.','', $post_domain_name);
                    } else {
                        $post_domain_name = $post_domain;
                    }
                    if(Settings::instance()->get('engine_enable_organisation_signup_flow') == '1'
                        && Settings::instance()->get('organisation_integration_api')) {
                        if (!empty(@$post['selected_organisation'])) {
                            if (is_numeric($post['selected_organisation'])) {
                                $organisation = new Model_Contacts3($post['selected_organisation']);

                                $synced = $rs->get_object_synced(Model_CDSAPI::API_NAME . '-Account', $post['selected_organisation']);
                                if ($synced) {
                                    if(Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                                        $cds_account = $cds->get_account($post['selected_organisation']);
                                    }

                                    $organisation->update_membership_for_organisation(@$cds_account['sp_membershipstatus']);
                                    $cds_billing_address = self::prepare_external_addresses($cds_account);
                                    if (!empty($cds_billing_address)) {
                                        $organisation->billing_address->load($cds_billing_address);
                                    }
                                    $organisation->save();
                                } else {
                                    self::create_external_account($organisation);
                                }
                                $contact_relations[] = array(
                                    'parent_id' => $organisation->get_id(),
                                    'position' => 'organisation');

                            } else {
                                $retrieved_account = array();
                                if (Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                                    $retrieved_account = $cds->get_account_by_remote_id($post['selected_organisation']);
                                }
                                if ($retrieved_account) {
                                    $synced_account = $rs->get_object_synced(Model_CDSAPI::API_NAME . '-Account', $post['selected_organisation'], 'remote');

                                    if (!empty($synced_account)) {
                                        $existing_organisation = new Model_Contacts3($synced_account['cms_id']);
                                        if (!empty($retrieved_account['sp_companyno'])) {
                                            $rs->save_object_synced(Model_NAVAPI::API_NAME . '-Account', @$retrieved_account['sp_companyno'], $existing_organisation->get_id());
                                        }
                                        $cds_billing_address = self::prepare_external_addresses($retrieved_account);
                                        if (!empty($cds_billing_address)) {
                                            $existing_organisation->billing_address->load($cds_billing_address);
                                        }
                                        $existing_organisation->set_domain_name($post_domain_name);
                                        $existing_organisation->save();
                                        $existing_organisation->update_membership_for_organisation(@$retrieved_account['sp_membershipstatus']);
                                        $existing_organisation->insert_notification(
                                            array('contact_id' => $existing_organisation->get_id(),
                                                'notification_id' => 1,
                                                'value' => $post['email']));
                                        $contact_relations[] = array(
                                            'parent_id' => $existing_organisation->get_id(),
                                            'position' => 'organisation');
                                    } else {
                                        $org_contact = new Model_Contacts3();
                                        $org_contact->set_first_name($post['org_name']);
                                        $org_contact->set_type(Model_Contacts3::find_type('Organisation')['contact_type_id']);
                                        $org_contact->set_subtype_id(0);
                                        $org_contact->set_is_primary(1);
                                        $post['town'] = !empty($post['town']) ?  $post['town'] : @$post['city'];
                                        $org_contact->address->load($post);
                                        $cds_billing_address = self::prepare_external_addresses($retrieved_account);
                                        if (!empty($cds_billing_address)) {
                                            $org_contact->billing_address->load($cds_billing_address);
                                        } else {
                                            $org_contact->billing_address->load($post);
                                        }
                                        $org_contact->set_domain_name($post_domain_name);
                                        $org_contact->save();
                                        $rs->save_object_synced(Model_CDSAPI::API_NAME . '-Account', $post['selected_organisation'], $org_contact->get_id());
                                        $org_contact->update_membership_for_organisation(@$retrieved_account['sp_membershipstatus']);
                                        $org_contact->insert_notification(
                                            array('contact_id' => $org_contact->get_id(),
                                                    'notification_id' => 1,
                                                    'value' => $post['email']));
                                        $organisation = $org_contact;
                                        $contact_relations[] = array(
                                            'parent_id' => $org_contact->get_id(),
                                            'position' => 'organisation');
                                    }

                                }
                            }
                        } else {
                            $org_contact = new Model_Contacts3();
                            $org_contact->set_first_name($post['org_name']);
                            $org_contact->set_type(Model_Contacts3::find_type('Organisation')['contact_type_id']);
                            $org_contact->set_subtype_id(0);
                            $org_contact->set_is_primary(1);
                            $post['town'] = !empty($post['town']) ?  $post['town'] : @$post['city'];
                            $org_contact->address->load($post);
                            $org_contact->billing_address->load($post);
                            $org_contact->set_domain_name($post_domain_name);
                            $org_contact->save();
                            self::create_external_account($org_contact);

                            $organisation = Model_Organisation::get_org_by_contact_id($org_contact->get_id());
                            if(!empty($post['org_size'])) {
                                $organisation->set_organisation_size_id($post['org_size']);
                            }
                            if (!empty($post['org_industry'])) {
                                $organisation->set_organisation_industry_id($post['org_industry']);
                            }
                            $organisation->save();
                            $org_contact->save();
                            $org_contact->insert_notification(
                                array('contact_id' => $org_contact->get_id(),
                                    'notification_id' => 1,
                                    'value' => $post['email']));
                            $contact_relations[] = array(
                                'parent_id' => $org_contact->get_id(),
                                'position' => 'organisation');
                            $org_rep_type = Model_Contacts3::find_type('Org rep');
                            $contact_data['type'] = ($org_rep_type === null) ? 1 : $org_rep_type['contact_type_id'];
                        }
                    } else {
                        $organisations = self::search(array('first_name' => $post['org_name'],
                            'type' => self::find_type('Organisation')['contact_type_id']));
                        if (!empty($organisations)) {
                            $org_contact = new Model_Contacts3($organisations[0]['id']);
                            $contact_relations[] = array(
                                'parent_id' => $org_contact->get_id(),
                                'position' => 'organisation');
                            $org_contact->insert_notification(
                                array('contact_id' => $org_contact->get_id(),
                                    'notification_id' => 1,
                                    'value' => $post['email']));
                        } else {
                            $org_contact = new Model_Contacts3();
                            $org_contact->set_first_name($post['org_name']);
                            $org_contact->set_type(Model_Contacts3::find_type('Organisation')['contact_type_id']);
                            $org_contact->set_subtype_id(0);
                            $org_contact->set_is_primary(1);
                            $post['town'] = !empty($post['town']) ?  $post['town'] : @$post['city'];
                            $org_contact->address->load($post);
                            $org_contact->billing_address->load($post);
                            $org_contact->set_domain_name($post_domain_name);
                            $org_contact->save();
                            $org_contact->insert_notification(
                                array('contact_id' => $org_contact->get_id(),
                                    'notification_id' => 1,
                                    'value' => $post['email']));
                            $organisation_data = Model_Organisation::get_org_by_contact_id($org_contact->get_id());
                            if(!empty($post['org_size'])) {
                                $organisation_data->set_organisation_size_id($post['org_size']);
                            }
                            if (!empty($post['org_industry'])) {
                                $organisation_data->set_organisation_industry_id($post['org_industry']);
                            }
                            $organisation_data->save();
                            $contact_relations[] = array(
                                'parent_id' => $org_contact->get_id(),
                                'position' => 'organisation');
                            $org_rep_type = Model_Contacts3::find_type('Org rep');
                            $contact_data['type'] = ($org_rep_type === null) ? 1 : $org_rep_type['contact_type_id'];
                        }
                    }
                }

                $contact3 = new Model_Contacts3($existing_contact ? $existing_contact['id'] : null);
                $user_id = (!empty($registered['id'])) ? $registered['id'] : $contacts3->get_linked_user_id();

                $contact3->load($contact_data);
                $contact3->set_permissions(array($user_id));
                $contact3->set_contact_relations($contact_relations);
                $contact3->set_linked_user_id($user_id);
                $contact3->save();
                $linked_organisation = $contact3->get_linked_organisation();
                if (!empty($organisation)) {
                    if (Settings::instance()->get('organisation_api_control_membership')
                        && Settings::instance()->get('organisation_integration_api')
                        && Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                        $membership_status = false;
                        $cds = new Model_CDSAPI();
                        $cds_account = $cds->get_account($linked_organisation->get_id());
                        if (!empty($cds_account)) {
                            $membership_status = @$cds_account['sp_membershipstatus'];
                        }
                        $linked_organisation->update_membership_for_organisation($membership_status);
                    }

                }
                //remove temporarily stored organisation data after signup finished
                if(!empty($post['signup'])) {
                    Model_Contacts3::delete_temporary_signup_data($post['signup']);
                }
                $ac_tags = array(
                    array('tag' => 'REGISTER', 'description' => 'External Register')
                );
                $ac_fields = array();
                if (@$post['org_name']) {
                    $ac_fields['Organisation'] = $post['org_name'];

                }
                if (@$post['org_industry']) {
                    $ac_fields['ACCT_INDUSTRY_VERTICAL'] = Model_Organisation::get_organization_industry($post['org_industry']);
                    if ($ac_fields['ACCT_INDUSTRY_VERTICAL']) {
                        $ac_fields['ACCT_INDUSTRY_VERTICAL'] = $ac_fields['ACCT_INDUSTRY_VERTICAL']['label'];
                    }
                }
                if (@$post['org_size']) {
                    $ac_fields['ACCT_NUMBER_OF_EMPLOYEES'] = Model_Organisation::get_organization_size($post['org_size']);
                    if ($ac_fields['ACCT_NUMBER_OF_EMPLOYEES']) {
                        $ac_fields['ACCT_NUMBER_OF_EMPLOYEES'] = $ac_fields['ACCT_NUMBER_OF_EMPLOYEES']['label'];
                    }
                }
                if (@$post['job_function']) {
                    $ac_fields['JOB_FUNCTION'] = Model_Contacts3::get_job_function($post['job_function']);
                    if ($ac_fields['JOB_FUNCTION']) {
                        $ac_fields['JOB_FUNCTION'] = $ac_fields['JOB_FUNCTION']['label'];
                    }
                }

                Model_Automations::run_triggers(
                    Model_Contacts3_Contactregistertrigger::NAME, array('contact_id' => $contact3->get_id(), 'tags' => $ac_tags, 'fields' => $ac_fields)
                );
            } else {
                if (!empty($existing_contact['id'])) {
                    $existing_contact_obj = new Model_Contacts3($existing_contact['id']);
                    $contact_relations = array();
                    $post_domain = !empty($post['domain_name']) ? parse_url($post['domain_name']) : substr(strrchr($post['email'], "@"), 1);
                    if (is_array($post_domain)) {
                        $post_domain_name = !empty($post_domain['host']) ? $post_domain['host'] : $post_domain['path'];
                        $post_domain_name = str_replace('www.','', $post_domain_name);
                    } else {
                        $post_domain_name = $post_domain;
                    }
                    if(Settings::instance()->get('engine_enable_external_register') == '1' && $post['role'] == 'Org rep') {
                        if(Settings::instance()->get('engine_enable_organisation_signup_flow') == '1'
                            && Settings::instance()->get('organisation_integration_api')) {
                            $rs = new Model_Remotesync();
                            $cds = new Model_CDSAPI();
                            if (!empty(@$post['selected_organisation'])) {
                                if (is_numeric($post['selected_organisation'])) {
                                    $organisation = new Model_Contacts3($post['selected_organisation']);
                                    $synced = $rs->get_object_synced(Model_CDSAPI::API_NAME . '-Account', $post['selected_organisation']);
                                    if ($synced) {
                                        if(Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                                            $cds_account = $cds->get_account($post['selected_organisation']);
                                        }

                                        $organisation->update_membership_for_organisation(@$cds_account['sp_membershipstatus']);
                                        $cds_billing_address = self::prepare_external_addresses($cds_account);
                                        if (!empty($cds_billing_address)) {
                                            $organisation->billing_address->load($cds_billing_address);
                                        }
                                        $organisation->save();
                                    } else {
                                        self::create_external_account($organisation);
                                    }
                                } else {
                                    $retrieved_account = array();
                                    if (Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                                        $retrieved_account = $cds->get_account_by_remote_id($post['selected_organisation']);
                                    }
                                    if ($retrieved_account) {
                                        $synced_account = $rs->get_object_synced(Model_CDSAPI::API_NAME . '-Account', $post['selected_organisation'], 'remote');

                                        if (!empty($synced_account)) {
                                            $existing_organisation = new Model_Contacts3($synced_account['cms_id']);
                                            if (!empty($retrieved_account['sp_companyno'])) {
                                                $rs->save_object_synced(Model_NAVAPI::API_NAME . '-Account', @$retrieved_account['sp_companyno'], $existing_organisation->get_id());
                                            }
                                            $cds_billing_address = self::prepare_external_addresses($retrieved_account);
                                            if (!empty($cds_billing_address)) {
                                                $existing_organisation->billing_address->load($cds_billing_address);
                                            }
                                            $existing_organisation->set_domain_name($post_domain_name);
                                            $existing_organisation->save();
                                            $existing_organisation->update_membership_for_organisation(@$retrieved_account['sp_membershipstatus']);
                                            $existing_organisation->insert_notification(
                                                array('contact_id' => $existing_organisation->get_id(),
                                                    'notification_id' => 1,
                                                    'value' => $post['email']));
                                            $contact_relations[] = array(
                                                'parent_id' => $existing_organisation->get_id(),
                                                'position' => 'organisation');
                                        } else {
                                            $org_contact = new Model_Contacts3();
                                            $org_contact->set_first_name($post['org_name']);
                                            $org_contact->set_type(Model_Contacts3::find_type('Organisation')['contact_type_id']);
                                            $org_contact->set_subtype_id(0);
                                            $org_contact->set_is_primary(1);
                                            $post['town'] = !empty($post['town']) ?  $post['town'] : @$post['city'];
                                            $org_contact->address->load($post);
                                            $cds_billing_address = self::prepare_external_addresses($retrieved_account);
                                            if (!empty($cds_billing_address)) {
                                                $org_contact->billing_address->load($cds_billing_address);
                                            } else {
                                                $org_contact->billing_address->load($post);
                                            }
                                            $org_contact->set_domain_name($post_domain_name);
                                            $org_contact->save();
                                            $rs->save_object_synced(Model_CDSAPI::API_NAME . '-Account', $post['selected_organisation'], $org_contact->get_id());
                                            $org_contact->update_membership_for_organisation(@$retrieved_account['sp_membershipstatus']);
                                            $org_contact->insert_notification(
                                                array('contact_id' => $org_contact->get_id(),
                                                    'notification_id' => 1,
                                                    'value' => $post['email']));
                                            $contact_relations[] = array(
                                                'parent_id' => $org_contact->get_id(),
                                                'position' => 'organisation');
                                            self::create_external_account($org_contact);

                                            $organisation = Model_Organisation::get_org_by_contact_id($org_contact->get_id());
                                            if(!empty($post['org_size'])) {
                                                $organisation->set_organisation_size_id($post['org_size']);
                                            }
                                            if (!empty($post['org_industry'])) {
                                                $organisation->set_organisation_industry_id($post['org_industry']);
                                            }
                                            $organisation->save();
                                            $org_contact->save();
                                            $org_contact->insert_notification(
                                                array('contact_id' => $org_contact->get_id(),
                                                    'notification_id' => 1,
                                                    'value' => $post['email']));
                                            $org_rep_type = Model_Contacts3::find_type('Org rep');
                                            $contact_data['type'] = ($org_rep_type === null) ? 1 : $org_rep_type['contact_type_id'];
                                        }
                                    }
                                }
                            } else {
                                $org_contact = new Model_Contacts3();
                                $org_contact->set_first_name($post['org_name']);
                                $org_contact->set_type(Model_Contacts3::find_type('Organisation')['contact_type_id']);
                                $org_contact->set_subtype_id(0);
                                $org_contact->set_is_primary(1);
                                $post['town'] = !empty($post['town']) ?  $post['town'] : @$post['city'];
                                $org_contact->address->load($post);
                                $org_contact->billing_address->load($post);
                                $org_contact->set_domain_name($post_domain_name);
                                $org_contact->save();
                                $org_contact->update_membership_for_organisation(false);
                                $org_contact->insert_notification(
                                    array('contact_id' => $org_contact->get_id(),
                                        'notification_id' => 1,
                                        'value' => $post['email']));
                                self::create_external_account($org_contact);
                                $organisation = Model_Organisation::get_org_by_contact_id($org_contact->get_id());
                                if(!empty($post['org_size'])) {
                                    $organisation->set_organisation_size_id($post['org_size']);
                                }
                                if (!empty($post['org_industry'])) {
                                    $organisation->set_organisation_industry_id($post['org_industry']);
                                }
                                $organisation->save();
                                $org_contact->save();
                                $org_contact->insert_notification(
                                    array('contact_id' => $org_contact->get_id(),
                                        'notification_id' => 1,
                                        'value' => $post['email']));
                                $contact_relations[] = array(
                                    'parent_id' => $org_contact->get_id(),
                                    'position' => 'organisation');
                                $org_rep_type = Model_Contacts3::find_type('Org rep');
                                $contact_data['type'] = ($org_rep_type === null) ? 1 : $org_rep_type['contact_type_id'];
                            }
                        }
                        $org_rep_type = Model_Contacts3::find_type('Org rep');
                        $contact_data['type'] = ($org_rep_type === null) ? 1 : $org_rep_type['contact_type_id'];
                        $existing_contact_obj->set_type($contact_data['type']);
                        $existing_contact_obj->set_contact_relations($contact_relations);
                    }
                    $existing_contact_obj->set_permissions(array($registered['id']));
                    $existing_contact_obj->set_linked_user_id($registered['id']);
                    $existing_contact_obj->save();
                }
            }

            DB::update('engine_users')->set(array('default_home_page' => '/admin'))->where('id', '=', $registered['id'])->execute();
        }

        return $registered;
    }

    public static function create_external_account($organisation, $api_type = 'cds') {
        if ($api_type == 'cds') {
            $cds = new Model_CDSAPI();
            $county_code = $organisation->get_billing_address()->get_county_code();
            $country_code = $organisation->get_billing_address()->get_country();
            if (empty($country_code)) {
                $country_code = 'IE';
            }
            $country_data = Model_Country::get_countries();
            if (array_key_exists($country_code, $country_data)) {
                $current_country = $country_data[$country_code];
                $alpha3_country_code = @$current_country['alpha3'];
            } else {
                $alpha3_country_code = '';
            }
            $sp_countycode = !empty($county_code) ? $county_code : 'OT';
            $sp_countrycode = !empty($country_code) ? $alpha3_country_code : 'IE';
            $domain_name = $organisation->get_domain_name();
            $params = explode('.', $domain_name);
            if(sizeof($params) == 3 && $params[0] == 'www') {
                $homepage = $domain_name;
            } else {
                $homepage = 'www.' . $domain_name;
            }
            $rs = new Model_Remotesync();
            $navapi_exists = $rs->get_object_synced(Model_NAVAPI::API_NAME . '-Account', $organisation->get_id());

            $create_exisiting_organisation_account = array(
                'name' => $organisation->get_first_name(),
                'address1_line1' => $organisation->get_billing_address()->get_address1(),
                'address1_line2' => $organisation->get_billing_address()->get_address2(),
                'address1_line3' => $organisation->get_billing_address()->get_address3(),
                'address1_city'  => $organisation->get_billing_address()->get_town(),
                'address1_postalcode' => $organisation->get_billing_address()->get_postcode(),
                'sp_homepage'    => $homepage,
                'sp_countycode'  => $sp_countycode,
                'sp_countrycode' => $sp_countrycode,
                'emailaddress1'  => $organisation->get_email(),
                'sp_companyno'   => @$navapi_exists['remote_id'],
                'sp_tmsid'       => (string) $organisation->get_id(),
                'sp_publicdomain' => 'false'
            );
            $check_existing = $cds->search_accounts('sp_homepage', $homepage, true, false);
            $public_exists = false;
            //check if public organisaton with this domain name exists, if yes, don't create a new account in CDS
            if (!empty($check_existing)) {
                foreach($check_existing as $existing_account) {
                    if (!$existing_account['sp_publicdomain'] || $existing_account['sp_publicdomain'] != 'false') {
                        $public_exists = true;
                    }
                }
            }
            if ($public_exists) {
                $create_exisiting_organisation_account['sp_publicdomain'] = 'true';
                $create_exisiting_organisation_account['sp_homepage'] = '';
                $organisation->set_is_public_domain(true);
                $organisation->save();
            }
            if(Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                $result = $cds->create_account($organisation->get_id(),
                    $create_exisiting_organisation_account);
            }
        }

    }

    public static function save_temporary_signup_data($id, $data) {
        $values = array(
            'signup_id' => $id,
            'signup_data' => json_encode($data),
            'date_created' => date('Y-m-d H:i:s'),
            'date_modified' => date('Y-m-d H:i:s')
        );
        $inserted = DB::insert(self::TEMPORARY_SIGNUP_TABLE)->values($values)->execute();
        return $inserted;
    }

    public static function get_temporary_signup_data($id) {
        $signup_data_query = DB::select('signup_data')
                ->from(self::TEMPORARY_SIGNUP_TABLE)
            ->where('signup_id', '=', $id)
            ->execute()
            ->current();
        if (empty($signup_data_query['signup_data'])) {
            return array();
        }
        return json_decode($signup_data_query['signup_data'], 1);
    }

    public static function delete_temporary_signup_data($id) {
        if(!empty($id)) {
            DB::delete(self::TEMPORARY_SIGNUP_TABLE)->where('signup_id', '=' , $id)->execute();
        }
        return true;
    }

    public static function prepare_external_addresses($cds_account) {
        $cds_billing_address = array();
        if (!empty($cds_account['address1_line1'])) {
            $cds_billing_address['address1'] = @$cds_account['address1_line1'];
            $cds_billing_address['address2'] = @$cds_account['address1_line2'];
            $cds_billing_address['address3'] = @$cds_account['address1_line3'];
            $cds_billing_address['town']     = @$cds_account['address1_city'];
            if (!empty( @$cds_account['sp_countrycode'])) {
                $countries = Model_Country::get_countries(3);
                if (!empty($countries)) {
                    $cds_billing_address['country']  = array_key_exists($cds_account['sp_countrycode'], $countries)
                        ? $countries[$cds_account['sp_countrycode']]['id'] : '';

                } else {
                    $cds_billing_address['country']  = '';
                }

            } else {
                $cds_billing_address['country']  = '';
            }
            if (!empty( @$cds_account['sp_countycode'])) {
                $county = Model_Cities::get_counties($cds_account['sp_countycode'], 'code');
                if (!empty($county)) {
                    $county = reset($county);
                    $cds_billing_address['county'] = $county['id'];
                } else {
                    $cds_billing_address['county'] = 0;
                }

            } else {
                $cds_billing_address['county'] = 0;
            }
            $cds_billing_address['postcode'] = @$cds_account['address1_postalcode'];
        }
        return $cds_billing_address;
    }

    public static function check_existing_contact_before_external_register($post)
    {
        $result = array('success' => true, 'error' => '');
        $email = $post['email'];
        $existing_contact = DB::select('contacts.*')
            ->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'notifications'))
                ->join(array(self::CONTACTS_TABLE, 'contacts'))->on('notifications.deleted', '=', db::expr(0))
            ->where('notifications.value', '=', $email)
            ->and_where_open()
                ->or_where('notifications.group_id', '=', DB::expr('contacts.notifications_group_id'))
                ->or_where('notifications.contact_id', '=', DB::expr('contacts.id'))
            ->and_where_close()
            ->and_where('contacts.delete', '=', 0)
            ->and_where('contacts.created_by', 'is not', null)
            ->execute()
            ->current();
        if (@$post['invite_member'] && @$post['invite_member']) {
            if (self::invite_check($post['invite_member'], $post['invite_hash'])) {
                $result['email_verified'] = 1;
            }
        } else {
            $userm = new Model_Users();
            $existing_user = $userm->get_user_by_email($email);
            if ($existing_contact && $existing_user == null) { //if user exists then it will show a different warning to reset password link
                $result = array(
                    'success' => false,
                    'error' => 'We already have your details in the system from a previous telephone or online booking.
Please confirm your mobile number to confirm your identity. We will then e-mail you a verification link.',
                    'redirect' => '/admin/login/duplicate_contact?' . http_build_query(array('email' => $email))
                );
            } else {

            }
        }
        return $result;
    }

    public static function get_existing_contact_by_email_and_mobile($email, $mobile = false)
    {
        if($mobile) {
            $mobile = str_replace(array(' ', '+', '.', '-'), '', $mobile);
            $mobile = substr($mobile, strlen($mobile) - 7);
        }
            $q = DB::select('contacts.*')
            ->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'notificationse'))
                ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'notificationsm'))
                    ->on('notificationse.group_id', '=', 'notificationsm.group_id')
                    ->on('notificationse.contact_id', '=', 'notificationsm.contact_id')
            ->join(array(self::CONTACTS_TABLE, 'contacts'))->on('notificationse.deleted', '=', db::expr(0))
            ->where('notificationse.value', '=', $email);
            if($mobile){
                $q->and_where('notificationsm.value', 'like', '%' . $mobile);
            }
            $existing_contact = $q->and_where_open()
                ->or_where('notificationse.group_id', '=', DB::expr('contacts.notifications_group_id'))
                ->or_where('notificationse.contact_id', '=', DB::expr('contacts.id'))
            ->and_where_close()
            ->and_where('contacts.delete', '=', 0)
            ->order_by('contacts.id', 'desc')
            ->execute()
            ->current();

        return $existing_contact;
    }

    public static function get_timetable_data($params = array())
    {
        if (@$params['before']) {
            $before = $params['before'];
        } else {
            $before = null;
        }
        if (@$params['after']) {
            $after = $params['after'];
        } else {
            $after = date('Y-m-d 00:00:00');
        }

        $contact_ids = null;
        if (!isset($params['contact_ids'])) {
            $user = Auth::instance()->get_user();
            $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            if (count($contacts) > 0) {
                $contact_ids = array($contacts[0]['id']);
            }
        } else {
            $contact_ids = $params['contact_ids'];
        }

        if (@$params['schedule_id']) {
            $schedule_id = $params['schedule_id'];
        } else {
            $schedule_id = null;
        }

        if (@$params['booking_id']) {
            $booking_id = $params['booking_id'];
        } else {
            $booking_id = null;
        }

        if (@$params['weekdays']) {
            $weekdays = $params['weekdays'];
        } else {
            $weekdays = null;
        }

        if (isset($params['attending'])) {
            $attending = $params['attending'];
        } else {
            $attending = null;
        }

        if (@$params['timeslot_status']) {
            $timeslot_status = $params['timeslot_status'];
        } else {
            $timeslot_status = null;
        }

        if (@$params['trainer_id']) {
            $trainer_id = $params['trainer_id'];
        } else {
            $trainer_id = null;
        }

        $booking_items = Model_KES_Bookings::get_booking_items_family($contact_ids, null, $before, $after, $schedule_id, $booking_id, $weekdays, $attending, $timeslot_status, $trainer_id);

        // Get the seven most recent unique dates and group bookings by date
        $previous_date = '-';
        $dates = array();
        $cells = array();
        foreach ($booking_items as $i => $booking_item) {
            $date = date('Y-m-d', strtotime($booking_items[$i]['datetime_start']));
            if (!isset($cells[$date])) $cells[$date] = array();
            $cells[$date][] = $booking_items[$i];
            if ($date != $previous_date) {
                array_push($dates, $date);
            }
            $previous_date = $date;

        }
        $booking_items = array_values($booking_items);
//        $booking_items = array_slice($booking_items, 0, $i);
        array_unique($dates);
        $dates = array_reverse($dates);

        $weeks = array();
        $week_number = NULL;
        for ($date = $after ; $date <= $before ; $date = date('Y-m-d',strtotime($date.'+1 day'))) {
            if ($week_number != date('W',strtotime($date)) ) {
                $week_number  = date('W',strtotime($date));
                $weeks[$week_number] = array('dates' => array(), 'times' => array(), 'cells' => array(), 'booking_items' => array());
            }
            $weeks[$week_number]['dates'][]=$date;
        }
        foreach ($weeks as $key => $week) {
            $times = array();
            foreach ($week['dates'] as $k => $date) {
                if (!isset($weeks[$key]['cells'][$date])) {
                    $weeks[$key]['cells'][$date] = array();
                }
                $previous_time = ' ';
                foreach ($booking_items as $booking_item) {
                    if ($date == date('Y-m-d', strtotime($booking_item['datetime_start']))) {
                        $weeks[$key]['booking_items'][] = $booking_item;
                        $weeks[$key]['cells'][$date][] = $booking_item;
                    }
                    if (date('W',strtotime($week['dates'][0])) == date('W',strtotime($booking_item['datetime_start']))) {
                        $time = date('H:i', strtotime($booking_item['datetime_start']));
                        if ($time != $previous_time) {
                            $previous_time = $time;
                            array_push($times, $time);
                        }
                    }
                }
            }
            $times = array_unique($times);
            sort($times);
            $weeks[$key]['times']=$times;
        }

        $previous_time = ' ';
        $times = array();
        $calendar_events = array();

        // Get all unique start times
        // and data for the calendar
        foreach ($booking_items as $key => $booking_item) {
            $time = date('H:i', strtotime($booking_item['datetime_start']));
            if ($time != $previous_time) {
                $previous_time = $time;
                array_push($times, $time);
            }

            $calendar_events[$key] = $booking_item;
            $calendar_events[$key]['title']  = $booking_item['schedule'];
            $calendar_events[$key]['start']  = $booking_item['datetime_start'];
            $calendar_events[$key]['end']    = $booking_item['datetime_end'];
            $calendar_events[$key]['booked'] = true;
        }

        $times = array_unique($times);
        sort($times);
        return $calendar_events;
    }

    public static function save_timetable_bulk_note($booking_item_ids, $note, $attending, $update = false)
    {
        try {
            $booking_item_ids = is_array($booking_item_ids) ? $booking_item_ids : array($booking_item_ids);

            Database::instance()->begin();
            $table_id = Model_EducateNotes::get_table_link_id_from_name('plugin_ib_educate_booking_items');
            $result = array();

            foreach ($booking_item_ids as $booking_item_id) {
                $exists = DB::select('*')
                    ->from('plugin_contacts3_notes')
                    ->where('link_id', '=', $booking_item_id)
                    ->and_where('table_link_id', '=', $table_id)
                    ->and_where('deleted', '=', 0)
                    ->execute()
                    ->current();
                if ($exists) {
                    DB::update('plugin_contacts3_notes')
                        ->set(array('note' => $exists['note'].'<br />'.$note)) // Append new note onto the current one
                        ->where('link_id', '=', $booking_item_id)
                        ->and_where('table_link_id', '=', $table_id)
                        ->and_where('deleted', '=', 0)
                        ->and_where('note', '!=', $note) // If the current note is the same as the old note, don't duplicate
                        ->execute();
                    $result[] = $exists['id'];
                } else {
                    $notes = array(
                        'note' => $note,
                        'link_id' => $booking_item_id,
                        'table_link_id' => $table_id
                    );
                    $inserted = DB::insert('plugin_contacts3_notes')->values($notes)->execute();
                    $result[] = $inserted[0];
                }
            }
            DB::update(Model_KES_Bookings::BOOKING_ITEMS_TABLE)
                ->set(array('attending' => $attending))
                ->where('booking_item_id', 'in', $booking_item_ids)
                ->execute();
            Database::instance()->commit();
            return $result;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function delete_timetable_bulk_note($booking_item_ids)
    {
        try {
            Database::instance()->begin();
            $table_id = Model_EducateNotes::get_table_link_id_from_name('plugin_ib_educate_booking_items');

            $result = DB::update('plugin_contacts3_notes')
                ->set(array('deleted' => 1))
                ->where('link_id', 'in', $booking_item_ids)
                ->and_where('table_link_id', '=', $table_id)
                ->and_where('deleted', '=', 0)
                ->execute();
            Database::instance()->commit();
            return $result;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function insert_notification($notification)
    {
        if (!empty($notification['value']))
        {
            if (!isset($notification['group_id'])) {
                if ($this->notifications_group_id == null) {
                    $group_inserted = $this->_sql_insert_contact_details_group();
                    $this->notifications_group_id = $group_inserted[0];
                    if (is_numeric($this->id)) {
                        DB::update(self::CONTACTS_TABLE)
                            ->set(array('notifications_group_id' => $this->notifications_group_id))
                            ->where('id', '=', $this->id)
                            ->execute();
                    }
                }
                $notification['group_id'] = $this->notifications_group_id;
            }

            // Check if this notification has already been saved
            $existing_notification = DB::select()->from(self::CONTACT_NOTIFICATION_RELATION_TABLE)
                ->where('group_id',        '=', $notification['group_id'])
                ->where('notification_id', '=', $notification['notification_id'])
                ->where('value',           '=', $notification['value'])
                ->where('deleted',         '=', 0)
                ->execute()->current()
            ;

            // If already exists, get its ID
            if (!empty($existing_notification['id'])) {
                $notification['id'] = $existing_notification['id'];
            }
            // Otherwise, insert a new notification and get its ID
            else {
                $inserted = DB::insert(self::CONTACT_NOTIFICATION_RELATION_TABLE)
                    ->values($notification)
                    ->execute();
                $notification['id'] = $inserted[0];
            }

            if ($notification['notification_id'] == 2 && $this->linked_user_id > 0) {
                DB::update(Model_Users::MAIN_TABLE)
                    ->set(array('mobile' => $notification['value']))
                    ->where('id', '=', $this->linked_user_id)
                    ->execute();
            }

            $this->notifications[] = $notification;
        }

        return $notification;
    }

    public function update_notification($notification)
    {
        return DB::update(self::CONTACT_NOTIFICATION_RELATION_TABLE)
            ->set(array('value' => $notification['value']))
            ->where('id', '=', $notification['id'])
            ->execute();
    }

    public static function autocomplete_list($term, $type = null, $role = null, $subtype = null, $user_only = false)
    {
        $q = DB::select(
            array("contacts.id", "value"),
            DB::expr("CONCAT_WS(' ', IF(types.label = 'Department', pcontacts.first_name, ''), contacts.first_name, contacts.last_name) AS label")
        )
            ->from(array(self::CONTACTS_TABLE, 'contacts'))
            ->where('contacts.delete', '=', 0)
            ->and_where_open()
            ->or_where('contacts.first_name', 'like', '%' . $term . '%')
            ->or_where('contacts.last_name', 'like', '%' . $term . '%')
            ->and_where_close();
        $q->join(array(self::CONTACT_RELATIONS_TABLE, 'rel'), 'left')->on('rel.child_id', '=', 'contacts.id');
        $q->join(array(self::CONTACTS_TABLE, 'pcontacts'), 'left')->on('rel.parent_id', '=', 'pcontacts.id');
        $q->join(array(self::CONTACTS_TYPE_TABLE, 'types'), 'left')->on('contacts.type', '=', 'types.contact_type_id');
        $q->join(array(self::CONTACTS_SUBTYPE_TABLE, 'subtypes'), 'left')->on('contacts.subtype_id', '=', 'subtypes.id');
        if ($type) {
            $q->and_where('types.label', '=', $type);
        }
        if ($subtype) {
            $q->and_where('subtypes.subtype', '=', $subtype);
        }
        if ($user_only) {
            $q->join(array(Model_Users::MAIN_TABLE, 'users'), 'inner')->on('contacts.linked_user_id', '=', 'users.id');
        }
        if ($role) {
            $q->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_roles'), 'inner')
                    ->on('contacts.id', '=', 'has_roles.contact_id')
                ->join(array(self::ROLE_TABLE, 'roles'), 'inner')
                    ->on('has_roles.role_id', '=', 'roles.id')
                ->and_where('roles.stub', '=', $role);
        }
        $contacts = $q
            ->order_by('contacts.first_name')
            ->order_by('contacts.last_name')
            ->limit(20)
            ->execute()
            ->as_array();

        return $contacts;
    }

    public static function search_messaging($type, $filters = [])
    {
        $query_colums = array(
            DB::expr('SQL_CALC_FOUND_ROWS contact.id'),
            'contact.title',
            'contact.first_name',
            'contact.last_name', 'contact.is_primary', 'contact.family_id','contact.date_created', 'contact.date_modified',
            @$filters['interview_status'] ? array('courses.title', 'primary_contacts') : array(DB::expr('group_concat(DISTINCT concat(primary.title, " ", primary.first_name, " ", primary.last_name) SEPARATOR "\n")'), 'primary_contacts'),
            array('type.label','type'),
            array('subtype.subtype','subtype'),
            array('family.family_name', 'family'),
            'address.address1', 'address.address2', 'address.address3', 'address.town', 'address.county', 'address.country', 'address.postcode', 'address.coordinates',
            array(DB::expr(Model_Contacts3::country_code_columns('c_notif', ' ')), 'mobile'),
            array('e_notif.value', 'email'),
            array('school.id', 'school_id'), array('school.name', 'school'),
            array('year.id','year_id'), 'year.year',
            array(DB::expr('group_concat(DISTINCT(has_role.role_id) SEPARATOR ",")'), 'role_ids'),
            array(DB::expr('group_concat(DISTINCT(role.name) SEPARATOR ",")'), 'role'),
            array(DB::expr('group_concat(DISTINCT(role.stub) SEPARATOR ",")'), 'stub')
        );

        $search_columns = ['', DB::expr("CONCAT_WS(' ', contact.first_name, contact.last_name)"), "e_notif.value", 'type.label', ''];
        if (@$filters['interview_status']) {
            $query_colums[] = DB::expr("'booking_id' as template_field");
            $query_colums[] = DB::expr("'interview_details' as template_helper_function");
            $query_colums[] = DB::expr("bookings.booking_id");
        }
        $query = DB::select_array($query_colums)->from(array(self::CONTACTS_TABLE, 'contact'))
            ->join(array(self::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'LEFT')->on('contact.id', '=', 'has_role.contact_id')
            ->join(array(self::ROLE_TABLE,          'role'   ), 'LEFT')->on('has_role.role_id', '=', 'role.id')
            ->join(array(self::CONTACTS_TYPE_TABLE, 'type'   ), 'LEFT')->on('contact.type',      '=', 'type.contact_type_id')
            ->join(array(self::CONTACTS_SUBTYPE_TABLE,'subtype' ), 'LEFT')->on('contact.subtype_id','=','subtype.id')
            ->join(array(self::FAMILY_TABLE,        'family' ), 'LEFT')->on('contact.family_id', '=', 'family.family_id')
            ->join(array(self::ADDRESS_TABLE,       'address'), 'LEFT')->on('contact.residence', '=', 'address.address_id')
            ->join(array('plugin_courses_locations', 'school'), 'LEFT')->on('contact.school_id', '=', 'school.id')->on('school.publish',  '=', DB::expr(1))->on('school.delete',  '=', DB::expr(0))->on('school.location_type_id', '=', DB::expr(10))
            ->join(array('plugin_courses_years',    'year'   ), 'LEFT')->on('contact.year_id',   '=', 'year.id')->on('year.publish', '=', DB::expr(1))->on('year.delete', '=', DB::expr(0))
            ->join(array(self::CONTACTS_TABLE,      'primary'), 'LEFT')->on('family.family_id',  '=', 'primary.family_id')->on('primary.is_primary', '=', DB::expr(1))->on('primary.publish', '=', DB::expr(1))->on('primary.delete', '=', DB::expr(0))
            ->join([Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE, 'cht'], 'left')->on('cht.contact_id', '=', 'contact.id')
            ->group_by('contact.id')
            ->where('contact.delete', '=', 0)
            ->order_by('contact.first_name')
            ->order_by('contact.last_name');

        if ($type == 'sms') {
            $query->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'c_notif'), 'inner')->on('contact.notifications_group_id', '=', 'c_notif.group_id')->on('c_notif.notification_id', '=', DB::expr(2));
            $query->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'e_notif'), 'left')->on('contact.notifications_group_id', '=', 'e_notif.group_id')->on('e_notif.notification_id', '=', DB::expr(1));
        }

        if ($type == 'email') {
            $query->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'e_notif'), 'inner')->on('contact.notifications_group_id', '=', 'e_notif.group_id')->on('e_notif.notification_id', '=', DB::expr(1));
            $query->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'c_notif'), 'left')->on('contact.notifications_group_id', '=', 'c_notif.group_id')->on('c_notif.notification_id', '=', DB::expr(2));
        }

        if (@$filters['contact_type']) {
            $query->and_where('contact.type', 'in', $filters['contact_type']);
        }

        if (@$filters['schedule_ids'] || @$filters['category_ids'] || @$filters['location_ids'] || @$filters['interview_status'] || @$filters['interview_course_id'] || !empty($filters['booking_status_ids'])) {
            $query
                ->join([Model_KES_Bookings::DELEGATES_TABLE, 'delegate'], 'LEFT')
                    ->on('delegate.contact_id', '=', 'contact.id')
                ->join([Model_KES_Bookings::BOOKING_TABLE, 'bookings'], 'LEFT')
                    ->on('bookings.booking_id', '=', 'delegate.booking_id');

            if (@$filters['interview_status'] || @$filters['interview_course_id']) {
                $query
                    ->join(array(Model_KES_Bookings::BOOKING_APPLICATIONS, 'applications'), 'inner')
                        ->on('bookings.booking_id', '=', 'applications.booking_id')
                    ->join(array(Model_KES_Bookings::BOOKING_COURSES, 'has_courses'), 'inner')
                        ->on('applications.booking_id', '=', 'has_courses.booking_id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                        ->on('has_courses.course_id', '=', 'courses.id')
                    ->group_by('bookings.booking_id');
                if (@$filters['interview_course_id']) {
                    $query->and_where('has_courses.course_id', '=', $filters['interview_course_id']);
                }
                if (@$filters['interview_schedule_id']) {
                    $query->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'booking_items'), 'inner')
                        ->on('bookings.booking_id', '=', 'booking_items.booking_id');
                    $query->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                        ->on('booking_items.period_id', '=', 'timeslots.id');
                    $query->and_where('timeslots.schedule_id', (is_array($filters['interview_schedule_id']) ? 'in' : '='), $filters['interview_schedule_id']);
                    $query->and_where('booking_items.delete', '=', 0);
                    $query->and_where('booking_items.booking_status', 'in', array(2,5));
                }
                if (@$filters['interview_status']) {
                    $query->and_where('applications.interview_status', '=', $filters['interview_status']);
                }
                $query->and_where('has_courses.deleted', '=', 0);
                $query->and_where('bookings.delete', '=', 0);

                if (!empty($filters['booking_status_ids'])) {
                    $query->and_where_open();
                        $query->where('has_courses.booking_status', 'IN', $filters['booking_status_ids']);
                        $query->or_where('bookings.booking_status', 'IN', $filters['booking_status_ids']);
                        if (!in_array(3, $filters['booking_status_ids'])) {
                            $query->and_where('delegates.cancelled' , '=' , 0);
                        }
                    $query->and_where_close();
                } else {
                    $query->and_where('delegates.cancelled' , '=' , 0);
                    $query->and_where('has_courses.booking_status', 'in', array(2,5));
                    $query->and_where('bookings.booking_status', 'in', array(2,5));
                }

            } else {
                $query->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'booking_items'),
                    'inner')->on('bookings.booking_id', '=', 'booking_items.booking_id');
                $query->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'),
                    'inner')->on('booking_items.period_id', '=', 'timeslots.id');
                $query->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('timeslots.schedule_id',
                    '=', 'schedules.id');
                $query->join(array(Model_Locations::TABLE_LOCATIONS, 'rooms'), 'left')->on('schedules.location_id',
                    '=', 'rooms.id');
                $query->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')->on('rooms.parent_id', '=',
                    'locations.id');
                $query->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')->on('schedules.course_id', '=',
                    'courses.id');

                if (@$filters['schedule_ids']) {
                    $query->and_where('schedules.id', 'in', $filters['schedule_ids'])->and_where('booking_items.delete',
                        '=', 0);
                    //$query->and_where('timeslots.datetime_start', '>=', date::now());
                } else {
                    if (@$filters['category_ids']) {
                        $query->and_where('courses.category_id', 'in', $filters['category_ids']);
                    }

                    if (@$filters['location_ids']) {
                        $query->and_where('locations.id', 'in', $filters['location_ids']);
                    }
                }

                if (!empty($filters['booking_status_ids'])) {
                    $query->where('bookings.booking_status', 'IN', $filters['booking_status_ids']);
                    if (!in_array(3, $filters['booking_status_ids'])) {
                        $query->and_where('delegates.cancelled' , '=' , 0);
                    }
                } else {
                    $query->where('bookings.booking_status', 'IN', array(2,5));
                    $query->and_where('delegate.cancelled', '=', 0);
                }
            }
        }

        if (!empty($filters['contact_tag_ids'])) {
            $query->where('cht.tag_id', 'IN', $filters['contact_tag_ids']);
        }

        if (!empty($filters['notification_preferences'])) {
            $query->join(array(Model_Contacts3::CONTACT_PREFERENCES_RELATION_TABLE, 'contact_preferences'), 'left')
                ->on('contact.id', '=', 'contact_preferences.contact_id')
                ->where('contact_preferences.preference_id', 'in', $filters['notification_preferences']);
        }

        // Global search
        if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
        {
            $query->and_where_open();
            for ($i = 0; $i < count($search_columns); $i++)
            {
                if ((isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $search_columns[$i] != '') ||
                    (isset($filters['global_search']) AND $search_columns[$i] != ''))
                {
                    $filters['sSearch'] = preg_replace('/\s+/', '%', $filters['sSearch']);
                    $query->or_where($search_columns[$i],'like','%'.$filters['sSearch'].'%');
                }
            }
            $query->and_where_close();
        }

        if (@$filters['limit']) {
            $query->limit($filters['limit']);
            if (@$filters['offset']) {
                $query->offset($filters['offset']);
            }
        } else if (@$filters['iDisplayLength']) {
            $query->limit($filters['iDisplayLength']);
            if (@$filters['iDisplayStart']) {
                $query->offset($filters['iDisplayStart']);
            }
        } else if($filters['limit_limited']){
            $query->limit(10);
        }

        $contacts = $query->execute()->as_array();
        DB::query(null, "set @found_rows=found_rows()")->execute();

        foreach ($contacts as $i => $contact) {
            $contacts[$i]['preferences'] = DB::select('preferences.stub', 'has_preferences.value')
                ->from(array(Model_Preferences::PREFERENCES_TABLE, 'preferences'))
                ->join(array(Model_Preferences::CONTACT_PREFERENCES_RELATION_TABLE, 'has_preferences'), 'left')
                ->on('preferences.id', '=', 'has_preferences.preference_id')
                ->on('has_preferences.contact_id', '=', DB::expr($contact['id']))
                ->on('has_preferences.deleted', '=', DB::expr(0))
                ->where('preferences.group', '=', 'contact')
                ->execute()
                ->as_array();
            $contacts[$i]['primary_contact'] = DB::select('primary_contacts.*')
                ->from(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'))
                    ->join(array(Model_Family::FAMILY_TABLE, 'families'), 'inner')
                        ->on('contacts.family_id', '=', 'families.family_id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'primary_contacts'), 'inner')
                        ->on('families.primary_contact_id', '=', 'primary_contacts.id')
                ->where('contacts.id', '=', $contact['id'])
                ->and_where('primary_contacts.delete', '=', 0)
                ->execute()
                ->current();
            $contacts[$i]['rtype'] = 'CMS_CONTACT3';
        }
        return $contacts;
    }
    
    public static function sync_user_to_contact($user_id, $postData)
    {
        $contact = Model_Contacts3::get_contact_ids_by_user($user_id);
        if ($contact) {
            $contact = current($contact);
            $contact3 = new Model_Contacts3($contact['id']);
            $contact3->set_first_name($postData['name']);
            $contact3->set_last_name($postData['surname']);
            if (@$postData['phone'] != '') {
                $contact3->load(
                    array(
                        'contactdetail_id' => array(''),
                        'contactdetail_type_id' => array(2),
                        'contactdetail_value' => array($postData['phone'])
                    )
                );
            }
            $contact3->save();
        }
    }

    /**
     * Check if a contact has complete their profile
     *
     * @param mixed $contact_id - The ID number of the contact or the string "current" to check the contact linked to the logged-in user
     * @return bool             - True, if the profile has been complete or the contact does not exist. False, if the contact exists and the profile is incomplete
     *
     */
    public static function check_profile_completion($contact_id = 'current')
    {
        $complete = true;

        if ($contact_id === 'current') {
            $user       = Auth::instance()->get_user();
            $contacts   = self::get_contact_ids_by_user($user['id']);
            $contact_id = isset($contacts[0]['id']) ? $contacts[0]['id'] : null;
        }

        $contact = new Model_Contacts3($contact_id);
        if ($contact->get_id()) {
            $complete = (trim($contact->get_first_name()) && trim($contact->get_last_name()) && trim($contact->get_mobile()));
        }

        return $complete;
    }

    public static function save_settings($post)
    {
        try {
            Database::instance()->begin();

            DB::delete('plugin_contacts3_roles_has_preferences')->execute();
            if (isset($post['permission']))
            foreach ($post['permission'] as $permission) {
                DB::insert('plugin_contacts3_roles_has_preferences')
                    ->values(
                        array(
                            'role_id' => $permission['role_id'],
                            'group' => $permission['group'],
                            'preference' => $permission['preference'],
                            'allowed' => $permission['allowed']
                        )
                    )->execute();
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function load_settings()
    {
        $settings = DB::select('h.*', 'p.label')
            ->from(array('plugin_contacts3_roles_has_preferences', 'h'))
                ->join(array('plugin_contacts3_preferences', 'p'), 'left')->on('h.preference', '=', 'p.stub')
            ->order_by('role_id')
            ->order_by('group')
            ->execute()
            ->as_array();
        return $settings;
    }

    public static function invite_member($email, $invited_by_contact_id, $name = '', $invited_contact_id = null, $register = null)
    {
        if ($invited_contact_id == null) {
            $invited_contact = self::get_existing_contact_by_email_and_mobile($email, '');
            $invited_contact_id = @$invited_contact['id'];
        }

        $inserted = DB::insert(self::INVITATIONS_TABLE)
            ->values(
                array(
                    'invited_by_contact_id' => $invited_by_contact_id,
                    'invited_email' => $email,
                    'invited_contact_id' => $invited_contact_id,
                    'status' => 'Wait'
                )
            )
            ->execute();

        $msg_params = array();
        $msg_params['email'] = $email;
        $msg_params['name'] = $name;
        if ($invited_contact_id && $register !== true) {
            $msg_params['url_join'] = URL::site('/admin/login?invite_member=' . $inserted[0] . '&invite_hash=' . sha1($inserted[0] . 'Wait') . '&join=1&email=' . urlencode($email));
        } else {
            $msg_params['url_join'] = URL::site('/admin/login/register?invite_member=' . $inserted[0] . '&invite_hash=' . sha1($inserted[0] . 'Wait') . '&join=1&email=' . urlencode($email));
        }
        $msg_params['url_reject'] = URL::site('/frontend/contacts3/reject_invite?invite_member=' . $inserted[0] . '&invite_hash=' . sha1($inserted[0] . 'Wait') . '&reject=1');

        $mm = new Model_Messaging();
        $mm->send_template(
            'contact-invite-family-member',
            null,
            null,
            array(
                array(
                    'target_type' => 'EMAIL',
                    'target' => $email
                )
            ),
            $msg_params
        );

        return $inserted[0];
    }

    public static function invite_accept($invite_member, $invite_hash)
    {
        if (sha1($invite_member . 'Wait') == $invite_hash) {
            $invitation = DB::select('*')
                ->from(self::INVITATIONS_TABLE)
                ->where('id', '=', $invite_member)
                ->and_where('status', '=', 'Wait')
                ->execute()
                ->current();
            if ($invitation) {
                DB::update(self::INVITATIONS_TABLE)
                    ->set(array('status' => 'Accepted'))
                    ->where('id', '=', $invite_member)
                    ->execute();
                if ($invitation['invited_contact_id']) {
                    $invited_contact = new Model_Contacts3($invitation['invited_contact_id']);
                    $invited_by_contact = new Model_Contacts3($invitation['invited_by_contact_id']);
                    $old_family_id = $invited_contact->get_family_id();
                    $invited_contact->set_family_id($invited_by_contact->get_family_id());
                    $invited_contact->save();

                    $old_family_members = DB::select('*')
                        ->from(self::CONTACTS_TABLE)
                        ->where('family_id', '=', $old_family_id)
                        ->and_where('delete', '=', 0)
                        ->execute()
                        ->as_array();
                    if (count($old_family_members) == 0) {
                        DB::update(Model_Family::FAMILY_TABLE)
                            ->set(array('delete' => 1))
                            ->where('family_id', '=', $old_family_id)
                            ->execute();
                    }
                }
                return $invitation;
            }
        }
        return false;
    }

    public static function get_cards($contact_id, $card_id = null, $check_parent = false)
    {
        $select = DB::select('cards.*')
            ->from(array(self::HAS_CARDS_TABLE, 'cards'))
                ->join(array(self::PAYMENTGW_TABLE, 'gw'), 'inner')->on('cards.has_paymentgw_id', '=', 'gw.id');
        if ($check_parent == true) {
            $select->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('gw.contact_id', '=', 'contacts.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'parents'), 'left')
                    ->on('contacts.family_id', '=', 'parents.family_id')
                    ->on('parents.is_primary', '=', DB::expr(1))
                ->and_where_open()
                    ->or_where('gw.contact_id', '=', $contact_id)
                    ->or_where('gw.contact_id', '=', DB::expr('parents.id'))
                ->and_where_close();
        } else {
            $select->where('gw.contact_id', '=', $contact_id);
        }
        if ($card_id) {
            $select->and_where('cards.id', '=', $card_id);
        }
        $cards = $select->execute()->as_array();
        return $cards;
    }

    public static function invite_reject($invite_member, $invite_hash)
    {
        if (sha1($invite_member . 'Wait') == $invite_hash) {
            $invitation = DB::select('*')
                ->from(self::INVITATIONS_TABLE)
                ->where('id', '=', $invite_member)
                ->and_where('status', '=', 'Wait')
                ->execute()
                ->current();
            if ($invitation) {
                DB::update(self::INVITATIONS_TABLE)
                    ->set(array('status' => 'Rejected'))
                    ->where('id', '=', $invite_member)
                    ->execute();
                return true;
            }
        }
        return false;
    }

    public static function invite_check($invite_member, $invite_hash)
    {
        if (sha1($invite_member . 'Wait') == $invite_hash) {
            $invitation = DB::select('*')
                ->from(self::INVITATIONS_TABLE)
                ->where('id', '=', $invite_member)
                ->and_where('status', '=', 'Wait')
                ->execute()
                ->current();
            return $invitation;
        }
        return false;
    }

    public static function invite_check_contact_id($contact_id)
    {
        if (!$contact_id) {
            return null;
        }
            $invitation = DB::select('*')
                ->from(self::INVITATIONS_TABLE)
                ->where('invited_contact_id', '=', $contact_id)
                ->order_by('id', 'desc')
                ->limit(1)
                ->execute()
                ->current();
            return $invitation;
    }

    public static function create_for_user($user)
    {
        $family = new Model_Family();
        $family->set_family_name($user['surname'] ?: $user['email']);
        $family->save();
        $contact3 = new Model_Contacts3();
        $contact3->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
        $contact3->set_subtype_id(0);
        $contact3->set_first_name($user['name'] ?: ' ');
        $contact3->set_last_name($user['surname'] ?: ' ');
        $contact3->set_linked_user_id($user['id']);
        $contact3->set_permissions(array($user['id']));
        $contact3->set_family_id($family->get_id());
        $contact3->save();
        $family->set_primary_contact_id($contact3->get_id());
        $family->save();
        return $contact3->get_id();
    }

    public static function get_linked_contact_to_user($user_id)
    {
        $contact = DB::select('contacts.*')
            ->from(array(self::CONTACTS_TABLE, 'contacts'))
            ->where('linked_user_id', '=', $user_id)
            ->execute()
            ->current();
        return $contact;
    }

    public static function delete_user_data($user_id)
    {
        $contact = self::get_linked_contact_to_user($user_id);

        if ($contact) {
            DB::update(self::CONTACTS_TABLE)
                ->set(
                    array(
                        'first_name' => '',
                        'last_name' => '',
                        'pps_number' => '',
                    )
                )
                ->where('id', '=', $contact['id'])
                ->execute();

            DB::update(self::CONTACT_NOTIFICATION_RELATION_TABLE)
                ->set(array('value' => ''))
                ->where('contact_id', '=', $contact['id'])
                ->or_where('group_id', '=', $contact['notifications_group_id'])
                ->execute();
        }
    }

    public static function default_notifications($select)
    {
        $options = array('SMS', 'EMAIL', 'BOTH', 'NONE');
        $return = '';
        foreach ($options AS $option) {
            $selected = '';
            if ($option == $select) {
                $selected = ' selected="selected"';
            }
            $return .= '<option value="' . $option . '"' . $selected . '>' . $option . '</option>';
        }
        return $return;
    }


    public static function get_parent_related_contacts($contact_id)
    {
        $contacts = DB::select('*')
            ->from(self::CONTACT_RELATIONS_TABLE)
            ->where('child_id', '=', $contact_id)
            ->execute()
            ->as_array();
        $parent_ids = array();
        foreach ($contacts as $contact) {
            $parent_ids[] = $contact['parent_id'];
        }
        return $parent_ids;
    }

    public static function get_child_related_contacts($contact_id, $role = null)
    {
        $contactsq = DB::select('*')
            ->from(self::CONTACT_RELATIONS_TABLE)
            ->where('parent_id', '=', $contact_id);
        if ($role) {
            $contactsq->and_where('role', '=', $role);
        }
        $contacts = $contactsq->execute()
            ->as_array();
        $child_ids = array();
        foreach ($contacts as $contact) {
            $child_ids[] = $contact['child_id'];
        }
        return $child_ids;
    }

    public static function search($params = array())
    {
        $select = DB::select('contacts.*', array('emails.value', 'email'))
            ->from(array(self::CONTACTS_TABLE, 'contacts'))
                ->join(array(self::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')
                    ->on('contacts.notifications_group_id', '=', 'emails.group_id')
                    ->on('emails.deleted', '=', DB::expr(0))
            ->where('contacts.delete', '=', 0);

        if (@$params['first_name']) {
            $select->and_where('contacts.first_name', '=', $params['first_name']);
        }
        if (@$params['last_name']) {
            $select->and_where('contacts.last_name', '=', $params['last_name']);
        }
        if (@$params['email']) {
            $select->and_where('emails.value', '=', $params['email']);
        }
        if (@$params['family_id']) {
            $select->and_where('contacts.family_id', '=', $params['family_id']);
        }
        if (@$params['type']) {
            $select->and_where('contacts.type', '=', $params['type']);
        }
        if (@$params['subtype']) {
            $subtype = self::find_subtype($params['subtype']);
            $params['subtype_id'] = $subtype['id'];
        }
        if (@$params['subtype_id']) {
            $select->and_where('contacts.subtype_id', '=', $params['subtype_id']);
        }
        if(@$params['domain_name']) {
            $select->and_where('contacts.domain_name', DB::expr('is not'), DB::expr('NULL'))
                ->and_where('contacts.domain_name', '=', $params['domain_name']);
        }

        $select->group_by('contacts.id');
        $select->order_by('contacts.first_name')->order_by('contacts.last_name');

        $contacts = $select->execute()->as_array();
        return $contacts;
    }
    
    public static function get_job_functions()
    {
        return DB::select('*')
            ->from(self::CONTACT_JOB_FUNCTIONS_TABLE)
            ->execute()
            ->as_array();
    }

    public static function get_job_function($id)
    {
        return DB::select('*')
            ->from(self::CONTACT_JOB_FUNCTIONS_TABLE)
            ->where('id', '=', $id)
            ->execute()
            ->current();
    }

    public static function organisation_membership_update($user_id) {

        //on each login update membership status for organisation.
        $contact = Model_Contacts3::get_linked_contact_to_user($user_id);
        if (!empty($contact)) {
            $contact = new Model_Contacts3($contact['id']);
            $organisation = $contact->get_linked_organisation();
            if ($organisation->get_id() > 0) {
                if (Settings::instance()->get('organisation_api_control_membership')
                    && Settings::instance()->get('organisation_integration_api')
                    && Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                    $membership_status = false;
                    $cds = new Model_CDSAPI();
                    $cds_account = $cds->get_account($organisation->get_id());
                    if (!empty($cds_account)) {
                        $membership_status = @$cds_account['sp_membershipstatus'];
                    }
                    $organisation->update_membership_for_organisation($membership_status);
                }

            }
        }
    }
}