/*
ts:2019-11-22 13:29:00
*/

UPDATE engine_settings SET `name`=CONCAT('AccountsIQ ', `name`) WHERE `group` = 'Accounts IQ';
UPDATE engine_settings SET `group`='Remote Accounting' WHERE `group` = 'Accounts IQ';

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_glcardaccountcode', 'AccountsIQ Card Payment Account Code', '', '', '',  '',  '',  'both', '', 'text', 'Remote Accounting', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_glcashaccountcode', 'AccountsIQ Cash Payment Account Code', '', '', '',  '',  '',  'both', '', 'text', 'Remote Accounting', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_glchequeaccountcode', 'AccountsIQ Cheque Payment Account Code', '', '', '',  '',  '',  'both', '', 'text', 'Remote Accounting', 0, '');

DELETE FROM engine_settings WHERE `variable` = 'accountsiq_biaccountcode';
