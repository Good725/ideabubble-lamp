<?php
class Model_Cart extends Model{
    const CART_TABLE                  = 'plugin_products_carts';
    const CART_PRODUCTS_TABLE         = 'plugin_products_cart_items';
    const CART_PRODUCTS_OPTIONS_TABLE = 'plugin_products_cart_items_options';

    private $id            = NULL;
    private $user_agent    = '';
    private $ip_address    = '';
	private $user_id       = NULL;
	private $cart_data     = '';
	private $form_data     = '';
	private $paid          = '';
    private $date_created  = '';
    private $date_modified = '';

    function __construct($id = NULL)
    {
        if (is_numeric($id))
        {
			$this->id = $id;
            $this->load(TRUE);
        }
    }

    public function load($autoload)
    {
        $data = $this->_sql_get_cart();

        if (count($data) > 0 AND ! isset($data))
        {
			$this->_sql_save_cart();
        }

        if ($autoload)
        {
            $this->set($data);
        }

        return count($data) > 0 ? $data : array();
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_instance()
    {
        return array(
			'id'            => $this->id,
            'user_agent'    => $this->user_agent,
            'ip_address'    => $this->ip_address,
			'user_id'       => $this->user_id,
			'cart_data'     => $this->cart_data,
			'form_data'     => $this->form_data,
			'paid'          => $this->paid,
            'date_created'  => $this->date_created,
            'date_modified' => $this->date_modified
        );
    }

    public function set($data)
    {
        foreach ($data AS $key=>$element)
        {
            if (is_object($element))
            {
                self::format_to_solution($element);
            }

            if (property_exists($this,$key))
            {
                $this->{$key} = $data[$key];
            }
        }

        return $this;
    }

    public function set_id($id = NULL)
    {
        if(is_numeric($id))
        {
            $this->id = $id;
        }
    }

	public function set_paid($value)
	{
		$this->paid = $value;
		return $this;
	}

    public function save()
    {
        if (self::cart_exists($this->id))
        {
            $this->_sql_update_cart();
        }
        else
        {
            $this->_sql_save_cart();
        }
    }

    public static function format_to_solution(&$data)
    {
        $data = (array) $data;
    }

    public static function cart_exists($cart_id)
    {
        $q = DB::select('id')->from(self::CART_TABLE)->where('id','=',$cart_id)->execute()->as_array();
        return (count($q) > 0);
    }

	/** Get a list of carts made by a specified user.
	 * @param int $user_id         - The ID of the user being checked. Defaults to the logged-in user's ID
	 * @param bool $include_unpaid - Show carts, even if they were not paid for. Defaults to TRUE
	 * @return array
	 */
	public static function shopping_history($user_id = NULL, $include_unpaid = TRUE)
	{
		if (is_null($user_id))
		{
			$user = Auth::instance()->get_user();
			$user_id = $user['id'];
		}
		if ($user_id)
		{
			$q = DB::select()->from(self::CART_TABLE)->where('user_id', '=', $user_id)->order_by('date_created', 'desc');
			if ( ! $include_unpaid)
			{
				$q->where('paid', '=', 1);
			}
			return $q->execute()->as_array();
		}
		else
		{
			return array();
		}

	}

    private function _sql_get_cart()
    {
        $q = DB::select('id','user_agent','ip_address', 'user_id', 'cart_data','form_data','paid','date_created','date_modified')
			->from(self::CART_TABLE)->where('id','=',$this->id)->execute()->as_array();

		return (count($q) > 0 ? $q[0] : array());
    }

    private function _sql_save_cart()
    {
		$this->date_modified = date('Y-m-d H:i:s');
		$this->date_created = $this->date_modified;

        try
		{
            Database::instance()->begin();
            DB::insert(self::CART_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();
            Database::instance()->commit();
        }
        catch (Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
    }

    private function _sql_update_cart()
    {
		$this->date_modified = date('Y-m-d H:i:s');
        try
		{
            Database::instance()->begin();
            DB::update(self::CART_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
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
