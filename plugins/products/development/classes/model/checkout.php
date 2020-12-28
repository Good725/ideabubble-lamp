<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Checkout extends Model
{
    // CART SESSION IDENTIFIER
    const CART_SESSION_ID            = 'MODEL_CHECKOUT_CART';

    // STATUS CODES
    const STATUS_S_OK                =  0;
    const STATUS_E_ERROR             = -1;
    const STATUS_E_MISSING_OPTIONS   = -2;
    const STATUS_E_WRONG_COUPON_CODE = -3;

    // MODIFY CART OPERATION CODES
    const MODIFY_CART_ADD            =  1;
    const MODIFY_CART_REMOVE         =  2;
    const MODIFY_CART_SET            =  3;

    // FIELDS
    private $cart;

    // TABLES
    const PRODUCTS_TABLE            = 'plugin_products_product';

    //
    // PUBLIC FUNCTIONS
    //

    /**
     * The constructor.
     */
    public function __construct()
    {
        $session = Session::instance();

        if (($this->cart = $session->get(self::CART_SESSION_ID, NULL)) === NULL)
        {
            $session->set(self::CART_SESSION_ID, $this->cart = new stdClass());

            // Create checkout ID based on the TIMESTAMP
            $this->cart->id = vsprintf('%d%06d', gettimeofday());
        }
    }

    /**
     * @return stdClass
     */
    public function get_cart()
    {
        return $this->generate_response(self::STATUS_S_OK, $this->cart);
    }

    /**
     * @param stdClass $data
     * @return stdClass
     */
    public function add_to_cart($data, $override_price = null)
    {
        $timestamp      = isset($data->timestamp)      ? $data->timestamp      : NULL;
		$sign_thumbnail = isset($data->sign_thumbnail) ? $data->sign_thumbnail : NULL;

        try
        {
            $ok = (isset($data->product_id) AND isset($data->options) AND isset($data->quantity) AND is_int($data->product_id) AND is_int($data->quantity) AND ($data->options === NULL OR is_object($data->options)));

            if ($ok)
            {
                if (($i = $this->index_of($data->product_id, $data->options)) != -1)
                {
                    // Modify Line
                    $status        = $this->modify_line($i, self::MODIFY_CART_ADD, $data->quantity) ? self::STATUS_S_OK : self::STATUS_E_ERROR;
                    $response_data = ($status === self::STATUS_S_OK) ? $this->get_cart_summary() : NULL;

                }
                else
                {
                    // Add Line
                    $product = Model_Product::get($data->product_id, array(array('publish', '=', 1), array('deleted', '=', 0), array('out_of_stock', '=', 0)));

                    if (count($product) == 0)
                    {
                        $status        = self::STATUS_E_ERROR;
                        $response_data = NULL;
                    }
                    else
                    {
                        if (($missing_options = $this->get_missing_options($product, $data->options)) !== NULL)
                        {
                            $status        = self::STATUS_E_MISSING_OPTIONS;
                            $response_data = $missing_options;
                        }
                        else
                        {
                            $status        = $this->add_line($product, $data->options, $data->quantity, $timestamp, $sign_thumbnail, $override_price) ? self::STATUS_S_OK : self::STATUS_E_ERROR;
                            $response_data = ($status === self::STATUS_S_OK) ? $this->get_cart_summary() : NULL;
                        }
                    }
                }
            }
            else
            {
                $status        = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }
        return $this->generate_response($status, $response_data);
    }

    /**
     * @param stdClass $data
     * @return stdClass
     */
    public function delete_from_cart($data)
    {
        try
        {
            $ok = (isset($data->line_id) AND is_int($data->line_id) AND isset($this->cart->lines[$data->line_id]));

            if ($ok)
            {
                $status        = $this->delete_line($data->line_id) ? self::STATUS_S_OK : self::STATUS_E_ERROR;
                $response_data = ($status === self::STATUS_S_OK) ? $this->get_cart_summary() : NULL;
            }
            else
            {
                $status        = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }

        return $this->generate_response($status, $response_data);
    }

    /**
     * @param stdClass $data
     * @return stdClass
     */
    public function modify_cart($data)
    {
		//echo Debug::vars($data->line_id);
        try
        {
            $ok = (isset($data->line_id) AND isset($data->operation) AND isset($data->quantity) AND is_int($data->line_id) AND is_int($data->quantity) AND isset($this->cart->lines[$data->line_id]));
			
            if ($ok)
            {
                $status        = $this->modify_line($data->line_id, $data->operation, $data->quantity) ? self::STATUS_S_OK : self::STATUS_E_ERROR;
                $response_data = ($status === self::STATUS_S_OK) ? $this->get_cart_summary() : NULL;
                /* check cart based discounts*/
            }
            else
            {
                $status        = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }
        return $this->generate_response($status, $response_data);
    }

    /**
     * @param stdClass $data
     * @return stdClass
     * @throws Exception
     */
    public function set_postal_zone($data)
    {
        try
        {
            $ok = (isset($data->zone_id) AND is_int($data->zone_id) AND isset($this->cart->lines) AND count((array) $this->cart->lines) > 0);

            if ($ok)
            {
                $zone = Model_PostageZone::get($data->zone_id, array(array('publish', '=', 1), array('deleted', '=', 0)));

                if (count($zone) == 0)
                {
                    $status        = self::STATUS_E_ERROR;
                    $response_data = NULL;
                }
                else
                {
                    $this->cart->zone_id = $zone['id'];
                    $this->update_cart();

                    $status        = self::STATUS_S_OK;
                    $response_data = $this->get_cart_summary();
                }
            }
            elseif ($data->zone_id == 0 OR is_null($data->zone_id))
            {
                $this->cart->zone_id = '';

                $this->update_cart();

                $status        = self::STATUS_S_OK;
                $response_data = $this->get_cart_summary();
            }
            else
            {
                $status        = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }

        return $this->generate_response($status, $response_data);
    }

	public function set_country($data)
	{
		try
		{
			$this->cart->country = isset($data->country) ? $data->country : '';
			$this->update_cart();
			$status = self::STATUS_S_OK;
			$response_data = $this->get_cart_summary();
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getTraceAsString());
			$status        = self::STATUS_E_ERROR;
			$response_data = NULL;
		}
		return $this->generate_response($status, $response_data);
	}

	public function set_delivery_method($data)
	{
		try
		{
			$this->cart->delivery_method = (isset($data->delivery_method)) ? $data->delivery_method : '';
			$this->update_cart();
			$status = self::STATUS_S_OK;
			$response_data = $this->get_cart_summary();
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getTraceAsString());
			$status        = self::STATUS_E_ERROR;
			$response_data = NULL;
		}
		return $this->generate_response($status, $response_data);
	}

	public function set_store_id($data)
	{
		try
		{
			$this->cart->store_id = isset($data->store_id) ? $data->store_id : '';
			$this->update_cart();
			$status = self::STATUS_S_OK;
			$response_data = $this->get_cart_summary();
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getTraceAsString());
			$status        = self::STATUS_E_ERROR;
			$response_data = NULL;
		}
		return $this->generate_response($status, $response_data);
	}

	public function set_po_number($data)
	{
		try
		{
			$this->cart->po_number = isset($data->po_number) ? $data->po_number : '';
			$this->update_cart();
			$status = self::STATUS_S_OK;
			$response_data = $this->get_cart_summary();
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getTraceAsString());
			$status        = self::STATUS_E_ERROR;
			$response_data = NULL;
		}
		return $this->generate_response($status, $response_data);
	}

	public function set_delivery_time($data)
	{
		try
		{
			$this->cart->delivery_time = isset($data->delivery_time) ? $data->delivery_time : '';
			$this->update_cart();
			$status        = self::STATUS_S_OK;
			$response_data = $this->get_cart_summary();
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getTraceAsString());
			$status        = self::STATUS_E_ERROR;
			$response_data = NULL;
		}
		return $this->generate_response($status, $response_data);
	}

	public function set_gift_option($data)
    {
        try
        {
            $this->cart->gift_option = isset($data->gift_option) ? $data->gift_option : false;
            $this->update_cart();
            $status        = self::STATUS_S_OK;
            $response_data = $this->get_cart_summary();
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());
            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }
        return $this->generate_response($status, $response_data);
    }

    /**
     * @param stdClass $data
     * @return stdClass
     * @throws Exception
     */
    public function set_location($data)
    {
        try
        {
            $ok = (isset($data->location_id) AND is_int($data->location_id) AND isset($this->cart->lines) AND count((array) $this->cart->lines) > 0);

            if ($ok)
            {
                $location = Model_Location::get($data->location_id, array(array('publish', '=', 1), array('deleted', '=', 0)));

                if (count($location) == 0)
                {
                    $status        = self::STATUS_E_ERROR;
                    $response_data = NULL;
                }
                else
                {
                    $this->cart->location_id = $location['id'];
                    $this->update_cart();

                    $status        = self::STATUS_S_OK;
                    $response_data = $this->get_cart_summary();
                }
            }
            else
            {
                $status        = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }

        return $this->generate_response($status, $response_data);
    }

    /**
     * @param stdClass $data
     * @return stdClass
     */
    public function set_coupon_code($data)
    {
        try
        {
            $ok = (isset($data->coupon_code) AND trim($data->coupon_code) != '' AND isset($this->cart->lines) AND count((array) $this->cart->lines) > 0);

            if ($ok)
            {
                $code = Model_DiscountFormat::get(
                    NULL,
                    array(
                        array('code', '=', $data->coupon_code),
                        array('type_id', 'IN', array(
                            Model_DiscountFormat::DISCOUNT_FORMAT_COUPON_PRICE,
                            Model_DiscountFormat::DISCOUNT_FORMAT_COUPON_SHIPPING
                        )),
                        array('publish', '=', 1),
                        array('deleted', '=', 0)
                    ),
					TRUE
                );

                if (count($code) == 0)
                {
                    $status        = self::STATUS_E_WRONG_COUPON_CODE;
                    $response_data = NULL;
                }
                else
                {
                    $this->cart->code_id = $code[0]['id'];
                    $this->update_cart();

                    $status        = self::STATUS_S_OK;
                    $response_data = $this->get_cart_summary();
                }
            }
            else
            {
                $status        = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }

        return $this->generate_response($status, $response_data);
    }

    /**
     * @return null|stdClass
     */
    public function get_cart_details()
    {
        $session = Session::instance();
        $cart    = $session->get(self::CART_SESSION_ID, NULL);

        return ($cart === NULL OR ! isset($this->cart->lines) OR count((array) $this->cart->lines) == 0) ? NULL : clone $cart;
    }

    /**
     * @param stdClass $data
     * @return stdClass
     */
    public function get_paypal_data($data)
    {
        try
        {
            $settings = Settings::instance();
            $business = FALSE;

            $ok = (isset($this->cart->lines) AND count((array) $this->cart->lines) > 0 AND ($business = $settings->get('paypal_email')) !== FALSE);

            if ($ok)
            {
                $form_data = new stdClass();

                // General
                $form_data->cmd           = '_cart';
                $form_data->upload        = 1;
                $form_data->business      = $business;
                $form_data->currency_code = 'EUR';
                $form_data->no_shipping   = 2;
                $form_data->return        = isset($data->return_url       ) ? $data->return_url        : $_SERVER['HTTP_HOST'];
                $form_data->cancel_return = isset($data->cancel_return_url) ? $data->cancel_return_url : $_SERVER['HTTP_HOST'];
				$form_data->notify_url    = URL::base().'/frontend/payments/paypal_callback/product';
				$form_data->custom        = isset($data->custom) ? $data->custom : '';

                // Products
                $i = 1;

                foreach ($this->cart->lines as $line)
                {
                    $form_data->{'item_name_'.$i} = $line->product->title;
                    $form_data->{'amount_'   .$i} = $line->price_per_unit;
                    $form_data->{'quantity_' .$i} = $line->quantity;

                    $i++;
                }

                // Shipping
                $form_data->{'item_name_'.$i} = 'Postage & Shipping';
                $form_data->{'amount_'   .$i} = $this->cart->shipping_price;
                $form_data->{'quantity_' .$i} = 1;

                // Discounts
                for ($j = 1, $total_price = 0; $j <= $i; $j++)
                {
                    $total_price += $form_data->{'amount_'.$j} * $form_data->{'quantity_'.$j};
                }

                $total_price += $this->cart->vat;
                $total_price = round($total_price, 2);
                $this->cart->final_price = round($this->cart->final_price, 2);
                $form_data->tax_cart = $this->cart->vat;

                $form_data->discount_amount_cart = round($total_price - $this->cart->final_price, 2);

                $status        = self::STATUS_S_OK;
                $response_data = $form_data;
            }
            else
            {
                $status        = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }

        return $this->generate_response($status, $response_data);
    }

    /**
     * @param stdClass $data
     * @return stdClass
     */
    public function get_line_id($data)
    {
        $ok = (isset($data->product_id) AND isset($data->options) AND is_int($data->product_id) AND ($data->options === NULL OR is_object($data->options)));

        if ($ok AND ($i = $this->index_of($data->product_id, $data->options)) != -1)
        {
            $status        = self::STATUS_S_OK;
            $response_data = $i;
        }
        else
        {
            $status        = self::STATUS_E_ERROR;
            $response_data = NULL;
        }

        return $this->generate_response($status, $response_data);
    }

    public function update_stock_count()
    {
        if(Settings::instance()->get('stock_enabled') == "TRUE")
        {
            foreach($this->cart->lines AS $line=>$product)
            {
                if(count($product->options) > 0)
                {
                    foreach($product->options AS $option_key=>$option)
                    {
                        $stock = Model_Product::check_stock_levels($product->product->id,$option->id);
                        if($stock['is_stock_item'] == "true" AND $stock['quantity'] > 0)
                        {
                            Model_Product::decrement_stock_level($product->product->id,$option->id,$product->quantity,$stock['quantity']);
                        }
                    }
                }
                else
                {
                    $q = DB::select('quantity_enabled','quantity')->from(self::PRODUCTS_TABLE)->where('id','=',$product->product->id)->execute()->as_array();
                    if(count($q) == 0)
                    {
                        throw new Exception('Product Not Found');
                    }
                    else
                    {
                        if($q[0]['quantity_enabled'] == '1')
                        {
                            $quantity = $q[0]['quantity'] - $product->quantity;
                            DB::update(self::PRODUCTS_TABLE)->set(array('quantity' => $quantity))->where('id','=',$product->product->id)->execute();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $trigger_name
     * @param $trigger_function
     */
    public function register_payment_successful_listener($trigger_name, $trigger_function)
    {
        $listeners = isset($this->cart->payment_successful_listeners) ? $this->cart->payment_successful_listeners : array();

        $listeners[$trigger_name] = $trigger_function;

        $this->cart->payment_successful_listeners = $listeners;
    }

    /**
     * @return mixed
     */
    public function get_cart_id()
    {
        return $this->cart->id;
    }

    /**
     *
     */
    public function on_successful_payment()
    {
        if (isset($this->cart->payment_successful_listeners) AND is_array($this->cart->payment_successful_listeners))
        {
            foreach ($this->cart->payment_successful_listeners as $function)
            {
                $f = create_function('', $function);

                $f();
            }
        }
    }

    //
    // STATIC/SERVICE FUNCTIONS (DO NOT ABUSE OF THEM)
    //

    public static function empty_cart()
    {
        Session::instance()->delete(self::CART_SESSION_ID);
    }

    public static function get_cart_value($value)
    {
        try
        {
            $cart = new Model_Checkout();

            if ( ! empty($cart) AND ! empty($cart->cart->$value))
            {
                return (string)$cart->cart->$value;
            }
            else
            {
                return '0';
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());
            return 'Error';
        }

    }

    /**
     * @return string Final price or cart price is shipping is not already set
     */
    public static function get_cart_total_price_value()
    {
        try
        {
            $cart = new Model_Checkout();

            if( ! empty($cart->cart->final_price))
            {
                return $cart->cart->final_price;
            }
            else
            {
                return $cart->cart->cart_price;
            }
        }
        catch (Exception $e)
        {
            return '0';
        }
    }

    public static function is_paypal_enabled()
    {
        $settings       = Settings::instance();
		$paypal_enabled = (is_null($settings->get('enable_paypal')) OR $settings->get('enable_paypal') == 1);
        $paypal_email   = $settings->get('paypal_email');

        return ($paypal_enabled AND $paypal_email !== FALSE AND $paypal_email != '');
    }

    public static function get_counties_as_options($selected_id = NULL, $region = NULL)
    {

        try{
            $counties = DB::select()
                ->from('engine_counties')
                ->where('publish', '=', 1)
                ->and_where('deleted', '=', 0);

            if (!is_null($region))
            {
                switch ($region)
                {
                    case 'ROI':
                        $region = 0;
                        break;
                    case 'NI':
                        $region = 1;
                        break;
                }
                $counties = $counties->where('region', '=', $region);
            }
            $counties = $counties->order_by('name')->execute();

            $return = '<option value="">'.__('Select county').'</option>';
            foreach ($counties as $county)
            {
                if ($county['id'] == $selected_id)
                {
                    $selected = ' selected="selected"';
                }
                else
                {
                    $selected = '';
                }
                $return .= '<option value="'.$county['name'].'" data-id="'.$county['id'].'"'.$selected.'>'.$county['name'].'</option>';
            }
            return $return;
        }
        catch(Exception $e)
        {
            return null;
        }

    }

    /**
     * @param int $discount Cent to be discounted from the car price
     */
    final public function add_payback_loyalty_discount($discount){
        try{
            $discount = (float)($discount / 100);

            if(!isset($this->cart->pl_discount)){
                $this->cart->payback_loyalty_discount = $discount;
                $this->update_cart();
            }
        }
        catch (Exception $e){
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());
        }

    }

    /**
     *
     */
    final public function remove_payback_loyalty_discount(){
        try{
            unset($this->cart->payback_loyalty_discount);
            $this->update_cart();
        }
        catch (Exception $e){
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());
        }
    }

    //
    // PRIVATE FUNCTIONS
    //

    /**
     * @param int $status
     * @param mixed $response_data
     * @return stdClass
     */
    private function generate_response($status, $response_data)
    {
        $response = new stdClass();

        $response->status = $status;
        $response->data   = $response_data;

        return $response;
    }

    /**
     * @param array $product
     * @param stdClass $options
     * @return array|null
     */
    private function get_missing_options($product, $options)
    {
        $options         = is_object($options) ? $options : new stdClass();
        $missing_options = array();

        for ($i = 0; $i < count($product['options']); $i++)
        {
            if ($product['options'][$i]['required'] == 1 AND ! isset($options->{$product['options'][$i]['group']}))
            {
                array_push($missing_options, $product['options'][$i]['group']);
            }
        }

        return count($missing_options) == 0 ? NULL : $missing_options;
    }

    /**
     * @param int $product_id
     * @param stdClass $options
     * @return int
     */
    private function index_of($product_id, $options)
    {
        $found = -1;

        if (isset($this->cart->lines))
        {
            foreach ($this->cart->lines as $id => $line)
            {
                if ($line->product->id == $product_id AND count($line->options) == count((array) $options))
                {
                    // Check if the values of the options are the same for the matched line
                    for ($i = 0; $i < count($line->options); $i++)
                    {
                        $option = $line->options[$i];
                        $group  = $option->group;

                        if ( ! (isset($options->{$option->group}) AND $options->$group == $option->id))
                            break;
                    }

                    if ($i == count($line->options))
                    {
                        $found = $id;

                        break;
                    }
                }
            }
        }

        return $found;
    }

    /**
     *
     */
    private function update_cart()
    {
        if ((isset($this->cart->lines) AND count((array) $this->cart->lines) > 0))
        {
            $this->cart->number_of_items = 0;
            $this->cart->cart_price      = 0;
			$this->cart->subtotal        = 0;
			$this->cart->subtotal2       = 0;
			$this->cart->vat             = 0;
			$this->cart->vat_rate        = Settings::instance()->get('vat_rate') ? (float)Settings::instance()->get('vat_rate') : 0;
            $this->cart->discounts       = 0;
            $this->cart->shipping_price  = NULL;
            $this->cart->final_price     = NULL;
            $this->cart->gift_price      = 0;

            // Calculate total weight, number of items and cart price
            $weights = array();
            foreach ($this->cart->lines as $line)
            {
                $line->price = $line->price_per_unit * $line->quantity;

                $this->cart->number_of_items += $line->quantity;
                $this->cart->cart_price      += $line->price;
				if ( ! isset($weights[$line->product->postal_format_id]))
                {
                    $weights[$line->product->postal_format_id] = 0;
                }

                $weights[$line->product->postal_format_id] += $line->product->weight * $line->quantity;
            }

            // Subtotal (no discounts, shipping or VAT)
            $this->cart->subtotal2 = $this->cart->subtotal = $this->cart->cart_price;

            // Shipping price and discounts
			$country = isset($this->cart->country) ? $this->cart->country : '';
            $this->update_shipping_price($weights, $country);
            $applied_discounts = $this->apply_discounts();
            $this->cart->discounts = 0;
            foreach ($applied_discounts as $applied_discount) {
                if (stripos($applied_discount['data']['type'], 'shipping') === false) {
                    $this->cart->discounts += $applied_discount['amount'];
                }
            }
            $this->cart->subtotal2 -= $this->cart->discounts;
			$this->cart->final_price = $this->cart->cart_price;

            // Apply VAT
            if ($this->cart->vat_rate > 0)
            {
                $this->cart->vat = round($this->cart->final_price * $this->cart->vat_rate, 2);
                $this->cart->subtotal2  = $this->cart->final_price;
            }

            if ($this->cart->shipping_price !== NULL)
            {
                $this->cart->subtotal2  += $this->cart->shipping_price;
                $this->cart->final_price = $this->cart->shipping_price + $this->cart->vat + $this->cart->cart_price;
            }

            $giftPrice = Settings::instance()->get('checkout_gift_price');
            $this->cart->gift_price = $giftPrice;

            if (@$this->cart->gift_option) {
                $this->cart->final_price += $giftPrice;
            }

			// Total Discounts
            //$this->cart->discounts = $this->cart->subtotal + $this->cart->vat + $this->cart->shipping_price - ($this->cart->final_price === NULL ? $this->cart->cart_price : $this->cart->final_price);
             /* check cart based discounts*/
			return array(
                'subtotal' => $this->cart->subtotal,
                'shipping_price' => $this->cart->shipping_price,
                'subtotal2' => $this->cart->subtotal2,
                'applied_discounts' => $applied_discounts,
                'vat' => $this->cart->vat,
                'final_price' => $this->cart->final_price,
                'gift_price' => $this->cart->gift_price,
            );
        }
        else
        {
            foreach ($this->cart as $key => $value)
            {
                unset($this->cart->{$key});
            }

            Session::instance()->delete(self::CART_SESSION_ID);
        }
    }

    /**
     * @param array $weights
     */
    private function update_shipping_price($weights, $country_name = NULL)
    {
        $min_shipping_price = 0;
        $max_shipping_price = 0;
        $stack_shipping_price = 0;

        $shipping_price_mode = Settings::instance()->get('product_shipping_price_mode');
        // If the customer is collecting the product from the store
		$is_collecting = (isset($this->cart->delivery_method) AND ($this->cart->delivery_method == 'reserve_and_collect' OR $this->cart->delivery_method == 'pay_and_collect'));

		$canvas = Session::instance()->get('canvas');
		$zone_title = '';
		if (isset($this->cart->zone_id) AND $this->cart->zone_id != '')
		{
			$zone       = new Model_PostageZone($this->cart->zone_id);
			$zone       = $zone->get_data();
			$zone_title = $zone['title'];
		}

        if ( ! is_null($canvas) AND Kohana::$config->load('config')->get('db_id') == 'lionprint' AND strtolower($zone_title) != 'collect in store')
        {
            // Some solution for Lion Print ...
            $area = 0;
            foreach ($canvas as $item)
            {
				if (strtolower($item['canvas_size']) == 'custom')
				{
					$area += $item['width'] * $item['height'];
				}
				else
				{
					$area += Model_Product::get_builder_product_area($item['canvas_size']);
				}
            }

			foreach ($weights as $format => $weight)
			{
				$rate = @Model_PostageRate::get(NULL, array(array('format_id', '=', $format), array('zone_id', '=', $this->cart->zone_id), array('weight_to', '>=', $area), array('publish', '=', 1), array('deleted', '=', 0)));

				if (count($rate) > 0)
				{
                    $lprice = isset($rate[0]['price']) ? $rate[0]['price'] : 0;
                    if ($min_shipping_price == 0) {
                        $min_shipping_price = $lprice;
                    } else {
                        if ($lprice > 0) {
                            $min_shipping_price = min($min_shipping_price, $lprice);
                        }
                    }
                    if ($max_shipping_price == 0) {
                        $max_shipping_price = $lprice;
                    } else {
                        if ($lprice > 0) {
                            $max_shipping_price = max($max_shipping_price, $lprice);
                        }
                    }
                    $stack_shipping_price += $lprice;
				}
			}

            if ($shipping_price_mode == 'Stack') {
                $this->cart->shipping_price = $stack_shipping_price;
            } else if ($shipping_price_mode == 'Minimum') {
                $this->cart->shipping_price = $min_shipping_price;
            } else {
                $this->cart->shipping_price = $max_shipping_price;
            }

            // Old hardcoded rules. This was replaced in LP-191 to use postage rates (above).
			// The "<= 300" rule has not been implemented in the new system
			/*
			if ($area < 4 AND $this->cart->subtotal <= 300)
            {
                $this->cart->shipping_price+= 25;
            }
            else
            {
                $this->cart->shipping_price+= 50;
            }
			*/
        }
		elseif ($is_collecting)
		{
			// No shipping, if the customer is coming to collect the product
			$this->cart->shipping_price = 0;
		}
		elseif (isset($this->cart->delivery_method) AND $this->cart->delivery_method == 'online')
		{
			// No shipping
			$this->cart->shipping_price = 0;
		}
        else
        {
            if (isset($this->cart->zone_id))
            {
                $this->cart->shipping_price = 0;
				$country_id = '';
				if ($country_name)
				{
					$country = ORM::factory('PostageCountry')->where('name', '=', $country_name)->find_published();
					$country_id = $country->id;
				}

                foreach ($weights as $format => $weight)
                {
					$where_clauses =  array(
						array('format_id',   'IN', array($format, '')),
						array('zone_id',     'IN', array($this->cart->zone_id, '')),
						array('weight_from', '<=', $weight),
						array('weight_to',   '>=', $weight),
						array('publish',     '=',  1),
						array('deleted',     '=',  0)
					);
					if ($country_id)
					{
						$where_clauses[] = array(DB::expr("COALESCE(`t1`.`country_id`, '')"),  'IN', array($country_id, ''));
					}

                    $rate = Model_PostageRate::get(NULL, $where_clauses);

                    if (count($rate) > 0)
                    {
                        $lprice = $rate[0]['price'];

                        if ($min_shipping_price == 0) {
                            $min_shipping_price = $lprice;
                        } else {
                            if ($lprice > 0) {
                                $min_shipping_price = min($min_shipping_price, $lprice);
                            }
                        }
                        if ($max_shipping_price == 0) {
                            $max_shipping_price = $lprice;
                        } else {
                            if ($lprice > 0) {
                                $max_shipping_price = max($max_shipping_price, $lprice);
                            }
                        }
                        $stack_shipping_price += $lprice;
                    }
                }

                if ($shipping_price_mode == 'Stack') {
                    $this->cart->shipping_price = $stack_shipping_price;
                } else if ($shipping_price_mode == 'Minimum') {
                    $this->cart->shipping_price = $min_shipping_price;
                } else {
                    $this->cart->shipping_price = $max_shipping_price;
                }
            }
        }
    }

    public function apply_discounts_to_product($product)
    {
        $return = array();
        // Put the item in a pseudo cart, so we can check if it is applicable for any discounts
        $pseudo_cart = new stdClass;
        $pseudo_cart->pseudo = TRUE;
        $pseudo_cart->cart_price = $product['display_offer'] ? $product['offer_price'] : $product['price'];
        $pseudo_cart->shipping_price = 0;
        $pseudo_cart->ignore_shipping_discounts = TRUE;
        $pseudo_cart->ignore_coupon_discounts = TRUE;
        $pseudo_cart->ignore_qty_discounts = TRUE;
        $cart_line = new stdClass;
        $cart_line->quantity = 1;
        $cart_line->product = new stdClass();
        $cart_line->product->id = $product['id'];
        $cart_line->price = $product['price'];
        $pseudo_cart->lines = array($cart_line);

        // Check for discounts
        $checkout_model = new Model_Checkout;
        $return['discounts'] = $checkout_model->apply_discounts($pseudo_cart);
        $return['discount_total'] = 0;
        foreach ($return['discounts'] as $discount)
        {
            $return['discount_total']+= $discount['amount'];
        }

        return $return;
    }

    /**
     *
     */
    public function apply_discounts($cart = NULL)
    {
        if (is_null($cart))
        {
            $cart = $this->cart;
        }

        $applied_discounts = array();
        // Cart price discounts
		$where_clauses = array(
			array('type_id', '=', Model_DiscountFormat::DISCOUNT_FORMAT_AMOUNT_PRICE),
			array('range_from', '<=', $cart->cart_price),
			array('range_to', '>=', $cart->cart_price),
			array('publish', '=', 1),
			array('deleted', '=', 0)
		);
        $discounts = Model_DiscountRate::get(NULL, $where_clauses, TRUE, TRUE, null);

        for ($i = 0; $i < count($discounts); $i++)
        {
            $discount_amount = round($cart->cart_price * ($discounts[$i]['discount_rate'] / 100), 2);
            $cart->cart_price -= $discount_amount;
            $applied_discounts[] = array('data' => $discounts[$i], 'amount' => $discount_amount);
        }

        // individual item category discounts
        foreach($cart->lines as $line) {
            $lcategories = Model_Category::get_product_categories_with_parents($line->product->id);
            $where_clauses = array(
                array('type_id', '=', Model_DiscountFormat::DISCOUNT_FORMAT_AMOUNT_PRICE),
                array('range_from', '<=', $line->price),
                array('range_to', '>=', $line->price),
                array('publish', '=', 1),
                array('deleted', '=', 0)
            );
            $discounts = Model_DiscountRate::get(NULL, $where_clauses, TRUE, TRUE, $lcategories);

            for ($i = 0; $i < count($discounts); $i++)
            {
                $discount_amount = round($line->price * ($discounts[$i]['discount_rate'] / 100), 2);
                $this->cart->cart_price -= $discount_amount;
                $applied_discounts[] = array('data' => $discounts[$i], 'amount' => $discount_amount);
            }
        }

        if ( ! isset($cart->ignore_qty_discounts) OR ! $cart->ignore_qty_discounts)
        {
            // Cart price QTY discounts
            $total_qty = 0;

            foreach($this->cart->lines as $line) {
                $total_qty += $line->quantity;
            }
            $where_clauses = array(
                array('type_id', '=', Model_DiscountFormat::CART_BASED_QTY_DISCOUNT),
                array('range_from', '<=', $total_qty),
                array('range_to', '>=', $total_qty),
                array('publish', '=', 1),
                array('deleted', '=', 0)
            );
            $discounts = Model_DiscountRate::get(NULL, $where_clauses, TRUE, TRUE, null);

            for ($i = 0; $i < count($discounts); $i++)
            {
                $discount_amount = round($this->cart->cart_price * ($discounts[$i]['discount_rate'] / 100), 2);
                $this->cart->cart_price -= $discount_amount;
                $applied_discounts[] = array('data' => $discounts[$i], 'amount' => $discount_amount);
            }

            // individual item category discounts
            foreach($cart->lines as $line) {
                $lcategories = Model_Category::get_product_categories_with_parents($line->product->id);
                $where_clauses = array(
                    array('type_id', '=', Model_DiscountFormat::CART_BASED_QTY_DISCOUNT),
                    array('range_from', '<=', $line->quantity),
                    array('range_to', '>=', $line->quantity),
                    array('publish', '=', 1),
                    array('deleted', '=', 0)
                );
                $discounts = Model_DiscountRate::get(NULL, $where_clauses, TRUE, TRUE, $lcategories);

                for ($i = 0; $i < count($discounts); $i++)
                {
                    $discount_amount = round($line->price * ($discounts[$i]['discount_rate'] / 100), 2);
                    $this->cart->cart_price -= $discount_amount;
                    $applied_discounts[] = array('data' => $discounts[$i], 'amount' => $discount_amount);
                }
            }
        }

        if ( ! isset($cart->ignore_shipping_discounts) OR ! $cart->ignore_shipping_discounts)
        {
            // Shipping price discounts
            $discounts = Model_DiscountRate::get(NULL, array(array('type_id', '=', Model_DiscountFormat::DISCOUNT_FORMAT_AMOUNT_SHIPPING), array('range_from', '<=', $this->cart->cart_price), array('range_to', '>=', $this->cart->cart_price), array('publish', '=', 1), array('deleted', '=', 0)), FALSE, TRUE);

            for ($i = 0; $i < count($discounts); $i++)
            {
                $discount_amount = round($cart->shipping_price * ($discounts[$i]['discount_rate'] / 100), 2);
                $cart->shipping_price -= $discount_amount;
                $applied_discounts[] = array('data' => $discounts[$i], 'amount' => $discount_amount);
            }
        }

        if ( ! isset($cart->ignore_coupon_discounts) OR ! $cart->ignore_coupon_discounts)
        {
            if (isset($cart->code_id))
            {
                // Cart price discounts coupon based
                $discounts = Model_DiscountRate::get(NULL, array(array('type_id', '=', Model_DiscountFormat::DISCOUNT_FORMAT_COUPON_PRICE), array('format_id', '=', $cart->code_id), array('range_from', '<=', $cart->cart_price), array('range_to', '>=', $cart->cart_price), array('publish', '=', 1), array('deleted', '=', 0)), FALSE, TRUE);

                for ($i = 0; $i < count($discounts); $i++)
                {
                    $discount_amount = round($cart->cart_price * ($discounts[$i]['discount_rate'] / 100), 2);
                    $cart->cart_price -= $discount_amount;
                    $applied_discounts[] = array('data' => $discounts[$i], 'amount' => $discount_amount);
                }

                // Shipping price discounts coupon based
                $discounts = Model_DiscountRate::get(NULL, array(array('type_id', '=', Model_DiscountFormat::DISCOUNT_FORMAT_COUPON_SHIPPING), array('format_id', '=', $cart->code_id), array('range_from', '<=', $cart->cart_price), array('publish', '=', 1), array('deleted', '=', 0)), FALSE, TRUE);

                for ($i = 0; $i < count($discounts); $i++)
                {
                    $discount_amount = round($cart->shipping_price * ($discounts[$i]['discount_rate'] / 100), 2);
                    $cart->shipping_price -= $discount_amount;
                    $applied_discounts[] = array('data' => $discounts[$i], 'amount' => $discount_amount);
                }
            }
        }

		// First purchase discounts (price and shipping)
		$user = Auth::instance()->get_user();
		if (isset($user['id']) AND $user['id'])
		{
			// Carts the user has paid for
			$carts = Model_Cart::shopping_history($user['id'], FALSE);
			// No previous carts, means this is the user's first purchase
			if (count($carts) == 0)
			{
				// Discounts within price range
				$where_clauses = array(
					array('type_id', 'IN', array(Model_DiscountFormat::DISCOUNT_FORMAT_FIRST_PURCHASE_PRICE, Model_DiscountFormat::DISCOUNT_FORMAT_FIRST_PURCHASE_SHIPPING)),
					array('range_from', '<=', $cart->cart_price),
					array('range_to', '>=', $cart->cart_price),
					array('publish', '=', 1),
					array('deleted', '=', 0)
				);
				$discounts = Model_DiscountRate::get(NULL, $where_clauses, FALSE, TRUE);
				foreach ($discounts as $discount)
				{
					if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_FIRST_PURCHASE_SHIPPING)
					{
                        $discount_amount = round($cart->shipping_price * ($discount['discount_rate'] / 100), 2);
						$cart->shipping_price -= $discount_amount;
					}
					else
					{
                        $discount_amount = round($cart->cart_price * ($discount['discount_rate'] / 100), 2);
						$cart->cart_price -= $discount_amount;
					}
                    $applied_discounts[] = array('data' => $discounts[$i], 'amount' => $discount_amount);
				}
			}
		}

        // Cart price discount based on payback loyalty
        if(isset($cart->payback_loyalty_discount))
        {
            $cart->cart_price -= $cart->payback_loyalty_discount;
            $applied_discounts[] = array(
                'data' => array('payback' => $cart->payback_loyalty_discount),
                'amount' => $discount_amount
            );
        }

        // Bookings -> Discounts Module
        if(method_exists('Model_Discount','get_all_discounts'))
        {
            $discounts = Model_Discount::get_all_discounts();
            foreach($discounts as $item)
            {
                $discount = Model_Discount::create($item['id'])->apply_discount(3);
            }
        }

        if ( ! isset($cart->pseudo) OR ! $cart->pseudo)
        {
            $this->cart = $cart;
        }

        return $applied_discounts;
    }

    /**
     * @return stdClass
     */
    private function get_cart_summary()
    {
		//echo '<pre>';
		//print_r($this->cart);
        $summary = (isset($this->cart->lines) AND count((array) $this->cart->lines) > 0) ? new stdClass() : NULL;
		if ($summary !== NULL)
        {
			$summary->qty_discount_title = '';
            $summary->qty_discount_percentage = '';
            $summary->cart_based_qty_discount = '';
            $summary->cart_based_price_discount_percentage = '';

            $summary->lines           = array();
            $summary->number_of_items = $this->cart->number_of_items;
            $summary->cart_price      = $this->cart->cart_price;
            $summary->subtotal        = $this->cart->subtotal;
			$summary->subtotal2       = $this->cart->subtotal2;
			$summary->vat             = $this->cart->vat;
			$summary->vat_rate        = $this->cart->vat_rate;
            $summary->discounts       = $this->cart->discounts;
            $summary->shipping_price  = $this->cart->shipping_price;
            $summary->final_price     = $this->cart->final_price;
            $summary->gift_option     = @$this->cart->gift_option ?: false;
            $summary->gift_price      = $this->cart->gift_price;

            foreach ($this->cart->lines as $id => $line)
            {
                $o = new stdClass();

                $o->line_id  = $id;
                $o->quantity = $line->quantity;
                $o->price    = $line->price;

                array_push($summary->lines, $o);
            }
        }

        return $summary;
    }

    /**
     * @param array $product
     * @param stdClass $options
     * @param int $quantity
     * @return bool
     */
    private function add_line($product, $options, $quantity, $timestamp = NULL, $sign_thumbnail = NULL, $override_price = null)
    {
        $ok = (($options_details = $this->get_options_details($product, $options)) !== NULL AND $quantity > 0);

        if ($ok)
        {
            if ( ! isset($this->cart->lines))
            {
                $this->cart->lines = array();
            }

            $ok = (count($this->cart->lines) + 1 == array_push($this->cart->lines, $this->generate_line($product, $options_details, $quantity, $timestamp, $sign_thumbnail, $override_price)));

            if ($ok)
            {
                $this->update_cart();
            }
        }
		// Matrix options
        else
        {
            if ( ! isset($this->cart->lines))
            {
                $this->cart->lines = array();
            }

            $options_array = (array) $options;
            $option_1 = array_slice($options_array, 0, 1);
            $option_2 = array_slice($options_array, 1, 1);
			$options_details = Model_Product::get_matrix_options_price(reset($option_1), reset($option_2), $product['id']);

			// Even though this product is using a matrix, it could also have other options
			$other_options = (object) array_slice($options_array, 2);
			if (count($other_options) > 0)
			{
				$other_option_details = $this->get_options_details($product, $other_options);
				if ( ! is_null($other_option_details))
				{
					$options_details = array_merge($options_details, $other_option_details);
				}
			}

            if($product['builder'] == 1)
            {
                $canvas_details = Model_Option::get_options_by_field('options.id',$option_1['option1']);
                $laminate_prices = array('A0' => 9,'A1' => 5,'A2'=>3,'A3'=>2,'A4'=>1.50,'A5'=>1);
                $adhesive_prices = array('A0' => 9,'A1' => 5,'A2'=>3,'A3'=>2,'A4'=>1.50,'A5'=>1);
            }

            if(isset($options->laminate) AND $options->laminate != "None")
            {
                $laminate_details = Model_Option::get_options_by_field('options.id',$options->laminate);
                $options_details[] = array('id' => $options->laminate,'group' => 'Laminate','label' => $laminate_details[0]['label'],'price' => $laminate_prices[$canvas_details[0]['value']]);
            }

            if(isset($options->adhesive) AND $options->adhesive != "No")
            {
                $options_details[] = array('id' => $options->laminate,'group' => 'Adhesive','label' => 'Adhesive','price' => $adhesive_prices[$canvas_details[0]['label']]);
            }

            $ok = (count($this->cart->lines) + 1 == array_push($this->cart->lines, $this->generate_line($product, $options_details, $quantity, $timestamp, $sign_thumbnail)));

            if ($ok)
            {
                $this->update_cart();
            }

        }

        return $ok;
    }

    /**
     * @param int $line_id
     * @param int $operation_id
     * @param int $quantity
     * @throws Exception
     * @return bool
     */
    private function modify_line($line_id, $operation_id, $quantity)
    {
        $ok = FALSE;

        switch ($operation_id)
        {
            case self::MODIFY_CART_ADD:
                // Add
                if ($ok = ($quantity > 0))
                {
                    $this->cart->lines[$line_id]->quantity += $quantity;
                }
                break;

            case self::MODIFY_CART_REMOVE:
                // Remove
                if ($ok = ($quantity > 0 AND $quantity <= $this->cart->lines[$line_id]->quantity))
                {
                    $this->cart->lines[$line_id]->quantity -= $quantity;

                    if ($this->cart->lines[$line_id]->quantity == 0)
                    {
                        unset($this->cart->lines[$line_id]);
                    }
                }
                break;

            case self::MODIFY_CART_SET:
                // Set
                if ($ok = ($quantity > 0))
                {
                    $this->cart->lines[$line_id]->quantity  = $quantity;
                }
                break;

            default:
                break;
        }

        if ($ok)
        {
            $this->update_cart();
        }

        return $ok;
    }

    /**
     * @param int $line_id
     * @return bool
     */
    private function delete_line($line_id)
    {
        $builder_product_id = $this->cart->lines[$line_id]->product->builder == "1" ? $this->cart->lines[$line_id]->product->timestamp : NULL;
        $canvas = Session::instance()->get('canvas',NULL);

        unset($this->cart->lines[$line_id]);

        if(!is_null($canvas) AND !is_null($builder_product_id))
        {
            unset($canvas[Model_Product::get_product_canvas_index($builder_product_id)]);
            Session::instance()->set('canvas',$canvas);
        }

        $this->update_cart();

        return TRUE;
    }

    /**
     * @param array $product
     * @param stdClass $options
     * @return array
     */
    private function get_options_details($product, $options)
    {
        $array = array();

        if (is_object($options))
        {
            foreach ($options as $group => $id)
            {
                // Check if the current option is an option for the current product
                for ($i = 0; $i < count($product['options']) AND $product['options'][$i]['group'] != $group; $i++);

                if ($i == count($product['options']))
                {
                    $array = NULL;
                    break;
                }

				// Most options' values are the option ID
				if (is_numeric($id) && (strpos($group, 'custom') === false))
				{
					$option = Model_Option::get($id);
					// Check if the current option exists and the group matches with the specified group
					if ( ! isset($option['group']) OR $option['group'] != $group)
					{
						$array = NULL;
						break;
					}

					//now check to see if this option has been setup as a stock option.
					if(Settings::instance()->get('stock_enabled') == "TRUE")
					{
						$stock = Model_Product::check_stock_levels($product['id'],$id);
						if($stock['is_stock_item'] == "true")
						{
							$price = Model_Product::check_stock_price($product['id'],$id);
							if($stock['quantity'] == 0)
							{
								throw new Exception("ERROR: PRODUCT OUT OF STOCK.");
							}
							elseif($price != FALSE)
							{
								$option['price'] = $price;
							}
						}
					}

				}
				// A custom input does not use an option ID. The user enters text. e.g. #hashtag on Mr-Tee
				else
				{
					$option['id']    = '';
					$option['label'] = $id;
					$option['group'] = $group;
					$option['value'] = $id;
					$option['price'] = 0; // Temporary; add DB query later.
				}
				$array = is_array($array) ? $array : array();

				array_push($array, $option);
            }
        }

        return $array;
    }

    /**
     * @param array $product
     * @param array $options
     * @param int $quantity
     * @throws Exception
     * @return stdClass
     */
    private function generate_line($product, $options, $quantity, $timestamp = NULL, $sign_thumbnail = NULL, $override_price = null)
    {
        $line = new stdClass();

        // Product
        $line->product = new stdClass();

        $is_builder = ($product['builder'] == 1);
        $no_images  = (sizeof($product['images']) == 0);
        $no_layers  = ($product['sign_builder_layers'] == '[]' OR $product['sign_builder_layers'] == '');

        $line->product->id                = $product['id'              ];
        $line->product->title             = $product['title'           ];
		$line->product->url_title         = $product['url_title'       ];
        $line->product->product_code      = $product['product_code'    ];
        $line->product->price             = $product['price'           ];
        $line->product->display_price     = $product['display_offer'   ];
        $line->product->offer_price       = $product['offer_price'     ];
        $line->product->display_offer     = $product['display_offer'   ];
        $line->product->weight            = $product['weight'          ];
        $line->product->postal_format_id  = $product['postal_format_id'];
		$line->product->use_postage       = $product['use_postage'     ];
        $line->product->builder           = $product['builder'         ];
        $line->product->from_scratch_sign = ($is_builder AND $no_images AND $no_layers);
		$line->product->timestamp         = ( ! is_null($timestamp))      ? $timestamp      : date('U');
		$line->product->sign_thumbnail    = ( ! is_null($sign_thumbnail)) ? $sign_thumbnail : NULL;
		
		if($override_price !== null){
			$line->product->price = $override_price;
		}

        // Options
        $line->options = array();

        for ($i = 0; $i < count($options); $i++)
        {
            $option = new stdClass();

            $option->id    = $options[$i]['id'   ];
            $option->label = $options[$i]['label'];
            $option->group = $options[$i]['group'];
            $option->price = $options[$i]['price'];
            if (isset($options[$i]['id2']))
            {
                $option->id2 = $options[$i]['id2'];
            }

            array_push($line->options, $option);
        }

        // Quantity
        $line->quantity = $quantity;

        // Price Per Unit
        $line->price_per_unit = ($line->product->display_offer == 1) ? $line->product->offer_price : $line->product->price;

        for ($i = 0; $i < count($line->options); $i++)
        {
            $line->price_per_unit += $line->options[$i]->price;
		}

		if ($product['builder'] == 1)
		{
			// Build a dummy cart using just this item in its quantity and get the discount
			$product = new stdClass();
			$product->price = $line->price_per_unit;
			$product->quantity = $quantity;
			$lines2 = array($product);
			$cart = new stdClass();
			$cart->lines = $lines2;
			Session::instance()->set('offers_cart', $cart);
			$discounts = Model_Discount::get_all_discounts();
			$discount_amount = 0;
			foreach ($discounts as $item)
			{
				$discount = Model_Discount::create($item['id']);
				$discount_amount += $discount->apply_discount(9);
				if ($discount_amount != 0)
				{
					$discount_to_apply = $discount;
					break;
				}
			}
			if (isset($discount_to_apply))
			{
				$line->price_per_unit *= (1 - $discount_to_apply->get_y() / 100);
			}
		}

        return $line;
    }
	
	public function save_to_session()
	{
		$session = Session::instance();
		$session->set(self::CART_SESSION_ID, $this->cart);
	}
	
	private function set_discount_options_for_checkout_page()
	{
		$discount_type_id = Model_DiscountFormat::CART_BASED_PRICE_DISCOUNT; //for  cart based price discount
		$model_product = new Model_Product();
		$chk_cart_discounts = $model_product->get_discount_data_by_id($discount_type_id);

		$cart_price = $this->cart->cart_price;
		$number_of_items = $this->cart->number_of_items;

		$this->cart->checkout_cart_based_discount_title = '';
		$this->cart->checkout_cart_based_shipping_free_title = '';
		$this->cart->checkout_cart_based_qty_discount_title = '';

		if(is_array($chk_cart_discounts) && sizeof($chk_cart_discounts) > 0 && !empty($chk_cart_discounts) && isset($chk_cart_discounts[0])){
			$range_from = $chk_cart_discounts[0]['range_from'];
			$range_to = $chk_cart_discounts[0]['range_to'];

			if($cart_price >= $range_from && $cart_price < $range_to)
			{
				$apply_discount_price = $range_to - $cart_price;
				$this->cart->checkout_cart_based_discount_title = "You can get " .$chk_cart_discounts[0]['discount_rate_percentage']." discount if you buy &euro; ".$apply_discount_price." more.";
			}
		}

		/* check if cart based free shipping available*/
		$free_shipping_type_id = Model_DiscountFormat::CART_BASED_FREE_SHIPPING; //for  cart based free shipping
		$free_shipping_arr = $model_product->get_discount_data_by_id($free_shipping_type_id);

		if(is_array($free_shipping_arr) && sizeof($free_shipping_arr) > 0 && isset($free_shipping_arr[0])){
			$cart_based_free_range_from = $free_shipping_arr[0]['range_from'];
			$cart_based_free_range_to = $free_shipping_arr[0]['range_to'];

			if($cart_price >= $cart_based_free_range_from && $cart_price < $cart_based_free_range_to){
				$apply_free_shipping_price = $cart_based_free_range_to - $cart_price;
				$this->cart->checkout_cart_based_shipping_free_title = "You can get free shipping if you buy &euro; ".$apply_free_shipping_price." more.";
			}
		}

		/* check if cart based qty discount available*/
		$qty_type_id = Model_DiscountFormat::CART_BASED_QTY_DISCOUNT; //for  cart based free shipping
		$qty_arr = $model_product->get_discount_data_by_id($qty_type_id);

		if(is_array($qty_arr) && sizeof($qty_arr) > 0 && isset($qty_arr[0])){
			$cart_based_qty_range_from = $qty_arr[0]['range_from'];
			$cart_based_qty_range_to = $qty_arr[0]['range_to'];
			if($number_of_items >= $cart_based_qty_range_from && $number_of_items < $cart_based_qty_range_to){
				$apply_qty_discount_qty = $cart_based_qty_range_to - $number_of_items;
				$this->cart->checkout_cart_based_qty_discount_title = "You can get ". $qty_arr[0]['discount_rate_percentage']." discount when you buy ". $apply_qty_discount_qty ." more products.";
			}
		}
	}

	public function render_discount_html()
    {
        if (isset($this->cart) && !empty($this->cart) && isset($this->cart->cart_price) && !empty($this->cart->cart_price) && isset($this->cart->number_of_items) && !empty($this->cart->number_of_items)) {
            $cart_price = $this->cart->cart_price;
            $number_of_items = $this->cart->number_of_items;

            $user = Auth::instance()->get_user();
            $data['user'] = $user;
            $user_previous_carts = array();
            if (@$user['id']) {
                $user_previous_carts = Model_Cart::shopping_history($user['id'], false);
            }
            $recommend_discounts = array();
            $discounts = DB::select('t1.format_id', 't1.range_from', 't1.range_to', 't1.discount_rate_percentage', 't2.id', 't2.title', 't2.type_id')
                ->from(array(Model_Product::TABLE_PRODUCT_DISCOUNT_RATE, 't1'))
                ->join(array(Model_Product::TABLE_PRODUCT_DISCOUNT_FORMAT, 't2'), 'INNER')
                ->on('t1.format_id', '=', 't2.id')
                ->where('t1.deleted', '=', 0)
                ->and_where('t1.publish', '=', 1)
                ->and_where('t2.publish', '=', 1)
                ->and_where('t2.deleted', '=', 0)
                ->order_by('t2.type_id', 'ASC')
                ->order_by('t1.range_from', 'ASC')->execute()->as_array();

            foreach($discounts as $discount) {
                if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_AMOUNT_PRICE) {
                    if($cart_price < $discount['range_from'])
                    {
                        $recommend_discounts[] = $discount;
                    }
                } else if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_AMOUNT_SHIPPING) {
                    if($cart_price < $discount['range_from'])
                    {
                        $recommend_discounts[] = $discount;
                    }
                } else if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_FIRST_PURCHASE_PRICE) {
                    if (@$user['id'] && count($user_previous_carts) == 0) {
                        $recommend_discounts[] = $discount;
                    }
                } else if ($discount['type_id'] == Model_DiscountFormat::DISCOUNT_FORMAT_FIRST_PURCHASE_SHIPPING) {
                    if (@$user['id'] && count($user_previous_carts) == 0) {
                        $recommend_discounts[] = $discount;
                    }
                } else if ($discount['type_id'] == Model_DiscountFormat::CART_BASED_QTY_DISCOUNT) {
                    if($number_of_items < $discount['range_from'])
                    {
                        $recommend_discounts[] = $discount;
                    }
                }
            }

            $data['recommend_discounts'] = $recommend_discounts;
            $data['cart_price'] = $this->cart->cart_price;
            $data['number_of_items'] = $this->cart->number_of_items;

            return View::factory('front_end/ajax_get_discount_options_html', $data);
        }
    }

	public function render_applied_discount_html()
	{
		if(isset($this->cart)  && !empty($this->cart) && isset($this->cart->cart_price) && !empty($this->cart->cart_price) && isset($this->cart->number_of_items) && !empty($this->cart->number_of_items))
		{
			$result = $this->update_cart();
			return View::factory('front_end/ajax_get_applied_discount_html', $result);
		}
	}

    public static function shipping_modes($selected = null)
    {
        return html::optionsFromArray(
            array(
                'Stack' => __('Stack'),
                'Maximum' => __('Maximum'),
                'Minimum' => __('Minimum'),
            ),
            $selected
        );
    }
}
