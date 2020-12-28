/*
ts:2016-06-02 19:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `linked_plugin_name`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('messaging_override_recipients', 'messaging', 'Override Recipients', '0', '1', '1', '1', '', 'both', '', 'toggle_button', 'Message Settings', 0, 'Model_Settings,on_or_off');

INSERT INTO `engine_settings`
  (`variable`, `linked_plugin_name`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('messaging_override_recipients_email', 'messaging', 'Override Recipient Email', '', 'support@ideabubble.ie', 'support@ideabubble.ie', 'support@ideabubble.ie', 'support@ideabubble.ie', 'both', '', 'text', 'Message Settings', 0, '');

INSERT INTO `engine_settings`
  (`variable`, `linked_plugin_name`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('messaging_override_recipients_sms', 'messaging', 'Override Recipient SMS', '', '', '', '', '', 'both', '', 'text', 'Message Settings', 0, '');

UPDATE `engine_settings` SET `linked_plugin_name` = 'messaging' WHERE `variable` LIKE 'messaging_%';

