<?php

class Model_Bookings_Applicationnotcompletetrigger extends Model_Automations_Trigger
{
    const NAME = 'an application is not completed';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('booking_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->initiator = Model_Automations_Trigger::INITIATOR_CRON;
    }
}
