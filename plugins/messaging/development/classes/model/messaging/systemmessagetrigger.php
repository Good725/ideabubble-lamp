<?php

class Model_Messaging_Systemmessagetrigger extends Model_Automations_Trigger
{
    const NAME = 'a Generic system message';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array();
        $this->purpose = Model_Automations::PURPOSE_SAVE;

        $this->generated_message_params = array(
            '@message@',
            '@link@',
            '@type@'
        );
    }
}
