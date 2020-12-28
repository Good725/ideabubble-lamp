/*
ts:2018-09-19 11:03:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'bigredcloud_key', 'Big Red Cloud API Key', '', '', '',  '',  '',  'both', '', 'text', 'Remote Accounting', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'bigredcloud_account_invoice', 'Big Red Cloud Account Invoice', '000', '000', '000',  '000',  '000',  'both', '', 'select', 'Remote Accounting', 0, 'Model_Bigredcloud,get_account_options');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'bigredcloud_account_payment', 'Big Red Cloud Account Payment', '', '', '',  '',  '',  'both', '', 'select', 'Remote Accounting', 0, 'Model_Bigredcloud,get_account_options');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'bigredcloud_account_bank_id', 'Big Red Cloud Bank Account Id', '', '', '',  '',  '',  'both', '', 'select', 'Remote Accounting', 0, 'Model_Bigredcloud,get_bank_account_options');

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('remoteaccounting_payonline_sync', 'Pay Online Form Auto Sync', 'remoteaccounting', '0', '0', '0', '0', '0', 'Pay Online Form Auto Sync', 'toggle_button', 'Remote Accounting', 'Model_Settings,on_or_off');
