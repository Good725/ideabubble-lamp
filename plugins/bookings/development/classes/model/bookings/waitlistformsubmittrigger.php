<?php

class Model_Bookings_Waitlistformsubmittrigger extends Model_Automations_Trigger
{
    const NAME = 'the waitlist form is submitted';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('contact_id', 'course_id', 'schedule_id');
        $this->purpose = Model_Automations::PURPOSE_SAVE;
    }
}
