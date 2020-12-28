/*
ts:2020-07-16 16:30:00
*/

ALTER TABLE `plugin_courses_schedules` ADD COLUMN `charge_per_delegate` INT(1) NULL DEFAULT 1 AFTER `payg_absent_fee`;
