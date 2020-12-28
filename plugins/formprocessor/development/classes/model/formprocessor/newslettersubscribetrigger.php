<?php

class Model_Formprocessor_Newslettersubscribetrigger extends Model_Automations_Trigger
{
    const NAME = 'Newsletter Subscribe';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('contact_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }

    public function filter($data, $sequence)
    {
        return array($data);
    }
}
