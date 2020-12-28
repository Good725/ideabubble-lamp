/*
ts:2017-03-10 18:45:00
*/

INSERT INTO `engine_settings`
  (`variable`, `linked_plugin_name`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('messaging_popout_menu', 'messaging', 'Popout Menu', '0', '0', '0', '0', '0', 'both', 'Enable an expandable menu for sending and viewing messages from any area in the back end.', 'toggle_button', 'Message Settings', 0, 'Model_Settings,on_or_off');
