<?php defined('SYSPATH') or die('No Direct Script Access.');

require_once APPPATH . '/vendor/xero/load.php';

final class Model_Xero extends Model implements Model_Remoteaccountingapi
{
    const API_NAME = 'Xero';

    public $key = '';
    public $secret = '';
    public $pkey = '';
    protected $xero = null;

    public function __construct()
    {
        $settings = Settings::instance();
        if ($settings->get('xero_pkey') == '') {
            return '';
        }

        $config = [
            'oauth' => [
                'callback' => URL::site('/admin/xero/callback'),
                'consumer_key' => $settings->get('xero_key'),
                'consumer_secret' => $settings->get('xero_secret'),
                'rsa_private_key' => $settings->get('xero_pkey'),
            ],
        ];
        $this->xero = new XeroPHP\Application\PrivateApplication($config);

    }

    public static function get_account_options($selected = '')
    {

        $xero = new Model_Xero();
        try {
            $acc = $xero->get_accounts();
        } catch (Exception $exc) {
            $acc = array();
        }
        $accounts = array(
            '' => ''
        );
        foreach ($acc as $a) {
            $accounts[$a->getCode()] = $a->getName();
        }

        return html::optionsFromArray($accounts, $selected);
    }

    public function get_accounts()
    {
        if ($this->xero == null) {
            return array();
        }

        return $this->xero->load('Accounting\Account')->execute();
    }

    public function get_contacts($cache_remote = false)
    {
        static $cache = null;
        if ($this->xero == null) {
            return array();
        }

        if ($cache == null || $cache_remote == false) {
            $contacts = $this->xero->load('Accounting\Contact')->execute();
            $result = array();
            foreach ($contacts as $contact) {
                $mobile = '';
                foreach ($contact->Phones as $phone) {
                    if ($phone['PhoneType'] == 'MOBILE') {
                        $mobile = $phone['PhoneNumber'];
                    }
                }
                $result[] = array(
                    'remote_id' => $contact['ContactID'],
                    'name' => $contact['FirstName'] . ' ' . $contact['LastName'],
                    'firstname' => $contact['FirstName'],
                    'lastname' => $contact['LastName'],
                    'email' => $contact['EmailAddress'],
                    'mobile' => $mobile,
                    'updated' => $contact->getUpdatedDateUTC()->format("Y-m-d H:i:s")
                );
            }
            if ($cache_remote) {
                $cache = $result;
            }
        }
        if ($cache_remote) {
            $result = $cache;
        }

        return $result;
    }

