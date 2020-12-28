/*
ts:2015-01-01 00:00:18
*/
-- -----------------------------------------------------
-- Table `plugin_locations_location`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_locations_location` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL ,
  `type` VARCHAR(255) NOT NULL ,
  `address_1` VARCHAR(255) NOT NULL ,
  `address_2` VARCHAR(255) NOT NULL ,
  `address_3` VARCHAR(255) NOT NULL ,
  `county` VARCHAR(255) NOT NULL ,
  `phone` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `map_reference` VARCHAR(255) NOT NULL ,
  `publish` TINYINT(4) NOT NULL ,
  `deleted` TINYINT(4) NOT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP ,
  `date_entered` TIMESTAMP NULL DEFAULT NULL ,
  `modified_by` INT(11) NOT NULL ,
  `created_by` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('locations', 'Locations', '1', '0', NULL);

UPDATE `plugins` SET icon = 'locations.png' WHERE friendly_name = 'Locations';
UPDATE `plugins` SET `plugins`.`order` = 12 WHERE friendly_name = 'Locations';
