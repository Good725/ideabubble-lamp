<?php
final class Model_Propman extends ORM
{
    const PROPERTIES_TABLE = 'plugin_propman_properties';
    const PROPERTIES_LINKED_TABLE = 'plugin_propman_properties_linked';
    const PROPERTIES_HAS_FACILITY_TABLE = 'plugin_propman_properties_has_facility';
    const PROPERTIES_HAS_SUITABILITY_TABLE = 'plugin_propman_properties_has_suitability';
    const PROPERTIES_HAS_MEDIA_TABLE = 'plugin_propman_properties_has_media';
    const PROPERTIES_HAS_RATECARDS_TABLE = 'plugin_propman_properties_has_ratecards';
    const PROPERTIES_CALENDAR_TABLE = 'plugin_propman_properties_calendar';
    const SUITABILITY_TYPES_TABLE = 'plugin_propman_suitability_types';
    const SUITABILITY_GROUPS_TABLE = 'plugin_propman_suitability_groups';
    const FACILITY_TYPES_TABLE = 'plugin_propman_facility_types';
    const FACILITY_GROUPS_TABLE = 'plugin_propman_facility_groups';
    const PROPERTY_TYPES_TABLE = 'plugin_propman_property_types';
    const BUILDING_TYPES_TABLE = 'plugin_propman_building_types';
    const PERIODS_TABLE = 'plugin_propman_periods';
    const RATECARDS_TABLE = 'plugin_propman_ratecards';
    const RATECARDS_CALENDAR_TABLE = 'plugin_propman_ratecards_calendar';
    const RATECARDS_DATERANGES_TABLE = 'plugin_propman_ratecards_date_ranges';
    const GROUPS_TABLE = 'plugin_propman_groups';
    const GROUPS_HAS_RATECARDS_TABLE = 'plugin_propman_groups_has_ratecards';
    const GROUPS_CALENDAR_TABLE = 'plugin_propman_groups_calendar';
    const BOOKINGS_TABLE = 'plugin_propman_bookings';
    const PAYMENTS_TABLE = 'plugin_propman_bookings_payments';
    const USERS_TABLE = 'engine_users';
    const IPNLOGS_TABLE = 'plugin_propman_ipn_logs';


    protected $_table_name = self::PROPERTIES_TABLE;
    protected $_publish_column = 'published';

    protected $_belongs_to = array(
        'building_type' => array('model' => 'Propman_BuildingType'),
        'property_type' => array('model' => 'Propman_PropertyType')
    );

    protected $_has_many = array(
        'photos'            => array(
            'model'           => 'Propman_Photo',
            'through'         => self::PROPERTIES_HAS_MEDIA_TABLE,
            'foreign_key'     => 'property_id',
            'far_key'         => 'media_id'
        ),
        'facility_types'    => array(
            'model'           => 'Propman_FacilityType',
            'through'         => self::PROPERTIES_HAS_FACILITY_TABLE,
            'foreign_key'     => 'property_id'
        ),
        'suitability_types' => array(
            'model'           => 'Propman_SuitabilityType',
            'through'         => self::PROPERTIES_HAS_SUITABILITY_TABLE,
            'foreign_key'     => 'property_id'
        ),
        'linked_properties' => array(
            'model'           => 'Propman',
            'through'         => self::PROPERTIES_LINKED_TABLE,
            'foreign_key'     => 'property_id_1',
            'far_key'         => 'property_id_2'
        ),
        'calendar'          => array(
            'model'           => 'Propman_PropertyCalendar',
            'through'         => self::PROPERTIES_CALENDAR_TABLE,
            'foreign_key'     => 'property_id'
        )
    );

    public function getMonth($year, $month)
    {
        $time = mktime(0, 0, 0, $month, 1, $year);

        $year = date('Y', $time);
        $month = date('m', $time);

        $first = date('Y-m-d', strtotime(date( 'Y-m-01 00:00:00', $time)));
        $last = date('Y-m-d', strtotime(date( 'Y-m-t 00:00:00', $time)));
        $lastn = date('t', $time);

        $days = array();
        $days['first'] = $first;
        $days['last'] = $last;
        $days['days'] = array();
        //$days['dayz'] = array();

        for ($day = 1 ; $day <= $lastn ; ++$day) {
            $days['days'][$day] = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
        }

        return $days;
    }

    public function getMonthList($year)
    {
        $months = array();
        for ($month = 1 ; $month <= 12 ; ++$month) {
            $months[$month] = self::getMonth($year, $month);
        }
        return $months;
    }

    public static function getWeeksInDateRange($start, $end)
    {
        $weeks = array();
        $time = strtotime($start);
        $end = strtotime($end);
        $first = true;
        do {
            $week = array();
            $week['start'] = date('Y-m-d', $time);
            if ($first) {
                $first = false;
                $w = 7 - date('w', $time);
                $week['end'] = date('Y-m-d', min($end,strtotime('+' . $w . 'day', $time)));
                $ntime = strtotime('+' . ($w + 1) . 'day', $time);
            } else {
                $week['end'] = date('Y-m-d', min($end,strtotime('+6day', $time)));
                $ntime = strtotime('+7day', $time);
            }
            $weeks[] = $week;
            $time = $ntime;
        } while($time < $end);
        return $weeks;
    }

    public static function getDaysInDateRange($starts, $ends)
    {
        $first = strtotime(date('Y-m-d', strtotime($starts)));

        $last = strtotime(date('Y-m-d', strtotime($ends)));

        $range = array();
        $range['first'] = $first;
        $range['last'] = $last;
        $range['days'] = array();

        for ($day = $first ; $day <= $last ; $day = strtotime('+1day', $day)) {
            $range['days'][$day] = date('Y-m-d', $day);
        }

        return $range;
    }

    public static function countries()
    {
        $countries = array();
        $query = DB::select('id', 'name')->from('engine_countries')
            ->where('published', '=', 1)->and_where('deleted', '=', 0)
            ->order_by('name')
            ->execute()->as_array();
        foreach($query as $q)
        {
            $countries[$q['id']] = $q['name'];
        }
        return $countries;
    }

    public static function counties($countryId)
    {
        $counties = array();
        if ($countryId === 'all') {
            $query = DB::select('id', 'name')->from('engine_counties')
                ->where('publish', '=', 1)->and_where('deleted', '=', 0)
                ->order_by('name')
                ->execute()->as_array();
        } else {
            $query = DB::select('id', 'name')->from('engine_counties')
                ->where('publish', '=', 1)->and_where('deleted', '=', 0)
                ->where('country_id','=',$countryId)
                ->order_by('name')
                ->execute()->as_array();
        }
        foreach($query as $q)
        {
            $counties[$q['id']] = $q['name'];
        }
        return $counties;
    }

    public static function getOwnerContacts()
    {
        $contacts = Model_Contacts::get_contact_all('first_name');
        $owners = array();
        foreach ($contacts as $contact) {
            if ($contact['mailing_list'] == 'Property Owner') {
                $owners[$contact['id']] = $contact['first_name'] . ' ' . $contact['last_name'];
            }
        }
        asort($owners);
        return $owners;
    }

    public static function suitabilityGroupsList()
    {
        $groups = DB::select('*')
            ->from(self::SUITABILITY_GROUPS_TABLE)
            ->where('deleted', '=', 0)
            ->order_by('sort', 'asc')
            ->execute()
            ->as_array();
        return $groups;
    }

