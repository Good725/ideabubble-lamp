<?php defined('SYSPATH') OR die('No Direct Script Access');


final class Controller_Frontend_Courses extends Controller_Template
{
    public $template = 'plugin_template';

    /**
     *
     */

    public function action_save_booking()
    {

        $data = $_POST;
        $response = Model_Bookings::save_booking($data);
        if ($response === false) {
            $this->request->redirect('/booking-form.html/?id=' . $data['schedule_id'] . '&error=true');
        } else {
            $this->request->redirect('/booking-form.html/?id=' . $data['schedule_id'] . '&saved=true');
        }
    }

    public function action_add_to_waitlist()
    {
        //die('<pre>' . print_r($this->request, 1) . '</pre>');
        if ($this->request->method() == 'GET') {
            $data = $this->request->query();
            $schedule = Model_Schedules::get_schedule( $data['interested_in_schedule_id']);
            $view = View::factory('front_end/addtowaitlist');

            $page = Model_Pages::get_page('add-to-waitlist');
            $view->page_data = isset($page[0]) ? $page[0] : [];

            $view->page_data['schedule_id'] = $data['interested_in_schedule_id'];
            $view->page_data['schedule'] = $schedule;
            $this->auto_render = true;
            $view->theme = Model_Engine_Theme::get_current_theme();
            return $view;

        } else {
            $data = $this->request->post();
            $schedule = Model_Schedules::get_schedule( $data['schedule_id']);
            $trainer = Model_Schedules::get_trainer_by_id($data['schedule_id']);
            $schedule_name = date('D - H:i', strtotime($schedule['start_date'])) . ' ' ;
            $schedule_name .= $schedule['location'] ? ' - ' . $schedule['location']   : '' . ' ' ;
            $schedule_name .= ( ! empty($trainer['trainer'])) ? ' - '  . @$trainer['trainer'] : '';
            $email = trim($data['addwaitlist_form_email_address']);
            $name = explode(' ', $data['addwaitlist_form_name']);
            $course = Model_Courses::get_course($schedule['course_id']);
            $course_data = array(
                'course_name' => $course['title'],
                'schedule_name' => $schedule_name
            );
            $waitlist_find = ORM::factory('Course_Waitlist')
                ->where('course_id', '=', $schedule['course_id'])
                ->and_where('schedule_id', '=', $schedule['id'])
                ->and_where('deleted', '=', 0)
                ->and_where('email' , '=', $email
            )->find();
            if (!empty($waitlist_find) && !empty($waitlist_find->id)) {
                //if waitlist record already exists update the date when it was added 
                $waitlist_find->set('date_modified', date("Y-m-d H:i:s", time()));
                $waitlist_find->save_with_moddate();
                $this->request->redirect('thank-you-waitlist');
            }
            $model_waitlist = ORM::factory('Course_Waitlist')->create();
            $model_waitlist->set('course_id', $schedule['course_id']);
            $model_waitlist->set('schedule_id', $schedule['id']);
            $model_waitlist->set('email', $email);
            $model_waitlist->set('name', trim($name[0]));
            $model_waitlist->set('surname', trim($name[1]));
            $model_waitlist->save_with_moddate();
            $new_contact = false;
            $exists = Model_Contacts3::search(array('email' => $email));
            if (count($exists) == 0) {
                if (class_exists('Model_Contact') && Model_Plugin::is_enabled_for_role('Administrator', 'contacts') )
                {
                    //@todo: if the class is deprecated forever, remove this

                    $data['forename']   = trim($name[0]);
                    $data['surname']    = trim($name[1]);
                    $data['salutation'] = $data['forename'];
                    $data['c_type']     = Model_Contacttype::lookup_type_id('General Contact');
                    $data['notes']      = '';
                    $contact_saved = Model_Contact::add($data);
                    $contact_details = new Model_ContactDetails();
                    $contact_details->add($contact_saved, array($email), array('email'), '');
                    if ($contact_saved !== FALSE)
                    {
                        //@todo: if the class is deprecated forever, remove this, if the class still works, check if id can be retrieved
                        $contact_id = $contact_saved->get_id();
                    }
                    $new_contact = true;

                } elseif (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                    $exists = Model_Contacts3::search(array('email' => $email));

                    $contact = new Model_Contacts3();
                    $type = Model_Contacts3::find_type('student');
                    $contact->set_type($type['contact_type_id']);
                    $contact->set_subtype_id(0);
                    $contact->set_first_name(trim($name[0]));
                    $contact->set_last_name(trim($name[1]));
                    $contact->insert_notification(array('value' => $email, 'notification_id' => 1));
                    $contact->trigger_save = false;
                    $contact->save(false);
                    $contact_id = $contact->get_id();
                    $new_contact = true;
                } else {
                    $contact_model = new Model_Contacts();
                    if (!empty($name)) {
                        $contact_model->set_first_name(trim($name[0]));
                    }

                    if (!empty($email)) {
                        $contact_model->set_email($email);
                    }
                    $contact_saved = $contact_model->save();
                    if ($contact_saved !== FALSE)
                    {
                        $new_contact = true;
                        $contact_id = $contact_model->get_id();
                    }
                }
            } else {
                $contact_id = $exists[0]['id'];
            }
            if (!empty($contact_id)) {
                $model_waitlist->set('contact_id', $contact_id);
                $model_waitlist->save_with_moddate();
            }
            $form_add_to_waitlist = array(
                'form_identifier' => 'add_to_waitlist_',
                'add_to_waitlist_form_name' => implode(' ', $name),
                'add_to_waitlist_form_email_address' => $email
            );
            $template_params = array(
                'course' => $course_data['course_name'],
                'schedule' => $schedule_name,
                'name' => implode(' ', $name),
                'email' => $email
            );
            $mm = new Model_Messaging();
            try {
                $mm->send_template(
                    'course-waitlist-admin',
                    null,
                    null,
                    array(),
                    $template_params
                );
            } catch (Exception $exc) {
                Model_Errorlog::save($exc, 'PHP');
            }

            try {
                $mm->send_template(
                    'course-waitlist-student',
                    null,
                    null,
                    array(
                        array(
                            'target_type' => 'EMAIL',
                            'target' => $email
                        )
                    ),
                    $template_params
                );
            } catch (Exception $exc) {
                Model_Errorlog::save($exc, 'PHP');
            }

            $this->request->redirect('thank-you-waitlist');
            //$this->request->redirect('course-detail/?id=' . $schedule['course_id']);
        }
    }

    public function action_search_course()
    {
        $term = $_GET['term'];
        $with_id = $_GET['with_id'] ?? FALSE;
        $ret = Model_Schedules::get_all_published_for_autocomplete($term, $with_id);
        echo json_encode($ret);
        exit;
    }

    public function action_process_nbs_checkout()
    {
        $post = $this->request->post();

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $result = array('success' => false, 'message' => __('Unknown error'));

        $result = Model_CourseBookings::nbs_checkout_save($post);

        echo json_encode($result);
    }

    public function action_checkout_get_paypal_form()
    {
        $model = new Model_Bookings();
        $this->response->body(json_encode($model->get_paypal_data(json_decode($_POST['data']))));
        $this->auto_render = FALSE;
    }

    public function action_ajax_save_booking()
    {
        $data = $_POST;
        $response = Model_Bookings::save_ajax_booking($data);
        echo json_encode($response);
        exit;
    }

    public function action_ajax_save_booking_with_cart()
    {
        $data = $_POST;
        $response = Model_Bookings::save_ajax_booking_with_cart($data);
        echo json_encode($response);
        exit;
    }

