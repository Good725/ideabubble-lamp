/*
ts:2018-04-04 20:00:00
*/

CREATE TABLE plugin_bookings_student_auth_codes
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  student_id  INT NOT NULL,
  code  VARCHAR(100) NOT NULL,
  expires DATETIME,
  status  ENUM('Wait', 'Expired', 'Validated') NOT NULL DEFAULT 'Wait',
  created DATETIME,
  updated DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY(student_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO `plugin_messaging_notification_templates` (`name`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('student-checkout-send-auth-code', 'SMS', '1', 'Student Booking Authorization', 'Hello $parentname, authorization code $code for $studentname, $amount', '1', 'Booking', '$parentname,$code,$studentname,$amount', 'bookings');
INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES ('bookings_student_auth_enabled', 'Student Authorization Enable', 'bookings', '0', '0', '0', '0', '0', 'Student Booking Authorization Enable', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');
INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`) VALUES ('bookings_student_auth_timeout', 'Student Authorization Timeout', 'bookings', '300', '300', '300', '300', '300', 'Student Booking Authorization Timeout Seconds', 'text', 'Bookings');
