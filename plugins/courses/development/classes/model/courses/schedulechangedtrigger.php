<?php

class Model_Courses_Schedulechangedtrigger extends Model_Automations_Trigger
{
    const NAME = 'a Schedule is changed';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('schedule_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }
}
