/*
ts:2020-08-26 12:44:00
*/

INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'timetable_conflict_detection',
  'Timetable conflict detection',
  'timetables',
  '1',
  '1',
  '1',
  '1',
  '1',
  'both',
  'Timetable conflict detection',
  'toggle_button',
  '0',
  'Timetables',
  '0',
  'Model_Settings,on_or_off'
);

