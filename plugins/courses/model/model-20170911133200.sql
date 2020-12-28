/*
ts:2018-09-11 13:32:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('courses', 'courses_enable_bookings', 'Enable Course Bookings', '1', '1', '1',  '1',  '1',  'both', '', 'toggle_button', 'Courses', 0, 'Model_Settings,on_or_off');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('courses', 'courses_enable_registrations', 'Enable Course Registrations', '1', '1', '1',  '1',  '1',  'both', '', 'toggle_button', 'Courses', 0, 'Model_Settings,on_or_off');
