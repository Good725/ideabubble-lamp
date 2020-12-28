/*
ts:2018-06-28 10:01:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('bookings_checkout_cash_enabled', 'Checkout Cash Payment Enable', 'bookings', '0', '0', '0', '0', '0', 'Checkout Cash Payment Enable', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');
INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`)
  VALUES
  ('bookings_checkout_cash_ips', 'Checkout Cash Payment IPs', 'bookings', '', '', '', '', '', 'Checkout Cash Payment IPs(one ip per line)', 'textarea', 'Bookings');
