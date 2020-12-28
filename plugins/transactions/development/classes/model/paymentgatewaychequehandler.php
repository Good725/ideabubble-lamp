<?php defined('SYSPATH') or die('No Direct Script Access.');

class PaymentGatewayChequeHandler implements  PaymentGatewayHandler
{
    public function name()
    {
        return 'cheque';
    }

    public function title()
    {
        return 'Cheque';
    }

    public function is_ready()
    {
        return true;
    }

    public function allow_for_limited_permissions()
    {
        return false;
    }

    public function process($payment, $post)
    {
        $payment['status'] = 'Completed';
        Model_TransactionPayments::save($payment);
        return $payment;
    }

    public function refund()
    {
        return array(
            'status' => 'Completed'
        );
    }

    public function get_inputs()
    {
        return '';
    }
}
