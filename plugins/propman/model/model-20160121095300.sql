/*
ts:2016-01-21 09:53:00
*/

DROP TABLE `plugin_propman_groups_calendar`;
CREATE TABLE `plugin_propman_properties_calendar`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  property_id  INT NOT NULL,
  date  DATE NOT NULL,
  available TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`date`)
)
ENGINE = InnoDB
CHARSET = UTF8;
