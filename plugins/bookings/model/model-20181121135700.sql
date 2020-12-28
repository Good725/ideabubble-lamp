/*
ts:2018-11-21 13:57:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('bookings_interview_login_require', 'Bookings Interview Login Require', 'bookings', '1', '1', '1', '1', '1', 'Bookings Interview Login Require', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('bookings_interview_redirect_url', 'Bookings Interview Redirect Url', 'bookings', '/thanks.html', '/thanks.html', '/thanks.html', '/thanks.html', '/thanks.html', 'Bookings Interview Redirect Url', 'text', 'Bookings', '');

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `create_via_code`, `linked_plugin_name`)
  VALUES
  ('course-interview-admin', 'Course Interview Admin', 'EMAIL', '0', 'New Course Interview Application', 'Bookings', 'bookings');

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `create_via_code`, `linked_plugin_name`, `message`)
  VALUES
  ('course-interview-student', 'Course Interview Student', 'EMAIL', '0', 'Course Interview Application Received', 'Bookings', 'bookings', 'Your application has been received. Thanks');
