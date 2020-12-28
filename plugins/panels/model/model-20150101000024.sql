/*
ts:2015-01-01 00:00:24
*/
-- -----------------------------------------------------
-- Table `plugin_panels`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_panels` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `page_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `position` VARCHAR(45) NULL DEFAULT NULL ,
  `order_no` INT(3) UNSIGNED NULL DEFAULT '0' ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
  `text` TEXT NULL DEFAULT NULL ,
  `link_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `link_url` VARCHAR(255) NULL DEFAULT NULL ,
  `date_publish` DATETIME NULL DEFAULT NULL ,
  `date_remove` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

CREATE OR REPLACE VIEW `ppanels_view_panels_list_front_end` AS select `plugin_panels`.`id` AS `id`,`plugin_panels`.`page_id` AS `page_id`,`plugin_panels`.`title` AS `title`,`plugin_panels`.`position` AS `position`,`plugin_panels`.`order_no` AS `order_no`,`plugin_panels`.`image` AS `image`,`plugin_panels`.`text` AS `text`,`plugin_panels`.`link_id` AS `link_id`,`plugin_panels`.`link_url` AS `link_url`,`plugin_panels`.`date_publish` AS `date_publish`,`plugin_panels`.`date_remove` AS `date_remove`,`plugin_panels`.`publish` AS `publish`,`plugin_panels`.`deleted` AS `deleted` from `plugin_panels` where ((`plugin_panels`.`publish` = 1) and (`plugin_panels`.`deleted` = 0));
CREATE OR REPLACE VIEW `ppanels_view_panels_list_admin` AS select `plugin_panels`.`id` AS `id`,`plugin_panels`.`page_id` AS `page_id`,`plugin_panels`.`title` AS `title`,`plugin_panels`.`position` AS `position`,`plugin_panels`.`order_no` AS `order_no`,`plugin_panels`.`image` AS `image`,`plugin_panels`.`text` AS `text`,`plugin_panels`.`link_id` AS `link_id`,`plugin_panels`.`link_url` AS `link_url`,`plugin_panels`.`date_publish` AS `date_publish`,`plugin_panels`.`date_remove` AS `date_remove`,`plugin_panels`.`date_created` AS `date_created`,`plugin_panels`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_created`.`role` AS `created_by_role`,`plugin_panels`.`date_modified` AS `date_modified`,`plugin_panels`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modified`.`role` AS `modified_by_role`,`plugin_panels`.`publish` AS `publish` from ((((`plugin_panels` left join `users` `users_create` on((`plugin_panels`.`created_by` = `users_create`.`id`))) left join `project_role` `roles_created` on((`users_create`.`role_id` = `roles_created`.`id`))) left join `users` `users_modify` on((`plugin_panels`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modified` on((`users_modify`.`role_id` = `roles_modified`.`id`))) where (`plugin_panels`.`deleted` = 0);

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('panels', 'Panels', '1', '1', 'panels');

UPDATE `plugins` SET icon = 'panels.png' WHERE friendly_name = 'Panels';
UPDATE `plugins` SET `plugins`.`order` = 5 WHERE friendly_name = 'Panels';

-- -----------------------------------------------------
-- WPPROD-316 Enquiry form, news and testimonials feed as panels
-- -----------------------------------------------------
ALTER TABLE `plugin_panels`
 ADD COLUMN `type_id`       INT(10)      UNSIGNED NULL      AFTER `order_no`,
 ADD COLUMN `predefined_id` INT(10)      UNSIGNED NULL      AFTER `type_id`,
 ADD COLUMN `view`          VARCHAR(200) NULL DEFAULT NULL  AFTER `text` ;

CREATE TABLE IF NOT EXISTS `plugin_panels_types` (
 `id` INT(10) NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR(100) NULL ,
 `friendly_name` VARCHAR(100) NULL ,
 `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
 `date_modified` TIMESTAMP NULL DEFAULT NULL ,
 `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
 `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
 `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
 `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
 PRIMARY KEY (`id`) ,
 UNIQUE INDEX `name_UNIQUE` (`name`) );


INSERT IGNORE INTO `plugin_panels_types` (`name`, `friendly_name`, `date_modified`) VALUES
('none',       'None',       CURRENT_TIMESTAMP()),
('static',     'Static',     CURRENT_TIMESTAMP()),
('custom',     'Custom',     CURRENT_TIMESTAMP()),
('view',       'View',       CURRENT_TIMESTAMP()),
('predefined', 'Predefined', CURRENT_TIMESTAMP());


CREATE TABLE IF NOT EXISTS `plugin_panels_predefined` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR(100) NULL ,
 `friendly_name` VARCHAR(100) NULL ,
 `content` VARCHAR(200) NULL ,
 `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
 `date_modified` TIMESTAMP NULL DEFAULT NULL ,
 `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
 `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
 `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
 `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
 PRIMARY KEY (`id`) ,
 UNIQUE INDEX `name_UNIQUE` (`name`) );

INSERT IGNORE INTO `plugin_panels_predefined` (`name`, `friendly_name`, `content`, `date_modified`) VALUES
('latest_news',         'News Feed',         'Model_News,get_plugin_items_front_end_feed',         CURRENT_TIMESTAMP()),
('latest_testimonials', 'Testimonials Feed', 'Model_Testimonials,get_plugin_items_front_end_feed', CURRENT_TIMESTAMP());

-- Update existing panels; numeric image = custom, everything else = static
UPDATE `plugin_panels` SET `type_id` = (SELECT `id` FROM `plugin_panels_types` WHERE name='custom') WHERE `image` > 0;
UPDATE `plugin_panels` SET `type_id` = (SELECT `id` FROM `plugin_panels_types` WHERE name='static') WHERE `type_id` IS NULL;
