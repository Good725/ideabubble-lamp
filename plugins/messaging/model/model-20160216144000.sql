/*
ts:2016-02-16 14:40:00
*/
ALTER IGNORE TABLE `plugin_messaging_messages` ADD COLUMN `ip_address` VARCHAR(32)  NULL AFTER `form_data` ;
ALTER IGNORE TABLE `plugin_messaging_messages` ADD COLUMN `user_agent` VARCHAR(255) NULL AFTER `ip_address` ;
