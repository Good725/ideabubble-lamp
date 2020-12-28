/*
ts:2016-01-24 18:35:00
*/

CREATE TABLE `plugin_propman_groups_has_ratecards`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  group_id  INT NOT NULL,
  ratecard_id INT NOT NULL,

  KEY (`group_id`),
  KEY (`ratecard_id`)
)
ENGINE = INNODB
CHARSET = UTF8;

ALTER TABLE `plugin_propman_ratecards` ADD COLUMN `property_type_id` INT;

