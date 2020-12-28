<?php
final class Model_Family extends Model implements Interface_Contacts3
{
    /**
     ** ----- CONSTANT VALUES -----
     **/

    CONST CONTACTS_TABLE                        = 'plugin_contacts3_contacts';
    CONST FAMILY_TABLE                          = 'plugin_contacts3_family';
    CONST CONTACT_FAMILY_RELATION_TABLE         = 'plugin_contacts3_contact_has_family';
    CONST ADDRESS_TABLE                         = 'plugin_contacts3_residences';
    CONST CONTACT_NOTIFICATION_RELATION_TABLE   = 'plugin_contacts3_contact_has_notifications';
    CONST NOTIFICATIONS_TABLE                   = 'plugin_contacts3_notifications';
    CONST CONTACT_PREFERENCES_RELATION_TABLE    = 'plugin_contacts3_contact_has_preferences';
    CONST PREFERENCES_TABLE                     = 'plugin_contacts3_preferences';
    CONST COUNTIES_TABLE                        = 'engine_counties';

    /**
     ** ----- PRIVATE MEMBER DATA -----
     **/

    private $family_id              = NULL;
    private $family_name            = '';
    private $primary_contact_id     = NULL;
    private $notes                  = '';
    private $residence              = NULL;
    private $notifications_group_id = NULL;
    private $publish                = 1;
    private $delete                 = 0;
    private $date_created           = '';
    private $date_modified          = '';
    private $created_by             = NULL;
    private $modified_by            = NULL;
    private $notifications          = array();

    /**
     ** ----- PUBLIC MEMBER DATA -----
     **/

    public $address = NULL;

    function __construct($id = NULL)
    {
        if(is_numeric($id))
        {
            $this->set_family_id($id);
        }

        $this->get(true);
    }

    public function load($data)
    {
		$data = Model_Contacts3::normalize_notification_data($data);
        foreach ($data AS $key => $value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = ($value == '') ? NULL : $value;
            }
        }
        $this->address = new Model_Residence($this->residence);

