<?php defined('SYSPATH') or die('No direct script access.');

class Model_Booking_Status extends ORM
{
    protected $_table_name = 'plugin_ib_educate_bookings_status';
    protected $_primary_key = 'status_id';
    protected $_deleted_column = 'delete';
}