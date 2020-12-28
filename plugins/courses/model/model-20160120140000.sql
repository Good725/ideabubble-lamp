/*
ts:2016-01-20 14:00:00
*/

ALTER IGNORE TABLE `plugin_courses_categories` ADD COLUMN `start_date` DATETIME NULL DEFAULT NULL AFTER `parent_id`;
ALTER IGNORE TABLE `plugin_courses_categories` ADD COLUMN `end_date` DATETIME NULL DEFAULT NULL AFTER `parent_id`;