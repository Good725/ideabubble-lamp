/*
ts:2016-05-19 16:12:00
*/

CREATE TABLE `plugin_propman_groups_calendar`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  group_id  INT NOT NULL,
  date  DATE NOT NULL,
  available TINYINT(1) NOT NULL DEFAULT 0,

  KEY (`date`)
)
ENGINE = InnoDB
CHARSET = UTF8
/*rac-300*/;

ALTER TABLE `plugin_propman_properties` ADD COLUMN `override_group_calendar` TINYINT DEFAULT 1;
