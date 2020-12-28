/*
ts:2017-03-16 20:19:00
*/

ALTER TABLE `plugin_messaging_messages` MODIFY COLUMN `driver_id` INT NULL;
ALTER TABLE `plugin_messaging_messages` MODIFY COLUMN `status`  enum('SENDING','SCHEDULED','SCHEDULE_MISSED','SENT','INTERRUPTED','RECEIVED','DRAFTED','FAILED');

