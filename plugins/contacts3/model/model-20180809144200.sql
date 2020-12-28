/*
ts:2018-08-09 14:42:00
*/

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('contacts3', 'contacts3_emergency_notification', 'Emergency', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'both', 'Emergency Notification Default', 'select', 'Contacts Notification Defaults', '0', 'Model_Contacts3,default_notifications');

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('contacts3', 'contacts3_absentee_notification', 'Absentee', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'both', 'Absentee Notification Default', 'select', 'Contacts Notification Defaults', '0', 'Model_Contacts3,default_notifications');

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('contacts3', 'contacts3_account_notification', 'Account', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'both', 'Account Notification Default', 'select', 'Contacts Notification Defaults', '0', 'Model_Contacts3,default_notifications');
