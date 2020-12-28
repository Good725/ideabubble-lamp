<?php

class Model_Navapi_Bookingcreateaction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Navision Create Booking';
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('booking_id');
    }

    public function run($params = array())
    {
        $this->message = null;
        try {
            $na = new Model_NAVAPI();
            $result = $na->create_booking($params['booking_id']);
            if ($result === null) {
                $this->message = __("Navision booking not created for booking " . $params['booking_id']);
            }
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }
}