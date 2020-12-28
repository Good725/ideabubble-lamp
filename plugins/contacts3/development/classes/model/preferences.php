<?php
final class Model_Preferences extends Model implements Interface_Contacts3
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

    private $preference_id = NULL;
    private $label = NULL;
    private $default = 0;
    private $publish = 1;
    private $delete = 0;
    private $id;
    private $stub;

    public function load($data)
    {
        foreach($data AS $key=>$value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    public function get($autoload = FALSE)
    {
        $data = $this->_sql_get_preference();

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
            if(is_numeric($this->preference_id))
            {
                DB::update(self::ADDRESS_TABLE)->set($this->get_instance())->where('alert_id','=',$this->preference_id)->execute();
            }
            else
            {
                $q = DB::insert(self::ADDRESS_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute()->as_array();
                $this->set_preference_id($q[0]);
            }
        }

        return $ok;
    }

    public function get_instance()
    {
        return array('preference_id'    => $this->preference_id,
                     'label'            => $this->label,
                     'default'          => $this->default,
                     'publish'          => $this->publish,
                     'delete'           => $this->delete
        );
    }

    public function set_preference_id($id)
    {
        $this->preference_id = is_numeric($id) AND $id > 0 ? (int) $id : NULL;
    }

    public function set_publish($publish)
    {
        $this->publish = $publish === 0 ? 0 : 1;
    }

    public function set_delete($delete)
    {
        $this->delete = $delete === 1 ? 1: 0;
    }

    public static function get_all_preferences()
    {
        return DB::select('id', 'label', 'stub', 'group','required', 'summary')->from(self::PREFERENCES_TABLE)
            ->where('publish', '=', 1)->where('deleted', '=', 0)->order_by('group')->execute()->as_array();
    }

    public static function get_all_preferences_grouped()
    {
        $all_preferences = self::get_all_preferences();
        $result = array();
        foreach($all_preferences as $preference){
            $result[$preference['group']][] = $preference;
        }

        return $result;
    }

    private function _sql_get_preference()
    {
        $q = DB::select()->from(self::PREFERENCES_TABLE);

        if ( ! empty($this->id))   $q->where('id', '=', $this->id);
        if ( ! empty($this->stub)) $q->where('stub', '=', $this->stub);

        $q = $q
            ->where('publish', '=', 1)
            ->where('deleted', '=', 0)
            ->execute()
            ->as_array();

        return count($q) > 0 ? $q[0] : array();
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

    public static function get_by_id($preference_id) {
        return DB::select('id', 'label', 'stub', 'group', 'required')
            ->from(self::PREFERENCES_TABLE)->where('id', '=',
                $preference_id)->execute()->current();
    }
    public static function get_stub_id($stub) {
        return DB::select('id')
            ->from(self::PREFERENCES_TABLE)
            ->where('stub', '=', $stub)->execute()->get('id');
    }
    public static function get_family_preferences()
    {
        $pref = DB::select('id', 'label', 'stub','required')->from(self::PREFERENCES_TABLE)
            ->where('group', '=', "family_permission")->where('deleted', '=', 0)->order_by('group')->execute()->as_array();

        return $pref;
    }

    public static function save_contact_privilege($priv_stub_array, $contact_id)
    {

     
        $delete_query = "delete " . self::CONTACT_PREFERENCES_RELATION_TABLE . " from " . self::CONTACT_PREFERENCES_RELATION_TABLE . " join " . self::PREFERENCES_TABLE
            . " on " . self::CONTACT_PREFERENCES_RELATION_TABLE . '.preference_id' . "=" . self::PREFERENCES_TABLE . '.id' .
            " and " . self::PREFERENCES_TABLE . '.group' . "=" . '"family_permission"' .
            " where " . self::CONTACT_PREFERENCES_RELATION_TABLE . '.contact_id' . " = :contact_id";

        $delete_query = DB::query(Database::DELETE, $delete_query);
        $delete_query->param(':contact_id', $contact_id);
        $delete_query->execute();

        if(!empty($priv_stub_array))
        {
            $priv_ref_array = DB::select("id")->from(self::PREFERENCES_TABLE)->where('stub','IN',$priv_stub_array)->and_where('group','=','family_permission')->execute()->as_array();


            $insert_query = DB::insert(self::CONTACT_PREFERENCES_RELATION_TABLE, array('contact_id','preference_id','value'));
            foreach ($priv_ref_array as $pref)
            {

                $insert_query->values(array($contact_id,$pref['id'],1));
            }

            $insert_query->execute();

        }

    }

    public static function contact_default_preferences_settings($selected_mode = null)
    {
        $preferences =  ORM::factory('Contacts3_Preference')->where('group', '=', 'contact')->find_all()->as_array('id', 'label');
        return html::optionsFromArray($preferences, $selected_mode);
    }
}
?>