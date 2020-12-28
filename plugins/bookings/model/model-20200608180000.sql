/*
ts:2020-06-08 18:00:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES ('show_cart_in_mobile_header', 'Show cart in mobile header', '1', '1', '1', '1', '1', 'both', 'Show a clickable cart icon in the header menu when viewing the site on mobile-sized screens', 'toggle_button', 'Bookings', 0, 'Model_Settings,on_or_off');

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `required`, `options`)
VALUES (
  'course_list_mode_mobile',
  'Mobile course-list mode',
  'bookings',
  'grid',
  'grid',
  'grid',
  'grid',
  'grid',
  'Display mode used for the course list on mobile-sized screens',
  'toggle_button',
  'Bookings',
  '0',
  '{"list":"List","grid":"Grid"}'
);