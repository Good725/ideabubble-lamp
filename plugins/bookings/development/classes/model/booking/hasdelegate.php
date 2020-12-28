<?php defined('SYSPATH') or die('No direct script access.');

class Model_Booking_HasDelegate extends ORM
{
    protected $_table_name = 'plugin_ib_educate_bookings_has_delegates';

    protected $_belongs_to = [
        'booking'  => ['model' => 'Booking_Booking',   'foreign_key' => 'booking_id'],
        'delegate' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'contact_id'],
    ];
}