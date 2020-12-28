/*
ts:2017-01-10 12:06:00
*/

CREATE TABLE IF NOT EXISTS `plugin_events_orders_pending`
(
  `id` INT NOT NULL AUTO_INCREMENT,
  `ticket_type_id` INT NOT NULL,
  `date_id` INT,
  `quantity` INT NOT NULL,
  `created` DATETIME NOT NULL,
  `deleted` TINYINT NOT NULL DEFAULT 0,
  `user_id` INT,
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB
CHARSET=utf8;

ALTER TABLE `plugin_events_events` ADD `count_down_seconds` INT NOT NULL DEFAULT 300;
