/*
ts: 2019-09-20 17:51:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_companyiq', 'Company Id', '', '', '',  '',  '',  'both', '', 'text', 'Accounts IQ', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_partnerkey', 'Partner Key', '', '', '',  '',  '',  'both', '', 'text', 'Accounts IQ', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_userkey', 'User Key', '', '', '',  '',  '',  'both', '', 'text', 'Accounts IQ', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_glaccountcode', 'GL Account Code', '', '', '',  '',  '',  'both', '', 'text', 'Accounts IQ', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_biaccountcode', 'BI Account Code', '', '', '',  '',  '',  'both', '', 'text', 'Accounts IQ', 0, '');

INSERT INTO `engine_automations_actions_triggers` (`action`, `trigger`) VALUES ('Remote Accounting Save Contact', 'Contact Save');
INSERT INTO `engine_automations_actions_triggers` (`action`, `trigger`) VALUES ('Remote Accounting Delete Contact', 'Contact Delete');

