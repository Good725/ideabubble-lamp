<?php defined('SYSPATH') or die('No direct script access.');

class Model_Booking_Item extends ORM
{
    protected $_table_name           = Model_KES_Bookings::BOOKING_ITEMS_TABLE;
    protected $_primary_key          = 'booking_item_id';
    protected $_date_created_column  = 'date_created';
    protected $_deleted_column       = 'delete';

    protected $_belongs_to = [
        'booking'  => ['model' => 'Booking_Booking',       'foreign_key' => 'booking_id'],
        'timeslot' => ['model' => 'Course_Schedule_Event', 'foreign_key' => 'period_id']
    ];

    // Method for queries to only include timeslots that the student attended.
    public function where_attended()
    {
        return $this->where('timeslot_status', 'in', ['Present', 'Late', 'Early Departures', 'Temporary Absence']);
    }

    /**
     * Add to a query to quickly apply all filters from an array
     * This is usually used for metrics and the datatable with filter data sent by the JavaScript
     *
     * @param   $filters    array   list of filters
     * @return  $this
     */
    public function apply_filters($filters = [])
    {
        $this
            ->with('booking')
            ->with('booking:applicant')
            ->with('timeslot:location')
            ->with('timeslot:schedule:course')
            ->with('timeslot:schedule:trainer')
            ->with('timeslot:trainer')
            ->where_undeleted()
        ;

        if (!empty($filters['start_date'])) {
            $this->where('timeslot.datetime_start', '>=', $filters['start_date'].' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $this->where('timeslot.datetime_start', '<=', $filters['end_date'].' 23:59:59');
        }

        if (!empty($filters['current_year'])) {
            $this
                ->where('timeslot.datetime_start', '>=', date('Y-01-01 00:00:00'))
                ->where('timeslot.datetime_start', '<=', date('Y-12-31 23:59:59'));
        }

        if (!empty($filters['course_ids'])) {
            $this->where('timeslot:schedule.course_id', 'in', $filters['course_ids']);
        }

        if (!empty($filters['schedule_ids'])) {
            $this->where('timeslot.schedule_id', 'in', $filters['schedule_ids']);
        }

        if (!empty($filters['student_ids'])) {
            $this->where('booking.contact_id', 'in', $filters['student_ids']);
        }

        if (!empty($filters['trainer_ids'])) {
            $this->where('timeslot:schedule.trainer_id', 'in', $filters['trainer_ids']);
        }

        if (!empty($filters['statuses'])) {
            $this->where('booking_item.timeslot_status', 'in', $filters['statuses']);
        }

        return $this;
    }

    // Get a list of all data for the metrics
    public static function get_reports($filters = [])
    {
        $reports = [];
        $data = ORM::factory('Booking_Item')->apply_filters($filters);

        // Get total
        $total = clone $data;
        $count = $total->count_all();
        $reports[] = ['amount' => $count, 'text' => 'Total booked'];

        // Get total per status
        $statuses = ['Present', 'Late', 'Absent'];
        foreach ($statuses as $status) {
            $status_data = clone $data;
            $count = $status_data->where('booking_item.timeslot_status', '=', $status)->count_all();
            $reports[] = ['amount' => $count, 'text' => $status];
        }

        return $reports;
    }

    // Get cells for the datatable
    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'booking_item.booking_id',
            [DB::expr("CONCAT(`timeslot:schedule:trainer`.`first_name`,  ' ', `timeslot:schedule:trainer`.`last_name`)" ), 'trainer'],
            [DB::expr("CONCAT(`booking:applicant`.`first_name`, ' ', `booking:applicant`.`last_name`)"), 'learner'],
            ['timeslot:schedule:course.title', 'course'],
            ['timeslot:schedule.name', 'schedule'],
            'booking_item.timeslot_status',
            'timeslot.datetime_start',
            'timeslot:location.name'
        ];
        $results = $this->apply_filters($filters);
        $all = clone $results;
        $results = $results
            ->apply_datatable_args($datatable_args, $column_definitions)
            ->order_by('timeslot.datetime_start', 'desc')
            ->find_all_undeleted();
        $datatable_args['unlimited'] = true;
        $all = $all->apply_datatable_args($datatable_args, $column_definitions);

        $rows = [];
        foreach ($results as $result) {
            $row = [];

            $options = [
                ['type' => 'link', 'title' => 'View', 'attributes' => ['class' => 'edit-link']],
            ];

            $row[] = $result->booking_id;
            $row[] = htmlspecialchars($result->timeslot->schedule->trainer->get_full_name());
            $row[] = htmlspecialchars($result->booking->applicant->get_full_name());
            $row[] = htmlspecialchars($result->timeslot->schedule->course->title);
            $row[] = htmlspecialchars($result->timeslot->schedule->name);
            $row[] = htmlspecialchars($result->timeslot_status);
            $row[] = IbHelpers::formatted_time($result->timeslot->datetime_start);
            $row[] = htmlspecialchars($result->timeslot->location->name);

            /*
            $row[] = View::factory('snippets/btn_dropdown')
                ->set('type', 'actions')
                ->set('options', $options)->render();
            */

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $all->count_all(),
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];

    }
}