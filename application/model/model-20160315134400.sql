/*
ts:2016-03-15 13:44:00
*/

ALTER TABLE `engine_cron_tasks` ADD COLUMN `action` VARCHAR(200) DEFAULT 'cron';
