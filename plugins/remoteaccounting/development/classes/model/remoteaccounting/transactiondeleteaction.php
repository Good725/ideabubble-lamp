<?php

class Model_Remoteaccounting_TransactionDeleteAction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Remote Accounting Delete Transaction';
        $this->purpose = Model_Automations::PURPOSE_DELETE;
        $this->params = array('transaction_id');
    }

    public function run($params = array())
    {
        if (Settings::instance()->get('remoteaccounting_api') != '') {
            $ac = new Model_Remoteaccounting();
        }
    }
}