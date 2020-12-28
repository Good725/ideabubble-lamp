/*
ts:2015-01-01 00:00:12
*/
-- -----------------------------------------------------
-- Table `plugin_custom_scroller_sequences`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_custom_scroller_sequences` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(200) NOT NULL ,
  `animation_type` VARCHAR(20) NOT NULL DEFAULT 'fade',
  `order_type` VARCHAR(10) NOT NULL DEFAULT 'ascending',
  `first_item` INT(10) UNSIGNED NOT NULL DEFAULT 1,
  `rotating_speed` INT(10) UNSIGNED NOT NULL DEFAULT 1000,
  `timeout` INT(10) UNSIGNED NOT NULL DEFAULT 8000,
  `pagination` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `controls` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `plugin_custom_scroller_sequence_items`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_custom_scroller_sequence_items` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `sequence_id` INT(10) UNSIGNED NOT NULL ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
	`image_location` VARCHAR(255) NULL DEFAULT NULL ,
  `order_no` INT(10) UNSIGNED NOT NULL DEFAULT 0 ,
	`title` VARCHAR(200) NOT NULL DEFAULT '',
	`html` LONGTEXT NULL DEFAULT NULL ,
	`link_type` VARCHAR(10) NOT NULL DEFAULT 'none' ,
	`link_url` VARCHAR(255) NOT NULL DEFAULT '' ,
	`link_target` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_custom_scroller_sequences_idx` (`sequence_id` ASC) ,
  CONSTRAINT `fk_plugin_custom_scroller_sequences`
  FOREIGN KEY (`sequence_id` )
  REFERENCES `plugin_custom_scroller_sequences` (`id` )
	ON DELETE NO ACTION
  ON UPDATE NO ACTION)
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `plugin_custom_scroller_plugins_sequences`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_custom_scroller_plugins_sequences` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`sequence_holder_id` INT(10) UNSIGNED NOT NULL ,
	`holder_plugin_name` VARCHAR(20) NOT NULL ,
	`sequence_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`, `sequence_holder_id`, `holder_plugin_name`))
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Add Plugin to Database - Plugins Table
-- -----------------------------------------------------
INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`, `icon`, `order`)
  VALUE ('customscroller', 'CustomScroller', '0', '0', NULL, NULL, '99');


-- -----------------------------------------------------
-- Update Table: plugin_custom_scroller_sequences Field: delete to be: deleted
-- Required to keep all DB Tables' Field Names CONSISTENT
-- -----------------------------------------------------
ALTER IGNORE TABLE `plugin_custom_scroller_sequences` CHANGE `delete` `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0';

-- WPPROD-242
ALTER TABLE `plugin_custom_scroller_sequences` ADD COLUMN `plugin` INT(3) NOT NULL AFTER `controls` ;

-- -----------------------------------------------------
-- KES-244
-- Banner to have HTML editable labels
-- -----------------------------------------------------
ALTER TABLE `plugin_custom_scroller_sequence_items` ADD COLUMN `label` LONGTEXT NULL DEFAULT NULL AFTER `title` ;
