<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course_Schedule extends ORM
{
    protected $_table_name = 'plugin_courses_schedules';
    protected $_deleted_column = 'delete';

    protected $_belongs_to = [
        'content'       => ['model' => 'Content',           'foreign_key' => 'content_id'],
        'course'        => ['model' => 'Course',            'foreign_key' => 'course_id'],
        'delivery_mode' => ['model' => 'Lookup',            'foreign_key' => 'delivery_mode_id'],
        'learning_mode' => ['model' => 'Lookup',            'foreign_key' => 'learning_mode_id'],
        'location'      => ['model' => 'Course_Location',   'foreign_key' => 'location_id'],
        'study_mode'    => ['model' => 'Course_StudyMode',  'foreign_key' => 'study_mode_id'],
        'trainer'       => ['model' => 'Contacts3_Contact', 'foreign_key' => 'trainer_id'],
    ];

    protected $_has_many = [
        'timeslots' => ['model' => 'Course_Schedule_Event', 'foreign_key' => 'schedule_id']
    ];

    /*
     * Get currently available timeslots
     * Schedules that have
     * ... "book on website" set to "yes"
     * ... "publish" set to "yes"
     * ... "delete" set to "no"
     * ... have timeslots in the future (This restriction can be turned off.)
     * ...... OR are self-paced (which doesn't use timeslots)
     */
    public function where_available($args = [])
    {
        $q = $this
            ->with('learning_mode')
            ->join(['plugin_courses_schedules_events', 'timeslot'], 'left')
                ->on('timeslot.schedule_id', '=', 'course_schedule.id')
                ->on('timeslot.delete', '=', DB::expr("0"))
            ->where('course_schedule.book_on_website', '=', 1)
            ->where('course_schedule.schedule_status', '<>', Model_Schedules::CANCELLED)
            ->where('course_schedule.publish', '=', 1)
            ->where('course_schedule.delete', '=', 0);

        // Toggle this option to include/exclude timeslots in the past.
        if (empty($args['include_past'])) {
            $q = $q
                ->and_where_open()
                ->where('timeslot.datetime_start', '> ', date('Y-m-d H:i:s'))
                ->or_where('learning_mode.value', '=', 'self_paced')
                ->and_where_close();
        }

        if (Settings::instance()->get('only_display_navision_courses')) {
            $q
                ->join([Model_NAVAPI::TABLE_EVENTS, 'nav_event'], 'inner')
                ->on('nav_event.schedule_id', '=', 'course_schedule.id');
        }

        return $q
            ->group_by('course_schedule.id')
            ->and_having_open()
                ->having(DB::expr("COUNT(`timeslot`.`id`)"), '>', 0)
                ->or_having(DB::expr("GROUP_CONCAT(`learning_mode`.`value`)"), 'like', '%self_paced%')
            ->and_having_close();
    }

    // Get schedules, available on the frontend, ordered by the date of each schedules' earliest timeslot.
    public function where_available_by_date($filters = [])
    {
        $schedule_filters = !empty($filters['unstarted_only']) ? ['unstarted_only' => true] : [];

        $schedules = $this
            ->select(['timeslot.id', 'timeslot_id'])
            ->select(['timeslot.datetime_start', 'timeslot_date'])
            ->where_available($schedule_filters)
            ->apply_filters($filters)
            ->group_by('course_schedule.id')
            ->order_by('timeslot.datetime_start', 'asc');

        // Toggle whether or not past dates are included.
        if (!empty($filters['unstarted_only'])) {
            $schedules->where('timeslot.datetime_start', '>', date('Y-m-d H:i:s'));
        }

        return $schedules;
    }

    public function apply_filters($filters = [])
    {
        if (!empty($filters)) {
            $search = Model_Courses::filter($filters);
            $schedule_ids = array_unique(array_column($search['all_data'], 'schedule_id'));

            if (!empty($schedule_ids)) {
                return $this->where('course_schedule.id', 'in', $schedule_ids);
            }
        }

        return $this;
    }

    // Get the date of the next timeslot
    public function get_next_timeslot($args = [])
    {
        $q = $this->timeslots;

        if (empty($args['include_past'])) {
            $q = $q->where('datetime_start', '>', date('Y-m-d H:i:s'));
        }

        return $q->order_by('datetime_start')->find_undeleted();
    }

    // Get the  discount objects applicable to this course according to args
    // If none, return false
    public function get_discounts($args = [])
    {
        $discounts = Model_KES_Discount::get_all_discounts_for_listing(array('publish_on_web' => 1));
        $discount_array = array();
        foreach ($discounts as $discount) {
            $discount = new Model_KES_Discount($discount['id']);
            if (@$args['member_only'] == true) {
                if ($discount->get_member_only() &&
                    $discount->test_unassigned() &&
                    ($discount->test_matching_schedules(array(array('id' => $this->id)))
                        || $discount->test_matching_courses(array(array('id' => $this->id))))) {
                    $logged_in_contact = Auth::instance()->get_contact();
                    $course_item = array(array(
                        'name' => $this->course->title,
                        'fee' => '',
                        'fee_per' => $this->course->schedule_fee_per,
                        'id' => $this->course->id,
                        'paymentoption_id' => 0,
                        'prepay' => null,
                        'total' => 0,
                        'type' => 'course',
                        'payg_fee' => 0,
                        'cc_fee' => 0,
                        'sms_fee' => 0,
                        'booking_fees' => 0,
                        'discounts' => array(),
                        'payment_method' => null,
                    ));
                    $schedule_item = array(array(
                        'fee' => '',
                        'id' => $this->id,
                        'paymentoption_id' => 0,
                        'prepay' => null,
                        'total' => 0,
                        'type' => 'course',
                        'payg_fee' => 0,
                        'cc_fee' => 0,
                        'sms_fee' => 0,
                        'booking_fees' => 0,
                        'discounts' => array(),
                        'payment_method' => null,
                    ));
                    $discount->calculate_discount($logged_in_contact->id, $schedule_item, $this->id, $discount->get_code());
                    foreach($discount->failing_conditions as $id => $failing) {

                        if ($failing->title == 'User must be a member to get this discount') {
                            unset($discount->failing_conditions[$id]);
                        }
                    }
                    $apply_to = $discount->get_apply_to();
                    if (empty($discount->failing_conditions) && $apply_to != 'Cart' && $apply_to != 'Code' && empty($discount->get_code())) {
                        if (isset($args['publish_on_web'])) {
                            if ($discount->get_publish_on_web() == ($args['publish_on_web'] ? 1 : 0)) {
                                $discount_array[] =  $discount;
                            }
                        } else {
                            $discount_array[] =  $discount;
                        }
                    }
                }
            } elseif(@$args['member_only'] == false) {
                if(!$discount->get_member_only()
                    && $discount->test_unassigned()
                    && $discount->test_matching_schedules([['id' => $this->id]])) {
                    $logged_in_contact = Auth::instance()->get_contact();
                    $schedule_item = array(array(
                        'fee' => '',
                        'id' => $this->id,
                        'paymentoption_id' => 0,
                        'prepay' => null,
                        'total' => 0,
                        'type' => 'course',
                        'payg_fee' => 0,
                        'cc_fee' => 0,
                        'sms_fee' => 0,
                        'booking_fees' => 0,
                        'discounts' => array(),
                        'payment_method' => null,
                    ));
                    $discount->calculate_discount($logged_in_contact->id, $schedule_item, $this->id, $discount->get_code());
                    $apply_to = $discount->get_apply_to();
                    if (empty($discount->failing_conditions) && $apply_to != 'Code' && empty($discount->get_code())) {
                        if (isset($args['publish_on_web'])) {
                            if ($discount->get_publish_on_web() == ($args['publish_on_web'] ? 1 : 0)) {
                                $discount_array[] =  $discount;
                            }
                        } else {
                            $discount_array[] =  $discount;
                        }
                    }
                }
            }
        }
        if (!empty($discount_array)) {
            return $discount_array;
        }
        return false;
    }

    /**
     * Calculate the subtotal for the initial payment
     *
     * @param $timeslot_ids  array  List of IDs of timeslots being booked, if relevant
     * @return number The amount due
     */
    public function get_subtotal_due($timeslot_ids = [])
    {
        $payment_types = Model_KES_Bookings::$payment_types;

        // Free trial or non-pre-pay => nothing to pay yet
        if ($this->trial_timeslot_free_booking || strtolower($payment_types[$this->payment_type]) != 'pre-pay') {
            return 0;
        }

        // Self paced does not have timeslots. Just one fixed sum.
        if ($this->learning_mode->value == 'self_paced') {
            return $this->fee_amount;
        }

        // Fee per timeslot => charge the fee once for each timeslot being booked
        if ($this->fee_per == 'Timeslot') {
            return $this->fee_amount * count($timeslot_ids);
        }

        return $this->fee_amount;
    }
}