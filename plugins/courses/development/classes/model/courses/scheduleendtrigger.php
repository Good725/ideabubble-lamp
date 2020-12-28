<?php

class Model_Courses_Scheduleendtrigger extends Model_Courses_Schedulestarttrigger
{
    const NAME = 'a Course ends';
    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;
    }
}
