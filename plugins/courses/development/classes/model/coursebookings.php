<?php defined('SYSPATH') or die('No direct script access.');

class Model_CourseBookings extends Model
{
    const TABLE_BOOKINGS = 'plugin_courses_bookings';
    const TABLE_HISTORY = 'plugin_courses_bookings_history';
    const TABLE_HAS_SCHEDULES = 'plugin_courses_bookings_has_schedules';
    const TABLE_HAS_TIMESLOTS = 'plugin_courses_bookings_has_schedules_has_timeslots';
    const TABLE_ROLLCALL = 'plugin_courses_rollcall';
    const TABLE_HAS_TRANSACTIONS = 'plugin_courses_bookings_has_transactions';
    const TABLE_HAS_DISCOUNTS = 'plugin_courses_bookings_has_discounts';

    public static function set_processing_status($booking_id, $status, $note, $user = null, $cancel_transactions = true, $clear_outstanding = true)
    {
        try {
            Database::instance()->begin();

            if ($user == null) {
                $user = auth::instance()->get_user();
            }

            DB::update(self::TABLE_BOOKINGS)
                ->set(array('updated' => date::now(), 'updated_by' => $user['id'], 'status' => $status))
                ->where('id', '=', $booking_id)
                ->and_where('deleted', '=', 0)
                ->execute();

            DB::update(self::TABLE_HAS_SCHEDULES)
                ->set(array('updated' => date::now(), 'updated_by' => $user['id'], 'status' => $status))
                ->where('booking_id', '=', $booking_id)
                ->and_where('deleted', '=', 0)
                ->execute();

            if ($status == 'Cancelled' && $cancel_transactions == true) {
                self::set_cancel_transactions($booking_id, $user, $clear_outstanding);
            }

            if ($status == 'Confirmed') {
                $booking = self::load($booking_id);
                foreach ($booking['has_schedules'] as $has_schedule) {
                    Model_SchedulesStudents::save('new', $booking['student_id'], $has_schedule['schedule_id'], 'Registered', '');
                }
            }

            if ($note) {
                Model_Notes::create('Course Booking', $booking_id, $note, $user);
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }

    }

    public static function set_cancel_transactions($booking_id, $user = null, $clear_outstanding = true)
    {
        try {
            Database::instance()->begin();

            if ($user == null) {
                $user = auth::instance()->get_user();
            }

            $transactions = self::get_transactions($booking_id);
            foreach ($transactions as $transaction) {
                Model_Transactions::cancel_transaction($transaction['id'], $user, $clear_outstanding);
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function save($booking, $user = null)
    {
        //header('content-type: text/plain');print_r($booking);exit;
        try {
            Database::instance()->begin();

            if ($user == null) {
                $user = auth::instance()->get_user();
            }

            self::save_booking($booking, $user);
            self::save_schedules($booking, $user);
            self::save_discounts($booking, $user);

            self::save_history($booking);
            Database::instance()->commit();

            return $booking;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    protected static function save_booking(&$booking, $user)
    {
        $data = arr::set($booking, "id", "student_id", "payer_id", "currency", "fee", "discount", "total", "status", "deleted");
        $data['updated'] = date::now();
        $data['updated_by'] = $user['id'];

        if (is_numeric(@$booking['id'])) {
            DB::update(self::TABLE_BOOKINGS)->set($data)->where('id', '=', $booking['id'])->execute();

        } else {
            $data['created'] = $data['updated'];
            $data['created_by'] = $data['updated_by'];
            $inserted = DB::insert(self::TABLE_BOOKINGS)->values($data)->execute();
            $booking['id'] = $inserted[0];

            /*
             * create a transaction for the schedule if total is set
             * if a transaction is created for whole booking then no transactions will be created for any schedules
             * if a transaction is not created for whole booking then a transaction will be created for each schedule
             */
            if (isset($booking['total'])) {
                self::create_has_transaction($booking, null, date::today(), $user);
            }
        }
    }

    protected static function save_schedules(&$booking, $user)
    {
        foreach ($booking['has_schedules'] as $i => $has_schedule) {
            $data = arr::set($has_schedule, "id", "schedule_id", "currency", "fee", "discount", "total", "status", "deleted");
            $data['booking_id'] = $booking['id'];
            $data['updated'] = date::now();
            $data['updated_by'] = $user['id'];

            if (is_numeric(@$has_schedule['id'])) {
                DB::update(self::TABLE_HAS_SCHEDULES)->set($data)->where('id', '=', $has_schedule['id'])->execute();
            } else {
                $data['created'] = $data['updated'];
                $data['created_by'] = $data['updated_by'];
                $inserted = DB::insert(self::TABLE_HAS_SCHEDULES)->values($data)->execute();
                $booking['has_schedules'][$i]['id'] = $inserted[0];

                // create a transaction for the schedule if total is set
                if (isset($has_schedule['total'])) {
                    self::create_has_transaction($booking, $booking['has_schedules'][$i], date::today(), $user);
                }
            }

            self::save_timeslots($booking['has_schedules'][$i], $user);
        }
    }

    protected static function create_has_transaction($booking, $booking_has_schedule, $due, $user)
    {
        $reason = 'Course Booking #' . $booking['id'];
        if ($booking_has_schedule['schedule_id']) {
            $schedule_details = Model_Schedules::get_one_for_details($booking_has_schedule['schedule_id']);
            $reason .= '; ' . $schedule_details['course'] . ' ' . date('d/m/Y', strtotime($schedule_details['start_date']));
        }

        $transaction = array(
            'contact_id' => $booking['payer_id'],
            'type' => 'Business',
            'currency' => $booking_has_schedule ? $booking_has_schedule['currency'] : $booking['currency'],
            'fee' => $booking_has_schedule ? $booking_has_schedule['fee'] : $booking['fee'],
            'discount' => $booking_has_schedule ? $booking_has_schedule['discount'] : $booking['discount'],
            'total' => $booking_has_schedule ? $booking_has_schedule['total'] : $booking['total'],
            'due' => $due,
            'status' => 'Outstanding',
            'reason' => $reason
        );

        $transaction = Model_Transactions::save($transaction, $user);

        $has_transaction = array(
            'booking_id' => $booking['id'],
            'booking_has_schedule_id' => $booking_has_schedule ? $booking_has_schedule['id'] : null,
            'transaction_id' => $transaction['id']
        );

        $has_transaction['updated'] = date::now();
        $has_transaction['updated_by'] = $user['id'];
        $has_transaction['created'] = $has_transaction['updated'];
        $has_transaction['created_by'] = $has_transaction['updated_by'];

        $inserted = DB::insert(self::TABLE_HAS_TRANSACTIONS)->values($has_transaction)->execute();
        return $inserted[0];
    }

    protected static function save_timeslots(&$has_schedule, $user)
    {
        foreach ($has_schedule['has_timeslots'] as $i => $has_timeslot) {
            $data = arr::set($has_timeslot, "id", "timeslot_id", "currency", "fee", "discount", "total", "attend", "deleted");
            $data['booking_has_schedule_id'] = $has_schedule['id'];
            $data['updated'] = date::now();
            $data['updated_by'] = $user['id'];

            if (is_numeric(@$has_timeslot['id'])) {
                DB::update(self::TABLE_HAS_TIMESLOTS)->set($data)->where('id', '=', $has_timeslot['id'])->execute();
            } else {
                $data['created'] = $data['updated'];
                $data['created_by'] = $data['updated_by'];
                $inserted = DB::insert(self::TABLE_HAS_TIMESLOTS)->values($data)->execute();
                $has_schedule['has_timeslots'][$i]['id'] = $inserted[0];
            }
            if (array_key_exists('note', $has_timeslot)) {
                Model_Notes::save(
                    array(
                        'type' =>'Course Booking Timeslot',
                        'reference_id' => $has_schedule['has_timeslots'][$i]['id'],
                        'note' => $has_timeslot['note'],
                    ),
                    true,
                    $user
                );
            }

        }
    }

    protected static function save_history($booking)
    {
        $result = DB::insert(self::TABLE_HISTORY)
            ->values(array(
                'booking_id' => $booking['id'],
                'saved' => date('Y-m-d H:i:s'),
                'data' => json_encode($booking)
            ))->execute();
        return $result[0];
    }

    public static function load($id)
    {
        $booking = DB::select('*')
            ->from(self::TABLE_BOOKINGS)
            ->where('id', '=', $id)
            ->execute()
            ->current();

        if ($booking) {
            $booking['student'] = DB::select('*')
                ->from(Model_Contacts::TABLE_CONTACT)
                ->where('id', '=', $booking['student_id'])
                ->execute()
                ->current();

            $booking['payer'] = DB::select('*')
                ->from(Model_Contacts::TABLE_CONTACT)
                ->where('id', '=', $booking['payer_id'])
                ->execute()
                ->current();

            $booking['all_fee'] = 0;
            if ($booking['fee'] > 0) {
                $booking['all_fee'] += $booking['fee'];
            }
            $booking['all_total'] = 0;
            if ($booking['total'] > 0) {
                $booking['all_total'] += $booking['total'];
            }

            $booking['has_schedules'] = DB::select(
                'schedules.*',
                array('courses.title', 'course'),
                'has_schedules.*'
            )
                ->from(array(self::TABLE_HAS_SCHEDULES, 'has_schedules'))
                    ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                        ->on('has_schedules.schedule_id', '=', 'schedules.id')
                    ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                        ->on('schedules.course_id', '=', 'courses.id')
                ->where('has_schedules.booking_id', '=', $id)
                ->and_where('has_schedules.deleted', '=', 0)
                ->execute()
                ->as_array();

            foreach ($booking['has_schedules'] as $i => $has_schedule) {

				$booking['has_schedules'][$i]['schedule'] = Model_Schedules::get_one_for_details($booking['has_schedules'][$i]['schedule_id']);

                $booking['has_schedules'][$i]['has_timeslots'] = DB::select(
                    array('schedules.name', 'schedule'),
                    array('courses.title', 'course'),
                    'timeslots.*',
                    'has_timeslots.*',
                    'rollcall.status',
                    'notes.note'
                )
                    ->from(array(self::TABLE_HAS_TIMESLOTS, 'has_timeslots'))
                        ->join(array(Model_ScheduleEvent::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                            ->on('has_timeslots.timeslot_id', '=', 'timeslots.id')
                        ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                            ->on('timeslots.schedule_id', '=', 'schedules.id')
                        ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                            ->on('schedules.course_id', '=', 'courses.id')
                        ->join(array(self::TABLE_HAS_SCHEDULES, 'has_schedules'), 'inner')
                            ->on('has_schedules.id', '=', 'has_timeslots.booking_has_schedule_id')
                        ->join(array(self::TABLE_BOOKINGS, 'bookings'), 'inner')
                            ->on('has_schedules.booking_id', '=', 'bookings.id')
                        ->join(array(self::TABLE_ROLLCALL, 'rollcall'), 'left')
                            ->on('timeslots.id', '=', 'rollcall.timeslot_id')
                            ->on('bookings.student_id', '=', 'rollcall.student_id')
                        ->join(array(Model_Notes::TABLE_NOTES, 'notes'), 'left')
                            ->on('has_timeslots.id', '=', 'notes.reference_id')
                            ->on('notes.type_id', '=', DB::expr(Model_Notes::get_type_id('Course Booking Timeslot')))
                    ->where('has_timeslots.booking_has_schedule_id', '=', $has_schedule['id'])
                    ->and_where('has_timeslots.deleted', '=', 0)
                    ->execute()
                    ->as_array();
                $booking['has_schedules'][$i]['due'] = 0;
                if ($has_schedule['total']) {
                    $booking['all_total'] += $has_schedule['total'];
                }
                if ($has_schedule['fee']) {
                    $booking['all_fee'] += $has_schedule['fee'];
                }
            }

            $booking['has_transactions'] = DB::select(
                'tx.*',
                'outstandings.outstanding',
                'has_tx.booking_has_schedule_id'
            )
                ->from(array(self::TABLE_HAS_TRANSACTIONS, 'has_tx'))
                    ->join(array(Model_Transactions::TABLE_TRANSACTIONS, 'tx'), 'inner')
                        ->on('has_tx.transaction_id', '=', 'tx.id')
                    ->join(array(Model_Transactions::TABLE_OUTSTANDINGS, 'outstandings'), 'left')
                        ->on('tx.id', '=', 'outstandings.transaction_id')
                ->where('has_tx.booking_id', '=', $id)
                ->and_where('has_tx.deleted', '=', 0)
                ->and_where('tx.deleted', '=', 0)
                ->execute()
                ->as_array();

            $outstanding = 0;
            foreach ($booking['has_transactions'] as $tx_key => $tx) {
                $outstanding += $tx['outstanding'];

                $booking['has_transactions'][$tx_key]['payments'] = DB::select('*')
                    ->from(Model_TransactionPayments::TABLE_PAYMENTS)
                    ->where('to_transaction_id', '=', $tx['id'])
                    ->execute()
                    ->as_array();

                foreach ($booking['has_schedules'] as $i => $has_schedule) {
                    if ($tx['booking_has_schedule_id'] == $has_schedule['id']) {
                        $booking['has_schedules'][$i]['due'] += $tx['outstanding'];
                    }
                }
            }
            $booking['outstanding'] = $outstanding;
            $booking['has_discounts'] = DB::select('discounts.title', 'has_discounts.*')
                ->from(array(self::TABLE_HAS_DISCOUNTS, 'has_discounts'))
                    ->join(array(Model_Coursediscounts::DISCOUNTS_TABLE, 'discounts'), 'inner')
                        ->on('has_discounts.discount_id', '=', 'discounts.id')
                ->where('has_discounts.booking_id', '=', $booking['id'])
                ->and_where('has_discounts.deleted', '=', 0)
                ->execute()
                ->as_array();

        }

        return $booking;
    }

    public static function calculate_booking_cost($schedule_id, $timeslot_ids = null, $from_date = null, $to_date = null)
    {
        $fee = null;
        $schedule = DB::select('*')
            ->from(Model_Schedules::TABLE_SCHEDULES)
            ->where('id', '=', $schedule_id)
            ->execute()
            ->current();

        if ($schedule) {
            if ($schedule['is_fee_required'] == 1) {
                if ($schedule['fee_per'] == 'Schedule') {
                    $fee = (float)$schedule['fee_amount'];
                } else if ($schedule['fee_per'] == 'Timeslot') {
                    $timeslotsq = DB::select(DB::expr("SUM(IFNULL(timeslots.fee_amount, schedules.fee_amount)) AS `fee`"))
                        ->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
                            ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                                ->on('schedules.id', '=', 'timeslots.schedule_id')
                        ->where('timeslots.delete', '=', 0)
                        ->and_where('schedules.id', '=', $schedule_id);
                    if ($timeslot_ids) {
                        $timeslotsq->and_where('timeslots.id', 'in', $timeslot_ids);
                    } else {
                        if ($from_date == null) {
                            $from_date = date::today();
                        }
                    }

                    if ($from_date) {
                        $from_datetime = date('Y-m-d 00:00:00', strtotime($from_date));
                        $timeslotsq->and_where('timeslots.datetime_start', '>=', $from_datetime);
                    }
                    if ($to_date) {
                        $to_datetime = date('Y-m-d 23:59:59', strtotime($to_date));
                        $timeslotsq->and_where('timeslots.datetime_start', '<=', $to_datetime);
                    }
                    $fee = (float)$timeslotsq->execute()->get('fee');
                }
            } else {
                $fee = 0;
            }
        }

        return $fee;
    }

    public static function search($params)
    {
        $selectq = DB::select(
            DB::expr("SQL_CALC_FOUND_ROWS bookings.*"),
            array('schedules.name', 'schedule'),
            array('timeslots.datetime_start', 'datetime_start'),
            array('timeslots.datetime_end', 'datetime_end'),
            array('courses.title', 'course'),
            'ccategories.category',
            'bookings.updated',
            'has_schedules.schedule_id',
            DB::expr("IF(bookings.total, bookings.total, has_schedules.total) as total"),
            DB::expr("CONCAT_WS(' ', students.title, students.first_name, students.last_name) AS student_name"),
            array('parents.id', 'parent_id'),
            DB::expr("IF(students.mobile IS NOT NULL AND students.mobile <> '', students.mobile, parents.mobile) AS mobile"),
            DB::expr("IF(students.email IS NOT NULL AND students.email <> '', students.email, parents.email) AS email"),
            'family.family',
            DB::expr("CONCAT_WS(' ', parents.title, parents.first_name, parents.last_name) AS parent_name"),
            'has_schedules.status',
            'outstanding.outstanding',
            array('providers.name', 'provider')
        )
            ->from(array(self::TABLE_BOOKINGS, 'bookings'))
                ->join(array(Model_Contacts::TABLE_CONTACT, 'students'), 'inner')
                    ->on('bookings.student_id', '=', 'students.id')
                ->join(array(Model_Families_Members::TABLE, 'fmembers'), 'left')
                    ->on('students.id', '=', 'fmembers.contact_id')
                ->join(array(Model_Families::TABLE, 'family'), 'left')
                    ->on('fmembers.family_id', '=', 'family.id')
                ->join(array(Model_Contacts::TABLE_CONTACT, 'parents'), 'left')
                    ->on('family.primary_contact_id', '=', 'parents.id')
                ->join(array(self::TABLE_HAS_SCHEDULES, 'has_schedules'), 'inner')
                    ->on('bookings.id', '=', 'has_schedules.booking_id')
                ->join(array(self::TABLE_HAS_TIMESLOTS, 'has_timeslots'), 'inner')
                    ->on('has_schedules.id', '=', 'has_timeslots.booking_has_schedule_id')
                ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'inner')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
                ->join(array(Model_Schedules::TABLE_TIMESLOTS, 'timeslots'), 'inner')
                    ->on('has_timeslots.timeslot_id', '=', 'timeslots.id')
                ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
                    ->on('schedules.course_id', '=', 'courses.id')
                ->join(array(Model_Categories::TABLE_CATEGORIES, 'ccategories'), 'left')
                    ->on('courses.category_id', '=', 'ccategories.id')
                ->join(array(self::TABLE_HAS_TRANSACTIONS, 'has_tx'), 'left')
                    ->on('bookings.id', '=', 'has_tx.booking_id')
                    ->on('has_tx.deleted', '=', DB::expr(0))
                ->join(array(Model_Transactions::TABLE_TRANSACTIONS, 'tx'), 'left')
                    ->on('has_tx.transaction_id', '=', 'tx.id')
                    ->on('tx.deleted', '=', DB::expr(0))
                ->join(array(Model_Transactions::TABLE_OUTSTANDINGS, 'outstanding'), 'left')
                    ->on('tx.id', '=', 'outstanding.transaction_id')
                ->join(array(Model_Providers::TABLE_PROVIDERS, 'providers'), 'left')
                    ->on('schedules.owned_by', '=', 'providers.franchisee_id')
            ->where('bookings.deleted', '=', 0)
            ->and_where('has_schedules.deleted', '=', 0)
            ->and_where('has_timeslots.deleted', '=', 0)
            /*->and_where_open()
                ->or_where('has_schedules.id', '=', 'has_tx.booking_has_schedule_id')
                ->or_where('has_tx.booking_has_schedule_id', 'is', null)
            ->and_where_close()*/;

        if (@$params['term']) {
            $selectq->and_where_open()
                ->or_where('courses.title', 'like', '%' . $params['term'] . '%')
                ->or_where('schedules.name', 'like', '%' . $params['term'] . '%')
                ->or_where('ccategories.category', 'like', '%' . $params['term'] . '%')
                ->and_where_close();
        }
        //$limit, $offset, $sort, $sort_dir, $term = null

        if (isset($params['user_id'])) {
            Model_Contacts::limited_user_access_filter($selectq, $params['user_id'], 'bookings.student_id');
        }

        if (isset($params['student_id'])) {
            $selectq->and_where('students.id', '=', $params['student_id']);
        }

        if (isset($params['schedule_id'])) {
            $selectq->and_where('schedules.id', '=', $params['schedule_id']);
        }

        if (isset($params['course_id'])) {
            $selectq->and_where('courses.id', '=', $params['course_id']);
        }

        if (isset($params['status'])) {
            $selectq->and_where('bookings.status', 'in', $params['status']);
        }

        if (isset($params['timeslot_id'])) {
            $selectq->and_where('has_timeslots.timeslot_id', '=', $params['timeslot_id']);
            $selectq->group_by('bookings.id')->group_by('has_timeslots.timeslot_id');
        } else {
            $selectq->group_by('bookings.id')->group_by('has_schedules.id');
        }

        if (@$params['limit'] > 0) {
            $selectq->limit($params['limit']);
        }
        if (@$params['offset'] > 0) {
            $selectq->offset($params['offset']);
        }

        if (isset($params['sort']) && isset($params['sort_dir'])) {
            $selectq->order_by($params['sort'], $params['sort_dir']);
        }

        $result = $selectq->execute()->as_array();
        return $result;
    }

    public static function get_datatable($params)
    {
        $data = self::search($params);

        $result = array();

        if (isset($params['sEcho'])) {
            $result['sEcho'] = $params['sEcho'];
        }
        $result['iTotalRecords'] = DB::query(Database::SELECT, "SELECT FOUND_ROWS() AS found")->execute()->get("found");

        $result['aaData'] = array();
        $result['iTotalDisplayRecords'] = count($data);

        foreach ($data as $row) {
            $result['aaData'][] = array(
                'id' => $row['id'],
                'course' => $row['course'],
                'category' => $row['category'],
                'schedule' => $row['schedule'],
                'provider' => $row['provider'],
                'student' => $row['student_name'],
                'time' => date::format('H:i', $row['datetime_start']) . ' - ' . date::format('H:i', $row['datetime_end']),
                'updated' => $row['updated'],
                'total' => $row['total'],
                'outstanding' => $row['outstanding'],
                'status' => $row['status'],
                'actions' => '<div class="dropdown">
						<button class="btn btn-default dropdown-toggle btn-actions" type="button" data-toggle="dropdown">
							' . __('Actions') . '
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">' .
                            (Auth::instance()->has_access('courses') && $row['status'] != 'Cancelled' ? '<li><a class="cancel" data-outstanding="' . $row['outstanding'] . '" data-booking_id="' . $row['id'] . '" class="edit-link"><span class="icon-pencil"></span>' .  __('Cancel') . '</a></li>' : '') .
                            (Auth::instance()->has_access('courses') && $row['status'] != 'Cancelled' ? '<li><a class="transfer" data-booking_id="' . $row['id'] . '" class="edit-link"><span class="icon-pencil"></span>' .  __('Transfer Away') . '</a></li>' : '') .
                        '</ul>
					</div>'
            );
        }
        return $result;
    }

    public static function bookings_quantity_select($schedule_id, $timeslot_id = null)
    {
        $select = DB::select(DB::expr("count(*) as quantity"))
            ->from(array(self::TABLE_BOOKINGS, 'bookings'))
                ->join(array(self::TABLE_HAS_SCHEDULES, 'has_schedules'), 'inner')
                    ->on('bookings.id', '=', 'has_schedules.booking_id')
            ->where('bookings.status', 'in', array('Confirmed', 'Processing'))
            ->and_where('bookings.deleted', '=', 0)
            ->and_where('has_schedules.status', 'in', array('Confirmed', 'Processing'))
            ->and_where('has_schedules.deleted', '=', 0)
            ->and_where('has_schedules.schedule_id', '=', $schedule_id);


        if ($timeslot_id) {
            $select->join(array(self::TABLE_HAS_TIMESLOTS, 'timeslots'), 'inner')
                ->on('has_schedules.id', '=', 'timeslots.booking_has_schedule_id')
                ->and_where('timeslots.timeslot_id', '=', $timeslot_id)
                ->and_where('timeslots.deleted', '=', 0);
        }
        return $select;
    }


    public static function get_transactions($booking_id, $type = null)
    {
        $selectq = DB::select('tx.*')
            ->from(array(self::TABLE_HAS_TRANSACTIONS, 'has_tx'))
                ->join(array(Model_Transactions::TABLE_TRANSACTIONS, 'tx'), 'inner')
                    ->on('has_tx.transaction_id', '=', 'tx.id')
            ->where('has_tx.deleted', '=', 0)
            ->and_where('tx.deleted', '=', 0)
            ->and_where('has_tx.booking_id', '=', $booking_id);
        if ($type) {
            $selectq->join(array(Model_Transactions::TABLE_TYPES, 'txtypes'), 'inner')
                ->on('tx.type_id', '=', 'txtypes.id')
                ->and_where('txtypes.transaction_type', 'in', $type);
        }
        $transactions = $selectq->execute()->as_array();
        return $transactions;
    }

    public static function nbs_checkout_save($post)
    {
        $users      = new Model_Users();
        $roles      = new Model_Roles();
		$contact_id = NULL;
		$student_id = NULL;

		$logged_in_user    = Auth::instance()->get_user();
		$existing_user     = $users->get_user_by_email($post['email']);
		$logged_in_user_id = empty($logged_in_user['id']) ? '' : $logged_in_user['id'];
		$existing_user_id  = empty($existing_user['id'] ) ? '' : $existing_user['id'];

		if (Session::instance()->get('nbs_checkout_new_user_id') AND Session::instance()->get('nbs_checkout_new_user_id') == $existing_user_id) {
            //user/contact has just been saved. probably, payment failed due to some error and trying payment again.
            // skip existing user/contact checks
        } else {
			$emailMessage      = __('Your email address is already registered to an account. Please choose "Registered User" to complete your booking or add a different email address to manage this booking.');
			$mobileMessage     = __('Your mobile is already registered to an account. Please choose "Registered User" to complete your booking or add a different mobile to manage this booking.');

			if ($existing_user AND ($existing_user['id'] != $logged_in_user_id)) {
                $result = array(
                    'success' => false,
                    'message' => $emailMessage
                );
                return $result;
            }

            if (!$existing_user_id) {
                /*
                 * check limited users with access to contacts
                 * */
                if (count(Model_Contacts::getPermissions(Model_Contacts::search(array('mobile' => $post['mobile'])))) > 0) {
                    $result = array(
                        'success' => false,
                        'message' => $mobileMessage
                    );
                    return $result;
                }

                if (count(Model_Contacts::getPermissions(Model_Contacts::search(array('phone' => $post['mobile'])))) > 0) {
                    $result = array(
                        'success' => false,
                        'message' => $mobileMessage
                    );
                    return $result;
                }
            }
        }


        $booking = null;

        try {
            Database::instance()->begin();

            $new_user_role_id = $roles->get_id_for_role('Parent/Guardian');
            if (!$new_user_role_id) {
                $new_user_role_id = $roles->get_id_for_role('External User');
            }
            if ($user_id = Session::instance()->get('nbs_checkout_new_user_id')) {
                $contact_id = Session::instance()->get('nbs_checkout_new_contact_id');
                $student_id = Session::instance()->get('nbs_checkout_new_student_id');
            }
			else
			{
				if ($existing_user)
				{
					$user_id = $logged_in_user_id;
					$contacts = Model_Contacts::get_contact_data_for_user($logged_in_user_id);
					if ( ! empty($contacts['guardian']['id']))
					{
						$contact_id = $contacts['guardian']['id'];
					}

					if ( ! empty($contacts['children']) AND  ! empty($contacts['children'][0]['id']))
					{
						//$student_id = $contacts['children'][0]['id'];
					}
				}
				else
				{
					$user_added = $users->add_user_data(array(
						'email' => $post['email'],
						'password' => $post['password'],
						'role_id' => $new_user_role_id,
						'can_login' => 1,
						'name' => strip_tags($post['first_name']),
						'surname' => strip_tags($post['last_name']),
						'address' => strip_tags($post['address']),
						'phone' => strip_tags($post['telephone'])
					));
					$user_id = $user_added[0];
					if (!$user_id) {
						throw new Exception("Unexpected Error");
					}
				}


                Session::instance()->set('nbs_checkout_new_user_id', $user_id);

                $contact = new Model_Contacts($contact_id);
                $contact->set_title($post['title']);
                $contact->set_first_name($post['first_name']);
                $contact->set_last_name($post['last_name']);
                $contact->set_email($post['email']);
                $contact->set_address1(@$post['address']);
                $contact->set_address2($post['city']);
                $contact->set_address3($post['state']);
                $contact->set_address4($post['country']);
                $contact->set_mobile($post['mobile']);
                $contact->set_notes(strip_tags($post['comments']));
                $contact->test_existing_email = false;
                $contact->set_mailing_list('Parent/Guardian');
                $contact->set_permissions(array($user_id));
                $contact->save('contact_only');
                $contact_id = $contact->get_id();
                Session::instance()->set('nbs_checkout_new_contact_id', $contact_id);

				if ( ! $existing_user)
				{
					$family_id = Model_Families::set_family('', $post['last_name'], 1, 0, $contact_id);
					Model_Families_Members::add_family_member($family_id, $contact_id, 'Parent');
				} else {
                    $family = Model_Families::get_family_of($contact_id);
                    $family_id = $family['id'];
                }

                $student_exists = Model_Contacts::search(
                    array(
                        'user_id' => $user_id,
                        'first_name' => $post['student_first_name'],
                        'last_name' => $post['student_last_name']
                    )
                );
                if (count($student_exists) > 0) {
                    $student_id = $student_exists[0]['id'];
                }
                $student = new Model_Contacts($student_id);
                $student->set_title($post['student_title']);
                $student->set_first_name($post['student_first_name']);
                $student->set_last_name($post['student_last_name']);
                $student->set_email('');
                $student->set_address1(@$post['address']);
                $student->set_address2($post['city']);
                $student->set_address3($post['state']);
                $student->set_address4($post['country']);
                $student->set_notes(strip_tags($post['comments']));
                $student->test_existing_email = false;
                $student->set_mailing_list('Student');
                $student->set_permissions(array($user_id));
                $student->set_dob($post['student_dob_yy'] . '-' . $post['student_dob_mm'] . '-' . $post['student_dob_dd']);
                $student->save('contact_only');

                if (is_array($post['preference'])) {
                    foreach ($post['preference'] as $pref => $value) {
                        $student->add_pref($pref, $value);
                    }
                }

				$student_id = $student->get_id();
                if (count($student_exists) == 0)
				{
                    Model_Families_Members::add_family_member($family_id, $student_id, 'Student');
				}
                Session::instance()->set('nbs_checkout_new_student_id', $student_id);
            }


            $booking = array(
                'student_id' => $student_id,
                'payer_id' => $student_id,
                'status' => $post['amount_type'] == 'later' ? 'Pending' : 'Processing',
                'has_schedules' => array(
                ),
                'has_discounts' => array()
            );

            $has_schedules = $post['has_schedule'];
            foreach ($has_schedules as $i => $has_schedule) {
                $timeslots_to_calculate = array();
                if (isset($has_schedule['has_timeslots']) && is_array($has_schedule['has_timeslots'])) { // specific timeslots are going to be booked
                    foreach ($has_schedule['has_timeslots'] as $ti => $has_timeslot) {
                        $has_schedule['has_timeslots'][$ti]['attend'] = 1;
                        $timeslots_to_calculate[] = $has_timeslot['timeslot_id'];
                    }
                } else { // all timeslots are going to be booked
                    $has_schedule['has_timeslots'] = array();
                    $timeslots = Model_Schedules::get_all_schedule_timeslots($has_schedule['schedule_id']);
                    foreach ($timeslots as $ti => $timeslot) {
                        $has_schedule['has_timeslots'][$ti]['timeslot_id'] = $timeslot['id'];
                        $has_schedule['has_timeslots'][$ti]['attend'] = 1;
                    }
                }

                $has_schedule['currency'] = 'EUR';
                $has_schedule['fee'] = self::calculate_booking_cost(
                    $has_schedule['schedule_id'],
                    count($timeslots_to_calculate) ? $timeslots_to_calculate : null
                );
                $available_discounts = Model_Coursebookings::get_available_discounts(
                    @$student_id,
                    array(
                        array(
                            'name' => '',
                            'fee' => (float)$has_schedule['fee'],
                            'fee_per' => '',
                            'id' => $has_schedule['schedule_id'],
                            'prepay' => true,
                            'next_payment' => null
                        )
                    )
                );
                $discounts_amount = 0;
                if (isset($available_discounts[0]['discounts']))
                foreach ($available_discounts[0]['discounts'] as $available_discount) {
                    $discounts_amount += $available_discount['amount'];
                    $booking['has_discounts'][] = array(
                        'schedule_id' => $has_schedule['schedule_id'],
                        'discount_id' => $available_discount['id'],
                        'amount' => $available_discount['amount']
                    );
                }
                $has_schedule['discount'] = $discounts_amount;
                $has_schedule['total'] = $has_schedule['fee'] - $has_schedule['discount'];
                $has_schedule['status'] = $post['amount_type'] == 'later' ? 'Pending' : 'Processing';

                $booking['has_schedules'][] = $has_schedule;
            }

            $booking = self::save($booking);

            Model_Notes::create('Course Booking', 'Created via web checkout', $booking['id']);

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            Session::instance()->set('nbs_checkout_new_user_id', null);
            Session::instance()->set('nbs_checkout_new_contact_id', null);
            Session::instance()->set('nbs_checkout_new_student_id', null);
            throw $exc;
        }

        // booking data has been saved, charge card
        if (@$booking['id']) {
            $total = 0;
            if ($booking['total']) {
                $total = $booking['total'];
            } else {
                foreach ($booking['has_schedules'] as $has_schedule) {
                    $total += $has_schedule['total'];
                }
            }

            $booked_schedule = Model_Schedules::get_one_for_details($booking['has_schedules'][0]['schedule_id']);

            $currency = '€';

            $message_params = array();
            $message_params['bookingid'] = $booking['id'];
            $message_params['course'] = $booked_schedule['course'];
            $message_params['schedule'] = $booked_schedule['schedule'];
            $message_params['paymenttype'] = ucwords($post['amount_type']);
            $message_params['deposit'] = $currency.$post['deposit'];
            $message_params['fee'] = $currency.$post['fee'];
            $message_params['total'] = $currency.$total;
            $message_params['status'] = $booking['status'];

            if ($post['amount_type'] == 'later') {
                $result = array(
                    'success' => true,
                    'message' => __('Booking Registered')
                );
                $message_params['status'] = 'Pending';
                Session::instance()->set('nbs_checkout_new_user_id', null);
                Session::instance()->set('nbs_checkout_new_contact_id', null);
                Session::instance()->set('nbs_checkout_new_student_id', null);
            } else {
                if (@$post['payment_method'] == 'cc' || @$post['ccNum']) {
                    if (self::nbs_checkout_process_cc($booking, $post)) {
                        $result = array(
                            'success' => true,
                            'message' => __('Booking Registered')
                        );

                        Session::instance()->set('nbs_checkout_new_user_id', null);
                        Session::instance()->set('nbs_checkout_new_contact_id', null);
                        Session::instance()->set('nbs_checkout_new_student_id', null);
                        $message_params['status'] = 'Confirmed';
                    } else {
                        $result = array(
                            'success' => false,
                            'message' => __('Payment failed')
                        );
                        $message_params['status'] = 'Failed';
                    }
                } else {
                    if ($post['payment_method'] == 'paypal') {
                        $total = 0;
                        if ($booking['total']) {
                            $total = $booking['total'];
                        } else {
                            foreach ($booking['has_schedules'] as $has_schedule) {
                                $total += $has_schedule['total'];
                            }
                        }

                        $schedule = Model_Schedules::get_schedule($booking['has_schedules'][0]['schedule_id']);
                        $result = array(
                            'success' => true,
                            'message' => __('Wait for paypal'),
                            'continue' => 'paypal',
                            'booking_id' => $booking['id'],
                            'amount' => $total,
                            'quantity' => 1,
                            'item_name' => $schedule['location'] . ' ' . $schedule['name']
                        );
                    } else {
                        $result = array(
                            'success' => false,
                            'message' => __('Unexpected Error(' . __LINE__ . ')')
                        );
                    }
                }
            }

            try {
                $mm = new Model_Messaging();
                $mm->send_template(
                    'course-booking-admin',
                    null,
                    date::now(),
                    array(),
                    $message_params
                );
                $mm->send_template(
                    'course-booking-parent',
                    null,
                    date::now(),
                    array(array('target_type' => 'CMS_CONTACT', 'target' => $contact_id)),
                    $message_params
                );
            } catch (Exception $exc) {
                // not a fatal error.

            }
        }

        return $result;
    }

    public static function nbs_checkout_process_cc($booking, $post)
    {
        $total = 0;
        if ($booking['total']) {
            $total = $booking['total'];
        } else {
            foreach ($booking['has_schedules'] as $has_schedule) {
                $total += $has_schedule['total'];
            }
        }

        if ($post['amount_type'] == 'deposit') {
            $amount_to_pay = (float)$post['deposit'];
        } else {
            $amount_to_pay = (float)$post['balance'];
        }

        $processor = new Model_Realvault();
        $ccresult = $processor->charge(
            'course-booking-' . $booking['id'] . (Kohana::$environment != Kohana::PRODUCTION ? date('YmdHis') : ''),
            $amount_to_pay,
            'EUR',
            $post['ccNum'],
            $post['ccExpMM'] . '' . $post['ccExpYY'],
            $post['ccType'],
            $post['first_name'] . ' ' . $post['last_name'],
            $post['ccv']
        );

        if ((string)$ccresult->result == '00') {
            self::set_processing_status($booking['id'], 'Confirmed', '');
            self::make_booking_payment($booking['id'], $amount_to_pay, 'Realex', (string)$ccresult->authcode . ':' . (string)$ccresult->pasref);
            return true;
        } else {
            self::set_processing_status($booking['id'], 'Cancelled', 'Payment Failed');
            return false;
        }
    }

    public static function make_booking_payment($booking_id, $amount, $gateway, $txinfo)
    {
        $transactions = self::get_transactions($booking_id, array('Business'));

        if (count($transactions) > 0){
            foreach ($transactions as $transaction) {
                if ($amount > 0) {
                    $payment = array();
                    $payment['type'] = 'Payment';
                    $payment['currency'] = $transaction['currency'];
                    $payment['amount'] = min($amount, $transaction['total']);
                    $payment['to_transaction_id'] = $transaction['id'];
                    $payment['gateway'] = $gateway;
                    $payment['gateway_tx_reference'] = $txinfo;
                    $payment['status'] = 'Completed';
                    Model_TransactionPayments::save($payment);
                    $amount -= $payment['amount'];
                }
            }
        } else {
            // this should not happen normallly
            // save the payment anyway so not loose it
            $payment = array();
            $payment['type'] = 'Payment';
            $payment['currency'] = '';
            $payment['amount'] = $amount;
            $payment['to_transaction_id'] = null;
            $payment['gateway'] = $gateway;
            $payment['gateway_tx_reference'] = $txinfo;
            $payment['status'] = 'Completed';
            Model_TransactionPayments::save($payment);
        }
    }

    /*
     * older imported functions
     *
     * */

    const STATUS_S_OK = 0;
    const STATUS_E_ERROR = -1;
    const STATUS_E_MISSING_OPTIONS = -2;
    const STATUS_E_WRONG_COUPON_CODE = -3;
    const FIELD_DELIMITER = ",";

    private static function generate_response($status, $response_data)
    {
        $response = new stdClass();

        $response->status = $status;
        $response->data = $response_data;

        return $response;
    }

    public static function save_ajax_booking_with_cart($data)
    {
        if (self::validate_no_empty($data['schedule_id']) === false)
            return false;
        if (self::validate_no_empty($data['student_first_name']) === false)
            return false;
        if (self::validate_no_empty($data['student_last_name']) === false)
            return false;
        if (self::validate_no_empty($data['student_email']) === false)
            return false;


        try {
            Database::instance()->begin();
            $contact = new Model_Contacts();
            $contact->set_first_name($data['student_first_name']);
            $contact->set_last_name($data['student_last_name']);
            $contact->set_email($data['student_email']);
            $contact->set_address1(@$data['student_address1'] ?: @$data['address1']);
            $contact->set_address2(@$data['student_address2'] ?: @$data['address2']);
            $contact->set_address3(@$data['county_id'] ?: @$data['county']);
            $contact->set_address4(@$data['country']);
            if (@$data['county']) {
                $contact->set_country_id(1);
            }
            $contact->set_phone(@$data['phone']);
            $contact->set_mobile(@$data['student_mobile'] ?: @$data['mobile_code'] . '' . @$data['mobile_number']);
            $contact->set_notes($data['comments']);
            $contact->test_existing_email = false;
            $contact->set_mailing_list('Student');
            $contact->save();
            $contact_id = $contact->get_id();

            $booking = array(
                'student_id' => $contact_id,
                'payer_id' => $contact_id,
                'status' => 'Processing',
                'has_schedules' => array(
                    array(
                        'schedule_id' => $data['schedule_id'],
                        'currency' => 'EUR',
                        'fee' => (float)$data['subtotal'],
                        'discount' => (float)$data['discount'],
                        'total' => (float)$data['amount'],
                        'status' => 'Processing',
                        'has_timeslots' => array(
                            array(
                                'timeslot_id' => $data['event_id'],
                                'attend' => 1
                            )
                        )
                    )
                )
            );

            $booking = self::save($booking);

            $return = false;
            if ($booking['id']) {
                $admin_fee = (Settings::instance()->get('admin_fee_toggle') === 'TRUE') ? Settings::instance()->get('admin_fee_price') : 0;
                $return['success'] = 1;
                $return['booking'] = $booking['id'];
                $session = Session::instance();
                $session->delete('bookings');
                $_bookings = $session->get('bookings');
                $_bookings['cart'][$booking['id']]['title'] = $data['training'];
                $_bookings['cart'][$booking['id']]['schedule'] = $data['schedule_id'];
                $_bookings['cart'][$booking['id']]['schedule_d'] = $data['schedule'];
                $_bookings['cart'][$booking['id']]['price'] = $data['price'];
                $_bookings['cart'][$booking['id']]['bid'] = $booking['id'];
                if (isset($_bookings['cart']['amount'])) {
                    $_bookings['cart']['amount'] = $_bookings['cart']['amount'] + $data['price'];
                } else {
                    $_bookings['cart']['amount'] = $data['price'] + $admin_fee;
                }

                if (Model_Plugin::is_enabled_for_role('Administrator', 'franchisee')) {
                    $fr_schedule = Model_Schedules::get_schedule($data['schedule_id']);
                    if (is_numeric($fr_schedule['owned_by'])) {
                        $return['franchisee_account'] = Model_Event::accountDetailsLoad($fr_schedule['owned_by']);
                    }
                }
                $session->set('bookings', $_bookings);
            }

            Database::instance()->commit();
            return $return;
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    private static function validate_no_empty($data, $minlength = 1, $maxlength = false)
    {
        $minlength--;
        if ($maxlength === false) {
            if (isset($data) AND strlen($data) > $minlength) {
                return true;
            } else {
                return false;
            }
        } else {
            $maxlength++;
            if (isset($data) AND strlen($data) > $minlength AND strlen($data) < $maxlength) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function cart_paid($code, $amount, $channel, $address1 = NULL, $address2 = NULL, $channel_info = '')
    {
        if (isset($code) AND strlen($code) > 0) {
            $code = substr($code, 0, -1);
            $booking_ids = explode('|', $code);

            foreach ($booking_ids as $booking_id) {
                self::set_processing_status($booking_id, 'Confirmed', 'Payment Success');
                $transactions = self::get_transactions($booking_id, array('Business'));
                foreach ($transactions as $transaction) {
                    if ($amount > 0) {
                        $payment = array();
                        $payment['type'] = 'Payment';
                        $payment['currency'] = $transaction['currency'];
                        $payment['amount'] = min($amount, $transaction['total']);
                        $payment['to_transaction_id'] = $transaction['id'];
                        $payment['gateway'] = $channel;
                        $payment['gateway_tx_reference'] = json_encode($channel_info);
                        $payment['status'] = 'Completed';
                        Model_TransactionPayments::save($payment);
                        $amount -= $payment['amount'];
                    }
                }
            }
        }
    }

    public static function paypal_handler_old($booking_id, $amount, $txinfo = '')
    {
        self::set_processing_status($booking_id, 'Confirmed', 'Payment Success');
        $transactions = self::get_transactions($booking_id, array('Business'));
        foreach ($transactions as $transaction) {
            if ($amount > 0) {
                $payment = array();
                $payment['type'] = 'Payment';
                $payment['currency'] = $transaction['currency'];
                $payment['amount'] = min($amount, $transaction['total']);
                $payment['to_transaction_id'] = $transaction['id'];
                $payment['gateway'] = 'Paypal';
                $payment['gateway_tx_reference'] = $txinfo;
                $payment['status'] = 'Completed';
                Model_TransactionPayments::save($payment);
                $amount -= $payment['amount'];
            }
        }
    }

    public function get_paypal_data($data)
    {
        try
        {
            $settings = Settings::instance();
            $business = FALSE;

            $ok = (isset($data->amount) AND ($business = $settings->get('paypal_email')) !== FALSE);

            if ($ok)
            {
                $form_data = new stdClass();

                // General
                $form_data->cmd = '_xclick';
                $form_data->upload = 1;
                $form_data->business = $business;
                $form_data->currency_code = 'EUR';
                $form_data->no_shipping = 1;
                $form_data->return = isset($data->return_url) ? URL::site($data->return_url) : URL::site("/");
                $form_data->cancel_return = isset($data->cancel_return_url) ? $data->cancel_return_url : URL::site("/");
                $form_data->notify_url = URL::site('/frontend/payments/paypal_callback/booking');

                // Schedule
                $form_data->item_name = $data->title;
                $form_data->amount = $data->amount;
                $form_data->quantity = 1;
                $form_data->custom = $data->custom;

                $status = self::STATUS_S_OK;
                $response_data = $form_data;
            }
            else
            {
                $status = self::STATUS_E_ERROR;
                $response_data = NULL;
            }
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getTraceAsString());

            $status = self::STATUS_E_ERROR;
            $response_data = NULL;
        }

        return self::generate_response($status, $response_data);
    }

	public static function render_booking_data()
	{
		$booking_id = Session::instance()->get('last_course_booking_id');
		$booking = Model_CourseBookings::load($booking_id);

		Session::instance()->delete('last_course_booking_id');

		return View::factory('front_end/course_booking_details')->set('booking', $booking);
	}

    public static function migrate_old_bookings()
    {
        $contact_id_cache = array();
        try {
            Database::instance()->begin();

            $old_bookings = DB::select('*')
                ->from('plugin_courses_bookings_migrate')
                ->execute()
                ->as_array();
            foreach ($old_bookings as $i => $old_booking) {
                if ($old_booking['payment_details'] && $old_booking['payment_details'] != '' && $old_booking['payment_details'] != 'Free course') {
                    if (@unserialize($old_booking['payment_details'])) {
                        $old_booking['payment_details'] = @unserialize($old_booking['payment_details']);
                    } else if (@json_decode($old_booking['payment_details'], true)) {
                        $old_booking['payment_details'] = @json_decode($old_booking['payment_details'], true);
                    }
                }
                if (isset($old_booking['data'])) {
                    $old_booking['data'] = unserialize($old_booking['data']);
                    $old_bookings[$i] = $old_booking;
                    if (!isset($contact_id_cache[$old_booking['data']['student_email']])) {
                        $contact = new Model_Contacts();
                    } else {
                        $contact = new Model_Contacts($contact_id_cache[$old_booking['data']['student_email']]);
                    }
                    $contact->test_existing_email = false;
                    if (@$old_booking['data']['student_title']) {
                        $contact->set_title($old_booking['data']['student_title']);
                    }
                    $contact->set_first_name($old_booking['data']['student_first_name']);
                    $contact->set_last_name($old_booking['data']['student_last_name']);
                    if (@$old_booking['data']['student_mobile']) {
                        $contact->set_mobile($old_booking['data']['student_mobile']);
                    }
                    $contact->set_phone($old_booking['data']['student_phone']);
                    $contact->set_address1($old_booking['data']['student_address1']);
                    if ($old_booking['data']['student_address2']) {
                        $contact->set_address2($old_booking['data']['student_address2']);
                    }
                    $contact->set_email($old_booking['data']['student_email']);
                    $contact->set_mailing_list('Student');
                    $contact->save();
                    $contact_id = $contact->get_id();
                    $contact_id_cache[$old_booking['data']['student_email']] = $contact_id;

                    if (isset($old_booking['data']['guardian_first_name'])) {
                        if (!isset($contact_id_cache[$old_booking['data']['guardian_email']])) {
                            $parent = new Model_Contacts();
                        } else {
                            $parent = new Model_Contacts($contact_id_cache[$old_booking['data']['guardian_email']]);
                        }
                        $parent->test_existing_email = false;
                        $parent->set_title($old_booking['data']['guardian_title']);
                        $parent->set_first_name($old_booking['data']['guardian_first_name']);
                        $parent->set_last_name($old_booking['data']['guardian_last_name']);
                        $parent->set_mobile($old_booking['data']['guardian_mobile']);
                        $parent->set_phone($old_booking['data']['guardian_phone']);
                        $parent->set_address1($old_booking['data']['guardian_address1']);
                        $parent->set_email($old_booking['data']['guardian_email']);
                    } else {
                        if (!isset($contact_id_cache[$old_booking['data']['email']])) {
                            $parent = new Model_Contacts();
                        } else {
                            $parent = new Model_Contacts($contact_id_cache[$old_booking['data']['email']]);
                        }
                        $parent->test_existing_email = false;
                        $parent->set_title($old_booking['data']['title']);
                        $parent->set_first_name($old_booking['data']['first_name']);
                        $parent->set_last_name($old_booking['data']['last_name']);
                        $parent->set_mobile($old_booking['mobile']);
                        $parent->set_phone($old_booking['data']['phone']);
                        $parent->set_address1($old_booking['data']['address1']);
                        $parent->set_email($old_booking['data']['email']);
                    }
                    $parent->set_mailing_list('Parent/Guardian');
                    $parent->save();
                    $parent_id = $parent->get_id();
                    if (isset($old_booking['data']['guardian_email'])) { // some difference between kes and others(stac etc.)
                        $contact_id_cache[$old_booking['data']['guardian_email']] = $parent_id;
                    } else {
                        $contact_id_cache[$old_booking['data']['email']] = $parent_id;
                    }
                } else {
                    if (!isset($contact_id_cache[$old_booking['email']])) {
                        $parent = new Model_Contacts();
                    } else {
                        $parent = new Model_Contacts($contact_id_cache[$old_booking['email']]);
                    }
                    $parent->test_existing_email = false;
                    $parent->set_title('');
                    $parent->set_first_name($old_booking['first_name']);
                    $parent->set_last_name($old_booking['last_name']);
                    $parent->set_mobile($old_booking['mobile']);
                    $parent->set_phone($old_booking['phone']);
                    $parent->set_address1($old_booking['address']);
                    $parent->set_email($old_booking['email']);
                    $parent->set_mailing_list('Parent/Guardian');
                    $parent->save();
                    $parent_id = $parent->get_id();
                    $contact_id_cache[$old_booking['email']] = $parent_id;
                }

                $family = Model_Families::get_family_of($parent_id);
                if (!$family) {
                    if (isset($old_booking['data']['guardian_last_name'])) {
                        $family_id = Model_Families::set_family('', $old_booking['data']['guardian_last_name'], 1, 0, $parent_id);
                    } else {
                        $family_id = Model_Families::set_family('', $old_booking['last_name'], 1, 0, $parent_id);
                    }
                    Model_Families_Members::add_family_member($family_id, $parent_id, 'Parent');
                }

                if (@$contact_id) {
                    if (!Model_Families::get_family_of($contact_id)) {
                        Model_Families_Members::add_family_member($family_id, $contact_id, 'Student');
                    }
                } else {
                    $contact_id = $parent_id;
                }


                $booking = array(
                    'student_id' => $contact_id,
                    'payer_id' => $contact_id,
                    'status' => $old_booking['paid'] == 1 ? 'Confirmed' : 'Cancelled',
                    'has_schedules' => array(
                    )
                );

                if (isset($old_booking['data']['schedule_id'])) {
                    $has_schedule['schedule_id'] = $old_booking['data']['schedule_id'];
                    $has_schedule['has_timeslots'] = array();
                    if (isset($old_booking['data']['event_id']) && $old_booking['data']['event_id']) {
                        $has_schedule['has_timeslots'][] = array(
                            'timeslot_id' => $old_booking['data']['event_id'],
                            'attend' => 1
                        );
                    } else {
                        $timeslots = Model_Schedules::get_all_schedule_timeslots($old_booking['data']['schedule_id']);
                        foreach ($timeslots as $ti => $timeslot) {
                            $has_schedule['has_timeslots'][$ti]['timeslot_id'] = $timeslot['id'];
                            $has_schedule['has_timeslots'][$ti]['attend'] = 1;
                        }
                    }
                } else if (isset($old_booking['schedule_id'])) {
                    $has_schedule['schedule_id'] = $old_booking['schedule_id'];
                    $has_schedule['has_timeslots'] = array();
                    $timeslots = Model_Schedules::get_all_schedule_timeslots($old_booking['schedule_id']);
                    foreach ($timeslots as $ti => $timeslot) {
                        $has_schedule['has_timeslots'][$ti]['timeslot_id'] = $timeslot['id'];
                        $has_schedule['has_timeslots'][$ti]['attend'] = 1;
                    }
                }


                $has_schedule['currency'] = 'EUR';
                $has_schedule['fee'] = isset($old_booking['data']['price']) ? $old_booking['data']['price'] : (isset($old_booking['payment_details']['amount']) ? $old_booking['payment_details']['amount'] : 0);
                $has_schedule['discount'] = 0;
                $has_schedule['total'] = $has_schedule['fee'] - $has_schedule['discount'];
                $has_schedule['status'] = $old_booking['paid'] == 1 ? 'Confirmed' : 'Cancelled';

                $booking['has_schedules'][] = $has_schedule;

                $booking = self::save($booking);

                if ($old_booking['paid'] == 1) {
                    self::set_processing_status($booking['id'], 'Confirmed', '');
                    self::make_booking_payment(
                        $booking['id'],
                        isset($old_booking['data']['price']) ? $old_booking['data']['price'] : (isset($old_booking['payment_details']['amount']) ? $old_booking['payment_details']['amount'] : 0),
                        @$old_booking['payment_details']['txn_id'] ? 'PayPal' : @$old_booking['payment_details']['channel'],
                        'Imported data: ' . @$old_booking['payment_details']['payment_date'] . @$old_booking['payment_details']['txn_id']
                    );
                }

                Model_Notes::create('Course Booking', 'Imported from old bookings', $booking['id']);

                DB::delete('plugin_courses_bookings_migrate')->where('id', '=', $old_booking['id'])->execute();
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }

        //header('content-type: text/plain');print_r($old_bookings);exit;
    }

    public static function transfer_booking($post)
    {
        try {
            Database::instance()->begin();

            $user = Auth::instance()->get_user();

            $old_booking = self::load($post['booking_id']);

            $new_booking = array(
                'student_id' => $old_booking['student_id'],
                'payer_id' => $old_booking['student_id'],
                'status' => $old_booking['status'],
                'has_schedules' => array()
            );

            foreach ($post['transfer'] as $i => $transfer) {
                $has_schedule = array();
                $has_schedule['schedule_id'] = $transfer['to_schedule_id'];
                $timeslots_to_calculate = array();
                if (isset($transfer['to_timeslot_id']) && is_numeric($transfer['to_timeslot_id'])) {
                    $has_schedule['has_timeslots'][] = array(
                        'attend' => 1,
                        'timeslot_id' => $transfer['to_timeslot_id']
                    );
                    $timeslots_to_calculate[] = $transfer['to_timeslot_id'];
                } else { // all timeslots are going to be booked
                    $has_schedule['has_timeslots'] = array();
                    $timeslots = Model_Schedules::get_all_schedule_timeslots($has_schedule['schedule_id']);
                    foreach ($timeslots as $ti => $timeslot) {
                        $has_schedule['has_timeslots'][$ti]['timeslot_id'] = $timeslot['id'];
                        $has_schedule['has_timeslots'][$ti]['attend'] = 1;
                    }
                }

                $has_schedule['currency'] = 'EUR';
                $has_schedule['fee'] = self::calculate_booking_cost(
                    $has_schedule['schedule_id'],
                    count($timeslots_to_calculate) ? $timeslots_to_calculate : null
                );
                $has_schedule['discount'] = 0;
                $has_schedule['total'] = $has_schedule['fee'] - $has_schedule['discount'];
                $has_schedule['status'] = $old_booking['status'];

                $new_booking['has_schedules'][] = $has_schedule;

                DB::update(self::TABLE_HAS_SCHEDULES)
                    ->set(array('updated' => date::now(), 'updated_by' => $user['id'], 'status' => 'Cancelled'))
                    ->and_where('id', '=', $transfer['from_bookingschedule_id'])
                    ->and_where('deleted', '=', 0)
                    ->execute();
            }

            if (count($old_booking['has_schedules']) == 1) {
                DB::update(self::TABLE_BOOKINGS)
                    ->set(array('updated' => date::now(), 'updated_by' => $user['id'], 'status' => 'Cancelled'))
                    ->and_where('id', '=', $old_booking['id'])
                    ->and_where('deleted', '=', 0)
                    ->execute();
            }

            $new_booking = self::save($new_booking);

            $old_booking = self::load($post['booking_id']);
            $new_booking = self::load($new_booking['id']);

            $transferrable_payments = array();
            foreach ($old_booking['has_transactions'] as $old_transaction) {
                foreach ($post['transfer'] as $i => $transfer) {
                    if ($old_transaction['booking_has_schedule_id'] == $transfer['from_bookingschedule_id']) {
                        if (count($old_transaction['payments']) > 0) {
                            foreach ($old_transaction['payments'] as $old_payment) {
                                $transferrable_payments[] = $old_payment;
                            }
                            $old_transaction['status'] = 'Completed';
                        } else {
                            $old_transaction['status'] = 'Cancelled';
                        }

                        Model_Transactions::save($old_transaction);
                        Model_Transactions::update_outstanding($old_transaction['id']);
                    }
                }
            }

            foreach ($transferrable_payments as $transferrable_payment) {
                foreach ($new_booking['has_transactions'] as $new_transaction) {
                    $payment = array();
                    $payment['type'] = 'Credit';
                    $payment['currency'] = $new_transaction['currency'];
                    $payment['amount'] = $transferrable_payment['amount'];
                    $payment['to_transaction_id'] = $new_transaction['id'];
                    $payment['from_transaction_id'] = $transferrable_payment['to_transaction_id'];
                    $payment['gateway_id'] = $transferrable_payment['gateway_id'];
                    $payment['gateway_tx_reference'] = '';
                    $payment['status'] = 'Completed';
                    Model_TransactionPayments::save($payment);

                }
            }

            $new_booking = self::load($new_booking['id']);

            //Database::instance()->rollback();
            Database::instance()->commit();
            return array(
                'old_booking' => $old_booking,
                'new_booking' => $new_booking
            );
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function save_discounts($booking, $user = null)
    {
        if ($user == null) {
            $user = auth::instance()->get_user();
        }

        if (isset($booking['has_discounts']))
        foreach ($booking['has_discounts'] as $i => $has_discount) {
            self::save_discount($booking['id'], $has_discount['schedule_id'], $has_discount['discount_id'], $has_discount['amount']);
        }
    }

    public static function save_discount($booking_id, $schedule_id, $discount_id, $amount, $memo = '')
    {
        if (!$schedule_id) {
            $schedule_id = null;
        }
        $eq = DB::select('booking_id','discount_id')
            ->from(self::TABLE_HAS_DISCOUNTS)
            ->where('booking_id','=',$booking_id);
        if ($discount_id == null) {
            $eq->and_where('discount_id', 'is', null);
        } else {
            $eq->and_where('discount_id', '=', $discount_id);
        }
        if ($schedule_id == null) {
            $eq->and_where('schedule_id', 'is', null);
        } else {
            $eq->and_where('schedule_id', '=', $schedule_id);
        }


        $exists = $eq
            ->execute()
            ->as_array();
        if ($exists) {
            $uq = DB::update(self::TABLE_HAS_DISCOUNTS)
                ->set(array('amount' => $amount, 'memo' => $memo))
                ->where('booking_id', '=', $booking_id);
            if ($discount_id == null) {
                $uq->and_where('discount_id', 'is', null);
            } else {
                $uq->and_where('discount_id', '=', $discount_id);
            }
            if ($schedule_id == null) {
                $uq->and_where('schedule_id', 'is', null);
            } else {
                $uq->and_where('schedule_id', '=', $schedule_id);
            }
            $discount = $uq->execute();
        } else {
            $discount = DB::insert(self::TABLE_HAS_DISCOUNTS, array('booking_id', 'schedule_id', 'discount_id', 'amount', 'memo'))
                ->values(array($booking_id, $schedule_id, $discount_id, $amount, $memo))->execute();
        }

        return $discount;
    }

    public static function get_available_discounts($client_id, $items, $base_student_count = 0, $coupon_code = null)
    {
        $result = array();
        $discounts = Model_Coursediscounts::get_all_discounts_for_listing();
        foreach ($items as $i => $item) {

            // apply available discounts
            foreach ($discounts as $discount) {
                if ($discount['publish'] == 0) {
                    continue;
                }
                $ignore = 0;
                $no_check = false;

                $discount_o = Model_Coursediscounts::create($discount['id']);
                $discount_o->base_student_count = $base_student_count;
                if ($no_check) {
                    $discount_amount = $discount_o->calculate_discount_no_check($client_id, $items, $item['id']);
                } else {
                    $discount_amount = $discount_o->calculate_discount($client_id, $items, $item['id'], $coupon_code);
                }

                if ($discount_amount > $item['fee']) {
                    $discount_amount = (float)$item['fee'];
                }
                if ($discount_amount > 0) {

                    $ignore_others = $discount_o->ignore_others() || $discount_amount >= $item['fee'];
                    $item['discounts'][] = array(
                        'id' => $discount['id'],
                        'amount' => $discount_amount,
                        'title' => $discount['title'],
                        'code' => $discount['code'],
                        'ignore' => $ignore,
                        'custom' => 0,
                        'memo' => '',
                        'ignore_others' => $ignore_others
                    );
                    if ($ignore == 0) {
                        $item['discount'] += $discount_amount;
                        $item['total'] = $item['fee'] - $item['discount'];
                    }

                    $result[$i] = $item;

                    if ($ignore_others && !$ignore) { // discounts like 100%, or quantity
                        foreach ($item['discounts'] as $di => $discountx) {
                            if (!@$discountx['ignore_others']) {
                                unset($item['discounts'][$di]);
                            }
                        }
                        $item['discounts'] = array_values($item['discounts']);
                        $item['discount'] = $discount_amount;
                        $item['total'] = $item['fee'] - $item['discount'];
                        $result[$i] = $item;

                        break;
                    }
                }
            }
        }

        return $result;
    }
}