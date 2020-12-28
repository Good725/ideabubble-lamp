<?php defined('SYSPATH') or die('No direct script access.');

class Model_Calendar_Event extends ORM {
    protected $_table_name = 'engine_calendar_events';

    protected $_belongs_to = array(
        'type' => array('model' => 'Calendar_Type'),
        'rule' => array('model' => 'Calendar_Rule')
    );

    public static function getEventList($plugin = null, $date_start = null, $date_end = null)
    {
        $select = DB::select(
            "ce.id",
            array(DB::expr("CONCAT_WS(' ', " . ($plugin ? "" : "cr.plugin_name, ") . "ce.title, '(between', CAST(ce.start_date AS DATE), 'and', CAST(ce.end_date AS DATE), ct.title, cr.title, ')')"), 'event')
        )
            ->from(array('engine_calendar_events', 'ce'))
                ->join(array('engine_calendar_rules', 'cr'), 'inner')
                    ->on('ce.rule_id', '=', 'cr.id')
                ->join(array('engine_calendar_types', 'ct'))
                    ->on('ce.type_id', '=', 'ct.id')
            ->where('ce.deleted', '=', 0)
            ->and_where('cr.deleted', '=', 0)
            ->and_where('ct.deleted', '=', 0);
        if ($plugin) {
            $select->and_where('cr.plugin_name', '=', $plugin);
        }
        if ($date_start) {
            $select->and_where('ce.start_date', '>=', $date_start);
        }
        if ($date_end) {
            $select->and_where('ce.start_date', '<=', $date_end);
        }
        $events = $select->execute()->as_array();

        return $events;
    }

    public static function search($plugin = null, $date_start = null, $date_end = null, $types = [])
    {
        $select = DB::select(
            "ce.*",
            array("cr.title", "rule"),
            array("ct.title", "type")
        )
            ->from(array('engine_calendar_events', 'ce'))
                ->join(array('engine_calendar_rules', 'cr'), 'left')->on('ce.rule_id', '=', 'cr.id')
                ->join(array('engine_calendar_types', 'ct'), 'left')->on('ce.type_id', '=', 'ct.id')
            ->where('ce.deleted', '=', 0);
        if ($plugin) {
            $select->and_where('cr.plugin_name', '=', $plugin);
        }
        if ($date_start) {
            $select->and_where('ce.start_date', '>=', $date_start);
        }
        if ($date_end) {
            $select->and_where('ce.start_date', '<=', $date_end);
        }

        if (!empty($types)) {
            $select->and_where('ct.title', 'in', $types);
        }

        $events = $select->execute()->as_array();

        return $events;
    }

    public static function get_all_published_dates($plugin = NULL, $eventIds = null)
    {
        $events = NULL;
        $dates = array();
        $step = '+1 day';
        $output_format = 'Y-m-d H:i:s';
        $select = null;
        if ( ! is_null($plugin))
        {
            $rules = DB::select('id')->from('engine_calendar_rules')->where('plugin_name','=',$plugin)->where('publish','=',1)->execute()->as_array();
            $check_rule = array();
            foreach($rules as $key=>$rule)
            {
                $check_rule[]=$rule;
            }
            if ($rules)
            {
                $select = ORM::factory('Calendar_Event')->where('publish', '=', 1)->where('deleted', '=', 0)->where('rule_id', 'IN', $check_rule);
            }
        }
        else
        {
            $select = ORM::factory('Calendar_Event')->where('publish','=',1)->where('deleted','=',0);
        }
        if ($select != null) {
            if ($eventIds !== null) {
                $select->and_where('id', 'in', $eventIds);
            }
            $events = $select->find_all()->as_array();
        }

        if ( ! is_null($events))
        {
            foreach ($events as $event)
            {
                $date = array();
                foreach ($event->_object as $key => $value)
                {
                    $date[$key] = $value;
                }
                $current = strtotime($date['start_date']);
                $last = strtotime($date['end_date']);

				// Get all dates in range
                while ($current <= $last)
                {
					$new_date['start_date'] = date($output_format,$current);
					$new_date['end_date']   = date($output_format, $last < strtotime('-1 day', $current) ? $last : $current);
					$new_date['date']       = $new_date['start_date'];
					$new_date['type']       = $event->type->title;
					$new_date['title']      = $event->title;
					$new_date['color']      = $event->type->color;

                    $dates[] = $new_date;
                    $current = strtotime($step, $current);
                }
            }
        }
        return $dates;
    }

    private static function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }
}