        return $this;
    }

    public function get($autoload = FALSE)
    {
        $data = $this->_sql_load_family();

        if($autoload)
        {
            $this->load($data);
        }

        return $data;
    }

    public function save()
    {
        $ok = $this->validate();
        if($ok)
        {
            Database::instance()->begin();
            try{
                $this->set_residence($this->address->save());
                $this->set_date_modified();
//                $this->save_contact_details();
                if(is_numeric($this->family_id))
                {
                    $this->_sql_update_family();
                }
                else
                {
                    $this->set_date_created();
                    $this->_sql_save_family();
                }
                Database::instance()->commit();
            }catch(Exception $e)
            {
                $ok = false;
                Database::instance()->rollback();
            }
        }

        return $ok;
    }

    public function save_contact_details()
    {
        if ($this->get_notifications_group_id() == '' AND ! empty($this->notifications))
        {
            $group = $this->_sql_insert_contact_details_group();
            $this->set_notifications_group_id($group[0]);
        }
        $notifications = $this->get_contact_details_instance();
        $existing_notifications = $new_notifications = array();
        foreach ($notifications as $notification)
        {
            ($notification['id'] == 'new' OR $notification['id'] == '') ? $new_notifications[] = $notification : $existing_notifications[] = $notification;
        }

        $this->_sql_insert_contact_details($new_notifications);
        $this->_sql_update_contact_details($existing_notifications);
    }

    public function get_instance()
    {
        return array(
            'family_id'              => $this->family_id,
            'family_name'            => $this->family_name,
            'primary_contact_id'     => $this->primary_contact_id,
            'notes'                  => $this->notes,
            'residence'              => $this->residence,
            'notifications_group_id' => $this->notifications_group_id,
            'publish'                => $this->publish,
            'delete'                 => $this->delete,
            'date_created'           => $this->date_created,
            'date_modified'          => $this->date_modified,
            'created_by'             => $this->created_by,
            'modified_by'            => $this->modified_by
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
                'value'           => $notification['value'],
                'date_created'    => $this->date_created,
                'date_modified'   => $this->date_modified,
                'created_by'      => $this->created_by,
                'modified_by'     => $this->modified_by
            );
        }
        return $return;
    }

    public function delete()
    {
        $this->set_delete(1);
        $this->set_publish(0);
        return $this->save();
    }

    public function validate()
    {
        return TRUE;
    }

    public function set_family_id($id)
    {
        $this->family_id = (is_numeric($id) AND $id > 0) ? (int) $id : NULL;
    }

    public function set_family_name($family_name)
    {
        $this->family_name = (is_string($family_name) AND trim(strlen($family_name)) > 0) ? $family_name : '';
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
    }

    public function set_date_modified()
    {
        $this->date_modified = date('Y-m-d H:i:s',time());
    }

    public function set_residence($residence_id)
    {
        $this->residence = (is_numeric($residence_id) AND $residence_id > 0) ? (int) $residence_id : NULL;
    }

    public function set_notifications_group_id($id)
    {
        $this->notifications_group_id = is_numeric($id) ? $id : NULL;
    }

    public function set_primary_contact_id($contact_id)
    {
        $this->primary_contact_id = is_numeric($contact_id) ? $contact_id : NULL;
    }

    public function get_id()
    {
        return $this->family_id;
    }

    public function get_family_count()
    {
        $count = DB::select(DB::expr('COUNT(*) AS `number`'))->from(self::CONTACTS_TABLE)->where('family_id','=',$this->family_id)->and_where('delete','=',0)->execute()->as_array();
        return $count[0]['number'];
    }

    public function get_family_name()
    {
        return $this->family_name;
    }

    public function get_notes()
    {
        return $this->notes;
    }

    public function get_residence()
    {
        return $this->residence;
    }

    public function get_notifications_group_id()
    {
        return $this->notifications_group_id;
    }

    public function get_primary_contact_id()
    {
        return $this->primary_contact_id;
    }

    public function get_members()
    {
        if ($this->family_id == null) {
            return array();
        }
        $return = array();

        $children_subquery = DB::select('has_role.*')
            ->from(array(Model_Contacts3::CONTACT_ROLE_RELATION_TABLE, 'has_role'))
            ->join(array(Model_Contacts3::ROLE_TABLE, 'role'))->on('has_role.role_id', '=', 'role.id')
            ->where('role.stub', '=', 'student');

        $members = DB::select('contact.*', array(DB::expr('IF(`child_role`.`role_id`, 1, 0)'), 'is_child'))
            ->from(array(self::CONTACTS_TABLE, 'contact'))
            ->join(array($children_subquery, 'child_role'), 'left')->on('child_role.contact_id', '=', 'contact.id')
            ->where('contact.family_id', '=', $this->family_id)
            ->where('contact.family_id', 'is not', null)
            ->where('contact.delete', '=', 0)
            ->order_by('contact.is_primary', 'desc') // primary contacts first
            ->order_by('is_child', 'asc') // non-children before children
            ->order_by('contact.first_name', 'asc')
            ->execute()->as_array();

        foreach ($members as $member) {
            $return[] = new Model_Contacts3($member['id']);
        }

        return $return;
    }

    public function get_member_ids()
    {
        $family_members = $this->get_members();
        $family_member_ids = [];
        foreach ($family_members as $family_member) {
            $family_member_ids[] = $family_member->get_id();
        }

        return $family_member_ids;
    }

    public function get_contact_notifications()
    {
		if($this->primary_contact_id){
			return DB::select('cn.id','cn.value','cn.notification_id',array('n.id', 'type_id'),array('n.name','type_text'),array('n.stub','type_stub'))
				->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'cn'))
				->join(array(self::NOTIFICATIONS_TABLE, 'n'), 'LEFT')->on('cn.notification_id', '=', 'n.id')
				->join(array(self::FAMILY_TABLE,'f'))->on('f.notifications_group_id','=','cn.group_id')
                ->join(array(self::CONTACTS_TABLE, 'c'), 'INNER')->on('f.family_id', '=', 'c.family_id')
                ->where('c.family_id', '=', $this->family_id)->and_where('n.deleted', '=', 0)->and_where('cn.deleted', '=', 0)
                ->group_by('cn.id')
				->execute()->as_array();
		} else {
			return DB::select('cn.id','cn.value','cn.notification_id',array('n.id', 'type_id'),array('n.name','type_text'),array('n.stub','type_stub'))
				->from(array(self::CONTACT_NOTIFICATION_RELATION_TABLE,'cn'))
				->join(array(self::NOTIFICATIONS_TABLE, 'n'), 'LEFT')->on('cn.notification_id', '=', 'n.id')
				->join(array(self::FAMILY_TABLE,'f'))->on('f.notifications_group_id','=','cn.group_id')
				->join(array(self::CONTACTS_TABLE, 'c'), 'INNER')->on('f.family_id', '=', 'c.family_id')
				->where('c.family_id', '=', $this->family_id)->and_where('n.deleted', '=', 0)->and_where('cn.deleted', '=', 0)
                ->group_by('cn.id')
				->execute()->as_array();
		}
    }

    public function get_primary_contact()
    {
        $primary = DB::select()->from('plugin_contacts3_contacts')
            ->where('is_primary', '=', 1)
            ->where('publish', '=', 1)
            ->where('delete', '=', 0)
            ->where('family_id','=',$this->family_id)
            ->execute()
            ->as_array();
        return $primary;
    }

    public function get_guardians()
    {
        $guardians = DB::select('c.*')
            ->from(array(Model_Contacts3::CONTACTS_TABLE, 'c'))
            ->join(array(Model_Contacts3::CONTACT_ROLE_RELATION_TABLE, 'r'), 'INNER')->on('c.id', '=', 'r.contact_id')
            ->where('r.role_id', '=', 1)
            ->and_where('c.publish', '=', 1)
            ->and_where('c.delete', '=', 0)
            ->and_where('c.family_id', '=', $this->family_id)
            ->execute()
            ->as_array();
        return $guardians;
    }

    public function get_nonchildren()
    {
        $nonchildren = DB::select(DB::expr('DISTINCT c.*'))
            ->from(array(Model_Contacts3::CONTACTS_TABLE, 'c'))
            ->join(array(Model_Contacts3::CONTACT_ROLE_RELATION_TABLE, 'r'), 'LEFT')->on('c.id', '=', 'r.contact_id')
            ->where('c.publish', '=', 1)
            ->and_where('c.delete', '=', 0)
            ->and_where('c.family_id', '=', $this->family_id)
            ->and_where_open()
                ->or_where('r.role_id', 'in', array(1, 3, 4, 5, 6)) //not children
                ->or_where('r.role_id', 'is', null) // billed organization; not guardian, mature etc.
            ->and_where_close()
            ->execute()
            ->as_array();
        return $nonchildren;
    }

    public static function get_all_families($where_clauses = array(), $order_by = 'family.date_modified', $dir = 'desc', $limit = null)
    {
        $query = DB::select('family.family_id','family.family_name','family.primary_contact_id','family.notes','residence.address1','residence.address2','residence.town',array('county.name','county'), array('contact.title','contact_title'),array('contact.first_name','contact_first_name'),array('contact.last_name', 'contact_last_name'))
            ->from(array(self::FAMILY_TABLE, 'family'))
            ->join(array(self::ADDRESS_TABLE, 'residence'),'LEFT')->on('family.residence', '=', 'residence.address_id')->on('residence.publish', '=', DB::expr(1))->on('residence.delete', '=', DB::expr(0))
            ->join(array(self::COUNTIES_TABLE, 'county'), 'LEFT')->on('residence.county', '=', 'county.id')->on('county.publish', '=', DB::expr(1))->on('county.deleted', '=', DB::expr(0))
            ->join(array(self::CONTACTS_TABLE, 'contact'),'LEFT')->on('family.primary_contact_id', '=', 'contact.id')->on('contact.publish', '=', DB::expr(1))->on('contact.delete', '=', DB::expr(0))
            ->where('family.delete','=',0)->and_where('family.publish','=',1);
        $query = self::where_clauses($query, $where_clauses);
		$query->order_by($order_by, $dir);
		if($limit){
			$query->limit($limit);
		}
        return $query->execute()->as_array();
    }

    public static function search_families($term = NULL)
    {
        return DB::select()->from(self::FAMILY_TABLE)->where('family_name','LIKE','%'.$term.'%')->execute()->as_array();
    }

	/*
	 * Get results for datatable
	 *
	 */
	public static function get_for_datatable($filters)
	{
		$scolumns = array(0 => 'family.family_id',
								'family.family_name',
								'family.primary_contact_id',
								'residence.address1',
								'county.name');

        $query = DB::select(DB::expr('SQL_CALC_FOUND_ROWS family.family_id'),
								'family.family_name',
								'family.primary_contact_id',
								'family.notes',
								'residence.address1',
								'residence.address2',
								array('county.name','county'), 
								array('contact.title','contact_title'),
								array('contact.first_name','contact_first_name'),
								array('contact.last_name', 'contact_last_name'))
            ->from(array(self::FAMILY_TABLE,'family'))
            ->join(array(self::ADDRESS_TABLE,'residence'),'LEFT')->on('family.residence', '=', 'residence.address_id')->on('residence.publish', '=', DB::expr(1))->on('residence.delete',  '=', DB::expr(0))
            ->join(array(self::COUNTIES_TABLE, 'county'),'LEFT')->on('residence.county', '=', 'county.id')->on('county.publish', '=', DB::expr(1))->on('county.deleted', '=', DB::expr(0))
            ->join(array(self::CONTACTS_TABLE, 'contact'),'LEFT')->on('family.primary_contact_id', '=', 'contact.id')->on('contact.publish', '=', DB::expr(1))->on('contact.delete', '=', DB::expr(0))
            ->where('family.delete','=',0)->and_where('family.publish', '=', 1);
		
		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$query->and_where_open();
			for ($i = 0; $i < count($scolumns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $scolumns[$i] != '')
				{
					$query->or_where($scolumns[$i],'like','%'.$filters['sSearch'].'%');
				}
			}
			$query->and_where_close();
		}
		// Limit. Only show the number of records for this paginated page
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$query->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$query->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($scolumns[$filters['iSortCol_'.$i]] != '')
				{
					$query->order_by($scolumns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}

        if (is_numeric(@$filters['check_permission_user_id'])) {
            $filter1 = DB::select('family_id')
                ->from(array(Model_Contacts3::TABLE_PERMISSION_LIMIT, 'permissions'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')->on('permissions.contact3_id', '=', 'contacts.id')
                ->where('user_id', '=', $filters['check_permission_user_id']);
            $query->and_where_open();
            $query->or_where('family.family_id', 'in', $filter1);
            $query->and_where_close();
        }

		$query->order_by('family.date_modified');
	
        $results = $query->execute()->as_array();

		$output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$output['iTotalRecords']        = count($results); // displayed results
        $output['aaData']               = array();
		
		foreach($results as $result)
		{
			$row   = array();
			$row[] = $result['family_id'];
			$row[] = '<a href="/admin/contacts3/ajax_display_family_details/'.$result['family_id'].'">'.$result['family_name'].'</a>';
			$row[] = $result['contact_title'] . ' ' . $result['contact_first_name'] . ' ' . $result['contact_last_name'];
			$row[] = $result['address1'] . ' ' . $result['address2'];
			$row[] = $result['county'];
			$output['aaData'][] = $row;
		}

        $output['sEcho'] = intval($filters['sEcho']);

        return json_encode($output);
	}

    /**
     * Remove the family association in contacts table when deleting a family
     * @return object
     */
    public static function remove_family_id($id)
    {
        return DB::update(self::CONTACTS_TABLE)->set(array('family_id'=>'Null'))->where('family_id','=',$id)->execute();
    }

    public static function get_notification_types()
    {
        return DB::select('id', 'name', 'stub')->from(self::NOTIFICATIONS_TABLE)
            ->where('publish', '=', 1)->and_where('deleted', '=', 0)
            ->execute()->as_array();
    }

    public static function instance($id = NULL)
    {
        return new self($id);
    }

    public static function get_family_account_supervisors($id)
    {
        return DB::select('c.id', 'c.title', 'c.first_name', 'c.last_name')->from(array(self::CONTACTS_TABLE, 'c'))
            ->join(array(self::CONTACT_PREFERENCES_RELATION_TABLE, 'cp'))->on('cp.contact_id', '=', 'c.id')
            ->join(array(self::PREFERENCES_TABLE, 'p'))->on('cp.preference_id', '=', 'p.id')
            ->where('c.family_id', '=', $id)
            ->and_where('p.stub', '=', 'accounts')
            ->execute()->as_array();
    }

    /**
     ** ----- PRIVATE FUNCTIONS -----
     **/

    private static function where_clauses($query, $where_clauses)
    {
        foreach ($where_clauses as $clause)
        {
            if     ($clause == 'open')                        $query = $query->where_open ();
            elseif ($clause == 'close')                       $query = $query->where_close();
            elseif (isset($clause[3]) AND $clause[3] == 'or') $query = $query->or_where ($clause[0], $clause[1], $clause[2]);
            else                                              $query = $query->and_where($clause[0], $clause[1], $clause[2]);
        }
        return $query;
    }

    private function _sql_load_family()
    {
        $q = DB::select()->from(self::FAMILY_TABLE)->where('family_id','=',$this->family_id)->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    private function _sql_update_family()
    {
        if ($this->_sql_load_family()) {
            DB::update(self::FAMILY_TABLE)->set($this->get_instance())->where('family_id', '=', $this->family_id)->execute();
        } else {
            DB::insert(self::FAMILY_TABLE, array_keys($this->get_instance()))->values($this->get_instance())->execute();
        }
    }

    private function _sql_save_family()
    {
        $q = DB::insert(self::FAMILY_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
        $this->set_family_id($q[0]);
    }

    /**
     * Save a contact's mobile number, email address etc. - adding new records
     */
    private function _sql_insert_contact_details($details)
    {
        if (isset($details[0]))
        {
            $query = DB::insert(self::CONTACT_NOTIFICATION_RELATION_TABLE, array_keys($details[0]));
            foreach ($details as $detail)
            {
                $query = $query->values($detail);
            }
            $query->execute();
        }
    }

    /**
     * Save a family's mobile number, email address etc. - adding a new record - updating existing records
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

    public static function fix_invalid_primary_contacts()
    {
        DB::query(null , "update plugin_contacts3_contacts c
		inner join plugin_contacts3_family f on c.id = f.primary_contact_id and c.family_id <> f.family_id
	set f.primary_contact_id = null")->execute();

        DB::query(null, "update plugin_contacts3_contacts c
		inner join plugin_contacts3_family f on c.family_id = f.family_id
	set f.primary_contact_id = c.id
	where f.primary_contact_id is null and c.is_primary = 1")->execute();

        DB::query(null, "update plugin_contacts3_contacts c
		inner join plugin_contacts3_family f on c.family_id = f.family_id
	set f.family_name = if (c.last_name = '', c.first_name, c.last_name)
	where f.family_name is null or f.family_name = ''")->execute();

        DB::query(null, 'update plugin_contacts3_contacts c
		inner join plugin_contacts3_family f on c.family_id = f.family_id and c.is_primary = 1 and f.primary_contact_id <> c.id
	set c.is_primary = 0')->execute();

        DB::query(null, 'update plugin_contacts3_contacts
	inner join plugin_contacts3_family on plugin_contacts3_contacts.family_id = plugin_contacts3_family.family_id
	inner join (select c.id, c.family_id
								from plugin_contacts3_contacts c
									inner join (select f.family_id
															from plugin_contacts3_family f
																left join plugin_contacts3_contacts c on f.primary_contact_id = c.id
															where f.primary_contact_id is null or c.id is null) fix_families on c.family_id = fix_families.family_id
									left join plugin_contacts3_contact_has_roles r on c.id = r.contact_id
								where r.role_id <> 2
								group by c.family_id) fix_list on plugin_contacts3_contacts.id = fix_list.id
	set	plugin_contacts3_contacts.is_primary = 1, plugin_contacts3_family.primary_contact_id = fix_list.id')->execute();

        DB::query(null, 'update plugin_contacts3_contacts
	inner join plugin_contacts3_family on plugin_contacts3_contacts.family_id = plugin_contacts3_family.family_id
	inner join (select c.id, c.family_id
								from plugin_contacts3_contacts c
									inner join (select f.family_id
															from plugin_contacts3_family f
																left join plugin_contacts3_contacts c on f.primary_contact_id = c.id
															where f.primary_contact_id is null or c.id is null) fix_families on c.family_id = fix_families.family_id
									left join plugin_contacts3_contact_has_roles r on c.id = r.contact_id
								group by c.family_id) fix_list on plugin_contacts3_contacts.id = fix_list.id
	set	plugin_contacts3_contacts.is_primary = 1, plugin_contacts3_family.primary_contact_id = fix_list.id')->execute();
    }

}
?>