	public function action_ajax_book_and_pay_with_cart()
	{
		$post = $this->request->post();
		$post['first_name'] = (isset($post['first_name']) AND $post['first_name'] != '') ? $post['first_name'] : (isset($post['student_first_name']) ? $post['student_first_name'] : '');
		$post['last_name']  = (isset($post['last_name'])  AND $post['last_name']  != '') ? $post['last_name']  : (isset($post['student_last_name'])  ? $post['student_last_name']  : '');
		$post['address1']   = (isset($post['address1'])   AND $post['address1']   != '') ? $post['address1']   : (isset($post['student_address1'])   ? $post['student_address1']   : '');
		$post['address2']   = (isset($post['address2'])   AND $post['address2']   != '') ? $post['address2']   : (isset($post['student_address2'])   ? $post['student_address2']   : '');
		$post['email']      = (isset($post['email'])      AND $post['email']      != '') ? $post['email']      : (isset($post['student_email'])      ? $post['student_email']      : '');
		$post['phone']      = (isset($post['phone'])      AND $post['phone']      != '') ? $post['phone']      : (isset($post['student_phone'])      ? $post['student_phone']      : '');
        $post['year_id']    = !empty($post['year_id']) ? $post['year_id'] : !empty($post['student_year_id']) ? $post['student_year_id'] : '';

        if (is_numeric(@$post['county'])) {
            $post['county'] = DB::select('name')->from('plugin_courses_counties')->where('id', '=', $post['county'])->execute()->get('name');
        }

        if (!empty($post['coupon_code']) && Settings::instance()->get('course_checkout_coupons')) {
            $event      = Model_ScheduleEvent::get($this->request->post('event_id'));
            $schedule   = Model_Schedules::get_schedule($event['schedule_id']);
            $items      = [['id' => $event['schedule_id'], 'fee' => $schedule['fee_amount'], 'discount' => 0, 'prepay' => 1]];
            $discounts_per_item = Model_CourseBookings::get_available_discounts(null, $items, 0, $post['coupon_code']);
            $post['amount'] = $discounts_per_item[0]['total'];
        }

        $response = Model_CourseBookings::save_ajax_booking_with_cart($post);

        $post['ccNum'] = isset($post['ccNum']) ? preg_replace('/[^0-9]/', '', $post['ccNum']) : '';

		if (isset($response['booking']) AND $response['booking'] != '')
		{
			Session::instance()->set('last_course_booking_id', $response['booking']);
            $booking_id       = $response['booking'];
			$data             = (Object)$post;
			$data->custom     = $response['booking'].'|';
			$data->ccAddress1 = $data->student_address1;
			$data->ccAddress2 = $data->student_address2;
            $data->gateway    = (Settings::instance()->get('stripe_enabled') == 'TRUE') ? 'Stripe' : 'Realex';
            $data->booking_id = $booking_id;
            if ($data->gateway == 'Stripe' && @$response['franchisee_account']['use_stripe_connect'] == 1 && $response['franchisee_account']['stripe_auth']['stripe_user_id']) {
                $data->stripe_user_id = $response['franchisee_account']['stripe_auth']['stripe_user_id'];
            }
			$response         = self::cart_processor($data);

            if ($response['status'] == 'success') {
                Model_CourseBookings::set_processing_status($booking_id, 'Confirmed', '');
                Model_CourseBookings::make_booking_payment($booking_id, $post['amount'], $data->gateway, $response['remote_id']);
            }
		}

		echo json_encode($response);
		exit;
	}

