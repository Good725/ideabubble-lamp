<?php
class Model_ProductOption extends Model{
    const OPTION_DETAILS_TABLE = 'plugin_products_option_details';
    const PRODUCTS_OPTIONS_TABLE = 'plugin_products_product_options';
    private $id = NULL;
    private $product_id = NULL;
    private $option_id = NULL;
    private $quantity = 0;
    private $location = 1;
    private $price = 0;

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
        return array('id' => $this->id,'product_id' => $this->product_id,'option_id' => $this->option_id,'quantity' => $this->quantity,'location' => $this->location,'price' => $this->price);
    }

    public function save()
    {
        $ok = false;

        try {
            Database::instance()->begin();
            if(is_numeric($this->id) OR is_numeric($this->row_exists()))
            {
                DB::update(self::OPTION_DETAILS_TABLE)->set($this->get_instance())->where('id','=',$this->id)->or_where_open()->where('product_id','=',$this->product_id)->and_where('option_id','=',$this->option_id)->or_where_close()->execute();
            }
            else
            {
                DB::insert(self::OPTION_DETAILS_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
            }
            Database::instance()->commit();
            $ok = true;
        } catch(Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }

        return $ok;
    }

    public function set_is_stock($data,$enabled = true)
    {
        //check that the row first exists.
        $q = DB::select('id')->from(self::PRODUCTS_OPTIONS_TABLE)->where('product_id','=',$data['product_id'])->and_where('option_group','=',$data['option_group'])->execute()->as_array();
        if(count($q) == 0)
        {
            DB::insert(self::PRODUCTS_OPTIONS_TABLE,array_keys($data))->values($data)->execute();
        }

        if($enabled)
        {
            DB::update(self::PRODUCTS_OPTIONS_TABLE)->set(array('is_stock' => 1))->where('option_group','=',$data['option_group'])->and_where('product_id','=',$data['product_id'])->execute();
        }
        else
        {
            DB::update(self::PRODUCTS_OPTIONS_TABLE)->set(array('is_stock' => 0))->where('option_group','=',$data['option_group'])->and_where('product_id','=',$data['product_id'])->execute();
        }
    }

    public static function instance()
    {
        return new self();
    }

    public static function update_options($product_id,$option_id,$data)
    {
        $ok = false;
        try{
            $q = DB::select('id')->from(self::OPTION_DETAILS_TABLE)->where('product_id','=',$product_id)->and_where('option_id','=',$option_id)->execute()->as_array();
            if(count($q) == 0)
            {
                if(!isset($data['product_id']))
                {
                    $data['product_id'] = $product_id;
                    $data['option_id'] = $option_id;
                }
                DB::insert(self::OPTION_DETAILS_TABLE,array_keys($data))->values($data)->execute();
            }
            else
            {
                DB::update(self::OPTION_DETAILS_TABLE)->set($data)->where('product_id','=',$product_id)->and_where('option_id','=',$option_id)->execute();
            }
            $ok = true;
        }catch(Exception $e)
        {
            $ok = false;
        }
        return $ok;
    }

    private function row_exists()
    {
        $q = DB::select('id')->from(self::OPTION_DETAILS_TABLE)->where('product_id','=',$this->product_id)->and_where('option_id','=',$this->option_id)->execute()->as_array();
        $this->id = count($q) > 0 ? $q[0]['id']: NULL;
        return count($q) > 0 ? $q[0]['id']:FALSE;
    }
}
?>