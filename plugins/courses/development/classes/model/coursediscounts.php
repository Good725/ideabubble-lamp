<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 14/11/2014
 * Time: 14:34
 * This is not ideally modelled - couldn't think of a better way with time allotted.
 *
 */
class Model_Coursediscounts extends Model
{
    /*** Finals & Constants ***/
    CONST DISCOUNTS_TABLE       = 'plugin_courses_discounts';
    CONST FOR_CONTACTS_TABLE       = 'plugin_courses_discounts_for_contacts';
    const HAS_SCHEDULES_TABLE = 'plugin_courses_discounts_has_schedules';
    const HAS_COURSES_TABLE = 'plugin_courses_discounts_has_courses';

    /*** Private Member Data ***/
    private $id         = NULL;
    private $title      = '';
    private $summary    = '';
    private $type       = 0;
    private $code       = '';
    private $from       = 0;
    private $to         = 0;
    private $valid_from = '0000-00-00 00:00:00';
    private $valid_to   = '0000-00-00 00:00:00';
    private $publish    = 1;
    private $delete     = 0;
    private $categories = '';
    private $amount_type = 'Percent';
    private $amount     = '';
    private $schedule_type = 'Prepay,PAYG';
    private $item_quantity_min = null;
    private $item_quantity_max = null;
    private $item_quantity_scope = null;
    private $min_students_in_family = null;
    private $max_students_in_family = null;
    private $usage_limit = null;

    private $for_contacts = array();

    private $is_package = 0;
    private $has_schedules = array();
    private $has_courses = array();

    /*** Public Member Data ***/
    public $type_title  = '';

    public $base_student_count = 0;


    /*** Public Functions ***/
    public function __construct($id = NULL)
    {
        $this->set_valid_from(date('y-m-d H:i:s',time()));
        $this->set_valid_to(date('y-m-d H:i:s',time()));
        if(is_numeric($id))
        {
            $this->set_id($id);
        }

        $this->init();
    }

    public function set($data)
    {
        foreach($data AS $key=>$value)
        {
            if(property_exists($this,$key))
            {
                $this->{$key} = $value;
            }
        }

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
        $this->valid_from = strtotime($date) !== FALSE ? date('y-m-d H:i:s',strtotime($date)) : $this->valid_from;
		return $this;
    }

    public function set_valid_to($date)
    {
        $this->valid_to = strtotime($date) !== FALSE ? date('y-m-d H:i:s',strtotime($date)) : $this->valid_to;
		return $this;
    }

