<?php
class Model_Matrix extends Model
{
    /*** Finals & Constants ***/
    CONST MATRIX_TABLE                      = 'plugin_products_matrices';
    CONST PRODUCTS_OPTIONS_TABLE            = Model_Product::TABLE_OPTIONS_MAIN;
    CONST MATRIX_OPTIONS_TABLE              = 'plugin_products_matrix_options';
    CONST MATRIX_SECONDARY_OPTIONS_TABLE    = 'plugin_products_matrix_options_extended';

    /*** Private Member Data ***/
    private $id                 = NULL;
    private $name               = '';
    private $option_1_id        = NULL;
    private $option_2_id        = NULL;
    private $enabled            = 0;
    private $delete             = 0;
    private $option_association = array();
    private $matrix_data        = array();

    /*** Public Member Data ***/
    public $options_list    = array();
    public $options_groups  = array();

    function __construct($id = NULL)
    {
        if(is_numeric($id))
        {
            $this->set_id($id);
            $this->get(true);
        }

        $this->options_list = Model_Option::get_all();
        $this->options_groups = Model_Option::get_all_groups();
    }

    /*** Public Functions ***/
    public function get($autoload = FALSE)
    {
        $data = $this->_sql_load_matrix();

        if($autoload)
        {
            $this->set($data);
        }

        return $data;
    }

