<?php defined('SYSPATH') or die('No direct script access.');

class Model_Booking_Application extends ORM
{
    protected $_table_name  = 'plugin_ib_educate_bookings_has_applications';
    protected $_primary_key = 'id';

    protected $has_many = [
        'booking' => ['model' => 'Booking_Booking', 'foreign_key' => 'booking_id', 'far_key' => 'booking_id'],
        'history' => ['model' => 'Booking_Application_History', 'foreign_key' => 'application_id', 'far_key' => 'application_id']
    ];

    public function save_with_moddate()
    {
        // "Date modified" / "modified by" values are not stored in this table. They corresponding booking is used instead.
        $booking = new Model_Booking_Booking($this->booking_id);
        $booking->save_with_moddate();
        return self::save();
    }

    /**
     * Save a change to the application and record the change in the history table
     *
     * @param  $column - the column to update or an associative array of columns and their new values
     * @param  $value - the new value (unless an array was used for the $column value)
     * @return ORM
     * @throws Exception
     * @throws Kohana_Exception
     */
    public function save_with_history($column, $value = null)
    {
        $db = Database::instance();
        $db->commit();
        $columns = is_array($column) ? $column : [$column => $value];
        try {
            // Save the application
            $this->values($columns);
            $saved = $this->save_with_moddate();

            // Save the history
            foreach ($columns as $column_name => $column_value) {
                $history = new Model_Booking_Application_History();
                $history->set('application_id', $this->id);
                $history->set('booking_id', $this->booking_id);
                $history->set('column', $column_name);
                $history->set('value', $column_value);
                $history->save_with_moddate();
            }

            return $saved;
        } catch (Exception $e) {
            // If there is an error with any of the above saves, revert
            $db->rollback();
            throw $e;
        }
    }

    public function find_all_undeleted()
    {
        // Records are not flagged as deleted from this table. The corresponding record in the bookings table is used.
        return $this
            ->join(['plugin_ib_educate_bookings', 'booking_deleted_check'])->on('booking_application.booking_id', '=', 'booking_deleted_check.booking_id')
            ->where('booking_deleted_check.delete', '=', 0)
            ->find_all();
    }

    public static function get_all_statuses()
    {
        $status_columns = [
            'application_status'  => 'application',
            'interview_status'    => 'interview',
            'offer_status'        => 'offer',
            'registration_status' => 'registration'
        ];

        $columns = ORM::factory('Booking_Application')->list_columns();

        $return = [];
        foreach ($columns as $key => $column) {
            if (isset($status_columns[$key]) && isset($column['options'])) {
                $return[$status_columns[$key]] = [
                    'label'    => ucfirst($status_columns[$key]),
                    'name'     => $key,
                    'statuses' => $column['options']
                ];
            }
        }

        return $return;
    }

    public function find_filtered($filters = [], $args = [])
    {
        $args['single_record'] = true;

        return $this->find_all_filtered($filters, $args);
    }

