/*
ts:2019-12-06 18:00:00
*/
ALTER TABLE `plugin_courses_levels` ADD COLUMN `short_name` VARCHAR(16) NULL AFTER `level`;

ALTER TABLE `plugin_courses_levels` ADD COLUMN `order` INT(4) NULL AFTER `description`;
