/*
ts:2016-03-05 16:40:00
*/

INSERT INTO `settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('messaging_ip_same_msg_limit_seconds', 'Same Message / IP Limit Seconds', '60', '60', '60', '60', '60', 'both', '', 'text', 'Message Settings', 0, '');
INSERT INTO `settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('messaging_ip_same_msg_limit_number', 'Same Message / IP Limit Number', '5', '5', '5', '5', '5', 'both', '', 'text', 'Message Settings', 0, '');

INSERT INTO `settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('messaging_ip_all_msg_limit_seconds', 'All Message / IP Limit Seconds', '60', '60', '60', '60', '60', 'both', '', 'text', 'Message Settings', 0, '');
INSERT INTO `settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('messaging_ip_all_msg_limit_number', 'All Message / IP Limit Number', '5', '5', '5', '5', '5', 'both', '', 'text', 'Message Settings', 0, '');