    public function find_all_filtered($filters = [], $args = [])
    {
        $this->select('booking.created_date');
        $this->select('booking.modified_date');
        $this->select(['course.id',     'course_id']);
        $this->select(['course.title',  'course_title']);
        $this->select(['schedule.id'  , 'schedule_id']);
        $this->select(['schedule.name', 'schedule_name']);
        $this->select('schedule.academic_year_id');
        $this->select([DB::expr("CONCAT(`applicant`.`first_name`, ' ', `applicant`.`last_name`)"), 'applicant_name']);
        $this->select(['interview_slot.id', 'interview_slot_id']);
        $this->select(['interview_slot.datetime_start', 'interview_datetime']);

        $this->join(['plugin_ib_educate_bookings',          'booking'])->on('booking_application.booking_id', '=', 'booking.booking_id');
        $this->join(['plugin_ib_educate_booking_has_schedules', 'bhs'])->on('bhs.booking_id',     '=', 'booking_application.booking_id');
        $this->join(['plugin_courses_schedules',           'schedule'])->on('bhs.schedule_id',    '=', 'schedule.id');
        $this->join(['plugin_courses_courses',               'course'])->on('schedule.course_id', '=', 'course.id');
        $this->join(['plugin_contacts3_contacts',         'applicant'])->on('booking.contact_id', '=', 'applicant.id');
        $this->join([Model_KES_Bookings::BOOKING_ITEMS_TABLE,        'item'])->on('item.booking_id',    '=', 'booking.booking_id');
        $this->join(['plugin_courses_schedules_events', 'interview_slot'])->on('item.period_id',  '=', 'interview_slot.id');

        if (!empty($filters['booking_id'])) {
            $this->where('booking_application.booking_id', '=', $filters['booking_id']);
        }

        if (!empty($filters['schedule_ids'])) {
            $this->where('schedule.id', 'in', $filters['schedule_ids']);
        }

        if (!empty($filters['course_id'])) {
            $this->where('course.id', '=', $filters['course_id']);
        }

        if (!empty($filters['start_date'])) {
            $this->where('booking.created_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $this->where('booking.created_date', '<=', $filters['end_date']);
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

        if (!empty($args['datatable_args'])) {
            $this->apply_datatable_args($args['datatable_args'], $args['column_definitions']);
        }

        $this->order_by('booking.modified_date', 'desc');

        // Return either a single object or an array of objects
        if (!empty($args['single_record'])) {
            $return = $this->find();
        } else {
            // Count all records, ignoring limit and offset
            $this->reset(false);
            $count_all = $this->count_all();

            $return = $this->find_all();
            $return->_count_all = $count_all;
        }

        return $return;
    }

    public static function get_application_reports($filters = [])
    {
        return [
            [
                'text'   => 'Total',
                'amount' => count(ORM::factory('Booking_Application')->find_all_filtered($filters))
            ],
            [
                'text'   => 'All offers',
                'amount' => count(ORM::factory('Booking_Application')->where('offer_status', '=', 'Offered')->find_all_filtered($filters))
            ],
            [
                'text'   => 'Total acceptances',
                'amount' => count(ORM::factory('Booking_Application')->where('application_status', '=', 'Accepted')->find_all_filtered($filters))
            ],
            [
                'text'   => 'Total registered',
                'amount' => count(ORM::factory('Booking_Application')->where('registration_status', '=', 'Registered')->find_all_filtered($filters))
            ],
            [
                'text'   => 'On-hold total',
                'amount' => count(ORM::factory('Booking_Application')->where('application_status', '=', 'On hold')->find_all_filtered($filters))
            ]
        ];
    }

    public static function get_interview_reports($filters = [])
    {
        return [
            [
                'text'   => 'Total',
                'amount' => count(ORM::factory('Booking_Application_History')->where('column', '=', 'application_status')->where('value', '=', 'Accepted')->find_all_filtered($filters))
            ],
            [
                'text'   => 'Scheduled',
                'amount' => count(ORM::factory('Booking_Application_History')->where('column', '=', 'interview_status')->where('value', '=', 'Scheduled')->find_all_filtered($filters))
            ],
            [
                'text'   => 'No show',
                'amount' => count(ORM::factory('Booking_Application_History')->where('column', '=', 'interview_status')->where('value', '=', 'No show')->find_all_filtered($filters))
            ],
            [
                'text'   => 'Waiting list',
                'amount' => count(ORM::factory('Booking_Application_History')->where('column', '=', 'offer_status')->where('value', '=', 'Waiting list')->find_all_filtered($filters))
            ],
            [
                'text'   => 'Not suitable',
                'amount' => count(ORM::factory('Booking_Application_History')->where('column', '=', 'interview_status')->where('value', '=', 'Not suitable')->find_all_filtered($filters))
            ]
        ];
    }

    public static function get_offer_reports($filters = [])
    {
        return [
            [
                'text'   => 'Total offers made',
                'amount' => ORM::factory('Booking_Application_History')->where('column', '=', 'offer_status')->where('value', '=', 'Offered')->find_all_filtered($filters)->count()
            ],
            [
                'text'   => 'No offer',
                'amount' => ORM::factory('Booking_Application_History')->where('column', '=', 'offer_status')->where('value', '=', 'No offer')->find_all_filtered($filters)->count()
            ],
            [
                'text'   => 'Offers accepted',
                'amount' => ORM::factory('Booking_Application_History')->where('column', '=', 'registration_status')->where('value', 'in', ['Deposit paid', 'Awaiting docs', 'Registered', 'Deferred'])->find_all_filtered($filters)->count()
            ],
            [
                'text'   => 'Total registrations completed',
                'amount' => ORM::factory('Booking_Application_History')->where('column', '=', 'registration_status')->where('value', '=', 'Registered')->find_all_filtered($filters)->count()
            ],
            [
                'text'   => 'Total offers unprocessed',
                'amount' => '0'
            ]
        ];
    }

    public static function get_for_datatable($filters = [], $datatable_args = [])
    {
        $model         = new Model_Booking_Application();
        $status_groups = self::get_all_statuses();
        $rows          = [];

        // SQL to get data for each column. This is used for sorting and filtering.
        $column_definitions = [
            'booking.created_date', // created date
            DB::expr("TRIM(CONCAT(`applicant`.`first_name`, ' ', `applicant`.`last_name`))"), // applicant name
            'course.title', // course title
            'booking_application.application_status', // application status
            'interview_slot.datetime_start', // interview
            '', // last offer
            'booking.modified_date', // updated
            '' // actions
        ];

        $results = $model->find_all_filtered($filters, ['datatable_args' => $datatable_args, 'column_definitions' => $column_definitions]);

        // HTML for each cell
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->created_date ? IbHelpers::relative_time_with_tooltip($result->created_date) : '';
            $row[] = htmlentities($result->applicant_name);
            $row[] = htmlentities($result->course_title);
            $row[] = '<span class="application-table-application_status" data-booking_id="'.$result->booking_id.'">'.htmlentities($result->application_status).'</span>';
            $row[] = $result->interview_datetime ? htmlentities(date('D j F H:i', strtotime($result->interview_datetime))) : '';
            $row[] = '';
            $row[] = $result->modified_date ? IbHelpers::relative_time_with_tooltip($result->modified_date) : '';
            $row[] = View::factory('admin/applications/snippets/application_actions_button')
                ->set('application', $result)
                ->set('stage', 'application')
                ->set('status_groups', $status_groups)
                ->render();

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $results->_count_all,
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }

    public static function get_for_interviews_datatable($filters = [], $datatable_args = [])
    {
        $model         = new Model_Booking_Application();
        $status_groups = self::get_all_statuses();
        $rows          = [];

        // SQL to get data for each column. This is used for sorting and filtering.
        $column_definitions = [
            'booking.created_date', // created date
            'schedule.name', // schedule
            'interview_slot.datetime_start', // interview time
            DB::expr("CONCAT(`applicant`.`first_name`, ' ', `applicant`.`last_name`)"), // applicant
            'course.title', // course
            'booking_application.interview_status', // status
            'booking.modified_date', // updated
            '' // actions
        ];

        $results = $model->find_all_filtered($filters, ['datatable_args' => $datatable_args, 'column_definitions' => $column_definitions]);

        // HTML for each cell
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->created_date ? IbHelpers::relative_time_with_tooltip($result->created_date) : '';
            $row[] = htmlentities($result->schedule_name);
            $row[] = ($result->interview_datetime ? htmlentities(date('D j F H:i', strtotime($result->interview_datetime))) : '');
            $row[] = htmlentities($result->applicant_name);
            $row[] = htmlentities($result->course_title);
            $row[] = '<span class="application-table-interview_status" data-booking_id="'.$result->booking_id.'">'.htmlentities($result->interview_status).'</span>';
            $row[] = $result->modified_date ? IbHelpers::relative_time_with_tooltip($result->modified_date) : '';
            $row[] = View::factory('admin/applications/snippets/application_actions_button')
                ->set('application', $result)
                ->set('stage', 'interview')
                ->set('status_groups', $status_groups)
                ->render();

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $results->_count_all,
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }

    public static function get_for_offers_datatable($filters = [], $datatable_args = [])
    {
        $model         = new Model_Booking_Application();
        $status_groups = self::get_all_statuses();
        $rows          = [];

        // SQL to get data for each column. This is used for sorting and filtering.
        $column_definitions = [
            'booking.created_date', // created
            '', // phase
            DB::expr("CONCAT(`applicant`.`first_name`, ' ', `applicant`.`last_name`)"), // applicant
            'course.title', // course
            'booking_application.offer_status', // status
            'booking.modified_date', // updated
            '' // actions
        ];

        $results = $model->find_all_filtered($filters, ['datatable_args' => $datatable_args, 'column_definitions' => $column_definitions]);

        // HTML for each cell
        foreach ($results as $result) {
            $row = [];

            $row[] = $result->created_date ? IbHelpers::relative_time_with_tooltip($result->created_date) : '';
            $row[] = '';
            $row[] = htmlentities($result->applicant_name);
            $row[] = htmlentities($result->course_title);
            $row[] = '<span class="application-table-offer_status" data-booking_id="'.$result->booking_id.'">'.htmlentities($result->offer_status).'</span>';
            $row[] = $result->modified_date ? IbHelpers::relative_time_with_tooltip($result->modified_date) : '';
            $row[] = View::factory('admin/applications/snippets/application_actions_button')
                ->set('application', $result)
                ->set('stage', 'offer')
                ->set('status_groups', $status_groups)
                ->render();

            $rows[] = $row;
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $results->_count_all,
            'iTotalRecords' => $results->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }

    public function apply_datatable_args($args, $column_definitions)
    {
        $sortColumns = $searchColumns = $column_definitions;

        // Global column search
        if (!empty($args['sSearch'])) {
            $this->and_where_open();
                for ($i = 0; $i < count($searchColumns); $i++) {
                    if (isset($args['bSearchable_'.$i]) && $args['bSearchable_'.$i] == 'true' && !empty($searchColumns[$i])) {
                        $this->or_where($searchColumns[$i],'like','%'.$args['sSearch'].'%');
                    }
                }
                $this->where('booking.delete', '=', 0);
            $this->and_where_close();
        }

        // Individual column search
        for ($i = 0; $i < count($searchColumns); $i++) {
            if (isset($args['bSearchable_'.$i]) && $args['bSearchable_'.$i] == 'true' && $args['sSearch_'.$i] != '' && !empty($searchColumns[$i])) {
                $this->and_where($searchColumns[$i],'like','%'.$args['sSearch_'.$i].'%');
            }
        }

        // Limit. Only show the number of records for this paginated page
        if (isset($args['iDisplayLength']) && $args['iDisplayLength'] != -1) {
            $this->limit(intval($args['iDisplayLength']));
            if (isset($args['iDisplayStart'])) {
                $this->offset(intval($args['iDisplayStart']));
            }
        }

        // Order
        if (isset($args['iSortCol_0']) && $args['iSortCol_0']) {
            for ($i = 0; $i < $args['iSortingCols']; $i++) {
                if ($sortColumns[$args['iSortCol_'.$i]] != '' && !empty($sortColumns[$i])) {
                    $this->order_by($sortColumns[$args['iSortCol_'.$i]], $args['sSortDir_'.$i]);
                }
            }
        }

        return $this;
    }

}