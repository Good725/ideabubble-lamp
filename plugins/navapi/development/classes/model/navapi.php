<?php
class Model_NAVAPI
{
    const TABLE_EVENTS = 'plugin_navapi_events';

    const API_NAME = 'NAVISION';

    public $base_url = 'https://virtserver.swaggerhub.com/SpanishPoint/IbecTraining/1.0.0';
    protected $curl = null;
    public $last_request;
    public $last_response;
    public $last_info;
    public $client_id = null;
    public $client_secret = null;
    public $scope = null;
    public $grant_type = null;
    public $ms_auth_url = null;
    protected $auth = null;
    protected $auth_expire = 0;
    protected static $shared_auth = null;
    protected static $shared_auth_expire = 0;
    protected $share_auth = true;

    public function __construct($share_auth = true)
    {
        $this->base_url = Settings::instance()->get('navapi_api_url');
        $this->client_id = Settings::instance()->get('navapi_client_id');
        $this->client_secret = Settings::instance()->get('navapi_client_secret');
        $this->scope = Settings::instance()->get('navapi_scope');
        $this->ms_auth_url = Settings::instance()->get('navapi_ms_auth_url');
        $this->share_auth = $share_auth;
    }

    public function auth()
    {
        //do not make multiple auth requests if one already shared
        if ($this->share_auth) {
            if (self::$shared_auth != null) {
                $this->auth = self::$shared_auth;
                $this->auth_expire = self::$shared_auth_expire;
            }
        }

        $time = time();
        if ($this->auth_expire > $time) {
            return true;
        }

        if ($this->client_id == null || $this->client_secret == null || $this->scope == null) {
            return false;
        }

        $auth = $this->request(
            'POST',
            '',
            array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'scope' => $this->scope . '/.default',
                'grant_type' => 'client_credentials'
            ),
            $this->ms_auth_url,
            false,
            false
        );
        if (@$auth['access_token'] != null) {
            $this->auth = $auth;
            $this->auth_expire = $time + $auth['expires_in'];
            if ($this->share_auth) {
                self::$shared_auth = $this->auth;
                self::$shared_auth_expire = $this->auth_expire;
            }
            return true;
        } else {
            $this->auth = null;
            $this->auth_expire = 0;
            return false;
        }
    }

    public function empty_to_null(&$val)
    {
        if ($val === "") {
            $val = null;
        }
    }

    protected function request($type, $uri, $params = null, $base_url = null, $auth = true, $json = true, $decode = true, $log_response = true)
    {
        $this->last_info = null;
        $this->last_request = null;
        $this->last_response = null;
        $this->response_headers = array();
        $headers = array(
            'Accept: application/json'
        );
        if ($auth) {
            if (!$this->auth()) {
                return false;
            }
            $headers[] = 'Authorization: ' . $this->auth['access_token'];
        }
        $url = ($base_url == null ? $this->base_url : $base_url) . $uri;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        /*curl_setopt(
            $this->curl,
            CURLOPT_HEADERFUNCTION,
            function($ch, $header){
                print_r($header);
            }
        );*/
        //curl_setopt($this->curl, CURLOPT_VERBOSE, true);

        if ($type != 'GET') {
            curl_setopt($this->curl, CURLOPT_POST, true);
            if ($json) {
                //array_walk_recursive($params, array($this, 'empty_to_null'));
                $json = json_encode($params, JSON_PRETTY_PRINT);
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Content-Length: ' . strlen($json);
                curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);
            } else {
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
            }
        } else {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        }

        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $t1 = microtime(true);
        $response = curl_exec($this->curl);
        $t2 = microtime(true);
        $this->last_request = $params;
        if ($log_response) {
            $this->last_response = $response;
        }
        $this->last_info = curl_getinfo($this->curl);
        curl_close($this->curl);
        Model_ExternalRequests::create(($base_url == null ? $this->base_url : $base_url), $uri, $params, ($log_response ? $this->last_response : null), $this->last_info['http_code'], null, null, $t2 - $t1);
        if ($decode) {
            return json_decode($response, true);
        } else {
            return $response;
        }
    }

    public function event_list()
    {
        $events = $this->request('GET', '/event/list?' . http_build_query(array('startIndex' => 0, 'pageLength' => 100000)), null, null, true, null, true, false);
        return $events;
    }

    public function event_sync()
    {
        try {
            $existing_events = DB::select("*")->from(self::TABLE_EVENTS)->execute()->as_array('remote_event_no');
            $events = $this->event_list();
            $ids = array();
            if (empty($events)) {
                return;
            }
            foreach ($events as $event) {
                $ids[] = $event['eventNo'];
            }
            Database::instance()->begin();
            DB::delete(self::TABLE_EVENTS)
                ->where('remote_event_no', 'not in', $ids)
                ->and_where('schedule_id', 'is', null)
                ->execute();
            foreach ($events as $event) {
                if (isset($existing_events[$event['eventNo']])) {
                    DB::update(self::TABLE_EVENTS)
                        ->set(
                            array(
                                'remote_event_title' => $event['eventTitle'],
                                'remote_venue' => $event['venue'],
                                'remote_cost_centre' => $event['costCentre'],
                                'remote_description' => $event['description'],
                                'remote_start_date' => $event['startDate'],
                                'remote_end_date' => $event['endDate'],
                                'remote_event_date' => $event['eventDate'],
                                'remote_status' => $event['eventStatus']
                            )
                        )->where('remote_event_no', '=', $event['eventNo'])
                        ->execute();
                    if (strtotime($existing_events[$event['eventNo']]['remote_event_date']) != strtotime($event['eventDate']) && $existing_events[$event['eventNo']]['schedule_id'] != null) {
                        Model_Automations::run_triggers(
                            Model_Navapi_Datechangetrigger::NAME,
                            array(
                                'scheduleid' => $existing_events[$event['eventNo']]['schedule_id'],
                                'oldeventdate' => $existing_events[$event['eventNo']]['remote_event_date'],
                                'neweventdate' => $event['eventDate']
                            )
                        );
                    }
                } else {
                    DB::insert(self::TABLE_EVENTS)
                        ->values(
                            array(
                                'remote_event_no' => $event['eventNo'],
                                'remote_event_title' => $event['eventTitle'],
                                'remote_venue' => $event['venue'],
                                'remote_cost_centre' => $event['costCentre'],
                                'remote_description' => $event['description'],
                                'remote_start_date' => $event['startDate'],
                                'remote_end_date' => $event['endDate'],
                                'remote_event_date' => $event['eventDate'],
                                'remote_status' => $event['eventStatus']
                            )
                        )->execute();
                }
            }

            $date_mismatches = DB::select('navevents.*', 'schedules.start_date')
                ->from(array(self::TABLE_EVENTS, 'navevents'))
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('navevents.schedule_id', '=', 'schedules.id')
                ->where('schedules.delete', '=', 0)
                ->and_where(DB::expr("cast(schedules.start_date as date)"), "<>", DB::expr("cast(navevents.remote_event_date as date)"))
                ->execute()
                ->as_array();

            foreach ($date_mismatches as $date_mismatch) {
                Model_Automations::run_triggers(
                    Model_Messaging_Systemmessagetrigger::NAME,
                    array(
                        'message' => 'Schedule start date does not match navision event date',
                        'schedule_id' => $date_mismatch['schedule_id'],
                        'type' => 'warning',
                        'link' => 'https://' . $_SERVER['HTTP_HOST'] . '/admin/courses/edit_schedule/?id=' . $date_mismatch['schedule_id']
                    )
                );
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            Model_Errorlog::save($exc);
        }
    }

    public static function event_search($params = array())
    {
        $select = DB::select('*')
            ->from(array(self::TABLE_EVENTS, 'events'));

        $select->order_by('remote_event_no');
        $events = $select->execute()->as_array();
        return $events;
    }

    public static function event_link_schedule($id, $schedule_id)
    {
        DB::update(self::TABLE_EVENTS)
            ->set(
                array('schedule_id' => null)
            )->where('schedule_id', '=', $schedule_id)
            ->execute();

        DB::update(self::TABLE_EVENTS)
            ->set(
                array('schedule_id' => $schedule_id)
            )->where('id', '=', $id)
            ->execute();
    }

    public function sync_bookings()
    {
        $bookings = DB::select('bookings.booking_id')
            ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                ->join(array(Model_Remotesync::SYNC_TABLE, 'sync'), 'left')
                    ->on('bookings.booking_id', '=', 'sync.cms_id')
                    ->on('sync.type', '=', DB::expr("'" . self::API_NAME . '-Booking' . "'"))
            ->where('sync.cms_id', 'is', null)
            ->and_where('bookings.delete', '=', 0)
            ->and_where('bookings.booking_status', '<>', 3)
            ->execute()
            ->as_array();
        foreach ($bookings as $booking) {
            $this->create_booking($booking['booking_id']);
        }
    }

    public function sync_transactions($ids = array(), $booking_ids = array())
    {
        $transactions_select = DB::select('transactions.id')
            ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                ->join(array(Model_Kes_Transaction::TYPE_TABLE, 'txtypes'), 'inner')
                    ->on('transactions.type', '=', 'txtypes.id')
                ->join(array(Model_Remotesync::SYNC_TABLE, 'sync'), 'left')
                    ->on('transactions.id', '=', 'sync.cms_id')
                    ->on('sync.type', '=', DB::expr("'" . self::API_NAME . '-Transaction' . "'"))
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                    ->on('transactions.booking_id', '=', 'bookings.booking_id')
            ->where('transactions.deleted', '=', 0)
            ->and_where('bookings.delete', '=', 0);
        if (count($ids) > 0) {
            $transactions_select->and_where('transactions.id', 'in', $ids);
        }
        if (count($booking_ids) > 0) {
            $transactions_select->and_where('transactions.booking_id', 'in', $booking_ids);
        }
        $transactions = $transactions_select
            ->and_where_open()
                ->or_where('sync.cms_id', 'is', null)
                ->or_where('transactions.updated', '>', DB::expr('sync.synced'))
            ->and_where_close()
            ->execute()
            ->as_array();
        foreach ($transactions as $transaction) {
            $this->create_transaction($transaction['id']);
        }
        $this->sync_payments(array(), array(), $booking_ids);
    }

    public function sync_transaction_pdfs()
    {
        $transactions = DB::select('transactions.id')
            ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                ->join(array(Model_Kes_Transaction::TYPE_TABLE, 'txtypes'), 'inner')
                    ->on('transactions.type', '=', 'txtypes.id')
                ->join(array(Model_Remotesync::SYNC_TABLE, 'sync'), 'left')
                    ->on('transactions.id', '=', 'sync.cms_id')
                    ->on('sync.type', '=', DB::expr("'" . self::API_NAME . '-PDF' . "'"))
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                    ->on('transactions.booking_id', '=', 'bookings.booking_id')
            ->where('transactions.deleted', '=', 0)
            ->and_where('bookings.delete', '=', 0)
            ->and_where_open()
                ->or_where('sync.cms_id', 'is', null)
                ->or_where('transactions.updated', '>', DB::expr('sync.synced'))
            ->and_where_close()
            ->execute()
            ->as_array();
        $rs = new Model_Remotesync();
        foreach ($transactions as $transaction) {
            $pdf = $this->get_pdf($transaction['id']);
            if ($pdf) {
                $rs->save_object_synced(self::API_NAME . '-PDF', $transaction['id'], $transaction['id']);
            }
        }
    }

    public function sync_payments($ids = array(), $transaction_ids = array(), $booking_ids = array())
    {
        $paymentsq = DB::select('payments.id')
            ->from(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'))
                ->join(array(Model_Kes_Payment::STATUS_TABLE, 'payment_statuses'), 'inner')
                    ->on('payments.status', '=', 'payment_statuses.id')
            ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                ->on('payments.transaction_id', '=', 'transactions.id')
            ->join(array(Model_Kes_Transaction::TYPE_TABLE, 'txtypes'), 'inner')
                ->on('transactions.type', '=', 'txtypes.id')
            ->join(array(Model_Remotesync::SYNC_TABLE, 'sync'), 'left')
                ->on('payments.id', '=', 'sync.cms_id')
                ->on('sync.type', '=', DB::expr("'" . self::API_NAME . '-Payment' . "'"))
            ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                ->on('transactions.booking_id', '=', 'bookings.booking_id')
            ->where('sync.cms_id', 'is', null)
            ->and_where('payments.deleted', '=', 0)
            ->and_where('transactions.deleted', '=', 0)
            ->and_where('txtypes.credit', '=', 1)
            ->and_where('bookings.delete', '=', 0)
            ->and_where('payment_statuses.credit', '=', 1);
        if (!empty($ids)) {
            $paymentsq->and_where('payments.id', 'in', $ids);
        }
        if (!empty($transaction_ids)) {
            $paymentsq->and_where('transactions.id', 'in', $transaction_ids);
        }
        if (!empty($booking_ids)) {
            $paymentsq->and_where('transactions.booking_id', 'in', $booking_ids);
        }
        $payments = $paymentsq->execute()->as_array();
        foreach ($payments as $payment) {
            $this->create_payment($payment['id']);
        }
    }

    public function get_booking($booking_id)
    {
        $booking_data = $this->request('GET', '/booking/findById?bookingId=' . $booking_id);
        return $booking_data;
    }

    public function create_booking($booking_id)
    {
        $rs = new Model_Remotesync();
        $booking_synced = $rs->get_object_synced(Model_NAVAPI::API_NAME . '-Booking', $booking_id);
        if ($booking_synced) {
            $this->sync_transactions(array(), array($booking_id));
            return true;
        }

        $booking_select = DB::select(
            'bookings.*',
            'navapi_events.remote_event_no',
            'navapi_events.remote_event_title',
            'leadbookers.first_name',
            'leadbookers.last_name',
            array('leadbooker_emails.value', 'email'),
            DB::expr("CONCAT_WS('', leadbooker_phones.country_dial_code,leadbooker_phones.dial_code, leadbooker_phones.value) as phone"),
            DB::expr("CONCAT_WS('', leadbooker_mobiles.country_dial_code,leadbooker_mobiles.dial_code, leadbooker_mobiles.value) as mobile"),
            /*'leadbooker_addresses.address1',
            'leadbooker_addresses.address2',
            'leadbooker_addresses.address3',
            'leadbooker_addresses.town',
            'leadbooker_addresses.county',
            'leadbooker_addresses.country',
            'leadbooker_addresses.postcode'*/
            'booking_addresses.address1',
            'booking_addresses.address2',
            'booking_addresses.address3',
            'booking_addresses.town',
            array('counties.name', 'county'),
            array('counties2.name', 'county2'),
            'booking_addresses.country',
            'booking_addresses.postcode'
        )
            ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                ->join(array(Model_KES_Bookings::BOOKING_SCHEDULES, 'has_schedules'), 'inner')
                    ->on('has_schedules.booking_id', '=', 'bookings.booking_id')
                    ->on('has_schedules.deleted', '=', DB::expr(0))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->join(array(Model_NAVAPI::TABLE_EVENTS, 'navapi_events'), 'inner')
                    ->on('schedules.id', '=', 'navapi_events.schedule_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'leadbookers'), 'inner')
                    ->on('bookings.contact_id', '=', 'leadbookers.id')
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'leadbooker_emails'), 'left')
                    ->on('leadbookers.notifications_group_id', '=', 'leadbooker_emails.group_id')
                    ->on('leadbooker_emails.notification_id', '=', DB::expr(1))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'leadbooker_phones'), 'left')
                    ->on('leadbookers.notifications_group_id', '=', 'leadbooker_phones.group_id')
                    ->on('leadbooker_phones.notification_id', '=', DB::expr(3))
                ->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'leadbooker_mobiles'), 'left')
                    ->on('leadbookers.notifications_group_id', '=', 'leadbooker_mobiles.group_id')
                    ->on('leadbooker_mobiles.notification_id', '=', DB::expr(2))
                ->join(array(Model_Residence::ADDRESS_TABLE, 'leadbooker_addresses'), 'left')
                    ->on('leadbookers.residence', '=', 'leadbooker_addresses.address_id')
                ->join(array(Model_Residence::ADDRESS_TABLE, 'booking_addresses'), 'left')
                    ->on('bookings.billing_address_id', '=', 'booking_addresses.address_id')
                ->join(array('engine_counties', 'counties'), 'left')
                    ->on('booking_addresses.county', '=', 'counties.id')
                ->join(array('plugin_courses_counties', 'counties2'), 'left') // this is a bug some code piece use engine_counties some use courses_counties. need bigger fix
                    ->on('booking_addresses.county', '=', 'counties2.id')
            ->where('bookings.booking_id', '=', $booking_id)
            ->group_by('bookings.booking_id')
            ->group_by('has_schedules.schedule_id');
        $booking = $booking_select->execute()->current();

        if ($booking) {
            if ($booking['county'] == '') {
                $booking['county'] = $booking['county2'];
                unset($booking['county2']);
            }
            if ($booking['email'] == '') {
                return null;
            }
            $delegates = DB::select(array('contacts.first_name', 'firstName'), array('contacts.last_name', 'lastName'))
                ->from(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'))
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                        ->on('has_delegates.contact_id', '=', 'contacts.id')
                ->where('has_delegates.booking_id', '=', $booking_id)
                ->and_where('has_delegates.deleted', '=', 0)
                ->execute()
                ->as_array();
            if ($booking['booking_status'] != 6 && $booking['invoice_details'] == '' && $booking['payment_method'] == 'invoice') {
                return null;
            }
            $org_booking = false;
            $company = null;
            $company_contact_id = $booking['contact_id'];
            $org_address = null;
            if(Model_Plugin::is_enabled_for_role('Administrator', 'cdsapi')) {
                $organization_type = Model_Contacts3::find_type('organisation');
                $related_contact_ids = Model_Contacts3::get_parent_related_contacts($booking['contact_id']);
                foreach ($related_contact_ids as $related_contact_id) {
                    $org_contact = new Model_Contacts3($related_contact_id);
                    if ($org_contact->get_type() == $organization_type['contact_type_id']) {
                        $org_booking = true;
                        $company_contact_id = $org_contact->get_id();
                        $org_address = $org_contact->get_billing_address();
                        $booking['address1'] = $org_address->get_address1();
                        $booking['address2'] = $org_address->get_address2();
                        $booking['address3'] = $org_address->get_address3();
                        $booking['town'] = $org_address->get_town();
                        $booking['county'] = $org_address->get_county_name();
                        $booking['postcode'] = $org_address->get_postcode();
                        $booking['country'] = $org_address->get_country();
                        break;
                    }
                }
                $cds = new Model_CDSAPI();
                $company_account = $cds->get_account($company_contact_id);
                if ($company_account) {
                    $company = '' . (@$company_account['sp_companyno'] ? @$company_account['sp_companyno'] : @$company_account['sp_tablekey']);
                }
            }
            $cash_account = false;
            if ($org_booking) {
                $cash_account = $booking['payment_method'] == 'cc';
                if ($company == '') {
                    return null;
                }
            } else {
                if ($booking['booking_status'] == 6) { // sales quote
                    $company = 'cash';
                } else {
                    $cash_account = $booking['payment_method'] == 'cc';
                    /*if ($booking['payment_method'] == 'cc') {
                        $company = 'cash';
                    }*/
                    $company = 'cash';
                }
            }
            if ($company == '' && $booking['booking_status'] != 6) {
                return null;
            }
            $request_data = array(
                //'entryNo' => 0,
                'bookingID' => (int)$booking_id ?: null,
                'eventID' => $booking['remote_event_no'] ?: null,
                'cashAccount' => $cash_account,
                'company' => $company ?: null,
                'leadBooker' => array(
                    'firstName' => $booking['first_name'] ?: null,
                    'lastName' => $booking['last_name'] ?: null,
                    'email' => "" . $booking['email'] ?: null,
                    'addressLine1' => "" . urlencode($booking['address1']),
                    'addressLine2' => "" . $booking['address2'],
                    'addressLine3' => "" . $booking['address3'],
                    'city' => "" . $booking['town'],
                    'county' => "" . $booking['county'],
                    'postCode' => "" . $booking['postcode'],
                    'countryCode' => "" . $booking['country'],
                    'phone' => "" . $booking['mobile']
                ),
                'transactions' => array(),
                'remainingAmount' => 0,
                'errorText' => '',
                'processedDateTime' => date(DATE_RFC3339, strtotime($booking['created_date']))
            );

            $transactions = DB::select('transactions.*')
                ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                    ->join(array(Model_Kes_Transaction::TYPE_TABLE, 'txtypes'), 'inner')
                        ->on('transactions.type', '=', 'txtypes.id')
                ->where('booking_id', '=', $booking_id)
                ->and_where('deleted', '=', 0)
                ->and_where('txtypes.credit', '=', 1)
                ->execute()
                ->as_array();
            foreach ($transactions as $transaction) {
                $tx_data = $this->create_transaction($transaction['id'], null, true);
                if ($tx_data) {
                    $request_data['remainingAmount'] += $tx_data['totalAmount'];
                    $request_data['transactions'][] = $tx_data;
                }
            }
            /*ghost transaction for sales quote
            if (count($transactions) == 0 && $booking['booking_status'] == 6) {
                $tx_data = $this->create_transaction(null, $booking_id, true);
                if ($tx_data) {
                    $request_data['transactions'][] = $tx_data;
                }
            }*/
            //header('content-type: text/plain');print_r($request_data);exit;
            $response = $this->request('POST', '/booking', $request_data);
            if (@$response['entryNo']) {
                $rs = new Model_Remotesync();
                $rs->save_object_synced(Model_NAVAPI::API_NAME . '-Booking', $response['entryNo'], $booking_id);

                foreach ($transactions as $transaction) {
                    $rs->save_object_synced(Model_NAVAPI::API_NAME . '-Transaction', $transaction['id'], $transaction['id']);
                }
                foreach ($transactions as $transaction) {
                    $paymentsq = DB::select('payments.*', 'transactions.booking_id')
                        ->from(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'))
                            ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                                ->on('payments.transaction_id', '=', 'transactions.id')
                            ->join(array(Model_Kes_Payment::STATUS_TABLE, 'payment_statuses'), 'inner')
                                ->on('payments.status', '=', 'payment_statuses.id')
                        ->where('transactions.id', '=', $transaction['id'])
                        ->and_where('payments.deleted', '=', 0)
                        ->and_where('payment_statuses.credit', '=', 1);
                    $payments = $paymentsq->execute()->as_array();
                    foreach ($payments as $payment) {
                        $this->create_payment($payment['id']);
                    }
                }/*
                if (count($transactions) == 0 && $booking['booking_status'] == 6) {
                    $this->create_transaction(0, $booking_id);
                }*/
            }
            return true;
        } else {
            return false;
        }
    }

    public function create_transaction($transaction_id, $quote_id_without_transaction = null, $return_data_only = false)
    {
        $rs = new Model_Remotesync();
        $transaction_synced = $rs->get_object_synced(Model_NAVAPI::API_NAME . '-Transaction', $transaction_id);
        if ($transaction_synced) {
            return true;
        }

        $transaction = null;
        if ($transaction_id) {
            $transaction = DB::select(
                'transactions.*', 'transaction_types.credit', 'transaction_types.type',
                'bookings.booking_status', 'bookings.payment_method', 'bookings.invoice_details'
            )
                ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                   ->join(array(Model_Kes_Transaction::TYPE_TABLE, 'transaction_types'), 'inner')
                        ->on('transactions.type', '=', 'transaction_types.id')
                    ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                        ->on('transactions.booking_id', '=', 'bookings.booking_id')
                ->where('transactions.id', '=', $transaction_id)
                ->and_where('transactions.deleted', '=', 0)
                ->execute()
                ->current();
        }
        if ($transaction || $quote_id_without_transaction) {
            if ($quote_id_without_transaction) {
                $booking_id = $quote_id_without_transaction;
            } else {
                $booking_id = $transaction['booking_id'];
                if (!$return_data_only) {
                    $rs = new Model_Remotesync();
                    $booking_synced = $rs->get_object_synced(Model_NAVAPI::API_NAME . '-Booking', $booking_id);
                    if (!$booking_synced) {
                        $this->create_booking($booking_id);
                        $booking_synced = $rs->get_object_synced(Model_NAVAPI::API_NAME . '-Booking', $booking_id);
                        if (!$booking_synced) {
                            return false;
                        }
                        $transaction_synced = $rs->get_object_synced(Model_NAVAPI::API_NAME . '-Transaction', $transaction_id);
                        if ($transaction_synced) {
                            return true;
                        }
                    }
                }
            }
            $booking = DB::select('*')
                ->from(Model_KES_Bookings::BOOKING_TABLE)
                ->where('booking_id', '=', $booking_id)
                ->execute()
                ->current();
            if ($quote_id_without_transaction) {
                $ghost_tx_inserted = DB::insert(Model_KES_Bookings::TRANSACTIONS_TABLE)
                    ->values(array('booking_id' => $booking_id, 'deleted' => 1))
                    ->execute();
                $transaction = array(
                    'amount' => $booking['amount'],
                    'total' => $booking['amount'],
                    'discount' => 0,
                    'credit' => 1,
                    'id' => $ghost_tx_inserted[0]
                );
            }
            $member_discount = "";
            $member_discount_amount = 0;
            $nonmember_discount = "";
            $nonmember_discount_amount = 0;
            $discounts = DB::select('discounts.*', array('has_discounts.amount', 'discount_amount'))
                ->from(array(Model_KES_Bookings::DISCOUNTS, 'has_discounts'))
                    ->join(array(Model_KES_Discount::DISCOUNTS_TABLE, 'discounts'), 'inner')
                        ->on('has_discounts.discount_id', '=', 'discounts.id')
                ->where('discounts.delete', '=', 0)
                ->and_where('has_discounts.amount', '>', 0)
                ->and_where('has_discounts.booking_id', '=', $booking_id)
                ->execute()
                ->as_array();
            foreach ($discounts as $discount) {
                if ($discount['member_only'] == 1) {
                    $member_discount = $discount['title'];
                    $member_discount_amount += $discount['discount_amount'];
                } else {
                    $nonmember_discount = $discount['title'];
                    $nonmember_discount_amount += $discount['discount_amount'];
                }
            }
            if ($quote_id_without_transaction) {
                $transaction['amount'] -= $member_discount_amount;
                $transaction['amount'] -= $nonmember_discount_amount;
                $transaction['discount'] += $member_discount_amount;
                $transaction['discount'] += $nonmember_discount_amount;
            }
            $remaining_delegates = DB::select(
                array('contacts.first_name', 'firstName'),
                array('contacts.last_name', 'lastName'),
                'cancel_reason_code'
            )
                ->from(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                ->on('has_delegates.contact_id', '=', 'contacts.id')
                ->where('has_delegates.booking_id', '=', $booking_id)
                ->and_where('has_delegates.deleted', '=', 0)
                ->and_where('has_delegates.cancelled', '=', 0)
                ->execute()
                ->as_array();
            if ($transaction['credit'] == 0) {
                $delegates = DB::select(
                    array('contacts.first_name', 'firstName'),
                    array('contacts.last_name', 'lastName'),
                    'cancel_reason_code'
                )
                    ->distinct('*')
                    ->from(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'))
                        ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                            ->on('has_delegates.contact_id', '=', 'contacts.id')
                    ->where('has_delegates.booking_id', '=', $booking_id)
                    ->and_where('has_delegates.cancel_transaction_id', '=', $transaction_id)
                    ->and_where('has_delegates.deleted', '=', 0)
                    ->execute()
                    ->as_array();
            } else {
                $delegates = DB::select(
                    array('contacts.first_name', 'firstName'),
                    array('contacts.last_name', 'lastName'),
                    'cancel_reason_code'
                )
                    ->distinct('*')
                    ->from(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'))
                       ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                            ->on('has_delegates.contact_id', '=', 'contacts.id')
                    ->where('has_delegates.booking_id', '=', $booking_id)
                    ->and_where('has_delegates.deleted', '=', 0)
                    ->and_where('has_delegates.cancelled', '=', 0)
                    ->execute()
                    ->as_array();
            }
            if (count($delegates) == 0){
                $delegates = DB::select(
                    array('contacts.first_name', 'firstName'),
                    array('contacts.last_name', 'lastName'),
                    'cancel_reason_code'
                )
                    ->distinct('*')
                    ->from(array(Model_KES_Bookings::DELEGATES_TABLE, 'has_delegates'))
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('has_delegates.contact_id', '=', 'contacts.id')
                    ->where('has_delegates.booking_id', '=', $booking_id)
                    ->and_where('has_delegates.deleted', '=', 0)
                    ->execute()
                    ->as_array();
            }
            $tx_type = 'Invoice';
            $reason = '';
            if ($booking['invoice_details'] == '' && $booking['payment_method'] == 'invoice') {
                return null;
            }
            $purchase_order_no = '' . $booking['invoice_details'];
            if ($booking['payment_method'] == 'cc') {
                $tx_type = 'Invoice';
            }
            if ($booking['booking_status'] == 6) {
                $tx_type = 'Quote';
            }
            if ($transaction['credit'] != 1) {
                $tx_type = 'Credit';
                $reason = $delegates[0]['cancel_reason_code'];
            }
            $delegate_count = count($delegates);
            if ($delegate_count == 0) { // should not happen
                return false;
            }
            $remaining_delegate_count = count($remaining_delegates);
            if ($remaining_delegate_count > 0) {
                $member_unit_discount = round((float)$member_discount_amount / $remaining_delegate_count, 2);
                $unit_discount = round((float)$nonmember_discount_amount / $remaining_delegate_count, 2);
            } else {
                $member_unit_discount = round((float)$member_discount_amount / $delegate_count, 2);
                $unit_discount = round((float)$nonmember_discount_amount / $delegate_count, 2);
            }
            $request_transaction_data = array(
                'transactionID' => (int)$transaction['id'] ?: null,
                'bookingID' => (int)$booking_id ?: null,
                'transactionType' => $tx_type ?: null,
                'purchaseOrderNumber' => $purchase_order_no,
                'unitAmount' => round((float)$transaction['amount'] / $delegate_count, 2) ?: 0,
                'memberUnitDiscount' => $member_unit_discount,
                'memberUnitDiscountDescription' => urlencode($member_discount),
                'reasonCode' => $reason,
                'quantity' => count($delegates),
                'totalAmount' => (float)$transaction['total'] ?: 0,
                'unitDiscountAmount' => $unit_discount,
                'unitDiscountDescription' => urlencode($nonmember_discount)
            );
            foreach ($delegates as $i => $delegate) {
                $delegates[$i]['firstName'] = $delegates[$i]['firstName'] ?: null;
                $delegates[$i]['lastName'] = $delegates[$i]['lastName'] ?: null;
                unset($delegates[$i]['cancel_reason_code']);
            }
            $request_transaction_data['delegates'] = $delegates;
            if ($return_data_only) {
                return $request_transaction_data;
            }
            $response = $this->request('POST', '/transaction', $request_transaction_data);
            if ($response && @$this->last_info['http_code'] == '201') {
                $rs = new Model_Remotesync();
                $rs->save_object_synced(Model_NAVAPI::API_NAME . '-Transaction', $transaction_id, $transaction_id);

                $payments = DB::select('payments.*', 'transactions.booking_id')
                    ->from(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'))
                        ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                            ->on('payments.transaction_id', '=', 'transactions.id')
                        ->join(array(Model_Kes_Payment::STATUS_TABLE, 'payment_statuses'), 'inner')
                            ->on('payments.status', '=', 'payment_statuses.id')
                    ->where('transactions.id', '=', $transaction_id)
                    ->and_where('payments.deleted', '=', 0)
                    ->and_where('payment_statuses.credit', '=', 1)
                    ->execute()
                    ->as_array();
                foreach ($payments as $payment) {
                    $this->create_payment($payment['id']);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function create_payment($payment_id)
    {
        $rs = new Model_Remotesync();
        $synced = $rs->get_object_synced(Model_NAVAPI::API_NAME . '-Payment', $payment_id);
        if ($synced) {
            return true;
        }

        $payment = DB::select('payments.*', 'transactions.booking_id', 'payment_statuses.credit')
            ->from(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'))
                ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                    ->on('payments.transaction_id', '=', 'transactions.id')
                ->join(array(Model_Kes_Payment::STATUS_TABLE, 'payment_statuses'), 'inner')
                    ->on('payments.status', '=', 'payment_statuses.id')
            ->where('payments.id', '=', $payment_id)
            ->and_where('payment_statuses.credit', '=', 1)
            ->execute()
            ->current();
        if ($payment) {
            $request_payment_data = array(
                'transactionID' => (int)$payment['transaction_id'] ?: null,
                'bookingID' => (int)$payment['booking_id'] ?: null,
                'type' => $payment['type'] ?: null,
                'amount' => $payment['amount'] ?: null,
                'currency' => $payment['currency'],
                'paymentDate' => date(DATE_RFC3339, strtotime($payment['created']))
            );
            $response = $this->request('POST', '/payment', $request_payment_data);
            if ($response && @$this->last_info['http_code'] == '201') {
                $rs = new Model_Remotesync();
                $rs->save_object_synced(Model_NAVAPI::API_NAME . '-Payment', $payment_id, $payment_id);
            }
            return true;
        } else {
            return false;
        }
    }

    public function get_for_datatable($filters = [])
    {
        $columns = [
            'navapi.remote_event_no',
            'navapi.remote_cost_centre',
            'navapi.remote_event_title',
            'navapi.remote_description',
            'navapi.remote_venue',
            'navapi.remote_event_date',
            'navapi.remote_status',
            'navapi.remote_end_date',
            'navapi.schedule_id',
            'navapi.id',
            'navapi.remote_start_date',
        ];

        $q = DB::select(DB::expr('SQL_CALC_FOUND_ROWS navapi.id'))
            ->select_array($columns)
            ->from(array(self::TABLE_EVENTS, 'navapi'));

        for ($i = 0; $i < count($columns); $i++) {
            if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '') {
                $filters['sSearch_'.$i] = preg_replace('/\s+/', '%', $filters['sSearch_'.$i]); //replace spaces with %
                $q->and_where($columns[$i], 'like', '%'.$filters['sSearch_'.$i].'%');
            }
        }

        if (empty($filters['iDisplayLength']) OR $filters['iDisplayLength'] == -1){
            $filters['iDisplayLength'] = 10;
        }
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1){
            $q->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart'])){
                $q->offset(intval($filters['iDisplayStart']));
            }
        }
        if (is_numeric(@$filters['schedule_id'])) {
            $q->order_by(DB::expr("if(navapi.schedule_id=" . $filters['schedule_id'] . ", 1, 0)"), "DESC");
        }
        if (isset($filters['iSortCol_0'])) {
            if (in_array(strtolower($filters['sSortDir_0']), array('asc', 'desc'))) {
                if ($filters['iSortCol_0'] == 7 && $filters['schedule_id'] > 0) {
                    $q->order_by(DB::expr('navapi.schedule_id=' . $filters['schedule_id']), $filters['sSortDir_0']);
                } else {
                    $q->order_by($columns[$filters['iSortCol_0']], $filters['sSortDir_0']);
                }
            }
        }
        $events = $q->execute()->as_array();
        $total = DB::query(Database::SELECT, 'SELECT FOUND_ROWS() AS total')->execute()->get('total'); // total number of results
        $rows = [];
        foreach ($events as $event) {
            $row = [
                htmlspecialchars($event['remote_event_no']),
                htmlspecialchars($event['remote_cost_centre']),
                htmlspecialchars($event['remote_event_title']),
                htmlspecialchars($event['remote_description']),
                htmlspecialchars($event['remote_venue']),
                htmlspecialchars($event['remote_event_date']),
                htmlspecialchars($event['remote_status'])
            ];
            if ($event['schedule_id'] != null && $filters['schedule_id'] != $event['schedule_id']) {
                $row[] = '<a href="/admin/courses/edit_schedule/?id=' . $event['schedule_id'] . '" target="_blank">view</a>';
            } else {
                $checked = false;
                if ($filters['schedule_id'] == $event['schedule_id'] && $filters['schedule_id'] > 0) {
                    $checked = true;
                }
                $row[] = '<input type="radio" name="navision_id" value="' . $event['id'] . '" ' . ($checked ? 'checked="checked"' : '') . ' data-eventdate="' . html::chars($event['remote_event_date']) . '"/>';
            }
            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $total,
            'iTotalRecords' => count($events),
            'sEcho' => intval($filters['sEcho'])
        ];
    }

    public function get_pdf($transaction_id, $store_to_contact = true)
    {
        $transaction = DB::select(
            'transactions.*', 'transaction_types.credit', 'transaction_types.type',
            'bookings.booking_status', 'bookings.payment_method', 'bookings.invoice_details',
            'bookings.contact_id', 'bookings.bill_payer',
            'contacts.gdpr_cleansed_datetime'
        )
            ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                ->join(array(Model_Kes_Transaction::TYPE_TABLE, 'transaction_types'), 'inner')
                    ->on('transactions.type', '=', 'transaction_types.id')
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'inner')
                    ->on('transactions.booking_id', '=', 'bookings.booking_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.id', '=', $transaction_id)
            ->and_where('transactions.deleted', '=', 0)
            ->and_where('contacts.gdpr_cleansed_datetime', 'is', null)
            ->execute()
            ->current();
        if ($transaction) {
            $pdf = $this->request(
                'GET',
                '/transaction/invoice?' .
                    http_build_query(
                        array(
                            'bookingId' => $transaction['booking_id'],
                            'transactionId' => $transaction['id']
                        )
                    ),
                null,
                null,
                true,
                false,
                false,
                false
            );
            if ($this->last_info['http_code'] == 200) {
                if ($store_to_contact) {
                    // store file to contact, payer and linked organizations
                    $contact_ids = array($transaction['contact_id']);
                    if ($transaction['bill_payer'] > 0) {
                        $contact_ids[] = $transaction['bill_payer'];
                    }
                    foreach ($contact_ids as $contact_id) {
                        $org_id = $this->get_id_organization_of_contact($contact_id);
                        if ($org_id) {
                            $contact_ids[] = $org_id;
                        }
                    }
                    //$this->store_to_contact('invoice-' . $transaction['booking_id'] . '_' . $transaction_id . '.pdf', $pdf, $contact_ids);
                    $this->store_to_booking('invoice-' . $transaction['booking_id'] . '_' . $transaction_id . '.pdf', $pdf, $transaction['booking_id'], $transaction_id);
                }
                return $pdf;
            } else {
                return false;
            }
        }
    }

    protected function store_to_contact($attachment_name, $file, $contact_ids)
    {
        $tmpname = tempnam(Kohana::$cache_dir, 'booking_transaction_');
        file_put_contents($tmpname, $file);
        foreach ($contact_ids as $contact_id) {
            $contact_dir_id = Model_Files::get_directory_id_r('/contacts/' . $contact_id);
            $file_id = Model_Files::create_file(
                $contact_dir_id,
                $attachment_name,
                array(
                    'tmp_name' => $tmpname,
                    'name' => $attachment_name,
                    'size' => filesize($tmpname),
                    'type' => 'application/pdf'
                )
            );
            $cf = new Model_Contacts3_Files();
            $cf->save_data(array('contact_id' => $contact_id, 'document_id' => $file_id));
        }
    }

    protected function store_to_booking($attachment_name, $file, $booking_id, $transaction_id) {
        $tmpname = tempnam(Kohana::$cache_dir, 'booking_transaction_');
        file_put_contents($tmpname, $file);
        $booking_dir_id = Model_Files::get_directory_id_r('/bookings/' . $booking_id);
        $file_id = Model_Files::create_file(
            $booking_dir_id,
            $attachment_name,
                array(
                    'tmp_name' => $tmpname,
                    'name' => $attachment_name,
                    'size' => filesize($tmpname),
                    'type' => 'application/pdf'
                )
            );
            $cf = new Model_Booking_Files();
            $cf->save_data(array(
                'booking_id' => $booking_id,
                'transaction_id' => $transaction_id,
                'document_id' => $file_id));

    }

    protected function get_id_organization_of_contact($contact_id)
    {
        $organization_type = Model_Contacts3::find_type('organisation');
        $related_contact_ids = Model_Contacts3::get_parent_related_contacts($contact_id);
        foreach ($related_contact_ids as $related_contact_id) {
            $org_contact = new Model_Contacts3($related_contact_id);
            if ($org_contact->get_type() == $organization_type['contact_type_id']) {
                return $org_contact->get_id();
            }
        }
        return false;
    }
}