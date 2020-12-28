<?php defined('SYSPATH') or die('No direct script access.');

class Model_Kes_Transaction extends ORM {
    const TABLE_HAS_SCHEDULES = 'plugin_bookings_transactions_has_schedule';
    const TYPE_TABLE = 'plugin_bookings_transactions_types';
    const TRANSACTION_TABLE = 'plugin_bookings_transactions';
    const TABLE_HAS_COURSES = 'plugin_bookings_transactions_has_courses';

    protected $_table_name = 'plugin_bookings_transactions';

    protected $_has_many = array(
        'payments'    => array(
            'model'       => 'Kes_Payment',
            'foreign_key' => 'transaction_id',
        ),
        'history'    => array(
            'model'       => 'Kes_Transaction_History',
            'foreign_key' => 'transaction_id',
        )
    );

    public $trigger_save = true;

    /**
     * Save a new transaction
     * @param $post
     * @return bool
     * @throws \Kohana_Exception
     */
    public function save_transaction($data)
    {
        $activity = new Model_Activity();
        $user     = Auth::instance()->get_user();

        $status = NULL ;
        // Removed the search for booking ID to set save a new transaction
        // $tutorial = self::is_booking_tutorial($data['booking_id']);
        $type     = $data['type'];
        $tut_stat = TRUE;
        $billed   = TRUE ;

        $data['modified_by'] = $user['id'];
        if ( ! isset($data['id']) OR $data['id'] == '')
        {
            $data['created_by'] = $user['id'];
        }
        // Create a billed booking for the bill payer and create a journal on the student account
        if (isset($data['transaction_billed']))
        {
            $payer_family = Model_Contacts3::get_family_id_by_contact_id($data['payer_id']);
            $billed = ORM::factory('Kes_Transaction')
                ->values($data)
                ->set('created', date("Y-m-d H:i:s"))
                ->set('updated', date("Y-m-d H:i:s"))
                ->set('contact_id', $data['payer_id'])
                ->set('family_id',$payer_family)
                ->set('type',8)
                ->save();
            $type = 9;
        }
        $stat = ORM::factory('Kes_Transaction')
            ->values($data)
            ->set('created', date("Y-m-d H:i:s"))
            ->set('updated', date("Y-m-d H:i:s"))
            ->set('type',$type)
            ->save();
        if (@$data['contact_id']) {
            DB::update(Model_Contacts3::CONTACTS_TABLE)
                ->set(array('date_modified' => date::now()))
                ->where('id', '=', $data['contact_id'])
                ->execute();
        }
        if ( ! ($stat AND $tut_stat AND $billed) )
        {
            $status = '';
        }
        else
        {
            $status = $stat->id;
            if (@$data['schedule_id']) {
                DB::insert(Model_Kes_Transaction::TABLE_HAS_SCHEDULES)
                    ->values(array('transaction_id' => $stat->id, 'schedule_id' => $data['schedule_id']))
                    ->execute();
            }

            if ($this->trigger_save) {
                Model_Automations::run_triggers(Model_Bookings_Transactionsavetrigger::NAME, array('transaction_id' => $stat->id));
            }
        }

        $activity
            ->set_item_type('transaction')
            ->set_action('create')
            ->set_item_id($stat->id)
            ->set_scope_id($data['contact_id'])
            ->set_user_id($user['id'])
            ->save();
        return $status;
    }

