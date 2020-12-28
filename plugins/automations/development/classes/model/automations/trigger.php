<?php
class Model_Automations_Trigger
{
    const INITIATOR_USER = 'USER';
    const INITIATOR_CRON = 'CRON';
    const INITIATOR_DB = 'DB';

    public $name = null;
    public $params = array();
    public $filters = array();
    public $purpose = null;
    public $generated_message_params = array();
    public $initiator = self::INITIATOR_USER;
    public $filter_select = null;
    public $repeat_fields = array();
    public $multiple_results = true;
    public $is_digest = false;
    public $no_duplicate_email_tag = array();

    public function __construct()
    {

    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_params()
    {
        return $this->params;
    }

    public function get_filters()
    {
        return $this->filters;
    }

    public function get_purpose()
    {
        return $this->purpose;
    }

    public function get_generated_message_params()
    {
        return $this->generated_message_params;
    }

    public function get_initiator()
    {
        return $this->initiator;
    }

    public function get_filter_select()
    {
        return $this->filter_select;
    }

    public function get_repeat_fields()
    {
        return $this->repeat_fields;
    }

    public function filter($data, $sequence)
    {
        return array($data);
    }


    public static function build_select($parameters)
    {
        $select = DB::select();
        if (isset($parameters['booking_id'])) {
            $select->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'));
        } else {

        }

    }

    public static function filter_date_interval_helper($select, $field, $execute, $operator, $interval, $amount)
    {
        $now = Model_Automations::$now;
        $range_start = $now;
        $range_end = $now;
        $interval = strtolower($interval);
        if (strpos($interval, 'min') !== false) {
            $interval = 'minute';
        }
        if ($operator == '>') {
            /*
             range after cron run. cron run: 2020-06-10 00:00; schedule starts 5 days after: 2020-06-15
             schedules.start_date <= 2020-06-15 and schedules.start_date < now
            */
            $range_end = strtotime('+' . $amount . $interval, Model_Automations::$now);
            if ($interval == 'minute') {
                $range_end = date('Y-m-d H:i:59', $range_end);
                $range_start = date('Y-m-d H:i:00', $range_start);
            } else if ($interval == 'hour') {
                $range_end = date('Y-m-d H:59:59', $range_end);
                $range_start = date('Y-m-d H:00:00', $range_start);
            } else if ($interval == 'day') {
                $range_end = date('Y-m-d 23:59:59', $range_end);
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else if ($interval == 'week') {
                $range_end = date('Y-m-d 23:59:59', strtotime("+1week", $range_start));
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else if ($interval == 'month') {
                $range_end = date('Y-m-d 23:59:59', strtotime("+1month", $range_start));
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else {
                throw new Exception('Unknown interval: (' . $interval . ')');
            }
        } else if ($operator == '>=') {
            /*
             on nth before cron run. cron run: 2020-06-10 00:00; schedule starts 5 days before: 2020-06-05
             schedules.start_date >= 2020-06-05 and schedules.start_date <= 2020-06-06 23:59:59
            */
            $range_start = strtotime('+' . $amount . $interval, Model_Automations::$now);
            if ($interval == 'minute') {
                $range_end = date('Y-m-d H:i:59', $range_start);
                $range_start = date('Y-m-d H:i:00', $range_start);
            } else if ($interval == 'hour') {
                $range_end = date('Y-m-d H:59:59', $range_start);
                $range_start = date('Y-m-d H:00:00', $range_start);
            } else if ($interval == 'day') {
                $range_end = date('Y-m-d 23:59:59', $range_start);
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else if ($interval == 'week') {
                $range_end = date('Y-m-d 23:59:59', strtotime("+1week", $range_start));
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else if ($interval == 'month') {
                $range_end = date('Y-m-d 23:59:59', strtotime("+1month", $range_start));
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else {
                throw new Exception('Unknown interval: (' . $interval . ')');
            }
        } else if ($operator == '<') {
            /*
             range after cron run. cron run: 2020-06-10 00:00; schedule starts 5 days after: 2020-06-15
             schedules.start_date >= 2020-06-10 and schedules.start_date < 2020-06-15
            */
            $range_start = strtotime('-' . $amount . $interval, Model_Automations::$now);
            if ($interval == 'minute') {
                $range_start = date('Y-m-d H:i:00', $range_start);
                $range_end = date('Y-m-d H:i:59', $range_end);
            } else if ($interval == 'hour') {
                $range_start = date('Y-m-d H:00:00', $range_start);
                $range_end = date('Y-m-d H:59:59', $range_end);
            } else if ($interval == 'day') {
                $range_start = date('Y-m-d 00:00:00', $range_start);
                $range_end = date('Y-m-d 23:59:59', $range_end);
            } else if ($interval == 'week') {
                $range_start = date('Y-m-d 00:00:00', $range_start);
                $range_end = date('Y-m-d 23:59:59', strtotime("-1week", $range_end));
            } else if ($interval == 'month') {
                $range_start = date('Y-m-d 00:00:00', $range_start);
                $range_end = date('Y-m-d 23:59:59', strtotime("-1month", $range_end));
            } else {
                throw new Exception('Unknown interval: (' . $interval . ')');
            }
        } else if ($operator == '<=') {
            /*
             on nth after cron run. cron run: 2020-06-10 00:00; schedule starts 5 days after: 2020-06-15
             schedules.start_date >= 2020-06-15 and schedules.start_date < 2020-06-15 23:59:59
            */

            $range_start = strtotime('-' . $amount . $interval, Model_Automations::$now);
            if ($interval == 'minute') {
                $range_end = date('Y-m-d H:i:59', $range_start);
                $range_start = date('Y-m-d H:i:00', $range_start);
            } else if ($interval == 'hour') {
                $range_end = date('Y-m-d H:59:59', $range_start);
                $range_start = date('Y-m-d H:00:00', $range_start);
            } else if ($interval == 'day') {
                $range_end = date('Y-m-d 23:59:59', $range_start);
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else if ($interval == 'week') {
                $range_end = date('Y-m-d 23:59:59', strtotime("-1week", $range_start));
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else if ($interval == 'month') {
                $range_end = date('Y-m-d 23:59:59', strtotime("-1month", $range_start));
                $range_start = date('Y-m-d 00:00:00', $range_start);
            } else {
                throw new Exception('Unknown interval: (' . $interval . ')');
            }
        } else {
            throw new Exception('Unknown operator: (' . $operator . ')');
        }

        $select->and_where($field, '>=', $range_start);
        $select->and_where($field, '<=', $range_end);
    }

    public static function filter_date_interval_helper0($select, $field, $execute, $operator, $interval, $amount)
    {
        if ($execute == 'before') {
            $now = strtotime('+' . $amount . $interval, Model_Automations::$now);
        } else if ($execute == 'after') {
            $now = strtotime('-' . $amount . $interval, Model_Automations::$now);
        } else {
            throw new Exception('Unknown execute: (' . $execute . ')');
        }
        $range_start = $now;
        $range_end = $now;
        if ($interval == 'minute') {
            $range_start = date('Y-m-d H:i:00', $range_start);
            $range_end = date('Y-m-d H:i:59', $range_end);
        } else if ($interval == 'hour') {
            $range_start = date('Y-m-d H:00:00', $range_start);
            $range_end = date('Y-m-d H:59:59', $range_end);
        } else if ($interval == 'day') {
            $range_start = date('Y-m-d 00:00:00', $range_start);
            $range_end = date('Y-m-d 23:59:59', $range_end);
        } else if ($interval == 'week') {
            $range_start = date('Y-m-d 00:00:00', $range_start);
            $range_end = date('Y-m-d 23:59:59', strtotime("+1week", $now));
        } else if ($interval == 'month') {
            $range_start = date('Y-m-d 00:00:00', $range_start);
            $range_end = date('Y-m-d 23:59:59', strtotime("+1month", $now));
        } else {
            throw new Exception('Unknown interval: (' . $interval . ')');
        }

        if ($operator == '=') {
            $select->and_where($field, '>=', $range_start);
            $select->and_where($field, '<=', $range_end);
        } else if ($operator == '<=') {
            if ($execute == 'before') {
                $select->and_where($field, '<=', $range_end);
            } else {
                $select->and_where($field, '<=', $range_end);
                $select->and_where($field, '>=', date('Y-m-d H:i:s', Model_Automations::$now));
            }
        } else if ($operator == '>=') {
            if ($execute == 'before') {
                $select->and_where($field, '>=', $range_start);
                $select->and_where($field, '<=', date('Y-m-d H:i:s', Model_Automations::$now));
            } else {
                $select->and_where($field, '>=', $range_start);
            }
        } else {
            throw new Exception('Unknown operator: (' . $operator . ')');
        }
    }
}