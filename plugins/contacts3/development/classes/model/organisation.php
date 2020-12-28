<?php


final class Model_Organisation extends Model
{
    const CONTACT_ORGANISATION_TABLE = 'plugin_contacts3_organisations';
    const CONTACT_ORGANISATION_SIZES = 'plugin_contacts3_organisation_sizes';
    const CONTACT_ORGANISATION_INDUSTRIES = 'plugin_contacts3_organisation_industries';
    
    private $id = null;
    private $contact = null;
    private $contact_id = null;
    private $primary_biller_id         = null;
    private $primary_biller            = null;
    private $organisation_size_id      = null;
    private $organisation_size = array();
    private $organisation_industry_id = null;
    private $organisation_industry = array();
    
    function __construct($id = null)
    {
        $this->set_id($id);
        $this->get();
    }
    
    public function get()
    {
        $data = $this->_sql_get_organisation();
        $data['organisation_size'] = $this->_sql_get_organisation_size();
        $data['organisation_industry'] = $this->_sql_get_organisation_industry();
        $this->load($data);
    }
    
    public static function get_org_by_contact_id($contact_id) {
        $query = DB::select()->from(self::CONTACT_ORGANISATION_TABLE)->where('contact_id', '=', $contact_id)->execute()->current();
        $org_contact = New Model_Organisation($query['id']);
        $org_contact->set_contact_id($contact_id);
        return $org_contact;
    }

    public static function get_organization_by_primary_biller_id($primary_biller_id)
    {
        $organization = DB::select('*')
            ->from(self::CONTACT_ORGANISATION_TABLE)
            ->where('primary_biller_id', '=', $primary_biller_id)
            ->execute()
            ->current();
        return $organization;
    }
    
    public function load($data)
    {
        foreach ($data AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = ($value == '') ? null : $value;
            }
        }
        $this->contact  = new Model_Contacts3($this->contact_id);
        $this->primary_biller = new Model_Contacts3($this->primary_biller_id);
    }
    
    public function save($save_contact = false) {
        Database::instance()->begin();
        try {
            if ($this->id) {
                $this->_sql_update_organisation();
            } else {
                $this->_sql_save_organisation();
            }
            if (get_class($this->contact) == 'Model_Contacts3' && $save_contact) {
                $this->contact->save();
            }
            Database::instance()->commit();
        } catch (Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }
    }
    
    public function get_id()
    {
        return $this->id;
    }
    
    public function set_id($id)
    {
        $this->id = $id;
    }
    
    public function get_contact()
    {
        return $this->contact;
    }
    
    public function get_contact_id()
    {
        return $this->contact_id;
    }
    
    public function set_contact_id($contact_id)
    {
        $this->contact_id = $contact_id;
        $this->contact = new Model_Contacts3($contact_id);
    }
    
    public function get_primary_biller_id()
    {
        return $this->primary_biller_id;
    }
    
    public function set_primary_biller_id($primary_biller_id)
    {
        $this->primary_biller_id = $primary_biller_id;
        $this->primary_biller = new Model_Contacts3($primary_biller_id);
    }
    
    public function get_primary_biller()
    {
        return $this->primary_biller;
    }
    public function get_organisation_size_id()
    {
        return $this->organisation_size_id;
    }
    
    public function set_organisation_size_id($organisation_size_id)
    {
        $this->organisation_size_id = $organisation_size_id;
    }
    
    public function get_organisation_size()
    {
        return $this->organisation_size;
    }
    
    public function set_organisation_size($organisation_size)
    {
        $this->organisation_size_id = $organisation_size;
    }
    
    public function get_organisation_industry_id()
    {
        return $this->organisation_industry_id;
    }
    
    public function set_organisation_industry_id($organisation_industry_id)
    {
        $this->organisation_industry_id = $organisation_industry_id;
    }
    
    public function get_organisation_industry()
    {
        return $this->organisation_industry;
    }
    
    public function set_organisation_industry($organisation_industry)
    {
        $this->organisation_industry_id = $organisation_industry;
    }
    
    private function _sql_get_organisation()
    {
        return DB::select()->from(self::CONTACT_ORGANISATION_TABLE)->where('id', '=', $this->id)->execute()->current();
    }
    
    private function _sql_update_organisation()
    {
        return DB::update(self::CONTACT_ORGANISATION_TABLE)->set(
            array('contact_id' => $this->contact_id, 'organisation_size_id' => $this->organisation_size_id,
                'organisation_industry_id' => $this->organisation_industry_id, 'primary_biller_id' => $this->primary_biller_id))
            ->where('id', '=', $this->id)->execute();
    }
    
    private function _sql_save_organisation()
    {
        return DB::insert(self::CONTACT_ORGANISATION_TABLE)->values(
            array(
                'contact_id' => $this->contact_id,
                'organisation_size_id' => $this->organisation_size_id,
                'organisation_industry_id' => $this->organisation_industry_id,
                'primary_biller_id' => $this->primary_biller_id))->execute();
    }
    
    private function _sql_get_organisation_size()
    {
        return DB::select()->from(self::CONTACT_ORGANISATION_SIZES)->where('id', '=', $this->organisation_size_id)->execute()->current();
    }
    
    private function _sql_get_organisation_industry()
    {
        return DB::select()->from(self::CONTACT_ORGANISATION_INDUSTRIES)->where('id', '=',
            $this->organisation_size_id)->execute()->current();
    }
    
    public static function get_organisation_industries()
    {
        return DB::select('*')
            ->from(self::CONTACT_ORGANISATION_INDUSTRIES)
            ->execute()
            ->as_array();
    }
    
    public static function get_organisation_sizes($find_all = false)
    {
        $query = DB::select('*')
            ->from(self::CONTACT_ORGANISATION_SIZES);
        if(!$find_all) {
            $query->where('publish', '=', 1);
        }
        return $query->order_by('order')->execute()->as_array();
    }

    public static function get_organization_industry($id)
    {
        return DB::select('*')
            ->from(self::CONTACT_ORGANISATION_INDUSTRIES)
            ->where('id', '=', $id)
            ->execute()
            ->current();
    }

    public static function get_organization_size($id)
    {
        return DB::select('*')
            ->from(self::CONTACT_ORGANISATION_SIZES)
            ->where('id', '=', $id)
            ->execute()
            ->current();
    }
}
?>