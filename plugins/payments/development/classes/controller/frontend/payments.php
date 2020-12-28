<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Payments extends Controller
{
	public $payment_log_id = null;

	public function action_payment_processor_ib_pay()
    {
		$this->payment_log_id = null;
        $session            = Session::instance();
        $response['status'] = 'success'; //error status for error handler
        $invoicepayment     = FALSE; //flag to toggle if we need a cart or total only payment process
        $stripe_payment     = (Settings::instance()->get('stripe_enabled') == 'TRUE');
        $all_post           = $this->request->post();
        $post               = json_decode($this->request->post('checkout'));
        $realexpayment      = new Model_Realexpayments();
		$sagepay_enabled    = (Settings::instance()->get('sagepay') == 1);
		$realex_enabled     = (Settings::instance()->get('enable_realex') == 1);
		$boipa_enabled      = (Settings::instance()->get('boipa_enable') == 1);
		$sagepay_payment    = null;
		$boipa              = null;
		$non_payment        = FALSE;
		$user               = Auth::instance()->get_user();
        $process_sagepay    = false;
		$processed_boipa    = false;
        $is_donation        = $this->request->post('is_donation');

		if($user && isset($user['id'])){
			$post->customer_user_id = $user['id'];
		}

        if(!isset($all_post['payment_type']))
        {
            if($sagepay_enabled){
				$all_post['payment_type'] = 'sagepay';
				$sagepay_payment = new Model_Sagepay();
			}
			if($realex_enabled){
	            $all_post['payment_type'] = 'realex';
			}
			if($boipa_enabled) {
				$all_post['payment_type'] = 'boipa';
				$boipa = new Model_Boipa();
			}
            $stripe_payment = FALSE;
        }

        //check for captcha is valid
        $formprocessor_model = new Model_Formprocessor();
        if (!$formprocessor_model->captcha_check($post))
        {
            $response['status'] = 'error';
            $response['message'] = "Your CAPTCHA failed validation, please verify you entered the correct details.";
            $page = new Model_Pages();
            $response['redirect'] = URL::site().$page->get_page_by_id(Settings::instance()->get('captcha_fail_page'));
        }

		// Validation if the user is collecting in store
		if (isset($post->delivery_method) AND ($post->delivery_method == 'reserve_and_collect' OR $post->delivery_method == 'pay_and_collect'))
		{
			$validpayment_result = TRUE;
			if ( ! isset($post->store_id) OR $post->store_id == '')
			{
				$validpayment_result = FALSE;
				$response['status'] = 'error';
			}
		}

		$reservation_only = (isset($post->delivery_method) AND $post->delivery_method == 'reserve_and_collect');
		$credit_account   = (isset($post->delivery_method) AND $post->delivery_method == 'credit_account' AND isset($user['credit_account']) AND ($user['credit_account'] == 1));

		// No payments, if just booking a reservation or if using the credit account option
		if ($reservation_only OR $credit_account OR (isset($all_post['donation_type']) AND $all_post['donation_type'] == 'direct_debit'))
		{
			$non_payment = TRUE;
			$validpayment_result = isset($validpayment_result) ? $validpayment_result : TRUE;
		}
        // Validation if paying through Stripe
        elseif ($stripe_payment AND $all_post['payment_type'] == "stripe")
        {
            $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
            $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
            $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');

            if (@$all_post['stripe_payment_intent_id']) {
                require_once APPPATH . '/vendor/stripe6/init.php';
                \Stripe\Stripe::setApiKey($stripe['secret_key']);

                $intent = \Stripe\PaymentIntent::retrieve($all_post['stripe_payment_intent_id']);
                if ($intent->status == 'succeeded') {
                    $validpayment_result = true;
                } else {
                    $validpayment_result = false;
                    $response['status'] = 'error';
                }
            } else {
                require_once APPPATH . '/vendor/stripe/lib/Stripe.php';
                Stripe::setApiKey($stripe['secret_key']);

                $validpayment_result = false;
                try {
                    $token = $all_post['token'];
                    $customer = Stripe_Customer::create(array(
                        'description' => 'Customer for ' . $token['email'],
                        'card' => $token['id']
                    ));
                    if ($customer->id) {
                        $validpayment_result = true;
                    }
                } catch (Exception $e) {
                    Log::instance()->add(Log::ERROR, $e->getMessage() . "\n" . $e->getTraceAsString());
                    $validpayment_result = false;
                    $response['status'] = 'error';
                }
            }
        }
        // Validation if paying through SagePay
		elseif ($sagepay_payment AND $all_post['payment_type'] == "sagepay")
		{
			//check if post object has a valid cart and products listing setup
            $validpayment_result = $sagepay_payment->validate($post);
		}
		// Validation if paying through SagePay
		elseif ($boipa_enabled AND $all_post['payment_type'] == "boipa")
		{
			//check if post object has a valid cart and products listing setup
			$validpayment_result = $boipa->validate($post);
		}
        // Validation if paying through Realex
        else
        {
            //check if post object has a valid cart and products listing setup
            $validpayment_result = $realexpayment->validate($post);
        }

		// check if we are using a non cart/product payment process
        if (isset($post->payment_total) AND $post->payment_total != "")
        {
            $invoicepayment = TRUE;
            //override payment validation as no need to validate cart or products
            $validpayment_result = TRUE;
        }

        // Validate payment
        if ($validpayment_result AND $response['status'] != 'error')
        {
            $charge = null;
            $stripe_payment_result = FALSE;
			$boipa_payment_result = false;
			$article_payment = isset($all_post['new_article']) AND $all_post['new_article'] AND class_exists('Model_Article');

			if ($article_payment)
			{
				$article = new Model_Article();
				parse_str($all_post['article_data'], $article_data);
				$article_data['deleted'] = 1; // This will be undeleted, if the payment is successful
				$valid_article = $article->set_data($article_data)->save();
				if ( ! $valid_article)
				{
					$response['status']  = 'error';
					$response['message'] = 'Error with the article submission. Please review the form fields.';
					$this->response->body(json_encode($response));
					exit();
				}
			}

            if ($stripe_payment)
            {
                if (isset($customer)) {
                    try {
                        // Pay to create an article. e.g. ReadersWriters
                        if ($article_payment) {
                            $price = 1;
                        } else {
                            $checkout_model = new Model_Checkout();
                            $products = $checkout_model->get_cart_details();
                            $price = (isset($post->payment_total)) ? $post->payment_total : $products->final_price;
                        }
                        $charge = @Stripe_Charge::create(array(
                            'customer' => $customer->id,
                            'amount' => $price * 100, // convert to cents
                            'currency' => 'eur'
                        ));
                        $stripe_payment_result = true;
                    } catch (Stripe_CardError $e) {
                        $stripe_payment_result = false;
                    }
                } else {
                    $stripe_payment_result = $validpayment_result;
                }
            }

			elseif ($non_payment)
			{


			}
			else if($sagepay_payment){

				$payment_process_result = ($invoicepayment) ? $sagepay_payment->process_invoice_payment($post) : $sagepay_payment->process_payment($post);
				if(isset($payment_process_result['errors'])){
					$process_sagepay = false;
				} else {
					if($payment_process_result['Status'] == '3DAUTH' && $payment_process_result['3DSecureStatus'] == 'OK'){
						echo json_encode($payment_process_result);
						exit();
					} else if($payment_process_result['Status'] == 'OK'){
						$process_sagepay = true;
					} else {
						$payment_process_result['message'] = $payment_process_result['StatusDetail'];
					}
				}
			}
			else if($boipa_enabled AND $all_post['payment_type'] == "boipa"){
				$boipa_result = $boipa->process_payment($post);
				if(isset($boipa_result['Response']) && $boipa_result['Response'] == 'Approved' && $boipa_result['ProcReturnCode'] == '00'){
					$processed_boipa = true;
                    $post->realex_status = 'BOIPA TX: ' . $boipa_result['TransId'];
				} else {
					$processed_boipa = false;
                    $payment_process_result['message'] = $boipa_result['ErrMsg'];
				}
			}
			else
            {
				$payment_process_result = ($invoicepayment) ? $realexpayment->process_invoice_payment($post) : $realexpayment->proccess_payment($post);
            }

            //Process payment
			$process_stripe  = ($stripe_payment AND $stripe_payment_result);
			$process_invoice = ($realexpayment->is_free($invoicepayment) XOR isset($post->payment_total));
			$process_realex  = (isset($payment_process_result) AND isset($payment_process_result['response']) AND $payment_process_result['response'] == '00');
            if ($process_stripe OR $process_invoice OR $process_realex OR $process_sagepay OR $processed_boipa)
            {
                Model_Product::prepare_pdf();
                // Sign/T-Shirt Builder Checking...

                if ($stripe_payment AND isset($all_post['payment_type']) AND $all_post['payment_type'] == "stripe")
                {
                    $post->email = $all_post['token']['card']['name'];
                }

				if ($article_payment)
				{
					if ($article->set_deleted(0)->save())
					{
						$customer = Auth::instance()->get_user();
						$article->send_mail_seller($customer);
						$article->send_mail_customer($customer);
						IBHelpers::set_message('Article #'.$article->get_id().' successfully created. Please check your email for more details.','success');

						$response['status'] = 'success';
						$response['redirect'] = '/admin/articles/edit/?id='.$article->get_id();
					}
					else
					{
						$response['error'] = 'success';
						$response['message'] = 'Error updating article. Please contact support.';
					}

				}
                else
				{
					if ( ! $invoicepayment)
					{
						$realexpayment->send_mail_seller($post, $all_post);
						$realexpayment->send_mail_customer($post, NULL, $all_post);
					}
					else
					{
                        $now = date::now();
						if (@$post->plan_payment_id) {
							Model_KES_Bookings::make_payment_for_plan_payment($post->plan_payment_id, $post->payment_total);
						}

                        if (@$post->transaction_id) {
                            DB::insert(Model_Kes_Payment::PAYMENT_TABLE)
                                ->values(array(
                                    'transaction_id' => $post->transaction_id,
                                    'amount' => $post->payment_total,
                                    'type' => 'card',
                                    'bank_fee' => 0,
                                    'status' => 2,
                                    'currency' => 'EUR',
                                    'created' => $now,
                                    'updated' => $now,
                                    'note' => 'Payment Online Form'
                                ))->execute();
                        }

						if (
								Model_Plugin::is_enabled_for_role('Administrator', 'remoteaccounting') &&
								Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') &&
								Settings::instance()->get('remoteaccounting_payonline_sync') == 1
						) {
							$ra = new Model_Remoteaccounting();
                            $now = date::now();
                            $f = new Model_Family();
                            $f->set_family_name($post->name);
                            $f->save();
                            $c3 = new Model_Contacts3();
                            $c3->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                            $c3->set_subtype_id(0);
                            $c3->set_first_name($post->name);
                            $c3->set_last_name(' ');
                            $c3->set_family_id($f->get_id());
                            $c3->set_is_primary(1);
                            $c3->set_date_modified($now);
                            $c3->save();
                            $contact_id = $c3->get_id();
                            $c3 = new Model_Contacts3();
                            $c3->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                            $c3->set_subtype_id(0);
                            $c3->set_first_name($post->student_name);
                            $c3->set_last_name(' ');
                            $c3->set_family_id($f->get_id());
                            $c3->save();

                            $contact = array(
                                'id' => $contact_id,
                                'first_name' => $post->name,
                                'last_name' => ' ',
                                'email' => $post->email,
                                'date_modified' => $now
                            );
                            $ra->save_contact($contact);

                            if (!isset($post->transaction_id) && !isset($post->plan_payment_id)) {
                                $tx_inserted = DB::insert(Model_Kes_Transaction::TRANSACTION_TABLE)
                                    ->values(array(
                                        'contact_id' => $contact_id,
                                        'family_id' => $f->get_id(),
                                        'type' => 1,
                                        'booking_id' => 0,
                                        'amount' => $post->payment_total,
                                        'fee' => 0,
                                        'total' => $post->payment_total,
                                        'created' => $now,
                                        'updated' => $now,
                                        'payment_due_date' => $now
                                    ))
                                    ->execute();

                                $tx = array(
                                    'id' => $tx_inserted[0],
                                    'table' => Model_Kes_Transaction::TRANSACTION_TABLE,
                                    'contact_id' => $contact_id,
                                    'items' => array(
                                        array(
                                            'amount' => $post->payment_total,
                                            'description' => 'Payment Online'
                                        )
                                    ),
                                    'total' => $post->payment_total,
                                    'details' => 'Payment Online',
                                    'due_date' => $now,
                                    'created' => $now
                                );
                                $ra->save_transaction($tx);

                                $payment_inserted = DB::insert(Model_Kes_Payment::PAYMENT_TABLE)
                                    ->values(array(
                                        'transaction_id' => $tx['id'],
                                        'amount' => $post->payment_total,
                                        'type' => 'card',
                                        'bank_fee' => 0,
                                        'status' => 2,
                                        'currency' => 'EUR',
                                        'created' => $now,
                                        'updated' => $now,
                                        'note' => 'Payment Online Form'
                                    ))->execute();

                                $payment = array(
                                    'transaction_id' => $tx['id'],
                                    'transaction_table' => $tx['table'],
                                    'id' => $payment_inserted[0],
                                    'table' => Model_Kes_Payment::PAYMENT_TABLE,
                                    'amount' => $post->payment_total
                                );
                                $ra->save_payment($payment);
                            }
						}
						$realexpayment->send_mail_seller_no_cart($post, $all_post);
						$realexpayment->send_mail_customer_no_cart($post, NULL, $all_post);
					}

					// Add the customer to the newsletters if is checked
					if (isset($post->signupnewsletter) AND $post->signupnewsletter == "true")
					{
						$formprocessor_model                     = new Model_Formprocessor();
						$mail_data['contact_form_name']          = isset($post->ccName) ? $post->ccName : '';
						$mail_data['contact_form_tel']           = $post->phone;
						$mail_data['contact_form_email_address'] = $post->email;
						$formprocessor_model->add_to_list($mail_data);
					}

					// Earn Payback Loyalty Points
					try
					{
						if (class_exists('Model_Paybackloyalty') AND isset($_SESSION['pl_user']['username']))
						{
							$pl_model = new Model_PaybackLoyalty();
							$pl_model->update_points();

							$pl_model->reload_session();
						}
					}
					catch (Exception $e)
					{
						Log::instance()->add(Log::ERROR, $e->getTraceAsString());
					}

					$model_checkout = new Model_Checkout();

					//Process the payment, send email with the payment successful
					//if cart is enabled AND is a CART transaction then log
					if (Settings::instance()->get('cart_logging') == "TRUE")
					{
						try
						{
							$user        = Auth::instance()->get_user();
							$cart        = $model_checkout->get_cart();
							$cart_report = new Model_Cart($cart->data->id);
							$details     = array(
								'id'            => $model_checkout->get_cart_id(),
								'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
								'ip_address'    => $_SERVER['REMOTE_ADDR'],
								'user_id'       => isset($user['id']) ? $user['id'] : NULL,
								'cart_data'     => json_encode($cart),
								'paid'          => 1,
								'date_created'  => date('d-m-Y H:i:s',time()),
								'date_modified' => date('d-m-Y H:i:s',time())
							);
							$cart_report->set($details);
							$cart_report_products = new Model_Cartitems($cart_report->get_id());
							if (isset($cart->data->lines))
							{
								$cart_report_products->set($cart->data->lines);
							}
							$cart_report->save();
							$cart_report_products->save();
							unset($cart);
							$this->payment_log_id = Model_Payments::log_payment($post,$cart_report->get_id(),$invoicepayment,$stripe_payment,$charge);
						}
						catch (Exception $e)
						{
							$this->payment_log_id = FALSE;
						}
						if ( ! $this->payment_log_id)
						{
							Log::instance()->add(Log::ERROR, 'Could not add purchase record to plugin_payments_log');
						}
					}

					try
					{
						$model_checkout->on_successful_payment();
					}
					catch (Exception $e)
					{

					}
					//-1 from the stock if applicable
					$model_checkout->update_stock_count();
					//Empty cart after payment
					Model_Checkout::empty_cart();
					//Remove sign builder stuff, if it's there.
					$canvas = Session::instance()->get('canvas');
					if(!is_null($canvas))
					{
						Session::instance()->delete('canvas');
					}

					if (isset($post->thanks_page) AND !empty($post->thanks_page))
					{
						$response['redirect'] = (stripos($post->thanks_page, 'http') === false ? URL::site() : '') . $post->thanks_page;
					}
					else
					{
						$response['redirect'] = Model_Payments::get_thank_you_page(['is_donation' => $is_donation]);
					}

				}
            }
			else
            {
				$user = Auth::instance()->get_user();
                $model_checkout = new Model_Checkout();
                $cart = $model_checkout->get_cart();
                $cart_report = new Model_Cart($cart->data->id);
                $details = array(
                    'id'            => $model_checkout->get_cart_id(),
                    'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
                    'ip_address'    => $_SERVER['REMOTE_ADDR'],
					'user_id'       => isset($user['id']) ? $user['id'] : NULL,
					'paid'          => 1,
                    'date_created'  => date('Y-m-d H:i:s'),
                    'date_modified' => date('Y-m-d H:i:s')
                );
                $cart_report->set($details);
                $cart_report_products = new Model_Cartitems($cart_report->get_id());
                if (isset($cart->data->lines))
                {
                    $cart_report_products->set($cart->data->lines);
                }
                unset($cart);
                $post->realex_status = isset($payment_process_result['message']) ? $payment_process_result['message'] : '';
                $post->paid = 0;
                $this->payment_log_id = @Model_Payments::log_payment($post,$cart_report->get_id(),$invoicepayment);
                if ( ! $this->payment_log_id)
                {
                    Log::instance()->add(Log::ERROR, 'Could not add purchase record to plugin_payments_log');
                }

				if ($non_payment)
				{
					// Add the customer to the newsletters if is checked
					if (isset($post->signupnewsletter) AND $post->signupnewsletter == "true")
					{
						$formprocessor_model                     = new Model_Formprocessor();
						$mail_data['contact_form_name']          = isset($post->ccName) ? $post->ccName : '';
						$mail_data['contact_form_tel']           = $post->phone;
						$mail_data['contact_form_email_address'] = $post->email;
						$formprocessor_model->add_to_list($mail_data);
					}

					// Send emails
					$realexpayment->send_mail_seller($post, $all_post);
					$realexpayment->send_mail_customer($post, NULL, $all_post);

					// Empty cart
					Model_Checkout::empty_cart();
					if ( ! is_null(Session::instance()->get('canvas')))
					{
						Session::instance()->delete('canvas');
					}

					// Response data
					$response['status']   = 'success';
					$response['redirect'] = Model_Payments::get_thank_you_page();
				}
				else
				{
					//Send mail with the report
					$message = $payment_process_result['message'];
					$response['status'] = 'error';
					$response['message'] = $payment_process_result['message'];
					$realexpayment->send_mail_report_payment_error($post, $message);
				}

            }
        }
        else
        {
            $response['status'] = 'error';
            if(isset($response['message']) AND $response['message'] == "")
            {
                 $response['message'] = 'Not valid data';
            }
        }
        //set redirect for the callee to redirect
        $this->response->body(json_encode($response));
    }

    public function action_stripe_charge()
    {
        require_once APPPATH.'/vendor/stripe/lib/Stripe.php';
        $stripe_testing            = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
        $stripe['secret_key']      = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
        $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key')  : Settings::instance()->get('stripe_public_key');
        Stripe::setApiKey($stripe['secret_key']);

        $this->auto_render = FALSE;
        $post     = $this->request->post();
        $token    = $post['token'];
        $customer = Stripe_Customer::create(array(
            'description' => 'Customer for '.$token['email'],
            'card'        => $token['id']
        ));

        $checkout_model = new Model_checkout();
        $checkout       = $checkout_model->get_cart();
        $products       = $checkout->data->lines;
        $amount         = 0;
        foreach ($products as $product)
        {
            $amount .= $product->product->price;
        }


        try
        {
            $charge = Stripe_Charge::create(array(
                'customer' => $customer->id,
                'amount'   => $amount * 100, // convert to cents
                'currency' => 'eur'
            ));
            $return = array('success' => TRUE, 'amount' => $amount);
        }
        catch (Stripe_CardError $e)
        {
            $return = array('success' => FALSE, 'error' => $e);
        }

        return json_encode($return);
    }

	/*
	 * AJAX function to get the details for the form to send to PayPal
	 */
	public function action_ajax_get_paypal_form()
	{
		$this->auto_render = FALSE;
		parse_str(Kohana::sanitize($this->request->post('form_data')), $form_data);
		$form_data = (Object) $form_data;

		// Log the cart. The card ID will be needed in the PayPal callback to get the form data
		if (Settings::Instance()->get('cart_logging') == "TRUE" AND class_exists('Model_Checkout'))
		{
			$user        = Auth::instance()->get_user();
			$checkout    = new Model_Checkout;
			$cart_report = new Model_Cart();
			$details     = array(
				'id'            => (int) (microtime('get as float')*1000000), // unix timestamp in microseconds
				'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
				'ip_address'    => $_SERVER['REMOTE_ADDR'],
				'user_id'       => isset($user['id']) ? $user['id'] : NULL,
				'cart_data'     => '{}',
				'form_data'     => json_encode($form_data),
				'paid'          => 0,
				'date_created'  => date('Y-m-d H:i:s'),
				'date_modified' => date('Y-m-d H:i:s')
			);
			$cart_report->set($details)->save();
			$form_data->custom = $cart_report->get_id();
		}

		// PayPal data
		$paypal_data                  = new stdClass();
		$paypal_data->cmd             = '_cart';
		$paypal_data->upload          = 1;
		$paypal_data->business        = Settings::instance()->get('paypal_email');
		$paypal_data->currency_code   = 'EUR';
		$paypal_data->no_shipping     = 2;
		$paypal_data->return          = isset($form_data->return_url)        ? $form_data->return_url        : $_SERVER['HTTP_HOST'];
		$paypal_data->cancel_return   = isset($form_data->cancel_return_url) ? $form_data->cancel_return_url : $_SERVER['HTTP_HOST'];
		$paypal_data->notify_url      = URL::base().'/frontend/payments/paypal_callback/invoice';
		$paypal_data->custom          = isset($form_data->custom)  ? $form_data->custom  : '';
		$paypal_data->{'item_name_1'} = isset($form_data->item_name) ? $form_data->item_name : 'Invoice Payment';
		$paypal_data->{'amount_1'   } = $form_data->payment_total;
		$paypal_data->{'quantity_1' } = 1;

		// Return data
		$return                  = new stdClass;
		$return->status          = TRUE;
		$return->data            = $paypal_data;
		$return->data->test_mode = (Settings::instance()->get('paypal_test_mode') == 1);

		$this->response->body(json_encode($return));
	}

	/*
	 * PayPal contacts the server and calls this function after a successful payment
	 * PayPal sends details about the payment, plus one custom parameter
	 * This function flags the record of the payment as "paid" and sends emails to the seller and customer
	 */
	public function action_paypal_callback()
	{
        $post    = $this->request->post();
		$type    = $this->request->param('id');
		$payment = new Model_Realexpayments; // Not actually "Realex"
		$data['purchase_time'] = date('Y-m-d H:i:s');

		IbHelpers::htmlspecialchars_array($post);

		$data['cart_details']  = json_encode(IbHelpers::iconv_array($post));

        try
		{
            $is_booking             = ($type == 'booking' AND isset($post['custom']));
			$is_product             = ($type == 'product' AND isset($post['custom']));
			$is_invoice             = ($type == 'invoice' AND isset($post['custom']));
			$data['paid']           = 1;
			$data['payment_type']   = 'PayPal';
			$data['payment_amount'] = isset($post['mc_gross']) ? $post['mc_gross'] : '';

			if ($is_booking)
			{
				// Use contact details from the booking, which should also be the data filled out in the checkout form
				$booking                    = Model_CourseBookings::load(trim($post['custom'], '|'));
                $data['customer_name']      = $booking['student']['first_name'].' '.$booking['student']['last_name'];
				$data['customer_telephone'] = $booking['student']['phone'];
				$data['customer_address']   = $booking['student']['address'];
				$data['customer_email']     = $booking['student']['email'];
			}
			elseif ($is_product OR $is_invoice)
			{
				$cart      = new Model_Cart($post['custom']);

				// Set the cart item as paid
				$cart->set_paid(1)->save();

				// Send the emails
				$cart      = $cart->get_instance();
				$form_data = json_decode($cart['form_data']);
				$cart_data = json_decode($cart['cart_data']);
				$cart_data = isset($cart_data->data) ? $cart_data->data : new stdClass();
                $cart_data->payment_type = 'Paypal';

				$data['customer_name']      = isset($form_data->ccName)    ? $form_data->ccName         : '';
				$data['customer_telephone'] = isset($form_data->phone)     ? $form_data-> phone         : '';
				$data['customer_address']   = isset($form_data->address_1) ? $form_data->address_1      : '';
				$data['customer_address']  .= isset($form_data->address_2) ? ', '.$form_data->address_2 : '';
				$data['customer_address']  .= isset($form_data->address_3) ? ', '.$form_data->address_3 : '';
				$data['customer_address']  .= isset($form_data->address_4) ? ', '.$form_data->address_4 : '';
				$data['customer_email']     = isset($form_data->email)     ? $form_data->email          : '';
				$data['cart_id']            = isset($cart_data->id)        ? $cart_data->id             : '';

				$payment->send_mail_seller($form_data, (array) $cart_data);
				$payment->send_mail_customer($form_data, NULL, (array) $cart_data);
			}
			else
			{
				// Use contact details from the buyer's PayPal account
				$data['customer_name']      = trim((isset($post['first_name'])?$post['first_name']:'').' '.(isset($post['last_name'])?$post['last_name']:''));
				$data['customer_telephone'] = isset($post['contact_phone']) ? $post['contact_phone'] : '';;
				$data['customer_address']   = "".
					(isset($post['address_name'])    ? $post['address_name']   ."\n" : '').
					(isset($post['address_street'])  ? $post['address_street'] ."\n" : '').
					(isset($post['address_city'])    ? $post['address_city']   ."\n" : '').
					(isset($post['address_state'])   ? $post['address_state']  ."\n" : '').
					(isset($post['address_zip'])     ? $post['address_zip']    ."\n" : '').
					(isset($post['address_country']) ? $post['address_country']."\n" : '');
				$data['customer_email']     = isset($post['payer_email']) ? $post['payer_email'] : '';
			}

			DB::insert('plugin_payments_log')->values($data)->execute();

            if ($is_booking)
			{
				Model_CourseBookings::paypal_handler_old($booking['id'], $post['mc_gross'], $post['txn_id']);

				// send success emails regarding bookings
				$payment->send_mail_seller_bookings($post);
				$payment->send_mail_customer_bookings($post);
			}

		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getMessage()."\n".$e->getTraceAsString());
			$data['payment_type'] = 'Test/failed payment';
			Model_Errorlog::save($e);
			DB::insert('plugin_payments_log')->values($data)->execute();
		}
	}

	public function action_stripe_create_pi()
	{
		$this->auto_render = false;
		$this->response->headers('Content-type', 'application/json; charset=utf-8');

        try {
            require_once APPPATH . '/vendor/stripe6/init.php';

            $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
            $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
            $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
            \Stripe\Stripe::setApiKey($stripe['secret_key']);

            $post = $this->request->post();
            $order_id = @$post['order_id'];
            if (!$order_id) {
                $order_id = date("ymdHis");
            }
            $amount = (float)$post['amount'];
            $currency = $post['currency'];
            $descriptor_suffix = Kohana::$environment == Kohana::PRODUCTION ? '' : '-' . Kohana::$environment;
            $statement_descriptor = "order-" . $order_id . $descriptor_suffix;

            $charge_params = array(
                'amount' => $amount * 100,
                'currency' => $currency,
                'statement_descriptor' => $statement_descriptor,
                'description' => $statement_descriptor,
                'confirmation_method' => 'automatic'
            );
            if (@$post['destination']) {
				$charge_params['transfer_data'] = array('destination' => $post['destination']);
            }

            $charge = \Stripe\PaymentIntent::create($charge_params);

            $response = array();
            $response['id'] = (string)$charge->id;
            $response['secret'] = (string)$charge->client_secret;
        } catch (Exception $exc) {
            $response = array(
                'error' => $exc->getMessage()
            );
        }
		echo json_encode($response);
	}

	public function action_ajax_check_for_realex()
	{
		session_commit();
		$this->auto_render = FALSE;
		echo Settings::instance()->get('enable_realex');
	}

    public function action_ajax_check_for_paypal()
    {
        session_commit();
        $this->auto_render = FALSE;
        echo Settings::instance()->get('enable_paypal');
    }

}