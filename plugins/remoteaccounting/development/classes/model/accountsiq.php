<?php defined('SYSPATH') or die('No Direct Script Access.');

class Model_Accountsiq extends Model // implements Model_Remoteaccountingapi
{
    const API_NAME = 'AccountsIQ';

    protected $wsdlUrl = "https://hostacct.com/system/dashboard/integration/integration_1_1.asmx?wsdl";
    public $companyid = '';
    public $partnerkey = '';
    public $userkey = '';
    public $soapClient = null;
    public $departmentid = null;
    public $prefix = 'CC';

    protected $token = null;
    protected static $shared_token = null;
    protected static $shared_soapClient = null;


    public $debug = true;
    public $queries = array();

    public function __construct($shared_login = true)
    {
        $settings = Settings::instance();
        $this->companyid = $settings->get('accountsiq_companyiq');
        $this->partnerkey = $settings->get('accountsiq_partnerkey');
        $this->userkey = $settings->get('accountsiq_userkey');
        $this->departmentid = $settings->get('accountsiq_invoice_departmentid');
        $this->prefix = $settings->get('accountsiq_prefix');

        ini_set('soap.wsdl_cache_enabled', 0);

        $opts = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT
            )
        );

        $params = array(
            'soap_version' => SOAP_1_1,
            'exceptions' => true,
            'trace' => 1,
            //'cache_wsdl' => WSDL_CACHE_DISK,
            'cache_wsdl' => WSDL_CACHE_NONE,
            //'stream_context' => stream_context_create($opts)
        );

        if ($shared_login) {
            if (self::$shared_token) {
                $this->token = self::$shared_token;
                $this->soapClient = self::$shared_soapClient;
                return;
            }
        }
        $this->soapClient = new SoapClient($this->wsdlUrl, $params);
        $this->login();
        if ($shared_login) {
            self::$shared_token = $this->token;
            self::$shared_soapClient = $this->soapClient;
        }
    }

    public function __destruct()
    {

    }

    public function prep_id($id, $prefix)
    {
        $new_id = $prefix . str_pad($id, 6, "0", STR_PAD_LEFT);
        return $new_id;
    }

    public function get_last_request()
    {
        $x = $this->soapClient->__getLastRequest();
        $s = new SimpleXMLElement($x);
        //echo $x;
        $dom = dom_import_simplexml($s)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML() . "\n\n";
    }

    public function get_last_response()
    {
        $x = $this->soapClient->__getLastResponse();
        $s = new SimpleXMLElement($x);
        //echo $x;
        $dom = dom_import_simplexml($s)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML() . "\n\n\n\n";
    }

    public function login()
    {
        if ($this->token != null) {
            return true;
        }

        if ($this->companyid != '' && $this->partnerkey != '' && $this->userkey != '') {
            $vars = array(
                'companyID' => $this->companyid,
                'partnerKey' => $this->partnerkey,
                'userKey' => $this->userkey,
            );

            $t1 = microtime(true);
            $loginResult = $this->soapClient->Login($vars);
            $t2 = microtime(true);
            Model_ExternalRequests::create($this->wsdlUrl, 'Login', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
            if ($this->debug) {
                $this->queries[] = array(
                    'request' => $this->get_last_request(),
                    'response' => $this->get_last_response()
                );
            }
            if (isset($loginResult->LoginResult)) {
                $this->token = $loginResult->LoginResult;
                return true;
            } else {
                return false;
            }
        }
    }

    public function getGLAccountList()
    {
        $params = new stdClass();
        $params->token = $this->token;
        $t1 = microtime(true);
        $cl = $this->soapClient->GetGLAccountList($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetGLAccountList', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        return $cl;
    }

    public static function get_glaccount_list()
    {
        $aiq = new Model_Accountsiq();
        $accounts = $aiq->getGLAccountList();
        print_r($accounts);
    }

    protected function getActiveCustomerList()
    {
        $params = new stdClass();
        $params->token = $this->token;
        $t1 = microtime(true);
        $cl = $this->soapClient->GetActiveCustomerList($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetActiveCustomerList', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        return $cl;
    }

    public function get_contacts()
    {
        $cl = $this->getActiveCustomerList();


        $contacts = array();
        if (isset($cl->GetActiveCustomerListResult->Result->Customer)) {
            foreach ($cl->GetActiveCustomerListResult->Result->Customer as $customer) {
                $contact = array();
                $contact['name'] = $customer->Name;
                $contact['email'] = $customer->Email;
                $contact['remote_id'] = $customer->Code;
                $contact['address1'] = $customer->Address1;
                $contact['address2'] = $customer->Address2;
                $contact['city'] = $customer->City;
                $contact['postcode'] = $customer->PostCode;
                $contact['country'] = $customer->Country;
                $contact['mobile'] = $customer->MobilePhone;
                $contact['phone'] = $customer->Phone;
                $contact['fax'] = $customer->Fax;
                $contacts[] = $contact;
            }
        }
        return $contacts;
    }

    public function get_contact($code)
    {
        $params = new stdClass();
        $params->token = $this->token;
        $params->customerCode = $code;

        $t1 = microtime(true);
        $response = $this->soapClient->GetCustomer($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetCustomer', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (isset($response->GetCustomerResult->Status) && $response->GetCustomerResult->Status == 'Success') {
            $contact = $response->GetCustomerResult->Result;
        }
        return $contact;
    }


    public function is_duplicate_contact($firstname, $lastname, $email)
    {
        $name = preg_replace('/\s+/', ' ', strtolower(trim($firstname . ' ' . $lastname)));
        $rcontacts = $this->get_contacts();
        $rnames = array();
        $remails = array();
        foreach ($rcontacts as $rcontact) {
            $rname = preg_replace('/\s+/', ' ', strtolower(trim($rcontact->Contact != '' ? $rcontact->Contact : $rcontact->Name)));
            //$rnames[$rname] = $rcontact->Email != '' ? $rcontact->Email : $rname;
            if (!isset($rnames[$rname])) {
                $rnames[$rname] = array();
            }
            $rnames[$rname][] = &$rcontact;
            if ($rcontact->Email != '') {
                $remails[$rcontact->Email] = &$rcontact;
            }
        }


        if ($email != '' && isset($remails[$email])) {
            return $remails[$email];
        }
        if (isset($rnames[$name])) {
            foreach ($rnames[$name] as $rcontact) {
                if ($email == '' || $rcontact->Email == '' || $email == $rcontact->Email) {
                    return $rcontact;
                }
            }
        }
        return false;
    }

    public function save_contact($contact, $remote_id = null)
    {
        $params = new stdClass();
        $params->token = $this->token;
        $t1 = microtime(true);
        if ($remote_id == null) {
            $rcustomer = $this->soapClient->GetNewCustomerFromDefaults($params);
            $t2 = microtime(true);
            Model_ExternalRequests::create($this->wsdlUrl, 'GetNewCustomerFromDefaults', $this->get_last_request(),
                $this->get_last_response(), '', date::now(), null, $t2 - $t1);
            if ($this->debug) {
                $this->queries[] = array(
                    'request' => $this->get_last_request(),
                    'response' => $this->get_last_response()
                );
            }
            $params->customer = $rcustomer->GetNewCustomerFromDefaultsResult->Result;
        } else {
            $params->customer = $this->get_contact($remote_id);
        }
        $params->customer->Code = $this->prep_id($contact['id'], $this->prefix);
        $ecustomer = $this->get_contact($params->customer->Code);
        if ($ecustomer) {
            $params->customer = $ecustomer;
            $params->customer->IsActive = true;
            $params->create = false;
        } else {
            $params->create = true;
        }
        if (@$contact['contact_name']) {
            $params->customer->Contact = $contact['contact_name'];
        }
        if (@$contact['billing_residence_id']) {
            $address = new Model_Residence($contact['billing_residence_id']);
            $params->customer->Address1 = $address->get_address1();
            $params->customer->Address2 = $address->get_address2();
            $params->customer->City = $address->get_town();
            $params->customer->County_State = $address->get_county_name();
            $params->customer->Country = Model_Country::get_countries()[$address->get_country()]['name'];
            $params->customer->PostCode = $address->get_postcode();
        } else {
            if (@$contact['residence']) {
                $address = new Model_Residence($contact['residence']);
                $params->customer->Address1 = $address->get_address1();
                $params->customer->Address2 = $address->get_address2();
                $params->customer->City = $address->get_town();
                $params->customer->County_State = $address->get_county_name();
                $params->customer->Country = Model_Country::get_countries()[$address->get_country()]['name'];
                $params->customer->PostCode = $address->get_postcode();
            }
        }
        $params->customer->Phone = '' . $contact['phone'] ? $contact['phone'] : $contact['mobile'];

        $params->customer->Name = trim($contact['first_name'] . ' ' . $contact['last_name']);
        $params->customer->Email = $contact['email'];
        $params->customer->MobilePhone = $contact['mobile'];
        $params->customer->Phone = $contact['phone'];
        $t1 = microtime(true);
        
        $result = $this->soapClient->UpdateCustomer($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'UpdateCustomer', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        
        if (@$result->UpdateCustomerResult->ErrorCode == 'SUCCESS') {
            $rcontact = $this->get_contact($params->customer->Code);
            if ($rcontact) {
                $rs = new Model_Remotesync();
                $rs->save_object_synced(Model_Accountsiq::API_NAME . '-Contact', $rcontact->Code, $contact['id']);

                $organization = Model_Organisation::get_organization_by_primary_biller_id($contact['id']);
                if ($organization) {
                    $org_contact = new Model_Contacts3($organization['contact_id']);
                    $organization = $org_contact->get_instance();
                    $organization['contact_name'] = $contact['first_name'] . ' ' . $contact['last_name'];
                    $organization['email'] = $org_contact->get_email();
                    $organization['phone'] = $org_contact->get_phone();
                    $organization['mobile'] = $org_contact->get_mobile();
                    $this->save_contact($organization);
                } else {
                    $contact3 = new Model_Contacts3($contact['id']);
                    $organization_type = Model_Contacts3::find_type('organisation');
                    $related_contact_ids = Model_Contacts3::get_parent_related_contacts($contact3->get_id());
                    foreach ($related_contact_ids as $related_contact_id) {
                        $org_contact = new Model_Contacts3($related_contact_id);
                        $org = Model_Organisation::get_org_by_contact_id($related_contact_id);
                        if ($org->get_primary_biller_id()) {
                            $primary_biller = $org->get_primary_biller();
                            $primary_biller_data = $primary_biller->get_instance();
                            $primary_biller_data['email'] = $primary_biller->get_email();
                            $primary_biller_data['phone'] = $primary_biller->get_phone();
                            $primary_biller_data['mobile'] = $primary_biller->get_mobile();
                            return $this->save_contact($primary_biller_data);
                        }
                        if ($org_contact->get_type() == $organization_type['contact_type_id']) {
                            $organization = $org_contact->get_instance();
                            $organization['contact_name'] = $contact['first_name'] . ' ' . $contact['last_name'];
                            $organization['email'] = $org_contact->get_email();
                            $organization['phone'] = $org_contact->get_phone();
                            $organization['mobile'] = $org_contact->get_mobile();
                            $this->save_contact($organization);
                        }
                    }
                }

                return $rcontact;
            }
            return false;
        }

        return false;
    }

    public function delete_contact($contact)
    {
        $rs = new Model_Remotesync();
        $sync = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $contact['contact_id']);
        if ($sync) {
            $rcustomer = $this->get_contact($sync['remote_id']);
            $this->inactivate_contact($rcustomer);
        }
    }

    public function inactivate_contact($rcustomer)
    {
        $params = new stdClass();
        $params->token = $this->token;
        $params->customer = $rcustomer;
        $params->customer->IsActive = false;
        $params->create = false;
        $t1 = microtime(true);
        $result = $this->soapClient->UpdateCustomer($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'UpdateCustomer', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (@$result->UpdateCustomerResult->ErrorCode == 'SUCCESS') {
            $response = $this->get_contact($params->customer->Code);
            return true;
        }

        return false;
    }

    public function sync_contacts($to = 'BOTH', $contacts_to_sync = null)
    {
        try {
            Database::instance()->begin();


            $synced_contacts = DB::select('*')
                ->from(Model_Remotesync::SYNC_TABLE)
                ->where('type', '=', Model_Accountsiq::API_NAME . '-Contact')
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_contacts as $synced_contact) {
                $local_id_map[$synced_contact['cms_id']] = $synced_contact['remote_id'];
                $remote_id_map[$synced_contact['remote_id']] = $synced_contact['cms_id'];
            }

            $rs = new Model_Remotesync();

            $lcontacts = Model_Contacts3::get_all_contacts();
            $rcontacts = $this->get_contacts();

            if ($to == 'BOTH' || $to == 'REMOTE') {
                foreach ($lcontacts as $lcontact) {
                    if ($contacts_to_sync === null || in_array($lcontact['id'], $contacts_to_sync)) {
                        if (@$local_id_map[$lcontact['id']] == null) {
                            $rcontact = $this->save_contact($lcontact);
                            $local_id_map[$lcontact['id']] = $rcontact->Code;
                            $remote_id_map[$rcontact->Code] = $lcontact['id'];
                        }
                    }
                }
            }

            if ($to == 'BOTH' || $to == 'LOCAL') {
                foreach ($rcontacts as $rcontact) {
                    if (@$remote_id_map[$rcontact['remote_id']] == null && ($contacts_to_sync === null || in_array($rcontact['remote_id'], $contacts_to_sync))) {
                        $f = new Model_Family();
                        $f->set_family_name($rcontact['name']);
                        if (Settings::instance()->get('contacts_create_family') == 1) {
                            $f->save();
                        }
                        $c3 = new Model_Contacts3();
                        $c3->trigger_save = false;
                        $c3->set_family_id($f->get_id());
                        $c3->set_is_primary(1);
                        $c3->set_first_name($rcontact['name']);
                        $c3->set_last_name('');
                        $c3->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                        $c3->set_subtype_id(1);
                        $residence = array();
                        if ($rcontact['address1'] != '') {
                            $residence['address1'] = $rcontact['address1'];
                        }
                        if ($rcontact['address2'] != '') {
                            $residence['address2'] = $rcontact['address2'];
                        }
                        if ($rcontact['city'] != '') {
                            $residence['city'] = $rcontact['city'];
                        }
                        if ($rcontact['postcode'] != '') {
                            $residence['postcode'] = $rcontact['postcode'];
                        }
                        if ($rcontact['country'] != '') {
                            $residence['country'] = $rcontact['country'];
                        }
                        $r = new Model_Residence();
                        $r->load($residence);
                        $r->save();
                        $c3->set_residence($r);
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
                        if ($rcontact['phone'] != '') {
                            $c3->insert_notification(array(
                                'value' => $rcontact['phone'],
                                'notification_id' => 3
                            ));
                        }
                        if ($rcontact['fax'] != '') {
                            $c3->insert_notification(array(
                                'value' => $rcontact['fax'],
                                'notification_id' => 3,
                                'description' => 'Fax'
                            ));
                        }

                        $rs->save_object_synced(Model_Accountsiq::API_NAME . '-Contact', $rcontact['remote_id'],  $c3->get_id());
                        
                        $local_id_map[$c3->get_id()] = $rcontact->Code;
                        $remote_id_map[$rcontact->Code] = $c3->get_id();

                        //echo $rcontact->Name . " to local\n";
                    }
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function get_transactions()
    {
        $invoiceParams = new stdClass();
        $invoiceParams->token = $this->token;
        $query = new stdClass();
        $query->FromDate = date('c', strtotime("yesterday")); //GetInvoicesBy
        $query->ToDate = date('c', strtotime("tomorrow"));
        $query->FromCreationDate = date('c', strtotime("yesterday"));
        $query->ToCreationDate = date('c', strtotime("tomorrow"));
        $query->Skip = 0;
        $invoiceParams->query = $query;
        //$invoiceParams->customerCode = 'CC100402';
        //header('content-type: text/plain');print_r($invoiceParams);exit;
        $t1 = microtime(true);
        $invoices = $this->soapClient->GetInvoicesBy($invoiceParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetInvoicesBy', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        //$invoices = $this->soapClient->GetInvoicesByCustomerCode($invoiceParams);
        $transactions = array();
        if (isset($invoices->GetInvoicesByResult->Result->Invoice)) {
            $rs = new Model_Remotesync();
            foreach ($invoices->GetInvoicesByResult->Result->Invoice as $invoice) {
                //print_R($invoice);exit;
                /*$contact = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $invoice->AccountID, 'remote_id');
                if (!$contact) {
                    var_dump($contact);
                    self::sync_contacts('LOCAL', array($invoice->AccountID));
                    $contact = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $invoice->AccountID, 'remote_id');
                    print_r($contact);
                    exit;
                }*/
                $transaction = array();
                //$invoice = $this->get_transaction($invoice->InvoiceID);
                $transaction['remote_id'] = $invoice->InvoiceID;
                $transaction['remote_contact_id'] = $invoice->AccountID;
                $transaction['amount'] = $invoice->NetAmount;
                $transaction['total'] = $invoice->GrossAmount;
                $transactions[] = $transaction;
            }
        }
        return $transactions;
    }

    public function get_transaction($invoiceId)
    {
        $invoiceParams = new stdClass();
        $invoiceParams->token = $this->token;
        $invoiceParams->invoiceID = $invoiceId;

        $t1 = microtime(true);
        $invoice = $this->soapClient->GetInvoice($invoiceParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetInvoice', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        return $invoice;
    }

    public function save_schedule($schedule)
    {
        $glaccount = Settings::instance()->get('accountsiq_glaccountcode');

        $item_id = $this->prep_id($schedule['id'], $this->prefix);
        $params = new stdClass();
        $params->token = $this->token;
        $params->stockItemID = $item_id;
        $t1 = microtime(true);
        $existing_item = $this->soapClient->GetStockItem($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetStockItem', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        $item = null;
        if (isset($existing_item->GetStockItemResult->Status) && $existing_item->GetStockItemResult->Status == 'Success') {
            if (isset($existing_item->GetStockItemResult->Result)) {
                $item = $existing_item->GetStockItemResult->Result;
                $item->ItemTypeID = 'S';
                $item->ItemPrice = (float)$schedule['fee_amount'];
                $params = new stdClass();
                $params->token = $this->token;
                $params->stockItem = $item;
                $params->create = false;
                try {
                    $t1 = microtime(true);
                    $response = $this->soapClient->SaveStockItem($params);
                    $t2 = microtime(true);
                    Model_ExternalRequests::create($this->wsdlUrl, 'SaveStockItem', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
                } catch (Exception $exc) {

                }
                if ($this->debug) {
                    $this->queries[] = array(
                        'request' => $this->get_last_request(),
                        'response' => $this->get_last_response()
                    );
                }
            }
        }

        if ($item == null) {
            $t1 = microtime(true);
            $new_item = $this->soapClient->GetNewStockItemFromDefaults($params);
            $t2 = microtime(true);
            Model_ExternalRequests::create($this->wsdlUrl, 'GetNewStockItemFromDefaults', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
            if (isset($new_item->GetNewStockItemFromDefaultsResult->Status) && $new_item->GetNewStockItemFromDefaultsResult->Status == 'Success') {
                $new_item = $new_item->GetNewStockItemFromDefaultsResult->Result;
            }

            if ($new_item) {
                $new_item->ItemPrice = $schedule['fee_amount'];
                $new_item->Description = $schedule['name'];
                $new_item->ItemTypeID = 'S';
                $new_item->OpeningStockGLAccountCode = $glaccount;
                $new_item->ClosingStockGLAccountCode = $glaccount;
                $new_item->DefaultSalesGLAccountCode = $glaccount;
                $new_item->DefaultPurchasesGLAccountCode = $glaccount;

                $params = new stdClass();
                $params->token = $this->token;
                $params->stockItem = $new_item;
                $params->create = true;
                try {
                    $t1 = microtime(true);
                    $response = $this->soapClient->SaveStockItem($params);
                    $t2 = microtime(true);
                    Model_ExternalRequests::create($this->wsdlUrl, 'SaveStockItem', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
                } catch (Exception $exc) {

                }
                if ($this->debug) {
                    $this->queries[] = array(
                        'request' => $this->get_last_request(),
                        'response' => $this->get_last_response()
                    );
                }
                $rs = new Model_Remotesync();
                $rs->save_object_synced(Model_Accountsiq::API_NAME . '-Schedule', $item_id, $schedule['id']);
                return $response;
            }

            return false;
        }
    }

    public function get_items()
    {
        $params = array('token' => $this->token);
        $t1 = microtime(true);
        $items = $this->soapClient->GetStockItemList($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetStockItemList', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        return $items;
    }

    public function get_active_items()
    {
        $params = array('token' => $this->token);
        $t1 = microtime(true);
        $items = $this->soapClient->GetActiveStockItemList($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetActiveStockItemList', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        $result = $items->GetActiveStockItemListResult->Result->StockItem;
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        return $result;
    }

    public function get_active_item($code)
    {
        $params = array('token' => $this->token);
        $t1 = microtime(true);
        $items = $this->soapClient->GetActiveStockItemList($params);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetActiveStockItemList', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        foreach ($items->GetActiveStockItemListResult->Result->StockItem as $si) {
            if ($si->StockItemID == $code) {
                return $si;
            }
        }
        return false;
    }

    public function save_transaction($transaction)
    {
        if (@$transaction['payment_method'] == 'invoice' && @$transaction['invoice_details'] == '') {
            return false;
        }
        $glaccount = Settings::instance()->get('accountsiq_glaccountcode');
        $rs = new Model_Remotesync();
        $already_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Transaction', $transaction['id'], 'cms');
        if ($already_synced) {
            return $this->get_transaction($already_synced['remote_id']);
        }
        $notes = '';
        $booked_by = '';
        $billing_contact = new Model_Contacts3($transaction['contact_id']);
        $organization_type = Model_Contacts3::find_type('organisation');
        $related_contact_ids = Model_Contacts3::get_parent_related_contacts($transaction['contact_id']);
        $org_contact = null;
        foreach ($related_contact_ids as $related_contact_id) {
            $org_contact = new Model_Contacts3($related_contact_id);
            //if booking is created by an org rep then set the organization for booking contact
            if ($org_contact->get_type() == $organization_type['contact_type_id']) {
                $booked_by = 'Booked by ' . trim($billing_contact->get_first_name() . ' ' . $billing_contact->get_last_name());                $billing_contact = $org_contact;
                break;
            }
        }

        $contact_name = trim($billing_contact->get_first_name() . ' ' . $billing_contact->get_last_name());
        if ((@$transaction['payment_method'] == 'cc' || $org_contact == null) && Settings::instance()->get('accountsiq_cc_invoice_to_customer_code') != '') {
            $customer_code = trim(Settings::instance()->get('accountsiq_cc_invoice_to_customer_code'));
        } else if(@$transaction['aiq_customer_code']) {
            $customer_code = $transaction['aiq_customer_code'];
        } else {
            $billing_contact = $org_contact;
            $org_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $billing_contact->get_id(), 'cms');
            if (!$org_synced) {
                $billing_contact_params = $billing_contact->get_instance();
                $billing_contact_params['email'] = $billing_contact->get_email();
                $billing_contact_params['phone'] = $billing_contact->get_phone();
                $billing_contact_params['mobile'] = $billing_contact->get_mobile();
                $rcontact = $this->save_contact($billing_contact_params);
            } else {
                $rcontact = $this->get_contact($org_synced['remote_id']);
            }
            if ($rcontact) {
                $customer_code = $rcontact->Code;
            } else {
                return false;
            }
        }

        $newinvoiceParams = new stdClass();
        $newinvoiceParams->token = $this->token;
        $newinvoiceParams->customerCode = $customer_code;
        $t1 = microtime(true);
        $newinvoice = $this->soapClient->GetNewSalesInvoice($newinvoiceParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetNewSalesInvoice', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (isset($newinvoice->GetNewSalesInvoiceResult->Result)) {
            $newinvoice = $newinvoice->GetNewSalesInvoiceResult->Result;
        } else {
            return false;
        }

        $newinvoice->CreationDate = date('c');
        if ($this->departmentid) {
            $newinvoice->DepartmentID = $this->departmentid;
        }
        $billing_contact_data = $billing_contact->get_instance();
        if (@$billing_contact_data['billing_residence_id']) {
            $billing_address = new Model_Residence($billing_contact_data['billing_residence_id']);
            $newinvoice->AccountAddress1 = $billing_address->get_address1();
            $newinvoice->AccountAddress2 = $billing_address->get_address2();
            $newinvoice->City = $billing_address->get_town();
            $newinvoice->County_State = $billing_address->get_county_name();
            $newinvoice->Country = Model_Country::get_countries()[$billing_address->get_country()]['name'];
            $newinvoice->PostCode = $billing_address->get_postcode();
        } else {
            if (@$billing_contact_data['residence']) {
                $billing_address = new Model_Residence($billing_contact_data['residence']);
                $newinvoice->AccountAddress1 = $billing_address->get_address1();
                $newinvoice->AccountAddress2 = $billing_address->get_address2();
                $newinvoice->City = $billing_address->get_town();
                $newinvoice->County_State = $billing_address->get_county_name();
                $newinvoice->Country = Model_Country::get_countries()[$billing_address->get_country()]['name'];;
                $newinvoice->PostCode = $billing_address->get_postcode();
            }
        }

        $newinvoice->Phone = '' . ($billing_contact->get_phone() ? $billing_contact->get_phone() : $billing_contact->get_mobile());
        $delegate_names = DB::select(DB::expr('GROUP_CONCAT(DISTINCT CONCAT_WS(" ", contacts.first_name, contacts.last_name) SEPARATOR ", ") as names'))
            ->from(array(Model_KES_Bookings::DELEGATES_TABLE, 'delegates'))
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
            ->on('delegates.contact_id', '=', 'contacts.id')
            ->where('delegates.deleted', '=', 0)
            ->and_where('delegates.cancelled', '=', 0)
            ->and_where('delegates.booking_id', '=', $transaction['booking_id'])
            ->execute()
            ->get('names');
        if ($delegate_names != '') {
            $notes .= " Delegates: " . trim($delegate_names);
        }

        if (@$transaction['invoice_details']) {
            $newinvoice->OrderNumber = $transaction['invoice_details'];
        } else {
            $newinvoice->OrderNumber = ''; //$this->prep_id($transaction['id'], $this->prefix);
        }
        $newinvoice->NetAmount = $transaction['total'];
        $newinvoice->TaxAmount = 0;
        $newinvoice->GrossAmount = $transaction['total'];
        $newinvoice->ExternalReference = $this->prep_id($transaction['id'], $this->prefix);
        $newinvoice->DeliveredQuantity = 0;
        $newinvoice->Contact = $contact_name;
        //$newinvoice->AccountName = "";
        //$newinvoice->Lines = array();
        $schedules = DB::select('schedules.*', array('courses.title', 'course'))
            ->from(array(Model_Kes_Transaction::TABLE_HAS_SCHEDULES, 'hs'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('hs.schedule_id', '=', 'schedules.id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('schedules.course_id', '=', 'courses.id')
            ->where('hs.transaction_id', '=', $transaction['id'])
            ->and_where('hs.deleted', '=', 0)
            ->execute()
            ->as_array();

        $newinvoice->Lines = array();

        foreach ($schedules as $schedule) {
            $siparams = array(
                'token' => $this->token,
                'stockItemID' => 'CCBOOKING1'
            );

            $t1 = microtime(true);
            $siitem = $this->soapClient->GetNewStockItemFromDefaults($siparams);
            $t2 = microtime(true);
            Model_ExternalRequests::create($this->wsdlUrl, 'GetNewStockItemFromDefaults', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
            if ($this->debug) {
                $this->queries[] = array(
                    'request' => $this->get_last_request(),
                    'response' => $this->get_last_response()
                );
            }
            $aitem = $this->get_active_item('CCBOOKING1');

            $line = $siitem->GetNewStockItemFromDefaultsResult->Result;
            if ($this->departmentid) {
                $line->DepartmentID = $this->departmentid;
            }
            $line->InvoiceID = $newinvoice->InvoiceID;
            $line->InvoiceItemID = $this->prefix . $transaction['id'] . '_' . $schedule['id'];
            $line->StockItemID = 'CCBOOKING1';//$this->prep_id($schedule['id'], 'SC');
            $line->StockItemPrice = $transaction['total'];
            $line->TaxCode = 'NT';
            $line->TaxRate = 0;
            $line->StockItemDescription = $schedule['course'] . ' ' . date('d/m/Y', strtotime($schedule['start_date'])) . ' - ' . date('d/m/Y', strtotime($schedule['end_date'])) . ($booked_by ? ' - ' . $booked_by : '');
            $line->StockItemCost = $transaction['total'];
            $line->InvoicedQuantity = 1;
            $line->NetAmount = $transaction['total'];
            $line->TaxAmount = 0;
            $line->GrossAmount = $transaction['total'];
            $line->GLAccountCode = $glaccount;
            $line->ActualPrice = $transaction['total'];
            $line->LocationID = "1";
            $line->SublocationID = "BIN1";
            $line->OpeningStockGLAccountCode = $aitem->OpeningStockGLAccountCode;
            $line->DeliveredQuantity = 0;
            $line->DiscountRate = 0;
            $line->CreationDate = date('c');
            $line->AdditionalCost = 0;
            if ($notes != '') {
                $line->Notes = substr($notes, 0, 1998);
            }
            $newinvoice->Lines[] = $line;
        }

        $saveParams = new stdClass();
        $saveParams->token = $this->token;
        $saveParams->invoice = $newinvoice;
        $saveParams->create = true;
        $t1 = microtime(true);
        $rinvoice = $this->soapClient->SaveInvoiceGetBackInvoiceID($saveParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'SaveInvoiceGetBackInvoiceID', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (isset($rinvoice->SaveInvoiceGetBackInvoiceIDResult->Status) && $rinvoice->SaveInvoiceGetBackInvoiceIDResult->Status == 'Created') {
            $postParams = new stdClass();
            $postParams->token = $this->token;
            $postParams->invoiceID = $rinvoice->invoiceID;
            $this->soapClient->PostInvoice($postParams);

            $rs = new Model_Remotesync();
            $rs->save_object_synced(Model_Accountsiq::API_NAME . '-Transaction', $rinvoice->invoiceID,  $transaction['id']);
        }
        return $rinvoice;
    }

    public function save_transaction_bak($transaction)
    {
        $glaccount = Settings::instance()->get('accountsiq_glaccountcode');
        $contact_name = '';
        $rs = new Model_Remotesync();
        $already_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Transaction', $transaction['id'], 'cms');
        if ($already_synced) {
            return $this->get_transaction($already_synced['remote_id']);
        }
        $billing_contact = new Model_Contacts3($transaction['contact_id']);
        $contact_name = trim($billing_contact->get_first_name() . ' ' . $billing_contact->get_last_name());
        if (@$transaction['payment_method'] == 'cc' && Settings::instance()->get('accountsiq_cc_invoice_to_customer_code') != '') {
            $customer_code = trim(Settings::instance()->get('accountsiq_cc_invoice_to_customer_code'));
        } else if(@$transaction['aiq_customer_code']) {
            $customer_code = $transaction['aiq_customer_code'];
        } else {
            $rcontact = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $transaction['contact_id'], 'cms');

            if ($rcontact) {
                $rcontact_id = $rcontact['remote_id'];
            }

            $rcontact = null;
            if (!$rcontact_id) {
                $contact = new Model_Contacts3($transaction['contact_id']);
                $contact_data = $contact->get_instance();
                $contact_data['email'] = $contact->get_email();
                $contact_data['phone'] = $contact->get_phone();
                $contact_data['mobile'] = $contact->get_mobile();
                $rcontact = $this->save_contact($contact_data);
                $contact_name = trim($contact->get_first_name() . ' ' . $contact->get_last_name());
                if (!$rcontact) {
                    return false;
                }
            }

            if ($rcontact == null) {
                $rcontact = $this->get_contact($rcontact_id);
                if ($rcontact) {
                    $customer_code = $rcontact->Code;
                }
            }

            $organization = Model_Organisation::get_organization_by_primary_biller_id($transaction['contact_id']);
            if ($organization) {
                $org_contact = new Model_Contacts3($organization['contact_id']);
                $billing_contact = $org_contact;
                $contact_name = trim($org_contact->get_first_name() . ' ' . $org_contact->get_last_name());
                $org_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $organization['contact_id'], 'cms');

                $rcontact = $this->get_contact($org_synced['remote_id']);
                if ($rcontact) {
                    $customer_code = $rcontact->Code;
                }
            } else {
                $contact3 = new Model_Contacts3($transaction['contact_id']);
                $contact_name = trim($contact3->get_first_name() . ' ' . $contact3->get_last_name());
                $organization_type = Model_Contacts3::find_type('organisation');
                $related_contact_ids = Model_Contacts3::get_parent_related_contacts($contact3->get_id());
                foreach ($related_contact_ids as $related_contact_id) {
                    $org_contact = new Model_Contacts3($related_contact_id);
                    //if booking is created by an org rep then set the organization for booking contact
                    if ($org_contact->get_type() == $organization_type['contact_type_id']) {
                        $billing_contact = $org_contact;
                        $contact_name = trim($org_contact->get_first_name() . ' ' . $org_contact->get_last_name());
                        $org_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $related_contact_id, 'cms');
                        $rcontact = $this->get_contact($org_synced['remote_id']);
                        if ($rcontact) {
                            $customer_code = $rcontact->Code;
                        }
                        $org = Model_Organisation::get_org_by_contact_id($related_contact_id);
                        $primary_biller_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $org->get_primary_biller_id(), 'cms');
                        if (!$primary_biller_synced) {
                            $primary_biller = new Model_Contacts3($org->get_primary_biller_id());
                            $primary_biller_data = $primary_biller->get_instance();
                            $primary_biller_data['contact_id'] = $primary_biller_data['id'];
                            $primary_biller_data['email'] = $primary_biller->get_email();
                            $primary_biller_data['phone'] = $primary_biller->get_phone();
                            $primary_biller_data['mobile'] = $primary_biller->get_mobile();
                            $this->save_contact($primary_biller_data);
                        }
                        break;
                    }
                }
            }
        }

        $newinvoiceParams = new stdClass();
        $newinvoiceParams->token = $this->token;
        $newinvoiceParams->customerCode = $customer_code;
        $t1 = microtime(true);
        $newinvoice = $this->soapClient->GetNewSalesInvoice($newinvoiceParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetNewSalesInvoice', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (isset($newinvoice->GetNewSalesInvoiceResult->Result)) {
            $newinvoice = $newinvoice->GetNewSalesInvoiceResult->Result;
        } else {
            return false;
        }

        $newinvoice->DepartmentID = $this->departmentid;
        $billing_contact_data = $billing_contact->get_instance();
        if (@$billing_contact_data['billing_residence_id']) {
            $billing_address = new Model_Residence($billing_contact_data['billing_residence_id']);
            $newinvoice->AccountAddress1 = $billing_address->get_address1();
            $newinvoice->AccountAddress2 = $billing_address->get_address2();
            $newinvoice->City = $billing_address->get_town();
            $newinvoice->County_State = $billing_address->get_county_name();
            $newinvoice->Country = $billing_address->get_country();
            $newinvoice->PostCode = $billing_address->get_postcode();
        }
        $newinvoice->Phone = '' . ($billing_contact->get_phone() ? $billing_contact->get_phone() : $billing_contact->get_mobile());
        $delegate_names = DB::select(DB::expr('GROUP_CONCAT(DISTINCT CONCAT_WS(" ", contacts.first_name, contacts.last_name) SEPARATOR ", ") as names'))
            ->from(array(Model_KES_Bookings::DELEGATES_TABLE, 'delegates'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('delegates.contact_id', '=', 'contacts.id')
            ->where('delegates.deleted', '=', 0)
            ->and_where('delegates.cancelled', '=', 0)
            ->execute()
            ->get('names');
        if ($delegate_names != '') {
            //$newinvoice->Notes = substr("Delegates: " . trim($delegate_names), 0, 1998);
        }

        $newinvoice->OrderID = $this->prep_id($transaction['id'], 'TX');
        $newinvoice->NetAmount = $transaction['total'];
        $newinvoice->TaxAmount = 0;
        $newinvoice->GrossAmount = $transaction['total'];
        $newinvoice->ExternalReference = 'CCTX' . $transaction['id'];
        $newinvoice->DeliveredQuantity = 0;
        $newinvoice->Contact = $contact_name;
        //$newinvoice->AccountName = "";
        //$newinvoice->Lines = array();
        $schedules = DB::select('schedules.*')
            ->from(array(Model_Kes_Transaction::TABLE_HAS_SCHEDULES, 'hs'))
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('hs.schedule_id', '=', 'schedules.id')
            ->where('hs.transaction_id', '=', $transaction['id'])
            ->and_where('hs.deleted', '=', 0)
            ->execute()
            ->as_array();

        $newinvoice->Lines = array();

        foreach ($schedules as $schedule) {
            $siparams = array(
                'token' => $this->token,
                'stockItemID' => 'CCBOOKING1'
            );

            $t1 = microtime(true);
            $siitem = $this->soapClient->GetNewStockItemFromDefaults($siparams);
            $t2 = microtime(true);
            Model_ExternalRequests::create($this->wsdlUrl, 'GetNewStockItemFromDefaults', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
            if ($this->debug) {
                $this->queries[] = array(
                    'request' => $this->get_last_request(),
                    'response' => $this->get_last_response()
                );
            }
            $aitem = $this->get_active_item('CCBOOKING1');

            $line = $siitem->GetNewStockItemFromDefaultsResult->Result;
            $line->InvoiceID = $newinvoice->InvoiceID;
            $line->InvoiceItemID = 'BK' . $transaction['id'] . '_' . $schedule['id'];
            $line->StockItemID = 'CCBOOKING1';//$this->prep_id($schedule['id'], 'SC');
            $line->StockItemPrice = $transaction['total'];
            $line->TaxCode = 'NT';
            $line->TaxRate = 0;
            $line->StockItemDescription = $schedule['name'];
            $line->StockItemCost = $transaction['total'];
            $line->InvoicedQuantity = 1;
            $line->NetAmount = $transaction['total'];
            $line->TaxAmount = 0;
            $line->GrossAmount = $transaction['total'];
            $line->GLAccountCode = $glaccount;
            $line->ActualPrice = $transaction['total'];
            $line->LocationID = "1";
            $line->SublocationID = "BIN1";
            $line->OpeningStockGLAccountCode = $aitem->OpeningStockGLAccountCode;
            $line->DeliveredQuantity = 0;
            $line->DiscountRate = 0;
            $line->CreationDate = date('c');
            $line->AdditionalCost = 0;
            $newinvoice->Lines[] = $line;
        }
        
        $saveParams = new stdClass();
        $saveParams->token = $this->token;
        $saveParams->invoice = $newinvoice;
        $saveParams->create = true;
        $t1 = microtime(true);
        $rinvoice = $this->soapClient->SaveInvoiceGetBackInvoiceID($saveParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'SaveInvoiceGetBackInvoiceID', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (isset($rinvoice->SaveInvoiceGetBackInvoiceIDResult->Status) && $rinvoice->SaveInvoiceGetBackInvoiceIDResult->Status == 'Created') {
            $postParams = new stdClass();
            $postParams->token = $this->token;
            $postParams->invoiceID = $rinvoice->invoiceID;
            $this->soapClient->PostInvoice($postParams);

            $rs = new Model_Remotesync();
            $rs->save_object_synced(Model_Accountsiq::API_NAME . '-Transaction', $rinvoice->invoiceID,  $transaction['id']);
        }
        return $rinvoice;
    }

    public function save_transaction_batchsalesinvoice($transaction)
    {
        $glaccount = Settings::instance()->get('accountsiq_glaccountcode');
        $rs = new Model_Remotesync();
        $already_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Transaction', $transaction['id'], 'cms');
        if ($already_synced) {
            return $this->get_transaction($already_synced['remote_id']);
        }
        if (@$transaction['payment_method'] == 'cc' && Settings::instance()->get('accountsiq_cc_invoice_to_customer_code') != '') {
            $customer_code = trim(Settings::instance()->get('accountsiq_cc_invoice_to_customer_code'));
        } else if(@$transaction['aiq_customer_code']) {
            $customer_code = $transaction['aiq_customer_code'];
        } else {
        $rcontact = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $transaction['contact_id'], 'cms');

        if ($rcontact) {
            $rcontact_id = $rcontact['remote_id'];
        }

        $rcontact = null;
        if (!$rcontact_id) {
            $contact = new Model_Contacts3($transaction['contact_id']);
            $contact_data = $contact->get_instance();
            $contact_data['email'] = $contact->get_email();
            $contact_data['phone'] = $contact->get_phone();
            $contact_data['mobile'] = $contact->get_mobile();
            $rcontact = $this->save_contact($contact_data);
            if (!$rcontact) {
                return false;
            }
        }

        if ($rcontact == null) {
            $rcontact = $this->get_contact($rcontact_id);
            if ($rcontact) {
                $customer_code = $rcontact->Code;
            }
        }

            $organization = Model_Organisation::get_organization_by_primary_biller_id($transaction['contact_id']);
            if ($organization) {
                $org_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $organization['contact_id'], 'cms');

                $rcontact = $this->get_contact($org_synced['remote_id']);
                if ($rcontact) {
                    $customer_code = $rcontact->Code;
                }
            } else {
                $contact3 = new Model_Contacts3($transaction['contact_id']);
                $organization_type = Model_Contacts3::find_type('organisation');
                $related_contact_ids = Model_Contacts3::get_parent_related_contacts($contact3->get_id());
                foreach ($related_contact_ids as $related_contact_id) {
                    $org_contact = new Model_Contacts3($related_contact_id);
                    //if booking is created by an org rep then set the organization for booking contact
                    if ($org_contact->get_type() == $organization_type['contact_type_id']) {
                        $org_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $related_contact_id, 'cms');
                        $rcontact = $this->get_contact($org_synced['remote_id']);
                        if ($rcontact) {
                            $customer_code = $rcontact->Code;
                        }
                        $org = Model_Organisation::get_org_by_contact_id($related_contact_id);
                        $primary_biller_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $org->get_primary_biller_id(), 'cms');
                        if (!$primary_biller_synced) {
                            $primary_biller = new Model_Contacts3($org->get_primary_biller_id());
                            $primary_biller_data = $primary_biller->get_instance();
                            $primary_biller_data['contact_id'] = $primary_biller_data['id'];
                            $primary_biller_data['email'] = $primary_biller->get_email();
                            $primary_biller_data['phone'] = $primary_biller->get_phone();
                            $primary_biller_data['mobile'] = $primary_biller->get_mobile();
                            $this->save_contact($primary_biller_data);
                        }
                        break;
                    }
                }
            }
        }
        
        $newinvoiceParams = new stdClass();
        $newinvoiceParams->token = $this->token;
        $newinvoiceParams->customerCode = $customer_code;
        $t1 = microtime(true);
        $newinvoice = $this->soapClient->GetNewBatchSalesInvoice($newinvoiceParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'GetNewBatchSalesInvoice', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (isset($newinvoice->GetNewBatchSalesInvoiceResult->Result)) {
            $newinvoice = $newinvoice->GetNewBatchSalesInvoiceResult->Result;
        } else {
            return false;
        }
        //print_R($newinvoice);exit;
        $newinvoice->Description = $this->prep_id($transaction['id'], 'TX');
        $newinvoice->ExternalReference = 'CCTX' . $transaction['id'];
        //$newinvoice->AccountName = "";
        //$newinvoice->Lines = array();
        $schedules = DB::select('schedules.*')
            ->from(array(Model_Kes_Transaction::TABLE_HAS_SCHEDULES, 'hs'))
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
            ->on('hs.schedule_id', '=', 'schedules.id')
            ->where('hs.transaction_id', '=', $transaction['id'])
            ->and_where('hs.deleted', '=', 0)
            ->execute()
            ->as_array();

        $line = new BatchSalesInvoiceLine();
        $newinvoice->Lines = array();

        foreach ($schedules as $schedule) {
            $line->Description = $schedule['name'] . ' #' . $schedule['id'];
            $line->DepartmentID = $this->departmentid;
            $line->GLAccountCode = $glaccount;
            $line->NetAmount = $transaction['total'];
            $line->TaxCode = 'NT';
            $line->TaxRate = 0;
            $newinvoice->Lines[] = $line;
        }

        $saveParams = new stdClass();
        $saveParams->token = $this->token;
        $saveParams->inv = $newinvoice;
        $t1 = microtime(true);
        $rinvoice = $this->soapClient->CreateBatchSalesInvoice($saveParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'CreateBatchSalesInvoice', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (isset($rinvoice->CreateBatchSalesInvoiceResult->Status) && $rinvoice->CreateBatchSalesInvoiceResult->Status == 'Success') {
            $rs->save_object_synced(Model_Accountsiq::API_NAME . '-Transaction', $rinvoice->CreateBatchSalesInvoiceResult->Result,  $transaction['id']);
        }
        return $rinvoice;
    }

    public function sync_transactions($to = 'BOTH', $transactions_to_sync = null)
    {
        try {
            Database::instance()->begin();

            $synced_transactions = DB::select('*')
                ->from(Model_Remotesync::SYNC_TABLE)
                ->where('type', '=', Model_Accountsiq::API_NAME . '-Transaction')
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_transactions as $synced_transaction) {
                $local_id_map[$synced_transaction['cms_id']] = $synced_transaction['remote_id'];
                $remote_id_map[$synced_transaction['remote_id']] = $synced_transaction['cms_id'];
            }

            $rs = new Model_Remotesync();

            $ltransactions = DB::select('*')
                ->from(Model_Kes_Transaction::TRANSACTION_TABLE)
                ->where('deleted', '=', 0)
                ->execute()
                ->as_array();
            $rtransactions = $this->get_transactions();

            if ($to == 'BOTH' || $to = 'REMOTE') {
                foreach ($ltransactions as $ltransaction) {
                    if ($transactions_to_sync === null || in_array($ltransaction['id'], $transactions_to_sync)) {
                        if (@$local_id_map[$ltransaction['id']] == null) {
                            $rtransaction = $this->save_transaction($ltransaction);
                            $local_id_map[$ltransaction['id']] = $rtransaction->invoiceID;
                            $remote_id_map[$rtransaction->invoiceID] = $ltransaction['id'];
                        }
                    }
                }
            }

            if ($to == 'BOTH' || $to = 'LOCAL') {
                foreach ($rtransactions as $rtransaction) {
                    if (@$remote_id_map[$rtransaction['remote_id']] == null) {

                    }
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
        return array();
    }

    public function save_payment($payment)
    {
        $transaction = DB::select(
            'transactions.*',
            'bookings.payment_method',
            'bookings.invoice_details'
        )
            ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                ->join(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'), 'left')
                    ->on('transactions.booking_id', '=', 'bookings.booking_id')
            ->where('transactions.id', '=', $payment['transaction_id'])
            ->execute()
            ->current();

        $rs = new Model_Remotesync();
        $already_synced = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Payment', $transaction['id'], 'cms');
        if ($already_synced) {
            return $already_synced['remote_id'];
        }
        $rtransaction = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Transaction', $payment['transaction_id'], 'cms');
        if (!$rtransaction) {
            if (!$this->save_transaction($transaction)) {
                return false;
            }
        }

        $rs = new Model_Remotesync();
        if (@$payment['type'] == 'card' && Settings::instance()->get('accountsiq_cc_invoice_to_customer_code') != '') {
            $rcontact_id = trim(Settings::instance()->get('accountsiq_cc_invoice_to_customer_code'));
        } else if(@$transaction['aiq_customer_code']) {
            $rcontact_id = $transaction['aiq_customer_code'];
        } else {
            $rcontact = $rs->get_object_synced(Model_Accountsiq::API_NAME . '-Contact', $transaction['contact_id'], 'cms');

            if ($rcontact) {
                $rcontact_id = $rcontact['remote_id'];
            }

            if (!$rcontact_id) {
                $contact = new Model_Contacts3($transaction['contact_id']);
                $contact = $contact->get_instance();
                $rcontact = $this->save_contact($contact);
                if (!$rcontact) {
                    return false;
                }
                $rcontact_id = $rcontact->Code;
            }
        }

        $newsr = new stdClass();
        $newsr->CustomerCode = $rcontact_id;
        if ($payment['type'] == 'cash') {
            $newsr->BankAccountCode = Settings::instance()->get('accountsiq_glcashaccountcode');
        } else if ($payment['type'] == 'card') {
            $newsr->BankAccountCode = Settings::instance()->get('accountsiq_glcardaccountcode');
        } else if ($payment['type'] == 'cheque') {
            $newsr->BankAccountCode = Settings::instance()->get('accountsiq_glchequeaccountcode');
        } else {
            return false;
        }
        $newsr->Description = $this->prep_id($transaction['id'], $this->prefix);
        $newsr->BankExchangeRate = 1;
        $newsr->ExchangeRate = 1;
        $newsr->PaymentAmount = (float)$payment['amount'];
        $newsr->PaymentDate = date('c', strtotime($payment['created']));
        $newsr->CheckReference = $this->prep_id($transaction['id'], $this->prefix);
        $newsr->LodgmentNumber = "";


        $saveParams = new stdClass();
        $saveParams->token = $this->token;
        $saveParams->salesReceipt = $newsr;
        $t1 = microtime(true);
        $rpayment = $this->soapClient->SaveSalesReceiptGetBackTransactionID($saveParams);
        $t2 = microtime(true);
        Model_ExternalRequests::create($this->wsdlUrl, 'SaveSalesReceiptGetBackTransactionID', $this->get_last_request(), $this->get_last_response(), '', date::now(), null, $t2-$t1);
        if ($this->debug) {
            $this->queries[] = array(
                'request' => $this->get_last_request(),
                'response' => $this->get_last_response()
            );
        }
        if (isset($rpayment->SaveSalesReceiptGetBackTransactionIDResult->Status) && isset($rpayment->transactionID) && $rpayment->SaveSalesReceiptGetBackTransactionIDResult->Status == 'Success') {
            $rs = new Model_Remotesync();
            $rs->save_object_synced(Model_Accountsiq::API_NAME . '-Payment', $rpayment->transactionID,  $payment['id']);
            return $rpayment->transactionID;
        }

        return false;
    }

    public function sync_payments($to = 'BOTH', $payments_to_sync = null)
    {
        try {
            Database::instance()->begin();

            $synced_payments = DB::select('*')
                ->from(Model_Remotesync::SYNC_TABLE)
                ->where('type', '=', Model_Accountsiq::API_NAME . '-Payment')
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_payments as $synced_payment) {
                $local_id_map[$synced_payment['cms_id']] = $synced_payment['remote_id'];
                $remote_id_map[$synced_payment['remote_id']] = $synced_payment['cms_id'];
            }

            $rs = new Model_Remotesync();

            $lpaymentsq = DB::select('payments.*', 'cheque_details.name_cheque')
                ->from(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'))
                    ->join(array('plugin_bookings_transactions_payments_cheque', 'cheque_details'), 'left')
                        ->on('payments.id', '=', 'cheque_details.payment_id')
                ->where('payments.deleted', '=', 0);
            if (is_array($payments_to_sync)) {
                $lpaymentsq->and_where('payments.id', 'in', $payments_to_sync);
            }
            $lpayments = $lpaymentsq->execute()->as_array();
            $rpayments = $this->get_payments();

            if ($to == 'BOTH' || $to = 'REMOTE') {
                foreach ($lpayments as $lpayment) {
                    if ($payments_to_sync === null || in_array($lpayment['id'], $payments_to_sync)) {
                        if (@$local_id_map[$lpayment['id']] == null) {
                            $rpayment = $this->save_payment($lpayment);
                            $local_id_map[$lpayment['id']] = $rpayment->transactionID;
                            $remote_id_map[$rpayment->invoiceID] = $lpayment['id'];
                        }
                    }
                }
            }

            if ($to == 'BOTH' || $to = 'LOCAL') {
                foreach ($rpayments as $rpayment) {
                    if (@$remote_id_map[$rpayment['remote_id']] == null) {

                    }
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function test()
    {
        try {
            $this->login();
            return true;
        } catch (Exception $exc) {
            print_r($exc->getMessage());
            return false;
        }
    }
}

class BatchSalesInvoiceLine
{

}