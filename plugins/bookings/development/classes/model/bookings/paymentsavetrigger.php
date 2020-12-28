<?php

class Model_Bookings_Paymentsavetrigger extends Model_Automations_Trigger
{
    const NAME = 'Booking Payment Save';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('payment_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }
}
