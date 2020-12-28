<?php

class Model_Bookings_Frontendbookingcreatetrigger extends Model_Bookings_Adminbookingcreatetrigger
{
    const NAME = 'a Website booking occurs';
    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;
        $this->params = array('booking_id');
    }
}
