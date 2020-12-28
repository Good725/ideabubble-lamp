/*
ts:2016-08-24 11:42:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('engine_cant_login_mailto', "Can't login mail to", '', 'support@ideabubble.ie', 'support@ideabubble.ie',  'support@ideabubble.ie',  'support@ideabubble.ie',  'both', '', 'text', 'Engine', 0, '');

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('engine_enable_external_register', "Enable External Register", '', '0', '0',  '0',  '0',  'both', '', 'toggle_button', 'Engine', 0, 'Model_Settings,on_or_off');
