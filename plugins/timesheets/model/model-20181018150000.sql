/*
ts:2018-10-18 15:00:00
*/
ALTER TABLE `plugin_timesheets_timesheets`
	ADD COLUMN `department_id` INT(11) NULL AFTER `staff_id`,
	ADD INDEX `department_id` (`department_id`);