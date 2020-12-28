<?php

class Model_Survey_Surveynotcompletetrigger extends Model_Automations_Trigger
{
    const NAME = 'a survey is not completed';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('booking_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->initiator = Model_Automations_Trigger::INITIATOR_CRON;
    }
}
