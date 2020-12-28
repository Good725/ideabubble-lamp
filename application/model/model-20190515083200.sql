/*
ts:2019-05-15 08:32:00
*/

INSERT IGNORE INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('host_application', 'Host Application', '0', '0', '0', '0', '0', 'Enable/Disable the Host Application Page', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');