	function cart_processor($post)
	{
		$realexpayment = new Model_Realexpayments();
        $remote_id = '';

        if (@$post->saved_card_id) {
            $card = DB::select('cards.card_id', 'gw.customer_id')
                ->from(array(Model_Contacts3::HAS_CARDS_TABLE, 'cards'))
                    ->join(array(Model_Contacts3::PAYMENTGW_TABLE, 'gw'), 'inner')->on('cards.has_paymentgw_id', '=', 'gw.id')
                ->where('cards.id', '=', $post->saved_card_id)
                ->execute()
                ->current();
            $post->stored_card = $card;
        }
		// Validate payment
		if (Model_Payments::validate_courses_details($post))
		{
            // Process payment
            if (isset($post->gateway) && strtolower($post->gateway) == 'stripe') {
                $stripe_testing            = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
                $stripe['secret_key']      = Settings::instance()->get($stripe_testing ? 'stripe_test_private_key' : 'stripe_private_key');
                $stripe['publishable_key'] = Settings::instance()->get($stripe_testing ? 'stripe_test_public_key'  : 'stripe_public_key');

                if ($post->stripe_payment_intent_id) {
                    require_once APPPATH . '/vendor/stripe6/init.php';
                    \Stripe\Stripe::setApiKey($stripe['secret_key']);
                } else {
                    require_once APPPATH . '/vendor/stripe/lib/Stripe.php';
                    Stripe::setApiKey($stripe['secret_key']);
                }

                try {
                    $descriptor = 'Booking'.(isset($post->booking_id) ? ' '.$post->booking_id : '');

                    if ($post->stripe_payment_intent_id) {
                        $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
                        $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
                        $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
                        \Stripe\Stripe::setApiKey($stripe['secret_key']);

                        $intent = \Stripe\PaymentIntent::retrieve($post->stripe_payment_intent_id);
                        if ($intent->status == 'succeeded') {
                            $payment_process_result = array('message' => '');
                            $payment_successful = true;
                            $remote_id = (string)$intent->id;
                        } else {
                            $payment_process_result = array('message' => 'payment failed');
                            $payment_successful = false;
                        }
                        //$conf = $intent->confirm();
                        //header('content-type: text/plain');print_r($intent);print_r($conf);exit;
                    } else {
                        $charge_params = array(
                            'amount' => $post->amount * 100,
                            'currency' => isset($post->currency) ? $post->currency : 'eur',
                            'statement_descriptor' => $descriptor,
                            'description' => $descriptor,
                            'source' =>
                                isset($post->stripe_token) ?
                                    $post->stripe_token
                                    :
                                    array(
                                        'exp_month' => $post->ccExpMM,
                                        'exp_year' => $post->ccExpYY,
                                        'number' => $post->ccNum,
                                        'object' => 'card',
                                        'cvc' => isset($post->cvc) ? $post->cvc : $post->ccv,
                                        'name' => $post->ccName
                                    )
                        );

                        if ($post->stripe_user_id) {
                            $charge_params['destination'] = $post->stripe_user_id;
                        }

                        $charge = Stripe_Charge::create($charge_params);
                        $remote_id = (string)$charge->id;

                        $payment_process_result = array('message' => '');
                        $payment_successful = true;
                    }
                } catch (Exception $e) {throw $e;
                    $payment_successful = false;
                    $error_message =  __(
                        'Error making payment. Please try again. If this problem continues, please $1.',
                        array('$1' => '<a href="/contact-us.html" target="_blank">'.__('contact the administration').'</a>')
                    );

                    $payment_process_result = array('message' => $error_message);

                    Log::instance()->add(Log::ERROR, "Error making stripe payment.\n".$e->getMessage()."n".$e->getTraceAsString());
                    IbHelpers::set_message($error_message, 'warning popup_box');
                }

            } else {
                $realvault = new Model_Realvault();
                $recurring = null;
                if ($post->recurring) {
                    $recurring = ['type' => 'variable', 'sequence' => 'first'];
                }
                if (@$post->stored_card) {
                    $ccresult = $realvault->charge_card(
                        $post->stored_card['customer_id'],
                        $post->stored_card['card_id'],
                        $post->transaction_id,
                        $post->amount,
                        'EUR',
                        $post->ccv,
                        $recurring
                    );
                } else {
                    $ccresult = $realvault->charge(
                        $post->transaction_id,
                        $post->amount,
                        'EUR',
                        $post->ccNum,
                        $post->ccExpMM . $post->ccExpYY,
                        $post->ccType,
                        $post->ccName,
                        $post->ccv,
                        $recurring
                    );
                }

                $payment_process_result = array();
                $payment_process_result['response'] = (string)$ccresult->result;
                $payment_process_result['timestamp'] = (string)$ccresult['timestamp'];
                $payment_process_result['message'] = (string)$ccresult->message;
                $payment_process_result['response'] = '00';
                $payment_successful     = ($payment_process_result['response'] == '00');
            }

			if ($payment_successful) {
                //Procces the payment, send email with the payment successful
				@$realexpayment->send_mail_seller_bookings($post);
				$realexpayment->send_mail_customer_bookings($post);

				//Add the customer to the newsletters if is checked
				if (isset($post->signupCheckbox) AND $post->signupCheckbox == '1') {
					$formprocessor_model = new Model_Formprocessor();
					$formprocessor_model->add_to_list(get_object_vars($post));
				}
				$response['status'] = 'success';
				if (isset($post->thanks_page) AND !empty($post->thanks_page)) {
					$response['redirect'] = $post->thanks_page;
				} else {
					$response['redirect'] = Model_Payments::get_thank_you_page();
				}
			} else {
                $response['message'] = $payment_process_result['message'];
                $response['status'] = 'error';
				$response['redirect'] = $post->error_page;
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = 'Not valid data';
		}

        $response['remote_id'] = $remote_id;

        // Log the payment
        if (Settings::instance()->get('cart_logging') == "TRUE")
        {
            $model_checkout = new Model_Checkout();
            $cart           = $model_checkout->get_cart();
            $cart_report    = new Model_Cart($cart->data->id);
            $user           = Auth::instance()->get_user();
            $details        = array(
                'id'            => $model_checkout->get_cart_id(),
                'user_agent'    => $_SERVER['HTTP_USER_AGENT'],
                'ip_address'    => $_SERVER['REMOTE_ADDR'],
                'user_id'       => isset($user['id']) ? $user['id'] : NULL,
                'cart_data'     => json_encode($cart),
                'paid'          => ($response['status'] == 'error') ? 0 : 1,
                'date_created'  => date('d-m-Y H:i:s',time()),
                'date_modified' => date('d-m-Y H:i:s',time())
            );
            $cart_report->set($details);
            $cart_report->save();
            $data                = (object) $post;
            $data->paid          = ($response['status'] == 'error') ? 0 : 1;
            $data->realex_status = isset($response['message']) ? $response['message'] : '';
            Model_Payments::log_payment($data, $cart_report->get_id());
        }

		return $response;
	}

	public function action_ajax_book_and_pay_with_cart_paypal()
	{
		$post = $this->request->post();
		$post['first_name'] = (isset($post['first_name']) AND $post['first_name'] != '') ? $post['first_name'] : (isset($post['student_first_name']) ? $post['student_first_name'] : '');
		$post['last_name']  = (isset($post['last_name'])  AND $post['last_name']  != '') ? $post['last_name']  : (isset($post['student_last_name'])  ? $post['student_last_name']  : '');
		$post['address1']   = (isset($post['address1'])   AND $post['address1']   != '') ? $post['address1']   : (isset($post['student_address1'])   ? $post['student_address1']   : '');
		$post['address2']   = (isset($post['address2'])   AND $post['address2']   != '') ? $post['address2']   : (isset($post['student_address2'])   ? $post['student_address2']   : '');
		$post['email']      = (isset($post['email'])      AND $post['email']      != '') ? $post['email']      : (isset($post['student_email'])      ? $post['student_email']      : '');
		$post['phone']      = (isset($post['phone'])      AND $post['phone']      != '') ? $post['phone']      : (isset($post['student_phone'])      ? $post['student_phone']      : '');

		// Record booking
		$model    = new Model_CourseBookings;
		$response = $model->save_ajax_booking_with_cart($post);

		// If successful, pay for the booking
		if (isset($response['booking']) AND $response['booking'] != '')
		{
            $data          = (Object)$post;
            $data->custom  = $response['booking'];

            if (is_numeric(@$post['event_id'])) { // book one timeslot
                $event = Model_Schedules::get_event_details($post['event_id']);
                $data->title   = $event['course'].', '.$event['location'].': '.$event['datetime_start'];
                $data->amount  = $event['fee_amount'];
            } else { // book whole schedule
                $schedule = Model_Schedules::get_one_for_details($post['schedule_id']);
                $data->title   = $schedule['course'] . ', ' . $schedule['location'] . ': ' . $schedule['start_date'];
                $data->amount  = $schedule['fee_amount'];
            }
			$return        = $model->get_paypal_data($data);
		}
		else
		{
			$return = new stdClass;
			$return->status = -1;
		}
		echo json_encode($return);
		exit();
	}

    public function action_payment_processor()
    {
        $post = json_decode($this->request->post('data'));
        $realexpayment = new Model_Realexpayments();

        //Validate payment
        if ($realexpayment->validate_courses_details($post)) {
            $payment_process_result = $realexpayment->proccess_courses_payment($post);
            //Process payment
            if ($payment_process_result['response'] == '00') {
                //Procces the payment, send email with the payment successful
                $realexpayment->send_mail_seller_bookings($post);
                $realexpayment->send_mail_customer_bookings($post);

                Model_Bookings::transaction_paid($post->custom, $post->schedule, $post->amount, 'Realex');
                //Add the customer to the newsletters if is checked
                if ($post->signupCheckbox == '1') {
                    $formprocessor_model = new Model_Formprocessor();
                    $mail_data['contact_form_name'] = $post->ccName;
                    $mail_data['contact_form_tel'] = $post->phone;
                    $mail_data['contact_form_email_address'] = $post->email;
                    $formprocessor_model->add_to_list($mail_data);
                }
                $response['status'] = 'success';
                if (isset($post->thanks_page) AND !empty($post->thanks_page)) {
                    $response['redirect'] = $post->thanks_page;
                } else {
                    $response['redirect'] = Model_Payments::get_thank_you_page();
                }
            } else {
                //Send mail with the report
                $message = $payment_process_result['message'];
                $response['status'] = 'error';
                $response['message'] = $payment_process_result['message'];
                $realexpayment->send_mail_report_payment_error($post);
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Not valid data';
        }
        echo json_encode($response);
        exit;
    }

    public function action_cart_processor() {
        if(Request::current()->is_ajax()){
            $post = json_decode($this->request->post('data'));
            $response = self::cart_processor($post);
            echo json_encode($response);
            self::action_clear_cart(false);
            exit;
        } else{
            $this->auto_render = FALSE;
            $post = $this->request->post();
            $post['error_page'] = false;
            $post['thanks_page'] = false;
            $post = json_decode(json_encode($post), FALSE);
            $response = self::cart_processor($post);
            $this->response->headers('Content-type','application/json; charset='.Kohana::$charset);
            $this->response->body(json_encode( $response ));
            return $response;
        }
    }

    public function action_clear_cart($redirect = true)
    {
        Model_Bookings::clear_cart();
        if ($redirect) {
            $this->request->redirect('/course-list.html');
        }
        exit;
    }

    public function action_get_locations_for_date()
    {
        $id = (int)$_POST['id'];
        $sid = (int)$_POST['sid'];
        $response = Model_Schedules::get_locations_for_data($id, $sid);
        echo $response;
        exit;
    }

    public function action_get_dates_for_location()
    {
        $lid = (int)$_POST['lid'];
        $cid = (int)$_POST['cid'];
        $sid = (int)$_POST['sid'];
        $response = Model_Schedules::get_dates_for_location($cid, $sid, $lid);
        echo $response;
        exit;
    }

    public function action_get_all_locations_for_date()
    {
        $cid = (int)$_POST['cid'];
        $response = Model_Schedules::get_all_locations_for_data($cid);
        echo $response;
        exit;
    }

    public function action_get_all_dates_for_location()
    {
        $cid = (int)$_POST['cid'];
        $response = Model_Schedules::get_all_dates_for_location($cid);
        echo $response;
        exit;
    }

    public function action_get_schedule_detailed_info()
    {
        $id = isset($_POST['id']) ? intval($_POST['id']) : NULL;
        $response = Model_Schedules::get_details_for_schedule($id);
        echo $response;
        exit;
    }

    public function action_get_schedule_event_detailed_info()
    {
        $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : NULL;
        $response = Model_Schedules::get_details_for_schedule_event($event_id);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo $response;
    }

    public function action_get_calendar_feed()
    {
        $month = @$_GET['month'] ? @$_GET['month'] : false;
        $year = @$_GET['year'] ? @$_GET['year'] : false;
        $response = Model_Schedules::get_front_events($month, $year);
        echo $response;
        exit;
    }

    public function action_get_locations_and_levels_for_course()
    {
        $level = $_POST['level'];
        $location = $_POST['location'];
        $course_title = $_POST['title'];
        $return = Model_Courses::get_locations_and_levels_for_course($course_title, $location, $level);
        echo $return;
        exit;
    }

    public function action_get_locations_and_categories_for_course()
    {
        $category = $_POST['category'];
        $location = $_POST['location'];
        $course_title = $_POST['title'];
        $return = Model_Courses::get_locations_and_categories_for_course($course_title, $location, $category);
        echo $return;
        exit;
    }

    public function action_get_calendar()
    {
        $response = Model_Schedules::render_calendar();
        echo $response;
        exit;
    }

    public function action_get_schedule_price_by_id()
    {
        $sid = trim($_POST['sid']);
        $event_id = @$_POST['event_id'];
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        if (is_numeric($sid)) {
            $result = Model_Schedules::get_schedule_price_by_id($sid, $event_id);
        } else {
            $result = array('error' => "Invalid Schedule ID");
        }
        echo json_encode($result);
    }

    public function action_get_trainer_by_id()
    {
        $sid = trim($_POST['sid']);
        echo (is_numeric($sid))? Model_Schedules::get_trainer_by_id($sid):"Invalid Schedule ID";
        exit;
    }


    public function action_ajax_get_schedule_price_and_discount()
	{
        $schedule_id = $this->request->post('schedule_id');
        $timeslot_id = $this->request->post('timeslot_id');
		$schedule = Model_Schedules::get_schedule($schedule_id);
        if (is_numeric($timeslot_id)) {
            $timeslot = Model_Schedules::get_event_details($timeslot_id);
        }
		if (isset($schedule['id'])) {
			$return['success']       = true;
			$return['is_free']       = ($schedule['is_fee_required'] == 0);
			$return['fee']           = ($schedule['fee_per'] == 'Schedule' || !$timeslot['fee_amount']) ? $schedule['fee_amount'] : $timeslot['fee_amount'];
            $return['fee_per']       = $schedule['fee_per'];
            $return['discount_info'] = '';

            $discounts = Model_CourseBookings::get_available_discounts(null, array(array('id' => $schedule_id, 'fee' => $schedule['fee_amount'], 'discount' => 0, 'prepay' => 1)));
            if (isset($discounts[0])) {
                if ($discounts[0]['discount'] > 0) {
                    $return['discount_info'] = '<span style="text-decoration: line-through;">€' . $return['fee'] . '</span><br /><i>-€' . $discounts[0]['discount'] . ' Off </i>';
                    $return['fee'] = $discounts[0]['total'];
                }
            }

		} else {
			$return['success']       = false;
			$return['error']         = 'Schedule data not found';
		}
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
		echo json_encode($return);exit;
	}

    /* This function is deprecated. It is maintained as an alias, until confirmed that it is no longer in use. */
    public function action_get_calendar_event_feed()
    {
        $frontend_controller = new Controller_Frontend_Frontend($this->request, $this->response);
        $frontend_controller->action_eventcalendar_items();
    }

    public function action_ajax_filter_results()
    {
        $post = $this->request->post();
		$page = $this->request->post('page');
		$page = $page ? $page : 1;
        $limit = Settings::instance()->get('courses_results_per_page');
        $limit = $limit ? (int) $limit : 12;

		if (empty($post['offset'])) {
			$post['offset'] = $page ? ($page - 1) * $limit : 0;
		}

        $referrer  = Request::factory(parse_url($this->request->referrer(), PHP_URL_PATH));
        $page_data = Model_Pages::get_page($referrer->param('page').'.html');

        $args['limit'] = Settings::instance()->get('courses_results_per_page');

        Session::instance()->set('filter_reminder', ($post['reminder'] == 0) ? 'FALSE' : 'TRUE');
        Session::instance()->set('last_search_params', $post);

        $view = View::factory('front_end/course_feed_items_snippet')
			->set('page',         $page)
            ->set('page_data',    isset($page_data[0]) ? $page_data[0] : null)
			->set('display_mode', $this->request->post('display'));

        $params = $post;
        $params['direction'] = $params['sort'];
        $params['book_on_website'] = true;
        $search = Model_Plugin::global_search($params);

        $view->search = $search;
        $view->search_results = $search['data'];

        $this->response->body(json_encode($view->render()));
        $this->auto_render = false;
    }

    public function action_ajax_filtered_discount_results()
    {
        $post     = $this->request->post();
        $count    = 0;
        $packages = Model_KES_Discount::get_all_discounts_with_advanced_search($post, [], $count);
        $view     = (string) View::factory('front_end/package_feed_snippet')->set('packages', $packages);

        $this->response->body(json_encode($view));
        $this->auto_render = FALSE;
    }

    public function action_ajax_filtered_course_results()
    {
        $post         = $this->request->post();
        $given_date   = isset($post['given_date']) ? $post['given_date'] : null;
        $args         = array('offset' => 1000);
        $count        = 0;
        $result       = Model_KES_Discount::get_all_courses_with_advanced_search($post, $args, $count);
        $courses      = $result['courses'];
        $schedule_ids = array();

        // get schedule_ids
        foreach ($courses as $course){
            if ($course['schedule_id']) {
                array_push($schedule_ids,$course['schedule_id']);
            }
        }

        $this->auto_render = FALSE;

        return $this->response->body(json_encode(self::action_draw_course_results_with_weekdays($courses,$schedule_ids,$given_date,'search')));
    }

    public function action_draw_course_results_with_weekdays($courses,$schedule_ids,$given_date=null,$source,$are_packages=false)
    {
        // get current week days
        $days = array();

        // Get the first time
        if ($given_date) {
            // If the user provides a start time, use that...
            $first_date = new DateTime($given_date);
        } else {
            $all_schedules = Model_Schedules::get_all_dates_for_schedules($schedule_ids, date('Y-m-d'));

            if (isset($all_schedules[0])) {
                // ... Otherwise get the time of the first schedule...
                $first_date = new DateTime($all_schedules[0]['start_date']);
            } else {
                // ... Otherwise use the current time.
                $first_date = new DateTime();
            }
        }

        // Get the week starting with the first day
        for ($i = 0; $i < 7; $i++) {
            array_push($days, strtotime("+$i day", $first_date->getTimestamp()) );
        }

        $week_days_header = array();
        $week_days = array();
        foreach ($days as $day){
            array_push($week_days_header, array('d' => date("D", $day), 'm' => date("d M", $day)) );
            array_push($week_days, date("Y-m-d", $day) );
        }

        // get schedules for specified week
        $start_date = $week_days[0];
        $end_date = $week_days[6];
        $schedules = Model_Schedules::get_all_dates_for_schedules($schedule_ids, $start_date, $end_date);

        if($source == 'search'){
            $class_right = 'search_courses_right';
            $class_left = 'search_courses_left';
        }else{
            $class_right = '';
            $class_left = '';
        }

        $args['location_ids'] = $args['category_ids'] = array();
        $add_to_cart_schedule_id = @$_GET['add_to_cart_schedule_id'];
        $add_to_cart_timeslot_id = @$_GET['add_to_cart_timeslot_id'];
        $add_to_cart_contact_id = @$_GET['add_to_cart_contact_id'];

        if ( ! empty($_GET['location_id'])) {
            if(false !== strpos($_GET['location_id'], ',')){//several locations
                $args['location_ids'] = $location_ids = explode(',', $_GET['location_id']);
            }
            else{
                $args['location_ids'][0] = $location_id = $_GET['location_id'];
            }
        }
        if ( ! empty($_REQUEST['location_ids'])) { $args['location_ids'] = $_REQUEST['location_ids'];  }
        if ( ! empty($_REQUEST['trainer_ids'])) { $args['trainer_ids'] = $_REQUEST['trainer_ids'];  }
        if ( ! empty($_REQUEST['subject_id'])) { $args['subject_ids'][0] = $subject_id = $_REQUEST['subject_id']; }
        if ( ! empty($_REQUEST['subject_ids'])) { $args['subject_ids'] = $_REQUEST['subject_ids']; }

        if ( ! empty($_REQUEST['category_id'])) { $args['category_ids'][0] = $category_id = $_REQUEST['category_id']; }
        if ( ! empty($_REQUEST['category_ids'])) { $args['category_ids'] = $_REQUEST['category_ids']; }

        if ( ! empty($_REQUEST['topic_id'])) { $args['topic_ids'][0] = $topic = $_REQUEST['topic_id']; }
        ( ! empty($_REQUEST['topic_ids'])) ? $args['topic_ids']= $_REQUEST['topic_ids'] : $args['topic_ids'] = array();

        if ( ! empty($_REQUEST['course_id']))   { $args['course_ids'][0]   = $course_id   = $_REQUEST['course_id']; }
        if ( ! empty($_REQUEST['course_ids']))   { $args['course_ids'] = $_REQUEST['course_ids']; }

        if ( ! empty($_REQUEST['year_id']))     { $args['year_ids'][0]         = $year     = $_REQUEST['year_id'];     }
        ( ! empty($_REQUEST['year_ids'])) ? $args['year_ids']= $_REQUEST['year_ids'] : $args['year_ids'] = array();

        ( ! empty($_REQUEST['keyword'])) ? $keyword = $_REQUEST['keyword'] : $keyword=null;

        if ( ! empty($_REQUEST['level']))    { $args['level']           = $level    = $_REQUEST['level'];    }
        ( ! empty($_REQUEST['level_ids'])) ? $args['level_ids']= $_REQUEST['level_ids'] : $args['level_ids'] = array();

        if ( ! empty($_REQUEST['type']))    { $args['type']           = $level    = $_REQUEST['type'];    }
        ( ! empty($_REQUEST['type_ids'])) ? $args['type_ids']= $_REQUEST['type_ids'] : $args['type_ids'] = array();

        $display_timeslots = Settings::instance()->get('course_availability_display_timeslots');

        $view = (string) View::factory('front_end/course_snippet')
            ->set('courses', $courses)
            ->set('are_packages', $are_packages)
            ->set('week_days_header', $week_days_header)
            ->set('week_days', $week_days)
            ->set('class_right', $class_right)
            ->set('class_left', $class_left)
            ->set('schedules', $schedules)
            ->set('locations_list', Model_Locations::get_locations_without_parent())
            ->set('trainers_list', Model_Contacts3::get_teachers(array('publish' => 1)))
            ->set('subjects_list', Model_Subjects::get_all_subjects(array('publish' => true)))
            ->set('categories_list', Model_Categories::get_all_categories())
            ->set('topics_list', Model_Topics::get_all_topics())
            ->set('courses_list', Model_Courses::get_all_published())
            ->set('years_list', Model_Years::get_all_years())
            ->set('levels_list', Model_Levels::get_all_levels())
            ->set('args', $args)
            ->set('display_timeslots', $display_timeslots);

      return $view;
//        $this->auto_render = FALSE;
    }

    public function action_ajax_filtered_package_course_results()
    {
        $items_per_page = 1000;

        $post = $this->request->post();
        $page_num=isset($post['page']) ? $post['page'] : 1;
        $sortBy=isset($post['sortBy']) ? $post['sortBy'] : 1;
        $given_date = isset($post['given_date']) ? $post['given_date'] : null;

        $show_from = ($page_num - 1) * $items_per_page;

        $args = [
            'order_by' => ($sortBy == 1) ? ' ORDER BY `title` ASC ' : '',
            'start'    => $show_from,
            'limit'    => $items_per_page,
            'cancelled_schedules'    => false,
        ];

        $packageCount = 0;
        $packages = Model_KES_Discount::get_all_discounts_with_advanced_search($post, $args, $packageCount);
        $packages_found_count = count(array_unique(array_column($packages, 'id')));

        $packageResult="";

        if ($packages_found_count > 0) {
            $packageResult = (string) View::factory('front_end/package_feed_snippet')
                ->set('packages', $packages);
        }


        $courseCount=0;

            switch ($sortBy) {
                case 2:  $sortCondition =" order by subject asc "; break;
                case 3:  $sortCondition = "order by type asc "; break;
                default: $sortCondition = ''; break;
            }

            $args = array(
                'order_by' => $sortCondition,
                'start'    => $show_from,
                'limit'    => $items_per_page,
                'cancelled_schedules' => false,
            );
            $result = Model_KES_Discount::get_all_courses_with_advanced_search($post, $args, $courseCount);
            if (count($result['courses']) == 0) {
                unset($post['given_date']);
                $given_date = null;
                $result = Model_KES_Discount::get_all_courses_with_advanced_search($post, $args, $courseCount);
                $given_date = $result['min_date'];
            }

            $courses = $result['courses'];
            $courses_found_count = count(array_unique(array_column($courses, 'id')));;
            $schedule_ids = array();
            // get schedule_ids
            foreach ($courses as $course){
                if ($course['schedule_id'] != null) {
                    array_push($schedule_ids, $course['schedule_id']);
                }
            }

        $courseResult = "";
        if ($courses_found_count > 0) {
            $courseResult = self::action_draw_course_results_with_weekdays($courses, $schedule_ids, $given_date, 'search', true);
        }

        $totalCount = $courses_found_count + $packages_found_count;
        $this->auto_render = FALSE;
        $res=new Package_Course_result();
        $res->jsEvalString="draw_pagination($totalCount, $items_per_page,$page_num)";
        $res->packageResult=$packageResult;
        $res->courseResult=$courseResult;
        $this->auto_render = FALSE;
        return $this->response->body(json_encode($res));

    }

    public function action_ajax_get_package_course_results()
    {
        $post = $this->request->post();
        if(isset($post['id'])){
            $id = $post['id'];
        }else{
            return false;
        }
        if (isset($post['given_date'])) {
            $given_date = $post[ 'given_date' ];
        }else{
            $given_date = null;
        }

        $discount = new Model_KES_Discount($id);
        // get schedule_ids
        $schedule_ids = $discount->get_has_schedules();
        $courses = array();
        $course_ids = array();
        if (empty($schedule_ids)){
            $course_ids_array = Model_KES_Discount::get_course_ids_for_package( $id );
            if (count($course_ids_array)) {
                // get course_ids
                foreach ($course_ids_array as $course) {
                    array_push($course_ids, $course['id']);
                }

            }
        }

        $filters = array('course_ids' => $course_ids, 'schedule_ids' => $schedule_ids);
        $args    = array('limit' => 1000);
        $count   = 0;
        $result  = Model_KES_Discount::get_all_courses_with_advanced_search($filters, $args, $count);
        $courses = $result['courses'];

        foreach ($courses as $course){
            array_push($schedule_ids,$course['schedule_id']);
        }

        $this->auto_render = FALSE;
        return $this->response->body(json_encode(self::action_draw_course_results_with_weekdays($courses,$schedule_ids,$given_date,null)));
    }

    public function action_ajax_show_course_offers_wrap()
    {
        $post = $this->request->post();

        if(isset($post['course_id']) && isset($post['schedule_id']) && isset($post['date'])){
            $course_id =$post['course_id'];
            $schedule_id = $post['schedule_id'];
            $date = $post['date'];
            @$discount_id = $post['discount_id'];
        }else{
            return false;
        }

        $course = Model_Courses::get_course($course_id);

        if ($course['display_availability'] == 'per_course') {
            $schedule_views = Model_Oviews::search(array(
                'type' => 'course',
                'object_id' => $course_id,
                'after' => date('Y-m-d H:i:s',
                    strtotime('-' . Settings::instance()->get('course_visiter_online_users_minutes') . ' minute')),
                'distinct' => true
            ));
            Model_Oviews::add('course', $course_id);
        } else {
            $schedule_views = Model_Oviews::search(array(
                'type' => 'course_schedule',
                'object_id' => $schedule_id,
                'after' => date('Y-m-d H:i:s',
                    strtotime('-' . Settings::instance()->get('course_visiter_online_users_minutes') . ' minute')),
                'distinct' => true
            ));
            Model_Oviews::add('course_schedule', $schedule_id);
        }

        $last_booking_q = DB::select('*')
            ->from(array('plugin_ib_educate_bookings', 'bookings'))
            ->join(array('plugin_ib_educate_booking_has_schedules', 'hs'), 'inner')->on('bookings.booking_id', '=', 'hs.booking_id')
            ->where('bookings.delete', '=', 0);

        if ($course['display_availability'] == 'per_course') {
            $last_booking_q->join(array('plugin_courses_schedules', 'schedules'), 'inner')->on('hs.schedule_id', '=', 'schedules.id')
                ->and_where('schedules.course_id', '=', $course_id);
        } else {
            $last_booking_q->and_where('hs.schedule_id', '=', $schedule_id);
        }
        $last_booking = $last_booking_q->order_by('bookings.created_date', 'desc')
            ->limit(1)
            ->execute()
            ->current();

        $schedules = null;
        if ($course['is_fulltime'] == 'YES') {
            $schedules = array(array(
                'id' => 0,
                'course_title' => $course['title']
            ));
            $course_schedule_details = $schedules;
            $topics = Model_Topics::get_topics(array('course_id' => $course_id));
        } else {
            if ($course['display_availability'] == 'per_course') {
                $schedules = Model_Schedules::search(array('course_id' => array($course_id), 'publish' => 1,
                    'book_on_website' => 1, 'schedule_status' => [Model_Schedules::CONFIRMED, Model_Schedules::IN_PROGRESS]));
                $course_schedule_details = array();
                $topics = array();
                foreach ($schedules as $schedule) {
                    if ($discount_id) {
                        $match_discount = false;
                        $sdiscounts = Model_KES_Discount::get_discounts_for_schedule($schedule['id'], true);
                        foreach ($sdiscounts as $sdiscount) {
                            if ($sdiscount['id'] == $discount_id) {
                                $match_discount = true;
                                break;
                            }
                        }
                        if (!$match_discount) {
                            continue;
                        }
                    }
                    $course_schedule_details_for_date = Model_Schedules::get_course_schedule_details_for_date($course_id, $schedule['id'], $date, ['publish' => 1, 'book_on_website' => 1]);
                    if ($course_schedule_details_for_date) {
                        $course_schedule_details[] = $course_schedule_details_for_date;
                    }
                    $topics = array_merge($topics, Model_Topics::get_topics(array('schedule_id' => $schedule['id'])));
                }

            } else {
                $course_schedule_details = Model_Schedules::get_course_schedule_details_for_date($course_id, $schedule_id, $date, ['publish' => 1, 'book_on_website' => 1]);
                $topics = Model_Topics::get_topics(array('schedule_id' => $schedule_id));
            }
        }
        $date_formatted = date('l F j', strtotime($date));


        // get current week days
        $days = array();
        $now = new DateTime($date);
        for ($i = 0; $i < 7; $i++) {
            array_push($days, strtotime("+$i day", $now->getTimestamp()) );
        }
        $week_days_header = array();
        $week_days = array();
        foreach ($days as $day){
            array_push($week_days_header, array('d' => date("D", $day), 'm' => date("d M", $day)) );
            array_push($week_days, date("Y-m-d", $day) );
        }

        // get schedules for specified week
        $start_date = $week_days[0];
        $end_date = $week_days[6];
        if ($course['display_availability'] == 'per_course') {
            foreach ($schedules as $schedule) {
                $time_slots[] = Model_Schedules::get_all_time_slots_for_given_duration($schedule['id'], $start_date, $end_date);
            }
        } else {
            $time_slots = Model_Schedules::get_all_time_slots_for_given_duration($schedule_id, $start_date, $end_date);
        }

        // for hiding next button if no time slots
        $last_date = new DateTime($end_date);
        $future_days = array();
        for ($i = 0; $i < 7; $i++) {
            array_push($future_days, strtotime("+$i day", $last_date->getTimestamp()));
        }
        $future_week_days = array();
        foreach ($future_days as $future_day){
            array_push($future_week_days, date("Y-m-d",$future_day) );
        }
        $future_start_date = $future_week_days[0];
        $future_end_date = $future_week_days[6];
        if ($course['display_availability'] == 'per_course') {
            foreach ($schedules as $schedule) {
                $future_time_slots = Model_Schedules::get_all_time_slots_for_given_duration($schedule['id'], $future_start_date, $future_end_date);
            }
        } else {
            $future_time_slots = Model_Schedules::get_all_time_slots_for_given_duration($schedule_id, $future_start_date, $future_end_date);
        }

        // for hiding back button if no time slots
        $last_date = new DateTime($start_date);
        $prv_days = array();
        for ($i = -7; $i < 0; $i++) {
            array_push($prv_days, strtotime("$i day", $last_date->getTimestamp()));
        }
        $prv_week_days = array();
        foreach ($prv_days as $prv_day){
            array_push($prv_week_days, date("Y-m-d",$future_day) );
        }
        $prv_start_date = $future_week_days[0];
        $prv_end_date = $future_week_days[6];
        if ($course['display_availability'] == 'per_course') {
            foreach ($schedules as $schedule) {
                $prv_time_slots = Model_Schedules::get_all_time_slots_for_given_duration($schedule['id'], $prv_start_date, $prv_end_date);
            }
        } else {
            $prv_time_slots = Model_Schedules::get_all_time_slots_for_given_duration($schedule_id, $prv_start_date, $prv_end_date);
        }



        // create array of times which will be alternative rows
        $time_rows = array();
        if ($course['display_availability'] == 'per_course') {
            foreach ($time_slots as $i => $stime_slots) {
                $time_rows[$i] = array();
                foreach ($stime_slots as $time_slot) {
                    if (!in_array($time_slot['start_time'], $time_rows[$i])) {
                        array_push($time_rows[$i], $time_slot['start_time']);
                    }
                }
            }
        } else {
            foreach ($time_slots as $time_slot) {
                if (!in_array($time_slot['start_time'], $time_rows)) {
                    array_push($time_rows, $time_slot['start_time']);
                }
            }
        }

        $user = Auth::instance()->get_user();
        if ($user) {
            $contact_ids = Model_Contacts3::get_all_family_members_ids_for_guardian_by_user($user['id']);
            foreach ($course_schedule_details as $i => $csd) {
                foreach ($csd as $ii => $cs) {
                    if (isset($cs['trial_timeslot_free_booking']) && $cs['trial_timeslot_free_booking'] == 1) {
                        if (Model_KES_Bookings::check_existing_booking($contact_ids, $cs['id'])) {
                            $course_schedule_details[$i][$ii]['trial_timeslot_free_booking'] = 0;
                        }
                    }
                }
                if (@$csd['trial_timeslot_free_booking'] == 1) {
                    if (Model_KES_Bookings::check_existing_booking($contact_ids, $csd['id'])) {
                        $course_schedule_details[$i]['trial_timeslot_free_booking'] = 0;
                    }
                }
            }
        }
        //header('content-type: text/plain');print_R($course_schedule_details);exit;
        $schedule = Model_Schedules::get_schedule($schedule_id);
        $display_timeslots = Settings::instance()->get('course_availability_display_timeslots');
        $view = (string) View::factory('front_end/course_offers_wrap_snippet')
            ->set('course_schedule_details', $course_schedule_details)
            ->set('date', $date)
            ->set('date_formatted', $date_formatted)
            ->set('future_time_slots', $future_time_slots)
            ->set('last_booking', $last_booking)
            ->set('prv_time_slots', $prv_time_slots)
            ->set('schedule', $schedule)
            ->set('schedule_views', $schedule_views)
            ->set('time_rows', $time_rows)
            ->set('time_slots', $time_slots)
            ->set('topics', $topics)
            ->set('week_days', $week_days)
            ->set('week_days_header', $week_days_header)
            ->set('course', $course)
            ->set('display_timeslots', $display_timeslots);

        $this->response->body(json_encode($view));
        $this->auto_render = FALSE;
    }

    public function action_ajax_get_time_slots_results()
    {
        $post = $this->request->post();
        if(isset($post['schedule_id'])){
            $schedule_id = $post['schedule_id'];
        }else{
            return false;
        }
        if (isset($post['given_date'])) {
            $given_date = $post[ 'given_date' ];
        }else{
            $given_date = null;
        }

        // get current week days
        $days = array();
        if($given_date){
            $now = new DateTime($given_date);
        }else{
            $now = new DateTime();
        }

        for ($i = 0; $i < 7; $i++) {
            array_push($days, strtotime("+$i day", $now->getTimestamp()) );
        }
        $week_days_header = array();
        $week_days = array();
        foreach ($days as $day){
            array_push($week_days_header, array('d' => date("D", $day), 'm' => date("d M", $day)) );
            array_push($week_days, date("Y-m-d",$day) );
        }


        // get schedules for specified week
        $start_date = $week_days[0];
        $end_date = $week_days[6];
        $time_slots = Model_Schedules::get_all_time_slots_for_given_duration($schedule_id, $start_date, $end_date);

        // for hiding next button if no time slots
        $last_date = new DateTime($end_date);
        $future_days = array();
        for ($i = 1; $i < 8; $i++) {
                array_push($future_days, strtotime("+$i day", $last_date->getTimestamp()));
        }
        $future_week_days = array();
        foreach ($future_days as $future_day){
            array_push($future_week_days, date("Y-m-d",$future_day) );
        }

        $future_start_date = $future_week_days[0];
        $future_end_date = $future_week_days[6];
        $future_time_slots = Model_Schedules::get_all_time_slots_for_given_duration($schedule_id, $future_start_date, $future_end_date);

        // for hiding back button if no time slots
        $first_date = new DateTime($start_date);
        $prv_days = array();
        for ($i = -7; $i < 0; $i++) {
            array_push($prv_days, strtotime("$i day", $first_date->getTimestamp()));
        }
        $prv_week_days = array();
        foreach ($prv_days as $prv_day){
            array_push($prv_week_days, date("Y-m-d",$prv_day) );
        }

        $prv_start_date = $prv_week_days[0];
        $prv_end_date = $prv_week_days[6];
        $prv_time_slots = Model_Schedules::get_all_time_slots_for_given_duration($schedule_id, $prv_start_date, $prv_end_date);

        // create array of times which will be alternative rows
        $time_rows = array();
        if (sizeof($time_slots)>0) {
            foreach ($time_slots as $time_slot) {
                if (!in_array($time_slot[ 'start_time' ], $time_rows)) {
                    array_push($time_rows, $time_slot[ 'start_time' ]);
                }
            }
        }

        self::action_draw_alternative_dates_results_with_weekdays($week_days_header,$time_rows,$week_days,$time_slots,$future_time_slots,$prv_time_slots);
    }

    public function action_draw_alternative_dates_results_with_weekdays($week_days_header,$time_rows,$week_days,$time_slots,$future_time_slots,$prv_time_slots)
    {
        $view = (string) View::factory('front_end/alternative_dates_snippet')
            ->set('week_days_header', $week_days_header)
            ->set('time_rows', $time_rows)
            ->set('week_days', $week_days)
            ->set('prv_time_slots', $prv_time_slots)
            ->set('future_time_slots', $future_time_slots)
            ->set('time_slots', $time_slots);

        $this->response->body(json_encode($view));
        $this->auto_render = FALSE;
    }

    public function action_ajax_get_whole_schedule_events_count()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        if(isset($post['schedule_id'])){
            $schedule_id = $post['schedule_id'];
        }else{
            return false;
        }

        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $data = array();
        $data['events'] = Model_Schedules::get_whole_schedule_events($schedule_id);
        echo json_encode($data);
    }

    public function action_paypal_callback_nbs()
    {
        $ipn_valid = PaypalIPN::is_valid($_POST);
        if ($ipn_valid) {
            Model_CourseBookings::set_processing_status($_POST['custom'], 'Confirmed', '');
            Model_CourseBookings::make_booking_payment($_POST['custom'], $_POST['mc_gross'], 'Paypal', $_POST['txn_id']);

        } else {
            Log::instance()->add(Log::WARNING, "Paypal IPN Invalid:" . strip_tags(print_r($_POST, 1)));
        }
    }

	public function action_ajax_get_categories_from_subject()
	{
		$this->auto_render = FALSE;
		$subject_id = $this->request->param('id');

		$course_category_ids = DB::select('category_id')
			->from('plugin_courses_courses')
			->where('subject_id', '=', $subject_id)
			->where('publish', '=', 1)
			->where('deleted', '=', 0)
			->distinct(true)
			->execute()
			->as_array();

		$category_ids = array();
		foreach ($course_category_ids as $id) $category_ids[] = (int) $id['category_id'];

		echo json_encode($category_ids);
	}

    public function action_ajax_get_categories_from_year()
    {
        $this->auto_render = FALSE;
        $year_id = $this->request->param('id');
        $year = Model_Years::get_year($year_id);
        $location_ids = $this->request->post('location_ids');
        $is_fulltime = $this->request->post('is_fulltime');
        $provider_ids = Model_Providers::get_providers_for_host();

        $selectq = DB::select('category_id')
            ->from(array(Model_Courses::TABLE_COURSES, 'courses'))
                ->join(array(Model_Courses::TABLE_HAS_YEARS, 'has_years'), 'inner')
                    ->on('courses.id', '=', 'has_years.course_id')
                ->join(array(Model_Years::YEARS_TABLE, 'years'), 'inner')
                    ->on('has_years.year_id', '=', 'years.id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('courses.id', '=', 'schedules.course_id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')
                    ->on('schedules.location_id', '=', 'locations.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'building'), 'left')
                    ->on('locations.parent_id', '=', 'building.id')
                ->join(array(Model_courses::TABLE_HAS_PROVIDERS, 'has_providers'), 'left')
                    ->on('has_providers.course_id', '=', 'courses.id')
            ->where('courses.publish', '=', 1)
            ->and_where('courses.deleted', '=', 0)
            ->and_where('schedules.publish', '=', 1)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('schedules.end_date', '>=', date('Y-m-d H:i:s'));
        if ($year['year'] != 'All Levels') {
            $selectq->and_where_open()
                ->or_where('has_years.year_id', '=', $year_id)
                ->or_where('years.year', '=', 'ALL Levels')
                ->and_where_close();
        }
        if ($is_fulltime !== '' && $is_fulltime !== null) {
            $selectq->and_where('courses.is_fulltime', '=', $is_fulltime);
        }
        $selectq->distinct(true);

        if (count($location_ids) > 0) {
            if (count($location_ids) > 0) {
                $selectq->and_where_open();
                    $selectq->or_where('schedules.location_id', 'in', $location_ids);
                    $selectq->or_where('building.id', 'in', $location_ids);
                $selectq->and_where_close();
            }
        }
        if ($provider_ids) {
            $selectq->and_where('has_providers.provider_id', 'in', $provider_ids);
        }

        $course_category_ids = $selectq
            ->execute()
            ->as_array();

        $category_ids = array();
        foreach ($course_category_ids as $id) $category_ids[] = (int) $id['category_id'];

        echo json_encode($category_ids);
    }

    public function action_ajax_get_all_categories()
    {
        $this->auto_render = FALSE;

        $result = DB::select('id','category')
            ->from('plugin_courses_categories')
            ->where('publish', '=', 1)
            ->where('delete', '=', 0)
            ->execute()
            ->as_array();

        echo json_encode($result);
    }

    public function action_ajax_get_all_courses()
    {
        $this->auto_render = FALSE;
        $start = date("Y-m-d H:i:s");
        $end = date("Y-m-d H:i:s", strtotime('+ 365 day'));

        $courses = DB::select('plugin_courses_courses.id','plugin_courses_courses.title')
            ->distinct(TRUE)
            ->from('plugin_courses_courses')
            ->join('plugin_courses_schedules', 'LEFT')->on('plugin_courses_courses.id', '=', 'plugin_courses_schedules.course_id')
                ->on('plugin_courses_schedules.delete', '=', DB::expr('0'))
                ->on('plugin_courses_schedules.publish', '=', DB::expr('1'))
            ->where('plugin_courses_courses.publish', '=', 1)
            ->and_where('plugin_courses_courses.deleted', '=', 0)
            ->and_where('plugin_courses_schedules.end_date', '>=', $start)
            ->and_where('plugin_courses_schedules.start_date', '<=', $end)
            ->execute()
            ->as_array();

        echo json_encode($courses);
    }

    public function action_ajax_get_all_topics()
    {
        $this->auto_render = FALSE;

        $topics = DB::select('plugin_courses_topics.id','plugin_courses_topics.name')
            ->from('plugin_courses_topics')
            ->where('plugin_courses_topics.deleted', '=', 0)
            ->execute()
            ->as_array();

        echo json_encode($topics);
    }

	public function action_ajax_get_courses()
	{
		$this->auto_render    = false;
		$data['subject_ids']  = array($this->request->query('subject_id'));
		$data['category_ids'] = array($this->request->query('category_id'));
        $data['location_ids'] = $this->request->query('location_ids');

        if ($this->request->query('course_id')) {
            $data['course_ids'] = array($this->request->query('course_id'));
        }

        // Don't show duplicates of a course when it has multiple schedules.
        $data['unique_courses'] = true;

		$courses = Model_Courses::filter($data);
		$courses = $courses['data'];

		echo json_encode($courses);
	}

    public function action_ajax_get_subjects()
    {
        $this->auto_render = false;
        $data['year_ids'] = array($this->request->query('year_id'));
        $data['category_ids'] = array($this->request->query('category_id'));
        $data['location_ids'] = $this->request->query('location_ids');
        $data['is_fulltime'] = $this->request->query('is_fulltime');
        $data['publish'] = true;
        $provider_ids = Model_Providers::get_providers_for_host();
        if ($provider_ids) {
            $data['provider_ids'] = $provider_ids;
        }
        $subjects = Model_Subjects::get_all_subjects($data);

        echo json_encode($subjects);
    }

    public function action_ajax_get_schedule()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $schedule_id = $this->request->query('schedule_id');
        $course_id   = $this->request->query('course_id');
        $schedule    = Model_Schedules::get_schedule($schedule_id, array('published' => 1));
        $topics      = Model_Topics::get_topics(array('schedule_id' => $schedule_id));
        $course      = Model_Courses::filter(array('course_ids' => array($schedule_id > 0 ? $schedule['course_id'] : $course_id)));
        $course      = isset($course['data'][0]) ? $course['data'][0] : $course['data'];
        $return      = array('schedule' => $schedule, 'course' => $course, 'topics' => $topics);

        echo json_encode($return);
    }

    private static function deleteAllEventsWithInvalidDatesInDatabase() {
        $sqlQuery = "DELETE FROM plugin_courses_schedules_events WHERE
            datetime_start = '0000-00-00 00:00:00' OR
            datetime_end = '0000-00-00 00:00:00'";
        DB::query(Database::DELETE, $sqlQuery)->execute();
        Database::instance()->commit();
    }

    public function action_ajax_get_timeslots()
    {
        self::deleteAllEventsWithInvalidDatesInDatabase();
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->query();
        $schedule_id = !empty($filters['schedule_id']) ? $filters['schedule_id'] : null;
        if (!empty($filters['course_id']) || !empty($filters['schedule_id'])) {

            $course_id = !empty($filters['course_id']) ? $filters['course_id'] : null;
            $date = !empty($filters['date']) ? $filters['date'] : null;

            $filters['book_on_website'] = 1;
            $filters['publish'] = 1;

            $timeslots = Model_Schedules::get_course_schedule_details_for_date($course_id, $schedule_id, $date, $filters);
        } else {
            $timeslots = array();
        }

        $schedule = new Model_Course_Schedule($schedule_id);
        $duration = count($timeslots);

        $return = [
            'county'             => $schedule->location->get_county()->name,
            'duration'           => $duration,
            'duration_formatted' => ($duration == 1) ? '1 session' : $duration . ' sessions',
            'timeslots'          => $timeslots
        ];

        echo json_encode($return);
    }

    public function action_ajax_get_schedule_availability()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $filters = $this->request->post();
        $args = [];
        $schedule_id = !empty($filters['schedule_id']) ? $filters['schedule_id'] : null;
        $result = array();
        if ($schedule_id) {
            $args['schedule_id'] = $schedule_id;
            //use this to get bare remaining places without trying to book a course
            $args['quantity'] = 0;
            $schedule_capacity = Model_KES_Bookings::check_schedule_capacity($args);
            if (!empty($schedule_capacity['error'])) {
                $result['error'] = $schedule_capacity['error'];
                $result['remaining'] = 0;
            } elseif(!empty($schedule_capacity['overbooked'])) {
                $result['remaining'] = 0;
            } else {
                $result['remaining'] = $schedule_capacity['remaining'];
            }
        }
        echo json_encode($result);
    }

    public function action_test_discounts_for_student()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $user = Auth::instance()->get_user();
        $linked_contacts = empty($user['id']) ? array() : Model_Contacts::search(array('user_id' => $user['id']));
        $guardian = null;
        $new_contact = 1;
        $post = $this->request->post();

        foreach ($linked_contacts as $linked_contact) {
            if (strtolower($linked_contact['first_name']) == strtolower($post['first_name']) && strtolower($linked_contact['last_name']) == strtolower($post['last_name'])) {
                $new_contact = 0;
            }
            if ($linked_contact['mail_list'] != 'Parent/Guardian') {

            }
            if ($linked_contact['email'] == $user['email']) {
                $guardian = $linked_contact;
            }
        }

        $available_discounts = Model_Coursebookings::get_available_discounts(
            $guardian['id'],
            array(
                $post['item']
            ),
            $new_contact
        );

        echo json_encode($available_discounts);
    }

