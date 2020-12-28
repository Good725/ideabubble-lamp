<?php defined('SYSPATH') or die('No Direct Script Access.');

class PaymentGatewayRealexHandler implements  PaymentGatewayHandler
{
    public function name()
    {
        return 'realex';
    }

    public function title()
    {
        return 'Credit Card via Realex';
    }

    public function is_ready()
    {
        if (Settings::instance()->get('realex_username') != '' &&
            Settings::instance()->get('realex_secret_key') != '' &&
            Settings::instance()->get('realex_mode') != '' &&
            Settings::instance()->get('enable_realex') == '1') {
            return true;
        }

        return false;
    }

    public function allow_for_limited_permissions()
    {
        return true;
    }

    public function process($payment, $post)
    {
        $order_id = $payment['to_transaction_id'] . '-' . $payment['id'];
        if (Kohana::$environment != Kohana::PRODUCTION) {
            $order_id .= '-' . Kohana::$environment;
        }
        $realvault = new Model_Realvault();
        $response = $realvault->charge(
            $order_id,
            $payment['amount'],
            $payment['currency'],
            $post['ccNum'],
            $post['ccExpMM'] . $post['ccExpYY'],
            $post['ccType'],
            $post['ccName'],
            $post['ccv']
        );
        if ((string)$response->result == '00') {
            $payment['status'] = 'Completed';
            $payment['gateway_tx_reference'] = 'authcode:' . $response->authcode . ';reference:' . $response->pasref;
            $payment = Model_TransactionPayments::save($payment);
        } else {
            $payment['status'] = 'Cancelled';
            $payment['gateway_tx_reference'] = 'result:' . $response->result. ';message:' . $response->message;
            $payment = Model_TransactionPayments::save($payment);
            $payment['message'] = $response->message . '(' . $response->result . ')';
        }
        return $payment;
    }

    public function refund()
    {

    }

    public function get_inputs()
    {
        return View::factory('realex_fields');
    }
}
