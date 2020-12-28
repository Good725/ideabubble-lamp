/*
ts:2020-05-18 15:20:00
*/

ALTER TABLE `plugin_news`
ADD COLUMN `course_category_id` INT(11) NULL AFTER `category_id`,
ADD COLUMN `course_id`          INT(11) NULL AFTER `category_id`,
ADD COLUMN `subject_id`         INT(11) NULL AFTER `course_category_id`;
