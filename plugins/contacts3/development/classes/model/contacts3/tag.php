<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Tag extends Model
{
    const CONTACT_TAG_TABLE = 'plugin_contacts3_tags';
    const CONTACT_HAS_TAG_TABLE = 'plugin_contacts3_contact_has_tags';
    
    private $id = null;
    private $label = null;
    private $name = null;
    private $publish = 1;
    private $delete = 0;
    
    function __construct($id = null)
    {
        if (is_array($id)) {
            $args = $id;
            $search = self::find_tag($id);
            $id = isset($search['id']) ? $search['id'] : null;
        }

        $this->set_id($id);
        $this->get();
    }
    
    public function get_id()
    {
        return $this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_label()
    {
        return $this->label;
    }

    public function set_label($label)
    {
        $this->label = $label;
        $this->name = str_replace(" ", "_", strtolower($label));
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_publish(): int
    {
        return $this->publish;
    }

    public function set_publish(int $publish)
    {
        $this->publish = $publish;
    }

    public function get_delete(): int
    {
        return $this->delete;
    }

    public function set_delete(int $delete)
    {
        $this->delete = $delete;
    }
    
    public function get()
    {
        $data = $this->_sql_get_tag();
        if ($data) {
            $this->load($data);
        }
    }

    public static function find_tag($args = [])
    {
        $q =  DB::select()->from(self::CONTACT_TAG_TABLE)->where('delete', '=', 0);

        foreach ($args as $key => $value) {
            $q->where($key, '=', $value);
        }

        return $q->execute()->current();
    }

    public static function get_all()
    {
        return DB::select()->from(self::CONTACT_TAG_TABLE)->where('delete', '=', 0)->order_by('label')->execute();
    }
    
    public function load($data)
    {
        foreach ($data AS $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = ($value == '') ? null : $value;
            }
        }
    }
    
    public function save()
    {
        Database::instance()->begin();
        try {
            if ($this->id) {
                $this->_sql_update_tag();
            } else {
                $this->_sql_save_tag();
            }
            Database::instance()->commit();
        } catch (Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }
    }
    
    public static function get_tag_by_name($name)
    {
       $tag = DB::select('id')->from(self::CONTACT_TAG_TABLE)->where('name', '=', $name)->execute()->current();
       if(isset($tag)) {
           return new self($tag['id']);
       } else {
           return null;
       }
       
    }
    
    private function _sql_get_tag()
    {
        return DB::select()->from(self::CONTACT_TAG_TABLE)->where('id', '=', $this->id)->execute()->current();
    }
    
    private function _sql_save_tag()
    {
        $insert = DB::insert(self::CONTACT_TAG_TABLE)->values(
            array(
                'id' => $this->id,
                'label' => $this->label,
                'name' => $this->name,
                'publish' => $this->publish,
                'delete' => $this->delete,
            ))->execute();

        // Set the tag ID to the ID of the newly created record
        $this->id = $insert[0];
    }
    
    private function _sql_update_tag()
    {
        return DB::update(self::CONTACT_TAG_TABLE)->set(
            array(
                'id' => $this->id,
                'label' => $this->label,
                'name' => $this->name,
                'publish' => $this->publish,
                'delete' => $this->delete,
            ))
            ->where('id', '=', $this->id)->execute();
    }
}