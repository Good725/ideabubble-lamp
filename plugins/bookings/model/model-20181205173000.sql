/*
ts:2018-12-05 17:30:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES (
  'booking_only_relevant_email_mandatory',
  'Only relevant email fields mandatory',
  'bookings',
  '0',
  '0',
  '0',
  '0',
  '0',
  'When <strong>on</strong>, if the user is a student or guardian, only the student email or guardian email field respectively will be mandatory at the checkout. When <strong>off</strong>, both email fields will be mandatory.',
  'toggle_button',
  'Bookings',
  'Model_Settings,on_or_off'
);

