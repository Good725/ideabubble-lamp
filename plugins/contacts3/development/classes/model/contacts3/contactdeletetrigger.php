<?php

class Model_Contacts3_Contactdeletetrigger extends Model_Automations_Trigger
{
    const NAME = 'Contact Delete';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('contact_id');
        $this->purpose = Model_Automations::PURPOSE_DELETE;
    }
}
