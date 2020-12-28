/*
ts:2015-12-11 13:30:00
*/

ALTER IGNORE TABLE `plugin_courses_schedules` ADD COLUMN `calendar_start_date_only` INT(1) NOT NULL DEFAULT 0  AFTER `run_off_schedule` ;