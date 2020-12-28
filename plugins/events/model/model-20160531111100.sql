/*
ts:2016-05-31 11:11:00
*/

ALTER TABLE `plugin_events_venues` ADD COLUMN `url` VARCHAR(255);
ALTER TABLE `plugin_events_events` ADD COLUMN `timezone` VARCHAR(8);
ALTER TABLE `plugin_events_events` ADD COLUMN `videos` TEXT;
ALTER TABLE `plugin_events_events` DROP COLUMN `start_datetime`;
ALTER TABLE `plugin_events_events` DROP COLUMN `end_datetime`;
ALTER TABLE `plugin_events_events` ADD COLUMN `one_ticket_for_all_dates` TINYINT DEFAULT 0;
CREATE TABLE `plugin_events_events_dates`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  event_id  INT NOT NULL,
  starts DATETIME,
  ends DATETIME,
  others  TEXT,
  deleted TINYINT DEFAULT 0,

  KEY (`event_id`)
)
ENGINE=INNODB
CHARSET=UTF8;

ALTER TABLE plugin_events_orders_items ADD COLUMN event_date_id INT;
ALTER TABLE plugin_events_orders_items ADD KEY (`event_date_id`);

CREATE TABLE `plugin_events_discounts`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  event_id  INT NOT NULL,
  `type` ENUM('Fixed', 'Percent'),
  amount  DECIMAL(10, 2),
  currency VARCHAR(3),
  code  VARCHAR(100),
  quantity INT,
  starts  DATETIME,
  ends DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (event_id)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE plugin_events_ticket_types_has_discounts
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  ticket_type_id INT,
  discount_id INT,

  KEY (ticket_type_id, discount_id)
)
  ENGINE=INNODB
  CHARSET=UTF8;

CREATE TABLE plugin_events_orders_has_discounts
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  discount_id INT,

  KEY (order_id, discount_id)
)
  ENGINE=INNODB
  CHARSET=UTF8;