    public function action_ajax_get_years()
    {
        $location_ids = $this->request->post('location_ids');
        $is_fulltime = $this->request->post('is_fulltime');
        $provider_ids = Model_Providers::get_providers_for_host();
        $years = Model_Schedules::get_possible_years($location_ids, $is_fulltime, $provider_ids);
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($years);
    }

    public function action_ajax_validate_coupon()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $code = $this->request->post('code');
        $applied_discount = 0;
        $total = null;
        // Check if the code is valid
        $code_valid = Settings::instance()->get('course_checkout_coupons') && Model_Coursediscounts::search(['code' => $this->request->post('code')]);
        if (!$code_valid) {
            $code_valid = Settings::instance()->get('course_checkout_coupons') && Model_KES_Discount::validate_coupon($this->request->post('code'), 1);
        }
        if (!$code_valid) {
            $message = 'Invalid coupon code';
        } else {
            if ($this->request->post('event_id')) {
                $event    = new Model_Course_Schedule_Event($this->request->post('event_id'));
                $schedule = $event->schedule;
            }
            else if ($this->request->post('schedule_id')) {
                $schedule = new Model_Course_Schedule($this->request->post('schedule_id'));
            } else {
                $schedule = new Model_Course_Schedule();
            }
            $message = $code_valid ? __('Discount has been applied') : __('Invalid discount code');
            $items      = [['id' => $schedule->id, 'fee' => $schedule->fee_amount, 'discount' => 0, 'prepay' => 1]];
            $discounts_per_item = Model_CourseBookings::get_available_discounts(null, $items, 0, $this->request->post('code'));
            $applied_discount = @$discounts_per_item[0]['discount'];
            $total = @$discounts_per_item[0]['total'];
            if (empty($discounts_per_item)) {
                $discounts_per_item = Model_KES_Discount::search(array('code' => $this->request->post('code')));
                if (!empty($discounts_per_item)) {
                    $discount = new Model_KES_Discount ($discounts_per_item[0]['id']);
                    $tmp_cart = Session::instance()->get('ibcart');
                    if (!$tmp_cart) {
                        $tmp_cart = array(
                            'booking' => array(),
                            'booking_id' => null,
                            'client_id' => null,
                            'discounts' => array(),
                            'courses' => array()
                        );
                    }
                    $tmp_data = Controller_FrontEnd_Bookings::get_cart_data($tmp_cart['booking'], $tmp_cart['booking_id'], $tmp_cart['client_id'], $tmp_cart['discounts'], $tmp_cart['courses']);
                    $calculated_discount = $discount->calculate_discount($tmp_cart['client_id'], $tmp_data, $schedule->id,  $this->request->post('code'));
                    if (!empty($discount->failing_conditions)) {
                        $code_valid = false;
                        $message = 'You are not allowed to apply this code';
                    } else {
                        $applied = false;
                        foreach($tmp_data as $key => $data) {
                            if ($discount->get_apply_to() == 'Schedule' && $data['type'] !== 'schedule') {
                                continue;
                            } elseif($discount->get_apply_to() == 'Cart' && $data['type'] !== 'subtotal') {
                                continue;
                            } elseif($discount->get_apply_to() == 'Schedule' && $data['type'] == 'schedule') {
                                $exists = false;
                               if (is_array($tmp_cart['discounts'][$schedule->id])) {
                                    foreach($tmp_cart['discounts'][$schedule->id] as $existing_discount) {

                                        if ($existing_discount['id'] == $discount->get_id()) {
                                            $exists = true;
                                        }
                                    }
                                }

                                if (!$exists) {
                                    $applied = true;
                                    $tmp_cart['discounts'][$schedule->id][$discount->get_id()]  = array(
                                        'id' => $discount->get_id(),
                                        'amount' => $calculated_discount,
                                        'title' => $discount->get_title(),
                                        'summary' => $discount->get_summary(),
                                        'code' =>  $discount->get_code(),
                                        'ignore' => 0,
                                        'custom' => 0,
                                        'memo' => '',
                                        'ignore_others' => false,
                                        'applied_for_timeslots' => 0
                                    );

                                }

                            } elseif ($discount->get_apply_to() == 'Cart' && $data['type'] == 'subtotal') {
                                $exists = false;
                                if (is_array($tmp_cart['discounts']['cart'])) {
                                    foreach($tmp_cart['discounts']['cart'] as $existing_discount) {
                                        if ($existing_discount['id'] == $discount->get_id()) {
                                            $exists = true;
                                        }
                                    }
                                }
                                if (!$exists) {
                                    $applied = true;
                                    $tmp_cart['discounts']['cart'][$discount->get_id()] = array('id' => $discount->get_id(),
                                        'amount' => $calculated_discount,
                                        'title' => $discount->get_title(),
                                        'summary' => $discount->get_summary(),
                                        'code' =>  $discount->get_code(),
                                        'ignore' => 0,
                                        'custom' => 0,
                                        'memo' => '',
                                        'ignore_others' => false,
                                        'applied_for_timeslots' => 0
                                    );
                                }
                            }
                        }
                        if ($applied) {
                            Session::instance()->set('ibcart', $tmp_cart);
                            $applied_discount = $calculated_discount;
                            if($discount->get_apply_to() == 'Schedule' && $tmp_data[0]['type'] == 'schedule') {
                                $total =  $tmp_data[0]['total'] - $calculated_discount;
                            } elseif($discount->get_apply_to() == 'Cart' && $tmp_data[1]['type'] == 'subtotal') {
                                $total =  $tmp_data[1]['total'] - $calculated_discount;
                            } else {
                                $total =  $tmp_data[1]['total'] - $calculated_discount;
                            }
                        }
                    }
                }
            }
        }
        // Get all discounts being applied in order to determine the new final price.

