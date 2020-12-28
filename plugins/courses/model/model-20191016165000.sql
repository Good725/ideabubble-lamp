/*
ts:2019-10-14 16:50:00
*/

ALTER TABLE `plugin_courses_subjects`
ADD COLUMN `image` VARCHAR(255) NULL AFTER `color`,
ADD COLUMN `order` INT(4) NULL AFTER `image`;
