/*
ts:2016-10-10 10:00:00
*/
ALTER TABLE `plugin_testimonials`
ADD COLUMN `course_id`          INT(11) NULL AFTER `category_id`,
ADD COLUMN `course_category_id` INT(11) NULL AFTER `course_id`;

ALTER TABLE `plugin_testimonials`
ADD COLUMN `subject_id`         INT(11) NULL AFTER `course_category_id`;