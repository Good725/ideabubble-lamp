<?php

class Model_Bookings_Tuapplicationsubmittedtrigger extends Model_Bookings_Adminbookingcreatetrigger
{
    const NAME = 'an Application is submitted';
    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;
        $this->params = array('application_id');
    }
}
