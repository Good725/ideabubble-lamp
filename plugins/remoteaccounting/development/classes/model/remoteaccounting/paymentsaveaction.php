<?php

class Model_Remoteaccounting_PaymentSaveAction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Remote Accounting Save Payment';
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('payment_id');
    }

    public function run($params = array())
    {
        try {
            if (Settings::instance()->get('remoteaccounting_api') != '') {
                $ac = new Model_Remoteaccounting();
                $payment = DB::select('payments.*', 'cheque_details.name_cheque')
                    ->from(array(Model_Kes_Payment::PAYMENT_TABLE, 'payments'))
                    ->join(array('plugin_bookings_transactions_payments_cheque', 'cheque_details'), 'left')
                    ->on('payments.id', '=', 'cheque_details.payment_id')
                    ->where('payments.deleted', '=', 0)
                    ->and_where('payments.id', '=', $params['payment_id'])
                    ->execute()
                    ->current();
                if ($payment) {
                    $ac->save_payment($payment);
                }
            }
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }
}