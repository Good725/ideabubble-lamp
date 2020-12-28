/*
ts:2019-10-09 10:30:00
*/
ALTER TABLE `plugin_pages_pages`
ADD COLUMN `course_category_id` INT(11) NULL AFTER `category_id`;


ALTER TABLE `plugin_pages_pages`
ADD COLUMN `course_id` INT(11) NULL AFTER `category_id`,
ADD COLUMN `subject_id` INT(11) NULL AFTER `course_category_id`;
