<?php

class Model_Todos_TaskDueTrigger extends Model_Todos_TaskAssignedTrigger
{
    const NAME = 'a Task is due';
    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;
        $this->initiator = Model_Automations_Trigger::INITIATOR_CRON;
        $this->multiple_results = true;
    }
}