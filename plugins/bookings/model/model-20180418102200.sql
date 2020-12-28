/*
ts:2018-04-18 10:22:00
*/

INSERT INTO plugin_bookings_transactions_payments_statuses
  (`status`, `credit`, `publish`, `delete`)
  VALUES
  ('Journal - Refund', -1, 1, 0);

CREATE TABLE IF NOT EXISTS `plugin_bookings_settlements_payments`
(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settlement_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `rental` decimal(10,2) NOT NULL,
  `income` decimal(10,2) NOT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settlement_id` (`settlement_id`,`payment_id`),
  KEY `payment_id` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

