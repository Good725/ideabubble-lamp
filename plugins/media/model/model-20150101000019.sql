/*
ts:2015-01-01 00:00:19
*/
-- -----------------------------------------------------
-- Table `shared_media`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `shared_media` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `filename` VARCHAR(200) NOT NULL ,
  `dimensions` VARCHAR(50) NULL DEFAULT NULL ,
  `location` VARCHAR(100) NOT NULL ,
  `size` INT(20) NOT NULL ,
  `mime_type` VARCHAR(50) NOT NULL ,
  `hash` VARCHAR(32) NULL DEFAULT NULL ,
  `preset_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shared_media_photo_presets`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `shared_media_photo_presets` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(50) NOT NULL ,
  `directory` VARCHAR(100) NOT NULL DEFAULT 'content' ,
  `height_large` INT(10) UNSIGNED NOT NULL ,
  `width_large` INT(10) UNSIGNED NOT NULL ,
  `action_large` VARCHAR(10) NOT NULL DEFAULT 'fit' ,
  `thumb` TINYINT(1) NOT NULL ,
  `height_thumb` INT(10) UNSIGNED NOT NULL ,
  `width_thumb` INT(10) UNSIGNED NOT NULL ,
  `action_thumb` VARCHAR(10) NOT NULL DEFAULT 'crop' ,
  `date_created` TIMESTAMP NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NOT NULL ,
  `modified_by` INT(10) UNSIGNED NOT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

CREATE OR REPLACE VIEW `pmedia_view_media_presets_list_admin` AS select `shared_media_photo_presets`.`id` AS `id`,`shared_media_photo_presets`.`title` AS `title`,`shared_media_photo_presets`.`directory` AS `directory`,`shared_media_photo_presets`.`height_large` AS `height_large`,`shared_media_photo_presets`.`width_large` AS `width_large`,`shared_media_photo_presets`.`action_large` AS `action_large`,`shared_media_photo_presets`.`thumb` AS `thumb`,`shared_media_photo_presets`.`height_thumb` AS `height_thumb`,`shared_media_photo_presets`.`width_thumb` AS `width_thumb`,`shared_media_photo_presets`.`action_thumb` AS `action_thumb`,`shared_media_photo_presets`.`date_created` AS `date_created`,`shared_media_photo_presets`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`shared_media_photo_presets`.`date_modified` AS `date_modified`,`shared_media_photo_presets`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`shared_media_photo_presets`.`publish` AS `publish` from ((((`shared_media_photo_presets` left join `users` `users_create` on((`shared_media_photo_presets`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`shared_media_photo_presets`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`shared_media_photo_presets`.`deleted` = 0);

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('media', 'Media', '1', '1', 'media');

UPDATE `plugins` SET icon = 'media.png' WHERE friendly_name = 'Media';
UPDATE `plugins` SET `plugins`.`order` = 3 WHERE friendly_name = 'Media';

INSERT IGNORE INTO `shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`) VALUES ('Menu Icons', 'menus', '21', '0', 'fith', '0');

UPDATE `plugins` SET `requires_media`='1', `media_folder`='menus' WHERE `name`='menus';

INSERT IGNORE INTO `shared_media_photo_presets` (`title`, `directory`, `height_large`, `action_large`, `thumb`, `date_created`) VALUES ('Logos', 'logos', '88', 'fith', '0', CURRENT_TIMESTAMP());

INSERT IGNORE INTO `shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`) VALUES ('Background Images', 'bg_images', '0', '2600', 'fitw', '1', '0', '200', 'fitw', CURRENT_TIMESTAMP());
