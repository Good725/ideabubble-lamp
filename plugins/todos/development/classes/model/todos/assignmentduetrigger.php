<?php

class Model_Todos_AssignmentDueTrigger extends Model_Todos_TaskDueTrigger
{
    const NAME = 'an Assignment is due';
    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;
    }
}
