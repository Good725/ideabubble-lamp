<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 11/05/15
 * Time: 11:09
 */

class Model_Docarrayhelper {
    private $doc_template ;

    // @todo move to database
    // Template data function name
    private $template_data =array();

    private $system_details ;

    private $_schedule_details = array(
        'schedule_id',
        'course_id',
        'schedule_cost',
        'schedule_name',
        'datetime_start',
        'date_start',
        'time_start',
        'schedule_location',
        'location',
        'course_name',
        'course_subject',
        'course_level',
        'course_category',
        'course_year',
        'course_type'
    );

    // Constructor - initialise a policy by id
    /**
     * Constructor - initialize a contact by id
     */
    public function __construct()
    {
        $this->get_system_details();
    }

    /**
     * @param null $student_id
     * @param null $payer_id
     * @return array
     */
    private function get_contact_details($student_id = NULL , $payer_id = NULL)
    {
        if ( ! is_null($student_id))
        {
            $contact_details['contact_student_id'] = $student_id;
            $student = new Model_Contacts3($student_id);
            $contact_details['contact_student_title']         = $student->get_title();
            $contact_details['contact_student_first_name']    = $student->get_first_name();
            $contact_details['contact_student_last_name']     = $student->get_last_name();
            $contact_details['contact_student_email']         = $student->get_email();
            $contact_details['contact_student_mobile_number'] = $student->get_mobile();
            $contact_details['contact_student_year'] = Model_Years::get_year($student->get_year_id());
            $contact_details['contact_student_year'] = $contact_details['contact_student_year']['year'];

            $pcontactid = $student->get_primary_contact();
            if ($pcontactid) {
                $contact_details['contact_id'] = $pcontactid;
                $pcontact = new Model_Contacts3($pcontactid);
                $address_id = $pcontact->get_residence();
                $contact_details['contact_title']         = $pcontact->get_title();
                $contact_details['contact_first_name']    = $pcontact->get_first_name();
                $contact_details['contact_last_name']     = $pcontact->get_last_name();
                $contact_details['contact_address1']      = $pcontact->get_address_line1($address_id);
                $contact_details['contact_address2']      = $pcontact->get_address_line2($address_id);
                $contact_details['contact_address3']      = $pcontact->get_address_line3($address_id);
                $contact_details['contact_town']          = $pcontact->get_address_town($address_id);
                $contact_details['contact_county']        = $pcontact->get_address_county($address_id);
                $contact_details['contact_postcode']      = $pcontact->get_address_postcode($address_id);
                $contact_details['contact_email']         = $pcontact->get_email();
                $contact_details['contact_mobile_number'] = $pcontact->get_mobile();
            } else {
                $contact_details['contact_id'] = $student_id;
                $address_id = $student->get_residence();
                $contact_details['contact_title']         = $student->get_title();
                $contact_details['contact_first_name']    = $student->get_first_name();
                $contact_details['contact_last_name']     = $student->get_last_name();
                $contact_details['contact_address1']      = $student->get_address_line1($address_id);
                $contact_details['contact_address2']      = $student->get_address_line2($address_id);
                $contact_details['contact_address3']      = $student->get_address_line3($address_id);
                $contact_details['contact_town']          = $student->get_address_town($address_id);
                $contact_details['contact_county']        = $student->get_address_county($address_id);
                $contact_details['contact_postcode']      = $student->get_address_postcode($address_id);
                $contact_details['contact_email']         = $student->get_email();
                $contact_details['contact_mobile_number'] = $student->get_mobile();
            }
        }
        if ( ! is_null($payer_id))
        {
            $contact_details['contact_id'] = $payer_id;
            $payer = new Model_Contacts3($payer_id);
            $address_id = $payer->get_residence();
            $contact_details['contact_title']         = $payer->get_title();
            $contact_details['contact_first_name']    = $payer->get_first_name();
            $contact_details['contact_last_name']     = $payer->get_last_name();
 	 	    $contact_details['contact_address1']      = $payer->get_address_line1($address_id);
 	 	    $contact_details['contact_address2']      = $payer->get_address_line2($address_id);
 	 	    $contact_details['contact_address3']      = $payer->get_address_line3($address_id);
            $contact_details['contact_town']          = $payer->get_address_town($address_id);
 	 	    $contact_details['contact_county']        = $payer->get_address_county($address_id);
            $contact_details['contact_postcode']      = $payer->get_address_postcode($address_id);
// 	 	    $contact_details['contact_country']       = $payer->get_address_country($address_id);
            $contact_details['contact_email']         = $payer->get_email();
            $contact_details['contact_mobile_number'] = $payer->get_mobile();
        }
        $contact_details['contact_address4'] = $contact_details['contact_town'];
        $contact_details['contact_address'] = $contact_details['contact_address1'] ;
        if (!empty($contact_details['contact_address2']))
        {
            $contact_details['contact_address'] .= "\n" . $contact_details['contact_address2'];
            $contact_details['contact_address'] .= (!empty($contact_details['contact_address3'])) ? "\n".$contact_details['contact_address3']: '';
        }
		$contact_address = preg_split('/\n+/', $contact_details['contact_address']);
		if(count($contact_address) > 1){
			$contact_details['contact_address'] = array('type' => 'multiline', 'lines' => $contact_address);
		}
        return $contact_details;
    }

