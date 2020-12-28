/*
ts:2020-08-13 15:40:00
*/

-- Setting for the ability to continue with a booking after being told it is a duplicate for the contact
INSERT INTO `engine_settings`
(`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`) VALUES
(
  'allow_duplicate_bookings_per_student',
  'Allow duplicate bookings per student',
  'bookings',
  '0',
  '0',
  '0',
  '0',
  '0',
  'both',
  'When making a duplicate booking for a student, a warning appears. When this setting is enabled, a &quot;continue anyway&quot; button appears in the warning.',
  'toggle_button',
  '0',
  'Bookings',
  '0',
  'Model_Settings,on_or_off'
);

