/*
ts:2017-05-10 20:51:00
*/

INSERT INTO engine_settings
  (`variable`, `name`, linked_plugin_name, value_live, value_stage, value_test, value_dev, `type`, `group`, options)
  VALUES
  ('imap_per_user', 'Imap Per User', 'messaging', 1, 1, 1, 1, 'toggle_button', 'Imap', 'Model_Settings,on_or_off');
