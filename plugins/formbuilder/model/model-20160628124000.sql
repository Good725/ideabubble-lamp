/*
ts:2016-06-28 12:40:00
*/

ALTER IGNORE TABLE `plugin_formbuilder_forms`
ADD COLUMN `email_all_fields` INT(1) NOT NULL DEFAULT 0 AFTER `summary` ;
