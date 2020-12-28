<?php defined('SYSPATH') or die('No direct script access.');
$settings = Settings::instance();
$realex = true;
class Model_Kes_Payment extends ORM
{

    protected $_table_name = 'plugin_bookings_transactions_payments';
    protected $_has_many = array(
        'history'    => array(
            'model'       => 'Kes_Payment_History',
            'foreign_key' => 'payment_id',
        )
    );

    CONST PAYMENT_TABLE = 'plugin_bookings_transactions_payments';
    CONST STATUS_TABLE = 'plugin_bookings_transactions_payments_statuses';
    const PAYMENT_PLAN_TABLE = 'plugin_bookings_transactions_payment_plans';
    const PAYMENT_PLAN_HAS_PAYMENTS_TABLE = 'plugin_bookings_transactions_payment_plans_has_payment';

    public $trigger_save = true;

    /**
     * Save the payment if it doesn't exist or call the save history function if the payment exist
     * @param $data
     * @return bool
     * @throws \Kohana_Exception
     */
    public function save_payment($data)
    {
        $activity = new Model_Activity();
        $user = Auth::instance()->get_user();
        $status = NULL;

        $answer = $this->set('created', date("Y-m-d H:i:s"))->values($data)
            ->set('created_by', $user['id'])
            ->set('modified_by', $user['id'])->save();
        if( ! $answer)
        {
            $status = NULL;
        }

        $installment_to_link = null;
        if (@$data['payment_plan_has_payment_id']) {
            $installment_to_link = ['id' => $data['payment_plan_has_payment_id']];
        } else {
            $payment_plan = Model_Kes_Payment::get_last_payment_plan_for_transaction($data['transaction_id']);
            if ($payment_plan) {
                foreach ($payment_plan['installments'] as $ppi => $installment) {
                    if ($installment['payment_id'] == null) {
                        $installment_to_link = $installment;
                        break;
                    }
                }
            }
        }

        $payment_id = $this->id;
        if ($installment_to_link) {
            $installment_update = array(
                'payment_id' => $this->id,
                'updated_by' => $user['id'],
                'updated' => date::now()
            );
            if ((float)$installment['amount'] != (float)$data['amount']) {
                $new_installment = $installment_to_link;
                $new_installment['amount'] = $installment_to_link['amount'] - $data['amount'];
                $new_installment['interest'] = 0;
                $new_installment['total'] = $new_installment['amount'];
                unset($new_installment['id']);
                $installment_update['total'] = $data['amount'] + $data['bank_fee'];
                $installment_update['amount'] = $data['amount'];
                $installment_update['interest'] = $data['bank_fee'];
                DB::insert(self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE)->values($new_installment)->execute();
            }
            DB::update(self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE)
                ->set($installment_update)
                ->where('id', '=', $installment_to_link['id'])
                ->execute();
        }
        if($data['type'] == 'cheque')
        {
            $values = array(
                'payment_id' => $payment_id,
                'name_cheque' => $data['name_cheque']
            );
            DB::insert('plugin_bookings_transactions_payments_cheque')
                ->values($values)
                ->execute();
        }
        if ($data['type'] == 'Journal' AND $data['status'] == 3)
        {
            $transaction = self::set_return_cheque_fee($data['transaction_id']);
        }
        else
        {
            $transaction = ORM::factory('Kes_Transaction')->set_update_on_payment($data['transaction_id']);
        }
        if( ! $transaction)
        {
            $status = NULL;
        }
        else
        {
            $status = $payment_id;
        }

        // Record Activity
        $activity
            ->set_item_type('payment')
            ->set_action('create')
            ->set_item_id($this->id)
            ->set_user_id($user['id'])
            ->set_scope_id($data['contact_id'] ?? '0')
            ->save();

        $tx = DB::select('*')
            ->from(Model_Kes_Transaction::TRANSACTION_TABLE)
            ->where('id', '=', $data['transaction_id'])
            ->execute()
            ->current();
        DB::update(Model_Contacts3::CONTACTS_TABLE)
            ->set(array('date_modified' => date::now()))
            ->where('id', '=', $tx['contact_id'])
            ->execute();
        if ($this->trigger_save && $payment_id) {
            Model_Automations::run_triggers(Model_Bookings_Paymentsavetrigger::NAME, array('payment_id' => $payment_id));
        }
        return $status;
    }

