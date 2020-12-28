<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 01/12/2014
 * Time: 10:31
 */

class Model_CSV extends Model implements Interface_Ideabubble
{

    /*** CLASS CONSTANTS ***/
    const CSV_TABLE     = 'engine_csv';
    const EDIT_URL      = '/admin/settings/manage_csv/';

    /*** PRIVATE MEMBER DATA ***/
    private $id         = null;
    private $title      = '';
    private $columns    = array();
    private $publish    = 1;
    private $delete     = 0;

    /**
     * @param null $id
     */
    function __construct($id = null)
    {
        if(is_numeric($id))
        {
            $this->set_id($id);
            $this->get(true);
        }
    }

    /**
     * @param $data
     * @return $this
     */
    public function set($data)
    {
        foreach($data as $key=>$value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * @param $autoload
     */
    public function get($autoload)
    {
        $data = $this->_sql_load_csv();

        if($autoload)
        {
            $this->set($data);
        }

        return $data;
    }

    /**
     * @param null $id
     * @return $this
     */
    public function set_id($id = null)
    {
        $this->id = is_numeric($id) ? intval($id) : $this->id;
        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return TRUE;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_columns()
    {
        return $this->columns;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $ok = true;
        if($this->validate())
        {
            try
            {
                if(is_numeric($this->id))
                {
                    $this->_sql_update_csv();
                }
                else
                {
                    $this->_sql_insert_csv();
                }
            }
            catch(Exception $e)
            {
                $ok = false;
            }
        }
        else
        {
            $ok = false;
        }

        return $ok;
    }

    /**
     * @return array
     */
    public function get_instance()
    {
        return array('id'   => $this->id,
                'title'     => $this->title,
                'columns'   => $this->columns,
                'publish'   => $this->publish,
                'delete'    => $this->delete
        );
    }

    public function execute_csv_import($csv)
    {
        $table = '';
        $data = json_decode($this->columns,true);
        if(array_key_exists('table',$data[0]))
        {
            $table = $data[0]['table'];
            unset($data[0]);
            $data = array_values($data);
        }
        DB::delete($table)->execute();

        $column_keys = array();
        $column_list = array();

        foreach($data as $key=>$columns)
        {
            if($columns['csv_column'] != "")
            {
                $column_list[$columns['csv_column']] = $columns['table_column'];
                if(in_array($columns['csv_column'],$csv['head']))
                {
                    if(($k = array_search($columns['csv_column'],$csv['head'])) !== false)
                    {
                        $column_keys[$columns['csv_column']] = $k;
                    }
                }
            }
        }

        $q = DB::insert($table,$column_list);

        foreach($csv['data'] as $key=>$csv_line)
        {
            $line_array = array();
            $line = $csv_line;
            if(count($line) > 0)
            {
                foreach($column_keys as $item=>$value)
                {
					$column_title = $column_list[$item];
					$line_array[$column_title] = isset($line[$value]) ? $line[$value] : '';
                }
                $q->values($line_array);
            }

        }

        $q->execute();
    }

    /**
     *
     */
    private function _sql_load_csv()
    {
        $q = DB::select_array(array_keys($this->get_instance()))->from(self::CSV_TABLE)->where('id','=',$this->id)->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    /**
     *
     */
    private function _sql_update_csv()
    {
        DB::update(self::CSV_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
    }

    /**
     * @return object
     */
    private function _sql_insert_csv()
    {
        return DB::insert(self::CSV_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
    }

    /**
     * @param null $id
     * @return Model_CSV
     */
    public static function create($id = null)
    {
        return new self($id);
    }

    /**
     *  Return all CSV templates in the DB.
     */
    public static function get_all_csvs()
    {
        return DB::select('id','title')->from(self::CSV_TABLE)->where('publish','=',1)->and_where('delete','=',0)->execute()->as_array();
    }

    /**
     * @return array
     */
    public static function get_all_database_tables()
    {
        return Database::instance()->list_tables();
    }

    /**
     * @param $table
     * @return array
     */
    public static function get_all_table_columns($table)
    {
        return Database::instance()->list_columns($table);
    }
}