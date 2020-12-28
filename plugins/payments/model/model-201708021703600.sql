/*
ts:2017-08-02 17:36:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'allpoints_charge_url', 'Charge Url', 'https://www.allpointsmessaging.com/mno/api/chargetobill.htm', 'https://www.allpointsmessaging.com/mno/api/chargetobill.htm', 'https://www.allpointsmessaging.com/mno/api/chargetobill.htm',  'https://www.allpointsmessaging.com/mno/api/chargetobill.htm',  'https://www.allpointsmessaging.com/mno/api/chargetobill.htm',  'both', '', 'text', 'All Points Payments', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'allpoints_sms_url', 'Send SMS Url', 'https://www.allpointsmessaging.com/bulksms/sendsms/sendbulksms.htm', 'https://www.allpointsmessaging.com/bulksms/sendsms/sendbulksms.htm', 'https://www.allpointsmessaging.com/bulksms/sendsms/sendbulksms.htm',  'https://www.allpointsmessaging.com/bulksms/sendsms/sendbulksms.htm',  'https://www.allpointsmessaging.com/bulksms/sendsms/sendbulksms.htm',  'both', '', 'text', 'All Points Payments', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'allpoints_sms_senderid', 'SMS Sender ID', '', '', '',  '',  '',  'both', '', 'text', 'All Points Payments', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'allpoints_username', 'Username', '', '', '',  '',  '',  'both', '', 'text', 'All Points Payments', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'allpoints_sendsms_password', 'Send SMS Password', '', '', '',  '',  '',  'both', '', 'text', 'All Points Payments', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'allpoints_charge_password', 'Charge Password', '', '', '',  '',  '',  'both', '', 'text', 'All Points Payments', 0, '');


INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'allpoints_contentid', 'Content ID', '', '', '',  '',  '',  'both', '', 'text', 'All Points Payments', 0, '');

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'allpoints_charge_operators', 'Charge Enabled Operators', '', '', '',  '',  '',  'both', '', 'multiselect', 'All Points Payments', 0, 'Model_Allpoints,get_operators_options');

CREATE TABLE plugin_payments_allpoints_transactions
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  amount  DECIMAL(10, 2) NOT NULL,
  verification_code VARCHAR(100) NOT NULL,
  mobilenumber  VARCHAR(20) NOT NULL,
  description TEXT,
  reference TEXT,
  operator VARCHAR(10),
  status  ENUM('NEW', 'VERIFIED', 'COMPLETED', 'FAILED', 'ABANDONED'),
  created DATETIME,
  updated DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0,
  remote_tx_id  VARCHAR(100),
  verification_fails TINYINT NOT NULL DEFAULT 0,

  KEY (mobilenumber)
)
ENGINE = INNODB
CHARSET = UTF8;

