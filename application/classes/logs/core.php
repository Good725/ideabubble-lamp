<?php defined('SYSPATH') or die('No direct script access.');


class Logs_Core
{
	/**
	 * @var   string   The regex to pull a date out of the log file
	 */
	public static $date_regex = "/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/";

	

	/**
	 * Parses the content of a log file.
	 *
	 * @param   string   The log file path
	 * @return  array     Parsed data
	 */
	public static function parse($file)          
	{
            $lines = file($file);

            $file_tmp = '';
            for($i=2;$i<count($lines);$i++) {
                $file_tmp .= $lines[$i];
            }

		$delimiter = "***";

		// Get the log file and remove the first 2 lines
		$log = str_replace(Kohana::FILE_SECURITY." ?>".PHP_EOL.PHP_EOL, "", $file_tmp);
		$log = preg_replace(self::$date_regex, "$delimiter\\0", $log);
		$log = preg_split('/'.preg_quote($delimiter).'/', ltrim($log, $delimiter));

		$parsed = array();

		for ($i = 0, $len = count($log); $i < $len; $i += 1)
		{
			$row = $log[$i];

                        $data = self::split_entry($row);

                        // And check for an error (which will have a stack trace)
                        if ($data['type'] === 'error')
                        {
                                // Grab the next element in the array which contains the stack trace
                                $i += 1;
								
								if(isset($log[$i])){
									$strace = self::split_entry($log[$i]);
	
									//list($row, $trace) = explode("--", $strace['log']);
									
									$tmp_tab = explode("--", $strace['log']);
									if(isset($tmp_tab[1])) $trace = $tmp_tab[1]; else $trace = '';
	  
									//$data['stacktrace'] = explode(PHP_EOL, rtrim($trace, PHP_EOL));
								} else {
									$trace = '';
								}
                                $data['stacktrace'] = $trace;
                        }

                        $last_type = $data['type'];

                        // And set the message
                        $data['message'] = $data['log'];

                        $parsed[] = $data;
		}

                return $parsed;

	}

	/**
	 * Takes a log entry and splits it into a manageable array.
	 *
	 * @param   string    The log entry
	 * @return  array     Assoc array of (date =>, type =>, log => )
	 */
	public static function split_entry($row)
	{
		// Get the date
		preg_match(self::$date_regex, $row, $matches);
                    $data = array(
                            'date' => $matches[0],
                    );


		// Trim off the date and ---
		$row = str_replace($data['date']." --- ", "", $row);

		// Now get the type
		preg_match("/^\w+/", $row, $matches);
		$data['type'] = strtolower($matches[0]);
		$data['log'] = $row;

		return $data;
	}

}