    /**
     * Get the logged user details and currante date
     * @return array
     */
    private function get_system_details()
    {
        $user     = Auth::instance()->get_user();
        $user_detail = DB::select(
            array('name','system_user_first_name'),
            array('surname','system_user_last_name')
        )
        ->from('engine_users')->where('id','=',$user['id'])->execute()->as_array();
        $this->system_details = @$user_detail[0];
        $this->system_details['system_user_last_name'] = (isset($this->system_details['system_user_last_name']) AND ($this->system_details['system_user_last_name'] != '') ) ? $this->system_details['system_user_last_name'] : '';
        $this->system_details['TodaysDate'] = date('D jS M Y');
        return $this->system_details;
    }

    /**
     * Details for the schedule
     * @param $schedule_id
     * @return array
     */
    private function get_schedule_details($schedule_id,$attend=TRUE)
    {
		$schedule_detail = array('schedule_id' => '', 'course_id' => '', 'schedule_cost' => '', 'schedule_name' => '', 'datetime_start' => '', 'schedule_location' => '', 'location' => '');
        $q = DB::select(
            array('s.id','schedule_id'),
            's.course_id',
            array('s.fee_amount','schedule_cost'),
            array('s.name','schedule_name'),
//            array('c.first_name','schedule_teacher_first_name'),
// 	        array('c.last_name','schedule_teacher_last_name'),
            's1.datetime_start',
            DB::expr("CONCAT_WS(' ', plocation.name, location.name) AS schedule_location"),
            DB::expr("CONCAT_WS(' ', plocation.name, location.name) AS location")
        )
            ->from(array('plugin_courses_schedules','s'))
//            ->join(array('plugin_contacts3_contacts','c'))->on('s.trainer_id','=','c.id')
            ->join(array('plugin_courses_schedules_events','s1'))->on('s1.schedule_id','=','s.id')
            ->join(array('plugin_ib_educate_booking_items','b'))->on('b.period_id','=','s1.id')
            ->join(array('plugin_courses_locations','location'), 'left')->on('location.id','=','s.location_id')
            ->join(array('plugin_courses_locations','plocation'), 'left')->on('location.parent_id','=','plocation.id')
            ->where('s.id','=',$schedule_id);
        if ($attend)
        {
            $q->where('b.attending','=',1);
        }
        $schedule_details = $q->where('s1.datetime_start','>=',DB::expr('CURDATE()'))->execute()->as_array();

        if (count($schedule_details) > 0)
        {
            $schedule_detail = $schedule_details[0];
        }
        $schedule_detail['date_start'] = ($schedule_detail['datetime_start'] != '') ? date('d-m-Y', strtotime($schedule_detail['datetime_start'])) : '';
        $schedule_detail['time_start'] = ($schedule_detail['datetime_start'] != '') ? date('H:i:', strtotime($schedule_detail['datetime_start']))  : '';

		$course_detail = array('course_name' => '', 'course_subject' => '', 'course_level' => '', 'course_category' => '', 'course_year' => '', 'course_type' => '');
		if (isset($schedule_detail['course_id']))
		{
			$course_details = DB::select(
				array('c0.title','course_name'),
				array('c1.name','course_subject'),
				array('c2.level','course_level'),
				array('c3.category','course_category'),
				array('c4.year','course_year'),
				array('c5.type','course_type')
			)
				->from(array('plugin_courses_courses','c0'))
				->join(array('plugin_courses_subjects','c1'))->on('c0.subject_id','=','c1.id')
				->join(array('plugin_courses_levels','c2'))->on('c0.level_id','=','c2.id')
				->join(array('plugin_courses_categories','c3'))->on('c0.category_id','=','c3.id')
				->join(array('plugin_courses_years','c4'))->on('c0.year_id','=','c4.id')
				->join(array('plugin_courses_types','c5'))->on('c0.type_id','=','c5.id')
				->where('c0.id','=',$schedule_detail['course_id'])
				->execute()->as_array();

			if (count($course_details) > 0)
			{
				$course_detail = $course_details[0];
			}
		}
        $schedule_detail['course_location'] = $schedule_detail['schedule_location'];
        return array_merge($schedule_detail,$course_detail);
    }

