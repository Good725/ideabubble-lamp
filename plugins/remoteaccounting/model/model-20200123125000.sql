/*
ts:2020-01-23 12:50:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_cc_invoice_to_customer_code', 'AccountsIQ Credit Card Customer Code', '', '', '',  '',  '',  'both', 'If this is set then it will not create new customers for credit card bookings. it will make bookings to this customer', 'text', 'Remote Accounting', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_invoice_departmentid', 'AccountsIQ Invoice Department Id', '', '', '',  '',  '',  'both', '', 'text', 'Remote Accounting', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('remoteaccounting', 'accountsiq_prefix', 'AccountsIQ Prefix', 'ITT', 'ITT', 'ITT',  'ITT',  'ITT',  'both', '', 'text', 'Remote Accounting', 0, '');
