<?php defined('SYSPATH') or die('No direct script access.');

class Model_Booking_Booking extends ORM
{
    protected $_table_name           = 'plugin_ib_educate_bookings';
    protected $_primary_key          = 'booking_id';
    protected $_date_created_column  = 'created_date';
    protected $_date_modified_column = 'modified_date';
    protected $_deleted_column       = 'delete';

    protected $_has_one = [
        'application' => ['model' => 'Booking_Application', 'foreign_key' => 'booking_id'],
    ];

    protected $_belongs_to = [
        'applicant' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'contact_id'],
        'status'    => ['model' => 'Booking_Status',    'foreign_key' => 'booking_status']
    ];

    protected $_has_many = array(
        'delegates'     => ['model' => 'Contacts3_Contact',   'through' => 'plugin_ib_educate_bookings_has_delegates', 'foreign_key' => 'booking_id', 'far_key' => 'contact_id'],
        'has_delegates' => ['model' => 'Booking_HasDelegate', 'foreign_key' => 'booking_id'],
        'items'         => ['model' => 'Booking_Item',        'foreign_key' => 'booking_id'],
        'schedules'     => ['model' => 'Course_Schedule',     'through' => 'plugin_ib_educate_booking_has_schedules',  'foreign_key' => 'booking_id', 'far_key' => 'schedule_id'],
    );

    public function get_host_family()
    {
        $contact_id = DB::select('contact_id')
            ->from(['plugin_ib_educate_bookings_has_linked_contacts', 'bhlc'])
            ->join(['plugin_contacts3_contacts',          'contact'], 'inner')->on('bhlc.contact_id',    '=', 'contact.id')
            ->join(['plugin_contacts3_contacts_subtypes', 'subtype'], 'inner')->on('contact.subtype_id', '=', 'subtype.id')
            ->where('subtype.subtype', '=', 'Host Family')
            ->where('bhlc.booking_id', '=', $this->{$this->_primary_key})
            ->execute()
            ->get('contact_id', 0);

        return ORM::factory('Contacts3_Contact')->where('id', '=', $contact_id)->find_undeleted();
    }

    // Filter bookings to ones made by the logged-in user
    public function where_auth_booked()
    {
        $contact     = Auth::instance()->get_contact();
        $bookings    = Model_Kes_Bookings::get_contact_family_bookings(null, $contact->id);
        $booking_ids = array_column($bookings, 'booking_id');
        $booking_ids = empty($booking_ids) ? [-1] : $booking_ids;

        return $this->where('booking_id', 'in', $booking_ids);
    }

    // Get the booked schedule
    public function get_schedule()
    {
        $booking_with_timeslot = $this->items->with('timeslot')->find();
        return $booking_with_timeslot->timeslot->schedule;
    }

    public function get_start_date()
    {
        return $this->items->with('timeslot')->order_by('timeslot.datetime_start', 'asc')->find()->timeslot->datetime_start;
    }

    public function get_end_date()
    {
        return $this->items->with('timeslot')->order_by('timeslot.datetime_end', 'desc')->find()->timeslot->datetime_end;
    }

    // Get the date content is available for the booked schedule
    public function content_available_from_date()
    {
        // Find the first booked timeslot for a schedule with content.
        // This is the start date.
        $timeslot = $this
            ->items
            ->with('timeslot')
            ->with('timeslot:schedule')
            ->where('timeslot:schedule.content_id', 'IS NOT', null)
            ->order_by('timeslot.datetime_start', 'asc')
            ->find()
            ->timeslot;

        if (!empty($timeslot->id)) {
            // Get the start date and schedule content from the timeslot
            $content = $timeslot->schedule->content;
            $start_date = $timeslot->datetime_start;
        } else {
            // If no timeslots have been booked, but the schedule is self-paced, use the booking date as the start date.
            $schedule = $this->schedules->find();
            if ($schedule->learning_mode->value == 'self_paced') {
                $start_date = $this->created_date;
                $content = $schedule->content;
            }
        }

        // If there is no start date or no content, return nothing.
        if (empty($start_date) || empty($content) || empty($content->id)) {
            return '';
        }

        // The content is available a configurable number of days before the schedule starts
        $days_before = $content->available_days_before ? $content->available_days_before : 0;

        return date('Y-m-d H:i:s', strtotime($start_date. ' - '.$days_before.' days'));
    }

    // Get the date content is last available for the booked schedule
    public function content_available_to_date()
    {
        // Find the last booked timeslot for a schedule with content.
        // This is the end date.
        $timeslot = $this
            ->items
            ->with('timeslot')
            ->with('timeslot:schedule')
            ->where('timeslot:schedule.content_id', 'IS NOT', null)
            ->order_by('timeslot.datetime_end', 'desc')
            ->find()
            ->timeslot;

        if (!empty($timeslot->id)) {
            // Get the end date and schedule content from the timeslot
            $content = $timeslot->schedule->content;
            $end_date = $timeslot->datetime_end;
        } else {
            // If no timeslots have been booked, but the schedule is self-paced, use the booking date as the end date.
            $schedule = $this->schedules->find();
            if ($schedule->learning_mode->value == 'self_paced') {
                $end_date = $this->created_date;
                $content = $schedule->content;
            }
        }

        // If there is no end date or no content, return nothing.
        if (empty($end_date) || empty($content) || empty($content->id)) {
            return '';
        }

        // The content is available until a configurable number of days after the schedule ends
        $days_after = $content->available_days_after ? $content->available_days_after : 0;

        return date('Y-m-d H:i:s', strtotime($end_date. ' + '.$days_after.' days'));
    }

    /**
     * Mark a booking as confirmed.
     * Set `planned_to_attend` to yes and booking status to "Confirmed" for each attendee.
     *
     * @param $args    array    filters, including `delegate_ids`
     * @return         integer  number of affected rows
     */
    public function confirm($args)
    {
        $confirmed = ORM::factory('Booking_Status')->where('title', '=', 'Confirmed')->find();

        $q = DB::update(Model_KES_Bookings::BOOKING_ROLLCALL_TABLE)
            ->set(['planned_to_attend' => 1, 'booking_status' => $confirmed->status_id])
            ->where('booking_id', '=', $this->booking_id);

        if (!empty($args['delegate_ids'])) {
            $q->where('delegate_id', 'in', $args['delegate_ids']);
        }

        return $q->execute();
    }
}