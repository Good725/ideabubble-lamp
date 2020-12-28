<?php
class Model_Cartitems extends Model{
    const CART_TABLE = 'plugin_products_carts';
    const CART_PRODUCTS_TABLE = 'plugin_products_cart_items';
    const CART_PRODUCTS_OPTIONS_TABLE = 'plugin_products_cart_items_options';

    private $cart_id = NULL;
    private $products = array();
    private $product_template = array('item_id' => 0,'cart_id' => NULL,'id' => NULL,'title' => '','quantity' => 0,'price' => 0.00);

    function __construct($cart_id)
    {
        $this->set_cart_id($cart_id);
        if(is_numeric($cart_id))
        {
            $this->load(true);
        }
    }

    public function load($autoload)
    {
        $data = $this->_sql_load_cart_items();
        $options = $this->_sql_load_cart_item_options();

        if($autoload)
        {
            $this->set($data);
            $this->set_options($options);
        }
    }

    public function set($data)
    {
        foreach($data AS $key=>$array)
        {
            if(is_object($array))
            {
                self::format_to_solution($array);
            }

            $products = isset($array['product']) ? (array) $array['product'] : array();
            $products['quantity'] = $array['quantity'];
            $products['cart_id'] = $this->cart_id;

            foreach($products AS $value=>$element)
            {
                if(array_key_exists($value,$this->product_template))
                {
                    $this->products[$products['id']][$value] = $products[$value];
                }
            }
            if(isset($array['options']) AND count($array['options']) > 0)
            {
                foreach($array['options'] AS $options_key=>$options_value)
                {
                    $array['options'][$options_key]->products_id = $products['id'];
                    $this->set_options($array['options'][$options_key]);
                }
            }
            elseif(!isset($this->products[$array['product']->id]['options']))
            {
                $this->products[$array['product']->id]['options'] = array();
            }
        }

        return $this;
    }

    public function set_options($options)
    {

        if(is_object($options))
        {
            self::format_to_solution($options);
        }

        if(count($this->products) > 0)
        {
            $this->products[$options['products_id']]['options'] = array();
            array_push($this->products[$options['products_id']]['options'],array($options['label'],$options['group'],$options['price']));
        }
    }

    public function set_cart_id($cart_id)
    {
        $this->cart_id = $cart_id;
    }

    public function save()
    {
        $this->_sql_save_cart_products();
        $this->_sql_save_cart_product_options();
    }

    public static function format_to_solution(&$data)
    {
        $data = (array) $data;
    }

    private function _sql_load_cart_items()
    {
        return DB::select('item_id','cart_id','id','title','quantity','price')->from(self::CART_PRODUCTS_TABLE)->where('cart_id','=',$this->cart_id)->execute()->as_array();
    }

    private function _sql_load_cart_item_options()
    {
        return DB::select('cart_id','id','label','group','price')->from(self::CART_PRODUCTS_OPTIONS_TABLE)->where('cart_id','=',$this->cart_id)->and_where('delete','=',0)->execute()->as_array();
    }

    private function get_products_for_save()
    {
        $result = array();
        foreach($this->products AS $key=>$product)
        {
            $result[] = array($product['cart_id'],$product['id'],$product['title'],$product['price'],$product['quantity']);
        }

        return $result;
    }

    private function _sql_save_cart_products()
    {
        try{
            Database::instance()->begin();
            foreach($this->products AS $key=>$product)
            {
                DB::insert(self::CART_PRODUCTS_TABLE,array('cart_id','id','title','price','quantity'))->values(array($product['cart_id'],$product['id'],$product['title'],$product['price'],$product['quantity']))->execute();
            }
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function _sql_save_cart_product_options()
    {

        try{
            Database::instance()->begin();
            foreach($this->products AS $key=>$product)
            {
                foreach($product['options'] AS $options_key=>$options_value)
                {
                    DB::insert(self::CART_PRODUCTS_OPTIONS_TABLE,array('cart_id','id','label','group','price'))->values(array($product['cart_id'],$product['id'],$options_value[0],$options_value[1],$options_value[2]))->execute();
                }
            }
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