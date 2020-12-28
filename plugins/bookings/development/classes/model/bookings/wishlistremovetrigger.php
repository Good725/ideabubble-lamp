<?php

class Model_Bookings_Wishlistremovetrigger extends Model_Automations_Trigger
{
    const NAME = 'Wishlist Remove';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->params = array('contact_id', 'course_id', 'schedule_id');
        $this->purpose = Model_Automations::PURPOSE_DELETE;
    }
}
