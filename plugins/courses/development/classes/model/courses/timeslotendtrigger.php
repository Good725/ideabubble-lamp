<?php

class Model_Courses_Timeslotendtrigger extends Model_Courses_Timeslotstarttrigger
{
    const NAME = 'a Session ends';
    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;
    }
}
