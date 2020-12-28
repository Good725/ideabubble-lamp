/*
ts:2019-01-29 00:00:00
*/

ALTER TABLE plugin_courses_providers ADD COLUMN list_url VARCHAR(100);

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('bookings_checkout_login_require', 'Bookings Checkout Login Require', 'bookings', '1', '1', '1', '1', '1', 'Bookings Checkout Login Require', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');
