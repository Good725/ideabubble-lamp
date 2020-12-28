<?php

class Model_Navapi_Transactioncreateaction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Navision Create Transaction';
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('transaction_id');
    }

    public function run($params = array())
    {
        $this->message = null;
        try {
            $na = new Model_NAVAPI();
            $result = $na->create_transaction($params['transaction_id']);
            if ($result === null) {
                $this->message = __("Navision transaction not created for transaction" . $params['transaction_id']);
            }
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }
}