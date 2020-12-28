<?php defined('SYSPATH') or die('No direct script access.');

class date extends Kohana_Date {

	/**
	 * Returns the difference between a time and now in a less "fuzzy" way.
	 * Displaying a fuzzy time instead of a date is usually faster to read and understand.
	 *
	 *     $span = Date::fuzzy_span(time() - 10); // "Today"
	 *     $span = Date::fuzzy_span(time() + 3600); // "Yesterday"
	 *
	 * A second parameter is available to manually set the "local" timestamp,
	 * however this parameter shouldn't be needed in normal usage and is only
	 * included for unit tests
	 *
	 * @param   integer  "remote" timestamp
	 * @param   integer  "local" timestamp, defaults to time()
	 * @return  string
	 */
	public static function less_fuzzy_span($timestamp, $local_timestamp = NULL)
	{

		$local_timestamp = ($local_timestamp === NULL) ? time() : (int) $local_timestamp;

		if ($timestamp <= $local_timestamp)
		{
			// This is in the past
			if (date('z', $local_timestamp) === date('z', $timestamp)) // Timestamp is today
			{
				$span = 'Today at ' . date('ga', $timestamp);
			}
			elseif (date('z', $local_timestamp) === (date('z', $timestamp) + 1)) // Timestamp was yesterday
			{
				$span = 'Yesterday at ' . date('ga', $timestamp);
			}
			elseif (date('W', $local_timestamp) === (date('W', $timestamp))) // Timestamp was this week
			{
				$span = date('l \a\t ga', $timestamp);
			}
			elseif (date('Y', $local_timestamp) === date('Y', $timestamp)) // Timestamp was this year
			{
				$span = date('jS M', $timestamp);
			}
			else // Timestamp was before this year
			{
				$span = date('jS M Y', $timestamp);
			}
		}
		else
		{
			// This in the future
			if (date('z', $local_timestamp) === date('z', $timestamp)) // Timestamp is today
			{
				$span = 'Today at ' . date('ga', $timestamp);
			}
			elseif (date('z', $local_timestamp) === (date('z', $timestamp) - 1)) // Timestamp is tomorrow
			{
				$span = 'Tomorrow at ' . date('ga', $timestamp);
			}
			elseif (date('W', $local_timestamp) === (date('W', $timestamp))) // Timestamp is this week
			{
				$span = date('l \a\t ga', $timestamp);
			}
			elseif (date('Y', $local_timestamp) === date('Y', $timestamp)) // Timestamp is this year
			{
				$span = date('jS M', $timestamp);
			}
			else // Timestamp is after this year
			{
				$span = date('jS M Y', $timestamp);
			}
		}

		return $span;

	}
	/**
		 * Returns time difference between two timestamps, in human readable format.
		 * If the second timestamp is not given, the current time will be used.
		 * Also consider using [Date::fuzzy_span] when displaying a span.
		 *
		 *     $span = Date::span(60, 182, 'minutes,seconds'); // array('minutes' => 2, 'seconds' => 2)
		 *     $span = Date::span(60, 182, 'minutes'); // 2
		 *
		 * @param   integer  timestamp to find the span of
		 * @param   integer  timestamp to use as the baseline
		 * @param   string   formatting string
		 * @return  string   when only a single output is requested
		 * @return  array    associative list of all outputs requested
		 */
	public static function span($remote, $local = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
		{
			// Normalize output
			$output = trim(strtolower( (string) $output));

			if ( ! $output)
			{
				// Invalid output
				return FALSE;
			}

			// Array with the output formats
			$output = preg_split('/[^a-z]+/', $output);

			// Convert the list of outputs to an associative array
			$output = array_combine($output, array_fill(0, count($output), 0));

			// Make the output values into keys
			extract(array_flip($output), EXTR_SKIP);

			if ($local === NULL)
			{
				// Calculate the span from the current time
				$local = time();
			}

			// Calculate timespan (seconds)
			$timespan = abs($remote - $local);

			if (isset($output['years']))
			{
				$timespan -= Date::YEAR * ($output['years'] = (int) floor($timespan / Date::YEAR));
			}

			if (isset($output['months']))
			{
				$timespan -= Date::MONTH * ($output['months'] = (int) floor($timespan / Date::MONTH));
			}

			if (isset($output['weeks']))
			{
				$timespan -= Date::WEEK * ($output['weeks'] = (int) floor($timespan / Date::WEEK));
			}

			if (isset($output['days']))
			{
				$timespan -= Date::DAY * ($output['days'] = (int) floor($timespan / Date::DAY));
			}

			if (isset($output['hours']))
			{
				$timespan -= Date::HOUR * ($output['hours'] = (int) floor($timespan / Date::HOUR));
			}

			if (isset($output['minutes']))
			{
				$timespan -= Date::MINUTE * ($output['minutes'] = (int) floor($timespan / Date::MINUTE));
			}

			// Seconds ago, 1
			if (isset($output['seconds']))
			{
				$output['seconds'] = $timespan;
			}

			if (count($output) === 1)
			{
				// Only a single output was requested, return it
				return array_pop($output);
			}

			// Return array
			return $output;
		}

    /**
     * Convert 2012-12-30 to 30-12-2012
     *
     * @static
     * @param $date Date string format: 2012-12-30
     * @return string date format: 30-12-2012
     */
    public static function ymd_to_dmy($date){
        if(!$date_formated = DateTime::createFromFormat('Y-m-d', $date)){
            if(!$date_formated = DateTime::createFromFormat('Y/m/d', $date)){
                return '';
            }
            return $date_formated->format('d-m-Y');
        }
        return $date_formated->format('d-m-Y');
    }

    /**
     * Convert 2012-12-30 12:11:10 to 30-12-2012
     *
     * @static
     * @param $date Date string format: 2012-12-30 12:11:10
     * @return string date format: 30-12-2012
     */
    public static function ymdh_to_dmy($date){
        if(!$date_formated = DateTime::createFromFormat('Y-m-d H:i:s', $date)){
            if(!$date_formated = DateTime::createFromFormat('Y/m/d H:i:s', $date)){
                return '';
            }
            return $date_formated->format('d-m-Y');
        }
        return $date_formated->format('d-m-Y');
    }

    /**
     * Convert 2012-12-30 12:11:10 to 30-12-2012 12:11:10
     *
     * @static
     * @param $date Date string format: 2012-12-30 12:11:10
     * @return string date format: 30-12-2012 12:11:10
     */
    public static function ymdh_to_dmyh($date){
        if(!$date_formated = DateTime::createFromFormat('Y-m-d H:i:s', $date)){
            if(!$date_formated = DateTime::createFromFormat('Y/m/d H:i:s', $date)){
                return '';
            }
            return $date_formated->format('d-m-Y H:i:s');
        }
        return $date_formated->format('d-m-Y H:i:s');
    }

    /**
     * Convert 30-12-2012 to 2012-12-30
     *
     * @static
     * @param $date Date string format: 30-12-2012
     * @return string date format: 2012-12-30
     */
    public static function dmy_to_ymd($date){
        if(!$date_formated = DateTime::createFromFormat('d-m-Y', $date)){
            if(!$date_formated = DateTime::createFromFormat('d/m/Y', $date)){
                return '';
            }
            return $date_formated->format('Y-m-d');
        }
        return $date_formated->format('Y-m-d');
    }

	public static function dmyh_to_ymdh($date){
		if(!$date_formated = DateTime::createFromFormat('d-m-Y H:i', $date)){
			if(!$date_formated = DateTime::createFromFormat('d/m/Y H:i', $date)){
				return '';
			}
			return $date_formated->format('Y-m-d H:i:s');
		}
		return $date_formated->format('Y-m-d H:i:s');
	}

	public static function now()
	{
		return date('Y-m-d H:i:s');
	}

	public static function today()
	{
		return date('Y-m-d');
	}

	public static function week($time = null)
	{
		if ($time == null) {
			$time = time();
		}

		return date("Y-m-d", strtotime('monday this week', $time));
	}

	public static function format($format, $dt)
	{
		if (!is_numeric($dt)) {
			$dt = strtotime($dt);
		}
		return date($format, $dt);
	}

	public static function same_day_of_month($orig_date, $date)
	{
		$stop_month = strtotime($date);

		$t = strtotime($orig_date);
		$year = date('Y', $t);
		$month = date('m', $t);
		$day = date('d', $t);
		for($i = 0 ;  ; ++$i) {
			$t = mktime(0, 0, 0, $month + $i, 1, $year);
			$monthdays = date("t", $t);
			$ndate = date("Y-m-", $t) . min($monthdays, $day);
			if (date("Y-m", $t) == date("Y-m", $stop_month)) {
				return $ndate;
			}
		}
		return false;
	}

	public static function dMonY_to_ymd($date)
	{
		$dt = date_parse_from_format('d/M/Y', $date);
		return $dt['year'] . '-' . $dt['month'] . '-' . $dt['day'] . ($dt['minute'] !== false ? ' ' . $dt['hour'] . ':' . $dt['minute'] : '');
	}

} // End url