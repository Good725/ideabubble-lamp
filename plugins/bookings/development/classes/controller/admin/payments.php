<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Payments extends Controller_Cms {

    public function before()
    {
        parent::before();
        Model_Kes_Payment::update_payment_plan_penalties();
    }

    public function action_ajax_save_payment(){
        $data = $this->request->post();
        $credit = $data['credit'];

        if ($credit == 1)
        {
            $results = $this->save_payment($data);
        }
        else
        {
            $results = $this->save_journal_payment($data);
        }
        exit(json_encode($results));
    }

    public function action_ajax_delete(){
        $id = $this->request->query('id');
        if($id){
            $status = ORM::factory('Kes_Payment', $id)->values(array('deleted'=>1))->save();
        } else {
            $status = false;
        }
        exit(json_encode($status ? array('status'=>'success') : array('status'=>'error', 'message'=>'The transaction doesen\'t exist.')));
    }

    public function action_ajax_show_payment_modal()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post();
        $credit         = $data['credit'];
        $statuses = ORM::factory('Kes_Payment')->get_payment_status(array('credit' => $data['credit']));
        @$booking_id = $data['booking_id'];
        $booking = null;
        $bookings = array();
        $outstanding = 0;

        if (@$data['booking_id']) {
            $transactions = Model_Kes_Transaction::get_contact_transactions($data['contact_id'], null, is_numeric($data['booking_id']) ? $booking_id : null);
            if ($booking_id == "all" || count($transactions) > 1) {
                foreach ($transactions as $xtransaction) {
                    $bookings[$xtransaction['booking_id']] = Model_KES_Bookings::get_details($xtransaction['booking_id']);
                }
            } else {
                $booking = Model_KES_Bookings::get_details($booking_id);
            }
            //print_r($booking);print_r($transactions);exit;
            $transaction = null;
            $payments = array();
            $credit_journal = ORM::factory('Kes_Transaction')->get_credit_journal($booking['bill_payer'] ? $booking['bill_payer'] : $booking['booking_contact']);
            $contact_journal = isset($credit_journal['contact']) ? $credit_journal['contact'] : NULL;
            $family_journal = isset($credit_journal['family']) ? $credit_journal['family'] : NULL;
            $payment_plan = null;
        } else {
            $transactions = array();
            $transaction = ORM::factory('Kes_Transaction')->get_transaction($data['transaction_id']);
            $statuses = ORM::factory('Kes_Payment')->get_payment_status(array('credit' => $data['credit']));
            $payments = ORM::factory('Kes_Payment')->get_transaction_payment($data['transaction_id'], TRUE);
            $credit_journal = ORM::factory('Kes_Transaction')->get_credit_journal($data['contact_id']);
            $contact_journal = isset($credit_journal['contact']) ? $credit_journal['contact'] : NULL;
            $family_journal = isset($credit_journal['family']) ? $credit_journal['family'] : NULL;
            $payment_plan = Model_Kes_Payment::get_last_payment_plan_for_transaction($data['transaction_id']);
        }

        $payment_plan_installment_to_pay = null;
        if ($payment_plan) {
            foreach ($payment_plan['installments'] as $ppi => $installment) {
                if ($installment['payment_id'] == null) {
                    $payment_plan_installment_to_pay = $installment;
                    break;
                }
            }
        }

        if ($payment_plan_installment_to_pay) {
            $outstanding = $payment_plan_installment_to_pay['amount'];
        } else if(@$booking['outstanding']) {
            $outstanding = $booking['outstanding'];
        } else if(@$transaction['outstanding']) {
            $outstanding = $transaction['outstanding'];
        } else if (count($transactions)) {
            foreach($transactions as $xtransaction) {
                $outstanding += $xtransaction['outstanding'];
            }
        }
        if (count($transactions) == 1 && $transaction == null) {
            $transaction = $transactions[0];
        }

        $view = View::factory('admin/snippets/payment_modal_form')
            ->set('booking_id', $booking_id)
            ->set('booking', $booking)
            ->set('bookings', $bookings)
            ->set('outstanding', $outstanding)
            ->set('transactions', $transactions)
            ->set('transaction', $transaction)
            ->set('statuses',$statuses)
            ->set('credit',$credit)
            ->set('contact_journal',$contact_journal)
            ->set('family_journal',$family_journal)
            ->set('payments',$payments)
            ->set('payment_plan', $payment_plan);

        echo $view;
    }

    public static function save_payment($data)
    {
        $status = TRUE;
        $overpay = 0 ;
        $credit_data = NULL;

        $validation = Validation::factory($data);
        if (@$data['booking_id']) {

        } else {
            $validation->rule('transaction_id', 'not_empty');
        }

        $validation->rule('type', 'not_empty')
            ->rule('amount', 'not_empty')
            ->rule('status','not_empty');

        if (@$data['payment_method'] == 'cc') {
            if (@$data['stripe_payment_intent_id'] == '' && @$data['saved_card_id'] == '') {
                $validation->rule('ccNum', 'not_empty');
                $validation->rule('ccExpMM', 'not_empty');
                $validation->rule('ccExpYY', 'not_empty');
            }
        }
        @$transactions = $data['transactions'];
        unset($data['transactions']);
        // Require name on cheque to be recorded
        if($data['type'] == 'cheque')
        {
            $validation->rule('name_cheque','not_empty');
        }

        if($validation->check())
        {
            if($data['type'] == 'card')
            {
                if (!isset($data['email'])) {
                    $contact = new Model_Contacts3($data['contact_id']);
                    $data['email'] = $contact->get_email();
                }
                $response = Request::factory('/frontend/courses/cart_processor')
                    ->method(Request::POST)
                    ->post($data)
                    ->execute();
                $answer = json_decode($response->body());
                $status = ($answer->status == 'success') ? TRUE : FALSE;
            }
            if($data['type'] == 'sms')
            {
                if (!@$data['charge_mobile_verification_code'] || !@$data['allpoints_tx_id']) {
                    $status = false;
                } else {
                    $allpoints_response = Model_Allpoints::verify_and_charge($data['allpoints_tx_id'], $data['charge_mobile_verification_code']);
                    if (!$allpoints_response) {
                        $status = false;
                        $answer = new stdClass();
                        $answer->message = 'SMS Payment Failed';
                    } else {
                        $status = true;
                    }
                }
            }
            if($status)
            {
                if ($data['create_journal'] == 'create')
                {
                    $overpay = $data['amount'] - $data['transaction_balance'];
                    if($data['journal_type'] == 'family')
                    {
                        $credit_transaction = ORM::factory('Kes_Transaction')->create_credit_journal($data['transaction_id'],$data['family_id'],NULL,$overpay);
                    }
                    else
                    {
                        $credit_transaction = ORM::factory('Kes_Transaction')->create_credit_journal($data['transaction_id'],NULL,$data['contact_id'],$overpay);
                    }
                    if ($credit_transaction)
                    {
                        $note = ' Overpayment on transaction ID:' . $data['transaction_id'] . ' Added to Credit Journal ID:' . $credit_transaction . ' ' . $data['note'];
                        $credit_data = array(
                            'type' => $data['type'],
                            'amount' => $overpay,
                            'status' => $data['status'],
                            'note'  => $note,
                            'transaction_id' => $credit_transaction,
                            'ccName' => $data['ccName'],
                            'ccType' => $data['ccType'],
                            'ccNum' => $data['ccNum'],
                            'ccv' => $data['ccv'],
                            'ccExpYY' => $data['ccExpYY'],
                            'ccExpMM' => $data['ccExpMM'],
                            'name_cheque' => $data['name_cheque']
                        );
                        $status = TRUE;
                    }
                    else
                    {
                        $status = FALSE ;
                    }

                }

                if ($status)
                {
                    $data['amount'] -= $overpay ;
                    if ($data['status']==2)
                    {
                        if ($transactions && count($transactions) > 1) {
                            foreach ($transactions as $transaction) {
                                if ($transaction['total'] == 0 || $transaction['total'] == null) {
                                    continue;
                                }
                                $p_data   = array(
                                    'credit'              => 1,
                                    'transaction_id'      => $transaction['id'],
                                    'amount'              => $transaction['total'],
                                    'bank_fee'            => 0,
                                    'status'              => 2,
                                    'note'                => '',
                                    'name_cheque'         => '',
                                    'credit_transaction'  => null,
                                    'contact_id'          => $data['contact_id'],
                                    'type'                => $data['type']
                                );
                                $status = ORM::factory('Kes_Payment')->save_payment($p_data);
                            }
                        } else if (@$data['btransactions_payments'] && count($data['btransactions_payments']) > 0) {
                            $bfee = $data['bank_fee'];
                            foreach ($data['btransactions_payments'] as $btransactions_payment) {
                                $p_data   = array(
                                    'credit'              => 1,
                                    'transaction_id'      => $btransactions_payment['transaction_id'],
                                    'amount'              => $btransactions_payment['amount'],
                                    'bank_fee'            => $bfee,
                                    'status'              => 2,
                                    'note'                => '',
                                    'name_cheque'         => '',
                                    'credit_transaction'  => null,
                                    'contact_id'          => $data['contact_id'],
                                    'type'                => $data['type']
                                );
                                $status = ORM::factory('Kes_Payment')->save_payment($p_data);
                                $bfee = 0;
                            }
                        } else {
                            $status = ORM::factory('Kes_Payment')->save_payment($data);
                        }
                    }
                    else if ($data['status']==5)
                    {
                        $status = ORM::factory('Kes_Payment')->use_credit($data);
                    }
                }
                if ( ! is_null($status) )
                {
                    $results = array('status'=>'success','message'=>'Payment on transaction ID:'. $data['transaction_id'].' successful.');
                    // Generate Document
                    if (class_exists('Model_Document'))
                    {
                        if ($data['type'] !== 'transfer')
                        {
                            if(@$data['send_backend_booking_emails'] === '1') {
                                $booking = new Model_KES_Bookings($data['booking_id']);
                                $booking_schedules = $booking->get_booking_schedules();
                                foreach($booking_schedules as $booking_schedule)
                                {
                                    $data['schedule_id'] = $booking_schedule['schedule_id'];
                                    $data['deposit'] = "€{$data['amount']}";
                                    // Post needs to be reset otherwise incorrect schedule amount is sent to customer
                                    $_POST = [];
                                    $booking->send_booking_emails($data);
                                }

                            }

                            $doc = NULL;
                            $doc_helper = new Model_Docarrayhelper();
                            $print_data = $doc_helper->booking_receipt($status);
                            $template = DB::select()->from('plugin_files_file')->where('name', '=', $print_data['template_name'])->execute()->as_array();
                            if ($template)
                            {
                                try {
                                    $doc = new Model_Document();
                                    $doc = $doc->auto_generate_document($print_data, $direct = 0, $pdf = false);
                                } catch (Exception $exc) {
                                    //doc generation failed. not a fatal error.
                                }

                                if ($doc)
                                {
                                    $results['message'] .= ' And '. str_replace('_',' ',$print_data['template_name']) .' successfully created';
                                }
                            }
                            else
                            {
                                $results['message'] .= ' But the document template does not exists';
                            }
                        }
                        else
                        {
                            $results['message'] .= 'A separate Template is needed for payment from credit';
                        }
                    }
                    if ( ! is_null($credit_data) )
                    {
                        $status_credit = ORM::factory('Kes_Payment')->save_payment($credit_data);
                        if ($status_credit)
                        {
                            $results['message'] .= ' And on Credit Journal ID:'. $credit_data['transaction_id'] .' successful. And Receipt created in document tab.';
                        }
                        else
                        {
                            $results['status'] = 'error';
                            $results['message'] .= 'There was an error using credit on your payments.';
                        }
                    }
                }
                else
                {
                    $results = array('status'=>'error','message'=>'There was an error saving your payments.');
                }
            }
            else
            {
                $results = array('status'=>'error', 'message'=>"There was a problem with your payment!", "error" => $answer->message);
            }
        }
        else
        {
            $message = '';
            $errors = $validation->errors();
            foreach ($errors as $field => $problem)
            {
                if (isset($problem[0]) && $problem[0] == 'not_empty') {
                    $message .= 'Empty <code>'.$field.'</code> parameter provided.<br />';
                }
                else {
                    $message .= 'Validation error with the <code>'.$field.'</code> parameter.<br />';
                }
                $message .= 'If this problem continues, please <a href="/contact-us.html">contact the administration</a>';
            }
            $results = array('status' => 'error', 'message' => $message);
        }

        return $results;
    }

    public function save_journal_payment($data)
    {
        $results = NULL;
        $validation = Validation::factory($data);
        $validation->rule('transaction_id', 'not_empty')
            ->rule('journal_payment_id', 'not_empty')
            ->rule('total', 'not_empty')
            ->rule('status','not_empty');
        if($validation->check())
        {
            $data['type']='Journal';
            $data['amount']=$data['total'];
            $status = ORM::factory('Kes_Payment')->save_payment($data);
            if ($status)
            {
                $q = DB::insert('plugin_bookings_transactions_payments_journal',array('journaled_payment_id','payment_id'))
                    ->values(array($data['journal_payment_id'],$status))->execute();

                if (@$data['create_journal'] == 'create' && @$data['journal_type'] != '') {
                    if ($data['journal_type'] == 'family') {
                        $credit_transaction = ORM::factory('Kes_Transaction')->create_credit_journal($data['transaction_id'], $data['family_id'], null, $data['amount']);
                    } else {
                        $credit_transaction = ORM::factory('Kes_Transaction')->create_credit_journal($data['transaction_id'], null, $data['contact_id'], $data['amount']);
                    }
                    if ($credit_transaction) {
                        $note = ' Refund for transaction ID:' . $data['transaction_id'] . ' Added to Credit Journal ID:' . $credit_transaction . ' ' . $data['note'];
                        $credit_data = array(
                            'type' => $data['type'],
                            'amount' => $data['amount'],
                            'status' => $data['status'],
                            'note'  => $note,
                            'transaction_id' => $credit_transaction,
                        );
                        $status_credit = ORM::factory('Kes_Payment')->save_payment($credit_data);
                        if ($status_credit) {
                            $results['message'] = ' And on Credit Journal ID:'. $credit_data['transaction_id'] .' successful. And Receipt created in document tab.';
                        } else {
                            $results['status'] = 'error';
                            $results['message'] = 'There was an error using credit on your payments.';
                        }
                    }
                }
            }
            if ($q)
            {
                $results = array(
                    'status'=>'success',
                    'message'=>'Payment Journal on transaction ID:'. $data['transaction_id'].' successful'
                );
                $user = Auth::instance()->get_user();
                $activity = new Model_Activity();
                $activity
                    ->set_item_type('payment_journal')
                    ->set_action('create')
                    ->set_item_id($data['journal_payment_id'])
                    ->set_scope_id($data['transaction_id'])
                    ->set_user_id($user['id'])
                    ->save();
            }
            else
            {
                $results = array(
                    'status'=>'error',
                    'message'=>'There was an error saving your payments.'
                );
            }
        }
        else
        {
            $results = array('status'=>'error', 'message'=>'Please check entered data.');
        }

        return $results;
    }

	public function action_ajax_settle_payg_payments()
	{
		$post = $this->request->post();
		Model_Kes_Settlement::settle_payg($post);
	}

    public function action_payment_plan_modal()
    {
        $this->auto_render = false;
        $data = $this->request->post();
        $transaction    = ORM::factory('Kes_Transaction')->get_transaction($data['transaction_id']);
        $payment_plan   = Model_Kes_Payment::get_last_payment_plan_for_transaction($data['transaction_id']);


        $view = View::factory('admin/snippets/payment_plan_modal_form')
            ->set('transaction', $transaction)
            ->set('payment_plan', $payment_plan);

        echo $view;
    }

    public function action_calculate_payment_plan()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $amount = (float)$post['amount'];
        $deposit = (float)@$post['deposit'];
        $adjustment = (float)@$post['adjustment'];
        $terms = (int)$post['terms'];
        $term_type = $post['term_type'];
        $interest_type = $post['interest_type'];
        $interest = (float)$post['interest'];
        $starts = $post['starts'];

        $payment_plan = Model_Kes_Payment::calculate_payment_plan($amount, $deposit, $adjustment, $terms, $term_type, $interest_type, $interest, $starts);

        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode($payment_plan);
    }

    public function action_save_payment_plan()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $id = (int)@$post['id'];
        $transaction_id = (int)$post['transaction_id'];
        $amount = (float)$post['amount'];
        $deposit = (float)@$post['deposit'];
        $adjustment = (float)@$post['adjustment'];
        $terms = (int)$post['terms'];
        $term_type = $post['term_type'];
        $interest_type = $post['interest_type'];
        $interest = (float)$post['interest'];
        $starts = $post['starts'];
        $installments = @$post['installments'];
        if (is_array($installments)) {
            foreach ($installments as $i => $installment) {
                $installments[$i]['due'] = date::dmy_to_ymd($installment['due']);
            }
        }

        $payment_plan = Model_Kes_Payment::save_payment_plan($id, $transaction_id, $amount, $deposit, $adjustment, $terms, $term_type, $interest_type, $interest, $starts, $installments);

        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode($payment_plan);
    }

    public function action_cancel_payment_plan()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $id = (int)$post['id'];

        $payment_plan = Model_Kes_Payment::cancel_payment_plan($id);

        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        echo json_encode($payment_plan);
    }

    public function action_payonline_data_link()
    {
        $before = $this->request->query('before');
        $after = $this->request->query('after');
        Model_KES_Bookings::payonline_data_link($after, $before);
        exit;
    }
}