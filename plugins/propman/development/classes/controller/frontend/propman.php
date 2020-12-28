<?php
Class Controller_Frontend_Propman extends Controller{

    function before()
    {
        parent::before();
    }

    public function action_index()
    {
    }

	public function action_calculate_price()
	{
		$post = $this->request->post();
		$propertyId = $post['propertyId'];
		$checkin = date::dmy_to_ymd($post['checkin']);
		$checkout = date::dmy_to_ymd($post['checkout']);
		$guests = $post['guests'];

		$result = array();
        if (strtotime($checkin) < time()) {
            $result['available'] = false;
            $result['price'] = array(
                'error' => 'invalid',
                'reason' => __('Past dates are not allowed')
            );
        } else if (strtotime($checkin) >= strtotime($checkout)) {
            $result['available'] = false;
            $result['price'] = array(
                'error' => 'invalid',
                'reason' => __('Check out must be after check in')
            );
        } else {
            $result['available'] = Model_Propman::isAvailable($propertyId, $checkin, $checkout);
            if ($result['available']) {
                $result['price'] = Model_Propman::calculatePrice($propertyId, $checkin, $checkout, $guests);
                if ($result['price']['error']) {
                    $result['available'] = false;
                }
            }
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
	}

    public function action_test_booking_message()
    {
        $bookingId = $this->request->query('booking_id');
        Model_Propman::sendBookingMessages($bookingId);
        exit();
    }

    public function action_save_booking()
    {
        $post = $this->request->post();
        $result = Model_Propman::saveBookingFromPost($post);
        if (isset($result['payment']) AND ($result['payment'] == 'done' OR $result['payment'] == 'continue')) {
            if ($result['booking_id'] && $result['payment'] == 'done') {
                Model_Propman::sendBookingMessages($result['booking_id']);
            }
        }
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

	public function action_process_booking()
	{
        $post = $this->request->post();
        header('content-type: text/plain');print_r($post);exit();
		$property               = ORM::factory('Propman')->where('id', '=', $this->request->post('property_id'))->find_published();
		$realex_enabled         = (Settings::instance()->get('enable_realex') == 1);
		$check_in_date          = $this->request->post('check_in');
		$check_out_date         = $this->request->post('check_out');
		$number_of_guests       = $this->request->post('guests');
		$amount_to_pay          = $property->calculate_price($check_in_date, $check_out_date, $number_of_guests);
		$email                  = trim($this->request->post('email'));
		$errors                 = array();

		if ( ! $email)
		{
			$errors[] = __('You must supply a valid email address');
		}

		if ($realex_enabled)
		{
			$payment_process_result = Model_PaymentProcessorRealex::process_realex_payment(
				$amount_to_pay,
				'Eur',
				$this->request->post('ccNum'),
				$this->request->post('ccExpMM'),
				$this->request->post('ccExpYY'),
				$this->request->post('ccType'),
				$this->request->post('ccv'),
				$this->request->post('ccName')
			);

			if (isset($payment_process_result['response']) AND $payment_process_result['response'] == '00')
			{
				$property->send_booking_messages($this->request->post());
			}
		}
	}

	public function action_ajax_paypal_booking()
	{
		$this->auto_render = FALSE;
		parse_str(Kohana::sanitize($this->request->post('form_data')), $form_data);
		$form_data         = (Object) $form_data;
		$email             = isset($form_data->email)     ? $form_data->email     : '';
		$check_in_date     = isset($form_data->check_in)  ? $form_data->check_in  : '';
		$check_out_date    = isset($form_data->check_out) ? $form_data->check_out : '';
		$number_of_guests  = isset($form_data->email)     ? $form_data->email     : '';

		$property          = ORM::factory('Propman')->where('id', '=', $form_data->property_id)->find_published();
		$amount_to_pay     = $property->calculate_price($form_data->check_in, $form_data->check_out, $form_data->guests);

		// PayPal data
		$paypal_data                  = new stdClass();
		$paypal_data->cmd             = '_cart';
		$paypal_data->upload          = 1;
		$paypal_data->business        = Settings::instance()->get('paypal_email');
		$paypal_data->currency_code   = 'EUR';
		$paypal_data->no_shipping     = 2;
		$paypal_data->return          = isset($form_data->return_url)        ? $form_data->return_url        : $_SERVER['HTTP_HOST'];
		$paypal_data->cancel_return   = isset($form_data->cancel_return_url) ? $form_data->cancel_return_url : $_SERVER['HTTP_HOST'];
		$paypal_data->notify_url      = URL::base().'/frontend/propman/paypal_callback/'.$property->id.'?email='.$email.'&check_in='.$check_in_date.'&check_out='.$check_out_date.'guests='.$number_of_guests;
		$paypal_data->custom          = isset($form_data->custom)  ? $form_data->custom  : '';
		$paypal_data->{'item_name_1'} = $property->id.': '.$property->ref_code.' '.$property->name;
		$paypal_data->{'amount_1'   } = $amount_to_pay;
		$paypal_data->{'quantity_1' } = 1;

		// Return data
		$return                  = new stdClass;
		$return->status          = TRUE;
		$return->data            = $paypal_data;
		$return->data->test_mode = (Settings::instance()->get('paypal_test_mode') == 1);

		$this->response->body(json_encode($return));
	}

	// Controller opened on PayPal's end after a payment has processed.
	// This will not work on .dev or .test
	public function action_paypal_callback()
	{
        $this->auto_render = false;
        Model_Propman::saveIPNLog();
        $ipn = $this->request->post();
        $payment = Model_Propman::paypalComplete($ipn);
        if ($payment) {
            Model_Propman::sendBookingMessages($payment['booking_id']);
        }
        echo "done";
	}

	// Add a property to the wishlist
	public function action_ajax_add_to_wishlist()
	{
		// Add the ID to an array, which is stored in a cookie.
		// Do not add the ID, if it is already in the array.
		$id       = $this->request->param('id');
		$wishlist = (array) json_decode(Cookie::get('propman_wishlist', '[]'));
		if (is_numeric($id) AND ! in_array($id, $wishlist))
		{
			$wishlist[] = $id;
		}
		$wishlist = (array) $wishlist;
		Cookie::set('propman_wishlist', json_encode($wishlist));

		$this->auto_render = FALSE;
	}

	// Remove a property from the wishlist
	public function action_ajax_remove_from_wishlist()
	{
		// Remove the ID from the cookie array, if it is in the array
		$id       = $this->request->param('id');
		$wishlist = (array) json_decode(Cookie::get('propman_wishlist', '[]'));

		if (($key = array_search($id, $wishlist)) !== FALSE)
		{
			unset($wishlist[$key]);
		}
		$wishlist = (array) $wishlist;
		Cookie::set('propman_wishlist', json_encode($wishlist));

		$this->auto_render = FALSE;
	}

	// View all wishlist items
	public function action_ajax_view_wishlist()
	{
		// Get property IDs from the cookie. Load the details for each property and store in a view
		$property_ids = (array) json_decode(Cookie::get('propman_wishlist', '[]'));
		$properties   = array();
		foreach ($property_ids as $id)
		{
			$properties[] = ORM::factory('Propman')->where('id', '=', $id)->find_published();
		}
		$view = View::factory('view_wishlist')->set('properties', $properties);

		$this->auto_render = FALSE;
		echo $view;
	}

    public function action_unavailable_dates()
    {
        $post = $this->request->post();
        $property = $post['property_id'];
        $today = date('Y-m-d');
        $dates = ORM::factory('Propman_PropertyCalendar')->where('property_id','=',$property)->where('available','=',0)->where('date','>=',$today)->find_all()->as_array();
        $booked_days = Model_Propman::getBookedDays($property);
        $unavailable = array();
        foreach($dates as $date)
        {
            $unavailable[] = $date->date;
        }
        $result = array_merge($booked_days,$unavailable);
        foreach ($result as $key=>$r)
        {
            $result[$key] = date('j-n-Y',strtotime($r));
        }
        $ratecard_dates = Model_Propman::property_rate_card_dates($property);
        $return = array('not_available'=>$result,'ratecard_date'=>$ratecard_dates);
		$this->auto_render = false;
		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($return);
    }

    public function action_cron()
    {
        $this->auto_render = false;
        header('Content-Type: text/plain; charset=utf-8');
        $lock_filename = "/tmp/plugin.propman.lock";
        $lock = fopen($lock_filename, "c+");
        if ($lock) {
            if (flock($lock, LOCK_EX|LOCK_NB)) {
                set_time_limit(0);
                $started = time();
                ob_start();
                $output = ob_get_clean();

                try {
                    Model_Propman::bookingsOutstandingReminder();
                    $finished = time();
                } catch (Exception $exc) {
                    print_r($exc);
                    $finished = null;
                }
                Model_Cron::insert_log('messaging', array(
                    'started' => date('Y-m-d H:i:s', $started),
                    'finished' => $finished ? date('Y-m-d H:i:s', $finished) : null,
                    'output' => $output
                ));
                echo $output;
                flock($lock, LOCK_UN);
                fclose($lock);
                unlink($lock_filename);
            } else {
                fclose($lock);
            }
        } else {
        }
        echo "Messaging Properties Completed";
    }

    public function action_getbalance()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $bookingId = $this->request->post('bookingId');
        if ($bookingId) {
            $balance = Model_Propman::bookingGetBalance($bookingId);
        } else {
            $balance = false;
        }
        echo json_encode($balance);
    }

	public function action_custom_payment()
	{
        $view = View::factory('templates/accommodation/custompayment');
        $view->page_data = array(
            'seo_description' => 'test',
            'seo_keywords' => 'test',
            'title' => 'Payment',
            'content' => '',
            'layout' => 'payment',
            'banner_photo' => '',
            'theme_home_page' => '',
        );
        return $view;
	}

    public function action_save_balance_payment()
    {
        $post = $this->request->post();
        $result = Model_Propman::saveBalancePaymentFromPost($post);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_ajax_deals()
    {
        $params = $this->request->query();
        $params['deals'] = 1;
        $result = Model_Propman::search_results($params);
        $deals = array(
            'properties' => array(),
            'page' => $result['page'],
            'count' => $result['count'],
            'results_found' => $result['results_found']
        );
        foreach ($result['results']->as_array() as $property) {
            $deals['properties'][] = (array)$property;
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($deals);
    }
}
?>