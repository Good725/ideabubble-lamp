<?php

class Model_Navapi_Paymentcreateaction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Navision Create Payment';
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('payment_id');
    }

    public function run($params = array())
    {
        $this->message = null;
        try {
            $na = new Model_NAVAPI();
            $result = $na->create_payment($params['payment_id']);
            if ($result === null) {
                $this->message = __("Navision payment not created for payment " . $params['payment_id']);
            }
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }
}