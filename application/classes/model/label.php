<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 18/11/2014
 * Time: 14:47
 */
class Model_Label extends Model implements Interface_Ideabubble
{
    /*** CLASS CONSTANTS ***/
    CONST LABELS_TABLE  = 'engine_labels';
    CONST DELETE_COLUMN = 'delete';
    CONST DELETE_STATUS = 1;


    /*** PRIVATE MEMBER DATA ***/
    private $id     = null;
    private $label  = '';
    private $plugin = null;
    private $delete = 0;

    function __construct($id = null)
    {
        if(is_numeric($id))
        {
            $this->get(true);
        }
    }

    public function set($data)
    {
        foreach($data as $key=>$item)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $item;
            }
        }

        return $this;
    }

    public function set_label($label = '')
    {
        $this->label = $label;
    }

    public function get($autoload)
    {
        $data = array();

        if($autoload)
        {
            $this->set($data);
        }

        return $data;
    }

    public function save()
    {
        if(is_numeric($this->id))
        {
            $this->_sql_update_label();
        }
        else
        {
            $this->_sql_insert_label();
        }
    }

    public function delete()
    {
        $this->set_delete(1);
        $this->save();
    }

    public function set_delete($delete = 0)
    {
        $this->delete = $delete === 1 ? 1 : 0;
    }

    public function validate()
    {
        return true;
    }

    private function _sql_insert_label()
    {
        DB::insert(self::LABELS_TABLE,array('label'))->values(array('label' => $this->label))->execute();
    }

    private function _sql_update_label()
    {
        DB::update(self::LABELS_TABLE)->set(array('delete' => $this->delete))->where('id','=',$this->id)->execute();
    }

    public static function create($id = null)
    {
        return new self($id);
    }

    public static function get_all_labels()
    {
        return DB::select('id','label','plugin_id','delete')->from(self::LABELS_TABLE)->where(self::DELETE_COLUMN,'=',!self::DELETE_STATUS)->execute()->as_array();
    }
}