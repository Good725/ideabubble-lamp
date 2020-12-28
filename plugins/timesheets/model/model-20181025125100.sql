/*
ts:2018-10-25 12:51:00
*/
ALTER TABLE `plugin_timesheets_timesheets`
  ADD COLUMN `reviewer_id` INT(11) NOT NULL AFTER `staff_id`,
  ADD INDEX `reviewer_id` (`reviewer_id`);
