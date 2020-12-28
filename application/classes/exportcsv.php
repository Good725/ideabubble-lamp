<?php defined('SYSPATH') or die('No direct script access.');

class ExportCsv {

	public static function mysql_field_array($query) {

		$field = mysql_num_fields($query);

		for ($i = 0; $i < $field; $i++)
		{

			$names[] = mysql_field_name($query, $i);

		}

		return $names;

	}


	/**
	 * Function used to Export specified Data from a Database Table or View to a CSV (<strong>C</strong>omma <strong>S</strong>eparated <strong>V</strong>alue) file.
	 *
	 * @param $response
	 * @param $sql - String, holding the SQL Query to be used to obtain the Data to be Exported to a CSV file.
	 * @param $filename - String, holding filename to be used for the Exported CSV file
	 * @param null $extension - String, used to specify the FILE_EXTENSION for the Exported CSV file.<br />
	 *                          <em>DEFAULT</em> value = <strong>NULL</strong> <br />
	 * 							If this value is omitted or set to NULL, a CSV will be used by default.
	 * @param bool $update_as_exported - Boolean <em>TRUE</em> / <em>FALSE</em> flag, used to specify whether to update the corresponding Database Record as EXPORTED or NOT.<br />
	 * 									<em>DEFAULT</em> value = <strong>FALSE</strong>
	 * 									If not specified, corresponding Database Record will NOT be updated.<br />
	 * 									<em><strong>Please NOTE:</strong></em> In order to have this functionality available, the following fields: <strong>transferred</strong> and <strong>date_transferred</strong>,
	 * 									will be required to be available in the corresponding Database Table.
	 * @param string $export_model - String holding the <em>NAME of THE MODEL</em> which Data will be Exported.<br />
	 * 									<em>PLEASE NOTE:</em> this field will be <strong>ONLY REQUIRED</strong>, when the <em>$update_as_exported</em> is set to <strong>TRUE</strong>.<br />
	 * 									The <em>MODEL_NAME</em> is required, in order to call the <em>MODEL_NAME</em><strong>->update()</strong> function,
	 * 									that will be used to update the corresponding Model->record data as "EXPORTED".<br />
	 * 									<em>EXAMPLE:</em> <strong>Model_Transaction</strong> as per the Kohana standards.<br />
	 * 									<em>DEFAULT</em> value = <strong>EMPTY_STRING</strong><br />
	 * 									If not specified, the <em>UPDATE</em> of the Exported data as "Exported" will not be applied,
	 * 									even when the <em>$update_as_exported</em> is set to <strong>TRUE</strong>.<br /><br />
	 *
	 * @param string $destination		folder to save the csv file;
	 *
	 * 									<strong>IMPORTANT!!!</strong><br />
	 * 									The EXPORT-Data update will ONLY be applied when:<br />
	 * 									<strong>1.</strong> <em>$update_as_exported</em> and <em>$export_model</em> are set AND<br />
	 * 									<strong>2.</strong> the <strong>exported record</strong> and its corresponding <strong>Database Table</strong> have the following fields:<br />
	 *   									<strong>2.1</strong> <em>date_updated</em> or Contacts Table holds: <em>lastmodifdate</em> instead of date_updated<br />
	 *   									<strong>2.2</strong> <em>date_transferred</em><br />
	 *   									<strong>2.3</strong> <em>transferred</em>
	 */
	public static function export_report($response, $sql, $filename, $extension = NULL, $update_as_exported = FALSE, $export_model = '', $destination = false) {

		if (!$extension) $extension = "csv";

		//set a timestamp for the exported file
		$file_export_timestamp = '_'.strftime("%Y%m%d%H%M%S");

		// Retrieve query fields from database with plain mysql function
		$db = Database::instance();
		$connection = $db->get_connection();
		$query = mysql_query($sql, $connection);
		$fields = self::mysql_field_array($query);

		// Output result
		$delimiter = ",";//was \t
		$header = "";

		//Set the Header Fields for the CSV File, extracted from the provided SQL String
		foreach ($fields as $field)
			$header .= $field . $delimiter;

		// Continue with "kohana style" data query - Get the specified Data to be EXPORTED
		$query = DB::query(Database::SELECT, $sql)
				->execute()
				->as_array();

		$data = "";
		if ($query)

			//Get an instance of the Model which used ot Manage the Data to be exported
			if($update_as_exported AND !empty($export_model)) $export_model_mgr = new $export_model();

			//Loop through the obtained data and generate the CSV file-content
			foreach ($query as $row)
			{
				/*
				 * Apply the EXPORT-Data update ONLY when both:
				 * - $update_as_exported and $export_model are set AND
				 * - the record view and its corresponding Database Table have the following fields:
				 *   1. date_updated - Contacts Table holds: lastmodifdate instead of date_updated
				 *   2. date_transferred
				 *   3. transferred
				 */
				if(
					isset($export_model_mgr) AND
					(
						(
							array_key_exists('lastmodifdate', $row) OR
							array_key_exists('date_updated', $row)
						) AND
						array_key_exists('date_transferred', $row) AND
						array_key_exists('transferred', $row)
					)
				){
					//Set the Corresponding Export related data before to update this Row
					$date_updated_key = ($export_model == 'Model_Contact')? 'lastmodifdate' : 'date_updated';
					$row[$date_updated_key] = date('Y-m-d H:i:s');
					$row['date_transferred'] = $row[$date_updated_key];
					$row['transferred'] = 1;

					//Update this Record using the specified Model
					switch($export_model){
						case 'Model_Contact': //Model_Contact->update() Function holds 2 parameters
							$export_model_mgr->update($row['id'], $row);
							break;
						default: //By Default theUpdate Function should have ONLY 1 parameter, i.e. the Item-Record data to be updated
							$export_model_mgr->update($row);
							break;
					}


				}//else Do Not update Record as Exported. Either Passed Export-Update required parameters were not correct, or the specified Export-Data does not have the REQUIRED EXPORT FIELDS

				$line = '';
				foreach ($row as $column)
				{

					if ($column == "") $column = $delimiter;
					else
					{
						$column = str_replace('"', '""', $column);
						//Encode commas in the Table fields, so they won't break th CSV columns
						if(strpos($column, ',', 0) !== FALSE) $column = str_replace(',', '&#44;', $column);
						$order = array("\r\n", "\n", "\r");
						$column = str_replace($order, '', $column);
						$column = $column . $delimiter;
					}
					$line .= $column;
				}
				$data .= trim($line) . "\n";
			}
		$data = str_replace("\r", "", $data);

		if ($data == "") $data = "\nno matching records found\n";
		$data = $header . "\n" . $data;

		if($destination){
			return $data;
		} else {
			self::sendResponse($response, $filename, $file_export_timestamp, $extension, $data);
		}
	}

