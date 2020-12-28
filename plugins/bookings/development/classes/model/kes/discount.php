<?php

/**
 * Created by PhpStorm.
 * User: dale
 * Date: 14/11/2014
 * Time: 14:34
 * This is not ideally modelled - couldn't think of a better way with time allotted.
 *
 */
class Model_KES_Discount extends Model
{
    /*** Finals & Constants ***/
    CONST DISCOUNTS_TABLE = 'plugin_bookings_discounts';
    CONST FOR_CONTACTS_TABLE = 'plugin_bookings_discounts_for_contacts';
    CONST DISCOUNTS_TYPE_TABLE = 'plugin_bookings_discounts_types';
    const HAS_SCHEDULES_TABLE = 'plugin_bookings_discounts_has_schedules';
    const HAS_COURSES_TABLE = 'plugin_bookings_discounts_has_courses';
    const HAS_PREVIOUS_BOOKING_CONDITION_TABLE = 'plugin_bookings_discounts_has_previous_booking_condition';
    const HAS_PREVIOUS_BOOKING_CONDITION_TYPES_TABLE = 'plugin_bookings_discounts_previous_booking_condition_types';
    const HAS_STUDENT_YEARS_TABLE = 'plugin_bookings_discounts_student_years';
    const DAILY_RATES_TABLE = 'plugin_bookings_discounts_daily_rates';
    const PER_DAY_RATES_TABLE = 'plugin_bookings_discounts_per_day_rates';
    const QTY_RATES_TABLE = 'plugin_bookings_discounts_quantity_rates';

    /*** Private Member Data ***/
    private $id = NULL;
    private $title = '';
    private $summary = '';
    private $image_id = '';
    private $type = 0;
    private $code = '';
    private $x = 0;
    private $y = 0;
    private $z = 0;
    private $from = 0;
    private $to = 0;
    private $valid_from = null;
    private $valid_to = null;
    private $publish = 1;
    private $publish_on_web = 0;
    private $delete = 0;
    private $categories = '';
    private $amount_type = 'Percent';
    private $amount = '';
    private $schedule_type = 'Prepay,PAYG';
    private $item_quantity_min = null;
    private $item_quantity_max = null;
    private $item_quantity_type = 'Courses';
    private $item_quantity_scope = null;
    private $min_students_in_family = null;
    private $max_students_in_family = null;
    private $usage_limit = null;
    private $max_usage_per = null;
    private $apply_to = null;
    private $member_only = false;

    private $for_contacts = array();
    private $student_years = array();
    private $daily_rates = array();
    private $per_day_rates = array();
    private $qty_rates = array();

    private $is_package = 0;
    private $has_schedules = array();
    private $has_courses = array();
    private $has_previous_discount_conditions = array();
    private $previous_term_paid_from = null;
    private $previous_term_paid_to = null;
    private $action_type = null;
    private $application_type = 'initial';
    private $application_order = 1;

    private $min_days = 1;
    private $min_days_is_consecutive = 0;
    private $days_of_the_week = array();
    private $min_number_of_classes = 0;


    private $has_previous_courses = null;
    private $has_previous_schedules = null;
    private $has_previous_category = null;
    private $course_date_from = null;
    private $course_date_to = null;
    private $class_time_from = null;
    private $class_time_to = null;


    /*** Public Member Data ***/
    public $type_title = '';
    public $failing_conditions = array();

    /*** Public Functions ***/
    public function __construct($id = NULL)
    {

        if (is_numeric($id)) {
            $this->set_id($id);
        }

        $this->init();
    }


    public function get_action_type()
    {
        return $this->action_type;
    }

    public function set($data)
    {

        foreach ($data AS $key => $value) {


            if (property_exists($this, $key)) {

                $this->{$key} = $value;

            }
        }


        $this->x = is_array($this->x) ? implode(',', $this->x) : $this->x;
        $this->y = is_array($this->y) ? implode(',', $this->y) : $this->y;
        $this->z = is_array($this->z) ? implode(',', $this->z) : $this->z;
        $this->categories = is_array($this->categories) ? implode(',', $this->categories) : $this->categories;
        if (count($this->has_schedules)) {
            $this->is_package = 1;
        }
        if (count($this->has_courses)) {
            $this->is_package = 1;
        }


        return $this;
    }

    public function set_id($id = NULL)
    {
        $this->id = is_numeric($id) ? intval($id) : $this->id;
        return $this;
    }

    public function set_title($title)
    {
        $this->title = is_string($title) ? $title : $this->title;
        return $this;
    }

    public function set_summary($summary)
    {
        $this->summary = is_string($summary) ? $summary : $this->summary;
        return $this;
    }

    public function set_type($type)
    {
        $this->type = is_numeric($type) ? intval($type) : $this->type;
        return $this;
    }

    public function set_x($x)
    {
        $this->$x = is_numeric($x) ? intval($x) : $this->x;
        return $this;
    }

    public function set_y($y)
    {
        $this->y = is_numeric($y) ? intval($y) : $this->y;
        return $this;
    }

    public function set_z($z)
    {
        $this->z = is_numeric($z) ? intval($z) : $this->z;
        return $this;
    }

    public function set_from($from)
    {
        $this->from = is_numeric($from) ? $from : null;
        return $this;
    }

    public function set_to($to)
    {
        $this->to = is_numeric($to) ? $to : null;
        return $this;
    }

    public function set_valid_from($date)
    {
        $this->valid_from = strtotime($date) !== FALSE ? date('y-m-d H:i:s', strtotime($date)) : $this->valid_from;
        return $this;
    }

    public function set_valid_previous_paid_from($date)
    {
        $this->previous_term_paid_from = strtotime($date) !== FALSE ? date('y-m-d H:i:s', strtotime($date)) : $this->previous_term_paid_from;
        return $this;
    }

    public function set_valid_previous_paid_to($date)
    {
        $this->previous_term_paid_to = strtotime($date) !== FALSE ? date('y-m-d H:i:s', strtotime($date)) : $this->previous_term_paid_to;
        return $this;
    }

    public function set_valid_to($date)
    {
        $this->valid_to = strtotime($date) !== FALSE ? date('y-m-d H:i:s', strtotime($date)) : $this->valid_to;
        return $this;
    }

    public function set_publish($publish = 1)
    {
        $this->publish = is_numeric($publish) ? intval($publish) : $this->publish;
        return $this;
    }

    public function set_publish_on_web($publish_on_web)
    {
        is_numeric($publish_on_web) ? intval($publish_on_web) : $this->publish_on_web;
    }

    public function set_delete($delete = 0)
    {
        $this->delete = is_numeric($delete) ? intval($delete) : $this->delete;
        return $this;
    }

    public function set_categories($categories)
    {
        if (is_array($categories)) {
            $categories = implode(',', $categories);
        }
        $this->categories = $categories;
    }

    public function set_amount_type($amount_type)
    {
        $this->amount_type = $amount_type;
        return $this;
    }

    public function set_amount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function set_schedule_type($schedule_type)
    {
        $this->schedule_type = $schedule_type;
        return $this;
    }

    public function set_item_quantity_min($item_quantity_min)
    {
        $this->item_quantity_min = $item_quantity_min;
        return $this;
    }

    public function set_item_quantity_max($item_quantity_max)
    {
        $this->item_quantity_max = $item_quantity_max;
        return $this;
    }

    public function set_item_quantity_type($item_quantity_type)
    {
        $this->item_quantity_type = $item_quantity_type;
        return $this;
    }

    public function set_item_quantity_scope($item_quantity_scope)
    {
        $this->item_quantity_scope = $item_quantity_scope;
        return $this;
    }

    public function set_min_students_in_family($min_students_in_family)
    {
        $this->min_students_in_family = $min_students_in_family;
        return $this;
    }

    public function set_max_students_in_family($max_students_in_family)
    {
        $this->max_students_in_family = $max_students_in_family;
        return $this;
    }

    public function set_usage_limit($usage_limit)
    {
        $this->usage_limit = $usage_limit;
        return $this;
    }

    public function set_max_usage_per($max_usage_per)
    {
        $this->max_usage_per = $max_usage_per;
        return $this;
    }

    public function set_apply_to($apply_to)
    {
        $this->apply_to = $apply_to;
        return $this;
    }

    public function set_member_only($member_only) {
        $this->member_only = $member_only;
        return $this;
    }

    public function set_application_type($application_type) {
        $this->application_type = $application_type;
        return $this;
    }

    public function set_application_order($application_order) {
        $this->application_order = $application_order;
        return $this;
    }

    public function set_code($code)
    {
        $this->code = $code;
        return $this;
    }

    public function set_for_contacts($for_contacts)
    {
        $this->for_contacts = $for_contacts;
        return $this;
    }

    public function set_student_years($student_years)
    {
        $this->student_years = $student_years;
        return $this;
    }

    public function set_daily_rates($daily_rates)
    {
        $this->daily_rates = $daily_rates;
        return $this;
    }

    public function set_per_day_rates($per_day_rates)
    {
        $this->per_day_rates = $per_day_rates;
        return $this;
    }

    public function set_qty_rates($qty_rates)
    {
        $this->qty_rates = $qty_rates;
        return $this;
    }

    public function set_is_package($is_package)
    {
        $this->is_package = $is_package;
        return $this;
    }

    public function set_has_schedules($has_schedules)
    {
        $this->has_schedules = $has_schedules;
        return $this;
    }

    public function set_has_courses($has_courses)
    {
        $this->has_schedules = $has_courses;
        return $this;
    }

    public function set_min_days($min_days)
    {
        $this->min_days = $min_days;
        return $this;
    }

    public function set_min_days_is_consecutive($min_days_is_consecutive)
    {
        $this->min_days_is_consecutive = $min_days_is_consecutive;
        return $this;
    }

    public function set_days_of_the_week($days_of_the_week)
    {
        $this->days_of_the_week = $days_of_the_week;
        return $this;
    }

    public function set_course_date_from($course_date_from)
    {
        $this->course_date_from = $course_date_from;
        return $this;
    }

    public function set_course_date_to($course_date_to)
    {
        $this->course_date_to = $course_date_to;
        return $this;
    }

    public function set_class_time_from($class_time_from)
    {
        $this->class_time_from = $class_time_from;
        return $this;
    }

    public function set_class_time_to($class_time_to)
    {
        $this->class_time_to = $class_time_to;
        return $this;
    }