    public function set_publish($publish = 1)
    {
        $this->publish = is_numeric($publish) ? intval($publish) : $this->publish;
		return $this;
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

    public function get($autoload = true)
    {
        $data = $this->_sql_load_discount();

        if($autoload)
        {
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

    public function get_valid_from($formatted = false)
    {
        return ($formatted) ? date('d-m-Y',strtotime($this->valid_from)) : $this->valid_from;
    }

    public function get_valid_to($formatted = false)
    {
        return ($formatted) ? date('d-m-Y',strtotime($this->valid_to)) : $this->valid_to;
    }

    public function get_publish()
    {
        return $this->publish;
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

    public function get_for_contacts()
    {
        return $this->for_contacts;
    }

    public function get_for_contacts_details()
    {
        $contacts = array();
        if ($this->for_contacts) {
            $contacts = DB::select('id', DB::expr("CONCAT_WS(' ', contact.first_name, contact.last_name) as fullname"))
                ->from(array(Model_Contacts::TABLE_CONTACT, 'contact'))
                ->where('id', 'in', $this->for_contacts)
                ->execute()
                ->as_array();
        }
        return $contacts;
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

    public function get_code()
    {
        return $this->code;
    }

    public function get_instance()
    {
        return array('id'           => $this->id,
            'title'         => $this->title,
            'summary'       => $this->summary,
            'type'          => $this->type,
            'code'          => $this->code,
            'from'          => $this->from ? $this->from : null,
            'to'            => $this->to ? $this->to : null,
            'valid_from'    => $this->valid_from,
            'valid_to'      => $this->valid_to,
            'publish'       => $this->publish,
            'delete'        => $this->delete,
            'categories'    => $this->categories,
            'amount_type'   => $this->amount_type,
            'amount'        => $this->amount,
            'schedule_type' => $this->schedule_type,
            'item_quantity_min' => $this->item_quantity_min ? $this->item_quantity_min : null,
            'item_quantity_max' => $this->item_quantity_max ? $this->item_quantity_max : null,
            'item_quantity_scope' => $this->item_quantity_scope ? $this->item_quantity_scope : null,
            'min_students_in_family' => $this->min_students_in_family ? $this->min_students_in_family : null,
            'max_students_in_family' => $this->max_students_in_family ? $this->max_students_in_family : null,
            'usage_limit' => $this->usage_limit ? $this->usage_limit : null,
            'is_package' => $this->is_package ? $this->is_package : (count($this->has_schedules) | count($this->has_courses) > 0 ? 1 : 0)
        );
    }

    public function get_type_title()
    {
        return $this->type_title;
    }

    public function save()
    {
        $ok = TRUE;
        Database::instance()->begin();
        try{
            $this->validate();
            if(!is_numeric($this->id))
            {
                $this->_sql_insert_discount(true);
            }
            else
            {
                $this->_sql_update_discount();
            }
            $this->_sql_set_contacts();
            $this->_sql_set_schedules();
            $this->_sql_set_courses();
            Database::instance()->commit();
        }
        catch(Exception $e)
        {
            $ok = FALSE;
            Database::instance()->rollback();
            throw $e;
        }

        return $ok ? $this->id : false;
    }

    public function get_usage($params = array())
    {
        $q = DB::select(DB::expr("distinct hs.id as hs_id, b.id as booking_id, hs.schedule_id"))
                ->from(array('plugin_courses_bookings', 'b'))
                    ->join(array('plugin_courses_bookings_has_schedules', 'hs'), 'inner')
                        ->on('b.id', '=', 'hs.booking_id')
                    ->join(array('plugin_courses_bookings_has_discounts', 'd'), 'left')
                        ->on('hs.booking_id', '=', 'd.booking_id')
                        //->on(DB::expr('(hs.schedule_id = d.schedule_id or d.schedule_id is null)'))
                        ->on(DB::expr('(hs.schedule_id'), '=', DB::expr('d.schedule_id or d.schedule_id is null)')) // a workaround to generate (hs.schedule_id = d.schedule_id or d.schedule_id is null)
                    ->join(array('plugin_contacts_contact', 'c'), 'inner')
                        ->on('b.student_id', '=', 'c.id')
                ->where('b.status', 'in', array('Processing', 'Confirmed', 'Pending')) // confirmed, in progress
                ->and_where('b.deleted', '=', 0)
                ->and_where('hs.deleted', '=', 0);

        if (@$params['booking_id']) {
            $q->and_where('b.id', '=', $params['booking_id']);
        }

        if (@$params['contact_id']) {
            $q->and_where('b.contact_id', '=', $params['contact_id']);
        }

        if (@$params['family_of'] || @$params['family_id']) {
            $q->join(array('plugin_family_members', 'fm'), 'inner')->on('c.id', '=', 'fm.contact_id');
        }

        if (@$params['family_id']) {
            $q->and_where('fm.family_id', '=', $params['family_id']);
        }

        if (@$params['family_of']) {
            $family = Model_Families::get_family_of($params['family_of']);
            $q->and_where('fm.family_id', '=', $family['id']);
        }

        $q_used = clone $q;
        $q_used->and_where('d.discount_id', '=', $this->id);
        $used_by_schedules = $q_used->execute()->as_array();

        $q_all = clone $q;
        $all_schedules = $q_all->execute()->as_array();

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
    public function calculate_discount_no_check($client_id, $lines, $schedule_id)
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
            if ($line['id'] != $schedule_id) {
                continue;
            }

            if ($this->amount_type == 'Fixed') {
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

    public function calculate_discount($client_id, $lines, $schedule_id, $coupon_code = null)
    {
        $discount = 0;

        $total = 0;
        $quantity = 0;

        if ($this->for_contacts) {
            if (!in_array($client_id, $this->for_contacts)) {
                return false;
            }
        }

        $usage = null;
        if ($this->usage_limit != null) {
            $usage = $this->get_usage(); //used times in existing bookings
            $used_times = $usage['used']['quantity'];

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
            if (!$client_id) {
                return false;
            }

            $usage = $this->get_usage(array('family_of' => $client_id));
            $quantity += $usage['all']['quantity'];
        }

        foreach ($lines as $key => $line) {
            // do not increase quantity since its already added above
            if ($line['id'] != null && ($line['existing_booking'] == false && $usage != null)) {
                $total += $line['fee'];
                ++$quantity;
            }
        }

        $matched = true;

        if ($this->from != null) {
            $matched = ($this->from <= $total);
        }
        if (!$matched) {
            return false;
        }

        if ($this->to != null) {
            $matched = ($this->to >= $total);
        }
        if (!$matched) {
            return false;
        }

        if ($this->item_quantity_min != null) {
            $matched = ($this->item_quantity_min <= $quantity);
        }
        if (!$matched) {
            return false;
        }

        if ($this->item_quantity_max != null) {
            $matched = ($this->item_quantity_max >= $quantity);
        }
        if (!$matched) {
            return false;
        }

        if (class_exists('Model_Families')) {
            $family = Model_Families::get_family_of($client_id);
            $student_count = $this->base_student_count;
            if (isset($family['members']))
            foreach ($family['members'] as $fmember) {
                if (in_array($fmember['role'], array('Student', 'Mature'))) {
                    ++$student_count;
                }
            }
        }


        if ($this->min_students_in_family != null) {
            $matched = ($this->min_students_in_family <= $student_count);
        }
        if (!$matched) {
            return false;
        }

        if ($this->max_students_in_family != null) {
            $matched = ($this->max_students_in_family >= $student_count);
        }
        if (!$matched) {
            return false;
        }

        if ($this->code != '' && $this->code != $coupon_code) {
            $matched = false;
        }
        if (!$matched) {
            return false;
        }

        if ($this->is_package) {
            $matched = $this->test_matching_schedules($lines) && $this->test_matching_courses($lines);
        }

        if ($matched) {
            $matched = false;
            $linesdup = $lines;
            foreach ($lines as $key => $line) {

                if ($line['id'] != $schedule_id) {
                    continue;
                }
                if (($line['id'] == null && $coupon_code != '') ||
                    ($this->test_matching_categories($line['id']) &&
                    ($this->schedule_type == 'Prepay,PAYG' || ($this->schedule_type == 'Prepay' && $line['prepay']) || ($this->schedule_type == 'PAYG' && !$line['prepay'])))) {
                    $matched = true;
                    if ($this->amount_type == 'Fixed') {
                        $lines[$key]['discount'] = (float)$this->amount;
                        $discount += $lines[$key]['discount'];
                    } else if ($this->amount_type == 'Percent') {
                        $lines[$key]['discount'] = round($line['fee'] * ($this->amount / 100), 2);
                        $discount += $lines[$key]['discount'];
                    } else if ($this->amount_type == 'Quantity') {
                        $free_quantity_left = $this->amount - $usage['used']['quantity'];
                        foreach ($linesdup as $keyd => $lined) {
                            if ($free_quantity_left == 0){
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
                            $discount = $lines[$key]['discount'] = (float)$line['fee'];
                            break;
                        }
                    }
                }
            }
        }

        return $discount;
    }

    public function courses_discount()
    {
        //$cart = Session::instance()->get(Model_BookingsCart::CART_NAME);
    }

    /*** Private Functions ***/

    private function _sql_insert_discount($set_id = FALSE)
    {
        $q = DB::insert(self::DISCOUNTS_TABLE,array_keys($this->get_instance()))->values($this->get_instance())->execute();

        if($set_id)
        {
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
                if ($exists['deleted'] == 1){
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
                if ($exists['deleted'] == 1){
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
                if ($exists['deleted'] == 1){
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
        $q = DB::update(self::DISCOUNTS_TABLE)->set($this->get_instance())->where('id','=',$this->id)->execute();
        return count($q) > 0 ? TRUE : FALSE;
    }

    private function init()
    {
        $this->get(true);
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

        $q = DB::select('*')->from(self::DISCOUNTS_TABLE)->where('id','=',$this->id)->execute()->as_array();
        return count($q) > 0 ? $q[0] : $this->get_instance();
    }

    private function validate()
    {
        $this->set_valid_from($this->valid_from);
        $this->set_valid_to($this->valid_to);
    }

    private function test_matching_categories($cart_line_id)
    {
        if (!$cart_line_id) {
            return false;
        }
        if ($this->categories != '') {
            $categories = explode(',', $this->categories);
            $course_category = Model_Schedules::get_schedule_category($cart_line_id);
            if(in_array($course_category, $categories)){
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }

    }

    private function test_matching_schedules($lines)
    {
        if ($this->has_schedules) {
            foreach ($this->has_schedules as $schedule_id) {
                $matched = false;
                foreach ($lines as $line) {
                    if ($schedule_id == $line['id']) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    return false;
                }
            }
        }

        return true;
    }

    private function test_matching_courses($lines, $cached = true)
    {
        static $cache_schedules = array();

        if ($this->has_courses) {
            foreach ($this->has_courses as $course_id) {
                $matched = false;
                foreach ($lines as $line) {
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
                if (!$matched) {
                    return false;
                }
            }
        }

        return true;
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
            DB::expr("GROUP_CONCAT(schedules.name) AS `schedules`")
        )
            ->from(array(self::DISCOUNTS_TABLE,'t1'))
                ->join(array(self::HAS_SCHEDULES_TABLE, 'has_schedules'), 'LEFT')
                    ->on('t1.id', '=', 'has_schedules.discount_id')
                ->join(array('plugin_courses_schedules', 'schedules'), 'LEFT')
                    ->on('has_schedules.schedule_id', '=', 'schedules.id')
            ->where('t1.delete','=',0);
        if (isset($params['is_package'])) {
            $q->and_where('t1.is_package', '=', $params['is_package']);
        }
        $q->group_by('t1.id');
        $discounts = $q->execute()->as_array();
        return $discounts;
    }

    public static function toggle_publish($publish)
    {
        DB::query(Database::UPDATE, 'UPDATE plugin_courses_discounts SET publish = 1 - publish WHERE id = '.$publish)->execute();
    }

    public static function get_all_discounts()
    {
        return DB::select('id')
            ->from(self::DISCOUNTS_TABLE)
            ->where('delete','=',0)
            ->and_where('publish','=',1)
            ->and_where('valid_from','<=',date('Y-m-d H:i:s',time()))
            ->and_where(DB::expr("DATE_ADD(valid_to, INTERVAL 1 DAY)"),'>',date('Y-m-d H:i:s'))
            ->order_by('valid_from','ASC')
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
            ->where('delete','=',0);
        $query->and_where('valid_from', '<=', date::now());
        $query->and_where('valid_to', '>=', date::today());
        if (@$params['code']) {
            $query->and_where('code', '=', $params['code']);
        }
        if (@$params['is_coupon']) {
            $query->and_where('code', '<>', '');
            $query->and_where('code', 'is not', null);
        }
        if (@$params['term']) {
            $query->and_where('code', 'like', '%' . $params['term'] . '%');
        }

        $discounts = $query->execute()->as_array();
        return $discounts;
    }

    public static function validate_coupon($code)
    {
        $params = array('code' => $code);
        $discounts = self::search($params);
        return count($discounts) > 0 ? true : false;
    }
}
?>