    private function get_teacher_detail($schedule_id=NULL)
    {
        $teacher_detail = array(
            'schedule_teacher_first_name' => '',
            'schedule_teacher_last_name' => '',
            'trainer_id' => ''
        );
        if ( ! is_null($schedule_id))
        {
            $q = DB::select(
                'c.first_name',
                'c.last_name',
                'c.id'
            )
                ->from(array('plugin_contacts3_contacts','c'))
                ->join(array('plugin_courses_schedules','s'))->on('s.trainer_id','=','c.id')
                ->where('s.id','=',$schedule_id)
                ->execute()->as_array();
            if ($q)
            {
                $teacher_detail=array(
                    'schedule_teacher_first_name'=>$q[0]['first_name'],
                    'schedule_teacher_last_name'=>$q[0]['last_name'],
                    'trainer_id' => $q[0]['id']
                );
            }
        }
        return $teacher_detail;
    }

    private function get_schedules_details($transaction_id=NULL,$booking_id=NULL,$single=FALSE,$attend=TRUE)
    {
        $schedules_details = array();
        if ( ! is_null($transaction_id))
        {
            $schedules = DB::select('schedule_id')->from('plugin_bookings_transactions_has_schedule')->where('transaction_id','=',$transaction_id)->execute()->as_array();
        }
        if ( ! is_null($booking_id))
        {
            $schedules = DB::select('schedule_id')->from('plugin_ib_educate_booking_has_schedules')->where('booking_id','=',$booking_id)->execute()->as_array();
        }
        if ($schedules)
        {
            foreach($schedules as $schedule)
            {
                $schedules_details[] = $this->get_schedule_details($schedule['schedule_id'],$attend);
            }
            if ($single)
            {
                $schedules_details = $schedules_details[0];
            }
        }
        return $schedules_details;
    }

