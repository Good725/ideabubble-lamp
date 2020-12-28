<?php
final class Model_Alerts extends Model implements Interface_Contacts3
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

    private $alert_id   = NULL;
    private $label      = '';
    private $default    = '';
    private $delete     = 0;
    private $publish    = 1;

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
        $data = $this->_sql_get_alert();

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
            if(is_numeric($this->alert_id))
            {
                DB::update(self::ADDRESS_TABLE)->set($this->get_instance())->where('alert_id','=',$this->alert_id)->execute();
            }
            else
            {
                $q = DB::insert(self::ADDRESS_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute()->as_array();
                $this->set_alert_id($q[0]);
            }
        }

        return $ok;
    }

    public function set_alert_id($id)
    {
        $this->alert_id = is_numeric($id) AND $id > 0 ? (int) $id : NULL;
    }

    public function set_publish($publish)
    {
        $this->publish = $publish === 0 ? 0 : 1;
    }

    public function set_delete($delete)
    {
        $this->delete = $delete === 1 ? 1 : 0;
    }

    public function get_instance()
    {
        return array('alert_id' => $this->alert_id,
                     'label'    => $this->label,
                     'default'  => $this->default,
                     'delete'   => $this->delete,
                     'publish'  => $this->publish
        );
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
}
?>