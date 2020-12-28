<?php

class Model_Courses_Coursesavetrigger extends Model_Automations_Trigger
{
    const NAME = 'Course Save';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('course_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }
}
