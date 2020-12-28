/*
ts:2015-01-01 00:00:21
*/
-- -----------------------------------------------------
-- Table `pmenus`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `pmenus` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(100) NOT NULL ,
  `title` VARCHAR(100) NOT NULL ,
  `link_tag` INT(11) NOT NULL ,
  `link_url` VARCHAR(500) NOT NULL ,
  `has_sub` TINYINT(1) NOT NULL ,
  `parent_id` INT(11) NOT NULL ,
  `menu_order` INT(11) NOT NULL ,
  `publish` TINYINT(1) NOT NULL ,
  `deleted` TINYINT(1) NOT NULL ,
  `date_modified` DATETIME NOT NULL ,
  `date_entered` DATETIME NOT NULL ,
  `created_by` INT(11) NOT NULL ,
  `modified_by` INT(11) NOT NULL ,
  `menus_target` VARCHAR(20) NOT NULL DEFAULT '_top' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('menus', 'Menus', '1', '0', NULL);

UPDATE `plugins` SET icon = 'menus.png' WHERE friendly_name = 'Menus';
UPDATE `plugins` SET `plugins`.`order` = 4 WHERE friendly_name = 'Menus';

ALTER IGNORE TABLE `pmenus` ADD COLUMN `image_id` INT(11) NULL;

UPDATE `plugins` SET `requires_media`='1', `media_folder` = 'menus' WHERE `name`='menus';

-- IBCMS-466
INSERT IGNORE INTO engine_localisation_custom_scanners (scanner) VALUES ('Model_Menus::get_localisation_messages');
ALTER TABLE `pmenus` RENAME TO `plugin_menus`;
