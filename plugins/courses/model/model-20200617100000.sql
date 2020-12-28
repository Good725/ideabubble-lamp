/*
ts:2020-06-17 10:00:00
*/

ALTER TABLE `plugin_content_content`
ADD COLUMN `available_days_before` INT(11) NULL AFTER `available_to`,
ADD COLUMN `available_days_after`  INT(11) NULL AFTER `available_days_before`;