    /**
     * Save a copy of the old payment to the history table and update the payment
     * @param $id
     * @param $data
     * @param user
     * @return bool
     */
    private function save_history($id,$data,$user)
    {
        $old_payment = ORM::factory('Kes_Payment')->get_payment($id);

        $payment_update = array(
            'amount' => $data['amount'] ,
            'status' => $data['status'],
            'note' => $data['note'],
            'bank_fee' => $data['bank_fee'],
            'updated' => date("Y-m-d H:i:s"),
            'modified_by'=> $user
        );
        $columns = array(
            'payment_id' => $old_payment['id'],
            'transaction_id' => $old_payment['transaction_id'],
            'type' => $old_payment['type'],
            'amount' => $old_payment['amount'],
            'status' => $old_payment['status'],
            'bank_fee' => $old_payment['bank_fee'],
            'created' => $old_payment['created'],
            'updated' => $old_payment['updated'],
            'deleted' => $old_payment['deleted'],
            'operation_date' => date("Y-m-d H:i:s")
        );

        /*
         * Use to update a cheque payment
         */
        if ($data['type'] == 'cheque' AND $data['status'] == 2)
        {
            self::set_return_cheque_fee($old_payment['transaction_id']);
        }

        $update = DB::update('plugin_bookings_transactions_payments')->set($payment_update)->where('id','=',$id)->execute();
        $copy = DB::insert('plugin_bookings_transactions_payments_history')->values($columns)->execute();
        if($update AND $copy)
        {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    /**
     * Use available credit to pay for a transaction
     * @param $data
     * @return bool
     */
    public function use_credit($data)
    {
        $result = FALSE;
        $transaction = ORM::factory('Kes_Transaction')->get_transaction($data['transaction_id']);
        $debit_data = array(
            'transaction_id'=>$data['credit_transaction'],
            'amount'=>$data['amount'],
            'type'=>$data['type'],
            'bank_fee'=>$data['bank_fee'],
            'status'=>6,
            'note'=>$data['note'].'Credit transfer for Booking #'.$transaction['booking_id']
        );
        $credit = $this->save_payment($data);
        $debit = ORM::factory('Kes_Payment')->save_payment($debit_data);
        if ($credit AND $debit )//AND $update)
        {
            $result =TRUE;
        }
        return $result;
    }


    /***            GET Functions              ***/
    /**
     * Get the payment statuses
     * @return mixed
     */
    public static function get_payment_status($args = array())
    {
        $q = DB::select('id','status','credit')
            ->from(self::STATUS_TABLE)
            ->where('publish','=',1)
            ->where('delete','=',0);

        if ( ! empty($args['credit']))
        {
            $q->where('credit', '=', $args['credit']);
        }

        if ( ! empty($args['status']))
        {
            $q->where('status', '=', $args['status']);
        }


        $return = $q->order_by('credit','DESC')
            ->order_by('id')
            ->execute()
            ->as_array();

        if ( ! empty($args['status']) && isset($return[0]))
        {
            $return = $return[0];
        }

        return $return;
    }

    /**
     * @param $transaction
     * @return mixed
     */
    public function get_transaction_payment($transaction,$journal=NULL)
    {
        $q = DB::select(
            'payments.id','payments.transaction_id','payments.type','payments.amount','payments.bank_fee','payments.currency','payments.note',
            'cheque.name_cheque','payments.created','payments.updated','payments.deleted','status.status','status.credit','tran.booking_id','tran.contact_id',
            array('modified_by.id','modified_by_id'),
            array('modified_by.name','modified_by_name'),
            array('modified_by.surname','modified_by_surname'),
            array('modified_by.email','modified_by_email'),
            array('spayments.settlement_id', 'settlement_id'),
            array('spayments.rental', 'settlement_rental'),
            array('spayments.income', 'settlement_income'),
            'payjournal.journaled_payment_id',
            'ppayments.due_date'
        )
            ->from(array('plugin_bookings_transactions_payments','payments'))
            ->join(array('plugin_bookings_transactions','tran'), 'left')
                ->on('tran.id','=','payments.transaction_id')
            ->join(array('plugin_bookings_transactions_payments_cheque', 'cheque'),'left')
                ->on('cheque.payment_id', '=','payments.id')
            ->join(array('engine_users','modified_by'),'LEFT')
                ->on('payments.modified_by', '=', 'modified_by.id')
            ->join(array(self::STATUS_TABLE,'status'),'LEFT')
                ->on('status.id','=','payments.status')
            ->join(array(Model_Kes_Settlement::SETTLEMENT_PAYMENTS_TABLE, 'spayments'), 'LEFT')
                ->on('payments.id', '=', 'spayments.payment_id')
                ->on('spayments.deleted', '=', DB::expr(0))
            ->join(array('plugin_bookings_transactions_payments_journal', 'payjournal'), 'left')
                ->on('payments.id', '=', 'payjournal.payment_id')
            ->join(array(self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'ppayments'), 'left')
                ->on('payments.id', '=', 'ppayments.payment_id');
        if ( ! is_null($journal))
        {
            $journaled = DB::select('journaled_payment_id')->from('plugin_bookings_transactions_payments_journal');
            $q->where('payments.id','NOT IN',$journaled);
        }
        $payments = $q->where('transaction_id','=',$transaction)
            ->and_where('payments.deleted', '=', 0)
            ->order_by('payments.id','DESC')
            ->execute()
        ->as_array();

        $unpaid_payments = DB::select(
            'payments.*',
            'plans.transaction_id',
            array('modified_by.id','modified_by_id'),
            array('modified_by.name','modified_by_name'),
            array('modified_by.surname','modified_by_surname'),
            array('modified_by.email','modified_by_email')
        )
            ->from(array(self::PAYMENT_PLAN_TABLE, 'plans'))
                ->join(array(self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'payments'), 'inner')->on('plans.id', '=', 'payments.payment_plan_id')
                ->join(array('engine_users','modified_by'),'LEFT')
                    ->on('payments.updated_by', '=', 'modified_by.id')
            ->where('payments.payment_id', 'is', null)
            ->and_where('payments.deleted', '=', 0)
            ->and_where('plans.deleted', '=', 0)
            ->and_where('plans.transaction_id', '=', $transaction)
            ->order_by('payments.due_date', 'asc')
            ->execute()
            ->as_array();
        foreach ($unpaid_payments as $i => $unpaid_payment) {
            $unpaid_payment['status'] = 'Unpaid';
            //$unpaid_payment['id'] = '';
            $unpaid_payment['type'] = 'Payment Plan Installment';
            $unpaid_payment['bank_fee'] = $unpaid_payment['interest'] + $unpaid_payment['penalty'];
            $unpaid_payment['currency'] = 'EUR';
            $unpaid_payment['journaled_payment_id'] = null;
            $unpaid_payment['settlement_id'] = null;
            $unpaid_payment['note'] = null;
            $unpaid_payments[$i] = $unpaid_payment;
        }
        $payments = array_merge($payments, $unpaid_payments);

//        return (count($payments) > 0) ? $payments[0] : array();
        return $payments;
    }

    /**
     * Get the payment details
     * @param $id
     * @return array
     */
    public function get_payment($id)
    {
        $payment =  DB::select(
            'payments.id','payments.transaction_id','payments.type','payments.amount','payments.bank_fee','payments.currency','payments.status','payments.note',
            'cheque.name_cheque','payments.created','payments.updated','payments.deleted','payments.currency',
            array('modified_by.id','modified_by_id'),
            array('modified_by.name','modified_by_name'),
            array('modified_by.surname','modified_by_surname'),
            array('modified_by.email','modified_by_email')
        )
            ->from(array('plugin_bookings_transactions_payments','payments'))
            ->join(array('plugin_bookings_transactions_payments_cheque', 'cheque'),'left')
                ->on('cheque.payment_id', '=','payments.id')
            ->join(array('engine_users','modified_by'),'LEFT')
                ->on('payments.modified_by', '=', 'modified_by.id')
            ->where('payments.id','=',$id)
            ->execute()
            ->as_array();

        $payment = (count($payment) > 0) ? $payment[0] : array(); // $payment[0];//$payment (count($payment) > 0) ? $payment[0] : array();
        $status = DB::select('status')->from('plugin_bookings_transactions_payments_statuses')->where('id','=',$payment['status'])->execute()->as_array();
        $payment['status'] = $status[0]['status'];
        return $payment;
    }

    public function get_contact_payment($contact=NULL)
    {
        $payments = NULL;
        if ( ! is_null($contact))
        {
            $payments = DB::select(array('tran.id','transaction'),'tran.booking_id','pay.type','pay.currency','pay.amount','pay.bank_fee','pay.created','pay.note','status.status','tran.id','pay.id',array('pay.type','payment_type'))
                ->from(array('plugin_bookings_transactions','tran'))
                ->join(array(self::PAYMENT_TABLE,'pay'))
                    ->on('pay.transaction_id','=','tran.id')
                ->join(array(self::STATUS_TABLE,'status'))
                    ->on('pay.status','=','status.id')
                ->where('tran.contact_id','=',$contact)
                ->execute()
                ->as_array();
        }
        return $payments;
    }

    public function get_payment_contact_details($id)
    {
        $payment = DB::select('tran.booking_id','pay.id','pay.transaction_id','pay.type','pay.currency','pay.amount','pay.bank_fee','pay.created','pay.note','status.status')
            ->from(array('plugin_bookings_transactions','tran'))
            ->join(array(self::PAYMENT_TABLE,'pay'))
            ->on('pay.transaction_id','=','tran.id')
            ->join(array(self::STATUS_TABLE,'status'))
            ->on('pay.status','=','status.id')
            ->where('pay.id','=',$id)
            ->execute()
            ->as_array();
        $transaction = ORM::factory('Kes_Transaction')->get_transaction($payment[0]['transaction_id']);
        $contact = new Model_Contacts3($transaction['contact_id']);
        $residence = $contact->get_residence();
        $address = $contact->get_address_details($residence);
        $contact_details = array(
            'id'            => $transaction['contact_id'],
            'name'          => $contact->get_contact_name(),
            'address1'      => $address[0]['address1'],
            'address2'      => $address[0]['address2'],
            'address3'      => $address[0]['address3'],
            'town'          => $address[0]['town'],
            'county'        => $address[0]['county'],
            'country'       => $address[0]['country'],
            'postcode'      => $address[0]['postcode']
        );
        return array(
            'transaction'   => $transaction,
            'payment'       => $payment[0],
            'contact'       => $contact_details
        );
    }

                           /***           SET FUNCTIONS          ***/
    /**
     * When a cheque is returned the transaction attached to the payment is updated with a €5 fee
     * @param $id
     */
    private function set_return_cheque_fee($id)
    {
        $transaction = ORM::factory('Kes_Transaction')->get_transaction($id);
        $data = array(
            'amount' => $transaction['total'] ,
            'fee' => intval($transaction['fee']+5),
            'total' => $transaction['total'],
            'updated' => date("Y-m-d H:i:s")
        );
        $update = ORM::factory('Kes_Transaction')->save_history($id,$data);
        return $update;
    }

    public static function calculate_payment_plan($amount, $deposit, $adjustment, $terms, $term_type, $interest_type, $interest, $starts, $first_installment_custom_date = null)
    {
        if ($amount == 0 || $terms == 0) {
            return array();
        }

        $balance = $amount;
        $date = $starts;
        $installments = array();
        $term = 0;
        $first_installment = true;
        if ($deposit > 0) {
            $balance -= $deposit;
            $installment = array(
                'amount' => $deposit,
                'interest' => 0,
                'adjustment' => 0,
                'total' => $deposit,
                'due' => $date,
                'status' => 'Unpaid',
                'balance' => $balance + $adjustment
            );
            $installments[] = $installment;
            $term = 1;
        }
        $terms_left = $terms - $term;

        $total_interest = $interest_type == 'Percent' ? round(($amount  + $adjustment - $deposit) * (($interest / 100) * $terms_left), 2) : ($interest * $terms_left);
        $balance_interest = $total_interest;
        $balance_adjustment = $adjustment;
        $term_interest = round($total_interest / $terms_left, 2);
        $term_amount = round($balance / $terms_left, 2);
        $term_adjustment = round($adjustment / $terms_left, 2);

        for (; $term < $terms; ++$term) {
            if ($first_installment && $first_installment_custom_date) {
                $first_installment = false;
                $date = $first_installment_custom_date;
            } else {
                $date = date('Y-m-d', strtotime("+1 " . $term_type, strtotime($date)));
            }
            $installment_amount = $term_amount;
            $installment_interest = $term_interest;
            $installment_adjustment = $term_adjustment;
            if ($term == ($terms - 1)) { // handle rounding issues(+/- 0.01) on last installment
                if (($balance - $term_amount) != 0) {
                    $installment_amount += ($balance - $term_amount);
                }

                if (($balance_interest - $term_interest) != 0){
                    $installment_interest -= ($balance_interest - $term_interest);
                }

                if (($balance_adjustment - $term_adjustment) != 0){
                    $installment_adjustment -= ($balance_adjustment - $term_adjustment);
                }
            }
            $balance -= $installment_amount;
            $balance_interest -= $term_interest;
            $balance_adjustment -= $term_adjustment;

            $installment = array(
                'amount' => $installment_amount,
                'adjustment' => $installment_adjustment,
                'interest' => $installment_interest,
                'total' => $installment_amount + $installment_interest + $installment_adjustment,
                'due' => $date,
                'status' => 'Unpaid',
                'balance' => round($balance + $balance_adjustment, 2)
            );
            $installments[] = $installment;
        }

        return $installments;
    }

    public static function save_payment_plan($id, $transaction_id, $amount, $deposit, $adjustment, $terms, $term_type, $interest_type, $interest, $starts, $installments = array(), $start_after = null, $send_reminder = true)
    {
        try {
            $db = Database::instance();
            $db->begin();
            if ($term_type != 'custom') {
                $installments = self::calculate_payment_plan($amount, $deposit, $adjustment, $terms, $term_type, $interest_type, $interest, $starts, $start_after);
            }
            
            $now = date::now();
            $user = Auth::instance()->get_user();

            $data = array(
                'transaction_id' => $transaction_id,
                'outstanding' => $amount,
                'deposit' => $deposit,
                'adjustment' => $adjustment,
                'term' => $terms,
                'interest_type' => $interest_type,
                'interest' => $interest,
                'starts' => $starts,
                'created' => $now,
                'created_by' => @$user['id'],
                'updated' => $now,
                'updated_by' => @$user['id'],
                'status' => 'Outstanding',
                'term_type' => $term_type
            );
            if ($id > 0) {
                DB::update(self::PAYMENT_PLAN_TABLE)->set($data)->where('id', '=', $id)->execute();
                DB::update(self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('due_date', '>=', $now)
                    ->and_where('payment_plan_id', '=', $id)
                    ->execute();
            } else {
                // cancel previos payment plan & create a new one when adjusting
                $prev_pp = self::get_last_payment_plan_for_transaction($transaction_id);
                if ($prev_pp) {
                    self::cancel_payment_plan($prev_pp['id']);
                }

                $inserted = DB::insert(self::PAYMENT_PLAN_TABLE)->values($data)->execute();
                $id = $inserted[0];
                $contact_id = DB::select('contact_id')
                    ->from('plugin_bookings_transactions')
                    ->where('id', '=', $transaction_id)->execute()->get('contact_id');
                $activity = new Model_Activity();
                $activity
                    ->set_item_type('payment_plan')
                    ->set_action('create')
                    ->set_item_id($id)
                    ->set_scope_id($contact_id)
                    ->save();
            }

            foreach ($installments as $installment) {
                if ($installment['due'] == '') {
                    $installment['due'] = date::today();
                }
                if (preg_match('#(\d\d\-\d\d-\d\d\d\d)#', $installment['due'])) {
                    $installment['due'] = date::dmy_to_ymd($installment['due']);
                }
                DB::insert(self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE)
                    ->values(
                        array(
                            'payment_plan_id' => $id,
                            'amount' => $installment['amount'],
                            'adjustment' => @$installment['adjustment'] ?: 0,
                            'interest' => $installment['interest'],
                            'penalty' => 0,
                            'total' => $installment['total'],
                            'due_date' => $installment['due'],
                            'created' => $now,
                            'created_by' => @$user['id'],
                            'updated' => $now,
                            'updated_by' => @$user['id'],
                        )
                    )
                    ->execute();
            }

            $reminder_interval_days = Settings::instance()->get('bookings_payment_plan_reminder_days_before');
            if ($reminder_interval_days && $send_reminder) {
                $first_installment = DB::select('tx.contact_id', 'tx.booking_id', 'ppp.*')
                    ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'tx'))
                       ->join(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'), 'inner')
                            ->on('tx.id', '=', 'pp.transaction_id')
                        ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'ppp'), 'inner')
                            ->on('pp.id', '=', 'ppp.payment_plan_id')
                        ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                            ->on('tx.booking_id', '=', 'bookings.booking_id')
                        ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                            ->on('bookings.contact_id', '=', 'students.id')
                        ->join(array(Model_Contacts3::CONTACTS_TABLE, 'payers'), 'left')
                            ->on('tx.contact_id', '=', 'payers.id')
                    ->where('tx.deleted', '=', 0)
                    ->and_where('pp.deleted', '=', 0)
                    ->and_where('ppp.deleted', '=', 0)
                    ->and_where('ppp.due_date', '<=',
                        DB::expr("DATE_ADD(CURDATE(), INTERVAL $reminder_interval_days DAY)"))
                    ->and_where('ppp.payment_id', 'is', null)
                    ->and_where('pp.id', '=', $id)
                    ->execute()
                    ->current();
                if ($first_installment) {
                    Model_KES_Bookings::send_payment_plan_reminder($first_installment);
                }
            }
            $db->commit();

            Model_Automations::run_triggers(Model_Bookings_Transactionsavetrigger::NAME, array('transaction_id' => $transaction_id));
            return $id;
        } catch (Exception $exc) {
            $db->rollback();
            throw $exc;
        }
    }

