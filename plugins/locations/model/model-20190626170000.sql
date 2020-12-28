/*
ts:2019-06-26 17:00:00
*/

CREATE TABLE `plugin_locations_cities` (
  `id`            INT NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(255) NOT NULL,
  `date_created`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` TIMESTAMP NULL,
  `created_by`    INT(11) NULL,
  `modified_by`   INT(11) NULL,
  `seed`          INT(1) NOT NULL DEFAULT 0,
  `publish`       INT(1) NOT NULL DEFAULT 1,
  `deleted`       INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`));

ALTER TABLE `plugin_locations_location`
  ADD COLUMN `city_id`   INT(11) NULL AFTER `address_3`,
  ADD COLUMN `latitude`  DECIMAL(11,8) NULL AFTER `map_reference`,
  ADD COLUMN `longitude` DECIMAL(11,8) NULL AFTER `latitude`,
  ADD COLUMN `seed`      INT(1) NOT NULL DEFAULT 0 AFTER `deleted`;

INSERT INTO `engine_notes_types` (`type`, `referenced_table`, `referenced_table_id`, `referenced_table_deleted`) VALUES ('Location', 'plugin_locations_location', 'id', 'deleted');

ALTER TABLE `plugin_locations_location`
  CHANGE COLUMN `publish` `publish` TINYINT(4) NOT NULL DEFAULT 1,
  CHANGE COLUMN `latitude`  `latitude`  VARCHAR(20) NULL,
  CHANGE COLUMN `longitude` `longitude` VARCHAR(20) NULL
;
