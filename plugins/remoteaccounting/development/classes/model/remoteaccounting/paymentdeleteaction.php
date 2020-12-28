<?php

class Model_Remoteaccounting_PaymentDeleteAction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Remote Accounting Delete Payment';
        $this->purpose = Model_Automations::PURPOSE_DELETE;
        $this->params = array('payment_id');
    }

    public function run($params = array())
    {
        if (Settings::instance()->get('remoteaccounting_api') != '') {
            $ac = new Model_Remoteaccounting();
        }
    }
}