<?php

class Model_Bookings_Frontendquotecreatetrigger extends Model_Bookings_Adminbookingcreatetrigger
{
    const NAME = 'a Website Quote occurs';

    public function __construct()
    {
        parent::__construct();
        $this->name = self::NAME;
    }
}