    /**
     * Transaction details
     * @param $transaction_id
     * @return array
     * @throws \Kohana_Exception
     */
    private function get_transaction_details($transaction_id=NULL,$booking_id=NULL)
    {
        if ( ! is_null($transaction_id))
        {
            $t = ORM::factory('Kes_Transaction')->get_transaction($transaction_id,NULL);
        }
        else
        {
            $t = ORM::factory('Kes_Transaction')->get_transaction(NULL,$booking_id);
        }
        if (@$t['id']) {
            $transaction_details = array(
                'transaction_id' => $t['id'],
                'transaction_amount' => (string)$t['amount'],
                'transaction_fee' => (string)$t['fee'],
                'transaction_total' => (string)$t['total'],
                'transaction_type' => $t['type'],
                'transaction_outstanding' => (string)$t['outstanding'],
                'transaction_contact_id' => $t['contact_id'],
                'transaction_payed' => (string)$t['payed'],
                'transaction_discount_amount' => (string)$t['discount'],
                'booking_id' => $t['booking_id']
            );
            $transaction_details['pp_interest_total'] = (float)DB::select(
                DB::expr("SUM(pp_installments.interest) as interest_total")
            )
                ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'))
                ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'pp_installments'), 'inner')
                ->on('pp.id', '=', 'pp_installments.payment_plan_id')
                ->where('pp.transaction_id', '=', $t['id'])
                ->and_where('pp_installments.deleted', '=', 0)
                ->execute()
                ->get('interest_total');
            $transaction_details['pp_interest_remaining'] = (float)DB::select(
                DB::expr("SUM(pp_installments.interest) as interest_total")
            )
                ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'))
                ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'pp_installments'), 'inner')
                ->on('pp.id', '=', 'pp_installments.payment_plan_id')
                ->where('pp.transaction_id', '=', $t['id'])
                ->and_where('pp_installments.deleted', '=', 0)
                ->and_where('pp_installments.payment_id', 'is', null)
                ->execute()
                ->get('interest_total');
        } else {
            $transaction_details = array(
                'transaction_id' => '',
                'transaction_amount' => '',
                'transaction_fee' => '',
                'transaction_total' => '',
                'transaction_type' => '',
                'transaction_outstanding' => '',
                'transaction_contact_id' => '',
                'transaction_payed' => '',
                'transaction_discount_amount' => '',
                'booking_id' => $booking_id,
                'pp_interest_total' => 0,
                'pp_interest_remaining' => 0
            );
        }
        return $transaction_details;
    }

    /**
     * Credit Journal transaction
     * @param $transaction_id
     * @return array
     * @throws \Kohana_Exception
     */
    private function get_transaction_credit_journal_details($transaction_id)
    {
        $t = ORM::factory('Kes_Transaction')->where('id', '=', $transaction_id)->find()->as_array();
        $transaction_credit_journal_details= array(
            'transaction_credit_journal_id'                    => $t['id'],
            'transaction_credit_journal_amount'                => (string) $t['amount'],
            'transaction_credit_journal_fee'                   => (string) $t['fee'],
            'transaction_credit_journal_total'                 => (string) $t['total'],
            'transaction_credit_journal_type'                  => $t['type'],
            'transaction_credit_journal_outstanding'           => (string) $t['outstanding'],
            `transaction_contact_id`                           => $t['contact_id']
        );
        return $transaction_credit_journal_details;
    }

    /**
     * Payments
     * @param $payment_id
     * @return array
     */
    private function get_payment_details($payment_id)
    {
        $p = ORM::factory('Kes_Payment')->get_payment($payment_id);
        $payment_details = array(
            'payment_id'            => $payment_id,
 	 	    'payment_type'          => $p['type'],
            'payment_amount'        => $p['amount'],
            'payment_bank_fee'      => $p['bank_fee'],
            'payment_status'        => $p['status'],
            'payment_note'          => $p['note'],
            'payment_total'         => $p['amount']+$p['bank_fee'],
            'payment_date'          => $p['created'],
            'transaction_currency'  => $p['currency'],
            'transaction_id'        => $p['transaction_id']
        );
        $pp_installment = DB::select(
            "pp_installments.*"
        )
            ->from(array(Model_Kes_Payment::PAYMENT_PLAN_TABLE, 'pp'))
            ->join(array(Model_Kes_Payment::PAYMENT_PLAN_HAS_PAYMENTS_TABLE, 'pp_installments'), 'inner')
            ->on('pp.id', '=', 'pp_installments.payment_plan_id')
            ->where('pp_installments.payment_id', '=', $payment_id)
            ->and_where('pp_installments.deleted', '=', 0)
            ->execute()
            ->current();
        if ($pp_installment) {
            $payment_details['payment_total'] = $pp_installment['total'];
        }
        return $payment_details;
    }

    /**
     * Booking details
     * @param $booking_id
     * @return array
     */
    private function get_booking_details($booking_id)
    {
        $booking = new Model_KES_Bookings($booking_id);
        $booking_detail = $booking->get();
        $booking_details = array(
            'booking_id'            => $booking_id,
			'booking_contact_id'    => $booking_detail['contact_id'],
			'booking_status'        => $booking_detail['booking_status'],
			'booking_created'       => $booking_detail['created_date'],
			'booking_updated'       => $booking_detail['modified_date'],
            'custom_discount'       => $booking_detail['custom_discount'],
            'discount_memo'         => $booking_detail['discount_memo'],
            'discount_coupon_code'  => $booking_detail['coupon_code']
        );
        return $booking_details;
    }

    /**
     * Fill the data to the template_array
     * @param $template
     * @param $data
     * @return array
     */
    private function fill_template_data($template, $data)
    {
        $template_array = array();
        foreach ($template as $key) {
            if (array_key_exists($key,$data)) {
                $template_array[$key] = $data[$key];
            } else {
                $template_array[$key] = '';
            }
            if ($template_array[$key] == null) {
                $template_array[$key] = '';
            }
        }
        return $template_array;
    }

    /**
     * @param $template The array of document tags to be filled Without _number
     *                      ie (start_time, end_time, schedule)
     * @param $data The data to be added to the array
     * @param $limit The number of element to fill
     * @return array (start_time_0, end_time_0, schedule_0)(start_time_1, end_time_1, schedule_1)
     */
    private function fill_array_template_data($template,$data,$limit)
    {
        $template_array = array();
        for ($i =0 ; $i < $limit ; $i++)
        {
            foreach($template as $key)
            {
                // Get a tag array element
                $value = $key . '_' . $i;
                if (array_key_exists($i, $data))
                {
                    if (array_key_exists($key, $data[$i]))
                    {
                        $template_array[$value] = $data[$i][$key];
                    }
                }
                else
                {
                    $template_array[$value] = '' ;
                }
            }
        }
        return $template_array;
    }

                /***** Individual Documents functions   *****/

    /**
     * @param $payment_id
     * @return array
     */
    public function booking_receipt($payment_id)
    {
        $payment        = $this->get_payment_details($payment_id);
        $transaction    = $this->get_transaction_details($payment['transaction_id']);
        $booking        = $this->get_booking_details($transaction['booking_id']);
        $contact        = $this->get_contact_details($booking['booking_contact_id'],$transaction['transaction_contact_id']);
        $schedules       = $this->get_schedules_details($payment['transaction_id']);
        $data           = array_merge($payment,$transaction,$booking,$contact,$this->system_details);
        $booking_receipt=array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3' ,
            'contact_town'  ,
            'contact_county'  ,
            'contact_postcode',
            'booking_id' ,
            'payment_amount'  ,
            'payment_type' ,
            'contact_student_first_name',
            'contact_student_last_name',
            'schedule_name',
            'course_name',
            'date_start' ,
            'time_start' ,
            'transaction_outstanding' ,
            'transaction_discount_amount',
            'transaction_payed',
            'transaction_amount',
            'transaction_discount',
            'transaction_total',
            'discount_type',
            'payment_amount'  ,
            'payment_type',
            'interestamount'
        );
        if ($transaction['transaction_type'] == 'Booking - Pay Now')
        {
            $contact['contact_id'] = $contact['contact_student_id'];
        }
        $template_schedule = $this->fill_array_template_data($this->_schedule_details,$schedules,12);
        $template=  $this->fill_template_data($booking_receipt,$data);
        if (! is_null($booking['custom_discount']) AND $booking['custom_discount']>0)
        {
            $data['transaction_discount'] = (string) $booking['custom_discount'];
            $data['discount_type'] = (string) $booking['discount_memo'];
        }
        else
        {
            $data['discount_type'] = (string) $booking['discount_memo'];
        }
        $template['transaction_outstanding'] = number_format($template['transaction_outstanding'] + $transaction['pp_interest_remaining'], 2);
        $template['subtotal'] = number_format($template['transaction_amount'], 2);
        $template['discount'] = number_format($template['transaction_discount_amount'] ? $template['transaction_discount_amount'] : 0, 2);
        $template['discounttype'] = $template['discount_type'];
        $template['totalamount'] = number_format($template['transaction_total'] + $transaction['pp_interest_total'], 2);
        $template['template_name'] = 'Payment_Receipt';
        $template['doc_postfix']  = 'Contact#'.$template['contact_id'].'_Transaction#'.$payment['transaction_id'].'_Payment#'.$payment_id.'-'.date('YmdHis');
        $result = array_merge($template, $template_schedule);
        $result['interestamount'] = number_format($transaction['pp_interest_total'], 2);
        $result['payment_amount'] = number_format($payment['payment_total'], 2);

        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        //header('content-type: text/plain');print_r($transaction);exit;
        return $result;
    }

    /**
     * Payment Reminder Template
     * @param $transaction_id
     * @return array
     */
    public function payment_reminder($transaction_id)
    {
        $transaction = $this->get_transaction_details($transaction_id);
        $booking = $this->get_booking_details($transaction['booking_id']);
        $contact        = $this->get_contact_details($booking['booking_contact_id'],$transaction['transaction_contact_id']);
        $schedules       = $this->get_schedules_details($transaction_id);
        $data           = array_merge($transaction,$booking,$contact,$this->system_details);
        $payment_reminder = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'transaction_payed',
            'payment_type',
            'transaction_outstanding',
            'contact_student_first_name',
            'contact_student_last_name',
            'schedule_name',
            'course_name',
            'date_start' ,
            'time_start'
        );
        if ($transaction['transaction_type'] == 'Booking - Pay Now')
        {
            $contact['contact_id'] = $contact['contact_student_id'];
        }
        $template_schedule = $this->fill_array_template_data($this->_schedule_details,$schedules,12);
        $template = $this->fill_template_data($payment_reminder,$data);
        $template['system_user_surname_name'] = $template['system_user_last_name'];
        $template['payment_amount'] = $template['transaction_payed'];
        $template['template_name'] = 'Payment_Reminder';
        $template['doc_postfix'] = 'Contact#'.$template['contact_id'].'-'.date('YmdHis');
        $result = array_merge($template, $template_schedule);
        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        //header('content-type: text/plain');print_r($result);exit;
        return $result;
    }

    /**
     * Booking Alteration Confirmation Template
     * @param $booking_id
     * @return array
     */
    public function booking_document($booking_id,$create=TRUE)
    {
        $booking = $this->get_booking_details($booking_id);
        $transaction = $this->get_transaction_details(NULL, $booking_id);
        $contact        = $this->get_contact_details($booking['booking_contact_id'],$transaction['transaction_contact_id']);
        $schedule       = $this->get_schedules_details(NULL,$booking_id);
        $data           = array_merge($transaction,$booking,$contact,$schedule,$this->system_details);

        $booking_alteration = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address' ,
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'transaction_outstanding',
            'contact_student_first_name',
            'contact_student_last_name',
            'schedule_name',
            'course_name',
            'date_start' ,
            'time_start'
        );
        if ($transaction['transaction_type'] == 'Booking - Pay Now')
        {
            $contact['contact_id'] = $contact['contact_student_id'];
        }
        $template_schedule = $this->fill_array_template_data($this->_schedule_details,$schedule,12);
        $template = $this->fill_template_data($booking_alteration,$data);
        if ($template['contact_id'] == null && @$contact['contact_student_id']) {
            $template['contact_id'] = @$contact['contact_student_id'];
        }
        $template['template_name'] = $create ? 'Booking' :'Booking_Alteration';
        $template['doc_postfix'] = 'Booking#'.$template['booking_id'].'-'.date('YmdHis');
        $result = array_merge($template, $template_schedule);
        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        return $result;
    }

    /**
     * Booking Cancellation Template
     * @param $booking_id
     * @return array
     */
    public function booking_cancellation($transaction_id)
    {
        $transaction    = $this->get_transaction_details($transaction_id,NULL);
        $booking        = $this->get_booking_details($transaction['booking_id']);
        $contact        = $this->get_contact_details($booking['booking_contact_id'],$transaction['transaction_contact_id']);
        $schedule       = $this->get_schedules_details($transaction_id,NULL,TRUE,FALSE);
        $data           = array_merge($transaction,$booking,$contact,$schedule,$this->system_details);
        $booking_cancellation = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_address4',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'schedule_name',
            'course_name',
            'date_start' ,
            'time_start'
        );
            $contact['contact_id'] = $contact['contact_student_id'];

        $template = $this->fill_template_data($booking_cancellation,$data);
        $template['template_name'] = 'Booking_Cancellation';
        $template['doc_postfix'] = 'Booking#'.$template['booking_id'].'-'.date('YmdHis');
        $result = $template;
        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        //header('content-type: text/plain');print_r($result);exit;
        return $result;
    }

    public function booking_cancellation2($booking_schedule_id)
    {
        $booking_schedule = Model_KES_Bookings::get_booking_by_bookingschedule($booking_schedule_id);
        $transaction = array();
        if (@$booking_schedule['transaction']['id']) {
            $transaction = $this->get_transaction_details(@$booking_schedule['transaction']['id'], null);
        }
        $booking = $this->get_booking_details($booking_schedule['booking_id']);
        $contact        = $this->get_contact_details($booking['booking_contact_id'], @$transaction['transaction_contact_id']);
        $schedule       = $this->get_schedule_details($booking_schedule['schedule_id'], false);
        $data           = array_merge($transaction, $booking, $contact, $schedule, $this->system_details);

        $booking_cancellation = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_address4',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'schedule_name',
            'course_name',
            'date_start' ,
            'time_start'
        );
        $contact['contact_id'] = $contact['contact_student_id'];

        $template = $this->fill_template_data($booking_cancellation, $data);
        $template['template_name'] = 'Booking_Cancellation';
        $template['doc_postfix'] = 'Booking#' . $template['booking_id'] . '-'.date('YmdHis');
        $result = $template;
        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        //header('content-type: text/plain');print_r($result);exit;
        return $result;
    }

    /**
     * Teacher Confirmation Template
     * @param $booking_id
     * @return array
     */
    public function teacher_booking_confirmation($transaction_id)
    {
        $transaction = $this->get_transaction_details($transaction_id,NULL);
        $booking = $this->get_booking_details($transaction['booking_id']);
        $contact        = $this->get_contact_details($booking['booking_contact_id'],$transaction['transaction_contact_id']);
        $schedule       = $this->get_schedules_details($transaction_id,NULL,TRUE);
        $teacher        = $this->get_teacher_detail($schedule['schedule_id']);
        $data           = array_merge($transaction,$booking,$contact,$schedule,$this->system_details,$teacher);
        $teacher_confirmation = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_address4',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'booking_created',
            'schedule_name',
            'schedule_cost',
            'contact_student_first_name',
            'contact_student_last_name',
            'schedule_teacher_first_name',
 	        'schedule_teacher_last_name',
            'course_name',
            'date_start' ,
            'time_start',
            'course_location',
            'contact_mobile_number'
        );
        if ($transaction['transaction_type'] == 'Booking - Pay Now')
        {
            $contact['contact_id'] = $contact['contact_student_id'];
        }
        $template = $this->fill_template_data($teacher_confirmation,$data);
        $template['template_name'] = 'Teacher_Booking_Confirmation';
        $template['doc_postfix'] = 'Booking#'.$template['booking_id'].'_Student-'.$template['contact_student_first_name'].'-'.date('YmdHis');
        $result = $template;
        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        //header('content-type: text/plain');print_r($result);exit;
        return $result;
    }

    public function teacher_booking_confirmation2($booking_schedule_id)
    {
        $booking_schedule = Model_KES_Bookings::get_booking_by_bookingschedule($booking_schedule_id);
        $transaction = array();
        if (@$booking_schedule['transaction']['id']) {
            $transaction = $this->get_transaction_details(@$booking_schedule['transaction']['id'], null);
        }
        $booking = $this->get_booking_details($booking_schedule['booking_id']);
        $contact        = $this->get_contact_details($booking['booking_contact_id'], @$transaction['transaction_contact_id']);
        $schedule       = $this->get_schedule_details($booking_schedule['schedule_id'], false);
        ///$this->get_schedules_details($transaction_id, NULL, TRUE);
        $teacher        = $this->get_teacher_detail($schedule['schedule_id']);
        $data           = array_merge($transaction,$booking,$contact,$schedule,$this->system_details,$teacher);
        $teacher_confirmation = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_address4',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'booking_created',
            'schedule_name',
            'schedule_cost',
            'contact_student_first_name',
            'contact_student_last_name',
            'schedule_teacher_first_name',
            'schedule_teacher_last_name',
            'course_name',
            'date_start' ,
            'time_start',
            'course_location',
            'contact_mobile_number'
        );
        //if ($transaction['transaction_type'] == 'Booking - Pay Now')
        $data['contact_id'] = $teacher['trainer_id'];
        $template = $this->fill_template_data($teacher_confirmation, $data);
        $template['template_name'] = 'Teacher_Booking_Confirmation';
        $template['doc_postfix'] = 'Booking#'.$template['booking_id'].'_Student-'.$template['contact_student_first_name'].'-'.date('YmdHis');
        $result = $template;
        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        //header('content-type: text/plain');print_r($result);exit;
        return $result;
    }

    /**
     * Teacher Cancellation Template
     * @param $booking_id
     * @return array
     */
    public function teacher_booking_cancellation($transaction_id)
    {
        $transaction = $this->get_transaction_details($transaction_id,NULL);
        $booking = $this->get_booking_details($transaction['booking_id']);
        $contact        = $this->get_contact_details($booking['booking_contact_id'],$transaction['transaction_contact_id']);
        $schedule       = $this->get_schedules_details($transaction_id,NULL,TRUE,FALSE);
        $teacher        = $this->get_teacher_detail($schedule['schedule_id']);
        $data           = array_merge($transaction,$booking,$contact,$schedule,$this->system_details,$teacher);
        $teacher_confirmation = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_address4',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'booking_updated',
            'booking_created',
            'schedule_name',
            'contact_student_first_name',
            'contact_student_last_name',
            'schedule_teacher_first_name',
            'schedule_teacher_last_name',
            'schedule_cost',
            'schedule_name',
            'course_name',
            'date_start' ,
            'time_start',
            'course_location',
            'contact_mobile_number'
        );
        $template = $this->fill_template_data($teacher_confirmation,$data);
        $template['template_name'] = 'Teacher_Booking_Cancellation';
        $template['doc_postfix'] = 'Booking#'.$template['booking_id'].'_Student-'.$template['contact_student_first_name'].'-'.date('YmdHis');
        $result = $template;
        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        //header('content-type: text/plain');print_r($result);exit;
        return $result;
    }

    public function teacher_booking_cancellation2($booking_schedule_id)
    {
        $booking_schedule = Model_KES_Bookings::get_booking_by_bookingschedule($booking_schedule_id);
        $transaction = array();
        if (@$booking_schedule['transaction']['id']) {
            $transaction = $this->get_transaction_details(@$booking_schedule['transaction']['id'], null);
        }
        $booking = $this->get_booking_details($booking_schedule['booking_id']);
        $contact        = $this->get_contact_details($booking['booking_contact_id'], @$transaction['transaction_contact_id']);
        $schedule       = $this->get_schedule_details($booking_schedule['schedule_id'], false);
        $teacher        = $this->get_teacher_detail($schedule['schedule_id']);
        $data           = array_merge($transaction, $booking, $contact, $schedule, $this->system_details, $teacher);

        $teacher_confirmation = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_address4',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'booking_updated',
            'booking_created',
            'schedule_name',
            'contact_student_first_name',
            'contact_student_last_name',
            'schedule_teacher_first_name',
            'schedule_teacher_last_name',
            'schedule_cost',
            'schedule_name',
            'course_name',
            'date_start' ,
            'time_start',
            'course_location',
            'contact_mobile_number'
        );
        $template = $this->fill_template_data($teacher_confirmation,$data);
        $template['template_name'] = 'Teacher_Booking_Cancellation';
        $template['doc_postfix'] = 'Booking#' . $template['booking_id'] . '_Student-' . $template['contact_student_first_name'] . '-' . date('YmdHis');
        $result = $template;
        foreach ($result as $key => $value) {
            if ($value === null) {
                $result[$key] = '';
            }
        }

        //header('content-type: text/plain');print_r($result);exit;
        return $result;
    }

    /**
     * @param $booking_id
     * @return array
     */
    public function booking_confirmation($booking_id)
    {
        $booking = $this->get_booking_details($booking_id);
        $transaction = $this->get_transaction_details(NULL,$booking_id);
        $contact        = $this->get_contact_details($booking['booking_contact_id'],$transaction['transaction_contact_id']);
        $schedules       = $this->get_schedules_details(NULL,$booking_id);
        $data           = array_merge($transaction,$booking,$contact,$schedules,$this->system_details);
        $booking_alteration = array(
            'TodaysDate',
            'system_user_first_name',
            'system_user_last_name',
            'contact_id',
            'contact_first_name',
            'contact_last_name',
            'contact_address',
            'contact_address1',
            'contact_address2',
            'contact_address3',
            'contact_address4',
            'contact_town',
            'contact_county',
            'contact_postcode',
            'booking_id',
            'transaction_outstanding',
            'contact_student_first_name',
            'contact_student_last_name',
            'schedule_name',
            'course_name',
            'date_start' ,
            'time_start'
        );
        if ($transaction['transaction_type'] == 'Booking - Pay Now')
        {
            $contact['contact_id'] = $contact['contact_student_id'];
        }
        $template = $this->fill_template_data($booking_alteration,$data);
        $template['template_name'] = 'Booking_Confirmation';
        $template['doc_postfix'] = 'Booking#'.$template['booking_id'].'-'.date('YmdHis');
        return $template;
    }

    public function student_details_and_bookings($student_id)
    {
        $contact       = $this->get_contact_details($student_id);
        $bookings      = Model_KES_Bookings::get_contact_family_bookings(null, $student_id);
        $booking_data  = [];
        foreach ($bookings as $key => $booking) {
            $start_timestamp = strtotime(strip_tags($booking['start_date']));

            $booking_data['schedule_name_'.$key] = strip_tags($booking['schedule_title']);
            $booking_data['location_'.$key]      = strip_tags($booking['location_name']);
            $booking_data['course_name_'.$key]   = strip_tags($booking['course_title']);
            $booking_data['date_start_'.$key]    = $start_timestamp ? date('j F Y', $start_timestamp) : '';
            $booking_data['time_start_'.$key]    = $start_timestamp ? date('H:i', $start_timestamp) : '';
        }

        $data          = array_merge($contact,  $booking_data, $this->system_details);
        $template_data = array(
            'contact_address',
            'contact_county',
            'contact_email',
            'contact_first_name',
            'contact_last_name',
            'contact_mobile_number',
            'contact_student_email',
            'contact_student_first_name',
            'contact_student_last_name',
            'contact_student_mobile_number',
            'contact_student_year',
            'contact_town',
            'TodaysDate',
        );

        for ($i = 0; $i < 10; $i++) {
            $template_data[] = 'course_name_'   . $i;
            $template_data[] = 'date_start_'    . $i;
            $template_data[] = 'location_'      . $i;
            $template_data[] = 'schedule_name_' . $i;
            $template_data[] = 'time_start_'    . $i;
        }

        $template                  = $this->fill_template_data($template_data, $data);
        $template['contact_id']    = $student_id;
        $template['template_name'] = 'Student_Details_and_Bookings';
        $template['doc_postfix']   = $student_id.'-'.date('YmdHis');

        return $template;
    }

    public function timetable($student_id, $after = null, $before = null)
    {
        $params = array();
        if ($student_id) {
            $params['student_id'] = $student_id;
        }

        if ($after) {
            $params['after'] = date::dmy_to_ymd($after);
        }

        if ($before) {
            $params['before'] = date::dmy_to_ymd($before);
        }

        $timeslots = Model_Timetables::search_slot($params);
        foreach ($timeslots as $i => $timeslot) {
            $timeslots[$i]['date'] = date('d/m/Y', strtotime($timeslot['datetime_start']));
            $timeslots[$i]['time'] = date('H:i', strtotime($timeslot['datetime_start']));
            $timeslots[$i]['teacher'] = $timeslot['contact'];
            $timeslots[$i]['sublocation'] = $timeslot['location'];
            $timeslots[$i]['location'] = $timeslot['plocation'];
        }

        $data = array();
        $student = new Model_Contacts3($student_id);
        $data['student_id'] = $student_id;
        $data['student'] = $student->get_first_name() . ' ' . $student->get_last_name();
        $data['email'] = $student->get_email() . '';
        $data['year'] = Model_Years::get_year($student->get_year_id());
        $data['From'] = $after;
        $data['To'] = $before;
        if (@$data['year']) {
            $data['year'] = $data['year']['year'];
        }
        $data['timeslots'] = $timeslots;

        //header('content-type: text/plain');print_r($data);exit;
        return $data;
    }
    public function certificate_of_attendance($data)
    {
        $date_format = 'j F Y';
        $booking     = new Model_Booking_Booking($data['booking_id']);
        $contact     = new Model_Contacts3_Contact($data['contact_id']);

        $course      = $booking->schedules->find()->course;
        $start_date  = $booking->get_start_date();
        $end_date    = $booking->get_end_date();
        $duration    = $booking->items->where_undeleted()->count_all();
        $attended    = $booking->items->where_attended()->where_undeleted()->count_all();
        $accrediation_bodies = Model_Providers::get_accreditation_bodies();
        $accrediation_bodies_ids = array();
        foreach($accrediation_bodies as $accrediation_body) {
            $accrediation_bodies_ids[] = $accrediation_body['id'];
        }
        $accredited_by_ids = DB::select('*')
            ->from(Model_Courses::TABLE_HAS_PROVIDERS)
            ->where('course_id', '=', $course->id)
            ->where('provider_id' , 'IN' , $accrediation_bodies_ids)
            ->join(Model_Providers::TABLE_PROVIDERS, 'left')->on('provider_id',  '=', 'id')
            ->execute()
            ->as_array();
        $accredited_by = array();
        if (!empty($accredited_by_ids)) {
            foreach($accredited_by_ids as $accredited_by_id) {
                $accredited_by[] = $accredited_by_id['name'];
            }
        }
        return [
            'first_name'        => $contact->first_name,
            'last_name'         => $contact->last_name,
            'course_name'       => trim($course->title),
            'level'             => trim($course->level->level),
            'accredited_toggle' => !empty($accredited_by) ? implode(', ', $accredited_by) : '',
            'start_date'        => $start_date ? date($date_format, strtotime($start_date)) : '',
            'end_date'          => $end_date   ? date($date_format, strtotime($end_date))   : '',
            'duration'          => $duration,
            'attended'          => $attended,
            'absences'          => $duration - $attended,
            'date_generated'    => date($date_format),
        ];
    }
} 