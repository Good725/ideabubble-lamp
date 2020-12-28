<?php
/*** Not to be confused with DAAAAYCEENNNT TAKKIES ***/
class Model_Trainers extends Model
{
    /*** CLASS CONSTANTS ***/
    CONST TRAINER_TABLE = 'plugin_courses_trainers';

    /*** PRIVATE MEMBER DATA ***/
    private $id         = NULL;
    private $first_name = '';
    private $last_name  = '';
    private $email      = '';

    /*** PUBLIC FUNCTIONS ***/
    function __construct($id = NULL)
    {
        if(is_numeric($id))
        {
            $this->set_id($id)->get(true);
        }
    }

    public function set_id($id)
    {
        $this->id = is_numeric($id) ? intval($id) : $this->id;
        return $this;
    }

    public function set($data)
    {
        foreach($data as $key=>$value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $value;
            }
        }
    }

    public function save()
    {
        $ok = true;
        try{
            if($this->trainer_exists($this->email))
            {
                $ok = $this->_sql_update_trainer();
            }
            else
            {
                $ok = $this->_sql_insert_trainer();
            }
        }catch(Exception $e)
        {
            $ok = false;
        }

        return $ok;
    }

    public function get($autoload = false)
    {
        $data = array();

        if($autoload)
        {
            $this->set($data);
        }

        return $data;
    }

    private function _sql_update_trainer()
    {
        DB::update(self::TRAINER_TABLE)->set(array())->where('id','=',$this->id)->execute();
    }

    private function _sql_insert_trainer()
    {
        DB::insert(self::TRAINER_TABLE,array('first_name','last_name','email','county_id','city_id'))->values(array($this->first_name,$this->last_name,$this->email,33,1))->execute();
    }

    private function trainer_exists($email)
    {
        $q = DB::select('id')->from(self::TRAINER_TABLE)->where('email','=',$email)->execute()->as_array();
        if(count($q) > 0)
        {
            $this->set_id($q[0]['id']);
            return true;
        }
        else
        {
            return false;
        }
    }

}
?>