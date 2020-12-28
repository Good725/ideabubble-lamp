/*
ts:2016-08-29 15:10:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('checkout_signup_default', 'Checkout Signup Default', 'products', '0', '0', '0', '0', '0', 'At the checkout, tick the &quot;sign up to the newsletter&quot; box by default', 'toggle_button', 'Shop Checkout', 'Model_Settings,on_or_off');

INSERT IGNORE INTO `engine_feeds` (`name`, `summary`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
  'Course Booking Data',
  'Display data on the last booking made by the end user.',
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP(),
  CURRENT_TIMESTAMP(),
  '1',
  '0',
  'course_booking_data',
  'Model_CourseBookings,render_booking_data'
);