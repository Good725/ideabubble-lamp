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
            $contact_details['contact_student_title'] = $student->get_title();
            $contact_details['contact_student_first_name'] = $student->get_first_name();
            $contact_details['contact_student_last_name'] = $student->get_last_name();
            $pcontactid = $student->get_primary_contact();
            if ($pcontactid) {
                $contact_details['contact_id'] = $pcontactid;
                $pcontact = new Model_Contacts3($pcontactid);
                $address_id = $pcontact->get_residence();
                $contact_details['contact_address1'] = $pcontact->get_address_line1($address_id);
                $contact_details['contact_address2'] = $pcontact->get_address_line2($address_id);
                $contact_details['contact_address3'] = $pcontact->get_address_line3($address_id);
                $contact_details['contact_town'] = $pcontact->get_address_town($address_id);
                $contact_details['contact_county'] = $pcontact->get_address_county($address_id);
                $contact_details['contact_postcode'] = $pcontact->get_address_postcode($address_id);
                $contact_details['contact_mobile_number'] = $pcontact->get_mobile();
            } else {
                $contact_details['contact_id'] = $student_id;
                $address_id = $student->get_residence();
                $contact_details['contact_address1'] = $student->get_address_line1($address_id);
                $contact_details['contact_address2'] = $student->get_address_line2($address_id);
                $contact_details['contact_address3'] = $student->get_address_line3($address_id);
                $contact_details['contact_town'] = $student->get_address_town($address_id);
                $contact_details['contact_county'] = $student->get_address_county($address_id);
                $contact_details['contact_postcode'] = $student->get_address_postcode($address_id);
                $contact_details['contact_mobile_number'] = $student->get_mobile();
            }
        }
        if ( ! is_null($payer_id))
        {
            $contact_details['contact_id'] = $payer_id;
            $payer = new Model_Contacts3($payer_id);
            $address_id = $payer->get_residence();
            $contact_details['contact_title'] = $payer->get_title();
            $contact_details['contact_first_name'] = $payer->get_first_name();
            $contact_details['contact_last_name'] = $payer->get_last_name();
 	 	    $contact_details['contact_address1'] = $payer->get_address_line1($address_id);
 	 	    $contact_details['contact_address2'] = $payer->get_address_line2($address_id);
 	 	    $contact_details['contact_address3'] = $payer->get_address_line3($address_id);
            $contact_details['contact_town'] = $payer->get_address_town($address_id);
 	 	    $contact_details['contact_county'] = $payer->get_address_county($address_id);
            $contact_details['contact_postcode'] = $payer->get_address_postcode($address_id);
// 	 	    $contact_details['contact_country'] = $payer->get_address_country($address_id);
 	 	    $contact_details['contact_mobile_number'] = $payer->get_mobile();
        }
        $contact_details['contact_address4'] = $contact_details['contact_town'];
        $contact_details['contact_address'] = $contact_details['contact_address1'] ;
        if (isset($contact_details['contact_address2']) AND $contact_details['contact_address2'] != '')
        {
            $contact_details['contact_address'] .= "\n" . $contact_details['contact_address2'];
            $contact_details['contact_address'] .= (isset($contact_details['contact_address3']) AND $contact_details['contact_address3'] != '') ? "\n".$contact_details['contact_address3']: '';
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
        if ($user) {
            $user_detail = DB::select(
                array('name', 'system_user_first_name'),
                array('surname', 'system_user_last_name')
            )
                ->from('engine_users')->where('id', '=', $user['id'])->execute()->as_array();
            $this->system_details = $user_detail[0];
            $this->system_details['system_user_last_name'] = (isset($this->system_details['system_user_last_name']) AND ($this->system_details['system_user_last_name'] != '')) ? $this->system_details['system_user_last_name'] : '';
            $this->system_details['TodaysDate'] = date('D jS M Y');
        } else {
            $this->system_details = array('id' => null);
            $this->system_details['system_user_last_name'] = '';
            $this->system_details['TodaysDate'] = date('D jS M Y');
        }
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
            ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE,'b'))->on('b.period_id','=','s1.id')
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
                'booking_id' => $booking_id
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
            'payment_type'
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
        $template['transaction_outstanding'] = number_format($template['transaction_outstanding'], 2);
        $template['subtotal'] = number_format($template['transaction_amount'] + $template['transaction_discount_amount'], 2);
        $template['discount'] = number_format($template['transaction_discount_amount'] ? $template['transaction_discount_amount'] : 0, 2);
        $template['discounttype'] = $template['discount_type'];
        $template['totalamount'] = number_format($template['transaction_total'], 2);
        $template['template_name'] = 'Payment_Receipt';
        $template['doc_postfix']  = 'Contact#'.$template['contact_id'].'_Transaction#'.$payment['transaction_id'].'_Payment#'.$payment_id.'-'.date('YmdHis');
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

    public function interview_details($booking_id)
    {
        $template_data = array();
        $booking = DB::select(
            array('timeslots.datetime_start', 'interview_date'),
            array('courses.title', 'course_title'),
            array('courses.code', 'course_code'),
            array('bookings.contact_id', 'student_id'),

            array('students.first_name', 'student_first_name'),
            array('students.last_name', 'student_last_name'),
            array('emails.value', 'student_email'),
            array('mobiles.value', 'student_mobile'),

            array('guardians.id', 'guardian_id'),
            array('guardians.first_name', 'guardian_first_name'),
            array('guardians.last_name', 'guardian_last_name'),
            array('gemails.value', 'guardian_email'),
            array('gmobiles.value', 'guardian_mobile'),

            'bookings.booking_id',
            'bookings.contact_id',
            DB::expr("CONCAT_WS(' ', students.first_name, students.last_name) as student"),
            DB::expr("CONCAT_WS(' ', staffs.first_name, staffs.last_name) as staff"),
            array('courses.title', 'course'),
            'courses.code',

            array('schedules.name', 'schedule'),
            array('has_applications.data', 'data_json'),
            array('has_applications.student', 'student_json'),
            DB::expr("IF(plocations.id, plocations.name, locations.name) as location")
        )
            ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                ->join(array(Model_KES_Bookings::BOOKING_COURSES, 'has_courses'), 'left')
                    ->on('bookings.booking_id', '=', 'has_courses.booking_id')
                    ->on('has_courses.deleted', '=', DB::expr(0))
                    ->on('has_courses.booking_status', '<>', DB::expr(3))
                ->join(array(Model_KES_Bookings::BOOKING_APPLICATIONS, 'has_applications'), 'left')
                    ->on('bookings.booking_id', '=', 'has_applications.booking_id')
                    ->on('has_applications.status_id', '<>', DB::expr(3))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'students'), 'inner')
                    ->on('bookings.contact_id', '=', 'students.id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                    ->on('has_courses.course_id', '=', 'courses.id')
                ->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'), 'left')
                    ->on('bookings.booking_id', '=', 'items.booking_id')
                ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'left')
                    ->on('items.period_id', '=', 'timeslots.id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                    ->on('timeslots.schedule_id', '=', 'schedules.id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'staffs'), 'left')
                    ->on('schedules.trainer_id', '=', 'staffs.id')

                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'), 'left')
                    ->on('students.notifications_group_id', '=', 'emails.group_id')
                    ->on('emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'mobiles'), 'left')
                    ->on('students.notifications_group_id', '=', 'mobiles.group_id')
                    ->on('mobiles.notification_id', '=', DB::expr(2))

                ->join(array(Model_Family::FAMILY_TABLE, 'families'), 'left')
                    ->on('students.family_id', '=', 'families.family_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'guardians'), 'left')
                    ->on('families.primary_contact_id', '=', 'guardians.id')
                    ->on('guardians.id', '<>', 'students.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'gemails'), 'left')
                    ->on('guardians.notifications_group_id', '=', 'emails.group_id')
                    ->on('emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'gmobiles'), 'left')
                    ->on('guardians.notifications_group_id', '=', 'mobiles.group_id')
                    ->on('mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'locations'), 'left')
                    ->on('schedules.location_id', '=', 'locations.id')
                ->join(array(Model_Locations::TABLE_LOCATIONS, 'plocations'), 'left')
                    ->on('locations.parent_id', '=', 'plocations.id')
            ->where('bookings.booking_id', '=', $booking_id)
            ->execute()
            ->current();
        ;
        $booking['data_json'] = json_decode($booking['data_json'], true);
        $booking['student_json'] = json_decode($booking['student_json'], true);
        //header('content-type: text/plain');print_r($booking);exit;
        if ($booking['data_json']) {
            $template_data = array_merge($template_data, $booking['data_json']);
        }
        if ($booking['student_json']) {
            $template_data = array_merge($template_data, $booking['student_json']);
        }
        $template_data = array_merge($template_data, $booking);

        $data = array();
        $c = new Model_Contacts3();
//$date,$student,$staff,$course,$code
        $data['date'] = date('d/m/Y');
        $data['time'] = '';

        $data['location'] = @$template_data['location'];
        $data['interview_date'] = @$template_data['interview_date'];
        $data['schedule'] = @$template_data['schedule'];
        $data['code'] = @$template_data['code'];
        $data['course'] = @$template_data['course'];
        $data['staff'] = @$template_data['staff'];
        $data['name'] = $data['student'] = @$template_data['student'];
        $data['booking_id'] = @$template_data['booking_id'];
        $data['contact_id'] = @$template_data['contact_id'];

        $data['course_title'] = @$template_data['course_title'];
        $data['course_code'] = @$template_data['course_code'];

        $data['student_id'] = @$template_data['student_id'];
        $data['student_first_name'] = @$template_data['student_first_name'];
        $data['student_last_name'] = @$template_data['student_last_name'];
        $data['student_email'] = @$template_data['student_email'];
        $data['student_mobile'] = @$template_data['student_mobile'];

        $data['address1'] = @$template_data['address1'];
        $data['address2'] = @$template_data['address2'];
        $data['town'] = @$template_data['town'];
        $data['postcode'] = @$template_data['postcode'];
        $data['country'] = @$template_data['country'];

        $data['dob'] = @$template_data['dob'];
        $genders = array('M' => 'Male', 'F' => 'Female');
        $data['gender'] = @$genders[$template_data['gender']];
        $data['nationality'] = @$template_data['nationality_id'];
        $data['birth_country'] = $c->get_address_country(@$template_data['birth_country_id']) ?: @$template_data['birth_country_id'];
        $data['pps'] = @$template_data['pps'];
        $data['address1'] = @$template_data['address1'];
        $data['address2'] = @$template_data['address2'];
        $data['town'] = @$template_data['town'];
        $data['postcode'] = @$template_data['postcode'];
        $data['country'] = $c->get_address_country(@$template_data['country']) ?: @$template_data['country'];

        $data['guardian_id'] = @$template_data['guardian_id'];
        $data['guardian_first_name'] = @$template_data['guardian_first_name'];
        $data['guardian_last_name'] = @$template_data['guardian_last_name'];
        $data['guardian_email'] = @$template_data['guardian_email'];
        $data['guardian_mobile'] = @$template_data['guardian_mobile'];
        $data['relationship_to_student'] = @$template_data['relationship_to_student'];

        $data['current_school'] = @$template_data['current_school'];
        $data['school_roll_number'] = @$template_data['school_roll_number'];
        $data['leaving_cert_type'] = @$template_data['leaving_cert_type'];
        $data['year'] = @$template_data['year'];
        //$data['subjects'] = $template_data['subjects'];
        $data['last_college_attended'] = @$template_data['last_college_attended'];
        $data['college_course_taken'] = @$template_data['college_course_taken'];
        $data['college_entry_year'] = @$template_data['college_entry_year'];
        $data['college_leaving_year'] = @$template_data['college_leaving_year'];
        //$data['work_experience'] = $template_data['work_experience'];
        $data['certificates_other'] = @$template_data['certificates_other'];
        $data['leisure_activities'] = @$template_data['leisure_activities'];
        $data['has_special_needs'] = @$template_data['has_special_needs'] == 1 ? 'Yes' : 'No';
        $data['special_needs_details'] = @$template_data['special_needs_details'];
        $data['other'] = @$template_data['other'];

        $data['subjects'] = array();
        if (@$booking['data_json']['subjects'])
        foreach ($booking['data_json']['subjects'] as $subject) {
            $data['subjects'][] = $subject['name'] . ' - ' . $subject['level'] . ' - ' . $subject['grade'];
        }
        $data['subjects'] = implode("\r\n", $data['subjects']);

        $data['work_experience'] = array();
        if (@$booking['data_json']['work_experience'])
        foreach ($booking['data_json']['work_experience'] as $work_experience) {
            $data['work_experience'][] = $work_experience['year'] . ' - ' . $work_experience['details'];
        }
        $data['work_experience'] = implode("\r\n", $data['work_experience']);
        $data['other'] = $booking['data_json']['application']['other'] ?? '';

        foreach ($data as $i => $value) {
            if ($value == null) {
                $data[$i] = '';
            }
        }

        //print_r($data);exit;
        return $data;
    }

    public function timetable($student_id, $after = null, $before = null, $category_id = null)
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

        if ($category_id) {
            $params['category_id'] = $category_id;
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
        return $data;
    }
    
    public function fill_survey_data($survey_id, $survey_answers)
    {
        $survey_questions = Model_Survey::get_questions_from_survey($survey_id);
        $details = array();
        foreach($survey_questions as $survey_question_index => $survey_question)
        {
            $details['question_' . ($survey_question_index + 1)] = $survey_question['title'] . ": ";
            if($survey_question['stub'] != 'input' || $survey_question['stub'] == 'textarea')
            {
                $survey_answer_label = $survey_question['answer_title'] . ": ";
                $survey_answer_label .=  DB::select('sao.label')->from(array('plugin_survey_answer_options', 'sao'))
                ->where('sao.id', '=', $survey_question['answer_id'])->execute()->current()['label'] ?? 'Cannot retrieve answer type';
            } else {
                $survey_answer_label = "Custom answer: {$survey_answers[$survey_question_index + 1]}";
            }
            $details['question_' . ($survey_question_index + 1) . '_answer'] = "$survey_answer_label";
        }
        return $details;
    }
    
    public function work_experience_welcome_note($contact_id)
    {
        $details = array();
        $contact = new Model_Contacts3($contact_id);
        $details['student_first_name'] = $contact->get_first_name();
        $bookings = Model_KES_Bookings::get_contact_family_bookings(null, $contact_id);
        $date_format = Settings::instance()->get('date_format') ?: 'd/M/Y';
        // We just need the contact's latest booking information
        foreach ($bookings as $booking) {
            $application =  Model_KES_Bookings::get_application_details_by_booking_id($booking['booking_id']);
            $details['arrival_date'] = date($date_format, strtotime($application['data']['arrival_flight_date']));
            $host_family = new Model_Contacts3(Model_KES_Bookings::get_linked_booking_contacts($booking['booking_id'],
                Model_Contacts3::find_type('Host Family')['contact_type_id'])['id']);
            $details['hf_first_name'] = $host_family->get_first_name();
            $details['hf_surname'] = $host_family->get_last_name();
            $address_id = $host_family->get_residence();
            $details['hf_address_1'] = $host_family->get_address_line1($address_id);
            $details['hf_address_2'] = $host_family->get_address_line2($address_id);
            $details['hf_address_3'] = $host_family->get_address_line3($address_id);
            $details['hf_address_4'] = $host_family->get_address_town($address_id);
            $details['hf_address_5'] = (empty($host_family->get_address_postcode($address_id))) ?
                $host_family->get_address_county($address_id) : $host_family->get_address_county($address_id) . ', ' .
                $host_family->get_address_postcode($address_id);
            $details['hf_phone_number'] = $host_family->get_mobile();
            foreach($booking['schedules'] as $schedule) {
                $details['schedule_start_date'] = date($date_format,
                    strtotime($schedule['start_date']));
                break;
            }
            break;
        }
        
        $details['contact_id'] = $contact_id;
        $details['template_name'] = 'work_experience_welcome_note';
        $details['doc_postfix'] = $contact_id . '-' . date('YmdHis');
        
        return $details;
    }
    
    public function hf_summer_school_letter_house_drop($hf_contact_id) {
        $details = array();
        $date_format = Settings::instance()->get('date_format') ?: 'd/M/Y';
        $host_family = new Model_Contacts3($hf_contact_id);
        $details['hf_contact_name'] = $host_family->get_contact_name();
        $address_id = $host_family->get_residence();
        $details['hf_address_1'] = $host_family->get_address_line1($address_id);
        $details['hf_address_2'] = $host_family->get_address_line2($address_id);
        $details['hf_address_3'] = $host_family->get_address_line3($address_id);
        $details['hf_address_4'] = $host_family->get_address_town($address_id);
        $details['hf_address_5'] = (empty($host_family->get_address_postcode($address_id))) ?
            $host_family->get_address_county($address_id) : $host_family->get_address_county($address_id) . ', ' . $host_family->get_address_postcode($address_id);
        $details['hf_first_name'] = $host_family->get_first_name();
    
        // Get the host families linked bookings, if they have many, we assume it's the latest booking
        $hf_linked_bookings = Model_KES_Bookings::get_bookings_contacts_linked_to_contact($hf_contact_id);
        foreach ($hf_linked_bookings as $hf_linked_booking) {
            $booking_id = $hf_linked_booking['booking_id'];
            $student = new Model_Contacts3($hf_linked_booking['id']);
            $details['student_first_name'] = $student->get_first_name();
            $details['student_last_name'] = $student->get_last_name();
            $details['student_nationality'] = $student->get_nationality() ?? '';
    
            $student_booking_details = Model_KES_Bookings::get_details('16');
            foreach($student_booking_details['schedules'] as $student_booking_schedule_detail) {
                $details['schedule_name'] = $student_booking_schedule_detail['name'];
                $details['school_name'] = $student_booking_schedule_detail['location_name'];
            }
            // Get student medical info
            $student_preference_ids = $student->get_preferences_ids();
            $details['student_medical'] = '';
            foreach ($student_preference_ids as $student_preference_id) {
                $details['student_medical'] .= ($details['student_medical'] == '') ? '' : ', ';
                $preference = Model_Preferences::get_by_id($student_preference_id);
                if($preference['group'] == 'special') {
                    $details['student_medical'] .= $preference['label'];
                }
            }
    
            $date = new DateTime($student->get_date_of_birth());
            $now = new DateTime();
            $details['student_age'] = $now->diff($date)->y . '';
            $details['student_gender'] = $student->get_gender();
    
            $application = Model_KES_Bookings::get_application_details_by_booking_id($booking_id);
            $details['arrival_date'] = (!empty($application['data']['arrival_flight_date'])) ? 
                date($date_format, strtotime($application['data']['arrival_flight_date'])) : 'N/A';
            $details['arrival_flight_number'] = $application['data']['arrival_flight_number'];
            $details['arrival_time'] = $application['data']['arrival_flight_time'];
            $details['arrival_location'] = $application['data']['arrival_airport'];
    
            $details['departure_date'] = (!empty($application['data']['departure_flight_date'])) ? 
            date($date_format, strtotime($application['data']['departure_flight_date'])) : 'N/A';
            $details['departure_flight_number'] = $application['data']['departure_flight_number'];
            $details['departure_time'] = $application['data']['departure_flight_time'];
            $details['departure_location'] = $application['data']['departure_airport'];
            
            if (count($hf_linked_booking['schedules']) != 0) {
                $details['schedule_start_date'] = date($date_format,
                    strtotime($hf_linked_booking['schedules'][0]['start_date']));
            }
            break;
        }
        
        $details['contact_id'] = $hf_contact_id;
        $details['template_name'] = 'hf_summer_school_letter_house_drop';
        $details['doc_postfix'] = 'Host_family_booking#' . $booking_id . date('YmdHis');
    
        return $details;
    }
    
    public function academic_year_welcome_note($contact_id)
    {
        $details = array();
        $date_format = Settings::instance()->get('date_format') ?: 'd/M/Y';
        $contact = new Model_Contacts3($contact_id);
        $details['student_first_name'] = $contact->get_first_name();
        $details['student_last_name'] = $contact->get_last_name();
        $bookings = Model_KES_Bookings::get_contact_family_bookings(null, $contact_id);
        // We just need the contact's latest booking information
        foreach ($bookings as $booking) {
            $host_family = new Model_Contacts3(Model_KES_Bookings::get_linked_booking_contacts($booking['booking_id'],
                Model_Contacts3::find_type('Host Family')['contact_type_id'])['id']);
            $details['hf_contact_name'] = $host_family->get_contact_name();
            $address_id = $host_family->get_residence();
            $details['hf_address_1'] = $host_family->get_address_line1($address_id);
            $details['hf_address_2'] = $host_family->get_address_line2($address_id);
            $details['hf_address_3'] = $host_family->get_address_line3($address_id);
            $details['hf_address_4'] = $host_family->get_address_town($address_id);
            $details['hf_address_5'] = (empty($host_family->get_address_postcode($address_id))) ?
                $host_family->get_address_county($address_id) : $host_family->get_address_county($address_id) . ', ' .
                $host_family->get_address_postcode($address_id);
           $details['hf_mobile'] = $host_family->get_mobile() ?? '';
           
           $application = Model_KES_Bookings::get_application_details_by_booking_id($booking['booking_id']);
           $details['arrival_date'] = date($date_format, strtotime($application['data']['arrival_flight_date']));
           
           // School info
           $school = Model_Providers::get_provider($contact->get_school_id() ?? '0');
           $details['student_school'] = $school['name'];
           $details['school_address_1'] = $school['address1'] ?? '';
           $details['school_address_2'] = $school['address2'] ?? '';
           $details['school_address_3'] = $school['address3'] ?? '';
           $details['school_address_4'] = $school['address4'] ?? '';
           
           // Coordinator info
           $coordinator = new Model_Contacts3(Model_KES_Bookings::get_linked_booking_contacts($booking['booking_id'],
               Model_Contacts3::find_type('Coordinator')['contact_type_id'])['id']);
           $details['coordinator_contact_name'] = $coordinator->get_contact_name();
           $details['coordinator_mobile'] = $coordinator->get_mobile() ?? '';
           break;
        }
        
        $details['contact_id'] = $contact_id;
        $details['template_name'] = 'academic_year_welcome_note';
        $details['doc_postfix'] = '#' . $contact_id . '-' . date('YmdHis');
        return $details;
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

        $absent_count =  DB::select(DB::expr("count(*) as cnt"))
            ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'rollcall'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
            ->on('rollcall.timeslot_id', '=', 'timeslots.id')
            ->where('rollcall.delete', '=', 0)
            ->and_where('rollcall.attendance_status', '=', 'Absent')
            ->and_where('rollcall.booking_id', '=', $data['booking_id'])
            ->and_where('rollcall.delegate_id', '=', $data['contact_id'])
            ->execute()
            ->get('cnt');
        $attend_count =  DB::select(DB::expr("count(*) as cnt"))
            ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'rollcall'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
            ->on('rollcall.timeslot_id', '=', 'timeslots.id')
            ->where('rollcall.delete', '=', 0)
            ->and_where('rollcall.attendance_status', '<>', 'Absent')
            ->and_where('rollcall.booking_id', '=', $data['booking_id'])
            ->and_where('rollcall.delegate_id', '=', $data['contact_id'])
            ->execute()
            ->get('cnt');

        $absent_days_subquery =  DB::select(
            'rollcall.delegate_id', 'timeslots.schedule_id',
            DB::expr("date_format(timeslots.datetime_start, '%Y-%m-%d') as date")
        )
            ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'rollcall'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
            ->on('rollcall.timeslot_id', '=', 'timeslots.id')
            ->where('rollcall.delete', '=', 0)
            ->and_where('rollcall.attendance_status', '=', 'Absent')
            ->and_where('rollcall.booking_id', '=', $data['booking_id'])
            ->and_where('rollcall.delegate_id', '=', $data['contact_id'])
            ->group_by('rollcall.delegate_id')
            ->group_by('timeslots.schedule_id')
            ->group_by('date');
        $absent_days_count = DB::select(DB::expr("IFNULL(count(*),0) as daysabsent"))
            ->from(array($absent_days_subquery, 'ads'))
            ->group_by('delegate_id')
            ->group_by('schedule_id')
            ->execute()
            ->get('daysabsent');
        $present_days_subquery =  DB::select(
            'rollcall.delegate_id', 'timeslots.schedule_id',
            DB::expr("date_format(timeslots.datetime_start, '%Y-%m-%d') as date")
        )
            ->from(array(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE, 'rollcall'))
            ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
            ->on('rollcall.timeslot_id', '=', 'timeslots.id')
            ->where('rollcall.delete', '=', 0)
            ->and_where('rollcall.attendance_status', '<>', 'Absent')
            ->and_where('rollcall.attendance_status', 'is not', null)
            ->and_where('rollcall.booking_id', '=', $data['booking_id'])
            ->and_where('rollcall.delegate_id', '=', $data['contact_id'])
            ->group_by('rollcall.delegate_id')
            ->group_by('timeslots.schedule_id')
            ->group_by('date');
        $present_days_count = DB::select(DB::expr("IFNULL(count(*),0) as dayspresent"))
            ->from(array($present_days_subquery, 'pds'))
            ->group_by('delegate_id')
            ->group_by('schedule_id')
            ->execute()
            ->get('dayspresent');

        $values = [
            'studentname' => trim($contact->first_name . ' ' . $contact->last_name),
            'first_name'        => $contact->first_name,
            'last_name'         => $contact->last_name,
            'contact_id'        => $contact->id,
            'template_name'     => 'certificate_of_attendance',
            'course_name'       => trim($course->title),
            'course'       => trim($course->title),
            'level'             => trim($course->level->level),
            'accredited_toggle' => !empty($accredited_by) ? implode(', ', $accredited_by) : '',
            'accreditedby' => !empty($accredited_by) ? implode(', ', $accredited_by) : '',
            'start_date'        => $start_date ? date($date_format, strtotime($start_date)) : '',
            'startdate' => $start_date ? date($date_format, strtotime($start_date)) : '',
            'end_date'          => $end_date   ? date($date_format, strtotime($end_date))   : '',
            'enddate'          => $end_date   ? date($date_format, strtotime($end_date))   : '',
            'duration'          => (int)$duration,
            'attended'          => (int)$attend_count,
            'absences'          => (int)$absent_count,
            'date_generated'    => date($date_format),
            'daysabsent'        => (int)$absent_days_count,
            'dayspresent'       => (int)$present_days_count
        ];

        return $values;
    }

    public function student_report_card($data)
    {
        $student         = new Model_Contacts3_Contact($data['contact_id']);
        $academic_year   = new Model_Course_AcademicYear($data['academic_year_id']);
        $primary_contact = $student->family->primary_contact;
        $primary_contact = $primary_contact->id ? $primary_contact : $student;

        $address = array_filter([
            $primary_contact->address->address1,
            $primary_contact->address->address2,
            $primary_contact->address->address3,
            $primary_contact->address->town
        ]);

        $results     = $student->results->find_all();
        $subject_ids = [];
        $todo_ids    = [];

        $schedule_ids = [];;
        foreach ($results as $key => $result) {
            // Should filter at SQL-level.
            if ($result->todo->type == 'State-Exam' && $result->todo->datetime >= $academic_year->start_date && $result->todo->datetime <= $academic_year->end_date) {
                $subject_ids[] = $result->subject_id;
                $todo_ids[] = $result->todo_id;
                $schedule_ids[] = $result->schedule_id;
            }
        }

        $subject_ids = array_unique($subject_ids);
        $todo_ids    = array_unique($todo_ids);
        $todos_categories = $data['todo_categories'];

        $todos_categories = empty($todo_ids) ? [] : ORM::factory('Todo_Category')->where('id', 'in', $todos_categories)->find_all_undeleted();
        foreach ($todos_categories as $number => $todos_category) {
            $details['exam'.($number + 1).'_name'] = $todos_category->title;
            $details['exam'.($number + 1).'_total'] = 0;
        }

        $results_table = [];
        $subjects = empty($subject_ids) ? [] : ORM::factory('Course_Subject')->where('id', 'in', $subject_ids)->order_by('name')->find_all_undeleted();
        foreach ($subjects as $key => $subject) {
            $results_table[$key]['subject'] = $subject->name;

            foreach ($todos_categories as $number => $todo_category) {
                // foreach todo category, find all the results of the student
                $todos = $todo_category->todo;
                $todos = (!Auth::instance()->has_access('todos_edit')) ?
                    $todos->where('todo_item.results_published_datetime', '<', DB::expr('NOW()'))->find_all_undeleted()
                    : $todos->find_all_undeleted();
                $subject_category_results['percent'] =  '';
                $subject_category_results['grade'] =  '';
                foreach($todos as $todo)
                {
                    $result = $student->results->where('subject_id', '=', $subject->id)->where('todo_id', '=', $todo->id)->find();
                    $subject_category_results['percent'] .= ($result->get_result('percent') === null) ? '' : $result->get_result('percent').'%, ';
                    $subject_category_results['grade']   .= ($result->get_result('grade_name') === null) ? '': $result->get_result('grade_name').', ';
                }
                $results_table[$key]['exam' . ($number + 1) . '_percent'] = substr($subject_category_results['percent'], 0, -2);
                $results_table[$key]['exam' . ($number + 1) . '_grade']   = substr($subject_category_results['grade'],  0, -2);
                $details['exam'.($number + 1).'_total'] += $result->get_result('points');
            }
        }

        $bookings = $student->bookings->find_all();
        $attended = 0;
        foreach ($bookings as $booking) {
            $attended += $booking->items->where('timeslot_status', '=', 'Present')->find_all();
        }

        $details['academic_year_range']   = date('F Y', strtotime($academic_year->start_date)) . ' - ' . date('F Y', strtotime($academic_year->end_date)) ;
        $details['contact_address']       = $address;
        $details['contact_name']          = $primary_contact->get_full_name();
        $details['contact_id']            = $primary_contact->id;
        $details['template_name']         = !empty($data['card_type']) ? $data['card_type'] : 'student_report_card';
        $details['number_absent']         = $student->get_booked_classes(['academic_year_id' => $academic_year->id, 'attendance_status' => 'Absent'])->count();
        $details['number_attended']       = $student->get_booked_classes(['academic_year_id' => $academic_year->id, 'attendance_status' => 'Present'])->count();
        $details['number_late']           = $student->get_booked_classes(['academic_year_id' => $academic_year->id, 'attendance_status' => 'Late'])->count();
        $details['number_left_early']     = $student->get_booked_classes(['academic_year_id' => $academic_year->id, 'attendance_status' => 'Early Departures'])->count();
        $details['student_mobile_number'] = $student->get_notification('mobile');
        $details['student_id']            = $student->id;
        $details['student_name']          = $student->get_full_name();
        $details['student_year']          = $student->year->year;
        $details['todays_date']           = date('jS F Y');
        $details['table1']                = $results_table;

        // The following tags should only be used on sites that will never have more than one course per document.
        $schedule = new Model_Course_Schedule(isset($schedule_ids[0]) ? $schedule_ids[0] : null);
        $details['course'] = $schedule->course->title;
        $details['level'] = $schedule->course->level->level;

        return $details;
    }

    public function tutor_meeting($data)
    {
        $academic_year = new Model_Course_AcademicYear($data['academic_year_id']);
        $exam          = new Model_Todo_Item($data['exam_id']);
        $student       = new Model_Contacts3_Contact($data['contact_id']);
        $tutor         = new Model_Contacts3_Contact($data['tutor_id']);

        $subject_ids   = array_unique(array_keys($student->results->where('todo_id', '=', $data['exam_id'])->find_all()->as_array('subject_id')));
        $subjects      = empty($subject_ids) ? [] : ORM::factory('Course_Subject')->where('id', 'in', $subject_ids)->order_by('name')->find_all_undeleted();

        $results_table = [];
        $details['exam_total'] = 0;

        foreach ($subjects as $key => $subject) {
            $result = $student->results->where('subject_id', '=', $subject->id)->where('todo_id', '=', $data['exam_id'])->find()->get_result();

            $results_table[] = [
                'subject'      => $subject->name,
                'exam_percent' => ($result['percent'] === '') ? '' : $result['percent'].'%',
                'exam_grade'   => $result['grade_name']
            ];

            $details['exam_total'] += $result['points'];
        }

        $details['academic_year_range']   = date('F Y', strtotime($academic_year->start_date)) . ' - ' . date('F Y', strtotime($academic_year->end_date)) ;
        $details['exam_name']             = $exam->title;
        $details['number_absent']         = $student->get_booked_classes(['academic_year_id' => $academic_year->id, 'attendance_status' => 'Absent'])->count();
        $details['number_attended']       = $student->get_booked_classes(['academic_year_id' => $academic_year->id, 'attendance_status' => 'Present'])->count();
        $details['number_late']           = $student->get_booked_classes(['academic_year_id' => $academic_year->id, 'attendance_status' => 'Late'])->count();
        $details['number_left_early']     = $student->get_booked_classes(['academic_year_id' => $academic_year->id, 'attendance_status' => 'Early Departures'])->count();
        $details['student_mobile_number'] = $student->get_notification('mobile');
        $details['student_id']            = $student->id;
        $details['student_name']          = $student->get_full_name();
        $details['student_year']          = $student->year->year;
        $details['template_name']         = 'tutor_meeting';
        $details['todays_date']           = date('jS F Y');
        $details['tutor_name']            = $tutor->get_full_name();
        $details['table1']                = $results_table;

        return $details;
    }

    public function course_brochure($course_id)
    {
        $course = new Model_Course($course_id);
        $course_details = Model_Courses::get_detailed_info($course_id, true, true,
            (Settings::instance()->get('only_show_primary_trainer_course_dropdown') === '1'), true);
        $course_topics_details = Model_Courses::get_course($course_id);
        $schedules = $course_details['schedules'];
        $cheapest = PHP_INT_MAX;
        $schedule_ids = array();
        foreach($schedules as $schedule) {
            if ((float)$schedule['fee_amount'] <= (float)$cheapest) {
                $cheapest = $schedule['fee_amount'];
            }
            $schedule_ids[] = $schedule['id'];
        }
        $course_discounts = Model_KES_Discount::get_discounts_for_course($course_id);
        $course_discount_lines = array();
        $course_discount_value = 0;
        foreach ($course_discounts as $course_discount_item) {
            if (empty($course_discount_item['member_only'])) {
                continue;
            }
            $course_discount_line = array();
            if ($course_discount_item['amount_type'] == 'Fixed') {
                $course_discount_line['fixed'] = (float)$course_discount_item['amount'];
                $course_discount_value += $course_discount_line['fixed'];
            } else if ($course_discount_item['amount_type'] == 'Percent') {
                $course_discount_line['percent'] = round($cheapest
                    * ($course_discount_item['amount'] / 100), 2);
                $course_discount_value += $course_discount_line['percent'];
            } else if ($course_discount_item['amount_type']  == 'Quantity') {
                $course_discount_line['quantity']= (float)$course_discount_item['amount'];
                $course_discount_value += $course_discount_line['quantity'];
            }
            $discount_lines[$course_discount_item['id']] = $course_discount_line;
        }
        $schedule_lines = array();
        $schedule_discount_value= 0;
        $schedule_discounts = Model_KES_Discount::get_discounts_for_schedule($schedule_ids);
        foreach ($schedule_discounts as $schedule_discount) {
            if (empty($schedule_discount['member_only'])) {
                continue;
            }
            if (!empty($course_discount_lines)
                && array_key_exists($schedule_discount['id'], $course_discount_lines)) {
                continue;
            }
            $schedule_line = array();
            if ($schedule_discount['amount_type'] == 'Fixed') {
                $schedule_line['fixed'] = (float)$schedule_discount['amount'];
                $schedule_discount_value += $schedule_line['fixed'];
            } else if ($schedule_discount['amount_type'] == 'Percent') {
                $schedule_line['percent'] = round($cheapest
                    * ($schedule_discount['amount'] / 100), 2);
                $schedule_discount_value += $schedule_line['percent'];
            } else if ($schedule_discount['amount_type']  == 'Quantity') {
                $schedule_line['quantity'] = (float)$schedule_discount['amount'];
                $schedule_discount_value += $schedule_line['quantity'];
            }
            $schedule_lines[$schedule_discount['id']] = $schedule_line;
        }
        $member_fee = $cheapest - $course_discount_value - $schedule_discount_value;
        if ($member_fee <= 0 ) {
            $member_fee = 0;
        }
        $duration_string = '';
        $duration_schedule = reset($schedules);
        $schedule_date_start =  new DateTime($duration_schedule['start_date']);
        $schedule_date_end =  new DateTime($duration_schedule['end_date']);
        //$duration = $schedule_date_end->diff($schedule_date_start);
        /*if ($duration->y != 0) {
            $value = $duration->y;
            $measure = $duration->y > 1 ?  ' years ': ' year ';
            $duration_string .= $value . $measure;
        }
        if ($duration->m != 0) {
             $value = $duration->m;
             $measure = $duration->m > 1 ?  ' months ': ' month ';
             $duration_string .= $value . $measure;
        }
        if ($duration->d != 0) {
            $value = $duration->d;
            $measure = $duration->d > 1 ?  ' days ': ' day ';
            $duration_string .= $value . $measure;
        }*/
        $duration_string = Model_Schedules::get_duration($duration_schedule['id']);
        $fee_type_string = '';
        if ($duration_schedule['fee_per'] == 'Timeslot') {
            $fee_type_string = 'per class';
        }
        $available_slots = $course->get_available_times(['time_format' => 'H:i j F Y ']);
        $accredited_by_name = '';
        $accredited_by_string = '';
        if (!empty($course_topics_details['accredited_by'])) {
            $accredited_by = reset($course_topics_details['accredited_by']);
            $accreditation_provider = Model_Providers::get_provider($accredited_by);
            if (!empty($accreditation_provider)) {
                $accredited_by_name = $accreditation_provider['name'];
                $accredited_by_string = 'Accredited By';
            }
        }
        $timeslots_styles = '<style>
            table { 
                 border-collapse: collapse!important;
                 width: 100%; 
                 color: #e50695; 
                 font-size: 9pt;
             } 
            td, th {
                border-right: none;
                border-left: none;
                border-top: none;
                border-bottom: 1px solid #e50695; 
                text-align: left; 
                padding: 8px;
            }
            th {
               font-weight: bold; 
            }
          
            </style>';
        $timeslots_table = $course->render_timeslots_table();
        $styles = '<style>
               .sans-serif, 
               .sans-serif h1, 
               .sans-serif h2, 
               .sans-serif h3, 
               .sans-serif h4,
               .sans-serif p, 
               .sans-serif ul, 
               .sans-serif ol {
                   font-family: "Helvetica Neue", "Helvetica", "Arial", sans-serif;
                    color : #330033;
                    font-size: 9pt;
                    font-weight: 300;
               }
               .sans-serif h1, 
               .sans-serif h2, 
               .sans-serif h3, 
               .sans-serif h4 {
                    font-weight: bold;
                    line-height: 1!important;
                    margin-bottom: 0!important;
               }
               .sans-serif p {
                    margin: 0!important;
                    padding: 0!important;
               }
                .sans-serif .simplebox-content{ 
                    margin: 0!important;
               }
               .sans-serif  .simplebox-content ul { 
                   list-style: none!important;
                   margin: 0!important;
                   padding: 0!important;
                   padding-inline-start: 0!important;
               }
               
              
               
                </style>';
        $testimonials_styles = '<style>
               .sans-serif, 
               .sans-serif h1, 
               .sans-serif h2, 
               .sans-serif h3, 
               .sans-serif h4 {
                    font-weight: 300;
                    line-height: 1!important;
                    margin-bottom: 0!important;
                    font-family: "Helvetica Neue", "Helvetica", "Arial", sans-serif;
                    color : #e51594;
                    font-size: 9pt;
               }
              .sans-serif h1, 
               .sans-serif h2, 
               .sans-serif h3, 
               .sans-serif h4 {
                    font-weight: bold;
                    line-height: 1!important;
                    margin-bottom: 0!important;
               }
               </style>';
        $testimonials = $course->testimonials->as_doc_string(1);
        $time_array = !empty($available_slots) ? reset($available_slots) : array();
        $details = [
            'accreditation_provider' => $accredited_by_name,
            'accredited_by_string' => $accredited_by_string,
            'course_title' => trim($course_details['title']),
            'description'  => ['type' => 'block', 'html' =>  trim($course_details['description']), 'styles' => $styles],
            'duration'     => $duration_string,
            'end_date'     => !empty($schedule_date_end) ? $schedule_date_end->format('j F Y'): '',
            'end_time'     => !empty($time_array) ? date('H:i', strtotime($time_array['end_time'])) : '',
            'fee_per'      => $fee_type_string,
            'fee'          => $cheapest   ? '€'.str_replace('.00', '', $cheapest)   : '',
            'fee_member_per' => $fee_type_string,
            'level'        => $course_details['level'],
            'member_fee'   => $member_fee ? '€'.str_replace('.00', '', $member_fee) : '',
            'start_date'   => !empty($schedule_date_start) ? $schedule_date_start->format('j F Y'): '',
            'start_time'   => !empty($time_array) ? date('H:i', strtotime($time_array['start_time'])) : '',
            'summary'      => ['type' => 'block', 'html' => trim($course_details['summary'])],
            'timeslots'    => ['type' => 'block', 'styles' => $timeslots_styles, 'html' => trim($timeslots_table)],
            'testimonials' => ['type' => 'block', 'styles' => $testimonials_styles, 'html' => !empty($testimonials) ? '<h4>What our clients say</h4> <p>' . $testimonials  . '</p>': ''],
            'file_name'    => str_replace(' ', '_', strtolower(trim($course_details['title']))) . '_brochure'
        ];

        return $details;
    }
}