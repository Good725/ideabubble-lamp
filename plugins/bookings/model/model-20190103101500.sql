/*
ts:2019-01-03 10:15:00
*/

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('student-attendance-edit-send-auth-code', 'SMS', '1', 'Student Attendance Edit Authorization', 'Hello $parentname, authorization code $code for $studentname', '1', 'Booking', '$parentname,$code,$studentname', 'bookings');
INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('bookings_student_attendance_auth_enabled', 'Student Attendance Edit Authorization Enable', 'bookings', '0', '0', '0', '0', '0', 'Student Attendance Edit Authorization Enable', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');
INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`)
  VALUES
  ('bookings_student_attendance_auth_timeout', 'Student Attendance Edit Authorization Timeout', 'bookings', '300', '300', '300', '300', '300', 'Student Attendance Edit Authorization Timeout Seconds', 'text', 'Bookings');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_attendance_edit', 'KES Contacts / Attendance Edit', 'KES Contacts / Attendance Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

DELETE FROM engine_settings where `variable` = 'bookings_student_attendance_auth_enabled';
DELETE FROM engine_settings where `variable` = 'bookings_student_attendance_auth_timeout';

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_attendance_edit_auth', 'KES Contacts / Attendance Authorized Edit', 'KES Contacts / Attendance Authorized Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));
