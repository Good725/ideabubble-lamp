/*
ts:2018-10-10 09:10:00
*/

DROP TABLE IF EXISTS plugin_events_events_has_paymentplans;

CREATE TABLE plugin_events_events_has_ticket_types_has_paymentplans
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  tickettype_id  INT NOT NULL,
  title VARCHAR(100),
  payment_type ENUM('Percent', 'Fixed'),
  payment_amount DECIMAL(10, 2),
  due_date  DATETIME,
  published TINYINT NOT NULL DEFAULT 0,
  deleted TINYINT NOT NULL DEFAULT 0,
  KEY (tickettype_id)
)
ENGINE=INNODB
CHARSET=UTF8;
