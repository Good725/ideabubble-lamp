<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Schedule_Event extends ORM
{
    protected $_table_name = 'plugin_courses_schedules_events';

    protected $_deleted_column = 'delete';

    protected $_belongs_to = [
        'location' => ['model' => 'Course_Location',   'foreign_key' => 'location_id'],
        'trainer'  => ['model' => 'Contacts3_Contact', 'foreign_key' => 'trainer_id'],
        'schedule' => ['model' => 'Course_Schedule',   'foreign_key' => 'schedule_id'],
    ];

    protected $_has_many = [
        'booking_items' => ['model' => 'Booking_Item',     'foreign_key' => 'period_id'],
        'rollcalls'     => ['model' => 'Booking_Rollcall', 'foreign_key' => 'timeslot_id']
    ];

    /* Count the number of people attending the timeslot
     * This is equal to the sum of the number of delegates on each booking
     *
     * @return int
     */
    public function count_attending()
    {
        return $this->get_attendees()->find_all_undeleted()->count();
    }

    /**
     * Get all people attending the timeslot.
     * Usage: $timeslot->get_attendees()->find_all() or $timeslot->get_attendees()->where(...)->find_all()
     *
     * @return ORM
     */
    public function get_attendees()
    {
        // Check the rollcall for who plans on attending.
        $rollcall = $this
            ->rollcalls
            ->with('booking_status')
            ->where('planned_to_attend', '=', 1)
            ->where('booking_status.title', 'not in', ['Cancelled', 'Sales Quote'])
            ->find_all_undeleted()
            ->as_array();

        // Get array of IDs of each delegate. If there are none, use -1, to avoid MySQL error with empty `IN()`.
        $attendee_ids = count($rollcall) ? array_column($rollcall, 'delegate_id') : [-1];

        // Return ORM query for the contacts.
        return ORM::factory('Contacts3_Contact')->where('id', 'in', $attendee_ids);
    }

    /**
     * Determine if the logged-in user has access to this timeslot.
     * They need either the permission to view all or they need to be the trainer.
     */
    public function has_access()
    {
        // User has access to all timeslots.
        if (Auth::instance()->has_access('timetables_view_all')) {
            return true;
        }

        if ($this->trainer_id == Auth::instance()->get_contact()->id) {
            return true;
        }

        return false;
    }

}