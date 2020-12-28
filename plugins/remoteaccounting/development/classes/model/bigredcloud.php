<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Bigredcloud extends Model implements Model_Remoteaccountingapi
{
    const API_NAME = 'Bigredcloud';
    protected $base_url = 'https://app.bigredcloud.com/api/v1/';
    public $key = '';
    protected $curl = null;

    public function __construct()
    {
        $settings = Settings::instance();
        $this->key = trim($settings->get('bigredcloud_key'));
    }

    public function __destruct()
    {

    }

    protected function request($type, $uri, $params = null)
    {
        $key_64 = base64_encode($this->key);
        $auth_head = "Basic " . $key_64;
        $url = $this->base_url . $uri;

        $this->follow_location = null;
        $this->last_id = null;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_VERBOSE, true);
//echo $auth_head;exit;
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Authorization: ' . $auth_head));
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->key);
        if ($type != 'GET') {
            //curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $type);

            if ($params) {
                $json = json_encode($params);
                /*$json = '{
    "ownerTypeId": 1,
    "code": "' . uniqid() . '",
    "contact": "John Soa",
    "email": "test1@email.com",
    "mobile": "123456789DFD",
    "name": "Customer 1",

}';*/
                curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'Content-Length: ' . strlen($json)));
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);
                //curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
            }
        } else {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
            //curl_setopt($this->curl, CURLOPT_POST, false);
            //curl_setopt($this->curl, CURLOPT_POSTFIELDS, null);
        }
        //curl_setopt($this->curl, CURLOPT_VERBOSE, 1);
        if ($type != 'GET') {
            curl_setopt($this->curl, CURLOPT_HEADER, 1);
        } else {
            curl_setopt($this->curl, CURLOPT_HEADER, 0);
        }
        //curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $response = curl_exec($this->curl);
        $this->last_request = $params;
        if ($type != 'GET') {
            if (preg_match('#Location: ((.+)/(\d+))#m', $response, $location)) {
                $this->follow_location = $location[1];
                $this->last_id = $location[3];
            }
        }
        $this->last_response = $response;
        $this->last_info = curl_getinfo($this->curl);
        curl_close($this->curl);

        return json_decode($response, true);
    }

    public static function get_account_options($selected = '')
    {
        $accounts = array(
            '' => ''
        );

        $brc = new Model_Bigredcloud();
        if ($brc->key) {
            try {
                foreach ($brc->get_accounts() as $account) {
                    $accounts[$account['code']] = $account['code'] . ' - ' . $account['description'] . ' (' . $account['accountType'] . ' )';
                }
            } catch (Exception $exc) {

            }
        }

        return html::optionsFromArray($accounts, $selected);
    }

    public static function get_bank_account_options($selected = '')
    {
        $accounts = array(
            '' => ''
        );

        $brc = new Model_Bigredcloud();
        if ($brc->key) {
            try {
                foreach ($brc->get_bankaccounts() as $account) {
                    $accounts[$account['id']] = $account['acCode'] . ' - ' . $account['details'];
                }
            } catch (Exception $exc) {

            }
        }

        return html::optionsFromArray($accounts, $selected);
    }

    public function get_accounts()
    {
        $accounts = array();

        $response = $this->request('GET', 'accounts');
        $accounts = $response['Items'];
        return $accounts;
    }

    public function get_bankaccounts()
    {
        $accounts = array();

        $response = $this->request('GET', 'bankAccounts');
        $accounts = $response['Items'];
        return $accounts;
    }

    public function get_contacts()
    {
        $contacts = array();
        $rcustomers = $this->request('GET', 'customers');
        $contacts = $rcustomers['Items'];

        return $contacts;
    }

    public function sync_contacts($contacts_to_sync = null)
    {
        try {
            Database::instance()->begin();

            $synced_contacts = DB::select('*')
                ->from(Model_Remoteaccounting::TABLE_RCONTACTS)
                ->where('remote_api', '=', Model_Bigredcloud::API_NAME)
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_contacts as $synced_contact) {
                $local_id_map[$synced_contact['local_contact_id']] = $synced_contact['remote_contact_id'];
                $remote_id_map[$synced_contact['remote_contact_id']] = $synced_contact['local_contact_id'];
            }

            $lcontacts = Model_Contacts3::get_all_contacts();
            $rcontacts = $this->get_contacts();

            foreach ($lcontacts as $lcontact) {
                if ($contacts_to_sync === null || in_array($lcontact['id'], $contacts_to_sync)) {
                    if (@$local_id_map[$lcontact['id']] == null) {
                        $this->save_contact($lcontact);
                        echo $lcontact['first_name'] . " " . $lcontact["last_name"] . " to remote\n";
                    }
                }
            }

            foreach ($rcontacts as $rcontact) {
                if (@$remote_id_map[$rcontact['id']] == null) {
                    $f = new Model_Family();
                    $f->set_family_name($rcontact['name']);
                    $f->save();
                    $c3 = new Model_Contacts3();
                    $c3->set_family_id($f->get_id());
                    $c3->set_is_primary(1);
                    $c3->set_first_name($rcontact['name']);
                    $c3->set_last_name('');
                    $c3->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                    $c3->set_subtype_id(1);
                    $c3->save();
                    if ($rcontact['email'] != '') {
                        $c3->insert_notification(array(
                            'value' => $rcontact['email'],
                            'notification_id' => 1
                        ));
                    }
                    if ($rcontact['mobile'] != '') {
                        $c3->insert_notification(array(
                            'value' => $rcontact['mobile'],
                            'notification_id' => 2
                        ));
                    }
                    DB::insert(Model_Remoteaccounting::TABLE_RCONTACTS)
                        ->values(
                            array(
                                'local_contact_id' => $c3->get_id(),
                                'remote_contact_id' => $rcontact['id'],
                                'remote_api' => Model_Bigredcloud::API_NAME
                            )
                        )->execute();
                    $local_id_map[$c3->get_id()] = $rcontact['id'];
                    $remote_id_map[$rcontact['id']] = $c3->get_id();

                    echo $rcontact['contact'] . " to local\n";
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function save_contact($contact)
    {
        $result = null;
        if ($contact) {
            $params = array('ownerTypeId' => 1);
            /*if (isset($contact['code'])) {
                $params['code'] = $contact['code'];
            }*/
            if (isset($contact['email'])) {
                $params['email'] = $contact['email'];
            }
            if (isset($contact['mobile'])) {
                $params['mobile'] = $contact['mobile'];
            }
            if (isset($contact['first_name'])) {
                $params['name'] = trim($contact['first_name'] . ' ' . $contact['last_name']);
            }
            if (isset($contact['name'])) {
                $params['name'] = $contact['name'];
            }
            if (isset($params['name'])) {
                $params['contact'] = $params['name'];
            }
            if (isset($contact['timestamp'])) {
                $params['timestamp'] = $contact['timestamp'];
            }

            if (is_numeric(@$contact['id'])) {
                $params['code'] = $contact['id'];
            }
            $this->request(
                is_numeric(@$contact['remote_contact_id']) ? 'PUT' : 'POST',
                'customers' . (is_numeric(@$contact['remote_contact_id']) ? '/' . $contact['remote_contact_id'] : ''),
                $params
            );
            $remote_id = null;
            if ($this->last_id) {
                $remote_id = $this->last_id;
            }
            /*
            $rcustomers = $this->request('GET', 'customers?orderby=id desc');
            foreach ($rcustomers['Items'] as $customer) {
                if ($customer['code'] == $params['code']) {
                    $remote_id = $customer['id'];
                    break;
                }
            }*/

            if ($remote_id) {
                DB::insert(Model_Remoteaccounting::TABLE_RCONTACTS)
                    ->values(
                        array(
                            'local_contact_id' => $contact['id'],
                            'remote_contact_id' => $remote_id,
                            'remote_api' => Model_Bigredcloud::API_NAME
                        )
                    )->execute();
            }
            return $remote_id;
        } else {
            return false;
        }
    }

    public function delete_contact($contact)
    {

    }

    public function get_transactions()
    {
        $transactions = array();
        $rtransactions = $this->request('GET', 'salesEntries?' . http_build_query(array('$orderby' => 'id desc')));
        $transactions = $rtransactions['Items'];

        return $transactions;
    }

    public function get_transaction($transaction_id)
    {
        $transaction = $this->request('GET', 'salesEntries/' . $transaction_id);
        return $transaction;
    }

    public function save_transaction($transaction)
    {
        /*
        $params = array();
        $params['customerId'] = 1741234;
        $params['details'] = $transaction['details'];
        $params['entryDate'] = $transaction['created'];
        $params['total'] = $transaction['total'];
        $params['totalVAT'] = 0;
        $params['vatTypeId'] = 1;
        $params['procDate'] = $transaction['created'];
        $params['productTrans'] = array(
        "acEntries" => [
        [
            "accountCode" => "000",
            "analysisCategoryId" => 1,
            "value" => $transaction['total']
        ]
      ],
      "productId"=> null,
      "quantity"=> 1,
      "unitPrice"=> 10,
      "vatRateId"=> 86010,
      "amountNet"=> 10
    );

        $params['loType'] = 1;
        $params['note'] = '';
        */

        $rcontact_id = DB::select('remote_contact_id')
            ->from(Model_Remoteaccounting::TABLE_RCONTACTS)
            ->where('local_contact_id', '=', $transaction['contact_id'])
            ->and_where('remote_api', '=', Model_Bigredcloud::API_NAME)
            ->execute()
            ->get('remote_contact_id');

        $params = array();
        $params['acEntries'] = [
            ['accountCode' => Settings::instance()->get('bigredcloud_account_invoice'), 'analysisCategoryId' => 319228, 'value' => $transaction['total']]
        ];
        $params['customFields'] = [];
        $params['customerId'] = $rcontact_id;
        $params['details'] = $transaction['details'];
        $params['entryDate'] = $transaction['created'];
        $params['note'] = @$transaction['note'] ?: '';
        $params['procDate'] = $transaction['created'];
        $params['reference'] = null;
        $params['total'] = $transaction['total'];
        $params['totalVAT'] = 0;
        $params['vatEntries'] = [['amount' => $transaction['total'], 'vatRateId' => '86010', 'vatTypeId' => 1]];
        $params['netGoods'] = 0;
        $params['netServices'] = 0;
        $params['vatTypeId'] = 1;
        $params['vatRateId'] = '86010';

        //print_r($params);exit;
        $this->request(
            'POST',
            'salesEntries',
            $params
        );
        $remote_id = null;
        if ($this->last_id) {
            $remote_id = $this->last_id;
        }

        if ($remote_id) {
            DB::insert(Model_Remoteaccounting::TABLE_RTRANSACTIONS)
                ->values(
                    array(
                        'local_transaction_id' => $transaction['id'],
                        'local_transaction_table' => $transaction['table'],
                        'remote_transaction_id' => $remote_id,
                        'remote_api' => Model_Bigredcloud::API_NAME
                    )
                )->execute();
            return $remote_id;
        }
        return null;
    }

    public function sync_transactions()
    {
        try {
            Database::instance()->begin();

            $synced_transactions = DB::select('*')
                ->from(Model_Remoteaccounting::TABLE_RTRANSACTIONS)
                ->where('remote_api', '=', Model_Bigredcloud::API_NAME)
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_transactions as $synced_transaction) {
                $ltransaction_id = $synced_transaction['local_transaction_table'] . $synced_transaction['local_transaction_id'];
                $local_id_map[$ltransaction_id] = $synced_transaction['remote_transaction_id'];
                $remote_id_map[$synced_transaction['remote_transaction_id']] = $ltransaction_id;
            }

            $ltransactions = array();
            if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
                $btransactions = Model_Kes_Transaction::search(array('type' => array(1, 2)));

                $contacts_to_sync = array();
                foreach ($btransactions as $btransaction) {
                    $contacts_to_sync[] = $btransaction['contact_id'];
                }
                $this->sync_contacts($contacts_to_sync);


                foreach ($btransactions as $btransaction) {
                    $ltransactions[] = array(
                        'table' => 'plugin_bookings_transactions',
                        'id' => $btransaction['id'],
                        'contact_id' => $btransaction['contact_id'],
                        'created' => $btransaction['created'],
                        'due_date' => $btransaction['payment_due_date'],
                        'items' => array(
                            array('amount' => $btransaction['total'], 'description' => $btransaction['schedules'] . $btransaction['courses'])
                        ),
                        'total' => $btransaction['total'],
                        'details' => $btransaction['schedules'] . $btransaction['courses']
                    );
                }
            }
            $rtransactions = $this->get_transactions();
//print_r($ltransactions);exit;
            foreach ($ltransactions as $ltransaction) {
                if (@$local_id_map[$ltransaction['table'] . $ltransaction['id']] == null) {
                    $this->save_transaction($ltransaction);
                    echo $ltransaction['id'] . " to remote\n";
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function get_payments()
    {
        $payments = array();
        if ($this->curl != null) {
            $rpayments = $this->request('GET', 'payments');
            $payments = $rpayments['Items'];
        }

        return $payments;
    }

    public function save_payment($payment)
    {
        $rtransaction_id = DB::select('remote_transaction_id')
            ->from(Model_Remoteaccounting::TABLE_RTRANSACTIONS)
            ->where('local_transaction_id', '=', $payment['transaction_id'])
            ->and_where('local_transaction_table', '=', $payment['transaction_table'])
            ->and_where('remote_api', '=', Model_Bigredcloud::API_NAME)
            ->execute()
            ->get('remote_transaction_id');
        if (!$rtransaction_id) {
            return null;
        }
        $rtransaction = $this->get_transaction($rtransaction_id);


        $params = array();
        $params['acEntries'] = array(
            array(
                'accountCode' => Settings::instance()->get('bigredcloud_account_invoice'),
                'analysisCategoryId' => 319230,
                'value' => $payment['amount']
            )
        );
        $params['bankAccountId'] = 31967;
        $params['customFields'] = array();
        $params['detailCollection'] = array();
        $params['entryDate'] = str_replace(' ', 'T', date::now());
        $params['note'] = @$payment['note'];
        $params['procDate'] = str_replace(' ', 'T', date::now());
        $params['reference'] = $rtransaction['reference'];
        $params['total'] = $payment['amount'];

        $rpayment = $this->request('POST', 'payments', $params);

        if ($this->last_id) {
            DB::insert(Model_Remoteaccounting::TABLE_RPAYMENTS)
                ->values(
                    array(
                        'local_payment_id' => $payment['id'],
                        'local_payment_table' => $payment['table'],
                        'remote_payment_id' => $this->last_id,
                        'remote_api' => Model_Bigredcloud::API_NAME
                    )
                )->execute();
        }
        return $this->last_id;
    }

    public function sync_payments()
    {
        $this->sync_transactions();
        try {
            Database::instance()->begin();

            $synced_transactions = DB::select('*')
                ->from(Model_Remoteaccounting::TABLE_RTRANSACTIONS)
                ->where('remote_api', '=', Model_Bigredcloud::API_NAME)
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_transactions as $synced_transaction) {
                $ltransaction_id = $synced_transaction['local_transaction_table'] . $synced_transaction['local_transaction_id'];
                $local_id_map[$ltransaction_id] = $synced_transaction['remote_transaction_id'];
                $remote_id_map[$synced_transaction['remote_transaction_id']] = $ltransaction_id;
            }

            $synced_payments = DB::select('*')
                ->from(Model_Remoteaccounting::TABLE_RPAYMENTS)
                ->where('remote_api', '=', Model_Bigredcloud::API_NAME)
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_payments as $synced_payment) {
                $lpayment_id = $synced_payment['local_payment_table'] . $synced_payment['local_payment_id'];
                $local_id_map[$lpayment_id] = $synced_payment['remote_payment_id'];
                $remote_id_map[$synced_payment['remote_payment_id']] = $lpayment_id;
            }

            $lpayments = array();
            if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
                $bpayments = Model_Kes_Payment::search();

                foreach ($bpayments as $bpayment) {
                    $lpayments[] = array(
                        'table' => 'plugin_bookings_transactions_payments',
                        'id' => $bpayment['id'],
                        'contact_id' => $bpayment['contact_id'],
                        'transaction_id' => $bpayment['transaction_id'],
                        'transaction_table' => 'plugin_bookings_transactions',
                        'amount' => $bpayment['amount'],
                        'refund' => $bpayment['status'] == 8,
                        'description' => $bpayment['schedules'] . $bpayment['courses']
                    );
                }
            }
            $rtransactions = $this->get_payments();

            foreach ($lpayments as $lpayment) {
                if (@$local_id_map[$lpayment['table'] . $lpayment['id']] == null) {
                    $this->save_payment($lpayment);
                    echo $lpayment['id'] . " to remote\n";
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function vat_rates()
    {
        $response = $this->request('GET', 'vatRates');
        $vat_rates = $response['Items'];
        return $vat_rates;
    }

    public function vat_types()
    {
        $response = $this->request('GET', 'vatTypes');
        $vat_types = $response['Items'];
        return $vat_types;
    }

    public function vat_categories()
    {
        $response = $this->request('GET', 'vatCategories');
        $vat_categories = $response['Items'];
        return $vat_categories;
    }

    public function analisys_categories()
    {
        $response = $this->request('GET', 'analysisCategories');
        $ac = $response['Items'];
        return $ac;
    }
}

