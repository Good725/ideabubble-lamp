/*
ts:2015-01-01 00:00:14
*/

-- -----------------------------------------------------
-- Table `plugin_files_file`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_files_file` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `type` TINYINT NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `parent_id` INT NULL ,
  `deleted` TINYINT NOT NULL ,
  `created_by` INT NULL ,
  `modified_by` INT NULL ,
  `date_created` TIMESTAMP NULL ,
  `date_modified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`, `type`) ,
  INDEX `file_idx_1` (`parent_id` ASC) ,
  INDEX `file_idx_2` (`name` ASC) ,
  CONSTRAINT `file_fk_1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `plugin_files_file` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_files_version`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_files_version` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `file_id` INT NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `mime_type` VARCHAR(255) NOT NULL ,
  `size` INT NOT NULL ,
  `path` VARCHAR(255) NOT NULL ,
  `active` TINYINT NOT NULL ,
  `deleted` TINYINT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `version_idx_1` (`file_id` ASC) ,
  CONSTRAINT `version_fk_1`
    FOREIGN KEY (`file_id` )
    REFERENCES `plugin_files_file` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

INSERT IGNORE INTO `plugin_files_file` (`type`, `name`, `deleted`) VALUE (0, '/', 0);

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`)
  VALUE ('files', 'Files', '1', '0');

INSERT IGNORE INTO `plugins_per_role`
  SELECT `plugins`.`id`, `project_role`.`id`, 1 FROM `plugins`, `project_role` WHERE `plugins`.`name` = 'files' AND `project_role`.`role` != 'External User';

-- -----------------------------------------------------
-- WPPROD-373 - Language column in files table
-- IBIS-3197 - Group documents by language
-- -----------------------------------------------------
ALTER IGNORE TABLE `plugin_files_file` ADD COLUMN `language` VARCHAR(45) NOT NULL DEFAULT 'en' AFTER `name` ;

ALTER IGNORE TABLE `plugin_files_file` ADD COLUMN `template_data` TINYINT(1) UNSIGNED NULL DEFAULT '0' AFTER `language`;
