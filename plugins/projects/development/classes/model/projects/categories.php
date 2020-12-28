<?php

final class Model_Projects_Categories extends Model
{
    const MAIN_TABLE        = 'plugin_projects_categories';
    private $id             = 'new';
    private $name           = '';
    private $parent         = '';
    private $summary        = '';
    private $description    = '';
    private $order          = 0 ;
    private $image          = '';
    private $publish        = 1 ;
    private $deleted        = 0 ;
    private $columns        = array('id','name','parent','summary','description','order','image','publish','deleted');

    public function __construct($id = 'new')
    {
        if(isset($id) AND is_numeric($id))
        {
            $this->set_id($id);
            $this->load($this->_sql_get_category());
        }
    }

    /* PUBLIC FUNCTIONS */

    public function get()
    {
        return array('id'           => $this->id,
                     'name'         => $this->name,
                     'parent'       => $this->parent,
                     'summary'      => $this->summary,
                     'description'  => $this->description,
                     'order'        => $this->order,
                     'image'        => $this->image,
                     'publish'      => $this->publish,
                     'deleted'      => $this->deleted
        );
    }

    public function load($data)
    {
        $this->id           = (isset($data['id']))          ? $data['id']           : $this->id;
        $this->name         = (isset($data['name']))        ? $data['name']         : $this->name;
        $this->parent       = (isset($data['parent']))      ? $data['parent']       : $this->parent;
        $this->summary      = (isset($data['summary']))     ? $data['summary']      : $this->summary;
        $this->description  = (isset($data['description'])) ? $data['description']  : $this->description;
        $this->order        = (isset($data['order']))       ? $data['order']        : $this->order;
        $this->image        = (isset($data['image']))       ? $data['image']        : $this->image;
        $this->publish      = (isset($data['publish']))     ? $data['publish']      : $this->publish;
        $this->deleted      = (isset($data['deleted']))     ? $data['deleted']      : $this->deleted;
    }

    public function save()
    {
        if($this->id == 'new')
        {
            $this->_sql_save_category();
        }
        else
        {
            $this->_sql_update_category();
        }
    }

    public function delete()
    {
        $this->deleted = 1;
        $this->save();
    }

    public function set_id($id)
    {
        if(is_numeric($id))
        {
            $this->id = (int) $id;
        }
    }

    public function set_name($name)
    {
        if(is_string($name))
        {
            $this->name = trim($name);
        }
    }

    public function set_parent($parent)
    {
        if(is_numeric($parent) AND $parent > 0)
        {
            $this->parent = (int) $parent;
        }
    }

    public function set_summary($summary)
    {
        if(is_string($summary))
        {
            $this->summary = trim($summary);
        }
    }

    public function set_description($description)
    {
        if(is_string($description))
        {
            $this->description = trim($description);
        }
    }

    public function set_order($order)
    {
        if(is_numeric($order) AND $order > 0)
        {
            $this->order = (int) $order;
        }
    }

    public function set_image($image)
    {
        if(is_string($image))
        {
            $this->image = trim($image);
        }
    }

    public function set_publish($publish)
    {
        if(is_numeric($publish) AND ($publish === 1 XOR $publish === 0))
        {
            $this->publish = (int) $publish;
        }
    }

    public function set_deleted($deleted)
    {
        if(is_numeric($deleted) AND ($deleted === 1 XOR $deleted === 0))
        {
            $this->deleted = (int) $deleted;
        }
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_parent()
    {
        return $this->parent;
    }


    /* PUBLIC STATIC FUNCTIONS */

    public static function get_all_categories()
    {
        return DB::select('id','name','summary','parent','publish')->from(self::MAIN_TABLE)->where('deleted','=',0)->execute()->as_array();
    }

    public static function get_all_sub_categories()
    {
        return DB::select('id','name','summary','parent','publish')->from(self::MAIN_TABLE)->where('deleted','=',0)->and_where('parent','<>',0)->execute()->as_array();
    }

    public static function get_categories_as_dropdown($parent = '')
    {
        $categories = self::get_all_categories();
        $result = (!empty($parent) AND $parent > 0) ? '' : '<option value="">Select a category</option>';
        foreach($categories AS $key=>$category)
        {
            if($category['id'] != $parent)
            {
                $result.='<option value="'.$category['id'].'">'.$category['name'].'</option>';
            }
            else
            {
                $result = '<option value="'.$category['id'].'">'.$category['name'].'</option>'.$result;
            }
        }
        return $result;
    }

    public static function get_sub_categories_as_dropdown($parent = '')
    {
        $categories = self::get_all_sub_categories();
        $result = (!empty($parent) AND $parent > 0) ? '' : '<option value="">Select a sub-category</option>';
        foreach($categories AS $key=>$category)
        {
            if($category['id'] != $parent)
            {
                $result.='<option value="'.$category['id'].'">'.$category['name'].'</option>';
            }
            else
            {
                $result = '<option value="'.$category['id'].'">'.$category['name'].'</option>'.$result;
            }
        }
        return $result;
    }

    /* PRIVATE FUNCTIONS */

    private function _sql_get_category()
    {
            $q = DB::select('id','name','parent','summary','description','order','image','publish','deleted')->from(self::MAIN_TABLE)->where('id','=',$this->id)->execute()->as_array();
            return (count($q) > 0) ? $q[0] : array();
    }

    private function _sql_save_category()
    {

        try{
            Database::instance()->begin();
            DB::insert(self::MAIN_TABLE,$this->columns)->values($this->get())->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
        return TRUE;
    }

    private function _sql_update_category()
    {
        try{
            Database::instance()->begin();
            DB::update(self::MAIN_TABLE)->set($this->get())->where('id','=',$this->id)->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
        return TRUE;
    }
}

?>