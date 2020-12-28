/*
ts:2016-07-29 12:06:42
*/

ALTER TABLE `plugin_events_events` ADD COLUMN `age_restriction`  int(11) NOT NULL DEFAULT 0 AFTER `image_file_id`;
