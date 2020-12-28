<?php
final class Model_Charts extends Model{
    const CHARTS_TABLE = 'plugin_reports_charts';
    private $id = NULL;
    private $title = '';
    private $type = '';
    private $x_axis = '';
    private $y_axis = '';
    private $publish = 1;
    private $delete = 0;

    function __construct($id,$autoload = true)
    {
        if(is_numeric($id))
        {
            $this->set_id($id);
        }

        if($autoload)
        {
            $this->load();
        }
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_x_axis()
    {
        return $this->x_axis;
    }

    public function get_y_axis()
    {
        return $this->y_axis;
    }

    public function set($data)
    {
        $this->id = (isset($data['chart_id'])) ? $this->set_id($data['chart_id']) : $this->id;
        $this->title = (isset($data['chart_title'])) ? $this->set_title($data['chart_title']) : $this->title;
        $this->type = (isset($data['chart_type'])) ? $this->set_type($data['chart_type']) : $this->type;
        $this->x_axis = (isset($data['chart_x_axis'])) ? $this->set_x_axis($data['chart_x_axis']) : $this->x_axis;
        $this->y_axis = (isset($data['chart_y_axis'])) ? $this->set_y_axis($data['chart_y_axis']) : $this->y_axis;
        $this->publish = (isset($data['chart_publish'])) ? $this->set_publish($data['chart_publish']) : $this->publish;
        $this->delete = (isset($data['chart_delete'])) ? $this->set_delete($data['chart_delete']) : $this->delete;
    }

    public function db_set($data)
    {
        $this->id = (isset($data['id'])) ? $this->set_id($data['id']) : $this->id;
        $this->title = (isset($data['title'])) ? $this->set_title($data['title']) : $this->title;
        $this->type = (isset($data['type'])) ? $this->set_type($data['type']) : $this->type;
        $this->x_axis = (isset($data['x_axis'])) ? $this->set_x_axis($data['x_axis']) : $this->x_axis;
        $this->y_axis = (isset($data['y_axis'])) ? $this->set_y_axis($data['y_axis']) : $this->y_axis;
        $this->publish = (isset($data['publish'])) ? $this->set_publish($data['publish']) : $this->publish;
        $this->delete = (isset($data['delete'])) ? $this->set_delete($data['delete']) : $this->delete;
    }

    public function set_id($id)
    {
        if(is_numeric($id) AND $id > 0)
        {
            $this->id = (int) trim($id);
        }

        return $this->id;
    }

    public function has_x_axis()
    {
        if(strlen(trim($this->x_axis)) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function has_y_axis()
    {
        if(strlen(trim($this->y_axis)) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function set_title($title)
    {
        if(is_string($title) AND strlen(trim($title)) > 0)
        {
            $this->title = trim($title);
        }

        return $this->title;
    }

    public function set_type($type)
    {
        if(is_numeric($type) AND $type > 0)
        {
            $this->type = (int) $type;
        }

        return $this->type;
    }

    public function set_x_axis($x_axis)
    {
        if(is_string($x_axis) AND strlen(trim($x_axis)) > 0)
        {
            $this->x_axis = $x_axis;
        }

        return $this->x_axis;
    }

    public function set_y_axis($y_axis)
    {
        if(is_string($y_axis) AND strlen(trim($y_axis)) > 0)
        {
            $this->y_axis = $y_axis;
        }

        return $this->y_axis;
    }

    public function set_publish($publish)
    {
        $this->publish = $publish === 0 ? 0 : 1;
        return $this->publish;
    }

    public function set_delete($delete)
    {
        $this->delete = $delete === 1 ? 1 : 0;
        return $this->delete;
    }

    public function load($autoload = true)
    {
        $result = $this->_sql_load_chart();

        if($autoload)
        {
            $this->db_set($result);
        }

        return $result;
    }

    public function get_instance(&$obj)
    {
        $obj = $this;
    }

    public function save()
    {
        $id = $this->id;
        if(is_numeric($this->id))
        {
            $this->_sql_update_chart();
        }
        elseif(is_null($this->id))
        {
            $id = $this->_sql_save_chart();
        }
        else
        {
            throw new Exception("UNDEFINED CHART ID");
        }

        return $id;
    }

    public function get_publish()
    {
        return $this->publish;
    }

    public function get_delete()
    {
        return $this->delete;
    }

    public function get()
    {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'x_axis' => $this->x_axis,
            'y_axis' => $this->y_axis,
            'publish' => $this->publish,
            'delete' => $this->delete
        );
    }

    /********* PRIVATE FUNCTIONS *********/

    private function _sql_load_chart()
    {
        $q = DB::select('id','title','type','x_axis','y_axis','publish','delete')->from(self::CHARTS_TABLE)->where('id','=',$this->id)->limit(1)->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    private function _sql_save_chart()
    {

        try{
            Database::instance()->begin();
            $q = DB::insert(self::CHARTS_TABLE,array_keys($this->get()))->values($this->get())->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
        return $q[0];
    }

    private function _sql_update_chart()
    {

        try{
            Database::instance()->begin();
            DB::update(self::CHARTS_TABLE)->set($this->get())->where('id','=',$this->id)->execute();
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