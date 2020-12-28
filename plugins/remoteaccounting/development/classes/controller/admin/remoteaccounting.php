<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Remoteaccounting extends Controller_Cms
{
    public function before()
    {
        parent::before();

        /*$this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',     'link' => 'admin'),
            array('name' => 'Contacts', 'link' => 'admin/remoteaccounting')
        );*/
    }

    public function action_index()
    {
        $this->request->redirect('/admin/remoteaccounting/contacts');
    }

    public function action_contacts()
    {
        $params = $this->request->query();
        if (!@$params['status']) {
            $params['status'] = 'synced';
        }
        $contacts = Model_Remoteaccounting::contacts_datatable($params);
        $this->template->body = View::factory('admin/remote_accounting_contacts');
        $this->template->body->status = $params['status'];
        $this->template->body->contacts = $contacts;
    }

    public function action_transactions()
    {
        $params = $this->request->query();
        if (!@$params['status']) {
            $params['status'] = 'synced';
        }
        $transactions = Model_Remoteaccounting::transactions_datatable($params);
        $this->template->body = View::factory('admin/remote_accounting_transactions');
        $this->template->body->status = $params['status'];
        $this->template->body->transactions = $transactions;
    }

    public function action_payments()
    {
        $params = $this->request->query();
        if (!@$params['status']) {
            $params['status'] = 'synced';
        }
        $payments = Model_Remoteaccounting::payments_datatable($params);
        $this->template->body = View::factory('admin/remote_accounting_payments');
        $this->template->body->status = $params['status'];
        $this->template->body->payments = $payments;
    }

    public function action_ajax_get_submenu()
    {
        $types = Model_Contacts3::get_types();
        $return['items'] = [
            array(
                'title' => 'Contacts',
                'link' => '/admin/remoteaccounting/contacts',
                'icon_svg' => 'contacts'
            ),
            array(
                'title' => 'Transactions',
                'link' => '/admin/remoteaccounting/transactions',
                'icon_svg' => 'bookings'
            ),
            array(
                'title' => 'Payments',
                'link' => '/admin/remoteaccounting/payments',
                'icon_svg' => 'purchase'
            ),
        ];

        return $return;
    }

    public function action_test_brc()
    {
        try {
            header('content-type: text/plain; charset=utf-8');
            $brc = new Model_Bigredcloud();
            $brc->key = Settings::instance()->get('bigredcloud_key');
            //$rcustomers = $brc->get_contacts();
            //$remote_id = null;
            //print_r($rcustomers);
            //exit;

            /*$contact = $brc->save_contact(
                array(
                    'id' => 79187,
                    'first_name' => 'kogudey',
                    'last_name' => 'meygen',
                    'email' => 'kogudey@meygen.net',
                    'mobile' => '1234567890'
                )
            );
            print_r($contact);*/

            /*$contact = $brc->save_contact(
                array(
                    'code' => '368733',
                    'first_name' => 'meygen',
                    'last_name' => 'kogudey',
                    'remote_contact_id' => 1741229,
                    'timestamp' => 'AAAAAAXMdWY='
                )
            );

            print_R($contact);
            */

            //$transactions = $brc->get_transactions();

            $accounts = $brc->get_accounts();
            $baccounts = $brc->get_bankaccounts();
            print_r($accounts);print_r($baccounts);
            print_r($brc->analisys_categories());exit;
            //print_r($brc->vat_rates());
            //print_r($brc->vat_types());
            //print_r($brc->vat_categories());exit;
            //print_r($brc->analisys_categories());
            //exit;
            //print_r($accounts);print_r($brc);exit;
            //$tx = $brc->get_transactions();
            //print_r($tx);print_r($brc);exit;
            /*$btransaction = array(
                'id' => 399,
                'contact_id' => 79187,
                'created' => '2018-09-19T08:22:54',
                'payment_due_date' => '2018-09-25T20:00:00',
                'total' => 10.0,
                'schedules' => 'sched 1',
                'courses' => 'course 1'
            );
            $brc->save_transaction(
                array(
                    'table' => 'plugin_bookings_transactions',
                    'id' => $btransaction['id'],
                    'contact_id' => $btransaction['contact_id'],
                    'created' => $btransaction['created'],
                    'due_date' => $btransaction['payment_due_date'],
                    'details' => $btransaction['schedules'] . ' ' . $btransaction['courses'],
                    'total' => $btransaction['total']
                )
            );
            */
            $brc->save_payment(
                array(
                    'table' => 'plugin_bookings_transactions_payments',
                    'transaction_table' => 'plugin_bookings_transactions',
                    'id' => 236,
                    'transaction_id' => 399,
                    'amount' => 10,
                )
            );
            print_r($brc);

        } catch (Exception $exc) {
            print_r($exc);
        }
        exit;
    }

    public function action_test_xero()
    {
        try {
            require_once APPPATH . '/vendor/xero/load.php';

            header('content-type: text/plain; charset=utf-8');
            /*$xero = new Xero();
            $xero->password = 'mary2018';
            $xero->user = 'mary@ideabubble.ie';
            $xero->key = 'XV07ZWPORKLGYCHZCDHQSJEEKHJX0J';
            $xero->secret = 'J7CPVFUFAHYRNIQRY4JKY0NWJOOIRM';

            print_r($xero->taxrates());*/

            //These are the minimum settings - for more options, refer to examples/config.php
            //echo realpath(dirname(__FILE__) . '/../../');exit;

            $settings = Settings::instance();
            $xero = new Model_Xero();
            //$xero->get_contacts(true);
            /*$acc = $xero->get_accounts();
            foreach ($acc as $a) {
                print_r($a);
            }*/

            $config = [
                'oauth' => [
                    'callback' => URL::site('/admin/xero/callback'),
                    'consumer_key' => $settings->get('xero_key'),
                    'consumer_secret' => $settings->get('xero_secret'),
                    'rsa_private_key' => $settings->get('xero_pkey'),
                ],
            ];
            $xero = new XeroPHP\Application\PrivateApplication($config);

            $mobile7 = substr('0831802859', -7);
            $mobile7 = '083180285x';
            //echo $mobile7;
            $r = $xero->load('Accounting\Contact')->where('Phones[3].PhoneNumber=="' . $mobile7 . '"')->execute();
            print_r($r);
            exit;

            $settings = Settings::instance();
            $config = [
                'oauth' => [
                    'callback' => URL::site('/admin/xero/callback'),
                    'consumer_key' => $settings->get('xero_key'),// 'XV07ZWPORKLGYCHZCDHQSJEEKHJX0J',
                    'consumer_secret' => $settings->get('xero_secret'), //'J7CPVFUFAHYRNIQRY4JKY0NWJOOIRM',
                    'rsa_private_key' => $settings->get('xero_pkey'), //'file://' . realpath(dirname(__FILE__) . '/../../xero_pri.pem'),
                ],
            ];
            $xero = new XeroPHP\Application\PrivateApplication($config);
            //$org = $xero->load('Accounting\\Organisation')->execute();
            //print_r($org[0]->getName());
            /*$acc = $xero->load('Accounting\Account')->execute();
            echo "\n" . $acc->count() . "\n";
            foreach ($acc as $a) {
                echo $a->getName() . "\n";
            }*/


            /*$cnew = new XeroPHP\Models\Accounting\Contact();
            $cnew->setName("mex");
            $xero->save($cnew);*/

            $conts = $xero->load('Accounting\Contact')->execute();
            //print_r($conts);
            foreach ($conts as $cont) {
                echo $cont->getName() . "\n";
            }
            //echo $org['Name'];
        } catch (Exception $exc) {
            print_r($exc);
        }
        exit;
    }

    public function action_sync_contacts()
    {
        $post = $this->request->post();
        if (@$post['clear'] == 'CLEAR') {
            Model_Remoteaccounting::sync_clear('Contact');
        } else {
            Model_Remoteaccounting::sync_contacts($post['direction'], $post['direction'] == 'REMOTE' ? $post['contacts']['id'] : $post['contacts']['remote_id']);

        }

        $this->request->redirect('/admin/remoteaccounting/contacts');

    }

    public function action_sync_transactions()
    {
        $post = $this->request->post();
        if (@$post['clear'] == 'CLEAR') {
            Model_Remoteaccounting::sync_clear('Transaction');
        } else {
            Model_Remoteaccounting::sync_transactions($post['direction'], $post['direction'] == 'REMOTE' ? $post['transactions']['id'] : $post['transactions']['remote_id']);
            if ($post['direction'] == 'REMOTE' && @$post['transactions']['id']) {
                $paymentq = DB::select('payments.*')
                    ->from(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'))
                    ->where('payments.deleted', '=', 0);
                if ($post['transactions']['id']) {
                    $paymentq->and_where('payments.transaction_id', 'in', $post['transactions']['id']);
                }
                $payments = $paymentq->execute()->as_array();
                $payment_ids = array();
                foreach ($payments as $payment) {
                    $payment_ids[] = $payment['id'];
                }
                if (count($payment_ids) > 0) {
                    Model_Remoteaccounting::sync_payments($post['direction'], $payment_ids);
                }
            }
        }
        $this->request->redirect('/admin/remoteaccounting/transactions');
    }

    public function action_sync_payments()
    {
        $post = $this->request->post();
        if (@$post['clear'] == 'CLEAR') {
            Model_Remoteaccounting::sync_clear('Payment');
        } else {
            Model_Remoteaccounting::sync_payments($post['direction'], $post['direction'] == 'REMOTE' ? $post['payments']['id'] : $post['payments']['remote_id']);
        }
        $this->request->redirect('/admin/remoteaccounting/payments');
    }

    public function action_test_aiq()
    {
        header('content-type: text/plain; charset=utf-8');
        $aiq = new Model_Accountsiq();
        $aiq->debug = true;
        if ($aiq->login()) {
            //print_r($aiq->queries);exit;
            print_R($aiq->getGLAccountList());print_r($aiq->queries);exit;

            //echo "login success";

            //$contacts = $aiq->get_contacts();
            //print_r($contacts);
            //print_r($aiq->get_contact('KM'));
            //$c = $aiq->get_contact('CC100118');
            //print_r($c->GetCustomerResult->Result);
            /*foreach ($contacts as $contact) {
                if (strpos($contact->Code, 'CC') === 0) {
                    $aiq->inactivate_contact($contact);
                }
            }*/
            //$aiq->inactivate_contact($c->GetCustomerResult->Result);
            //$aiq->sync_contacts('LOCAL');
            $tx = DB::select('*')
                ->from(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'))
                ->where('transactions.id', '=', 1)
                //->order_by('id', 'desc')
                ->limit(1)
                ->execute()
                ->current();

            //print_r($aiq->get_items());
            //print_R($aiq->get_active_items('CCBOOKING'));exit;
            $aiq->save_transaction($tx);
            //$schedule = Model_Schedules::get_one_for_details(1);
            $schedule = DB::select('schedules.*')
                ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                ->where('schedules.id', '=', 1)
                ->execute()
                ->current();

            /*$r = $aiq->save_schedule($schedule);*/
            //$r = $aiq->get_items();
            //print_R($r);
            //echo $aiq->get_last_request();
            //echo $aiq->get_last_response();
        } else {
            echo "login fail";
        }
        exit;
    }
}
