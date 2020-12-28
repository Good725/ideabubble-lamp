<?php

class Model_Bookings_Adminquotecreatetrigger extends Model_Bookings_Adminbookingcreatetrigger
{
    const NAME = 'a back-office quote occurs';

    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;
    }

}