    /**
     * Update a transaction and copy the old transaction to the transaction history table
     * @param $id
     * @param $data
     * @return bool
     */
    public function save_history($id = NULL, $data)
    {
		$activity = new Model_Activity();
        $user = Auth::instance()->get_user();
		$old_transaction = null;
        if ( ! is_null($id))
        {
            $old_transaction = ORM::factory('Kes_Transaction')->get_transaction($id);
        }
		if($old_transaction == null && isset($data['booking_id'])){
			$existing_transactions = DB::select('tx.*',array('tx.type', 'type_id'))
										->from(array(self::TRANSACTION_TABLE, 'tx'))
										->join(array(self::TYPE_TABLE, 'ty'), 'inner')->on('tx.type', '=', 'ty.id')
										->where('tx.booking_id', '=', $data['booking_id'])
										->and_where('tx.deleted', '=', 0)
										->and_where('ty.credit', '=', 1)
										->execute()
										->as_array();
			if(isset($existing_transactions[0])){
				$old_transaction = $existing_transactions[0];
				$id = $old_transaction['id'];
			}
		}        
		
		if($old_transaction == null){
			return false;
		}
	
        $transaction_update = array(
            'updated' => date("Y-m-d H:i:s"),
            'modified_by' => $user['id']
        );
		if(isset($data['amount'])){
			$transaction_update['amount'] = $data['amount'];
		}
		if(isset($data['fee'])){
			$transaction_update['fee'] = $data['fee'];
		}
		if(isset($data['total'])){
			$transaction_update['total'] = $data['total'];
		}
            
        isset($data['discount']) ? $transaction_update['discount'] = $data['discount'] : $transaction_update['discount'] =0.0 ;
        if (isset($data['type']))
        {
            $transaction_update['type']=$data['type'];
        }
        $columns = array(
            'transaction_id' => $old_transaction['id'],
            'booking_id' => $old_transaction['booking_id'],
            'amount' => $old_transaction['amount'],
            'fee' => $old_transaction['fee'],
            'total' => $old_transaction['total'],
            'type' => $old_transaction['type_id'],
            'created' => $old_transaction['created'],
            'updated' => $old_transaction['updated'],
            'deleted' => $old_transaction['deleted'],
            'operation_date' => date("Y-m-d H:i:s"),
            'modified_by' => $old_transaction['modified_by']
        );
        $update = DB::update(self::TRANSACTION_TABLE)->set($transaction_update)->where('id','=',$id)->execute();
        $copy = DB::insert('plugin_bookings_transactions_history')->values($columns)->execute();
        $activity
            ->set_item_type('transaction')
            ->set_action('update')
            ->set_item_id($id)
            ->set_user_id($user['id'])
            ->set_scope_id($old_transaction['contact_id'])
            ->save();
        if($update AND $copy)
        {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    /**
     * @param $booking_id
     * @param $type
     * @param $amount
     * @return null|object
     * @throws \Kohana_Exception
     */
    public function create_transaction($transaction_data, $payer = null)
    {
        $status = NULL ;
        $result = array();
        $activity = new Model_Activity();
        $user = Auth::instance()->get_user();
        if (is_null($payer)) {
            $data = DB::select('plugin_ib_educate_bookings.booking_id','plugin_ib_educate_bookings.contact_id','plugin_ib_educate_bookings.amount')
                ->from('plugin_ib_educate_bookings')
                ->where('plugin_ib_educate_bookings.booking_id','=',$transaction_data['booking_id'])
                ->execute()
                ->as_array();
            $family_id = Model_Contacts3::get_family_id_by_contact_id($data[0]['contact_id']);
            $payer = $data[0]['contact_id'];
        } else {
            $family_id = Model_Contacts3::get_family_id_by_contact_id($payer);
        }
        if ($transaction_data['type'] == 2) {
            $due = $this->get_next_due_date($transaction_data['booking_id']);
        }

        $stat = ORM::factory('Kes_Transaction')
            ->set('booking_id',$transaction_data['booking_id'])
            ->set('created_by', $user['id'])
            ->set('family_id',$family_id)
            ->set('contact_id',$payer)
            ->set('modified_by', $user['id'])
            ->set('created', date("Y-m-d H:i:s"))
            ->set('updated', date("Y-m-d H:i:s"))
            ->set('amount',$transaction_data['amount'])
            ->set('total',$transaction_data['total'])
            ->set('type',$transaction_data['type'])
            ->set('discount',$transaction_data['discount'])
            ->set('payment_due_date',isset($due)?$due:date("Y-m-d H:i:s"))
            ->set('fee', @$transaction_data['fee'] ?: 0)
            ->save();

        $status = $stat->id;
        $activity
            ->set_item_type('transaction')
            ->set_action('create')
            ->set_item_id($status)
            ->set_user_id($user['id'])
            ->save();

        $notificationRecipients = array();
        foreach($transaction_data['schedule'] as $s_index => $schedule)
        {
            $has_schedule = array('transaction_id' => $status);
            if (is_array($schedule)) {
                $has_schedule['schedule_id'] = $schedule['schedule_id'];
                if (@$schedule['payg_period']) {
                    $has_schedule['payg_period'] = $schedule['payg_period'];
                }
                if (@$schedule['event_id']) {
                    $has_schedule['event_id'] = $schedule['event_id'];
                }
            } else {
                $has_schedule['schedule_id'] = $schedule;
                if (@$transaction_data['deposit'][$s_index]) {
                    $has_schedule['deposit'] = $transaction_data['deposit'][$s_index];
                }
            }
            DB::insert('plugin_bookings_transactions_has_schedule')->values($has_schedule)->execute();
            $trainerId = DB::select('trainer_id')
                ->from('plugin_courses_schedules')
                ->where('id', '=', $has_schedule['schedule_id'])
                ->execute()
                ->get('trainer_id');
            if ($trainerId) {
                $notificationRecipients[] = array(
                    'target_type' => 'CMS_CONTACT3',
                    'target' => $trainerId
                );
            }
        }

        if ($this->trigger_save) {
            Model_Automations::run_triggers(Model_Bookings_Transactionsavetrigger::NAME, array('transaction_id' => $stat->id, 'payment_method' => @$transaction_data['payment_method']));
        }

        $result['transaction'] = $status;
        $result['message'] = 'Transaction created';

        return $result;
    }

    /**
     * Check if credit journal exist and update it by the amount or create the credit journal for the amount
     * @param      $transaction
     * @param null $family
     * @param null $contact
     * @param      $amount
     * @return mixed|null
     */
    public function create_credit_journal($transaction,$family_id=NULL,$contact_id=NULL,$amount)
    {
        $transaction_data = $this->get_transaction($transaction);
        $credit_transaction = NULL;
        $q = DB::select('id','amount','fee','total')
            ->from(self::TRANSACTION_TABLE);

        if ( ! is_null($contact_id) )
        {
            $q->where('contact_id','=',$contact_id);
        }
        else if ( ! is_null($family_id))
        {
            $q->where('family_id','=',$family_id);
        }
        $answer = $q->and_where('booking_id','=',0)
            ->and_where('deleted','=',0)
            ->execute()
            ->as_array();
        if ( $answer)
        {
            $data = array(
                'amount' => $answer[0]['amount'] + $amount ,
                'fee' => $answer[0]['fee'],
                'total' => $answer[0]['total'] + $amount
            );
            ORM::factory('Kes_Transaction')->save_history($answer[0]['id'],$data);
            $credit_transaction = $answer[0]['id'];
        }
        else
        {
            $data = array(
                'id' => NULL,
                'booking_id' => 0,
                'amount' => $amount,
                'fee' => 0,
                'total' => $amount,
                'type' => 3,
                'contact_id' => is_null($contact_id) ? NULL : $contact_id ,
                'family_id' => $family_id,
                'operation_date' => date("Y-m-d H:i:s")
            );
            $credit_transaction = ORM::factory('Kes_Transaction')->save_transaction($data);
//            $credit_transaction = $credit_transaction->id;
        }
        return $credit_transaction;
    }

    /**
     * @param null $id
     * @return bool
     */
    public function delete_transaction($id = null)
    {
        $status = true;
        if($id){
            $transaction = ORM::factory('Kes_Transaction', $id);
            if($transaction->loaded()){
                DB::update(ORM::factory('Kes_Payment')->table_name())
                    ->set(array('deleted' => '1'))
                    ->where('transaction_id', '=', $id)
                    ->execute();
                $transaction->values(array('deleted' => 1))->save();
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }
        return $status;
    }


    /**
	 * This function is called "calculate_outstanding_balance", but seems to calculate
	 * the amount that has been paid, rather than the amount yet to be paid
	 *
     * @param null $booking_id
     * @param null $transaction_id
     * @return int
     */
    public function calculate_outstanding_balance($booking_id=NULL,$transaction_id=NULL)
    {
        $q = DB::select('pay.id','pay.amount','pay.transaction_id','pay.status','tran.total', 'tran.fee',
            'pay.transaction_id', 'tran.booking_id','tran.id','tran.type','type.credit',
            array('status.credit','pay_credit')
        );
        if ( ! is_null($transaction_id)) {
            $q->from(array($this->table_name(), 'tran'))
                ->join(array('plugin_bookings_transactions_payments', 'pay'))
                ->on('tran.id', '=', 'pay.transaction_id')
                ->where('pay.transaction_id','=',$transaction_id)
                ->where('pay.deleted','=',DB::expr('0'));
        } else if ( ! is_null($booking_id)) {
            $q->from(array($this->table_name(), 'tran'))
                ->join(array('plugin_bookings_transactions_payments', 'pay'))
                ->on('tran.id', '=', 'pay.transaction_id')
                ->where('tran.booking_id', '=', $booking_id)
                ->on('pay.deleted', '=', DB::expr('0'));
        }
        $q->join(array(self::TYPE_TABLE,'type'),'LEFT')
            ->on('type.id','=','tran.type')
            ->join(array('plugin_bookings_transactions_payments_statuses','status'),'LEFT')
            ->on('pay.status','=','status.id');;
        $payments = $q->execute()->as_array();

        $refunds = DB::select('credit_usages.*', array('status.credit','pay_credit'), 'ctx.type')
            ->from(array('plugin_bookings_transactions_payments', 'refunds'))
                ->join(array('plugin_bookings_transactions_payments_journal', 'journals'), 'inner')
                    ->on('refunds.id', '=', 'journals.journaled_payment_id')
                ->join(array('plugin_bookings_transactions_payments', 'credit_usages'), 'inner')
                    ->on('credit_usages.id', '=', 'journals.payment_id')
                ->join(array('plugin_bookings_transactions_payments_statuses', 'status'), 'inner')
                    ->on('credit_usages.status','=','status.id')
                ->join(array('plugin_bookings_transactions', 'ctx'), 'inner')
                    ->on('refunds.transaction_id', '=', 'ctx.id');

        $refunds->where('refunds.deleted', '=', 0);

        if (!is_null($transaction_id)) {
            $refunds->and_where('refunds.transaction_id','=',$transaction_id)
                ->and_where('refunds.deleted', '=', 0);
        }

        $refunds = $refunds->execute()->as_array();
        //$payments = array_merge($payments, $refunds);
        $total_payed = 0 ;
        foreach ( $payments as $key=>$payment)
        {
            switch($payment['pay_credit'])
            {
                case 1:
                    $total_payed += $payment['amount'];
                    break;
                case 0:
                    $total_payed -= $payment['amount'];
                    break;
                case -1:
                    $total_payed -= $payment['amount'];
                    break;

            }
        }
        return $total_payed;
    }

    /**
     * Get the balance for contacts or family
     * @param null $contact_id
     * @param null $family_id
     * @return int
     */
    public function calculate_contact_balance($contact_id=NULL,$family_id=NULL)
    {
        $balance = 0 ; $credit = 0 ; $detail = '';
        if ( ! is_null($contact_id))
        {
            $transactions = self::get_contact_transactions($contact_id,NULL);
        }
        else if ( ! is_null($family_id))
        {
            $transactions = self::get_contact_transactions(NULL,$family_id);
        }

        $categories = array();
        if (isset($transactions))
        {
            foreach ($transactions as $transaction)
            {
                if ($transaction['type_publish'] == 0) {
                    continue;
                }
                if ( ! in_array($transaction['category'],$categories) ) {
                    $categories[] = $transaction['category'];
                }
                if ($transaction['credit'] == 0) {
                    if ($transaction['booking_id'] == 0) {
                        $credit -= $transaction['outstanding'];
                    }
                } else {
                    $balance += $transaction['outstanding'] + $transaction['pp_interest_outstanding'];
                }
            }
            asort($categories);
            $detail = '';
            foreach($categories as $category) {
                $cat_balance = 0;
                foreach ($transactions as $transaction) {
                    if ($transaction['category'] == $category) {
                        if ($transaction['credit'] == 1) {
                            if ($transaction['tran_type'] == 1) {
                                $cat_balance += $transaction['outstanding'];
                            } elseif($transaction['tran_type'] == 2) {
                                $cat_balance += $transaction['outstanding_todate'];
                            }
                        }
                    }
                }
                if ($cat_balance > 0) {
                    $detail .= $category . ' balance = €'.$cat_balance.'<br />';
                }
                else if ($cat_balance < 0) {
                    $detail .= $category . ' over payed = €'.$cat_balance.'<br />';
                }
            }
        }
        $balance = round($balance, 2);
        $credit = round($credit, 2);
        return array($balance,$credit,$detail);
    }

    /**
     * Use to check if the booking is of type tutorial
     * @param $booking_id
     * @return bool
     */
    public function is_booking_tutorial($booking_id)
    {
        $answer = DB::select('plugin_courses_categories.grinds_tutorial','plugin_courses_schedules.trainer_id')
            ->from('plugin_ib_educate_booking_schedule_has_label')
            ->join('plugin_courses_schedules')->on('plugin_ib_educate_booking_schedule_has_label.schedule_id', '=' ,'plugin_courses_schedules.id')
            ->join('plugin_courses_courses')->on('plugin_courses_schedules.course_id','=', 'plugin_courses_courses.id')
            ->join('plugin_courses_categories')->on('plugin_courses_courses.category_id','=','plugin_courses_categories.id')
            ->where('plugin_ib_educate_booking_schedule_has_label.booking_id','=',$booking_id)
            ->execute();
        if ($answer['grinds_tutorial']=='1')    return $answer['trainer_id'];
        else                                    return NULL;
    }

    /**
     * @param $booking_id
     * @return bool
     */
    public function booking_has_multiple_transaction($booking_id)
    {
        $multiple = 0;
        $count = DB::select(DB::expr('COUNT(*) AS `count`'))
            ->from(array('plugin_bookings_transactions','t'))
            ->join(array('plugin_bookings_transactions_types','t1'))
            ->on('t1.id','=','t.type')
            ->where('t.booking_id','=',$booking_id)
            ->where('t1.type','NOT LIKE','%Journal%')
            ->execute()
            ->as_array();
        if ($count[0]['count'] == 1)
        {
            $multiple = 1;
        }
        return $multiple;
    }

    public function booking_is_billed($booking_id) {
        $bill_payer = DB::select('bill_payer')
            ->from('plugin_ib_educate_bookings')
            ->where('booking_id', '=', $booking_id)
            ->execute()
            ->get('bill_payer');
        return $bill_payer != null;
    }

    public function bill_payer_full_name($booking_id) {
        $details = DB::select(array(DB::expr("CONCAT(c.first_name,' ',c.last_name)"),'full_name'))
            ->from(array('plugin_contacts3_contacts','c'))
            ->join(array('plugin_ib_educate_bookings','b'))
            ->on('c.id','=','b.bill_payer')
            ->where('b.booking_id','=',$booking_id)
            ->execute()
            ->current();
        return isset($details['full_name']) ? $details['full_name'] : '';
    }

                         /***           PUBLIC GET FUNCTIONS             ***/

    /**
     * Get the transactions types
     * @return mixed
     */
    public function get_transaction_types()
    {
        $types = DB::select('id','type','credit')
            ->from(self::TYPE_TABLE)
            ->where('publish','=',1)
            ->where('delete','=',0)
            ->execute()
            ->as_array();
        return $types;
    }

    public function get_id()
    {
        return $this->id;
    }

    /**
     * DEPRECIATED
     * Get the transaction id for a tutorial booking using the type 6 for Teacher journal 7 for tutorial booking
     *
     * @param $type
     * @param $id
     * @return mixed
     */
    public function get_transaction_id_by_type($type,$id)
    {
        $booking = DB::select('booking_id')
            ->from(self::TRANSACTION_TABLE)
            ->where('booking_id','=',$id)
            ->where('type','=',$type)
            ->execute();
        $type = ($type == 7) ? 8 : 7;
        $id = DB::select('id')
            ->from(self::TRANSACTION_TABLE)
            ->where('booking_id','=',$booking)
            ->where('type','=',$type)
            ->execute();
        return $id['id'];
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Kohana_Exception
     */
    public function get_type($id)
    {
        $type = ORM::factory('Kes_Transaction')->select('type')->where('id', '=', $id)->find()->as_array();
        return $type['type'];
    }

    public static function find_type_id($type)
    {
        $id = DB::select('id')->from(self::TYPE_TABLE)->where('type', '=', $type)->execute()->get('id');
        return $id;
    }

    /**
     * Get a transaction including type credit/Debit and balance
     * @param $id transaction
     * @param $booking_id
     * @return array
     * @throws \Kohana_Exception
     */
    public function get_transaction($id = null, $booking_id = null, $schedule_id = null, $payg_period = null, $types = null)
    {
        if (!is_null($id)) {
            //$t = ORM::factory('Kes_Transaction')->where('id', '=', $id)->find()->as_array();
            $tq = DB::select(
                'tx.*',
                DB::expr('GROUP_CONCAT(s.name) AS `schedule`'),
                DB::expr('GROUP_CONCAT(c.title) AS `course`'),
                DB::expr('GROUP_CONCAT(has.schedule_id) AS `schedule_id`'),
                'booking_has_card.card_id',
                'booking_has_card.recurring_payments_enabled',
                'contact_has_card.last_4'
            )
                ->from(array('plugin_bookings_transactions_has_schedule', 'has'))
                    ->join(array('plugin_bookings_transactions', 'tx'), 'inner')->on('has.transaction_id', '=', 'tx.id')
                    ->join(array('plugin_ib_educate_booking_has_schedules', 'has_sc'), 'left')
                        ->on('tx.booking_id', '=', 'has_sc.booking_id')
                        ->on('has.schedule_id', '=', 'has_sc.schedule_id')
                    ->join(array('plugin_courses_schedules', 's'), 'left')
                        ->on('has.schedule_id', '=', 's.id')
                    ->join(array('plugin_courses_courses', 'c'), 'left')
                        ->on('s.course_id', '=', 'c.id')
                    ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'left')
                        ->on('tx.booking_id', '=', 'bookings.booking_id')
                    ->join(array(Model_KES_Bookings::HAS_CARD_TABLE, 'booking_has_card'), 'left')
                        ->on('bookings.booking_id', '=', 'booking_has_card.booking_id')
                    ->join(array(Model_Contacts3::HAS_CARDS_TABLE, 'contact_has_card'), 'left')
                        ->on('booking_has_card.card_id', '=', 'contact_has_card.id')
                        ->on('contact_has_card.deleted', '=', DB::expr(0))
                ->where('tx.id', '=', $id)
                ->and_where('has.deleted', '=', 0)
                ->and_where('has_sc.deleted', '=', 0);


            $t = $tq->execute()->current();
        } else if (!is_null($booking_id)) {
            if ($schedule_id != null) {
                $tq = DB::select(
                    'tx.*',
                    DB::expr('GROUP_CONCAT(s.name) AS `schedule`'),
                    DB::expr('GROUP_CONCAT(c.title) AS `course`'),
                    DB::expr('GROUP_CONCAT(has.schedule_id) AS `schedule_id`')
                )
                    ->from(array('plugin_bookings_transactions_has_schedule', 'has'))
                        ->join(array('plugin_bookings_transactions', 'tx'), 'inner')->on('has.transaction_id', '=', 'tx.id')
                        ->join(array('plugin_ib_educate_booking_has_schedules', 'has_sc'), 'left')
                            ->on('tx.booking_id', '=', 'has_sc.booking_id')
                            ->on('has.schedule_id', '=', 'has_sc.schedule_id')
                        ->join(array('plugin_courses_schedules', 's'), 'left')
                            ->on('has.schedule_id', '=', 's.id')
                        ->join(array('plugin_courses_courses', 'c'), 'left')
                            ->on('s.course_id', '=', 'c.id')
                    ->where('has.schedule_id', '=', $schedule_id)
                    ->and_where('tx.booking_id', '=', $booking_id)
                    ->and_where('has.deleted', '=', 0)
                    ->and_where('has_sc.deleted', '=', 0);
                if ($payg_period) {
                    $tq->and_where('has.payg_period', '=', $payg_period);
                }
                if ($types) {
                    $tq->and_where('tx.type', 'in', $types);
                }
                $t = $tq->execute()->current();
                if (!$t['id']) {
                    $t = null;
                }
            } else {
                $t = ORM::factory('Kes_Transaction')->where('booking_id', '=', $booking_id)->find()->as_array();
            }
        } else {
            throw new Exception('Bug: id or booking_id must be set to a non null value');
        }
        if ($t && $t['id']) {
            $type = DB::select('type', 'credit')->from(self::TYPE_TABLE)->where('id', '=', $t['type'])->execute();
            $t['type_id'] = $t['type'];
            $t['type'] = $type[0]['type'];
//        $credit=DB::select('credit')->from(self::TYPE_TABLE)->where('id','=',$t['type'])->execute()->as_array();
            $t['credit'] = $type[0]['credit'];
            $t['payed'] = $this->calculate_outstanding_balance(NULL, $t['id']);
            $t['multiple'] = ORM::factory('Kes_Transaction')->booking_has_multiple_transaction($t['booking_id']);
            if ($t['credit'] == 1) {
                $t['outstanding'] = $t['total'] - $t['payed'];
            } else {
                $t['outstanding'] = $t['payed'];
            }
        }
        return $t;
    }

    public function get_schedule_transactions($booking_id, $schedule_id, $event_id = null, $types = array(1, 2))
    {
        $tq = DB::select('tx.*')
            ->from(array('plugin_bookings_transactions_has_schedule', 'has'))
            ->join(array('plugin_bookings_transactions', 'tx'), 'right')->on('has.transaction_id', '=', 'tx.id')
            ->where('has.schedule_id', '=', $schedule_id)
            ->and_where('tx.booking_id', '=', $booking_id);
        if ($event_id) {
            $tq->and_where('has.event_id', '=', $event_id);
        }
        $tq->and_where('tx.type', 'in', $types); // only booking types. no journals
        $transactions = $tq->execute()->as_array();
        foreach ($transactions as $i => $transaction){
            $type = DB::select('type', 'credit', 'publish')->from(self::TYPE_TABLE)->where('id', '=', $transaction['type'])->execute()->current();
            if ($type['publish'] == 0) {
                continue;
            }
            $transaction['type_id'] = $transaction['type'];
            $transaction['type'] = $type[0]['type'];
            $transaction['credit'] = $type[0]['credit'];
            $transaction['payed'] = $this->calculate_outstanding_balance(null, $transaction['id']);
            if ($transaction['credit'] == 1) {
                $pp_interest_outstanding = DB::select(
                    DB::expr("SUM(pp_installments.interest) as interest_outstanding")
                )
                    ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'))
                    ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'pp_installments'), 'inner')
                    ->on('pp.id', '=', 'pp_installments.payment_plan_id')
                    ->where('pp.transaction_id', '=', $transaction['id'])
                    ->and_where('pp_installments.deleted', '=', 0)
                    ->and_where('pp_installments.payment_id', 'is', null)
                    ->execute()
                    ->get('interest_outstanding');
                $transaction['outstanding'] = $transaction['total'] + $pp_interest_outstanding - $transaction['payed'];
            } else {
                $transaction['outstanding'] = $transaction['payed'];
            }
            $transactions[$i] = $transaction;
        }

        return $transactions;
    }

    public function get_course_transactions($booking_id, $course_id, $types = array(1, 2))
    {
        $tq = DB::select('tx.*')
            ->from(array('plugin_bookings_transactions_has_courses', 'has'))
            ->join(array('plugin_bookings_transactions', 'tx'), 'inner')->on('has.transaction_id', '=', 'tx.id')
            ->where('has.course_id', '=', $course_id)
            ->and_where('tx.booking_id', '=', $booking_id);
        $tq->and_where('tx.type', 'in', $types); // only booking types. no journals
        $transactions = $tq->execute()->as_array();

        foreach ($transactions as $i => $transaction){
            $type = DB::select('type', 'credit')->from(self::TYPE_TABLE)->where('id', '=', $transaction['type'])->execute();
            $transaction['type_id'] = $transaction['type'];
            $transaction['type'] = $type[0]['type'];
            $transaction['credit'] = $type[0]['credit'];
            $transaction['payed'] = $this->calculate_outstanding_balance(null, $transaction['id']);
            if ($transaction['credit'] == 1) {
                $transaction['outstanding'] = $transaction['total'] - $transaction['payed'];
            } else {
                $transaction['outstanding'] = $transaction['payed'];
            }
            $transactions[$i] = $transaction;
        }

        return $transactions;
    }

    /**
     * Used For the Plugin Bookings
     * @param $bookings
     * @return null
     */
    public function get_transactions($bookings){
        $booking_ids = array();
        $transactions = null;
        foreach($bookings as $booking){
            $booking_ids[] = $booking['booking_id'];
        }

        if(!empty($booking_ids)){
            $transactions = DB::select('tran.id', 'tran.booking_id', 'tran.amount', 'tran.fee', 'tran.total', array('tran.type','tran_type'), 'tran.created', 'tran.updated',
                'tran.deleted', 'type.credit', 'type.type')
                ->from(array($this->table_name(), 'tran'))
                ->join(array(self::TYPE_TABLE,'type'),'LEFT')
                ->on('type.id','=','tran.type')
                ->where('tran.booking_id', 'IN', $booking_ids)
                ->where('tran.deleted', '=', 0)
                ->group_by('tran.id', 'tran.booking_id', 'tran.amount', 'tran.fee', 'tran.total', 'tran.type', 'tran.created', 'tran.updated', 'tran.deleted')
                ->execute()
                ->as_array();
            foreach($transactions as $key=>$transaction)
            {
                $transaction['outstanding'] = $this->calculate_outstanding_balance($transaction['id']);
            }
            foreach($transactions as $key=>$transaction)
            {
                $transactions[$key]['status_label'] = ORM::factory('Kes_Transaction')->get_status_label($transaction['id']);
            }
        }
        return $transactions;
    }

    public function get_booking_transaction($booking_id)
    {
        $outstanding = NULL;
        $transactions = DB::select('tran.id', 'tran.booking_id', 'tran.amount', 'tran.fee', 'tran.total', array('tran.type','tran_type'), 'tran.created',
            'tran.updated', 'tran.deleted', 'type.credit', 'type.type','tran.contact_id','tran.family_id', array('type.publish', 'type_publish'))
            ->from(array(self::TRANSACTION_TABLE, 'tran'))
            ->join(array(self::TYPE_TABLE,'type'),'LEFT')->on('type.id','=','tran.type')
			->where('tran.booking_id', '=', $booking_id)
            ->where('tran.deleted', '=', 0)
            ->execute()
            ->as_array();
        $test = array_filter($transactions);
        $paying_family = $paying_contact = NULL ;
        if ( ! empty($test))
        {
            $outstanding = 0.0;
            foreach($transactions as $transaction)
            {
                if ($transaction['type_publish'] == 0) {
                    continue;
                }
				if ($transaction['credit'] == 1)
				{
                    $pp_interest_outstanding = DB::select(
                        DB::expr("SUM(pp_installments.interest) as interest_outstanding")
                    )
                        ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'))
                        ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'pp_installments'), 'inner')
                        ->on('pp.id', '=', 'pp_installments.payment_plan_id')
                        ->where('pp.transaction_id', '=', $transaction['id'])
                        ->and_where('pp_installments.deleted', '=', 0)
                        ->and_where('pp_installments.payment_id', 'is', null)
                        ->execute()
                        ->get('interest_outstanding');

					$outstanding += $transaction['total'] + $pp_interest_outstanding - $this->calculate_outstanding_balance(NULL,$transaction['id']);
				} else {
                    if ($transaction['tran_type'] == 4 && $transaction['type'] == 'Journal Cancel Booking') {
                        $outstanding -= $transaction['total'] - $this->calculate_outstanding_balance(NULL,$transaction['id']);
                    }
                }
                $paying_family = $transaction['family_id'];
                $paying_contact = $transaction['contact_id'];
            }
        }
        return array('outstanding'=>$outstanding,'paying_family'=>$paying_family,'paying_contact'=>$paying_contact);
    }

    /**
     * Retrieve the Family or Contact Transactions
     * @param null $contact_id
     * @param null $family_id
     * @return mixed
     */
    public static function get_contact_transactions($contact_id = null, $family_id = null, $booking_id = null)
    {
        $q = DB::select(
            'tran.id', 'tran.booking_id', 'tran.amount', 'tran.fee', 'tran.total', 'tran.discount', array('tran.type','tran_type'), 'tran.created',
            'tran.updated', 'tran.deleted', 't3.name', 'type.credit', 'type.type', array('type.publish', 'type_publish'),
            array('contact.family_id','booked_family'),array('tran.family_id','paying_family'),
            array('contact.id','booked_contact'),array('tran.contact_id','paying_contact'),
            array(DB::expr("CONCAT(`contact`.`first_name`+' '+`contact`.`last_name`)"), 'contact_name'),
            array('contact.first_name','first_name'),array('contact.last_name','last_name'),
            array('modified_by.id','modified_by_id'),
            array('modified_by.name','modified_by_name'),
            array('modified_by.surname','modified_by_surname'),
            array('modified_by.email','modified_by_email'),
            array('cat.category','category'),
            array('roles.role', 'creator_role'),
            'booking_has_card.card_id',
            'booking_has_card.recurring_payments_enabled',
            'contact_has_card.last_4'
        )
            ->from(array(self::TRANSACTION_TABLE, 'tran'))
            ->join(array('plugin_ib_educate_bookings','book'),'LEFT')
            ->on('tran.booking_id','=','book.booking_id')
            ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE,'t1'),'LEFT')
            ->on('tran.booking_id','=','t1.booking_id')
            ->join(array('plugin_courses_schedules_events','t2'),'LEFT')
            ->on('t1.period_id','=','t2.id')
            ->join(array('plugin_courses_schedules','t3'),'LEFT')
            ->on('t2.schedule_id','=','t3.id')
            ->join(array('plugin_courses_courses','c'),'LEFT')
            ->on('t3.course_id','=','c.id')
            ->join(array('plugin_courses_categories','cat'),'LEFT')
            ->on('c.category_id','=','cat.id')
            ->join(array(self::TYPE_TABLE,'type'),'LEFT')
            ->on('type.id','=','tran.type')
            ->join(array(Model_Users::MAIN_TABLE, 'users'), 'LEFT')
                ->on('book.created_by', '=', 'users.id')
            ->join(array(Model_Roles::MAIN_TABLE, 'roles'), 'LEFT')
                ->on('users.role_id', '=', 'roles.id')
            ->join(array(Model_KES_Bookings::HAS_CARD_TABLE, 'booking_has_card'), 'left')
                ->on('book.booking_id', '=', 'booking_has_card.booking_id')
            ->join(array(Model_Contacts3::HAS_CARDS_TABLE, 'contact_has_card'), 'left')
                ->on('booking_has_card.card_id', '=', 'contact_has_card.id')
                ->on('contact_has_card.deleted', '=', DB::expr(0));
        $q->join(array('plugin_contacts3_contacts', 'contact'), 'LEFT')
            ->on('book.contact_id', '=', 'contact.id');

        if ( ! is_null($contact_id))
        {
            $q->where('tran.contact_id',   '=', $contact_id);
        }
        if ( ! is_null($family_id))
        {
            $q->where('tran.family_id', '=', $family_id);
        }

        if (!is_null($booking_id)) {
            $q->and_where('tran.booking_id', '=', $booking_id);
        }

        $transactions = $q
            ->join(array('engine_users','modified_by'),'LEFT')
            ->on('tran.modified_by', '=', 'modified_by.id')
            ->where('tran.deleted', '=', 0)
            ->group_by('tran.id', 'tran.booking_id', 'tran.amount', 'tran.fee', 'tran.total', 'tran.type', 'tran.created', 'tran.updated', 'tran.deleted')
            ->order_by('tran.booking_id', 'DESC')
            ->order_by('tran.updated','DESC')
            ->execute()
            ->as_array();

        foreach($transactions as $key=>$transaction)
        {
            $pp_interest_total = DB::select(
                DB::expr("SUM(pp_installments.interest) as interest_total")
            )
                ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'))
                    ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'pp_installments'), 'inner')
                        ->on('pp.id', '=', 'pp_installments.payment_plan_id')
                ->where('pp.transaction_id', '=', $transaction['id'])
                ->and_where('pp_installments.deleted', '=', 0)
                ->execute()
                ->get('interest_total');

            $pp_interest_outstanding = DB::select(
                DB::expr("SUM(pp_installments.interest) as interest_outstanding")
            )
                ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'))
                    ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'pp_installments'), 'inner')
                        ->on('pp.id', '=', 'pp_installments.payment_plan_id')
                ->where('pp.transaction_id', '=', $transaction['id'])
                ->and_where('pp_installments.deleted', '=', 0)
                ->and_where('pp_installments.payment_id', 'is', null)
                ->execute()
                ->get('interest_outstanding');

            $transactions[$key]['pp_interest_total'] = $pp_interest_total;
            $transactions[$key]['pp_interest_outstanding'] = $pp_interest_outstanding;
            $schedules = DB::select('s.id','s.name')->from(array('plugin_bookings_transactions_has_schedule','s1'))
                ->join(array('plugin_courses_schedules','s'))->on('s.id','=','s1.schedule_id')
                ->where('s1.transaction_id','=',$transaction['id'])
                ->execute()->as_array();
            $transactions[$key]['schedule']='';
            foreach($schedules as $schedule)
            {
                $transactions[$key]['schedule'].=$schedule['name']. '<br>' ;
            }
            $payed = ORM::factory('Kes_Transaction')->calculate_outstanding_balance(NULL, $transaction['id']);
            if ($transactions[$key]['type'] == 'Journal Cancel Booking') {
                $transactions[$key]['outstanding'] = 0;
            } else {
                if ( ( ! is_null($contact_id) AND $transaction['paying_contact'] == $contact_id)
                    OR ( ! is_null($family_id) AND $transaction['paying_family'] == $family_id)
                    OR $booking_id != null) {
                    $transactions[$key]['outstanding'] = $transaction['total'] - $payed;
                } else {
                    $transactions[$key]['outstanding'] = 0;
                }
                if ($transaction['credit'] == 0)
                {
                    $transactions[$key]['outstanding'] = $payed;
                }
            }


            $balance_todate = 0;
            // PAYG transaction
            // this will probably be changed with kes-1725
            if ($transaction['tran_type'] == 2) {
                if (count($schedules)) {
                    $timeslots = DB::select(array(DB::expr('COUNT(*)'), 'count'))
                        ->from(array('plugin_bookings_transactions', 't1'))
                        ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 't3'))
                        ->on('t1.booking_id', '=', 't3.booking_id')
                        ->join(array('plugin_courses_schedules_events', 't4'))
                        ->on('t3.period_id', '=', 't4.id')
                        ->where('t1.id', '=', $transaction['id'])
                        ->where('t4.schedule_id', '=', $schedules[0]['id'])
                        ->where('t3.attending', '=', 1)
                        ->where('t4.datetime_start', '<', DB::expr('NOW()'))
                        ->execute()->as_array();
                    $balance_todate = $transaction['amount'] * $timeslots[0]['count'] - $payed;
                } else {
                    $balance_todate = $transaction['amount'];
                }
            }
            $transactions[$key]['outstanding_todate'] = $balance_todate ;
            $transactions[$key]['family'] = is_null($family_id)?FALSE:TRUE ;
            $transactions[$key]['multiple'] = ORM::factory('Kes_Transaction')->booking_has_multiple_transaction($transaction['booking_id']);
        }

        foreach($transactions as $key=>$transaction)
        {
            $transactions[$key]['status_label'] = ORM::factory('Kes_Transaction')->get_status_label($transaction);
            $transactions[$key]['discounts'] = self::get_transaction_discounts($transaction['id']);
            $transactions[$key]['schedule'] = substr($transactions[$key]['schedule'], 0, -4);
        }
        return $transactions;
    }

    public static function get_contact_outstanding_transactions($contact_id=NULL)
    {
        $sql = "SELECT t.id,t.booking_id, t4.type, c3.first_name, c3.last_name, (t.total -  COALESCE((SELECT SUM(t1.amount) FROM plugin_bookings_transactions_payments AS `t1` ".
            "JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id  WHERE t2.credit = 1 AND t1.transaction_id = t.id),0) + ".
            "COALESCE((SELECT SUM(t1.amount) FROM plugin_bookings_transactions_payments AS `t1` JOIN plugin_bookings_transactions_payments_statuses AS `t2` ".
            "ON t1.`status` = t2.id WHERE t2.credit = 0 AND t1.transaction_id = t.id),0))As `balance` FROM plugin_bookings_transactions `t` ".
            "INNER JOIN plugin_bookings_transactions_types t4 ON t4.id = t.type INNER JOIN plugin_ib_educate_bookings `b` ON t.booking_id = b.booking_id ".
            "INNER JOIN plugin_contacts3_contacts `c3` ON c3.id = b.contact_id WHERE t.contact_id = ".$contact_id." AND t4.type NOT LIKE '%Journal%' HAVING balance > 0";

        $transactions = DB::query(DATABASE::SELECT, $sql)->execute()->as_array();

        return $transactions;
    }

    public static function get_contact_payg_booking($contact_id=NULL,$booked=TRUE)
    {
        $q = DB::select('t.booking_id','t.id','t1.type')
            ->from(array(self::TRANSACTION_TABLE,'t'))
            ->join(array(self::TYPE_TABLE,'t1'))
            ->on('t1.id','=','t.type');
        if ($booked)
        {
            $q->where('t1.type','=','Booking - PAYG');
        }
        else
        {
            $hist = DB::select('t2.transaction_id')
                ->from(array('plugin_bookings_transactions_history','t2'))
                ->join(array(self::TYPE_TABLE,'t3'))
                ->on('t2.type','=','t3.id')
                ->where('t3.type','=','Booking - PAYG');
            $q->where('t1.type','=','Journal Cancel Booking')
                ->where('t.id','IN',$hist);
        }
            $bookings = $q->where('t.contact_id','=',$contact_id)
                ->execute()
            ->as_array();
        return $bookings;
    }

    public static function get_contact_cancelled_booking($contact_id=NULL)
    {
        $q = DB::select('t.booking_id','t.id','t1.type')
            ->from(array(self::TRANSACTION_TABLE,'t'))
            ->join(array(self::TYPE_TABLE,'t1'))
            ->on('t1.id','=','t.type');

        $hist = DB::select('t2.transaction_id')
            ->from(array('plugin_bookings_transactions_history','t2'))
            ->join(array(self::TYPE_TABLE,'t3'))
            ->on('t2.type','=','t3.id')
            ->where('t3.type','=','Booking - Pay Now');

        $q->where('t1.type','=','Journal Cancel Booking')
            ->where('t.id','IN',$hist);

        $bookings = $q->where('t.contact_id','=',$contact_id)
            ->execute()
            ->as_array();
        return $bookings;
    }

    public function get_transaction_history($transaction_id)
    {
        $q = DB::select(
                'hist.id',
                'hist.transaction_id','hist.booking_id','hist.total',//'t3.name',
                'hist.updated', 'hist.deleted', 'type.type',
                array('modified_by.name','modified_by_name'),
                array('modified_by.surname','modified_by_surname')
            )
            ->from(array('plugin_bookings_transactions_history','hist'))
            ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE,'t1'),'LEFT')
                ->on('hist.booking_id','=','t1.booking_id')
            ->join(array(self::TYPE_TABLE,'type'),'LEFT')
                ->on('type.id','=','hist.type')
            ->join(array('engine_users','modified_by'),'LEFT')
                ->on('hist.modified_by', '=', 'modified_by.id')
            ->order_by('hist.id','DESC')
            ->where('transaction_id','=',$transaction_id)
            ->distinct(TRUE);


        $transactions = $q->execute()->as_array();

        foreach($transactions as $key=>$transaction)
        {
            $schedules = DB::select('s.name')->from(array('plugin_bookings_transactions_has_schedule','s1'))
                ->join(array('plugin_courses_schedules','s'))->on('s.id','=','s1.schedule_id')
                ->where('s1.transaction_id','=',$transaction['transaction_id'])
                ->execute()->as_array();
            $transactions[$key]['schedule']='';
            foreach($schedules as $schedule)
            {
                $transactions[$key]['schedule'].=$schedule['name']. ' - ' ;
            }
            $transactions[$key]['schedule'] = substr($transactions[$key]['schedule'], 0, -3);
        }

        return $transactions;
    }

    public function get_credit_journal($contact_id = NULL)
    {
        $journal = array();
        $q = DB::select()
            ->from(self::TRANSACTION_TABLE)
            ->where('deleted','=',0)
            ->where('booking_id','=',0);
        if( ! is_null($contact_id))
        {
            $c = $q ;
            $contact = new Model_Contacts3($contact_id);
            $q->where('family_id','=',$contact->get_family_id());
            $family_credit= $q->execute()->as_array();
            $c->where('contact_id','=',$contact_id);
            $contact_journal= $c->execute()->as_array();
        }
        if ( ! empty($family_credit))
        {
            foreach($family_credit as $credit)
            {
                if (is_null($credit['contact_id']) OR $credit['contact_id'] == '')
                {
                    $credit['outstanding'] = ORM::factory('Kes_Transaction')->calculate_outstanding_balance(NULL,$credit['id']);
                    $journal['family'] = $credit;
                }
            }
        }
        if ( ! empty($contact_journal))
        {
            $contact_journal[0]['outstanding'] = ORM::factory('Kes_Transaction')->calculate_outstanding_balance(NULL,$contact_journal[0]['id']);
            $journal['contact'] = $contact_journal[0];
        }
        return $journal;
    }

    /**
     * Get the HTML for the balance label
     *
     * @static
     * @param int $customer_id
     * @return string HTML
     */
    public function get_contact_balance_label($customer_id = 0,$family_id = 0)
    {
        $span = '' ;

        if($customer_id == 0){
            $balances = $this->calculate_contact_balance(NULL,$family_id);
        }
        else
        {
            $balances = $this->calculate_contact_balance($customer_id,NULL);
        }

        $balance = $balances[0];
        $credit = $balances[1];

        /*
         * This Balance is displayed with the point of view of the IBIS-Company, i.e.:
         * 1. When Balance is POSITIVE ($balance > 0), i.e. The Company (IBIS) ows money to the Client
         *    - Display it with a RED Background and negative Value so it will mean: "Money Out of the Company"
         * 2. When Balance is NEGATIVE ($balance < 0), The Client ows money to the Company (IBIS)
         *    - Display it with GREEN background and negative Value, so it will mean: "Money FOR the Company"
         */
        if($credit < 0){
            $span .= '<span class="popinit label label-success" data-original-title="" rel="popover">Credit Available = '. ((-1)*$credit) .'</span>';
        }

        if($balance > 0){
            $span .= '<span class="popinit label label-warning" data-original-title="Balance detail" data-content="'.$balances[2].'" rel="popover">Balance = '. ((-1)*$balance) .'</span>';
        }
        elseif($balance < 0){
            $span .= '<span class="popinit label label-success" data-original-title="Balance detail" rel="popover">Balance = '. ((-1)*$balance) .'</span>';
        }
        else{ //balance is Zero
            $span .= '<span class="popinit label label-success" data-original-title="Balance detail" rel="popover">Balance = 0.00</span>';
        }
        return $span;
    }

    public function get_status_label($transaction=NULL)
    {
        $status_label = NULL ;
        if ( ! is_null($transaction))
        {
            $balance = $transaction['outstanding'];
            $journal = intval($transaction['credit']) === 0 ? TRUE : FALSE ;
            $booking = intval($transaction['booking_id']) !== 0 ? TRUE : FALSE;
            if ($journal AND $booking)
            {
                $label = 4;
            }
            else
            {
                if ($balance == 0)
                {
                    $label =  $journal ? 5 : 1 ;
                }
                else if ($balance < 0)
                {
                    $label = $journal ? 3 : 2 ;
                }
                else
                {
                    $label = $journal ? 2 : 3 ;
                }
            }
            switch ($label)
            {
                case 1:
                    $status_label = 'Completed';
                    break;
                case 2:
                    $status_label = 'Credit Available';
                    break;
                case 3:
                    $status_label = 'Outstanding';
                    break;
                case 4:
                    $status_label = 'Cancelled';
                    break;
                case 5:
                    $status_label = 'No Credit Available';
                    break;
            }
        }
        return $status_label;
    }

    public function get_next_due_date($booking_id=NULL)
    {
        $dates = DB::select('s.datetime_start')
            ->from(array('plugin_courses_schedules_events','s'))
            ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE,'b'))
            ->on('s.id','=','b.period_id')
            ->where('b.booking_id','=',$booking_id)
            ->where('s.delete','=',0)
            ->where('b.attending','=',1)
            ->where('s.datetime_start','>=',DB::expr('CURDATE()'))
            ->order_by('s.datetime_start')
            ->execute()->as_array();
        return isset($dates[0])?$dates[0]['datetime_start']:'N/A';
    }

                /***        SET FUNCTIONS ***/

    public function set_update_on_payment($transaction)
    {
        if($this->get_type($transaction) == 1)
        {
            $q = ORM::factory('Kes_Transaction',$transaction)->set('updated', date("Y-m-d H:i:s"))->save();
        }
        else
        {
            $transaction = ORM::factory('Kes_Transaction')->select()->where('id','=',$transaction)->find()->as_array();
            $due = $this->get_next_due_date($transaction['booking_id']);
            $q = ORM::factory('Kes_Transaction',$transaction)->set('updated', date("Y-m-d H:i:s"))->set('payment_due_date',$due)->save();
        }
        return $q;
    }

    public function set_journal_cancel($transaction)
    {
        $o = ORM::factory('Kes_Transaction')->select()->where('id','=',$transaction)->find()->as_array();
        $this->save_history($transaction,$o);
        $q = ORM::factory('Kes_Transaction',$transaction)
            ->set('updated', date("Y-m-d H:i:s"))
            ->set('type',4)
            ->save();
        return $q;
    }

    public static function cancel_transaction($data)
    {
        $results = array('status'=>'','message'=>'');
        $transac = ORM::factory('Kes_Transaction')->get_transaction($data['transaction_id'],NULL,NULL);

        $update_transaction = ORM::factory('Kes_Transaction')->set_journal_cancel($data['transaction_id']);
        $update_transaction->get_id();
        if ($update_transaction)
        {
            $results['status'] = 'success';
            $results['message'] .= 'Transaction #'.$data['transaction_id'].' was updated successfully. ';
            $user = Auth::instance()->get_user();
            $activity = new Model_Activity();
            $activity
                ->set_item_type('transaction')
                ->set_action('cancel')
                ->set_item_id($data['transaction_id'])
                ->set_user_id($user['id'])
                ->set_scope_id($update_transaction->contact_id)
                ->save();
        }
        else
        {
            $results['status'] = 'error';
            $results['message'] .= 'Error Updating the transaction. ';
        }
        if ($data['credit_payment'] === 'yes')
        {

            if ($data['credit_destination'] == 'family')
            {
                $credit_to_family_id = null;
                $contact = new Model_Contacts3($data['contact_id']) ;
                if (@$data['credit_to_family_id']) {
                    $credit_to_family_id = $data['credit_to_family_id'];
                } else {
                    $credit_to_family_id = $contact->get_family_id();
                }
                $credit_journal     = ORM::factory('Kes_Transaction')->create_credit_journal($data['transaction_id'],$credit_to_family_id,NULL,$data['credit_amount']);
            }
            else
            {
                $credit_to_contact_id = null;
                if ($data['credit_to_contact_id']) {
                    $credit_to_contact_id = $data['credit_to_contact_id'];
                } else {
                    $credit_to_contact_id = $data['contact_id'];
                }
                $credit_journal     = ORM::factory('Kes_Transaction')->create_credit_journal($data['transaction_id'],NULL,$credit_to_contact_id,$data['credit_amount']);
            }
            $note = '€'.$data['credit_amount'].'. Cancel booking#'.$data['booking_id'];

            $remove_credit    = array('transaction_id'=>$data['transaction_id'],'type'=>'Transfer', 'amount'=>$data['credit_amount'], 'status'=>6, 'note'=>'Transfer to credit available '.$note);
            $add_credit    = array('transaction_id'=>$credit_journal, 'type'=>'Transfer', 'amount'=>$data['credit_amount'], 'status'=>5, 'note'=>$note.' From contact#'.$data['contact_id']);
            $remove = ORM::factory('Kes_Payment')->save_payment($remove_credit);
            $add = ORM::factory('Kes_Payment')->save_payment($add_credit);
            if ($credit_journal AND $remove AND $add)
            {
                $results['credit_journal'] = $credit_journal;
                $results['status'] = 'success';
                $results['message'] .= 'Credit was Added to transaction #' . $credit_journal .'. ';
            }
            else
            {
                $results['status'] = 'error';
                $results['message'] .= 'Error adding the credit. ';
            }
        }

        Model_Automations::run_triggers(Model_Bookings_Transactiondeletetrigger::NAME, array('transaction_id' => $data['transaction_id']));

        return $results;
    }

    public static function get_transaction_discounts($transaction_id)
    {
        $discountsq = DB::select(
            'd.*',
            'dd.title'
        )
            ->from(array(self::TRANSACTION_TABLE, 'tx'))
                ->join(array('plugin_bookings_transactions_has_schedule', 'hs'), 'inner')
                    ->on('tx.id', '=', 'hs.transaction_id')
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'b'), 'inner')
                    ->on('tx.booking_id', '=', 'b.booking_id')
                ->join(array(Model_KES_Bookings::DISCOUNTS, 'd'), 'inner')
                    ->on('b.booking_id', '=', 'd.booking_id')
                ->join(array(Model_KES_Discount::DISCOUNTS_TABLE, 'dd'), 'inner')
                    ->on('d.discount_id', '=', 'dd.id')
            ->where('tx.id', '=', $transaction_id)
            ->and_where('d.status', '<>', 'ignored_discount')
            ->and_where('hs.deleted', '=', 0)
            ->and_where_open()
                ->or_where('hs.schedule_id', '=', DB::expr('d.schedule_id'))
                ->or_where('d.schedule_id', 'is', null)
            ->and_where_close();
        $discounts = $discountsq->execute()->as_array();
        return $discounts;
    }

    public static function search($params = array())
    {
        $selectq = DB::select('tx.*', 'type.type', DB::expr('GROUP_CONCAT(schedules.name) as schedules'), DB::expr('GROUP_CONCAT(courses.title) as courses'))
            ->from(array(self::TRANSACTION_TABLE, 'tx'))
                ->join(array(self::TYPE_TABLE, 'type'), 'LEFT')->on('type.id','=','tx.type')
                ->join(array(self::TABLE_HAS_SCHEDULES, 'has_schedules'), 'left')->on('tx.id', '=', 'has_schedules.transaction_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->join(array(self::TABLE_HAS_COURSES, 'has_courses'), 'left')->on('tx.id', '=', 'has_courses.transaction_id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')->on('has_courses.course_id', '=', 'courses.id')
            ->where('tx.deleted', '=', 0);

        if (@$params['type']) {
            $selectq->and_where('tx.type', 'in', $params['type']);
        }

        if (@$params['booking_id']) {
            $selectq->and_where('tx.booking_id', '=', $params['booking_id']);
        }

        if (@$params['id']) {
            $selectq->and_where('tx.id', (is_array($params['id']) ? 'in' : '='), $params['id']);
        }

        $selectq->group_by('tx.id');
        $transactions = $selectq->execute()->as_array();

        return $transactions;
    }
}