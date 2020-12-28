<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Remoteaccounting extends Model
{
    const TABLE_RCONTACTS = 'plugin_remoteaccounting_contacts';
    const TABLE_RTRANSACTIONS = 'plugin_remoteaccounting_transactions';
    const TABLE_RPAYMENTS = 'plugin_remoteaccounting_payments';

    protected static function init_api()
    {
        $settings = Settings::instance();
        $api_name = $settings->get('remoteaccounting_api');

        $api = null;
        if ($api_name == Model_Xero::API_NAME) {
            $api = new Model_Xero();
        } else if ($api_name == Model_Bigredcloud::API_NAME) {
            $api = new Model_Bigredcloud();
        } else if ($api_name == Model_Accountsiq::API_NAME) {
            $api = new Model_Accountsiq();
        } else {
            return false;
        }
        return $api;
    }

    public static function get_apis($selected = '')
    {
        return html::optionsFromArray(
            array(
                '' => '',
                Model_Xero::API_NAME => 'Xero',
                Model_Bigredcloud::API_NAME => 'Big Red Cloud',
                Model_Accountsiq::API_NAME => 'Accounts IQ'
            ),
            $selected
        );
    }

    public static function get_contacts()
    {
        $api = self::init_api();
        $contacts = $api->get_contacts();

        return $contacts;
    }

    public static function save_contact($contact)
    {
        $api = self::init_api();
        $api->save_contact($contact);

        return true;
    }

    public static function delete_contact($contact)
    {
        $api = self::init_api();
        $api->delete_contact($contact);
        return true;
    }

    public static function get_transactions()
    {
        $api = self::init_api();
        $transactions = $api->get_transactions();

        return $transactions;
    }

    public static function save_transaction($tx)
    {
        $api = self::init_api();
        $api->save_transaction($tx);

        return true;
    }

    public static function save_payment($payment)
    {
        $api = self::init_api();
        $api->save_payment($payment);

        return true;
    }

    public static function sync_contacts($direction = 'BOTH', $ids = null)
    {
        $api = self::init_api();
        $api->sync_contacts($direction, $ids);

        return true;
    }

    public static function sync_transactions($direction = 'BOTH', $ids = null)
    {
        $api = self::init_api();
        $api->sync_transactions($direction, $ids);

        return true;
    }

    public static function sync_payments($direction = 'BOTH', $ids = null)
    {
        $api = self::init_api();
        $api->sync_payments($direction, $ids);

        return true;
    }

    public static function contacts_datatable($params)
    {
        $settings = Settings::instance();
        $api_name = $settings->get('remoteaccounting_api');
        if (@$params['status'] == 'synced') {
            $select = DB::select(
                'contacts.id',
                DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as name"),
                'sync.remote_id',
                'sync.cms_id',
                'sync.synced'
            )
                ->from(array(Model_Remotesync::SYNC_TABLE, 'sync'))
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('sync.cms_id', '=', 'contacts.id')
                    ->on('sync.type', '=', DB::expr("'" . $api_name . '-Contact' . "'"));
            $select->where('contacts.delete', '=', 0);
            $select->order_by('contacts.first_name', 'asc');
            $select->order_by('contacts.last_name', 'asc');
            $contacts = $select->execute()->as_array();
        }
        if (@$params['status'] == 'local') {
            $select = DB::select(
                'contacts.id',
                DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as name"),
                'sync.remote_id',
                'sync.cms_id',
                'sync.synced'
            )
                ->from(array(Model_Remotesync::SYNC_TABLE, 'sync'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'right')
                ->on('sync.cms_id', '=', 'contacts.id')
                ->on('sync.type', '=', DB::expr("'" . $api_name . '-Contact' . "'"));
            $select->where('contacts.delete', '=', 0);
            $select->where('sync.remote_id', 'is', null);
            $select->order_by('contacts.first_name', 'asc');
            $select->order_by('contacts.last_name', 'asc');

            $contacts = $select->execute()->as_array();
        }
        if (@$params['status'] == 'remote') {
            $contacts = Model_Remoteaccounting::get_contacts();
            DB::query(null, "DROP TEMPORARY TABLE IF EXISTS remote_accounting_contacts")->execute();
            DB::query(null, "CREATE TEMPORARY TABLE remote_accounting_contacts (name VARCHAR(100), email VARCHAR(100), remote_id VARCHAR(100), KEY (remote_id), KEY(email))")->execute();
            foreach ($contacts as $contact) {
                DB::insert('remote_accounting_contacts')
                    ->values(
                        array(
                            'name' => $contact['name'],
                            'email' => $contact['email'],
                            'remote_id' => $contact['remote_id']
                        )
                    )->execute();
            }

            $select = DB::select(
                DB::expr("null as id"),
                'contacts.name',
                'contacts.remote_id',
                'sync.cms_id',
                'sync.synced'
            )
                ->from(array(Model_Remotesync::SYNC_TABLE, 'sync'))
                ->join(array('remote_accounting_contacts', 'contacts'), 'right')
                ->on('sync.remote_id', '=', 'contacts.remote_id')
                ->on('sync.type', '=', DB::expr("'" . $api_name . '-Contact' . "'"));
            $select->where('sync.cms_id', 'is', null);
            $select->order_by('contacts.name', 'asc');

            $contacts = $select->execute()->as_array();
        }

        return $contacts;
    }

    public static function transactions_datatable($params)
    {
        $settings = Settings::instance();
        $api_name = $settings->get('remoteaccounting_api');
        if (@$params['status'] == 'synced') {
            $select = DB::select(
                'transactions.id',
                'transactions.total',
                'transactions.contact_id',
                DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as name"),
                'sync.remote_id',
                'sync.cms_id',
                'sync.synced'
            )
                ->from(array(Model_Remotesync::SYNC_TABLE, 'sync'))
                ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                    ->on('sync.cms_id', '=', 'transactions.id')
                    ->on('sync.type', '=', DB::expr("'" . $api_name . '-Transaction' . "'"))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('transactions.contact_id', '=', 'contacts.id');

            $select->where('transactions.deleted', '=', 0);
            $select->order_by('transactions.created', 'desc');
            $transactions = $select->execute()->as_array();
        }
        if (@$params['status'] == 'local') {
            $select = DB::select(
                'transactions.id',
                'transactions.total',
                'transactions.contact_id',
                DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as name"),
                'sync.remote_id',
                'sync.cms_id',
                'sync.synced'
            )
                ->from(array(Model_Remotesync::SYNC_TABLE, 'sync'))
                ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'right')
                    ->on('sync.cms_id', '=', 'transactions.id')
                    ->on('sync.type', '=', DB::expr("'" . $api_name . '-Transaction' . "'"))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('transactions.contact_id', '=', 'contacts.id');

            $select->where('transactions.deleted', '=', 0);
            $select->where('sync.remote_id', 'is', null);
            $select->order_by('transactions.created', 'desc');
            $transactions = $select->execute()->as_array();
        }
        if (@$params['status'] == 'remote') {
            $transactions = Model_Remoteaccounting::get_transactions();
            //header('content-type: text/plain');print_R($transactions);exit;
            DB::query(null, "DROP TEMPORARY TABLE IF EXISTS remote_accounting_transactions")->execute();
            DB::query(null, "CREATE TEMPORARY TABLE remote_accounting_transactions (remote_id VARCHAR(100), remote_contact_id VARCHAR(100), amount DECIMAL(10,2), total DECIMAL(10,2), KEY(remote_id)) CHARSET=UTF8 COLLATE=utf8_general_ci")->execute();
            foreach ($transactions as $transaction) {
                DB::insert('remote_accounting_transactions')
                    ->values(
                        array(
                            'remote_contact_id' => $transaction['remote_contact_id'],
                            'amount' => $transaction['amount'],
                            'total' => $transaction['total'],
                            'remote_id' => $transaction['remote_id']
                        )
                    )->execute();
            }

            $select = DB::select(
                DB::expr("null as id"),
                'transactions.remote_contact_id',
                'transactions.remote_id',
                'transactions.amount',
                'transactions.total',
                'sync.cms_id',
                'sync.synced',
                DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as name")
            )
                ->from(array('remote_accounting_transactions', 'transactions'))
                ->join(array(Model_Remotesync::SYNC_TABLE, 'sync'), 'left')
                    ->on('sync.remote_id', '=', 'transactions.remote_id')
                    ->on('sync.type', '=', DB::expr("'" . $api_name . '-Transaction' . "'"))
                ->join(array(Model_Remotesync::SYNC_TABLE, 'sync_contacts'), 'left')
                    ->on('transactions.remote_contact_id', '=', 'sync_contacts.remote_id')
                    ->on('sync_contacts.type', '=', DB::expr("'" . $api_name . '-Contact' . "'"))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                    ->on('sync_contacts.cms_id', '=', 'contacts.id')
                    ->on('sync_contacts.type', '=', DB::expr("'" . $api_name . '-Contact' . "'"));
            $select->where('sync.cms_id', 'is', null);
            $select->order_by('transactions.remote_id', 'desc');

            $transactions = $select->execute()->as_array();
        }

        return $transactions;
    }

    public static function payments_datatable($params)
    {
        $settings = Settings::instance();
        $api_name = $settings->get('remoteaccounting_api');
        if (@$params['status'] == 'synced') {
            $select = DB::select(
                'payments.id',
                'payments.amount',
                'transactions.contact_id',
                'payments.type',
                DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as name"),
                'sync.remote_id',
                'sync.cms_id',
                'sync.synced'
            )
                ->from(array(Model_Remotesync::SYNC_TABLE, 'sync'))
                    ->join(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'), 'inner')
                        ->on('sync.cms_id', '=', 'payments.id')
                        ->on('sync.type', '=', DB::expr("'" . $api_name . '-Payment' . "'"))
                    ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                        ->on('payments.transaction_id', '=', 'transactions.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                        ->on('transactions.contact_id', '=', 'contacts.id');

            $select->where('payments.deleted', '=', 0);
            $select->order_by('payments.created', 'desc');
            $transactions = $select->execute()->as_array();
        }
        if (@$params['status'] == 'local') {
            $select = DB::select(
                'payments.id',
                'payments.amount',
                'transactions.contact_id',
                'payments.type',
                DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as name"),
                'sync.remote_id',
                'sync.cms_id',
                'sync.synced'
            )
                ->from(array(Model_Remotesync::SYNC_TABLE, 'sync'))
                    ->join(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'), 'right')
                        ->on('sync.cms_id', '=', 'payments.id')
                        ->on('sync.type', '=', DB::expr("'" . $api_name . '-Payment' . "'"))
                    ->join(array(Model_Kes_Transaction::TRANSACTION_TABLE, 'transactions'), 'inner')
                        ->on('payments.transaction_id', '=', 'transactions.id')
                    ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                        ->on('transactions.contact_id', '=', 'contacts.id');

            $select->where('payments.deleted', '=', 0);
            $select->where('sync.remote_id', 'is', null);
            $select->order_by('payments.created', 'desc');
            $transactions = $select->execute()->as_array();
        }
        if (@$params['status'] == 'remote') {
            $transactions = Model_Remoteaccounting::get_transactions();
            //header('content-type: text/plain');print_R($transactions);exit;
            DB::query(null, "DROP TEMPORARY TABLE IF EXISTS remote_accounting_transactions")->execute();
            DB::query(null, "CREATE TEMPORARY TABLE remote_accounting_transactions (remote_id VARCHAR(100), remote_contact_id VARCHAR(100), amount DECIMAL(10,2), total DECIMAL(10,2), KEY(remote_id))")->execute();
            foreach ($transactions as $transaction) {
                DB::insert('remote_accounting_transactions')
                    ->values(
                        array(
                            'remote_contact_id' => $transaction['remote_contact_id'],
                            'amount' => $transaction['amount'],
                            'total' => $transaction['total'],
                            'remote_id' => $transaction['remote_id']
                        )
                    )->execute();
            }

            $select = DB::select(
                DB::expr("null as id"),
                'transactions.remote_contact_id',
                'transactions.remote_id',
                'transactions.amount',
                'transactions.total',
                'sync.cms_id',
                'sync.synced',
                DB::expr("CONCAT_WS(' ', contacts.first_name, contacts.last_name) as name")
            )
                ->from(array('remote_accounting_transactions', 'transactions'))
                ->join(array(Model_Remotesync::SYNC_TABLE, 'sync'), 'left')
                ->on('sync.remote_id', '=', 'transactions.remote_id')
                ->on('sync.type', '=', DB::expr("'" . $api_name . '-Transaction' . "'"))
                ->join(array(Model_Remotesync::SYNC_TABLE, 'sync_contacts'), 'left')
                ->on('transactions.remote_contact_id', '=', 'sync_contacts.remote_id')
                ->on('sync_contacts.type', '=', DB::expr("'" . $api_name . '-Contact' . "'"))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
                ->on('sync_contacts.cms_id', '=', 'contacts.id')
                ->on('sync_contacts.type', '=', DB::expr("'" . $api_name . '-Contact' . "'"));
            $select->where('sync.cms_id', 'is', null);
            $select->order_by('transactions.remote_id', 'desc');

            $transactions = $select->execute()->as_array();
        }

        return $transactions;
    }

    public static function sync_clear($type)
    {
        $rs = new Model_Remotesync();
        $rs->clear(Settings::instance()->get('remoteaccounting_api') . '-' . $type);
    }
}