    public function sync_contacts($to = '', $contacts_to_sync = null)
    {
        try {
            Database::instance()->begin();

            $synced_contacts = DB::select('*')
                ->from(Model_Remotesync::SYNC_TABLE)
                ->where('type', '=', Model_Xero::API_NAME . '-Contact')
                ->execute()
                ->as_array();

            $rs = new Model_Remotesync();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_contacts as $synced_contact) {
                $local_id_map[$synced_contact['cms_id']] = $synced_contact['remote_id'];
                $remote_id_map[$synced_contact['remote_id']] = $synced_contact['cms_id'];
            }

            $lcontacts = Model_Contacts3::get_all_contacts();
            try {
                $rcontacts = $this->get_contacts();
            } catch (Exception $exc) {
                $rcontacts = array();
            }
            if ($to == 'BOTH' || $to == 'REMOTE') {
                foreach ($lcontacts as $lcontact) {
                    if (@$local_id_map[$lcontact['id']] == null) {
                        if ($contacts_to_sync === null || in_array($lcontact['id'], $contacts_to_sync)) {
                            try {
                                $this->save_contact($lcontact);
                            } catch (Exception $exc) {
                                if (strpos($exc->getMessage(), 'already assigned to another contact')) {
                                    $r_e_contacts = $this->get_contacts(true);
                                    foreach ($r_e_contacts as $r_e_contact) {
                                        if ($r_e_contact['name'] . ' #' . $lcontact['id'] == $lcontact['first_name'] . ' ' . $lcontact['last_name'] . ' #' . $lcontact['id']) {
                                            $rs->save_object_synced(Model_Xero::API_NAME . '-Contact', $r_e_contact['remote_id'], $lcontact['id']);
                                        }
                                    }
                                }
                            }
                            //echo $lcontact['first_name'] . " " . $lcontact["last_name"] . " to remote\n";
                        }
                    }
                }
            }

            if ($to == 'BOTH' || $to == 'LOCAL') {
                foreach ($rcontacts as $rcontact) {
                    if (@$remote_id_map[$rcontact['remote_id']] == null) {
                        if ($contacts_to_sync === null || in_array($rcontact['remote_id'], $contacts_to_sync)) {
                            $check_emails = Model_Contacts3::search(array('email' => $rcontact['email']));
                            if (count($check_emails) > 0){
                                $local_id_map[$check_emails[0]['id']] = $rcontact['remote_id'];
                                $remote_id_map[$rcontact['remote_id']] = $check_emails[0]['id'];
                                $rs->save_object_synced(Model_Xero::API_NAME . '-Contact', $rcontact['remote_id'], $check_emails[0]['id']);
                                continue;
                            }
                            $check_emails = Model_Contacts3::search(array('email' => $rcontact['mobile']));
                            if (count($check_emails) > 0){
                                $local_id_map[$check_emails[0]['id']] = $rcontact['remote_id'];
                                $remote_id_map[$rcontact['remote_id']] = $check_emails[0]['id'];
                                $rs->save_object_synced(Model_Xero::API_NAME . '-Contact', $rcontact['remote_id'], $check_emails[0]['id']);
                                continue;
                            }
                            $f = new Model_Family();
                            $f->set_family_name($rcontact['lastname'] ?: $rcontact['firstname']);
                            $f->save();
                            $c3 = new Model_Contacts3();
                            $c3->set_family_id($f->get_id());
                            $c3->set_is_primary(1);
                            $c3->set_first_name($rcontact['firstname']);
                            $c3->set_last_name($rcontact['lastname']);
                            $c3->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                            $c3->set_subtype_id(1);
                            $c3->set_date_modified($rcontact['updated']);
                            $c3->trigger_save = false;
                            $c3->save();

                            if ($rcontact['email'] != '') {
                                $c3->insert_notification(array(
                                    'value' => $rcontact['email'],
                                    'notification_id' => 1
                                ));
                            }
                            $rs->save_object_synced(Model_Xero::API_NAME . '-Contact', $rcontact['remote_id'],
                                $c3->get_id());
                            $local_id_map[$c3->get_id()] = $rcontact['remote_id'];
                            $remote_id_map[$rcontact['remote_id']] = $c3->get_id();

                            //echo $rcontact->getFirstName() . " " . $rcontact->getLastName() . " to local\n";
                        }
                    }
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
        $rs = new Model_Remotesync();
        $synced = $rs->get_object_synced(Model_Xero::API_NAME . '-Contact', $contact['id'], 'cms_id');
        if ($synced) {
            $cnew = $this->xero->loadByGUID(XeroPHP\Models\Accounting\Contact::class, $synced['remote_id']);
        } else {
            if (@$contact['email']) {
                $exists = $this->xero->load('Accounting\Contact')->where('EmailAddress=="' . $contact['email'] . '"')->execute();
                if ($exists->count() > 0) {
                    $rs = new Model_Remotesync();
                    $rs->save_object_synced(Model_Xero::API_NAME . '-Contact', $exists[0]['ContactID'], $contact['id']);
                    return $exists[0];
                }
            }
            if (@$contact['mobile']) {
                $mobile7 = str_replace(array('', '-'), '', $contact['mobile']);
                $exists = $this->xero->load('Accounting\Contact')->where('Phones[3].PhoneNumber=="' . $mobile7 . '"')->execute();
                if ($exists->count() > 0) {
                    $rs = new Model_Remotesync();
                    $rs->save_object_synced(Model_Xero::API_NAME . '-Contact', $exists[0]['ContactID'], $contact['id']);
                    return $exists[0];
                }
                /*$contacts = $this->xero->load('Accounting\Contact')->execute();
                foreach ($contacts as $rcontact) {
                    foreach ($rcontact->Phones as $rphone) {
                        if ($rphone->PhoneNumber == $contact['mobile']) {
                            $rs = new Model_Remotesync();
                            $rs->save_object_synced(Model_Xero::API_NAME . '-Contact', $rcontact['ContactID'],
                                $contact['id']);
                            return $rcontact;
                        }
                    }
                }*/
            }
            $cnew = new XeroPHP\Models\Accounting\Contact();
        }

        $cnew->setName(@$contact['first_name'] . ' ' . @$contact['last_name'] . ' #' . sprintf('%6d', $contact['id']));
        if (@$contact['first_name']) {
            $cnew->setFirstName($contact['first_name']);
        }
        if (@$contact['last_name']) {
            $cnew->setLastName($contact['last_name']);
        }
        if (@$contact['email']) {
            $cnew->setEmailAddress($contact['email']);
        }
        $cnew->setUpdatedDateUTC(new DateTime($contact['date_modified']));
        if (@$contact['mobile']) {
            //$cnew->($contact['mobile']);
            $m = new XeroPHP\Models\Accounting\Phone();
            $m->setPhoneType('MOBILE');
            $m->setPhoneNumber($contact['mobile']);
            $cnew->addPhone($m);
        }
        $e = $this->xero->save($cnew)->getElements();
        if (isset($e[0])) {
            $c = $e[0];

            $rs = new Model_Remotesync();
            $rs->save_object_synced(Model_Xero::API_NAME . '-Contact', $c['ContactID'], $contact['id']);
            return $c;
        } else {
            return false;
        }
    }

    public function delete_contact($contact)
    {

    }

    public function get_transactions()
    {
        if ($this->xero == null) {
            return array();
        }

        $rinvoices = $this->xero->load('Accounting\Invoice')->execute();
        $transactions = array();
        foreach ($rinvoices as $rinvoice) {
            $transaction = array();
            $transaction['amount'] = $rinvoice->SubTotal;
            $transaction['total'] = $rinvoice->Total;
            $transaction['remote_contact_id'] = $rinvoice->Contact->ContactID;
            $transaction['remote_id'] = $rinvoice->InvoiceNumber;
            $transactions[] = $transaction;
        }
        return $transactions;
    }

    public function save_transaction($transaction)
    {
        $this->sync_contacts('REMOTE', array($transaction['contact_id']));
        $rcontact_id = DB::select('remote_id')
            ->from(Model_Remotesync::SYNC_TABLE)
            ->where('cms_id', '=', $transaction['contact_id'])
            ->and_where('type', '=', Model_Xero::API_NAME . '-Contact')
            ->execute()
            ->get('remote_id');
        if (!$rcontact_id) {
            return false;
        }

        $rcontact = new XeroPHP\Models\Accounting\Contact();
        $rcontact->setContactID($rcontact_id);
        $rs = new Model_Remotesync();
        $synced = $rs->get_object_synced(Model_Xero::API_NAME . '-Transaction', $transaction['id'], 'cms_id');
        $update = false;
        if ($synced) {
            $txnew = $this->xero->loadByGUID(XeroPHP\Models\Accounting\Invoice::class, $synced['remote_id']);
            $update = true;
        } else {
            $txnew = new XeroPHP\Models\Accounting\Invoice();
        }
        if (!$update) {
        $acc = new XeroPHP\Models\Accounting\Account();
        //echo Settings::instance()->get('xero_account_invoice');exit;
        $acc->setCode(Settings::instance()->get('xero_account_invoice'));
        $txnew->setContact($rcontact);
        $txnew->setDate(new DateTime($transaction['created']));
        $txnew->setDueDate(new DateTime($transaction['due_date']));
        $txnew->setType('ACCREC');
        $txnew->setStatus('AUTHORISED');


        foreach ($transaction['items'] as $item) {
            $ritem = new XeroPHP\Models\Accounting\Invoice\LineItem();
            $ritem->setAccountCode(Settings::instance()->get('xero_account_invoice'));
            $ritem->setLineAmount($item['amount']);
            $ritem->setDescription($item['description']);
            $ritem->setTaxAmount(0);
            $ritem->setTaxType('NONE');
            $txnew->addLineItem($ritem);
        }
        }

        if ($transaction['interest_total'] > 0) {
            $ritem = new XeroPHP\Models\Accounting\Invoice\LineItem();
            $ritem->setAccountCode(Settings::instance()->get('xero_account_invoice'));
            $ritem->setLineAmount($transaction['interest_total']);
            $ritem->setDescription('Payment Plan Interest');
            $ritem->setTaxAmount(0);
            $ritem->setTaxType('NONE');
            $txnew->addLineItem($ritem);
        }

        $response = $this->xero->save($txnew);
        if (!$update) {
            $e = $response->getElements();

            if (isset($e[0])) {
                $t = $e[0];
                $rs->save_object_synced(Model_Xero::API_NAME . '-Transaction', $t['InvoiceID'], $transaction['id']);
                return $t;
            }
        } else {
            return $txnew;
        }
    }

    public function sync_transactions($to = 'BOTH', $transactions_to_sync = null)
    {
        try {
            Database::instance()->begin();

            $synced_transactions = DB::select('*')
                ->from(Model_Remotesync::SYNC_TABLE)
                ->where('type', '=', Model_Xero::API_NAME . '-Transaction')
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_transactions as $synced_transaction) {
                $ltransaction_id = $synced_transaction['cms_id'];
                $local_id_map[$ltransaction_id] = $synced_transaction['remote_id'];
                $remote_id_map[$synced_transaction['remote_id']] = $ltransaction_id;
            }

            $ltransactions = array();
            if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
                $btransactions = Model_Kes_Transaction::search(array('type' => array(1, 2), 'id' => $transactions_to_sync));

                foreach ($btransactions as $btransaction) {
                    $ltransactions[] = array(
                        'id' => $btransaction['id'],
                        'contact_id' => $btransaction['contact_id'],
                        'created' => $btransaction['created'],
                        'due_date' => $btransaction['payment_due_date'],
                        'items' => array(
                            array('amount' => $btransaction['total'], 'description' => $btransaction['schedules'] . $btransaction['courses'])
                        )
                    );
                }
            }
            $rtransactions = $this->get_transactions();

            if ($to == 'BOTH' || $to == 'REMOTE') {
                foreach ($ltransactions as $ltransaction) {
                    if (@$local_id_map[$ltransaction['id']] == null) {
                        if ($transactions_to_sync === null || in_array($ltransaction['id'], $transactions_to_sync)) {
                            $this->save_transaction($ltransaction);
                        }
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
        if ($this->xero == null) {
            return array();
        }

        $payments = $this->xero->load('Accounting\Payment')->execute();
        return $payments;
    }

    public function save_payment($payment)
    {
        $rtransaction_id = DB::select('remote_id')
            ->from(Model_Remotesync::SYNC_TABLE)
            ->where('cms_id', '=', $payment['transaction_id'])
            ->and_where('type', '=', Model_Xero::API_NAME . '-Transaction')
            ->execute()
            ->get('remote_id');
        $rinvoice = new XeroPHP\Models\Accounting\Invoice();
        $rinvoice->setInvoiceID($rtransaction_id);

        $ts = $this->get_transactions();
        foreach ($ts as $t) {
            //echo $t->getInvoiceNumber() . ":" . $t->getInvoiceID() . "\n";
        }
        //exit;
        if ($payment['refund']) {
            $rcontact_id = DB::select('remote_id')
                ->from(Model_Remotesync::SYNC_TABLE)
                ->where('cms_id', '=', $payment['contact_id'])
                ->and_where('type', '=', Model_Xero::API_NAME . '-Contact')
                ->execute()
                ->get('remote_id');
            $rcontact = new XeroPHP\Models\Accounting\Contact();
            $rcontact->setContactID($rcontact_id);

            $creditnote = new XeroPHP\Models\Accounting\CreditNote();
            $creditnote->setContact($rcontact);
            $creditnote->setTotal($payment['amount']);
            $creditnote->setTotalTax(0);
            $creditnote->setType('ACCRECCREDIT');
                $ritem = new XeroPHP\Models\Accounting\Invoice\LineItem();
                $ritem->setAccountCode(Settings::instance()->get('xero_account_invoice'));
                $ritem->setLineAmount($payment['amount']);
                $ritem->setDescription($payment['description'] ? $payment['description'] : '#' . $payment['id']);
                $ritem->setTaxAmount(0);
                $ritem->setTaxType('NONE');
                $creditnote->addLineItem($ritem);

            $response = $this->xero->save($creditnote);
            $e = $response->getElements();

            if (isset($e[0])) {
                $p = $e[0];

                $rs = new Model_Remotesync();
                $rs->save_object_synced(Model_Xero::API_NAME . '-Payment', $p['CreditNoteID'],  $payment['id']);
                return $p;
            }
        } else {
            $paynew = new XeroPHP\Models\Accounting\Payment();
            $acc = new XeroPHP\Models\Accounting\Account();
            $acc->setCode(Settings::instance()->get('xero_account_payment'));
            $paynew->setAccount($acc);
            $paynew->setStatus('AUTHORISED');
            $paynew->setAmount($payment['amount']);
            $paynew->setInvoice($rinvoice);
            try {
                $response = $this->xero->save($paynew);
            } catch (Exception $exc) {
                return false;
            }
            $e = $response->getElements();

            if (isset($e[0])) {
                $p = $e[0];

                $rs = new Model_Remotesync();
                $rs->save_object_synced(Model_Xero::API_NAME . '-Payment', $p['PaymentID'],  $payment['id']);
                return $p;
            }
        }
    }

    public function sync_payments($to = 'BOTH', $payments_to_sync = null)
    {
        if ($payments_to_sync == null) {
            $this->sync_transactions();
        }
        try {
            Database::instance()->begin();

            $synced_transactions = DB::select('*')
                ->from(Model_Remotesync::SYNC_TABLE)
                ->where('type', '=', Model_Xero::API_NAME . '-Payment')
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_transactions as $synced_transaction) {
                $ltransaction_id = $synced_transaction['cms_id'];
                $local_id_map[$ltransaction_id] = $synced_transaction['remote_id'];
                $remote_id_map[$synced_transaction['remote_id']] = $ltransaction_id;
            }

            $synced_payments = DB::select('*')
                ->from(Model_Remotesync::SYNC_TABLE)
                ->where('type', '=', Model_Xero::API_NAME . '-Payment')
                ->execute()
                ->as_array();

            $local_id_map = array();
            $remote_id_map = array();
            foreach ($synced_payments as $synced_payment) {
                $lpayment_id = $synced_payment['cms_id'];
                $local_id_map[$lpayment_id] = $synced_payment['remote_id'];
                $remote_id_map[$synced_payment['remote_id']] = $lpayment_id;
            }

            $lpayments = array();
            if (Model_Plugin::is_enabled_for_role('Administrator', 'bookings')) {
                $bpayments = Model_Kes_Payment::search();

                foreach ($bpayments as $bpayment) {
                    $lpayments[] = array(
                        'id' => $bpayment['id'],
                        'contact_id' => $bpayment['contact_id'],
                        'transaction_id' => $bpayment['transaction_id'],
                        'amount' => $bpayment['amount'],
                        'refund' => $bpayment['status'] == 8,
                        'description' => $bpayment['schedules'] . $bpayment['courses']
                    );
                }
            }
            $rtransactions = $this->get_payments();

            if ($to == 'BOTH' || $to == 'REMOTE') {
                foreach ($lpayments as $lpayment) {
                    if (@$local_id_map[$lpayment['id']] == null) {
                        if ($payments_to_sync === null || in_array($lpayment['id'], $payments_to_sync)) {
                            $this->save_payment($lpayment);
                        }
                    }
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
}