    public function set($data)
    {
        foreach($data AS $key=>$item)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $item;
            }
        }

        return $this;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_option_1_id()
    {
        return $this->option_1_id;
    }

    public function get_option_2_id()
    {
        return $this->option_2_id;
    }

    public function get_options()
    {
        return $this->options_list;
    }

    public function get_publish()
    {
        return $this->enabled;
    }

    public function get_option_groups()
    {
        Model_Option::get_options_by_field();
        return $this->options_groups;
    }

    public function generate_table()
    {
        $y = Model_Option::get_options_by_group_id($this->option_1_id);
        $x = Model_Option::get_options_by_group_id($this->option_2_id);
        return '';
    }

    public function get_instance()
    {
        return array('id' => $this->id,
        'name' => $this->name,
        'option_1_id' => $this->option_1_id,
        'option_2_id' => $this->option_2_id,
        'enabled' => $this->enabled,
        'delete' => $this->delete);
    }

    public function set_id($id = NULL)
    {
        $this->id = $id;
    }

    public function save()
    {

        try {
            Database::instance()->begin();
            if(is_numeric($this->id))
            {
                $this->sql_update_matrix();
            }
            else
            {
                $q = $this->sql_insert_matrix();
                $this->set_id($q);
            }
            $this->save_options();
            Database::instance()->commit();
        } catch(Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }
    }

    public function get_matrix_data($json_encode = false)
    {
        return $json_encode ? json_encode($this->gather_matrix_data()) : $this->gather_matrix_data();
    }

    public function get_id()
    {
        return $this->id;
    }

    /*** Private Functions ***/
    private function _sql_load_matrix()
    {
        $q = DB::select_array(array_keys($this->get_instance()))->from(self::MATRIX_TABLE)->where('id','=',$this->id)->execute()->as_array();
        return count($q) > 0 ? $q[0] : array();
    }

    private function sql_insert_matrix()
    {
        $q = DB::insert(self::MATRIX_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
        return $q[0];
    }

    private function sql_update_matrix()
    {
        DB::update(self::MATRIX_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
    }

    private function save_options()
    {

        $this->matrix_data = is_array($this->matrix_data) ? $this->matrix_data : json_decode($this->matrix_data,true);
        $q = DB::insert(self::MATRIX_OPTIONS_TABLE);
        $r = DB::insert(self::MATRIX_SECONDARY_OPTIONS_TABLE);
        foreach($this->matrix_data AS $key=>$option)
        {
            DB::delete(self::MATRIX_OPTIONS_TABLE)->where('option1','=',$option['option1'])->and_where('option2','=',$option['option2'])->and_where('matrix_id','=',$this->id)->execute();
            $secondary = json_decode($option['secondary'],true);
            $publish = !isset($option['publish']) ? 0 : $option['publish'];
            $q->values(array('matrix_id' => $this->id,'option1' =>$option['option1'],'option2' => $option['option2'],'price' => $option['price'],'price_adjustment' => $option['price_adjustment'],'publish' => $publish,'image' => $option['image']));

            if(count($secondary) > 0)
            {
                foreach($secondary AS $value=>$item)
                {
                    DB::delete(self::MATRIX_SECONDARY_OPTIONS_TABLE)->where('option1','=',$item['option1'])->and_where('option2','=',$item['option2'])->and_where('option3','=',$item['option3'])->execute();
                    $r->values(array('data_id' => $this->id,'option1' => $item['option1'],'option2' => $item['option2'],'option3' => $item['option3'],'publish' => 1,'price' => $item['price'],'price_adjustment' => $item['price_adjustment'],'option_group' => $item['option_group']));
                }
            }
        }

        if(count($this->matrix_data) > 0)
        {
            $q->execute();
            if(count($secondary) > 0)
            {
                $r->execute();
            }
        }
    }

    private function gather_matrix_data()
    {
        $q = DB::select_array(array('matrix_id','option1','option2','price','price_adjustment','publish','image'))->from(self::MATRIX_OPTIONS_TABLE)->where('matrix_id','=',$this->id)->execute()->as_array();
        $result = array();

        foreach($q AS $key=>$row)
        {
            $r = DB::select_array(array('data_id','option1','option2','option3','publish','price','price_adjustment','option_group'))->from(self::MATRIX_SECONDARY_OPTIONS_TABLE)->where('data_id','=',$this->id)->and_where('option1','=',$row['option1'])->and_where('option2','=',$row['option2'])->execute()->as_array();
            $secondary = count($r) > 0 ? json_encode($r) : array();
            $result[] = array('option1' => $row['option1'],'option2' => $row['option2'],'publish' => $row['publish'],'price' => $row['price'],'price_adjustment' => $row['price_adjustment'],'image' => $row['image'],'secondary' => $secondary);
        }

        return $result;
    }

    /*** Public Static Functions ***/

    public static function create($id = NULL)
    {
        return new self($id);
    }

    public static function get_all_matrices()
    {
        $matrix = DB::select(
            'matrix.*',
            array('options_1.group', 'option_1'),
            array('options_2.group', 'option_2')
        )
            ->from(array(self::MATRIX_TABLE, 'matrix'))
                ->join(array(Model_Option::OPTION_GROUPS, 'options_1'), 'left')
                    ->on('matrix.option_1_id', '=', 'options_1.id')
                ->join(array(Model_Option::OPTION_GROUPS, 'options_2'), 'left')
                    ->on('matrix.option_2_id', '=', 'options_2.id')
            ->where('matrix.delete', '=', 0)
            ->execute()
            ->as_array();
        return $matrix;
    }

    public static function get_all_matrices_list()
    {
        $matrices = DB::select(
            'matrix.*',
            array('options_1.group', 'option_a'),
            array('options_2.group', 'option_b')
        )
            ->from(array(self::MATRIX_TABLE, 'matrix'))
                ->join(array(Model_Option::OPTION_GROUPS, 'options_1'), 'left')
                    ->on('matrix.option_1_id', '=', 'options_1.id')
                ->join(array(Model_Option::OPTION_GROUPS, 'options_2'), 'left')
                    ->on('matrix.option_2_id', '=', 'options_2.id')
            ->where('matrix.delete', '=', 0)
            ->and_where('matrix.enabled', '=', 1)
            ->execute()
            ->as_array();
        return $matrices;
    }

    public static function get_all_available_options($option_id)
    {
        $q = DB::select('option1','option2','price','price_adjustment','publish')->from(self::MATRIX_OPTIONS_TABLE)->where('publish','=',1)->execute()->as_array();
    }

	public static function get_matrix_option_groups($matrix_id)
	{
		$matrix_model                     = new Model_Matrix($matrix_id);
		$option_groups                    = array();
		$option_groups[0]['value']        = $matrix_model->get_option_1_id();
		$option_groups[0]['options']      = Model_Option::get_options_by_group_id($option_groups[0]['value'], $matrix_id);
		$option_groups[0]['option_group'] = $option_groups[0]['options'][0]['group'];
		$option_groups[0]['group_label']  = $option_groups[0]['options'][0]['group_label'];
		$option_groups[1]['value']        = $matrix_model->get_option_2_id();
		$option_groups[1]['options']      = Model_Option::get_options_by_group_id($option_groups[1]['value'], $matrix_id, 2);
		$option_groups[1]['option_group'] = $option_groups[1]['options'][1]['group'];
		$option_groups[1]['group_label']  = $option_groups[1]['options'][1]['group_label'];

		return $option_groups;
	}
}
?>