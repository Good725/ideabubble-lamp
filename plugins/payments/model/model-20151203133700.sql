/*
ts:2015-12-03 13:37:00
*/

INSERT INTO `settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('boipa_enable', 'Enable BOI Payments', 0, 0, 0, 0, 0, 'both', '', 'toggle_button', 'Bank Of Ireland Payment', 0, 'Model_Settings,on_or_off');
INSERT INTO `settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('boipa_clientid', 'Client Id', '', '', '', '', '', 'both', '', 'text', 'Bank Of Ireland Payment', 0, '');
INSERT INTO `settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('boipa_password', 'Password', '', '', '', '', '', 'both', '', 'text', 'Bank Of Ireland Payment', 0, '');
INSERT INTO `settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('boipa_name', 'Name', '', '', '', '', '', 'both', '', 'text', 'Bank Of Ireland Payment', 0, '');
INSERT INTO `settings`
(`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
values
  ('boipa_apihost', 'API Host', '', 'https://testvpos.boipa.com:19445', 'https://testvpos.boipa.com:19445', 'https://testvpos.boipa.com:19445', 'https://testvpos.boipa.com:19445', 'both', '', 'text', 'Bank Of Ireland Payment', 0, '');
