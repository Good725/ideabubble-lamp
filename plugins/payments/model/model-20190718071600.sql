/*
ts:2019-07-18 07:16:00
*/

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'payments_store_card', 'Enable Storing Cards', '0', '0', '0', '0', '0', 'both', 'Use remote card storage', 'toggle_button', 'Payments', '0', 'Model_Settings,on_or_off');

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'payments_recurring_payments', 'Enable Recurring Payments', '0', '0', '0', '0', '0', 'both', 'Use stored cards for payment plans', 'toggle_button', 'Payments', '0', 'Model_Settings,on_or_off');

INSERT IGNORE INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'payments_recurring_payments_max_attempt', 'Recurring Payments Max Failed Attempts', '3', '3', '3', '3', '3', 'both', 'If a recurring payment failes it wont try to charge again', 'text', 'Payments', '0', 'Model_Settings,on_or_off');

UPDATE `engine_settings` SET `type`='text', `options`= '' WHERE (`variable`='payments_recurring_payments_max_attempt');

CREATE TABLE plugin_contacts3_has_paymentgw
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  contact_id  INT NOT NULL,
  paymentgw VARCHAR(100) NOT NULL,
  customer_id VARCHAR(100) NOT NULL,
  created_by  INT,
  created DATETIME,
  updated_by  INT,
  updated DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (contact_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_contacts3_has_paymentgw_has_card
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  has_paymentgw_id  INT NOT NULL,
  card_id VARCHAR(100) NOT NULL,
  last_4  VARCHAR(10),
  created_by  INT,
  created DATETIME,
  updated_by  INT,
  updated DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (has_paymentgw_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_ib_educate_bookings_has_card
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  card_id INT NOT NULL,
  recurring_payments_enabled  TINYINT NOT NULL DEFAULT 0,
  UNIQUE KEY (booking_id, card_id)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO `engine_cron_tasks`
  (`title`, `frequency`, `plugin_id`, `publish`, `action`)
  VALUES
  ('Bookings Recurring Auto Payments', '{\"minute\":[\"0\"],\"hour\":[\"0\"],\"day_of_month\":[\"*\"],\"month\":[\"*\"],\"day_of_week\":[\"*\"]}', (select id from engine_plugins where `name` = 'bookings'), '0', 'cron_autopayments');

ALTER TABLE plugin_bookings_transactions_payment_plans_has_payment ADD COLUMN failed_auto_payment_attempts TINYINT NOT NULL DEFAULT 0;

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('recurring-payment-failed-customer', 'Recurring Payment Failed', 'EMAIL', '0', 'Payment has failed', 'An automatic attempt to charge has been failed.<br />Transaction Id:$transaction_id<br />Amount: $amount<br />Name:$name', '0', 'payments', '$transaction_id,$amount,$name,$booking_id', 'bookings');

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('recurring-payment-succeded-customer', 'Recurring Payment Succeded', 'EMAIL', '0', 'Payment has succeded', 'Payment Successful.<br />Transaction Id:$transaction_id<br />Amount: $amount<br />Name:$name', '0', 'payments', '$transaction_id,$amount,$name,$booking_id', 'bookings');

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('recurring-payment-failed-admin', 'Recurring Payment Failed', 'EMAIL', '0', 'Payment has failed', 'An automatic attempt to charge has been failed.<br />Transaction Id:$transaction_id<br />Amount: $amount<br />Name:$name', '0', 'payments', '$transaction_id,$amount,$name,$booking_id,$error', 'bookings');

INSERT INTO `plugin_messaging_notification_templates`
  (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `created_by`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`)
  VALUES
  ('recurring-payment-succeded-admin', 'Recurring Payment Succeded', 'EMAIL', '0', 'Payment has succeded', 'Payment Successful.<br />Transaction Id:$transaction_id<br />Amount: $amount<br />Name:$name', '0', 'payments', '$transaction_id,$amount,$name,$booking_id,$error', 'bookings');


UPDATE `plugin_reports_reports` SET `sql`='SELECT \n		t.booking_id as `Booking ID`,\n		pp.transaction_id AS `Transaction Id`,\n		p.amount + p.adjustment AS `Amount`,\n		p.interest AS `Interest`,\n		IF(0/*p.due_date < CURDATE()*/, ROUND((p.interest / 30) * DATEDIFF(CURDATE(), p.due_date), 2), 0) AS `Penalty`,\n		p.total + IF(0/*p.due_date < CURDATE()*/, ROUND((p.interest / 30) * DATEDIFF(CURDATE(), p.due_date), 2), 0) AS `Total`,\n		p.due_date AS `Due Date`,\n                p.failed_auto_payment_attempts as `Failed Auto Payment Attempts`,\n		CONCAT_WS(\' \', c.title, c.first_name, c.last_name) AS `Payer`,\n    c.id AS `Payer ID`\n	FROM plugin_bookings_transactions_payment_plans pp\n		INNER JOIN plugin_bookings_transactions_payment_plans_has_payment p ON pp.id = p.payment_plan_id\n		INNER JOIN plugin_bookings_transactions t ON pp.transaction_id = t.id\n		INNER JOIN plugin_contacts3_contacts c ON t.contact_id = c.id\n	WHERE pp.deleted = 0 AND p.deleted = 0 AND t.deleted = 0 AND p.due_date < CURDATE() AND p.payment_id IS NULL\r\n' WHERE (`name`='Payment Plan Due');

INSERT INTO `engine_settings`
  (linked_plugin_name, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('payments', 'realex_api_url', 'Realex API URL', 'https://epage.payandshop.com/epage-remote.cgi', 'https://epage.payandshop.com/epage-remote.cgi', 'https://epage.payandshop.com/epage-remote.cgi', 'https://epage.payandshop.com/epage-remote.cgi', 'https://epage.payandshop.com/epage-remote.cgi', 'both', 'Realex API URL', 'text', 'Realex Settings', '0', '');
