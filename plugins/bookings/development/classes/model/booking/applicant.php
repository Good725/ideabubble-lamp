<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Deprecated. Use Model_Contacts3_Contact
 *
 * This file can be deleted, once confirmed that it is no longer in use.
 */

class Model_Booking_Applicant extends ORM
{
    protected $_table_name = 'plugin_contacts3_contacts';
    protected $_deleted_column = 'delete';

    protected $_has_many = [
        'bookings' => ['model' => 'Booking_Booking', 'foreign_key' => 'contact_id']
    ];

    public function get_full_name()
    {
        return trim($this->first_name. ' ' . $this->last_name);
    }

    public function where_is_current_user()
    {
        $user = Auth::instance()->get_user();

        return $this
            ->join(['plugin_contacts3_users_has_permission', 'uhp'])
            ->on('uhp.contact3_id', '=', 'booking_applicant.id')
            ->where('uhp.user_id', '=', $user['id']);
    }
}