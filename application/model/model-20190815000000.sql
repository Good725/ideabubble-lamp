/*
ts: 2019-08-15 00:00:00
*/

ALTER TABLE engine_users ADD COLUMN two_step_auth ENUM('None', 'Email', 'SMS');
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`) VALUES ('two-step-auth-email', 'Two Factor authentication Email', 'EMAIL', '0', 'Authentication Code', 'Your authentication code is $code', 'login', '$code');
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`) VALUES ('two-step-auth-sms', 'Two Factor authentication SMS', 'SMS', '0', 'Authentication Code', 'Your authentication code is $code', 'login', '$code');

CREATE TABLE engine_login_auth_codes
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  code VARCHAR(20) NOT NULL,
  created DATETIME,
  expires DATETIME NOT NULL,
  valid TINYINT NOT NULL DEFAULT 1,

  KEY (user_id)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`, `expose_to_api`)
  VALUES
  ('engine_two_step_auth_timeout_seconds', 'Two Step Authentication Timeout', null, '300', '300', '300', '300', '300', 'Two Step Authentication Timeout Seconds', 'text', 'Engine', '', 1);