    public static function suitabilityGroupSet($id, $name, $sort, $published, $types = array())
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['sort'] = $sort;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        if (!is_numeric($id)) {
            $data['created'] = date('Y-m-d H:i:s', $time);
            $data['created_by'] = $user['id'];
            $result = DB::insert(self::SUITABILITY_GROUPS_TABLE, array_keys($data))
                ->values($data)
                ->execute();
            if (isset($result[0])) {
                $id = $result[0];
            } else {
                $id = false;
            }
        } else {
            DB::update(self::SUITABILITY_GROUPS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }

        if (is_array($types)) {
            $updates = array();
            foreach ($types as $type) {
                if (is_numeric($type['id'])) {
                    $updates[] = $type['id'];
                }
            }
            if (count($updates)) {
                DB::update(self::SUITABILITY_TYPES_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('suitability_group_id', '=', $id)
                    ->and_where('id', 'not in', $updates)
                    ->execute();
            }
            if (count($types) > 0) {
                foreach ($types as $sort => $type) {
                    if ($type != '') {
                        self::suitabilityTypeSet(
                            is_numeric($type['id']) ? $type['id'] : null,
                            $type['type'],
                            $id,
                            $sort,
                            1
                        );
                    }
                }
            } else {
                DB::update(self::SUITABILITY_TYPES_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('suitability_group_id', '=', $id)
                    ->execute();
            }
        }

        return $id;
    }

    public static function suitabilityGroupPublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::SUITABILITY_GROUPS_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function suitabilityGroupGet($id, $types = false)
    {
        $suitabilityGroup = DB::select('*')
            ->from(self::SUITABILITY_GROUPS_TABLE)
            ->where('id', '=', (int)$id)
            ->execute()
            ->current();

        if ($suitabilityGroup && $types) {
            $suitabilityGroup['types'] = DB::select('*')
                ->from(self::SUITABILITY_TYPES_TABLE)
                ->where('suitability_group_id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->order_by('sort', 'asc')
                ->execute()
                ->as_array();
        }
        return $suitabilityGroup;
    }

    public static function suitabilityGroupInUse($id)
    {
        $cnt = DB::select(DB::expr('COUNT(*) AS CNT'))
            ->from(self::SUITABILITY_TYPES_TABLE)
            ->where('suitability_group_id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->get('CNT');
        return $cnt > 0;
    }

    public static function suitabilityGroupDelete($id)
    {
        $return = array();
        if (!self::suitabilityGroupInUse($id)) {
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::update(self::SUITABILITY_GROUPS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
            $return = array('status'=>'success','message'=>'Suitability Group:' . $id . ' was deleted');
        } else {
            $return = array('status'=>'in_use','message'=>'Suitability Group:' . $id . ' is in use');
        }
        return $return;
    }

    public static function suitabilityGroupDeleteUsed($id)
    {
        try
        {
            Database::instance()->begin();
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            $used = DB::select('hs.id')
                ->from(array(self::PROPERTIES_HAS_SUITABILITY_TABLE,'hs'))
                ->join(array(self::SUITABILITY_TYPES_TABLE,'st'))
                ->on('hs.suitability_type_id','=','st.id')
                ->where('st.suitability_group_id','=',$id)
                ->execute()
                ->as_array();

            DB::update(self::PROPERTIES_HAS_SUITABILITY_TABLE)
                ->set($data)
                ->where('id', 'IN', $used)
                ->execute();

            DB::update(self::SUITABILITY_TYPES_TABLE)
                ->set($data)
                ->where('suitability_group_id', '=', $id)
                ->execute();

            DB::update(self::SUITABILITY_GROUPS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function suitabilityTypesList($groupId = null)
    {
        $query = DB::select('stypes.*', array('sgroups.name', 'group'))
            ->from(array(self::SUITABILITY_TYPES_TABLE, 'stypes'))
            ->join(array(self::SUITABILITY_GROUPS_TABLE, 'sgroups'), 'inner')
            ->on('stypes.suitability_group_id', '=', 'sgroups.id')
            ->where('stypes.deleted', '=', 0);
        if ($groupId != null) {
            $query->and_where('stypes.suitability_group_id', '=', $groupId);
        }
        $types = $query
            ->order_by('sgroups.sort', 'asc')
            ->order_by('stypes.sort', 'asc')
            ->execute()
            ->as_array();
        return $types;
    }

    public static function suitabilityTypeSet($id, $name, $suitabilityGroupId, $sort, $published)
    {
        if ($id === null) {
            $existingId = DB::select('id')
                ->from(self::SUITABILITY_TYPES_TABLE)
                ->where('suitability_group_id', '=', $suitabilityGroupId)
                ->and_where('name', '=', $name)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->get('id');
            if ($existingId) {
                $id = $existingId;
            }
        }
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['suitability_group_id'] = $suitabilityGroupId;
        $data['sort'] = $sort;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        if (!is_numeric($id)) {
            $data['created'] = date('Y-m-d H:i:s', $time);
            $data['created_by'] = $user['id'];
            $result = DB::insert(self::SUITABILITY_TYPES_TABLE, array_keys($data))
                ->values($data)
                ->execute();
            if (isset($result[0])) {
                $id = $result[0];
            } else {
                $id = false;
            }
        } else {
            DB::update(self::SUITABILITY_TYPES_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }
        return $id;
    }

    public static function suitabilityTypePublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::SUITABILITY_TYPES_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function suitabilityTypeGet($id)
    {
        $facilityType = DB::select('*')
            ->from(self::SUITABILITY_TYPES_TABLE)
            ->where('id', '=', (int)$id)
            ->execute()
            ->current();
        return $facilityType;
    }

    public static function suitabilityTypeGetId($type)
    {
        $typeId = DB::select('id')
            ->from(self::SUITABILITY_TYPES_TABLE)
            ->where('name', '=', $type)
            ->execute()
            ->get('id');
        return $typeId;
    }

    public static function suitabilityTypeInUse($id)
    {
        $cnt = DB::select(DB::expr('COUNT(*) AS CNT'))
            ->from(self::PROPERTIES_HAS_SUITABILITY_TABLE)
            ->where('suitability_type_id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->get('CNT');
        return $cnt > 0;
    }

    public static function suitabilityTypeDelete($id)
    {
        if (!self::suitabilityTypeInUse($id)) {
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::update(self::SUITABILITY_TYPES_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        } else {
            throw new Exception('Suitability Type:' . $id . ' is in use');
        }
    }

    public static function facilityGroupsList()
    {
        $groups = DB::select('*')
            ->from(self::FACILITY_GROUPS_TABLE)
            ->where('deleted', '=', 0)
            ->order_by('sort', 'asc')
            ->execute()
            ->as_array();
        return $groups;
    }

    public static function facilityGroupSet($id, $name, $sort, $published, $types = array())
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['sort'] = $sort;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        if (!is_numeric($id)) {
            $data['created'] = date('Y-m-d H:i:s', $time);
            $data['created_by'] = $user['id'];
            $result = DB::insert(self::FACILITY_GROUPS_TABLE, array_keys($data))
                ->values($data)
                ->execute();
            if (isset($result[0])) {
                $id = $result[0];
            } else {
                $id = false;
            }
        } else {
            DB::update(self::FACILITY_GROUPS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }

        if (is_array($types)) {
            $updates = array();
            foreach ($types as $type) {
                if (is_numeric($type['id'])) {
                    $updates[] = $type['id'];
                }
            }
            if (count($updates) > 0) {
                DB::update(self::FACILITY_TYPES_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('facility_group_id', '=', $id)
                    ->and_where('id', 'not in', $updates)
                    ->execute();
            }

            if (count($types) > 0) {
                foreach ($types as $sort => $type) {
                    if ($type != '') {
                        self::facilityTypeSet(
                            is_numeric($type['id']) ? $type['id'] : null,
                            $type['type'],
                            $id,
                            $sort,
                            1
                        );
                    }
                }
            } else {
                DB::update(self::FACILITY_TYPES_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('facility_group_id', '=', $id)
                    ->execute();
            }
        }
        return $id;
    }

    public static function facilityGroupPublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::FACILITY_GROUPS_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function facilityGroupGet($id, $types = false)
    {
        $facilityGroup = DB::select('*')
            ->from(self::FACILITY_GROUPS_TABLE)
            ->where('id', '=', (int)$id)
            ->execute()
            ->current();
        if ($facilityGroup && $types) {
            $facilityGroup['types'] = DB::select('*')
                ->from(self::FACILITY_TYPES_TABLE)
                ->where('facility_group_id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->order_by('sort', 'asc')
                ->execute()
                ->as_array();
        }
        return $facilityGroup;
    }

    public static function facilityGroupInUse($id)
    {
        $cnt = DB::select(DB::expr('COUNT(*) AS CNT'))
            ->from(self::FACILITY_TYPES_TABLE)
            ->where('facility_group_id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->get('CNT');
        return $cnt > 0;
    }

    public static function facilityGroupDelete($id)
    {
        if ( ! self::facilityGroupInUse($id)) {
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::update(self::FACILITY_GROUPS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
            $return = array('status' => 'success');
        } else {
            $return = array('status' => 'in_use', 'message' => 'Facility Group:' . $id . ' is in use');
        }

        return $return;
    }

    public static function facilityGroupDeleteUsed($id)
    {
        try {
            Database::instance()->begin();
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            $used_facilities = DB::select('has.id')
                ->from(array(self::PROPERTIES_HAS_FACILITY_TABLE, 'has'))
                ->join(array(self::FACILITY_TYPES_TABLE, 'type'))
                ->on('has.facility_type_id', '=', 'type.id')
                ->where('type.facility_group_id', '=', $id)
                ->execute()
                ->as_array();
            $used = array();
            foreach ($used_facilities as $facility) {
                $used[] = $facility;
            }

            DB::update(self::PROPERTIES_HAS_FACILITY_TABLE)
                ->set($data)
                ->where('id', 'IN', $used)
                ->execute();

            DB::update(self::FACILITY_TYPES_TABLE)
                ->set($data)
                ->where('facility_group_id', '=', $id)
                ->execute();

            DB::update(self::FACILITY_GROUPS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function facilityTypesList($groupId = null)
    {
        $query = DB::select('ftypes.*', array('fgroups.name', 'group'))
            ->from(array(self::FACILITY_TYPES_TABLE, 'ftypes'))
                ->join(array(self::FACILITY_GROUPS_TABLE, 'fgroups'), 'inner')
                    ->on('ftypes.facility_group_id', '=', 'fgroups.id')
            ->where('ftypes.deleted', '=', 0);
        if ($groupId != null) {
            $query->and_where('ftypes.facility_group_id', '=', $groupId);
        }
        $types = $query
            ->order_by('fgroups.sort', 'asc')
            ->order_by('ftypes.sort', 'asc')
            ->execute()
            ->as_array();
        return $types;
    }

    public static function facilityTypeSet($id, $name, $facilityGroupId, $sort, $published)
    {
        if ($id === null) {
            $existingId = DB::select('id')
                ->from(self::FACILITY_TYPES_TABLE)
                ->where('facility_group_id', '=', $facilityGroupId)
                ->and_where('name', '=', $name)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->get('id');
            if ($existingId) {
                $id = $existingId;
            }
        }
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['facility_group_id'] = $facilityGroupId;
        $data['sort'] = $sort;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        if (!is_numeric($id)) {
            $data['created'] = date('Y-m-d H:i:s', $time);
            $data['created_by'] = $user['id'];
            $result = DB::insert(self::FACILITY_TYPES_TABLE, array_keys($data))
                ->values($data)
                ->execute();
            if (isset($result[0])) {
                $id = $result[0];
            } else {
                $id = false;
            }
        } else {
            DB::update(self::FACILITY_TYPES_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }
        return $id;
    }

    public static function facilityTypePublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::FACILITY_TYPES_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function facilityTypeGet($id)
    {
        $facilityType = DB::select('*')
            ->from(self::FACILITY_TYPES_TABLE)
            ->where('id', '=', (int)$id)
            ->execute()
            ->current();
        return $facilityType;
    }

    public static function facilityTypeGetId($type)
    {
        $typeId = DB::select('id')
            ->from(self::FACILITY_TYPES_TABLE)
            ->where('name', '=', $type)
            ->execute()
            ->get('id');
        return $typeId;
    }

    public static function facilityTypeInUse($id)
    {
        $cnt = DB::select(DB::expr('COUNT(*) AS CNT'))
            ->from(self::PROPERTIES_HAS_FACILITY_TABLE)
            ->where('facility_type_id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->get('CNT');
        return $cnt > 0;
    }

    public static function facilityTypeDelete($id)
    {
        if (!self::facilityTypeInUse($id)) {
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::update(self::FACILITY_TYPES_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
            $return = array('status'  => 'success',
                            'message' => 'The Facility Group: ' . $id . ' was deleted successfully.'
            );
        } else {
            $return = array('status'  => 'in_use',
                            'message' => 'Facility Type:' . $id . ' is in use');
        }
        return $return;
    }

    public static function buildingTypesList()
    {
        $types = DB::select('*')
            ->from(self::BUILDING_TYPES_TABLE)
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();
        return $types;
    }

    public static function buildingTypeSet($id, $name, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        if (!is_numeric($id)) {
            $data['created'] = date('Y-m-d H:i:s', $time);
            $data['created_by'] = $user['id'];
            $result = DB::insert(self::BUILDING_TYPES_TABLE, array_keys($data))
                ->values($data)
                ->execute();
            if (isset($result[0])) {
                $id = $result[0];
            } else {
                $id = false;
            }
        } else {
            DB::update(self::BUILDING_TYPES_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }
        return $id;
    }

    public static function buildingTypePublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::BUILDING_TYPES_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function buildingTypeGet($id)
    {
        $buildingType = DB::select('*')
            ->from(self::BUILDING_TYPES_TABLE)
            ->where('id', '=', (int)$id)
            ->execute()
            ->current();
        return $buildingType;
    }

    public static function buildingTypeInUse($id)
    {
        $cnt = DB::select(DB::expr('COUNT(*) AS CNT'))
            ->from(self::PROPERTIES_TABLE)
            ->where('building_type_id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->get('CNT');
        return $cnt > 0;
    }

    public static function buildingTypeDelete($id)
    {
        if (!self::buildingTypeInUse($id)) {
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::update(self::BUILDING_TYPES_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        } else {
            throw new Exception('Building Type:' . $id . ' is in use');
        }
    }

    public static function propertyTypesList()
    {
        $types = DB::select('*')
            ->from(self::PROPERTY_TYPES_TABLE)
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();
        return $types;
    }

    public static function propertyTypeSet($id, $name, $bedrooms, $sleep, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['bedrooms'] = $bedrooms;
        $data['sleep'] = $sleep;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        if (!is_numeric($id)) {
            $data['created'] = date('Y-m-d H:i:s', $time);
            $data['created_by'] = $user['id'];
            $result = DB::insert(self::PROPERTY_TYPES_TABLE, array_keys($data))
                ->values($data)
                ->execute();
            if (isset($result[0])) {
                $id = $result[0];
            } else {
                $id = false;
            }
        } else {
            DB::update(self::PROPERTY_TYPES_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }
        return $id;
    }

    public static function propertyTypePublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::PROPERTY_TYPES_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function propertyTypeGet($id)
    {
        $propertyType = DB::select('*')
            ->from(self::PROPERTY_TYPES_TABLE)
            ->where('id', '=', (int)$id)
            ->execute()
            ->current();
        return $propertyType;
    }

    public static function propertyTypeInUse($id)
    {
        $cnt = DB::select(DB::expr('COUNT(*) AS CNT'))
            ->from(self::PROPERTIES_TABLE)
            ->where('property_type_id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->get('CNT');
        return $cnt > 0;
    }

    public static function propertyTypeDelete($id)
    {
        if (!self::buildingTypeInUse($id)) {
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::update(self::PROPERTY_TYPES_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        } else {
            throw new Exception('Property Type:' . $id . ' is in use');
        }
    }

    public static function periodsList()
    {
        $periods = DB::select('*')
            ->from(self::PERIODS_TABLE)
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();
        return $periods;
    }

    public static function periodsCalendarList()
    {
        $periods = DB::select('*')
            ->from(self::PERIODS_TABLE)
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();
        foreach ($periods as $i => $period) {
            $periods[$i]['calendar'] = self::periodCalendarGet($period);
        }
        return $periods;
    }

    public static function periodsWeekList()
    {
        $periods = DB::select('*')
            ->from(self::PERIODS_TABLE)
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();
        foreach ($periods as $i => $period) {
            $periods[$i]['weeks'] = self::periodWeeksGet($period);
        }
        return $periods;
    }

    public static function periodValidate($period)
    {
        $errors = array();
        if ( ! isset($period['name']) OR trim($period['name'] == ''))
        {
            $errors[]  = __('You must enter a name.');
        }
        if ( ! isset($period['starts']) OR trim($period['starts'] == ''))
        {
            $errors[] = __('You must enter a start date.');
        }
        if ( ! isset($period['ends']) OR trim($period['ends'] == ''))
        {
            $errors[] = __('You must enter an end date.');
        }
        if (isset($period['starts']) AND isset($period['ends']) AND strtotime($period['starts']) > strtotime($period['ends']))
        {
            $errors[] = __('The start date must be before the end date.');
        }
        return $errors;
    }

    public static function periodSet($id, $name, $starts, $ends, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['starts'] = date::dmy_to_ymd($starts);
        $data['ends'] = date::dmy_to_ymd($ends);
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        if (!is_numeric($id)) {
            $data['created'] = date('Y-m-d H:i:s', $time);
            $data['created_by'] = $user['id'];
            $result = DB::insert(self::PERIODS_TABLE, array_keys($data))
                ->values($data)
                ->execute();
            if (isset($result[0])) {
                $id = $result[0];
            } else {
                $id = false;
            }
        } else {
            DB::update(self::PERIODS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }
        return $id;
    }

    public static function periodPublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::PERIODS_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function periodGet($id)
    {
        $period = DB::select('*')
            ->from(self::PERIODS_TABLE)
            ->where('id', '=', (int)$id)
            ->execute()
            ->current();
        return $period;
    }

    public static function periodCalendarGet($period)
    {
        if (is_numeric($period)) {
            $period = DB::select('*')
                ->from(self::PERIODS_TABLE)
                ->where('id', '=', (int)$period)
                ->execute()
                ->current();
        }
        $period['months'] = array();
        $end = strtotime($period['ends']);
        $time = strtotime(date('Y-m-01', strtotime($period['starts'])));
        $first = true;
        do {
            $month = array();
            $month['year'] = date('Y', $time);
            $month['month'] = date('n', $time);
            if ($first) {
                $month['start'] = date('j', strtotime($period['starts']));
                $first = false;
            } else {
                $month['start'] = 1;
            }
            $ntime = strtotime(date('Y-m-01', strtotime("+1month", $time)));
            if ($ntime >= $end) {
                $month['end'] = date('j', $end);
            } else {
                $month['end'] = date('t', $time);
            }
            $period['months'][] = $month;
            $time = $ntime;
        } while ($time < $end);

        return $period;
    }

    public static function periodWeeksGet($period)
    {
        if (is_numeric($period)) {
            $period = DB::select('*')
                ->from(self::PERIODS_TABLE)
                ->where('id', '=', (int)$period)
                ->execute()
                ->current();
        }
        $period['weeks'] = self::getWeeksInDateRange($period['starts'], $period['ends']);
        return $period;
    }

    public static function periodInUse($id)
    {
        return false;
    }

    public static function periodDelete($id)
    {
        if (!self::periodInUse($id)) {
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::update(self::PERIODS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        } else {
            throw new Exception('Period:' . $id . ' is in use');
        }
    }

    public static function ratecardsList($propertyTypeId = null)
    {
        $query = DB::select('rates.*', 'periods.starts', 'periods.ends', array('users.name', 'update_username'),'users.surname', array(DB::expr('GROUP_CONCAT(DISTINCT(groups.name))'), 'g_name'), array('prop_type.name', 'p_name'))
            ->from(array(self::RATECARDS_TABLE, 'rates'))
            ->join(array(self::PERIODS_TABLE, 'periods'), 'inner')->on('rates.period_id', '=', 'periods.id')
            ->join(array(self::USERS_TABLE, 'users'), 'left')->on('rates.updated_by', '=', 'users.id')
            ->join(array(self::GROUPS_HAS_RATECARDS_TABLE, 'has_group'), 'left')->on('rates.id', '=', 'has_group.ratecard_id')
            ->join(array(self::GROUPS_TABLE, 'groups'), 'left')->on('has_group.group_id', '=', 'groups.id')
			->join(array(self::PROPERTY_TYPES_TABLE, 'prop_type'), 'left')->on('rates.property_type_id', '=', 'prop_type.id')
            ->where('rates.deleted', '=', 0);
        if ($propertyTypeId != null) {
            $query->and_where('property_type_id', '=', $propertyTypeId);
        }
        $ratecards = $query->group_by('rates.id')->order_by('periods.starts', 'desc')
            ->execute()
            ->as_array();
        return $ratecards;
    }

    public static function ratecardSet($id,
        $name,
        $propertyTypeId,
        $periodId,
        $starts,
        $ends,
        $weeklyPrice,
        $shortStayPrice,
        $additionalNightsPrice,
        $minStay,
        $pricing,
        $discount,
        $arrival,
        $published,
        $dateRanges,
        $linkedGroups = null)
    {
        $discountType = 'Fixed';
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['property_type_id'] = $propertyTypeId;
        $data['period_id'] = $periodId;
        $data['starts'] = date::dmy_to_ymd($starts);
        $data['ends'] = date::dmy_to_ymd($ends);
        $data['weekly_price'] = $weeklyPrice;
        $data['short_stay_price'] = $shortStayPrice;
        $data['additional_nights_price'] = $additionalNightsPrice;
        $data['min_stay'] = $minStay;
        $data['pricing'] = $pricing;
        $data['discount_type'] = $discountType;
        $data['discount'] = $discount;
        $data['arrival'] = $arrival;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        try {
            Database::instance()->begin();
            if (!is_numeric($id)) {
                $data['created'] = date('Y-m-d H:i:s', $time);
                $data['created_by'] = $user['id'];
                $result = DB::insert(self::RATECARDS_TABLE, array_keys($data))
                    ->values($data)
                    ->execute();
                if (isset($result[0])) {
                    $id = $result[0];
                } else {
                    $id = false;
                }
            } else {
                DB::update(self::RATECARDS_TABLE)
                    ->set($data)
                    ->where('id', '=', $id)
                    ->execute();
            }

            DB::delete(self::RATECARDS_CALENDAR_TABLE)
                ->where('ratecard_id', '=', $id)
                ->execute();
            DB::delete(self::RATECARDS_DATERANGES_TABLE)
                ->where('ratecard_id', '=', $id)
                ->execute();
            foreach ($dateRanges as $dateRange) {
                $dateRange['starts'] = date::dmy_to_ymd($dateRange['starts']);
                $dateRange['ends'] = date::dmy_to_ymd($dateRange['ends']);
                $rangeId = DB::insert(
                    self::RATECARDS_DATERANGES_TABLE,
                    array(
                        'ratecard_id',
                        'starts',
                        'ends',
                        'weekly_price',
                        'short_stay_price',
                        'additional_nights_price',
                        'min_stay',
                        'pricing',
                        'discount',
                        'discount_type',
                        'arrival',
                        'is_deal'
                    )
                )->values(
                    array(
                        $id,
                        $dateRange['starts'],
                        $dateRange['ends'],
                        $dateRange['weekly_price'],
                        $dateRange['short_stay_price'],
                        $dateRange['additional_nights_price'],
                        $dateRange['min_stay'],
                        $dateRange['pricing'],
                        $dateRange['discount'],
                        $discountType,
                        $dateRange['arrival'],
                        @$dateRange['is_deal'] ? 1 : 0,
                    )
                )->execute();
                $rangeId = $rangeId[0];

                $days = self::getDaysInDateRange($dateRange['starts'], $dateRange['ends']);
                array_pop($days['days']); // last one is check out, not needed
                // RATECARDS_CALENDAR_TABLE is just to simplify some calculations
                foreach ($days['days'] as $day) {
                    DB::insert(
                        self::RATECARDS_CALENDAR_TABLE,
                        array(
                            'ratecard_id',
                            'range_id',
                            'date'
                        )
                    )->values(
                        array(
                            $id,
                            $rangeId,
                            $day
                        )
                    )->execute();
                }
            }

            if ($linkedGroups !== null) {
                if (count($linkedGroups) == 0) {
                    DB::delete(self::GROUPS_HAS_RATECARDS_TABLE)
                        ->where('ratecard_id', '=', $id)
                        ->execute();
                } else {
                    DB::delete(self::GROUPS_HAS_RATECARDS_TABLE)
                        ->where('ratecard_id', '=', $id)
                        ->and_where('group_id', 'not in', $linkedGroups)
                        ->execute();

                    foreach ($linkedGroups as $groupId) {
                        self::ratecardLinkGroup($id, $groupId);
                    }
                }
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
        return $id;
    }

    public static function ratecardPublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::RATECARDS_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function ratecardGet($id)
    {
        $ratecard = DB::select('rates.*', 'periods.starts', 'periods.ends')
            ->from(array(self::RATECARDS_TABLE, 'rates'))
            ->join(array(self::PERIODS_TABLE, 'periods'), 'inner')->on('rates.period_id', '=', 'periods.id')
            ->where('rates.id', '=', $id)
            ->execute()
            ->current();
        if ($ratecard) {
            $ratecard['dateRanges'] = DB::select('*')
                ->from(self::RATECARDS_DATERANGES_TABLE)
                ->where('ratecard_id', '=', $id)
                ->execute()
                ->as_array();
            foreach ($ratecard['dateRanges'] as $i => $dateRange) {
                $ratecard['dateRanges'][$i]['starts'] = date::ymd_to_dmy($dateRange['starts']);
                $ratecard['dateRanges'][$i]['ends'] = date::ymd_to_dmy($dateRange['ends']);
            }
            $ratecard['calendar'] = DB::select('*')
                ->from(self::RATECARDS_CALENDAR_TABLE)
                ->where('ratecard_id', '=', $id)
                ->order_by('date', 'asc')
                ->execute()
                ->as_array();
            $ratecard['groups'] = DB::select('groups.*')
                ->from(array(self::GROUPS_HAS_RATECARDS_TABLE, 'linked'))
                    ->join(array(self::GROUPS_TABLE, 'groups'), 'inner')
                        ->on('linked.group_id', '=', 'groups.id')
                ->where('ratecard_id', '=', $id)
                ->execute()
                ->as_array();
        }
        return $ratecard;
    }

    public static function ratecardsTestDateConflict($ratecard, $ratecardsToTest)
    {
        $conflictingRatecards = array();
        /*$start = strtotime($ratecard['starts']);
        $end = strtotime($ratecard['ends']);
        foreach ($ratecardsToTest as $ratecardToTest) {
            $startTest = strtotime($ratecardToTest['starts']);
            $endTest = strtotime($ratecardToTest['ends']);
            if (($start >= $startTest && $start <= $endTest) || ($end >= $startTest && $end <= $endTest)) {
                $conflictingRatecards[] = $ratecardToTest;
            }
        }
        */
        return $conflictingRatecards;
    }

    public static function ratecardLinkProperty($ratecardId, $propertyId)
    {
        $ratecard = self::ratecardGet($ratecardId);
        $linkedRatecards = DB::select('ratecards.*')
            ->from(array(self::PROPERTIES_HAS_RATECARDS_TABLE, 'linked'))
                ->join(array(self::RATECARDS_TABLE, 'ratecards'), 'inner')
                    ->on('linked.ratecard_id', '=', 'ratecards.id')
                ->join(array(self::PERIODS_TABLE, 'periods'), 'inner')
                    ->on('ratecards.period_id', '=', 'periods.id')
            ->where('linked.property_id', '=', $propertyId)
            ->order_by('periods.starts', 'asc')
            ->execute()
            ->as_array();

        $conflicts = self::ratecardsTestDateConflict($ratecard, $linkedRatecards);

        if (count($conflicts) == 0) {
            $result = DB::insert(self::PROPERTIES_HAS_RATECARDS_TABLE, array('property_id', 'ratecard_id'))
                ->values(array($propertyId, $ratecardId))
                ->execute();
            return $result[0];
        } else {
            return false;
        }
    }

    public static function ratecardUnlinkProperty($ratecardId, $propertyId)
    {
        DB::delete(self::PROPERTIES_HAS_RATECARDS_TABLE)
            ->where('property_id', '=', $propertyId)
            ->and_where('ratecard_id', '=', $ratecardId)
            ->execute();
    }

    public static function ratecardLinkGroup($ratecardId, $groupId)
    {
        $ratecard = self::ratecardGet($ratecardId);
        $linkedRatecards = DB::select('linked.*')
            ->from(array(self::GROUPS_HAS_RATECARDS_TABLE, 'linked'))
            ->where('linked.group_id', '=', $groupId)
            ->and_where('linked.ratecard_id', '=', $ratecardId)
            ->execute()
            ->as_array();

        if (count($linkedRatecards) == 0) {
            $result = DB::insert(self::GROUPS_HAS_RATECARDS_TABLE, array('group_id', 'ratecard_id'))
                ->values(array($groupId, $ratecardId))
                ->execute();
            return $result[0];
        } else {
            return false;
        }
    }

    public static function ratecardUnlinkGroup($ratecardId, $groupId)
    {
        DB::delete(self::GROUPS_HAS_RATECARDS_TABLE)
            ->where('group_id', '=', $groupId)
            ->and_where('ratecard_id', '=', $ratecardId)
            ->execute();
    }

    public static function ratecardInUse($id)
    {
        $cnt1 = DB::select(DB::expr('COUNT(*) AS CNT'))
            ->from(self::PROPERTIES_HAS_RATECARDS_TABLE)
            ->where('ratecard_id', '=', $id)
            ->execute()
            ->get('CNT');
        $cnt2 = DB::select(DB::expr('COUNT(*) AS CNT'))
            ->from(self::GROUPS_HAS_RATECARDS_TABLE)
            ->where('ratecard_id', '=', $id)
            ->execute()
            ->get('CNT');
        $cnt = $cnt1 + $cnt2;
        return $cnt > 0;
    }

    public static function ratecardDelete($id)
    {
        if (!self::ratecardInUse($id)) {
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::update(self::RATECARDS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();

            $return = array('status'  => 'success',
                            'message' => 'The Rate Card: ' . $id . ' was deleted successfully.'
            );
        } else {
            $return = array('status' => 'in_use', 'message' => 'Rate Card:' . $id . ' is in use');
        }

        return $return;
    }

    public static function ratecardDeleteUsed($id)
    {
        try
        {
            Database::instance()->begin();
            $user = Auth::instance()->get_user();
            $data = array();
            $data['deleted'] = 1;

            $time = time();
            $data['updated'] = date('Y-m-d H:i:s', $time);
            $data['updated_by'] = $user['id'];

            DB::delete(self::PROPERTIES_HAS_RATECARDS_TABLE)
                ->where('ratecard_id','=',$id)
                ->execute();

            DB::update(self::RATECARDS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function groupsList()
    {
        DB::query(null, 'CREATE TEMPORARY TABLE tmp_has_properties (group_id INT PRIMARY KEY, has_properties INT)')
            ->execute();
        DB::query(null, 'INSERT INTO tmp_has_properties (group_id, has_properties)
          (SELECT group_id, count(*) FROM ' . self::PROPERTIES_TABLE . ' WHERE deleted = 0 GROUP BY group_id)')
            ->execute();

        $groups = DB::select('groups.*', 'properties.has_properties', 'contacts.first_name', 'contacts.last_name')
            ->from(array(self::GROUPS_TABLE, 'groups'))
                ->join(array('tmp_has_properties', 'properties'), 'left')
                    ->on('groups.id', '=', 'properties.group_id')
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'left')
                    ->on('groups.host_contact_id', '=', 'contacts.id')
            ->where('groups.deleted', '=', 0)
            ->order_by('groups.name','asc')
            ->execute()
            ->as_array();
        return $groups;
    }

    public static function groupSet($id,
        $name,
        $address1,
        $address2,
        $countryId,
        $countyId,
        $city,
        $postcode,
        $latitude,
        $longitude,
        $total_properties,
        $host_contact_id,
        $arrival_details,
        $calendar,
        $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['name'] = $name;
        $data['address1'] = $address1;
        $data['address2'] = $address2;
        $data['countryId'] = $countryId;
        $data['countyId'] = $countyId;
        $data['city'] = $city;
        $data['postcode'] = $postcode;
        $data['latitude'] = $latitude;
        $data['longitude'] = $longitude;
        $data['total_properties'] = $total_properties;
        $data['host_contact_id'] = $host_contact_id;
        $data['arrival_details'] = $arrival_details;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];
        if (!is_numeric($id)) {
            $data['created'] = date('Y-m-d H:i:s', $time);
            $data['created_by'] = $user['id'];
            $result = DB::insert(self::GROUPS_TABLE, array_keys($data))
                ->values($data)
                ->execute();
            if (isset($result[0])) {
                $id = $result[0];
            } else {
                $id = false;
            }
        } else {
            DB::update(self::GROUPS_TABLE)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }

        if (is_array($calendar)) {
            foreach ($calendar as $day) {
                $date = $day['date'];
                $available = $day['available'];
                $existingDate = DB::select('*')
                    ->from(self::GROUPS_CALENDAR_TABLE)
                    ->where('group_id', '=', $id)
                    ->and_where('date', '=', $date)
                    ->execute()
                    ->current();
                if ($existingDate != null) {
                    if ($existingDate['available'] != $available) {
                        DB::update(self::GROUPS_CALENDAR_TABLE)
                            ->set(array('available' => $available))
                            ->where('group_id', '=', $id)
                            ->and_where('date', '=', $date)
                            ->execute();
                    }
                } else {
                    DB::insert(self::GROUPS_CALENDAR_TABLE, array('group_id', 'date', 'available'))
                        ->values(array($id, $date, $available))
                        ->execute();
                }
            }
        }

        return $id;
    }

    public static function groupPublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::GROUPS_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function groupGet($id, $allDetails = false)
    {
        if (is_numeric($id)) {
            $group = DB::select('*')
                ->from(self::GROUPS_TABLE)
                ->where('id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->current();
        } else {
            $group = DB::select('*')
                ->from(self::GROUPS_TABLE)
                ->where('name', '=', $id)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->current();
        }

        if ($group && $allDetails) {
            $group['prices'] = array();
            $group['ratecards'] = array();
            $group['host_contact'] = array();
            $group['properties'] = array();
            $group['calendar'] = array();
            $query = DB::select('*')
                ->from(self::GROUPS_CALENDAR_TABLE)
                ->where('group_id', '=', $id)
                ->order_by('date', 'asc');

            foreach ($query->execute()->as_array() as $row) {
                $group['calendar'][] = array('date' => $row['date'], 'available' => $row['available']);
            }
        }
        return $group;
    }

    public static function groupDelete($id)
    {
        DB::update(self::GROUPS_TABLE)
            ->set(array('deleted' => 1))
            ->where('id', '=', $id)
            ->execute();
    }

    public static function propertyList($filter = array(),$id = NULL)
    {
        $thumbs = false;
        if (@$filter['thumbs']) {
            $thumbs = true;
        }

        if ($thumbs) {
            DB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_thumbs')->execute();
            DB::query(null, 'CREATE TEMPORARY TABLE tmp_thumbs (property_id INT, media_id INT, KEY(property_id))')
                ->execute();
            DB::query(
                null,
                'INSERT INTO tmp_thumbs (property_id, media_id)
                  (SELECT pm.property_id, pm.media_id
                        FROM plugin_propman_properties_has_media pm
                            INNER JOIN
                                (SELECT
                                            property_id, MIN(sort) AS sort
                                        FROM plugin_propman_properties_has_media
                                        WHERE deleted = 0 AND published = 1
                                        GROUP BY property_id
                                        ORDER BY sort ASC) fm ON pm.property_id = fm.property_id AND pm.sort = fm.sort
                        WHERE pm.deleted = 0 AND pm.published = 1
                    )'
            )->execute();
        }
        if ($thumbs) {
            $select = DB::select(
                'properties.*',
                array('building.name', 'building_type'),
                array('types.name' , 'property_type'),
                array('groups.name', 'group'),
                array('tmp_thumbs.media_id', 'thumb_id'),
                array('media.filename', 'thumb'),
                array('media.location', 'thumb_location')
            );
        } else {
            $select = DB::select(
                'properties.*',
                array('building.name', 'building_type'),
                array('types.name' , 'property_type'),
                array('groups.name', 'group')
            );
        }
        $select->from(array(self::PROPERTIES_TABLE, 'properties'))
            ->join(array(self::GROUPS_TABLE, 'groups'), 'left')->on('properties.group_id', '=', 'groups.id')
            ->join(array(self::BUILDING_TYPES_TABLE,'building'),'left')->on('properties.building_type_id','=','building.id')
            ->join(array(self::PROPERTY_TYPES_TABLE,'types'),'left')->on('properties.property_type_id','=','types.id');
        if ($thumbs) {
            $select->join('tmp_thumbs', 'left')->on('properties.id', '=', 'tmp_thumbs.property_id');
            $select->join(array('plugin_media_shared_media', 'media'), 'left')
                ->on('tmp_thumbs.media_id', '=', 'media.id');
        }

        $select->where('properties.deleted', '=', 0);

        if (isset($filter['published']) && $filter['published'] !== null) {
            $select->and_where('published', '=', $filter['published']);
        }

        if (isset($filter['group_id'])) {
            $select->and_where('group_id', '=', $filter['group_id']);
        }

        if ( ! is_null($id)) {
            $select->where('properties.id','!=',$id);
        }

        $properties = $select->order_by('properties.name', 'asc')
            ->execute()
            ->as_array();

        if ($thumbs) {
            $sharedMedia = new Model_Sharedmedia();
            $folder = Kohana::$config->load('config')->project_media_folder;
            foreach ($properties as $i => $property) {
                if ($property['thumb']) {
                    $properties[$i]['thumb_url'] = Model_Media::get_path_to_media_item_admin(
                        $folder,
                        $property['thumb'],
                        $property['thumb_location']);
                } else {
                    $properties[$i]['thumb_url'] = '';
                }
            }
        }
        return $properties;
    }

    public static function propertySet($id,
        $group_id,
        $name,
        $building_type_id,
        $property_type_id,
        $ref_code,
        $beds_single,
        $beds_double,
        $beds_king,
        $beds_bunks,
        $max_occupancy,
        $rooms_ensuite,
        $rooms_bathrooms,
        $summary,
        $description,
        $address1,
        $address2,
        $countryId,
        $countyId,
        $city,
        $postcode,
        $latitude,
        $longitude,
        $facilities,
        $surcharges,
        $suitabilities,
        $sharedMedias,
        $linkedProperties,
        $linkedRatecardIds,
        $calendar,
        $override_group_calendar,
        $published)
    {
        $user = Auth::instance()->get_user();
        $group = self::groupGet($group_id);
        $data = array();
        $data['group_id'] = $group_id;
        $data['name'] = $name;
        $data['url'] = trim(
            preg_replace(
                '/[^a-z0-9]+/i',
                '-',
                strtolower($group['name'] . '-' . $name . '-' . $ref_code)
            ),
            '-'
        );
        $data['building_type_id'] = $building_type_id;
        $data['property_type_id'] = $property_type_id;
        $data['ref_code'] = $ref_code;
        $data['beds_single'] = $beds_single;
        $data['beds_double'] = $beds_double;
        $data['beds_king'] = $beds_king;
        $data['beds_bunks'] = $beds_bunks;
        $data['max_occupancy'] = $max_occupancy;
        $data['rooms_ensuite'] = $rooms_ensuite;
        $data['rooms_bathrooms'] = $rooms_bathrooms;
        $data['summary'] = $summary;
        $data['description'] = $description;
        $data['address1'] = $address1;
        $data['address2'] = $address2;
        $data['country_id'] = $countryId;
        $data['county_id'] = $countyId;
        $data['city'] = $city;
        $data['postcode'] = $postcode;
        $data['latitude'] = $latitude;
        $data['longitude'] = $longitude;
        $data['override_group_calendar'] = $override_group_calendar;
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        Database::instance()->begin();
        try {
            if (!is_numeric($id)) {
                $data['created'] = date('Y-m-d H:i:s', $time);
                $data['created_by'] = $user['id'];
                $result = DB::insert(self::PROPERTIES_TABLE, array_keys($data))
                    ->values($data)
                    ->execute();
                if (isset($result[0])) {
                    $id = $result[0];
                } else {
                    $id = false;
                }
            } else {
                DB::update(self::PROPERTIES_TABLE)
                    ->set($data)
                    ->where('id', '=', $id)
                    ->execute();
            }

            if (is_array($facilities)) {
                $facilities = array_keys($facilities);
                DB::update(self::PROPERTIES_HAS_FACILITY_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('property_id', '=', $id)
                    ->and_where('facility_type_id', 'not in', $facilities)
                    ->and_where('deleted', '=', 0)
                    ->execute();
                foreach ($facilities as $facilityTypeId) {
                    $fdata = array();
                    $fdata['facility_type_id'] = $facilityTypeId;
                    $fdata['property_id'] = $id;
                    if (@$surcharges[$facilityTypeId] > 0) {
                        $fdata['surcharge'] = $surcharges[$facilityTypeId];
                    } else {
                        $fdata['surcharge'] = null;
                    }
                    $fdata['updated'] = date('Y-m-d H:i:s', $time);
                    $fdata['updated_by'] = $user['id'];
                    $fdata['published'] = $published;
                    $fdata['deleted'] = 0;

                    $facilityId = DB::select('id')
                        ->from(self::PROPERTIES_HAS_FACILITY_TABLE)
                        ->where('property_id', '=', $id)
                        ->and_where('facility_type_id', '=', $facilityTypeId)
                        ->and_where('deleted', '=', 0)
                        ->execute()
                        ->get('id');
                    if ($facilityId) {
                        DB::update(self::PROPERTIES_HAS_FACILITY_TABLE)
                            ->set($fdata)
                            ->where('id', '=', $facilityId)
                            ->execute();
                    } else {
                        $fdata['created'] = date('Y-m-d H:i:s', $time);
                        $fdata['created_by'] = $user['id'];
                        DB::insert(self::PROPERTIES_HAS_FACILITY_TABLE, array_keys($fdata))
                            ->values($fdata)
                            ->execute();
                    }
                }
            }

            if (is_array($suitabilities)) {
                DB::update(self::PROPERTIES_HAS_SUITABILITY_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('property_id', '=', $id)
                    ->and_where('suitability_type_id', 'not in', $suitabilities)
                    ->and_where('deleted', '=', 0)
                    ->execute();
                foreach ($suitabilities as $suitabilityTypeId) {
                    $sdata = array();
                    $sdata['suitability_type_id'] = $suitabilityTypeId;
                    $sdata['property_id'] = $id;
                    $sdata['updated'] = date('Y-m-d H:i:s', $time);
                    $sdata['updated_by'] = $user['id'];
                    $sdata['published'] = $published;
                    $sdata['deleted'] = 0;

                    $suitabilityId = DB::select('id')
                        ->from(self::PROPERTIES_HAS_SUITABILITY_TABLE)
                        ->where('property_id', '=', $id)
                        ->and_where('suitability_type_id', '=', $suitabilityTypeId)
                        ->and_where('deleted', '=', 0)
                        ->execute()
                        ->get('id');
                    if ($suitabilityId) {
                        DB::update(self::PROPERTIES_HAS_SUITABILITY_TABLE)
                            ->set($sdata)
                            ->where('id', '=', $suitabilityId)
                            ->execute();
                    } else {
                        $sdata['created'] = date('Y-m-d H:i:s', $time);
                        $sdata['created_by'] = $user['id'];
                        DB::insert(self::PROPERTIES_HAS_SUITABILITY_TABLE, array_keys($sdata))
                            ->values($sdata)
                            ->execute();
                    }
                }
            }

            if (is_array($sharedMedias)) {
                DB::update(self::PROPERTIES_HAS_MEDIA_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('property_id', '=', $id)
                    ->and_where('media_id', 'not in', $sharedMedias)
                    ->and_where('deleted', '=', 0)
                    ->execute();

                foreach ($sharedMedias as $sort => $sharedMediaId) {
                    $mdata = array();
                    $mdata['media_id'] = $sharedMediaId;
                    $mdata['sort'] = $sort;
                    $mdata['property_id'] = $id;
                    $mdata['updated'] = date('Y-m-d H:i:s', $time);
                    $mdata['updated_by'] = $user['id'];
                    $mdata['published'] = $published;
                    $mdata['deleted'] = 0;

                    $hasMediaId = DB::select('id')
                        ->from(self::PROPERTIES_HAS_MEDIA_TABLE)
                        ->where('property_id', '=', $id)
                        ->and_where('media_id', '=', $sharedMediaId)
                        ->and_where('deleted', '=', 0)
                        ->execute()
                        ->get('id');
                    if ($hasMediaId) {
                        DB::update(self::PROPERTIES_HAS_MEDIA_TABLE)
                            ->set($mdata)
                            ->where('id', '=', $hasMediaId)
                            ->execute();
                    } else {
                        $mdata['created'] = date('Y-m-d H:i:s', $time);
                        $mdata['created_by'] = $user['id'];
                        DB::insert(self::PROPERTIES_HAS_MEDIA_TABLE, array_keys($mdata))
                            ->values($mdata)
                            ->execute();
                    }
                }
            }

            if (is_array($linkedProperties)) {
                DB::delete(self::PROPERTIES_LINKED_TABLE)
                    ->where('property_id_1', '=', $id)
                    ->execute();

                foreach ($linkedProperties as $sort => $linkedPropertyId) {
                    $ldata = array();
                    $ldata['property_id_1'] = $id;
                    $ldata['property_id_2'] = $linkedPropertyId;
                    $ldata['sort'] = $sort;
                    DB::insert(self::PROPERTIES_LINKED_TABLE, array_keys($ldata))
                        ->values($ldata)
                        ->execute();
                }
            }

            if (is_array($linkedRatecardIds)) {
                DB::delete(self::PROPERTIES_HAS_RATECARDS_TABLE)
                    ->where('property_id', '=', $id)
                    ->and_where('ratecard_id', 'not in', $linkedRatecardIds)
                    ->execute();

                foreach ($linkedRatecardIds as $linkedRatecardId) {
                    $ldata = array();
                    $ldata['ratecard_id'] = $linkedRatecardId;
                    $ldata['property_id'] = $id;

                    $hasRatecardId = DB::select('id')
                        ->from(self::PROPERTIES_HAS_RATECARDS_TABLE)
                        ->where('property_id', '=', $id)
                        ->and_where('ratecard_id', '=', $linkedRatecardId)
                        ->execute()
                        ->get('id');
                    if (!$hasRatecardId) {
                        DB::insert(self::PROPERTIES_HAS_RATECARDS_TABLE, array_keys($ldata))
                            ->values($ldata)
                            ->execute();
                    }
                }
            }

            if (is_array($calendar) && $override_group_calendar == 1) {
                foreach ($calendar as $day) {
                    $date = $day['date'];
                    $available = $day['available'];
                    $existingDate = DB::select('*')
                        ->from(self::PROPERTIES_CALENDAR_TABLE)
                        ->where('property_id', '=', $id)
                        ->and_where('date', '=', $date)
                        ->execute()
                        ->current();
                    if ($existingDate != null) {
                        if ($existingDate['available'] != $available) {
                            DB::update(self::PROPERTIES_CALENDAR_TABLE)
                                ->set(array('available' => $available))
                                ->where('property_id', '=', $id)
                                ->and_where('date', '=', $date)
                                ->execute();
                        }
                    } else {
                        DB::insert(self::PROPERTIES_CALENDAR_TABLE, array('property_id', 'date', 'available'))
                            ->values(array($id, $date, $available))
                            ->execute();
                    }
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
        return $id;

    }

    public static function propertyPublish($id, $published)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['published'] = $published;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::PROPERTIES_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function propertyDelete($id)
    {
        $user = Auth::instance()->get_user();
        $data = array();
        $data['deleted'] = 1;

        $time = time();
        $data['updated'] = date('Y-m-d H:i:s', $time);
        $data['updated_by'] = $user['id'];

        DB::update(self::PROPERTIES_TABLE)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
        return $id;
    }

    public static function propertyGet($id)
    {
        $property = DB::select('*')
            ->from(self::PROPERTIES_TABLE)
            ->where('id', '=', $id)
            ->execute()
            ->current();

        if ($property) {
            $property['facilities'] = DB::select('*')
                ->from(self::PROPERTIES_HAS_FACILITY_TABLE)
                ->where('property_id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->as_array();
            $property['suitabilities'] = DB::select('*')
                ->from(self::PROPERTIES_HAS_SUITABILITY_TABLE)
                ->where('property_id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->as_array();
            $property['medias'] = DB::select('*')
                ->from(self::PROPERTIES_HAS_MEDIA_TABLE)
                ->where('property_id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->order_by('sort', 'asc')
                ->execute()
                ->as_array();
			$property['ratecards'] = DB::select('ranges.*', 'rates.name', 'rates.id')
					->from(array(self::GROUPS_HAS_RATECARDS_TABLE, 'has_ratecards'))
						->join(array(self::RATECARDS_TABLE, 'rates'), 'inner')
							->on('has_ratecards.group_id', '=', DB::expr($property['group_id']))
							->on('has_ratecards.ratecard_id', '=', 'rates.id')
							->on('rates.property_type_id', '=', DB::expr($property['property_type_id']))
						->join(array(self::RATECARDS_DATERANGES_TABLE, 'ranges'), 'inner')
							->on('rates.id', '=', 'ranges.ratecard_id')
					->where('rates.deleted', '=', 0)
					->execute()
					->as_array();
            $property['linkedProperties'] = DB::select('properties.*')
                ->from(array(self::PROPERTIES_LINKED_TABLE, 'linked'))
                    ->join(array(self::PROPERTIES_TABLE, 'properties'), 'inner')
                        ->on('linked.property_id_2', '=', 'properties.id')
                ->where('linked.property_id_1', '=', $id)
                ->order_by('linked.sort', 'asc')
                ->execute()
                ->as_array();

            $query = DB::select('*')
                ->from(self::PROPERTIES_CALENDAR_TABLE)
                ->where('property_id', '=', $id);
            $data = $query
                ->order_by('date', 'asc')
                ->execute()
                ->as_array();
            $dates = array();
            foreach ($data as $row) {
                $dates[] = array('date' => $row['date'], 'available' => $row['available']);
            }

            $property['calendar'] = $dates;

            $query = DB::select('*')
                ->from(self::GROUPS_CALENDAR_TABLE)
                ->where('group_id', '=', $property['group_id']);
            $data = $query
                ->order_by('date', 'asc')
                ->execute()
                ->as_array();
            $dates = array();
            foreach ($data as $row) {
                $dates[] = array('date' => $row['date'], 'available' => $row['available']);
            }

            $property['group_calendar'] = $dates;
        }

        return $property;
    }

    public static function import_properties_rac()
    {
        $user     = Auth::instance()->get_user();
        $csv = '../database_seeding/rac_properties_import.csv';
        $csv = fopen($csv, 'r');
        $groups = array();
        if($csv) {
            $columns = fgetcsv($csv, 0, ',');
            $i = 1;
            while ($row = fgetcsv($csv, 0, ',')) {
                $group = array();
                foreach ( $columns as $i => $column) {
                    $group[$column] = trim($row[$i]);
                }
                $groups[] = $group;
            }
            fclose($csv);

            $counties = self::counties(1);

            try {
                Database::instance()->begin();
                echo "<table border=1><tr><th>Original Name</th><th>Group ID</th><th>Group Name</th><th>Address 1</th><th>Town</th><th>County</th><th>Country</th><th>Number of Properties</th><th>Properties ID's</th></tr>";
                foreach ($groups as $g => $group) {
                    $county_id = $county_id = array_search($group['regiunea'],$counties);
                    $country_id = 1;
                    $type_pos = strpos($group['pname'],'(Type');
                    $dash = strpos($group['pname'],'-');
                    $comma = strpos($group['pname'],',');
                    $pos = $comma !== FALSE ? $comma : $dash;
                    $pos1 = $type_pos !== FALSE ? $type_pos : $pos ;

                    $name = substr($group['pname'],0,$pos1);
                    $address1 = substr($group['pname'],0,$pos);

                    $name = $name =='' ? $group['pname'] : $name;
                    $address1 = $address1 =='' ? $name : $address1;

                    $group_value = array(
                        'name'              => $name,
                        'address1'          => $address1,
                        'address2'          => '',
                        'countryId'         => $country_id,
                        'countyId'          => $county_id,
                        'city'              => $group['tara'],
                        'total_properties'  => $group['nrofproperties'],
                        'host_contact_id'   => '',
                        'created'           => date("Y-m-d H:i:s"),
                        'updated'           => date("Y-m-d H:i:s"),
                        'created_by'        =>$user['id'],
                        'updated_by'        =>$user['id']
                    );
                    $group_insert = DB::insert('plugin_propman_groups',array_keys($group_value))->values($group_value)->execute();
                    $group_id = $group_insert[0];

                    echo "<tr><td>".$group['pname']."</td><td>".$group_insert[0]."</td><td>".$name."</td><td>".$address1."</td><td>".$group['town']."</td><td>".$group['regiunea']."</td>"
                        ."<td>".$group['tara']."</td><td>".$group['nrofproperties']."</td><td>";

                    $property_value = array(
                        'name'          => '',
                        'group_id'      => $group_id,
                        'max_occupancy' => $group['sleeps'],
                        'summary'       => $group['pshortdescription'],
                        'description'   => $group['description'],
                        'address1'      => $address1,
                        'address2'      => '',
                        'country_id'    => $country_id,
                        'county_id'     => $county_id,
                        'city'          => $group['tara'],
                        'ref_code'      => $group['pid'],
                        'created'       => date("Y-m-d H:i:s"),
                        'updated'       => date("Y-m-d H:i:s"),
                        'created_by'    =>$user['id'],
                        'updated_by'    =>$user['id']
                    );
                    $property_value['name'] = $name.' - House: ';
                    $property_insert = DB::insert('plugin_propman_properties',array_keys($property_value))->values($property_value)->execute();
                    echo $property_insert[0] . ' - '.$property_value['name'] . "</td></tr>";
                }
                echo "</table>";
                Database::instance()->commit();
            }
            catch(Exception $e){
                Database::instance()->rollback();
                throw $e;
            }
        } else {}
        die();
    }

    public static function getArrivalOptions($selected)
    {
        return HTML::optionsFromArray(
            array(
                'Any' => __('Any'),
                'Monday' => __('Monday'),
                'Tuesday' => __('Tuesday'),
                'Wednesday' => __('Wednesday'),
                'Thursday' => __('Thursday'),
                'Friday' => __('Friday'),
                'Saturday' => __('Saturday'),
                'Sunday' => __('Sunday')
            ),
            $selected
        );
    }

    public static function search_results($args)
    {
        $results = ORM::factory('Propman');

        // Keywords
        if (isset($args['keywords']) AND trim($args['keywords']) != '')
        {
            // Split the string into separate words.
            // Check that each word is used. The words do not have to be used one after the other.
            $keywords = explode(' ', $args['keywords']);
            foreach ($keywords as $keyword)
            {
                $county = DB::select('id')->from('engine_counties')->where('name','like', '%'.$keyword.'%')->execute()->as_array();
                $results
                    ->and_where_open()
                    ->where('name', 'like', '%'.$keyword.'%')
                    ->or_where('address1', 'like', '%'.$keyword.'%')
                    ->or_where('address2', 'like', '%'.$keyword.'%');
                if (count($county) > 0) {
                    $results->or_where('county_id', 'IN', $county);
                }
                $results->and_where_close();
            }
        }
		// County (This is separate from entering the county in the keyword search)
		if (isset($args['county']) AND $args['county'])
		{
			$county = DB::select('id')->from('engine_counties')->where('name','=', $args['county'])->execute()->as_array();
			$results->where('county_id', 'IN', $county);
		}

        // Number of guests
        if (isset($args['guests']) AND $args['guests'])
        {
            $results->where('max_occupancy', '>=', $args['guests']);
        }
        // Number of bedrooms
        if (isset($args['bedrooms']) AND $args['bedrooms'])
        {
            $results->where(DB::expr("`beds_single` + `beds_double` + `beds_king` + `beds_bunks`"), '>=', $args['bedrooms']);
        }
        // Building type
        if (isset($args['building_types']) AND is_array($args['building_types']) AND count($args['building_types']))
        {
            $results->where('building_type_id', 'in', $args['building_types']);
        }
        // Check In and Out
        if ((isset($args['check_in']) AND $args['check_in']) AND (isset($args['check_out']) AND $args['check_out']))
        {
            $check_in = date::dmy_to_ymd($args['check_in']);
            $check_out = date::dmy_to_ymd($args['check_out']);
            $date1 = new DateTime($check_in);
            $date2 = new DateTime($check_out);
            $diff = $date1->diff($date2);

            // create a list of properties available as many days as needed
            /// check property calendars
            DB::query(null, "CREATE TEMPORARY TABLE tmp_available_properties (property_id INT PRIMARY KEY, available_days INT)")->execute();
            DB::query(null, "INSERT INTO tmp_available_properties
              (property_id, available_days)
              (SELECT property_id, count(*) AS cnt
                FROM plugin_propman_properties_calendar
                  INNER JOIN plugin_propman_properties
                    ON plugin_propman_properties_calendar.property_id = plugin_propman_properties.id
                WHERE available = 1 AND
                `date` >= '" . $check_in . "' AND
                `date` <= '" . $check_out . "' AND
                plugin_propman_properties.override_group_calendar = 1
                GROUP BY property_id
                HAVING cnt >=" . $diff->days . ")")->execute();
            /// check group calendars
            DB::query(null, "INSERT INTO tmp_available_properties
              (property_id, available_days)
              (SELECT plugin_propman_properties.id, count(*) AS cnt
                FROM plugin_propman_groups_calendar
                  INNER JOIN plugin_propman_groups
                    ON plugin_propman_groups_calendar.group_id = plugin_propman_groups.id
                  INNER JOIN plugin_propman_properties
                    ON plugin_propman_groups.id = plugin_propman_properties.group_id
                WHERE available = 1 AND
                `date` >= '" . $check_in . "' AND
                `date` <= '" . $check_out . "' AND
                plugin_propman_properties.override_group_calendar = 0
                GROUP BY plugin_propman_properties.id
                HAVING cnt >=" . $diff->days . ")")->execute();

            //remove properties if there are no ratecard for the selected dates
            DB::query(null, "CREATE TEMPORARY TABLE tmp_available_ratecards (ratecard_id INT PRIMARY KEY, available_days INT)")->execute();
            DB::query(null, "INSERT INTO tmp_available_ratecards
              (ratecard_id, available_days)
              (SELECT ratecard_id, count(*) AS cnt
                FROM plugin_propman_ratecards_calendar
                  INNER JOIN plugin_propman_ratecards
                    ON plugin_propman_ratecards_calendar.ratecard_id = plugin_propman_ratecards.id
                      AND plugin_propman_ratecards.published = 1
                      AND plugin_propman_ratecards.deleted = 0
                WHERE
                `date` >= '" . $check_in . "' AND
                `date` <= '" . $check_out . "'
                GROUP BY ratecard_id
                HAVING cnt >=" . $diff->days . ")")->execute();
            DB::query(null,"DELETE FROM tmp_available_properties WHERE (property_id NOT IN (SELECT p.id
	FROM plugin_propman_properties p
		INNER JOIN plugin_propman_groups_has_ratecards gr ON p.group_id = gr.group_id
		INNER JOIN plugin_propman_ratecards r ON p.property_type_id = r.property_type_id AND gr.ratecard_id = r.id
		INNER JOIN tmp_available_ratecards tr ON r.id = tr.ratecard_id))")->execute();

            // remove properties if they are booked in the date range
            DB::query(null, "DELETE FROM tmp_available_properties
              WHERE property_id IN
              (SELECT property_id
                  FROM plugin_propman_bookings
                  WHERE deleted = 0 AND
                  `status` <> 'Cancelled' AND
                  (
                    (checkin >= '" . $check_in . "' AND checkin <= '" . $check_out . "') -- do not list properties checked in in the requested date range
                    OR
                    (checkout > '" . $check_in . "' AND checkout < '" . $check_out . "') -- do not list properties checkout in in the requested date range, checkin in same day as checkout is ok
                    OR
                    (checkin < '" . $check_in . "' AND checkout > '" . $check_out . "') -- do not list properties checked in before requested range and checkout after requested range
                  )
                  )")->execute();

            $results->join('tmp_available_properties', 'inner')
                ->on('propman.id', '=', 'tmp_available_properties.property_id');
        }
        // Check In Only
        else if (isset($args['check_in']) AND $args['check_in'])
        {
            $check_in = date('Y-m-d',strtotime($args['check_in']));
            $results->calendar->where('date','>=',$check_in);
        }

        if (isset($args['suitabilities_all'])) {
            foreach ($args['suitabilities_all'] as $i => $suitability) {
                if (!is_numeric($suitability)) {
                    $args['suitabilities_all'][$i] = self::suitabilityTypeGetId($suitability);
                }
            }
            DB::query(null, "CREATE TEMPORARY TABLE tmp_suitable_properties (property_id INT PRIMARY KEY)")->execute();
            DB::query(null, "INSERT INTO tmp_suitable_properties
                (property_id)
                (SELECT property_id FROM " . self::PROPERTIES_HAS_SUITABILITY_TABLE . " WHERE suitability_type_id in :suitabilities AND deleted=0 GROUP BY property_id HAVING COUNT(*) >= " . count($args['suitabilities_all']) . ")")
                ->bind(':suitabilities', $args['suitabilities_all'])
                ->execute();
            $results->join('tmp_suitable_properties', 'inner')->on('propman.id', '=', 'tmp_suitable_properties.property_id');
        }

        if (isset($args['suitabilities_any'])) {
            foreach ($args['suitabilities_any'] as $i => $suitability) {
                if (!is_numeric($suitability)) {
                    $args['suitabilities_any'][$i] = self::suitabilityTypeGetId($suitability);
                }
            }
            DB::query(null, "CREATE TEMPORARY TABLE tmp_suitable_properties2 (property_id INT PRIMARY KEY)")->execute();
            DB::query(null, "INSERT INTO tmp_suitable_properties2
                (property_id)
                (SELECT DISTINCT property_id FROM " . self::PROPERTIES_HAS_SUITABILITY_TABLE . " WHERE suitability_type_id in :suitabilities AND deleted=0)")
                ->bind(':suitabilities', $args['suitabilities_any'])
                ->execute();
            $results->join('tmp_suitable_properties2', 'inner')->on('propman.id', '=', 'tmp_suitable_properties2.property_id');
        }

        if (isset($args['facilities_all'])) {
            foreach ($args['facilities_all'] as $i => $facility) {
                if (!is_numeric($facility)) {
                    $args['facilities_all'][$i] = self::facilityTypeGetId($facility);
                }
            }

            DB::query(null, "CREATE TEMPORARY TABLE tmp_facility_properties (property_id INT PRIMARY KEY)")->execute();
            DB::query(null, "INSERT INTO tmp_facility_properties
                (property_id)
                (SELECT property_id FROM " . self::PROPERTIES_HAS_FACILITY_TABLE . " WHERE facility_type_id in :facilities and deleted=0 GROUP BY property_id HAVING COUNT(*) >= " . count($args['facilities_all']) . ")")
                ->bind(':facilities', $args['facilities_all'])
                ->execute();
            $results->join('tmp_facility_properties', 'inner')->on('propman.id', '=', 'tmp_facility_properties.property_id');
        }

        if (isset($args['deals'])) {
            //remove properties if there are no ratecard for the selected dates
            DB::query(null, "CREATE TEMPORARY TABLE tmp_deal_ratecards (ratecard_id INT PRIMARY KEY)")->execute();
            DB::query(null, "INSERT IGNORE INTO tmp_deal_ratecards
              (ratecard_id)
              (SELECT plugin_propman_ratecards.id
                FROM plugin_propman_ratecards_calendar
                  INNER JOIN plugin_propman_ratecards
                    ON plugin_propman_ratecards_calendar.ratecard_id = plugin_propman_ratecards.id
                      AND plugin_propman_ratecards.published = 1
                      AND plugin_propman_ratecards.deleted = 0
                  INNER JOIN plugin_propman_ratecards_date_ranges
                    ON plugin_propman_ratecards.id = plugin_propman_ratecards_date_ranges.ratecard_id
                WHERE
                " . (@$args['check_in'] && @$args['check_out'] ? '`date` >= :check_in AND `date` <= :check_out' : '`date` >= NOW()') . " AND is_deal = 1)")
                ->bind(':check_in', $check_in)
                ->bind(':check_out', $check_out)
                ->execute();

            DB::query(null, "CREATE TEMPORARY TABLE tmp_deal_properties (property_id INT PRIMARY KEY)")->execute();

            DB::query(null, "INSERT IGNORE INTO tmp_deal_properties
              (property_id)
              (SELECT p.id
                FROM plugin_propman_properties p
                    INNER JOIN plugin_propman_groups_has_ratecards gr ON p.group_id = gr.group_id
                    INNER JOIN plugin_propman_ratecards r ON p.property_type_id = r.property_type_id AND gr.ratecard_id = r.id
                    INNER JOIN tmp_deal_ratecards dr ON r.id = dr.ratecard_id)")->execute();

            $results->join('tmp_deal_properties', 'inner')->on('propman.id', '=', 'tmp_deal_properties.property_id');
        }

        if (isset($args['facilities_any'])) {
            foreach ($args['facilities_any'] as $i => $facility) {
                if (!is_numeric($facility)) {
                    $args['facilities_any'][$i] = self::facilityTypeGetId($facility);
                }
            }

            DB::query(null, "CREATE TEMPORARY TABLE tmp_facility_properties2 (property_id INT PRIMARY KEY)")->execute();
            DB::query(null, "INSERT INTO tmp_facility_properties2
                (property_id)
                (SELECT DISTINCT property_id FROM " . self::PROPERTIES_HAS_FACILITY_TABLE . " WHERE facility_type_id in :facilities and deleted=0)")
                ->bind(':facilities', $args['facilities_any'])
                ->execute();
            $results->join('tmp_facility_properties2', 'inner')->on('propman.id', '=', 'tmp_facility_properties2.property_id');
        }

        $return = array();

        // Pagination
        if (!isset($args['limit'])) {
            $limit = 10;
        } else if ($args['limit'] > 0){
            $limit = $args['limit'];
        } else {
            $limit = 1000;
        }
        if ($limit) {
            if (!isset($args['page'])) {
                $args['page'] = 1;
            }
            $offset = ($args['page'] - 1) * $limit;
            $countq = clone $results;
            $count = $countq->find_all_published()->count(); // use cloned query; kohana orm resets criteria
            if (@$args['result_format'] == 'map') {
                $results = $results->find_all_published();
            } else {
                $results = $results->offset($offset)->limit($limit)->find_all_published();
            }
        }


        $return['results'] = $results;
        $return['page'] = $args['page'];
        $first_result = ($args['page'] - 1) / $limit;
        $last_result = ($limit + $offset > $count) ? $count : $limit + $offset;
        $return['results_found'] = 'Showing results '.$first_result.' to '.$last_result.' of '.$count;
        $return['count'] = $count;

        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS tmp_available_properties")->execute();
        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS tmp_available_ratecards")->execute();
        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS tmp_suitable_properties")->execute();
        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS tmp_suitable_properties2")->execute();
        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS tmp_facility_properties")->execute();
        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS tmp_facility_properties2")->execute();
        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS tmp_deal_ratecards")->execute();
        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS tmp_deal_properties")->execute();

        return $return;
    }

    public static function getDeals($propertyId = null, $checkin = null, $checkout = null)
    {
        $select = DB::select('prices.*', array('properties.id', 'property_id'))
            ->from(array(self::RATECARDS_DATERANGES_TABLE, 'prices'))
                ->join(array(self::RATECARDS_TABLE, 'rates'))
                    ->on('prices.ratecard_id', '=', 'rates.id')
                    ->on('prices.is_deal', '=', DB::expr(1))
                ->join(array(self::GROUPS_HAS_RATECARDS_TABLE, 'gr'))
                    ->on('rates.id', '=', 'gr.ratecard_id')
                ->join(array(self::PROPERTIES_TABLE, 'properties'))
                    ->on('rates.property_type_id', '=', 'properties.property_type_id')
                    ->on('gr.group_id', '=', 'properties.group_id')
            ->where('properties.deleted', '=', 0)
            ->and_where('properties.published', '=', 1)
            ->and_where('rates.deleted', '=', 0)
            ->and_where('rates.published', '=', 1);
        if ($propertyId) {
            $select->where('properties.id', '=', $propertyId);
        }
        if ($checkin && $checkout) {
            $select->and_where('prices.starts', '>=', $checkin);
            $select->and_where('prices.ends', '<=', $checkout);
        } else {
            $select->and_where('prices.ends', '>=', date('Y-m-d'));
        }
        $select->order_by('prices.starts');
        return $select->execute()->as_array();
    }

	// This is for the short tag
	// put {property_deals-} in a page to embed the feed
	public static function render_deals_feed()
	{
		$deals = array();

		// Get all counties
		$counties = DB::select('id', 'name')
			->from('engine_counties')
			->where('publish', '=', 1)
			->where('deleted', '=', 0)
			->order_by('name')
			->execute()
			->as_array();

        $county_deals = Model_Propman::search_results(array('deals' => 1, 'limit' => false));
        $county_deals = $county_deals['results']->as_array();
        // Get deals for each county. Put them in a 3D array.
		foreach ($counties as $county)
		{
			foreach ($county_deals as $property) {
                if ($property->county_id == $county['id']) {
                    $deals[$county['name']][] = $property;
                }
            }
		}

		// Put the deals in a view and return it
		return (string) View::factory('front_end/property_deals')->set('counties', $deals);
	}

    public static function property_rate_card_dates($propertyId)
    {
        $result = array();
        $ratecard_dates = DB::select('rc_c.date')
            ->from(array(self::GROUPS_HAS_RATECARDS_TABLE,'g_has_r'))
                ->join(array(self::RATECARDS_TABLE, 'rates'), 'inner')
                    ->on('g_has_r.ratecard_id', '=', 'rates.id')
                ->join(array(self::PROPERTIES_TABLE,'p'), 'inner')
                    ->on('g_has_r.group_id','=', 'p.group_id')
                    ->on('rates.property_type_id', '=', 'p.property_type_id')
                ->join(array(self::RATECARDS_CALENDAR_TABLE,'rc_c'), 'inner')
                    ->on('rc_c.ratecard_id','=', 'g_has_r.ratecard_id')
            ->where('p.id','=',$propertyId)
            ->execute()
            ->as_array();
        foreach ($ratecard_dates as $d)
        {
            $result[] = date('j-n-Y',strtotime($d['date']));
        }
        return $result;
    }

    public static function isAvailable($propertyId, $checkin, $checkout)
    {
        if ($checkin == "" || $checkout == "") {
            return false;
        }

        $property = DB::select('*')
            ->from(self::PROPERTIES_TABLE)
            ->where('id', '=', $propertyId)
            ->execute()
            ->current();
        if (!$property) {
            return false;
        }
        if ($property['override_group_calendar'] == 1) {
            // check property calendar
            $unavailable = DB::select(DB::expr('count(*) as unavailable'))
                ->from(self::PROPERTIES_CALENDAR_TABLE)
                ->where('property_id', '=', $propertyId)
                ->and_where('date', '>=', $checkin)
                ->and_where('date', '<=', $checkout)
                ->and_where('available', '=', 0)
                ->execute()
                ->get('unavailable');
        } else {
            // check group calendar
            $unavailable = DB::select(DB::expr('count(*) as unavailable'))
                ->from(self::GROUPS_CALENDAR_TABLE)
                ->where('group_id', '=', $property['group_id'])
                ->and_where('date', '>=', $checkin)
                ->and_where('date', '<=', $checkout)
                ->and_where('available', '=', 0)
                ->execute()
                ->get('unavailable');
        }

        // check if a price is set for these dates
        if ($unavailable == 0) {
            $checkinDate = new DateTime($checkin);
            $checkoutDate = new DateTime($checkout);
            $dayCount = $checkinDate->diff($checkoutDate);
            $nights = $dayCount->days;
            $property = self::propertyGet($propertyId);
            $priced = (int)DB::select(DB::expr('count(*) as priced'))
                ->from(array(self::GROUPS_HAS_RATECARDS_TABLE, 'has_ratecards'))
                    ->join(array(self::RATECARDS_TABLE, 'rates'), 'inner')
                        ->on('has_ratecards.group_id', '=', DB::expr($property['group_id']))
                        ->on('has_ratecards.ratecard_id', '=', 'rates.id')
                        ->on('rates.property_type_id', '=', DB::expr($property['property_type_id']))
                    ->join(array(self::RATECARDS_CALENDAR_TABLE, 'calendar'), 'inner')
                        ->on('rates.id', '=', 'calendar.ratecard_id')
                ->where('calendar.date', '>=', $checkin)
                ->and_where('calendar.date', '<', $checkout)
                ->and_where('rates.published', '=', 1)
                ->and_where('rates.deleted', '=', 0)
                ->execute()
                ->get('priced');

            // check if it's not already booked
            if ($priced == $nights) {
                $booked = (int)DB::select(DB::expr('count(*) as booked'))
                    ->from(self::BOOKINGS_TABLE)
                    ->where('property_id', '=', $propertyId)
                    ->and_where('deleted', '=', 0)
                    ->and_where('status', '<>', 'Cancelled')
                    ->and_where_open()
                        ->or_where_open()
                            ->and_where('checkin', '>=', $checkin)
                            ->and_where('checkin', '<=', $checkout)
                        ->or_where_close()
                        ->or_where_open()
                            ->and_where('checkout', '>', $checkin)
                            ->and_where('checkout', '<=', $checkout)
                        ->or_where_close()
                        ->or_where_open()
                            ->and_where('checkin', '<', $checkin)
                            ->and_where('checkout', '>', $checkout)
                        ->or_where_close()
                    ->and_where_close()
                    ->execute()
                    ->get('booked');

                if ($booked > 0) {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public static function getRates($groupId, $propertyTypeId, $time = null, $published_only = FALSE)
    {
        $rates = array();
        if (! is_null($groupId)) {
            $query = DB::select('ranges.*')
                ->from(array(self::GROUPS_HAS_RATECARDS_TABLE, 'has_ratecards'))
                ->join(array(self::RATECARDS_TABLE, 'rates'), 'inner')
                ->on('has_ratecards.group_id', '=', DB::expr($groupId))
                ->on('has_ratecards.ratecard_id', '=', 'rates.id')
                ->on('rates.property_type_id', '=', DB::expr($propertyTypeId))
                ->join(array(self::RATECARDS_DATERANGES_TABLE, 'ranges'), 'inner')
                ->on('rates.id', '=', 'ranges.ratecard_id')
                ->where('rates.deleted', '=', 0);

            if ($published_only)
            {
               $query->where('rates.published', '=', 1);
            }

            if ($time) {
                $query->and_where('ranges.ends', '>=', date('Y-m-d H:i:s'));
            }
            $rates = $query->order_by('ranges.starts', 'asc')
                ->execute()
                ->as_array();
        }
        return $rates;
    }

    public static function calculatePrice($propertyId, $checkin, $checkout, $guests, $applyDiscounts = true)
    {
        if ($checkin == "" || $checkout == "" || $guests == "") {
            return array('error' => 'invalid');
        }

        $propertyId = (int)$propertyId;
        $property = self::propertyGet($propertyId);
        $checkinDate = new DateTime($checkin);
        $checkoutDate = new DateTime($checkout);
        $checkinDayName = $checkinDate->format('l');
        $dayCount = $checkinDate->diff($checkoutDate);
        $nights = $dayCount->days;
        $guests = (int)$guests;
        $result = null;
        $bookingFee = (float)Settings::instance()->get('propman_booking_fee');
        $maxAdditionalNightsPrice = 0;
        $ignoreLowNights = false;
        $discardLowNights = 0;
        $shortStaySetting = (int)Settings::instance()->get('propman_short_stay');
        $additionalNightsMaxSetting = (int)Settings::instance()->get('propman_additional_nights_max');

        $rates = DB::select('ranges.*')
            ->from(array(self::GROUPS_HAS_RATECARDS_TABLE, 'has_ratecards'))
                ->join(array(self::RATECARDS_TABLE, 'rates'), 'inner')
                    ->on('has_ratecards.group_id', '=', DB::expr($property['group_id']))
                    ->on('has_ratecards.ratecard_id', '=', 'rates.id')
                    ->on('rates.property_type_id', '=', DB::expr($property['property_type_id']))
                ->join(array(self::RATECARDS_DATERANGES_TABLE, 'ranges'), 'inner')
                    ->on('rates.id', '=', 'ranges.ratecard_id')
            ->where('rates.deleted', '=', 0)
            ->and_where('rates.published', '=', 1)
            ->and_where_open()
                ->or_where_open()
                    ->and_where('ranges.ends', '>', $checkin)
                    ->and_where('ranges.ends', '<=', $checkout)
                ->or_where_close()
                ->or_where_open()
                    ->and_where('ranges.starts', '>=', $checkin)
                    ->and_where('ranges.starts', '<', $checkout)
                ->or_where_close()
                ->or_where_open()
                    ->and_where('ranges.starts', '<=', $checkin)
                    ->and_where('ranges.ends', '>=', $checkout)
                ->or_where_close()
            ->and_where_close()
            ->order_by('ranges.starts', 'asc')
            ->execute()
            ->as_array();

        if (count($rates) == 0) {
            $result = array('error' => 'norate');
        } else {
            //check if there are more than one rate between checking/out dates
            //all must be continuous
            $noEmptyDates = true;
            $previous = null;
            $arrivalOk = null;
            foreach ($rates as $rate) {
                if ($previous != null) {
                    if ($rate['starts'] != $previous['ends']) {
                        $noEmptyDates = false;
                        break;
                    }
                }
                if ($arrivalOk === null) {
                    if ($rate['arrival'] == 'Any' || $rate['arrival'] == $checkinDayName) { // booking wiki rule 5; test arrival day
                        $arrivalOk = true;
                    } else {
                        $arrivalOk = false;
                        $result = array(
                            'error' => 'arrival',
                            'arrival' => $rate['arrival'],
                            'reason' => sprintf(
                                __('Check in must be on %s between %s and %s'),
                                $rate['arrival'],
                                date('d/m/Y', strtotime($rate['starts'])),
                                date('d/m/Y', strtotime($rate['ends']))
                            )
                        );
                        break;
                    }
                }
                $maxAdditionalNightsPrice = max($rate['additional_nights_price'], $maxAdditionalNightsPrice); // booking wiki rules 3 & 4; calculate most expensive additional days price
                $previous = $rate;
            }

            if ($arrivalOk) {
                if ($noEmptyDates) {

                    $rstart = clone $checkinDate;
                    foreach ($rates as $rate) { // booking wiki rules 8 & 9;
                        $rprice = $rate;
                        $rends = new DateTime($rate['ends']);
                        if ($rends > $checkoutDate) {
                            $diff = $rstart->diff($checkoutDate);
                        } else {
                            $diff = $rstart->diff($rends);
                        }

                        if ($rprice['pricing'] == 'High' && $nights >= 7 && ($diff->days % 7)) {
                            if ((7 - ($diff->days % 7)) <= $additionalNightsMaxSetting) {
                                $ignoreLowNights = true;
                                $discardLowNights += 7 - ($diff->days % 7);
                            }
                        }
                        $rstart = $rends;
                    }

                    $rprices = array();
                    $rstart = $checkinDate;

                    $fee = 0.0;
                    $discount = 0.0;
                    $calculatedNights = 0;
                    foreach ($rates as $rate) {
                        $rprice = $rate;
                        $rends = new DateTime($rate['ends']);
                        if ($rends > $checkoutDate) {
                            $diff = $rstart->diff($checkoutDate);
                        } else {
                            $diff = $rstart->diff($rends);
                        }

                        $rprice['nights'] = $diff->days;
                        if ($rprice['pricing'] == 'High' && $rprice['nights'] < 7 && // booking wiki rule 2; test min stay
                            $ignoreLowNights == false // booking wiki rule 8
                        ) {
                            $result = array(
                                'error' => 'min_stay',
                                'days' => 7,
                                'starts' => $rate['starts'],
                                'ends' => $rate['ends'],
                                'ignoreLowNights' => $ignoreLowNights,
                                'discardLowNights' => $discardLowNights,
                                'maxAdditionalNightsPrice' => $maxAdditionalNightsPrice,
                                'reason' => sprintf(
                                    __('Minimum booking %d nights Between %s and %s'),
                                    7,
                                    date('d/m/Y', strtotime($rate['starts'])),
                                        date('d/m/Y', strtotime($rate['ends']))
                                )
                            );
                            break;
                        } else {
                            if (($rprice['pricing'] == 'High' && $rprice['nights'] < $rprice['min_stay'] && $ignoreLowNights == false) ||
                                ($rprice['pricing'] == 'Low' && $nights < $rprice['min_stay']) // booking wiki rule 1, 3, 4; test total nights min stay
                            ) {
                                $result = array(
                                    'error' => 'min_stay',
                                    'days' => $rprice['min_stay'],
                                    'starts' => $rate['starts'],
                                    'ends' => $rate['ends'],
                                    'ignoreLowNights' => $ignoreLowNights,
                                    'discardLowNights' => $discardLowNights,
                                    'maxAdditionalNightsPrice' => $maxAdditionalNightsPrice,
                                    'reason' => sprintf(
                                        __('Minimum booking %d nights Between %s and %s'),
                                        $rprice['min_stay'],
                                        date('d/m/Y', strtotime($rate['starts'])),
                                        date('d/m/Y', strtotime($rate['ends']))
                                    )
                                );
                                break;
                            } else {
                                if ($rprice['nights'] >= 7 || ($nights >= 7 && $ignoreLowNights == true && $rprice['pricing'] == 'High')) { // booking wiki rule 8,9
                                    if ($nights >= 7 && $ignoreLowNights == true && $rprice['pricing'] == 'High') { // booking wiki rule 8,9
                                        $rprice['weeks'] = ceil(($rprice['nights'] - $discardLowNights) / 7);
                                        $rprice['additional_days'] = 0;
                                    } else if ($rprice['additional_nights_price'] == 0 || $rprice['short_stay_price'] == 0) { // booking wiki rule 7
                                        $rprice['weeks'] = ceil($rprice['nights'] / 7);
                                        $rprice['additional_days'] = 0;
                                    } else {
                                        $rprice['weeks'] = floor($rprice['nights'] / 7);
                                        $rprice['additional_days'] = $rprice['nights'] - ($rprice['weeks'] * 7);
                                    }
                                    $rprice['fee'] = round(
                                        ($rprice['weeks'] * $rprice['weekly_price']) +
                                        ($rprice['additional_days'] * $rprice['additional_nights_price'])
                                    );
                                    if ($applyDiscounts) {
                                        $rprice['appliedDiscount'] = $rprice['discount_type'] == 'Fixed' ?
                                            $rprice['weeks'] * $rprice['discount']
                                            :
                                            $rprice['fee'] * ($rprice['discount'] / 100);
                                    } else {
                                        $rprice['appliedDiscount'] = 0;
                                    }
                                } else {
                                    if ($ignoreLowNights == true && $rprice['pricing'] == 'Low' && $rprice['nights'] <= $shortStaySetting) { // booking wiki rule 8, 9
                                        if ($rprice['pricing'] == 'Low') { // booking wiki rule 3, 4
                                            $rprice['fee'] = ($rprice['nights'] - $discardLowNights) * $maxAdditionalNightsPrice;
                                        }
                                    } else {
                                        if ($rprice['pricing'] == 'Low' && $rprice['nights'] < $rprice['min_stay'] && $nights > $rprice['min_stay']) { // booking wiki rule 3, 4
                                            $rprice['fee'] = $rprice['nights'] * $maxAdditionalNightsPrice;
                                        } else {
                                            $snights = $nights - $calculatedNights;
                                            if ($snights <= $shortStaySetting) {
                                                $rprice['fee'] = $rprice['short_stay_price'];
                                            } else {
                                                $rprice['fee'] = $rprice['weekly_price'];
                                            }
                                        }
                                    }
                                    $rprice['appliedDiscount'] = 0;
                                }
                                $calculatedNights += $rprice['nights'];
                                $fee += $rprice['fee'];
                                $discount += $rprice['appliedDiscount'];
                                $rprices[] = $rprice;
                                $rstart = $rends;
                            }
                        }
                    }
                    if ($result === null) {
                        if ($rends >= $checkoutDate) {
                            $result = array(
                                'error' => false,
                                'arrival' => $checkinDayName,
                                'nights' => $nights,
                                'nightfee' => round($fee / $nights, 2),
                                'fee' => $fee,
                                'bookingfee' => $bookingFee,
                                'guests' => $guests,
                                'total' => ($fee + $bookingFee - $discount),
                                'discount' => $discount,
                                'rates' => $rprices,
                                'ignoreLowNights' => $ignoreLowNights,
                                'discardLowNights' => $discardLowNights,
                                'maxAdditionalNightsPrice' => $maxAdditionalNightsPrice
                            );
                        } else {
                            $result = array('error' => 'norate2', 'reason' => __('Not Available'));
                        }
                    }
                } else {
                    $result = array('error' => 'norate3', 'reason' => __('Not Available'));
                }
            }
        }

        return $result;
    }

    public function count_beds()
    {
        return $this->beds_single + $this->beds_double + $this->beds_king + $this->beds_bunks;
    }

    public function get_rate_card($start = NULL, $end = NULL)
    {
        $start = is_null($start) ? date('Y-m-d') : $start;
        $end   = is_null($end)   ? date('Y-m-d') : $end;

        return $this->rate_cards
            ->join(array(self::PERIODS_TABLE, 'period'))
            ->on('propman_ratecard.period_id', '=', 'period.id')
            ->where('period.starts', '<=', $start)
            ->where('period.ends', '>', $end)
            ->find_published();
    }

    /**
     * Calculate the price for a stay in the loaded property
     *
     * @param $check_in            date    the check in date
     * @param $check_out        date    the check out date
     * @param $guests            int        the number of guests
     * @param $include_discount bool    take the discount, if applicable, into account
     * @return                    int        the price
     */
    public function calculate_price($check_in, $check_out, $guests, $include_discount = TRUE)
    {
        /** Get the length of the stay in weeks and days **/
        $start_date       = new DateTime($check_in);
        $end_date         = new DateTime($check_out);
        $days             = $start_date->diff($end_date)->format('%a');
        $weeks            = ($days - $days % 7) / 7;
        $days             = $days % 7;

        /** Apply rates **/
        $rate_card        = $this->get_rate_card($check_in, $check_out);

        if ($weeks == 0)
        {
            // Less than one week => number of days * short stay rate * number of guests
            $price = $days * $rate_card->short_stay_price * $guests;
        }
        else
        {
            // Longer than one week => number of weeks * weekly price + additional days * additional nights price
            // Users staying longer than a week are entitled to a discount, if one exists
            $price_per_person = $weeks * $rate_card->weekly_price + $days * $rate_card->additional_nights_price;
            $price            = $price_per_person * $guests;

            if ($include_discount)
            {
                if ($rate_card->discount_type == 'Fixed')
                {
                    $price -= $rate_card->discount;
                }
                elseif ($rate_card->discount_type == 'Percent')
                {
                    $price -= $price * ($rate_card->discount / 100);
                }
            }
        }

        return $price;
    }

    public function get_photos()
    {
        // Base filepath
        $filepath = HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'properties'));

        // Get all published photos for the property
        $photos = $this->photos
            ->where(self::PROPERTIES_HAS_MEDIA_TABLE.'.published', '=', 1)
            ->where(self::PROPERTIES_HAS_MEDIA_TABLE.'.deleted', '=', 0)
            ->order_by('sort')
            ->find_all();

        $return = array();
        // Add the filepath and thumb filepath to each photo object
        foreach ($photos as $photo)
        {
            $dimensions = explode('x', $photo->dimensions);

            $new_photo = (object) $photo->_object;
            $new_photo->filepath = $filepath.$photo->filename;
            $new_photo->thumb_filepath = $filepath.'_thumbs/'.$photo->filename;
            $new_photo->width  = isset($dimensions[0]) ? $dimensions[0] : '';
            $new_photo->height = isset($dimensions[1]) ? $dimensions[1] : '';
            $return[] = $new_photo;
        }

        return $return;
    }

    // Get the filepath to the thumbnail (the second image for the property).
    public function get_thumbnail()
    {
        $photo = $this->photos
            ->where(self::PROPERTIES_HAS_MEDIA_TABLE.'.published', '=', 1)
            ->where(self::PROPERTIES_HAS_MEDIA_TABLE.'.deleted', '=', 0)
            ->order_by('sort')
            ->offset(1)
            ->find();

        $filename = '';
        if (trim($photo->filename) != '')
        {
            $filepath = HTML::entities(Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'properties'));

            $filename = $filepath.'_thumbs/'.$photo->filename;
        }

        return $filename;
    }

    // Check if the loaded property is in the wishlist cookie
    public function is_on_wishlist()
    {
        $wishlist = (array) json_decode(Cookie::get('propman_wishlist', '[]'));
        return in_array($this->id, $wishlist);
    }


    // Temporary. This should be replaced after the county and country tables have been setup
    public function get_county()
    {
        $counties = self::counties($this->country_id);
        return isset($counties[$this->county_id]) ? $counties[$this->county_id] : '';
    }

    public static function sendBookingMessages($bookingId)
    {
        $booking = self::bookingGet($bookingId);
        $property = self::propertyGet($booking['property_id']);
        $group = self::groupGet($property['group_id']);
        try {
            if ($group['host_contact_id'] > 0) {
                $hostContact = new Model_Contacts($group['host_contact_id']);
                $hostContact = $hostContact->get_details();
            } else {
                $hostContact = array(
                    'first_name' => '',
                    'last_name' => '',
                    'phone' => '',
                    'mobile' => ''
                );
            }
        } catch (Exception $exc) {
            $hostContact = array(
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'mobile' => ''
            );
        }
        $payment = DB::select('*')
            ->from(self::PAYMENTS_TABLE)
            ->where('booking_id', '=', $bookingId)
            ->execute()
            ->current();

        $messageParams = array();
        $messageParams['property_name'] = $property['name'];
        $messageParams['ref_code'] = $property['ref_code'];
        $messageParams['property_url_frontend'] = 'http://' . $_SERVER['HTTP_HOST'] . '/property-details.html/' . $property['url'];
        $messageParams['property_url_backend'] = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/propman/property_edit?id=' . $property['id'];
        $messageParams['checkin'] = $booking['checkin'];
        $messageParams['checkout'] = $booking['checkout'];
        $messageParams['guests'] = $booking['guests'];
        $messageParams['amount_paid'] = $payment['amount'];
        $messageParams['amount_rental'] = $booking['price'];
        $messageParams['guests'] = $booking['guests'];
        $messageParams['customer'] = $booking['billing_name'];
        $messageParams['billing_name'] = $booking['billing_name'];
        $messageParams['billing_address'] = $booking['billing_address'];
        $messageParams['billing_town'] = $booking['billing_town'];
        $messageParams['billing_county'] = $booking['billing_county'];
        $messageParams['billing_country'] = $booking['billing_country'];
        $messageParams['billing_phone'] = $booking['billing_phone'];
        $messageParams['billing_email'] = $booking['billing_email'];
        $messageParams['comments'] = strip_tags($booking['comments']);
        $messageParams['time'] = date('Y-m-d H:i:s');
        $messageParams['hostname'] = $_SERVER['HTTP_HOST'];
        $messageParams['property_contact'] = $hostContact['first_name'] . ' ' . $hostContact['last_name'];
        $messageParams['property_phone'] = $hostContact['phone'];
        $messageParams['property_mobile'] = $hostContact['mobile'];
        $messageParams['arrival_details'] = $group['arrival_details'];

        $extraTargets = array(array(
            'template_id' => null,
            'target_type' => 'CMS_CONTACT',
            'target' => $booking['customer_id'] ,
        ));
        $messaging = new Model_Messaging;
        $messaging->send_template('new_booking_admin', null, null, array(), $messageParams);
        $messaging->send_template('new_booking_customer', null, null, $extraTargets, $messageParams);
    }

    public static function fixPropertyUrls()
    {
        $properties = DB::select('properties.*', array('groups.name', 'group'))
            ->from(array(self::PROPERTIES_TABLE, 'properties'))
                ->join(array(self::GROUPS_TABLE, 'groups'))
                    ->on('properties.group_id', '=', 'groups.id')
            ->where('properties.deleted', '=', 0)
            ->execute()
            ->as_array();
        try {
            Database::instance()->begin();
            foreach ($properties as $property) {
                $url = trim(
                    preg_replace(
                        '/[^a-z0-9]+/',
                        '-',
                        strtolower($property['group'] . '-' . $property['name'] . '-' . $property['ref_code'])
                    ),
                    '-'
                );
                DB::update(self::PROPERTIES_TABLE)
                    ->set(array('url' => $url))
                    ->where('id', '=', $property['id'])
                    ->execute();
            }
            Database::instance()->commit();
        } catch (Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }
    }


    public static function saveBookingFromPost($post)
    {
        $user = Auth::instance()->get_user();

        $result = array('error' => false);
        foreach ($post as $key => $value) {
            $post[$key] = trim(strip_tags($value));
        }

        $propertyId = (int)$post['property_id'];
        $checkin = $post['check_in'];
        $checkout = $post['check_out'];
        $guests = (int)$post['guests'];
        $available = self::isAvailable($propertyId, $checkin, $checkout, $guests);
        if ($available) {

            try {
                Database::instance()->begin();
                $price = self::calculatePrice($propertyId, $checkin, $checkout, $guests);
                if (!is_numeric($post['booking_id'])) {
                    $contact = new Model_Contacts();
                    $contact->set_first_name($post['firstName']);
                    $contact->set_last_name($post['lastName']);
                    $contact->set_email($post['email']);
                    $contact->set_phone($post['telephone']);
                    $contact->set_notes($post['comments']);
                    $contact->set_mailing_list('Customer');
                    $contact->save();
                    $contactDetails = $contact->get_details();
                    $result['customer_id'] = $contactDetails['id'];

                    $booking = array();
                    $booking['customer_id'] = $result['customer_id'];
                    $booking['adults'] = @$post['adults'];
                    $booking['children'] = @$post['children'];
                    $booking['infants'] = @$post['infants'];
                    $booking['billing_name'] = $post['firstName'] . ' ' . $post['lastName'];
                    $booking['billing_address'] = $post['address'];
                    $booking['billing_town'] = $post['town'];
                    $booking['billing_county'] = $post['county'];
                    $booking['billing_country'] = $post['country'];
                    $booking['billing_phone'] = $post['telephone'];
                    $booking['billing_email'] = $post['email'];
                    $booking['comments'] = $post['comments'];
                    $booking['property_id'] = $propertyId;
                    $booking['checkin'] = $checkin;
                    $booking['checkout'] = $checkout;
                    $booking['guests'] = $guests;
                    $booking['fee'] = $price['fee'];
                    $booking['discount'] = $price['discount'];
                    $booking['price'] = $price['total'];
                    $booking['created'] = $booking['updated'] = date('Y-m-d H:i:s');
                    $booking['created_by'] = $booking['updated_by'] = $user['id'];
                    $booking['deleted'] = 0;
                    $booking['status'] = 'New';

                    $bookingResult = DB::insert(self::BOOKINGS_TABLE, array_keys($booking))
                        ->values($booking)
                        ->execute();
                    if (isset($bookingResult[0])) {
                        $booking['id'] = $bookingResult[0];
                        $result['booking_id'] = $booking['id'];
                        $result['action'] = 'created';
                    } else {
                        $result['error'] = true;
                        $result['message'] = __('Unexpected Error');
                    }
                } else {
                    $booking = DB::select('*')
                        ->from(self::BOOKINGS_TABLE)
                        ->where('id', '=', $post['booking_id'])
                        ->execute()
                        ->current();
                    $booking['adults'] = $post['adults'];
                    $booking['children'] = $post['children'];
                    $booking['infants'] = $post['infants'];
                    $booking['billing_name'] = $post['firstName'] . ' ' . $post['lastName'];
                    $booking['billing_address'] = $post['address'];
                    $booking['billing_town'] = $post['town'];
                    $booking['billing_county'] = $post['county'];
                    $booking['billing_country'] = $post['country'];
                    $booking['billing_phone'] = $post['phone'];
                    $booking['billing_email'] = $post['email'];
                    $booking['comments'] = $post['comments'];
                    $booking['property_id'] = $propertyId;
                    $booking['checkin'] = $checkin;
                    $booking['checkout'] = $checkout;
                    $booking['guests'] = $guests;
                    $booking['fee'] = $price['fee'];
                    $booking['discount'] = $price['discount'];
                    $booking['price'] = $price['total'];
                    $booking['updated'] = date('Y-m-d H:i:s');
                    $booking['updated_by'] = $user['id'];
                    DB::update(self::BOOKINGS_TABLE)
                        ->set($booking)
                        ->where('id', '=', $booking['id'])
                        ->execute();
                    $result['action'] = 'updated';
                }

                Database::instance()->commit();

                $payment = array();
                $payment['booking_id'] = $booking['id'];
                $minDeposit = Settings::instance()->get('propman_min_deposit');
                $payment['amount'] = $post['pay'] == 'deposit' ? $minDeposit : $booking['price'];
                $payment['status'] = 'Processing';
                $payment['created'] = $payment['updated'] = date('Y-m-d H:i:s');
                $payment['created_by'] = $payment['updated_by'] = $user['id'];
                $payment['deleted'] = 0;
                $result['price'] = $payment['amount'];

                if ($post['payment_select'] == 'realex') {
                    $payment['gateway'] = 'realex';
                    $paymentResult = DB::insert(self::PAYMENTS_TABLE, array_keys($payment))
                        ->values($payment)
                        ->execute();
                    $paymentId = $paymentResult[0];
                    $result['payment_id'] = $paymentId;

                    $realex = new Model_Realvault();
                    $realexOrderId = 'booking-' . $booking['id'] . '-' .
                        $booking['property_id'] . '-' .
                        str_pad($booking['guests'], '0', 2) . '-' .
                        str_replace('-', '', $booking['checkin']) . '-' .
                        str_replace('-', '', $booking['checkout']);
                    try {
                        $realexResult = $realex->charge(
                            $realexOrderId,
                            $payment['amount'],
                            'EUR',
                            $post['ccNum'],
                            $post['ccExpMM'] . $post['ccExpYY'],
                            $post['ccType'],
                            $post['firstName'] . ' ' . $post['lastName'],
                            $post['ccv']
                        );
                        if ((string)$realexResult->result == '00') {
                            DB::update(self::PAYMENTS_TABLE)
                                ->set(array(
                                    'gateway_tx' => (string)$realexResult->pasref,
                                    'status' => 'Paid',
                                    'updated' => date('Y-m-d H:i:s')
                                ))
                                ->where('id', '=', $paymentId)
                                ->execute();
                            $result['payment'] = 'done';
                            $result['redirect'] = Model_Payments::get_thank_you_page().'?booking_id='.$booking['id'];
                        } else {
                            DB::update(self::PAYMENTS_TABLE)
                                ->set(array(
                                    'gateway_tx' => 'error:' . $realexResult->message,
                                    'status' => 'Error',
                                    'updated' => date('Y-m-d H:i:s')
                                ))
                                ->where('id', '=', $paymentId)
                                ->execute();
                            DB::update(self::BOOKINGS_TABLE)
                                ->set(array('status' => 'Cancelled'))
                                ->where('id', '=', $booking['id'])
                                ->execute();
                            $result['payment'] = 'error:' . $realexResult->message;
                        }
                    } catch (Exception $exc) {
                        DB::update(self::PAYMENTS_TABLE)
                            ->set(array(
                                'gateway_tx' => 'error:' . $exc->getMessage(),
                                'status' => 'Error',
                                'updated' => date('Y-m-d H:i:s')
                            ))
                            ->where('id', '=', $paymentId)
                            ->execute();
                        DB::update(self::BOOKINGS_TABLE)
                            ->set(array('status' => 'Cancelled'))
                            ->where('id', '=', $booking['id'])
                            ->execute();
                        $result['payment'] = 'error:' . $exc->getMessage();
                    }
                } else if ($post['payment_select'] == 'paypal') {
                    $payment['gateway'] = 'paypal';
                    $paymentResult = DB::insert(self::PAYMENTS_TABLE, array_keys($payment))
                        ->values($payment)
                        ->execute();
                    $paymentId = $paymentResult[0];
                    $result['payment_id'] = $paymentId;
                    $result['payment'] = 'continue';
                } else {
                    $booking['gateway'] = 'error';
                    $result['error'] = true;
                    $result['message'] = __('Unexpected Payment Type');
                }
            } catch (Exception $exc) {
                Database::instance()->rollback();
                $result['error'] = true;
                $result['message'] = $exc->getMessage();
            }
        } else {
            $result['error'] = true;
            $result['message'] = __('No longer Available');
        }
        return $result;
    }

    public static function saveBalancePaymentFromPost($post)
    {
        $user = Auth::instance()->get_user();

        $result = array('error' => false);
        foreach ($post as $key => $value) {
            $post[$key] = trim(strip_tags($value));
        }

        $bookingId = $post['booking_id'];
        $firstname = $post['firstName'];
        $lastname = $post['lastName'];
        $email = $post['email'];
        $property = $post['property'];
        $checkin = $post['checkin'];
        $checkout = $post['checkout'];
        $balance = (float)$post['balance'];
        $custom = serialize(
            array(
                'booking_id' => $bookingId ? $bookingId : 'Not Set',
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'property' => $property,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'balance' => $balance,
            )
        );

        $payment = array();
        $payment['booking_id'] = $bookingId ? $bookingId : null;
        $payment['amount'] = $balance;
        $payment['status'] = 'Processing';
        $payment['created'] = $payment['updated'] = date('Y-m-d H:i:s');
        $payment['created_by'] = $payment['updated_by'] = $user['id'];
        $payment['deleted'] = 0;
        $payment['custom'] = $custom;
        $result['amount'] = $payment['amount'];

        if ($post['payment_select'] == 'realex') {
            $payment['gateway'] = 'realex';
            $paymentResult = DB::insert(self::PAYMENTS_TABLE, array_keys($payment))
                ->values($payment)
                ->execute();
            $paymentId = $paymentResult[0];
            $result['payment_id'] = $paymentId;

            $realex = new Model_Realvault();
            $realexOrderId = 'payment-' . $paymentId . '-' .
                preg_replace('/[^a-z0-9]+/i', '-', $property) . '-' .
                str_replace('-', '', $checkin) . '-' .
                str_replace('-', '', $checkout);
            try {
                $realexResult = $realex->charge(
                    $realexOrderId,
                    $payment['amount'],
                    'EUR',
                    $post['ccNum'],
                    $post['ccExpMM'] . $post['ccExpYY'],
                    $post['ccType'],
                    $firstname . ' ' . $lastname,
                    $post['ccv']
                );
                if ((string)$realexResult->result == '00') {
                    DB::update(self::PAYMENTS_TABLE)
                        ->set(array(
                            'gateway_tx' => (string)$realexResult->pasref,
                            'status' => 'Paid',
                            'updated' => date('Y-m-d H:i:s')
                        ))
                        ->where('id', '=', $paymentId)
                        ->execute();
                    $result['payment'] = 'done';
                    self::sendNotificationForCustomPayment($paymentId);
                } else {
                    DB::update(self::PAYMENTS_TABLE)
                        ->set(array(
                            'gateway_tx' => 'error:' . $realexResult->message,
                            'status' => 'Error',
                            'updated' => date('Y-m-d H:i:s')
                        ))
                        ->where('id', '=', $paymentId)
                        ->execute();
                    $result['payment'] = 'error:' . $realexResult->message;
                }
            } catch (Exception $exc) {
                DB::update(self::PAYMENTS_TABLE)
                    ->set(array(
                        'gateway_tx' => 'error:' . $exc->getMessage(),
                        'status' => 'Error',
                        'updated' => date('Y-m-d H:i:s')
                    ))
                    ->where('id', '=', $paymentId)
                    ->execute();
                $result['payment'] = 'error:' . $exc->getMessage();
            }
        } else if ($post['payment_select'] == 'paypal') {
            $payment['gateway'] = 'paypal';
            $paymentResult = DB::insert(self::PAYMENTS_TABLE, array_keys($payment))
                ->values($payment)
                ->execute();
            $paymentId = $paymentResult[0];
            $result['payment_id'] = $paymentId;
            $result['payment'] = 'continue';
        } else {
            $result['error'] = true;
            $result['message'] = __('Unexpected Payment Type');
        }

        return $result;
    }

    public static function sendNotificationForCustomPayment($paymentId)
    {
        $payment = self::paymentGet($paymentId);
        if ($payment) {
            $params = unserialize($payment['custom']);
            $params['customer'] = $params['firstname'] . ' ' . $params['lastname'];
            $params['time'] = date('Y-m-d H:i:s');
            $params['amount'] = '&euro' . $payment['amount'];
            $messaging = new Model_Messaging();
            $messaging->send_template(
                'booking-balance-payment-admin',
                null,
                date('Y-m-d H:i:s'),
                array(),
                $params
            );

            $messaging->send_template(
                'booking-balance-payment-customer',
                null,
                date('Y-m-d H:i:s'),
                array(array('target_type' => 'EMAIL', 'target' => $params['email'])),
                $params
            );
        }
    }

    public static function paypalComplete($ipn)
    {
        $paymentId = $ipn['invoice'];
        $payment = DB::select('*')
            ->from(self::PAYMENTS_TABLE)
            ->where('id', '=', $paymentId)
            ->execute()
            ->current();
        if ($payment) {
            $booking = DB::select('*')
                ->from(self::BOOKINGS_TABLE)
                ->where('id', '=', $payment['booking_id'])
                ->execute()
                ->current();
            $payment['gateway_tx'] = $ipn['txn_id'];
            if ($ipn['payment_status'] == 'Completed') {
                $payment['status'] = 'Paid';
            } else {
                if ($booking) {
                    DB::update(self::BOOKINGS_TABLE)
                        ->set(array('status' => 'Cancelled'))
                        ->where('id', '=', $booking['id'])
                        ->execute();
                }
            }
            DB::update(self::PAYMENTS_TABLE)
                ->set($payment)
                ->where('id', '=', $paymentId)
                ->execute();
            if ($payment['status'] == 'Paid') {
                if ($payment['custom'] != '') {
                    self::sendNotificationForCustomPayment($paymentId);
                }
                return $payment;
            }
        }
        return false;
    }

    public static function bookingslist($params = array())
    {
        $query = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS bookings.*'),
            DB::expr("CONCAT(contacts.first_name, ' ', contacts.last_name) AS contact"),
            'contacts.email',
            array('properties.name', 'property')
        )
            ->from(array(self::BOOKINGS_TABLE, 'bookings'))
                ->join(array('plugin_contacts_contact', 'contacts'), 'inner')
                    ->on('bookings.customer_id', '=', 'contacts.id')
                ->join(array(self::PROPERTIES_TABLE, 'properties'), 'inner')
                    ->on('bookings.property_id', '=', 'properties.id')
            ->where('bookings.deleted', '=', 0)
            ->order_by('bookings.checkin', 'desc')
            ->order_by('bookings.updated', 'desc');
        $result['records'] = $query->execute()->as_array();
        $result['total'] = DB::select(DB::expr('FOUND_ROWS() AS total'))->execute()->get('total');
        return $result;
    }

    public static function bookingGet($id)
    {
        $booking = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS bookings.*'),
            DB::expr("CONCAT(contacts.first_name, ' ', contacts.last_name) AS contact"),
            'contacts.email',
            array('properties.name', 'property')
        )
            ->from(array(self::BOOKINGS_TABLE, 'bookings'))
                ->join(array('plugin_contacts_contact', 'contacts'), 'inner')
                    ->on('bookings.customer_id', '=', 'contacts.id')
                ->join(array(self::PROPERTIES_TABLE, 'properties'), 'inner')
                    ->on('bookings.property_id', '=', 'properties.id')
            ->where('bookings.deleted', '=', 0)
            ->and_where('bookings.id', '=', $id)
            ->execute()
            ->current();
        $booking['contact'] = DB::select('*')
            ->from(Model_Contacts::TABLE_CONTACT)
            ->where('id', '=', $booking['customer_id'])
            ->execute()
            ->current();
        $booking['payments'] = DB::select('*')
            ->from(self::PAYMENTS_TABLE)
            ->where('booking_id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->order_by('updated', 'desc')
            ->execute()
            ->as_array();
        return $booking;
    }

    public static function bookingsListOutstanding($params = array())
    {
        $query = DB::select(
            DB::expr('bookings.*'),
            DB::expr("CONCAT(contacts.first_name, ' ', contacts.last_name) AS contact"),
            'contacts.email',
            array('properties.name', 'property'),
            array('properties.url', 'property_url'),
            array('properties.ref_code', 'property_ref_code'),
            DB::expr('IFNULL(SUM(payments.amount), 0) AS paid')
        )
            ->from(array(self::BOOKINGS_TABLE, 'bookings'))
                ->join(array('plugin_contacts_contact', 'contacts'), 'inner')
                    ->on('bookings.customer_id', '=', 'contacts.id')
                ->join(array(self::PROPERTIES_TABLE, 'properties'), 'inner')
                    ->on('bookings.property_id', '=', 'properties.id')
                ->join(array(self::PAYMENTS_TABLE, 'payments'), 'left')
                    ->on('bookings.id', '=', 'payments.booking_id')
                    ->on('payments.deleted', '=', DB::expr(0))
                    ->on('payments.status', '=', DB::expr("'Paid'"))
            ->where('bookings.deleted', '=', 0);

        if (isset($params['checkin'])) {
            $query->and_where('bookings.checkin', '=', $params['checkin']);
        }
        $bookings = $query->having(DB::expr('price - paid'), '>', 0)
            ->group_by('bookings.id')
            ->order_by('bookings.checkin', 'asc')
            ->execute()
            ->as_array();
        return $bookings;
    }

    public static function bookingsOutstandingReminder()
    {
        $bookings = self::bookingsListOutstanding(array('checkin' => date('Y-m-d', strtotime("+8week"))));
        $project_name = Settings::instance()->get('project_name');
        foreach ($bookings as $booking) {
            $booking['outstanding'] = '&euro ' . $booking['price'] - $booking['paid'];
            $booking['time'] = date('Y-m-d H:i:s');
            $booking['property_name'] = $booking['property'];
            $booking['property_url'] = URL::site() . 'property-details.html/' . $booking['property_url'];
            unset($booking['property']);
            $booking['project_name'] = $project_name;
            $messaging = new Model_Messaging();
            $messaging->send_template(
                'outstanding-bookings-8w-reminder',
                null,
                date('Y-m-d H:i:s'),
                array(
                    array('target_type' => 'CMS_CONTACT', 'target' => $booking['customer_id'])
                ),
                $booking
            );
        }
        return $bookings;
    }

    public static function paymentslist($params = array())
    {
        $query = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS payments.*'),
            'bookings.checkin',
            'bookings.checkout',
            'bookings.customer_id',
            DB::expr("CONCAT(contacts.first_name, ' ', contacts.last_name) AS contact"),
            'contacts.email',
            array('properties.name', 'property')
        )
            ->from(array(self::PAYMENTS_TABLE, 'payments'))
                ->join(array(self::BOOKINGS_TABLE, 'bookings'), 'left')
                    ->on('payments.booking_id', '=', 'bookings.id')
                ->join(array('plugin_contacts_contact', 'contacts'), 'left')
                    ->on('bookings.customer_id', '=', 'contacts.id')
                ->join(array(self::PROPERTIES_TABLE, 'properties'), 'left')
                    ->on('bookings.property_id', '=', 'properties.id')
            ->where('payments.deleted', '=', 0)
            ->order_by('payments.updated', 'desc');
        $result['records'] = $query->execute()->as_array();
        $result['total'] = DB::select(DB::expr('FOUND_ROWS() AS total'))->execute()->get('total');
        return $result;
    }


    public static function bookingCancelIfUnpaid($timeoutMinutes = 10)
    {
        if ($timeoutMinutes > 0) {
            DB::query(null, 'UPDATE plugin_propman_bookings
	INNER JOIN
(SELECT b.id AS booking_id, SUM(p.amount) AS \'Paid\'
	FROM plugin_propman_bookings b
		LEFT JOIN plugin_propman_bookings_payments p ON b.id = p.booking_id AND p.`status` = \'Paid\'
	WHERE b.status <> \'Cancelled\' AND b.created <= DATE_SUB(NOW(),INTERVAL 10 MINUTE)
	GROUP BY b.id
	HAVING `Paid` IS NULL) unpaid ON plugin_propman_bookings.id = unpaid.booking_id
		SET plugin_propman_bookings.status = \'Cancelled\'
	WHERE
	  plugin_propman_bookings.status <> \'Cancelled\' AND
	  plugin_propman_bookings.created <= DATE_SUB(NOW(),INTERVAL ' . $timeoutMinutes . ' MINUTE)')->execute();
        }
    }

    public static function bookingSetStatus($bookingId, $status)
    {
        DB::update(self::BOOKINGS_TABLE)
            ->set(array('status' => $status, 'updated' => date('Y-m-d H:i:s')))
            ->where('id', '=', $bookingId)
            ->execute();
    }

    public static function paymentGet($id)
    {
        $payment = DB::select(
            'payments.*',
            'bookings.checkin',
            'bookings.checkout',
            'bookings.customer_id',
            DB::expr("CONCAT(contacts.first_name, ' ', contacts.last_name) AS contact"),
            'contacts.email',
            array('properties.name', 'property')
        )
            ->from(array(self::PAYMENTS_TABLE, 'payments'))
                ->join(array(self::BOOKINGS_TABLE, 'bookings'), 'left')
                    ->on('payments.booking_id', '=', 'bookings.id')
                ->join(array('plugin_contacts_contact', 'contacts'), 'left')
                    ->on('bookings.customer_id', '=', 'contacts.id')
                ->join(array(self::PROPERTIES_TABLE, 'properties'), 'left')
                    ->on('bookings.property_id', '=', 'properties.id')
            ->where('payments.id', '=', $id)

            ->order_by('payments.updated', 'desc')
            ->execute()
            ->current();
        return $payment;
    }

    public static function bookingLinkPayment($bookingId, $paymentId)
    {
        $user = Auth::instance()->get_user();
        DB::update(self::PAYMENTS_TABLE)
            ->set(array(
                'booking_id' => $bookingId,
                'updated_by' => $user['id'],
                'updated' => date('Y-m-d H:i:s')
            ))
            ->where('id', '=', $paymentId)
            ->execute();
    }

    public static function paymentslistUnlinked()
    {
        $payments = DB::select('*')
            ->from(array(self::PAYMENTS_TABLE, 'payments'))
            ->where('payments.deleted', '=', 0)
            ->and_where('booking_id', 'is', null)
            ->order_by('payments.updated', 'desc')
            ->execute()
            ->as_array();
        return $payments;
    }

    public static function getBookedDays($propertyId)
    {
        self::bookingCancelIfUnpaid();
        $bookings = DB::select('*')
            ->from(self::BOOKINGS_TABLE)
            ->where('property_id', '=', $propertyId)
            ->and_where('deleted', '=', 0)
            ->and_where('status', '<>', 'Cancelled')
            ->order_by('checkin')
            ->execute()
            ->as_array();
        $days = array();
        foreach ($bookings as $booking){
            $bdays = self::getDaysInDateRange($booking['checkin'], $booking['checkout']);
            array_pop($bdays['days']); // last day is checkout day. it can be used for checkin
            foreach ($bdays['days'] as $day){
                $days[] = $day;
            }
        }
        return $days;
    }

    public static function bookingGetBalance($bookingId)
    {
        $booking = self::bookingGet($bookingId);
        if ($booking) {
            $paid = 0;
            foreach ($booking['payments'] as $payment) {
                if ($payment['status'] == 'Paid') {
                    $paid += $payment['amount'];
                }
            }
            $data = array();
            $data['first_name'] = $booking['contact']['first_name'];
            $data['last_name'] = $booking['contact']['last_name'];
            $data['email'] = $booking['contact']['email'];
            $data['property'] = $booking['property'];
            $data['checkin'] = $booking['checkin'];
            $data['checkout'] = $booking['checkout'];
            $data['price'] = $booking['price'];
            $data['paid'] = $paid;
            $data['balance'] = $data['price'] - $data['paid'];

            return $data;
        } else {
            return false;
        }
    }

    public static function fixRatecardCalendar()
    {
        try {
            Database::instance()->begin();
            DB::delete(self::RATECARDS_CALENDAR_TABLE)->execute();
            $ranges = DB::select('*')
                ->from(self::RATECARDS_DATERANGES_TABLE)
                ->order_by('ratecard_id')
                ->order_by('starts')
                ->execute()
                ->as_array();
            foreach ($ranges as $range) {
                $days = self::getDaysInDateRange($range['starts'], $range['ends']);
                array_pop($days['days']);
                foreach ($days['days'] as $day) {
                    DB::insert(self::RATECARDS_CALENDAR_TABLE, array('ratecard_id', 'range_id', 'date'))
                        ->values(array($range['ratecard_id'], $range['id'], $day))
                        ->execute();
                }
            }
            Database::instance()->commit();
            return true;
        } catch (Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }

    }

    public static function saveIPNLog()
    {
        DB::insert(self::IPNLOGS_TABLE)
            ->values(
                array(
                    'get' => json_encode(IbHelpers::iconv_array($_GET)),
                    'post' => json_encode(IbHelpers::iconv_array($_POST)),
                    'cookies' => json_encode(IbHelpers::iconv_array($_COOKIE)),
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'time' => date('Y-m-d H:i:s')
                )
            )->execute();
    }
}