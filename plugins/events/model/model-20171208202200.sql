/*
ts:2017-12-08 20:22:00
*/

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('events', 'events_warn_duplicate_order', 'Warn Duplicate Orders', '0', '0', '0', '0', '0', 'both', 'Warn Duplicate Orders', 'toggle_button', 'Events', '0', 'Model_Settings,on_or_off');
