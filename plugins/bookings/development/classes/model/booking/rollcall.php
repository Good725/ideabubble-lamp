<?php defined('SYSPATH') or die('No direct script access.');

class Model_Booking_Rollcall extends ORM
{
    protected $_table_name = 'plugin_ib_educate_bookings_rollcall';

    protected $_deleted_column = 'delete';

    protected $_belongs_to = [
        'booking'        => ['model' => 'Booking_Booking',       'foreign_key' => 'booking_id'],
        'booking_item'   => ['model' => 'Booking_Item',          'foreign_key' => 'booking_item_id'],
        'booking_status' => ['model' => 'Booking_Status',        'foreign_key' => 'booking_status'],
        'delegate'       => ['model' => 'Contacts3_Contact',     'foreign_key' => 'delegate_id'],
        'timeslot'       => ['model' => 'Course_Schedule_Event', 'foreign_key' => 'timeslot_id']
    ];
}