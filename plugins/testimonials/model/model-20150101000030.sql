/*
ts:2015-01-01 00:00:30
*/
-- -----------------------------------------------------
-- Table `plugin_testimonials_categories`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_testimonials_categories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(200) NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugin_testimonials`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_testimonials` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category_id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `item_signature` TEXT NULL DEFAULT NULL ,
  `item_company` TEXT NULL DEFAULT NULL ,
  `item_website` TEXT NULL DEFAULT NULL ,
  `content` LONGTEXT NULL DEFAULT NULL ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
  `event_date` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_testimonials_plugin_testimonials_categories_idx` (`category_id` ASC) ,
  CONSTRAINT `fk_plugin_testimonials_plugin_testimonials_categories`
  FOREIGN KEY (`category_id` )
  REFERENCES `plugin_testimonials_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

CREATE OR REPLACE VIEW `ptestimonials_view_testimonials_list_admin` AS select `plugin_testimonials`.`id` AS `id`,`plugin_testimonials`.`title` AS `title`,`plugin_testimonials`.`category_id` AS `category_id`,`plugin_testimonials_categories`.`category` AS `category`,`plugin_testimonials`.`summary` AS `summary`,`plugin_testimonials`.`content` AS `content`,`plugin_testimonials`.`image` AS `image`,`plugin_testimonials`.`event_date` AS `event_date`,`plugin_testimonials`.`item_signature` AS `item_signature`,`plugin_testimonials`.`item_company` AS `item_company`,`plugin_testimonials`.`date_created` AS `date_created`,`plugin_testimonials`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_testimonials`.`date_modified` AS `date_modified`,`plugin_testimonials`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_testimonials`.`publish` AS `publish`,`plugin_testimonials`.`item_website` AS `item_website` from (((((`plugin_testimonials` left join `plugin_testimonials_categories` on((`plugin_testimonials`.`category_id` = `plugin_testimonials_categories`.`id`))) left join `users` `users_create` on((`plugin_testimonials`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`plugin_testimonials`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_testimonials`.`deleted` = 0);
CREATE OR REPLACE VIEW `ptestimonials_view_testimonials_list_front_end` AS select `plugin_testimonials`.`id` AS `id`,`plugin_testimonials`.`title` AS `title`,`plugin_testimonials_categories`.`category` AS `category`,`plugin_testimonials`.`summary` AS `summary`,`plugin_testimonials`.`content` AS `content`,`plugin_testimonials`.`image` AS `image`,`plugin_testimonials`.`event_date` AS `event_date`,`plugin_testimonials`.`item_company` AS `item_company`,`plugin_testimonials`.`item_signature` AS `item_signature`,`plugin_testimonials`.`date_modified` AS `date_modified`,`plugin_testimonials`.`item_website` AS `item_website` from (`plugin_testimonials` left join `plugin_testimonials_categories` on((`plugin_testimonials`.`category_id` = `plugin_testimonials_categories`.`id`))) where ((`plugin_testimonials`.`publish` = 1) and (`plugin_testimonials`.`deleted` = 0));

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('testimonials', 'Testimonials', '1', '1', 'testimonials');

-- -----------------------------------------------------
-- WPPROD-245
-- -----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('testimonials_animation_type', 'Animation type', 'horizontal', 'both', 'The manner in which the testimonials panel moves from one testimonial to another.', 'select', 'Testimonials', '0', 'Model_Testimonials,get_animation_types');

UPDATE `plugins` SET icon = 'testimonials.png' WHERE friendly_name = 'Testimonials';
UPDATE `plugins` SET `plugins`.`order` = 8 WHERE friendly_name = 'Testimonials';

-- -----------------------------------------------------
-- WPPROD-245 Testimonials truncation setting
-- -----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('testimonials_truncation', 'Maximum feed item length', '200', 'both', 'The length an item in the testimonials feed can be before it gets cut short.', 'text', 'Testimonials', '0', '');

-- WPPROD-245 Read more button toggle
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `type`, `group`, `required`, `options`) VALUES ('testimonials_read_more', 'Display a read more button in the feed', 'TRUE', 'both', 'checkbox', 'Testimonials', '0', '');

-- WPPROD-245 News feed item timeout speed
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('testimonials_feed_timeout', 'Feed timeout speed', '8000', 'both', 'The amount of time in milliseconds a testimonial is displayed before moving on to the next item. Default: 8000', 'text', 'Testimonials', '0', '');

-- WPPROD-245 Number of items in feed
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('testimonials_feed_item_count', 'No. of Items Displayed in Feed', '3', 'both', 'Enter the maximum number of testimonials you would like to appear in the testimonials feed', 'text', 'Testimonials', '0', '');

-- IBCMS-253, STAC-4 - Turn-off Testimonials Pagination in feed
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('testimonials_feed_pagination', 'Display pagination', '1', '1', '1', '1', '1', 'both', 'Display pagination buttons in the testimonials module', 'toggle_button', 'Testimonials', '0', 'Model_Settings,on_or_off');


-- -----------------------------------------------------
-- PCSYS-70 cannot select a category
-- Add "Testimonials" category, if there are no categories
-- -----------------------------------------------------
INSERT IGNORE INTO `plugin_testimonials_categories` (`id`, `category`) VALUES (1, 'Testimonials');
