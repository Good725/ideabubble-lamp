/*
ts:2015-12-11 12:05:00
*/

ALTER IGNORE TABLE `plugin_messaging_notification_templates` ADD COLUMN `overwrite_cms_message` INT(1) NOT NULL DEFAULT 0  AFTER `message` ;

ALTER IGNORE TABLE `plugin_messaging_messages` ADD COLUMN `form_data` BLOB NULL  AFTER `message` ;
