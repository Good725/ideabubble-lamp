/*
ts:2015-01-01 00:00:22
*/
-- -----------------------------------------------------
-- Table `plugin_news_categories`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_news_categories` (
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
-- Table `plugin_news`
-- -----------------------------------------------------

CREATE  TABLE IF NOT EXISTS `plugin_news` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category_id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `content` LONGTEXT NULL DEFAULT NULL ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
  `event_date` DATETIME NULL DEFAULT NULL ,
  `date_publish` DATETIME NULL DEFAULT NULL ,
  `date_remove` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_news_plugin_news_categories_idx` (`category_id` ASC) ,
  CONSTRAINT `fk_plugin_news_plugin_news_categories`
  FOREIGN KEY (`category_id` )
  REFERENCES `plugin_news_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

CREATE OR REPLACE VIEW `pnews_view_news_list_admin` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news`.`category_id` AS `category_id`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_created` AS `date_created`,`plugin_news`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_news`.`publish` AS `publish` from (((((`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) left join `users` `users_create` on((`plugin_news`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`plugin_news`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_news`.`deleted` = 0);
CREATE OR REPLACE VIEW `pnews_view_news_list_front_end` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_modified` AS `date_modified` from (`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) where ((`plugin_news`.`publish` = 1) and (`plugin_news`.`deleted` = 0));

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUE ('news', 'News', '1', '1', 'news');

-- -----------------------------------------------------
-- WPPROD-111 SEO fields added for news.
-- -----------------------------------------------------
alter ignore table plugin_news
 add column `seo_title` varchar(255) DEFAULT NULL AFTER `image`,
 add column `seo_keywords` varchar(255) DEFAULT NULL AFTER `seo_title`,
 add column `seo_description` varchar(255) DEFAULT NULL AFTER `seo_keywords`,
 add column `seo_footer` varchar(255) DEFAULT NULL AFTER `seo_description`;

-- -----------------------------------------------------
-- WPPROD-111 replace views for news with seo fields
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `pnews_view_news_list_admin` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news`.`category_id` AS `category_id`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_created` AS `date_created`,`plugin_news`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_news`.`publish` AS `publish`,`plugin_news`.`seo_title` AS `seo_title`,`plugin_news`.`seo_keywords` AS `seo_keywords`,`plugin_news`.`seo_description` AS `seo_description`,`plugin_news`.`seo_footer` AS `seo_footer` from (((((`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) left join `users` `users_create` on((`plugin_news`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`plugin_news`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_news`.`deleted` = 0);
CREATE OR REPLACE VIEW `pnews_view_news_list_front_end` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`seo_title` AS `seo_title`,`plugin_news`.`seo_keywords` AS `seo_keywords`,`plugin_news`.`seo_description` AS `seo_description`,`plugin_news`.`seo_footer` AS `seo_footer` from (`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) where ((`plugin_news`.`publish` = 1) and (`plugin_news`.`deleted` = 0));

-- -----------------------------------------------------
-- WPPROD-247 Dashboard icons missing
-- -----------------------------------------------------
UPDATE `plugins` SET icon = 'news.png' WHERE friendly_name = 'News';
UPDATE `plugins` SET `plugins`.`order` = 7 WHERE friendly_name = 'News';

-- -----------------------------------------------------
-- WPPROD-245 News animation and truncation settings
-- -----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES
  ('news_animation_type', 'Animation type', 'vertical', 'both', 'The manner in which the news panel moves from one item to another.', 'select', 'News', '0', 'Model_News,get_animation_types'),
  ('news_truncation', 'Maximum feed item length', '100', 'both', 'The length an item in the news feed can be before it gets cut short.', 'text', 'News', '0', '');

-- WPPROD-245 Read more button toggle
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `type`, `group`, `required`, `options`) VALUES ('news_read_more', 'Display a read more button in the feed', 'TRUE', 'both', 'checkbox', 'News', '0', '');

-- WPPROD-245 News feed item timeout speed
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('news_feed_timeout', 'Feed timeout speed', '8000', 'both', 'The amount of time in milliseconds a news feed item is displayed before moving on to the next item. Default: 8000', 'text', 'News', '0', '');

-- WPPROD-245 Number of items in feed
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('news_feed_item_count', 'No. of Items Displayed in Feed', '3', 'both', 'Enter the maximum number of news items you would like to appear in the news feed. Default: 3', 'text', 'News', '0', '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('show_news_date', 'Toggle to show news publish date.', 'TRUE', 'TRUE', 'TRUE', 'TRUE', 'TRUE', 'both', 'Show the publish date on news','checkbox', 'News', '0', '');


-- -----------------------------------------------------
-- WPPROD-293 New alt text option for images
-- -----------------------------------------------------
ALTER TABLE `plugin_news` ADD COLUMN `alt_text` VARCHAR(255) NULL DEFAULT NULL  AFTER `image` ;

ALTER TABLE `plugin_news` ADD COLUMN `title_text` VARCHAR(255) NULL DEFAULT NULL  AFTER `image` ;

CREATE OR REPLACE VIEW `pnews_view_news_list_admin` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news`.`category_id` AS `category_id`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`alt_text` AS `alt_text`,`plugin_news`.`title_text` AS `title_text`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_created` AS `date_created`,`plugin_news`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_news`.`publish` AS `publish`,`plugin_news`.`seo_title` AS `seo_title`,`plugin_news`.`seo_keywords` AS `seo_keywords`,`plugin_news`.`seo_description` AS `seo_description`,`plugin_news`.`seo_footer` AS `seo_footer` from (((((`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) left join `users` `users_create` on((`plugin_news`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`plugin_news`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_news`.`deleted` = 0);
CREATE OR REPLACE VIEW `pnews_view_news_list_front_end` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`alt_text` AS `alt_text`,`plugin_news`.`title_text` AS `title_text`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`seo_title` AS `seo_title`,`plugin_news`.`seo_keywords` AS `seo_keywords`,`plugin_news`.`seo_description` AS `seo_description`,`plugin_news`.`seo_footer` AS `seo_footer` from (`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) where ((`plugin_news`.`publish` = 1) and (`plugin_news`.`deleted` = 0));

-- -----------------------------------------------------
-- PCSYS-70 cannot select a category
-- Add "News" category, if there are no categories
-- -----------------------------------------------------
INSERT IGNORE INTO `plugin_news_categories` (`id`, `category`) VALUES (1, 'News');

ALTER IGNORE TABLE `plugin_news_categories` ADD COLUMN `order` INT(10) NULL  AFTER `category` ;

ALTER IGNORE TABLE `plugin_news_categories` ADD UNIQUE INDEX `category_UNIQUE` (`category` ASC) ;

INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `note`, `type`, `group`) VALUES
('news_min_items_per_slide', 'Minimum Items per Slide', '2', 'both', 'The minimum number of items to appear per slide in the news feed (only works with vertical animation type)', 'text', 'News'),
('news_max_items_per_slide', 'Maximum Items per Slide', '2', 'both', 'The maximum number of items to appear per slide in the news feed (only works with vertical animation type)', 'text', 'News');

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('news_infinite_scroller', 'Infinite Scrolling', '0', '0', '0', '0', '0', 'both', 'Enable infinite scrolling on the news feed', 'toggle_button', 'News', '', 'Model_Settings,on_or_off');

-- "order" column
ALTER IGNORE TABLE `plugin_news` ADD COLUMN `order` INT(10) NULL DEFAULT NULL  AFTER `date_remove` ;
CREATE OR REPLACE VIEW `pnews_view_news_list_front_end` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`alt_text` AS `alt_text`,`plugin_news`.`title_text` AS `title_text`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`order` AS `order`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`seo_title` AS `seo_title`,`plugin_news`.`seo_keywords` AS `seo_keywords`,`plugin_news`.`seo_description` AS `seo_description`,`plugin_news`.`seo_footer` AS `seo_footer` from (`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) where ((`plugin_news`.`publish` = 1) and (`plugin_news`.`deleted` = 0));
CREATE OR REPLACE VIEW `pnews_view_news_list_admin` AS select `plugin_news`.`id` AS `id`,`plugin_news`.`title` AS `title`,`plugin_news`.`category_id` AS `category_id`,`plugin_news_categories`.`category` AS `category`,`plugin_news`.`summary` AS `summary`,`plugin_news`.`content` AS `content`,`plugin_news`.`image` AS `image`,`plugin_news`.`alt_text` AS `alt_text`,`plugin_news`.`title_text` AS `title_text`,`plugin_news`.`event_date` AS `event_date`,`plugin_news`.`date_publish` AS `date_publish`,`plugin_news`.`date_remove` AS `date_remove`,`plugin_news`.`order` AS `order`,`plugin_news`.`date_created` AS `date_created`,`plugin_news`.`created_by` AS `created_by`,`users_create`.`name` AS `created_by_name`,`roles_create`.`role` AS `created_by_role`,`plugin_news`.`date_modified` AS `date_modified`,`plugin_news`.`modified_by` AS `modified_by`,`users_modify`.`name` AS `modified_by_name`,`roles_modify`.`role` AS `modified_by_role`,`plugin_news`.`publish` AS `publish`,`plugin_news`.`seo_title` AS `seo_title`,`plugin_news`.`seo_keywords` AS `seo_keywords`,`plugin_news`.`seo_description` AS `seo_description`,`plugin_news`.`seo_footer` AS `seo_footer` from (((((`plugin_news` left join `plugin_news_categories` on((`plugin_news`.`category_id` = `plugin_news_categories`.`id`))) left join `users` `users_create` on((`plugin_news`.`created_by` = `users_create`.`id`))) left join `users` `users_modify` on((`plugin_news`.`modified_by` = `users_modify`.`id`))) left join `project_role` `roles_modify` on((`users_modify`.`role_id` = `roles_modify`.`id`))) left join `project_role` `roles_create` on((`users_create`.`role_id` = `roles_create`.`id`))) where (`plugin_news`.`deleted` = 0);
