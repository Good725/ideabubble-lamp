<?php

class Model_Bookings_Transactionsavetrigger extends Model_Automations_Trigger
{
    const NAME = 'Booking Transaction Save';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('transaction_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->filters = array(
        );
        $this->generated_message_params = array(
        );
    }
}
