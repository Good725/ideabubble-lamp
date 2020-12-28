/*
ts:2020-08-06 11:13:00
*/

UPDATE `plugin_reports_reports`
  SET
    `action_event`='$.post(\r\n  \"/admin/bookings/ajax_rollcall_update\",\r\n  $(\"#report_table input, #report_table select\").serialize(),\r\n  function(response){\r\n    $(\"#generate_report\").click();alert(response);\r\n  }\r\n);\r\n',
    `custom_report_rules`='init();\nfunction init() {\n    $(\"#report_table tbody > tr\").each(function () {\n        var tr = this;\n        var bookingId = null;\n        var $timeslotStatuses = $(tr).find(\"[name*=timeslot_status]\");\n        var transactions = $(tr).find(\"var.transactions\");\n\n		$(tr).find(\"input.datetimepicker\").datetimepicker({\n			datepicker : false,\n			format: \'H:i\',\n			formatTime: \'H:i\',\n			step: 5\n		});\n    });\n}\n\n$(\'#report_table_wrapper\').find(\'.dataTables_paginate > a\').on(\"click\", function () {\n    init();\n});\n\r\n',
    `php_modifier`='',
    `sql`=''
  WHERE (`name` in ('Master Roll Call', 'My Roll Call', 'Roll Call Attendance', 'Roll Call Finance', 'Print Roll Call'));

UPDATE `plugin_reports_reports`
  SET `php_post_filter`='$data = Model_KES_Bookings::rollcall_report_helper($this->get_parameter(\'timeslot_id\'), array(\'attendance\' => true, \'finance\' => true));'
  WHERE (`name` in ('Master Roll Call'));

UPDATE `plugin_reports_reports`
  SET `php_post_filter`='$data = Model_KES_Bookings::rollcall_report_helper($this->get_parameter(\'timeslot_id\'), array(\'attendance\' => true));'
  WHERE (`name` in ('My Roll Call', 'Roll Call Attendance'));

UPDATE `plugin_reports_reports`
  SET `php_post_filter`='$data = Model_KES_Bookings::rollcall_report_helper($this->get_parameter(\'timeslot_id\'), array(\'finance\' => true));'
  WHERE (`name` in ('Roll Call Finance'));

UPDATE `plugin_reports_reports`
  SET `php_post_filter`='$data = Model_KES_Bookings::rollcall_report_helper($this->get_parameter(\'timeslot_id\'), array(\'print\' => true));'
  WHERE (`name` in ('Print Roll Call'));

UPDATE plugin_reports_parameters SET `value`=replace (`value`, '@logged_id_contact_id', '@logged_in_contact_id') WHERE `value` LIKE '%@logged_id_contact_id%';

UPDATE `plugin_reports_reports`
  SET `php_post_filter`='$data = Model_KES_Bookings::rollcall_report_helper($this->get_parameter(\'Timeslot\'), array(\'attendance\' => true));'
  WHERE (`name` in ('My Roll Call'));