/*
ts:2019-10-08 18:00:00
*/
ALTER TABLE `plugin_courses_categories`
ADD COLUMN `color` VARCHAR(100) NOT NULL DEFAULT 10 AFTER `description`;

ALTER TABLE `plugin_courses_categories` CHANGE COLUMN `color` `color` VARCHAR(100) NULL DEFAULT '#000000' ;

UPDATE `plugin_courses_categories` SET `color` = null WHERE `color` = '10';

