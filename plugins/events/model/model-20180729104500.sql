/*
ts:2018-07-29 10:45:00
*/

ALTER TABLE plugin_events_events ADD COLUMN enable_multiple_payers ENUM ('YES', 'NO');

ALTER TABLE plugin_events_events_has_ticket_types ADD COLUMN sleep_capacity INT;

CREATE TABLE plugin_events_events_has_paymentplans
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  event_id  INT NOT NULL,
  title VARCHAR(100),
  payment_percent DECIMAL(10, 2),
  due_date  DATETIME,
  published TINYINT NOT NULL DEFAULT 0,
  deleted TINYINT NOT NULL DEFAULT 0,
  KEY (event_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_events_orders_payments_has_partial_payments
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  main_payment_id  INT NOT NULL,
  payment_id  INT,
  payer_email VARCHAR(100),
  payer_name VARCHAR(100),
  payment_amount  DECIMAL(10, 2),
  due_date  DATETIME,
  url_hash VARCHAR(200),

  KEY (main_payment_id)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO `plugin_messaging_notification_templates` (`name`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('event-partial-payment', 'EMAIL', '1', 'Event Payment Link', 'Hello,\r\nName: $name,\r\nEmail: $email,\r\nDue Date: $due_date,\r\nAmount: $amount,\r\nEvent: $event\r\n<a href=\"$link\">click</a> to pay.\r\nThanks', 'Events', '$name,$email,$due_date,$event,$amount,$link', 'events');
