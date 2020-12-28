/*
ts:2018-10-25 17:16:00
*/
ALTER TABLE `plugin_timesheets_timesheets`
  ALTER `status` DROP DEFAULT;
ALTER TABLE `plugin_timesheets_timesheets`
  CHANGE COLUMN `status` `status` ENUM('open','pending','declined','approved','ready') NOT NULL AFTER `department_id`;