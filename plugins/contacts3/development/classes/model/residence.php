<?php
/**
 * Class Model_Residence
 * This class is that which acts as a CRUD wrapper for the addresses of all contacts in this plugin.
 */
final class Model_Residence extends Model implements Interface_Contacts3
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

    /**
     ** ----- PRIVATE MEMBER DATA -----
     **/

    private $address_id  = NULL;
    private $address1    = '';
    private $address2    = '';
    private $address3    = '';
    private $country     = NULL;
    private $county      = NULL;
    private $postcode    = '';
    private $town        = '';
    private $coordinates = '';
    private $address_type = '';
    private $publish     = 1;
    private $delete      = 0;

    function __construct($id = NULL)
    {
        if(is_numeric($id))
        {
            $this->set_address_id($id);
        }

        $this->get(true);
    }

    public function load($data)
    {
        foreach($data AS $key=>$value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $value;
            }
        }
    }

    public function get($autoload = FALSE)
    {
        $data = $this->_sql_get_residence();

        if($autoload)
        {
            $this->load($data);
        }

        if (array_key_exists('country', $data) && $data['country'] == null) {
            $data['country'] = 'IE';
        }
        return $data;
    }

    public function save()
    {
        $ok = $this->validate();
        if($ok)
        {
            if(is_numeric($this->address_id))
            {
                DB::update(self::ADDRESS_TABLE)->set($this->get_instance())->where('address_id','=',$this->address_id)->execute();
            }
            else
            {
                $q = DB::insert(self::ADDRESS_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
                $this->set_address_id($q[0]);
            }
        }
        else
        {
            throw new Exception("Failed on Validation");
        }

        return $this->address_id;
    }

    public function get_instance()
    {
        if ($this->country == null) {
            $this->country = 'IE';
        }
        return array(
            'address_id'  => $this->address_id,
            'address1'    => $this->address1,
            'address2'    => $this->address2,
            'address3'    => $this->address3,
            'country'     => $this->country,
            'county'      => $this->county,
            'postcode'    => $this->postcode,
            'town'        => $this->town,
            'coordinates' => $this->coordinates,
            'address_type' => $this->address_type,
            'publish'     => $this->publish,
            'delete'      => $this->delete
        );
    }

    public function get_address_id()
    {
        return $this->address_id;
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

    public function get_town()
    {
        return $this->town;
    }

    public function get_coordinates()
    {
        return $this->coordinates;
    }

    public function get_address_type()
    {
        return $this->address_type;
    }

    public function get_country()
    {
        return $this->country;
    }

    public function get_county()
    {
        return $this->county;
    }

    public function get_county_name()
    {
        $counties = DB::select('name')->from('engine_counties')
            ->where('publish', '=', 1)
            ->and_where('deleted', '=', 0)
            ->and_where('id','=', $this->county)
            ->execute()->as_array();
        if (empty($counties)) {
            $counties = DB::select('name')->from('plugin_courses_counties')
                ->where('publish', '=', 1)
                ->and_where('delete', '=', 0)
                ->and_where('id','=', $this->county)
                ->execute()->as_array();
        }
        return $counties[0]['name'];
    }

    public function get_county_code() {
        $counties = DB::select('code')->from('engine_counties')
            ->where('publish', '=', 1)
            ->and_where('deleted', '=', 0)
            ->and_where('id','=', $this->county)
            ->execute()->as_array();
        if (empty($counties)) {
            $counties = DB::select('code')->from('plugin_courses_counties')
                ->where('publish', '=', 1)
                ->and_where('delete', '=', 0)
                ->and_where('id','=', $this->county)
                ->execute()->as_array();
        }
        return $counties[0]['code'];
    }

    public function get_postcode()
    {
        return $this->postcode;
    }

    public function set_postcode($postcode) {
        $this->postcode = $postcode;
    }

    public function set_address_id($id)
    {
        $this->address_id = (is_numeric($id) AND $id > 0) ? (int) $id : NULL;
    }

    public function set_publish($publish)
    {
        $this->publish = $publish === 0 ? 0 : 1;
    }

    public function set_delete($delete)
    {
        $this->delete = $delete === 1 ? 1 : 0;
    }

    public static function get_all_countries()
    {
        return DB::select('code', 'name')->from('countries')
            ->where('publish', '=', 1)->and_where('deleted', '=', 0)
            ->order_by(DB::expr('CASE WHEN `code`=\'IE\' THEN 0 ELSE 1 END, `name`'))
            ->execute()->as_array();
    }

    public static function get_all_counties($table = 'engine_counties')
    {
        $query = DB::select('id', 'name')->from($table)
            ->where('publish', '=', 1);
        if ($table == 'engine_counties') {
            $query->and_where('deleted', '=', 0);
        } else {
            $query->and_where('delete', '=', 0);
        }
        return $query->order_by('name')
            ->execute()->as_array();
    }

    public static function get_county_id($county, $table = 'engine_counties', $field = 'name')
    {
        if (empty($table)) {
            $table = 'engine_counties';
        }
        $counties_query = DB::select('id', 'name')->from($table)
            ->where('publish', '=', 1);
        if ($field != 'code') {
            $field = 'name';
        }
        $counties_query->and_where($field,'=', $county);
        if ($table == 'plugin_courses_counties') {
            $counties_query->and_where('delete', '=', 0);
        } else {
            $counties_query->and_where('deleted', '=', 0);
        }
        $counties_query->order_by('name');
        $counties = $counties_query->execute()->as_array();
        $county_id = empty($counties[0]['id']) ? 0: $counties[0]['id'];
        return $county_id;
    }

    public static function get_county_by_id($county_id)
    {
        return DB::select()->from('engine_counties')
            ->where('publish', '=', 1)
            ->and_where('deleted', '=', 0)
            ->and_where('id','=',$county_id)
            ->order_by('name')
            ->execute()->current();
    }

    public function delete()
    {
        $this->set_publish(0);
        $this->set_delete(1);
        $this->save();
    }

    public function validate()
    {
        return TRUE;
    }

    private function _sql_get_residence()
    {
        $q = DB::select('address_id','address1','address2','address3','country','county','postcode','town','coordinates','address_type', 'publish','delete')->from(self::ADDRESS_TABLE)->where('address_id','=',$this->address_id)->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    public function insert_residence($data)
    {
        if (!isset($data['address_type'])) {
            $data['address_type'] = 'Personal';
        }
        $inserted = DB::insert(self::ADDRESS_TABLE)
            ->values($data)
            ->execute();
        return $inserted[0];
    }
}
?>