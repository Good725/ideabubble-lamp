/*
ts:2016-01-14 16:45:00
*/

ALTER TABLE `plugin_survey` ADD COLUMN `display_thank_you` TINYINT(1) UNSIGNED NULL DEFAULT '0' AFTER `result_template_id`;
ALTER TABLE `plugin_survey` ADD COLUMN `thank_you_page_id` INT(11) NULL AFTER `display_thank_you`;