    public static function get_last_payment_plan_for_transaction($tx_id)
    {
        $payment_plan = DB::select('*')
            ->from(self::PAYMENT_PLAN_TABLE)
            ->where('transaction_id', '=', $tx_id)
            ->and_where('deleted', '=', 0)
            ->and_where('status', '=', 'Outstanding')
            ->order_by('id', 'desc')
            ->execute()
            ->current();
        if ($payment_plan) {
            $payment_plan['installments'] = DB::select('*')
                ->from(self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE)
                ->where('payment_plan_id', '=', $payment_plan['id'])
                ->and_where('deleted', '=', 0)
                ->order_by('due_date', 'asc')
                ->execute()
                ->as_array();
        }
        return $payment_plan;
    }

    public static function cancel_payment_plan($id)
    {
        try {
            $db = Database::instance();
            $db->begin();

            $now = date::now();
            $user = Auth::instance()->get_user();
            DB::update(self::PAYMENT_PLAN_TABLE)
                ->set(array('status' => 'Cancelled', 'updated' => $now, 'updated_by' => $user['id']))
                ->where('id', '=', $id)->execute();
            DB::update(self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE)
                ->set(array('deleted' => 1))
                ->where('due_date', '>=', $now)
                ->and_where('payment_plan_id', '=', $id)
                ->execute();

            $db->commit();
            return $id;
        } catch (Exception $exc) {
            $db->rollback();
            throw $exc;
        }
    }

