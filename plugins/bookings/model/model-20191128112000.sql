/*
ts:2019-11-28 11:20:00
*/


INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES (
  'bookings_display_booking_warning',
  'Display booking warning',
  'bookings',
  '1',
  '1',
  '1',
  '1',
  '1',
  'Display a warning when attempting to book a student to a schedule that does not match their criteria.',
  'toggle_button',
  'Bookings',
  'Model_Settings,on_or_off'
);
