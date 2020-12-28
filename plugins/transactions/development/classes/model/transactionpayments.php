<?php defined('SYSPATH') or die('No direct script access.');

require_once dirname(__FILE__) . '/paymentgatewayhandler.php';

class Model_TransactionPayments extends Model
{
    const TABLE_TYPES = 'plugin_transactions_paymenttypes';
    const TABLE_PAYMENTS = 'plugin_transactions_payments';
    const TABLE_GATEWAYS = 'plugin_transactions_gateways';

    protected static $gateway_handlers = null;

    public static function get_type($type_id)
    {
        $payment_type = DB::select('payment_type')
            ->from(self::TABLE_TYPES)
            ->where('id', '=', $type_id)
            ->order_by('deleted', 'asc')
            ->limit(1)
            ->execute()
            ->get('payment_type');

        return $payment_type;
    }

    public static function get_type_id($type)
    {
        $id = DB::select('id')
            ->from(self::TABLE_TYPES)
            ->where('payment_type', '=', $type)
            ->order_by('deleted', 'asc')
            ->limit(1)
            ->execute()
            ->get('id');

        return $id;
    }

    public static function get_gateway_id($getway)
    {
        $id = DB::select('id')
            ->from(self::TABLE_GATEWAYS)
            ->where('gateway', '=', $getway)
            ->order_by('deleted', 'asc')
            ->limit(1)
            ->execute()
            ->get('id');

        return $id;
    }

    public static function save($payment, $user = null)
    {
        try {
            Database::instance()->begin();

            if ($user == null) {
                $user = auth::instance()->get_user();
            }

            $payment['updated'] = date::now();
            $payment['updated_by'] = $user['id'];
            if (!is_numeric(@$payment['id'])) {
                $payment['created'] = $payment['updated'];
                $payment['created_by'] = $payment['updated_by'];
            }

            if (!isset($payment["paymenttype_id"]) && isset($payment["type"])) {
                $payment["paymenttype_id"] = self::get_type_id($payment["type"]);
            }

            if (!isset($payment["gateway_id"]) && isset($payment["gateway"])) {
                $payment["gateway_id"] = self::get_gateway_id($payment["gateway"]);
            }

            $data = arr::set(
                $payment,
                "id",
                "to_transaction_id",
                "from_transaction_id",
                "paymenttype_id",
                "journalled_payment_id",
                "currency",
                "currency_rate",
                "amount",
                "gateway_id",
                "gateway_tx_reference",
                "gateway_fee",
                "gateway_fee_included",
                "status",
                "created",
                "updated",
                "created_by",
                "updated_by",
                "deleted"
            );

            if (is_numeric(@$payment['id'])) {
                DB::update(self::TABLE_PAYMENTS)->set($data)->where('id', '=', $payment['id'])->execute();
            } else {
                $inserted = DB::insert(self::TABLE_PAYMENTS)->values($data)->execute();
                $payment['id'] = $inserted[0];
            }

            if (@$payment['to_transaction_id']) {
                Model_Transactions::update_outstanding($payment['to_transaction_id']);
            }
            if (@$payment['from_transaction_id']) {
                Model_Transactions::update_outstanding($payment['from_transaction_id']);
            }

            Database::instance()->commit();

            return $payment;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function search($params)
    {
        $selectq = DB::select(
            'pays.*',
            'ptypes.payment_type',
            'gateways.gateway',
            DB::expr("GROUP_CONCAT(notes.note) AS `note`")
        )
            ->from(array(self::TABLE_PAYMENTS, 'pays'))
                ->join(array(self::TABLE_TYPES, 'ptypes'), 'inner')
                    ->on('pays.paymenttype_id', '=', 'ptypes.id')
                ->join(array(self::TABLE_GATEWAYS, 'gateways'), 'inner')
                    ->on('pays.gateway_id', '=', 'gateways.id')
            ->where('pays.deleted', '=', 0);
        Model_Notes::reference_join($selectq, 'Payment', 'pays', 'id');

        if (isset($params['to_transaction_id'])) {
            $selectq->and_where('pays.to_transaction_id', '=', $params['to_transaction_id']);
        }

        if (isset($params['from_transaction_id'])) {
            $selectq->and_where('pays.from_transaction_id', '=', $params['from_transaction_id']);
        }

        if (isset($params['transaction_id'])) {
            $selectq->and_where_open()
                ->or_where('pays.from_transaction_id', '=', $params['transaction_id'])
                ->or_where('pays.to_transaction_id', '=', $params['transaction_id'])
                ->and_where_close();

        }

        if (@$params['limit'] > 0) {
            $selectq->limit($params['limit']);
        }

        if (@$params['offset'] > 0) {
            $selectq->offset($params['offset']);
        }

        $selectq->order_by('pays.updated', 'desc');
        $selectq->group_by('pays.id');

        $payments = $selectq->execute()->as_array();
        return $payments;
    }

    public static function get_gateway_handlers()
    {
        if (self::$gateway_handlers === null) {
            $files = scandir(dirname(__FILE__));
            $transactions_limited_access = Auth::instance()->has_access('transactions_limited_access');
            $transactions_unlimited_access = Auth::instance()->has_access('transactions');
            foreach ($files as $file) {
                if (preg_match('/paymentgateway(.+?)handler\.php/', $file, $match)) {
                    require_once dirname(__FILE__) . '/' . $file;
                    $handlername = 'PaymentGateway' . ucfirst($match[1]) . 'Handler';
                    $handler = new $handlername();
                    if ($transactions_unlimited_access || ($handler->allow_for_limited_permissions()  && $transactions_limited_access)) {
                        self::$gateway_handlers[$handler->name()] = $handler;
                    }
                }
            }
        }
        return self::$gateway_handlers;
    }

    public static function get_gateway_handler($name)
    {
        $handlers = self::get_gateway_handlers();
        return $handlers[$name];
    }
}

