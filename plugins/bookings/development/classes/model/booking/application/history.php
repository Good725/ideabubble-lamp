<?php defined('SYSPATH') or die('No direct script access.');

class Model_Booking_Application_History extends ORM
{
    protected $_table_name = 'plugin_ib_educate_bookings_has_applications_history';

    protected $_created_by_column    = 'modified_by';
    protected $_modified_by_column   = 'modified_by';
    protected $_date_created_column  = 'timestamp';
    protected $_date_modified_column = 'timestamp';

    protected $_belongs_to = [
        'booking' => ['model' => 'Booking_Application', 'foreign_key' => 'application_id', 'far_key' => 'application_id']
    ];

    public function find_all_filtered()
    {
        $this->join(['plugin_ib_educate_bookings',          'booking'])->on('booking_application_history.booking_id', '=', 'booking.booking_id');
        $this->join(['plugin_ib_educate_bookings_has_applications', 'booking_application'])->on('booking_application_history.booking_id', '=', 'booking_application.booking_id');
        $this->join(['plugin_ib_educate_booking_has_schedules', 'bhs'])->on('bhs.booking_id',     '=', 'booking_application.booking_id');
        $this->join(['plugin_courses_schedules',           'schedule'])->on('bhs.schedule_id',    '=', 'schedule.id');
        $this->join(['plugin_courses_courses',               'course'])->on('schedule.course_id', '=', 'course.id');
        $this->join([Model_KES_Bookings::BOOKING_ITEMS_TABLE,        'item'])->on('item.booking_id',    '=', 'booking.booking_id');
        $this->join(['plugin_courses_schedules_events', 'interview_slot'])->on('item.period_id',  '=', 'interview_slot.id');

        if (!empty($filters['start_date'])) {
            $this->where('booking_application_history.timestamp', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->where('booking_application_history.timestamp', '<=', $filters['end_date']);
        }

        if (!empty($filters['schedule_ids'])) {
            $this->where('schedule.id', 'in', $filters['schedule_ids']);
        }

        if (!empty($filters['course_id'])) {
            $this->where('course.id', '=', $filters['course_id']);
        }

        if (!empty($filters['application_statuses'])) {
            $this->where('booking_application.application_status', 'in', $filters['application_statuses']);
        }

        if (!empty($filters['interview_statuses'])) {
            $this->where('booking_application.interview_status', 'in', $filters['interview_statuses']);
        }

        if (!empty($filters['offer_statuses'])) {
            $this->where('booking_application.offer_status', 'in', $filters['offer_statuses']);
        }

        $this->where('booking.delete', '=', 0);


        return $this->find_all_undeleted();
    }

}