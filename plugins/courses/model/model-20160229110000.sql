/*
ts:2016-02-29 11:00:00
*/

ALTER IGNORE TABLE `plugin_courses_categories` ADD COLUMN `start_time` TIME NULL AFTER `start_date`;
ALTER IGNORE TABLE `plugin_courses_categories` ADD COLUMN `end_time` TIME NULL AFTER `start_time`;