<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 07/10/2014
 * Time: 09:21
 */
final class Model_KES_Bookings extends Model_Ideabubble
{
    /*** CONSTANTS ***/
    CONST BOOKING_TABLE             = 'plugin_ib_educate_bookings';
    CONST BOOKING_ITEMS_TABLE       = 'plugin_ib_educate_booking_items';
    CONST BOOKING_ROLLCALL_TABLE    = 'plugin_ib_educate_bookings_rollcall';
    CONST BOOKING_STATUS_TABLE      = 'plugin_ib_educate_bookings_status';
    CONST CONTACTS_TABLE            = Model_Contacts3::CONTACTS_TABLE;
    CONST TRANSACTIONS_TABLE        = 'plugin_ib_educate_transactions';
    CONST PAYMENT_METHOD_TABLE      = 'plugin_ib_educate_payment_methods';
    CONST PAYMENTS_TABLE            = 'plugin_ib_educate_payments';
    CONST BOOKING_LABELS            = 'plugin_ib_educate_bookings_labels';
    CONST BOOKING_SCHEDULE_LABELS   = 'plugin_ib_educate_booking_schedule_has_label';
    CONST BOOKING_SCHEDULES         = 'plugin_ib_educate_booking_has_schedules';
    const BOOKING_COURSES           = 'plugin_ib_educate_bookings_has_courses';
    const BOOKING_APPLICATIONS      = 'plugin_ib_educate_bookings_has_applications';
    CONST IGNORED_DISCOUNTS         = 'plugin_ib_educate_bookings_ignored_discounts';
    CONST DISCOUNTS                 = 'plugin_ib_educate_bookings_discounts';
    CONST BOOKING_LINKED_CONTACTS   = 'plugin_ib_educate_bookings_has_linked_contacts';
    const STUDENT_BOOKING_AUTHS     = 'plugin_bookings_student_auth_codes';
    const HAS_CARD_TABLE            = 'plugin_ib_educate_bookings_has_card';
    const DELEGATES_TABLE           = 'plugin_ib_educate_bookings_has_delegates';
    CONST ENQUIRY                   = 1;
    CONST CONFIRMED                 = 2;
    CONST CANCELLED                 = 3;
    CONST INPROGRESS                = 4;
    CONST COMPLETED                 = 5;
    CONST BOOKING_CART              = 'booking_cart';

    /*** PRIVATE MEMBER DATA ***/
    private $booking_id             = NULL;
    private $contact_id             = NULL;
    private $booking_items          = array();
    private $booking_status         = 1;
    private $created_date           = '';
    private $modified_date          = '';
    private $publish                = 1;
    private $delete                 = 0;
    private $amount                 = NULL;
    private $modified_by            = NULL;
    private $created_by             = NULL;
    private $schedule_id            = NULL;
    private $schedule_ids           = [];
    private $coupon_code            = NULL;
	private $custom_discount        = 0;
	private $discount_memo          = '';
    private $bill_payer             = '';
    private $amendable              = 0;
    private $payg_booking_fee       = 0;
    private $cc_booking_fee         = 0;
    private $sms_booking_fee        = 0;
    private $payment_method         = 'cc';
    private $courses                = array();
    private $application            = null;
    private $student                = null;
    private $delegate_ids           = array();
    private $billing_address_id     = null;

    /*** PUBLIC MEMBER DATA ***/
    public $contact_details         = array();
    public $booking_cost            = 0;
    public $payg_cost               = 0;
    public $additional_booking_data = '';
    public $interview_status        = null;
    public $application_status      = null;
    public $extra_data = null;
    public $invoice_details = '';
    public $how_did_you_hear = 0;

    /*** PUBLIC STATIC DATA ***/
    public static $MAX_DISCOUNTS_ALLOWED = 15;

    /*** PUBLIC FUNCTIONS ***/

    public static $payment_types = array(1 => 'Pre-Pay', 2 => 'PAYG');

    public function __construct($id = NULL)
    {
        if(!is_null($id) AND is_numeric($id))
        {
            $this->set_booking_id($id);
            $this->get(TRUE);
        }
    }

    /**
     * Dynamically set member data.
     * @param $data
     */
    protected $_data = null;
    public function set($data)
    {
        $this->_data = $data;
        foreach ($data AS $key => $value)
        {
            if (property_exists($this, $key))
            {
                $this->{$key} = $value;
            }
        }

        if (is_string($this->booking_items))
        {
            $this->booking_items = json_decode($this->booking_items, TRUE);
        }

        if (is_string($this->additional_booking_data))
        {
            $this->additional_booking_data = json_decode($this->additional_booking_data, TRUE);
        }

        return $this;
    }

    /**
     * Return data from database for this booking.
     * @param $autoload
     * @return array
     */
    public function get($autoload = FALSE)
    {
        $data = $this->get_booking_details();

        if($autoload)
        {
            $this->set($data);
            $this->get_contact_details();
        }

        return $data;
    }

	/**
	 * Set member data for a specified column
	 * @param	$column	string	the name of the column you are changing
	 * @param	$value	mixed	the new column value
	 * @return	$this
	 */
	public function set_column($column, $value)
	{
		$this->{$column} = $value;
		return $this;
	}

	/**
     * @param null $booking_id
     * @return $this
     */
    public function set_booking_id($booking_id = NULL)
    {
        $this->booking_id = is_numeric($booking_id) ? intval($booking_id) : $this->booking_id;
        return $this;
    }

    /**
     * @param null $contact_id
     * @return $this
     */
    public function set_contact_id($contact_id = NULL)
    {
        $this->contact_id = is_numeric($contact_id) ? intval($contact_id) : $this->contact_id;
        return $this;
    }

//    public function set_booking_amount($amount = NULL)
//    {
//        $this->amount = is_numeric($amount) ? floatval($amount) : $this->amount;
//        return $this;
//    }

    /**
     * @param array $booking_items
     * @return $this
     */
    public function set_booking_items($booking_items = array())
    {
        if(is_string($booking_items))
        {
            $booking_items = json_decode($booking_items,TRUE);
        }

        $this->booking_items = is_array($booking_items) ? $booking_items : $this->booking_items;

        return $this;
    }

	public function get_column($column)
	{
		return $this->{$column};
	}

    public function get_booking_status()
    {
        return $this->booking_status;
    }

    public function get_delegate_contacts ()
    {
        $delegates = [];
        foreach($this->delegate_ids as $delegate_id) {
            $delegates[] = new Model_Contacts3($delegate_id);
        }
        return $delegates;
    }
    public static function fix_period_bookings()
    {
        $applications =DB::select('*')
            ->from(self::BOOKING_APPLICATIONS)
            ->where('data', 'like', '%has_period%')
            ->execute()
            ->as_array();
        foreach ($applications as $application) {
            self::fix_period_booking_items($application);
        }
    }

    public static function fix_period_booking_items($application)
    {
        if (is_numeric($application)) {
            $application = DB::select('*')
                ->from(self::BOOKING_APPLICATIONS)
                ->where('booking_id', '=', $application)
                ->execute()
                ->current();
        }
        $booking_id = $application['booking_id'];

        if ($application) {
            if ($application['data'] != '') {
                $application['data'] = json_decode($application['data'], true);
                if (@$application['data']['has_period']) {
                    $schedules = DB::select('*')
                        ->from(self::BOOKING_SCHEDULES)
                        ->where('booking_id', '=', $booking_id)
                        ->execute()
                        ->as_array();
                    $schedule_ids = array();
                    foreach ($schedules as $schedule) {
                        $schedule_ids[] = $schedule['schedule_id'];
                    }

                    if ($schedule_ids) {
                        $periods = $application['data']['has_period'];
                        $timeslotsq = DB::select('*')
                            ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                            ->where('schedule_id', 'in', $schedule_ids)
                            ->and_where('delete', '=', 0)
                            ->order_by('datetime_start', 'asc');
                        if (count($periods) > 0) {
                            $timeslotsq->and_where(DB::expr("CONCAT_WS(',', DATE_FORMAT(datetime_start, '%a %H:%i'), trainer_id)"),
                                'in', $periods);
                        }
                        $timeslots = $timeslotsq->execute()->as_array();
                        $timeslot_ids = array();
                        foreach ($timeslots as $timeslot) {
                            $timeslot_ids[] = $timeslot['id'];
                        }
                        $deleteq = DB::update(self::BOOKING_ITEMS_TABLE)
                            ->set(array('delete' => 1))
                            ->where('booking_id', '=', $booking_id);
                        if ($timeslot_ids) {
                            $deleteq->and_where('period_id', 'not in', $timeslot_ids);
                        }
                        $deleteq->execute();

                    }
                }
            }
        }
    }

    public static function send_booking_schedule_start_reminders()
    {
        $reminder_intervals = Settings::instance()->get('bookings_schedule_start_reminder_days_before');
        $reminder_intervals = explode('/', $reminder_intervals);
        $mm = new Model_Messaging();
        $date_format = Settings::instance()->get('date_format');
        if ($date_format == '') {
            $date_format = 'd-m-Y H:i';
        }

        foreach ($reminder_intervals as $reminder_interval) {
            if ($reminder_interval > 0) {
                $timeslotsq = DB::select(
                    'bookings.booking_id',
                    'students.first_name',
                    'students.last_name',
                    array('students.id', 'student_id'),
                    array('emails.value', 'email'),
                    DB::expr("MIN(timeslots.datetime_start) as starts"),
                    array('schedules.name', 'schedule')
                )
                    ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                        ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                            ->on('bookings.booking_id', '=', 'items.booking_id')
                        ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                            ->on('items.period_id', '=', 'timeslots.id')
                        ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                            ->on('timeslots.schedule_id', '=', 'schedules.id')
                        ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                            ->on('bookings.contact_id', '=', 'students.id')
                        ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'inner')
                            ->on('students.notifications_group_id', '=', 'emails.group_id')
                            ->on('emails.notification_id', '=', DB::expr(1))
                    ->where('bookings.booking_status', '<>', 3)
                    ->and_where('items.booking_status', '<>', 3)
                    ->and_where('bookings.delete', '=', 0)
                    ->and_where('items.delete', '=', 0)
                    ->having('starts', '>=', DB::expr("date_add(curdate(), interval $reminder_interval day)"))
                    ->having('starts', '<', DB::expr("date_add(curdate(), interval " . ($reminder_interval + 1) . " day)"))
                    ->group_by('bookings.booking_id')
                    ->group_by('timeslots.schedule_id');

                $timeslots = $timeslotsq->execute()->as_array();

                foreach ($timeslots as $timeslot) {
                    $timeslot['starts'] = date($date_format, strtotime($timeslot['starts']));
                    $mm->send_template(
                        'booking-schedule-start-reminder',
                        null,
                        null,
                        array(array('target_type' => 'CMS_CONTACT3', 'target' => $timeslot['student_id'])),
                        array(
                            'starts' => $timeslot['starts'],
                            'name' => $timeslot['first_name'] . ' ' . $timeslot['last_name'],
                            'schedule' => $timeslot['schedule'],
                            'booking_id' => $timeslot['booking_id']
                        )
                    );
                }
            }
        }
    }

    public function get_booking_amount()
    {
        return $this->amount;
    }

    public function get_payment_method() {
        return $this->payment_method;
    }

    public static function process_auto_recurring_payments()
    {
        self::process_auto_recurring_subscriptions();
        self::process_auto_recurring_paymentplans();
    }

    public static function process_auto_recurring_subscriptions()
    {
        $today = date::today();
        $timeslots = DB::select(
            'items.*',
            array('items.period_id', 'timeslot_id'),
            'bookings.contact_id',
            'timeslots.schedule_id',
            'timeslots.datetime_start',
            'schedules.fee_amount',
            'schedules.payg_period',
            array('bookings.created_date', 'booking_created_date')
        )
            ->from(array(self::BOOKING_ITEMS_TABLE, 'items'))
                ->join(array(self::BOOKING_TABLE, 'bookings'), 'inner')
                    ->on('items.booking_id', '=', 'bookings.booking_id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('items.period_id', '=', 'timeslots.id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('timeslots.schedule_id', '=', 'schedules.id')
            ->where('items.delete', '=', 0)
            ->and_where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', '<>', 3)
            ->and_where('items.booking_status', '<>', 3)
            ->and_where('timeslots.datetime_start', '>=', date('Y-m-01', strtotime($today)))
            //->and_where('timeslots.datetime_start', '<', date('Y-m-t 23:59:59', strtotime($today)))
            //->and_where('timeslots.datetime_start', '>=', $today)
            ->and_where('timeslots.datetime_start', '<', DB::expr("DATE_ADD('$today', INTERVAL 1 DAY)"))
            ->and_where('schedules.booking_type', '=', 'Subscription')
            ->order_by('timeslots.datetime_start', 'desc')
            ->group_by('items.booking_id')
            ->execute()
            ->as_array();

        foreach ($timeslots as $timeslot) {
            $mtx = new Model_Kes_Transaction();
            if ($timeslot['payg_period'] == 'month') {
                $payg_period = date('Y-m', strtotime($timeslot['datetime_start']));

                $new_charge_date = date::same_day_of_month($timeslot['booking_created_date'], $today);
                if (strtotime($new_charge_date) > strtotime($today)) {
                    continue;
                }


                $eTransaction = $mtx->get_transaction(
                    null,
                    $timeslot['booking_id'],
                    $timeslot['schedule_id'],
                    $payg_period,
                    array(2)
                );
                if (!$eTransaction) {
                    $transaction = new Model_Kes_Transaction();
                    $transaction->create_transaction(
                        array(
                            'booking_id' => $timeslot['booking_id'],
                            'amount' => $timeslot['fee_amount'],
                            'total' => $timeslot['fee_amount'],
                            'type' => 2,
                            'discount' => 0,
                            'schedule' => array(
                                array(
                                    'schedule_id' => $timeslot['schedule_id'],
                                    'event_id' => $timeslot['timeslot_id'],
                                    'payg_period' => $payg_period
                                )
                            )
                        ),
                        $timeslot['contact_id']
                    );
                }
            }
        }

        $max_fails = Settings::instance()->get('payments_recurring_payments_max_attempt');
        $payments_to_process = DB::select(
            ['transactions.id', 'transaction_id'],
            'transactions.total',
            'cards.card_id',
            'gw.customer_id',
            'gw.paymentgw',
            'bookings.booking_id',
            'bookings.contact_id',
            'bookings.booking_id',
            'contacts.first_name',
            'contacts.last_name',
            'failed_auto_payment_attempts',
            DB::expr("SUM(IFNULL(payments.amount, 0)) as paid"),
            DB::expr("SUM(IFNULL(payments.amount, 0)) < transactions.total as process")
        )
            ->from([self::BOOKING_TABLE, 'bookings'])
            ->join([self::HAS_CARD_TABLE, 'has_cards'], 'inner')->on('bookings.booking_id', '=', 'has_cards.booking_id')
            ->join([Model_Contacts3::HAS_CARDS_TABLE, 'cards'], 'inner')->on('has_cards.card_id', '=', 'cards.id')
            ->join([Model_Contacts3::PAYMENTGW_TABLE, 'gw'], 'inner')->on('cards.has_paymentgw_id', '=', 'gw.id')
            ->join([Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'], 'inner')->on('bookings.booking_id', '=', 'transactions.booking_id')
            ->join([Model_Kes_Transaction::TYPE_TABLE, 'transaction_types'], 'inner')->on('transactions.type', '=', 'transaction_types.id')
            ->join([Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'paymentplans'], 'left')->on('transactions.id', '=', 'paymentplans.transaction_id')
            ->join([Model_Kes_Payment::PAYMENT_TABLE, 'payments'], 'left')->on('transactions.id', '=', 'payments.transaction_id')
            ->join([Model_Contacts3::CONTACTS_TABLE, 'contacts'], 'inner')->on('bookings.contact_id', '=', 'contacts.id')
            ->where('bookings.delete', '=', 0)
            ->and_where('transactions.deleted', '=', 0)
            ->and_where('has_cards.recurring_payments_enabled', '=', 1)
            ->and_where('paymentplans.id', 'is', null)
            ->and_where('transactions.failed_auto_payment_attempts', '<', $max_fails)
            ->and_where('transaction_types.credit', '=', 1)
            ->having('process', '=', 1)
            ->group_by('transaction_id')
            ->execute()
            ->as_array();
        $realvault = new Model_Realvault();
        $mm = new Model_Messaging();
        foreach ($payments_to_process as $payment) {
            $payment['amount'] = $payment['total'] - $payment['paid'];
            if ($payment['amount'] == 0) {
                continue;
            }
            if ($payment['paymentgw'] == 'realex') {
                $result = $realvault->charge_card(
                    $payment['customer_id'],
                    $payment['card_id'],
                    (Kohana::$environment != Kohana::PRODUCTION ? Kohana::$environment . '_' : '') . $payment['transaction_id'],
                    $payment['amount'],
                    'EUR',
                    null,
                    ['type' => 'variable', 'sequence' => 'subsequent']
                );
                $recipients = [['target_type' => 'CMS_CONTACT3', 'target' => $payment['contact_id']]];
                $parameters = [
                    'booking_id' => $payment['booking_id'],
                    'transaction_id' => $payment['transaction_id'],
                    'amount' => $payment['amount'],
                    'name' => $payment['first_name'] . ' ' . $payment['last_name'],
                ];
                $parameters['error'] = (string)$result->message;
                if ((string)$result->result != '00') {
                    DB::update(Model_Kes_Transaction::TRANSACTION_TABLE)
                        ->set(['failed_auto_payment_attempts' => $payment['failed_auto_payment_attempts'] + 1])
                        ->where('id', '=', $payment['transaction_id'])
                        ->execute();
                    $mm->send_template('recurring-payment-failed-customer', null, null, $recipients, $parameters);
                    $mm->send_template('recurring-payment-failed-admin', null, null, array(), $parameters);
                } else {
                    $payment_m = new Model_Kes_Payment();
                    $payment_m->save_payment(
                        [
                            'transaction_id' => $payment['transaction_id'],
                            'amount' => $payment['amount'],
                            'type' => 'card',
                            'status' => 2,
                            'created' => date::now(),
                            'updated' => date::now(),
                            'note' => 'Automatic cron payment'
                        ]
                    );
                    $mm->send_template('recurring-payment-succeded-customer', null, null, $recipients, $parameters);
                    $mm->send_template('recurring-payment-succeded-admin', null, null, array(), $parameters);
                }
            }
        }
    }

    public static function process_auto_recurring_paymentplans()
    {
        $today = date::today();
        $max_fails = Settings::instance()->get('payments_recurring_payments_max_attempt');
        $payments_to_process = DB::select(
            'installments.*',
            'paymentplans.transaction_id',
            'cards.card_id',
            'gw.customer_id',
            'gw.paymentgw',
            'bookings.booking_id',
            'bookings.contact_id',
            'bookings.booking_id',
            'contacts.first_name',
            'contacts.last_name'
        )
            ->from([self::BOOKING_TABLE, 'bookings'])
                ->join([self::HAS_CARD_TABLE, 'has_cards'], 'inner')->on('bookings.booking_id', '=', 'has_cards.booking_id')
                ->join([Model_Contacts3::HAS_CARDS_TABLE, 'cards'], 'inner')->on('has_cards.card_id', '=', 'cards.id')
                ->join([Model_Contacts3::PAYMENTGW_TABLE, 'gw'], 'inner')->on('cards.has_paymentgw_id', '=', 'gw.id')
                ->join([Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'], 'inner')->on('bookings.booking_id', '=', 'transactions.booking_id')
                ->join([Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'paymentplans'], 'inner')->on('transactions.id', '=', 'paymentplans.transaction_id')
                ->join([Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'installments'], 'inner')->on('paymentplans.id', '=', 'installments.payment_plan_id')
                ->join([Model_Contacts3::CONTACTS_TABLE, 'contacts'], 'inner')->on('bookings.contact_id', '=', 'contacts.id')
            ->where('bookings.delete', '=', 0)
            ->and_where('transactions.deleted', '=', 0)
            ->and_where('paymentplans.deleted', '=', 0)
            ->and_where('installments.deleted', '=', 0)
            ->and_where('installments.payment_id', 'is', null)
            ->and_where('installments.due_date', '<=', $today)
            ->and_where('has_cards.recurring_payments_enabled', '=', 1)
            ->and_where('installments.failed_auto_payment_attempts', '<', $max_fails)
            ->execute()
            ->as_array();
        $realvault = new Model_Realvault();
        $mm = new Model_Messaging();
        foreach ($payments_to_process as $payment) {
            if ($payment['paymentgw'] == 'realex') {
                $result = $realvault->charge_card(
                    $payment['customer_id'],
                    $payment['card_id'],
                    (Kohana::$environment != Kohana::PRODUCTION ? Kohana::$environment . '_' : '') . $payment['transaction_id'] . '-' . $payment['payment_plan_id'] . '-' . $payment['id'],
                    $payment['total'],
                    'EUR',
                    null,
                    ['type' => 'variable', 'sequence' => 'subsequent']
                );
                $recipients = [['target_type' => 'CMS_CONTACT3', 'target' => $payment['contact_id']]];
                $parameters = [
                    'booking_id' => $payment['booking_id'],
                    'transaction_id' => $payment['transaction_id'],
                    'amount' => $payment['amount'],
                    'name' => $payment['first_name'] . ' ' . $payment['last_name'],
                ];
                $parameters['error'] = (string)$result->message;
                if ((string)$result->result != '00') {
                    DB::update(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE)
                        ->set(['failed_auto_payment_attempts' => $payment['failed_auto_payment_attempts'] + 1])
                        ->where('id', '=', $payment['id'])
                        ->execute();
                    $mm->send_template('recurring-payment-failed-customer', null, null, $recipients, $parameters);
                    $mm->send_template('recurring-payment-failed-admin', null, null, array(), $parameters);
                } else {
                    $payment_m = new Model_Kes_Payment();
                    $payment_m->save_payment(
                        [
                            'transaction_id' => $payment['transaction_id'],
                            'amount' => $payment['amount'],
                            'payment_plan_has_payment_id' => $payment['id'],
                            'type' => 'card',
                            'status' => 2,
                            'created' => date::now(),
                            'updated' => date::now(),
                            'note' => 'Automatic cron payment'
                        ]
                    );
                    $mm->send_template('recurring-payment-succeded-customer', null, null, $recipients, $parameters);
                    $mm->send_template('recurring-payment-succeded-admin', null, null, array(), $parameters);
                }
            }
        }
    }

    public static function send_payment_plan_reminders()
    {
        $interval_days = Settings::instance()->get('bookings_payment_plan_reminder_days_before');
        if ($interval_days > 0) {
            $select = DB::select('tx.contact_id', 'tx.booking_id', 'ppp.*')
                ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'tx'))
                    ->join(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'), 'inner')
                        ->on('tx.id', '=', 'pp.transaction_id')
                    ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'ppp'), 'inner')
                        ->on('pp.id', '=', 'ppp.payment_plan_id')
                    ->join(array(self::BOOKING_TABLE, 'bookings'), 'inner')
                        ->on('tx.booking_id', '=', 'bookings.booking_id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                        ->on('bookings.contact_id', '=', 'students.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'payers'), 'left')
                        ->on('tx.contact_id', '=', 'payers.id')
                ->where('tx.deleted', '=', 0)
                ->and_where('pp.deleted', '=', 0)
                ->and_where('ppp.deleted', '=', 0)
                ->and_where('ppp.due_date', '=', DB::expr("DATE_ADD(CURDATE(), INTERVAL $interval_days DAY)"))
                ->and_where('ppp.payment_id', 'is', null);

            $plans = $select->execute()->as_array();
            foreach ($plans as $plan) {
                self::send_payment_plan_reminder($plan);
            }
        }
    }

    public static function send_payment_plan_reminder($ppp_id)
    {
        $ppp = null;
        if (is_numeric($ppp_id)) {
            $ppp = DB::select('tx.contact_id', 'tx.booking_id', 'ppp.*')
                ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'tx'))
                    ->join(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'), 'inner')
                        ->on('tx.id', '=', 'pp.transaction_id')
                    ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'ppp'), 'inner')
                        ->on('pp.id', '=', 'ppp.payment_plan_id')
                    ->join(array(self::BOOKING_TABLE, 'bookings'), 'inner')
                        ->on('tx.booking_id', '=', 'bookings.booking_id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                        ->on('bookings.contact_id', '=', 'students.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'payers'), 'left')
                        ->on('tx.contact_id', '=', 'payers.id')
                ->where('ppp.id', '=', $ppp_id)
                ->execute()
                ->current();
        } else if (is_array($ppp_id)) {
            $ppp = $ppp_id;
        }


        if ($ppp) {
            $mm = new Model_Messaging();
            $params = array();
            $params['duedate'] = $ppp['due_date'];
            $params['dueamount'] = $ppp['total'];
            $params['bookingid'] = $ppp['booking_id'];
            $params['paylink'] = URL::site('/pay-online.html?booking_id=' . $ppp['booking_id'] . '&plan_payment_id=' . $ppp['id'] . '&amount=' . $ppp['total'] . '&contact_id=' . $ppp['contact_id']);
            $recipients = array(
                array(
                    'target_type' => 'CMS_CONTACT3',
                    'target' => $ppp['contact_id']
                )
            );
            $c3 = new Model_Contacts3($ppp['contact_id']);
            if ($parent_id = $c3->get_primary_contact()) {
                if ($parent_id != $ppp['contact_id']) {
                    $recipients[] = array(
                        'target_type' => 'CMS_CONTACT3',
                        'target' => $ppp['contact_id']
                    );
                }
            }

            $mm->send_template(
                'course-payment-plan-reminder',
                null,
                null,
                $recipients,
                $params
            );
        }
        return $ppp;
    }

    public function get_days_row($contact_id, $date, $filters = array()){
        $closeDates = $this->get_booked_days($contact_id, $date, $filters);

        if(!$closeDates){
            return false;
        }

        $days = DB::select(
            'item.id',
            'booking.contact_id',
            'contact.title',
            'contact.first_name',
            'contact.last_name',
            'event.datetime_start'
        )
            ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'item'    ))
            ->join(array('plugin_courses_schedules_events', 'event'   ))
            ->on('item.timeslot_id',       '=', 'event.id')
            ->join(array('plugin_ib_educate_bookings',      'booking' ))
            ->on('item.booking_id',      '=', 'booking.booking_id')
            ->join(array('plugin_contacts3_contacts',       'contact' ))
            ->on('item.delegate_id',   '=', 'contact.id')
            ->where('item.delete', '=', 0)
            ->and_where('event.delete', '=', 0)
            ->and_where('contact.id', '=', $contact_id)
            ->and_where('event.datetime_start', 'IN', $closeDates)
            ->group_by(DB::expr('DATE(event.datetime_start)'))
            ->order_by('event.datetime_start', 'ASC');

        if($filters){
            $days->and_where_open();
            foreach ($filters as $status) {
                if ($status == 'Absent' || $status === '') {
                    $days->or_where('item.timeslot_status', '=', '');
                } else if ($status === 'Attending') {
                    $days->or_where('item.attending', '=', 1);
                } else if ($status === 'Not Attending') {
                    $days->or_where('item.attending', '=', 0);
                } else {
                    $days->or_where(DB::expr("find_in_set('" . $status . "', item.timeslot_status)"), '>', 0);
                }
            }
            $days->and_where_close();
        }

        return $days->execute()->as_array();
    }

    protected function get_booked_days($contact_id, $date, $filters){
        $now = new DateTime();
        $date = new DateTime($date);
        $interval = $now->diff($date);

        $closeDates = DB::select('event.datetime_start')
            ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'item'    ))
            ->join(array('plugin_courses_schedules_events', 'event'   ))
            ->on('item.timeslot_id',       '=', 'event.id')
            ->join(array('plugin_ib_educate_bookings',      'booking' ))
            ->on('item.booking_id',      '=', 'booking.booking_id')
            ->join(array('plugin_contacts3_contacts',       'contact' ))
            ->on('item.delegate_id',   '=', 'contact.id')
            ->where('item.delete', '=', 0)
            ->and_where('event.delete', '=', 0)
            ->and_where('contact.id', '=', $contact_id)
            ->group_by(DB::expr('DATE(event.datetime_start)'));

        if($interval->format('%a') == 0){
            $closeDates->order_by(DB::expr("ABS(DATEDIFF(event.datetime_start, NOW()))"), 'ASC');
        }else{
            $closeDates->order_by(DB::expr("ABS(DATEDIFF(event.datetime_start, '{$date->format('Y-m-d H:i:s')}'))"), 'ASC');
        }

        if($filters){
            $closeDates->and_where_open();
            foreach ($filters as $status) {
                if ($status === '' || $status === 'Absent') {
                    $closeDates->or_where('item.timeslot_status', '=', '');
                } else if ($status === 'Attending') {
                    $closeDates->or_where('item.attending', '=', 1);
                } else if ($status === 'Not Attending') {
                    $closeDates->or_where('item.attending', '=', 0);
                } else {
                    $closeDates->or_where(DB::expr("find_in_set('" . $status . "', item.timeslot_status)"), '>', 0);
                }
            }
            $closeDates->and_where_close();
        }

        return $closeDates->execute()->as_array();
    }

    public function get_classes_by_day($contact_id, $date, $filters = array()){

        $q = DB::select('*', array('course.title', 'course'))->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'item'))
            ->join(array('plugin_ib_educate_bookings',      'booking' ))
            ->on('item.booking_id',      '=', 'booking.booking_id')
            ->join(array('plugin_contacts3_contacts',       'contact' ))
            ->on('booking.contact_id',   '=', 'contact.id')
            ->join(array('plugin_courses_schedules_events', 'event'   ))
            ->on('item.period_id',       '=', 'event.id')
            ->join(array('plugin_courses_schedules',        'schedule'))
            ->on('event.schedule_id',    '=', 'schedule.id')
            ->join(array('plugin_courses_courses',          'course'  ))
            ->on('schedule.course_id',   '=', 'course.id')
            ->join(array('plugin_contacts3_notes',          'notes'), 'LEFT')
            ->on('item.booking_item_id',    '=', 'notes.link_id')
            ->where('item.delete', '=', 0)
            ->and_where('item.booking_status', '<>', 3);

        $date = new DateTime(date('Y-m-d', strtotime($date)));

        $q->and_where('booking.contact_id', '=', $contact_id);
        $q->and_where(DB::expr('DATE_FORMAT(event.datetime_start, "%Y-%m-%d")'), '=', $date->format('Y-m-d'));

        if($filters){
            $q->and_where_open();
            foreach ($filters as $status) {
                if ($status === '' || $status === 'Absent') {
                    $q->or_where('item.timeslot_status', '=', '');
                } else if ($status === 'Attending') {
                    $q->or_where('item.attending', '=', 1);
                } else if ($status === 'Not Attending') {
                    $q->or_where('item.attending', '=', 0);
                } else {
                    $q->or_where(DB::expr("find_in_set('" . $status . "', item.timeslot_status)"), '>', 0);
                }
            }
            $q->and_where_close();
        }

        $bookings = $q->execute()->as_array();

        foreach ($bookings as $i => $booking) {
            if ($booking['timeslot_status'] === '') {
                $booking['timeslot_status'] = 'Absent';
            }
            $bookings[$i]['timeslot_status'] = explode(',', $booking['timeslot_status']);
        }

        return $bookings;
    }

    public static function get_schedule_timeslots($schedule_id)
    {
        self::tmp_booking_count();

        $contacts_table = (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? Model_Contacts3::CONTACTS_TABLE : Model_Contacts::TABLE_CONTACT);

        $timeslots = DB::select(
            'timeslot.*',
            'tmp_count.booking_count',
            ['room.name', 'room'],
            [DB::expr("TRIM(CONCAT_WS(' ', `trainer`.`first_name`, `trainer`.`last_name`))"), 'trainer']
        )
            ->from([Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslot'])
            ->join(['tmp_timeslot_booking_counts',       'tmp_count'], 'left')->on('tmp_count.timeslot_id', '=', 'timeslot.id')
            ->join([Model_ScheduleEvent::TABLE_SCHEDULES, 'schedule'], 'left')->on('timeslot.schedule_id',  '=', 'schedule.id')
            ->join([Model_Locations::TABLE_LOCATIONS,         'room'], 'left')->on('schedule.location_id',  '=', 'room.id')
            ->join([$contacts_table,                       'trainer'], 'left')->on('schedule.trainer_id',   '=', 'trainer.id')
            ->where('timeslot.schedule_id', '=', $schedule_id)
            ->and_where('timeslot.delete', '=', 0)
            ->order_by('timeslot.datetime_start')
            ->execute()
            ->as_array();

        return $timeslots;
    }

    public function get_bulk_update_classes($data){
        $filters = array();

        if(!empty($data['target']) && $data['target'] == 'family'){
            $user = Auth::instance()->get_user();
            $family_members = Model_Contacts3::get_all_family_members_ids_for_guardian_by_user($user['id']);
            $filters['contact_ids'] = $family_members;
        }elseif(!empty($data['target']) && $data['target'] == 'member' && !empty($data['contact_id'])){
            $filters['contact_ids'] = array($data['contact_id']);
        }else{
            return array();
        }

        $affectedClasses = DB::select('item.*', 'event.datetime_start', array('course.title', 'course'))
            ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'item'    ))
            ->join(array('plugin_courses_schedules_events', 'event'   ))
            ->on('item.period_id',       '=', 'event.id')
            ->join(array('plugin_ib_educate_bookings',      'booking' ))
            ->on('item.booking_id',      '=', 'booking.booking_id')
            ->join(array('plugin_contacts3_contacts',       'contact' ))
            ->on('booking.contact_id',   '=', 'contact.id')
            ->join(array('plugin_courses_schedules',        'schedule'))
            ->on('event.schedule_id',    '=', 'schedule.id')
            ->join(array('plugin_courses_courses',          'course'  ))
            ->on('schedule.course_id',   '=', 'course.id')
            ->where('item.delete', '=', 0)
            ->and_where('event.delete', '=', 0)
            ->and_where('contact.id', 'IN', $filters['contact_ids']);

        /*if($data['attending'] == 'will_attend'){
            $affectedClasses->and_where('item.attending', '=', 0);
        }elseif($data['attending'] == 'will_not_attend'){
            $affectedClasses->and_where('item.attending', '=', 1);
        }*/

        if(!empty($data['week_days']) && is_array($data['week_days'])){
            $affectedClasses->and_where(DB::expr('DAYOFWEEK(event.datetime_start)'), 'IN', $data['week_days']);
        }

        if(!empty($data['date_from']) && self::verifyDate($data['date_from'])){
            $filters['date_from'] = new DateTime($data['date_from']);
            $affectedClasses->and_where('event.datetime_start', '>=', $filters['date_from']->format('Y-m-d'));
        }

        if(!empty($data['date_to']) && self::verifyDate($data['date_to'])){
            $filters['date_to'] = new DateTime($data['date_to']);
            $affectedClasses->and_where('event.datetime_start', '<=', $filters['date_to']->format('Y-m-d'));
        }

        return $affectedClasses->execute()->as_array();
    }

    public function attendance_bulk_update($data){

        $current_user      = Auth::instance()->get_user();
        $contacts          = Model_Contacts3::get_contact_ids_by_user($current_user['id']);
        $logged_in_contact = (isset($contacts[0])) ? new Model_Contacts3($contacts[0]['id']) : NULL;

        if ( ! empty($logged_in_contact) AND $logged_in_contact->has_preference('db-mn-ads-gu'))
        {

            $bulkUpdate = DB::update(Model_KES_Bookings::BOOKING_ITEMS_TABLE)

                ->where('booking_item_id', 'IN', $data['classes_ids']);

            if($data['attending'] == 1 OR $data['attending'] == 'will_attend'){
                $bulkUpdate->set(array('attending' => 1));
            }else{
                $bulkUpdate->set(array('attending' => 0));
            }

            return $bulkUpdate->execute();
        }
        else
        {
            IbHelpers::set_message(__('You need access to the "Manage attendance" privilege to use this feature.'), 'danger');
            return FALSE;
        }
    }

    public function get_statistics($contact_id)
    {
        $records = self::get_attendance([
            'contact_id' => $contact_id,
            'timeslot_status' => 'all'
        ]);

        $result = array();
        if (!empty($records['timeslots'])) foreach($records['timeslots'] as $record){
            if (!$record['attending']) {
                @$result['Not Attending']++;
            } else {
                @$result['Attending']++;
            }

            if ($record['timeslot_status'] === '') {
                @$result['Absent']++;
            }

            if (empty($record['timeslot_status'])){ continue; }

            foreach (explode(',', $record['timeslot_status']) as $status) {
                if (in_array($status, ['Present', 'Late', 'Early Departures', 'Temporary Absence'])){
                    @$result[$status]++;
                }
            }
        }
        return $result;
    }

    public function save_attendance($booking_item_id, $is_attending){
        $q = DB::update(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE)
            ->set(array('planned_to_attend' => $is_attending))
            ->where('id', '=', $booking_item_id);
        return $q->execute();
    }

    public static function check_duplicate_bookings($contact_id, $schedule_id, $timeslot_id = null)
    {
        $select = DB::select('*')
            ->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(self::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
                    ->on('bookings.booking_id', '=', 'has_schedules.booking_id')
            ->where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', '<>', 3)
            ->and_where('has_schedules.deleted', '=', 0)
            ->and_where('has_schedules.booking_status', '<>', 3)
            ->and_where('bookings.contact_id', '=', $contact_id)
            ->and_where('has_schedules.schedule_id', '=', $schedule_id);
        if ($timeslot_id != null) {
            $select->join(array(self::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                    ->on('items.booking_id', '=', 'bookings.booking_id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('items.period_id', '=', 'timeslots.id')
                ->and_where('items.delete', '=', 0)
                ->and_where('items.booking_status', '<>', 3)
                ->and_where('items.period_id', '=', $timeslot_id);
        }
        $exits = $select->execute()->current();
        return $exits;
    }

    public static function get_booking_items_family($contact_id = NULL, $family_id = NULL, $before = NULL, $after = NULL, $schedule_id = null, $booking_id = null, $weekdays = null, $attending = null, $timeslot_status = null, $trainer_id = null)
    {
        $is_parent = false;
        if ($contact_id) {
            $c3 = new Model_Contacts3($contact_id);
            if (in_array(1, $c3->get_roles())) { // parent
                $family_id = $c3->get_family_id();
                $is_parent = true;
            }
        }
        $q = DB::select(
            'item.booking_item_id', 'item.booking_id', 'item.period_id', 'item.attending', 'item.booking_status', 'item.timeslot_status',
            'booking.contact_id',
            'contact.title', 'contact.first_name', 'contact.last_name',
            'event.datetime_start', 'event.datetime_end', 'event.schedule_id',
            'schedule.fee_per', DB::expr("IF(event.fee_amount, event.fee_amount, schedule.fee_amount) as fee"),
            array('schedule.name', 'schedule'),'schedule.course_id','schedule.trainer_id',DB::expr('IFNULL(timeslot_location.id, schedule.location_id) as location_id'),
            array('course.title', 'course'), 'course.category_id', 'course.subject_id',
			array('category.category', 'category'),
			array(DB::expr("CONCAT(`trainer`.`first_name`, ' ', `trainer`.`last_name`)"), 'trainer'),
            DB::expr('IF(timeslot_location.id IS NULL, CONCAT_WS(\' \', parent_location.name, location.name), CONCAT_WS(\' \', timeslot_location.name, timeslot_location.name)) as location'),
            DB::expr('IFNULL(timeslot_location.name, location.name) as room'),
            DB::expr('IFNULL(parent_timeslot_location.name, parent_location.name) as building'),
            array('subject.name', 'subject'), 'subject.color',
            DB::expr('GROUP_CONCAT(notes.note) AS `note`'),
            DB::expr('IFNULL(timeslot_location.id, location.id) as location_id')
        )
            ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'item'    ))
            ->join(array('plugin_ib_educate_bookings',      'booking' ))
                ->on('item.booking_id',      '=', 'booking.booking_id')
            ->join(array('plugin_contacts3_contacts',       'contact' ))
                ->on('booking.contact_id',   '=', 'contact.id')
            ->join(array(self::DELEGATES_TABLE, 'has_delegates'), 'left')
                ->on('has_delegates.booking_id', '=', 'booking.booking_id')
                ->on('has_delegates.deleted', '=', DB::expr(0))
            ->join(array('plugin_courses_schedules_events', 'event'   ))
                ->on('item.period_id',       '=', 'event.id')
            ->join(array('plugin_courses_schedules',        'schedule'))
                ->on('event.schedule_id',    '=', 'schedule.id')
            ->join(array('plugin_ib_educate_booking_has_schedules', 'bhs'))
                ->on('booking.booking_id', '=', 'bhs.booking_id')
                ->on('event.schedule_id', '=', 'bhs.schedule_id')
            ->join(array('plugin_courses_courses',          'course'  ), 'LEFT')
                ->on('schedule.course_id',   '=', 'course.id')
			->join(array('plugin_courses_categories',       'category'), 'LEFT')
				->on('course.category_id',   '=', 'category.id')
			->join(array('plugin_contacts3_contacts',       'trainer'), 'LEFT')
				->on('schedule.trainer_id',  '=', 'trainer.id')
            ->join(array('plugin_courses_locations',        'location'), 'left')
                ->on('schedule.location_id', '=', 'location.id')
            ->join(array('plugin_courses_locations',        'parent_location'), 'left')
                ->on('location.parent_id', '=', 'parent_location.id')
            ->join(array('plugin_courses_locations',        'timeslot_location'), 'left')
                ->on('event.location_id', '=', 'timeslot_location.id')
            ->join(array('plugin_courses_locations',        'parent_timeslot_location'), 'left')
                ->on('timeslot_location.parent_id', '=', 'parent_timeslot_location.id')

            ->join(array('plugin_courses_subjects',         'subject' ),'LEFT')
                ->on('course.subject_id',    '=', 'subject.id')
            ->join(array('plugin_contacts3_notes', 'notes'), 'LEFT')
                ->on('item.booking_item_id', '=', 'notes.link_id')
                ->on('notes.table_link_id', '=', DB::expr(3)) // booking item
                ->on('notes.deleted', '=', DB::expr(0))
            ->where('item.delete', '=', 0)
            ->and_where('booking.delete', '=', 0)
            ->and_where('item.booking_status', '<>', 3)
            ->and_where('event.delete', '=', 0)
            ->and_where('bhs.deleted', '=', 0);

        if (!$is_parent) {
            if (is_array($contact_id)) {
                if (count($contact_id) > 0) {
                    $q->and_where_open();
                    $q->or_where('contact.id', 'in', $contact_id);
                    $q->or_where('has_delegates.contact_id', 'in', $contact_id);
                    $q->and_where_close();
                }
            } else {
                if ($contact_id > 0) {
                    $q->and_where_open();
                    $q->or_where('contact.id', '=', $contact_id);
                    $q->or_where('has_delegates.contact_id', '=', $contact_id);
                    $q->and_where_close();
                }
            }
        }

        if ( ! is_null($family_id))
        {
            $q
                ->join(array('plugin_contacts3_family','family'))->on('contact.family_id', '=', 'family.family_id')
                ->where('family.family_id', '=', $family_id);
        }

        if ( ! is_null($before))
        {
            $q->where('event.datetime_start', '<', date('Y-m-d H:i:s', strtotime($before)));
        }
        if ( ! is_null($after))
        {
            $q->where('event.datetime_start', '>=', date('Y-m-d H:i:s', strtotime($after)));
        }

        if ($trainer_id) {
            $q->and_where_open();
            $q->or_where('schedule.trainer_id', '=', $trainer_id);
            $q->or_where('event.trainer_id', '=', $trainer_id);
            $q->and_where_close();
        }

        if ($schedule_id) {
            $q->and_where('schedule.id', '=', $schedule_id);
        }

        if ($booking_id) {
            $q->and_where('booking.booking_id', '=', $booking_id);
        }

        if ($weekdays) {
            $q->and_where(DB::expr("DATE_FORMAT(event.datetime_start, '%w')"), 'in', $weekdays);
        }

        if (is_numeric($attending) && empty($timeslot_status)) {
            $q->and_where('item.attending', '=', $attending);
        }

        if (!empty($timeslot_status)) {
            $q->and_where_open();
                $q->or_where('item.attending', '=', $attending);
            foreach ($timeslot_status as $tstatus) {
                if ($tstatus === '') {
                    $q->or_where('item.timeslot_status', '=', '');
                } else {
                    $q->or_where(DB::expr("find_in_set('" . $tstatus . "', item.timeslot_status)"), '>', 0);
                }
            }
            $q->and_where_close();
        }

        $q->group_by('item.booking_item_id');
        $bookings = $q->order_by('event.datetime_start', 'desc')->execute()->as_array();

        $balance_cache = array();
        foreach($bookings as $key=>$booking)
        {
            if (!isset($balance_cache[$booking['booking_id']])) {
                $balance_cache[$booking['booking_id']] = ORM::factory('Kes_Transaction')->calculate_outstanding_balance($booking['booking_id'], null);
            }
            $bookings[$key]['outstanding'] = $balance_cache[$booking['booking_id']];
        }

        return $bookings;
    }

    public static function get_all_booked_periods($contact_id = NULL)
    {
        $result = array();
        if ( ! is_null($contact_id))
        {
            $data = array('location'=>'', 'room'=>'', 'category'=>'', 'year'=>'', 'search'=>'');
            $events = DB::select()->from('plugin_courses_schedules_events')->where('delete','=', 0);
            $booked = DB::select('item.period_id')
                ->from(array(self::BOOKING_TABLE,'booking'))
                ->join(array(self::BOOKING_ITEMS_TABLE,'item'))
                ->on('booking.booking_id','=','item.booking_id')
                ->where('booking.contact_id','=',$contact_id)
                ->and_where('item.delete','=',0)
                ->distinct(TRUE)
                ->execute()
                ->as_array();
            if ($booked)
            {
                $q = DB::select(array('t1.id','schedule_id'),array('t2.id','period_id'),'t1.name','t2.datetime_start','t2.datetime_end',array(DB::expr('COALESCE(t1.max_capacity,t3.capacity)'),'capacity'),array('t3.name','location'),'t0.category_id', 'category.category','t4.color')
                    ->from(array('plugin_courses_courses','t0'))
                    ->join(array('plugin_courses_categories', 'category'))->on('t0.category_id', '=', 'category.id')
                    ->join(array('plugin_courses_schedules','t1'))->on('t1.course_id','=','t0.id')
                    ->join(array($events,'t2'))->on('t2.schedule_id','=','t1.id')
                    ->join(array('plugin_courses_locations','t3'),'LEFT')->on('t3.id','=','t1.location_id')
                    ->join(array('plugin_courses_subjects','t4'),'LEFT')->on('t0.subject_id', '=', 't4.id')
                    ->where('t2.id','IN',$booked);
                $q = $q->execute()->as_array();
                foreach($q as $key=>$row)
                {
                    $r = DB::select(array(DB::expr('count(booking_item_id)'),'count'))->from(Model_KES_Bookings::BOOKING_ITEMS_TABLE)->where('period_id','=',$row['period_id'])->and_where('delete','=',0)->execute()->current();
                    $enquiries = DB::select(array(DB::expr('count(t1.booking_item_id)'),'count'))->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE,'t1'))->join(array(Model_KES_Bookings::BOOKING_TABLE,'t2'))->on('t1.booking_id','=','t2.booking_id')->where('t1.period_id','=',$row['period_id'])->and_where('t2.booking_status','=',1)->execute()->current();
                    $result[] = array('schedule_id' => $row['schedule_id'],'period_id' => $row['period_id'],'name' => $row['name'],'datetime_start' => $row['datetime_start'],'datetime_end' => $row['datetime_end'],'room_no' => $row['location'],'category_id' => $row['category_id'], 'category' => $row['category'], 'places_available' => $row['capacity'] - $r['count'],'no_of_enquiries' => $enquiries['count'],'color' => $row['color']);
                }
            }
        }
        return $result;
    }

    public static function get_all_contact_schedule_id($contact_id = NULL)
    {
        $result = array();
        if ( ! is_null($contact_id))
        {
            $schedules = DB::select('schedule_id')
                ->from(array(self::BOOKING_TABLE, 'bookings'))
                    ->join(array(self::BOOKING_SCHEDULES, 'bschedules'), 'inner')
                        ->on('bookings.booking_id', '=', 'bschedules.booking_id')
                ->where('bookings.contact_id', '=', $contact_id)
                ->and_where('bookings.booking_status', 'not in', array(3, 5))
                ->and_where('bookings.delete', '=', 0)
                ->and_where('bschedules.publish', '=', 1)
                ->and_where('bschedules.deleted', '=', 0)
                ->execute()
                ->as_array();
            foreach($schedules as $schedule)
            {
                $result[]=$schedule['schedule_id'];
            }
        }
        return $result;
    }

    public function get_booking_items($json = FALSE)
    {
        $data    = self::get_all_booking_periods($this->booking_id,NULL);
        $periods = new stdClass();

        foreach ($data as $period)
        {
            if ( ! isset($periods->{$period['schedule_id']}))
            {
                $periods->{$period['schedule_id']} = new stdClass();
            }

			if ( ! isset($periods->{$period['schedule_id']}->{$period['period_id']}))
			{
				$periods->{$period['schedule_id']}->{$period['period_id']} = new stdClass();
			}

			$periods->{$period['schedule_id']}->{$period['period_id']}->attending = $period['attending'];
			$periods->{$period['schedule_id']}->{$period['period_id']}->note      = $period['note'];
            $periods->{$period['schedule_id']}->{$period['period_id']}->fee       = $period['fee_amount'];
            $periods->{$period['schedule_id']}->{$period['period_id']}->fee_per   = $period['fee_per'];
        }

        return $json ? json_encode($periods) : $periods;
    }

    public function get_additional_booking_details($json = FALSE)
    {
        $data = $this->_sql_get_additional_booking_details();
        $periods = new stdClass();

        $periods->outside_of_school = is_array($data['outside_of_school']) ? $data['outside_of_school'] : array();

        return $json ? json_encode($periods) : $periods;
    }

    /**
     * @param mixed $contact_details
     */
    public function set_contact_details($contact_details)
    {
        if(is_string($contact_details))
        {
            $contact_details = json_decode($contact_details,TRUE);
        }

        $this->contact_details = $contact_details;

        return $this;
    }

    public function set_booking_details($booking_details)
    {
        if(is_string($booking_details))
        {
            $booking_details = json_decode($booking_details,TRUE);
        }

        $this->booking_items = $booking_details;
		$this->set_booking_cost();

        return $this;
    }

    public function set_delete($delete)
    {
        $this->delete = is_numeric($delete) ? intval($delete) : $this->delete;
    }

    public function set_publish($publish)
    {
        $this->publish = is_numeric($publish) ? intval($publish) : $this->publish;
    }

    public function set_schedule_id($schedule)
    {
        $this->schedule_id = is_numeric($schedule) ? intval($schedule) : $this->schedule_id;
    }

    public function get_instance()
    {
		return array(
			'booking_id'      => $this->booking_id,
			'contact_id'      => $this->contact_id,
			'booking_status'  => $this->booking_status,
			'created_date'    => ($this->created_date  == '' ? date('Y-m-d H:i:s', time()) : $this->created_date),
			'modified_date'   => ($this->modified_date == '' ? date('Y-m-d H:i:s', time()) : $this->modified_date),
			'publish'         => $this->publish,
			'delete'          => $this->delete,
			'created_by'      => $this->created_by,
			'modified_by'     => $this->modified_by,
			'custom_discount' => $this->custom_discount,
			'discount_memo'   => $this->discount_memo,
            'coupon_code'     => $this->coupon_code,
            'bill_payer'      => $this->bill_payer,
            'amendable'       => $this->amendable,
            'amount'          => $this->amount,
            'payg_booking_fee'=> $this->payg_booking_fee,
            'sms_booking_fee' => $this->sms_booking_fee,
            'cc_booking_fee'  => $this->cc_booking_fee,
            'payment_method'  => $this->payment_method,
            'extra_data' => $this->extra_data,
            'invoice_details' => $this->invoice_details,
            'how_did_you_hear' => $this->how_did_you_hear,
            'billing_address_id' => $this->billing_address_id,
		);
    }

    public function orm()
    {
        return new Model_Booking_Booking($this->booking_id);
    }

    public function get_booking_id()
    {
        return $this->booking_id;
    }

    public function get_schedule_id()
    {
        return $this->schedule_id;
    }

    public function validate()
    {
        return TRUE;
    }

    public function get_contact_details_name()
    {
        return (is_array($this->contact_details) AND isset($this->contact_details['first_name'])) ? $this->contact_details['first_name'].' '.$this->contact_details['last_name'] : '';
    }

    public function get_contact_details_id()
    {
        return (is_array($this->contact_details) AND isset($this->contact_details['id'])) ? $this->contact_details['id'] : NULL;
    }

    public static function get_booking_discounts($booking_id)
    {
        return DB::select('d.discount_id','d.status','d.amount','d1.title')->from(array(self::DISCOUNTS,'d'))
            ->join(array('plugin_bookings_discounts','d1'))
                ->on('d.discount_id','=', 'd1.id')
            ->where('booking_id','=',$booking_id)->execute()->as_array();
    }

    // Determine if a booking is an application
    public function is_application()
    {
        // This is what was used in the `_sql_insert_booking_courses` function.
        // There may be a cleaner way of doing this, but this is at least what has been working so far.
        return is_array($this->courses) && count($this->courses) > 0;
    }

    /***
     * 1) Take the booking object
     * 2) Get the User Details, confirm user exists.
     * 3) Save the overall booking. The user details, the amount owed, etc. Return the Booking ID.
     * 4) Save the individual Booking Items, i.e. the periods in which the student is attending.
     * TODO Add the amount to the booking
     */
    public function book()
    {
        $result = array();
        $r = DB::select('id')->from(self::CONTACTS_TABLE)->where('id','=',$this->contact_id)->and_where('delete','=',0)->and_where('publish','=',1)->execute()->as_array();
        if (count($r) > 0)
        {
            try
            {
                $activity = new Model_Activity();
                Database::instance()->begin();
                $user = Auth::instance()->get_user();
                $this->modified_by = $user['id'];

                $contact = new Model_Contacts3($this->contact_id);
                /*if ($contact->get_type()) {
                    $contact_type = Model_Contacts3::get_contact_type($contact->get_type());
                    if ($contact_type['name'] == 'org_rep') {
                        $organization_type = Model_Contacts3::find_type('organisation');
                        $related_contact_ids = Model_Contacts3::get_parent_related_contacts($contact->get_id());
                        foreach ($related_contact_ids as $related_contact_id) {
                            $rcontact = new Model_Contacts3($related_contact_id);
                            //if booking is created by an org rep then set the organization for booking contact
                            if ($rcontact->get_type() == $organization_type['contact_type_id']) {
                                $this->contact_id = $rcontact->get_id();
                                $contact = $rcontact;
                            }
                        }
                    }
                }*/

                if (is_numeric($this->booking_id))
                {
                    $this->sql_update_booking();
                    $activity->set_action('update');
                }
                else
                {
                    if ($this->is_application()) {
                        $this->booking_status = self::ENQUIRY;
                    }

                    $this->created_by = $this->modified_by;
                    $this->sql_insert_booking();
                    $activity->set_action('create');

                }

                // Reformat the subject data and save it to the contact preferences
                if (!empty($this->application['subject'])) {
                    $subject_preferences = [];

                    foreach ($this->application['subject'] as $subject) {
                        if (@$subject['id']) {
                            $subject_preferences[] = ['subject_id' => $subject['id'], 'level_id' => @$subject['level']];
                        }
                    }

                    $contact->set_subject_preferences($subject_preferences);
                }

                if (!empty($this->application['cycle'])) {
                    $contact->set_cycle($this->application['cycle']);
                }

                if (!empty($this->application['courses_want'])) {
                    $contact->set_courses_i_would_like(@$this->application['courses_want'][0]['course']);
                    $contact->set_points_required(@$this->application['courses_want'][0]['points']);
                }


                $contact->set_date_modified()->save();

				$activity
					->set_item_type('booking')
					->set_item_id($this->booking_id)
					->set_user_id($user['id'])
                    ->set_scope_id($this->contact_id)
					->save();
                $this->sql_save_delegates();
                $this->_sql_remove_booking_items();
                $this->sql_insert_booking_items();
//                $this->_sql_remove_booking_schedules();
                $this->_sql_insert_booking_schedules();
                $this->_sql_insert_booking_courses();
                //$this->sql_manage_transaction();
                $this->add_additional_booking_details();
                // TODO $this->add_amount_to_booking();
                Database::instance()->commit();
                $result['result'] = TRUE;
                $result['id'] = $this->booking_id;
                $schedules = array_keys($this->booking_items);
                    foreach($schedules as $schedule_id) {
                        $schedule = Model_Schedules::get_schedule($schedule_id);
                        $waitlist_find = ORM::factory('Course_Waitlist')
                            ->where('course_id', '=', $schedule['course_id'])
                            ->and_where('schedule_id', '=', $schedule_id)
                            ->and_where('deleted', '=', 0)
                            ->and_where('contact_id' , '=', $this->contact_id)->find();
                        if (!empty($waitlist_find)) {
                            $waitlist_find->set('deleted', 1);
                            $waitlist_find->save_with_moddate();
                        }
                    }
            }
            catch(Exception $e)
            {
                $result['result'] = FALSE;
                Database::instance()->rollback();
                throw $e;
            }
        }
        else
        {
            $result['result'] = FALSE;
            $result[] = array('id' => 'select_contact','message' => 'This user does not exist');
        }

        return $result;
    }

    public static function get_delegates($booking_id, $args = [])
    {
        $delegates = DB::select(
            'delegates.*',
            'has_delegates.cancelled',
            'has_delegates.cancel_reason_code',
            ['emails.value', 'email'],
            ['mobiles.country_dial_code','country_dial_code'],
            ['mobiles.dial_code', 'dial_code'],
            ['mobiles.value', 'mobile'],
            ['organisation.id', 'organisation_id'],
            [DB::expr("CONCAT(IFNULL(`organisation`.`first_name`, ''), ' ', IFNULL(`organisation`.`last_name`, ''))"), 'organisation_name']
        )
            ->from(array(self::DELEGATES_TABLE, 'has_delegates'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'delegates'), 'inner')
                    ->on('has_delegates.contact_id', '=', 'delegates.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')
                    ->on('delegates.notifications_group_id', '=', 'emails.group_id')
                    ->on('emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobiles'), 'left')
                    ->on('delegates.notifications_group_id', '=', 'mobiles.group_id')
                    ->on('mobiles.notification_id', '=', DB::expr(2))
            ->join([Model_CONTACTS3::CONTACT_RELATIONS_TABLE, 'relations'], 'left')
                ->on('relations.child_id', '=', 'delegates.id')
                ->on('relations.position', '=', DB::expr("'organisation'"))
            ->join([Model_CONTACTS3::CONTACTS_TABLE, 'organisation'], 'left')
                ->on('relations.parent_id', '=', 'organisation.id')
            ->where('has_delegates.booking_id', '=', $booking_id)
            ->and_where('has_delegates.deleted', '=', 0)
            ->order_by('delegates.first_name')
            ->order_by('delegates.last_name')
            ->group_by('delegates.id');

        if (empty($args['include_cancelled'])) {
            $delegates = $delegates->where('has_delegates.cancelled', '=', 0);
        }

        return $delegates
            ->execute()
            ->as_array();
    }

    /**
     * Wrapper function for Controller_Admin_Bookings::process_booking($data) with simplified input
     * Saves a booking, creates a transaction and pays for it
     *
     * Run on an existing booking object:
     * $booking = new Model_KES_Booking($data);
     * $booking->book_and_pay($args);
     *
     * @param $args array    associative array with the following keys
     *      amount   amount being paid
     *      ccType   credit card type
     *      ccName   name on credit card
     *      ccNum    credit card number
     *      ccv      three-digit number from the back of the card
     *      ccExpMM  two-digit expiration month
     *      ccExpYY  two-digit expiration year
     *
     * @return array    associative array with the following keys
     *      booking  object for the booking
     *      payment  associative array with status on the booking
     *
     */
    public function book_and_pay($args)
    {
        $data = array(
            'amount'     => ($args['amount']) ? $args['amount'] : 0,
            'how_did_you_hear' => ($args['how_did_you_hear']) ?  $args['how_did_you_hear'] : 0,
            'discount'   => '',
            'schedules'  => array(),
            'contact_id' => $this->contact_id,
            'discounts'  => !empty($args['discounts']) ? $args['discounts'] : array(),
            'courses'    => array()
        );

        $schedule_ids = !empty($this->schedule_ids) ? $this->schedule_ids : array_keys($this->booking_items);

        $has_payg = false;
        $schedules_in_progress = false;
        if (!empty($schedule_ids)) {
            foreach ($schedule_ids as $schedule_id) {
                $schedule = Model_Schedules::get_schedule($schedule_id);
                $schedules_in_progress = ($schedules_in_progress === true) ? $schedules_in_progress : (strtotime($schedule['start_date']) < strtotime('now'));
                $data['schedules'][] = array(
                    'id'            => $schedule_id,
                    'schedule_id'   => $schedule_id,
                    'prepay'        => ($schedule['payment_type'] == 1),
                    'schedule_cost' => $schedule['fee_amount']
                );
                if ($schedule['payment_type'] == 2) {
                    $has_payg = true;
                }
            }
        }

        if ($has_payg) {
            $args['payg_booking_fee'] = (float)Settings::instance()->get('course_payg_booking_fee');
        }

        foreach ($args['courses'] as $course) {
            $course_details = Model_Courses::get_course($course['course_id']);
            $data['courses'][] = array(
                'id' => $course['course_id'],
                'course_id' => $course['course_id'],
                'fulltime_price' => $course_details['fulltime_price'],
                'paymentoption_id' => $course['paymentoption_id'],
            );
        }
        $data['interview_status'] = $this->interview_status ?? null;
        $data['application_status'] = (isset($args['application']) && Settings::instance()->get('checkout_customization') == 'sls') ? 'Enquiry' : null;

        // Make the booking and transaction
        $data        = array_merge($args, $data);
        //header('content-type: text/plain');print_r($data);Exit;
        $data['booking_status'] = ($schedules_in_progress) ? Model_KES_Bookings::INPROGRESS : Model_KES_Bookings::CONFIRMED;

        if (!empty($args['is_sales_quote'])) {
            $sales_quote_status = new Model_Booking_Status(['title' => 'Sales Quote']);
            $data['booking_status'] = $sales_quote_status->status_id;
        }

        $process     = Controller_Admin_Bookings::process_booking($data);
        $this->booking_id = @$process['booking_id'];
        if (isset($process['status']) && $process['status'] == 'error') {
            return array(
                'booking' => $process, // contains error data
                'payment' => $process  // contains error data
            );
        }
        $booking     = new Model_KES_Bookings($process['booking_id']);
        $transaction = new Model_KES_Transaction($process['transaction_id']);

        //header('content-type: text/plain');print_r($process);print_r($booking);print_r($transaction);exit;
        // Make the payment
        $card_id = null;
        if ($process['amount'] > 0 && $args['payment_method'] == 'cc' && @$process['booking_id']) {
            if (@$args['card_save'] == 0 && @$args['saved_card_id']) {
                $saved_cards = Model_Contacts3::get_cards($this->contact_id, $args['saved_card_id'], true);
                if (count($saved_cards) == 1) {
                    $card_id = $args['saved_card_id'];
                }
            }
            if (@$args['cc_store'] == 'YES') {
                //$contact_id, $order_id, $card_type, $card_number, $expdate, $holder_name
                $card_id = Model_Payments::card_save(
                    isset($args['card_contact_id']) ? $args['card_contact_id'] : $args['student_id'],
                    $process['transaction_id'],
                    $args['ccType'],
                    $args['ccNum'],
                    $args['ccExpMM'] . $args['ccExpYY'],
                    $args['ccName']
                );
            }
            if ($card_id) {
                self::save_card($process['booking_id'], $card_id,$args['cc_recurring_payments'] == 'YES' ? 1 : 0);
            }
            $payment_status = Model_KES_Payment::get_payment_status(array('status' => 'Payment'));
            $payment_data = array(
                'credit' => 1,
                'transaction_id' => $transaction->id,
                'transaction_balance' => $transaction->total,
                'amount' => $process['amount'],
                'type' => $args['payment_method'] == 'sms' ? 'sms' : 'card',
                'bank_fee' => 0,
                'status' => $payment_status['id'],
                'note' => '',
                'name_cheque' => '',
                'ccType' => isset($args['ccType']) ? $args['ccType'] : '',
                'ccName' => isset($args['ccName']) ? $args['ccName'] : '',
                'ccNum' => isset($args['ccNum']) ? preg_replace('/\D/', '', $args['ccNum']) : '',
                'ccv' => isset($args['ccv']) ? $args['ccv'] : '',
                'ccExpMM' => isset($args['ccExpMM']) ? $args['ccExpMM'] : '',
                'ccExpYY' => isset($args['ccExpYY']) ? $args['ccExpYY'] : '',
                'create_journal' => '',
                'journal_type' => '',
                'credit_transaction' => '',
                'contact_id' => $this->contact_id,
                'transactions' => $process['transactions'],
                'saved_card_id' => $card_id,
                'recurring' => @$args['cc_recurring_payments'] == 'YES' ? 1 : 0
            );

            $payment_data = array_merge($args, $payment_data);
            $payment_data['kes_booking_id'] = $booking->get_booking_id();
            $payment = Controller_Admin_Payments::save_payment($payment_data);
        } else {
            $payment = null;
        }

        if (@$payment['status'] == 'error') {
            DB::update(self::BOOKING_TABLE)
                ->set(array('delete' => 1))
                ->where('booking_id', '=', $process['booking_id'])
                ->execute();
            DB::update(Model_Kes_Transaction::TRANSACTION_TABLE)
                ->set(array('deleted' => 1))
                ->where('booking_id', '=', $process['booking_id'])
                ->execute();
        } elseif (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') && !empty($data['billing_address']['email'])
            && isset($data['guardian_id'])) {
            $primary_contact_details = new Model_Contacts3($data['guardian_id']);
                // if the user's linked to an organisation , give the org the billing details, otherwise update the invidividual
            if(count($primary_contact_details->get_contact_relations_details(array('contact_type' => 'organisation'))) > 0) {
                $organisation_id = $primary_contact_details->get_contact_relations_details(array('contact_type' => 'organisation'))[0]['parent_id'];
                $organisation =  Model_Organisation::get_org_by_contact_id($organisation_id);
                $primary_biller = new Model_Contacts3($organisation->get_primary_biller_id());
                // If contact in org, update their billing address and set them as primary biller
                $org_has_primary_biller = false;
                // get all contacts with the same email
                foreach (Model_Contacts3::search(['email' => $data['billing_address']['email']]) as $contact) {
                    $contact = new Model_Contacts3($contact['id']);
                    // get organisation of the contact and check if it it the same as the signed in user's org
                    foreach($contact->get_contact_relations_details(array('contact_type' => 'organisation')) as $organisation_rel) {
                        if($organisation_id === $organisation_rel['parent_id']) {
                            $org_has_primary_biller = true;
                            $contact->set_first_name($data['billing_address']['first_name']);
                            $contact->set_last_name($data['billing_address']['last_name']);
                            $contact->save();
                            $contact_to_bill = $contact;
                            break 2;
                        }
                    }
                }
                if(!$org_has_primary_biller) {
                    $family = new Model_Family();
                    $family->load(array('family_name' => $data['billing_address']['last_name']));
                    $family->save();
                    $contact_data = array();
                    $contact_data['family_id'] = $family->get_id();
                    $contact_data['type'] = Model_Contacts3::find_type('Staff')['contact_type_id'];
                    $contact_data['first_name'] = $data['billing_address']['first_name'];
                    $contact_data['last_name'] = $data['billing_address']['last_name'];
                    $contact_data['subtype_id'] = 1;
                    $contact_data['notifications'] = array(
                        array(
                            'id' => 'new',
                            'value' => $data['billing_address']['email'],
                            'notification_id' => 1
                        )
                    );
                    $contact_to_bill = new Model_Contacts3();
                    $contact_relations[] = array(
                        'parent_id' => $organisation->get_contact_id(),
                        'position' => 'organisation'
                    );
                    $contact_to_bill->set_contact_relations($contact_relations);
                    $contact_to_bill->load($contact_data);
                    $contact_to_bill->save();
                }
                $organisation->set_primary_biller_id($contact_to_bill->get_id());
                $organisation->save();
                $org_billing = $organisation->get_contact()->get_billing_address();
                $org_billing->load($data['billing_address']);
                $org_billing->save();
                // add the org personal address if none exists
                $org_personal = new Model_Residence($organisation->get_contact()->get_residence());
                // need to set it and save it in for new addresses
                if(empty($org_personal->get_address1()) && empty($org_personal->get_address2()) &&
                    empty($org_personal->get_address3())) {
                    $org_personal->load($data['billing_address']);
                    $org_personal->save();
                    $organisation->get_contact()->set_residence($org_personal);
                }
                $organisation->get_contact()->set_billing_address($org_billing);
                $organisation->get_contact()->save();
            } else {
                $contact_to_bill = new Model_Contacts3($data['guardian_id']);
            }
            $contact_billing = $contact_to_bill->get_billing_address();
            $contact_billing->load($data['billing_address']);
            $contact_billing->save();
            // Check if the personal address is empty, if it is give the personal address the billing address
            $primary_address = $contact_to_bill->get_address();
            if (empty($primary_address->get_address1()) && empty($primary_address->get_address2()) && empty($primary_address->get_town())) {
                $primary_address->load($data['billing_address']);
                $primary_address->save();
            }
            // if the billing address email is not empty, we can assume the purchase order was selected and the billing details are the organisation's
        } else if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')
            && isset($data['guardian_id']) && $data['billing_address']){
            // The guardian gets the billing address details
            $primary_contact_details = new Model_Contacts3($data['guardian_id']);
            $primary_billing = $primary_contact_details->get_billing_address();
            $primary_billing->load($data['billing_address']);
            $primary_billing->save();
            // Check if the personal address is empty, if it is give the personal address the billing address
            $primary_address = $primary_contact_details->get_address();
            if(empty($primary_address->get_address1()) && empty($primary_address->get_address2()) && empty($primary_address->get_town())) {
                $primary_address->load($data['billing_address']);
                $primary_address->save();
            }
        }

        //remove booked contacts from waitlist
            foreach($schedule_ids as $schedule_id) {
                $schedule = Model_Schedules::get_schedule($schedule_id);
                $waitlist_find = ORM::factory('Course_Waitlist')
                    ->where('course_id', '=', $schedule['course_id'])
                    ->and_where('schedule_id', '=', $schedule_id)
                    ->and_where('deleted', '=', 0)
                    ->and_where('contact_id' , '=', $this->contact_id)->find();
                if (!empty($waitlist_find)) {
                    $waitlist_find->set('deleted', 1);
                    $waitlist_find->save_with_moddate();
                }
            }


        return array(
            'booking' => $booking,
            'payment' => $payment
        );
    }

    /*
     * Get information about the booking, formatted for submission to Google Analytics
     * Usage: $this->get_google_analytics()
     *
     * @return array
     */
    public function get_google_analytics()
    {
        $return = [
            'transactionId' => $this->booking_id,
            'transactionTotal' => (float) $this->amount,
            'currency' => 'EUR',
            'items' => [],
        ];

        foreach ($this->booking_items as $schedule_id => $schedule_data) {
            $schedule = new Model_Course_Schedule($schedule_id);
            $number_of_delegates = 0;

            foreach ($schedule_data as $timeslot_id => $timeslot_data) {
                $number_of_delegates += $timeslot_data->number_of_delegates;
            }

            $return['items'][] = [
                'name'     => $schedule->name,
                'id'       => $schedule_id,
                'price'    => (float) $schedule->fee_amount,
                'quantity' => $number_of_delegates,
                'sku'      => $schedule_id
            ];
        }

        return $return;
    }


    public function cancel()
    {
        $result = array();

        $this->booking_status = self::CANCELLED;
        Database::instance()->begin();
        try
        {
            $this->delete_booking_periods();
            //$this->delete_transactions();
            $this->save();
            $user = Auth::instance()->get_user();
            $activity = new Model_Activity();
            $activity->set_action('cancel');
            $activity
                ->set_item_type('booking')
                ->set_item_id($this->booking_id)
                ->set_user_id($user['id'])
                ->set_scope_id($this->contact_id)
                ->save();
            Database::instance()->commit();
            $result['result'] = TRUE;
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
            $result['result'] = FALSE;
        }

        return $result;
    }

    public function delete()
    {
        $this->booking_status = self::CANCELLED;
        $this->set_delete(1);
        $this->set_publish(0);
        Database::instance()->begin();
        try
        {
            $this->delete_booking_periods();
            $this->delete_transactions();
            $this->save();
            $user = Auth::instance()->get_user();
            $activity = new Model_Activity();
            $activity->set_action('delete');
            $activity
                ->set_item_type('booking')
                ->set_item_id($this->booking_id)
                ->set_scope_id($this->contact_id)
                ->set_user_id($user['id'])
                ->save();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
        }
    }

    public function save()
    {
        $user = Auth::instance()->get_user();
        $this->modified_by = $user['id'];
        $this->sql_update_booking();
    }

    public function save_discounts($booking_id, $schedule_id, $discount_id, $status, $amount, $memo = '')
    {
        if (!$schedule_id) {
            $schedule_id = null;
        }
        $eq = DB::select('booking_id','discount_id')
            ->from(self::DISCOUNTS)
            ->where('booking_id','=',$booking_id);
        if ($discount_id == null) {
            $eq->and_where('discount_id', 'is', null);
        } else {
            $eq->and_where('discount_id', '=', $discount_id);
        }
        if ($schedule_id == null) {
            $eq->and_where('schedule_id', 'is', null);
        } else {
            $eq->and_where('schedule_id', '=', $schedule_id);
        }


        $exists = $eq
            ->execute()
            ->as_array();
        if ($exists) {
            $uq = DB::update(self::DISCOUNTS)
                ->set(array('status' => $status, 'amount' => $amount, 'memo' => $memo))
                ->where('booking_id', '=', $booking_id);
            if ($discount_id == null) {
                $uq->and_where('discount_id', 'is', null);
            } else {
                $uq->and_where('discount_id', '=', $discount_id);
            }
            if ($schedule_id == null) {
                $uq->and_where('schedule_id', 'is', null);
            } else {
                $uq->and_where('schedule_id', '=', $schedule_id);
            }
            $discount = $uq->execute();
        } else {
            $discount = DB::insert(self::DISCOUNTS, array('booking_id', 'schedule_id', 'discount_id', 'status', 'amount', 'memo'))
                ->values(array($booking_id, $schedule_id, $discount_id, $status, $amount, $memo))->execute();
        }

        return $discount;
    }

    public function get_booking_schedules()
    {
        return DB::select('has_schedule.*', 'schedule.course_id', 'category.category', 'schedule.charge_per_delegate')
            ->from(array('plugin_ib_educate_booking_has_schedules', 'has_schedule'))
            ->join(array('plugin_courses_schedules', 'schedule'), 'inner')
            ->on('has_schedule.schedule_id', '=', 'schedule.id')
            ->join(array('plugin_courses_courses', 'courses'), 'inner')
            ->on('schedule.course_id', '=', 'courses.id')
            ->join(array('plugin_courses_categories', 'category'), 'inner')
            ->on('courses.category_id', '=', 'category.id')
            ->where('booking_id', '=', $this->booking_id)
            ->execute()
            ->as_array();
    }

    public function send_booking_emails($data)
    {
        $delegates = $this->get_delegate_contacts();
        // Do not send the delegate booking emails if the "delegate" only the same lead booker
        if((count($delegates) > 0) && !(count($delegates) === 1 && current($delegates)->get_id() !== $data['contact_id'])) {
            $lead_booker = new Model_Contacts3($data['contact_id']);
            $data['schedule_info'] =  Model_Schedules::get_schedule($booking_schedule['id']);
            $data['organisation'] = $lead_booker->get_linked_organisation();
            Controller_Frontend_Contacts3::send_org_emails($data, $lead_booker, $delegates);
        } else {
            $this->send_booking_create_notification($data);
        }
    }
    /*** PRIVATE FUNCTIONS ***/

    /**
     * This function requires contact ID.
     */
    private function get_contact_details()
    {
        $data = $this->sql_get_contact_details();
        $this->set_contact_details($data);
    }

    /**
     * This function required booking ID.
     */
    private function get_booking_details()
    {
        return $this->sql_get_booking_details();
    }

    private function delete_booking_periods()
    {
        DB::update(self::BOOKING_ITEMS_TABLE)->set(array('delete' => 1))->where('booking_id','=',$this->booking_id)->execute();
    }

    private function delete_transactions()
    {
        DB::update(self::TRANSACTIONS_TABLE)->set(array('delete' => 1))->where('booking_id','=',$this->booking_id)->execute();
    }

    private function sql_get_contact_details()
    {
        return DB::select('id','first_name','last_name')->from(self::CONTACTS_TABLE)->where('id','=',$this->contact_id)->execute()->current();
    }

    private function sql_get_booking_details()
    {
        $booking = DB::select_array(array_keys($this->get_instance()))->from(self::BOOKING_TABLE)->where('booking_id','=',$this->booking_id)->execute()->current();
        if ($booking['extra_data']) {
            $booking['extra_data'] = json_decode($booking['extra_data'], true);
        }
        return $booking;
    }

    private function sql_insert_booking()
    {
        $data = $this->get_instance();
        if (@$data['extra_data'] && is_array(@$data['extra_data'])) {
            @$data['extra_data'] = json_encode(@$data['extra_data'], JSON_PRETTY_PRINT);
        }
        $q = DB::insert(self::BOOKING_TABLE)->values($data)->execute();
        $this->set_booking_id($q[0]);
    }

    private function sql_save_delegates()
    {
        DB::delete(self::DELEGATES_TABLE)->where('booking_id', '=', $this->booking_id)->execute();
        if (count($this->delegate_ids) == 0) {
            $this->delegate_ids[] = $this->contact_id;
        }
        foreach ($this->delegate_ids as $delegate_id) {
            DB::insert(self::DELEGATES_TABLE)
                ->values(
                    array('booking_id' => $this->booking_id, 'contact_id' => $delegate_id)
                )->execute();
        }
    }

    private function sql_update_booking()
    {
        $data = $this->get_instance();
        if (@$data['extra_data'] && is_array(@$data['extra_data'])) {
            @$data['extra_data'] = json_encode(@$data['extra_data'], JSON_PRETTY_PRINT);
        }

        DB::update(self::BOOKING_TABLE)->set($data)->where('booking_id','=',$this->booking_id)->execute();
    }

    private function sql_insert_booking_items()
    {
		// Notes might be saved too
		if (class_exists('Model_EducateNotes'))
		{
			// Index ID for the booking items to be used in the notes table
			$table_id = Model_EducateNotes::get_table_link_id_from_name(Model_KES_Bookings::BOOKING_ITEMS_TABLE);
		}

        if (count($this->booking_items) > 0)
        {
            foreach ($this->booking_items as $schedule_id => $schedule_periods)
            {
				foreach ($schedule_periods as $period_id => $period_data)
				{
                    if ( ! empty($period_data['seat_row_id']))
                    {
                        $seat_row_id = $period_data['seat_row_id'];
                        $seat_fee    = DB::select('price')
                            ->from('plugin_courses_schedules_have_zones')
                            ->where('row_id',      '=', $period_data['seat_row_id'])
                            ->where('schedule_id', '=', $schedule_id)
                            ->execute()
                            ->get('price', 0);
                    }
                    else
                    {
                        $seat_row_id = null;
                        $seat_fee = null;
                    }

                    $sales_quote_status = new Model_Booking_Status(['title' => 'Sales Quote']);

                    if ($this->get_booking_status() == $sales_quote_status->status_id) {
                        $status_id = $sales_quote_status->status_id;
                        $attending = $period_data['attending'];
                    } else {
                        $confirmed_status = new Model_Booking_Status(['title' => 'Confirmed']);
                        $status_id = $confirmed_status->status_id;
                        $attending = $period_data['attending'];
                    }

					// Save the booking item
					$insert = DB::insert(self::BOOKING_ITEMS_TABLE, array('booking_id', 'period_id', 'seat_row_id', 'seat_fee', 'attending', 'booking_status'))
						->values(array(
                                $this->booking_id,
                                $period_id,
                                $seat_row_id,
                                $seat_fee,
                                $attending,
                                $status_id
                            ))
						->execute();
                    $booking_item_id = $insert[0]; // Get the ID of the inserted booking item

                    foreach ($this->delegate_ids as $delegate_id) {
                        DB::insert(
                            self::BOOKING_ROLLCALL_TABLE,
                            array('booking_item_id', 'delegate_id', 'booking_id', 'timeslot_id', 'seat_row_id', 'seat_fee', 'planned_to_attend', 'booking_status')
                        )
                            ->values(
                                array(
                                    $booking_item_id,
                                    $delegate_id,
                                    $this->booking_id,
                                    $period_id,
                                    $seat_row_id,
                                    $seat_fee,
                                    $attending,
                                    $status_id
                                )
                            )
                            ->execute();
                    }

					// Save the note, if there is one
					if (isset($period_data['note']) AND trim($period_data['note']) != '' AND isset($table_id))
					{
						// If a note for this booking item already exists, overwrite it, otherwise create a new note
						$existing_notes = Model_EducateNotes::get_all_notes(array(array('table_link_id', '=', $table_id), array('link_id', '=', $booking_item_id)));
						$note_id = isset($existing_notes[0]) ? $existing_notes[0]['id'] : NULL;
						// $period  = ORM::factory('ScheduleEvent', $period_id);
						$note    = new Model_EducateNotes($note_id);

						// Save the note. It will be prefixed by the timestamp of the event (commented out)
						$note
							->set_column('note', $period_data['note'])
							// ->set_column('note', 'Time slot: '.$period->datetime_start.", \n".$period_data['note'])
							->set_column('link_id', $booking_item_id)
							->set_column('table_link_id', $table_id)
							->save();
					}
				}
            }
        }
    }

    /**
     * Insert the booking Schedules
     */
    private function _sql_insert_booking_schedules()
    {
        $schedule_ids = !empty($this->schedule_ids) ? $this->schedule_ids : array_keys($this->booking_items);
        if (count($schedule_ids)) {
            foreach ($schedule_ids as $schedule_id) {
                $check = DB::select()->from(self::BOOKING_SCHEDULES)->where('booking_id','=',$this->booking_id)->where('schedule_id','=',$schedule_id)->and_where('deleted', '=', 0)->execute()->as_array();
                if ($check) {
                    //DB::update(self::BOOKING_SCHEDULES)->set(array('deleted'=>1,'publish'=>0))->where('booking_id','=',$this->booking_id)->execute();
                } else {
                    DB::insert(self::BOOKING_SCHEDULES, array('booking_id', 'schedule_id', 'booking_status'))
                        ->values(array($this->booking_id, $schedule_id, self::CONFIRMED))
                        ->execute();

                    if (Model_Plugin::is_enabled_for_role('Administrator', 'homework')) {
                        Model_SchedulesStudents::save('new', $this->contact_id, $schedule_id, 'Registered', '');
                    }
                }
                DB::update(Model_KES_Wishlist::WISHLIST_TABLE)
                    ->set(array('deleted' => 1))
                    ->where('contact_id', '=', $this->contact_id)
                    ->and_where('schedule_id', '=', $schedule_id)
                    ->execute();
            }
        }
    }

    private function _sql_insert_booking_courses()
    {
        if ($this->is_application()) {

            DB::insert(self::BOOKING_APPLICATIONS)
                ->values(
                    array(
                        'booking_id' => $this->booking_id,
                        'status_id' => 1,
                        'data' => json_encode($this->interview_status ? $this->_data : $this->application, defined("JSON_PRETTY_PRINT" ) ? JSON_PRETTY_PRINT : 0),
                        'student' => json_encode($this->student, defined("JSON_PRETTY_PRINT" ) ? JSON_PRETTY_PRINT : 0),
                        'interview_status' => $this->interview_status ?: null,
                        'application_status' => $this->application_status ?: null,
                    )
                )
                ->execute();

            foreach ($this->courses as $course) {
                $check = DB::select('*')
                    ->from(self::BOOKING_COURSES)
                    ->where('booking_id', '=', $this->booking_id)
                    ->where('course_id', '=', $course['course_id'])
                    ->and_where('deleted', '=', 0)
                    ->execute()
                    ->current();
                if ($check) {
                    //DB::update(self::BOOKING_SCHEDULES)->set(array('deleted'=>1,'publish'=>0))->where('booking_id','=',$this->booking_id)->execute();
                } else {
                    DB::insert(self::BOOKING_COURSES, array('booking_id', 'course_id', 'booking_status', 'paymentoption_id'))
                        ->values(array($this->booking_id, $course['course_id'], self::ENQUIRY, @$course['paymentoption_id'] ?: null))
                        ->execute();
                }
            }
        }
    }

    private function sql_manage_transaction()
    {

        /**
         * 1) Check if the transaction exists for this booking.
         * 2) If it does, update it.
         */

        $transaction_check_pre_pay = DB::select('transaction_id')->from(self::TRANSACTIONS_TABLE)->where('booking_id','=',$this->booking_id)->and_where('transaction_type','=',1)->and_where('delete','=',0)->and_where('completed','=',0)->execute()->current();
        $transaction_check_payg = DB::select('transaction_id')->from(self::TRANSACTIONS_TABLE)->where('booking_id','=',$this->booking_id)->and_where('transaction_type','=',2)->and_where('delete','=',0)->and_where('completed','=',0)->execute()->current();

        if(is_array($transaction_check_pre_pay) AND count($transaction_check_pre_pay) > 0)
        {
            DB::update(self::TRANSACTIONS_TABLE)->set(array('amount' => $this->booking_cost))->where('booking_id','=',$transaction_check_pre_pay['transaction_id'])->execute();
        }
        elseif($this->booking_cost > 0 AND count($transaction_check_pre_pay) == 0)
        {
            DB::insert(self::TRANSACTIONS_TABLE,array('booking_id','amount'))->values(array($this->booking_id,$this->booking_cost))->execute();
        }

        if(is_array($transaction_check_payg) AND count($transaction_check_payg) > 0)
        {
            DB::update(self::TRANSACTIONS_TABLE)->set(array('amount' => $this->booking_cost))->where('booking_id','=',$transaction_check_payg['transaction_id'])->execute();
        }
        elseif($this->payg_cost > 0 AND count($transaction_check_pre_pay) == 0)
        {
            DB::insert(self::TRANSACTIONS_TABLE,array('booking_id','amount'))->values(array($this->booking_id,$this->payg_cost))->execute();
        }
    }

    private function add_amount_to_booking()
    {

        DB::update(self::BOOKING_TABLE)->set(array('amount'=>$this->amount))->execute();


        $transaction = DB::select('amount','fee','total')->from('plugin_bookings_transactions')->where('booking_id','=',$this->booking_id)->execute()->current();
        /**
         * Update the transaction amount booking with the amount for prepay
         */
        if ($transaction)
        {
            $transaction_check_pre_pay['amount'] = $this->amount;
            ORM::factory('Kes_Transaction')->save_history($this->booking_id,$transaction_check_pre_pay);
        }
    }

    private function add_additional_booking_details()
    {
        if(isset($this->additional_booking_data['outside_of_school']))
        {
            if(count($this->additional_booking_data['outside_of_school']) > 0)
            {
                DB::update(self::BOOKING_SCHEDULE_LABELS)->set(array('delete' => 0))->where('booking_id','=',$this->booking_id)->execute();
                $q = DB::insert(self::BOOKING_SCHEDULE_LABELS,array('label_id','schedule_id','booking_id'));

                foreach($this->additional_booking_data['outside_of_school'] as $key=>$schedule)
                {
                    $q->values(array(6,$schedule,$this->booking_id));
                }

                $q->execute();
            }
        }
    }

    private function set_booking_cost()
    {
        $this->booking_cost = 0;
        /*$fees = Model_Schedules::get_order_data($this->booking_items);
        foreach($fees as $key=>$data)
        {
            if($data['prepay'] == TRUE)
            {
                $this->booking_cost+=$data['fee'];
            }
            else
            {
                $this->payg_cost+=$data['fee'];
            }
        }*/
    }
	
	private function get_booking_schedule_details()
    {
        $q = DB::select('schedule_id')->from(self::BOOKING_SCHEDULE_LABELS)->where('publish','=',1)->and_where('delete','=',0)->and_where('booking_id','=',$this->booking_id)->execute()->as_array();
        $result = array();

        foreach($q as $key=>$item)
        {
            $result[] = intval($item['schedule_id']);
        }

        return $result;
    }

    private function _sql_remove_booking_items()
    {
        DB::update(self::BOOKING_ITEMS_TABLE)->set(array('delete'=>1))->where('booking_id','=',$this->booking_id)->execute();
    }

    /**
     * Remove the booking Schedules
     */
    private function _sql_remove_booking_schedules()
    {
        DB::update(self::BOOKING_SCHEDULES)->set(array('deleted'=>1,'published'=>0))->where('booking_id','=',$this->booking_id)->execute();
    }

    /*** PUBLIC STATIC FUNCTION ***/

    public static function create($id = NULL)
    {
        return new self($id);
    }

    public static function get_payment_types()
    {
        return DB::select(array('method_id','title'))->from(self::PAYMENT_METHOD_TABLE)->order_by('order')->execute()->as_array();
    }

    public static function get_contact_family_bookings($family_id = NULL,$contact_id = NULL, $like = '',$all = NULL,
        $status=NULL, $filter_linked_contact_bookings = null)
    {
        $bookings = [];

        $q = array();
        if(is_numeric($family_id) OR is_numeric($contact_id) OR $like != '' OR $all)
        {

            // Filter out the deleted periods before the join, rather than after.
            // This way existing bookings with deleted periods are not filtered out
            $undeleted_periods = DB::select()->from(self::BOOKING_ITEMS_TABLE)->where('delete', '=', 0);

            $q = DB::select(
//                array('t1.id','schedule_id'),
                't4.booking_id',
                array('t4.modified_date','last_modified'),
                array('t4.contact_id','booking_contact'),
                't4.bill_payer',
                array('billto.family_id', 'bill_family'),
                array('t4.booking_status','status_id'),
                array(DB::expr('CONCAT(t5.first_name," ",t5.last_name)'),'student'),
                array('t5.id','student_id'),
                array('t5.family_id','student_family'),
                array('t3.date_created','date_created'),
                // status - select Application status if it is enquiry, otherwise interview if it's not empty, then booking status
                array(DB::expr("if(`applications`.`application_status` = 'Enquiry' and `applications`.`status_id` = 1, applications.application_status,
                              if(`applications`.`interview_status` IS NULL or `applications`.`interview_status` = '',
                                 t11.title, `applications`.`interview_status`))"), 'status'),
                array('modified_by.id','modified_by_id'),
                array('modified_by.name','modified_by_name'),
                array('modified_by.surname','modified_by_surname'),
                array('modified_by.email','modified_by_email'),
                array('t1.name', 'schedule'),
                array('row.id',    'seat_row_id'),
                array('row.name',  'seat_row_name'),
                array('zone.id',   'zone_id'),
                array('zone.name', 'zone_name'),
                array('has_ftcourses.course_id', 'has_course_id'),
                array('t4.amount', 'booking_amount'),
                array(DB::expr('SUM(discounts.amount)'), 'discount_amount'),
                DB::expr('GROUP_CONCAT(DISTINCT `t6`.`title` SEPARATOR "\n") as `course_title`'),
                DB::expr("min(t2.datetime_start) as start_date"),
                't3.seat_fee',
                't4.amendable',
                't1.study_mode_id',
                'study_mode.study_mode',
                array('t1.id', 'schedule_id')
            )
                ->from(array(self::BOOKING_TABLE,                   't4' ))
                ->join(array(self::DELEGATES_TABLE, 'has_delegates'), 'left')
                    ->on('has_delegates.booking_id', '=', 't4.booking_id')
                    ->on('has_delegates.deleted', '=', DB::expr(0))
                ->join(array(self::DISCOUNTS, 'discounts'), 'left')
                    ->on('t4.booking_id', '=', 'discounts.booking_id')
				->join(array(self::BOOKING_SCHEDULES,               't12'), 'LEFT')
                    ->on('t12.booking_id',    '=', 't4.booking_id')
                    ->on('t12.deleted',    '=', DB::expr(0))
                ->join(array(self::BOOKING_ITEMS_TABLE,                    't3' ), 'LEFT')
                    ->on('t3.booking_id',     '=', 't4.booking_id')
                    ->on('t3.delete',     '=', DB::expr(0))
                ->join(array(self::BOOKING_COURSES,                 'has_ftcourses' ),'LEFT')->on('t4.booking_id',   '=','has_ftcourses.booking_id')
				->join(array('plugin_courses_schedules',            't1' ), 'LEFT')->on('t12.schedule_id',   '=', 't1.id')
                ->join(array('plugin_courses_schedules_events',     't2' ), 'LEFT')->on('t3.period_id',      '=', 't2.id')
                ->join(array('plugin_courses_rows',                 'row'), 'LEFT')->on('t3.seat_row_id',    '=', 'row.id')
                ->join(array('plugin_courses_schedules_have_zones', 'shz'), 'LEFT')->on('shz.row_id',        '=', 'row.id')
                                                                                   ->on('shz.schedule_id',   '=', 't1.id');
                if($filter_linked_contact_bookings) {
                    $q->join(array('plugin_ib_educate_bookings_has_linked_contacts', 'lb'))
                        ->on('t4.booking_id', '=','lb.booking_id');
                }
                $q->join(array('plugin_courses_zones',               'zone'), 'LEFT')->on('shz.zone_id',       '=', 'zone.id')
                ->join(array('plugin_contacts3_contacts',           't5' ), 'LEFT')->on('t4.contact_id',     '=', 't5.id')
                ->join(array('plugin_contacts3_contacts',        'billto'), 'LEFT')->on('t4.bill_payer',     '=', 'billto.id')
				->join(array('plugin_courses_courses',              't6' ), 'LEFT')
                    ->on(DB::expr('t6.id = t1.course_id or has_ftcourses.course_id = t6.id'), '', DB::expr(''))
				->join(array('plugin_courses_years',                't7' ), 'LEFT')->on('t6.year_id',        '=', 't7.id')
                ->join(array(self::BOOKING_STATUS_TABLE,            't11'), 'LEFT')->on('t4.booking_status', '=', 't11.status_id')
                    ->join(array(self::BOOKING_APPLICATIONS, 'applications'), 'LEFT')->on('t4.booking_id', '=',
                        'applications.booking_id')
                ->join(array('engine_users','modified_by'),'LEFT')
                    ->on('t4.modified_by', '=', 'modified_by.id')//,'t1.id');
                ->join(array('plugin_courses_study_modes',   'study_mode'), 'LEFT')->on('t1.study_mode_id',  '=', 'study_mode.id')
                ;

            if ( ! is_null($status))
            {
                $q->where('t11.title','=',$status);
            }
            if ( ! $all)
            {
                if (is_numeric($family_id))
                {
                    $q->where('t5.family_id','=',$family_id);
                }
                else if (is_numeric($contact_id))
                {
                    //this makes it two slow
                    // probably or
                    /*$q->and_where_open();
                    $q->or_where('t5.id','=',$contact_id);
                    $q->or_where('has_delegates.contact_id', '=', $contact_id);
                    $q->and_where_close();*/
                }
                if ($like != '')
                {
                    $q->where(DB::expr('CONCAT(`t4`.`booking_id`, " - ", LOWER(`t5`.`first_name`), " ", LOWER(`t5`.`last_name`), " - ", LOWER(`t1`.`name`), " - ", LOWER(`t6`.`title`), " - ", LOWER(`t7`.`year`))'), 'LIKE', '%'.strtolower($like).'%');
                }
            }
            
            if ($filter_linked_contact_bookings) {
                $q->where('lb.contact_id', '=', $filter_linked_contact_bookings);
            }

            if (!$all && is_numeric($contact_id)) {
                $q2 = clone $q;
                $bookings1 = $q->and_where('t5.id','=',$contact_id)
                    ->group_by('t4.booking_id')
                    ->group_by('t1.id')
                    ->and_where('t4.delete','=',0)
                    ->order_by('t4.modified_date','DESC')
                    ->execute()->as_array();
                $bookings2 = $q2->and_where('has_delegates.contact_id', '=', $contact_id)
                    ->group_by('t4.booking_id')
                    ->group_by('t1.id')
                    ->and_where('t4.delete','=',0)
                    ->order_by('t4.modified_date','DESC')
                    ->execute()->as_array();
                foreach ($bookings1 as $booking) {
                    $bookings[$booking['booking_id'].'_'.$booking['schedule_id']] = $booking;
                }
                foreach ($bookings2 as $booking) {
                    $bookings[$booking['booking_id'].'_'.$booking['schedule_id']] = $booking;
                }
                usort($bookings, function($a1, $a2){
                    if (strtotime($a1['last_modified']) < strtotime($a2['last_modified'])) {
                        return 1;
                    } else if (strtotime($a1['last_modified']) == strtotime($a2['last_modified'])) {
                        return 0;
                    } else {
                        return -1;
                    }
                });
            } else {
                $bookings = $q
                    ->group_by('t4.booking_id')
                    ->group_by('t2.schedule_id')
                    ->and_where('t4.delete','=',0)
                    ->order_by('t4.modified_date','DESC')
                    ->execute()->as_array();
            }


            $booking_ids = array();
            foreach ($bookings as $booking) {
                $booking_ids[] = $booking['booking_id'];
            }
            foreach ($bookings as $key => $booking) {
                $scheduleq = DB::select(
                    's.id',
                    DB::expr("if(s1.booking_status = 3, CONCAT(s.name, ' - <b>' , bstatus.title , '</b>'), s.name) as name"),
                    array('t6.id', 'course_id'),
                    array('t6.title','course_title'),
                    array('t7.year','year'),
                    array('s.name','schedule_title'),
                    's.start_date',
                    's.end_date',
                    's.content_id',
                    array('t8.level','level'),
                    't6.category_id',
                    array('t9.category','category'),
                    array('s.location_id', 'location_id'),
                    array('location.parent_id', 'parent_location_id'),
                    array('location.name','location_name'),
                    'subject.color',
                    array('t10.type','type'),
                    array('bstatus.title', 'status')
                )
                    ->distinct('s.id')
                    ->from(array(self::BOOKING_TABLE,  'booking'))
                    ->join(array(self::BOOKING_SCHEDULES,  's1'), 'left')->on('s1.booking_id', '=', 'booking.booking_id')
                    ->on('s1.deleted','=', DB::expr('0'))
                    ->join(array('plugin_courses_schedules','s'), 'left')->on('s.id','=','s1.schedule_id')
                    ->join(array(self::BOOKING_COURSES, 'has_ftcourses'), 'LEFT')->on('booking.booking_id', '=',
                        'has_ftcourses.booking_id')
                    ->join(array(self::BOOKING_STATUS_TABLE,       'bstatus'), 'LEFT')->on('s1.booking_status', '=', 'bstatus.status_id')
                    ->on('has_ftcourses.deleted', '=', DB::expr('0'))
                    ->join(array('plugin_courses_courses',         't6' ),'LEFT')->on(DB::expr('t6.id = s.course_id or has_ftcourses.course_id = t6.id'), '', DB::expr(''))
                    ->join(array('plugin_courses_years',           't7' ),'LEFT')->on('t6.year_id',    '=','t7.id')
                    ->join(array('plugin_courses_levels',          't8' ),'LEFT')->on('t6.level_id',   '=','t8.level')
                    ->join(array('plugin_courses_categories',      't9' ),'LEFT')->on('t6.category_id','=','t9.id')
                    ->join(array('plugin_courses_types',           't10'),'LEFT')->on('t6.type_id',    '=','t10.id')
                    ->join(array('plugin_courses_locations',  'location'),'LEFT')->on('s.location_id', '=','location.id')
                    ->join(array('plugin_courses_subjects',   'subject' ),'LEFT')->on('t6.subject_id', '=','subject.id')
                ->where('booking.booking_id','=',$booking['booking_id']);
                if ($booking['status_id'] == 3) {

                } else {
                    $scheduleq->where('booking.delete', '=', 0);
                }
                    $schedules = $scheduleq->execute()->as_array();
                $schedule = array(
                    'name'          =>'',
                    'course_title'  =>$bookings[$key]['course_title'],
                    'course_id'     =>'',
                    'year'          =>'',
                    'schedule_title'=>'',
                    'start_date'    =>'',
                    'level'         =>'',
                    'category'      =>'',
                    'location_id'   =>'',
                    'parent_location_id'=>'',
                    'location_name' =>'',
                    'subject.color' =>'',
                    'type'          =>'',
                    'next_timeslot' =>''
                );
                $bookings[$key]=array_merge($bookings[$key],$schedule);
                $bookings[$key]['total'] = number_format(round((float) $booking['booking_amount'], 2),  2);

                $bookings[$key]['course_title'] = '';

                foreach($schedules as $skey => $schedule)
                {
                    $bookings[$key]['schedule_title'] .= '<p>'.$schedule['name'].         '</p>';
                    $bookings[$key]['course_title'] .= '<p>' . $schedule['course_title'] . '</p>';
                    $bookings[$key]['year']           .= '<p>'.$schedule['year'].         '</p>';
                    $bookings[$key]['level']          .= '<p>'.$schedule['level'].        '</p>';
                    $bookings[$key]['category']       .= '<p>'.$schedule['category'].     '</p>';
                    $bookings[$key]['type']           .= '<p>'.$schedule['type'].         '</p>';
                    $bookings[$key]['location_name']  .= '<p>'.$schedule['location_name'].'</p>';

                    $start_period = DB::select(
                            DB::expr('MIN(b.period_id)'),
                            's.datetime_start'
                        )
                        ->from(array(self::BOOKING_ITEMS_TABLE,'b'))
                        ->join(array('plugin_courses_schedules_events','s'))
                        ->on('b.period_id','=','s.id')
                        ->where('attending','=',1)
                        ->where('booking_id','=',$booking['booking_id'])
                        ->execute()
                        ->as_array();

                    $bookings[$key]['start_date']=$start_period[0]['datetime_start'];
                    $bookings[$key]['end_date']=$start_period[0]['datetime_end'];

                    $bookings[$key]['next_timeslot'] .= '<p>' .
                        date::ymdh_to_dmyh(
                            DB::select(DB::expr('MIN(timeslots.datetime_start) as next_timeslot'))
                        ->from(array(self::BOOKING_ITEMS_TABLE, 'items'))
                        ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'))
                        ->on('items.period_id', '=', 'timeslots.id')
                        ->where('items.delete', '=', 0)
                        ->and_where('items.booking_id', '=', $booking['booking_id'])
                        ->and_where('timeslots.schedule_id', '=', $schedule['id'])
                        ->and_where('timeslots.datetime_start', '>=', date::now())
                        ->group_by('timeslots.schedule_id')
                        ->order_by('timeslots.datetime_start', 'asc')
                        ->execute()
                        ->get('next_timeslot')
                        ).
                    '</p>';
                }
                $bookings[$key]['schedules'] = $schedules;
                $outstandings = ORM::factory('Kes_Transaction')->get_booking_transaction($booking['booking_id']);
                $bookings[$key]['outstanding'] = $outstandings['outstanding'];
                if ($bookings[$key]['bill_payer']) {
                    $bookings[$key]['paying_contact'] = $bookings[$key]['bill_payer'];
                    $bookings[$key]['paying_family'] = $bookings[$key]['bill_family'];
                } else {
                    $bookings[$key]['paying_contact'] = $outstandings['paying_contact'];
                    $bookings[$key]['paying_family'] = $outstandings['paying_family'];
                }
                $bookings[$key]['first_name'] =  self::get_booking_first_name($booking['booking_contact']);
                $bookings[$key]['multiple_transaction'] = ORM::factory('Kes_Transaction')->booking_has_multiple_transaction($booking['booking_id']);
            }
        }
        return $bookings;
    }

    public static function get_details($booking_id)
    {
        $bookings = NULL;

        $q = array();
        if (is_numeric($booking_id)) {

            // Filter out the deleted periods before the join, rather than after.
            // This way existing bookings with deleted periods are not filtered out
            $undeleted_periods = DB::select()->from(self::BOOKING_ITEMS_TABLE)->where('delete', '=', 0);

            $q = DB::select(
//                array('t1.id','schedule_id'),
                't4.booking_id',
                array('t4.modified_date','last_modified'),
                array('t4.contact_id','booking_contact'),
                't4.bill_payer',
                array('billto.family_id', 'bill_family'),
                array('t4.booking_status','status_id'),
                array(DB::expr('CONCAT(t5.first_name," ",t5.last_name)'),'student'),
                array('t5.id','student_id'),
                array('t5.family_id','student_family'),
                array('t4.created_date','date_created'),
                array('t11.title','status'),
                array('modified_by.id','modified_by_id'),
                array('modified_by.name','modified_by_name'),
                array('modified_by.surname','modified_by_surname'),
                array('modified_by.email','modified_by_email'),
                array('t1.name', 'schedule')
            )
                ->from(array(self::BOOKING_TABLE,              't4' ))
                    ->join(array(self::BOOKING_SCHEDULES,          't12'),'LEFT')->on('t12.booking_id','=','t4.booking_id')
                    ->join(array('plugin_courses_schedules',       't1' ),'LEFT')->on('t12.schedule_id','=','t1.id')
                    ->join(array('plugin_contacts3_contacts',      't5' ),'LEFT')->on('t4.contact_id', '=','t5.id')
                    ->join(array('plugin_contacts3_contacts',      'billto' ),'LEFT')->on('t4.bill_payer', '=','billto.id')
                    ->join(array('plugin_courses_courses',         't6' ),'LEFT')->on('t6.id',         '=','t1.course_id')
                    ->join(array('plugin_courses_years',           't7' ),'LEFT')->on('t6.year_id',    '=','t7.id')
                    ->join(array(self::BOOKING_STATUS_TABLE,       't11'),'LEFT')->on('t4.booking_status', '=','t11.status_id')
                    ->join(array('engine_users','modified_by'),'LEFT')->on('t4.modified_by', '=', 'modified_by.id');
                $q->where('t4.booking_id', '=', $booking_id);

        $booking = $q
                ->group_by('t4.booking_id')
                ->and_where('t4.delete','=',0)
                ->order_by('t4.modified_date','DESC')
                ->execute()->current();

        if ($booking) {
                $schedules = DB::select(
                    's.id',
                    's.name',
                    's1.booking_status',
                    array('t6.id', 'course_id'),
                    array('t6.title','course_title'),
                    array('t7.year','year'),
                    array('s.name','schedule_title'),
                    's.start_date',
                    array('t8.level','level'),
                    't6.category_id',
                    array('t9.category','category'),
                    array('s.location_id', 'location_id'),
                    array('location.parent_id', 'parent_location_id'),
                    array('location.name','location_name'),
                    'subject.color',
                    array('t10.type','type')
                )
                    ->from(array(self::BOOKING_SCHEDULES,'s1'))
                    ->join(array('plugin_courses_schedules','s'))->on('s.id','=','s1.schedule_id')
                    ->join(array('plugin_courses_courses',         't6' ),'LEFT')->on('t6.id',         '=','s.course_id')
                    ->join(array('plugin_courses_years',           't7' ),'LEFT')->on('t6.year_id',    '=','t7.id')
                    ->join(array('plugin_courses_levels',          't8' ),'LEFT')->on('t6.level_id',   '=','t8.level')
                    ->join(array('plugin_courses_categories',      't9' ),'LEFT')->on('t6.category_id','=','t9.id')
                    ->join(array('plugin_courses_types',           't10'),'LEFT')->on('t6.type_id',    '=','t10.id')
                    ->join(array('plugin_courses_locations',  'location'),'LEFT')->on('s.location_id',    '=','location.id')
                    ->join(array('plugin_courses_subjects',   'subject' ),'LEFT')->on('t6.subject_id',     '=','subject.id')
                    ->where('s1.booking_id','=',$booking['booking_id'])
                    ->where('s1.deleted','=',0)
                    ->execute()->as_array();

                $booking['schedules'] = $schedules;
                $outstandings = ORM::factory('Kes_Transaction')->get_booking_transaction($booking['booking_id']);
                $booking['outstanding'] = $outstandings['outstanding'];
                if ($booking['bill_payer']) {
                    $booking['paying_contact'] = $booking['bill_payer'];
                    $booking['paying_family'] = $booking['bill_family'];
                } else {
                    $booking['paying_contact'] = $outstandings['paying_contact'];
                    $booking['paying_family'] = $outstandings['paying_family'];
                }
                $booking['first_name'] =  self::get_booking_first_name($booking['booking_contact']);
                $booking['multiple_transaction'] = ORM::factory('Kes_Transaction')->booking_has_multiple_transaction($booking['booking_id']);
            }
        }

        return $booking;
    }

	public static function get_contact_canceled_bookings($contact_id)
	{
		return DB::select(
			array('transaction.id', 'transaction_id'), 'transaction.booking_id',
			's.schedule_id', array('schedule.name','schedule_title')
		)
			->from(array('plugin_bookings_transactions',       'transaction'))
			->join(array('plugin_ib_educate_bookings',         'booking'    ))->on('transaction.booking_id', '=', 'booking.booking_id')
            ->join(array('plugin_ib_educate_booking_has_schedules','s'      ))->on('booking.booking_id',     '=', 's.booking_id')
			->join(array('plugin_bookings_transactions_types', 'type'       ))->on('transaction.type',       '=', 'type.id')
			->join(array('plugin_courses_schedules',           'schedule'   ))->on('s.schedule_id',          '=', 'schedule.id')
			->where('transaction.deleted', '=', 0)
			->where('booking.contact_id',  '=', $contact_id)
			->where('type.type',           '=', 'Journal Cancel Booking')
			->execute()
			->as_array()
		;
	}

    public static function get_booking_first_name($booking_contact)
    {
        $contact = new Model_Contacts3($booking_contact);
        return $contact->get_first_name();
    }

    public static function get_all_booking_periods($booking_id, $schedule_id = NULL)
    {
		// Notes subquery
		// If these JOINs are applied to the main query, the WHEREs will filter out all records with no notes
		$notes = DB::select('n.note', 'n.link_id')
			->from(array('plugin_contacts3_notes', 'n'))
			->join(array('plugin_contacts3_notes_tables',   'note_table'))->on('n.table_link_id', '=', 'note_table.id')
			->where('n.deleted', '=', 0)
			->and_where('note_table.table', '=', self::BOOKING_ITEMS_TABLE);

        $q = DB::select(
            'event.datetime_start','event.datetime_end',DB::expr('IFNULL(event.fee_amount,schedule.fee_amount) as fee_amount'),
            array('schedule.id','schedule_id'), 'schedule.name', array('schedule.name', 'schedule_name'), 'schedule.fee_per',
            array('course.id', 'course_id'), 'course.title', array('course.title', 'course_title'),
            'booking_item.period_id','booking_item.attending', 'booking_item.seat_row_id', 'booking_item.seat_fee',
            'note.note',
            array('location.name', 'location'),
            array('parent_location.name', 'parent_location'),
            array('zone.name', 'zone_name'),
            array(DB::expr("CONCAT_WS(`student`.`first_name`, `student`.`last_name`)"), 'student')
        )
			->from(array('plugin_courses_schedules_events', 'event'))
			->join(array('plugin_courses_schedules',        'schedule'    ))->on('event.schedule_id',      '=', 'schedule.id')
            ->join(array('plugin_courses_courses',          'course'      ), 'left')->on('course.id',              '=', 'schedule.course_id')
            ->join(array(self::BOOKING_ITEMS_TABLE,         'booking_item'))->on('booking_item.period_id', '=', 'event.id')
			->join(array(self::BOOKING_TABLE,               'booking'     ))->on('booking.booking_id',     '=', 'booking_item.booking_id')
            ->join(array('plugin_ib_educate_booking_has_schedules',  'bhs'))
                ->on('booking.booking_id', '=', 'bhs.booking_id')
                ->on('event.schedule_id', '=', 'bhs.schedule_id')
            ->join(array($notes,                            'note'), 'LEFT')->on('note.link_id',           '=', 'booking_item.booking_item_id')

            ->join(array('plugin_courses_locations',        'location'), 'LEFT')->on('schedule.location_id', '=', 'location.id')
            ->join(array('plugin_courses_locations', 'parent_location'), 'LEFT')->on('location.parent_id',   '=', 'parent_location.id')
            ->join(array('plugin_courses_schedules_have_zones',  'shz'), 'LEFT')->on('shz.row_id',           '=', 'booking_item.seat_row_id')
                                                                                ->on('shz.schedule_id',      '=', 'schedule.id')
            ->join(array('plugin_courses_zones',                'zone'), 'LEFT')->on('shz.zone_id',          '=', 'zone.id')
            ->join(array('plugin_contacts3_contacts',        'student'), 'LEFT')->on('booking.contact_id',   '=', 'student.id')

            ->where('booking.booking_id', '=', $booking_id)
			->and_where('booking_item.delete', '=', 0)
            ->and_where('booking_item.booking_status', '<>', 3)
			->and_where('booking.delete', '=', 0)
            ->and_where('bhs.deleted', '=', 0)
			->order_by('event.datetime_start')
			->group_by('booking_item.period_id');

        if (is_numeric($schedule_id))
        {
            $q->and_where('schedule.id','=',$schedule_id);
        }

        $q = $q->execute()->as_array();

        return $q;
    }

    public function _sql_get_additional_booking_details()
    {
        $result = array();
        $result['outside_of_school'] = $this->get_booking_schedule_details();
        return $result;
    }

    public static function get_contact_balance($contact_id)
    {
        /***
         * Get Sum of Transactions
         * Get Sum of Payments
         * Return Difference
         */

        $transactions_total = DB::select(array(DB::expr('COALESCE(SUM(t1.amount),0)'),'amount'))->from(array(self::TRANSACTIONS_TABLE,'t1'))->join(array(self::BOOKING_TABLE,'t2'),'LEFT')->on('t1.booking_id','=','t2.booking_id')->join(array(self::CONTACTS_TABLE,'t3'),'LEFT')->on('t3.id','=','t2.contact_id')->where('t3.id','=',$contact_id)->and_where('t1.completed','=',0)->execute()->current();

        $payments_total = DB::select(array(DB::expr('COALESCE(SUM(t0.amount),0)'),'amount'))->from(array(self::PAYMENTS_TABLE,'t0'))->join(array(self::TRANSACTIONS_TABLE,'t1'),'LEFT')->on('t0.transaction_id','=','t1.transaction_id')->join(array(self::BOOKING_TABLE,'t2'),'LEFT')->on('t1.booking_id','=','t2.booking_id')->join(array(self::CONTACTS_TABLE,'t3'),'LEFT')->on('t3.id','=','t2.contact_id')->where('t3.id','=',$contact_id)->and_where('t1.completed','=',0)->execute()->current();

        $pre_paid_transactions = DB::select(array(DB::expr('COALESCE(SUM(t1.amount),0)'),'amount'))->from(array(self::TRANSACTIONS_TABLE,'t1'))->join(array(self::BOOKING_TABLE,'t2'),'LEFT')->on('t1.booking_id','=','t2.booking_id')->join(array(self::CONTACTS_TABLE,'t3'),'LEFT')->on('t3.id','=','t2.contact_id')->where('t3.id','=',$contact_id)->and_where('t1.transaction_type','=',1)->and_where('t1.completed','=',0)->execute()->current();

        $payg_transactions = DB::select(array(DB::expr('COALESCE(SUM(t1.amount),0)'),'amount'))->from(array(self::TRANSACTIONS_TABLE,'t1'))->join(array(self::BOOKING_TABLE,'t2'),'LEFT')->on('t1.booking_id','=','t2.booking_id')->join(array(self::CONTACTS_TABLE,'t3'),'LEFT')->on('t3.id','=','t2.contact_id')->where('t3.id','=',$contact_id)->and_where('t1.transaction_type','=',2)->and_where('t1.completed','=',0)->execute()->current();

        $pre_paid_payments = DB::select(array(DB::expr('COALESCE(SUM(t0.amount),0)'),'amount'))->from(array(self::PAYMENTS_TABLE,'t0'))->join(array(self::TRANSACTIONS_TABLE,'t1'),'LEFT')->on('t0.transaction_id','=','t1.transaction_id')->join(array(self::BOOKING_TABLE,'t2'),'LEFT')->on('t1.booking_id','=','t2.booking_id')->join(array(self::CONTACTS_TABLE,'t3'),'LEFT')->on('t3.id','=','t2.contact_id')->where('t3.id','=',$contact_id)->and_where('t1.transaction_type','=',1)->and_where('t1.completed','=',0)->execute()->current();

        $payg_payments = DB::select(array(DB::expr('COALESCE(SUM(t0.amount),0)'),'amount'))->from(array(self::PAYMENTS_TABLE,'t0'))->join(array(self::TRANSACTIONS_TABLE,'t1'),'LEFT')->on('t0.transaction_id','=','t1.transaction_id')->join(array(self::BOOKING_TABLE,'t2'),'LEFT')->on('t1.booking_id','=','t2.booking_id')->join(array(self::CONTACTS_TABLE,'t3'),'LEFT')->on('t3.id','=','t2.contact_id')->where('t3.id','=',$contact_id)->and_where('t1.transaction_type','=',2)->and_where('t1.completed','=',0)->execute()->current();

        return array('total_transactions' => $transactions_total,'total_payments' => $payments_total,'pre-paid_transactions' => $pre_paid_transactions,'pre-paid_payments' => $pre_paid_payments,'payg_transactions' => $payg_transactions,'payg_payments' => $payg_payments);
    }

    public static function get_booking_locations($contact_id)
    {
        $q = DB::select('t1.id','t1.parent_id')
            ->from(array('plugin_courses_locations','t1'))
            ->join(array('plugin_courses_schedules','t2'),'LEFT')->on('t2.location_id','=','t1.id')
            ->join(array('plugin_courses_schedules_events','t3'),'LEFT')->on('t2.id','=','t3.schedule_id')
            ->join(array(self::BOOKING_ITEMS_TABLE,'t4'),'LEFT')->on('t4.period_id','=','t3.id')
            ->join(array(self::BOOKING_TABLE,'t5'),'LEFT')->on('t5.booking_id','=','t4.booking_id')
            ->join(array(self::CONTACTS_TABLE,'t6'),'LEFT')->on('t6.id','=','t5.contact_id')
            ->where('t6.id','=',$contact_id)->group_by('t1.id')
            ->execute()->as_array();

        $locations = array();

        foreach($q as $key=>$value)
        {
            if(is_null($value['parent_id']))
            {
                $r = DB::select('name')->from('plugin_courses_locations')->where('id','=',$value['id'])->execute()->current();
                $q[$key]['name'] = $r['name'];
                if(in_array($r['name'],$locations))
                {
                    unset($q[$key]);
                }
                else
                {
                    $locations[] = $r['name'];
                }
            }
            else
            {
                $r = DB::select('name')->from('plugin_courses_locations')->where('id','=',$value['parent_id'])->execute()->current();
                $q[$key]['name'] = $r['name'];
                if(in_array($r['name'],$locations))
                {
                    unset($q[$key]);
                }
                else
                {
                    $locations[] = $r['name'];
                }
            }
        }

        return $q;
    }

    public static function get_all_bookings()
    {
        // $q = DB::select('t1.booking_id',array(DB::expr('CONCAT(t2.first_name," ",t2.last_name)'),'student'),'t1.created_date')->from(array(self::BOOKING_TABLE,'t1'))->join(array(self::CONTACTS_TABLE,'t2'),'LEFT')->on('t1.contact_id','=','t2.id')->where('t1.delete','=',0)->execute()->as_array();

        // Need a location ID and category ID to open the timetable by default
        $q = DB::select(
                'booking.booking_id',
                array(
                    DB::expr('CONCAT(contact.first_name," ",contact.last_name)'),'student'),
                'booking.created_date','booking.modified_date',
                'course.category_id',
                'schedule.location_id',
                'schedule.name', 'course.title','course.year_id',
                array('location.parent_id','parent_location_id'),
                array('location.name','location_name'),
                'schedule.start_date',
                'item.seat_row_id',
                'item.seat_fee'
            )
            ->from(array(self::BOOKING_TABLE,               'booking'))
            ->join(array(self::CONTACTS_TABLE,              'contact' ),'LEFT')->on('booking.contact_id',   '=', 'contact.id')
            ->join(array(self::BOOKING_ITEMS_TABLE,         'item'    ),'LEFT')->on('item.booking_id',      '=', 'booking.booking_id')
            ->join(array('plugin_courses_schedules_events', 'event'   ),'LEFT')->on('item.period_id',       '=', 'event.id')
            ->join(array('plugin_courses_schedules',        'schedule'),'LEFT')->on('event.schedule_id',    '=', 'schedule.id')
            ->join(array('plugin_courses_courses',          'course'  ),'LEFT')->on('schedule.course_id',   '=', 'course.id')
            ->join(array('plugin_courses_locations',        'location'),'LEFT')->on('schedule.location_id', '=', 'location.id')
            ->where('booking.delete','=',0)
            ->group_by('booking.booking_id')
            ->order_by('booking.modified_date', 'desc')
            ->order_by('event.datetime_start', 'desc')
            ->execute()->as_array();

        foreach($q as $key=>$booking)
        {
            $q[$key]['outstanding'] = ORM::factory('Kes_Transaction')->get_booking_transaction($booking['booking_id']);
        }

        return $q;
    }

        public static function get_bookings($where = array())
        {
            $q = DB::select('t1.datetime_start', 't1.datetime_end', array('t2.id', 'schedule_id'), 't2.name',
                't3.title', 't4.period_id', 't4.seat_row_id', 't4.seat_fee')
                ->from(array('plugin_courses_schedules_events', 't1'))
                ->join(array('plugin_courses_schedules', 't2'))->on('t1.schedule_id', '=', 't2.id')
                ->join(array('plugin_courses_courses', 't3'))->on('t3.id', '=', 't2.course_id')
                ->join(array(self::BOOKING_ITEMS_TABLE, 't4'))->on('t4.period_id', '=', 't1.id')
                ->join(array(self::BOOKING_TABLE, 't5'))->on('t5.booking_id', '=', 't4.booking_id');
            foreach ($where as $where_item) {
                $q->where($where_item['column'], $where_item['op'], $where_item['value']);
            }
            $return = $q->and_where('t4.delete', '=', 0)
                ->and_where('t5.delete', '=', 0)->execute()->as_array();
            return $return;
        }

    public static function get_all_rooms()
    {
        return DB::select('id','name')->from('plugin_courses_locations')
            ->where('location_type_id','=',2)
            ->where('delete','=',0)
            ->execute()->as_array();
    }

    public static function check_schedule_capacity($args = [])
    {
        $schedule_id  = isset($args['schedule_id']) ? $args['schedule_id'] : null;
        $timeslot_id  = isset($args['timeslot_id']) ? $args['timeslot_id'] : null;
        $quantity  = isset($args['quantity']) ? $args['quantity'] : 1;
        $warn_percent = Settings::instance()->get('course_booking_room_capacity_warn_percent');
        $result       = [
            'error'       => false,
            'booking_qty' => 0,
            'capacity'    => 0,
            'full'        => false,
            'overbooked'  => false,
            'remaining'   => $quantity,
            'suggestions' => null,
            'warn'        => false
        ];

        if ($timeslot_id) {
            $timeslot = Model_ScheduleEvent::get($timeslot_id);

            if (empty($timeslot) || empty($timeslot['id'])) {
                $result['error'] = __('No timeslot found');
                return $result;
            }

            $schedule_id = $timeslot['schedule_id'];
        }

        $schedule = Model_Schedules::get_schedule($schedule_id);

        if (empty($schedule) || empty($schedule['id'])) {
            $result['error'] = __('No schedule found');
            return $result;
        }

        $result['schedule'] = $schedule;
        $bookings = Model_KES_Bookings::search(array(
            'timeslot_id'    => array($timeslot_id),
            'schedule_id'    => $schedule_id,
            'booking_status' => array(Model_KES_Bookings::INPROGRESS, Model_KES_Bookings::CONFIRMED)
        ));
        $booking_count = 0;
        if (!empty($bookings))  {
            foreach($bookings as $booking) {
                $delegates_list = self::get_delegates($booking['booking_id']);
                if (!empty($delegates_list)) {
                    $booking_count += count($delegates_list);
                } else {
                    $booking_count++;
                }

            }
        }
        $qty = $booking_count + $quantity;

        $result['booking_qty']  = $qty;
        $result['capacity']     = $schedule['max_capacity'];

        if ($schedule['max_capacity'] > 0) {
            $result['full']         = ($qty > $schedule['max_capacity']);
            $result['remaining']    = ($booking_count < $schedule['max_capacity']) ? $schedule['max_capacity'] - $booking_count : 0;
            $result['overbooked']   = ($qty > $schedule['max_capacity']);

            if ($result['full']) {
                $result['suggestions'] = array();
                $result['warn'] = true;
            }
            if ($warn_percent > 0 && ($warn_percent / 100) <= $qty / $schedule['max_capacity']) {
                $search_params = array(
                    'course_id' => array($schedule['course_id']),
                    'building_id' => array($schedule['building_id']),
                );

                if (!empty($timeslot)) {
                    $search_params['after'] = date('Y-m-d 00:00:00', strtotime($timeslot['datetime_start']));
                    $search_params['before'] = date('Y-m-d 23:59:59', strtotime($timeslot['datetime_start']));
                }

                $suggestions = Model_Schedules::search($search_params);


                foreach ($suggestions as $i => $suggestion) {
                    if ($suggestion['id'] == $schedule['id']) {
                        unset($result['suggestions'][$i]);
                    } else {
                        $bsearch_parameters = array('schedule_id' => array($suggestion['id']));

                        if (!empty($timeslot)) {
                            $bsearch_parameters['after'] = date('Y-m-d 00:00:00', strtotime($timeslot['datetime_start']));
                            $bsearch_parameters['before'] = date('Y-m-d 23:59:59', strtotime($timeslot['datetime_start']));
                        }

                        $suggestions[$i]['booked'] = count(Model_KES_Bookings::search($bsearch_parameters));
                    }
                }

                $result['suggestions'] = array_values($suggestions);
                $result['warn'] = true;
            }
        }

        return $result;
    }

    public static function get_available_places($period_id = NULL)
    {
        $q = DB::select(array(DB::expr('COALESCE(COUNT(t4.period_id))'),'booked_places'),'t1.capacity','t2.max_capacity','t1.id')
            ->from(array('plugin_courses_locations','t1'))
            ->join(array('plugin_courses_schedules','t2'),'LEFT')->on('t2.location_id','=','t1.id')
            ->join(array('plugin_courses_schedules_events','t3'),'LEFT')->on('t3.schedule_id','=','t2.id')
            ->join(array(self::BOOKING_ITEMS_TABLE,'t4'),'LEFT')->on('t4.period_id','=','t3.id')
            ->join(array(self::BOOKING_TABLE,'t5'),'LEFT')->on('t5.booking_id','=','t4.booking_id')
            ->where('t4.period_id','=',$period_id)
            ->and_where('t4.delete','=',0)
            ->and_where('t5.delete','=',0)->and_where('t5.booking_status','=',2)
            ->execute()->current();

        $places_available = 0;
        $capacity = 0;

        if(is_null($q['max_capacity']))
        {
            $capacity = is_null($q['capacity']) ? 0 : intval($q['capacity']);
        }
        else
        {
            if($q['max_capacity'] <= $q['booked_places'])
            {
                //Get run off schedule room (if it exists)
                $r = DB::select(array(DB::expr('COALESCE(COUNT(t4.period_id))'),'booked_places'));
            }
            else
            {
                $capacity = intval($q['max_capacity']);
            }
        }

        $places_available = $capacity - intval($q['booked_places']);
    }

    public static function check_schedule_for_booked_periods($schedule_id)
    {
        //get capacity
        $r = DB::select('max_capacity')->from('plugin_courses_schedules')->where('id','=',$schedule_id)->execute()->current();

        $s = DB::select('t1.id')->from(array('plugin_courses_schedules_events','t1'))->join(array('plugin_courses_schedules','t2'),'LEFT')->on('t1.schedule_id','=','t2.id')->execute()->as_array();

        foreach($s as $key=>$period)
        {
            $t = DB::select(array(DB::expr('COUNT(t1.booking_item_id)')))->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE,'t1'))->join(array('plugin_courses_schedules_events','t2'),'LEFT')
            ->on('t2.id','=','t1.period_id')->join(array(),'LEFT');
        }

        //get all periods (count)

        //get amount used

        $q = DB::select(array(DB::expr('COUNT(t1.id')))->from(array('plugin_courses_schedules_events','t1'))->join(array('plugin_courses_schedules','t2'))->on('t2.id','=','t1.schedule_id')
        ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE,'t3'),'LEFT')->on('','','');
    }

    /**
     * Check if booking an event would result in a scheduling conflict for a contact
     *
     * @param   $contact_id    int    ID of the contact, whose schedule is being checked
     * @param   $event_id      int    ID of the event being considered for booking
     * @return  array
     */
    public static function check_for_booking_conflict($contact_id, $event_id, $args = array())
    {
        $timeslots             = array();
        $new_timeslot          = Model_Schedules::get_event_details($event_id);
        $overlapping_timeslots = array();

        $return = array(
            'has_overlap'        => null,
            'overlapping_events' => array(),
            'has_permission'     => true,
        );

        // Security: Make sure the user has permission to view details from the contact they're trying to book for
        // This is to stop this function being abused to see booking details for other people
        // If no contact ID is specified, there is no need to perform this check
        if ($contact_id && ! empty($args['check_permission']))
        {
            $user              = Auth::instance()->get_user();
            $family_member_ids = Model_Contacts3::get_all_family_members_ids_for_guardian_by_user($user['id']);

            if ( ! in_array($contact_id, $family_member_ids))
            {
                $return['has_permission'] = false;

                // Prematurely exit the function, if the user has no permission to continue
                return $return;
            }
        }


        // Get timeslots of existing bookings
        if ($contact_id) {
            $timeslots = array_merge($timeslots, self::get_all_booked_periods($contact_id));
        }

        // Get timeslots of items in the cart
        $cart = Session::instance()->get('ibcart');
        if ( ! empty($args['check_cart'])) {
            if (isset($cart['booking']) && ! $cart['booking_id']) {
                foreach ($cart['booking'] as $cart_timeslot_ids) {
                    if (is_numeric($cart_timeslot_ids)) {
                        $timeslots[] = Model_Schedules::get_event_details($cart_timeslot_ids);
                    }
                    else {
                        foreach ($cart_timeslot_ids as $timeslot_id => $timeslot_data) {
                            is_numeric($timeslot_id) ? $timeslots[] = Model_Schedules::get_event_details($timeslot_id) : null;
                        }
                    }
                }
            }
        }

        // Check each timeslot to see if it overlaps with the one being considered for booking
        foreach ($timeslots as $timeslot)
        {
            if ( ! empty($timeslot['id']))
            {
                $timeslot['name'] = isset($timeslot['name]']) ? $timeslot['name'] : $timeslot['schedule'];

                $after_start_time = (strtotime($timeslot['datetime_start']) <= strtotime($new_timeslot['datetime_end']  ));
                $before_end_time  = (strtotime($timeslot['datetime_end'])   >= strtotime($new_timeslot['datetime_start']));

                if ($after_start_time && $before_end_time) {
                    $overlapping_timeslots[] = $timeslot;
                }
            }
        }

        $return['has_conflict']       = (count($overlapping_timeslots) > 0);
        $return['overlapping_events'] = $overlapping_timeslots;

        return $return;
    }

    public static function get_label_text($label_id)
    {
        $q = DB::select('title')->from(self::BOOKING_LABELS)->where('label_id','=',$label_id)->execute()->as_array();
        return count($q) > 0 ? $q[0]['title'] : '';
    }

    /**
     * Get the schedule name course title and year for the booking id
     * @param $study_year
     * @param $schedule_id
     * @return mixed
     */
    public static function get_schedule_details($study_year,$schedule_id)
    {
        $y = DB::select(array('year','study_year'),array('id','study_year_id'))
            ->from('plugin_courses_years')
            ->where('id','=',$study_year)
            ->execute()->as_array();
        if ($y == NULL)
        {
            $y[0]=array('study_year'=>NULL,'study_year_id'=>NULL);
        }
        $q = DB::select('t1.id','t1.name','t2.title',DB::expr('GROUP_CONCAT(t3.year) as `year`'),'has_years.year_id')
            ->from(array('plugin_courses_schedules','t1'))
            ->join(array('plugin_courses_courses','t2'))
            ->on('t1.course_id','=','t2.id')
            ->join(array('plugin_courses_courses_has_years', 'has_years'), 'left')->on('t2.id', '=', 'has_years.course_id')
            ->join(array('plugin_courses_years','t3'))
            ->on('has_years.year_id','=','t3.id')
            ->where('t1.id','=',$schedule_id)
            ->group_by('t1.id')
            ->execute()->as_array();
		if ($q == NULL)
		{
			$q[0]=array('id'=>NULL,'name'=>NULL,'title'=>NULL,'year'=>NULL,'year_id'=>NULL);
		}

        $answer = array_merge($y[0],$q[0]);
        return $answer;
    }

	/** Get all notes relating to booking items, for a specified contact
	 * @param $contact_id   int  the ID of the contact
	 * @return array    an array of notes
	 */
	public static function get_contact_booking_notes($contact_id)
	{
		$booking_items = DB::select('booking_item.booking_item_id', 'booking_item.booking_id')
			->from(array(self::BOOKING_ITEMS_TABLE,   'booking_item'))
			->join(array(self::BOOKING_TABLE,         'booking'     ))->on('booking_item.booking_id', '=', 'booking.booking_id')
			->join(array('plugin_contacts3_contacts', 'contact'     ))->on('booking.contact_id',      '=', 'contact.id')
			->where('booking.delete', '=', 0)
			->where('booking_item.delete', '=', 0)
			->where('booking.contact_id', '=', $contact_id)
			->execute()
			->as_array();

        $bookings = DB::select('booking.booking_id')
            ->from(array(self::BOOKING_TABLE,         'booking'     ))
            ->join(array('plugin_contacts3_contacts', 'contact'     ))->on('booking.contact_id',      '=', 'contact.id')
            ->where('booking.delete', '=', 0)
            ->where('booking.contact_id', '=', $contact_id)
            ->execute()
            ->as_array();

        $return = array();

        if (count($bookings) > 0) {
            $booking_ids = array();
            foreach ($bookings as $booking)
            {
                $booking_ids[] = $booking['booking_id'];
            }
            $booking_ids = array_unique($booking_ids);

            $where_clauses   = array();
            $where_clauses[] = array('table.table', '=', self::BOOKING_TABLE);
            $where_clauses[] = array('note.link_id', 'IN', $booking_ids);

            $return1 = Model_EducateNotes::get_all_notes($where_clauses);

            $return = array_merge($return, $return1);
        }

		if (count($booking_items) > 0) {
			$booking_item_ids = array();
			foreach ($booking_items as $booking_item)
			{
				$booking_item_ids[] = $booking_item['booking_item_id'];
			}

            $where_clauses   = array();
            $where_clauses[] = array('table.table', '=', self::BOOKING_ITEMS_TABLE);
            $where_clauses[] = array('note.link_id', 'IN', $booking_item_ids);
            $return2 = Model_EducateNotes::get_all_notes($where_clauses);

            $return = array_merge($return, $return2);
		}

		return $return;
	}
	
	public static function get_for_datatable($filters)
	{
        $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';
        $link_contact_to_booking = (bool) Settings::instance()->get('link_contacts_to_bookings');

		$output    = array();

        $searchColumns   = array();
        $searchColumns[] = 't4.booking_id'; // booking_id
        $searchColumns[] = DB::expr('CONCAT_WS(" ", t5.first_name,t5.last_name)'); // student
        $searchColumns[] = (Settings::instance()->get('cms_platform') === 'training_company') ? "organ_contact.first_name" : 'student_year.year' ; // student year or the contact's organisation
        $searchColumns[] = 't1.name'; // schedule_title
        $searchColumns[] = DB::expr('IFNULL(t6.title, t6_2.title)');  // course_title
        $searchColumns[] = DB::expr('IFNULL(t10.type, t10_2.type)'); // type
        // status - select Application status if it is enquiry, otherwise interview if it's not empty, then booking status
        $searchColumns[] = DB::expr("if(`applications`.`application_status` = 'Enquiry' and `applications`.`status_id` = 1, applications.application_status,
                              if(`applications`.`interview_status` IS NULL or `applications`.`interview_status` = '',
                                 t11.title, `applications`.`interview_status`))");
        $searchColumns[] = 'min_dates.start_date'; // start_date
        $searchColumns[] = 'location.name'; // location_name

		$sortColumns   = array();
		$sortColumns[] = 't4.booking_id'; // booking_id
		$sortColumns[] = DB::expr('CONCAT_WS(" ", t5.first_name,t5.last_name)'); // student
        $sortColumns[] = (Settings::instance()->get('cms_platform') === 'training_company') ? "organ_contact.first_name" : 'student_year.year' ; // student year or the contact's organisation
        $sortColumns[] = 't1.name'; // schedule_title
		$sortColumns[] = DB::expr('IFNULL(t6.title, t6_2.title)');  // course_title
		$sortColumns[] = DB::expr('IFNULL(t10.type, t10_2.type)'); // type
		$sortColumns[] = DB::expr("if(`applications`.`application_status` = 'Enquiry' and `applications`.`status_id` = 1, applications.application_status,
                              if(`applications`.`interview_status` IS NULL or `applications`.`interview_status` = '',
                                 t11.title, `applications`.`interview_status`))"); // status
		$sortColumns[] = 'min_dates.start_date'; // start_date
		$sortColumns[] = 'location.name'; // location_name
        $sortColumns[] = 'status'; // status
		$sortColumns[] = 't4.modified_date';

        $min_dates = DB::select(DB::expr("min(e.datetime_start) as start_date"), 'e.schedule_id', 'b.booking_id')
            ->from(array(self::BOOKING_TABLE, 'b'))
            ->join(array(self::BOOKING_ITEMS_TABLE, 'i'), 'inner')->on('b.booking_id', '=', 'i.booking_id')
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'e'), 'inner')->on('i.period_id', '=', 'e.id')
            ->where('e.delete', '=', 0)
            ->and_where('i.delete', '=', 0)
            ->and_where('b.delete', '=', 0)
            ->group_by('b.booking_id')
            ->group_by('e.schedule_id');

        $display_columns = array(
            DB::expr('SQL_CALC_FOUND_ROWS t4.booking_id'),
            array(DB::expr('CONCAT_WS(" ", t5.first_name,t5.last_name)'), 'student'));
        $display_columns[] = (Settings::instance()->get('cms_platform') === 'training_company') ?
            ['organ_contact.first_name', 'organ_contact_name'] : ['student_year.year', 'student_year'];
        $display_columns = array_merge($display_columns,  array(array(DB::expr('GROUP_CONCAT(DISTINCT t1.name SEPARATOR "\n")'), 'schedule_title'),
            array(DB::expr('GROUP_CONCAT(DISTINCT IFNULL(t6_2.title, t6.title) SEPARATOR "\n")'), 'course_title'),
            array(DB::expr('GROUP_CONCAT(DISTINCT IFNULL(t10_2.type, t10.type) SEPARATOR "\n")'), 'type'),
            array('min_dates.start_date', 'start_date'),
            array(DB::expr("if(`applications`.`application_status` = 'Enquiry' and `applications`.`status_id` = 1, applications.application_status,
                    if(`applications`.`interview_status` IS NULL or `applications`.`interview_status` = '',
                    t11.title, `applications`.`interview_status`))"),'status'),
            array('location.name', 'location_name'),
            array('t4.booking_status', 'status_id'),
            array('t4.modified_date', 'last_modified'),
            //array($outstand,'outstanding'),
            array('t5.id', 'contact_id'),
            'applications.interview_status'));
        
        $agent_query = DB::query(Database::SELECT, "
            SELECT CONCAT_WS(' ', c.first_name, c.last_name) FROM plugin_ib_educate_bookings_has_linked_contacts as `lc`
            INNER JOIN plugin_contacts3_contacts as `c` on c.id = lc.contact_id
            INNER JOIN plugin_contacts3_contact_type as `t` on c.type = t.contact_type_id
            where t.label = 'Agent' and lc.booking_id = t4.booking_id");
        
        $host_family_query = DB::query(Database::SELECT, "
            SELECT CONCAT_WS(' ', c.first_name, c.last_name) FROM plugin_ib_educate_bookings_has_linked_contacts as `lc`
            INNER JOIN plugin_contacts3_contacts as `c` on c.id = lc.contact_id
            INNER JOIN plugin_contacts3_contact_type as `t` on c.type = t.contact_type_id
            where t.label = 'Host Family' and lc.booking_id = t4.booking_id");

        $coordinator_query = DB::query(Database::SELECT, "
            SELECT CONCAT_WS(' ', c.first_name, c.last_name) FROM plugin_ib_educate_bookings_has_linked_contacts as `lc`
            INNER JOIN plugin_contacts3_contacts as `c` on c.id = lc.contact_id
            INNER JOIN plugin_contacts3_contact_type as `t` on c.type = t.contact_type_id
            where t.label = 'Coordinator' and lc.booking_id = t4.booking_id");

        if ($link_contact_to_booking) {
            $display_columns[] = array($agent_query, 'agent');
            $display_columns[] = array($host_family_query, 'host_family');
            $display_columns[] = array($coordinator_query, 'coordinator');
            $sortColumns[] = 'agent';
            $sortColumns[] = 'host_family';
            $sortColumns[] = 'coordinator';
            $searchColumns[] = $agent_query;
            $searchColumns[] = $host_family_query;
            $searchColumns[] = $coordinator_query;
        }

        // Make Outstanding Payments unsearchable but make Coordinator searchable
        $filters['bSearchable_7'] = "false";
        $filters['bSearchable_9'] = "true";

        $searchColumns[] = 't4.modified_date';
        $sortColumns[] = 't4.modified_date';

		$q = DB::select_array(
            $display_columns
            )
                ->from(array(self::BOOKING_TABLE,              't4' ))
				->join(array(self::BOOKING_SCHEDULES,          't12'),'LEFT')
                    ->on('t12.booking_id','=','t4.booking_id')->on('t12.deleted', '=', DB::expr(0))
                ->join(array('plugin_courses_schedules',       't1' ),'LEFT')->on('t12.schedule_id','=','t1.id')
                ->join(array($min_dates, 'min_dates' ), 'LEFT')->on('t4.booking_id',  '=','min_dates.booking_id')
                ->join(array('plugin_contacts3_contacts',      't5' ),'LEFT')->on('t4.contact_id', '=','t5.id')
                ->join(array(self::BOOKING_COURSES,            'has_ftcourses' ),'LEFT')->on('t4.booking_id',   '=','has_ftcourses.booking_id')
                ->join(array('plugin_courses_courses',         't6' ),'LEFT')->on('t1.course_id', '=', 't6.id')
				->join(array('plugin_courses_types',           't10'),'LEFT')->on('t6.type_id',    '=','t10.id')
				->join(array('plugin_courses_years',           't7' ),'LEFT')->on('t6.year_id',    '=','t7.id')
                ->join(array('plugin_courses_years',  'student_year'),'LEFT')->on('t5.year_id',    '=','student_year.id')

                ->join(array('plugin_courses_courses',         't6_2' ),'LEFT')->on('has_ftcourses.course_id', '=', 't6_2.id')
                ->join(array('plugin_courses_types',           't10_2'),'LEFT')->on('t6_2.type_id',    '=','t10_2.id')
                ->join(array('plugin_courses_years',           't7_2' ),'LEFT')->on('t6_2.year_id',    '=','t7_2.id')

                ->join(array(self::BOOKING_STATUS_TABLE,       't11'),'LEFT')->on('t4.booking_status', '=','t11.status_id')
				->join(array('plugin_courses_locations',  'location'),'LEFT')->on('t1.location_id',    '=','location.id')
                ->join(array(self::BOOKING_APPLICATIONS, 'applications'), 'LEFT')->on('t4.booking_id', '=', 'applications.booking_id')

                ->join(array('engine_users','modified_by'),'LEFT')
                    ->on('t4.modified_by', '=', 'modified_by.id');
		if(Settings::instance()->get('cms_platform') === 'training_company') {
            $q = $q->join(array(Model_Contacts3::CONTACT_RELATIONS_TABLE, 'organ_relat'), 'left')->on('t5.id', '=', 'organ_relat.child_id');
            $q = $q->join(array(Model_Contacts3::CONTACTS_TABLE, 'organ_contact'), 'left')->on('organ_relat.parent_id', '=', 'organ_contact.id');
        }
		$q = $q->where('t4.delete', '=', 0);
                /*->and_where_open()
                    ->or_where('t4.booking_status', '=', 3)
                    ->or_where_open()
                        ->or_where('t4.booking_status', '<>', 3)
                        ->and_where('t12.deleted', '=', 0)
                    ->or_where_close()
                ->and_where_close();*/

			$q->group_by('t4.booking_id');
		$sort = '';
		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$q->and_where_open();
			for ($i = 0; $i < count($searchColumns); $i++)
			{
                if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $searchColumns[$i] != '')
				{
					$q->or_where($searchColumns[$i],'like','%'.$filters['sSearch'].'%');
				}
			}
			$q->and_where_close();
		}
		// Individual column search
		for ($i = 0; $i < count($searchColumns); $i++)
		{
			if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
			{
				$q->and_where($searchColumns[$i],'like','%'.$filters['sSearch_'.$i].'%');
			}
		}

		// Limit. Only show the number of records for this paginated page
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$q->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$q->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order
		if (isset($filters['iSortCol_0']) && is_numeric($filters['iSortCol_0']))
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
               if ($sortColumns[$filters['iSortCol_'.$i]] != '')
				{
					$q->order_by($sortColumns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$q->order_by('t4.modified_date', 'desc');

		$results = $q->execute()->as_array();

		$output['iTotalDisplayRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
		$output['iTotalRecords']        = count($results); // displayed results
		$output['aaData']               = array();
        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['booking_id'];
        }
        $plan_outstanding = "select
          plan.id, plan.transaction_id, SUM(planpayment.total) as outstanding_installments, sum(planpayment.interest + planpayment.penalty) as installment_fees
        from plugin_bookings_transactions_payment_plans plan
          inner join plugin_bookings_transactions_payment_plans_has_payment planpayment ON plan.id = planpayment.payment_plan_id and planpayment.deleted = 0
        where planpayment.payment_id is null
        group by plan.id";

        $outstandings = DB::query(Database::SELECT,"SELECT t.booking_id, SUM(t.total) + SUM(IFNULL(plan_outstanding.installment_fees, 0)) 
                         - (COALESCE(
                (
                    SELECT
                        SUM(`pay`.`amount`)
                    FROM `plugin_bookings_transactions_payments` AS `pay`
                    LEFT JOIN `plugin_bookings_transactions` AS `tran` ON `tran`.`id` = `pay`.`transaction_id`
                    LEFT JOIN `plugin_bookings_transactions_payments_statuses` AS `status` ON(`pay`.`status` = `status`.`id`)
                    WHERE tran.deleted = 0 AND pay.deleted = 0 AND tran.booking_id = t.booking_id
                    AND `status`.credit = 1
                ),
                0
            )- COALESCE(
                (
                    SELECT SUM(`pay`.`amount`)
                    FROM `plugin_bookings_transactions_payments` AS `pay`
                    LEFT JOIN `plugin_bookings_transactions` AS `tran` ON `tran`.`id` = `pay`.`transaction_id`
                    LEFT JOIN `plugin_bookings_transactions_payments_statuses` AS `status` ON(`pay`.`status` = `status`.`id`)
                    WHERE tran.deleted = 0 AND pay.deleted = 0 AND tran.booking_id = t.booking_id
                    AND `status`.credit = 0
                ),
                0
            ) ) - COALESCE(
                (SELECT SUM(`cancelled_transactions`.`total`) as `cancelled_amount` 
                FROM plugin_bookings_transactions as `cancelled_transactions`
                WHERE  `cancelled_transactions`.booking_id = t.booking_id 
                  AND `cancelled_transactions`.deleted = 0 
                  AND `cancelled_transactions`.`type` = 4) ,0) as outstanding
            FROM plugin_bookings_transactions AS t
              left join ($plan_outstanding) plan_outstanding ON t.id = plan_outstanding.transaction_id
            WHERE t.deleted = 0 AND t.type IN (1,2,7) " . (count($ids) > 0 ? " AND t.booking_id in (" . implode(',', $ids) . ")" : '') .
            " GROUP BY t.booking_id"
        )->execute()->as_array();

		foreach ($results as $result)
		{
            $outstanding = 0;
            foreach ($outstandings as $boutstanding) {
                if ($boutstanding['booking_id'] == $result['booking_id']) {
                    $outstanding = $boutstanding['outstanding'];
                }
            }
			$link_url = URL::site().'admin/bookings/add_edit_booking/' . $result['booking_id'];

            $result['schedule_title'] = '<p>'.preg_replace('/\n/', '</p><p>', $result['schedule_title']).'</p>';
            $result['course_title']   = '<p>'.preg_replace('/\n/', '</p><p>', $result['course_title']).'</p>';
            $result['type']           = '<p>'.preg_replace('/\n/', '</p><p>', $result['type']).'</p>';

            $row = array();
			$row[] = $result['booking_id'];
			$row[] = '<a href="'.$link_url.'" class="view_link" data-contact_id="'.$result['contact_id'].'">'.$result['student'].'</a>';
            $row[] = (Settings::instance()->get('cms_platform') === 'training_company') ? $result['organ_contact_name'] : $result['student_year'];
			$row[] = $result['schedule_title'];
			$row[] = $result['course_title'];
			$row[] = $result['type'];
			$row[] = $result['interview_status'] ? 'Interview ' . $result['interview_status'] : $result['status'];
			$row[] = $result['start_date'];
			$row[] = $result['location_name'];
			$row[] = ($outstanding ? '&euro;' . $outstanding : '');
            if ($link_contact_to_booking) {
                $row[] = (is_null($result['agent']) ? '' : $result['agent']);
                $row[] = (is_null($result['host_family']) ? '' : $result['host_family']);
                $row[] = (is_null($result['coordinator']) ? '' : $result['coordinator']);
            }
            $row[] = count(self::get_delegates($result['booking_id']));
            $row[] = IbHelpers::relative_time_with_tooltip(strtotime($result['last_modified']));
           $output['aaData'][] = $row;
		}
		$output['sEcho'] = intval($filters['sEcho']);

		return json_encode($output);
	}

    public static $timeslot_attendance_status_enum = array('Present','Late','Early Departures','Temporary Absence','Plan', 'Absent');
    public static $timeslot_account_status_enum = array('Paid');
    public static function rollcallUpdate($bookings, $update_accounts = true, $update_attendance = true)
    {
        Database::instance()->begin();
        try {
            $user = Auth::instance()->get_user();
            $userId = $user['id'];
            $schedules = array();
            if (count($bookings) > 0) {
                $messaging = new Model_Messaging();

                foreach ($bookings as $booking) {
                    $rollcall = DB::select('*')
                        ->from(self::BOOKING_ROLLCALL_TABLE)
                        ->where('id', '=', $booking['id'])
                        ->execute()
                        ->current();
                    $bookingData = new Model_KES_Bookings($rollcall['booking_id']);
                    $bookingData->set_booking_details($bookingData->get_booking_items(true));
                    $bookingData = $bookingData->get_instance();
                    $ft_application = DB::select('*')
                        ->from(array(self::BOOKING_APPLICATIONS, 'applications'))
                            ->join(array(self::BOOKING_COURSES, 'has_courses'), 'inner')
                                ->on('applications.booking_id', '=', 'has_courses.booking_id')
                            ->join(array(self::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                                ->on('applications.booking_id', '=', 'items.booking_id')
                            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                                ->on('items.period_id', '=', 'timeslots.id')
                            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                                ->on('timeslots.schedule_id', '=', 'schedules.id')
                                ->on('has_courses.course_id', '=', 'schedules.course_id')
                        ->where('applications.booking_id', '=', $booking['id'])
                        ->and_where('applications.delegate_id', '=', $rollcall['delegate_id'])
                        ->execute()
                        ->current();
                    $contact = new Model_Contacts3($rollcall['delegate_id']);
                    $primaryContact = null;
                    try {
                        $primaryContact = new Model_Contacts3($contact->get_primary_contact());
                    } catch(Exception $exc) {
                        $primaryContact = $contact;
                    }

                    $preferences = $primaryContact->get_preferences();
                    $sendAccountNotification = false;
                    $sendAttendanceNotification = false;
                    $notificationDriver = 'email';
                    foreach ($preferences as $preference){
                        if ($preference['preference_id'] == 2 && $preference['value'] == 1){
                            $sendAccountNotification = true;
                        }
                        if ($preference['preference_id'] == 3 && $preference['value'] == 1){
                            $sendAttendanceNotification = true;
                        }
                        if ($preference['preference_id'] == 15 && $preference['value'] == 1){
                            $notificationDriver = 'sms';
                        }
                    }
                    $updateAccount = 0;
                    $notifyAccountBalance = false;

                    foreach ($booking['items'] as $bookingItem) {
                        $notifyAttendance = null;
                        $notificationTemplateName = '';
                        $updateTransaction = 0;
                        $bookingItemData = DB::select(
                            'bi.*',
                            'se.schedule_id',
                            'se.datetime_start',
                            'se.datetime_end',
                            'se.monitored',
                            'sc.payment_type',
                            'sc.is_fee_required',
                            DB::expr('IF(se.fee_amount, se.fee_amount, sc.fee_amount) AS `fee_amount`'),
                            array('sc.name', 'schedule_name'),
                            'sc.payg_period',
                            'sc.payg_apply_fees_when_absent',
                            'sc.payg_absent_fee'
                        )
                            ->from(array(self::BOOKING_ROLLCALL_TABLE, 'bi'))
                            ->join(array('plugin_courses_schedules_events', 'se'), 'inner')->on('bi.timeslot_id', '=',
                                'se.id')
                            ->join(array('plugin_courses_schedules', 'sc'), 'inner')->on('se.schedule_id', '=', 'sc.id')
                            ->where('bi.id', '=', $bookingItem['id'])
                            ->execute()
                            ->current();
                        if ($bookingItemData['timeslot_status_alerted'] != '') {
                            $bookingItemData['timeslot_status_alerted'] = explode(',', $bookingItemData['timeslot_status_alerted']);
                        } else {
                            $bookingItemData['timeslot_status_alerted'] = array();
                        }
                        if ($bookingItemData['payg_period'] == 'week') {
                            $weekstart = date('Y-m-d',
                                strtotime("monday this week", strtotime($bookingItemData['datetime_start'])));
                            $bookingItemData['fee_amount'] = DB::select(DB::expr('SUM(IF(e.fee_amount, e.fee_amount, s.fee_amount)) AS `total`'))
                                ->from(array('plugin_courses_schedules_events', 'e'))
                                ->join(array('plugin_courses_schedules', 's'), 'inner')->on('e.schedule_id', '=',
                                    's.id')
                                ->where('e.schedule_id', '=', $bookingItemData['schedule_id'])
                                ->and_where('e.delete', '=', 0)
                                ->and_where('e.datetime_start', '>=', $weekstart)
                                ->and_where('e.datetime_start', '<',
                                    DB::expr('date_add("' . $weekstart . '", interval 1 week)'))
                                ->execute()
                                ->get('total');
                        }
                        if ($bookingItemData['payg_period'] == 'month') {
                            $monthstart = date('Y-m-01', strtotime($bookingItemData['datetime_start']));
                            $bookingItemData['fee_amount'] = DB::select('s.fee_amount')
                                ->from(array('plugin_courses_schedules_events', 'e'))
                                ->join(array('plugin_courses_schedules', 's'), 'inner')->on('e.schedule_id', '=',
                                    's.id')
                                ->where('e.schedule_id', '=', $bookingItemData['schedule_id'])
                                ->and_where('e.delete', '=', 0)
                                ->and_where('e.datetime_start', '>=', $monthstart)
                                ->and_where('e.datetime_start', '<',
                                    DB::expr('date_add("' . $monthstart . '", interval 1 month)'))
                                ->execute()
                                ->get('fee_amount');
                        }

                        $currentTimeslotStatus = $bookingItemData['attendance_status'] !== null ? explode(',', $bookingItemData['attendance_status']) : array();
                        $newTimeslotStatus = $bookingItem['status'] != '' ? explode(',', $bookingItem['status']) : array('Absent');
                        $timeslot_status_alerted = $bookingItemData['timeslot_status_alerted'];
                        if ($bookingItemData['monitored'] == 1) {
                            if (in_array('Absent', $newTimeslotStatus) && !in_array('Absent', $bookingItemData['timeslot_status_alerted'])
                            ) { // absent
                                $alert = true;
                                if ($ft_application) {
                                    $already_alerted = DB::select('*')
                                        ->from(array(self::BOOKING_ROLLCALL_TABLE, 'items'))
                                            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                                                ->on('items.period_id', '=', 'timeslots.id')
                                        ->where('items.booking_id', '=', $bookingItemData['booking_id'])
                                        ->and_where('timeslots.datetime_start', '>=', date('Y-m-d', strtotime($bookingItemData['datetime_start'])))
                                        ->and_where('timeslots.datetime_start', '<', date('Y-m-d', strtotime($bookingItemData['datetime_start']) + 86400))
                                        ->and_where(DB::expr('find_in_set(items.timeslot_status_alerted, "Absent")'), '<>', 0)
                                        ->execute()
                                        ->current();
                                    if ($already_alerted) {
                                        $alert = false;
                                    }
                                }
                                if ($alert) {
                                    $notifyAttendance = 'Absent';
                                    $timeslot_status_alerted[] = 'Absent';
                                    $notificationTemplateName = 'rollcall-absent-sms';
                                }
                            }
                            if (in_array('Late', $newTimeslotStatus) && !in_array('Late', $bookingItemData['timeslot_status_alerted'])) {
                                $notifyAttendance = 'Late';
                                $timeslot_status_alerted[] = 'Late';
                                $notificationTemplateName = 'rollcall-signed-late-sms';
                            }
                            if (in_array('Early Departures', $newTimeslotStatus) && !in_array('Early Departures', $bookingItemData['timeslot_status_alerted'])
                            ) {
                                $notifyAttendance = 'Early Departures';
                                $timeslot_status_alerted[] = 'Early Departures';
                                $notificationTemplateName = 'rollcall-left-early-sms';
                            }
                        }

                        $new_status = array();
                        if ($update_attendance == false) {
                            foreach ($currentTimeslotStatus as $cts) {
                                if ($cts != '' && in_array($cts, self::$timeslot_attendance_status_enum)) {
                                    $new_status[] = $cts;
                                }
                            }
                        }
                        if ($update_accounts == false) {
                            foreach ($currentTimeslotStatus as $cts) {
                                if ($cts != '' && in_array($cts, self::$timeslot_account_status_enum)) {
                                    $new_status[] = $cts;
                                }
                            }
                        }
                        if ($update_attendance == true) {
                            foreach ($newTimeslotStatus as $nts) {
                                if ($nts != '' && in_array($nts, self::$timeslot_attendance_status_enum)) {
                                    $new_status[] = $nts;
                                }
                            }
                        }
                        if ($update_accounts == true) {
                            foreach ($newTimeslotStatus as $nts) {
                                if ($nts != '' && in_array($nts, self::$timeslot_account_status_enum)) {
                                    $new_status[] = $nts;
                                }
                            }
                        }
                        $new_status = implode(',', $new_status);
                        $update_params = array(
                            'arrived' => @$bookingItem['arrived'],
                            'left' => @$bookingItem['left'],
                            'status_updated' => date::now(),
                            'timeslot_status_alerted' => implode(',', $timeslot_status_alerted)
                        );
                        if (array_key_exists('attendance_status', $bookingItem)) {
                            $update_params['attendance_status'] = $bookingItem['attendance_status'];
                        }
                        if (array_key_exists('finance_status', $bookingItem)) {
                            $update_params['finance_status'] = $bookingItem['finance_status'];
                        }
                        if (array_key_exists('planned_arrival', $bookingItem)) {
                            $update_params['planned_arrival'] = $bookingItem['planned_arrival']
                                ?
                                date('Y-m-d', strtotime($bookingItemData['datetime_end'])) . ' ' . $bookingItem['planned_arrival']
                                :
                                null;
                        }
                        if (array_key_exists('planned_leave', $bookingItem)) {
                            $update_params['planned_leave'] = $bookingItem['planned_leave']
                                ?
                                date('Y-m-d', strtotime($bookingItemData['datetime_end'])) . ' ' . $bookingItem['planned_leave']
                                :
                                null;
                        }

                        if (array_key_exists('temporary_absences', $bookingItem)) {
                            $update_params['temporary_absences'] = $bookingItem['temporary_absences'];
                        }
                        DB::update(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE)
                            ->set($update_params)
                            ->where('id', '=', $rollcall['id'])
                            ->execute();

                        if ($booking['note'] != '') {
                            $note = new Model_EducateNotes();
                            $note->getByLink($rollcall['id'], 3);
                            $note->load(array(
                                'link_id' => $rollcall['id'],
                                'table_link_id' => 3,
                                'note' => $booking['note']
                            ));
                            $note->save('nots');
                        }

                        if ($update_accounts) {


                        if ($bookingItemData['payment_type'] == 2) { // 1 prepay, 2 payg
                            $mtx = new Model_Kes_Transaction();
                            if ($bookingItemData['payg_period'] == 'month') {
                                $payg_period = date('Y-m', strtotime($bookingItemData['datetime_start']));
                            } else {
                                if ($bookingItemData['payg_period'] == 'week') {
                                    $payg_period = date('Y-m-d',
                                        strtotime("monday this week", strtotime($bookingItemData['datetime_start'])));
                                } else {
                                    $payg_period = $bookingItemData['datetime_start'];
                                }
                            }
                            $eTransaction = $mtx->get_transaction(
                                null,
                                $booking['id'],
                                $bookingItemData['schedule_id'],
                                $payg_period,
                                array(2)
                            );

                            // if there is an existing transaction and the slot is marked absent then cancel the existing transaction(roll call run for a second time)
                            if ($eTransaction && in_array('Absent', $newTimeslotStatus) &&
                                $bookingItemData['payg_apply_fees_when_absent'] == 0 && $bookingItem['apply_absent_fee'] == null) {
                                // absent
                                $transactionData = new Model_Kes_Transaction();
                                $jTransaction = $transactionData->create_transaction(array(
                                    'booking_id' => $booking['id'],
                                    'amount' => $bookingItemData['fee_amount'],
                                    'total' => $bookingItemData['fee_amount'],
                                    'type' => 6,
                                    'discount' => 0,
                                    'schedule' => array($bookingItemData['schedule_id'])
                                ),
                                    $bookingData['bill_payer'] ? $bookingData['bill_payer'] : $bookingData['contact_id']
                                );

                                if ($jTransaction && @$eTransaction['id']) {
                                    $q = DB::insert('plugin_bookings_transactions_journal',
                                        array('journaled_transaction_id', 'transaction_id'))
                                        ->values(array($eTransaction['id'], $jTransaction['transaction']))->execute();
                                }
                                $notifyAccountBalance = true;
                                $updateAccount = -1;
                            } else if($eTransaction && is_numeric($bookingItem['apply_absent_fee'])) {
                                // Cancel existing transaciton, and create a new one
                                $transactionData = new Model_Kes_Transaction();
                                $jTransaction = $transactionData->create_transaction(array(
                                    'booking_id' => $booking['id'],
                                    'amount' => $bookingItem['apply_absent_fee'],
                                    'total' => $bookingItem['apply_absent_fee'],
                                    'type' => 2,
                                    'discount' => 0,
                                    'schedule' => array($bookingItemData['schedule_id'])
                                ),
                                    $bookingData['bill_payer'] ? $bookingData['bill_payer'] : $bookingData['contact_id']
                                );

                                if ($jTransaction && @$eTransaction['id']) {
                                    $q = DB::insert('plugin_bookings_transactions_journal',
                                        array('journaled_transaction_id', 'transaction_id'))
                                        ->values(array($eTransaction['id'], $jTransaction['transaction']))->execute();
                                }
                                $notifyAccountBalance = true;
                                $updateAccount = -1;
                            }

                            // if there is no existing transaction and timeslot is marked as paid then create a transactio
                            if (!$eTransaction && (!in_array('Absent', $newTimeslotStatus) || @$bookingItem['apply_absent_fee'] > 0)
                                && isset($bookingItemData['fee_amount']) && is_numeric($bookingItemData['fee_amount']))
                            {
                                $tx_amount = $bookingItemData['fee_amount'];
                                if (in_array('Absent', $newTimeslotStatus) || $bookingItem['status'] == 'Paid' || $bookingItem['status'] == 'Unpaid') {
                                    if (isset($bookingItem['apply_absent_fee']) && $bookingItem['apply_absent_fee'] > 0) {
                                        $tx_amount = $bookingItem['apply_absent_fee'];
                                    }
                                }
                                $notifyAccountBalance = true;
                                $updateAccount = 1;
                                $transactionData = new Model_Kes_Transaction();
                                $txresult = $transactionData->create_transaction(array(
                                    'booking_id' => $booking['id'],
                                    'amount' => $tx_amount,
                                    'total' => $tx_amount,
                                    'type' => 2,
                                    'discount' => 0,
                                    'schedule' => array(
                                        array(
                                            'schedule_id' => $bookingItemData['schedule_id'],
                                            'event_id' => $bookingItemData['period_id'],
                                            'payg_period' => $payg_period
                                        )
                                    )
                                ),
                                    $bookingData['bill_payer'] ? $bookingData['bill_payer'] : $bookingData['contact_id']
                                );

                                // if make a payment for the entered amount in the roll call if paid
                                if (in_array('Paid', $newTimeslotStatus)) {
                                    $amount = min((float)$booking['amount'], $bookingItemData['fee_amount']);
                                    if ($booking['amount'] > 0) {
                                        $payment = new Model_Kes_Payment();
                                        $payment->save_payment(array(
                                            'transaction_id' => $txresult['transaction'],
                                            'type' => 'cash',
                                            'amount' => $amount,
                                            'currency' => 'EUR',
                                            'status' => 2,
                                            'created' => date('Y-m-d H:i:s'),
                                            'updated' => date('Y-m-d H:i:s'),
                                            'note' => 'created from rollcall at ' . date('Y-m-d H:i:s'),
                                            'deleted' => 0,
                                            'created_by' => $userId,
                                            'modified_by' => $userId
                                        ));
                                        $booking['amount'] -= $amount;
                                    }
                                }
                            }
                        } else {
                            $mtx = new Model_Kes_Transaction();
                            $eTransaction = $mtx->get_transaction(
                                null,
                                $booking['id'],
                                $bookingItemData['schedule_id']
                            );
                            if ($eTransaction) {
                                if ($eTransaction['outstanding'] > 0) {
                                    if (in_array('Paid', $newTimeslotStatus)) {
                                        $amount = min((float)$booking['amount'], $eTransaction['outstanding']);
                                        if ($booking['amount'] > 0) {
                                            $payment = new Model_Kes_Payment();
                                            $payment->save_payment(array(
                                                'transaction_id' => $eTransaction['id'],
                                                'type' => 'cash',
                                                'amount' => $amount,
                                                'currency' => 'EUR',
                                                'status' => 2,
                                                'created' => date('Y-m-d H:i:s'),
                                                'updated' => date('Y-m-d H:i:s'),
                                                'note' => 'created from rollcall at ' . date('Y-m-d H:i:s'),
                                                'deleted' => 0,
                                                'created_by' => $userId,
                                                'modified_by' => $userId
                                            ));
                                            $booking['amount'] -= $amount;
                                        }
                                    }
                                }
                            }

                        }

                            $notifyAccountBalance = false;
                            $sendAccountNotification = false;
                        }

                        if ($update_attendance == false) {
                            $notifyAttendance = null;
                            $sendAttendanceNotification = false;
                        }
                        if ($bookingItemData['monitored'] == 1 && $notifyAttendance != null && $sendAttendanceNotification == true) {
                            try {
                                $messaging->send_template($notificationTemplateName,
                                    null,
                                    date('Y-m-d H:i:s'),
                                    array(
                                        array(
                                            'target_type' => 'CMS_CONTACT3',
                                            'target' => $primaryContact->get_id()
                                        )
                                    ),
                                    array(
                                        'contactName' => $primaryContact->get_contact_name(),
                                        'student_name' => $contact->get_contact_name(),
                                        'schedule' => $bookingItemData['schedule_name'],
                                        'date' => date('d/M', strtotime($bookingItemData['datetime_start'])),
                                        'time' => date('H:i',
                                                strtotime($bookingItemData['datetime_start'])) . ' - ' . date('H:i',
                                                strtotime($bookingItemData['datetime_end'])),
                                        'status' => $notifyAttendance
                                    ));
                            } catch (Exception $exc) {

                            }
                        }
                    }

                    $bookingData = new Model_KES_Bookings($rollcall['booking_id']);
                    $bookingData->set_booking_details($bookingData->get_booking_items(true));
                    $bookingCostAfter = $bookingData->booking_cost + $bookingData->payg_cost;
                    if ($notifyAccountBalance == true && $sendAccountNotification == true) {
                        $notificationTemplateName = 'rollcall-booking-outstanding-' . $notificationDriver;
                        try {
                            $result = $messaging->send_template($notificationTemplateName,
                                null,
                                date('Y-m-d H:i:s'),
                                array(
                                    array(
                                        'target_type' => 'CMS_CONTACT3',
                                        'target' => $primaryContact->get_id()
                                    )
                                ),
                                array(
                                    'contactName' => $primaryContact->get_contact_name(),
                                    'scheduleTitle' => $bookingItemData['schedule_name'],
                                    'bookingId' => $booking['id'],
                                    'outstandingAmount' => ORM::factory('Kes_Transaction')->calculate_outstanding_balance($booking['id'])
                                ));
                        } catch (Exception $exc) {

                        }
                    }
                    /*
                     * if($bookingCostBefore != $bookingCostAfter && false){
                        $transaction = ORM::factory('Kes_Transaction');
                        $transaction->save_history(null, array('booking_id' => $booking['id'], 'total' => $bookingCostAfter));
                        if (class_exists('Model_Document')) {
                            $doc_helper = new Model_Docarrayhelper();
                            $data = $doc_helper->booking_alteration($booking['id']);
                            $doc = ORM::factory('Document')->auto_generate_document($data, $direct = 0, $pdf = TRUE);
                        }
                    }*/
                }
            }
            Database::instance()->commit();
            return true;
        } catch(Exception $e) {
            Database::instance()->rollback();
            throw $e;
        }
    }

    public static function get_attendance($params = array())
    {
        $select = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS schedules.id'),
            array('schedules.name', 'schedule'),
            'schedules.course_id',
            array('courses.title', 'course'),
            array('locations.name', 'location'),
            array('plocations.name', 'plocation'),
            'events.datetime_start',
            'rollcalls.booking_item_id',
            'rollcalls.planned_to_attend',
            'rollcalls.attendance_status',
            'rollcalls.attendance_status',
            'rollcalls.temporary_absences',
            'rollcalls.planned_arrival',
            'rollcalls.planned_leave',
            'rollcalls.arrived',
            'rollcalls.left',
            'notes.note'
        )
            ->distinct(true)
            ->from(array(self::BOOKING_ROLLCALL_TABLE, 'rollcalls'))
            ->join(array('plugin_courses_schedules_events', 'events'), 'inner')
                ->on('rollcalls.timeslot_id', '=', 'events.id')
                ->on('rollcalls.delete', '=', DB::expr(0))
                ->on('events.delete', '=', DB::expr(0))
            ->join(array(self::BOOKING_TABLE, 'bookings'), 'inner')
                ->on('rollcalls.booking_id', '=', 'bookings.booking_id')
            ->join(array('plugin_courses_schedules', 'schedules'), 'inner')
                ->on('events.schedule_id', '=', 'schedules.id')
                ->on('schedules.delete', '=', DB::expr(0))
            ->join(array('plugin_courses_courses', 'courses'), 'inner')
                ->on('schedules.course_id', '=', 'courses.id')
            ->join(array('plugin_courses_locations', 'locations'), 'left')
                ->on('schedules.location_id', '=', 'locations.id')
            ->join(array('plugin_courses_locations', 'plocations'), 'left')
                ->on('locations.parent_id', '=', 'plocations.id')
            ->join(array('plugin_contacts3_notes', 'notes'), 'left')
                ->on('rollcalls.booking_item_id', '=', 'notes.link_id')
                ->on('notes.table_link_id', '=', DB::expr(3))// 3 => booking item
            ->join(['plugin_ib_educate_bookings_has_delegates', 'delegates'], 'left')
                ->on('delegates.booking_id', '=', 'bookings.booking_id')
                ->on('delegates.contact_id', '=', 'rollcalls.delegate_id')
            ->where('bookings.booking_status', '<>', 3); // not cancelled

        if (!isset($args['timeslot_status']) || $args['timeslot_status'] != 'all') {
            //$select->and_where('rollcalls.attendance_status', 'in', ['Present', 'Late', 'Early Departures', 'Temporary Absence']); // roll call executed
            $select->and_where('rollcalls.attendance_status', 'is not', null); // roll call executed
        }

        if (!empty($params['contact_id'])) {
            // Get timeslots where the contact is a delegate or is the lead booker on a non-group booking
            $operator = is_array($params['contact_id']) ? 'in' : '=';
            $select->and_where('delegates.contact_id', $operator, $params['contact_id']);
        }

        if (@$params['after']) {
            $select->and_where('events.datetime_start', '>=', $params['after']);
        }

        if (@$params['before']) {
            $select->and_where('events.datetime_start', '<=', $params['before']);
        }

        if (@$params['offset']) {
            $select->offset($params['offset']);
        }

        if (@$params['limit']) {
            $select->limit($params['limit']);
        }

        if (@$params['order']) {
            $select->order_by($params['order']);
        } else {
            $select->order_by('events.datetime_start', 'desc');
        }

        $result = array();
        $result['timeslots'] = $select->execute()->as_array();
        $result['total_timeslots'] = DB::select(DB::expr('FOUND_ROWS() AS `total`'))->execute()->get('total');
        $result['attended'] = 0;
        $result['not_attended'] = 0;
        foreach ($result['timeslots'] as $i => $timeslot) {
            if ($timeslot['planned_to_attend'] == 1) {
                ++$result['attended'];
            } else {
                ++$result['not_attended'];
            }
            if ($timeslot['temporary_absences']) {
                $result['timeslots'][$i]['temporary_absences'] = json_decode($timeslot['temporary_absences'], true);
            }
        }
        return $result;
    }

    public static function get_booking_by_bookingschedule($booking_schedule_id)
    {
        $result = false;
        $booking_schedule = DB::select('*')
            ->from('plugin_ib_educate_booking_has_schedules')
            ->where('id', '=', $booking_schedule_id)
            ->execute()
            ->current();
        if ($booking_schedule) {
            $result = $booking_schedule;
            $result['booking'] = DB::select('*')
                ->from('plugin_ib_educate_bookings')
                ->where('booking_id', '=', $booking_schedule['booking_id'])
                ->execute()
                ->current();
            $result['schedule'] = DB::select('*')
                ->from('plugin_courses_schedules')
                ->where('id', '=', $booking_schedule['schedule_id'])
                ->execute()
                ->current();
            $result['transaction'] = DB::select('tx.*')
                ->from(array('plugin_bookings_transactions_has_schedule', 'has'))
                ->join(array('plugin_bookings_transactions', 'tx'), 'inner')
                ->on('has.transaction_id', '=', 'tx.id')
                ->where('has.schedule_id', '=', $booking_schedule['schedule_id'])
                ->and_where('tx.booking_id', '=', $booking_schedule['booking_id'])
                ->execute()
                ->current();
        }

        return $result;
    }
    
    // Get the contact's booking information that is linked to the contact
    public static function get_bookings_contacts_linked_to_contact($contact_id) {
        $q = DB::select(array('c.*'), 'b.booking_id')
            ->from(array(self::BOOKING_LINKED_CONTACTS, 'lb'))
            ->join(array('plugin_ib_educate_bookings', 'b'), 'inner')->on('lb.booking_id', '=', 'b.booking_id')
            ->join(array('plugin_contacts3_contacts', 'c'), 'inner')->on('c.id', '=', 'b.contact_id')
            ->where('lb.contact_id', '=', $contact_id)
            ->order_by("b.created_date", 'desc');
        return $q->execute()->as_array();
    }
    
    public static function get_linked_booking_contacts($booking_id, $contact_type_id = false) {
	    $q = DB::select(array('c.id', 'id'), DB::expr("CONCAT(c.first_name, ' ', c.last_name) AS `name`"))
            ->from(array('plugin_ib_educate_bookings_has_linked_contacts', 'lb'))
            ->join(array('plugin_contacts3_contacts', 'c'), 'inner')->on('c.id', '=', 'lb.contact_id')
            ->where('lb.booking_id', '=', $booking_id);
	    if($contact_type_id != false) {
	        $q = $q->where('c.type','=', $contact_type_id);
        }
	   return $q->execute()->current();
    }
    public static function sort_by_fee_asc($item1, $item2)
    {

        if ($item1['fee'] < $item2['fee']) {
            return -1;
        } else if ($item1['fee'] > $item2['fee']) {
            return 1;
        } else {
            return 0;
        }
    }

    // moved from model_schedules to here
    // todo: reduce the amount of function arguments
    public static function get_order_data($bookings, $apply_discounts, $client_id = null, $booking_id = null, $payment_method = null, $courses = null, $new_student_params = array(), $number_of_delegates = null)
    {
        $result = array();
        $has_payg = false;
        if (!empty($bookings) AND !is_null($bookings)) {
            $schedules_cache = DB::select('id', 'name', array('fee_amount', 'fee'), 'is_fee_required', 'fee_per', 'payment_type', 'trial_timeslot_free_booking', 'charge_per_delegate')
                ->from('plugin_courses_schedules')
                ->where('id', 'in', array_keys($bookings))
                ->execute()
                ->as_array();
            $schedules_q = array();
            foreach ($schedules_cache as $schedule_cached) {
                $schedules_q[$schedule_cached['id']] = array($schedule_cached);
            }
            $timeslots_cache = DB::select('*')
                ->from('plugin_courses_schedules_events')
                ->where('schedule_id', 'in', array_keys($bookings))
                //->and_where('datetime_start', '>=', date('Y-m-d H:i:s'))
                ->execute()
                ->as_array();
            $timeslots_q = array();
            foreach ($timeslots_cache as $timeslot_cached) {
                $timeslots_q[$timeslot_cached['id']] = $timeslot_cached;
            }

            $charge_per_delegate = false;

            foreach ($bookings as $schedule_id => $schedule) {
                $schedule_orm = new Model_Course_Schedule($schedule_id);
                $charge_per_delegate = $charge_per_delegate || $schedule_orm->charge_per_delegate == 1;
                $schedule_event_count = count($schedule);
                /*$q = DB::select('id', 'name', array('fee_amount', 'fee'), 'is_fee_required', 'fee_per', 'payment_type')
                    ->from('plugin_courses_schedules')
                    ->where('id', '=', $schedule_id)
                    ->execute()
                    ->as_array();*/
                $booked_previosly = null;
                if ($client_id) {
                    $booked_previosly = Model_KES_Bookings::check_existing_booking($client_id, $schedule_id);
                    if ($booked_previosly) {
                        $schedules_q[$schedule_id][0]['trial_timeslot_free_booking'] = 0;
                    }
                }

                $q = $schedules_q[$schedule_id];
                if (!isset($q[0])) {
                    continue;
                }

                $periods_attending = array();
                $timeslots = array();
                $next_payment = null;
                $paymentoption_id = null;

                foreach ($schedule as $period_id => $period) {

                    if ($paymentoption_id == null && @$period['paymentoption_id']) {
                        $paymentoption_id = $period['paymentoption_id'];
                    }
                    if ($period['attending'] == 1) {
                        $periods_attending[] = $period_id;
                    }

                    /*$timeslot = DB::select('*')
                        ->from('plugin_courses_schedules_events')
                        ->where('id', '=', $period_id)
                        //->and_where('datetime_start', '>=', date('Y-m-d H:i:s'))
                        ->execute()
                        ->current();*/
                    $timeslot = @$timeslots_q[$period_id];

                    if ($timeslot) {
                        if ($timeslot['fee_amount'] == null) {
                            $timeslot['fee_amount'] = $q[0]['fee'];
                        }
                        if ($schedule_event_count == 1 && $q[0]['trial_timeslot_free_booking'] == 1) {
                            $timeslot['fee_amount'] = 0;
                        }
                        $timeslot['end_date'] = $timeslot['datetime_end'];
                        $timeslots[$timeslot['id']] = $timeslot;

                        if ($period['attending'] == 1 && ($next_payment == null || $next_payment['date'] < time())) {
                            $next_payment = array(
                                'date' => strtotime($timeslot['datetime_start']),
                                'fee' => $timeslot['fee_amount']
                            );
                        }
                    }
                }
                if ($q[0]['is_fee_required'] == 1) {
                    if ($schedule_event_count == 1 && $q[0]['trial_timeslot_free_booking'] == 1) {
                        $fee = 0;
                    } else if ($q[0]['fee_per'] == 'Timeslot') {
                        $fee = $q[0]['fee'] ?? 0;
                        if ($q[0]['fee_per'] == 'Timeslot') {
                            $fee = 0;
                        }
                        foreach ($schedule as $period) {
                            if ($period['attending'] == 1 || $q[0]['payment_type'] == 1) { // 1 => prepay, 2 => payg
                                $fee += $timeslot['fee_amount'];
                            }
                        }
                    } else if ($q[0]['fee_per'] == 'Day') {
                        $fee = Model_Schedules::calculate_fee_for_schedule($schedule_id, array_keys($schedule));
                    } else {
                        $fee = $q[0]['fee'];
                    }
                } else {
                    $fee = 0;
                }

                $item = array(
                    'name' => $q[0]['name'],
                    'fee' => (float)$fee,
                    'remaining_fee' => (float)$fee,
                    'fee_per' => $q[0]['fee_per'],
                    'paymentoption_id' => $paymentoption_id,
                    'id' => $schedule_id,
                    'prepay' => true,
                    'next_payment' => null,
                    'periods_attending' => $periods_attending,
                    'type' => 'schedule',
                    'timeslot_details' => $timeslots,
                    'details' => Model_Schedules::get_one_for_details($schedule_id),
                    'number_of_delegates' => isset($period['number_of_delegates']) ? @$period['number_of_delegates'] : $number_of_delegates,
                    'trial_timeslot_free_booking' => $q[0]['trial_timeslot_free_booking']
                );
                switch ($q[0]['payment_type']) {
                    case 1:
                        $item['prepay'] = true;
                        break;

                    case 2:
                        $item['prepay'] = false;
                        $has_payg = true;
                        $next_payment['date'] = date('d M Y', $next_payment['date']);
                        $item['next_payment'] = $next_payment;
                        break;

                    case 3:
                        $item['prepay'] = true;
                        break;
                }
                $item['discount'] = 0;
                $item['discounts'] = array();
                $item['total'] = $item['fee'] - $item['discount'] >= 0 ? $item['fee'] - $item['discount'] : 0;

                if (@$period['number_of_delegates'] > 1 && $charge_per_delegate) {
                    $item['total'] *= @$period['number_of_delegates'];
                }
                $item['existing_booking'] = null;
                if ($booking_id) {
                    $item['existing_booking'] = DB::select('id')
                        ->from(array(self::BOOKING_SCHEDULES, 'bs'))
                        ->where('booking_id', '=', $booking_id)
                        ->and_where('schedule_id', '=', $schedule_id)
                        ->and_where('deleted', '=', 0)
                        ->and_where('publish', '=', 1)
                        ->execute()
                        ->get('id');

                    $txg = new Model_Kes_Transaction();
                    $stx = $txg->get_schedule_transactions($booking_id, $schedule_id);
                    $btx = $txg->get_booking_transaction($booking_id);

                    $item['outstanding'] = 0;
                    foreach($stx as $tx) {
                        $item['outstanding'] += $tx['outstanding'];
                    }
                    if (!empty($btx['outstanding'])) {
                        $item['outstanding'] = $btx['outstanding'];
                    }
                }
                $result[] = $item;
            }
        }

        if (is_array($courses)) {
            foreach ($courses as $course) {
                $paymentoption = null;
                if (@$course['paymentoption_id']) {
                    $paymentoption = DB::select('*')
                        ->from(Model_Courses::TABLE_HAS_PAYMENTOPTIONS)
                        ->where('id', '=', $course['paymentoption_id'])
                        ->execute()
                        ->current();
                }
                $course = DB::select('*')
                    ->from(Model_Courses::TABLE_COURSES)
                    ->where('id', '=', $course['course_id'])
                    ->execute()
                    ->current();

                $course_item = array(
                    'name' => $course['title'],
                    'fee' => $course['fulltime_price'],
                    'remaining_fee' => $course['fulltime_price'],
                    'fee_per' => '',
                    'id' => $course['id'],
                    'paymentoption_id' => $paymentoption ? $paymentoption['id'] : null,
                    'prepay' => null,
                    'discount' => 0,
                    'discounts' => array(),
                    'total' => 0,
                    'type' => 'course',
                    'payg_fee' => 0,
                    'cc_fee' => 0,
                    'sms_fee' => 0,
                    'booking_fees' => 0,
                    'payment_method' => null,
                );
                $course_item['existing_booking'] = null;
                if ($booking_id) {
                    $item['existing_booking'] = DB::select('id')
                        ->from(array(self::BOOKING_COURSES, 'bc'))
                        ->where('booking_id', '=', $booking_id)
                        ->and_where('course_id', '=', $course['id'])
                        ->and_where('deleted', '=', 0)
                        ->execute()
                        ->get('id');
                    $txg = new Model_Kes_Transaction();
                    $stx = $txg->get_course_transactions($booking_id, $course['id']);
                    $course_item['outstanding'] = 0;
                    foreach($stx as $tx) {
                        $course_item['outstanding'] += $tx['outstanding'];
                    }
                }

                $result[] = $course_item;
            }
        }
        usort($result, 'Model_KES_Bookings::sort_by_fee_asc');
        $subtotal_item = array(
            'name' => '',
            'fee' => 0,
            'fee_per' => '',
            'id' => null,
            'prepay' => null,
            'discount' => 0,
            'discounts' => array(),
            'total' => 0,
            'type' => 'subtotal',
            'payg_fee' => (float)Settings::instance()->get('course_payg_booking_fee'),
            'cc_fee' => (float)Settings::instance()->get('course_cc_booking_fee'),
            'sms_fee' => (float)Settings::instance()->get('course_sms_booking_fee'),
            'booking_fees' => 0,
            'payment_method' => null,
        );
        foreach ($result as $schedule_item) {
            $subtotal_item['fee'] += $schedule_item['fee'];
            $subtotal_item['total'] += $schedule_item['total'];
        }
        $subtotal_item['remaining_fee'] = $schedule_item['fee'];
        $charge_per_delegate = $schedule_item['details']['charge_per_delegate'];

        if ($has_payg) {
            $subtotal_item['booking_fees'] += $subtotal_item['payg_fee'];
        }

        if ($payment_method == 'cc') {
            $subtotal_item['booking_fees'] += $subtotal_item['cc_fee'];
        }

        if ($payment_method == 'sms') {
            $subtotal_item['booking_fees'] += $subtotal_item['sms_fee'];
        }

        $result[] = $subtotal_item;
        if ($booking_id && $apply_discounts) {
            foreach ($apply_discounts as $aschedule_id => $adiscounts) {
                foreach($adiscounts as $i => $apply_discount) {
                    if ($apply_discount['amount'] == null) {
                        unset($apply_discounts[$aschedule_id][$i]);
                    }
                }
                $apply_discounts[$aschedule_id] = array_values($apply_discounts[$aschedule_id]);
                if (count($apply_discounts[$aschedule_id]) == 0) {
                    unset($apply_discounts[$aschedule_id]);
                }
            }
        }
        if ($booking_id && !$apply_discounts) {
            $saved_discounts = DB::select('bd.*', 'd.code')
                ->from(array('plugin_ib_educate_bookings_discounts', 'bd'))
                ->join(array('plugin_bookings_discounts', 'd'), 'left')->on('bd.discount_id', '=', 'd.id')
                ->where('booking_id', '=', $booking_id)
                ->execute()
                ->as_array();
            $apply_discounts = array();
            foreach ($saved_discounts as $saved_discount) {
                if (!isset($apply_discounts[$saved_discount['schedule_id']])) {
                    $apply_discounts[$saved_discount['schedule_id']] = array();
                }
                $apply_discounts[$saved_discount['schedule_id']][] = array(
                    'code' => $saved_discount['code'],
                    'id' => $saved_discount['discount_id'] == null ? 'custom' : $saved_discount['discount_id'],
                    'custom' => $saved_discount['discount_id'] == null ? 1 : 0,
                    'amount' => $saved_discount['amount'],
                    'ignore' => $saved_discount['status'] == 'ignored_discount' ? 1 : 0,
                    'memo' => $saved_discount['memo'],
                    'no_check' => true
                );
            }
        }
       /*** APPLY DISCOUNT(S) ***/
        if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {

            $discounts = Model_KES_Discount::get_all_discounts_for_listing(array('publish_on_web' => 1));
            $courses_discounts_apply = Settings::instance()->get('courses_discounts_apply');

            foreach ($result as $i => $item) {
                if ($item['type'] == 'subtotal') {
                    if (isset($apply_discounts['cart'])) {
                        $apply_item_discounts = @$apply_discounts['cart'];
                    }
                    if (isset($apply_discounts[null])) {
                        $apply_item_discounts = $apply_discounts[null];
                    }
                } else {
                    $apply_item_discounts = @$apply_discounts[$item['id']];
                }
                // apply custom discount if exists
                if ($apply_item_discounts) {
                    foreach ($apply_item_discounts as $aid => $apply_item_discount) {
                        if ($apply_item_discount['id'] == 'custom') {
                            $item['discounts'][$apply_item_discount['id']] = array(
                                'id' => 'custom',
                                'amount' => $apply_item_discount['amount'],
                                'title' => 'Custom',
                                'code' => '',
                                'ignore' => 0,
                                'memo' => @$apply_item_discount['memo'],
                                'custom' => 1
                            );
                            $item['discount'] += $apply_item_discount['amount'];
                            $item['total'] = $item['fee'] - $item['discount'] >= 0 ? $item['fee'] - $item['discount'] : 0;
                            $result[$i] = $item;
                        } else {
                            $item['prepay'] = 1;
                            $item['discounts'][$apply_item_discount['id']] = $apply_item_discount;
                        }
                    }
                }


                $min_discount = null;
                $max_discount = null;
                // apply available discounts
                foreach ($discounts as $discount) {
                    if (($discount['apply_to'] == 'Schedule' && $item['type'] == 'subtotal') ||
                        ($discount['apply_to'] == 'Cart' && $item['type'] == 'schedule')) {
                        continue;
                    }
                    $discount_amount = 0;
                    $ignore = 0;
                    $coupon_code = '';
                    $no_check = false;

                    if ($apply_item_discounts)
                        foreach ($apply_item_discounts as $apply_item_discount) {
                            if ($apply_item_discount['id'] == $discount['id']) {
                                if ((int)$apply_item_discount['ignore'] == 1) {
                                    $ignore = 1;
                                }
                                if (@$apply_item_discount['no_check']) {
                                    $no_check = true;
                                }
                                $discount_amount = $apply_item_discount['amount'];
                            }
                            if ($apply_item_discount['code'] != '') {
                                $coupon_code = $apply_item_discount['code'];
                            }
                        }

                    $discount_o = Model_KES_Discount::create($discount['id']);
                    //if (!$discount_o->test_unassigned()) {
                        //continue;
                    //}

                    if ($coupon_code == '' && $discount_o->get_code() != '') {
                        continue;
                    }
                    
                    if ($no_check) {
                        //$discount_amount = $discount_o->calculate_discount_no_check($client_id, $result, $item['id']);
                    } else {
                        if ($discount_o->get_publish() == 1) {
                            $discount_amount = $discount_o->calculate_discount($client_id, $result, $item['id'], $coupon_code, $new_student_params);
                        } else {
                            $discount_amount = 0;
                        }
                    }

                    if (!empty($item['number_of_delegates']) && $item['number_of_delegates'] > 1 && $charge_per_delegate) {
                        if ($discount_amount > $item['fee'] * $item['number_of_delegates']) {
                            $discount_amount = (float)$item['fee'] * $item['number_of_delegates'];
                        }
                    } else {
                        if ($discount_amount > $item['fee']) {
                            $discount_amount = (float)$item['fee'];
                        }
                    }
                    $item = $result[$i];
                    if ($discount_amount > 0 && sizeof($discount_o->failing_conditions) == 0) {
                        if ($item['number_of_delegates'] > 1 && $charge_per_delegate) {
                            $ignore_others = $discount_o->ignore_others() || $discount_amount >= $item['fee'] * $item['number_of_delegates'];

                        } else {
                            $ignore_others = $discount_o->ignore_others() || $discount_amount >= $item['fee'];
                        }

                        if ($courses_discounts_apply == 'Minimum') {
                            if($discount_amount < $min_discount || $min_discount === null) {
                                $min_discount = $discount_amount;
                                $item['discount'] = 0;
                                foreach ($item['discounts'] as $di => $discountx) {
                                    $item['discounts'][$di]['ignore_others'] = false;
                                    $item['discounts'][$di]['amount'] = 0;
                                }
                            } else {
                                $discount_amount = 0;
                            }
                        }

                        if ($courses_discounts_apply == 'Maximum') {
                            if ($discount_amount > $max_discount || $max_discount === null) {
                                $max_discount = $discount_amount;
                                $item['discount'] = 0;
                                foreach ($item['discounts'] as $di => $discountx) {
                                    $item['discounts'][$di]['ignore_others'] = false;
                                    $item['discounts'][$di]['amount'] = 0;
                                }
                            } else {
                                $discount_amount = 0;
                            }
                        }


                        $item['discounts'][$discount['id']] = array(
                            'id' => $discount['id'],
                            'amount' => $discount_amount,
                            'title' => $discount['title'],
                            'summary' => $discount['summary'],
                            'code' => $discount['code'],
                            'ignore' => $ignore,
                            'custom' => 0,
                            'memo' => '',
                            'ignore_others' => $ignore_others,
                            'applied_for_timeslots' => $discount_o->calculated_for_timeslots
                        );
                        if ($ignore == 0) {
                            $item['discount'] += $discount_amount;

                            if (!empty($item['number_of_delegates']) && $item['number_of_delegates'] > 1 && $charge_per_delegate) {
                                $item['total'] = $item['fee'] * $item['number_of_delegates']  - $item['discount'] ;
                            } else {
                                $item['total'] = $item['fee'] - $item['discount'];
                            }
                            $item['total'] = $item['total'] > 0 ?  $item['total'] : 0;
                        }

                        $result[$i] = $item;

                        if ($ignore_others && !$ignore) { // discounts like 100%, or quantity
                            foreach ($item['discounts'] as $di => $discountx) {
                                if (!@$discountx['ignore_others']) {
                                    unset($item['discounts'][$di]);
                                }
                            }
                            $item['discounts'] = array_values($item['discounts']);
                            $item['discount'] = $discount_amount;
                            if (!empty($item['number_of_delegates']) && $item['number_of_delegates'] > 1 && $charge_per_delegate) {
                                $item['total'] = $item['fee'] * $item['number_of_delegates']  - $item['discount'];
                            } else {
                                $item['total'] = $item['fee'] - $item['discount'];
                            }
                            $item['total'] = $item['total'] > 0 ?  $item['total'] : 0;

                            $result[$i] = $item;

                            break;
                        }
                    } else if (sizeof($discount_o->failing_conditions) != 0 && $item['id'] != null) {
                        $item['discounts'][$discount['id']] = array(
                            'id' => $discount['id'],
                            'amount' => $discount_amount,
                            'title' => $discount['title'],
                            'summary' => $discount['summary'],
                            'code' => $discount['code'],
                            'ignore' => $ignore,
                            'custom' => 0,
                            'memo' => '',
                            'remaining_conditions' => $discount_o->failing_conditions,
                            'applied_for_timeslots' => 0
                        );
                        $result[$i] = $item;
                    }
                }
                if (!$booking_id && $item['prepay']) {
                    $item['outstanding'] = $item['total'];
                    $result[$i] = $item;
                }
            }
        }
        return $result;
    }

    public static function check_existing_booking($contact_id, $schedule_id, $timeslot_id = null, $exlude_booking_id = null)
    {
        $select = DB::select('*')
            ->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(self::BOOKING_SCHEDULES, 'has_schedules'), 'inner')->on('bookings.booking_id', '=', 'has_schedules.booking_id')
                ->join(array(self::BOOKING_ITEMS_TABLE, 'items'), 'inner')->on('bookings.booking_id', '=', 'items.booking_id')
            ->where('bookings.booking_status', '<>', 3)
            ->and_where('bookings.delete', '=', 0)
            ->and_where('has_schedules.booking_status', '<>', 3)
            ->and_where('has_schedules.deleted', '=', 0)
            ->and_where('items.booking_status', '<>', 3)
            ->and_where('items.delete', '=', 0)
            ->and_where('has_schedules.schedule_id', '=', $schedule_id)
            ->and_where('bookings.contact_id', (is_array($contact_id) ? 'in' : '='), $contact_id);
        if ($timeslot_id) {
            $select->and_where('items.period_id', '=', $timeslot_id);
        }
        if ($exlude_booking_id) {
            $select->and_where('bookings.booking_id', '<>', $exlude_booking_id);
        }
        $exists = $select->execute()->current();
        return $exists;
    }

    public static function get_next_payment_date($booking_id, $schedule_id)
    {
        // check if there is an existing payment. add one month if exists
        $last_tx = DB::select('*')
            ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                ->join(array(Model_Kes_Transaction::TABLE_HAS_SCHEDULES, 'has_schedules'), 'left')
                    ->on('transactions.id', '=', 'has_schedules.transaction_id')
            ->where('transactions.deleted', '=', 0)
            ->and_where('transactions.type', '=', 2) //payg
            ->and_where('transactions.booking_id', '=', $booking_id)
            ->and_where('has_schedules.schedule_id', '=', $schedule_id)
            ->order_by('transactions.updated', 'desc')
            ->limit(1)
            ->execute()
            ->current();
        
        if ($last_tx) {
            $next_date = date('Y-m-d', strtotime($last_tx['updated'] . " +1month"));
        } else { // display next timeslot date
            $next_timeslot = DB::select('*')
                ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'))
                    ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                        ->on('items.period_id', '=', 'timeslots.id')
                ->where('items.booking_id', '=', $booking_id)
                ->and_where('timeslots.schedule_id', '=', $schedule_id)
                ->and_where('timeslots.delete', '=', 0)
                ->and_where('items.delete', '=', 0)
                ->and_where('items.booking_status', '<>', 3)
                ->and_where('timeslots.datetime_start', '>=', date::today())
                ->order_by('timeslots.datetime_start', 'asc')
                ->execute()
                ->current();
            $next_date = date('Y-m-d', strtotime($next_timeslot['datetime_start']));
        }
        return $next_date;
    }

    public static function cancel_booking_schedule($args)
    {
        $booking_id = $args['booking_id'];
        $schedule_id = $args['schedule_id'];
        $credit = $args['credit'];
        $credit_to_family_id = $args['credit_to_family_id'];
        $note = $args['note'];
        $cancel_transactions = isset($args['cancel_transactions']) ? $args['cancel_transactions'] : false;
        $reason_code = $args['reason_code'];

        try {
            Database::instance()->begin();
            $user = Auth::instance()->get_user();

            $booking = self::get_details($booking_id);

            $contact = new Model_Contacts3($booking['booking_contact']);
            $primaryContact = null;
            try {
                $primaryContact = new Model_Contacts3($contact->get_primary_contact());
            } catch(Exception $exc) {
                $primaryContact = $contact;
            }

            $preferences = $primaryContact->get_preferences();
            $notification = 'course-booking-cancelled-parent-email';
            foreach ($preferences as $preference){
                if ($preference['preference_id'] == 15 && $preference['value'] == 1){
                    $notification = 'course-booking-cancelled-parent-sms';
                }
            }

            $booking_schedule = DB::select('*')
                ->from('plugin_ib_educate_booking_has_schedules')
                ->where('booking_id', '=', $booking_id)
                ->and_where('schedule_id', '=', $schedule_id)
                ->and_where('booking_status', '<>', self::CANCELLED)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->current();

            $schedule = Model_Schedules::get_one_for_details($schedule_id);

            try {
                $messaging = new Model_Messaging();
                $messaging->send_template($notification,
                    null,
                    date('Y-m-d H:i:s'),
                    array(
                        array(
                            'target_type' => 'CMS_CONTACT3',
                            'target' => $primaryContact->get_id()
                        )
                    ),
                    array(
                        'parentName' => $primaryContact->get_contact_name(),
                        'studentName' => $contact->get_contact_name(),
                        'scheduleTitle' => $schedule['schedule'],
                        'bookingId' => $booking_id
                    ));
            } catch (Exception $exc) {

            }
            $result = array('message' => '');

            DB::insert('plugin_contacts3_notes')
                ->values(array(
                    'note' => $note,
                    'link_id' => $booking_id,
                    'table_link_id' => 4,
                    'created_by' => $user['id'],
                    'modified_by' => $user['id'],
                    'date_modified' => date('Y-m-d H:i:s')
                ))
                ->execute();

            DB::query(
                Database::UPDATE,
                'UPDATE
                    ' . Model_KES_Bookings::BOOKING_ITEMS_TABLE . ' i
                        INNER JOIN plugin_courses_schedules_events e ON i.period_id = e.id
                    SET i.booking_status = ' . self::CANCELLED . '
                    WHERE
                      i.booking_id = ' . $booking_id . ' AND
                      e.schedule_id = ' . $schedule_id . ' AND
                      i.timeslot_status is NULL'
            )->execute();

            DB::update('plugin_ib_educate_booking_has_schedules')
                ->set([
                    'booking_status'     => self::CANCELLED,
                    'cancel_reason_code' => $reason_code
                ])
                ->where('schedule_id', '=', $schedule_id)
                ->and_where('booking_id', '=', $booking_id)
                ->and_where('deleted', '=', 0)
                ->execute();
            $id = $contact->get_id();
            $activity = new Model_Activity();
            $activity->set_action('cancel');
            $activity
                ->set_item_type('booking')
                ->set_item_id($booking_id)
                ->set_user_id($user['id'])
                ->set_scope_id($booking['booking_contact'])
                ->save();

            $booking_still_has_schedule = DB::select('id')
                ->from('plugin_ib_educate_booking_has_schedules')
                ->where('booking_id', '=', $booking_id)
                ->and_where('booking_status', '<>', self::CANCELLED)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->get('id');
            if (!$booking_still_has_schedule) {
                DB::update('plugin_ib_educate_bookings')
                    ->set(array('booking_status' => self::CANCELLED, 'modified_date' => date::now()))
                    ->and_where('booking_id', '=', $booking_id)
                    ->execute();
            }
            $result['message'] = 'Booking #' . $booking_id . ', Schedule #' . $schedule_id . ' has been cancelled.';

            if ($cancel_transactions == true) {
                $transactions = ORM::factory('Kes_Transaction')->get_schedule_transactions($booking_id, $schedule_id);
                $txcount = count($transactions);

                foreach ($transactions as $cancel_transaction) {
                    $cancel_data = array();
                    $cancel_data['contact_id'] = $booking['bill_payer'] > 0 ? $booking['bill_payer'] : $booking['booking_contact'];
                    $bcontact = new Model_Contacts3($cancel_data['contact_id']);
                    $bfamily_id = $bcontact->get_family_id();
                    if ($credit_to_family_id == null) { // add credit to own family
                        $crcontact = new Model_Contacts3($cancel_data['contact_id']);
                        $credit_to_family_id = $crcontact->get_family_id();
                    }
                    $cancel_data['transaction_id'] = $cancel_transaction['id'];
                    $cancel_data['booking_id'] = $booking_id;
                    $cancel_data['credit_amount'] = is_numeric($credit) ? $credit : 0;
                    $cancel_data['transaction_balance'] = 0;
                    $cancel_data['credit_payment'] = $cancel_data['credit_amount'] > 0 ? 'yes' : 'no';
                    $cancel_data['credit_destination'] = 'family';
                    $cancel_data['note'] = html::entities($note);

                    ORM::factory('Kes_Transaction')->set_journal_cancel($cancel_transaction['id']);
                    $result['message'] .= 'Transaction #' . $cancel_transaction['id'] . ' has been cancelled';

                    if ($credit > 0) {
                        $credit_journal_id = null;
                        if ($cancel_data['credit_amount'] > 0) {

                            $credit_journal_id = ORM::factory('Kes_Transaction')
                                ->create_credit_journal(
                                    $cancel_data['transaction_id'],
                                    $credit_to_family_id,
                                    null,
                                    $cancel_data['credit_amount']
                                );
                            $cnote = '€' . $cancel_data['credit_amount'] . '. Cancel booking#' . $booking_id;

                            $remove_credit = array(
                                'transaction_id' => $cancel_data['transaction_id'],
                                'type' => 'Transfer',
                                'amount' => $cancel_data['credit_amount'],
                                'status' => 6,
                                'note' => 'Transfer to credit available ' . $cnote . ': ' . $cancel_data['note'] .
                                    ($bfamily_id != $credit_to_family_id ? ', family# ' . $credit_to_family_id : '')
                            );
                            $add_credit = array(
                                'transaction_id' => $credit_journal_id,
                                'type' => 'Transfer',
                                'amount' => $cancel_data['credit_amount'],
                                'status' => 5,
                                'note' => $cnote . ' From contact#' . $cancel_data['contact_id'] . ': ' . $cancel_data['note'] .
                                    ($bfamily_id != $credit_to_family_id ? ', family# ' . $bfamily_id : '')
                            );
                            ORM::factory('Kes_Payment')->save_payment($remove_credit);
                            ORM::factory('Kes_Payment')->save_payment($add_credit);
                        }
                    }
                }
            }

            $doc_helper = new Model_Docarrayhelper();

            $doc_data = $doc_helper->booking_cancellation2($booking_schedule['id']);
            $template = DB::select()->from('plugin_files_file')->where('name', '=',
                $doc_data['template_name'])->execute()->as_array();
            if ($template) {
                $doc = null;
                try {
                    $doc = ORM::factory('Document')->auto_generate_document($doc_data, $direct = 0, $pdf = true);
                } catch (Exception $exc) {
                    $result['message'] .= $exc->getMessage();
                }
                if ($doc) {
                    $result['message'] .= ' And Booking Cancelled Document Created.';
                } else {
                    $result['message'] .= ' But an error happened when creating the document';
                }
            }

            $doc_data = $doc_helper->teacher_booking_cancellation2($booking_schedule['id']);
            $template = DB::select()->from('plugin_files_file')->where('name', '=',
                $doc_data['template_name'])->execute()->as_array();
            if ($template) {
                $doc = null;
                try {
                    $doc = ORM::factory('Document')->auto_generate_document($doc_data, $direct = 0, $pdf = true);
                } catch (Exception $exc) {
                    $result['message'] .= $exc->getMessage();
                }
                if ($doc) {
                    $result['message'] .= ' And Teacher Booking Cancelled Document Created.';
                } else {
                    $result['message'] .= ' But an error happened when creating the document';
                }
            }

            Model_Automations::run_triggers(Model_Courses_Schedulespaceavailabletrigger::NAME, array('schedule_id' => $schedule_id));

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }

        return $result;
    }

    public static function cancel_booking_schedules($args)
    {
        try {
            $schedules = $args['schedules'];
            $note = $args['note'];
            $credit_to_family_id = $args['credit_to_family_id'];
            $cancel_transactions = isset($args['cancel_transactions']) ? $args['cancel_transactions'] : false;
            $reason_code = isset($args['reason_code']) ? $args['reason_code'] : null;
            $booking_id = isset($args['booking_id']) ? $args['booking_id'] : null;

            Database::instance()->begin();

            $results = array();
            foreach ($schedules as $cb_schedule) {
                $results[] = self::cancel_booking_schedule([
                    'booking_id' => isset($cb_schedule['booking_id']) ? $cb_schedule['booking_id'] : $booking_id,
                    'schedule_id' => is_numeric($cb_schedule) ? $cb_schedule : $cb_schedule['schedule_id'],
                    'credit' => isset($cb_schedule['credit']) ? $cb_schedule['credit'] : 0,
                    'credit_to_family_id' => $credit_to_family_id,
                    'note' => $note,
                    'cancel_transactions' => $cancel_transactions,
                    'reason_code' => $reason_code
                ]);
            }

            Database::instance()->commit();
            return $results;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }

    }

    /**
     * Cancel delegates from a booking.
     * If all delegates have been cancelled, cancel the entire booking.
     *
     * @param $args array
     *     booking_id    int     ID of the booking
     *     delegate_ids  array   List of IDs of delegates to cancel from the booking
     *     reason_code   string  The code of the reason for cancellation
     */
    public static function cancel_booking_delegates($args)
    {
        try {
            Database::instance()->begin();
        $booking_id   = $args['booking_id'];
        $delegate_ids = !empty($args['delegate_ids']) ? $args['delegate_ids'] : [];
        $reason_code  = !empty($args['reason_code']) ? $args['reason_code'] : null;

        $cancel_count = count($delegate_ids);
        $booking = DB::select('bookings.*', DB::expr("count(*) as delegate_count"))
            ->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'delegates'), 'inner')
                    ->on('bookings.booking_id', '=', 'delegates.booking_id')
            ->where('bookings.booking_id', '=', $booking_id)
            ->and_where('delegates.deleted', '=', 0)
            ->and_where('delegates.cancelled', '=', 0)
            ->group_by('bookings.booking_id')
            ->execute()
            ->current();
        //echo '<pre>' . print_r($booking, 1) . '</pre>';

        $cancel_transaction_total = round(($booking['amount'] / $booking['delegate_count']) * $cancel_count, 2);
        $cancel_transaction_discount = 0;
        $discounts = DB::select('*')
            ->from(Model_KES_Bookings::DISCOUNTS)
            ->where('booking_id', '=', $booking_id)
            ->and_where('amount', '>', 0)
            ->execute()
            ->as_array();

            $new_amount = $booking['amount'] - $cancel_transaction_total;
            foreach ($discounts as $i => $discount) {
                $discount_new_amount = round(($discount['amount'] / $booking['delegate_count']) * ($booking['delegate_count'] - $cancel_count), 2);
                $cancel_transaction_discount += round(($discount['amount'] / $booking['delegate_count']) * $cancel_count, 2);
                if ($cancel_count < $booking['delegate_count']) {
                    DB::update(Model_KES_Bookings::DISCOUNTS)
                        ->set(array('amount' => $discount_new_amount))
                        ->where('id', '=', $discount['id'])
                        ->execute();
                }
            }

            DB::update(self::BOOKING_TABLE)
            ->set(
                array(
                    'amount' => $new_amount,
                    'modified_date' => date::now(),
                    'modified_by' => Auth::instance()->get_user()['id']
                )
            )
            ->where('booking_id', '=', $booking_id)
            ->execute();

            // Cancel each individual delegate
        foreach ($delegate_ids as $delegate_id) {
            // Load relationship
            $has_delegate = new Model_Booking_HasDelegate([
                'contact_id'  => $delegate_id,
                'booking_id'  => $booking_id
            ]);
            // Set data and save
            $has_delegate->set('cancelled', 1);
            $has_delegate->set('cancel_reason_code', $reason_code);
            $has_delegate->set('date_cancelled', date('Y-m-d H:i:s'));
            $has_delegate->save();

            // Track activity
            $user_id =  Auth::instance()->get_user()['id'];
            $activity = new Model_Activity();
            $activity
                ->set_action('cancel_delegate')
                ->set_item_type('booking')
                ->set_item_id($booking_id)
                ->set_user_id($user_id)
                ->set_scope_id($delegate_id)
                ->save();
        }
            $tx = new Model_Kes_Transaction();
            $tx->trigger_save = false;
            $tx_stat = $tx->create_transaction(
                array(
                    'booking_id' => $booking_id,
                    'amount' => $cancel_transaction_total + $cancel_transaction_discount,
                    'discount' => $cancel_transaction_discount,
                    'total' => $cancel_transaction_total,
                    'type' => 4,
                    'schedule' => array()
                )
            );

            DB::update(Model_KES_Bookings::DELEGATES_TABLE)
                ->set(array('cancel_transaction_id' => $tx_stat['transaction']))
                ->where('booking_id', '=', $booking_id)
                ->and_where('contact_id', 'in', $delegate_ids)
                ->execute();

            Model_Automations::run_triggers(Model_Bookings_Transactionsavetrigger::NAME, array('transaction_id' => $tx_stat['transaction']));
            
			// If there are no delegates left, cancel the booking.
            $remaining_delegates = self::get_delegates($booking_id, ['include_cancelled' => false]);
            $booking = new Model_Booking_Booking($booking_id);
            $args['schedules'] = array_keys($booking->schedules->find_all()->as_array('id'));
            if (empty($remaining_delegates)) {
                Model_KES_Bookings::cancel_booking_schedules($args);
            }
            foreach ($args['schedules'] as $schedule_id){
                if (!empty($args['do_not_trigger_available'])) {
                    $skip = false;
                    foreach ($args['do_not_trigger_available'] as $do_not_trigger_available) {
                        if ($do_not_trigger_available['schedule_id'] == $schedule_id) {
                            $skip = true;
                            break;
                        }
                    }
                    if ($skip) {
                        continue;
                    }
                }
                Model_Automations::run_triggers(Model_Courses_Schedulespaceavailabletrigger::NAME, array('schedule_id' => $schedule_id));
            }
            Database::instance()->commit();
        } catch (Exception $exc){
            Database::instance()->rollback();Model_Errorlog::save($exc);
        }
    }

    public static function all_set_inprogress_completed($contact_id = null)
    {
        try {
            DB::query(null, "SELECT GET_LOCK('all_set_inprogress_completed', 300)")->execute();
            Database::instance()->begin();

            /*
             * find first and last booked item dates
             * */
            DB::query(null, "DROP TEMPORARY TABLE IF EXISTS first_booking_event_dates")->execute();
            DB::query(null, "CREATE TEMPORARY TABLE first_booking_event_dates (booking_id INT, dt DATETIME) ENGINE=MEMORY")->execute();
            DB::query(null, "INSERT INTO first_booking_event_dates
	(SELECT b.booking_id, MIN(e.datetime_start) AS first_dt	FROM plugin_ib_educate_bookings b
		INNER JOIN plugin_ib_educate_bookings_status t ON b.booking_status = t.status_id
		INNER JOIN " . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " i ON b.booking_id = i.booking_id
		INNER JOIN plugin_courses_schedules_events e ON i.period_id = e.id
		WHERE b.`delete` = 0 AND i.`delete` = 0 AND e.`delete` = 0 " . ($contact_id ? " AND b.contact_id=" . $contact_id : "") . "
	GROUP BY b.booking_id)")->execute();

            DB::query(null, "DROP TEMPORARY TABLE IF EXISTS last_booking_event_dates")->execute();
            DB::query(null, "CREATE TEMPORARY TABLE last_booking_event_dates (booking_id INT, dt DATETIME) ENGINE=MEMORY")->execute();
            DB::query(null, "INSERT INTO last_booking_event_dates
	(SELECT b.booking_id, MAX(e.datetime_end) AS last_dt	FROM plugin_ib_educate_bookings b
		INNER JOIN plugin_ib_educate_bookings_status t ON b.booking_status = t.status_id
		INNER JOIN " . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " i ON b.booking_id = i.booking_id
		INNER JOIN plugin_courses_schedules_events e ON i.period_id = e.id
		WHERE b.`delete` = 0 AND i.`delete` = 0 AND e.`delete` = 0 " . ($contact_id ? " AND b.contact_id=" . $contact_id : "") . "
	GROUP BY b.booking_id)")->execute();

            // if booking is confirmed and first timeslot passed then set to in progress
            DB::query(null, "UPDATE
	" . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " b
		INNER JOIN plugin_ib_educate_bookings_status t ON b.booking_status = t.status_id
		INNER JOIN first_booking_event_dates f ON b.booking_id = f.booking_id
	SET b.booking_status = (SELECT status_id FROM plugin_ib_educate_bookings_status WHERE title = 'In Progress')
	WHERE t.title = 'Confirmed' AND f.dt <= NOW() AND b.booking_status <> 3")->execute();
            DB::query(null, "UPDATE
	plugin_ib_educate_booking_has_schedules b
		INNER JOIN plugin_ib_educate_bookings_status t ON b.booking_status = t.status_id
		INNER JOIN first_booking_event_dates f ON b.booking_id = f.booking_id
	SET b.booking_status = (SELECT status_id FROM plugin_ib_educate_bookings_status WHERE title = 'In Progress')
	WHERE t.title = 'Confirmed' AND f.dt <= NOW() AND b.booking_status <> 3")->execute();
            DB::query(null, "UPDATE
	plugin_ib_educate_bookings b
		INNER JOIN plugin_ib_educate_bookings_status t ON b.booking_status = t.status_id
		INNER JOIN first_booking_event_dates f ON b.booking_id = f.booking_id
	SET b.booking_status = (SELECT status_id FROM plugin_ib_educate_bookings_status WHERE title = 'In Progress')
	WHERE t.title = 'Confirmed' AND f.dt <= NOW()")->execute();

            // if booking is in progress and last timeslot passed then set to completed
            DB::query(null, "UPDATE
	" . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " b
		INNER JOIN plugin_ib_educate_bookings_status t ON b.booking_status = t.status_id
		INNER JOIN last_booking_event_dates l ON b.booking_id = l.booking_id
	SET b.booking_status = (SELECT status_id FROM plugin_ib_educate_bookings_status WHERE title = 'Completed')
	WHERE t.title = 'In Progress' AND l.dt <= NOW() AND b.booking_status <> 3")->execute();
            DB::query(null, "UPDATE
	plugin_ib_educate_booking_has_schedules b
		INNER JOIN plugin_ib_educate_bookings_status t ON b.booking_status = t.status_id
		INNER JOIN last_booking_event_dates l ON b.booking_id = l.booking_id
	SET b.booking_status = (SELECT status_id FROM plugin_ib_educate_bookings_status WHERE title = 'Completed')
	WHERE t.title = 'In Progress' AND l.dt <= NOW() AND b.booking_status <> 3")->execute();
            DB::query(null, "UPDATE
	plugin_ib_educate_bookings b
		INNER JOIN plugin_ib_educate_bookings_status t ON b.booking_status = t.status_id
		INNER JOIN last_booking_event_dates l ON b.booking_id = l.booking_id
	SET b.booking_status = (SELECT status_id FROM plugin_ib_educate_bookings_status WHERE title = 'Completed')
	WHERE t.title = 'In Progress' AND l.dt <= NOW()")->execute();


            Database::instance()->commit();

            DB::query(null, "SELECT RELEASE_LOCK('all_set_inprogress_completed')")->execute();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            DB::query(null, "SELECT RELEASE_LOCK('all_set_inprogress_completed')")->execute();
            throw $exc;
        }
    }

    public static function search($params = array())
    {
        $searchq = DB::select(
            'bookings.*'
        )->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
            ->distinct('bookings.id')
            ->where('bookings.delete', '=', 0);

        if (@$params['booking_status']) {
            $searchq->and_where('bookings.booking_status', 'in', $params['booking_status']);
        }

        if (@$params['contact_id']) {
            if (is_array($params['contact_id'])) {
                $searchq->and_where('bookings.contact_id', 'in', $params['contact_id']);
            } else {
                $searchq->and_where('bookings.contact_id', '=', $params['contact_id']);
            }
        }

        if (@$params['schedule_id'] || @$params['sort'] || @$params['trainer_id']) {
            $searchq->join(array(self::BOOKING_SCHEDULES, 'bschedules'), 'inner')
                ->on('bookings.booking_id', '=', 'bschedules.booking_id');
            $searchq->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                ->on('bschedules.schedule_id', '=', 'schedules.id');
            if (@$params['schedule_id']) {
                $searchq->and_where('bschedules.schedule_id', '=', $params['schedule_id']);
            }

            if (@$params['booking_status']) {
                $searchq->and_where('bschedules.booking_status', 'in', $params['booking_status']);
            }
        }
        //if timeslots ids are not passed, $params has empty array, need to filter them to prevent empty conditions
        $timeslots = @array_filter($params['timeslot_id']);

        if (@$params['after'] || @$params['before'] || !empty($timeslots) || @$params['trainer_id']) {
            $searchq->join(array(self::BOOKING_ITEMS_TABLE, 'btimeslots'), 'inner')
                ->on('bookings.booking_id', '=', 'btimeslots.booking_id');
        }
        if (!empty($timeslots)) {
            $searchq->and_where('btimeslots.period_id', '=', $params['timeslot_id']);
        }

        if (@$params['trainer_id']) {
            $searchq->and_where_open();
                $searchq->or_where('schedules.trainer_id', '=', $params['trainer_id']);
                $searchq->or_where('timeslots.trainer_id', '=', $params['trainer_id']);
            $searchq->and_where_close();
        }

        if (@$params['after'] || @$params['before'] || @$params['sort'] == 'date' || @$params['trainer_id']) {
            $searchq->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                ->on('btimeslots.period_id', '=', 'timeslots.id');
            $searchq->and_where('timeslots.delete', '=', 0);
        }

        if (@$params['after']) {
            $searchq->and_where('timeslots.datetime_end', '>=', $params['after']);
        }
        if (@$params['before']) {
            $searchq->and_where('timeslots.datetime_end', '<=', $params['before']);
        }

        if (@$params['sort']) {
            if ($params['sort'] == 'schedule') {
                $searchq->order_by('schedules.name', 'asc');
            } else if ($params['sort'] == 'date') {
                $searchq->order_by('timeslots.datetime_start', 'asc');
            } else {
                $searchq->order_by('bookings.booking_id', 'desc');
            }
        } else {
            $searchq->order_by('bookings.booking_id', 'desc');
        }
        $result = $searchq->execute()->as_array();
        return $result;
    }

    public static function search2($params = array())
    {
        $searchq = DB::select(
            DB::expr('DISTINCT bookings.*'),
            array('schedules.name', 'schedule'),
            'schedules.start_date',
            'schedules.end_date',
            'schedules.rental_fee',
            'schedules.payment_type',
            array('courses.title', 'course'),
            DB::expr("CONCAT_WS(' ', trainers.first_name, trainers.last_name) as trainer"),
            array('locations.name', 'room'),
            array('plocations.name', 'building'),
            array('users.id', 'contact_user_id'),
            'users.avatar',
            'users.use_gravatar',
            'bschedules.schedule_id',
            'courses.subject_id',
            array('subjects.name', 'subject'),
            'subjects.color'
        )->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
            ->join(array(self::BOOKING_SCHEDULES, 'bschedules'), 'inner')
                ->on('bookings.booking_id', '=', 'bschedules.booking_id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                ->on('bschedules.schedule_id', '=', 'schedules.id')
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                ->on('schedules.course_id', '=', 'courses.id')
            ->join(array('plugin_courses_subjects', 'subjects'), 'left')
                ->on('courses.subject_id', '=', 'subjects.id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'left')
                ->on('schedules.trainer_id', '=', 'trainers.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')
                ->on('schedules.location_id', '=', 'locations.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'plocations'), 'left')
                ->on('locations.parent_id', '=', 'plocations.id')
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                ->on('bookings.contact_id', '=', 'contacts.id')
            ->join(array(Model_Contacts3::TABLE_PERMISSION_LIMIT, 'pt'), 'left')
                ->on('contacts.id', '=', 'pt.contact3_id')
            ->join(array(Model_Users::MAIN_TABLE, 'users'), 'left')
            ->on('pt.user_id', '=', 'users.id')
            ->where('bookings.delete', '=', 0);

        if (@$params['booking_status']) {
            $searchq->and_where('bookings.booking_status', 'in', $params['booking_status']);
        }

        if (@$params['contact_id']) {
            if (is_array($params['contact_id'])) {
                $searchq->and_where('bookings.contact_id', 'in', $params['contact_id']);
            } else {
                $searchq->and_where('bookings.contact_id', '=', $params['contact_id']);
            }
        }

        if (@$params['schedule_id']) {
            $searchq->and_where('bschedules.schedule_id', '=', $params['schedule_id']);

            if (@$params['booking_status']) {
                $searchq->and_where('bschedules.booking_status', 'in', $params['booking_status']);
            } else {
                $searchq->and_where('bschedules.booking_status', 'in', array(2,4,5));
            }
        }

        if (@$params['after'] || @$params['before'] || @$params['timeslot_id'] || @$params['sort'] == 'date' || @$params['trainer_id']) {
            $searchq->join(array(self::BOOKING_ITEMS_TABLE, 'btimeslots'), 'inner')
                ->on('bookings.booking_id', '=', 'btimeslots.booking_id');
        }

        if (@$params['timeslot_id']) {
            $searchq->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                ->on('btimeslots.period_id', '=', 'timeslots.id')
                ->on('schedules.id', '=', 'timeslots.schedule_id')
            ->and_where('btimeslots.period_id', '=', $params['timeslot_id']);
        }

        if (@$params['after'] || @$params['before'] || @$params['sort'] == 'date' || @$params['trainer_id']) {
            if (!@$params['timeslot_id']) {
                $searchq->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('btimeslots.period_id', '=', 'timeslots.id');
            }
            $searchq->and_where('timeslots.delete', '=', 0);
            $searchq->and_where('btimeslots.delete', '=', 0);
        }

        if (@$params['after']) {
            $searchq->and_where('timeslots.datetime_end', '>=', $params['after']);
        }
        if (@$params['before']) {
            $searchq->and_where('timeslots.datetime_end', '<=', $params['before']);
        }

        if (@$params['trainer_id']) {
            $searchq->and_where_open();
            $searchq->or_where('schedules.trainer_id', '=', $params['trainer_id']);
            $searchq->or_where('timeslots.trainer_id', '=', $params['trainer_id']);
            $searchq->and_where_close();
        }

        if (@$params['keyword'])
        {
            $keywords = preg_split('/[\ ,]+/i', trim(preg_replace('/[^a-z0-9\ ]/i', '', $params['keyword'])));
            $match1 = array();
            $match2 = array();
            foreach ($keywords as $i => $keyword) {
                if (strlen($keyword) < 3) { // remove too short things like "at" "'s" "on" ...
                    unset($keywords[$i]);
                } else {
                    if (substr($keyword, -3) == 'ies'){
                        $match2[] = '+' . substr($keyword, 0, -3) . 'y' . '*';
                    } else if (substr($keyword, -3) == 'ses' || substr($keyword, -3) == 'xes'){
                        $match2[] = '+' . substr($keyword, 0, -2) . '*';
                    } else if ($keyword[strlen($keyword) - 1] == 's') {
                        $match2[] = '+' . substr($keyword, 0, -1) . '*'; /*'+' . $keyword . '* */
                    } else {
                        $match2[] = '+' . $keyword . '*';
                    }
                    $match1[] = '+' . $keyword . '*';
                }
            }

            $searchq->and_where_open();

            if (!empty($keywords)) {
                $match1 = Database::instance()->escape(implode(' ', $match1));
                $match2 = Database::instance()->escape(implode(' ', $match2));
                // Separate terms, enclose in quotes to stop special characters causing problems, "+" before each term
                $searchq->or_where(DB::expr('match(`courses`.`title`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $searchq->or_where(DB::expr('match(`courses`.`title`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
                $searchq->or_where(DB::expr('match(`schedules`.`name`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $searchq->or_where(DB::expr('match(`schedules`.`name`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
            } else {
                $searchq->or_where('courses.title', 'like', '%' . $params['keyword'] . '%');
                $searchq->or_where('schedules.name', 'like', '%' . $params['keyword'] . '%');
            }
            $searchq->and_where_close();
        }

        if (@$params['sort']) {
            if ($params['sort'] == 'schedule') {
                $searchq->order_by('schedules.name', 'asc');
            } else if ($params['sort'] == 'date') {
                $searchq->order_by('timeslots.datetime_start', 'asc');
            } else {
                $searchq->order_by('bookings.booking_id', 'desc');
            }
        } else {
            $searchq->order_by('bookings.booking_id', 'desc');
        }
        $searchq->and_where('bookings.booking_status', 'in', array(2,4,5));
        $searchq->and_where('bschedules.deleted', '=', 0);

        $result = $searchq->execute()->as_array();
        foreach ($result as $i => $row) {
            if ($row['avatar'] || $row['use_gravatar']) {
                $result[$i]['profile_image_url'] = URL::get_avatar($row['contact_user_id']);
            } else {
                $result[$i]['profile_image_url'] = null;
            }

            $timeslot_searchq = DB::select('timeslots.*', 'tx_schedules.transaction_id')
                ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                    ->join(array(self::BOOKING_SCHEDULES, 'bschedules'), 'inner')
                        ->on('bookings.booking_id', '=', 'bschedules.booking_id')
                    ->join(array(self::BOOKING_ITEMS_TABLE, 'btimeslots'), 'inner')
                        ->on('bookings.booking_id', '=', 'btimeslots.booking_id')
                    ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                        ->on('bschedules.schedule_id', '=', 'timeslots.schedule_id')
                        ->on('btimeslots.period_id', '=', 'timeslots.id')
                    ->join(array(Model_Kes_Transaction::TABLE_HAS_SCHEDULES, 'tx_schedules'), 'left')
                        ->on('tx_schedules.event_id', '=', 'btimeslots.period_id')
                    ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'tx'), 'left')
                        ->on('tx_schedules.transaction_id', '=', 'tx.id')
                        ->on('tx.booking_id', '=', 'bookings.booking_id')
                ->where('timeslots.delete', '=', 0)
                ->and_where('btimeslots.delete', '=', 0)
                ->and_where('bookings.booking_id', '=', $row['booking_id']);

            if (@$params['after']) {
                $timeslot_searchq->and_where('timeslots.datetime_start', '>=', $params['after']);
            }
            if (@$params['before']) {
                $timeslot_searchq->and_where('timeslots.datetime_end', '<=', $params['before']);
            }
            if (@$params['timeslot_id']) {
                $timeslot_searchq->and_where('timeslots.id', '=', $params['timeslot_id']);
            }
            $result[$i]['timeslots'] = $timeslot_searchq->execute()->as_array();
            foreach ($result[$i]['timeslots'] as $t => $timeslot) {
                $result[$i]['timeslots'][$t] = array(
                    'id' => $timeslot['id'],
                    'start_date' => $timeslot['datetime_start'],
                    'end_date' => $timeslot['datetime_end'],
                    'trainer_id' => $timeslot['trainer_id'],
                    'fee_amount' => $timeslot['fee_amount'],
                    'monitored' => $timeslot['monitored'],
                    'transaction_id' => $timeslot['transaction_id'],
                    'topic_id' => $timeslot['topic_id'],
                );
            }

        }
        return $result;
    }

    private static function verifyDate($date, $strict = true)
    {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        if ($strict) {
            $errors = DateTime::getLastErrors();
            if (!empty($errors['warning_count'])) {
                return false;
            }
        }
        return $dateTime !== false;
    }


    public function send_booking_create_notification($data)
    {
        $payment_types = array(1 => 'Pre-Pay', 2 => 'Pay as you go');
        $booked_schedule = Model_Schedules::get_one_for_details($data['schedule_id']);

        $currency = '€';

        $message_params = array();
        $message_params['bookingid'] = $this->booking_id;
        $message_params['course'] = $booked_schedule['course'];
        $message_params['schedule'] = $booked_schedule['schedule'];
        $message_params['paymenttype'] = $payment_types[$booked_schedule['payment_type']];
        $message_params['status'] = $this->booking_status;
        $message_params['total'] = $currency.number_format((isset($_POST['amount']) ? $_POST['amount'] : (empty($booked_schedule['fee_amount']) ? '0' : $booked_schedule['fee_amount'])), 2);
        $message_params['deposit'] = $currency.number_format((empty($booked_schedule['deposit']) ? '0' : $booked_schedule['deposit']), 2);
        $message_params['fee'] = $currency.number_format((empty($booked_schedule['fee_amount']) ? '0' : $booked_schedule['fee_amount']), 2);
        //Override the message parameters if other data has been passed through the other stacks instead
        $message_params = array_merge($message_params, $data);

        $contact = new Model_Contacts3($this->contact_id);
        $message_params['student'] = $contact->get_first_name() . ' ' . $contact->get_last_name();
        $family_id = $contact->get_family_id();
        $parents_to_notify = Model_Contacts3::get_family_members($family_id, array('bookings'));
        if (count($parents_to_notify) > 0 || $contact->get_id()) {
            $recipients = array();
            foreach ($parents_to_notify as $parent_to_notify) {
                $recipients[] = array('target_type' => 'CMS_CONTACT3', 'target' => $parent_to_notify['id']);
            }

            if ($contact->get_id()) {
                // Also send to the student (despite "parent" being part of the notification name)
                $recipients[] = ['target_type' => 'CMS_CONTACT3', 'target' => $contact->get_id()];
            }

            $mm = new Model_Messaging();
            $mm->send_template(
                'course-booking-parent',
                null,
                date::now(),
                $recipients,
                $message_params
            );
        }
    }

    public static function search_schedules($limit, $offset, $sort, $dir, $search = '', $filters = array())
    {
        $_search   = '';
        $columns   = array();
        $columns[] = 'schedules.id';
        $columns[] = 'location';
        $columns[] = 'subjects.name';
        $columns[] = 'courses.title';
        $columns[] = 'schedules.name';
        $columns[] = 'categories.category';
        $columns[] = 'year';
        $columns[] = 'levels.level';
        $columns[] = 'trainer';
        $columns[] = 'day';
        $columns[] = 'datetime_start';
        $columns[] = 'fee';
        $columns[] = 'schedules.payment_type';
        $columns[] = 'number_of_bookings';
        $columns[] = 'timeslots_counts';

        $ts_count = DB::select(
            "e.schedule_id",
            DB::expr("count(*) as timeslot_count"),
            DB::expr("sum(if (e.datetime_start >= now(), 1, 0)) as future_count")
        )->from(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'e'))
            ->where('e.delete', '=', 0)
            ->group_by('e.schedule_id');

        $b_count = DB::select(
            "s.schedule_id",
            DB::expr("count(*) as booking_count")
        )->from(array(self::BOOKING_SCHEDULES, 's'))
            ->join(array(self::BOOKING_TABLE, 'b'), 'inner')->on('s.booking_id', '=', 'b.booking_id')
            ->where('s.deleted', '=', 0)
            ->and_where('s.booking_status', '!=', self::CANCELLED)
            ->and_where('b.delete', '=', 0)
            ->and_where('b.booking_status', '<>', self::CANCELLED)
            ->group_by('s.schedule_id');

        $select = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS `schedules`.`id`'),
            DB::expr("IF(`plocations`.`id` is not null, `plocations`.`name`, `locations`.`name`) as `location`"),
            array('subjects.name', 'subject'),
            array('courses.title', 'course'),
            array('schedules.name', 'schedule'),
            'categories.category',
            DB::expr('GROUP_CONCAT(DISTINCT `years`.`year`) as `year`'),
            'levels.level',
            DB::expr("CONCAT_WS(' ', `trainers`.`first_name`, `trainers`.`last_name`) as `trainer`"),

            // If the timeslots repeat on a specific time of the week or run on three or fewer different times of week,
            // show the times of week
            DB::expr("IF(
                `repeat`.`name` = 'Weekly' OR `repeat`.`name` = 'Fortnightly' OR COUNT(DISTINCT CONCAT(DATE_FORMAT(`timeslots`.`datetime_start`, '%a %H:%i'), '-', DATE_FORMAT(`timeslots`.`datetime_end`, '%H:%i'), '')) <= 3,
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        '<span class=\"nowrap\">',
                        DATE_FORMAT(`timeslots`.`datetime_start`,'%a %H:%i'),
                        '–',
                        DATE_FORMAT(`timeslots`.`datetime_end`,'%H:%i'),
                        '</span>'
                    )
                    ORDER BY DATE_FORMAT(`timeslots`.`datetime_start`,'%w %H:%i')
                    SEPARATOR ',<br />'
                ),
                ''
            ) AS `day`"),
            DB::expr('MIN(timeslots.datetime_start) as datetime_start'),
            array('schedules.fee_amount', 'fee'),
            'schedules.payment_type',
            array("bhs.booking_count", 'number_of_bookings'),
            DB::expr("CONCAT(ts_count.future_count, '/', ts_count.timeslot_count) as timeslots_counts")
        )
            ->from(array('plugin_courses_schedules',         'schedules' ))
            ->join(array('plugin_courses_repeat',            'repeat',   ),  'left' )->on('schedules.repeat',      '=', 'repeat.id')
            ->join(array('plugin_courses_courses',           'courses'   ),  'inner')
                ->on('schedules.course_id', '=', 'courses.id')
                ->on('courses.publish',     '=', DB::expr("'1'"))
                ->on('courses.deleted',     '=', DB::expr("'0'"))
            ->join(array('plugin_courses_schedules_events',  'timeslots' ),  'left' )->on('timeslots.schedule_id', '=', 'schedules.id')
                                                                                     ->on('timeslots.delete',      '=', DB::expr("0"))
            ->join(array($b_count,                           'bhs'       ),  'left' )->on('bhs.schedule_id',       '=', 'schedules.id')
            ->join(array('plugin_courses_locations',         'locations' ),  'left' )->on('schedules.location_id', '=', 'locations.id')
            ->join(array('plugin_courses_locations',         'plocations'),  'left' )->on('locations.parent_id',   '=', 'plocations.id')
            ->join(array('plugin_courses_categories',        'categories'),  'left' )->on('courses.category_id',   '=', 'categories.id')
            ->join(array('plugin_courses_subjects',          'subjects'  ),  'left' )->on('courses.subject_id',    '=', 'subjects.id')
            ->join(array('plugin_courses_courses_has_years', 'has_years' ),  'left' )->on('courses.id',            '=', 'has_years.course_id')
            ->join(array('plugin_courses_years',             'years'     ),  'left' )->on('has_years.year_id',     '=', 'years.id')
            ->join(array('plugin_courses_levels',            'levels'    ),  'left' )->on('courses.level_id',      '=', 'levels.id')
            ->join(array('plugin_contacts3_contacts',        'trainers'  ),  'left' )->on('schedules.trainer_id',  '=', 'trainers.id')
            ->join(array('engine_lookup_values',          'learning_mode'),  'left' )->on('schedules.learning_mode_id', '=', 'learning_mode.id')
            ->join(array($ts_count, 'ts_count'), 'left')->on('schedules.id', '=', 'ts_count.schedule_id');
        $select->where('schedules.delete', '=', 0);
        $select->where('schedules.publish', '=', 1);

        if (isset($filters['timeslots_range']) && $filters['timeslots_range'] == 'past') {
            $select
                ->and_where_open()
                    ->where('timeslots.datetime_start', '<=', date::now())
                    ->or_where('learning_mode.value', '=', 'self_paced')
                ->and_where_close();
        }

        if (isset($filters['timeslots_range']) && $filters['timeslots_range'] == 'upcoming') {
            $select
                ->and_where_open()
                    ->where('timeslots.datetime_start', '>', date::now())
                    ->or_where('learning_mode.value', '=', 'self_paced')
                ->and_where_close();
        }

        if (@$filters['is_group_booking'] == 1) {
            $select->and_where('schedules.is_group_booking', '=', 1);
        }
        if (@$filters['exclude_cancelled'] == 1) {
            $select->and_where('schedules.schedule_status', '<>', Model_Schedules::CANCELLED);
        }
        if ($search != '') {
            $select->and_where_open();
                $select->or_where('schedules.name', 'like', '%' . $search . '%');
                $select->or_where('courses.title',  'like', '%' . $search . '%');
                $select->or_where(DB::expr("CONCAT_WS(' ', `trainers`.`first_name`, `trainers`.`last_name`)"), 'like', '%' . $search . '%');
            $select->and_where_close();
        }

        foreach ($columns as $key => $column) {
            if (!empty($filters['sSearch_'.$key])) {
                if (strpos($filters['sSearch_'.$key], '|') !== false) {
                    // Column multiselect filters
                    $select->and_having($column, 'regexp', $filters['sSearch_'.$key]);
                }
                else {
                    // Column search filters
                    $select->and_having($column, 'like', '%' . $filters['sSearch_'.$key] . '%');
                }

            }
        }

        if ($limit <10 || $limit > 100) {
            $limit = 10;
        }
        $select
            ->order_by($columns[$sort], $dir)
            ->group_by('schedules.id')
            ->limit($limit)
            ->offset($offset);

        $data = $select->execute()->as_array();
        $return = array();
        $output['iTotalRecords'] = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        foreach ($data as $i => $row) {
            $return[$i] = $row;
            $return[$i]['payment_type'] = $return[$i]['payment_type'] == 2 ? 'PAYG' : 'PrePay';
            $return[$i]['action'] = '<div>
                    <div class="hidden--multiple">
                        <button type="button" class="booking-schedules-select-single" data-id="'.$row['id'].'">'.__('Select').'</button>
                    </div>

                    <div class="hidden--single">'.Form::ib_checkbox(null, 'schedule_id[]', $row['id']).'</div>
                </div>';
        }
        $output['sql'] = (string)$select;

        $output['aaData'] = $return;
        return $output;
    }

    public static function get_schedule($schedule_id, $timeslot_ids = array())
    {
        $select = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS schedules.*'),
            array('plocations.name', 'location'),
            array('locations.name', 'room'),
            array('subjects.name', 'subject'),
            array('courses.title', 'course'),
            array('schedules.name', 'schedule'),
            'categories.category',
            DB::expr('GROUP_CONCAT(DISTINCT years.year) as `year`'),
            'levels.level',
            DB::expr("CONCAT_WS(' ', trainers.first_name, trainers.last_name) as trainer")
        )
            ->from(array('plugin_courses_schedules', 'schedules'))
            ->join(array('plugin_courses_courses', 'courses'), 'inner')->on('schedules.course_id', '=', 'courses.id')
            ->join(array('plugin_courses_locations', 'locations'), 'left')->on('schedules.location_id', '=', 'locations.id')
            ->join(array('plugin_courses_locations', 'plocations'), 'left')->on('locations.parent_id', '=', 'plocations.id')
            ->join(array('plugin_courses_categories', 'categories'), 'left')->on('courses.category_id', '=', 'categories.id')
            ->join(array('plugin_courses_subjects', 'subjects'), 'left')->on('courses.subject_id', '=', 'subjects.id')
            ->join(array('plugin_courses_courses_has_years', 'has_years'), 'left')->on('courses.id', '=', 'has_years.course_id')
            ->join(array('plugin_courses_years', 'years'), 'left')->on('has_years.year_id', '=', 'years.id')
            ->join(array('plugin_courses_levels', 'levels'), 'left')->on('courses.level_id', '=', 'levels.id')
            ->join(array('plugin_contacts3_contacts', 'trainers'), 'left')->on('schedules.trainer_id', '=', 'trainers.id');
        $select->where('schedules.delete', '=', 0);
        $select->and_where('schedules.id', '=', $schedule_id);
        $schedule = $select->execute()->current();

        $tselect = DB::select('*')
            ->from('plugin_courses_schedules_events')
            ->where('schedule_id', '=', $schedule_id)
            ->and_where('delete', '=', 0);


        if (!empty($timeslot_ids)) {
            $tselect->and_where('id', 'in', $timeslot_ids);
        }
        $schedule['timeslots'] = $tselect->execute()->as_array();

        return $schedule;
    }


    public static function details($id)
    {
        $booking = DB::select('bookings.*', array('stats.title', 'status'))
            ->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(self::BOOKING_STATUS_TABLE, 'stats'), 'inner')->on('bookings.booking_status', '=', 'stats.status_id')
            ->where('bookings.booking_id', '=', $id)
            ->execute()
            ->current();

        if ($booking) {
            $booking['schedules'] = DB::select('schedules.*', 'hschedules.amendable', array('stats.title', 'status'), array('courses.title', 'course'))
                ->from(array(self::BOOKING_SCHEDULES, 'hschedules'))
                    ->join(array(self::BOOKING_STATUS_TABLE, 'stats'), 'inner')->on('hschedules.booking_status', '=', 'stats.status_id')
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('hschedules.schedule_id', '=', 'schedules.id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')->on('schedules.course_id', '=', 'courses.id')
                ->where('hschedules.booking_id', '=', $id)
                ->and_where('hschedules.deleted', '=', 0)
                ->execute()
                ->as_array();
            foreach ($booking['schedules'] as $i => $schedule) {
                $booking['schedules'][$i]['timeslots'] = DB::select('items.*', array('stats.title', 'status'), 'timeslots.datetime_start', 'timeslots.datetime_end')
                    ->from(array(self::BOOKING_ITEMS_TABLE, 'items'))
                        ->join(array(self::BOOKING_STATUS_TABLE, 'stats'), 'inner')->on('items.booking_status', '=', 'stats.status_id')
                        ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('items.period_id', '=', 'timeslots.id')
                    ->where('timeslots.schedule_id', '=', $schedule['id'])
                    ->and_where('items.delete', '=', 0)
                    ->order_by('timeslots.datetime_start', 'asc')
                    ->execute()
                    ->as_array();
            }
        }
        return $booking;
    }

    public static function generate_auth_code_for_student_booking($student_id)
    {
        $code = mt_rand(10000000, 99999999);
        $timeout = (int)Settings::instance()->get('bookings_student_auth_timeout');
        $data = array(
            'student_id' => $student_id,
            'code' => $code,
            'expires' => date('Y-m-d H:i:s', time() + $timeout),
            'status' => 'Wait',
            'created' => date::now(),
            'updated' => date::now()
        );
        $inserted = DB::insert(self::STUDENT_BOOKING_AUTHS)
            ->values($data)
            ->execute();
        $data['id'] = $inserted[0];
        return $data;
    }

    public static function send_sms_attendance_edit_auth_code($student_id)
    {
        $student = new Model_Contacts3($student_id);
        $members = Model_Contacts3::get_family_members($student->get_family_id());
        $parent = null;
        foreach ($members as $member) {
            if (array_search('guardian', $member['has_roles']) !==false) {
                foreach ($member['notifications'] as $notification) {
                    if ($notification['notification_id'] == 2) {
                        $mobile = trim($notification['value']);
                        $parent = $member;
                        break;
                    }
                }
            }
        }

        if ($parent) {
            $auth = self::generate_auth_code_for_student_booking($student_id);

            $msg_params = array();
            $msg_params['studentname'] = $student->get_first_name() . ' ' . $student->get_last_name();
            $msg_params['parentname'] = $parent['first_name'] . ' ' . $parent['last_name'];
            $msg_params['code'] = $auth['code'];
            $mm = new Model_Messaging();
            $mm->send_template(
                'student-attendance-edit-send-auth-code',
                null,
                null,
                array(
                    array('target_type' => 'CMS_CONTACT3', 'target' => $parent['id'], 'final_target' => $mobile)
                ),
                $msg_params
            );

            return $auth;
        } else {
            return false;
        }
    }

    public static function send_sms_auth_code($student_id, $mobile, $amount)
    {
        $student = new Model_Contacts3($student_id);
        $members = Model_Contacts3::get_family_members($student->get_family_id());
        $parent = null;
        $mobile = preg_replace('/\s+/', '', trim($mobile));
        foreach ($members as $member) {
            if (array_search('guardian', $member['has_roles']) !==false) {
                foreach ($member['notifications'] as $notification) {
                    if (preg_replace('/\s+/', '', trim($notification['value'])) == $mobile) {
                        $parent = $member;
                        break;
                    }
                }
            }
        }

        if ($parent) {
            $auth = self::generate_auth_code_for_student_booking($student_id);

            $msg_params = array();
            $msg_params['amount'] = '€' . $amount;
            $msg_params['studentname'] = $student->get_first_name() . ' ' . $student->get_last_name();
            $msg_params['parentname'] = $parent['first_name'] . ' ' . $parent['last_name'];
            $msg_params['code'] = $auth['code'];
            $mm = new Model_Messaging();
            $mm->send_template(
                'student-checkout-send-auth-code',
                null,
                null,
                array(
                    array('target_type' => 'CMS_CONTACT3', 'target' => $parent['id'], 'final_target' => $mobile)
                ),
                $msg_params
            );

            return $auth;
        } else {
            return false;
        }
    }

    public static function check_student_booking_auth($student_id, $auth_id, $code)
    {
        $code = trim($code);
        $timeout = (int)Settings::instance()->get('bookings_student_auth_timeout');
        DB::update(self::STUDENT_BOOKING_AUTHS)
            ->set(array('status' => 'Expired', 'updated' => date::now()))
            ->where('status', '=', 'Wait')
            ->and_where('expires', '<=', date('Y-m-d H:i:s', time() - $timeout))
            ->execute();

        $authq = DB::select('*')
            ->from(self::STUDENT_BOOKING_AUTHS)
            ->where('student_id', '=', $student_id)
            ->and_where('code', '=', trim($code))
            ->and_where('status', '=', 'Wait')
            ->and_where('deleted', '=', 0);
        if ($auth_id) {
            $authq->and_where('id', '=', $auth_id);
        }

        $auth = $authq->execute()->current();
        return $auth;
    }

    public static function set_student_booking_auth_validated($id)
    {
        $status = 'Validated';
        DB::update(self::STUDENT_BOOKING_AUTHS)
            ->set(array('status' => $status, 'updated' => date::now()))
            ->where('id', '=', $id)
            ->execute();
    }

    public static function has_subscription($booking_id)
    {
        $schedules = DB::select('schedules.*')
            ->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(self::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
                    ->on('bookings.booking_id', '=', 'has_schedules.booking_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
            ->where('bookings.booking_id', '=', $booking_id)
            ->and_where('has_schedules.booking_status', '<>', 3)
            ->and_where('has_schedules.deleted', '=', 0)
            ->and_where('schedules.booking_type', '=', 'Subscription')
            ->execute()
            ->as_array();
        return count($schedules) > 0;
    }

    public static function email_subscription_link($booking_id)
    {
        $booking = DB::select('bookings.*', 'contacts.first_name', 'contacts.last_name')
            ->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('bookings.contact_id', '=', 'contacts.id')
            ->where('bookings.booking_id', '=', $booking_id)
            ->execute()
            ->current();

        $contact = new Model_Contacts3($booking['contact_id']);
        $recipients = array(array('target_type' => 'CMS_CONTACT3', 'target' => $booking['contact_id']));
        if ($primary_contact = $contact->get_primary_contact()) {
            $recipients[] = array('target_type' => 'CMS_CONTACT3', 'target' => $primary_contact);
        }
        $mm = new Model_Messaging();
        $mm->send_template(
            'booking_subscription_confirm',
            null,
            null,
            $recipients,
            array(
                'studentname' => $booking['first_name'] . ' ' . $booking['last_name'],
                'link' => URL::site('/checkout?confirmation=subscription&booking_id=' . $booking_id . '&hash=' . sha1($booking['created_date']))
            )
        );
    }

    public static function cart_items_from_booking($booking_id)
    {
        $items = DB::select(
            'items.*',
            'timeslots.schedule_id',
            DB::expr('IF(timeslots.fee_amount, timeslots.fee_amount, schedules.fee_amount) as fee'),
            DB::expr('IF(schedules.payment_type = 1, 1, 0) as prepay')
        )
            ->from(array(self::BOOKING_ITEMS_TABLE, 'items'))
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('items.period_id', '=', 'timeslots.id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('schedules.id', '=', 'timeslots.schedule_id')
            ->where('items.booking_id', '=', $booking_id)
            ->and_where('items.delete', '=', 0)
            ->and_where('items.booking_status', '<>', 3)
            ->execute()
            ->as_array();
        $booking_items = array();
        foreach ($items as $item) {
            if (!isset($booking_items[$item['schedule_id']])) {
                $booking_items[$item['schedule_id']] = array();
            }
            $booking_items[$item['schedule_id']][$item['period_id']] = array(
                'attending' => 1,
                'note' => '',
                'fee' => $item['fee'],
                'prepay' => $item['prepay'] == 1,
                'number_of_delegates' => 1
            );
        }

        return $booking_items;
    }

    public static function save_card($booking_id, $card_id, $recurring)
    {
        //remove previos linked card to booking
        DB::delete(self::HAS_CARD_TABLE)->where('booking_id', '=', $booking_id)->execute();
        
        DB::insert(self::HAS_CARD_TABLE)
            ->values(
                [
                    'booking_id' => $booking_id,
                    'card_id' => $card_id,
                    'recurring_payments_enabled' => $recurring
                ]
            )->execute();
    }

    public static function get_contact_applications($student_id)
    {
        /*$student = new Model_Contacts3($student_id);
        $family = new Model_Family($student->get_family_id());
        $student->get_family_members()*/
        $selectq = DB::select(
            'bookings.*',
            DB::expr("IFNULL(GROUP_CONCAT(DISTINCT `schedule_courses`.`title`), GROUP_CONCAT(DISTINCT `course_courses`.`title`)) as `course`"),
            DB::expr("IFNULL(schedule_subjects.name, course_subjects.name) as subject"),
            ['stats.title', 'status'],
            'applications.interview_status',
            'applications.data',
            'applications.delegate_id',
            'applications.id'
        )
            ->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(self::BOOKING_APPLICATIONS, 'applications'), 'inner')
                    ->on('bookings.booking_id', '=', 'applications.booking_id')
                ->join(array(self::BOOKING_SCHEDULES, 'has_schedules'), 'left')
                    ->on('bookings.booking_id', '=', 'has_schedules.booking_id')
                    ->on('has_schedules.deleted' , '=', DB::expr("0"))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->join(array(self::BOOKING_COURSES, 'has_courses'), 'left')
                    ->on('bookings.booking_id', '=', 'has_courses.booking_id')
                ->join(array(Model_Courses::TABLE_COURSES, 'schedule_courses'), 'left')
                    ->on('schedules.course_id', '=', 'schedule_courses.id')
                    ->on('schedule_courses.deleted', '=', DB::expr("0"))
                ->join(array(Model_Courses::TABLE_COURSES, 'course_courses'), 'left')
                    ->on('has_courses.course_id', '=', 'course_courses.id')
                    ->on('course_courses.deleted', '=', DB::expr("0"))
                ->join([Model_Courses::TABLE_SUBJECTS, 'schedule_subjects'], 'left')
                    ->on('schedule_courses.subject_id', '=', 'schedule_subjects.id')
                ->join([Model_Courses::TABLE_SUBJECTS, 'course_subjects'], 'left')
                    ->on('course_courses.subject_id', '=', 'course_subjects.id')
                ->join(array(self::BOOKING_STATUS_TABLE, 'stats'), 'left')
                    ->on('applications.status_id', '=', 'stats.status_id')
                ->join(array(self::DELEGATES_TABLE, 'has_delegates'), 'left')
                    ->on('has_delegates.booking_id', '=', 'bookings.booking_id')
                    ->on('has_delegates.deleted', '=', DB::expr(0))
            ->where('bookings.delete', '=', 0)
            ->and_where_open()
                ->or_where('bookings.contact_id', '=', $student_id)
                ->or_where('has_delegates.contact_id', '=' , $student_id)
            ->and_where_close()
            ->group_by('applications.id')
            ->order_by('bookings.modified_date', 'desc');

        $applications = $selectq->execute()->as_array();
        foreach ($applications as $i => $application) {
            $interview_slot = DB::select('timeslots.*')
                ->from(array(self::BOOKING_ITEMS_TABLE, 'items'))
                    ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                        ->on('items.period_id', '=', 'timeslots.id')
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('timeslots.schedule_id', '=', 'schedules.id')
                ->where('items.delete', '=', 0)
                ->and_where('timeslots.delete', '=', 0)
                ->and_where('schedules.is_interview', '=', 1)
                ->and_where('items.booking_id', '=', $application['booking_id'])
                ->execute()
                ->current();
            $applications[$i]['interview_date'] = @$interview_slot['datetime_start'];
            $application_data = json_decode($application['data'], 1);
            $applications[$i]['form_id'] = @$application_data['formbuilder_id'];

            if ($application_data['contact_id'] != $student_id && $application['delegate_id'] != $student_id && $application['contact_id'] != $student_id) {
                unset($applications[$i]);
            }
        }
        return $applications;
    }

    public static function get_application_details_by_booking_id($booking_id)
    {
        $application_id = DB::select('id')
            ->from(self::BOOKING_APPLICATIONS)
            ->where('booking_id', '=', $booking_id)
            ->execute()
            ->get('id');
        return self::get_application_details($application_id);
    }

    public static function get_application_details($application_id)
    {
        // If the project is SLS, retrieve the application data instead of the booking
        $application = (Settings::instance()->get('checkout_customization') == 'sls') ? DB::select(
            'applications.*',
            array('stats.title', 'status'),
            array('applications.data', 'data')
        ) : DB::select(
            'applications.*',
            array('stats.title', 'status'),
            'bookings.extra_data'
        );
            $application = $application->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(self::BOOKING_APPLICATIONS, 'applications'), 'inner')
                    ->on('bookings.booking_id', '=', 'applications.booking_id')
                ->join(array(self::BOOKING_STATUS_TABLE, 'stats'), 'left')
                    ->on('applications.status_id', '=', 'stats.status_id')
            ->where('bookings.delete', '=', 0)
            ->and_where('applications.id', '=', $application_id)
            ->execute()->current();
        $booking_id = $application['booking_id'];
        if ($application) {
            if ($application['extra_data'] || $application['data']) {
                $application['extra_data'] = json_decode($application['extra_data'] ?? $application['data'], true);
            }
            $application['student'] = json_decode($application['student'], true);
            $application['data'] = json_decode($application['data'], true);

            $courses = DB::select(
                'has_courses.*',
                'courses.title',
                array('courses.title', 'course'),
                'courses.fulltime_price'
            )
                ->from(array(self::BOOKING_TABLE, 'bookings'))
                    ->join(array(self::BOOKING_APPLICATIONS, 'applications'), 'inner')
                        ->on('bookings.booking_id', '=', 'applications.booking_id')
                    ->join(array(self::BOOKING_COURSES, 'has_courses'), 'inner')
                        ->on('bookings.booking_id', '=', 'has_courses.booking_id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                        ->on('has_courses.course_id', '=', 'courses.id')
                    ->join(array(self::BOOKING_STATUS_TABLE, 'stats'), 'inner')
                        ->on('applications.status_id', '=', 'stats.status_id')
                ->where('bookings.delete', '=', 0)
                ->and_where('has_courses.deleted', '=', 0)
                ->and_where('courses.deleted', '=', 0)
                ->and_where('bookings.booking_id', '=', $booking_id)
                ->execute()
                ->as_array();

            foreach ($courses as $i => $course) {
                $courses[$i]['paymentoptions'] = DB::select('*')
                    ->from(Model_Courses::TABLE_HAS_PAYMENTOPTIONS)
                    ->where('course_id', '=', $course['course_id'])
                    ->and_where('deleted', '=', 0)
                    ->execute()
                    ->as_array();
            }

            $schedules = DB::select(
                DB::expr('distinct schedules.*'),
                array('courses.title', 'course')
            )
                ->from(array(self::BOOKING_TABLE, 'bookings'))
                    ->join(array(self::BOOKING_APPLICATIONS, 'applications'), 'inner')
                        ->on('bookings.booking_id', '=', 'applications.booking_id')
                    ->join(array(self::BOOKING_COURSES, 'has_courses'), 'inner')
                        ->on('bookings.booking_id', '=', 'has_courses.booking_id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                        ->on('has_courses.course_id', '=', 'courses.id')
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('courses.id', '=', 'schedules.course_id')
                    ->join(array(self::BOOKING_STATUS_TABLE, 'stats'), 'inner')
                        ->on('applications.status_id', '=', 'stats.status_id')
                ->where('bookings.delete', '=', 0)
                ->and_where('has_courses.deleted', '=', 0)
                ->and_where('courses.deleted', '=', 0)
                ->and_where('schedules.delete', '=', 0)
                ->and_where('bookings.booking_id', '=', $booking_id)
                ->execute()
                ->as_array();

            $application['courses'] = $courses;
            $application['schedules'] = array();
            $application['ft_schedules'] = $schedules;

            if ($application['interview_status'] != null || count($application['courses']) > 0) {
                self::tmp_booking_count();
                foreach ($application['courses'] as $i => $course) {
                    $application['courses'][$i]['timeslots_available'] = DB::query(Database::SELECT,
                        "select timeslots.id, timeslots.datetime_start, schedules.`name` as schedule, timeslots.max_capacity, sum(if(items.booking_item_id, 1, 0)) as bcount from plugin_courses_schedules_events timeslots
	left join " . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " items on timeslots.id = items.period_id and timeslots.`delete` = 0 and items.`delete` = 0
	left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id
	left join plugin_courses_schedules_has_courses hc on schedules.id = hc.schedule_id
	where hc.course_id=" . $course['course_id'] . "
	group by timeslots.id
	having bcount < timeslots.max_capacity"
                    )->execute()
                    ->as_array();

                    $application['courses'][$i]['all_timeslots'] = DB::query(Database::SELECT,
                        "select timeslots.id, timeslots.datetime_start, schedules.`name` as schedule, timeslots.max_capacity, sum(if(items.booking_item_id, 1, 0)) as bcount from plugin_courses_schedules_events timeslots
	left join " . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " items on timeslots.id = items.period_id and timeslots.`delete` = 0 and items.`delete` = 0
	left join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id
	left join plugin_courses_schedules_has_courses hc on schedules.id = hc.schedule_id
	where hc.course_id=" . $course['course_id'] . "
	group by timeslots.id"
                    )->execute()
                        ->as_array();
                }
                $application['schedules'] = DB::select(
                    DB::expr('distinct schedules.*'),
                    'timeslots.datetime_start',
                    array('timeslots.id', 'timeslot_id'),
                    'applications.interview_status',
                    array('courses.title', 'course')
                )
                    ->from(array(self::BOOKING_TABLE, 'bookings'))
                        ->join(array(self::BOOKING_APPLICATIONS, 'applications'), 'inner')
                            ->on('bookings.booking_id', '=', 'applications.booking_id')
                        ->join(array(self::BOOKING_COURSES, 'has_courses'), 'inner')
                            ->on('bookings.booking_id', '=', 'has_courses.booking_id')
                        ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                            ->on('has_courses.course_id', '=', 'courses.id')
                        ->join(array(self::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                            ->on('bookings.booking_id', '=', 'items.booking_id')
                        ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                            ->on('items.period_id', '=', 'timeslots.id')
                        ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                            ->on('timeslots.schedule_id', '=', 'schedules.id')
                        ->join(array(Model_Schedules::TABLE_HAS_COURSES, 'has_coursesx'), 'inner')
                            ->on('has_coursesx.schedule_id', '=', 'schedules.id')
                            ->on('has_coursesx.course_id', '=', 'has_courses.course_id')
                    ->join(array(self::BOOKING_STATUS_TABLE, 'stats'), 'inner')
                    ->on('applications.status_id', '=', 'stats.status_id')
                    ->where('bookings.delete', '=', 0)
                    ->and_where('items.delete', '=', 0)
                    ->and_where('timeslots.delete', '=', 0)
                    ->and_where('bookings.booking_id', '=', $booking_id)
                    ->execute()
                    ->as_array();

                foreach ($application['schedules'] as $i => $schedule) {
                    $application['schedules'][$i]['timeslots'] = self::get_schedule_timeslots($schedule['id']);
                }
            }

            $schedule_ids = array();
            foreach ($application['schedules'] as $schedule) {
                $schedule_ids[] = $schedule['id'];
            }

            $application['assigned_schedules'] = DB::select('*')
                ->from(self::BOOKING_SCHEDULES)
                ->where('booking_id', '=', $booking_id)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->as_array();
            foreach ($application['assigned_schedules'] as $i => $assigned_schedule) {
                $application['assigned_schedules'][$i] = $assigned_schedule['schedule_id'];
            }
        }

        return $application;
    }

    public static function clone_interview($booking_id, $course_id, $schedule_id, $timeslot_id, $interview_status, $cancel_previos, $send_email)
    {
        if ($interview_status == '') {
            $interview_status = 'Not Scheduled';
        }
        $booking = DB::select('*')
            ->from(self::BOOKING_TABLE)
            ->where('booking_id', '=', $booking_id)
            ->execute()
            ->current();
        unset ($booking['booking_id']);
        $booking['created_date'] = $booking['modified_date'] = date::now();
        $inserted = DB::insert(self::BOOKING_TABLE)->values($booking)->execute();
        $new_booking_id = $inserted[0];
        if ($inserted) {
            $application = DB::select('*')
                ->from(self::BOOKING_APPLICATIONS)
                ->where('booking_id', '=', $booking_id)
                ->execute()
                ->current();
            $application['booking_id'] = $new_booking_id;
            $application['interview_status'] = $interview_status;
            DB::insert(self::BOOKING_APPLICATIONS)
                ->values($application)
                ->execute();
            DB::insert(self::BOOKING_COURSES)
                ->values(array(
                    'booking_id' => $new_booking_id,
                    'course_id' => $course_id,
                    'deleted' => 0,
                    'booking_status' => 2
                ))->execute();
            if ($schedule_id) {
                DB::insert(self::BOOKING_SCHEDULES)
                    ->values(array(
                        'booking_id' => $new_booking_id,
                        'schedule_id' => $schedule_id,
                        'deleted' => 0,
                        'publish' => 1,
                        'booking_status' => 2
                    ))->execute();
            }
            if ($timeslot_id) {
                DB::insert(self::BOOKING_ITEMS_TABLE)
                    ->values(
                        array(
                            'booking_id' => $new_booking_id,
                            'period_id' => $timeslot_id,
                            'attending' => 1,
                            'delete' => 0,
                            'date_created' => $booking['created_date'],
                            'booking_status' => 2,

                        )
                    )->execute();
            }

            if ($cancel_previos) {
                DB::update(self::BOOKING_TABLE)
                    ->set(array('booking_status' => 3))
                    ->where('booking_id', '=', $booking_id)
                    ->execute();
                DB::update(self::BOOKING_COURSES)
                    ->set(array('booking_status' => 3))
                    ->where('booking_id', '=', $booking_id)
                    ->execute();
                DB::update(self::BOOKING_ITEMS_TABLE)
                    ->set(array('booking_status' => 3))
                    ->where('booking_id', '=', $booking_id)
                    ->execute();
            }
        }
    }

    public static function update_application($post)
    {
        ignore_user_abort(1);
        set_time_limit(0);
        $booking_id = @$post['booking_id'];
        $application_id = $post['application_id'];
        $contact_id = null;
        if ($application_id) {
            $booking_id = DB::select('booking_id')
                ->from(SELF::BOOKING_APPLICATIONS)
                ->where('id', '=', $application_id)
                ->execute()
                ->get('booking_id');
        }
        $booking = DB::select('*')
            ->from(self::BOOKING_TABLE)
            ->where('booking_id', '=', $booking_id)
            ->execute()
            ->current();
        $contact_id = $booking['contact_id'];
        $update = $post['update'];
        $auth = Auth::instance();
        $user = $auth->get_user();

        $extra_data = null;
        if (@$post['interview_status']) {
            foreach ($post['work_experience'] as $i => $we) {
                if ($we['details'] == '') {
                    unset($post['work_experience'][$i]);
                }
            }
            $post['work_experience'] = array_values($post['work_experience']);

            foreach ($post['subjects'] as $i => $we) {
                if ($we['name'] == '') {
                    unset($post['subjects'][$i]);
                }
            }
            $post['subjects'] = array_values($post['subjects']);

            $application = $post;
        } else {
            $application = @$post['application'];
            if (!$application) {
                $application = $post;
                $extra_data = $post;
            }
        }

        if ($update == 'create') {
            try {
                $ftapplication = array(
                    'data' => json_encode($application, defined("JSON_PRETTY_PRINT") ? JSON_PRETTY_PRINT : 0),
                    'status_id' => $post['create_ftcourse_transaction'] == 'Yes' ? 1 : 1
                );

                Database::instance()->begin();
                $binserted = DB::insert(self::BOOKING_TABLE)
                    ->values(array(
                        'contact_id' => $post['contact_id'],
                        'booking_status' => 1,
                        'created_date' => date::now(),
                        'modified_date' => date::now(),
                        'publish' => 1,
                        'delete' => 0,
                        'amount' => $post['create_ftcourse_transaction'] == 'Yes' ? $post['ftcourse_transaction_amount'] : 0,
                        'extra_data' => json_encode($extra_data, JSON_PRETTY_PRINT)
                    ))->execute();
                $booking_id = $binserted[0];

                $ftapplication['booking_id'] = $booking_id;
                DB::insert(self::BOOKING_APPLICATIONS)
                    ->values($ftapplication)
                    ->execute();

                if (@$post['fulltime_course_id']) {
                    $cinserted = DB::insert(self::BOOKING_COURSES)
                        ->values(array(
                            'booking_id' => $booking_id,
                            'course_id' => $post['fulltime_course_id'],
                            'deleted' => 0,
                            'booking_status' => 1
                        ))->execute();
                }

                if (@$post['has_course_id']) {
                    if (isset($post['move']) && $post['move'] == 1) {
                        $delete = DB::delete(self::BOOKING_COURSES)
                                ->where('booking_id', '=', $booking_id);
                    }
                    $cinserted = DB::insert(self::BOOKING_COURSES)
                        ->values(array(
                                'booking_id' => $booking_id,
                                'course_id' => $post['has_course_id'],
                                'deleted' => 0,
                                'booking_status' => 1
                        ))->execute();



                }

                if ($post['create_ftcourse_transaction'] == 'Yes') {
                    $tinserted = DB::insert(Model_Kes_Transaction::TRANSACTION_TABLE)
                        ->values(array(
                            'booking_id' => $booking_id,
                            'amount' => $post['ftcourse_transaction_amount'],
                            'discount' => 0,
                            'fee' => 0,
                            'total' => $post['ftcourse_transaction_amount'],
                            'type' => 1,
                            'created' => date::now(),
                            'updated' => date::now(),
                            'deleted' => 0,
                            'contact_id' => $post['contact_id'],
                            'created_by' => $user['id'],
                            'modified_by' => $user['id'],
                            'payment_due_date' => date::today()
                        ))->execute();

                    $transaction_id = $tinserted[0];

                    DB::insert(Model_Kes_Transaction::TABLE_HAS_COURSES)
                        ->values(array(
                            'transaction_id' => $transaction_id,
                            'course_id' => $post['fulltime_course_id']
                        ))->execute();

                    Model_Automations::run_triggers(Model_Bookings_Transactionsavetrigger::NAME, array('transaction_id' => $transaction_id));
                }
                Database::instance()->commit();
                return $booking_id;
            } catch (Exception $exc) {
                Database::instance()->rollback();
                throw $exc;
            }
        }

        if ($update == 'cancel') {
            DB::update(Model_KES_Bookings::BOOKING_APPLICATIONS)
                ->set(array('status_id' => 3))
                ->where('booking_id', '=', $booking_id)
                ->execute();

            $transactions = DB::select('*')
                ->from(Model_Kes_Transaction::TRANSACTION_TABLE)
                ->where('booking_id', '=', $booking_id)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->as_array();
            if (count($transactions) == 0) {
                //cancel booking if there is no transaction after cancelling application
                DB::update(Model_KES_Bookings::BOOKING_TABLE)
                    ->set(array('booking_status' => 3, 'modified_date' => date::now()))
                    ->where('booking_id', '=', $booking_id)
                    ->execute();
            } else {
                DB::update(Model_KES_Bookings::BOOKING_TABLE)
                    ->set(array('modified_date' => date::now()))
                    ->where('booking_id', '=', $booking_id)
                    ->execute();
            }
            return true;
        }

        try {
            Database::instance()->begin();
            $values = array('data' => json_encode($application, defined("JSON_PRETTY_PRINT") ? JSON_PRETTY_PRINT : 0));
            if ($update == 'approve') {
                $values['status_id'] = 2;
            }
            if (@$post['interview_status']) {
                $values['interview_status'] = $post['interview_status'];

                if (@$post['timeslot_id_replace']) {
                    if ($post['timeslot_id_replace']['old'] != $post['timeslot_id_replace']['new']) {
                        if ($post['timeslot_id_replace']['old']) {
                            if ($post['timeslot_id_replace']['new']) {
                                $old_schedule_id = DB::select('schedule_id')
                                    ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                                    ->where('id', '=', $post['timeslot_id_replace']['old'])
                                    ->execute()
                                    ->get('schedule_id');
                                $new_schedule_id = DB::select('schedule_id')
                                    ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                                    ->where('id', '=', $post['timeslot_id_replace']['new'])
                                    ->execute()
                                    ->get('schedule_id');
                                DB::update(self::BOOKING_ITEMS_TABLE)
                                    ->set(array('period_id' => $post['timeslot_id_replace']['new']))
                                    ->where('booking_id', '=', $booking_id)
                                    ->and_where('period_id', '=', $post['timeslot_id_replace']['old'])
                                    ->execute();
                                DB::update(self::BOOKING_SCHEDULES)
                                    ->set(array('schedule_id' => $new_schedule_id))
                                    ->where('schedule_id', '=', $old_schedule_id)
                                    ->and_where('booking_id', '=', $booking_id)
                                    ->execute();
                                if ($post['interview_status'] == 'Scheduled' && @$post['send_email'] == 'yes') {
                                    self::send_interview_schedule_email($booking_id);
                                }
                            } else {
                                DB::update(self::BOOKING_ITEMS_TABLE)
                                    ->set(array('delete' => 1))
                                    ->where('booking_id', '=', $booking_id)
                                    ->and_where('period_id', '=', $post['timeslot_id_replace']['old'])
                                    ->execute();
                            }
                        } else {
                            $inserted = DB::insert(self::BOOKING_ITEMS_TABLE)
                                ->values(array('period_id' => $post['timeslot_id_replace']['new'], 'booking_id' => $booking_id, 'booking_status' => 2))
                                ->execute();
                            if ($post['interview_status'] == 'Scheduled' && @$post['send_email'] == 'yes') {
                                self::send_interview_schedule_email($booking_id);
                            }
                        }
                    }
                }

                if (@$post['interview_transfer_to_course_id']) {
                    self::clone_interview(
                        $booking_id,
                        $post['interview_transfer_to_course_id'],
                        @$post['interview_transfer_to_schedule_id'],
                        @$post['interview_transfer_to_timeslot_id'],
                        $post['interview_transfer_interview_status'],
                        true,
                        @$post['send_email'] == 'yes'
                    );
                }
            }

            DB::update(Model_KES_Bookings::BOOKING_APPLICATIONS)
                ->set($values)
                ->where('booking_id', '=', $booking_id)
                ->execute();
    
            // Update course ID if user has changed it when saving or approving
            if(@$post['has_course_id']){
                if (isset($post['move']) && $post['move'] == 1) {
                    $delete = DB::delete(self::BOOKING_COURSES)
                        ->where('booking_id', '=', $booking_id)->execute();
                    $cinserted = DB::insert(self::BOOKING_COURSES)
                        ->values(array(
                            'booking_id' => $booking_id,
                            'course_id' => $post['has_course_id'],
                            'deleted' => 0,
                            'booking_status' => 1
                        ))->execute();
                    if (is_string($application['data'])) {
                        $application['data'] = json_decode($application['data'], true);
                    }
                    self::assign_application_schedules($booking_id, array($post['has_schedule_id']), isset($application['data']['has_period']) ? $application['data']['has_period'] : @$application['has_period'], $contact_id);
                } else {
                    DB::update(Model_KES_Bookings::BOOKING_COURSES)
                        ->set(array('course_id' => $post['has_course_id']))
                        ->where('booking_id', '=', $booking_id)
                        ->execute();
                }
                $application = DB::select('*')
                    ->from(array(self::BOOKING_APPLICATIONS, 'applications'))
                    ->where('applications.id', '=', $application_id)
                    ->execute()->current();
                if (!empty($application) && !empty($application['data'])) {
                    $application_data = json_decode($application['data'], 1);
                    if (!empty($application_data['has_course_id'])) {
                        $application_data['has_course_id'] = $post['has_course_id'];
                    }
                    if (!empty($application_data['has_schedule_id'])) {
                        $application_data['has_schedule_id'] = $post['has_schedule_id'];
                        $application_data['schedule_id'] = $post['has_schedule_id'];
                    }
                    $application_object = new Model_Booking_Application($application_id);
                    $application_object->save_with_history('data', json_encode($application_data));
                }
            }

            if(@$post['fulltime_course_id']){
                DB::update(Model_KES_Bookings::BOOKING_COURSES)
                    ->set(array('course_id' => $post['fulltime_course_id']))
                    ->where('booking_id', '=', $booking_id)
                    ->execute();
            }
    
            if (isset($post['create_transaction']) && $post['create_transaction'] == 'Yes' && (float)$post['transaction_amount'] > 0) {
                $tinserted = DB::insert(Model_Kes_Transaction::TRANSACTION_TABLE)
                    ->values(array(
                        'booking_id' => $booking_id,
                        'amount' => $post['transaction_amount'],
                        'discount' => 0,
                        'fee' => 0,
                        'total' => $post['transaction_amount'],
                        'type' => 1,
                        'created' => date::now(),
                        'updated' => date::now(),
                        'deleted' => 0,
                        'contact_id' => $post['contact_id'],
                        'created_by' => $user['id'],
                        'modified_by' => $user['id'],
                        'payment_due_date' => date::today()
                    ))->execute();

                $transaction_id = $tinserted[0];

                if (@$post['has_schedule_id'] > 0) {
                    DB::insert(Model_Kes_Transaction::TABLE_HAS_SCHEDULES)
                        ->values(array(
                            'transaction_id' => $transaction_id,
                            'schedule_id' => $post['has_schedule_id']
                        ))->execute();
                } else {
                    DB::insert(Model_Kes_Transaction::TABLE_HAS_COURSES)
                        ->values(array(
                            'transaction_id' => $transaction_id,
                            'course_id' => $post['has_course_id']
                        ))->execute();
                }

                Model_Automations::run_triggers(Model_Bookings_Transactionsavetrigger::NAME, array('transaction_id' => $transaction_id));
            }
            if (@$post['interview_status'] == null) {
                if (is_string($application['data'])) {
                    $application['data'] = json_decode($application['data'], true);
                }
                if (@$values['status_id'] != 3) {
                    if (@$post['has_schedule_id'] > 0) {
                        self::assign_application_schedules($booking_id, array($post['has_schedule_id']), isset($application['data']['has_period']) ? $application['data']['has_period'] : @$application['has_period'], $contact_id);
                    }
                    if (@$post['has_schedule']) {
                        self::assign_application_schedules($booking_id, @$post['has_schedule'], isset($application['data']['has_period']) ? $application['data']['has_period'] : @$application['has_period'], $contact_id);
                    }
                }

                $application = self::get_application_details($application_id);
                $booking = DB::select('*')
                    ->from(self::BOOKING_TABLE)
                    ->where('booking_id', '=', $booking_id)
                    ->execute()
                    ->current();
                $extra_data = array(
                    'has_schedule_id' => $post['has_schedule_id']
                );
                DB::update(self::BOOKING_TABLE)
                    ->set(array('extra_data' => json_encode($extra_data), 'modified_date' => date::now()))
                    ->where('booking_id', '=', $booking_id)
                    ->execute();
                if ($update == 'approve') {
                    DB::update(self::BOOKING_TABLE)
                        ->set(array('booking_status' => 2, 'modified_date' => date::now()))
                        ->where('booking_id', '=', $booking_id)
                        ->execute();
                    DB::update(self::BOOKING_SCHEDULES)
                        ->set(array('booking_status' => 2))
                        ->where('booking_id', '=', $booking_id)
                        ->execute();
                    DB::update(self::BOOKING_COURSES)
                        ->set(array('booking_status' => 2))
                        ->where('booking_id', '=', $booking_id)
                        ->execute();

                    if (@$post['fulltime_course_id']) {
                        if (!isset($application['courses'])) {
                            $application['courses'] = array(
                                array(
                                    'course_id' => $post['fulltime_course_id'],
                                    'fulltime_price' => $post['ftcourse_transaction_amount']
                                )
                            );
                        }
                        self::create_fulltime_course_transaction($booking, $application);
                    }
                    $doc_helper = new Model_Docarrayhelper();
                    $doc1 = null;
                    try {
                        $doc1 = ORM::factory('Document')
                            ->auto_generate_document($doc_helper->booking_document($booking_id), 0, true);
                    } catch (Exception $exc) {

                    }
                }
                
                if (@$post['send_email'] == 'yes') {
                    self::send_application_approve_email($booking, $application);
                }
            }

            Database::instance()->commit();
            return $booking_id;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function assign_application_schedules($booking_id, $schedule_ids, $periods = array(), $contact_id = null)
    {
        if (is_array($schedule_ids)) {
            db::update(self::BOOKING_SCHEDULES)
                ->set(array('deleted' => 1))
                ->where('booking_id', '=', $booking_id)
                ->and_where('schedule_id', 'not in', $schedule_ids)
                ->execute();
            DB::update(self::BOOKING_ITEMS_TABLE)
                ->set(array('delete' => 1))
                ->where('booking_id', '=', $booking_id)
                ->execute();
            DB::update(self::BOOKING_ROLLCALL_TABLE)
                ->set(array('delete' => 1))
                ->where('booking_id', '=', $booking_id)
                ->execute();
            foreach ($schedule_ids as $schedule_id) {
                $exists = DB::select('*')
                    ->from(self::BOOKING_SCHEDULES)
                    ->where('booking_id', '=', $booking_id)
                    ->and_where('schedule_id', '=', $schedule_id)
                    ->and_where('deleted', '=', 0)
                    ->execute()
                    ->current();
                if (!$exists) {
                    DB::insert(self::BOOKING_SCHEDULES)
                        ->values(
                            array(
                                'booking_id' => $booking_id,
                                'schedule_id' => $schedule_id,
                                'booking_status' => 2
                            )
                        )
                        ->execute();
                } else {
                    DB::update(self::BOOKING_SCHEDULES)
                        ->set(
                            array(
                                'booking_status' => 2,
                                'deleted' => 0
                            )
                        )
                        ->where('booking_id', '=', $booking_id)
                        ->and_where('schedule_id', '=', $schedule_id)
                        ->execute();
                }

                $timeslotsq = DB::select('*')
                    ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                    ->where('schedule_id', '=', $schedule_id)
                    ->and_where('delete', '=', 0)
                    ->order_by('datetime_start', 'asc');
                if (!empty($periods)) {
                    $timeslotsq->and_where(DB::expr("CONCAT_WS(',', DATE_FORMAT(datetime_start, '%a %H:%i'), trainer_id)"), 'in', $periods);
                }
                $timeslots = $timeslotsq->execute()->as_array();
                foreach ($timeslots as $timeslot) {
                    $item_inserted = DB::insert(self::BOOKING_ITEMS_TABLE)
                        ->values(
                            array(
                                'booking_id' => $booking_id,
                                'period_id' => $timeslot['id'],
                                'attending' => 1,
                                'booking_status' => 2
                            )
                        )
                        ->execute();
                    DB::insert(self::BOOKING_ROLLCALL_TABLE)
                        ->values(
                            array(
                                'booking_id' => $booking_id,
                                'booking_item_id' => $item_inserted[0],
                                'delegate_id' => $contact_id,
                                'timeslot_id' => $timeslot['id'],
                                'planned_to_attend' => 1,
                                'booking_status' => 2
                            )
                        )
                        ->execute();
                }
            }
        } else {
            db::update(self::BOOKING_SCHEDULES)
                ->set(array('deleted' => 1))
                ->where('booking_id', '=', $booking_id)
                ->execute();
        }
        Database::instance()->commit();
    }

    public static function send_interview_schedule_email($booking_id)
    {
        $booking = DB::select('*')
            ->from(self::BOOKING_TABLE)
            ->where('booking_id', '=', $booking_id)
            ->execute()
            ->current();
        $timeslot = DB::select('datetime_start', array('locations.name', 'room'), array('plocations.name', 'location'))
            ->from(array(self::BOOKING_ITEMS_TABLE, 'items'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('items.period_id', '=', 'timeslots.id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('timeslots.schedule_id', '=', 'schedules.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'inner')->on('schedules.location_id', '=', 'locations.id')
            ->join(array(Model_Locations::TABLE_LOCATIONS, 'plocations'), 'left')->on('locations.parent_id', '=', 'plocations.id')
            ->where('booking_id', '=', $booking_id)
            ->and_where('items.delete', '=', 0)
            ->order_by('timeslots.datetime_start')
            ->execute()
            ->current();
        $datetime = $timeslot['datetime_start'];

        $course = DB::select('courses.*')
            ->from(array(self::BOOKING_COURSES, 'has_courses'))
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')->on('has_courses.course_id', '=', 'courses.id')
            ->where('booking_id', '=', $booking_id)
            ->execute()
            ->current();
        $application = self::get_application_details_by_booking_id($booking_id);


        $student = new Model_Contacts3($booking['contact_id']);

        $recipients = array();
        $recipients[] = array(
            'target_type' => 'CMS_CONTACT3',
            'target' => $booking['contact_id']
        );

        $parameters = array(
            'student' => $student->get_first_name() . ' ' . $student->get_last_name(),
            'course' => $course['title'],
            'code' => $course['code'],
            'interview_date' => date('d/m/Y H:i', strtotime($datetime)),
            'date' => date('d/m/Y'),
            'location' => $timeslot['location'] ? $timeslot['location'] : $timeslot['room']
        );

        $mm = new Model_Messaging();
        return $mm->send_template(
            'course-interview-schedule',
            null,
            null,
            $recipients,
            $parameters
        );
    }

    public static function send_application_approve_email($booking, $application)
    {
        $student = new Model_Contacts3($booking['contact_id']);
        $family = new Model_Family($student->get_family_id());
        $guardian = new Model_Contacts3($family->get_primary_contact_id());

        $recipients = array();
        $recipients[] = array(
            'target_type' => 'CMS_CONTACT3',
            'target' => $guardian->get_id() ? $guardian->get_id() : $student->get_id()
        );

        $courses = array();
        foreach ($application['courses'] as $course) {
            $courses[] = $course['course'];
        }

        $parameters = array(
            'name' => $student->get_first_name() . ' ' . $student->get_last_name(), //$guardian,$student,$course,$link'
            'student' => $student->get_first_name() . ' ' . $student->get_last_name(), //$guardian,$student,$course,$link'
            'guardian' => $guardian->get_first_name() . ' ' . $guardian->get_last_name(),
            'course' => implode(', ', $courses),
            'link' => URL::site('/frontend/bookings/pay_application_fee?booking_id=' . $booking['booking_id'] . '&hash=' . sha1($booking['created_date']))
        );
        $mm = new Model_Messaging();
        $mm->send_template(
            'fulltime-course-application-approved-customer',
            null,
            null,
            $recipients,
            $parameters
        );
    }

    public static function create_fulltime_course_transaction($booking, $application)
    {
        $user = Auth::instance()->get_user();
        $student = new Model_Contacts3($booking['contact_id']);
        $family = new Model_Family($student->get_family_id());
        $guardian = new Model_Contacts3($family->get_primary_contact_id());

        $tx = array(
            'booking_id' => $booking['booking_id']
        );

        foreach ($application['courses'] as $course) {
            if ((float)$course['fulltime_price'] == 0) {
                continue;
            }
            $tx['amount'] = $course['fulltime_price'];
            $tx['fee'] = 0;
            $tx['total'] = $course['fulltime_price'];
            $tx['type'] = 1;
            $tx['contact_id'] = $booking['contact_id'];
            $tx['family_id'] = $student->get_family_id();
            $tx['created_by'] = $user['id'];
            $tx['created'] = date::now();
            $tx['modified_by'] = $user['id'];
            $tx['updated'] = date::now();
            $tx['discount'] = 0;
            $tx['payment_due_date'] = date::now();

            $inserted = DB::insert(Model_Kes_Transaction::TRANSACTION_TABLE)
                ->values($tx)
                ->execute();

            DB::insert(Model_Kes_Transaction::TABLE_HAS_COURSES)
                ->values(array('transaction_id' => $inserted[0], 'course_id' => $course['course_id']))
                ->execute();

            Model_Automations::run_triggers(Model_Bookings_Transactionsavetrigger::NAME, array('transaction_id' => $inserted[0]));
        }
    }

    public static function get_booking_hash($booking_id, $hash)
    {
        $booking = DB::select('*')
            ->from(self::BOOKING_TABLE)
            ->where('booking_id', '=', $booking_id)
            ->and_where(DB::expr("sha1(created_date)"), '=', $hash)
            ->execute()
            ->current();
        return $booking;
    }

    public static function calculate_outstanding_tmp_rollcall($schedule_id)
    {
        DB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat')->execute();
        DB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_tx_stat')->execute();
        DB::query(null, 'CREATE TEMPORARY TABLE tmp_tx_stat
(SELECT
		tx.id,
		tx.booking_id,
		tx.total,
		tt.credit,
		SUM(IFNULL(py.amount,0)) AS pyt,
		SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS pyx,
		tx.total - SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS outstanding
	FROM plugin_bookings_transactions tx
		INNER JOIN plugin_bookings_transactions_has_schedule has ON tx.id = has.transaction_id AND has.schedule_id = ' . $schedule_id . '
		LEFT JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id
		LEFT JOIN plugin_bookings_transactions_payments py ON tx.id = py.transaction_id AND py.deleted = 0
		LEFT JOIN plugin_bookings_transactions_payments_statuses ps ON py.`status` = ps.id
	GROUP BY tx.id
	HAVING outstanding <> 0)')->execute();
        DB::query(null, 'ALTER TABLE tmp_tx_stat ADD KEY (`booking_id`)')->execute();
        DB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat')->execute();
        DB::query(null, 'CREATE TEMPORARY TABLE tmp_account_stat
(SELECT
		bk.booking_id,
		SUM(IFNULL(IF(tx.credit > 0, tx.outstanding, -tx.outstanding), 0)) AS outstanding
	FROM plugin_ib_educate_bookings bk
		LEFT JOIN tmp_tx_stat tx ON bk.booking_id = tx.booking_id
	GROUP BY bk.booking_id)')->execute();
    }

    private static function analytics_set_params($selectq, $params)
    {
        if (@$params['before']) {
            $selectq->and_where('timeslots.datetime_start', '<=', $params['before']);
        }
        if (@$params['after']) {
            $selectq->and_where('timeslots.datetime_start', '>=', $params['after']);
        }
        if (@$params['trainer_id']) {
            $selectq->and_where_open();
            $selectq->or_where('timeslots.trainer_id', '=', $params['trainer_id']);
            $selectq->or_where('schedules.trainer_id', '=', $params['trainer_id']);
            $selectq->and_where_close();
        }
        if (@$params['timeslot_id']) {
            $selectq->and_where('timeslots.id', '=', $params['timeslot_id']);
        }
    }

    public static function analytics($params = array())
    {
        $tmp_tx_stat_select = DB::select()
            ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'tx'))
            ->join(array(Model_Kes_Transaction::TYPE_TABLE, 'tt'), 'left')->on('tx.type', '=', 'tt.id')
            ->join(array(Model_Kes_Payment::PAYMENT_TABLE, 'py'), 'left')->on('tx.id', '=', 'py.transaction_id')->on('py.deleted', '=', DB::expr(0))
            ->join(array(Model_Kes_Payment::STATUS_TABLE, 'ps'), 'left')->on('py.status', '=', 'ps.id')
            ->where('tx.deleted', '=', 0);

        DB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat')->execute();
        DB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_tx_stat')->execute();
        DB::query(null, 'CREATE TEMPORARY TABLE tmp_tx_stat
(SELECT
		tx.id,
		tx.booking_id,
		tx.total,
		tt.credit,
		SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS paid,
		hs.schedule_id,
		hs.event_id as timeslot_id,
		hs.payg_period,
		s.payment_type,
		s.rental_fee
	FROM plugin_bookings_transactions tx
		LEFT JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id
		LEFT JOIN plugin_bookings_transactions_payments py ON tx.id = py.transaction_id AND py.deleted = 0
		LEFT JOIN plugin_bookings_transactions_payments_statuses ps ON py.`status` = ps.id
		LEFT JOIN plugin_bookings_transactions_has_schedule hs ON tx.id = hs.transaction_id AND hs.deleted = 0
		LEFT JOIN plugin_courses_schedules s on hs.schedule_id = s.id
    WHERE tx.deleted = 0
    ' . (@$params['timeslot_id'] ? ' AND (hs.event_id=' . $params['timeslot_id'] . ' OR s.payment_type = 1)' : '') // payg timeslot linked transaction or prepay schedule
    .
    '	GROUP BY tx.id, hs.schedule_id)')->execute();

        $stats = array();
        $stats['category'] = array('data' => array(), 'total_minutes' => 0, 'total_quantity' => 0);
        $stats['subject'] = array('data' => array(), 'total_minutes' => 0, 'total_quantity' => 0);
        $stats['course'] = array('data' => array(), 'total_minutes' => 0, 'total_quantity' => 0);
        $stats['transaction'] = array('total' => 0, 'paid' => 0);

        $booking_ids = array();
        $schedule_ids = array();

        $selectq = DB::select(
            'courses.id',
            array('courses.title', 'name'),
            DB::expr("SUM(timeslots.datetime_end - timeslots.datetime_start) as minutes"),
            DB::expr("GROUP_CONCAT(distinct schedules.id) as schedule_ids"),
            DB::expr("GROUP_CONCAT(distinct bitems.booking_id) as booking_ids")
        )
            ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'bitems'))
            ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('bitems.period_id', '=', 'timeslots.id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')->on('courses.id', '=', 'schedules.course_id')
            ->where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0)
            ->and_where('bitems.delete', '=', 0)
            ->group_by('courses.id');
        self::analytics_set_params($selectq, $params);
        
        $stats['course']['data'] = $selectq->execute()->as_array();
        foreach ($stats['course']['data'] as $i => $row) {
            $stats['course']['data'][$i]['minutes'] = self::mysql_duration_to_minutes($row['minutes']);
            $stats['course']['data'][$i]['schedule_ids'] = $row['schedule_ids'] ? explode(',', $row['schedule_ids']) : array();
            $stats['course']['data'][$i]['booking_ids'] = $row['booking_ids'] ? explode(',', $row['booking_ids']) : array();
            $stats['course']['data'][$i]['quantity'] = count($stats['course']['data'][$i]['booking_ids']);
            $stats['course']['data'][$i]['income'] = 0;
            $stats['course']['total_minutes'] += $stats['course']['data'][$i]['minutes'];
            $stats['course']['total_quantity'] += $stats['course']['data'][$i]['quantity'];

            $booking_ids = array_merge($stats['course']['data'][$i]['booking_ids'], $booking_ids);
            $schedule_ids = array_merge($stats['course']['data'][$i]['schedule_ids'], $schedule_ids);
        }

        $selectq = DB::select(
            'categories.id',
            array('categories.category', 'name'),
            array('categories.file_id', 'image'),
            DB::expr("SUM(timeslots.datetime_end - timeslots.datetime_start) as minutes"),
            DB::expr("GROUP_CONCAT(distinct schedules.id) as schedule_ids"),
            DB::expr("GROUP_CONCAT(distinct bitems.booking_id) as booking_ids")
        )
            ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'bitems'))
            ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('bitems.period_id', '=', 'timeslots.id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')->on('courses.id', '=', 'schedules.course_id')
            ->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')->on('categories.id', '=', 'courses.category_id')
            ->where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0)
            ->and_where('bitems.delete', '=', 0)
            ->group_by('categories.id');
        self::analytics_set_params($selectq, $params);
        $stats['category']['data'] = $selectq->execute()->as_array();
        foreach ($stats['category']['data'] as $i => $row) {
            $stats['category']['data'][$i]['minutes'] = self::mysql_duration_to_minutes($row['minutes']);
            $stats['category']['data'][$i]['schedule_ids'] = $row['schedule_ids'] ? explode(',', $row['schedule_ids']) : array();
            $stats['category']['data'][$i]['booking_ids'] = $row['booking_ids'] ? explode(',', $row['booking_ids']) : array();
            $stats['category']['data'][$i]['quantity'] = count($stats['category']['data'][$i]['booking_ids']);
            $stats['category']['data'][$i]['income'] = 0;
            $stats['category']['total_minutes'] += $stats['category']['data'][$i]['minutes'];
            $stats['category']['total_quantity'] += $stats['category']['data'][$i]['quantity'];

            $booking_ids = array_merge($stats['category']['data'][$i]['booking_ids'], $booking_ids);
            $schedule_ids = array_merge($stats['category']['data'][$i]['schedule_ids'], $schedule_ids);
        }

        $selectq = DB::select(
            'subjects.id',
            'subjects.name',
            'subjects.color',
            DB::expr("SUM(timeslots.datetime_end - timeslots.datetime_start) as minutes"),
            DB::expr("GROUP_CONCAT(distinct schedules.id) as schedule_ids"),
            DB::expr("GROUP_CONCAT(distinct bitems.booking_id) as booking_ids")
        )
            ->from(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'bitems'))
            ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('bitems.period_id', '=', 'timeslots.id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')->on('courses.id', '=', 'schedules.course_id')
            ->join(array(Model_Subjects::TABLE_SUBJECTS, 'subjects'), 'left')->on('subjects.id', '=', 'courses.subject_id')
            ->where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0)
            ->and_where('bitems.delete', '=', 0)
            ->group_by('subjects.id');
        self::analytics_set_params($selectq, $params);
        $stats['subject']['data'] = $selectq->execute()->as_array();
        foreach ($stats['subject']['data'] as $i => $row) {
            $stats['subject']['data'][$i]['minutes'] = self::mysql_duration_to_minutes($row['minutes']);
            $stats['subject']['data'][$i]['schedule_ids'] = $row['schedule_ids'] ? explode(',', $row['schedule_ids']) : array();
            $stats['subject']['data'][$i]['booking_ids'] = $row['booking_ids'] ? explode(',', $row['booking_ids']) : array();
            $stats['subject']['data'][$i]['quantity'] = count($stats['subject']['data'][$i]['booking_ids']);
            $stats['subject']['data'][$i]['income'] = 0;
            $stats['subject']['total_minutes'] += $stats['subject']['data'][$i]['minutes'];
            $stats['subject']['total_quantity'] += $stats['subject']['data'][$i]['quantity'];

            $booking_ids = array_merge($stats['subject']['data'][$i]['booking_ids'], $booking_ids);
            $schedule_ids = array_merge($stats['subject']['data'][$i]['schedule_ids'], $schedule_ids);
        }

        $booking_ids = array_unique($booking_ids);
        $schedule_ids = array_unique($schedule_ids);

        if (count($booking_ids) > 0 && count($schedule_ids) > 0) {
            $tx_stats = DB::select(
                'tmp_tx_stat.booking_id',
                'tmp_tx_stat.schedule_id',
                DB::expr("SUM(tmp_tx_stat.total) as total"),
                DB::expr("SUM(tmp_tx_stat.paid) as paid"),
                'tmp_tx_stat.rental_fee',
                'tmp_tx_stat.timeslot_id',
		        'tmp_tx_stat.payg_period',
		        'tmp_tx_stat.rental_fee',
                'tmp_tx_stat.payment_type'
            )
                ->from('tmp_tx_stat')
                ->where('tmp_tx_stat.booking_id', 'in', $booking_ids)
                ->and_where('tmp_tx_stat.schedule_id', 'in', $schedule_ids)
                ->group_by('tmp_tx_stat.booking_id')
                ->group_by('tmp_tx_stat.schedule_id')
                ->execute()
                ->as_array();
        } else {
            $tx_stats = array();
        }

        foreach ($tx_stats as $tx_stat) {
            foreach ($stats['course']['data'] as $i => $row) {
                if (in_array($tx_stat['booking_id'], $row['booking_ids']) && in_array($tx_stat['schedule_id'], $row['schedule_ids'])) {
                    if ((float)$tx_stat['rental_fee'] > 0) {
                        $stats['course']['data'][$i]['income'] += round($tx_stat['total'] * ($tx_stat['rental_fee'] / 100.0), 2);
                    } else {
                        $stats['course']['data'][$i]['income'] += $tx_stat['total'];
                    }
                }
            }

            foreach ($stats['category']['data'] as $i => $row) {
                if (in_array($tx_stat['booking_id'], $row['booking_ids']) && in_array($tx_stat['schedule_id'], $row['schedule_ids'])) {
                    if ((float)$tx_stat['rental_fee'] > 0) {
                        $stats['category']['data'][$i]['income'] += round($tx_stat['total'] * ($tx_stat['rental_fee'] / 100.0), 2);
                    } else {
                        $stats['category']['data'][$i]['income'] += $tx_stat['total'];
                    }
                }
            }

            foreach ($stats['subject']['data'] as $i => $row) {
                if (in_array($tx_stat['booking_id'], $row['booking_ids']) && in_array($tx_stat['schedule_id'], $row['schedule_ids'])) {
                    if ((float)$tx_stat['rental_fee'] > 0) {
                        $stats['subject']['data'][$i]['income'] += round($tx_stat['total'] * ($tx_stat['rental_fee'] / 100.0), 2);
                    } else {
                        $stats['subject']['data'][$i]['income'] += $tx_stat['total'];
                    }
                }
            }

            $stats['transaction']['total'] += $tx_stat['total'];
            $stats['transaction']['paid'] += $tx_stat['paid'];
        }
        return $stats;
    }

    public static function mysql_duration_to_minutes($duration)
    {
        $len = strlen($duration);
        $hours = substr($duration, 0, $len - 4);
        $minutes = substr($duration, $len - 4, 2);
        $seconds = substr($duration, $len - 2);
        return ($hours * 60) + $minutes;
    }

    public static function rollcall_report_helper($timeslot_id, $params = array('finance' => true, 'attendance' => true, 'print' => false))
    {
        if (!is_numeric($timeslot_id) || $timeslot_id <= 0) {
            return array();
        }
        $rollcalls = self::rollcall_list(array('timeslot_id' => $timeslot_id));

        $table = array();
        $date_format = Settings::instance()->get('date_format') ?: 'd/m/Y';
        foreach ($rollcalls as $rollcall) {
            if (@$params['print']) {
                $row['Student ID'] = $rollcall['delegate_id'];
                $row['Booking ID'] = $rollcall['booking_id'];
                $row['Student'] = trim($rollcall['first_name'] . ' ' . $rollcall['last_name']);
                $row[date($date_format, strtotime($rollcall['datetime_start'])) . ' ' . date('H:i', strtotime($rollcall['datetime_start']))] = $rollcall['planned_to_attend'] == 1 ? '<input type="checkbox" />' : __('Not attending');
                $row['Student Signature'] = '          ';
            } else {
                $row = array();
                $row['Student ID'] = $rollcall['delegate_id'];
                $row['Student'] = trim($rollcall['first_name'] . ' ' . $rollcall['last_name']);
                $row['Booking ID'] = $rollcall['booking_id'];
                $row['Flexi'] = $rollcall['is_flexi_student'] == 1 ? __('Yes') : __('No');
                $row['Course Category'] = $rollcall['category'];
                $row['Course'] = $rollcall['course'];
                $row['Schedule'] = $rollcall['schedule'];
                $row['Date'] = date($date_format, strtotime($rollcall['datetime_start']));
                $row['Time'] = date('H:i', strtotime($rollcall['datetime_start']));
                if (@$params['attendance']) {
                    if ($rollcall['planned_to_attend'] == 1) {
                        $row['Attendance Status'] =
                            '<select name="rollcall[' . $rollcall['id'] . '][items][' . $rollcall['id'] . '][attendance_status]">' .
                            html::optionsFromArray(
                                array(
                                    '' => '',
                                    'Present' => 'Present',
                                    'Late' => 'Late',
                                    'Early Departures' => 'Early Departures',
                                    'Temporary Absence' => 'Temporary Absence',
                                    'Absent' => 'Absent'
                                ),
                                $rollcall['attendance_status']
                            ) .
                            '</select>';
                        $row['Planned Arrival'] = '<input class="datetimepicker" type="text" name="rollcall[' . $rollcall['id'] . '][items][' . $rollcall['id'] . '][planned_arrival]" value="' . ($rollcall['planned_arrival'] ? date('H:i', strtotime($rollcall['planned_arrival'])) : '') . '"/>';
                        $row['Planned Leave'] = '<input class="datetimepicker" type="text" name="rollcall[' . $rollcall['id'] . '][items][' . $rollcall['id'] . '][planned_leave]" value="' . ($rollcall['planned_leave'] ? date('H:i', strtotime($rollcall['planned_leave'])) : '') . '"/>';
                    } else {
                        $row['Attendance Status'] = __("Not attending");
                        $row['Planned Arrival'] = '';
                        $row['Planned Leave'] = '';
                    }
                }
                if (@$params['finance']) {
                    $row['Finance Status'] = '<select name="rollcall[' . $rollcall['id'] . '][items][' . $rollcall['id'] . '][finance_status]">' .
                        html::optionsFromArray(
                            array('' => '', 'Paid' => 'Paid', 'Unpaid' => 'Unpaid'),
                            $rollcall['finance_status']
                        ) .
                        '</select>';
                    $row['Outstanding'] = $rollcall['outstanding'];
                    $row['Amount Received'] = '<input type="text" name="rollcall[' . $rollcall['id'] . '][items][' . $rollcall['id'] . '][received]" />';
                }
                $row['Notes'] = '<input type="text" name="rollcall[' . $rollcall['id'] . '][items][' . $rollcall['id'] . '][notes]" />' .
                    '<input type="hidden" name="rollcall[' . $rollcall['id'] . '][id]" value="' . $rollcall['id'] . '"/>';
                $row['Student Signature'] = '          ';
            }
            $table[] = $row;
        }
        return $table;
    }

    public static function rollcall_list($params)
    {
        if (@$params['timeslot_id']) {
            $timeslot = DB::select('*')
                ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                ->where('id', '=', $params['timeslot_id'])
                ->execute()
                ->current();
            $schedule_id = $timeslot['schedule_id'];
        } else {
            $schedule_id = 0;
        }

        self::calculate_outstanding_tmp_rollcall($schedule_id);

        $select = DB::select(
            'rollcalls.*',
            DB::expr('IFNULL(timeslots.fee_amount, schedules.fee_amount) as fee_amount'),
            DB::expr("IF(schedules.payment_type=1,'prepay', 'payg') as payment_type"),
            'students.first_name',
            'students.last_name',
            'students.is_flexi_student',
            'schedules.payg_apply_fees_when_absent',
            array('schedules.name', 'schedule'),
            array('courses.title', 'course'),
            'categories.category',
            array('parents.first_name', 'guardian_first_name'),
            array('parents.last_name', 'guardian_last_name'),
            array('mobiles.value', 'guardian_mobile'),
            'tmp_account_stat.outstanding',
            'users.avatar',
            array('users.id', 'user_id'),
            'timeslots.datetime_start',
            'timeslots.datetime_end'
        )
            ->from(array(self::BOOKING_ROLLCALL_TABLE, 'rollcalls'))
                ->join(array(self::BOOKING_TABLE, 'bookings'), 'inner')
                    ->on('rollcalls.booking_id', '=', 'bookings.booking_id')
                ->join(array(self::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                    ->on('rollcalls.booking_item_id', '=', 'items.booking_item_id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('rollcalls.timeslot_id', '=', 'timeslots.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                    ->on('rollcalls.delegate_id', '=', 'students.id')
                ->join(array(self::DELEGATES_TABLE, 'delegates'), 'inner')
                    ->on('rollcalls.delegate_id', '=', 'delegates.contact_id')
                    ->on('rollcalls.booking_id', '=', 'delegates.booking_id')
                ->join('tmp_account_stat', 'left')
                    ->on('bookings.booking_id', '=', 'tmp_account_stat.booking_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                    ->on('timeslots.schedule_id', '=', 'schedules.id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('schedules.course_id', '=', 'courses.id')
                ->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')
                    ->on('courses.category_id', '=', 'categories.id')
                ->join(array(Model_Contacts3::FAMILY_TABLE, 'families'), 'left')
                    ->on('students.family_id', '=', 'families.family_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'parents'), 'left')
                    ->on('families.primary_contact_id', '=', 'parents.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobiles'), 'left')
                    ->on('parents.notifications_group_id', '=', 'mobiles.group_id')
                    ->on('mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Users::MAIN_TABLE, 'users'), 'left')
                    ->on('students.linked_user_id', '=', 'users.id')
            ->where('rollcalls.delete', '=', 0)
            ->and_where('rollcalls.delete', '=', 0)
            ->and_where('rollcalls.booking_status', 'in', array(2,4,5))
            ->and_where('delegates.cancelled', '=', 0)
            ->and_where('delegates.deleted', '=', 0)
            ->and_where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', 'in', array(2,4,5))
            ->and_where('items.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0);

        if (@$params['timeslot_id']) {
            $select->and_where('timeslots.id', '=', $params['timeslot_id']);

        }
        if (@$params['trainer_id']) {
            $select->and_where_open();
            $select->or_where('schedules.trainer_id', '=', $params['trainer_id']);
            $select->or_where('timeslots.trainer_id', '=', $params['trainer_id']);
            $select->or_where('timeslots.trainer_id', '=', 0);
            $select->or_where('timeslots.trainer_id', 'is', null);
            $select->and_where_close();
        }
        if (@$params['schedule_id']) {
            $select->and_where('timeslots.schedule_id', '=', $params['schedule_id']);
        }
        if (@$params['date']) {
            $select->and_where('timeslots.datetime_start', '>=', $params['date'] . ' 00:00:00');
            $select->and_where('timeslots.datetime_start', '<=', $params['date'] . ' 23:59:59');
        }
        if (@$params['attendance_status']) {
            $select->and_where_open();
            foreach ($params['attendance_status'] as $attendance_status) {
                if (in_array($attendance_status, array('Present','Late','Early Departures','Paid','Temporary Absence', 'Absent', '', null, 'null'))) {
                    $select->or_where(DB::expr("FIND_IN_SET('" . $attendance_status . "', rollcalls.attendance_status)"), '>', 0);
                }
            }
            $select->and_where_close();
        }
        if (@$params['finance_status']) {
            $select->and_where_open();
            foreach ($params['finance_status'] as $finance_status) {
                if (in_array($finance_status, array('Paid','Unpaid'))) {
                    $select->or_where(DB::expr("FIND_IN_SET('" . $finance_status . "', rollcalls.finance_status)"), '>', 0);
                }
            }
            $select->and_where_close();
        }

        if (@$params['keyword'])
        {
            $keywords = preg_split('/[\ ,]+/i', trim(preg_replace('/[^a-z0-9\ ]/i', '', $params['keyword'])));
            $match1 = array();
            $match2 = array();
            foreach ($keywords as $i => $keyword) {
                if (strlen($keyword) < 3) { // remove too short things like "at" "'s" "on" ...
                    unset($keywords[$i]);
                } else {
                    if (substr($keyword, -3) == 'ies'){
                        $match2[] = '+' . substr($keyword, 0, -3) . 'y' . '*';
                    } else if (substr($keyword, -3) == 'ses' || substr($keyword, -3) == 'xes'){
                        $match2[] = '+' . substr($keyword, 0, -2) . '*';
                    } else if ($keyword[strlen($keyword) - 1] == 's') {
                        $match2[] = '+' . substr($keyword, 0, -1) . '*'; /*'+' . $keyword . '* */
                    } else {
                        $match2[] = '+' . $keyword . '*';
                    }
                    $match1[] = '+' . $keyword . '*';
                }
            }

            $select->and_where_open();

            if (!empty($keywords)) {
                $match1 = Database::instance()->escape(implode(' ', $match1));
                $match2 = Database::instance()->escape(implode(' ', $match2));
                // Separate terms, enclose in quotes to stop special characters causing problems, "+" before each term
                $select->or_where(DB::expr('match(`courses`.`title`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $select->or_where(DB::expr('match(`courses`.`title`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
                $select->or_where(DB::expr('match(`schedules`.`name`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $select->or_where(DB::expr('match(`schedules`.`name`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
                $select->or_where('students.first_name', 'like', '%' . $params['keyword'] . '%');
                $select->or_where('students.last_name', 'like', '%' . $params['keyword'] . '%');
            } else {
                $select->or_where('courses.title', 'like', '%' . $params['keyword'] . '%');
                $select->or_where('schedules.name', 'like', '%' . $params['keyword'] . '%');
                $select->or_where('students.first_name', 'like', '%' . $params['keyword'] . '%');
                $select->or_where('students.last_name', 'like', '%' . $params['keyword'] . '%');
            }
            $select->and_where_close();
        }

        $select->order_by('students.last_name');
        $select->order_by('students.first_name');
        $select->group_by('rollcalls.id');
        $students = $select->execute()->as_array();
        foreach ($students as $i => $student) {
            if ($student['status_updated']) {
                if ($student['temporary_absences']) {
                    $students[$i]['temporary_absences'] = json_decode($student['temporary_absences'], true);
                }
                if ($student['payg_apply_fees_when_absent'] == 1) {
                    if ($student['timeslot_status'] == 'Absent') {
                        $students[$i]['timeslot_status'] = 'Absent,Unpaid';
                    }
                } else {
                    if (strpos($student['timeslot_status'], 'Paid') === false) {
                        $students[$i]['timeslot_status'] .= ',Unpaid';
                    }
                }
            }
        }
        return $students;
    }

        public static function rollcall_list_ex($params)
    {
        if (@$params['timeslot_id']) {
            $timeslot = DB::select('*')
                ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                ->where('id', '=', $params['timeslot_id'])
                ->execute()
                ->current();
            $schedule_id = $timeslot['schedule_id'];
        } else {
            $schedule_id = 0;
        }

        self::calculate_outstanding_tmp_rollcall($schedule_id);

        $select = DB::select(
            'rollcalls.id',
            'rollcalls.delegate_id',
            'booking_items.booking_item_id',
            'booking_items.booking_id',
            'timeslots.schedule_id',
            array('booking_items.period_id', 'timeslot_id'),
            DB::expr('IFNULL(timeslots.fee_amount, schedules.fee_amount) as fee_amount'),
            DB::expr("IF(schedules.payment_type=1,'prepay', 'payg') as payment_type"),
            DB::expr("IF(booking_items.timeslot_status='', 'Absent', booking_items.timeslot_status) as timeslot_status"),
            'booking_items.attending',
            'booking_items.arrived',
            'booking_items.left',
            'booking_items.planned_arrival',
            'booking_items.planned_leave',
            'booking_items.temporary_absences',
            'booking_items.status_updated',

            'bookings.contact_id',
            'students.first_name',
            'students.last_name',
            'schedules.payg_apply_fees_when_absent',
            array('parents.first_name', 'guardian_first_name'),
            array('parents.last_name', 'guardian_last_name'),
            array('mobiles.value', 'guardian_mobile'),
            'tmp_account_stat.outstanding',
            'users.avatar',
            array('users.id', 'user_id')
            //DB::expr("CONCAT('/media/photos/avatars/', users.avatar) as avatar")
        )
            ->from(array(self::BOOKING_ITEMS_TABLE, 'booking_items'))
                ->join(array(self::BOOKING_TABLE, 'bookings'), 'inner')
                    ->on('booking_items.booking_id', '=', 'bookings.booking_id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('booking_items.period_id', '=', 'timeslots.id')
                ->join(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'rollcalls'), 'inner')
                    ->on('booking_items.booking_item_id', '=', 'rollcalls.booking_item_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                    ->on('rollcalls.delegate_id', '=', 'students.id')
                ->join('tmp_account_stat', 'left')
                    ->on('bookings.booking_id', '=', 'tmp_account_stat.booking_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                    ->on('timeslots.schedule_id', '=', 'schedules.id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('schedules.course_id', '=', 'courses.id')
                ->join(array(Model_Contacts3::FAMILY_TABLE, 'families'), 'left')
                    ->on('students.family_id', '=', 'families.family_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'parents'), 'left')
                    ->on('families.primary_contact_id', '=', 'parents.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobiles'), 'left')
                    ->on('parents.notifications_group_id', '=', 'mobiles.group_id')
                    ->on('mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Users::MAIN_TABLE, 'users'), 'left')
                    ->on('students.linked_user_id', '=', 'users.id')
            ->where('booking_items.delete', '=', 0)
            ->and_where('booking_items.booking_status', 'in', array(2,4,5))
            ->and_where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', 'in', array(2,4,5))
            ->and_where('timeslots.delete', '=', 0);

        if (@$params['timeslot_id']) {
            $select->and_where('timeslots.id', '=', $params['timeslot_id']);

        }
        if (@$params['trainer_id']) {
            $select->and_where_open();
            $select->or_where('schedules.trainer_id', '=', $params['trainer_id']);
            $select->or_where('timeslots.trainer_id', '=', $params['trainer_id']);
            $select->or_where('timeslots.trainer_id', '=', 0);
            $select->or_where('timeslots.trainer_id', 'is', null);
            $select->and_where_close();
        }
        if (@$params['schedule_id']) {
            $select->and_where('timeslots.schedule_id', '=', $params['schedule_id']);
        }
        if (@$params['date']) {
            $select->and_where('timeslots.datetime_start', '>=', $params['date'] . ' 00:00:00');
            $select->and_where('timeslots.datetime_start', '<=', $params['date'] . ' 23:59:59');
        }
        if (@$params['status']) {
            $select->and_where_open();
            foreach ($params['status'] as $status) {
                if (in_array($status, array('Present','Late','Early Departures','Paid','Temporary Absence', 'Absent', '', null, 'null'))) {
                    if ($status == 'Absent') {
                        $select->and_where('booking_items.timeslot_status', '=', '');
                    } else if ($status == 'null' || $status === null) {
                        $select->and_where('booking_items.timeslot_status', 'is', null);
                    } else {
                        $select->or_where(DB::expr("FIND_IN_SET('" . $status . "', booking_items.timeslot_status)"), '>', 0);
                    }
                }
            }
            $select->and_where_close();
        }

        if (@$params['keyword'])
        {
            $keywords = preg_split('/[\ ,]+/i', trim(preg_replace('/[^a-z0-9\ ]/i', '', $params['keyword'])));
            $match1 = array();
            $match2 = array();
            foreach ($keywords as $i => $keyword) {
                if (strlen($keyword) < 3) { // remove too short things like "at" "'s" "on" ...
                    unset($keywords[$i]);
                } else {
                    if (substr($keyword, -3) == 'ies'){
                        $match2[] = '+' . substr($keyword, 0, -3) . 'y' . '*';
                    } else if (substr($keyword, -3) == 'ses' || substr($keyword, -3) == 'xes'){
                        $match2[] = '+' . substr($keyword, 0, -2) . '*';
                    } else if ($keyword[strlen($keyword) - 1] == 's') {
                        $match2[] = '+' . substr($keyword, 0, -1) . '*'; /*'+' . $keyword . '* */
                    } else {
                        $match2[] = '+' . $keyword . '*';
                    }
                    $match1[] = '+' . $keyword . '*';
                }
            }

            $select->and_where_open();

            if (!empty($keywords)) {
                $match1 = Database::instance()->escape(implode(' ', $match1));
                $match2 = Database::instance()->escape(implode(' ', $match2));
                // Separate terms, enclose in quotes to stop special characters causing problems, "+" before each term
                $select->or_where(DB::expr('match(`courses`.`title`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $select->or_where(DB::expr('match(`courses`.`title`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
                $select->or_where(DB::expr('match(`schedules`.`name`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $select->or_where(DB::expr('match(`schedules`.`name`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
                $select->or_where('students.first_name', 'like', '%' . $params['keyword'] . '%');
                $select->or_where('students.last_name', 'like', '%' . $params['keyword'] . '%');
            } else {
                $select->or_where('courses.title', 'like', '%' . $params['keyword'] . '%');
                $select->or_where('schedules.name', 'like', '%' . $params['keyword'] . '%');
                $select->or_where('students.first_name', 'like', '%' . $params['keyword'] . '%');
                $select->or_where('students.last_name', 'like', '%' . $params['keyword'] . '%');
            }
            $select->and_where_close();
        }

        $select->order_by('students.last_name');
        $select->order_by('students.first_name');
        $select->group_by('rollcalls.id');
        $students = $select->execute()->as_array();
        foreach ($students as $i => $student) {
            if ($student['status_updated']) {
                if ($student['temporary_absences']) {
                    $students[$i]['temporary_absences'] = json_decode($student['temporary_absences'], true);
                }
                if ($student['payg_apply_fees_when_absent'] == 1) {
                    if ($student['timeslot_status'] == 'Absent') {
                        $students[$i]['timeslot_status'] = 'Absent,Unpaid';
                    }
                } else {
                    if (strpos($student['timeslot_status'], 'Paid') === false) {
                        $students[$i]['timeslot_status'] .= ',Unpaid';
                    }
                }
            }
        }
        return $students;
    }

    public static function send_payment_plan_due_email($transaction_id)
    {
        $due_payments = DB::select('payments.*', 'transactions.contact_id', 'transactions.booking_id')
            ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'plans'))
                ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'payments'), 'inner')
                    ->on('plans.id', '=', 'payments.payment_plan_id')
                ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                    ->on('plans.transaction_id', '=', 'transactions.id')
            ->where('plans.transaction_id', '=', $transaction_id)
            ->and_where('payments.due_date', '<', date::now())
            ->and_where('payments.payment_id', 'is', null)
            ->and_where('payments.deleted', '=', 0)
            ->execute()
            ->as_array();

        $mm = new Model_Messaging();
        foreach ($due_payments as $due_payment) {
            $params = array();
            $params['duedate'] = $due_payment['due_date'];
            $params['dueamount'] = $due_payment['total'];
            $params['bookingid'] = $due_payment['booking_id'];
            $params['paylink'] = URL::site('/pay-online.html?booking_id=' . $due_payment['booking_id'] . '&plan_payment_id=' . $due_payment['id'] . '&amount=' . $due_payment['total'] . '&contact_id=' . $due_payment['contact_id']);
            $recipients = array(
                array(
                    'target_type' => 'CMS_CONTACT3',
                    'target' => $due_payment['contact_id']
                )
            );

            $mm->send_template(
                'course-payment-plan-due',
                null,
                null,
                $recipients,
                $params
            );
        }
        return $due_payments;
    }

    public static function make_payment_for_plan_payment($plan_payment_id, $amount)
    {
        $plan_payment = $due_payments = DB::select('payments.*', 'plans.transaction_id', 'transactions.contact_id', 'transactions.booking_id')
            ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'plans'))
            ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'payments'), 'inner')
            ->on('plans.id', '=', 'payments.payment_plan_id')
            ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
            ->on('plans.transaction_id', '=', 'transactions.id')
            ->where('payments.id', '=', $plan_payment_id)
            ->and_where('payments.due_date', '<', date::now())
            ->and_where('payments.payment_id', 'is', null)
            ->and_where('payments.deleted', '=', 0)
            ->execute()
            ->current();

        $now = date::now();

        $payment_inserted = DB::insert(Model_Kes_Payment::PAYMENT_TABLE)
            ->values(array(
                'transaction_id' => $plan_payment['transaction_id'],
                'amount' => $amount,
                'type' => 'card',
                'bank_fee' => 0,
                'status' => 2,
                'currency' => 'EUR',
                'created' => $now,
                'updated' => $now,
                'note' => 'Payment Online Form'
            ))->execute();

        DB::update(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE)
            ->set(array('payment_id' => $payment_inserted[0]))
            ->where('id', '=', $plan_payment_id)
            ->execute();
    }

    public static function payonline_data_link($after = null, $before = null)
    {
        if ($after == null) {
            $after = '2018-03-01';
        }
        if ($before == null) {
            $before = date::now();
        }
        $payments = DB::select('*')
            ->from('plugin_payments_log')
            ->where('student', 'is', null)
            ->and_where('purchase_time', '>=', $after)
            ->and_where('purchase_time', '<=', $before)
            ->order_by('purchase_time', 'desc')
            ->execute()
            ->as_array();
        foreach ($payments as $payment) {
            $message = DB::select('*')
                ->from('plugin_messaging_messages')
                ->where('date_created', '>=', DB::expr("date_sub('" . $payment['purchase_time'] . "', interval 1 minute)"))
                ->and_where('date_created', '<=', DB::expr("date_add('" . $payment['purchase_time'] . "', interval 1 minute)"))
                ->and_where('form_data', 'like', '%' . $payment['customer_name'] . '%')
                ->execute()
                ->current();

            if ($message) {
                //echo $message['form_data'];
                $checkout = @json_decode($message['form_data'], 1);
                $form_data = @json_decode($checkout['checkout'], 1);
                if ($form_data['student_name']) {
                    DB::update('plugin_payments_log')
                        ->set(array('student' => $form_data['student_name']))
                        ->where('id', '=', $payment['id'])
                        ->execute();
                } else {
                    DB::update('plugin_payments_log')
                        ->set(array('student' => ''))
                        ->where('id', '=', $payment['id'])
                        ->execute();
                }
            }
        }


    }

    public static function check_duplicate_booking($student_id, $schedule_id, $timeslots = null)
    {
        $select = DB::select('*')
            ->from(array(self::BOOKING_TABLE, 'bookings'))
            ->join(array(self::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
                ->on('bookings.booking_id', '=', 'has_schedules.booking_id')
            ->where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', '<>', 3)
            ->and_where('has_schedules.deleted', '=', 0)
            ->and_where('has_schedules.booking_status', '<>', 3);

        $select->and_where('bookings.contact_id', '=', $student_id);
        $select->and_where('has_schedules.schedule_id', '=', $schedule_id);
        if (is_array($timeslots) && count($timeslots) > 0) {
            $select
                ->join(array(self::BOOKING_ITEMS_TABLE, 'btimeslots'), 'inner')
                    ->on('bookings.booking_id', '=', 'btimeslots.booking_id')
                ->and_where('btimeslots.delete', '=', 0)
                ->and_where('btimeslots.booking_status', '<>', 3)
                ->and_where('btimeslots.period_id', 'in', $timeslots);
        }

        $has_bookings = $select->execute()->as_array();
        return $has_bookings;
    }

    public static function tmp_booking_count()
    {
        $cancelled_status   = new Model_Booking_Status(['title' => 'Cancelled']);
        $sales_quote_status = new Model_Booking_Status(['title' => 'Sales Quote']);

        DB::query(
            null,
            "DROP TEMPORARY TABLE IF EXISTS tmp_timeslot_booking_counts"
        )
            ->execute();

        DB::query(
            null,
            "CREATE TEMPORARY TABLE tmp_timeslot_booking_counts (timeslot_id INT primary key, booking_count INT)"
        )
            ->execute();
        DB::query(
            null,
            "INSERT INTO tmp_timeslot_booking_counts (timeslot_id, booking_count)
            (select
                  items.period_id, count(*) as booking_count
                from " . self::BOOKING_TABLE . " bookings
                    inner join " . self::BOOKING_ITEMS_TABLE . " items on bookings.booking_id = items.booking_id
                where bookings.delete = 0
                and bookings.booking_status <> $cancelled_status->status_id
                and bookings.booking_status <> $sales_quote_status->status_id
                and items.delete = 0
                and items.booking_status <> $cancelled_status->status_id
                and items.booking_status <> $sales_quote_status->status_id
                and items.attending = 1
                group by items.period_id
            )
            "
        )->execute();
    }

    public static function find_interview_slot($course_code)
    {
        self::tmp_booking_count();

        $timeslot_book_count_q = DB::select('items.period_id', DB::expr("count(*) as booking_count"))
            ->from(array(self::BOOKING_TABLE, 'bookings'))
                ->join(array(self::BOOKING_ITEMS_TABLE, 'items'), 'inner')->on('bookings.booking_id', '=', 'items.booking_id')
            ->where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', '<>', 3)
            ->and_where('items.delete', '=', 0)
            ->and_where('items.booking_status', '<>', 3)
            ->group_by('items.period_id');

        $timeslots = DB::select('timeslots.*', 'has_courses.course_id')
            ->from(array(Model_Courses::TABLE_COURSES, 'courses'))
                ->join(array(Model_Schedules::TABLE_HAS_COURSES, 'has_courses'), 'inner')->on('courses.id', '=', 'has_courses.course_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('has_courses.schedule_id', '=', 'schedules.id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
                ->join('tmp_timeslot_booking_counts', 'left')->on('timeslots.id', '=', 'tmp_timeslot_booking_counts.timeslot_id')
            ->where('courses.code', '=', $course_code)
            ->and_where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0)
            ->and_where_open()
                ->or_where(DB::expr("IFNULL(timeslots.max_capacity, schedules.max_capacity)"), '>', DB::expr('tmp_timeslot_booking_counts.booking_count'))
                ->or_where('tmp_timeslot_booking_counts.booking_count', 'is', null)
            ->and_where_close()
            ->and_where('timeslots.datetime_start', '>=', date::now())
            ->order_by('timeslots.datetime_start', 'asc')
            ;

        $timeslots = $timeslots->execute()
            ->as_array();
        return $timeslots;
    }

    public static function find_last_interview_slot($course_code)
    {
        self::tmp_booking_count();

        $timeslots = DB::select('timeslots.*', 'has_courses.course_id', 'tmp_timeslot_booking_counts.booking_count')
            ->from(array(Model_Courses::TABLE_COURSES, 'courses'))
            ->join(array(Model_Schedules::TABLE_HAS_COURSES, 'has_courses'), 'inner')->on('courses.id', '=', 'has_courses.course_id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')->on('has_courses.schedule_id', '=', 'schedules.id')
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')->on('schedules.id', '=', 'timeslots.schedule_id')
            ->join('tmp_timeslot_booking_counts', 'left')->on('timeslots.id', '=', 'tmp_timeslot_booking_counts.timeslot_id')
            ->where('courses.code', '=', $course_code)
            ->and_where('courses.deleted', '=', 0)
            ->and_where('schedules.delete', '=', 0)
            ->and_where('timeslots.delete', '=', 0)
            ->order_by('timeslots.datetime_start', 'desc')
            ->limit(1)
        ;
        $timeslot = $timeslots->execute()
            ->current();
        return $timeslot;
    }

    public static function set_interview_timeslots_missing()
    {
        $bookings = DB::query(Database::SELECT,
            "select
		b.booking_id, c.code, hc.course_id, c.title as `Course`, a.first_name as `First Name`, a.last_name as `Last Name`, e.`value` as `Email`
	from plugin_ib_educate_bookings b
		inner join plugin_ib_educate_bookings_has_courses hc on b.booking_id = hc.booking_id
		inner join plugin_ib_educate_bookings_has_applications ha on b.booking_id = ha.booking_id and ha.interview_status is not null
		left join " . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " i on b.booking_id = i.booking_id
		left join plugin_courses_schedules_events t on i.period_id = t.id and t.`delete` = 0
		inner join plugin_courses_courses c on hc.course_id = c.id
		inner join plugin_contacts3_contacts a on a.id = b.contact_id
		left join plugin_contacts3_contact_has_notifications e on a.notifications_group_id = e.group_id and e.notification_id = 1 and a.`delete` = 0

	where b.`delete` = 0 and b.booking_status <> 3 and hc.booking_status <> 3 and hc.deleted = 0 and (t.id is null or ha.interview_status = 'Not Scheduled') and b.booking_id not in (select i.booking_id from " . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " i inner join plugin_courses_schedules_events t on i.period_id = t.id and t.`delete` = 0 and i.`delete` = 0)
	order by b.booking_id")
            ->execute()
            ->as_array();
        Database::instance()->begin();
        foreach ($bookings as $booking) {
            $slots = self::find_interview_slot($booking['code']);
            if (count($slots) > 0) {
                self::set_interview_timeslot($booking['booking_id'], $slots[0]['id']);
            }
        }
        Database::instance()->commit();
    }

    public static function set_interview_timeslot($booking_id, $timeslot_id)
    {
        $booking_id = (int)$booking_id;
        $timeslot_id = (int)$timeslot_id;
        $timeslot = DB::select('timeslots.*', 'schedules.course_id')
            ->from(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('timeslots.schedule_id', '=', 'schedules.id')
            ->where('timeslots.id', '=', $timeslot_id)
            ->execute()
            ->current();
        DB::query(
            null,
            "update " . Model_KES_Bookings::BOOKING_ITEMS_TABLE . " i
	            inner join plugin_courses_schedules_events t on i.period_id = t.id
	            inner join plugin_courses_schedules s on t.schedule_id = s.id
	          set i.`delete` = 1
	          where i.booking_id = $booking_id and i.delete = 0 and s.is_interview=1"
        )->execute();

        $hc = DB::select('*')
            ->from(self::BOOKING_COURSES)
            ->where('booking_id', '=', $booking_id)
            ->and_where('course_id', '=', $timeslot['course_id'])
            ->and_where('deleted', '=', 0)
            ->execute()
            ->current();
        if (!$hc) {
            DB::insert(self::BOOKING_COURSES)
                ->values(array('booking_id' => $booking_id, 'course_id' => $timeslot['course_id']))
                ->execute();
        }
        $inserted = DB::insert(self::BOOKING_ITEMS_TABLE)
            ->values(array('booking_id' => $booking_id, 'period_id' => $timeslot_id, 'booking_status' => 2))
            ->execute();

        DB::update('plugin_ib_educate_bookings_has_applications')
            ->set(array('interview_status', '=', 'Scheduled'))
            ->where('booking_id', '=', $booking_id)
            ->execute();
        return $inserted[0];
    }

    public static function set_interview_status_bulk($interviews)
    {
        try {
            Database::instance()->begin();

            foreach ($interviews as $interview) {
                DB::update('plugin_ib_educate_bookings_has_applications')
                    ->set(array('interview_status' => $interview['interview_status']))
                    ->where('booking_id', '=', $interview['booking_id'])
                    ->execute();
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function import_interview_old_data()
    {
        try {
            Database::instance()->begin();
            $interviews = DB::select('*')
                ->from('plugin_messaging_messages')
                ->where('subject', '=', 'New Course Interview Application')
                ->order_by('id', 'asc')
                ->execute()
                ->as_array();

            $imports = array();
            foreach ($interviews as $interview) {
                $family = null;
                $interview = json_decode($interview['form_data'], true);
                $contact = DB::select('*')
                    ->from(Model_Contacts3::CONTACTS_TABLE)
                    ->where('first_name', '=', $interview['student_first_name'])
                    ->and_where('last_name', '=', $interview['student_last_name'])
                    //->and_where('pps_number', '=', $interview['pps_number'])
                    ->execute()
                    ->current();
                $parent = DB::select('*')
                    ->from(Model_Contacts3::CONTACTS_TABLE)
                    ->where('first_name', '=', $interview['first_name'])
                    ->and_where('last_name', '=', $interview['last_name'])
                    //->and_where('pps_number', '=', $interview['pps_number'])
                    ->execute()
                    ->current();
                if ($contact) {
                    $interview['contact_id'] = $contact['id'];
                    if (!$contact['family_id']) {
                        $family = new Model_Family();
                        $family->set_family_name($contact['last_name']);
                        $family->save();
                        DB::update(Model_Contacts3::CONTACTS_TABLE)
                            ->set(array('family_id' => $family->get_id()))
                            ->where('id', '=', $contact['id'])
                            ->execute();
                    }
                    if ($parent) {
                        $interview['guardian_id'] = $parent['id'];
                        if (!$parent['family_id']) {
                            if ($family) {
                                DB::update(Model_Contacts3::CONTACTS_TABLE)
                                    ->set(array('family_id' => $family->get_id()))
                                    ->where('id', '=', $parent['id'])
                                    ->execute();
                            }
                        }
                    }

                    $interview_slot = null;
                    $interview['courses'] = array();
                    $interview_slots = Model_KES_Bookings::find_interview_slot($interview['course_code']);
                    if (count($interview_slots) > 0) {
                        $interview_slot = $interview_slots[0];
                        $interview['booking_items'][$interview_slot['schedule_id']] = array();
                        $interview['booking_items'][$interview_slot['schedule_id']][$interview_slot['id']] = array('schedule_id' => $interview_slot['schedule_id'], 'attending' => 1);
                        $interview['courses'][] = array('course_id' => $interview_slot['course_id']);
                        /*$data['application'] = array(
                            'interview_status' => 'SCHEDULED',
                        );*/
                    } else {
                        //echo "no timeslot " . $interview['course_code'] . "<br />\n";
                        $course_id = DB::select('id')
                            ->from(Model_Courses::TABLE_COURSES)
                            ->where('code', '=', $interview['course_code'])
                            ->execute()
                            ->get('id');

                        if ($course_id) {
                            $interview['courses'][] = array('course_id' => $course_id);
                        } else {

                        }
                    }

                    if ($interview_slot) {
                        $booking = new Model_KES_Bookings();
                        $booking->interview_status = 'Scheduled';
                        $booking->set($interview);
                        $a = $booking->book_and_pay($interview);

                        $imports[] = array('contat' => $contact, 'booking_id' => $booking->get_booking_id());
                    }
                }
            }
            Database::instance()->commit();
            return $imports;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
    
    public static function link_contacts_to_booking($booking_id, $contact_ids = array())
    {
        foreach ($contact_ids as $contact_id) {
            // Currently implemented like this as we may want to assign multiple contacts of the same type to a booking in the future
            // Get new linked contact sub type id
            $type_id = DB::select('c.type')
                ->from(array('plugin_contacts3_contacts', 'c'))
                ->where('c.id', '=', $contact_id)
                ->execute()->get('type');
            // check if that type already exists for this booking
            $contact_match = DB::select(array('c.id', 'id'))
                ->from(array('plugin_ib_educate_bookings_has_linked_contacts', 'lb'))
                ->join(array('plugin_contacts3_contacts', 'c'), 'inner')->on('c.id', '=', 'lb.contact_id')
                ->where('c.type', '=', $type_id)
                ->where('lb.booking_id', '=', $booking_id)->execute();
            // If a contact has been found, we need to replace it
            if ($contact_match->count() == 0) {
                // If it doesn't, insert it
                DB::insert('plugin_ib_educate_bookings_has_linked_contacts')
                    ->values(array(
                        'booking_id' => $booking_id,
                        'contact_id' => $contact_id
                    ))->execute();
            } else {
                DB::update('plugin_ib_educate_bookings_has_linked_contacts')
                    ->set(array(
                        'booking_id' => $booking_id,
                        'contact_id' => $contact_id
                    ))->where('contact_id', '=', $contact_match->get('id'))
                    ->where('booking_id', '=', $booking_id)->execute();
            }
        }
    }
    
    public static function email_students_timeslot_change($timeslots)
    {
        $ids_to_update = array();
        foreach ($timeslots as $timeslot) {
            if (@$timeslot['update']) {
                $ids_to_update[] = $timeslot['id'];
            }
        }
        if (count($ids_to_update) > 0) {
            $bookings = DB::select(
                'timeslots.*',
                array('schedules.name', 'schedule'),
                DB::expr("CONCAT_WS(' ', students.first_name, students.last_name) as student"),
                'bookings.contact_id',
                DB::expr("CONCAT_WS(' ', trainers.first_name, trainers.last_name) as trainer"),
                'schedules.trainer_id'
            )
                ->from(array(self::BOOKING_TABLE, 'bookings'))
                    ->join(array(self::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                        ->on('bookings.booking_id', '=', 'items.booking_id')
                    ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                        ->on('items.period_id', '=', 'timeslots.id')
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('timeslots.schedule_id', '=', 'schedules.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                        ->on('bookings.contact_id', '=', 'students.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'trainers'), 'inner')
                        ->on('schedules.trainer_id', '=', 'trainers.id')
                ->where('bookings.delete', '=', 0)
                ->and_where('bookings.booking_status', '<>', 3)
                ->and_where('items.delete', '=', 0)
                ->and_where('items.booking_status', '<>', 3)
                ->and_where('timeslots.delete', '=', 0)
                ->and_where('schedules.delete', '=', 0)
                ->and_where('timeslots.id', 'in', $ids_to_update)
                ->execute()
                ->as_array();

            $trainer_cache = array();
            $mm = new Model_Messaging();
            foreach ($bookings as $booking) {
                foreach ($timeslots as $timeslot) {
                    if ($booking['id'] == $timeslot['id']) {
                        $recipients = array(
                            array('target_type' => 'CMS_CONTACT3', 'target' => $booking['contact_id'])
                        );

                        if (@$timeslot['trainer_id'] && @$timeslot['trainer_id'] != $booking['trainer_id']) {
                            if (!isset($trainer_cache[$timeslot['trainer_id']])) {
                                $newtrainer = new Model_Contacts3($timeslot['trainer_id']);
                                $trainer_cache[$timeslot['trainer_id']] = $newtrainer->get_first_name() . ' ' . $newtrainer->get_last_name();
                            }
                            $newtrainer = $trainer_cache[$timeslot['trainer_id']];
                        } else {
                            $newtrainer = $booking['trainer'];
                        }
                        $parameters = array();
                        //$schedule,$date,$time,$trainer,$newschedule,$newdate,$newtime,$newtrainer
                        $parameters['student'] = $booking['student'];
                        $parameters['schedule'] = $booking['schedule'];
                        $parameters['date'] = date('d-m-Y', strtotime($booking['datetime_start']));
                        $parameters['time'] = date('H:i', strtotime($booking['datetime_start']));
                        $parameters['trainer'] = $booking['trainer'];
                        $parameters['newschedule'] = $booking['schedule'];
                        $parameters['newdate'] = date('d-m-Y', strtotime($timeslot['datetime_start']));
                        $parameters['newtime'] = date('H:i', strtotime($timeslot['datetime_start']));
                        $parameters['newtrainer'] = $newtrainer;

                        $mm->send_template('course-timeslot-changed', null, null, $recipients, $parameters);
                    }
                }
            }
        }
    }

    public static function create_host_application($data)
    {
        try {
            Database::instance()->begin();
            $family = new Model_Family();
            $family->set_family_name($data['last_name']);
            $family->save();

            $contact = new Model_Contacts3();
            $contact->set_subtype_id(1);
            $type = Model_Contacts3::find_type('Host Family');
            if ($type) {
                $contact->set_type($type['contact_type_id']);
            } else {
                $contact->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
            }
            $contact->set_family_id($family->get_id());
            $contact->add_role(1);
            $address = new Model_Residence();
            $address->load(
                array(
                    'address1' => $data['address_1'],
                    'address2' => $data['address_2'],
                    'town' => $data['city'],
                    'postcode' => $data['postcode'],
                    'country' => $data['country'],
                )
            );
            $address->save();
            $contact->set_residence($address->get_address_id());
            $contact->set_occupation($data['occupation']);
            $contact->load($data);
            $contact->save();
            $family->set_primary_contact_id($contact->get_id());
            $family->save();
            $contact->insert_notification(array(
                'contact_id'      => 0,
                'notification_id' => 1,
                'value'           => $data['email']
            ));
            $contact->insert_notification(array(
                'contact_id' => 0,
                'notification_id' => 2,
                'value' => $data['mobile_international_code'] . '' . $data['mobile_number']
            ));
            $contact->save();

            if ($data['partner_first_name']) {
                $partner = new Model_Contacts3();
                $partner->add_role(1);
                $partner->set_subtype_id(1);
                $partner->set_family_id($family->get_id());
                $partner->set_first_name($data['partner_first_name']);
                $partner->set_last_name($data['partner_last_name']);
                $partner->save();
                if (@$data['partner_email']) {
                    $partner->insert_notification(array(
                        'contact_id' => 0,
                        'notification_id' => 1,
                        'value' => $data['partner_email']
                    ));
                }
                if (@$data['partner_phone']) {
                    $partner->insert_notification(array(
                        'contact_id' => 0,
                        'notification_id' => 2,
                        'value' => $data['partner_phone_international_code'] . '' . $data['partner_phone']
                    ));
                }
                $partner->save();
            }

            foreach ($data['children'] as $cdata) {
                if (@$cdata['first_name']) {
                    $child = new Model_Contacts3();
                    $child->add_role(2);
                    $child->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                    $child->set_subtype_id(1);
                    $child->set_first_name($cdata['first_name']);
                    $child->set_last_name($cdata['last_name']);
                    $child->set_date_of_birth($cdata['date_of_birth']);
                    $child->set_gender($cdata['gender']);
                    $child->set_family_id($family->get_id());
                    $child->save();
                }
            }

            $host = array(
                'contact_id' => $contact->get_id(),
                'pets' => $data['pets'],
                'facilities_description' => @$data['facilities_description'],
                'student_profile' => @$data['allowed_profile_types'] ? implode(',', $data['allowed_profile_types']) : null,
                'availability' => @$data['availability'],
                'facilities' => @$data['facilities'] ? implode(',', $data['facilities']) : null,
                'rules' => $data['rules'],
                'other' => $data['other'],
                'status' => 'Pending',
                'published' => 0,
                'created' => date::now(),
                'updated' => date::now()
            );
            $host_id = Model_Host::save($host);
            Database::instance()->commit();
            return $host_id;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
}
?>