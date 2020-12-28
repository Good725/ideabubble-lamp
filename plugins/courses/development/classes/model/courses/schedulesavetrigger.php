<?php

class Model_Courses_Schedulesavetrigger extends Model_Automations_Trigger
{
    const NAME = 'Schedule Save';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('schedule_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }
}
