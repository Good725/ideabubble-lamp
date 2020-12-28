<?php

class Model_Bookings_Checkouttrigger extends Model_Automations_Trigger
{
    const NAME = 'Booking Checkout';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('contact_id', 'transaction_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->filters = array(
        );
        $this->generated_message_params = array(
        );
    }
}
