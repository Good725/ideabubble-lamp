/*
ts:2018-04-25 07:40:00
*/

INSERT IGNORE INTO `engine_plugins`
  (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUES
  ('remoteaccounting', 'Remote Accounting', '1', '0', NULL);

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'remoteaccounting_api', 'Remote Accounting API', '', '', '',  '',  '',  'both', '', 'select', 'Remote Accounting', 0, 'Model_Remoteaccounting,get_apis');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'xero_secret', 'Xero Secret', '', '', '',  '',  '',  'both', '', 'text', 'Remote Accounting', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'xero_key', 'Xero Key', '', '', '',  '',  '',  'both', '', 'text', 'Remote Accounting', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'xero_pkey', 'Xero Private Key', '', '', '',  '',  '',  'both', '', 'textarea', 'Remote Accounting', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'xero_account_invoice', 'Xero Account Invoice', '', '', '',  '',  '',  'both', '', 'select', 'Remote Accounting', 0, 'Model_Xero,get_account_options');

  INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('remoteaccounting', 'xero_account_payment', 'Xero Account Payment', '', '', '',  '',  '',  'both', '', 'select', 'Remote Accounting', 0, 'Model_Xero,get_account_options');

CREATE TABLE plugin_remoteaccounting_contacts
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  local_contact_id  INT,
  remote_contact_id VARCHAR(100),
  remote_api  VARCHAR(20),

  KEY (local_contact_id),
  KEY (remote_contact_id)
)
ENGINE=INNODB
CHARSET = UTF8;

CREATE TABLE plugin_remoteaccounting_transactions
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  local_transaction_id  INT,
  local_transaction_table VARCHAR(100),
  remote_transaction_id VARCHAR(100),
  remote_api  VARCHAR(20),

  KEY (local_transaction_id),
  KEY (remote_transaction_id)
)
ENGINE=INNODB
CHARSET = UTF8;

CREATE TABLE plugin_remoteaccounting_payments
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  local_payment_id  INT,
  local_payment_table VARCHAR(100),
  remote_payment_id VARCHAR(100),
  remote_api  VARCHAR(20),

  KEY (local_payment_id),
  KEY (remote_payment_id)
)
ENGINE=INNODB
CHARSET = UTF8;

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`, `autoload`, `action_button_label`, `action_button`, `action_event`) VALUES ('Remote Accounting Transactions', 'select \r\n		concat(\'Booking Transaction #\', tx.id) as Transaction,\r\n		CONCAT_WS(\' \', c.title, c.first_name, c.last_name) as Contact,\r\n		tx.total as Total,\r\n		rtx.remote_api as API,\r\n		rtx.remote_transaction_id As \'Remote Transaction\'\r\n	from plugin_bookings_transactions tx\r\n		inner join plugin_contacts3_contacts c on tx.contact_id = c.id\r\n		left join plugin_remoteaccounting_transactions rtx on tx.id = rtx.local_transaction_id and rtx.local_transaction_table = \'plugin_bookings_transactions\'', '1', '0', 'sql', '1', 'Sync Transactions', '1', '$.post(\r\n\"/admin/remoteaccounting/sync_transactions\",\r\n{\r\n},\r\nfunction (response){\r\n$(\'#generate_report\').click();\r\n}\r\n);');
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `report_type`, `autoload`, `action_button_label`, `action_button`, `action_event`) VALUES ('Remote Accounting Payments', 'select \r\n		concat(\'Booking Transaction #\', tx.id) as \'Transaction\',\r\n		payments.id as \'Payment\',\r\n		CONCAT_WS(\' \', c.title, c.first_name, c.last_name) as \'Contact\',\r\n		tx.total as \'Transaction Total\',\r\n		payments.amount as \'Payment Amount\',\r\n		rtx.remote_api as \'API\',\r\n		rpayments.remote_payment_id as \'Remote Payment\'\r\n	from plugin_bookings_transactions tx\r\n		inner join plugin_contacts3_contacts c on tx.contact_id = c.id\r\n		inner join plugin_bookings_transactions_payments payments on tx.id = payments.transaction_id\r\n		left join plugin_remoteaccounting_transactions rtx on tx.id = rtx.local_transaction_id and rtx.local_transaction_table = \'plugin_bookings_transactions\'\r\n		left join plugin_remoteaccounting_payments rpayments on payments.id = rpayments.local_payment_id and rpayments.local_payment_table = \'plugin_bookings_transactions_payments\';', '1', '0', 'sql', '1', 'Sync Payments', '1', '$.post(\r\n\"/admin/remoteaccounting/sync_payments\",\r\n{\r\n},\r\nfunction (response){\r\n$(\'#generate_report\').click();\r\n}\r\n);');
