/*
ts:2018-09-25 05:47:00
*/

ALTER TABLE `plugin_timeoff_config`
ADD COLUMN `is_active`  tinyint(1) NULL AFTER `value`,
ADD COLUMN `start_time`  time NULL AFTER `is_active`,
ADD COLUMN `end_time`  time NULL AFTER `start_time`;

