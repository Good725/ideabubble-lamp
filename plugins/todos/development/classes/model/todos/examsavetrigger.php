<?php

class Model_Todos_ExamSaveTrigger extends Model_Automations_Trigger
{
    const NAME = 'Todo Assesment Save';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('todo_id');
    }
}