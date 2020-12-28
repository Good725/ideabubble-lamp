/*
ts:2016-03-25 15:01:00
*/

UPDATE `plugin_reports_reports`
  SET `action_button_label` = 'Fix Timeslot Trainers', `action_button` = '1', `action_event` = '$.post(\n	\"/admin/courses/fix_schedule_timeslots_without_trainers\",\n	function(){\n		window.location.reload();\n	}\n);\n'
  WHERE `name`='Timeslots with no trainer';