    public static function update_payment_plan_penalties()
    {
        //penalties disabled for now.
        return;
        DB::query(null,
            'UPDATE ' . self::PAYMENT_PLAN_HAS_PAYMENTS_TABLE . '
                SET
                    penalty = ROUND((interest / 30) * DATEDIFF(CURDATE(), due_date), 2),
                    total = amount + adjustment + interest + ROUND((interest / 30) * DATEDIFF(CURDATE(), due_date), 2)
                WHERE deleted = 0 AND due_date <= CURDATE()'
        )->execute();
    }

    public static function search($params = array())
    {
        $selectq = DB::select('payments.*', 'tx.contact_id', DB::expr('GROUP_CONCAT(schedules.name) as schedules'), DB::expr('GROUP_CONCAT(courses.title) as courses'))
            ->from(array(self::PAYMENT_TABLE, 'payments'))
                ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'tx'), 'inner')
                    ->on('payments.transaction_id', '=', 'tx.id')
                    ->join(array(Model_Kes_Transaction::TABLE_HAS_SCHEDULES, 'has_schedules'), 'left')
                        ->on('tx.id', '=', 'has_schedules.transaction_id')
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                        ->on('has_schedules.schedule_id', '=', 'schedules.id')
                    ->join(array(Model_Kes_Transaction::TABLE_HAS_COURSES, 'has_courses'), 'left')
                        ->on('tx.id', '=', 'has_courses.transaction_id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                        ->on('has_courses.course_id', '=', 'courses.id')
            ->where('payments.deleted', '=', 0);

        if (@$params['type']) {
            $selectq->and_where('payments.type', 'in', $params['type']);
        }

        if (@$params['status']) {
            $selectq->and_where('payments.status', 'in', $params['status']);
        }

        $selectq->group_by('payments.id');
        $payments = $selectq->execute()->as_array();


        return $payments;
    }
}