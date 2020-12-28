<?php

class Model_Bookings_Transactiondeletetrigger extends Model_Automations_Trigger
{
    const NAME = 'Booking Transaction Delete';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('transaction_id');
        $this->purpose = Model_Automations::PURPOSE_DELETE;
    }
}
