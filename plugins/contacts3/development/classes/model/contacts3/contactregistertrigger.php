<?php

class Model_Contacts3_Contactregistertrigger extends Model_Automations_Trigger
{
    const NAME = 'Frontend Register';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('contact_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }
}
