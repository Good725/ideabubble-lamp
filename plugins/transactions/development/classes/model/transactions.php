<?php defined('SYSPATH') or die('No direct script access.');

class Model_Transactions extends Model
{
    const TABLE_TYPES = 'plugin_transactions_types';
    const TABLE_TRANSACTIONS = 'plugin_transactions_transactions';
    const TABLE_OUTSTANDINGS = 'plugin_transactions_transactions_outstanding';
    const TABLE_HISTORY = 'plugin_transactions_history';


    public static function get_type($type_id)
    {
        $transaction_type = DB::select('transaction_type')
            ->from(self::TABLE_TYPES)
            ->where('id', '=', $type_id)
            ->order_by('deleted', 'asc')
            ->limit(1)
            ->execute()
            ->get('transaction_type');

        return $transaction_type;
    }

    public static function get_type_id($type)
    {
        $id = DB::select('id')
            ->from(self::TABLE_TYPES)
            ->where('transaction_type', '=', $type)
            ->order_by('deleted', 'asc')
            ->limit(1)
            ->execute()
            ->get('id');

        return $id;
    }

    public static function save($transaction, $user = null)
    {
        try {
            Database::instance()->begin();

            if ($user == null) {
                $user = auth::instance()->get_user();
            }

            self::save_transaction($transaction, $user);
            self::save_history($transaction);

            Database::instance()->commit();

            return $transaction;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    protected static function save_transaction(&$transaction, $user)
    {
        $transaction['updated'] = date::now();
        $transaction['updated_by'] = $user['id'];
        if (!is_numeric(@$transaction['id'])) {
            $transaction['created'] = $transaction['updated'];
            $transaction['created_by'] = $transaction['updated_by'];
        }

        if (!isset($transaction["type_id"]) && isset($transaction["type"])) {
            $transaction["type_id"] = self::get_type_id($transaction["type"]);
        }

        $data = arr::set(
            $transaction,
            "id",
            "type_id",
            "journalled_transaction_id",
            "reason",
            "currency",
            "amount",
            "discount",
            "total",
            "due",
            "contact_id",
            "family_id",
            "user_id",
            "status",
            "created",
            "updated",
            "created_by",
            "updated_by",
            "deleted"
        );

        if (is_numeric(@$transaction['id'])) {
            DB::update(self::TABLE_TRANSACTIONS)->set($data)->where('id', '=', $transaction['id'])->execute();
        } else {
            $inserted = DB::insert(self::TABLE_TRANSACTIONS)->values($data)->execute();
            $transaction['id'] = $inserted[0];

            DB::insert(self::TABLE_OUTSTANDINGS)
                ->values(array('transaction_id' => $transaction['id'], 'outstanding' => $transaction['total']))
                ->execute();
        }
    }

    protected static function save_history($transaction)
    {
        $result = DB::insert(self::TABLE_HISTORY)
            ->values(array(
                'transaction_id' => $transaction['id'],
                'saved' => date('Y-m-d H:i:s'),
                'data' => serialize($transaction)
            ))->execute();
        return $result[0];
    }

    public static function update_outstanding($transaction_id)
    {
        $transaction = DB::select('*')
            ->from(self::TABLE_TRANSACTIONS)
            ->where('id', '=', $transaction_id)
            ->execute()
            ->current();

        if ($transaction['status'] == 'Cancelled' || $transaction['status'] == 'Completed') {
            $outstanding = 0;
        } else {
            $paid = DB::select(DB::expr("SUM(payments.amount * IF(types.income = 0, IF(to_transaction_id = $transaction_id, 1, -1), types.income)) AS paid"))
                ->from(array(Model_TransactionPayments::TABLE_PAYMENTS, 'payments'))
                ->join(array(Model_TransactionPayments::TABLE_TYPES, 'types'), 'inner')
                ->on('payments.paymenttype_id', '=', 'types.id')
                ->where('payments.deleted', '=', 0)
                ->and_where('payments.status', '=', 'Completed')
                ->and_where_open()
                ->or_where('payments.to_transaction_id', '=', $transaction_id)
                ->or_where('payments.from_transaction_id', '=', $transaction_id)
                ->and_where_close()
                ->execute()
                ->get('paid');
            $outstanding = $transaction['total'] - $paid;
        }

        if (is_numeric($outstanding)) {
            DB::update(self::TABLE_OUTSTANDINGS)
                ->set(array('outstanding' => $outstanding))
                ->where('transaction_id', '=', $transaction_id)
                ->execute();
            if ($outstanding == 0) {
                DB::update(self::TABLE_TRANSACTIONS)
                    ->set(array('status' => 'Completed', 'updated' => date::now()))
                    ->where('id', '=', $transaction_id)
                    ->execute();
            }
        }
        return $outstanding;
    }

    public static function cancel_transaction($id, $user = null, $clear_outstanding = true)
    {
        if ($user == null) {
            $user = auth::instance()->get_user();
        }

        $data = array(
            'status' => 'Cancelled',
            'updated' => date::now(),
            'updated_by' => $user['id']
        );
        DB::update(self::TABLE_TRANSACTIONS)
            ->set($data)
            ->where('id', '=', $id)
            ->execute();

        if ($clear_outstanding) {
            DB::update(self::TABLE_OUTSTANDINGS)
                ->set(
                    array(
                    'outstanding' => 0
                    )
                )
                ->where('transaction_id', '=', $id)
                ->execute();
        }
        $data['id'] = $id;

        self::save_history($data);
    }

    public static function search($params)
    {
        $selectq = DB::select(
            'tx.*',
            'types.transaction_type',
            'types.income',
            'outstanding.outstanding',
            DB::expr("CONCAT_WS(' ', contacts.title, contacts.first_name, contacts.last_name) AS contact"),
            "families.family",
            DB::expr("CONCAT_WS(' ', users.name, users.surname) AS username")
        )
            ->from(array(self::TABLE_TRANSACTIONS, 'tx'))
                ->join(array(self::TABLE_TYPES, 'types'), 'inner')
                    ->on('tx.type_id', '=', 'types.id')
                ->join(array(self::TABLE_OUTSTANDINGS, 'outstanding'), 'left')
                    ->on('tx.id', '=', 'outstanding.transaction_id')
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'left')
                    ->on('tx.contact_id', '=', 'contacts.id')
                ->join(array(Model_Families::TABLE, 'families'), 'left')
                    ->on('tx.family_id', '=', 'families.id')
                ->join(array(Model_Users::MAIN_TABLE, 'users'), 'left')
                    ->on('tx.user_id', '=', 'users.id')
            ->where('tx.deleted', '=', 0);

        if (isset($params['transaction_id'])) {
            $selectq->and_where('tx.id', '=', $params['transaction_id']);
        }

        if (isset($params['contact_id'])) {
            $selectq->and_where('tx.contact_id', '=', $params['contact_id']);
        }

        if (isset($params['family_id'])) {
            $contactsfilterfamq = DB::select('contact_id')
                ->from(array(Model_Families_Members::TABLE, 'members'))
                ->where('members.family_id', '=', $params['family_id']);
            $selectq->and_where_open();
                $selectq->or_where('tx.family_id', '=', $params['family_id']);
                $selectq->or_where('tx.contact_id', 'in', $contactsfilterfamq);
            $selectq->and_where_close();
        }

        if (isset($params['user_id'])) {
            $selectq->and_where_open();
                $selectq->or_where('tx.user_id', '=', $params['user_id']);
                $selectq->or_where_open();
                Model_Contacts::limited_user_access_filter($selectq, $params['user_id'], 'tx.contact_id');
                $selectq->or_where_close();
            $selectq->and_where_close();
        }

        if (@$params['limit'] > 0) {
            $selectq->limit($params['limit']);
        }

        if (@$params['offset'] > 0) {
            $selectq->offset($params['offset']);
        }

        $selectq->order_by('tx.updated', 'desc');

        $transactions = $selectq->execute()->as_array();

        return $transactions;
    }

    public static function load($id)
    {
        $transaction = current(self::search(array('transaction_id' => $id)));

        if ($transaction) {
            $transaction['payments'] = Model_TransactionPayments::search(array('transaction_id' => $id));
        }

        return $transaction;
    }

    public static function test_has_changed($id, $updated)
    {
        $same = DB::select('updated')
            ->from(self::TABLE_TRANSACTIONS)
            ->where('id', '=', $id)
            ->and_where('updated', '=', $updated)
            ->execute()
            ->get('id');
        if ($same) {
            return false;
        }

        return true;
    }
}