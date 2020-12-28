<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Extra extends Controller
{
    function action_login()
    {
        $this->auto_render = false;

        $post     = $this->request->post();
        $post     = Validation::factory($post)
            ->rule('email',    'not_empty')
            ->rule('email',    'Valid::email')
            ->rule('password', 'not_empty');

        $email    = isset ($post['email'])    ? $post['email']    : '';
        $password = isset ($post['password']) ? $post['password'] : '';

        $users    = new Model_Users();
        $user     = $users->get_user_by_email($email);

        if ($user['email_verified'] == 0)
        {
            IbHelpers::set_message('This email has not been verified. Please check your email inbox', 'error');
            $this->request->redirect('/customer-login.html');
        }
        elseif ( ! is_null($user['trial_start_date']) AND $user['trial_start_date'] < date('Y-m-d H:i:s', strtotime("-30 days")))
        {
            IbHelpers::set_message('Your 30-day trial has expired', 'error');
            $this->request->redirect('/customer-login.html');
        }
        elseif (Auth::instance()->login($email, $password))
        {
            $this->request->redirect('/customer-payment.html');
        }
        else
        {
            IbHelpers::set_message('The username or password you entered is incorrect', 'error');
            $this->request->redirect('/customer-login.html');
        }
    }

    function action_logout()
    {
        $this->auto_render = false;
        Auth::instance()->logout();
        $this->request->redirect('/customer-login.html');
    }

    function action_update_user()
    {
        $logged_in_user = Auth::instance()->get_user();
        $user_model = new Model_Users();
        $user_data = $user_model->get_user($logged_in_user['id']);
        if($user_data){
            $post = $this->request->post();
            $user_data['name'] = $post['user_first_name'];
            $user_data['surname'] = $post['user_last_name'];
            $user_data['email'] = $post['user_email'];
            $user_data['phone'] = $post['user_phone'];
            $user_data['password'] = $post['user_password_new'] . '';
            $user_data['mpassword'] = $post['user_password_new_confirm'] . '';
            $user_model->update_user_data($logged_in_user['id'], $user_data);
            $this->request->redirect('/customer-payment.html');
        } else {
             $this->request->redirect('/customer-login.html');
        }
    }

    function action_save_customer()
    {
        $post = $this->request->post();
        $this->request->post('checkout', $post['checkout_data']);
        $customer = new Model_Customers($post['id'], $post);
        $customer->set($post);
        $customer->save(true);
        $this->action_extra_pay();
        $this->request->redirect('/customer-payment.html?success=true');
    }

    public function action_extra_pay()
    {
        $user = Auth::instance()->get_user();
        $post = $this->request->post();
        $checkout_data = json_decode($post['checkout_data'], true);
        $customer_id = isset($post['id']) ? $post['id'] : null;
        $customer = new Model_Customers($customer_id);
        $realvault_card_id = isset($checkout_data['realvault_card_id']) ? $checkout_data['realvault_card_id'] : false;

        try {
            Database::instance()->begin();
            if (isset($checkout_data['save_card']) && $checkout_data['save_card']) {
                $card_id = $customer->save_card_to_realvault(
                    $customer_id,
                    $checkout_data['ccType'],
                    $checkout_data['ccNum'],
                    $checkout_data['ccExpMM'] . $checkout_data['ccExpYY'],
                    $checkout_data['ccName'],
                    $checkout_data['ccv']
                );
            }
            if (count($checkout_data['invoices']) == 0) {
                return;
            }

            $total = 0;
            $invoices = array();

            $cart = array();
            $cart['id'] = 'ib-' . $customer_id . '-' . time();
            $cart['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $cart['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $cart['user_id'] = $user['id'];
            $cart['cart_data'] = '';
            $cart['form_data'] = $post['checkout_data'];
            $cart['paid'] = 0;
            $cart['date_created'] = $cart['date_modified'] = date('Y-m-d H:i:s');

            $cartItems = array();

            $vat_rate = (float)Settings::instance()->get('vat_rate');
            foreach ($checkout_data['invoices'] as $invoice_id) {
                $invoice = Model_Extra::getInvoice($invoice_id);
                $invoices[] = $invoice;
                $total += $invoice['amount'];
                $cartItem = array(
                    'cart_id' => $cart['id'],
                    'id' => $invoice['service_type_id'],
                    'title' => $invoice['service_type'] . ' ' .
                        $invoice['url'] . ' ' .
                        $invoice['date_from'] . ', ' .
                        $invoice['date_to'],
                    'quantity' => 1,
                    'price' => $invoice['amount'],
                    'delete' => 0
                );
                $cartItems[] = $cartItem;
            }
            $cart['cart_data'] = json_encode($cartItems);
            $payment_success = false;
            $realvault = new Model_Realvault();
            if ($realvault_card_id) {
                $customer_realvault_id = Model_Customers::get_realvault_id($customer_id);
                $card = Model_Customers::get_card($customer_id, $realvault_card_id);
                if ($card) {
                    $result = $realvault->charge_card(
                        $customer_realvault_id,
                        $card['realvault_id'],
                        $customer_id . '-' . uniqid(),
                        $total,
                        'EUR',
                        $card['cv']
                    );
                    if ((string)$result->result == '00') {
                        $payment_success = true;
                    } else {
                        IbHelpers::set_message($result->message, 'error');
                    }
                } else {
                    throw new Exception('No realvault card');
                }
            } else {
                $result = $realvault->charge(
                    $customer_id . '-' . uniqid(),
                    $total,
                    'EUR',
                    $checkout_data['ccNum'],
                    $checkout_data['ccExpMM'] . $checkout_data['ccExpYY'],
                    $checkout_data['ccType'],
                    $checkout_data['ccName'],
                    $checkout_data['ccv']
                );
                if ((string)$result->result == '00') {
                    $payment_success = true;
                } else {
                    IbHelpers::set_message($result->message, 'error');
                }
            }
            if ($payment_success) {
                foreach ($invoices as $invoice) {
                    DB::update(Model_Extra::INVOICE_TABLE)
                        ->set(array('status' => 'Paid'))
                        ->where('id', '=', $invoice['id'])
                        ->execute();
                }
                if ($customer_id != null) {
                    DB::insert(
                        Model_Cart::CART_TABLE,
                        array_keys($cart)
                    )->values($cart)
                        ->execute();
                    foreach ($cartItems as $cartItem) {
                        DB::insert(
                            Model_Cart::CART_PRODUCTS_TABLE,
                            array_keys($cartItem)
                        )->values($cartItem)
                            ->execute();
                    }

                    $payment_log_result = DB::insert(
                        'plugin_payments_log',
                        array(
                            'cart_details',
                            'customer_name',
                            'customer_telephone',
                            'customer_email',
                            'paid',
                            'payment_type',
                            'payment_amount',
                            'customer_user_id',
                            'delivery_method',
                            'cart_id',
                            'ip_address',
                            'user_agent',
                            'purchase_time'
                        )
                    )->values(
                        array(
                            json_encode($cart),
                            $checkout_data['ccName'],
                            $checkout_data['phone'],
                            $checkout_data['email'],
                            '1',
                            $realvault_card_id ? 'realvault' : 'realex',
                            $total,
                            $user['id'],
                            'online',
                            $cart['id'],
                            $_SERVER['REMOTE_ADDR'],
                            $_SERVER['HTTP_USER_AGENT'],
                            date('Y-m-d H:i:s')
                        )
                    )->execute();
                    if ($payment_log_result) {
                        $payment_log_id = $payment_log_result[0];
                    }

                    $bullet = new Model_BulletHQ();
                    $bullethq_id = Model_Customers::check_bullethq_for_customer($customer_id);
                    $data = Model_Customers::get_data_for_bullethq($customer_id);
                    if ($bullethq_id) {
                        $bullet->update_client($data, $bullethq_id);
                    } else {
                        $cresult = $bullet->add_client($data, $customer_id);
                        $bullethq_id = $cresult['id'];
                    }

                    $bullethq_payment = array();
                    $bullethq_payment['clientId'] = $bullethq_id;
                    $bullethq_payment['bankAccountId'] = Settings::instance()->get('bullethq_bank_account_id');
                    $bullethq_payment['amount'] = $total;
                    $bullethq_payment['currency'] = 'EUR';
                    $bullethq_payment['dateReceived'] = date('Y-m-d');
                    $bullethq_payment['invoiceIds'] = array();
                    foreach ($invoices as $invoice) {
                        $bullethq_payment['invoiceIds'][] = $invoice['bullethq_id'];
                    }
                    $bullethq_payment_result = $bullet->add_payment($bullethq_payment);
                    $bullethqb = new Model_BulletHQB();
                    foreach ($invoices as $invoice) {
                        $bullethqb->email_invoice($invoice['bullethq_id']);
                    }
                }
                IbHelpers::set_message('Payment succeeded', 'info');
            } else {
                IbHelpers::set_message('Payment failed', 'error');
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    function action_register_customer()
    {
        $data          = $this->request->post();

        //check for captcha is valid
        $formprocessor_model = new Model_Formprocessor();
        if ( ! $formprocessor_model->captcha_check($data))
        {
            $response['status']   = 'error';
            $response['message']  = "Your captcha failed validation, please verify you enter the correct details.";
        }
        else
        {
            $data['email'] = $data['contact_email'];

            // Create customer
            $customer    = new Model_Customers('new', $data);
            $customer->set($data);
            $customer_id = $customer->save(true);
            $customer    = $customer->get_customer_details($customer_id);
            $user_id = $customer['user_id'];

            // Send verification email
            $salt         = '3rb|04hegftou,4lm!,0#xbfidjr.qfHhr+*^';
            $hash         = md5($data['email'].$salt);
            $link         = URL::site('frontend/extra/registration_confirmation/'.$user_id.'?hash='.$hash);
            $email_body   = (string) View::factory('frontend/registration_confirmation_email')->set('link', $link);
            $subject      = 'Email confirmation for '.$data['email'].' on Idea Bubble';
            // $from, $to, $cc, $bcc, $subject, $message
            IbHelpers::send_email('extra-helpdesk@ideabubble.ie', $data['email'], null, null, $subject, $email_body);

            $response['status']  = 'success';
            $response['message'] = 'A validation email has been sent';
        }
        $this->response->body(json_encode($response));
    }

    function action_registration_confirmation()
    {
        $this->auto_render = false;
        $id    = $this->request->param('id');
        $hash  = $this->request->query('hash');
        $users = new Model_Users();
        $user  = $users->get_user($id);
        $salt  = '3rb|04hegftou,4lm!,0#xbfidjr.qfHhr+*^';
        $hash2 = md5($user['email'].$salt);

        if ($user['email_verified'] == 1)
        {
            IbHelpers::set_message('Email is already verified');
        }
        elseif ($hash == $hash2)
        {
            $user['email_verified'] = 1;
            $user['password'] = $user['mpassword'] = '';
            $user['trial_start_date'] = date('Y-m-d H:i:s');
            $users->update_user_data($id, $user);
            IbHelpers::set_message('Verification successful', 'success');
        }
        else
        {
            IbHelpers::set_message('Verification failed', 'error');
        }

        $this->request->redirect('/customer-payment.html');
    }


    public function action_update_cart()
    {
        $post = $this->request->post();
        $item_id = (int) $post['item_id'];
        $toggle = ($post['toggle'] == "true") ? true : false;
        $total = Model_Customers::toggle_cart_item($item_id,$toggle);
        $this->response->body($total);
    }

    public function action_check_email()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $ok = Model_Customers::check_email($post['email']);
        $this->response->body($ok);
    }

    public function action_invoice($id = null)
    {
        //assumed everyone for the moment, so "all" will be used.
        $id = !is_null($id) ? $id : $this->request->param('id');

        $bullet = new Model_BulletHQ();
        $is_in_database = Model_Customers::check_bullethq_for_customer($id);
        $customer_data = Model_Customers::get_data_for_bullethq($id);

        $data = array("clientName" => $customer_data['name'],"currency" => "EUR","issueDate" => date('Y-m-d',time()),"dueDate" => date('Y-m-d',strtotime("+1 Month")),"draft" => false);

        if($is_in_database)
        {
            $bullethq_id = Model_Customers::get_bullethq_id($id);
            $bullet->update_client($customer_data,$bullethq_id);
        }
        else
        {
            $bullet->add_client($customer_data,$id);
        }

        $extra_model     = New Model_Extra();
        $services        = $extra_model->get_service_data(null,$id);
        //$services        = Model_Customers::prepare_cart($services);
        $data            = $bullet->create_invoice($data);

        $model           = new Model_Notifications();
        $from            = 'accounts@ideabubble.ie';
        $model->set_from($from);
        $model->send_to($customer_data['email'],View::factory('email/invoice', array('form' => $data,'customer' => $customer_data))->set('skip_comments_in_beginning_of_included_view_file', true)->render(),'Invoice');
        return $data['id'];
    }

    private function make_payments($invoice_id,$client_id)
    {
        $bullethq_id = Model_Customers::get_bullethq_id($client_id);
        $bullet = new Model_BulletHQ();
        $data = array(
            'currency' => "EUR",
            'exchangeRateBankToHome' => 1,
            'exchangeRatePaymentToHome' => 1,
            'exchangeRatePaymentToBank' => 1,
            'amount' => strval(Model_Customers::get_payment_total()),
            'dateReceived' => date('Y-m-d',time()),
            'clientId' => intval($bullethq_id),
            'bankAccountId' => 3877,
            'invoiceIds' => array(intval($invoice_id)));
        $data = $bullet->add_payment($data,$client_id);

        $customer_data = Model_Customers::get_data_for_bullethq($client_id);
        $model           = new Model_Notifications();
        $from            = 'accounts@ideabubble.ie';
        $model->set_from($from);
        $model->send_to($customer_data['email'],View::factory('email/receipt', array('form' => json_decode($data,true),'customer' => $customer_data))->set('skip_comments_in_beginning_of_included_view_file', true)->render(),'Invoice');
        return $data['id'];
    }

    public function action_bulk_update_expiry_date()
    {
        $urls = Model_Extra::bulk_refresh_expiry();
        $this->response->body($urls);
    }

    /**
     * This will generate an email with a list of services due for expiry.
     *
     * Usage : http://WEB-ADDRESS/frontend/extra/email_services_expired
     */
    public function action_email_services_expired()
    {

        $services = Model_Extra::get_services_expired(30,false,null);

        $title  = "Services due for Renewal - 30 days from now";
        $model  = new Model_Notifications();
        $from   = 'IB EXTRA <extra@ideabubble.ie>';
        $to     = 'accounts@ideabubble.ie';

        $model->set_from($from);
        $result = $model->send_to_custom($to,View::factory('email/services-upcoming-renewals-digest', array('services' => $services,'title' => $title))->set('skip_comments_in_beginning_of_included_view_file', true)->render(),$title);

        if ($result)
        {
            echo 'email sent ok';
        }
        else
            echo 'email not sent due to an error';

    }
    
    public function action_delete_card()
    {
        $post = $this->request->post();
        Model_Customers::delete_cards($post['customer_id'], $post['card_delete']);
        $this->request->redirect('/customer-payment.html');
    }
    
    public function action_cron()
    {
        $this->auto_render = false;
        header('content-type: text/plain');
        Model_Extra::auto_invoice_services();
        Model_Extra::auto_pay_invoices();
        Model_Extra::send_renew_reminders();
        echo "Extra Cron Completed";
    }
}