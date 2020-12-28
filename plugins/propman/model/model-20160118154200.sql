/*
ts:2016-01-18 15:42:00
*/

DROP TABLE `plugin_propman_groups_has_ratecards`;

CREATE TABLE `plugin_propman_properties_has_ratecards`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  property_id  INT NOT NULL,
  ratecard_id INT NOT NULL,

  KEY (`property_id`),
  KEY (`ratecard_id`)
)
ENGINE = InnoDB
CHARSET = UTF8;
