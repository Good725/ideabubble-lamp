/*
ts:2018-10-16 14:12:00
*/
ALTER TABLE `plugin_timesheets_timesheets`
  ALTER `status` DROP DEFAULT;
ALTER TABLE `plugin_timesheets_timesheets`
  CHANGE COLUMN `status` `status` ENUM('open','pending','declined','approved') NOT NULL AFTER `staff_id`;