	public static function export_report_data_array($response, $data_array, $filename, $extension = null) {

		if (!$extension) $extension = "csv";

		// Output result
		$delimiter = "\t";//was \t
		$header = "";
		if(count($data_array) > 0){
			foreach ($data_array[0] as $field => $data)
				$header .= $field . $delimiter;
		}
		// Continue with "kohana style" data query

		$data = "";
		if ($data_array)
			foreach ($data_array as $row)
			{

				$line = '';
				foreach ($row as $column)
				{

					if ($column == "") $column = $delimiter;
					else
					{
						$column = str_replace('"', '""', $column);
						$order = array("\r\n", "\n", "\r");
						$column = str_replace($order, '', $column);
						$column = $column . $delimiter;
					}
					$line .= $column;
				}
				$data .= trim($line) . "\n";
			}
		$data = str_replace("\r", "", $data);

		if ($data == "") $data = "\nno matching records found\n";
		$data = $header . "\n" . $data;

		self::sendResponse($response, $filename, '', $extension, $data);
	}

	public static function sendResponse($response, $filename, $timestamp, $extension, $data){
		$response->headers('Content-type', 'application/octet-stream');
		$response->headers('Content-Disposition', "attachment; filename=\"" . $filename.$timestamp . "." . $extension . "\"");
		$response->headers('Content-Encoding: UTF-8');
		$response->headers('Content-type: text/csv; charset=UTF-8');
		$response->headers('Pragma', "no-cache");
		$response->headers('Expires', "0");
        $data = chr(255).chr(254).mb_convert_encoding($data, 'UTF-16LE', 'UTF-8');
		$response->body($data);
		$response->send_file(TRUE, $filename.$timestamp.'.'.$extension);
	}

}