        $result = [
            'success'  => $code_valid,
            'message'  => $message,
            'discount' => $applied_discount,
            'code'     => $code_valid ? 'Applied coupon: ' . $code : 'Invalid code',
            'total'    => $total
        ];
        echo json_encode($result);
    }

    public function action_ajax_get_cart_messages()
    {
        session_commit();
        header('Content-Type: application/json');
        $this->auto_render = false;
        $messages = array();
        $schedule_ids = $this->request->query('schedule_ids');
        $course_ids = $this->request->query('course_ids');
        if ($schedule_ids) {
            $schedule_ids = is_array($schedule_ids) ? $schedule_ids : array($schedule_ids);
            foreach ($schedule_ids as $schedule_id) {
                $schedule = Model_Schedules::get_one_for_details($schedule_id);
                if (!empty($schedule['checkout_alert'])) {
                    $messages[] = $schedule['checkout_alert'];
                }
            }
        }

        if ($course_ids) {
            $course_ids = is_array($course_ids) ? $course_ids : array($course_ids);
            foreach ($course_ids as $course_id) {
                $course = Model_Courses::get_course($course_id);
                $category = Model_Categories::get_category($course['category_id']);
                if (!empty($category['checkout_alert'])) {
                    $messages[] = $category['checkout_alert'];
                }
            }
        }

        $messages = array_unique($messages);
        echo json_encode($messages);
    }

    public static function embed_subjects_menu()
    {
        $subjects = ORM::factory('Course_Subject')->order_by('order')->order_by('date_created', 'desc')->find_all_published();

        return View::factory('front_end/subjects_accordion')->set([
            'type' => 'subject',
            'items' => $subjects
        ])->render();
    }

    public static function embed_categories_menu()
    {
        $categories = ORM::factory('Course_Category')->order_by('order')->order_by('date_created', 'desc')->find_all_published();

        return View::factory('front_end/subjects_accordion')->set([
            'type' => 'category',
            'items' => $categories
        ])->render();
    }

    public static function embed_course_testimonials()
    {
        $page_name = Request::current()->param('page');
        $page = ORM::factory('Page')->where_name($page_name)->find_published();

        // If this is a course details page, get testimonials for the course
        if ($page->layout->layout == 'course_detail' || $page->layout->layout == 'course_detail2') {
            $course = ORM::factory('course')->where('id', '=', Request::$current->query('id'))->find_published();
            $testimonials = $course->testimonials->find_all_published();
            return View::factory('front_end/testimonials_slider_compact')->set('testimonials', $testimonials);
        }
        // Otherwise get testimonials for the page
        else {
            $testimonials = $page->get_testimonials(['include_indirect' => true]);
            return View::factory('front_end/testimonials_slider')->set('testimonials', $testimonials);
        }
    }

    public static function embed_course_selector()
    {
        $page_name = Request::current()->param('page');
        $page = ORM::factory('Page')->where_name($page_name)->find_published();

        return View::factory('front_end/course_selector')->set('courses', $page->get_courses());
    }

    public static function embed_upcoming_feed()
    {
        $limit = Settings::instance()->get('courses_results_per_page');
        $courses = ORM::factory('Course')->find_upcoming(['limit' => $limit]);

        return View::factory('front_end/upcoming_feed')->set(compact('courses'));
    }


}

class Package_Course_result
{
    public $packageResult;
    public $courseResult;
    public $jsEvalString;
}