    public function get($autoload = true)
    {
        $data = $this->_sql_load_discount();

        if ($autoload) {
            $this->set($data);
        }

        return $data;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_summary()
    {
        return trim($this->summary);
    }

    public function get_image()
    {
        $image = Model_Media::get($this->image_id);
        $image['id'] = $this->image_id;
        $image['url'] = Model_Media::get_path_to_id($this->image_id);
        return $image;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_from()
    {
        return $this->from;
    }

    public function get_to()
    {
        return $this->to;
    }

    public function get_x($array = false)
    {
        return $this->x;
    }

    public function get_y()
    {
        return $this->y;
    }

    public function get_z()
    {
        return $this->z;
    }

    public function get_valid_from($formatted = false)
    {
        return $this->valid_from ? (($formatted) ? date('d-m-Y', strtotime($this->valid_from)) : $this->valid_from) : null;
    }

    public function get_valid_to($formatted = false)
    {
        return $this->valid_to ? (($formatted) ? date('d-m-Y', strtotime($this->valid_to)) : $this->valid_to) : null;
    }


    public function get_previous_term_from($formatted = false)
    {
        $res = '';

        if ($this->previous_term_paid_from) {
            $res = ($formatted) ? date('d-m-Y', strtotime($this->previous_term_paid_from)) : $this->previous_term_paid_from;

        }

        return $res;
    }


    public function get_previous_term_to($formatted = false)
    {
        $res = '';
        if ($this->previous_term_paid_to) {
            $res = ($formatted) ? date('d-m-Y', strtotime($this->previous_term_paid_to)) : $this->previous_term_paid_to;

        }


        return $res;
    }


    public function get_publish()
    {
        return $this->publish;
    }

    public function get_publish_on_web()
    {
        return $this->publish_on_web;
    }

    public function get_categories()
    {
        return $this->categories;
    }

    public function get_amount_type()
    {
        return $this->amount_type;
    }

    public function get_amount()
    {
        return $this->amount;
    }

    public function get_amount_formatted()
    {
        $return = str_replace('.00', '', $this->amount);

        switch ($this->amount_type) {
            case 'Percent' : $return = $return.'%'; break;
            case 'Fixed'   : $return = '€'.$return; break;
            case 'Quantity': $return = $return.' unit'; break;
        }

        return $return;
    }

    public function get_schedule_type()
    {
        return $this->schedule_type;
    }

    public function get_item_quantity_min()
    {
        return $this->item_quantity_min;
    }

    public function get_item_quantity_max()
    {
        return $this->item_quantity_max;
    }

    public function get_item_quantity_type()
    {
        return $this->item_quantity_type;
    }

    public function get_item_quantity_scope()
    {
        return $this->item_quantity_scope;
    }

    public function get_min_students_in_family()
    {
        return $this->min_students_in_family;
    }

    public function get_max_students_in_family()
    {
        return $this->max_students_in_family;
    }

    public function get_usage_limit()
    {
        return $this->usage_limit;
    }

    public function get_max_usage_per()
    {
        return $this->max_usage_per;
    }

    public function get_apply_to()
    {
        return $this->apply_to;
    }

    public function get_member_only()
    {
        return $this->member_only;
    }

    public function get_application_type() {
        return $this->application_type;
    }

    public function get_application_order(){
        return $this->application_order;
    }

    public function get_for_contacts()
    {
        return $this->for_contacts;
    }

    public function get_for_contacts_details()
    {
        $contacts = array();
        if ($this->for_contacts) {
            $contacts = DB::select('id', DB::expr("CONCAT_WS(' ', contact.first_name, contact.last_name) as fullname"))
                ->from(array(Model_Contacts3::CONTACTS_TABLE, 'contact'))
                ->where('id', 'in', $this->for_contacts)
                ->execute()
                ->as_array();
        }
        return $contacts;
    }

    public function get_student_years()
    {
        return $this->student_years;
    }

    public function get_student_years_details()
    {
        $years = array();
        if ($this->student_years) {
            $years = DB::select('id', 'year')
                ->from(array('plugin_courses_years', 'years'))
                ->where('id', 'in', $this->student_years)
                ->execute()
                ->as_array();
        }
        return $years;
    }

    public function get_daily_rates()
    {
        return $this->daily_rates;
    }

    public function get_per_day_rates()
    {
        return $this->per_day_rates;
    }

    public function get_qty_rates()
    {
        return $this->qty_rates;
    }

    public function get_terms()
    {

        $terms = DB::select('id', DB::expr("name"))
            ->from('plugin_bookings_discounts_term_types')
            ->execute()
            ->as_array();

        return $terms;
    }

    public function get_is_package()
    {
        return $this->is_package;
    }

    public function get_has_schedules()
    {
        return $this->has_schedules;
    }

    public function get_has_schedules_details()
    {
        $schedules = array();
        if ($this->has_schedules) {
            $schedules = DB::select('id', 'name')
                ->from(array('plugin_courses_schedules', 'schedules'))
                ->where('id', 'in', $this->has_schedules)
                ->execute()
                ->as_array();
        }
        return $schedules;
    }

    public function get_has_courses()
    {
        return $this->has_courses;
    }

    public function get_has_courses_details()
    {
        $courses = array();
        if ($this->has_courses) {
            $courses = DB::select('id', DB::expr("title"))
                ->from('plugin_courses_courses')
                ->where('id', 'in', $this->has_courses)
                ->execute()
                ->as_array();
        }
        return $courses;
    }

    public function get_has_category_details()
    {
        $categories = array();
        if ($this->categories) {
            $categories = DB::select('id', 'category')
                ->from(Model_Categories::TABLE_CATEGORIES)
                ->where('id', 'in', explode(',', $this->categories))
                ->execute()
                ->as_array();
        }
        return $categories;
    }

    public function get_min_days()
    {
        return $this->min_days;
    }

    public function get_min_days_is_consecutive()
    {
        return $this->min_days_is_consecutive;
    }

    public function get_days_of_the_week()
    {
        return $this->days_of_the_week;
    }

    public function get_course_date_from()
    {
        return $this->course_date_from;
    }

    public function get_course_date_to()
    {
        return $this->course_date_to;
    }

    public function get_class_time_from()
    {
        return $this->class_time_from;
    }

    public function get_class_time_to()
    {
        return $this->class_time_to;
    }

    public function get_code()
    {
        return $this->code;
    }

    public function get_instance()
    {
        return array('id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'image_id' => $this->image_id,
            'type' => $this->type,
            'code' => $this->code,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'from' => $this->from ? $this->from : null,
            'to' => $this->to ? $this->to : null,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'publish' => $this->publish,
            'publish_on_web' => $this->publish_on_web,
            'delete' => $this->delete,
            'categories' => $this->categories,
            'amount_type' => $this->amount_type,
            'amount' => $this->amount,
            'schedule_type' => $this->schedule_type,
            'item_quantity_min' => $this->item_quantity_min ? $this->item_quantity_min : null,
            'item_quantity_max' => $this->item_quantity_max ? $this->item_quantity_max : null,
            'item_quantity_type' => $this->item_quantity_type ? $this->item_quantity_type : '',
            'item_quantity_scope' => $this->item_quantity_scope ? $this->item_quantity_scope : null,
            'min_students_in_family' => $this->min_students_in_family ? $this->min_students_in_family : null,
            'max_students_in_family' => $this->max_students_in_family ? $this->max_students_in_family : null,
            'usage_limit' => $this->usage_limit ? $this->usage_limit : null,
            'max_usage_per' => $this->max_usage_per ? $this->max_usage_per : null,
            'apply_to' => $this->apply_to ? $this->apply_to : null,
            'is_package' => $this->is_package ? $this->is_package : (count($this->has_schedules) | count($this->has_courses) > 0 ? 1 : 0),
            'previous_term_paid_from' => $this->previous_term_paid_from ? $this->previous_term_paid_from : null,
            'previous_term_paid_to' => $this->previous_term_paid_to ? $this->previous_term_paid_to : null,
            'action_type' => $this->action_type ? $this->action_type : null,
            'min_days' => $this->min_days,
            'min_days_is_consecutive' => $this->min_days_is_consecutive,
            'days_of_the_week' => $this->days_of_the_week,
            'course_date_from' => $this->course_date_from ?: null,
            'course_date_to' => $this->course_date_to ?: null,
            'class_time_from' => $this->class_time_from ?: null,
            'class_time_to' => $this->class_time_to ?: null,
            'member_only' => $this->member_only ?: 0,
            'application_type' => $this->application_type ?: 'initial',
            'application_order' => $this->application_order ?: 1
        );
    }

    public function get_type_title()
    {
        return $this->type_title;
    }

    public function save()
    {


        if (count($this->daily_rates) > 0 || count($this->per_day_rates) > 0) {
            $this->amount_type = 'Fixed';
        }
        if (!empty($this->has_previous_courses)) {
            foreach ($this->has_previous_courses as $id) {
                $this->has_previous_discount_conditions[] = array("ref_id" => $id, 'type_id' => '2');
            }


        }

        if (!empty($this->has_previous_schedules)) {
            foreach ($this->has_previous_schedules as $id) {
                $this->has_previous_discount_conditions[] = array("ref_id" => $id, 'type_id' => '3');
            }


        }

        if (!empty($this->has_previous_category)) {
            foreach ($this->has_previous_category as $id) {
                $this->has_previous_discount_conditions[] = array("ref_id" => $id, 'type_id' => '1');
            }


        }

        $ok = TRUE;
        Database::instance()->begin();
        try {
            $this->validate();
            if (!is_numeric($this->id)) {
                $this->_sql_insert_discount(true);
            } else {
                $this->_sql_update_discount();
            }
            $this->_sql_set_contacts();
            $this->_sql_set_schedules();
            $this->_sql_set_courses();
            $this->_sql_set_previous_booking_conditions();
            $this->_sql_set_student_years();
            $this->_sql_set_daily_rates();
            $this->_sql_set_per_day_rates();
            $this->_sql_set_qty_rates();
            Database::instance()->commit();
        } catch (Exception $e) {
            $ok = FALSE;
            Database::instance()->rollback();
            throw $e;
        }

        return $ok ? $this->id : false;
    }


    // Check what affect the discount will have on a schedule
    // Can also be used to determine if the discount is relevant to the schedule, by checking if it returns `0`.
    public function apply_to_schedule($schedule_id, $args = [])
    {
        $client_id = isset($args['client_id']) ? $args['client_id'] : Auth::instance()->get_contact()->id;
        $schedule = ORM::factory('Course_Schedule')->where('id', '=', $schedule_id)->find_undeleted();

        // Create a dummy cart, using just this schedule to test the discount
        $timeslot_ids = array_keys($schedule->timeslots->find_all_published()->as_array('id'));
        $lines = [[
            'type'              => 'schedule',
            'id'                => $schedule->id,
            'details'           => Model_Schedules::get_one_for_details($schedule_id),
            'discounts'         => [],
            'periods_attending' => $timeslot_ids,
            'fee'               => $schedule->fee_amount
        ]];

        return $this->calculate_discount($client_id, $lines, $schedule->id) > 0;
    }

    private function getpreviousUsageOfterm($contact_id)
    {
        $q = DB::select(DB::expr("*"))
            ->from(array('plugin_ib_educate_bookings', 'b'))
            ->and_where('b.contact_id', '=', $contact_id)
            ->and_where('b.delete', '=', 0)
            ->and_where('b.booking_status', 'not in', array(1, 3))// not enquiry , cancelled;
            ->and_where('b.created_date', '>=', $this->previous_term_paid_from)
            ->and_where('b.created_date', '<=', $this->previous_term_paid_to);
        return $q->execute()->as_array();
    }

    public function get_usage($params = array())
    {
        $q = DB::select(DB::expr("distinct hs.id as hs_id, b.booking_id, hs.schedule_id" . ($this->item_quantity_type == 'Classes' ? ', items.booking_item_id' : '')))
            ->from(array('plugin_ib_educate_bookings', 'b'))
            ->join(array('plugin_ib_educate_booking_has_schedules', 'hs'), 'inner')
            ->on('b.booking_id', '=', 'hs.booking_id')
            ->join(array('plugin_ib_educate_bookings_discounts', 'd'), 'left')
            ->on('hs.booking_id', '=', 'd.booking_id')
            //->on(DB::expr('(hs.schedule_id = d.schedule_id or d.schedule_id is null)'))
            ->on(DB::expr('(hs.schedule_id'), '=', DB::expr('d.schedule_id or d.schedule_id is null)'))// a workaround to generate (hs.schedule_id = d.schedule_id or d.schedule_id is null)
            ->join(array('plugin_contacts3_contacts', 'c'), 'inner')
            ->on('b.contact_id', '=', 'c.id')
            ->where('b.booking_status', 'in', array(2, 4))// confirmed, in progress
            ->and_where('b.publish', '=', 1)
            ->and_where('b.delete', '=', 0)
            ->and_where('hs.deleted', '=', 0)
            ->and_where('hs.publish', '=', 1);

        if (@$params['booking_id']) {
            $q->and_where('b.booking_id', '=', $params['booking_id']);
        }

        if (@$params['contact_id']) {
            $q->and_where('b.contact_id', '=', $params['contact_id']);
        }

        if (@$params['family_id']) {
            $q->and_where('c.family_id', '=', $params['family_id']);
        }

        if (@$params['family_of']) {
            $q->and_where('c.family_id', '=', Model_Contacts3::get_family_id_by_contact_id($params['family_of']));
        }

        if ($this->item_quantity_type == 'Classes') {
            $q->join(array(Model_KES_Bookings::BOOKING_ITEMS_TABLE, 'items'), 'inner')
                ->on('b.booking_id', '=', 'items.booking_id')
                ->on('items.delete', '=', DB::expr(0));
        }

        $q_used = clone $q;
        $q_used->and_where('d.discount_id', '=', $this->id);
        $used_by_schedules = $q_used->execute()->as_array();

        $q_all = clone $q;
        $all_schedules = $q_all->execute()->as_array();

        if ($this->item_quantity_type == 'Classes') {
            $not_using_schedules = $all_schedules;
            foreach ($not_using_schedules as $i => $schedule1) {
                foreach ($used_by_schedules as $schedule2) {
                    if ($schedule1['booking_item_id'] == $schedule2['booking_item_id']) {
                        unset($not_using_schedules[$i]);
                        break;
                    }
                }
            }
            $not_using_schedules = array_values($not_using_schedules);
        } else {
            $not_using_schedules = $all_schedules;
            foreach ($not_using_schedules as $i => $schedule1) {
                foreach ($used_by_schedules as $schedule2) {
                    if ($schedule1['hs_id'] == $schedule2['hs_id']) {
                        unset($not_using_schedules[$i]);
                        break;
                    }
                }
            }
            $not_using_schedules = array_values($not_using_schedules);
        }

        $result = array(
            'used' => array('quantity' => count($used_by_schedules), 'items' => $used_by_schedules),
            'all' => array('quantity' => count($all_schedules), 'items' => $all_schedules),
            'not' => array('quantity' => count($not_using_schedules), 'items' => $not_using_schedules)
        );

        return $result;
    }

    public function ignore_others()
    {
        return ($this->amount_type == 'Quantity' || ($this->amount_type == 'Percent' && $this->amount == 100));
    }

    // this is to calculate discounts that are already applied to an existing booking
    public function calculate_discount_no_check($client_id, $lines, $schedule_id, $new_student_params = array())
    {
        $discount = 0;

        $total = 0;
        $quantity = 0;

        $usage = null;
        $check_existing_item_quantity = ($this->item_quantity_min != null || $this->item_quantity_max != null);
        if ($this->item_quantity_scope == 'Contact' && $check_existing_item_quantity) {
            $usage = $this->get_usage(array('contact_id' => $client_id));
            $quantity += $usage['all']['quantity'];
        } else if ($this->item_quantity_scope == 'Family' && $check_existing_item_quantity) {
            $usage = $this->get_usage(array('family_of' => $client_id));
            $quantity += $usage['all']['quantity'];
        }
        foreach ($lines as $key => $line) {
            if ($line['id'] != null) {
                $total += $line['fee'];
                ++$quantity;
            }
        }

        foreach ($lines as $key => $line) {
            if ($line['id'] != $schedule_id || $line['type'] != 'schedule') {
                continue;
            }

            if (count($this->daily_rates) > 0 || count($this->per_day_rates) > 0) {
                $max_fee = $this->calculate_max_fee($line['id'], $line['periods_attending']);
                if ($max_fee === false) {
                    return 0;
                } else {
                    return $max_fee['fee_non_modified'] - $max_fee['fee'];
                }
            } else if ($this->amount_type == 'Fixed') {
                $lines[$key]['discount'] = (float)$this->amount;
                $discount += $lines[$key]['discount'];
            } else if ($this->amount_type == 'Percent') {
                $lines[$key]['discount'] = round($line['fee'] * ($this->amount / 100), 2);
                $discount += $lines[$key]['discount'];
            } else if ($this->amount_type == 'Quantity') {
                $discount = $lines[$key]['discount'] = (float)$line['fee'];
            }
        }

        return $discount;
    }

    public function preview_discount_on_fee($fee)
    {
        if ($this->amount_type == 'Fixed'){
             return $fee - $this->amount;
        } else if ($this->amount_type == 'Percent') {
            return $fee - round($fee * ($this->amount / 100), 2);
        } else {
            return $fee;
        }
    }

    public function get_discount_for_fee($fee) {
        if ($this->amount_type == 'Fixed'){
            return $this->amount;
        } else if ($this->amount_type == 'Percent') {
            return round($fee * ($this->amount / 100), 2);
        } else {
            return 0;
        }
    }

    public $calculated_for_timeslots = null;
    public function calculate_discount($client_id, &$lines, $schedule_id, $coupon_code = null, $new_student_params = array())
    {
        $discount = 0;

        $total = 0;
        $quantity = 0;
        $schedule = new Model_Course_Schedule($schedule_id);

        if ($this->for_contacts) {
            if (!in_array($client_id, $this->for_contacts)) {
                return false;
            }
        }

        $usage = null;
        if ($this->usage_limit != null) {
            if ($this->max_usage_per == '' || $this->max_usage_per == 'GLOBAL') {
                $usage = $this->get_usage(); //used times in existing bookings
                $used_times = $usage['used']['quantity'];
            } else if ($this->max_usage_per == 'Contact' && $client_id) {
                $usage = $this->get_usage(array('contact_id' => $client_id)); //used times by student
                $used_times = $usage['used']['quantity'];
            } else if ($this->max_usage_per == 'Family' && $client_id) {
                $usage = $this->get_usage(array('family_of' => $client_id)); //used times by student
                $used_times = $usage['used']['quantity'];
            } else { // per cart
                $used_times = 0;
            }

            //used times in new booking
            foreach ($lines as $line) {
                if ($line['discounts']) {
                    foreach ($line['discounts'] as $ldiscount) {
                        if ($ldiscount['id'] == $this->id) {
                            ++$used_times;
                        }
                    }
                }
            }

            if ($used_times >= $this->usage_limit) {
                return false;
            }

        }

        $check_existing_item_quantity = ($this->item_quantity_min != null || $this->item_quantity_max != null);
        if ($this->item_quantity_scope == 'Contact' && $check_existing_item_quantity) {
            $usage = $this->get_usage(array('contact_id' => $client_id));
            $quantity += $usage['all']['quantity'];
        } else if ($this->item_quantity_scope == 'Family' && $check_existing_item_quantity) {
            $usage = $this->get_usage(array('family_of' => $client_id));
            $quantity += $usage['all']['quantity'];
        }

        $matching_schedules_total = 0;
        foreach ($lines as $key => $line) {
            // do not increase quantity since its already added above
            if (!array_key_exists('existing_booking', $line)) {
                //ob_clean();header('content-type: text/plain');print_r($line);exit;
            }

            if ($line['type'] == 'schedule') {
                if (count($this->has_schedules) > 0) {
                    if (!in_array($line['id'], $this->has_schedules)) {
                        continue;
                    }
                }
                if (count($this->has_courses) > 0) {
                    if (!in_array($line['details']['course_id'], $this->has_courses)) {
                        continue;
                    }
                }
            }
            
            if ($line['id'] != null && ($line['existing_booking'] == false)) {
                if ($this->categories) {
                    if ($this->test_matching_categories($line['id'], $lines, $usage)) {
                        $total += $line['fee'] ? $line['fee'] : 0;
                        $matching_schedules_total += $line['fee'] ? $line['fee'] : 0;
                    }
                } else {
                    $total += $line['fee'] ? $line['fee'] : 0;
                    $matching_schedules_total += $line['fee'] ? $line['fee'] : 0;
                }
                if ($this->item_quantity_type == 'Classes') {
                    $quantity += count($line['periods_attending']);
                } else {
                    ++$quantity;
                }
            }
        }

//        $remcondition = new Remaining_Conditions();
//        array_push($this->failing_conditions,$remcondition);
        $matched = true;

        //  vd($this->days_of_the_week,"days of the week");
        if ($this->code != '' && $this->code != $coupon_code) {
            $matched = false;
        }
        if (!$matched) {
            $this->failing_conditions = array();
            $remcondition = new Remaining_Conditions();
            $remcondition->weight = PHP_INT_MAX;
            $remcondition->setTypeImpossible();
            $remcondition->title = "Coupon code is required";
            array_push($this->failing_conditions, $remcondition);
            return 0;
        }


        if ($this->from != null) {
            $matched = ($this->from <= $total);
        }
        if (!$matched) {
            $remcondition = new Remaining_Conditions();
            $remcondition->title = "Add some more classes for " . ($this->from - $total) . " EUR and more to get " . $this->getMessageBasedOnAmountType() . " discount.";
            $remcondition->weight = 1;
            array_push($this->failing_conditions, $remcondition);
            //  return false;
            $matched = true;
        }

        if ($this->to != null) {
            $matched = ($this->to >= $total);
        }
        if (!$matched) {
            $remcondition = new Remaining_Conditions();
            $remcondition->weight = PHP_INT_MAX;
            $remcondition->title = "Booking amount is already high for this discount";
            $remcondition->setTypeImpossible();
            array_push($this->failing_conditions, $remcondition);
            // return false;
            $matched = true;
        }

        if ($this->item_quantity_min != null) {
            $matched = ($this->item_quantity_min <= $quantity);
        }
        if (!$matched) {
            $remcondition = new Remaining_Conditions();
            $remcondition->weight = $this->item_quantity_min - $quantity;
            $remcondition->title = "You still need " . $remcondition->weight . " bookings to get " . $this->getMessageBasedOnAmountType();
            $remcondition->setTypeFuture();
            array_push($this->failing_conditions, $remcondition);
            // return false;
            $matched = true;
        }

        if ($this->item_quantity_max != null) {
            $matched = ($this->item_quantity_max >= $quantity);
        }
        if (!$matched) {
            $remcondition = new Remaining_Conditions();
            $remcondition->weight = PHP_INT_MAX;
            $remcondition->setTypeImpossible();
            $remcondition->title = 'Item quantity is already high for this discount';
            array_push($this->failing_conditions, $remcondition);
            //return false;
            $matched = true;
        }

        $qty_rate_fee = null;
        $qty_rate_matched = null;
        if (count($this->qty_rates) > 0) {
            foreach ($this->qty_rates as $qty_rate) {
                $min_qty_rate_matched = false;
                $max_qty_rate_matched = false;
                if ($qty_rate['min_qty']) {
                    if ($qty_rate['min_qty'] <= $quantity) {
                        $min_qty_rate_matched = true;
                    }
                } else {
                    $min_qty_rate_matched = true;
                }

                if ($min_qty_rate_matched) {
                    if ($qty_rate['max_qty']) {
                        if ($qty_rate['max_qty'] >= $quantity) {
                            $max_qty_rate_matched = true;
                        }
                    } else {
                        $max_qty_rate_matched = true;
                    }
                }

                if ($min_qty_rate_matched && $max_qty_rate_matched) {
                    $qty_rate_fee = $qty_rate['amount'];
                    $qty_rate_matched = $qty_rate;
                    break;
                }
            }
        }

        $contacts = Model_Contacts3::get_siblings($client_id);
        $student_count = count($contacts) + 1;


        if ($this->min_students_in_family != null) {
            $matched = ($this->min_students_in_family <= $student_count);
        }
        if (!$matched) {
            $remcondition = new Remaining_Conditions();
            $remcondition->weight = $this->min_students_in_family - $student_count;
            $remcondition->setTypeFuture();
            $remcondition->title = "Family needs to have " . $remcondition->weight . " more students to get " . $this->getMessageBasedOnAmountType();
            array_push($this->failing_conditions, $remcondition);
            //   return false;
            $matched = true;
        }

        if ($this->max_students_in_family != null) {
            $matched = ($this->max_students_in_family >= $student_count);
        }
        if (!$matched) {
            $remcondition = new Remaining_Conditions();
            $remcondition->weight = PHP_INT_MAX;
            $remcondition->setTypeImpossible();
            array_push($this->failing_conditions, $remcondition);
            // return false;
            $matched = true;
        }


        if ($this->is_package) {
            $this->test_matching_schedules($lines) && $this->test_matching_courses($lines) && $this->test_unassigned();
        }

        if ($schedule_id != null) {
            $this->checkPreviousBookingConditionsForCSC($client_id, $schedule_id);
        }

        $this->checkDayMatch($lines, $schedule_id);

        $this->checkPerDayMatch($lines, $schedule_id);

        if ($schedule_id != null) {
            $this->checkCourseDateMatch($lines, $schedule_id);
        }

        //$this->checkMinDays($lines, $schedule_id);

        if ($schedule_id != null) {
            if ($this->previous_term_paid_from && $this->previous_term_paid_to) {
                $this->checkTerms($client_id, $schedule_id);
            }
        }

        $this->checkStudentYears($client_id, $new_student_params);

        if ($this->member_only) {
            $client = new Model_Contacts3($client_id);
            $matched = $client->is_special_member() || $client_id == 'member';
        }
        if (!$matched) {
            $remcondition = new Remaining_Conditions();
            $remcondition->weight = PHP_INT_MAX;
            $remcondition->setTypeFuture();
            $remcondition->title = "User must be a member to get this discount";
            array_push($this->failing_conditions, $remcondition);
            //   return false;
            $matched = true;
        }

        $matched = count($this->failing_conditions) == 0;

        if ($matched) {
            $matched = false;
            $linesdup = $lines;
            $applied_for_timeslots = 0;
            foreach ($lines as $key => $line) {
                foreach ($line['discounts'] as $applied_discount) {
                    if ($applied_discount['id'] == $this->id) {
                        if (is_numeric($applied_discount['applied_for_timeslots'])  && $applied_discount['applied_for_timeslots'] > 0) {
                            $applied_for_timeslots += $applied_discount['applied_for_timeslots'];
                        }
                    }
                }
            }
            foreach ($lines as $key => $line) {
                if ($line['id'] != $schedule_id || ($line['type'] != 'schedule' && $line['type'] != 'subtotal')) {
                    continue;
                }

                if (($line['id'] == null && $coupon_code != '') ||
                    ($line['id'] == null && $this->apply_to == 'Cart') ||
                    ($this->test_matching_categories($line['id'], $lines, $usage) &&
                        ($this->schedule_type == 'Prepay,PAYG' || ($this->schedule_type == 'Prepay' && $line['prepay']) || ($this->schedule_type == 'PAYG' && !$line['prepay'])))
                ) {
                    $amount = (float)$this->amount;
                    $matched = true;
                    if ($qty_rate_fee) {
                        $discount = $matching_schedules_total - (float)$qty_rate_fee;
                    } else if ((count($this->daily_rates) > 0 || count($this->per_day_rates) > 0) && $line['id'] != null) {
                        $max_fee = $this->calculate_max_fee($line['id'], $line['periods_attending']);
                        if ($max_fee === false) {
                            return 0;
                        } else {
                            return $max_fee['fee_non_modified'] - $max_fee['fee'];
                        }
                    } else if ($this->amount_type == 'Fixed') {
                        if ($line['number_of_delegates'] && $line['number_of_delegates'] > 1) {
                            if ($this->action_type == 2) {
                                $lines[$key]['discount'] *= -1;
                            }

                            $quantity = $schedule->charge_per_delegate ? $line['number_of_delegates'] : 1;

                            $fixed_delegates_discount = $amount * $quantity;
                            if ($lines[$key]['remaining_fee'] < $amount) {
                                $lines[$key]['remaining_fee'] = 0;
                            } else {
                                $lines[$key]['remaining_fee'] -= $amount;
                            }
                            $discount += $fixed_delegates_discount;
                        } else {
                            if ($this->action_type == 2) {
                                $lines[$key]['discount'] *= -1;
                            }
                            if ($lines[$key]['remaining_fee'] < $amount) {
                                $lines[$key]['remaining_fee'] = 0;
                            } else {
                                $lines[$key]['remaining_fee'] -= $amount;
                            }
                            $discount += $amount;
                        }

                    } else if ($this->amount_type == 'Percent') {

                        if (($this->item_quantity_type == 'Classes' || count($this->per_day_rates) > 0) && $this->class_time_from != '' && $this->class_time_to != '' && $line['id'] != null) {
                            $fee = 0;
                            foreach ($line['timeslot_details'] as $timeslot_detail) {
                                if (strtotime($this->class_time_from) <= strtotime(date('H:i:s', strtotime($timeslot_detail['end_date']))) && strtotime($this->class_time_to) >= strtotime(date('H:i:s', strtotime($timeslot_detail['datetime_start'])))) {
                                    $fee += (float)$timeslot_detail['fee_amount'];
                                }
                            }
                        } else {
                            if ($this->item_quantity_type == 'Classes' && $this->usage_limit > 0 && $line['id'] != null) {
                                $this->calculated_for_timeslots = 0;
                                $fee = 0;
                                foreach ($line['timeslot_details'] as $timeslot_detail) {
                                    if (($this->calculated_for_timeslots + $applied_for_timeslots) < $this->usage_limit) {
                                        $this->calculated_for_timeslots += 1;
                                        $fee += (float)$timeslot_detail['fee_amount'];
                                    }
                                }
                            }
                            else {
                                $fee = $line['fee'];
                            }
                        }
                        if ($this->action_type == 2) {

                            $lines[$key]['discount'] *= -1;

                        }
                        $delegates = ($line['number_of_delegates'] && $line['number_of_delegates'] > 1) ? $line['number_of_delegates'] : 1;
                        if ($this->application_type && $this->application_type == 'latest') {
                            $discount_from_fee = $line['remaining_fee'] ?
                                    round($lines[$key]['remaining_fee']  * ($amount / 100), 2)
                                    : round($lines[$key]['fee']  * ($amount / 100), 2);
                                if ($lines[$key]['remaining_fee'] < $discount_from_fee) {
                                    $lines[$key]['remaining_fee'] = 0;
                                } else {
                                    $lines[$key]['remaining_fee'] -= $discount_from_fee;
                                }
                            } else {
                                $discount_from_fee = round($fee * ($amount / 100), 2);
                                if ($lines[$key]['remaining_fee'] < $discount_from_fee) {
                                    $lines[$key]['remaining_fee'] = 0;
                                } else {
                                    $lines[$key]['remaining_fee'] -= $discount_from_fee;
                                }
                            }

                        $quantity = $schedule->charge_per_delegate ? $delegates : 1;

                        $discount += $discount_from_fee * $quantity;
                    } else if ($this->amount_type == 'Quantity') {
                        $free_quantity_left = $amount - $usage['used']['quantity'];
                        foreach ($linesdup as $keyd => $lined) {
                            if ($free_quantity_left == 0) {
                                break;
                            }
                            if ($lined['discounts']) {
                                foreach ($lined['discounts'] as $ldiscount) {
                                    if ($ldiscount['id'] == $this->id) {
                                        --$free_quantity_left;
                                    }
                                }
                            }
                        }
                        if ($free_quantity_left > 0) {
                            $delegates = ($line['number_of_delegates'] && $line['number_of_delegates'] > 1) ? $line['number_of_delegates'] : 1;
                            $quantity = $schedule->charge_per_delegate ? $delegates : 1;
                            if ($this->item_quantity_type == 'Classes') {
                                if ($line['id'] != null) {
                                    $discount = 0;
                                    for (; $free_quantity_left > 0;) {
                                        $timeslot = current($lines[$key]['timeslot_details']);
                                        if ($this->class_time_from != '' && $this->class_time_to != '') {
                                            if (strtotime($this->class_time_from) <= strtotime(date('H:i:s', strtotime($timeslot['end_date']))) && strtotime($this->class_time_to) >= strtotime(date('H:i:s', strtotime($timeslot['datetime_start'])))) {
                                                $discount += $timeslot['fee_amount'] * $quantity;
                                                $lines[$key]['remaining_fee'] -=$timeslot['fee_amount'];
                                                --$free_quantity_left;
                                            }
                                        } else {
                                            $discount += $timeslot['fee_amount'] * $quantity;
                                            $lines[$key]['remaining_fee'] -=$timeslot['fee_amount'];
                                            --$free_quantity_left;
                                        }
                                    }
                                }
                            } else {
                                $discount = $lines[$key]['discount'] = (float)$line['fee'] * $quantity;
                                $lines[$key]['remaining_fee'] = 0;
                            }
                            break;
                        }
                    }
                }
            }
        }

        return $discount;
    }


    private function checkCourseDateMatch($lines, $schedule_id)
    {
        foreach ($lines as $line) {
            if ($line['id'] == $schedule_id && $line['type'] == 'schedule') {
                if ($this->course_date_from != '') {
                    if (strtotime($this->course_date_from) > strtotime($line['details']['end_date'])) {
                        $remcondition = new Remaining_Conditions();
                        $remcondition->title = 'You need to book a course after ' . $this->course_date_from . '(' . $line['details']['end_date'] . ')';
                        $remcondition->weight = PHP_INT_MAX;
                        $remcondition->setTypeFuture();
                        array_push($this->failing_conditions, $remcondition);
                    }
                }

                if ($this->course_date_to != '') {
                    if (strtotime($this->course_date_to) < strtotime($line['details']['datetime_start'])) {
                        $remcondition = new Remaining_Conditions();
                        $remcondition->title = 'You need to book a course before ' . $this->course_date_to . '('.$line['details']['datetime_start'].')' ;
                        $remcondition->weight = PHP_INT_MAX;
                        $remcondition->setTypeFuture();
                        array_push($this->failing_conditions, $remcondition);
                    }
                }
            }
        }
    }

    private function checkTerms($contact_id)
    {

        $res = $this->getpreviousUsageOfterm($contact_id);
        if (count($res) == 0) {
            $remcondition = new Remaining_Conditions();
            $remcondition->title = 'You need to have a booking between ' . $this->previous_term_paid_from . ' and ' . $this->previous_term_paid_to;
            $remcondition->weight = PHP_INT_MAX;
            $remcondition->setTypeFuture();
            array_push($this->failing_conditions, $remcondition);
        }


    }

    private function checkStudentYears($contact_id, $new_student_params = array())
    {
        if (count($this->student_years) > 0) {
            if (@$new_student_params['year']) {
                $student_year = $new_student_params['year'];
            } else {
                $student_year = DB::select('year_id')
                    ->from(Model_Contacts3::CONTACTS_TABLE)
                    ->where('id', '=', $contact_id)
                    ->execute()
                    ->get('year_id');
            }
            if (!in_array($student_year, $this->student_years)) {
                $sy = $this->get_student_years_details();
                $msg = array();
                foreach ($sy as $year) {
                    $msg[] = $year['year'];
                }
                $remcondition = new Remaining_Conditions();
                $remcondition->title = 'For Student Years ' . implode(', ', $msg);
                $remcondition->weight = '';
                array_push($this->failing_conditions, $remcondition);
            }
        }
    }


    private function checkMinDays($lines, $schedule_id)
    {
        if (!$this->min_number_of_classes) {
            return;
        }
        $daysCount = 0;
        foreach ($lines as $line) {
            if ($line['type'] != 'schedule') {
                continue;
            }

            if ($this->item_quantity_scope == 'Schedule' && $line['id'] != $schedule_id) {
                continue;
            }

            $daysCount += count($line['periods_attending']);

            if ($daysCount >= $this->min_number_of_classes) {
                return;
            }
        }


        $remcondition = new Remaining_Conditions();
        $remcondition->title = 'You need to book ' . ($this->min_number_of_classes - $daysCount) . ' classes ' . $this->getMessageBasedOnAmountType();
        $remcondition->weight = $this->min_number_of_classes - $daysCount;
        $remcondition->setTypeCurrent();
        array_push($this->failing_conditions, $remcondition);

    }


    private function checkDayMatch($lines, $schedule_id)
    {

        $dnames = array(
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        );

        $days_of_schedules = array();
        $unique_days_of_schedules = array();
        foreach ($lines as $line) {
            if ($line['type'] != 'schedule') {
                continue;
            }

            if ($this->item_quantity_scope == 'Schedule' && $line['id'] != $schedule_id) {
                continue;
            }
            $days_of_schedule = $this->getScheduleEventDays($line['periods_attending']);
            foreach ($days_of_schedule as $day_of_schedule) {
                $unique_days_of_schedules[] = date('Y-m-d', strtotime($day_of_schedule['datetime_start']));
            }
            $days_of_schedules[] = $days_of_schedule;
        }
        $unique_days_of_schedules = array_unique($unique_days_of_schedules);
        sort($unique_days_of_schedules);

        $max_consecutive_days = 0;
        $consecutive_days = 0;
        $previous_day = null;

        foreach ($unique_days_of_schedules as $unique_day_of_schedule) {
            if ($previous_day == null) {
                $consecutive_days = 1;
            } else {
                $expected_day = date('Y-m-d', strtotime($previous_day . ' +1day'));
                if ($expected_day == $unique_day_of_schedule) {
                    ++$consecutive_days;
                    $max_consecutive_days = max($max_consecutive_days, $consecutive_days);
                } else {
                    $consecutive_days = 1;
                    $previous_day = null;
                }
            }
            $previous_day = $unique_day_of_schedule;
        }


        /*$failed_days = array();
        foreach ($this->days_of_the_week as $required_day_of_week) {
            $day = $dnames[$required_day_of_week];
            $matched = false;
            foreach ($days_of_schedules as $days_of_schedule) {
                foreach ($days_of_schedule as $ds) {
                    if ($day == $ds['day']) {
                        $matched = true;
                        break;
                    }
                }
            }
            if (!$matched) {
                $failed_days[] = $required_day_of_week;
                break;
            }
        }

        if (count($failed_days) > 0) {
            $remcondition = new Remaining_Conditions();
            $remcondition->title = 'Need to book a ' . implode(', ', $failed_days) . ' class';
            $remcondition->weight = PHP_INT_MAX;
            $remcondition->setTypeImpossible();
            array_push($this->failing_conditions, $remcondition);
        }*/

        if (count($this->days_of_the_week) > 0) {
            $matched_at_least_one_week_day = false;
            $rdaynames = array();
            foreach ($this->days_of_the_week as $required_day_of_week) {
                $day = $dnames[$required_day_of_week];
                $rdaynames[] = $day;
                $matched = false;
                foreach ($days_of_schedules as $days_of_schedule) {
                    foreach ($days_of_schedule as $ds) {
                        if ($day == $ds['day']) {
                            $matched_at_least_one_week_day = true;
                            break;
                        }
                    }
                }
            }
            if ($matched_at_least_one_week_day == 0) {
                $remcondition = new Remaining_Conditions();
                $remcondition->title = 'Book a class in one of these days' . implode(', ', $rdaynames);
                $remcondition->weight = PHP_INT_MAX;
                $remcondition->setTypeImpossible();
                array_push($this->failing_conditions, $remcondition);
            }
        }



        $matched_at_least_one_rate = false;
        $need_consecutive_days = 0;
        foreach ($this->daily_rates as $daily_rate) {
            if ($daily_rate['min_days'] > 0) {
                $attending_days = count($unique_days_of_schedules);
                if ($daily_rate['is_consecutive'] == 1) {
                    if ($max_consecutive_days >= $daily_rate['min_days'] && $max_consecutive_days <= $daily_rate['max_days']) {
                        $matched_at_least_one_rate = true;
                        $this->amount = $daily_rate['amount'];
                        break;
                    } else {
                        $need_consecutive_days = $daily_rate['min_days'];
                    }
                } else {
                    if ($attending_days >= $daily_rate['min_days'] && $attending_days <= $daily_rate['max_days']) {
                        $matched_at_least_one_rate = true;
                        $this->amount = $daily_rate['amount'];
                        break;
                    }
                }
            }
        }

        if (count($this->daily_rates) > 0) {
            if (!$matched_at_least_one_rate) {
                $remcondition = new Remaining_Conditions();
                $remcondition->title = 'Need to book at least ' . $this->daily_rates[0]['min_days'] . ($need_consecutive_days ? ' consecutive' : '') . ' days';
                $remcondition->weight = PHP_INT_MAX;
                array_push($this->failing_conditions, $remcondition);
            }
        }
    }

    private function checkPerDayMatch($lines, $schedule_id)
    {
        if (count($this->per_day_rates) > 0) {
            $timeslots_days = array();

            foreach ($lines as $line) {
                if ($line['type'] != 'schedule') {
                    continue;
                }

                if ($this->item_quantity_scope == 'Schedule' && $line['id'] != $schedule_id) {
                    continue;
                }

                $timeslots = DB::select('*')->from(array('plugin_courses_schedules_events', 'e'));

                if (!empty($line['periods_attending'])) {
                    $timeslots = $timeslots->where('id', 'IN', $line['periods_attending']);
                }

                $timeslots = $timeslots
                    ->and_where('e.delete', '=', 0)
                    ->and_where('e.publish', '=', 1)
                    ->order_by('e.datetime_start', 'asc')
                    ->execute()
                    ->as_array();
                $timeslots_days = array();

                foreach ($timeslots as $i => $timeslot) {
                    $time = strtotime($timeslot['datetime_start']);
                    $date = date('Y-m-d', $time);
                    if (!isset($timeslots_days[$date])) {
                        $timeslots_days[$date] = array();
                    }

                    $timeslots_days[$date][] = $timeslot;
                }
            }

            $matched = false;
            foreach ($this->per_day_rates as $per_day_rate) {
                foreach ($timeslots_days as $date => $timeslots_day) {
                    if ($per_day_rate['min_timeslots'] > 0) {
                        $attending_timeslots = 0;
                        if ($this->class_time_from != '' && $this->class_time_to != '') {
                            foreach ($timeslots_day as $timeslot) {
                                if (strtotime($this->class_time_from) <= strtotime(date('H:i:s', strtotime($timeslot['datetime_end']))) && strtotime($this->class_time_to) >= strtotime(date('H:i:s', strtotime($timeslot['datetime_start'])))) {
                                    ++$attending_timeslots;
                                }
                            }
                        } else {
                            $attending_timeslots = count($timeslots_day);
                        }
                        if ($attending_timeslots >= $per_day_rate['min_timeslots'] && $attending_timeslots <= $per_day_rate['max_timeslots']) {
                            $matched = true;
                            $this->amount = $per_day_rate['amount'];
                            break;
                        }
                    }
                }
            }

            if (!$matched) {
                $remcondition = new Remaining_Conditions();
                $remcondition->title = 'Need to book at least ' . $this->per_day_rates[0]['min_timeslots'] . ' classes';
                $remcondition->weight = PHP_INT_MAX;
                array_push($this->failing_conditions, $remcondition);
            }
        }
    }

    public function calculate_max_fee($schedule, $timeslots)
    {
        $per_day_fees = $this->per_day_rates;
        $daily_fees = $this->daily_rates;

        $dnames = array(
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        );
        $dnames_f = array_flip($dnames);

        if (is_numeric($schedule)) {
            $schedule = DB::select('*')
                ->from(Model_Schedules::TABLE_SCHEDULES)
                ->where('id', '=', $schedule)
                ->execute()
                ->current();
        }

        foreach ($timeslots as $i => $timeslot) {
            if (is_numeric($timeslot)) {
                $timeslots[$i] = DB::select('*')
                    ->from(Model_ScheduleEvent::TABLE_TIMESLOTS)
                    ->where('id', '=', $timeslot)
                    ->execute()
                    ->current();
            }
        }

        foreach ($timeslots as $i => $timeslot) {
            if ($this->class_time_from != '' && $this->class_time_to != '') {
                if (strtotime($this->class_time_from) > strtotime(date('H:i:s', strtotime($timeslot['datetime_start']))) || strtotime($this->class_time_to) < strtotime(date('H:i:s', strtotime($timeslot['datetime_start'])))) {
                    unset($timeslots[$i]);
                }
            }

            if ($this->course_date_from != '') {
                if (strtotime($this->course_date_from) > strtotime($timeslot['datetime_start'])) {
                    unset($timeslots[$i]);
                }
            }

            if ($this->course_date_to != '') {
                if (strtotime($this->course_date_to) < strtotime($timeslot['datetime_start'])) {
                    unset($timeslots[$i]);
                }
            }

            if (count($this->days_of_the_week) > 0) {
                if (!in_array($dnames_f[date('N', strtotime($timeslot['datetime_start']))], $this->days_of_the_week)){
                    unset($timeslots[$i]);
                }
            }
        }
        $timeslots = array_values($timeslots);
        if (count($timeslots) == 0) {
            return false;
        }

        $fee_non_modified = 0;
        foreach ($timeslots as $timeslot) {
            $fee_non_modified += $timeslot['fee_amount'] ?: $schedule['fee_amount'];
        }

        if (count($per_day_fees) > 0 || count($daily_fees) > 0) {
            $day_fees_calculated = array();
            $days_of_schedules = array();
            foreach ($timeslots as $timeslot) {
                $day = date('Y-m-d', strtotime($timeslot['datetime_start']));
                $days_of_schedules[] = $day;
            }
            $unique_days_of_schedules = array_unique($days_of_schedules);
            $consecutive_days_list = array();
            $nonconsecutive_days_list = array();
            sort($unique_days_of_schedules);

            $max_consecutive_days = 0;
            $consecutive_days = array();
            $previous_day = null;
            $expected_day = null;

            foreach ($unique_days_of_schedules as $unique_day_of_schedule) {
                if ($previous_day == null) {
                    $consecutive_days = array($unique_day_of_schedule);
                } else {
                    $expected_day = date('Y-m-d', strtotime($previous_day . ' +1day'));
                    if ($expected_day == $unique_day_of_schedule) {
                        $consecutive_days[] = $unique_day_of_schedule;
                        $max_consecutive_days = max($max_consecutive_days, $consecutive_days);
                    } else {
                        if (count($consecutive_days) > 1) {
                            $consecutive_days_list[] = $consecutive_days;
                        } else {
                            if (@$consecutive_days[0]) {
                                $nonconsecutive_days_list[] = $consecutive_days[0];
                            }
                        }
                        $consecutive_days = array($unique_day_of_schedule);
                        $previous_day = null;
                    }
                }
                $previous_day = $unique_day_of_schedule;
            }
            if (count($consecutive_days) == 1) {
                $nonconsecutive_days_list[] = $consecutive_days[0];
            }
            if (count($consecutive_days) > 1) {
                $consecutive_days_list[] = $consecutive_days;
            }

            $timeslots_days = array();
            foreach ($timeslots as $timeslot) {
                $time = strtotime($timeslot['datetime_start']);
                $date = date('Y-m-d', $time);
                if (!isset($timeslots_days[$date])) {
                    $timeslots_days[$date] = array();
                }
                $timeslots_days[$date][] = $timeslot;
                $day_fees_calculated[$date] = 0.0;
            }

            foreach ($timeslots_days as $date => $timeslots_day) {
                $matched = false;
                foreach ($per_day_fees as $per_day_fee) {
                    if ($per_day_fee['min_timeslots'] > 0) {
                        $attending_timeslots = count($timeslots_day);
                        if ($attending_timeslots >= $per_day_fee['min_timeslots'] && $attending_timeslots <= $per_day_fee['max_timeslots']) {
                            $day_fees_calculated[$date] = $per_day_fee['amount'];
                            $matched = true;
                            break;
                        }
                    }
                }
                if (!$matched) {
                    foreach ($timeslots_day as $timeslot) {
                        $day_fees_calculated[$date] += $timeslot['fee_amount'] ?: $schedule['fee_amount'];
                    }
                }
            }
            //print_r($day_fees_calculated);print_r($consecutive_days_list);print_r($nonconsecutive_days_list);exit;
            $fee = 0.0;
            foreach ($consecutive_days_list as $i => $consecutive_days) {
                $matched = false;
                foreach ($daily_fees as $daily_fee) {
                    if ($daily_fee['min_days'] > 0) {
                        if ($daily_fee['is_consecutive'] == 1) {
                            if (count($consecutive_days) >= $daily_fee['min_days'] && count($consecutive_days) <= $daily_fee['max_days']) {
                                $matched = true;
                                $fee += $daily_fee['amount'];
                                break;
                            }
                        }
                    }
                }

                if (!$matched) {
                    foreach ($consecutive_days as $day) {
                        $nonconsecutive_days_list[] = $day;
                    }
                    unset ($consecutive_days_list[$i]);
                }
            }

            $matched = false;
            foreach ($daily_fees as $daily_fee) {
                if ($daily_fee['min_days'] > 0) {
                    if ($daily_fee['is_consecutive'] == 0) {
                        if (count($nonconsecutive_days_list) >= $daily_fee['min_days'] && count($nonconsecutive_days_list) <= $daily_fee['max_days']) {
                            $matched = true;
                            $fee += $daily_fee['fee'];
                            break;
                        }
                    }
                }
            }
            if (!$matched) {
                foreach ($nonconsecutive_days_list as $day) {
                    $fee += $day_fees_calculated[$day];
                }
            }

        } else {
            if ($schedule['fee_per'] == 'Schedule') {
                $fee = $schedule['fee_amount'];
            } else {
                $fee = 0;
                foreach ($timeslots as $timeslot) {
                    $fee += $timeslot['fee_amount'] ?: $schedule['fee_amount'];
                }
            }
        }

        return array('fee_non_modified' => $fee_non_modified, 'fee' => $fee);
    }

    private function getScheduleEventDays($periods_attending)
    {
        if (empty($periods_attending)) {
            return [];
        }

        $q = DB::select(DB::expr("WEEKDAY(e.datetime_start) + 1 as day"), 'e.datetime_start')
            ->from(array('plugin_courses_schedules_events', 'e'))
            ->where('id', 'IN', $periods_attending)
            ->and_where('e.delete', '=', 0)
            ->and_where('e.publish', '=', 1)
            ->order_by('e.datetime_start', 'asc');

        return $q->execute()->as_array();

    }

    private function get_used_booking_categories_courses_schedules($contact_id)
    {
        $q = DB::select(DB::expr("cs.category_id as category_id,cs.id as course_id,sh.id as schedule_id "))
            ->from(array('plugin_ib_educate_bookings', 'b'))
            ->join(array('plugin_ib_educate_booking_has_schedules', 'hs'), 'inner')
            ->on('b.booking_id', '=', 'hs.booking_id')
            ->join(array('plugin_courses_schedules', 'sh'))
            ->on('sh.id', '=', 'hs.schedule_id')
            ->join(array('plugin_courses_courses', 'cs'))
            ->on('cs.id', '=', 'sh.course_id')
            ->join(array('plugin_contacts3_contacts', 'c'), 'inner')
            ->on('b.contact_id', '=', 'c.id')
            ->where('b.booking_status', 'in', array(2, 4, 5))// confirmed, in progress, completed
            ->and_where('b.publish', '=', 1)
            ->and_where('b.delete', '=', 0)
            ->and_where('hs.deleted', '=', 0)
            ->and_where('hs.publish', '=', 1)
            ->and_where('c.id', '=', $contact_id);

        return $q->execute()->as_array();
    }

    private function checkPreviousBookingConditionsForYearlyOnly($client_id)
    {
        $usage = null;
        foreach ($this->has_previous_discount_conditions as $pc) {
            if ($usage == null) {
                $usage = $this->get_used_booking_categories_courses_schedules($client_id);
            }

            if ($usage) {

            }
        }
    }

    //check previous conditions for course,schedule and category
    private function checkPreviousBookingConditionsForCSC($client_id)
    {
        $usage = $this->get_used_booking_categories_courses_schedules($client_id);
        //  vd($this->has_previous_discount_conditions ,'previous discount conditions ');
        $match_count = 0;
        foreach ($this->has_previous_discount_conditions as $pc) {
            //vd($usage, 'show usage');
            if ($usage) {
                $remcat = array();


                $remcource = array();


                $remschedule = array();


                $matched = false;
                foreach ($usage as $u) {
                    if ($pc['type_name'] == 'category') {
                        if ($u['category_id'] == $pc['ref_id']) {
                            $matched = true;
                            ++$match_count;
                            break;
                        }
                    }

                    if ($pc['type_name'] == 'course') {
                        if ($u['course_id'] == $pc['ref_id']) {
                            $matched = true;
                            ++$match_count;
                            break;
                        }
                    }

                    if ($pc['type_name'] == 'schedule') {
                        if ($u['schedule_id'] == $pc['ref_id']) {
                            $matched = true;
                            ++$match_count;
                            break;
                        }
                    }
                }
                if ($matched) {
                    break;
                }
                if (!$matched) {
                    if ($pc['type_name'] == 'category') {
                        $remcondition = new Remaining_Conditions();
                        $remcondition->weight = 1;
                        $remcondition->setTypeImpossible();
                        $remcondition->title = 'previous booking in category ' . $pc['title'] . ' is required';
                        array_push($remcat, $remcondition);
                    }

                    if ($pc['type_name'] == 'course') {
                        $remcondition = new Remaining_Conditions();
                        $remcondition->weight = 1;
                        $remcondition->setTypeImpossible();
                        $remcondition->title = 'previous booking of course ' . $pc['title'] . ' is required';
                        array_push($remcource, $remcondition);
                    }

                    if ($pc['type_name'] == 'schedule') {
                            $remcondition = new Remaining_Conditions();
                            $remcondition->weight = 1;
                            $remcondition->setTypeImpossible();
                            $remcondition->title = 'previous booking of schedule ' . $pc['title'] . ' is required';
                            array_push($remschedule, $remcondition);
                    }
                }
                $usage_count = count($usage);
                if ($usage_count == count($remcat)) {
                    array_push($this->failing_conditions, $remcat[0]);
                }


                if ($usage_count == count($remschedule)) {
                    array_push($this->failing_conditions, $remschedule[0]);
                }

                if ($usage_count == count($remcource)) {
                    array_push($this->failing_conditions, $remcource[0]);
                }

            } else {

                if ($pc['type_name'] == 'category') {
                    $remcondition = new Remaining_Conditions();
                    $remcondition->weight = 1;
                    $remcondition->setTypeImpossible();
                    $remcondition->title = 'previous booking in category ' . $pc['title'] . ' is required';
                    array_push($this->failing_conditions, $remcondition);
                }

                if ($pc['type_name'] == 'course') {
                    $remcondition = new Remaining_Conditions();
                    $remcondition->weight = 1;
                    $remcondition->setTypeImpossible();
                    $remcondition->title = 'previous booking of course ' . $pc['title'] . ' is required';
                    array_push($this->failing_conditions, $remcondition);
                }

                if ($pc['type_name'] == 'schedule') {
                    $remcondition = new Remaining_Conditions();
                    $remcondition->weight = 1;
                    $remcondition->setTypeImpossible();
                    $remcondition->title = 'previous booking of schedule ' . $pc['title'] . ' is required';
                    array_push($this->failing_conditions, $remcondition);
                }

            }
        }
        return;
    }

    private function getMessageBasedOnAmountType()
    {
        if ($this->amount_type == 'Fixed') {
            return "$this->amount Euro discount";
        } else if ($this->amount_type == 'Percent') {
            return "$this->amount%  discount";
        } else if ($this->amount_type == 'Quantity') {
            return "$this->amount bookings for free";
        }
    }

    public function courses_discount()
    {
        //$cart = Session::instance()->get(Model_BookingsCart::CART_NAME);
    }

    /*** Private Functions ***/

    private function _sql_insert_discount($set_id = FALSE)
    {
        $data = $this->get_instance();
        if (!empty($data['days_of_the_week'])) {
            $data['days_of_the_week'] = implode(',', $data['days_of_the_week']);
        } else {
            $data['days_of_the_week'] = '';
        }
        $q = DB::insert(self::DISCOUNTS_TABLE)->values($data)->execute();

        if ($set_id) {
            $this->set_id($q[0]);
        }

        return count($q) > 0 ? TRUE : FALSE;
    }

    private function _sql_set_contacts()
    {
        foreach ($this->for_contacts as $contact_id) {
            $exists = DB::select('*')
                ->from(self::FOR_CONTACTS_TABLE)
                ->where('discount_id', '=', $this->id)
                ->and_where('contact_id', '=', $contact_id)
                ->execute()
                ->current();
            if ($exists) {
                if ($exists['deleted'] == 1) {
                    DB::update(self::FOR_CONTACTS_TABLE)
                        ->set(array('deleted' => 0))
                        ->where('discount_id', '=', $this->id)
                        ->and_where('contact_id', '=', $contact_id)
                        ->execute();
                }
            } else {
                DB::insert(self::FOR_CONTACTS_TABLE)
                    ->values(array(
                        'discount_id' => $this->id,
                        'contact_id' => $contact_id
                    ))
                    ->execute();
            }
        }

        $deleteq = DB::update(self::FOR_CONTACTS_TABLE)
            ->set(array('deleted' => 1))
            ->where('discount_id', '=', $this->id);
        if ($this->for_contacts) {
            $deleteq->and_where('contact_id', 'not in', $this->for_contacts);
        }
        $deleteq->execute();
    }

    private function _sql_set_student_years()
    {
        foreach ($this->student_years as $year_id) {
            $exists = DB::select('*')
                ->from(self::HAS_STUDENT_YEARS_TABLE)
                ->where('discount_id', '=', $this->id)
                ->and_where('year_id', '=', $year_id)
                ->execute()
                ->current();
            if ($exists) {
                if ($exists['deleted'] == 1) {
                    DB::update(self::HAS_STUDENT_YEARS_TABLE)
                        ->set(array('deleted' => 0))
                        ->where('discount_id', '=', $this->id)
                        ->and_where('year_id', '=', $year_id)
                        ->execute();
                }
            } else {
                DB::insert(self::HAS_STUDENT_YEARS_TABLE)
                    ->values(array(
                        'discount_id' => $this->id,
                        'year_id' => $year_id
                    ))
                    ->execute();
            }
        }

        $deleteq = DB::update(self::FOR_CONTACTS_TABLE)
            ->set(array('deleted' => 1))
            ->where('discount_id', '=', $this->id);
        if ($this->for_contacts) {
            $deleteq->and_where('year_id', 'not in', $this->student_years);
        }
        $deleteq->execute();
    }

    private function _sql_set_daily_rates()
    {
        $ids = array();


        foreach ($this->daily_rates as $daily_rate) {
            if (is_numeric(@$daily_rate['id'])){
                DB::update(self::DAILY_RATES_TABLE)
                    ->set(
                        array(
                            'deleted' => 0,
                            'min_days' => $daily_rate['min_days'],
                            'max_days' => $daily_rate['max_days'],
                            'amount' => $daily_rate['amount'],
                            'is_consecutive' => @$daily_rate['is_consecutive'] ?: 0
                        ))
                    ->where('id', '=', $daily_rate['id'])
                    ->execute();
                $ids[] = $daily_rate['id'];
            } else {
                $inserted = DB::insert(self::DAILY_RATES_TABLE)
                    ->values(array(
                        'discount_id' => $this->id,
                        'min_days' => $daily_rate['min_days'],
                        'max_days' => $daily_rate['max_days'],
                        'amount' => $daily_rate['amount'],
                        'is_consecutive' => @$daily_rate['is_consecutive'] ?: 0
                    ))
                    ->execute();
                $ids[] = $inserted[0];
            }
        }

        if (count($ids)) {
            $deleteq = DB::update(self::DAILY_RATES_TABLE)
                ->set(array('deleted' => 1))
                ->where('discount_id', '=', $this->id)
                ->and_where('id', 'not in', $ids);
        } else {
            $deleteq = DB::update(self::DAILY_RATES_TABLE)
                ->set(array('deleted' => 1))
                ->where('discount_id', '=', $this->id);
        }

        $deleteq->execute();
    }
    private function _sql_set_per_day_rates()
    {
        $ids = array();


        foreach ($this->per_day_rates as $per_day_rate) {
            if (is_numeric(@$per_day_rate['id'])){
                DB::update(self::PER_DAY_RATES_TABLE)
                    ->set(
                        array(
                            'deleted' => 0,
                            'min_timeslots' => $per_day_rate['min_timeslots'],
                            'max_timeslots' => $per_day_rate['max_timeslots'],
                            'amount' => $per_day_rate['amount']
                        ))
                    ->where('id', '=', $per_day_rate['id'])
                    ->execute();
                $ids[] = $per_day_rate['id'];
            } else {
                $inserted = DB::insert(self::PER_DAY_RATES_TABLE)
                    ->values(array(
                        'discount_id' => $this->id,
                        'min_timeslots' => $per_day_rate['min_timeslots'],
                        'max_timeslots' => $per_day_rate['max_timeslots'],
                        'amount' => $per_day_rate['amount']
                    ))
                    ->execute();
                $ids[] = $inserted[0];
            }
        }

        if (count($ids)) {
            $deleteq = DB::update(self::PER_DAY_RATES_TABLE)
                ->set(array('deleted' => 1))
                ->where('discount_id', '=', $this->id)
                ->and_where('id', 'not in', $ids);
        } else {
            $deleteq = DB::update(self::PER_DAY_RATES_TABLE)
                ->set(array('deleted' => 1))
                ->where('discount_id', '=', $this->id);
        }

        $deleteq->execute();
    }

    private function _sql_set_qty_rates()
    {
        $ids = array();


        foreach ($this->qty_rates as $qty_rate) {
            if (is_numeric(@$qty_rate['id'])){
                DB::update(self::QTY_RATES_TABLE)
                    ->set(
                        array(
                            'deleted' => 0,
                            'min_qty' => $qty_rate['min_qty'],
                            'max_qty' => $qty_rate['max_qty'],
                            'amount' => $qty_rate['amount']
                        ))
                    ->where('id', '=', $qty_rate['id'])
                    ->execute();
                $ids[] = $qty_rate['id'];
            } else {
                $inserted = DB::insert(self::QTY_RATES_TABLE)
                    ->values(array(
                        'discount_id' => $this->id,
                        'min_qty' => $qty_rate['min_qty'],
                        'max_qty' => $qty_rate['max_qty'],
                        'amount' => $qty_rate['amount']
                    ))
                    ->execute();
                $ids[] = $inserted[0];
            }
        }

        if (count($ids)) {
            $deleteq = DB::update(self::QTY_RATES_TABLE)
                ->set(array('deleted' => 1))
                ->where('discount_id', '=', $this->id)
                ->and_where('id', 'not in', $ids);
        } else {
            $deleteq = DB::update(self::QTY_RATES_TABLE)
                ->set(array('deleted' => 1))
                ->where('discount_id', '=', $this->id);
        }

        $deleteq->execute();
    }

    private function _sql_set_schedules()
    {
        foreach ($this->has_schedules as $schedule_id) {
            $exists = DB::select('*')
                ->from(self::HAS_SCHEDULES_TABLE)
                ->where('discount_id', '=', $this->id)
                ->and_where('schedule_id', '=', $schedule_id)
                ->execute()
                ->current();
            if ($exists) {
                if ($exists['deleted'] == 1) {
                    DB::update(self::HAS_SCHEDULES_TABLE)
                        ->set(array('deleted' => 0))
                        ->where('discount_id', '=', $this->id)
                        ->and_where('schedule_id', '=', $schedule_id)
                        ->execute();
                }
            } else {
                DB::insert(self::HAS_SCHEDULES_TABLE)
                    ->values(array(
                        'discount_id' => $this->id,
                        'schedule_id' => $schedule_id
                    ))
                    ->execute();
            }
        }

        $deleteq = DB::update(self::HAS_SCHEDULES_TABLE)
            ->set(array('deleted' => 1))
            ->where('discount_id', '=', $this->id);
        if ($this->has_schedules) {
            $deleteq->and_where('schedule_id', 'not in', $this->has_schedules);
        }
        $deleteq->execute();
    }


    private function _sql_set_previous_booking_conditions()
    {
        DB::delete(self::HAS_PREVIOUS_BOOKING_CONDITION_TABLE)->where('discount_id', '=', $this->get_id())->execute();

        foreach ($this->has_previous_discount_conditions as $cond) {
            DB::insert(self::HAS_PREVIOUS_BOOKING_CONDITION_TABLE)
                ->values(array(
                    'discount_id' => $this->get_id(),
                    'ref_id' => $cond['ref_id'],
                    'type_id' => $cond['type_id']
                ))
                ->execute();
        }
    }

    private function _sql_set_courses()
    {
        foreach ($this->has_courses as $course_id) {
            $exists = DB::select('*')
                ->from(self::HAS_COURSES_TABLE)
                ->where('discount_id', '=', $this->id)
                ->and_where('course_id', '=', $course_id)
                ->execute()
                ->current();
            if ($exists) {
                if ($exists['deleted'] == 1) {
                    DB::update(self::HAS_COURSES_TABLE)
                        ->set(array('deleted' => 0))
                        ->where('discount_id', '=', $this->id)
                        ->and_where('course_id', '=', $course_id)
                        ->execute();
                }
            } else {
                DB::insert(self::HAS_COURSES_TABLE)
                    ->values(array(
                        'discount_id' => $this->id,
                        'course_id' => $course_id
                    ))
                    ->execute();
            }
        }

        $deleteq = DB::update(self::HAS_COURSES_TABLE)
            ->set(array('deleted' => 1))
            ->where('discount_id', '=', $this->id);
        if ($this->has_courses) {
            $deleteq->and_where('course_id', 'not in', $this->has_courses);
        }
        $deleteq->execute();
    }

    private function _sql_update_discount()
    {
        $data = $this->get_instance();
        if (!empty($data['days_of_the_week'])) {
            $data['days_of_the_week'] = implode(',', $data['days_of_the_week']);
        } else {
            $data['days_of_the_week'] = '';
        }

        return DB::update(self::DISCOUNTS_TABLE)->set($data)->where('id', '=', $this->id)->execute();
    }

    private function init()
    {
        $this->get(true);
        $this->set_type_title();
    }

    private function set_type_title()
    {
        $q = DB::select('title')->from(self::DISCOUNTS_TYPE_TABLE)->where('id', '=', $this->type)->execute()->as_array();
        $this->type_title = count($q) > 0 ? $q[0]['title'] : '';
    }

    private function _sql_load_discount()
    {
        $this->for_contacts = array();
        $contacts = DB::select('contact_id')
            ->from(self::FOR_CONTACTS_TABLE)
            ->where('discount_id', '=', $this->id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->as_array();
        foreach ($contacts as $contact) {
            $this->for_contacts[] = $contact['contact_id'];
        }

        $this->qty_rates = DB::select('*')
            ->from(self::QTY_RATES_TABLE)
            ->where('discount_id', '=', $this->id)
            ->and_where('deleted', '=', 0)
            ->order_by('min_qty', 'asc')
            ->execute()
            ->as_array();

        $this->daily_rates = DB::select('*')
            ->from(self::DAILY_RATES_TABLE)
            ->where('discount_id', '=', $this->id)
            ->and_where('deleted', '=', 0)
            ->order_by('min_days', 'asc')
            ->execute()
            ->as_array();

        $this->per_day_rates = DB::select('*')
            ->from(self::PER_DAY_RATES_TABLE)
            ->where('discount_id', '=', $this->id)
            ->and_where('deleted', '=', 0)
            ->order_by('min_timeslots', 'asc')
            ->execute()
            ->as_array();

        $this->student_years = array();
        $years = DB::select('year_id')
            ->from(self::HAS_STUDENT_YEARS_TABLE)
            ->where('discount_id', '=', $this->id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->as_array();
        foreach ($years as $year) {
            $this->student_years[] = $year['year_id'];
        }

        $schedules = DB::select('schedule_id')
            ->from(self::HAS_SCHEDULES_TABLE)
            ->where('discount_id', '=', $this->id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->as_array();
        foreach ($schedules as $schedule) {
            $this->has_schedules[] = $schedule['schedule_id'];
        }

        $courses = DB::select('course_id')
            ->from(self::HAS_COURSES_TABLE)
            ->where('discount_id', '=', $this->id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->as_array();
        foreach ($courses as $course) {
            $this->has_courses[] = $course['course_id'];
        }

        $discount_conditions = DB::select(
            'ref_id',
            self::HAS_PREVIOUS_BOOKING_CONDITION_TABLE . '.type_id',
            array(self::HAS_PREVIOUS_BOOKING_CONDITION_TYPES_TABLE . '.name', 'type_name'),
            DB::expr("CONCAT_WS(' ', courses.title, categories.category, schedules.name) AS title")
        )
            ->from(self::HAS_PREVIOUS_BOOKING_CONDITION_TABLE)
            ->join(self::HAS_PREVIOUS_BOOKING_CONDITION_TYPES_TABLE)
            ->on(self::HAS_PREVIOUS_BOOKING_CONDITION_TABLE . '.type_id', '=', self::HAS_PREVIOUS_BOOKING_CONDITION_TYPES_TABLE . '.id')
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'), 'left')
                ->on('ref_id', '=', 'schedules.id')
                ->on(self::HAS_PREVIOUS_BOOKING_CONDITION_TABLE . '.type_id', '=', DB::expr(3))
            ->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'left')
                ->on('ref_id', '=', 'courses.id')
                ->on(self::HAS_PREVIOUS_BOOKING_CONDITION_TABLE . '.type_id', '=', DB::expr(2))
            ->join(array(Model_Categories::TABLE_CATEGORIES, 'categories'), 'left')
                ->on('ref_id', '=', 'categories.id')
                ->on(self::HAS_PREVIOUS_BOOKING_CONDITION_TABLE . '.type_id', '=', DB::expr(1))
            ->where('discount_id', '=', $this->id)
            ->execute()
            ->as_array();

        $this->has_previous_discount_conditions = $discount_conditions;

        $discount = DB::select('*')->from(self::DISCOUNTS_TABLE)->where('id', '=', $this->id)->execute()->current();
        if ($discount) {
            if ($discount['days_of_the_week'] != '') {
                $discount['days_of_the_week'] = explode(',', $discount['days_of_the_week']);
            } else {
                $discount['days_of_the_week'] = array();
            }
        } else {
            $discount = $this->get_instance();
        }
        return $discount;
    }

    public function get_previous_discount_condition_schedule_details()
    {
        $schedules = array();
        if ($this->has_previous_discount_conditions) {
            $previous_schedules = array();

            foreach ($this->has_previous_discount_conditions as $cond) {
                if ($cond['type_id'] == 3) {
                    $previous_schedules[] = $cond['ref_id'];
                }

            }

            if ($previous_schedules)
                $schedules = DB::select('id', 'name')
                    ->from(array('plugin_courses_schedules', 'schedules'))
                    ->where('id', 'in', $previous_schedules)
                    ->execute()
                    ->as_array();
        }
        return $schedules;

    }


    public function get_previous_discount_condition_course_details()
    {
        $courses = array();
        if ($this->has_previous_discount_conditions) {
            $previous_courses = array();

            foreach ($this->has_previous_discount_conditions as $cond) {
                if ($cond['type_id'] == 2) {
                    $previous_courses[] = $cond['ref_id'];
                }

            }

            if ($previous_courses)
                $courses = DB::select('id', DB::expr("title"))
                    ->from('plugin_courses_courses')
                    ->where('id', 'in', $previous_courses)
                    ->execute()
                    ->as_array();

        }
        return $courses;

    }

    public function get_previous_discount_condition_category_details()
    {
        $category = array();
        if ($this->has_previous_discount_conditions) {
            $previous_categories = array();

            foreach ($this->has_previous_discount_conditions as $cond) {
                if ($cond['type_id'] == 1) {
                    $previous_categories[] = $cond['ref_id'];
                }

            }

            if ($previous_categories)
                $category = DB::select('id', DB::expr("category"))
                    ->from('plugin_courses_categories')
                    ->where('id', 'in', $previous_categories)
                    ->execute()
                    ->as_array();

        }

        return $category;

    }


    private function validate()
    {
        $this->set_valid_from($this->valid_from);
        $this->set_valid_to($this->valid_to);
        $this->set_valid_previous_paid_from($this->previous_term_paid_from);
        $this->set_valid_previous_paid_to($this->previous_term_paid_to);
    }

    private function test_matching_categories($cart_line_id, $lines, $usage)
    {
        if (!$cart_line_id) {
            return false;
        }
        if ($this->categories != '') {
            $categories = explode(',', $this->categories);
            $cat_names = $this->get_has_category_details();
            foreach ($cat_names as $i => $cat_name) {
                $cat_names[$i] = $cat_name['category'];
            }
            $others_matched = true;
            if ($this->item_quantity_min || $this->item_quantity_max) {
                $other_matched_items = array();
                foreach ($lines as $line) {
                    $course_category = Model_Schedules::get_schedule_category($line['id']);
                    if (in_array($course_category, $categories)) {
                        $other_matched_items[] = $line;
                    }
                }
                if ($this->item_quantity_scope == 'Family' || $this->item_quantity_scope == 'Student') {
                    foreach ($usage['all']['items'] as $previous_booking) {
                        $course_category = Model_Schedules::get_schedule_category($previous_booking['schedule_id']);
                        if (in_array($course_category, $categories)) {
                            $other_matched_items[] = $previous_booking;
                        }
                    }
                }

                $other_matched_item_count = 0;
                if ($this->item_quantity_type == 'Classes') {
                    foreach ($other_matched_items as $other_matched_item) {
                        if (isset($other_matched_item['periods_attending'])) {
                            $other_matched_item_count += count($other_matched_item['periods_attending']);
                        }
                    }
                } else {
                    $other_matched_item_count = count($other_matched_items);
                }
                if ($this->item_quantity_min && $other_matched_item_count < $this->item_quantity_min) {
                    $others_matched = false;
                }
                if ($this->item_quantity_max && $other_matched_item_count > $this->item_quantity_max) {
                    $others_matched = false;
                }
            }
            $course_category = Model_Schedules::get_schedule_category($cart_line_id);

            if (in_array($course_category, $categories) && $others_matched) {
                return true;
            } else {
                $remcondition = new Remaining_Conditions();
                $remcondition->setTypeImpossible();
                $remcondition->weight = count($categories);
                $remcondition->title = "This discount is only active for the following categories " . implode(', ', $cat_names);
                array_push($this->failing_conditions, $remcondition);
                return false;
            }
        } else {
            return true;
        }

    }

    public function test_matching_schedules($lines)
    {
        if ($this->has_schedules) {
            $details = $this->get_has_schedules_details();
            $remaining_conditions = array();

            $matched = false;
            foreach ($details as $schedule) {
                foreach ($lines as $line) {
                    if ($schedule['id'] == $line['id']) {
                        $matched = true;
                        break;
                    }
                }
                if ($matched) {
                    break;
                }
            }

            if (!$matched) {
                $remcondition = new Remaining_Conditions();
                $remcondition->setTypeCurrent();
                $remcondition->weight = 1;
                $remcondition->title = "Book classes from " . $schedule['name'] . ' to get ' . $this->getMessageBasedOnAmountType();
                array_push($remaining_conditions, $remcondition);
                //   return false;
            }

            $this->failing_conditions = array_merge($this->failing_conditions, $remaining_conditions);
            return count($remaining_conditions) == 0;
        } else {
            return true;
        }
    }

    public function test_matching_courses($lines, $cached = true)
    {
        static $cache_schedules = array();
        $matched = false;
        if ($this->has_courses) {
            $details = $this->get_has_courses_details();
            $course_id = null;
            $remaining_conditions = array();
            foreach ($this->has_courses as $course_id) {
                $schedule = null;
                foreach ($lines as $line) {
                    $course_id = $course_id;
                    if (@$cache_schedules[$line['id']] && $cached) {
                        $schedule = $cache_schedules[$line['id']];
                    } else {
                        $schedule = $cache_schedules[$line['id']] = Model_Schedules::get_schedule($line['id']);

                    }
                    if ($schedule) {
                        if ($course_id == $schedule['course_id']) {
                            $matched = true;
                            break;
                        }
                    }
                }

                if ($matched) {
                    break;
                }
            }

            if (!$matched) {
                foreach ($details as $course_details) {
                    if ($course_details['id'] == $course_id) {
                        $remcondition = new Remaining_Conditions();
                        $remcondition->weight = 1;
                        $remcondition->setTypeCurrent();
                        $remcondition->title = "Book one of the classes from " . $course_details['title'] . ' course to get ' . $this->getMessageBasedOnAmountType();
                        array_push($remaining_conditions, $remcondition);
                    }
                }
            }

            if ($remaining_conditions) {
                $this->failing_conditions = array_merge($this->failing_conditions, $remaining_conditions);
                return false;
            }

            return $matched;

        } else {
            return true;
        }
    }

    public function test_unassigned() {
        $matched =  !empty($this->has_courses) || !empty($this->has_schedules) || !empty($this->get_categories());
        if (!$matched) {
            $remcondition = new Remaining_Conditions();
            $remcondition->weight = 1;
            $remcondition->setTypeCurrent();
            $remcondition->title = "This discount is not applicable";
            $this->failing_conditions = array_merge($this->failing_conditions, array($remcondition));
        }
        return $matched;
    }

    /*** Public Static Functions ***/

    public static function create($id = NULL)
    {
        return new self($id);
    }

    public static function get_all_discounts_for_listing($params = array())
    {
        $q = DB::select(
            't1.*',
            array('t2.title', 'type_title'),
            DB::expr("GROUP_CONCAT(schedules.name) AS `schedules`")
        )
            ->from(array(self::DISCOUNTS_TABLE, 't1'))
            ->join(array(self::DISCOUNTS_TYPE_TABLE, 't2'), 'LEFT')
            ->on('t1.type', '=', 't2.id')
            ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'LEFT')
            ->on('t1.id', '=', 'has_schedules.discount_id')
            ->join(array('plugin_courses_schedules', 'schedules'), 'LEFT')
            ->on('has_schedules.schedule_id', '=', 'schedules.id')
            ->where('t1.delete', '=', 0);
        if (isset($params['is_package'])) {
            $q->and_where('t1.is_package', '=', $params['is_package']);
        }
        if (isset($params['published'])) {
            $q->and_where('t1.publish', '=', $params['published']);
        }
        if (isset($params['publish_on_web'])) {
            $q->and_where('t1.publish_on_web', '=', $params['publish_on_web']);
        }
        $q->group_by('t1.id');
        $q->order_by('t1.application_order', 'ASC');
        $q->order_by('t1.id', 'ASC');
        $discounts = $q->execute()->as_array();
        return $discounts;
    }

    public static function get_all_discount_types()
    {
        return DB::select('id', 'title')->from(self::DISCOUNTS_TYPE_TABLE)->where('delete', '=', 0)->execute()->as_array();
    }

    public static function toggle_publish($publish)
    {
        DB::query(Database::UPDATE, 'UPDATE plugin_bookings_discounts SET publish = 1 - publish WHERE id = ' . $publish)->execute();
    }

    public static function get_all_discounts()
    {
        return DB::select('id')
            ->from(self::DISCOUNTS_TABLE)
            ->where('delete', '=', 0)
            ->and_where('publish', '=', 1)
            ->and_where('valid_from', '<=', date('Y-m-d H:i:s', time()))
            ->and_where(DB::expr("DATE_ADD(valid_to, INTERVAL 1 DAY)"), '>', date('Y-m-d H:i:s'))
            ->order_by('valid_from', 'ASC')
            ->execute()
            ->as_array();
    }

    public static function get_course_cats_from_discount_x($x)
    {
        $x = explode(',', $x);
        $cats = DB::select(DB::expr('GROUP_CONCAT(category) AS cats'))
            ->from('plugin_courses_categories')
            ->where('id', 'in', $x)
            ->execute()
            ->get('cats');
        return $cats;
    }

    public static function search($params = array())
    {
        $query = DB::select('*')
            ->from(self::DISCOUNTS_TABLE)
            ->where('delete', '=', 0);
        $query->and_where('valid_from', '<=', date::now());
        $query->and_where('valid_to', '>=', date::today());
        if (array_key_exists('code', $params)) {
            $query->and_where('code', '=', $params['code']);
        }
        if (@$params['is_coupon']) {
            $query->and_where('code', '<>', '');
            $query->and_where('code', 'is not', null);
        }
        if (@$params['term']) {
            $query->and_where('code', 'like', '%' . $params['term'] . '%');
        }
        if (array_key_exists('publish_on_web', $params)) {
            $query->and_where('publish_on_web', '=', $params['publish_on_web']);
        }

        $discounts = $query->execute()->as_array();
        return $discounts;
    }

    public static function validate_coupon($code, $publish_on_web = 0)
    {
        $params = array('code' => $code, 'is_coupon' => 1);
        if ($publish_on_web) {
            $params['publish_on_web'] = $publish_on_web;
        }
        $discounts = self::search($params);
        return count($discounts) > 0 ? true : false;
    }

    public static function old_get_all_discounts_with_advanced_search($locations=Array(),$subjects=Array(),$categories=Array(),$topics=Array(),$courses=Array(),$keyword = null, $start = 0, $offset=20)
    {
        ( sizeof($locations)==0 ) ? $locations='' : $locations=implode(',',$locations);
        ( sizeof($subjects)==0 ) ? $subjects='' : $subjects=implode(',',$subjects);
        ( sizeof($categories)==0 ) ? $categories='' : $categories=implode(',',$categories);
        ( sizeof($topics)==0 ) ? $topics='' : $topics=implode(',',$topics);
        ( sizeof($courses)==0 ) ? $courses='' : $courses=implode(',',$courses);
        ( sizeof($keyword)==0 ) ? $keyword = '%' : $keyword = '%'.$keyword.'%';
//        is_null($keyword) ? $keyword = '%' : $keyword = '%'.$keyword.'%';

        if(!empty($courses)){
            if(!empty($locations)){
                $sql = " SELECT  id, SUM(priority_for_courses) AS priority_for_courses, SUM(priority_for_loc) AS priority_for_loc, title, discount_summary";
            }else{
                $sql = " SELECT  id, SUM(priority_for_courses) AS priority_for_courses, title, discount_summary";
            }
        }else{
            if(!empty($locations)){
                $sql = " SELECT  id, SUM(priority_for_loc) AS priority_for_loc, title, discount_summary";
            }else{
                $sql = " SELECT  id,title, discount_summary";
            }
        }

        $sql .=" , schedule_schedule_name,schedule_courses_subject_name,schedule_courses_subject_summary,schedule_courses_category_name,schedule_courses_category_summary,schedule_courses_topic_name,schedule_courses_topic_description,
	               courses_courses_title,course_courses_schedule_name,schedule_courses_subject_name,schedule_courses_subject_summary,schedule_courses_category_name,schedule_courses_category_summary,schedule_courses_topic_name,schedule_courses_topic_description
                
                FROM ( SELECT discounts.id AS id, discounts.title AS title, discounts.summary AS discount_summary,
                    plugin_bookings_discounts_has_schedules.`schedule_id` AS discount_has_schedule_id,
                    plugin_courses_schedules.id AS schedule_schedule_id,
                    plugin_courses_schedules.`location_id` AS schedule_location_id,
                    `cc`.id AS schedule_course_id,
                    `cc`.title AS schedule_course_title,
                    `cc`.summary AS schedule_course_summary,
                    `cc`.subject_id AS schedule_course_subject_id,
                    `cc`.category_id AS schedule_course_category_id,
                     cct.topic_id AS schedule_course_topic_id,
                     plugin_courses_schedules.name AS schedule_schedule_name,
                     ccs.name AS schedule_courses_subject_name,
                     ccs.summary AS schedule_courses_subject_summary,
                     ccc.category AS schedule_courses_category_name,
                     ccc.summary AS schedule_courses_category_summary,
                     cctt.name AS schedule_courses_topic_name,
                     cctt.description AS schedule_courses_topic_description,
                                
                    plugin_bookings_discounts_has_courses.`course_id` AS discount_has_course_id,
                    plugin_courses_courses.`id` AS  courses_courses_id,
                    plugin_courses_courses.subject_id AS  courses_course_subject_id,
                    plugin_courses_courses.category_id AS  courses_course_category_id,
                    plugin_courses_courses_has_topics.topic_id AS course_course_topic_id,
                    cs.id AS courses_schedule_id,
                    cs.location_id AS courses_schedule_location_id, 
                     plugin_courses_courses.title AS courses_courses_title,
                     plugin_courses_courses.summary AS courses_courses_summary,
                     cs.name AS course_courses_schedule_name,
                     plugin_courses_subjects.name AS course_courses_subject_name,
                     plugin_courses_subjects.summary AS course_courses_subject_summary,
                     plugin_courses_categories.category AS course_courses_category_name,
                     plugin_courses_categories.summary AS course_courses_category_summary,
                     plugin_courses_topics.name AS course_courses_topic_name,
                     plugin_courses_topics.description AS course_courses_topic_description";

        if(!empty($courses)){
            $sql .= ", CASE  WHEN (`cc`.id IN (:courses)  OR  plugin_courses_courses.`id` IN (:courses))
                            THEN 1
                            ELSE 0
                        END  AS  priority_for_courses";
        }

        if(!empty($locations)){
            $sql .= ", CASE  WHEN (plugin_courses_schedules.`location_id` IN (:locations)  OR  cs.location_id IN (:locations))
                            THEN 1
                            ELSE 0
                        END  AS  priority_for_loc";
        }

        $sql .= " FROM `plugin_bookings_discounts` AS `discounts` 
                    LEFT JOIN `plugin_bookings_discounts_has_schedules` ON (`plugin_bookings_discounts_has_schedules`.`discount_id`=`discounts`.id 
                        AND plugin_bookings_discounts_has_schedules.`deleted` = 0)
                    LEFT JOIN plugin_courses_schedules ON (plugin_bookings_discounts_has_schedules.`schedule_id` = plugin_courses_schedules.id
                        AND ( IF (plugin_courses_schedules.`booking_type`='One Timeslot', (plugin_courses_schedules.`end_date` >= NOW()),(plugin_courses_schedules.`start_date` <= NOW() AND plugin_courses_schedules.`end_date` >= NOW()) ) ) 
                        AND plugin_courses_schedules.`delete` = 0 
                        AND plugin_courses_schedules.`publish`=1 )
                    LEFT JOIN `plugin_courses_courses` cc ON (`cc`.id = plugin_courses_schedules.`course_id` 
                        AND cc.`publish`=1 AND cc.`deleted`=0)
                    LEFT JOIN `plugin_courses_courses_has_topics` cct ON (cct.course_id = cc.id AND cct.deleted = 0) 
                    LEFT JOIN `plugin_courses_subjects` ccs ON (cc.subject_id = ccs.id AND ccs.publish = 1 AND ccs.deleted = 0  ) 
                    LEFT JOIN `plugin_courses_categories` ccc ON (cc.category_id = ccc.id AND ccc.publish = 1 AND ccc.delete = 0  ) 
                    LEFT JOIN `plugin_courses_topics` cctt ON (cct.topic_id = cctt.id AND cctt.deleted = 0  )  
                    LEFT JOIN `plugin_bookings_discounts_has_courses` ON (`plugin_bookings_discounts_has_courses`.`discount_id`=`discounts`.id 
                        AND plugin_bookings_discounts_has_courses.`deleted`=0 )
                    LEFT JOIN `plugin_courses_courses` ON (`plugin_courses_courses`.id = plugin_bookings_discounts_has_courses.`course_id` 
                        AND plugin_courses_courses.`deleted` = 0 AND plugin_courses_courses.`publish`=1)
                    LEFT JOIN `plugin_courses_courses_has_topics` ON (plugin_courses_courses_has_topics.course_id = plugin_courses_courses.id 
                        AND plugin_courses_courses_has_topics.deleted=0 )   
                    LEFT JOIN `plugin_courses_schedules` cs ON ( cs.course_id = plugin_courses_courses.id 
                        AND cs.delete=0 AND cs.`publish` = 1 
                        AND ( IF (cs.`booking_type`='One Timeslot', (cs.`end_date` >= NOW()),(cs.`start_date` <= NOW() AND cs.`end_date` >= NOW()) ) ) )
                    LEFT JOIN `plugin_courses_subjects` ON (plugin_courses_courses.subject_id = plugin_courses_subjects.id AND plugin_courses_subjects.publish = 1 AND plugin_courses_subjects.deleted = 0  ) 
                    LEFT JOIN `plugin_courses_categories` ON (plugin_courses_courses.category_id = plugin_courses_categories.id AND plugin_courses_categories.publish = 1 AND plugin_courses_categories.delete = 0  ) 
                    LEFT JOIN `plugin_courses_topics` ON (plugin_courses_courses_has_topics.topic_id = plugin_courses_topics.id AND plugin_courses_topics.deleted = 0  )  
                    WHERE 
                       discounts.publish = 1
                       AND discounts.delete = 0
                       AND ( discounts.valid_from <= NOW() AND discounts.valid_to >= NOW() AND discounts.valid_from < DATE_ADD(NOW(),INTERVAL 1 YEAR)) 
                       AND (plugin_courses_schedules.id IS NOT NULL OR plugin_courses_courses.`id` IS NOT NULL) 
                   ) result 
                   WHERE ( result.schedule_schedule_id IS NOT NULL AND result.schedule_course_id IS NOT NULL ";

        if(!empty($subjects)){
            $sql .= " AND ( result.schedule_course_subject_id IN (:subjects) ";
            if(!empty($categories)){
                $sql .= " OR result.schedule_course_category_id IN (:categories) ";
                if(!empty($topics)){
                    $sql .= " OR result.schedule_course_topic_id IN (:topics) ))";
                }else{
                    $sql .= " ))";
                }
            }else{
                if(!empty($topics)){
                    $sql .= " OR result.schedule_course_topic_id IN (:topics) ))";
                }else{
                    $sql .= " ))";
                }
            }
        }else if(!empty($categories)){
            $sql .= " AND (result.schedule_course_category_id IN (:categories) ";
            if(!empty($topics)){
                $sql .= " OR result.schedule_course_topic_id IN (:topics) ))";
            }else{
                $sql .= " ))";
            }
        }else if(!empty($topics)){
            $sql .= " AND ( result.schedule_course_topic_id IN (:topics) ))";
        }else{
            $sql .= " )";
        }

        $sql .= " OR (result.courses_courses_id IS NOT NULL ";

        if(!empty($subjects)){
            $sql .= "AND ( result.courses_course_subject_id IN (:subjects) ";
            if(!empty($categories)){
                $sql .= " OR result.courses_course_category_id IN (:categories) ";
                if(!empty($topics)){
                    $sql .= " OR result.course_course_topic_id IN (:topics) ))";
                }else{
                    $sql .= ")) ";
                }
            }else{
                if(!empty($topics)){
                    $sql .= " OR result.course_course_topic_id IN (:topics) ))";
                }else{
                    $sql .= ")) ";
                }
            }
        }else if(!empty($categories)){
            $sql .= "AND (  result.schedule_course_category_id IN (:categories) ";
            if(!empty($topics)){
                $sql .= " OR result.schedule_course_topic_id IN (:topics) ))";
            }else{
                $sql .= ")) ";
            }
        }else if(!empty($topics)){
            $sql .= "AND ( result.schedule_course_topic_id IN (:topics) ))";
        }else{
            $sql .= " ) ";
        }

        $sql .= "  AND ( title LIKE :keyword OR discount_summary LIKE :keyword 
                        OR schedule_schedule_name LIKE :keyword OR schedule_course_title LIKE :keyword OR schedule_course_summary LIKE :keyword OR schedule_courses_subject_name LIKE :keyword OR schedule_courses_subject_summary LIKE :keyword OR schedule_courses_category_name LIKE :keyword OR schedule_courses_category_summary LIKE :keyword OR schedule_courses_topic_name LIKE :keyword OR schedule_courses_topic_description LIKE :keyword 
                        OR courses_courses_title LIKE :keyword OR courses_courses_summary LIKE :keyword OR course_courses_schedule_name LIKE :keyword OR course_courses_subject_name LIKE :keyword OR course_courses_subject_summary LIKE :keyword OR course_courses_category_name LIKE :keyword OR course_courses_category_summary LIKE :keyword OR course_courses_topic_name LIKE :keyword OR course_courses_topic_description LIKE :keyword ) ";
        $sql .= " GROUP BY id ";

        if(!empty($courses)){
            if(!empty($locations)){
                $sql .= " ORDER BY priority_for_courses DESC, priority_for_loc DESC, id ";
            }else{
                $sql .= " ORDER BY priority_for_courses DESC, id DESC ";
            }
        }else{
            if(!empty($locations)){
                $sql .= " ORDER BY priority_for_loc DESC, id DESC ";
            }else{
                $sql .= " ORDER BY id DESC ";
            }
        }

        $sql .= " LIMIT :start, :offset";

        $query = DB::query(Database::SELECT, $sql);

        $query->param(':locations', $locations);
        $query->param(':subjects', $subjects);
        $query->param(':categories', $categories);
        $query->param(':topics', $topics);
        $query->param(':courses', $courses);
        $query->param(':keyword', $keyword);
        $query->param(':start', $start);
        $query->param(':offset', $offset);

        $result = $query->execute()->as_array();

        return $result;
    }


    public static function get_all_discounts_with_advanced_search($post, $args, &$totalCount)
    {
        $course_search = Model_KES_Discount::get_all_courses_with_advanced_search($post, $args, $totalCount);
        $schedule_ids  = [];

        foreach ($course_search['courses'] as $course) {
            $schedule_ids[] = $course['schedule_id'];
        }

        return self::get_discounts_for_schedule($schedule_ids, ['publish_on_web' => true]);
    }

    private static function implode_int($a)
    {
        foreach ($a as $key => $value) {
            if (!is_numeric($value)) {
                unset($a[$key]);
            }
        }
        return implode(',', $a);
    }

    public static function get_all_courses_with_advanced_search($filters, $args, &$totalCount)    {
        $locations    = isset($filters['location_ids'])  ? $filters['location_ids']  : array();
        $trainers     = isset($filters['trainer_ids'])  ? $filters['trainer_ids']  : array();
        $subjects     = isset($filters['subject_ids'])   ? $filters['subject_ids']   : array();
        $categories   = isset($filters['category_ids'])  ? $filters['category_ids']  : array();
        $topics       = isset($filters['topic_ids'])     ? $filters['topic_ids']     : array();
        $courses      = isset($filters['course_ids'])    ? $filters['course_ids']    : array();
        $years        = isset($filters['year_ids'])      ? $filters['year_ids']      : array();
        $types        = isset($filters['type_ids'])      ? $filters['type_ids']      : array();
        $levels       = isset($filters['level_ids'])     ? $filters['level_ids']     : array();
        $schedule_ids = isset($filters['schedule_ids'])  ? $filters['schedule_ids']  : array();
        $keyword      = isset($filters['keywords'])      ? $filters['keywords']      : '';
        $cycles       = isset($filters['cycles'])  ? $filters['cycles']  : array();
        $is_fulltimes = isset($filters['is_fulltimes'])  ? $filters['is_fulltimes']  : array();
        $given_date  = @$filters['given_date'];
        $start   = isset($args['start'])    ? $args['start']    : 0;
        $limit   = isset($args['limit'])    ? $args['limit']    : '';
        $orderBy = isset($args['order_by']) ? $args['order_by'] : '';

        $category_join = 'LEFT';
        if (count($categories)) {
            $category_join = 'INNER';
        }

        if (is_array($years) && count($years) > 0) {
            $years_test = DB::select('*')
                ->from(Model_Years::YEARS_TABLE)
                ->where('id', 'in', $years)
                ->execute()
                ->as_array();
            foreach ($years_test as $year_test) {
                if ($year_test['year'] == 'All Levels') {
                    $years = array();
                    break;
                }
            }
        }

        ( sizeof($locations)==0 ) ? $locations='' : $locations=self::implode_int($locations);
        ( sizeof($trainers)==0 ) ? $trainers='' : $trainers=self::implode_int($trainers);
        ( sizeof($subjects)==0 ) ? $subjects='' : $subjects=self::implode_int($subjects);
        ( sizeof($categories)==0 ) ? $categories='' : $categories=self::implode_int($categories);
        ( sizeof($topics)==0 ) ? $topics='' : $topics=self::implode_int($topics);
        ( sizeof($courses)==0 ) ? $courses='' : $courses=self::implode_int($courses);
        ( sizeof($years)==0 ) ? $years='' : $years=self::implode_int($years);
        ( sizeof($types)==0 ) ? $types='' : $types=self::implode_int($types);
        ( sizeof($levels)==0 ) ? $levels='' : $levels=self::implode_int($levels);
        is_array($schedule_ids) && count($schedule_ids) > 0 ? $schedule_ids = self::implode_int($schedule_ids) : $schedule_ids = '';

        $cycles_sql = '';
        if (is_array($cycles) && count($cycles) > 0) {
            $cycles_sql = array();
            foreach ($cycles as $cycle) {
                $cycles_sql[] = 'FIND_IN_SET(\'' . $cycle . '\', plugin_courses_courses.cycle)';
            }
            $cycles_sql = ' AND (' . implode(' OR ', $cycles_sql) . ')';
        }

        $fulltime_sql = '';
        if (is_array($is_fulltimes) && count($is_fulltimes) > 0) {
            $is_fulltimes_sql = array();
            foreach ($is_fulltimes as $is_fulltime) {
                $is_fulltimes_sql[] = 'plugin_courses_courses.is_fulltime = \'' . $is_fulltime . '\'';
            }
            $fulltime_sql = ' AND (' . implode(' OR ', $is_fulltimes_sql) . ')';
        }

        ($keyword == 'null' || $keyword == null) ? $keyword = '%' : $keyword = '%'.$keyword.'%';

        $sql ="SELECT
                SQL_CALC_FOUND_ROWS DISTINCT plugin_courses_courses.id,
                    plugin_courses_courses.`category_id`,
                    plugin_courses_categories.category,
                    plugin_courses_courses.display_availability,
                    plugin_courses_courses.title,
                    plugin_courses_schedules.attend_all_default,
                    plugin_courses_courses.summary,
                    plugin_courses_courses_images.`image`,
                    plugin_courses_subjects.`name` AS `subject`,
                    plugin_courses_years.`year`,
                    plugin_courses_levels.`level`,
                    plugin_courses_schedules.id AS `schedule_id`,
                    plugin_courses_schedules.name as `schedule`,
                    plugin_courses_schedules.display_timeslots_on_frontend AS `display_timeslots_on_frontend`,
                    plugin_courses_types.type as type,
                    plugin_courses_schedules.`amendable`,
                    min(plugin_courses_schedules_events.datetime_start) as datetime_start ";
        $sql .=",plugin_courses_courses.is_fulltime, plugin_courses_courses.fulltime_price FROM plugin_courses_courses
                LEFT JOIN plugin_courses_schedules ON (plugin_courses_schedules.`course_id` = plugin_courses_courses.id
                    AND ( IF (plugin_courses_schedules.`booking_type`='One Timeslot', (plugin_courses_schedules.`end_date` >= NOW()),(plugin_courses_schedules.`end_date` >= NOW()) ) )
                    AND `plugin_courses_schedules`.`delete` = 0
                    AND `plugin_courses_schedules`.`publish`= 1
                    AND `plugin_courses_schedules`.`book_on_website` = 1)
                LEFT JOIN plugin_courses_courses_images ON (plugin_courses_courses_images.id = plugin_courses_courses.id AND plugin_courses_courses_images.deleted = 0 AND plugin_courses_courses_images.`image` IS NOT NULL)
                LEFT JOIN plugin_courses_locations ON (plugin_courses_locations.id = plugin_courses_schedules.`location_id` AND plugin_courses_locations.`delete`=0 AND plugin_courses_locations.`publish`=1)
                LEFT JOIN plugin_courses_courses_has_topics ON (plugin_courses_courses.id = plugin_courses_courses_has_topics.`course_id` AND plugin_courses_courses_has_topics.`deleted`=0)
                LEFT JOIN plugin_courses_topics ON (plugin_courses_topics.id = plugin_courses_courses_has_topics.`topic_id` AND plugin_courses_topics.`deleted`=0 )
                LEFT JOIN plugin_courses_subjects ON (plugin_courses_subjects.id = plugin_courses_courses.`subject_id` AND plugin_courses_subjects.`deleted`=0 AND plugin_courses_subjects.`publish`=1)
                $category_join JOIN plugin_courses_categories ON (plugin_courses_categories.id = plugin_courses_courses.`category_id` AND plugin_courses_categories.`delete`=0 AND plugin_courses_categories.`publish`=1)
                LEFT JOIN plugin_courses_types ON (plugin_courses_types.id = plugin_courses_courses.`type_id` AND plugin_courses_types.`delete`=0 AND plugin_courses_types.`publish`=1)
                LEFT JOIN plugin_courses_courses_has_years ON (plugin_courses_courses.id = plugin_courses_courses_has_years.course_id)
                LEFT JOIN plugin_courses_courses_has_providers ON plugin_courses_courses_has_providers.course_id   = plugin_courses_courses.id
                LEFT JOIN plugin_courses_providers     ON plugin_courses_courses_has_providers.provider_id = plugin_courses_providers.id
                LEFT JOIN plugin_courses_years ON (plugin_courses_years.id = plugin_courses_courses_has_years.`year_id` AND plugin_courses_years.`delete`=0 AND plugin_courses_years.`publish`=1)
                LEFT JOIN plugin_courses_levels ON (plugin_courses_levels.id = plugin_courses_courses.`level_id` AND plugin_courses_levels.`delete`=0 AND plugin_courses_levels.`publish`=1)
                " . (1 ? "INNER JOIN plugin_courses_schedules_events ON plugin_courses_schedules.id = plugin_courses_schedules_events.schedule_id AND plugin_courses_schedules_events.delete = 0 " : "") . "
                WHERE
                (plugin_courses_courses.is_fulltime = 'YES' OR plugin_courses_schedules.id IS NOT NULL)
                AND plugin_courses_courses.`publish`=1
                AND plugin_courses_courses.`deleted`=0 ";
        if(isset($args['cancelled_schedules']) && $args['cancelled_schedules'] === false) {
            $sql .= " AND `plugin_courses_schedules`.`schedule_status` <> " . Model_Schedules::CANCELLED . " ";
        }
        if(!empty($courses)) {
            $sql .= " AND plugin_courses_courses.id IN ($courses) ";
        }
        if(!empty($subjects)) {
            $sql .= " AND plugin_courses_courses.`subject_id` IN ($subjects) ";
        }
        if(!empty($categories)) {
            $sql .= " AND plugin_courses_courses.`category_id` IN ($categories) ";
        }
        if(!empty($years)) {
            $sql .= " AND (plugin_courses_years.`id` IN ($years) OR plugin_courses_years.year = 'All Levels')";
        }
        if(!empty($types)) {
            $sql .= " AND plugin_courses_courses.`type_id` IN ($types) ";
        }
        if(!empty($topics)) {
            $sql .= " AND plugin_courses_topics.id IN ($topics) ";
        }
        if(!empty($locations)) {
            $sql .= " AND (`plugin_courses_schedules`.`location_id` IN ($locations) OR `plugin_courses_locations`.`parent_id` IN ($locations)) ";
        }
        if(!empty($trainers)) {
            $sql .= " AND (`plugin_courses_schedules`.`trainer_id` IN ($trainers) OR `plugin_courses_schedules_events`.`trainer_id` IN ($trainers)) ";
        }
        if(!empty($levels)) {
            $sql .= " AND plugin_courses_courses.`level_id` IN ($levels) ";
        }
        if ($schedule_ids) {
            $sql .= " AND plugin_courses_schedules.`id` IN ($schedule_ids) ";
        }
        if ($given_date) {
            $date_start = date('Y-m-d H:i:s', strtotime($given_date));
            $date_end = date('Y-m-d H:i:s', strtotime($given_date) + (3600 * 24 * 7));
            $sql .= " AND plugin_courses_schedules_events.datetime_start >= '" . $date_start . "' AND plugin_courses_schedules_events.datetime_end <= '" . $date_end . "' ";
        } else {
            $sql .= " AND plugin_courses_schedules_events.datetime_start >= NOW() ";
        }

        $provider_ids = Model_Providers::get_providers_for_host();

        if ($provider_ids) {
            $sql .= " AND `plugin_courses_providers`.`id` IN (".implode(',', $provider_ids).") ";
        }

        $sql .= $cycles_sql;

        $sql .= $fulltime_sql;

        if ($keyword != '%') {
            $sql .= " AND ( plugin_courses_locations.`name` LIKE :keyword
                      OR plugin_courses_courses.`title` LIKE :keyword
                      OR plugin_courses_courses.`summary` LIKE :keyword
                      OR plugin_courses_topics.`name` LIKE :keyword
                      OR plugin_courses_subjects.`name` LIKE :keyword
                      OR plugin_courses_categories.`category` LIKE :keyword
                      OR plugin_courses_types.`type` LIKE :keyword
                      OR plugin_courses_levels.`level` LIKE :keyword
                      OR plugin_courses_years.`year` LIKE :keyword ) ";
        }

        $sql .= " GROUP BY plugin_courses_schedules.id ";
        $sql_limit = ($limit) ? " LIMIT :start, :limit " : " ";

        $query = DB::query(Database::SELECT,  $sql.$orderBy . $sql_limit);
        $query->param(':keyword', $keyword);
        $query->param(':start',   $start);
        $query->param(':limit',   $limit);
        $courses = $query->execute()->as_array();
        $totalCount = DB::select(DB::expr("FOUND_ROWS() as qty"))->execute()->get('qty');

        $min_date = 0;
        foreach ($courses as $key => $course) {
            if (strtotime($course['datetime_start']) >= time()) {
                if ($min_date == 0) {
                    $min_date = strtotime($course['datetime_start']);
                } else {
                    $min_date = min(strtotime($course['datetime_start']), $min_date);
                }
            }
            $courses[$key]['images'] = Model_Courses::get_images($course['id'], -1, null, 'course_image.id', 'asc');
            if ($course['is_fulltime'] == 'YES') {
                $courses[$key]['paymentoptions'] = DB::select('*')
                ->from(Model_Courses::TABLE_HAS_PAYMENTOPTIONS)
                    ->where('course_id', '=', $course['id'])
                    ->and_where('deleted', '=', 0)
                    ->and_where('published', '=', 1)
                    ->order_by('months', 'asc')
                    ->execute()
                    ->as_array();
            }
        }

        return array('courses' => $courses, 'total' => $totalCount, 'min_date' => date('Y-m-d', $min_date));
    }

    public static function get_course_ids_for_package($id=null, $offset=0, $limit=1000)
    {
        if(!$id){
            return false;
        }

        $categories = DB::select('categories')
            ->from('plugin_bookings_discounts')
            ->where('id', '=', $id)
            ->execute()
            ->get('categories');
        if ($categories) {
            $categories = explode(',', $categories);
            $select = DB::select('id')
                ->from('plugin_courses_courses')
                ->where('category_id', 'in', $categories)
                ->and_where('deleted', '=', 0)
                ->and_where('publish', '=', 1);
        } else {
            $schedules = DB::select('schedule_id')
                ->from('plugin_bookings_discounts_has_schedules')
                ->where('discount_id', '=', $id)
                ->and_where('deleted', '=', 0)
                ->execute()
                ->as_array();
            if (count($schedules)) {
                $schedule_ids = array();
                foreach ($schedules as $schedule) {
                    $schedule_ids[] = $schedule['schedule_id'];
                }
                $select = DB::select(array('course_id', 'id'))
                    ->from('plugin_courses_schedules')
                    ->where('id', 'in', $schedule_ids);
            } else {
                $select = DB::select(array('course_id', 'id'))
                    ->from('plugin_bookings_discounts_has_courses')
                    ->where('discount_id', '=', $id)
                    ->and_where('deleted', '=', 0);
            }
        }

        if ($limit) {
            $select->limit($limit);
        }
        if ($offset) {
            $select->offset($offset);
        }

        $courses = $select->execute()->as_array();

        foreach ($courses as $key => $course) {
            $courses[$key]['images'] = Model_Courses::get_images($course['id'], -1, null, 'course_image.id', 'asc');
        }

        return $courses;
    }

    public static function get_discounts_for_schedule($schedule_ids, $args = [])
    {
        if (is_numeric($schedule_ids)) {
            $schedule_ids = [$schedule_ids];
        }

        if (empty($schedule_ids)) {
            return [];
        }

        DB::query(null, "CREATE TEMPORARY TABLE _tmp_discounts (id INT PRIMARY KEY)")->execute();

        DB::query(null, "INSERT IGNORE INTO _tmp_discounts (id)
        (select
            distinct discounts.id
        from plugin_bookings_discounts discounts
            inner join plugin_bookings_discounts_has_schedules has
                on discounts.id = has.discount_id
            inner join plugin_courses_schedules schedules
                on has.schedule_id = schedules.id
                and discounts.schedule_type like IF(`schedules`.`payment_type` = 1, '%Prepay%', '%PAYG%')
        where has.schedule_id in (".implode(', ', $schedule_ids)."))")->execute();

        DB::query(null, "INSERT IGNORE INTO _tmp_discounts (id)
        (select
            distinct discounts.id
        from plugin_bookings_discounts discounts
            inner join plugin_bookings_discounts_has_courses has
                on discounts.id = has.discount_id
            inner join plugin_courses_schedules schedules
                on has.course_id = schedules.course_id
                and discounts.schedule_type like IF(`schedules`.`payment_type` = 1, '%Prepay%', '%PAYG%')
        where schedules.id in (".implode(', ', $schedule_ids)."))")->execute();

        DB::query(null, "INSERT IGNORE INTO _tmp_discounts (id)
        (select
            distinct discounts.id
        from plugin_bookings_discounts discounts
            inner join plugin_courses_courses courses
                on (discounts.categories = courses.category_id or discounts.categories like concat('%,',courses.category_id, ',%') or discounts.categories like concat('%,',courses.category_id) or discounts.categories like concat(courses.category_id, ',%'))
            inner join plugin_courses_schedules schedules
                on courses.id = schedules.course_id
                and discounts.schedule_type like IF(`schedules`.`payment_type` = 1, '%Prepay%', '%PAYG%')
        where schedules.id in (".implode(', ', $schedule_ids)."))")->execute();

        $discounts = DB::query(
            Database::SELECT,
            "SELECT plugin_bookings_discounts.*, `image`.`filename`, `image`.`location` AS `image_location`
              FROM _tmp_discounts
              inner join plugin_bookings_discounts on _tmp_discounts.id = plugin_bookings_discounts.id
              LEFT JOIN `plugin_media_shared_media` `image` ON `plugin_bookings_discounts`.`image_id` = `image`.`id`

              where plugin_bookings_discounts.delete = 0
              and plugin_bookings_discounts.publish=1 " .
              (isset($args['publish_on_web']) ? " AND plugin_bookings_discounts.publish_on_web=".($args['publish_on_web'] ? "1" : "0") : "").
              (isset($args['member_only']) ? " AND plugin_bookings_discounts.member_only=".($args['member_only'] ? "1" : "0") : "")
        )->execute()->as_array();

        DB::query(null, "DROP TEMPORARY TABLE IF EXISTS _tmp_discounts")->execute();

        return $discounts;
    }

    public static function get_discounts_for_course($course_id)
    {
        $query = DB::select('*')
            ->from('plugin_bookings_discounts_has_courses')
            ->join('plugin_bookings_discounts', 'inner')
            ->on('plugin_bookings_discounts_has_courses.discount_id', '=', 'plugin_bookings_discounts.id')
            ->where('plugin_bookings_discounts_has_courses.course_id', '=', $course_id);
        return $query->execute()->as_array();
    }

    public static function get_discount_modes($selected = null)
    {
        return HTML::optionsFromArray(
            array(
                'All' => __('All'),
                'Minimum' => __('Minimum'),
                'Maximum' => __('Maximum'),
            ),
            $selected
        );
    }
}


class Remaining_Conditions
{

    public function setTypeCurrent()
    {
        $this->type = "current";
    }

    public function setTypeFuture()
    {
        $this->type = "future";
    }

    public function setTypeImpossible()
    {
        $this->type = "impossible";
    }

    public $weight = 0;
    public $title = "";
    public $type = "current";

}

?>