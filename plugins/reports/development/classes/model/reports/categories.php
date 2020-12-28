<?php
final class Model_Reports_Categories extends Model
{
    const MAIN_TABLE = 'plugin_reports_categories';
    private $id = NULL;
    private $name = '';
    private $summary = '';
    private $content = '';
    private $parent = 0;
    private $order = 0;
    private $publish = 1;
    private $delete = 0;
    private $date_created = '';
    private $date_modified = '';

    public function __construct($id = NULL)
    {
        $this->set_id($id);
    }

    public function get($autoload = false)
    {
        $data = $this->_sql_get_category();

        if($autoload)
        {
            $this->load($data);
        }

        return $data;
    }

    public function get_instance()
    {
        return $this->get_data();
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_summary()
    {
        return $this->summary;
    }

    public function get_order()
    {
        return $this->order;
    }

    public function get_content()
    {
        return $this->content;
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function get_publish()
    {
        return $this->publish;
    }

    public function get_delete()
    {
        return $this->delete;
    }

    public function get_date_created()
    {
        return $this->date_created;
    }

    public function get_date_modified()
    {
        return $this->date_modified;
    }

    public function load($data)
    {
        $this->id = (isset($data['id']))   ? $this->set_id($data['id'])     : $this->id;
        $this->name = (isset($data['name'])) ? $this->set_name($data['name']) : $this->name;
        $this->summary = (isset($data['summary'])) ? $this->set_summary($data['summary']) : $this->summary;
        $this->content = (isset($data['content'])) ? $this->set_content($data['content']) : $this->content;
        $this->parent = (isset($data['parent'])) ? $this->set_parent($data['parent']) : $this->parent;
        $this->order = (isset($data['order'])) ? $this->set_order($data['order']) : $this->order;
        $this->publish = (isset($data['publish'])) ? $this->set_publish($data['publish']) : $this->publish;
        $this->delete = (isset($data['delete'])) ? $this->set_delete($data['delete']) : $this->delete;
        $this->date_created = $this->set_date_created($this->date_created);
        $this->date_modified = $this->set_date_modified($this->date_modified);
    }

    public function save()
    {
        if(is_numeric($this->id))
        {
            $this->_sql_update_category();
        }
        else
        {
            $this->_sql_save_category();
        }
    }

    public function delete()
    {
        $ok = TRUE;
        try{
            $this->set_delete(1);
            $this->set_publish(0);
            $this->save();
        }
        catch(Exception $e)
        {
            $ok = FALSE;
        }
        return $ok;
    }

    public function publish($publish)
    {
        $this->set_publish($publish);
        $this->save();
    }

    public function get_categories_dropdown()
    {
        $result = '';
        $categories = self::get_all_categories();
        foreach($categories AS $category)
        {
            if($category['id'] == $this->parent)
            {
                $result.='<option value="'.$category['id'].'" selected="selected">'.$category['name'].'</option>';
            }
            else
            {
                $result.='<option value="'.$category['id'].'">'.$category['name'].'</option>';
            }
        }
        return $result;
    }

    /* --- STATIC FUNCTIONS --- */

    public static function get_all_categories()
    {
        return DB::select('id','name','parent','summary','content','publish','delete','date_created','date_modified')->from(self::MAIN_TABLE)->where('delete','=',0)->execute()->as_array();
    }

    public static function get_all_sub_categories()
    {
        return DB::select('id','name','parent','summary','content','publish','delete','date_created','date_modified')->from(self::MAIN_TABLE)->where('delete','=',0)->and_where('parent','>',0)->execute()->as_array();
    }

    public static function categories_as_option($category_id = NULL,$subcategory = false)
    {
        $result = '';
        if($subcategory)
        {
            $categories = self::get_all_sub_categories();
            if($category_id == NULL)
            {
                foreach($categories AS $category)
                {
                    $result.='<option value="'.$category['id'].'">'.$category['name'].'</option>';
                }
            }
            else
            {
                foreach($categories AS $category)
                {
                    if($category['id'] == $category_id)
                    {
                        $result.='<option value="'.$category['id'].'" selected="selected">'.$category['name'].'</option>';
                    }
                    else
                    {
                        $result.='<option value="'.$category['id'].'">'.$category['name'].'</option>';
                    }
                }
            }
        }
        else
        {
            $categories = self::get_all_categories();
            if($category_id == NULL)
            {
                foreach($categories AS $category)
                {
                    $result.='<option value="'.$category['id'].'">'.$category['name'].'</option>';
                }
            }
            else
            {
                foreach($categories AS $category)
                {
                    if($category['id'] == $category_id)
                    {
                        $result.='<option value="'.$category['id'].'" selected="selected">'.$category['name'].'</option>';
                    }
                    else
                    {
                        $result.='<option value="'.$category['id'].'">'.$category['name'].'</option>';
                    }
                }
            }
        }
        return $result;
    }

    /* --- PRIVATE FUNCTIONS --- */
    private function set_id($id)
    {
        if(is_numeric($id))
        {
            $this->id = (int) trim($id);
        }

        return $this->id;
    }

    private function set_name($name)
    {
        $this->name = trim($name);
        return $this->name;
    }

    private function set_summary($summary)
    {
        $this->summary = trim($summary);
        return $this->summary;
    }

    private function set_content($content)
    {
        $this->content = trim($content);
        return $this->content;
    }

    private function set_parent($parent)
    {
        $this->parent = (!is_numeric($parent)) ? 0 : (int) $parent;
        return $this->parent;
    }

    private function set_date_created($date)
    {
        if(strtotime($date) !== FALSE)
        {
            $this->date_created = $date;
        }
        else
        {
            $this->date_created = date("Y-m-d H:i:s",time());
        }

        return $this->date_created;
    }

    private function set_order($order)
    {
        $this->order = (is_numeric($order)) ? (int) trim($order) : 0;
        return $this->order;
    }

    private function set_date_modified($date)
    {
        if(strtotime($date) !== FALSE)
        {
            $this->date_modified = $date;
        }
        else
        {
            $this->date_modified = date("Y-m-d H:i:s",time());
        }

        return $this->date_modified;
    }

    private function set_delete($delete = 0)
    {
        $this->delete = ($delete === 1) ? 1 : 0;
        return $this->delete;
    }

    private function set_publish($publish = 1)
    {
        $this->publish = ($publish === 0) ? 0 : 1;
        return $this->publish;
    }

    private function get_data()
    {
        return array('id' => $this->id,'name' => $this->name,'summary' => $this->summary,'content' => $this->content,'parent' => $this->parent,'publish' => $this->publish,'delete' => $this->delete,'date_created' => $this->date_created,'date_modified' => $this->date_modified);
    }

    private function _sql_get_category()
    {
        $q = DB::select('id','name','parent','summary','content','publish','delete','date_created','date_modified')->from(self::MAIN_TABLE)->where('id','=',$this->id)->execute()->as_array();
        return (count($q) > 0) ? $q[0]: array();
    }

    private function _sql_save_category()
    {

        try {
            Database::instance()->begin();
            DB::insert(self::MAIN_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function _sql_update_category()
    {

        try {
            Database::instance()->begin();
            DB::update(self::MAIN_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }
}
?>