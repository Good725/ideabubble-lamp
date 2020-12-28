/*
ts:2017-11-14 11:30:00
*/

ALTER TABLE `plugin_formbuilder_forms` ADD COLUMN `captcha_version` INT(2) NOT NULL DEFAULT 1  AFTER `captcha_enabled` ;
