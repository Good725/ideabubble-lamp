<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Realexpayments extends Model
{
    public function validate($post){

        $valid = true;

        try{
            $checkout_model = new Model_Checkout();
            $products = $checkout_model->get_cart_details();

            if( !isset($post) OR empty($post) ) return false;
            if( !isset($products) OR empty($products) ) return false;

            //Check if post data is the same than the session data, Is only checking the ID and the Amount, the function can be updated to be more or less strict
            foreach ($products->lines as $key => $line) {
                //Same ID
                if((int)$line->product->id != $post->products[$key]->id) return false;
                //Same quantity
                if($line->quantity != $post->products[$key]->quantity) return false;
            }

            //Check if the postal destination is set
            if(!isset($products->shipping_price) OR (is_null($products->shipping_price))){
                return false;
            }
        }
        catch(Exception $e){
            Log::instance()->add(Log::ERROR, $e->getMessage());
            $valid = false;
        }

        return $valid;
    }

    /**
     * Return true if the total price is 0
     * @return bool
     */
    public final function is_free(){
        $checkout_model = new Model_Checkout();
        $products = $checkout_model->get_cart_details();

        $amount_to_pay = @$products->cart_price;

        return ($amount_to_pay > 0) ? false : true;
    }

    public function proccess_payment($post){
        $checkout_model = new Model_Checkout();
        $products = $checkout_model->get_cart_details();

        $amount_to_pay  = $products->final_price;
        $currency       = 'Eur';
        $card_number    = isset($post->ccNum) ? $post->ccNum : '';
        $card_exp_month = isset($post->ccExpMM) ? $post->ccExpMM : '';
        $card_exp_year  = isset($post->ccExpYY) ? $post->ccExpYY : '';
        $card_type      = isset($post->ccType) ? $post->ccType : '';
        $card_ccv       = isset($post->ccv) ? $post->ccv : '';
        $card_name      = isset($post->ccName) ? $post->ccName : '';

        $payment_process_result = Model_PaymentProcessorRealex::process_realex_payment(
            $amount_to_pay, $currency, $card_number, $card_exp_month, $card_exp_year, $card_type, $card_ccv, $card_name
        );

        return $payment_process_result;

    }

    // Just one "post" argument should be needed, but existing uses need to be updated.
    public function send_mail_seller($post, $all_post)
    {
        // Use the messaging plugin, if it is enabled and we are not using the config file
        $use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');

        $invoice_file_id = null;
        $data = (array) $post;
        $template_name = !empty($data['template_name']) ? $data['template_name'] : (!empty($all_post['template_name']) ? $all_post['template_name'] : '');

        if ($template_name && class_exists('Model_Document'))
        {
            if (isset($all_post['lines'])) {
                $cart_details = $all_post;
                $cart_id = $cart_details['id'];
            } else {
                $checkout_model = new Model_Checkout();
                $cart_details = $checkout_model->get_cart_details();
                $cart_id = $checkout_model->get_cart_id();
            }

            $shipping_surname = isset($data['shipping_surname']) ? ' '.$data['shipping_surname'] : '';
            $address          = array_filter(array(str_replace("\n", ', ', $data['address_1']), $data['address_2'], $data['address_3'], $data['address_4']));
            $shipping_address = array_filter(array(str_replace("\n", ', ', $data['shipping_address_1']), $data['shipping_address_2'], $data['shipping_address_3'], $data['shipping_address_4'], $data['shipping_postcode']));

            $address_multiline = preg_replace('/[\r\n]+/', "\n", $data['address_1']."\n".$data['address_2']."\n".$data['address_3']."\n".$data['address_4']);
            $shipping_address_multiline = preg_replace('/[\r\n]+/', "\n", $data['shipping_address_1']."\n".$data['shipping_address_2']."\n".$data['shipping_address_3']."\n".$data['shipping_address_4']."\n".$data['shipping_postcode']);

            $template_data    = array(
                'address'                      => $address_multiline,
                'address_1'                    => $data['address_1'],
                'address_2'                    => $data['address_2'],
                'address_3'                    => $data['address_3'],
                'address_4'                    => $data['address_4'],
                'address_with_commas'          => implode(', ', $address),
                'cart'                         => '',
                'comments'                     => !empty($data['comments']) ? trim($data['comments']) : '',
                'comments_heading'             => trim($data['comments']) ? 'Comments' : '',
                'customer_name'                => $data['ccName'],
                'doc_postfix'                  => '_'.$cart_id,
                'email'                        => $data['email'],
                'gift_card_text'               => !empty($data['gift_card_text']) ? trim($data['gift_card_text']) : (!empty($data['giftcard_text']) ? trim($data['giftcard_text']) : ''),
                'id'                           => $cart_id,
                'payment_mode'                 => ($all_post['payment_type'] == 'realex') ? 'Credit Card' : $all_post['payment_type'],
                'payment_type'                 => $all_post['payment_type'],
                'phone'                        => $data['phone'],
                'products'                     => '',
                'shipping_address'             => $shipping_address_multiline,
                'shipping_address_1'           => $data['shipping_address_1'],
                'shipping_address_2'           => $data['shipping_address_2'],
                'shipping_address_3'           => $data['shipping_address_3'],
                'shipping_address_4'           => $data['shipping_address_4'],
                'shipping_address_with_commas' => implode(', ', $shipping_address),
                'shipping_name'                => trim($data['shipping_name'].$shipping_surname),
                'shipping_postcode'            => $data['shipping_postcode'],
                'shipping_price'               => '€'.(isset($cart_details->shipping_price) ? $cart_details->shipping_price : $cart_details['shipping_price']),
                'size_guide'                   => !empty($data['size_guide_read']) ? $data['size_guide_read'] : 'No',
                'subtotal2'                    => '€'.(isset($cart_details->subtotal2) ? $cart_details->subtotal2 : $cart_details['subtotal2']),
                'template_name'                => $template_name,
                'today_date'                   => date('D jS M Y'),
                'today_dd_mm_yyyy'             => date('d-m-Y')
            );

            // If shipping information is not provided, assume the billing information is the shipping information
            $billing_parameters = array('address_1', 'address_2', 'address_3', 'address_4', 'address_with_commas');
            foreach ($billing_parameters as $parameter) {
                $template_data['shipping_'.$parameter] = !empty($template_data['shipping_'.$parameter]) ? $template_data['shipping_'.$parameter] : $template_data[$parameter];
            }
            $template_data['shipping_name'] = !empty($template_data['shipping_name']) ? $template_data['shipping_name'] : $template_data['customer_name'];

            $number = 0;
            $products_list = '';
            $lines = isset($cart_details->lines) ? $cart_details->lines : $cart_details['lines'];
            foreach ($lines as $product)
            {
                // Only continue if there is a product. (e.g. skip deleted cart items)
                if ($product->product->id)
                {
                    $pdetails = new Model_Product($product->product->id);
                    $pdetails = $pdetails->get_data();
                    $products = array(
                        'id_'.$number       => $product->product->id,
                        'details_'.$number  => $product->product->title,
                        'quantity_'.$number => ' × '.$product->quantity,
                        'price_'.$number    => ' = €'.$product->price
                    );
                    $options_text = '';

                    if (!empty($pdetails['category_ids'])) {
                        $pcats = array();
                        foreach ($pdetails['category_ids'] as $cat_id) {
                            $pcat = Model_Category::get($cat_id);
                            $pcats[] = $pcat[0]['category'];
                        }
                        $options_text .= 'Category: ' . implode(', ', $pcats) . '; ';
                    }

                    foreach ($product->options as $option) {
                        // Check if this option is a matrix combination
                        if (strpos($option->group, '&times;') > -1 && strpos($option->label, '&times;') > -1) {
                            $matrix_groups = explode('×', html_entity_decode($option->group));
                            $matrix_labels = explode('×', html_entity_decode($option->label));
                            foreach ($matrix_groups as $key => $matrix_group) {
                                $group_label = explode('(', $matrix_group);
                                $options_text .= trim($group_label[0]) . ': ' . trim($matrix_labels[$key]) . ', ';
                            }
                        }
                        else {
                            $group_label = explode('(', $option->group);
                            $options_text .= trim($group_label[0]).': ' . $option->label . ', ';
                        }
                    }
                    $options_text = trim($options_text, ', ');
                    $options_text = $options_text ? ' ['.$options_text.']' : '';
                    $products['details_'.$number] .= $options_text;

                    $template_data['cart'] .= $products['details_'.$number].$products['quantity_'.$number].$products['price_'.$number]."\n";
                    $products_list .= '(' . $products['details_'.$number] . ') ' .$products['quantity_'.$number].' '.$products['price_'.$number]."\n";
                    $template_data = array_merge($template_data, $products);

                    $number++;
                }
            }
            $template_data['products'] = Model_Document::maintain_multiline(trim($products_list));

            // If less than five items have been added to the cart, add blanks to fill up five lines in the document
            for (; $number <= 5; $number++)
            {
                $products = array(
                    'id_'.$number           => '',
                    'details_'.$number      => '',
                    'quantity_'.$number     => '',
                    'price_'.$number        => ''
                );
                $template_data = array_merge($template_data,$products);
            }
            $docgenerator = new Model_Document();
            try {
                $docgenerator->auto_generate_document($template_data, $direct = 0, $pdf = true);
            } catch (Exception $e) {
                Log::instance()->add("Error generating email attachment\n".Log::ERROR, $e->getMessage().$e->getTraceAsString());
            }

            $invoice_file_id = $docgenerator->lastFileId;
        }

		if ( ! $use_messaging)
		{
			$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('successful_payment_seller'));
		}

        if ($use_messaging OR (isset($event_id) AND $event_id !== FALSE))
        {
            $checkout_model = new Model_Checkout();
            $canvas = Session::instance()->get('canvas');
            $files = array();
            if ( ! is_null($canvas))
            {
                foreach ($canvas as $product)
                {
					$files[] = '/var/tmp/'.$product['filename'];
					// $files[] = '/var/tmp/'.$product['filename_raster'];
					// $files[] = $product['canvas_image']; // .png of the canvas. This can be used for comparisons, during testing
                }
            }

			if (isset($post->store_id) AND $post->store_id != '')
			{
				$store = Model_Location::get($post->store_id);
			}

            $attachments = array();
            if ($invoice_file_id != null)
            {

                $document = DB::select('name')->from('plugin_files_file')->where('id','=',$invoice_file_id)->execute()->as_array();
                $files[]=$document[0]['name'];
                $attachments[] = array(
                    'file_id' => $invoice_file_id
                );
            }

            // This should really only come from "$all_post", but it needs to be confirmed that nowhere else relying on any of other locations
            $products = isset($post->lines) ? $post : (isset($all_post['lines']) ? (object) $all_post : $checkout_model->get_cart_details());

			$view = View::factory('email/payment_success_seller', array(
					'checkout' => $post,
					'products' => $products,
					'post'     => self::clean_sensitive_data($all_post),
					'store'    => (isset($store) ? $store : NULL)
				)
			)
				->set('skip_comments_in_beginning_of_included_view_file', TRUE)
				->render();

			if ($use_messaging)
			{
                $messaging_model = new Model_messaging;
				$messaging_model->send_template(
                    'successful_payment_seller',
                    count($attachments) ? array('content' => $view, 'attachments' => $attachments) : $view
                );
			}
			elseif (isset($event_id) AND $event_id !== FALSE)
			{
				$notifications_model = new Model_Notifications($event_id);
				$notifications_model->send($view, $files);
			}
        }
    }

    public function send_mail_seller_no_cart($post, $all_post = array())
    {
		$use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');

		if ( ! $use_messaging)
		{
			$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('successful_payment_seller'));
			if ($event_id !== FALSE)
			{
				$notifications_model = new Model_Notifications($event_id);
				$notifications_model->update_tags($notifications_model,(array) $post);
				$notifications_model->send(View::factory('email/payment_success_seller', array(
						'checkout'     => $post,
						'notifications'=>$notifications_model,
						'post'         => self::clean_sensitive_data($all_post),
					)
				)
					->set('skip_comments_in_beginning_of_included_view_file', TRUE)
					->render());
			}
		}
		else
		{
			$body = View::factory('email/payment_success_seller', array('checkout' => $post, 'post' => self::clean_sensitive_data($all_post)))
				->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
			$messaging_model = new Model_messaging;
			$messaging_model->send_template('successful_payment_seller', $body);
		}
    }

    public function send_mail_customer_no_cart($post, $card_details = NULL, $all_post = array())
    {
		$use_messaging = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');
		$body = View::factory('email/payment_success_customer', array('checkout' => $post, 'post' => self::clean_sensitive_data($all_post)))
			->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

		if ( ! $use_messaging)
		{
			$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('successful_payment_customer'));
			if ($event_id !== FALSE)
			{
				$notifications_model = new Model_Notifications($event_id);
				$notifications_model->send_to($post->email, $body);
			}
		}
		else
		{
            if (!isset($post->email)) {
                $post->email = $all_post['email'];
            }
			$extra_targets = array(
				array('id' => NULL,'template_id' => NULL,'target_type' => 'EMAIL','target' => $post->email,'x_details' => 'to','date_created' => NULL)
			);
            if (isset($post->contact_id)){
                $c3 = new Model_Contacts3($post->contact_id);
                if ($c3->has_role('student')) {
                    $parent_id = $c3->get_primary_contact();
                    if ($parent_id) {
                        $extra_targets[] = array('id' => NULL,'template_id' => NULL,'target_type' => 'CMS_CONTACT3','target' => $parent_id,'x_details' => 'to','date_created' => NULL);
                    }
                }
            }
            $messaging_model = new Model_messaging;
			$messaging_model->send_template('successful_payment_customer', $body, NULL, $extra_targets, self::clean_sensitive_data($all_post));

		}
    }

    public function send_mail_customer($post, $cart_details = NULL, $all_post = array())
	{
		$email          = isset($post->email) ? $post->email : (isset($all_post['email']) ? $all_post['email'] : NULL);
		$use_messaging  = Model_Plugin::is_enabled_for_role('Administrator', 'Messaging');
		$header         = '';
		$footer         = '';

		if ( ! $use_messaging)
		{
			$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('successful_payment_customer'));
			if ($event_id != FALSE)
			{
				$notifications_model = new Model_Notifications($event_id);
				$header = $notifications_model->get_footer();
				$footer = $notifications_model->get_footer();
			}
		}

		if ($use_messaging OR (isset($event_id) AND $event_id !== FALSE))
		{

			$checkout_model      = new Model_Checkout();
			if ( is_null($cart_details))
			{
				$cart_details = $checkout_model->get_cart_details();
			}

            // This should really only come from "$all_post", but it needs to be confirmed that nowhere else relying on any of other locations
            $products = isset($post->lines) ? $post : (isset($all_post['lines']) ? (object) $all_post : $checkout_model->get_cart_details());

            $view = View::factory('email/payment_success_customer', array(
				'checkout' => $post,
				'products' => $products,
				'post'     => self::clean_sensitive_data($all_post),
				'footer'   => $footer,
				'header'   => $header
			))->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

			if ($use_messaging)
			{
				$messaging_model = new Model_messaging;
				$extra_targets   = array(
					array(
						'id' => NULL,
						'template_id' => NULL,
						'target_type' => 'EMAIL',
						'target' => $email,
						'x_details' => 'to',
						'date_created' => NULL
					)
				);

				$messaging_model->send_template('successful_payment_customer', $view, NULL, $extra_targets);
			}
			elseif (isset($notifications_model))
			{
				$notifications_model->send_to($email, $view, (isset($cart_details->id) ? $cart_details->id : NULL));
			}
		}
    }

    public function send_mail_report_payment_error($post, $message = ""){
        if (Settings::instance()->get('payments_notify_failed') == 1) {
            $payment_mailing_list = Kohana::$config->load('config')->get('payment_mailing_list');
            $checkout_model = new Model_Checkout();
            $host = $_SERVER['HTTP_HOST'];
            $data = nl2br(html::chars(preg_replace('/\[ccNum\] \=\> ([0-9\s\-]+)/', '[ccnumm] *************', print_r($post, 1))));

            if (empty($contact_mailing_list)) {
                $payment_mailing_list = 'admins';
            }

            $contacts = Model_Formprocessor::get_contacs_from_list($payment_mailing_list);//check empty
            if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
                $messaging = new Model_Messaging();
                $messaging->send_template('payment-error', null, null, array(), array('message' => $message, 'data' => $data, 'host' => $host));
            } else {
                $message .= "<br />\nHost:" . $host;
                $message .= "<br />\nData:" . $data;

                foreach ($contacts as $contact) {
                    $from = Settings::instance()->get('account_verification_sender');
                    $from = ($from == '') ? 'noreply@ideabubble.ie' : $from;
                    IbHelpers::send_email($from, $contact['email'], null, null, 'Online Shop payment error', 'A payment failed attempt. ' . $message); // $from, $to, $cc, $bcc, $subject, $message
                }
            }
        }
    }

    public static function proccess_courses_payment($post){
        $amount_to_pay = $post->amount;
        $currency = 'Eur';
        $card_number = $post->ccNum;
        $card_exp_month = $post->ccExpMM;
        $card_exp_year = $post->ccExpYY;
        $card_type = $post->ccType;
        $card_ccv = $post->ccv;
        $card_name = $post->ccName;



        $payment_process_result = Model_PaymentProcessorRealex::process_realex_payment(
            $amount_to_pay, $currency, $card_number, $card_exp_month, $card_exp_year, $card_type, $card_ccv, $card_name
        );

        return $payment_process_result;

    }

    public function process_invoice_payment($post){

        $amount_to_pay = $post->payment_total;
        $currency = 'Eur';
        $card_number = $post->ccNum;
        $card_exp_month = $post->ccExpMM;
        $card_exp_year = $post->ccExpYY;
        $card_type = $post->ccType;
        $card_ccv = $post->ccv;
        $card_name = $post->ccName;

        $payment_process_result = Model_PaymentProcessorRealex::process_realex_payment(
            $amount_to_pay, $currency, $card_number, $card_exp_month, $card_exp_year, $card_type, $card_ccv, $card_name
        );

        return $payment_process_result;

    }

    public static function send_mail_customer_bookings($post)
	{
        $event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('successful_payment_customer_bookings'));

        if ($event_id !== FALSE)
        {
            $post         = (object) $post;
            $notification = new Model_Notifications($event_id);
            $email        = isset($post->email) ? $post->email : '';
            $parent_email = null;
            if ( ! empty($post->kes_booking_id) && class_exists('Model_Kes_Bookings'))
            {
                $booking_id   = $post->kes_booking_id;
                $booking      = new Model_Kes_Bookings($booking_id);
                $booking_data = $booking->get();
                $email        = ( ! empty($booking_data['email'])) ? $booking_data['email'] : $email;
                $student_id = ( ! empty($post->student_id) ? $post->student_id : (isset($_POST['student_id']) ? $_POST['student_id'] : ''));
                $student      = new Model_Contacts3($student_id);
                if ($student->has_role('student')) {
                    $parent_id = $student->get_primary_contact();
                    if ($parent_id) {
                        $parent = new Model_Contacts3($parent_id);
                        $parent_email = $parent->get_email();
                    }
                }
                $data         = array(
                    'data'          => $post,
                    'booking'       => $booking_data,
                    'booking_items' => $booking->get_all_booking_periods($booking->get_booking_id()),
                    'student'       => $student
                );
                if (!$student_id && @$data['booking']['contact_id']) {
                    $data['student'] = new Model_Contacts3($data['booking']['contact_id']);
                }
            }
            else
            {
                $booking_id   = isset($post->custom) ? $post->custom : '';
                $booking      = Model_CourseBookings::load($booking_id);
                $schedule     = Model_Schedules::get_schedule($booking['has_schedules'][0]['schedule_id']);
                $course       = Model_Courses::get_course(isset($schedule['course_id']) ? $schedule['course_id'] : '');

                $course_event_id = ( ! empty($post->event_id)) ? $post->event_id : '';

                if ( ! empty($course_event_id)) {
                    $schedule_event = new Model_ScheduleEvent($course_event_id);
                }
                else if (is_numeric(@$booking['data']['event_id'])) {
                    $schedule_event = ORM::factory('ScheduleEvent', $booking['has_schedules'][0]['has_timeslots'][0]['timeslot_id']);
                } else {
                    $schedule_event = null;
                }
                $email        = ($booking['email'] != '') ? $booking['email'] : $email;
                $data         = array('data' => $post, 'booking' => $booking, 'schedule' => $schedule, 'course' => $course, 'schedule_event' => $schedule_event);
            }

            if (isset($post->student_email)) {
                @$notification->send_to($post->student_email, View::factory('email/payment_success_customer_bookings', $data)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
            }
            if ($parent_email && $parent_email != $email) {
                @$notification->send_to($parent_email, View::factory('email/payment_success_customer_bookings', $data)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
            }
            return @$notification->send_to($email, View::factory('email/payment_success_customer_bookings', $data)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
        }
		else
		{
			return FALSE;
		}
    }

    public function send_mail_seller_bookings($post)
	{
        $event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('successful_payment_seller_bookings'));
        if ($event_id !== FALSE)
        {
            $post = (object) $post;
            $notification = new Model_Notifications($event_id);

            if ( ! empty($post->kes_booking_id) && class_exists('Model_Kes_Bookings'))
            {
                $booking_id = $post->kes_booking_id;
                $student_id = ( ! empty($post->student_id) ? $post->student_id : (isset($_POST['student_id']) ? $_POST['student_id'] : ''));
                $booking    = new Model_Kes_Bookings($booking_id);
                $data       = array(
                    'data'          => $post,
                    'booking'       => $booking->get(),
                    'booking_items' => $booking->get_all_booking_periods($booking->get_booking_id()),
                    'student'       => new Model_Contacts3($student_id)
                );

                if (!$student_id && @$data['booking']['contact_id']) {
                    $data['student'] = new Model_Contacts3($data['booking']['contact_id']);
                }
            }
            else
            {
                $booking_id      = isset($post->custom) ? $post->custom : '';
                $booking         = Model_CourseBookings::load($booking_id);
                $schedule        = Model_Schedules::get_schedule($booking['has_schedules'][0]['schedule_id']);
                $course          = Model_Courses::get_course($schedule['course_id']);
                $course_event_id = ( ! empty($post->event_id)) ? $post->event_id : '';


                if ( ! empty($course_event_id)) {
                    $schedule_event = new Model_ScheduleEvent($course_event_id);
                }
                else if (is_numeric(@$booking['data']['event_id'])) {
                    $schedule_event = ORM::factory('ScheduleEvent', $booking['has_schedules'][0]['has_timeslots'][0]['timeslot_id']);
                } else {
                    $schedule_event = null;
                }
                $data         = array('data' => $post, 'booking' => $booking, 'schedule' => $schedule, 'course' => $course, 'schedule_event' => $schedule_event);
            }

            $data['view'] = View::factory('email/payment_success_seller_bookings', $data)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();
            if (is_numeric(@$schedule['owned_by']))
            {
                $mm = new Model_Messaging();
                $mm->send_template(
                    'franchisee-checkout-complete',
                    $data['view'],
                    null,
                    array(
                        array('target_type' => 'CMS_USER', 'target' => $schedule['owned_by'])
                    )
                );
            }
            return $notification->send(View::factory('email/payment_success_seller_bookings', $data)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render());
        }
        else if (class_exists('Model_Messaging'))
        {
            $unable_to_retreive_message = "Unable to retrieve";
            $message_params = array();
            $message_params['bookingid'] = $post->kes_booking_id ?? $post->booking_id ?? $unable_to_retreive_message;

            $currency = '€';
            
            foreach ($post->booking_items as $booking_item) {
                foreach ($booking_item as $item) {
                    $message_params['course'] = $item->course_title ?? $unable_to_retreive_message;
                    $message_params['schedule'] = Model_Schedules::get_schedule($item->schedule_id)['name'] ?? $unable_to_retreive_message;
                    $message_params['deposit'] = $currency.number_format(($item->deposit ?? 0), 2);
                    $message_params['fee'] = $currency.number_format(($item->fee ?? 0), 2);
                    break 2;
                }
            }
            
            $message_params['paymenttype'] = ucfirst($post->type ?? $unable_to_retreive_message);
            $message_params['total'] = $currency.number_format(($post->transaction_balance ?? $unable_to_retreive_message), 2);
            $message_params['status'] = Model_Schedules::get_booking_status_label($post->status);
            $mm = new Model_Messaging();
            $mm->send_template(
                'course-booking-admin',
                null,
                date::now(),
                array(),
                $message_params ?? array()
            );
        } else
		{
			return FALSE;
		}
    }

	// Remove sensitive information from post data before adding it to an email
	public static function clean_sensitive_data($data)
	{
		$internal_keys = array('formbuilder_id', 'redirect', 'failpage', 'checkout', 'token');
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value)
            {
                if (is_string($value))
                {
                    $data[$key] = htmlspecialchars($value);
                }

                if (substr($key, 0, 2) == 'cc') // Remove credit card information
                {
                    unset($data[$key]);
                }
                elseif (in_array($key, $internal_keys)) // Remove information for internal operations
                {
                    unset($data[$key]);
                }
            }
        }
		return $data;
	}

}