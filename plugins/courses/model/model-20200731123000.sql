/*
ts:2020-07-31 12:30:00
*/

-- Add GDPR type toggle to courses' table
ALTER TABLE `plugin_courses_courses`
ADD COLUMN `gdpr_type` ENUM('', 'gdpr1', 'gdpr2') NULL DEFAULT NULL AFTER `description`;
