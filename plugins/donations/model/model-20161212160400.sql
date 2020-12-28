/*
ts:2016-12-12 16:04:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('donations', 'donations_warn_color', 'Warning Color', '#ffff00', '#ffff00', '#ffff00',  '#ffff00',  '#ffff00',  '#ffff00', '', 'color_picker', 'Donations', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('donations', 'donations_alarm_color', 'Alarm Color', '#ff0000', '#ff0000', '#ff0000',  '#ff0000',  '#ff0000',  '#ff0000', '', 'color_picker', 'Donations', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('donations', 'donations_warn_count', 'Number of Requests Warning Limit', '3', '3', '3',  '3',  '3',  '3', '', 'text', 'Donations', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('donations', 'donations_warn_paid', 'Value of Paid Warning Limit', '150', '150', '150',  '150',  '150',  '150', '', 'text', 'Donations', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('donations', 'donations_alarm_count', 'Number of Requests Alarm Limit', '5', '5', '5',  '5',  '5',  '5', '', 'text', 'Donations', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('donations', 'donations_alarm_paid', 'Value of Paid Alarm Limit', '300', '300', '300',  '300',  '300',  '300', '', 'text', 'Donations', 0, '');

