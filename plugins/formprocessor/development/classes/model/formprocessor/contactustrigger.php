<?php

class Model_Formprocessor_Contactustrigger extends Model_Automations_Trigger
{
    const NAME = 'Contact Us';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('contact_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }
}
