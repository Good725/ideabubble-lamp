/*
ts:2016-12-07 09:20:00
*/

UPDATE plugin_reports_widgets
	SET type = 2
	WHERE
		`name` IN (
			'Number Of Requests',
			'Value Of Requests',
			'Number Of Completed Requests',
			'Value Of Paid Requests',
			'Valid Requests Awaiting Decision',
			'Value Of Valid Awaiting Requests',
			'Weekly Number Of Requests',
			'Value Of Requests',
			'Number Of Completed Requests',
			'Value Of Paid Requests',
			'Number Of Awaiting Requests',
			'Value Of Awaiting Requests',
			'Number Of Requests',
			'Value Of Requests',
			'Number Of Completed Requests',
			'Value Of Paid Requests',
			'Number Of Awaiting Requests',
			'Value Of Awaiting Requests'
		);

UPDATE plugin_reports_widgets SET `name` = 'Number Of Requests' WHERE `name` = 'Number Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Requests' WHERE `name` = 'Value Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Approved' WHERE `name` = 'Number Of Completed Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Approved' WHERE `name` = 'Value Of Paid Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Received' WHERE `name` = 'Valid Requests Awaiting Decision';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Received' WHERE `name` = 'Value Of Valid Awaiting Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Requests' WHERE `name` = 'Weekly Number Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Requests' WHERE `name` = 'Value Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Approved' WHERE `name` = 'Number Of Completed Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Approved' WHERE `name` = 'Value Of Paid Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Received' WHERE `name` = 'Number Of Awaiting Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Received' WHERE `name` = 'Value Of Awaiting Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Requests' WHERE `name` = 'Number Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Requests' WHERE `name` = 'Value Of Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Approved' WHERE `name` = 'Number Of Completed Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Approved' WHERE `name` = 'Value Of Paid Requests';
UPDATE plugin_reports_widgets SET `name` = 'Number Of Received' WHERE `name` = 'Number Of Awaiting Requests';
UPDATE plugin_reports_widgets SET `name` = 'Value Of Received' WHERE `name` = 'Value Of Awaiting Requests';
