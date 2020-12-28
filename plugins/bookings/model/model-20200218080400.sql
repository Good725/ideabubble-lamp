/*
ts:2020-02-18 08:04:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('bookings_require_primary_biller_organisation_booking', 'Require primary biller for organisation bookings', 'bookings', '0', '0', '0', '0', '0', 'Require primary biller for organisation bookings', 'toggle_button', 'Bookings', 'Model_Settings,on_or_off');
