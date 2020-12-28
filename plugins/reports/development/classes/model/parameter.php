<?php
class Model_Parameter extends Model{
    const PARAMETER_TABLE = 'plugin_reports_parameters';
    private $id = NULL;
    private $report_id = NULL;
    private $type = '';
    private $name = '';
    private $value = '';
    private $delete = 0;
	private $is_multiselect = 0;

    function __construct($id = NULL)
    {
        if(is_numeric($id))
        {
            $this->set_id($id)->load();
        }
    }

    public function set_id($id)
    {
        if(is_numeric($id))
        {
            $this->id = (int) $id;
        }

        return $this;
    }

    public function set_report_id($id)
    {
        if(is_numeric($id))
        {
            $this->report_id = (int) $id;
        }

        return $this;
    }

    public function set_type($type)
    {
        $this->type = (is_string($type)) ? $type : '';
        return $this;
    }

    public function set_name($name)
    {
        $this->name = (is_string($name)) ? $name : '';
        return $this;
    }

    public function set_value($value)
    {
        $this->value = (is_string($value)) ? $value : '';
        return $this;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_delete($delete)
    {
        $this->delete = ($delete === 1) ? 1 : 0;
    }

    public function get_report_id()
    {
        return $this->report_id;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_value()
    {
        return $this->value;
    }
	
	public function get_is_multiselect()
	{
		return $this->is_multiselect;
	}
	
	public function set_is_multiselect($value)
	{
		return $this->is_multiselect = $value;
	}

    public function load()
    {
        $data = array();

        if(is_numeric($this->id))
        {
            $data = $this->_sql_load_parameter();
            $this->set($data);
        }

        return $data;
    }

    public function save()
    {
        if(is_numeric($this->id))
        {
            $this->_sql_update_parameter();
        }
        else
        {
            $this->_sql_save_parameter();
        }
    }

    public function set($data)
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

    public function get_instance()
    {
        return array('id' => $this->id,
                     'report_id' => $this->report_id,
                     'type' => $this->type,
                     'name' => $this->name,
                     'value' => $this->value,
                     'delete' => $this->delete,
					 'is_multiselect' => $this->is_multiselect
        );
    }

    public static function get_all_parameters($report_id)
    {
        return DB::select('id','report_id','type','name','value','delete', 'is_multiselect')->from(self::PARAMETER_TABLE)->where('delete','=',0)->and_where('report_id','=',$report_id)->execute()->as_array();
    }

    private function _sql_load_parameter()
    {
        $q = DB::select('id','report_id','type','name','value','is_multiselect')->from(self::PARAMETER_TABLE)->where('id','=',$this->id)->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    private function _sql_update_parameter()
    {

        try{
            Database::instance()->begin();
            DB::update(self::PARAMETER_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function _sql_save_parameter()
    {

        try{
            Database::instance()->begin();
            DB::insert(self::PARAMETER_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
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